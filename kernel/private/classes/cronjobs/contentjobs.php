<?php
/**
 * The cronjob part contentjobs (cronjobs/contentjobs.php, frequent group): starts the content jobs that need a
 * worker and have none — a running job whose worker died (kill -9, a reboot, a memory limit), a queued job
 * nobody started within content.ini [ContentJobSettings] QueuedGrace seconds (the spawn from the web request
 * failed), and a job that failed for a temporary database error (busy, locked, a deadlock) once its worker's
 * BusyRetries ran out — and removes finished jobs older than KeepDays, and the runs of the import and export
 * tools' run history older than transfer.ini [TransferSettings] KeepDays (expKernelTransferHistory::purge()).
 *
 * The worker is started detached (bin/php/expcontentjob.php run <id>), as from the web request, so a long job
 * never holds up the other cronjob parts; started as root, it becomes the site user. A job resumed again and
 * again without finishing a batch is failed by the worker after MaxAttempts starts instead of looping, and a job
 * failed for a temporary error is resumed at most MaxAttempts times in a row (the count starts again after every
 * batch done); after that it waits for a person (bin/php/expcontentjob.php list --resume).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
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
        // the run history of the import and export tools keeps transfer.ini [TransferSettings] KeepDays days
        $runs = class_exists( '\expKernelTransferHistory' ) ? \expKernelTransferHistory::purge() : 0;
        if ( !$quiet && $runs > 0 )
            $cli->output( "Transfer runs: $runs old runs removed" );
        return $started;
    }

    /**
     * Starts a worker for each job that needs one: queued or running without a worker (isStale()), or failed
     * for a temporary database error (autoResumable()).
     *
     * @param callable|null $say receives a line per job
     * @param bool $spawn false: queue them only (tests)
     * @return int the number of jobs started
     */
    public static function resumeStale( $say = null, $spawn = true )
    {
        $started = 0;
        foreach ( \expContentJob::listFor( null, array( 'queued', 'running', 'failed' ) ) as $job )
        {
            $was = $job->state();
            if ( $was === \expContentJob::STATE_FAILED )
            {
                if ( !$job->autoResumable() )
                    continue;
                $error = $job->error();
                if ( !$job->resume( false, true ) )
                    continue;
                $job->appendLog( 'the cronjob part contentjobs starts a worker (the job failed for a temporary error: ' . $error . ')' );
                $why = 'failed for a temporary error, resume ' . $job->autoResumes() . ' of ' . max( 1, (int) \expContentJob::setting( 'MaxAttempts' ) );
            }
            else
            {
                if ( !$job->isStale() )
                    continue;
                if ( !$job->resume( false, true ) )
                    continue;
                $job->appendLog( 'the cronjob part contentjobs starts a worker (the job was ' . $was . ' without one)' );
                $why = $was . ', no worker';
            }
            if ( $spawn )
                $job->spawn();
            $started++;
            if ( $say )
                call_user_func( $say, 'Content job ' . $job->id() . ' (' . $why . '): worker started' );
        }
        return $started;
    }
}

}
