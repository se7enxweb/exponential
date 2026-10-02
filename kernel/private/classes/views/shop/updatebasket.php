<?php
/**
 * The code of kernel/shop/updatebasket.php, moved into a class (#207 stage 1). The file kernel/shop/updatebasket.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/shop/updatebasket.php:
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

class Updatebasket extends \Exponential\Runnable\ModuleView
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

        $itemCountList = $http->sessionVariable( 'ProductItemCountList' );
        $itemIDList = $http->sessionVariable( 'ProductItemIDList' );

        $operationResult = \eZOperationHandler::execute( 'shop', 'updatebasket', array( 'item_count_list' => $itemCountList,
                                                                                       'item_id_list' => $itemIDList ) );

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
        }

        $module->redirectTo( '/shop/' . \eZBasket::viewName() . '/' );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
