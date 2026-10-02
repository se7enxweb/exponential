<?php
/**
 * The code of kernel/shop/setusercountry.php, moved into a class (#207 stage 1). The file kernel/shop/setusercountry.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/shop/setusercountry.php:
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

class Setusercountry extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $module = $Params['Module'];

        if ( $module->isCurrentAction( 'Set' ) && $module->hasActionParameter( 'Country' ) )
        {
            $country = $module->actionParameter( 'Country' );
        }
        elseif ( isset( $Params['Country'] ) )
        {
            $country = $Params['Country'];
        }
        else
        {
            $country = null;
        }

        if ( $country !== null )
        {
            \eZShopFunctions::setPreferredUserCountry( $country );
            \eZDebug::writeNotice( "Set user country to <$country>" );
        }
        else
        {
            \eZDebug::writeWarning( "No country chosen to set." );
        }

        \eZRedirectManager::redirectTo( $module, false );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
