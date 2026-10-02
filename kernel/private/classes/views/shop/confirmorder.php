<?php
/**
 * The code of kernel/shop/confirmorder.php, moved into a class (#207 stage 1). The file kernel/shop/confirmorder.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/shop/confirmorder.php:
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

class Confirmorder extends \Exponential\Runnable\ModuleView
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


        $tpl = \eZTemplate::factory();
        $tpl->setVariable( "module_name", 'shop' );

        $orderID = $http->sessionVariable( 'MyTemporaryOrderID' );

        $order = \eZOrder::fetch( $orderID );
        // No order waiting for confirmation (none started, or the session no longer
        // knows it): back to the basket, rather than the kernel's "not available"
        // error page, which reads like the checkout itself had failed.
        if ( !is_object( $order ) )
        {
            $module->redirectTo( '/shop/' . \eZBasket::viewName() . '/' );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        // An order already confirmed is not confirmed again. The session keeps
        // MyTemporaryOrderID after checkout, so going back to this view -- the Back
        // button, a bookmark, a second tab -- showed the finished order as if it were
        // awaiting confirmation and ran the confirm order operation on it once more;
        // Confirm then sent it through checkout a second time. Show the order instead.
        if ( !$order->attribute( 'is_temporary' ) )
        {
            $http->removeSessionVariable( 'MyTemporaryOrderID' );
            if ( $http->hasSessionVariable( 'UserOrderID' ) && $http->sessionVariable( 'UserOrderID' ) == $order->attribute( 'id' ) )
                $module->redirectTo( \eZShopReceipt::linkURL( $order ) );
            else
                $module->redirectTo( '/shop/' . \eZBasket::viewName() . '/' );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        if ( $order instanceof \eZOrder )
        {
            if ( $http->hasPostVariable( "ConfirmOrderButton" ) )
            {
                $order->detachProductCollection();
                $ini = \eZINI::instance();
                if ( $ini->variable( 'ShopSettings', 'ClearBasketOnCheckout' ) == 'enabled' )
                {
                    $basket = \eZBasket::currentBasket();
                    $basket->remove();
                }
                $module->redirectTo( '/shop/checkout/' );
                return $this->viewResult( isset( $Result ) ? $Result : null, null );
            }

            if ( $http->hasPostVariable( "CancelButton" ) )
            {
                $order->purge( /*$removeCollection = */ false );
                $module->redirectTo( '/shop/' . \eZBasket::viewName() . '/' );
                return $this->viewResult( isset( $Result ) ? $Result : null, null );
            }

            $tpl->setVariable( "order", $order );
        }

        $basket = \eZBasket::currentBasket();
        $basket->updatePrices();

        $operationResult = \eZOperationHandler::execute( 'shop', 'confirmorder', array( 'order_id' => $order->attribute( 'id' ) ) );

        switch( $operationResult['status'] )
        {
            case \eZModuleOperationInfo::STATUS_CONTINUE:
            {
                if ( $operationResult != null &&
                     !isset( $operationResult['result'] ) &&
                     ( !isset( $operationResult['redirect_url'] ) || $operationResult['redirect_url'] == null ) )
                {
                    $order = \eZOrder::fetch( $order->attribute( 'id' ) );
                    $tpl->setVariable( "order", $order );

                    $Result = array();
                    $Result['content'] = $tpl->fetch( "design:shop/confirmorder.tpl" );
                    $Result['path'] = array( array( 'url' => false,
                                                    'text' => \ezpI18n::tr( 'kernel/shop', 'Confirm order' ) ) );
                }
            }break;

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
                    {
                        $resultContent = $result;
                    }
                    $Result['content'] = $resultContent;
                }
            }break;
            case \eZModuleOperationInfo::STATUS_CANCELLED:
            {
                $Result = array();
                if ( isset( $operationResult['result']['content'] ) )
                    $Result['content'] = $operationResult['result']['content'];
                else
                    $Result['content'] = \ezpI18n::tr( 'kernel/shop', "The confirm order operation was canceled. Try to checkout again." );

                $Result['path'] = array( array( 'url' => false,
                                                'text' => \ezpI18n::tr( 'kernel/shop', 'Confirm order' ) ) );
            }

        }

        /*
        $Result = array();
        $Result['content'] = $tpl->fetch( "design:shop/confirmorder.tpl" );
        $Result['path'] = array( array( 'url' => false,
                                        'text' => ezpI18n::tr( 'kernel/shop', 'Confirm order' ) ) );
        */

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
