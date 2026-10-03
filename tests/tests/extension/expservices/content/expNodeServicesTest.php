<?php
/**
 * Node services: reads against the stable nodes, writes on test content under the Media root.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expContentServicesTestCase.php';

class expNodeServicesTest extends expContentServicesTestCase
{
    public function testDeclarations()
    {
        $this->assertDeclarations( 'expNodeServices', 45 );
    }

    public function testGetMediaRoot()
    {
        $n = $this->ok( 'expNodeServices', 'get', array( 43 ) );
        $this->assertSame( 43, $n['node_id'] );
        $this->assertSame( 'folder', $n['class'] );
        $this->assertTrue( $n['is_container'] );
        $this->assertSame( 1, $n['depth'] );
    }

    public function testGetMissingNode()
    {
        $this->fails( 404, 'expNodeServices', 'get', array( 99999999 ) );
    }

    public function testGetBadArgument()
    {
        $this->fails( 400, 'expNodeServices', 'get', array( 'abc' ) );
        $this->fails( 400, 'expNodeServices', 'get', array() );
    }

    public function testChildrenPagedAndSorted()
    {
        $e = $this->envelope( 'expNodeServices', 'children', array( 2, 'name', 'asc', 3, 0 ) );
        $this->assertLessThanOrEqual( 3, count( $e['data'] ) );
        $this->assertSame( 3, $e['meta']['limit'] );
        $this->assertGreaterThan( 0, $e['meta']['total'] );
        foreach ( $e['data'] as $n )
            $this->assertSame( 2, $n['parent_node_id'] );
    }

    public function testChildrenFilteredByClass()
    {
        $e = $this->envelope( 'expNodeServices', 'children', array( 2, 'name', 'asc', 50, 0, '{"class":["folder"]}' ) );
        foreach ( $e['data'] as $n )
            $this->assertSame( 'folder', $n['class'] );
    }

    public function testChildrenBadSort()
    {
        $this->fails( 400, 'expNodeServices', 'children', array( 2, 'nonsense' ) );
        $this->fails( 400, 'expNodeServices', 'children', array( 2, 'name', 'sideways' ) );
    }

    public function testChildrenCountMatchesPage()
    {
        $count = $this->ok( 'expNodeServices', 'childrenCount', array( 2 ) );
        $e = $this->envelope( 'expNodeServices', 'children', array( 2, 'name', 'asc', 1, 0 ) );
        $this->assertSame( $count['count'], $e['meta']['total'] );
    }

    public function testChildrenNames()
    {
        $e = $this->envelope( 'expNodeServices', 'childrenNames', array( 2, 5 ) );
        $this->assertLessThanOrEqual( 5, count( $e['data'] ) );
        $this->assertArrayHasKey( 'name', $e['data'][0] );
    }

    public function testSubtreeDepthLimitedAndCount()
    {
        $e = $this->envelope( 'expNodeServices', 'subtree', array( 2, 'path', 'asc', 5, 0, '{"depth":2}' ) );
        foreach ( $e['data'] as $n )
            $this->assertLessThanOrEqual( 4, $n['depth'] );
        $c = $this->ok( 'expNodeServices', 'subtreeCount', array( 2, '{"depth":2}' ) );
        $this->assertSame( $c['count'], $e['meta']['total'] );
    }

    public function testPathAndBreadcrumb()
    {
        $path = $this->ok( 'expNodeServices', 'path', array( 43 ) );
        $this->assertSame( 43, end( $path )['node_id'] );
        $crumbs = $this->ok( 'expNodeServices', 'breadcrumb', array( 43 ) );
        $this->assertSame( 43, end( $crumbs )['node_id'] );
    }

    public function testParentAndExists()
    {
        $p = $this->ok( 'expNodeServices', 'parent', array( 43 ) );
        $this->assertSame( 1, $p['node_id'] );
        $this->assertTrue( $this->ok( 'expNodeServices', 'exists', array( 43 ) )['exists'] );
        $this->assertFalse( $this->ok( 'expNodeServices', 'exists', array( 99999999 ) )['exists'] );
    }

    public function testGetByPathAndObjectAndRemoteId()
    {
        $node = eZContentObjectTreeNode::fetch( 2 );
        $media = eZContentObjectTreeNode::fetch( 43 );
        $byPath = $this->ok( 'expNodeServices', 'getByPath', array( ltrim( $media->urlAlias(), '/' ) ) );
        $this->assertSame( 43, $byPath['node_id'] );
        $byObject = $this->ok( 'expNodeServices', 'getByObject', array( $node->attribute( 'contentobject_id' ) ) );
        $this->assertSame( 2, $byObject['node_id'] );
        $byRemote = $this->ok( 'expNodeServices', 'getByRemoteId', array( $node->attribute( 'remote_id' ) ) );
        $this->assertSame( 2, $byRemote['node_id'] );
        $this->fails( 404, 'expNodeServices', 'getByRemoteId', array( 'no-such-remote-id' ) );
    }

    public function testGetMany()
    {
        $r = $this->ok( 'expNodeServices', 'getMany', array( '2,43,99999999' ) );
        $this->assertCount( 2, $r );
    }

    public function testDataMapAndAttribute()
    {
        $map = $this->ok( 'expNodeServices', 'dataMap', array( 43 ) );
        $this->assertArrayHasKey( 'name', $map );
        $this->assertSame( 'ezstring', $map['name']['data_type'] );
        $attr = $this->ok( 'expNodeServices', 'attribute', array( 43, 'name' ) );
        $this->assertSame( 'Media', $attr['value'] );
        $this->fails( 404, 'expNodeServices', 'attribute', array( 43, 'no_such_attribute' ) );
    }

    public function testRightsAndCreatableClasses()
    {
        $rights = $this->ok( 'expNodeServices', 'rights', array( 43 ) );
        $this->assertTrue( $rights['read'] );
        $this->assertTrue( $rights['create'] );
        $classes = $this->ok( 'expNodeServices', 'creatableClasses', array( 43 ) );
        $this->assertContains( 'folder', array_column( $classes, 'identifier' ) );
    }

    public function testCountByClassAndLatest()
    {
        $facet = $this->ok( 'expNodeServices', 'countByClass', array( 43 ) );
        $this->assertNotEmpty( $facet );
        $this->assertIsInt( reset( $facet ) );
        $latest = $this->ok( 'expNodeServices', 'latest', array( 2, 3 ) );
        $this->assertLessThanOrEqual( 3, count( $latest ) );
    }

    public function testVisibilitySortInfoUrlNames()
    {
        $v = $this->ok( 'expNodeServices', 'visibility', array( 43 ) );
        $this->assertFalse( $v['is_hidden'] );
        $s = $this->ok( 'expNodeServices', 'sortInfo', array( 43 ) );
        $this->assertContains( 'name', $s['fields'] );
        $this->assertNotEmpty( $this->ok( 'expNodeServices', 'url', array( 43 ) )['system_url'] );
        $this->assertNotEmpty( (array)$this->ok( 'expNodeServices', 'names', array( 43 ) ) );
    }

    public function testTreeIsNested()
    {
        $t = $this->ok( 'expNodeServices', 'tree', array( 2, 1, 3 ) );
        $this->assertSame( 2, $t['node_id'] );
        $this->assertLessThanOrEqual( 3, count( $t['children'] ) );
    }

    public function testWritesNeedPostAndToken()
    {
        expServiceBase::$trustRequest = null;
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->fails( 403, 'expNodeServices', 'hide', array( 43 ) );
        unset( $_SERVER['REQUEST_METHOD'] );
    }

    public function testWritesDeniedToAnonymous()
    {
        $this->loginAnonymous();
        $this->fails( 403, 'expNodeServices', 'create', array( 43 ), array( 'class' => 'folder', 'attributes' => '{"name":"x"}' ) );
        $this->fails( 401, 'expNodeServices', 'rights', array( 43 ) );
    }

    public function testCreateUpdateRenameAndRemove()
    {
        $folder = $this->testFolder();
        $n = $this->ok( 'expNodeServices', 'create', array( $folder ), array( 'class' => 'folder', 'attributes' => '{"name":"Created by a service"}' ) );
        $this->createdObjects[] = $n['object_id'];
        $this->assertSame( 'Created by a service', $n['name'] );
        $this->assertSame( $folder, $n['parent_node_id'] );
        $u = $this->ok( 'expNodeServices', 'update', array( $n['node_id'] ), array( 'attributes' => '{"name":"Changed by a service"}' ) );
        $this->assertSame( 'Changed by a service', $u['name'] );
        $r = $this->ok( 'expNodeServices', 'rename', array( $n['node_id'] ), array( 'name' => 'Renamed' ) );
        $this->assertSame( 'Renamed', $r['name'] );
        $gone = $this->ok( 'expNodeServices', 'remove', array( $n['node_id'] ), array( 'move_to_trash' => '0', 'mode' => 'now' ) );
        $this->assertSame( array( $n['node_id'] ), $gone['removed'] );
        $this->fails( 404, 'expNodeServices', 'get', array( $n['node_id'] ) );
    }

    public function testCreateValidatesInput()
    {
        $folder = $this->testFolder();
        $this->fails( 422, 'expNodeServices', 'create', array( $folder ), array( 'class' => 'folder', 'attributes' => '{"nope":"x"}' ) );
        $this->fails( 422, 'expNodeServices', 'create', array( $folder ), array( 'class' => 'folder', 'attributes' => '{}' ) );
        $this->fails( 404, 'expNodeServices', 'create', array( $folder ), array( 'class' => 'no_such_class', 'attributes' => '{"name":"x"}' ) );
        $this->fails( 400, 'expNodeServices', 'create', array( $folder ), array( 'attributes' => '{"name":"x"}' ) );
        $this->fails( 422, 'expNodeServices', 'create', array( $folder ), array( 'class' => 'image', 'attributes' => '{"name":"x","image":"/etc/passwd"}' ) );
    }

    public function testMoveCopySwapAndHide()
    {
        $a = $this->createFolder( 'A ' . uniqid() );
        $b = $this->createFolder( 'B ' . uniqid() );
        $c = $this->createFolder( 'C ' . uniqid(), $a );
        $moved = $this->ok( 'expNodeServices', 'move', array( $c, $b ), array( 'mode' => 'now' ) );
        $this->assertSame( $b, $moved['parent_node_id'] );
        $this->fails( 422, 'expNodeServices', 'move', array( $b, $c ), array( 'mode' => 'now' ) );
        $this->fails( 409, 'expNodeServices', 'move', array( $c, $b ), array( 'mode' => 'now' ) );
        $copy = $this->ok( 'expNodeServices', 'copy', array( $c, $a ) );
        $this->createdObjects[] = $copy['object_id'];
        $this->assertSame( $a, $copy['parent_node_id'] );
        $this->assertNotSame( $c, $copy['node_id'] );
        $swap = $this->ok( 'expNodeServices', 'swap', array( $a, $b ) );
        $this->assertCount( 2, $swap );
        $h = $this->ok( 'expNodeServices', 'hide', array( $a ), array( 'mode' => 'now' ) );
        $this->assertTrue( $h['is_hidden'] );
        $this->fails( 409, 'expNodeServices', 'hide', array( $a ), array( 'mode' => 'now' ) );
        $r = $this->ok( 'expNodeServices', 'reveal', array( $a ), array( 'mode' => 'now' ) );
        $this->assertFalse( $r['is_hidden'] );
        $t = $this->ok( 'expNodeServices', 'toggleHide', array( $a ) );
        $this->assertTrue( $t['is_hidden'] );
        $this->ok( 'expNodeServices', 'toggleHide', array( $a ) );
    }

    public function testSortPriorityAndRemoteId()
    {
        $a = $this->createFolder();
        $c1 = $this->createFolder( 'one ' . uniqid(), $a );
        $c2 = $this->createFolder( 'two ' . uniqid(), $a );
        $s = $this->ok( 'expNodeServices', 'setSort', array( $a, 'name', 'desc' ) );
        $this->assertSame( 'name', $s['sort_field'] );
        $this->assertSame( 'desc', $s['sort_order'] );
        $this->fails( 400, 'expNodeServices', 'setSort', array( $a, 'bogus' ) );
        $p = $this->ok( 'expNodeServices', 'setPriority', array( $c1, 7 ) );
        $this->assertSame( 7, $p['priority'] );
        $this->ok( 'expNodeServices', 'setPriorities', array( $a ), array( 'priorities' => json_encode( array( $c1 => 3, $c2 => 4 ) ) ) );
        $this->assertSame( 4, eZContentObjectTreeNode::fetch( $c2 )->attribute( 'priority' ) );
        $remote = 'exptest-' . uniqid();
        $r = $this->ok( 'expNodeServices', 'setRemoteId', array( $c1, $remote ) );
        $this->assertSame( $remote, $r['remote_id'] );
        $this->fails( 409, 'expNodeServices', 'setRemoteId', array( $c2, $remote ) );
        $this->fails( 422, 'expNodeServices', 'setRemoteId', array( $c2, 'bad id!' ) );
    }

    public function testSiblingsAndNeighbours()
    {
        $a = $this->createFolder( 'a1 ' . uniqid() );
        $b = $this->createFolder( 'b1 ' . uniqid() );
        $e = $this->envelope( 'expNodeServices', 'siblings', array( $a ) );
        $this->assertContains( $b, array_column( $e['data'], 'node_id' ) );
        $this->assertNotContains( $a, array_column( $e['data'], 'node_id' ) );
        $n = $this->ok( 'expNodeServices', 'neighbours', array( $a ) );
        $this->assertArrayHasKey( 'previous', $n );
        $this->assertArrayHasKey( 'next', $n );
    }

    public function testRemoveRefusesRootsAndMoveToTrash()
    {
        $this->fails( 403, 'expNodeServices', 'remove', array( 43 ), array( 'mode' => 'now' ) );
        $n = $this->createFolder();
        $o = $this->objectOf( $n );
        $this->ok( 'expNodeServices', 'remove', array( $n ), array( 'mode' => 'now' ) );
        $this->assertNotEmpty( eZContentObjectTrashNode::fetchByContentObjectID( $o ) );
    }

    public function testFindAndByClassAndHidden()
    {
        $name = 'zzfind' . uniqid();
        $n = $this->createFolder( $name );
        $found = $this->envelope( 'expNodeServices', 'find', array( $this->testFolder(), 'zzfind' ) );
        $this->assertContains( $n, array_column( $found['data'], 'node_id' ) );
        $byClass = $this->envelope( 'expNodeServices', 'byClass', array( $this->testFolder(), 'folder' ) );
        $this->assertContains( $n, array_column( $byClass['data'], 'node_id' ) );
        $since = $this->envelope( 'expNodeServices', 'modifiedSince', array( $this->testFolder(), '-1 hour' ) );
        $this->assertGreaterThanOrEqual( 1, $since['meta']['total'] );
        $this->ok( 'expNodeServices', 'hide', array( $n ), array( 'mode' => 'now' ) );
        $hidden = $this->envelope( 'expNodeServices', 'hidden', array( $this->testFolder() ) );
        $this->assertContains( $n, array_column( $hidden['data'], 'node_id' ) );
    }
}
