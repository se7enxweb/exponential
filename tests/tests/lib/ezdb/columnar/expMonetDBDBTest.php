<?php
/**
 * The MonetDB driver (expMonetDBDB) on a stand-in for the protocol client: connecting, the schema, the write lock
 * around writes and transactions, the wait for it, values read back, new ids, errors.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expColumnarTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('columnar')]
class expMonetDBDBTest extends PHPUnit\Framework\TestCase
{
    private $saved;

    protected function setUp(): void
    {
        $this->saved = expColumnarTestKit::quietDebug();
    }

    protected function tearDown(): void
    {
        expColumnarTestKit::restoreDebug( $this->saved );
    }

    /** A client whose lock table is there and whose schema is "site" */
    private static function mapi()
    {
        $mapi = new expMonetDBFakeMapi();
        $mapi->Answers = array(
            '/^SELECT CURRENT_SCHEMA/' => array( array( 'type' => 'table', 'id' => 0, 'count' => 1, 'columns' => array( 's' ), 'types' => array( 'varchar' ),
                                                        'rows' => array( array( 'site' ) ) ) ),
            '/^SELECT n FROM "site"\.monetdb_write_lock/' => array( array( 'type' => 'table', 'id' => 1, 'count' => 1, 'columns' => array( 'n' ),
                                                                           'types' => array( 'bigint' ), 'rows' => array( array( '3' ) ) ) ),
            '/^START TRANSACTION/' => array( array( 'type' => 'trans', 'autocommit' => false ) ),
            '/^(COMMIT|ROLLBACK)/' => array( array( 'type' => 'trans', 'autocommit' => true ) ),
        );
        return $mapi;
    }

    public function testConnect()
    {
        $mapi = self::mapi();
        $db = expColumnarTestKit::monetdb( $mapi, array( 'Schema' => 'site' ) );
        $this->assertTrue( $db->isConnected() );
        $this->assertSame( array( 'mdb.example', 50000, 'u', 'p', 'site' ), $mapi->Connects[0] );
        $this->assertSame( 'SET SCHEMA "site"', $mapi->Sent[0] );
        $this->assertSame( 'monetdb', $db->databaseName() );
        $this->assertSame( 'monetdb', $db->sqlHelperDialect() );

        $failing = new expMonetDBFakeMapi();
        $failing->ConnectError = 'Cannot connect to MonetDB at tcp://mdb.example:50000: refused';
        try
        {
            expColumnarTestKit::monetdb( $failing );
            $this->fail( 'no exception' );
        }
        catch ( eZDBNoConnectionException $e )
        {
            $this->assertStringContainsString( 'refused', $e->getMessage() );
        }
        // the setup wizard asks the driver before it has a database
        $none = new expMonetDBDB( array( 'database' => '', 'monetdb_mapi' => new expMonetDBFakeMapi() ) );
        $this->assertFalse( $none->isConnected() );
    }

    public function testWriteOutsideATransactionTakesTheLock()
    {
        $mapi = self::mapi();
        $db = expColumnarTestKit::monetdb( $mapi );
        $mapi->Sent = array();
        $this->assertTrue( $db->query( "INSERT INTO ezsection (name) VALUES ('x')" ) );
        $this->assertSame( array( 'SELECT CURRENT_SCHEMA AS s', 'SELECT n FROM "site".monetdb_write_lock WHERE name = \'write\'', 'START TRANSACTION',
                                  'UPDATE "site".monetdb_write_lock SET n = n + 1 WHERE name = \'write\'', "INSERT INTO ezsection (name) VALUES ('x')", 'COMMIT' ),
                           $mapi->Sent );
        // a read is sent as it is, and the lock table is not looked for again
        $mapi->Sent = array();
        $db->arrayQuery( 'SELECT 1 AS one' );
        $db->query( "UPDATE ezsection SET name = 'y'" );
        $this->assertSame( array( 'SELECT 1 AS one', 'START TRANSACTION', 'UPDATE "site".monetdb_write_lock SET n = n + 1 WHERE name = \'write\'',
                                  "UPDATE ezsection SET name = 'y'", 'COMMIT' ), $mapi->Sent );
    }

    public function testTransactionTakesTheLockOnce()
    {
        $mapi = self::mapi();
        $db = expColumnarTestKit::monetdb( $mapi );
        $db->begin();
        $mapi->Sent = array();
        $db->query( "INSERT INTO t VALUES (1)" );
        $db->query( "INSERT INTO t VALUES (2)" );
        $db->commit();
        $this->assertSame( array( 'INSERT INTO t VALUES (1)', 'INSERT INTO t VALUES (2)', 'COMMIT' ), $mapi->Sent );
        $this->assertSame( 0, $db->transactionCounter() );
    }

    public function testLockWaitRunsOut()
    {
        $mapi = self::mapi();
        $mapi->Answers = array( '/^UPDATE "site"\.monetdb_write_lock/' => array( array( 'type' => 'error', 'message' => 'Update failed due to conflict with another transaction',
                                                                                     'sqlstate' => '42000' ) ) ) + $mapi->Answers;
        $db = expColumnarTestKit::monetdb( $mapi, array( 'LockWait' => 0.05 ) );
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        $started = microtime( true );
        try
        {
            $db->query( 'DELETE FROM t' );
            $this->fail( 'no exception' );
        }
        catch ( eZDBException $e )
        {
            $this->assertSame( '55P03', $e->getCode() );
        }
        $this->assertGreaterThanOrEqual( 0.05, microtime( true ) - $started );
        $this->assertGreaterThan( 0, $db->LockWaits );
        $this->assertNotContains( 'DELETE FROM t', $mapi->Sent, 'the write was never sent' );
        $this->assertContains( 'ROLLBACK', $mapi->Sent, 'each try ends its transaction' );
    }

    public function testWithoutTheWriteLock()
    {
        $mapi = self::mapi();
        $db = expColumnarTestKit::monetdb( $mapi, array( 'WriteLock' => 'disabled', 'ConflictRetries' => 2 ) );
        $mapi->Answers = array( '/^UPDATE t/' => array( array( 'type' => 'error', 'message' => 'COMMIT: transaction is aborted because of concurrency conflicts',
                                                               'sqlstate' => '40001' ) ) ) + $mapi->Answers;
        $mapi->Sent = array();
        $this->assertFalse( $db->query( 'UPDATE t SET a = 1' ) );
        $this->assertSame( array( 'UPDATE t SET a = 1', 'UPDATE t SET a = 1', 'UPDATE t SET a = 1' ), $mapi->Sent, 'sent again twice after a conflict' );
        $this->assertSame( '40001', $db->errorNumber() );
        $mapi->Sent = array();
        $db->query( 'INSERT INTO u VALUES (1)' );
        $this->assertSame( array( 'INSERT INTO u VALUES (1)' ), $mapi->Sent, 'no lock' );
    }

    public function testValuesIdsAndPaging()
    {
        $mapi = self::mapi();
        $mapi->Answers = array(
            '/^SELECT \* FROM t/' => array( array( 'type' => 'table', 'id' => 4, 'count' => 2, 'columns' => array( 'id', 'ok', 'ts', 't' ),
                                                   'types' => array( 'int', 'boolean', 'timestamp', 'time' ),
                                                   'rows' => array( array( '1', 'true', '2026-01-02 03:04:05.250000', '10:11:12.000000' ),
                                                                    array( '2', 'false', null, null ) ) ) ),
            '/^INSERT INTO "?ezx/' => array( array( 'type' => 'update', 'affected' => 1, 'lastid' => 17 ) ),
            '/^INSERT INTO ezy/' => array( array( 'type' => 'update', 'affected' => 1, 'lastid' => -1 ) ),
        ) + $mapi->Answers;
        $db = expColumnarTestKit::monetdb( $mapi, array( 'WriteLock' => 'disabled' ) );
        $rows = $db->arrayQuery( 'SELECT * FROM t' );
        $this->assertSame( array( 'id' => '1', 'ok' => '1', 'ts' => '2026-01-02 03:04:05', 't' => '10:11:12' ), $rows[0] );
        $this->assertSame( array( 'id' => '2', 'ok' => '0', 'ts' => null, 't' => null ), $rows[1] );
        $this->assertSame( array( 5 => '1', 6 => '2' ), $db->arrayQuery( 'SELECT * FROM t', array( 'offset' => 5, 'limit' => 2, 'column' => 'id' ) ) );
        $this->assertSame( "SELECT * FROM t\nLIMIT 2 OFFSET 5", end( $mapi->Sent ) );
        $this->assertSame( array(), $db->arrayQuery( 'SELECT * FROM t', array( 'limit' => 0 ) ) );

        $db->query( "INSERT INTO ezx (name) VALUES ('a')" );
        $db->query( "INSERT INTO ezy VALUES (1)" );
        $this->assertSame( 17, $db->lastSerialID( 'ezx', 'id' ) );
        $this->assertSame( 17, $db->lastSerialID( 'other', 'id' ), 'another table: the last id of the connection' );
    }

    public function testErrorsAndLostConnection()
    {
        $mapi = self::mapi();
        $mapi->Answers = array( '/missing/' => array( array( 'type' => 'error', 'message' => "SELECT: no such table 'missing'", 'sqlstate' => '42S02' ) ),
                                '/^SELECT 2/' => false ) + $mapi->Answers;
        $db = expColumnarTestKit::monetdb( $mapi );
        $this->assertFalse( $db->arrayQuery( 'SELECT * FROM missing' ) );
        $this->assertSame( '42P01', $db->errorNumber() );
        $this->assertFalse( $db->arrayQuery( 'SELECT 2' ) );
        $this->assertSame( '08006', $db->errorNumber() );
        $this->assertFalse( $db->isConnected() );
    }

    public function testDatabasesAndStrings()
    {
        $db = expColumnarTestKit::monetdb( self::mapi() );
        $this->assertSame( array( 'site' ), $db->availableDatabases() );
        $db->createDatabase( 'other' );
        $this->assertStringContainsString( 'monetdb create other', $db->errorMessage() );
        $this->assertSame( "it''s a \\\\", $db->escapeString( "it's a \\" ) );
        $this->assertSame( ' SUBSTRING( name FROM 2 FOR 3 ) ', $db->subString( 'name', 2, 3 ) );
        $this->assertSame( ' a || b ', $db->concatString( array( 'a', 'b' ) ) );
        $this->assertTrue( $db->supportsDefaultValuesInsertion() );
        $this->assertTrue( $db->isCharsetSupported( 'utf-8' ) );
        $this->assertFalse( $db->isCharsetSupported( 'iso-8859-1' ) );
        $this->assertSame( '1', expMonetDBDB::value( 'true', 'boolean' ) );
        $this->assertSame( 'true', expMonetDBDB::value( 'true', 'varchar' ) );
    }
}
