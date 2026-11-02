<?php
/**
 * The migration/stream view (/migration/stream/<run id>/(offset)/<bytes>/(log)/<bytes>): follows a job of the
 * administration as Server-Sent Events while it runs, as setup/cronjobsstream follows a cronjob. It reads the job's
 * progress file (expMigrationProgress) and the run log (migrations/runs/<run id>.log) from the offsets the page
 * already has, so a reconnect sends nothing twice, and ends when the job's process has ended and both are read to
 * the end. The run is not affected by the page: it is a process of its own and goes on when the page is closed.
 *
 * Each event is a JSON object: type progress (state: expMigrationProgress::fold() of every event so far, meter: the
 * step of the meter classes, offset), log (text: one line of the run log, level, offset), done (exit: the report's
 * exit code, or null without a report) or info. Policy migration/read.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Migration
{

class Stream extends \Exponential\Runnable\ModuleView
{
    /** How long one connection follows a job; the page connects again after it. */
    const MAX_SECONDS = 1800;

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $runId = isset( $Params['RunID'] ) ? (string)$Params['RunID'] : '';
        $job = \expMigrationJob::get( $runId );
        if ( !$job )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );
        $offset = max( 0, (int)\expViewParameters::get( $Params, 'offset', 'Offset' ) );
        $logOffset = max( 0, (int)\expViewParameters::get( $Params, 'log', 'LogOffset' ) );
        // a rollback and a deploy write no run log here (a deploy's runs log on the remotes)
        $logFile = !in_array( $job['kind'], array( 'rollback', 'deploy' ), true ) ? \expMigrationAdmin::runLogFile( $runId ) : '';

        // not a loop on ob_get_level(): a persistent worker's own buffer cannot be removed (eZExecution::discardOutputBuffers())
        if ( method_exists( '\eZExecution', 'discardOutputBuffers' ) )
            \eZExecution::discardOutputBuffers();
        else
            while ( ob_get_level() > 0 && @ob_end_clean() );

        header( 'Content-Type: text/event-stream; charset=utf-8' );
        header( 'Cache-Control: no-cache, no-store, must-revalidate' );
        header( 'Pragma: no-cache' );
        header( 'X-Accel-Buffering: no' );
        @set_time_limit( 0 );
        ignore_user_abort( false );

        $send = function ( array $payload )
        {
            echo 'data: ' . json_encode( $payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR ) . "\n\n";
            echo ': ' . str_repeat( ' ', 2048 ) . "\n\n";
            flush();
        };

        // the state of every event so far, also of those the page already has
        $before = \expMigrationProgress::read( $job['progress'], 0 );
        $events = $before['events'];
        if ( $offset > $before['offset'] )
            $offset = $before['offset'];
        $state = \expMigrationProgress::fold( $events );
        $send( array( 'type' => 'progress', 'state' => $state, 'meter' => \expMigrationProgress::meterStep( $state['percent'] ), 'offset' => $before['offset'] ) );
        $offset = $before['offset'];

        $deadline = time() + self::MAX_SECONDS;
        $endedSince = false;
        while ( true )
        {
            $read = \expMigrationProgress::read( $job['progress'], $offset );
            if ( $read['events'] )
            {
                $state = \expMigrationProgress::fold( $read['events'], $state );
                $send( array( 'type' => 'progress', 'state' => $state, 'meter' => \expMigrationProgress::meterStep( $state['percent'] ), 'offset' => $read['offset'] ) );
            }
            $offset = $read['offset'];

            if ( $logFile !== '' )
            {
                clearstatcache( true, $logFile );
                $size = is_file( $logFile ) ? (int)filesize( $logFile ) : 0;
                if ( $size < $logOffset )
                    $logOffset = 0;
                if ( $size > $logOffset && ( $handle = @fopen( $logFile, 'rb' ) ) )
                {
                    fseek( $handle, $logOffset );
                    $chunk = (string)stream_get_contents( $handle );
                    fclose( $handle );
                    $end = strrpos( $chunk, "\n" );
                    if ( $end !== false )
                    {
                        foreach ( explode( "\n", substr( $chunk, 0, $end ) ) as $line )
                            if ( trim( $line ) !== '' )
                                $send( array( 'type' => 'log', 'text' => rtrim( $line ), 'level' => self::level( $line ), 'offset' => $logOffset + $end + 1 ) );
                        $logOffset += $end + 1;
                    }
                }
            }

            if ( !\expMigrationJob::alive( (int)$job['pid'] ) )
            {
                // one more pass after the end, for what the run wrote as it ended
                if ( $endedSince === false )
                    $endedSince = time();
                else
                {
                    $ended = \expMigrationJob::get( $runId );
                    $send( array( 'type' => 'done', 'exit' => $ended ? $ended['exit'] : null ) );
                    break;
                }
            }
            if ( time() > $deadline )
            {
                $send( array( 'type' => 'info', 'text' => 'reconnect' ) );
                break;
            }
            if ( connection_aborted() )
                break;
            usleep( 700000 );
        }

        echo "event: end\ndata: {}\n\n";
        flush();
        \eZExecution::cleanExit();
        return $this->viewResult( null, null );
    }

    /**
     * The level of a run log line ("2026-10-31T12:00:00+00:00 info ..."), info when it names none.
     *
     * @param string $line
     * @return string
     */
    public static function level( $line )
    {
        return preg_match( '/^\S+\s+(debug|info|notice|warning|error)\b/', (string)$line, $m ) ? $m[1] : 'info';
    }
}

}
?>
