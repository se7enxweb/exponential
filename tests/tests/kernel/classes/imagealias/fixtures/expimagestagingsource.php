<?php
/**
 * A source installation for the image staging tests (expImageAliasStager), run with PHP's built-in server on
 * 127.0.0.1 only:
 *
 *   STAGING_SOURCE_ROOT=<installation root> [STAGING_SOURCE_AUTH=user:password] [STAGING_SOURCE_TOKEN=<token>] \
 *   [STAGING_SOURCE_SLOW=<seconds>] [STAGING_SOURCE_LOG=<file>] \
 *     php -S 127.0.0.1:18766 tests/tests/kernel/classes/imagealias/fixtures/expimagestagingsource.php
 *
 * The first path segment chooses how it answers for the storage file named by the rest (var/<site>/storage/...,
 * read from STAGING_SOURCE_ROOT, never outside a storage directory):
 *
 *   /ok/...         the file, typed by its extension
 *   /auth/...       the file with basic authentication (STAGING_SOURCE_AUTH), else 401
 *   /bearer/...     the file with a bearer token (STAGING_SOURCE_TOKEN), else 401
 *   /missing/...    404
 *   /error/...      500
 *   /html/...       200, an HTML error page typed text/html
 *   /lying/...      200, an HTML error page typed as the image
 *   /corrupt/...    200, bytes that are no image, typed as the image
 *   /wrongtype/...  the file typed text/plain
 *   /slow/...       the file after STAGING_SOURCE_SLOW seconds (default 10)
 *   /big/...        200, typed as the image, 3 MB of bytes
 *
 * Each request is appended to STAGING_SOURCE_LOG as "<mode> <path>" (never a header), so a test can count them.
 */
if ( PHP_SAPI !== 'cli-server' )
{
    fwrite( STDERR, "Run me with php -S 127.0.0.1:<port> " . __FILE__ . "\n" );
    exit( 2 );
}
if ( strpos( (string)$_SERVER['REMOTE_ADDR'], '127.' ) !== 0 )
{
    http_response_code( 403 );
    exit;
}
$root = rtrim( (string)getenv( 'STAGING_SOURCE_ROOT' ), '/' );
$path = rawurldecode( (string)parse_url( (string)$_SERVER['REQUEST_URI'], PHP_URL_PATH ) );
$parts = explode( '/', ltrim( $path, '/' ), 2 );
$mode = $parts[0];
$file = isset( $parts[1] ) ? $parts[1] : '';
$log = (string)getenv( 'STAGING_SOURCE_LOG' );
if ( $log !== '' )
    file_put_contents( $log, $mode . ' ' . $file . "\n", FILE_APPEND | LOCK_EX );

$types = array( 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp',
                'svg' => 'image/svg+xml', 'avif' => 'image/avif' );
$suffix = strtolower( (string)pathinfo( $file, PATHINFO_EXTENSION ) );
$type = isset( $types[$suffix] ) ? $types[$suffix] : 'application/octet-stream';
$html = "<!DOCTYPE html>\n<html><head><title>Error</title></head><body><h1>Something went wrong</h1></body></html>\n";

$real = $root !== '' && preg_match( '#^var/([^/]+/)?storage/#', $file ) && strpos( $file, '..' ) === false ? realpath( $root . '/' . $file ) : false;
$readable = $real !== false && is_file( $real ) && strpos( $real, realpath( $root ) . '/' ) === 0;

function stagingSourceFile( $real, $readable, $type )
{
    if ( !$readable )
    {
        http_response_code( 404 );
        header( 'Content-Type: text/html' );
        echo "<!DOCTYPE html><html><body>Not found</body></html>";
        exit;
    }
    header( 'Content-Type: ' . $type );
    header( 'Content-Length: ' . filesize( $real ) );
    readfile( $real );
    exit;
}

switch ( $mode )
{
    case 'ok':
        stagingSourceFile( $real, $readable, $type );
    case 'auth':
        $expected = (string)getenv( 'STAGING_SOURCE_AUTH' );
        $given = isset( $_SERVER['HTTP_AUTHORIZATION'] ) ? (string)$_SERVER['HTTP_AUTHORIZATION'] : '';
        if ( $expected === '' || !hash_equals( 'Basic ' . base64_encode( $expected ), $given ) )
        {
            http_response_code( 401 );
            header( 'WWW-Authenticate: Basic realm="staging test"' );
            header( 'Content-Type: text/html' );
            echo $html;
            exit;
        }
        stagingSourceFile( $real, $readable, $type );
    case 'bearer':
        $expected = (string)getenv( 'STAGING_SOURCE_TOKEN' );
        $given = isset( $_SERVER['HTTP_AUTHORIZATION'] ) ? (string)$_SERVER['HTTP_AUTHORIZATION'] : '';
        if ( $expected === '' || !hash_equals( 'Bearer ' . $expected, $given ) )
        {
            http_response_code( 401 );
            exit;
        }
        stagingSourceFile( $real, $readable, $type );
    case 'missing':
        http_response_code( 404 );
        header( 'Content-Type: text/html' );
        echo $html;
        exit;
    case 'error':
        http_response_code( 500 );
        header( 'Content-Type: text/html' );
        echo $html;
        exit;
    case 'html':
        header( 'Content-Type: text/html; charset=utf-8' );
        echo $html;
        exit;
    case 'lying':
        header( 'Content-Type: ' . $type );
        echo $html;
        exit;
    case 'corrupt':
        header( 'Content-Type: ' . $type );
        echo str_repeat( "\x00\x13\x37garbage", 200 );
        exit;
    case 'wrongtype':
        stagingSourceFile( $real, $readable, 'text/plain' );
    case 'slow':
        $seconds = (float)( getenv( 'STAGING_SOURCE_SLOW' ) ?: 10 );
        usleep( (int)( $seconds * 1000000 ) );
        stagingSourceFile( $real, $readable, $type );
    case 'big':
        header( 'Content-Type: ' . $type );
        echo str_repeat( 'x', 3 * 1024 * 1024 );
        exit;
}
http_response_code( 404 );
