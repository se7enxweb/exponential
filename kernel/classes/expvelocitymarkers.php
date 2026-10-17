<?php
/**
 * File containing the expVelocityMarkers class.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * The small state files that say why an engine is not running.
 *
 * A server that is down is either down on purpose or by accident, and only
 * the second must be brought back. Two files next to the engine's pid file
 * (var/vc/<engine>/run/) tell them apart:
 *
 *   stopped.json              written by an explicit exp:velocity stop or kill,
 *                             removed by start, restart and graceful
 *   restart-in-progress.json  written by the detached restart before it stops
 *                             the server, removed once its start has finished
 *
 * A restart whose caller was killed half-way (2026-10-17: stopped, never
 * started, 28 minutes of connection refused) leaves no stopped.json, and its
 * restart-in-progress.json names a process that is gone or a time long past,
 * so the watchdog knows it may start the server.
 *
 * The run directory and not /run: an explicit stop survives a reboot.
 */
class expVelocityMarkers
{
    /** An explicit stop or kill: the server stays down until started. */
    const STOPPED = 'stopped';

    /** A detached stop-and-start is running; nobody else starts the server. */
    const RESTART = 'restart-in-progress';

    /** How long a restart may take before its marker no longer holds others back. */
    const DEFAULT_RESTART_TIMEOUT = 300;

    /** @var string */
    protected $dir;

    /**
     * @param string $dir the directory the markers live in
     */
    public function __construct( $dir )
    {
        $this->dir = rtrim( (string)$dir, '/' );
    }

    /**
     * The markers of an engine: next to its pid file.
     *
     * @param expVelocity $velocity
     * @return expVelocityMarkers
     */
    public static function forVelocity( expVelocity $velocity )
    {
        return new self( dirname( $velocity->pidFile() ) );
    }

    /** @return string */
    public function dir()
    {
        return $this->dir;
    }

    /**
     * @param string $name STOPPED, RESTART or another short name
     * @return string
     */
    public function path( $name )
    {
        return $this->dir . '/' . preg_replace( '/[^A-Za-z0-9._-]/', '', (string)$name ) . '.json';
    }

    /**
     * Write a marker atomically (a reader never sees half of one).
     *
     * @param string $name
     * @param array $data
     * @return bool
     */
    public function write( $name, array $data )
    {
        if ( !is_dir( $this->dir ) && !eZDir::mkdir( $this->dir, false, true ) && !is_dir( $this->dir ) )
            return false;
        $path = $this->path( $name );
        $tmp = $path . '.' . getmypid() . '.tmp';
        if ( @file_put_contents( $tmp, json_encode( $data ) . "\n" ) === false )
            return false;
        if ( !@rename( $tmp, $path ) )
        {
            @unlink( $tmp );
            return false;
        }
        return true;
    }

    /**
     * @param string $name
     * @return array|null null when there is none; an unreadable one is an empty array
     */
    public function read( $name )
    {
        $path = $this->path( $name );
        if ( !is_file( $path ) )
            return null;
        $data = json_decode( (string)@file_get_contents( $path ), true );
        return is_array( $data ) ? $data : array();
    }

    /**
     * @param string $name
     * @return bool true when it is gone
     */
    public function clear( $name )
    {
        $path = $this->path( $name );
        return !file_exists( $path ) || @unlink( $path );
    }

    /**
     * Mark the server as stopped on purpose.
     *
     * @param string $by stop or kill
     * @return bool
     */
    public function markStopped( $by )
    {
        $user = function_exists( 'posix_getpwuid' ) && function_exists( 'posix_geteuid' ) ? posix_getpwuid( posix_geteuid() ) : false;
        return $this->write( self::STOPPED, array( 'since' => time(), 'by' => (string)$by,
                                                   'user' => is_array( $user ) ? $user['name'] : (string)get_current_user(),
                                                   'pid' => getmypid() ) );
    }

    /** @return bool whether an explicit stop holds the server down */
    public function isStopped()
    {
        return $this->read( self::STOPPED ) !== null;
    }

    /**
     * Whether a restart is under way: its marker is there, its process alive
     * and it is younger than $timeout.
     *
     * @param int $timeout seconds
     * @param int|null $now
     * @return bool
     */
    public function restartInProgress( $timeout = self::DEFAULT_RESTART_TIMEOUT, $now = null )
    {
        $restart = $this->read( self::RESTART );
        if ( $restart === null )
            return false;
        return self::restartHolds( $restart, self::pidAlive( isset( $restart['pid'] ) ? (int)$restart['pid'] : 0 ),
                                   $now === null ? time() : (int)$now, (int)$timeout );
    }

    /**
     * Whether a restart marker still holds others back. Pure.
     *
     * @param array $restart the marker
     * @param bool $pidAlive whether the process it names runs
     * @param int $now
     * @param int $timeout
     * @return bool
     */
    public static function restartHolds( array $restart, $pidAlive, $now, $timeout )
    {
        if ( !$pidAlive )
            return false;
        $started = isset( $restart['started'] ) ? (int)$restart['started'] : 0;
        if ( $timeout <= 0 )
            $timeout = self::DEFAULT_RESTART_TIMEOUT;
        return $started > 0 && $now - $started < $timeout;
    }

    /**
     * Whether a server that should run is to be started now. Pure: the
     * watchdog's whole decision, testable without a server.
     *
     * @param bool $running whether the engine runs (and answers)
     * @param array|null $stopped the STOPPED marker, or null
     * @param array|null $restart the RESTART marker, or null
     * @param bool $restartPidAlive whether the restart marker's process runs
     * @param int $now
     * @param int $timeout seconds a restart may take
     * @return bool
     */
    public static function shouldStart( $running, $stopped, $restart, $restartPidAlive, $now, $timeout )
    {
        if ( $running )
            return false;
        if ( $stopped !== null )
            return false;
        if ( is_array( $restart ) && self::restartHolds( $restart, (bool)$restartPidAlive, (int)$now, (int)$timeout ) )
            return false;
        return true;
    }

    /**
     * @param int $pid
     * @return bool
     */
    public static function pidAlive( $pid )
    {
        $pid = (int)$pid;
        if ( $pid <= 0 )
            return false;
        if ( function_exists( 'posix_kill' ) )
            return @posix_kill( $pid, 0 ) || ( function_exists( 'posix_get_last_error' ) && posix_get_last_error() === 1 );
        return is_dir( '/proc/' . $pid );
    }
}
