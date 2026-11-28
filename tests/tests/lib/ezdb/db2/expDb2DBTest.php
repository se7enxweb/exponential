<?php
/**
 * expDb2DB and expInformixDB over a recording transport (no server needed).
 *
 *  DB-01 - Connecting: the eZDB parameters reach the transport; the schema is set; a refused login throws
 *          eZDBNoConnectionException without the password; databaseName(), the server version, the helper dialect
 *  DB-02 - Statements go out rewritten; arrayQuery() pages on the server, keys rows from the offset, takes a column
 *  DB-03 - lastSerialID(): Db2 reads PREVIOUS VALUE FOR the table's sequence (the last table inserted into without
 *          one); Informix keeps DBINFO('sqlca.sqlerrd1') of each INSERT; correctSequenceValues() restarts sequences
 *  DB-04 - Transactions: begin, commit, rollback reach the transport once for nested eZDB transactions
 *  DB-05 - A deadlock outside a transaction is sent again, inside one it is not; a lost connection outside a
 *          transaction is opened again; errors carry their category and no password
 *  DB-06 - Temporary tables: Db2 makes the global temporary table over a second connection once and deletes its rows
 *          when the kernel drops it; Informix uses its own temporary tables; a database without a user temporary
 *          tablespace (SQL0286N) gets EXP_USERTEMP of USERSPACE1's page size and the table, or an error naming the fix
 *  DB-07 - Relations in lower case; a dropped table takes its sequence; createDatabase() refused with the reason;
 *          long literals as parameters; Informix's session setup (lock wait, isolation, line breaks)
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expDb2TestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('db2')]
class expDb2DBTest extends PHPUnit\Framework\TestCase
{
    private $saved;

    protected function setUp(): void
    {
        $this->saved = expDb2TestKit::quietDebug();
    }

    protected function tearDown(): void
    {
        expDb2TestKit::restoreDebug( $this->saved );
    }

    public function testConnect()
    {
        $t = new expDb2FakeTransport();
        $t->on( '/ENV_INST_INFO/', array( array( 'v' => 'DB2 v12.1.6.0' ) ) );
        $db = expDb2TestKit::driver( $t, array( 'Schema' => 'site' ), array( 'server' => 'db.example', 'port' => 50001, 'database' => 'EXP' ) );
        $this->assertTrue( $db->isConnected() );
        $this->assertSame( 'db2', $db->databaseName() );
        $this->assertSame( 'db2', $db->sqlHelperDialect() );
        $this->assertSame( array( 'db.example', 50001, 'EXP', 'db2inst1' ), array( $t->config['host'], $t->config['port'], $t->config['database'], $t->config['user'] ) );
        $this->assertSame( array( 'SET SCHEMA "SITE"' ), $t->sent( '/^SET SCHEMA/' ) );
        $this->assertTrue( $t->config['concentrator'] );
        $this->assertSame( '12.1.6.0', $db->databaseServerVersion()['string'] );
        $this->assertSame( array( 12, 1, 6, 0 ), $db->databaseServerVersion()['values'] );

        $refused = new expDb2FakeTransport();
        $refused->refuseOpen = true;
        try
        {
            expDb2TestKit::driver( $refused );
            $this->fail( 'no exception' );
        }
        catch ( eZDBNoConnectionException $e )
        {
            $this->assertStringNotContainsString( 'Secret-Pass-123', $e->getMessage() );
        }
    }

    public function testStatementsAndPaging()
    {
        $t = new expDb2FakeTransport();
        $t->on( '/FROM ezcontentobject/', array( array( 'id' => '5', 'name' => 'a' ), array( 'id' => '6', 'name' => 'b' ) ) );
        $db = expDb2TestKit::driver( $t );
        $this->assertTrue( $db->query( 'UPDATE ezview_counter SET count = count + 1 WHERE `node_id` = 2' ) );
        $this->assertSame( array( 'UPDATE ezview_counter SET count = count + 1 WHERE "NODE_ID" = 2' ), $t->sent( '/^UPDATE/' ) );
        $rows = $db->arrayQuery( 'SELECT id, name FROM ezcontentobject ORDER BY id', array( 'limit' => 2, 'offset' => 10 ) );
        $this->assertSame( array( 10, 11 ), array_keys( $rows ) );
        $this->assertSame( array( "SELECT id, name FROM ezcontentobject ORDER BY id\nOFFSET 10 ROWS FETCH FIRST 2 ROWS ONLY" ), $t->sent( '/FROM ezcontentobject/' ) );
        $this->assertSame( array( 0 => '5', 1 => '6' ), $db->arrayQuery( 'SELECT id, name FROM ezcontentobject', array( 'column' => 'id' ) ) );
        $this->assertSame( array(), $db->arrayQuery( 'SELECT id FROM ezcontentobject', array( 'limit' => 0 ) ) );
    }

    public function testLastSerialID()
    {
        $t = new expDb2FakeTransport();
        $t->on( '/PREVIOUS VALUE FOR S_EZCONTENTOBJECT$/', array( array( '1' => '57' ) ) );
        $db = expDb2TestKit::driver( $t );
        $this->assertFalse( $db->lastSerialID() );
        $this->assertTrue( $db->query( "INSERT INTO ezcontentobject (name) VALUES ('x')" ) );
        $this->assertSame( 57, $db->lastSerialID( 'ezcontentobject', 'id' ) );
        $this->assertSame( 57, $db->lastSerialID() );

        $t->on( '/FROM SYSCAT.SEQUENCES s/', array( array( 'seq' => 's_ezuser', 'col' => 'ezuser.contentobject_id' ) ) );
        $t->on( '/SELECT MAX\(contentobject_id\)/', array( array( 'm' => '41' ) ) );
        $this->assertTrue( $db->correctSequenceValues() );
        $this->assertSame( array( 'ALTER SEQUENCE s_ezuser RESTART WITH 42' ), $t->sent( '/^ALTER SEQUENCE/' ) );

        $i = new expDb2FakeTransport();
        $i->on( "/DBINFO\('sqlca.sqlerrd1'\)/", array( array( 'id' => '12' ) ) );
        $ifx = expDb2TestKit::driver( $i, array(), array(), 'expInformixDB' );
        $this->assertSame( 'informix', $ifx->databaseName() );
        $this->assertTrue( $ifx->query( "INSERT INTO ezsection (name) VALUES ('x')" ) );
        $this->assertSame( 12, $ifx->lastSerialID( 'ezsection' ) );
        $this->assertSame( 12, $ifx->lastSerialID() );
        $this->assertFalse( $ifx->lastSerialID( 'ezuser' ) );
        $this->assertSame( array( 'SET LOCK MODE TO WAIT 30', 'SET ISOLATION TO COMMITTED READ LAST COMMITTED', "EXECUTE PROCEDURE IFX_ALLOW_NEWLINE('t')" ),
                           $i->sent( '/^(SET|EXECUTE)/' ) );
        $this->assertTrue( $ifx->correctSequenceValues() );
    }

    public function testTransactionsAndRetries()
    {
        $t = new expDb2FakeTransport();
        $db = expDb2TestKit::driver( $t );
        $db->begin();
        $db->begin();
        $db->commit();
        $db->commit();
        $this->assertSame( array( 'begin', 'commit' ), $t->transactions );

        // a deadlock outside a transaction: sent again
        $t->failNext( -911, '40001', 'SQL0911N The current transaction has been rolled back because of a deadlock' );
        $this->assertTrue( $db->query( 'UPDATE t SET a = 1' ) );
        $this->assertSame( 1, $db->Retries );
        $this->assertCount( 2, $t->sent( '/^UPDATE t/' ) );

        // a lost connection outside a transaction: opened again
        $opens = $t->opens;
        $t->failNext( -30081, '08001', 'SQL30081N A communication error has been detected' );
        $this->assertTrue( $db->query( 'UPDATE t SET a = 2' ) );
        $this->assertSame( $opens + 1, $t->opens );

        // a duplicate key: no retry, the category kept, no password in the message
        $db->Password = 'Secret-Pass-123';
        $t->failNext( -803, '23505', 'SQL0803N duplicate Secret-Pass-123' );
        $this->assertFalse( $db->query( 'INSERT INTO t (a) VALUES (1)' ) );
        $this->assertSame( 'duplicate', $db->ErrorCategory );
        $this->assertSame( -803, $db->errorNumber() );
        $this->assertStringNotContainsString( 'Secret-Pass-123', $db->errorMessage() );
    }

    public function testTemporaryTables()
    {
        $t = new expDb2FakeTransport();
        $db = expDb2TestKit::driver( $t );
        $name = $db->generateUniqueTempTableName( 'ezsearch_tmp_%' );
        $this->assertSame( 'ezsearch_tmp_0', $name );
        $this->assertTrue( $db->createTempTable( "CREATE TEMPORARY TABLE $name ( contentobject_id int not null )" ) );
        $this->assertCount( 1, $t->siblings );
        $this->assertSame( array( 'CREATE GLOBAL TEMPORARY TABLE ezsearch_tmp_0 ( contentobject_id INTEGER NOT NULL ) ON COMMIT PRESERVE ROWS NOT LOGGED' ),
                           $t->siblings[0]->sent( '/^CREATE/' ) );
        $this->assertSame( 'ezsearch_tmp_1', $db->generateUniqueTempTableName( 'ezsearch_tmp_%' ) );
        $this->assertTrue( $db->dropTempTable( "DROP TABLE $name" ) );
        $this->assertSame( array( 'DELETE FROM ezsearch_tmp_0', 'DELETE FROM ezsearch_tmp_0' ), $t->sent( '/^DELETE/' ) );
        $this->assertSame( array(), $t->sent( '/^DROP/' ) );

        // a definition of other columns is dropped and made again over the second connection
        $s = new expDb2FakeTransport();
        $s->on( "/TYPE IN \('T', 'G'\)/", array( array( 'name' => 'ezsearch_tmp_0' ) ) );
        $s->on( '/FROM SYSCAT.COLUMNS/', array( array( 'name' => 'id' ) ) );
        $stale = expDb2TestKit::driver( $s );
        $this->assertTrue( $stale->createTempTable( 'CREATE TEMPORARY TABLE ezsearch_tmp_0 ( contentobject_id int primary key not null, published int )' ) );
        $this->assertSame( array( 'DROP TABLE ezsearch_tmp_0', 'CREATE GLOBAL TEMPORARY TABLE ezsearch_tmp_0 ( contentobject_id INTEGER NOT NULL, published INTEGER ) ON COMMIT PRESERVE ROWS NOT LOGGED' ),
                           $s->siblings[0]->sent( '/^(DROP|CREATE)/' ) );

        $i = new expDb2FakeTransport();
        $ifx = expDb2TestKit::driver( $i, array(), array(), 'expInformixDB' );
        $this->assertTrue( $ifx->createTempTable( 'CREATE TEMPORARY TABLE tmp_x ( id int )' ) );
        $this->assertTrue( $ifx->dropTempTable( 'DROP TABLE tmp_x' ) );
        $this->assertSame( array( 'CREATE TEMP TABLE tmp_x ( id INTEGER ) WITH NO LOG', 'DROP TABLE tmp_x' ), $i->sent( '/^(CREATE|DROP)/' ) );
    }

    /**
     * DB-06: a database made without a user temporary tablespace (SQL0286N on the global temporary table) gets one of
     * USERSPACE1's page size in a buffer pool of that size, and the table is made after it; without the authority
     * the table is not made and the error names the statement to run.
     */
    public function testTemporaryTableMakesTheUserTemporaryTablespace()
    {
        $space = false;
        $t = new expDb2FakeTransport();
        $t->on( '/^CREATE GLOBAL TEMPORARY TABLE/', function () use ( &$space ) {
            return $space ? array() : array( 'error' => array( -286, '42727', 'SQL0286N A table space could not be found with a page size of at least "4096"' ) );
        } );
        $t->on( '/^CREATE USER TEMPORARY TABLESPACE/', function () use ( &$space ) { $space = true; return array(); } );
        $t->on( "/FROM SYSCAT.TABLESPACES WHERE TBSPACE = 'USERSPACE1'/", array( array( 'page_size' => '32768' ) ) );
        $t->on( '/FROM SYSCAT.BUFFERPOOLS WHERE PAGESIZE = 32768/', array( array( 'name' => 'IBMDEFAULTBP ' ) ) );
        $db = expDb2TestKit::driver( $t );
        $this->assertTrue( $db->createTempTable( 'CREATE TEMPORARY TABLE ezsearch_tmp_0 ( contentobject_id int primary key not null, published int )' ) );
        $this->assertSame( array( 'CREATE GLOBAL TEMPORARY TABLE ezsearch_tmp_0 ( contentobject_id INTEGER NOT NULL, published INTEGER ) ON COMMIT PRESERVE ROWS NOT LOGGED',
                                  'CREATE USER TEMPORARY TABLESPACE EXP_USERTEMP PAGESIZE 32768 BUFFERPOOL "IBMDEFAULTBP"',
                                  'CREATE GLOBAL TEMPORARY TABLE ezsearch_tmp_0 ( contentobject_id INTEGER NOT NULL, published INTEGER ) ON COMMIT PRESERVE ROWS NOT LOGGED' ),
                           $t->siblings[0]->sent( '/^CREATE/' ) );
        $this->assertSame( array( 'DELETE FROM ezsearch_tmp_0' ), $t->sent( '/^DELETE/' ) );

        // no authority to make a tablespace: no table, and the error says what to run
        $r = new expDb2FakeTransport();
        $r->on( '/^CREATE GLOBAL TEMPORARY TABLE/', array( 'error' => array( -286, '42727', 'SQL0286N A table space could not be found' ) ) );
        $r->on( '/^CREATE USER TEMPORARY TABLESPACE/', array( 'error' => array( -552, '42502', 'SQL0552N "EXP" does not have the privilege' ) ) );
        $refused = expDb2TestKit::driver( $r );
        $this->assertFalse( $refused->createTempTable( 'CREATE TEMPORARY TABLE ezsearch_tmp_0 ( contentobject_id int not null )' ) );
        $this->assertStringContainsString( 'CREATE USER TEMPORARY TABLESPACE USERTEMP PAGESIZE 32 K', $refused->errorMessage() );
        $this->assertSame( array(), $r->sent( '/^DELETE FROM ezsearch_tmp_0/' ) );
        $this->assertFalse( expDb2DB::lacksUserTemporaryTablespace( array( 'sqlcode' => -204, 'state' => '42704', 'message' => 'SQL0204N' ) ) );
        $this->assertFalse( expDb2DB::lacksUserTemporaryTablespace( null ) );
    }

    public function testRelationsAndDatabases()
    {
        $t = new expDb2FakeTransport();
        $t->on( "/FROM SYSCAT.TABLES WHERE TABSCHEMA = CURRENT SCHEMA AND TYPE IN \('T'\)/", array( array( 'name' => 'ezuser' ), array( 'name' => 'ezsection' ) ) );
        $t->on( '/FROM SYSCAT.SEQUENCES WHERE SEQSCHEMA = CURRENT SCHEMA AND SEQNAME/', array( array( 'found' => '1' ) ) );
        $db = expDb2TestKit::driver( $t );
        $this->assertSame( array( 'ezuser', 'ezsection' ), $db->relationList( eZDBInterface::RELATION_TABLE ) );
        $this->assertSame( 2, $db->relationCount( eZDBInterface::RELATION_TABLE ) );
        $this->assertTrue( $db->query( 'DROP TABLE ezuser' ) );
        $this->assertSame( array( 'DROP SEQUENCE S_EZUSER' ), $t->sent( '/^DROP SEQUENCE/' ) );
        $this->assertFalse( $db->createDatabase( 'x' ) );
        $this->assertStringContainsString( 'CREATE DATABASE', $db->errorMessage() );

        $long = str_repeat( 'é', 9000 );
        $this->assertTrue( $db->query( "INSERT INTO ezcontentobject_attribute (data_text) VALUES ('$long')" ) );
        $last = end( $t->log );
        $this->assertSame( array( 'INSERT INTO ezcontentobject_attribute (data_text) VALUES (?)', array( $long ) ), $last );
    }
}
