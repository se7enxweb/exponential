<?php
/**
 * eZPaymentCallbackChecker, the base of the payment gateways' callback checks: the callback data from POST and from
 * the query string, field values and field checks, the server IP check, the amount check with the shop's rounding
 * precision, the currency check, and the steps that need an order or a payment object when there is none.
 *
 * No database and no network: orders and product collections are stand-ins, the logger records in memory, nothing
 * is written to var/log.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group shop
 */

class eZPaymentCallbackCheckerTestLogger
{
    public $lines = array();

    public function writeTimedString( $string, $label = '' )
    {
        $this->lines[] = ( $label !== '' ? $label . ': ' : '' ) . ( is_array( $string ) ? implode( ',', $string ) : $string );
    }
}

class eZPaymentCallbackCheckerTestOrder
{
    public $total;
    public $currency;

    public function attribute( $name )
    {
        return $name === 'total_inc_vat' ? $this->total : null;
    }

    public function productCollection()
    {
        return new eZProductCollection( array( 'currency_code' => $this->currency ) );
    }
}

class eZPaymentCallbackCheckerTest extends PHPUnit\Framework\TestCase
{
    private $server;
    private $post;
    private $saved = array();

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->server = $_SERVER;
        $this->post = $_POST;
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->server;
        $_POST = $this->post;
        foreach ( array_reverse( $this->saved ) as $entry )
        {
            list( $file, $group, $name, $value ) = $entry;
            $ini = eZINI::instance( $file );
            if ( $value === null )
                $ini->removeSetting( $group, $name );
            else
                $ini->setVariable( $group, $name, $value[0] );
        }
        $this->saved = array();
    }

    private function setting( $file, $group, $name, $value )
    {
        $ini = eZINI::instance( $file );
        $this->saved[] = array( $file, $group, $name, $ini->hasVariable( $group, $name ) ? array( $ini->variable( $group, $name ) ) : null );
        if ( $value === null )
            $ini->removeSetting( $group, $name );
        else
            $ini->setVariable( $group, $name, $value );
    }

    private static function checker()
    {
        $checker = new eZPaymentCallbackChecker( 'shop.ini' );
        $checker->logger = new eZPaymentCallbackCheckerTestLogger();
        return $checker;
    }

    public function testDataFromPost()
    {
        $_POST = array( 'txn_id' => 'k1-1', 'amount' => '10.00' );
        $checker = self::checker();
        $this->assertTrue( $checker->createDataFromPOST() );
        $this->assertSame( array( 'txn_id' => 'k1-1', 'amount' => '10.00' ), $checker->callbackData );
        $this->assertSame( '10.00', $checker->getFieldValue( 'amount' ) );
        $this->assertNull( $checker->getFieldValue( 'missing' ) );
        $this->assertContains( 'getFieldValue failed: field missing does not exist.', $checker->logger->lines );
        $_POST = array();
        $this->assertFalse( $checker->createDataFromPOST() );
        $this->assertSame( array(), $checker->callbackData );
    }

    public function testDataFromQueryString()
    {
        $_SERVER['QUERY_STRING'] = 'order=12&status=ok';
        $checker = self::checker();
        $this->assertTrue( $checker->createDataFromGET() );
        $this->assertSame( array( 'order' => '12', 'status' => 'ok' ), $checker->callbackData );
        $_SERVER['QUERY_STRING'] = '';
        $this->assertFalse( $checker->createDataFromGET() );
    }

    public function testCheckDataField()
    {
        $checker = self::checker();
        $checker->callbackData = array( 'status' => 'Completed', 'n' => '5' );
        $this->assertTrue( $checker->checkDataField( 'status', 'Completed' ) );
        $this->assertTrue( $checker->checkDataField( 'n', 5 ) );
        $this->assertFalse( $checker->checkDataField( 'status', 'Pending' ) );
        $this->assertContains( 'Value          :Completed', $checker->logger->lines );
    }

    public function testServerIpCheck()
    {
        $_SERVER['REMOTE_ADDR'] = '192.0.2.10';
        unset( $_SERVER['HTTP_X_FORWARDED_FOR'], $_SERVER['HTTP_CLIENT_IP'] );
        $checker = self::checker();
        $checker->ini = new eZINI( 'k1payment.ini', 'settings', null, false, false, false, false, false );
        $this->assertTrue( $checker->checkServerIP(), 'no list: not checked' );
        $checker->ini->setVariable( 'ServerSettings', 'ServerIP', array( '192.0.2.1', '192.0.2.10' ) );
        $this->assertTrue( $checker->checkServerIP() );
        $checker->ini->setVariable( 'ServerSettings', 'ServerIP', array( '192.0.2.1' ) );
        $this->assertFalse( $checker->checkServerIP() );
    }

    public static function amountProvider()
    {
        return array(
            'same' => array( '10.00', '10.00', 2, true ),
            'same as number' => array( '10.00', 10, 2, true ),
            'rounded the same' => array( 9.999, '10.00', 2, true ),
            'different cent' => array( '10.00', '10.01', 2, false ),
            'precision zero' => array( '10.40', '10.20', 0, true ),
            'not a number' => array( '10.00', 'ten', 2, false ),
            'empty' => array( '10.00', '', 2, false ),
            'array' => array( '10.00', array( '10.00' ), 2, false ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('amountProvider')]
    public function testCheckAmount( $orderTotal, $received, $precision, $expected )
    {
        $this->setting( 'shop.ini', 'MathSettings', 'RoundingPrecision', (string)$precision );
        $checker = self::checker();
        $checker->order = new eZPaymentCallbackCheckerTestOrder();
        $checker->order->total = $orderTotal;
        $this->assertSame( $expected, $checker->checkAmount( $received ) );
    }

    public function testCheckCurrency()
    {
        $checker = self::checker();
        $checker->order = new eZPaymentCallbackCheckerTestOrder();
        $checker->order->currency = 'EUR';
        $this->assertTrue( $checker->checkCurrency( 'EUR' ) );
        $this->assertFalse( $checker->checkCurrency( 'USD' ) );
    }

    public function testStepsWithoutPaymentObjectOrOrder()
    {
        $checker = self::checker();
        $this->assertFalse( $checker->requestValidation() );
        $this->assertNull( $checker->approvePayment() );
        $this->assertNull( $checker->continueWorkflow() );
        $this->assertFalse( $checker->setupOrderAndPaymentObject( 0 ) );
        $this->assertFalse( $checker->setupOrderAndPaymentObject( null ) );
        $this->assertNull( $checker->buildRequestString() );
        $this->assertNull( $checker->handleResponse( null ) );
        $this->assertContains( 'approvePayment failed: payment object is not set', $checker->logger->lines );
        $this->assertContains( 'setupOrderAndPaymentObject failed: Invalid orderID=0', $checker->logger->lines );
    }
}
