<?php
/**
 * The SQLite DFS cluster backend (expDFSFileHandlerSQLiteBackend) on a real SQLite database: the parity checks every
 * backend passes (expDFSBackendParityChecks), including four processes on one cache key and a bounded wait for the
 * write lock, plus the SQLite gateway of index_cluster.php, the case sensitivity of the name searches and the use of
 * the name index.
 *
 * Needs no server: the class makes var/tmp/dfssqlite-<pid>-<random>/cluster.db with
 * kernel/sql/sqlite/cluster_dfs_schema.sql and removes it after the last test. Skipped without pdo_sqlite.
 *   php vendor/bin/phpunit tests/tests/kernel/classes/clusterfilehandlers/dfsbackends/expDFSSQLiteBackendIntegrationTest.php
 *
 * SQLite is one file on one machine: the processes of the tests share it as the PHP servers of one host would. A
 * cluster of several machines is not something SQLite can be (doc/eng-us/features/6.0/sqlite-dfs-cluster.md).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group cluster-live
 */

require_once __DIR__ . '/expDFSBackendParityChecks.php';

#[\PHPUnit\Framework\Attributes\Group('cluster-live')]
class expDFSSQLiteBackendIntegrationTest extends ezpTestCase
{
    use expDFSBackendParityChecks;

    /** @var string|null the folder of the test database */
    private static $folder = null;

    /** @var string|null the absolute path of the test database */
    private static $file = null;

    /** @var PDO|null a connection to the test database, outside the backend */
    private static $raw = null;

    protected function backendClass()
    {
        return 'expDFSFileHandlerSQLiteBackend';
    }

    protected function supportsBoundedLockRetry()
    {
        return true;
    }

    protected function supportsTimesAfter2038()
    {
        return true;
    }

    /** The write lock of the whole database: SQLite has no row locks */
    protected function holdLock( $holder, $path )
    {
        $holder->db->exec( 'BEGIN IMMEDIATE' );
    }

    protected function releaseLock( $holder )
    {
        $holder->db->exec( 'COMMIT' );
    }

    /** SQLITE_BUSY */
    protected function lockWaitErrorCode()
    {
        return '5';
    }

    protected function lastBackendErrorCode()
    {
        return $this->backend->lastErrorCode();
    }

    /** @return PDO */
    private static function connect( $file )
    {
        $db = new PDO( 'sqlite:' . $file, null, null, array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 10 ) );
        $db->exec( 'PRAGMA busy_timeout = 10000' );
        return $db;
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
        if ( !extension_loaded( 'pdo_sqlite' ) )
            return 'the PHP extension pdo_sqlite is not loaded';
        $root = dirname( __DIR__, 6 );
        $folder = "$root/var/tmp/dfssqlite-" . getmypid() . '-' . bin2hex( random_bytes( 3 ) );
        if ( !is_dir( $folder ) && !mkdir( $folder, 0700, true ) )
            return "Cannot make $folder";
        self::$folder = $folder;
        self::$file = "$folder/cluster.db";
        try
        {
            self::$raw = self::connect( self::$file );
            self::runSqlFile( self::$raw, "$root/kernel/sql/sqlite/cluster_dfs_schema.sql" );
        }
        catch ( PDOException $e )
        {
            return 'Cannot prepare a SQLite test database: ' . $e->getMessage();
        }
        return null;
    }

    protected static function engineTearDown()
    {
        self::$raw = null;
        if ( self::$folder !== null && strpos( basename( self::$folder ), 'dfssqlite-' ) === 0 && is_dir( self::$folder ) )
        {
            foreach ( (array)glob( self::$folder . '/*' ) as $file )
                @unlink( $file );
            @rmdir( self::$folder );
        }
        self::$folder = null;
        self::$file = null;
    }

    protected function engineSettings()
    {
        return array(
            'DBHost' => '',
            'DBPort' => '',
            'DBSocket' => '',
            'DBName' => self::$file,
            'DBUser' => '',
            'DBPassword' => '',
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

    public function testSchemaUses64BitIntegersAndTheDatabaseIsInWalMode()
    {
        foreach ( array( 'ezdfsfile', 'ezdfsfile_cache' ) as $table )
        {
            $types = array_column( $this->rawRows( "PRAGMA table_info($table)" ), 'type', 'name' );
            $this->assertSame( 'INTEGER', $types['size'] );
            $this->assertSame( 'INTEGER', $types['mtime'] );
        }
        $this->assertSame( 'wal', strtolower( (string)$this->backend->db->query( 'PRAGMA journal_mode' )->fetchColumn() ) );
        $this->assertSame( '1000', (string)$this->backend->db->query( 'PRAGMA busy_timeout' )->fetchColumn() );
    }

    /**
     * The GLOB prefix search the purges and deletes run uses the name index, so a purge of var/<site>/cache/* does
     * not read the table.
     */
    public function testPrefixSearchUsesTheIndex()
    {
        $glob = expDFSFileHandlerSQLiteBackend::globPrefix( 'var/site/cache/' );
        $plan = implode( "\n", array_column( $this->rawRows( "EXPLAIN QUERY PLAN SELECT name FROM ezdfsfile WHERE name GLOB '$glob'" ), 'detail' ) );
        $this->assertMatchesRegularExpression( '/INDEX ezdfsfile_(expired_)?name\b/', $plan );
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

    /** GLOB's own wildcards in a name are literals of a LIKE pattern and of a prefix */
    public function testGlobCharactersInNamesAreLiterals()
    {
        if ( PHP_OS_FAMILY === 'Windows' )
            $this->markTestSkipped( 'a Windows file name cannot hold * or ?, and the backend keeps a copy of each file' );
        foreach ( array( 'a[1].txt', 'a1.txt', 'b*.txt', 'bX.txt' ) as $name )
            $this->backend->_storeContents( $this->path( "glob/$name" ), $name, 'binaryfile', 'text/plain', 1700000000 );
        $this->assertTrue( $this->backend->_deleteByLike( $this->path( 'glob/a[1]%' ) ) );
        $this->assertFalse( $this->backend->_exists( $this->path( 'glob/a[1].txt' ) ) );
        $this->assertTrue( $this->backend->_exists( $this->path( 'glob/a1.txt' ) ) );
        $this->assertSame( array( $this->path( 'glob/b*.txt' ) ), $this->backend->_getFileList( false, false, false, $this->path( 'glob/b*' ) ) );
    }

    /** A DBName that names no existing file is a connection error, not a new empty database */
    public function testAMissingDatabaseFileIsAConnectionError()
    {
        $missing = self::$folder . '/none.db';
        ezpINIHelper::setINISetting( 'file.ini', 'eZDFSClusteringSettings', 'DBName', $missing );
        try
        {
            $this->newBackend();
            $this->fail( 'connected to a database that does not exist' );
        }
        catch ( eZClusterHandlerDBNoConnectionException $e )
        {
            $this->assertFileDoesNotExist( $missing );
        }
    }

    public function testGatewayServesMetadataAndRanges()
    {
        $path = $this->path( 'storage/original/application/file.bin' );
        $data = random_bytes( 150000 );
        $this->backend->_storeContents( $path, $data, 'binaryfile', 'application/octet-stream', 1700000000 );

        if ( !class_exists( 'ezpClusterGateway', false ) )
            require_once dirname( __DIR__, 6 ) . '/kernel/clustering/gateway.php';
        require_once dirname( __DIR__, 6 ) . '/kernel/clustering/dfssqlite.php';
        if ( !defined( 'CLUSTER_MOUNT_POINT_PATH' ) )
            define( 'CLUSTER_MOUNT_POINT_PATH', realpath( $this->mount ) );
        if ( CLUSTER_MOUNT_POINT_PATH !== realpath( $this->mount ) )
            $this->markTestSkipped( 'CLUSTER_MOUNT_POINT_PATH is already defined for another mount' );

        $gateway = new expDfsSQLiteClusterGateway( array( 'name' => self::$file, 'charset' => 'utf-8' ) );
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

        $gateway = new expDfsSQLiteClusterGateway( array( 'name' => self::$folder . '/none.db' ) );
        $this->expectException( RuntimeException::class );
        $gateway->connect();
    }
}
