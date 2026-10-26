<?php
/**
 * A fake SMTP server for eZSMTPTransportTest: listens on 127.0.0.1 on a free port, prints "port <n>" on stdout,
 * takes one connection, greets, answers EHLO with "250 AUTH <first argument>" (default NTLM), QUIT with 221 and
 * anything else with 502. Every line the client sent is printed as "C: <line>" on stdout. It ends after the
 * connection is closed or after ten seconds without one. Nothing is delivered anywhere.
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

$auth = isset( $argv[1] ) && $argv[1] !== '' ? $argv[1] : 'NTLM';
$server = stream_socket_server( 'tcp://127.0.0.1:0', $errno, $errstr );
if ( !$server )
{
    fwrite( STDERR, "cannot listen: $errstr\n" );
    exit( 2 );
}
$name = stream_socket_get_name( $server, false );
echo 'port ', substr( $name, strrpos( $name, ':' ) + 1 ), "\n";
fflush( STDOUT );

$connection = @stream_socket_accept( $server, 10 );
if ( !$connection )
    exit( 3 );
stream_set_timeout( $connection, 10 );
fwrite( $connection, "220 fake.invalid ESMTP\r\n" );
while ( ( $line = fgets( $connection ) ) !== false )
{
    $line = rtrim( $line, "\r\n" );
    echo 'C: ', $line, "\n";
    fflush( STDOUT );
    if ( preg_match( '/^(EHLO|HELO) /i', $line ) )
        fwrite( $connection, "250-fake.invalid\r\n250 AUTH $auth\r\n" );
    elseif ( strcasecmp( $line, 'QUIT' ) === 0 )
    {
        fwrite( $connection, "221 bye\r\n" );
        break;
    }
    else
        fwrite( $connection, "502 not implemented\r\n" );
}
fclose( $connection );
fclose( $server );
