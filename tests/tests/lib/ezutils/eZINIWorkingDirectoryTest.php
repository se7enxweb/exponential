<?php
/**
 * eZINI reads the base file of a settings file from the installation root, but its override directories relative
 * to the working directory, and keeps its cache below the installation root. A process working elsewhere reads
 * other files, so it gets a cache file of its own; in the usual case, working in the installation root or in a
 * directory that resolves to it, the cache file name is the same as before.
 *
 * Before, a process working in a temporary root without settings (the Velocity warm-up test) stored a site.ini
 * without settings/override under the name every process uses, and the site, the admin and every script read that
 * until the INI cache was cleared.
 *
 * The tests work on a settings tree and a cache directory below var/tmp of the installation root (run them from
 * that root) and change the working directory only within a test.
 *
 * Run: php vendor/bin/phpunit tests/tests/lib/ezutils/eZINIWorkingDirectoryTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class eZINIWorkingDirectoryTest extends PHPUnit\Framework\TestCase
{
    /**
     * Directory of a test, relative to the installation root
     *
     * @var string
     */
    private $testDir;

    /**
     * Settings directory of a test, relative to the installation root
     *
     * @var string
     */
    private $rootDir;

    /**
     * @var string
     */
    private $cwd;

    /**
     * @var array{0: bool, 1: mixed} whether the INI cache directory was set, and to what
     */
    private $cacheDir;

    protected function setUp(): void
    {
        $this->testDir = 'var/tmp/ezini_workdir_' . str_replace( '.', '', uniqid( '', true ) );
        $this->rootDir = $this->testDir . '/settings';
        $this->writeFile( 'settings/test.ini', "[Values]\nLayer=base\n" );
        $this->writeFile( 'settings/override/test.ini.append.php', "<?php /*\n[Values]\nLayer=override\n*/ ?>\n" );
        mkdir( $this->root() . $this->testDir . '/elsewhere', 0777, true );
        $this->cwd = getcwd();
        $this->cacheDir = array( array_key_exists( 'eZINI_CONFIG_CACHE_DIR', $GLOBALS ), $GLOBALS['eZINI_CONFIG_CACHE_DIR'] ?? null );
        $GLOBALS['eZINI_CONFIG_CACHE_DIR'] = $this->root() . $this->testDir . '/cache/';
        $this->setEnvironment( false );
        chdir( $this->root() );
    }

    protected function tearDown(): void
    {
        chdir( $this->cwd );
        if ( $this->cacheDir[0] )
            $GLOBALS['eZINI_CONFIG_CACHE_DIR'] = $this->cacheDir[1];
        else
            unset( $GLOBALS['eZINI_CONFIG_CACHE_DIR'] );
        eZINI::resetEnvironment();
        $this->removeDir( $this->root() . $this->testDir );
    }

    public function testCacheFileNameInTheInstallationRootIsUnchanged()
    {
        $ini = $this->ini();
        $expected = $ini->FileName . '-' . $ini->RootDir . '-' . $ini->DirectAccess .
                    '-' . serialize( $ini->overrideDirs() ) .
                    '-' . eZTextCodec::internalCharset();
        $this->assertSame( 'test-' . md5( $expected ) . '.php', $this->cacheFileName( $ini ) );
    }

    public function testAnotherWorkingDirectoryGetsItsOwnCacheFile()
    {
        $inRoot = $this->cacheFileName( $this->ini() );
        $elsewhere = $this->root() . $this->testDir . '/elsewhere';
        chdir( $elsewhere );
        $this->assertNotSame( $inRoot, $this->cacheFileName( $this->ini() ) );
        $this->assertSame( 'missing:' . getcwd(), $this->workingSettingsDirectory( $this->ini() ) );
    }

    public function testADirectoryThatResolvesToTheInstallationCountsAsTheSame()
    {
        $inRoot = $this->cacheFileName( $this->ini() );
        $elsewhere = $this->root() . $this->testDir . '/elsewhere';
        // var of the working directory is a link to the installation's: the settings read are the same files
        if ( !function_exists( 'symlink' ) || !@symlink( $this->root() . 'var', $elsewhere . '/var' ) )
            $this->markTestSkipped( 'symbolic links cannot be made here' );
        chdir( $elsewhere );
        $this->assertFalse( $this->workingSettingsDirectory( $this->ini() ) );
        $this->assertSame( $inRoot, $this->cacheFileName( $this->ini() ) );
    }

    public function testAnAbsoluteSettingsDirectoryIsTheSameEverywhere()
    {
        $ini = new eZINI( 'test.ini', $this->root() . $this->rootDir, null, false, true, false, false, false );
        $this->assertFalse( $this->workingSettingsDirectory( $ini ) );
        chdir( $this->root() . $this->testDir . '/elsewhere' );
        $this->assertFalse( $this->workingSettingsDirectory( $ini ) );
    }

    /**
     * The case that happened: the cache written while working elsewhere, where no override was found, is not the
     * one a process in the installation root reads.
     */
    public function testWhatIsCachedElsewhereIsNotReadInTheInstallationRoot()
    {
        chdir( $this->root() . $this->testDir . '/elsewhere' );
        $this->assertSame( 'base', $this->ini( true, true )->variable( 'Values', 'Layer' ) );

        chdir( $this->root() );
        $this->assertSame( 'override', $this->ini( true, true )->variable( 'Values', 'Layer' ) );
        // and the other way round, from the cache this time
        $this->assertSame( 'override', $this->ini( true, true )->variable( 'Values', 'Layer' ) );
        $this->assertCount( 2, glob( $GLOBALS['eZINI_CONFIG_CACHE_DIR'] . 'test-*.php' ) );
    }

    /**
     * Sets the environment eZINI uses, as if EXP_ENV had been read
     *
     * @param string|false $environment
     */
    private function setEnvironment( $environment )
    {
        $property = new ReflectionProperty( 'eZINI', 'environment' );
        if ( PHP_VERSION_ID < 80100 )
            $property->setAccessible( true );
        $property->setValue( null, $environment );
    }

    /**
     * Returns an eZINI for test.ini in the test tree with an override dir
     *
     * @param bool $load
     * @param bool $useCache
     * @return eZINI
     */
    private function ini( $load = false, $useCache = false )
    {
        $ini = new eZINI( 'test.ini', $this->rootDir, null, $useCache, true, false, false, false );
        $ini->setOverrideDirs( array( 'sa-extension' => array(),
                                      'siteaccess' => array(),
                                      'extension' => array(),
                                      'override' => array( array( 'override', false ) ) ) );
        if ( $load )
        {
            // Loading can set eZDebug up, which resets the PHP error handler; the
            // handler found before is put back, so a test leaves it as it found it.
            $probe = function() { return false; };
            $handler = set_error_handler( $probe );
            restore_error_handler();
            $ini->load();
            $current = set_error_handler( $probe );
            restore_error_handler();
            if ( $current !== $handler && $handler !== null )
                set_error_handler( $handler );
        }
        return $ini;
    }

    /**
     * @param eZINI $ini
     * @return string
     */
    private function cacheFileName( $ini )
    {
        $method = new ReflectionMethod( 'eZINI', 'cacheFileName' );
        if ( PHP_VERSION_ID < 80100 )
            $method->setAccessible( true );
        return $method->invoke( $ini );
    }

    /**
     * @param eZINI $ini
     * @return string|false
     */
    private function workingSettingsDirectory( $ini )
    {
        $method = new ReflectionMethod( 'eZINI', 'workingSettingsDirectory' );
        if ( PHP_VERSION_ID < 80100 )
            $method->setAccessible( true );
        return $method->invoke( $ini );
    }

    /**
     * Returns the installation root eZINI reads the base settings from, with a trailing slash
     *
     * @return string
     */
    private function root()
    {
        if ( defined( 'EXP_ROOT_DIR' ) )
            return EXP_ROOT_DIR . '/';
        return realpath( __DIR__ . '/../../../../' ) . '/';
    }

    /**
     * @param string $relativePath path below the test directory
     * @param string $content
     */
    private function writeFile( $relativePath, $content )
    {
        $path = $this->root() . $this->testDir . '/' . $relativePath;
        if ( !is_dir( dirname( $path ) ) )
            mkdir( dirname( $path ), 0777, true );
        file_put_contents( $path, $content );
    }

    /**
     * Removes $dir and what is below it; a symbolic link is removed, never followed
     *
     * @param string $dir
     */
    private function removeDir( $dir )
    {
        if ( !is_dir( $dir ) || is_link( $dir ) )
            return;
        foreach ( scandir( $dir ) as $entry )
        {
            if ( $entry === '.' || $entry === '..' )
                continue;
            $path = $dir . '/' . $entry;
            if ( is_link( $path ) || !is_dir( $path ) )
                unlink( $path );
            else
                $this->removeDir( $path );
        }
        rmdir( $dir );
    }
}
