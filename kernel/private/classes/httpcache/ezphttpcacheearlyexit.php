<?php
/**
 * File containing the early exit of the role-aware HTTP cache.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

/*
 * Included from config.php, before the autoloader, INI or database exist:
 *
 *   if ( is_file( __DIR__ . '/kernel/private/classes/httpcache/ezphttpcacheearlyexit.php' ) )
 *       require __DIR__ . '/kernel/private/classes/httpcache/ezphttpcacheearlyexit.php';
 *
 * On a hit it sends the cached page and ends the request; otherwise it returns
 * and the kernel runs as usual (and stores the page, see ezpHttpCacheListener).
 * It does nothing unless var/site/cache/exphttpcache/contract.php exists and
 * says enabled, so it is inert until the cache is switched on in httpcache.ini.
 */
if ( PHP_SAPI !== 'cli' && !defined( 'EXP_HTTPCACHE_EARLY_EXIT_DONE' ) )
{
    define( 'EXP_HTTPCACHE_EARLY_EXIT_DONE', true );
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
        // Scheme and host are worked out from $_SERVER as the kernel does
        // (forwarded headers included), so a page stored behind a load
        // balancer that ends TLS is found again.
        $response = $contract->serve( array(
            'server' => $_SERVER,
            'host' => $_SERVER['HTTP_HOST'] ?? '',
            'uri' => $_SERVER['REQUEST_URI'] ?? '/',
            'method' => $method,
            'cookies' => $_COOKIE,
            'acceptEncoding' => $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '',
            'ifNoneMatch' => $_SERVER['HTTP_IF_NONE_MATCH'] ?? null,
            // request-shield, when it ran first: the page this request may be answered from.
            'lookupUri' => $_SERVER['REQUEST_SHIELD_CACHE_LOOKUP'] ?? null,
        ) );
        if ( $response === null )
        {
            // Tell the kernel why, for the debug header on the response it renders.
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
}
