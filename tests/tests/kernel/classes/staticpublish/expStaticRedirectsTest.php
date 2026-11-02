<?php
/**
 * The redirect stubs of a static copy (expStaticRedirects): pure, with rows as ezurlalias_ml and ezurlwildcard hold
 * them.
 *
 *  RD-01 the full path of every alias row, through its parents (the current element of a parent first)
 *  RD-02 history and custom aliases of the nodes the build holds become stubs; current addresses, nodes not built,
 *        a file the build has and a module action do not
 *  RD-03 wildcards without * to a page of the build; others not
 *  RD-04 a stub page: meta refresh, canonical, noindex, escaped
 *  RD-05 a redirect the crawl met: where its chain ends, a loop, a chain too long, no Location; a Location header
 *        made absolute
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expStaticRedirectsTest extends PHPUnit\Framework\TestCase
{
    private static function row( $id, $parent, $text, $action, $original = 1, $alias = 0 )
    {
        return array( 'id' => $id, 'parent' => $parent, 'lang_mask' => 3, 'text' => $text, 'action' => $action, 'is_original' => $original, 'is_alias' => $alias );
    }

    private static function rows()
    {
        return array( 'aliases' => array(
            self::row( 1, 0, 'news', 'eznode:60' ),
            self::row( 2, 1, 'item-1', 'eznode:61' ),
            self::row( 3, 1, 'old-item', 'eznode:61', 0 ),           // history: renamed
            self::row( 4, 0, 'old-news', 'eznode:60', 0 ),           // history of the folder
            self::row( 5, 4, 'item-0', 'eznode:62', 0 ),             // history below a history element
            self::row( 6, 0, 'promo', 'eznode:61', 1, 1 ),           // custom alias
            self::row( 7, 0, 'gone', 'eznode:99', 0 ),               // a node not built
            self::row( 8, 0, 'login', 'module:user/login', 1, 1 ),   // not a node
            self::row( 9, 0, 'about-us', 'eznode:63', 0 ),           // the build has a page there
            self::row( 10, 77, 'orphan', 'eznode:61', 0 ),           // its parent is missing
        ), 'wildcards' => array(
            array( 'source_url' => 'go/news', 'destination_url' => 'news' ),
            array( 'source_url' => 'go/*', 'destination_url' => 'news/{1}' ),
            array( 'source_url' => 'go/elsewhere', 'destination_url' => 'not-built' ),
        ) );
    }

    /** RD-01 */
    public function testPaths()
    {
        $paths = expStaticRedirects::paths( self::rows()['aliases'] );
        $this->assertSame( 'news/item-1', $paths[1] );
        $this->assertSame( 'news/old-item', $paths[2] );
        $this->assertSame( 'old-news/item-0', $paths[4] );
        $this->assertArrayNotHasKey( 9, $paths );
    }

    /** RD-02, RD-03 */
    public function testStubs()
    {
        $taken = function ( $file ) { return $file === 'about-us/index.html'; };
        $pages = array( '/news' => true, '/news/item-1' => true, '/about-us' => true );
        $stubs = expStaticRedirects::stubs( self::rows(), array( 60 => '/news', 61 => '/news/item-1', 62 => '/news/item-0', 63 => '/about-us' ), $taken, $pages );
        $this->assertSame( array( '/go/news', '/news/old-item', '/old-news', '/old-news/item-0', '/promo' ), array_keys( $stubs ) );
        $this->assertSame( array( 'to' => '/news/item-1', 'source' => 'history', 'file' => 'news/old-item/index.html' ), $stubs['/news/old-item'] );
        $this->assertSame( 'alias', $stubs['/promo']['source'] );
        $this->assertSame( array( 'to' => '/news', 'source' => 'wildcard', 'file' => 'go/news/index.html' ), $stubs['/go/news'] );
    }

    /** RD-04 */
    public function testStub()
    {
        $html = expStaticRedirects::stub( '../news/item-1/', 'https://o.github.io/r/news/item-1/' );
        $this->assertStringContainsString( '<meta http-equiv="refresh" content="0; url=../news/item-1/">', $html );
        $this->assertStringContainsString( '<link rel="canonical" href="https://o.github.io/r/news/item-1/">', $html );
        $this->assertStringContainsString( '<meta name="robots" content="noindex">', $html );
        // the stub's own text is English: screen readers need the language (accessibility check of the demo build)
        $this->assertStringContainsString( '<html lang="en">', $html );
        $this->assertStringNotContainsString( 'canonical', expStaticRedirects::stub( 'x/' ) );
        $this->assertStringContainsString( 'url=a&quot;b', expStaticRedirects::stub( 'a"b' ) );
    }

    /** RD-05 */
    public function testResolveChain()
    {
        $map = array(
            'http://s.test/authors' => array( 'status' => 302, 'location' => 'http://s.test/' ),
            'http://s.test/authors/jane' => array( 'status' => 301, 'location' => 'http://s.test/authors' ),
            'http://s.test/a' => array( 'status' => 302, 'location' => 'http://s.test/b' ),
            'http://s.test/b' => array( 'status' => 302, 'location' => 'http://s.test/a' ),
            'http://s.test/none' => array( 'status' => 302, 'location' => '' ),
        );
        $this->assertSame( array( 'to' => 'http://s.test/', 'hops' => 1, 'error' => '' ), expStaticRedirects::resolveChain( $map, 'http://s.test/authors' ) );
        $this->assertSame( array( 'to' => 'http://s.test/', 'hops' => 2, 'error' => '' ), expStaticRedirects::resolveChain( $map, 'http://s.test/authors/jane' ) );
        $this->assertSame( 'loop', expStaticRedirects::resolveChain( $map, 'http://s.test/a' )['error'] );
        $this->assertSame( 'without a Location', expStaticRedirects::resolveChain( $map, 'http://s.test/none' )['error'] );
        $this->assertSame( 'chain longer than 1', expStaticRedirects::resolveChain( $map, 'http://s.test/authors/jane', 1 )['error'] );
        $chain = array();
        for ( $i = 0; $i < 9; $i++ )
            $chain["http://s.test/$i"] = array( 'status' => 302, 'location' => 'http://s.test/' . ( $i + 1 ) );
        $this->assertSame( 'chain longer than 5', expStaticRedirects::resolveChain( $chain, 'http://s.test/0', 5 )['error'] );
        $this->assertSame( '', expStaticRedirects::resolveChain( $chain, 'http://s.test/5', 5 )['error'] );

        $this->assertSame( 'http://s.test/', expPreloadRunner::resolveLocation( '/', 'http://s.test/authors' ) );
        $this->assertSame( 'http://s.test:8080/news/x', expPreloadRunner::resolveLocation( 'x', 'http://s.test:8080/news/item' ) );
        $this->assertSame( 'https://other.example/x', expPreloadRunner::resolveLocation( '//other.example/x', 'https://s.test/a' ) );
        $this->assertSame( 'https://other.example/x', expPreloadRunner::resolveLocation( 'https://other.example/x', 'http://s.test/a' ) );
        $this->assertSame( '', expPreloadRunner::resolveLocation( ' ', 'http://s.test/a' ) );
    }
}
