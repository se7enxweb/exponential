<?php
/**
 * Reads the lines of a log file as entries (doc/features/6.0/log-viewer.md, "The line format").
 *
 * The formats, tried in this order on a line that starts an entry:
 *  - eZDebug:  [ Oct 17 2026 01:54:58 ] [ip] [siteaccess] [url or command line] Label:
 *              (the message follows on the next lines; older files have no siteaccess and url)
 *  - eZLog:    [ Oct 17 2026 01:54:58 ][ siteaccess ][ url ] message   (or [ date ] message)
 *  - ISO:      2026-10-17T01:54:58+02:00 message, or [2026-10-17 01:54:58] message
 *  - JSON:     one object per line with time / ts / timestamp, level, message / msg, ip, url / uri
 * Any other line continues the entry before it (a message over several lines, a stack trace); a chunk that starts
 * inside an entry gives an entry of continuation lines (cont = true) the viewer appends to the one it has.
 *
 * Pure: nothing here reads a file, a setting or the clock.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expLogParser
{
    /** The levels the viewer knows, most severe first */
    public static $levels = array( 'error', 'warning', 'notice', 'debug' );

    const DATE = '[A-Z][a-z]{2} \d{1,2} \d{4} \d{2}:\d{2}:\d{2}';

    /** eZDebug: date, ip, then optionally siteaccess and the request (which ends at "] ": it holds no space) */
    const RE_DEBUG = '/^\[ ([A-Z][a-z]{2} \d{1,2} \d{4} \d{2}:\d{2}:\d{2}) \] \[([^\]]*)\](?: \[([^\]\s]*)\](?: \[((?:[^\]]|\](?! ))*)\])?)? ?(.*)$/s';

    /** eZLog: date, then optionally siteaccess and request */
    const RE_LOG = '/^\[ ([A-Z][a-z]{2} \d{1,2} \d{4} \d{2}:\d{2}:\d{2}) \](?:\[ ([^\]]*?) \]\[ ((?:[^\]]|\](?!\s))*?) \])? ?(.*)$/s';

    /** ISO 8601 or SQL time at the start, optionally in brackets */
    const RE_ISO = '/^\[?(\d{4}-\d{2}-\d{2}[T ]\d{2}:\d{2}:\d{2}(?:[.,]\d{1,6})?(?:Z|[+\-]\d{2}:?\d{2})?)\]?\s*(.*)$/s';

    /** A level named at the start of a message or label */
    const RE_LEVEL = '/^\W{0,3}(?:php\s+)?(fatal(?:\s+error)?|critical|crit|emerg(?:ency)?|alert|error|err|warning|warn|strict(?:\s+standards)?|deprecated|notice|info|debug|trace)\b/i';

    /** The error reference of an error page (eZExecution::errorReference()) */
    const RE_REFERENCE = '/\bERR-[0-9A-F]{10}\b/';

    /** The links the viewer draws, tried in this order: type => pattern (group 1 is the value) */
    public static $linkPatterns = array(
        'reference' => '/\b(ERR-[0-9A-F]{10})\b/',
        'url' => '#\b(https?://[^\s\'"<>()\[\]{}]+)#',
        'node' => '/(?:\b(?:main[_ ]?node[_ ]?id|parent[_ ]?node[_ ]?id|node[_ ]?id|nodeid|node)\b\s*(?:[:=#]|=>|\bis\b)?\s*[\'"]?|\/content\/view\/[a-z_]+\/)(\d{1,10})\b/i',
        'object' => '/\b(?:content[_ ]?object[_ ]?id|contentobjectid|object[_ ]?id|objectid|object)\b\s*(?:[:=#]|=>|\bis\b)?\s*[\'"]?(\d{1,10})\b/i',
        'user' => '/\b(?:user[_ ]?id|userid|user)\b\s*(?:[:=#]|=>)\s*[\'"]?(\d{1,10})\b/i',
        'class' => '/\bclass(?:[_ ]?identifier)?\b\s*(?:[:=]|=>)?\s*[\'"]([a-z][a-z0-9_]{1,63})[\'"]/',
        'file' => '#\b((?:design|extension|kernel|lib|settings|bin|cronjobs|var/[\w\-]+/cache/template/compiled)/[\w./\-]*\.(?:tpl|php|ini|ini\.append\.php|css|js))\b#',
    );

    /** The longest text of one entry kept (the rest is cut, with a note) */
    const MAX_ENTRY_BYTES = 65536;

    /**
     * Whether a line starts an entry.
     *
     * @param string $line
     * @return bool
     */
    public static function startsEntry( $line )
    {
        if ( $line === '' )
            return false;
        $c = $line[0];
        if ( $c === '[' )
            return preg_match( '/^\[ ?(?:[A-Z][a-z]{2} (?:\d|[A-Z][a-z]{2} {1,2}\d)|\d{4}-\d{2}-\d{2})/', $line ) === 1;
        if ( $c === '{' )
            return substr( rtrim( $line ), -1 ) === '}';
        if ( $c >= '0' && $c <= '9' && preg_match( '/^\d{4}-\d{2}-\d{2}[T ]\d{2}:\d{2}/', $line ) === 1 )
            return true;
        // a web server's access log: every line is an entry
        return strpos( $line, '] "' ) !== false && preg_match( self::RE_ACCESS, $line ) === 1;
    }

    /** A web server's access log, common or combined: host ident user [time] "request" status bytes ... */
    const RE_ACCESS = '#^(\S+) \S+ (\S+) \[(\d{2}/[A-Z][a-z]{2}/\d{4}:\d{2}:\d{2}:\d{2} [+\-]\d{4})\] "(?:(\S+) (\S+)(?: [^"]*)?|[^"]*)" (\d{3}) (\S+)(.*)$#s';

    /** A web server's error log (Apache 2.4): [Fri Oct 17 01:54:58.123456 2026] [module:level] [pid N] [client ip:port] message */
    const RE_APACHE_ERROR = '/^\[([A-Z][a-z]{2} [A-Z][a-z]{2} {1,2}\d{1,2} \d{2}:\d{2}:\d{2}(?:\.\d+)? \d{4})\] \[(?:[\w\-]+:)?(\w+)\](?: \[pid \d+(?::tid \d+)?\])?(?: \[client ([^\]]+)\])? ?(.*)$/s';

    /**
     * The header fields of the first line of an entry.
     *
     * @param string $line
     * @return array|null time_text, time (unix, or null), ip, siteaccess, url, message, format
     */
    public static function header( $line )
    {
        $line = rtrim( $line, "\r\n" );
        if ( $line === '' )
            return null;
        if ( $line[0] === '[' && preg_match( self::RE_DEBUG, $line, $m ) )
            return array( 'time_text' => $m[1], 'time' => self::time( $m[1] ), 'ip' => $m[2],
                          'siteaccess' => isset( $m[3] ) ? $m[3] : '', 'url' => isset( $m[4] ) ? $m[4] : '',
                          'message' => isset( $m[5] ) ? $m[5] : '', 'format' => 'debug' );
        if ( $line[0] === '[' && preg_match( self::RE_LOG, $line, $m ) )
            return array( 'time_text' => $m[1], 'time' => self::time( $m[1] ), 'ip' => '',
                          'siteaccess' => isset( $m[2] ) ? $m[2] : '', 'url' => isset( $m[3] ) ? $m[3] : '',
                          'message' => isset( $m[4] ) ? $m[4] : '', 'format' => 'log' );
        if ( $line[0] === '{' )
        {
            $data = json_decode( $line, true );
            if ( is_array( $data ) )
                return self::jsonHeader( $data );
        }
        if ( $line[0] === '[' && preg_match( self::RE_APACHE_ERROR, $line, $m ) )
        {
            $client = isset( $m[3] ) ? preg_replace( '/:\d+$/', '', $m[3] ) : '';
            return array( 'time_text' => $m[1], 'time' => self::time( preg_replace( '/\.\d+/', '', $m[1] ) ), 'ip' => $client,
                          'siteaccess' => '', 'url' => '', 'message' => $m[2] . ': ' . ( isset( $m[4] ) ? $m[4] : '' ),
                          'format' => 'apache_error', 'level' => self::level( $m[2] . ':' ) );
        }
        if ( strpos( $line, '] "' ) !== false && preg_match( self::RE_ACCESS, $line, $m ) )
        {
            $d = DateTime::createFromFormat( 'd/M/Y:H:i:s O', $m[3] );
            $status = (int)$m[6];
            return array( 'time_text' => $m[3], 'time' => $d ? $d->getTimestamp() : null, 'ip' => $m[1], 'siteaccess' => '',
                          'url' => isset( $m[5] ) ? (string)$m[5] : '',
                          'message' => ( $m[4] !== '' ? $m[4] . ' ' . $m[5] . ' ' : '' ) . $status . ' ' . $m[7] . $m[8],
                          'format' => 'access', 'status' => $status,
                          'level' => $status >= 500 ? 'error' : ( $status >= 400 ? 'warning' : 'notice' ) );
        }
        if ( preg_match( self::RE_ISO, $line, $m ) )
            return array( 'time_text' => $m[1], 'time' => self::time( $m[1] ), 'ip' => '', 'siteaccess' => '', 'url' => '',
                          'message' => $m[2], 'format' => 'iso' );
        return null;
    }

    /**
     * The header of a JSON line.
     *
     * @param array $data
     * @return array
     */
    protected static function jsonHeader( array $data )
    {
        $pick = function ( array $keys ) use ( $data ) {
            foreach ( $keys as $k )
                if ( isset( $data[$k] ) && is_scalar( $data[$k] ) )
                    return (string)$data[$k];
            return '';
        };
        $timeText = $pick( array( 'time', 'ts', 'timestamp', '@timestamp', 'date', 'datetime' ) );
        $time = null;
        if ( $timeText !== '' )
            $time = is_numeric( $timeText ) ? (int)( (float)$timeText > 1e11 ? (float)$timeText / 1000 : (float)$timeText ) : self::time( $timeText );
        $message = $pick( array( 'message', 'msg', 'text', 'event', 'name' ) );
        $level = $pick( array( 'level', 'severity', 'level_name', 'lvl' ) );
        $rest = $data;
        foreach ( array( 'message', 'msg', 'text' ) as $k )
            unset( $rest[$k] );
        $detail = $rest ? json_encode( $rest, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR ) : '';
        return array( 'time_text' => $timeText, 'time' => $time, 'ip' => $pick( array( 'ip', 'client_ip', 'remote_addr' ) ),
                      'siteaccess' => $pick( array( 'siteaccess', 'site' ) ), 'url' => $pick( array( 'url', 'uri', 'request_uri', 'path' ) ),
                      'message' => ( $level !== '' ? $level . ': ' : '' ) . ( $message !== '' ? $message : '' ) . ( $detail !== '' ? ( $message !== '' ? ' ' : '' ) . $detail : '' ),
                      'format' => 'json' );
    }

    /**
     * A time of a log line as a Unix time, in the server's time zone when it names none.
     *
     * @param string $text
     * @return int|null
     */
    public static function time( $text )
    {
        static $cache = array();
        if ( isset( $cache[$text] ) )
            return $cache[$text];
        $t = null;
        if ( preg_match( '/^[A-Z][a-z]{2} \d{1,2} \d{4} \d{2}:\d{2}:\d{2}$/', $text ) )
        {
            $d = DateTime::createFromFormat( '!M d Y H:i:s', $text );
            $t = $d ? $d->getTimestamp() : null;
        }
        else
        {
            $s = strtotime( str_replace( ',', '.', $text ) );
            $t = $s === false ? null : $s;
        }
        if ( count( $cache ) > 4096 )
            $cache = array();
        return $cache[$text] = $t;
    }

    /**
     * The level of an entry: the one its message names, else the file's, else notice.
     *
     * @param string $message the first line's message and the next line (eZDebug puts the label alone on the first)
     * @param string $fileLevel
     * @return string one of $levels
     */
    public static function level( $message, $fileLevel = '' )
    {
        if ( preg_match( self::RE_LEVEL, ltrim( (string)$message ), $m ) )
        {
            $word = strtolower( $m[1] );
            if ( preg_match( '/^(fatal|critical|crit|emerg|alert|error|err)/', $word ) )
                return 'error';
            if ( preg_match( '/^(warn|strict)/', $word ) )
                return 'warning';
            if ( preg_match( '/^(debug|trace)/', $word ) )
                return 'debug';
            return 'notice';
        }
        if ( in_array( $fileLevel, self::$levels, true ) )
            return $fileLevel;
        return 'notice';
    }

    /**
     * Where an entry comes from: the label of an eZDebug entry ("eZTemplate", "kernel/content/view.php"), "cli" for a
     * command line, or ''.
     *
     * @param array $header
     * @return string
     */
    public static function source( array $header )
    {
        $message = trim( (string)$header['message'] );
        if ( $message !== '' && preg_match( '/^(?:(?:Error|Warning|Notice|Debug|Strict|Deprecated)\s*(?:\(\w+\))?\s*:\s*)?([A-Za-z_][\w\\\\\/.:\-]{0,80}?)\s*(?::\s*$|::|\s+-\s|:\s)/', $message, $m ) )
            return rtrim( $m[1], ':' );
        if ( $header['url'] !== '' && !preg_match( '#^https?://#', $header['url'] ) )
            return 'cli';
        return '';
    }

    /**
     * The entries of a chunk of a file.
     *
     * @param string $chunk whole lines (as expLogTail reads them)
     * @param int $offset the chunk's byte offset in the file
     * @param string $fileLevel the file's level (FileLevels[])
     * @return array list of entries: offset, end, cont (bool), time, time_text, ip, siteaccess, url, level, source,
     *               text (the whole entry, header line included), references
     */
    public static function parse( $chunk, $offset = 0, $fileLevel = '' )
    {
        $entries = array();
        $current = null;
        $pos = 0;
        $length = strlen( $chunk );
        while ( $pos < $length )
        {
            $nl = strpos( $chunk, "\n", $pos );
            $end = $nl === false ? $length : $nl + 1;
            $line = substr( $chunk, $pos, $end - $pos );
            $plain = rtrim( $line, "\r\n" );
            $header = self::startsEntry( $plain ) ? self::header( $plain ) : null;
            if ( $header !== null )
            {
                if ( $current !== null )
                    $entries[] = self::finish( $current, $fileLevel );
                $current = array( 'offset' => $offset + $pos, 'end' => $offset + $end, 'cont' => false, 'header' => $header, 'lines' => array( $plain ), 'bytes' => strlen( $plain ) );
            }
            else
            {
                if ( $current === null )
                    $current = array( 'offset' => $offset + $pos, 'end' => $offset + $end, 'cont' => true, 'header' => null, 'lines' => array(), 'bytes' => 0 );
                if ( $current['bytes'] <= self::MAX_ENTRY_BYTES )
                {
                    $current['lines'][] = $plain;
                    $current['bytes'] += strlen( $plain ) + 1;
                }
                $current['end'] = $offset + $end;
            }
            $pos = $end;
        }
        if ( $current !== null )
            $entries[] = self::finish( $current, $fileLevel );
        return $entries;
    }

    /**
     * An entry from its lines.
     *
     * @param array $current
     * @param string $fileLevel
     * @return array
     */
    protected static function finish( array $current, $fileLevel )
    {
        $h = $current['header'];
        $text = implode( "\n", $current['lines'] );
        if ( $current['bytes'] > self::MAX_ENTRY_BYTES )
        {
            $text = substr( $text, 0, self::MAX_ENTRY_BYTES );
            $current['truncated'] = true;
        }
        $first = $h !== null ? (string)$h['message'] : (string)( isset( $current['lines'][0] ) ? $current['lines'][0] : '' );
        $levelText = $first;
        if ( $h !== null && isset( $current['lines'][1] ) && preg_match( '/^\s*\S{1,40}:\s*$/', $first ) && !preg_match( self::RE_LEVEL, ltrim( $first ) ) )
            $levelText = $current['lines'][1];
        preg_match_all( self::RE_REFERENCE, $text, $refs );
        // the message: the entry without the header fields of its first line
        $message = $text;
        if ( $h !== null )
        {
            $nl = strpos( $text, "\n" );
            $message = (string)$h['message'] . ( $nl !== false ? substr( $text, $nl ) : '' );
        }
        return array( 'offset' => $current['offset'], 'end' => $current['end'], 'cont' => $current['cont'], 'message' => $message,
                      'time' => $h !== null ? $h['time'] : null, 'time_text' => $h !== null ? $h['time_text'] : '',
                      'ip' => $h !== null ? $h['ip'] : '', 'siteaccess' => $h !== null ? $h['siteaccess'] : '',
                      'url' => $h !== null ? $h['url'] : '', 'format' => $h !== null ? $h['format'] : '',
                      'level' => isset( $h['level'] ) ? $h['level'] : ( $h !== null || $fileLevel !== '' ? self::level( $levelText, $fileLevel ) : 'notice' ),
                      'source' => $h !== null ? self::source( $h ) : '',
                      'text' => $text, 'truncated' => !empty( $current['truncated'] ),
                      'references' => array_values( array_unique( $refs[0] ) ) );
    }

    /**
     * A text cut into plain parts and links, for the viewer to draw without parsing it again: a list of
     * array( text ) and array( text, type, value ). The types: reference, url, node, object, user, class, file.
     * Overlapping matches go to the first type of $linkPatterns that found them.
     *
     * @param string $text
     * @param int $max the most links (the rest stays plain)
     * @return array
     */
    public static function parts( $text, $max = 50 )
    {
        $text = (string)$text;
        $found = array();
        if ( $text !== '' )
        {
            foreach ( self::$linkPatterns as $type => $pattern )
            {
                if ( !preg_match_all( $pattern, $text, $m, PREG_OFFSET_CAPTURE | PREG_SET_ORDER ) )
                    continue;
                foreach ( $m as $match )
                {
                    $value = $match[1][0];
                    $start = $match[1][1];
                    // a masked value (*** in it) is never a link
                    if ( strpos( $value, '***' ) !== false || ( $type === 'url' && strpos( $value, '.x.x' ) !== false ) )
                        continue;
                    if ( $type === 'url' )
                        $value = rtrim( $value, '.,;:' );
                    $found[] = array( $start, strlen( $value ), $type, $value );
                }
            }
        }
        usort( $found, function ( $a, $b ) { return $a[0] === $b[0] ? 0 : ( $a[0] < $b[0] ? -1 : 1 ); } );
        $parts = array();
        $pos = 0;
        $count = 0;
        foreach ( $found as $f )
        {
            if ( $f[0] < $pos || $count >= $max )
                continue;
            if ( $f[0] > $pos )
                $parts[] = array( substr( $text, $pos, $f[0] - $pos ) );
            $parts[] = array( substr( $text, $f[0], $f[1] ), $f[2], $f[3] );
            $pos = $f[0] + $f[1];
            $count++;
        }
        if ( $pos < strlen( $text ) )
            $parts[] = array( substr( $text, $pos ) );
        return $parts;
    }
}
