<?php
/**
 * File containing the expDebugBarSummary class: the numbers of the Exp Debug bar's pinned summary.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Collects what the request cost, from what eZDebug already measured: page time, SQL statements and their time
 * (the *_query accumulators of the database drivers, else eZDB's statement counter), peak memory, templates used,
 * messages per level, included files, the accumulators and timing points, and who gets debug. Each figure has a
 * level (ok, warn, high) against [DebugBarSettings] Thresholds[] of debugbar.ini.
 *
 *   echo '<script type="application/json" id="exp-debug-summary">' . expDebugBarSummary::json() . '</script>';
 *
 * Nothing here starts a database connection, a session or a template engine that the request did not start.
 */
class expDebugBarSummary
{
    /**
     * @param eZDebug|null $debug
     * @param array|null $thresholds name => array( warn, high ) (null: debugbar.ini)
     * @return array
     */
    public static function collect( $debug = null, ?array $thresholds = null )
    {
        if ( $debug === null && class_exists( 'eZDebug' ) )
            $debug = eZDebug::instance();
        if ( $thresholds === null )
        {
            try
            {
                $thresholds = class_exists( 'expDebugBarRegistry' ) && class_exists( 'eZINI' ) ? ( new expDebugBarRegistry() )->thresholds() : array();
            }
            catch ( Exception $e )
            {
                $thresholds = array();
            }
        }
        $thresholds += array( 'time_ms' => array( 500, 1500 ), 'sql_count' => array( 100, 300 ), 'sql_ms' => array( 200, 800 ),
                              'memory_mb' => array( 64, 192 ), 'templates' => array( 150, 400 ) );

        $start = $debug && $debug->ScriptStart ? $debug->ScriptStart : ( isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? $_SERVER['REQUEST_TIME_FLOAT'] : microtime( true ) );
        $stop = $debug && $debug->ScriptStop ? $debug->ScriptStop : microtime( true );
        $totalMs = round( max( 0, $stop - $start ) * 1000, 1 );

        // accumulators
        $accumulators = array();
        $sqlCount = 0;
        $sqlMs = 0.0;
        $sqlFromAccumulators = false;
        if ( $debug && is_array( $debug->TimeAccumulatorList ) )
        {
            foreach ( $debug->TimeAccumulatorList as $key => $acc )
            {
                if ( !is_array( $acc ) || !empty( $acc['is_group'] ) )
                    continue;
                $ms = round( ( isset( $acc['time'] ) ? $acc['time'] : 0 ) * 1000, 2 );
                $count = isset( $acc['count'] ) ? (int)$acc['count'] : 0;
                $accumulators[] = array( 'key' => (string)$key, 'name' => isset( $acc['name'] ) ? (string)$acc['name'] : (string)$key,
                                         'group' => isset( $acc['in_group'] ) && $acc['in_group'] !== false ? (string)$acc['in_group'] : null,
                                         'time_ms' => $ms, 'count' => $count );
                if ( preg_match( '/_query$/', (string)$key ) && strpos( (string)$key, 'cluster' ) === false )
                {
                    $sqlCount += $count;
                    $sqlMs += $ms;
                    $sqlFromAccumulators = true;
                }
            }
        }
        if ( !$sqlFromAccumulators && class_exists( 'eZDB', false ) && eZDB::hasInstance() )
        {
            $db = eZDB::instance();
            $sqlCount = isset( $db->NumQueries ) ? (int)$db->NumQueries : 0;
        }

        // messages
        $messages = array( 'error' => 0, 'warning' => 0, 'notice' => 0, 'debug' => 0, 'strict' => 0, 'timing' => 0 );
        if ( $debug && is_array( $debug->DebugStrings ) )
        {
            $names = array( eZDebug::LEVEL_ERROR => 'error', eZDebug::LEVEL_WARNING => 'warning', eZDebug::LEVEL_NOTICE => 'notice',
                            eZDebug::LEVEL_DEBUG => 'debug', eZDebug::LEVEL_STRICT => 'strict', eZDebug::LEVEL_TIMING_POINT => 'timing' );
            foreach ( $debug->DebugStrings as $d )
            {
                if ( isset( $d['Level'], $names[$d['Level']] ) )
                    $messages[$names[$d['Level']]]++;
            }
        }
        $messageLevel = $messages['error'] ? 'high' : ( $messages['warning'] ? 'warn' : 'ok' );

        // templates
        $templates = 0;
        if ( class_exists( 'eZTemplate', false ) )
        {
            try
            {
                $templates = count( (array)eZTemplate::templatesUsageStatistics() );
            }
            catch ( Throwable $e )
            {
            }
        }

        // timing points
        $points = array();
        if ( $debug && is_array( $debug->TimePoints ) )
        {
            foreach ( $debug->TimePoints as $p )
            {
                $points[] = array( 'description' => isset( $p['Description'] ) ? (string)$p['Description'] : '',
                                   'time_ms' => isset( $p['Time'] ) ? round( ( $p['Time'] - $start ) * 1000, 2 ) : null,
                                   'memory' => isset( $p['MemoryUsage'] ) ? (int)$p['MemoryUsage'] : null );
            }
        }

        $peak = memory_get_peak_usage( true );
        $ini = class_exists( 'eZINI', false ) ? eZINI::instance() : null;
        return array(
            'time' => array( 'total_ms' => $totalMs, 'level' => self::level( $totalMs, $thresholds['time_ms'] ) ),
            'sql' => array( 'count' => $sqlCount, 'time_ms' => round( $sqlMs, 2 ),
                            'level' => self::worst( self::level( $sqlCount, $thresholds['sql_count'] ), self::level( $sqlMs, $thresholds['sql_ms'] ) ),
                            'output' => $ini ? $ini->variable( 'DatabaseSettings', 'SQLOutput' ) === 'enabled' : false,
                            'source' => $sqlFromAccumulators ? 'accumulators' : 'counter' ),
            'memory' => array( 'peak_bytes' => $peak, 'peak' => self::bytes( $peak ), 'limit' => (string)ini_get( 'memory_limit' ),
                               'level' => self::level( $peak / 1048576, $thresholds['memory_mb'] ) ),
            'templates' => array( 'count' => $templates, 'level' => self::level( $templates, $thresholds['templates'] ) ),
            'messages' => $messages + array( 'level' => $messageLevel ),
            'included_files' => count( get_included_files() ),
            'accumulators' => $accumulators,
            'timing_points' => $points,
            'debug' => array( 'enabled' => class_exists( 'eZDebug', false ) ? eZDebug::isDebugEnabled() : false,
                              'by_ip' => $ini ? $ini->variable( 'DebugSettings', 'DebugByIP' ) === 'enabled' : false,
                              'by_user' => $ini ? $ini->variable( 'DebugSettings', 'DebugByUser' ) === 'enabled' : false,
                              'ip_match' => isset( $GLOBALS['eZDebugIPMatch'] ) ? $GLOBALS['eZDebugIPMatch'] : null ),
            'engine' => self::engine(),
            'siteaccess' => isset( $GLOBALS['eZCurrentAccess']['name'] ) ? $GLOBALS['eZCurrentAccess']['name'] : null,
            'php' => PHP_VERSION,
            'thresholds' => $thresholds,
        );
    }

    /**
     * collect() as JSON that is safe inside a <script> element.
     *
     * @return string
     */
    public static function json( $debug = null )
    {
        try
        {
            $data = self::collect( $debug );
        }
        catch ( Throwable $e )
        {
            $data = array( 'error' => $e->getMessage() );
        }
        return json_encode( $data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR );
    }

    /** ok, warn or high for $value against array( warn, high ). */
    public static function level( $value, array $limits )
    {
        if ( $value >= $limits[1] )
            return 'high';
        if ( $value >= $limits[0] )
            return 'warn';
        return 'ok';
    }

    protected static function worst( $a, $b )
    {
        $rank = array( 'ok' => 0, 'warn' => 1, 'high' => 2 );
        return $rank[$a] >= $rank[$b] ? $a : $b;
    }

    /** '24 MB'. */
    public static function bytes( $bytes )
    {
        $units = array( 'B', 'KB', 'MB', 'GB' );
        $i = 0;
        $n = (float)$bytes;
        while ( $n >= 1024 && $i < count( $units ) - 1 )
        {
            $n /= 1024;
            $i++;
        }
        return ( $i ? round( $n, 1 ) : (int)$n ) . ' ' . $units[$i];
    }

    /** 'velocity' when the request runs in Velocity's server, else the SAPI ('fpm-fcgi', 'cli', ...). */
    public static function engine()
    {
        if ( defined( 'QBIX_WEBSERVER' ) || isset( $_SERVER['QBIX_WORKER'] ) || isset( $_SERVER['VELOCITY'] ) || class_exists( 'Q_WebServer', false ) )
            return 'velocity';
        return PHP_SAPI;
    }
}
