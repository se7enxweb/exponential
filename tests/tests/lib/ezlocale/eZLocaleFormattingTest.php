<?php
/**
 * Tests of lib/ezlocale: eZLocale (number, currency, date and time formatting, names, parsing back with
 * internalNumber()/internalCurrency()), every locale file in share/locale, eZDate, eZTime, eZDateTime, eZCurrency
 * and eZDateUtils.
 *
 * Fixed timestamps and the UTC time zone, so the results do not depend on the day or the server. No kernel
 * bootstrap, no database: the locale INI files are read from share/locale.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezlocale
 */

class eZLocaleFormattingTest extends PHPUnit\Framework\TestCase
{
    /** 2023-11-14 22:13:20 UTC, a Tuesday */
    const STAMP = 1700000000;

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

    private static function locale( $code )
    {
        return eZLocale::create( $code );
    }

    // ---------------------------------------------------------------- every locale file

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

    #[\PHPUnit\Framework\Attributes\DataProvider('localeFileProvider')]
    public function testEveryLocaleFileLoadsWithCompleteNames( $code )
    {
        $locale = self::locale( $code );
        $this->assertTrue( $locale->isValid(), "$code is valid" );
        $this->assertNotSame( '', (string)$locale->languageName(), 'language name' );
        $this->assertCount( 7, $locale->weekDays() );
        $this->assertCount( 12, $locale->months() );
        foreach ( range( 1, 12 ) as $month )
        {
            $this->assertNotSame( '', (string)$locale->longMonthName( $month ), "long name of month $month" );
            $this->assertNotSame( '', (string)$locale->shortMonthName( $month ), "short name of month $month" );
        }
        foreach ( $locale->weekDays() as $day )
        {
            $this->assertNotSame( '', (string)$locale->longDayName( $day ), "long name of day $day" );
            $this->assertNotSame( '', (string)$locale->shortDayName( $day ), "short name of day $day" );
        }
        $this->assertNotSame( '', $locale->formatDateTime( self::STAMP ) );
        $this->assertNotSame( '', $locale->formatShortDate( self::STAMP ) );
        $this->assertNotSame( '', $locale->formatTime( self::STAMP ) );
        $this->assertNotSame( '', $locale->formatNumber( 1234.5 ) );
        $this->assertNotSame( '', $locale->formatCurrency( 1234.5 ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('localeFileProvider')]
    public function testEveryLocaleReadsItsOwnNumbersBack( $code )
    {
        $locale = self::locale( $code );
        foreach ( array( 0, 1, -1, 12.5, 1234.56, -98765.4 ) as $number )
        {
            $formatted = $locale->formatNumber( $number );
            $this->assertEqualsWithDelta( $number, (float)$locale->internalNumber( $formatted ), 0.001, "$code: '$formatted'" );
        }
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('localeFileProvider')]
    public function testEveryLocaleReadsItsOwnCurrencyBack( $code )
    {
        $locale = self::locale( $code );
        foreach ( array( 0, 1, 12.5, 1234.56, 1234567.5 ) as $number )
        {
            $formatted = $locale->formatCleanCurrency( $number );
            $expected = round( $number, (int)$locale->currencyDecimalCount() );
            $this->assertEqualsWithDelta( $expected, (float)$locale->internalCurrency( $formatted ), 0.001, "$code: '$formatted'" );
        }
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('localeFileProvider')]
    public function testEveryLocaleHasDistinctDecimalAndThousandsSymbols( $code )
    {
        $locale = self::locale( $code );
        $this->assertNotSame( $locale->decimalSymbol(), $locale->thousandsSeparator(), 'numbers' );
        $this->assertNotSame( $locale->currencyDecimalSymbol(), $locale->currencyThousandsSeparator(), 'currency' );
    }

    // ---------------------------------------------------------------- codes and names

    public function testLocaleCodes()
    {
        $locale = self::locale( 'nor-NO' );
        $this->assertSame( 'nor-NO', $locale->localeCode() );
        $this->assertSame( 'nor', $locale->languageCode() );
        $this->assertSame( 'NO', $locale->countryCode() );
        $this->assertSame( 'nb-NO', $locale->httpLocaleCode() );
    }

    public function testLocaleWithVariation()
    {
        $locale = self::locale( 'ger-DE@euro' );
        $this->assertSame( 'ger-DE', $locale->localeCode() );
        $this->assertSame( 'ger-DE@euro', $locale->localeFullCode() );
        $this->assertSame( 'euro', $locale->countryVariation() );
    }

    public static function localeInformationProvider()
    {
        return array(
            'plain'          => array( 'eng-GB', 'eng', 'GB', '', '' ),
            'with variation' => array( 'nor-NO@intl', 'nor', 'NO', '', 'intl' ),
            'with charset'   => array( 'eng-GB.utf-8', 'eng', 'GB', 'utf-8', '' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('localeInformationProvider')]
    public function testLocaleInformationSplitsTheCode( $string, $language, $country, $charset, $variation )
    {
        $info = self::locale( 'eng-GB' )->localeInformation( $string );
        $this->assertSame( $language, $info['language'] );
        $this->assertSame( $country, $info['country'] );
        $this->assertSame( $charset, (string)$info['charset'] );
        $this->assertSame( $variation, (string)$info['country-variation'] );
    }

    public function testDayAndMonthNames()
    {
        $gb = self::locale( 'eng-GB' );
        $this->assertSame( 'January', $gb->longMonthName( 1 ) );
        $this->assertSame( 'Dec', $gb->shortMonthName( 12 ) );
        $this->assertSame( 'Monday', $gb->longDayName( 1 ) );
        $this->assertSame( 'Sun', $gb->shortDayName( 0 ) );
        $de = self::locale( 'ger-DE' );
        $this->assertSame( 'Januar', $de->longMonthName( 1 ) );
        $this->assertSame( 'Montag', $de->longDayName( 1 ) );
    }

    public function testMondayFirst()
    {
        $this->assertTrue( self::locale( 'ger-DE' )->isMondayFirst() );
        $this->assertFalse( self::locale( 'eng-US' )->isMondayFirst() );
    }

    // ---------------------------------------------------------------- numbers and currency

    public static function numberProvider()
    {
        return array(
            'gb thousands'     => array( 'eng-GB', 1234567.891, '1,234,567.89' ),
            'gb negative'      => array( 'eng-GB', -1.5, '-1.50' ),
            'gb zero'          => array( 'eng-GB', 0, '0.00' ),
            'de thousands'     => array( 'ger-DE', 1234567.891, '1.234.567,89' ),
            'no thousands'     => array( 'nor-NO', 1234.5, '1.234,50' ),
            'se thousands'     => array( 'swe-SE', 1234.5, '1.234,50' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('numberProvider')]
    public function testFormatNumber( $code, $number, $expected )
    {
        $this->assertSame( $expected, self::locale( $code )->formatNumber( $number ) );
    }

    public static function currencyProvider()
    {
        return array(
            'gb'           => array( 'eng-GB', 1234.5, '1,234.50' ),
            'gb negative'  => array( 'eng-GB', -2, '-2.00' ),
            'us'           => array( 'eng-US', 1234.5, '1,234.50' ),
            'de'           => array( 'ger-DE', 1234.5, '1.234,50' ),
            'jp no cents'  => array( 'jpn-JP', 1234.5, '1,235' ),
        );
    }

    /**
     * formatCleanCurrency() is the amount without the currency symbol.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('currencyProvider')]
    public function testFormatCleanCurrency( $code, $number, $expected )
    {
        $this->assertSame( $expected, self::locale( $code )->formatCleanCurrency( $number ) );
    }

    public function testFormatCurrencyPutsTheSymbolWhereTheLocaleSays()
    {
        $this->assertStringStartsWith( '£', self::locale( 'eng-GB' )->formatCurrency( 1234.5 ) );
        $this->assertStringEndsWith( '1,234.50', self::locale( 'eng-GB' )->formatCurrency( 1234.5 ) );
        $this->assertSame( '€ 1.234,50', self::locale( 'ger-DE' )->formatCurrency( 1234.5 ) );
    }

    public function testFormatCurrencyWithAnotherSymbol()
    {
        $this->assertSame( 'NOK 1,234.50', self::locale( 'eng-GB' )->formatCurrencyWithSymbol( 1234.5, 'NOK' ) );
    }

    /**
     * internalCurrency() matched the thousands groups with the separator of the numbers, then removed the one of the
     * currency: an amount of a locale whose two separators differ (swe-SE: "1.234,56" kr) was returned unconverted.
     */
    public function testInternalCurrencyUsesTheCurrencyThousandsSeparator()
    {
        $locale = self::locale( 'ger-DE' );
        $locale->ThousandsSeparator = ' ';
        $locale->CurrencyThousandsSeparator = '.';
        $this->assertSame( '1234567.50', $locale->internalCurrency( '1.234.567,50' ) );
        $locale->ThousandsSeparator = '.';
        $locale->CurrencyThousandsSeparator = ' ';
        $this->assertSame( '1234567.50', $locale->internalCurrency( '1 234 567,50' ) );
    }

    public function testInternalNumberLeavesTextItCannotReadUnchanged()
    {
        $locale = self::locale( 'eng-GB' );
        $this->assertSame( '12abc', $locale->internalNumber( '12abc' ) );
        $this->assertSame( '1234.5', $locale->internalNumber( '1,234.5' ) );
        $this->assertSame( '', $locale->internalNumber( null ) );
        $this->assertSame( array( '1' ), $locale->internalNumber( array( '1' ) ) );
    }

    public function testInternalCurrencyReadsTheFormattedValueBack()
    {
        foreach ( array( 'eng-GB', 'ger-DE', 'nor-NO', 'fre-FR' ) as $code )
        {
            $locale = self::locale( $code );
            $this->assertEqualsWithDelta( 1234.5, (float)$locale->internalCurrency( $locale->formatCleanCurrency( 1234.5 ) ), 0.001, $code );
        }
    }

    public function testCurrencyNames()
    {
        $gb = self::locale( 'eng-GB' );
        $this->assertSame( 'GBP', $gb->currencyShortName() );
        $this->assertSame( '£', $gb->currencySymbol() );
        $this->assertSame( 'EUR', self::locale( 'ger-DE' )->currencyShortName() );
    }

    // ---------------------------------------------------------------- dates and times

    public static function dateTimeProvider()
    {
        return array(
            'gb time'            => array( 'eng-GB', 'formatTime', '10:13:20 pm' ),
            'no time'            => array( 'nor-NO', 'formatTime', '22:13:20' ),
            'gb short time'      => array( 'eng-GB', 'formatShortTime', '10:13 pm' ),
            'no short time'      => array( 'nor-NO', 'formatShortTime', '22:13' ),
            'gb date'            => array( 'eng-GB', 'formatDate', 'Tuesday 14 November 2023' ),
            'gb short date'      => array( 'eng-GB', 'formatShortDate', '14/11/2023' ),
            'gb date time'       => array( 'eng-GB', 'formatDateTime', 'Tuesday 14 November 2023 10:13:20 pm' ),
            'gb short date time' => array( 'eng-GB', 'formatShortDateTime', '14/11/2023 10:13 pm' ),
            'us short date'      => array( 'eng-US', 'formatShortDate', '11/14/2023' ),
            'us time'            => array( 'eng-US', 'formatTime', '10:13:20 pm' ),
            'de date'            => array( 'ger-DE', 'formatDate', 'Dienstag, 14. November 2023' ),
            'de short date'      => array( 'ger-DE', 'formatShortDate', '14.11.2023' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('dateTimeProvider')]
    public function testFormatDateAndTime( $code, $method, $expected )
    {
        $this->assertSame( $expected, self::locale( $code )->$method( self::STAMP ) );
    }

    public static function formatTypeProvider()
    {
        return array(
            'year month day'     => array( 'formatDateType', '%Y-%m-%d', '2023-11-14' ),
            'day names'          => array( 'formatDateType', '%l %D', 'Tuesday Tue' ),
            'month names'        => array( 'formatDateType', '%F %M', 'November Nov' ),
            'two digit year'     => array( 'formatDateType', '%y', '23' ),
            'day without zero'   => array( 'formatDateType', '%j', '14' ),
            'time'               => array( 'formatTimeType', '%H:%i:%s', '22:13:20' ),
            'twelve hours'       => array( 'formatTimeType', '%g %a', '10 pm' ),
            'literal text'       => array( 'formatDateType', 'at %Y', 'at 2023' ),
            'date time combined' => array( 'formatDateTimeType', '%Y-%m-%d %H:%i', '2023-11-14 22:13' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('formatTypeProvider')]
    public function testFormatWithOwnFormat( $method, $format, $expected )
    {
        $this->assertSame( $expected, self::locale( 'eng-GB' )->$method( $format, self::STAMP ) );
    }

    public function testMeridiemName()
    {
        $gb = self::locale( 'eng-US' );
        $this->assertSame( 'pm', $gb->meridiemName( self::STAMP ) );
        $this->assertSame( 'PM', $gb->meridiemName( self::STAMP, true ) );
        $this->assertSame( 'am', $gb->meridiemName( gmmktime( 9, 0, 0, 1, 1, 2020 ) ) );
    }

    public function testTransformToPHPFormat()
    {
        $this->assertSame( 'Y-m-d', eZLocale::transformToPHPFormat( '%Y-%m-%d', array( 'Y', 'm', 'd' ) ) );
    }

    // ---------------------------------------------------------------- eZDate, eZTime, eZDateTime

    public function testDateFromMDY()
    {
        $date = eZDate::create( 2, 29, 2024 );
        $this->assertSame( 2024, (int)$date->year() );
        $this->assertSame( 2, (int)$date->month() );
        $this->assertSame( 29, (int)$date->day() );
        $this->assertTrue( $date->isValid() );
    }

    public function testDateAdjustCrossesMonthAndYear()
    {
        $date = eZDate::create( 12, 31, 2023 );
        $date->adjustDate( 0, 1, 0 );
        $this->assertSame( '2024-01-01', date( 'Y-m-d', $date->timeStamp() ) );
        $date->adjustDate( 1, 0, 0 );
        $this->assertSame( '2024-02-01', date( 'Y-m-d', $date->timeStamp() ) );
    }

    public function testDateComparisons()
    {
        $a = eZDate::create( 1, 1, 2020 );
        $b = eZDate::create( 1, 2, 2020 );
        $this->assertTrue( $b->isGreaterThan( $a ) );
        $this->assertFalse( $a->isGreaterThan( $b ) );
        $this->assertFalse( $a->isGreaterThan( $a->duplicate() ) );
        $this->assertTrue( $a->isGreaterThan( $a->duplicate(), true ) );
        $this->assertTrue( $a->isEqualTo( $a->duplicate() ) );
        $this->assertFalse( $a->isEqualTo( $b ) );
    }

    public function testDateAttributes()
    {
        $date = new eZDate( self::STAMP );
        $this->assertTrue( $date->hasAttribute( 'year' ) );
        $this->assertSame( 2023, (int)$date->attribute( 'year' ) );
        $this->assertSame( 11, (int)$date->attribute( 'month' ) );
        $this->assertSame( 14, (int)$date->attribute( 'day' ) );
        $this->assertFalse( $date->hasAttribute( 'no_such_attribute' ) );
    }

    public function testTimeOfDay()
    {
        $time = eZTime::create( 10, 30, 15 );
        $this->assertSame( 10, (int)$time->hour() );
        $this->assertSame( 30, (int)$time->minute() );
        $this->assertSame( 15, (int)$time->second() );
        $this->assertSame( 10 * 3600 + 30 * 60 + 15, (int)$time->timeOfDay() );
        $this->assertSame( 86400, eZTime::secondsPerDay() );
    }

    public function testTimeAdjustWrapsAroundMidnight()
    {
        $time = eZTime::create( 23, 30, 0 );
        $time->adjustTime( 1, 0, 0 );
        $this->assertSame( 0, (int)$time->hour() );
        $this->assertSame( 30, (int)$time->minute() );
    }

    public function testTimeComparisons()
    {
        $a = eZTime::create( 8, 0, 0 );
        $b = eZTime::create( 9, 0, 0 );
        $this->assertTrue( $b->isGreaterThan( $a ) );
        $this->assertFalse( $a->isGreaterThan( $b ) );
        $copy = $a->duplicate();
        $this->assertTrue( $a->isEqualTo( $copy ) );
    }

    public function testDateTimeParts()
    {
        $dt = new eZDateTime( self::STAMP );
        $this->assertSame( 2023, (int)$dt->year() );
        $this->assertSame( 11, (int)$dt->month() );
        $this->assertSame( 14, (int)$dt->day() );
        $this->assertSame( 22, (int)$dt->hour() );
        $this->assertSame( 13, (int)$dt->minute() );
        $this->assertSame( 20, (int)$dt->second() );
        $this->assertSame( self::STAMP, (int)$dt->timeStamp() );
    }

    public function testDateTimeSetters()
    {
        $dt = new eZDateTime( self::STAMP );
        $dt->setMDYHMS( 6, 15, 2021, 8, 5, 9 );
        $this->assertSame( '2021-06-15 08:05:09', date( 'Y-m-d H:i:s', $dt->timeStamp() ) );
        $dt->setHMS( 1, 2, 3 );
        $this->assertSame( '2021-06-15 01:02:03', date( 'Y-m-d H:i:s', $dt->timeStamp() ) );
        $dt->setMDY( 1, 2, 2000 );
        $this->assertSame( '2000-01-02 01:02:03', date( 'Y-m-d H:i:s', $dt->timeStamp() ) );
    }

    public function testDateTimeAdjust()
    {
        $dt = eZDateTime::create( 23, 0, 0, 12, 31, 2023 );
        $dt->adjustDateTime( 2, 0, 0, 0, 0, 0 );
        $this->assertSame( '2024-01-01 01:00:00', date( 'Y-m-d H:i:s', $dt->timeStamp() ) );
        $dt->adjustDateTime( 0, 0, 0, 1, 0, 0 );
        $this->assertSame( '2024-02-01 01:00:00', date( 'Y-m-d H:i:s', $dt->timeStamp() ) );
    }

    public function testDateTimeComparisonsAndConversions()
    {
        $a = new eZDateTime( self::STAMP );
        $b = new eZDateTime( self::STAMP + 1 );
        $this->assertTrue( $b->isGreaterThan( $a ) );
        $copy = $a->duplicate();
        $this->assertTrue( $a->isEqualTo( $copy ) );
        $this->assertFalse( $a->isGreaterThan( $b ) );
        $this->assertSame( 14, (int)$a->toDate()->day() );
        $this->assertSame( 22, (int)$a->toTime()->hour() );
    }

    // ---------------------------------------------------------------- eZCurrency, eZDateUtils

    public function testCurrencyObject()
    {
        $currency = new eZCurrency( 1234.5 );
        $gb = self::locale( 'eng-GB' );
        $currency->setLocale( $gb );
        $this->assertEqualsWithDelta( 1234.5, $currency->value(), 0.0001 );
        $this->assertSame( 'GBP', $currency->shortName() );
        $currency->setValue( 2 );
        $this->assertEqualsWithDelta( 2, $currency->value(), 0.0001 );
    }

    public function testRfc1123Date()
    {
        $this->assertSame( 'Tue, 14 Nov 2023 22:13:20 GMT', eZDateUtils::rfc1123Date( self::STAMP ) );
    }

    public function testRfc850Date()
    {
        $this->assertSame( 'Tuesday, 14-Nov-2023 22:13:20 GMT', eZDateUtils::rfc850Date( self::STAMP ) );
    }

    public function testTextToDateReadsRfc1123Back()
    {
        $this->assertSame( self::STAMP, eZDateUtils::textToDate( eZDateUtils::rfc1123Date( self::STAMP ) ) );
    }
}
