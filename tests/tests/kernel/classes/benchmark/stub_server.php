<?php
/**
 * The router of the throwaway server ExpBenchmarkHttpTest starts with "php -S 127.0.0.1:<port>".
 *
 *   /           200, 1000 bytes, X-Exp-Cache: HIT, or MISS when the request carries the cache-busting parameter
 *   /missing    404
 *   /slow       200 after 20 ms
 *   /auth       401 unless the basic auth user is "bench" with the password "secret"
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

$path = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
$query = (string)parse_url( $_SERVER['REQUEST_URI'], PHP_URL_QUERY );
parse_str( $query, $parameters );

header( 'Content-Type: text/plain' );

switch ( $path )
{
    case '/':
        header( 'X-Exp-Cache: ' . ( isset( $parameters['_bench'] ) ? 'MISS' : 'HIT' ) );
        echo str_repeat( 'x', 1000 );
        break;

    case '/missing':
        http_response_code( 404 );
        echo 'not found';
        break;

    case '/slow':
        usleep( 20000 );
        echo 'slow';
        break;

    case '/auth':
        if ( !isset( $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'] )
             or $_SERVER['PHP_AUTH_USER'] !== 'bench' or $_SERVER['PHP_AUTH_PW'] !== 'secret' )
        {
            http_response_code( 401 );
            header( 'WWW-Authenticate: Basic realm="bench"' );
            echo 'no';
            break;
        }
        echo 'yes';
        break;

    default:
        http_response_code( 404 );
}
