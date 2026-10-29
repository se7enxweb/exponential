<?php
/**
 * admin4's side columns on the first paint, checked on the files alone (no database). ezadmin_menubar.js opens a
 * column (.show) once the document is parsed; a page drawn before that showed the content across the whole window
 * and then moved it aside for the columns (a cumulative layout shift of 0.87 on the dashboard at 1440px).
 *
 *  FP-01 - both page layouts (admin4, admin4l) write html.a4-preload, on <html> beside the user's preferences
 *          (parts/a4_preference_attributes.tpl), outside every cache-block
 *  FP-02 - until the script has applied its state, admin4.css draws the columns from those preferences with the
 *          script's rules: beside the content open unless saved closed (the left from 1024px, the right from 1280px),
 *          with the same room as an open column (.show) takes; a right drawer saved open is open on a tablet-sized
 *          window (576 to 1279px) and from 1024px the left column steps aside for it; phones start closed
 *  FP-03 - no transition runs while html.a4-preload is there
 *  FP-04 - the script takes the class away two frames after sidebarToggle() has applied the state (endPreload() run
 *          in node, tests/tests/design/js/first_paint.js; skipped without node), at once without
 *          requestAnimationFrame
 *  FP-05 - which columns the page has comes from the server, <body data-a4-columns> (left: the frame's column or the
 *          page's own, right), not only from :has(), which a paint of the half-parsed page does not match yet
 *  FP-06 - html.a4-js (a4_head.js, before the first paint) draws the right column's disclosures closed from the start
 *  FP-07 - the content tree scrolls in a card that fills the column: Trash, the content finder and the width buttons
 *          stand at its foot and do not move while the tree is built
 *  FP-08 - those rules only reach the card that is the column's own child (#leftmenu-design > #content-tree): a menu
 *          that wraps the tree in a box of its own with lists after it (the newsletter menu) keeps the ordinary flow,
 *          where flex-basis 0 left the card one heading high and drew the tree over the lists
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminFirstPaintColumnsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminFirstPaintColumnsTest extends PHPUnit\Framework\TestCase
{
    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function read( $path )
    {
        return (string)file_get_contents( self::root() . '/' . $path );
    }

    /** the rules of one @media block of admin4.css, by its exact condition, all blocks with that condition together */
    private static function media( $css, $condition )
    {
        $out = '';
        $at = 0;
        $head = '@media ' . $condition . ' {';
        while ( ( $at = strpos( $css, $head, $at ) ) !== false )
        {
            $depth = 0;
            for ( $i = $at + strlen( $head ) - 1, $n = strlen( $css ); $i < $n; $i++ )
            {
                if ( $css[$i] === '{' ) { $depth++; }
                elseif ( $css[$i] === '}' && --$depth === 0 ) { break; }
            }
            $out .= substr( $css, $at + strlen( $head ), $i - $at - strlen( $head ) ) . "\n";
            $at = $i;
        }
        return $out;
    }

    /** the declarations of the rule with exactly this selector in $css, as property => value */
    private static function declarations( $css, $selector )
    {
        $quoted = preg_quote( $selector, '/' );
        if ( !preg_match( '/(?:^|[}\s])' . $quoted . '\s*\{([^}]*)\}/', $css, $m ) )
        {
            return null;
        }
        $out = array();
        foreach ( explode( ';', $m[1] ) as $d )
        {
            if ( strpos( $d, ':' ) !== false )
            {
                list( $p, $v ) = explode( ':', $d, 2 );
                $out[trim( $p )] = trim( $v );
            }
        }
        return $out;
    }

    public function testBothPageLayoutsStartWithThePreloadClass()
    {
        foreach ( array( 'admin4', 'admin4l' ) as $design )
        {
            $layout = self::read( 'design/' . $design . '/templates/pagelayout.tpl' );
            $this->assertMatchesRegularExpression(
                "/<html class=\"a4-preload\" [^\\n]*\\{include uri='design:parts\\/a4_preference_attributes\\.tpl'\\}>/",
                $layout, $design . ': html.a4-preload beside the preferences' );
            $html = strpos( $layout, '<html ' );
            $cache = strpos( $layout, '{cache-block' );
            $this->assertNotFalse( $cache );
            $this->assertLessThan( $cache, $html, $design . ': outside every cache-block' );
        }
        // the preferences the stylesheet reads are the ones the attributes template writes
        $prefs = self::read( 'design/admin4/templates/parts/a4_preference_attributes.tpl' );
        foreach ( array( 'admin_left_sidebar', 'admin_right_sidebar', 'admin_right_drawer' ) as $name )
        {
            $this->assertStringContainsString( "'" . $name . "'", $prefs );
        }
    }

    public function testTheFirstPaintHasTheColumnsOfTheSavedChoice()
    {
        $css = self::read( 'design/admin4/stylesheets/admin4.css' );
        $left = 'html.a4-preload:not([data-a4-pref-admin-left-sidebar="0"])';
        $right = 'html.a4-preload:not([data-a4-pref-admin-right-sidebar="0"])';
        $drawer = 'html.a4-preload[data-a4-pref-admin-right-drawer="1"]';
        $shown = array( 'opacity' => '1', 'visibility' => 'visible' );
        // which columns the page has: the server's <body data-a4-columns>, :has() for a layout without it (FP-05)
        $hasLeft = 'body:is([data-a4-columns~="left"], :has(.sidebar.left))';
        $hasRight = 'body:is([data-a4-columns~="right"], :has(.sidebar.right))';

        // beside the content: shown, and the same room as an open column takes once the script has run
        $wide = self::media( $css, 'screen and (min-width: 1024px)' );
        $this->assertSame( $shown, self::declarations( $wide, $left . ' .sidebar.left' ) );
        $this->assertSame( self::declarations( $wide, 'html body:has(.sidebar.left.show)' ),
                           self::declarations( $wide, $left . ' ' . $hasLeft ), 'left: the room of .show' );
        $this->assertSame( self::declarations( $wide, 'body:has(.sidebar.left.show) .content-wrapper' ),
                           self::declarations( $wide, $left . ' ' . $hasLeft . ' .content-wrapper' ) );
        $wider = self::media( $css, 'screen and (min-width: 1280px)' );
        $this->assertSame( $shown, self::declarations( $wider, $right . ' .sidebar.right' ) );
        $this->assertSame( self::declarations( $wider, 'html body:has(.sidebar.right.show)' ),
                           self::declarations( $wider, $right . ' ' . $hasRight ), 'right: the room of .show' );
        $this->assertSame( self::declarations( $wider, 'body:has(.sidebar.right.show) .content-wrapper' ),
                           self::declarations( $wider, $right . ' ' . $hasRight . ' .content-wrapper' ) );
        $this->assertNotNull( self::declarations( $wide, 'html body:has(.sidebar.left.show)' ) );
        $this->assertNotNull( self::declarations( $wider, 'html body:has(.sidebar.right.show)' ) );

        // the right drawer saved open on a tablet-sized window; from 1024px the left column steps aside for it, as
        // sidebarToggle() does (drawerOpen "right" hides the left column)
        $tablet = self::media( $css, 'screen and (min-width: 576px) and (max-width: 1279.98px)' );
        $this->assertSame( $shown, self::declarations( $tablet, $drawer . ' .sidebar.right' ) );
        $mid = self::media( $css, 'screen and (min-width: 1024px) and (max-width: 1279.98px)' );
        $this->assertSame( array( 'opacity' => '0', 'visibility' => 'hidden' ), self::declarations( $mid, $drawer . ' .sidebar.left' ) );
        $this->assertSame( array( 'margin-inline-start' => '0' ), self::declarations( $mid, $drawer . ' ' . $hasLeft ) );
        // the drawer's rules come after the wide ones they override (same weight)
        $this->assertGreaterThan( strpos( $css, $left . ' ' . $hasLeft . ' {' ), strpos( $css, $drawer . ' ' . $hasLeft . ' {' ) );

        // nothing of this on a phone or below the left column's breakpoint: the drawers start closed
        $phone = self::media( $css, 'screen and (max-width: 575.98px)' ) . self::media( $css, 'screen and (max-width: 1023.98px)' );
        $this->assertStringNotContainsString( 'a4-preload', $phone );
        // and only before the script: every rule of this kind is bound to html.a4-preload
        preg_match_all( '/data-a4-pref-admin-(left|right)-(sidebar|drawer)/', $css, $all, PREG_OFFSET_CAPTURE );
        foreach ( $all[0] as $hit )
        {
            $line = substr( $css, strrpos( substr( $css, 0, $hit[1] ), "\n" ) + 1, 120 );
            $this->assertStringContainsString( 'html.a4-preload', $line );
        }
    }

    public function testNoTransitionRunsBeforeTheScriptHasRun()
    {
        $css = self::read( 'design/admin4/stylesheets/admin4.css' );
        $this->assertMatchesRegularExpression(
            '/html\.a4-preload \*,\s*html\.a4-preload \*::before,\s*html\.a4-preload \*::after \{ transition: none !important; \}/', $css );
        // reduced motion still ends every transition at once
        $this->assertMatchesRegularExpression( '/@media \(prefers-reduced-motion: reduce\)\s*\{\s*\*, \*::before, \*::after\s*\{[^}]*transition-duration/s', $css );
    }

    public function testTheScriptEndsThePreloadTwoFramesAfterItHasAppliedTheState()
    {
        $js = self::read( 'design/admin4/javascript/ezadmin_menubar.js' );
        $this->assertMatchesRegularExpression( '/function init\(\) \{\s*(?:\/\/[^\n]*\s*)?try \{\s*sidebarToggle\(\);\s*\} finally \{\s*endPreload\(\);\s*\}/', $js,
            'right after the columns have their state' );

        $node = trim( (string)shell_exec( 'command -v node 2>/dev/null' ) );
        if ( $node === '' )
        {
            $this->markTestSkipped( 'node is not installed' );
        }
        $out = shell_exec( escapeshellarg( $node ) . ' ' . escapeshellarg( __DIR__ . '/js/first_paint.js' ) . ' '
                           . escapeshellarg( self::root() . '/design/admin4/javascript/ezadmin_menubar.js' ) );
        $s = json_decode( (string)$out, true );
        $this->assertIsArray( $s, 'the harness ran: ' . $out );
        $this->assertArrayNotHasKey( 'missing', $s, 'endPreload() is in ezadmin_menubar.js' );
        // still there after the call and after the first frame, gone after the second; the page's other classes stay
        $this->assertSame( array( true, true, false ), $s['frames']['log'] );
        $this->assertTrue( $s['frames']['other'] );
        $this->assertSame( array( false ), $s['noFrames']['log'] );
    }

    public function testThePageNamesItsColumnsOnTheBody()
    {
        foreach ( array( 'admin4', 'admin4l' ) as $design )
        {
            $layout = self::read( 'design/' . $design . '/templates/pagelayout.tpl' );
            $this->assertStringContainsString( '<body data-a4-columns="{$a4_columns}">', $layout, $design );
            $this->assertMatchesRegularExpression( "/\\{def \\\$a4_columns = cond\\( or\\( \\\$hide_left_menu\\|not, \\\$module_result\\.content\\|contains\\( 'class=\"sidebar left\"' \\) \\), 'left right', 'right' \\)\\}/",
                                                   $layout, $design . ': the frame\'s left column or the page\'s own, the right always' );
            // defined before the cache-block that holds <body>, and a key of it
            $def = strpos( $layout, '{def $a4_columns' );
            $this->assertLessThan( strpos( $layout, '{cache-block' ), $def, $design );
            $this->assertMatchesRegularExpression( '/\{cache-block keys=array\([^\n]*\$main_wrapped, \$a4_columns \)/', $layout, $design );
        }
    }

    public function testTheRightColumnsDisclosuresAreClosedOnTheFirstPaint()
    {
        $head = self::read( 'design/admin4/javascript/a4_head.js' );
        $this->assertStringContainsString( "root.classList.add('a4-js');", $head );
        $css = self::read( 'design/admin4/stylesheets/admin4.css' );
        $this->assertSame( array( 'display' => 'flex' ),
                           self::declarations( $css, 'html.a4-js .a4-uiswitch-toggle[data-a4-disclosure][hidden]' ) );
        $this->assertSame( array( 'display' => 'none' ),
                           self::declarations( $css, 'html.a4-js .a4-uiswitch-toggle[data-a4-disclosure][aria-expanded="false"] + .a4-uiswitch-menu' ) );
        // the button stands right before its list in the template the rule is written for
        $this->assertMatchesRegularExpression( '/<button type="button" class="a4-uiswitch-toggle"[^\n]* data-a4-disclosure=""[^\n]* hidden>[^\n]*<\/button>\n<form class="a4-uiswitch-menu"/',
                                               self::read( 'design/admin4/templates/uiswitch/choices.tpl' ) );
    }

    public function testTheContentTreeScrollsAndThePartsAfterItStayAtTheCardsFoot()
    {
        $css = self::read( 'design/admin4/stylesheets/admin4.css' );
        $this->assertSame( array( 'display' => 'flex', 'flex' => '1 1 auto', 'flex-direction' => 'column', 'min-height' => '0' ),
                           self::declarations( $css, '#leftmenu-design:has(> #content-tree)' ) );
        $this->assertSame( array( 'flex' => 'none' ), self::declarations( $css, '#leftmenu-design:has(> #content-tree) > *' ) );
        $this->assertSame( array( 'flex' => '1 1 0', 'min-height' => '8rem', 'overflow' => 'auto' ),
                           self::declarations( $css, '#leftmenu-design > #content-tree #contentstructure' ) );
        // the cards the rules are written for: #content-tree, its .box-bc > .box-ml > .box-content, the tree in it
        foreach ( array( 'content', 'media', 'my', 'user' ) as $part )
        {
            $menu = self::read( 'design/admin4/templates/parts/' . $part . '/menu.tpl' );
            $this->assertMatchesRegularExpression( '/<div id="content-tree">.*<div class="box-bc"><div class="box-ml"><div class="box-content">\s*(\{[^}]*\}\s*)*<div id="contentstructure">/s',
                                                   $menu, $part );
        }
    }

    public function testTheTreeCardRulesOnlyReachTheColumnsOwnCard()
    {
        $css = self::read( 'design/admin4/stylesheets/admin4.css' );
        $this->assertSame( array( 'display' => 'flex', 'flex' => '1 1 0', 'flex-direction' => 'column', 'min-height' => '0' ),
                           self::declarations( $css, '#leftmenu-design > #content-tree > .box-bc > .box-ml > .box-content' ) );
        // every selector that sizes the card's inner boxes or the tree starts at the column's own card
        preg_match_all( '/([^{}]+)\{([^}]*)\}/', preg_replace( '#/\*.*?\*/#s', '', $css ), $rules, PREG_SET_ORDER );
        $checked = 0;
        foreach ( $rules as $rule )
        {
            if ( strpos( $rule[2], 'flex' ) === false && strpos( $rule[2], 'min-height' ) === false )
            {
                continue;
            }
            foreach ( explode( ',', $rule[1] ) as $selector )
            {
                $selector = trim( $selector );
                if ( preg_match( '/#content-tree(\s*>\s*\.box-bc|\s+#contentstructure)/', $selector ) )
                {
                    ++$checked;
                    $this->assertStringStartsWith( '#leftmenu-design > #content-tree', $selector,
                                                   'a wrapped tree (the newsletter menu) would collapse to its heading' );
                }
            }
        }
        $this->assertGreaterThanOrEqual( 4, $checked );
    }
}
