<?php
/**
 * The code of kernel/shop/checkout.php, moved into a class (#207 stage 1). The file kernel/shop/checkout.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/shop/checkout.php:
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

class Checkout extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $http = \eZHTTPTool::instance();
        $module = $Params['Module'];

        $orderID = $http->sessionVariable( 'MyTemporaryOrderID' );
        $order = \eZOrder::fetch( $orderID );

        if ( $order instanceof \eZOrder )
        {
            if (  $order->attribute( 'is_temporary' ) )
            {
                $paymentObj = \eZPaymentObject::fetchByOrderID( $orderID );
                if ( $paymentObj != null )
                {
                    $startTime = time();
                    while( ( time() - $startTime ) < 25 )
                    {
                        \eZDebug::writeDebug( "next iteration", "checkout" );
                        $order = \eZOrder::fetch( $orderID );
                        if ( $order->attribute( 'is_temporary' ) == 0 )
                        {
                            break;
                        }
                        else
                        {
                            sleep ( 2 );
                        }
                    }
                }
                $order = \eZOrder::fetch( $orderID );
                if (  $order->attribute( 'is_temporary' ) == 1 && $paymentObj == null  )
                {
                    $email = $order->accountEmail();
                    $order->setAttribute( 'email', $email );
                    $order->store();

                    $http->setSessionVariable( "UserOrderID", $order->attribute( 'id' ) );

                    $operationResult = \eZOperationHandler::execute( 'shop', 'checkout', array( 'order_id' => $order->attribute( 'id' ) ) );
                    switch( $operationResult['status'] )
                    {
                        case \eZModuleOperationInfo::STATUS_HALTED:
                        case \eZModuleOperationInfo::STATUS_REPEAT:
                        {
                            if (  isset( $operationResult['redirect_url'] ) )
                            {
                                $module->redirectTo( $operationResult['redirect_url'] );
                                return $this->viewResult( isset( $Result ) ? $Result : null, null );
                            }
                            else if ( isset( $operationResult['result'] ) )
                            {
                                $result = $operationResult['result'];
                                $resultContent = false;
                                if ( is_array( $result ) )
                                {
                                    if ( isset( $result['content'] ) )
                                    {
                                        $resultContent = $result['content'];
                                    }
                                    if ( isset( $result['path'] ) )
                                    {
                                        $Result['path'] = $result['path'];
                                    }
                                }
                                else
                                    $resultContent = $result;
                                $Result['content'] = $resultContent;
                                return $this->viewResult( isset( $Result ) ? $Result : null, null );
                            }
                        }break;
                        case \eZModuleOperationInfo::STATUS_CANCELLED:
                        {
                            $Result = array();

                            $tpl = \eZTemplate::factory();
                            $tpl->setVariable( 'operation_result', $operationResult );

                            $Result['content'] = $tpl->fetch( "design:shop/cancelcheckout.tpl" ) ;
                            $Result['path'] = array( array( 'url' => false,
                                                            'text' => \ezpI18n::tr( 'kernel/shop', 'Checkout' ) ) );

                            return $this->viewResult( isset( $Result ) ? $Result : null, null );
                        }

                    }
                }
                else
                {
                    if ( $order->attribute( 'is_temporary' ) == 0 )
                    {
                        $http->removeSessionVariable( "CheckoutAttempt" );
                        $module->redirectTo( \eZShopReceipt::linkURLForID( $orderID ) );
                        return $this->viewResult( isset( $Result ) ? $Result : null, null );
                    }
                    else
                    {
                        // Get the attempt number and the order.
                        $attempt = ($http->hasSessionVariable("CheckoutAttempt") ? $http->sessionVariable("CheckoutAttempt") : 0 );
                        $attemptOrderID = ($http->hasSessionVariable("CheckoutAttemptOrderID") ? $http->sessionVariable("CheckoutAttemptOrderID") : 0 );

                        // This attempt is for another order. So reset the attempt.
                        if ($attempt != 0 && $attemptOrderID != $orderID) $attempt = 0;

                        $http->setSessionVariable("CheckoutAttempt", ++$attempt);
                        $http->setSessionVariable("CheckoutAttemptOrderID", $orderID);

                        if ( $attempt < 4)
                        {
                            $Result = array();

                            $tpl = \eZTemplate::factory();
                            $tpl->setVariable( 'attempt', $attempt );
                            $tpl->setVariable( 'orderID', $orderID );
                            $Result['content'] = $tpl->fetch( "design:shop/checkoutagain.tpl" ) ;
                            $Result['path'] = array( array( 'url' => false,
                                                            'text' => \ezpI18n::tr( 'kernel/shop', 'Checkout' ) ) );
                            return $this->viewResult( isset( $Result ) ? $Result : null, null );
                        }
                        else
                        {
                            // Got no receipt or callback from the payment server.
                            $http->removeSessionVariable( "CheckoutAttempt" );

                            $Result = array();

                            $tpl = \eZTemplate::factory();
                            $tpl->setVariable ("ErrorCode", "NO_CALLBACK");
                            $tpl->setVariable ("OrderID", $orderID);

                            $Result['content'] = $tpl->fetch( "design:shop/cancelcheckout.tpl" ) ;
                            $Result['path'] = array( array( 'url' => false,
                                                            'text' => \ezpI18n::tr( 'kernel/shop', 'Checkout' ) ) );
                            return $this->viewResult( isset( $Result ) ? $Result : null, null );
                        }
                    }
                }
           }
           $module->redirectTo( \eZShopReceipt::linkURLForID( $orderID ) );
           return $this->viewResult( isset( $Result ) ? $Result : null, null );

        }

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
