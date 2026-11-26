<?php
/**
 * The PostgreSQL DFS cluster backend (expDFSFileHandlerPostgresqlBackend) against a real PostgreSQL server: the
 * parity checks every backend passes (expDFSBackendParityChecks), plus the PostgreSQL gateway of index_cluster.php
 * and the upgrade of tables made by the ezpostgresqlcluster extension.
 *
 * Needs a server: EXP_TEST_PGCLUSTER_DSN=pgsql://<user>[:<password>]@<host>[:<port>]/<database to connect to>,
 * an account that may CREATE and DROP a database. The class makes exp_test_dfs_<pid>_<random> with
 * kernel/sql/postgresql/cluster_dfs_schema.sql and drops it after the last test. Skipped without the variable.
 *   EXP_TEST_PGCLUSTER_DSN=pgsql://postgres@127.0.0.20:54313/postgres php vendor/bin/phpunit \
 *       tests/tests/kernel/classes/clusterfilehandlers/dfsbackends/expDFSPostgresqlBackendIntegrationTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group cluster-live
 */

require_once __DIR__ . '/expDFSBackendParityChecks.php';

#[\PHPUnit\Framework\Attributes\Group('cluster-live')]
class expDFSPostgresqlBackendIntegrationTest extends ezpTestCase
{
    use expDFSBackendParityChecks;

    /** @var array|null host, port, user, password, admin database, test database */
    private static $server = null;

    /** @var PDO|null a connection to the test database, outside the backend */
    private static $raw = null;

    protected function backendClass()
    {
        return 'expDFSFileHandlerPostgresqlBackend';
    }

    protected function supportsBoundedLockRetry()
    {
        return true;
    }

    protected function supportsTimesAfter2038()
    {
        return true;
    }

    /** @return array|string the parts of EXP_TEST_PGCLUSTER_DSN, or why there are none */
    private static function dsnParts()
    {
        if ( !extension_loaded( 'pdo_pgsql' ) )
            return 'the PHP extension pdo_pgsql is not loaded';
        $dsn = trim( (string)getenv( 'EXP_TEST_PGCLUSTER_DSN' ) );
        if ( $dsn === '' )
            return 'EXP_TEST_PGCLUSTER_DSN is not set (pgsql://user[:password]@host[:port]/database)';
        $parts = parse_url( $dsn );
        if ( !is_array( $parts ) || !isset( $parts['host'] ) || !in_array( $parts['scheme'] ?? '', array( 'pgsql', 'postgresql', 'postgres' ), true ) )
            return 'EXP_TEST_PGCLUSTER_DSN is not a pgsql:// URL';
        return array(
            'host' => $parts['host'],
            'port' => isset( $parts['port'] ) ? (int)$parts['port'] : 5432,
            'user' => isset( $parts['user'] ) ? rawurldecode( $parts['user'] ) : 'postgres',
            'password' => isset( $parts['pass'] ) ? rawurldecode( $parts['pass'] ) : '',
            'admin' => isset( $parts['path'] ) && trim( $parts['path'], '/' ) !== '' ? trim( $parts['path'], '/' ) : 'postgres',
        );
    }

    /** @return PDO */
    private static function connect( array $server, $database )
    {
        return new PDO( "pgsql:host={$server['host']};port={$server['port']};dbname=$database", $server['user'], $server['password'],
                        array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ) );
    }

    /**
     * Runs an SQL file of statements separated by semicolons at line ends, comment lines left out.
     *
     * @param PDO $db
     * @param string $file
     */
    public static function runSqlFile( PDO $db, $file )
    {
        $sql = preg_replace( '/^\s*--.*$/m', '', file_get_contents( $file ) );
        foreach ( preg_split( '/;\s*$/m', $sql ) as $statement )
        {
            if ( trim( $statement ) !== '' )
                $db->exec( $statement );
        }
    }

    protected static function engineSetUp()
    {
        $server = self::dsnParts();
        if ( !is_array( $server ) )
            return $server;
        try
        {
            $admin = self::connect( $server, $server['admin'] );
            $server['database'] = 'exp_test_dfs_' . getmypid() . '_' . bin2hex( random_bytes( 3 ) );
            $admin->exec( "CREATE DATABASE {$server['database']}" );
            self::$server = $server;
            self::$raw = self::connect( $server, $server['database'] );
            self::runSqlFile( self::$raw, dirname( __DIR__, 6 ) . '/kernel/sql/postgresql/cluster_dfs_schema.sql' );
        }
        catch ( PDOException $e )
        {
            return 'Cannot prepare a PostgreSQL test database: ' . $e->getMessage();
        }
        return null;
    }

    protected static function engineTearDown()
    {
        self::$raw = null;
        if ( self::$server === null )
            return;
        try
        {
            $admin = self::connect( self::$server, self::$server['admin'] );
            $admin->exec( 'DROP DATABASE IF EXISTS ' . self::$server['database'] . ' WITH (FORCE)' );
        }
        catch ( PDOException $e )
        {
            fwrite( STDERR, "Could not drop " . self::$server['database'] . ": " . $e->getMessage() . "\n" );
        }
        self::$server = null;
    }

    protected function engineSettings()
    {
        return array(
            'DBHost' => self::$server['host'],
            'DBPort' => (string)self::$server['port'],
            'DBSocket' => '',
            'DBName' => self::$server['database'],
            'DBUser' => self::$server['user'],
            'DBPassword' => self::$server['password'],
        );
    }

    protected function rawExec( $sql )
    {
        self::$raw->exec( $sql );
    }

    protected function rawRows( $sql )
    {
        return self::$raw->query( $sql )->fetchAll( PDO::FETCH_ASSOC );
    }

    public function testSchemaUsesBigintTimesAndPatternIndexes()
    {
        $columns = array();
        foreach ( $this->rawRows( "SELECT table_name, column_name, data_type FROM information_schema.columns WHERE table_name IN ('ezdfsfile', 'ezdfsfile_cache')" ) as $row )
            $columns[$row['table_name'] . '.' . $row['column_name']] = $row['data_type'];
        foreach ( array( 'ezdfsfile', 'ezdfsfile_cache' ) as $table )
        {
            $this->assertSame( 'bigint', $columns["$table.size"] );
            $this->assertSame( 'bigint', $columns["$table.mtime"] );
        }
        $indexes = array_column( $this->rawRows( "SELECT indexdef FROM pg_indexes WHERE tablename = 'ezdfsfile'" ), 'indexdef' );
        $this->assertNotEmpty( preg_grep( '/\(name text_pattern_ops\)/', $indexes ) );
    }

    /**
     * A prefix search uses the text_pattern_ops index, so a purge of var/<site>/cache/% does not read the table.
     */
    public function testPrefixSearchCanUseTheIndex()
    {
        $this->rawExec( 'SET enable_seqscan = off' );
        $plan = implode( "\n", array_column( $this->rawRows( "EXPLAIN SELECT name FROM ezdfsfile WHERE name LIKE 'var/site/cache/%'" ), 'QUERY PLAN' ) );
        $this->rawExec( 'SET enable_seqscan = on' );
        $this->assertStringContainsString( 'ezdfsfile_name', $plan );
    }

    public function testGatewayServesMetadataAndRanges()
    {
        $path = $this->path( 'storage/original/application/file.bin' );
        $data = random_bytes( 150000 );
        $this->backend->_storeContents( $path, $data, 'binaryfile', 'application/octet-stream', 1700000000 );

        if ( !class_exists( 'ezpClusterGateway', false ) )
            require_once dirname( __DIR__, 6 ) . '/kernel/clustering/gateway.php';
        require_once dirname( __DIR__, 6 ) . '/kernel/clustering/dfspostgresql.php';
        if ( !defined( 'CLUSTER_MOUNT_POINT_PATH' ) )
            define( 'CLUSTER_MOUNT_POINT_PATH', realpath( $this->mount ) );
        if ( CLUSTER_MOUNT_POINT_PATH !== realpath( $this->mount ) )
            $this->markTestSkipped( 'CLUSTER_MOUNT_POINT_PATH is already defined for another mount' );

        $gateway = new expDfsPostgresqlClusterGateway( array(
            'host' => self::$server['host'], 'port' => self::$server['port'], 'user' => self::$server['user'],
            'password' => self::$server['password'], 'name' => self::$server['database'], 'charset' => 'utf-8',
        ) );
        $gateway->connect();
        $meta = $gateway->fetchFileMetadata( $path );
        $this->assertSame( array( 'datatype' => 'application/octet-stream', 'size' => '150000', 'mtime' => '1700000000' ), $meta );
        $this->assertFalse( $gateway->fetchFileMetadata( $this->path( 'storage/none.bin' ) ) );

        ob_start();
        $gateway->passthrough( $path, 150000, 100, 70000 );
        $this->assertSame( substr( $data, 100, 70000 ), ob_get_clean() );
        ob_start();
        $gateway->passthrough( $path, 150000 );
        $this->assertSame( $data, ob_get_clean() );
        $gateway->close();
    }

    /**
     * With CLUSTER_PERSISTENT_CONNECTION true, a second gateway request of the same PHP process uses the same server
     * connection again (the same backend pid) after close(), and errors are still exceptions on it.
     */
    public function testGatewayReusesAPersistentConnection()
    {
        if ( !class_exists( 'ezpClusterGateway', false ) )
            require_once dirname( __DIR__, 6 ) . '/kernel/clustering/gateway.php';
        require_once dirname( __DIR__, 6 ) . '/kernel/clustering/dfspostgresql.php';
        if ( !defined( 'CLUSTER_PERSISTENT_CONNECTION' ) )
            define( 'CLUSTER_PERSISTENT_CONNECTION', true );
        if ( !CLUSTER_PERSISTENT_CONNECTION )
            $this->markTestSkipped( 'CLUSTER_PERSISTENT_CONNECTION is already defined as false' );

        $pids = array();
        for ( $request = 0; $request < 3; $request++ )
        {
            $gateway = new expDfsPostgresqlClusterGateway( array(
                'host' => self::$server['host'], 'port' => self::$server['port'], 'user' => self::$server['user'],
                'password' => self::$server['password'], 'name' => self::$server['database'], 'charset' => 'utf-8',
            ) );
            $gateway->connect();
            $this->assertFalse( $gateway->fetchFileMetadata( $this->path( 'storage/none.bin' ) ) );
            $db = ( function () { return $this->db; } )->call( $gateway );
            $pids[] = (int)$db->query( 'SELECT pg_backend_pid()' )->fetchColumn();
            try
            {
                $db->query( 'SELECT * FROM exp_no_such_table' );
                $this->fail( 'a failed statement on a reused connection must throw' );
            }
            catch ( PDOException $e )
            {
                $this->assertSame( '42P01', $e->getCode() );
            }
            $gateway->close();
        }
        $this->assertCount( 1, array_unique( $pids ), 'backend pids: ' . implode( ', ', $pids ) );
    }

    /**
     * Tables in the form the ezpostgresqlcluster extension made them are upgraded in place, rows kept, and the
     * upgrade can run twice.
     */
    public function testUpgradeOfExtensionTables()
    {
        $root = dirname( __DIR__, 6 );
        $this->rawExec( 'DROP TABLE ezdfsfile' );
        $this->rawExec( 'DROP TABLE ezdfsfile_cache' );
        try
        {
            $this->rawExec( "CREATE TABLE ezdfsfile ( datatype varchar(255) DEFAULT 'application/octet-stream' NOT NULL, name text NOT NULL, name_trunk text NOT NULL, " .
                            "name_hash character(32) DEFAULT '' NOT NULL PRIMARY KEY, scope varchar(20) DEFAULT '' NOT NULL, size integer DEFAULT 0 NOT NULL, " .
                            "mtime integer DEFAULT 0 NOT NULL, expired integer DEFAULT 0 NOT NULL )" );
            $this->rawExec( "CREATE INDEX ezdfsfile_name ON ezdfsfile ( name )" );
            $this->rawExec( "CREATE TABLE ezdfsfile_cache ( datatype varchar(255) DEFAULT 'application/octet-stream' NOT NULL, name text NOT NULL, name_trunk text NOT NULL, " .
                            "name_hash character(32) DEFAULT '' NOT NULL PRIMARY KEY, scope varchar(20) DEFAULT '' NOT NULL, size integer DEFAULT 0 NOT NULL, " .
                            "mtime integer DEFAULT 0 NOT NULL, expired integer DEFAULT 0 NOT NULL )" );
            $this->rawExec( "INSERT INTO ezdfsfile (name, name_trunk, name_hash, scope, size, mtime) VALUES ('a/b.txt', 'a/b.txt', '" . md5( 'a/b.txt' ) . "', 'binaryfile', 3, 1700000000)" );

            self::runSqlFile( self::$raw, "$root/kernel/sql/postgresql/cluster_dfs_schema_upgrade.sql" );
            self::runSqlFile( self::$raw, "$root/kernel/sql/postgresql/cluster_dfs_schema_upgrade.sql" );

            $this->testSchemaUsesBigintTimesAndPatternIndexes();
            $this->backend->_disconnect();
            $this->backend = $this->newBackend();
            $this->assertSame( '1700000000', (string)$this->backend->_fetchMetadata( 'a/b.txt' )['mtime'] );
            $this->assertNotFalse( $this->backend->_storeContents( $this->path( 'files/up.txt' ), 'up', 'binaryfile', 'text/plain', 4102444800 ) );
        }
        finally
        {
            $this->rawExec( 'DROP TABLE IF EXISTS ezdfsfile' );
            $this->rawExec( 'DROP TABLE IF EXISTS ezdfsfile_cache' );
            self::runSqlFile( self::$raw, "$root/kernel/sql/postgresql/cluster_dfs_schema.sql" );
        }
    }
}
