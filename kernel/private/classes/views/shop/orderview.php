<?php
/**
 * The code of kernel/shop/orderview.php, moved into a class (#207 stage 1). The file kernel/shop/orderview.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/shop/orderview.php:
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

class Orderview extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $OrderID = $Params['OrderID'];
        $module = $Params['Module'];


        $ini = \eZINI::instance();
        $http = \eZHTTPTool::instance();
        $user = \eZUser::currentUser();
        $access = false;
        $order = \eZOrder::fetch( $OrderID );
        if ( !$order )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
        }

        $accessToAdministrate = $user->hasAccessTo( 'shop', 'administrate' );
        $accessToAdministrateWord = $accessToAdministrate['accessWord'];

        $accessToBuy = $user->hasAccessTo( 'shop', 'buy' );
        $accessToBuyWord = $accessToBuy['accessWord'];

        if ( $accessToAdministrateWord != 'no' )
        {
            $access = true;
        }
        elseif ( $accessToBuyWord != 'no' )
        {
            if ( $user->id() == $ini->variable( 'UserSettings', 'AnonymousUserID' ) )
            {
                if( $OrderID != $http->sessionVariable( 'UserOrderID' ) )
                {
                    $access = false;
                }
                else
                {
                    $access = true;
                }
            }
            else
            {
                if ( $order->attribute( 'user_id' ) == $user->id() )
                {
                    $access = true;
                }
                else
                {
                    $access = false;
                }
            }
        }
        if ( !$access )
        {
             return $this->viewResult( isset( $Result ) ? $Result : null,  $module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );
        }
        $tpl = \eZTemplate::factory();


        $tpl->setVariable( "order", $order );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:shop/orderview.tpl" );
        $Result['path'] = array( array( 'url' => 'shop/orderlist',
                                        'text' => \ezpI18n::tr( 'kernel/shop', 'Order list' ) ),
                                 array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'kernel/shop', 'Order #%order_id', null, array( '%order_id' => $order->attribute( 'order_nr' ) ) ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
