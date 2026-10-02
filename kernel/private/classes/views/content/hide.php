<?php
/**
 * The code of kernel/content/hide.php, moved into a class (#207 stage 1). The file kernel/content/hide.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/content/hide.php:
 *
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Content
{

class Hide extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];
        $NodeID = $Params['NodeID'];

        $curNode = \eZContentObjectTreeNode::fetch( $NodeID );
        if ( !$curNode )
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );

        if ( !$curNode->attribute( 'can_hide' ) )
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );

        if ( \eZOperationHandler::operationIsAvailable( 'content_hide' ) )
        {
            $operationResult = \eZOperationHandler::execute( 'content',
                                                            'hide',
                                                             array( 'node_id' => $NodeID ),
                                                             null, true );
        }
        else
        {
            \eZContentOperationCollection::changeHideStatus( $NodeID );
        }


        $hasRedirect = \eZRedirectManager::redirectTo( $Module, false );
        if ( !$hasRedirect )
        {
            // redirect to the parent node
            if( ( $parentNodeID = $curNode->attribute( 'parent_node_id' ) ) == 1 )
                $redirectNodeID = $NodeID;
            else
                $redirectNodeID = $parentNodeID;
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectToView( 'view', array( 'full', $redirectNodeID ) ) );
        }

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
