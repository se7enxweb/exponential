<?php
/**
 * The code of kernel/notification/runfilter.php, moved into a class (#207 stage 1). The file kernel/notification/runfilter.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/notification/runfilter.php:
 *
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Notification
{

class Runfilter extends \Exponential\Runnable\ModuleView
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

        $tpl = \eZTemplate::factory();

        $tpl->setVariable( 'filter_proccessed', false );
        $tpl->setVariable( 'time_event_created', false );

        if ( $http->hasPostVariable( 'RunFilterButton' ) )
        {
            \eZNotificationEventFilter::process();
            $tpl->setVariable( 'filter_proccessed', true );

        }
        else if ( $http->hasPostVariable( 'SpawnTimeEventButton' ) )
        {
            $event = \eZNotificationEvent::create( 'ezcurrenttime', array() );
            $event->store();
            $tpl->setVariable( 'time_event_created', true );

        }

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:notification/runfilter.tpl' );
        $Result['path'] = array( array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'kernel/notification', 'Notification settings' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
