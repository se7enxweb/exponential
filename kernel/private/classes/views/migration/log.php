<?php
/**
 * The migration/log view (/migration/log/<run id>): one run or one job of the administration. The run's record
 * (state, user, environment, captions, commit, tag, push, message), its migrations with their state and time, the
 * job that runs it in the background (its progress, its report and errors once it has ended) and the run log
 * (migrations/runs/<run id>.log, its end). While the job runs, page/migration_log.js follows it through
 * migration/stream and reloads the page when it has ended; without javascript a reload shows how far it is. Policy
 * migration/read.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Migration
{

class Log extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $runId = isset( $Params['RunID'] ) ? (string)$Params['RunID'] : '';
        if ( !\expMigrationRecord::validRunId( $runId ) )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );

        $run = null;
        $records = array();
        $problem = '';
        try
        {
            $db = \eZDB::instance();
            if ( \expMigrationRecord::runTableExists( $db ) === true )
            {
                $row = \expMigrationRecord::run( $db, $runId );
                if ( $row )
                    $run = \expMigrationStatus::runSummary( $row ) + array( 'log_file' => $row['log_file'],
                                                                           'resumable' => in_array( $row['state'], \expMigrationResume::RESUMABLE, true ) );
            }
            if ( \expMigrationRecord::exists( $db ) === true )
                foreach ( \expMigrationRecord::all( $db ) as $record )
                    if ( $record['run_id'] === $runId )
                        $records[] = $record;
        }
        catch ( \Throwable $e )
        {
            $problem = $e->getMessage();
        }
        $job = \expMigrationJob::get( $runId );
        if ( !$run && !$job && $problem === '' )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );

        $progress = \expMigrationProgress::fold( array() );
        $progressOffset = 0;
        if ( $job )
        {
            $read = \expMigrationProgress::read( $job['progress'], 0 );
            $progress = \expMigrationProgress::fold( $read['events'] );
            $progressOffset = $read['offset'];
        }
        $log = \expMigrationAdmin::logTail( $run || ( $job && $job['kind'] !== 'rollback' ) ? \expMigrationAdmin::runLogFile( $runId ) : '' );

        $report = $job ? $job['report'] : null;
        if ( is_array( $report ) && isset( $report['migrations'] ) )
            $report['migrations'] = \expMigrationAdmin::decorate( (array)$report['migrations'] );
        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'run_id', $runId );
        $tpl->setVariable( 'run', $run ? \expMigrationAdmin::decorate( array( $run ) )[0] : null );
        $tpl->setVariable( 'records', \expMigrationAdmin::decorate( $records ) );
        $tpl->setVariable( 'job', $job );
        $tpl->setVariable( 'job_running', $job ? (bool)$job['running'] : false );
        $tpl->setVariable( 'job_kind', $job ? (string)$job['kind'] : '' );
        // what a run printed as errors, when it failed or left no report (a run that passed may print notices there)
        $tpl->setVariable( 'job_errors', $job && $job['ended'] && ( !$job['reported'] || $job['exit'] !== 0 ) ? (string)$job['errors'] : '' );
        $tpl->setVariable( 'report', $report );
        $tpl->setVariable( 'restore_lines', $job ? \expMigrationAdmin::restoreLines( $job['report'] ) : array() );
        $tpl->setVariable( 'progress', $progress );
        $tpl->setVariable( 'meter', \expMigrationProgress::meterStep( $progress['percent'] ) );
        $tpl->setVariable( 'progress_offset', $progressOffset );
        $tpl->setVariable( 'log', $log );
        $tpl->setVariable( 'problem', $problem );
        $tpl->setVariable( 'can_run', \expMigrationAdmin::canDo( 'run' ) );
        $tpl->setVariable( 'can_rollback', \expMigrationAdmin::canDo( 'rollback' ) );
        $tpl->setVariable( 'view', 'log' );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:migration/log.tpl' );
        $Result['path'] = array( array( 'url' => 'setup/menu', 'text' => \ezpI18n::tr( 'design/admin/migration', 'Setup' ) ),
                                 array( 'url' => 'migration/status', 'text' => \ezpI18n::tr( 'design/admin/migration', 'Migrations' ) ),
                                 array( 'url' => 'migration/history', 'text' => \ezpI18n::tr( 'design/admin/migration', 'History' ) ),
                                 array( 'url' => false, 'text' => $runId ) );
        return $Result;
    }
}

}
?>
