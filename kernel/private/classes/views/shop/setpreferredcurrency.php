<?php
/**
 * The code of kernel/shop/setpreferredcurrency.php, moved into a class (#207 stage 1). The file kernel/shop/setpreferredcurrency.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/shop/setpreferredcurrency.php:
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

class Setpreferredcurrency extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $module = $Params['Module'];

        $preferredCurrency = $Params['Currency'];

        if ( $module->isCurrentAction( 'Set' ) )
        {
            if ( $module->hasActionParameter( 'Currency' ) )
                $preferredCurrency = $module->actionParameter( 'Currency' );
        }

        if ( $preferredCurrency )
            \eZShopFunctions::setPreferredCurrencyCode( $preferredCurrency );

        \eZRedirectManager::redirectTo( $module, false );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
