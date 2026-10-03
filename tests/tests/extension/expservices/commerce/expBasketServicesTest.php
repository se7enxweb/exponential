<?php
/**
 * The basket services on a test session: the basket and its lines exist only for a session key made by the test and
 * are removed in tearDown. The checkout steps create a temporary order and cancel it; no order is completed.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expCommerceTestCase.php';

class expBasketServicesTest extends expCommerceTestCase
{
    protected function addProduct( $node, $quantity = 1 )
    {
        $v = $this->ok( 'expProductServices', 'view', array( $node ) );
        return $this->ok( 'expBasketServices', 'add', array(), array( 'object_id' => $v['object_id'], 'quantity' => $quantity ) );
    }

    public function testAnEmptyBasketOfANewSession()
    {
        $this->testSession();
        $b = $this->ok( 'expBasketServices', 'view' );
        $this->assertNull( $b['basket_id'] );
        $this->assertSame( array(), $b['items'] );
        $this->assertTrue( $this->ok( 'expBasketServices', 'isEmpty' )['empty'] );
        $this->assertSame( 0, $this->ok( 'expBasketServices', 'count' )['lines'] );
    }

    public function testAddPutsAProductInTheBasket()
    {
        $this->testSession();
        $p = $this->createProduct( '10' );
        $b = $this->addProduct( $p, 2 );
        $this->assertNotNull( $b['basket_id'] );
        $this->assertCount( 1, $b['items'] );
        $this->assertSame( 2, $b['items'][0]['count'] );
        $this->assertEqualsWithDelta( 20.0, $b['totals']['total_inc_vat'], 0.01 );
        $this->assertSame( 2, $this->ok( 'expBasketServices', 'count' )['quantity'] );
        $this->assertFalse( $this->ok( 'expBasketServices', 'isEmpty' )['empty'] );
    }

    public function testAddingTheSameProductAgainRaisesTheQuantity()
    {
        $this->testSession();
        $p = $this->createProduct( '10' );
        $this->addProduct( $p, 1 );
        $b = $this->addProduct( $p, 3 );
        $this->assertCount( 1, $b['items'] );
        $this->assertSame( 4, $b['items'][0]['count'] );
    }

    public function testAddNodeByNodeId()
    {
        $this->testSession();
        $p = $this->createProduct( '7' );
        $b = $this->ok( 'expBasketServices', 'addNode', array(), array( 'node_id' => $p ) );
        $this->assertSame( $p, $b['items'][0]['node_id'] );
    }

    public function testAddRejectsBadInput()
    {
        $this->testSession();
        $p = $this->createProduct();
        $v = $this->ok( 'expProductServices', 'view', array( $p ) );
        $this->fails( 422, 'expBasketServices', 'add', array(), array( 'object_id' => $v['object_id'], 'quantity' => 0 ) );
        $this->fails( 422, 'expBasketServices', 'add', array(), array( 'object_id' => $v['object_id'], 'quantity' => 100000 ) );
        $this->fails( 404, 'expBasketServices', 'add', array(), array( 'object_id' => 99999999 ) );
        $this->fails( 400, 'expBasketServices', 'add', array(), array() );
        $folder = eZContentObjectTreeNode::fetch( $this->testFolder() );
        $this->fails( 422, 'expBasketServices', 'add', array(), array( 'object_id' => $folder->attribute( 'contentobject_id' ) ) );
    }

    public function testCanAdd()
    {
        $this->testSession();
        $p = $this->createProduct();
        $v = $this->ok( 'expProductServices', 'view', array( $p ) );
        $this->assertTrue( $this->ok( 'expBasketServices', 'canAdd', array( $v['object_id'] ) )['can'] );
        $folder = eZContentObjectTreeNode::fetch( $this->testFolder() );
        $r = $this->ok( 'expBasketServices', 'canAdd', array( $folder->attribute( 'contentobject_id' ) ) );
        $this->assertFalse( $r['can'] );
        $this->assertSame( 'not a product', $r['reason'] );
    }

    public function testUpdateSetsTheQuantityAndZeroRemovesTheLine()
    {
        $this->testSession();
        $p = $this->createProduct( '5' );
        $line = $this->addProduct( $p )['items'][0]['id'];
        $b = $this->ok( 'expBasketServices', 'update', array(), array( 'item_id' => $line, 'quantity' => 6 ) );
        $this->assertSame( 6, $b['items'][0]['count'] );
        $this->assertEqualsWithDelta( 30.0, $b['totals']['total_inc_vat'], 0.01 );
        $b = $this->ok( 'expBasketServices', 'update', array(), array( 'item_id' => $line, 'quantity' => 0 ) );
        $this->assertSame( array(), $b['items'] );
    }

    public function testUpdateRefusesALineOfAnotherBasket()
    {
        $this->testSession();
        $p = $this->createProduct();
        $this->addProduct( $p );
        $this->fails( 404, 'expBasketServices', 'update', array(), array( 'item_id' => 99999999, 'quantity' => 1 ) );
        $this->fails( 422, 'expBasketServices', 'update', array(), array( 'item_id' => $this->ok( 'expBasketServices', 'items' )[0]['id'], 'quantity' => -1 ) );
    }

    public function testUpdateManyAndRemoveAndEmpty()
    {
        $this->testSession();
        $a = $this->createProduct( '1' );
        $b = $this->createProduct( '2' );
        $this->addProduct( $a );
        $basket = $this->addProduct( $b );
        $ids = array_column( $basket['items'], 'id' );
        $this->assertCount( 2, $ids );
        $r = $this->ok( 'expBasketServices', 'updateMany', array(), array( 'quantities' => json_encode( array( $ids[0] => 3, $ids[1] => 4 ) ) ) );
        $this->assertSame( 7, $r['totals']['quantity'] );
        $r = $this->ok( 'expBasketServices', 'remove', array(), array( 'item_id' => $ids[0] ) );
        $this->assertCount( 1, $r['items'] );
        $r = $this->ok( 'expBasketServices', 'empty' );
        $this->assertSame( array(), $r['items'] );
        $this->fails( 400, 'expBasketServices', 'updateMany', array(), array( 'quantities' => '5' ) );
    }

    public function testItemTotalsAndVatGroups()
    {
        $this->testSession();
        $p = $this->createProduct( '10' );
        $b = $this->addProduct( $p, 3 );
        $item = $this->ok( 'expBasketServices', 'item', array( $b['items'][0]['id'] ) );
        $this->assertEqualsWithDelta( 30.0, $item['total_inc_vat'], 0.01 );
        $t = $this->ok( 'expBasketServices', 'totals' );
        $this->assertEqualsWithDelta( 30.0, $t['totals']['total_inc_vat'], 0.01 );
        $this->assertNotEmpty( $t['vat'] );
        $this->fails( 404, 'expBasketServices', 'item', array( 99999999 ) );
    }

    public function testRefreshPricesFollowsAProductPriceChange()
    {
        $this->testSession();
        $p = $this->createProduct( '10' );
        $this->addProduct( $p, 2 );
        $this->ok( 'expProductServices', 'setPrice', array( $p ), array( 'price' => '15' ) );
        $b = $this->ok( 'expBasketServices', 'refreshPrices' );
        $this->assertEqualsWithDelta( 30.0, $b['totals']['total_inc_vat'], 0.01 );
    }

    public function testCheckoutStatusAndSteps()
    {
        $this->testSession();
        $s = $this->ok( 'expBasketServices', 'checkoutStatus' );
        $this->assertFalse( $s['can_checkout'] );
        $this->assertContains( 'the basket is empty', $s['blockers'] );
        $p = $this->createProduct( '10' );
        $this->addProduct( $p );
        $s = $this->ok( 'expBasketServices', 'checkoutStatus' );
        $this->assertTrue( $s['can_checkout'], json_encode( $s['blockers'] ) );
        $this->assertSame( 'basket', $s['steps'][0]['step'] );
    }

    public function testStartReviewAndCancelCheckoutLeaveNoOrderBehind()
    {
        $this->testSession();
        $this->fails( 409, 'expBasketServices', 'startCheckout' );
        $this->fails( 404, 'expBasketServices', 'review' );
        $p = $this->createProduct( '10' );
        $this->addProduct( $p );
        $o = $this->ok( 'expBasketServices', 'startCheckout' );
        $this->assertTrue( $o['is_temporary'] );
        $this->assertCount( 1, $o['items'] );
        $again = $this->ok( 'expBasketServices', 'startCheckout' );
        $this->assertSame( $o['id'], $again['id'], 'the temporary order is reused' );
        $review = $this->call( 'expBasketServices', 'review' );
        $this->assertTrue( $review['ok'], json_encode( $review['error'] ?? null ) );
        $this->assertTrue( $this->ok( 'expBasketServices', 'checkoutStatus' )['steps'][2]['done'] );
        $b = $this->ok( 'expBasketServices', 'cancelCheckout' );
        $this->assertSame( 0, $b['order_id'] );
        $this->assertNull( eZOrder::fetch( $o['id'] ), 'the temporary order is gone' );
        $this->assertCount( 1, $b['items'], 'the basket keeps its lines' );
    }

    public function testAdminSeesTheBasketsAndRefusesNothingElse()
    {
        $this->testSession();
        $p = $this->createProduct();
        $b = $this->addProduct( $p );
        $list = $this->call( 'expBasketServices', 'adminList', array( 5, 0 ) );
        $this->assertTrue( $list['ok'] );
        $this->assertGreaterThanOrEqual( 1, $list['meta']['total'] );
        $v = $this->ok( 'expBasketServices', 'adminView', array( $b['basket_id'] ) );
        $this->assertSame( $b['basket_id'], $v['basket_id'] );
        $this->fails( 404, 'expBasketServices', 'adminView', array( 99999999 ) );
        $this->fails( 422, 'expBasketServices', 'adminCleanup', array(), array( 'days' => 0 ) );
    }

    public function testSetCurrencyChecksTheCurrency()
    {
        $this->fails( 422, 'expBasketServices', 'setCurrency', array(), array( 'currency' => 'ZZZ' ) );
        $this->assertArrayHasKey( 'preferred', $this->ok( 'expBasketServices', 'currency' ) );
    }
}
