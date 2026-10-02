<?php
/**
 * The code of kernel/shop/add.php, moved into a class (#207 stage 1). The file kernel/shop/add.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/shop/add.php:
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

class Add extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $http = \eZHTTPTool::instance();
        $basket = \eZBasket::currentBasket();
        $module = $Params['Module'];

        $quantity = (int)$module->NamedParameters["Quantity"];
        if ( !is_numeric( $quantity ) or $quantity <= 0 )
        {
            $quantity = 1;
        }
        // Verify the ObjectID input
        if ( !is_numeric( $ObjectID ) )
            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );

        // Check if the object exists on disc
        if ( !\eZContentObject::exists( $ObjectID ) )
            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );

        // Check if the user can read the object
        $object = \eZContentObject::fetch( $ObjectID );
        if ( !$object->canRead() )
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel', array( 'AccessList' => $object->accessList( 'read' ) ) ) );

        // Check if the object has a price datatype, if not it cannot be used in the basket
        $error = $basket->canAddProduct( $object );
        if ( $error !== \eZError::SHOP_OK )
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( $error, 'shop' ) );

        // The addtobasket operation declares option_list as a required parameter of
        // type array (see kernel/shop/operation_definition.php). The session variable
        // is simply unset for a product that has no options, so this used to hand the
        // handler a null: the parameter check then failed and the operation returned
        // without ever running its body, so the add silently did nothing.
        $OptionList = $http->hasSessionVariable( "AddToBasket_OptionList_" . $ObjectID )
                      ? $http->sessionVariable( "AddToBasket_OptionList_" . $ObjectID )
                      : array();
        if ( !is_array( $OptionList ) )
            $OptionList = array();

        $operationResult = \eZOperationHandler::execute( 'shop', 'addtobasket', array( 'basket_id' => $basket->attribute( 'id' ),
                                                                                      'object_id' => $ObjectID,
                                                                                      'quantity' => $quantity,
                                                                                      'option_list' => $OptionList ) );

        switch( $operationResult['status'] )
        {
            case \eZModuleOperationInfo::STATUS_HALTED:
            {
                if ( isset( $operationResult['redirect_url'] ) )
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
                    return $this->viewResult( isset( $Result ) ? $Result : null,  $Result );
               }
            }break;
            case \eZModuleOperationInfo::STATUS_CANCELLED:
            {
                if ( isset( $operationResult['reason'] ) &&  $operationResult['reason'] == 'validation' )
                {
                    $http = \eZHTTPTool::instance();
                    $http->setSessionVariable( "BasketError", $operationResult['error_data'] );
                    $module->redirectTo( $module->functionURI( \eZBasket::viewName() ) . "/(error)/options" );
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
                    return $this->viewResult( isset( $Result ) ? $Result : null,  $Result );
               }
            }break;

        }


        $ini = \eZINI::instance();
        if ( $ini->variable( 'ShopSettings', 'RedirectAfterAddToBasket' ) == 'reload' )
            $module->redirectTo( $http->sessionVariable( "FromPage" ) );
        else
            $module->redirectTo( '/shop/' . \eZBasket::viewName() . '/' );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
