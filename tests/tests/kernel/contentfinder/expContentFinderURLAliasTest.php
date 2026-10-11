<?php
/**
 * How the content finder resolves a URL alias or a page address (expContentFinder::urlAliasCandidates(),
 * prefixed(), expContentFinderInput::urlHost(); doc/features/6.0/content-finder.md, "URL aliases and siteaccesses").
 *
 *  CU-01  a full URL names its host in the query; a path has none
 *  CU-02  the PathPrefix of a siteaccess is put in front as the kernel does: not twice, not before an excluded path
 *  CU-03  a page address with a siteaccess segment (URI matching): the segment comes off, that siteaccess's prefix
 *         goes in front (/site/showcase/test-product -> fit-healthy/showcase/test-product)
 *  CU-04  a page address matched by its host (no segment): the host's siteaccess's prefix goes in front
 *  CU-05  a bare alias: exactly first, then with each distinct prefix of the siteaccesses; each path once
 *  CU-06  a part of an alias: its words, the escaped LIKE pattern, and the exact decision (order, separators, case)
 *
 * No database, no settings.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expContentFinderURLAliasTest extends PHPUnit\Framework\TestCase
{
    /** siteaccesses as alpha has them: site and bold with a PathPrefix, admin without */
    private function siteAccesses()
    {
        return array( 'site' => array( 'prefix' => 'fit-healthy', 'exclude' => array( 'media', 'users' ) ),
                      'bold' => array( 'prefix' => 'bold-agency', 'exclude' => array( 'media' ) ),
                      'bold_de' => array( 'prefix' => 'bold-agency', 'exclude' => array( 'media' ) ),
                      'admin' => array( 'prefix' => '', 'exclude' => array() ) );
    }

    /** @return string[] the paths tried, in order */
    private function paths( $path, $matched = null )
    {
        return array_column( expContentFinder::urlAliasCandidates( $path, $this->siteAccesses(), $matched ), 'path' );
    }

    /** CU-01 */
    public function testAFullURLNamesItsHost()
    {
        $parsed = expContentFinderInput::parse( 'https://Alpha.Example.invalid:8443/site/showcase/test-product?x=1' );
        $this->assertSame( array( array( 'type' => 'urlalias', 'value' => 'site/showcase/test-product', 'host' => 'alpha.example.invalid' ) ),
                           $parsed['queries'] );
        $parsed = expContentFinderInput::parse( '/site/showcase/test-product' );
        $this->assertSame( array( array( 'type' => 'urlalias', 'value' => 'site/showcase/test-product' ) ), $parsed['queries'] );
        $this->assertSame( '', expContentFinderInput::urlHost( 'showcase/test-product' ) );
        $this->assertSame( '', expContentFinderInput::urlHost( 'ftp://example.invalid/x' ) );
    }

    /** CU-02 */
    public function testThePathPrefixAsTheKernelPutsIt()
    {
        $this->assertSame( 'fit-healthy/showcase/test-product', expContentFinder::prefixed( 'showcase/test-product', 'fit-healthy' ) );
        $this->assertSame( 'fit-healthy/showcase', expContentFinder::prefixed( 'fit-healthy/showcase', 'fit-healthy' ) );
        $this->assertSame( 'Fit-Healthy/showcase', expContentFinder::prefixed( 'Fit-Healthy/showcase', '/fit-healthy/' ) );
        $this->assertSame( 'fit-healthy', expContentFinder::prefixed( '', 'fit-healthy' ) );
        $this->assertSame( 'media/images/a', expContentFinder::prefixed( 'media/images/a', 'fit-healthy', array( 'media' ) ) );
        $this->assertSame( 'fit-healthy/mediathek', expContentFinder::prefixed( 'mediathek', 'fit-healthy', array( 'media' ) ) );
        $this->assertSame( 'showcase', expContentFinder::prefixed( 'showcase', '' ) );
    }

    /** CU-03 */
    public function testAPageAddressWithASiteAccessSegment()
    {
        $matched = array( 'name' => 'site', 'uri_part' => array( 'site' ) );
        $candidates = expContentFinder::urlAliasCandidates( 'site/showcase/test-product', $this->siteAccesses(), $matched );
        $this->assertSame( array( 'path' => 'site/showcase/test-product', 'siteaccess' => '', 'prefix' => '' ), $candidates[0] );
        $this->assertSame( array( 'path' => 'fit-healthy/showcase/test-product', 'siteaccess' => 'site', 'prefix' => 'fit-healthy' ), $candidates[1] );
        $this->assertSame( array( 'path' => 'showcase/test-product', 'siteaccess' => 'site', 'prefix' => '' ), $candidates[2] );
        // the segment of the URI match comes off however it is written; a first segment that names a
        // siteaccess comes off without a match as well
        $this->assertContains( 'fit-healthy/showcase/test-product', $this->paths( 'Site/showcase/test-product', $matched ) );
        $this->assertContains( 'bold-agency/about-us', $this->paths( 'bold/about-us' ) );
        // the media library is not below the prefix
        $this->assertNotContains( 'fit-healthy/media/images', $this->paths( 'site/media/images', $matched ) );
        $this->assertContains( 'media/images', $this->paths( 'site/media/images', $matched ) );
    }

    /** CU-04 */
    public function testAPageAddressMatchedByItsHost()
    {
        $candidates = expContentFinder::urlAliasCandidates( 'about-us', $this->siteAccesses(), array( 'name' => 'bold', 'uri_part' => array() ) );
        $this->assertSame( 'about-us', $candidates[0]['path'] );
        $this->assertSame( array( 'path' => 'bold-agency/about-us', 'siteaccess' => 'bold', 'prefix' => 'bold-agency' ), $candidates[1] );
        // a match that names no known siteaccess is ignored
        $this->assertSame( $this->paths( 'about-us' ), $this->paths( 'about-us', array( 'name' => 'nosuch', 'uri_part' => array() ) ) );
    }

    /** CU-06 */
    public function testAPartOfAnAlias()
    {
        $this->assertSame( array( 'test', 'product' ), expContentFinder::aliasPartWords( 'Test Product' ) );
        $this->assertSame( array( 'test', 'product' ), expContentFinder::aliasPartWords( 'showcase/test_-product/' ) );
        $this->assertSame( array(), expContentFinder::aliasPartWords( ' - _ ' ) );
        // the LIKE pattern: the words escaped with !, one character between them
        $this->assertSame( '%test_product%', expContentFinder::aliasPartPattern( array( 'test', 'product' ) ) );
        $this->assertSame( '%a!%b_c!!d%', expContentFinder::aliasPartPattern( array( 'a%b', 'c!d' ) ) );
        $this->assertSame( '%!_x%', expContentFinder::aliasPartPattern( array( '_x' ) ) );
        // the exact decision: in order, one separator between, any case
        foreach ( array( 'test-product', 'Test_Product', 'my test product 2', 'TEST-PRODUCTS' ) as $text )
            $this->assertTrue( expContentFinder::partMatches( $text, array( 'test', 'product' ) ), $text );
        foreach ( array( 'testproduct', 'product-test', 'test--product', 'testXproduct' ) as $text )
            $this->assertFalse( expContentFinder::partMatches( $text, array( 'test', 'product' ) ), $text );
        $this->assertTrue( expContentFinder::partMatches( '100%-sure', array( '100%' ) ) );
        $this->assertFalse( expContentFinder::partMatches( '1000-sure', array( '10%' ) ) );
        $this->assertFalse( expContentFinder::partMatches( 'anything', array() ) );
        // the closest first: the element itself, starting with it, whole words, at a word start, inside a word
        $words = array( 'test' );
        $this->assertSame( 0, expContentFinder::partRank( 'Test', $words ) );
        $this->assertSame( 1, expContentFinder::partRank( 'test-product', $words ) );
        $this->assertSame( 2, expContentFinder::partRank( 'a-test-product', $words ) );
        $this->assertSame( 3, expContentFinder::partRank( 'testing', $words ) );
        $this->assertSame( 3, expContentFinder::partRank( 'my-testing', $words ) );
        $this->assertSame( 4, expContentFinder::partRank( 'nottest-article', $words ) );
        $this->assertNull( expContentFinder::partRank( 'product', $words ) );
        $this->assertSame( 1, expContentFinder::partRank( 'test-product-2', array( 'test', 'product' ) ) );
    }

    /** CU-05 */
    public function testABareAliasIsTriedExactlyFirstThenWithEachPrefix()
    {
        $this->assertSame( array( 'showcase/test-product', 'fit-healthy/showcase/test-product', 'bold-agency/showcase/test-product' ),
                           $this->paths( 'showcase/test-product' ) );
        $this->assertSame( array( 'fit-healthy/showcase/test-product', 'bold-agency/fit-healthy/showcase/test-product' ),
                           $this->paths( 'fit-healthy/showcase/test-product' ) );
        $this->assertSame( array(), expContentFinder::urlAliasCandidates( '', array() ) );
    }
}
