<?php
/**
 * Object services: reads against stable content, writes on test content under the Media root.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expContentServicesTestCase.php';

class expObjectServicesTest extends expContentServicesTestCase
{
    protected function mediaObject()
    {
        return (int)eZContentObjectTreeNode::fetch( 43 )->attribute( 'contentobject_id' );
    }

    public function testDeclarations()
    {
        $this->assertDeclarations( 'expObjectServices', 30 );
    }

    public function testGetAndSummary()
    {
        $id = $this->mediaObject();
        $o = $this->ok( 'expObjectServices', 'get', array( $id ) );
        $this->assertSame( $id, $o['id'] );
        $this->assertSame( 'folder', $o['class'] );
        $this->assertContains( 43, $o['node_ids'] );
        $s = $this->ok( 'expObjectServices', 'summary', array( $id ) );
        $this->assertSame( 43, $s['main_node_id'] );
        $this->fails( 404, 'expObjectServices', 'get', array( 99999999 ) );
        $this->fails( 400, 'expObjectServices', 'get', array( '-1' ) );
    }

    public function testExistsAndRemoteId()
    {
        $id = $this->mediaObject();
        $this->assertTrue( $this->ok( 'expObjectServices', 'exists', array( $id ) )['exists'] );
        $this->assertFalse( $this->ok( 'expObjectServices', 'exists', array( 99999999 ) )['exists'] );
        $remote = eZContentObject::fetch( $id )->attribute( 'remote_id' );
        $this->assertSame( $id, $this->ok( 'expObjectServices', 'getByRemoteId', array( $remote ) )['id'] );
        $this->fails( 404, 'expObjectServices', 'getByRemoteId', array( 'nope-nope' ) );
    }

    public function testListPagedFilteredAndCount()
    {
        $e = $this->envelope( 'expObjectServices', 'list', array( 'published', 'desc', 4, 0, '{"class":["folder"]}' ) );
        $this->assertLessThanOrEqual( 4, count( $e['data'] ) );
        foreach ( $e['data'] as $n )
            $this->assertSame( 'folder', $n['class'] );
        $c = $this->ok( 'expObjectServices', 'count', array( '{"class":["folder"]}' ) );
        $this->assertSame( $c['count'], $e['meta']['total'] );
    }

    public function testByClassAndOwnerAndRecent()
    {
        $e = $this->envelope( 'expObjectServices', 'byClass', array( 'folder', 3 ) );
        $this->assertLessThanOrEqual( 3, count( $e['data'] ) );
        $this->fails( 404, 'expObjectServices', 'byClass', array( 'no_such_class' ) );
        $admin = eZUser::fetchByName( 'admin' )->attribute( 'contentobject_id' );
        $o = $this->envelope( 'expObjectServices', 'byOwner', array( $admin, 2 ) );
        $this->assertArrayHasKey( 'total', $o['meta'] );
        $r = $this->ok( 'expObjectServices', 'recent', array( 3 ) );
        $this->assertLessThanOrEqual( 3, count( $r ) );
        $m = $this->ok( 'expObjectServices', 'recentlyModified', array( 3, 'folder' ) );
        foreach ( $m as $n )
            $this->assertSame( 'folder', $n['class'] );
    }

    public function testDataMapAttributesAttribute()
    {
        $id = $this->mediaObject();
        $map = $this->ok( 'expObjectServices', 'dataMap', array( $id ) );
        $this->assertSame( 'Media', $map['name']['value'] );
        $attrs = $this->ok( 'expObjectServices', 'attributes', array( $id ) );
        $this->assertArrayNotHasKey( 'value', $attrs['name'] );
        $one = $this->ok( 'expObjectServices', 'attribute', array( $id, 'name' ) );
        $this->assertSame( 'Media', $one['value'] );
        $this->fails( 404, 'expObjectServices', 'attribute', array( $id, 'zzz' ) );
    }

    public function testNamesOwnerClassSectionStates()
    {
        $id = $this->mediaObject();
        $this->assertSame( 'Media', $this->ok( 'expObjectServices', 'name', array( $id ) )['name'] );
        $this->assertNotEmpty( (array)$this->ok( 'expObjectServices', 'names', array( $id ) ) );
        $this->assertArrayHasKey( 'id', $this->ok( 'expObjectServices', 'owner', array( $id ) ) );
        $c = $this->ok( 'expObjectServices', 'class', array( $id ) );
        $this->assertSame( 'folder', $c['identifier'] );
        $this->assertNotEmpty( $c['attributes'] );
        $this->assertSame( 'media', $this->ok( 'expObjectServices', 'section', array( $id ) )['identifier'] );
        $this->assertIsArray( $this->ok( 'expObjectServices', 'states', array( $id ) ) );
    }

    public function testNodesMainNodeLanguagesVersionsRightsUrl()
    {
        $id = $this->mediaObject();
        $this->assertSame( 43, $this->ok( 'expObjectServices', 'nodes', array( $id ) )[0]['node_id'] );
        $this->assertSame( 43, $this->ok( 'expObjectServices', 'mainNode', array( $id ) )['node_id'] );
        $l = $this->ok( 'expObjectServices', 'languages', array( $id ) );
        $this->assertNotEmpty( $l['languages'] );
        $this->assertGreaterThanOrEqual( 1, $this->ok( 'expObjectServices', 'versionCount', array( $id ) )['count'] );
        $this->assertTrue( $this->ok( 'expObjectServices', 'rights', array( $id ) )['read'] );
        $this->assertNotEmpty( $this->ok( 'expObjectServices', 'url', array( $id ) )['system_url'] );
    }

    public function testCreateUpdateRenameRemoteIdAndRemove()
    {
        $folder = $this->testFolder();
        $o = $this->ok( 'expObjectServices', 'create', array(), array( 'parent_node_id' => $folder, 'class' => 'folder', 'attributes' => '{"name":"Object service test"}' ) );
        $this->createdObjects[] = $o['id'];
        $this->assertSame( 'Object service test', $o['name'] );
        $u = $this->ok( 'expObjectServices', 'update', array( $o['id'] ), array( 'attributes' => '{"name":"Updated"}' ) );
        $this->assertSame( 'Updated', $u['name'] );
        $this->assertGreaterThan( $o['current_version'], $u['current_version'] );
        $r = $this->ok( 'expObjectServices', 'rename', array( $o['id'] ), array( 'name' => 'Renamed again' ) );
        $this->assertSame( 'Renamed again', $r['name'] );
        $remote = 'objtest-' . uniqid();
        $this->assertSame( $remote, $this->ok( 'expObjectServices', 'setRemoteId', array( $o['id'], $remote ) )['remote_id'] );
        $this->fails( 422, 'expObjectServices', 'setRemoteId', array( $o['id'], 'a b' ) );
        $gone = $this->ok( 'expObjectServices', 'remove', array( $o['id'] ), array( 'move_to_trash' => '0', 'mode' => 'now' ) );
        $this->assertNotEmpty( $gone['removed'] );
        eZContentObject::clearCache();
        $this->fails( 404, 'expObjectServices', 'get', array( $o['id'] ) );
    }

    public function testUpdateRejectsUnwritableInput()
    {
        $n = $this->createFolder();
        $id = $this->objectOf( $n );
        $this->fails( 422, 'expObjectServices', 'update', array( $id ), array( 'attributes' => '{"bogus":"x"}' ) );
        $this->fails( 400, 'expObjectServices', 'update', array( $id ), array() );
    }

    public function testSetOwnerAndLanguagesAndAlwaysAvailable()
    {
        $n = $this->createFolder();
        $id = $this->objectOf( $n );
        $admin = (int)eZUser::fetchByName( 'admin' )->attribute( 'contentobject_id' );
        $this->assertSame( $admin, $this->ok( 'expObjectServices', 'setOwner', array( $id, $admin ) )['owner_id'] );
        $initial = $this->ok( 'expObjectServices', 'languages', array( $id ) )['initial'];
        $this->assertSame( $initial, $this->ok( 'expObjectServices', 'setInitialLanguage', array( $id, $initial ) )['initial_language'] );
        $this->assertTrue( $this->ok( 'expObjectServices', 'setAlwaysAvailable', array( $id, 1 ) )['always_available'] );
        $this->assertFalse( $this->ok( 'expObjectServices', 'setAlwaysAvailable', array( $id, 0 ) )['always_available'] );
        $this->fails( 422, 'expObjectServices', 'setInitialLanguage', array( $id, 'xxx-XX' ) );
        $this->ok( 'expObjectServices', 'expireCache', array( $id ) );
        $this->ok( 'expObjectServices', 'cleanupDrafts', array( $id ) );
    }

    public function testCopyObject()
    {
        $a = $this->createFolder();
        $b = $this->createFolder();
        $copy = $this->ok( 'expObjectServices', 'copy', array( $this->objectOf( $a ), $b ) );
        $this->createdObjects[] = $copy['object_id'];
        $this->assertSame( $b, $copy['parent_node_id'] );
    }
}
