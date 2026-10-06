<?php
/**
 * The channel settings of an RSS export that are kept as JSON on the export, without a database: the podcast head
 * (defaults, what setPodcastHead() keeps and how it cleans each field, Apple's categories) and the OPML head
 * (numbers, addresses and e-mail only where they belong), the format labels, and the element writer that leaves
 * empty elements out.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class eZRSSExportHeadsTest extends PHPUnit\Framework\TestCase
{
    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        eZExecution::registerShutdownHandler();
    }

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
    }

    private function export( array $row = array() )
    {
        return new eZRSSExport( $row + array( 'rss_version' => 'ITUNES', 'podcast_head' => '', 'opml_head' => '' ) );
    }

    // ---------------------------------------------------------------- podcast head

    public function testPodcastHeadDefaults()
    {
        $head = $this->export()->podcastHead();
        $this->assertSame( 'false', $head['explicit'] );
        $this->assertSame( 'episodic', $head['type'] );
        $this->assertSame( '', $head['category'] );
        $this->assertCount( 15, $head );
        $this->assertSame( $head, $this->export( array( 'podcast_head' => 'not json' ) )->podcastHead() );
        $this->assertSame( $head, $this->export( array( 'podcast_head' => '"a string"' ) )->podcastHead() );
    }

    public function testPodcastHeadKeepsWhatItKnowsAndCleansIt()
    {
        $export = $this->export();
        $export->setPodcastHead( array(
            'author' => "  The k1c show \x07 ",
            'ownerEmail' => 'owner@k1c.example.invalid',
            'imageUrl' => 'javascript:alert(1)',
            'newFeedUrl' => 'https://k1c.example.invalid/feed',
            'category' => 'Technology',
            'subcategory' => 'Tech News',
            'explicit' => 'YES',
            'block' => 'no',
            'complete' => '',
            'type' => 'odd',
            'unknown' => 'dropped',
            'summary' => array( 'not text' ),
        ) );
        $head = $export->podcastHead();
        $this->assertSame( 'The k1c show', trim( str_replace( '  ', ' ', $head['author'] ) ) );
        $this->assertStringNotContainsString( "\x07", $head['author'] );
        $this->assertSame( 'owner@k1c.example.invalid', $head['ownerEmail'] );
        $this->assertSame( '', $head['imageUrl'], 'only http(s) addresses' );
        $this->assertSame( 'https://k1c.example.invalid/feed', $head['newFeedUrl'] );
        $this->assertSame( 'Technology', $head['category'] );
        $this->assertSame( '', $head['subcategory'], 'Technology has no subcategories' );
        $this->assertSame( 'true', $head['explicit'] );
        $this->assertSame( 'false', $head['block'] );
        $this->assertSame( '', $head['complete'] );
        $this->assertSame( 'episodic', $head['type'] );
        $this->assertSame( '', $head['summary'] );
        $this->assertArrayNotHasKey( 'unknown', json_decode( $export->attribute( 'podcast_head' ), true ) );

        $export->setPodcastHead( array( 'category' => 'News', 'subcategory' => 'Tech News', 'type' => 'serial', 'explicit' => 'whatever' ) );
        $head = $export->podcastHead();
        $this->assertSame( array( 'News', 'Tech News', 'serial', 'false' ), array( $head['category'], $head['subcategory'], $head['type'], $head['explicit'] ) );
        $this->assertSame( 'owner@k1c.example.invalid', $head['ownerEmail'], 'a field not given keeps its value' );
    }

    public function testSubcategoryAloneIsCheckedAgainstTheStoredCategory()
    {
        $export = $this->export();
        $export->setPodcastHead( array( 'category' => 'News', 'subcategory' => 'Politics' ) );
        $export->setPodcastHead( array( 'subcategory' => 'Daily News' ) );
        $head = $export->podcastHead();
        $this->assertSame( 'News', $head['category'] );
        $this->assertSame( 'Daily News', $head['subcategory'] );
        $export->setPodcastHead( array( 'subcategory' => 'Baseball' ) );
        $this->assertSame( '', $export->podcastHead()['subcategory'], 'not a subcategory of News' );
    }

    public static function categoryProvider()
    {
        return array(
            array( 'Arts', false, 'Arts' ), array( ' Arts ', false, 'Arts' ), array( 'arts', false, '' ), array( '', false, '' ),
            array( 'Design', 'Arts', 'Design' ), array( 'Design', 'Business', '' ), array( 'Design', 'Nope', '' ),
            array( 'Arts', 'Arts', '' ), array( 'Tech News', ' News ', 'Tech News' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('categoryProvider')]
    public function testKnownPodcastCategory( $value, $parent, $expected )
    {
        $this->assertSame( $expected, eZRSSExport::knownPodcastCategory( $value, $parent ) );
    }

    public function testEveryCategoryListIsAList()
    {
        foreach ( eZRSSExport::podcastCategories() as $category => $subcategories )
        {
            $this->assertSame( $category, eZRSSExport::knownPodcastCategory( $category ) );
            foreach ( $subcategories as $subcategory )
                $this->assertSame( $subcategory, eZRSSExport::knownPodcastCategory( $subcategory, $category ) );
        }
    }

    public function testFormatFlags()
    {
        $this->assertTrue( $this->export( array( 'rss_version' => 'ITUNES' ) )->isPodcast() );
        $this->assertFalse( $this->export( array( 'rss_version' => 'ITUNES' ) )->isOPML() );
        $this->assertTrue( $this->export( array( 'rss_version' => 'OPML' ) )->isOPML() );
        $this->assertFalse( $this->export( array( 'rss_version' => '2.0' ) )->isPodcast() );
        $this->assertNull( $this->export( array( 'rss_version' => 'k1c' ) )->rssXmlContent() );
    }

    // ---------------------------------------------------------------- OPML head

    public function testOpmlHeadKeepsOnlyCleanValues()
    {
        $export = $this->export( array( 'rss_version' => 'OPML' ) );
        $this->assertSame( 'http://opml.org/spec2.opml', $export->opmlHead()['docs'] );
        $export->setOPMLHead( array( 'ownerName' => 'K1c', 'ownerEmail' => 'not an address', 'ownerId' => 'https://k1c.example.invalid/me',
                                     'docs' => 'ftp://k1c.example.invalid/x', 'expansionState' => '1, 3,5', 'vertScrollState' => '1;DROP',
                                     'windowTop' => '-20', 'windowLeft' => array( 1 ), 'other' => 'x' ) );
        $head = $export->opmlHead();
        $this->assertSame( 'K1c', $head['ownerName'] );
        $this->assertSame( '', $head['ownerEmail'] );
        $this->assertSame( 'https://k1c.example.invalid/me', $head['ownerId'] );
        $this->assertSame( 'http://opml.org/spec2.opml', $head['docs'], 'a refused address falls back to the default' );
        $this->assertSame( '1, 3,5', $head['expansionState'] );
        $this->assertSame( '', $head['vertScrollState'] );
        $this->assertSame( '-20', $head['windowTop'] );
        $this->assertSame( '', $head['windowLeft'] );
        $this->assertSame( array( 'ownerName', 'ownerId', 'expansionState', 'windowTop' ), array_keys( json_decode( $export->attribute( 'opml_head' ), true ) ) );

        $export->setOPMLHead( array( 'ownerEmail' => 'me@k1c.example.invalid' ) );
        $this->assertSame( 'me@k1c.example.invalid', $export->opmlHead()['ownerEmail'] );
        $this->assertSame( '', $export->opmlHead()['ownerName'], 'the head is replaced as a whole' );
    }

    // ---------------------------------------------------------------- helpers

    public function testElementLeavesEmptyValuesOut()
    {
        $doc = new DOMDocument();
        $root = $doc->createElement( 'r' );
        $doc->appendChild( $root );
        eZRSSExport::element( $doc, $root, 'a', 'x & <y>' );
        eZRSSExport::element( $doc, $root, 'b', '   ' );
        eZRSSExport::element( $doc, $root, 'c', 0 );
        $this->assertSame( '<r><a>x &amp; &lt;y&gt;</a><c>0</c></r>', $doc->saveXML( $root ) );
    }

    public function testPodcastTextOfMissingAndPlainAttributes()
    {
        $this->assertSame( '', eZRSSExport::podcastText( array(), 'title' ) );
        $this->assertSame( '', eZRSSExport::podcastText( array( 'title' => null ), false ) );
    }

    public function testEmptyDocumentsAreWellFormed()
    {
        foreach ( array( eZRSSExport::emptyPodcast( 'K1c <&> show' ), eZRSSExport::emptyOPML( 'K1c <&> list' ) ) as $xml )
        {
            $doc = new DOMDocument();
            $this->assertTrue( $doc->loadXML( $xml ) );
            $this->assertStringContainsString( 'K1c &lt;&amp;&gt;', $xml );
        }
    }
}
