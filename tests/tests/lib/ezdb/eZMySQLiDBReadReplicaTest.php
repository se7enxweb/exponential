<?php
/**
 * eZMySQLiDB's read replica ([DatabaseSettings] UseSlaveServer), the same rule as eZPostgreSQLDB's: which statements
 * may run there, which requests may use it, the pin to the primary after a write, the replica's status (both
 * threads running, SlaveServerMaxLag), the catch-up to the primary's GTID position (SlaveServerCatchUp) and the
 * per-request reset (also in a persistent worker).
 *
 *  RR-01 - Plain SELECTs may run on the replica
 *  RR-02 - Writes, locks, session statements, INTO, named locks, sequences and LAST_INSERT_ID() stay on the primary
 *  RR-03 - Only anonymous GET and HEAD web requests may read the replica
 *  RR-04 - A write pins the rest of the request to the primary; nothing is pinned without a replica
 *  RR-05 - Transactions and SERVER_MASTER skip the replica; SERVER_SLAVE is only a wish
 *  RR-06 - A command line process decides once for the primary and opens no connection
 *  RR-07 - The replica status: both threads, the lag against SlaveServerMaxLag, NULL lag, no row, no privilege
 *  RR-08 - The catch-up statement and its answer, MariaDB and MySQL; no statement without a GTID position
 *  RR-09 - A whole decision in a web request: read, lagging, stopped, not caught up, no status privilege, trusted
 *  RR-10 - The decision holds for the request; a write pins; the reset starts the next request afresh
 *  RR-11 - The reset hook is the shared one of every driver
 *  RR-12 - A connection kept from an earlier request that broke meanwhile is opened again once; a check that fails on
 *          a new connection reads the primary
 *
 * No MySQL server is needed: the driver is created without connecting, and the cases that need a replica use a
 * driver whose replica answers are given (the live proof against a real primary and replica pair is separate).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezdb
 */

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
class eZMySQLiDBReadReplicaTest extends PHPUnit\Framework\TestCase
{
    private $globalInstance;
    private $server;
    private $cookies;

    protected function setUp(): void
    {
        if ( !class_exists( 'mysqli' ) )
            $this->markTestSkipped( 'The mysqli extension is not loaded' );
        $this->globalInstance = isset( $GLOBALS['eZDBGlobalInstance'] ) ? $GLOBALS['eZDBGlobalInstance'] : null;
        $this->server = $_SERVER;
        $this->cookies = $_COOKIE;
    }

    protected function tearDown(): void
    {
        if ( $this->globalInstance === null )
            unset( $GLOBALS['eZDBGlobalInstance'] );
        else
            $GLOBALS['eZDBGlobalInstance'] = $this->globalInstance;
        $_SERVER = $this->server;
        $_COOKIE = $this->cookies;
    }

    private function driver( $useReplica = true )
    {
        $class = new ReflectionClass( 'eZMySQLiDB' );
        $db = $class->newInstanceWithoutConstructor();
        $db->DBConnection = false;
        $db->DBWriteConnection = false;
        $db->IsConnected = false;
        $db->UseSlaveServer = $useReplica;
        $db->SlaveServer = '127.0.0.1';
        $db->SlavePort = 3408;
        $db->TransactionCounter = 0;
        $db->ReplicaSettings = array( 'SlaveServerStatusCheck' => 'enabled', 'SlaveServerMaxLag' => 0,
                                      'SlaveServerCatchUp' => 'enabled', 'SlaveServerCatchUpWait' => 100 );
        return $db;
    }

    /**
     * A driver whose replica answers are given: the status row (array, array() for none, false for an error with
     * $statusErrno), the primary's GTID position and the catch-up answer. Records what it was asked.
     */
    private function answeringDriver( $status, $position = '0-1-42', $caughtUp = '0', $statusErrno = 0 )
    {
        $db = new class( array() ) extends eZMySQLiDB
        {
            public $answers = array();
            public $asked = array();
            public $connects = 0;
            public function __construct( $parameters ) {}
            protected function connectReplica() { $this->connects++; return new stdClass(); }
            protected function primaryGtidPosition() { $this->asked[] = 'position'; return $this->answers['position']; }
            protected function isMariaDB() { return true; }
            protected function replicaRawRow( $sql, &$errno, $mode = MYSQLI_ASSOC )
            {
                $this->asked[] = $sql;
                $errno = 0;
                if ( stripos( $sql, 'SHOW ' ) === 0 && !empty( $this->answers['statusSeq'] ) )
                {
                    list( $row, $errno ) = array_shift( $this->answers['statusSeq'] );
                    return $row;
                }
                if ( stripos( $sql, 'SHOW ' ) === 0 )
                {
                    $errno = $this->answers['statusErrno'];
                    return $this->answers['status'];
                }
                return array( $this->answers['caughtUp'] );
            }
        };
        $db->DBConnection = false;
        $db->IsConnected = false;
        $db->UseSlaveServer = true;
        $db->SlaveServer = '127.0.0.1';
        $db->SlavePort = 3408;
        $db->TransactionCounter = 0;
        $db->ReplicaSettings = array( 'SlaveServerStatusCheck' => 'enabled', 'SlaveServerMaxLag' => 0,
                                      'SlaveServerCatchUp' => 'enabled', 'SlaveServerCatchUpWait' => 100 );
        $db->answers = array( 'status' => $status, 'statusErrno' => $statusErrno, 'position' => $position, 'caughtUp' => $caughtUp );
        return $db;
    }

    /** An anonymous GET in a web process (a Velocity worker is a web process on the command line SAPI). */
    private function anonymousWebGet()
    {
        $_SERVER['QBIX_WORKER'] = '1';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_COOKIE = array();
    }

    private function call( $db, $method, array $args )
    {
        $m = new ReflectionMethod( 'eZMySQLiDB', $method );
        if ( PHP_VERSION_ID < 80100 )
            $m->setAccessible( true );
        return $m->invokeArgs( $db, $args );
    }

    private static function running( $lag, $io = 'Yes', $sql = 'Yes', $mysql8 = false )
    {
        return $mysql8 ? array( 'Replica_IO_Running' => $io, 'Replica_SQL_Running' => $sql, 'Seconds_Behind_Source' => $lag )
                       : array( 'Slave_IO_Running' => $io, 'Slave_SQL_Running' => $sql, 'Seconds_Behind_Master' => $lag );
    }

    public static function readableStatements()
    {
        return array(
            array( 'SELECT * FROM ezcontentobject WHERE id = 1' ),
            array( "  select id\nfrom ezcontentobject_tree" ),
            array( '(SELECT 1) UNION (SELECT 2)' ),
            array( 'SELECT count(*) AS count FROM ezuser WHERE login = \'editor\'' ),
            array( 'SELECT SQL_CALC_FOUND_ROWS id FROM ezurl LIMIT 10' ),
            array( 'SELECT FOUND_ROWS()' ),
            array( 'SELECT @@server_id' ),
        );
    }

    public static function primaryStatements()
    {
        return array(
            array( 'INSERT INTO ezurl (url) VALUES (\'x\')' ),
            array( 'UPDATE ezcontentobject SET modified = 1' ),
            array( 'DELETE FROM ezsession' ),
            array( 'REPLACE INTO ezpreferences VALUES (1)' ),
            array( 'BEGIN WORK' ),
            array( 'COMMIT' ),
            array( 'LOCK TABLES ezcontentobject WRITE' ),
            array( 'UNLOCK TABLES' ),
            array( 'SET NAMES utf8mb4' ),
            array( 'SET SESSION group_concat_max_len = 65536' ),
            array( 'CREATE TEMPORARY TABLE ezsearch_tmp_0 ( contentobject_id int )' ),
            array( 'SHOW TABLES' ),
            array( 'WITH d AS (SELECT 1) SELECT * FROM d' ),
            array( 'SELECT * FROM ezcontentobject WHERE id = 1 FOR UPDATE' ),
            array( 'SELECT * FROM ezcontentobject WHERE id = 1 for share' ),
            array( 'SELECT * FROM ezcontentobject WHERE id = 1 LOCK IN SHARE MODE' ),
            array( 'SELECT * INTO OUTFILE \'/x\' FROM ezcontentobject' ),
            array( 'SELECT id INTO @x FROM ezcontentobject LIMIT 1' ),
            array( "SELECT GET_LOCK('ezpublish', 10)" ),
            array( "SELECT RELEASE_LOCK('ezpublish')" ),
            array( 'SELECT LAST_INSERT_ID()' ),
            array( 'SELECT NEXTVAL(ezurl_s)' ),
            array( 'SELECT NEXT VALUE FOR ezurl_s' ),
            array( "SELECT MASTER_GTID_WAIT('0-1-1', 1)" ),
            array( 'SELECTED' ),
            array( '' ),
        );
    }

    /** RR-01 @dataProvider readableStatements */
    #[\PHPUnit\Framework\Attributes\DataProvider('readableStatements')]
    public function testPlainSelectsMayRunOnTheReplica( $sql )
    {
        $this->assertTrue( eZMySQLiDB::isReplicaReadable( $sql ) );
    }

    /** RR-02 @dataProvider primaryStatements */
    #[\PHPUnit\Framework\Attributes\DataProvider('primaryStatements')]
    public function testWritesLocksAndSessionStatementsStayOnThePrimary( $sql )
    {
        $this->assertFalse( eZMySQLiDB::isReplicaReadable( $sql ) );
    }

    /** RR-03 */
    public function testOnlyAnonymousGetAndHeadWebRequestsMayReadTheReplica()
    {
        $this->assertTrue( eZMySQLiDB::requestMayReadReplica( array( 'REQUEST_METHOD' => 'GET' ), array(), true ) );
        $this->assertTrue( eZMySQLiDB::requestMayReadReplica( array( 'REQUEST_METHOD' => 'head' ), array(), true ) );
        $this->assertFalse( eZMySQLiDB::requestMayReadReplica( array( 'REQUEST_METHOD' => 'POST' ), array(), true ) );
        $this->assertFalse( eZMySQLiDB::requestMayReadReplica( array(), array(), true ) );
        $this->assertFalse( eZMySQLiDB::requestMayReadReplica( array( 'REQUEST_METHOD' => 'GET' ), array( 'is_logged_in' => 'true' ), true ) );
        $this->assertFalse( eZMySQLiDB::requestMayReadReplica( array( 'REQUEST_METHOD' => 'GET' ), array(), false ) );
    }

    /** RR-04 */
    public function testAWritePinsTheRestOfTheRequestToThePrimary()
    {
        $db = $this->driver();
        $this->call( $db, 'notePrimaryStatement', array( 'SELECT 1' ) );
        $this->assertFalse( $db->ReplicaPinned, 'a SELECT on the primary does not pin' );
        $this->call( $db, 'notePrimaryStatement', array( 'UPDATE ezcontentobject SET modified = 1' ) );
        $this->assertTrue( $db->ReplicaPinned );
        $this->assertFalse( $this->call( $db, 'replicaConnectionFor', array( 'SELECT 1', false ) ) );
    }

    /** RR-04 */
    public function testNothingIsPinnedWithoutAReplica()
    {
        $db = $this->driver( false );
        $this->call( $db, 'notePrimaryStatement', array( 'BEGIN WORK' ) );
        $this->assertFalse( $db->ReplicaPinned );
        $this->assertFalse( $this->call( $db, 'replicaConnectionFor', array( 'SELECT 1', false ) ) );
        $this->assertNull( $db->ReplicaState, 'no decision is made when the replica is off' );
    }

    /** RR-05 */
    public function testTransactionsAndExplicitPrimaryReadsSkipTheReplica()
    {
        $this->anonymousWebGet();
        $db = $this->answeringDriver( self::running( 0 ) );
        $db->TransactionCounter = 1;
        $this->assertFalse( $this->call( $db, 'replicaConnectionFor', array( 'SELECT 1', false ) ) );
        $db->TransactionCounter = 0;
        $this->assertFalse( $this->call( $db, 'replicaConnectionFor', array( 'SELECT 1', eZDBInterface::SERVER_MASTER ) ) );
        $this->assertNull( $db->ReplicaState );
        // SERVER_SLAVE (the temporary tables of a permission check, the search) is a wish: the rule decides
        $this->assertNotFalse( $this->call( $db, 'replicaConnectionFor', array( 'SELECT 1', eZDBInterface::SERVER_SLAVE ) ) );
        $this->assertFalse( $this->call( $db, 'replicaConnectionFor', array( 'CREATE TEMPORARY TABLE t ( id int )', eZDBInterface::SERVER_SLAVE ) ) );
    }

    /** RR-06 */
    public function testACommandLineProcessDecidesOnceForThePrimary()
    {
        if ( PHP_SAPI !== 'cli' || defined( 'QBIX_WEBSERVER' ) || class_exists( 'Q_WebServer', false ) )
            $this->markTestSkipped( 'Needs a plain command line process' );
        unset( $_SERVER['QBIX_WORKER'], $_SERVER['VELOCITY'] );
        $db = $this->answeringDriver( self::running( 0 ) );
        $this->assertFalse( $this->call( $db, 'replicaConnectionFor', array( 'SELECT 1', false ) ) );
        $this->assertSame( 'off', $db->ReplicaState );
        $this->assertSame( 0, $db->connects, 'no connection was opened' );
        $this->assertSame( array(), $db->asked, 'not even the primary was asked' );
    }

    /** RR-07 */
    public function testTheReplicaStatus()
    {
        $this->assertTrue( eZMySQLiDB::replicaStatusVerdict( self::running( '0' ), 0 )[0] );
        $this->assertTrue( eZMySQLiDB::replicaStatusVerdict( self::running( 0, 'Yes', 'Yes', true ), 0 )[0], 'MySQL 8.0.22 names' );
        $this->assertFalse( eZMySQLiDB::replicaStatusVerdict( self::running( '1' ), 0 )[0] );
        $this->assertTrue( eZMySQLiDB::replicaStatusVerdict( self::running( '3' ), 5 )[0] );
        $this->assertFalse( eZMySQLiDB::replicaStatusVerdict( self::running( '6' ), 5 )[0] );
        $this->assertFalse( eZMySQLiDB::replicaStatusVerdict( self::running( null ), 5 )[0], 'NULL: it does not know' );
        $this->assertFalse( eZMySQLiDB::replicaStatusVerdict( self::running( 0, 'Connecting' ), 5 )[0] );
        $this->assertFalse( eZMySQLiDB::replicaStatusVerdict( self::running( 0, 'Yes', 'No' ), 5 )[0] );
        $notAReplica = eZMySQLiDB::replicaStatusVerdict( array(), 0 );
        $this->assertTrue( $notAReplica[0], 'a server that is no replica is read as it is' );
        $this->assertFalse( $notAReplica[2] );
        $unreadable = eZMySQLiDB::replicaStatusVerdict( false, 0 );
        $this->assertFalse( $unreadable[0] );
        $this->assertStringContainsString( 'REPLICATION CLIENT', $unreadable[1] );
    }

    /** RR-08 */
    public function testTheCatchUpStatement()
    {
        $this->assertSame( "SELECT MASTER_GTID_WAIT('0-1-42,1-2-7', 0.100)", eZMySQLiDB::catchUpStatement( true, ' 0-1-42, 1-2-7 ', 100 ) );
        $this->assertSame( "SELECT GTID_SUBSET('3e11fa47-71ca-11e1-9e33-c80aa9429562:1-5,3e11fa47-71ca-11e1-9e33-c80aa9429563:1-2', @@GLOBAL.gtid_executed)",
                           eZMySQLiDB::catchUpStatement( false, "3e11fa47-71ca-11e1-9e33-c80aa9429562:1-5,\n3e11fa47-71ca-11e1-9e33-c80aa9429563:1-2", 100 ) );
        $this->assertNull( eZMySQLiDB::catchUpStatement( true, '', 100 ), 'no binary log: no position' );
        $this->assertNull( eZMySQLiDB::catchUpStatement( true, "0-1-1'); DROP TABLE x; --", 100 ), 'only a GTID position' );
        $this->assertTrue( eZMySQLiDB::catchUpVerdict( true, '0' ) );
        $this->assertFalse( eZMySQLiDB::catchUpVerdict( true, '-1' ), 'the wait ran out' );
        $this->assertFalse( eZMySQLiDB::catchUpVerdict( true, null ) );
        $this->assertTrue( eZMySQLiDB::catchUpVerdict( false, '1' ) );
        $this->assertFalse( eZMySQLiDB::catchUpVerdict( false, '0' ) );
    }

    /** RR-09 */
    public function testAWholeDecisionInAWebRequest()
    {
        $this->anonymousWebGet();

        $db = $this->answeringDriver( self::running( 0 ) );
        $this->assertNotFalse( $this->call( $db, 'replicaConnectionFor', array( 'SELECT 1', false ) ), 'a current replica is read' );
        $this->assertSame( 'on', $db->ReplicaState );
        $this->assertSame( 'position', $db->asked[0], 'the primary is asked first' );
        $this->assertSame( "SELECT MASTER_GTID_WAIT('0-1-42', 0.100)", end( $db->asked ) );

        $db = $this->answeringDriver( self::running( 2 ) );
        $this->assertFalse( $this->call( $db, 'replicaConnectionFor', array( 'SELECT 1', false ) ), 'lagging' );
        $this->assertSame( 'off', $db->ReplicaState );

        $db = $this->answeringDriver( self::running( 0, 'No' ) );
        $this->assertFalse( $this->call( $db, 'replicaConnectionFor', array( 'SELECT 1', false ) ), 'stopped' );

        $db = $this->answeringDriver( self::running( 0 ), '0-1-42', '-1' );
        $this->assertFalse( $this->call( $db, 'replicaConnectionFor', array( 'SELECT 1', false ) ), 'not caught up' );

        $db = $this->answeringDriver( array(), '0-1-42', '-1' );
        $this->assertNotFalse( $this->call( $db, 'replicaConnectionFor', array( 'SELECT 1', false ) ), 'no replica: no catch-up asked' );
        $this->assertNotContains( "SELECT MASTER_GTID_WAIT('0-1-42', 0.100)", $db->asked );

        $db = $this->answeringDriver( self::running( 0 ), '' );
        $this->assertNotFalse( $this->call( $db, 'replicaConnectionFor', array( 'SELECT 1', false ) ), 'no GTID position: the lag decides' );

        $db = $this->answeringDriver( false, '0-1-42', '0', 1227 );
        $this->assertFalse( $this->call( $db, 'replicaConnectionFor', array( 'SELECT 1', false ) ), 'no status privilege' );

        $db = $this->answeringDriver( false, '0-1-42', '0', 1227 );
        $db->ReplicaSettings['SlaveServerStatusCheck'] = 'disabled';
        $this->assertNotFalse( $this->call( $db, 'replicaConnectionFor', array( 'SELECT 1', false ) ), 'trusted' );
        $this->assertNotContains( 'SHOW REPLICA STATUS', $db->asked );

        $db = $this->answeringDriver( false, '0-1-42', '0', 1064 );
        $this->call( $db, 'replicaConnectionFor', array( 'SELECT 1', false ) );
        $this->assertContains( 'SHOW SLAVE STATUS', $db->asked, 'an older server is asked the old way' );

        $db = $this->answeringDriver( self::running( 0 ), '0-1-42', '-1' );
        $db->ReplicaSettings['SlaveServerCatchUp'] = 'disabled';
        $this->assertNotFalse( $this->call( $db, 'replicaConnectionFor', array( 'SELECT 1', false ) ), 'no catch-up wanted' );
        $this->assertNotContains( 'position', $db->asked );

        $_COOKIE = array( 'is_logged_in' => 'true' );
        $db = $this->answeringDriver( self::running( 0 ) );
        $this->assertFalse( $this->call( $db, 'replicaConnectionFor', array( 'SELECT 1', false ) ), 'signed in' );
        $this->assertSame( 0, $db->connects );
    }

    /** RR-10 */
    public function testTheDecisionHoldsForTheRequestAndTheResetStartsAfresh()
    {
        $this->anonymousWebGet();
        $db = $this->answeringDriver( self::running( 0 ) );
        $this->assertNotFalse( $this->call( $db, 'replicaConnectionFor', array( 'SELECT 1', false ) ) );
        $asked = count( $db->asked );
        $this->assertNotFalse( $this->call( $db, 'replicaConnectionFor', array( 'SELECT 2', false ) ) );
        $this->assertSame( $asked, count( $db->asked ), 'decided once per request' );
        $this->call( $db, 'notePrimaryStatement', array( "INSERT INTO ezurl (url) VALUES ('x')" ) );
        $this->assertFalse( $this->call( $db, 'replicaConnectionFor', array( 'SELECT 3', false ) ), 'pinned after the write' );

        $GLOBALS['eZDBGlobalInstance'] = $db;
        eZDBInterface::resetReplicaRequest();
        $this->assertNull( $db->ReplicaState );
        $this->assertFalse( $db->ReplicaPinned );
        $this->assertNotFalse( $this->call( $db, 'replicaConnectionFor', array( 'SELECT 4', false ) ) );
        $this->assertSame( 1, $db->connects, 'the replica connection is kept across requests' );
    }

    /** RR-11 */
    public function testTheResetHookIsTheSharedOne()
    {
        $this->assertSame( array( 'eZDBInterface', 'resetReplicaRequest' ), expKernelServiceScope::DEFAULT_RESET_HOOKS['dbreplica'] );
        $db = $this->driver();
        $db->ReplicaState = 'off';
        $db->ReplicaPinned = true;
        $GLOBALS['eZDBGlobalInstance'] = $db;
        eZMySQLiDB::resetRequest();
        $this->assertNull( $db->ReplicaState );
        $this->assertFalse( $db->ReplicaPinned );
    }

    /** RR-12 */
    public function testAKeptConnectionThatBrokeIsOpenedAgainOnce()
    {
        $this->anonymousWebGet();
        $db = $this->answeringDriver( self::running( 0 ) );
        $db->ReplicaConnection = new stdClass();   // kept from the request before
        $db->answers['statusSeq'] = array( array( false, 2006 ), array( self::running( 0 ), 0 ) );
        $this->assertNotFalse( $this->call( $db, 'replicaConnectionFor', array( 'SELECT 1', false ) ) );
        $this->assertSame( 1, $db->connects, 'one new connection' );
        $this->assertSame( 'on', $db->ReplicaState );

        $db = $this->answeringDriver( self::running( 0 ) );
        $db->answers['statusSeq'] = array( array( false, 1053 ) );   // a new connection whose check fails
        $this->assertFalse( $this->call( $db, 'replicaConnectionFor', array( 'SELECT 1', false ) ) );
        $this->assertSame( 1, $db->connects, 'no second try on a new connection' );
        $this->assertFalse( $db->ReplicaConnection, 'dropped' );
        $this->assertSame( 'off', $db->ReplicaState );
    }
}
