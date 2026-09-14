<?php
/**
 * File containing the setup/staticcachestream view.
 *
 * Runs the static cache generator and streams its progress as Server-Sent
 * Events, so the operator watches pages being written instead of waiting on a
 * request that a proxy is free to time out.
 *
 * Same construction as setup/preloadstream: the work is done by a runner in
 * this process, and this file is only the transport.
 *
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

require_once 'kernel/setup/expstaticcacherunner.php';

$Module = $Params['Module'];
$http = eZHTTPTool::instance();

// Read before any output: once the stream is open there is nowhere to report a
// bad parameter to except the stream itself.
$siteaccess = $http->hasVariable( 'SiteAccess' ) ? (string)$http->variable( 'SiteAccess' ) : '';
$maxPages = $http->hasVariable( 'MaxPages' ) ? (int)$http->variable( 'MaxPages' ) : 2500;
$maxDepth = $http->hasVariable( 'MaxDepth' ) ? (int)$http->variable( 'MaxDepth' ) : 12;
$maxPages = max( 1, min( 10000, $maxPages ) );
$maxDepth = max( 0, min( 30, $maxDepth ) );
// A regeneration replaces what is there. Passing Purge=0 adds to it instead,
// which is what a second, deeper pass over the same site wants.
$purge = $http->hasVariable( 'Purge' ) ? (bool)(int)$http->variable( 'Purge' ) : true;

// Only a siteaccess this installation can actually cache may be named, so the
// parameter cannot be used to point the generator somewhere else.
$allowed = eZStaticCache::cacheableSiteAccessList();
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

// A full site can outlast the default limit; the run is bounded by the url set
// and MaxCacheDepth rather than by the clock.
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

$send( 'info', 'Static cache generation started at ' . date( 'Y-m-d H:i:s T' ) . '.' );

try
{
    $runner = new expStaticCacheRunner( $send, array(
        'siteaccess' => $siteaccess,
        'max_pages'  => $maxPages,
        'max_depth'  => $maxDepth,
        'purge'      => $purge,
    ) );
    $runner->run();
}
catch ( Exception $e )
{
    $send( 'error', 'Generation stopped: ' . $e->getMessage() );
    $send( 'done', 'Stopped early.' );
    eZDebug::writeError( $e->getMessage(), __FILE__ );
}

echo "event: end\ndata: {}\n\n";
flush();

// The response is already complete and is not html, so the module's normal
// template rendering must not run.
eZExecution::cleanExit();

?>
