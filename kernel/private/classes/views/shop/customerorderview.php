<?php
/**
 * The code of kernel/shop/customerorderview.php, moved into a class (#207 stage 1). The file kernel/shop/customerorderview.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/shop/customerorderview.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Shop
{

class Customerorderview extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $CustomerID = $Params['CustomerID'];
        $Email = $Params['Email'];
        $module = $Params['Module'];


        $http = \eZHTTPTool::instance();

        $tpl = \eZTemplate::factory();

        $Email = urldecode( $Email );
        $productList = \eZOrder::productList( $CustomerID, $Email );
        $orderList = \eZOrder::orderList( $CustomerID, $Email );

        $tpl->setVariable( "product_list", $productList );

        $tpl->setVariable( "order_list", $orderList );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:shop/customerorderview.tpl" );
        $path = array();
        $path[] = array( 'url' => '/shop/orderlist',
                         'text' => \ezpI18n::tr( 'kernel/shop', 'Order list' ) );
        $path[] = array( 'url' => false,
                         'text' => \ezpI18n::tr( 'kernel/shop', 'Customer order view' ) );
        $Result['path'] = $path;

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
