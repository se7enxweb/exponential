<?php
/**
 * File containing the expMigrationProgress class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The progress of a migration run as a file of events, one JSON object per line: the run's process writes it through
 * the runner's progress callback (writer(), the option --progress=<file> of exp:migration:migrate, :resume and
 * :rollback), the administration reads it while the run goes on (read()) and folds the events into what the page shows
 * (fold(): how many of how many, the migration running now, the phase, the end).
 *
 * A line: {"time": <unix time>, "event": "<run|phase|step|step-done|host|end>", ...the data of the event}
 * (expMigrationRunner names the events and their data; a deploy, expMigrationRemoteDeploy, adds host: the state of one
 * remote). Lines are appended whole under an exclusive lock, so a reader
 * never takes half a line for an event: read() stops before a line without its newline and leaves it for the next read.
 * No secrets and no personal data are written: ids of migrations, states, counts and times only.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationProgress
{
    /** The events a progress file holds. */
    const EVENTS = array( 'run', 'phase', 'step', 'step-done', 'host', 'end' );

    /** The longest line read() accepts (a line beyond it is skipped). */
    const MAX_LINE = 65536;

    /**
     * A progress callback for the runner that appends each event to a file.
     *
     * @param string $file
     * @param int|null $time a fixed clock (tests)
     * @return callable( string $event, array $data )
     */
    public static function writer( $file, $time = null )
    {
        $file = (string)$file;
        return function ( $event, array $data = array() ) use ( $file, $time ) {
            $line = self::line( $event, $data, $time === null ? time() : (int)$time );
            if ( $line !== '' )
                @file_put_contents( $file, $line, FILE_APPEND | LOCK_EX );
        };
    }

    /**
     * One line of the file. Pure.
     *
     * @param string $event
     * @param array $data
     * @param int $time
     * @return string the JSON object and a newline, '' for an event that is not one of EVENTS
     */
    public static function line( $event, array $data, $time )
    {
        if ( !in_array( (string)$event, self::EVENTS, true ) )
            return '';
        unset( $data['time'], $data['event'] );
        $json = json_encode( array( 'time' => (int)$time, 'event' => (string)$event ) + $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR );
        return is_string( $json ) ? $json . "\n" : '';
    }

    /**
     * The events of a file from a byte offset on.
     *
     * @param string $file
     * @param int $offset where the last read stopped
     * @return array events (each an array with time and event), offset (where the next read starts)
     */
    public static function read( $file, $offset = 0 )
    {
        $offset = max( 0, (int)$offset );
        clearstatcache( true, (string)$file );
        if ( !is_file( (string)$file ) )
            return array( 'events' => array(), 'offset' => $offset );
        $size = (int)filesize( (string)$file );
        if ( $size < $offset )
            $offset = 0;
        if ( $size === $offset )
            return array( 'events' => array(), 'offset' => $offset );
        $handle = @fopen( (string)$file, 'rb' );
        if ( !$handle )
            return array( 'events' => array(), 'offset' => $offset );
        fseek( $handle, $offset );
        $chunk = (string)stream_get_contents( $handle );
        fclose( $handle );
        return self::parse( $chunk, $offset );
    }

    /**
     * The whole lines of a chunk read at an offset. Pure.
     *
     * @param string $chunk
     * @param int $offset
     * @return array events, offset (after the last whole line)
     */
    public static function parse( $chunk, $offset = 0 )
    {
        $events = array();
        $end = strrpos( (string)$chunk, "\n" );
        if ( $end === false )
            return array( 'events' => array(), 'offset' => (int)$offset );
        foreach ( explode( "\n", substr( (string)$chunk, 0, $end ) ) as $line )
        {
            if ( $line === '' || strlen( $line ) > self::MAX_LINE )
                continue;
            $event = json_decode( $line, true );
            if ( is_array( $event ) && isset( $event['event'] ) && in_array( $event['event'], self::EVENTS, true ) )
                $events[] = $event;
        }
        return array( 'events' => $events, 'offset' => (int)$offset + $end + 1 );
    }

    /**
     * What the page shows, from the events so far. Pure.
     *
     * @param array $events in their order
     * @param array|null $state the state of earlier events, to go on from
     * @return array mode, total, done (finished steps), current (the migration running now, '' between them), phase,
     *               failed (ids), percent (0 to 100), ended (bool), exit (null until the end), hosts (a deploy: remote id =>
     *               state, phase, problems)
     */
    public static function fold( array $events, $state = null )
    {
        $state = is_array( $state ) ? $state : array( 'mode' => '', 'total' => 0, 'done' => 0, 'current' => '', 'phase' => '', 'failed' => array(),
                                                         'percent' => 0, 'ended' => false, 'exit' => null, 'hosts' => array() );
        if ( !isset( $state['hosts'] ) )
            $state['hosts'] = array();
        foreach ( $events as $event )
        {
            switch ( isset( $event['event'] ) ? $event['event'] : '' )
            {
                case 'run':
                    $state['mode'] = isset( $event['mode'] ) ? (string)$event['mode'] : '';
                    $state['total'] = isset( $event['total'] ) ? max( 0, (int)$event['total'] ) : 0;
                    break;
                case 'phase':
                    $state['phase'] = isset( $event['name'] ) ? (string)$event['name'] : '';
                    $state['current'] = '';
                    break;
                case 'step':
                    $state['current'] = isset( $event['id'] ) ? (string)$event['id'] : '';
                    $state['phase'] = 'migrations';
                    if ( isset( $event['total'] ) )
                        $state['total'] = max( $state['total'], (int)$event['total'] );
                    break;
                case 'step-done':
                    $state['done'] = max( $state['done'], isset( $event['index'] ) ? (int)$event['index'] : $state['done'] + 1 );
                    $state['current'] = '';
                    if ( isset( $event['action'] ) && $event['action'] === 'failed' && isset( $event['id'] ) )
                        $state['failed'][] = (string)$event['id'];
                    break;
                case 'host':
                    if ( isset( $event['remote'] ) && preg_match( '/^[A-Za-z0-9][A-Za-z0-9_-]{0,63}$/', (string)$event['remote'] ) )
                        $state['hosts'][(string)$event['remote']] = array( 'state' => isset( $event['state'] ) ? (string)$event['state'] : '',
                                                                          'phase' => isset( $event['phase'] ) ? (string)$event['phase'] : '',
                                                                          'problems' => isset( $event['problems'] ) ? max( 0, (int)$event['problems'] ) : 0 );
                    break;
                case 'end':
                    $state['ended'] = true;
                    $state['current'] = '';
                    $state['exit'] = isset( $event['exit'] ) ? (int)$event['exit'] : null;
                    break;
            }
        }
        $state['done'] = min( $state['done'], $state['total'] );
        $state['percent'] = $state['ended'] ? 100 : ( $state['total'] > 0 ? (int)floor( $state['done'] * 100 / $state['total'] ) : 0 );
        return $state;
    }

    /**
     * A percentage rounded down to the steps of the meter classes (.a4-pct-0, -4, ... -100). Pure.
     *
     * @param int $percent
     * @return int
     */
    public static function meterStep( $percent )
    {
        return (int)( floor( max( 0, min( 100, (int)$percent ) ) / 4 ) * 4 );
    }
}
