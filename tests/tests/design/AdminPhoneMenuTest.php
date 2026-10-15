<?php
/**
 * admin4's header on narrow windows, checked on the files alone (no database):
 *
 *  PM-01 - Escape closes the open phone menu (below 576px), sets aria-expanded to false and gives the focus back to
 *          the menu button; an open search scope popup in the menu, a focus elsewhere on the page or a script that
 *          took the key keep the menu open (ezadmin_menubar.js navbarToggle() run in node, tests/tests/design/js/
 *          phone_menu.js; skipped without node)
 *  PM-02 - the open phone menu ends under its search field (admin3's 7rem under the list left about 80px of empty
 *          panel), and the search field takes the panel's width
 *  PM-03 - from 576px the header search field gives way last: 122px at 576px beside the 152px logo (166px beside the
 *          old 114px image, 106px before that), 230px from 684px
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminPhoneMenuTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminBaseStylesheetsTest.php';

class AdminPhoneMenuTest extends PHPUnit\Framework\TestCase
{
    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function css( $file )
    {
        return preg_replace( '#/\*.*?\*/#s', '', (string)file_get_contents( self::root() . '/design/admin4/stylesheets/' . $file ) );
    }

    /** The inner text of every "@media <query> { ... }" block of $css, joined */
    private static function media( $css, $query )
    {
        $inner = '';
        $offset = 0;
        $head = '@media ' . $query . ' {';
        while ( ( $start = strpos( $css, $head, $offset ) ) !== false )
        {
            $i = $start + strlen( $head );
            $depth = 1;
            $from = $i;
            for ( ; $i < strlen( $css ) && $depth > 0; $i++ )
            {
                if ( $css[$i] === '{' ) $depth++;
                if ( $css[$i] === '}' ) $depth--;
            }
            $inner .= substr( $css, $from, $i - 1 - $from ) . "\n";
            $offset = $i;
        }
        return $inner;
    }

    /** PM-01 */
    public function testEscapeClosesThePhoneMenu()
    {
        $node = trim( (string)shell_exec( 'command -v node 2>/dev/null' ) );
        if ( $node === '' )
            $this->markTestSkipped( 'node is not installed' );
        $out = (string)shell_exec( escapeshellarg( $node ) . ' ' . escapeshellarg( __DIR__ . '/js/phone_menu.js' ) . ' '
                                   . escapeshellarg( self::root() . '/design/admin4/javascript/ezadmin_menubar.js' ) );
        $s = array();
        foreach ( array_filter( explode( "\n", $out ) ) as $line )
        {
            $row = json_decode( $line, true );
            $this->assertIsArray( $row, 'the harness ran: ' . $out );
            $s[$row['name']] = $row;
        }
        $this->assertCount( 8, $s );

        $this->assertTrue( $s['click opens']['open'] );
        $this->assertSame( 'true', $s['click opens']['expanded'] );
        foreach ( array( 'escape on the button closes', 'escape in the menu closes and focuses the button',
                         'escape after the popup closed closes' ) as $name )
        {
            $this->assertFalse( $s[$name]['open'], $name );
            $this->assertFalse( $s[$name]['bodyOpen'], $name );
            $this->assertSame( 'false', $s[$name]['expanded'], $name );
            $this->assertSame( 'icon', $s[$name]['focus'], $name . ': the focus goes back to the menu button' );
        }
        foreach ( array( 'escape with the scope popup open keeps the menu', 'escape with the focus on the page keeps the menu',
                         'escape taken by another script keeps the menu' ) as $name )
        {
            $this->assertTrue( $s[$name]['open'], $name );
            $this->assertSame( 'true', $s[$name]['expanded'], $name );
            $this->assertSame( array(), $s[$name]['log'], $name . ': the focus stays' );
        }
        $this->assertFalse( $s['escape while closed does nothing']['open'] );
        $this->assertSame( array(), $s['escape while closed does nothing']['log'] );
        // the capture phase: the search's own Escape listener on the document closes its popup only after this one
        // has seen it open
        $this->assertTrue( $s['click opens']['captured'] );
    }

    /** PM-02 */
    public function testTheOpenPhoneMenuEndsUnderItsSearch()
    {
        $phone = AdminBaseStylesheetsTest::rules( self::media( self::css( 'admin4.css' ), 'screen and (max-width: 575.98px)' ) );
        $this->assertSame( 'var(--a4-space-2) var(--a4-space-5)', $phone['.navbar-bar']['padding-block'] );
        $this->assertSame( 'stretch', $phone['.navbar-bar > .header-search-mobile']['align-self'] );
        $this->assertSame( 'var(--a4-space-4)', $phone['.navbar-bar > .header-search-mobile']['padding-inline'] );
        // the rule it overrides is still admin3's
        $this->assertStringContainsString( 'padding-block: 0.5rem 7rem;', self::css( 'pagelayout.css' ) );
    }

    /** PM-03 */
    public function testTheHeaderSearchFieldIsWiderOnSmallTablets()
    {
        $tablet = AdminBaseStylesheetsTest::rules( self::media( self::css( 'admin4.css' ), 'screen and (min-width: 576px)' ) );
        $width = $tablet['#header .searchblock input.searchtext']['width'];
        // 454px with the inline logo (152px wide, the old image 114px): the tools keep one row at 576px
        $this->assertSame( 'clamp(6rem, calc(100vw - 454px), 230px)', $width );
    }
}
