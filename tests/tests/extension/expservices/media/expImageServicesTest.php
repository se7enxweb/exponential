<?php
require_once __DIR__ . '/expMediaTestCase.php';

/** expimage: aliases, image information and URLs of node images (read only against existing content). */
class expImageServicesTest extends expMediaTestCase
{
    public function testEveryServiceIsDeclared()
    {
        $this->assertDeclared( 'expImageServices', 17 );
    }

    public function testAliasesListTheConfiguredAliases()
    {
        $r = $this->ok( 'expImageServices', 'aliases' );
        $names = array_column( $r['data'], 'name' );
        $this->assertContains( 'small', $names );
        $this->assertContains( 'large', $names );
    }

    public function testAliasNamesMatchAliases()
    {
        $names = $this->ok( 'expImageServices', 'aliasnames' )['data'];
        $this->assertSame( $names, array_column( $this->ok( 'expImageServices', 'aliases' )['data'], 'name' ) );
    }

    public function testOneAliasHasItsFilters()
    {
        $a = $this->ok( 'expImageServices', 'alias', array( 'small' ) )['data'];
        $this->assertSame( 'small', $a['name'] );
        $this->assertNotEmpty( $a['filters'] );
    }

    public function testUnknownAliasIs404()
    {
        $this->fails( 404, 'expImageServices', 'alias', array( 'no_such_alias' ) );
    }

    public function testAliasNeedsItsName()
    {
        $this->fails( 400, 'expImageServices', 'alias' );
    }

    public function testFormatsAndFilters()
    {
        $this->assertContains( 'image/jpeg', $this->ok( 'expImageServices', 'formats' )['data']['common'] );
        $this->assertNotEmpty( $this->ok( 'expImageServices', 'filters' )['data'] );
    }

    public function testQualityOfJpeg()
    {
        $q = $this->ok( 'expImageServices', 'quality' )['data'];
        $this->assertArrayHasKey( 'image/jpeg', $q );
    }

    public function testStatsCountFilesAndImages()
    {
        $s = $this->ok( 'expImageServices', 'stats' )['data'];
        $this->assertGreaterThan( 0, $s['files'] );
        $this->assertGreaterThan( 0, $s['attributes'] );
        $this->assertGreaterThan( 0, $s['aliases'] );
    }

    public function testListIsPagedAndCarriesImages()
    {
        $r = $this->ok( 'expImageServices', 'list', array( 43, 5, 0, 'small' ) );
        $this->assertPaged( $r );
        $this->assertLessThanOrEqual( 5, $r['meta']['count'] );
        foreach ( $r['data'] as $item )
            $this->assertSame( 'image', $item['class'] );
    }

    public function testSearchNeedsTwoCharacters()
    {
        $this->fails( 422, 'expImageServices', 'search', array( 'a' ) );
    }

    public function testSearchIsPaged()
    {
        $this->assertPaged( $this->ok( 'expImageServices', 'search', array( 'ab', 43, 3, 0 ) ) );
    }

    public function testAttributesOfAnImageNode()
    {
        list( $node ) = $this->nodeWith( 'ezimage' );
        $list = $this->ok( 'expImageServices', 'attributes', array( $node->attribute( 'node_id' ) ) )['data'];
        $this->assertNotEmpty( $list );
        $this->assertTrue( $list[0]['has_content'] );
        $this->assertArrayHasKey( 'original', $list[0] );
    }

    public function testInfoHasTheOriginal()
    {
        list( $node, $attr ) = $this->nodeWith( 'ezimage' );
        $i = $this->ok( 'expImageServices', 'info', array( $node->attribute( 'node_id' ), $attr->contentClassAttributeIdentifier() ) )['data'];
        $this->assertSame( (int)$node->attribute( 'node_id' ), $i['node_id'] );
        $this->assertGreaterThan( 0, $i['original']['width'] );
        $this->assertNotSame( '', $i['original']['url'] );
    }

    public function testUrlOfAnAlias()
    {
        list( $node, $attr ) = $this->nodeWith( 'ezimage' );
        $u = $this->ok( 'expImageServices', 'url', array( $node->attribute( 'node_id' ), $attr->contentClassAttributeIdentifier(), 'small' ) )['data'];
        $this->assertNotSame( '', $u['url'] );
        $this->assertStringStartsWith( 'http', $u['absolute_url'] );
    }

    public function testUrlsHaveEveryAlias()
    {
        list( $node, $attr ) = $this->nodeWith( 'ezimage' );
        $m = $this->ok( 'expImageServices', 'urls', array( $node->attribute( 'node_id' ), $attr->contentClassAttributeIdentifier() ) )['data'];
        $this->assertArrayHasKey( 'small', $m );
        $this->assertArrayHasKey( 'large', $m );
    }

    public function testGenerateReportsTheFile()
    {
        list( $node, $attr ) = $this->nodeWith( 'ezimage' );
        $g = $this->ok( 'expImageServices', 'generate', array( $node->attribute( 'node_id' ), $attr->contentClassAttributeIdentifier(), 'tiny' ) )['data'];
        $this->assertArrayHasKey( 'exists', $g );
    }

    public function testAltText()
    {
        list( $node, $attr ) = $this->nodeWith( 'ezimage' );
        $a = $this->ok( 'expImageServices', 'alt', array( $node->attribute( 'node_id' ), $attr->contentClassAttributeIdentifier() ) )['data'];
        $this->assertArrayHasKey( 'alternative_text', $a );
    }

    public function testFilesOfAnObject()
    {
        list( $node ) = $this->nodeWith( 'ezimage' );
        $this->assertPaged( $this->ok( 'expImageServices', 'files', array( $node->attribute( 'node_id' ), 10, 0 ) ) );
    }

    public function testByMimeCountsExtensions()
    {
        $list = $this->ok( 'expImageServices', 'bymime' )['data'];
        $this->assertNotEmpty( $list );
        $this->assertArrayHasKey( 'extension', $list[0] );
    }

    public function testNodeWithoutImageIs404()
    {
        $this->fails( 404, 'expImageServices', 'info', array( 2 ) );
    }

    public function testMissingNodeIs404()
    {
        $this->fails( 404, 'expImageServices', 'attributes', array( 99999999 ) );
    }

    public function testPurgeIsAWrite()
    {
        $this->assertTrue( expImageServices::$services['purge']['write'] );
        list( $node ) = $this->nodeWith( 'ezimage' );
        $this->fails( 403, 'expImageServices', 'purge', array( $node->attribute( 'node_id' ) ) );
    }

    public function testAnonymousListNeedsContentRead()
    {
        $this->loginAnonymous();
        $r = $this->call( 'expImageServices', 'stats' );
        $anon = eZUser::currentUser()->hasAccessTo( 'content', 'read' );
        $this->assertSame( $anon['accessWord'] !== 'no', $r['ok'] );
    }
}
