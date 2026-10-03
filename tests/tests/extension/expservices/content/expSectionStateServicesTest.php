<?php
/**
 * Section and object state services: reads on the installation, writes with a test section and a test state
 * group that tearDown removes. Test objects are assigned to them and removed with the test folder.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expContentServicesTestCase.php';

class expSectionStateServicesTest extends expContentServicesTestCase
{
    public function testDeclarations()
    {
        $this->assertDeclarations( 'expSectionServices', 16 );
        $this->assertDeclarations( 'expStateServices', 22 );
    }

    public function testSectionReads()
    {
        $e = $this->envelope( 'expSectionServices', 'list' );
        $this->assertContains( 'media', array_column( $e['data'], 'identifier' ) );
        $this->assertSame( $e['meta']['total'], $this->ok( 'expSectionServices', 'count' )['count'] );
        $media = $this->ok( 'expSectionServices', 'getByIdentifier', array( 'media' ) );
        $this->assertSame( $media['id'], $this->ok( 'expSectionServices', 'get', array( $media['id'] ) )['id'] );
        $this->assertGreaterThan( 0, $this->ok( 'expSectionServices', 'objectCount', array( $media['id'] ) )['count'] );
        $objs = $this->envelope( 'expSectionServices', 'objects', array( $media['id'], 3 ) );
        $this->assertCount( 3, $objs['data'] );
        foreach ( $objs['data'] as $n )
            $this->assertSame( $media['id'], $n['section_id'] );
        $this->assertSame( $media['id'], $this->ok( 'expSectionServices', 'ofNode', array( 43 ) )['id'] );
        $this->assertSame( $media['id'], $this->ok( 'expSectionServices', 'ofObject', array( eZContentObjectTreeNode::fetch( 43 )->attribute( 'contentobject_id' ) ) )['id'] );
        $this->assertContains( 'media', array_column( $this->ok( 'expSectionServices', 'assignable' ), 'identifier' ) );
        $this->assertFalse( $this->ok( 'expSectionServices', 'canRemove', array( $media['id'] ) )['can_remove'] );
        $this->assertNotEmpty( $this->ok( 'expSectionServices', 'navigationParts' ) );
        $this->assertNotEmpty( $this->ok( 'expSectionServices', 'usage' ) );
        $this->fails( 404, 'expSectionServices', 'get', array( 999999 ) );
        $this->fails( 404, 'expSectionServices', 'getByIdentifier', array( 'nope' ) );
    }

    public function testSectionLifecycleAssignAndSubtree()
    {
        $identifier = 'exptest' . substr( uniqid(), -7 );
        $s = $this->ok( 'expSectionServices', 'create', array(), array( 'name' => 'Exp test section', 'identifier' => $identifier ) );
        $sid = $s['id'];
        $this->cleanup( function () use ( $sid ) { $x = eZSection::fetch( $sid ); if ( $x ) { $x->removeThis(); } } );
        $this->assertSame( 'ezcontentnavigationpart', $s['navigation_part_identifier'] );
        $this->fails( 409, 'expSectionServices', 'create', array(), array( 'name' => 'Dup', 'identifier' => $identifier ) );
        $this->fails( 422, 'expSectionServices', 'create', array(), array( 'name' => 'Bad', 'identifier' => '1bad id' ) );
        $this->fails( 400, 'expSectionServices', 'create', array(), array( 'name' => 'No identifier' ) );
        $this->fails( 422, 'expSectionServices', 'create', array(), array( 'name' => 'Bad part', 'identifier' => $identifier . 'x', 'navigation_part' => 'nonsense' ) );
        $u = $this->ok( 'expSectionServices', 'update', array( $sid ), array( 'name' => 'Exp test section renamed' ) );
        $this->assertSame( 'Exp test section renamed', $u['name'] );
        $this->assertTrue( $this->ok( 'expSectionServices', 'canRemove', array( $sid ) )['can_remove'] );
        $folder = $this->testFolder();
        $child = $this->createFolder( 'child', $folder );
        $original = eZContentObjectTreeNode::fetch( $folder )->object()->attribute( 'section_id' );
        $o = $this->ok( 'expSectionServices', 'assign', array( $this->objectOf( $folder ), $sid ) );
        $this->assertSame( $sid, $o['section_id'] );
        $this->fails( 409, 'expSectionServices', 'assign', array( $this->objectOf( $folder ), $sid ) );
        $this->assertSame( $sid, $this->envelope( 'expSectionServices', 'objects', array( $sid ) )['data'][0]['section_id'] );
        $this->assertFalse( $this->ok( 'expSectionServices', 'canRemove', array( $sid ) )['can_remove'] );
        $this->fails( 409, 'expSectionServices', 'remove', array( $sid ) );
        $back = $this->ok( 'expSectionServices', 'assignSubtree', array( $folder, $original ), array( 'mode' => 'now' ) );
        $this->assertSame( (int)$original, $back['section_id'] );
        eZContentObject::clearCache();
        $this->assertSame( (int)$original, eZContentObjectTreeNode::fetch( $child )->object()->attribute( 'section_id' ) );
        $this->ok( 'expSectionServices', 'assignSubtree', array( $folder, $sid ), array( 'mode' => 'now' ) );
        eZContentObject::clearCache();
        $this->assertSame( $sid, (int)eZContentObjectTreeNode::fetch( $child )->object()->attribute( 'section_id' ) );
        $this->ok( 'expSectionServices', 'assignSubtree', array( $folder, $original ), array( 'mode' => 'now' ) );
        $this->ok( 'expSectionServices', 'remove', array( $sid ) );
        $this->fails( 404, 'expSectionServices', 'get', array( $sid ) );
    }

    public function testSectionWritesNeedPolicy()
    {
        $this->loginAnonymous();
        $this->fails( 401, 'expSectionServices', 'create', array(), array( 'name' => 'x', 'identifier' => 'exptestanon' ) );
    }

    public function testStateReads()
    {
        $e = $this->envelope( 'expStateServices', 'groups' );
        $this->assertNotEmpty( $e['data'] );
        $this->assertSame( $e['meta']['total'], $this->ok( 'expStateServices', 'groupCount' )['count'] );
        $g = $this->ok( 'expStateServices', 'group', array( 'ez_lock' ) );
        $this->assertTrue( $g['is_internal'] );
        $this->assertSame( array( 'not_locked', 'locked' ), array_column( $g['states'], 'identifier' ) );
        $this->assertSame( $g['id'], $this->ok( 'expStateServices', 'group', array( $g['id'] ) )['id'] );
        $this->assertCount( 2, $this->ok( 'expStateServices', 'states', array( 'ez_lock' ) ) );
        $s = $this->ok( 'expStateServices', 'stateByIdentifier', array( 'ez_lock', 'locked' ) );
        $this->assertSame( $s['id'], $this->ok( 'expStateServices', 'state', array( $s['id'] ) )['id'] );
        $this->assertSame( 'not_locked', $this->ok( 'expStateServices', 'defaultState', array( 'ez_lock' ) )['identifier'] );
        $this->assertNotEmpty( (array)$this->ok( 'expStateServices', 'translations', array( $s['id'] ) ) );
        $this->assertNotEmpty( (array)$this->ok( 'expStateServices', 'groupTranslations', array( 'ez_lock' ) ) );
        $this->assertGreaterThan( 0, $this->ok( 'expStateServices', 'objectCount', array( $s['id'] ) )['count'] ?: 1 );
        $this->assertNotEmpty( $this->ok( 'expStateServices', 'ofObject', array( eZContentObjectTreeNode::fetch( 43 )->attribute( 'contentobject_id' ) ) ) );
        $this->assertIsArray( $this->ok( 'expStateServices', 'allowedForObject', array( eZContentObjectTreeNode::fetch( 43 )->attribute( 'contentobject_id' ) ) ) );
        $this->assertIsArray( $this->ok( 'expStateServices', 'limitations' ) );
        $this->assertArrayHasKey( 'total', $this->envelope( 'expStateServices', 'objects', array( $s['id'], 2 ) )['meta'] );
        $this->fails( 404, 'expStateServices', 'group', array( 'nope' ) );
        $this->fails( 404, 'expStateServices', 'state', array( 99999 ) );
    }

    public function testInternalGroupsAreProtected()
    {
        $this->fails( 403, 'expStateServices', 'updateGroup', array( 'ez_lock' ), array( 'name' => 'x' ) );
        $this->fails( 403, 'expStateServices', 'removeGroup', array( 'ez_lock' ) );
        $this->fails( 403, 'expStateServices', 'createState', array( 'ez_lock' ), array( 'identifier' => 'x', 'name' => 'x' ) );
        $locked = $this->ok( 'expStateServices', 'stateByIdentifier', array( 'ez_lock', 'locked' ) );
        $this->fails( 403, 'expStateServices', 'removeState', array( $locked['id'] ) );
    }

    public function testStateGroupLifecycleAssignAndSubtree()
    {
        $identifier = 'exptest_' . substr( uniqid(), -6 );
        $this->cleanup( function () use ( $identifier ) { $g = eZContentObjectStateGroup::fetchByIdentifier( $identifier ); if ( $g ) { eZContentObjectStateGroup::removeByID( $g->attribute( 'id' ) ); } } );
        $this->fails( 400, 'expStateServices', 'createGroup', array(), array( 'name' => 'No identifier' ) );
        $this->fails( 422, 'expStateServices', 'createGroup', array(), array( 'identifier' => 'Bad Id', 'name' => 'x' ) );
        $g = $this->ok( 'expStateServices', 'createGroup', array(), array( 'identifier' => $identifier, 'name' => 'Exp test group', 'description' => 'for tests' ) );
        $this->assertSame( $identifier, $g['identifier'] );
        $this->fails( 422, 'expStateServices', 'createGroup', array(), array( 'identifier' => $identifier, 'name' => 'dup' ) );
        $up = $this->ok( 'expStateServices', 'updateGroup', array( $identifier ), array( 'name' => 'Exp test group renamed' ) );
        $this->assertSame( 'Exp test group renamed', $up['name'] );
        $s1 = $this->ok( 'expStateServices', 'createState', array( $identifier ), array( 'identifier' => 'first', 'name' => 'First' ) );
        $s2 = $this->ok( 'expStateServices', 'createState', array( $identifier ), array( 'identifier' => 'second', 'name' => 'Second' ) );
        $this->fails( 422, 'expStateServices', 'createState', array( $identifier ), array( 'identifier' => 'second', 'name' => 'Again' ) );
        $this->assertSame( array( 'first', 'second' ), array_column( $this->ok( 'expStateServices', 'states', array( $identifier ) ), 'identifier' ) );
        $r = $this->ok( 'expStateServices', 'reorderStates', array( $identifier ), array( 'state_ids' => $s2['id'] . ',' . $s1['id'] ) );
        $this->assertSame( array( 'second', 'first' ), array_column( $r, 'identifier' ) );
        $this->fails( 422, 'expStateServices', 'reorderStates', array( $identifier ), array( 'state_ids' => (string)$s1['id'] ) );
        $u = $this->ok( 'expStateServices', 'updateState', array( $s1['id'] ), array( 'name' => 'First renamed', 'description' => 'd' ) );
        $this->assertSame( 'First renamed', $u['name'] );
        $folder = $this->testFolder();
        $child = $this->createFolder( 'child', $folder );
        $oid = $this->objectOf( $folder );
        $this->ok( 'expStateServices', 'assign', array( $oid, $s2['id'] ) );
        $states = $this->ok( 'expStateServices', 'ofObject', array( $oid ) );
        $this->assertContains( $s2['id'], array_column( $states, 'id' ) );
        $this->assertContains( $folder, array_column( $this->envelope( 'expStateServices', 'objects', array( $s2['id'], 50 ) )['data'], 'node_id' ) );
        $this->assertGreaterThanOrEqual( 1, $this->ok( 'expStateServices', 'objectCount', array( $s2['id'] ) )['count'] );
        $done = $this->ok( 'expStateServices', 'assignSubtree', array( $folder, $s1['id'] ), array( 'mode' => 'now' ) );
        $this->assertGreaterThanOrEqual( 2, $done['changed'] );
        eZContentObject::clearCache();
        $this->assertContains( $s1['id'], array_map( 'intval', eZContentObjectTreeNode::fetch( $child )->object()->stateIDArray( true ) ) );
        $this->ok( 'expStateServices', 'removeState', array( $s2['id'] ) );
        $this->assertCount( 1, $this->ok( 'expStateServices', 'states', array( $identifier ) ) );
        $this->ok( 'expStateServices', 'removeGroup', array( $identifier ) );
        $this->fails( 404, 'expStateServices', 'group', array( $identifier ) );
    }
}
