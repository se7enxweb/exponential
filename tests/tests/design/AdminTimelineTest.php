<?php
/**
 * The .a4-timeline component (components.css, both copies) and where admin4 draws a chronological list with it: the
 * dashboard's audit block and the node view's Audit tab, through the shared parts/audit_timeline.tpl. A timeline is
 * an ordered list with machine-readable times; its dots repeat the state the result badge says in words; its line is
 * an edge, so forced colors keep it. doc/bc/6.0/admin4-styling.md (Timeline).
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminTimelineTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminTimelineTest extends PHPUnit\Framework\TestCase
{
    const PART = 'design/admin4/templates/parts/audit_timeline.tpl';

    /** The admin4 templates that draw audit events through the part */
    const USERS = array( 'design/admin4/templates/dashboard/audit.tpl', 'design/admin4/templates/tabs/audit/node.tpl' );

    private static function read( $path )
    {
        $file = dirname( __DIR__, 3 ) . '/' . $path;
        self::assertFileExists( $file );
        return (string)file_get_contents( $file );
    }

    public function testTheComponentIsInBothStylesheets()
    {
        foreach ( array( 'design/admin4/stylesheets/components.css', 'design/admin/stylesheets/components.css' ) as $css )
        {
            $source = self::read( $css );
            $this->assertStringContainsString( '.a4-timeline > li {', $source, $css );
            $this->assertStringContainsString( '.a4-timeline-time {', $source, $css );
            foreach ( array( 'ok', 'warn', 'bad', 'info' ) as $state )
                $this->assertStringContainsString( ".a4-timeline > li.is-$state::after { background-color: var(--a4-$state); }", $source, "$css: dot for is-$state" );
            // the line is an edge (a fill would vanish in forced colors), and the dots keep their colour there
            $this->assertMatchesRegularExpression( '/\.a4-timeline > li::before \{[^}]*border-left: 2px solid var\(--a4-line-strong\)/', $source, $css );
            $forced = substr( $source, (int)strrpos( $source, '@media (forced-colors: active)' ) );
            $this->assertStringContainsString( '.a4-timeline > li::after { forced-color-adjust: none;', $forced, $css );
        }
    }

    public function testThePartIsAnOrderedListWithMachineReadableTimes()
    {
        $part = self::read( self::PART );
        $this->assertStringContainsString( '<ol class="a4-timeline">', $part );
        $this->assertStringContainsString( '<time class="a4-timeline-time" datetime="{$e.time_utc|wash}">', $part );
        // the state is said in words (the badge) as well as by the dot's colour
        $this->assertStringContainsString( '<span class="a4-badge{if $audit_state} {$audit_state}{/if}">', $part );
        $this->assertStringContainsString( "{'Result'|i18n( 'design/admin/audit' )}", $part );
        $this->assertStringContainsString( "{'Actor'|i18n( 'design/admin/audit' )}", $part );
        // every value that comes from an event is washed
        preg_match_all( '/\{\$e\.(\w+)(\|wash)?\}/', $part, $outputs, PREG_SET_ORDER );
        $this->assertNotEmpty( $outputs );
        foreach ( $outputs as $output )
            $this->assertSame( '|wash', $output[2] ?? '', '$e.' . $output[1] . ' is written without |wash' );
    }

    public function testTheAuditListsUseThePartInsteadOfATable()
    {
        foreach ( self::USERS as $path )
        {
            $template = self::read( $path );
            $this->assertStringContainsString( "{include uri='design:parts/audit_timeline.tpl' events=", $template, $path );
            $this->assertStringNotContainsString( '<table', $template, $path );
        }
    }
}
