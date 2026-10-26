<?php
/**
 * The device line of "Your devices" (user/sessions), expUserAgent::label(), in the interface language. The word
 * "on" between the browser and the system was English in every language. Without a database.
 *
 *  UAL-01  the label is translated through the context kernel/user/sessions: "Firefox 131 unter Linux" in German
 *  UAL-02  every catalogue with that context has "%browser on %os", finished in all but untranslated, with both
 *          placeholders
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expUserAgentLabelTranslationTest extends PHPUnit\Framework\TestCase
{
    const FIREFOX_LINUX = 'Mozilla/5.0 (X11; Linux x86_64; rv:131.0) Gecko/20100101 Firefox/131.0';

    private $savedSettings = array();
    private $savedGlobals = array();

    private static function root()
    {
        return dirname( __DIR__, 5 );
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance();
        foreach ( $this->savedSettings as $name => $value )
            $ini->setVariable( 'RegionalSettings', $name, $value );
        foreach ( $this->savedGlobals as $key => $saved )
        {
            if ( $saved === null )
                unset( $GLOBALS[$key] );
            else
                $GLOBALS[$key] = $saved[0];
        }
        $this->savedSettings = $this->savedGlobals = array();
    }

    /** Switches the translation to $locale for this test (restored in tearDown), without the translation cache. */
    private function useLocale( $locale )
    {
        $ini = eZINI::instance();
        foreach ( array( 'Locale', 'TranslationCache' ) as $name )
            $this->savedSettings[$name] = $ini->variable( 'RegionalSettings', $name );
        foreach ( array( 'eZLocaleStringDefault', 'eZTranslatorManagerInstance', 'eZTSTranslationTables' ) as $key )
            $this->savedGlobals[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
        $ini->setVariable( 'RegionalSettings', 'Locale', $locale );
        $ini->setVariable( 'RegionalSettings', 'TranslationCache', 'disabled' );
        unset( $GLOBALS['eZLocaleStringDefault'], $GLOBALS['eZTranslatorManagerInstance'], $GLOBALS['eZTSTranslationTables'] );
        if ( eZLocale::instance()->localeFullCode() !== $locale )
            $this->markTestSkipped( "the locale $locale cannot be loaded here" );
    }

    /** UAL-01 */
    public function testTheLabelIsGermanInGerman()
    {
        $this->useLocale( 'ger-DE' );
        $this->assertSame( 'Firefox 131 unter Linux', expUserAgent::label( expUserAgent::parse( self::FIREFOX_LINUX ) ) );
        // without a system there is nothing to join
        $this->assertSame( 'curl 8', expUserAgent::label( expUserAgent::parse( 'curl/8.9.1' ) ) );
    }

    /** UAL-01 */
    public function testTheLabelIsEnglishInEnglish()
    {
        $this->useLocale( 'eng-GB' );
        $this->assertSame( 'Firefox 131 on Linux', expUserAgent::label( expUserAgent::parse( self::FIREFOX_LINUX ) ) );
    }

    /** UAL-01: the code asks for the text the catalogues carry */
    public function testTheLabelUsesTheContextOfTheSessionsPage()
    {
        $source = file_get_contents( self::root() . '/kernel/classes/user/expuseragent.php' );
        $this->assertStringContainsString( "ezpI18n::tr( 'kernel/user/sessions', '%browser on %os'", $source );
        $this->assertStringNotContainsString( "' on '", $source );
    }

    /** UAL-02 */
    public function testEveryCatalogueHasTheLabel()
    {
        $count = 0;
        foreach ( glob( self::root() . '/share/translations/*/translation.ts' ) as $file )
        {
            $language = basename( dirname( $file ) );
            $doc = new DOMDocument();
            $this->assertTrue( @$doc->load( $file, LIBXML_NONET ), "$language: not valid XML" );
            $xpath = new DOMXPath( $doc );
            if ( !$xpath->query( '/TS/context[name="kernel/user/sessions"]' )->length )
                continue;
            $count++;
            $messages = $xpath->query( '/TS/context[name="kernel/user/sessions"]/message[source="%browser on %os"]' );
            $this->assertSame( 1, $messages->length, "$language: \"%browser on %os\" missing in kernel/user/sessions" );
            $translation = $xpath->query( 'translation', $messages->item( 0 ) )->item( 0 );
            if ( $language === 'untranslated' )
            {
                $this->assertSame( 'unfinished', $translation->getAttribute( 'type' ) );
                continue;
            }
            $this->assertSame( '', $translation->getAttribute( 'type' ), "$language: unfinished" );
            $text = $translation->textContent;
            $this->assertSame( 1, substr_count( $text, '%browser' ), "$language: $text" );
            $this->assertSame( 1, preg_match_all( '/%os(?![A-Za-z0-9_])/', $text ), "$language: $text" );
        }
        $this->assertGreaterThanOrEqual( 60, $count );
    }
}
?>
