<?php
/**
 * The help's addresses and link rewriting (doc/en/us/features/6.0/help-system.md, "Security"), without a database.
 *
 *  HL-01  expHelp::url(): module-relative, each segment encoded, the anchor encoded, the query RFC 3986
 *  HL-02  expHelp::requestedId(): the rest of the address or ?p=, decoded, backslashes as slashes, no array tricks
 *  HL-03  expHelpLinks::resolvePath(): relative to the current page, never above the root (null), no NUL
 *  HL-04  expHelpLinks::link(): javascript:, data:, vbscript:, file: and every other scheme dropped; http(s) and
 *         mailto kept; protocol-relative made https; a relative link to a page that is not served dropped
 *  HL-05  expHelpLinks::image(): https or a served image only; http, data:, protocol-relative dropped
 *  HL-07  expHelpLinks::sizedImage(): a served image with its width and height (the viewer renders with it, so the
 *         page keeps the image's room), an https image its address only; the render cache key carries a version
 *  HL-06  the views' request helpers: search bounds and filters, the asset content types (no SVG, no HTML)
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expHelpLinksTest extends PHPUnit\Framework\TestCase
{
    /** HL-01 */
    public function testUrl()
    {
        $this->assertSame( '/exphelp/dashboard', expHelp::url( 'dashboard' ) );
        $this->assertSame( '/exphelp/viewer/features/6.0/help-system.md', expHelp::url( 'viewer', 'features/6.0/help-system.md' ) );
        $this->assertSame( '/exphelp/viewer/help/a%20b.md#first-step', expHelp::url( 'viewer', '/help/a b.md', 'first-step' ) );
        $this->assertSame( '/exphelp/viewer/x.md#%22%3E%3Cscript%3E', expHelp::url( 'viewer', 'x.md', '"><script>' ) );
        $this->assertSame( '/exphelp/viewer/%22%3E%3Cimg%3E.md', expHelp::url( 'viewer', '"><img>.md' ) );
        $this->assertSame( '/exphelp/search?q=a%20b&format=json', expHelp::url( 'search', '', '', array( 'q' => 'a b', 'format' => 'json' ) ) );
    }

    /** HL-02 */
    public function testRequestedId()
    {
        $this->assertSame( 'features/6.0/help-system.md',
                           expHelp::requestedId( array( 'Parameters' => array( 'features', '6.0', 'help-system.md' ) ), array() ) );
        $this->assertSame( 'help/start.md', expHelp::requestedId( array(), array( 'p' => '/help/start.md/' ) ) );
        $this->assertSame( 'a/b.md', expHelp::requestedId( array(), array( 'p' => 'a\\b.md' ) ) );
        $this->assertSame( '', expHelp::requestedId( array(), array( 'p' => array( 'x' ) ) ), 'an array p is ignored' );
        $this->assertSame( '', expHelp::requestedId( array(), array() ) );
        // the address wins over ?p=
        $this->assertSame( 'one.md', expHelp::requestedId( array( 'Parameters' => array( 'one.md' ) ), array( 'p' => 'two.md' ) ) );
        // decoded here; the index refuses the result (see expHelpDocumentIndexTest)
        $this->assertSame( '../settings/site.ini', expHelp::requestedId( array(), array( 'p' => '%2e%2e/settings/site.ini' ) ) );
    }

    public static function escapingPaths()
    {
        return array(
            array( '../../../settings/site.ini', 'features/6.0/x.md' ),
            array( '../../../etc/passwd', 'features/6.0/x.md' ),
            array( '..', '' ),
            array( '../x.md', 'top.md' ),
            array( '%2e%2e/%2e%2e/x.md', 'a/b.md' ),
            array( '..\\..\\x.md', 'a/b.md' ),
            array( "x.md\0.png", 'a/b.md' ),
            array( '', 'a/b.md' ),
            array( '/', 'a/b.md' ),
        );
    }

    /**
     * HL-03
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'escapingPaths' )]
    public function testResolvePathNeverEscapes( $path, $current )
    {
        $this->assertNull( expHelpLinks::resolvePath( $path, $current ), var_export( $path, true ) );
    }

    public function testResolvePath()
    {
        $this->assertSame( 'features/6.0/y.md', expHelpLinks::resolvePath( 'y.md', 'features/6.0/x.md' ) );
        $this->assertSame( 'features/y.md', expHelpLinks::resolvePath( '../y.md', 'features/6.0/x.md' ) );
        $this->assertSame( 'features/6.0/img/a.png', expHelpLinks::resolvePath( './img/a.png', 'features/6.0/x.md' ) );
        $this->assertSame( 'help/start.md', expHelpLinks::resolvePath( '/doc/en/us/help/start.md', 'features/6.0/x.md' ) );
        $this->assertSame( 'help/start.md', expHelpLinks::resolvePath( '/help/start.md', 'features/6.0/x.md' ) );
        $this->assertSame( 'x.md', expHelpLinks::resolvePath( 'a/../x.md', '' ) );
    }

    public static function droppedLinks()
    {
        return array(
            array( 'javascript:alert(1)' ), array( 'JaVaScRiPt:alert(1)' ), array( '  javascript:alert(1)' ),
            array( 'data:text/html;base64,PHNjcmlwdD5hbGVydCgxKTwvc2NyaXB0Pg==' ), array( 'vbscript:msgbox(1)' ),
            array( 'file:///etc/passwd' ), array( 'ftp://example.com/x' ), array( 'ms-settings:' ),
            array( 'mailto:a@b.c?subject=<script>' ), array( '' ), array( '   ' ),
        );
    }

    /**
     * HL-04
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'droppedLinks' )]
    public function testLinkDropsSchemes( $href )
    {
        $this->assertNull( expHelpLinks::link( $href, 'help/start.md' ), var_export( $href, true ) );
    }

    public function testLinkKeeps()
    {
        $this->assertSame( 'https://github.com/se7enxweb/exponential', expHelpLinks::link( 'https://github.com/se7enxweb/exponential' ) );
        $this->assertSame( 'http://example.com/', expHelpLinks::link( 'http://example.com/' ) );
        $this->assertSame( 'https://example.com/x', expHelpLinks::link( '//example.com/x' ) );
        $this->assertSame( 'mailto:security@example.com', expHelpLinks::link( 'mailto:security@example.com' ) );
        $this->assertSame( '#first-step', expHelpLinks::link( '#first-step' ) );
        $this->assertSame( '#%22%3E%3Cx%3E', expHelpLinks::link( '#"><x>' ) );
    }

    public function testLinkToUnservedPageDropped()
    {
        $this->assertNull( expHelpLinks::link( '../../settings/site.ini', 'features/6.0/x.md' ) );
        $this->assertNull( expHelpLinks::link( '../../../AGENTS.md', 'features/6.0/x.md' ) );
        $this->assertNull( expHelpLinks::link( 'definitely-not-a-page-w612.md', 'features/6.0/x.md' ) );
    }

    /** HL-05 */
    public function testImage()
    {
        $this->assertSame( 'https://example.com/a.png', expHelpLinks::image( 'https://example.com/a.png' ) );
        foreach ( array( 'http://example.com/a.png', '//example.com/a.png', 'data:image/svg+xml;base64,PHN2Zz4=',
                         'javascript:alert(1)', '../../settings/site.ini', '/etc/passwd', '', 'not-there-w612.png' ) as $src )
        {
            $this->assertNull( expHelpLinks::image( $src, 'features/6.0/x.md' ), $src );
        }
    }

    /** HL-07 */
    public function testSizedImageReservesTheRoom()
    {
        $src = '../../images/creators/about-extensions.webp';
        $page = 'help/creators/website-creators/site-packages-and-shipped-themes.md';
        $file = dirname( __DIR__, 5 ) . '/doc/en/us/help/images/creators/about-extensions.webp';
        $this->assertFileExists( $file );
        $size = getimagesize( $file );
        $sized = expHelpLinks::sizedImage( $src, $page );
        $this->assertIsArray( $sized, 'a served image comes with its size' );
        $this->assertSame( expHelpLinks::image( $src, $page ), $sized['url'] );
        $this->assertSame( array( (int)$size[0], (int)$size[1] ), array( $sized['width'], $sized['height'] ) );
        $this->assertSame( 'https://example.com/a.png', expHelpLinks::sizedImage( 'https://example.com/a.png' ), 'an https image: its address only' );
        $this->assertNull( expHelpLinks::sizedImage( '../../settings/site.ini', 'features/6.0/x.md' ) );
        $this->assertNull( expHelpLinks::sizedImage( 'javascript:alert(1)', 'features/6.0/x.md' ) );
        // the viewer renders with it, and a changed rendering is not served from an older cache entry
        $viewer = (string)file_get_contents( dirname( __DIR__, 5 ) . '/kernel/private/classes/views/exphelp/viewer.php' );
        $this->assertStringContainsString( "'image_resolver' => array( 'expHelpLinks', 'sizedImage' )", $viewer );
        $this->assertMatchesRegularExpression( '/const RENDER_VERSION = [2-9]/', $viewer );
        $this->assertStringContainsString( 'sha1( self::RENDER_VERSION . "\0" . $index->signature()', $viewer );
        $html = ( new expHelpMarkdownRenderer( array( 'image_resolver' => array( 'expHelpLinks', 'sizedImage' ) ) ) )->render( '![About](' . $src . ')', $page );
        $this->assertStringContainsString( 'width="' . (int)$size[0] . '" height="' . (int)$size[1] . '"', is_array( $html ) ? $html['html'] : (string)$html );
    }

    /** HL-06 */
    public function testSearchRequestBounds()
    {
        $r = Exponential\View\Kernel\Exphelp\Search::request( array( 'q' => str_repeat( 'a', 500 ), 'limit' => '100000', 'offset' => '-5',
                                                                       'audience' => 'editor', 'category' => '<script>', 'type' => array( 'x' ),
                                                                       'format' => 'json' ) );
        $this->assertSame( 200, mb_strlen( $r['q'] ) );
        $this->assertSame( 50, $r['limit'] );
        $this->assertSame( 0, $r['offset'] );
        $this->assertSame( array( 'audience' => 'editor' ), $r['filters'] );
        $this->assertSame( 'json', $r['format'] );
        $r = Exponential\View\Kernel\Exphelp\Search::request( array( 'q' => array( 'x' ), 'limit' => '0', 'offset' => '99999999', 'format' => 'xml' ) );
        $this->assertSame( '', $r['q'] );
        $this->assertSame( 1, $r['limit'] );
        $this->assertSame( 10000, $r['offset'] );
        $this->assertSame( 'html', $r['format'] );
    }

    public function testAssetContentTypes()
    {
        $type = array( 'Exponential\View\Kernel\Exphelp\Asset', 'contentType' );
        $this->assertSame( 'image/png', $type( 'doc/images/a.PNG' ) );
        $this->assertSame( 'image/jpeg', $type( 'a.jpeg' ) );
        foreach ( array( 'a.svg', 'a.html', 'a.htm', 'a.md', 'a.php', 'a.png.php', 'a' ) as $file )
        {
            $this->assertNull( $type( $file ), $file . ' is never served as an image' );
        }
    }
}
