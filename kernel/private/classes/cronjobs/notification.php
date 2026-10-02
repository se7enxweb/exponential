<?php
/**
 * The code of cronjobs/notification.php, moved into a class (#207 stage 1). The file cronjobs/notification.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
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
