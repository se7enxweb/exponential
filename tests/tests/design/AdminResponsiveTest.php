<?php
/**
 * admin4 across window widths (phone 390px, tablet 820px, small desktop 1180px, wide 2560px), checked on the files
 * alone (no database):
 *
 * - the phone menu is an opaque panel that scrolls on its own, and the side columns' tabs hide while it is open;
 * - from 576px to 1279px the top menu stays on one line and scrolls sideways;
 * - below 1280px only the left column takes room beside the content, the right one opens over it;
 * - the content column stops growing at --a4-content-max;
 * - the node's tabs on a phone: the tab row on its own, scrolling, the tabs switch under it; a wide table in a tab
 *   scrolls inside it;
 * - a sub-item's class icon and name stay together; the sub-items toolbar wraps with a gap;
 * - tree labels break between words, with hyphens; a search result name breaks on a phone;
 * - a fact's note stands under its value; the header search has no square frame; touch targets of 24px.
 *
 * doc/bc/6.0/admin4-styling.md
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminResponsiveTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminBaseStylesheetsTest.php';

class AdminResponsiveTest extends PHPUnit\Framework\TestCase
{
    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function file( $path )
    {
        return (string)file_get_contents( self::root() . '/' . $path );
    }

    private static function css( $file )
    {
        return preg_replace( '#/\*.*?\*/#s', '', self::file( 'design/admin4/stylesheets/' . $file ) );
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

    public function testThePhoneMenuIsOpaqueAndHidesTheSidebarTabs()
    {
        $phone = AdminBaseStylesheetsTest::rules( self::media( self::css( 'admin4.css' ), 'screen and (max-width: 575.98px)' ) );
        $this->assertSame( 'var(--a4-card)', $phone['.navbar-menu.show']['background-color'], 'the page shone through the translucent header colour' );
        $this->assertSame( 'auto', $phone['.navbar-menu.show']['overflow-y'] );
        $this->assertSame( 'hidden', $phone['body.navbar-open .sidebar-controls, body:has(.navbar-menu.show) .sidebar-controls']['visibility'] );
    }

    public function testTheTopMenuStaysOnOneLineOnTabletsAndSmallDesktops()
    {
        $tablet = AdminBaseStylesheetsTest::rules( self::media( self::css( 'admin4.css' ), 'screen and (min-width: 576px) and (max-width: 1279.98px)' ) );
        $this->assertSame( 'nowrap', $tablet['.navbar-bar']['flex-wrap'] );
        $this->assertSame( 'auto', $tablet['.navbar-bar']['overflow-x'] );
        // a scroll container clips vertically too: room for the 3px ring with its 2px offset, inset at the row's ends
        $this->assertSame( 'var(--a4-space-2)', $tablet['.navbar-bar']['padding-block'] );
        $this->assertSame( '-3px', $tablet['.navbar-bar a:focus-visible']['outline-offset'] );
        $this->assertSame( '0', $tablet['.navbar-menu']['min-width'], 'the panel stays as wide as the window, so the row scrolls' );
    }

    public function testBelow1280OnlyTheLeftColumnTakesRoom()
    {
        $small = AdminBaseStylesheetsTest::rules( self::media( self::css( 'admin4.css' ), 'screen and (min-width: 1024px) and (max-width: 1279.98px)' ) );
        // html body:has(...) outweighs pagelayout.css's body:has(.sidebar.right.show) margin
        $this->assertSame( '0', $small['html body:has(.sidebar.right.show)']['margin-right'] );
        $this->assertStringContainsString( "body:has(.sidebar.right.show) {\n        margin-right: calc(15rem + 0.5rem);", self::css( 'pagelayout.css' ) );
    }

    public function testTheContentColumnStopsGrowing()
    {
        $rules = AdminBaseStylesheetsTest::rules( self::css( 'admin4.css' ) );
        $this->assertSame( 'var(--a4-content-max)', $rules['#maincolumn > #maincontent']['max-width'] );
        $this->assertSame( 'auto', $rules['#maincolumn > #maincontent']['margin-inline'] );
        $this->assertMatchesRegularExpression( '/--a4-content-max: \d+px;/', self::css( 'components.css' ) );
    }

    public function testTheNodeTabsFitAPhone()
    {
        $css = self::css( 'admin4.css' );
        $rules = AdminBaseStylesheetsTest::rules( $css );
        $this->assertSame( '0', $rules['#window-controls.a4-node-tabs > *']['min-width'], 'a wide table must not widen the box' );
        $this->assertSame( 'auto', $rules['#window-controls.a4-node-tabs > .tabs-content > .tab-content']['overflow-x'] );
        $phone = AdminBaseStylesheetsTest::rules( self::media( $css, 'screen and (max-width: 575.98px)' ) );
        $this->assertSame( '1 / -1', $phone['#window-controls.a4-node-tabs > ul.tabs']['grid-column'], 'the tabs on their own row' );
        $this->assertSame( 'nowrap', $phone['#window-controls.a4-node-tabs > ul.tabs']['flex-wrap'] );
        $this->assertSame( 'auto', $phone['#window-controls.a4-node-tabs > ul.tabs']['overflow-x'] );
        $this->assertSame( '2', $phone['#window-controls.a4-node-tabs > :is(.button-left, .a4-tabs-switch-label)']['grid-row'], 'the switch under them' );
    }

    public function testASubItemsIconAndNameStayTogether()
    {
        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            $script = self::file( "design/$design/javascript/ezajaxsubitems_expdatatable.js" );
            $this->assertStringContainsString( '<span class="a4-subitem-name"><a href="', $script, $design );
            $this->assertStringNotContainsString( "'</a>' + '&nbsp;' +", $script, "$design: a space between icon and name let the name wrap away from it" );
        }
        $rules = AdminBaseStylesheetsTest::rules( self::css( 'admin4.css' ) );
        $this->assertSame( 'inline-flex', $rules['.a4-subitem-name']['display'] );
        $this->assertSame( 'none', $rules['.a4-subitem-name > a:first-child']['flex'] );
        $this->assertSame( 'var(--a4-space-2)', $rules['html #action-controls']['gap'], 'the toolbar wraps with a gap, not with margins' );
    }

    public function testLongWordsBreakBetweenSyllables()
    {
        $pagelayout = AdminBaseStylesheetsTest::rules( self::css( 'pagelayout.css' ) );
        $tree = $pagelayout['div#contentstructure ul#content_tree_menu li'];
        $this->assertSame( 'break-word', $tree['overflow-wrap'], '"anywhere" broke words mid-syllable' );
        $this->assertSame( 'auto', $tree['hyphens'] );
        $this->assertStringContainsString( '<table class="list a4-search-results"', self::file( 'design/admin4/templates/content/searchresult.tpl' ) );
        $phone = AdminBaseStylesheetsTest::rules( self::media( self::css( 'admin4.css' ), 'screen and (max-width: 575.98px)' ) );
        $this->assertSame( 'anywhere', $phone['table.a4-search-results td a']['overflow-wrap'] );
    }

    public function testAFactsNoteStandsUnderItsValue()
    {
        $rules = AdminBaseStylesheetsTest::rules( self::css( 'components.css' ) );
        $this->assertSame( 'block', $rules['.a4-facts dd > .a4-meta']['display'] );
    }

    public function testTheHeaderSearchHasNoSquareFrame()
    {
        $rules = AdminBaseStylesheetsTest::rules( self::css( 'admin4.css' ) );
        $this->assertSame( '0', $rules['#header .searchtextwrapper']['border'] );
        $this->assertSame( '999px', $rules['#header .searchblock .searchbuttonfield']['border-radius'] );
    }

    public function testTouchScreensGetTwentyFourPixelTargets()
    {
        $coarse = self::media( self::css( 'admin4.css' ), '(pointer: coarse)' );
        foreach ( array( 'a.nodeicon', '#searchscope-mobile', 'button.a4-copy', 'input:is([type=checkbox], [type=radio])' ) as $target )
        {
            $this->assertStringContainsString( $target, $coarse );
        }
        $this->assertStringContainsString( 'min-height: var(--a4-space-5)', $coarse );
    }

    public function testTheLeftDrawerKeepsItsTabAtItsEdge()
    {
        $drawer = AdminBaseStylesheetsTest::rules( self::media( self::css( 'admin4.css' ), 'screen and (max-width: 1023.98px)' ) );
        $this->assertStringStartsWith( 'min(var(--left-sidebar-width', $drawer['body:has(#leftmenu.sidebar.show) .sidebar-control.left']['translate'] );
    }

    public function testSearchResultsShowTypeAndPathOnAPhone()
    {
        $template = self::file( 'design/admin4/templates/content/searchresult.tpl' );
        $this->assertStringContainsString( '<span class="a4-search-path">/{$SearchResult.item.url_alias|wash}</span>', $template, 'the path under the name' );
        // drawn as a grid on a phone: the roles keep it a table for assistive technology
        $this->assertStringContainsString( 'role="table"', $template );
        $this->assertSame( 2, substr_count( $template, 'role="row"' ) );
        $this->assertSame( 3, substr_count( $template, 'role="columnheader"' ) );
        $this->assertSame( 3, substr_count( $template, 'role="cell"' ) );
        $phone = AdminBaseStylesheetsTest::rules( self::media( self::css( 'admin4.css' ), 'screen and (max-width: 575.98px)' ) );
        $this->assertSame( 'grid', $phone['table.a4-search-results tr']['display'], 'the type stacks under the name instead of leaving the screen' );
        $this->assertSame( '2', $phone['table.a4-search-results tr > :nth-child(3)']['grid-column'] );
        $rules = AdminBaseStylesheetsTest::rules( self::css( 'admin4.css' ) );
        $this->assertSame( 'anywhere', $rules['.a4-search-path']['overflow-wrap'] );
    }

    public function testTreeArrowsAndTheLanguageChoiceAreLargeEnoughToTouch()
    {
        $coarse = AdminBaseStylesheetsTest::rules( self::media( self::css( 'admin4.css' ), '(pointer: coarse)' ) );
        $this->assertSame( '-4px 0 -4px -8px', $coarse['div#contentstructure ul#content_tree_menu ul a.openclose-open::after, div#contentstructure ul#content_tree_menu ul a.openclose-close::after']['inset'], '16px arrow + 8px = 24px' );
        // the node's language choice is as high as the buttons beside it at every width (32px, 28px compact)
        $rules = AdminBaseStylesheetsTest::rules( self::css( 'admin4.css' ) );
        $this->assertSame( 'var(--a4-control-h)', $rules['.controlbar.a4-node-actions form .button-left .block select']['height'] );
    }

    public function testTheBodyTextGrowsModestlyOnVeryWideWindows()
    {
        $components = self::css( 'components.css' );
        $this->assertStringContainsString( '--a4-text-body: clamp(var(--a4-text-s), calc(var(--a4-text-s) - 3px + 0.15625vw), var(--a4-text-m));', $components,
            '13px up to 1920px, 14px at 2560px: never below the scale\'s 13px body, never above its next step' );
        $rules = AdminBaseStylesheetsTest::rules( self::css( 'admin4.css' ) );
        $this->assertSame( 'var(--a4-text-body)', $rules['body']['font-size'] );
    }

    public function testTheFrameHasOneGutter()
    {
        $components = self::css( 'components.css' );
        $this->assertStringContainsString( '--a4-gutter: var(--a4-space-5);', $components, '24px, the gap of a 24px layout grid' );
        $this->assertStringContainsString( "--a4-card-pad-x: var(--a4-space-3);\n  --a4-gutter: var(--a4-space-4);", $components, '16px in compact density' );
        $css = self::css( 'admin4.css' );
        $rules = AdminBaseStylesheetsTest::rules( $css );
        // the cards: the gutter towards the content, under the header and below; 24px at the window's edge for the tab
        $this->assertSame( 'var(--a4-gutter) var(--a4-gutter) var(--a4-gutter) var(--a4-space-5)', $rules['.sidebar > div, .sidebar.left > div']['margin'] );
        $this->assertSame( 'var(--a4-gutter) var(--a4-space-5) var(--a4-gutter) var(--a4-gutter)', $rules['.sidebar.right > div']['margin'] );
        $this->assertStringContainsString( ".content-wrapper {\n  padding: var(--a4-gutter) var(--a4-space-5);", $css, 'the gutter under the header' );
        // a column beside the content ends where the content begins (no 8px of the body, no 24px of the content)
        $wide = AdminBaseStylesheetsTest::rules( self::media( $css, 'screen and (min-width: 1024px)' ) );
        $this->assertSame( 'var(--left-sidebar-width, 15rem)', $wide['html body:has(.sidebar.left.show)']['margin-left'] );
        $this->assertSame( '0', $wide['body:has(.sidebar.left.show) .content-wrapper']['padding-left'] );
        $wider = AdminBaseStylesheetsTest::rules( self::media( $css, 'screen and (min-width: 1280px)' ) );
        $this->assertSame( '15rem', $wider['html body:has(.sidebar.right.show)']['margin-right'] );
        $this->assertSame( '0', $wider['body:has(.sidebar.right.show) .content-wrapper']['padding-right'] );
        // the width grip stands in the gutter, not over the content
        $this->assertSame( 'calc((var(--a4-gutter) - var(--a4-space-3)) / 2)', $rules['.leftmenu-resize-handle']['right'] );
        $this->assertSame( 'var(--a4-space-3)', $rules['.leftmenu-resize-handle']['width'] );
    }
}
