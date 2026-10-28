<?php
/**
 * Every complete administration translation is registered wherever the kernel lists languages: a locale file in
 * share/locale whose HTTP code matches the catalogue's language, the catalogue itself (complete against the source
 * texts, as the interface language switch measures it), the switch's ranked groups in settings/uiswitch.ini, the
 * catalogues of the bundled ezoe and ezjscore extensions, and the table of doc/eng-us/languages.md. Ukrainian additionally
 * checks its Cyrillic names, its left-to-right direction and its fallback in settings/i18n.ini; Arabic, Hebrew,
 * Yiddish, Sindhi and Western Punjabi their right-to-left locale, their script in every catalogue and the user-name
 * sense of "Login". Without a database.
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
    /** The complete administration translations besides English (doc/eng-us/languages.md, section 4). */
    const COMPLETE = array( 'ger-DE', 'esl-ES', 'esl-MX', 'fre-FR', 'heb-IL', 'lat-VA', 'tlh-QO', 'ukr-UA', 'ara-SA',
        'afr-ZA', 'bos-BA', 'bul-BG', 'cat-ES', 'dsb-DE', 'ell-GR', 'eng-AU', 'est-EE', 'fin-FI', 'gle-IE', 'hin-IN',
        'hun-HU', 'isl-IS', 'jpn-JP', 'kal-GL', 'kor-KR', 'lav-LV', 'lit-LT', 'mlt-MT', 'por-PT', 'ron-RO', 'rus-RU',
        'ser-SR', 'slk-SK', 'slv-SI', 'sqi-AL', 'srp-RS', 'yid-US', 'vie-VN', 'ind-ID', 'tha-TH', 'lao-LA', 'msa-MY',
        'jav-ID', 'sun-ID', 'nep-NP', 'bho-IN', 'mai-IN', 'pcm-NG', 'syr-IQ', 'arc-IQ', 'huy-IL', 'kat-GE', 'sna-ZW',
        'kaz-KZ', 'gla-GB', 'mri-NZ', 'fil-PH', 'che-RU', 'snd-PK', 'pnb-PK', 'uzb-UZ', 'mya-MM', 'haw-US', 'hye-AM',
        'lin-CD', 'ful-SN', 'orm-ET', 'aze-AZ', 'aka-GH', 'sin-LK', 'asm-IN', 'kik-KE', 'khm-KH', 'uig-CN', 'kin-RW',
        'ceb-PH', 'ckb-IQ', 'tir-ET', 'mlg-MG', 'mon-MN', 'kir-KG', 'tat-RU', 'tuk-TM' );

    /** Those whose ezoe and ezjscore catalogues are complete too. */
    const WITH_EXTENSION_CATALOGUES = array( 'fre-FR', 'ukr-UA', 'ara-SA', 'heb-IL', 'afr-ZA', 'bos-BA', 'bul-BG',
        'cat-ES', 'dsb-DE', 'ell-GR', 'eng-AU', 'est-EE', 'fin-FI', 'gle-IE', 'hin-IN', 'hun-HU', 'isl-IS', 'jpn-JP',
        'kal-GL', 'kor-KR', 'lav-LV', 'lit-LT', 'mlt-MT', 'por-PT', 'ron-RO', 'rus-RU', 'ser-SR', 'slk-SK', 'slv-SI',
        'sqi-AL', 'srp-RS', 'yid-US', 'vie-VN', 'ind-ID', 'tha-TH', 'lao-LA', 'msa-MY', 'jav-ID', 'sun-ID', 'nep-NP',
        'bho-IN', 'mai-IN', 'pcm-NG', 'syr-IQ', 'arc-IQ', 'huy-IL', 'kat-GE', 'sna-ZW', 'kaz-KZ', 'gla-GB', 'mri-NZ',
        'fil-PH', 'che-RU', 'snd-PK', 'pnb-PK', 'uzb-UZ', 'mya-MM', 'haw-US', 'hye-AM', 'lin-CD', 'ful-SN', 'orm-ET',
        'aze-AZ', 'aka-GH', 'sin-LK', 'asm-IN', 'kik-KE', 'khm-KH', 'uig-CN', 'kin-RW', 'ceb-PH', 'ckb-IQ', 'tir-ET',
        'mlg-MG', 'mon-MN', 'kir-KG', 'tat-RU', 'tuk-TM' );

    /**
     * Complete translations that no ranked group of settings/uiswitch.ini names: the switch offers them in its A-Z
     * list of "All languages" (AllLanguages=enabled), as every locale with a file in share/locale.
     */
    const ALL_LANGUAGES_ONLY = array( 'lat-VA', 'tlh-QO', 'afr-ZA', 'bos-BA', 'bul-BG', 'cat-ES', 'dsb-DE', 'ell-GR',
        'est-EE', 'fin-FI', 'gle-IE', 'hun-HU', 'isl-IS', 'kal-GL', 'lav-LV', 'lit-LT', 'mlt-MT', 'ron-RO', 'ser-SR',
        'slk-SK', 'slv-SI', 'sqi-AL', 'srp-RS', 'yid-US', 'tha-TH', 'lao-LA', 'msa-MY', 'jav-ID', 'sun-ID', 'nep-NP',
        'bho-IN', 'mai-IN', 'pcm-NG', 'syr-IQ', 'arc-IQ', 'huy-IL', 'kat-GE', 'sna-ZW', 'kaz-KZ', 'gla-GB', 'mri-NZ',
        'fil-PH', 'che-RU', 'snd-PK', 'pnb-PK', 'uzb-UZ', 'mya-MM', 'haw-US', 'hye-AM', 'lin-CD', 'ful-SN', 'orm-ET',
        'aze-AZ', 'aka-GH', 'sin-LK', 'asm-IN', 'kik-KE', 'khm-KH', 'uig-CN', 'kin-RW', 'ceb-PH', 'ckb-IQ', 'tir-ET',
        'mlg-MG', 'mon-MN', 'kir-KG', 'tat-RU', 'tuk-TM' );

    /** @var string the repository */
    protected $root;

    /** @var array|null the entries of the interface language switch, built from the files once */
    protected static $switchEntries = null;

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
        // not /\R/: without the u modifier it also splits at the byte 0x85, which is inside UTF-8 letters (Arabic م)
        foreach ( preg_split( '/\r\n|\n|\r/', (string)file_get_contents( $file ) ) as $line )
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
        // a script subtag between language and region names the script (Serbian Latin: sr_Latn_RS for sr-RS)
        $language = preg_replace( '/^([a-z]{2,3})_[a-z]{4}_/', '$1_', strtolower( $this->catalogueLanguage( $ts ) ) );
        if ( $language === '' && !in_array( $code, self::WITH_EXTENSION_CATALOGUES, true ) )
            return;
        $this->assertContains( $language, array( strtolower( str_replace( '-', '_', $values['ContentLanguage'] ) ), strtolower( $code ) ),
                               "$code: catalogue language" );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'completeLanguages' )]
    public function testTheSwitchMeasuresTheCatalogueAsComplete( $code )
    {
        // Built once for every data set: setUp() empties the catalogue's memory, and building it
        // from every translation file again for each language took some ten seconds a language,
        // four to five minutes for the data sets together.
        if ( self::$switchEntries === null )
            self::$switchEntries = ( new expUISwitchLanguageCatalogue( $this->root, '' ) )->entries();
        $entry = self::$switchEntries[$code] ?? null;
        $this->assertIsArray( $entry, "$code is not in the catalogue of the interface language switch" );
        $this->assertSame( 100, (int)$entry['coverage'], "$code: coverage" );
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
        if ( in_array( $code, self::ALL_LANGUAGES_ONLY, true ) )
        {
            // Latin, Klingon and the languages of no ranked group are offered by "All languages" only.
            $this->assertMatchesRegularExpression( '/^AllLanguages=enabled\s*$/m', $text );
            return;
        }
        $this->assertArrayHasKey( $code, $groups, "$code is in no GroupValues_* list of settings/uiswitch.ini" );
        $this->assertNotEmpty( array_intersect( $groups[$code], $listed[1] ), "$code: its group is not in GroupList[]" );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'completeLanguages' )]
    public function testLanguagesDocumentListsTheTranslationAsComplete( $code )
    {
        $doc = (string)file_get_contents( $this->root . '/doc/eng-us/languages.md' );
        $this->assertMatchesRegularExpression( '/^\| `' . preg_quote( $code, '/' ) . '` \| 100% /m', $doc );
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

    /** @return string the finished translation of a source in a context, '' when there is none */
    protected function translationOf( $file, $context, $source )
    {
        $doc = new DOMDocument( '1.0', 'utf-8' );
        $doc->load( $file );
        $xpath = new DOMXPath( $doc );
        foreach ( $xpath->query( '//context[name="' . $context . '"]/message[source="' . $source . '"]/translation[not(@type)]' ) as $node )
            return $node->textContent;
        return '';
    }

    /**
     * The header tab of the shop is "Store" in design/admin/pagelayout (menu.ini [Topmenu_shop]): a noun, the name
     * the shop menu calls "Shop", not the verb "Store" (save) of the forms.
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'completeLanguages' )]
    public function testTheShopTabIsNamedLikeTheShopNotLikeSaving( $code )
    {
        $ts = $this->root . '/share/translations/' . $code . '/translation.ts';
        $tab = $this->translationOf( $ts, 'design/admin/pagelayout', 'Store' );
        $shop = $this->translationOf( $ts, 'design/admin/parts/shop/menu', 'Shop' );
        if ( $tab === '' || $shop === '' )
            $this->markTestSkipped( "$code has no translation of the shop tab or the shop menu" );
        $this->assertSame( $shop, $tab, "$code: the shop tab" );
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

    public static function rightToLeftTranslations()
    {
        return array(
            'Arabic' => array( 'ara-SA', 'ar-SA', 'Arabic', '\p{Arabic}' ),
            'Hebrew' => array( 'heb-IL', 'he-IL', 'Hebrew', '\p{Hebrew}' ),
            'Yiddish' => array( 'yid-US', 'yi-US', 'Yiddish', '\p{Hebrew}' ),
            'Sindhi' => array( 'snd-PK', 'sd-PK', 'Sindhi', '\p{Arabic}' ),
            'Western Punjabi' => array( 'pnb-PK', 'pa-PK', 'Western Punjabi', '\p{Arabic}' ),
            'Uyghur' => array( 'uig-CN', 'ug-CN', 'Uyghur', '\p{Arabic}' ),
            'Central Kurdish' => array( 'ckb-IQ', 'ckb-IQ', 'Central Kurdish', '\p{Arabic}' ),
        );
    }

    /**
     * The complete right-to-left translations: the locale is right to left and names itself in its script, the
     * interface language switch offers it as right to left, the catalogue, its ezoe and its ezjscore catalogues are
     * written in the script, not left in English, and it needs no fallback of its own: the other countries of the
     * language fall back to it.
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'rightToLeftTranslations' )]
    public function testRightToLeftTranslationIsWrittenInItsScript( $code, $http, $name, $script )
    {
        $values = $this->iniValues( $this->root . '/share/locale/' . $code . '.ini' );
        $this->assertSame( 'rtl', strtolower( $values['TextDirection'] ?? '' ), "$code: TextDirection" );
        $this->assertSame( $http, $values['ContentLanguage'] ?? '', "$code: ContentLanguage" );
        // "Arabic" or, from CLDR, "Arabic (Saudi Arabia)"
        $this->assertMatchesRegularExpression( '/^' . $name . '( \(.+\))?$/', $values['InternationalLanguageName'] ?? '', "$code: InternationalLanguageName" );
        $this->assertMatchesRegularExpression( '/^[' . $script . '\s()]+$/u', $values['LanguageName'] ?? '', "$code: LanguageName" );
        // the translation has its own file, so it needs no fallback; every other country of the language that
        // settings/i18n.ini names falls back to it
        $i18n = (string)file_get_contents( $this->root . '/settings/i18n.ini' );
        $this->assertDoesNotMatchRegularExpression( '/^FallbackLanguages\[' . preg_quote( $code, '/' ) . '\]=/m', $i18n );
        preg_match_all( '/^FallbackLanguages\[(' . substr( $code, 0, 3 ) . '-[A-Z]+)\]=(\S*)\s*$/m', $i18n, $m, PREG_SET_ORDER );
        foreach ( $m as $row )
            $this->assertSame( $code, $row[2], "FallbackLanguages[{$row[1]}]" );
        foreach ( array( 'share', 'extension/ezoe', 'extension/ezjscore' ) as $base )
        {
            $ts = (string)file_get_contents( $this->root . '/' . $base . '/translations/' . $code . '/translation.ts' );
            preg_match_all( '/<translation>([^<]+)<\/translation>/u', $ts, $m );
            $inScript = count( preg_grep( '/' . $script . '/u', $m[1] ) );
            $this->assertGreaterThan( 0.85 * count( $m[1] ), $inScript, "$base $code: translations in the $name script" );
        }
    }

    /**
     * "Login" names the user's login (user name) in the user, session, audit and package lists and forms, not the
     * act of logging in: the right-to-left translations say so, as Hebrew did from the start.
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'rightToLeftTranslations' )]
    public function testTheLoginColumnIsNamedLikeAUserName( $code, $http, $name, $script )
    {
        $ts = $this->root . '/share/translations/' . $code . '/translation.ts';
        $field = $this->translationOf( $ts, 'design/admin/user/edit', 'Login' );
        foreach ( array( 'design/admin/user', 'design/admin/setup/session', 'design/admin/audit', 'kernel/user' ) as $context )
            $this->assertSame( $field, $this->translationOf( $ts, $context, 'Login' ), "$code: Login in $context" );
        $this->assertNotSame( $field, $this->translationOf( $ts, 'design/admin/error/kernel', 'Login' ),
                              "$code: the Login button of the error page is the act of logging in" );
    }
}
