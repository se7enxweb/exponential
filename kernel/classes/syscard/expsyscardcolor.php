<?php
/**
 * File containing the expSysCardColor class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The colors of the system card on a terminal: which color depth the terminal has, and the ANSI codes of a color at
 * that depth.
 *
 *   truecolor  COLORTERM=truecolor or 24bit: ESC[38;2;R;G;Bm, the exact color
 *   256        TERM=*256color: ESC[38;5;Nm, the nearest color of the 6x6x6 cube or the gray ramp
 *   16         any other TERM: ESC[3Nm / ESC[9Nm, the nearest of the 16 basic colors
 *   none       NO_COLOR set (https://no-color.org), TERM=dumb, or not a terminal: no codes at all
 *
 * Colors are given as #RRGGBB everywhere (syscard.ini themes); an empty color means "the terminal's own".
 */
class expSysCardColor
{
    const TRUECOLOR = 'truecolor';
    const C256 = '256';
    const C16 = '16';
    const NONE = 'none';

    /** The 16 basic colors as xterm draws them, in code order (30-37, then 90-97) */
    const BASIC = array(
        array( 0, 0, 0 ), array( 205, 0, 0 ), array( 0, 205, 0 ), array( 205, 205, 0 ),
        array( 0, 0, 238 ), array( 205, 0, 205 ), array( 0, 205, 205 ), array( 229, 229, 229 ),
        array( 127, 127, 127 ), array( 255, 0, 0 ), array( 0, 255, 0 ), array( 255, 255, 0 ),
        array( 92, 92, 255 ), array( 255, 0, 255 ), array( 0, 255, 255 ), array( 255, 255, 255 ),
    );

    /** The levels of the 6x6x6 cube of the 256 colors */
    const CUBE = array( 0, 95, 135, 175, 215, 255 );

    /**
     * The color depth of a terminal.
     *
     * @param array $env the environment (NO_COLOR, COLORTERM, TERM)
     * @param bool $tty whether the output is a terminal
     * @return string truecolor, 256, 16 or none
     */
    public static function detect( array $env, $tty )
    {
        if ( isset( $env['NO_COLOR'] ) && $env['NO_COLOR'] !== '' && $env['NO_COLOR'] !== null )
            return self::NONE;
        if ( !$tty )
            return self::NONE;
        $term = isset( $env['TERM'] ) ? strtolower( (string)$env['TERM'] ) : '';
        if ( $term === 'dumb' )
            return self::NONE;
        $colorterm = isset( $env['COLORTERM'] ) ? strtolower( (string)$env['COLORTERM'] ) : '';
        if ( $colorterm === 'truecolor' || $colorterm === '24bit' )
            return self::TRUECOLOR;
        if ( strpos( $term, '256' ) !== false )
            return self::C256;
        return $term !== '' ? self::C16 : self::NONE;
    }

    /**
     * A depth given on the command line (--color=...), or null for "detect".
     *
     * @param string|null $value auto, truecolor, 24bit, 256, 16, 8, none, never, always
     * @return string|null
     */
    public static function parse( $value )
    {
        $value = strtolower( trim( (string)$value ) );
        switch ( $value )
        {
            case 'truecolor':
            case '24bit':
            case 'always':
                return self::TRUECOLOR;
            case '256':
                return self::C256;
            case '16':
            case '8':
                return self::C16;
            case 'none':
            case 'never':
            case 'no':
            case 'off':
                return self::NONE;
        }
        return null;
    }

    /**
     * @param string $hex #RRGGBB or RRGGBB (also #RGB)
     * @return int[]|null array( r, g, b ), null for an empty or broken color
     */
    public static function rgb( $hex )
    {
        $hex = ltrim( trim( (string)$hex ), '#' );
        if ( strlen( $hex ) === 3 )
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        if ( !preg_match( '/^[0-9a-f]{6}$/i', $hex ) )
            return null;
        return array( hexdec( substr( $hex, 0, 2 ) ), hexdec( substr( $hex, 2, 2 ) ), hexdec( substr( $hex, 4, 2 ) ) );
    }

    /**
     * @param int[] $rgb
     * @return string #rrggbb
     */
    public static function hex( array $rgb )
    {
        return sprintf( '#%02x%02x%02x', max( 0, min( 255, (int)round( $rgb[0] ) ) ), max( 0, min( 255, (int)round( $rgb[1] ) ) ),
                        max( 0, min( 255, (int)round( $rgb[2] ) ) ) );
    }

    /**
     * The SGR parameters of a foreground (or background) color at a depth: "38;2;242;140;40", "38;5;208", "33".
     *
     * @param string $hex
     * @param string $depth
     * @param bool $background
     * @return string '' when there is nothing to send
     */
    public static function code( $hex, $depth, $background = false )
    {
        $rgb = self::rgb( $hex );
        if ( $rgb === null || $depth === self::NONE )
            return '';
        switch ( $depth )
        {
            case self::TRUECOLOR:
                return ( $background ? '48' : '38' ) . ';2;' . $rgb[0] . ';' . $rgb[1] . ';' . $rgb[2];
            case self::C256:
                return ( $background ? '48' : '38' ) . ';5;' . self::to256( $rgb );
            case self::C16:
                $index = self::to16( $rgb );
                return (string)( ( $index < 8 ? 30 + $index : 90 + $index - 8 ) + ( $background ? 10 : 0 ) );
        }
        return '';
    }

    /**
     * The nearest of the 256 colors (16-231 the cube, 232-255 the gray ramp).
     *
     * @param int[] $rgb
     * @return int
     */
    public static function to256( array $rgb )
    {
        $level = function ( $v ) {
            $best = 0;
            foreach ( self::CUBE as $i => $c )
                if ( abs( $c - $v ) < abs( self::CUBE[$best] - $v ) )
                    $best = $i;
            return $best;
        };
        list( $r, $g, $b ) = array( $level( $rgb[0] ), $level( $rgb[1] ), $level( $rgb[2] ) );
        $cube = 16 + 36 * $r + 6 * $g + $b;
        $cubeRgb = array( self::CUBE[$r], self::CUBE[$g], self::CUBE[$b] );
        $average = ( $rgb[0] + $rgb[1] + $rgb[2] ) / 3;
        $grayIndex = (int)max( 0, min( 23, round( ( $average - 8 ) / 10 ) ) );
        $gray = 8 + 10 * $grayIndex;
        return self::distance( $rgb, array( $gray, $gray, $gray ) ) < self::distance( $rgb, $cubeRgb ) ? 232 + $grayIndex : $cube;
    }

    /**
     * The nearest of the 16 basic colors (0-7 normal, 8-15 bright).
     *
     * @param int[] $rgb
     * @return int
     */
    public static function to16( array $rgb )
    {
        $best = 0;
        $bestDistance = PHP_INT_MAX;
        foreach ( self::BASIC as $i => $basic )
        {
            $d = self::distance( $rgb, $basic );
            if ( $d < $bestDistance )
            {
                $bestDistance = $d;
                $best = $i;
            }
        }
        return $best;
    }

    /**
     * The squared distance of two colors, weighted for the eye.
     *
     * @param array $a
     * @param array $b
     * @return float
     */
    public static function distance( array $a, array $b )
    {
        return 2 * pow( $a[0] - $b[0], 2 ) + 4 * pow( $a[1] - $b[1], 2 ) + 3 * pow( $a[2] - $b[2], 2 );
    }

    /**
     * Text in a color (and bold or dim) at a depth; at depth none the text as it is.
     *
     * @param string $text
     * @param string $hex
     * @param string $depth
     * @param bool $bold
     * @param bool $dim
     * @return string
     */
    public static function paint( $text, $hex, $depth, $bold = false, $dim = false )
    {
        if ( $depth === self::NONE || $text === '' )
            return $text;
        $codes = array();
        if ( $bold )
            $codes[] = '1';
        if ( $dim )
            $codes[] = '2';
        $color = self::code( $hex, $depth );
        if ( $color !== '' )
            $codes[] = $color;
        return empty( $codes ) ? $text : "\033[" . implode( ';', $codes ) . 'm' . $text . "\033[0m";
    }

    /**
     * The color at a point of a gradient.
     *
     * @param string[] $stops #RRGGBB colors, at least one
     * @param float $t 0 (the first stop) to 1 (the last)
     * @return string #rrggbb, '' without stops
     */
    public static function gradient( array $stops, $t )
    {
        $colors = array();
        foreach ( $stops as $stop )
        {
            $rgb = self::rgb( $stop );
            if ( $rgb !== null )
                $colors[] = $rgb;
        }
        if ( empty( $colors ) )
            return '';
        if ( count( $colors ) === 1 )
            return self::hex( $colors[0] );
        $t = max( 0.0, min( 1.0, (float)$t ) );
        $scaled = $t * ( count( $colors ) - 1 );
        $i = (int)min( count( $colors ) - 2, floor( $scaled ) );
        $f = $scaled - $i;
        $a = $colors[$i];
        $b = $colors[$i + 1];
        return self::hex( array( $a[0] + ( $b[0] - $a[0] ) * $f, $a[1] + ( $b[1] - $a[1] ) * $f, $a[2] + ( $b[2] - $a[2] ) * $f ) );
    }

    /**
     * Text with a gradient across its characters (spaces keep no color).
     *
     * @param string $text
     * @param string[] $stops
     * @param string $depth
     * @param int $width the number of columns the gradient spans (so lines of one block share it); 0 for the text's length
     * @param float $offset added to the position (the reveal of --animate shifts it)
     * @return string
     */
    public static function paintGradient( $text, array $stops, $depth, $width = 0, $offset = 0.0 )
    {
        if ( $depth === self::NONE || $text === '' )
            return $text;
        $chars = self::chars( $text );
        $span = max( 1, ( $width > 0 ? $width : count( $chars ) ) - 1 );
        $out = '';
        $last = null;
        foreach ( $chars as $i => $char )
        {
            if ( $char === ' ' )
            {
                if ( $last !== null )
                    $out .= "\033[0m";
                $last = null;
                $out .= $char;
                continue;
            }
            $t = $i / $span + $offset;
            if ( $t > 1.0 )
                $t -= floor( $t );
            $code = self::code( self::gradient( $stops, $t ), $depth );
            if ( $code !== $last )
            {
                $out .= $code === '' ? "\033[0m" : "\033[" . $code . 'm';
                $last = $code;
            }
            $out .= $char;
        }
        return $out . ( $last !== null ? "\033[0m" : '' );
    }

    /**
     * The characters of a UTF-8 text.
     *
     * @param string $text
     * @return string[]
     */
    public static function chars( $text )
    {
        $chars = preg_split( '//u', (string)$text, -1, PREG_SPLIT_NO_EMPTY );
        return is_array( $chars ) ? $chars : str_split( (string)$text );
    }

    /**
     * The width of a text on the terminal: its characters without the ANSI codes.
     *
     * @param string $text
     * @return int
     */
    public static function width( $text )
    {
        return count( self::chars( self::strip( $text ) ) );
    }

    /**
     * A text without its ANSI codes.
     *
     * @param string $text
     * @return string
     */
    public static function strip( $text )
    {
        return (string)preg_replace( '/\033\[[0-9;?]*[A-Za-z]/', '', (string)$text );
    }
}
