<?php
/**
 * The checks every DFS cluster metadata backend passes, whatever its engine: one behavior, several engines. A test
 * class of an engine uses this trait and supplies the throwaway database (engineSetUp(), engineTearDown()), the
 * file.ini settings to reach it (engineSettings()), the backend class (backendClass()) and a raw statement on it
 * (rawExec(), rawRows()). The kernel runs it for MySQL/MariaDB (expDFSMySQLiBackendParityTest), PostgreSQL
 * (expDFSPostgresqlBackendIntegrationTest), SQLite (expDFSSQLiteBackendIntegrationTest) and MongoDB
 * (expDFSMongoDBBackendIntegrationTest, whose rawExec() translates the few statements below); a backend for another
 * engine adds a class of its own (doc/eng-us/features/6.0/postgresql-dfs-cluster.md, "More engines"). An engine
 * without row locks says how a second connection holds the lock a store waits for (holdLock(), releaseLock()).
 *
 * Every file lives under var/tmp/dfsparity-<pid>/ (the local files and the DFS mount), removed after each test.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

trait expDFSBackendParityChecks
{
    /** @var string|null why the class is skipped (no server configured, cannot connect ...) */
    protected static $engineSkip = null;

    /** @var bool whether engineSetUp() ran for the class */
    protected static $engineReady = false;

    /** @var expDFSFileHandlerDBBackendInterface the backend of backendClass() */
    protected $backend;

    /** @var string the directory of the test's files, relative to the root: var/tmp/dfsparity-<pid> */
    protected $dir;

    /** @var string the DFS mount of the test */
    protected $mount;

    /** @var ezpEvent the event handler of the backend, a private one per test */
    protected $events;

    /** @return string the backend class */
    abstract protected function backendClass();

    /** @return string|null null when the engine's throwaway database is ready, else why the class is skipped */
    abstract protected static function engineSetUp();

    /** Drops the throwaway database */
    abstract protected static function engineTearDown();

    /** @return array file.ini [eZDFSClusteringSettings] DBHost, DBPort, DBSocket, DBName, DBUser, DBPassword */
    abstract protected function engineSettings();

    /** Runs a statement on the throwaway database, outside the backend */
    abstract protected function rawExec( $sql );

    /** @return array the rows of a SELECT on the throwaway database, outside the backend */
    abstract protected function rawRows( $sql );

    /** @return bool whether a lock wait ends after DBLockTimeout seconds and is retried by the backend */
    protected function supportsBoundedLockRetry()
    {
        return false;
    }

    /** @return bool whether mtime holds times after 2038 */
    protected function supportsTimesAfter2038()
    {
        return false;
    }

    /**
     * Takes, on the connection of $holder (a second backend), the lock a store of $path has to wait for, until
     * releaseLock(): the row's lock on a server engine (SELECT ... FOR UPDATE in a transaction).
     *
     * @param object $holder a connected backend of backendClass()
     * @param string $path
     */
    protected function holdLock( $holder, $path )
    {
        $holder->db->beginTransaction();
        $holder->db->query( "SELECT * FROM ezdfsfile WHERE name_hash = '" . md5( $path ) . "' FOR UPDATE" );
    }

    /** Releases the lock holdLock() took */
    protected function releaseLock( $holder )
    {
        $holder->db->commit();
    }

    /** @return string the error code the backend keeps when a lock wait ran out (PostgreSQL's SQLSTATE 55P03) */
    protected function lockWaitErrorCode()
    {
        return '55P03';
    }

    /** @return string|null the error code of the backend's last failed statement */
    protected function lastBackendErrorCode()
    {
        return $this->backend->lastSqlState();
    }

    public static function tearDownAfterClass(): void
    {
        if ( self::$engineReady )
            static::engineTearDown();
        self::$engineReady = false;
        self::$engineSkip = null;
        parent::tearDownAfterClass();
    }

    protected function setUp(): void
    {
        parent::setUp();
        if ( !self::$engineReady && self::$engineSkip === null )
        {
            self::$engineSkip = static::engineSetUp();
            self::$engineReady = self::$engineSkip === null;
        }
        if ( self::$engineSkip !== null )
            $this->markTestSkipped( self::$engineSkip );

        $this->dir = 'var/tmp/dfsparity-' . getmypid();
        $this->mount = $this->dir . '/mount';
        eZDir::mkdir( $this->mount, false, true );

        $settings = $this->engineSettings() + array(
            'MountPointPath' => $this->mount,
            'DBBackend' => $this->backendClass(),
            'DFSBackend' => 'eZDFSFileHandlerDFSBackend',
            'DBConnectRetries' => '1',
            'DBExecuteRetries' => '5',
            'DBLockTimeout' => '1',
            'MaxCopyRetries' => '3',
        );
        foreach ( $settings as $name => $value )
            ezpINIHelper::setINISetting( 'file.ini', 'eZDFSClusteringSettings', $name, $value );
        ezpINIHelper::setINISetting( 'site.ini', 'ContentSettings', 'CacheGenerationTimeout', '60' );

        self::resetStatic( 'eZDFSFileHandlerBackendFactory', 'instance' );
        $this->backend = $this->newBackend();
        $this->rawExec( 'DELETE FROM ezdfsfile' );
        $this->rawExec( 'DELETE FROM ezdfsfile_cache' );
    }

    protected function tearDown(): void
    {
        if ( $this->backend )
            $this->backend->_disconnect();
        $this->backend = null;
        ezpINIHelper::restoreINISettings();
        self::resetStatic( 'eZDFSFileHandlerBackendFactory', 'instance' );
        if ( $this->dir && strpos( $this->dir, 'var/tmp/dfsparity-' ) === 0 && is_dir( $this->dir ) )
            eZDir::recursiveDelete( $this->dir );
        parent::tearDown();
    }

    /**
     * A connected backend with fresh connection parameters and a private event handler.
     *
     * @return expDFSFileHandlerDBBackendInterface
     */
    protected function newBackend()
    {
        $class = $this->backendClass();
        self::resetStatic( $class, 'dbparams' );
        $backend = new $class();
        $this->events = new ezpEvent( false );
        self::setProperty( $backend, 'eventHandler', $this->events );
        $backend->_connect();
        return $backend;
    }

    /** Sets a static property of $class (or of the class declaring it) to null */
    protected static function resetStatic( $class, $property )
    {
        $reflection = new ReflectionProperty( $class, $property );
        if ( PHP_VERSION_ID < 80100 )
            $reflection->setAccessible( true );
        $reflection->setValue( null, null );
    }

    /** Sets a property of an object, whatever its visibility */
    protected static function setProperty( $object, $property, $value )
    {
        $reflection = new ReflectionProperty( $object, $property );
        if ( PHP_VERSION_ID < 80100 )
            $reflection->setAccessible( true );
        $reflection->setValue( $object, $value );
    }

    /** @return string a path of the test */
    protected function path( $name )
    {
        return $this->dir . '/' . $name;
    }

    /** @return bool whether the file is on the DFS mount */
    protected function onDFS( $path )
    {
        clearstatcache();
        return file_exists( $this->mount . '/' . $path );
    }

    public function testStoreContentsAndFetchMetadata()
    {
        $path = $this->path( 'files/hello.txt' );
        $this->assertNotFalse( $this->backend->_storeContents( $path, 'hello', 'binaryfile', 'text/plain', 1700000000 ) );

        $meta = $this->backend->_fetchMetadata( $path );
        $this->assertIsArray( $meta );
        $this->assertSame( $path, $meta['name'] );
        $this->assertSame( md5( $path ), trim( $meta['name_hash'] ) );
        $this->assertSame( '5', (string)$meta['size'] );
        $this->assertSame( '1700000000', (string)$meta['mtime'] );
        $this->assertSame( '0', (string)$meta['expired'] );
        $this->assertSame( 'text/plain', $meta['datatype'] );
        $this->assertSame( 'binaryfile', $meta['scope'] );
        $this->assertTrue( $this->backend->_exists( $path ) );
        $this->assertTrue( $this->backend->_exists( $path, false, true, true ) );
        $this->assertSame( 'hello', $this->backend->_fetchContents( $path ) );
        $this->assertFalse( $this->backend->_fetchMetadata( $this->path( 'files/none.txt' ) ) );
        $this->assertFalse( $this->backend->_exists( $this->path( 'files/none.txt' ) ) );
    }

    public function testStoreContentsAgainUpdatesTheOneRow()
    {
        $path = $this->path( 'files/again.txt' );
        $this->backend->_storeContents( $path, 'hello', 'binaryfile', 'text/plain', 1700000000 );
        $this->backend->_storeContents( $path, 'hello world', 'binaryfile', 'text/html', 1700000100 );

        $meta = $this->backend->_fetchMetadata( $path );
        $this->assertSame( '11', (string)$meta['size'] );
        $this->assertSame( '1700000100', (string)$meta['mtime'] );
        $this->assertSame( 'text/html', $meta['datatype'] );
        $this->assertSame( array( $path ), $this->backend->_getFileList( false, false, false, $this->path( 'files/' ) ) );
        $this->assertSame( 'hello world', $this->backend->_fetchContents( $path ) );
    }

    public function testStoreLocalFileAndFetchIt()
    {
        $path = $this->path( 'local/image.bin' );
        eZDir::mkdir( dirname( $path ), false, true );
        $data = random_bytes( 300000 );
        file_put_contents( $path, $data );
        $this->backend->_store( $path, 'application/octet-stream', 'image' );
        $this->assertTrue( $this->onDFS( $path ) );
        $this->assertSame( (string)strlen( $data ), (string)$this->backend->_fetchMetadata( $path )['size'] );

        unlink( $path );
        $this->assertSame( $path, $this->backend->_fetch( $path ) );
        $this->assertSame( $data, file_get_contents( $path ) );

        $unique = $this->backend->_fetch( $path, true );
        $this->assertNotSame( $path, $unique );
        $this->assertSame( $data, file_get_contents( $unique ) );
    }

    public function testPassThroughRange()
    {
        $path = $this->path( 'files/range.bin' );
        $data = random_bytes( 200000 );
        $this->backend->_storeContents( $path, $data, 'binaryfile', 'application/octet-stream' );
        ob_start();
        $this->assertTrue( $this->backend->_passThrough( $path, 1000, 5000 ) );
        $out = ob_get_clean();
        $this->assertSame( substr( $data, 1000, 5000 ), $out );
    }

    public function testTimesAfter2038()
    {
        if ( !$this->supportsTimesAfter2038() )
            $this->markTestSkipped( 'mtime of this engine is a 32 bit integer' );
        $path = $this->path( 'files/future.txt' );
        $this->assertNotFalse( $this->backend->_storeContents( $path, 'later', 'binaryfile', 'text/plain', 4102444800 ) );
        $this->assertSame( '4102444800', (string)$this->backend->_fetchMetadata( $path )['mtime'] );
        $this->backend->_delete( $path );
        $this->assertSame( '-4102444800', (string)$this->backend->_fetchMetadata( $path )['mtime'] );
    }

    public function testCopyAndLinkCopy()
    {
        $src = $this->path( 'files/src.txt' );
        $this->backend->_storeContents( $src, 'source', 'binaryfile', 'text/plain', 1700000000 );

        $this->assertTrue( $this->backend->_copy( $src, $this->path( 'files/copy.txt' ) ) );
        $this->assertTrue( $this->backend->_linkCopy( $src, $this->path( 'files/link.txt' ) ) );
        foreach ( array( 'copy.txt', 'link.txt' ) as $name )
        {
            $meta = $this->backend->_fetchMetadata( $this->path( "files/$name" ) );
            $this->assertSame( '6', (string)$meta['size'] );
            $this->assertSame( '1700000000', (string)$meta['mtime'] );
            $this->assertSame( 'source', $this->backend->_fetchContents( $this->path( "files/$name" ) ) );
        }
        $this->assertFalse( $this->backend->_copy( $this->path( 'files/none.txt' ), $this->path( 'files/x.txt' ) ) );
    }

    public function testRename()
    {
        $src = $this->path( 'files/old.txt' );
        $dst = $this->path( 'files/new.txt' );
        $this->backend->_storeContents( $src, 'moving', 'binaryfile', 'text/plain', 1700000000 );
        $this->backend->_storeContents( $dst, 'in the way', 'binaryfile', 'text/plain', 1600000000 );

        $this->assertTrue( $this->backend->_rename( $src, $dst ) );
        $this->assertFalse( $this->backend->_fetchMetadata( $src ) );
        $this->assertFalse( $this->onDFS( $src ) );
        $meta = $this->backend->_fetchMetadata( $dst );
        $this->assertSame( '1700000000', (string)$meta['mtime'] );
        $this->assertSame( 'moving', $this->backend->_fetchContents( $dst ) );
        $this->assertFalse( $this->backend->_rename( $this->path( 'files/none.txt' ), $dst ) );
        $this->assertNull( $this->backend->_rename( $dst, $dst ) );
    }

    public function testDeleteExpiresAndPurgeRemoves()
    {
        $path = $this->path( 'files/gone.txt' );
        $this->backend->_storeContents( $path, 'gone', 'binaryfile', 'text/plain', 1700000000 );
        $this->backend->_delete( $path );

        $this->assertFalse( $this->backend->_exists( $path ) );
        $this->assertTrue( $this->backend->_exists( $path, false, false ) );
        $meta = $this->backend->_fetchMetadata( $path );
        $this->assertSame( '-1700000000', (string)$meta['mtime'] );
        $this->assertSame( '1', (string)$meta['expired'] );
        $this->assertTrue( $this->onDFS( $path ) );

        // an expiry before the file's time keeps it
        $this->backend->_purge( $path, false, -1800000000 );
        $this->assertIsArray( $this->backend->_fetchMetadata( $path ) );

        $this->assertTrue( $this->backend->_purge( $path, true ) );
        $this->assertFalse( $this->backend->_fetchMetadata( $path ) );
        $this->assertFalse( $this->onDFS( $path ) );
    }

    public function testDeleteByLikeTreatsUnderscoreAsALiteral()
    {
        foreach ( array( 'item_1.txt', 'item_2.txt', 'itemX1.txt' ) as $name )
            $this->backend->_storeContents( $this->path( "like/$name" ), $name, 'binaryfile', 'text/plain', 1700000000 );

        $this->assertTrue( $this->backend->_deleteByLike( $this->path( 'like/item_%' ) ) );
        $this->assertFalse( $this->backend->_exists( $this->path( 'like/item_1.txt' ) ) );
        $this->assertFalse( $this->backend->_exists( $this->path( 'like/item_2.txt' ) ) );
        $this->assertTrue( $this->backend->_exists( $this->path( 'like/itemX1.txt' ) ) );

        $this->assertSame( 1, $this->backend->_purgeByLike( $this->path( 'like/item_%' ), true, 1 ) );
        $this->assertSame( 1, $this->backend->_purgeByLike( $this->path( 'like/item_%' ), true, 1 ) );
        $this->assertSame( 0, $this->backend->_purgeByLike( $this->path( 'like/item_%' ), true, 1 ) );
        $this->assertFalse( $this->onDFS( $this->path( 'like/item_1.txt' ) ) );
        $this->assertTrue( $this->backend->_exists( $this->path( 'like/itemX1.txt' ) ) );
    }

    public function testDeleteByWildcard()
    {
        foreach ( array( 'a.txt', 'b.txt', 'c.txt', 'aXtxt' ) as $name )
            $this->backend->_storeContents( $this->path( "wild/$name" ), $name, 'binaryfile', 'text/plain', 1700000000 );

        $this->assertTrue( $this->backend->_deleteByWildcard( $this->path( 'wild/{a,b}.txt' ) ) );
        $this->assertFalse( $this->backend->_exists( $this->path( 'wild/a.txt' ) ) );
        $this->assertFalse( $this->backend->_exists( $this->path( 'wild/b.txt' ) ) );
        $this->assertTrue( $this->backend->_exists( $this->path( 'wild/c.txt' ) ) );
        $this->assertTrue( $this->backend->_exists( $this->path( 'wild/aXtxt' ) ), 'the dot is a literal' );

        $this->assertTrue( $this->backend->_deleteByWildcard( $this->path( 'wild/?.t*' ) ) );
        $this->assertFalse( $this->backend->_exists( $this->path( 'wild/c.txt' ) ) );
        $this->assertTrue( $this->backend->_exists( $this->path( 'wild/aXtxt' ) ) );
    }

    public function testDeleteByDirListByNameAndByNameTrunk()
    {
        foreach ( array( 'd1/f1', 'd2/f2', 'd3/f3' ) as $name )
            $this->backend->_storeContents( $this->path( "dirs/$name" ), $name, 'binaryfile', 'text/plain', 1700000000 );
        $this->assertTrue( $this->backend->_deleteByDirList( array( 'd1', 'd2' ), $this->path( 'dirs' ), 'f' ) );
        $this->assertFalse( $this->backend->_exists( $this->path( 'dirs/d1/f1' ) ) );
        $this->assertFalse( $this->backend->_exists( $this->path( 'dirs/d2/f2' ) ) );
        $this->assertTrue( $this->backend->_exists( $this->path( 'dirs/d3/f3' ) ) );

        // view cache: the files of one node share a name_trunk (<dir>/<node>-), expired in one statement
        $viewCache = $this->path( 'cache/content/site/1/2' );
        $this->backend->_storeContents( "$viewCache/2-abc.cache", 'one', 'viewcache', 'text/plain', 1700000000 );
        $this->backend->_storeContents( "$viewCache/2-def.cache", 'two', 'viewcache', 'text/plain', 1700000000 );
        $this->backend->_storeContents( "$viewCache/3-abc.cache", 'three', 'viewcache', 'text/plain', 1700000000 );
        $this->assertTrue( $this->backend->_deleteByDirList( array( 'site' ), $this->path( 'cache/content' ), '1/2/2-' ) );
        $this->assertFalse( $this->backend->_exists( "$viewCache/2-abc.cache" ) );
        $this->assertFalse( $this->backend->_exists( "$viewCache/2-def.cache" ) );
        $this->assertTrue( $this->backend->_exists( "$viewCache/3-abc.cache" ) );
    }

    public function testCachePathsUseTheCacheTable()
    {
        $cache = $this->path( 'cache/template-block/x.cache' );
        $storage = $this->path( 'storage/images/x.png' );
        $this->backend->_storeContents( $cache, 'c', 'template-block', 'text/plain', 1700000000 );
        $this->backend->_storeContents( $storage, 's', 'image', 'image/png', 1700000000 );
        $this->assertCount( 1, $this->rawRows( "SELECT name FROM ezdfsfile_cache WHERE name_hash = '" . md5( $cache ) . "'" ) );
        $this->assertCount( 1, $this->rawRows( "SELECT name FROM ezdfsfile WHERE name_hash = '" . md5( $storage ) . "'" ) );
        $this->assertCount( 0, $this->rawRows( "SELECT name FROM ezdfsfile WHERE name_hash = '" . md5( $cache ) . "'" ) );
    }

    public function testGetFileListFiltersAndLimits()
    {
        $this->backend->_storeContents( $this->path( 'list/a.txt' ), 'a', 'binaryfile', 'text/plain', 1700000000 );
        $this->backend->_storeContents( $this->path( 'list/b.jpg' ), 'b', 'image', 'image/jpeg', 1700000000 );
        $this->backend->_storeContents( $this->path( 'list/c.jpg' ), 'c', 'image', 'image/jpeg', 1700000000 );
        $this->backend->_storeContents( $this->path( 'other/d.txt' ), 'd', 'binaryfile', 'text/plain', 1700000000 );

        $images = $this->backend->_getFileList( array( 'image' ) );
        sort( $images );
        $this->assertSame( array( $this->path( 'list/b.jpg' ), $this->path( 'list/c.jpg' ) ), $images );
        $notImages = $this->backend->_getFileList( array( 'image' ), true );
        sort( $notImages );
        $this->assertSame( array( $this->path( 'list/a.txt' ), $this->path( 'other/d.txt' ) ), $notImages );
        $this->assertSame( array( $this->path( 'list/a.txt' ) ), $this->backend->_getFileList( array( 'binaryfile' ), false, false, $this->path( 'list/' ) ) );
        $this->assertCount( 2, $this->backend->_getFileList( false, false, array( 1, 2 ) ) );
    }

    public function testExpiredFilesList()
    {
        $this->backend->_storeContents( $this->path( 'exp/a.txt' ), 'a', 'binaryfile', 'text/plain', time() - 1000 );
        $this->backend->_storeContents( $this->path( 'exp/b.jpg' ), 'b', 'image', 'image/jpeg', time() - 1000 );
        $this->backend->_storeContents( $this->path( 'exp/c.txt' ), 'c', 'binaryfile', 'text/plain', time() - 1000 );
        $this->backend->_delete( $this->path( 'exp/a.txt' ) );
        $this->backend->_delete( $this->path( 'exp/b.jpg' ) );

        $this->assertSame( array( $this->path( 'exp/a.txt' ) ), $this->backend->expiredFilesList( array( 'binaryfile' ) ) );
        $both = $this->backend->expiredFilesList( array( 'binaryfile', 'image' ), false );
        sort( $both );
        $this->assertSame( array( $this->path( 'exp/a.txt' ), $this->path( 'exp/b.jpg' ) ), $both );
    }

    public function testCacheGenerationHasOneWinner()
    {
        $path = $this->path( 'cache/gen/page.cache' );
        $first = $this->backend->_startCacheGeneration( $path, "$path.generating" );
        $this->assertSame( 'ok', $first['result'] );

        $other = $this->newBackend();
        $second = $other->_startCacheGeneration( $path, "$path.generating" );
        $this->assertSame( 'ko', $second['result'] );
        $this->assertGreaterThan( 0, $second['remaining'] );
        $this->assertLessThanOrEqual( 60, $second['remaining'] );
        $other->_disconnect();

        $this->backend->_storeContents( "$path.generating", 'generated', 'viewcache', 'text/plain' );
        $this->assertTrue( $this->backend->_endCacheGeneration( $path, "$path.generating", true ) );
        $this->assertSame( 'generated', $this->backend->_fetchContents( $path ) );
        $this->assertFalse( $this->backend->_fetchMetadata( "$path.generating" ) );
        $this->assertFalse( $this->onDFS( "$path.generating" ) );
    }

    public function testCacheGenerationOntoAnExpiredFile()
    {
        $path = $this->path( 'cache/gen/old.cache' );
        $this->backend->_storeContents( $path, 'old', 'viewcache', 'text/plain', time() - 100 );
        $this->backend->_delete( $path );

        $start = $this->backend->_startCacheGeneration( $path, "$path.generating" );
        $this->assertSame( 'ok', $start['result'] );
        $this->backend->_storeContents( "$path.generating", 'new contents', 'viewcache', 'text/plain' );
        $this->assertTrue( $this->backend->_endCacheGeneration( $path, "$path.generating", true ) );

        $meta = $this->backend->_fetchMetadata( $path );
        $this->assertSame( '0', (string)$meta['expired'] );
        $this->assertSame( '12', (string)$meta['size'] );
        $this->assertGreaterThan( 0, (int)$meta['mtime'] );
        $this->assertSame( 'new contents', $this->backend->_fetchContents( $path ) );
    }

    public function testCacheGenerationIsTakenOverAfterItsTimeout()
    {
        $path = $this->path( 'cache/gen/slow.cache' );
        $this->assertSame( 'ok', $this->backend->_startCacheGeneration( $path, "$path.generating" )['result'] );
        $this->rawExec( "UPDATE ezdfsfile_cache SET mtime = " . ( time() - 1000 ) . " WHERE name_hash = '" . md5( "$path.generating" ) . "'" );

        $other = $this->newBackend();
        $takeOver = $other->_startCacheGeneration( $path, "$path.generating" );
        $this->assertSame( 'ok', $takeOver['result'] );
        $other->_disconnect();
    }

    public function testCheckCacheGenerationTimeout()
    {
        $path = $this->path( 'cache/gen/check.cache' );
        $start = $this->backend->_startCacheGeneration( $path, "$path.generating" );
        $this->assertTrue( $this->backend->_checkCacheGenerationTimeout( "$path.generating", $start['mtime'] ) );

        $this->rawExec( "UPDATE ezdfsfile_cache SET mtime = 12345 WHERE name_hash = '" . md5( "$path.generating" ) . "'" );
        $this->assertFalse( $this->backend->_checkCacheGenerationTimeout( "$path.generating", $start['mtime'] ) );
    }

    public function testAbortAndEndWithoutRename()
    {
        $path = $this->path( 'cache/gen/abort.cache' );
        $this->backend->_startCacheGeneration( $path, "$path.generating" );
        $this->backend->_abortCacheGeneration( "$path.generating" );
        $this->assertFalse( $this->backend->_fetchMetadata( "$path.generating" ) );
        $this->assertSame( 'ok', $this->backend->_startCacheGeneration( $path, "$path.generating" )['result'] );
        $this->assertTrue( $this->backend->_endCacheGeneration( $path, "$path.generating", false ) );
        $this->assertFalse( $this->backend->_fetchMetadata( "$path.generating" ) );
        $this->assertFalse( $this->backend->_fetchMetadata( $path ) );
    }

    public function testEventsReachTheListener()
    {
        $listener = new expDFSParityRecordingListener();
        $this->backend->registerListener( $listener );
        $path = $this->path( 'files/events.txt' );
        $this->backend->_storeContents( $path, 'x', 'binaryfile', 'text/plain', 1700000000 );
        $this->backend->_fetchMetadata( $path );
        $this->backend->_delete( $path );
        $this->backend->_deleteByLike( $this->path( 'files/%' ) );

        $this->assertContains( 'storeMetadata', $listener->calls );
        $this->assertContains( 'deleteFile', $listener->calls );
        $this->assertContains( 'deleteByLike', $listener->calls );
        $this->assertContains( 'loadMetadata', $listener->calls );
        $this->assertInstanceOf( 'eZClusterEventNotifier', $this->backend );
        $this->assertInstanceOf( 'expDFSFileHandlerDBBackendInterface', $this->backend );
    }

    public function testDeleteCacheFilesMovesNoStorage()
    {
        $cacheRow = eZSys::cacheDirectory() . '/parity/' . getmypid() . '.cache';
        $storageRow = $this->path( 'storage/kept.txt' );
        $this->rawExec( "INSERT INTO ezdfsfile (name, name_trunk, name_hash, datatype, scope, size, mtime, expired) VALUES " .
                        "('$cacheRow', '$cacheRow', '" . md5( $cacheRow ) . "', 'text/plain', 'viewcache', 1, 1700000000, 0)" );
        $this->backend->_storeContents( $storageRow, 'k', 'binaryfile', 'text/plain', 1700000000 );

        $this->assertSame( 1, (int)$this->backend->deleteCacheFiles( 10 ) );
        $this->assertCount( 0, $this->rawRows( "SELECT name FROM ezdfsfile WHERE name_hash = '" . md5( $cacheRow ) . "'" ) );
        $this->assertTrue( $this->backend->_exists( $storageRow ) );
    }

    /**
     * Four processes ask for the generation of one cache file at the same moment: one generates, the others are
     * told to wait ('ko'), nobody fails or waits forever, and the file ends with the winner's contents.
     */
    public function testParallelGenerationOfOneKeyHasOneWinner()
    {
        if ( !function_exists( 'pcntl_fork' ) || !function_exists( 'posix_kill' ) )
            $this->markTestSkipped( 'pcntl and posix are needed for processes in parallel' );

        $path = $this->path( 'cache/gen/parallel.cache' );
        $results = $this->dir . '/parallel-results.txt';
        $this->backend->_disconnect();
        $start = microtime( true ) + 0.5;
        $children = array();
        for ( $i = 0; $i < 4; ++$i )
        {
            $pid = pcntl_fork();
            if ( $pid === 0 )
            {
                $line = "$i error";
                try
                {
                    $backend = $this->newBackend();
                    while ( microtime( true ) < $start )
                        usleep( 1000 );
                    $answer = $backend->_startCacheGeneration( $path, "$path.generating" );
                    $line = "$i " . $answer['result'];
                    if ( $answer['result'] === 'ok' )
                    {
                        usleep( 300000 );
                        $backend->_storeContents( "$path.generating", "generated by $i", 'viewcache', 'text/plain' );
                        $line .= $backend->_endCacheGeneration( $path, "$path.generating", true ) ? ' ended' : ' not-ended';
                    }
                }
                catch ( Exception $e )
                {
                    $line = "$i exception " . $e->getMessage();
                }
                file_put_contents( $results, "$line\n", FILE_APPEND | LOCK_EX );
                // no destructor: the parent's connections must stay open
                posix_kill( getmypid(), SIGKILL );
            }
            $children[] = $pid;
        }
        foreach ( $children as $pid )
            pcntl_waitpid( $pid, $status );

        $this->backend = $this->newBackend();
        $lines = file( $results, FILE_IGNORE_NEW_LINES );
        $this->assertCount( 4, $lines, implode( '; ', $lines ) );
        $winners = preg_grep( '/^\d ok ended$/', $lines );
        $this->assertCount( 1, $winners, implode( '; ', $lines ) );
        $this->assertCount( 3, preg_grep( '/^\d ko$/', $lines ), implode( '; ', $lines ) );
        $winner = (int)reset( $winners );
        $this->assertSame( "generated by $winner", $this->backend->_fetchContents( $path ) );
        $this->assertFalse( $this->backend->_fetchMetadata( "$path.generating" ) );
    }

    /**
     * A row held by another transaction: the store waits DBLockTimeout (1 s) per try, is retried, and succeeds as
     * soon as the lock is gone; while the lock stays, it gives up after DBExecuteRetries tries instead of hanging.
     */
    public function testLockWaitIsBoundedAndRetried()
    {
        if ( !$this->supportsBoundedLockRetry() )
            $this->markTestSkipped( 'the engine waits for its own lock timeout' );
        if ( !function_exists( 'pcntl_fork' ) || !function_exists( 'posix_kill' ) )
            $this->markTestSkipped( 'pcntl and posix are needed for processes in parallel' );

        $path = $this->path( 'files/locked.txt' );
        $this->backend->_storeContents( $path, 'before', 'binaryfile', 'text/plain', 1700000000 );
        $this->backend->_disconnect();

        $holdFor = 1.5;
        $pid = pcntl_fork();
        if ( $pid === 0 )
        {
            $holder = $this->newBackend();
            $this->holdLock( $holder, $path );
            usleep( (int)( $holdFor * 1000000 ) );
            $this->releaseLock( $holder );
            posix_kill( getmypid(), SIGKILL );
        }
        usleep( 300000 );
        $this->backend = $this->newBackend();
        $time = microtime( true );
        $this->assertNotFalse( $this->backend->_storeContents( $path, 'after', 'binaryfile', 'text/plain', 1700000100 ) );
        $time = microtime( true ) - $time;
        pcntl_waitpid( $pid, $status );
        $this->assertGreaterThan( 0.9, $time, 'the first try waited for the lock' );
        $this->assertLessThan( 4.0, $time, 'a later try succeeded once the lock was gone' );
        $this->assertSame( 'after', $this->backend->_fetchContents( $path ) );

        // a lock that stays: DBExecuteRetries (2 here) tries of 1 s each, then false
        ezpINIHelper::setINISetting( 'file.ini', 'eZDFSClusteringSettings', 'DBExecuteRetries', '2' );
        $this->backend->_disconnect();
        $pid = pcntl_fork();
        if ( $pid === 0 )
        {
            $holder = $this->newBackend();
            $this->holdLock( $holder, $path );
            usleep( 6000000 );
            posix_kill( getmypid(), SIGKILL );
        }
        usleep( 300000 );
        $this->backend = $this->newBackend();
        $time = microtime( true );
        $this->assertFalse( $this->backend->_storeContents( $path, 'blocked', 'binaryfile', 'text/plain', 1700000200 ) );
        $time = microtime( true ) - $time;
        posix_kill( $pid, SIGKILL );
        pcntl_waitpid( $pid, $status );
        $this->assertGreaterThan( 1.9, $time );
        $this->assertLessThan( 4.0, $time );
        $this->assertSame( $this->lockWaitErrorCode(), $this->lastBackendErrorCode() );
    }
}

/**
 * A cluster event listener that records which events reached it.
 */
class expDFSParityRecordingListener implements eZClusterEventListener
{
    /** @var string[] */
    public $calls = array();

    public function initialize() { $this->calls[] = 'initialize'; }
    public function loadMetadata( $filepath ) { $this->calls[] = 'loadMetadata'; return false; }
    public function storeMetadata( array $metadata ) { $this->calls[] = 'storeMetadata'; }
    public function fileExists( $filepath ) { $this->calls[] = 'fileExists'; return false; }
    public function deleteFile( $filepath ) { $this->calls[] = 'deleteFile'; }
    public function deleteByLike( $like ) { $this->calls[] = 'deleteByLike'; }
    public function deleteByWildcard( $wildcard ) { $this->calls[] = 'deleteByWildcard'; }
    public function deleteByDirList( array $dirList, $commonPath, $commonSuffix ) { $this->calls[] = 'deleteByDirList'; }
    public function deleteByNametrunk( $nametrunk ) { $this->calls[] = 'deleteByNametrunk'; }
}
