<?php
/**
 * Every locale file in share/locale: it parses with eZINI, eZLocale loads it, it has the keys eZLocale reads, its
 * names are valid UTF-8, and dates, times, numbers and currency format through it. The files generated from CLDR for
 * 6.0.15 are also checked against the conventions in doc/languages.md (code form, HTTP code, utf-8, the license
 * note, the right-to-left note).
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

    /** Languages written right to left; their generated files carry a note, eZLocale has no direction key. */
    const RTL_LANGUAGES = array( 'ara', 'heb', 'fas', 'urd', 'pus', 'pnb', 'snd', 'uig' );

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

    public function testRightToLeftAndAddedLocalesAreSelectable()
    {
        foreach ( array( 'fas-IR', 'urd-PK', 'ara-EG', 'ben-BD', 'vie-VN', 'swa-KE', 'ron-RO', 'ger-AT', 'esl-AR' ) as $code )
            $this->assertContains( $code, eZLocale::localeList( false, false ), "$code is in the locale list" );
    }
}
