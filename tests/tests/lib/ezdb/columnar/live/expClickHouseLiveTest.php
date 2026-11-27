<?php
/**
 * The ClickHouse driver against a ClickHouse server (EXP_TEST_CLICKHOUSE_HOST ...; skipped without it). Every test
 * works in a database of its own, dropped afterwards.
 *
 *  CL-01 - HTTP: login, values of every type read back (text with tabs, line breaks, backslashes, quotes and Unicode,
 *          an empty text, NULL, Bool, Decimal, Date, DateTime, DateTime64, UInt64), 5,000 rows, errors and their numbers
 *  CL-02 - The schema handler: tables of share/db_schema.dba made as MergeTree tables, read back with their columns,
 *          types and keys; the whole kernel schema and its data inserted
 *  CL-03 - What ClickHouse does not do, measured: a second row with the same key is kept; a rollback undoes nothing
 *  CL-04 - Changing rows: UPDATE as a lightweight update, as a mutation on a table without block numbers; DELETE,
 *          DELETE without WHERE (TRUNCATE)
 *  CL-05 - New ids: nextSerialID() (the maximum plus one) through eZPersistentObject's path, lastSerialID()
 *  CL-06 - The portable helpers: LIMIT/OFFSET, groupConcat with an order, UNIX timestamps, upsert refused, FOR UPDATE
 *          left out
 *  CL-07 - A data table connection (engine clickhouse): test(), open(), a table made, rows written, planned again:
 *          nothing; the describer
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__ ) . '/expColumnarTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('columnar-live')]
class expClickHouseLiveTest extends PHPUnit\Framework\TestCase
{
    private $saved;
    private $server;
    private $database;
    private $open = array();

    protected function setUp(): void
    {
        $this->server = expColumnarTestKit::live( 'CLICKHOUSE' );
        if ( $this->server === null )
            $this->markTestSkipped( 'No ClickHouse to test against: set EXP_TEST_CLICKHOUSE_HOST (and _PORT, _USER, _PASSWORD, _DATABASE)' );
        $this->saved = expColumnarTestKit::quietDebug();
        $this->database = expColumnarTestKit::uniqueName( 'exp_test' );
        $admin = $this->connect( $this->server['database'] );
        $this->assertTrue( $admin->query( 'CREATE DATABASE ' . $this->database ), (string)$admin->errorMessage() );
    }

    protected function tearDown(): void
    {
        if ( $this->server === null )
            return;
        $admin = $this->connect( $this->server['database'] );
        $admin->query( 'DROP DATABASE IF EXISTS ' . $this->database );
        $this->open = array();
        expColumnarTestKit::restoreDebug( $this->saved );
    }

    private function connect( $database )
    {
        $db = new expClickHouseDB( array( 'database' => $database ) + $this->server );
        $this->open[] = $db;
        return $db;
    }

    private function db()
    {
        return $this->connect( $this->database );
    }

    private static function root()
    {
        return dirname( __DIR__, 6 );
    }

    /** CL-01 */
    public function testHttp()
    {
        $db = $this->db();
        $this->assertTrue( $db->isConnected() );
        $this->assertMatchesRegularExpression( '/^\d+\.\d+/', $db->databaseServerVersion()['string'] );
        $db->query( 'CREATE TABLE t (i Int32, u UInt64, d Decimal(10, 2), f Float64, v String, n Nullable(String), o Bool, dt Date, ts DateTime,
                                     ts6 DateTime64(6)) ENGINE = MergeTree ORDER BY i' );
        $text = "it's a \\ back\tslash\nwith ünïcödé \"quoted\" and \\N";
        $this->assertTrue( $db->query( "INSERT INTO t VALUES (1, 18446744073709551615, 12.34, 1.5, '" . $db->escapeString( $text ) . "', NULL, true,
                                       '2026-11-26', '2026-01-02 03:04:05', '2026-01-02 03:04:05.250000'), (2, 0, 0, 0, '', 'x', false, '1970-01-01', 0, 0)" ),
                           (string)$db->errorMessage() );
        $rows = $db->arrayQuery( 'SELECT * FROM t ORDER BY i' );
        $this->assertSame( array( 'i' => '1', 'u' => '18446744073709551615', 'd' => '12.34', 'f' => '1.5', 'v' => $text, 'n' => null, 'o' => '1',
                                  'dt' => '2026-11-26', 'ts' => '2026-01-02 03:04:05', 'ts6' => '2026-01-02 03:04:05' ), $rows[0] );
        $this->assertSame( '', $rows[1]['v'] );
        $this->assertSame( 'x', $rows[1]['n'] );
        $this->assertSame( '0', $rows[1]['o'] );
        $this->assertSame( array( '' ), $db->arrayQuery( "SELECT '' AS e", array( 'column' => 'e' ) ), 'a row of one empty text' );

        $many = $db->arrayQuery( "SELECT number AS n, concat( 'row ', toString( number ) ) AS label FROM numbers( 5000 )" );
        $this->assertCount( 5000, $many );
        $this->assertSame( array( 'n' => '4999', 'label' => 'row 4999' ), $many[4999] );

        $this->assertFalse( $db->arrayQuery( 'SELECT * FROM missing_table' ) );
        $this->assertSame( '42P01', $db->errorNumber() );
        $this->assertFalse( $db->arrayQuery( 'SELECT nope FROM t' ) );
        $this->assertSame( '42703', $db->errorNumber() );
        $this->assertFalse( $db->arrayQuery( 'SELEC 1' ) );
        $this->assertSame( '42601', $db->errorNumber() );

        $wrong = new expClickHouseHttp( array( 'user' => $this->server['user'], 'password' => 'wrong-password' ) + $this->server
                                        + array( 'host' => $this->server['server'] ) );
        $this->assertFalse( $wrong->query( 'SELECT 1' ) );
        $this->assertSame( '28000', expClickHouseDialect::errorCode( $wrong->ErrorCode ) );
        $this->assertStringNotContainsString( 'wrong-password', $wrong->Error );
    }

    /** CL-02 */
    public function testSchemaHandler()
    {
        $db = $this->db();
        $both = array_merge( eZDbSchema::read( self::root() . '/share/db_schema.dba', true ),
                             eZDbSchema::read( self::root() . '/share/db_data.dba', true ) );
        $handler = new expClickHouseSchema( $both + array( 'instance' => $db, 'type' => 'clickhouse' ) );
        $started = microtime( true );
        $this->assertTrue( $handler->insertSchema( array( 'schema' => true, 'data' => true ) ), (string)$db->errorMessage() );
        fwrite( STDERR, sprintf( "\n[clickhouse] the kernel schema and data through the schema handler: %.1f s\n", microtime( true ) - $started ) );
        $this->assertSame( 153, count( $db->relationList() ) );
        $this->assertGreaterThan( 0, (int)$db->arrayQuery( 'SELECT count() AS c FROM ezcontentobject_tree' )[0]['c'] );

        $schema = eZDbSchema::read( self::root() . '/share/db_schema.dba' );
        $back = ( new expClickHouseSchema( array( 'instance' => $db ) ) )->schema( array( 'format' => 'generic' ) );
        foreach ( array( 'ezcontentobject', 'ezsection', 'ezuser', 'ezcontentobject_attribute' ) as $table )
        {
            $expected = array_keys( $schema[$table]['fields'] );
            sort( $expected );
            $this->assertSame( $expected, array_keys( $back[$table]['fields'] ), "columns of $table" );
            foreach ( $schema[$table]['indexes'] as $index )
                if ( $index['type'] === 'primary' )
                    $this->assertSame( $index['fields'], $back[$table]['indexes']['PRIMARY']['fields'], "primary key of $table" );
        }
        $this->assertSame( 'auto_increment', $back['ezcontentobject']['fields']['id']['type'] );
        $this->assertSame( 'longtext', $back['ezcontentobject']['fields']['remote_id']['type'], 'every text is a String' );
        $this->assertSame( 'int', $back['ezcontentobject']['fields']['owner_id']['type'] );
    }

    /** CL-03 */
    public function testWhatClickHouseDoesNotDo()
    {
        $db = $this->db();
        $handler = new expClickHouseSchema( array( 'instance' => $db ) );
        $definition = array( 'name' => 't', 'fields' => array( 'id' => array( 'type' => 'int', 'not_null' => '1', 'default' => 0 ),
                                                               'name' => array( 'type' => 'varchar', 'length' => 20, 'not_null' => '1', 'default' => '' ) ),
                             'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ),
                                                 't_name' => array( 'type' => 'unique', 'fields' => array( 'name' ) ) ) );
        foreach ( $handler->generateTableSQLList( 't', $definition, array(), false ) as $sql )
            $this->assertTrue( $db->query( $sql ), (string)$db->errorMessage() );
        $this->assertTrue( $db->query( "INSERT INTO t VALUES (1, 'a')" ) );
        $this->assertTrue( $db->query( "INSERT INTO t VALUES (1, 'a')" ), 'no duplicate key error' );
        $this->assertSame( '2', $db->arrayQuery( 'SELECT count() AS c FROM t' )[0]['c'], 'both rows with the same key and the same unique name are kept' );

        $db->begin();
        $db->query( "INSERT INTO t VALUES (2, 'b')" );
        $db->rollback();
        $this->assertSame( '3', $db->arrayQuery( 'SELECT count() AS c FROM t' )[0]['c'], 'a rollback undoes nothing' );
        $this->assertSame( 0, $db->transactionCounter() );
    }

    /** CL-04 */
    public function testChangingRows()
    {
        $db = $this->db();
        $handler = new expClickHouseSchema( array( 'instance' => $db ) );
        $definition = array( 'name' => 'u', 'fields' => array( 'id' => array( 'type' => 'int', 'not_null' => '1', 'default' => 0 ),
                                                               'n' => array( 'type' => 'int', 'not_null' => '1', 'default' => 0 ) ),
                             'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ) ) );
        foreach ( $handler->generateTableSQLList( 'u', $definition, array(), false ) as $sql )
            $db->query( $sql );
        $db->query( 'INSERT INTO u SELECT number, 0 FROM numbers( 1000 )' );
        $timings = array();
        $started = microtime( true );
        $this->assertTrue( $db->query( 'UPDATE u SET n = 5 WHERE id = 7' ), (string)$db->errorMessage() );
        $timings['lightweight UPDATE'] = microtime( true ) - $started;
        $this->assertSame( '5', $db->arrayQuery( 'SELECT n FROM u WHERE id = 7' )[0]['n'] );

        // a table without the block number columns: the mutation, waited for
        $db->query( 'CREATE TABLE plain (id Int32, n Int32) ENGINE = MergeTree ORDER BY id' );
        $db->query( 'INSERT INTO plain SELECT number, 0 FROM numbers( 1000 )' );
        $started = microtime( true );
        $this->assertTrue( $db->query( 'UPDATE plain SET n = 6 WHERE id = 8' ), (string)$db->errorMessage() );
        $timings['UPDATE as a mutation'] = microtime( true ) - $started;
        $this->assertSame( '6', $db->arrayQuery( 'SELECT n FROM plain WHERE id = 8' )[0]['n'] );

        $started = microtime( true );
        $this->assertTrue( $db->query( 'DELETE FROM u WHERE id < 10' ) );
        $timings['lightweight DELETE'] = microtime( true ) - $started;
        $this->assertSame( '990', $db->arrayQuery( 'SELECT count() AS c FROM u' )[0]['c'] );
        $this->assertTrue( $db->query( 'DELETE FROM u' ) );
        $this->assertSame( '0', $db->arrayQuery( 'SELECT count() AS c FROM u' )[0]['c'] );

        $started = microtime( true );
        for ( $i = 0; $i < 20; $i++ )
            $db->query( "INSERT INTO u VALUES ($i, $i)" );
        $timings['INSERT of one row'] = ( microtime( true ) - $started ) / 20;
        $started = microtime( true );
        for ( $i = 0; $i < 20; $i++ )
            $db->arrayQuery( "SELECT n FROM u WHERE id = $i" );
        $timings['SELECT of one row by key'] = ( microtime( true ) - $started ) / 20;
        foreach ( $timings as $what => $seconds )
            fwrite( STDERR, sprintf( "\n[clickhouse] %s: %.1f ms", $what, $seconds * 1000 ) );
        fwrite( STDERR, "\n" );
    }

    /** CL-05 */
    public function testSerialIds()
    {
        $db = $this->db();
        $db->query( "CREATE TABLE s (id Int32 COMMENT 'auto_increment', name String) ENGINE = MergeTree ORDER BY id" );
        $this->assertTrue( $db->serialIDNeedsValue( 's', 'id' ) );
        $this->assertSame( 1, $db->nextSerialID( 's', 'id' ) );
        $db->query( "INSERT INTO s VALUES (1, 'a')" );
        $this->assertSame( 1, $db->lastSerialID( 's', 'id' ) );
        $db->query( "INSERT INTO s VALUES (40, 'given')" );
        $this->assertSame( 41, $db->nextSerialID( 's', 'id' ) );
        $this->assertSame( 42, $db->nextSerialID( 's', 'id' ), 'not the same id twice in one connection' );
    }

    /** CL-06 */
    public function testPortableHelpers()
    {
        $db = $this->db();
        $db->query( 'CREATE TABLE t (id Int32, name String, created Int64) ENGINE = MergeTree ORDER BY id' );
        $db->query( "INSERT INTO t VALUES (1, 'b', 86400), (2, 'a', 0), (3, 'c', 0)" );
        $this->assertSame( array( '2', '3' ), array_values( $db->arrayQuery( $db->sqlLimit( 'SELECT id FROM t ORDER BY id', 2, 1 ), array( 'column' => 'id' ) ) ) );
        $this->assertSame( array( '3' ), array_values( $db->arrayQuery( $db->sqlLimit( 'SELECT id FROM t ORDER BY id', false, 2 ), array( 'column' => 'id' ) ) ) );
        $this->assertSame( array( '2', '3' ), array_values( $db->arrayQuery( 'SELECT id FROM t ORDER BY id LIMIT 1, 2', array( 'column' => 'id' ) ) ) );
        $this->assertSame( 'c|b|a', $db->arrayQuery( 'SELECT ' . $db->sqlGroupConcat( 'name', '|', 'name DESC' ) . ' AS g FROM t' )[0]['g'] );
        $this->assertSame( 'a|b|c', $db->arrayQuery( 'SELECT ' . $db->sqlGroupConcat( 'name', '|', 'name' ) . ' AS g FROM t' )[0]['g'] );
        $now = (int)$db->arrayQuery( 'SELECT ' . $db->sqlUnixTimestamp() . ' AS n' )[0]['n'];
        $this->assertEqualsWithDelta( time(), $now, 5 );
        $this->assertSame( '1970-01-02 00:00:00', $db->arrayQuery( 'SELECT ' . $db->sqlFromUnixTimestamp( '86400' ) . ' AS d' )[0]['d'] );
        $this->assertSame( '86400', $db->arrayQuery( 'SELECT ' . $db->sqlUnixTimestamp( $db->sqlFromUnixTimestamp( '86400' ) ) . ' AS u' )[0]['u'] );
        $this->assertSame( 1, count( $db->arrayQuery( 'SELECT id FROM t WHERE id = 2 FOR UPDATE' ) ) );
        $this->assertSame( md5( 'x' ), $db->arrayQuery( 'SELECT ' . $db->md5( "'x'" ) . ' AS m' )[0]['m'] );
        $this->assertSame( 'c', $db->arrayQuery( 'SELECT ' . $db->subString( "'abcdef'", 3, 1 ) . ' AS s' )[0]['s'] );
        $this->assertSame( '1', $db->arrayQuery( 'SELECT ' . $db->bitAnd( 5, 3 ) . ' AS b' )[0]['b'] );
        $this->assertSame( 'ab', $db->arrayQuery( 'SELECT ' . $db->concatString( array( "'a'", "'b'" ) ) . ' AS c' )[0]['c'] );
        try
        {
            $db->sqlUpsert( 't', array( 'id' => 1, 'name' => 'x', 'created' => 0 ), array( 'id' ) );
            $this->fail( 'an upsert was written' );
        }
        catch ( expDBUnsupportedException $e )
        {
            $this->assertStringContainsString( 'clickhouse', $e->getMessage() );
        }
    }

    /** CL-07 */
    public function testDataTableConnection()
    {
        $engines = expDataConnections::engines();
        $this->assertTrue( $engines['clickhouse']['available'], $engines['clickhouse']['why'] );
        $descriptor = array( 'identifier' => 'clickhouse_live', 'engine' => 'clickhouse', 'server' => $this->server['server'], 'port' => $this->server['port'],
                             'user' => $this->server['user'], 'database' => $this->database );
        $this->assertSame( array(), expDataConnections::problems( $descriptor ) );
        try
        {
            $test = expDataConnections::test( $descriptor, $this->server['password'] );
            $this->assertTrue( $test['ok'], $test['error'] );
            $this->assertSame( 'ez', $test['via'] );
            $db = expDataConnections::open( $descriptor, $this->server['password'] );
            $this->assertInstanceOf( 'expClickHouseDB', $db );
            $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
            $schema = expDataTableSchema::fromDefinition( array( 'name' => 'exp_ch_dt', 'increment_key' => 'id',
                'fields' => array( 'id' => array( 'datatype' => 'integer' ), 'title' => array( 'datatype' => 'string' ), 'score' => array( 'datatype' => 'float' ) ),
                'keys' => array( 'id' ) ) );
            $result = expDataTableSchema::apply( $schema, $db );
            $this->assertTrue( $result['ok'], json_encode( $result['tables'] ) );
            $id = $db->nextSerialID( 'exp_ch_dt', 'id' );
            $db->query( "INSERT INTO exp_ch_dt ( id, title, score ) VALUES ( $id, 'Täst', 1.5 )" );
            $this->assertSame( 1, $db->lastSerialID( 'exp_ch_dt', 'id' ) );
            $rows = $db->arrayQuery( 'SELECT title, score FROM exp_ch_dt' );
            $this->assertSame( 'Täst', $rows[0]['title'] );
            $this->assertEqualsWithDelta( 1.5, (float)$rows[0]['score'], 0.0001 );
            $again = expDataTableSchema::plan( $schema, $db );
            $this->assertSame( 'none', $again['tables']['exp_ch_dt']['action'], json_encode( $again['tables']['exp_ch_dt'] ) );
            $columns = expDataTableDescriber::columns( $db, 'exp_ch_dt' );
            $this->assertArrayHasKey( 'title', $columns );
        }
        finally
        {
            expDataConnections::closeAll();
        }
    }
}
