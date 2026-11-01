<?php
/**
 * The router of exp:static:serve: run by PHP's built-in web server (php -S host:port -t <export> this file), it
 * answers every request from the static export as expStaticServe::resolve() decides. The export's folder and the
 * mount come from the environment: EXP_STATIC_ROOT and EXP_STATIC_MOUNT (default /). It loads no installation.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once __DIR__ . '/expstaticlinkrewriter.php';
require_once __DIR__ . '/expstaticmanifest.php';
require_once __DIR__ . '/expstaticserve.php';

$expStaticRoot = (string)getenv( 'EXP_STATIC_ROOT' );
$expStaticMount = getenv( 'EXP_STATIC_MOUNT' ) !== false ? (string)getenv( 'EXP_STATIC_MOUNT' ) : '/';
$expStaticPath = (string)parse_url( isset( $_SERVER['REQUEST_URI'] ) ? (string)$_SERVER['REQUEST_URI'] : '/', PHP_URL_PATH );
$expStaticAnswer = expStaticServe::resolve( $expStaticRoot !== '' ? $expStaticRoot : (string)getcwd(), $expStaticMount, $expStaticPath );

http_response_code( $expStaticAnswer['status'] );
header( 'X-Content-Type-Options: nosniff' );
if ( $expStaticAnswer['location'] !== '' )
    header( 'Location: ' . $expStaticAnswer['location'] );
if ( $expStaticAnswer['file'] !== null )
{
    header( 'Content-Type: ' . $expStaticAnswer['type'] );
    header( 'Content-Length: ' . (string)filesize( $expStaticAnswer['file'] ) );
    readfile( $expStaticAnswer['file'] );
}
else if ( $expStaticAnswer['status'] === 404 )
{
    header( 'Content-Type: text/plain; charset=utf-8' );
    echo "404 Not Found\n";
}
return true;
