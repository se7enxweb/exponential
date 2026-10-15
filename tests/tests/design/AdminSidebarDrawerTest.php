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
        // 1180px: the right column starts closed although it is saved open beside the content, opens as a drawer (the
        // left steps aside) with the focus inside it, Escape closes it and gives the focus back to its tab. The
        // drawer's state is its own preference, admin_right_drawer; the wide-window choice admin_right_sidebar is
        // never written below 1280px
        $this->assertSame( array( 'start:L-', 'click-right:-R', 'escape:L-', 'click-left:--', 'click-right:-R', 'click-left:L-' ), $s['mid1180']['states'] );
        $this->assertSame( array( 'right1', 'right', 'right1' ), $s['mid1180']['focusLog'], 'into the drawer, back to its tab on Escape' );
        $this->assertSame( array( 'admin_right_drawer=1', 'admin_right_drawer=0', 'admin_left_sidebar=0', 'admin_right_drawer=1',
                                  'admin_right_drawer=0', 'admin_left_sidebar=1' ), $s['mid1180']['saved'] );

        // 800px: both are drawers, one at a time; only the right drawer's state is saved
        $this->assertSame( array( 'start:--', 'click-left:L-', 'click-right:-R', 'escape:--' ), $s['narrow800']['states'] );
        $this->assertSame( array( 'admin_right_drawer=1', 'admin_right_drawer=0' ), $s['narrow800']['saved'] );

        // a left column saved closed opens and saves at 1180px
        $this->assertSame( array( 'start:--', 'click-left:L-' ), $s['midStoredClosed']['states'] );

        // resizing: the saved wide-window choice comes back on a wide window; the drawer's state does not change it
        $this->assertSame( array( 'start:LR', 'resize:1180:L-', 'click-right:-R', 'resize:1440:LR' ), $s['resize']['states'] );
        $this->assertSame( array( 'admin_right_drawer=1' ), $s['resize']['saved'] );
    }

    public function testTheRightDrawersStateIsTheUsersPreference()
    {
        $s = self::harness();
        // saved open: open again on the next page of a tablet-sized window and at 960px (the 200% zoom) ...
        $this->assertSame( array( 'start:-R' ), $s['drawerSaved1180']['states'] );
        $this->assertSame( array( 'start:-R', 'click-right:--' ), $s['drawerSaved960']['states'] );
        $this->assertSame( array( 'admin_right_drawer=0' ), $s['drawerSaved960']['saved'] );
        // ... a page opened with the drawer restored does not take the focus
        $this->assertSame( array(), $s['drawerSaved1180']['focusLog'] );
        // not on a phone (it would cover the page; nothing is saved there) and not beside the content
        $this->assertSame( array( 'start:--', 'click-right:-R', 'click-right:--' ), $s['drawerSavedPhone']['states'] );
        $this->assertSame( array(), $s['drawerSavedPhone']['saved'] );
        $this->assertSame( array( 'start:LR' ), $s['drawerSaved1440']['states'] );
        // an eZPreference for a signed-in user: written through window.a4Pref and on <html> by the attributes template
        $tpl = self::read( 'design/admin4/templates/parts/a4_preference_attributes.tpl' );
        $this->assertStringContainsString( "'admin_right_drawer'", $tpl );
        $this->assertStringContainsString( 'const drawerPref = "admin_right_drawer";', self::read( 'design/admin4/javascript/ezadmin_menubar.js' ) );
    }

    public function testTheOpenDrawerKeepsTheFocus()
    {
        $s = self::harness();
        // Tab cycles the drawer's controls and its tab, Shift+Tab backwards, Escape returns to the tab
        $this->assertSame( array( 'start:--', 'click-right:-R', 'tab>right2:-R', 'tab>right:-R', 'shift-tab>right2:-R',
                                  'shift-tab>right1:-R', 'escape:--' ), $s['trap']['states'] );
        $this->assertSame( 'right', $s['trap']['focused'] );
        // the focus somewhere else (a click on the page) is left alone
        $this->assertSame( array( 'start:--', 'click-right:-R', 'focus:left:-R', 'tab>page:-R' ), $s['trapOutside']['states'] );
        // the tab reports the state
        foreach ( array( 'admin4' => 'design/admin4/templates/page_topmenu.tpl',
                         'admin4l' => 'design/admin4l/templates/explayouts/block/admin_sidebar_toggles.tpl' ) as $design => $path )
        {
            $this->assertStringContainsString( 'class="sidebar-control right" aria-controls="rightmenu" aria-expanded="false"', self::read( $path ), $design );
        }
    }

    /**
     * @return array the scenarios of tests/tests/design/js/sidebar_drawer.js
     */
    private function harness()
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
        return $s;
    }
}
