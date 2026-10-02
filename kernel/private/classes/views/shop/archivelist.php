<?php
/**
 * The code of kernel/shop/archivelist.php, moved into a class (#207 stage 1). The file kernel/shop/archivelist.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/shop/archivelist.php:
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

class Archivelist extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $module = $Params['Module'];

        $tpl = \eZTemplate::factory();

        $offset = $Params['Offset'];
        $limit = \expAdminPagination::limit( 'shop/archivelist' );


        if( \eZPreferences::value( 'admin_archivelist_sortfield' ) )
        {
            $sortField = \eZPreferences::value( 'admin_archivelist_sortfield' );
        }

        if ( !isset( $sortField ) || ( ( $sortField != 'created' ) && ( $sortField!= 'user_name' ) ) )
        {
            $sortField = 'created';
        }

        if( \eZPreferences::value( 'admin_archivelist_sortorder' ) )
        {
            $sortOrder = \eZPreferences::value( 'admin_archivelist_sortorder' );
        }

        if ( !isset( $sortOrder ) || ( ( $sortOrder != 'asc' ) && ( $sortOrder!= 'desc' ) ) )
        {
            $sortOrder = 'asc';
        }

        $http = \eZHTTPTool::instance();

        // Unarchive options.
        if ( $http->hasPostVariable( 'UnarchiveButton' ) )
        {
            if ( $http->hasPostVariable( 'OrderIDArray' ) )
            {
                $orderIDArray = $http->postVariable( 'OrderIDArray' );
                if ( $orderIDArray !== null )
                {
                    $http->setSessionVariable( 'OrderIDArray', $orderIDArray );
                    $Module->redirectTo( $Module->functionURI( 'unarchiveorder' ) . '/' );
                }
            }
        }

        $archiveArray = \eZOrder::active( true, $offset, $limit, $sortField, $sortOrder, \eZOrder::SHOW_ARCHIVED );
        $archiveCount = \eZOrder::activeCount( \eZOrder::SHOW_ARCHIVED );

        $tpl->setVariable( 'archive_list', $archiveArray );
        $tpl->setVariable( 'archive_list_count', $archiveCount );
        $tpl->setVariable( 'limit', $limit );

        $viewParameters = array( 'offset' => $offset );
        $tpl->setVariable( 'view_parameters', $viewParameters );
        $tpl->setVariable( 'sort_field', $sortField );
        $tpl->setVariable( 'sort_order', $sortOrder );

        $Result = array();
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/shop', 'Order list' ),
                                        'url' => false ) );

        $Result['content'] = $tpl->fetch( 'design:shop/archivelist.tpl' );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
