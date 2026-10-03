<?php
/**
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expUsersTestCase.php';

class expRoleServicesTest extends expUsersTestCase
{
    public function testListCountFetchView()
    {
        $r = $this->call( 'expRoleServices', 'listAll', array( '3' ) );
        $this->assertPaged( $r );
        $this->assertGreaterThanOrEqual( count( $r['data'] ), $this->okCall( 'expRoleServices', 'count' )['count'] );
        $id = $r['data'][0]['id'];
        $this->assertSame( $id, $this->okCall( 'expRoleServices', 'fetch', array( (string)$id ) )['id'] );
        $v = $this->okCall( 'expRoleServices', 'view', array( (string)$id ) );
        $this->assertArrayHasKey( 'policies', $v );
        $this->assertArrayHasKey( 'assignments', $v );
        $this->assertError( $this->call( 'expRoleServices', 'fetch', array( '99999999' ) ), 404 );
    }

    public function testCreateRenameCopyRemove()
    {
        $id = $this->newRole( array( array( 'content', 'read' ) ) );
        $name = 'Renamed ' . self::uniq();
        $this->assertSame( $name, $this->okWrite( 'expRoleServices', 'rename', array( 'id' => $id, 'name' => $name ) )['name'] );
        $this->assertTrue( $this->okCall( 'expRoleServices', 'exists', array( $name ) )['exists'] );
        $this->assertSame( $id, $this->okCall( 'expRoleServices', 'byName', array( $name ) )['id'] );
        $copy = $this->okWrite( 'expRoleServices', 'copy', array( 'id' => $id, 'name' => 'Copy ' . self::uniq() ) );
        self::$made['roles'][] = $copy['id'];
        $this->assertCount( 1, $copy['policies'] );
        $d = $this->okWrite( 'expRoleServices', 'remove', array( 'id' => $copy['id'] ) );
        $this->assertTrue( $d['removed'] );
        $this->assertError( $this->call( 'expRoleServices', 'fetch', array( (string)$copy['id'] ) ), 404 );
    }

    public function testDuplicateNameIs409()
    {
        $name = 'Dup ' . self::uniq();
        $this->newRole( array(), $name );
        $this->assertError( $this->write( 'expRoleServices', 'create', array( 'name' => $name ) ), 409 );
        $this->assertError( $this->write( 'expRoleServices', 'create', array( 'name' => ' ' ) ), 422 );
    }

    public function testDraftEditAndPublish()
    {
        $id = $this->newRole( array( array( 'content', 'read' ) ) );
        $this->assertTrue( $this->okWrite( 'expRoleServices', 'draftCreate', array( 'id' => $id ) )['is_draft'] );
        $d = $this->okWrite( 'expRoleServices', 'draftAddPolicy', array( 'id' => $id, 'module' => 'content', 'function' => 'edit' ) );
        $this->assertSame( 'edit', $d['function'] );
        $this->assertCount( 2, $this->okCall( 'expRoleServices', 'draft', array( (string)$id ) )['policies'] );
        $this->assertCount( 1, $this->okCall( 'expRoleServices', 'view', array( (string)$id ) )['policies'] );
        $this->okWrite( 'expRoleServices', 'draftRename', array( 'id' => $id, 'name' => 'Published ' . self::uniq() ) );
        $this->okWrite( 'expRoleServices', 'publish', array( 'id' => $id ) );
        $this->assertSame( 2, $this->okCall( 'expRoleServices', 'policyCount', array( (string)$id ) )['count'] );
        $this->assertError( $this->call( 'expRoleServices', 'draft', array( (string)$id ) ), 409 );
    }

    public function testDraftRemovePolicyAndDiscard()
    {
        $id = $this->newRole( array( array( 'content', 'read' ), array( 'content', 'edit' ) ) );
        $draft = $this->okWrite( 'expRoleServices', 'draftCreate', array( 'id' => $id ) );
        $this->assertCount( 2, $draft['policies'] );
        $after = $this->okWrite( 'expRoleServices', 'draftRemovePolicy', array( 'id' => $id, 'policy' => $draft['policies'][0]['id'] ) );
        $this->assertCount( 1, $after['policies'] );
        $this->assertError( $this->write( 'expRoleServices', 'draftRemovePolicy', array( 'id' => $id, 'policy' => 99999999 ) ), 404 );
        $this->assertTrue( $this->okWrite( 'expRoleServices', 'draftDiscard', array( 'id' => $id ) )['draft_discarded'] );
        $this->assertSame( 2, $this->okCall( 'expRoleServices', 'policyCount', array( (string)$id ) )['count'] );
    }

    public function testAssignAndUnassign()
    {
        $role = $this->newRole( array( array( 'content', 'read' ) ) );
        $u = $this->newUser();
        $d = $this->okWrite( 'expRoleServices', 'assign', array( 'id' => $role, 'object' => $u ) );
        $this->assertCount( 1, $d['assignments'] );
        $this->assertContains( $role, array_column( $this->okCall( 'expRoleServices', 'assignmentsOf', array( (string)$u ) ), 'role_id' ) );
        $this->assertContains( $u, array_column( $this->call( 'expRoleServices', 'assignments', array( (string)$role ) )['data'], 'object_id' ) );
        $this->assertArrayHasKey( $u, $this->okCall( 'expRoleServices', 'assigned', array( (string)$role ) )['objects'] );
        $this->assertError( $this->write( 'expRoleServices', 'remove', array( 'id' => $role ) ), 409 );
        $this->okWrite( 'expRoleServices', 'unassign', array( 'id' => $role, 'object' => $u ) );
        $this->assertCount( 0, $this->okCall( 'expRoleServices', 'assignmentsOf', array( (string)$u ) ) );
        $this->assertError( $this->write( 'expRoleServices', 'unassign', array( 'id' => $role, 'object' => $u ) ), 404 );
    }

    public function testAssignWithSubtreeLimit()
    {
        $role = $this->newRole( array( array( 'content', 'read' ) ) );
        $g = $this->fixtureGroup();
        $obj = eZContentObjectTreeNode::fetch( $g )->attribute( 'contentobject_id' );
        $d = $this->okWrite( 'expRoleServices', 'assign', array( 'id' => $role, 'object' => $obj, 'limit' => 'subtree', 'limit_value' => 43 ) );
        $this->assertSame( 'Subtree', $d['assignments'][0]['limit_identifier'] );
        $this->assertNotEmpty( $this->okCall( 'expRoleServices', 'byLimitation', array( 'Subtree', $d['assignments'][0]['limit_value'] ) ) );
        $this->assertNotEmpty( $this->call( 'expRoleServices', 'usersWith', array( (string)$role ) )['data'] );
    }

    public function testAssignRejectsBadInput()
    {
        $role = $this->newRole();
        $u = $this->newUser();
        $this->assertError( $this->write( 'expRoleServices', 'assign', array( 'id' => $role, 'object' => $u, 'limit' => 'bogus', 'limit_value' => 1 ) ), 422 );
        $this->assertError( $this->write( 'expRoleServices', 'assign', array( 'id' => $role, 'object' => $u, 'limit' => 'subtree', 'limit_value' => 99999999 ) ), 404 );
        $this->assertError( $this->write( 'expRoleServices', 'assign', array( 'id' => $role, 'object' => 99999999 ) ), 404 );
        $this->assertError( $this->write( 'expRoleServices', 'assign', array( 'id' => $role, 'object' => 2 ) ), 422 );
    }

    public function testRoleChangesNeedThePolicy()
    {
        $this->loginAs( $this->memberUser() );
        $this->assertError( $this->write( 'expRoleServices', 'create', array( 'name' => 'nope' ) ), 403 );
        $this->assertError( $this->call( 'expRoleServices', 'listAll' ), 403 );
    }
}
