<?php
/**
 * The staticpublish/log view (/staticpublish/log/<run id>): one job started in the administration (build, publish or
 * rollback) or one run recorded in exp_static_run (a publish or rollback, also one from the console): the progress,
 * followed live while the job runs, the job's report once it has ended, and the run's record (state, counts, commit,
 * tag, push state, who ran it). Policy staticpublish/read for the job's target.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Staticpublish
{

class Log extends Page
{
    /** The most progress lines a page shows */
    const LINES = 400;

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $runId = isset( $Params['RunID'] ) ? (string)$Params['RunID'] : '';
        if ( !preg_match( '/^[A-Za-z0-9._-]{1,64}$/', $runId ) )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );
        $job = \expStaticJob::get( $runId );
        $run = null;
        try
        {
            foreach ( \expStaticStatus::runs( \eZDB::instance(), null, 0 ) as $row )
                if ( $row['run_id'] === $runId )
                    $run = $row;
        }
        catch ( \Throwable $e )
        {
        }
        $targetId = $job ? $job['target'] : ( $run ? $run['target'] : '' );
        if ( $targetId === '' || ( !$job && !$run ) )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );
        if ( !\expStaticAdmin::canDo( 'read', $targetId ) )
            return $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );
        $target = \expStaticTarget::get( $targetId );

        return self::page( 'log.tpl', array(
            'run_id' => $runId,
            'run' => $run,
            'target_id' => $targetId,
            'target_name' => $target && $target->name() !== '' ? $target->name() : $targetId,
            'view' => 'log',
        ) + self::jobVariables( $job ), self::path( self::t( 'Job %id', array( '%id' => $runId ) ), $target ) );
    }

    /**
     * What a page shows of a job: its progress so far (with the offset the stream goes on from), the lines of its
     * progress, its report once it has ended.
     *
     * @param array|null $job expStaticJob::get()
     * @return array job, job_running, progress, meter, progress_offset, lines, report, job_errors
     */
    public static function jobVariables( $job )
    {
        $out = array( 'job' => $job, 'job_running' => false, 'progress' => null, 'meter' => 0, 'progress_offset' => 0, 'lines' => array(),
                      'report' => null, 'job_errors' => '' );
        if ( !$job )
            return $out;
        $read = \expStaticProgress::read( $job['progress'], 0 );
        $out['progress'] = \expStaticProgress::fold( $read['events'], null, (int)$job['expected'] );
        $out['meter'] = \expStaticProgress::meterStep( $out['progress']['percent'] );
        $out['progress_offset'] = $read['offset'];
        foreach ( array_slice( $read['events'], -self::LINES ) as $event )
            if ( $event['event'] !== 'end' && $event['message'] !== '' )
                $out['lines'][] = $event['message'];
        $out['job_running'] = (bool)$job['running'];
        $out['report'] = $job['report'];
        $out['job_errors'] = $job['ended'] && !$job['report'] ? (string)$job['errors'] : '';
        return $out;
    }
}

}
?>
