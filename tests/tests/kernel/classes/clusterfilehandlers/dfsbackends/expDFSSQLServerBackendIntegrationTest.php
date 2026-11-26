<?php
/**
 * The SQL Server DFS cluster backend (expDFSFileHandlerSQLServerBackend) on a real SQL Server: the parity checks every
 * backend passes (expDFSBackendParityChecks), including four processes on one cache key and a bounded wait for a row
 * lock held by another connection (on the PDO_ODBC layer, see beforeFork()), plus the schema's column types, the case
 * sensitivity of the name searches, the LIKE characters in names, Unicode names, a connection error, and the gateway of
 * index_cluster.php (kernel/clustering/dfssqlserver.php).
 *
 * Uses the SQL Server of the driver's live tests: EXP_TEST_SQLSERVER_HOST, _PORT, _USER, _PASSWORD (or
 * _PASSWORD_FILE), _LAYER; the database EXP_TEST_DFS_SQLSERVER_DATABASE (default exp_dfs_test) is made on first use.
 * The class creates ezdfsfile and ezdfsfile_cache with kernel/sql/sqlserver/cluster_dfs_schema.sql and drops them
 * after the last test; a database that has them already is not touched (the class is skipped).
 *   EXP_TEST_SQLSERVER_HOST=127.0.0.1 EXP_TEST_SQLSERVER_PASSWORD=... \
 *     php vendor/bin/phpunit tests/tests/kernel/classes/clusterfilehandlers/dfsbackends/expDFSSQLServerBackendIntegrationTest.php
 *
 * The rawExec()/rawRows() of the parity checks run as they are, except that the size column is filesize.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group cluster-live
 */

require_once __DIR__ . '/expDFSBackendParityChecks.php';
require_once dirname( __DIR__, 4 ) . '/lib/ezdb/sqlserver/expSQLServerTestKit.php';
foreach ( array( 'sqlserverresult', 'sqlserver' ) as $file )
    require_once dirname( __DIR__, 6 ) . "/kernel/private/classes/clusterfilehandlers/dfsbackends/$file.php";

#[\PHPUnit\Framework\Attributes\Group('cluster-live')]
#[\PHPUnit\Framework\Attributes\Group('sqlserver')]
class expDFSSQLServerBackendIntegrationTest extends ezpTestCase
{
    use expDFSBackendParityChecks;

    /** @var array|null the live server's settings plus the database */
    private static $account = null;

    /** @var expSQLServerTransport|null a connection outside the backend */
    private static $raw = null;

    /** @var bool whether the class created the tables (and so drops them) */
    private static $created = false;

    protected function backendClass()
    {
        return 'expDFSFileHandlerSQLServerBackend';
    }

    protected function supportsBoundedLockRetry()
    {
        return true;
    }

    protected function supportsTimesAfter2038()
    {
        return true;
    }

    /** The row lock of another connection: WITH (UPDLOCK, ROWLOCK) in an open transaction */
    protected function holdLock( $holder, $path )
    {
        $holder->db->run( "BEGIN TRANSACTION; SELECT name_hash FROM ezdfsfile WITH (UPDLOCK, ROWLOCK) WHERE name_hash = '" . md5( $path ) . "'" );
    }

    protected function releaseLock( $holder )
    {
        $holder->db->run( 'IF @@TRANCOUNT > 0 COMMIT TRANSACTION' );
    }

    /**
     * sqlsrv and pdo_sqlsrv keep their ODBC environment for the life of the process, and with it the socket of a
     * connection the process has closed (still ESTABLISHED after sqlsrv_close(), checked in /proc/self/fd). A child
     * forked after such a close gets a driver whose new connections the server resets (TCP Provider error 0x2746,
     * and 0x20 in the parent afterwards). PDO_ODBC frees its environment with the connection, so it forks cleanly:
     * the tests with processes in parallel run on that layer (the CI matrix has it), the others skip them here.
     */
    protected function beforeFork()
    {
        if ( in_array( self::$account['layer_name'], array( 'sqlsrv', 'pdo_sqlsrv' ), true ) )
            $this->markTestSkipped( self::$account['layer_name'] . ' cannot fork after closing a connection; this test runs on the PDO_ODBC layer' );
    }

    /** 1222: lock request time out period exceeded */
    protected function lockWaitErrorCode()
    {
        return '1222';
    }

    protected function lastBackendErrorCode()
    {
        return $this->backend->lastErrorCode();
    }

    /** Runs a batch on the raw connection; @return array the transport's result */
    private static function exec( $sql )
    {
        $result = self::$raw->run( $sql );
        if ( $result === false )
        {
            $e = self::$raw->error();
            throw new RuntimeException( ( is_array( $e ) ? $e['message'] : 'SQL Server error' ) . " in: $sql" );
        }
        return $result;
    }

    /** The connection settings of the transport for the test database (or master) */
    private static function config( $database )
    {
        return array( 'host' => self::$account['host'], 'port' => self::$account['port'], 'database' => $database,
                      'user' => self::$account['user'], 'password' => self::$account['password'], 'encrypt' => 'yes',
                      'trust_server_certificate' => true, 'login_timeout' => 10, 'connect_retry_count' => 0,
                      'odbc_driver' => self::$account['odbc_driver'], 'app_name' => 'Exponential DFS test' );
    }

    protected static function engineSetUp()
    {
        $live = expSQLServerTestKit::live();
        if ( $live === null )
            return 'EXP_TEST_SQLSERVER_HOST is not set';
        $layer = expSQLServerTransport::chooseLayer( $live['layer'] );
        if ( $layer === null )
            return 'PHP has no SQL Server layer (sqlsrv, pdo_sqlsrv, PDO_ODBC, pdo_dblib)';
        self::$account = $live + array( 'dfs_database' => getenv( 'EXP_TEST_DFS_SQLSERVER_DATABASE' ) ?: 'exp_dfs_test', 'layer_name' => $layer );
        self::$raw = expSQLServerTransport::create( $layer );
        if ( !self::$raw->open( self::config( 'master' ) ) )
            return 'Cannot connect to the SQL Server for the cluster tests: ' . self::$raw->error()['message'];
        try
        {
            $db = expSQLServerDialect::quoteIdentifier( self::$account['dfs_database'] );
            self::exec( "IF DB_ID(N'" . expSQLServerDialect::escapeString( self::$account['dfs_database'] ) . "') IS NULL CREATE DATABASE $db" );
            self::$raw->close();
            self::$raw->open( self::config( self::$account['dfs_database'] ) );
            self::exec( expSQLServerDialect::sessionSetup() );
            $count = self::exec( "SELECT COUNT(*) AS c FROM sys.tables WHERE name IN (N'ezdfsfile', N'ezdfsfile_cache')" );
            if ( (int)$count['sets'][0][0]['c'] > 0 )
                return 'the test database has cluster tables already; it is left alone (use a database of its own)';
            self::$created = true;
            foreach ( expClusterSetup::schemaStatements( file_get_contents( dirname( __DIR__, 6 ) . '/kernel/sql/sqlserver/cluster_dfs_schema.sql' ) ) as $st )
                self::exec( $st['sql'] );
        }
        catch ( Exception $e )
        {
            return 'Cannot prepare the SQL Server test tables: ' . $e->getMessage();
        }
        return null;
    }

    protected static function engineTearDown()
    {
        if ( self::$raw && self::$created )
        {
            foreach ( array( 'ezdfsfile', 'ezdfsfile_cache' ) as $table )
            {
                try
                {
                    self::exec( "IF OBJECT_ID(N'$table') IS NOT NULL DROP TABLE $table" );
                }
                catch ( Exception $e )
                {
                }
            }
        }
        if ( self::$raw )
            self::$raw->close();
        self::$raw = null;
        self::$created = false;
    }

    protected function engineSettings()
    {
        // the connection's layer and encryption, as for the site's database
        ezpINIHelper::setINISetting( 'site.ini', 'SQLServerSettings', 'Layer', self::$account['layer_name'] );
        ezpINIHelper::setINISetting( 'site.ini', 'SQLServerSettings', 'TrustServerCertificate', 'enabled' );
        ezpINIHelper::setINISetting( 'site.ini', 'SQLServerSettings', 'ODBCDriver', self::$account['odbc_driver'] );
        return array(
            'DBHost' => self::$account['host'],
            'DBPort' => (string)self::$account['port'],
            'DBSocket' => '',
            'DBName' => self::$account['dfs_database'],
            'DBUser' => self::$account['user'],
            'DBPassword' => self::$account['password'],
        );
    }

    protected function rawExec( $sql )
    {
        // the size column is filesize
        $sql = preg_replace_callback( '/^(\s*INSERT\s+INTO\s+\w+\s*\()([^)]*)\)/i', function ( $m )
        {
            return $m[1] . preg_replace( '/\bsize\b/', 'filesize', $m[2] ) . ')';
        }, $sql );
        self::exec( expSQLServerDialect::unicodeLiterals( $sql ) );
    }

    protected function rawRows( $sql )
    {
        $result = self::exec( $sql );
        $rows = array();
        foreach ( isset( $result['sets'][0] ) ? $result['sets'][0] : array() as $row )
            $rows[] = expDFSFileHandlerSQLServerBackend::normalizeRow( $row );
        return $rows;
    }

    public function testSchemaUses64BitNumbersAndFilesize()
    {
        foreach ( array( 'ezdfsfile', 'ezdfsfile_cache' ) as $table )
        {
            $types = array();
            foreach ( $this->rawRows( "SELECT c.name AS column_name, t.name AS data_type, c.collation_name FROM sys.columns c JOIN sys.types t ON t.user_type_id = c.user_type_id WHERE c.object_id = OBJECT_ID(N'$table')" ) as $row )
                $types[$row['column_name']] = $row['data_type'] . ( $row['collation_name'] !== '' ? ' ' . $row['collation_name'] : '' );
            $this->assertSame( 'bigint', $types['filesize'] );
            $this->assertSame( 'bigint', $types['mtime'] );
            $this->assertSame( 'nvarchar Latin1_General_100_BIN2', $types['name'] );
            // a Windows collation: the NVARCHAR value of a bound name_hash seeks the key instead of scanning the table
            $this->assertSame( 'varchar Latin1_General_100_BIN2', $types['name_hash'] );
            $this->assertArrayNotHasKey( 'size', $types );
        }
        $path = $this->path( 'files/size.txt' );
        $this->backend->_storeContents( $path, '12345', 'binaryfile', 'text/plain', 1700000000 );
        $meta = $this->backend->_fetchMetadata( $path );
        $this->assertSame( '5', $meta['size'], 'filesize is returned as size' );
        $this->assertArrayNotHasKey( 'filesize', $meta );
    }

    /** Names are compared case sensitively, as MySQL's binary LIKE on name compares them */
    public function testNameSearchesAreCaseSensitive()
    {
        $this->backend->_storeContents( $this->path( 'case/Upper.txt' ), 'U', 'binaryfile', 'text/plain', 1700000000 );
        $this->backend->_storeContents( $this->path( 'case/upper.txt' ), 'u', 'binaryfile', 'text/plain', 1700000000 );
        $this->assertTrue( $this->backend->_deleteByLike( $this->path( 'case/up%' ) ) );
        $this->assertTrue( $this->backend->_exists( $this->path( 'case/Upper.txt' ) ) );
        $this->assertFalse( $this->backend->_exists( $this->path( 'case/upper.txt' ) ) );
        $this->assertSame( array( $this->path( 'case/Upper.txt' ) ), $this->backend->_getFileList( false, false, false, $this->path( 'case/U' ) ) );
    }

    /** LIKE's own characters (%, _, [ and the escape \) in a name are literals of a prefix and of a wildcard */
    public function testLikeCharactersInNamesAreLiterals()
    {
        foreach ( array( '100%.txt', '100X.txt', 'a_b.txt', 'aXb.txt', 'c\\d.txt', 'cd.txt', '[x].txt', 'x.txt' ) as $name )
            $this->backend->_storeContents( $this->path( "like/$name" ), $name, 'binaryfile', 'text/plain', 1700000000 );
        $this->assertSame( array( $this->path( 'like/100%.txt' ) ), $this->backend->_getFileList( false, false, false, $this->path( 'like/100%' ) ) );
        $this->assertSame( array( $this->path( 'like/a_b.txt' ) ), $this->backend->_getFileList( false, false, false, $this->path( 'like/a_' ) ) );
        $this->assertSame( array( $this->path( 'like/[x].txt' ) ), $this->backend->_getFileList( false, false, false, $this->path( 'like/[' ) ) );
        $this->assertTrue( $this->backend->_deleteByWildcard( $this->path( 'like/c\\d.t?t' ) ) );
        $this->assertFalse( $this->backend->_exists( $this->path( 'like/c\\d.txt' ) ) );
        $this->assertTrue( $this->backend->_exists( $this->path( 'like/cd.txt' ) ) );
    }

    /** A non-ASCII name is stored and found as it is */
    public function testUnicodeNames()
    {
        $path = $this->path( 'unicode/größe-😀.txt' );
        $this->backend->_storeContents( $path, 'ü', 'binaryfile', 'text/plain', 1700000000 );
        $this->assertTrue( $this->backend->_exists( $path ) );
        $this->assertSame( array( $path ), $this->backend->_getFileList( false, false, false, $this->path( 'unicode/gr' ) ) );
    }

    /** A schema without the tables: the kernel's TableNotFound exception, as on MySQL */
    public function testAMissingTableIsReported()
    {
        ezpINIHelper::setINISetting( 'file.ini', 'eZDFSClusteringSettings', 'MetaDataTableNameCache', 'ezdfsfile_none' );
        $backend = $this->newBackend();
        $this->expectException( eZDFSFileHandlerTableNotFoundException::class );
        $backend->_fetchMetadata( 'var/site/cache/none/x.cache' );
    }

    public function testAWrongPasswordIsAConnectionError()
    {
        ezpINIHelper::setINISetting( 'file.ini', 'eZDFSClusteringSettings', 'DBPassword', 'wrong' . bin2hex( random_bytes( 4 ) ) );
        try
        {
            $this->newBackend();
            $this->fail( 'no exception' );
        }
        catch ( eZClusterHandlerDBNoConnectionException $e )
        {
            $this->assertStringNotContainsString( self::$account['password'], $e->getMessage() );
        }
    }

    public function testGatewayServesMetadataAndRanges()
    {
        $path = $this->path( 'storage/original/application/file.bin' );
        $data = random_bytes( 150000 );
        $this->backend->_storeContents( $path, $data, 'binaryfile', 'application/octet-stream', 1700000000 );

        if ( !class_exists( 'ezpClusterGateway', false ) )
            require_once dirname( __DIR__, 6 ) . '/kernel/clustering/gateway.php';
        require_once dirname( __DIR__, 6 ) . '/kernel/clustering/dfssqlserver.php';
        if ( !defined( 'CLUSTER_MOUNT_POINT_PATH' ) )
            define( 'CLUSTER_MOUNT_POINT_PATH', realpath( $this->mount ) );
        if ( CLUSTER_MOUNT_POINT_PATH !== realpath( $this->mount ) )
            $this->markTestSkipped( 'CLUSTER_MOUNT_POINT_PATH is already defined for another mount' );
        foreach ( array( 'CLUSTER_SQLSERVER_LAYER' => self::$account['layer_name'], 'CLUSTER_SQLSERVER_TRUST_SERVER_CERTIFICATE' => true,
                         'CLUSTER_SQLSERVER_ODBC_DRIVER' => self::$account['odbc_driver'] ) as $constant => $value )
            if ( !defined( $constant ) )
                define( $constant, $value );

        $params = array( 'host' => self::$account['host'], 'port' => self::$account['port'], 'user' => self::$account['user'],
                         'password' => self::$account['password'], 'name' => self::$account['dfs_database'], 'charset' => 'utf-8' );
        $gateway = new expDfsSQLServerClusterGateway( $params );
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

        $wrong = 'wrong' . bin2hex( random_bytes( 4 ) );
        $gateway = new expDfsSQLServerClusterGateway( array( 'password' => $wrong ) + $params );
        try
        {
            $gateway->connect();
            $this->fail( 'a wrong password connected' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertStringStartsWith( 'Failed connecting to the SQL Server cluster database', $e->getMessage() );
            $this->assertStringNotContainsString( $wrong, $e->getMessage() );
        }
    }
}
