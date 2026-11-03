<?php
/**
 * File containing the expViewCountLogReader class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Reads the lines of an access log a site has not counted yet, from the byte where its last run stopped.
 *
 * Where a site stopped is an entry of expViewCountState: the inode and device of the log, the byte offset after the
 * last complete line read, and the head (a hash of the log's first line). plan() compares it with the log as it is:
 *
 * - resumed:   the same file (inode and head), at least as long as the offset: read on from the offset.
 * - rotated:   the log was moved away and a new one started (logrotate's create), or copied and emptied
 *              (copytruncate): the rest of the old file is read first when one of RotatedSuffixes[] (access_log.1,
 *              access_log.1.gz, access_log.0) is that file (same inode, or same first line), then the
 *              new log from its first line.
 * - truncated: the log is shorter than the offset and no rotated copy was found: the new log from its first line;
 *              what was written between the last run and the truncation is lost, and the report says so.
 * - new:       no entry yet: the log from its first line.
 *
 * Only complete lines are read: a line the web server is still writing is left for the next run. A line longer
 * than MaxLineLength is skipped whole and counted as too long.
 */
class expViewCountLogReader
{
    /** @var string */
    private $file;

    /** @var string[] */
    private $rotatedSuffixes;

    /** @var int */
    private $maxLineLength;

    /**
     * @param string $file the access log
     * @param string[] $rotatedSuffixes names of the rotated copy, appended to the log's name (.1, .1.gz, .0)
     * @param int $maxLineLength the longest line read, in bytes
     */
    public function __construct( $file, array $rotatedSuffixes = array( '.1', '.1.gz', '.0' ), $maxLineLength = 65536 )
    {
        $this->file = (string)$file;
        $this->rotatedSuffixes = array_values( array_filter( array_map( 'trim', array_map( 'strval', $rotatedSuffixes ) ), 'strlen' ) );
        $this->maxLineLength = max( 1024, (int)$maxLineLength );
    }

    /** @return string */
    public function file()
    {
        return $this->file;
    }

    /**
     * The log as it is now: inode, device, size and head; null when it cannot be read.
     *
     * @return array|null
     */
    public function stat()
    {
        return self::statFile( $this->file );
    }

    /**
     * @param string $file
     * @return array|null hash inode, device, size, head
     */
    public static function statFile( $file )
    {
        clearstatcache( true, $file );
        if ( !is_file( $file ) || !is_readable( $file ) )
            return null;
        $stat = @stat( $file );
        if ( !$stat )
            return null;
        $gz = substr( $file, -3 ) === '.gz';
        return array( 'inode' => (int)$stat['ino'], 'device' => (int)$stat['dev'], 'size' => $gz ? null : (int)$stat['size'],
                      'head' => self::head( $file ) );
    }

    /**
     * A hash of the first complete line of a file, '' when it has none yet. It tells a log apart from another one
     * that was written to the same inode (copytruncate) or got the same name.
     *
     * @param string $file
     * @return string
     */
    public static function head( $file )
    {
        $handle = @fopen( substr( $file, -3 ) === '.gz' ? 'compress.zlib://' . $file : $file, 'rb' );
        if ( !$handle )
            return '';
        $line = fgets( $handle, 8192 );
        fclose( $handle );
        if ( $line === false || substr( $line, -1 ) !== "\n" )
            return '';
        return sha1( $line );
    }

    /**
     * What to read for a site: the segments (file, start, final) and what happened to the log since its entry.
     *
     * @param array|null $entry the site's state entry, null for none
     * @param array $current stat() of the log
     * @return array hash event (new, resumed, rotated, truncated), segments (list of hash file, start, final),
     *               rotated (the rotated file read, or '')
     */
    public function plan( $entry, array $current )
    {
        if ( !is_array( $entry ) || !isset( $entry['offset'] ) )
            return array( 'event' => 'new', 'rotated' => '', 'segments' => array( array( 'file' => $this->file, 'start' => 0, 'final' => false ) ) );
        $offset = max( 0, (int)$entry['offset'] );
        $head = isset( $entry['head'] ) ? (string)$entry['head'] : '';
        $sameFile = isset( $entry['inode'] ) && (int)$entry['inode'] === $current['inode']
                    && ( !isset( $entry['device'] ) || (int)$entry['device'] === $current['device'] )
                    && ( $head === '' || $head === $current['head'] );
        // a compressed log (an older rotation given with --log-file) has no size to compare; it is not written to
        if ( $sameFile && ( $current['size'] === null || $current['size'] >= $offset ) )
            return array( 'event' => 'resumed', 'rotated' => '', 'segments' => array( array( 'file' => $this->file, 'start' => $offset, 'final' => false ) ) );

        $rotated = $this->findRotated( $entry, $offset );
        $segments = array();
        if ( $rotated !== '' )
            $segments[] = array( 'file' => $rotated, 'start' => $offset, 'final' => true );
        $segments[] = array( 'file' => $this->file, 'start' => 0, 'final' => false );
        return array( 'event' => $rotated !== '' ? 'rotated' : 'truncated', 'rotated' => $rotated, 'segments' => $segments );
    }

    /**
     * The rotated copy of the log that was the file of the entry: same inode and device (moved), or the same first
     * line (copied, compressed), and at least as long as the offset.
     *
     * @param array $entry
     * @param int $offset
     * @return string the file, '' for none
     */
    private function findRotated( array $entry, $offset )
    {
        $head = isset( $entry['head'] ) ? (string)$entry['head'] : '';
        foreach ( $this->rotatedSuffixes as $suffix )
        {
            $candidate = $this->file . $suffix;
            $stat = self::statFile( $candidate );
            if ( $stat === null )
                continue;
            $sameInode = isset( $entry['inode'] ) && (int)$entry['inode'] === $stat['inode']
                         && ( !isset( $entry['device'] ) || (int)$entry['device'] === $stat['device'] );
            $sameHead = $head !== '' && $head === $stat['head'];
            if ( !$sameHead && !( $sameInode && $head === '' ) )
                continue;
            if ( $stat['size'] !== null && $stat['size'] < $offset )
                continue;
            return $candidate;
        }
        return '';
    }

    /**
     * Reads the plans of several sites in one pass over each file: a line is handed over once, with the sites it is
     * new to.
     *
     * @param array $plans site name => plan()
     * @param callable $onLine function( string $line, string[] $sites, string $file, int $offset )
     * @return array hash end (the offset after the last complete line of the log), bytes (read), lines,
     *               too_long, files (file => bytes read)
     */
    public function read( array $plans, $onLine )
    {
        $files = array();
        foreach ( $plans as $site => $plan )
        {
            foreach ( $plan['segments'] as $segment )
            {
                $key = $segment['file'];
                if ( !isset( $files[$key] ) )
                    $files[$key] = array( 'final' => $segment['final'], 'sites' => array() );
                $files[$key]['sites'][$site] = (int)$segment['start'];
            }
        }
        // rotated copies first: their lines are older than the log's
        uasort( $files, function ( $a, $b ) { return (int)$b['final'] - (int)$a['final']; } );

        $result = array( 'end' => 0, 'bytes' => 0, 'lines' => 0, 'too_long' => 0, 'files' => array() );
        foreach ( $files as $file => $info )
        {
            $read = $this->readFile( $file, $info['sites'], $info['final'], $onLine, $result );
            $result['files'][$file] = $read['bytes'];
            if ( $file === $this->file )
                $result['end'] = $read['end'];
        }
        return $result;
    }

    /**
     * @return array hash end, bytes
     */
    private function readFile( $file, array $starts, $final, $onLine, array &$result )
    {
        $gz = substr( $file, -3 ) === '.gz';
        $handle = @fopen( $gz ? 'compress.zlib://' . $file : $file, 'rb' );
        $start = min( $starts );
        if ( !$handle )
            return array( 'end' => $start, 'bytes' => 0 );
        $offset = 0;
        if ( $start > 0 )
        {
            if ( !$gz && fseek( $handle, $start ) === 0 )
                $offset = $start;
            else
            {
                // a compressed file is read up to the offset
                while ( $offset < $start && !feof( $handle ) )
                {
                    $chunk = fread( $handle, (int)min( 1048576, $start - $offset ) );
                    if ( $chunk === false || $chunk === '' )
                        break;
                    $offset += strlen( $chunk );
                }
            }
        }
        $end = $offset;
        $bytes = 0;
        while ( true )
        {
            $line = fgets( $handle, $this->maxLineLength + 1 );
            if ( $line === false )
                break;
            $length = strlen( $line );
            if ( substr( $line, -1 ) !== "\n" )
            {
                if ( feof( $handle ) )
                {
                    // the line the server is still writing: left for the next run, unless the file will not grow
                    if ( !$final )
                        break;
                }
                else
                {
                    // longer than MaxLineLength: skipped up to its end
                    while ( ( $rest = fgets( $handle, 65536 ) ) !== false )
                    {
                        $length += strlen( $rest );
                        if ( substr( $rest, -1 ) === "\n" )
                            break;
                    }
                    $lineOffset = $offset;
                    $offset += $length;
                    $bytes += $length;
                    $end = $offset;
                    if ( $this->sitesAt( $starts, $lineOffset ) )
                        ++$result['too_long'];
                    continue;
                }
            }
            $lineOffset = $offset;
            $offset += $length;
            $bytes += $length;
            $end = $offset;
            $sites = $this->sitesAt( $starts, $lineOffset );
            if ( $sites )
            {
                ++$result['lines'];
                call_user_func( $onLine, $line, $sites, $file, $lineOffset );
            }
        }
        fclose( $handle );
        $result['bytes'] += $bytes;
        return array( 'end' => $end, 'bytes' => $bytes );
    }

    /** @return string[] the sites for which a line at this offset is new */
    private function sitesAt( array $starts, $offset )
    {
        $sites = array();
        foreach ( $starts as $site => $start )
        {
            if ( $offset >= $start )
                $sites[] = (string)$site;
        }
        return $sites;
    }
}
