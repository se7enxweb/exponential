<?php
/**
 * The MonetDB driver against a MonetDB server (EXP_TEST_MONETDB_HOST ...; skipped without it). Every test works in a
 * schema of its own, dropped afterwards.
 *
 *  ML-01 - MAPI: login, values of every kernel type read back (text with quotes, backslashes, tabs, line breaks and
 *          Unicode, NULL, boolean, decimal, timestamp), 5,000 rows, an error with its SQLSTATE
 *  ML-02 - The kernel schema (kernel/sql/monetdb/kernel_schema.sql) and the clean data load; sequences restarted after
 *  ML-03 - The schema handler: share/db_schema.dba inserted with its data, read back with the same tables and keys
 *  ML-04 - Transactions: commit, rollback, a failed statement inside one; nested begin/commit of eZDB
 *  ML-05 - Serial ids: NEXT VALUE FOR through the column default, lastSerialID() per table, correctSequenceValues()
 *  ML-06 - Errors: duplicate key 23505 (primary key and unique constraint), missing table 42P01, exceptions mode
 *  ML-07 - The portable helpers run: LIMIT/OFFSET, MERGE upsert, GROUP_CONCAT, UNIX timestamps, FOR UPDATE left out
 *  ML-08 - Without the write lock: two open transactions that change one table conflict (40001), even on other rows
 *  ML-09 - The reserved kernel columns day and records in kernel statements; backticks and LIMIT offset, count
 *  ML-10 - removeDatabase() empties the schema
 *  ML-11 - A data table connection (engine monetdb): test(), open(), a table made, planned again: nothing
 *  ML-12 - The write lock: a second writer waits (LockWait) and fails with 55P03, reads do not wait, a writer in another
 *          process writes after the commit; retryTransaction()
 *  ML-13 - Schema upgrades through the handler: a column added, a column's type changed (copied into a new column)
 *  ML-14 - DROP TABLE drops the sequences of the table's serial columns (so the table can be made again), not one
 *          another table still reads; the reserved extension columns chain, snapshot, details and next through the
 *          schema handler and in statements
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__ ) . '/expColumnarTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('columnar-live')]
class expMonetDBLiveTest extends PHPUnit\Framework\TestCase
{
    private $saved;
    private $server;
    private $schema;
    private $open = array();

    protected function setUp(): void
    {
        $this->server = expColumnarTestKit::live( 'MONETDB' );
        if ( $this->server === null )
            $this->markTestSkipped( 'No MonetDB to test against: set EXP_TEST_MONETDB_HOST (and _PORT, _USER, _PASSWORD, _DATABASE)' );
        $this->saved = expColumnarTestKit::quietDebug();
        $this->schema = expColumnarTestKit::uniqueName( 'exp_test' );
        $admin = $this->connect( array() );
        $this->assertTrue( $admin->query( 'CREATE SCHEMA ' . $this->schema ), (string)$admin->errorMessage() );
    }

    protected function tearDown(): void
    {
        if ( $this->server === null )
            return;
        foreach ( $this->open as $db )
            $db->close();
        $this->open = array();
        $admin = $this->connect( array() );
        $admin->query( 'DROP SCHEMA ' . $this->schema . ' CASCADE' );
        $admin->close();
        $this->open = array();
        expColumnarTestKit::restoreDebug( $this->saved );
    }

    private function connect( array $settings )
    {
        $db = new expMonetDBDB( $this->server + array( 'monetdb' => $settings ) );
        $this->open[] = $db;
        return $db;
    }

    private function db()
    {
        return $this->connect( array( 'Schema' => $this->schema ) );
    }

    private static function sorted( array $list )
    {
        sort( $list );
        return $list;
    }

    private static function root()
    {
        return dirname( __DIR__, 6 );
    }

    /** ML-01 */
    public function testMapi()
    {
        $db = $this->db();
        $this->assertTrue( $db->isConnected() );
        $version = $db->databaseServerVersion();
        $this->assertMatchesRegularExpression( '/^11\.\d+/', $version['string'] );
        $db->query( 'CREATE TABLE t (i INTEGER, b BIGINT, s SMALLINT, d DECIMAL(10,2), f DOUBLE, r REAL, v VARCHAR(200), c CLOB, o BOOLEAN,
                                     dt DATE, ts TIMESTAMP)' );
        $text = "it's a \\ back\tslash\nwith ünïcödé \"quoted\" and 100%";
        $this->assertTrue( $db->query( "INSERT INTO t VALUES (1, 9007199254740993, -3, 12.34, 1.5, 0.25, '" . $db->escapeString( $text ) . "', '"
                                       . $db->escapeString( str_repeat( 'long ', 4000 ) ) . "', true, DATE '2026-11-26', TIMESTAMP '2026-01-02 03:04:05.25')" ),
                           (string)$db->errorMessage() );
        $db->query( "INSERT INTO t VALUES (NULL, NULL, NULL, NULL, NULL, NULL, 'NULL', '', false, NULL, NULL)" );
        $rows = $db->arrayQuery( 'SELECT * FROM t ORDER BY i NULLS LAST' );
        $this->assertSame( array( 'i' => '1', 'b' => '9007199254740993', 's' => '-3', 'd' => '12.34', 'f' => '1.5', 'r' => '0.25', 'v' => $text,
                                  'c' => str_repeat( 'long ', 4000 ), 'o' => '1', 'dt' => '2026-11-26', 'ts' => '2026-01-02 03:04:05' ), $rows[0] );
        $this->assertNull( $rows[1]['i'] );
        $this->assertSame( 'NULL', $rows[1]['v'], 'the text NULL is not a NULL' );
        $this->assertSame( '', $rows[1]['c'] );
        $this->assertSame( '0', $rows[1]['o'] );

        $db->query( 'CREATE TABLE n (n INTEGER, label VARCHAR(20))' );
        $db->query( "INSERT INTO n SELECT value, 'row ' || CAST(value AS VARCHAR(10)) FROM sys.generate_series(0, 5000)" );
        $many = $db->arrayQuery( 'SELECT n, label FROM n ORDER BY n' );
        $this->assertCount( 5000, $many );
        $this->assertSame( array( 'n' => '4999', 'label' => 'row 4999' ), $many[4999] );

        $this->assertFalse( $db->arrayQuery( 'SELECT nope FROM t' ) );
        $this->assertSame( '42703', $db->errorNumber() );
    }

    /** ML-02 */
    public function testKernelSchemaAndCleanData()
    {
        $db = $this->db();
        $started = microtime( true );
        $db->insertFile( self::root() . '/kernel/sql/monetdb', 'kernel_schema.sql', false );
        $this->assertSame( '', (string)$db->errorMessage(), 'the kernel schema loads' );
        $db->begin();
        $db->insertFile( self::root() . '/kernel/sql/common', 'cleandata.sql', false );
        $db->commit();
        $this->assertSame( '', (string)$db->errorMessage(), 'the clean data loads' );
        $this->assertTrue( $db->correctSequenceValues() );
        fwrite( STDERR, sprintf( "\n[monetdb] kernel schema and clean data: %.1f s\n", microtime( true ) - $started ) );
        $this->assertSame( 153, count( array_diff( $db->relationList(), array( expMonetDBDB::LOCK_TABLE ) ) ) );
        $this->assertGreaterThan( 0, (int)$db->arrayQuery( 'SELECT COUNT(*) AS c FROM ezcontentobject' )[0]['c'] );

        $max = (int)$db->arrayQuery( 'SELECT MAX(id) AS m FROM ezsection' )[0]['m'];
        $db->query( "INSERT INTO ezsection (name, identifier) VALUES ('Monet', 'monet')" );
        $this->assertSame( '', (string)$db->errorMessage() );
        $this->assertGreaterThan( $max, $db->lastSerialID( 'ezsection', 'id' ) );
        // a reserved column of the kernel, through a kernel-style statement
        $db->query( "INSERT INTO ezgeneral_digest_user_settings (user_id, day, time, receive_digest, digest_type) VALUES (14, 'monday', '08:00', 1, 1)" );
        $this->assertSame( '', (string)$db->errorMessage() );
        $this->assertSame( 'monday', $db->arrayQuery( 'SELECT day FROM ezgeneral_digest_user_settings WHERE user_id = 14' )[0]['day'] );
    }

    /** ML-03 */
    public function testSchemaHandlerRoundTrip()
    {
        $db = $this->db();
        $both = array_merge( eZDbSchema::read( self::root() . '/share/db_schema.dba', true ),
                             eZDbSchema::read( self::root() . '/share/db_data.dba', true ) );
        $handler = new expMonetDBSchema( $both + array( 'instance' => $db, 'type' => 'monetdb' ) );
        $started = microtime( true );
        $this->assertTrue( $handler->insertSchema( array( 'schema' => true, 'data' => true ) ), (string)$db->errorMessage() );
        fwrite( STDERR, sprintf( "\n[monetdb] schema handler, schema and data: %.1f s\n", microtime( true ) - $started ) );
        $this->assertGreaterThan( 0, (int)$db->arrayQuery( 'SELECT COUNT(*) AS c FROM ezcontentobject_tree' )[0]['c'] );

        $schema = eZDbSchema::read( self::root() . '/share/db_schema.dba' );
        $back = ( new expMonetDBSchema( array( 'instance' => $db ) ) )->schema( array( 'format' => 'generic' ) );
        foreach ( $schema as $table => $definition )
        {
            if ( $table === '_info' )
                continue;
            $this->assertArrayHasKey( $table, $back, "table $table" );
            $expected = array_keys( $definition['fields'] );
            sort( $expected );
            $this->assertSame( $expected, array_keys( $back[$table]['fields'] ), "columns of $table" );
            foreach ( $definition['indexes'] as $name => $index )
            {
                if ( $index['type'] === 'primary' )
                    $this->assertSame( $index['fields'], $back[$table]['indexes']['PRIMARY']['fields'], "primary key of $table" );
                elseif ( $index['type'] === 'unique' )
                    $this->assertSame( 'unique', $back[$table]['indexes'][$name]['type'], "unique key $name of $table" );
            }
        }
        $this->assertSame( 'auto_increment', $back['ezcontentobject']['fields']['id']['type'] );
        $this->assertSame( 'varchar', $back['ezcontentobject']['fields']['remote_id']['type'] );
    }

    /** ML-04 */
    public function testTransactions()
    {
        $db = $this->db();
        $db->query( 'CREATE TABLE t (id INTEGER PRIMARY KEY, name VARCHAR(20))' );
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
        $this->assertSame( 3, count( $db->arrayQuery( 'SELECT id FROM t' ) ) );
    }

    /** ML-05 */
    public function testSerialIds()
    {
        $db = $this->db();
        $db->query( 'CREATE SEQUENCE ezx_id_seq AS INTEGER START WITH 1' );
        $db->query( "CREATE TABLE ezx (id INTEGER DEFAULT NEXT VALUE FOR ezx_id_seq NOT NULL PRIMARY KEY, name VARCHAR(20) DEFAULT '' NOT NULL)" );
        $db->query( 'CREATE TABLE ezy (id INTEGER, name VARCHAR(20))' );
        $db->query( "INSERT INTO ezx (name) VALUES ('a')" );
        $db->query( "INSERT INTO ezy VALUES (7, 'other table')" );
        $this->assertSame( 1, $db->lastSerialID( 'ezx', 'id' ) );
        $db->query( "INSERT INTO ezx (id, name) VALUES (40, 'given')" );
        $this->assertTrue( $db->correctSequenceValues() );
        $db->query( "INSERT INTO ezx (name) VALUES ('b')" );
        $this->assertSame( 41, $db->lastSerialID( 'ezx', 'id' ) );
    }

    /** ML-06 */
    public function testErrors()
    {
        $db = $this->db();
        $db->query( 'CREATE TABLE t (id INTEGER PRIMARY KEY, code VARCHAR(10), CONSTRAINT t_code UNIQUE (code))' );
        $db->query( "INSERT INTO t VALUES (1, 'a')" );
        $this->assertFalse( $db->query( "INSERT INTO t VALUES (1, 'b')" ) );
        $this->assertSame( '23505', $db->errorNumber() );
        $this->assertFalse( $db->query( "INSERT INTO t VALUES (2, 'a')" ) );
        $this->assertSame( '23505', $db->errorNumber(), 'a unique constraint is enforced' );
        $this->assertFalse( $db->arrayQuery( 'SELECT * FROM missing_table' ) );
        $this->assertSame( '42P01', $db->errorNumber() );
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        try
        {
            $db->query( "INSERT INTO t VALUES (1, 'c')" );
            $this->fail( 'no exception' );
        }
        catch ( eZDBException $e )
        {
            $this->assertSame( '23505', $e->getCode() );
        }
    }

    /** ML-07 */
    public function testPortableHelpers()
    {
        $db = $this->db();
        $db->query( 'CREATE TABLE t (id INTEGER PRIMARY KEY, name VARCHAR(20), created INTEGER)' );
        $db->query( "INSERT INTO t VALUES (1, 'b', 86400), (2, 'a', 0), (3, 'c', 0)" );
        $this->assertSame( array( '2', '3' ), array_values( $db->arrayQuery( $db->sqlLimit( 'SELECT id FROM t ORDER BY id', 2, 1 ), array( 'column' => 'id' ) ) ) );
        $this->assertSame( array( '3' ), array_values( $db->arrayQuery( $db->sqlLimit( 'SELECT id FROM t ORDER BY id', false, 2 ), array( 'column' => 'id' ) ) ) );
        $this->assertSame( array( 2 => '3' ), $db->arrayQuery( 'SELECT id FROM t ORDER BY id', array( 'column' => 'id', 'offset' => 2, 'limit' => 5 ) ) );
        $this->assertTrue( $db->query( $db->sqlUpsert( 't', array( 'id' => 1, 'name' => "it's", 'created' => 5 ), array( 'id' ) ) ), (string)$db->errorMessage() );
        $this->assertTrue( $db->query( $db->sqlUpsert( 't', array( 'id' => 9, 'name' => 'new', 'created' => 5 ), array( 'id' ) ) ) );
        $this->assertTrue( $db->query( $db->sqlUpsert( 't', array( 'id' => 9, 'name' => 'kept', 'created' => 6 ), array( 'id' ), array() ) ) );
        $this->assertSame( "it's", $db->arrayQuery( 'SELECT name FROM t WHERE id = 1', array( 'column' => 'name' ) )[0] );
        $this->assertSame( 'new', $db->arrayQuery( 'SELECT name FROM t WHERE id = 9', array( 'column' => 'name' ) )[0], 'insert only' );
        $this->assertSame( 4, count( $db->arrayQuery( 'SELECT id FROM t' ) ) );
        $rows = $db->arrayQuery( 'SELECT ' . $db->sqlGroupConcat( 'name', '|', 'name DESC' ) . ' AS g FROM t WHERE id IN (2, 3)' );
        // the order inside GROUP_CONCAT where the server takes it (not Dec2023: there the driver leaves the order out)
        $this->assertSame( $db->groupConcatOrders() ? 'c|a' : array( 'a', 'c' ), $db->groupConcatOrders() ? $rows[0]['g'] : self::sorted( explode( '|', $rows[0]['g'] ) ) );
        $now = (int)$db->arrayQuery( 'SELECT ' . $db->sqlUnixTimestamp() . ' AS n' )[0]['n'];
        $this->assertEqualsWithDelta( time(), $now, 5 );
        $this->assertSame( '1970-01-02 00:00:00', $db->arrayQuery( 'SELECT ' . $db->sqlFromUnixTimestamp( '86400' ) . ' AS d' )[0]['d'] );
        $this->assertSame( '86400', $db->arrayQuery( 'SELECT ' . $db->sqlUnixTimestamp( $db->sqlFromUnixTimestamp( '86400' ) ) . ' AS u' )[0]['u'] );
        $this->assertSame( 1, count( $db->arrayQuery( $db->sqlForUpdate( 'SELECT id FROM t WHERE id = 2' ) ) ) );
        $this->assertSame( 1, count( $db->arrayQuery( 'SELECT id FROM t WHERE id = 2 FOR UPDATE' ) ) );
        $this->assertSame( 'c', trim( $db->arrayQuery( 'SELECT ' . $db->subString( "'abcdef'", 3, 1 ) . ' AS s' )[0]['s'] ) );
        $this->assertSame( '1', $db->arrayQuery( 'SELECT ' . $db->bitAnd( 5, 3 ) . ' AS b' )[0]['b'] );
        $this->assertSame( md5( 'x' ), $db->arrayQuery( 'SELECT ' . $db->md5( "'x'" ) . ' AS m' )[0]['m'] );
        $this->assertSame( 'ab', $db->arrayQuery( 'SELECT ' . $db->concatString( array( "'a'", "'b'" ) ) . ' AS c' )[0]['c'] );
    }

    /** ML-08 */
    public function testOptimisticConflictWithoutWriteLock()
    {
        $db = $this->connect( array( 'Schema' => $this->schema, 'WriteLock' => 'disabled' ) );
        $db->query( 'CREATE TABLE t (id INTEGER PRIMARY KEY, n INTEGER)' );
        $db->query( 'INSERT INTO t VALUES (1, 0), (2, 0)' );
        $other = $this->connect( array( 'Schema' => $this->schema, 'WriteLock' => 'disabled', 'ConflictRetries' => 0 ) );
        $other->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        $db->begin();
        $other->begin();
        $db->query( 'UPDATE t SET n = 1 WHERE id = 1' );
        try
        {
            // another row of the same table: MonetDB tells the two transactions' changes apart by table, not by row
            $other->query( 'UPDATE t SET n = 2 WHERE id = 2' );
            $this->fail( 'no conflict' );
        }
        catch ( eZDBException $e )
        {
            $this->assertSame( '40001', $e->getCode() );
            $other->rollback();
        }
        $this->assertSame( 0, $other->transactionCounter(), 'the failed transaction is rolled back' );
        $this->assertTrue( $db->commit() );
        $this->assertSame( '1', $db->arrayQuery( 'SELECT n FROM t WHERE id = 1' )[0]['n'] );
        $this->assertTrue( $other->query( 'UPDATE t SET n = 3 WHERE id = 2' ), 'the connection works after the conflict' );
    }

    /** ML-12 */
    public function testWriteLock()
    {
        $db = $this->db();
        $db->query( 'CREATE TABLE t (id INTEGER PRIMARY KEY, n INTEGER)' );
        $db->query( 'INSERT INTO t VALUES (1, 0), (2, 0)' );
        $other = $this->connect( array( 'Schema' => $this->schema, 'LockWait' => 0.3 ) );
        $other->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        $db->begin();
        $db->query( 'UPDATE t SET n = 1 WHERE id = 1' );
        $started = microtime( true );
        try
        {
            $other->query( 'UPDATE t SET n = 2 WHERE id = 2' );
            $this->fail( 'the write did not wait for the lock' );
        }
        catch ( eZDBException $e )
        {
            $this->assertSame( '55P03', $e->getCode() );
        }
        $this->assertGreaterThan( 0.25, microtime( true ) - $started, 'the writer waited LockWait' );
        $this->assertGreaterThan( 0, $other->LockWaits );
        $this->assertSame( array( array( 'n' => '0' ) ), $other->arrayQuery( 'SELECT n FROM t WHERE id = 1' ), 'reads do not wait' );
        $db->commit();
        $this->assertTrue( $other->query( 'UPDATE t SET n = 2 WHERE id = 2' ), 'the lock is free after the commit' );

        // a writer in another process waits for the lock and then writes
        $db->begin();
        $db->query( 'UPDATE t SET n = 10 WHERE id = 1' );
        $child = $this->startChild( 'UPDATE t SET n = n + 1 WHERE id = 1' );
        usleep( 700000 );
        $db->commit();
        $out = stream_get_contents( $this->childOut );
        proc_close( $child );
        $this->assertStringContainsString( 'ok', $out );
        $this->assertSame( '11', $db->arrayQuery( 'SELECT n FROM t WHERE id = 1' )[0]['n'], 'the waiting writer wrote after the commit' );

        // retryTransaction(): the whole transaction again after a conflict
        $runs = 0;
        $value = $other->retryTransaction( function ( $db ) use ( &$runs ) {
            $runs++;
            $db->query( 'UPDATE t SET n = n + 1 WHERE id = 2' );
            return 'done';
        } );
        $this->assertSame( 'done', $value );
        $this->assertSame( 1, $runs );
    }

    private $childOut;

    /**
     * A PHP process that runs one write through the driver, with the write lock and LockWait 10 s.
     */
    private function startChild( $sql )
    {
        $script = sys_get_temp_dir() . '/exp-monetdb-child-' . getmypid() . '-' . bin2hex( random_bytes( 3 ) ) . '.php';
        file_put_contents( $script, '<?php
chdir( ' . var_export( self::root(), true ) . ' );
require "vendor/autoload.php";
$GLOBALS["eZDebugEnabled"] = false;
$db = new expMonetDBDB( ' . var_export( $this->server + array( 'monetdb' => array( 'Schema' => $this->schema, 'LockWait' => 10 ) ), true ) . ' );
echo $db->query( ' . var_export( $sql, true ) . ' ) ? "ok" : "failed: " . $db->errorMessage();
unlink( __FILE__ );
' );
        $process = proc_open( array( PHP_BINARY, $script ), array( 1 => array( 'pipe', 'w' ), 2 => array( 'file', '/dev/null', 'w' ) ), $pipes );
        $this->childOut = $pipes[1];
        return $process;
    }

    /** ML-09 */
    public function testKernelStatementsRewritten()
    {
        $db = $this->db();
        $db->query( "CREATE TABLE expaudit_file (channel VARCHAR(32), file_name VARCHAR(64), \"records\" INTEGER DEFAULT 0 NOT NULL)" );
        $this->assertTrue( $db->query( "INSERT INTO expaudit_file (channel, file_name, records) VALUES ('a', 'f', 3)" ), (string)$db->errorMessage() );
        $this->assertTrue( $db->query( "UPDATE expaudit_file SET records = records + 1 WHERE channel = 'a'" ), (string)$db->errorMessage() );
        $this->assertSame( '4', $db->arrayQuery( "SELECT `records` FROM expaudit_file WHERE file_name = 'records'  OR channel = 'a'" )[0]['records'] );
        $db->query( 'CREATE TABLE t (id INTEGER)' );
        $db->query( 'INSERT INTO t VALUES (1), (2), (3), (4)' );
        $this->assertSame( array( '3', '4' ), array_values( $db->arrayQuery( 'SELECT id FROM t ORDER BY id LIMIT 2, 2', array( 'column' => 'id' ) ) ) );
        $this->assertTrue( $db->query( 'LOCK TABLE t WRITE' ), 'LOCK TABLE is not sent' );
    }

    /** ML-13 */
    public function testSchemaUpgrades()
    {
        $db = $this->db();
        $handler = new expMonetDBSchema( array( 'instance' => $db ) );
        $db->query( "CREATE TABLE ezu (id INTEGER NOT NULL PRIMARY KEY, code VARCHAR(10) DEFAULT '' NOT NULL)" );
        $db->query( "INSERT INTO ezu VALUES (1, 'abc')" );
        $statements = array_merge(
            explode( "\n", trim( $handler->generateAddFieldSql( 'ezu', 'extra', array( 'type' => 'int', 'not_null' => '1', 'default' => 7 ), array() ) ) ),
            explode( "\n", trim( $handler->generateAlterFieldSql( 'ezu', 'code', array( 'type' => 'varchar', 'length' => 50, 'not_null' => '1', 'default' => 'x' ), array() ) ) ) );
        foreach ( $statements as $sql )
            $this->assertTrue( $db->query( rtrim( $sql, ';' ) ), $sql . ': ' . $db->errorMessage() );
        $this->assertSame( array( 'id' => '1', 'extra' => '7', 'code' => 'abc' ), $db->arrayQuery( 'SELECT id, extra, code FROM ezu' )[0] );
        $back = $handler->fetchTableFields( 'ezu', array( 'sort_columns' => true ) );
        $this->assertSame( 50, $back['code']['length'] );
        $this->assertSame( 'x', $back['code']['default'] );
        $this->assertSame( '1', $back['code']['not_null'] );
    }

    /** ML-14 */
    public function testDropTableDropsSerialSequences()
    {
        $db = $this->db();
        $handler = new expMonetDBSchema( array( 'instance' => $db ) );
        $schema = array( 'ezx' => array( 'name' => 'ezx',
                                         'fields' => array( 'id' => array( 'type' => 'auto_increment', 'default' => false ),
                                                            'name' => array( 'type' => 'varchar', 'length' => 20, 'not_null' => '1', 'default' => '' ) ),
                                         'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ) ) ) );
        $statements = function () use ( $handler, $schema )
        {
            $arrays = $handler->generateTableArrays( 'ezx', $schema['ezx'], array(), false );
            return array_merge( $arrays['sequences'], $arrays['tables'], $arrays['indexes'] );
        };
        foreach ( $statements() as $sql )
            $this->assertTrue( $db->query( $sql ), $sql . ': ' . $db->errorMessage() );
        $db->query( 'CREATE SEQUENCE shared_seq AS INTEGER START WITH 1' );
        $db->query( 'CREATE TABLE eza (id INTEGER DEFAULT NEXT VALUE FOR shared_seq)' );
        $db->query( 'CREATE TABLE ezb (id INTEGER DEFAULT NEXT VALUE FOR shared_seq)' );
        $this->assertSame( array( 'ezx_id_seq', 'shared_seq' ), self::sorted( $db->relationList( eZDBInterface::RELATION_SEQUENCE ) ) );

        $this->assertTrue( $db->query( 'DROP TABLE IF EXISTS ezx' ), (string)$db->errorMessage() );
        $this->assertTrue( $db->query( 'DROP TABLE eza CASCADE' ), (string)$db->errorMessage() );
        $this->assertSame( array( 'shared_seq' ), $db->relationList( eZDBInterface::RELATION_SEQUENCE ), 'ezb still reads shared_seq' );
        $this->assertTrue( $db->query( 'DROP TABLE IF EXISTS ezx' ), 'a table that is gone: nothing to do' );

        // made again, as an extension schema is inserted after its tables were dropped; in a transaction as well
        foreach ( $statements() as $sql )
            $this->assertTrue( $db->query( $sql ), $sql . ': ' . $db->errorMessage() );
        $db->begin();
        $this->assertTrue( $db->query( 'DROP TABLE ezx' ), (string)$db->errorMessage() );
        foreach ( $statements() as $sql )
            $this->assertTrue( $db->query( $sql ), $sql . ': ' . $db->errorMessage() );
        $this->assertTrue( $db->commit() );
        $db->query( "INSERT INTO ezx (name) VALUES ('a')" );
        $this->assertSame( 1, $db->lastSerialID( 'ezx', 'id' ) );

        // columns that are reserved words in MonetDB
        $reserved = array( 'expai_chain_run' => array( 'chain', 'snapshot' ), 'expai_probe_result' => array( 'details' ),
                           'sqliimport_scheduled' => array( 'next' ) );
        foreach ( $reserved as $table => $columns )
        {
            $fields = array( 'id' => array( 'type' => 'auto_increment', 'default' => false ) );
            foreach ( $columns as $column )
                $fields[$column] = array( 'type' => 'varchar', 'length' => 20, 'not_null' => '1', 'default' => '' );
            $arrays = $handler->generateTableArrays( $table, array( 'name' => $table, 'fields' => $fields,
                                                                    'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ) ) ), array(), false );
            foreach ( array_merge( $arrays['sequences'], $arrays['tables'] ) as $sql )
                $this->assertTrue( $db->query( $sql ), $sql . ': ' . $db->errorMessage() );
            $sql = "INSERT INTO $table (" . implode( ', ', $columns ) . ") VALUES ('" . implode( "', '", $columns ) . "')";
            $this->assertTrue( $db->query( $sql ), $sql . ': ' . $db->errorMessage() );
            $rows = $db->arrayQuery( 'SELECT ' . implode( ', ', $columns ) . " FROM $table WHERE {$columns[0]} = '{$columns[0]}'" );
            $this->assertSame( array_combine( $columns, $columns ), $rows[0] );
        }
    }

    /** ML-10 */
    public function testRemoveDatabase()
    {
        $db = $this->db();
        $db->query( 'CREATE SEQUENCE s1 AS INTEGER START WITH 1' );
        $db->query( 'CREATE TABLE t (id INTEGER DEFAULT NEXT VALUE FOR s1, name VARCHAR(20))' );
        $db->query( 'CREATE INDEX t_name ON t (name)' );
        $db->query( 'CREATE VIEW v AS SELECT * FROM t' );
        $this->assertSame( array( expMonetDBDB::LOCK_TABLE, 't' ), $db->relationList(), 'the table, and the write lock\'s' );
        $this->assertSame( array( 't_name' ), $db->relationList( eZDBInterface::RELATION_INDEX ) );
        $this->assertTrue( $db->removeDatabase( $this->server['database'] ) );
        $this->assertSame( array(), $db->relationList() );
        $this->assertSame( array(), $db->relationList( eZDBInterface::RELATION_SEQUENCE ) );
        $this->assertSame( array(), $db->relationList( eZDBInterface::RELATION_VIEW ) );
    }

    /** ML-11 */
    public function testDataTableConnection()
    {
        $engines = expDataConnections::engines();
        $this->assertTrue( $engines['monetdb']['available'], $engines['monetdb']['why'] );
        $descriptor = array( 'identifier' => 'monetdb_live', 'engine' => 'monetdb', 'server' => $this->server['server'], 'port' => $this->server['port'],
                             'user' => $this->server['user'], 'database' => $this->server['database'], 'options' => array( 'schema' => $this->schema ) );
        $this->assertSame( array(), expDataConnections::problems( $descriptor ) );
        try
        {
            $test = expDataConnections::test( $descriptor, $this->server['password'] );
            $this->assertTrue( $test['ok'], $test['error'] );
            $this->assertSame( 'ez', $test['via'] );
            $db = expDataConnections::open( $descriptor, $this->server['password'] );
            $this->open[] = $db;
            $this->assertInstanceOf( 'expMonetDBDB', $db );
            $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
            $schema = expDataTableSchema::fromDefinition( array( 'name' => 'exp_monet_dt', 'increment_key' => 'id',
                'fields' => array( 'id' => array( 'datatype' => 'integer' ), 'title' => array( 'datatype' => 'string' ), 'score' => array( 'datatype' => 'float' ) ),
                'keys' => array( 'id' ) ) );
            $result = expDataTableSchema::apply( $schema, $db );
            $this->assertTrue( $result['ok'], json_encode( $result['tables'] ) );
            $db->query( "INSERT INTO exp_monet_dt ( title, score ) VALUES ( 'Täst', 1.5 )" );
            $this->assertSame( 1, $db->lastSerialID( 'exp_monet_dt', 'id' ) );
            $rows = $db->arrayQuery( 'SELECT title, score FROM exp_monet_dt' );
            $this->assertSame( 'Täst', $rows[0]['title'] );
            $this->assertEqualsWithDelta( 1.5, (float)$rows[0]['score'], 0.0001 );
            $again = expDataTableSchema::plan( $schema, $db );
            $this->assertSame( 'none', $again['tables']['exp_monet_dt']['action'], json_encode( $again['tables']['exp_monet_dt'] ) );
            $this->assertNotEmpty( expDataTableDescriber::columns( $db, 'exp_monet_dt' ) );
        }
        finally
        {
            expDataConnections::closeAll();
        }
    }
}
