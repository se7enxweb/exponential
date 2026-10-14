<?php
/**
 * The contrast of the administration's colour tokens (design/admin4/stylesheets/components.css and the frame's
 * page colour in admin4.css), computed with the WCAG 2 formula in the light and in the dark mode: the text tokens
 * on every surface 4.5:1, each state's text on its own tint 4.5:1 (a translucent tint is laid over the card first),
 * the text on filled buttons 4.5:1 also when hovered, the focus ring and the field edges 3:1 (WCAG 1.4.11), the
 * chart colours 3:1 on a card. A token changed so that a pair falls below fails here.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminTokenContrastTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminTokenContrastTest extends PHPUnit\Framework\TestCase
{
    private static $modes;

    public static function setUpBeforeClass(): void
    {
        $root = dirname( __DIR__, 3 );
        $components = file_get_contents( $root . '/design/admin4/stylesheets/components.css' );
        $frame = file_get_contents( $root . '/design/admin4/stylesheets/admin4.css' );
        $light = self::tokens( $components, ':root' ) + self::tokens( $frame, ':root' );
        $dark = self::tokens( $components, 'html[data-a4-theme="dark"]' ) + self::tokens( $frame, 'html[data-a4-theme="dark"]' ) + $light;
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
            foreach ( array( '--a4-card', '--a4-surface', '--a4-soft' ) as $surface )
            {
                $pairs[] = array( $text, $surface, 4.5 );
            }
        }
        foreach ( array( 'ok', 'warn', 'bad', 'info' ) as $state )
        {
            $pairs[] = array( "--a4-$state", "--a4-$state-bg", 4.5 );
        }
        $pairs[] = array( '--a4-on-accent', '--a4-accent-strong', 4.5 );
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
}
