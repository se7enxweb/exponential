<?php
/**
 * File containing the expStaticProgress class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The progress of a static publishing job as a file of events, one JSON object per line, modelled on
 * expMigrationProgress: the build, publish or rollback writes it through its emit callback (writer(), the option
 * --progress=<file> of exp:static:build, exp:static:publish and exp:static:rollback), the administration reads it
 * while the job goes on (read()) and folds the events into what the page shows (fold(): the phase, the pages written,
 * the warnings and errors, the end).
 *
 * A line: {"time": <unix time>, "event": "<phase|phase-item|ok|info|warn|error|end|...>", "message": "<text>"}
 * Lines are appended whole under an exclusive lock, so a reader never takes half a line for an event: read() stops
 * before a line without its newline. No secret is written: the messages are those the commands print, which never
 * hold a credential's value.
 *
 * @since 6.0.15
 * @package kernel
 */
class expStaticProgress
{
    /** The longest line read() accepts (a line beyond it is skipped). */
    const MAX_LINE = 65536;

    /** The longest message kept in a line. */
    const MAX_MESSAGE = 2000;

    /**
     * An emit callback for expStaticBuild and expStaticPublisher that appends each event to a file.
     *
     * @param string $file
     * @param int|null $time a fixed clock (tests)
     * @return callable( string $type, string $message, array $data = array() )
     */
    public static function writer( $file, $time = null )
    {
        $file = (string)$file;
        return function ( $type, $message = '', array $data = array() ) use ( $file, $time ) {
            $line = self::line( (string)$type, (string)$message, $time === null ? time() : (int)$time );
            if ( $line !== '' )
                @file_put_contents( $file, $line, FILE_APPEND | LOCK_EX );
        };
    }

    /**
     * One line of the file. Pure.
     *
     * @param string $event a word: letters and -
     * @param string $message
     * @param int $time
     * @return string the JSON object and a newline, '' for an event that is no word
     */
    public static function line( $event, $message, $time )
    {
        if ( !preg_match( '/^[a-z][a-z-]{0,30}$/', (string)$event ) )
            return '';
        $message = (string)$message;
        if ( strlen( $message ) > self::MAX_MESSAGE )
            $message = substr( $message, 0, self::MAX_MESSAGE ) . '...';
        $json = json_encode( array( 'time' => (int)$time, 'event' => (string)$event, 'message' => $message ),
                             JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE );
        return is_string( $json ) ? $json . "\n" : '';
    }

    /**
     * The events of a file from a byte offset on.
     *
     * @param string $file
     * @param int $offset where the last read stopped
     * @return array events, offset (where the next read starts)
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
            if ( is_array( $event ) && isset( $event['event'] ) && is_string( $event['event'] ) )
                $events[] = $event + array( 'message' => '', 'time' => 0 );
        }
        return array( 'events' => $events, 'offset' => (int)$offset + $end + 1 );
    }

    /**
     * What the page shows, from the events so far. Pure.
     *
     * @param array $events in their order
     * @param array|null $state the state of earlier events, to go on from
     * @param int $expected the pages the job is expected to write (those of the last build; 0 when unknown)
     * @return array phase (the last phase line), pages (pages written), warnings, errors (counts), percent (0 to 100;
     *               an estimate while the job runs), ended (bool), exit (null until the end)
     */
    public static function fold( array $events, $state = null, $expected = 0 )
    {
        $state = is_array( $state ) ? $state : array( 'phase' => '', 'pages' => 0, 'warnings' => 0, 'errors' => 0, 'percent' => 0,
                                                         'ended' => false, 'exit' => null );
        foreach ( $events as $event )
        {
            $message = isset( $event['message'] ) ? (string)$event['message'] : '';
            switch ( isset( $event['event'] ) ? $event['event'] : '' )
            {
                case 'phase':
                case 'phase-item':
                    $state['phase'] = $message;
                    break;
                case 'ok':
                    $state['pages']++;
                    break;
                case 'warn':
                    $state['warnings']++;
                    break;
                case 'error':
                    $state['errors']++;
                    break;
                case 'end':
                    $state['ended'] = true;
                    $state['exit'] = is_numeric( $message ) ? (int)$message : null;
                    break;
            }
        }
        if ( $state['ended'] )
            $state['percent'] = 100;
        else if ( $expected > 0 )
            $state['percent'] = (int)min( 95, floor( $state['pages'] * 90 / $expected ) );
        else
            $state['percent'] = (int)floor( 90 * $state['pages'] / ( $state['pages'] + 25 ) );
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
