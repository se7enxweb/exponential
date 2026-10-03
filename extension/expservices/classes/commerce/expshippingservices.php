<?php
/**
 * ezjscore/call/expshipping::<method>: what the shop knows about shipping: the handler, the simple shipping
 * workflow cost, the shipping info of a basket or an order. Read only; the cost lives in settings and workflows.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expShippingServices extends expServiceBase
{
    public static $services = array(
        'status' => array( 'summary' => 'Whether shipping is configured: handler, simple shipping workflow', 'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'handler, simple_shipping' ),
        'handler' => array( 'summary' => 'The shipping handler of shop.ini and where it is searched', 'access' => array( 'shop', 'administrate' ), 'write' => false, 'args' => array(), 'returns' => 'name, directories, loaded' ),
        'simpleShipping' => array( 'summary' => 'The cost and description of the simple shipping workflow', 'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'cost, description' ),
        'basket' => array( 'summary' => 'The shipping info of the current basket', 'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'description, cost, vat' ),
        'order' => array( 'summary' => 'The shipping line of an order (order items of shipping type)', 'access' => array( 'shop', 'administrate' ), 'write' => false, 'args' => array( 'order_id' => 'int' ), 'returns' => 'list of shipping items' ),
        'basketInfoHandler' => array( 'summary' => 'The basket info handler that calculates totals and shipping', 'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'name, loaded' ),
        'vatOfShipping' => array( 'summary' => 'The VAT split of a shipping cost (the VAT of the products it is spread over)', 'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'vat info of the current basket' ),
        'settings' => array( 'summary' => 'The ShippingSettings and BasketInfoSettings of shop.ini', 'access' => array( 'shop', 'administrate' ), 'write' => false, 'args' => array(), 'returns' => 'settings' ),
    );

    protected static function simple()
    {
        $ini = eZINI::instance( 'workflow.ini' );
        if ( !$ini->hasVariable( 'SimpleShippingWorkflow', 'ShippingCost' ) )
            return null;
        return array( 'cost' => expCommerceExport::num( $ini->variable( 'SimpleShippingWorkflow', 'ShippingCost' ) ),
            'description' => $ini->hasVariable( 'SimpleShippingWorkflow', 'ShippingDescription' ) ? $ini->variable( 'SimpleShippingWorkflow', 'ShippingDescription' ) : '' );
    }

    public static function status( array $args )
    {
        self::guard( 'status' );
        $ini = eZINI::instance( 'shop.ini' );
        $h = eZShippingManager::loadShippingHandler();
        return self::ok( array( 'handler' => $ini->hasVariable( 'ShippingSettings', 'Handler' ) ? $ini->variable( 'ShippingSettings', 'Handler' ) : null,
            'handler_loaded' => is_object( $h ), 'simple_shipping' => self::simple() ) );
    }

    public static function handler( array $args )
    {
        self::guard( 'handler' );
        $ini = eZINI::instance( 'shop.ini' );
        $h = eZShippingManager::loadShippingHandler();
        return self::ok( array( 'name' => $ini->hasVariable( 'ShippingSettings', 'Handler' ) ? $ini->variable( 'ShippingSettings', 'Handler' ) : null,
            'directories' => $ini->variable( 'ShippingSettings', 'RepositoryDirectories' ), 'extensions' => $ini->variable( 'ShippingSettings', 'ExtensionDirectories' ),
            'loaded' => is_object( $h ), 'class' => is_object( $h ) ? get_class( $h ) : null ) );
    }

    public static function simpleShipping( array $args )
    {
        self::guard( 'simpleShipping' );
        return self::ok( self::simple() );
    }

    public static function basket( array $args )
    {
        self::guard( 'basket' );
        $b = expBasketServices::invoke( 'expBasketServices', 'view' );
        $id = $b['data']['basket_id'];
        $info = null;
        if ( $id )
        {
            $basket = eZPersistentObject::fetchObject( eZBasket::definition(), null, array( 'id' => $id ) );
            $info = eZShippingManager::getShippingInfo( $basket->attribute( 'productcollection_id' ) );
        }
        return self::ok( is_array( $info ) ? array( 'description' => isset( $info['description'] ) ? $info['description'] : null,
            'cost' => isset( $info['cost'] ) ? expCommerceExport::num( $info['cost'] ) : null, 'vat_value' => isset( $info['vat_value'] ) ? $info['vat_value'] : null,
            'is_vat_inc' => isset( $info['is_vat_inc'] ) ? (bool)$info['is_vat_inc'] : null ) : null );
    }

    public static function order( array $args )
    {
        self::guard( 'order' );
        $o = eZOrder::fetch( self::arg( $args, 0, 'int' ) );
        if ( !$o instanceof eZOrder )
            throw new expServiceException( 'No such order', 404 );
        $out = array();
        foreach ( $o->orderItems() as $i )
            if ( stripos( (string)$i->attribute( 'type' ), 'ship' ) !== false )
                $out[] = array( 'type' => $i->attribute( 'type' ), 'description' => $i->attribute( 'description' ), 'price_inc_vat' => expCommerceExport::num( $i->priceIncVAT() ),
                    'price_ex_vat' => expCommerceExport::num( $i->priceExVAT() ) );
        return self::ok( $out );
    }

    public static function basketInfoHandler( array $args )
    {
        self::guard( 'basketInfoHandler' );
        $ini = eZINI::instance( 'shop.ini' );
        $h = eZShippingManager::loadBasketInfoHandler();
        return self::ok( array( 'name' => $ini->hasVariable( 'BasketInfoSettings', 'Handler' ) ? $ini->variable( 'BasketInfoSettings', 'Handler' ) : null, 'loaded' => is_object( $h ) ) );
    }

    public static function vatOfShipping( array $args )
    {
        self::guard( 'vatOfShipping' );
        $b = expBasketServices::invoke( 'expBasketServices', 'view' );
        return self::ok( array( 'vat' => $b['data']['vat'], 'totals' => $b['data']['totals'] ) );
    }

    public static function settings( array $args )
    {
        self::guard( 'settings' );
        $ini = eZINI::instance( 'shop.ini' );
        return self::ok( array( 'shipping' => $ini->hasGroup( 'ShippingSettings' ) ? $ini->group( 'ShippingSettings' ) : array(),
            'basket_info' => $ini->hasGroup( 'BasketInfoSettings' ) ? $ini->group( 'BasketInfoSettings' ) : array() ) );
    }
}
