<?php
/**
 * A webhook receiver for the audit tests, run with PHP's built-in server on 127.0.0.1 only:
 *
 *   AUDIT_RECEIVER_DIR=var/tmp/audit-tests/.../receiver AUDIT_RECEIVER_SECRET=s3cret \
 *     php -S 127.0.0.1:18765 tests/tests/kernel/classes/audit/fixtures/expaudittestwebhookreceiver.php
 *
 * Every POST is checked like a real receiver does (expAuditWebhookSink::verify(): the signature over
 * timestamp + "." + body, the timestamp within 300 s) and stored as <dir>/batch-<n>.json with the result.
 * While <dir>/fail contains "1" it answers 500 (a forced outage, to prove the retries).
 */
if ( PHP_SAPI !== 'cli-server' )
{
    fwrite( STDERR, "Run me with php -S 127.0.0.1:<port> " . __FILE__ . "\n" );
    exit( 2 );
}
$dir = rtrim( (string)getenv( 'AUDIT_RECEIVER_DIR' ), '/' );
$secret = (string)getenv( 'AUDIT_RECEIVER_SECRET' );
if ( $dir === '' || !is_dir( $dir ) || strpos( $_SERVER['REMOTE_ADDR'], '127.' ) !== 0 )
{
    http_response_code( 403 );
    exit;
}
if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
{
    echo "audit test receiver\n";
    exit;
}
$body = file_get_contents( 'php://input' );
$ts = isset( $_SERVER['HTTP_X_EXPONENTIAL_TIMESTAMP'] ) ? $_SERVER['HTTP_X_EXPONENTIAL_TIMESTAMP'] : '';
$sig = isset( $_SERVER['HTTP_X_EXPONENTIAL_SIGNATURE'] ) ? $_SERVER['HTTP_X_EXPONENTIAL_SIGNATURE'] : '';
$expected = 'sha256=' . hash_hmac( 'sha256', $ts . '.' . $body, $secret );
$check = !ctype_digit( $ts ) || abs( time() - (int)$ts ) > 300 ? 'stale_timestamp' : ( hash_equals( $expected, $sig ) ? 'ok' : 'bad_signature' );
$failing = trim( (string)@file_get_contents( $dir . '/fail' ) ) === '1';
$n = count( glob( $dir . '/batch-*.json' ) ) + 1;
file_put_contents( $dir . sprintf( '/batch-%04d.json', $n ), json_encode( array(
    'check' => $check, 'answered' => $failing ? 500 : ( $check === 'ok' ? 200 : 401 ),
    'batch' => isset( $_SERVER['HTTP_X_EXPONENTIAL_BATCH'] ) ? $_SERVER['HTTP_X_EXPONENTIAL_BATCH'] : '',
    'content_type' => isset( $_SERVER['CONTENT_TYPE'] ) ? $_SERVER['CONTENT_TYPE'] : '', 'body' => json_decode( $body, true ) ) ) );
http_response_code( $failing ? 500 : ( $check === 'ok' ? 200 : 401 ) );
echo $failing ? "forced failure\n" : $check . "\n";
