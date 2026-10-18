<?php
/**
 * Every complete administration translation is registered wherever the kernel lists languages: a locale file in
 * share/locale whose HTTP code matches the catalogue's language, the catalogue itself (complete against the source
 * texts, as the interface language switch measures it), the switch's ranked groups in settings/uiswitch.ini, the
 * catalogues of the bundled ezoe and ezjscore extensions, and the table of doc/languages.md. Ukrainian additionally
 * checks its Cyrillic names, its left-to-right direction and its fallback in settings/i18n.ini. Without a database.
 *
 * Run: php vendor/bin/phpunit tests/tests/lib/ezi18n/expAdminTranslationsRegisteredTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

$expAdminTranslationsRoot = dirname( __DIR__, 4 );
require_once $expAdminTranslationsRoot . '/kernel/classes/uiswitch/expuiswitchlanguagecatalogue.php';
unset( $expAdminTranslationsRoot );

class expAdminTranslationsRegisteredTest extends PHPUnit\Framework\TestCase
{
    /** The complete administration translations besides English (doc/languages.md, section 4). */
    const COMPLETE = array( 'ger-DE', 'esl-ES', 'esl-MX', 'fre-FR', 'heb-IL', 'lat-VA', 'tlh-QO', 'ukr-UA' );

    /** Those whose ezoe and ezjscore catalogues are complete too. */
    const WITH_EXTENSION_CATALOGUES = array( 'fre-FR', 'ukr-UA' );

    /** @var string the repository */
    protected $root;

    protected function setUp(): void
    {
        $this->root = dirname( __DIR__, 4 );
        expUISwitchLanguageCatalogue::resetMemory();
    }

    protected function tearDown(): void
    {
        expUISwitchLanguageCatalogue::resetMemory();
    }

    public static function completeLanguages()
    {
        $data = array();
        foreach ( self::COMPLETE as $code )
            $data[$code] = array( $code );
        return $data;
    }

    /** @return array key => value of an INI file (sections ignored), without the eZINI class */
    protected function iniValues( $file )
    {
        $values = array();
        foreach ( preg_split( '/\R/', (string)file_get_contents( $file ) ) as $line )
        {
            if ( preg_match( '/^([A-Za-z0-9_]+)=(.*)$/', trim( $line ), $m ) )
                $values[$m[1]] = $m[2];
        }
        return $values;
    }

    /** @return string the language attribute of a catalogue's <TS> element */
    protected function catalogueLanguage( $file )
    {
        $head = (string)file_get_contents( $file, false, null, 0, 2048 );
        return preg_match( '/<TS[^>]*\blanguage="([^"]+)"/', $head, $m ) ? $m[1] : '';
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'completeLanguages' )]
    public function testLocaleFileMatchesTheCatalogue( $code )
    {
        $locale = $this->root . '/share/locale/' . $code . '.ini';
        $ts = $this->root . '/share/translations/' . $code . '/translation.ts';
        $this->assertFileExists( $locale );
        $this->assertFileExists( $ts );
        $values = $this->iniValues( $locale );
        $this->assertNotSame( '', $values['LanguageName'] ?? '', "$code: LanguageName" );
        $this->assertNotSame( '', $values['ContentLanguage'] ?? '', "$code: ContentLanguage" );
        // the catalogue names its language by the HTTP code (uk-UA in the locale, uk_UA in the catalogue) or by the
        // locale code (heb-IL); the attribute is optional, but the French and Ukrainian catalogues carry it
        $language = strtolower( $this->catalogueLanguage( $ts ) );
        if ( $language === '' && !in_array( $code, self::WITH_EXTENSION_CATALOGUES, true ) )
            return;
        $this->assertContains( $language, array( strtolower( str_replace( '-', '_', $values['ContentLanguage'] ) ), strtolower( $code ) ),
                               "$code: catalogue language" );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'completeLanguages' )]
    public function testTheSwitchMeasuresTheCatalogueAsComplete( $code )
    {
        $catalogue = new expUISwitchLanguageCatalogue( $this->root, '' );
        $entry = $catalogue->entry( $code );
        $this->assertIsArray( $entry, "$code is not in the catalogue of the interface language switch" );
        $this->assertGreaterThanOrEqual( 99, $entry['coverage'], "$code: coverage" );
        $this->assertFalse( $entry['test'], "$code must not be a test language" );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'completeLanguages' )]
    public function testTheSwitchOffersTheLanguageInARankedGroup( $code )
    {
        $text = (string)file_get_contents( $this->root . '/settings/uiswitch.ini' );
        preg_match_all( '/^GroupValues_([a-z0-9_]+)\[\]=(\S+)\s*$/m', $text, $m, PREG_SET_ORDER );
        $groups = array();
        foreach ( $m as $row )
            $groups[$row[2]][] = $row[1];
        preg_match_all( '/^GroupList\[\]=(\S+)\s*$/m', $text, $listed );
        if ( in_array( $code, array( 'lat-VA', 'tlh-QO' ), true ) )
        {
            // Latin and Klingon are offered by "All languages" only.
            $this->assertMatchesRegularExpression( '/^AllLanguages=enabled\s*$/m', $text );
            return;
        }
        $this->assertArrayHasKey( $code, $groups, "$code is in no GroupValues_* list of settings/uiswitch.ini" );
        $this->assertNotEmpty( array_intersect( $groups[$code], $listed[1] ), "$code: its group is not in GroupList[]" );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'completeLanguages' )]
    public function testLanguagesDocumentListsTheTranslationAsComplete( $code )
    {
        $doc = (string)file_get_contents( $this->root . '/doc/languages.md' );
        $this->assertMatchesRegularExpression( '/^\| `' . preg_quote( $code, '/' ) . '` \| (99|100)% /m', $doc );
    }

    public static function extensionCatalogues()
    {
        $data = array();
        foreach ( self::WITH_EXTENSION_CATALOGUES as $code )
            foreach ( array( 'ezoe', 'ezjscore' ) as $extension )
                $data["$extension $code"] = array( $extension, $code );
        return $data;
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'extensionCatalogues' )]
    public function testBundledExtensionsCarryTheCatalogue( $extension, $code )
    {
        $ts = $this->root . '/extension/' . $extension . '/translations/' . $code . '/translation.ts';
        $this->assertFileExists( $ts );
        $this->assertNotSame( '', $this->catalogueLanguage( $ts ) );
        $catalogue = new expUISwitchLanguageCatalogue( $this->root, '' );
        $this->assertNotEmpty( $catalogue->messageKeys( $ts, true ), "$extension $code has no translated message" );
        $this->assertSame( array(), array_diff_key( $catalogue->messageKeys( $this->root . '/extension/' . $extension . '/translations/untranslated/translation.ts', false ),
                                                    $catalogue->messageKeys( $ts, true ) ),
                           "$extension $code: messages of the source catalogue without a translation" );
    }

    public function testUkrainianIsCyrillicLeftToRightWithAFallback()
    {
        $values = $this->iniValues( $this->root . '/share/locale/ukr-UA.ini' );
        $this->assertMatchesRegularExpression( '/^\p{Cyrillic}+$/u', $values['LanguageName'] );
        $this->assertSame( 'Ukrainian', $values['InternationalLanguageName'] );
        $this->assertSame( 'uk-UA', $values['ContentLanguage'] );
        $this->assertNotSame( 'rtl', strtolower( $values['TextDirection'] ?? 'ltr' ) );
        $this->assertMatchesRegularExpression( '/^FallbackLanguages\[ukr-UA\]=\S+/m',
                                               (string)file_get_contents( $this->root . '/settings/i18n.ini' ) );
        // the catalogue is written in Cyrillic, not left in English
        $ts = (string)file_get_contents( $this->root . '/share/translations/ukr-UA/translation.ts' );
        preg_match_all( '/<translation>([^<]+)<\/translation>/u', $ts, $m );
        $cyrillic = count( preg_grep( '/\p{Cyrillic}/u', $m[1] ) );
        $this->assertGreaterThan( 0.9 * count( $m[1] ), $cyrillic, 'Ukrainian translations in Cyrillic' );
    }
}
