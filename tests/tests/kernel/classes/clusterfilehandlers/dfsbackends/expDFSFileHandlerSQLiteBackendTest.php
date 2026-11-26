<?php
/**
 * The SQLite DFS cluster backend without a database: the result codes it retries and _protect() around a busy
 * BEGIN IMMEDIATE, the database file of DBName, LIKE and wildcard patterns as GLOB patterns, the method for method
 * parity with the MySQLi backend, the schema file and the gateway. The parts that need a database are in
 * expDFSSQLiteBackendIntegrationTest.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expDFSFileHandlerSQLiteBackendTest extends ezpTestCase
{
    /** @return string the root of the installation */
    private static function root()
    {
        return dirname( __DIR__, 6 );
    }

    public function testRetryableCodes()
    {
        // SQLITE_BUSY, SQLITE_LOCKED and their extended codes (BUSY_RECOVERY, BUSY_SNAPSHOT, LOCKED_SHAREDCACHE)
        foreach ( array( '5', 5, '6', '261', '517', '262' ) as $code )
            $this->assertTrue( expDFSFileHandlerSQLiteBackend::isRetryableCode( $code ), var_export( $code, true ) );
        // an error, a constraint, a read-only database, PostgreSQL's SQLSTATE and nothing are not
        foreach ( array( '1', '19', '8', '55P03', 'HY000', '', null ) as $code )
            $this->assertFalse( expDFSFileHandlerSQLiteBackend::isRetryableCode( $code ), var_export( $code, true ) );
    }

    public function testErrorCodeOfAnException()
    {
        $e = new PDOException( 'database is locked' );
        $e->errorInfo = array( 'HY000', 5, 'database is locked' );
        $this->assertSame( '5', expDFSFileHandlerSQLiteBackend::errorCodeOf( $e ) );
        // a failed connection: the code is in the message
        $this->assertSame( '14', expDFSFileHandlerSQLiteBackend::errorCodeOf( new PDOException( 'SQLSTATE[HY000] [14] unable to open database file' ) ) );
        $this->assertSame( '', expDFSFileHandlerSQLiteBackend::errorCodeOf( new RuntimeException( 'x', 5 ) ) );
        $this->assertTrue( expDFSFileHandlerSQLiteBackend::isMissingTable( '1', 'no such table: ezdfsfile' ) );
        $this->assertFalse( expDFSFileHandlerSQLiteBackend::isMissingTable( '1', 'near "x": syntax error' ) );
    }

    public function testDatabaseFile()
    {
        $this->assertSame( '/srv/dfs/cluster.db', expDFSFileHandlerSQLiteBackend::filePath( '/srv/dfs/cluster.db' ) );
        $this->assertSame( 'var/storage/sqlite3/cluster.db', expDFSFileHandlerSQLiteBackend::filePath( 'cluster.db' ) );
        // no database for processes to share
        $this->assertSame( '', expDFSFileHandlerSQLiteBackend::filePath( '' ) );
        $this->assertSame( '', expDFSFileHandlerSQLiteBackend::filePath( ':memory:' ) );
        $this->assertSame( '/srv/x.db', expDfsSQLiteClusterGatewayFile::of( '/srv/x.db', '/var/www/site' ) );
        $this->assertSame( '/var/www/site/var/storage/sqlite3/x.db', expDfsSQLiteClusterGatewayFile::of( 'x.db', '/var/www/site/' ) );
        $this->assertSame( '', expDfsSQLiteClusterGatewayFile::of( ':memory:', '/var/www/site' ) );
    }

    public function testLikeToGlob()
    {
        // % is any text, _ a literal (as in the MySQLi backend), GLOB's own wildcards are literals
        $this->assertSame( 'var/site/cache/a_b*', expDFSFileHandlerSQLiteBackend::likeToGlob( 'var/site/cache/a_b%' ) );
        $this->assertSame( 'var/[*]x[?]y[[]1]*', expDFSFileHandlerSQLiteBackend::likeToGlob( 'var/*x?y[1]%' ) );
        // the escape of the callers (addcslashes( $like, '_' )) makes the next character a literal
        $this->assertSame( 'a_b%c', expDFSFileHandlerSQLiteBackend::likeToGlob( 'a\\_b\\%c' ) );
        $this->assertSame( 'var/100%_x*', expDFSFileHandlerSQLiteBackend::globPrefix( 'var/100%_x' ) );
        $this->assertSame( 'var/[*][[]*', expDFSFileHandlerSQLiteBackend::globPrefix( 'var/*[' ) );
    }

    public function testWildcardToGlobs()
    {
        $this->assertSame( array( 'var/a.txt', 'var/b.txt' ), expDFSFileHandlerSQLiteBackend::wildcardToGlobs( 'var/{a,b}.txt' ) );
        $this->assertSame( array( 'var/?.*' ), expDFSFileHandlerSQLiteBackend::wildcardToGlobs( 'var/?.*' ) );
        // nested braces, a comma outside braces, an unclosed brace, a literal [
        $this->assertSame( array( 'x/a1', 'x/a2', 'x/b' ), expDFSFileHandlerSQLiteBackend::wildcardToGlobs( 'x/{a{1,2},b}' ) );
        $this->assertSame( array( 'a,b[[]1]' ), expDFSFileHandlerSQLiteBackend::wildcardToGlobs( 'a,b[1]' ) );
        $this->assertSame( array( 'x', 'y' ), expDFSFileHandlerSQLiteBackend::wildcardToGlobs( '{x,y' ) );
        $this->assertSame( array( 'ab', 'b' ), expDFSFileHandlerSQLiteBackend::wildcardToGlobs( '{a,}b' ) );
        $this->expectException( InvalidArgumentException::class );
        expDFSFileHandlerSQLiteBackend::wildcardToGlobs( str_repeat( '{a,b,c}', 6 ) );
    }

    public function testNameTrunkOfViewCache()
    {
        $this->assertSame( 'var/site/cache/content/site/1/2-',
            expDFSFileHandlerSQLiteBackend::nameTrunk( 'var/site/cache/content/site/1/2-abcdef.cache', 'viewcache' ) );
        $this->assertSame( 'var/storage/a.png', expDFSFileHandlerSQLiteBackend::nameTrunk( 'var/storage/a.png', 'image' ) );
    }

    public function testConnectionOptionsOpenWithoutCreate()
    {
        $options = expDFSFileHandlerSQLiteBackend::connectionOptions( 3 );
        $this->assertSame( PDO::ERRMODE_EXCEPTION, $options[PDO::ATTR_ERRMODE] );
        $this->assertSame( 3, $options[PDO::ATTR_TIMEOUT] );
        $this->assertCount( 4, $options, 'the open flags are set' );
        $this->assertNotContains( 6, $options, 'READWRITE | CREATE is not asked for' );
    }

    /**
     * Every public method of the MySQLi backend exists in the SQLite backend, with as many parameters and the same
     * number of required ones: eZDFSFileHandler can use either.
     */
    public function testMethodParityWithTheMySQLiBackend()
    {
        $mysql = new ReflectionClass( 'eZDFSFileHandlerMySQLiBackend' );
        $sqlite = new ReflectionClass( 'expDFSFileHandlerSQLiteBackend' );
        foreach ( $mysql->getMethods( ReflectionMethod::IS_PUBLIC ) as $method )
        {
            $name = $method->getName();
            $this->assertTrue( $sqlite->hasMethod( $name ), "missing $name" );
            $other = $sqlite->getMethod( $name );
            $this->assertTrue( $other->isPublic(), "$name is not public" );
            $this->assertGreaterThanOrEqual( $method->getNumberOfParameters(), $other->getNumberOfParameters(), $name );
            $this->assertSame( $method->getNumberOfRequiredParameters(), $other->getNumberOfRequiredParameters(), $name );
        }
        foreach ( array( 'eZClusterEventNotifier', 'expDFSFileHandlerDBBackendInterface' ) as $interface )
            $this->assertTrue( $sqlite->implementsInterface( $interface ), $interface );
    }

    public function testNoServerSqlInTheCode()
    {
        // the code only: the comments compare with the server engines on purpose
        $source = '';
        foreach ( token_get_all( file_get_contents( self::root() . '/kernel/private/classes/clusterfilehandlers/dfsbackends/sqlite.php' ) ) as $token )
        {
            if ( !is_array( $token ) || !in_array( $token[0], array( T_COMMENT, T_DOC_COMMENT ), true ) )
                $source .= is_array( $token ) ? $token[1] : $token;
        }
        foreach ( array( 'FOR UPDATE', ' LIKE ', 'SKIP LOCKED', 'SET lock_timeout', '~ :', 'pgsql:', 'mysqli_' ) as $server )
            $this->assertStringNotContainsString( $server, $source );
        $this->assertStringContainsString( 'BEGIN IMMEDIATE', $source );
        $this->assertStringContainsString( 'ON CONFLICT (name_hash) DO UPDATE', $source );
        $this->assertStringContainsString( 'busy_timeout', $source );
        $this->assertStringContainsString( 'journal_mode = WAL', $source );
    }

    /**
     * _protect() tries again when the database stays busy at the BEGIN IMMEDIATE or at a statement, never runs the
     * callback outside a transaction, and gives up after DBExecuteRetries tries.
     */
    public function testProtectRetriesABusyDatabase()
    {
        $probe = new expDFSSQLiteProtectProbe( 3 );
        $this->assertSame( 'done', $probe->run( 1, 0 ) );
        $this->assertSame( 2, $probe->calls );
        $this->assertSame( 1, $probe->commits );

        // a busy BEGIN: the callback is not called for that try
        $probe = new expDFSSQLiteProtectProbe( 3 );
        $this->assertSame( 'done', $probe->run( 0, 2 ) );
        $this->assertSame( 1, $probe->calls );
        $this->assertSame( 3, $probe->begins );

        $probe = new expDFSSQLiteProtectProbe( 3 );
        $this->assertFalse( $probe->run( 0, 10 ) );
        $this->assertSame( 0, $probe->calls );
        $this->assertSame( 0, $probe->commits );

        $probe = new expDFSSQLiteProtectProbe( 3 );
        $this->assertFalse( $probe->run( 10, 0 ) );
        $this->assertSame( 3, $probe->calls );
        $this->assertSame( 0, $probe->commits );
    }

    public function testSchemaFile()
    {
        $schema = file_get_contents( self::root() . '/kernel/sql/sqlite/cluster_dfs_schema.sql' );
        foreach ( array( 'ezdfsfile', 'ezdfsfile_cache' ) as $table )
            $this->assertMatchesRegularExpression( "/CREATE TABLE $table \\(/", $schema );
        $this->assertSame( 4, preg_match_all( '/^\s+(size|mtime) INTEGER NOT NULL/m', $schema ) );
        $this->assertSame( 2, preg_match_all( '/PRIMARY KEY \(name_hash\)/', $schema ) );
        // no collation: GLOB needs BINARY to use the index
        $this->assertStringNotContainsString( 'COLLATE', $schema );
        // the statements exp:cluster:enable runs
        $this->assertCount( 10, expClusterSetup::schemaStatements( $schema ) );
    }

    public function testGatewaySelection()
    {
        $gateway = file_get_contents( self::root() . '/kernel/clustering/dfssqlite.php' );
        $this->assertStringContainsString( 'class expDfsSQLiteClusterGateway extends ezpClusterGateway', $gateway );
        $this->assertStringContainsString( "setGatewayClass( 'expDfsSQLiteClusterGateway' )", $gateway );
        $classes = require self::root() . '/autoload/ezp_kernel.php';
        $this->assertSame( 'kernel/private/classes/clusterfilehandlers/dfsbackends/sqlite.php', $classes['expDFSFileHandlerSQLiteBackend'] );
        $this->assertSame( 'kernel/clustering/dfssqlite.php', $classes['expDfsSQLiteClusterGateway'] );
    }
}

/**
 * The gateway's pure database file rule, without the gateway file's setGatewayClass() side effect at load.
 */
class expDfsSQLiteClusterGatewayFile
{
    public static function of( $name, $root )
    {
        if ( !class_exists( 'ezpClusterGateway', false ) )
            require_once dirname( __DIR__, 6 ) . '/kernel/clustering/gateway.php';
        if ( !class_exists( 'expDfsSQLiteClusterGateway', false ) )
            require_once dirname( __DIR__, 6 ) . '/kernel/clustering/dfssqlite.php';
        return expDfsSQLiteClusterGateway::databaseFile( $name, $root );
    }
}

/**
 * The transaction handling of the SQLite backend without a connection: a BEGIN IMMEDIATE that finds the database
 * busy on its first $busyBegins tries, and a callback that finds it busy on its first $failures runs.
 */
class expDFSSQLiteProtectProbe extends expDFSFileHandlerSQLiteBackend
{
    public $calls = 0;
    public $begins = 0;
    public $commits = 0;
    public $rollbacks = 0;
    private $failures = 0;
    private $busyBegins = 0;

    public function __construct( $maxTries )
    {
        self::$dbparams = array( 'max_execute_tries' => $maxTries, 'sql_output' => false, 'cache_generation_timeout' => 60 );
    }

    public function run( $failures, $busyBegins )
    {
        $this->failures = $failures;
        $this->busyBegins = $busyBegins;
        return $this->_protect( array( $this, 'work' ), 'probe' );
    }

    public function work()
    {
        ++$this->calls;
        if ( $this->calls <= $this->failures )
        {
            $this->recordError( self::busy() );
            return false;
        }
        return 'done';
    }

    private static function busy()
    {
        $e = new PDOException( 'database is locked' );
        $e->errorInfo = array( 'HY000', 5, 'database is locked' );
        return $e;
    }

    protected function _begin( $fname = false )
    {
        $this->transactionCount++;
        ++$this->begins;
        if ( $this->begins <= $this->busyBegins )
        {
            $this->recordError( self::busy() );
            return false;
        }
        return true;
    }

    protected function _commit( $fname = false )
    {
        $this->transactionCount--;
        ++$this->commits;
        return true;
    }

    protected function _rollback( $fname = false )
    {
        $this->transactionCount--;
        ++$this->rollbacks;
    }

    protected function retryPause( $try )
    {
    }
}
