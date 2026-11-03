<?php
/**
 * File containing the expMigrationStatus class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The state of the installation's migrations, as exp:migration:status and the administration show it: every
 * migration found for the environment, the kernel's upgrade steps first (source "kernel", expMigrationUpgradeSource),
 * with its state (done, pending, not-needed, failed, unknown) and its record,
 * the records whose migration file is gone (orphans), the latest runs, the runs whose push waits, and who holds the
 * installation lock. Changes nothing and takes no lock.
 *
 * The report is the runner's (exp-migration/1, mode "status") with these keys added:
 *
 *   table     whether exp_migration and exp_migration_run exist
 *   orphans   rows of exp_migration (source other than kernel) whose migration is not found any more
 *   runs      the latest rows of exp_migration_run, newest first
 *   pushes    the runs whose push_state is pending (the cronjob part migrationpush, exp:migration:push)
 *   lock      the holder of the installation lock (expMigrationLock::holder()), null when it is free
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationStatus
{
    /**
     * @param array $options db, environment, only (migration ids), runs (how many runs, default 10), the options of
     *                       expMigrationContext (lock-file, repository, root, login ...); migrations (expMigration[])
     *                       instead of the installation's, with errors (string[]) their problems; upgrade-source and
     *                       locator for expMigrationRunner::installation()
     * @return array the report
     */
    public static function report( array $options = array() )
    {
        $context = new expMigrationContext( isset( $options['db'] ) ? $options['db'] : null, false, $options + array( 'no-mirror' => true ) );
        $errors = isset( $options['errors'] ) ? (array)$options['errors'] : array();
        $warnings = array();
        $migrations = isset( $options['migrations'] ) ? (array)$options['migrations']
                                                     : expMigrationRunner::installation( $context->environment(), $errors, $warnings, $options );
        $only = isset( $options['only'] ) ? expUpgrade::listOption( $options['only'] ) : array();
        $all = $migrations;
        if ( $only )
            $migrations = expMigrationRunner::select( $migrations, $only, array(), $context->root(), $errors );

        $db = $context->db();
        $ready = $context->recordReady();
        $runner = new expMigrationRunner( $context, array( 'plan' => false ) );
        $report = $runner->run( $migrations );
        $report['mode'] = 'status';
        $report['errors'] = array_merge( $errors, $report['errors'] );
        $report['warnings'] = array_merge( $warnings, $report['warnings'] );
        $report['table'] = $ready;
        // the kernel version: a deploy (expMigrationRemoteDeploy) compares the source's with every remote's
        $report['version'] = class_exists( 'ExponentialSDK' ) ? (string)ExponentialSDK::version() : '';
        // ... and checks the PHP version, the extensions' versions and the free disk space before it sends anything
        $report['host'] = expMigrationRemoteDeploy::hostInfo( $context->root() );
        if ( !$ready )
            $report['warnings'][] = 'the tables ' . expMigrationRecord::TABLE . ' and ' . expMigrationRecord::RUN_TABLE
                                  . ' do not exist yet: ./console exp:upgrade --apply makes them (step 6.0.15-migration-tables)';

        $found = array();
        foreach ( $all as $migration )
            $found[$migration->source() . "\n" . $migration->id()] = true;
        $report['orphans'] = array();
        $report['runs'] = array();
        $report['pushes'] = array();
        if ( $ready )
        {
            foreach ( expMigrationRecord::all( $db ) as $row )
                if ( $row['source'] !== expMigrationRecord::SOURCE_UPGRADE && !isset( $found[$row['source'] . "\n" . $row['migration_id']] ) )
                    $report['orphans'][] = array( 'id' => $row['migration_id'], 'source' => $row['source'], 'state' => $row['state'], 'file' => $row['file_path'],
                                                  'run_id' => $row['run_id'], 'finished_at' => $row['finished_at'] ? date( 'c', $row['finished_at'] ) : null );
            $limit = isset( $options['runs'] ) ? max( 0, (int)$options['runs'] ) : 10;
            foreach ( expMigrationRecord::runs( $db, 0 ) as $i => $run )
            {
                if ( $run['push_state'] === expMigrationRepository::PENDING )
                    $report['pushes'][] = $run['run_id'];
                if ( $limit > 0 && $i < $limit )
                    $report['runs'][] = self::runSummary( $run );
            }
        }
        foreach ( $report['orphans'] as $orphan )
            $report['warnings'][] = $orphan['id'] . ' (' . $orphan['source'] . ') is recorded as ' . $orphan['state'] . ', but its file is not found any more';
        if ( $report['pushes'] )
            $report['warnings'][] = count( $report['pushes'] ) . ' run(s) wait for their push: ./console exp:migration:push --apply';
        $holder = $context->lock()->holder();
        $report['lock'] = $context->lock()->isLocked() ? $holder : null;
        if ( $report['errors'] && $report['exit'] === expUpgrade::EXIT_OK )
            $report['exit'] = expUpgrade::EXIT_FAILED;
        return $report;
    }

    /**
     * A run row as the status lists it. Pure.
     *
     * @param array $run a row of exp_migration_run
     * @return array
     */
    public static function runSummary( array $run )
    {
        return array(
            'run_id' => $run['run_id'],
            'state' => $run['state'],
            'started_at' => $run['started_at'] ? date( 'c', $run['started_at'] ) : null,
            'finished_at' => $run['finished_at'] ? date( 'c', $run['finished_at'] ) : null,
            'run_by' => $run['run_by'],
            'environment' => $run['environment'],
            'caption_pre' => $run['caption_pre'],
            'caption_post' => $run['caption_post'],
            'commit_id' => $run['commit_id'],
            'tag_name' => $run['tag_name'],
            'push_state' => $run['push_state'],
            'message' => $run['message'],
        );
    }
}
