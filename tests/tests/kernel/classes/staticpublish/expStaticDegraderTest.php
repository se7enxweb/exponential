<?php
/**
 * What a static copy cannot do, found and given a fallback (expStaticDegrader): pure.
 *
 *  DG-01 the features of a page: search, collector, comments, shop, login, a script that calls the server, another form
 *  DG-02 the search form goes to the build's search page, a GET form; without a search page it stays
 *  DG-03 a collector posts to the form endpoint with the target and a token; hidden without an endpoint or with Forms=hide
 *  DG-04 comments and shop forms become the fallback text with the link to the live page; the login form is removed
 *  DG-05 the report: counts, pages, what happened, examples; merge()
 *  DG-06 the attribute helpers
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expStaticDegraderTest extends PHPUnit\Framework\TestCase
{
    const SEARCH = '<form action="https://live.example.com/content/search" method="get"><input name="SearchText" /><button>Go</button></form>';
    const COLLECT = '<form method="post" action="https://live.example.com/content/action"><input type="hidden" name="ContentObjectID" value="57" />'
                  . '<input type="hidden" name="ContentNodeID" value="59" /><input name="ContentObjectAttribute_ezstring_data_text_1" />'
                  . '<input type="submit" name="ActionCollectInformation" value="Send" /></form>';
    const COMMENT = '<form method="post" action="https://live.example.com/comment/add"><textarea name="c"></textarea></form>';
    const SHOP = '<form method="post" action="https://live.example.com/content/action"><input type="submit" name="ActionAddToBasket" /></form>';
    const LOGIN = '<form method="post" action="https://live.example.com/user/login"><input name="Login" /><input type="password" name="Password" /></form>';
    const OTHER = '<form method="post" action="https://elsewhere.example.org/subscribe"><input name="e" /></form>';

    private function degrader( array $options = array() )
    {
        return new expStaticDegrader( $options + array(
            'target' => 'pub', 'search' => true, 'forms' => 'endpoint', 'endpoint' => 'https://live.example.com/staticpublish/form',
            'token' => function ( $objectId ) { return "tok-$objectId"; },
            'link' => function ( $file, $fromFile ) { return expStaticLinkRewriter::relative( $fromFile, $file ); },
            'live_url' => 'https://live.example.com/' ) );
    }

    /** DG-01 */
    public function testFeatures()
    {
        $html = self::SEARCH . self::COLLECT . self::COMMENT . self::SHOP . self::LOGIN . self::OTHER
              . '<form method="get" action="/x"><input name="page" /></form><script>jQuery.ez("a::b")</script>';
        $this->assertSame( array( 'search' => 1, 'collect' => 1, 'comments' => 1, 'shop' => 1, 'login' => 1, 'form' => 1, 'ajax' => 1 ), expStaticDegrader::features( $html ) );
        $this->assertSame( array(), expStaticDegrader::features( '<p>No forms</p>' ) );
        $this->assertSame( 'search', expStaticDegrader::classify( '<form>', '<input name="SearchText">' ) );
        $this->assertSame( 'form', expStaticDegrader::classify( '<form method="POST">', '<input name="SearchText">' ) );
    }

    /** DG-02 */
    public function testSearch()
    {
        $out = $this->degrader()->apply( '<p>x</p>' . self::SEARCH, '/news/item', 'news/item/index.html' );
        $this->assertStringContainsString( '<form action="../../search.html" method="get" data-exp-static="search">', $out );
        $this->assertStringContainsString( '<input name="SearchText" />', $out );
        $kept = $this->degrader( array( 'search' => false ) )->apply( self::SEARCH, '/', 'index.html' );
        $this->assertSame( self::SEARCH, $kept );
    }

    /** DG-03 */
    public function testCollector()
    {
        $out = $this->degrader()->apply( self::COLLECT, '/contact', 'contact/index.html' );
        $this->assertStringContainsString( 'action="https://live.example.com/staticpublish/form"', $out );
        $this->assertStringContainsString( '<input type="hidden" name="ExpStaticTarget" value="pub" /><input type="hidden" name="ExpStaticToken" value="tok-57" />', $out );
        $this->assertStringContainsString( 'name="ActionCollectInformation"', $out );
        foreach ( array( array( 'forms' => 'hide' ), array( 'endpoint' => '' ) ) as $options )
        {
            $out = $this->degrader( $options )->apply( self::COLLECT, '/contact', 'contact/index.html' );
            $this->assertStringNotContainsString( '<form', $out );
            $this->assertSame( '<p class="exp-static-fallback">This form works on the live site only. <a href="https://live.example.com/contact">Open this page there</a></p>', $out );
        }
        // without an object id there is nothing to sign
        $out = $this->degrader()->apply( str_replace( 'value="57"', 'value=""', self::COLLECT ), '/contact', 'contact/index.html' );
        $this->assertStringContainsString( 'exp-static-fallback', $out );
    }

    /** DG-04 */
    public function testFallbacks()
    {
        $d = $this->degrader( array( 'live_url' => '', 'fallback_text' => 'Nur online <b>.' ) );
        $out = $d->apply( '<div>' . self::COMMENT . '</div><div>' . self::SHOP . '</div><div>' . self::LOGIN . '</div>' . self::OTHER, '/a', 'a/index.html' );
        $this->assertSame( '<div><p class="exp-static-fallback">Nur online &lt;b&gt;.</p></div><div><p class="exp-static-fallback">Nur online &lt;b&gt;.</p></div><div></div>' . self::OTHER, $out );
    }

    /** DG-05 */
    public function testReport()
    {
        $d = $this->degrader( array( 'forms' => 'hide' ) );
        $d->apply( self::SEARCH . self::SEARCH . self::COLLECT, '/a', 'a/index.html' );
        $d->apply( self::COLLECT . '<script src="/ezjscore/call/x"></script>', '/b', 'b/index.html' );
        $report = $d->report();
        $this->assertSame( array( 'search', 'collect', 'ajax' ), array_keys( $report ) );
        $this->assertSame( array( 'count' => 2, 'pages' => 1, 'handled' => array( 'search-page' => 2 ), 'examples' => array( '/a' ) ), $report['search'] );
        $this->assertSame( array( 'count' => 2, 'pages' => 2, 'handled' => array( 'fallback' => 2 ), 'examples' => array( '/a', '/b' ) ), $report['collect'] );
        $merged = expStaticDegrader::merge( $report, array( 'collect' => array( 'count' => 1, 'pages' => 1, 'handled' => array( 'endpoint' => 1 ), 'examples' => array( '/c' ) ) ) );
        $this->assertSame( array( 'count' => 3, 'pages' => 3, 'handled' => array( 'fallback' => 2, 'endpoint' => 1 ), 'examples' => array( '/a', '/b', '/c' ) ), $merged['collect'] );
    }

    /** DG-06 */
    public function testAttributes()
    {
        $this->assertSame( '/x', expStaticDegrader::attribute( '<form data-action="/no" action=\'/x\'>', 'action' ) );
        $this->assertNull( expStaticDegrader::attribute( '<form>', 'action' ) );
        $this->assertSame( '<form action="a&amp;b">', expStaticDegrader::setAttribute( '<form>', 'action', 'a&b' ) );
        $this->assertSame( '<form method="get" id="f">', expStaticDegrader::setAttribute( '<form method=post id="f">', 'method', 'get' ) );
        $this->assertSame( '57', expStaticDegrader::fieldValue( self::COLLECT, 'ContentObjectID' ) );
    }
}
