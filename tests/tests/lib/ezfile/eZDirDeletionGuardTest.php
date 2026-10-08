<?php
/**
 * The guard of eZDir::recursiveDelete() and the caches that are cleared through it, for every place site.ini
 * [FileSettings] may put the cache of a site:
 *
 *  - the default layout, VarDir/CacheDir inside the root;
 *  - a CacheVarDir inside the root, also when it is a link to another file system (a memory file system);
 *  - an absolute CacheDir or CacheVarDir outside the root.
 *
 * In every layout what is inside the cache directory may be deleted, and the cache directory as a whole; nothing
 * beside it, never the root of the installation or a directory that contains it.
 *
 * Every test runs in its own process, because it changes the global site.ini.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

#[RunTestsInSeparateProcesses]
class eZDirDeletionGuardTest extends PHPUnit\Framework\TestCase
{
    private $dirs = array();

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
    }

    protected function tearDown(): void
    {
        foreach ( array_reverse( $this->dirs ) as $dir )
        {
            self::removeTree( $dir );
        }
        parent::tearDown();
    }

    /** Removes what a test made, without the guard under test; a link is removed, never followed */
    private static function removeTree( $dir )
    {
        if ( is_link( $dir ) || !is_dir( $dir ) )
        {
            @unlink( $dir );
            return;
        }
        foreach ( scandir( $dir ) as $entry )
        {
            if ( $entry !== '.' && $entry !== '..' )
            {
                self::removeTree( $dir . '/' . $entry );
            }
        }
        @rmdir( $dir );
    }

    private static function root()
    {
        return realpath( eZSys::rootDir() );
    }

    /** A new directory outside the installation, or a skip where the system temp directory is inside it */
    private function outsideDir( $name )
    {
        $dir = sys_get_temp_dir() . '/x1-deletion-guard-' . $name . '-' . uniqid();
        mkdir( $dir, 0777, true );
        $this->dirs[] = $dir;
        if ( strpos( realpath( $dir ) . '/', self::root() . '/' ) === 0 )
        {
            $this->markTestSkipped( 'The system temp directory is inside the installation' );
        }
        return realpath( $dir );
    }

    /** A name for a new directory directly inside the root (relative to it), removed after the test */
    private function insideName( $prefix )
    {
        $name = $prefix . '-' . uniqid();
        $this->dirs[] = self::root() . '/' . $name;
        return $name;
    }

    /** Sets FileSettings, with the cache, log and expiry settings of the defaults unless given */
    private function useSite( array $fileSettings )
    {
        $ini = eZINI::instance();
        $fileSettings += array( 'VarDir' => 'var', 'CacheDir' => 'cache', 'LogDir' => 'log',
                                'CacheVarDir' => '', 'LogVarDir' => '', 'ExpiryDir' => '' );
        foreach ( $fileSettings as $name => $value )
        {
            $ini->setVariable( 'FileSettings', $name, $value );
        }
    }

    /** Makes $dir/<each of $subDirs> with a file in it */
    private static function fill( $dir, array $subDirs )
    {
        foreach ( $subDirs as $sub )
        {
            mkdir( $dir . '/' . $sub, 0777, true );
            touch( $dir . '/' . $sub . '/x1.txt' );
        }
    }

    private static function call( $method )
    {
        $reflection = new ReflectionMethod( 'eZDir', $method );
        if ( PHP_VERSION_ID < 80100 )
            $reflection->setAccessible( true );
        return $reflection->invoke( null );
    }

    /** VarDir inside the root, as installed: inside the cache, and the cache directory as a whole */
    public function testTheDefaultLayout()
    {
        $varDir = 'var/x1-guard-site-' . uniqid();
        $this->dirs[] = self::root() . '/' . $varDir;
        $this->useSite( array( 'VarDir' => $varDir ) );
        $cache = eZSys::cacheDirectory();
        $this->assertSame( $varDir . '/cache', $cache );
        self::fill( $cache, array( 'template/compiled', 'ini' ) );
        self::fill( $varDir, array( 'log/old' ) );

        $this->assertTrue( eZDir::recursiveDelete( $cache . '/template' ) );
        $this->assertDirectoryDoesNotExist( $cache . '/template' );
        $this->assertTrue( eZDir::recursiveDelete( $varDir . '/log/old' ) );
        $this->assertTrue( eZDir::recursiveDelete( $cache ) );
        $this->assertDirectoryDoesNotExist( $cache );
    }

    /** CacheVarDir=var_cache inside the root: the cache of the site is var_cache/<site>/cache */
    public function testARelativeCacheVarDirInsideTheRoot()
    {
        $cacheVarDir = $this->insideName( 'x1-guard-var_cache' );
        $this->useSite( array( 'VarDir' => 'var/x1site', 'CacheVarDir' => $cacheVarDir ) );
        $cache = eZSys::cacheDirectory();
        $this->assertSame( $cacheVarDir . '/x1site/cache', $cache );
        self::fill( $cache, array( 'template/compiled', 'override' ) );

        $this->assertTrue( eZDir::recursiveDelete( $cache . '/template' ) );
        $this->assertDirectoryDoesNotExist( $cache . '/template' );
        $this->assertTrue( eZDir::recursiveDelete( $cache ) );
        $this->assertDirectoryDoesNotExist( $cache );
    }

    /**
     * CacheVarDir inside the root that is a link to another file system, as for a memory file system shared by every
     * site: the cache of the site is cleared, also as a whole, and nothing else on that file system is deleted
     */
    public function testACacheVarDirLinkedOutsideTheRoot()
    {
        $target = $this->outsideDir( 'memory' );
        self::fill( $target, array( 'x1site/cache/template/compiled', 'x1site/cache/override', 'other/cache/template' ) );
        $cacheVarDir = $this->insideName( 'x1-guard-var_cache' );
        symlink( $target, self::root() . '/' . $cacheVarDir );
        $this->useSite( array( 'VarDir' => 'var/x1site', 'CacheVarDir' => $cacheVarDir ) );
        $cache = eZSys::cacheDirectory();
        $this->assertSame( $cacheVarDir . '/x1site/cache', $cache );

        $this->assertTrue( eZDir::recursiveDelete( $cache . '/template' ) );
        $this->assertDirectoryDoesNotExist( $target . '/x1site/cache/template' );
        $this->assertTrue( eZDir::recursiveDelete( $cache ) );
        $this->assertDirectoryDoesNotExist( $target . '/x1site/cache' );

        // The cache of another site, and the tree above the cache, stay
        $this->assertFalse( eZDir::recursiveDelete( $cacheVarDir . '/other/cache/template' ) );
        $this->assertFalse( eZDir::recursiveDelete( $cacheVarDir . '/other' ) );
        $this->assertFileExists( $target . '/other/cache/template/x1.txt' );
        $this->assertFalse( eZDir::recursiveDelete( $cacheVarDir . '/x1site' ) );
        $this->assertDirectoryExists( $target . '/x1site' );
    }

    /** An absolute CacheDir outside the root: removed as a whole, its parent and what is beside it are not */
    public function testAnAbsoluteCacheDirOutsideTheRootIsRemovedAsAWhole()
    {
        $outside = $this->outsideDir( 'outside' );
        self::fill( $outside, array( 'cache/template/compiled', 'log/old', 'beside/sub' ) );
        $this->useSite( array( 'CacheDir' => $outside . '/cache', 'LogDir' => $outside . '/log' ) );

        $this->assertTrue( eZDir::recursiveDelete( $outside . '/cache' ) );
        $this->assertDirectoryDoesNotExist( $outside . '/cache' );

        $this->assertFalse( eZDir::recursiveDelete( $outside ) );
        $this->assertFalse( eZDir::recursiveDelete( $outside . '/beside' ) );
        $this->assertFalse( eZDir::recursiveDelete( $outside . '/log' ) );
        $this->assertFileExists( $outside . '/beside/sub/x1.txt' );
        $this->assertFileExists( $outside . '/log/old/x1.txt' );
    }

    /** An absolute CacheVarDir: the cache of the site goes, the directory of the site in that tree stays */
    public function testAnAbsoluteCacheVarDirOutsideTheRoot()
    {
        $outside = $this->outsideDir( 'var_cache' );
        self::fill( $outside, array( 'x1site/cache/template/compiled', 'x1site/other' ) );
        $this->useSite( array( 'VarDir' => 'var/x1site', 'CacheVarDir' => $outside ) );
        $this->assertSame( $outside . '/x1site/cache', eZSys::cacheDirectory() );

        $this->assertTrue( eZDir::recursiveDelete( $outside . '/x1site/cache/template' ) );
        $this->assertTrue( eZDir::recursiveDelete( $outside . '/x1site/cache' ) );
        $this->assertDirectoryDoesNotExist( $outside . '/x1site/cache' );
        $this->assertFalse( eZDir::recursiveDelete( $outside . '/x1site/other' ) );
        $this->assertFalse( eZDir::recursiveDelete( $outside . '/x1site' ) );
        $this->assertDirectoryExists( $outside . '/x1site/other' );
    }

    /** The cache clears that end in recursiveDelete() (site.ini [FileSettings] RenameBeforeDelete=disabled), outside the root */
    public function testCacheClearsWithAnAbsoluteCacheDirAndNoRename()
    {
        $outside = $this->outsideDir( 'outside' );
        self::fill( $outside, array( 'cache/codepages', 'cache/template/compiled', 'cache/override', 'cache/packages/import/x' ) );
        $this->useSite( array( 'CacheDir' => $outside . '/cache', 'RenameBeforeDelete' => 'disabled' ) );
        unset( $GLOBALS['eZTemplateCompilerDirectory'], $GLOBALS['eZTemplateCompilerSettings'] );

        eZCache::clearItem( array( 'name' => 'Codepage cache', 'id' => 'codepage', 'path' => 'codepages' ) );
        $this->assertDirectoryDoesNotExist( $outside . '/cache/codepages' );

        eZCache::clearTemplateCompileCache();
        $this->assertDirectoryDoesNotExist( $outside . '/cache/template/compiled' );

        eZCache::clearTemplateOverrideCache( array( 'path' => 'override' ) );
        $this->assertDirectoryDoesNotExist( $outside . '/cache/override' );

        eZPackage::removeFiles( eZPackage::temporaryImportPath() . '/x' );
        $this->assertDirectoryDoesNotExist( $outside . '/cache/packages/import/x' );

        // The whole cache directory, as a clear of everything with no trash may do
        eZCache::removeDirectory( eZSys::cacheDirectory() );
        $this->assertDirectoryDoesNotExist( $outside . '/cache' );
    }

    /** The same clears renamed aside first (the default), outside the root */
    public function testCacheClearsWithAnAbsoluteCacheDirRenamedAside()
    {
        $outside = $this->outsideDir( 'outside' );
        self::fill( $outside, array( 'cache/codepages', 'cache/template/compiled' ) );
        $this->useSite( array( 'CacheDir' => $outside . '/cache', 'RenameBeforeDelete' => 'enabled' ) );
        unset( $GLOBALS['eZTemplateCompilerDirectory'], $GLOBALS['eZTemplateCompilerSettings'] );

        eZCacheTrash::begin();
        eZCache::clearItem( array( 'name' => 'Codepage cache', 'id' => 'codepage', 'path' => 'codepages' ) );
        eZCache::clearTemplateCompileCache();
        eZCacheTrash::end();

        $this->assertDirectoryDoesNotExist( $outside . '/cache/codepages' );
        $this->assertDirectoryDoesNotExist( $outside . '/cache/template/compiled' );
        $this->assertSame( array( '.', '..' ), scandir( $outside . '/cache/.cleanup-trash' ) );
    }

    /**
     * A LogDir (or CacheDir) that contains the installation allows nothing: the root and everything beside it under
     * that directory would otherwise be deletable
     */
    public function testADirectoryThatContainsTheRootIsNoSiteDirectory()
    {
        $outside = $this->outsideDir( 'beside-root' );
        self::fill( $outside, array( 'sub' ) );
        // The nearest directory that holds both the installation and the temp directory
        $ancestor = self::root();
        while ( strpos( $outside . '/', rtrim( $ancestor, '/' ) . '/' ) !== 0 )
        {
            $ancestor = dirname( $ancestor );
        }
        if ( $ancestor === dirname( $ancestor ) )
        {
            $this->markTestSkipped( 'The installation and the temp directory share only the file system root' );
        }
        $this->useSite( array( 'LogDir' => $ancestor, 'CacheDir' => self::root() ) );

        $this->assertNotContains( $ancestor, self::call( 'siteDirectories' ) );
        $this->assertNotContains( self::root(), self::call( 'siteDirectories' ) );
        $this->assertFalse( self::call( 'siteCacheDirectory' ) );
        $this->assertFalse( eZDir::recursiveDelete( $outside . '/sub' ) );
        $this->assertFileExists( $outside . '/sub/x1.txt' );
    }

    /** A cache directory that does not exist allows nothing, and a path that does not exist is nothing to delete */
    public function testACacheDirectoryThatDoesNotExist()
    {
        $outside = $this->outsideDir( 'outside' );
        self::fill( $outside, array( 'other' ) );
        $this->useSite( array( 'CacheDir' => $outside . '/missing' ) );

        $this->assertFalse( self::call( 'siteCacheDirectory' ) );
        $this->assertTrue( eZDir::recursiveDelete( $outside . '/missing' ) );
        $this->assertFalse( eZDir::recursiveDelete( $outside . '/other' ) );
        $this->assertDirectoryExists( $outside . '/other' );
    }
}
