<?php
/**
 * The code of kernel/notification/addtonotification.php, moved into a class (#207 stage 1). The file kernel/notification/addtonotification.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/notification/addtonotification.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Notification
{

class Addtonotification extends \Exponential\Runnable\ModuleView
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

        //$Offset = $Params['Offset'];
        //$viewParameters = array( 'offset' => $Offset );

        //$nodeID = $http->postVariable( 'ContentNodeID' );
        $nodeID = $Params['ContentNodeID'];
        $user = \eZUser::currentUser();

        $redirectURI = $http->postVariable( 'RedirectURI', $http->sessionVariable( 'LastAccessesURI', '/' ) );

        $viewMode = $http->hasPostVariable( 'ViewMode' ) ? $http->postVariable( 'ViewMode' ) : 'full';

        if ( !$user->isRegistered() )
        {
            \eZDebug::writeError( 'User not logged in trying to subscribe for notification, node ID: ' . $nodeID,
                                 'kernel/content/action.php' );
            $module->redirectTo( $redirectURI );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        $contentNode = \eZContentObjectTreeNode::fetch( $nodeID );
        if ( !$contentNode )
        {
            \eZDebug::writeError( 'The nodeID parameter was empty, user ID: ' . $user->attribute( 'contentobject_id' ),
                                 'kernel/content/action.php' );
            $module->redirectTo( $redirectURI );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }
        if ( !$contentNode->attribute( 'can_read' ) )
        {
            \eZDebug::writeError( 'User does not have access to subscribe for notification, node ID: ' . $nodeID . ', user ID: ' . $user->attribute( 'contentobject_id' ),
                                 'kernel/content/action.php' );
            $module->redirectTo( $redirectURI );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        $tpl = \eZTemplate::factory();
        if ( $http->hasSessionVariable( "LastAccessesURI", false ) )
            $tpl->setVariable( 'redirect_url', $http->sessionVariable( "LastAccessesURI" ) );
        //else
        //    $tpl->setVariable( 'redirect_url', $module->functionURI( 'view' ) . '/full/2' );

        $alreadyExists = true;

        $nodeIDList = \eZSubtreeNotificationRule::fetchNodesForUserID( $user->attribute( 'contentobject_id' ), false );
        if ( !in_array( $nodeID, $nodeIDList ) )
        {
            $rule = \eZSubtreeNotificationRule::create( $nodeID, $user->attribute( 'contentobject_id' ) );
            $rule->store();
            $alreadyExists = false;
        }
        $tpl->setVariable( 'already_exists', $alreadyExists );
        $tpl->setVariable( 'node_id', $nodeID );


        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:notification/addingresult.tpl' );
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/notification', ($alreadyExists ? 'Notification already exists.' : 'Notification was added successfully!') ),
                                        'url' => false ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
