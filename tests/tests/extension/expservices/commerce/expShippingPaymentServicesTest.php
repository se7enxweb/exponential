<?php
/**
 * The shipping and payment status services: reads on what the installation has (no payment is created or approved).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expCommerceTestCase.php';

class expShippingPaymentServicesTest extends expCommerceTestCase
{
    public function testShippingStatusAndHandler()
    {
        $s = $this->ok( 'expShippingServices', 'status' );
        $this->assertArrayHasKey( 'handler_loaded', $s );
        $this->assertArrayHasKey( 'simple_shipping', $s );
        $h = $this->ok( 'expShippingServices', 'handler' );
        $this->assertIsArray( $h['directories'] );
    }

    public function testShippingSettingsAndHandlers()
    {
        $s = $this->ok( 'expShippingServices', 'settings' );
        $this->assertArrayHasKey( 'shipping', $s );
        $this->assertArrayHasKey( 'basket_info', $s );
        $this->assertArrayHasKey( 'loaded', $this->ok( 'expShippingServices', 'basketInfoHandler' ) );
    }

    public function testShippingOfTheBasketAndAnOrder()
    {
        $this->testSession();
        $this->assertTrue( $this->call( 'expShippingServices', 'basket' )['ok'] );
        $this->assertArrayHasKey( 'vat', $this->ok( 'expShippingServices', 'vatOfShipping' ) );
        $this->fails( 404, 'expShippingServices', 'order', array( 99999999 ) );
    }

    public function testShippingStatusIsPublicButSettingsAreNot()
    {
        $this->loginAnonymous();
        $this->assertTrue( $this->call( 'expShippingServices', 'status' )['ok'] );
        $this->fails( 401, 'expShippingServices', 'settings' );
    }

    public function testPaymentCountsAreConsistent()
    {
        $c = $this->ok( 'expPaymentServices', 'counts' );
        $this->assertSame( $c['pending'] + $c['approved'], $c['total'] );
        $list = $this->call( 'expPaymentServices', 'list', array( 5, 0, 'all' ) );
        $this->assertTrue( $list['ok'] );
        $this->assertSame( $c['total'], $list['meta']['total'] );
    }

    public function testPaymentListFiltersByStatus()
    {
        $this->assertTrue( $this->call( 'expPaymentServices', 'list', array( 5, 0, 'pending' ) )['ok'] );
        $this->assertTrue( $this->call( 'expPaymentServices', 'list', array( 5, 0, 'approved' ) )['ok'] );
        $this->fails( 400, 'expPaymentServices', 'list', array( 5, 0, 'weird' ) );
    }

    public function testPaymentLookupsOfMissingThings()
    {
        $this->fails( 404, 'expPaymentServices', 'view', array( 99999999 ) );
        $this->assertNull( $this->ok( 'expPaymentServices', 'byOrder', array( 99999999 ) ) );
        $this->fails( 404, 'expPaymentServices', 'myStatus', array( 99999999 ) );
        $this->fails( 404, 'expPaymentServices', 'approve', array(), array( 'id' => 99999999 ) );
    }

    public function testGatewaysEventsAndHandlers()
    {
        $this->assertArrayHasKey( 'available', $this->ok( 'expPaymentServices', 'gateways' ) );
        $this->assertIsArray( $this->ok( 'expPaymentServices', 'workflowEvents' ) );
        $h = $this->ok( 'expPaymentServices', 'handlers' );
        $this->assertArrayHasKey( 'account', $h );
        $this->assertArrayHasKey( 'confirm', $h );
    }

    public function testMethodsAlwaysOfferSomething()
    {
        $m = $this->ok( 'expPaymentServices', 'methods' );
        $this->assertNotEmpty( $m );
        $this->assertArrayHasKey( 'method', $m[0] );
    }

    public function testPaymentsAreForAdministrators()
    {
        $this->loginAnonymous();
        $this->fails( 401, 'expPaymentServices', 'counts' );
        $this->fails( 401, 'expPaymentServices', 'approve', array(), array( 'id' => 1 ) );
        $this->assertTrue( $this->call( 'expPaymentServices', 'methods' )['ok'] );
    }
}
