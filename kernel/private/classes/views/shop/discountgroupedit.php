<?php
/**
 * The code of kernel/shop/discountgroupedit.php, moved into a class (#207 stage 1). The file kernel/shop/discountgroupedit.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/shop/discountgroupedit.php:
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

class Discountgroupedit extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $module = $Params['Module'];
        $discountGroupID = null;
        if ( isset( $Params["DiscountGroupID"] ) )
            $discountGroupID = $Params["DiscountGroupID"];

        if ( is_numeric( $discountGroupID ) )
        {
            $discountGroup = \eZDiscountRule::fetch( $discountGroupID );
        }
        else
        {
            $discountGroup = \eZDiscountRule::create();
            $discountGroupID = $discountGroup->attribute( "id" );
        }

        $http = \eZHTTPTool::instance();
        if ( $http->hasPostVariable( "DiscardButton" ) )
        {
            $module->redirectTo( $module->functionURI( "discountgroup" ) . "/" );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }
        if ( $http->hasPostVariable( "ApplyButton" ) )
        {
            if ( $http->hasPostVariable( "discount_group_name" ) )
            {
                $name = $http->postVariable( "discount_group_name" );
            }
            $discountGroup->setAttribute( "name", $name );
            $discountGroup->store();
            $module->redirectTo( $module->functionURI( "discountgroup" ) . "/" );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        $module->setTitle( "Editing discount group" );
        $tpl = \eZTemplate::factory();
        $tpl->setVariable( "module", $module );
        $tpl->setVariable( "discount_group", $discountGroup );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:shop/discountgroupedit.tpl" );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
