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

        $userID = (int)$user->attribute( 'contentobject_id' );
        $nodeIDList = \eZSubtreeNotificationRule::fetchNodesForUserID( $userID, false );
        $subscribed = in_array( (int)$nodeID, array_map( 'intval', $nodeIDList ), true );

        // Opening the address changes nothing: it asks. The change is made by the form it shows, a POST that carries
        // the form token (the address used to subscribe on a plain GET, so a link or an image in any page could do it).
        $post = $_SERVER['REQUEST_METHOD'] === 'POST';
        if ( $post && $http->hasPostVariable( 'CancelNotification' ) )
        {
            $module->redirectTo( $redirectURI );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        $tpl->setVariable( 'node_id', $nodeID );
        $tpl->setVariable( 'node', $contentNode );
        $tpl->setVariable( 'redirect_uri', $redirectURI );
        $tpl->setVariable( 'view_mode', $viewMode );
        $tpl->setVariable( 'already_exists', $subscribed );

        if ( $post && $http->hasPostVariable( 'ConfirmRemoveNotification' ) )
        {
            if ( $subscribed )
                \eZSubtreeNotificationRule::removeByNodeAndUserID( $userID, (int)$nodeID );
            $tpl->setVariable( 'removed', $subscribed );
            $tpl->setVariable( 'already_exists', false );
            $Result = array();
            $Result['content'] = $tpl->fetch( 'design:notification/removeresult.tpl' );
            $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/notification', 'Notification was removed.' ), 'url' => false ) );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }
        if ( $post && $http->hasPostVariable( 'ConfirmAddNotification' ) )
        {
            $alreadyExists = $subscribed;
            if ( !$subscribed )
            {
                $rule = \eZSubtreeNotificationRule::create( (int)$nodeID, $userID );
                $rule->store();
            }
            $tpl->setVariable( 'already_exists', $alreadyExists );
            $Result = array();
            $Result['content'] = $tpl->fetch( 'design:notification/addingresult.tpl' );
            $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/notification', ($alreadyExists ? 'Notification already exists.' : 'Notification was added successfully!') ),
                                            'url' => false ) );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:notification/addconfirm.tpl' );
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/notification', 'Add to my notifications' ), 'url' => false ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
