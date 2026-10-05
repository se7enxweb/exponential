<?php
/**
 * File containing the expNotificationJob class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A notification run for the status page ("Run now"), in the background.
 *
 * The page starts exp:notification:run detached (expProcessTools finds PHP and setsid), the command writes
 * its lines to var/<var dir>/notification/jobs/<id>.jsonl, and the page reads them from there, as the preload
 * and update pages do. A run that has written nothing for two minutes and not ended is shown as stopped.
 */
class expNotificationJob
{
    const ID_PATTERN = '#^[a-f0-9]{16}$#';

    public static function directory()
    {
        return expNotificationService::directory() . '/jobs';
    }

    public static function isID( $id )
    {
        return is_string( $id ) && preg_match( self::ID_PATTERN, $id ) === 1;
    }

    /**
     * Starts a run. Returns its id, or false with $error set.
     *
     * @param string $siteaccess
     * @param bool $dry the command is started with --dry-run
     * @return string|false
     */
    public static function start( $siteaccess, $dry, &$error )
    {
        $error = '';
        $dir = self::directory();
        if ( !is_dir( $dir ) && !eZDir::mkdir( $dir, false, true ) )
        {
            $error = "Cannot create $dir.";
            return false;
        }
        self::prune( $dir );
        if ( expNotificationService::runningNow() )
        {
            $error = 'A notification run is already in progress.';
            return false;
        }
        if ( expProcessTools::error() !== '' )
        {
            $error = 'The run cannot start in the background: ' . expProcessTools::error();
            return false;
        }
        $php = expProcessTools::phpCli();
        $setsid = expProcessTools::setsid();
        $id = bin2hex( random_bytes( 8 ) );
        touch( $dir . '/' . $id . '.jsonl' );

        $args = array( $php, 'bin/php/notificationrun.php', '--job=' . $id, '--source=web', '--no-colors' );
        if ( $dry )
            $args[] = '--dry-run';
        if ( $siteaccess !== '' )
            $args[] = '--siteaccess=' . $siteaccess;
        if ( function_exists( 'posix_geteuid' ) && posix_geteuid() === 0 )
            $args[] = '--allow-root-user';
        // what the command prints that does not reach the progress file (a fatal error, a missing extension) is kept
        // in <id>.log next to it: the run is detached, so nothing else would show it
        $command = array_merge( array( $setsid, '-f', '/bin/sh', '-c', 'exec "$@" >> ' . escapeshellarg( $dir . '/' . $id . '.log' ) . ' 2>&1', 'sh' ), $args );

        // only pipes go to the child: a server that serves files through its own stream wrapper cannot hand a
        // descriptor on, and its sockets must not be inherited by a run that outlives the request
        $spec = array( 0 => array( 'pipe', 'r' ), 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) );
        $engine = class_exists( 'Q_WebServer_Shell_Exec', false );
        if ( $engine )
            $spec = Q_WebServer_Shell_Exec::descriptors( $spec );
        $env = getenv();
        $env = is_array( $env ) ? $env : array();
        if ( empty( $env['PATH'] ) )
            $env['PATH'] = '/usr/local/bin:/usr/bin:/bin';
        $process = @proc_open( $command, $spec, $pipes, eZSys::rootDir(), $env );
        if ( !is_resource( $process ) )
        {
            $error = 'The run could not be started.';
            return false;
        }
        if ( $engine )
            Q_WebServer_Shell_Exec::closeExtra( $pipes );
        foreach ( $pipes as $pipe )
            fclose( $pipe );
        proc_close( $process );
        return $id;
    }

    /**
     * Appends an event to the job's file (the command's side).
     */
    public static function emit( $id, array $event )
    {
        if ( !self::isID( $id ) )
            return;
        $dir = self::directory();
        if ( !is_dir( $dir ) )
            eZDir::mkdir( $dir, false, true );
        @file_put_contents( $dir . '/' . $id . '.jsonl', json_encode( $event ) . "\n", FILE_APPEND | LOCK_EX );
    }

    /**
     * The events after line $offset, and whether the run has ended.
     *
     * @return array|false events (list), offset (next line), done (bool)
     */
    public static function progress( $id, $offset )
    {
        $file = self::directory() . '/' . $id . '.jsonl';
        if ( !self::isID( $id ) || !is_file( $file ) )
            return false;
        $lines = file( $file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
        $events = array();
        $done = false;
        foreach ( array_slice( $lines, max( 0, (int)$offset ) ) as $line )
        {
            $event = json_decode( $line, true );
            if ( !is_array( $event ) )
                continue;
            if ( $event['type'] === 'end' )
            {
                $done = true;
                $events[] = $event;
                continue;
            }
            $events[] = $event;
        }
        if ( !$done && filemtime( $file ) < time() - 120 )
        {
            $events[] = array( 'type' => 'error', 'message' => 'The run stopped without finishing (no progress for two minutes).' );
            $events[] = array( 'type' => 'end', 'result' => 'failed' );
            $done = true;
        }
        return array( 'events' => $events, 'offset' => count( $lines ), 'done' => $done );
    }

    /** Keeps the newest 20 runs. */
    private static function prune( $dir )
    {
        $files = glob( $dir . '/*.jsonl' ) ?: array();
        if ( count( $files ) < 20 )
            return;
        usort( $files, function ( $a, $b ) { return filemtime( $b ) - filemtime( $a ); } );
        foreach ( array_slice( $files, 19 ) as $file )
        {
            @unlink( $file );
            @unlink( substr( $file, 0, -6 ) . '.log' );
        }
    }
}
