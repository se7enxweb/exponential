<?php
/**
 * Searches log files and their rotations, within limits (doc/features/6.0/log-viewer.md, "Searching").
 *
 * The criteria: a text (literal, or a regular expression), case sensitive or not; levels; a time range; the
 * siteaccess; part of the request; the client IP (its start); an error reference (ERR-XXXXXXXXXX). Every request
 * is bounded: at most MaxBytesPerRequest bytes read, TimeLimit milliseconds, MaxResults entries; a search that
 * stops at a limit gives a cursor ("<file index>:<byte offset>") to continue from, which is how the page offers
 * "More results". A regular expression runs with pcre.backtrack_limit set to BacktrackLimit, and a pattern that
 * exceeds it on a line stops the search with the error "regex_limit" instead of holding the worker.
 * A time range starts each plain file at the first entry of the range (a binary search on the times, a few small
 * reads), so a narrow window in a big file costs little.
 *
 * The files are searched in the order given (the page gives the current file, then .1, .2 ... and the .gz ones);
 * within a file the entries come in the order they were written.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expLogSearch
{
    /** @var expLogViewerConfig */
    protected $config;

    /** @var float|null set by tests: the time budget in seconds instead of TimeLimit */
    public $timeBudget = null;

    public function __construct( expLogViewerConfig $config )
    {
        $this->config = $config;
    }

    /**
     * The criteria of a request, cleaned. Pure.
     *
     * @param array $in q, regex, case, levels (list or comma separated), from, to (a time strtotime() reads, or a
     *                  Unix time), siteaccess, uri, ip, reference
     * @param int $maxPattern the longest text
     * @return array q, regex (bool), case (bool), levels (list), from, to (int|null), siteaccess, uri, ip, reference,
     *               invalid (field => reason)
     */
    public static function criteria( array $in, $maxPattern = 200 )
    {
        $str = function ( $key, $max ) use ( $in ) {
            if ( !isset( $in[$key] ) || !is_scalar( $in[$key] ) )
                return '';
            $v = trim( preg_replace( '/[\x00-\x08\x0B-\x1F\x7F]+/', '', (string)$in[$key] ) );
            return strlen( $v ) > $max ? substr( $v, 0, $max ) : $v;
        };
        $c = array( 'q' => $str( 'q', $maxPattern ), 'regex' => !empty( $in['regex'] ), 'case' => !empty( $in['case'] ),
                    'levels' => array(), 'from' => null, 'to' => null, 'siteaccess' => $str( 'siteaccess', 64 ),
                    'uri' => $str( 'uri', 300 ), 'ip' => $str( 'ip', 64 ), 'reference' => '', 'invalid' => array() );
        if ( isset( $in['q'] ) && is_scalar( $in['q'] ) && strlen( (string)$in['q'] ) > $maxPattern )
            $c['invalid']['q'] = 'too_long';
        $levels = isset( $in['levels'] ) ? $in['levels'] : array();
        if ( is_string( $levels ) )
            $levels = explode( ',', $levels );
        foreach ( (array)$levels as $l )
            if ( is_scalar( $l ) && in_array( strtolower( trim( (string)$l ) ), expLogParser::$levels, true ) )
                $c['levels'][] = strtolower( trim( (string)$l ) );
        $c['levels'] = array_values( array_unique( $c['levels'] ) );
        if ( count( $c['levels'] ) === count( expLogParser::$levels ) )
            $c['levels'] = array();
        foreach ( array( 'from', 'to' ) as $k )
        {
            $v = $str( $k, 40 );
            if ( $v === '' )
                continue;
            $t = ctype_digit( $v ) ? (int)$v : strtotime( $v );
            if ( $t === false || $t <= 0 )
                $c['invalid'][$k] = 'not_a_time';
            else
                $c[$k] = $k === 'to' && preg_match( '/^\d{4}-\d{2}-\d{2}$/', $v ) ? $t + 86399 : $t;
        }
        if ( $c['ip'] !== '' && !preg_match( '/^[0-9A-Fa-f.:]+$/', $c['ip'] ) )
        {
            $c['invalid']['ip'] = 'not_an_address';
            $c['ip'] = '';
        }
        $ref = strtoupper( $str( 'reference', 20 ) );
        if ( $ref !== '' )
        {
            if ( preg_match( '/^(?:ERR-)?([0-9A-F]{10})$/', $ref, $m ) )
                $c['reference'] = 'ERR-' . $m[1];
            else
                $c['invalid']['reference'] = 'not_a_reference';
        }
        if ( $c['regex'] && $c['q'] !== '' )
        {
            $compiled = self::compile( $c['q'], true, $c['case'] );
            if ( $compiled['error'] !== '' )
                $c['invalid']['q'] = $compiled['error'];
        }
        return $c;
    }

    /**
     * A search text as a pattern: a regular expression between ~ (a ~ in it is escaped), with i unless case
     * sensitive, compiled once to see that it is valid; or a literal (pattern ''). Pure.
     *
     * @param string $q
     * @param bool $regex
     * @param bool $case
     * @return array pattern, literal, error ('' or invalid_regex)
     */
    public static function compile( $q, $regex, $case )
    {
        if ( !$regex )
            return array( 'pattern' => '', 'literal' => (string)$q, 'error' => '' );
        $pattern = '~' . preg_replace( '/(?<!\\\\)~/', '\\~', (string)$q ) . '~' . ( $case ? '' : 'i' );
        set_error_handler( function () { return true; } );
        $ok = preg_match( $pattern, '' );
        restore_error_handler();
        if ( $ok === false )
            return array( 'pattern' => '', 'literal' => '', 'error' => 'invalid_regex' );
        return array( 'pattern' => $pattern, 'literal' => '', 'error' => '' );
    }

    /**
     * Whether an entry's text matches the search text. A regular expression that exceeds the backtracking limit
     * throws expLogSearchLimitException.
     *
     * @param array $compiled from compile()
     * @param bool $case
     * @param string $text
     * @return bool
     */
    public static function textMatches( array $compiled, $case, $text )
    {
        if ( $compiled['pattern'] !== '' )
        {
            $r = preg_match( $compiled['pattern'], $text );
            if ( $r === false )
                throw new expLogSearchLimitException( preg_last_error() );
            return $r === 1;
        }
        if ( $compiled['literal'] === '' )
            return true;
        return $case ? strpos( $text, $compiled['literal'] ) !== false : stripos( $text, $compiled['literal'] ) !== false;
    }

    /**
     * Whether a parsed entry meets the criteria other than the text.
     *
     * @param array $entry from expLogParser
     * @param array $c criteria
     * @return int 1 yes, 0 no, 2 no and every later entry is past the time range
     */
    public static function entryMatches( array $entry, array $c )
    {
        if ( $c['to'] !== null && $entry['time'] !== null && $entry['time'] > $c['to'] )
            return $entry['time'] > $c['to'] + 300 ? 2 : 0; // a little disorder between writers is tolerated
        if ( $c['from'] !== null && ( $entry['time'] === null || $entry['time'] < $c['from'] ) )
            return 0;
        if ( $c['to'] !== null && $entry['time'] === null )
            return 0;
        if ( $c['levels'] && !in_array( $entry['level'], $c['levels'], true ) )
            return 0;
        if ( $c['siteaccess'] !== '' && strcasecmp( $entry['siteaccess'], $c['siteaccess'] ) !== 0 )
            return 0;
        if ( $c['uri'] !== '' && stripos( $entry['url'], $c['uri'] ) === false )
            return 0;
        if ( $c['ip'] !== '' && strncasecmp( $entry['ip'], $c['ip'], strlen( $c['ip'] ) ) !== 0 )
            return 0;
        if ( $c['reference'] !== '' && !in_array( $c['reference'], $entry['references'], true ) )
            return 0;
        return 1;
    }

    /**
     * Searches files.
     *
     * @param array $files resolved files (expLogFileLocator::resolve()), in the order to search
     * @param array $c criteria (criteria())
     * @param string $cursor "<file index>:<offset>", '' from the start
     * @param callable|null $redact a function( string ): string; with it, a text search must also match the text as
     *                              the viewer shows it (what is masked cannot be found by its hidden value)
     * @return array entries (each with file: the file's id), cursor (to continue from, '' when done), stopped
     *               ('' | results | bytes | time | regex_limit), scanned (bytes), ms, files (ids searched), error
     */
    public function run( array $files, array $c, $cursor = '', $redact = null )
    {
        $started = microtime( true );
        $budget = $this->timeBudget !== null ? (float)$this->timeBudget : $this->config->int( 'SearchSettings', 'TimeLimit' ) / 1000;
        $maxBytes = $this->config->int( 'SearchSettings', 'MaxBytesPerRequest' );
        $maxResults = $this->config->int( 'SearchSettings', 'MaxResults' );
        $maxGzip = $this->config->int( 'SearchSettings', 'MaxGzipBytes' );
        $result = array( 'entries' => array(), 'cursor' => '', 'stopped' => '', 'scanned' => 0, 'ms' => 0, 'files' => array(), 'error' => '' );
        if ( $c['invalid'] )
        {
            $result['error'] = 'invalid';
            return $result;
        }
        $compiled = self::compile( $c['q'], $c['regex'], $c['case'] );
        if ( $compiled['error'] !== '' )
        {
            $result['error'] = $compiled['error'];
            return $result;
        }
        $fileIndex = 0;
        $startOffset = 0;
        if ( preg_match( '/^(\d{1,4}):(\d{1,15})$/', (string)$cursor, $m ) )
        {
            $fileIndex = (int)$m[1];
            $startOffset = (int)$m[2];
        }
        $oldLimit = ini_get( 'pcre.backtrack_limit' );
        $oldJit = ini_get( 'pcre.jit' );
        if ( $compiled['pattern'] !== '' )
        {
            ini_set( 'pcre.backtrack_limit', (string)$this->config->int( 'SearchSettings', 'BacktrackLimit' ) );
            ini_set( 'pcre.jit', '0' ); // the JIT ignores the backtracking limit
        }
        try
        {
            for ( $i = $fileIndex, $n = count( $files ); $i < $n; $i++ )
            {
                $file = $files[$i];
                $result['files'][] = $file['id'];
                $offset = $i === $fileIndex ? $startOffset : 0;
                if ( $offset === 0 && $c['from'] !== null && !$file['gz'] )
                    $offset = self::seekTime( $file['path'], $c['from'], (int)$file['size'] );
                $stop = $this->scanFile( $file, $offset, $c, $compiled, $redact, $result, $started, $budget, $maxBytes, $maxResults, $maxGzip );
                if ( $stop !== null )
                {
                    $result['stopped'] = $stop[0];
                    $result['cursor'] = $i . ':' . $stop[1];
                    break;
                }
            }
        }
        catch ( expLogSearchLimitException $e )
        {
            $result['stopped'] = 'regex_limit';
            $result['error'] = 'regex_limit';
            $result['cursor'] = '';
        }
        if ( $compiled['pattern'] !== '' )
        {
            ini_set( 'pcre.backtrack_limit', (string)$oldLimit );
            ini_set( 'pcre.jit', (string)$oldJit );
        }
        $result['ms'] = (int)round( ( microtime( true ) - $started ) * 1000 );
        return $result;
    }

    /**
     * Searches one file from an offset; null when it was read to its end, else array( reason, offset to go on from ).
     */
    protected function scanFile( array $file, $offset, array $c, array $compiled, $redact, array &$result, $started, $budget, $maxBytes, $maxResults, $maxGzip )
    {
        $gz = !empty( $file['gz'] );
        $handle = $gz ? @gzopen( $file['path'], 'rb' ) : @fopen( $file['path'], 'rb' );
        if ( $handle === false )
            return null;
        $pos = 0;
        if ( $offset > 0 )
        {
            if ( $gz )
            {
                // a compressed file is unpacked from its start up to the offset
                while ( $pos < $offset && !gzeof( $handle ) )
                {
                    $skip = gzread( $handle, (int)min( 1048576, $offset - $pos ) );
                    if ( $skip === false || $skip === '' )
                        break;
                    $pos += strlen( $skip );
                }
            }
            else if ( fseek( $handle, $offset ) === 0 )
                $pos = $offset;
        }
        $lines = array();
        $entryStart = $pos;
        $lineCount = 0;
        $fileLevel = isset( $file['level'] ) ? $file['level'] : '';
        $stop = null;
        $needHeader = $c['levels'] || $c['from'] !== null || $c['to'] !== null || $c['siteaccess'] !== '' || $c['uri'] !== ''
                      || $c['ip'] !== '' || $c['reference'] !== '';
        $check = function ( $start, $end ) use ( &$lines, &$result, $file, $c, $compiled, $redact, $fileLevel, $needHeader )
        {
            if ( !$lines )
                return 1;
            $text = implode( '', $lines );
            if ( $c['reference'] !== '' && strpos( $text, $c['reference'] ) === false )
                return 1;
            if ( !expLogSearch::textMatches( $compiled, $c['case'], $text ) )
            {
                if ( !$needHeader || $c['to'] === null )
                    return 1;
            }
            else
            {
                $entries = expLogParser::parse( $text, $start, $fileLevel );
                $entry = $entries[0];
                $verdict = expLogSearch::entryMatches( $entry, $c );
                if ( $verdict === 1 && $redact !== null && ( $compiled['pattern'] !== '' || $compiled['literal'] !== '' ) )
                    $verdict = expLogSearch::textMatches( $compiled, $c['case'], call_user_func( $redact, $entry['text'] ) ) ? 1 : 0;
                if ( $verdict === 1 )
                {
                    $entry['file'] = $file['id'];
                    $result['entries'][] = $entry;
                }
                return $verdict;
            }
            // no text match, but a time range: see whether the file is past it
            if ( $c['to'] !== null )
            {
                $h = expLogParser::header( rtrim( $lines[0], "\r\n" ) );
                if ( $h !== null && $h['time'] !== null && $h['time'] > $c['to'] + 300 )
                    return 2;
            }
            return 1;
        };
        while ( true )
        {
            $line = $gz ? gzgets( $handle, 1048576 ) : fgets( $handle, 1048576 );
            if ( $line === false )
                break;
            $len = strlen( $line );
            $result['scanned'] += $len;
            if ( expLogParser::startsEntry( $line ) && $lines )
            {
                $v = $check( $entryStart, $pos );
                $lines = array();
                $entryStart = $pos;
                if ( $v === 2 )
                    break;
                if ( count( $result['entries'] ) >= $maxResults )
                {
                    $stop = array( 'results', $entryStart );
                    break;
                }
            }
            if ( count( $lines ) < 2000 )
                $lines[] = $line;
            $pos += $len;
            if ( ( ++$lineCount & 255 ) === 0 )
            {
                if ( microtime( true ) - $started > $budget )
                {
                    $stop = array( 'time', $entryStart );
                    break;
                }
                if ( $result['scanned'] > $maxBytes )
                {
                    $stop = array( 'bytes', $entryStart );
                    break;
                }
                if ( $gz && $pos > $maxGzip )
                    break;
            }
        }
        if ( $stop === null && $lines )
        {
            $check( $entryStart, $pos );
            if ( count( $result['entries'] ) > $maxResults )
                array_splice( $result['entries'], $maxResults );
        }
        $gz ? gzclose( $handle ) : fclose( $handle );
        return $stop;
    }

    /**
     * The offset of the first line of a plain file with a time at or after $time (a binary search on the times of
     * the lines, about 30 reads of 8 KB for a file of 1 GB). The files are written in time order; when they are not,
     * the offset is a little early, never late.
     *
     * @param string $path
     * @param int $time
     * @param int $size
     * @return int
     */
    public static function seekTime( $path, $time, $size )
    {
        if ( $size < 65536 )
            return 0;
        $handle = @fopen( $path, 'rb' );
        if ( $handle === false )
            return 0;
        $lo = 0;
        $hi = $size;
        $steps = 0;
        while ( $hi - $lo > 65536 && $steps++ < 64 )
        {
            $mid = (int)( ( $lo + $hi ) / 2 );
            $found = self::firstTimeAfter( $handle, $mid );
            if ( $found === null || $found[0] >= $time )
                $hi = $mid;
            else
                $lo = $found[1];
        }
        fclose( $handle );
        if ( $lo === 0 )
            return 0;
        // the start of the first whole line at or after $lo
        $handle = @fopen( $path, 'rb' );
        if ( $handle === false )
            return 0;
        fseek( $handle, $lo );
        $block = (string)fread( $handle, 65536 );
        fclose( $handle );
        $nl = strpos( $block, "\n" );
        return $nl === false ? 0 : $lo + $nl + 1;
    }

    /**
     * The time of the first entry starting after $offset, and that entry's offset.
     *
     * @param resource $handle
     * @param int $offset
     * @return array|null time, offset
     */
    protected static function firstTimeAfter( $handle, $offset )
    {
        fseek( $handle, $offset );
        $block = (string)fread( $handle, 16384 );
        $pos = strpos( $block, "\n" );
        while ( $pos !== false && $pos < strlen( $block ) - 1 )
        {
            $next = strpos( $block, "\n", $pos + 1 );
            if ( $next === false )
                break;
            $line = substr( $block, $pos + 1, $next - $pos - 1 );
            if ( expLogParser::startsEntry( $line ) )
            {
                $h = expLogParser::header( $line );
                if ( $h !== null && $h['time'] !== null )
                    return array( $h['time'], $offset + $pos + 1 );
            }
            $pos = $next;
        }
        return null;
    }
}

/**
 * A regular expression that exceeded pcre.backtrack_limit (or another PCRE limit) on a line.
 */
class expLogSearchLimitException extends RuntimeException
{
}
