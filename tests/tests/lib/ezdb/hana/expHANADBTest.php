<?php
/**
 * expHANADB over a recording transport (no server needed).
 *
 *  HA-01 - Connecting: the eZDB parameters reach the transport; a refused login throws eZDBNoConnectionException
 *          without the password; a missing schema is made (CreateSchema); databaseName(), version, helper dialect
 *  HA-02 - Statements go out rewritten; arrayQuery() pages on the server, keys rows from the offset, takes a column;
 *          an INSERT of several rows goes out row by row
 *  HA-03 - lastSerialID() reads the CURRVAL of the table's sequence, of the table given or of the last INSERT
 *  HA-04 - Transactions: begin, commit, rollback reach the transport once for nested eZDB transactions; lock() opens
 *          one and locks each table, unlock() commits it
 *  HA-05 - A deadlock outside a transaction is sent again; inside one it is not; a lost connection outside a
 *          transaction is opened again; errors carry their category and no password
 *  HA-06 - Temporary tables: the definition is made over a second connection once, the rows are deleted when the
 *          kernel drops the table; names stay the same for the same pattern
 *  HA-07 - NCLOB columns read once and compared as TO_NVARCHAR; read again after a change of the schema
 *  HA-09 - Literal values bound as parameters, not those the odbc extension would read as a file name
 *  HA-08 - Relations in lower case; a dropped table takes its sequence along; IF EXISTS asked of the catalogue
 *  HA-10 - isAvailable(): an ODBC layer of PHP and SAP's ODBC driver registered in an odbcinst.ini (or a library path
 *          that exists); what the setup wizard offers SAP HANA by
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expHANATestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('hana')]
class expHANADBTest extends PHPUnit\Framework\TestCase
{
    private $saved;

    protected function setUp(): void
    {
        $this->saved = expHANATestKit::quietDebug();
    }

    protected function tearDown(): void
    {
        expHANATestKit::restoreDebug( $this->saved );
    }

    /**
     * A transport that knows no NCLOB column, so the tests of other rules see their statements unchanged.
     */
    private function transport()
    {
        $t = new expHANAFakeTransport();
        $t->on( '/FROM SYS\.TABLE_COLUMNS WHERE SCHEMA_NAME = CURRENT_SCHEMA GROUP BY/', array() );
        return $t;
    }

    public function testConnect()
    {
        $t = $this->transport();
        $db = expHANATestKit::driver( $t, array( 'TenantDatabase' => 'HXE', 'Driver' => '/opt/sap/libodbcHDB.so' ),
                                      array( 'server' => 'hana.example', 'port' => 30015, 'database' => 'site' ) );
        $this->assertTrue( $db->isConnected() );
        $this->assertSame( 'hana', $db->databaseName() );
        $this->assertSame( 'hana', $db->sqlHelperDialect() );
        $this->assertSame( array( 'hana.example', 30015, 'site', 'EXPTEST', 'HXE', '/opt/sap/libodbcHDB.so' ),
                           array( $t->config['host'], $t->config['port'], $t->config['schema'], $t->config['user'], $t->config['tenant'], $t->config['driver'] ) );
        $this->assertSame( '2.00.088.00.1760424921', $db->databaseServerVersion()['string'] );
        $this->assertSame( array( 2, 0, 88 ), $db->databaseServerVersion()['values'] );
        $this->assertFalse( $db->supportsDefaultValuesInsertion() );

        $refused = new expHANAFakeTransport();
        $refused->refuseOpen = true;
        try
        {
            expHANATestKit::driver( $refused );
            $this->fail( 'no exception' );
        }
        catch ( eZDBNoConnectionException $e )
        {
            $this->assertStringNotContainsString( 'Secret-Pass-123', $e->getMessage() );
        }
    }

    public function testMissingSchemaIsMade()
    {
        $t = new class extends expHANAFakeTransport {
            public function open( array $config )
            {
                if ( $config['schema'] !== '' && !$this->sent( '/^CREATE SCHEMA/' ) )
                {
                    $this->configs[] = $config;
                    $this->setError( 362, 'HY000', 'invalid schema name: SITE' );
                    return false;
                }
                return parent::open( $config );
            }
        };
        $db = expHANATestKit::driver( $t, array(), array( 'database' => 'site' ) );
        $this->assertTrue( $db->isConnected() );
        $this->assertSame( array( 'CREATE SCHEMA "SITE"' ), $t->sent( '/^CREATE SCHEMA/' ) );
        $this->assertSame( array( 'site', '', 'site' ), array_column( $t->configs, 'schema' ) );

        $off = new class extends expHANAFakeTransport {
            public function open( array $config )
            {
                $this->setError( 362, 'HY000', 'invalid schema name: SITE' );
                return false;
            }
        };
        $this->expectException( eZDBNoConnectionException::class );
        expHANATestKit::driver( $off, array( 'CreateSchema' => 'disabled' ) );
    }

    public function testStatementsAndPaging()
    {
        $t = $this->transport();
        $t->on( '/FROM ezsite_data/', array( array( 'name' => 'a', 'value' => '1' ), array( 'name' => 'b', 'value' => '2' ) ) );
        $db = expHANATestKit::driver( $t );
        $rows = $db->arrayQuery( 'SELECT name, value FROM ezsite_data ORDER BY name', array( 'limit' => 2, 'offset' => 10 ) );
        $this->assertSame( "SELECT name, value FROM ezsite_data ORDER BY name\nLIMIT 2 OFFSET 10", $db->LastStatement );
        $this->assertSame( array( 10, 11 ), array_keys( $rows ) );
        $this->assertSame( array( '1', '2' ), array_values( $db->arrayQuery( 'SELECT value FROM ezsite_data', array( 'column' => 'value' ) ) ) );
        $this->assertSame( array( 'a', 'b' ), array_values( $db->arrayQuery( 'SELECT name FROM ezsite_data', array( 'column' => 0 ) ) ) );
        $this->assertSame( array(), $db->arrayQuery( 'SELECT name FROM ezsite_data', array( 'limit' => 0 ) ) );
        $this->assertTrue( $db->query( 'UPDATE ezcontentobject SET flags = flags | 4 WHERE id = 1' ) );
        $this->assertSame( 'UPDATE ezcontentobject SET flags = BITOR(flags, 4) WHERE id = 1', $db->LastStatement );
        $this->assertTrue( $db->query( "INSERT INTO ezx (a, b) VALUES (1, 'x'), (2, 'y');" ) );
        $this->assertSame( array( "INSERT INTO ezx (a, b) VALUES (1, 'x')", "INSERT INTO ezx (a, b) VALUES (2, 'y')" ), $t->sent( '/^INSERT INTO ezx/' ) );
        $this->assertSame( "a''b", $db->escapeString( "a'b" ) );
        $this->assertSame( 'id IN ( 1, 2 )', $db->generateSQLINStatement( array( 1, 2 ), 'id', false, true, 'int' ) );
    }

    public function testLiteralsBound()
    {
        $t = $this->transport();
        $db = expHANATestKit::driver( $t, array( 'BindLiterals' => 'enabled' ) );
        $this->assertTrue( $db->query( "UPDATE ezx SET name = 'a' WHERE id = 5" ) );
        $this->assertSame( array( 'UPDATE ezx SET name = ? WHERE id = ?', array( 'a', '5' ) ), end( $t->log ) );
        $db->arrayQuery( "SELECT id FROM ezx WHERE name = 'b'", array( 'limit' => 3 ) );
        $this->assertSame( array( "SELECT id FROM ezx WHERE name = ?\nLIMIT 3", array( 'b' ) ), end( $t->log ) );
        // the odbc extension reads a value in single quotes as a file name: such a statement keeps its literals
        $odbc = new class extends expHANAFakeTransport { public function name() { return 'odbc'; } };
        $odbc->on( '/FROM SYS\.TABLE_COLUMNS WHERE SCHEMA_NAME = CURRENT_SCHEMA GROUP BY/', array() );
        $db = expHANATestKit::driver( $odbc, array( 'BindLiterals' => 'enabled' ) );
        $this->assertTrue( $db->query( "UPDATE ezx SET name = '''quoted''' WHERE id = 5" ) );
        $this->assertSame( array( "UPDATE ezx SET name = '''quoted''' WHERE id = 5", array() ), end( $odbc->log ) );
    }

    public function testLastSerialID()
    {
        $t = $this->transport();
        $t->on( '/S_EZX\.CURRVAL/', array( array( 'id' => '42' ) ) );
        $t->on( '/S_EZY\.CURRVAL/', array( 'error' => array( 326, 'CURRVAL of given sequence is not yet defined in this session' ) ) );
        $db = expHANATestKit::driver( $t );
        $this->assertFalse( $db->lastSerialID() );
        $this->assertTrue( $db->query( "INSERT INTO ezx (name) VALUES ('a')" ) );
        $this->assertSame( 42, $db->lastSerialID() );
        $this->assertSame( 42, $db->lastSerialID( 'ezx', 'id' ) );
        $this->assertFalse( $db->lastSerialID( 'ezy', 'id' ) );
        $this->assertSame( array( 'SELECT S_EZX.CURRVAL AS id FROM DUMMY' ), array_slice( $t->sent( '/CURRVAL/' ), 0, 1 ) );
    }

    public function testTransactionsAndLocks()
    {
        $t = $this->transport();
        $db = expHANATestKit::driver( $t );
        $db->begin();
        $db->begin();
        $db->commit();
        $db->commit();
        $this->assertSame( array( 'begin', 'commit' ), $t->transactions );
        $db->begin();
        $db->rollback();
        $this->assertSame( array( 'begin', 'commit', 'begin', 'rollback' ), $t->transactions );
        $db->lock( array( 'ezcontentobject WRITE', array( 'table' => 'ezsearch_word' ) ) );
        $this->assertSame( array( 'LOCK TABLE ezcontentobject IN EXCLUSIVE MODE', 'LOCK TABLE ezsearch_word IN EXCLUSIVE MODE' ), $t->sent( '/^LOCK TABLE/' ) );
        $db->unlock();
        $this->assertSame( 'commit', end( $t->transactions ) );
    }

    public function testRetriesAndErrors()
    {
        $t = $this->transport();
        $db = expHANATestKit::driver( $t );
        $t->failNext( 133, 'transaction rolled back by detected deadlock' );
        $this->assertTrue( $db->query( 'UPDATE ezx SET a = 1' ) );
        $this->assertSame( 1, $db->Retries );

        // inside a transaction a deadlock is not sent again (a failed statement there ends the request: exceptions here)
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        $db->begin();
        $t->failNext( 133, 'transaction rolled back by detected deadlock' );
        try
        {
            $db->query( 'UPDATE ezx SET a = 2' );
            $this->fail( 'no exception' );
        }
        catch ( eZDBException $e )
        {
            $this->assertSame( 'deadlock', $db->ErrorCategory );
            $this->assertSame( 133, $db->errorNumber() );
        }
        $this->assertCount( 1, $t->sent( '/^UPDATE ezx SET a = 2/' ) );
        while ( $db->transactionCounter() > 0 )
            $db->rollback();
        $db->setErrorHandling( eZDB::ERROR_HANDLING_STANDARD );

        $t->failNext( -10807, 'Connection down', '08S01' );
        $opens = $t->opens;
        $this->assertTrue( $db->query( 'UPDATE ezx SET a = 3' ) );
        $this->assertSame( $opens + 1, $t->opens );
        $this->assertSame( 1, $db->Reconnects );

        $t->failNext( 301, 'unique constraint violated for Secret-Pass-123' );
        $this->assertFalse( $db->query( "INSERT INTO ezx (id) VALUES (1)" ) );
        $this->assertSame( 'duplicate', $db->ErrorCategory );
        $this->assertSame( 301, $db->ErrorNumber );
        $this->assertStringNotContainsString( 'Secret-Pass-123', $db->ErrorMessage );
    }

    public function testTemporaryTables()
    {
        $t = $this->transport();
        $t->on( '/FROM SYS\.TABLES WHERE SCHEMA_NAME = CURRENT_SCHEMA$/', array( array( 'name' => 'ezcontentobject' ) ) );
        $db = expHANATestKit::driver( $t );
        $name = $db->generateUniqueTempTableName( 'ezsearch_tmp_%' );
        $this->assertSame( 'ezsearch_tmp_0', $name );
        $this->assertTrue( $db->createTempTable( "CREATE TEMPORARY TABLE $name ( contentobject_id int primary key not null )" ) );
        $this->assertSame( array( 'CREATE GLOBAL TEMPORARY TABLE ezsearch_tmp_0 ( contentobject_id INTEGER NOT NULL PRIMARY KEY )' ),
                           $t->siblings[0]->sent( '/^CREATE/' ) );
        $this->assertSame( array( 'DELETE FROM ezsearch_tmp_0' ), $t->sent( '/^DELETE/' ) );
        $this->assertSame( 'ezsearch_tmp_1', $db->generateUniqueTempTableName( 'ezsearch_tmp_%' ) );
        $db->dropTempTableList( array( $name ) );
        $this->assertSame( array( 'DELETE FROM ezsearch_tmp_0', 'DELETE FROM ezsearch_tmp_0' ), $t->sent( '/^DELETE/' ) );
        $this->assertSame( array(), $t->sent( '/^DROP/' ) );
        $this->assertSame( 'ezsearch_tmp_0', $db->generateUniqueTempTableName( 'ezsearch_tmp_%' ) );
    }

    public function testLobColumns()
    {
        $t = new expHANAFakeTransport();
        $t->on( '/GROUP BY LOWER\(COLUMN_NAME\)/', array( array( 'name' => 'param', 'lob' => '1' ), array( 'name' => 'action', 'lob' => '0' ) ) );
        $db = expHANATestKit::driver( $t, array(), array( 'database' => 'LOBTEST_' . uniqid() ) );
        $db->arrayQuery( "SELECT id FROM ezpending_actions WHERE param = '5' AND action = 'x'" );
        $this->assertSame( "SELECT id FROM ezpending_actions WHERE TO_NVARCHAR(param) = '5' AND action = 'x'", $db->LastStatement );
        $db->query( "DELETE FROM ezpending_actions WHERE param = '6'" );
        $this->assertCount( 1, $t->sent( '/GROUP BY LOWER\(COLUMN_NAME\)/' ) );
        // a plain INSERT and the schema's statements do not ask for them
        $db->query( "INSERT INTO ezpending_actions (action, param) VALUES ('a', '1')" );
        $db->query( 'CREATE COLUMN TABLE ezy (a INTEGER)' );
        $this->assertCount( 1, $t->sent( '/GROUP BY LOWER\(COLUMN_NAME\)/' ) );
        $db->query( "DELETE FROM ezpending_actions WHERE param = '7'" );
        $this->assertCount( 2, $t->sent( '/GROUP BY LOWER\(COLUMN_NAME\)/' ) );
    }

    public function testDistinctOverNclob()
    {
        $t = new expHANAFakeTransport();
        $t->on( '/GROUP BY LOWER\(COLUMN_NAME\)/', array( array( 'name' => 'data_text', 'lob' => '1' ) ) );
        $t->on( '/FROM ezcontentclass/', array( array( 'id' => '1', 'data_text' => 'x' ), array( 'id' => '1', 'data_text' => 'x' ), array( 'id' => '2', 'data_text' => 'y' ) ) );
        $db = expHANATestKit::driver( $t, array(), array( 'database' => 'DISTINCT_' . uniqid() ) );
        $rows = $db->arrayQuery( 'SELECT DISTINCT cc.* FROM ezcontentclass cc', array( 'offset' => 1 ) );
        $this->assertSame( 'SELECT cc.* FROM ezcontentclass cc', $db->LastStatement );
        $this->assertSame( array( 1 => array( 'id' => '2', 'data_text' => 'y' ) ), $rows );
    }

    public function testRelationsAndConditionalDDL()
    {
        $t = $this->transport();
        $t->on( '/SELECT LOWER\(TABLE_NAME\) AS name FROM SYS\.TABLES/', array( array( 'name' => 'ezx' ), array( 'name' => 'ezy' ) ) );
        $t->on( '/SEQUENCE_NAME = \'S_EZX\'/', array( array( 'found' => '1' ) ) );
        $t->on( '/TABLE_NAME = \'EZGONE\'/', array() );
        $db = expHANATestKit::driver( $t );
        $this->assertSame( array( 'ezx', 'ezy' ), $db->relationList() );
        $this->assertSame( 2, $db->relationCount() );
        $this->assertTrue( $db->query( 'DROP TABLE ezx' ) );
        $this->assertSame( array( 'DROP SEQUENCE S_EZX' ), $t->sent( '/^DROP SEQUENCE/' ) );
        $this->assertTrue( $db->query( 'DROP TABLE IF EXISTS ezgone' ) );
        $this->assertSame( array(), $t->sent( '/^DROP TABLE ezgone/' ) );
        $this->assertTrue( $db->removeRelation( 'ezy', eZDBInterface::RELATION_TABLE ) );
        $this->assertSame( array( 'DROP TABLE "EZY"' ), $t->sent( '/^DROP TABLE "EZY"/' ) );
    }

    /** HA-10 */
    public function testAvailableWhenTheODBCDriverIsRegistered()
    {
        $dir = sys_get_temp_dir() . '/exp-hana-odbcinst-' . getmypid() . '-' . bin2hex( random_bytes( 3 ) );
        mkdir( $dir, 0700, true );
        $with = $dir . '/with.ini';
        $without = $dir . '/without.ini';
        file_put_contents( $with, "# drivers\r\n[PostgreSQL]\r\nDriver=/usr/lib64/psqlodbcw.so\r\n\r\n [ HDBODBC ] \r\nDriver=/usr/sap/hdbclient/libodbcHDB.so\r\n" );
        file_put_contents( $without, "[PostgreSQL]\nDriver=/usr/lib64/psqlodbcw.so\n; [HDBODBC]\nDescription=HDBODBC\n" );
        try
        {
            $this->assertTrue( expHANAODBCTransport::driverRegistered( 'HDBODBC', array( $with ) ) );
            $this->assertTrue( expHANAODBCTransport::driverRegistered( '{hdbodbc}', array( $with ) ), 'braces and case do not matter' );
            $this->assertTrue( expHANAODBCTransport::driverRegistered( '', array( $with ) ), 'the default driver' );
            $this->assertFalse( expHANAODBCTransport::driverRegistered( 'HDBODBC', array( $without, $dir . '/missing.ini' ) ) );
            $this->assertTrue( expHANAODBCTransport::driverRegistered( 'HDBODBC', array( $without, $with ) ) );
            // a library path: whether the file exists
            $this->assertTrue( expHANAODBCTransport::driverRegistered( $with, array() ) );
            $this->assertFalse( expHANAODBCTransport::driverRegistered( $dir . '/libodbcHDB.so', array( $with ) ) );

            $settings = array( 'Driver' => 'HDBODBC', 'Layer' => 'auto' );
            $this->assertTrue( expHANADB::isAvailable( $settings, array( 'odbc' ), array( $with ) ) );
            $this->assertTrue( expHANADB::isAvailable( $settings, array( 'pdo_odbc' ), array( $with ) ) );
            $this->assertFalse( expHANADB::isAvailable( $settings, array(), array( $with ) ), 'no ODBC layer in PHP' );
            $this->assertFalse( expHANADB::isAvailable( array( 'Layer' => 'odbc' ) + $settings, array( 'pdo_odbc' ), array( $with ) ), 'not the layer asked for' );
            $this->assertFalse( expHANADB::isAvailable( $settings, array( 'odbc' ), array( $without ) ), 'no HANA ODBC driver' );

            // ODBCSYSINI (as the CI job sets it) is read first
            $saved = getenv( 'ODBCSYSINI' );
            putenv( 'ODBCSYSINI=' . $dir );
            try
            {
                $this->assertSame( $dir . '/odbcinst.ini', expHANAODBCTransport::odbcInstFiles()[0] );
            }
            finally
            {
                putenv( $saved === false ? 'ODBCSYSINI' : 'ODBCSYSINI=' . $saved );
            }
            $this->assertContains( '/etc/odbcinst.ini', expHANAODBCTransport::odbcInstFiles() );
        }
        finally
        {
            @unlink( $with );
            @unlink( $without );
            @rmdir( $dir );
        }
    }
}
