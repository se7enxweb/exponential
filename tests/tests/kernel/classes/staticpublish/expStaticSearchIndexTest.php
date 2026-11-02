<?php
/**
 * The search of a static copy (expStaticSearchIndex): pure.
 *
 *  SI-01 what a page offers: its language, title, teaser (description or text), terms by frequency; nothing for noindex
 *  SI-02 one index per language, schema exp-static-search/1, addresses relative to the build's root
 *  SI-03 the size cap: fewer terms, then no teasers, then fewer pages (reported as dropped), never above the limit
 *  SI-04 a partial build keeps the entries of the pages it did not build
 *  SI-05 the search page names the indexes, reads SearchText, loads the script and style sheet below the root
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expStaticSearchIndexTest extends PHPUnit\Framework\TestCase
{
    private static function page( $lang, $title, $text, $head = '' )
    {
        return "<!DOCTYPE html><html lang=\"$lang\"><head><title>$title</title>$head</head><body><nav>Menu Home</nav>"
             . "<main><h1>$title</h1><p>$text</p><script>var hidden = 'secretword';</script></main></body></html>";
    }

    /** SI-01 */
    public function testExtract()
    {
        $page = expStaticSearchIndex::extract( self::page( 'en-GB', 'Bikes &amp; more', 'Bikes are fast. Bikes are green. Trains are fast too.' ) );
        $this->assertSame( 'en-gb', $page['lang'] );
        $this->assertSame( 'Bikes & more', $page['title'] );
        $this->assertSame( 'bikes', explode( ' ', $page['terms'] )[0] );
        $this->assertStringNotContainsString( 'secretword', $page['terms'] );
        $this->assertStringNotContainsString( 'menu', $page['terms'] );
        $this->assertStringStartsWith( 'Bikes & more Bikes are fast.', $page['teaser'] );
        $described = expStaticSearchIndex::extract( self::page( 'de', 'T', 'Text', '<meta name="description" content="Kurz und gut">' ) );
        $this->assertSame( 'Kurz und gut', $described['teaser'] );
        $this->assertNull( expStaticSearchIndex::extract( self::page( 'en', 'T', 'x', '<meta name="robots" content="noindex, follow">' ) ) );
        $this->assertSame( 'und', expStaticSearchIndex::extract( '<html><title>X</title></html>' )['lang'] );
        $this->assertSame( array( 'ccc', 'aaa', 'bbb' ), expStaticSearchIndex::terms( 'aaa bbb ccc ccc 12345 x', 5 ) );
        $this->assertSame( 'one two…', expStaticSearchIndex::cut( 'one two three four', 9 ) );
    }

    /** SI-02 */
    public function testFiles()
    {
        $index = new expStaticSearchIndex();
        $this->assertTrue( $index->add( self::page( 'en', 'Home', 'Welcome' ), '' ) );
        $this->assertTrue( $index->add( self::page( 'en', 'About', 'About us' ), 'about-us/' ) );
        $this->assertTrue( $index->add( self::page( 'de', 'Über', 'Über uns' ), 'de/ueber/' ) );
        $this->assertSame( array( 'de', 'en' ), $index->languages() );
        $files = $index->files();
        $this->assertSame( array( 'static-search/index-de.json', 'static-search/index-en.json' ), array_keys( $files ) );
        $en = json_decode( $files['static-search/index-en.json']['content'], true );
        $this->assertSame( 'exp-static-search/1', $en['schema'] );
        $this->assertSame( array( '', 'about-us/' ), array_column( $en['pages'], 'u' ) );
        $this->assertSame( 'Über', json_decode( $files['static-search/index-de.json']['content'], true )['pages'][0]['t'] );
    }

    /** SI-03 */
    public function testSizeCap()
    {
        $index = new expStaticSearchIndex( 2000 );
        for ( $i = 0; $i < 80; $i++ )
            $index->add( self::page( 'en', "Page $i", str_repeat( "word$i alpha$i beta$i gamma$i delta$i epsilon$i ", 3 ) ), "p$i/" );
        $file = $index->files()['static-search/index-en.json'];
        $this->assertLessThanOrEqual( 2000, strlen( $file['content'] ) );
        $this->assertGreaterThan( 0, $file['dropped'] );
        $this->assertSame( 80, $file['pages'] + $file['dropped'] );
        $first = json_decode( $file['content'], true )['pages'][0];
        $this->assertSame( '', $first['d'] );
        $this->assertSame( 'p0/', $first['u'] );
    }

    /** SI-04 */
    public function testKeep()
    {
        $old = new expStaticSearchIndex();
        $old->add( self::page( 'en', 'Home', 'Old home' ), '' );
        $old->add( self::page( 'en', 'News', 'Old news' ), 'news/' );
        $json = $old->files()['static-search/index-en.json']['content'];
        $new = new expStaticSearchIndex();
        $new->add( self::page( 'en', 'Home', 'New home' ), '' );
        $this->assertSame( 1, $new->keep( $json ) );
        $this->assertSame( 0, $new->keep( '{"schema":"other"}' ) );
        $pages = json_decode( $new->files()['static-search/index-en.json']['content'], true )['pages'];
        $this->assertSame( array( '', 'news/' ), array_column( $pages, 'u' ) );
        $this->assertStringContainsString( 'New home', $pages[0]['d'] );
    }

    /** SI-05 */
    public function testPage()
    {
        $html = expStaticSearchIndex::page( array( 'de', 'en' ), array( 'title' => 'Suche', 'nothing' => 'Nichts </script>' ) );
        $this->assertStringContainsString( '<html lang="de">', $html );
        $this->assertStringContainsString( 'name="SearchText"', $html );
        $this->assertStringContainsString( '"de":"static-search/index-de.json"', $html );
        $this->assertStringContainsString( 'Nichts <\/script>', $html );
        $this->assertStringContainsString( '<script src="static-search/search.js" defer></script>', $html );
        $this->assertStringContainsString( 'href="static-search/search.css"', $html );
        $this->assertStringContainsString( "params.get('SearchText')", expStaticSearchIndex::script() );
        $this->assertStringContainsString( 'prefers', expStaticSearchIndex::styleSheet() . 'prefers' );
    }
}
