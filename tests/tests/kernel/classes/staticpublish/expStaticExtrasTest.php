<?php
/**
 * The extras of a static build (expStaticExtras): pure.
 *
 *  EX-01 .nojekyll is empty; CNAME is the domain on one line, anything else is refused
 *  EX-02 robots.txt names the sitemap only with a public address and a sitemap
 *  EX-03 sitemap.xml: absolute addresses below the public address, sorted, lastmod as a date, escaped; none without
 *        a public address
 *  EX-04 the missing path and the plain 404 page
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expStaticExtrasTest extends PHPUnit\Framework\TestCase
{
    /** EX-01 */
    public function testNojekyllAndCname()
    {
        $this->assertSame( '', expStaticExtras::nojekyll() );
        $this->assertSame( "www.example.com\n", expStaticExtras::cname( ' WWW.Example.com ' ) );
        $this->assertSame( "docs.my-site.example.org\n", expStaticExtras::cname( 'docs.my-site.example.org' ) );
        foreach ( array( 'localhost', 'http://example.com', 'example.com/x', '-a.example.com', 'a..b.com', '' ) as $bad )
            $this->assertNull( expStaticExtras::cname( $bad ), $bad );
    }

    /** EX-02 */
    public function testRobots()
    {
        $this->assertSame( "User-agent: *\nAllow: /\n", expStaticExtras::robots( '', true ) );
        $this->assertSame( "User-agent: *\nAllow: /\n", expStaticExtras::robots( 'https://o.github.io/r/', false ) );
        $this->assertSame( "User-agent: *\nAllow: /\n\nSitemap: https://o.github.io/r/sitemap.xml\n", expStaticExtras::robots( 'https://o.github.io/r', true ) );
    }

    /** EX-03 */
    public function testSitemap()
    {
        $this->assertNull( expStaticExtras::sitemap( '', array( '' => null ) ) );
        $this->assertNull( expStaticExtras::sitemap( '/relative/', array( '' => null ) ) );
        $xml = expStaticExtras::sitemap( 'https://o.github.io/r', array( 'b/' => 1700000000, '' => null, 'a&b/' => 0 ) );
        $this->assertSame( "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n"
                           . "  <url><loc>https://o.github.io/r/</loc></url>\n"
                           . "  <url><loc>https://o.github.io/r/a&amp;b/</loc></url>\n"
                           . "  <url><loc>https://o.github.io/r/b/</loc><lastmod>2023-11-14</lastmod></url>\n"
                           . "</urlset>\n", $xml );
        $this->assertNotFalse( simplexml_load_string( (string)$xml ) );
    }

    /** EX-04 */
    public function testNotFound()
    {
        $this->assertSame( '/static-publish-not-found-20261101-120000-abc123', expStaticExtras::missingPath( '20261101-120000-ABC123' ) );
        $this->assertSame( '/static-publish-not-found-xy', expStaticExtras::missingPath( 'x/../<y>' ), 'nothing but letters, digits and -' );
        $page = expStaticExtras::fallbackNotFound( '/repo/' );
        $this->assertStringContainsString( '<a href="/repo/">', $page );
        $this->assertStringContainsString( '<title>Page not found</title>', $page );
    }
}
