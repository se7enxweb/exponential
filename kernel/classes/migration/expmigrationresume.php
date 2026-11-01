<?php
/**
 * File containing the expMigrationResume class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Resumes a migration run that did not finish, for exp:migration:resume: a run that failed, or one that was
 * interrupted (its row still says running, but no process holds the installation lock any more).
 *
 * The run named, or the newest run that failed or was interrupted. A run that is still running (the lock is held),
 * that finished, or that was rolled back is not resumed. Resuming is a new run (its own run id, captions and record)
 * of the migrations still pending: those the old run did not reach, the one that failed, and those a restore after
 * the failure took back. With apply, the row of an interrupted run is closed first (state failed, "interrupted").
 *
 * The report is the runner's with 'resumed_from' => the old run's row.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationResume
{
    /** The states of a run that can be resumed. */
    const RESUMABLE = array( 'failed', 'running' );

    /**
     * The run to resume: the one named, or the newest failed or interrupted one. Pure apart from the lock check.
     *
     * @param array[] $runs rows of exp_migration_run, newest first
     * @param string|null $runId
     * @param bool $locked whether the installation lock is held right now
     * @param string $error why there is none
     * @return array|null the run's row
     */
    public static function pick( array $runs, $runId, $locked, &$error = '' )
    {
        $error = '';
        foreach ( $runs as $run )
        {
            if ( $runId !== null && $runId !== '' && $run['run_id'] !== $runId )
                continue;
            if ( $run['state'] === 'running' && $locked )
            {
                $error = 'the run ' . $run['run_id'] . ' is still running (the installation lock is held)';
                return null;
            }
            if ( in_array( $run['state'], self::RESUMABLE, true ) )
                return $run;
            if ( $runId !== null && $runId !== '' )
            {
                $error = "the run $runId is " . $run['state'] . ', there is nothing to resume';
                return null;
            }
            // the newest run finished: nothing before it waits
            $error = 'the newest run ' . $run['run_id'] . ' is ' . $run['state'] . ', there is nothing to resume';
            return null;
        }
        $error = $runId !== null && $runId !== '' ? "no run $runId (exp_migration_run)" : 'no run that failed or was interrupted';
        return null;
    }

    /**
     * Resumes a run.
     *
     * @param string|null $runId null: the newest failed or interrupted one
     * @param array $options the options of expMigrationRunner::migrate() (apply, db, environment, no-caption ...)
     * @return array the report
     */
    public static function run( $runId, array $options = array() )
    {
        $db = isset( $options['db'] ) ? $options['db'] : eZDB::instance();
        if ( $runId !== null && $runId !== '' && !expMigrationRecord::validRunId( $runId ) )
            return self::refused( "not a run id: $runId", expUpgrade::EXIT_USAGE );
        if ( expMigrationRecord::runTableExists( $db ) !== true )
            return self::refused( 'the table ' . expMigrationRecord::RUN_TABLE . ' does not exist yet: there is no run to resume', expUpgrade::EXIT_FAILED );
        $lock = expMigrationLock::installation( isset( $options['lock-file'] ) ? $options['lock-file'] : null );
        $error = '';
        $run = self::pick( expMigrationRecord::runs( $db, 0 ), $runId, $lock->isLocked() && !$lock->isHeld(), $error );
        if ( !$run )
            return self::refused( $error, $runId !== null && $runId !== '' && strpos( $error, 'no run ' ) === 0 ? expUpgrade::EXIT_USAGE : expUpgrade::EXIT_FAILED );
        if ( !isset( $options['environment'] ) && $run['environment'] !== '' )
            $options['environment'] = $run['environment'];
        $options['db'] = $db;
        if ( !empty( $options['apply'] ) && $run['state'] === 'running' )
        {
            $run['state'] = expMigration::FAILED;
            $run['finished_at'] = $run['finished_at'] ?: time();
            $run['message'] = mb_substr( 'interrupted; resumed on ' . date( 'c' ) . ( $run['message'] !== '' ? '; ' . $run['message'] : '' ), 0, 1000 );
            expMigrationRecord::writeRun( $db, $run );
        }
        $report = expMigrationRunner::migrate( $options );
        $report['resumed_from'] = $run;
        if ( !empty( $options['apply'] ) )
            expUpgrade::audit( 'system.migration.run', array( 'object' => array( 'type' => 'migration_run', 'id' => $report['run_id'] ), 'verb' => 'resume',
                                                              'result' => $report['exit'] === expUpgrade::EXIT_OK ? 'success' : 'failure',
                                                              'x' => array( 'resumed_from' => $run['run_id'] ) ) );
        return $report;
    }

    /**
     * @param string $error
     * @param int $exit
     * @return array a report that refuses
     */
    protected static function refused( $error, $exit )
    {
        return array( 'schema' => expMigrationRunner::REPORT, 'mode' => 'resume', 'migrations' => array(), 'summary' => array(),
                      'errors' => array( $error ), 'warnings' => array(), 'resumed_from' => null, 'exit' => $exit );
    }
}
