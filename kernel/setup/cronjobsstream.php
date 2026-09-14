<?php
/**
 * File containing the setup/cronjobsstream view.
 *
 * Follows a running cronjob's output and relays it as Server-Sent Events, so
 * the console shows the job as it works rather than a page that has to be
 * reloaded to learn anything.
 *
 * The same transport as setup/preloadstream and setup/staticcachestream. The
 * difference is what is being followed: those run the work in this process,
 * this one tails a log file written by a process that outlives the request, and
 * stops when that process is gone and its output has been read to the end.
 *
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

require_once 'kernel/setup/expcronjobrunner.php';

$Module = $Params['Module'];
$http = eZHTTPTool::instance();

// Where in the log to start. The page sends the size it already has, so a
// reconnect does not reprint what the operator has already read.
$offset = $http->hasVariable( 'Offset' ) ? (int)$http->variable( 'Offset' ) : 0;
if ( $offset < 0 )
    $offset = 0;

while ( ob_get_level() > 0 )
    ob_end_clean();

header( 'Content-Type: text/event-stream; charset=utf-8' );
header( 'Cache-Control: no-cache, no-store, must-revalidate' );
header( 'Pragma: no-cache' );
header( 'Connection: keep-alive' );
// nginx buffers proxied responses by default, which would hold the whole run
// back until it finished and defeat the point of streaming it.
header( 'X-Accel-Buffering: no' );

@set_time_limit( 0 );
// The browser closing the connection must end this loop, or a page left open
// would leave a php worker reading a file for as long as the job runs.
ignore_user_abort( false );

$send = function ( $type, $message, array $data = array() )
{
    $payload = array( 'type' => $type, 'message' => $message ) + $data;
    echo 'data: ' . json_encode( $payload ) . "\n\n";
    echo ': ' . str_repeat( ' ', 2048 ) . "\n\n";
    flush();
};

$logFile = expCronjobRunner::logFile();
$errorFile = expCronjobRunner::errorFile();
$status = expCronjobRunner::status();

if ( !$status['running'] )
{
    $send( 'info', 'No cronjob is running. Showing what is already in the log.' );
}
else
{
    $send( 'phase', sprintf( 'Following the "%s" part for %s, process %d.',
                             $status['part'], $status['siteaccess'], $status['pid'] ) );
}

$errorOffset = file_exists( $errorFile ) ? filesize( $errorFile ) : 0;
// A stream that never ends would hold a worker forever if the job hung. The
// job is not affected: it is a separate process and keeps running either way.
$deadline = time() + 3600;
$idleSince = false;

while ( true )
{
    clearstatcache();

    $size = file_exists( $logFile ) ? filesize( $logFile ) : 0;
    if ( $size > $offset )
    {
        $handle = @fopen( $logFile, 'rb' );
        if ( $handle )
        {
            fseek( $handle, $offset );
            $chunk = (string)stream_get_contents( $handle );
            fclose( $handle );
            $offset += strlen( $chunk );

            foreach ( explode( "\n", rtrim( $chunk, "\n" ) ) as $line )
            {
                if ( trim( $line ) === '' )
                    continue;
                $type = preg_match( '/^=====/', $line ) ? 'phase' : 'ok';
                $send( $type, rtrim( $line ), array( 'offset' => $offset ) );
            }
            $idleSince = false;
        }
    }
    else if ( $size < $offset )
    {
        // The log was cleared underneath us.
        $offset = $size;
        $send( 'warn', 'The log was cleared while it was being followed.' );
    }

    $errorSize = file_exists( $errorFile ) ? filesize( $errorFile ) : 0;
    if ( $errorSize > $errorOffset )
    {
        $handle = @fopen( $errorFile, 'rb' );
        if ( $handle )
        {
            fseek( $handle, $errorOffset );
            $chunk = (string)stream_get_contents( $handle );
            fclose( $handle );
            $errorOffset += strlen( $chunk );

            foreach ( explode( "\n", rtrim( $chunk, "\n" ) ) as $line )
            {
                if ( trim( $line ) !== '' )
                    $send( 'error', rtrim( $line ) );
            }
        }
    }

    $status = expCronjobRunner::status();
    if ( !$status['running'] )
    {
        // One more pass has already been made above, so anything the job wrote
        // as it exited has been sent.
        if ( $idleSince === false )
        {
            $idleSince = time();
        }
        else if ( time() - $idleSince >= 2 )
        {
            $send( 'done', 'The cronjob has finished.', array( 'offset' => $offset ) );
            break;
        }
    }

    if ( time() > $deadline )
    {
        $send( 'warn', 'Stopped following after an hour. The cronjob is still running; reopen the page to follow it again.' );
        $send( 'done', 'Stopped following.', array( 'offset' => $offset ) );
        break;
    }

    if ( connection_aborted() )
        break;

    usleep( 750000 );
}

echo "event: end\ndata: {}\n\n";
flush();

eZExecution::cleanExit();

?>
