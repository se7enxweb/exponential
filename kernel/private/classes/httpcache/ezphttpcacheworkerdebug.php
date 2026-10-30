<?php
/**
 * File containing the page cache's answer for a debug viewer in a persistent worker.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/*
 * Required by index.php at the start of every request of a persistent worker (Exponential Velocity), where
 * config.php and with it the early exit of the HTTP cache (ezphttpcacheearlyexit.php) ran only once.
 *
 * The server process in front of the workers asks the page cache before a worker, but without the visitor's
 * address, so it cannot tell a visitor the debug bar is shown to (DebugIPList) and leaves such a visitor to a
 * worker (ezpHttpCacheContract::lookup(), "debug viewer, address not known here"). Here the address is known: a
 * debug viewer is answered from their own entry with the bar of the render that stored it
 * (ezpHttpCacheContract::lookupDebug()); anybody else goes on to the kernel without a second lookup.
 */
( function () {
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    if ( $method !== 'GET' && $method !== 'HEAD' )
        return;
    $root = dirname( __DIR__, 4 );
    $dir = $root . '/var/site/cache/exphttpcache';
    if ( !is_file( $dir . '/contract.php' ) )
        return;
    require_once __DIR__ . '/ezphttpcachecontract.php';
    $contract = ezpHttpCacheContract::fromDir( $dir );
    if ( !$contract )
        return;
    $request = array(
        'server' => $_SERVER,
        'host' => $_SERVER['HTTP_HOST'] ?? '',
        'uri' => $_SERVER['REQUEST_URI'] ?? '/',
        'method' => $method,
        'cookies' => $_COOKIE,
        'acceptEncoding' => $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '',
        'ifNoneMatch' => $_SERVER['HTTP_IF_NONE_MATCH'] ?? null,
        'start' => isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? (float)$_SERVER['REQUEST_TIME_FLOAT'] : microtime( true ),
    );
    // Only for an address the debug bar is shown to (DebugByIP): the server process asked for everybody else
    if ( empty( $contract->config['debug']['output'] ) || empty( $contract->config['debug']['byIP'] ) )
        return;
    if ( !class_exists( 'expDebugBarIPList', false ) )
        require_once $root . '/kernel/classes/debugbar/expdebugbariplist.php';
    $ip = $contract->clientAddress( $request );
    if ( $ip === null || expDebugBarIPList::match( $ip, (array)( $contract->config['debug']['ipList'] ?? array() ) ) === null )
        return;
    $response = $contract->serve( $request );
    if ( $response === null )
    {
        // Tell the kernel why, for the debug bar of the page it renders.
        $GLOBALS['EXP_HTTPCACHE_LOOKUP'] = $contract->lastReason;
        return;
    }
    list( $status, $headers, $body ) = $response;
    http_response_code( $status );
    foreach ( $headers as $name => $value )
        header( $name . ': ' . $value );
    echo $body;
    exit;
} )();
