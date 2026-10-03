<?php
/**
 * The feed services. The subtree feeds are produced from articles created in a test folder under the Media root;
 * the export management creates a test export and removes it again. The RSS imports are only read (and never run).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/../commerce/expCommerceTestCase.php';

class expFeedServicesTest extends expCommerceTestCase
{
    protected $exportIds = array();

    public function tearDown(): void
    {
        if ( self::$bootError === null )
            foreach ( $this->exportIds as $id )
                if ( $e = eZRSSExport::fetch( $id ) )
                    $e->removeThis();
        parent::tearDown();
    }

    protected function folderWithItems( $n = 3 )
    {
        $f = $this->testFolder();
        $ids = array();
        for ( $i = 1; $i <= $n; $i++ )
            $ids[] = (int)$this->createObject( $f, 'comment', array( 'subject' => "Item $i & <b>more</b>", 'author' => 'Feed Tester', 'message' => "Text of item $i" ) )->attribute( 'main_node_id' );
        return array( $f, $ids );
    }

    public function testItemsOfASubtreeNewestFirst()
    {
        list( $f, $ids ) = $this->folderWithItems( 3 );
        $r = $this->call( 'expFeedServices', 'items', array( $f, 2, 0 ) );
        $this->assertTrue( $r['ok'] );
        $this->assertSame( 3, $r['meta']['total'] );
        $this->assertCount( 2, $r['data'] );
        $this->assertSame( $ids[2], $r['data'][0]['node_id'] );
        $this->assertStringStartsWith( 'https://', $r['data'][0]['url'] );
        $this->assertNotNull( $r['data'][0]['published'] );
        $this->assertSame( 'Text of item 3', $r['data'][0]['summary'] );
        $page = $this->call( 'expFeedServices', 'items', array( $f, 2, 2 ) );
        $this->assertCount( 1, $page['data'] );
        $this->assertFalse( $page['meta']['has_more'] );
    }

    public function testItemsCanBeFilteredByClass()
    {
        list( $f ) = $this->folderWithItems( 2 );
        $this->assertCount( 2, $this->ok( 'expFeedServices', 'items', array( $f, 10, 0, 'comment' ) ) );
        $this->assertCount( 0, $this->ok( 'expFeedServices', 'items', array( $f, 10, 0, 'product' ) ) );
    }

    public function testRssOfASubtreeIsWellFormed()
    {
        list( $f ) = $this->folderWithItems( 2 );
        $r = $this->ok( 'expFeedServices', 'rss', array( $f, 10 ) );
        $this->assertStringStartsWith( 'application/rss+xml', $r['content_type'] );
        $d = new DOMDocument();
        $this->assertTrue( $d->loadXML( $r['content'] ) );
        $this->assertSame( 'rss', $d->documentElement->nodeName );
        $this->assertSame( 2, $d->getElementsByTagName( 'item' )->length );
        $this->assertStringContainsString( 'Item 2 &amp; &lt;b&gt;more&lt;/b&gt;', $r['content'], 'text is escaped' );
        $this->assertSame( 1, $d->getElementsByTagName( 'channel' )->length );
    }

    public function testAtomOfASubtreeIsWellFormed()
    {
        list( $f ) = $this->folderWithItems( 2 );
        $r = $this->ok( 'expFeedServices', 'atom', array( $f, 10 ) );
        $this->assertStringStartsWith( 'application/atom+xml', $r['content_type'] );
        $d = new DOMDocument();
        $this->assertTrue( $d->loadXML( $r['content'] ) );
        $this->assertSame( 'http://www.w3.org/2005/Atom', $d->documentElement->namespaceURI );
        $this->assertSame( 2, $d->getElementsByTagNameNS( 'http://www.w3.org/2005/Atom', 'entry' )->length );
        $this->assertSame( 1, $d->getElementsByTagNameNS( 'http://www.w3.org/2005/Atom', 'updated' )->length > 0 ? 1 : 0 );
    }

    public function testJsonFeedFollowsTheSpecification()
    {
        list( $f, $ids ) = $this->folderWithItems( 2 );
        $j = $this->ok( 'expFeedServices', 'json', array( $f, 10 ) );
        $this->assertSame( 'https://jsonfeed.org/version/1.1', $j['version'] );
        $this->assertNotEmpty( $j['title'] );
        $this->assertCount( 2, $j['items'] );
        $this->assertSame( (string)$ids[1], $j['items'][0]['id'] );
        $this->assertArrayHasKey( 'content_text', $j['items'][0] );
        $this->assertArrayHasKey( 'date_published', $j['items'][0] );
        $this->assertSame( eZContentObject::fetch( (int)eZUser::fetchByName( 'admin' )->attribute( 'contentobject_id' ) )->attribute( 'name' ), $j['items'][0]['authors'][0]['name'] );
        $this->assertNotNull( json_encode( $j ) );
    }

    public function testFeedsOfAnEmptyFolderAreValid()
    {
        $f = $this->testFolder();
        $this->assertSame( array(), $this->ok( 'expFeedServices', 'json', array( $f ) )['items'] );
        $d = new DOMDocument();
        $this->assertTrue( $d->loadXML( $this->ok( 'expFeedServices', 'rss', array( $f ) )['content'] ) );
        $this->assertTrue( $d->loadXML( $this->ok( 'expFeedServices', 'atom', array( $f ) )['content'] ) );
    }

    public function testFeedLimitsAndNodes()
    {
        $this->fails( 400, 'expFeedServices', 'rss', array( $this->testFolder(), 0 ) );
        $this->fails( 404, 'expFeedServices', 'rss', array( 99999999 ) );
        $this->fails( 400, 'expFeedServices', 'json', array( 'abc' ) );
        list( $f ) = $this->folderWithItems( 3 );
        $this->assertCount( 3, $this->ok( 'expFeedServices', 'json', array( $f, 500 ) )['items'], 'the limit is capped, not refused' );
    }

    public function testAnonymousMayReadFeedsOfReadableContentOnly()
    {
        list( $f ) = $this->folderWithItems( 1 );
        $this->loginAnonymous();
        $r = $this->call( 'expFeedServices', 'json', array( $f ) );
        $this->assertTrue( $r['ok'] || $r['error']['code'] === 403, 'readable or forbidden, never a fault' );
        $this->assertTrue( $this->call( 'expFeedServices', 'formats' )['ok'] );
    }

    public function testDiscoverOffersTheGenericFeeds()
    {
        list( $f ) = $this->folderWithItems( 1 );
        $d = $this->ok( 'expFeedServices', 'discover', array( $f ) );
        $this->assertSame( array( 'rss', 'atom', 'json' ), array_slice( array_column( $d, 'type' ), 0, 3 ) );
        $this->assertStringContainsString( (string)$f, $d[0]['url'] );
    }

    // ---------------------------------------------------------------- exports

    public function testExportsAreListedAndViewed()
    {
        $r = $this->call( 'expFeedServices', 'exports', array( 50, 0 ) );
        $this->assertTrue( $r['ok'] );
        $this->assertGreaterThanOrEqual( 1, $r['meta']['total'] );
        $e = $this->ok( 'expFeedServices', 'export', array( $r['data'][0]['id'] ) );
        $this->assertArrayHasKey( 'sources', $e );
        $by = $this->ok( 'expFeedServices', 'exportByUrl', array( $e['access_url'] ) );
        $this->assertSame( $e['id'], $by['id'] );
        $this->fails( 404, 'expFeedServices', 'export', array( 99999999 ) );
        $this->fails( 404, 'expFeedServices', 'exportByUrl', array( 'no-such-export-address' ) );
    }

    public function testFormatsAreNamed()
    {
        $f = $this->ok( 'expFeedServices', 'formats' );
        $this->assertContains( '2.0', array_column( $f, 'value' ) );
        $this->assertContains( 'ATOM', array_column( $f, 'value' ) );
    }

    public function testCreateUpdateAndRemoveAnExport()
    {
        list( $f ) = $this->folderWithItems( 2 );
        $e = $this->ok( 'expFeedServices', 'createExport', array(), array( 'title' => 'Test export', 'access_url' => 'exptest_' . uniqid(), 'source_node_id' => $f, 'number_of_objects' => 5 ) );
        $this->exportIds[] = $e['id'];
        $this->assertTrue( $e['active'] );
        $this->assertSame( 5, $e['number_of_objects'] );
        $this->assertCount( 1, $e['sources'] );
        $this->assertSame( $f, $e['sources'][0]['source_node_id'] );
        $u = $this->ok( 'expFeedServices', 'updateExport', array(), array( 'id' => $e['id'], 'title' => 'Renamed', 'rss_version' => 'ATOM', 'number_of_objects' => 7 ) );
        $this->assertSame( 'Renamed', $u['title'] );
        $this->assertSame( 'ATOM', $u['rss_version'] );
        $this->assertFalse( $this->ok( 'expFeedServices', 'setActive', array(), array( 'id' => $e['id'], 'active' => 'false' ) )['active'] );
        $this->fails( 404, 'expFeedServices', 'output', array( $e['access_url'] ) );
        $this->assertTrue( $this->ok( 'expFeedServices', 'setActive', array(), array( 'id' => $e['id'], 'active' => 'true' ) )['active'] );
        $this->assertSame( array( 'removed' => $e['id'] ), $this->ok( 'expFeedServices', 'removeExport', array(), array( 'id' => $e['id'] ) ) );
        $this->fails( 404, 'expFeedServices', 'export', array( $e['id'] ) );
    }

    public function testExportOutputIsTheDocumentOfTheExport()
    {
        list( $f ) = $this->folderWithItems( 2 );
        $url = 'exptest_' . uniqid();
        $e = $this->ok( 'expFeedServices', 'createExport', array(), array( 'title' => 'Output export', 'access_url' => $url, 'source_node_id' => $f ) );
        $this->exportIds[] = $e['id'];
        $r = $this->call( 'expFeedServices', 'output', array( $url ) );
        $this->assertTrue( $r['ok'], json_encode( $r['error'] ?? null ) );
        $d = new DOMDocument();
        $this->assertTrue( $d->loadXML( $r['data']['content'] ) );
        $this->assertStringContainsString( 'xml', $r['data']['content_type'] );
    }

    public function testSourcesCanBeAddedAndRemoved()
    {
        list( $f ) = $this->folderWithItems( 1 );
        $e = $this->ok( 'expFeedServices', 'createExport', array(), array( 'title' => 'Sources', 'access_url' => 'exptest_' . uniqid(), 'source_node_id' => $f ) );
        $this->exportIds[] = $e['id'];
        $e2 = $this->ok( 'expFeedServices', 'addSource', array(), array( 'id' => $e['id'], 'source_node_id' => 43, 'subnodes' => 'false' ) );
        $this->assertCount( 2, $e2['sources'] );
        $second = end( $e2['sources'] );
        $this->assertFalse( $second['subnodes'] );
        $e3 = $this->ok( 'expFeedServices', 'removeSource', array(), array( 'id' => $e['id'], 'source_id' => $second['id'] ) );
        $this->assertCount( 1, $e3['sources'] );
        $this->fails( 404, 'expFeedServices', 'removeSource', array(), array( 'id' => $e['id'], 'source_id' => 99999999 ) );
    }

    public function testExportInputIsValidated()
    {
        list( $f ) = $this->folderWithItems( 1 );
        $this->fails( 422, 'expFeedServices', 'createExport', array(), array( 'title' => 't', 'access_url' => 'bad url!', 'source_node_id' => $f ) );
        $this->fails( 404, 'expFeedServices', 'createExport', array(), array( 'title' => 't', 'access_url' => 'ok_url', 'source_node_id' => 99999999 ) );
        $this->fails( 400, 'expFeedServices', 'createExport', array(), array( 'title' => 't', 'source_node_id' => $f ) );
        $e = $this->ok( 'expFeedServices', 'createExport', array(), array( 'title' => 'dup', 'access_url' => 'exptest_dup' . uniqid(), 'source_node_id' => $f ) );
        $this->exportIds[] = $e['id'];
        $this->fails( 409, 'expFeedServices', 'createExport', array(), array( 'title' => 'dup2', 'access_url' => $e['access_url'], 'source_node_id' => $f ) );
        $this->fails( 422, 'expFeedServices', 'updateExport', array(), array( 'id' => $e['id'], 'rss_version' => '9.9' ) );
        $this->fails( 422, 'expFeedServices', 'updateExport', array(), array( 'id' => $e['id'], 'number_of_objects' => 0 ) );
    }

    public function testAnonymousCannotManageExports()
    {
        list( $f ) = $this->folderWithItems( 1 );
        $this->loginAnonymous();
        $this->fails( 401, 'expFeedServices', 'createExport', array(), array( 'title' => 't', 'access_url' => 'x_y', 'source_node_id' => $f ) );
        $this->fails( 401, 'expFeedServices', 'imports' );
        $this->assertTrue( $this->call( 'expFeedServices', 'exports' )['ok'] );
    }

    // ---------------------------------------------------------------- imports

    public function testImportsAreListed()
    {
        $r = $this->call( 'expFeedServices', 'imports', array( 20, 0 ) );
        $this->assertTrue( $r['ok'] );
        $this->assertSame( count( $r['data'] ), min( 20, $r['meta']['total'] ) );
    }

    public function testImportLookupsOfAMissingImportAre404()
    {
        $this->fails( 404, 'expFeedServices', 'import', array( 99999999 ) );
        $this->fails( 404, 'expFeedServices', 'importStatus', array( 99999999 ) );
        $this->fails( 404, 'expFeedServices', 'importCheck', array( 99999999 ) );
        $this->fails( 404, 'expFeedServices', 'importSetActive', array(), array( 'id' => 99999999, 'active' => 'true' ) );
    }

    public function testImportStatusAndCheckOfATestImport()
    {
        $i = eZRSSImport::create( (int)eZUser::currentUser()->attribute( 'contentobject_id' ) );
        $i->setAttribute( 'name', 'Test import' );
        $i->setAttribute( 'url', 'file:///etc/passwd' );
        $i->setAttribute( 'status', eZRSSImport::STATUS_VALID );
        $i->setAttribute( 'active', 0 );
        $i->store();
        $id = (int)$i->attribute( 'id' );
        try
        {
            $s = $this->ok( 'expFeedServices', 'importStatus', array( $id ) );
            $this->assertSame( 0, $s['objects'] );
            $this->assertFalse( $s['active'] );
            $c = $this->ok( 'expFeedServices', 'importCheck', array( $id, 'true' ) );
            $this->assertFalse( $c['fetchable'], 'only http and https sources are fetched' );
            $this->assertFalse( $c['fetched'] );
            $on = $this->ok( 'expFeedServices', 'importSetActive', array(), array( 'id' => $id, 'active' => 'true' ) );
            $this->assertTrue( $on['active'] );
            $this->assertSame( $id, $this->ok( 'expFeedServices', 'import', array( $id ) )['id'] );
        }
        finally
        {
            eZPersistentObject::removeObject( eZRSSImport::definition(), array( 'id' => $id ) );
        }
    }
}
