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
 * With a caption flow (option flow, expMigrationRunFlow; migrate() builds it from migration.ini): before the first
 * migration the code trees are checked and the caption migration-pre is taken; after a failure the state is captioned
 * (migration-failure) and the pre caption restored; after the run the caption migration-post is taken (a success) and
 * the record is committed, tagged and pushed in the migrations repository.
 *
 * The kernel's upgrade steps (exp:upgrade) are migrations of the source "kernel" (expMigrationUpgradeSource) and come
 * first (installation()): migrate() applies the pending steps of the stages database and scripts before the site's
 * migrations, in the same run, under the same lock and caption. A pending step of the stages code, libraries and
 * finish is not run (action "deferred", with a warning that names the exp:upgrade command), and a migration that
 * requires it waits. Upgrade steps have no checksum guard and no transaction (expMigrationUpgradeStep).
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
 * The progress callback (option progress, a callable( string $event, array $data )) follows a run that writes as it
 * goes, for a page that shows it live (expMigrationProgress writes the events to a file the administration reads):
 *   run        run_id, mode (apply, rollback), total (the migrations to apply or undo), ids
 *   phase      name: caption-pre (the code check and the caption before), after-migrations (the restore after a
 *              failure), caption-post (the caption after, the commit, tag and push)
 *   step       id, index (from 1), total
 *   step-done  id, index, total, state, action, duration_ms
 *   end        exit, summary
 * A callback that throws is ignored: the run never depends on who watches it.
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
     *                       'stop-on-failure' => bool (default true),
     *                       'flow' => expMigrationRunFlow|null (captions, restore on failure, commit, tag, push),
     *                       'plan' => bool (default true; false: a dry run lists no plan(), as the status does),
     *                       'progress' => callable|null (see the class),
     *                       'max-migrations' => int (apply at most this many migrations, 0: all; the rest are postponed),
     *                       'max-time' => int (seconds: start no further migration after this, 0: no limit),
     *                       'throttle' => float (seconds of pause between two migrations),
     *                       'sleep' => callable|null (function( float $seconds ), a test's), 'clock' => callable|null
     *                       (function(): float, a test's)
     */
    public function __construct( expMigrationContext $context, array $options = array() )
    {
        $this->context = $context;
        $this->options = $options + array( 'force' => false, 'reason' => '', 'stop-on-failure' => true, 'flow' => null, 'plan' => true, 'progress' => null,
                                            'max-migrations' => 0, 'max-time' => 0, 'throttle' => 0.0, 'sleep' => null, 'clock' => null );
    }

    /** @return expMigrationRunFlow|null */
    public function flow()
    {
        return $this->options['flow'] instanceof expMigrationRunFlow ? $this->options['flow'] : null;
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
        $setting = function ( $name ) use ( $ini ) {
            return $ini->hasVariable( 'RunSettings', $name ) ? trim( (string)$ini->variable( 'RunSettings', $name ) ) : '';
        };
        foreach ( array( 'max-migrations' => 'MaxMigrations', 'max-time' => 'MaxTime' ) as $option => $name )
            $options[$option] = max( 0, (int)( isset( $options[$option] ) && $options[$option] !== '' && $options[$option] !== null ? $options[$option] : $setting( $name ) ) );
        $options['throttle'] = self::throttleSeconds( isset( $options['throttle'] ) && $options['throttle'] !== '' && $options['throttle'] !== null
                                                      ? $options['throttle'] : $setting( 'Throttle' ) );
        $options['nice'] = self::niceLevel( isset( $options['nice'] ) && $options['nice'] !== '' && $options['nice'] !== null ? $options['nice'] : $setting( 'Nice' ) );
        return $options;
    }

    /**
     * A pause in seconds from a setting or an option: a number (0.5, 2), at most 60; anything else is 0. Pure.
     *
     * @param mixed $value
     * @return float
     */
    public static function throttleSeconds( $value )
    {
        if ( !is_numeric( $value ) )
            return 0.0;
        return min( 60.0, max( 0.0, (float)$value ) );
    }

    /**
     * The nice level of a run from a setting or an option: 0 to 19; anything else is 0 (unchanged). Pure.
     *
     * @param mixed $value
     * @return int
     */
    public static function niceLevel( $value )
    {
        if ( !is_numeric( $value ) || (string)(int)$value !== trim( (string)$value ) )
            return 0;
        return min( 19, max( 0, (int)$value ) );
    }

    /**
     * Lowers the priority of this process for a run that applies, on the command line only (a web request's
     * worker is shared and stays as it is): proc_nice() by the level, once per process.
     *
     * @param int $level niceLevel()
     * @return bool whether the priority was lowered now
     */
    public static function applyNice( $level )
    {
        static $done = false;
        if ( $done || (int)$level < 1 || PHP_SAPI !== 'cli' || !function_exists( 'proc_nice' ) )
            return false;
        $done = true;
        return @proc_nice( (int)$level );
    }

    /**
     * Finds, orders, loads and runs the installation's migrations on its database (the entry point the console
     * commands and the administration build on). A dry run unless $options['apply'].
     *
     * @param array $options apply, force, reason, login, environment, only (migration ids), path (files or folders,
     *                       select()), migrations (expMigration[] in their order instead of the installation's: a
     *                       test's), the options of expMigrationContext and those of
     *                       expMigrationRunFlow::fromSettings() (no-caption, no-restore, no-commit, no-push, no-tag,
     *                       include-uncommitted), progress (a callable, see the class), upgrade-source and locator
     *                       (installation())
     * @return array the report; the problems of finding and loading are its errors
     */
    public static function migrate( array $options = array() )
    {
        if ( !isset( $options['repository'] ) )
            $options['repository'] = expMigrationRepository::fromSettings( $options )->directory();
        // batches() of PHP migrations pause as long as the run does between migrations
        $options['throttle'] = self::throttleSeconds( isset( $options['throttle'] ) && $options['throttle'] !== '' && $options['throttle'] !== null
                                                      ? $options['throttle'] : self::settingsOptions( array( 'stop-on-failure' => true ) )['throttle'] );
        $context = new expMigrationContext( isset( $options['db'] ) ? $options['db'] : null, !empty( $options['apply'] ), $options );
        $errors = array();
        $warnings = array();
        $migrations = isset( $options['migrations'] ) ? array_values( (array)$options['migrations'] ) : self::installation( $context->environment(), $errors, $warnings, $options );
        $only = isset( $options['only'] ) ? expUpgrade::listOption( $options['only'] ) : array();
        $paths = isset( $options['path'] ) ? expUpgrade::listOption( $options['path'] ) : array();
        if ( $only || $paths )
        {
            $all = $migrations;
            $migrations = self::select( $all, $only, $paths, $context->root(), $errors );
            foreach ( self::unselectedRequirements( $migrations, $all ) as $id => $deps )
                foreach ( $deps as $dep )
                {
                    try
                    {
                        $state = (string)$dep->state( $context );
                    }
                    catch ( Throwable $e )
                    {
                        $state = expMigration::UNKNOWN;
                    }
                    if ( !in_array( $state, array( expMigration::DONE, expMigration::NOT_NEEDED ), true ) )
                        $errors[] = "$id requires " . $dep->id() . ", which is $state and not selected: add it to --only or --path";
                }
        }
        $runnerOptions = self::settingsOptions( array_intersect_key( $options, array_flip( array( 'force', 'reason', 'stop-on-failure', 'progress', 'max-migrations',
                                                                                                 'max-time', 'throttle', 'nice', 'sleep', 'clock' ) ) ) );
        $runnerOptions['flow'] = expMigrationRunFlow::fromSettings( $context, $options );
        if ( !empty( $options['apply'] ) && empty( $options['migrations'] ) )
            self::applyNice( $runnerOptions['nice'] );
        $runner = new self( $context, $runnerOptions );
        if ( $errors )
        {
            // nothing runs while a migration cannot be found, ordered or loaded: the order would not be the one written
            $context = new expMigrationContext( $context->db(), false, $options + array( 'run-id' => $context->runId() ) );
            $report = ( new self( $context ) )->run( $migrations );
            $report['errors'] = array_merge( $errors, $report['errors'] );
            $report['warnings'] = array_merge( $warnings, $report['warnings'] );
            $report['exit'] = expUpgrade::EXIT_FAILED;
            return $report;
        }
        $report = $runner->run( $migrations );
        $report['warnings'] = array_merge( $warnings, $report['warnings'] );
        return $report;
    }

    /**
     * Every migration of the installation in the order a run takes them: the kernel's upgrade steps first
     * (expMigrationUpgradeSource, in exp:upgrade's order), then the migrations of the site, the active extensions and
     * the packages (migrations()). A site migration may require a step by its id.
     *
     * @param string|null $environment null: the installation's
     * @param string[] $errors problems of finding and loading the migrations are added here
     * @param string[] $warnings problems of the upgrade steps' registry are added here (exp:upgrade reports them as
     *                           warnings too: a step whose class is missing is left out, the rest runs)
     * @param array $options upgrade-source (an expMigrationUpgradeSource), locator (an expMigrationLocator)
     * @return expMigration[]
     */
    public static function installation( $environment = null, array &$errors = array(), array &$warnings = array(), array $options = array() )
    {
        $source = isset( $options['upgrade-source'] ) ? $options['upgrade-source'] : expMigrationUpgradeSource::fromSettings();
        foreach ( $source->errors() as $error )
            $warnings[] = 'upgrade step: ' . $error;
        $site = self::migrations( $environment, $errors, isset( $options['locator'] ) ? $options['locator'] : null, $source->ids() );
        return expMigrationUpgradeSource::merge( $source->migrations(), $site, $errors );
    }

    /**
     * The migrations of a selection, in their order: those whose id is in $only, and those whose file is one of $paths
     * or inside one of them (a path relative to the installation's root or absolute). An id or a path that selects
     * nothing is an error. Pure apart from resolving the paths.
     *
     * @param expMigration[] $migrations in their order
     * @param string[] $only migration ids
     * @param string[] $paths files or folders
     * @param string $root the installation's root
     * @param string[] $errors problems are added here
     * @return expMigration[]
     */
    public static function select( array $migrations, array $only, array $paths, $root, array &$errors = array() )
    {
        $root = rtrim( (string)$root, '/' );
        $normalize = function ( $path ) use ( $root ) {
            $path = rtrim( (string)$path, '/' );
            if ( $path === '' )
                return '';
            if ( $path[0] !== '/' )
                $path = $root . '/' . ( strpos( $path, './' ) === 0 ? substr( $path, 2 ) : $path );
            $real = realpath( $path );
            return $real !== false ? $real : $path;
        };
        $wanted = array();
        foreach ( $paths as $path )
            if ( ( $n = $normalize( $path ) ) !== '' )
                $wanted[$path] = $n;
        $usedIds = array();
        $usedPaths = array();
        $selected = array();
        foreach ( $migrations as $migration )
        {
            $take = false;
            $id = (string)$migration->id();
            if ( in_array( $id, $only, true ) )
            {
                $take = true;
                $usedIds[$id] = true;
            }
            $file = method_exists( $migration, 'file' ) ? (string)$migration->file() : '';
            if ( $file !== '' && $wanted )
            {
                $file = $normalize( $file );
                foreach ( $wanted as $given => $path )
                    if ( $file === $path || strpos( $file, $path . '/' ) === 0 )
                    {
                        $take = true;
                        $usedPaths[$given] = true;
                    }
            }
            if ( $take )
                $selected[] = $migration;
        }
        foreach ( $only as $id )
            if ( !isset( $usedIds[$id] ) )
                $errors[] = "no migration $id (--only)";
        foreach ( array_keys( $wanted ) as $given )
            if ( !isset( $usedPaths[$given] ) )
                $errors[] = "no migration in $given (--path): a path must be a migration file or a folder in a migrations directory";
        return $selected;
    }

    /**
     * The requirements of selected migrations that are not selected themselves. Pure.
     *
     * @param expMigration[] $selected
     * @param expMigration[] $all
     * @return array id => expMigration[] the requirements left out
     */
    public static function unselectedRequirements( array $selected, array $all )
    {
        $byId = array();
        foreach ( $all as $migration )
            $byId[(string)$migration->id()] = $migration;
        $in = array();
        foreach ( $selected as $migration )
            $in[(string)$migration->id()] = true;
        $out = array();
        foreach ( $selected as $migration )
            foreach ( (array)$migration->requires() as $dep )
                if ( !isset( $in[$dep] ) && isset( $byId[$dep] ) )
                    $out[(string)$migration->id()][] = $byId[$dep];
        return $out;
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
     * @param string[] $kernel the ids of the kernel's upgrade steps, which a migration may require (they run first)
     * @return expMigration[]
     */
    public static function migrations( $environment = null, array &$errors = array(), $locator = null, array $kernel = array() )
    {
        $locator = $locator ?: expMigrationLocator::fromSettings();
        $definitions = $locator->find();
        foreach ( $locator->errors() as $error )
            $errors[] = $error;
        $extensions = class_exists( 'eZExtension' ) ? eZExtension::activeExtensions() : null;
        $graph = new expMigrationGraph( $definitions, array( 'environment' => $environment === null ? expMigrationRecord::environment() : $environment,
                                                             'extensions' => $extensions, 'kernel' => $kernel ) );
        foreach ( $graph->errors() as $error )
            $errors[] = $error;
        return self::load( $graph->migrations(), $errors );
    }

    /**
     * The migrations of definitions, in their order, loaded from their files (expMigrationPhp, expMigrationYaml, and
     * expMigrationSql and expMigrationIni, which make the YAML migration of their steps); what cannot be loaded is
     * listed in $errors.
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
                if ( $definition->type() === 'yml' )
                    $migrations[] = expMigrationYaml::load( $definition );
                else if ( $definition->type() === 'sql' )
                    $migrations[] = expMigrationSql::load( $definition );
                else if ( $definition->type() === 'ini' )
                    $migrations[] = expMigrationIni::load( $definition );
                else
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
     * The plan, and with a context that writes the run. What the migrations change is not recorded by a recording
     * of changes that runs at the same time (expMigrationRecorder): it is a migration already.
     *
     * @param expMigration[] $migrations in the order they run (expMigrationGraph)
     * @return array the report
     */
    public function run( array $migrations )
    {
        $self = $this;
        return expMigrationRecorder::paused( function () use ( $self, $migrations ) {
            return $self->runUnrecorded( $migrations );
        } );
    }

    /**
     * run() without the pause of the recording of changes.
     *
     * @param expMigration[] $migrations
     * @return array the report
     */
    public function runUnrecorded( array $migrations )
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
            $deferred = array();
            foreach ( $report['migrations'] as $i => $entry )
                if ( in_array( $entry['state'], array( expMigration::PENDING, expMigration::FAILED ), true ) )
                {
                    // an upgrade step of the stages code, libraries or finish: listed, run by exp:upgrade
                    $migration = $byId[$entry['id']];
                    if ( method_exists( $migration, 'runnable' ) && !$migration->runnable() )
                    {
                        $report['migrations'][$i]['action'] = 'deferred';
                        $report['warnings'][] = $entry['id'] . ': ' . $entry['state'] . ', not run by a migration run: ' . $migration->notRunReason();
                        $deferred[$entry['id']] = 'deferred';
                        continue;
                    }
                    $pending[] = $i;
                }
                else if ( $entry['state'] === expMigration::UNKNOWN )
                    $report['warnings'][] = $entry['id'] . ': skipped, its state cannot be told: ' . $entry['detail'];

            if ( !$apply )
            {
                foreach ( $pending as $i )
                {
                    $entry =& $report['migrations'][$i];
                    $entry['action'] = 'would-apply';
                    if ( !$this->options['plan'] )
                    {
                        unset( $entry );
                        continue;
                    }
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
                if ( $pending && $this->flow() )
                    $this->flow()->plan( $report );
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

            $ids = array();
            foreach ( $pending as $i )
                $ids[] = $report['migrations'][$i]['id'];
            $flow = $this->flow();
            // the caption flow: the code trees checked, the caption taken (a run without one goes on with a warning)
            $this->progress( 'run', array( 'run_id' => $context->runId(), 'mode' => 'apply', 'total' => count( $ids ), 'ids' => $ids ) );
            if ( $flow )
                $this->progress( 'phase', array( 'name' => 'caption-pre' ) );
            if ( $flow && !$flow->before( $report, $ids ) )
            {
                if ( $report['exit'] === expUpgrade::EXIT_OK )
                    $report['exit'] = expUpgrade::EXIT_FAILED;
                $this->summarize( $report );
                $this->progress( 'end', array( 'exit' => $report['exit'], 'summary' => $report['summary'] ) );
                if ( !$heldBefore )
                    $lock->release();
                return $report;
            }

            $started = time();
            $report['started_at'] = $started;
            $runStarted = true;
            $context->writeRun( $this->runRow( 'running', $started, 0, '' ) );
            $context->log( 'info', 'run ' . $context->runId() . ' by ' . $context->userLogin() . ' on ' . $context->engine()
                                   . ( $context->environment() !== '' ? ' in ' . $context->environment() : '' ) . ': ' . count( $pending ) . ' to apply'
                                   . ( $context->runField( 'caption_pre' ) !== '' ? ', caption ' . $context->runField( 'caption_pre' ) : '' ) );
            $context->notify( 'migration/run/before', array( $context->runId(), $ids, $context ) );
            $context->becomeUser();
            try
            {
                if ( $accept )
                    foreach ( $changed as $i )
                        $this->acceptChange( $report['migrations'][$i], $byId[$report['migrations'][$i]['id']] );

                $outcome = $deferred;
                $stop = false;
                $total = count( $pending );
                $index = 0;
                $applied = 0;
                $postponed = '';
                $limit = max( 0, (int)$this->options['max-migrations'] );
                $maxTime = max( 0, (int)$this->options['max-time'] );
                $runStart = $this->now();
                foreach ( $pending as $i )
                {
                    $entry =& $report['migrations'][$i];
                    $migration = $byId[$entry['id']];
                    $index++;
                    // a long run in parts: the batch is full or its time is up; the rest stay pending for the next run
                    if ( !$stop && $postponed === '' && $applied > 0 )
                    {
                        if ( $limit > 0 && $applied >= $limit )
                            $postponed = "the limit of $limit migrations per run";
                        else if ( $maxTime > 0 && $this->now() - $runStart >= $maxTime )
                            $postponed = "the time limit of $maxTime seconds";
                    }
                    if ( $postponed !== '' )
                    {
                        $entry['action'] = 'postponed';
                        $entry['messages'][] = 'postponed: ' . $postponed;
                        $outcome[$entry['id']] = 'postponed';
                        $this->stepDone( $entry, $index, $total );
                        unset( $entry );
                        continue;
                    }
                    if ( $stop )
                    {
                        $entry['action'] = 'not-run';
                        $this->stepDone( $entry, $index, $total );
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
                        $this->stepDone( $entry, $index, $total );
                        unset( $entry );
                        continue;
                    }
                    if ( $applied > 0 && $this->options['throttle'] > 0 )
                        $this->pause( (float)$this->options['throttle'] );
                    $this->progress( 'step', array( 'id' => $entry['id'], 'index' => $index, 'total' => $total ) );
                    $this->apply( $entry, $migration );
                    $applied++;
                    $this->stepDone( $entry, $index, $total );
                    $outcome[$entry['id']] = $entry['state'];
                    if ( $entry['state'] === expMigration::FAILED && !empty( $this->options['stop-on-failure'] ) )
                        $stop = true;
                    unset( $entry );
                }
                if ( $postponed !== '' )
                {
                    $left = 0;
                    foreach ( $report['migrations'] as $entry )
                        if ( $entry['action'] === 'postponed' )
                            $left++;
                    $report['warnings'][] = "$left migrations postponed by $postponed: the next run (./console exp:migration:migrate --apply) goes on with them";
                    $context->log( 'info', "$left migrations postponed by $postponed" );
                }
            }
            finally
            {
                $context->restoreUser();
            }
            // after a failure: the pre caption restored, before the run's record is written
            if ( $flow )
            {
                $this->progress( 'phase', array( 'name' => 'after-migrations' ) );
                $flow->afterMigrations( $report );
            }
        }
        catch ( Throwable $e )
        {
            $report['errors'][] = 'internal error: ' . $e->getMessage();
            $report['exit'] = expUpgrade::EXIT_INTERNAL;
        }

        $this->summarize( $report );
        if ( $runStarted )
        {
            foreach ( $report['migrations'] as $entry )
                if ( $entry['source'] === expMigrationUpgradeStep::SOURCE && $entry['action'] === 'applied' )
                {
                    // exp:upgrade's finish stage (deploy, caches, smoke check) is not part of a migration run
                    $report['warnings'][] = 'kernel upgrade steps were applied: ./console exp:upgrade:finish --apply makes them live (deploy, caches, smoke check)';
                    break;
                }
            // the caches the applied migrations touched, and the search index of the objects they changed
            expMigrationAfterRun::finish( $report, $byId, $this->context );
            $this->finishRun( $report );
            // the caption after the run, then the commit, tag and push of the migrations repository
            $flow = $this->flow();
            if ( $flow )
            {
                $this->progress( 'phase', array( 'name' => 'caption-post' ) );
                try
                {
                    $flow->afterRun( $report );
                }
                catch ( Throwable $e )
                {
                    $report['warnings'][] = 'after the run: ' . $e->getMessage();
                }
            }
            $this->progress( 'end', array( 'exit' => $report['exit'], 'summary' => $report['summary'] ) );
        }
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
        $self = $this;
        return expMigrationRecorder::paused( function () use ( $self, $migrations ) {
            return $self->rollbackUnrecorded( $migrations );
        } );
    }

    /**
     * rollback() without the pause of the recording of changes.
     *
     * @param expMigration[] $migrations
     * @return array the report
     */
    public function rollbackUnrecorded( array $migrations )
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
            $total = count( $report['migrations'] );
            $ids = array();
            foreach ( $report['migrations'] as $undo )
                $ids[] = $undo['id'];
            $this->progress( 'run', array( 'run_id' => $context->runId(), 'mode' => 'rollback', 'total' => $total, 'ids' => $ids ) );
            try
            {
                $stop = false;
                $reversed = array_reverse( $migrations );
                foreach ( $report['migrations'] as $i => &$entry )
                {
                    if ( $stop )
                    {
                        $entry['action'] = 'not-run';
                        $this->stepDone( $entry, $i + 1, $total );
                        continue;
                    }
                    $migration = $reversed[$i];
                    $this->progress( 'step', array( 'id' => $entry['id'], 'index' => $i + 1, 'total' => $total ) );
                    $context->notify( 'migration/step/before', array( $context->runId(), $migration, $context ) );
                    $start = microtime( true );
                    $useTransaction = $this->useTransaction( $migration );
                    self::beginEvents( $migration );
                    try
                    {
                        if ( $useTransaction )
                            $context->begin();
                        $entry['messages'] = array_values( array_map( 'strval', (array)$migration->down( $context ) ) );
                        if ( $useTransaction )
                            $context->commit();
                        self::endEvents( true );
                        $context->removeRecord( $entry['source'], $entry['id'] );
                        $entry['state'] = expMigration::PENDING;
                        $entry['action'] = 'undone';
                    }
                    catch ( Throwable $e )
                    {
                        if ( $useTransaction )
                            $context->rollback();
                        self::endEvents( !$useTransaction );
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
                    $this->stepDone( $entry, $i + 1, $total );
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
        if ( $apply && $lock )
        {
            // the caches the undone migrations touched, and the objects their down steps changed
            $byId = array();
            foreach ( $migrations as $migration )
                $byId[(string)$migration->id()] = $migration;
            expMigrationAfterRun::finish( $report, $byId, $context );
            $this->progress( 'end', array( 'exit' => $report['exit'], 'summary' => $report['summary'] ) );
        }
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
        self::beginEvents( $migration );
        $context->takeFileChanges();
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
            self::endEvents( true );
            $entry['state'] = expMigration::DONE;
            $entry['action'] = 'applied';
            // done: where a long migration got to is no longer needed
            try
            {
                expMigrationCheckpoint::of( $id, $context )->clear();
            }
            catch ( InvalidArgumentException $e )
            {
            }
        }
        catch ( Throwable $e )
        {
            self::endEvents( !$useTransaction );
            if ( $useTransaction )
            {
                $context->rollback();
                if ( $entry['transaction'] === expMigrationContext::TRANSACTION_DATA )
                    $entry['messages'][] = 'rolled back what could be: ' . $context->engine() . ' keeps schema changes made before the failure';
            }
            $entry['state'] = expMigration::FAILED;
            $entry['action'] = 'failed';
            $entry['messages'][] = $e->getMessage();
            // the code and design files its steps wrote: a transaction does not undo them
            foreach ( $context->takeFileChanges() as $change )
            {
                try
                {
                    $entry['messages'][] = expMigrationFiles::restore( $context->root(), $change );
                }
                catch ( Throwable $restoreError )
                {
                    $entry['messages'][] = 'not put back: ' . $restoreError->getMessage();
                }
            }
        }
        $context->takeFileChanges();
        $entry['duration_ms'] = (int)round( ( microtime( true ) - $start ) * 1000 );

        if ( $maintenance === 'enabled' || $maintenance === 'adopted' )
            $context->maintenanceEnd();

        $written = $context->writeRecord( array(
            'source' => $entry['source'],
            'migration_id' => $id,
            'stage' => $entry['stage'],
            'version' => method_exists( $migration, 'version' ) ? (string)$migration->version() : '',
            'file_path' => $entry['file'],
            'checksum' => (string)$entry['checksum'],
            'state' => $entry['state'],
            'environment' => $context->environment(),
            'run_id' => $context->runId(),
            'started_at' => $startedAt,
            'finished_at' => time(),
            'duration_ms' => $entry['duration_ms'],
            'run_by' => $context->runBy(),
            'caption_id' => $context->runField( 'caption_pre' ),
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
        return array_merge( array( 'run_id' => $context->runId(), 'state' => $state, 'started_at' => $started, 'finished_at' => $finished,
                                   'run_by' => $context->runBy(), 'environment' => $context->environment(), 'log_file' => $context->logFile(),
                                   'message' => $message ), $context->runFields() );
    }

    /**
     * Tells the progress callback (option progress) an event; a callback that throws is ignored.
     *
     * @param string $event
     * @param array $data
     */
    protected function progress( $event, array $data = array() )
    {
        if ( !is_callable( $this->options['progress'] ) )
            return;
        try
        {
            call_user_func( $this->options['progress'], (string)$event, $data );
        }
        catch ( Throwable $e )
        {
        }
    }

    /**
     * Now, in seconds (option clock, a test's, or microtime()).
     *
     * @return float
     */
    protected function now()
    {
        return is_callable( $this->options['clock'] ) ? (float)call_user_func( $this->options['clock'] ) : microtime( true );
    }

    /**
     * Pauses between two migrations (option throttle), through option sleep when a test gives one.
     *
     * @param float $seconds
     */
    protected function pause( $seconds )
    {
        if ( is_callable( $this->options['sleep'] ) )
            call_user_func( $this->options['sleep'], (float)$seconds );
        else
            usleep( (int)round( (float)$seconds * 1000000 ) );
    }

    /**
     * The event step-done of an entry.
     *
     * @param array $entry
     * @param int $index from 1
     * @param int $total
     */
    protected function stepDone( array $entry, $index, $total )
    {
        $this->progress( 'step-done', array( 'id' => $entry['id'], 'index' => (int)$index, 'total' => (int)$total, 'state' => $entry['state'],
                                             'action' => $entry['action'], 'duration_ms' => (int)$entry['duration_ms'] ) );
    }

    /**
     * The event journal of the GraphQL API learns that a migration starts (expGraphQLEventListener::beginMigration()).
     *
     * @param expMigration $migration
     */
    protected static function beginEvents( expMigration $migration )
    {
        if ( class_exists( 'expGraphQLEventListener' ) )
            expGraphQLEventListener::beginMigration( (string)$migration->id(), self::eventsMode( $migration ) );
    }

    /**
     * @param bool $kept the changes of the migration stay (false: rolled back)
     */
    protected static function endEvents( $kept )
    {
        if ( class_exists( 'expGraphQLEventListener' ) )
            expGraphQLEventListener::endMigration( $kept );
    }

    /**
     * What a migration's content changes put into the event journal of the GraphQL API: its header events: (each,
     * summary, none); each for a migration without a header (an upgrade step). Pure.
     *
     * @param expMigration $migration
     * @return string
     */
    public static function eventsMode( expMigration $migration )
    {
        $definition = method_exists( $migration, 'definition' ) ? $migration->definition() : null;
        return $definition instanceof expMigrationDefinition ? $definition->events() : 'each';
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
            'stage' => method_exists( $migration, 'stage' ) ? (string)$migration->stage() : 'migration',
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
            if ( method_exists( $migration, 'detail' ) )
                $entry['detail'] = (string)$migration->detail();
        }
        catch ( Throwable $e )
        {
            $entry['state'] = expMigration::UNKNOWN;
            $entry['detail'] = 'the state cannot be told: ' . $e->getMessage();
        }
        // no guard for a migration whose state is detected (an upgrade step: its class changes with the kernel)
        $guarded = !method_exists( $migration, 'guarded' ) || $migration->guarded();
        $entry['changed'] = $guarded && $entry['state'] === expMigration::DONE && $record && $record['state'] === expMigration::DONE
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
        $actions = array( 'applied', 'would-apply', 'not-run', 'blocked', 'refused', 'accepted', 'would-accept', 'undone', 'would-undo', 'restored', 'simulated',
                          'deferred', 'postponed' );
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
