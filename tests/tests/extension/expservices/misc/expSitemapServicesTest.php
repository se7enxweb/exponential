<?php
require_once __DIR__ . '/../media/expMediaTestCase.php';

/** expsitemap: entries of the content tree as a sitemap lists them. */
class expSitemapServicesTest extends expMediaTestCase
{
    public function testEveryServiceIsDeclared()
    {
        $this->assertDeclared( 'expSitemapServices', 11 );
    }

    public function testConfig()
    {
        $c = $this->ok( 'expSitemapServices', 'config' )['data'];
        $this->assertGreaterThan( 0, $c['root_node'] );
        $this->assertContains( $c['protocol'], array( 'http', 'https' ) );
    }

    public function testEntriesArePagedWithSitemapFields()
    {
        $r = $this->ok( 'expSitemapServices', 'entries', array( 2, 4, 0 ) );
        $this->assertPaged( $r );
        $this->assertNotEmpty( $r['data'] );
        $e = $r['data'][0];
        foreach ( array( 'loc', 'lastmod', 'changefreq', 'priority', 'node_id' ) as $k )
            $this->assertArrayHasKey( $k, $e );
        $this->assertContains( $e['changefreq'], array( 'daily', 'weekly', 'monthly', 'yearly' ) );
        $this->assertGreaterThan( 0, $e['priority'] );
        $this->assertLessThanOrEqual( 1, $e['priority'] );
    }

    public function testCountAgreesWithEntries()
    {
        $count = $this->ok( 'expSitemapServices', 'count', array( 2 ) )['data'];
        $this->assertSame( $this->ok( 'expSitemapServices', 'entries', array( 2, 1, 0 ) )['meta']['total'], $count['count'] );
        $this->assertSame( (int)ceil( $count['count'] / 50000 ), $count['pages'] );
    }

    public function testEntryOfOneNode()
    {
        $e = $this->ok( 'expSitemapServices', 'entry', array( 2 ) )['data'];
        $this->assertSame( 2, $e['node_id'] );
        $this->assertStringStartsWith( 'http', $e['loc'] );
    }

    public function testMissingNodeIs404()
    {
        $this->fails( 404, 'expSitemapServices', 'entry', array( 99999999 ) );
    }

    public function testRecentIsNewestFirst()
    {
        $list = $this->ok( 'expSitemapServices', 'recent', array( 2, 5 ) )['data'];
        $dates = array_column( $list, 'lastmod' );
        $sorted = $dates;
        rsort( $sorted );
        $this->assertSame( $sorted, $dates );
    }

    public function testIndexPagesCoverAllEntries()
    {
        $idx = $this->ok( 'expSitemapServices', 'index', array( 2 ) );
        $this->assertSame( 0, $idx['data'][0]['offset'] );
        $this->assertSame( 50000, $idx['data'][0]['limit'] );
    }

    public function testFilesAndRobotsLine()
    {
        $this->assertIsArray( $this->ok( 'expSitemapServices', 'files' )['data'] );
        $this->assertStringStartsWith( 'Sitemap: http', $this->ok( 'expSitemapServices', 'robots' )['data']['line'] );
        $this->loginAnonymous();
        $this->assertTrue( $this->call( 'expSitemapServices', 'robots' )['ok'] );
    }

    public function testRootNodeAndClasses()
    {
        $this->assertGreaterThan( 0, $this->ok( 'expSitemapServices', 'rootnode' )['data']['node_id'] );
        $this->assertContains( $this->ok( 'expSitemapServices', 'classes' )['data']['type'], array( 'include', 'exclude' ) );
    }

    public function testAvailableNamesBothExtensions()
    {
        $a = $this->ok( 'expSitemapServices', 'available' )['data'];
        $this->assertArrayHasKey( 'bcgooglesitemaps', $a );
        $this->assertArrayHasKey( 'xrowmetadata', $a );
    }
}
