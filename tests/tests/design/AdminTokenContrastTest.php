<?php
/**
 * The contrast of the administration's colour tokens (design/admin4/stylesheets/components.css and the frame's
 * page colour in admin4.css), computed with the WCAG 2 formula in the light and in the dark mode: the text tokens
 * on every surface 4.5:1, each state's text on its own tint 4.5:1 (a translucent tint is laid over the card first),
 * the text on filled buttons 4.5:1 also when hovered, the focus ring and the field edges 3:1 (WCAG 1.4.11), the
 * chart colours 3:1 on a card, a meter's fill 3:1 on its track. A token changed so that a pair falls below fails here.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminTokenContrastTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminFiles.php';

class AdminTokenContrastTest extends PHPUnit\Framework\TestCase
{
    private static $modes;

    public static function setUpBeforeClass(): void
    {
        $root = dirname( __DIR__, 3 );
        $components = AdminFiles::read( $root . '/design/admin4/stylesheets/components.css' );
        $frame = AdminFiles::read( $root . '/design/admin4/stylesheets/admin4.css' );
        $light = self::tokens( $components, ':root' ) + self::tokens( $frame, ':root' );
        $dark = self::tokens( $components, 'html[data-a4-theme="dark"]:not(:where([data-a4-modes="light"]))' ) + self::tokens( $frame, 'html[data-a4-theme="dark"]' ) + $light;
        self::$modes = array( 'light' => $light, 'dark' => $dark );
    }

    /**
     * Returns the custom properties (name => value) of every top-level block whose selector is exactly $selector,
     * a later block overriding an earlier one as in the cascade
     */
    private static function tokens( $css, $selector )
    {
        $css = preg_replace( '#/\*.*?\*/#s', '', $css );
        preg_match_all( '/(^|\})\s*' . preg_quote( $selector, '/' ) . '\s*\{([^}]*)\}/s', $css, $blocks );
        $tokens = array();
        foreach ( $blocks[2] as $block )
        {
            preg_match_all( '/(--[\w-]+)\s*:\s*([^;]+);/', $block, $pairs, PREG_SET_ORDER );
            foreach ( $pairs as $pair )
            {
                $tokens[$pair[1]] = trim( $pair[2] );
            }
        }
        return $tokens;
    }

    /**
     * Returns an opaque #rrggbb for #rgb, #rrggbb or rgba() laid over $under (an opaque #rrggbb)
     */
    public static function opaque( $colour, $under )
    {
        if ( preg_match( '/^rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*(?:,\s*([\d.]+)\s*)?\)$/', $colour, $m ) )
        {
            $alpha = isset( $m[4] ) && $m[4] !== '' ? (float)$m[4] : 1.0;
            $base = self::rgb( $under );
            $out = '#';
            foreach ( array( 1, 2, 3 ) as $i )
            {
                $out .= sprintf( '%02x', (int)round( $m[$i] * $alpha + $base[$i - 1] * ( 1 - $alpha ) ) );
            }
            return $out;
        }
        return $colour;
    }

    private static function rgb( $hex )
    {
        $hex = ltrim( $hex, '#' );
        if ( strlen( $hex ) === 3 )
        {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        return array( hexdec( substr( $hex, 0, 2 ) ), hexdec( substr( $hex, 2, 2 ) ), hexdec( substr( $hex, 4, 2 ) ) );
    }

    /**
     * Returns the relative luminance of an opaque #rgb or #rrggbb colour
     */
    public static function luminance( $hex )
    {
        $channels = array();
        foreach ( self::rgb( $hex ) as $value )
        {
            $c = $value / 255;
            $channels[] = $c <= 0.03928 ? $c / 12.92 : pow( ( $c + 0.055 ) / 1.055, 2.4 );
        }
        return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
    }

    public static function ratio( $a, $b )
    {
        $la = self::luminance( $a );
        $lb = self::luminance( $b );
        return ( max( $la, $lb ) + 0.05 ) / ( min( $la, $lb ) + 0.05 );
    }

    public function testTheFormulaGivesTheKnownRatios()
    {
        $this->assertEqualsWithDelta( 21.0, self::ratio( '#000', '#fff' ), 0.01 );
        $this->assertEqualsWithDelta( 4.54, self::ratio( '#767676', '#fff' ), 0.01 );
        $this->assertEqualsWithDelta( 1.0, self::ratio( '#c2410c', '#c2410c' ), 0.001 );
        $this->assertSame( '#808080', self::opaque( 'rgba(255, 255, 255, 0.5)', '#000000' ) );
        $this->assertSame( '#123456', self::opaque( '#123456', '#ffffff' ) );
    }

    /**
     * Returns the pairs to check: foreground token, background token, the ratio it needs, and optionally the one
     * mode the pair is used in
     */
    public static function pairs()
    {
        $pairs = array();
        foreach ( array( '--a4-ink', '--a4-muted', '--a4-accent-text', '--a4-ok', '--a4-warn', '--a4-bad', '--a4-info' ) as $text )
        {
            foreach ( array( '--a4-card', '--a4-surface', '--a4-soft', '--a4-zebra', '--a4-head-bg', '--a4-info-bg' ) as $surface )
            {
                $pairs[] = array( $text, $surface, 4.5 );
            }
        }
        foreach ( array( 'ok', 'warn', 'bad', 'info' ) as $state )
        {
            $pairs[] = array( "--a4-$state", "--a4-$state-bg", 4.5 );
            // muted text in a tinted row (a :target row of the Info page, a notice's second line)
            $pairs[] = array( '--a4-muted', "--a4-$state-bg", 4.5 );
        }
        $pairs[] = array( '--a4-on-accent', '--a4-accent-strong', 4.5 );
        // the main language's badge: the card colour on an ink fill (components.css .a4-lang.is-main)
        $pairs[] = array( '--a4-card', '--a4-ink', 4.5 );
        // the bar that marks a current or just-moved row on the accent tint
        $pairs[] = array( '--a4-accent-text', '--a4-accent-soft', 3.0 );
        // white on the pressed fill in light mode; in dark mode that fill is a light orange and carries
        // --a4-on-accent-hover, the pair checked above
        $pairs[] = array( '--a4-on-accent', '--a4-accent-press', 4.5, 'light' );
        $pairs[] = array( '--a4-on-accent-hover', '--a4-accent-press', 4.5 );
        foreach ( array( '--a4-card', '--a4-surface', '--a4-soft', '--a4-field', '--a4-page-color' ) as $surface )
        {
            $pairs[] = array( '--a4-ring', $surface, 3.0 );
        }
        foreach ( array( '--a4-card', '--a4-surface', '--a4-soft', '--a4-field', '--a4-page-color' ) as $surface )
        {
            $pairs[] = array( '--a4-field-line', $surface, 3.0 );
        }
        for ( $i = 1; $i <= 8; $i++ )
        {
            $pairs[] = array( "--a4-chart-$i", '--a4-card', 3.0 );
        }
        // the fill of an .a4-meter (plain and each state) against its track (WCAG 1.4.11): the amount is the bar
        foreach ( array( '--a4-accent-text', '--a4-ok', '--a4-warn', '--a4-bad', '--a4-info' ) as $fill )
        {
            $pairs[] = array( $fill, '--a4-soft', 3.0 );
        }
        return $pairs;
    }

    public function testTheTokenPairsReachTheirContrast()
    {
        $failures = array();
        foreach ( self::$modes as $mode => $tokens )
        {
            foreach ( self::pairs() as $pair )
            {
                list( $front, $back, $needed ) = $pair;
                if ( isset( $pair[3] ) && $pair[3] !== $mode )
                {
                    continue;
                }
                $this->assertArrayHasKey( $front, $tokens, "$mode: $front" );
                $this->assertArrayHasKey( $back, $tokens, "$mode: $back" );
                $this->assertMatchesRegularExpression( '/^#[0-9a-f]{3}([0-9a-f]{3})?$/i', $tokens[$front], "$mode: $front must be an opaque colour" );
                // a translucent background (the dark tints) is laid over the card it sits on
                $back = self::opaque( $tokens[$back], $tokens['--a4-card'] );
                $this->assertMatchesRegularExpression( '/^#[0-9a-f]{3}([0-9a-f]{3})?$/i', $back, "$mode: {$pair[1]} must be a colour" );
                $ratio = self::ratio( $tokens[$front], $back );
                if ( $ratio < $needed )
                {
                    $failures[] = sprintf( '%s: %s on %s is %.2F:1, needs %.1F:1', $mode, $front, $pair[1], $ratio, $needed );
                }
            }
        }
        $this->assertSame( array(), $failures );
    }

    /**
     * The classic admin (design/admin) and admin3 read the same tokens: their components.css is admin4's copy, so
     * every pair above holds there too (admin4l and the editor design load admin4's stylesheets)
     */
    public function testEveryAdminDesignReadsTheSameTokens()
    {
        $root = dirname( __DIR__, 3 );
        $admin4 = AdminFiles::read( $root . '/design/admin4/stylesheets/components.css' );
        $classic = AdminFiles::read( $root . '/design/admin/stylesheets/components.css' );
        foreach ( array( ':root', 'html[data-a4-theme="dark"]:not(:where([data-a4-modes="light"]))' ) as $selector )
        {
            $tokens = self::tokens( $admin4, $selector );
            $this->assertNotEmpty( $tokens, $selector );
            $this->assertSame( $tokens, self::tokens( $classic, $selector ), "design/admin's tokens ($selector) are admin4's" );
        }
        foreach ( array( 'admin4l', 'editor' ) as $design )
        {
            $this->assertDirectoryDoesNotExist( "$root/design/$design/stylesheets", "$design draws with admin4's stylesheets and tokens" );
        }
    }

    /**
     * The list tables of every admin design have admin4's header: --a4-head-bg with --a4-ink (4.5:1, checked
     * above) in place of the classic grey bar with light text (#eee on #6f6e6b was 4.4:1), separators, rounded top
     * corners, zebra and hover rows from the tokens; the datatable theme and expui's tables (the bridge) likewise;
     * and a line under a page section's heading
     */
    public function testTheListTablesOfEveryDesignTakeTheHeaderToken()
    {
        $root = dirname( __DIR__, 3 );
        foreach ( array( 'design/admin4/stylesheets/admin4.css', 'design/admin/stylesheets/theme/rounded.css', 'design/admin3/stylesheets/theme/rounded.css' ) as $file )
        {
            $css = preg_replace( '#/\*.*?\*/#s', '', AdminFiles::read( "$root/$file" ) );
            preg_match_all( '/(?:^|\})\s*table\.list th, table\.special th\s*\{([^}]*)\}/', $css, $m );
            $this->assertNotEmpty( $m[1], "$file: table.list th" );
            $last = end( $m[1] );
            $this->assertMatchesRegularExpression( '/background-color:\s*var\(--a4-head-bg\)/', $last, "$file: the header row" );
            $this->assertMatchesRegularExpression( '/(?<![\w-])color:\s*var\(--a4-ink\)/', $last, "$file: the header text" );
            $this->assertMatchesRegularExpression( '/border-inline-start:\s*1px solid var\(--a4-line-strong\)/', $last, "$file: the separators" );
            $this->assertStringContainsString( 'border-start-start-radius: var(--a4-radius-s)', $css, "$file: the rounded corners" );
            $this->assertMatchesRegularExpression( '/\{ background-color: var\(--a4-zebra\); \}/', $css, "$file: zebra rows" );
            $this->assertMatchesRegularExpression( '/tr:hover > :is\(td, th\[scope="row"\]\) \{ background-color: var\(--a4-info-bg\); \}/', $css, "$file: the hovered row" );
            $this->assertMatchesRegularExpression( '/div\.context-block \.box-header\s*\{[^}]*border-bottom:\s*1px solid var\(--a4-line\)/', $css, "$file: the section heading's line" );
        }
        $datatable = preg_replace( '#/\*.*?\*/#s', '', AdminFiles::read( "$root/design/admin/stylesheets/theme/admin_datatable.css" ) );
        $this->assertMatchesRegularExpression( '/\.admin-dt th \{[^}]*background-color: var\(--a4-head-bg\)/', $datatable );
        $this->assertMatchesRegularExpression( '/\.admin-dt th,\.admin-dt th a \{[^}]*color: var\(--a4-ink\)/', $datatable );
        foreach ( array( 'design/admin/stylesheets/pagelayout.css', 'design/admin3/stylesheets/pagelayout.css' ) as $file )
        {
            $css = AdminFiles::read( "$root/$file" );
            $this->assertStringContainsString( '--exp-dt-head: var(--a4-head-bg);', $css, "$file: expui's table header" );
            $this->assertStringContainsString( '--exp-dt-head-ink: var(--a4-ink);', $css, "$file: expui's table header text" );
        }
    }

    /**
     * The classic admin and admin3 have no dark mode: a raw text colour in their content area (not the dark frame:
     * header, top menu, footer, login page) reaches 4.5:1 on the rule's own raw background, or on white when the rule
     * sets none. White text without a background of its own sits on a fill set elsewhere; a disabled control is
     * exempt (WCAG 1.4.3). Greys come from --a4-ink and --a4-muted.
     */
    public function testTheClassicDesignsHaveNoLowContrastRawTextColour()
    {
        $root = dirname( __DIR__, 3 );
        $frame = '/#header|topmenu|#footer|login|^\s*#page\s*$|#page\s*,|#search|#logo|#columns\s*$|\.navbar|#leftmenu-design\s*$|#rightmenu-design\s*$|#debug|\.debug|code|pre\b|#mainmenu|#toolbar|disabled/i';
        $files = array_merge(
            array_diff( AdminFiles::files( "$root/design/admin/stylesheets/*.css" ), array( "$root/design/admin/stylesheets/components.css" ) ),
            AdminFiles::files( "$root/design/admin/stylesheets/theme/*.css" ),
            AdminFiles::files( "$root/design/admin3/stylesheets/*.css" ),
            AdminFiles::files( "$root/design/admin3/stylesheets/theme/*.css" )
        );
        $failures = array();
        $checked = 0;
        foreach ( $files as $file )
        {
            $css = preg_replace( '#/\*.*?\*/#s', '', AdminFiles::read( $file ) );
            preg_match_all( '/([^{}]*)\{([^{}]*)\}/', $css, $rules, PREG_SET_ORDER );
            foreach ( $rules as $rule )
            {
                $selector = trim( preg_replace( '/^.*@media[^{]*$/s', '', $rule[1] ) );
                if ( $selector === '' || preg_match( $frame, $selector ) || !preg_match( '/(?<![\w-])color\s*:\s*(#[0-9a-f]{3}(?:[0-9a-f]{3})?)\b/i', $rule[2], $colour ) )
                {
                    continue;
                }
                $own = preg_match( '/(?<![\w-])background(?:-color)?\s*:\s*[^;]*?(#[0-9a-f]{3}(?:[0-9a-f]{3})?)\b/i', $rule[2], $back );
                if ( !$own && self::luminance( $colour[1] ) > 0.99 )
                {
                    continue;
                }
                $checked++;
                $ratio = self::ratio( $colour[1], $own ? $back[1] : '#ffffff' );
                if ( $ratio < 4.5 )
                {
                    $failures[] = sprintf( '%s: %s { color: %s } on %s is %.2F:1', substr( $file, strlen( $root ) + 1 ), preg_replace( '/\s+/', ' ', $selector ), $colour[1], $own ? $back[1] : 'white', $ratio );
                }
            }
        }
        $this->assertGreaterThan( 20, $checked, 'the scan found raw text colours' );
        $this->assertSame( array(), $failures, 'use var(--a4-ink), var(--a4-muted) or a state token' );
    }

    /**
     * Resolves a custom property of the expui bridge (html:root in admin4.css, with the dark mode's override) to a
     * colour: var(--a4-x) is looked up in the mode's tokens
     */
    private static function bridge( $mode, $name )
    {
        $frame = file_get_contents( dirname( __DIR__, 3 ) . '/design/admin4/stylesheets/admin4.css' );
        $bridge = self::tokens( $frame, 'html:root' );
        if ( $mode === 'dark' )
        {
            $bridge = self::tokens( $frame, 'html[data-a4-theme="dark"]:root' ) + $bridge;
        }
        self::assertArrayHasKey( $name, $bridge, "$mode: $name" );
        self::assertMatchesRegularExpression( '/^var\((--[\w-]+)\)$/', $bridge[$name] );
        preg_match( '/^var\((--[\w-]+)\)$/', $bridge[$name], $m );
        return self::$modes[$mode][$m[1]];
    }

    /**
     * expui paints --exp-accent and --exp-accent-dark as the FILL of its primary dialog button, its upload button and a
     * selected day, with white text (exp/dialog.css, upload.css, datepicker.css): the bridge must map them to fills
     * white reaches 4.5:1 on, in both modes (it mapped --a4-accent, 3.06:1, and in dark mode --a4-accent-hover, 2.34:1).
     * The accent also draws outlines and a spinner edge: 3:1 on the card.
     */
    public function testTheExpuiBridgeFillsCarryWhiteText()
    {
        $failures = array();
        foreach ( array( 'light', 'dark' ) as $mode )
        {
            foreach ( array( '--exp-accent', '--exp-accent-dark' ) as $name )
            {
                $fill = self::bridge( $mode, $name );
                $ratio = self::ratio( '#ffffff', $fill );
                if ( $ratio < 4.5 )
                {
                    $failures[] = sprintf( '%s: white on %s (%s) is %.2F:1, needs 4.5:1', $mode, $name, $fill, $ratio );
                }
            }
            $card = self::$modes[$mode]['--a4-card'];
            $this->assertGreaterThanOrEqual( 3.0, self::ratio( self::bridge( $mode, '--exp-accent' ), $card ), "$mode: the accent as an outline on the card" );
        }
        $this->assertSame( array(), $failures );
    }

    /**
     * --exp-accent-ring is the tint expui lays over a drop zone while a file is dragged (upload.css) and its focus ring
     * is a box-shadow in the same colour: the tint stays a translucent fill, and the focus rings of expui's widgets are
     * set to --a4-ring (3:1, checked above) in admin4.css
     */
    public function testTheExpuiRingIsATintAndTheFocusRingsAreStillTheRing()
    {
        $frame = file_get_contents( dirname( __DIR__, 3 ) . '/design/admin4/stylesheets/admin4.css' );
        $bridge = self::tokens( $frame, 'html:root' );
        $this->assertSame( 'var(--a4-accent-soft)', $bridge['--exp-accent-ring'] );
        $this->assertMatchesRegularExpression( '/html \.exp-focus-ring:focus-visible, html \.exp-scope :focus-visible, html \.exp-dialog :focus-visible,\s*html \.exp-upload :focus-visible, html \.exp-upload-select:focus-within, html \.exp-datepicker button:focus-visible,\s*html img\.datepicker-icon:focus-visible \{ box-shadow: 0 0 0 3px var\(--a4-ring\); \}/', $frame );
        $this->assertStringContainsString( 'html .exp-dt-menu [role=menuitem]:focus-visible { box-shadow: inset 0 0 0 2px var(--a4-ring); }', $frame );
    }
}
