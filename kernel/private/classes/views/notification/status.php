<?php
/**
 * File containing the notification/status view: the administrator's page about the notification system.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Notification
{

class Status extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $http = \eZHTTPTool::instance();
        $tr = function ( $text, $args = null ) {
            return \ezpI18n::tr( 'design/admin/notification/status', $text, null, $args );
        };
        $notice = false;

        // actions (a POST: the form token is checked before the view runs)
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' )
        {
            if ( $http->hasPostVariable( 'CleanupHandled' ) )
            {
                $r = \expNotificationService::cleanup();
                $notice = array( 'type' => 'success', 'text' => $tr( 'Removed %count handled events that had nothing left to send.', array( '%count' => $r['handled'] ) ) );
            }
            else if ( $http->hasPostVariable( 'CleanupOld' ) )
            {
                $age = \expNotificationService::parseAge( (string)$http->postVariable( 'OlderThan', '' ) );
                if ( $age === false || $age < 86400 )
                    $notice = array( 'type' => 'error', 'text' => $tr( 'Choose an age of at least one day.' ) );
                else
                {
                    $r = \expNotificationService::cleanup( $age );
                    $notice = array( 'type' => 'success', 'text' => $tr( 'Removed %count events older than the chosen age (%unknown of unknown age were kept).',
                                                                         array( '%count' => $r['removed'] + $r['handled'], '%unknown' => $r['unknown'] ) ) );
                }
            }
            else if ( $http->hasPostVariable( 'RemoveMissingRules' ) )
            {
                $data = \expNotificationService::subscriptions( null, array( 'missing' => true ), 0, 100000 );
                foreach ( $data['rows'] as $row )
                    \eZPersistentObject::removeObject( \eZSubtreeNotificationRule::definition(), array( 'id' => $row['id'] ) );
                $notice = array( 'type' => 'success', 'text' => $tr( 'Removed %count subscriptions whose content no longer exists.', array( '%count' => count( $data['rows'] ) ) ) );
            }
        }

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'status', \expNotificationService::status() );
        $tpl->setVariable( 'notice', $notice );
        $problems = array();
        foreach ( \expNotificationService::problems() as $p )
            $problems[] = array( 'level' => $p[0], 'text' => \expNotificationService::problemText( $p ) );
        $tpl->setVariable( 'problems', $problems );
        $missing = \expNotificationService::subscriptions( null, array( 'missing' => true ), 0, 1 );
        $tpl->setVariable( 'missing_rules', $missing['total'] );
        $recent = \expNotificationService::subscriptions( null, array(), 0, 8 );
        $tpl->setVariable( 'recent_subscriptions', $recent['rows'] );
        $tpl->setVariable( 'recent_total', $recent['total'] );
        $tpl->setVariable( 'events', \expNotificationService::eventsReport( null, 10 ) );
        $tpl->setVariable( 'job_available', \expProcessTools::error() === '' );
        $tpl->setVariable( 'job_error', \expProcessTools::error() );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:notification/status.tpl' );
        $Result['path'] = array( array( 'url' => 'notification/settings', 'text' => \ezpI18n::tr( 'kernel/notification', 'Notification settings' ) ),
                                 array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/notification', 'Notification status' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
