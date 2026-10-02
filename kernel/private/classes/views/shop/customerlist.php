<?php
/**
 * The code of kernel/shop/customerlist.php, moved into a class (#207 stage 1). The file kernel/shop/customerlist.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/shop/customerlist.php:
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

class Customerlist extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $module = $Params["Module"];

        $offset = $Params['Offset'];
        $limit = \expAdminPagination::limit( 'shop/customerlist' );

        $tpl = \eZTemplate::factory();

        $http = \eZHTTPTool::instance();

        $customerArray = \eZOrder::customerList( $offset, $limit );

        $customerCount = \eZOrder::customerCount();

        $tpl->setVariable( "customer_list", $customerArray );
        $tpl->setVariable( "customer_list_count", $customerCount );
        $tpl->setVariable( "limit", $limit );

        $viewParameters = array( 'offset' => $offset );
        $tpl->setVariable( "module", $module );
        $tpl->setVariable( 'view_parameters', $viewParameters );

        $path = array();
        $path[] = array( 'text' => \ezpI18n::tr( 'kernel/shop', 'Customer list' ),
                         'url' => false );

        $Result = array();
        $Result['path'] = $path;

        $Result['content'] = $tpl->fetch( "design:shop/customerlist.tpl" );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
