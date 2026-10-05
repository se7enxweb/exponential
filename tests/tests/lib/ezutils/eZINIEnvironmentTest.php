<?php
/**
 * eZINI reads, next to every settings file, the variant of the environment named by EXP_ENV.
 *
 * site.ini.append.php gets site.ini.<env>.append.php, site.ini gets site.ini.<env>, and so
 * on. The variant comes right after its standard file, so it only overrides its own layer,
 * and it is also read when the standard file does not exist. Without an environment the
 * list of files and the cache file name are the same as before environments existed.
 *
 * The tests work on a settings tree below var/tmp of the installation root (run them from
 * that root, as eZINI reads override directories relative to the working directory) and set
 * the environment through the
 * protected eZINI::$environment, since a constant can not be changed within one run.
 *
 * Run: php vendor/bin/phpunit tests/tests/lib/ezutils/eZINIEnvironmentTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class eZINIEnvironmentTest extends PHPUnit\Framework\TestCase
{
    /**
     * Settings directory of a test, relative to the installation root
     *
     * @var string
     */
    private $rootDir;

    protected function setUp(): void
    {
        $this->rootDir = 'var/tmp/ezini_environment_' . uniqid( '', true ) . '/settings';
        $this->writeFile( 'test.ini', "[Values]\nLayer=base\nBase=base\n" );
        $this->writeFile( 'test.ini.dev', "[Values]\nLayer=base-dev\nBaseDev=base-dev\n" );
        $this->writeFile( 'siteaccess/site/test.ini.dev.append.php', "<?php /*\n[Values]\nLayer=siteaccess-dev\n*/ ?>\n" );
        $this->writeFile( 'override/test.ini.append.php', "<?php /*\n[Values]\nLayer=override\nOverride=override\n*/ ?>\n" );
        $this->writeFile( 'override/test.ini.dev.append.php', "<?php /*\n[Values]\nLayer=override-dev\n*/ ?>\n" );
        $this->writeFile( 'override/test.ini.prod.append.php', "<?php /*\n[Values]\nLayer=override-prod\n*/ ?>\n" );
        eZINI::resetEnvironment();
    }

    protected function tearDown(): void
    {
        eZINI::resetEnvironment();
        $this->removeDir( $this->root() . dirname( $this->rootDir ) );
    }

    public function testNoEnvironmentReadsOnlyTheStandardFiles()
    {
        $this->setEnvironment( false );
        $this->assertSame(
            array( 'test.ini', 'override/test.ini.append.php' ),
            $this->inputFiles()
        );
        $this->assertSame( 'override', $this->ini()->variable( 'Values', 'Layer' ) );
    }

    public function testVariantFollowsItsStandardFileInEveryLayer()
    {
        $this->setEnvironment( 'dev' );
        $this->assertSame(
            array( 'test.ini',
                   'test.ini.dev',
                   'siteaccess/site/test.ini.dev.append.php',
                   'override/test.ini.append.php',
                   'override/test.ini.dev.append.php' ),
            $this->inputFiles()
        );
    }

    public function testVariantOverridesOnlyWithinItsLayer()
    {
        $this->setEnvironment( 'dev' );
        $ini = $this->ini();
        $this->assertSame( 'override-dev', $ini->variable( 'Values', 'Layer' ) );
        $this->assertSame( 'base', $ini->variable( 'Values', 'Base' ) );
        $this->assertSame( 'base-dev', $ini->variable( 'Values', 'BaseDev' ) );
        $this->assertSame( 'override', $ini->variable( 'Values', 'Override' ) );
    }

    public function testOtherEnvironmentReadsOnlyItsOwnVariants()
    {
        $this->setEnvironment( 'prod' );
        $this->assertSame(
            array( 'test.ini', 'override/test.ini.append.php', 'override/test.ini.prod.append.php' ),
            $this->inputFiles()
        );
        $this->assertSame( 'override-prod', $this->ini()->variable( 'Values', 'Layer' ) );
    }

    public function testCacheFileNameWithoutEnvironmentIsUnchanged()
    {
        $this->setEnvironment( false );
        $ini = $this->ini( false );
        $expected = $ini->FileName . '-' . $ini->RootDir . '-' . $ini->DirectAccess .
                    '-' . serialize( $ini->overrideDirs() ) .
                    '-' . eZTextCodec::internalCharset();
        $this->assertSame( 'test-' . md5( $expected ) . '.php', $this->cacheFileName( $ini ) );
    }

    public function testEachEnvironmentHasItsOwnCacheFile()
    {
        $this->setEnvironment( false );
        $none = $this->cacheFileName( $this->ini( false ) );
        $this->setEnvironment( 'dev' );
        $dev = $this->cacheFileName( $this->ini( false ) );
        $this->setEnvironment( 'prod' );
        $prod = $this->cacheFileName( $this->ini( false ) );

        $this->assertNotSame( $none, $dev );
        $this->assertNotSame( $dev, $prod );
    }

    /**
     * @return array value of EXP_ENV, expected environment name
     */
    public static function environmentNameProvider()
    {
        return array(
            'undefined' => array( false, false ),
            'null' => array( null, false ),
            'empty' => array( '', false ),
            'blank' => array( '   ', false ),
            'plain name' => array( 'dev', 'dev' ),
            'trimmed' => array( " test\n", 'test' ),
            'digits, dash and underscore' => array( 'stage-2_eu', 'stage-2_eu' ),
            'upper case' => array( 'Dev', false ),
            'leading digit' => array( '2dev', false ),
            'path separator' => array( '../dev', false ),
            'dot' => array( 'dev.local', false ),
            'too long' => array( str_repeat( 'a', 33 ), false ),
            'array' => array( array( 'dev' ), false ),
        );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'environmentNameProvider' )]
    public function testEnvironmentName( $value, $expected )
    {
        $errorLog = ini_set( 'error_log', '/dev/null' );
        $this->assertSame( $expected, eZINI::environmentName( $value ) );
        ini_set( 'error_log', $errorLog === false ? '' : $errorLog );
    }

    /**
     * @return array file path, environment, expected variant path
     */
    public static function environmentFilePathProvider()
    {
        return array(
            'base file' => array( 'settings/site.ini', 'test', 'settings/site.ini.test' ),
            'append.php' => array( 'settings/override/site.ini.append.php', 'test', 'settings/override/site.ini.test.append.php' ),
            'append' => array( 'settings/override/site.ini.append', 'test', 'settings/override/site.ini.test.append' ),
            'ini.php' => array( 'settings/site.ini.php', 'test', 'settings/site.ini.test.php' ),
            '.ini in a directory name stays' => array( 'settings/my.ini.d/site.ini', 'dev', 'settings/my.ini.d/site.ini.dev' ),
            'no .ini part' => array( 'settings/site.conf', 'dev', false ),
            '.ini inside a word' => array( 'settings/site.initial', 'dev', false ),
        );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'environmentFilePathProvider' )]
    public function testEnvironmentFilePath( $filePath, $environment, $expected )
    {
        $this->assertSame( $expected, eZINI::environmentFilePath( $filePath, $environment ) );
    }

    /**
     * Sets the environment eZINI uses, as if EXP_ENV had been read
     *
     * @param string|false $environment
     */
    private function setEnvironment( $environment )
    {
        $property = new ReflectionProperty( 'eZINI', 'environment' );
        $property->setValue( null, $environment );
    }

    /**
     * Returns an uncached eZINI for test.ini in the test tree with a siteaccess and an override dir
     *
     * @param bool $load
     * @return eZINI
     */
    private function ini( $load = true )
    {
        $ini = new eZINI( 'test.ini', $this->rootDir, null, false, true, false, false, false );
        $ini->setOverrideDirs( array( 'sa-extension' => array(),
                                      'siteaccess' => array( array( 'siteaccess/site', false ) ),
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
     * Returns the files eZINI reads for test.ini, relative to the test settings directory
     *
     * @return array
     */
    private function inputFiles()
    {
        $ini = $this->ini( false );
        $inputFiles = array();
        $iniFile = '';
        $ini->findInputFiles( $inputFiles, $iniFile );

        $prefix = $this->rootDir . '/';
        $relative = array();
        foreach ( $inputFiles as $inputFile )
        {
            $position = strpos( $inputFile, $prefix );
            $relative[] = $position === false ? $inputFile : substr( $inputFile, $position + strlen( $prefix ) );
        }
        return $relative;
    }

    /**
     * @param eZINI $ini
     * @return string
     */
    private function cacheFileName( $ini )
    {
        $method = new ReflectionMethod( 'eZINI', 'cacheFileName' );
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
     * @param string $relativePath path below the test settings directory
     * @param string $content
     */
    private function writeFile( $relativePath, $content )
    {
        $path = $this->root() . $this->rootDir . '/' . $relativePath;
        if ( !is_dir( dirname( $path ) ) )
            mkdir( dirname( $path ), 0777, true );
        file_put_contents( $path, $content );
    }

    /**
     * @param string $dir
     */
    private function removeDir( $dir )
    {
        if ( !is_dir( $dir ) )
            return;
        foreach ( scandir( $dir ) as $entry )
        {
            if ( $entry === '.' || $entry === '..' )
                continue;
            $path = $dir . '/' . $entry;
            if ( is_dir( $path ) && !is_link( $path ) )
                $this->removeDir( $path );
            else
                unlink( $path );
        }
        rmdir( $dir );
    }
}
