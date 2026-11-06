<?php
/**
 * eZTranslationCache (lib/ezi18n/classes/eztranslationcache.php) keeps each locale apart. It named its directory
 * after RegionalSettings/Locale, kept that directory for the whole process and kept one in-memory table for all
 * locales, so a translator for another locale than the setting (a second language in the same request, a test)
 * wrote its texts into the setting's cache, and later lookups of that locale returned them: with Locale=eng-US,
 * "Register new account" came out as "Neues Konto registrieren".
 *   - a context cached for one locale is not returned for another, in memory
 *   - without a locale the RegionalSettings/Locale setting is used, as before
 *   - each locale has a cache directory of its own
 *   - eZTSTranslator (lib/ezi18n/classes/eztstranslator.php) passes its own locale to every cache call
 *
 * The cache globals are saved in setUp() and restored in tearDown(); nothing is written to disk.
 *
 * Run: php vendor/bin/phpunit tests/tests/lib/ezi18n/eZTranslationCacheLocaleTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group lib
 * @group ezi18n
 */

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezi18n')]
class eZTranslationCacheLocaleTest extends PHPUnit\Framework\TestCase
{
    private $savedGlobals;

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
    }

    public function testAContextCachedForOneLocaleIsNotReturnedForAnother()
    {
        $german = array( 'Register new account' => array( 'translation' => 'Neues Konto registrieren' ) );
        eZTranslationCache::setContextCache( 'design/test/user', $german, 'ger-DE' );

        $this->assertNull( eZTranslationCache::contextCache( 'design/test/user', 'eng-US' ),
                           'the English cache does not return the German context' );
        $this->assertSame( $german, eZTranslationCache::contextCache( 'design/test/user', 'ger-DE' ) );
    }

    public function testTheSameContextIsCachedForEachLocaleOnItsOwn()
    {
        eZTranslationCache::setContextCache( 'design/test/user', array( 'en' ), 'eng-US' );
        eZTranslationCache::setContextCache( 'design/test/user', array( 'de' ), 'ger-DE' );

        $this->assertSame( array( 'en' ), eZTranslationCache::contextCache( 'design/test/user', 'eng-US' ) );
        $this->assertSame( array( 'de' ), eZTranslationCache::contextCache( 'design/test/user', 'ger-DE' ) );
    }

    public function testWithoutALocaleTheSettingIsUsed()
    {
        $setting = eZINI::instance()->variable( 'RegionalSettings', 'Locale' );
        $this->assertSame( $setting, eZTranslationCache::cacheLocale() );
        $this->assertSame( $setting, eZTranslationCache::cacheLocale( '' ) );
        $this->assertSame( 'ger-DE', eZTranslationCache::cacheLocale( 'ger-DE' ) );

        eZTranslationCache::setContextCache( 'design/test/default', array( 'x' ) );
        $this->assertSame( array( 'x' ), eZTranslationCache::contextCache( 'design/test/default' ) );
        $this->assertSame( array( 'x' ), eZTranslationCache::contextCache( 'design/test/default', $setting ),
                           'a call without a locale and a call with the setting\'s locale share the cache' );
    }

    public function testEachLocaleHasACacheDirectoryOfItsOwn()
    {
        $english = eZTranslationCache::cacheDirectory( 'eng-US' );
        $german = eZTranslationCache::cacheDirectory( 'ger-DE' );

        $this->assertNotSame( $english, $german );
        $this->assertSame( 'eng-US', basename( $english ) );
        $this->assertSame( 'ger-DE', basename( $german ) );
        $this->assertSame( dirname( $english ), dirname( $german ), 'both below the same root cache directory' );
    }

    public function testTheTranslatorPassesItsLocaleToEveryCacheCall()
    {
        $code = file_get_contents( dirname( __DIR__, 4 ) . '/lib/ezi18n/classes/eztstranslator.php' );
        $calls = $this->cacheCalls( $code );

        $this->assertNotEmpty( $calls, 'the translator uses the translation cache' );
        foreach ( $calls as $call )
        {
            $this->assertStringContainsString( '$this->Locale', $call, 'eZTSTranslator passes its locale: ' . $call );
        }
    }

    /**
     * The calls of eZTranslationCache methods in $code that read or write a locale's cache, each as its text from the
     * method name to the closing parenthesis (nested parentheses included).
     */
    private function cacheCalls( $code )
    {
        $calls = array();
        $offset = 0;
        while ( preg_match( '/eZTranslationCache::(\w+)\s*\(/', $code, $match, PREG_OFFSET_CAPTURE, $offset ) )
        {
            $start = $match[0][1];
            $position = $start + strlen( $match[0][0] );
            $depth = 1;
            while ( $depth > 0 && $position < strlen( $code ) )
            {
                if ( $code[$position] === '(' )
                    $depth++;
                elseif ( $code[$position] === ')' )
                    $depth--;
                $position++;
            }
            if ( in_array( $match[1][0], array( 'canRestoreCache', 'restoreCache', 'contextCache', 'setContextCache', 'storeCache', 'cacheTable', 'cacheDirectory' ), true ) )
                $calls[] = preg_replace( '/\s+/', ' ', substr( $code, $start, $position - $start ) );
            $offset = $position;
        }
        return $calls;
    }
}
