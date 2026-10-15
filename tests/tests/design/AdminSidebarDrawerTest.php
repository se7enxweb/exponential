<?php
/**
 * admin4's side columns: beside the content (the left from 1024px, the right from 1280px) each follows the user's
 * saved choice; below its breakpoint a column is a drawer over the page that starts closed, never opens together with
 * the other one, closes on Escape with the focus back on its tab and saves nothing. The breakpoints of the script are
 * the stylesheets' own.
 *
 * The behaviour runs ezadmin_menubar.js's sidebarToggle() in node (tests/tests/design/js/sidebar_drawer.js, a small
 * stand-in for matchMedia, jQuery and the key listener); without node that test is skipped, the breakpoint test runs.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminSidebarDrawerTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminSidebarDrawerTest extends PHPUnit\Framework\TestCase
{
    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function read( $path )
    {
        return (string)file_get_contents( self::root() . '/' . $path );
    }

    public function testTheScriptsBreakpointsAreTheStylesheets()
    {
        $js = self::read( 'design/admin4/javascript/ezadmin_menubar.js' );
        $this->assertStringContainsString( 'const narrow = window.matchMedia("(max-width: 1023.98px)");', $js );
        $this->assertStringContainsString( 'right: window.matchMedia("(max-width: 1279.98px)")', $js );
        // the left column takes room from 1024px (pagelayout.css), the right one from 1280px (admin4.css)
        $this->assertStringContainsString( '@media screen and (min-width: 1024px) {', self::read( 'design/admin4/stylesheets/pagelayout.css' ) );
        $this->assertStringContainsString( '@media screen and (min-width: 1024px) and (max-width: 1279.98px) {', self::read( 'design/admin4/stylesheets/admin4.css' ) );
    }

    public function testEachColumnIsADrawerBelowItsBreakpoint()
    {
        $node = trim( (string)shell_exec( 'command -v node 2>/dev/null' ) );
        if ( $node === '' )
        {
            $this->markTestSkipped( 'node is not installed' );
        }
        $out = shell_exec( escapeshellarg( $node ) . ' ' . escapeshellarg( __DIR__ . '/js/sidebar_drawer.js' ) . ' '
                           . escapeshellarg( self::root() . '/design/admin4/javascript/ezadmin_menubar.js' ) );
        $s = json_decode( (string)$out, true );
        $this->assertIsArray( $s, 'the harness ran: ' . $out );

        // 1440px: both columns beside the content, a click saves the choice
        $this->assertSame( array( 'start:LR', 'click-right:L-', 'click-right:LR' ), $s['wide1440']['states'] );
        $this->assertSame( array( 'admin_right_sidebar=0', 'admin_right_sidebar=1' ), $s['wide1440']['saved'] );

        // 1180px: the right column starts closed although it is saved open, opens as a drawer (the left steps
        // aside), Escape closes it and gives the focus back to its tab; nothing of the drawer is saved
        $this->assertSame( array( 'start:L-', 'click-right:-R', 'escape:L-', 'click-left:--', 'click-right:-R', 'click-left:L-' ), $s['mid1180']['states'] );
        $this->assertSame( 'right', $s['mid1180']['focused'] );
        $this->assertSame( array( 'admin_left_sidebar=0', 'admin_left_sidebar=1' ), $s['mid1180']['saved'], 'only the left column, beside the content, saves' );

        // 800px: both are drawers, one at a time, nothing saved
        $this->assertSame( array( 'start:--', 'click-left:L-', 'click-right:-R', 'escape:--' ), $s['narrow800']['states'] );
        $this->assertSame( array(), $s['narrow800']['saved'] );

        // a left column saved closed opens and saves at 1180px
        $this->assertSame( array( 'start:--', 'click-left:L-' ), $s['midStoredClosed']['states'] );

        // resizing: the saved choice comes back on a wide window, the drawer is not saved
        $this->assertSame( array( 'start:LR', 'resize:1180:L-', 'click-right:-R', 'resize:1440:LR' ), $s['resize']['states'] );
        $this->assertSame( array(), $s['resize']['saved'] );
    }
}
