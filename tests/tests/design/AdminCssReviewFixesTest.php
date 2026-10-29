<?php
/**
 * The fixes of the admin4 CSS review of 15.10.2026, checked on the files alone (no database):
 *
 * - the sign-in page's .a4-check and the node head's .a4-badge edge are scoped, not defined for the whole admin;
 * - the row menu's mask icons (.crankfield, .thumbfield) draw in forced colours and the menu button is a 24px target;
 * - the pop-up menus cap their height with 100dvh after 100vh (iOS);
 * - the dead legacy modal rule (div:has(.modal-window)) and the duplicated datatable block are gone;
 * - the IcoMoon font loads woff only, with font-display, and its glyphs are not read out;
 * - text is not set in a percentage that falls below 12px;
 * - the language badge's focus ring is the admin ring, the in-place confirmation's summary has one rule of its own
 *   (components.css, both copies).
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminCssReviewFixesTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminBaseStylesheetsTest.php';

class AdminCssReviewFixesTest extends PHPUnit\Framework\TestCase
{
    private static function file( $path )
    {
        return (string)file_get_contents( dirname( __DIR__, 3 ) . '/' . $path );
    }

    private static function css( $file )
    {
        return preg_replace( '#/\*.*?\*/#s', '', self::file( 'design/admin4/stylesheets/' . $file ) );
    }

    /** Every selector (comma list split) that ends in or contains $class, over the rules outside @media blocks */
    private static function selectorsWith( $css, $class )
    {
        $found = array();
        foreach ( array_keys( AdminBaseStylesheetsTest::rules( $css ) ) as $list )
        {
            foreach ( AdminBaseStylesheetsTest::selectors( $list ) as $selector )
            {
                if ( preg_match( '/' . preg_quote( $class, '/' ) . '(?![\w-])/', $selector ) )
                {
                    $found[] = $selector;
                }
            }
        }
        return $found;
    }

    public function testTheSignInCheckboxRuleDoesNotReachOtherCheckboxRows()
    {
        // admin4.css loads before components.css, so a bare .a4-check there leaked its margin, size and colour
        foreach ( self::selectorsWith( self::css( 'admin4.css' ), '.a4-check' ) as $selector )
        {
            $this->assertStringStartsWith( '.a4-login-form ', $selector, "admin4.css: $selector must stay inside the sign-in form" );
        }
        $rules = AdminBaseStylesheetsTest::rules( self::css( 'admin4.css' ) );
        $this->assertSame( 'var(--a4-space-4) 0 0', $rules['.a4-login-form .a4-check']['margin'] );
        $this->assertStringContainsString( 'class="a4-check"', self::file( 'design/admin4/templates/user/login.tpl' ), 'the template the rule is for' );
        $this->assertStringContainsString( 'class="a4-login-form"', self::file( 'design/admin4/templates/user/login.tpl' ) );
    }

    public function testTheNodeHeadAddsOnlyTheNeutralBadgesLine()
    {
        $css = self::css( 'admin4.css' );
        foreach ( self::selectorsWith( $css, '.a4-badge' ) as $selector )
        {
            $this->assertMatchesRegularExpression( '/^\.a4-node-badges |^\.a4-subitem-name > /', $selector, "admin4.css: $selector would style every badge of the admin" );
        }
        $rules = AdminBaseStylesheetsTest::rules( $css );
        $this->assertSame( 'inset 0 0 0 1px var(--a4-line)', $rules['.a4-node-badges .a4-badge']['box-shadow'] );
        $this->assertSame( 'none', $rules['.a4-node-badges .a4-badge-class, .a4-node-badges .a4-badge.is-ok, .a4-node-badges .a4-badge.is-warn, .a4-node-badges .a4-badge.is-bad']['box-shadow'] );
        // the component's own look (radius, padding, weight) is components.css alone: no dead copy in the frame
        foreach ( array( 'padding', 'border-radius', 'font-weight', 'line-height' ) as $property )
        {
            $this->assertArrayNotHasKey( $property, $rules['.a4-node-badges .a4-badge'] );
        }
        $this->assertStringContainsString( 'a4-node-badges', self::file( 'design/admin4/templates/node/view/a4_full_body.tpl' ) );
    }

    public function testTheRowMenuIconsSurviveForcedColours()
    {
        $forced = AdminBaseStylesheetsTest::rules( self::css( 'base.css' ), true );
        $list = '.crankfield::before, .crankfield-disabled::before, a:hover > .crankfield::before, a:focus-visible > .crankfield::before, .thumbview > .thumbfield::before';
        $this->assertArrayHasKey( $list, $forced, 'the mask icons are drawn with background-color, which forced colours replace with Canvas' );
        $this->assertSame( 'none', $forced[$list]['forced-color-adjust'] );
        $this->assertSame( 'CanvasText', $forced[$list]['background-color'] );
        $this->assertSame( 'GrayText', $forced['.crankfield-disabled::before']['background-color'], 'disabled stays recognisable' );
        // every crank and thumbnail mask icon of the stylesheet is in the block (the sort carets are in admin4.css)
        $plain = self::css( 'base.css' );
        preg_match_all( '/([^{}]+)\{[^{}]*\bmask:/', preg_replace( '/@media[^{]*\{(?:[^{}]*\{[^{}]*\})*[^{}]*\}/s', '', $plain ), $masks );
        foreach ( $masks[1] as $selectors )
        {
            foreach ( AdminBaseStylesheetsTest::selectors( $selectors ) as $selector )
            {
                if ( strpos( $selector, 'crankfield' ) === false && strpos( $selector, 'thumbfield' ) === false )
                {
                    continue;
                }
                $this->assertContains( $selector, AdminBaseStylesheetsTest::selectors( $list ), "$selector is a mask icon without a forced-colours rule" );
            }
        }
    }

    public function testTheRowMenuButtonIsATwentyFourPixelTarget()
    {
        $rules = AdminBaseStylesheetsTest::rules( self::css( 'base.css' ) );
        $this->assertSame( '16px', $rules['.crankfield, .crankfield-disabled']['height'], 'the icon itself does not grow' );
        $this->assertSame( '""', $rules['.crankfield::after']['content'] );
        $this->assertSame( 'absolute', $rules['.crankfield::after']['position'] );
        $this->assertSame( '-4px', $rules['.crankfield::after']['inset'], '16px + 2 x 4px = 24px' );
        $this->assertSame( 'relative', $rules['.crankfield, .crankfield-disabled']['position'], 'the target is placed from the icon' );
        $this->assertStringContainsString( 'class="crankfield"', self::file( 'design/admin4/javascript/ezajaxsubitems_expdatatable.js' ) );
    }

    public function testThePopupMenusUseTheDynamicViewportHeight()
    {
        foreach ( array( 'admin4.css', 'base.css' ) as $file )
        {
            $css = self::css( $file );
            preg_match_all( '/max-height:\s*calc\(100vh([^;]*)\);(\s*)(max-height:\s*calc\(100dvh([^;]*)\);)?/', $css, $m, PREG_SET_ORDER );
            foreach ( $m as $pair )
            {
                $this->assertNotEmpty( $pair[3] ?? '', "$file: max-height with 100vh needs the 100dvh line after it" );
                $this->assertSame( $pair[1], $pair[4], "$file: the dvh line repeats the vh one" );
            }
        }
        $this->assertStringContainsString( 'calc(100dvh - 2 * var(--a4-space-3))', self::css( 'base.css' ), 'the context menu' );
    }

    public function testNothingFixesAndCentresAnAncestorOfALegacyModalWindow()
    {
        // :has(.modal-window) matched every ancestor div of one and made it fixed and shifted by 50%
        $this->assertStringNotContainsString( ':has(.modal-window)', self::css( 'base.css' ) );
        $this->assertStringNotContainsString( ':has(.modal-window)', self::css( 'admin4.css' ) );
        $this->assertStringNotContainsString( 'modal-window', self::file( 'design/admin4/templates/pagelayout.tpl' ) );
    }

    public function testTheDatatableStylesheetDefinesTheCrankAndThumbFieldsOnce()
    {
        $css = self::file( 'design/admin4/stylesheets/base.css' );
        $this->assertSame( 2, substr_count( $css, 'three-dots-vertical.svg' ), 'the -webkit-mask and the mask of one rule' );
        $this->assertSame( 1, substr_count( $css, '.thumbview:hover span' ) );
        $this->assertSame( 1, preg_match_all( '/^\.crankfield,\s*\.crankfield-disabled\s*\{\s*height: 16px;/m', $css ) );
    }

    public function testTheIcoMoonFontIsLoadedLean()
    {
        $css = self::file( 'design/admin4/stylesheets/base.css' );
        preg_match( '/@font-face\s*\{[^}]*IcoMoon[^}]*\}/s', $css, $face );
        $this->assertNotEmpty( $face, '@font-face IcoMoon' );
        $this->assertStringContainsString( "IcoMoon.woff", $face[0] );
        $this->assertFileExists( dirname( __DIR__, 3 ) . '/design/admin4/fonts/IcoMoon.woff' );
        foreach ( array( '.eot', '.svg', '.ttf' ) as $dead )
        {
            $this->assertStringNotContainsString( "IcoMoon$dead", $face[0] );
        }
        // block, not swap: an icon glyph has no readable fallback letter, and a swapped-in letter of another width moved the pagers (#554)
        $this->assertMatchesRegularExpression( '/font-display:\s*block/', $face[0] );
        $this->assertDoesNotMatchRegularExpression( '/\bspeak\s*:/', preg_replace( '#/\*.*?\*/#s', '', $css ), 'speak is obsolete' );
        $this->assertStringContainsString( 'content: attr(data-icon) / "";', $css, 'no text for the private-use glyph' );
    }

    public function testNoBaseTextIsSetInAPercentageBelowTwelvePixels()
    {
        // 85% of the 13px body is 11px
        $core = AdminBaseStylesheetsTest::rules( self::css( 'base.css' ) );
        $this->assertSame( 'var(--a4-text-xs)', $core['.small']['font-size'] );
        $table = AdminBaseStylesheetsTest::rules( self::css( 'base.css' ) );
        $this->assertSame( 'var(--a4-text-xs)', $table['.admin-dt caption']['font-size'] );
    }

    public function testThePasswordRulesTickIsADrawnMaskInTheCardColour()
    {
        $css = self::css( 'admin4.css' );
        $rules = AdminBaseStylesheetsTest::rules( $css );
        $icon = '.a4pw-rules li[data-state="met"] .a4pw-rule-icon';
        $this->assertStringNotContainsString( '%23fff', $css, 'a white stroke is 1.4:1 on the light green of dark mode' );
        $this->assertSame( 'var(--a4-card)', $rules[$icon . '::after']['background-color'] );
        $this->assertStringContainsString( 'check-lg.svg', $rules[$icon . '::after']['mask'] );
        $this->assertSame( 'Canvas', AdminBaseStylesheetsTest::rules( $css, true )[$icon . '::after']['background-color'] );
    }

    public function testTheLanguageBadgeFocusRingIsTheAdminRing()
    {
        foreach ( array( 'design/admin4', 'design/admin' ) as $design )
        {
            $rules = AdminBaseStylesheetsTest::rules( preg_replace( '#/\*.*?\*/#s', '', self::file( "$design/stylesheets/components.css" ) ) );
            // 2px of --a4-accent-strong was 2.8:1 on the soft tint of a current badge in dark mode
            $this->assertSame( '3px solid var(--a4-ring)', $rules['a.a4-lang:focus-visible']['outline'], $design );
        }
    }

    public function testTheConfirmationSummaryHasOneRuleOfItsOwn()
    {
        $rules = AdminBaseStylesheetsTest::rules( self::css( 'components.css' ) );
        foreach ( array_keys( $rules ) as $list )
        {
            $selectors = AdminBaseStylesheetsTest::selectors( $list );
            if ( in_array( '.a4-confirm > summary', $selectors, true ) )
                $this->assertSame( array( '.a4-confirm > summary' ), $selectors, "shared with $list" );
        }
        $summary = $rules['.a4-confirm > summary'];
        $this->assertSame( 'inline-flex', $summary['display'] );
        $this->assertSame( 'var(--a4-bad)', $summary['color'] );
        $this->assertSame( 'var(--a4-control-h)', $summary['min-height'] );
        $this->assertSame( 'none', $summary['list-style'] );
        $this->assertSame( 'pointer', $summary['cursor'] );
        $this->assertSame( 'flex', $rules['.a4-fold > summary']['display'] );
    }
}
