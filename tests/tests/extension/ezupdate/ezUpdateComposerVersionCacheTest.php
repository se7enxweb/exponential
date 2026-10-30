<?php
/**
 * The Overview asks Composer for its version once per [ComposerSettings] VersionCacheTime, not on every visit:
 * eZUpdateManager::composerVersion() keeps the answer in <cache dir>/ezupdate/composer-version.json, keyed on
 * the binary, and a Composer run that succeeds is logged at debug level, not as a notice. A fake composer.phar
 * counts its own runs. The live installation, no test database.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/ezupdate/ezUpdateComposerVersionCacheTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/../expservices/core/expServicesCoreTestCase.php';

class ezUpdateComposerVersionCacheTest extends expServicesCoreTestCase
{
    const DIR = 'var/tmp/ezupdate_version_test/';

    /** @var array original ezupdate.ini values, restored in tearDown */
    private $saved = array();

    /** @var string|null|false the cache file as it was (null: not there; false: not read yet) */
    private $savedCache = false;

    protected function root()
    {
        return dirname( __DIR__, 4 );
    }

    public function setUp(): void
    {
        parent::setUp();
        if ( !class_exists( 'eZUpdateManager' ) || !method_exists( 'eZUpdateManager', 'parseComposerVersion' ) )
        {
            $this->markTestSkipped( 'ezupdate 1.1.21 or later is not installed.' );
        }
        $file = eZUpdateManager::versionCacheFile();
        $this->savedCache = is_file( $file ) ? (string)file_get_contents( $file ) : null;
    }

    public function tearDown(): void
    {
        $ini = eZINI::instance( 'ezupdate.ini' );
        foreach ( $this->saved as $name => $value )
        {
            $ini->setVariable( 'ComposerSettings', $name, $value );
        }
        if ( $this->savedCache !== false )
        {
            $file = eZUpdateManager::versionCacheFile();
            if ( $this->savedCache === null )
            {
                @unlink( $file );
            }
            else
            {
                file_put_contents( $file, $this->savedCache );
            }
        }
        $dir = $this->root() . '/' . self::DIR;
        foreach ( (array)glob( $dir . '*' ) as $leftover )
        {
            @unlink( $leftover );
        }
        @rmdir( $dir );
        parent::tearDown();
    }

    protected function setting( $name, $value )
    {
        $ini = eZINI::instance( 'ezupdate.ini' );
        if ( !array_key_exists( $name, $this->saved ) )
        {
            $this->saved[$name] = $ini->hasVariable( 'ComposerSettings', $name ) ? $ini->variable( 'ComposerSettings', $name ) : '';
        }
        $ini->setVariable( 'ComposerSettings', $name, $value );
    }

    /** A composer.phar that prints a version and appends a line to runs.txt each time it is started. */
    protected function fakeComposer( $version = '2.9.9' )
    {
        $dir = $this->root() . '/' . self::DIR;
        if ( !is_dir( $dir ) )
        {
            mkdir( $dir, 0775, true );
        }
        file_put_contents( $dir . 'composer.phar', "<?php\nfile_put_contents( __DIR__ . '/runs.txt', \"run\\n\", FILE_APPEND );\n"
            . "echo \"Do not run Composer as root/super user! See https://getcomposer.org/root for details\\n\";\n"
            . "echo 'Composer version " . $version . " 2026-10-01 00:00:00', \"\\n\";\n" );
        $this->setting( 'Path', $dir );
        $this->setting( 'Binary', 'composer.phar' );
        $this->setting( 'PHPBinary', PHP_BINARY );
        return $dir;
    }

    protected function runs( $dir )
    {
        return is_file( $dir . 'runs.txt' ) ? substr_count( (string)file_get_contents( $dir . 'runs.txt' ), "run\n" ) : 0;
    }

    public function testParseSkipsWarningsBeforeTheVersionLine()
    {
        $this->assertSame( 'Composer version 2.8.12 2025-09-19 13:41:59',
            eZUpdateManager::parseComposerVersion( "Do not run Composer as root!\n  Composer version 2.8.12 2025-09-19 13:41:59\nPHP version 8.5.0" ) );
        $this->assertSame( 'something else', eZUpdateManager::parseComposerVersion( "something else\nmore" ) );
        $this->assertSame( '', eZUpdateManager::parseComposerVersion( '' ) );
    }

    public function testCacheIsReadOnlyForTheSameKeyAndWithinItsTime()
    {
        $dir = $this->root() . '/' . self::DIR;
        @mkdir( $dir, 0775, true );
        $file = $dir . 'version.json';
        $this->assertTrue( eZUpdateManager::writeVersionCache( $file, 'k1', 'Composer version 1.0', 1000 ) );
        $this->assertSame( 'Composer version 1.0', eZUpdateManager::readVersionCache( $file, 'k1', 60, 1030 ) );
        $this->assertFalse( eZUpdateManager::readVersionCache( $file, 'k2', 60, 1030 ), 'another binary' );
        $this->assertFalse( eZUpdateManager::readVersionCache( $file, 'k1', 60, 1060 ), 'expired' );
        $this->assertFalse( eZUpdateManager::readVersionCache( $file, 'k1', 60, 999 ), 'written in the future' );
        $this->assertFalse( eZUpdateManager::readVersionCache( $file, 'k1', 0, 1030 ), 'switched off' );
        file_put_contents( $file, 'not json' );
        $this->assertFalse( eZUpdateManager::readVersionCache( $file, 'k1', 60, 1030 ), 'broken file' );
        $this->assertFalse( eZUpdateManager::readVersionCache( $dir . 'missing.json', 'k1', 60, 1030 ) );
    }

    public function testKeyChangesWhenTheBinaryChanges()
    {
        $dir = $this->fakeComposer();
        $before = eZUpdateManager::versionCacheKey( array( PHP_BINARY, $dir . 'composer.phar' ) );
        $this->assertSame( $before, eZUpdateManager::versionCacheKey( array( PHP_BINARY, $dir . 'composer.phar' ) ) );
        file_put_contents( $dir . 'composer.phar', "\n// a new release\n", FILE_APPEND );
        clearstatcache();
        $this->assertNotSame( $before, eZUpdateManager::versionCacheKey( array( PHP_BINARY, $dir . 'composer.phar' ) ) );
    }

    public function testComposerIsAskedOnceThenTheCacheAnswers()
    {
        $dir = $this->fakeComposer();
        $this->setting( 'VersionCacheTime', '3600' );
        @unlink( eZUpdateManager::versionCacheFile() );

        $first = eZUpdateManager::getInstance()->composerVersion();
        $this->assertSame( 'Composer version 2.9.9 2026-10-01 00:00:00', $first );
        $this->assertSame( 1, $this->runs( $dir ) );

        // a new request (a new manager) does not start Composer again
        $second = eZUpdateManager::getInstance();
        $this->assertSame( $first, $second->composerVersion() );
        $this->assertSame( 1, $this->runs( $dir ), 'the second visit must not start Composer' );
        $this->assertSame( '', $second->lastOutput() );

        // fresh asks anyway
        $this->assertSame( $first, eZUpdateManager::getInstance()->composerVersion( true ) );
        $this->assertSame( 2, $this->runs( $dir ) );
    }

    public function testNoCacheWhenSwitchedOffAndNoCacheOfAFailure()
    {
        $dir = $this->fakeComposer();
        $this->setting( 'VersionCacheTime', '0' );
        @unlink( eZUpdateManager::versionCacheFile() );
        eZUpdateManager::getInstance()->composerVersion();
        eZUpdateManager::getInstance()->composerVersion();
        $this->assertSame( 2, $this->runs( $dir ) );
        $this->assertFileDoesNotExist( eZUpdateManager::versionCacheFile() );

        // a Composer that fails is asked again next time, and says why
        file_put_contents( $dir . 'composer.phar', "<?php\nfile_put_contents( __DIR__ . '/runs.txt', \"run\\n\", FILE_APPEND );\necho 'broken';\nexit( 1 );\n" );
        $this->setting( 'VersionCacheTime', '3600' );
        $manager = eZUpdateManager::getInstance();
        $this->assertFalse( $manager->composerVersion() );
        $this->assertSame( 'broken', $manager->lastOutput() );
        $this->assertFalse( eZUpdateManager::getInstance()->composerVersion() );
        $this->assertSame( 4, $this->runs( $dir ) );
        $this->assertFileDoesNotExist( eZUpdateManager::versionCacheFile() );
    }

    public function testCacheIsAClearableCacheItem()
    {
        $found = false;
        foreach ( eZCache::fetchList() as $item )
        {
            if ( $item['id'] === 'ezupdate' )
            {
                $found = $item;
            }
        }
        $this->assertNotFalse( $found, 'site.ini [Cache] CacheItems has no ezupdate entry' );
        $this->assertSame( 'ezupdate', $found['path'] );
        $this->assertSame( eZSys::cacheDirectory() . '/ezupdate', dirname( eZUpdateManager::versionCacheFile() ) );
        $this->assertSame( '86400', eZINI::instance( 'ezupdate.ini' )->variable( 'ComposerSettings', 'VersionCacheTime' ) );
    }

    public function testASuccessfulRunIsNotLoggedAsANotice()
    {
        $source = (string)file_get_contents( ( new ReflectionClass( 'eZUpdateManager' ) )->getFileName() );
        $this->assertSame( 1, preg_match( "/if \\( \\(int\\)\\\$exit === 0 \\)\\s*\\{\\s*eZDebug::writeDebug\\( 'composer '/", $source ),
            'run() must log a run that worked at debug level' );
    }
}
