<?php
/**
 * The code of kernel/shop/discountgroup.php, moved into a class (#207 stage 1). The file kernel/shop/discountgroup.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/shop/discountgroup.php:
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

class Discountgroup extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $module = $Params['Module'];

        $http = \eZHTTPTool::instance();

        $discountGroupArray = \eZDiscountRule::fetchList();

        // Paged. The whole list was read and every row of it drawn.
        $pageCount  = count( $discountGroupArray );
        $pageLimit  = \expAdminPagination::limit( 'shop/discountgroup' );
        $pageOffset = \expAdminPagination::offset( $Params );
        $discountGroupArray = \expAdminPagination::page( $discountGroupArray, $pageOffset, $pageLimit );

        if ( $http->hasPostVariable( "AddDiscountGroupButton" ) )
        {
            $params = array();
            $Module->redirectTo( $Module->functionURI( "discountgroupedit" ) );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        if ( $http->hasPostVariable( "EditGroupButton" ) && $http->hasPostVariable( "EditGroupID" ) )
        {
            $Module->redirectTo( $Module->functionURI( "discountgroupedit" ) . "/" . $http->postVariable( "EditGroupID" ) );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        if ( $http->hasPostVariable( "RemoveDiscountGroupButton" ) )
        {
            $discountRuleIDList = $http->postVariable( "discountGroupIDList" );

            $db = \eZDB::instance();
            $db->begin();
            foreach ( $discountRuleIDList  as $discountRuleID )
            {
                \eZDiscountRule::removeByID( $discountRuleID );
            }
            $db->commit();

            // we changed prices of products (no discount now) => remove content caches
            \eZContentCacheManager::clearAllContentCache();

            $module->redirectTo( $module->functionURI( "discountgroup" ) . "/" );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }
        $module->setTitle( "View discount group" );
        $tpl = \eZTemplate::factory();
        $tpl->setVariable( "discountgroup_array", $discountGroupArray );
        $tpl->setVariable( "module", $module );
        $tpl->setVariable( 'discountgroup_count', $pageCount );
        $tpl->setVariable( 'limit', $pageLimit );
        $tpl->setVariable( 'view_parameters', array( 'offset' => $pageOffset ) );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:shop/discountgroup.tpl" );
        $Result['path'] = array( array( 'url' => '/shop/discountgroup/',
                                        'text' => \ezpI18n::tr( 'kernel/shop', 'Discount group' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
