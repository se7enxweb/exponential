<?php
/**
 * The link from the shop's order lists to shop/customerorderview carries the customer's e-mail address in the
 * path. An address with a "+" (or "?", "#", "&") arrived changed: the templates put it into the path as it is, the
 * request URI is decoded twice (eZSys and eZURI; once with [URLTranslator] DecodeOnce=enabled) and the view decoded
 * it a third time, so "a+b@example.com" was looked up as "a b@example.com" and the customer's orders were not found.
 * The case of LoveStack (mugoweb/ezpublish-legacy) commit 5a17c08bfc.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group shop
 */

class eZShopCustomerOrderViewEmailTest extends PHPUnit\Framework\TestCase
{
    /**
     * every template of the kernel designs that links to shop/customerorderview with an order's address; the
     * customer list is not among them, eZOrder::customerList() gives its addresses urlencode()d already
     */
    private static $templates = array(
        'design/standard/templates/shop/orderlist.tpl',
        'design/admin/templates/shop/orderlist.tpl',
        'design/admin/templates/shop/archivelist.tpl',
        'design/admin/templates/shop/dashboard.tpl',
        'design/admin4/templates/shop/orderlist.tpl',
        'design/admin4/templates/shop/archivelist.tpl',
        'design/admin4/templates/shop/dashboard.tpl',
    );

    private static $addresses = array( 'a+b@example.com', 'first.last@example.com', "o'neil+shop@example.com",
                                       'what?ever@example.com', 'a#b&c@example.com', 'plain@example.com' );

    public function testEveryLinkEncodesTheAddress()
    {
        $root = dirname( __DIR__, 4 );
        foreach ( self::$templates as $template )
        {
            $source = file_get_contents( $root . '/' . $template );
            $this->assertSame( 1, preg_match_all( '#customerorderview/#', $source ) >= 1 ? 1 : 0, "$template links to the view" );
            preg_match_all( '#concat\(\s*[\'"]/shop/customerorderview/[\'"][^}]*\)\s*\|ezurl#', $source, $links );
            $this->assertTrue( count( $links[0] ) > 0, $template );
            foreach ( $links[0] as $link )
                $this->assertStringContainsString( '|rawurlencode', $link, $template );
        }
    }

    /** the address as the link encodes it, through both URI decodings, comes out unchanged */
    public function testAddressSurvivesTheRequestDecoding()
    {
        foreach ( self::$addresses as $address )
        {
            $segment = rawurlencode( $address );
            $decodedTwice = urldecode( urldecode( $segment ) );   // [URLTranslator] DecodeOnce=disabled (the default)
            $decodedOnce = urldecode( $segment );                 // DecodeOnce=enabled
            $this->assertSame( $address, \Exponential\View\Kernel\Shop\Customerorderview::emailFromURL( $decodedTwice ), $address );
            $this->assertSame( $address, \Exponential\View\Kernel\Shop\Customerorderview::emailFromURL( $decodedOnce ), $address );
        }
    }

    /** the customer list's links: eZOrder::customerList() urlencode()s the address, the template adds nothing */
    public function testCustomerListAddressSurvivesTheRequestDecoding()
    {
        $root = dirname( __DIR__, 4 );
        foreach ( array( 'design/admin/templates/shop/customerlist.tpl', 'design/admin4/templates/shop/customerlist.tpl' ) as $template )
            $this->assertStringNotContainsString( '$Customers.email|rawurlencode', file_get_contents( $root . '/' . $template ), $template );
        foreach ( self::$addresses as $address )
        {
            $segment = urlencode( $address );
            $this->assertSame( $address, \Exponential\View\Kernel\Shop\Customerorderview::emailFromURL( urldecode( urldecode( $segment ) ) ), $address );
            $this->assertSame( $address, \Exponential\View\Kernel\Shop\Customerorderview::emailFromURL( urldecode( $segment ) ), $address );
        }
    }

    /** a link written before (or by a design that does not encode) still finds an address with a "+" */
    public function testUnencodedLinkStillFindsThePlusAddress()
    {
        $this->assertSame( 'a+b@example.com', \Exponential\View\Kernel\Shop\Customerorderview::emailFromURL( urldecode( urldecode( 'a+b@example.com' ) ) ) );
        $this->assertSame( 'plain@example.com', \Exponential\View\Kernel\Shop\Customerorderview::emailFromURL( 'plain@example.com' ) );
        $this->assertSame( '', \Exponential\View\Kernel\Shop\Customerorderview::emailFromURL( null ) );
    }
}
