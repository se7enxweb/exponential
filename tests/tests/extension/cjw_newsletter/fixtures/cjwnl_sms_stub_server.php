<?php
/**
 * A stand-in for an SMS provider API, for cjwNewsletterSmsTest: the router of PHP's built-in web server, started by
 * the test on 127.0.0.1 and stopped by it. Nothing leaves the machine.
 *
 * Every request is appended as one JSON line (method, uri, headers, body) to <STUB_DIR>/requests.jsonl. The answer is
 * read from <STUB_DIR>/response.json: hash( status, body, content_type ); without it: 201 with a JSON sid and id.
 *
 * Run by the test: php -S 127.0.0.1:<port> cjwnl_sms_stub_server.php, with the environment variable CJWNL_SMS_STUB_DIR.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

$dir = (string)getenv( 'CJWNL_SMS_STUB_DIR' );
if ( $dir === '' || !is_dir( $dir ) )
{
    http_response_code( 500 );
    echo '{"error":"no stub dir"}';
    return true;
}
$headers = array();
foreach ( $_SERVER as $key => $value )
    if ( strncmp( $key, 'HTTP_', 5 ) === 0 )
        $headers[strtolower( str_replace( '_', '-', substr( $key, 5 ) ) )] = $value;
if ( isset( $_SERVER['CONTENT_TYPE'] ) )
    $headers['content-type'] = $_SERVER['CONTENT_TYPE'];
$line = array( 'method' => $_SERVER['REQUEST_METHOD'], 'uri' => $_SERVER['REQUEST_URI'], 'headers' => $headers,
               'body' => (string)file_get_contents( 'php://input' ) );
file_put_contents( $dir . '/requests.jsonl', json_encode( $line ) . "\n", FILE_APPEND | LOCK_EX );

$response = array( 'status' => 201, 'content_type' => 'application/json', 'body' => json_encode( array( 'sid' => 'SMstub' . count( file( $dir . '/requests.jsonl' ) ), 'id' => 'stub-' . time() ) ) );
if ( is_file( $dir . '/response.json' ) )
{
    $configured = json_decode( (string)file_get_contents( $dir . '/response.json' ), true );
    if ( is_array( $configured ) )
        $response = array_merge( $response, $configured );
}
http_response_code( (int)$response['status'] );
header( 'Content-Type: ' . $response['content_type'] );
echo $response['body'];
return true;
