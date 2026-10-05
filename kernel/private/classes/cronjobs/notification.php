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
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
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
    /**
     * One pass of the notification filter through the service (expNotificationService::run): a lock, a time
     * event, every pending event through every handler, a record of the run for the status page and
     * exp:notification:status. runcronjobs.php locks the part and writes the audit event system.cronjob.run
     * around this; the service's own lock also keeps it from running at the same time as the console command
     * or the status page's Run now.
     */
    public function run( array $scope )
    {
        $cli = isset( $scope['cli'] ) ? $scope['cli'] : \eZCLI::instance();
        $cli->output( "Starting notification event processing" );
        $result = \expNotificationService::run( array( 'source' => 'cron' ) );
        if ( $result['result'] === 'busy' )
            $cli->output( "Skipped: " . $result['error'] );
        else if ( $result['result'] !== 'ok' )
            $cli->error( "Failed: " . $result['error'] );
        else
            $cli->output( sprintf( "Done: %d events, %d messages to %d recipients", $result['events'], $result['mails'], $result['recipients'] ) );
        if ( $result['result'] === 'ok' && !empty( $result['send_failed'] ) )
            $cli->error( sprintf( "The mail transport refused %d message(s); they are kept for the next run", $result['send_failed'] ) );
        if ( $result['result'] === 'ok' && !empty( $result['dropped'] ) )
            $cli->output( sprintf( "%d message(s) were given up", $result['dropped'] ) );
        return $result['result'] === 'ok';
    }
}

}
