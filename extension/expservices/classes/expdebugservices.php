<?php
/**
 * expdebug: the summary of the request's debug output (as the Exp Debug bar), settings read-only.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expDebugServices extends expServiceBase
{
    public static $services = array(
        'summary' => array( 'summary' => 'Time, SQL, memory, templates and warnings of this request with their levels (the debug bar summary)',
            'access' => array( 'setup', 'setup' ), 'write' => false, 'args' => array(), 'returns' => 'summary of the request' ),
        'enabled' => array( 'summary' => 'Whether debug output is enabled for this request', 'access' => array( 'setup', 'setup' ), 'write' => false, 'args' => array(), 'returns' => 'enabled' ),
        'level' => array( 'summary' => 'The debug level settings of the site', 'access' => array( 'setup', 'setup' ), 'write' => false, 'args' => array(), 'returns' => 'DebugOutput, DebugByIP, level' ),
        'thresholds' => array( 'summary' => 'The warn and high thresholds of the debug bar', 'access' => array( 'setup', 'setup' ), 'write' => false, 'args' => array(), 'returns' => 'map of name to warn, high' ),
        'engine' => array( 'summary' => 'Which engine serves this request (Apache, Velocity, FrankenPHP, CLI)', 'access' => 'user', 'write' => false, 'args' => array(), 'returns' => 'engine' ),
        'phpversion' => array( 'summary' => 'The PHP version and SAPI of this request', 'access' => 'user', 'write' => false, 'args' => array(), 'returns' => 'version, sapi' ),
    );

    public static function summary( $args )
    {
        self::guard( __FUNCTION__ );
        return self::ok( expDebugBarSummary::collect() );
    }

    public static function enabled( $args )
    {
        self::guard( __FUNCTION__ );
        return self::ok( array( 'enabled' => (bool)eZDebug::isDebugEnabled() ) );
    }

    public static function level( $args )
    {
        self::guard( __FUNCTION__ );
        $ini = eZINI::instance();
        return self::ok( array( 'debug_output' => $ini->variable( 'DebugSettings', 'DebugOutput' ),
                               'debug_by_ip' => $ini->variable( 'DebugSettings', 'DebugByIP' ),
                               'level' => $ini->hasVariable( 'DebugSettings', 'DebugLevel' ) ? $ini->variable( 'DebugSettings', 'DebugLevel' ) : null ) );
    }

    public static function thresholds( $args )
    {
        self::guard( __FUNCTION__ );
        $t = ( new expDebugBarRegistry() )->thresholds();
        return self::ok( $t, array( 'total' => count( $t ) ) );
    }

    public static function engine( $args )
    {
        self::guard( __FUNCTION__ );
        return self::ok( array( 'engine' => expDebugBarSummary::engine() ) );
    }

    public static function phpversion( $args )
    {
        self::guard( __FUNCTION__ );
        return self::ok( array( 'version' => PHP_VERSION, 'sapi' => PHP_SAPI ) );
    }
}
