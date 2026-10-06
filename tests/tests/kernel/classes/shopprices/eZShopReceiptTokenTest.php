<?php
/**
 * eZShopReceipt without the database: the receipt token of an order (payload and bcrypt signature, the same order
 * always the same token), every token refused before the order is looked up (malformed, too long, wrong version,
 * missing fields, a signature of another payload, a changed payload), the cost bounds, the secret from shop.ini,
 * and the link view and URLs.
 *
 * No database. The secret is set for the test and put back; the secret file is never read or written.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group shop
 */

class eZShopReceiptTokenTest extends PHPUnit\Framework\TestCase
{
    private $secret;
    private $saved = array();

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $property = new ReflectionProperty( 'eZShopReceipt', 'secret' );
        $this->secret = $property->getValue();
        $property->setValue( null, str_repeat( 'k1-secret-', 4 ) );
        $this->setting( 'OrderReceiptSettings', 'BcryptCost', '4' );
    }

    protected function tearDown(): void
    {
        ( new ReflectionProperty( 'eZShopReceipt', 'secret' ) )->setValue( null, $this->secret );
        $ini = eZINI::instance( 'shop.ini' );
        foreach ( array_reverse( $this->saved ) as $entry )
        {
            if ( $entry[2] === null )
                $ini->removeSetting( $entry[0], $entry[1] );
            else
                $ini->setVariable( $entry[0], $entry[1], $entry[2][0] );
        }
        $this->saved = array();
    }

    private function setting( $group, $name, $value )
    {
        $ini = eZINI::instance( 'shop.ini' );
        $this->saved[] = array( $group, $name, $ini->hasVariable( $group, $name ) ? array( $ini->variable( $group, $name ) ) : null );
        if ( $value === null )
            $ini->removeSetting( $group, $name );
        else
            $ini->setVariable( $group, $name, $value );
    }

    private static function order( $id = 12, $created = 1790000000, $userID = 10 )
    {
        return new eZOrder( array( 'id' => $id, 'created' => $created, 'user_id' => $userID, 'is_temporary' => 0 ) );
    }

    private static function encode( $bytes )
    {
        return rtrim( strtr( base64_encode( $bytes ), '+/', '-_' ), '=' );
    }

    private static function call( $method, array $arguments = array() )
    {
        $reflection = new ReflectionMethod( 'eZShopReceipt', $method );
        return $reflection->invokeArgs( null, $arguments );
    }

    public function testTokenCarriesTheOrderAndASignature()
    {
        $token = eZShopReceipt::token( self::order() );
        $this->assertMatchesRegularExpression( '/^[A-Za-z0-9_-]+\.[A-Za-z0-9_-]+$/', $token );
        list( $payload, $signature ) = explode( '.', $token );
        $this->assertSame( array( 'v' => 1, 'o' => 12, 'c' => 1790000000, 'u' => 10 ),
                           json_decode( base64_decode( strtr( $payload, '-_', '+/' ) ), true ) );
        $hash = base64_decode( strtr( $signature, '-_', '+/' ) );
        $this->assertStringStartsWith( '$2y$04$', $hash );
        $this->assertSame( 60, strlen( $hash ) );
    }

    public function testSameOrderSameTokenOtherOrderOtherToken()
    {
        $this->assertSame( eZShopReceipt::token( self::order() ), eZShopReceipt::token( self::order() ) );
        $this->assertNotSame( eZShopReceipt::token( self::order() ), eZShopReceipt::token( self::order( 13 ) ) );
        $this->assertNotSame( eZShopReceipt::token( self::order() ), eZShopReceipt::token( self::order( 12, 1790000001 ) ) );
    }

    public function testOtherSecretOtherToken()
    {
        $first = eZShopReceipt::token( self::order() );
        ( new ReflectionProperty( 'eZShopReceipt', 'secret' ) )->setValue( null, str_repeat( 'other-k1-', 5 ) );
        $this->assertNotSame( $first, eZShopReceipt::token( self::order() ) );
    }

    public static function malformedProvider()
    {
        return array(
            'empty' => array( '' ),
            'no dot' => array( 'abc' ),
            'two dots' => array( 'a.b.c' ),
            'too long' => array( str_repeat( 'a', 300 ) . '.' . str_repeat( 'b', 300 ) ),
            'bad characters' => array( 'a+b.c/d' ),
            'not json' => array( self::encode( 'not json' ) . '.' . self::encode( 'x' ) ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('malformedProvider')]
    public function testMalformedTokensAreRefused( $token )
    {
        $this->assertNull( eZShopReceipt::orderFromToken( $token ) );
    }

    public function testWrongVersionOrMissingFieldsAreRefused()
    {
        foreach ( array( array( 'v' => 2, 'o' => 1, 'c' => 1, 'u' => 1 ), array( 'v' => '1', 'o' => 1, 'c' => 1, 'u' => 1 ),
                         array( 'v' => 1, 'o' => 1, 'c' => 1 ) ) as $data )
        {
            $payload = self::encode( json_encode( $data ) );
            $signature = self::encode( self::call( 'sign', array( $payload ) ) );
            $this->assertNull( eZShopReceipt::orderFromToken( $payload . '.' . $signature ), json_encode( $data ) );
        }
    }

    public function testChangedPayloadOrBorrowedSignatureIsRefused()
    {
        list( $payload, $signature ) = explode( '.', eZShopReceipt::token( self::order() ) );
        list( $otherPayload, $otherSignature ) = explode( '.', eZShopReceipt::token( self::order( 99 ) ) );
        $this->assertNull( eZShopReceipt::orderFromToken( $otherPayload . '.' . $signature ) );
        $this->assertNull( eZShopReceipt::orderFromToken( $payload . '.' . $otherSignature ) );
        $this->assertNull( eZShopReceipt::orderFromToken( $payload . '.' . self::encode( 'not a hash' ) ) );
    }

    public function testCostStaysBetweenFourAndSix()
    {
        foreach ( array( '1' => 4, '4' => 4, '5' => 5, '6' => 6, '12' => 6, 'x' => 4 ) as $setting => $cost )
        {
            $this->setting( 'OrderReceiptSettings', 'BcryptCost', (string)$setting );
            $this->assertSame( $cost, self::call( 'cost' ), "BcryptCost=$setting" );
        }
        $this->setting( 'OrderReceiptSettings', 'BcryptCost', null );
        $this->assertSame( 5, self::call( 'cost' ) );
    }

    public function testSecretFromTheSettingWhenLongEnough()
    {
        $property = new ReflectionProperty( 'eZShopReceipt', 'secret' );
        $property->setValue( null, null );
        $this->setting( 'OrderReceiptSettings', 'Secret', '  ' . str_repeat( 'S', 32 ) . '  ' );
        $this->assertSame( str_repeat( 'S', 32 ), eZShopReceipt::secret() );
        $this->setting( 'OrderReceiptSettings', 'Secret', 'changed later, the first one stays' );
        $this->assertSame( str_repeat( 'S', 32 ), eZShopReceipt::secret() );
    }

    public function testLinkView()
    {
        $this->setting( 'OrderViewSettings', 'OrderLinkView', null );
        $this->assertSame( 'orderview', eZShopReceipt::linkView() );
        $this->setting( 'OrderViewSettings', 'OrderLinkView', ' OrderReceipt ' );
        $this->assertSame( 'orderreceipt', eZShopReceipt::linkView() );
        $this->setting( 'OrderViewSettings', 'OrderLinkView', 'anything' );
        $this->assertSame( 'orderview', eZShopReceipt::linkView() );
    }

    public function testUrls()
    {
        $order = self::order();
        $this->assertSame( '/shop/orderreceipt/' . eZShopReceipt::token( $order ), eZShopReceipt::receiptURL( $order ) );
        $this->setting( 'OrderViewSettings', 'OrderLinkView', 'orderview' );
        $this->assertSame( '/shop/orderview/12/', eZShopReceipt::linkURL( $order ) );
        $this->setting( 'OrderViewSettings', 'OrderLinkView', 'orderreceipt' );
        $this->assertSame( eZShopReceipt::receiptURL( $order ), eZShopReceipt::linkURL( $order ) );
    }

    public function testViewThroughTheTokenIsAlwaysAllowed()
    {
        $this->assertTrue( eZShopReceipt::canView( self::order(), null, true ) );
    }
}
