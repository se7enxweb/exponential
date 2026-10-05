<?php
/**
 * The code of kernel/collaboration/group.php, moved into a class (#207 stage 1). The file kernel/collaboration/group.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/collaboration/group.php:
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

class Group extends \Exponential\Runnable\ModuleView
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
        $GroupID = $Params['GroupID'];

        $Offset = $Params['Offset'];
        if ( !is_numeric( $Offset ) )
            $Offset = 0;

        // only the user's own groups
        $userID = (int)\eZUser::currentUser()->attribute( 'contentobject_id' );
        $collabGroup = is_numeric( $GroupID ) ? \eZCollaborationGroup::fetch( (int)$GroupID, $userID ) : null;
        if ( $collabGroup === null )
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );

        if ( !\eZCollaborationViewHandler::groupExists( $ViewMode ) )
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );

        $http = \eZHTTPTool::instance();
        $groupURL = array( $ViewMode, $collabGroup->attribute( 'id' ) );
        if ( $http->hasPostVariable( 'CollaborationGroupCreate' ) )
            $result = \expCollaborationGroupManager::create( $userID, $http->postVariable( 'CollaborationGroupTitle', '' ), $collabGroup->attribute( 'id' ) );
        else if ( $http->hasPostVariable( 'CollaborationGroupRename' ) )
            $result = \expCollaborationGroupManager::rename( $userID, $collabGroup->attribute( 'id' ), $http->postVariable( 'CollaborationGroupTitle', '' ) );
        else if ( $http->hasPostVariable( 'CollaborationGroupDelete' ) )
        {
            $result = \expCollaborationGroupManager::delete( $userID, $collabGroup->attribute( 'id' ) );
            if ( $result['ok'] )
                $groupURL = false;
        }
        if ( isset( $result ) )
        {
            \expCollaborationGroupManager::setNotice( $result['ok'] ? 'success' : 'error', $result['text'] );
            if ( $groupURL === false )
                return $this->viewResult( null, $Module->redirectToView( 'view', array( 'summary' ) ) );
            return $this->viewResult( null, $Module->redirectToView( 'group', $groupURL ) );
        }

        $view = \eZCollaborationViewHandler::instance( $ViewMode, \eZCollaborationViewHandler::TYPE_GROUP );

        $template = $view->template();

        $collabGroupTitle = $collabGroup->attribute( 'title' );

        $viewParameters = array( 'offset' => $Offset,
                                 'status' => isset( $Params['Status'] ) && $Params['Status'] ? $Params['Status'] : 'all',
                                 'role' => isset( $Params['Role'] ) && $Params['Role'] ? $Params['Role'] : 'all',
                                 'type' => isset( $Params['Type'] ) && $Params['Type'] ? $Params['Type'] : 'all' );

        $tpl = \eZTemplate::factory();

        $tpl->setVariable( 'view_parameters', $viewParameters );
        $tpl->setVariable( 'collab_group', $collabGroup );
        $tpl->setVariable( 'notice', \expCollaborationGroupManager::takeNotice() );

        $Result = array();
        $Result['content'] = $tpl->fetch( $template );
        $Result['path'] = array( array( 'url' => 'collaboration/view/summary',
                                        'text' => \ezpI18n::tr( 'kernel/collaboration', 'Collaboration' ) ),
                                 array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'kernel/collaboration', 'Group' ) ),
                                 array( 'url' => false,
                                        'text' => $collabGroupTitle ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
