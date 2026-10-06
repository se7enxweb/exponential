<?php
/**
 * The code of kernel/setup/preloadjob.php, moved into a class (#207 stage 1). The file kernel/setup/preloadjob.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/setup/preloadjob.php:
 *
 *
 * File containing the setup/preloadjob view.
 *
 * The preloader console's back end (expPreloadJob), answering JSON:
 *   POST setup/preloadjob with Action=start, MaxPages, MaxDepth, SiteAccess
 *        -> { "id": "..." }
 *   POST setup/preloadjob with Action=stop, JobID
 *   GET  setup/preloadjob/<id>/<offset>
 *        -> { "events": [...], "offset": n, "done": bool }
 *
 * @copyright Copyright (C) 1998 - 2026 7x and others. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Setup
{

/**
 * Setup > Preload's back end, answering JSON:
 *   POST Action=start, SiteAccess, MaxPages, MaxDepth[, Images]  -> { "id": "..." }, 409 while a run is going on
 *   POST Action=stop, JobID                                      -> { "stopping": bool }
 *   POST Action=dryrun, SiteAccess, MaxPages, MaxDepth[, Images] -> { "plan": { base_url, start_urls, command, ... } }
 *   GET  setup/preloadjob/status          -> { "running": status|false, "runs": [ status, ... ] }
 *   GET  setup/preloadjob/<id>/<offset>   -> { "events": [...], "offset": n, "done": bool, "status": status }
 * Every POST carries the form token (the page sends it as X-CSRF-Token).
 */
class Preloadjob extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $http = \eZHTTPTool::instance();

        $answer = function ( array $data, $status = 200 )
        {
            if ( $status !== 200 )
                http_response_code( $status );
            header( 'Content-Type: application/json; charset=utf-8' );
            header( 'Cache-Control: no-store' );
            echo json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR );
            \eZExecution::cleanExit();
        };

        if ( $_SERVER['REQUEST_METHOD'] === 'POST' )
        {
            $action = $http->hasPostVariable( 'Action' ) ? (string)$http->postVariable( 'Action' ) : '';
            if ( $action === 'start' || $action === 'dryrun' )
            {
                $options = \expPreloadJob::options( array(
                    'max_pages' => $http->hasPostVariable( 'MaxPages' ) ? $http->postVariable( 'MaxPages' ) : null,
                    'max_depth' => $http->hasPostVariable( 'MaxDepth' ) ? $http->postVariable( 'MaxDepth' ) : null,
                    'images' => $http->hasPostVariable( 'Images' ) && $http->postVariable( 'Images' ),
                ) );
                // Only a siteaccess this installation serves may be named; nothing else reaches a command line.
                $siteaccess = $http->hasPostVariable( 'SiteAccess' ) ? (string)$http->postVariable( 'SiteAccess' ) : '';
                if ( $siteaccess !== '' && !in_array( $siteaccess, \expPreloadJob::knownSiteaccesses(), true ) )
                    $answer( array( 'error' => 'Unknown siteaccess.' ), 400 );
                if ( $action === 'dryrun' )
                    $answer( array( 'plan' => \expPreloadJob::plan( $siteaccess, $options ) ) );
                if ( \expPreloadJob::running() !== false )
                    $answer( array( 'error' => 'A preload is already running. Wait for it to end or stop it first.', 'busy' => true ), 409 );
                $error = '';
                $id = \expPreloadJob::start( $siteaccess, $options['max_pages'], $options['max_depth'], $error, $options );
                if ( $id === false )
                    $answer( array( 'error' => $error ), 500 );
                $answer( array( 'id' => $id ) );
            }
            if ( $action === 'stop' )
            {
                $id = $http->hasPostVariable( 'JobID' ) ? (string)$http->postVariable( 'JobID' ) : '';
                $answer( array( 'stopping' => \expPreloadJob::stop( $id ) ) );
            }
            $answer( array( 'error' => 'Unknown action.' ), 400 );
        }

        if ( (string)$Params['JobID'] === 'status' )
        {
            $runs = array();
            foreach ( \expPreloadJob::history()->runs( 10 ) as $status )
                $runs[] = \expPreloadJob::publicStatus( $status );
            $running = \expPreloadJob::running();
            $answer( array( 'running' => $running === false ? false : \expPreloadJob::publicStatus( $running ), 'runs' => $runs ) );
        }

        $progress = \expPreloadJob::progress( (string)$Params['JobID'], (int)$Params['Offset'] );
        if ( $progress === false )
            $answer( array( 'error' => 'Unknown preload run.' ), 404 );
        $answer( $progress );

        return $this->viewResult( null, null );
    }
}

}
