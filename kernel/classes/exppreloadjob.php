<?php
/**
 * File containing the expPreloadJob class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and others. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A preload run for Setup > Preload, in the background.
 *
 * The page used to hold one request open for the whole crawl and read its
 * progress as it was streamed. A web server that answers from a pool of
 * workers hands a response over only when it is complete and ends a request
 * after its time limit (Exponential Velocity answered 504 after 30 seconds),
 * and a proxy may hold streamed output back: the Start button then did
 * nothing. The crawl now runs in bin/php/preloadjob.php, started detached;
 * the page reads its events from var/<var dir>/preload/<id>.jsonl.
 */
class expPreloadJob
{
    const ID_PATTERN = '#^[a-f0-9]{16}$#';

    public static function directory()
    {
        return eZSys::varDirectory() . '/preload';
    }

    public static function isID( $id )
    {
        return is_string( $id ) && preg_match( self::ID_PATTERN, $id ) === 1;
    }

    /**
     * Starts a run. Returns its id, or false with $error set.
     */
    public static function start( $siteaccess, $maxPages, $maxDepth, &$error )
    {
        $dir = self::directory();
        if ( !is_dir( $dir ) && !eZDir::mkdir( $dir, false, true ) )
        {
            $error = "Cannot create $dir.";
            return false;
        }
        self::prune( $dir );

        $id = bin2hex( random_bytes( 8 ) );
        touch( $dir . '/' . $id . '.jsonl' );

        $php = self::phpBinary();
        $setsid = is_executable( '/usr/bin/setsid' ) ? '/usr/bin/setsid' : ( is_executable( '/bin/setsid' ) ? '/bin/setsid' : false );
        if ( !$php || !$setsid || !function_exists( 'proc_open' ) )
        {
            $error = 'The preloader needs proc_open, setsid and the PHP command line.';
            return false;
        }
        $command = array( $setsid, '-f', $php, 'bin/php/preloadjob.php', '--id=' . $id,
                          '--max-pages=' . (int)$maxPages, '--max-depth=' . (int)$maxDepth );
        if ( $siteaccess !== '' )
            $command[] = '--target=' . $siteaccess;
        if ( function_exists( 'posix_geteuid' ) && posix_geteuid() === 0 )
            $command[] = '--allow-root-user';

        // Only pipes go to the child: under a server that serves files
        // through its own stream wrapper a file descriptor cannot be handed
        // on, and the server's own sockets must not be inherited by a run
        // that outlives the request.
        $spec = array( 0 => array( 'pipe', 'r' ), 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) );
        $engine = class_exists( 'Q_WebServer_Shell_Exec', false );
        if ( $engine )
            $spec = Q_WebServer_Shell_Exec::descriptors( $spec );
        $env = getenv();
        $env = is_array( $env ) ? $env : array();
        $env['EXP_PRELOAD_DETACHED'] = '1';
        if ( empty( $env['PATH'] ) )
            $env['PATH'] = '/usr/local/bin:/usr/bin:/bin';
        $process = @proc_open( $command, $spec, $pipes, eZSys::rootDir(), $env );
        if ( !is_resource( $process ) )
        {
            $error = 'The preloader could not be started.';
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
     * The events after line $offset, and whether the run has ended.
     *
     * @return array|false hash events (list), offset (next line), done (bool)
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
                continue;
            }
            $events[] = $event;
        }
        // A run that has written nothing for a long time and not ended died.
        if ( !$done && filemtime( $file ) < time() - 120 )
        {
            $events[] = array( 'type' => 'error', 'message' => 'The preloader stopped without finishing (no progress for two minutes).' );
            $events[] = array( 'type' => 'done', 'message' => 'Stopped.' );
            $done = true;
        }
        return array( 'events' => $events, 'offset' => count( $lines ), 'done' => $done );
    }

    /**
     * Asks a run to stop; it ends after the page it is fetching.
     */
    public static function stop( $id )
    {
        if ( !self::isID( $id ) || !is_file( self::directory() . '/' . $id . '.jsonl' ) )
            return false;
        return touch( self::directory() . '/' . $id . '.stop' );
    }

    /**
     * The PHP command line: PHP_BINARY on the command line, the php next to
     * the running PHP (PHP_BINDIR) under PHP-FPM.
     */
    private static function phpBinary()
    {
        if ( PHP_SAPI === 'cli' && PHP_BINARY !== '' )
            return PHP_BINARY;
        foreach ( array( PHP_BINDIR . '/php', '/usr/local/bin/php', '/usr/bin/php' ) as $file )
            if ( is_executable( $file ) )
                return $file;
        return false;
    }

    /**
     * Keeps the newest 20 runs.
     */
    private static function prune( $dir )
    {
        $files = glob( $dir . '/*.jsonl' ) ?: array();
        if ( count( $files ) < 20 )
            return;
        usort( $files, function ( $a, $b ) { return filemtime( $b ) - filemtime( $a ); } );
        foreach ( array_slice( $files, 19 ) as $file )
        {
            @unlink( $file );
            @unlink( substr( $file, 0, -6 ) . '.stop' );
        }
    }
}
