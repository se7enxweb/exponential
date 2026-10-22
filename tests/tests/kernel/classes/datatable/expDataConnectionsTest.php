<?php
/**
 * The named database connections of the data tables (expDataConnections, expDataConnection;
 * doc/eng-us/features/6.0/data-tables.md, "Connections"). No installation: the pure functions, and SQLite files
 * under var/datatable/ of the installation (the allowed directory of datatable.ini), removed afterwards.
 *
 *  DC-01 - normalize(): every key typed, engine aliases (mariadb, mysqli, pgsql, mongo, mssql), the port kept in
 *          0..65535, unknown options dropped, an unknown origin is admin
 *  DC-02 - problems(): the identifier's rules, "site" reserved, server engines need a server and a database, a DSN
 *          holding a password and a secret name that is not one refused
 *  DC-03 - problems() of SQLite: only .db/.sqlite/.sqlite3, only in SQLiteDirectories, no path out of them
 *  DC-04 - dsn() for every PDO driver
 *  DC-05 - plan(): SQLite through the kernel's driver with the absolute file; SQL Server through expPDODB with its
 *          dialect; an unknown engine and a missing secret are errors naming the secret, never a value
 *  DC-06 - engines(): every engine with the keys the pages read; SQL Server marked untested
 *  DC-07 - connectionOf() / classHasConnection(): the definition's connection, "site" as the site's own, the class
 *          map of the settings
 *  DC-08 - declared(): [Connection_<id>] blocks of datatable.ini, read-only (origin settings), bad identifiers and
 *          "site" left out
 *  DC-09 - db(): one instance per connection and request, the same one when asked again; "site" and "" are the
 *          site's own database; an unknown connection throws
 *  DC-10 - test(): a SQLite connection reports ok, the engine, the server's version and its tables; a connection
 *          whose secret is missing fails naming the secret
 *  DC-11 - get() and all(): the site's database first, then the test connections
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expDataTableTestLoader.php';

class expDataConnectionsTest extends PHPUnit\Framework\TestCase
{
    /** @var string[] files to remove */
    private $files = array();

    protected function setUp(): void
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The SQLite3 extension is not loaded' );
    }

    protected function tearDown(): void
    {
        expDataConnections::setConnections( null );
        expDataConnections::setClassMap( null );
        foreach ( $this->files as $file )
        {
            foreach ( array( '', '-wal', '-shm', '-journal' ) as $suffix )
                if ( is_file( $file . $suffix ) )
                    unlink( $file . $suffix );
        }
    }

    /** @return string a SQLite file of the test, relative to the installation */
    private function sqliteFile()
    {
        $relative = 'var/datatable/t691-dc-' . getmypid() . '-' . count( $this->files ) . '.sqlite';
        $this->files[] = eZSys::rootDir() . '/' . $relative;
        return $relative;
    }

    /**
     * A directory as eZINI wants it: relative to the root eZINI reads from (the checkout its class was loaded from,
     * which may be another one than this test's).
     *
     * @param string $dir absolute
     * @return string
     */
    private static function iniDir( $dir )
    {
        $method = new ReflectionMethod( 'eZINI', 'installationRoot' );
        $from = explode( '/', trim( (string)realpath( $method->invoke( null ) ), '/' ) );
        $to = explode( '/', trim( (string)realpath( $dir ), '/' ) );
        while ( $from && $to && $from[0] === $to[0] )
        {
            array_shift( $from );
            array_shift( $to );
        }
        return implode( '/', array_merge( array_fill( 0, count( $from ), '..' ), $to ) );
    }

    /** DC-01 */
    public function testNormalize()
    {
        $c = expDataConnections::normalize( array( 'identifier' => ' Archive ', 'engine' => 'MariaDB', 'port' => '99999',
                                                    'options' => array( 'charset' => 'utf8mb4', 'evil' => 'x', 'dsn' => '' ),
                                                    'origin' => 'hacker', 'read_only' => '1' ) );
        $this->assertSame( 'archive', $c['identifier'] );
        $this->assertSame( 'mysql', $c['engine'] );
        $this->assertSame( 65535, $c['port'] );
        $this->assertSame( array( 'charset' => 'utf8mb4' ), $c['options'] );
        $this->assertSame( 'admin', $c['origin'] );
        $this->assertTrue( $c['read_only'] );
        foreach ( array( 'mysqli' => 'mysql', 'pgsql' => 'postgresql', 'mongo' => 'mongodb', 'mssql' => 'sqlserver', 'sqlite' => 'sqlite' ) as $alias => $engine )
            $this->assertSame( $engine, expDataConnections::normalize( array( 'engine' => $alias ) )['engine'], $alias );
        $this->assertSame( 0, expDataConnections::normalize( array( 'port' => '-5' ) )['port'] );
        $this->assertSame( 0, expDataConnections::normalize( array( 'port' => 'abc' ) )['port'] );
        $keys = array( 'identifier', 'name', 'engine', 'server', 'port', 'database', 'user', 'secret', 'options', 'description',
                       'read_only', 'origin', 'id', 'modified' );
        $this->assertSame( $keys, array_keys( expDataConnections::normalize( array() ) ) );
    }

    /** DC-02 */
    public function testProblems()
    {
        expDataConnections::setConnections( array() );
        $ok = array( 'identifier' => 'archive', 'engine' => 'mysql', 'server' => 'db.example.com', 'database' => 'archive', 'secret' => 'env:ARCHIVE_PW' );
        $this->assertSame( array(), expDataConnections::problems( $ok ) );
        // (an identifier longer than 64 is cut to 64 by normalize(); the form's maxlength keeps it there)
        foreach ( array( '1abc', 'Ab', '', 'a-b', '_a' ) as $bad )
        {
            $c = expDataConnections::normalize( array( 'identifier' => $bad ) + $ok );
            // normalize lower-cases: "Ab" becomes "ab", which is fine
            if ( $c['identifier'] === 'ab' )
                continue;
            $this->assertNotEmpty( expDataConnections::problems( $c ), "identifier '$bad'" );
        }
        $this->assertStringContainsString( 'site', implode( ' ', expDataConnections::problems( array( 'identifier' => 'site' ) + $ok ) ) );
        $this->assertNotEmpty( expDataConnections::problems( array( 'server' => '' ) + $ok ) );
        $this->assertNotEmpty( expDataConnections::problems( array( 'database' => '' ) + $ok ) );
        $this->assertSame( array(), expDataConnections::problems( array( 'engine' => 'oracle', 'database' => '' ) + $ok ), 'Oracle needs no database name' );
        $this->assertNotEmpty( expDataConnections::problems( array( 'engine' => 'nosuch' ) + $ok ) );
        $this->assertNotEmpty( expDataConnections::problems( array( 'server' => 'db example' ) + $ok ) );
        $this->assertNotEmpty( expDataConnections::problems( array( 'options' => array( 'dsn' => 'mysql:host=x;password=y' ) ) + $ok ) );
        $this->assertNotEmpty( expDataConnections::problems( array( 'options' => array( 'dsn' => 'odbc:Driver=x;PWD = y' ) ) + $ok ) );
        $this->assertNotEmpty( expDataConnections::problems( array( 'secret' => 'not a name!' ) + $ok ) );
        $this->assertNotEmpty( expDataConnections::problems( array( 'secret' => 'env:' ) + $ok ) );
    }

    /** DC-03 */
    public function testSqliteFiles()
    {
        expDataConnections::setConnections( array() );
        $sqlite = array( 'identifier' => 'local', 'engine' => 'sqlite' );
        foreach ( array( 'var/datatable/a.db', 'var/datatable/b.sqlite', 'var/datatable/c.SQLITE3' ) as $file )
            $this->assertSame( array(), expDataConnections::problems( array( 'database' => $file ) + $sqlite ), $file );
        foreach ( array( 'var/datatable/a.txt', 'var/datatable/a.php', 'var/a.db', 'var/datatable/../a.db', 'var/datatable/../../settings/a.db',
                         '/etc/passwd.db', 'var/datatable/nosuchdir/a.db', '' ) as $file )
            $this->assertNotEmpty( expDataConnections::problems( array( 'database' => $file ) + $sqlite ), "file '$file'" );
        $this->assertSame( eZSys::rootDir() . '/var/datatable/a.db', expDataConnections::sqlitePath( 'var/datatable/a.db' ) );
        $this->assertNull( expDataConnections::sqlitePath( "var/datatable/a\0.db" ) );
        $this->assertSame( array( 'var/datatable' ), expDataConnections::sqliteDirectories() );
    }

    /** DC-04 */
    public function testDsn()
    {
        $this->assertSame( 'sqlite:/x/a.db', expDataConnections::dsn( 'sqlite', 'sqlite', '', 0, '/x/a.db' ) );
        $this->assertSame( 'mysql:host=h;port=3307;dbname=d;charset=utf8mb4', expDataConnections::dsn( 'mysql', 'mysql', 'h', 3307, 'd' ) );
        $this->assertSame( 'mysql:host=h;dbname=d;charset=utf8mb4', expDataConnections::dsn( 'mysql', 'mysql', 'h', 0, 'd' ) );
        $this->assertSame( 'pgsql:host=h;port=5432;dbname=d', expDataConnections::dsn( 'postgresql', 'pgsql', 'h', 5432, 'd' ) );
        $this->assertSame( 'oci:dbname=//h:1521/ORCL;charset=AL32UTF8', expDataConnections::dsn( 'oracle', 'oci', 'h', 1521, 'ORCL' ) );
        $this->assertSame( 'sqlsrv:Server=h,1433;Database=d', expDataConnections::dsn( 'sqlserver', 'sqlsrv', 'h', 1433, 'd' ) );
        $this->assertSame( 'dblib:host=h:1433;dbname=d;charset=UTF-8', expDataConnections::dsn( 'sqlserver', 'dblib', 'h', 1433, 'd' ) );
        $this->assertSame( 'odbc:Driver={ODBC Driver 18 for SQL Server};Server=h,1433;Database=d', expDataConnections::dsn( 'sqlserver', 'odbc', 'h', 1433, 'd' ) );
        $this->assertSame( 'odbc:Driver={FreeTDS};Server=h;Database=d', expDataConnections::dsn( 'sqlserver', 'odbc', 'h', 0, 'd', array( 'odbc_driver' => 'FreeTDS' ) ) );
        $this->assertSame( '', expDataConnections::dsn( 'x', 'nosuch', 'h', 0, 'd' ) );
    }

    /** DC-05 */
    public function testPlan()
    {
        $file = $this->sqliteFile();
        $plan = expDataConnections::plan( array( 'identifier' => 'local', 'engine' => 'sqlite', 'database' => $file ) );
        $this->assertSame( '', $plan['error'] );
        $this->assertSame( 'ez', $plan['via'] );
        $this->assertSame( 'sqlite3', $plan['implementation'] );
        $this->assertSame( eZSys::rootDir() . '/' . $file, $plan['parameters']['database'] );
        $this->assertFalse( $plan['parameters']['use_defaults'] );

        $plan = expDataConnections::plan( array( 'identifier' => 'x', 'engine' => 'nosuch' ) );
        $this->assertStringContainsString( 'nosuch', $plan['error'] );

        putenv( 'T691_DC_MISSING' );
        $plan = expDataConnections::plan( array( 'identifier' => 'x', 'engine' => 'sqlite', 'database' => $file, 'secret' => 'env:T691_DC_MISSING' ) );
        $this->assertStringContainsString( 'env:T691_DC_MISSING', $plan['error'] );

        putenv( 'T691_DC_PW=s3cr3t-value' );
        $plan = expDataConnections::plan( array( 'identifier' => 'x', 'engine' => 'sqlite', 'database' => $file, 'secret' => 'env:T691_DC_PW' ) );
        $this->assertSame( 's3cr3t-value', $plan['parameters']['password'] );
        putenv( 'T691_DC_PW' );

        $engines = expDataConnections::engines();
        if ( !$engines['sqlserver']['available'] )
        {
            $plan = expDataConnections::plan( array( 'identifier' => 'x', 'engine' => 'sqlserver', 'server' => 'h', 'database' => 'd' ) );
            $this->assertStringContainsString( 'no driver', $plan['error'] );
        }
        else
        {
            $plan = expDataConnections::plan( array( 'identifier' => 'x', 'engine' => 'sqlserver', 'server' => 'h', 'database' => 'd' ) );
            $this->assertSame( 'pdo', $plan['via'] );
            $this->assertSame( 'sqlserver', $plan['parameters']['pdo_dialect'] );
            $this->assertNotSame( '', $plan['parameters']['pdo_dsn'] );
        }
        // a test password is used instead of the secret, and is not looked up
        $plan = expDataConnections::plan( array( 'identifier' => 'x', 'engine' => 'sqlite', 'database' => $file, 'secret' => 'env:T691_DC_MISSING' ), 'typed' );
        $this->assertSame( '', $plan['error'] );
        $this->assertSame( 'typed', $plan['parameters']['password'] );
    }

    /** DC-06 */
    public function testEngines()
    {
        $engines = expDataConnections::engines();
        $this->assertSame( array( 'sqlite', 'mysql', 'postgresql', 'oracle', 'sqlserver', 'mongodb' ), array_keys( $engines ) );
        foreach ( $engines as $key => $engine )
        {
            foreach ( array( 'label', 'kind', 'ez', 'pdo', 'port', 'file', 'enabled', 'available', 'via', 'handler', 'pdo_driver', 'why', 'untested' ) as $field )
                $this->assertArrayHasKey( $field, $engine, "$key.$field" );
            $this->assertSame( $engine['why'] === '', $engine['available'], $key );
        }
        $this->assertTrue( $engines['sqlserver']['untested'] );
        $this->assertFalse( $engines['mysql']['untested'] );
        $this->assertTrue( $engines['sqlite']['file'] );
        $this->assertSame( 'document', $engines['mongodb']['kind'] );
        $this->assertTrue( $engines['sqlite']['available'], 'SQLite is available here' );
        $this->assertSame( 'ez', $engines['sqlite']['via'] );
    }

    /** DC-07 */
    public function testConnectionOf()
    {
        expDataConnections::setClassMap( array( 'MyMapped' => 'archive', 'MySite' => '' ) );
        $this->assertSame( 'archive', expDataConnections::connectionOf( array( 'connection' => 'archive' ) ) );
        $this->assertSame( '', expDataConnections::connectionOf( array( 'connection' => 'site' ) ) );
        $this->assertSame( '', expDataConnections::connectionOf( array( 'connection' => '' ) ) );
        $this->assertSame( 'archive', expDataConnections::connectionOf( array( 'class_name' => 'MyMapped' ) ) );
        $this->assertSame( '', expDataConnections::connectionOf( array( 'class_name' => 'Other' ) ) );
        $this->assertSame( 'other', expDataConnections::connectionOf( array( 'class_name' => 'MyMapped', 'connection' => 'other' ) ), 'the definition wins' );
        $this->assertTrue( expDataConnections::classHasConnection( 'MyMapped' ) );
        $this->assertFalse( expDataConnections::classHasConnection( 'MySite' ) );
        $this->assertFalse( expDataConnections::classHasConnection( 'Other' ) );
        $this->assertSame( '', expDataConnections::connectionOf( array() ) );
    }

    /** DC-08 */
    public function testDeclared()
    {
        $dir = eZSys::rootDir() . '/var/tmp/t691-dc-ini-' . getmypid();
        if ( !is_dir( $dir ) )
            mkdir( $dir, 0775, true );
        $file = $dir . '/t691datatable.ini';
        file_put_contents( $file, "[Connection_archive]\nName=Archive\nEngine=MariaDB\nServer=db2.example.com\nPort=3307\nDatabase=archive\nUser=arch\nSecret=env:ARCHIVE_PW\nCharset=utf8mb4\nReadOnly=true\n\n"
                                . "[Connection_site]\nEngine=mysql\n\n[Connection_Bad-Id]\nEngine=mysql\n\n[Other]\nX=1\n" );
        try
        {
            $ini = new eZINI( 't691datatable.ini', self::iniDir( $dir ), null, false, null, true );
            $declared = expDataConnections::declared( $ini );
        }
        finally
        {
            unlink( $file );
            rmdir( $dir );
        }
        $this->assertSame( array( 'archive' ), array_keys( $declared ) );
        $a = $declared['archive'];
        $this->assertSame( 'settings', $a['origin'] );
        $this->assertSame( 'mysql', $a['engine'] );
        $this->assertSame( 3307, $a['port'] );
        $this->assertSame( 'env:ARCHIVE_PW', $a['secret'] );
        $this->assertSame( array( 'charset' => 'utf8mb4' ), $a['options'] );
        $this->assertTrue( $a['read_only'] );
        $this->assertSame( 'Archive', $a['name'] );
    }

    /** DC-09 */
    public function testDbOncePerRequest()
    {
        $file = $this->sqliteFile();
        expDataConnections::setConnections( array( 'local' => array( 'engine' => 'sqlite', 'database' => $file ) ) );
        $db = expDataConnections::db( 'local' );
        $this->assertInstanceOf( 'eZSQLite3DB', $db );
        $this->assertTrue( $db->isConnected() );
        $this->assertSame( $db, expDataConnections::db( 'local' ) );
        $this->assertFileExists( eZSys::rootDir() . '/' . $file );
        try
        {
            expDataConnections::db( 'nosuch' );
            $this->fail( 'an unknown connection throws' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertStringContainsString( 'nosuch', $e->getMessage() );
        }
        $this->assertNotSame( $db, eZDB::hasInstance() ? $GLOBALS['eZDBGlobalInstance'] : null, 'not the site\'s global instance' );
    }

    /** DC-10 */
    public function testTest()
    {
        $file = $this->sqliteFile();
        expDataConnections::setConnections( array( 'local' => array( 'engine' => 'sqlite', 'database' => $file ) ) );
        $db = expDataConnections::db( 'local' );
        $db->query( 'CREATE TABLE t691_a ( id INTEGER PRIMARY KEY )' );
        $db->query( 'CREATE TABLE t691_b ( id INTEGER PRIMARY KEY )' );
        $result = expDataConnections::test( 'local' );
        $this->assertTrue( $result['ok'], $result['error'] );
        $this->assertSame( 'sqlite', $result['engine'] );
        $this->assertSame( 'ez', $result['via'] );
        $this->assertSame( 2, $result['tables'] );
        $this->assertNotSame( '', $result['version'] );
        $this->assertSame( '', $result['error'] );
        $this->assertIsInt( $result['ms'] );

        putenv( 'T691_DC_NOPE' );
        $result = expDataConnections::test( array( 'identifier' => 'x', 'engine' => 'sqlite', 'database' => $file, 'secret' => 'env:T691_DC_NOPE' ) );
        $this->assertFalse( $result['ok'] );
        $this->assertStringContainsString( 'env:T691_DC_NOPE', $result['error'] );

        $result = expDataConnections::test( 'nosuch' );
        $this->assertFalse( $result['ok'] );
    }

    /** DC-11 */
    public function testGetAndAll()
    {
        expDataConnections::setConnections( array( 'local' => array( 'engine' => 'sqlite', 'database' => 'var/datatable/x.db' ) ) );
        $all = expDataConnections::all();
        $this->assertSame( array( 'site', 'local' ), array_keys( $all ) );
        $this->assertSame( 'site', $all['site']['origin'] );
        $this->assertSame( 'site', expDataConnections::get( '' )['identifier'] );
        $this->assertSame( 'local', expDataConnections::get( 'local' )['identifier'] );
        $this->assertNull( expDataConnections::get( 'nosuch' ) );
    }
}
