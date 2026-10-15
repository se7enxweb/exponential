<?php
/**
 * admin4's header on narrow windows, checked on the files alone (no database):
 *
 *  PM-01 - Escape closes the open phone menu (below 576px), sets aria-expanded to false and gives the focus back to
 *          the menu button; an open search scope popup in the menu, a focus elsewhere on the page or a script that
 *          took the key keep the menu open. While it is open on a phone, Tab and Shift+Tab stay in the header (its
 *          row, the menu button, the panel), going round from its last control to its first (focus trap); the close button
 *          at the panel's end closes it and gives the focus back; a tap outside the header closes it; a window that
 *          grows past 575px closes it (ezadmin_menubar.js navbarToggle() run in node, tests/tests/design/js/
 *          phone_menu.js; skipped without node)
 *  PM-02 - the open phone menu is a grid of tiles: the search first and on a row of its own, then the tabs two or
 *          three to a row instead of admin3's column of centred links, with more room around them (16px above, 24px
 *          at the sides and the end) and one scroller, the panel
 *  PM-03 - from 576px the header search field gives way last: 122px at 576px beside the 152px logo (166px beside the
 *          old 114px image, 106px before that), 230px from 684px
 *  PM-04 - the open phone menu always fits the visible window: it starts under the header's row and is at most the
 *          dynamic viewport height (100dvh, after a 100vh fallback) less the header, so on iOS, where 100vh is the
 *          height with the browser's toolbars hidden, its end no longer lies under the bottom toolbar; it scrolls
 *          inside, does not chain its scrolling to the page, keeps clear of the safe area, and the page does not
 *          scroll sideways
 *  PM-05 - every target in the phone menu is at least 44 x 44px (Apple's Human Interface Guidelines): the tabs, the
 *          search field and its buttons, the user's actions and the close button
 *  PM-06 - the markup: the search first, the tabs, "More", the user's actions, the close button; the phone's own items
 *          are drawn below 576px only and the one-line menu's fitting (admin4.js) leaves them out; the user's actions
 *          follow the header's user menu (same links, same conditions) and stand for it on a phone
 *  PM-07 - closed, the panel is out of the Tab order (visibility, after its slide); the open menu's button shows a
 *          cross; no slide with reduced motion; in forced colors the tiles keep an edge and the current tab more
 *          than its fill
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
        $this->assertCount( 19, $s );

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

        // the focus trap: the header (its row, the button, the panel), round from its last control to its first
        $this->assertTrue( $s['tab from the last control goes to the first']['prevented'] );
        $this->assertSame( 'logo', $s['tab from the last control goes to the first']['focus'] );
        $this->assertTrue( $s['shift tab from the first control goes to the last']['prevented'] );
        $this->assertSame( 'site link', $s['shift tab from the first control goes to the last']['focus'] );
        foreach ( array( 'tab inside the panel is left to the browser', 'tab from the close button to the row is left to the browser',
                         'tab from the button into the panel is left to the browser', 'tab while closed is left alone',
                         'tab on a wide window is left alone' ) as $name )
        {
            $this->assertFalse( $s[$name]['prevented'], $name );
            $this->assertSame( array(), $s[$name]['log'], $name );
        }
        // the close button at the panel's end
        $this->assertFalse( $s['the close button closes and focuses the button']['open'] );
        $this->assertSame( 'false', $s['the close button closes and focuses the button']['expanded'] );
        $this->assertSame( 'icon', $s['the close button closes and focuses the button']['focus'] );
        // a tap outside the header closes, without moving the focus; one in the menu does not (pointerdown: iOS sends
        // no click to the document for a tap on the page's background)
        $this->assertStringContainsString( 'document.addEventListener("pointerdown", (e) => {', (string)file_get_contents( self::root() . '/design/admin4/javascript/ezadmin_menubar.js' ) );
        $this->assertFalse( $s['a tap outside the header closes']['open'] );
        $this->assertFalse( $s['a tap outside the header closes']['bodyOpen'] );
        $this->assertSame( array(), $s['a tap outside the header closes']['log'] );
        $this->assertTrue( $s['a tap in the menu keeps it']['open'] );
        // turned to landscape: the menu is the header's row again, and the page scrolls again
        $this->assertFalse( $s['a wider window closes the menu']['open'] );
        $this->assertFalse( $s['a wider window closes the menu']['bodyOpen'] );
        $this->assertSame( 'false', $s['a wider window closes the menu']['expanded'] );
        // the window's resize as well, for a browser that reports no change of the query (WebKit emulating a phone)
        $this->assertFalse( $s['a resize to a wider window without the query event closes the menu']['open'] );
        $this->assertFalse( $s['a resize to a wider window without the query event closes the menu']['bodyOpen'] );
        $this->assertTrue( $s['a resize on a phone keeps the menu']['open'] );
    }

    private static function phone()
    {
        return AdminBaseStylesheetsTest::rules( self::media( self::css( 'admin4.css' ), 'screen and (max-width: 575.98px)' ) );
    }

    private static function template( $path )
    {
        return (string)file_get_contents( self::root() . '/design/' . $path );
    }

    /** PM-02 */
    public function testTheOpenPhoneMenuIsAGridOfTiles()
    {
        $phone = self::phone();
        $this->assertSame( 'grid', $phone['.navbar-bar']['display'] );
        $this->assertSame( 'repeat(auto-fill, minmax(min(100%, 6.5rem), 1fr))', $phone['.navbar-bar']['grid-template-columns'],
                           'three tiles to a row at 390px, two at 320px, one at 200% text' );
        $this->assertSame( 'var(--a4-space-2)', $phone['.navbar-bar']['gap'] );
        $this->assertSame( 'visible', $phone['.navbar-bar']['overflow'], 'one scroller: the panel (admin3 let the list scroll as well)' );
        $this->assertSame( 'var(--a4-space-4) max(var(--a4-space-5), env(safe-area-inset-right)) calc(var(--a4-space-5) + env(safe-area-inset-bottom)) max(var(--a4-space-5), env(safe-area-inset-left))',
                           preg_replace( '/\s+/', ' ', $phone['.navbar-bar']['padding'] ) );
        $this->assertSame( '1 / -1', $phone['.navbar-bar > :is(.header-search-mobile, .a4-phone-only)']['grid-column'] );
        $tile = $phone['.navbar-menu .navbar-bar > li > a, .navbar-menu .navbar-bar > li > a.selected, .navbar-menu .navbar-bar > li > a.selected:hover'];
        $this->assertSame( 'normal', $tile['white-space'], 'a long name wraps inside its tile' );
        $this->assertSame( 'center', $tile['text-align'] );
        $this->assertSame( 'var(--a4-radius-s)', $tile['border-radius'] );
        $this->assertSame( 'var(--a4-soft)', $phone['.navbar-menu .navbar-bar > li > a:not(.selected)']['background-color'] );
        // the current tab keeps the accent fill of the base rule and gets an edge of its colour
        $this->assertSame( 'var(--a4-accent-strong)', $phone['.navbar-menu .navbar-bar > li > a.selected']['border-color'] );
        $this->assertArrayNotHasKey( 'background-color', $tile, 'the tile rule leaves the current tab its fill' );
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

    /** PM-04 */
    public function testTheOpenPhoneMenuFitsTheVisibleWindow()
    {
        $phone = self::phone();
        $this->assertSame( '100% 0 auto', $phone['.navbar-menu']['inset'], 'under the header\'s row, not behind it' );
        $this->assertSame( '0', $phone['.navbar-menu.show']['padding'], 'no padding-top of the header\'s height inside the panel' );
        $this->assertSame( 'calc(100dvh - var(--header-height))', $phone['.navbar-menu.show']['max-height'] );
        // the fallback first, the dynamic viewport height after it: the later one wins where it is known
        $css = self::css( 'admin4.css' );
        $this->assertStringContainsString( "max-height: calc(100vh - var(--header-height));\n    max-height: calc(100dvh - var(--header-height));", $css );
        $this->assertSame( 'auto', $phone['.navbar-menu.show']['overflow-y'] );
        $this->assertSame( 'hidden', $phone['.navbar-menu.show']['overflow-x'] );
        $this->assertSame( 'contain', $phone['.navbar-menu.show']['overscroll-behavior'], 'the panel\'s scrolling does not move the page' );
        $this->assertSame( 'hidden', $phone['body.navbar-open']['overflow'] );
        $this->assertSame( 'none', $phone['body.navbar-open']['overscroll-behavior'] );
        // no phone rule of the panel keeps 100vh, the height that let its end fall under iOS's bottom toolbar
        $this->assertDoesNotMatchRegularExpression( '/max-height:\s*100vh/', self::media( $css, 'screen and (max-width: 575.98px)' ) );
        $this->assertStringContainsString( 'env(safe-area-inset-bottom)', $phone['.navbar-bar']['padding'] );
    }

    /** PM-05 */
    public function testEveryTargetInThePhoneMenuIsAtLeast44px()
    {
        $phone = self::phone();
        $this->assertSame( '44px', $phone['.navbar-menu .navbar-bar > li > a, .navbar-menu .navbar-bar > li > a.selected, .navbar-menu .navbar-bar > li > a.selected:hover']['min-height'] );
        $this->assertSame( 'grid', $phone['.a4-phone-user-links']['display'], 'the user\'s actions side by side' );
        $action = $phone['.navbar-menu .a4-phone-user-links a, .a4-phone-user-links .disabled, .a4-phone-close-button'];
        $this->assertSame( '44px', $action['min-height'] );
        $this->assertSame( '100%', $phone['.a4-phone-close-button']['width'], 'the close button spans the panel' );
        $this->assertSame( '44px', $phone['.navbar-menu .a4-phone-user-name > a']['min-height'] );
        $this->assertSame( '44px', $phone['.header-search-mobile .searchblock input.searchtext']['min-height'] );
        $this->assertSame( '44px', $phone['#header .header-search-mobile .searchblock .searchbuttonfield']['width'] );
        $this->assertSame( '44px', $phone['#header .header-search-mobile .searchblock .searchbuttonfield']['height'] );
        // the scope button inside the 44px field: 40px, with the field's text starting after it
        $this->assertSame( '40px', $phone['#header #searchscope-mobile']['width'] );
        $this->assertSame( '40px', $phone['#header #searchscope-mobile']['height'] );
        $this->assertSame( 'calc(var(--a4-space-7) - var(--a4-space-1))', $phone['.header-search-mobile .searchblock input.searchtext']['padding-left'] );
    }

    /** PM-06 */
    public function testThePhoneMenuMarkup()
    {
        $top = self::template( 'admin4/templates/page_topmenu.tpl' );
        $order = array( '<li class="header-search-mobile">', "{include uri='design:parts/a4_topmenu_items.tpl'", '<li class="a4-nav-more" hidden>',
                        "{include uri='design:parts/a4_phone_user.tpl'}", '<li class="a4-phone-only a4-phone-close">', '<!-- end::Navbar Menu -->' );
        $at = -1;
        foreach ( $order as $needle )
        {
            $next = strpos( $top, $needle );
            $this->assertNotFalse( $next, $needle );
            $this->assertGreaterThan( $at, $next, $needle . ' in its place' );
            $at = $next;
        }
        $this->assertStringContainsString( '<button type="button" class="a4-phone-close-button" aria-controls="a4-navbar-menu">', $top );
        $this->assertStringContainsString( "{'Close'|i18n( 'design/admin/pagelayout' )|wash}</button>", $top );

        // drawn below 576px only, and never a tab of the one-line menu
        $base = AdminBaseStylesheetsTest::rules( preg_replace( '/@media[^{]*\{((?:[^{}]*\{[^{}]*\})*)[^{}]*\}/s', '', self::css( 'admin4.css' ) ) );
        $this->assertSame( 'none', $base['.a4-phone-only']['display'] );
        $this->assertSame( 'block', self::phone()['.navbar-bar > :is(.header-search-mobile, .a4-phone-only)']['display'] );
        $this->assertStringContainsString( "return li !== more && !li.classList.contains('header-search-mobile') && !li.classList.contains('a4-phone-only');",
                                           (string)file_get_contents( self::root() . '/design/admin4/javascript/admin4.js' ) );
        $this->assertStringContainsString( '$(".a4-phone-close-button").on(\'click\'', (string)file_get_contents( self::root() . '/design/admin4/javascript/ezadmin_menubar.js' ) );

        // the user's actions: the header user menu's links under the same conditions, and logout
        $user = self::template( 'admin4/templates/parts/a4_phone_user.tpl' );
        $header = self::template( 'admin4/templates/parts/a4_header_user.tpl' );
        $this->assertStringContainsString( '<li class="a4-phone-only a4-phone-user">', $user );
        foreach ( array( '{if $current_user.is_logged_in}', '{if $current_user.contentobject.can_edit}', "{if fetch( 'user', 'can_open', hash( 'uri', 'user/password' ) )}",
                         "{concat( '/content/edit/', \$current_user.contentobject_id, '/' )|ezurl}", "{'/user/password/'|ezurl}", "{'/mailpreferences/settings'|ezurl}",
                         "'Change information'", "'Change password'", "'My e-mail preferences'", "'Change user info'", "'Open your user object'" ) as $needle )
        {
            $this->assertStringContainsString( $needle, $user, $needle );
            $this->assertStringContainsString( $needle, $header, 'the header\'s user menu has it too: ' . $needle );
        }
        $this->assertStringContainsString( "<li><a href={'/user/logout'|ezurl} class=\"a4-phone-logout\">", $user );
        $this->assertStringContainsString( "or( \$ui_context_edit, eq( \$ui_context, 'browse' ) )", $user, 'leads nowhere on edit and browse pages, as the header' );
        $this->assertStringContainsString( '<ul class="a4-phone-user-links" aria-labelledby="a4-phone-user-name">', $user );
        // it stands for the header's user menu and logout on a phone; admin4l's header, without it, keeps them
        $this->assertSame( 'none', self::phone()['.navbar-main:has(.a4-phone-user) > .header-user']['display'] );
        $tabMenu = self::template( 'admin4l/templates/explayouts/block/admin_tab_menu.tpl' );
        $this->assertStringNotContainsString( 'a4_phone_user', $tabMenu );
        // admin4l's tab menu block has the same order and the same close button at the panel's end
        $at = -1;
        foreach ( array( '<li class="header-search-mobile">', "{include uri='design:parts/a4_topmenu_items.tpl'", '<li class="a4-nav-more" hidden>',
                         '<li class="a4-phone-only a4-phone-close">', '<!-- end::Navbar Menu -->' ) as $needle )
        {
            $next = strpos( $tabMenu, $needle );
            $this->assertNotFalse( $next, 'admin4l: ' . $needle );
            $this->assertGreaterThan( $at, $next, 'admin4l: ' . $needle . ' in its place' );
            $at = $next;
        }
        $this->assertStringContainsString( '<button type="button" class="a4-phone-close-button" aria-controls="a4-navbar-menu">', $tabMenu );
    }

    /** PM-07 */
    public function testTheClosedPanelIsOutOfReachAndTheButtonShowsItsState()
    {
        $closed = AdminBaseStylesheetsTest::rules( self::media( self::css( 'admin4.css' ), 'screen and (max-width: 575.98px)' ) );
        $this->assertSame( 'hidden', $closed['.navbar-menu:not(.show)']['visibility'] );
        $this->assertSame( 'max-height 300ms ease, visibility 0s linear 300ms', $closed['.navbar-menu:not(.show)']['transition'], 'hidden after its slide' );
        $this->assertSame( 'visible', $closed['.navbar-menu.show']['visibility'] );
        $this->assertSame( '"\\00D7"', $closed['#header .navbar-icon[aria-expanded="true"]::before']['content'] );
        $this->assertSame( 'none', $closed['#header .navbar-icon[aria-expanded="true"] > span']['display'] );

        $css = self::css( 'admin4.css' );
        $motion = AdminBaseStylesheetsTest::rules( self::media( $css, 'screen and (max-width: 575.98px) and (prefers-reduced-motion: reduce)' ) );
        $this->assertSame( 'none', $motion['.navbar-menu, .navbar-menu:not(.show), .navbar-menu.show']['transition'] );
        $forced = self::media( $css, 'screen and (max-width: 575.98px) and (forced-colors: active)' );
        $this->assertStringContainsString( '.navbar-menu .navbar-bar > li > a.selected { border: 3px solid Highlight; text-decoration: underline; }', $forced );
        $this->assertStringContainsString( 'border-color: ButtonText;', $forced );
    }
}
