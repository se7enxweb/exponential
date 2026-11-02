<?php
/**
 * File containing the expMigrationService class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * What the APIs read about the migrations of the installation: the status (every migration with its state, the
 * counts, who holds the lock), the latest runs, and one run with the migrations it wrote. The GraphQL queries
 * migrationStatus, migrationRuns and migrationRun (expMigrationGraphQLExtension) and the REST routes
 * /api/migration/v1/status, /runs and /runs/<id> (expMigrationRestProvider) answer from it, with the same fields.
 *
 * Read only: nothing here runs, undoes or locks anything (a run through an API is not offered: "Verify it's you"
 * asks only the web form). Whoever reads needs the policy migration/read (mayRead()); the callers check it.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationService
{
    /** The most runs one read lists. */
    const MAX_RUNS = 100;

    /**
     * @param eZUser|null $user null: the current user
     * @return bool the user has the policy migration/read
     */
    public function mayRead( $user = null )
    {
        return expMigrationAdmin::canDo( 'read', $user );
    }

    /**
     * The status: counts, environment, engine, lock, every migration found and the problems.
     *
     * @return array summary (pending, failed, orphans, total, ready), environment, engine, lock ('' when free),
     *               migrations (each of migration()), orphans, warnings, errors
     */
    public function status()
    {
        return self::statusOf( expMigrationStatus::report( array( 'runs' => 1 ) ) );
    }

    /**
     * The latest runs, newest first.
     *
     * @param int $limit 1 to MAX_RUNS
     * @param int $offset
     * @return array[] each of expMigrationStatus::runSummary()
     */
    public function runs( $limit = 20, $offset = 0 )
    {
        list( $limit, $offset ) = self::page( $limit, $offset );
        $rows = array_slice( expMigrationRecord::runs( eZDB::instance(), 0 ), $offset, $limit );
        return array_map( array( 'expMigrationStatus', 'runSummary' ), $rows );
    }

    /**
     * One run and the migrations it wrote (a migration undone later is not listed any more).
     *
     * @param string $runId
     * @return array|null a run summary with migrations (each of record()), null when there is no such run
     */
    public function run( $runId )
    {
        if ( !expMigrationRecord::validRunId( $runId ) )
            return null;
        $db = eZDB::instance();
        $run = expMigrationRecord::run( $db, $runId );
        if ( $run === null )
            return null;
        $migrations = array();
        foreach ( expMigrationRecord::all( $db ) as $row )
            if ( $row['run_id'] === $runId )
                $migrations[] = self::record( $row );
        return expMigrationStatus::runSummary( $run ) + array( 'migrations' => $migrations );
    }

    /**
     * A limit and an offset within their bounds. Pure.
     *
     * @param mixed $limit
     * @param mixed $offset
     * @return int[] array( limit, offset )
     */
    public static function page( $limit, $offset )
    {
        return array( max( 1, min( self::MAX_RUNS, (int)$limit ) ), max( 0, (int)$offset ) );
    }

    /**
     * A status report (expMigrationStatus::report()) as the APIs give it. Pure.
     *
     * @param array $report
     * @return array
     */
    public static function statusOf( array $report )
    {
        $summary = expMigrationAdmin::summary( $report );
        $migrations = array();
        foreach ( isset( $report['migrations'] ) ? (array)$report['migrations'] : array() as $entry )
            $migrations[] = self::migration( $entry );
        $orphans = array();
        foreach ( isset( $report['orphans'] ) ? (array)$report['orphans'] : array() as $orphan )
            $orphans[] = array( 'id' => (string)$orphan['id'], 'source' => (string)$orphan['source'], 'state' => (string)$orphan['state'],
                                'file' => (string)$orphan['file'], 'run_id' => (string)$orphan['run_id'], 'finished_at' => $orphan['finished_at'] );
        return array(
            'summary' => array( 'pending' => (int)$summary['pending'], 'failed' => (int)$summary['failed'], 'orphans' => (int)$summary['orphans'],
                                'total' => (int)$summary['total'], 'ready' => (bool)$summary['ready'] ),
            'environment' => isset( $report['environment'] ) ? (string)$report['environment'] : '',
            'engine' => isset( $report['engine'] ) ? (string)$report['engine'] : '',
            'lock' => (string)$summary['lock'],
            'migrations' => $migrations,
            'orphans' => $orphans,
            'warnings' => array_values( array_map( 'strval', isset( $report['warnings'] ) ? (array)$report['warnings'] : array() ) ),
            'errors' => array_values( array_map( 'strval', isset( $report['errors'] ) ? (array)$report['errors'] : array() ) ),
        );
    }

    /**
     * A migration of a status report (an entry of expMigrationRunner) as the APIs give it. Pure.
     *
     * @param array $entry
     * @return array id, source, description, stage, file, state, detail, changed, record (state, run_id, finished_at, or null)
     */
    public static function migration( array $entry )
    {
        return array(
            'id' => (string)$entry['id'],
            'source' => (string)$entry['source'],
            'description' => isset( $entry['description'] ) ? (string)$entry['description'] : '',
            'stage' => isset( $entry['stage'] ) ? (string)$entry['stage'] : 'migration',
            'file' => isset( $entry['file'] ) ? (string)$entry['file'] : '',
            'state' => (string)$entry['state'],
            'detail' => isset( $entry['detail'] ) ? (string)$entry['detail'] : '',
            'changed' => !empty( $entry['changed'] ),
            'record' => isset( $entry['record'] ) && is_array( $entry['record'] ) ? $entry['record'] : null,
        );
    }

    /**
     * A row of exp_migration as the APIs give it (never the checksum's history, the caption or the revision). Pure.
     *
     * @param array $row
     * @return array id, source, state, file, started_at, finished_at, duration_ms, run_by, engine, message
     */
    public static function record( array $row )
    {
        return array(
            'id' => (string)$row['migration_id'],
            'source' => (string)$row['source'],
            'state' => (string)$row['state'],
            'file' => (string)$row['file_path'],
            'started_at' => $row['started_at'] ? date( 'c', (int)$row['started_at'] ) : null,
            'finished_at' => $row['finished_at'] ? date( 'c', (int)$row['finished_at'] ) : null,
            'duration_ms' => (int)$row['duration_ms'],
            'run_by' => (string)$row['run_by'],
            'engine' => (string)$row['engine'],
            'message' => (string)$row['message'],
        );
    }
}
