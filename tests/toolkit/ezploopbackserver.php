<?php
/**
 * File containing the ezpLoopbackServer class.
 *
 * Starts PHP's built-in web server for a test so that it can neither hold the test process's file descriptors nor
 * outlive it.
 *
 * proc_open() hands every open descriptor of the test process to the child: flock()ed files such as
 * var/cache/ezmutex/*, and the pipe the test output goes to. A server or worker holding that pipe keeps whatever
 * reads the output waiting after the test run has ended. Each descriptor above stderr therefore becomes /dev/null
 * in the child. A SIGTERM to the server stops it but not its workers (PHP_CLI_SERVER_WORKERS), and nothing stops
 * anything when the test process dies before its tearDown (a fatal error, a kill): a shell in between stops workers
 * and server on TERM, and as soon as the test process is gone.
 *
 *   $server = ezpLoopbackServer::start( array( PHP_BINARY, '-S', '127.0.0.1:' . $port, $router ), $env, $cwd );
 *   ...
 *   ezpLoopbackServer::stop( $server );
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */
class ezpLoopbackServer
{
    /**
     * @param string[] $command the server's command line
     * @param array|null $env environment of the server, null for the test process's
     * @param string|null $cwd working directory of the server
     * @param string|null $log file the server's output is appended to, null to discard it
     * @return resource|false the process of the guarding shell
     */
    public static function start( array $command, $env = null, $cwd = null, $log = null )
    {
        $null = DIRECTORY_SEPARATOR === '\\' ? 'NUL' : '/dev/null';
        $out = $log === null ? array( 'file', $null, 'w' ) : array( 'file', $log, 'a' );
        $descriptors = array( 0 => array( 'file', $null, 'r' ), 1 => $out, 2 => $out );
        // No POSIX shell (Windows): the server alone, as before
        if ( DIRECTORY_SEPARATOR === '\\' )
            return proc_open( $command, $descriptors, $pipes, $cwd, $env );
        foreach ( (array)@scandir( '/dev/fd' ) as $fd )
        {
            if ( ctype_digit( (string)$fd ) && (int)$fd > 2 )
                $descriptors[(int)$fd] = array( 'file', '/dev/null', 'r' );
        }
        $script = '"$@" & server=$!; '
                // The workers are the server's children; without pkill (minimal containers) /proc names them
                . 'workers() { if command -v pkill >/dev/null 2>&1; then pkill -TERM -P "$server"; else '
                . 'for stat in /proc/[0-9]*/stat; do set -- $(cat "$stat" 2>/dev/null); [ "$4" = "$server" ] && kill "$1"; done; fi; } 2>/dev/null; '
                . 'stop() { workers; kill "$server" 2>/dev/null; wait "$server" 2>/dev/null; exit 0; }; '
                . 'trap stop TERM INT HUP; '
                . 'while kill -0 "$PPID" 2>/dev/null && kill -0 "$server" 2>/dev/null; do sleep 1 & wait $!; done; stop';
        return proc_open( array_merge( array( '/bin/sh', '-c', $script, 'sh' ), $command ), $descriptors, $pipes, $cwd, $env );
    }

    /**
     * Stops server and workers and waits for the guarding shell.
     *
     * @param resource|null $process what start() returned
     */
    public static function stop( $process )
    {
        if ( !is_resource( $process ) )
            return;
        proc_terminate( $process );
        proc_close( $process );
    }
}
