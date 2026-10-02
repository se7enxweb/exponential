<?php
/**
 * The code of kernel/shop/setstatus.php, moved into a class (#207 stage 1). The file kernel/shop/setstatus.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/shop/setstatus.php:
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

class Setstatus extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $module = $Params['Module'];
        $http = \eZHTTPTool::instance();
        $user = \eZUser::currentUser();

        $order = \eZOrder::fetch( $OrderID );
        if ( !$order )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
        }

        if ( $http->hasPostVariable( "OrderID" ) && $http->hasPostVariable( "StatusID" ) && $http->hasPostVariable( "SetOrderStatusButton" ) )
        {
            $access = $order->canModifyStatus( $StatusID );

            if ( $access )
            {
                if ( $order->attribute( 'status_id' ) != $StatusID )
                {
                    $order->modifyStatus( $StatusID );
                }

                if ( $http->hasPostVariable( 'RedirectURI' ) )
                {
                    $uri = $http->postVariable( 'RedirectURI' );
                    $module->redirectTo( $uri );
                    return $this->viewResult( isset( $Result ) ? $Result : null, null );
                }
                else
                {
                    $module->redirectTo( '/shop/orderview/' . $orderID );
                    return $this->viewResult( isset( $Result ) ? $Result : null, null );
                }
            }
            else
            {
                return $this->viewResult( isset( $Result ) ? $Result : null,  $module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );
            }
        }

        return $this->viewResult( isset( $Result ) ? $Result : null,  $module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
