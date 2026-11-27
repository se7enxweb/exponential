<?php
/**
 * The DuckDB driver (lib/ezdb/classes/expduckdbdb.php) on a stand-in for the library binding, without DuckDB:
 *
 *  DB-01 - filePath(): a name in var/storage/duckdb (.duckdb added), an absolute path and :memory: as they are
 *  DB-02 - settings() and openConfig(): defaults, overrides, threads, memory limit, extra Config[] names checked
 *  DB-03 - AccessMode=auto: an existing file opens read-only; the first write opens it again read-write, once
 *  DB-04 - A transaction opens the file read-write before BEGIN; AccessMode=read_only refuses writes
 *  DB-05 - Another process's lock: the open is tried again until LockWait, then fails with SQLSTATE 55P03
 *  DB-06 - The connection's settings: integer_division at every open, SET statements replayed after a reopen
 *  DB-07 - arrayQuery(): limit/offset, rows keyed from the offset, column by name and by position
 *  DB-08 - A failed statement: error message, SQLSTATE, an eZDBException with exceptions on
 *  DB-09 - Locks: FOR UPDATE left out, LOCK TABLE not sent; lock()/unlock() are a transaction
 *  DB-10 - lastSerialID() reads currval of <table>_<column>_seq; the SQL expressions (substring, ||, MD5, & and |)
 *  DB-11 - databaseName(), sqlHelperDialect(), relation list queries, version strings, close() rolls back
 *  DB-12 - correctSequenceValues(): setval() where DuckDB has it, nextval() over range() where it has not
 *  DB-13 - release() at the end of a request lets the file go; a later statement opens it again, close() does not
 *  DB-14 - What the kernel asks the driver instead of its name: isUniqueViolation(), tableExistsInSchema(),
 *          supportsUpdateFrom(); kernel core no longer names DuckDB in the three places that did
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expDuckDBTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('duckdb')]
class expDuckDBDBTest extends PHPUnit\Framework\TestCase
{
    private $saved;

    /** @var string a file that exists, so AccessMode=auto opens it read-only */
    private $existing;

    protected function setUp(): void
    {
        $this->saved = expDuckDBTestKit::quietDebug();
        $this->existing = expDuckDBTestKit::tempFile( 'existing' );
        touch( $this->existing );
    }

    protected function tearDown(): void
    {
        expDuckDBTestKit::removeTempFiles();
        expDuckDBTestKit::restoreDebug( $this->saved );
    }

    /** DB-01 */
    public function testFilePath()
    {
        $this->assertSame( 'var/storage/duckdb/site.duckdb', expDuckDBDB::filePath( 'site' ) );
        $this->assertSame( 'var/storage/duckdb/site.db', expDuckDBDB::filePath( 'site.db' ) );
        $this->assertSame( '/srv/data/site.duckdb', expDuckDBDB::filePath( '/srv/data/site.duckdb' ) );
        $this->assertSame( 'C:\\data\\site.duckdb', expDuckDBDB::filePath( 'C:\\data\\site.duckdb' ) );
        $this->assertSame( ':memory:', expDuckDBDB::filePath( ':memory:' ) );
        $this->assertSame( '', expDuckDBDB::filePath( '' ) );
    }

    /** DB-02 */
    public function testSettingsAndOpenConfig()
    {
        $settings = expDuckDBDB::settings( array( 'Threads' => 3 ) );
        $this->assertSame( 'auto', $settings['AccessMode'] );
        $this->assertSame( 3, $settings['Threads'] );
        $engine = new expDuckDBFakeEngine();
        $db = expDuckDBTestKit::driver( $engine, array( 'Threads' => 2, 'MemoryLimit' => '512MB',
                                                        'Config' => array( 'preserve_insertion_order' => 'false', 'bad name;' => 'x' ) ), $this->existing );
        $config = $db->openConfig( 'READ_WRITE' );
        $this->assertSame( array( 'access_mode' => 'READ_WRITE', 'threads' => '2', 'memory_limit' => '512MB', 'preserve_insertion_order' => 'false' ), $config );
        $memory = expDuckDBTestKit::driver( new expDuckDBFakeEngine(), array(), ':memory:' );
        $this->assertArrayNotHasKey( 'access_mode', $memory->openConfig( 'READ_ONLY' ) );
        $this->assertSame( 10.0, expDuckDBTestKit::driver( new expDuckDBFakeEngine(), array( 'LockWait' => '' , 'LockWaitCommandLine' => 10 ), $this->existing )->lockWait() );
    }

    /** DB-03 */
    public function testReadOnlyUntilTheFirstWrite()
    {
        $engine = new expDuckDBFakeEngine();
        $db = expDuckDBTestKit::driver( $engine, array(), $this->existing );
        $this->assertTrue( $db->isConnected() );
        $this->assertSame( 'READ_ONLY', $engine->lastMode() );
        $db->arrayQuery( 'SELECT 1 AS a' );
        $this->assertCount( 1, $engine->Opens );
        $this->assertTrue( $db->query( 'UPDATE ezsection SET name = \'x\'' ) );
        $this->assertSame( 'READ_WRITE', $engine->lastMode() );
        $this->assertSame( 'READ_WRITE', $db->openMode() );
        $this->assertSame( 1, $engine->Closes );
        $db->query( 'DELETE FROM ezsection WHERE id = 9' );
        $this->assertCount( 2, $engine->Opens, 'opened to write once' );

        // a file that is not there yet is made, so it is opened to write at once
        $fresh = new expDuckDBFakeEngine();
        expDuckDBTestKit::driver( $fresh, array(), expDuckDBTestKit::tempFile( 'fresh' ) );
        $this->assertSame( 'READ_WRITE', $fresh->lastMode() );

        // a write the classification missed is sent again after the reopen
        $missed = new expDuckDBFakeEngine();
        $db = expDuckDBTestKit::driver( $missed, array(), $this->existing );
        $missed->Answers = array( '/^SELECT nextval/' => array( 'error' => 'Invalid Input Error: Cannot execute statement on database which is attached in read-only mode!' ) );
        $db->arrayQuery( "SELECT nextval('s') AS n" );
        $this->assertSame( 'READ_WRITE', $missed->lastMode() );
    }

    /** DB-04 */
    public function testTransactionsWriteAndReadOnlyMode()
    {
        $engine = new expDuckDBFakeEngine();
        $db = expDuckDBTestKit::driver( $engine, array(), $this->existing );
        $db->begin();
        $this->assertSame( 'READ_WRITE', $engine->lastMode() );
        $this->assertSame( 'BEGIN TRANSACTION', end( $engine->Sent ) );
        $db->commit();
        $this->assertSame( 'COMMIT', end( $engine->Sent ) );

        $engine = new expDuckDBFakeEngine();
        $db = expDuckDBTestKit::driver( $engine, array( 'AccessMode' => 'read_only' ), $this->existing );
        $this->assertFalse( $db->query( 'INSERT INTO t VALUES (1)' ) );
        $this->assertSame( '25006', $db->errorNumber() );
        $this->assertCount( 1, $engine->Opens );

        $engine = new expDuckDBFakeEngine();
        expDuckDBTestKit::driver( $engine, array( 'AccessMode' => 'read_write' ), $this->existing );
        $this->assertSame( 'READ_WRITE', $engine->lastMode() );
    }

    /** DB-05 */
    public function testLockWait()
    {
        $engine = new expDuckDBFakeEngine();
        $engine->OpenErrors = array( 'IO Error: Could not set lock on file "x": Conflicting lock is held', 'IO Error: Could not set lock on file "x": Conflicting lock is held' );
        $db = expDuckDBTestKit::driver( $engine, array( 'LockWait' => 5 ), $this->existing );
        $this->assertTrue( $db->isConnected() );
        $this->assertCount( 3, $engine->Opens );

        $engine = new expDuckDBFakeEngine();
        $engine->OpenErrors = array_fill( 0, 50, 'IO Error: Could not set lock on file "x": Conflicting lock is held' );
        try
        {
            expDuckDBTestKit::driver( $engine, array( 'LockWait' => 0.05 ), $this->existing );
            $this->fail( 'no exception' );
        }
        catch ( eZDBNoConnectionException $e )
        {
            $this->assertStringContainsString( 'Error #55P03', $e->getMessage() );
        }
        $this->assertLessThan( 50, count( $engine->Opens ) );

        // an error that is not a lock is not waited for
        $engine = new expDuckDBFakeEngine();
        $engine->OpenErrors = array( 'IO Error: Cannot open file: Permission denied' );
        try
        {
            expDuckDBTestKit::driver( $engine, array( 'LockWait' => 5 ), $this->existing );
            $this->fail( 'no exception' );
        }
        catch ( eZDBNoConnectionException $e )
        {
        }
        $this->assertCount( 1, $engine->Opens );
    }

    /** DB-06 */
    public function testConnectionSettingsAreReplayed()
    {
        $engine = new expDuckDBFakeEngine();
        $db = expDuckDBTestKit::driver( $engine, array(), $this->existing );
        $this->assertSame( array( 'SET integer_division = true' ), $engine->Sent );
        $db->query( "SET TimeZone = 'UTC'" );
        $engine->Sent = array();
        $db->query( 'INSERT INTO t VALUES (1)' );
        $this->assertSame( array( 'SET integer_division = true', "SET TimeZone = 'UTC'", 'INSERT INTO t VALUES (1)' ), $engine->Sent );

        $engine = new expDuckDBFakeEngine();
        expDuckDBTestKit::driver( $engine, array( 'IntegerDivision' => 'disabled' ), $this->existing );
        $this->assertSame( array(), $engine->Sent );
    }

    /** DB-07 */
    public function testArrayQuery()
    {
        $engine = new expDuckDBFakeEngine();
        $engine->Answers = array( '/FROM ezsection/' => array( 'columns' => array( 'id', 'name' ),
                                                               'rows' => array( array( 'id' => '3', 'name' => 'A' ), array( 'id' => '4', 'name' => null ) ) ) );
        $db = expDuckDBTestKit::driver( $engine, array(), $this->existing );
        $rows = $db->arrayQuery( 'SELECT id, name FROM ezsection ORDER BY id', array( 'limit' => 2, 'offset' => 10 ) );
        $this->assertSame( "SELECT id, name FROM ezsection ORDER BY id\nLIMIT 2 OFFSET 10", end( $engine->Sent ) );
        $this->assertSame( array( 10, 11 ), array_keys( $rows ) );
        $this->assertSame( array( 'id' => '3', 'name' => 'A' ), $rows[10] );
        $this->assertSame( array( '3', '4' ), $db->arrayQuery( 'SELECT id, name FROM ezsection', array( 'column' => 'id' ) ) );
        $this->assertSame( array( 'A', null ), $db->arrayQuery( 'SELECT id, name FROM ezsection', array( 'column' => 1 ) ) );
        $this->assertSame( array(), $db->arrayQuery( 'SELECT id FROM ezsection', array( 'limit' => 0 ) ) );
    }

    /** DB-08 */
    public function testFailedStatement()
    {
        $engine = new expDuckDBFakeEngine();
        $engine->Answers = array( '/nope/' => array( 'error' => 'Catalog Error: Table with name nope does not exist!', 'type' => expDuckDBFFI::ERROR_CATALOG ) );
        $db = expDuckDBTestKit::driver( $engine, array(), $this->existing );
        $this->assertFalse( $db->arrayQuery( 'SELECT * FROM nope' ) );
        $this->assertSame( '42P01', $db->errorNumber() );
        $this->assertStringContainsString( 'nope does not exist', $db->errorMessage() );

        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        try
        {
            $db->query( 'DELETE FROM nope' );
            $this->fail( 'no exception' );
        }
        catch ( eZDBException $e )
        {
            $this->assertSame( '42P01', $e->getCode() );
        }
    }

    /** DB-09 */
    public function testLocks()
    {
        $engine = new expDuckDBFakeEngine();
        $db = expDuckDBTestKit::driver( $engine, array( 'AccessMode' => 'read_write' ), $this->existing );
        $engine->Sent = array();
        $db->arrayQuery( 'SELECT * FROM ezcontentobject WHERE id = 1 FOR UPDATE' );
        $this->assertTrue( $db->query( 'LOCK TABLE ezcontentobject' ) );
        $this->assertSame( array( 'SELECT * FROM ezcontentobject WHERE id = 1' ), $engine->Sent );
        $db->lock( 'ezcontentobject' );
        $db->unlock();
        $this->assertSame( array( 'SELECT * FROM ezcontentobject WHERE id = 1', 'BEGIN TRANSACTION', 'COMMIT' ), $engine->Sent );
    }

    /** DB-10 */
    public function testSerialAndExpressions()
    {
        $engine = new expDuckDBFakeEngine();
        $engine->Answers = array( '/currval/' => array( 'columns' => array( 'id' ), 'rows' => array( array( 'id' => '42' ) ) ) );
        $db = expDuckDBTestKit::driver( $engine, array(), $this->existing );
        $this->assertSame( 42, $db->lastSerialID( 'ezsection', 'id' ) );
        $this->assertSame( "SELECT currval( 'ezsection_id_seq' ) AS id", end( $engine->Sent ) );
        $this->assertFalse( $db->lastSerialID( false ) );
        $this->assertSame( ' SUBSTRING( name FROM 2 FOR 3 ) ', $db->subString( 'name', 2, 3 ) );
        $this->assertSame( ' SUBSTRING( name FROM 2 ) ', $db->subString( 'name', 2 ) );
        $this->assertSame( " a || 'b' ", $db->concatString( array( 'a', "'b'" ) ) );
        $this->assertSame( ' MD5( name ) ', $db->md5( 'name' ) );
        $this->assertSame( '(a & 2 ) ', $db->bitAnd( 'a', 2 ) );
        $this->assertSame( "it''s", $db->escapeString( "it's" ) );
        $this->assertSame( "'it''s'", $db->sqlLiteral( "it's" ) );
        $this->assertSame( 'SELECT 1', $db->sqlForUpdate( 'SELECT 1' ) );
    }

    /** DB-11 */
    public function testNamesCatalogAndClose()
    {
        $engine = new expDuckDBFakeEngine();
        $engine->Answers = array( '/duckdb_tables/' => array( 'columns' => array( 'name' ), 'rows' => array( array( 'name' => 'ezsection' ), array( 'name' => 'other' ) ) ),
                                  '/version\(\)/' => array( 'columns' => array( 'v' ), 'rows' => array( array( 'v' => 'v2.0.1' ) ) ) );
        $db = expDuckDBTestKit::driver( $engine, array(), $this->existing );
        $this->assertSame( 'duckdb', $db->databaseName() );
        $this->assertSame( 'duckdb', $db->sqlHelperDialect() );
        $this->assertSame( array( 'ezsection', 'other' ), $db->relationList() );
        $this->assertSame( array( 'ezsection' => eZDBInterface::RELATION_TABLE ), $db->eZTableList() );
        $this->assertStringContainsString( 'duckdb_sequences()', expDuckDBDB::relationListQuery( eZDBInterface::RELATION_SEQUENCE ) );
        $this->assertStringNotContainsString( 'temporary', expDuckDBDB::relationListQuery( eZDBInterface::RELATION_INDEX ) );
        $this->assertNull( expDuckDBDB::relationListQuery( eZDBInterface::RELATION_TRIGGER ) );
        $this->assertSame( '2.0.1', $db->databaseServerVersion()['string'] );
        $this->assertSame( array( '2', '0', '1' ), $db->databaseClientVersion()['values'] );
        $this->assertTrue( $db->isCharsetSupported( 'utf-8' ) );

        $db->begin();
        $engine->Sent = array();
        $db->close();
        $this->assertSame( array( 'ROLLBACK' ), $engine->Sent );
        $this->assertFalse( $db->isConnected() );
        $this->assertNull( $db->openMode() );
        $this->assertFalse( $db->query( 'SELECT 1' ) );
    }

    /** DB-12 */
    public function testCorrectSequenceValues()
    {
        $columns = array( 'columns' => array( 'table_name', 'column_name', 'column_default' ),
                          'rows' => array( array( 'table_name' => 'ezsection', 'column_name' => 'id', 'column_default' => "nextval(CAST('ezsection_id_seq' AS VARCHAR))" ),
                                           array( 'table_name' => 'ezx', 'column_name' => 'id', 'column_default' => "nextval('ezx_id_seq')" ) ) );
        $max = array( 'columns' => array( 'm' ), 'rows' => array( array( 'm' => '7' ) ) );

        $engine = new expDuckDBFakeEngine();
        $engine->Answers = array( '/duckdb_columns/' => $columns, '/function_name = .setval/' => array( 'columns' => array( 'x' ), 'rows' => array( array( 'x' => '1' ) ) ),
                                  '/^SELECT MAX\(id\)/' => $max );
        $db = expDuckDBTestKit::driver( $engine, array( 'AccessMode' => 'read_write' ), $this->existing );
        $this->assertTrue( $db->correctSequenceValues() );
        $this->assertContains( "SELECT setval( 'ezsection_id_seq', 7 )", $engine->Sent );
        $this->assertContains( "SELECT setval( 'ezx_id_seq', 7 )", $engine->Sent );

        $engine = new expDuckDBFakeEngine();
        $engine->Answers = array( '/duckdb_columns/' => $columns, '/^SELECT MAX\(id\)/' => $max,
                                  '/last_value/' => array( 'columns' => array( 'last_value' ), 'rows' => array( array( 'last_value' => '3' ) ) ) );
        $db = expDuckDBTestKit::driver( $engine, array( 'AccessMode' => 'read_write' ), $this->existing );
        $this->assertTrue( $db->correctSequenceValues() );
        $this->assertContains( "SELECT MAX( nextval( 'ezsection_id_seq' ) ) FROM range( 4 )", $engine->Sent );
    }

    /** DB-13 */
    public function testReleaseAtTheEndOfARequest()
    {
        $engine = new expDuckDBFakeEngine();
        $db = expDuckDBTestKit::driver( $engine, array(), $this->existing );
        $db->query( 'INSERT INTO t VALUES (1)' );
        $this->assertSame( 'READ_WRITE', $db->openMode() );
        $db->release();
        $this->assertNull( $db->openMode() );
        $this->assertFalse( $db->isConnected() );
        $closes = $engine->Closes;
        // a statement after the release (a later cleanup handler) opens the file again, read-only first
        $db->arrayQuery( 'SELECT 1 AS a' );
        $this->assertSame( 'READ_ONLY', $db->openMode() );
        $this->assertSame( 'READ_ONLY', $engine->lastMode() );
        $db->release();
        $this->assertSame( $closes + 1, $engine->Closes );
        // close() is final
        $db->arrayQuery( 'SELECT 1 AS a' );
        $db->close();
        $this->assertFalse( $db->query( 'SELECT 1' ) );
        $this->assertFalse( expDuckDBDB::isWebProcess() );
    }

    /** DB-14 */
    public function testTheKernelAsksTheDriverNotItsName()
    {
        $engine = new expDuckDBFakeEngine();
        $engine->Answers = array( "/information_schema\.tables WHERE table_schema = current_schema\(\) AND table_name = 'expmail_preference'/"
                                      => array( 'columns' => array( 'c' ), 'rows' => array( array( 'c' => 1 ) ) ),
                                  "/information_schema\.tables/" => array( 'columns' => array( 'c' ), 'rows' => array( array( 'c' => 0 ) ) ) );
        $db = expDuckDBTestKit::driver( $engine, array(), $this->existing );

        // a refused unique key: the SQLSTATE the driver gives, or DuckDB's message
        $this->assertTrue( $db->isUniqueViolation( '23505', '' ) );
        $this->assertTrue( $db->isUniqueViolation( 0, 'Constraint Error: Duplicate key "login: anna" violates unique constraint' ) );
        $this->assertFalse( $db->isUniqueViolation( '42P01', 'Catalog Error: Table with name ezuser does not exist!' ) );
        $this->assertTrue( expUserLoginIntegrity::isDuplicateKeyError( $db, '23505', 'duplicate key' ) );
        $this->assertFalse( expUserLoginIntegrity::isDuplicateKeyError( $db, '42P01', 'no such table' ) );

        // a table of the current schema, from information_schema
        $this->assertTrue( $db->tableExistsInSchema( 'expmail_preference' ) );
        $this->assertFalse( $db->tableExistsInSchema( "nothing'here" ) );
        $this->assertStringContainsString( "table_name = 'nothing''here'", end( $engine->Sent ) );

        // PostgreSQL's UPDATE ... FROM
        $this->assertTrue( $db->supportsUpdateFrom() );

        // every other driver leaves the answer to the caller, as before
        $this->assertNull( ( new ReflectionMethod( 'eZDBInterface', 'isUniqueViolation' ) )->invoke( $db, '23505', '' ) );
        $this->assertNull( ( new ReflectionMethod( 'eZDBInterface', 'tableExistsInSchema' ) )->invoke( $db, 'ezuser' ) );
        $this->assertFalse( ( new ReflectionMethod( 'eZDBInterface', 'supportsUpdateFrom' ) )->invoke( $db ) );

        // kernel core does not branch on DuckDB's name in these places any more
        $root = dirname( __DIR__, 5 );
        foreach ( array( 'kernel/classes/expuserloginintegrity.php', 'kernel/classes/ezcontentclassattribute.php',
                         'kernel/classes/mailpreferences/expmailpreferencesservice.php' ) as $file )
            $this->assertDoesNotMatchRegularExpression( "/'duckdb'/i", file_get_contents( $root . '/' . $file ), $file );
    }
}
