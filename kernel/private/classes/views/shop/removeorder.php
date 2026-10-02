<?php
/**
 * The code of kernel/shop/removeorder.php, moved into a class (#207 stage 1). The file kernel/shop/removeorder.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/shop/removeorder.php:
 *
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Shop
{

class Removeorder extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];
        $http = \eZHTTPTool::instance();
        $deleteIDArray = $http->sessionVariable( "DeleteOrderIDArray" );

        if ( $http->hasPostVariable( "ConfirmButton" ) )
        {
            $db = \eZDB::instance();
            $db->begin();
            foreach ( $deleteIDArray as $deleteID )
            {
                \eZOrder::cleanupOrder( $deleteID );
            }
            $db->commit();
            $Module->redirectTo( '/shop/orderlist/' );
        }
        elseif ( $http->hasPostVariable( "CancelButton" ) )
        {
            $Module->redirectTo( '/shop/orderlist/' );
        }
        else // no action yet: just displaying the template
        {
            $orderNumbersArray = array();
            foreach ( $deleteIDArray as $orderID )
            {
                $order = \eZOrder::fetch( $orderID );
                if ( $order === null )
                    continue;   // just to prevent possible fatal error below

                $orderNumbersArray[] = $order->attribute( 'order_nr' );
            }
            $orderNumbersString = implode( ', ', $orderNumbersArray );

            $Module->setTitle( \ezpI18n::tr( 'shop', 'Remove orders' ) );

            $tpl = \eZTemplate::factory();
            $tpl->setVariable( "module", $Module );
            $tpl->setVariable( "delete_result", $orderNumbersString );
            $Result = array();

            $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/shop', 'Remove order' ),
                                            'url' => false ) );
            $Result['content'] = $tpl->fetch( "design:shop/removeorder.tpl" );
        }

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
