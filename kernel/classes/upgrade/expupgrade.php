<?php
/**
 * File containing the expUpgrade class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The upgrade of an installation, as one API: the console commands exp:upgrade and exp:upgrade:* are thin wrappers
 * of it, and the administration or another tool calls the same code.
 *
 *   $report = expUpgrade::status( array( 'database', 'scripts' ) );
 *   $report = expUpgrade::run( array( 'database', 'scripts' ), array( 'apply' => true ) );
 *   exit( expUpgrade::exitCode( $report ) );
 *
 * Stages run in a fixed order: code, libraries, database, scripts, finish (expUpgradeRegistry::STAGES). Every stage
 * is made of steps (expUpgradeStep). A step's state:
 *
 *   done        in place, and exp:upgrade applied it (the record says so)
 *   not-needed  in place without exp:upgrade (a new installation, or applied by hand), or not for this installation
 *   pending     missing: the step has work to do
 *   failed      missing, and the last attempt failed (the record keeps the message)
 *   unknown     cannot be told (the reason is in the detail); a run skips it and goes on
 *
 * A run without 'apply' changes nothing and lists, per pending step, what it would change. With 'apply' it takes the
 * lock, makes the backup before the first write (unless 'no-backup'), applies the pending steps in order, checks each
 * with detect() again, records it, and stops at the first failure.
 *
 * The report is the machine output exp-upgrade/1 (versioned; keys are only ever added): see report().
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgrade
{
    /** The machine output's format (permanent; a later format gets a new number). */
    const FORMAT = 'exp-upgrade/1';

    /** Exit codes (permanent). */
    const EXIT_OK = 0;
    const EXIT_FAILED = 1;
    const EXIT_USAGE = 2;
    const EXIT_UNREACHABLE = 3;
    const EXIT_INTERNAL = 4;

    /** The states of a step (permanent). */
    const STATES = array( 'done', 'pending', 'not-needed', 'failed', 'unknown' );

    /**
     * The state of every step of some stages, without changing anything (no plan, no lock, no backup).
     *
     * @param string[] $stages
     * @param array $options only, skip (step ids), db (a connection), registry (an expUpgradeRegistry)
     * @return array the report
     */
    public static function status( array $stages, array $options = array() )
    {
        return self::execute( $stages, array( 'apply' => false, 'plan' => false ) + $options, 'status' );
    }

    /**
     * Runs some stages: a dry run, or with 'apply' => true the upgrade.
     *
     * Options: apply (bool), only / skip (step ids), force-order (bool), no-backup (bool), backup-dir (string),
     * fail-on-pending (bool), db (connection), registry (expUpgradeRegistry), progress (callable( string $line )),
     * command (the name shown in the report).
     *
     * @param string[] $stages
     * @param array $options
     * @return array the report
     */
    public static function run( array $stages, array $options = array() )
    {
        $apply = !empty( $options['apply'] );
        return self::execute( $stages, array( 'plan' => true ) + $options, $apply ? 'apply' : 'dry-run' );
    }

    /**
     * Re-detects every step and reports drift: a step the record calls done whose effect is missing.
     *
     * @param string[] $stages
     * @param array $options
     * @return array the report, with 'drift' => step ids
     */
    public static function verify( array $stages, array $options = array() )
    {
        $report = self::status( $stages, $options );
        $report['mode'] = 'verify';
        $report['drift'] = array();
        foreach ( $report['stages'] as $stage )
            foreach ( $stage['steps'] as $step )
                if ( $step['drift'] )
                    $report['drift'][] = $step['id'];
        if ( $report['drift'] )
            $report['exit'] = self::EXIT_FAILED;
        return $report;
    }

    /**
     * The stages a value names: "a,b" or an array; every stage when empty.
     *
     * @param string|string[]|null $value
     * @param string[] $errors unknown names are added here
     * @return string[] in the fixed order
     */
    public static function stages( $value, array &$errors = array() )
    {
        $names = is_array( $value ) ? $value : ( $value === null || $value === '' || $value === false ? array() : explode( ',', (string)$value ) );
        $names = array_values( array_filter( array_map( 'trim', $names ), 'strlen' ) );
        if ( !$names )
            return expUpgradeRegistry::STAGES;
        foreach ( $names as $name )
            if ( !in_array( $name, expUpgradeRegistry::STAGES, true ) )
                $errors[] = "unknown stage $name; stages: " . implode( ', ', expUpgradeRegistry::STAGES );
        return array_values( array_intersect( expUpgradeRegistry::STAGES, $names ) );
    }

    /**
     * A list option ("a,b", repeated, or an array) as a flat list of names.
     *
     * @param mixed $value
     * @return string[]
     */
    public static function listOption( $value )
    {
        $out = array();
        foreach ( (array)$value as $part )
            foreach ( explode( ',', (string)$part ) as $name )
                if ( trim( $name ) !== '' )
                    $out[] = trim( $name );
        return array_values( array_unique( $out ) );
    }

    /**
     * The state of a step from what detect() found and what the record says. Pure.
     *
     * @param array $effect detect()'s answer
     * @param array|null $record the record's row
     * @param bool $engineMatches whether the step is for this engine
     * @return string one of STATES
     */
    public static function stateOf( array $effect, $record, $engineMatches = true )
    {
        if ( !$engineMatches )
            return 'not-needed';
        switch ( $effect['effect'] )
        {
            case expUpgradeStep::PRESENT:
                return $record && $record['state'] === 'done' ? 'done' : 'not-needed';
            case expUpgradeStep::NOT_APPLICABLE:
                return 'not-needed';
            case expUpgradeStep::MISSING:
                return $record && $record['state'] === 'failed' ? 'failed' : 'pending';
        }
        return 'unknown';
    }

    /**
     * The exit code of a report. Pure.
     *
     * @param array $report
     * @return int
     */
    public static function exitCode( array $report )
    {
        return isset( $report['exit'] ) ? (int)$report['exit'] : self::EXIT_INTERNAL;
    }

    /**
     * The empty report.
     *
     * @param string $mode status, dry-run, apply, verify
     * @param array $options
     * @return array
     */
    public static function report( $mode, array $options = array() )
    {
        return array(
            'format' => self::FORMAT,
            'command' => isset( $options['command'] ) ? (string)$options['command'] : '',
            'mode' => $mode,
            'installation' => array( 'engine' => '', 'database_version' => null, 'kernel_version' => self::kernelVersion() ),
            'backup' => null,
            'stages' => array(),
            'summary' => array_fill_keys( self::STATES, 0 ) + array( 'applied' => 0 ),
            'errors' => array(),
            'warnings' => array(),
            'exit' => self::EXIT_OK,
        );
    }

    /** @return string the version of the code (eZPublishSDK) */
    public static function kernelVersion()
    {
        return class_exists( 'eZPublishSDK' ) ? (string)eZPublishSDK::version() : '';
    }

    /**
     * @param string[] $stages
     * @param array $options
     * @param string $mode
     * @return array
     */
    protected static function execute( array $stages, array $options, $mode )
    {
        $report = self::report( $mode, $options );
        $progress = isset( $options['progress'] ) && is_callable( $options['progress'] ) ? $options['progress'] : null;
        $apply = $mode === 'apply';
        try
        {
            $db = isset( $options['db'] ) ? $options['db'] : eZDB::instance();
            if ( !$db || !$db->isConnected() )
            {
                $report['errors'][] = 'the database is not reachable';
                $report['exit'] = self::EXIT_UNREACHABLE;
                return $report;
            }
            $registry = isset( $options['registry'] ) ? $options['registry'] : expUpgradeRegistry::fromSettings();
            foreach ( $registry->errors() as $error )
                $report['warnings'][] = $error;
            $context = new expUpgradeContext( $db, $apply, $options, isset( $options['schema-file'] ) ? $options['schema-file'] : null );
            $engine = $context->engine();
            $report['installation']['engine'] = $engine;
            $report['installation']['database_version'] = $engine === 'mongodb' ? null : expUpgradeStepSiteVersion::stored( $db );

            $only = self::listOption( isset( $options['only'] ) ? $options['only'] : array() );
            $skip = self::listOption( isset( $options['skip'] ) ? $options['skip'] : array() );
            foreach ( array_merge( $only, $skip ) as $id )
                if ( !$registry->get( $id ) )
                    $report['errors'][] = "no step $id (./console exp:upgrade:steps lists them)";
            if ( $report['errors'] )
            {
                $report['exit'] = self::EXIT_USAGE;
                return $report;
            }

            // the order of the stages: a stage left out of the run must not have pending steps before a later one writes
            if ( $apply && empty( $options['force-order'] ) )
            {
                $first = null;
                foreach ( expUpgradeRegistry::STAGES as $i => $name )
                    if ( in_array( $name, $stages, true ) && $first === null )
                        $first = $i;
                $earlier = array_slice( expUpgradeRegistry::STAGES, 0, (int)$first );
                if ( $earlier )
                {
                    $before = self::status( $earlier, array( 'db' => $db, 'registry' => $registry ) );
                    foreach ( $before['stages'] as $stage )
                        if ( $stage['pending'] > 0 )
                        {
                            $report['errors'][] = "the stage {$stage['stage']} has {$stage['pending']} pending step(s): run it first (or --force-order)";
                            $report['exit'] = self::EXIT_FAILED;
                        }
                    if ( $report['errors'] )
                        return $report;
                }
            }

            $records = expUpgradeRecord::all( $db );
            $locked = false;
            $backedUp = !empty( $options['no-backup'] );
            if ( !empty( $options['no-backup'] ) && $apply )
                $report['warnings'][] = 'no backup made (--no-backup)';
            $stop = false;
            $outcome = array();
            foreach ( $stages as $stageName )
            {
                $stageReport = array( 'stage' => $stageName, 'state' => 'done', 'pending' => 0, 'steps' => array() );
                foreach ( $registry->stageSteps( array( $stageName ) ) as $step )
                {
                    $id = $step->id();
                    if ( ( $only && !in_array( $id, $only, true ) ) || in_array( $id, $skip, true ) || ( $step->optional() && !in_array( $id, $only, true ) ) )
                        continue;
                    $entry = self::entry( $step, $records );
                    $engineMatches = in_array( '*', $step->engines(), true ) || in_array( $engine, $step->engines(), true );
                    try
                    {
                        $effect = $engineMatches ? $step->detect( $context ) : array( 'effect' => expUpgradeStep::NOT_APPLICABLE, 'detail' => "not for $engine" );
                    }
                    catch ( Throwable $e )
                    {
                        $effect = array( 'effect' => expUpgradeStep::UNKNOWN, 'detail' => 'detection failed: ' . $e->getMessage() );
                    }
                    $entry['detail'] = $effect['detail'];
                    $entry['state'] = self::stateOf( $effect, isset( $records[$id] ) ? $records[$id] : null, $engineMatches );
                    $entry['drift'] = $effect['effect'] === expUpgradeStep::MISSING && isset( $records[$id] ) && $records[$id]['state'] === 'done';

                    $blockedBy = array();
                    foreach ( $step->after() as $dep )
                        if ( isset( $outcome[$dep] ) && in_array( $outcome[$dep], array( 'failed', 'blocked' ), true ) )
                            $blockedBy[] = $dep;

                    if ( $stop )
                        $entry['action'] = in_array( $entry['state'], array( 'pending', 'failed' ), true ) ? 'not-run' : 'none';
                    else if ( in_array( $entry['state'], array( 'pending', 'failed' ), true ) && $mode !== 'status' )
                    {
                        if ( $blockedBy )
                        {
                            $entry['action'] = 'blocked';
                            $entry['messages'][] = 'waits for ' . implode( ', ', $blockedBy );
                            $outcome[$id] = 'blocked';
                        }
                        else if ( !$apply )
                        {
                            $entry['action'] = 'would-apply';
                            try
                            {
                                $entry['plan'] = array_values( (array)$step->plan( $context ) );
                            }
                            catch ( Throwable $e )
                            {
                                $entry['plan'] = array( 'cannot list the changes: ' . $e->getMessage() );
                            }
                        }
                        else
                        {
                            if ( !$locked )
                            {
                                if ( !expUpgradeLock::acquire( isset( $options['lock-file'] ) ? $options['lock-file'] : null ) )
                                {
                                    $report['errors'][] = 'another upgrade is running (lock ' . expUpgradeLock::file() . ')';
                                    $report['exit'] = self::EXIT_FAILED;
                                    return $report;
                                }
                                $locked = true;
                            }
                            if ( !$backedUp )
                            {
                                if ( $progress )
                                    $progress( 'backup ...' );
                                $backup = expUpgradeBackup::create( $db, isset( $options['backup-dir'] ) ? $options['backup-dir'] : null );
                                $report['backup'] = array( 'dir' => $backup['dir'], 'ok' => $backup['ok'], 'files' => $backup['files'], 'messages' => $backup['messages'] );
                                if ( !$backup['ok'] )
                                {
                                    $report['errors'][] = 'the backup failed, nothing was changed: ' . implode( '; ', $backup['messages'] ) . ' (--no-backup runs without one)';
                                    $report['exit'] = self::EXIT_FAILED;
                                    expUpgradeLock::release();
                                    return $report;
                                }
                                $backedUp = true;
                            }
                            if ( $progress )
                                $progress( "apply $id ..." );
                            $start = microtime( true );
                            try
                            {
                                $entry['messages'] = array_values( (array)$step->apply( $context ) );
                                expKernelTableSchema::reset();
                                $after = $step->detect( $context );
                                if ( $after['effect'] !== expUpgradeStep::PRESENT && $after['effect'] !== expUpgradeStep::NOT_APPLICABLE )
                                    throw new RuntimeException( 'applied, but detection still says ' . $after['effect'] . ': ' . $after['detail'] );
                                $entry['detail'] = $after['detail'];
                                $entry['state'] = 'done';
                                $entry['action'] = 'applied';
                                $report['summary']['applied']++;
                            }
                            catch ( Throwable $e )
                            {
                                $entry['state'] = 'failed';
                                $entry['action'] = 'failed';
                                $entry['messages'][] = $e->getMessage();
                                $report['errors'][] = "$id: " . $e->getMessage();
                                $stop = true;
                            }
                            $entry['duration_ms'] = (int)round( ( microtime( true ) - $start ) * 1000 );
                            $written = expUpgradeRecord::write( $db, $step, $entry['state'], $entry['duration_ms'], implode( "\n", $entry['messages'] ) );
                            if ( !$written && $entry['state'] === 'done' && $id !== '6.0.15-upgrade-record-table' )
                                $report['warnings'][] = "$id: not recorded (no table " . expUpgradeRecord::TABLE . ')';
                            self::log( $id, $entry, $engine );
                            $outcome[$id] = $entry['state'];
                        }
                    }
                    else if ( $entry['state'] === 'unknown' && $mode !== 'status' )
                        $report['warnings'][] = "$id: skipped, cannot tell whether it is needed: " . $entry['detail'];

                    if ( in_array( $entry['state'], array( 'pending', 'failed' ), true ) )
                        $stageReport['pending']++;
                    $report['summary'][$entry['state']]++;
                    $stageReport['steps'][] = $entry;
                }
                $stageReport['state'] = self::stageState( $stageReport['steps'] );
                $report['stages'][] = $stageReport;
            }
            if ( $locked )
                expUpgradeLock::release();
            if ( $apply && $report['summary']['applied'] > 0 )
                $report['installation']['database_version'] = $engine === 'mongodb' ? null : expUpgradeStepSiteVersion::stored( $db );

            if ( $report['summary']['failed'] > 0 )
                $report['exit'] = self::EXIT_FAILED;
            else if ( !empty( $options['fail-on-pending'] ) && $report['summary']['pending'] > 0 )
                $report['exit'] = self::EXIT_FAILED;
        }
        catch ( Throwable $e )
        {
            expUpgradeLock::release();
            $report['errors'][] = 'internal error: ' . $e->getMessage();
            $report['exit'] = self::EXIT_INTERNAL;
        }
        return $report;
    }

    /**
     * A stage's state from its steps: failed, pending, unknown, done (something applied by exp:upgrade) or not-needed.
     *
     * @param array[] $steps
     * @return string
     */
    public static function stageState( array $steps )
    {
        $states = array_column( $steps, 'state' );
        foreach ( array( 'failed', 'pending', 'unknown', 'done' ) as $state )
            if ( in_array( $state, $states, true ) )
                return $state;
        return 'not-needed';
    }

    /**
     * The report entry of a step before detection.
     *
     * @param expUpgradeStep $step
     * @param array $records
     * @return array
     */
    protected static function entry( expUpgradeStep $step, array $records )
    {
        $id = $step->id();
        $record = isset( $records[$id] ) ? $records[$id] : null;
        return array(
            'id' => $id,
            'stage' => $step->stage(),
            'version' => $step->version(),
            'description' => $step->description(),
            'engines' => array_values( $step->engines() ),
            'after' => array_values( $step->after() ),
            'optional' => (bool)$step->optional(),
            'state' => 'unknown',
            'detail' => '',
            'drift' => false,
            'action' => 'none',
            'plan' => array(),
            'messages' => array(),
            'duration_ms' => 0,
            'record' => $record ? array( 'state' => (string)$record['state'], 'applied_at' => $record['applied_at'] ? date( 'c', $record['applied_at'] ) : null,
                                         'duration_ms' => $record['duration_ms'], 'by' => (string)$record['applied_by'] ) : null,
        );
    }

    /**
     * One line in var/log/upgrade.log per step applied.
     */
    protected static function log( $id, array $entry, $engine )
    {
        eZLog::write( sprintf( '%s %s on %s in %d ms: %s', $entry['state'], $id, $engine, $entry['duration_ms'],
                               str_replace( "\n", ' | ', implode( "\n", $entry['messages'] ) ) ), 'upgrade.log' );
    }
}
