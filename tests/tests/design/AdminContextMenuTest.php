<?php
/**
 * The context menus of the admin designs (ezpopupmenu.js: content tree, sub-items, class and bookmark menus and
 * their submenus) are drawn from the admin tokens in admin4 and work from the keyboard. admin4's menu had a fixed
 * 14em width with 11.5px text in 18px rows, a hard grey drop shadow that glowed light in dark mode, a fixed grey
 * title bar (4.39:1) and a blue hover found nowhere else; no menu had a role or could be reached without a mouse.
 *
 * The roles and keys come from ezpopupmenu.js when a menu is shown, so menus written by extensions get them too;
 * these tests hold the pieces that make that work.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminContextMenuTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminContextMenuTest extends PHPUnit\Framework\TestCase
{
    /** The designs whose copies of the menu script and its openers are kept identical */
    const SCRIPT_COPIES = array(
        'javascript/popupmenu/ezpopupmenu.js',
        'javascript/contentstructuremenu_dynamic.js',
        'javascript/ezajaxsubitems_expdatatable.js',
        'templates/contentstructuremenu/show_content_structure.tpl',
    );

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    /** The POPUP MENU block of admin4's pagelayout.css, comments removed */
    private static function menuCss()
    {
        $css = (string)file_get_contents( self::root() . '/design/admin4/stylesheets/pagelayout.css' );
        $start = strpos( $css, '/* POPUP MENU' );
        $end = strpos( $css, '/* BOX MODEL */' );
        self::assertNotFalse( $start, 'pagelayout.css has no POPUP MENU block' );
        self::assertNotFalse( $end, 'pagelayout.css has no BOX MODEL block after the menu' );
        return preg_replace( '#/\*.*?\*/#s', '', substr( $css, $start, $end - $start ) );
    }

    public function testAdmin4MenuTakesItsColoursFromTheTokens()
    {
        $css = self::menuCss();
        // forced-colors keywords (CanvasText, Highlight, GrayText) are system colours, not literals
        $this->assertDoesNotMatchRegularExpression( '/#[0-9a-f]{3,8}\b|rgba?\(|hsla?\(/i', $css,
            'The admin4 context menu names a colour; use the --a4-* tokens (doc/bc/6.0/admin4-styling.md)' );
        foreach ( array( '--a4-card', '--a4-line', '--a4-radius-s', '--a4-shadow-m', '--a4-text-m', '--a4-accent-soft', '--a4-muted' ) as $token )
            $this->assertStringContainsString( "var($token)", $css, "The admin4 context menu does not use $token" );
    }

    /** The pixel value of an admin4 --a4-space-N token, from components.css */
    private static function spaceTokenPx( $token )
    {
        $tokens = (string)file_get_contents( self::root() . '/design/admin4/stylesheets/components.css' );
        self::assertSame( 1, preg_match( '/' . preg_quote( $token, '/' ) . ':\s*(\d+)px;/', $tokens, $match ),
                          "components.css does not define $token in px" );
        return (int)$match[1];
    }

    public function testAdmin4MenuRowsAreCompactButAtLeast24PxAndKeepTheirFocusRing()
    {
        $css = self::menuCss();
        // compact rows: 14px text, line-height 1.35 and 4px above and below (about 27px), but never below the
        // 24px target size of WCAG 2.2 (2.5.8)
        $this->assertSame( 1, preg_match( '/div\.popupmenu a\s*\{[^}]*min-height:\s*var\((--a4-space-\d)\)/', $css, $match ),
            'Menu rows need a min-height from the --a4-space-* tokens' );
        $this->assertGreaterThanOrEqual( 24, self::spaceTokenPx( $match[1] ),
            'Menu rows must be at least 24px high (WCAG 2.2 target size)' );
        $this->assertMatchesRegularExpression( '/div\.popupmenu a\s*\{[^}]*padding:\s*var\(--a4-space-1\) var\(--a4-space-3\);[^}]*line-height:\s*1\.35;/', $css,
            'Menu rows are compact: 4px above and below the text at line-height 1.35' );
        $this->assertMatchesRegularExpression( '/div\.popupmenu h3\s*\{[^}]*padding:\s*var\(--a4-space-1\) var\(--a4-space-3\);/', $css,
            'The title row is as compact as the items' );
        $this->assertMatchesRegularExpression( '/div\.popupmenu hr\s*\{[^}]*margin:\s*calc\(var\(--a4-space-1\) \/ 2\) 0;/', $css,
            'Separators keep 2px above and below' );
        $this->assertMatchesRegularExpression( '/div\.popupmenu a:focus-visible\s*\{[^}]*outline:\s*2px solid/', $css,
            'The focused menu row needs a visible outline' );
        $this->assertStringContainsString( '@media (forced-colors: active)', $css, 'The menu needs a forced-colors fallback' );
        $this->assertMatchesRegularExpression( '/div\.popupmenu a\s*\{[^}]*white-space:\s*nowrap;[^}]*overflow:\s*hidden;[^}]*text-overflow:\s*ellipsis/', $css,
            'Unwrapped menu items longer than the menu\'s max-width must end in an ellipsis, not spill past the card' );
        $this->assertDoesNotMatchRegularExpression( '/outline:\s*(none|0)\b/', $css, 'The menu must not remove the focus outline' );
    }

    public function testMenuScriptGivesRolesAndKeys()
    {
        $js = (string)file_get_contents( self::root() . '/design/admin4/javascript/popupmenu/ezpopupmenu.js' );
        foreach ( array( "'role', 'menu'", "'role', 'menuitem'", "'role', 'separator'", "'aria-haspopup'", "'aria-expanded'",
                         "'aria-disabled'", "'aria-labelledby'", "'tabindex', '-1'" ) as $needle )
            $this->assertStringContainsString( $needle, $js, "ezpopupmenu.js no longer sets $needle" );
        foreach ( array( 'ArrowDown', 'ArrowUp', 'ArrowRight', 'ArrowLeft', 'Home', 'End', 'Escape', 'Tab', "'F10'", "'ContextMenu'" ) as $key )
            $this->assertStringContainsString( $key, $js, "ezpopupmenu.js no longer handles $key" );
        // Shift+F10 in a form field of a row keeps the browser's own context menu
        $this->assertMatchesRegularExpression( '/INPUT\|TEXTAREA\|SELECT\)\$\/\.test\( active\.tagName \) \|\| active\.isContentEditable \) return;/', $js,
            'ezpopupmenu.js must leave Shift+F10 in form fields to the browser' );
        // ... and opens the row's OWN menu: a tree node's li also holds the li of its sub items
        $this->assertStringContainsString( "candidates[i].closest( 'li, tr' ) === row", $js,
            'ezpopupmenu.js must take the menu button of the row itself, not one of a sub item' );
        // the public interface extensions call stays as it was
        foreach ( array( 'ezpopmenu_showTopLevel', 'ezpopmenu_showSubLevel', 'ezpopmenu_mouseOver', 'ezpopmenu_hideAll',
                         'ezpopmenu_hide', 'ezpopmenu_submitForm', 'ez_createAArray', 'ezpopup_SubstituteAndRedirect' ) as $api )
            $this->assertMatchesRegularExpression( '/window\.' . preg_quote( $api, '/' ) . '\s*=/', $js, "ezpopupmenu.js no longer exports $api" );
    }

    /**
     * A click made with Enter, Space or element.click() (the Shift+F10 path) has clientX/clientY 0, but pageX/pageY
     * carry the scroll offset: on a scrolled page a test on pageX/pageY took it for a mouse click and opened the menu
     * in the window's top left corner, without moving the focus into it.
     */
    public function testAKeyboardClickIsToldByItsClickCountNotByItsPositionOnThePage()
    {
        $js = (string)file_get_contents( self::root() . '/design/admin4/javascript/popupmenu/ezpopupmenu.js' );
        $this->assertSame( 1, preg_match( '/function _isKeyboardEvent\( event \)\s*\{(.*?)\n\}/s', $js, $match ),
                           'ezpopupmenu.js no longer has _isKeyboardEvent()' );
        $body = (string)preg_replace( '/\/\/[^\n]*/', '', $match[1] );
        $this->assertDoesNotMatchRegularExpression( '/\bpage[XY]\b/', $body,
                                                    'pageX/pageY include the scroll offset and are not 0 for a keyboard click on a scrolled page' );
        $this->assertStringContainsString( "event.type === 'click'", $body );
        $this->assertStringContainsString( 'event.detail === 0', $body, 'A click made with the keyboard counts no clicks' );
    }

    public function testContentTreeMenuButtonsHaveANameAndAnnounceTheirMenu()
    {
        foreach ( array( 'javascript/contentstructuremenu_dynamic.js', 'templates/contentstructuremenu/show_content_structure.tpl' ) as $file )
        {
            $text = (string)file_get_contents( self::root() . "/design/admin4/$file" );
            $this->assertMatchesRegularExpression( '/class="nodeicon" href="#" role="button" aria-haspopup="menu" aria-expanded="false" aria-label="/', $text,
                "The tree's menu button in $file needs role, aria-haspopup, aria-expanded and a name" );
        }
    }

    /**
     * The sub-items menu opener is the same in both designs: the native event goes to ezpopupmenu.js, which places
     * a menu opened from the keyboard itself.
     */
    public function testSubitemsMenuOpenerIsTheSameInBothDesigns()
    {
        foreach ( array( "admin4", "admin" ) as $design )
        {
            $text = (string)file_get_contents( self::root() . "/design/$design/javascript/ezajaxsubitems_expdatatable.js" );
            $this->assertStringContainsString( "var ev = e.originalEvent || e;", $text, "design/$design: the sub-items menu button passes the native event" );
            $this->assertStringNotContainsString( "getBoundingClientRect", $text, "design/$design: the sub-items menu button places the menu itself again" );
        }
    }

    public function testAdminCopiesOfTheMenuScriptStayIdentical()
    {
        foreach ( self::SCRIPT_COPIES as $file )
        {
            $copy = self::root() . "/design/admin/$file";
            if ( !is_file( $copy ) )
                continue;
            $this->assertFileEquals( self::root() . "/design/admin4/$file", $copy,
                "design/admin/$file and design/admin4/$file differ: change both copies together" );
        }
    }
}
