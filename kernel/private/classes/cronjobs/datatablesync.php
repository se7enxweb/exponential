<?php
/**
 * The cronjob part datatablesync (cronjobs/datatablesync.php, frequent group; doc/eng-us/features/6.0/data-tables.md,
 * "Synchronisation"): starts each enabled scheduled or live data table synchronisation whose next run is due, through
 * the transfer runner (expqueue, content jobs, or this process), and moves its next run by its interval. Does nothing
 * on an installation without the sync tables.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Cronjob\Kernel
{

class Datatablesync extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        $cli = isset( $scope['cli'] ) ? $scope['cli'] : \eZCLI::instance();
        $quiet = !empty( $scope['isQuiet'] );
        if ( !class_exists( '\expDataSync' ) || !\expDataSync::installed() )
            return 0;
        $started = 0;
        foreach ( \expDataSync::due() as $sync )
        {
            // the next run moves first: a run that takes longer than the interval is not started twice
            $sync->setAttribute( 'next_run', time() + max( 60, (int)$sync->attribute( 'interval_seconds' ) ) );
            $sync->store();
            $run = \expDataSync::start( $sync );
            $started++;
            if ( !$quiet )
                $cli->output( 'Data table synchronisation "' . $sync->attribute( 'name' ) . '": ' . $run['engine']
                              . ( !empty( $run['run_id'] ) ? ', run ' . $run['run_id'] : '' ) . ( !empty( $run['error'] ) ? ', ' . $run['error'] : '' ) );
        }
        return $started;
    }
}

}
?>
