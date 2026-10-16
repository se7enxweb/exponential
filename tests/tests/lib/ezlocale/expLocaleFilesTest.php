<?php
/**
 * Every locale file in share/locale: it parses with eZINI, eZLocale loads it, it has the keys eZLocale reads, its
 * names are valid UTF-8, and dates, times, numbers and currency format through it. The files generated from CLDR for
 * 6.0.15 are also checked against the conventions in doc/languages.md (code form, HTTP code, utf-8, the license
 * note, the right-to-left note). Every right-to-left file, and no other, has [LanguageSettings] TextDirection=rtl;
 * no file holds double-encoded UTF-8 (the old Hebrew file did).
 *
 * No kernel bootstrap, no database, no PHP extension: the files are plain INI. Fixed timestamp, UTC.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezlocale
 */

class expLocaleFilesTest extends PHPUnit\Framework\TestCase
{
    /** 2023-11-14 22:13:20 UTC, a Tuesday in November */
    const STAMP = 1700000000;

    /** Languages written right to left: their files carry a note and [LanguageSettings] TextDirection=rtl. */
    const RTL_LANGUAGES = array( 'ara', 'heb', 'fas', 'urd', 'pus', 'pnb', 'snd', 'uig', 'yid', 'ckb', 'syr', 'kas', 'nqo', 'div' );

    /** Every right-to-left locale file there must be (Arabic, Hebrew, Persian and Dari, Urdu, Pashto, Western
     * Punjabi, Sindhi, Uyghur, Yiddish, Central Kurdish, Syriac, Kashmiri, N'Ko, Dhivehi). */
    const RTL_LOCALES = array( 'ara-SA', 'ara-AE', 'ara-EG', 'ara-MA', 'ara-DZ', 'ara-TN', 'ara-IQ', 'ara-JO', 'ara-LB',
        'ara-SY', 'ara-KW', 'ara-QA', 'heb-IL', 'fas-IR', 'fas-AF', 'urd-PK', 'urd-IN', 'pus-AF', 'pnb-PK', 'snd-PK',
        'uig-CN', 'yid-US', 'ckb-IQ', 'syr-IQ', 'syr-SY', 'kas-IN', 'nqo-GN', 'div-MV' );

    private $timezone;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->timezone = date_default_timezone_get();
        date_default_timezone_set( 'UTC' );
    }

    protected function tearDown(): void
    {
        date_default_timezone_set( $this->timezone );
    }

    public static function localeFileProvider()
    {
        $cases = array();
        foreach ( glob( dirname( __DIR__, 4 ) . '/share/locale/*.ini' ) as $file )
        {
            $code = basename( $file, '.ini' );
            $cases[$code] = array( $code );
        }
        return $cases;
    }

    public static function generatedFileProvider()
    {
        $cases = array();
        foreach ( self::localeFileProvider() as $code => $case )
        {
            if ( strpos( (string) file_get_contents( "share/locale/$code.ini" ), 'Generated from Unicode CLDR' ) !== false )
                $cases[$code] = $case;
        }
        return $cases;
    }

    private static function isVariation( $code )
    {
        return strpos( $code, '@' ) !== false;
    }

    public function testThereAreLocaleFiles()
    {
        $this->assertGreaterThanOrEqual( 49, count( self::localeFileProvider() ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('localeFileProvider')]
    public function testFileIsUtf8AndParsesWithEzIni( $code )
    {
        $text = file_get_contents( "share/locale/$code.ini" );
        $this->assertTrue( mb_check_encoding( $text, 'UTF-8' ), "$code.ini is valid UTF-8" );
        // chi-HK.ini starts with a byte order mark, which eZINI skips.
        $this->assertStringStartsWith( '#?ini charset="utf-8"?', preg_replace( '/^\xEF\xBB\xBF/', '', $text ) );
        $ini = new eZINI( "$code.ini", 'share/locale', null, false, false );
        $this->assertTrue( $ini->hasGroup( 'RegionalSettings' ) || $ini->hasGroup( 'Charset' ) || $ini->hasGroup( 'Currency' ), "$code.ini has a group" );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('localeFileProvider')]
    public function testFileHasTheKeysEzLocaleReads( $code )
    {
        if ( self::isVariation( $code ) )
        {
            // A variation only overrides some keys of its base file, which must exist.
            $this->assertFileExists( 'share/locale/' . preg_replace( '/@.*/', '', $code ) . '.ini' );
            return;
        }
        $ini = new eZINI( "$code.ini", 'share/locale', null, false, false );
        $required = array(
            'RegionalSettings' => array( 'Country', 'LanguageName', 'InternationalLanguageName' ),
            'Charset' => array( 'Preferred' ),
            'HTTP' => array( 'ContentLanguage' ),
            'Currency' => array( 'Symbol', 'Name', 'ShortName', 'DecimalSymbol', 'ThousandsSeparator', 'FractDigits',
                                 'PositiveFormat', 'NegativeFormat' ),
            'Numbers' => array( 'DecimalSymbol', 'ThousandsSeparator', 'FractDigits' ),
            'DateTime' => array( 'TimeFormat', 'ShortTimeFormat', 'DateFormat', 'ShortDateFormat', 'DateTimeFormat',
                                 'ShortDateTimeFormat', 'MondayFirst' ),
        );
        foreach ( $required as $group => $keys )
        {
            foreach ( $keys as $key )
                $this->assertTrue( $ini->hasVariable( $group, $key ), "$code: [$group] $key" );
        }
        foreach ( array( 'ShortMonthNames', 'LongMonthNames' ) as $group )
        {
            foreach ( array( 'jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec' ) as $key )
                $this->assertNotSame( '', (string) $ini->variable( $group, $key ), "$code: [$group] $key" );
        }
        foreach ( array( 'ShortDayNames', 'LongDayNames' ) as $group )
        {
            foreach ( array( 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun' ) as $key )
                $this->assertNotSame( '', (string) $ini->variable( $group, $key ), "$code: [$group] $key" );
        }
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('localeFileProvider')]
    public function testEzLocaleFormatsDatesTimesNumbersAndCurrency( $code )
    {
        $locale = eZLocale::create( $code );
        $this->assertTrue( $locale->isValid(), "$code is valid" );

        $date = $locale->formatDate( self::STAMP );
        // The long date shows November by its long or short name, or as a number (Chinese: 11月).
        $shown = strpos( $date, (string) $locale->longMonthName( 11 ) ) !== false
              || strpos( $date, (string) $locale->shortMonthName( 11 ) ) !== false
              || strpos( $date, '11' ) !== false;
        $this->assertTrue( $shown, "$code date shows the month: '$date'" );
        $this->assertStringContainsString( '14', $date, "$code date shows the day" );
        $this->assertMatchesRegularExpression( '/2023|23/', $locale->formatShortDate( self::STAMP ), "$code short date" );
        $this->assertStringContainsString( '13', $locale->formatTime( self::STAMP ), "$code time shows the minutes" );
        $this->assertStringContainsString( '13', $locale->formatShortDateTime( self::STAMP ), "$code short date and time" );
        $this->assertStringNotContainsString( '%', $locale->formatDateTime( self::STAMP ), "$code: no format code left over" );

        $number = $locale->formatNumber( -1234567.891 );
        $this->assertStringContainsString( '567', $number, "$code number" );
        $this->assertEqualsWithDelta( -1234567.89, (float) $locale->internalNumber( $number ), 0.01, "$code number reads back: '$number'" );

        $currency = $locale->formatCurrency( 1234.5 );
        $this->assertStringContainsString( (string) $locale->currencySymbol(), $currency, "$code currency shows its symbol" );
        $this->assertStringNotContainsString( '%', $currency, "$code: currency format fully replaced" );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('localeFileProvider')]
    public function testNamesAreReadableText( $code )
    {
        if ( self::isVariation( $code ) )
        {
            $this->assertTrue( true );
            return;
        }
        $locale = eZLocale::create( $code );
        foreach ( array( $locale->languageName(), $locale->internationalLanguageName(), $locale->countryName() ) as $name )
        {
            $this->assertNotSame( '', trim( (string) $name ), "$code name" );
            $this->assertTrue( mb_check_encoding( (string) $name, 'UTF-8' ), "$code name is UTF-8" );
            $this->assertStringNotContainsString( "\u{FFFD}", (string) $name, "$code name has no replacement character" );
        }
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('generatedFileProvider')]
    public function testGeneratedFileFollowsTheConventions( $code )
    {
        $this->assertMatchesRegularExpression( '/^[a-z]{3}-[A-Z]{2}$/', $code, 'three-letter language, two-letter country' );
        $text = file_get_contents( "share/locale/$code.ini" );
        $this->assertStringContainsString( 'Unicode License v3', $text, 'CLDR license note' );

        $locale = eZLocale::create( $code );
        $this->assertSame( 'utf-8', $locale->charset() );
        list( $language, $country ) = explode( '-', $code );
        $this->assertMatchesRegularExpression( '/^[a-z]{2,3}-' . $country . '$/', $locale->httpLocaleCode(), 'HTTP code is language-COUNTRY' );
        $this->assertSame( 12, count( array_unique( array_map( array( $locale, 'longMonthName' ), range( 1, 12 ) ) ) ), 'twelve distinct month names' );
        $this->assertSame( 7, count( array_unique( array_map( array( $locale, 'longDayName' ), range( 0, 6 ) ) ) ), 'seven distinct day names' );
        $this->assertSame( in_array( $language, self::RTL_LANGUAGES, true ), strpos( $text, 'right-to-left' ) !== false, 'right-to-left note' );
        // eZLocale puts separators into a regular expression character class: one byte each.
        foreach ( array( $locale->decimalSymbol(), $locale->thousandsSeparator(), $locale->currencyDecimalSymbol(), $locale->currencyThousandsSeparator() ) as $symbol )
            $this->assertSame( 1, strlen( (string) $symbol ), "$code separator '$symbol' is one byte" );
        $this->assertGreaterThan( 200, count( (array) $locale->translatedCountryNames() ), 'country names' );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('localeFileProvider')]
    public function testTextDirectionKeyMatchesTheLanguage( $code )
    {
        $text = file_get_contents( "share/locale/$code.ini" );
        list( $language ) = explode( '-', $code );
        $rtl = in_array( $language, self::RTL_LANGUAGES, true ) || strpos( $text, 'right-to-left' ) !== false;
        $hasKey = (bool) preg_match( '/^TextDirection\s*=\s*rtl\s*$/m', $text );
        if ( self::isVariation( $code ) )
        {
            // A variation inherits the key from its base file; it never sets a different direction.
            $this->assertDoesNotMatchRegularExpression( '/^TextDirection\s*=/m', $text, "$code: no own TextDirection" );
            return;
        }
        $this->assertSame( $rtl, $hasKey, "$code: TextDirection=rtl exactly when the language is written right to left" );
        $this->assertDoesNotMatchRegularExpression( '/^TextDirection\s*=\s*(?!rtl\s*$)/m', $text, "$code: TextDirection is only ever rtl" );
        if ( $hasKey )
        {
            $ini = new eZINI( "$code.ini", 'share/locale', null, false, false );
            $this->assertSame( 'rtl', $ini->variable( 'LanguageSettings', 'TextDirection' ), "$code: the key is in [LanguageSettings]" );
            $this->assertStringContainsString( 'right-to-left', $text, "$code: right-to-left note" );
        }
        $this->assertSame( $rtl ? 'rtl' : 'ltr', eZLocale::create( $code )->textDirection(), "$code: eZLocale::textDirection()" );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('localeFileProvider')]
    public function testFileHasNoDoubleEncodedUtf8( $code )
    {
        // UTF-8 read as Latin-1 and encoded again: Hebrew letters become "×", Latin accents "Ã" or "Â".
        $text = file_get_contents( "share/locale/$code.ini" );
        foreach ( array( "\u{00C3}", "\u{00C2}", "\u{00D7}" ) as $sign )
            $this->assertStringNotContainsString( $sign, $text, "$code.ini has no double-encoded UTF-8 ('$sign')" );
    }

    public function testLatinHolySeeLocale()
    {
        $text = file_get_contents( 'share/locale/lat-VA.ini' );
        $this->assertTrue( mb_check_encoding( $text, 'UTF-8' ) );
        $this->assertStringContainsString( 'Generated from Unicode CLDR', $text );
        $locale = eZLocale::create( 'lat-VA' );
        $this->assertTrue( $locale->isValid() );
        $this->assertSame( 'Latina', $locale->languageName() );
        $this->assertSame( 'Latin', $locale->internationalLanguageName() );
        $this->assertSame( 'Vatican City', $locale->countryName() );
        $this->assertSame( 'la-VA', $locale->httpLocaleCode() );
        $this->assertSame( 'utf-8', $locale->charset() );
        $this->assertSame( 'EUR', $locale->currencyShortName() );
        $this->assertSame( '€', $locale->currencySymbol() );
        $this->assertSame( 'ltr', $locale->textDirection() );
        $this->assertSame( 'Ianuarius', $locale->longMonthName( 1 ) );
        $this->assertSame( 'December', $locale->longMonthName( 12 ) );
        $this->assertSame( 'Dominica', $locale->longDayName( 0 ) );
        $this->assertSame( 'Sabbatum', $locale->longDayName( 6 ) );
        $this->assertTrue( $locale->isMondayFirst() );
        $this->assertSame( 'Feria tertia, 14 Nov. 2023', $locale->formatDate( self::STAMP ) );
        $this->assertSame( '1.234,50 €', $locale->formatCurrency( 1234.5 ) );
    }

    public function testHebrewLocaleIsRegeneratedUtf8()
    {
        $text = file_get_contents( 'share/locale/heb-IL.ini' );
        $this->assertTrue( mb_check_encoding( $text, 'UTF-8' ) );
        $this->assertStringContainsString( 'Generated from Unicode CLDR', $text );
        $this->assertStringNotContainsString( 'windows-1255', $text );
        $locale = eZLocale::create( 'heb-IL' );
        $this->assertTrue( $locale->isValid() );
        $this->assertSame( 'עברית', $locale->languageName() );
        $this->assertSame( 'Hebrew', $locale->internationalLanguageName() );
        $this->assertSame( 'he-IL', $locale->httpLocaleCode() );
        $this->assertSame( 'utf-8', $locale->charset() );
        $this->assertSame( 'ILS', $locale->currencyShortName() );
        $this->assertSame( '₪', $locale->currencySymbol() );
        $this->assertSame( 'rtl', $locale->textDirection() );
        $this->assertSame( 'ינואר', $locale->longMonthName( 1 ) );
        $this->assertSame( 'יום ראשון', $locale->longDayName( 0 ) );
        foreach ( range( 1, 12 ) as $month )
            $this->assertMatchesRegularExpression( '/^[\x{05D0}-\x{05EA}\x{05F3}\x{0027} ]+$/u', $locale->longMonthName( $month ), "Hebrew month $month" );
    }

    public function testRightToLeftAndAddedLocalesAreSelectable()
    {
        foreach ( array( 'fas-IR', 'urd-PK', 'ara-EG', 'ben-BD', 'vie-VN', 'swa-KE', 'ron-RO', 'ger-AT', 'esl-AR', 'lat-VA', 'heb-IL', 'tlh-QO' ) as $code )
            $this->assertContains( $code, eZLocale::localeList( false, false ), "$code is in the locale list" );
    }

    public function testKlingonLocaleIsHandMade()
    {
        // Klingon (tlh, private-use region QO): CLDR has no data, so the file is written by hand, in the Latin
        // transcription (pIqaD lies in the Private Use Area), with no real currency (ISO 4217 XXX).
        $text = file_get_contents( 'share/locale/tlh-QO.ini' );
        $this->assertStringNotContainsString( 'Generated from Unicode CLDR', $text );
        $locale = eZLocale::create( 'tlh-QO' );
        $this->assertTrue( $locale->isValid() );
        $this->assertSame( 'tlhIngan Hol', $locale->languageName() );
        $this->assertSame( 'Klingon', $locale->internationalLanguageName() );
        $this->assertSame( 'tlh', $locale->httpLocaleCode() );
        $this->assertSame( 'utf-8', $locale->charset() );
        $this->assertSame( 'XXX', $locale->currencyShortName() );
        $this->assertSame( 'ltr', $locale->textDirection() );
        $this->assertSame( 'DaSjaj', $locale->longDayName( 1 ) );
        $this->assertSame( 12, count( array_unique( array_map( array( $locale, 'longMonthName' ), range( 1, 12 ) ) ) ) );
        $this->assertSame( 7, count( array_unique( array_map( array( $locale, 'longDayName' ), range( 0, 6 ) ) ) ) );
        // Latin letters and the apostrophe only: no Private Use Area character (pIqaD) in any name.
        foreach ( array_merge( array_map( array( $locale, 'longMonthName' ), range( 1, 12 ) ),
                               array_map( array( $locale, 'longDayName' ), range( 0, 6 ) ) ) as $name )
            $this->assertMatchesRegularExpression( "/^[A-Za-z' ]+$/", $name );
    }

    /** The world languages and regional variants added from CLDR in #521, all left to right. */
    const WORLD_LOCALES = array( 'ful-SN', 'aka-GH', 'ewe-GH', 'wol-SN', 'bam-ML', 'lin-CD', 'run-BI', 'lug-UG',
        'kik-KE', 'kab-DZ', 'zgh-MA', 'tuk-TM', 'kir-KG', 'tat-RU', 'che-RU', 'dzo-BT', 'bod-CN', 'san-IN', 'kok-IN',
        'mai-IN', 'mni-IN', 'sat-IN', 'doi-IN', 'sun-ID', 'ceb-PH', 'mri-NZ', 'haw-US', 'ton-TO', 'bre-FR', 'oci-FR',
        'ast-ES', 'fao-FO', 'roh-CH', 'gla-GB', 'fry-NL', 'kal-GL', 'sme-NO', 'esl-VE', 'esl-EC', 'esl-GT', 'esl-CU',
        'esl-DO', 'esl-BO', 'esl-UY', 'fre-SN', 'fre-CI', 'fre-CM', 'fre-MA', 'fre-CD', 'por-CV', 'eng-NG', 'eng-KE',
        'eng-GH', 'eng-PK', 'eng-PH', 'msa-SG', 'msa-BN', 'tam-LK', 'tam-SG', 'brx-IN', 'hsb-DE', 'dsb-DE' );

    public function testWorldLanguagesAreThereLeftToRightAndSelectable()
    {
        $list = eZLocale::localeList( false, false );
        foreach ( self::WORLD_LOCALES as $code )
        {
            $this->assertContains( $code, $list, "$code is offered as a content language" );
            $locale = eZLocale::create( $code );
            $this->assertSame( 'ltr', $locale->textDirection(), "$code is left to right" );
            $this->assertNotSame( '', (string) $locale->languageName(), "$code has a native name" );
        }
        $this->assertSame( 'Kirundi', eZLocale::create( 'run-BI' )->internationalLanguageName() );
        $this->assertSame( 'Malay (Malaysia)', eZLocale::create( 'msa-MY' )->internationalLanguageName() );
        $this->assertSame( 'CVE', eZLocale::create( 'por-CV' )->currencySymbol(), 'no invisible escudo symbol' );
    }

    public function testEveryRightToLeftLocaleIsThereAndSelectable()
    {
        $list = eZLocale::localeList( false, false );
        foreach ( self::RTL_LOCALES as $code )
        {
            $this->assertFileExists( "share/locale/$code.ini" );
            $this->assertContains( $code, $list, "$code is offered as a content language" );
            // The UI switch offers every locale code of this form (expUISwitchLanguageCatalogue).
            $this->assertMatchesRegularExpression( '/^[a-z]{3}-[A-Z]{2}$/', $code, "$code has the UI switch form" );
            $locale = eZLocale::create( $code );
            $this->assertTrue( $locale->isValid(), "$code is valid" );
            $this->assertSame( 'rtl', $locale->textDirection(), "$code is right to left" );
            $this->assertSame( 'rtl', eZLocale::textDirectionOf( $code ) );
            $this->assertNotSame( '', (string) $locale->formatDate( self::STAMP ), "$code formats a date" );
        }
        // And no other file is right to left: the list is complete.
        foreach ( $list as $code )
            if ( !in_array( $code, self::RTL_LOCALES, true ) )
                $this->assertSame( 'ltr', eZLocale::textDirectionOf( $code ), "$code is not in RTL_LOCALES, so left to right" );
    }
}
