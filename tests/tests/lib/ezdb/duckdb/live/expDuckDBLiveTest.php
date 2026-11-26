<?php
/**
 * The DuckDB driver against DuckDB itself (libduckdb through FFI). Skipped without FFI or without EXP_TEST_DUCKDB_LIBRARY
 * (the path of libduckdb.so); every test works in its own file in the temporary directory.
 *
 *  DL-01 - The binding: version, types read back (integers, decimals, floats, text inline and longer, BLOB, BOOLEAN,
 *          DATE, TIMESTAMP, HUGEINT, NULL), several chunks, changed rows, an error with its class
 *  DL-02 - The kernel schema (kernel/sql/duckdb/kernel_schema.sql) and the clean data load; sequences corrected after
 *  DL-03 - The schema handler: share/db_schema.dba inserted with its data, read back with the same tables and keys
 *  DL-04 - Transactions: commit, rollback, a failed statement inside one; nested begin/commit of eZDB
 *  DL-05 - Serial ids: nextval through the column default, lastSerialID(), correctSequenceValues()
 *  DL-06 - Errors: duplicate key 23505, missing table 42P01, exceptions mode
 *  DL-07 - The portable helpers run: LIMIT/OFFSET, ON CONFLICT upsert, STRING_AGG, UNIX timestamps, FOR UPDATE left out
 *  DL-08 - AccessMode=auto: read-only first, read-write at the first write; two processes read at once
 *  DL-09 - Another process writing: the open waits for the lock (LockWait) or fails with 55P03
 *  DL-10 - Integer division as in PostgreSQL; removeDatabase() empties the open database
 *  DL-11 - A data table connection (engine duckdb): problems(), test(), open(), a table made, planned again: nothing
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__ ) . '/expDuckDBTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('duckdb')]
#[\PHPUnit\Framework\Attributes\Group('duckdb-live')]
class expDuckDBLiveTest extends PHPUnit\Framework\TestCase
{
    private $saved;
    private $library;
    private $open = array();

    protected function setUp(): void
    {
        $this->library = expDuckDBTestKit::liveLibrary();
        if ( $this->library === null )
            $this->markTestSkipped( 'No DuckDB to test against: set EXP_TEST_DUCKDB_LIBRARY to libduckdb (PHP needs the FFI extension)' );
        $this->saved = expDuckDBTestKit::quietDebug();
    }

    protected function tearDown(): void
    {
        foreach ( $this->open as $db )
            $db->close();
        $this->open = array();
        if ( $this->library !== null )
        {
            expDuckDBTestKit::removeTempFiles();
            expDuckDBTestKit::restoreDebug( $this->saved );
        }
    }

    private function db( $file = null, array $settings = array() )
    {
        $db = new expDuckDBDB( array( 'database' => $file !== null ? $file : expDuckDBTestKit::tempFile( 'live' ),
                                      'duckdb' => $settings + array( 'Library' => $this->library, 'LockWait' => 5, 'Threads' => 2 ) ) );
        $this->open[] = $db;
        return $db;
    }

    private static function root()
    {
        return dirname( __DIR__, 6 );
    }

    /** DL-01 */
    public function testBinding()
    {
        $duck = new expDuckDBFFI( $this->library );
        $this->assertMatchesRegularExpression( '/^v\d+\.\d+/', $duck->libraryVersion() );
        $this->assertTrue( $duck->open( expDuckDBTestKit::tempFile( 'binding' ), array( 'threads' => '1' ) ), $duck->Error );
        $this->assertNotFalse( $duck->query( "CREATE TABLE t (i INTEGER, b BIGINT, s SMALLINT, d DECIMAL(10,2), d2 DECIMAL(30,4), f DOUBLE, r REAL,
                                             v VARCHAR, bl BLOB, o BOOLEAN, dt DATE, ts TIMESTAMP, h HUGEINT, u UBIGINT)" ) );
        $result = $duck->query( "INSERT INTO t VALUES (1, 9007199254740993, -3, 12.34, -123456789012345678901.5, 1.5, 0.25, 'short', 'a\\x00b'::BLOB, true,
                                 DATE '2026-11-26', TIMESTAMP '2026-01-02 03:04:05.25', 170141183460469231731687303715884105727, 18446744073709551615),
                                 (NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'a text longer than twelve bytes, with ünïcödé', NULL, false, NULL, NULL, -2, 0)" );
        $this->assertSame( 2, $result['changed'] );
        $rows = $duck->query( 'SELECT * FROM t ORDER BY i NULLS LAST' )['rows'];
        $this->assertSame( array( 'i' => '1', 'b' => '9007199254740993', 's' => '-3', 'd' => '12.34', 'd2' => '-123456789012345678901.5000', 'f' => '1.5',
                                  'r' => '0.25', 'v' => 'short', 'bl' => "a\0b", 'o' => '1', 'dt' => '2026-11-26', 'ts' => '2026-01-02 03:04:05.250000',
                                  'h' => '170141183460469231731687303715884105727', 'u' => '18446744073709551615' ), $rows[0] );
        $this->assertNull( $rows[1]['i'] );
        $this->assertSame( 'a text longer than twelve bytes, with ünïcödé', $rows[1]['v'] );
        $this->assertSame( '0', $rows[1]['o'] );
        $this->assertSame( '-2', $rows[1]['h'] );

        $many = $duck->query( "SELECT range AS n, 'row ' || range AS label FROM range(5000)" )['rows'];
        $this->assertCount( 5000, $many, 'rows of several chunks' );
        $this->assertSame( array( 'n' => '4999', 'label' => 'row 4999' ), $many[4999] );

        $this->assertFalse( $duck->query( 'SELECT nope FROM t' ) );
        $this->assertSame( expDuckDBFFI::ERROR_BINDER, $duck->ErrorType );
        $this->assertFalse( $duck->query( 'SELECT [1, 2] AS list' ), 'a LIST is refused with a message' );
        $this->assertStringContainsString( 'CAST it to VARCHAR', $duck->Error );
        $duck->close();
        $this->assertFalse( $duck->isOpen() );
    }

    /** DL-02 */
    public function testKernelSchemaAndCleanData()
    {
        $db = $this->db();
        $db->insertFile( self::root() . '/kernel/sql/duckdb', 'kernel_schema.sql', false );
        $this->assertSame( '', (string)$db->errorMessage(), 'the kernel schema loads' );
        $db->insertFile( self::root() . '/kernel/sql/common', 'cleandata.sql', false );
        $this->assertSame( '', (string)$db->errorMessage(), 'the clean data loads' );
        $this->assertTrue( $db->correctSequenceValues() );
        $this->assertGreaterThan( 100, count( $db->relationList() ) );
        $count = $db->arrayQuery( 'SELECT COUNT(*) AS c FROM ezcontentobject' );
        $this->assertGreaterThan( 0, (int)$count[0]['c'] );

        // the next section gets an id past the clean data's
        $max = (int)$db->arrayQuery( 'SELECT MAX(id) AS m FROM ezsection' )[0]['m'];
        $db->query( "INSERT INTO ezsection (name, identifier) VALUES ('Duck', 'duck')" );
        $this->assertSame( '', (string)$db->errorMessage() );
        $this->assertGreaterThan( $max, $db->lastSerialID( 'ezsection', 'id' ) );
    }

    /** DL-03 */
    public function testSchemaHandlerRoundTrip()
    {
        $db = $this->db();
        // as the setup does it (eZStepCreateSites)
        $both = array_merge( eZDbSchema::read( self::root() . '/share/db_schema.dba', true ),
                             eZDbSchema::read( self::root() . '/share/db_data.dba', true ) );
        $handler = new expDuckDBSchema( $both + array( 'instance' => $db, 'type' => 'duckdb' ) );
        $this->assertTrue( $handler->insertSchema( array( 'schema' => true, 'data' => true ) ), (string)$db->errorMessage() );
        $this->assertGreaterThan( 0, (int)$db->arrayQuery( 'SELECT COUNT(*) AS c FROM ezcontentobject_tree' )[0]['c'] );

        $schema = eZDbSchema::read( self::root() . '/share/db_schema.dba' );
        $back = ( new expDuckDBSchema( array( 'instance' => $db ) ) )->schema( array( 'format' => 'generic' ) );
        foreach ( $schema as $table => $definition )
        {
            if ( $table === '_info' )
                continue;
            $this->assertArrayHasKey( $table, $back, "table $table" );
            $expected = array_keys( $definition['fields'] );
            sort( $expected );
            $this->assertSame( $expected, array_keys( $back[$table]['fields'] ), "columns of $table" );
            foreach ( $definition['indexes'] as $name => $index )
                if ( $index['type'] === 'primary' )
                    $this->assertSame( $index['fields'], $back[$table]['indexes']['PRIMARY']['fields'], "primary key of $table" );
        }
        $this->assertSame( 'auto_increment', $back['ezcontentobject']['fields']['id']['type'] );
        $this->assertSame( 'varchar', $back['ezcontentobject']['fields']['remote_id']['type'] );
    }

    /** DL-04 */
    public function testTransactions()
    {
        $db = $this->db();
        $db->query( 'CREATE TABLE t (id INTEGER PRIMARY KEY, name VARCHAR)' );
        $db->begin();
        $db->query( "INSERT INTO t VALUES (1, 'a')" );
        $db->begin();
        $db->query( "INSERT INTO t VALUES (2, 'b')" );
        $db->commit();
        $db->commit();
        $this->assertSame( array( '1', '2' ), $db->arrayQuery( 'SELECT id FROM t ORDER BY id', array( 'column' => 'id' ) ) );

        $db->begin();
        $db->query( "INSERT INTO t VALUES (3, 'c')" );
        $db->rollback();
        $this->assertSame( 2, count( $db->arrayQuery( 'SELECT id FROM t' ) ) );

        // a failed statement inside a transaction (eZDB's exception mode, as the kernel's callers that catch it use)
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        $db->begin();
        try
        {
            $db->query( "INSERT INTO t VALUES (1, 'duplicate')" );
            $this->fail( 'no exception' );
        }
        catch ( eZDBException $e )
        {
            $db->rollback();
        }
        $db->setErrorHandling( eZDB::ERROR_HANDLING_STANDARD );
        $this->assertTrue( $db->query( "INSERT INTO t VALUES (4, 'after')" ), 'the connection works after a failed transaction' );
        $this->assertSame( 0, $db->transactionCounter() );
    }

    /** DL-05 */
    public function testSerialIds()
    {
        $db = $this->db();
        $db->query( 'CREATE SEQUENCE ezx_id_seq START 1' );
        $db->query( "CREATE TABLE ezx (id INTEGER DEFAULT nextval('ezx_id_seq') NOT NULL PRIMARY KEY, name VARCHAR DEFAULT '' NOT NULL)" );
        $db->query( "INSERT INTO ezx (name) VALUES ('a')" );
        $this->assertSame( 1, $db->lastSerialID( 'ezx', 'id' ) );
        $db->query( "INSERT INTO ezx (id, name) VALUES (40, 'given')" );
        $this->assertTrue( $db->correctSequenceValues() );
        $db->query( "INSERT INTO ezx (name) VALUES ('b')" );
        $this->assertGreaterThan( 40, $db->lastSerialID( 'ezx', 'id' ) );
    }

    /** DL-06 */
    public function testErrors()
    {
        $db = $this->db();
        $db->query( 'CREATE TABLE t (id INTEGER PRIMARY KEY)' );
        $db->query( 'INSERT INTO t VALUES (1)' );
        $this->assertFalse( $db->query( 'INSERT INTO t VALUES (1)' ) );
        $this->assertSame( '23505', $db->errorNumber() );
        $this->assertFalse( $db->arrayQuery( 'SELECT * FROM missing_table' ) );
        $this->assertSame( '42P01', $db->errorNumber() );
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        try
        {
            $db->query( 'INSERT INTO t VALUES (1)' );
            $this->fail( 'no exception' );
        }
        catch ( eZDBException $e )
        {
            $this->assertSame( '23505', $e->getCode() );
        }
    }

    /** DL-07 */
    public function testPortableHelpers()
    {
        $db = $this->db();
        $db->query( 'CREATE TABLE t (id INTEGER PRIMARY KEY, name VARCHAR, created INTEGER)' );
        $db->query( "INSERT INTO t VALUES (1, 'b', 86400), (2, 'a', 0), (3, 'c', 0)" );
        $this->assertSame( array( '2', '3' ), array_values( $db->arrayQuery( $db->sqlLimit( 'SELECT id FROM t ORDER BY id', 2, 1 ), array( 'column' => 'id' ) ) ) );
        $this->assertSame( array( '3' ), array_values( $db->arrayQuery( $db->sqlLimit( 'SELECT id FROM t ORDER BY id', false, 2 ), array( 'column' => 'id' ) ) ) );
        $this->assertTrue( $db->query( $db->sqlUpsert( 't', array( 'id' => 1, 'name' => "it's", 'created' => 5 ), array( 'id' ) ) ) );
        $this->assertTrue( $db->query( $db->sqlUpsert( 't', array( 'id' => 9, 'name' => 'new', 'created' => 5 ), array( 'id' ) ) ) );
        $this->assertSame( "it's", $db->arrayQuery( 'SELECT name FROM t WHERE id = 1', array( 'column' => 'name' ) )[0] );
        $this->assertSame( 4, count( $db->arrayQuery( 'SELECT id FROM t' ) ) );
        $rows = $db->arrayQuery( 'SELECT ' . $db->sqlGroupConcat( 'name', '|', 'name' ) . ' AS g FROM t WHERE id IN (2, 3)' );
        $this->assertSame( 'a|c', $rows[0]['g'] );
        $now = (int)$db->arrayQuery( 'SELECT ' . $db->sqlUnixTimestamp() . ' AS n' )[0]['n'];
        $this->assertEqualsWithDelta( time(), $now, 5 );
        $this->assertSame( '1970-01-02 00:00:00', $db->arrayQuery( 'SELECT ' . $db->sqlFromUnixTimestamp( '86400' ) . ' AS d' )[0]['d'] );
        $this->assertSame( 1, count( $db->arrayQuery( $db->sqlForUpdate( 'SELECT id FROM t WHERE id = 2' ) ) ) );
        $this->assertSame( 1, count( $db->arrayQuery( 'SELECT id FROM t WHERE id = 2 FOR UPDATE' ) ) );
    }

    /** DL-08 */
    public function testReadOnlyFirstAndSharedReaders()
    {
        $file = expDuckDBTestKit::tempFile( 'shared' );
        $writer = $this->db( $file );
        $writer->query( 'CREATE TABLE t (id INTEGER)' );
        $writer->query( 'INSERT INTO t VALUES (1)' );
        $writer->close();

        $db = $this->db( $file );
        $this->assertSame( 'READ_ONLY', $db->openMode() );
        $this->assertSame( 1, count( $db->arrayQuery( 'SELECT id FROM t' ) ) );
        // another process reads at the same time
        $this->assertSame( "1\n", $this->child( $file, 'read', 0 ) );
        $db->query( 'INSERT INTO t VALUES (2)' );
        $this->assertSame( 'READ_WRITE', $db->openMode() );
        $this->assertSame( 2, count( $db->arrayQuery( 'SELECT id FROM t' ) ) );
    }

    /** DL-09 */
    public function testLockWait()
    {
        $file = expDuckDBTestKit::tempFile( 'locked' );
        $db = $this->db( $file );
        $db->query( 'CREATE TABLE t (id INTEGER)' );
        $db->close();

        // a process that holds the file to write for 1.5 seconds
        $holder = $this->startChild( $file, 'hold', 1.5 );
        usleep( 500000 );
        $started = microtime( true );
        try
        {
            $this->db( $file, array( 'AccessMode' => 'read_write', 'LockWait' => 0.2 ) );
            $this->fail( 'the open did not wait for the lock' );
        }
        catch ( eZDBNoConnectionException $e )
        {
            $this->assertStringContainsString( '55P03', $e->getMessage() );
        }
        $waited = $this->db( $file, array( 'AccessMode' => 'read_write', 'LockWait' => 10 ) );
        $this->assertTrue( $waited->isConnected() );
        $this->assertGreaterThan( 0.5, microtime( true ) - $started );
        $this->assertSame( 1, count( $waited->arrayQuery( 'SELECT id FROM t' ) ), 'the holder\'s row is there' );
        proc_close( $holder );
    }

    /** DL-10 */
    public function testIntegerDivisionAndRemoveDatabase()
    {
        $file = expDuckDBTestKit::tempFile( 'misc' );
        $db = $this->db( $file );
        $this->assertSame( '3', $db->arrayQuery( 'SELECT 7 / 2 AS q' )[0]['q'] );
        $db->query( 'CREATE SEQUENCE s1' );
        $db->query( "CREATE TABLE t (id INTEGER DEFAULT nextval('s1'), name VARCHAR)" );
        $db->query( 'CREATE INDEX t_name ON t (name)' );
        $db->query( 'CREATE VIEW v AS SELECT * FROM t' );
        $this->assertTrue( $db->removeDatabase( $file ) );
        $this->assertSame( array(), $db->relationList() );
        $this->assertSame( array(), $db->relationList( eZDBInterface::RELATION_SEQUENCE ) );
        $this->assertSame( array(), $db->relationList( eZDBInterface::RELATION_VIEW ) );
    }


    /** DL-11 */
    public function testDataTableConnection()
    {
        $dir = eZSys::rootDir() . '/var/datatable';
        if ( !is_dir( $dir ) )
            mkdir( $dir, 0775, true );
        $file = 'var/datatable/exp-duckdb-live-' . bin2hex( random_bytes( 4 ) ) . '.duckdb';
        $engines = expDataConnections::engines();
        $this->assertTrue( $engines['duckdb']['available'], $engines['duckdb']['why'] );
        $descriptor = array( 'identifier' => 'duckdb_live', 'engine' => 'duckdb', 'database' => $file );
        $this->assertSame( array(), expDataConnections::problems( $descriptor ) );
        try
        {
            $test = expDataConnections::test( $descriptor );
            $this->assertTrue( $test['ok'], $test['error'] );
            $this->assertSame( 'ez', $test['via'] );
            $db = expDataConnections::open( $descriptor );
            $this->open[] = $db;
            $this->assertInstanceOf( 'expDuckDBDB', $db );
            $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
            $schema = expDataTableSchema::fromDefinition( array( 'name' => 'exp_duck_dt', 'increment_key' => 'id',
                'fields' => array( 'id' => array( 'datatype' => 'integer' ), 'title' => array( 'datatype' => 'string' ), 'score' => array( 'datatype' => 'float' ) ),
                'keys' => array( 'id' ) ) );
            $result = expDataTableSchema::apply( $schema, $db );
            $this->assertTrue( $result['ok'], json_encode( $result['tables'] ) );
            $db->query( "INSERT INTO exp_duck_dt ( title, score ) VALUES ( 'Täst', 1.5 )" );
            $this->assertSame( 1, $db->lastSerialID( 'exp_duck_dt', 'id' ) );
            $rows = $db->arrayQuery( 'SELECT title, score FROM exp_duck_dt' );
            $this->assertSame( 'Täst', $rows[0]['title'] );
            $this->assertEqualsWithDelta( 1.5, (float)$rows[0]['score'], 0.0001 );
            $again = expDataTableSchema::plan( $schema, $db );
            $this->assertSame( 'none', $again['tables']['exp_duck_dt']['action'], json_encode( $again['tables']['exp_duck_dt'] ) );
            $columns = expDataTableDescriber::columns( $db, 'exp_duck_dt' );
            $this->assertNotEmpty( $columns );
        }
        finally
        {
            foreach ( $this->open as $db )
                $db->close();
            $this->open = array();
            expDataConnections::closeAll();
            foreach ( array( $file, $file . '.wal' ) as $path )
                if ( is_file( eZSys::rootDir() . '/' . $path ) )
                    unlink( eZSys::rootDir() . '/' . $path );
        }
    }
    /**
     * Runs a child process with the binding: 'read' prints the rows of t, 'hold' opens the file to write, inserts a
     * row and keeps it open for $seconds.
     */
    private function child( $file, $mode, $seconds )
    {
        $process = $this->startChild( $file, $mode, $seconds );
        $out = stream_get_contents( $this->childOut );
        proc_close( $process );
        return $out;
    }

    private $childOut;

    private function startChild( $file, $mode, $seconds )
    {
        $script = expDuckDBTestKit::tempFile( 'child' ) . '.php';
        file_put_contents( $script, '<?php
require ' . var_export( self::root() . '/lib/ezdb/classes/expduckdbffi.php', true ) . ';
$duck = new expDuckDBFFI( ' . var_export( $this->library, true ) . ' );
if ( ' . var_export( $mode, true ) . ' === "read" ) {
    if ( !$duck->open( ' . var_export( $file, true ) . ', array( "access_mode" => "READ_ONLY" ) ) ) { echo $duck->Error; exit( 1 ); }
    foreach ( $duck->query( "SELECT id FROM t ORDER BY id" )["rows"] as $row ) echo $row["id"], "\n";
} else {
    if ( !$duck->open( ' . var_export( $file, true ) . ', array( "access_mode" => "READ_WRITE" ) ) ) { echo $duck->Error; exit( 1 ); }
    $duck->query( "INSERT INTO t VALUES (1)" );
    usleep( ' . (int)( $seconds * 1000000 ) . ' );
}
$duck->close();
' );
        $process = proc_open( array( PHP_BINARY, '-d', 'ffi.enable=true', $script ), array( 1 => array( 'pipe', 'w' ), 2 => array( 'file', '/dev/null', 'w' ) ), $pipes );
        $this->childOut = $pipes[1];
        return $process;
    }
}
