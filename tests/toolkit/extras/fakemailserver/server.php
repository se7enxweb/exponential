<?php
/**
 * A fake IMAP or POP3 server for the tests (started through expFakeMailServer, never by hand on a live port).
 *
 *   php server.php <imap|pop3> <port> <spool directory> <log file> <password>
 *
 * The spool holds one file per message (<number>.eml, numbered from 1). Every command a client sends is appended to
 * the log, one line each (a password is replaced by ***). A message deleted on the server (IMAP STORE \Deleted and
 * EXPUNGE, POP3 DELE and QUIT) has its file renamed to <number>.deleted, so a test sees what is left. The server
 * answers one client at a time, plain text only, and implements the commands the ezcMail transports and the
 * help desk's mailbox client send; anything else is answered OK.
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

if ( PHP_SAPI !== 'cli' || $argc < 6 )
{
    fwrite( STDERR, "usage: server.php <imap|pop3> <port> <spool> <log> <password>\n" );
    exit( 2 );
}
list( , $proto, $port, $spool, $logFile, $password ) = $argv;
$server = stream_socket_server( 'tcp://127.0.0.1:' . (int)$port, $errno, $errstr );
if ( !$server )
{
    fwrite( STDERR, "cannot listen: $errstr\n" );
    exit( 1 );
}

/** @return array number => path of the messages left on the server (not deleted), by number */
function fakeMessages( $spool )
{
    $out = array();
    foreach ( (array)glob( $spool . '/*.eml' ) as $path )
        $out[(int)basename( $path, '.eml' )] = $path;
    ksort( $out );
    return $out;
}

function fakeLog( $logFile, $line )
{
    file_put_contents( $logFile, $line . "\n", FILE_APPEND | LOCK_EX );
}

function fakeSend( $client, $text )
{
    fwrite( $client, $text );
}

/** IMAP sequence set (1,3:5, *) => numbers among $known */
function fakeSet( $set, array $known )
{
    $max = count( $known ) ? max( $known ) : 0;
    $out = array();
    foreach ( explode( ',', $set ) as $part )
    {
        $range = explode( ':', $part, 2 );
        $from = $range[0] === '*' ? $max : (int)$range[0];
        $to = isset( $range[1] ) ? ( $range[1] === '*' ? $max : (int)$range[1] ) : $from;
        for ( $n = min( $from, $to ); $n <= max( $from, $to ); $n++ )
            if ( in_array( $n, $known, true ) )
                $out[] = $n;
    }
    return $out;
}

function fakeImap( $client, $spool, $logFile, $password )
{
    $deleted = array();
    $seen = array();
    fakeSend( $client, "* OK fake IMAP ready\r\n" );
    while ( ( $line = fgets( $client ) ) !== false )
    {
        $line = rtrim( $line, "\r\n" );
        $parts = explode( ' ', $line, 3 );
        $tag = $parts[0];
        $cmd = strtoupper( isset( $parts[1] ) ? $parts[1] : '' );
        $args = isset( $parts[2] ) ? $parts[2] : '';
        $uid = false;
        if ( $cmd === 'UID' )
        {
            $uid = true;
            $sub = explode( ' ', $args, 2 );
            $cmd = strtoupper( $sub[0] );
            $args = isset( $sub[1] ) ? $sub[1] : '';
        }
        fakeLog( $logFile, $cmd === 'LOGIN' ? "$tag LOGIN ***" : $line );
        $messages = fakeMessages( $spool );
        $known = array_keys( $messages );
        switch ( $cmd )
        {
            case 'CAPABILITY':
                fakeSend( $client, "* CAPABILITY IMAP4rev1 UIDPLUS MOVE\r\n$tag OK CAPABILITY completed\r\n" );
                break;
            case 'LOGIN':
                $given = preg_match( '/^"?[^" ]*"? "?([^"]*)"?$/', $args, $m ) ? $m[1] : '';
                if ( $given !== $password )
                    fakeSend( $client, "$tag NO [AUTHENTICATIONFAILED] Invalid credentials\r\n" );
                else
                    fakeSend( $client, "$tag OK LOGIN completed\r\n" );
                break;
            case 'SELECT':
            case 'EXAMINE':
                fakeSend( $client, '* ' . count( $known ) . " EXISTS\r\n* 0 RECENT\r\n* FLAGS (\\Seen \\Deleted)\r\n$tag OK [READ-WRITE] $cmd completed\r\n" );
                break;
            case 'SEARCH':
                $hits = array();
                foreach ( $known as $n )
                {
                    if ( in_array( $n, $deleted, true ) )
                        continue;
                    if ( stripos( $args, 'UNSEEN' ) !== false && in_array( $n, $seen, true ) )
                        continue;
                    $hits[] = $n;
                }
                fakeSend( $client, '* SEARCH' . ( $hits ? ' ' . implode( ' ', $hits ) : '' ) . "\r\n$tag OK SEARCH completed\r\n" );
                break;
            case 'FETCH':
                $sub = explode( ' ', $args, 2 );
                $what = strtoupper( isset( $sub[1] ) ? $sub[1] : '' );
                foreach ( fakeSet( $sub[0], $known ) as $n )
                {
                    $body = (string)file_get_contents( $messages[$n] );
                    $id = $uid ? "UID $n " : '';
                    if ( strpos( $what, 'RFC822.SIZE' ) !== false )
                        fakeSend( $client, "* $n FETCH ({$id}RFC822.SIZE " . strlen( $body ) . ")\r\n" );
                    else if ( strpos( $what, 'FLAGS' ) !== false && strpos( $what, 'BODY' ) === false && strpos( $what, 'RFC822' ) === false )
                        fakeSend( $client, "* $n FETCH ({$id}FLAGS (" . ( in_array( $n, $seen, true ) ? '\\Seen' : '' ) . "))\r\n" );
                    else
                    {
                        $item = strpos( $what, 'BODY' ) !== false ? 'BODY[]' : 'RFC822';
                        if ( strpos( $what, 'PEEK' ) === false )
                            $seen[] = $n;
                        fakeSend( $client, "* $n FETCH ({$id}$item {" . strlen( $body ) . "}\r\n" . $body . ")\r\n" );
                    }
                }
                fakeSend( $client, "$tag OK FETCH completed\r\n" );
                break;
            case 'STORE':
                $sub = explode( ' ', $args, 3 );
                foreach ( fakeSet( $sub[0], $known ) as $n )
                {
                    $add = strpos( $sub[1], '+' ) === 0;
                    $flag = isset( $sub[2] ) ? $sub[2] : '';
                    if ( stripos( $flag, 'Deleted' ) !== false )
                        $deleted = $add ? array_merge( $deleted, array( $n ) ) : array_diff( $deleted, array( $n ) );
                    if ( stripos( $flag, 'Seen' ) !== false )
                        $seen = $add ? array_merge( $seen, array( $n ) ) : array_values( array_diff( $seen, array( $n ) ) );
                }
                fakeSend( $client, "$tag OK STORE completed\r\n" );
                break;
            case 'MOVE':
                $sub = explode( ' ', $args, 2 );
                foreach ( fakeSet( $sub[0], $known ) as $n )
                    rename( $messages[$n], $spool . '/' . $n . '.moved' );
                fakeSend( $client, "$tag OK MOVE completed\r\n" );
                break;
            case 'EXPUNGE':
                foreach ( array_unique( $deleted ) as $n )
                {
                    if ( isset( $messages[$n] ) )
                        rename( $messages[$n], $spool . '/' . $n . '.deleted' );
                    fakeSend( $client, "* $n EXPUNGE\r\n" );
                }
                $deleted = array();
                fakeSend( $client, "$tag OK EXPUNGE completed\r\n" );
                break;
            case 'LOGOUT':
                fakeSend( $client, "* BYE fake IMAP\r\n$tag OK LOGOUT completed\r\n" );
                return;
            default:
                fakeSend( $client, "$tag OK $cmd completed\r\n" );
        }
    }
}

function fakePop3( $client, $spool, $logFile, $password )
{
    $deleted = array();
    fakeSend( $client, "+OK fake POP3 ready\r\n" );
    while ( ( $line = fgets( $client ) ) !== false )
    {
        $line = rtrim( $line, "\r\n" );
        $parts = explode( ' ', $line, 2 );
        $cmd = strtoupper( $parts[0] );
        $arg = isset( $parts[1] ) ? $parts[1] : '';
        fakeLog( $logFile, $cmd === 'PASS' ? 'PASS ***' : $line );
        $messages = fakeMessages( $spool );
        $left = array_diff_key( $messages, array_flip( $deleted ) );
        switch ( $cmd )
        {
            case 'USER':
                fakeSend( $client, "+OK\r\n" );
                break;
            case 'PASS':
                fakeSend( $client, $arg === $password ? "+OK logged in\r\n" : "-ERR [AUTH] invalid credentials\r\n" );
                break;
            case 'STAT':
                $size = 0;
                foreach ( $left as $path )
                    $size += filesize( $path );
                fakeSend( $client, '+OK ' . count( $left ) . " $size\r\n" );
                break;
            case 'LIST':
            case 'UIDL':
                $out = "+OK\r\n";
                foreach ( $left as $n => $path )
                    $out .= $n . ' ' . ( $cmd === 'LIST' ? filesize( $path ) : 'uid' . $n ) . "\r\n";
                fakeSend( $client, $out . ".\r\n" );
                break;
            case 'RETR':
            case 'TOP':
                $n = (int)$arg;
                if ( !isset( $left[$n] ) )
                {
                    fakeSend( $client, "-ERR no such message\r\n" );
                    break;
                }
                $body = str_replace( array( "\r\n", "\r" ), "\n", (string)file_get_contents( $left[$n] ) );
                $out = "+OK\r\n";
                foreach ( explode( "\n", rtrim( $body, "\n" ) ) as $l )
                    $out .= ( isset( $l[0] ) && $l[0] === '.' ? '.' : '' ) . $l . "\r\n";
                fakeSend( $client, $out . ".\r\n" );
                break;
            case 'DELE':
                $deleted[] = (int)$arg;
                fakeSend( $client, "+OK marked\r\n" );
                break;
            case 'RSET':
                $deleted = array();
                fakeSend( $client, "+OK\r\n" );
                break;
            case 'QUIT':
                foreach ( array_unique( $deleted ) as $n )
                    if ( isset( $messages[$n] ) )
                        rename( $messages[$n], $spool . '/' . $n . '.deleted' );
                fakeSend( $client, "+OK bye\r\n" );
                return;
            default:
                fakeSend( $client, "+OK\r\n" );
        }
    }
    // the connection dropped without QUIT: POP3 deletes nothing
}

while ( true )
{
    $client = @stream_socket_accept( $server, -1 );
    if ( !$client )
        continue;
    if ( $proto === 'pop3' )
        fakePop3( $client, $spool, $logFile, $password );
    else
        fakeImap( $client, $spool, $logFile, $password );
    fclose( $client );
}
