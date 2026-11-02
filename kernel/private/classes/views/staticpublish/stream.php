<?php
/**
 * The staticpublish/stream view (/staticpublish/stream/<job id>/(offset)/<bytes>): follows a job of the administration
 * as Server-Sent Events while it runs, modelled on migration/stream. It reads the job's progress file
 * (expStaticProgress) from the offset the page already has, so a reconnect sends nothing twice, and ends when the
 * job's process has ended and the file is read to the end. The job is not affected by the page.
 *
 * Each event is a JSON object: type progress (state: expStaticProgress::fold() of every event so far, meter: the step
 * of the meter classes, offset), line (text: one line of the progress, level), done (exit: the report's exit code, or
 * null without a report) or info. Policy staticpublish/read for the job's target.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Staticpublish
{

class Stream extends Page
{
    /** How long one connection follows a job; the page connects again after it. */
    const MAX_SECONDS = 1800;

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $id = isset( $Params['JobID'] ) ? (string)$Params['JobID'] : '';
        $job = \expStaticJob::get( $id );
        if ( !$job )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );
        if ( !\expStaticAdmin::canDo( 'read', $job['target'] ) )
            return $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );
        $offset = max( 0, (int)\expViewParameters::get( $Params, 'offset', 'Offset' ) );

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

        $before = \expStaticProgress::read( $job['progress'], 0 );
        $state = \expStaticProgress::fold( $before['events'], null, (int)$job['expected'] );
        if ( $offset > $before['offset'] )
            $offset = $before['offset'];
        // the lines the page does not have yet
        $since = \expStaticProgress::read( $job['progress'], $offset );
        foreach ( $since['events'] as $event )
            if ( $event['event'] !== 'end' && $event['message'] !== '' )
                $send( array( 'type' => 'line', 'text' => (string)$event['message'], 'level' => self::level( $event['event'] ) ) );
        $send( array( 'type' => 'progress', 'state' => $state, 'meter' => \expStaticProgress::meterStep( $state['percent'] ), 'offset' => $before['offset'] ) );
        $offset = $before['offset'];

        $deadline = time() + self::MAX_SECONDS;
        $endedSince = false;
        $beats = 0;
        while ( true )
        {
            $read = \expStaticProgress::read( $job['progress'], $offset );
            if ( $read['events'] )
            {
                $state = \expStaticProgress::fold( $read['events'], $state, (int)$job['expected'] );
                foreach ( $read['events'] as $event )
                    if ( $event['event'] !== 'end' && $event['message'] !== '' )
                        $send( array( 'type' => 'line', 'text' => (string)$event['message'], 'level' => self::level( $event['event'] ) ) );
                $send( array( 'type' => 'progress', 'state' => $state, 'meter' => \expStaticProgress::meterStep( $state['percent'] ), 'offset' => $read['offset'] ) );
            }
            $offset = $read['offset'];

            if ( !\expStaticJob::alive( (int)$job['pid'] ) )
            {
                if ( $endedSince === false )
                    $endedSince = time();
                else
                {
                    $ended = \expStaticJob::get( $id );
                    $send( array( 'type' => 'done', 'exit' => $ended ? $ended['exit'] : null ) );
                    break;
                }
            }
            if ( time() > $deadline )
            {
                $send( array( 'type' => 'info', 'text' => 'reconnect' ) );
                break;
            }
            // a comment now and then, so a page that went away is noticed and its worker freed (a long commit
            // writes no progress for minutes)
            if ( ++$beats % 14 === 0 )
            {
                echo ": ping\n\n";
                flush();
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
     * The level of a progress event for the page. Pure.
     *
     * @param string $event
     * @return string error, warning or info
     */
    public static function level( $event )
    {
        return $event === 'error' ? 'error' : ( $event === 'warn' ? 'warning' : 'info' );
    }
}

}
?>
