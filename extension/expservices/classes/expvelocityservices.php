<?php
/**
 * expvelocity: the Velocity server's state, read-only (setup/system_info).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expVelocityServices extends expServiceBase
{
    public static $services = array(
        'status' => array( 'summary' => 'Whether Velocity runs: processes, listening ports, engine', 'access' => array( 'setup', 'system_info' ), 'write' => false, 'args' => array( 'engine' => 'string' ), 'returns' => 'running, processes, listening, https, engine' ),
        'engines' => array( 'summary' => 'The engines Velocity knows and the configured default', 'access' => array( 'setup', 'system_info' ), 'write' => false, 'args' => array(), 'returns' => 'engines, default' ),
        'urls' => array( 'summary' => 'The URLs the server answers on', 'access' => array( 'setup', 'system_info' ), 'write' => false, 'args' => array(), 'returns' => 'list of label, url' ),
        'cache' => array( 'summary' => 'The Velocity response cache: files, bytes, last cleared', 'access' => array( 'setup', 'system_info' ), 'write' => false, 'args' => array(), 'returns' => 'cache status' ),
        'installed' => array( 'summary' => 'Whether Velocity is part of this installation', 'access' => array( 'setup', 'system_info' ), 'write' => false, 'args' => array(), 'returns' => 'installed' ),
    );

    protected static function engine( $engine = null )
    {
        if ( !class_exists( 'expVelocity' ) )
            throw new expServiceException( 'Velocity is not part of this installation', 404 );
        if ( $engine !== null && !in_array( $engine, expVelocity::engines(), true ) )
            throw new expServiceException( 'engine is one of ' . implode( ', ', expVelocity::engines() ), 400 );
        try
        {
            return expVelocity::create( 'velocity.ini', $engine );
        }
        catch ( Exception $e )
        {
            throw new expServiceException( 'Velocity is not available: ' . $e->getMessage(), 404 );
        }
    }

    public static function status( $args )
    {
        self::guard( __FUNCTION__ );
        $status = self::engine( self::arg( $args, 0, 'string', null ) )->status();
        // paths stay on the server
        unset( $status['script'], $status['log'], $status['pidFile'], $status['engine'] );
        return self::ok( $status );
    }

    public static function engines( $args )
    {
        self::guard( __FUNCTION__ );
        if ( !class_exists( 'expVelocity' ) )
            throw new expServiceException( 'Velocity is not part of this installation', 404 );
        return self::ok( array( 'engines' => expVelocity::engines(), 'default' => expVelocity::defaultEngine() ) );
    }

    public static function urls( $args )
    {
        self::guard( __FUNCTION__ );
        $list = array();
        foreach ( self::engine()->urls() as $u )
            $list[] = array( 'label' => $u[0], 'url' => $u[1] );
        return self::ok( $list, array( 'total' => count( $list ) ) );
    }

    public static function cache( $args )
    {
        self::guard( __FUNCTION__ );
        $r = expCacheManager::velocityCacheStatus();
        if ( isset( $r['data'] ) )
            unset( $r['data']['dir'], $r['data']['marker'] );
        return self::ok( $r );
    }

    public static function installed( $args )
    {
        self::guard( __FUNCTION__ );
        return self::ok( array( 'installed' => class_exists( 'expVelocity' ) ) );
    }
}
