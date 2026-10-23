<?php
/**
 * Stand-alone month names (#16654): [LongMonthNames] and [ShortMonthNames] of a locale file hold the form used inside
 * a date (CLDR format context, MMMM/MMM); the optional groups [LongMonthNamesStandalone] and
 * [ShortMonthNamesStandalone] hold the form of a month named on its own (CLDR stand-alone context, LLLL/LLL), as in a
 * calendar heading or a month list. In Polish, Russian, Czech, Finnish, Greek, Catalan ... the two differ:
 * "15 października 2025", but "październik 2025" above a calendar.
 *
 * eZLocale::longMonthNameStandalone() and shortMonthNameStandalone() read the new groups and fall back to
 * longMonthName() and shortMonthName() where a locale has none.
 *
 * The expected names are fixed strings, not read from ICU: CLDR changes between ICU versions, the test must not.
 * No kernel bootstrap, no database: the locale INI files are read from share/locale. Fixed timestamp, UTC.
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezlocale
 */

class expLocaleStandaloneMonthNamesTest extends PHPUnit\Framework\TestCase
{
    /** 2025-10-15 12:00:00 UTC, a Wednesday in October */
    const STAMP = 1760529600;

    const MONTH_KEYS = array( 'jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec' );

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

    /**
     * October inside a date and on its own, for locales whose languages inflect the month name.
     */
    public static function inflectingLocaleProvider()
    {
        return array(
            'pol-PL' => array( 'pol-PL', 'października', 'październik' ),
            'rus-RU' => array( 'rus-RU', 'октября', 'октябрь' ),
            'ukr-UA' => array( 'ukr-UA', 'жовтня', 'жовтень' ),
            'bel-BY' => array( 'bel-BY', 'кастрычніка', 'кастрычнік' ),
            'cze-CZ' => array( 'cze-CZ', 'října', 'říjen' ),
            'slk-SK' => array( 'slk-SK', 'októbra', 'október' ),
            'cro-HR' => array( 'cro-HR', 'listopada', 'listopad' ),
            'lit-LT' => array( 'lit-LT', 'spalio', 'spalis' ),
            'fin-FI' => array( 'fin-FI', 'lokakuuta', 'lokakuu' ),
            'ell-GR' => array( 'ell-GR', 'Οκτωβρίου', 'Οκτώβριος' ),
            'hye-AM' => array( 'hye-AM', 'հոկտեմբերի', 'հոկտեմբեր' ),
            'cat-ES' => array( 'cat-ES', 'd’octubre', 'octubre' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('inflectingLocaleProvider')]
    public function testTheDateUsesTheFormatFormAndTheMonthOnItsOwnTheStandaloneForm( $code, $inDate, $onItsOwn )
    {
        $locale = eZLocale::create( $code );
        $this->assertTrue( $locale->isValid(), "$code is valid" );
        $this->assertSame( $inDate, $locale->longMonthName( 10 ), "$code: [LongMonthNames] oct" );
        $this->assertSame( $onItsOwn, $locale->longMonthNameStandalone( 10 ), "$code: [LongMonthNamesStandalone] oct" );
        $this->assertStringContainsString( $inDate, $locale->formatDate( self::STAMP ), "$code: the long date" );
        $this->assertStringContainsString( $inDate, $locale->formatDateTime( self::STAMP ), "$code: the long date and time" );
    }

    public function testPolishAndRussianLongDates()
    {
        $this->assertSame( 'środa, 15 października 2025', eZLocale::create( 'pol-PL' )->formatDate( self::STAMP ) );
        $this->assertSame( '15 октября 2025 г.', eZLocale::create( 'rus-RU' )->formatDate( self::STAMP ) );
        $this->assertSame( 'październik', eZLocale::create( 'pol-PL' )->longMonthNameStandalone( 10 ) );
        $this->assertSame( 'май', eZLocale::create( 'rus-RU' )->longMonthNameStandalone( 5 ) );
        $this->assertSame( 'мая', eZLocale::create( 'rus-RU' )->longMonthName( 5 ) );
    }

    public function testTheStandaloneListIsKeyedLikeTheMonthNameList()
    {
        $locale = eZLocale::create( 'rus-RU' );
        $list = $locale->monthsNamesStandalone();
        $this->assertSame( array_keys( $locale->monthsNames() ), array_keys( $list ) );
        $this->assertSame( 'январь', $list['jan'] );
        $this->assertSame( $list, $locale->attribute( 'month_name_standalone_list' ) );
        $this->assertSame( 'января', $locale->attribute( 'month_name_list' )['jan'] );
    }

    public function testALocaleWithoutTheGroupsFallsBackToTheNormalNames()
    {
        foreach ( array( 'eng-GB', 'ger-DE', 'fre-FR' ) as $code )
        {
            $locale = eZLocale::create( $code );
            foreach ( range( 1, 12 ) as $month )
            {
                $this->assertSame( $locale->longMonthName( $month ), $locale->longMonthNameStandalone( $month ), "$code long $month" );
                $this->assertSame( $locale->shortMonthName( $month ), $locale->shortMonthNameStandalone( $month ), "$code short $month" );
            }
            $this->assertSame( $locale->monthsNames(), $locale->monthsNamesStandalone(), $code );
        }
        // pol-PL has a long stand-alone group but no short one: the short name falls back.
        $polish = eZLocale::create( 'pol-PL' );
        $this->assertSame( $polish->shortMonthName( 10 ), $polish->shortMonthNameStandalone( 10 ) );
    }

    public function testAMissingStandaloneNameFallsBackForThatMonthOnly()
    {
        $locale = eZLocale::create( 'rus-RU' );
        $locale->LongMonthNamesStandalone['jan'] = '';
        $this->assertSame( 'января', $locale->longMonthNameStandalone( 1 ) );
        $this->assertSame( 'января', $locale->monthsNamesStandalone()['jan'] );
        $this->assertSame( 'февраль', $locale->longMonthNameStandalone( 2 ) );
    }

    public function testAMonthOutsideOneToTwelveHasNoName()
    {
        $locale = eZLocale::create( 'pol-PL' );
        $this->assertNull( $locale->longMonthNameStandalone( 0 ) );
        $this->assertNull( $locale->longMonthNameStandalone( 13 ) );
        $this->assertNull( $locale->shortMonthNameStandalone( 13 ) );
    }

    public static function standaloneGroupProvider()
    {
        $cases = array();
        foreach ( glob( dirname( __DIR__, 4 ) . '/share/locale/*.ini' ) as $file )
        {
            $code = basename( $file, '.ini' );
            foreach ( array( 'LongMonthNames', 'ShortMonthNames' ) as $group )
            {
                if ( strpos( (string) file_get_contents( $file ), "[{$group}Standalone]" ) !== false )
                    $cases["$code $group"] = array( $code, $group );
            }
        }
        return $cases;
    }

    public function testThereAreStandaloneGroups()
    {
        $this->assertGreaterThanOrEqual( 40, count( self::standaloneGroupProvider() ) );
    }

    /**
     * Every stand-alone group has all twelve months, the file has the group it stands beside, and it differs from
     * that group in at least one month (a copy would only be a second place to maintain).
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('standaloneGroupProvider')]
    public function testEveryStandaloneGroupIsCompleteAndDiffers( $code, $group )
    {
        $ini = new eZINI( "$code.ini", 'share/locale', null, false, false );
        $this->assertTrue( $ini->hasGroup( $group ), "$code has [$group]" );
        $differs = false;
        foreach ( self::MONTH_KEYS as $key )
        {
            $name = (string) $ini->variable( "{$group}Standalone", $key );
            $this->assertNotSame( '', $name, "$code: [{$group}Standalone] $key" );
            $this->assertTrue( mb_check_encoding( $name, 'UTF-8' ), "$code: [{$group}Standalone] $key is UTF-8" );
            if ( $name !== (string) $ini->variable( $group, $key ) )
                $differs = true;
        }
        $this->assertTrue( $differs, "$code: [{$group}Standalone] differs from [$group]" );
    }
}
