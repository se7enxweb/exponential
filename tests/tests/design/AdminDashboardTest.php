<?php
/**
 * The admin4 dashboard's key figures and chart: every figure has an icon tile, the week's figure and its trend come
 * from the 14 days the chart already counts (no query of their own), the trend says its change in words and an
 * arrow, not by colour, and the chart's numbers are there as a table.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminDashboardTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminDashboardTest extends PHPUnit\Framework\TestCase
{
    private static function read( $path )
    {
        return (string)file_get_contents( dirname( __DIR__, 3 ) . '/' . $path );
    }

    public function testEveryFigureHasAnIconTile()
    {
        $tpl = self::read( 'design/admin4/templates/content/dashboard.tpl' );
        preg_match_all( '/class="a4-dash-figure(?: [^"]*)?"/', $tpl, $figures );
        preg_match_all( '/<span class="a4-dash-figure-ico" aria-hidden="true"><span class="a4-icon a4-bi-[a-z0-9-]+"><\/span><\/span>/', $tpl, $tiles );
        $this->assertNotEmpty( $figures[0] );
        $this->assertSame( count( $figures[0] ), count( $tiles[0] ), 'every key figure has a hidden icon tile' );

        $components = self::read( 'design/admin4/stylesheets/components.css' );
        preg_match_all( '/a4-bi-([a-z0-9-]+)/', implode( ' ', $tiles[0] ), $icons );
        foreach ( array_unique( $icons[1] ) as $icon )
            $this->assertStringContainsString( ".a4-bi-$icon {", $components, "icon class .a4-bi-$icon is defined" );
    }

    public function testTheWeekAndItsTrendComeFromTheChartsDays()
    {
        $tpl = self::read( 'design/admin4/templates/content/dashboard.tpl' );
        $this->assertDoesNotMatchRegularExpression( '/\$count_week\s*=\s*fetch\(/', $tpl, 'the week is summed from $days, not fetched again' );
        $this->assertStringContainsString( '{set $count_week = sum( $count_week, $d.count )}', $tpl );
        // $days runs oldest first: the first seven are the week before, the last seven this week
        $this->assertStringContainsString( '{if lt( $n, 7 )}{set $count_prev_week = sum( $count_prev_week, $d.count )}{else}{set $count_week = sum( $count_week, $d.count )}{/if}', $tpl );
        $this->assertStringContainsString( '{for 13 to 0 as $i}', $tpl, 'the days are built oldest first' );
        $this->assertStringContainsString( '{set $count_prev_week = sum( $count_prev_week, $d.count )}', $tpl );
        $this->assertStringContainsString( '{def $week_delta = sub( $count_week, $count_prev_week )}', $tpl );
    }

    public function testTheTrendSaysItsChangeInWordsAndAnArrow()
    {
        $tpl = self::read( 'design/admin4/templates/content/dashboard.tpl' );
        $this->assertMatchesRegularExpression( '/<span class="a4-dash-trend">/', $tpl );
        foreach ( array( 'a4-bi-arrow-up-right', 'a4-bi-arrow-down-right', 'a4-bi-dash' ) as $arrow )
            $this->assertStringContainsString( '<span class="a4-icon ' . $arrow . '" aria-hidden="true"></span>', $tpl );
        $this->assertSame( 2, substr_count( $tpl, "'%delta against the 7 days before'|i18n( 'design/admin/dashboard'" ) );
        $this->assertStringContainsString( "'As many as the 7 days before'|i18n( 'design/admin/dashboard' )", $tpl );

        $css = self::read( 'design/admin4/stylesheets/admin4.css' );
        $this->assertMatchesRegularExpression( '/\.a4-dash-figure > \.a4-dash-trend \{[^}]*color: var\(--a4-muted\)/s', $css,
            'the trend is not coloured good or bad: a rise is not always good' );
        $this->assertMatchesRegularExpression( '/\.a4-dash-figure > \.a4-dash-trend \{[^}]*grid-column: 1 \/ -1/s', $css );
        // the label rule must leave the trend alone, or its column 2 (more specific) wins over the trend's span
        preg_match_all( '/^([^{}\n]*)\{[^}]*grid-column: 2;/m', $css, $columnTwo );
        foreach ( $columnTwo[1] as $selector )
            if ( strpos( $selector, 'a4-dash-figure' ) !== false && strpos( $selector, 'span' ) !== false )
                $this->assertStringContainsString( ':not(.a4-dash-trend)', $selector, "$selector would pull the trend into column 2" );
        $this->assertMatchesRegularExpression( '/a\.a4-dash-figure:is\(:hover, :focus-visible\)[^{]*\{[^}]*text-decoration: underline/', $css,
            'a linked figure shows it is a link beyond its arrow' );
    }

    public function testTheChartsNumbersAreATable()
    {
        $tpl = self::read( 'design/admin4/templates/content/dashboard.tpl' );
        $this->assertMatchesRegularExpression( '/<details class="a4-dash-data">\s*<summary>/', $tpl );
        $this->assertMatchesRegularExpression( '/<table class="a4-table">\s*<caption class="a4-sr-only">[^<]+<\/caption>\s*<thead><tr><th scope="col">/', $tpl );
        $this->assertStringContainsString( '<td class="a4-num">{$d.count}</td>', $tpl );
    }

    public function testTheNewStringsAreTranslated()
    {
        foreach ( array( 'ger-DE', 'eng-US' ) as $locale )
        {
            $ts = self::read( "share/translations/$locale/translation.ts" );
            $start = strpos( $ts, '<name>design/admin/dashboard</name>' );
            $this->assertNotFalse( $start );
            $context = substr( $ts, $start, strpos( $ts, '</context>', $start ) - $start );
            foreach ( array( '%delta against the 7 days before', 'As many as the 7 days before', 'Show the numbers', 'Day', 'Published' ) as $source )
                $this->assertStringContainsString( "<source>$source</source>", $context, "$locale: $source" );
        }
    }
}
