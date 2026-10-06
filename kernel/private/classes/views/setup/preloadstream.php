<?php
/**
 * The code of kernel/setup/preloadstream.php, moved into a class (#207 stage 1). The file kernel/setup/preloadstream.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/setup/preloadstream.php:
 *
 *
 * File containing the setup/preloadstream view.
 *
 * Runs the preloader and streams its progress as Server-Sent Events.
 *
 * The reference implementation in exponentialbasic spawned the command line
 * script with popen and relayed its stdout. This runs expPreloadRunner in the
 * same process instead, so there is no dependency on shell_exec being enabled,
 * on wget being installed, or on the web user being able to find a cli php
 * binary - and the exit path is an ordinary return rather than an exit code
 * recovered from a pipe.
 *
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 *
 */
namespace Exponential\View\Kernel\Setup
{

/**
 * The older progress stream of Setup > Preload, kept for anything that opens it. It used to run the whole crawl
 * inside this request: it held a web server worker for minutes, ended in a time-out behind a pooled server or a
 * buffering proxy, and started a load on the site from a plain GET. It runs nothing now. It answers the events of
 * the run going on (or of the last run) so far, in the same Server-Sent Events format, and ends; an EventSource
 * opens it again by itself, so a reader still follows a run. Runs are started by setup/preloadjob or the form of
 * setup/preload.
 */
class Preloadstream extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        if ( method_exists( '\eZExecution', 'discardOutputBuffers' ) )
            \eZExecution::discardOutputBuffers();
        else
            while ( ob_get_level() > 0 && @ob_end_clean() );

        header( 'Content-Type: text/event-stream; charset=utf-8' );
        header( 'Cache-Control: no-cache, no-store, must-revalidate' );
        header( 'X-Accel-Buffering: no' );

        $send = function ( array $payload )
        {
            echo 'data: ' . json_encode( $payload, JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR ) . "\n\n";
        };

        $run = \expPreloadJob::running();
        if ( $run === false || $run['id'] === '' )
        {
            $runs = \expPreloadJob::history()->runs( 1 );
            $run = $runs ? $runs[0] : false;
        }

        echo "retry: 3000\n\n";
        $send( array( 'type' => 'info', 'message' => 'This stream starts no preload; Setup > Preload starts one in the background. It shows the run going on, or the last one.' ) );
        if ( $run !== false && $run['id'] !== '' )
        {
            $progress = \expPreloadJob::progress( $run['id'], 0 );
            if ( $progress !== false )
                foreach ( $progress['events'] as $event )
                    $send( $event );
        }
        echo "event: end\ndata: {}\n\n";
        flush();

        \eZExecution::cleanExit();

        return $this->viewResult( null, null );
    }
}

}
