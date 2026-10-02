<?php
/**
 * The cronjob part contentjobs (cronjobs/contentjobs.php, frequent group): starts the content jobs that need a
 * worker and have none — a running job whose worker died (kill -9, a reboot, a memory limit) and a queued job
 * nobody started within content.ini [ContentJobSettings] QueuedGrace seconds (the spawn from the web request
 * failed) — and removes finished jobs older than KeepDays.
 *
 * The worker is started detached (bin/php/expcontentjob.php run <id>), as from the web request, so a long job
 * never holds up the other cronjob parts; started as root, it becomes the site user. A job resumed again and
 * again without finishing a batch is failed by the worker after MaxAttempts starts instead of looping.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Cronjob\Kernel
{

class Contentjobs extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        $cli = isset( $scope['cli'] ) ? $scope['cli'] : \eZCLI::instance();
        $quiet = !empty( $scope['isQuiet'] );
        $started = self::resumeStale( function ( $line ) use ( $cli, $quiet ) {
            if ( !$quiet )
                $cli->output( $line );
        } );
        $purged = \expContentJob::purgeFinished();
        if ( !$quiet && ( $started || $purged ) )
            $cli->output( "Content jobs: $started started, $purged old jobs removed" );
        return $started;
    }

    /**
     * Starts a worker for each job that needs one.
     *
     * @param callable|null $say receives a line per job
     * @param bool $spawn false: queue them only (tests)
     * @return int the number of jobs started
     */
    public static function resumeStale( $say = null, $spawn = true )
    {
        $started = 0;
        foreach ( \expContentJob::listFor( null, array( 'queued', 'running' ) ) as $job )
        {
            if ( !$job->isStale() )
                continue;
            $was = $job->state();
            if ( !$job->resume( false ) )
                continue;
            $job->appendLog( 'the cronjob part contentjobs starts a worker (the job was ' . $was . ' without one)' );
            if ( $spawn )
                $job->spawn();
            $started++;
            if ( $say )
                call_user_func( $say, 'Content job ' . $job->id() . ' (' . $was . ', no worker): worker started' );
        }
        return $started;
    }
}

}
