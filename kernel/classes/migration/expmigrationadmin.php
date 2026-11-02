<?php
/**
 * File containing the expMigrationAdmin class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * What the views of the module migration (Setup > Migrations) share: reading their forms into the options of a job
 * (expMigrationJob), the kind of badge a state gets, the runs filtered by state, the end of a run log, and what a
 * rollback target is. Pure where it can be (tested without a database); the views only call it and fill templates.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationAdmin
{
    /** The flags of the run form (POST RunFlag[<name>]=1), as exp:migration:migrate names them. */
    const RUN_FLAGS = array( 'no-caption', 'no-restore', 'include-uncommitted', 'no-commit', 'no-push', 'no-tag' );

    /** The long-run fields of the run form (POST LongRun[<name>]), as exp:migration:migrate names them. */
    const LONG_RUN_FIELDS = array( 'max-migrations', 'max-time', 'throttle', 'nice' );

    /** The parts of a caption a rollback of a run may restore. */
    const RESTORE_PARTS = array( 'database', 'storage', 'settings' );

    /** The longest reason accepted for --force. */
    const MAX_REASON = 500;

    /** The states of a run the history can be filtered by. */
    const RUN_STATES = array( 'running', 'done', 'failed', 'rolled-back' );

    /** How much of a run log a page shows, from its end (migration.ini [AdminSettings] LogBytes). */
    const LOG_BYTES = 262144;

    /**
     * The badge of a state: ok, warn, bad or info. Pure.
     *
     * @param string $state a migration's (done, pending, not-needed, failed, unknown) or a run's (running, done, failed,
     *                      rolled-back) state, or an action (applied, would-apply, undone ...)
     * @return string
     */
    public static function stateKind( $state )
    {
        switch ( (string)$state )
        {
            case 'done':
            case 'applied':
            case 'undone':
            case 'pushed':
                return 'ok';
            case 'pending':
            case 'would-apply':
            case 'would-undo':
            case 'unknown':
            case 'blocked':
            case 'not-run':
            case 'accepted':
            case 'would-accept':
                return 'warn';
            case 'failed':
            case 'refused':
                return 'bad';
            default:
                return 'info';
        }
    }

    /**
     * Rows (migrations of a report, runs, records) with what the templates need besides: kind (stateKind() of the
     * state), action_kind (of the action, when there is one) and started_ts, finished_ts (Unix times of started_at
     * and finished_at, which reports give as ISO 8601 text; 0 without one). Pure.
     *
     * @param array[] $rows
     * @return array[]
     */
    public static function decorate( array $rows )
    {
        foreach ( $rows as $i => $row )
        {
            if ( !is_array( $row ) )
                continue;
            $rows[$i]['kind'] = self::stateKind( isset( $row['state'] ) ? $row['state'] : '' );
            $rows[$i]['action_kind'] = self::stateKind( isset( $row['action'] ) ? $row['action'] : '' );
            foreach ( array( 'started_at' => 'started_ts', 'finished_at' => 'finished_ts' ) as $from => $to )
            {
                $value = isset( $row[$from] ) ? $row[$from] : null;
                $rows[$i][$to] = is_numeric( $value ) ? (int)$value : ( is_string( $value ) && $value !== '' ? (int)strtotime( $value ) : 0 );
            }
        }
        return $rows;
    }

    /**
     * The options of a migrate job from the run form. Pure.
     *
     * @param array $post the POST variables: MigrationOnly[] (the ids ticked; every pending one: all), MigrationChoice
     *                    (the form listed them: none ticked is an error), RunFlag[<flag>], ForceChanged, ForceReason
     * @param string[] $pending the ids the form listed
     * @param string[] $errors problems are added here (codes: only, reason, reason-length)
     * @return array the options of expMigrationJob::start()
     */
    public static function runOptions( array $post, array $pending, array &$errors = array() )
    {
        $options = array();
        $chosen = isset( $post['MigrationOnly'] ) ? array_values( array_unique( array_map( 'strval', (array)$post['MigrationOnly'] ) ) ) : array();
        $chosen = array_values( array_intersect( $chosen, $pending ) );
        // MigrationChoice: the form listed the migrations, so none ticked is a choice of none, not of all
        if ( ( isset( $post['MigrationOnly'] ) || !empty( $post['MigrationChoice'] ) ) && !$chosen && $pending )
            $errors[] = 'only';
        if ( $chosen && count( $chosen ) < count( $pending ) )
            $options['only'] = $chosen;
        $flags = isset( $post['RunFlag'] ) ? (array)$post['RunFlag'] : array();
        foreach ( self::RUN_FLAGS as $flag )
            if ( !empty( $flags[$flag] ) )
                $options[$flag] = true;
        $options += self::longRunOptions( isset( $post['LongRun'] ) && is_array( $post['LongRun'] ) ? $post['LongRun'] : array(), $errors );
        if ( !empty( $post['ForceChanged'] ) )
        {
            $reason = trim( preg_replace( '/\s+/', ' ', isset( $post['ForceReason'] ) ? (string)$post['ForceReason'] : '' ) );
            if ( $reason === '' )
                $errors[] = 'reason';
            else if ( mb_strlen( $reason ) > self::MAX_REASON )
                $errors[] = 'reason-length';
            else
            {
                $options['force'] = true;
                $options['reason'] = $reason;
            }
        }
        return $options;
    }

    /**
     * The long-run options of a run from the run form, as the command checks them (--max-migrations, --max-time,
     * --throttle, --nice); an empty field leaves the setting of [RunSettings]. Pure.
     *
     * @param array $fields LongRun[<option>] of the form
     * @param string[] $errors problems are added here (codes: the option's name)
     * @return array option => value
     */
    public static function longRunOptions( array $fields, array &$errors = array() )
    {
        $options = array();
        foreach ( self::LONG_RUN_FIELDS as $name )
        {
            $value = isset( $fields[$name] ) && is_scalar( $fields[$name] ) ? trim( (string)$fields[$name] ) : '';
            if ( $value === '' )
                continue;
            if ( $name === 'throttle' )
            {
                if ( !is_numeric( $value ) || (float)$value < 0 || (float)$value > 60 )
                    $errors[] = $name;
                else
                    $options[$name] = (string)(float)$value;
                continue;
            }
            $max = $name === 'nice' ? 19 : PHP_INT_MAX;
            if ( !ctype_digit( $value ) || (int)$value < 1 || (int)$value > $max )
                $errors[] = $name;
            else
                $options[$name] = (string)(int)$value;
        }
        return $options;
    }

    /**
     * The values of [RunSettings] the long-run fields leave in place, for the form's placeholders: option => text,
     * '' where the setting is empty.
     *
     * @param eZINI|null $ini
     * @return string[]
     */
    public static function longRunDefaults( $ini = null )
    {
        $ini = $ini === null ? eZINI::instance( 'migration.ini' ) : $ini;
        $out = array();
        foreach ( array( 'max-migrations' => 'MaxMigrations', 'max-time' => 'MaxTime', 'throttle' => 'Throttle', 'nice' => 'Nice' ) as $option => $setting )
            $out[$option] = $ini->hasVariable( 'RunSettings', $setting ) ? trim( (string)$ini->variable( 'RunSettings', $setting ) ) : '';
        return $out;
    }

    /**
     * The options of a rollback job from the rollback form. Pure.
     *
     * @param string $kind what the target is: run, migration or caption (target())
     * @param string $target
     * @param array $post ConfirmRollback (run, migration), RestorePart[] (run), Expert and ConfirmCaption (caption)
     * @param string[] $errors problems are added here (codes: confirm, expert, typed, parts)
     * @return array the options of expMigrationJob::start()
     */
    public static function rollbackOptions( $kind, $target, array $post, array &$errors = array() )
    {
        $options = array( 'target' => (string)$target );
        if ( $kind === 'caption' )
        {
            if ( empty( $post['Expert'] ) )
                $errors[] = 'expert';
            if ( !isset( $post['ConfirmCaption'] ) || trim( (string)$post['ConfirmCaption'] ) !== (string)$target )
                $errors[] = 'typed';
            $options['expert'] = true;
            $options['confirm'] = isset( $post['ConfirmCaption'] ) ? trim( (string)$post['ConfirmCaption'] ) : '';
        }
        else if ( empty( $post['ConfirmRollback'] ) )
            $errors[] = 'confirm';
        if ( $kind === 'run' || $kind === 'caption' )
        {
            $parts = isset( $post['RestorePart'] ) ? array_values( array_intersect( self::RESTORE_PARTS, array_map( 'strval', (array)$post['RestorePart'] ) ) ) : array();
            if ( isset( $post['RestorePart'] ) && !$parts )
                $errors[] = 'parts';
            if ( $parts && count( $parts ) < count( self::RESTORE_PARTS ) )
                $options['parts'] = implode( ',', $parts );
        }
        return $options;
    }

    /**
     * What a rollback target is: a run of exp_migration_run, an applied migration or a caption. The order is the
     * command's (exp:migration:rollback).
     *
     * @param string $target
     * @param array $runIds the run ids of exp_migration_run (or a run row keyed by id)
     * @param string[] $migrationIds the ids of the installation's migrations
     * @param string[] $captionIds
     * @return string run, migration, caption, or '' for none
     */
    public static function targetKind( $target, array $runIds, array $migrationIds, array $captionIds )
    {
        $target = (string)$target;
        if ( $target === '' )
            return '';
        if ( expMigrationRecord::validRunId( $target ) && in_array( $target, $runIds, true ) )
            return 'run';
        if ( in_array( $target, $migrationIds, true ) )
            return 'migration';
        if ( in_array( $target, $captionIds, true ) )
            return 'caption';
        return '';
    }

    /**
     * The runs in a state, all for ''. Pure.
     *
     * @param array[] $runs rows or runSummary()s with state
     * @param string $state
     * @return array[]
     */
    public static function filterRuns( array $runs, $state )
    {
        if ( (string)$state === '' || !in_array( (string)$state, self::RUN_STATES, true ) )
            return array_values( $runs );
        return array_values( array_filter( $runs, function ( $run ) use ( $state ) {
            return isset( $run['state'] ) && $run['state'] === $state;
        } ) );
    }

    /**
     * The end of a log file.
     *
     * @param string $file
     * @param int|null $bytes how much (default migration.ini [AdminSettings] LogBytes, else LOG_BYTES)
     * @return array text, size (the file's size: where a live view goes on reading), truncated (bool), exists (bool)
     */
    public static function logTail( $file, $bytes = null )
    {
        if ( $bytes === null )
        {
            $ini = eZINI::instance( 'migration.ini' );
            $bytes = $ini->hasVariable( 'AdminSettings', 'LogBytes' ) ? (int)$ini->variable( 'AdminSettings', 'LogBytes' ) : self::LOG_BYTES;
        }
        $bytes = $bytes > 0 ? (int)$bytes : self::LOG_BYTES;
        clearstatcache( true, (string)$file );
        if ( (string)$file === '' || !is_file( (string)$file ) )
            return array( 'text' => '', 'size' => 0, 'truncated' => false, 'exists' => false );
        $size = (int)filesize( (string)$file );
        $from = max( 0, $size - $bytes );
        $text = (string)@file_get_contents( (string)$file, false, null, $from );
        if ( $from > 0 )
        {
            // from the first whole line
            $newline = strpos( $text, "\n" );
            $text = $newline === false ? $text : substr( $text, $newline + 1 );
        }
        return array( 'text' => $text, 'size' => $size, 'truncated' => $from > 0, 'exists' => true );
    }

    /**
     * The log file of a run in the migrations repository.
     *
     * @param string $runId
     * @return string '' for no run id
     */
    public static function runLogFile( $runId )
    {
        if ( !expMigrationRecord::validRunId( (string)$runId ) )
            return '';
        $mirror = new expMigrationStatusMirror( expMigrationRepository::fromSettings()->directory() );
        return $mirror->logFile( (string)$runId );
    }

    /**
     * Whether the signed-in user may use a function of the module.
     *
     * @param string $function read, run, rollback, edit
     * @param eZUser|null $user
     * @return bool
     */
    public static function canDo( $function, $user = null )
    {
        $user = $user ?: eZUser::currentUser();
        $access = $user->hasAccessTo( 'migration', (string)$function );
        return isset( $access['accessWord'] ) && $access['accessWord'] !== 'no';
    }

    /**
     * The fetch function migration/summary (kernel/migration/function_definition.php), for the dashboard block
     * "migrations": the counts of the status and the latest run. Empty for a user without migration/read; never
     * throws (a problem is its text in problem).
     *
     * @return array result => array( pending, failed, orphans, total, ready, run (the latest run with kind and
     *               started_ts, or null), running (the run going on in the background, or null), lock (who holds the
     *               installation lock, '' when nobody), problem )
     */
    public static function fetchSummary()
    {
        if ( !self::canDo( 'read' ) )
            return array( 'result' => array() );
        $summary = array( 'pending' => 0, 'failed' => 0, 'orphans' => 0, 'total' => 0, 'ready' => false, 'run' => null, 'running' => null,
                          'lock' => '', 'problem' => '' );
        try
        {
            $report = expMigrationStatus::report( array( 'runs' => 1 ) );
            $summary = self::summary( $report ) + $summary;
            $job = expMigrationJob::running();
            $summary['running'] = $job ? array( 'id' => $job['id'], 'kind' => $job['kind'], 'user' => $job['user'], 'started' => (int)$job['started'] ) : null;
        }
        catch ( Throwable $e )
        {
            $summary['problem'] = $e->getMessage();
        }
        return array( 'result' => $summary );
    }

    /**
     * The fetch function migration/recording (kernel/migration/function_definition.php), for the bar "Recording..." on
     * every page of the administration: the signed-in user's own recording and the site-wide window that run. Every
     * user sees the site-wide window (their changes are recorded too); only holders of migration/edit may stop it.
     * Reads the file of running recordings, no query; never throws.
     *
     * @return array result => array( mine (recording_id, started_at, or null), site (or null), can_stop_site (bool) )
     */
    public static function fetchRecording()
    {
        $out = array( 'mine' => null, 'site' => null, 'can_stop_site' => false );
        try
        {
            if ( !class_exists( 'expMigrationRecording' ) )
                return array( 'result' => $out );
            $user = eZUser::currentUser();
            if ( !$user->isRegistered() )
                return array( 'result' => $out );
            $out = expMigrationRecording::forUser( (int)$user->attribute( 'contentobject_id' ) ) + $out;
            if ( $out['site'] !== null )
                $out['can_stop_site'] = self::canDo( 'edit', $user );
        }
        catch ( Throwable $e )
        {
        }
        return array( 'result' => $out );
    }

    /**
     * The counts of a status report (expMigrationStatus::report()) the dashboard shows. Pure.
     *
     * @param array $report
     * @return array pending, failed, orphans, total, ready, run, lock
     */
    public static function summary( array $report )
    {
        $out = array( 'pending' => 0, 'failed' => 0, 'orphans' => isset( $report['orphans'] ) ? count( $report['orphans'] ) : 0,
                      'total' => isset( $report['migrations'] ) ? count( $report['migrations'] ) : 0, 'ready' => !empty( $report['table'] ),
                      'run' => null, 'lock' => !empty( $report['lock'] ) ? expMigrationLock::describe( $report['lock'] ) : '' );
        foreach ( isset( $report['migrations'] ) ? $report['migrations'] : array() as $m )
            if ( isset( $m['state'] ) && ( $m['state'] === expMigration::PENDING || $m['state'] === expMigration::FAILED ) )
                $out[$m['state'] === expMigration::PENDING ? 'pending' : 'failed']++;
        if ( !empty( $report['runs'] ) )
            $out['run'] = self::decorate( array( $report['runs'][0] ) )[0];
        return $out;
    }

    /**
     * The lines of the restore part of a report (rollback of a run or a caption) for a page. Pure.
     *
     * @param array|null $report
     * @return string[]
     */
    public static function restoreLines( $report )
    {
        if ( !is_array( $report ) || !class_exists( '\\Exponential\\Command\\Kernel\\ExpmigrationBase' ) )
            return array();
        if ( isset( $report['target']['kind'] ) && $report['target']['kind'] === 'migration' )
            return array();
        if ( empty( $report['rollback'] ) && empty( $report['manual'] ) && empty( $report['run'] ) )
            return array();
        return \Exponential\Command\Kernel\ExpmigrationBase::restoreLines( $report + array( 'mode' => 'rollback' ) );
    }
}
