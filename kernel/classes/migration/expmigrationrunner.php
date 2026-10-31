<?php
/**
 * File containing the expMigrationRunner class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Runs migrations: the plan first (the state of each, the checksum guard), then, when the context writes, each
 * pending migration in the order given:
 *
 *   1. maintenance mode on, when the migration asks for it (and off again after it, when this run switched it on)
 *   2. a transaction where the engine has one (expMigrationContext::transactionMode()): PostgreSQL and SQLite undo
 *      everything a failed migration did, MySQL/MariaDB and Oracle only its data (they commit a schema change at
 *      once), MongoDB nothing (begin, commit and rollback do nothing there)
 *   3. up()
 *   4. the record (exp_migration, through the context), the events migration/step/before and migration/step/after
 *      (ezpEvent), the run log and the audit events system.migration.apply or system.migration.fail
 *
 * The run stops at the first migration that fails (migration.ini [RunSettings] StopOnFailure=enabled); with
 * StopOnFailure=disabled it goes on with the migrations that do not require a failed one. The whole run holds the
 * installation lock (expMigrationLock, shared with exp:upgrade), runs as the run's user (the signed-in user, or
 * [RunSettings] RunAsUser) and is framed by the events migration/run/before and migration/run/after and the audit event
 * system.migration.run. A dry run (a context that does not write) changes nothing and takes no lock.
 *
 * The checksum guard: a migration recorded as done whose file has changed since (another checksum) is refused, and
 * the run with it, unless the run is forced with a reason (options force and reason, --force --reason=<text>); the
 * new checksum is then recorded and the override audited (system.migration.apply, verb accept-change). The migration
 * is not run again.
 *
 * Listeners receive, by position:
 *   migration/run/before   ( string $runId, string[] $ids, expMigrationContext $context )
 *   migration/step/before  ( string $runId, expMigration $migration, expMigrationContext $context )
 *   migration/step/after   ( string $runId, expMigration $migration, array $entry, expMigrationContext $context )
 *   migration/run/after    ( string $runId, array $report, expMigrationContext $context )
 *
 * The report (run(), rollback()) is versioned (exp-migration/1); its exit codes are expUpgrade's (0 done, 1 failed,
 * 2 usage, 3 unreachable, 4 internal).
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationRunner
{
    /** The version of the report. */
    const REPORT = 'exp-migration/1';

    /** @var expMigrationContext */
    protected $context;

    /** @var array */
    protected $options;

    /**
     * @param expMigrationContext $context
     * @param array $options 'force' => bool, 'reason' => string (both for the checksum guard),
     *                       'stop-on-failure' => bool (default true)
     */
    public function __construct( expMigrationContext $context, array $options = array() )
    {
        $this->context = $context;
        $this->options = $options + array( 'force' => false, 'reason' => '', 'stop-on-failure' => true );
    }

    /**
     * The options of [RunSettings] (migration.ini) under the given ones.
     *
     * @param array $options
     * @param eZINI|null $ini
     * @return array
     */
    public static function settingsOptions( array $options = array(), $ini = null )
    {
        $ini = $ini ?: eZINI::instance( 'migration.ini' );
        if ( !array_key_exists( 'stop-on-failure', $options ) )
            $options['stop-on-failure'] = !$ini->hasVariable( 'RunSettings', 'StopOnFailure' ) || $ini->variable( 'RunSettings', 'StopOnFailure' ) !== 'disabled';
        return $options;
    }

    /**
     * Finds, orders, loads and runs the installation's migrations on its database (the entry point the console
     * commands and the administration build on). A dry run unless $options['apply'].
     *
     * @param array $options apply, force, reason, login, environment, and the options of expMigrationContext
     * @return array the report; the problems of finding and loading are its errors
     */
    public static function migrate( array $options = array() )
    {
        $context = new expMigrationContext( isset( $options['db'] ) ? $options['db'] : null, !empty( $options['apply'] ), $options );
        $errors = array();
        $migrations = self::migrations( $context->environment(), $errors );
        $runner = new self( $context, self::settingsOptions( array_intersect_key( $options, array_flip( array( 'force', 'reason', 'stop-on-failure' ) ) ) ) );
        if ( $errors )
        {
            // nothing runs while a migration cannot be found, ordered or loaded: the order would not be the one written
            $context = new expMigrationContext( $context->db(), false, $options + array( 'run-id' => $context->runId() ) );
            $report = ( new self( $context ) )->run( $migrations );
            $report['errors'] = array_merge( $errors, $report['errors'] );
            $report['exit'] = expUpgrade::EXIT_FAILED;
            return $report;
        }
        return $runner->run( $migrations );
    }

    /** @return expMigrationContext */
    public function context()
    {
        return $this->context;
    }

    /**
     * The installation's migrations as the runner takes them: found (expMigrationLocator), ordered for the environment
     * (expMigrationGraph) and loaded. A migration that cannot be loaded is listed in $errors and left out, and so is
     * every migration after it that requires it.
     *
     * @param string|null $environment null: the installation's
     * @param string[] $errors problems are added here
     * @param expMigrationLocator|null $locator
     * @return expMigration[]
     */
    public static function migrations( $environment = null, array &$errors = array(), $locator = null )
    {
        $locator = $locator ?: expMigrationLocator::fromSettings();
        $definitions = $locator->find();
        foreach ( $locator->errors() as $error )
            $errors[] = $error;
        $extensions = class_exists( 'eZExtension' ) ? eZExtension::activeExtensions() : null;
        $graph = new expMigrationGraph( $definitions, array( 'environment' => $environment === null ? expMigrationRecord::environment() : $environment,
                                                             'extensions' => $extensions ) );
        foreach ( $graph->errors() as $error )
            $errors[] = $error;
        return self::load( $graph->migrations(), $errors );
    }

    /**
     * The migrations of definitions, in their order. PHP migrations are loaded from their files; the other types
     * (yml, sql, ini) have no runner yet and are listed in $errors.
     *
     * @param expMigrationDefinition[] $definitions
     * @param string[] $errors
     * @return expMigration[]
     */
    public static function load( array $definitions, array &$errors = array() )
    {
        $migrations = array();
        $missing = array();
        foreach ( $definitions as $definition )
        {
            $id = $definition->id();
            $waits = array_values( array_intersect( $definition->requiredMigrations(), array_keys( $missing ) ) );
            if ( $waits )
            {
                $errors[] = "$id: left out, it requires " . implode( ', ', $waits ) . ', which cannot be loaded';
                $missing[$id] = true;
                continue;
            }
            try
            {
                if ( $definition->type() !== 'php' )
                    throw new RuntimeException( 'migrations of type .' . $definition->type() . ' cannot be run yet' );
                $migrations[] = expMigrationPhp::load( $definition );
            }
            catch ( Throwable $e )
            {
                $errors[] = "$id: " . $e->getMessage();
                $missing[$id] = true;
            }
        }
        return $migrations;
    }

    /**
     * The plan, and with a context that writes the run.
     *
     * @param expMigration[] $migrations in the order they run (expMigrationGraph)
     * @return array the report
     */
    public function run( array $migrations )
    {
        $context = $this->context;
        $apply = $context->isApply();
        $report = $this->report( $apply ? 'apply' : 'dry-run' );
        $lock = null;
        $heldBefore = false;
        $runStarted = false;
        try
        {
            $byId = array();
            foreach ( $migrations as $migration )
            {
                $entry = $this->entry( $migration );
                $byId[$entry['id']] = $migration;
                $report['migrations'][] = $entry;
            }

            // the checksum guard
            $changed = array();
            foreach ( $report['migrations'] as $i => $entry )
                if ( $entry['changed'] )
                    $changed[] = $i;
            $accept = false;
            if ( $changed )
            {
                $forced = !empty( $this->options['force'] );
                $reason = trim( (string)$this->options['reason'] );
                foreach ( $changed as $i )
                {
                    $entry =& $report['migrations'][$i];
                    $text = $entry['id'] . ' changed after it was applied (checksum ' . substr( $entry['record_checksum'], 0, 12 ) . ' -> '
                          . substr( (string)$entry['checksum'], 0, 12 ) . ')';
                    if ( $forced && $reason !== '' )
                    {
                        $entry['action'] = $apply ? 'accepted' : 'would-accept';
                        $report['warnings'][] = "$text; accepted: $reason";
                    }
                    else
                    {
                        $entry['action'] = 'refused';
                        $report['errors'][] = $text . ( $forced ? '; --force needs a --reason=<text>' : '; a run with --force --reason=<text> accepts the change' );
                    }
                    unset( $entry );
                }
                if ( !( $forced && $reason !== '' ) )
                {
                    $report['exit'] = expUpgrade::EXIT_FAILED;
                    $this->summarize( $report );
                    return $report;
                }
                $accept = true;
            }

            $pending = array();
            foreach ( $report['migrations'] as $i => $entry )
                if ( in_array( $entry['state'], array( expMigration::PENDING, expMigration::FAILED ), true ) )
                    $pending[] = $i;
                else if ( $entry['state'] === expMigration::UNKNOWN )
                    $report['warnings'][] = $entry['id'] . ': skipped, its state cannot be told: ' . $entry['detail'];

            if ( !$apply )
            {
                foreach ( $pending as $i )
                {
                    $entry =& $report['migrations'][$i];
                    $entry['action'] = 'would-apply';
                    try
                    {
                        $entry['plan'] = array_values( array_map( 'strval', (array)$byId[$entry['id']]->plan( $context ) ) );
                    }
                    catch ( Throwable $e )
                    {
                        $entry['plan'] = array( 'cannot list the changes: ' . $e->getMessage() );
                    }
                    unset( $entry );
                }
                $this->summarize( $report );
                return $report;
            }

            if ( !$pending && !$accept )
            {
                $this->summarize( $report );
                return $report;
            }

            if ( !$context->recordReady() )
            {
                $report['errors'][] = 'the table ' . expMigrationRecord::TABLE . ' does not exist yet: run ./console exp:upgrade --apply first (step 6.0.15-migration-tables)';
                $report['exit'] = expUpgrade::EXIT_FAILED;
                $this->summarize( $report );
                return $report;
            }
            if ( !$context->user() )
            {
                $report['errors'][] = 'the user to run as does not exist: ' . ( $context->option( 'login' ) ?: '[RunSettings] RunAsUser=' . expMigrationContext::runAsLogin() . ' (migration.ini)' );
                $report['exit'] = expUpgrade::EXIT_FAILED;
                $this->summarize( $report );
                return $report;
            }
            $report['user'] = $context->userLogin();

            $lock = $context->lock();
            $heldBefore = $lock->isHeld();
            if ( !$lock->acquire( array( 'purpose' => 'migration run ' . $context->runId(), 'user' => $context->userLogin() ) ) )
            {
                $lock = null;
                $report['errors'][] = 'another run holds the lock: ' . expMigrationLock::describe( $context->lock()->holder() );
                $report['exit'] = expUpgrade::EXIT_FAILED;
                $this->summarize( $report );
                return $report;
            }

            $started = time();
            $report['started_at'] = $started;
            $runStarted = true;
            $context->writeRun( $this->runRow( 'running', $started, 0, '' ) );
            $context->log( 'info', 'run ' . $context->runId() . ' by ' . $context->userLogin() . ' on ' . $context->engine()
                                   . ( $context->environment() !== '' ? ' in ' . $context->environment() : '' ) . ': ' . count( $pending ) . ' to apply' );
            $ids = array();
            foreach ( $pending as $i )
                $ids[] = $report['migrations'][$i]['id'];
            $context->notify( 'migration/run/before', array( $context->runId(), $ids, $context ) );
            $context->becomeUser();
            try
            {
                if ( $accept )
                    foreach ( $changed as $i )
                        $this->acceptChange( $report['migrations'][$i], $byId[$report['migrations'][$i]['id']] );

                $outcome = array();
                $stop = false;
                foreach ( $pending as $i )
                {
                    $entry =& $report['migrations'][$i];
                    $migration = $byId[$entry['id']];
                    if ( $stop )
                    {
                        $entry['action'] = 'not-run';
                        unset( $entry );
                        continue;
                    }
                    $blockedBy = array();
                    foreach ( $migration->requires() as $dep )
                        if ( isset( $outcome[$dep] ) && $outcome[$dep] !== expMigration::DONE )
                            $blockedBy[] = $dep;
                    if ( $blockedBy )
                    {
                        $entry['action'] = 'blocked';
                        $entry['messages'][] = 'waits for ' . implode( ', ', $blockedBy );
                        $outcome[$entry['id']] = 'blocked';
                        $context->log( 'warning', $entry['id'] . ' not run: waits for ' . implode( ', ', $blockedBy ) );
                        unset( $entry );
                        continue;
                    }
                    $this->apply( $entry, $migration );
                    $outcome[$entry['id']] = $entry['state'];
                    if ( $entry['state'] === expMigration::FAILED && !empty( $this->options['stop-on-failure'] ) )
                        $stop = true;
                    unset( $entry );
                }
            }
            finally
            {
                $context->restoreUser();
            }
        }
        catch ( Throwable $e )
        {
            $report['errors'][] = 'internal error: ' . $e->getMessage();
            $report['exit'] = expUpgrade::EXIT_INTERNAL;
        }

        $this->summarize( $report );
        if ( $runStarted )
            $this->finishRun( $report );
        if ( $lock && !$heldBefore )
            $lock->release();
        return $report;
    }

    /**
     * Undoes applied migrations with their down(), the last first: each must be done and reversible. The record of
     * each is removed. Under the lock, as the run's user, with the events and the audit event
     * system.migration.rollback; stops at the first failure. A context that does not write lists what would be undone.
     *
     * @param expMigration[] $migrations in the order they ran
     * @return array the report
     */
    public function rollback( array $migrations )
    {
        $context = $this->context;
        $apply = $context->isApply();
        $report = $this->report( $apply ? 'rollback' : 'rollback-dry-run' );
        $lock = null;
        $heldBefore = false;
        try
        {
            foreach ( array_reverse( $migrations ) as $migration )
            {
                $entry = $this->entry( $migration );
                if ( $entry['state'] !== expMigration::DONE )
                    $report['errors'][] = $entry['id'] . ' cannot be undone: it is ' . $entry['state'];
                else if ( !$migration->reversible() )
                    $report['errors'][] = $entry['id'] . ' cannot be undone by itself (no down()); restore the caption taken before it';
                $report['migrations'][] = $entry;
            }
            if ( $report['errors'] || !$apply )
            {
                if ( $report['errors'] )
                    $report['exit'] = expUpgrade::EXIT_FAILED;
                else
                    foreach ( $report['migrations'] as &$entry )
                        $entry['action'] = 'would-undo';
                unset( $entry );
                $this->summarize( $report );
                return $report;
            }
            if ( !$context->user() )
            {
                $report['errors'][] = 'the user to run as does not exist';
                $report['exit'] = expUpgrade::EXIT_FAILED;
                $this->summarize( $report );
                return $report;
            }
            $report['user'] = $context->userLogin();
            $lock = $context->lock();
            $heldBefore = $lock->isHeld();
            if ( !$lock->acquire( array( 'purpose' => 'migration rollback ' . $context->runId(), 'user' => $context->userLogin() ) ) )
            {
                $lock = null;
                $report['errors'][] = 'another run holds the lock: ' . expMigrationLock::describe( $context->lock()->holder() );
                $report['exit'] = expUpgrade::EXIT_FAILED;
                $this->summarize( $report );
                return $report;
            }
            $context->becomeUser();
            try
            {
                $stop = false;
                $reversed = array_reverse( $migrations );
                foreach ( $report['migrations'] as $i => &$entry )
                {
                    if ( $stop )
                    {
                        $entry['action'] = 'not-run';
                        continue;
                    }
                    $migration = $reversed[$i];
                    $context->notify( 'migration/step/before', array( $context->runId(), $migration, $context ) );
                    $start = microtime( true );
                    $useTransaction = $this->useTransaction( $migration );
                    try
                    {
                        if ( $useTransaction )
                            $context->begin();
                        $entry['messages'] = array_values( array_map( 'strval', (array)$migration->down( $context ) ) );
                        if ( $useTransaction )
                            $context->commit();
                        $context->removeRecord( $entry['source'], $entry['id'] );
                        $entry['state'] = expMigration::PENDING;
                        $entry['action'] = 'undone';
                    }
                    catch ( Throwable $e )
                    {
                        if ( $useTransaction )
                            $context->rollback();
                        $entry['action'] = 'failed';
                        $entry['messages'][] = $e->getMessage();
                        $report['errors'][] = $entry['id'] . ': ' . $e->getMessage();
                        $stop = true;
                    }
                    $entry['duration_ms'] = (int)round( ( microtime( true ) - $start ) * 1000 );
                    $context->log( $entry['action'] === 'undone' ? 'info' : 'error', $entry['action'] . ' ' . $entry['id'] . ' in ' . $entry['duration_ms'] . ' ms'
                                   . ( $entry['messages'] ? ': ' . implode( ' | ', $entry['messages'] ) : '' ) );
                    $context->audit( 'system.migration.rollback', array( 'object' => array( 'type' => 'migration', 'id' => $entry['id'] ), 'verb' => 'rollback',
                                                                         'result' => $entry['action'] === 'undone' ? 'success' : 'failure',
                                                                         'reason' => $entry['action'] === 'undone' ? '' : mb_substr( (string)end( $entry['messages'] ), 0, 200 ),
                                                                         'x' => array( 'source' => $entry['source'], 'run_id' => $context->runId(), 'engine' => $context->engine(),
                                                                                       'duration_ms' => $entry['duration_ms'] ) ) );
                    $context->notify( 'migration/step/after', array( $context->runId(), $migration, $entry, $context ) );
                }
                unset( $entry );
            }
            finally
            {
                $context->restoreUser();
            }
        }
        catch ( Throwable $e )
        {
            $report['errors'][] = 'internal error: ' . $e->getMessage();
            $report['exit'] = expUpgrade::EXIT_INTERNAL;
        }
        $this->summarize( $report );
        if ( $apply )
            $context->writeStatus();
        if ( $lock && !$heldBefore )
            $lock->release();
        return $report;
    }

    /**
     * Applies one migration and fills its entry.
     *
     * @param array $entry
     * @param expMigration $migration
     */
    protected function apply( array &$entry, expMigration $migration )
    {
        $context = $this->context;
        $id = $entry['id'];
        $context->notify( 'migration/step/before', array( $context->runId(), $migration, $context ) );

        $maintenance = false;
        if ( $migration->maintenance() )
        {
            $maintenance = $context->maintenanceBegin();
            $entry['maintenance'] = $maintenance === false ? 'failed' : $maintenance;
            if ( $maintenance === false )
                $entry['messages'][] = 'maintenance mode could not be switched on';
        }

        $useTransaction = $this->useTransaction( $migration );
        $entry['transaction'] = $useTransaction ? $context->transactionMode() : expMigrationContext::TRANSACTION_NONE;
        $startedAt = time();
        $start = microtime( true );
        try
        {
            if ( $useTransaction )
                $context->begin();
            $entry['messages'] = array_merge( $entry['messages'], array_values( array_map( 'strval', (array)$migration->up( $context ) ) ) );
            if ( $migration instanceof expMigrationPhp )
            {
                $detected = $migration->detect( $context );
                if ( $detected !== null && !in_array( $detected, array( expMigration::DONE, expMigration::NOT_NEEDED ), true ) )
                    throw new RuntimeException( "applied, but detection still says $detected" );
            }
            if ( $useTransaction )
                $context->commit();
            $entry['state'] = expMigration::DONE;
            $entry['action'] = 'applied';
        }
        catch ( Throwable $e )
        {
            if ( $useTransaction )
            {
                $context->rollback();
                if ( $entry['transaction'] === expMigrationContext::TRANSACTION_DATA )
                    $entry['messages'][] = 'rolled back what could be: ' . $context->engine() . ' keeps schema changes made before the failure';
            }
            $entry['state'] = expMigration::FAILED;
            $entry['action'] = 'failed';
            $entry['messages'][] = $e->getMessage();
        }
        $entry['duration_ms'] = (int)round( ( microtime( true ) - $start ) * 1000 );

        if ( $maintenance === 'enabled' || $maintenance === 'adopted' )
            $context->maintenanceEnd();

        $written = $context->writeRecord( array(
            'source' => $entry['source'],
            'migration_id' => $id,
            'stage' => 'migration',
            'file_path' => $entry['file'],
            'checksum' => (string)$entry['checksum'],
            'state' => $entry['state'],
            'environment' => $context->environment(),
            'run_id' => $context->runId(),
            'started_at' => $startedAt,
            'finished_at' => time(),
            'duration_ms' => $entry['duration_ms'],
            'run_by' => $context->runBy(),
            'engine' => $context->engine(),
            'message' => implode( "\n", $entry['messages'] ),
        ) );
        if ( !$written )
            $entry['messages'][] = 'not recorded (no table ' . expMigrationRecord::TABLE . ')';

        $context->log( $entry['state'] === expMigration::DONE ? 'info' : 'error',
                       $entry['action'] . " $id in " . $entry['duration_ms'] . ' ms' . ( $entry['messages'] ? ': ' . implode( ' | ', $entry['messages'] ) : '' ) );
        $done = $entry['state'] === expMigration::DONE;
        $context->audit( $done ? 'system.migration.apply' : 'system.migration.fail',
                         array( 'object' => array( 'type' => 'migration', 'id' => $id ), 'verb' => 'apply',
                                'result' => $done ? 'success' : 'failure',
                                'reason' => $done ? '' : mb_substr( (string)end( $entry['messages'] ), 0, 200 ),
                                'x' => array( 'source' => $entry['source'], 'run_id' => $context->runId(), 'engine' => $context->engine(),
                                              'duration_ms' => $entry['duration_ms'], 'transaction' => $entry['transaction'] ) ) );
        $context->notify( 'migration/step/after', array( $context->runId(), $migration, $entry, $context ) );
    }

    /**
     * Records the new checksum of an applied migration whose file changed (the forced checksum guard) and audits it.
     *
     * @param array $entry
     * @param expMigration $migration
     */
    protected function acceptChange( array &$entry, expMigration $migration )
    {
        $context = $this->context;
        $reason = trim( (string)$this->options['reason'] );
        $row = $context->record( $entry['source'], $entry['id'] );
        if ( $row )
        {
            $row['checksum'] = (string)$entry['checksum'];
            $row['message'] = 'checksum ' . $entry['record_checksum'] . ' replaced by run ' . $context->runId() . ': ' . $reason;
            $context->writeRecord( $row );
        }
        $entry['messages'][] = 'the change of the file was accepted: ' . $reason;
        $context->log( 'warning', $entry['id'] . ' changed after it was applied; the change was accepted: ' . $reason );
        $context->audit( 'system.migration.apply', array( 'object' => array( 'type' => 'migration', 'id' => $entry['id'] ), 'verb' => 'accept-change',
                                                          'result' => 'success', 'reason' => mb_substr( $reason, 0, 200 ),
                                                          'x' => array( 'source' => $entry['source'], 'run_id' => $context->runId(),
                                                                        'checksum_before' => $entry['record_checksum'], 'checksum_after' => (string)$entry['checksum'] ) ) );
    }

    /**
     * Writes the end of the run: its row, the status mirror, the event migration/run/after and the audit event.
     *
     * @param array $report
     */
    protected function finishRun( array &$report )
    {
        $context = $this->context;
        $failed = $report['summary']['failed'] > 0 || $report['exit'] !== expUpgrade::EXIT_OK;
        if ( $failed && $report['exit'] === expUpgrade::EXIT_OK )
            $report['exit'] = expUpgrade::EXIT_FAILED;
        $state = $failed ? expMigration::FAILED : expMigration::DONE;
        $message = $failed ? implode( '; ', $report['errors'] ) : $report['summary']['applied'] . ' applied';
        try
        {
            $context->writeRun( $this->runRow( $state, $report['started_at'], time(), $message ) );
            $context->writeStatus();
        }
        catch ( Throwable $e )
        {
            $report['warnings'][] = 'the record of the run could not be written: ' . $e->getMessage();
        }
        $context->log( $failed ? 'error' : 'info', 'run ' . $context->runId() . ' ' . $state . ': ' . $report['summary']['applied'] . ' applied, '
                                                  . $report['summary']['failed'] . ' failed' );
        $context->audit( 'system.migration.run', array( 'object' => array( 'type' => 'migration_run', 'id' => $context->runId() ), 'verb' => 'run',
                                                        'result' => $failed ? 'failure' : 'success',
                                                        'x' => array( 'applied' => $report['summary']['applied'], 'failed' => $report['summary']['failed'],
                                                                      'engine' => $context->engine(), 'environment' => $context->environment() ) ) );
        $context->notify( 'migration/run/after', array( $context->runId(), $report, $context ) );
    }

    /**
     * @param string $state
     * @param int $started
     * @param int $finished
     * @param string $message
     * @return array a row of exp_migration_run
     */
    protected function runRow( $state, $started, $finished, $message )
    {
        $context = $this->context;
        return array( 'run_id' => $context->runId(), 'state' => $state, 'started_at' => $started, 'finished_at' => $finished,
                      'run_by' => $context->runBy(), 'environment' => $context->environment(), 'log_file' => $context->logFile(),
                      'message' => $message );
    }

    /**
     * Whether up()/down() of a migration run in a transaction.
     *
     * @param expMigration $migration
     * @return bool
     */
    protected function useTransaction( expMigration $migration )
    {
        if ( $this->context->transactionMode() === expMigrationContext::TRANSACTION_NONE )
            return false;
        return !method_exists( $migration, 'transactional' ) || $migration->transactional();
    }

    /**
     * The report entry of a migration with its state.
     *
     * @param expMigration $migration
     * @return array
     */
    protected function entry( expMigration $migration )
    {
        $context = $this->context;
        $id = (string)$migration->id();
        $source = (string)$migration->source();
        $checksum = method_exists( $migration, 'checksum' ) ? $migration->checksum() : null;
        $file = method_exists( $migration, 'file' ) ? (string)$migration->file() : '';
        $root = rtrim( $context->root(), '/' ) . '/';
        if ( $file !== '' && strpos( $file, $root ) === 0 )
            $file = substr( $file, strlen( $root ) );
        $record = $context->record( $source, $id );
        $entry = array(
            'id' => $id,
            'source' => $source,
            'description' => (string)$migration->description(),
            'file' => $file,
            'checksum' => $checksum,
            'record_checksum' => $record ? (string)$record['checksum'] : '',
            'changed' => false,
            'state' => expMigration::UNKNOWN,
            'detail' => '',
            'action' => 'none',
            'maintenance' => $migration->maintenance() ? 'asked' : 'no',
            'transaction' => '',
            'plan' => array(),
            'messages' => array(),
            'duration_ms' => 0,
            'record' => $record ? array( 'state' => $record['state'], 'run_id' => $record['run_id'],
                                         'finished_at' => $record['finished_at'] ? date( 'c', $record['finished_at'] ) : null ) : null,
        );
        $engines = (array)$migration->engines();
        if ( !in_array( '*', $engines, true ) && !in_array( $context->engine(), $engines, true ) )
        {
            $entry['state'] = expMigration::NOT_NEEDED;
            $entry['detail'] = 'not for ' . $context->engine();
            return $entry;
        }
        if ( !self::appliesTo( (array)$migration->environments(), $context->environment() ) )
        {
            $entry['state'] = expMigration::NOT_NEEDED;
            $entry['detail'] = 'not for the environment ' . ( $context->environment() !== '' ? $context->environment() : '(none)' );
            return $entry;
        }
        try
        {
            $entry['state'] = (string)$migration->state( $context );
        }
        catch ( Throwable $e )
        {
            $entry['state'] = expMigration::UNKNOWN;
            $entry['detail'] = 'the state cannot be told: ' . $e->getMessage();
        }
        $entry['changed'] = $entry['state'] === expMigration::DONE && $record && $record['state'] === expMigration::DONE
                            && $record['checksum'] !== '' && $checksum !== null && $record['checksum'] !== $checksum;
        return $entry;
    }

    /**
     * Whether a migration with these environments runs in an environment (expMigrationDefinition::appliesTo()).
     * Pure.
     *
     * @param string[] $environments
     * @param string $environment
     * @return bool
     */
    public static function appliesTo( array $environments, $environment )
    {
        $include = array();
        foreach ( $environments as $item )
        {
            $item = (string)$item;
            if ( $item !== '' && $item[0] === '!' )
            {
                if ( substr( $item, 1 ) === (string)$environment )
                    return false;
            }
            else if ( $item !== '' )
            {
                $include[] = $item;
            }
        }
        return !$include || in_array( (string)$environment, $include, true );
    }

    /**
     * @param string $mode
     * @return array an empty report
     */
    protected function report( $mode )
    {
        $context = $this->context;
        return array(
            'schema' => self::REPORT,
            'mode' => $mode,
            'run_id' => $context->runId(),
            'environment' => $context->environment(),
            'engine' => $context->engine(),
            'transaction' => $context->transactionMode(),
            'user' => '',
            'started_at' => 0,
            'migrations' => array(),
            'summary' => array(),
            'errors' => array(),
            'warnings' => array(),
            'exit' => expUpgrade::EXIT_OK,
        );
    }

    /**
     * Counts the states and actions, and sets the exit code of a run with a failure.
     *
     * @param array $report
     */
    protected function summarize( array &$report )
    {
        // states (done, pending, not-needed, failed, unknown) and actions (applied ...); "failed" is the state
        $states = array( expMigration::DONE, expMigration::PENDING, expMigration::NOT_NEEDED, expMigration::FAILED, expMigration::UNKNOWN );
        $actions = array( 'applied', 'would-apply', 'not-run', 'blocked', 'refused', 'accepted', 'would-accept', 'undone', 'would-undo' );
        $summary = array( 'total' => count( $report['migrations'] ) ) + array_fill_keys( array_merge( $states, $actions ), 0 );
        foreach ( $report['migrations'] as $entry )
        {
            if ( in_array( $entry['state'], $states, true ) )
                $summary[$entry['state']]++;
            if ( in_array( $entry['action'], $actions, true ) )
                $summary[$entry['action']]++;
        }
        $report['summary'] = $summary;
        if ( $summary[expMigration::FAILED] > 0 && $report['exit'] === expUpgrade::EXIT_OK && $report['mode'] === 'apply' )
            $report['exit'] = expUpgrade::EXIT_FAILED;
        if ( $report['errors'] && $report['exit'] === expUpgrade::EXIT_OK )
            $report['exit'] = expUpgrade::EXIT_FAILED;
    }
}
