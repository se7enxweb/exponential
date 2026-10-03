<?php
/**
 * The discount and wish list services. A test discount group with rules and members is created and removed in
 * each test; the wish list test adds a product of the test folder to the wish list of the admin user and takes it off.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expCommerceTestCase.php';

class expDiscountWishlistServicesTest extends expCommerceTestCase
{
    protected $groupIds = array();
    protected $wishItems = array();

    public function tearDown(): void
    {
        if ( self::$bootError === null )
        {
            $this->loginAdmin();
            foreach ( $this->wishItems as $id )
                if ( $i = eZProductCollectionItem::fetch( $id ) )
                    $i->remove();
            foreach ( $this->groupIds as $id )
                if ( eZDiscountRule::fetch( $id ) )
                {
                    expServiceBase::$trustRequest = true;
                    expServiceBase::$postData = array( 'id' => $id );
                    expServiceBase::invoke( 'expDiscountServices', 'removeGroup' );
                }
        }
        parent::tearDown();
    }

    protected function newGroup( $name = 'Test group' )
    {
        $g = $this->ok( 'expDiscountServices', 'createGroup', array(), array( 'name' => $name ) );
        $this->groupIds[] = $g['id'];
        return $g;
    }

    protected function adminObjectId()
    {
        return (int)eZUser::fetchByName( 'admin' )->attribute( 'contentobject_id' );
    }

    public function testGroupsAreCreatedRenamedListedAndRemoved()
    {
        $g = $this->newGroup( 'Gold customers' );
        $this->assertSame( 'Gold customers', $g['name'] );
        $this->assertSame( 'Silver', $this->ok( 'expDiscountServices', 'renameGroup', array(), array( 'id' => $g['id'], 'name' => 'Silver' ) )['name'] );
        $listed = $this->ok( 'expDiscountServices', 'groups' );
        $this->assertContains( $g['id'], array_column( $listed, 'id' ) );
        $this->assertSame( array( 'removed' => $g['id'] ), $this->ok( 'expDiscountServices', 'removeGroup', array(), array( 'id' => $g['id'] ) ) );
        $this->fails( 404, 'expDiscountServices', 'group', array( $g['id'] ) );
    }

    public function testGroupInputIsValidated()
    {
        $this->fails( 422, 'expDiscountServices', 'createGroup', array(), array( 'name' => ' ' ) );
        $this->fails( 400, 'expDiscountServices', 'createGroup', array(), array() );
        $this->fails( 404, 'expDiscountServices', 'renameGroup', array(), array( 'id' => 99999999, 'name' => 'x' ) );
    }

    public function testRulesWithLimitsAreCreatedUpdatedAndRemoved()
    {
        $g = $this->newGroup();
        $r = $this->ok( 'expDiscountServices', 'createRule', array(), array( 'group_id' => $g['id'], 'name' => '10 percent', 'percent' => '10' ) );
        $this->assertEqualsWithDelta( 10.0, $r['percent'], 0.001 );
        $this->assertSame( '*', $r['limitation'] );
        $u = $this->ok( 'expDiscountServices', 'updateRule', array(), array( 'id' => $r['id'], 'percent' => '12,5', 'classes' => '20', 'sections' => '1' ) );
        $this->assertEqualsWithDelta( 12.5, $u['percent'], 0.001 );
        $this->assertSame( array( 20 ), $u['classes'] );
        $this->assertSame( array( 1 ), $u['sections'] );
        $this->assertSame( '', $u['limitation'] );
        $u = $this->ok( 'expDiscountServices', 'updateRule', array(), array( 'id' => $r['id'], 'classes' => '', 'sections' => '' ) );
        $rules = $this->ok( 'expDiscountServices', 'rules', array( $g['id'] ) );
        $this->assertCount( 1, $rules );
        $this->assertSame( $r['id'], $this->ok( 'expDiscountServices', 'rule', array( $r['id'] ) )['id'] );
        $this->ok( 'expDiscountServices', 'removeRule', array(), array( 'id' => $r['id'] ) );
        $this->assertCount( 0, $this->ok( 'expDiscountServices', 'rules', array( $g['id'] ) ) );
    }

    public function testRuleInputIsValidated()
    {
        $g = $this->newGroup();
        $this->fails( 422, 'expDiscountServices', 'createRule', array(), array( 'group_id' => $g['id'], 'name' => 'x', 'percent' => '150' ) );
        $this->fails( 422, 'expDiscountServices', 'createRule', array(), array( 'group_id' => $g['id'], 'name' => 'x', 'percent' => 'abc' ) );
        $this->fails( 404, 'expDiscountServices', 'createRule', array(), array( 'group_id' => 99999999, 'name' => 'x', 'percent' => '5' ) );
        $this->fails( 422, 'expDiscountServices', 'createRule', array(), array( 'group_id' => $g['id'], 'name' => 'x', 'percent' => '5', 'classes' => 'abc' ) );
    }

    public function testMembersAndTheDiscountOfAUser()
    {
        $g = $this->newGroup();
        $this->ok( 'expDiscountServices', 'createRule', array(), array( 'group_id' => $g['id'], 'name' => 'all', 'percent' => '20' ) );
        $admin = $this->adminObjectId();
        $m = $this->ok( 'expDiscountServices', 'addMember', array(), array( 'group_id' => $g['id'], 'object_id' => $admin ) );
        $this->assertSame( $admin, $m[0]['object_id'] );
        $this->fails( 409, 'expDiscountServices', 'addMember', array(), array( 'group_id' => $g['id'], 'object_id' => $admin ) );
        $this->assertContains( $g['id'], array_column( $this->ok( 'expDiscountServices', 'groupsOfUser', array( $admin ) ), 'id' ) );
        $p = $this->createProduct( '10' );
        $this->assertGreaterThanOrEqual( 20.0, $this->ok( 'expDiscountServices', 'mine', array( $p ) )['percent'] );
        $this->assertGreaterThanOrEqual( 20.0, $this->ok( 'expDiscountServices', 'forUser', array( eZUser::fetchByName( 'admin' )->attribute( 'contentobject_id' ), $p ) )['percent'] );
        $group = $this->ok( 'expDiscountServices', 'group', array( $g['id'] ) );
        $this->assertCount( 1, $group['members'] );
        $this->assertCount( 1, $group['rules'] );
        $this->assertSame( array(), $this->ok( 'expDiscountServices', 'removeMember', array(), array( 'group_id' => $g['id'], 'object_id' => $admin ) ) );
        $this->fails( 404, 'expDiscountServices', 'removeMember', array(), array( 'group_id' => $g['id'], 'object_id' => $admin ) );
    }

    public function testOnlyUsersAndUserGroupsCanBeMembers()
    {
        $g = $this->newGroup();
        $folder = eZContentObjectTreeNode::fetch( $this->testFolder() );
        $this->fails( 422, 'expDiscountServices', 'addMember', array(), array( 'group_id' => $g['id'], 'object_id' => $folder->attribute( 'contentobject_id' ) ) );
        $this->fails( 404, 'expDiscountServices', 'addMember', array(), array( 'group_id' => $g['id'], 'object_id' => 99999999 ) );
    }

    // ---------------------------------------------------------------- wish list

    public function testAddingAndRemovingAProductOnTheWishList()
    {
        $p = $this->createProduct( '9' );
        $v = $this->ok( 'expProductServices', 'view', array( $p ) );
        $w = $this->ok( 'expWishlistServices', 'add', array(), array( 'object_id' => $v['object_id'] ) );
        foreach ( $w['items'] as $i )
            if ( $i['object_id'] === $v['object_id'] )
                $this->wishItems[] = $i['id'];
        $this->assertCount( 1, $this->wishItems );
        $c = $this->ok( 'expWishlistServices', 'contains', array( $v['object_id'] ) );
        $this->assertTrue( $c['contains'] );
        $this->assertSame( $this->wishItems[0], $c['item_id'] );
        $again = $this->ok( 'expWishlistServices', 'add', array(), array( 'object_id' => $v['object_id'] ) );
        $this->assertSame( count( $w['items'] ), count( $again['items'] ), 'a product is on the list once' );
        $w = $this->ok( 'expWishlistServices', 'remove', array(), array( 'item_id' => $this->wishItems[0] ) );
        $this->assertNotContains( $v['object_id'], array_column( $w['items'], 'object_id' ) );
        $this->assertFalse( $this->ok( 'expWishlistServices', 'contains', array( $v['object_id'] ) )['contains'] );
    }

    public function testWishListViewCountAndItems()
    {
        $view = $this->ok( 'expWishlistServices', 'view' );
        $this->assertSame( $this->adminObjectId(), $view['user_id'] );
        $this->assertSame( count( $view['items'] ), $this->ok( 'expWishlistServices', 'count' )['count'] );
        $r = $this->call( 'expWishlistServices', 'items', array( 5, 0 ) );
        $this->assertTrue( $r['ok'] );
        $this->assertSame( count( $view['items'] ), $r['meta']['total'] );
    }

    public function testWishListRefusesBadInput()
    {
        $this->fails( 404, 'expWishlistServices', 'add', array(), array( 'object_id' => 99999999 ) );
        $folder = eZContentObjectTreeNode::fetch( $this->testFolder() );
        $this->fails( 422, 'expWishlistServices', 'add', array(), array( 'object_id' => $folder->attribute( 'contentobject_id' ) ) );
        $this->fails( 404, 'expWishlistServices', 'remove', array(), array( 'item_id' => 99999999 ) );
    }

    public function testMoveToBasketPutsTheProductInTheBasketAndOffTheList()
    {
        $this->testSession();
        $p = $this->createProduct( '11' );
        $v = $this->ok( 'expProductServices', 'view', array( $p ) );
        $w = $this->ok( 'expWishlistServices', 'add', array(), array( 'object_id' => $v['object_id'] ) );
        $line = null;
        foreach ( $w['items'] as $i )
            if ( $i['object_id'] === $v['object_id'] )
                $line = $i['id'];
        $this->wishItems[] = $line;
        $r = $this->ok( 'expWishlistServices', 'moveToBasket', array(), array( 'item_id' => $line, 'quantity' => 2 ) );
        $this->assertSame( 2, $r['basket']['items'][0]['count'] );
        $this->assertNotContains( $v['object_id'], array_column( $r['wishlist']['items'], 'object_id' ) );
    }

    public function testAdminSeesWishLists()
    {
        $list = $this->call( 'expWishlistServices', 'adminList', array( 10, 0 ) );
        $this->assertTrue( $list['ok'] );
        $this->assertTrue( $this->call( 'expWishlistServices', 'popular', array( 5 ) )['ok'] );
        $this->fails( 404, 'expWishlistServices', 'adminView', array( 99999999 ) );
    }

    public function testAnonymousHasNoWishListOrDiscounts()
    {
        $this->loginAnonymous();
        $this->fails( 401, 'expWishlistServices', 'view' );
        $this->fails( 401, 'expDiscountServices', 'groups' );
    }
}
