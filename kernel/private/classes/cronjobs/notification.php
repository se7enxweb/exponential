<?php
/**
 * The code of cronjobs/notification.php, moved into a class (#207 stage 1). The file cronjobs/notification.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of cronjobs/notification.php:
 *
 *
 * @description Process and dispatch pending notification events to subscribers
 *
 * File containing the notification.php cronjob
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\Cronjob\Kernel
{

class Notification extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $event = \eZNotificationEvent::create( 'ezcurrenttime', array() );

        $event->store();
        $cli->output( "Starting notification event processing" );
        \eZNotificationEventFilter::process();

        $cli->output( "Done" );
    }
}

}
