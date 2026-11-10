<?php
/**
 * eZTranslationCache (lib/ezi18n/classes/eztranslationcache.php) after the cache was split by locale:
 *   - a locale names a directory of PHP files that are included later, so only a locale code is used for it; any
 *     other value falls back to the RegionalSettings/Locale setting
 *   - a cache file written before the split (another code date) is not restored, so a file that could hold another
 *     locale's texts is rebuilt from the translation files; a file of the current code date round-trips
 *   - the directory of a locale is kept per site cache directory (a Velocity worker serves several sites)
 *   - eZTSTranslator checks a cached context strictly against null and does not iterate one it could not restore
 *
 * The cache globals are saved in setUp() and restored in tearDown(); files go to a directory of the test's own
 * below var/tmp, removed afterwards.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group lib
 * @group ezi18n
 */

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezi18n')]
class eZTranslationCacheHardeningTest extends PHPUnit\Framework\TestCase
{
    private $savedGlobals;

    /** @var string */
    private $dir = '';

    protected function setUp(): void
    {
        $this->savedGlobals = array();
        foreach ( array( 'eZTranslationCacheTable', 'eZTranslationCacheDirectory' ) as $key )
            $this->savedGlobals[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
        eZTranslationCache::resetGlobals();
    }

    protected function tearDown(): void
    {
        foreach ( $this->savedGlobals as $key => $saved )
        {
            if ( $saved === null )
                unset( $GLOBALS[$key] );
            else
                $GLOBALS[$key] = $saved[0];
        }
        if ( $this->dir !== '' && is_dir( $this->dir ) )
        {
            foreach ( glob( $this->dir . '/*' ) as $file )
                unlink( $file );
            rmdir( $this->dir );
        }
    }

    public function testOnlyALocaleCodeNamesADirectory()
    {
        foreach ( array( 'eng-US', 'ger-DE', 'nor-NO@nynorsk', 'chi-CN.utf8', 'untranslated', 'ger_DE' ) as $locale )
        {
            $this->assertTrue( eZTranslationCache::isLocaleCode( $locale ), $locale );
            $this->assertSame( $locale, eZTranslationCache::cacheLocale( $locale ) );
        }
        $setting = eZINI::instance()->variable( 'RegionalSettings', 'Locale' );
        foreach ( array( '../../settings', 'ger-DE/../../x', '/etc', 'a/b', 'a\\b', '.hidden', '-x', "ger-DE\n", "ger-DE\0",
                         'ger..DE', str_repeat( 'a', 65 ), array( 'ger-DE' ), 12, true ) as $locale )
        {
            $this->assertFalse( eZTranslationCache::isLocaleCode( $locale ), var_export( $locale, true ) );
            $this->assertSame( $setting, @eZTranslationCache::cacheLocale( $locale ), var_export( $locale, true ) );
        }
        $this->assertStringEndsWith( '/' . $setting, @eZTranslationCache::cacheDirectory( '../../settings' ) );
    }

    public function testACacheFileOfAnotherCodeDateIsRebuilt()
    {
        $this->assertGreaterThan( 1058863428, eZTranslationCache::CODE_DATE, 'raised when the cache was split by locale' );
        $locale = 'tst-ZZ';
        $this->dir = dirname( __DIR__, 4 ) . '/var/tmp/translation-cache-test-' . bin2hex( random_bytes( 6 ) );
        mkdir( $this->dir, 0755, true );
        $GLOBALS['eZTranslationCacheDirectory'][eZSys::cacheDirectory()][$locale] = $this->dir;
        $this->assertSame( $this->dir, eZTranslationCache::cacheDirectory( $locale ), 'kept per site cache directory' );

        // a file as the cache wrote it before the split: the old code date
        $php = new eZPHPCreator( $this->dir, md5( 'design/test/old' ) . '.php' );
        $php->addRawVariable( 'eZTranslationCacheCodeDate', 1058863428 );
        $php->addRawVariable( 'CacheInfo', array( 'charset' => 'utf-8' ) );
        $php->addRawVariable( 'TranslationInfo', array( 'context' => 'design/test/old' ) );
        $php->addRawVariable( 'TranslationRoot', array( 'Register new account' => 'Neues Konto registrieren' ) );
        $php->store( true );
        $this->assertTrue( eZTranslationCache::canRestoreCache( 'design/test/old', 0, $locale ) );
        $this->assertFalse( eZTranslationCache::restoreCache( 'design/test/old', $locale ) );
        $this->assertNull( eZTranslationCache::contextCache( 'design/test/old', $locale ) );

        // the current code date round-trips
        $root = array( 'Register new account' => array( 'translation' => 'Register new account' ) );
        eZTranslationCache::setContextCache( 'design/test/new', $root, $locale );
        eZTranslationCache::storeCache( 'design/test/new', $locale );
        unset( $GLOBALS['eZTranslationCacheTable'] );
        $this->assertTrue( eZTranslationCache::restoreCache( 'design/test/new', $locale ) );
        $this->assertSame( $root, eZTranslationCache::contextCache( 'design/test/new', $locale ) );
    }

    public function testTheTranslatorChecksContextsStrictly()
    {
        $code = file_get_contents( dirname( __DIR__, 4 ) . '/lib/ezi18n/classes/eztstranslator.php' );
        $this->assertStringContainsString( 'if ( eZTranslationCache::contextCache( $contextName, $this->Locale ) === null )', $code );
        $this->assertStringNotContainsString( '$this->Locale ) == null', $code );
        $this->assertStringContainsString( 'foreach ( (array)$this->CachedMessages[$contextName] as $key => $msg )', $code );
    }
}
