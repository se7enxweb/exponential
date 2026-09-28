<?php
/**
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
 */

$http = eZHTTPTool::instance();

$answer = function ( array $data, $status = 200 )
{
    if ( $status !== 200 )
        http_response_code( $status );
    header( 'Content-Type: application/json; charset=utf-8' );
    header( 'Cache-Control: no-store' );
    echo json_encode( $data );
    eZExecution::cleanExit();
};

if ( $_SERVER['REQUEST_METHOD'] === 'POST' )
{
    $action = $http->hasPostVariable( 'Action' ) ? (string)$http->postVariable( 'Action' ) : '';
    if ( $action === 'start' )
    {
        $maxPages = $http->hasPostVariable( 'MaxPages' ) ? max( 1, min( 5000, (int)$http->postVariable( 'MaxPages' ) ) ) : 250;
        $maxDepth = $http->hasPostVariable( 'MaxDepth' ) ? max( 0, min( 10, (int)$http->postVariable( 'MaxDepth' ) ) ) : 3;
        // Only a siteaccess this installation serves may be named.
        $siteaccess = $http->hasPostVariable( 'SiteAccess' ) ? (string)$http->postVariable( 'SiteAccess' ) : '';
        $siteIni = eZINI::instance( 'site.ini' );
        $allowed = $siteIni->hasVariable( 'SiteAccessSettings', 'RelatedSiteAccessList' )
                 ? (array)$siteIni->variable( 'SiteAccessSettings', 'RelatedSiteAccessList' ) : array();
        if ( $siteaccess !== '' && !in_array( $siteaccess, $allowed, true ) )
            $answer( array( 'error' => 'Unknown siteaccess.' ), 400 );
        $error = '';
        $id = expPreloadJob::start( $siteaccess, $maxPages, $maxDepth, $error );
        if ( $id === false )
            $answer( array( 'error' => $error ), 500 );
        $answer( array( 'id' => $id ) );
    }
    if ( $action === 'stop' )
    {
        $id = $http->hasPostVariable( 'JobID' ) ? (string)$http->postVariable( 'JobID' ) : '';
        $answer( array( 'stopping' => expPreloadJob::stop( $id ) ) );
    }
    $answer( array( 'error' => 'Unknown action.' ), 400 );
}

$progress = expPreloadJob::progress( (string)$Params['JobID'], (int)$Params['Offset'] );
if ( $progress === false )
    $answer( array( 'error' => 'Unknown preload run.' ), 404 );
$answer( $progress );
