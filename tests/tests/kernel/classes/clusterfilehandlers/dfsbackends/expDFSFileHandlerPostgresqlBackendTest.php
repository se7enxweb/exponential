<?php
/**
 * The PostgreSQL DFS cluster backend without a database: SQLSTATE mapping and the retry of _protect(), the DSN,
 * LIKE and wildcard building, name trunks, the method for method parity with the MySQLi backend, the schema file,
 * the gateway selection and the earlier class name. The parts that need a server are in
 * expDFSPostgresqlBackendIntegrationTest.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expDFSFileHandlerPostgresqlBackendTest extends ezpTestCase
{
    /** @return string the root of the installation */
    private static function root()
    {
        return dirname( __DIR__, 6 );
    }

    public function testRetryableSqlStates()
    {
        foreach ( array( '40P01', '55P03', '40001' ) as $state )
            $this->assertTrue( expDFSFileHandlerPostgresqlBackend::isRetryableSqlState( $state ), $state );
        // MySQL's numbers, a unique violation, a missing table and nothing are not
        foreach ( array( '1205', '1213', '23505', '42P01', '', null ) as $state )
            $this->assertFalse( expDFSFileHandlerPostgresqlBackend::isRetryableSqlState( $state ), var_export( $state, true ) );
    }

    public function testSqlStateOfAnException()
    {
        $e = new PDOException( 'deadlock detected' );
        $e->errorInfo = array( '40P01', 7, 'deadlock detected' );
        $this->assertSame( '40P01', expDFSFileHandlerPostgresqlBackend::sqlStateOf( $e ) );
        $this->assertSame( '', expDFSFileHandlerPostgresqlBackend::sqlStateOf( new RuntimeException( 'x', 5 ) ) );
    }

    public function testDsn()
    {
        $this->assertSame( 'pgsql:host=db.example;port=5433;dbname=cluster;connect_timeout=10',
            expDFSFileHandlerPostgresqlBackend::buildDsn( array( 'host' => 'db.example', 'port' => '5433', 'socket' => '', 'dbname' => 'cluster', 'connect_timeout' => 10 ) ) );
        // an empty port is PostgreSQL's, a socket directory replaces the host, ; cannot inject a parameter
        $this->assertSame( 'pgsql:host=/run/postgresql;port=5432;dbname=clusterx',
            expDFSFileHandlerPostgresqlBackend::buildDsn( array( 'host' => 'localhost', 'port' => null, 'socket' => '/run/postgresql', 'dbname' => 'cluster;x' ) ) );
        // DBODBCDriver: PDO_ODBC with psqlODBC, for a site on PostgreSQL over ODBC
        $this->assertSame( 'odbc:Driver={PostgreSQL Unicode};Server=db.example;Port=5433;Database=cluster',
            expDFSFileHandlerPostgresqlBackend::buildDsn( array( 'host' => 'db.example', 'port' => '5433', 'socket' => '', 'dbname' => 'cluster;', 'connect_timeout' => 10,
                                                                 'odbc_driver' => 'PostgreSQL Unicode' ) ) );
        $this->assertArrayNotHasKey( PDO::ATTR_EMULATE_PREPARES, expDFSFileHandlerPostgresqlBackend::connectionOptions( false, true ) );
        $this->assertTrue( expDFSFileHandlerPostgresqlBackend::connectionOptions( true )[PDO::ATTR_EMULATE_PREPARES] );
        $this->assertTrue( expDFSFileHandlerPostgresqlBackend::connectionOptions( true )[PDO::ATTR_PERSISTENT] );
        require_once dirname( __DIR__, 6 ) . '/kernel/clustering/gateway.php';
        require_once dirname( __DIR__, 6 ) . '/kernel/clustering/dfspostgresql.php';
        $this->assertSame( 'pgsql:host=h;port=5432;dbname=d;connect_timeout=10', expDfsPostgresqlClusterGateway::dsn( 'h', null, 'd' ) );
        $this->assertSame( 'odbc:Driver={PostgreSQL Unicode(x64)};Server=h;Port=6432;Database=d', expDfsPostgresqlClusterGateway::dsn( 'h', 6432, 'd', 'PostgreSQL Unicode(x64)' ) );
    }

    public function testClientEncoding()
    {
        $this->assertSame( 'UTF8', expDFSFileHandlerPostgresqlBackend::clientEncoding( 'utf-8' ) );
        $this->assertSame( 'UTF8', expDFSFileHandlerPostgresqlBackend::clientEncoding( 'UTF8' ) );
        $this->assertSame( 'UTF8', expDFSFileHandlerPostgresqlBackend::clientEncoding( '' ) );
        // nothing but letters, digits and _ reaches the SET statement
        $this->assertSame( 'LATIN1DROP', expDFSFileHandlerPostgresqlBackend::clientEncoding( "latin1'; DROP" ) );
    }

    public function testLikePatterns()
    {
        $this->assertSame( 'var/site/cache/a\\_b%', expDFSFileHandlerPostgresqlBackend::likePattern( 'var/site/cache/a_b%' ) );
        $this->assertSame( 'var/100\\%\\_x%', expDFSFileHandlerPostgresqlBackend::likePrefix( 'var/100%_x' ) );
    }

    public function testWildcardToRegex()
    {
        $this->assertSame( '^var/(a|b)\\.txt$', expDFSFileHandlerPostgresqlBackend::wildcardToRegex( 'var/{a,b}.txt' ) );
        $this->assertSame( '^var/.\\..*$', expDFSFileHandlerPostgresqlBackend::wildcardToRegex( 'var/?.*' ) );
        // a comma outside braces and other specials are literals; an unclosed brace is closed
        $this->assertSame( '^a,b\\+\\(\\)\\}$', expDFSFileHandlerPostgresqlBackend::wildcardToRegex( 'a,b+()}' ) );
        $this->assertSame( '^(x|y)$', expDFSFileHandlerPostgresqlBackend::wildcardToRegex( '{x,y' ) );
    }

    public function testNameTrunkOfViewCache()
    {
        $this->assertSame( 'var/site/cache/content/site/1/2-',
            expDFSFileHandlerPostgresqlBackend::nameTrunk( 'var/site/cache/content/site/1/2-abcdef.cache', 'viewcache' ) );
        $this->assertSame( 'var/storage/a.png', expDFSFileHandlerPostgresqlBackend::nameTrunk( 'var/storage/a.png', 'image' ) );
    }

    /**
     * Every public method of the MySQLi backend exists in the PostgreSQL backend, with as many parameters and the
     * same number of required ones: eZDFSFileHandler can use either.
     */
    public function testMethodParityWithTheMySQLiBackend()
    {
        $mysql = new ReflectionClass( 'eZDFSFileHandlerMySQLiBackend' );
        $pgsql = new ReflectionClass( 'expDFSFileHandlerPostgresqlBackend' );
        foreach ( $mysql->getMethods( ReflectionMethod::IS_PUBLIC ) as $method )
        {
            $name = $method->getName();
            $this->assertTrue( $pgsql->hasMethod( $name ), "missing $name" );
            $other = $pgsql->getMethod( $name );
            $this->assertTrue( $other->isPublic(), "$name is not public" );
            $this->assertGreaterThanOrEqual( $method->getNumberOfParameters(), $other->getNumberOfParameters(), $name );
            $this->assertSame( $method->getNumberOfRequiredParameters(), $other->getNumberOfRequiredParameters(), $name );
        }
        foreach ( array( 'eZClusterEventNotifier', 'expDFSFileHandlerDBBackendInterface' ) as $interface )
        {
            $this->assertTrue( $pgsql->implementsInterface( $interface ), "PostgreSQL: $interface" );
            $this->assertTrue( $mysql->implementsInterface( $interface ), "MySQLi: $interface" );
        }
    }

    public function testNoPgFunctionsAndNoMysqlErrorNumbers()
    {
        // the code only: the comments name the MySQL numbers on purpose
        $source = '';
        foreach ( token_get_all( file_get_contents( self::root() . '/kernel/private/classes/clusterfilehandlers/dfsbackends/postgresql.php' ) ) as $token )
        {
            if ( !is_array( $token ) || !in_array( $token[0], array( T_COMMENT, T_DOC_COMMENT ), true ) )
                $source .= is_array( $token ) ? $token[1] : $token;
        }
        $this->assertDoesNotMatchRegularExpression( '/\bpg_[a-z_]+\s*\(/', $source );
        $this->assertDoesNotMatchRegularExpression( '/\b(1205|1213|1062)\b/', $source );
        $this->assertStringContainsString( 'ON CONFLICT (name_hash) DO UPDATE', $source );
        $this->assertStringContainsString( 'FOR UPDATE SKIP LOCKED', $source );
        $this->assertStringContainsString( 'lock_timeout', $source );
    }

    /**
     * _protect() runs the callback again after a deadlock, and gives up after DBExecuteRetries tries.
     */
    public function testProtectRetriesADeadlock()
    {
        $probe = new expDFSPostgresqlProtectProbe( 3 );
        $this->assertSame( 'done', $probe->run( 1 ) );
        $this->assertSame( 2, $probe->calls );
        $this->assertSame( 1, $probe->rollbacks );
        $this->assertSame( 1, $probe->commits );

        $probe = new expDFSPostgresqlProtectProbe( 3 );
        $this->assertFalse( $probe->run( 10 ) );
        $this->assertSame( 3, $probe->calls );
        $this->assertSame( 0, $probe->commits );
    }

    public function testSchemaFiles()
    {
        $schema = file_get_contents( self::root() . '/kernel/sql/postgresql/cluster_dfs_schema.sql' );
        foreach ( array( 'ezdfsfile', 'ezdfsfile_cache' ) as $table )
            $this->assertMatchesRegularExpression( "/CREATE TABLE $table \\(/", $schema );
        $this->assertSame( 4, preg_match_all( '/^\s+(size|mtime) bigint NOT NULL/m', $schema ) );
        $this->assertStringContainsString( 'text_pattern_ops', $schema );
        $this->assertDoesNotMatchRegularExpression( '/\binteger\b/', $schema );
        $upgrade = file_get_contents( self::root() . '/kernel/sql/postgresql/cluster_dfs_schema_upgrade.sql' );
        $this->assertStringContainsString( 'ALTER COLUMN mtime TYPE bigint', $upgrade );
    }

    public function testGatewaySelection()
    {
        $root = self::root();
        // index_cluster.php loads kernel/clustering/<CLUSTER_STORAGE_BACKEND>.php
        $this->assertStringContainsString( '"kernel/clustering/" . CLUSTER_STORAGE_BACKEND . ".php"', file_get_contents( "$root/index_cluster.php" ) );
        $gateway = file_get_contents( "$root/kernel/clustering/dfspostgresql.php" );
        $this->assertStringContainsString( 'class expDfsPostgresqlClusterGateway extends ezpClusterGateway', $gateway );
        $this->assertStringContainsString( 'protected $port = 5432;', $gateway );
        $this->assertStringContainsString( "setGatewayClass( 'expDfsPostgresqlClusterGateway' )", $gateway );
        $this->assertDoesNotMatchRegularExpression( '/\bpg_[a-z_]+\s*\(/', $gateway );
        $this->assertStringContainsString( "dfspostgresql.php", file_get_contents( "$root/kernel/clustering/dfspostgres.php" ) );
    }

    /**
     * The gateway's connection is persistent exactly when config.cluster.php defines CLUSTER_PERSISTENT_CONNECTION
     * true (index_cluster.php defaults it to false), and errors stay exceptions either way.
     */
    public function testGatewayPersistentConnection()
    {
        $root = self::root();
        if ( !class_exists( 'ezpClusterGateway', false ) )
            require_once "$root/kernel/clustering/gateway.php";
        require_once "$root/kernel/clustering/dfspostgresql.php";

        foreach ( array( false, true ) as $persistent )
        {
            $options = expDfsPostgresqlClusterGateway::connectionOptions( $persistent );
            $this->assertSame( $persistent, $options[PDO::ATTR_PERSISTENT] );
            $this->assertSame( PDO::ERRMODE_EXCEPTION, $options[PDO::ATTR_ERRMODE] );
            $this->assertTrue( $options[PDO::ATTR_STRINGIFY_FETCHES] );
        }
        $this->assertFalse( expDfsPostgresqlClusterGateway::connectionOptions( null )[PDO::ATTR_PERSISTENT] );

        $gateway = file_get_contents( "$root/kernel/clustering/dfspostgresql.php" );
        $this->assertStringContainsString( "self::connectionOptions( defined( 'CLUSTER_PERSISTENT_CONNECTION' ) && constant( 'CLUSTER_PERSISTENT_CONNECTION' ) )", $gateway );
        $this->assertStringContainsString( "define( 'CLUSTER_PERSISTENT_CONNECTION', false )", file_get_contents( "$root/index_cluster.php" ) );
    }

    /**
     * The earlier name maps to the kernel class in the kernel autoload (an installed ezpostgresqlcluster extension
     * still comes first, as extension classes do).
     */
    public function testEarlierClassName()
    {
        $classes = require self::root() . '/autoload/ezp_kernel.php';
        $this->assertSame( 'kernel/private/classes/clusterfilehandlers/dfsbackends/ezdfsfilehandlerpostgresqlbackend.php', $classes['eZDFSFileHandlerPostgresqlBackend'] );
        $this->assertSame( 'kernel/private/classes/clusterfilehandlers/dfsbackends/postgresql.php', $classes['expDFSFileHandlerPostgresqlBackend'] );
        $this->assertStringContainsString( 'class eZDFSFileHandlerPostgresqlBackend extends expDFSFileHandlerPostgresqlBackend',
            file_get_contents( self::root() . '/' . $classes['eZDFSFileHandlerPostgresqlBackend'] ) );
    }
}

/**
 * The transaction handling of the PostgreSQL backend without a connection: a callback that hits a deadlock on its
 * first $failures runs.
 */
class expDFSPostgresqlProtectProbe extends expDFSFileHandlerPostgresqlBackend
{
    public $calls = 0;
    public $commits = 0;
    public $rollbacks = 0;
    private $failures = 0;

    public function __construct( $maxTries )
    {
        self::$dbparams = array( 'max_execute_tries' => $maxTries, 'sql_output' => false, 'cache_generation_timeout' => 60 );
    }

    public function run( $failures )
    {
        $this->failures = $failures;
        return $this->_protect( array( $this, 'work' ), 'probe' );
    }

    public function work()
    {
        ++$this->calls;
        if ( $this->calls <= $this->failures )
        {
            $e = new PDOException( 'deadlock detected' );
            $e->errorInfo = array( '40P01', 7, 'deadlock detected' );
            $this->recordError( $e );
            return false;
        }
        return 'done';
    }

    protected function _begin( $fname = false )
    {
        $this->transactionCount++;
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
