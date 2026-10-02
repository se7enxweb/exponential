<?php
/**
 * The code of kernel/collaboration/action.php, moved into a class (#207 stage 1). The file kernel/collaboration/action.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/collaboration/action.php:
 *
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Collaboration
{

class Action extends \Exponential\Runnable\ModuleView
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

        if ( $Module->isCurrentAction( 'Custom' ) )
        {
            $typeIdentifier = $Module->actionParameter( 'TypeIdentifer' );
            $itemID = $Module->actionParameter( 'ItemID' );
            $collaborationItem = \eZCollaborationItem::fetch( $itemID );
            $handler = \eZCollaborationItemHandler::instantiate( $typeIdentifier );
            return $this->viewResult( isset( $Result ) ? $Result : null,  $handler->handleCustomAction( $Module, $collaborationItem ) );
        }

        $Result = array();
        $Result['content'] = false;
        $Result['path'] = array( array( 'url' => false,
                                        \ezpI18n::tr( 'kernel/collaboration', 'Collaboration custom action' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
