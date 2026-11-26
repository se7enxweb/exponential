<?php
/**
 * expFirebirdDB over a recording transport (no server needed).
 *
 *  FB-01 - Connecting: the eZDB parameters reach the transport; a refused login throws eZDBNoConnectionException
 *          without the password; databaseName(), the server version, the helper dialect
 *  FB-02 - Statements go out rewritten; arrayQuery() pages on the server, keys rows from the offset, takes a column
 *  FB-03 - lastSerialID() reads the id the table's trigger kept in the connection's context, per table or the last
 *  FB-04 - Transactions: begin, commit, rollback reach the transport once for nested eZDB transactions; lock()
 *          opens one, unlock() commits it
 *  FB-05 - A deadlock outside a transaction is sent again; inside one it is not; a lost connection outside a
 *          transaction is opened again; errors carry their category and no password
 *  FB-06 - Temporary tables: the definition is made over a second connection once, the rows are deleted when the
 *          kernel drops the table; names stay the same for the same pattern
 *  FB-07 - Relations in lower case; createDatabase() refused with the reason; long literals as parameters
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expFirebirdTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('firebird')]
class expFirebirdDBTest extends PHPUnit\Framework\TestCase
{
    private $saved;

    protected function setUp(): void
    {
        $this->saved = expFirebirdTestKit::quietDebug();
    }

    protected function tearDown(): void
    {
        expFirebirdTestKit::restoreDebug( $this->saved );
    }

    public function testConnect()
    {
        $t = new expFirebirdFakeTransport();
        $db = expFirebirdTestKit::driver( $t, array( 'Role' => 'EDITOR' ), array( 'server' => 'fb.example', 'port' => 3051, 'database' => '/data/site.fdb' ) );
        $this->assertTrue( $db->isConnected() );
        $this->assertSame( 'firebird', $db->databaseName() );
        $this->assertSame( 'firebird', $db->sqlHelperDialect() );
        $this->assertSame( array( 'fb.example', 3051, '/data/site.fdb', 'SYSDBA', 'EDITOR', 'UTF8' ),
                           array( $t->config['host'], $t->config['port'], $t->config['database'], $t->config['user'], $t->config['role'], $t->config['charset'] ) );
        $this->assertSame( '5.0.4.1812', $db->databaseServerVersion()['string'] );
        $this->assertSame( array( 5, 0, 4, 1812 ), $db->databaseServerVersion()['values'] );

        $refused = new expFirebirdFakeTransport();
        $refused->refuseOpen = true;
        try
        {
            expFirebirdTestKit::driver( $refused );
            $this->fail( 'no exception' );
        }
        catch ( eZDBNoConnectionException $e )
        {
            $this->assertStringNotContainsString( 'Secret-Pass-123', $e->getMessage() );
        }
    }

    public function testStatementsAndPaging()
    {
        $t = new expFirebirdFakeTransport();
        $t->on( '/FROM ezsite_data/', array( array( 'name' => 'a', 'value' => '1' ), array( 'name' => 'b', 'value' => '2' ) ) );
        $db = expFirebirdTestKit::driver( $t );
        $rows = $db->arrayQuery( 'SELECT name, value FROM ezsite_data ORDER BY name', array( 'limit' => 2, 'offset' => 10 ) );
        $this->assertSame( "SELECT name, \"VALUE\" FROM ezsite_data ORDER BY name\nOFFSET 10 ROWS FETCH NEXT 2 ROWS ONLY", $db->LastStatement );
        $this->assertSame( array( 10, 11 ), array_keys( $rows ) );
        $this->assertSame( array( '1', '2' ), array_values( $db->arrayQuery( 'SELECT value FROM ezsite_data', array( 'column' => 'value' ) ) ) );
        $this->assertSame( array( 'a', 'b' ), array_values( $db->arrayQuery( 'SELECT name FROM ezsite_data', array( 'column' => 0 ) ) ) );
        $this->assertSame( array(), $db->arrayQuery( 'SELECT name FROM ezsite_data', array( 'limit' => 0 ) ) );
        $this->assertTrue( $db->query( 'UPDATE ezview_counter SET count = count + 1' ) );
        $this->assertSame( 'UPDATE ezview_counter SET "COUNT" = "COUNT" + 1', $db->LastStatement );
        $this->assertSame( "a''b", $db->escapeString( "a'b" ) );
    }

    public function testLastSerialID()
    {
        $t = new expFirebirdFakeTransport();
        $t->on( "/RDB\\\$GET_CONTEXT\\('USER_SESSION', 'EXP_ID.EZCONTENTOBJECT'\\)/", array( array( 'id' => '57' ) ) );
        $t->on( "/RDB\\\$GET_CONTEXT\\('USER_SESSION', 'EXP_ID'\\)/", array( array( 'id' => '58' ) ) );
        $t->on( "/EXP_ID.EZUSER'/", array( array( 'id' => null ) ) );
        $db = expFirebirdTestKit::driver( $t );
        $this->assertSame( 57, $db->lastSerialID( 'ezcontentobject', 'id' ) );
        $this->assertSame( 58, $db->lastSerialID() );
        $this->assertFalse( $db->lastSerialID( 'ezuser', 'contentobject_id' ) );
    }

    public function testTransactions()
    {
        $t = new expFirebirdFakeTransport();
        $db = expFirebirdTestKit::driver( $t );
        $db->begin();
        $db->begin();
        $db->commit();
        $db->commit();
        $this->assertSame( array( 'begin', 'commit' ), $t->transactions );
        $db->begin();
        $db->rollback();
        $this->assertSame( array( 'begin', 'commit', 'begin', 'rollback' ), $t->transactions );
        $db->lock( 'ezcontentobject' );
        $db->unlock();
        $this->assertSame( array( 'begin', 'commit', 'begin', 'rollback', 'begin', 'commit' ), $t->transactions );
    }

    public function testRetriesAndReconnects()
    {
        $t = new expFirebirdFakeTransport();
        $db = expFirebirdTestKit::driver( $t );
        $t->failNext( -913, 335544336, 'deadlock update conflicts with concurrent update' );
        $this->assertTrue( $db->query( 'UPDATE t SET a = 1' ) );
        $this->assertSame( 1, $db->Retries );
        $this->assertCount( 2, $t->sent( '/^UPDATE t/' ) );

        $t->failNext( -902, 335544721, 'Unable to complete network request to host' );
        $this->assertTrue( $db->query( 'UPDATE t SET a = 2' ) );
        $this->assertSame( 1, $db->Reconnects );
        $this->assertSame( 2, $t->opens );

        // inside a transaction a deadlock is not sent again (a failed statement there ends the request: exceptions here)
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        $db->begin();
        $t->failNext( -913, null, 'deadlock' );
        try
        {
            $db->query( 'UPDATE t SET a = 3' );
            $this->fail( 'no exception' );
        }
        catch ( eZDBException $e )
        {
            $this->assertSame( 'deadlock', $db->ErrorCategory );
            $this->assertSame( -913, $db->errorNumber() );
        }
        $this->assertCount( 1, $t->sent( '/^UPDATE t SET a = 3/' ) );
        while ( $db->transactionCounter() > 0 )
            $db->rollback();
        $db->setErrorHandling( eZDB::ERROR_HANDLING_STANDARD );

        $t->failNext( -803, 335544665, 'violation of PRIMARY or UNIQUE KEY constraint, password Secret-Pass-123' );
        $this->assertFalse( $db->query( 'INSERT INTO t (id) VALUES (1)' ) );
        $this->assertSame( 'duplicate', $db->ErrorCategory );
        $this->assertStringNotContainsString( 'Secret-Pass-123', $db->errorMessage() );
    }

    public function testTemporaryTables()
    {
        $t = new expFirebirdFakeTransport();
        $t->on( '/FROM RDB\$RELATIONS/', array() );
        $db = expFirebirdTestKit::driver( $t );
        $this->assertSame( 'ezsearch_tmp_0', $db->generateUniqueTempTableName( 'ezsearch_tmp_%', 0 ) );
        $this->assertSame( 'ezsearch_tmp_0', $db->generateUniqueTempTableName( 'ezsearch_tmp_%' ) );
        $db->createTempTable( 'CREATE TEMPORARY TABLE ezsearch_tmp_0 ( contentobject_id int primary key not null, published int )' );
        $this->assertCount( 1, $t->siblings );
        $this->assertCount( 1, $t->siblings[0]->sent( '/^CREATE GLOBAL TEMPORARY TABLE ezsearch_tmp_0/' ) );
        $this->assertSame( array( 'DELETE FROM ezsearch_tmp_0' ), $t->sent( '/^DELETE/' ) );
        // the same name is taken while the table is in use
        $this->assertSame( 'ezsearch_tmp_1', $db->generateUniqueTempTableName( 'ezsearch_tmp_%', 0 ) );
        $db->dropTempTableList( array( 'ezsearch_tmp_0' ) );
        $this->assertSame( array( 'DELETE FROM ezsearch_tmp_0', 'DELETE FROM ezsearch_tmp_0' ), $t->sent( '/^DELETE/' ) );
        $this->assertSame( array(), $t->sent( '/^DROP/' ) );

        // a definition that exists is not made again
        $t->on( '/FROM RDB\$RELATIONS/', array( array( 'name' => 'eznode_count_0' ) ) );
        $db->KnownTables = null;
        $db->createTempTable( 'CREATE TEMPORARY TABLE eznode_count_0 ( count int )' );
        $this->assertCount( 1, $t->siblings );
    }

    public function testRelationsAndDatabases()
    {
        $t = new expFirebirdFakeTransport();
        $t->on( '/FROM RDB\$RELATIONS/', array( array( 'name' => 'ezuser' ), array( 'name' => 'ezsite_data' ) ) );
        $t->on( '/FROM RDB\$GENERATORS/', array( array( 'name' => 's_ezuser' ) ) );
        $db = expFirebirdTestKit::driver( $t );
        $this->assertSame( array( 'ezuser', 'ezsite_data' ), $db->relationList() );
        $this->assertSame( array( 'ezuser' => eZDBInterface::RELATION_TABLE, 'ezsite_data' => eZDBInterface::RELATION_TABLE,
                                  's_ezuser' => eZDBInterface::RELATION_SEQUENCE ), $db->eZTableList() );
        $this->assertTrue( $db->removeRelation( 's_ezuser', eZDBInterface::RELATION_SEQUENCE ) );
        $this->assertSame( array( 'DROP SEQUENCE "S_EZUSER"' ), $t->sent( '/^DROP/' ) );
        // DROP ... IF EXISTS asks the catalogue first
        $t->on( "/RDB\\\$RELATION_NAME = 'GONE'/", array() );
        $t->on( "/RDB\\\$RELATION_NAME = 'THERE'/", array( array( 'found' => '1' ) ) );
        $this->assertTrue( $db->query( 'DROP TABLE IF EXISTS gone' ) );
        $this->assertTrue( $db->query( 'DROP TABLE IF EXISTS there' ) );
        $this->assertTrue( $db->query( 'CREATE TABLE IF NOT EXISTS there ( id INTEGER )' ) );
        // the dropped table's sequence goes with it (the catalogue answers that it exists)
        $this->assertSame( array( 'DROP SEQUENCE "S_EZUSER"', 'DROP TABLE there', 'DROP SEQUENCE S_THERE' ), $t->sent( '/^DROP/' ) );
        $this->assertSame( array(), $t->sent( '/^CREATE/' ) );
        $this->assertFalse( $db->createDatabase( 'x' ) );
        $this->assertStringContainsString( 'CREATE DATABASE', $db->errorMessage() );

        $big = str_repeat( 'ä', 9000 );
        $this->assertTrue( $db->query( "UPDATE ezcontentobject_attribute SET data_text = '$big' WHERE id = 1" ) );
        $last = end( $t->log );
        $this->assertSame( array( 'UPDATE ezcontentobject_attribute SET data_text = ? WHERE id = 1', array( $big ) ), $last );
    }
}
