<?php
/**
 * The order services against the live database. No real order is created: the order read services are run on what
 * exists (the lists, statuses, statistics) and the write services on the temporary order of a test basket, which is
 * removed again.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expCommerceTestCase.php';

class expOrderServicesTest extends expCommerceTestCase
{
    /** A temporary order of a test basket: array( order id ). */
    protected function tempOrder()
    {
        $this->testSession();
        $p = $this->createProduct( '10' );
        $v = $this->ok( 'expProductServices', 'view', array( $p ) );
        $this->ok( 'expBasketServices', 'add', array(), array( 'object_id' => $v['object_id'], 'quantity' => 2 ) );
        $o = $this->ok( 'expBasketServices', 'startCheckout' );
        return $o['id'];
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testStatusesAreListed()
    {
        $s = $this->ok( 'expOrderServices', 'statuses' );
        $this->assertGreaterThanOrEqual( 5, count( $s ) );
        $this->assertArrayHasKey( 'status_id', $s[0] );
        $this->assertArrayHasKey( 'name', $s[0] );
    }

    public function testListCountAndDashboardAreConsistent()
    {
        $count = $this->ok( 'expOrderServices', 'count', array( 'all' ) )['count'];
        $list = $this->call( 'expOrderServices', 'list', array( 5, 0, 'all' ) );
        $this->assertTrue( $list['ok'] );
        $this->assertSame( $count, $list['meta']['total'] );
        $dash = $this->ok( 'expOrderServices', 'dashboard' );
        $this->assertSame( $count, $dash['orders'] );
        $this->fails( 400, 'expOrderServices', 'list', array( 5, 0, 'bogus' ) );
        $this->fails( 400, 'expOrderServices', 'list', array( 5, 0, 'all', 'nonsense' ) );
    }

    public function testViewOfAMissingOrderIs404()
    {
        $this->fails( 404, 'expOrderServices', 'view', array( 99999999 ) );
        $this->fails( 404, 'expOrderServices', 'byNumber', array( 99999999 ) );
        $this->fails( 404, 'expOrderServices', 'items', array( 99999999 ) );
        $this->fails( 404, 'expOrderServices', 'myOrder', array( 99999999 ) );
    }

    public function testMyOrdersOfTheTestUser()
    {
        $this->assertIsInt( $this->ok( 'expOrderServices', 'myCount' )['count'] );
        $r = $this->call( 'expOrderServices', 'mine' );
        $this->assertTrue( $r['ok'] );
    }

    public function testStatisticsCustomersAndSearch()
    {
        $this->assertArrayHasKey( 'products', $this->ok( 'expOrderServices', 'statistics' ) );
        $this->assertArrayHasKey( 'products', $this->ok( 'expOrderServices', 'statistics', array( 2025 ) ) );
        $this->fails( 400, 'expOrderServices', 'statistics', array( 0, 5 ) );
        $this->assertTrue( $this->call( 'expOrderServices', 'customers', array( 5, 0 ) )['ok'] );
        $this->assertSame( array(), $this->ok( 'expOrderServices', 'search', array( 'no-such-customer-xyz' ) ) );
        $this->assertIsArray( $this->ok( 'expOrderServices', 'customerOrders', array( 99999999 ) ) );
        $this->assertIsArray( $this->ok( 'expOrderServices', 'customerProducts', array( 99999999 ) ) );
    }

    public function testViewOfATemporaryOrder()
    {
        $id = $this->tempOrder();
        $o = $this->ok( 'expOrderServices', 'view', array( $id ) );
        $this->assertTrue( $o['is_temporary'] );
        $this->assertCount( 1, $o['items'] );
        $this->assertSame( 2, $o['items'][0]['count'] );
        $this->assertEqualsWithDelta( 20.0, $o['total_inc_vat'], 0.01 );
        $items = $this->ok( 'expOrderServices', 'items', array( $id ) );
        $this->assertCount( 1, $items );
        $this->assertIsArray( $this->ok( 'expOrderServices', 'orderItems', array( $id ) ) );
        $this->assertIsArray( $this->ok( 'expOrderServices', 'history', array( $id ) ) );
        $nr = $this->ok( 'expOrderServices', 'byNumber', array( $o['order_nr'] ) );
        $this->assertSame( $id, $nr['id'] );
        $this->assertArrayHasKey( 'email', $this->ok( 'expOrderServices', 'account', array( $id ) ) );
        $this->ok( 'expBasketServices', 'cancelCheckout' );
    }

    public function testSetStatusChangesAndRecordsTheHistory()
    {
        $id = $this->tempOrder();
        $before = $this->ok( 'expOrderServices', 'view', array( $id ) );
        $options = $this->ok( 'expOrderServices', 'statusOptions', array( $id ) );
        $this->assertNotEmpty( $options );
        $target = null;
        foreach ( $options as $s )
            if ( $s['status_id'] !== $before['status_id'] )
                $target = $s['status_id'];
        $o = $this->ok( 'expOrderServices', 'setStatus', array(), array( 'order_id' => $id, 'status_id' => $target ) );
        $this->assertSame( $target, $o['status_id'] );
        $this->assertNotEmpty( $this->ok( 'expOrderServices', 'history', array( $id ) ) );
        $this->fails( 422, 'expOrderServices', 'setStatus', array(), array( 'order_id' => $id, 'status_id' => 99999 ) );
        $this->ok( 'expBasketServices', 'cancelCheckout' );
    }

    public function testArchiveAndUnarchive()
    {
        $id = $this->tempOrder();
        $this->assertTrue( $this->ok( 'expOrderServices', 'archive', array(), array( 'order_id' => $id ) )['is_archived'] );
        $this->assertFalse( $this->ok( 'expOrderServices', 'unarchive', array(), array( 'order_id' => $id ) )['is_archived'] );
        $this->fails( 404, 'expOrderServices', 'archive', array(), array( 'order_id' => 99999999 ) );
        $this->ok( 'expBasketServices', 'cancelCheckout' );
    }

    public function testMyOrderRefusesAnOrderOfAnotherUser()
    {
        $id = $this->tempOrder();
        $this->fails( 404, 'expOrderServices', 'myOrder', array( $id ) );
        $this->ok( 'expBasketServices', 'cancelCheckout' );
    }

    public function testRemoveOfAMissingOrderIs404()
    {
        $this->fails( 404, 'expOrderServices', 'remove', array(), array( 'order_id' => 99999999 ) );
    }

    public function testAnAnonymousVisitorCannotListOrders()
    {
        $this->loginAnonymous();
        $this->fails( 401, 'expOrderServices', 'list' );
        $this->fails( 401, 'expOrderServices', 'mine' );
    }
}
