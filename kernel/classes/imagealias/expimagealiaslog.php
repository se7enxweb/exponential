<?php
/**
 * File containing the expImageAliasLog class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The history of on-demand image aliases: var/log/imagealias.log (the site's log directory), one line per event,
 * written through eZLog (so it is rotated under the kernel's log rules: site.ini [FileSettings] LogFileMaxSize,
 * LogFileRotateCount, LogFileCompress). A line:
 *
 *   [ Nov 13 2026 09:12:01 ][ site ][ <request> ] event=generate result=ok ms=84 alias=small run=- path=var/site/storage/...
 *
 * Events: generate, serve, stage, placeholder, fail, defer (result=timeout: no conversion slot or lock within the
 * wait; the request answered the placeholder and a job makes the alias, so it is no failure), run-start, run-end,
 * queue (work handed to the job queue), pause and resume (of bulk runs). read() gives the newest lines back parsed,
 * for the administration (setup/aliases). Lines written before defer existed say event=fail result=timeout; parse()
 * reads them as defer.
 *
 * @since 6.0.15
 * @package kernel
 */
class expImageAliasLog
{
    const FILE = 'imagealias.log';

    /** The events a line can carry */
    const EVENTS = array( 'generate', 'serve', 'stage', 'placeholder', 'fail', 'defer', 'run-start', 'run-end', 'queue', 'pause', 'resume' );

    /** @var string|null a log directory for this process (tests) */
    protected static $directory = null;

    /**
     * Writes one event.
     *
     * @param string $event one of EVENTS
     * @param array $data alias, path, ms, result, run (and any other short value); values are made one line
     * @return bool
     */
    public static function write( $event, array $data = array() )
    {
        if ( !in_array( $event, self::EVENTS, true ) )
            return false;
        $line = self::format( $event, $data );
        if ( self::$directory !== null )
        {
            if ( !is_dir( self::$directory ) )
                eZDir::mkdir( self::$directory, false, true );
            return @file_put_contents( self::path(), '[ ' . date( 'M d Y H:i:s' ) . ' ][ - ][ - ] ' . $line . "\n", FILE_APPEND | LOCK_EX ) !== false;
        }
        // A bulk run writes a line per alias: the log gets its own size (image.ini [AliasSettings] LogFileMaxSize),
        // so a run's history is not rotated away within minutes under the kernel's size for all logs
        $before = $GLOBALS['eZMaxLogSize'] ?? null;
        $GLOBALS['eZMaxLogSize'] = expImageAliasSettings::logFileMaxSize();
        try
        {
            eZLog::write( $line, self::FILE );
        }
        finally
        {
            if ( $before === null )
                unset( $GLOBALS['eZMaxLogSize'] );
            else
                $GLOBALS['eZMaxLogSize'] = $before;
        }
        return true;
    }

    /**
     * The event of an attempt to make an alias or a variant with this result: ok is generate, timeout (no conversion
     * slot or lock in time: the alias is made later by a job) is defer, anything else is fail.
     *
     * @param string $result ok, timeout, failed ...
     * @return string generate, defer or fail
     */
    public static function eventFor( $result )
    {
        if ( $result === 'ok' )
            return 'generate';
        return $result === 'timeout' ? 'defer' : 'fail';
    }

    /**
     * The message part of a line: event=... then the fields in a fixed order, then the others.
     *
     * @param string $event
     * @param array $data
     * @return string
     */
    public static function format( $event, array $data )
    {
        $parts = array( 'event=' . $event );
        $order = array( 'result', 'ms', 'alias', 'run' );
        foreach ( $order as $key )
        {
            if ( array_key_exists( $key, $data ) )
                $parts[] = $key . '=' . self::value( $data[$key] );
        }
        foreach ( $data as $key => $value )
        {
            if ( in_array( $key, $order, true ) || $key === 'path' || !preg_match( '/^[a-z_]{1,32}$/', (string)$key ) )
                continue;
            $parts[] = $key . '=' . self::value( $value );
        }
        // The path last: it is the only value that may hold spaces
        if ( array_key_exists( 'path', $data ) )
            $parts[] = 'path=' . str_replace( array( "\r", "\n" ), ' ', (string)$data['path'] );
        return implode( ' ', $parts );
    }

    /**
     * The newest $limit lines of the log, newest first, parsed.
     *
     * @param int $limit
     * @param string|null $event only this event
     * @return array of array( time, siteaccess, event, result, ms, alias, run, path, fields, line )
     */
    public static function read( $limit = 100, $event = null )
    {
        $file = self::path();
        if ( !is_file( $file ) || !is_readable( $file ) )
            return array();
        $lines = self::tail( $file, max( 1, (int)$limit ) * ( $event === null ? 1 : 20 ) + 1 );
        $out = array();
        foreach ( array_reverse( $lines ) as $line )
        {
            $entry = self::parse( $line );
            if ( $entry === null || ( $event !== null && $entry['event'] !== $event ) )
                continue;
            $out[] = $entry;
            if ( count( $out ) >= $limit )
                break;
        }
        return $out;
    }

    /**
     * One log line parsed, or null when it is not a line of this log. An older "event=fail result=timeout" of an
     * alias or variant (not a staging fetch, which names its source) is read as event defer.
     *
     * @param string $line
     * @return array|null
     */
    public static function parse( $line )
    {
        // The request context (a URL) may hold "]": the message starts at the first " ] event="
        if ( !preg_match( '/^\[ (.+?) \]\[ (.*?) \]\[ .*? \] (event=[a-z-]+(?: .*)?)$/', rtrim( (string)$line, "\r\n" ), $match ) )
            return null;
        $message = $match[3];
        $path = '';
        $pathPos = strpos( $message, ' path=' );
        if ( $pathPos !== false )
        {
            $path = substr( $message, $pathPos + 6 );
            $message = substr( $message, 0, $pathPos );
        }
        $fields = array();
        foreach ( explode( ' ', $message ) as $pair )
        {
            $eq = strpos( $pair, '=' );
            if ( $eq !== false )
                $fields[substr( $pair, 0, $eq )] = substr( $pair, $eq + 1 );
        }
        $time = strtotime( $match[1] );
        $event = isset( $fields['event'] ) ? $fields['event'] : '';
        // A line from before the defer event: a timeout was logged as fail (fields keeps what the line says)
        if ( $event === 'fail' && isset( $fields['result'] ) && $fields['result'] === 'timeout' && !isset( $fields['source'] ) )
            $event = 'defer';
        return array( 'time' => $time === false ? 0 : $time, 'siteaccess' => $match[2],
                      'event' => $event,
                      'result' => isset( $fields['result'] ) ? $fields['result'] : '',
                      'ms' => isset( $fields['ms'] ) ? (int)$fields['ms'] : null,
                      'alias' => isset( $fields['alias'] ) ? $fields['alias'] : '',
                      'run' => isset( $fields['run'] ) ? $fields['run'] : '',
                      'path' => $path, 'fields' => $fields, 'line' => rtrim( (string)$line, "\r\n" ) );
    }

    /**
     * @return string the log file
     */
    public static function path()
    {
        if ( self::$directory !== null )
            return self::$directory . '/' . self::FILE;
        return rtrim( eZDebug::instance()->logDirectory(), '/' ) . '/' . self::FILE;
    }

    /**
     * Sets the log directory for this process (tests); null: the site's log directory.
     *
     * @param string|null $directory
     */
    public static function setDirectory( $directory )
    {
        self::$directory = $directory === null ? null : rtrim( (string)$directory, '/' );
    }

    /**
     * A value of a field: no spaces, no line breaks, at most 200 characters.
     *
     * @param mixed $value
     * @return string
     */
    protected static function value( $value )
    {
        if ( is_bool( $value ) )
            $value = $value ? '1' : '0';
        $value = preg_replace( '/[\s]+/', '_', (string)$value );
        return $value === '' ? '-' : substr( $value, 0, 200 );
    }

    /**
     * The last $count lines of $file, read from its end.
     *
     * @param string $file
     * @param int $count
     * @return string[]
     */
    protected static function tail( $file, $count )
    {
        $handle = @fopen( $file, 'rb' );
        if ( !$handle )
            return array();
        $size = filesize( $file );
        $chunk = 65536;
        $data = '';
        $position = $size;
        while ( $position > 0 && substr_count( $data, "\n" ) <= $count )
        {
            $read = min( $chunk, $position );
            $position -= $read;
            fseek( $handle, $position );
            $data = fread( $handle, $read ) . $data;
        }
        fclose( $handle );
        $lines = explode( "\n", rtrim( $data, "\n" ) );
        if ( $position > 0 )
            array_shift( $lines );
        return array_slice( $lines, -$count );
    }
}
