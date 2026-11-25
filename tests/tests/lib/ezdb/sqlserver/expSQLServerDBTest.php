<?php
/**
 * expSQLServerDB over a recording transport (no server needed): what the driver sends and how it reacts.
 *
 *  DB-01 - Connect: session setup first, server facts read once, databaseName() exactly "SQLServer", unsupported
 *          versions warned, a refused login throws eZDBNoConnectionException without the password
 *  DB-02 - arrayQuery(): paging sent to the server (old bug 1/2), limit 0 sends nothing, keys from the offset,
 *          'column' by name and by position (old bug 9: a column by name never worked)
 *  DB-03 - INSERT: SCOPE_IDENTITY() in the same batch (old bug 3: @@IDENTITY), lastSerialID() per table, NULL/0 ids
 *          left out, explicit ids with IDENTITY_INSERT switched off again after a failure (old bug 4), identity
 *          columns read once per connection (old bug 10: never cached) and again after DDL
 *  DB-04 - Literals: bound by default, N'' with LiteralMode=prefix, MySQL backticks as brackets
 *  DB-05 - Errors: deadlock retried outside a transaction only, lock timeout never, a transaction made invalid,
 *          exceptions mode throws eZDBException with the native number, passwords masked
 *  DB-06 - Lost connection: opened again and the statement sent again outside a transaction; not inside one, not
 *          with #temp tables alive, not with ReconnectOnLoss disabled
 *  DB-07 - Temporary tables: #names, tracked, dropped at the request reset (old bug 8)
 *  DB-08 - Transactions and locks: BEGIN/COMMIT/ROLLBACK guarded by @@TRANCOUNT, lock() as TABLOCKX in a
 *          transaction, application locks, retryOnDeadlock()
 *  DB-09 - Read replica routing: anonymous GET reads go to the replica; writes, POST, temp tables, transactions and
 *          an unreadable replica keep the primary; a failing replica falls back
 *  DB-10 - Relations, versions, helpers (sqlLimit, sqlUpsert, sqlGroupConcat, unix timestamps, IN lists)
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expSQLServerTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('sqlserver')]
class expSQLServerDBTest extends PHPUnit\Framework\TestCase
{
    private $saved;
    private $server;
    private $cookies;

    protected function setUp(): void
    {
        $this->saved = expSQLServerTestKit::quietDebug();
        $this->server = $_SERVER;
        $this->cookies = $_COOKIE;
    }

    protected function tearDown(): void
    {
        expSQLServerTestKit::restoreDebug( $this->saved );
        $_SERVER = $this->server;
        $_COOKIE = $this->cookies;
    }

    private function fake()
    {
        return new expSQLServerFakeTransport();
    }

    private function identity( expSQLServerFakeTransport $fake, array $map )
    {
        $rows = array();
        foreach ( $map as $table => $column )
            $rows[] = array( 'table_name' => $table, 'column_name' => $column );
        $fake->on( '/sys\.identity_columns c/', $rows );
    }

    // ---------------------------------------------------------------- DB-01

    public function testConnectRunsSessionSetupFirst()
    {
        $fake = $this->fake();
        expSQLServerTestKit::driver( $fake );
        $this->assertStringStartsWith( 'SET NOCOUNT ON; SET XACT_ABORT ON', $fake->log[0][0] );
        $this->assertStringContainsString( "SERVERPROPERTY('ProductVersion')", $fake->log[1][0] );
        $this->assertSame( 1, $fake->opens );
    }

    public function testDatabaseNameIsExactlySQLServer()
    {
        $this->assertSame( 'SQLServer', expSQLServerTestKit::driver()->databaseName() );
    }

    public function testConnectionConfigReachesTheTransport()
    {
        $fake = $this->fake();
        expSQLServerTestKit::driver( $fake, array( 'Encrypt' => 'strict', 'MultiSubnetFailover' => 'enabled', 'LoginTimeout' => 9, 'ApplicationName' => 'X' ) );
        $this->assertSame( '127.0.0.1', $fake->config['host'] );
        $this->assertSame( 14330, $fake->config['port'] );
        $this->assertSame( 'exptest', $fake->config['database'] );
        $this->assertSame( 'strict', $fake->config['encrypt'] );
        $this->assertTrue( $fake->config['multi_subnet_failover'] );
        $this->assertSame( 9, $fake->config['login_timeout'] );
        $this->assertSame( 'X', $fake->config['app_name'] );
        $this->assertSame( 'ReadWrite', $fake->config['application_intent'] );
    }

    public function testServerFactsAreRead()
    {
        $db = expSQLServerTestKit::driver();
        $this->assertSame( 'SQL Server 2022', $db->ServerInfo['name'] );
        $this->assertSame( 16, $db->ServerInfo['major'] );
        $this->assertTrue( $db->ServerInfo['rcsi'] );
        $this->assertSame( 57, $db->ServerInfo['spid'] );
        $this->assertSame( array( 'string' => '16.0.4325.4', 'values' => array( 16, 0, 4325, 4 ), 'name' => 'SQL Server 2022' ), $db->databaseServerVersion() );
        $this->assertSame( '16.0.4325.4', $db->version() );
        $this->assertSame( array( 'string' => 'fake 1.2.3', 'values' => array( 1, 2, 3 ) ), $db->databaseClientVersion() );
        $this->assertSame( 'fake', $db->layer() );
    }

    public function testOldServerStillConnectsButIsMarkedUnsupported()
    {
        $fake = $this->fake();
        $fake->version = '13.0.6300.2';
        $db = expSQLServerTestKit::driver( $fake );
        $this->assertTrue( $db->isConnected() );
        $this->assertFalse( $db->ServerInfo['supported'] );
    }

    public function testRefusedLoginThrowsWithoutPassword()
    {
        $fake = $this->fake();
        $fake->refuseOpen = true;
        try
        {
            expSQLServerTestKit::driver( $fake );
            $this->fail( 'no exception' );
        }
        catch ( eZDBNoConnectionException $e )
        {
            $this->assertStringContainsString( '18456', $e->getMessage() );
            $this->assertStringNotContainsString( 'Secret-Pass-123', $e->getMessage() );
        }
    }

    public function testConnectRetries()
    {
        $fake = $this->fake();
        $fake->refuseOpen = true;
        try
        {
            expSQLServerTestKit::driver( $fake, array(), array( 'connect_retries' => 1 ) );
        }
        catch ( eZDBNoConnectionException $e )
        {
        }
        $this->assertSame( 2, $fake->opens );
    }

    public function testFailedSessionSetupIsNoConnection()
    {
        $fake = $this->fake();
        $fake->on( '/^SET NOCOUNT ON/', array( 'error' => array( 4060, '42000', 'Cannot open database' ) ) );
        $this->expectException( eZDBNoConnectionException::class );
        expSQLServerTestKit::driver( $fake );
    }

    public function testCharsetSupport()
    {
        $db = expSQLServerTestKit::driver();
        $this->assertTrue( $db->isCharsetSupported( 'utf-8' ) );
        $this->assertFalse( $db->isCharsetSupported( 'iso-8859-1' ) );
    }

    // ---------------------------------------------------------------- DB-02

    public function testPagingIsSentToTheServer()
    {
        $fake = $this->fake();
        $fake->on( '/FROM t/', array( array( 'id' => '21' ), array( 'id' => '22' ) ) );
        $db = expSQLServerTestKit::driver( $fake );
        $rows = $db->arrayQuery( 'SELECT id FROM t ORDER BY id', array( 'limit' => 2, 'offset' => 20 ) );
        $this->assertSame( "SELECT id FROM t ORDER BY id\nOFFSET 20 ROWS FETCH NEXT 2 ROWS ONLY", $db->LastStatement );
        // keys start at the offset, as on the other drivers; the rows are the server's page (old bug 1: the first rows)
        $this->assertSame( array( 20 => array( 'id' => '21' ), 21 => array( 'id' => '22' ) ), $rows );
    }

    public function testLimitZeroSendsNothing()
    {
        $fake = $this->fake();
        $db = expSQLServerTestKit::driver( $fake );
        $before = count( $fake->log );
        $this->assertSame( array(), $db->arrayQuery( 'SELECT id FROM t', array( 'limit' => 0 ) ) );
        $this->assertCount( $before, $fake->log );
    }

    public function testColumnByName()
    {
        $fake = $this->fake();
        $fake->on( '/FROM t/', array( array( 'id' => '1', 'name' => 'a' ), array( 'id' => '2', 'name' => 'b' ) ) );
        $db = expSQLServerTestKit::driver( $fake );
        $this->assertSame( array( 'a', 'b' ), $db->arrayQuery( 'SELECT id, name FROM t', array( 'column' => 'name' ) ) );
    }

    public function testColumnByPosition()
    {
        $fake = $this->fake();
        $fake->on( '/FROM t/', array( array( 'id' => '1', 'name' => 'a' ) ) );
        $db = expSQLServerTestKit::driver( $fake );
        $this->assertSame( array( 'a' ), $db->arrayQuery( 'SELECT id, name FROM t', array( 'column' => 1 ) ) );
        $this->assertSame( array( '1' ), $db->arrayQuery( 'SELECT id, name FROM t', array( 'column' => '0' ) ) );
    }

    public function testMissingColumnIsNull()
    {
        $fake = $this->fake();
        $fake->on( '/FROM t/', array( array( 'id' => '1' ) ) );
        $db = expSQLServerTestKit::driver( $fake );
        $this->assertSame( array( null ), $db->arrayQuery( 'SELECT id FROM t', array( 'column' => 'nope' ) ) );
    }

    public function testEmptyResult()
    {
        $db = expSQLServerTestKit::driver();
        $this->assertSame( array(), $db->arrayQuery( 'SELECT id FROM empty_table' ) );
    }

    public function testFailedSelectReturnsFalse()
    {
        $fake = $this->fake();
        $fake->on( '/FROM missing/', array( 'error' => array( 208, '42S02', "Invalid object name 'missing'." ) ) );
        $db = expSQLServerTestKit::driver( $fake );
        $this->assertFalse( $db->arrayQuery( 'SELECT id FROM missing' ) );
        $this->assertSame( 208, $db->errorNumber() );
        $this->assertSame( 'unknown_object', $db->ErrorCategory );
    }

    public function testQueryReturnsBool()
    {
        $fake = $this->fake();
        $fake->on( '/^UPDATE bad/', array( 'error' => array( 102, '42000', 'Incorrect syntax' ) ) );
        $db = expSQLServerTestKit::driver( $fake );
        $this->assertTrue( $db->query( 'UPDATE t SET a = 1' ) );
        $this->assertFalse( $db->query( 'UPDATE bad' ) );
    }

    // ---------------------------------------------------------------- DB-03

    public function testInsertCapturesScopeIdentityInTheSameBatch()
    {
        $fake = $this->fake();
        $this->identity( $fake, array( 'ezuser' => 'contentobject_id', 'ezurl' => 'id' ) );
        $db = expSQLServerTestKit::driver( $fake );
        $this->assertTrue( $db->query( "INSERT INTO ezurl (url) VALUES ('http://x')" ) );
        $this->assertStringEndsWith( "SELECT CAST(SCOPE_IDENTITY() AS BIGINT) AS exp_identity", $db->LastStatement );
        $this->assertStringNotContainsString( '@@IDENTITY', $db->LastStatement );
        $this->assertSame( 41, $db->lastSerialID( 'ezurl', 'id' ) );
        $this->assertSame( 41, $db->lastSerialID() );
        $this->assertFalse( $db->lastSerialID( 'ezuser' ) );
    }

    public function testLastSerialIDWithBracketsAndSchema()
    {
        $fake = $this->fake();
        $this->identity( $fake, array( 'ezurl' => 'id' ) );
        $db = expSQLServerTestKit::driver( $fake );
        $db->query( "INSERT INTO [dbo].[ezurl] (url) VALUES ('x')" );
        $this->assertSame( 41, $db->lastSerialID( 'dbo.ezurl' ) );
        $this->assertSame( 41, $db->lastSerialID( '[ezurl]' ) );
        $this->assertSame( 41, $db->lastSerialID( 'EZURL' ) );
    }

    public function testNoIdentityTableCapturesNothing()
    {
        $fake = $this->fake();
        $db = expSQLServerTestKit::driver( $fake );
        $db->query( "INSERT INTO ezsite_data (name, value) VALUES ('a', 'b')" );
        $this->assertStringNotContainsString( 'SCOPE_IDENTITY', $db->LastStatement );
        $this->assertFalse( $db->lastSerialID() );
    }

    public function testNullIdIsLeftOut()
    {
        $fake = $this->fake();
        $this->identity( $fake, array( 'ezurl' => 'id' ) );
        $db = expSQLServerTestKit::driver( $fake );
        $db->query( "INSERT INTO ezurl (id, url) VALUES (NULL, 'x')" );
        $this->assertStringStartsWith( 'INSERT INTO ezurl (url) VALUES (?)', $db->LastStatement );
        $this->assertStringNotContainsString( 'IDENTITY_INSERT', $db->LastStatement );
    }

    public function testExplicitIdSwitchesIdentityInsertForThisStatementOnly()
    {
        $fake = $this->fake();
        $this->identity( $fake, array( 'ezurl' => 'id' ) );
        $db = expSQLServerTestKit::driver( $fake );
        $db->query( "INSERT INTO ezurl (id, url) VALUES (7, 'x')" );
        $this->assertStringStartsWith( "SET IDENTITY_INSERT ezurl ON;\nINSERT INTO ezurl (id, url) VALUES (7, ?)", $db->LastStatement );
        $this->assertStringEndsWith( 'SET IDENTITY_INSERT ezurl OFF', $db->LastStatement );
        // a plain insert afterwards has no IDENTITY_INSERT (old bug 4: on and off around every insert)
        $db->query( "INSERT INTO ezurl (url) VALUES ('y')" );
        $this->assertStringNotContainsString( 'IDENTITY_INSERT', $db->LastStatement );
    }

    public function testIdentityInsertIsSwitchedOffAfterAFailure()
    {
        $fake = $this->fake();
        $this->identity( $fake, array( 'ezurl' => 'id' ) );
        $fake->on( '/SET IDENTITY_INSERT ezurl ON/', array( 'error' => array( 2627, '23000', 'Violation of PRIMARY KEY' ) ) );
        $db = expSQLServerTestKit::driver( $fake );
        $this->assertFalse( $db->query( "INSERT INTO ezurl (id, url) VALUES (7, 'x')" ) );
        $this->assertSame( array( 'SET IDENTITY_INSERT ezurl OFF' ), $fake->sent( '/^SET IDENTITY_INSERT ezurl OFF$/' ) );
        $this->assertSame( 2627, $db->errorNumber() );
        $this->assertSame( 'duplicate_key', $db->ErrorCategory );
    }

    public function testIdentityColumnsAreReadOncePerConnection()
    {
        $fake = $this->fake();
        $this->identity( $fake, array( 'a' => 'id', 'b' => 'id' ) );
        $db = expSQLServerTestKit::driver( $fake );
        $db->query( 'INSERT INTO a (x) VALUES (1)' );
        $db->query( 'INSERT INTO b (x) VALUES (1)' );
        $db->query( 'INSERT INTO c (x) VALUES (1)' );
        $this->assertCount( 1, $fake->sent( '/sys\.identity_columns c/' ) );
    }

    public function testIdentityColumnsAreReadAgainAfterDdl()
    {
        $fake = $this->fake();
        $this->identity( $fake, array( 'a' => 'id' ) );
        $db = expSQLServerTestKit::driver( $fake );
        $db->query( 'INSERT INTO a (x) VALUES (1)' );
        $db->query( 'CREATE TABLE b (id INT IDENTITY PRIMARY KEY, x INT)' );
        $db->query( 'INSERT INTO a (x) VALUES (1)' );
        $this->assertCount( 2, $fake->sent( '/sys\.identity_columns c/' ) );
    }

    public function testTempTableIdentityIsReadFromTempdb()
    {
        $fake = $this->fake();
        $fake->on( '/tempdb\.sys\.identity_columns/', array( array( 'name' => 'n' ) ) );
        $db = expSQLServerTestKit::driver( $fake );
        $this->assertSame( 'n', $db->identityColumn( '#tmp' ) );
        $this->assertSame( 'n', $db->identityColumn( '#tmp' ) );
        $this->assertCount( 1, $fake->sent( '/tempdb\.sys\.identity_columns/' ) );
    }

    // ---------------------------------------------------------------- DB-04

    public function testLiteralsAreBoundByDefault()
    {
        $db = expSQLServerTestKit::driver();
        $db->query( "UPDATE t SET a = 'ä😀' WHERE b = 'it''s'" );
        $this->assertSame( 'UPDATE t SET a = ? WHERE b = ?', $db->LastStatement );
        $this->assertSame( array( 'ä😀', "it's" ), $db->LastParameters );
    }

    public function testPrefixModeWritesNLiterals()
    {
        $db = expSQLServerTestKit::driver( null, array( 'LiteralMode' => 'prefix' ) );
        $db->query( "UPDATE t SET a = 'ä'" );
        $this->assertSame( "UPDATE t SET a = N'ä'", $db->LastStatement );
        $this->assertSame( array(), $db->LastParameters );
    }

    public function testDdlKeepsNLiteralsInBindMode()
    {
        $db = expSQLServerTestKit::driver();
        $db->query( "CREATE TABLE x (a NVARCHAR(5) DEFAULT 'b')" );
        $this->assertSame( "CREATE TABLE x (a NVARCHAR(5) DEFAULT N'b')", $db->LastStatement );
    }

    public function testBackticksBecomeBrackets()
    {
        $db = expSQLServerTestKit::driver();
        $db->query( 'UPDATE `t` SET `key` = 1' );
        $this->assertSame( 'UPDATE [t] SET [key] = 1', $db->LastStatement );
    }

    public function testEscapeString()
    {
        $db = expSQLServerTestKit::driver();
        $this->assertSame( "a''b", $db->escapeString( "a'b\0" ) );
    }

    // ---------------------------------------------------------------- DB-05

    public function testDeadlockOutsideTransactionIsRetried()
    {
        $fake = $this->fake();
        $db = expSQLServerTestKit::driver( $fake );
        $fake->failNext( 1205, '40001', 'deadlocked' )->failNext( 1205, '40001', 'deadlocked' );
        $this->assertTrue( $db->query( 'UPDATE t SET a = 1' ) );
        $this->assertSame( 2, $db->Retries );
        $this->assertCount( 3, $fake->sent( '/^UPDATE t/' ) );
    }

    public function testDeadlockRetriesStopAtTheLimit()
    {
        $fake = $this->fake();
        $db = expSQLServerTestKit::driver( $fake, array( 'DeadlockRetries' => 1 ) );
        $fake->failNext( 1205, '40001', 'd' )->failNext( 1205, '40001', 'd' )->failNext( 1205, '40001', 'd' );
        $this->assertFalse( $db->query( 'UPDATE t SET a = 1' ) );
        $this->assertCount( 2, $fake->sent( '/^UPDATE t/' ) );
        $this->assertSame( 1205, $db->errorNumber() );
        $this->assertSame( 'deadlock', $db->ErrorCategory );
    }

    public function testDeadlockInsideTransactionIsNotRetriedAndInvalidatesIt()
    {
        $fake = $this->fake();
        $db = expSQLServerTestKit::driver( $fake );
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        $db->begin();
        $fake->failNext( 1205, '40001', 'deadlocked' );
        try
        {
            $db->query( 'UPDATE t SET a = 1' );
            $this->fail( 'no exception' );
        }
        catch ( eZDBException $e )
        {
            $this->assertSame( 1205, $e->getCode() );
        }
        $this->assertCount( 1, $fake->sent( '/^UPDATE t/' ) );
        $this->assertFalse( $db->isTransactionValid() );
        $db->rollback();
    }

    public function testLockTimeoutIsNeverRetried()
    {
        $fake = $this->fake();
        $db = expSQLServerTestKit::driver( $fake );
        $fake->failNext( 1222, 'HYT00', 'Lock request time out period exceeded.' );
        $this->assertFalse( $db->query( 'UPDATE t SET a = 1' ) );
        $this->assertCount( 1, $fake->sent( '/^UPDATE t/' ) );
        $this->assertSame( 'lock_timeout', $db->ErrorCategory );
    }

    public function testDuplicateKeyIsNeverRetried()
    {
        $fake = $this->fake();
        $db = expSQLServerTestKit::driver( $fake );
        $db->identityColumn( 't' );
        $fake->failNext( 2601, '23000', 'Cannot insert duplicate key row' );
        $this->assertFalse( $db->query( 'INSERT INTO t (a) VALUES (1)' ) );
        $this->assertSame( 2601, $db->errorNumber() );
    }

    public function testAzureTransientErrorIsRetried()
    {
        $fake = $this->fake();
        $db = expSQLServerTestKit::driver( $fake );
        $fake->failNext( 40613, null, 'Database is not currently available' );
        $this->assertTrue( $db->query( 'UPDATE t SET a = 1' ) );
    }

    public function testExceptionModeThrowsWithNativeNumber()
    {
        $fake = $this->fake();
        $fake->on( '/^UPDATE bad/', array( 'error' => array( 2627, '23000', 'Violation of PRIMARY KEY constraint' ) ) );
        $db = expSQLServerTestKit::driver( $fake );
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        try
        {
            $db->query( 'UPDATE bad' );
            $this->fail( 'no exception' );
        }
        catch ( eZDBException $e )
        {
            $this->assertSame( 2627, $e->getCode() );
        }
    }

    public function testErrorWithoutNumberUsesSqlState()
    {
        $fake = $this->fake();
        $fake->on( '/^UPDATE bad/', array( 'error' => array( null, '07002', 'COUNT field incorrect' ) ) );
        $db = expSQLServerTestKit::driver( $fake );
        $db->query( 'UPDATE bad' );
        $this->assertSame( '07002', $db->errorNumber() );
        $this->assertSame( 'too_many_parameters', $db->ErrorCategory );
    }

    public function testPasswordIsMaskedInErrors()
    {
        $fake = $this->fake();
        $fake->on( '/^UPDATE bad/', array( 'error' => array( 102, '42000', 'near Secret-Pass-123' ) ) );
        $db = expSQLServerTestKit::driver( $fake );
        $db->query( 'UPDATE bad' );
        $this->assertSame( 'near ***', $db->errorMessage() );
    }

    public function testNotConnectedQueryFails()
    {
        $db = expSQLServerTestKit::driver();
        $db->close();
        $this->assertFalse( $db->query( 'SELECT 1' ) );
        $this->assertFalse( $db->isConnected() );
    }

    // ---------------------------------------------------------------- DB-06

    public function testLostConnectionIsOpenedAgainOutsideTransaction()
    {
        $fake = $this->fake();
        $db = expSQLServerTestKit::driver( $fake );
        $fake->failNext( 10054, '08S01', 'TCP Provider: Error code 0x68' );
        $this->assertTrue( $db->query( 'UPDATE t SET a = 1' ) );
        $this->assertSame( 1, $db->Reconnects );
        $this->assertSame( 2, $fake->opens );
        // the session is set up again on the new connection
        $this->assertCount( 2, $fake->sent( '/^SET NOCOUNT ON/' ) );
    }

    public function testLostConnectionInsideTransactionFails()
    {
        $fake = $this->fake();
        $db = expSQLServerTestKit::driver( $fake );
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        $db->begin();
        $fake->failNext( 10054, '08S01', 'link failure' );
        try
        {
            $db->query( 'UPDATE t SET a = 1' );
            $this->fail( 'no exception' );
        }
        catch ( eZDBException $e )
        {
            $this->assertSame( 10054, $e->getCode() );
        }
        $this->assertSame( 0, $db->Reconnects );
        $this->assertFalse( $db->isTransactionValid() );
        $db->rollback();
    }

    public function testLostConnectionWithTempTablesFails()
    {
        $fake = $this->fake();
        $db = expSQLServerTestKit::driver( $fake );
        $db->createTempTable( 'CREATE TEMPORARY TABLE x (a INT)' );
        $fake->failNext( 10054, '08S01', 'link failure' );
        $this->assertFalse( $db->query( 'SELECT a FROM #x' ) );
        $this->assertSame( 0, $db->Reconnects );
    }

    public function testReconnectCanBeSwitchedOff()
    {
        $fake = $this->fake();
        $db = expSQLServerTestKit::driver( $fake, array( 'ReconnectOnLoss' => 'disabled' ) );
        $fake->failNext( 10054, '08S01', 'link failure' );
        $this->assertFalse( $db->query( 'UPDATE t SET a = 1' ) );
        $this->assertSame( 'connection', $db->ErrorCategory );
    }

    public function testReconnectIsTriedOnlyOnce()
    {
        $fake = $this->fake();
        $db = expSQLServerTestKit::driver( $fake );
        $fake->failNext( 10054, '08S01', 'a' );
        $fake->queue[] = null; // session setup of the new connection succeeds
        $fake->queue[] = array( 10054, '08S01', 'b' );
        $this->assertFalse( $db->query( 'UPDATE t SET a = 1' ) );
        $this->assertSame( 1, $db->Reconnects );
    }

    public function testPing()
    {
        $fake = $this->fake();
        $db = expSQLServerTestKit::driver( $fake );
        $this->assertTrue( $db->ping() );
        $fake->failNext( 10054, '08S01', 'gone' );
        $this->assertFalse( $db->ping() );
    }

    // ---------------------------------------------------------------- DB-07

    public function testTemporaryTableBecomesSessionTable()
    {
        $fake = $this->fake();
        $db = expSQLServerTestKit::driver( $fake );
        $name = $db->generateUniqueTempTableName( 'ezsearch_tmp_%' );
        $this->assertStringStartsWith( '#ezsearch_tmp_', $name );
        $db->createTempTable( "CREATE TEMPORARY TABLE $name ( contentobject_id int )" );
        $this->assertSame( "CREATE TABLE $name ( contentobject_id int )", $db->LastStatement );
        $this->assertSame( array( strtolower( $name ) => $name ), $db->TempTables );
    }

    public function testTemporaryTableIsDropped()
    {
        $fake = $this->fake();
        $db = expSQLServerTestKit::driver( $fake );
        $db->createTempTable( 'CREATE TEMPORARY TABLE x (a INT)' );
        $db->dropTempTable( 'DROP TABLE x' );
        $this->assertSame( "IF OBJECT_ID(N'tempdb..#x') IS NOT NULL DROP TABLE #x", $db->LastStatement );
        $this->assertSame( array(), $db->TempTables );
    }

    public function testDropTempTableList()
    {
        $db = expSQLServerTestKit::driver();
        $db->createTempTable( 'CREATE TEMPORARY TABLE #a (x INT)' );
        $db->createTempTable( 'CREATE TEMPORARY TABLE #b (x INT)' );
        $db->dropTempTableList( array( '#a', '#b' ) );
        $this->assertSame( array(), $db->TempTables );
    }

    public function testLeftoverTempTablesAreDroppedAtRequestReset()
    {
        $fake = $this->fake();
        $db = expSQLServerTestKit::driver( $fake );
        $db->createTempTable( 'CREATE TEMPORARY TABLE x (a INT)' );
        $db->createTempTable( 'CREATE TEMPORARY TABLE y (a INT)' );
        $db->resetReplicaState();
        $this->assertSame( array(), $db->TempTables );
        $sent = $fake->sent( '/DROP TABLE #x/' );
        $this->assertStringContainsString( 'DROP TABLE #y', end( $sent ) );
    }

    public function testLeftoverTempTablesStayInsideTransaction()
    {
        $db = expSQLServerTestKit::driver();
        $db->createTempTable( 'CREATE TEMPORARY TABLE x (a INT)' );
        $db->begin();
        $this->assertSame( 0, $db->dropLeftoverTempTables() );
        $db->commit();
        $this->assertSame( 1, $db->dropLeftoverTempTables() );
    }

    public function testDroppingAPermanentTableIsPassedThrough()
    {
        $db = expSQLServerTestKit::driver();
        $db->dropTempTable( 'DROP TABLE real_table' );
        $this->assertSame( 'DROP TABLE real_table', $db->LastStatement );
    }

    // ---------------------------------------------------------------- DB-08

    public function testTransactionStatements()
    {
        $fake = $this->fake();
        $db = expSQLServerTestKit::driver( $fake );
        $db->begin();
        $db->begin();
        $db->commit();
        $db->commit();
        $db->begin();
        $db->rollback();
        $this->assertSame( array( 'BEGIN TRANSACTION', 'IF @@TRANCOUNT > 0 COMMIT TRANSACTION', 'BEGIN TRANSACTION', 'IF @@TRANCOUNT > 0 ROLLBACK TRANSACTION' ),
                           $fake->sent( '/^(BEGIN|IF @@TRANCOUNT)/' ) );
    }

    public function testLockTakesTableLocksInATransaction()
    {
        $fake = $this->fake();
        $db = expSQLServerTestKit::driver( $fake );
        $db->lock( array( array( 'table' => 'ezcontentobject' ), array( 'table' => 'ezcontentobject_tree' ) ) );
        $this->assertSame( 1, $db->transactionCounter() );
        $this->assertSame( array( 'SELECT TOP (0) 1 AS locked FROM [ezcontentobject] WITH (TABLOCKX, HOLDLOCK)',
                                  'SELECT TOP (0) 1 AS locked FROM [ezcontentobject_tree] WITH (TABLOCKX, HOLDLOCK)' ), $fake->sent( '/TABLOCKX/' ) );
        $db->unlock();
        $this->assertSame( 0, $db->transactionCounter() );
    }

    public function testLockSingleTable()
    {
        $fake = $this->fake();
        $db = expSQLServerTestKit::driver( $fake );
        $db->lock( 'ezuser' );
        $this->assertCount( 1, $fake->sent( '/\[ezuser\] WITH \(TABLOCKX/' ) );
        $db->unlock();
    }

    public function testAppLock()
    {
        $fake = $this->fake();
        $fake->on( '/sp_getapplock/', array( array( 'result' => '0' ) ) );
        $fake->on( '/sp_releaseapplock/', array( array( 'result' => '0' ) ) );
        $db = expSQLServerTestKit::driver( $fake );
        $this->assertTrue( $db->getAppLock( 'cluster-maintenance', 1000 ) );
        $this->assertStringContainsString( "@LockOwner = N'Session', @LockTimeout = 1000", $db->LastStatement );
        $this->assertTrue( $db->releaseAppLock( 'cluster-maintenance' ) );
    }

    public function testAppLockTimeout()
    {
        $fake = $this->fake();
        $fake->on( '/sp_getapplock/', array( array( 'result' => '-1' ) ) );
        $db = expSQLServerTestKit::driver( $fake );
        $this->assertFalse( $db->getAppLock( 'x', 0 ) );
    }

    public function testRetryOnDeadlockRunsTheWorkAgain()
    {
        $fake = $this->fake();
        $db = expSQLServerTestKit::driver( $fake );
        $calls = 0;
        $value = $db->retryOnDeadlock( function ( $db ) use ( &$calls, $fake )
        {
            $calls++;
            if ( $calls === 1 )
                $fake->failNext( 1205, '40001', 'deadlocked' );
            $db->query( 'UPDATE t SET a = a + 1' );
            return 'done';
        } );
        $this->assertSame( 'done', $value );
        $this->assertSame( 2, $calls );
        $this->assertSame( 0, $db->transactionCounter() );
        $this->assertSame( eZDB::ERROR_HANDLING_STANDARD, $this->errorHandlingOf( $db ) );
    }

    public function testRetryOnDeadlockGivesUpOnOtherErrors()
    {
        $fake = $this->fake();
        $db = expSQLServerTestKit::driver( $fake );
        $calls = 0;
        try
        {
            $db->retryOnDeadlock( function ( $db ) use ( &$calls, $fake )
            {
                $calls++;
                $fake->failNext( 2627, '23000', 'duplicate' );
                $db->query( 'UPDATE t SET a = 1' );
            } );
            $this->fail( 'no exception' );
        }
        catch ( eZDBException $e )
        {
            $this->assertSame( 2627, $e->getCode() );
        }
        $this->assertSame( 1, $calls );
        $this->assertSame( 0, $db->transactionCounter() );
    }

    private function errorHandlingOf( $db )
    {
        $p = new ReflectionProperty( 'eZDBInterface', 'errorHandling' );
        return $p->getValue( $db );
    }

    // ---------------------------------------------------------------- DB-09

    private function replicaDriver( expSQLServerFakeTransport $primary, expSQLServerFakeTransport $replica, $updateability = 'READ_ONLY' )
    {
        $replica->on( '/Updateability/', array( array( 'updateability' => $updateability ) ) );
        $db = expSQLServerTestKit::driver( $primary, array( 'ReplicaTransport' => $replica ),
                                           array( 'use_slave_server' => true, 'slave_server' => 'ag-listener', 'slave_port' => 1433 ) );
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['VELOCITY'] = 1;
        $_COOKIE = array();
        $db->resetReplicaState();
        return $db;
    }

    public function testAnonymousGetReadGoesToTheReplica()
    {
        $primary = $this->fake();
        $replica = $this->fake();
        $db = $this->replicaDriver( $primary, $replica );
        $db->arrayQuery( 'SELECT id FROM ezcontentobject' );
        $this->assertCount( 1, $replica->sent( '/FROM ezcontentobject/' ) );
        $this->assertCount( 0, $primary->sent( '/FROM ezcontentobject/' ) );
        $this->assertSame( 'ReadOnly', $replica->config['application_intent'] );
        $this->assertSame( 'ag-listener', $replica->config['host'] );
    }

    public function testWriteGoesToThePrimaryAndPinsTheRequest()
    {
        $primary = $this->fake();
        $replica = $this->fake();
        $db = $this->replicaDriver( $primary, $replica );
        $db->query( 'UPDATE t SET a = 1' );
        $db->arrayQuery( 'SELECT id FROM t' );
        $this->assertCount( 0, $replica->sent( '/FROM t/' ) );
        $this->assertCount( 1, $primary->sent( '/FROM t/' ) );
        $this->assertTrue( $db->ReplicaPinned );
    }

    public function testPinIsResetForTheNextRequest()
    {
        $primary = $this->fake();
        $replica = $this->fake();
        $db = $this->replicaDriver( $primary, $replica );
        $db->query( 'UPDATE t SET a = 1' );
        $db->resetReplicaState();
        $db->arrayQuery( 'SELECT id FROM t' );
        $this->assertCount( 1, $replica->sent( '/FROM t/' ) );
    }

    public function testPostReadsThePrimary()
    {
        $primary = $this->fake();
        $replica = $this->fake();
        $db = $this->replicaDriver( $primary, $replica );
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $db->arrayQuery( 'SELECT id FROM t' );
        $this->assertCount( 0, $replica->sent( '/FROM t/' ) );
    }

    public function testSignedInUserReadsThePrimary()
    {
        $primary = $this->fake();
        $replica = $this->fake();
        $db = $this->replicaDriver( $primary, $replica );
        $_COOKIE['is_logged_in'] = 'true';
        $db->arrayQuery( 'SELECT id FROM t' );
        $this->assertCount( 0, $replica->sent( '/FROM t/' ) );
    }

    public function testTempTablePinsThePrimary()
    {
        $primary = $this->fake();
        $replica = $this->fake();
        $db = $this->replicaDriver( $primary, $replica );
        $db->createTempTable( 'CREATE TEMPORARY TABLE x (a INT)' );
        $db->arrayQuery( 'SELECT id FROM t' );
        $this->assertCount( 0, $replica->sent( '/FROM t/' ) );
    }

    public function testTransactionReadsThePrimary()
    {
        $primary = $this->fake();
        $replica = $this->fake();
        $db = $this->replicaDriver( $primary, $replica );
        $db->begin();
        $db->arrayQuery( 'SELECT id FROM t' );
        $db->commit();
        $this->assertCount( 0, $replica->sent( '/FROM t/' ) );
    }

    public function testUnreadableReplicaIsNotUsed()
    {
        $primary = $this->fake();
        $replica = $this->fake();
        $db = $this->replicaDriver( $primary, $replica, 'UNKNOWN' );
        $db->arrayQuery( 'SELECT id FROM t' );
        $this->assertCount( 0, $replica->sent( '/FROM t/' ) );
        $this->assertSame( 'off', $db->ReplicaState );
    }

    public function testReplicaBehindTheLagLimitIsNotUsed()
    {
        $primary = $this->fake();
        $replica = $this->fake();
        $replica->on( '/secondary_lag_seconds/', array( array( 'lag' => '60' ) ) );
        $db = $this->replicaDriver( $primary, $replica );
        $db->ReplicaSettings['SlaveServerMaxLag'] = 10;
        $db->arrayQuery( 'SELECT id FROM t' );
        $this->assertCount( 0, $replica->sent( '/FROM t/' ) );
    }

    public function testFailingReplicaFallsBackToThePrimary()
    {
        $primary = $this->fake();
        $replica = $this->fake();
        $primary->on( '/FROM t/', array( array( 'id' => '1' ) ) );
        $replica->on( '/FROM t/', array( 'error' => array( 10054, '08S01', 'gone' ) ) );
        $db = $this->replicaDriver( $primary, $replica );
        $this->assertSame( array( array( 'id' => '1' ) ), $db->arrayQuery( 'SELECT id FROM t' ) );
        $this->assertSame( 'off', $db->ReplicaState );
        $this->assertNull( $db->ReplicaTransport );
    }

    public function testRefusedReplicaConnectionReadsThePrimary()
    {
        $primary = $this->fake();
        $replica = $this->fake();
        $replica->refuseOpen = true;
        $db = $this->replicaDriver( $primary, $replica );
        $db->arrayQuery( 'SELECT id FROM t' );
        $this->assertCount( 1, $primary->sent( '/FROM t/' ) );
    }

    public function testCliProcessNeverReadsTheReplica()
    {
        $primary = $this->fake();
        $replica = $this->fake();
        $db = $this->replicaDriver( $primary, $replica );
        unset( $_SERVER['VELOCITY'] );
        $db->resetReplicaState();
        $db->arrayQuery( 'SELECT id FROM t' );
        $this->assertCount( 0, $replica->sent( '/FROM t/' ) );
    }

    // ---------------------------------------------------------------- DB-10

    public function testRelationListQueries()
    {
        $fake = $this->fake();
        $fake->on( '/FROM sys\.tables WHERE/', array( array( 'name' => 'ezuser' ), array( 'name' => 'ezurl' ) ) );
        $fake->on( '/FROM sys\.sequences/', array( array( 'name' => 's1' ) ) );
        $db = expSQLServerTestKit::driver( $fake );
        $this->assertSame( array( 'ezuser', 'ezurl' ), $db->relationList() );
        $this->assertSame( 2, $db->relationCount() );
        $this->assertSame( array( 'ezuser' => 0, 'ezurl' => 0, 's1' => 1 ), $db->eZTableList() );
        $this->assertSame( 3, $db->relationCounts( eZDBInterface::RELATION_TABLE_BIT | eZDBInterface::RELATION_SEQUENCE_BIT ) );
        $this->assertFalse( $db->relationList( 99 ) );
    }

    public static function relationTypes()
    {
        return array( array( eZDBInterface::RELATION_TABLE, 'sys.tables' ), array( eZDBInterface::RELATION_SEQUENCE, 'sys.sequences' ),
                      array( eZDBInterface::RELATION_TRIGGER, 'sys.triggers' ), array( eZDBInterface::RELATION_VIEW, 'sys.views' ),
                      array( eZDBInterface::RELATION_INDEX, 'sys.indexes' ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('relationTypes')]
    public function testRelationListQueryUsesCatalogViews( $type, $view )
    {
        $sql = expSQLServerDB::relationListQuery( $type );
        $this->assertStringContainsString( $view, $sql );
        $this->assertStringNotContainsString( 'sysobjects', $sql );
    }

    public function testRemoveRelation()
    {
        $fake = $this->fake();
        $db = expSQLServerTestKit::driver( $fake );
        $db->removeRelation( 'ezuser', eZDBInterface::RELATION_TABLE );
        $this->assertSame( 'DROP TABLE [ezuser]', $db->LastStatement );
        $db->removeRelation( 'v', eZDBInterface::RELATION_VIEW );
        $this->assertSame( 'DROP VIEW [v]', $db->LastStatement );
        $fake->on( '/FROM sys\.indexes WHERE name/', array( array( 'table_name' => 'ezuser' ) ) );
        $db->removeRelation( 'ezuser_login', eZDBInterface::RELATION_INDEX );
        $this->assertSame( 'DROP INDEX [ezuser_login] ON [ezuser]', $db->LastStatement );
    }

    public function testSupportedRelationTypes()
    {
        $db = expSQLServerTestKit::driver();
        $this->assertCount( 5, $db->supportedRelationTypes() );
        $this->assertSame( eZDBInterface::RELATION_MASK, $db->supportedRelationTypeMask() );
    }

    public function testCreateDatabaseWithCollation()
    {
        $db = expSQLServerTestKit::driver( null, array( 'DatabaseCollation' => 'Latin1_General_100_CI_AS_SC_UTF8' ) );
        $db->createDatabase( 'site' );
        $this->assertSame( 'CREATE DATABASE [site] COLLATE Latin1_General_100_CI_AS_SC_UTF8', $db->LastStatement );
        $db->removeDatabase( 'site' );
        $this->assertSame( 'DROP DATABASE [site]', $db->LastStatement );
    }

    public function testCollationSettingCannotInject()
    {
        $db = expSQLServerTestKit::driver( null, array( 'DatabaseCollation' => 'x; DROP DATABASE y' ) );
        $db->createDatabase( 'site' );
        $this->assertSame( 'CREATE DATABASE [site]', $db->LastStatement );
    }

    public function testHelpers()
    {
        $db = expSQLServerTestKit::driver();
        $this->assertSame( "SELECT a FROM t ORDER BY a\nOFFSET 0 ROWS FETCH NEXT 1 ROWS ONLY", $db->sqlLimit( 'SELECT a FROM t ORDER BY a', 1 ) );
        $this->assertStringStartsWith( 'MERGE INTO [t] WITH (HOLDLOCK)', $db->sqlUpsert( 't', array( 'a' => 1 ), array( 'a' ) ) );
        $this->assertStringStartsWith( 'STRING_AGG(', $db->sqlGroupConcat( 'a' ) );
        $this->assertStringStartsWith( 'DATEDIFF_BIG(', $db->sqlUnixTimestamp() );
        $this->assertStringContainsString( 'DATEADD(', $db->sqlFromUnixTimestamp( 'x' ) );
        $this->assertSame( '[dbo].[t]', $db->sqlQuoteIdentifier( 'dbo.t' ) );
        $this->assertSame( 'SUBSTRING(a, 1, 2147483647)', $db->subString( 'a', 1 ) );
        $this->assertSame( 'CONCAT(a, b)', $db->concatString( array( 'a', 'b' ) ) );
        $this->assertStringContainsString( '_UTF8', $db->md5( 'a' ) );
        $this->assertSame( '(a & 1)', $db->bitAnd( 'a', 1 ) );
        $this->assertSame( '(a | 1)', $db->bitOr( 'a', 1 ) );
        $this->assertTrue( $db->supportsDefaultValuesInsertion() );
        $this->assertSame( eZDBInterface::BINDING_NO, $db->bindingType() );
    }

    public function testMd5FollowsServerVersion()
    {
        $fake = $this->fake();
        $fake->version = '14.0.3456.2';
        $db = expSQLServerTestKit::driver( $fake );
        $this->assertStringNotContainsString( 'UTF8', $db->md5( 'a' ) );
    }

    public function testGenerateSqlInStatementChunksAndUniques()
    {
        $db = expSQLServerTestKit::driver( null, array( 'InListChunkSize' => 2 ) );
        $this->assertSame( '( id IN ( 1, 2 ) OR id IN ( 3 ) )', $db->generateSQLINStatement( array( 1, 2, 2, 3 ), 'id', false, true, 'int' ) );
        $this->assertSame( 'id NOT IN ( 5 )', $db->generateSQLINStatement( 5, 'id', true, true, 'int' ) );
        $this->assertSame( '(1=0)', $db->generateSQLINStatement( array(), 'id' ) );
    }

    public function testTableColumnsQuery()
    {
        $fake = $this->fake();
        $db = expSQLServerTestKit::driver( $fake );
        $db->tableColumns( 'ezuser' );
        $this->assertStringContainsString( 'sys.columns', $db->LastStatement );
        $this->assertSame( array( 'ezuser' ), $db->LastParameters );
    }

    public function testCloseClosesEverything()
    {
        $fake = $this->fake();
        $db = expSQLServerTestKit::driver( $fake );
        $db->close();
        $this->assertFalse( $fake->isOpen() );
        $this->assertFalse( $db->isConnected() );
    }
}
