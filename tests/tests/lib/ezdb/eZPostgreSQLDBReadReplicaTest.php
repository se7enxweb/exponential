<?php
/**
 * eZPostgreSQLDB's read replica ([DatabaseSettings] UseSlaveServer): which statements may run there, which requests
 * may use it, the pin to the primary after a write, and the per-request reset (also in a persistent worker).
 *
 * No PostgreSQL server is needed: the driver is created without connecting; every case here decides before a
 * connection would be opened.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezdb
 */

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
class eZPostgreSQLDBReadReplicaTest extends PHPUnit\Framework\TestCase
{
    private $globalInstance;

    protected function setUp(): void
    {
        $this->globalInstance = isset( $GLOBALS['eZDBGlobalInstance'] ) ? $GLOBALS['eZDBGlobalInstance'] : null;
    }

    protected function tearDown(): void
    {
        if ( $this->globalInstance === null )
            unset( $GLOBALS['eZDBGlobalInstance'] );
        else
            $GLOBALS['eZDBGlobalInstance'] = $this->globalInstance;
    }

    private function driver( $useReplica = true )
    {
        $class = new ReflectionClass( 'eZPostgreSQLDB' );
        $db = $class->newInstanceWithoutConstructor();
        $db->DBConnection = false;
        $db->IsConnected = false;
        $db->UseSlaveServer = $useReplica;
        $db->SlaveServer = '127.0.0.1';
        $db->SlavePort = 5413;
        $db->TransactionCounter = 0;
        return $db;
    }

    private function call( $db, $method, array $args )
    {
        $m = new ReflectionMethod( 'eZPostgreSQLDB', $method );
        if ( PHP_VERSION_ID < 80100 )
            $m->setAccessible( true );
        return $m->invokeArgs( $db, $args );
    }

    public static function readableStatements()
    {
        return array(
            array( 'SELECT * FROM ezcontentobject WHERE id = 1' ),
            array( "  select id\nfrom ezcontentobject_tree" ),
            array( '(SELECT 1) UNION (SELECT 2)' ),
            array( 'SELECT count(*) AS count FROM ezuser WHERE login = \'editor\'' ),
        );
    }

    public static function primaryStatements()
    {
        return array(
            array( 'INSERT INTO ezurl (url) VALUES (\'x\')' ),
            array( 'UPDATE ezcontentobject SET modified = 1' ),
            array( 'DELETE FROM ezsession' ),
            array( 'BEGIN WORK' ),
            array( 'COMMIT WORK' ),
            array( 'LOCK TABLE ezcontentobject' ),
            array( 'SET search_path TO public' ),
            array( 'WITH d AS (DELETE FROM ezpending_actions RETURNING *) SELECT * FROM d' ),
            array( "SELECT currval( 'ezcontentobject_s' )" ),
            array( "SELECT nextval('ezurl_s')" ),
            array( "SELECT setval('ezurl_s', max(id)) FROM ezurl" ),
            array( 'SELECT * FROM ezcontentobject WHERE id = 1 FOR UPDATE' ),
            array( 'SELECT * FROM ezcontentobject WHERE id = 1 for share' ),
            array( 'SELECT * INTO ezbackup FROM ezcontentobject' ),
            array( 'SELECT pg_advisory_lock(42)' ),
            array( 'SELECT pg_try_advisory_xact_lock(42)' ),
            array( 'SELECT txid_current()' ),
            array( 'SELECTED' ),
            array( '' ),
        );
    }

    /** @dataProvider readableStatements */
    #[\PHPUnit\Framework\Attributes\DataProvider('readableStatements')]
    public function testPlainSelectsMayRunOnTheReplica( $sql )
    {
        $this->assertTrue( eZPostgreSQLDB::isReplicaReadable( $sql ) );
    }

    /** @dataProvider primaryStatements */
    #[\PHPUnit\Framework\Attributes\DataProvider('primaryStatements')]
    public function testWritesLocksAndSequencesStayOnThePrimary( $sql )
    {
        $this->assertFalse( eZPostgreSQLDB::isReplicaReadable( $sql ) );
    }

    public function testOnlyAnonymousGetAndHeadWebRequestsMayReadTheReplica()
    {
        $this->assertTrue( eZPostgreSQLDB::requestMayReadReplica( array( 'REQUEST_METHOD' => 'GET' ), array(), true ) );
        $this->assertTrue( eZPostgreSQLDB::requestMayReadReplica( array( 'REQUEST_METHOD' => 'head' ), array(), true ) );
        $this->assertFalse( eZPostgreSQLDB::requestMayReadReplica( array( 'REQUEST_METHOD' => 'POST' ), array(), true ) );
        $this->assertFalse( eZPostgreSQLDB::requestMayReadReplica( array(), array(), true ) );
        // a signed-in editor reads the primary
        $this->assertFalse( eZPostgreSQLDB::requestMayReadReplica( array( 'REQUEST_METHOD' => 'GET' ), array( 'is_logged_in' => 'true' ), true ) );
        // cronjobs and scripts read the primary
        $this->assertFalse( eZPostgreSQLDB::requestMayReadReplica( array( 'REQUEST_METHOD' => 'GET' ), array(), false ) );
    }

    public function testAWritePinsTheRestOfTheRequestToThePrimary()
    {
        $db = $this->driver();
        $this->call( $db, 'notePrimaryStatement', array( 'SELECT 1' ) );
        $this->assertFalse( $db->ReplicaPinned, 'a SELECT on the primary does not pin' );
        $this->call( $db, 'notePrimaryStatement', array( 'UPDATE ezcontentobject SET modified = 1' ) );
        $this->assertTrue( $db->ReplicaPinned );
        $this->assertFalse( $this->call( $db, 'replicaConnectionFor', array( 'SELECT 1', false ) ) );
    }

    public function testNothingIsPinnedWithoutAReplica()
    {
        $db = $this->driver( false );
        $this->call( $db, 'notePrimaryStatement', array( 'BEGIN WORK' ) );
        $this->assertFalse( $db->ReplicaPinned );
        $this->assertFalse( $this->call( $db, 'replicaConnectionFor', array( 'SELECT 1', false ) ) );
        $this->assertNull( $db->ReplicaState, 'no decision is made when the replica is off' );
    }

    public function testTransactionsAndExplicitPrimaryReadsSkipTheReplica()
    {
        $db = $this->driver();
        $db->TransactionCounter = 1;
        $this->assertFalse( $this->call( $db, 'replicaConnectionFor', array( 'SELECT 1', false ) ) );
        $db->TransactionCounter = 0;
        $this->assertFalse( $this->call( $db, 'replicaConnectionFor', array( 'SELECT 1', eZDBInterface::SERVER_MASTER ) ) );
        $this->assertNull( $db->ReplicaState );
    }

    public function testACommandLineProcessDecidesOnceForThePrimary()
    {
        if ( PHP_SAPI !== 'cli' || defined( 'QBIX_WEBSERVER' ) || class_exists( 'Q_WebServer', false ) )
            $this->markTestSkipped( 'Needs a plain command line process' );
        $db = $this->driver();
        $this->assertFalse( $this->call( $db, 'replicaConnectionFor', array( 'SELECT 1', false ) ) );
        $this->assertSame( 'off', $db->ReplicaState );
        $this->assertFalse( $db->ReplicaConnection, 'no connection was opened' );
    }

    public function testTheRequestResetForgetsTheDecisionAndThePin()
    {
        $db = $this->driver();
        $db->ReplicaState = 'off';
        $db->ReplicaPinned = true;
        $GLOBALS['eZDBGlobalInstance'] = $db;
        eZPostgreSQLDB::resetRequest();
        $this->assertNull( $db->ReplicaState );
        $this->assertFalse( $db->ReplicaPinned );
    }

    public function testTheResetHookIsRegistered()
    {
        $this->assertSame( array( 'eZPostgreSQLDB', 'resetRequest' ), expKernelServiceScope::DEFAULT_RESET_HOOKS['dbreplica'] );
    }
}
