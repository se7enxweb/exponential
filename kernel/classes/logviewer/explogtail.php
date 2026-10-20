<?php
/**
 * Reads what a log file gained since a byte offset (doc/eng-us/features/6.0/log-viewer.md, "Following a file").
 *
 * The cost of a poll is kept to what changed:
 *  - one stat() first; a file that has not grown since the offset is answered without opening it;
 *  - only the bytes after the offset are read, at most MaxBytesPerPoll per poll (a file that grew more is caught
 *    up over the next polls), never the whole file;
 *  - the last chunk read of each file is kept in the cache directory for ChunkCacheTTL seconds with the file's
 *    inode, size and modification time, so a second viewer of the same file, or a poll that asks again for bytes
 *    already read, is answered from it; only the bytes after the cached ones are read from the file;
 *  - no file is ever locked (the writers append without locks too), and the cache is written to a temporary
 *    file and renamed, so a reader never sees half of it;
 *  - a chunk ends at the last whole line: a line still being written is left for the next poll.
 * A file that was rotated (another inode) or emptied (smaller than the offset) is read again from its start, and
 * the answer says so (reset).
 *
 * A compressed rotation (.gz) is never followed: lastBytesOfGzip() unpacks it once, as a stream, keeping only the
 * end of it, with a limit on what is unpacked.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expLogTail
{
    /** @var expLogViewerConfig */
    protected $config;

    /** @var string|null the cache directory, null: none */
    protected $cacheDir;

    /** @var array what the last read() did: stat, open, cache_hit, bytes_read (for the cost measurements) */
    public $lastStats = array();

    /**
     * @param expLogViewerConfig $config
     * @param string|null $cacheDir where the chunk cache is kept; null: <cache dir>/logviewer/tail, false: no cache
     */
    public function __construct( expLogViewerConfig $config, $cacheDir = null )
    {
        $this->config = $config;
        if ( $cacheDir === null && class_exists( 'eZSys' ) )
            $cacheDir = eZSys::cacheDirectory() . '/logviewer/tail';
        $this->cacheDir = $cacheDir ? rtrim( (string)$cacheDir, '/' ) : null;
    }

    /**
     * The bytes of a file after $offset.
     *
     * @param string $path a file the locator resolved
     * @param int $offset the byte offset the reader has; -1: the end of the file (the last InitialBytes, from
     *                    the first whole line)
     * @param int $inode the inode the reader's offset belongs to (0: unknown)
     * @param int|null $maxBytes null: MaxBytesPerPoll
     * @return array data (whole lines), offset (where data starts), next (the offset to ask for next), size,
     *               mtime, ino, reset (the file was rotated or emptied), more (there is more to read now),
     *               cached (answered from the cache, at least in part)
     */
    public function read( $path, $offset, $inode = 0, $maxBytes = null )
    {
        $this->lastStats = array( 'stat' => 1, 'open' => 0, 'cache_hit' => 0, 'bytes_read' => 0 );
        $maxBytes = $maxBytes === null ? $this->config->int( 'TailSettings', 'MaxBytesPerPoll' ) : max( 1024, (int)$maxBytes );
        clearstatcache( true, $path );
        $stat = @stat( $path );
        if ( $stat === false )
            return array( 'data' => '', 'offset' => 0, 'next' => 0, 'size' => 0, 'mtime' => 0, 'ino' => 0, 'reset' => true,
                          'more' => false, 'cached' => false, 'missing' => true );
        $size = (int)$stat['size'];
        $ino = (int)$stat['ino'];
        $mtime = (int)$stat['mtime'];
        $offset = (int)$offset;
        $reset = false;
        if ( $offset >= 0 && ( ( $inode > 0 && $inode !== $ino ) || $offset > $size ) )
        {
            $offset = 0;
            $reset = true;
        }
        $result = array( 'data' => '', 'offset' => $offset, 'next' => $offset, 'size' => $size, 'mtime' => $mtime, 'ino' => $ino,
                         'reset' => $reset, 'more' => false, 'cached' => false, 'missing' => false );

        if ( $offset < 0 )
        {
            // the first look at a file: its end
            $initial = $this->config->int( 'TailSettings', 'InitialBytes' );
            $start = max( 0, $size - $initial );
            $data = $this->readRange( $path, $start, $size - $start, $ino, $size, $mtime );
            if ( $start > 0 )
            {
                $nl = strpos( $data, "\n" );
                if ( $nl === false )
                    return array_merge( $result, array( 'offset' => $size, 'next' => $size ) );
                $data = substr( $data, $nl + 1 );
                $start += $nl + 1;
            }
            list( $data, $next ) = self::wholeLines( $data, $start, $size, true );
            return array_merge( $result, array( 'data' => $data, 'offset' => $start, 'next' => $next, 'more' => false,
                                                'cached' => $this->lastStats['cache_hit'] > 0 ) );
        }

        if ( $offset === $size )
            return $result; // nothing new: one stat(), no open

        $want = min( $maxBytes, $size - $offset );
        $data = $this->readRange( $path, $offset, $want, $ino, $size, $mtime );
        list( $data, $next ) = self::wholeLines( $data, $offset, $size, $want >= $maxBytes && $offset + $want < $size );
        $result['data'] = $data;
        $result['next'] = $next;
        $result['more'] = $offset + $want < $size;
        $result['cached'] = $this->lastStats['cache_hit'] > 0;
        return $result;
    }

    /**
     * A chunk cut to its last whole line. A chunk without a line end is kept whole when it filled the whole read
     * ($full: a line longer than a poll), and left for the next poll otherwise (a line still being written).
     *
     * @param string $data
     * @param int $offset where $data starts
     * @param int $size the file's size
     * @param bool $full the read was as long as allowed
     * @return array data, next offset
     */
    public static function wholeLines( $data, $offset, $size, $full )
    {
        $length = strlen( $data );
        if ( $length === 0 )
            return array( '', $offset );
        if ( $data[$length - 1] === "\n" )
            return array( $data, $offset + $length );
        $nl = strrpos( $data, "\n" );
        if ( $nl === false )
            return $full ? array( $data, $offset + $length ) : array( '', $offset );
        return array( substr( $data, 0, $nl + 1 ), $offset + $nl + 1 );
    }

    /**
     * Bytes of a file, from the cache where it has them and from the file for the rest. The bytes read from the file
     * become the cached chunk (the last chunk read, at most ChunkCacheMaxBytes), so the next reader at the same
     * offset is answered from it.
     *
     * @param string $path
     * @param int $start
     * @param int $length
     * @param int $ino
     * @param int $size
     * @param int $mtime
     * @return string
     */
    protected function readRange( $path, $start, $length, $ino, $size, $mtime )
    {
        if ( $length <= 0 )
            return '';
        $prefix = '';
        $from = $start;
        $cache = $this->cacheGet( $path, $ino, $size );
        if ( $cache !== null && $start >= $cache['start'] && $start < $cache['end'] )
        {
            $handle = @fopen( $cache['file'], 'rb' );
            if ( $handle !== false )
            {
                if ( fseek( $handle, $cache['data_offset'] + ( $start - $cache['start'] ) ) === 0 )
                    $prefix = (string)stream_get_contents( $handle, (int)min( $length, $cache['end'] - $start ) );
                fclose( $handle );
            }
            $from = $start + strlen( $prefix );
            if ( $prefix !== '' )
                $this->lastStats['cache_hit'] = 1;
        }
        $rest = '';
        $need = $start + $length - $from;
        if ( $need > 0 )
        {
            $handle = @fopen( $path, 'rb' );
            if ( $handle !== false )
            {
                $this->lastStats['open']++;
                if ( $from === 0 || fseek( $handle, $from ) === 0 )
                    $rest = (string)stream_get_contents( $handle, $need );
                fclose( $handle );
                $this->lastStats['bytes_read'] += strlen( $rest );
            }
        }
        if ( $rest !== '' )
            $this->cachePut( $path, $ino, $size, $mtime, $from, $rest );
        return $prefix . $rest;
    }

    /** @return string|null the cache file of a log file */
    protected function cacheFile( $path )
    {
        if ( $this->cacheDir === null || $this->config->int( 'TailSettings', 'ChunkCacheTTL' ) <= 0
             || $this->config->int( 'TailSettings', 'ChunkCacheMaxBytes' ) <= 0 )
            return null;
        return $this->cacheDir . '/' . sha1( $path ) . '.chunk';
    }

    /**
     * Where the cached chunk of a file is, when it belongs to the same inode, the file has not shrunk since and it
     * is fresh. Only the header line of the cache file is read here.
     *
     * @param string $path
     * @param int $ino
     * @param int $size
     * @return array|null start, end, file (the cache file), data_offset (where the bytes start in it)
     */
    protected function cacheGet( $path, $ino, $size )
    {
        $file = $this->cacheFile( $path );
        if ( $file === null )
            return null;
        $handle = @fopen( $file, 'rb' );
        if ( $handle === false )
            return null;
        $header = fgets( $handle, 1024 );
        fclose( $handle );
        $meta = $header !== false ? json_decode( $header, true ) : null;
        if ( !is_array( $meta ) || !isset( $meta['ino'], $meta['start'], $meta['length'], $meta['time'], $meta['path'], $meta['size'] )
             || (int)$meta['ino'] !== $ino || $size < (int)$meta['size'] || $meta['path'] !== $path
             || time() - (int)$meta['time'] > $this->config->int( 'TailSettings', 'ChunkCacheTTL' ) )
            return null;
        return array( 'start' => (int)$meta['start'], 'end' => (int)$meta['start'] + (int)$meta['length'], 'file' => $file,
                      'data_offset' => strlen( $header ) );
    }

    /**
     * Keeps the chunk just read (its last ChunkCacheMaxBytes) for the next reader.
     */
    protected function cachePut( $path, $ino, $size, $mtime, $start, $data )
    {
        $file = $this->cacheFile( $path );
        if ( $file === null )
            return;
        $max = $this->config->int( 'TailSettings', 'ChunkCacheMaxBytes' );
        if ( strlen( $data ) > $max )
        {
            $start += strlen( $data ) - $max;
            $data = substr( $data, -$max );
        }
        if ( !is_dir( $this->cacheDir ) && !@mkdir( $this->cacheDir, eZDir::dirMode( 0770 ), true ) && !is_dir( $this->cacheDir ) )
            return;
        $meta = json_encode( array( 'path' => $path, 'ino' => $ino, 'size' => $size, 'mtime' => $mtime, 'start' => $start,
                                    'length' => strlen( $data ), 'time' => time() ), JSON_UNESCAPED_SLASHES );
        $tmp = $file . '.' . getmypid() . '.' . mt_rand() . '.tmp';
        if ( @file_put_contents( $tmp, $meta . "\n" . $data ) === false )
            return;
        @chmod( $tmp, eZFile::fileMode( 0660 ) );
        if ( !@rename( $tmp, $file ) )
            @unlink( $tmp );
    }

    /**
     * The last bytes of a compressed file, unpacked as a stream (at most $limit bytes unpacked, the last $keep kept).
     *
     * @param string $path
     * @param int $keep
     * @param int $limit
     * @return array data (whole lines), offset (of data in the unpacked file), size (unpacked so far), complete
     */
    public static function lastBytesOfGzip( $path, $keep, $limit )
    {
        $handle = @gzopen( $path, 'rb' );
        if ( $handle === false )
            return array( 'data' => '', 'offset' => 0, 'size' => 0, 'complete' => false );
        $buffer = '';
        $total = 0;
        while ( !gzeof( $handle ) && $total < $limit )
        {
            $block = gzread( $handle, 262144 );
            if ( $block === false || $block === '' )
                break;
            $total += strlen( $block );
            $buffer .= $block;
            if ( strlen( $buffer ) > $keep * 2 )
                $buffer = substr( $buffer, -$keep );
        }
        $complete = gzeof( $handle );
        gzclose( $handle );
        if ( strlen( $buffer ) > $keep )
            $buffer = substr( $buffer, -$keep );
        $offset = $total - strlen( $buffer );
        if ( $offset > 0 )
        {
            $nl = strpos( $buffer, "\n" );
            $buffer = $nl === false ? '' : substr( $buffer, $nl + 1 );
            $offset = $total - strlen( $buffer );
        }
        return array( 'data' => $buffer, 'offset' => $offset, 'size' => $total, 'complete' => $complete );
    }
}
