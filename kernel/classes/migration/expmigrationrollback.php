<?php
/**
 * File containing the expMigrationRollback class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Taking migrations back by hand, three ways:
 *
 *   run()        a run: its pre caption (exp_migration_run.caption_pre) restored through exp:upgrade's rollback
 *                (expUpgradeRollback::run(): a safety caption of the current state first, then the parts). The run's
 *                row is written again afterwards with the state rolled-back, since the restore takes the record back
 *                to the time of the caption.
 *   migration()  one migration: its down() (expMigrationRunner::rollback()).
 *   caption()    any earlier caption, for experts: refused unless expert is given and confirm repeats the caption's
 *                id (the typed confirmation).
 *
 * Each lists what it would do unless apply is given. A database without a built-in restore (Oracle) stops with the
 * steps to restore by hand (expUpgradeRollback::manualSteps()). The audit event system.migration.rollback records
 * each (verbs rollback-run, rollback-caption; rollback for one migration, by the runner).
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationRollback
{
    /** The state of a run taken back with run(). */
    const ROLLED_BACK = 'rolled-back';

    /**
     * Restores the caption taken before a run.
     *
     * @param string $runId
     * @param array $options apply, db, root, parts, backup-dir, lock-file, no-caption, force-no-caption, no-verify
     *                       (expUpgradeRollback::run()); parts default to [CaptionSettings] RestoreParts
     * @return array the report of expUpgradeRollback::run() with 'run' => the run's row
     */
    public static function run( $runId, array $options = array() )
    {
        if ( !expMigrationRecord::validRunId( $runId ) )
            return self::refused( "not a run id: $runId", expUpgrade::EXIT_USAGE );
        $db = isset( $options['db'] ) ? $options['db'] : eZDB::instance();
        $run = expMigrationRecord::run( $db, $runId );
        if ( !$run )
            return self::refused( "no run $runId (exp_migration_run)", expUpgrade::EXIT_USAGE );
        if ( $run['caption_pre'] === '' )
            return self::refused( "the run $runId has no caption taken before it: take its migrations back one by one with down(), or restore an "
                                  . 'earlier caption (expert)', expUpgrade::EXIT_FAILED, array( 'run' => $run ) );
        $options['db'] = $db;
        if ( empty( $options['parts'] ) )
            $options['parts'] = implode( ',', expMigrationCaption::settingsFrom()['restore-parts'] );
        $report = self::restore( $run['caption_pre'], $options );
        $report['run'] = $run;
        if ( !empty( $options['apply'] ) && $report['exit'] === expUpgrade::EXIT_OK )
        {
            $run['state'] = self::ROLLED_BACK;
            $run['message'] = mb_substr( 'rolled back to the caption ' . $run['caption_pre'] . ' on ' . date( 'c' ) . '; ' . $run['message'], 0, 1000 );
            expMigrationRecord::writeRun( $db, $run );
            $report['run'] = $run;
        }
        if ( !empty( $options['apply'] ) )
            self::audit( 'rollback-run', $runId, $run['caption_pre'], $report['exit'] === expUpgrade::EXIT_OK );
        return $report;
    }

    /**
     * Takes one migration back with its down().
     *
     * @param string $id the migration's id
     * @param array $options apply, source (when two sources have the id), login, db, progress (the runner's
     *                       progress callback), and the options of expMigrationContext
     * @return array the runner's report
     */
    public static function migration( $id, array $options = array() )
    {
        $context = new expMigrationContext( isset( $options['db'] ) ? $options['db'] : null, !empty( $options['apply'] ), $options );
        $errors = array();
        $found = array();
        foreach ( expMigrationRunner::migrations( $context->environment(), $errors ) as $migration )
            if ( $migration->id() === (string)$id && ( empty( $options['source'] ) || $migration->source() === $options['source'] ) )
                $found[] = $migration;
        if ( count( $found ) !== 1 )
            return self::refused( $found ? "$id is in more than one source: name it with source" : "no migration $id", expUpgrade::EXIT_USAGE );
        return ( new expMigrationRunner( $context, array_intersect_key( $options, array( 'progress' => true ) ) ) )->rollback( $found );
    }

    /**
     * Restores any caption (expert).
     *
     * @param string $captionId
     * @param array $options as run(), and expert (bool), confirm (the caption's id typed again)
     * @return array the report of expUpgradeRollback::run()
     */
    public static function caption( $captionId, array $options = array() )
    {
        if ( !empty( $options['apply'] ) )
        {
            if ( empty( $options['expert'] ) )
                return self::refused( 'restoring an earlier caption is for experts: give expert and confirm=<the caption id>', expUpgrade::EXIT_USAGE );
            if ( !isset( $options['confirm'] ) || (string)$options['confirm'] !== (string)$captionId )
                return self::refused( "confirm with the caption's id ($captionId) typed again", expUpgrade::EXIT_USAGE );
        }
        if ( empty( $options['parts'] ) )
            $options['parts'] = implode( ',', expMigrationCaption::settingsFrom()['restore-parts'] );
        $report = self::restore( $captionId, $options );
        if ( !empty( $options['apply'] ) )
            self::audit( 'rollback-caption', '', $captionId, $report['exit'] === expUpgrade::EXIT_OK );
        return $report;
    }

    /**
     * expUpgradeRollback::run(), with the steps to restore by hand where the engine has no built-in restore.
     *
     * @param string $captionId
     * @param array $options
     * @return array
     */
    protected static function restore( $captionId, array $options )
    {
        $report = expUpgradeRollback::run( $captionId, $options );
        $db = isset( $options['db'] ) ? $options['db'] : null;
        $engine = $db ? expKernelTableSchema::type( $db ) : '';
        $parts = expUpgrade::listOption( isset( $options['parts'] ) ? $options['parts'] : array() );
        $report['manual'] = ( !$parts || in_array( 'database', $parts, true ) ) && $engine !== '' && !in_array( $engine, expUpgradeRollback::RESTORE_ENGINES, true )
                          ? expUpgradeRollback::manualSteps( $engine, $db, expUpgradeCaption::find( $captionId, isset( $options['backup-dir'] ) ? $options['backup-dir'] : null ) )
                          : array();
        return $report;
    }

    /**
     * A report that refuses.
     *
     * @param string $error
     * @param int $exit
     * @param array $extra
     * @return array
     */
    protected static function refused( $error, $exit, array $extra = array() )
    {
        return array( 'schema' => expMigrationRunner::REPORT, 'mode' => 'rollback', 'errors' => array( $error ), 'warnings' => array(),
                      'manual' => array(), 'exit' => $exit ) + $extra;
    }

    /**
     * @param string $verb
     * @param string $runId
     * @param string $captionId
     * @param bool $ok
     */
    protected static function audit( $verb, $runId, $captionId, $ok )
    {
        expUpgrade::audit( 'system.migration.rollback', array( 'object' => array( 'type' => $runId !== '' ? 'migration_run' : 'caption', 'id' => $runId !== '' ? $runId : $captionId ),
                                                               'verb' => $verb, 'result' => $ok ? 'success' : 'failure', 'x' => array( 'caption' => $captionId ) ) );
    }
}
