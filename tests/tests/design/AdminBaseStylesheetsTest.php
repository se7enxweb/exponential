<?php
/**
 * admin4's base stylesheets after the CSS review of October 2026, checked on the files alone (no database):
 *
 * - links and the diff marks take the tokens (a dark-mode link was 1.8:1, a diff mark light text on yellow 1.1:1);
 * - a module stylesheet's state that differs only by border, background or shadow (current, selected, moved,
 *   pressed, changed) gets a Highlight outline in forced colors, which drop all three;
 * - the meter's fill is --a4-accent-text on the --a4-soft track (the pair is in AdminTokenContrastTest);
 * - a focused input.button looks hovered (theme/rounded.css painted it rgba(0, 0, 0, .1) over admin4.css);
 * - the context menu's focus ring is --a4-ring in both modes, with no dark-mode rule of its own;
 * - the search scope pane fits a phone, the sidebar tabs take the tokens;
 * - responsive.css and setup-extensions.css, which admin4 did not need, are gone, and admin3 still loads its own
 *   responsive.css;
 * - no vendor-prefixed or IE-only leftovers in the base stylesheets;
 * - a print style, and the head script prints with the light tokens.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminBaseStylesheetsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminTokenContrastTest.php';

class AdminBaseStylesheetsTest extends PHPUnit\Framework\TestCase
{
    const BASE = array( 'core.css', 'content.css', 'pagelayout.css', 'theme/rounded.css', 'theme/admin_datatable.css', 'theme/modalwindow.css' );

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function css( $file )
    {
        return preg_replace( '#/\*.*?\*/#s', '', (string)file_get_contents( self::root() . '/design/admin4/stylesheets/' . $file ) );
    }

    /**
     * Returns selector => array( property => value ) of the top-level rules and the rules inside @media blocks (a
     * later declaration of the same selector wins, as in the cascade); with $forced only the rules inside
     * "@media (forced-colors: active)", without it only the rules outside such a block
     */
    public static function rules( $css, $forced = false )
    {
        preg_match_all( '/@media \(forced-colors: active\)\s*\{((?:[^{}]*\{[^{}]*\})*)[^{}]*\}/s', $css, $blocks );
        $css = $forced ? implode( "\n", $blocks[1] ) : str_replace( $blocks[0], '', $css );
        $css = preg_replace( '/@(media|supports)[^{]*\{((?:[^{}]*\{[^{}]*\})*)[^{}]*\}/s', '$2', $css );
        $rules = array();
        preg_match_all( '/([^{}]+)\{([^{}]*)\}/s', $css, $m, PREG_SET_ORDER );
        foreach ( $m as $rule )
        {
            $selector = trim( preg_replace( '/\s+/', ' ', $rule[1] ) );
            foreach ( explode( ';', $rule[2] ) as $declaration )
            {
                if ( strpos( $declaration, ':' ) !== false )
                {
                    list( $property, $value ) = explode( ':', $declaration, 2 );
                    $rules[$selector][strtolower( trim( $property ) )] = trim( $value );
                }
            }
        }
        return $rules;
    }

    /**
     * Splits a selector list at its top-level commas (not inside :is(), :has(), [..])
     */
    public static function selectors( $list )
    {
        return array_map( 'trim', preg_split( '/,(?![^()\[\]]*[)\]])/', $list ) );
    }

    public function testLinksTakeTheAccentTextToken()
    {
        $core = self::rules( self::css( 'core.css' ) );
        $this->assertSame( 'var(--a4-accent-text)', $core['a']['color'] );
        $this->assertSame( 'var(--a4-ink)', $core['a:hover']['color'] );
        $rounded = self::rules( self::css( 'theme/rounded.css' ) );
        $this->assertSame( 'var(--a4-accent-text)', $rounded['ul.tabs li a, ul.tabs li span.disabled']['color'] );
        $this->assertSame( 'var(--a4-accent-text)', self::rules( self::css( 'theme/admin_datatable.css' ) )['tbody .admin-dt-editable']['color'] );
    }

    public function testDiffMarksTakeTheStateTokens()
    {
        $content = self::rules( self::css( 'content.css' ) );
        $this->assertSame( 'var(--a4-ok)', $content['ins']['color'] );
        $this->assertSame( '1px solid var(--a4-ok)', $content['ins']['border-bottom'] );
        $this->assertSame( 'var(--a4-bad)', $content['del']['color'] );
        $this->assertSame( '0.5em solid var(--a4-ok)', $content['div.blockchanges ins']['border-left'] );
        $this->assertSame( '0.5em solid var(--a4-bad)', $content['div.blockchanges del']['border-left'] );
        // the shown side's changes: ink on the warn tint, readable in both modes (modules/content.css does the same)
        foreach ( array( 'div.previous del', 'div.latest ins' ) as $selector )
        {
            $this->assertSame( 'var(--a4-warn-bg)', $content[$selector]['background-color'], $selector );
            $this->assertSame( 'var(--a4-ink)', $content[$selector]['color'], $selector );
        }
        $this->assertArrayNotHasKey( 'color', $content['ins:hover'], 'a hovered mark keeps the token colour' );
    }

    /**
     * A state rule of a module stylesheet: its selector names a state, and it changes the look only by border,
     * background or shadow (a colour change of the text alone survives forced colors)
     */
    public static function isEdgeOnlyState( $selector, array $declarations )
    {
        if ( !preg_match( '/\.is-(current|selected|moved|dirty)\b|\[aria-(pressed|current|selected)="true"\]|:checked/', $selector ) )
        {
            return false;
        }
        return (bool)preg_grep( '/^(border(-color)?|background(-color)?|box-shadow)$/', array_keys( $declarations ) );
    }

    public function testStateCheckerCatchesWhatItShould()
    {
        $this->assertTrue( self::isEdgeOnlyState( '.a4-x.is-current', array( 'border-color' => 'var(--a4-accent)' ) ) );
        $this->assertTrue( self::isEdgeOnlyState( '.a4-x[aria-pressed="true"]', array( 'background-color' => 'var(--a4-accent-soft)' ) ) );
        $this->assertTrue( self::isEdgeOnlyState( '.a4-x:has(input:checked)', array( 'box-shadow' => 'inset 0 0 0 1px var(--a4-accent)' ) ) );
        $this->assertFalse( self::isEdgeOnlyState( '.a4-x.is-dirty .a4-meta', array( 'color' => 'var(--a4-ink)' ) ) );
        $this->assertFalse( self::isEdgeOnlyState( '.a4-x:hover', array( 'border-color' => 'var(--a4-accent)' ) ) );
        $this->assertSame( array( '.a', '.b:is(.c, .d)', '.e[x="1,2"]' ), self::selectors( '.a, .b:is(.c, .d), .e[x="1,2"]' ) );
    }

    public function testModuleStatesKeepAnOutlineInForcedColors()
    {
        $missing = array();
        foreach ( array( 'admin4', 'admin' ) as $design )
        {
            foreach ( glob( self::root() . "/design/$design/stylesheets/modules/*.css" ) as $path )
            {
                $css = preg_replace( '#/\*.*?\*/#s', '', (string)file_get_contents( $path ) );
                $outlined = array();
                foreach ( self::rules( $css, true ) as $list => $declarations )
                {
                    if ( preg_match( '/^2px solid Highlight$/', $declarations['outline'] ?? '' ) )
                    {
                        $outlined = array_merge( $outlined, self::selectors( $list ) );
                    }
                }
                foreach ( self::rules( $css ) as $list => $declarations )
                {
                    if ( !self::isEdgeOnlyState( $list, $declarations ) )
                    {
                        continue;
                    }
                    foreach ( self::selectors( $list ) as $selector )
                    {
                        if ( !in_array( $selector, $outlined, true ) )
                        {
                            $missing[] = "design/$design/stylesheets/modules/" . basename( $path ) . ": $selector";
                        }
                    }
                }
            }
        }
        $this->assertSame( array(), $missing,
            'give these states "outline: 2px solid Highlight" in the file\'s @media (forced-colors: active) block' );
    }

    public function testTheMeterFillStandsOutFromItsTrack()
    {
        $components = self::rules( self::css( 'components.css' ) );
        $this->assertSame( 'var(--a4-soft)', $components['.a4-meter']['background-color'] );
        $this->assertSame( 'var(--a4-accent-text)', $components['.a4-meter > span']['background-color'],
            '--a4-accent is 2.9:1 on --a4-soft in light mode, --a4-accent-strong 2.8:1 in dark mode' );
        $pairs = array_map( function ( $pair ) { return $pair[0] . ' on ' . $pair[1] . ' ' . $pair[2]; }, AdminTokenContrastTest::pairs() );
        $this->assertContains( '--a4-accent-text on --a4-soft 3', $pairs );
    }

    public function testAFocusedButtonLooksHovered()
    {
        $rounded = self::rules( self::css( 'theme/rounded.css' ) );
        $this->assertSame( 'var(--a4-soft)', $rounded['input.button:hover, input.button:focus']['background-color'] );
        $frame = self::rules( self::css( 'admin4.css' ) );
        $this->assertSame( 'var(--a4-soft)', $frame['input.button:hover, input.button:focus']['background-color'] );
        $this->assertSame( 'var(--a4-field-line)', $frame['input.button:hover, input.button:focus']['border-color'] );
    }

    public function testTheContextMenuRingIsOneTokenInBothModes()
    {
        $pagelayout = self::css( 'pagelayout.css' );
        $rules = self::rules( $pagelayout );
        $this->assertSame( '2px solid var(--a4-ring)', $rules['div.popupmenu a:focus-visible']['outline'] );
        // the tokens switch for the dark mode; a base stylesheet has no dark-mode rule of its own
        foreach ( self::BASE as $file )
        {
            $this->assertStringNotContainsString( 'data-a4-theme', self::css( $file ), $file );
        }
    }

    public function testHeaderPartsTakeTheTokens()
    {
        $rules = self::rules( self::css( 'pagelayout.css' ) );
        $this->assertSame( 'min(300px, calc(100vw - 2 * var(--a4-space-4)))', $rules['.searchscope-pane']['width'],
            'the pane fits a 320px window' );
        $this->assertSame( '1px solid var(--a4-line)', $rules['.searchscope-pane > .searchscope-title']['border-bottom'] );
        $this->assertSame( 'var(--a4-field)', $rules['.searchblock input.searchtext:focus']['background-color'],
            'the focused search field was pale yellow under light text in dark mode' );
        // the tabs on the window edges: the field edge (3:1 on the page in both modes) with the card colour on it
        $this->assertSame( 'var(--a4-field-line)', $rules['.sidebar-control']['background-color'] );
        $this->assertSame( 'var(--a4-card)', $rules['.sidebar-control .a4-icon']['color'] );
        $this->assertSame( 'var(--a4-muted)', $rules['.sidebar-control:hover']['background-color'] );
        $this->assertStringNotContainsString( 'sidebar-control .a4-icon', self::css( 'admin4.css' ), 'admin4.css gives the tab icon no header colour' );
    }

    public function testUnusedStylesheetsAreGone()
    {
        $root = self::root();
        foreach ( array( 'design/admin4/stylesheets/responsive.css', 'design/admin4/stylesheets/setup-extensions.css', 'design/admin/stylesheets/setup-extensions.css' ) as $file )
        {
            $this->assertFileDoesNotExist( "$root/$file" );
        }
        $this->assertDoesNotMatchRegularExpression( '/^BackendCSSFileList\[\]=responsive\.css/m', (string)file_get_contents( "$root/settings/design.ini" ) );
        $this->assertStringNotContainsString( "'responsive.css'", (string)file_get_contents( "$root/design/admin4/templates/page_head_style.tpl" ) );
        // admin3 keeps its responsive layout: it names the file itself in both branches
        $this->assertFileExists( "$root/design/admin3/stylesheets/responsive.css" );
        $this->assertSame( 2, substr_count( (string)file_get_contents( "$root/design/admin3/templates/page_head_style.tpl" ), "'responsive.css'" ) );
        // the one live rule of admin4's responsive.css moved to pagelayout.css
        $rules = self::rules( self::css( 'pagelayout.css' ) );
        $this->assertSame( '0.5rem', $rules['.message-confirmation, .message-feedback, .message-warning, .message-error']['padding'] ?? null );
    }

    public function testNoVendorPrefixedOrInternetExplorerLeftovers()
    {
        $found = array();
        foreach ( self::BASE as $file )
        {
            if ( preg_match_all( '/-(moz|ms|o)-(transition|box-shadow|border-radius|linear-gradient|outline)|-webkit-(transition|box-shadow|border-radius|gradient)\b|filter:\s*alpha|zoom:\s*1|linear-gradient\(\s*top|:-moz-placeholder|::-moz-placeholder|-webkit-input-placeholder/', self::css( $file ), $m ) )
            {
                $found[] = "$file: " . implode( ', ', array_unique( $m[0] ) );
            }
        }
        $this->assertSame( array(), $found );
    }

    public function testThePagePrintsWithoutTheFrame()
    {
        $frame = self::css( 'admin4.css' );
        $this->assertSame( 1, preg_match( '/@media print\s*\{((?:[^{}]*\{[^{}]*\})*)[^{}]*\}/s', $frame, $print ) );
        $rules = self::rules( $print[1] );
        $hidden = array();
        foreach ( $rules as $list => $declarations )
        {
            if ( ( $declarations['display'] ?? '' ) === 'none !important' )
            {
                $hidden = array_merge( $hidden, self::selectors( $list ) );
            }
        }
        foreach ( array( '#header', '.sidebar', '.sidebar-controls', '#controlbar-top', '#footer', 'div.popupmenu', '.a4-toasts' ) as $part )
        {
            $this->assertContains( $part, $hidden, "$part is not printed" );
        }
        $this->assertSame( 'none !important', $rules['*, *::before, *::after']['box-shadow'] );
        $head = (string)file_get_contents( self::root() . '/design/admin4/templates/page_head_style.tpl' );
        $this->assertStringContainsString( "window.addEventListener('beforeprint', function () { root.setAttribute('data-a4-theme', 'light'); });", $head );
        $this->assertStringContainsString( "window.addEventListener('afterprint', function () { apply(root.getAttribute('data-a4-theme-choice')); });", $head );
    }
}
