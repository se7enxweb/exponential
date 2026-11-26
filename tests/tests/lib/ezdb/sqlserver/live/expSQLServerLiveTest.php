<?php
/**
 * expSQLServerDB against a real SQL Server (skipped without one): set EXP_TEST_SQLSERVER_HOST, _PORT, _USER,
 * _PASSWORD (or _PASSWORD_FILE), optionally _DATABASE (exp_sqlserver_test, made on first use) and _LAYER (sqlsrv,
 * pdo_sqlsrv, pdo_odbc, pdo_dblib; auto by default). Every test makes its own tables and drops them.
 *
 *  LV-01 - Connect: version and edition read, encryption options (a self-signed certificate is refused unless
 *          trusted), databaseName(), session options in effect
 *  LV-02 - CRUD with identity: lastSerialID() of this statement, NULL/0 ids, explicit ids and IDENTITY_INSERT
 *          off again after a failure (a second table takes explicit ids)
 *  LV-03 - Triggers: lastSerialID() is the table's id although a trigger inserted elsewhere; @@IDENTITY is not
 *          (the old extension's bug 3); SCOPE_IDENTITY() in a later batch is NULL after a bound insert
 *  LV-04 - Paging: every limit/offset equals the slice of the ordered rows (the old extension returned the first
 *          rows for every offset, bug 1); DISTINCT, UNION, TOP, ORDER BY LOWER(), limit 0, offset past the end
 *  LV-05 - Transactions: commit, rollback, nesting, XACT_ABORT; RCSI readers not blocked by writers
 *  LV-06 - Deadlock (1205) with a second process: victim detected, transaction invalid, retryOnDeadlock() succeeds
 *  LV-07 - Lock timeout (1222) and table/application locks
 *  LV-08 - Parameters: 2,100 is the server's cap (8003); 2,500 string literals still work (N'' fallback);
 *          IN lists of 5,000 ids
 *  LV-09 - Text and binary: Unicode (emoji, CJK, combining), 1 MB NVARCHAR(MAX), VARBINARY(MAX), SUBSTRING past
 *          8,000, line continuation kept, quotes/comments/? in values, NUL dropped, '' is not NULL, trailing
 *          spaces and case-insensitive default collation, VARCHAR in a non-UTF-8 collation loses characters,
 *          md5() equals PHP's
 *  LV-10 - Dates: unix timestamps now and past 2038, DATETIME2 as 'Y-m-d H:i:s'
 *  LV-11 - Large results: 20,000 rows
 *  LV-12 - Reconnect: a killed session is opened again outside a transaction, fails inside one
 *  LV-13 - Read replica: a "replica" that is the primary (READ_WRITE through the listener) is read for anonymous GET
 *  LV-14 - Errors: duplicate 2627/2601, FK 547, NOT NULL 515, truncation 2628, conversion 245, syntax 102, unknown
 *          object 208, index key past 1,700 bytes 1946, NVARCHAR(MAX) as index key 1919, a 129 character name 103
 *  LV-15 - Schema read-back: tableColumns(), relationList() of tables, views, sequences, triggers, indexes;
 *          removeRelation()
 *  LV-16 - Temporary tables: #tables of the session, invisible to others, dropped at the request reset
 *  LV-17 - Helpers: upsert (MERGE) insert then update, STRING_AGG past 8,000, CONCAT, charLength
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__ ) . '/expSQLServerTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('sqlserver')]
#[\PHPUnit\Framework\Attributes\Group('sqlserver-live')]
class expSQLServerLiveTest extends PHPUnit\Framework\TestCase
{
    /** @var expSQLServerDB */
    private $db;

    /** @var expSQLServerDB[] other connections of the test */
    private $others = array();

    /** @var string[] tables to drop */
    private $tables = array();

    private $saved;
    private $server;
    private $cookies;

    protected function setUp(): void
    {
        $this->saved = expSQLServerTestKit::quietDebug();
        $this->server = $_SERVER;
        $this->cookies = $_COOKIE;
        $db = expSQLServerTestKit::liveDriver();
        if ( !$db instanceof expSQLServerDB )
            $this->markTestSkipped( $db );
        $this->db = $db;
    }

    protected function tearDown(): void
    {
        if ( $this->db )
        {
            $this->db->setErrorHandling( eZDB::ERROR_HANDLING_STANDARD );
            while ( $this->db->isConnected() && $this->db->transactionCounter() > 0 )
                $this->db->rollback();
            foreach ( array_reverse( $this->tables ) as $table )
                $this->db->query( "IF OBJECT_ID(N'$table') IS NOT NULL DROP TABLE [$table]" );
            $this->db->close();
        }
        foreach ( $this->others as $other )
            $other->close();
        $_SERVER = $this->server;
        $_COOKIE = $this->cookies;
        expSQLServerTestKit::restoreDebug( $this->saved );
    }

    private function table( $definition, $prefix = 't' )
    {
        $name = 'exp_' . $prefix . '_' . getmypid() . '_' . count( $this->tables ) . '_' . mt_rand( 1000, 9999 );
        $this->tables[] = $name;
        $this->assertTrue( $this->db->query( "CREATE TABLE [$name] ($definition)" ), (string)$this->db->errorMessage() );
        return $name;
    }

    private function other( array $settings = array() )
    {
        $db = expSQLServerTestKit::liveDriver( $settings );
        $this->assertInstanceOf( 'expSQLServerDB', $db );
        $this->others[] = $db;
        return $db;
    }

    private function value( $sql, ?expSQLServerDB $db = null )
    {
        $rows = ( $db ?: $this->db )->arrayQuery( $sql );
        $this->assertIsArray( $rows, (string)( $db ?: $this->db )->errorMessage() );
        return $rows ? reset( $rows[0] ) : null;
    }

    private function expectError( $sql, $number )
    {
        $this->assertFalse( $this->db->query( $sql ) );
        $this->assertSame( $number, $this->db->errorNumber(), (string)$this->db->errorMessage() );
    }

    private function peer( $scenario, $table )
    {
        $php = PHP_BINARY;
        $args = trim( (string)getenv( 'EXP_TEST_SQLSERVER_PHP_ARGS' ) );
        $command = escapeshellarg( $php ) . ( $args !== '' ? ' ' . $args : '' ) . ' ' . escapeshellarg( __DIR__ . '/expSQLServerPeer.php' )
                 . ' ' . escapeshellarg( $scenario ) . ' ' . escapeshellarg( $table );
        $process = proc_open( $command, array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes );
        $this->assertIsResource( $process );
        return array( $process, $pipes );
    }

    private function peerResult( array $peer )
    {
        list( $process, $pipes ) = $peer;
        $out = stream_get_contents( $pipes[1] );
        stream_get_contents( $pipes[2] );
        proc_close( $process );
        return json_decode( (string)$out, true );
    }

    // ---------------------------------------------------------------- LV-01

    public function testConnectReadsServerFacts()
    {
        $this->assertSame( 'SQLServer', $this->db->databaseName() );
        $this->assertTrue( $this->db->ServerInfo['supported'], $this->db->ServerInfo['note'] );
        $this->assertGreaterThanOrEqual( 14, $this->db->ServerInfo['major'] );
        $this->assertNotSame( '', $this->db->ServerInfo['collation'] );
        $this->assertTrue( $this->db->ServerInfo['rcsi'] );
        $version = $this->db->databaseServerVersion();
        $this->assertSame( $this->db->ServerInfo['major'], $version['values'][0] );
    }

    public function testSessionOptionsAreInEffect()
    {
        $rows = $this->db->arrayQuery( 'SELECT CAST(SESSIONPROPERTY(\'ANSI_NULLS\') AS INT) AS ansi_nulls, CAST(SESSIONPROPERTY(\'QUOTED_IDENTIFIER\') AS INT) AS qi,'
                                     . ' CAST(SESSIONPROPERTY(\'CONCAT_NULL_YIELDS_NULL\') AS INT) AS cnyn, @@LOCK_TIMEOUT AS lt,'
                                     . ' CASE WHEN (@@OPTIONS & 16384) = 16384 THEN 1 ELSE 0 END AS xact_abort, CASE WHEN (@@OPTIONS & 512) = 512 THEN 1 ELSE 0 END AS nocount' );
        $this->assertSame( array( 'ansi_nulls' => '1', 'qi' => '1', 'cnyn' => '1', 'lt' => '-1', 'xact_abort' => '1', 'nocount' => '1' ), $rows[0] );
    }

    public function testSelfSignedCertificateIsRefusedUnlessTrusted()
    {
        $db = expSQLServerTestKit::liveDriver( array( 'TrustServerCertificate' => 'disabled' ) );
        if ( $db instanceof expSQLServerDB )
        {
            $db->close();
            $this->markTestSkipped( 'The test server has a trusted certificate' );
        }
        $this->assertStringContainsStringIgnoringCase( 'certificate', $db );
    }

    public function testUnencryptedConnectionWorksWhenAllowed()
    {
        $db = $this->other( array( 'Encrypt' => 'no' ) );
        $this->assertSame( '1', $this->value( 'SELECT 1', $db ) );
    }

    public function testLoginTimeoutAgainstNothing()
    {
        $start = microtime( true );
        $db = expSQLServerTestKit::liveDriver( array( 'LoginTimeout' => 2 ), array( 'server' => '127.0.0.1', 'port' => 1 ) );
        $this->assertIsString( $db );
        $this->assertLessThan( 20, microtime( true ) - $start );
    }

    // ---------------------------------------------------------------- LV-02, LV-03

    public function testInsertGivesItsOwnId()
    {
        $t = $this->table( 'id INT IDENTITY(1,1) PRIMARY KEY, name NVARCHAR(50) NOT NULL' );
        $this->assertTrue( $this->db->query( "INSERT INTO $t (name) VALUES ('a')" ) );
        $this->assertSame( 1, $this->db->lastSerialID( $t, 'id' ) );
        $this->assertTrue( $this->db->query( "INSERT INTO $t (id, name) VALUES (NULL, 'b')" ) );
        $this->assertSame( 2, $this->db->lastSerialID( $t, 'id' ) );
        $this->assertTrue( $this->db->query( "INSERT INTO $t (id, name) VALUES (0, 'c')" ) );
        $this->assertSame( 3, $this->db->lastSerialID( $t, 'id' ) );
        $this->assertSame( array( 'a', 'b', 'c' ), $this->db->arrayQuery( "SELECT name FROM $t ORDER BY id", array( 'column' => 'name' ) ) );
    }

    public function testExplicitIdsAndIdentityInsertSwitchedOffAfterFailure()
    {
        $a = $this->table( 'id INT IDENTITY(1,1) PRIMARY KEY, name NVARCHAR(50)' );
        $b = $this->table( 'id INT IDENTITY(1,1) PRIMARY KEY, name NVARCHAR(50)' );
        $this->assertTrue( $this->db->query( "INSERT INTO $a (id, name) VALUES (10, 'x')" ), (string)$this->db->errorMessage() );
        $this->assertSame( 10, $this->db->lastSerialID( $a ) );
        // a duplicate explicit id fails in the middle of the IDENTITY_INSERT batch
        $this->assertFalse( $this->db->query( "INSERT INTO $a (id, name) VALUES (10, 'dup')" ) );
        $this->assertSame( 2627, $this->db->errorNumber() );
        // IDENTITY_INSERT may be on for one table per session: had it stayed on for $a, this would fail with 8107
        $this->assertTrue( $this->db->query( "INSERT INTO $b (id, name) VALUES (5, 'y')" ), (string)$this->db->errorMessage() );
        $this->assertTrue( $this->db->query( "INSERT INTO $a (name) VALUES ('next')" ) );
        $this->assertSame( 11, $this->db->lastSerialID( $a ) );
    }

    public function testExplicitIdIntoIdentityWithoutSwitchIsRefusedByTheServer()
    {
        $t = $this->table( 'id INT IDENTITY(1,1) PRIMARY KEY, name NVARCHAR(50)' );
        $result = $this->db->Transport->run( "INSERT INTO $t (id, name) VALUES (5, N'x')" );
        $this->assertFalse( $result );
        $this->assertSame( 544, $this->db->Transport->error()['number'] );
    }

    public function testTriggerDoesNotChangeLastSerialID()
    {
        $t = $this->table( 'id INT IDENTITY(1,1) PRIMARY KEY, name NVARCHAR(50)' );
        $audit = $this->table( 'id INT IDENTITY(1000,1) PRIMARY KEY, note NVARCHAR(50)', 'audit' );
        $this->assertTrue( $this->db->query( "CREATE TRIGGER [trg_$t] ON [$t] AFTER INSERT AS INSERT INTO [$audit] (note) SELECT name FROM inserted" ) );
        $this->assertTrue( $this->db->query( "INSERT INTO $t (name) VALUES ('a')" ) );
        $this->assertSame( 1, $this->db->lastSerialID( $t ) );
        // what the old extension read: @@IDENTITY is the trigger's insert
        $result = $this->db->Transport->run( "INSERT INTO $t (name) VALUES (N'b'); SELECT CAST(@@IDENTITY AS INT) AS i, CAST(SCOPE_IDENTITY() AS INT) AS s" );
        $this->assertSame( array( 'i' => '1001', 's' => '2' ), $result['sets'][0][0] );
    }

    public function testScopeIdentityInALaterBatchIsLostAfterABoundInsert()
    {
        $t = $this->table( 'id INT IDENTITY(1,1) PRIMARY KEY, name NVARCHAR(50)' );
        $this->db->Transport->run( "INSERT INTO $t (name) VALUES (?)", array( 'x' ) );
        $later = $this->db->Transport->run( 'SELECT CAST(SCOPE_IDENTITY() AS INT) AS s' );
        // the bound insert ran in sp_executesql's (or sp_prepexec's) own scope; the driver reads it in the same batch
        $this->assertNotSame( '1', $later['sets'][0][0]['s'] );
        $this->assertTrue( $this->db->query( "INSERT INTO $t (name) VALUES ('y')" ) );
        $this->assertSame( 2, $this->db->lastSerialID( $t ) );
    }

    public function testIdsOfTwoSessionsDoNotMix()
    {
        $t = $this->table( 'id INT IDENTITY(1,1) PRIMARY KEY, name NVARCHAR(50)' );
        $other = $this->other();
        $this->db->query( "INSERT INTO $t (name) VALUES ('a')" );
        $other->query( "INSERT INTO $t (name) VALUES ('b')" );
        $this->db->query( "INSERT INTO $t (name) VALUES ('c')" );
        $other->query( "INSERT INTO $t (name) VALUES ('d')" );
        $this->assertSame( 3, $this->db->lastSerialID( $t ) );
        $this->assertSame( 4, $other->lastSerialID( $t ) );
    }

    public function testBigintIdentity()
    {
        $t = $this->table( 'id BIGINT IDENTITY(5000000000,1) PRIMARY KEY, name NVARCHAR(5)' );
        $this->db->query( "INSERT INTO $t (name) VALUES ('a')" );
        $this->assertSame( 5000000000, $this->db->lastSerialID( $t ) );
    }

    public function testUpdateAndDelete()
    {
        $t = $this->table( 'id INT IDENTITY(1,1) PRIMARY KEY, name NVARCHAR(50)' );
        $this->db->query( "INSERT INTO $t (name) VALUES ('a'), ('b')" );
        $this->assertTrue( $this->db->query( "UPDATE $t SET name = 'z' WHERE name = 'a'" ) );
        $this->assertTrue( $this->db->query( "DELETE FROM $t WHERE name = 'b'" ) );
        $this->assertSame( array( 'z' ), $this->db->arrayQuery( "SELECT name FROM $t", array( 'column' => 'name' ) ) );
    }

    // ---------------------------------------------------------------- LV-04

    private function pagingTable()
    {
        $t = $this->table( 'id INT PRIMARY KEY, name NVARCHAR(20), grp INT' );
        $values = array();
        for ( $i = 1; $i <= 100; $i++ )
            $values[] = "($i, 'N" . sprintf( '%03d', 101 - $i ) . "', " . ( $i % 7 ) . ')';
        $this->assertTrue( $this->db->query( "INSERT INTO $t (id, name, grp) VALUES " . implode( ', ', $values ) ) );
        return $t;
    }

    public static function pages()
    {
        return array( array( 10, 0 ), array( 10, 10 ), array( 7, 93 ), array( 10, 95 ), array( 1, 99 ), array( 5, 100 ), array( 50, 25 ), array( false, 90 ), array( 100, 0 ), array( 1000, 1 ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('pages')]
    public function testPageEqualsSliceOfOrderedRows( $limit, $offset )
    {
        $t = $this->pagingTable();
        $all = $this->db->arrayQuery( "SELECT id FROM $t ORDER BY id", array( 'column' => 'id' ) );
        $params = array( 'offset' => $offset, 'column' => 'id' );
        if ( $limit !== false )
            $params['limit'] = $limit;
        $page = $this->db->arrayQuery( "SELECT id FROM $t ORDER BY id", $params );
        $this->assertSame( array_slice( $all, $offset, $limit === false ? null : $limit, true ), $page );
    }

    public function testOldOffsetBugIsGone()
    {
        $t = $this->pagingTable();
        // xrowmssql fetched from the first row and keyed it with the offset: offset 50 gave ids 1..10
        $page = $this->db->arrayQuery( "SELECT id FROM $t ORDER BY id", array( 'limit' => 10, 'offset' => 50, 'column' => 'id' ) );
        $this->assertSame( '51', $page[50] );
        $this->assertSame( '60', $page[59] );
    }

    public function testPagingWithOrderByFunction()
    {
        $t = $this->pagingTable();
        $page = $this->db->arrayQuery( "SELECT id FROM $t ORDER BY LOWER(name)", array( 'limit' => 3, 'column' => 'id' ) );
        $this->assertSame( array( '100', '99', '98' ), array_values( $page ) );
    }

    public function testPagingDistinctWithoutOrderBy()
    {
        $t = $this->pagingTable();
        $page = $this->db->arrayQuery( "SELECT DISTINCT grp FROM $t", array( 'limit' => 3, 'offset' => 2 ) );
        $this->assertCount( 3, $page, (string)$this->db->errorMessage() );
        $all = $this->db->arrayQuery( "SELECT DISTINCT grp FROM $t", array( 'limit' => 100 ) );
        $this->assertCount( 7, $all );
    }

    public function testPagingUnionWithoutOrderBy()
    {
        $t = $this->pagingTable();
        $page = $this->db->arrayQuery( "SELECT id FROM $t WHERE id <= 3 UNION SELECT id FROM $t WHERE id >= 98", array( 'limit' => 4, 'offset' => 1 ) );
        $this->assertCount( 4, $page, (string)$this->db->errorMessage() );
    }

    public function testPagingTop()
    {
        $t = $this->pagingTable();
        $page = $this->db->arrayQuery( "SELECT TOP 20 id FROM $t ORDER BY id", array( 'limit' => 5, 'offset' => 18, 'column' => 'id' ) );
        $this->assertCount( 2, $page, (string)$this->db->errorMessage() );
    }

    public function testPagingWithoutOrderByCoversEveryRowOnce()
    {
        $t = $this->pagingTable();
        $seen = array();
        for ( $offset = 0; $offset < 100; $offset += 25 )
            foreach ( $this->db->arrayQuery( "SELECT id FROM $t", array( 'limit' => 25, 'offset' => $offset, 'column' => 'id' ) ) as $id )
                $seen[] = $id;
        $this->assertCount( 100, $seen );
        $this->assertCount( 100, array_unique( $seen ) );
    }

    public function testLimitZero()
    {
        $t = $this->pagingTable();
        $this->assertSame( array(), $this->db->arrayQuery( "SELECT id FROM $t", array( 'limit' => 0 ) ) );
    }

    public function testFetchNextZeroIsAnErrorOnTheServer()
    {
        $this->assertFalse( $this->db->Transport->run( 'SELECT 1 AS a ORDER BY a OFFSET 0 ROWS FETCH NEXT 0 ROWS ONLY' ) );
        $this->assertSame( 10744, $this->db->Transport->error()['number'] );
    }

    public function testOffsetPastTheEnd()
    {
        $t = $this->pagingTable();
        $this->assertSame( array(), $this->db->arrayQuery( "SELECT id FROM $t ORDER BY id", array( 'limit' => 10, 'offset' => 500 ) ) );
    }

    public function testPagingWithStringLiteralsAndGroupBy()
    {
        $t = $this->pagingTable();
        $rows = $this->db->arrayQuery( "SELECT grp, COUNT(*) AS c, 'x' AS tag FROM $t WHERE name LIKE 'N%' GROUP BY grp ORDER BY grp", array( 'limit' => 2, 'offset' => 1 ) );
        $this->assertSame( array( 1 => array( 'grp' => '1', 'c' => '15', 'tag' => 'x' ), 2 => array( 'grp' => '2', 'c' => '15', 'tag' => 'x' ) ), $rows, (string)$this->db->errorMessage() );
    }

    // ---------------------------------------------------------------- LV-05

    public function testCommitAndRollback()
    {
        $t = $this->table( 'id INT PRIMARY KEY' );
        $this->db->begin();
        $this->db->query( "INSERT INTO $t VALUES (1)" );
        $this->db->commit();
        $this->db->begin();
        $this->db->query( "INSERT INTO $t VALUES (2)" );
        $this->db->rollback();
        $this->assertSame( array( '1' ), $this->db->arrayQuery( "SELECT id FROM $t", array( 'column' => 'id' ) ) );
    }

    public function testNestedTransactionCommitsOnce()
    {
        $t = $this->table( 'id INT PRIMARY KEY' );
        $this->db->begin();
        $this->db->begin();
        $this->db->query( "INSERT INTO $t VALUES (1)" );
        $this->db->commit();
        $this->assertSame( '1', $this->value( 'SELECT @@TRANCOUNT' ) );
        $this->db->commit();
        $this->assertSame( '0', $this->value( 'SELECT @@TRANCOUNT' ) );
    }

    public function testErrorInsideTransactionRollsItBack()
    {
        $t = $this->table( 'id INT PRIMARY KEY' );
        $this->db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        $this->db->begin();
        $this->db->query( "INSERT INTO $t VALUES (1)" );
        try
        {
            $this->db->query( "INSERT INTO $t VALUES (1)" );
            $this->fail( 'no exception' );
        }
        catch ( eZDBException $e )
        {
            $this->assertSame( 2627, $e->getCode() );
        }
        $this->assertFalse( $this->db->isTransactionValid() );
        // XACT_ABORT ON: the server rolled the whole transaction back, the first row too
        $this->assertSame( '0', $this->value( 'SELECT @@TRANCOUNT' ) );
        $this->db->rollback();
        $this->assertSame( '0', $this->value( "SELECT COUNT(*) FROM $t" ) );
    }

    public function testReaderIsNotBlockedByWriterWithRcsi()
    {
        $t = $this->table( 'id INT PRIMARY KEY, v INT' );
        $this->db->query( "INSERT INTO $t VALUES (1, 1)" );
        $writer = $this->other();
        $writer->begin();
        $writer->query( "UPDATE $t SET v = 2 WHERE id = 1" );
        $reader = $this->other( array( 'LockTimeout' => 500 ) );
        $this->assertSame( '1', $this->value( "SELECT v FROM $t WHERE id = 1", $reader ) );
        $writer->rollback();
    }

    // ---------------------------------------------------------------- LV-06, LV-07

    public function testDeadlockVictimAndRetry()
    {
        $t = $this->table( 'id INT PRIMARY KEY, v INT' );
        $this->db->query( "INSERT INTO $t VALUES (1, 0), (2, 0)" );
        $this->db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        $this->db->begin();
        $this->db->query( "UPDATE $t SET v = v + 1 WHERE id = 1" );
        $peer = $this->peer( 'deadlock-peer', $t );
        $this->waitUntilRowIsLocked( $t, 2 );
        try
        {
            // the peer holds row 2 and waits for row 1; this waits for row 2: a deadlock, and this session the victim
            $this->db->query( "UPDATE $t SET v = v + 1 WHERE id = 2" );
            $this->fail( 'no deadlock' );
        }
        catch ( eZDBException $e )
        {
            $this->assertSame( 1205, $e->getCode() );
            $this->assertSame( 'deadlock', $this->db->ErrorCategory );
        }
        $this->assertFalse( $this->db->isTransactionValid() );
        $this->db->rollback();
        $result = $this->peerResult( $peer );
        $this->assertTrue( $result['ok'], json_encode( $result ) );
        $this->db->setErrorHandling( eZDB::ERROR_HANDLING_STANDARD );

        // the same with retryOnDeadlock(): the first attempt loses, the second succeeds
        $attempt = 0;
        $peer = null;
        $self = $this;
        $value = $this->db->retryOnDeadlock( function ( $db ) use ( $t, &$attempt, &$peer, $self )
        {
            $attempt++;
            $db->query( "UPDATE $t SET v = v + 10 WHERE id = 1" );
            if ( $attempt === 1 )
            {
                $peer = $self->startPeer( 'deadlock-peer', $t );
                $self->waitUntilRowIsLocked( $t, 2 );
            }
            $db->query( "UPDATE $t SET v = v + 10 WHERE id = 2" );
            return $attempt;
        } );
        $this->peerResult( $peer );
        $this->assertSame( 2, $value );
        // each committed peer added 1 to both rows, the losing attempts nothing, the winning attempt 10
        $this->assertSame( array( '12', '12' ), $this->db->arrayQuery( "SELECT v FROM $t ORDER BY id", array( 'column' => 'v' ) ) );
    }

    /**
     * Waits (at most 20 s) until another session holds a lock on the row: READPAST then skips it.
     */
    public function waitUntilRowIsLocked( $table, $id )
    {
        $probe = $this->others ? $this->others[0] : $this->other();
        for ( $i = 0; $i < 200; $i++ )
        {
            $rows = $probe->arrayQuery( "SELECT COUNT(*) AS c FROM [$table] WITH (READCOMMITTEDLOCK, READPAST) WHERE id = " . (int)$id );
            if ( is_array( $rows ) && $rows[0]['c'] === '0' )
                return;
            usleep( 100000 );
        }
        $this->fail( 'The peer process never locked the row' );
    }

    /** For the closure in testDeadlockVictimAndRetry. */
    public function startPeer( $scenario, $table )
    {
        return $this->peer( $scenario, $table );
    }

    public function testLockTimeout()
    {
        $t = $this->table( 'id INT PRIMARY KEY, v INT' );
        $this->db->query( "INSERT INTO $t VALUES (1, 0)" );
        $holder = $this->other();
        $holder->begin();
        $holder->query( "UPDATE $t SET v = 1 WHERE id = 1" );
        $waiter = $this->other( array( 'LockTimeout' => 300 ) );
        $start = microtime( true );
        $this->assertFalse( $waiter->query( "UPDATE $t SET v = 2 WHERE id = 1" ) );
        $this->assertSame( 1222, $waiter->errorNumber() );
        $this->assertSame( 'lock_timeout', $waiter->ErrorCategory );
        $this->assertSame( 0, $waiter->Retries );
        $this->assertLessThan( 5, microtime( true ) - $start );
        $holder->rollback();
    }

    public function testTableLockBlocksOthers()
    {
        $t = $this->table( 'id INT PRIMARY KEY' );
        $this->db->lock( $t );
        $other = $this->other( array( 'LockTimeout' => 300 ) );
        $this->assertFalse( $other->query( "INSERT INTO $t VALUES (1)" ) );
        $this->assertSame( 1222, $other->errorNumber() );
        $this->db->unlock();
        $this->assertTrue( $other->query( "INSERT INTO $t VALUES (1)" ) );
    }

    public function testApplicationLock()
    {
        $other = $this->other();
        $name = 'exp-test-' . getmypid();
        $this->assertTrue( $this->db->getAppLock( $name, 0 ) );
        $this->assertFalse( $other->getAppLock( $name, 0 ) );
        $this->assertTrue( $this->db->releaseAppLock( $name ) );
        $this->assertTrue( $other->getAppLock( $name, 0 ) );
        $this->assertTrue( $other->releaseAppLock( $name ) );
        $this->assertFalse( $other->releaseAppLock( $name ) );
    }

    // ---------------------------------------------------------------- LV-08

    public function testServerRefusesMoreThan2100Parameters()
    {
        $params = array_fill( 0, 2101, 'x' );
        $sql = 'SELECT 1 AS a WHERE N\'x\' IN (' . implode( ',', array_fill( 0, 2101, '?' ) ) . ')';
        $result = $this->db->Transport->run( $sql, $params );
        if ( $result !== false && $this->db->layer() === 'pdo_odbc' )
            $this->markTestSkipped( 'PDO_ODBC with ODBC Driver 18 sends 2,101 parameters without complaint (measured); the driver keeps its budget of 2,000 anyway' );
        $this->assertFalse( $result );
        $error = $this->db->Transport->error();
        // the server refuses it (8003), or the client library before sending (sqlsrv: IMSSP, no number)
        $this->assertSame( 'too_many_parameters', expSQLServerErrorMap::category( $error['number'], $error['state'], $error['message'] ), json_encode( $error ) );
    }

    public function testManyStringLiteralsStillWork()
    {
        $t = $this->table( 'id INT PRIMARY KEY, name NVARCHAR(20)' );
        $this->db->query( "INSERT INTO $t VALUES (1, 'v7'), (2, 'v2400')" );
        $list = array();
        for ( $i = 0; $i < 2500; $i++ )
            $list[] = "'v$i'";
        $rows = $this->db->arrayQuery( "SELECT id FROM $t WHERE name IN (" . implode( ', ', $list ) . ') ORDER BY id', array( 'column' => 'id' ) );
        $this->assertSame( array( '1', '2' ), $rows, (string)$this->db->errorMessage() );
        $this->assertSame( array(), $this->db->LastParameters );
    }

    public function testInListOfFiveThousandIds()
    {
        $t = $this->pagingTable();
        $in = $this->db->generateSQLINStatement( range( 1, 5000 ), 'id', false, true, 'int' );
        $this->assertSame( '100', $this->value( "SELECT COUNT(*) FROM $t WHERE $in" ) );
        $notIn = $this->db->generateSQLINStatement( range( 1, 5000, 2 ), 'id', true, true, 'int' );
        $this->assertSame( '50', $this->value( "SELECT COUNT(*) FROM $t WHERE $notIn" ) );
    }

    // ---------------------------------------------------------------- LV-09

    public static function texts()
    {
        return array(
            'ascii' => array( 'hello' ),
            'umlauts' => array( 'äöüß ÄÖÜ' ),
            'emoji' => array( '😀👍🏽 family 👨‍👩‍👧' ),
            'cjk' => array( '漢字かなカナ한국어' ),
            'combining' => array( "e\u{0301} a\u{0308}" ),
            'rtl' => array( 'שלום مرحبا' ),
            'quotes' => array( "it's \"quoted\" ''" ),
            'comment markers' => array( "-- not a comment /* nor this */" ),
            'question marks' => array( 'a ? b ? c' ),
            'N prefix inside' => array( "N'x' n'y'" ),
            'backslashes' => array( 'C:\\path\\to\\' ),
            'backslash newline' => array( "line\\\nnext" ),
            'crlf' => array( "a\r\nb" ),
            'tabs' => array( "a\tb" ),
            'percent underscore' => array( '100% _x_' ),
            'brackets' => array( '[a] ]b[' ),
            'empty' => array( '' ),
            'trailing spaces' => array( 'abc   ' ),
            'leading spaces' => array( '   abc' ),
            'semicolon drop' => array( "x'; DROP TABLE y; --" ),
            'html' => array( '<p class="a">&amp;</p>' ),
            'long word' => array( str_repeat( 'ü', 4000 ) ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('texts')]
    public function testTextRoundTripsBound( $text )
    {
        $t = $this->table( 'id INT PRIMARY KEY, v NVARCHAR(MAX)' );
        $this->assertTrue( $this->db->query( "INSERT INTO $t VALUES (1, '" . $this->db->escapeString( $text ) . "')" ), (string)$this->db->errorMessage() );
        $this->assertSame( $text, $this->value( "SELECT v FROM $t WHERE id = 1" ) );
        $this->assertSame( '1', $this->value( "SELECT id FROM $t WHERE v = '" . $this->db->escapeString( $text ) . "'" ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('texts')]
    public function testTextRoundTripsAsNLiteral( $text )
    {
        if ( $this->db->layer() === 'pdo_odbc' && preg_match( '/[^\x00-\x7f]/', $text ) )
            $this->markTestSkipped( 'PDO_ODBC sends the statement text in the client code page (W0): non-ASCII text needs bound parameters there' );
        $db = $this->other( array( 'LiteralMode' => 'prefix' ) );
        $t = $this->table( 'id INT PRIMARY KEY, v NVARCHAR(MAX)' );
        $this->assertTrue( $db->query( "INSERT INTO $t VALUES (1, '" . $db->escapeString( $text ) . "')" ), (string)$db->errorMessage() );
        $this->assertSame( $text, $this->value( "SELECT v FROM $t WHERE id = 1", $db ) );
    }

    public function testLineContinuationEatsTheLineBreakOfAPlainLiteral()
    {
        // the T-SQL rule behind expSQLServerDialect's split of 'a\<LF>b'
        $result = $this->db->Transport->run( "SELECT N'a\\\nb' AS v" );
        $this->assertSame( 'ab', $result['sets'][0][0]['v'] );
    }

    public function testNulIsDropped()
    {
        $t = $this->table( 'id INT PRIMARY KEY, v NVARCHAR(10)' );
        $this->db->query( "INSERT INTO $t VALUES (1, '" . $this->db->escapeString( "a\0b" ) . "')" );
        $this->assertSame( 'ab', $this->value( "SELECT v FROM $t" ) );
    }

    public function testMegabyteOfText()
    {
        $t = $this->table( 'id INT PRIMARY KEY, v NVARCHAR(MAX)' );
        $text = str_repeat( 'ä😀abc', 200000 );
        $this->assertTrue( $this->db->query( "INSERT INTO $t VALUES (1, '" . $this->db->escapeString( $text ) . "')" ), (string)$this->db->errorMessage() );
        $this->assertSame( md5( $text ), md5( (string)$this->value( "SELECT v FROM $t" ) ) );
        // SUBSTRING without a length reaches past 8,000 characters (xrowmssql cut there)
        $sub = $this->value( "SELECT " . $this->db->subString( 'v', 7990 ) . " FROM $t" );
        $this->assertGreaterThan( 8000, mb_strlen( $sub, 'UTF-8' ) );
    }

    public function testBinaryRoundTrip()
    {
        $t = $this->table( 'id INT PRIMARY KEY, b VARBINARY(MAX)' );
        $bytes = '';
        for ( $i = 0; $i < 256; $i++ )
            $bytes .= chr( $i );
        $bytes = str_repeat( $bytes, 400 );
        $this->assertTrue( $this->db->query( "INSERT INTO $t VALUES (1, " . expSQLServerDialect::binaryLiteral( $bytes ) . ')' ) );
        $value = $this->value( "SELECT b FROM $t" );
        if ( $this->db->layer() === 'pdo_odbc' && strlen( $value ) === 2 * strlen( $bytes ) )
            $value = hex2bin( $value );
        $this->assertSame( md5( $bytes ), md5( $value ) );
    }

    public function testEmptyStringIsNotNull()
    {
        $t = $this->table( 'id INT PRIMARY KEY, v NVARCHAR(10) NULL' );
        $this->db->query( "INSERT INTO $t VALUES (1, ''), (2, NULL)" );
        $this->assertSame( array( 1 => '', 2 => null ), array_combine( array( 1, 2 ), $this->db->arrayQuery( "SELECT v FROM $t ORDER BY id", array( 'column' => 'v' ) ) ) );
        $this->assertSame( '1', $this->value( "SELECT COUNT(*) FROM $t WHERE v = ''" ) );
    }

    public function testTrailingSpacesAndCase()
    {
        $t = $this->table( 'id INT PRIMARY KEY, v NVARCHAR(10)' );
        $this->db->query( "INSERT INTO $t VALUES (1, 'Abc  ')" );
        // ANSI padding rules: trailing spaces do not count in =; the default collation ignores case
        $this->assertSame( '1', $this->value( "SELECT COUNT(*) FROM $t WHERE v = 'abc'" ) );
        $this->assertSame( '3', $this->value( "SELECT LEN(v) FROM $t" ) );
        $this->assertSame( '5', $this->value( 'SELECT ' . expSQLServerDialect::charLength( 'v' ) . " FROM $t" ) );
        $this->assertSame( 'Abc  ', $this->value( "SELECT v FROM $t" ) );
        // LIKE does see trailing spaces of the pattern
        $this->assertSame( '0', $this->value( "SELECT COUNT(*) FROM $t WHERE v LIKE 'abc'" ) );
    }

    public function testVarcharInALatinCollationLosesCharacters()
    {
        $t = $this->table( 'id INT PRIMARY KEY, v VARCHAR(20) COLLATE SQL_Latin1_General_CP1_CI_AS' );
        $this->db->query( "INSERT INTO $t VALUES (1, '😀漢')" );
        $this->assertNotSame( '😀漢', $this->value( "SELECT v FROM $t" ) );
    }

    public function testVarcharInAUtf8CollationKeepsCharacters()
    {
        if ( $this->db->ServerInfo['major'] < 15 )
            $this->markTestSkipped( 'UTF-8 collations need SQL Server 2019' );
        if ( $this->db->layer() === 'pdo_odbc' )
            $this->markTestSkipped( 'PDO_ODBC reads VARCHAR columns in the client code page (measured: emoji and CJK lost); keep text in NVARCHAR columns' );
        $t = $this->table( 'id INT PRIMARY KEY, v VARCHAR(40) COLLATE Latin1_General_100_CI_AS_SC_UTF8' );
        $this->db->query( "INSERT INTO $t VALUES (1, '😀漢ä')" );
        $this->assertSame( '😀漢ä', $this->value( "SELECT v FROM $t" ) );
    }

    public static function md5Texts()
    {
        return array( array( 'abc' ), array( 'äöü' ), array( '😀' ), array( '' ), array( "a'b" ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('md5Texts')]
    public function testMd5EqualsPhp( $text )
    {
        if ( $this->db->ServerInfo['major'] < 15 && preg_match( '/[^\x00-\x7f]/', $text ) )
            $this->markTestSkipped( 'SQL Server 2017 hashes the stored bytes' );
        $this->assertSame( md5( $text ), $this->value( 'SELECT ' . $this->db->md5( "'" . $this->db->escapeString( $text ) . "'" ) ) );
    }

    // ---------------------------------------------------------------- LV-10

    public function testUnixTimestampNow()
    {
        $this->assertEqualsWithDelta( time(), (int)$this->value( 'SELECT ' . $this->db->sqlUnixTimestamp() ), 5 );
    }

    public static function timestamps()
    {
        return array( array( 0 ), array( 86399 ), array( 1700000000 ), array( 2147483647 ), array( 2147483748 ), array( 4102444800 ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('timestamps')]
    public function testUnixTimestampRoundTrip( $ts )
    {
        $date = $this->value( 'SELECT CONVERT(VARCHAR(19), ' . $this->db->sqlFromUnixTimestamp( (string)$ts ) . ', 120)' );
        $this->assertSame( gmdate( 'Y-m-d H:i:s', $ts ), $date );
        $this->assertSame( (string)$ts, $this->value( 'SELECT ' . $this->db->sqlUnixTimestamp( $this->db->sqlFromUnixTimestamp( (string)$ts ) ) ) );
    }

    public function testDateTimeComesBackAsString()
    {
        $t = $this->table( 'id INT PRIMARY KEY, d DATETIME2(0), dt DATETIME' );
        $this->db->query( "INSERT INTO $t VALUES (1, '2026-11-24 10:11:12', '2026-11-24 10:11:12')" );
        $row = $this->db->arrayQuery( "SELECT d, dt FROM $t" )[0];
        $this->assertSame( '2026-11-24 10:11:12', substr( $row['d'], 0, 19 ) );
        $this->assertSame( '2026-11-24 10:11:12', substr( $row['dt'], 0, 19 ) );
    }

    // ---------------------------------------------------------------- LV-11

    public function testLargeResultSet()
    {
        $t = $this->table( 'id INT PRIMARY KEY, name NVARCHAR(30)' );
        $this->assertTrue( $this->db->query( "INSERT INTO $t (id, name) SELECT TOP (20000) ROW_NUMBER() OVER (ORDER BY (SELECT NULL)), CONCAT(N'row ', ROW_NUMBER() OVER (ORDER BY (SELECT NULL))) FROM sys.all_objects a CROSS JOIN sys.all_objects b" ) );
        $rows = $this->db->arrayQuery( "SELECT id, name FROM $t ORDER BY id" );
        $this->assertCount( 20000, $rows );
        $this->assertSame( array( 'id' => '20000', 'name' => 'row 20000' ), $rows[19999] );
        $page = $this->db->arrayQuery( "SELECT id FROM $t ORDER BY id", array( 'limit' => 5, 'offset' => 19998, 'column' => 'id' ) );
        $this->assertSame( array( 19998 => '19999', 19999 => '20000' ), $page );
    }

    // ---------------------------------------------------------------- LV-12

    public function testKilledSessionIsOpenedAgain()
    {
        $spid = $this->value( 'SELECT @@SPID' );
        $killer = $this->other();
        $this->assertTrue( $killer->query( 'KILL ' . (int)$spid ), (string)$killer->errorMessage() );
        $this->assertSame( '1', $this->value( 'SELECT 1' ) );
        // the driver opened it again (ODBC's own idle connection resiliency is off: ConnectRetryCount=0); the server
        // may give the new session the same number
        $this->assertSame( 1, $this->db->Reconnects );
    }

    public function testKilledSessionInsideTransactionFails()
    {
        $t = $this->table( 'id INT PRIMARY KEY' );
        $this->db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        $this->db->begin();
        $this->db->query( "INSERT INTO $t VALUES (1)" );
        $spid = $this->value( 'SELECT @@SPID' );
        $this->other()->query( 'KILL ' . (int)$spid );
        try
        {
            $this->db->query( "INSERT INTO $t VALUES (2)" );
            $this->fail( 'no error' );
        }
        catch ( eZDBException $e )
        {
            $this->assertSame( 'connection', $this->db->ErrorCategory );
        }
        $this->assertSame( 0, $this->db->Reconnects );
        $this->db->setErrorHandling( eZDB::ERROR_HANDLING_STANDARD );
        $this->db->TransactionCounter = 0;
        $this->assertTrue( $this->db->reconnect() );
        $this->assertSame( '0', $this->value( "SELECT COUNT(*) FROM $t" ) );
    }

    // ---------------------------------------------------------------- LV-13

    public function testReplicaThatIsThePrimaryIsRead()
    {
        $live = expSQLServerTestKit::live();
        $db = expSQLServerTestKit::liveDriver( array(), array( 'use_slave_server' => true, 'slave_server' => $live['host'], 'slave_port' => $live['port'],
                                                               'slave_user' => $live['user'], 'slave_password' => $live['password'] ) );
        $this->others[] = $db;
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['VELOCITY'] = 1;
        $_COOKIE = array();
        $db->resetReplicaState();
        $this->assertSame( '1', $this->value( 'SELECT 1 AS one', $db ) );
        $this->assertSame( 'on', $db->ReplicaState );
        // the read went over the second connection
        $this->assertTrue( $db->ReplicaTransport->isOpen() );
        $this->assertNotSame( $db->Transport, $db->ReplicaTransport );
        // a session value is never read from the replica
        $this->assertSame( (string)$db->ServerInfo['spid'], $this->value( 'SELECT @@SPID', $db ) );
        $db->query( 'SET NOCOUNT ON' );
        $this->assertTrue( $db->ReplicaPinned );
    }

    // ---------------------------------------------------------------- LV-14

    public function testErrorNumbers()
    {
        $parent = $this->table( 'id INT PRIMARY KEY, code NVARCHAR(5) NOT NULL UNIQUE' );
        $child = $this->table( "id INT PRIMARY KEY, parent_id INT NOT NULL REFERENCES [$parent](id), n INT" );
        $this->db->query( "CREATE UNIQUE INDEX [ux_$child] ON [$child] (n)" );
        $this->db->query( "INSERT INTO $parent VALUES (1, 'a')" );
        $this->db->query( "INSERT INTO $child VALUES (1, 1, 5)" );
        $this->expectError( "INSERT INTO $parent VALUES (1, 'b')", 2627 );
        $this->expectError( "INSERT INTO $child VALUES (2, 1, 5)", 2601 );
        $this->expectError( "INSERT INTO $child VALUES (3, 99, 6)", 547 );
        $this->expectError( "INSERT INTO $parent VALUES (2, NULL)", 515 );
        $this->assertFalse( $this->db->query( "INSERT INTO $parent VALUES (3, 'toolong')" ) );
        // the server (2628, 8152 before 2019), or the ODBC driver before sending a bound value (SQLSTATE 22001)
        $this->assertContains( $this->db->errorNumber(), array( 2628, 8152, '22001' ) );
        $this->assertSame( 'truncation', $this->db->ErrorCategory );
        // the server (245), or the ODBC driver converting the bound 'x' itself (SQLSTATE 22018)
        $this->assertFalse( $this->db->query( "SELECT CAST('x' AS INT)" ) );
        $this->assertContains( $this->db->errorNumber(), array( 245, '22018' ) );
        $this->assertSame( 'conversion', $this->db->ErrorCategory );
        $this->assertFalse( $this->db->query( 'SELECT FROM WHERE' ) );
        $this->assertContains( $this->db->errorNumber(), array( 102, 156 ) );
        $this->assertSame( 'syntax', $this->db->ErrorCategory );
        $this->expectError( 'SELECT * FROM exp_no_such_table', 208 );
    }

    public function testIndexKeyLimits()
    {
        $t = $this->table( 'id INT PRIMARY KEY, v NVARCHAR(1000), m NVARCHAR(MAX)' );
        $this->db->query( "CREATE INDEX [ix_$t] ON [$t] (v)" );
        // 851 characters of NVARCHAR are 1,702 bytes, past the 1,700 bytes of a nonclustered key
        $this->expectError( "INSERT INTO $t (id, v) VALUES (1, '" . str_repeat( 'x', 851 ) . "')", 1946 );
        $this->assertTrue( $this->db->query( "INSERT INTO $t (id, v) VALUES (2, '" . str_repeat( 'x', 850 ) . "')" ) );
        $this->expectError( "CREATE INDEX [ixm_$t] ON [$t] (m)", 1919 );
    }

    public function testNameLongerThan128IsRefusedByTheServer()
    {
        $this->expectError( 'CREATE TABLE [' . str_repeat( 'n', 129 ) . '] (a INT)', 103 );
    }

    // ---------------------------------------------------------------- LV-15

    public function testSchemaReadBack()
    {
        $t = $this->table( "id INT IDENTITY(1,1) PRIMARY KEY, name NVARCHAR(150) NOT NULL DEFAULT N'', body NVARCHAR(MAX) NULL, price DECIMAL(10,2) NULL, flag BIT NOT NULL DEFAULT 0, raw VARBINARY(MAX) NULL, created DATETIME2(0) NULL" );
        $columns = array();
        foreach ( $this->db->tableColumns( $t ) as $column )
            $columns[$column['name']] = $column;
        $this->assertSame( array( 'id', 'name', 'body', 'price', 'flag', 'raw', 'created' ), array_keys( $columns ) );
        $this->assertSame( 'int', $columns['id']['type'] );
        $this->assertSame( '1', $columns['id']['identity'] );
        $this->assertSame( 'nvarchar', $columns['name']['type'] );
        $this->assertSame( '300', $columns['name']['max_length'] );
        $this->assertSame( '0', $columns['name']['nullable'] );
        $this->assertSame( "(N'')", $columns['name']['default'] );
        $this->assertSame( '-1', $columns['body']['max_length'] );
        $this->assertSame( array( 'decimal', '10', '2' ), array( $columns['price']['type'], $columns['price']['precision'], $columns['price']['scale'] ) );
        $this->assertSame( '((0))', $columns['flag']['default'] );
        $this->assertSame( 'varbinary', $columns['raw']['type'] );
        $this->assertSame( 'datetime2', $columns['created']['type'] );
    }

    public function testRelationLists()
    {
        $t = $this->table( 'id INT PRIMARY KEY, v INT' );
        $this->db->query( "CREATE INDEX [ix_$t] ON [$t] (v)" );
        $this->db->query( "CREATE VIEW [v_$t] AS SELECT id FROM [$t]" );
        $this->db->query( "CREATE SEQUENCE [s_$t] START WITH 1" );
        $this->db->query( "CREATE TRIGGER [tr_$t] ON [$t] AFTER UPDATE AS SET NOCOUNT ON" );
        $this->assertContains( $t, $this->db->relationList( eZDBInterface::RELATION_TABLE ) );
        $this->assertContains( "v_$t", $this->db->relationList( eZDBInterface::RELATION_VIEW ) );
        $this->assertContains( "s_$t", $this->db->relationList( eZDBInterface::RELATION_SEQUENCE ) );
        $this->assertContains( "tr_$t", $this->db->relationList( eZDBInterface::RELATION_TRIGGER ) );
        $this->assertContains( "ix_$t", $this->db->relationList( eZDBInterface::RELATION_INDEX ) );
        $this->assertArrayHasKey( $t, $this->db->eZTableList() );
        $this->assertTrue( $this->db->removeRelation( "ix_$t", eZDBInterface::RELATION_INDEX ) );
        $this->assertTrue( $this->db->removeRelation( "v_$t", eZDBInterface::RELATION_VIEW ) );
        $this->assertTrue( $this->db->removeRelation( "s_$t", eZDBInterface::RELATION_SEQUENCE ) );
        $this->assertTrue( $this->db->removeRelation( "tr_$t", eZDBInterface::RELATION_TRIGGER ) );
        $this->assertNotContains( "v_$t", $this->db->relationList( eZDBInterface::RELATION_VIEW ) );
        $this->assertNotContains( "ix_$t", $this->db->relationList( eZDBInterface::RELATION_INDEX ) );
    }

    public function testAvailableDatabases()
    {
        $live = expSQLServerTestKit::live();
        $this->assertContains( $live['database'], $this->db->availableDatabases() );
    }

    // ---------------------------------------------------------------- LV-16

    public function testTemporaryTables()
    {
        $name = $this->db->generateUniqueTempTableName( 'exp_tmp_%' );
        $this->assertTrue( $this->db->createTempTable( "CREATE TEMPORARY TABLE $name ( id INT IDENTITY(1,1), contentobject_id INT )" ), (string)$this->db->errorMessage() );
        $this->assertTrue( $this->db->query( "INSERT INTO $name (contentobject_id) VALUES (5), (6)" ), (string)$this->db->errorMessage() );
        $this->assertTrue( $this->db->query( "INSERT INTO $name (id, contentobject_id) VALUES (NULL, 7)" ), (string)$this->db->errorMessage() );
        $this->assertSame( 3, $this->db->lastSerialID( $name ) );
        $this->assertSame( '3', $this->value( "SELECT COUNT(*) FROM $name" ) );
        // another session does not see it
        $other = $this->other();
        $this->assertFalse( $other->query( "SELECT * FROM $name" ) );
        $this->assertSame( 208, $other->errorNumber() );
        // the request reset drops it
        $this->db->resetReplicaState();
        $this->assertNull( $this->value( "SELECT OBJECT_ID(N'tempdb..$name')" ) );
    }

    public function testTemporaryTableDropTwiceIsHarmless()
    {
        $name = $this->db->generateUniqueTempTableName( 'exp_tmp_%' );
        $this->db->createTempTable( "CREATE TEMPORARY TABLE $name ( a INT )" );
        $this->assertTrue( $this->db->dropTempTable( "DROP TABLE $name" ) );
        $this->assertTrue( $this->db->query( expSQLServerDialect::dropTemporaryTable( $name ) ) );
    }

    // ---------------------------------------------------------------- LV-17

    public function testUpsert()
    {
        $t = $this->table( 'name NVARCHAR(50) PRIMARY KEY, value NVARCHAR(MAX)' );
        $this->assertTrue( $this->db->query( $this->db->sqlUpsert( $t, array( 'name' => 'k', 'value' => 'v1' ), array( 'name' ) ) ), (string)$this->db->errorMessage() );
        $this->assertTrue( $this->db->query( $this->db->sqlUpsert( $t, array( 'name' => 'k', 'value' => 'v2 ä' ), array( 'name' ) ) ) );
        $this->assertSame( 'v2 ä', $this->value( "SELECT value FROM $t WHERE name = 'k'" ) );
        $this->assertTrue( $this->db->query( $this->db->sqlUpsert( $t, array( 'name' => 'k', 'value' => 'v3' ), array( 'name' ), array() ) ) );
        $this->assertSame( 'v2 ä', $this->value( "SELECT value FROM $t WHERE name = 'k'" ) );
        $this->assertSame( '1', $this->value( "SELECT COUNT(*) FROM $t" ) );
    }

    public function testGroupConcatPast8000Bytes()
    {
        $t = $this->table( 'id INT PRIMARY KEY, grp INT, v NVARCHAR(100)' );
        $values = array();
        for ( $i = 1; $i <= 200; $i++ )
            $values[] = "($i, 1, '" . str_repeat( 'x', 99 ) . "')";
        $this->db->query( "INSERT INTO $t VALUES " . implode( ', ', $values ) );
        $this->db->query( "INSERT INTO $t VALUES (201, 2, 'b'), (202, 2, 'a')" );
        $rows = $this->db->arrayQuery( 'SELECT grp, ' . $this->db->sqlGroupConcat( 'v', ',', 'v' ) . " AS joined FROM $t GROUP BY grp ORDER BY grp" );
        $this->assertSame( 200 * 99 + 199, strlen( $rows[0]['joined'] ), (string)$this->db->errorMessage() );
        $this->assertSame( 'a,b', $rows[1]['joined'] );
    }

    public function testConcatTakesNumbersAndNull()
    {
        $this->assertSame( 'a1', $this->value( 'SELECT ' . $this->db->concatString( array( "'a'", '1' ) ) ) );
        $this->assertSame( 'a', $this->value( 'SELECT ' . $this->db->concatString( array( "'a'", 'NULL' ) ) ) );
    }

    public function testBitOperators()
    {
        $this->assertSame( '1', $this->value( 'SELECT ' . $this->db->bitAnd( 5, 3 ) ) );
        $this->assertSame( '7', $this->value( 'SELECT ' . $this->db->bitOr( 5, 3 ) ) );
    }

    public function testMysqlBackticksWork()
    {
        $t = $this->table( 'id INT PRIMARY KEY, [key] NVARCHAR(10)' );
        $this->assertTrue( $this->db->query( "INSERT INTO `$t` (`id`, `key`) VALUES (1, 'k')" ), (string)$this->db->errorMessage() );
        $this->assertSame( 'k', $this->value( "SELECT `key` FROM `$t`" ) );
    }
}
