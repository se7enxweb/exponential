<?php
/**
 * The sink registry and delivery (doc/bc/6.0/audit.md, "Sinks (Q7)", "The sink registry").
 *
 *   [AuditSinkSettings] SinkClasses[<name>]=<class implementing expAuditSink>, SpoolDir
 *   [AuditSink_<name>]  the sink's own settings
 *   [AuditChannel_<c>]  Sinks[] the sinks a channel's records are copied to
 *
 * dispatch() runs after records are in the file (expAudit::write(), so a sink failure never loses a record) with
 * the records as written (privacy already applied): each record goes to its channel's Sinks[] and, for a
 * system.audit.alert, also to the sinks its rule names (after.sinks). Sinks that write a local socket (syslog
 * local/journald) deliver at once; network and mail sinks get the record appended to their spool, which
 * deliverSpools() empties from the audit cronjob part (and exp:audit sinks flush) with retries: RetryBackoff
 * seconds before the first retry, doubled each time, up to Retries; after the last retry the batch stays spooled
 * and system.audit.sink.failed is recorded, at most once per sink per hour. Records of critical severity or above
 * are also tried right after the response on PHP-FPM (fastcgi_finish_request()) and at the end of a command.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditSinkRegistry
{
    /** @var array name => expAuditSink, per settings hash */
    protected static $sinks = array();

    /** @var string|null */
    protected static $sinksFor = null;

    /** @var bool dispatch() is running (a sink failure recorded while delivering is not dispatched again) */
    protected static $dispatching = false;

    /** @var bool The after-response delivery of critical records is registered for this request */
    protected static $urgentRegistered = false;

    /** @var array sink name => true: spools holding critical records of this request */
    protected static $urgent = array();

    /** @var int|null Fixed clock (tests) */
    public static $now = null;

    /** @return array name => class: the configured sinks (the shipped three when nothing is configured) */
    public static function classes()
    {
        $classes = expAuditConfig::hash( 'AuditSinkSettings', 'SinkClasses' );
        if ( !$classes )
            $classes = array( 'syslog' => 'expAuditSyslogSink', 'webhook' => 'expAuditWebhookSink', 'mail' => 'expAuditMailSink' );
        return $classes;
    }

    /**
     * A sink by name.
     *
     * @param string $name
     * @return expAuditSink|null
     */
    public static function get( $name )
    {
        $config = expAuditConfig::get();
        if ( self::$sinksFor !== $config['hash'] )
        {
            self::$sinks = array();
            self::$sinksFor = $config['hash'];
        }
        if ( array_key_exists( $name, self::$sinks ) )
            return self::$sinks[$name];
        $classes = self::classes();
        $sink = null;
        if ( isset( $classes[$name] ) && class_exists( $classes[$name] ) )
        {
            $class = $classes[$name];
            $sink = is_subclass_of( $class, 'expAuditSinkBase' ) ? new $class( $name ) : new $class();
            if ( !$sink instanceof expAuditSink )
                $sink = null;
        }
        return self::$sinks[$name] = $sink;
    }

    /** Forgets the sink objects (tests, changed settings). */
    public static function reset()
    {
        self::$sinks = array();
        self::$sinksFor = null;
        self::$urgent = array();
    }

    /** @return string The spool directory */
    public static function spoolDir()
    {
        return expAuditConfig::path( expAuditConfig::value( 'AuditSinkSettings', 'SpoolDir', 'log/audit/spool' ) );
    }

    /** @return expAuditSpool */
    public static function spool( $name )
    {
        return new expAuditSpool( self::spoolDir(), $name );
    }

    /**
     * The sinks of a channel ([AuditChannel_<c>] Sinks[]).
     *
     * @param string $channel
     * @return string[]
     */
    public static function channelSinks( $channel )
    {
        return expAuditConfig::lists( 'AuditChannel_' . $channel, 'Sinks' );
    }

    /**
     * Copies written records to their sinks. Never throws.
     *
     * @param string $channel
     * @param array[] $records the records as written
     * @return array sink name => count handed over (delivered or spooled)
     */
    public static function dispatch( $channel, array $records )
    {
        $out = array();
        if ( self::$dispatching || !$records || !expAuditConfig::sinksAllowed() )
            return $out;
        self::$dispatching = true;
        try
        {
            $channelSinks = self::channelSinks( $channel );
            $bySink = array();
            foreach ( $records as $r )
            {
                unset( $r['file'] );
                $targets = $channelSinks;
                if ( isset( $r['name'] ) && $r['name'] === 'system.audit.alert' && !empty( $r['after']['sinks'] ) )
                    $targets = array_merge( $targets, (array)$r['after']['sinks'] );
                foreach ( array_unique( $targets ) as $name )
                    $bySink[$name][] = $r;
            }
            foreach ( $bySink as $name => $list )
            {
                $sink = self::get( $name );
                if ( !$sink )
                    continue;
                if ( $sink instanceof expAuditSinkBase )
                    $list = array_values( array_filter( $list, array( $sink, 'wants' ) ) );
                if ( !$list )
                    continue;
                try
                {
                    if ( $sink instanceof expAuditSinkBase && $sink->isSpooled() )
                    {
                        if ( self::spool( $name )->append( $list ) )
                            $out[$name] = count( $list );
                        foreach ( $list as $r )
                        {
                            if ( expAuditTaxonomy::rank( isset( $r['severity'] ) ? $r['severity'] : 'info' ) <= 2 )
                            {
                                self::$urgent[$name] = true;
                                self::registerUrgent();
                            }
                        }
                    }
                    elseif ( $sink->problem() === '' )
                    {
                        $n = (int)$sink->deliver( $list );
                        $out[$name] = $n;
                        if ( $n < count( $list ) )
                            self::spool( $name )->append( array_slice( $list, $n ) );
                    }
                }
                catch ( Throwable $e )
                {
                    if ( class_exists( 'eZDebug' ) )
                        eZDebug::writeError( "Audit sink $name: " . $e->getMessage(), __METHOD__ );
                }
            }
        }
        catch ( Throwable $e )
        {
            if ( class_exists( 'eZDebug' ) )
                eZDebug::writeError( 'Audit sinks: ' . $e->getMessage(), __METHOD__ );
        }
        finally
        {
            self::$dispatching = false;
        }
        return $out;
    }

    /**
     * Delivers the spools (the cronjob part, exp:audit sinks flush).
     *
     * @param bool $force deliver now: ignore BatchSeconds and the retry backoff
     * @param string|null $only one sink
     * @return array sink => delivered, remaining, failed, waiting (a reason), error
     */
    public static function deliverSpools( $force = false, $only = null )
    {
        $out = array();
        if ( !expAuditConfig::sinksAllowed() )
            return $out;
        foreach ( array_keys( self::classes() ) as $name )
        {
            if ( $only !== null && $name !== $only )
                continue;
            $spool = self::spool( $name );
            if ( !is_file( $spool->path() ) || !filesize( $spool->path() ) )
                continue;
            $out[$name] = self::deliverSpool( $name, $force );
        }
        return $out;
    }

    /**
     * Delivers one sink's spool.
     *
     * @param string $name
     * @param bool $force
     * @return array delivered, remaining, failed, waiting, error, attempts
     */
    public static function deliverSpool( $name, $force = false )
    {
        $sink = self::get( $name );
        $spool = self::spool( $name );
        $state = $spool->state();
        $now = self::$now !== null ? (int)self::$now : time();
        $result = array( 'delivered' => 0, 'remaining' => $spool->count(), 'failed' => false, 'waiting' => null, 'error' => null,
                         'attempts' => isset( $state['attempts'] ) ? (int)$state['attempts'] : 0 );
        if ( !$sink )
        {
            $result['waiting'] = 'no such sink';
            return $result;
        }
        $problem = $sink->problem();
        if ( $problem !== '' && !( $sink instanceof expAuditWebhookSink && strpos( $problem, 'SigningSecret' ) !== false ) )
        {
            $result['waiting'] = $problem;
            return $result;
        }
        if ( !$force && isset( $state['next'] ) && $now < (int)$state['next'] )
        {
            $result['waiting'] = 'retry at ' . gmdate( 'Y-m-d\TH:i:s\Z', (int)$state['next'] );
            return $result;
        }
        $batchSize = $sink instanceof expAuditSinkBase ? $sink->batchSize() : 100;
        if ( !$force && $sink instanceof expAuditWebhookSink && $result['remaining'] < $batchSize )
        {
            // BatchSeconds: a partial batch waits until its oldest record is that old
            $first = $spool->peek( 1 );
            $oldest = isset( $first[0]['time'] ) ? strtotime( $first[0]['time'] ) : 0;
            $wait = max( 0, (int)$sink->setting( 'BatchSeconds' ) );
            if ( $oldest && $now - $oldest < $wait )
            {
                $result['waiting'] = 'batch not full yet';
                return $result;
            }
        }
        $error = null;
        $drain = $spool->drain( $batchSize, function ( array $records ) use ( $sink, &$error ) {
            $n = $sink->deliver( $records );
            if ( $n < count( $records ) )
            {
                $error = isset( $sink->lastError ) && $sink->lastError ? $sink->lastError : 'delivered ' . $n . ' of ' . count( $records );
                return false;
            }
            return true;
        } );
        $result['delivered'] = $drain['delivered'];
        $result['remaining'] = $drain['remaining'];
        $result['failed'] = $drain['failed'];
        $result['error'] = $error;
        if ( $drain['failed'] )
        {
            $attempts = isset( $state['attempts'] ) ? (int)$state['attempts'] + 1 : 1;
            $retries = $sink instanceof expAuditSinkBase ? max( 0, (int)( $sink->setting( 'Retries' ) ?? 5 ) ) : 5;
            $backoff = $sink instanceof expAuditSinkBase ? max( 1, (int)( $sink->setting( 'RetryBackoff' ) ?? 30 ) ) : 30;
            $state['attempts'] = $attempts;
            $state['next'] = $now + $backoff * ( 1 << min( 20, max( 0, min( $attempts, max( 1, $retries ) ) - 1 ) ) );
            $state['last_error'] = $error;
            $state['last_failure'] = $now;
            $result['attempts'] = $attempts;
            if ( $attempts >= max( 1, $retries ) && ( !isset( $state['reported'] ) || $now - (int)$state['reported'] >= 3600 ) )
            {
                $state['reported'] = $now;
                $spool->saveState( $state );
                if ( class_exists( 'expAudit' ) )
                    expAudit::event( 'system.audit.sink.failed', array(
                        'object' => array( 'type' => 'sink', 'id' => $name ),
                        'result' => 'failed', 'reason' => 'error',
                        'after' => array( 'sink' => $name, 'error' => (string)$error, 'attempts' => $attempts, 'spooled' => $drain['remaining'] ) ) );
                return $result;
            }
        }
        else
        {
            $state['attempts'] = 0;
            unset( $state['next'] );
            if ( $drain['delivered'] )
                $state['last_delivery'] = $now;
        }
        $spool->saveState( $state );
        return $result;
    }

    /**
     * Every sink with its problem, spool size and last delivery (exp:audit sinks list, the console's settings).
     *
     * @return array name => class, problem, spooled, last_delivery, last_error, channels
     */
    public static function status()
    {
        $config = expAuditConfig::get();
        $out = array();
        foreach ( self::classes() as $name => $class )
        {
            $sink = self::get( $name );
            $spool = self::spool( $name );
            $state = $spool->state();
            $channels = array();
            foreach ( $config['channels'] as $c )
                if ( in_array( $name, self::channelSinks( $c ), true ) )
                    $channels[] = $c;
            $out[$name] = array(
                'class' => $class,
                'problem' => $sink ? $sink->problem() : "the class $class does not exist or does not implement expAuditSink",
                'spooled' => $spool->count(),
                'last_delivery' => isset( $state['last_delivery'] ) ? gmdate( 'Y-m-d\TH:i:s\Z', (int)$state['last_delivery'] ) : null,
                'last_error' => isset( $state['last_error'] ) ? $state['last_error'] : null,
                'channels' => $channels,
            );
        }
        return $out;
    }

    /**
     * Sends a test record through a sink now (exp:audit sinks test <name>), bypassing its spool and filters.
     *
     * @param string $name
     * @return array delivered (int), problem, error
     */
    public static function test( $name )
    {
        $sink = self::get( $name );
        if ( !$sink )
            return array( 'delivered' => 0, 'problem' => 'no such sink', 'error' => null );
        $now = self::$now !== null ? self::$now * 1000 : (int)( microtime( true ) * 1000 );
        $record = array( 'v' => 1, 'id' => expAudit::ulid( $now ), 'name' => 'system.audit.sink.test', 'channel' => 'system',
                         'time' => expAudit::timeString( $now ), 'severity' => 'notice', 'verb' => 'test',
                         'object' => array( 'type' => 'sink', 'id' => $name ), 'result' => 'success',
                         'request' => array( 'id' => expAudit::requestId(), 'host' => gethostname() ?: '-', 'pid' => getmypid() ),
                         'x' => array( 'test' => true ) );
        $n = (int)$sink->deliver( array( $record ) );
        return array( 'delivered' => $n, 'problem' => $sink->problem(), 'error' => isset( $sink->lastError ) ? $sink->lastError : null,
                      'record' => $record );
    }

    /** Registers the after-response delivery of critical records (once per request). */
    protected static function registerUrgent()
    {
        if ( self::$urgentRegistered || expAuditConfig::isOverridden() )
            return;
        // Velocity's persistent workers: the next cronjob run delivers (no shutdown function per request there)
        if ( expAudit::engine() === 'velocity' )
            return;
        self::$urgentRegistered = true;
        register_shutdown_function( array( __CLASS__, 'deliverUrgent' ) );
    }

    /** After the response (PHP-FPM) or at the end of a command: tries the spools that hold critical records. */
    public static function deliverUrgent()
    {
        self::$urgentRegistered = false;
        $urgent = self::$urgent;
        self::$urgent = array();
        if ( !$urgent )
            return;
        try
        {
            if ( function_exists( 'fastcgi_finish_request' ) && PHP_SAPI === 'fpm-fcgi' )
                @fastcgi_finish_request();
            foreach ( array_keys( $urgent ) as $name )
            {
                $sink = self::get( $name );
                if ( $sink instanceof expAuditMailSink )
                    continue; // mail always from the cronjob part
                self::deliverSpool( $name, false );
            }
        }
        catch ( Throwable $e )
        {
        }
    }
}
