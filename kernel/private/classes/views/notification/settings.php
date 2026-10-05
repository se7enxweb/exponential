<?php
/**
 * The code of kernel/notification/settings.php, moved into a class (#207 stage 1). The file kernel/notification/settings.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/notification/settings.php:
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

class Settings extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $http = \eZHTTPTool::instance();

        $Module = $Params['Module'];

        $user = \eZUser::currentUser();
        $userID = (int)$user->attribute( 'contentobject_id' );
        $tr = function ( $text, $args = null ) {
            return \ezpI18n::tr( 'design/admin/notification/settings', $text, null, $args );
        };

        $availableHandlers = \eZNotificationEventFilter::availableHandlers();
        $subtreeID = \eZSubTreeHandler::NOTIFICATION_HANDLER_ID;
        $notice = false;
        $confirmRemove = false;

        // the filter of the list: a form posts it, the address keeps it (/notification/settings/(q)/text/(class)/folder)
        $query = isset( $Params['Query'] ) ? trim( (string)$Params['Query'] ) : '';
        $classFilter = isset( $Params['ClassFilter'] ) ? trim( (string)$Params['ClassFilter'] ) : '';
        if ( $http->hasPostVariable( 'FilterSubscriptions' ) || $http->hasPostVariable( 'ClearFilter' ) )
        {
            $uri = '/notification/settings';
            $q = $http->hasPostVariable( 'ClearFilter' ) ? '' : trim( (string)$http->postVariable( 'Query', '' ) );
            $c = $http->hasPostVariable( 'ClearFilter' ) ? '' : trim( (string)$http->postVariable( 'ClassFilter', '' ) );
            if ( $q !== '' )
                $uri .= '/(q)/' . rawurlencode( $q );
            if ( $c !== '' )
                $uri .= '/(class)/' . rawurlencode( $c );
            $Module->redirectTo( $uri );
            return $this->viewResult( null, null );
        }

        $rulesBefore = \eZSubtreeNotificationRule::fetchListCount( $userID );
        $cancelled = $http->hasPostVariable( 'CancelRemoveRule' );
        $post = $http->hasPostVariable( 'RemoveRule_' . $subtreeID ) && $http->hasPostVariable( 'UseConfirm' ) && !$cancelled;
        $removeConfirmed = $http->hasPostVariable( 'ConfirmRemoveRule' );
        if ( $post && !$removeConfirmed )
        {
            // the form asks first: the rules that were ticked, only the user's own, are shown again with the question
            $ids = array_map( 'intval', (array)$http->postVariable( 'SelectedRuleIDArray_' . $subtreeID, array() ) );
            $own = array();
            foreach ( \eZSubtreeNotificationRule::fetchList( $userID, false ) as $row )
                $own[(int)$row['id']] = true;
            $ids = array_values( array_filter( $ids, function ( $id ) use ( $own ) { return isset( $own[$id] ); } ) );
            if ( !$ids )
                $notice = array( 'type' => 'warning', 'text' => $tr( 'Tick at least one item to remove.' ) );
            else
                $confirmRemove = $this->rulesByID( $userID, $ids );
        }

        $db = \eZDB::instance();
        $db->begin();
        $stored = false;
        if ( $http->hasPostVariable( 'Store' ) )
        {
            foreach ( $availableHandlers as $handler )
            {
                $handler->storeSettings( $http, $Module );
            }
            $stored = true;
        }

        foreach ( $availableHandlers as $key => $handler )
        {
            // the confirmation step has not been answered: the subtree handler must not remove yet
            if ( $key === $subtreeID && $post && !$removeConfirmed )
                continue;
            if ( $cancelled && $key === $subtreeID )
                continue;
            $handler->fetchHttpInput( $http, $Module );
        }
        $db->commit();

        $rulesAfter = \eZSubtreeNotificationRule::fetchListCount( $userID );
        if ( $notice === false )
        {
            if ( $stored )
                $notice = array( 'type' => 'success', 'text' => $tr( 'Your notification settings were saved.' ) );
            else if ( $http->hasPostVariable( 'CollaborationHandlerSelection' ) )
                $notice = array( 'type' => 'success', 'text' => $tr( 'Your collaboration notifications were saved.' ) );
            else if ( $removeConfirmed && $rulesAfter < $rulesBefore )
                $notice = array( 'type' => 'success', 'text' => $tr( 'Removed %count notification(s).', array( '%count' => $rulesBefore - $rulesAfter ) ) );
            else if ( $http->hasPostVariable( 'BrowseActionName' ) && !$http->hasPostVariable( 'BrowseCancelButton' ) )
                $notice = $rulesAfter > $rulesBefore
                    ? array( 'type' => 'success', 'text' => $tr( 'Added %count notification(s).', array( '%count' => $rulesAfter - $rulesBefore ) ) )
                    : array( 'type' => 'warning', 'text' => $tr( 'Nothing was added: you already follow those items, or you may not read them.' ) );
        }

        // the paging: the preference of the other admin lists (10, 25, 50)
        $perPage = array( 1 => 10, 2 => 25, 3 => 50 );
        $pref = (int)\eZPreferences::value( 'admin_list_limit' );
        $limit = isset( $perPage[$pref] ) ? $perPage[$pref] : 10;
        $offset = max( 0, (int)$Params['Offset'] );
        $list = \expNotificationService::subscriptions( $userID, array( 'q' => $query, 'class' => $classFilter ), $offset, $limit );
        if ( !$list['rows'] && $offset > 0 && $list['total'] > 0 )
        {
            // the last page was emptied by a removal
            $offset = max( 0, (int)( ( $list['total'] - 1 ) / $limit ) * $limit );
            $list = \expNotificationService::subscriptions( $userID, array( 'q' => $query, 'class' => $classFilter ), $offset, $limit );
        }

        $viewParameters = array( 'offset' => $offset, 'q' => $query, 'class' => $classFilter );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'user', $user );
        $tpl->setVariable( 'view_parameters', $viewParameters );
        $tpl->setVariable( 'notice', $notice );
        $tpl->setVariable( 'confirm_remove', $confirmRemove );
        $tpl->setVariable( 'subscriptions', $list['rows'] );
        $tpl->setVariable( 'subscription_total', $list['total'] );
        $tpl->setVariable( 'subscription_all_total', \eZSubtreeNotificationRule::fetchListCount( $userID ) );
        $tpl->setVariable( 'subscription_limit', $limit );
        $tpl->setVariable( 'subscription_classes', \expNotificationService::subscribedClasses( $userID ) );
        $tpl->setVariable( 'filter_query', $query );
        $tpl->setVariable( 'filter_class', $classFilter );
        $access = $user->hasAccessTo( 'notification', 'administrate' );
        $tpl->setVariable( 'can_administrate', $access['accessWord'] !== 'no' );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:notification/settings.tpl' );
        $Result['path'] = array( array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'kernel/notification', 'Notification settings' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
    /**
     * The user's own rules with the id in $ids, as the list shows them.
     */
    private function rulesByID( $userID, array $ids )
    {
        $found = \expNotificationService::subscriptions( $userID, array( 'ids' => $ids ), 0, max( 1, count( $ids ) ) );
        return $found['rows'];
    }
}

}
