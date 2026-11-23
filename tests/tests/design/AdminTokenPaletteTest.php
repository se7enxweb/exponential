<?php
/**
 * The administration's colours come from one palette: every colour in admin4's stylesheets and in the classic
 * admin's copy of the tokens (design/admin/stylesheets/components.css) is a colour of Tailwind CSS 3.4's default
 * palette (fixtures/tailwind-palette-3.4.17.json), white, black or the logo's orange; a translucent rgba() takes
 * the red, green and blue of one of them. The grey ramp is slate only. The logo's orange is the fill of the logo
 * artwork (parts/a4_logo.tpl) and of --a4-brand and --a4-accent. Whether the colours reach their contrast is
 * AdminTokenContrastTest's part.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminTokenPaletteTest.php
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminFiles.php';

class AdminTokenPaletteTest extends PHPUnit\Framework\TestCase
{
    const LOGO = '#f15a28';

    /** @var array colour (#rrggbb) => name, e.g. '#1e293b' => 'slate-800' */
    private static $palette;

    public static function setUpBeforeClass(): void
    {
        $fixture = json_decode( AdminFiles::read( __DIR__ . '/fixtures/tailwind-palette-3.4.17.json' ), true );
        self::$palette = array();
        foreach ( $fixture['colors'] as $family => $steps )
        {
            foreach ( $steps as $step => $hex )
            {
                self::$palette[strtolower( $hex )] = "$family-$step";
            }
        }
        self::$palette['#ffffff'] = 'white';
        self::$palette['#000000'] = 'black';
        self::$palette[self::LOGO] = 'logo';
    }

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    /** @return string[] the stylesheets whose colours are checked */
    private static function stylesheets()
    {
        $root = self::root();
        return array_merge(
            AdminFiles::files( "$root/design/admin4/stylesheets/*.css" ),
            AdminFiles::files( "$root/design/admin4/stylesheets/*/*.css" ),
            array( "$root/design/admin/stylesheets/components.css" )
        );
    }

    /**
     * Returns every colour literal of $css outside comments as #rrggbb (rgb()/rgba() without the alpha), also the
     * URL-encoded ones of an inline SVG (stroke='%2364748b'); a selector's id (#feed-elements) is not a colour, only
     * a value after a colon is looked at. Named colours, hsl() and the space syntax of rgb() are not recognised:
     * the stylesheets use none of them
     */
    public static function colours( $css )
    {
        $css = preg_replace( '#/\*.*?\*/#s', '', $css );
        $found = array();
        preg_match_all( '/:([^;{}]*)/', $css, $values );
        foreach ( $values[1] as $value )
        {
            // #rgb ... #rrggbbaa, the same URL-encoded in a data: SVG (%23rrggbb), and rgb()/rgba() with commas
            preg_match_all( '/(?:#|%23)([0-9a-f]{8}|[0-9a-f]{6}|[0-9a-f]{3,4})\b|rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/i', $value, $m, PREG_SET_ORDER );
            foreach ( $m as $hit )
            {
                if ( isset( $hit[2] ) && $hit[2] !== '' )
                {
                    $found[] = sprintf( '#%02x%02x%02x', $hit[2], $hit[3], $hit[4] );
                    continue;
                }
                $hex = strtolower( $hit[1] );
                if ( strlen( $hex ) <= 4 )
                {
                    $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
                }
                $found[] = '#' . substr( $hex, 0, 6 );
            }
        }
        return $found;
    }

    public function testTheScanFindsColoursAndSkipsSelectors()
    {
        $this->assertSame( array( '#1e293b', '#0f172a', '#ffffff' ),
                           self::colours( "div#feed-elements { color: #1e293b; box-shadow: 0 1px rgba(15, 23, 42, 0.1); }\n/* #123456 */ a { background: #fff; }" ) );
        $this->assertSame( array( '#64748b' ), self::colours( "b { background: url(\"data:image/svg+xml,%3Cpath stroke='%2364748b'/%3E\"); }" ) );
        $this->assertSame( 'slate-800', self::$palette['#1e293b'] );
        $this->assertSame( 'orange-700', self::$palette['#c2410c'] );
        $fixture = json_decode( AdminFiles::read( __DIR__ . '/fixtures/tailwind-palette-3.4.17.json' ), true );
        $this->assertCount( 22, $fixture['colors'], 'the 22 colour families' );
        $this->assertSame( 242, array_sum( array_map( 'count', $fixture['colors'] ) ), '11 steps each' );
    }

    public function testEveryColourIsAPaletteColour()
    {
        $failures = array();
        $checked = 0;
        foreach ( self::stylesheets() as $file )
        {
            foreach ( array_unique( self::colours( AdminFiles::read( $file ) ) ) as $colour )
            {
                $checked++;
                if ( !isset( self::$palette[$colour] ) )
                {
                    $failures[] = substr( $file, strlen( self::root() ) + 1 ) . ": $colour";
                }
            }
        }
        $this->assertGreaterThan( 50, $checked, 'the scan found the tokens' );
        $this->assertSame( array(), $failures, 'take a Tailwind 3.4 colour (tests/tests/design/fixtures/tailwind-palette-3.4.17.json), white, black or the logo orange' );
    }

    public function testTheGreysAreSlate()
    {
        $failures = array();
        foreach ( self::stylesheets() as $file )
        {
            foreach ( array_unique( self::colours( AdminFiles::read( $file ) ) ) as $colour )
            {
                $name = isset( self::$palette[$colour] ) ? self::$palette[$colour] : '';
                if ( preg_match( '/^(gray|zinc|neutral|stone)-/', $name ) )
                {
                    $failures[] = substr( $file, strlen( self::root() ) + 1 ) . ": $colour ($name)";
                }
            }
        }
        $this->assertSame( array(), $failures, 'the grey ramp is slate' );
    }

    public function testTheBrandAndTheAccentAreTheLogosOrange()
    {
        $root = self::root();
        $logo = AdminFiles::read( "$root/design/admin4/templates/parts/a4_logo.tpl" );
        preg_match_all( '/class="a4-logo-brand" fill="(#[0-9a-f]{6})"/i', $logo, $fills );
        $this->assertNotEmpty( $fills[1], 'the logo has its orange square' );
        $this->assertSame( array( self::LOGO ), array_values( array_unique( array_map( 'strtolower', $fills[1] ) ) ) );
        $frame = preg_replace( '#/\*.*?\*/#s', '', AdminFiles::read( "$root/design/admin4/stylesheets/admin4.css" ) );
        $components = preg_replace( '#/\*.*?\*/#s', '', AdminFiles::read( "$root/design/admin4/stylesheets/components.css" ) );
        preg_match_all( '/--a4-brand:\s*([^;]+);/', $frame, $brand );
        preg_match_all( '/--a4-accent:\s*([^;]+);/', $components, $accent );
        $this->assertNotEmpty( $brand[1] );
        $this->assertNotEmpty( $accent[1] );
        foreach ( array_merge( $brand[1], $accent[1] ) as $value )
        {
            $this->assertSame( self::LOGO, strtolower( trim( $value ) ) );
        }
    }
}
