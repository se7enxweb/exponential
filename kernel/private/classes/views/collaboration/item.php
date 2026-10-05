<?php
/**
 * The code of kernel/collaboration/item.php, moved into a class (#207 stage 1). The file kernel/collaboration/item.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/collaboration/item.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Collaboration
{

class Item extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];
        $ViewMode = $Params['ViewMode'];
        $ItemID = $Params['ItemID'];

        $Offset = $Params['Offset'];
        if ( !is_numeric( $Offset ) )
            $Offset = 0;

        /** @var eZCollaborationItem $collabItem */
        $collabItem = is_numeric( $ItemID ) ? \eZCollaborationItem::fetch( (int)$ItemID ) : null;

        // an item that does not exist is "not available", not a fatal error
        if ( !$collabItem )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
        }

        if ( !$collabItem->userIsParticipant( \eZUser::currentUser() ) )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel', array() ) );
        }

        $http = \eZHTTPTool::instance();
        if ( $http->hasPostVariable( 'CollaborationMoveItem' ) )
        {
            $result = \expCollaborationGroupManager::moveItem( \eZUser::currentUser()->attribute( 'contentobject_id' ), $collabItem->attribute( 'id' ),
                                                                (int)$http->postVariable( 'CollaborationGroupID', 0 ) );
            \expCollaborationGroupManager::setNotice( $result['ok'] ? 'success' : 'error', $result['text'] );
            return $this->viewResult( null, $Module->redirectToView( 'item', array( $ViewMode, $collabItem->attribute( 'id' ) ) ) );
        }

        $collabHandler = $collabItem->handler();
        if ( !$collabHandler )
            return $this->viewResult( null, $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
        $template = $collabHandler->template( $ViewMode );
        $row = \expCollaborationInbox::fetchRow( $collabItem->attribute( 'id' ) );
        $collabTitle = $row && $row['title'] !== '' ? $row['title'] : $collabItem->title();

        $viewParameters = array( 'offset' => $Offset );

        $tpl = \eZTemplate::factory();

        $tpl->setVariable( 'view_parameters', $viewParameters );
        $tpl->setVariable( 'collab_item', $collabItem );
        $tpl->setVariable( 'notice', \expCollaborationGroupManager::takeNotice() );

        $Result = array();
        $Result['content'] = $tpl->fetch( $template );

        $collabHandler->readItem( $collabItem );

        $Result['path'] = array( array( 'url' => 'collaboration/view/summary',
                                        'text' => \ezpI18n::tr( 'kernel/collaboration', 'Collaboration' ) ),
                                 array( 'url' => false,
                                        'text' => $collabTitle ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
