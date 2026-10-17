<?php
/**
 * File containing the expVelocityRestartUnit class.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * Runs exp:velocity restart as one detached unit: the stop and the start in a
 * process that does not belong to the caller.
 *
 * On 2026-10-17 a deploy's caller was killed between the stop and the start,
 * and the server stayed down for 28 minutes. Now the command re-launches
 * itself in its own session (setsid -f) and, where systemd runs and we are
 * root, in its own transient scope (systemd-run --scope --unit=vc-restart-<ts>),
 * so neither a killed caller, its process group, a closed terminal (SIGHUP)
 * nor the end of a login session takes the restart down with it. The server it
 * starts lives in that scope too, not in the caller's session.
 *
 * The caller still waits for the outcome and prints exactly what the restart
 * printed, with its exit code, so scripts and the deploy see no difference.
 * If the caller goes away, the restart finishes on its own.
 *
 * The detached process writes restart-in-progress.json (expVelocityMarkers)
 * for every engine it restarts and removes it when it is done; a marker left
 * behind names a process that is gone, so the watchdog may start the server.
 *
 * Off with [ControlSettings] DetachedRestart=disabled in velocity.ini, or
 * EXP_VELOCITY_DETACHED_RESTART=0 in the environment.
 */
class expVelocityRestartUnit
{
    /** Set in the detached process: the token that names its result file. */
    const TOKEN_ENV = 'EXP_VELOCITY_RESTART_TOKEN';

    /** 0 turns detaching off for one call. */
    const SWITCH_ENV = 'EXP_VELOCITY_DETACHED_RESTART';

    /** Seconds the detached process gets to write its marker. */
    const BEGIN_TIMEOUT = 30;

    /** Seconds the caller waits for the outcome at most. */
    const WAIT_TIMEOUT = 900;

    /** @var string */
    protected $runDir;

    /** @var string[] marker directories the child holds */
    protected static $childDirs = array();

    /** @var bool */
    protected static $childDone = false;

    /**
     * @param string $runDir where the result and log of the detached process go
     */
    public function __construct( $runDir )
    {
        $this->runDir = rtrim( (string)$runDir, '/' );
    }

    /** @return bool whether this process is the detached restart */
    public static function isChild()
    {
        return self::validToken( (string)getenv( self::TOKEN_ENV ) );
    }

    /**
     * @param string $token
     * @return bool
     */
    public static function validToken( $token )
    {
        return (bool)preg_match( '/^[a-f0-9]{8,32}\z/', (string)$token );
    }

    /**
     * Whether restart detaches: on unless switched off, and only where it can.
     *
     * @param string $setting [ControlSettings] DetachedRestart
     * @return bool
     */
    public static function enabled( $setting = 'enabled' )
    {
        if ( self::isChild() )
            return false;
        $switch = getenv( self::SWITCH_ENV );
        if ( $switch !== false && in_array( strtolower( trim( $switch ) ), array( '0', 'false', 'no', 'disabled', 'off' ), true ) )
            return false;
        if ( in_array( strtolower( trim( (string)$setting ) ), array( 'disabled', 'false', 'no', '0', 'off' ), true ) )
            return false;
        if ( !function_exists( 'exec' ) || in_array( 'exec', array_map( 'trim', explode( ',', (string)ini_get( 'disable_functions' ) ) ), true ) )
            return false;
        return self::findExecutable( 'setsid' ) !== false;
    }

    /**
     * The shell command that starts the detached restart. Pure.
     *
     * @param string $php the PHP binary
     * @param string $script bin/php/velocity.php, absolute
     * @param array $args the arguments after the script
     * @param string $token
     * @param string $log where its output goes
     * @param string $setsid the setsid binary
     * @param string|false $systemdRun the systemd-run binary, or false for none
     * @param string $unit the scope's name
     * @return string
     */
    public static function detachedCommand( $php, $script, array $args, $token, $log, $setsid, $systemdRun, $unit )
    {
        $inner = array_merge( array( $setsid, '-f', 'env', self::TOKEN_ENV . '=' . $token, $php, $script ), array_values( $args ) );
        $prefix = array();
        if ( $systemdRun !== false && $systemdRun !== '' )
            $prefix = array( $systemdRun, '--scope', '--quiet', '--collect', '--unit=' . $unit, '--' );
        return implode( ' ', array_map( 'escapeshellarg', array_merge( $prefix, $inner ) ) )
             . ' > ' . escapeshellarg( $log ) . ' 2>&1 < /dev/null';
    }

    /**
     * The scope's name for a token: vc-restart-<date>-<token>.
     *
     * @param string $token
     * @param int|null $now
     * @return string
     */
    public static function unitName( $token, $now = null )
    {
        return 'vc-restart-' . date( 'Ymd-His', $now === null ? time() : (int)$now ) . '-' . substr( $token, 0, 8 );
    }

    /** @return string */
    public function resultFile( $token )
    {
        return $this->runDir . '/restart-' . $token . '.result.json';
    }

    /** @return string */
    public function logFile( $token )
    {
        return $this->runDir . '/restart-' . $token . '.log';
    }

    /**
     * Start the restart detached and wait for its outcome.
     *
     * @param string $script bin/php/velocity.php, absolute
     * @param array $args the command's arguments, as given
     * @param array $markerDirs the run directories of the engines it restarts
     * @return array|null array( exit code, output ) or null when it could not
     *                    be detached (the caller then restarts in place, as before)
     */
    public function delegate( $script, array $args, array $markerDirs )
    {
        if ( !is_dir( $this->runDir ) && !@mkdir( $this->runDir, 0775, true ) && !is_dir( $this->runDir ) )
            return null;
        $token = bin2hex( random_bytes( 8 ) );
        $log = $this->logFile( $token );
        $setsid = self::findExecutable( 'setsid' );
        if ( $setsid === false )
            return null;
        $systemdRun = self::systemdScopeAvailable() ? self::findExecutable( 'systemd-run' ) : false;
        $unit = self::unitName( $token );

        $code = 1;
        $out = array();
        @exec( self::detachedCommand( PHP_BINARY, $script, $args, $token, $log, $setsid, $systemdRun, $unit ), $out, $code );
        if ( $code !== 0 && $systemdRun !== false )
        {
            // No scope (a container, a user without the right): the session alone.
            $unit = '';
            @exec( self::detachedCommand( PHP_BINARY, $script, $args, $token, $log, $setsid, false, $unit ), $out, $code );
        }
        if ( $code !== 0 )
            return null;

        return $this->wait( $token, $markerDirs );
    }

    /**
     * Wait for the detached restart's result.
     *
     * @param string $token
     * @param array $markerDirs
     * @param int $timeout
     * @return array array( exit code, output )
     */
    public function wait( $token, array $markerDirs, $timeout = self::WAIT_TIMEOUT )
    {
        $result = $this->resultFile( $token );
        $log = $this->logFile( $token );
        $begun = microtime( true );
        $pid = 0;
        while ( true )
        {
            clearstatcache();
            if ( is_file( $result ) )
            {
                $data = json_decode( (string)@file_get_contents( $result ), true );
                $output = (string)@file_get_contents( $log );
                @unlink( $result );
                @unlink( $log );
                return array( is_array( $data ) && isset( $data['code'] ) ? (int)$data['code'] : 1, $output );
            }
            if ( !$pid )
                $pid = self::childPid( $token, $markerDirs );
            $elapsed = microtime( true ) - $begun;
            if ( $pid && !expVelocityMarkers::pidAlive( $pid ) )
            {
                // Its result may have been written just before it ended.
                clearstatcache();
                if ( is_file( $result ) )
                    continue;
                return array( 1, (string)@file_get_contents( $log )
                    . "velocity: the detached restart (pid $pid) ended without a result; see $log."
                    . " exp:velocity status shows what runs, exp:velocity start starts it\n" );
            }
            if ( !$pid && $elapsed > self::BEGIN_TIMEOUT )
                return array( 1, (string)@file_get_contents( $log )
                    . 'velocity: the detached restart did not begin within ' . self::BEGIN_TIMEOUT . "s; see $log\n" );
            if ( $elapsed > $timeout )
                return array( 1, (string)@file_get_contents( $log )
                    . "velocity: still restarting after {$timeout}s (pid $pid), in the background; see $log\n" );
            usleep( 250000 );
        }
    }

    /**
     * The detached process's pid, from its restart marker.
     *
     * @param string $token
     * @param array $markerDirs
     * @return int
     */
    protected static function childPid( $token, array $markerDirs )
    {
        foreach ( $markerDirs as $dir )
        {
            $marker = ( new expVelocityMarkers( $dir ) )->read( expVelocityMarkers::RESTART );
            if ( is_array( $marker ) && isset( $marker['token'] ) && $marker['token'] === $token && !empty( $marker['pid'] ) )
                return (int)$marker['pid'];
        }
        return 0;
    }

    /**
     * In the detached process: mark every engine it restarts as restarting.
     * The markers go again when it is done, or when it ends any other way.
     *
     * @param array $markerDirs
     * @param string $by
     */
    public static function beginChild( array $markerDirs, $by = 'restart' )
    {
        if ( !self::isChild() )
            return;
        $token = (string)getenv( self::TOKEN_ENV );
        $unit = '';
        $cgroup = (string)@file_get_contents( '/proc/self/cgroup' );
        if ( preg_match( '#/(vc-restart-[A-Za-z0-9-]+)\.scope#', $cgroup, $m ) )
            $unit = $m[1];
        self::$childDirs = array_values( array_unique( $markerDirs ) );
        foreach ( self::$childDirs as $dir )
            ( new expVelocityMarkers( $dir ) )->write( expVelocityMarkers::RESTART, array(
                'started' => time(), 'pid' => getmypid(), 'unit' => $unit !== '' ? $unit : null,
                'by' => (string)$by, 'token' => $token ) );
        self::sweep( $markerDirs );
        register_shutdown_function( array( __CLASS__, 'childShutdown' ) );
    }

    /**
     * In the detached process: the restart is done.
     *
     * @param bool $ok
     */
    public static function childDone( $ok )
    {
        if ( !self::isChild() || self::$childDone )
            return;
        self::$childDone = true;
        foreach ( self::$childDirs as $dir )
            ( new expVelocityMarkers( $dir ) )->clear( expVelocityMarkers::RESTART );
        $unit = new self( isset( self::$childDirs[0] ) ? self::$childDirs[0] : '.' );
        $file = $unit->resultFile( (string)getenv( self::TOKEN_ENV ) );
        @file_put_contents( $file . '.tmp', json_encode( array( 'code' => $ok ? 0 : 1, 'pid' => getmypid(), 'ended' => time() ) ) );
        @rename( $file . '.tmp', $file );
    }

    /** Ended without childDone (a fatal error, an exit elsewhere): failed. */
    public static function childShutdown()
    {
        if ( !self::$childDone )
            self::childDone( false );
    }

    /**
     * Results and logs nobody read (the caller was gone), after a day.
     *
     * @param array $dirs
     */
    protected static function sweep( array $dirs )
    {
        foreach ( $dirs as $dir )
            foreach ( (array)glob( rtrim( $dir, '/' ) . '/restart-*.{log,json}', GLOB_BRACE ) as $file )
                if ( is_file( $file ) && time() - (int)@filemtime( $file ) > 86400 )
                    @unlink( $file );
    }

    /** @return bool whether a transient systemd scope can be made here */
    public static function systemdScopeAvailable()
    {
        return is_dir( '/run/systemd/system' ) && function_exists( 'posix_geteuid' ) && posix_geteuid() === 0
            && self::findExecutable( 'systemd-run' ) !== false;
    }

    /**
     * @param string $name
     * @return string|false
     */
    public static function findExecutable( $name )
    {
        $path = (string)getenv( 'PATH' );
        $dirs = array_merge( $path !== '' ? explode( PATH_SEPARATOR, $path ) : array(),
                             array( '/usr/local/sbin', '/usr/local/bin', '/usr/sbin', '/usr/bin', '/sbin', '/bin' ) );
        foreach ( $dirs as $dir )
            if ( $dir !== '' && is_file( $dir . '/' . $name ) && is_executable( $dir . '/' . $name ) )
                return $dir . '/' . $name;
        return false;
    }
}
