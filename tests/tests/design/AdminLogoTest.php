<?php
/**
 * admin4's logo, checked on the files alone (no database):
 *
 *  LG-01 - one source: the header (admin4's page_topmenu.tpl, admin4l's admin_logo block) and the sign-in page include
 *          parts/a4_logo.tpl; no other template draws the logo, and no stylesheet rule of the header or the sign-in
 *          page still paints the old images (logo-light.png, admin3's sprite)
 *  LG-02 - the name: the links around the logo carry it (aria-label), the inline drawings are hidden from assistive
 *          technology; the image files name themselves (role="img", aria-label, <title>)
 *  LG-03 - the SVG is plain drawing: well-formed, no script, no event handler, no style element, no link or other
 *          reference outside the file
 *  LG-04 - the inline drawings and the image files (images/admin4/exponential-logo.svg, exponential-mark.svg) are the
 *          same drawing, path for path
 *  LG-05 - the colors: letters and outlines in currentColor, the orange square in the brand color #f15a28, the inner
 *          square in the surface color; under forced colors CanvasText and Canvas
 *  LG-06 - the header keeps its heights (56px, 88px from 576px, 56px from 1280px) and the logo box its 32px; the logo is
 *          24px high, and below 576px the mark stands alone in a 36px link, as large a target as the tools
 *  LG-07 - the word stands as high as the mark: the E shares the back square's top and the front square's baseline
 *  LG-08 - the raster logos are rendered from this drawing at twice the size they are drawn at (a device pixel ratio of
 *          2): logo-light.png and setup's Exponential-logo-title.png 400 x 100 (the same file), the standard login
 *          page's Exponential-logo.gif 420 x 120; small files
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminLogoTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminBaseStylesheetsTest.php';

class AdminLogoTest extends PHPUnit\Framework\TestCase
{
    const PARTIAL = 'design/admin4/templates/parts/a4_logo.tpl';
    const INCLUDE = "{include uri='design:parts/a4_logo.tpl'}";

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

    /** The partial without its template comment: the markup it outputs */
    private static function partial()
    {
        return trim( preg_replace( '/\{\*.*?\*\}/s', '', self::file( self::PARTIAL ) ) );
    }

    /** The <svg> elements of $markup as DOM elements, keyed by their position */
    private static function svgs( $markup )
    {
        $doc = new DOMDocument();
        $ok = $doc->loadXML( '<root>' . $markup . '</root>' );
        return $ok ? iterator_to_array( $doc->getElementsByTagName( 'svg' ) ) : array();
    }

    /** The drawing of an <svg>: one line per shape, tag and attributes, in order */
    private static function drawing( DOMElement $svg )
    {
        $shapes = array();
        foreach ( $svg->getElementsByTagName( '*' ) as $node )
        {
            if ( !in_array( $node->nodeName, array( 'path', 'rect' ), true ) )
                continue;
            $fill = $node->getAttribute( 'fill' ) !== '' ? $node->getAttribute( 'fill' ) : $node->parentNode->getAttribute( 'fill' );
            $shape = $node->nodeName . ' fill=' . $fill;
            foreach ( array( 'd', 'x', 'y', 'width', 'height' ) as $name )
            {
                if ( $node->hasAttribute( $name ) )
                    $shape .= " $name=" . $node->getAttribute( $name );
            }
            $shapes[] = $shape;
        }
        return $shapes;
    }

    /** LG-01 */
    public function testEveryLogoComesFromThePartial()
    {
        $places = array( 'design/admin4/templates/page_topmenu.tpl' => 2,
                         'design/admin4l/templates/explayouts/block/admin_logo.tpl' => 2,
                         'design/admin4/templates/loginpagelayout.tpl' => 1 );
        foreach ( $places as $path => $count )
            $this->assertSame( $count, substr_count( self::file( $path ), self::INCLUDE ), $path );
        // the include sits inside the logo links
        $this->assertSame( 2, substr_count( self::file( 'design/admin4/templates/page_topmenu.tpl' ),
            'aria-label="{$a4_brand_name|wash}">' . self::INCLUDE . '</a>' ) );
        $this->assertSame( 2, substr_count( self::file( 'design/admin4l/templates/explayouts/block/admin_logo.tpl' ),
            'aria-label="{$a4_brand_name|wash}">' . self::INCLUDE . '</a>' ) );
        $this->assertStringContainsString( '<a class="a4-login-logo" href={\'/\'|ezurl} aria-label="Exponential">' . self::INCLUDE . '</a>',
            self::file( 'design/admin4/templates/loginpagelayout.tpl' ) );

        // no other template of admin4 or admin4l draws it
        foreach ( array( 'design/admin4/templates', 'design/admin4/override/templates', 'design/admin4l/templates' ) as $dir )
        {
            $files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( self::root() . '/' . $dir, FilesystemIterator::SKIP_DOTS ) );
            foreach ( $files as $file )
            {
                $path = substr( $file->getPathname(), strlen( self::root() ) + 1 );
                if ( $path !== self::PARTIAL && substr( $path, -4 ) === '.tpl' )
                    $this->assertStringNotContainsString( 'class="a4-logo', (string)file_get_contents( $file->getPathname() ), $path );
            }
        }

        // the header and the sign-in page paint no logo image any more
        $css = self::css( 'admin4.css' );
        $this->assertStringNotContainsString( 'logo-light.png', $css );
        $this->assertStringNotContainsString( 'logo_separator_sprite.png', $css );
        $this->assertStringNotContainsString( '--a4-logo', $css );
        $rules = AdminBaseStylesheetsTest::rules( $css );
        $this->assertSame( 'none', $rules['#header-logo .brand']['background'], 'admin3\'s sprite in pagelayout.css is switched off' );
    }

    /** LG-02 */
    public function testTheLinksCarryTheName()
    {
        $svgs = self::svgs( self::partial() );
        $this->assertCount( 2, $svgs, 'the full logo and the mark' );
        foreach ( $svgs as $svg )
        {
            $this->assertSame( 'true', $svg->getAttribute( 'aria-hidden' ) );
            $this->assertSame( 'false', $svg->getAttribute( 'focusable' ) );
            $this->assertFalse( $svg->hasAttribute( 'role' ) );
        }
        $this->assertSame( 'a4-logo a4-logo-full', $svgs[0]->getAttribute( 'class' ) );
        $this->assertSame( 'a4-logo a4-logo-mark', $svgs[1]->getAttribute( 'class' ) );
        // the partial outputs the drawings and nothing else (no text node, no own link)
        $this->assertStringStartsWith( '<svg ', self::partial() );
        $this->assertStringEndsWith( '</svg>', self::partial() );
        $this->assertStringNotContainsString( '<a ', self::partial() );

        foreach ( array( 'exponential-logo.svg', 'exponential-mark.svg' ) as $name )
        {
            $svg = self::svgs( self::file( 'design/admin4/images/admin4/' . $name ) )[0];
            $this->assertSame( 'img', $svg->getAttribute( 'role' ), $name );
            $this->assertSame( 'Exponential', $svg->getAttribute( 'aria-label' ), $name );
            $this->assertSame( 'Exponential', $svg->getElementsByTagName( 'title' )->item( 0 )->textContent, $name );
        }
    }

    /** LG-03 */
    public function testTheSvgIsPlainDrawing()
    {
        $sources = array( self::PARTIAL => self::partial() );
        foreach ( array( 'exponential-logo.svg', 'exponential-mark.svg' ) as $name )
            $sources[$name] = self::file( 'design/admin4/images/admin4/' . $name );
        foreach ( $sources as $name => $markup )
        {
            $this->assertNotEmpty( self::svgs( $markup ), "$name is well-formed" );
            $this->assertDoesNotMatchRegularExpression( '/<(script|style|foreignObject|image|use|a|iframe|object|embed|animate\w*|set|filter|mpath)\b/i', $markup, $name );
            $this->assertDoesNotMatchRegularExpression( '/\son\w+\s*=/i', $markup, "$name: no event handler" );
            $this->assertDoesNotMatchRegularExpression( '/\b(href|src)\s*=|url\(|javascript:|data:|@import/i', $markup, "$name: no reference" );
            $this->assertStringNotContainsString( 'http://', str_replace( 'xmlns="http://www.w3.org/2000/svg"', '', $markup ), "$name: no address but the SVG namespace" );
            $this->assertStringNotContainsString( 'https://', $markup, $name );
            $this->assertStringNotContainsString( 'class="cls-', $markup, "$name: the artwork's classes are attributes now" );
        }
        $this->assertStringNotContainsString( '{', self::partial(), 'the partial has no template code inside the drawing' );
    }

    /** LG-04 */
    public function testTheInlineLogoIsTheImageFile()
    {
        $inline = self::svgs( self::partial() );
        $full = self::svgs( self::file( 'design/admin4/images/admin4/exponential-logo.svg' ) )[0];
        $mark = self::svgs( self::file( 'design/admin4/images/admin4/exponential-mark.svg' ) )[0];

        $this->assertSame( self::drawing( $full ), self::drawing( $inline[0] ) );
        $this->assertSame( self::drawing( $mark ), self::drawing( $inline[1] ) );
        $this->assertSame( $full->getAttribute( 'viewBox' ), $inline[0]->getAttribute( 'viewBox' ) );
        $this->assertSame( $mark->getAttribute( 'viewBox' ), $inline[1]->getAttribute( 'viewBox' ) );
        $this->assertSame( '6 6 380 60', $full->getAttribute( 'viewBox' ), 'cut to the drawing, no white margin' );
        // the mark is the end of the full logo's drawing: the word, then the mark
        $this->assertSame( self::drawing( $mark ), array_slice( self::drawing( $full ), -4 ) );
        $this->assertCount( 11 + 4, self::drawing( $full ), 'eleven letters, two squares with their outlines' );
        // no background: the artwork's white rectangle over the whole area is gone
        $this->assertStringNotContainsString( 'width="400"', self::file( 'design/admin4/images/admin4/exponential-logo.svg' ) );
    }

    /** LG-05 */
    public function testTheColorsFollowTheTheme()
    {
        $inline = self::svgs( self::partial() );
        $fills = array_map( function ( $shape ) { return explode( ' ', $shape )[1]; }, self::drawing( $inline[0] ) );
        $this->assertSame( array_merge( array_fill( 0, 11, 'fill=currentColor' ),
            array( 'fill=#fff', 'fill=currentColor', 'fill=#f15a28', 'fill=currentColor' ) ), $fills );
        // the inner square: the surface color on the page, white as a fallback and in the image files
        $this->assertSame( 2, substr_count( self::partial(), '<rect class="a4-logo-paper" fill="#fff"' ) );
        foreach ( array( 'exponential-logo.svg', 'exponential-mark.svg' ) as $name )
            $this->assertStringContainsString( 'color="#231f20"', self::file( 'design/admin4/images/admin4/' . $name ), "$name: the artwork's ink where nothing sets a color" );
        $this->assertStringNotContainsString( 'color="', preg_replace( '/\s(fill)="[^"]*"/', '', self::partial() ), 'inline, the link sets the color' );

        $css = self::css( 'admin4.css' );
        $rules = AdminBaseStylesheetsTest::rules( $css );
        $this->assertSame( 'var(--a4-surface)', $rules['.a4-logo-paper']['fill'] );
        // the orange square takes the brand color from its token (the attribute is the fallback), light and dark
        $this->assertSame( 2, substr_count( self::partial(), '<rect class="a4-logo-brand" fill="#f15a28"' ) );
        $this->assertSame( 'var(--a4-brand)', $rules['.a4-logo-brand']['fill'] );
        $this->assertSame( 2, preg_match_all( '/^\s*--a4-brand: #f15a28;/m', $css ), 'a light and a dark value' );
        $this->assertSame( 'var(--a4-ink)', $rules['#header-logo .brand']['color'] );
        $this->assertSame( 'var(--a4-ink)', $rules['.a4-login-logo']['color'] );
        $forced = AdminBaseStylesheetsTest::rules( $css, true );
        $this->assertSame( 'CanvasText', $forced['#header-logo .brand, .a4-login-logo']['color'] );
        $this->assertSame( 'Canvas', $forced['.a4-logo-paper']['fill'] );
    }

    /** LG-06 */
    public function testTheHeaderKeepsItsHeights()
    {
        $css = self::css( 'admin4.css' );
        $this->assertStringContainsString( ':root { --header-height: 56px; }', $css );
        $this->assertStringContainsString( ':root { --header-height: 88px; }', self::media( $css, 'screen and (min-width: 576px)' ) );
        $this->assertStringContainsString( ':root { --header-height: 56px; }', self::media( $css, 'screen and (min-width: 1280px)' ) );

        $rules = AdminBaseStylesheetsTest::rules( preg_replace( '/@media[^{]*\{((?:[^{}]*\{[^{}]*\})*)[^{}]*\}/s', '', $css ) );
        $this->assertSame( '32px', $rules['#header-logo .brand']['height'], 'the box of the old logo image' );
        $this->assertSame( '24px', $rules['.a4-logo-full']['height'] );
        $this->assertSame( 'calc(24px * 380 / 60)', $rules['.a4-logo-full']['width'], 'the viewBox\'s ratio' );
        $this->assertSame( 'none', $rules['.a4-logo-mark']['display'] );
        $this->assertSame( 'calc(28px * 380 / 60)', $rules['.a4-login-logo .a4-logo-full']['width'] );

        $phone = AdminBaseStylesheetsTest::rules( self::media( $css, 'screen and (max-width: 575.98px)' ) );
        $this->assertSame( 'none', $phone['#header-logo .a4-logo-full']['display'] );
        $this->assertSame( 'block', $phone['#header-logo .a4-logo-mark']['display'] );
        $this->assertSame( '36px', $phone['#header-logo .brand']['min-width'], 'the mark\'s link as large a target as the tools' );
        $this->assertSame( '36px', $phone['#header-logo .brand']['height'] );
        $tablet = AdminBaseStylesheetsTest::rules( self::media( $css, 'screen and (min-width: 576px)' ) );
        $this->assertSame( 'clamp(6rem, calc(100vw - 454px), 230px)', $tablet['#header .searchblock input.searchtext']['width'],
            'the field gives the wider logo its 44px, so the tools keep one row at 576px' );
    }

    /** The smallest and largest y of a path's absolute coordinates (the letters and squares use M, V, H, L, Q, Z) */
    private static function verticalExtent( $d )
    {
        $ys = array();
        preg_match_all( '/([MLQVHZmlqvhz])([^MLQVHZmlqvhz]*)/', $d, $m, PREG_SET_ORDER );
        foreach ( $m as $command )
        {
            $numbers = array_map( 'floatval', preg_split( '/[\s,]+/', trim( $command[2] ), -1, PREG_SPLIT_NO_EMPTY ) );
            if ( $command[1] === 'V' )
                $ys = array_merge( $ys, $numbers );
            elseif ( in_array( $command[1], array( 'M', 'L', 'Q' ), true ) )
            {
                for ( $i = 1; $i < count( $numbers ); $i += 2 )
                    $ys[] = $numbers[$i];
            }
        }
        return array( min( $ys ), max( $ys ) );
    }

    /** LG-07 */
    public function testTheWordIsAsHighAsTheMark()
    {
        $full = self::svgs( self::file( 'design/admin4/images/admin4/exponential-logo.svg' ) )[0];
        $paths = $full->getElementsByTagName( 'path' );
        $e = self::verticalExtent( $paths->item( 0 )->getAttribute( 'd' ) );
        $l = self::verticalExtent( $paths->item( 10 )->getAttribute( 'd' ) );
        $rects = $full->getElementsByTagName( 'rect' );
        $back = (float)$rects->item( 0 )->getAttribute( 'y' ) - 2;   // the outline is 2 units outside the paper
        $front = (float)$rects->item( 1 )->getAttribute( 'y' ) + (float)$rects->item( 1 )->getAttribute( 'height' ) + 2;
        $this->assertEqualsWithDelta( $back, $e[0], 0.01, 'the E starts at the top of the back square' );
        $this->assertEqualsWithDelta( $front, $e[1], 0.01, 'the E stands on the baseline of the front square' );
        $this->assertEqualsWithDelta( $e[0], $l[0], 0.01, 'the l as high as the E' );
        $this->assertEqualsWithDelta( $e[1], $l[1], 0.01 );
    }

    /** LG-08 */
    public function testTheRasterLogosAreRenderedForTwoDevicePixels()
    {
        $light = 'design/admin4/images/admin4/logo-light.png';
        $this->assertSame( array( 400, 100 ), array_slice( getimagesize( self::root() . '/' . $light ), 0, 2 ) );
        $this->assertSame( md5( self::file( $light ) ), md5( self::file( 'design/standard/images/setup/2/Exponential-logo-title.png' ) ),
            'the setup wizard shows the same file' );
        // drawn at half its size where it is used
        $this->assertStringContainsString( 'logo-light.png" alt="Exponential" width="200" height="50"', self::file( 'lib/ezutils/classes/ezexecution.php' ) );
        $this->assertMatchesRegularExpression( '#logo-light\.png[^;]*;\s*background-size: auto 50px;#', self::file( 'design/admin/stylesheets/pagelayout.css' ) );

        $gif = 'design/standard/images/Exponential-logo.gif';
        $this->assertSame( array( 420, 120 ), array_slice( getimagesize( self::root() . '/' . $gif ), 0, 2 ) );
        $this->assertStringContainsString( '{"Exponential-logo.gif"|ezimage} width="210" height="60"', self::file( 'design/standard/templates/loginpagelayout.tpl' ) );

        foreach ( array( $light, $gif ) as $path )
            $this->assertLessThan( 10 * 1024, strlen( self::file( $path ) ), "$path: compressed" );
    }
}
