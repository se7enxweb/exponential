<?php
/**
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
 */

require_once 'kernel/setup/exppreloadrunner.php';

$Module = $Params['Module'];
$http = eZHTTPTool::instance();

// Read before any output: once the stream is open there is nowhere to report a
// bad parameter to except the stream itself.
$maxPages = $http->hasVariable( 'MaxPages' ) ? (int)$http->variable( 'MaxPages' ) : 250;
$maxDepth = $http->hasVariable( 'MaxDepth' ) ? (int)$http->variable( 'MaxDepth' ) : 3;
$maxPages = max( 1, min( 5000, $maxPages ) );
$maxDepth = max( 0, min( 10, $maxDepth ) );

// Only a siteaccess this installation actually serves may be named, so the
// parameter cannot be used to point the crawler somewhere else.
$siteaccess = $http->hasVariable( 'SiteAccess' ) ? (string)$http->variable( 'SiteAccess' ) : '';
$siteIni = eZINI::instance( 'site.ini' );
$allowed = $siteIni->hasVariable( 'SiteAccessSettings', 'RelatedSiteAccessList' )
         ? (array)$siteIni->variable( 'SiteAccessSettings', 'RelatedSiteAccessList' ) : array();
if ( $siteaccess !== '' && !in_array( $siteaccess, $allowed, true ) )
    $siteaccess = '';

while ( ob_get_level() > 0 )
    ob_end_clean();

header( 'Content-Type: text/event-stream; charset=utf-8' );
header( 'Cache-Control: no-cache, no-store, must-revalidate' );
header( 'Pragma: no-cache' );
header( 'Connection: keep-alive' );
// nginx buffers proxied responses by default, which would hold the whole run
// back until it finished and defeat the point of streaming it.
header( 'X-Accel-Buffering: no' );

// A run can outlast the default limit on a large site; it is bounded by
// max_pages rather than by the clock.
@set_time_limit( 0 );
ignore_user_abort( false );

$send = function ( $type, $message, array $data = array() )
{
    $payload = array( 'type' => $type, 'message' => $message ) + $data;
    echo 'data: ' . json_encode( $payload ) . "\n\n";
    // Padding defeats any remaining proxy buffer that waits for a full block.
    echo ': ' . str_repeat( ' ', 2048 ) . "\n\n";
    flush();
};

$send( 'info', 'Preloader started at ' . date( 'Y-m-d H:i:s T' ) . '.' );

try
{
    $runner = new expPreloadRunner( $send, array(
        'siteaccess' => $siteaccess,
        'max_pages'  => $maxPages,
        'max_depth'  => $maxDepth,
    ) );
    $runner->run();
}
catch ( Exception $e )
{
    $send( 'error', 'Preloader stopped: ' . $e->getMessage() );
    $send( 'done', 'Stopped early.' );
    eZDebug::writeError( $e->getMessage(), __FILE__ );
}

echo "event: end\ndata: {}\n\n";
flush();

// The response is already complete and is not html, so the module's normal
// template rendering must not run.
eZExecution::cleanExit();

?>
