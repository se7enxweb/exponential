<?php
require_once __DIR__ . '/expMediaTestCase.php';

/** exptags: the tag tree, searches and the write cycle on test tags (created and deleted by the test). */
class expTagsServicesTest extends expMediaTestCase
{
    protected function aTag()
    {
        $roots = $this->ok( 'expTagsServices', 'roots', array( 5, 0 ) )['data'];
        $this->assertNotEmpty( $roots );
        return $roots[0];
    }

    /** Makes a top level test tag, deleted again in tearDown. */
    protected function testTag( $suffix = '' )
    {
        $name = 'expservices-a5-' . uniqid() . $suffix;
        $r = $this->ok( 'expTagsServices', 'add', array( 0 ), array( 'keyword' => $name ) );
        $id = $r['data']['id'];
        $this->cleanups[] = function () use ( $id ) {
            $t = eZTagsObject::fetchWithMainTranslation( $id );
            if ( $t )
                $t->recursivelyDeleteTag();
        };
        return $r['data'];
    }

    public function testEveryServiceIsDeclared()
    {
        $this->assertDeclared( 'expTagsServices', 27 );
    }

    public function testAvailable()
    {
        $r = $this->ok( 'expTagsServices', 'available' )['data'];
        $this->assertTrue( $r['available'] );
        $this->assertGreaterThan( 0, $r['tags'] );
    }

    public function testRootsArePaged()
    {
        $r = $this->ok( 'expTagsServices', 'roots', array( 1, 0 ) );
        $this->assertPaged( $r );
        $this->assertSame( 1, $r['meta']['count'] );
        $this->assertSame( 0, $r['data'][0]['parent_id'] );
    }

    public function testGetATag()
    {
        $root = $this->aTag();
        $t = $this->ok( 'expTagsServices', 'get', array( $root['id'] ) )['data'];
        $this->assertSame( $root['keyword'], $t['keyword'] );
        $this->assertArrayHasKey( 'related_count', $t );
        $this->assertContains( 'eng-US', $t['languages'] );
    }

    public function testMissingTagIs404()
    {
        $this->fails( 404, 'expTagsServices', 'get', array( 99999999 ) );
    }

    public function testChildrenOfARoot()
    {
        $root = $this->aTag();
        $r = $this->ok( 'expTagsServices', 'children', array( $root['id'], 100, 0 ) );
        $this->assertPaged( $r );
        $this->assertSame( $root['children_count'], $r['meta']['total'] );
        $this->assertSame( $root['children_count'], $this->ok( 'expTagsServices', 'childrencount', array( $root['id'] ) )['data']['count'] );
    }

    public function testTreeIsNested()
    {
        $root = $this->aTag();
        $t = $this->ok( 'expTagsServices', 'tree', array( $root['id'], 1 ) )['data'];
        $this->assertArrayHasKey( 'children', $t );
        $this->assertCount( $root['children_count'], $t['children'] );
        $all = $this->ok( 'expTagsServices', 'tree', array( 0, 1 ) )['data'];
        $this->assertNotEmpty( $all );
    }

    public function testPathEndsAtTheTag()
    {
        $root = $this->aTag();
        $kids = $this->ok( 'expTagsServices', 'children', array( $root['id'], 1, 0 ) )['data'];
        if ( !$kids )
            $this->markTestSkipped( 'the first root has no children' );
        $path = $this->ok( 'expTagsServices', 'path', array( $kids[0]['id'] ) )['data'];
        $this->assertSame( $kids[0]['id'], end( $path )['id'] );
        $this->assertSame( $root['id'], $path[0]['id'] );
        $this->assertSame( $root['id'], $this->ok( 'expTagsServices', 'parent', array( $kids[0]['id'] ) )['data']['id'] );
    }

    public function testSearchFindsAKeyword()
    {
        $root = $this->aTag();
        $r = $this->ok( 'expTagsServices', 'search', array( substr( $root['keyword'], 0, 3 ), 10, 0 ) );
        $this->assertPaged( $r );
        $this->assertContains( $root['id'], array_column( $r['data'], 'id' ) );
    }

    public function testSearchRejectsEmptyText()
    {
        $this->fails( 400, 'expTagsServices', 'search', array( '' ) );
    }

    public function testSearchTreatsPercentLiterally()
    {
        $this->assertSame( array(), $this->ok( 'expTagsServices', 'search', array( '%%%%', 10, 0 ) )['data'] );
    }

    public function testSuggestStartsWith()
    {
        $root = $this->aTag();
        $list = $this->ok( 'expTagsServices', 'suggest', array( substr( $root['keyword'], 0, 2 ), 20 ) )['data'];
        foreach ( $list as $t )
            $this->assertStringStartsWith( strtolower( substr( $root['keyword'], 0, 2 ) ), strtolower( $t['keyword'] ) );
    }

    public function testByKeywordIsExact()
    {
        $root = $this->aTag();
        $list = $this->ok( 'expTagsServices', 'bykeyword', array( $root['keyword'] ) )['data'];
        $this->assertContains( $root['id'], array_column( $list, 'id' ) );
    }

    public function testByRemoteAndPath()
    {
        $root = $this->aTag();
        $this->assertSame( $root['id'], $this->ok( 'expTagsServices', 'byremote', array( $root['remote_id'] ) )['data']['id'] );
        $this->assertSame( $root['id'], $this->ok( 'expTagsServices', 'bypath', array( $root['path_string'] ) )['data']['id'] );
        $this->fails( 400, 'expTagsServices', 'bypath', array( 'x/../y' ) );
        $this->fails( 404, 'expTagsServices', 'byremote', array( 'no-such-remote-id' ) );
    }

    public function testRelatedObjectsAreReadable()
    {
        $popular = $this->ok( 'expTagsServices', 'popular', array( 1 ) )['data'];
        $this->assertNotEmpty( $popular );
        $id = $popular[0]['tag']['id'];
        $r = $this->ok( 'expTagsServices', 'related', array( $id, 5, 0 ) );
        $this->assertPaged( $r );
        $this->assertSame( $this->ok( 'expTagsServices', 'relatedcount', array( $id ) )['data']['count'] >= $r['meta']['total'], true );
    }

    public function testStatsAndRecent()
    {
        $s = $this->ok( 'expTagsServices', 'stats' )['data'];
        $this->assertGreaterThan( 0, $s['tags'] );
        $this->assertLessThanOrEqual( 3, count( $this->ok( 'expTagsServices', 'recent', array( 3 ) )['data'] ) );
    }

    public function testOfNodeListsTagAttributes()
    {
        list( $node ) = $this->nodeWith( 'eztags', false );
        $list = $this->ok( 'expTagsServices', 'ofnode', array( $node->attribute( 'node_id' ) ) )['data'];
        $this->assertNotEmpty( $list );
        $this->assertArrayHasKey( 'tags', $list[0] );
    }

    public function testWritesNeedPost()
    {
        $this->fails( 403, 'expTagsServices', 'add', array( 0 ) );
        $this->fails( 403, 'expTagsServices', 'delete', array( 1 ) );
    }

    public function testWritesNeedThePolicy()
    {
        $this->loginAnonymous();
        $r = $this->call( 'expTagsServices', 'add', array( 0 ), array( 'keyword' => 'expservices-a5-anon' ) );
        $this->assertFalse( $r['ok'] );
        $this->assertContains( $r['error']['code'], array( 401, 403 ) );
    }

    public function testAddRenameSynonymDeleteCycle()
    {
        $tag = $this->testTag();
        $this->assertSame( 1, $tag['depth'] );
        $this->assertFalse( $tag['is_synonym'] );

        $newName = $tag['keyword'] . '-renamed';
        $renamed = $this->ok( 'expTagsServices', 'rename', array( $tag['id'] ), array( 'keyword' => $newName ) )['data'];
        $this->assertSame( $newName, $renamed['keyword'] );

        $syn = $this->ok( 'expTagsServices', 'addsynonym', array( $tag['id'] ), array( 'keyword' => $newName . '-syn' ) )['data'];
        $this->assertTrue( $syn['is_synonym'] );
        $this->assertSame( $tag['id'], $syn['main_tag_id'] );
        $this->assertCount( 1, $this->ok( 'expTagsServices', 'synonyms', array( $tag['id'] ) )['data'] );

        $this->assertSame( array( 'deleted' => $tag['id'] ), $this->ok( 'expTagsServices', 'delete', array( $tag['id'] ), array( 'confirm' => '1' ) )['data'] );
        $this->fails( 404, 'expTagsServices', 'get', array( $tag['id'] ) );
    }

    public function testChildTagAndDuplicateRefused()
    {
        $tag = $this->testTag();
        $child = $this->ok( 'expTagsServices', 'add', array( $tag['id'] ), array( 'keyword' => 'child' ) )['data'];
        $this->assertSame( $tag['id'], $child['parent_id'] );
        $this->assertSame( 2, $child['depth'] );
        $this->fails( 409, 'expTagsServices', 'add', array( $tag['id'] ), array( 'keyword' => 'child' ) );
        $this->fails( 422, 'expTagsServices', 'add', array( $tag['id'] ), array( 'keyword' => '   ' ) );
    }

    public function testAttachAndDetachOnATestObject()
    {
        $tag = $this->testTag();
        $object = $this->createTestObject( 'ng_recipe', array( 'title' => 'expservices A5 tags test' ) );
        $node = $object->attribute( 'main_node_id' );
        $a = $this->ok( 'expTagsServices', 'attach', array( $node, 'tags' ), array( 'tag' => $tag['id'] ) )['data'];
        $this->assertContains( $tag['id'], array_column( $a['tags'], 'id' ) );
        $of = $this->ok( 'expTagsServices', 'ofnode', array( $node ) )['data'];
        $found = false;
        foreach ( $of as $row )
            $found = $found || in_array( $tag['id'], array_column( $row['tags'], 'id' ) );
        $this->assertTrue( $found );
        $this->fails( 409, 'expTagsServices', 'attach', array( $node, 'tags' ), array( 'tag' => $tag['id'] ) );
        $d = $this->ok( 'expTagsServices', 'detach', array( $node, 'tags' ), array( 'tag' => $tag['id'] ) )['data'];
        $this->assertNotContains( $tag['id'], array_column( $d['tags'], 'id' ) );
        $this->fails( 404, 'expTagsServices', 'detach', array( $node, 'tags' ), array( 'tag' => $tag['id'] ) );
    }

    public function testAttachToANodeWithoutTagsAttributeIs404()
    {
        $tag = $this->testTag();
        $this->fails( 404, 'expTagsServices', 'attach', array( 2 ), array( 'tag' => $tag['id'] ) );
    }
}
