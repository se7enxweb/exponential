<?php
/**
 * The router of the throwaway "php -S" server ezpRestHttpResponseWriterStatusLineTest starts. Each request
 * answers with ezpRestHttpResponseWriter and one of the REST status objects, the way the REST dispatcher does
 * when a result carries a status:
 *
 *   /?status=challenge    ezpOauthRequired without an error: 401 and a WWW-Authenticate challenge
 *   /?status=scope        ezpOauthRequired, insufficient_scope: 403 with a challenge
 *   /?status=http         ezpRestHttpResponse( 401, message )
 *   /?status=response     ezpRestStatusResponse( 404 )
 *   /?status=none         no status object: 200
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

$root = dirname( __DIR__, 6 );
require $root . '/vendor/autoload.php';
$kernelClasses = require $root . '/autoload/ezp_kernel.php';
spl_autoload_register( function ( $class ) use ( $root, $kernelClasses )
{
    if ( isset( $kernelClasses[$class] ) )
    {
        require $root . '/' . $kernelClasses[$class];
    }
} );

$response = new ezcMvcResponse();
$response->body = 'body';
switch ( $_GET['status'] ?? 'none' )
{
    case 'challenge':
        $response->status = new ezpOauthRequired( 'test' );
        break;
    case 'scope':
        $response->status = new ezpOauthRequired( 'test', ezpOauthErrorType::INSUFFICIENT_SCOPE );
        break;
    case 'http':
        $response->status = new ezpRestHttpResponse( 401, 'Not signed in' );
        break;
    case 'response':
        $response->status = new ezpRestStatusResponse( 404 );
        break;
}

$writer = new ezpRestHttpResponseWriter( $response );
$writer->handleResponse();
