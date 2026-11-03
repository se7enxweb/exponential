<?php
/**
 * File containing the expViewCountLogParser class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Reads one line of a web server access log into its fields, for the view counter (expViewCountUpdater).
 *
 * The formats are told apart by the line itself:
 *
 * - common:          host ident user [time] "request" status bytes
 * - combined:        common + "referer" "user agent" (Apache, nginx, Plesk "plesklog", Velocity)
 * - vhost_combined:  "host:port " in front of combined (Apache's vhost_combined, one log for several hosts)
 *
 * Anything after the user agent (nginx's "$http_x_forwarded_for", Velocity's request time) is ignored, and so is
 * an empty client address (Velocity writes none when the peer is not known). Quoted
 * fields may hold escaped quotes (\"), as Apache writes them; IPv6 addresses are taken as they are. A format of
 * its own is read with a regular expression of named groups (logfile.ini [ViewCountSettings] LineRegexp): ip,
 * time, request, status and, optional, host, agent, referer.
 */
class expViewCountLogParser
{
    /** The built-in pattern: common, combined and vhost_combined. */
    const PATTERN = '~^(?:(?P<vhost>[^\s\[\]"]+)\s+)?(?P<ip>\S*)\s+(?P<ident>\S+)\s+(?P<user>\S+)\s+\[(?P<time>[^\]]+)\]\s+"(?P<request>(?:[^"\\\\]|\\\\.)*)"\s+(?P<status>\d{3}|-)\s+(?P<bytes>\d+|-)(?:\s+"(?P<referer>(?:[^"\\\\]|\\\\.)*)"\s+"(?P<agent>(?:[^"\\\\]|\\\\.)*)")?(?P<rest>.*)$~s';

    /** @var string */
    private $pattern;

    /**
     * @param string $pattern a regular expression with named groups, '' for the built-in formats
     * @throws InvalidArgumentException when the pattern does not compile or lacks a required group
     */
    public function __construct( $pattern = '' )
    {
        $pattern = (string)$pattern;
        if ( $pattern === '' )
        {
            $this->pattern = self::PATTERN;
            return;
        }
        if ( @preg_match( $pattern, '' ) === false )
            throw new InvalidArgumentException( 'logfile.ini [ViewCountSettings] LineRegexp is not a valid regular expression' );
        foreach ( array( 'time', 'request', 'status' ) as $group )
        {
            if ( strpos( $pattern, '?P<' . $group . '>' ) === false && strpos( $pattern, '?<' . $group . '>' ) === false )
                throw new InvalidArgumentException( "logfile.ini [ViewCountSettings] LineRegexp has no group named $group" );
        }
        $this->pattern = $pattern;
    }

    /**
     * The fields of a line, or null when it is not a request line.
     *
     * @param string $line one line, with or without its line end
     * @return array|null hash host ('' when the line names none; lower case, without the port), port (int|null),
     *                    ip, time (the text between the brackets), method (upper case; '' when the request is not
     *                    "METHOD target [protocol]"), target (as requested, still URL encoded), protocol, status
     *                    (int, 0 for "-"), referer and agent ('' when the format has none; escapes removed)
     */
    public function parse( $line )
    {
        $line = rtrim( (string)$line, "\r\n" );
        if ( $line === '' || !preg_match( $this->pattern, $line, $m ) )
            return null;
        $field = function ( $name ) use ( $m )
        {
            return isset( $m[$name] ) && $m[$name] !== '-' ? (string)$m[$name] : '';
        };
        $host = '';
        $port = null;
        $vhost = $field( 'vhost' ) !== '' ? $field( 'vhost' ) : $field( 'host' );
        if ( $vhost !== '' )
            list( $host, $port ) = self::splitHost( $vhost );

        $method = '';
        $target = '';
        $protocol = '';
        $request = self::unescape( $field( 'request' ) );
        if ( preg_match( '~^([A-Za-z]+) (\S+)(?: (HTTP/[0-9.]+))?$~', $request, $r ) )
        {
            $method = strtoupper( $r[1] );
            $target = $r[2];
            $protocol = isset( $r[3] ) ? $r[3] : '';
        }
        return array(
            'host' => $host,
            'port' => $port,
            'ip' => $field( 'ip' ),
            'time' => $field( 'time' ),
            'method' => $method,
            'target' => $target,
            'protocol' => $protocol,
            'status' => ctype_digit( $field( 'status' ) ) ? (int)$field( 'status' ) : 0,
            'referer' => self::unescape( $field( 'referer' ) ),
            'agent' => self::unescape( $field( 'agent' ) ),
        );
    }

    /**
     * The Unix time of a log time stamp ("10/Oct/2026:13:55:36 -0700"), null when it is not one.
     *
     * @param string $time
     * @return int|null
     */
    public static function timestamp( $time )
    {
        $date = DateTime::createFromFormat( 'd/M/Y:H:i:s O', trim( (string)$time ) );
        return $date ? $date->getTimestamp() : null;
    }

    /**
     * The path of a request target: without the scheme and host of an absolute target, without the query and the
     * fragment, still URL encoded; '' when the target is not a path ("*", "example.com:443").
     *
     * @param string $target
     * @return string
     */
    public static function path( $target )
    {
        $target = (string)$target;
        if ( preg_match( '~^[a-z][a-z0-9+.-]*://[^/]*(/.*)?$~i', $target, $m ) )
            $target = isset( $m[1] ) ? $m[1] : '/';
        $target = preg_replace( '~[?#].*$~s', '', $target );
        return ( $target !== '' && $target[0] === '/' ) ? $target : '';
    }

    /**
     * @param string $value host or host:port, [v6]:port
     * @return array host (lower case) and port (int|null)
     */
    public static function splitHost( $value )
    {
        $value = strtolower( trim( (string)$value ) );
        if ( preg_match( '~^\[([^\]]+)\](?::(\d+))?$~', $value, $m ) )
            return array( $m[1], isset( $m[2] ) ? (int)$m[2] : null );
        if ( substr_count( $value, ':' ) === 1 )
        {
            list( $host, $port ) = explode( ':', $value );
            return array( $host, ctype_digit( $port ) ? (int)$port : null );
        }
        return array( $value, null );
    }

    /** Removes the escapes Apache writes into a quoted field (\" \\ \xhh). */
    private static function unescape( $value )
    {
        if ( strpos( $value, '\\' ) === false )
            return $value;
        return preg_replace_callback( '~\\\\(x[0-9a-fA-F]{2}|.)~s', function ( $m )
        {
            return $m[1][0] === 'x' && strlen( $m[1] ) === 3 ? chr( hexdec( substr( $m[1], 1 ) ) ) : $m[1];
        }, $value );
    }
}
