<?php
/**
 * The code of kernel/collaboration/view.php, moved into a class (#207 stage 1). The file kernel/collaboration/view.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/collaboration/view.php:
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

class View extends \Exponential\Runnable\ModuleView
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

        $Offset = $Params['Offset'];
        if ( !is_numeric( $Offset ) )
            $Offset = 0;

        if ( !\eZCollaborationViewHandler::exists( $ViewMode ) )
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );

        $view = \eZCollaborationViewHandler::instance( $ViewMode );

        $template = $view->template();

        $http = \eZHTTPTool::instance();
        $userID = (int)\eZUser::currentUser()->attribute( 'contentobject_id' );

        // group management on the summary: a new group (the form token of the form is checked by the form token filter)
        if ( $http->hasPostVariable( 'CollaborationGroupCreate' ) )
        {
            $result = \expCollaborationGroupManager::create( $userID, $http->postVariable( 'CollaborationGroupTitle', '' ),
                                                              (int)$http->postVariable( 'CollaborationGroupParent', 0 ) );
            \expCollaborationGroupManager::setNotice( $result['ok'] ? 'success' : 'error', $result['text'] );
            return $this->viewResult( null, $Module->redirectToView( 'view', array( $ViewMode ) ) );
        }

        // the filters of the inbox: /collaboration/view/summary/(status)/waiting/(role)/approver/(type)/ezapprove/(group)/3/(offset)/10
        $viewParameters = array( 'offset' => $Offset,
                                 'status' => isset( $Params['Status'] ) && $Params['Status'] ? $Params['Status'] : 'all',
                                 'role' => isset( $Params['Role'] ) && $Params['Role'] ? $Params['Role'] : 'all',
                                 'type' => isset( $Params['Type'] ) && $Params['Type'] ? $Params['Type'] : 'all',
                                 'group' => isset( $Params['Group'] ) ? (int)$Params['Group'] : 0 );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'view_parameters', $viewParameters );
        $tpl->setVariable( 'notice', \expCollaborationGroupManager::takeNotice() );

        $Result = array();
        $Result['content'] = $tpl->fetch( $template );
        $Result['path'] = array( array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'kernel/collaboration', 'Collaboration' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
