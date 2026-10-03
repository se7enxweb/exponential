<?php
/**
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expUsersTestCase.php';

class expUserGroupServicesTest extends expUsersTestCase
{
    public function testRootAndTree()
    {
        $root = $this->okCall( 'expUserGroupServices', 'root' );
        $this->assertSame( $this->usersRootNode(), $root['node_id'] );
        $t = $this->okCall( 'expUserGroupServices', 'tree', array( (string)$root['node_id'], '2' ) );
        $this->assertSame( $root['node_id'], $t['node_id'] );
        $this->assertArrayHasKey( 'groups', $t );
        $this->assertError( $this->call( 'expUserGroupServices', 'tree', array( '5', '99' ) ), 400 );
    }

    public function testFetchPathParentChildren()
    {
        $g = $this->fixtureGroup();
        $this->assertSame( $g, $this->okCall( 'expUserGroupServices', 'fetch', array( (string)$g ) )['node_id'] );
        $path = $this->okCall( 'expUserGroupServices', 'path', array( (string)$g ) );
        $this->assertSame( $g, end( $path )['node_id'] );
        $this->assertSame( $this->usersRootNode(), $this->okCall( 'expUserGroupServices', 'parent', array( (string)$g ) )['node_id'] );
        $this->assertPaged( $this->call( 'expUserGroupServices', 'children', array( (string)$this->usersRootNode() ) ) );
    }

    public function testNonGroupIs404()
    {
        $this->assertError( $this->call( 'expUserGroupServices', 'fetch', array( '2' ) ), 404 );
    }

    public function testCreateRenameSearch()
    {
        $name = 'Searchable ' . self::uniq();
        $g = $this->newGroup( null, $name );
        $this->okWrite( 'expUserGroupServices', 'rename', array( 'group' => $g, 'name' => $name . ' renamed' ) );
        $this->assertSame( $name . ' renamed', $this->okCall( 'expUserGroupServices', 'fetch', array( (string)$g ) )['name'] );
        $r = $this->call( 'expUserGroupServices', 'search', array( 'Searchable' ) );
        $this->assertPaged( $r );
        $this->assertContains( $g, array_column( $r['data'], 'node_id' ) );
    }

    public function testCreateNeedsAName()
    {
        $this->assertError( $this->write( 'expUserGroupServices', 'create', array( 'parent' => $this->usersRootNode(), 'name' => '  ' ) ), 400 + 22 );
    }

    public function testSubgroupsAndRemove()
    {
        $parent = $this->newGroup();
        $child = $this->newGroup( $parent );
        $this->assertSame( 1, $this->okCall( 'expUserGroupServices', 'subgroupCount', array( (string)$parent ) )['count'] );
        $this->assertError( $this->write( 'expUserGroupServices', 'remove', array( 'group' => $parent ) ), 409 );
        $d = $this->okWrite( 'expUserGroupServices', 'remove', array( 'group' => $parent, 'force' => '1' ) );
        $this->assertSame( 1, $d['removed_children'] );
        $this->assertError( $this->call( 'expUserGroupServices', 'fetch', array( (string)$child ) ), 404 );
    }

    public function testUsersRootCannotBeRemoved()
    {
        $r = $this->write( 'expUserGroupServices', 'remove', array( 'group' => $this->usersRootNode(), 'force' => '1' ) );
        $this->assertFalse( $r['ok'] );
        $this->assertContains( $r['error']['code'], array( 403, 409 ) );
    }

    public function testMembership()
    {
        $a = $this->newGroup();
        $b = $this->newGroup();
        $u = $this->newUser( $a );
        $this->assertSame( 1, $this->okCall( 'expUserGroupServices', 'memberCount', array( (string)$a ) )['count'] );
        $this->assertTrue( $this->okCall( 'expUserGroupServices', 'isMember', array( (string)$u, (string)$a ) )['member'] );
        $this->assertFalse( $this->okCall( 'expUserGroupServices', 'isMember', array( (string)$u, (string)$b ) )['member'] );
        $this->okWrite( 'expUserGroupServices', 'addMember', array( 'group' => $b, 'user' => $u ) );
        $this->assertTrue( $this->okCall( 'expUserGroupServices', 'isMember', array( (string)$u, (string)$b ) )['member'] );
        $this->assertGreaterThanOrEqual( 2, count( $this->okCall( 'expUserGroupServices', 'ofUser', array( (string)$u ) ) ) );
        $this->assertError( $this->write( 'expUserGroupServices', 'addMember', array( 'group' => $b, 'user' => $u ) ), 409 );
        $this->okWrite( 'expUserGroupServices', 'removeMember', array( 'group' => $b, 'user' => $u ) );
        $this->assertFalse( $this->okCall( 'expUserGroupServices', 'isMember', array( (string)$u, (string)$b ) )['member'] );
    }

    public function testLastMembershipCannotBeRemoved()
    {
        $a = $this->newGroup();
        $u = $this->newUser( $a );
        $this->assertError( $this->write( 'expUserGroupServices', 'removeMember', array( 'group' => $a, 'user' => $u ) ), 409 );
    }

    public function testMoveMember()
    {
        $a = $this->newGroup();
        $b = $this->newGroup();
        $u = $this->newUser( $a );
        $this->okWrite( 'expUserGroupServices', 'moveMember', array( 'user' => $u, 'from' => $a, 'to' => $b ) );
        $this->assertTrue( $this->okCall( 'expUserGroupServices', 'isMember', array( (string)$u, (string)$b ) )['member'] );
        $this->assertFalse( $this->okCall( 'expUserGroupServices', 'isMember', array( (string)$u, (string)$a ) )['member'] );
        $ids = array_column( $this->call( 'expUserGroupServices', 'members', array( (string)$b ) )['data'], 'id' );
        $this->assertContains( $u, $ids );
    }

    public function testAddMembers()
    {
        $a = $this->newGroup();
        $b = $this->newGroup();
        $u1 = $this->newUser( $a );
        $u2 = $this->newUser( $a );
        $d = $this->okWrite( 'expUserGroupServices', 'addMembers', array( 'group' => $b, 'users' => $u1 . ',' . $u2 . ',99999999' ) );
        $this->assertCount( 2, $d['added'] );
        $this->assertSame( 1, $d['skipped'] );
    }

    public function testRolesOfGroup()
    {
        $this->memberUser();
        $d = $this->okCall( 'expUserGroupServices', 'rolesOf', array( (string)$this->fixtureGroup() ) );
        $this->assertContains( self::$fixture['role'], array_column( $d, 'id' ) );
    }

    public function testAnonymousCannotCreateGroups()
    {
        $this->loginAnonymous();
        $this->assertError( $this->write( 'expUserGroupServices', 'create', array( 'parent' => 12, 'name' => 'x' ) ), 401 );
    }
}
