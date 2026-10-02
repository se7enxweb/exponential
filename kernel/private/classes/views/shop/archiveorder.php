<?php
/**
 * The code of kernel/shop/archiveorder.php, moved into a class (#207 stage 1). The file kernel/shop/archiveorder.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/shop/archiveorder.php:
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

class Archiveorder extends \Exponential\Runnable\ModuleView
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
        $archiveIDArray = $http->sessionVariable( "OrderIDArray" );

        $db = \eZDB::instance();
        $db->begin();
        foreach ( $archiveIDArray as $archiveID )
        {
            \eZOrder::archiveOrder( $archiveID );
        }
        $db->commit();
        $Module->redirectTo( '/shop/orderlist/' );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
