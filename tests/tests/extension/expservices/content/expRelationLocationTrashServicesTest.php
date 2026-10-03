<?php
/**
 * Relation, location and trash services on test content under the Media root.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expContentServicesTestCase.php';

class expRelationLocationTrashServicesTest extends expContentServicesTestCase
{
    public function testDeclarations()
    {
        $this->assertDeclarations( 'expRelationServices', 15 );
        $this->assertDeclarations( 'expLocationServices', 15 );
        $this->assertDeclarations( 'expTrashServices', 12 );
    }

    public function testRelationReadsOnExistingContent()
    {
        $id = (int)eZContentObjectTreeNode::fetch( 43 )->attribute( 'contentobject_id' );
        $c = $this->ok( 'expRelationServices', 'counts', array( $id ) );
        $this->assertArrayHasKey( 'common', $c['related'] );
        $this->assertArrayHasKey( 'attribute', $c['reverse'] );
        $this->assertIsArray( $this->envelope( 'expRelationServices', 'related', array( $id ) )['data'] );
        $this->assertIsArray( $this->envelope( 'expRelationServices', 'reverse', array( $id, 'all', 5 ) )['data'] );
        $this->assertSame( array_sum( $c['related'] ), $this->ok( 'expRelationServices', 'relatedCount', array( $id ) )['count'] );
        $this->assertSame( array_sum( $c['reverse'] ), $this->ok( 'expRelationServices', 'reverseCount', array( $id ) )['count'] );
        $this->assertContains( 'embed', array_keys( (array)$this->ok( 'expRelationServices', 'types' ) ) );
        $this->fails( 400, 'expRelationServices', 'related', array( $id, 'bogus' ) );
        $n = $this->ok( 'expRelationServices', 'neighbours', array( $id ) );
        $this->assertArrayHasKey( 'related', $n );
        $this->assertIsArray( $this->ok( 'expRelationServices', 'broken', array( $id ) ) );
        $this->assertIsArray( $this->ok( 'expRelationServices', 'byAttribute', array( $id, 'tags' ) ) );
        $this->assertArrayHasKey( '43', (array)$this->ok( 'expRelationServices', 'reverseCountForNodes', array( '43,2' ) ) );
    }

    public function testRelationAddExistsRemoveReplace()
    {
        $a = $this->objectOf( $this->createFolder() );
        $b = $this->objectOf( $this->createFolder() );
        $c = $this->objectOf( $this->createFolder() );
        $this->assertFalse( $this->ok( 'expRelationServices', 'exists', array( $a, $b ) )['exists'] );
        $this->ok( 'expRelationServices', 'add', array( $a, $b ) );
        $this->assertTrue( $this->ok( 'expRelationServices', 'exists', array( $a, $b ) )['exists'] );
        $this->assertSame( array( 'common' ), $this->ok( 'expRelationServices', 'exists', array( $a, $b ) )['types'] );
        $this->fails( 409, 'expRelationServices', 'add', array( $a, $b ) );
        $this->fails( 422, 'expRelationServices', 'add', array( $a, $a ) );
        $this->fails( 400, 'expRelationServices', 'add', array( $a, $c ), array( 'type' => 'attribute' ) );
        $rel = $this->envelope( 'expRelationServices', 'related', array( $a ) );
        $this->assertSame( array( $b ), array_column( $rel['data'], 'object_id' ) );
        $rev = $this->envelope( 'expRelationServices', 'reverse', array( $b ) );
        $this->assertSame( array( $a ), array_column( $rev['data'], 'object_id' ) );
        $this->ok( 'expRelationServices', 'remove', array( $a, $b ) );
        $this->fails( 404, 'expRelationServices', 'remove', array( $a, $b ) );
        $r = $this->ok( 'expRelationServices', 'replace', array( $a ), array( 'object_ids' => "$b,$c" ) );
        $this->assertSame( 2, count( $r['added'] ) );
        $r2 = $this->ok( 'expRelationServices', 'replace', array( $a ), array( 'object_ids' => (string)$c ) );
        $this->assertSame( array( $b ), $r2['removed'] );
        $all = $this->ok( 'expRelationServices', 'removeAll', array( $a ) );
        $this->assertSame( array( $c ), $all['removed'] );
    }

    public function testLocationReads()
    {
        $id = (int)eZContentObjectTreeNode::fetch( 43 )->attribute( 'contentobject_id' );
        $this->assertSame( 43, $this->ok( 'expLocationServices', 'list', array( $id ) )[0]['node_id'] );
        $this->assertSame( 1, $this->ok( 'expLocationServices', 'count', array( $id ) )['count'] );
        $this->assertSame( 43, $this->ok( 'expLocationServices', 'main', array( $id ) )['node_id'] );
        $this->assertSame( 1, $this->ok( 'expLocationServices', 'parents', array( $id ) )[0]['node_id'] );
        $this->assertTrue( $this->ok( 'expLocationServices', 'assignments', array( $id ) )[0]['is_main'] );
        $this->assertTrue( $this->ok( 'expLocationServices', 'isMain', array( 43 ) )['is_main'] );
        $this->assertFalse( $this->ok( 'expLocationServices', 'canRemove', array( 43 ) )['can_remove'] );
        $this->assertArrayHasKey( 'can_add', $this->ok( 'expLocationServices', 'canAdd', array( $id, 2 ) ) );
        $e = $this->envelope( 'expLocationServices', 'candidates', array( 43, 'folder', 5 ) );
        $this->assertArrayHasKey( 'total', $e['meta'] );
        $this->assertArrayHasKey( 'total', $this->envelope( 'expLocationServices', 'orphans', array( 5 ) )['meta'] );
    }

    public function testLocationAddSetMainRemove()
    {
        $a = $this->createFolder();
        $b = $this->createFolder();
        $oid = $this->objectOf( $a );
        $this->assertFalse( $this->ok( 'expLocationServices', 'canRemove', array( $a ) )['can_remove'] );
        $locs = $this->ok( 'expLocationServices', 'add', array( $oid, $b ) );
        $this->assertCount( 2, $locs );
        $this->fails( 409, 'expLocationServices', 'add', array( $oid, $b ) );
        $second = null;
        foreach ( $locs as $l )
            if ( $l['parent_node_id'] === $b )
                $second = $l['node_id'];
        $this->assertNotNull( $second );
        $this->fails( 409, 'expLocationServices', 'setMain', array( $a ) );
        $moved = $this->ok( 'expLocationServices', 'setMain', array( $second ) );
        $this->assertSame( $second, $moved[0]['main_node_id'] );
        $this->assertTrue( $this->ok( 'expLocationServices', 'isMain', array( $second ) )['is_main'] );
        $this->assertTrue( $this->ok( 'expLocationServices', 'canRemove', array( $a ) )['can_remove'] );
        $left = $this->ok( 'expLocationServices', 'remove', array( $a ), array( 'mode' => 'now' ) );
        $this->assertCount( 1, $left );
        $this->fails( 409, 'expLocationServices', 'remove', array( $second ) );
    }

    public function testLocationAddManyAndRemoveMany()
    {
        $t = $this->createFolder();
        $a = $this->createFolder();
        $b = $this->createFolder();
        $r = $this->ok( 'expLocationServices', 'addMany', array( $t ), array( 'node_ids' => "$a,$b", 'mode' => 'now' ) );
        $this->assertCount( 2, $r['added'] );
        $kids = $this->envelope( 'expNodeServices', 'children', array( $t ) );
        $this->assertSame( 2, $kids['meta']['total'] );
        $ids = array_column( $kids['data'], 'node_id' );
        $rm = $this->ok( 'expLocationServices', 'removeMany', array(), array( 'node_ids' => implode( ',', $ids ), 'mode' => 'now' ) );
        $this->assertSame( $ids, array_values( $rm['removed'] ) );
        $this->assertSame( 0, $this->envelope( 'expNodeServices', 'children', array( $t ) )['meta']['total'] );
    }

    public function testTrashRemoveListRestoreAndPurge()
    {
        $n = $this->createFolder( 'trash me ' . uniqid() );
        $oid = $this->objectOf( $n );
        $this->assertFalse( $this->ok( 'expTrashServices', 'isTrashed', array( $oid ) )['trashed'] );
        $this->ok( 'expNodeServices', 'remove', array( $n ), array( 'mode' => 'now' ) );
        $this->assertTrue( $this->ok( 'expTrashServices', 'isTrashed', array( $oid ) )['trashed'] );
        $t = $this->ok( 'expTrashServices', 'get', array( $oid ) );
        $this->assertSame( 'folder', $t['class'] );
        $this->assertTrue( $t['original_parent_exists'] );
        $this->assertGreaterThanOrEqual( 1, $this->ok( 'expTrashServices', 'count' )['count'] );
        $list = $this->envelope( 'expTrashServices', 'list', array( 'trashed', 'desc', 10 ) );
        $this->assertContains( $oid, array_column( $list['data'], 'object_id' ) );
        $this->assertContains( $oid, array_column( $this->envelope( 'expTrashServices', 'byClass', array( 'folder' ) )['data'], 'object_id' ) );
        $this->assertContains( $oid, array_column( $this->envelope( 'expTrashServices', 'since', array( '-1 hour' ) )['data'], 'object_id' ) );
        $this->assertSame( $this->testFolder(), $this->ok( 'expTrashServices', 'originalParent', array( $oid ) )['node_id'] );
        $r = $this->ok( 'expTrashServices', 'restore', array( $oid ) );
        $this->assertSame( $this->testFolder(), $r['parent_node_id'] );
        $this->assertFalse( $this->ok( 'expTrashServices', 'isTrashed', array( $oid ) )['trashed'] );
        $this->fails( 404, 'expTrashServices', 'restore', array( $oid ) );
        $this->ok( 'expNodeServices', 'remove', array( $r['node_id'] ), array( 'mode' => 'now' ) );
        $this->assertSame( 1, $this->ok( 'expTrashServices', 'purge', array( $oid ) )['purged'] );
        eZContentObject::clearCache();
        $this->fails( 404, 'expTrashServices', 'get', array( $oid ) );
    }

    public function testTrashRestoreToOtherParentAndPurgeMany()
    {
        $a = $this->createFolder();
        $b = $this->createFolder();
        $oa = $this->objectOf( $a );
        $ob = $this->objectOf( $b );
        $this->ok( 'expNodeServices', 'removeMany', array(), array( 'node_ids' => "$a,$b", 'mode' => 'now' ) );
        $other = $this->createFolder();
        $r = $this->ok( 'expTrashServices', 'restore', array( $oa ), array( 'parent_node_id' => $other ) );
        $this->assertSame( $other, $r['parent_node_id'] );
        $this->assertSame( 1, $this->ok( 'expTrashServices', 'purgeMany', array(), array( 'object_ids' => (string)$ob ) )['purged'] );
        $this->fails( 400, 'expTrashServices', 'emptyTrash', array(), array() );
        $this->fails( 400, 'expTrashServices', 'list', array( 'bogus' ) );
        $this->assertIsBool( $this->ok( 'expTrashServices', 'canEmpty' )['can_empty'] );
    }

    public function testTrashNeedsPolicy()
    {
        $this->loginAnonymous();
        $this->fails( 401, 'expTrashServices', 'purge', array( 1 ) );
    }
}
