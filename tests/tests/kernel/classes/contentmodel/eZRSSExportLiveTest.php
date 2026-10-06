<?php
/**
 * RSS exports of a throwaway folder of articles, written as RSS 1.0, RSS 2.0, Atom, a podcast and OPML: which
 * articles become items (newest first, the number of objects, hidden ones left out, articles of subfolders only
 * when the source says so), their titles and descriptions from the mapped attributes, the channel title, the
 * export lookups by access URL and in the feed browser, and removing an export with its items.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/expContentModelLiveTestCase.php';

class eZRSSExportLiveTest extends expContentModelLiveTestCase
{
    protected static $folder;
    protected static $sub;
    protected static $articles = array();

    /** @var int[] export ids to remove */
    protected static $exports = array();

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::$exports = array();
        static::$folder = static::folder( static::$root['node'], 'Feed source' );
        static::$sub = static::folder( static::$folder['node'], 'Feed subfolder' );
        static::$articles = array();
        $time = time() - 1000;
        foreach ( array( 'First story' => static::$folder, 'Second story' => static::$folder, 'Third story' => static::$folder, 'Deep story' => static::$sub ) as $title => $parent )
        {
            $article = static::createObject( 'article', $parent['node'], array( 'title' => $title, 'intro' =>
                '<?xml version="1.0" encoding="utf-8"?><section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"><paragraph>About ' . $title . '</paragraph></section>' ) );
            // distinct publishing times, one minute apart, so the order is known
            $object = eZContentObject::fetch( $article['object'] );
            $object->setAttribute( 'published', $time += 60 );
            $object->store();
            static::$articles[$title] = $article;
        }
        eZContentObject::clearCache();
    }

    public static function tearDownAfterClass(): void
    {
        foreach ( static::$exports as $id )
        {
            foreach ( array( eZRSSExport::STATUS_VALID, eZRSSExport::STATUS_DRAFT ) as $status )
            {
                $export = eZRSSExport::fetch( $id, true, $status );
                if ( $export )
                    $export->removeThis();
            }
        }
        static::$exports = array();
        parent::tearDownAfterClass();
    }

    private function export( $version, array $more = array(), $subnodes = false )
    {
        $export = eZRSSExport::create( eZUser::currentUserID() );
        $export->setAttribute( 'title', 'k1c feed ' . uniqid() );
        $export->setAttribute( 'description', 'Stories of the k1c test' );
        $export->setAttribute( 'rss_version', $version );
        $export->setAttribute( 'access_url', 'k1c_feed_' . uniqid() );
        $export->setAttribute( 'url', 'https://k1c.example.invalid' );
        $export->setAttribute( 'number_of_objects', 10 );
        $export->setAttribute( 'main_node_only', 1 );
        $export->setAttribute( 'status', eZRSSExport::STATUS_VALID );
        foreach ( $more as $name => $value )
            $export->setAttribute( $name, $value );
        $export->store();
        static::$exports[] = (int)$export->attribute( 'id' );

        $item = eZRSSExportItem::create( $export->attribute( 'id' ) );
        $item->setAttribute( 'class_id', eZContentClass::classIDByIdentifier( 'article' ) );
        $item->setAttribute( 'title', 'title' );
        $item->setAttribute( 'description', 'intro' );
        $item->setAttribute( 'source_node_id', static::$folder['node'] );
        $item->setAttribute( 'subnodes', $subnodes ? 1 : 0 );
        $item->setAttribute( 'status', eZRSSExport::STATUS_VALID );
        $item->store();
        return eZRSSExport::fetch( $export->attribute( 'id' ) );
    }

    private function itemTitles( $xml )
    {
        $doc = new DOMDocument();
        $this->assertTrue( $doc->loadXML( $xml ), 'well formed' );
        $xpath = new DOMXPath( $doc );
        $titles = array();
        foreach ( $xpath->query( '//*[local-name()="item" or local-name()="entry"]/*[local-name()="title"]' ) as $node )
            $titles[] = trim( $node->textContent );
        return $titles;
    }

    public static function formatProvider()
    {
        return array( 'RSS 1.0' => array( '1.0', 'rdf:RDF' ), 'RSS 2.0' => array( '2.0', 'rss' ), 'Atom' => array( 'ATOM', 'feed' ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('formatProvider')]
    public function testFeedOfTheFolderNewestFirst( $version, $rootElement )
    {
        $export = $this->export( $version );
        $xml = $export->rssXmlContent();
        $doc = new DOMDocument();
        $this->assertTrue( $doc->loadXML( $xml ) );
        $this->assertSame( $rootElement, $doc->documentElement->nodeName );
        $this->assertSame( array( 'Third story', 'Second story', 'First story' ), $this->itemTitles( $xml ) );
        $this->assertStringContainsString( $export->attribute( 'title' ), $xml );
        $this->assertStringContainsString( 'About Second story', $xml, 'the description from the intro' );
    }

    public function testNumberOfObjectsAndSubnodes()
    {
        $two = $this->export( '2.0', array( 'number_of_objects' => 2 ) );
        $this->assertSame( array( 'Third story', 'Second story' ), $this->itemTitles( $two->rssXmlContent() ) );

        $deep = $this->export( '2.0', array(), true );
        $this->assertSame( array( 'Deep story', 'Third story', 'Second story', 'First story' ), $this->itemTitles( $deep->rssXmlContent() ) );
    }

    public function testHiddenArticleIsLeftOut()
    {
        $second = eZContentObjectTreeNode::fetch( static::$articles['Second story']['node'] );
        eZContentObjectTreeNode::hideSubTree( $second );
        try
        {
            $titles = $this->itemTitles( $this->export( 'ATOM' )->rssXmlContent() );
            if ( eZContentObjectTreeNode::showInvisibleNodes() )
                $this->assertContains( 'Second story', $titles );
            else
                $this->assertSame( array( 'Third story', 'First story' ), $titles );
        }
        finally
        {
            eZContentObjectTreeNode::unhideSubTree( eZContentObjectTreeNode::fetch( static::$articles['Second story']['node'] ) );
        }
    }

    public function testPodcastWithoutEnclosuresHasAChannelAndNoEpisodes()
    {
        $export = $this->export( 'ITUNES' );
        $export->setPodcastHead( array( 'author' => 'K1c', 'category' => 'Technology', 'explicit' => 'no', 'ownerName' => 'K1c owner' ) );
        $export->store();
        $xml = eZRSSExport::fetch( $export->attribute( 'id' ) )->rssXmlContent();
        $doc = new DOMDocument();
        $this->assertTrue( $doc->loadXML( $xml ) );
        $this->assertSame( 'rss', $doc->documentElement->nodeName );
        $this->assertSame( array(), $this->itemTitles( $xml ), 'an article has nothing to play' );
        $this->assertStringContainsString( '<itunes:category text="Technology"/>', $xml );
        $this->assertStringContainsString( '<itunes:explicit>false</itunes:explicit>', $xml );
        $this->assertStringContainsString( '<itunes:name>K1c owner</itunes:name>', $xml );
        $this->assertStringContainsString( 'https://k1c.example.invalid/rss/feed/' . $export->attribute( 'access_url' ), $xml );
        $this->assertCount( 3, $export->podcastItemList( 'https://k1c.example.invalid' ), 'three episodes without an enclosure' );
    }

    public function testOpmlWithoutOutlinesIsValid()
    {
        $export = $this->export( 'OPML' );
        $xml = $export->rssXmlContent();
        $doc = new DOMDocument();
        $this->assertTrue( $doc->loadXML( $xml ) );
        $this->assertSame( 'opml', $doc->documentElement->nodeName );
        $this->assertSame( array(), $export->opmlItemList() );
    }

    public function testLookupsAndRemoval()
    {
        $export = $this->export( '2.0' );
        $id = (int)$export->attribute( 'id' );
        $this->assertSame( $id, (int)eZRSSExport::fetchByName( $export->attribute( 'access_url' ) )->attribute( 'id' ) );
        $this->assertNull( eZRSSExport::fetchByName( 'k1c_no_such_feed' ) );
        $this->assertCount( 1, $export->itemList() );
        $this->assertSame( static::$folder['node'], (int)$export->itemList()[0]->attribute( 'source_node_id' ) );

        $found = eZRSSExport::fetchBrowserList( $export->attribute( 'title' ) );
        $this->assertSame( array( $id ), array_map( function ( $e ) { return (int)$e->attribute( 'id' ); }, $found ) );
        $this->assertSame( 1, eZRSSExport::fetchBrowserListCount( $export->attribute( 'title' ) ) );
        $this->assertSame( 0, eZRSSExport::fetchBrowserListCount( $export->attribute( 'title' ), $id ), 'the excluded export' );
        $this->assertGreaterThanOrEqual( 1, (int)eZRSSExport::fetchListCount() );

        $filter = $export->getObjectListFilter();
        $this->assertSame( 10, (int)$filter['number_of_objects'] );
        $this->assertSame( 1, (int)$filter['main_node_only'] );

        $export->removeThis();
        $this->assertNull( eZRSSExport::fetch( $id ) );
        $this->assertSame( array(), eZDB::instance()->arrayQuery( 'SELECT id FROM ezrss_export_item WHERE rssexport_id=' . $id ) );
    }

    public function testNewExportDefaults()
    {
        $export = eZRSSExport::create( 14 );
        $this->assertSame( eZRSSExport::STATUS_DRAFT, $export->attribute( 'status' ) );
        $this->assertSame( 14, $export->attribute( 'creator_id' ) );
        $this->assertSame( 1, $export->attribute( 'active' ) );
        $this->assertSame( 0, (int)$export->attribute( 'node_id' ) );
        $this->assertNull( $export->imageNode() );
        $this->assertNull( $export->imagePath() );
    }
}
