<?php
/**
 * The code of kernel/shop/orderreciept.php, moved into a class (#207 stage 1). The file kernel/shop/orderreciept.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/shop/orderreciept.php:
 *
 *
 * shop/orderreciept/<token>: the common misspelling of shop/orderreceipt,
 * answered with a permanent redirect so either address keeps working.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Shop
{

class Orderreciept extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $module = $Params['Module'];
        $token = isset( $Params['Token'] ) ? (string)$Params['Token'] : '';
        // redirectTo() carries the query string (?download=1) across by itself.
        $module->redirectTo( '/shop/orderreceipt/' . rawurlencode( $token ) );
        $module->setRedirectStatus( '301 Moved Permanently' );
        return $this->viewResult( isset( $Result ) ? $Result : null, null );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
