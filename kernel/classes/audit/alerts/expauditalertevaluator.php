<?php
/**
 * Evaluates the alert rules (doc/bc/6.0/audit.md, "Alerts (F5)") and fires system.audit.alert.
 *
 *   [AuditAlertSettings] Alerts, EvaluateIn[] (flush, cronjob), Rules[], RuleClasses[], BusinessDays, BusinessHours
 *   [AlertRule_<name>]   Class (default threshold), Event (pattern), Threshold, Window, GroupBy, Severity, Sinks[], ...
 *
 * At flush (afterWrite(), called by expAudit::write() once records are in the file): each rule whose Event pattern
 * matches one of the written records is evaluated with those records; a request without matching records reads
 * no state at all. In the cronjob part (cronjob()): the records written since the last run (a cursor per
 * channel in <LogDir>/alerts/.cursor.json) are evaluated again — event ids make that idempotent — so rules also
 * see events whose writer did not evaluate them; the "audit disabled" check compares the state file
 * <LogDir>/.state with the settings (a change made by editing a file by hand); and windows that closed are pruned.
 *
 * Each firing is the event system.audit.alert with the rule's Severity; after holds rule, group, count, window,
 * first, last, sinks and message, and expAuditSinkRegistry::dispatch() sends it to the rule's Sinks[].
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditAlertEvaluator
{
    const ALERT = 'system.audit.alert';

    /** @var bool evaluate() is running */
    protected static $evaluating = false;

    /** @var array[] alerts fired by this process (tests, the command's output) */
    public static $fired = array();

    /** @return bool Alerts=enabled */
    public static function isEnabled()
    {
        return strtolower( trim( (string)expAuditConfig::value( 'AuditAlertSettings', 'Alerts', 'enabled' ) ) ) === 'enabled';
    }

    /** @return bool $where (flush, cronjob) is in EvaluateIn[] */
    public static function evaluatesIn( $where )
    {
        return in_array( $where, expAuditConfig::lists( 'AuditAlertSettings', 'EvaluateIn', array( 'flush', 'cronjob' ) ), true );
    }

    /** @return array name => class: the rule classes (the shipped three when nothing is configured) */
    public static function classes()
    {
        $c = expAuditConfig::hash( 'AuditAlertSettings', 'RuleClasses' );
        if ( !$c )
            $c = array( 'threshold' => 'expAuditThresholdRule', 'match' => 'expAuditMatchRule', 'schedule' => 'expAuditScheduleRule' );
        return $c;
    }

    /**
     * The rules in use: Rules[] whose [AlertRule_<name>] block exists, with their class.
     *
     * @return array name => array( config, class, problem )
     */
    public static function rules()
    {
        $out = array();
        $classes = self::classes();
        foreach ( expAuditConfig::lists( 'AuditAlertSettings', 'Rules' ) as $name )
        {
            $config = expAuditConfig::block( 'AlertRule_' . $name );
            $problem = '';
            if ( !$config )
                $problem = "no [AlertRule_$name] block";
            $classKey = isset( $config['Class'] ) && trim( $config['Class'] ) !== '' ? trim( $config['Class'] ) : 'threshold';
            $class = isset( $classes[$classKey] ) ? $classes[$classKey] : null;
            if ( $problem === '' && ( !$class || !class_exists( $class ) || !is_subclass_of( $class, 'expAuditAlertRule' ) ) )
                $problem = "Class=$classKey is not a registered rule class implementing expAuditAlertRule";
            if ( $problem === '' && trim( (string)( isset( $config['Event'] ) ? $config['Event'] : '' ) ) === '' )
                $problem = 'no Event pattern';
            $config += array( 'Event' => '', 'Severity' => 'warning', 'Sinks' => array() );
            $config['Sinks'] = array_values( array_filter( array_map( 'trim', (array)$config['Sinks'] ), 'strlen' ) );
            $out[$name] = array( 'config' => $config, 'class' => $class, 'problem' => $problem, 'class_key' => $classKey );
        }
        return $out;
    }

    /** @return string The alerts directory */
    public static function stateDir()
    {
        return expAuditConfig::get()['logDir'] . '/alerts';
    }

    /**
     * After records were written (expAudit::write()). Never throws.
     *
     * @param string $channel
     * @param array[] $records
     */
    public static function afterWrite( $channel, array $records )
    {
        if ( self::$evaluating || !$records )
            return;
        try
        {
            if ( !self::isEnabled() || !self::evaluatesIn( 'flush' ) )
                return;
            self::evaluate( $records );
        }
        catch ( Throwable $e )
        {
            if ( class_exists( 'eZDebug' ) )
                eZDebug::writeError( 'Audit alerts: ' . $e->getMessage(), __METHOD__ );
        }
    }

    /**
     * Evaluates the rules over records and fires the alerts.
     *
     * @param array[] $records
     * @param bool $fire false: return the alerts without recording them, with in-memory state (replay)
     * @param string|null $only one rule
     * @return array[] alerts: rule, group, count, first, last, message, severity, sinks, event (the alert's id)
     */
    public static function evaluate( array $records, $fire = true, $only = null )
    {
        if ( self::$evaluating )
            return array();
        self::$evaluating = true;
        $alerts = array();
        try
        {
            $records = array_values( array_filter( $records, function ( $r ) {
                return isset( $r['name'] ) && $r['name'] !== expAuditAlertEvaluator::ALERT && empty( $r['imported'] );
            } ) );
            if ( !$records )
                return array();
            foreach ( self::rules() as $name => $rule )
            {
                if ( $rule['problem'] !== '' || ( $only !== null && $name !== $only ) )
                    continue;
                $pattern = trim( (string)$rule['config']['Event'] );
                $matching = array();
                foreach ( $records as $r )
                    if ( expAuditTaxonomy::match( $pattern, $r['name'] ) >= 0 )
                        $matching[] = $r;
                if ( !$matching )
                    continue;
                usort( $matching, function ( $a, $b ) {
                    return strcmp( isset( $a['time'] ) ? $a['time'] : '', isset( $b['time'] ) ? $b['time'] : '' );
                } );
                $class = $rule['class'];
                $instance = new $class();
                $state = $fire ? expAuditAlertState::open( self::stateDir(), $name ) : expAuditAlertState::memory();
                try
                {
                    $found = $instance->evaluate( $rule['config'], $matching, $state );
                    $window = max( 0, (int)( isset( $rule['config']['Window'] ) ? $rule['config']['Window'] : 0 ) );
                    $last = end( $matching );
                    $state->prune( expAuditAlertRuleBase::timeMs( $last ), max( $window * 1000, 86400000 ) );
                }
                finally
                {
                    if ( $fire )
                        $state->close();
                }
                foreach ( (array)$found as $a )
                {
                    $a['rule'] = $name;
                    $a['severity'] = in_array( strtolower( (string)$rule['config']['Severity'] ), expAuditTaxonomy::$severities, true )
                                     ? strtolower( $rule['config']['Severity'] ) : 'warning';
                    $a['sinks'] = $rule['config']['Sinks'];
                    $a['window'] = isset( $rule['config']['Window'] ) ? (int)$rule['config']['Window'] : 0;
                    $a['event'] = $fire ? self::fire( $a ) : null;
                    $alerts[] = $a;
                }
            }
        }
        finally
        {
            self::$evaluating = false;
        }
        return $alerts;
    }

    /**
     * Records one alert as system.audit.alert.
     *
     * @param array $a
     * @return string|null the event id
     */
    protected static function fire( array $a )
    {
        $after = array( 'rule' => $a['rule'], 'group' => (string)$a['group'], 'count' => (int)$a['count'], 'window' => (int)$a['window'],
                        'first' => $a['first'], 'last' => $a['last'], 'sinks' => array_values( $a['sinks'] ),
                        'message' => isset( $a['message'] ) ? (string)$a['message'] : '' );
        if ( !empty( $a['policies'] ) )
            $after['policies'] = array_values( $a['policies'] );
        $data = array( 'severity' => $a['severity'], 'object' => array( 'type' => 'rule', 'id' => $a['rule'] ),
                       'target' => array( 'type' => 'event', 'id' => (string)$a['last'] ), 'after' => $after );
        self::$fired[] = $a;
        if ( expAudit::isEnabled() )
            return expAudit::event( self::ALERT, $data );
        // audit is off (the audit_disabled rule): written all the same
        return self::forceWrite( self::ALERT, $data );
    }

    /**
     * Writes a system record even with Audit=disabled (system.audit.disable found by the cronjob part, and the
     * alert it raises), and hands it to the sinks.
     *
     * @param string $name
     * @param array $data
     * @return string|null the event id
     */
    public static function forceWrite( $name, array $data )
    {
        try
        {
            $config = expAuditConfig::get();
            $keys = new expAuditKeys( $config );
            $keys->keys();
            $record = expAudit::internalRecord( $name, $data, 'system', $config );
            $written = expAudit::writerFor( $config, $keys )->append( 'system', array( $record ) );
            if ( class_exists( 'expAuditSinkRegistry' ) )
                expAuditSinkRegistry::dispatch( 'system', $written );
            if ( $name !== self::ALERT )
                self::evaluate( $written );
            return isset( $written[0]['id'] ) ? $written[0]['id'] : null;
        }
        catch ( Throwable $e )
        {
            if ( class_exists( 'eZDebug' ) )
                eZDebug::writeError( "Audit: $name could not be written: " . $e->getMessage(), __METHOD__ );
            return null;
        }
    }

    /**
     * The cronjob part's pass: new records since the last run, the "audit disabled" check, closed windows.
     *
     * @return array evaluated (records), alerts (count), disabled (bool: system.audit.disable written now)
     */
    public static function cronjob()
    {
        $out = array( 'evaluated' => 0, 'alerts' => 0, 'disabled' => false, 'enabled' => false );
        $config = expAuditConfig::get();
        $stateFile = $config['logDir'] . '/.state';
        $was = @file_get_contents( $stateFile );
        $now = $config['enabled'] ? 'enabled' : 'disabled';
        if ( $was !== false && trim( $was ) === 'enabled' && $now === 'disabled' )
        {
            self::forceWrite( 'system.audit.disable', array( 'object' => array( 'type' => 'audit', 'id' => 'Audit' ),
                                                             'reason' => 'state', 'before' => array( 'state' => 'enabled' ),
                                                             'after' => array( 'state' => 'disabled', 'found_by' => 'cronjob' ) ) );
            $out['disabled'] = true;
        }
        elseif ( $was !== false && trim( $was ) === 'disabled' && $now === 'enabled' )
        {
            expAudit::event( 'system.audit.enable', array( 'object' => array( 'type' => 'audit', 'id' => 'Audit' ),
                                                           'before' => array( 'state' => 'disabled' ), 'after' => array( 'state' => 'enabled', 'found_by' => 'cronjob' ) ) );
            $out['enabled'] = true;
        }
        if ( $was === false || trim( $was ) !== $now )
        {
            if ( expAuditWriter::ensureDirectory( $config['logDir'] ) )
            {
                $created = !is_file( $stateFile );
                @file_put_contents( $stateFile, $now );
                if ( $created )
                    expAuditWriter::ownLikeParent( $stateFile, 0640 );
            }
        }
        if ( !self::isEnabled() || !self::evaluatesIn( 'cronjob' ) )
            return $out;
        $records = self::readNew( $config );
        $out['evaluated'] = count( $records );
        if ( $records )
            $out['alerts'] = count( self::evaluate( $records ) );
        self::pruneStates();
        return $out;
    }

    /**
     * Records written since the cursor, moving the cursor. The first run starts at the end of the files.
     *
     * @param array $config
     * @return array[]
     */
    public static function readNew( array $config )
    {
        $dir = self::stateDir();
        $cursorFile = $dir . '/.cursor.json';
        $s = @file_get_contents( $cursorFile );
        $cursor = $s !== false ? json_decode( $s, true ) : null;
        $first = !is_array( $cursor );
        $cursor = is_array( $cursor ) ? $cursor : array();
        $records = array();
        foreach ( $config['channels'] as $channel )
        {
            $files = expAuditWriter::channelFiles( $config['logDir'], $channel );
            if ( !$files )
                continue;
            $c = isset( $cursor[$channel] ) ? $cursor[$channel] : null;
            $start = 0;
            if ( $c && in_array( $c['file'], $files, true ) )
                $start = array_search( $c['file'], $files, true );
            elseif ( $first )
                $start = count( $files ) - 1;
            for ( $i = $start; $i < count( $files ); $i++ )
            {
                $path = $config['logDir'] . '/' . $files[$i];
                $offset = ( $c && $files[$i] === $c['file'] ) ? (int)$c['offset'] : 0;
                $size = (int)@filesize( $path );
                if ( $first && $i === count( $files ) - 1 )
                    $offset = $size;
                $h = @fopen( $path, 'rb' );
                if ( !$h )
                    continue;
                fseek( $h, $offset );
                while ( ( $line = fgets( $h ) ) !== false )
                {
                    if ( substr( $line, -1 ) !== "\n" )
                        break;
                    $offset += strlen( $line );
                    $r = json_decode( $line, true );
                    if ( is_array( $r ) && isset( $r['name'] ) )
                        $records[] = $r;
                }
                fclose( $h );
                $cursor[$channel] = array( 'file' => $files[$i], 'offset' => $offset );
            }
        }
        if ( expAuditWriter::ensureDirectory( $dir ) )
        {
            $created = !is_file( $cursorFile );
            $tmp = $cursorFile . '.' . getmypid() . '.tmp';
            if ( @file_put_contents( $tmp, json_encode( $cursor ) ) !== false )
                @rename( $tmp, $cursorFile );
            if ( $created )
                expAuditWriter::ownLikeParent( $cursorFile, 0640 );
        }
        return $records;
    }

    /** Drops closed windows of every rule's state. */
    public static function pruneStates()
    {
        $now = (int)( microtime( true ) * 1000 );
        foreach ( self::rules() as $name => $rule )
        {
            if ( !is_file( self::stateDir() . '/' . $name . '.json' ) )
                continue;
            $state = expAuditAlertState::open( self::stateDir(), $name );
            $window = max( 0, (int)( isset( $rule['config']['Window'] ) ? $rule['config']['Window'] : 0 ) );
            $state->prune( $now, max( $window * 1000, 86400000 ) );
            $state->close();
        }
    }

    /**
     * Runs one rule over past records without firing (exp:audit alerts test <rule> --replay=<from>).
     *
     * @param string $rule
     * @param string $from YYYY-MM-DD
     * @return array records (count), alerts
     */
    public static function replay( $rule, $from )
    {
        $config = expAuditConfig::get();
        $records = array();
        foreach ( $config['channels'] as $channel )
        {
            foreach ( expAuditWriter::channelFiles( $config['logDir'], $channel ) as $f )
            {
                $p = expAuditWriter::parseFileName( $f );
                if ( strcmp( $p['date'], $from ) < 0 )
                    continue;
                $h = @fopen( $config['logDir'] . '/' . $f, 'rb' );
                while ( $h && ( $line = fgets( $h ) ) !== false )
                {
                    $r = json_decode( $line, true );
                    if ( is_array( $r ) && isset( $r['name'] ) )
                        $records[] = $r;
                }
                if ( $h )
                    fclose( $h );
            }
        }
        return array( 'records' => count( $records ), 'alerts' => self::evaluate( $records, false, $rule ) );
    }
}
