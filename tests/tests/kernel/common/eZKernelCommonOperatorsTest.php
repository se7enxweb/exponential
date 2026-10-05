<?php
/**
 * Tests of template operators in kernel/common that work on plain values, called directly through modify():
 *   - month_overview (eZDateOperatorCollection): the calendar of every month from 2019 to 2031 in a Monday-first
 *     and a Sunday-first locale, links, and the previous/next months
 *   - autolink (eZAutoLinkOperator): mail addresses and URLs, shortening, links already in the text
 *   - simpletags (eZSimpleTagsOperator): the default tag list and a custom one
 *   - alphabet (eZAlphabetOperator): alphabets from content.ini ranges and sequences
 *   - eztoc (eZTOCOperator): the table of contents of nested sections
 *
 * The settings each operator reads are set by the test and put back afterwards. No database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group templateoperators
 */

class eZKernelCommonOperatorsTplStub
{
    public $missing = array();

    public function ini()
    {
        return eZINI::instance( 'template.ini' );
    }

    public function missingParameter( $operatorName, $parameter )
    {
        $this->missing[] = "$operatorName:$parameter";
        return false;
    }
}

class eZKernelCommonOperatorsTest extends PHPUnit\Framework\TestCase
{
    private $saved = array();

    private $timezone;

    private $locale;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->timezone = date_default_timezone_get();
        date_default_timezone_set( 'UTC' );
        $this->locale = array_key_exists( 'eZLocaleStringDefault', $GLOBALS ) ? array( $GLOBALS['eZLocaleStringDefault'] ) : null;
    }

    protected function tearDown(): void
    {
        foreach ( array_reverse( $this->saved ) as $entry )
        {
            list( $file, $group, $name, $had, $value ) = $entry;
            $ini = eZINI::instance( $file );
            if ( $had )
                $ini->setVariable( $group, $name, $value );
            else
                $ini->removeSetting( $group, $name );
        }
        $this->saved = array();
        date_default_timezone_set( $this->timezone );
        if ( $this->locale === null )
            unset( $GLOBALS['eZLocaleStringDefault'] );
        else
            $GLOBALS['eZLocaleStringDefault'] = $this->locale[0];
    }

    private function setting( $file, $group, $name, $value )
    {
        $ini = eZINI::instance( $file );
        $had = $ini->hasVariable( $group, $name );
        $this->saved[] = array( $file, $group, $name, $had, $had ? $ini->variable( $group, $name ) : null );
        $ini->setVariable( $group, $name, $value );
    }

    private function useLocale( $code )
    {
        $GLOBALS['eZLocaleStringDefault'] = $code;
    }

    private static function runOperator( $operator, $name, $value, $named )
    {
        $tpl = new eZKernelCommonOperatorsTplStub();
        $operator->modify( $tpl, $name, array(), '', '', $value, $named, false );
        return $value;
    }

    // ---------------------------------------------------------------- month_overview

    private static function monthOverview( $date, $items = array(), $optional = false )
    {
        return self::runOperator( new eZDateOperatorCollection(), 'month_overview', $items,
                          array( 'field' => 'published', 'date' => $date, 'optional' => $optional ) );
    }

    public static function monthProvider()
    {
        $cases = array();
        foreach ( array( 'ger-DE', 'eng-US' ) as $locale )
        {
            foreach ( range( 2019, 2031 ) as $year )
            {
                foreach ( range( 1, 12 ) as $month )
                    $cases["$locale $year-$month"] = array( $locale, $year, $month );
            }
        }
        return $cases;
    }

    /**
     * Every day of the month is in the calendar exactly once, in order, each in the column of its weekday, and the
     * weeks are full rows of seven.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('monthProvider')]
    public function testMonthOverviewHasEveryDayOnceInItsWeekdayColumn( $localeCode, $year, $month )
    {
        $this->useLocale( $localeCode );
        $overview = self::monthOverview( gmmktime( 12, 0, 0, $month, 15, $year ) );
        $locale = eZLocale::instance();
        $weekDays = $locale->weekDays();
        $this->assertCount( 7, $overview['weekdays'] );

        $seen = array();
        foreach ( $overview['weeks'] as $week )
        {
            $this->assertCount( 7, $week );
            foreach ( $week as $column => $day )
            {
                if ( $day === false )
                    continue;
                $seen[] = $day['day'];
                $wday = (int)gmdate( 'w', gmmktime( 0, 0, 0, $month, $day['day'], $year ) );
                $this->assertSame( $weekDays[$column], $wday, "day {$day['day']} in column $column" );
            }
        }
        $this->assertSame( range( 1, (int)gmdate( 't', gmmktime( 0, 0, 0, $month, 1, $year ) ) ), $seen );
        $this->assertSame( $year, $overview['year'] );
    }

    public function testMonthOverviewLinksTheDaysWithItems()
    {
        $this->useLocale( 'eng-GB' );
        $items = array( array( 'published' => gmmktime( 10, 0, 0, 3, 5, 2024 ) ),
                        array( 'published' => gmmktime( 10, 0, 0, 4, 5, 2024 ) ),
                        array( 'other' => 1 ) );
        $overview = self::monthOverview( gmmktime( 0, 0, 0, 3, 1, 2024 ), $items, array( 'link' => '/blog' ) );
        $links = array();
        foreach ( $overview['weeks'] as $week )
        {
            foreach ( $week as $day )
            {
                if ( $day && $day['link'] )
                    $links[$day['day']] = $day['link'];
            }
        }
        $this->assertSame( array( 5 => '/blog/(year)/2024/(month)/3/(day)/5' ), $links );
    }

    public function testMonthOverviewPreviousAndNextMonths()
    {
        $this->useLocale( 'eng-GB' );
        $overview = self::monthOverview( gmmktime( 0, 0, 0, 1, 10, 2024 ), array(),
                                         array( 'previous' => array( 'link' => '/p' ), 'next' => array( 'link' => '/n' ) ) );
        $this->assertSame( array( 'month' => 'December', 'year' => 2023, 'link' => '/p/(year)/2023/(month)/12' ), $overview['previous'] );
        $this->assertSame( array( 'month' => 'February', 'year' => 2024, 'link' => '/n/(year)/2024/(month)/2' ), $overview['next'] );
        $this->assertSame( 'January', $overview['month'] );
    }

    public function testMonthOverviewWithoutNextHasNoNextMonth()
    {
        $this->useLocale( 'eng-GB' );
        $overview = self::monthOverview( gmmktime( 0, 0, 0, 1, 10, 2024 ) );
        $this->assertFalse( $overview['next'] );
        $this->assertFalse( $overview['previous'] );
        $this->assertSame( '/(year)/2024/(month)/1', $overview['current']['link'] );
    }

    public function testMonthOverviewMarksTheCurrentDay()
    {
        $this->useLocale( 'eng-GB' );
        $overview = self::monthOverview( gmmktime( 0, 0, 0, 2, 1, 2024 ), array(),
                                         array( 'current' => gmmktime( 0, 0, 0, 2, 14, 2024 ), 'current_class' => 'today' ) );
        $highlighted = array();
        foreach ( $overview['weeks'] as $week )
        {
            foreach ( $week as $day )
            {
                if ( $day && $day['highlight'] )
                    $highlighted[] = array( $day['day'], $day['class'] );
            }
        }
        $this->assertSame( array( array( 14, 'today' ) ), $highlighted );
    }

    public function testMonthOverviewNeedsFieldAndDate()
    {
        $tpl = new eZKernelCommonOperatorsTplStub();
        $value = array();
        $operator = new eZDateOperatorCollection();
        $operator->modify( $tpl, 'month_overview', array(), '', '', $value, array( 'field' => false, 'date' => 1, 'optional' => false ), false );
        $operator->modify( $tpl, 'month_overview', array(), '', '', $value, array( 'field' => 'x', 'date' => false, 'optional' => false ), false );
        $this->assertSame( array( 'month_overview:field', 'month_overview:date' ), $tpl->missing );
    }

    // ---------------------------------------------------------------- autolink

    private function autolink( $text, $max = null )
    {
        $this->setting( 'template.ini', 'AutoLinkOperator', 'MaxCharacters', '72' );
        $this->setting( 'template.ini', 'AutoLinkOperator', 'Methods', array( 'http', 'https', 'ftp' ) );
        return self::runOperator( new eZAutoLinkOperator(), 'autolink', $text, array( 'max_chars' => $max ) );
    }

    public static function autolinkProvider()
    {
        return array(
            'url'          => array( 'see http://example.invalid/page now', 'see <a href="http://example.invalid/page" title="http://example.invalid/page">http://example.invalid/page</a> now' ),
            'https query'  => array( 'https://t1.example.invalid/a?b=c&d=e', '<a href="https://t1.example.invalid/a?b=c&d=e" title="https://t1.example.invalid/a?b=c&d=e">https://t1.example.invalid/a?b=c&d=e</a>' ),
            'trailing dot' => array( 'Go to http://example.invalid.', 'Go to <a href="http://example.invalid" title="http://example.invalid">http://example.invalid</a>.' ),
            'mail'         => array( 'write to info@t1.example.invalid', "write to <a href='mailto:info@t1.example.invalid'>info@t1.example.invalid</a>" ),
            'existing a'   => array( '<a href="http://example.invalid">x</a>', '<a href="http://example.invalid">x</a>' ),
            'no link'      => array( 'nothing here', 'nothing here' ),
            'other scheme' => array( 'gopher://example.invalid', 'gopher://example.invalid' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('autolinkProvider')]
    public function testAutolink( $text, $expected )
    {
        $this->assertSame( $expected, $this->autolink( $text ) );
    }

    public function testAutolinkShortensLongUrls()
    {
        $url = 'http://example.invalid/' . str_repeat( 'abcdefghij', 5 );
        $html = $this->autolink( $url, 20 );
        $this->assertStringContainsString( 'href="' . $url . '"', $html );
        $this->assertSame( 1, preg_match( '#>([^<]*)</a>#', $html, $m ) );
        $this->assertStringContainsString( '...', $m[1] );
        $this->assertLessThanOrEqual( 20, strlen( $m[1] ) );
    }

    // ---------------------------------------------------------------- simpletags

    private function simpletags( $text, $listName = false )
    {
        $this->setting( 'template.ini', 'SimpleTagsOperator', 'TagList', array(
            'text' => ';;htmlspecialchars,nl2br',
            'literal' => '<pre>;</pre>;htmlspecialchars',
            'strong' => '<b>;</b>;htmlspecialchars,nl2br',
            'emphasize' => '<i>;</i>;htmlspecialchars,nl2br' ) );
        $this->setting( 'template.ini', 'SimpleTagsOperator', 'TagList_t1', array( 'x' => '[;]' ) );
        $this->setting( 'template.ini', 'SimpleTagsOperator', 'IncludeList', array() );
        return self::runOperator( new eZSimpleTagsOperator(), 'simpletags', $text, array( 'listname' => $listName ) );
    }

    public static function simpletagsProvider()
    {
        return array(
            'text is escaped'     => array( 'a < b & "c"', 'a &lt; b &amp; &quot;c&quot;' ),
            'single quote'        => array( "it's", 'it&#039;s' ),
            'newlines'            => array( "a\nb", "a<br />\nb" ),
            'strong'              => array( 'x <strong>bold</strong> y', 'x <b>bold</b> y' ),
            'emphasize escaped'   => array( '<emphasize>a<b</emphasize>', '<i>a&lt;b</i>' ),
            'literal'             => array( "<literal><x>\n</literal>", "<pre>&lt;x&gt;\n</pre>" ),
            'unknown tag escaped' => array( '<script>x</script>', '&lt;script&gt;x&lt;/script&gt;' ),
            'unclosed tag'        => array( '<strong>open', '<b>open</b>' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('simpletagsProvider')]
    public function testSimpletags( $text, $expected )
    {
        $this->assertSame( $expected, $this->simpletags( $text ) );
    }

    public function testSimpletagsWithACustomList()
    {
        // a list without a text entry escapes text and unknown tags with htmlspecialchars only
        $this->assertSame( 'a[b]&lt;strong&gt;', $this->simpletags( 'a<x>b</x><strong>', 't1' ) );
    }

    // ---------------------------------------------------------------- alphabet

    public function testAlphabetOfRangesAndSequences()
    {
        $this->setting( 'content.ini', 'AlphabeticalFilterSettings', 'AlphabetList', array( 't1' => '97-99,229, 248' ) );
        $this->setting( 'content.ini', 'AlphabeticalFilterSettings', 'ContentFilterList', array( 't1' ) );
        $this->assertSame( array( 'a', 'b', 'c', 'å', 'ø' ), eZAlphabetOperator::fetchAlphabet() );
    }

    public function testAlphabetFallsBackToTheEnglishAlphabet()
    {
        $this->setting( 'content.ini', 'AlphabeticalFilterSettings', 'ContentFilterList', array( 'no-such-alphabet-t1' ) );
        $this->assertSame( range( 'a', 'z' ), eZAlphabetOperator::fetchAlphabet() );
    }

    public function testAlphabetIsOffWithAnEmptyFilterList()
    {
        $this->setting( 'content.ini', 'AlphabeticalFilterSettings', 'ContentFilterList', array() );
        $this->assertFalse( eZAlphabetOperator::fetchAlphabet() );
    }

    public function testAlphabetOperatorReturnsTheAlphabet()
    {
        $this->setting( 'content.ini', 'AlphabeticalFilterSettings', 'AlphabetList', array( 't1' => '120-122' ) );
        $this->setting( 'content.ini', 'AlphabeticalFilterSettings', 'ContentFilterList', array( 't1' ) );
        $this->assertSame( array( 'x', 'y', 'z' ), self::runOperator( new eZAlphabetOperator(), 'alphabet', null, array() ) );
    }

    // ---------------------------------------------------------------- eztoc

    private static function toc( $xml, $attributeId = 7 )
    {
        $dom = new DOMDocument( '1.0', 'utf-8' );
        $dom->preserveWhiteSpace = false;
        $dom->loadXML( $xml );
        $operator = new eZTOCOperator();
        $operator->ObjectAttributeId = $attributeId;
        $text = $operator->handleSection( $dom->documentElement );
        while ( $operator->LastHeaderLevel > 0 )
        {
            $text .= "</li>\n</ul>\n";
            $operator->LastHeaderLevel--;
        }
        return $text;
    }

    public function testTocOfNestedSections()
    {
        $xml = '<section><section><header>One</header><section><header>One.A</header></section>'
             . '<section><header>One.B</header></section></section><section><header>Two</header></section></section>';
        $html = self::toc( $xml );
        $this->assertSame( array( 'eztoc7_1', 'eztoc7_1_1', 'eztoc7_1_2', 'eztoc7_2' ), self::anchors( $html ) );
        $this->assertSame( 2, substr_count( $html, '<ul>' ) );
        $this->assertSame( substr_count( $html, '<li>' ), substr_count( $html, '</li>' ) );
    }

    public function testTocEscapesTheHeaderText()
    {
        $html = self::toc( '<section><section><header>a &lt;b&gt; &amp; c</header></section></section>' );
        $this->assertStringContainsString( '>a &lt;b&gt; &amp; c</a>', $html );
    }

    public function testTocOfAValueThatIsNoAttributeIsEmpty()
    {
        $value = 'something';
        $operator = new eZTOCOperator();
        $operator->modify( new eZKernelCommonOperatorsTplStub(), 'eztoc', array(), '', '', $value, array( 'dom' => 'not an attribute' ), false );
        $this->assertSame( '', $value );
    }

    private static function anchors( $html )
    {
        preg_match_all( '/href="#([^"]+)"/', $html, $m );
        return $m[1];
    }
}
