<?php
/**
 * File containing the shared code of the exp:migration commands.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

/**
 * What every exp:migration command shares: the options, the text and JSON output, the exit codes. The work is in
 * the expMigration* classes (kernel/classes/migration/); these commands only parse, call and print.
 *
 * The names, options, arguments, exit codes and the exp-migration/1 output are a permanent contract (contract() and
 * arguments() are the snapshot tests/tests/kernel/classes/migration/expMigrationCommandContractTest.php compares
 * against): options and keys may be added, never renamed or removed. The exit codes are exp:upgrade's: 0 done,
 * 1 failed, 2 usage, 3 the database unreachable, 4 internal error.
 */
abstract class ExpmigrationBase extends \Exponential\Runnable\Command
{
    /** The options of the commands that select and run migrations. */
    const SELECT_OPTIONS = '[only:*][path:*][environment:][format:][login:]';

    /** The options of the caption flow around a run that writes. */
    const FLOW_OPTIONS = '[no-caption][no-restore][include-uncommitted][no-commit][no-push][no-tag]';

    /** The checksum guard. */
    const FORCE_OPTIONS = '[force][reason:]';

    /** A run started by the administration in the background: its id given, its progress written to a file. */
    const PROGRESS_OPTIONS = '[run-id:][progress:]';

    /** A long run in parts, paced: migrate and resume. */
    const LONG_RUN_OPTIONS = '[max-migrations:][max-time:][throttle:][nice:]';

    /**
     * Every command with its option configuration (permanent: only additions).
     *
     * @return array command => option config
     */
    public static function contract()
    {
        return array(
            'exp:migration:status' => '[only:*][environment:][format:][fail-on-pending][runs:]',
            'exp:migration:migrate' => '[apply][dry-run]' . self::SELECT_OPTIONS . self::FLOW_OPTIONS . self::FORCE_OPTIONS . self::PROGRESS_OPTIONS . self::LONG_RUN_OPTIONS,
            'exp:migration:dry-run' => self::SELECT_OPTIONS . '[diff][simulate]',
            'exp:migration:rollback' => '[apply][dry-run][format:][source:][environment:][login:][expert][confirm:][parts:][backup-dir:][no-safety-caption][force-no-caption][no-verify][progress:]',
            'exp:migration:resume' => '[apply][dry-run][environment:][format:][login:]' . self::FLOW_OPTIONS . self::FORCE_OPTIONS . self::PROGRESS_OPTIONS . self::LONG_RUN_OPTIONS,
            'exp:migration:caption' => '[apply][dry-run][format:][list][label:][backup-dir:]',
            'exp:migration:push' => '[apply][dry-run][format:][limit:]',
            'exp:migration:check' => '[path:*][environment:][format:]',
            'exp:migration:generate' => '[skeleton][source:][description:][author:][type:][apply][dry-run][format:][from-recording:][engine:][from-content:][depth:][max-objects:][from-class:][from-role:][from-section:][from-settings:][siteaccess:][from-diff:][compare:][subtree:][token:][backup-dir:]',
            'exp:migration:record:start' => '[apply][dry-run][format:][site-wide][label:][user:][for:][until:]',
            'exp:migration:record:stop' => '[apply][dry-run][format:][site-wide][user:]',
            'exp:migration:record:status' => '[format:][user:]',
            'exp:migration:record:list' => '[format:][count:]',
            'exp:migration:record:show' => '[format:][plan]',
            'exp:migration:record:discard' => '[apply][dry-run][format:]',
            'exp:migration:deploy' => '[remotes:*][apply][dry-run][transfer-only][only:*][format:][parallel:][timeout:][retries:][staged][continue:][rollback:][login:][run-id:][progress:]',
            'exp:migration:remote:list' => '[remotes:*][format:]',
            'exp:migration:remote:status' => '[remotes:*][format:][parallel:][timeout:][history:]',
            'exp:migration:heal' => '[fix:*][stale-lock-age:][apply][dry-run][format:]',
            'exp:migration:convert' => '[apply][dry-run][format:][source:][author:]',
            'exp:migration:repo:split' => '[apply][dry-run][format:][directory:][branch:][to:][remote:]',
        );
    }

    /**
     * The arguments of the commands that take one (permanent).
     *
     * @return array command => argument config
     */
    public static function arguments()
    {
        return array(
            'exp:migration:rollback' => '[target]',
            'exp:migration:resume' => '[run]',
            'exp:migration:generate' => '[name]',
            'exp:migration:record:stop' => '[recording]',
            'exp:migration:record:show' => '[recording]',
            'exp:migration:record:discard' => '[recording]',
            'exp:migration:convert' => '[file]',
        );
    }

    /** @return array option => help text for every option of the family */
    public static function optionHelp()
    {
        return array(
            'apply' => 'Make the changes (the default is a dry run that lists them and changes nothing)',
            'dry-run' => 'The default, accepted for scripts that pass it: list what would change, change nothing',
            'only' => 'Only these migrations (migration ids, a comma list or repeated)',
            'path' => 'Migration files or folders, relative to the root or absolute (a comma list or repeated): migrate and dry-run take only the migrations in them, check checks them instead of the migration directories',
            'environment' => 'The environment (default the installation\'s: [AdminEnvironment] Level, or EXP_ENV with Level=auto; check: every environment); only the migrations for it count',
            'format' => 'text (default) or json (the machine output exp-migration/1)',
            'login' => 'Run as this user (default: [RunSettings] RunAsUser of migration.ini)',
            'no-caption' => 'No caption before and after the run: a failure cannot be restored automatically',
            'no-restore' => 'After a failure, leave the installation as the failure left it instead of restoring the caption taken before the run',
            'include-uncommitted' => 'Run although settings/, extension/ or design/ have changes that are not committed, and keep them in the caption',
            'no-commit' => 'Do not commit the status file and run log to the migrations repository (no tag and no push either)',
            'no-push' => 'Commit and tag, but do not push',
            'no-tag' => 'Commit and push, but do not tag the run',
            'force' => 'With --reason: accept migrations whose file changed after they were applied (they are not run again)',
            'reason' => 'Why a changed migration is accepted (needed with --force; kept in the record and the audit log)',
            'fail-on-pending' => 'Exit 1 when migrations are pending or failed (for CI)',
            'runs' => 'How many of the latest runs to list (default 10, 0 for none)',
            'diff' => 'With --simulate: what each migration changed in the database (tables, columns, rows)',
            'simulate' => 'Run the migrations in a transaction that is rolled back (PostgreSQL and SQLite; elsewhere the plan is listed)',
            'source' => 'The source: site or extension:<name> (generate: where the file goes; rollback: which migration, when two sources have its id)',
            'expert' => 'Restore a caption that is not the one taken before a run (with --confirm)',
            'confirm' => 'The id of the caption typed again, to restore it with --expert',
            'parts' => 'Only these parts of the caption, a comma list of database, storage, settings, libraries, code (default [CaptionSettings] RestoreParts)',
            'backup-dir' => 'Where the captions made without git_manager are (default var/backup/upgrade)',
            'no-safety-caption' => 'Restore without taking a caption of the current state first; refused on production without --force-no-caption',
            'force-no-caption' => 'With --no-safety-caption on a production installation: restore without a caption all the same',
            'no-verify' => 'Skip the verification after the restore',
            'list' => 'List the captions instead of taking one',
            'label' => 'The label of the caption or of the recording (a caption without one: "manual caption" with the time)',
            'limit' => 'Push at most this many runs (default 20)',
            'skeleton' => 'An empty migration with its header: PHP with plan(), up() and down(), or the kind --type names',
            'description' => 'The description in the header of the new migration',
            'author' => 'The author in the header of the new migration',
            'type' => 'The type of the new migration: php (the default), yml, sql, ini or graphql (a YAML migration with a graphql step to fill in)',
            'engine' => 'With --type=sql: the file of one engine (mysql, pgsql, sqlite, oracle, mongodb) instead of the portable one',
            'from-recording' => 'Generate a YAML migration from a recording of changes (its id)',
            'from-content' => 'Generate a YAML migration of content: the nodes (id, remote:<remote id>, path:<url path>), comma-separated',
            'depth' => 'With --from-content: the levels below each node that go in too (default 0: the node alone)',
            'max-objects' => 'With --from-content, or --from-diff with --subtree: at most this many objects (default 500)',
            'from-class' => 'Generate a YAML migration of content classes (identifiers, comma-separated)',
            'from-role' => 'Generate a YAML migration of roles with their policies (names, comma-separated)',
            'from-section' => 'Generate a YAML migration of sections (identifiers, comma-separated)',
            'from-settings' => 'Generate a YAML migration of settings files of settings/override (site.ini ..., comma-separated)',
            'siteaccess' => 'With --from-settings: the files of this siteaccess instead of settings/override',
            'from-diff' => 'Generate a YAML migration of what changed since a saved state: a caption id, the address of another installation (https://...) or a database dump',
            'compare' => 'With --from-diff: what to compare (classes, roles, sections, states, languages, content, settings; comma-separated, default all but content)',
            'subtree' => 'With --from-diff: compare the content of the subtree of this node (id, remote:<remote id>, path:<url path>)',
            'token' => 'With --from-diff=<address>: the API key of the other installation with the scope migration:read, by secret name or env:VARIABLE',
            'site-wide' => 'The site-wide recording: every change of every user and of the console (without it: one user\'s own changes)',
            'user' => 'The user, by login, whose own changes a session recording keeps (default: [RunSettings] RunAsUser of migration.ini)',
            'for' => 'A window of this many minutes: the recording stops by itself when it ends',
            'until' => 'A window until this time (2026-11-20 18:00, the server\'s time zone): the recording stops by itself then',
            'count' => 'How many recordings to list, the newest first (default 50)',
            'plan' => 'The steps a migration of the recording would have (its events folded per thing changed) instead of every event',
            'run-id' => 'The id of the run to start (default a new one: <date>-<time>-<6 hex digits>); the administration names it before it starts the run',
            'progress' => 'Append the progress of the run to this file, one JSON object per line (what the administration shows while the run goes on)',
            'max-migrations' => 'Apply at most this many migrations; the rest stay pending for the next run (default [RunSettings] MaxMigrations, empty: all)',
            'max-time' => 'Start no further migration after this many seconds; the rest stay pending for the next run (default [RunSettings] MaxTime)',
            'throttle' => 'Pause this many seconds between two migrations and between the batches of a long migration (default [RunSettings] Throttle)',
            'nice' => 'Run at this lower priority, 1 to 19 as nice(1) (default [RunSettings] Nice)',
            'remotes' => 'The remotes: ids or group names of [RemoteSettings] RemoteList[] in migration.ini, or all (a comma list or repeated)',
            'transfer-only' => 'Send and verify the migration files, but do not run them (with --apply; the only mode for an FTP remote without a trigger)',
            'parallel' => 'How many remotes are worked on at the same time (default [RemoteSettings] Parallel)',
            'timeout' => 'Seconds each remote may take for one phase (default [RemoteSettings] Timeout)',
            'retries' => 'How often a connection failure is tried again (default [RemoteSettings] Retries)',
            'history' => 'How many of the latest deploys to list (default 10, 0 for none)',
            'fix' => 'The fixes: stale-lock, paths, modes, caches (a comma list or repeated)',
            'stale-lock-age' => 'Seconds from which a held run lock whose process is gone counts as stale (default 7200)',
            'staged' => 'Deploy stage by stage ([RemoteSettings] StageOrder[] by environment): the first stage now, a gate before each next one (with --apply)',
            'continue' => 'Approve the gate of a staged deploy and deploy its next stage (the deploy id; with --apply)',
            'rollback' => 'Take back every remote that applied this deploy (the deploy id; with --apply)',
            'directory' => 'The migrations folder to split (default the first [MigrationSettings] DirectoryList entry)',
            'branch' => 'The branch of the checkout the split history goes to (default migrations-split; never an existing one)',
            'to' => 'A new or empty folder for the new repository (its branch main); without it only the branch is made',
            'remote' => 'The remote origin of the new repository (a URL or path; it is not pushed)',
        );
    }

    /** @return string the command's name, e.g. exp:migration:status */
    abstract protected function name();

    /**
     * The command's work: parse with begin(), call the expMigration* class, print with finish(). Every command
     * overrides it; this one only starts the script and ends with a usage error.
     */
    public function run()
    {
        $this->begin();
        $this->usage( $this->name() . ' has no work of its own' );
    }

    /**
     * Starts the script: $this->script( array( 'description' => "...", ... ) ) and $this->startup( '<config>',
     * '<arguments>', <help> ), written out literally in each command, so ./console exp:help:examples reads the help
     * text and the options from the code. The option configuration must equal contract() and every help text
     * optionHelp() (expMigrationCommandContractTest checks both).
     *
     * @return array the options
     */
    abstract protected function begin();

    /** The script settings every command shares besides its description. */
    const SCRIPT = array( 'use-session' => false, 'use-modules' => true, 'use-extensions' => true );

    /**
     * The output format; after an error (not text or json) the command ends with exit 2.
     *
     * @param array $options
     * @return string
     */
    protected function format( array $options )
    {
        $format = isset( $options['format'] ) && $options['format'] ? (string)$options['format'] : 'text';
        if ( !in_array( $format, array( 'text', 'json' ), true ) )
            $this->usage( "--format must be text or json, not $format" );
        return $format;
    }

    /**
     * A usage error: the message and exit 2.
     *
     * @param string $message
     */
    protected function usage( $message )
    {
        $this->error( 'FAIL: ' . $message );
        $this->shutdown( \expUpgrade::EXIT_USAGE );
    }

    /**
     * Whether the command writes: --apply; --apply with --dry-run is a usage error.
     *
     * @param array $options
     * @return bool
     */
    protected function apply( array $options )
    {
        $apply = self::applyRequested( $options );
        if ( $apply === null )
            $this->usage( '--apply and --dry-run exclude each other; without --apply nothing is changed' );
        return (bool)$apply;
    }

    /**
     * The first argument, or ''.
     *
     * @param array $options
     * @return string
     */
    protected function argument( array $options )
    {
        return isset( $options['arguments'][0] ) ? trim( (string)$options['arguments'][0] ) : '';
    }

    /**
     * Ends with exit 3 when the database is not reachable.
     *
     * @param string $format
     * @return \eZDBInterface
     */
    protected function database( $format )
    {
        $problem = '';
        try
        {
            $db = \eZDB::instance();
        }
        catch ( \Throwable $e )
        {
            $db = null;
            $problem = ': ' . $e->getMessage();
        }
        if ( !$db || !$db->isConnected() )
            $this->finish( array( 'mode' => 'unreachable', 'errors' => array( 'the database is not reachable' . $problem ), 'warnings' => array(),
                                  'exit' => \expUpgrade::EXIT_UNREACHABLE ), $format );
        return $db;
    }

    /**
     * The options shared by the commands that select migrations, checked; a usage error ends with exit 2.
     *
     * @param array $options
     * @return array environment, login, only, path (as the runner takes them; absent when not given)
     */
    protected function selection( array $options )
    {
        $out = array( 'command' => $this->name() );
        if ( isset( $options['environment'] ) && $options['environment'] !== false && $options['environment'] !== '' )
        {
            $environment = strtolower( trim( (string)$options['environment'] ) );
            if ( !preg_match( '/^[a-z0-9][a-z0-9_.-]{0,49}$/', $environment ) )
                $this->usage( "--environment must be a name of letters, digits, _, . and -, not $environment" );
            $out['environment'] = $environment;
        }
        if ( isset( $options['login'] ) && $options['login'] !== false && $options['login'] !== '' )
            $out['login'] = (string)$options['login'];
        foreach ( array( 'only', 'path' ) as $name )
            if ( isset( $options[$name] ) && $options[$name] !== false )
            {
                $list = \expUpgrade::listOption( $options[$name] );
                if ( $list )
                    $out[$name] = $list;
            }
        return $out;
    }

    /**
     * The options of the caption flow and the checksum guard, checked.
     *
     * @param array $options
     * @return array
     */
    protected function flow( array $options )
    {
        $out = array();
        foreach ( array( 'no-caption', 'no-restore', 'include-uncommitted', 'no-commit', 'no-push', 'no-tag', 'force' ) as $name )
            $out[$name] = !empty( $options[$name] );
        $reason = isset( $options['reason'] ) && $options['reason'] !== false ? trim( (string)$options['reason'] ) : '';
        if ( $reason !== '' && !$out['force'] )
            $this->usage( '--reason goes with --force' );
        if ( $out['force'] && $reason === '' )
            $this->usage( '--force needs a --reason=<text>: why the changed migrations are accepted' );
        $out['reason'] = $reason;
        return $out;
    }

    /**
     * The options --run-id and --progress, checked: the run's id (expMigrationContext option run-id) and the runner's
     * progress callback writing to the file (expMigrationProgress::writer()). A usage error ends with exit 2.
     *
     * @param array $options
     * @return array run-id, progress (absent when not given)
     */
    protected function progress( array $options )
    {
        $out = array();
        if ( isset( $options['run-id'] ) && $options['run-id'] !== false && $options['run-id'] !== '' )
        {
            $runId = (string)$options['run-id'];
            if ( !\expMigrationRecord::validRunId( $runId ) )
                $this->usage( "--run-id must be a run id (<date>-<time>-<6 hex digits>), not $runId" );
            $out['run-id'] = $runId;
        }
        if ( isset( $options['progress'] ) && $options['progress'] !== false && $options['progress'] !== '' )
        {
            $file = (string)$options['progress'];
            if ( !is_dir( dirname( $file ) ) || ( file_exists( $file ) && !is_file( $file ) ) )
                $this->usage( "--progress must name a file in a directory that exists, not $file" );
            $out['progress'] = \expMigrationProgress::writer( $file );
        }
        return $out;
    }

    /**
     * The options --max-migrations, --max-time, --throttle and --nice, checked; a usage error ends with exit 2.
     *
     * @param array $options
     * @return array max-migrations, max-time, throttle, nice (absent when not given: the settings count)
     */
    protected function longRun( array $options )
    {
        $out = array();
        foreach ( array( 'max-migrations', 'max-time', 'nice' ) as $name )
            if ( isset( $options[$name] ) && $options[$name] !== false && $options[$name] !== '' )
            {
                $value = trim( (string)$options[$name] );
                $max = $name === 'nice' ? 19 : PHP_INT_MAX;
                if ( !ctype_digit( $value ) || (int)$value < 1 || (int)$value > $max )
                    $this->usage( "--$name must be a whole number from 1" . ( $name === 'nice' ? ' to 19' : '' ) . ", not $value" );
                $out[$name] = (int)$value;
            }
        if ( isset( $options['throttle'] ) && $options['throttle'] !== false && $options['throttle'] !== '' )
        {
            $value = trim( (string)$options['throttle'] );
            if ( !is_numeric( $value ) || (float)$value < 0 || (float)$value > 60 )
                $this->usage( "--throttle must be seconds from 0 to 60 (0.5 is half a second), not $value" );
            $out['throttle'] = (float)$value;
        }
        return $out;
    }

    /**
     * Prints a report and ends with its exit code.
     *
     * @param array $report
     * @param string $format
     */
    protected function finish( array $report, $format )
    {
        $report = array( 'schema' => \expMigrationRunner::REPORT, 'command' => $this->name() ) + $report;
        if ( !isset( $report['exit'] ) )
            $report['exit'] = $report['errors'] ? \expUpgrade::EXIT_FAILED : \expUpgrade::EXIT_OK;
        if ( $format === 'json' )
            $this->cli()->output( json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
        else
            $this->printText( $report );
        $this->shutdown( (int)$report['exit'] );
    }

    /**
     * A report as text: the command's own lines (lines()), then warnings, errors and PASS or FAIL.
     *
     * @param array $report
     */
    protected function printText( array $report )
    {
        foreach ( $this->lines( $report ) as $line )
            $this->output( $line );
        foreach ( isset( $report['warnings'] ) ? $report['warnings'] : array() as $w )
            $this->output( 'WARN ' . $w );
        foreach ( isset( $report['errors'] ) ? $report['errors'] : array() as $e )
            $this->error( 'FAIL: ' . $e );
        $this->output( (int)$report['exit'] === \expUpgrade::EXIT_OK ? 'PASS' : 'FAIL (exit ' . (int)$report['exit'] . ')' );
    }

    /**
     * The command's lines of a report; by default those of a run report.
     *
     * @param array $report
     * @return string[]
     */
    protected function lines( array $report )
    {
        return self::runLines( $report );
    }

    /**
     * The lines of a runner report (status, dry run, simulation, run, rollback with down()). Pure.
     *
     * @param array $report
     * @return string[]
     */
    public static function runLines( array $report )
    {
        $modes = array(
            'status' => 'status',
            'dry-run' => 'dry run, nothing is changed (--apply runs the migrations)',
            'simulate' => 'simulation, the database is rolled back afterwards',
            'apply' => 'run',
            'rollback' => 'rollback with down()',
            'rollback-dry-run' => 'rollback with down(), dry run (--apply undoes them)',
        );
        $mode = isset( $report['mode'] ) ? (string)$report['mode'] : '';
        $lines = array( 'Exponential migrations: ' . ( isset( $modes[$mode] ) ? $modes[$mode] : $mode ) );
        if ( !empty( $report['resumed_from'] ) )
            $lines[] = 'Resumes the run ' . $report['resumed_from']['run_id'] . ' (' . $report['resumed_from']['state'] . ')';
        if ( isset( $report['engine'] ) )
            $lines[] = sprintf( 'Run %s, environment %s, database %s (transactions: %s)%s', $report['run_id'],
                                $report['environment'] !== '' ? $report['environment'] : '(none)', $report['engine'] !== '' ? $report['engine'] : '-',
                                $report['transaction'], !empty( $report['user'] ) ? ', as ' . $report['user'] : '' );
        if ( !empty( $report['lock'] ) )
            $lines[] = 'Lock: ' . \expMigrationLock::describe( $report['lock'] );
        $migrations = isset( $report['migrations'] ) ? $report['migrations'] : array();
        if ( $migrations )
            $lines[] = '';
        foreach ( $migrations as $m )
        {
            $label = $m['action'] !== 'none' && $mode !== 'status' ? $m['action'] : $m['state'];
            $lines[] = sprintf( '  %-11s %-44s %-18s %s', $label, $m['id'], $m['source'], $m['detail'] !== '' ? $m['detail'] : $m['description'] );
            // a plan line indented by its migration (the steps of a YAML loop) keeps its indent before "would"
            foreach ( $m['plan'] as $line )
                $lines[] = '              ' . str_repeat( ' ', strlen( $line ) - strlen( ltrim( $line, ' ' ) ) ) . 'would ' . ltrim( $line, ' ' );
            if ( isset( $m['simulation'] ) )
            {
                $s = $m['simulation'];
                $lines[] = '              simulation ' . $s['result'] . ( $s['duration_ms'] ? ' in ' . $s['duration_ms'] . ' ms' : '' );
                foreach ( $s['messages'] as $line )
                    $lines[] = '              ' . $line;
                foreach ( $s['diff'] as $line )
                    $lines[] = '              ' . $line;
            }
            foreach ( $m['messages'] as $line )
                $lines[] = '              ' . $line;
            if ( $m['changed'] )
                $lines[] = '              CHANGED: the file is not the one applied (checksum ' . substr( $m['record_checksum'], 0, 12 ) . ' -> ' . substr( (string)$m['checksum'], 0, 12 ) . ')';
            if ( $mode === 'status' && $m['record'] )
                $lines[] = '              recorded ' . $m['record']['state'] . ' by run ' . $m['record']['run_id'] . ( $m['record']['finished_at'] ? ' on ' . $m['record']['finished_at'] : '' );
        }
        if ( $migrations )
            $lines[] = '';
        foreach ( isset( $report['orphans'] ) ? $report['orphans'] : array() as $o )
            $lines[] = sprintf( '  %-11s %-44s %-18s %s', 'orphan', $o['id'], $o['source'], 'recorded ' . $o['state'] . ', the file is gone' );
        if ( !empty( $report['captions'] ) )
        {
            $parts = array();
            foreach ( $report['captions'] as $kind => $caption )
                if ( is_array( $caption ) )
                    $parts[] = $kind . ' ' . $caption['id'] . ( $caption['reused'] ? ' (reused)' : '' ) . ( $caption['ok'] ? '' : ' (FAILED)' );
                else if ( $caption !== null )
                    $parts[] = $kind . ' ' . $caption;
            if ( $parts )
                $lines[] = 'Captions: ' . implode( ', ', $parts );
        }
        if ( !empty( $report['restore'] ) )
        {
            $lines[] = 'Restore: ' . $report['restore']['state'] . ( !empty( $report['restore']['caption'] ) ? ' (caption ' . $report['restore']['caption'] . ')' : '' );
            foreach ( isset( $report['restore']['steps'] ) ? (array)$report['restore']['steps'] : array() as $step )
                $lines[] = '  ' . $step;
        }
        if ( !empty( $report['repository'] ) )
        {
            $r = $report['repository'];
            $lines[] = 'Repository: ' . ( $r['commit_id'] !== '' ? 'commit ' . substr( $r['commit_id'], 0, 12 ) : 'no commit' )
                     . ( $r['tag_name'] !== '' ? ', tag ' . $r['tag_name'] : '' ) . ', push ' . $r['push_state'];
        }
        foreach ( isset( $report['runs'] ) ? $report['runs'] : array() as $i => $run )
        {
            if ( $i === 0 )
                $lines[] = 'The latest runs, newest first:';
            $lines[] = sprintf( '  %-24s %-11s %s  %s%s', $run['run_id'], $run['state'], $run['started_at'] ?: '-', $run['run_by'],
                                $run['push_state'] !== '' ? ', push ' . $run['push_state'] : '' );
        }
        if ( !empty( $report['summary'] ) )
        {
            $s = $report['summary'];
            $done = array();
            foreach ( array( 'applied', 'would-apply', 'simulated', 'undone', 'would-undo', 'restored', 'accepted', 'not-run', 'blocked', 'refused' ) as $action )
                if ( !empty( $s[$action] ) )
                    $done[] = $s[$action] . ' ' . $action;
            $lines[] = sprintf( 'Summary: %d migration(s): %d done, %d pending, %d not needed, %d failed, %d unknown%s', $s['total'], $s['done'], $s['pending'],
                                $s['not-needed'], $s['failed'], $s['unknown'], $done ? '; ' . implode( ', ', $done ) : '' );
        }
        return $lines;
    }

    /**
     * The lines of a caption restore (exp:upgrade's rollback report, as expMigrationRollback::run() and caption()
     * give it). Pure.
     *
     * @param array $report
     * @return string[]
     */
    public static function restoreLines( array $report )
    {
        $lines = array( 'Exponential migrations: ' . ( $report['mode'] === 'rollback' ? 'restore of a caption' : 'restore of a caption, dry run (--apply restores it)' ) );
        if ( !empty( $report['run'] ) )
            $lines[] = 'Run ' . $report['run']['run_id'] . ' (' . $report['run']['state'] . '), caption taken before it: ' . $report['run']['caption_pre'];
        if ( !empty( $report['rollback'] ) )
        {
            $lines[] = 'Caption: ' . $report['rollback']['caption'];
            if ( !empty( $report['rollback']['safety_caption'] ) )
                $lines[] = 'Safety caption of the state before: ' . $report['rollback']['safety_caption']['id'] . ( $report['rollback']['safety_caption']['ok'] ? '' : ' (FAILED)' );
            foreach ( $report['rollback']['parts'] as $part => $partLines )
            {
                $lines[] = '  ' . $part;
                foreach ( (array)$partLines as $line )
                    $lines[] = '      ' . $line;
            }
        }
        if ( !empty( $report['manual'] ) )
        {
            $lines[] = 'No built-in restore for this database; restore it by hand:';
            foreach ( $report['manual'] as $step )
                $lines[] = '  ' . $step;
        }
        return $lines;
    }
}

}
