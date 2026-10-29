<?php
/**
 * The help shows an image of another site that the admin's Content Security Policy does not allow as a link, not as
 * an <img>, so a page such as the project file README.md (its screenshots are on github.com/user-attachments) makes
 * no request the policy blocks and logs no console error. No database: the policy's sources are given, or read from
 * a stand-in site.ini.
 *
 *  HEI-01  a blocked external image becomes a.a4-help-imglink "Image on <host>: <alt>", target _blank, noopener
 *  HEI-02  a local image (an asset of the documentation) stays an <img>
 *  HEI-03  an external image whose host the policy allows stays an <img>; without image_sources every https image does
 *  HEI-04  README.md under the shipped img-src has no <img src="https://...">
 *  HEI-05  expContentSecurityPolicy::imageSources() and sourcesAllowUrl() read img-src the way a browser applies it
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expHelpExternalImagesTest extends PHPUnit\Framework\TestCase
{
    /** the shipped img-src of settings/site.ini */
    const SELF_ONLY = array( "'self'", 'data:', 'blob:' );

    private function renderer( $imageSources )
    {
        return new expHelpMarkdownRenderer( array(
            'link_resolver' => static function ( $href ) { return preg_match( '#^https?://#i', $href ) ? $href : null; },
            'image_resolver' => static function ( $src, $currentId ) {
                $id = expHelpLinks::resolvePath( (string)$src, $currentId );
                return $id === 'help/img/ok.png' ? '/admin/exphelp/asset/' . $id : null;
            },
            'translate' => static function ( $text ) { return $text; },
            'image_sources' => $imageSources ) );
    }

    /** @return DOMElement[] */
    private static function elements( $html, $tag )
    {
        $dom = new DOMDocument();
        $prev = libxml_use_internal_errors( true );
        $dom->loadHTML( '<?xml encoding="UTF-8"><html><body>' . $html . '</body></html>', LIBXML_NONET );
        libxml_clear_errors();
        libxml_use_internal_errors( $prev );
        return iterator_to_array( $dom->getElementsByTagName( $tag ) );
    }

    private static function externalImgSrcs( $html )
    {
        $srcs = array();
        foreach ( self::elements( $html, 'img' ) as $img )
        {
            if ( preg_match( '#^(https?:)?//#i', $img->getAttribute( 'src' ) ) )
                $srcs[] = $img->getAttribute( 'src' );
        }
        return $srcs;
    }

    /** HEI-01 */
    public function testBlockedExternalImageBecomesALink()
    {
        $md = "Before\n\n![Admin dashboard](https://github.com/user-attachments/assets/abc-123)\n\n"
            . "Inline <img src=\"https://example.com/b.png\" alt=\"Badge\"> and ![](https://cdn.example.net/c.png)\n";
        $r = $this->renderer( self::SELF_ONLY )->render( $md, 'help/page.md' );
        $this->assertSame( array(), self::externalImgSrcs( $r['html'] ), 'no external <img> under img-src \'self\'' );
        $links = array();
        foreach ( self::elements( $r['html'], 'a' ) as $a )
        {
            if ( strpos( ' ' . $a->getAttribute( 'class' ) . ' ', ' a4-help-imglink ' ) !== false )
                $links[$a->getAttribute( 'href' )] = $a;
        }
        $this->assertCount( 3, $links );
        $gh = $links['https://github.com/user-attachments/assets/abc-123'];
        $this->assertSame( 'Image on GitHub: Admin dashboard', trim( $gh->textContent ) );
        $this->assertSame( '_blank', $gh->getAttribute( 'target' ) );
        $this->assertSame( 'noopener noreferrer', $gh->getAttribute( 'rel' ) );
        $this->assertStringContainsString( 'a4-help-ext', $gh->getAttribute( 'class' ) );
        $this->assertSame( 'Image on example.com: Badge', trim( $links['https://example.com/b.png']->textContent ) );
        $this->assertSame( 'Image on cdn.example.net', trim( $links['https://cdn.example.net/c.png']->textContent ), 'no alt: the label alone' );
        $this->assertStringNotContainsString( 'style=', $r['html'] );
    }

    /** HEI-02 */
    public function testLocalImageStaysAnImage()
    {
        $r = $this->renderer( self::SELF_ONLY )->render( "![A screenshot](img/ok.png)\n", 'help/page.md' );
        $imgs = self::elements( $r['html'], 'img' );
        $this->assertCount( 1, $imgs );
        $this->assertSame( '/admin/exphelp/asset/help/img/ok.png', $imgs[0]->getAttribute( 'src' ) );
        $this->assertSame( 'A screenshot', $imgs[0]->getAttribute( 'alt' ) );
        $this->assertCount( 1, self::elements( $r['html'], 'figure' ), 'a lone local image is still a figure' );
    }

    /** HEI-03 */
    public function testAllowedHostsStayImages()
    {
        $md = "![a](https://images.example.org/a.png) ![b](https://x.cdn.example.net/b.png) ![c](https://other.example.com/c.png)\n";
        $r = $this->renderer( array_merge( self::SELF_ONLY, array( 'images.example.org', 'https://*.cdn.example.net' ) ) )->render( $md, 'help/page.md' );
        $this->assertSame( array( 'https://images.example.org/a.png', 'https://x.cdn.example.net/b.png' ), self::externalImgSrcs( $r['html'] ) );
        $this->assertStringContainsString( 'href="https://other.example.com/c.png"', $r['html'] );

        $r = $this->renderer( null )->render( $md, 'help/page.md' );
        $this->assertCount( 3, self::externalImgSrcs( $r['html'] ), 'no policy given: every https image is an <img>' );
    }

    /** HEI-04 */
    public function testReadmeUnderTheShippedPolicy()
    {
        $root = dirname( __DIR__, 5 );
        $siteIni = (string)file_get_contents( $root . '/settings/site.ini' );
        $this->assertSame( 1, preg_match( '/^Directives\[img-src\]=(.*)$/m', $siteIni, $m ), 'site.ini names img-src' );
        $sources = preg_split( '/\s+/', trim( $m[1] ) );
        $readme = $root . '/README.md';
        if ( !is_file( $readme ) )
            $this->markTestSkipped( 'no README.md' );
        $markdown = (string)file_get_contents( $readme );
        $r = $this->renderer( $sources )->render( $markdown, 'README.md' );
        $this->assertSame( array(), self::externalImgSrcs( $r['html'] ), 'README.md renders no blocked <img>' );
        $this->assertDoesNotMatchRegularExpression( '#<img[^>]+src="https://#i', $r['html'] );
        if ( preg_match( '#!\[[^\]]*\]\(https://github\.com/user-attachments/#', $markdown ) )
            $this->assertStringContainsString( 'a4-help-imglink', $r['html'], 'its GitHub images are links' );
    }

    /** HEI-05 */
    public function testPolicySources()
    {
        $values = array(
            'Mode' => 'report-only',
            'Directives' => array( 'default-src' => "'self'", 'img-src' => "'self' data: blob:" ),
            'AdditionalSources' => array( 'img-src images.example.org' ),
        );
        $ini = $this->createMock( 'eZINI' );
        $ini->method( 'hasVariable' )->willReturnCallback( static function ( $section, $name ) use ( &$values ) {
            return $section === 'ContentSecurityPolicy' && array_key_exists( $name, $values );
        } );
        $ini->method( 'variable' )->willReturnCallback( static function ( $section, $name ) use ( &$values ) {
            return $values[$name];
        } );
        $this->assertSame( array( "'self'", 'data:', 'blob:', 'images.example.org' ), expContentSecurityPolicy::imageSources( $ini ),
                           'report-only counts: the browser still logs what it would block' );
        unset( $values['Directives']['img-src'] );
        $this->assertSame( array( "'self'" ), expContentSecurityPolicy::imageSources( $ini ), 'no img-src: default-src' );
        $values['Mode'] = 'disabled';
        $this->assertNull( expContentSecurityPolicy::imageSources( $ini ), 'no policy: any image' );

        $allow = static function ( array $sources, $url ) { return expContentSecurityPolicy::sourcesAllowUrl( $sources, $url ); };
        $this->assertFalse( $allow( self::SELF_ONLY, 'https://github.com/user-attachments/assets/x' ) );
        $this->assertTrue( $allow( array( '*' ), 'https://github.com/a.png' ) );
        $this->assertTrue( $allow( array( 'https:' ), 'https://github.com/a.png' ) );
        $this->assertTrue( $allow( array( 'github.com' ), 'https://github.com/a.png' ) );
        $this->assertFalse( $allow( array( 'github.com' ), 'https://evilgithub.com/a.png' ) );
        $this->assertTrue( $allow( array( '*.githubusercontent.com' ), 'https://user-images.githubusercontent.com/a.png' ) );
        $this->assertFalse( $allow( array( '*.githubusercontent.com' ), 'https://githubusercontent.com/a.png' ) );
        $this->assertFalse( $allow( array( 'github.com:8443' ), 'https://github.com/a.png' ) );
        $this->assertTrue( $allow( array( 'https://github.com/user-attachments/' ), 'https://github.com/user-attachments/assets/x' ) );
        $this->assertFalse( $allow( array( 'https://github.com/user-attachments/' ), 'https://github.com/other/x' ) );
        $this->assertFalse( $allow( array( "'none'", "'unsafe-inline'", 'data:' ), 'https://github.com/a.png' ) );
    }
}
