<?php
/**
 * File containing the eZRSSExportFeedEscapingTest class.
 *
 * eZRSSExport::generateFeed() hands its values to ezcFeed, which escapes most
 * of them itself (text nodes and attributes) and writes a few verbatim
 * (createElement() with a value, nodeValue). Escaping every value before
 * ezcFeed saw it wrote "&amp;amp;" and "&amp;lt;" into RSS 1.0 and 2.0 feeds.
 * These tests build a feed of each type the way generateFeed() does, through
 * eZRSSExport::feedText(), and read it back. No database.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

class eZRSSExportFeedEscapingTest extends PHPUnit\Framework\TestCase
{
    const TITLE = 'Fish & "Chips" <b>today</b>';
    const DESCRIPTION = '<p>Salt & vinegar</p>';
    const URL = 'http://example.invalid/news?a=1&b=2';
    const PERSON = 'Smith & Sons';
    const CATEGORY = 'Food & drink';

    public static function feedTypes()
    {
        return array( 'RSS 1.0' => array( 'rss1' ), 'RSS 2.0' => array( 'rss2' ), 'ATOM' => array( 'atom' ) );
    }

    /**
     * Builds a feed of $type exactly as eZRSSExport::generateFeed() fills it.
     */
    private function generate( $type )
    {
        $feed = new ezcFeed();
        $feed->title = eZRSSExport::feedText( $type, 'title', self::TITLE );
        $link = $feed->add( 'link' );
        $link->href = self::URL;
        $feed->description = eZRSSExport::feedText( $type, 'description', self::DESCRIPTION );
        $feed->id = self::URL;
        $feed->updated = 1000000000;
        $author = $feed->add( 'author' );
        $author->email = eZRSSExport::feedText( $type, 'author', 'feeds@example.invalid' );
        $author->name = eZRSSExport::feedText( $type, 'author', self::PERSON );

        $item = $feed->add( 'item' );
        $item->title = eZRSSExport::feedText( $type, 'title', self::TITLE );
        $itemLink = $item->add( 'link' );
        $itemLink->href = self::URL;
        $item->id = $type === 'rss2' ? eZRSSExport::feedText( $type, 'guid', 'a&b' ) : self::URL;
        $itemAuthor = $item->add( 'author' );
        $itemAuthor->name = eZRSSExport::feedText( $type, 'author', self::PERSON );
        $itemAuthor->email = eZRSSExport::feedText( $type, 'author', 'feeds@example.invalid' );
        $item->description = eZRSSExport::feedText( $type, 'description', self::DESCRIPTION );
        $category = $item->add( 'category' );
        $category->term = eZRSSExport::feedText( $type, 'category', self::CATEGORY );
        $enclosure = $item->add( 'enclosure' );
        $enclosure->url = self::URL;
        $enclosure->length = 1;
        $enclosure->type = 'audio/mpeg';
        $item->published = 1000000000;
        $item->updated = 1000000000;

        return $feed->generate( $type );
    }

    private function load( $xml )
    {
        $dom = new DOMDocument( '1.0', 'utf-8' );
        $this->assertTrue( $dom->loadXML( $xml, LIBXML_NOERROR | LIBXML_NOWARNING ), 'the feed is not valid XML' );

        return new DOMXPath( $dom );
    }

    /**
     * @dataProvider feedTypes
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('feedTypes')]
    public function testNoValueIsEscapedTwice( $type )
    {
        $xml = $this->generate( $type );
        $this->load( $xml );
        $this->assertDoesNotMatchRegularExpression( '/&amp;(amp|lt|gt|quot|#\d+);/', $xml, "$type feed escapes a value twice:\n$xml" );
    }

    /**
     * @dataProvider feedTypes
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('feedTypes')]
    public function testTitleDescriptionAndLinkReadBackAsWritten( $type )
    {
        $xpath = $this->load( $this->generate( $type ) );
        $xpath->registerNamespace( 'rss1', 'http://purl.org/rss/1.0/' );
        $xpath->registerNamespace( 'atom', 'http://www.w3.org/2005/Atom' );

        switch ( $type )
        {
            case 'rss1':
                $item = '//rss1:item/';
                $titles = array( '//rss1:channel/rss1:title', $item . 'rss1:title' );
                $descriptions = array( $item . 'rss1:description' );
                $links = array( '//rss1:channel/rss1:link', $item . 'rss1:link', '//rss1:channel/@rdf:about' );
                break;
            case 'rss2':
                $item = '//channel/item/';
                $titles = array( '//channel/title', $item . 'title' );
                $descriptions = array( $item . 'description' );
                $links = array( '//channel/link', $item . 'link', $item . 'enclosure/@url', '//channel/atom:link/@href' );
                break;
            default:
                $item = '//atom:entry/';
                $titles = array( '/atom:feed/atom:title', $item . 'atom:title' );
                $descriptions = array( $item . 'atom:summary' );
                $links = array( '/atom:feed/atom:link/@href', $item . 'atom:link/@href', '/atom:feed/atom:id' );
        }

        foreach ( $titles as $query )
        {
            $this->assertSame( self::TITLE, $xpath->evaluate( 'string(' . $query . ')' ), "$type $query" );
        }
        foreach ( $descriptions as $query )
        {
            $this->assertSame( self::DESCRIPTION, $xpath->evaluate( 'string(' . $query . ')' ), "$type $query" );
        }
        foreach ( $links as $query )
        {
            $this->assertSame( self::URL, $xpath->evaluate( 'string(' . $query . ')' ), "$type $query" );
        }
    }

    public function testRss2PersonCategoryAndGuidAreKept()
    {
        // ezcFeed writes these with createElement( $name, $value ): an unescaped
        // "&" leaves them empty, an escaped one must come back as "&"
        $xpath = $this->load( $this->generate( 'rss2' ) );

        $this->assertSame( 'feeds@example.invalid (' . self::PERSON . ')', $xpath->evaluate( 'string(//channel/item/author)' ) );
        $this->assertSame( 'feeds@example.invalid (' . self::PERSON . ')', $xpath->evaluate( 'string(//channel/managingEditor)' ) );
        $this->assertSame( self::CATEGORY, $xpath->evaluate( 'string(//channel/item/category)' ) );
        $this->assertSame( 'a&b', $xpath->evaluate( 'string(//channel/item/guid)' ) );
    }

    public function testAtomPersonAndCategoryAreNotEscapedTwice()
    {
        $xpath = $this->load( $this->generate( 'atom' ) );
        $xpath->registerNamespace( 'atom', 'http://www.w3.org/2005/Atom' );

        $this->assertSame( self::PERSON, $xpath->evaluate( 'string(//atom:entry/atom:author/atom:name)' ) );
        $this->assertSame( self::CATEGORY, $xpath->evaluate( 'string(//atom:entry/atom:category/@term)' ) );
    }

    public function testGenerateFeedHandsPlainValuesToFeedText()
    {
        // generateFeed() itself needs a database; this guards that it keeps
        // leaving the escaping to feedText() instead of escaping values itself
        $source = file_get_contents( __DIR__ . '/../../../../kernel/classes/ezrssexport.php' );
        $start = strpos( $source, 'function generateFeed(' );
        $this->assertNotFalse( $start );
        $end = strpos( $source, 'function fetchImageURL(', $start );
        $body = substr( $source, $start, $end - $start );

        $this->assertDoesNotMatchRegularExpression( '/htmlspecialchars\(\s*\$(?!origImage|itemImage|descContent->attribute\( \'alternative_text)/', $body,
            'generateFeed() escapes a feed value itself; use eZRSSExport::feedText()' );
        $this->assertStringNotContainsString( '&lt;a href', $body, 'an image description is escaped by hand' );
    }
}
?>
