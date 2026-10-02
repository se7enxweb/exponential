<?php
/**
 * Appends audit records to a channel's live file and keeps the hash chain (doc/bc/6.0/audit.md, "The hash chain").
 *
 * Files: <LogDir>/<channel>-<YYYY-MM-DD>.jsonl (the UTC date), and when a day's file passes MaxFileSize the next
 * part <channel>-<YYYY-MM-DD>.2.jsonl, .3.jsonl ... One chain per channel runs through all of its files.
 *
 * An append takes the channel's lock (<LogDir>/.<channel>.lock, flock LOCK_EX), so PHP-FPM workers, Velocity
 * workers and commands never interleave; under the lock it reads the head of the chain from the end of the file
 * (one seek, at most MaxLineBytes read; never from memory), and then:
 *  - a file closed by rotation (its last record is system.audit.file.close) is followed by the next part;
 *  - a file past MaxFileSize gets system.audit.file.close and the next part is started;
 *  - a new file starts with system.audit.file.open: seq 1, prev = the last hash of the channel's previous file
 *    (or the genesis value for the very first file of a channel), after = previous_file, previous_seq,
 *    previous_hash;
 *  - a torn last line (the file does not end in a newline, or its last line is not a record) gets a newline and a
 *    system.audit.chain.repair record whose prev is the last valid hash and whose after gives the torn bytes'
 *    offset and length; the torn bytes stay in the file;
 * then computes seq, prev and hash for every record in order and writes them with one fwrite().
 *
 * New directories and files get the owner and group of their parent directory when written by root (Velocity,
 * commands run as root), like expIniEditor::save() and expContentJobStore::fixOwner(); files are mode 0640.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditWriter
{
    const FILE_OPEN = 'system.audit.file.open';
    const FILE_CLOSE = 'system.audit.file.close';
    const CHAIN_REPAIR = 'system.audit.chain.repair';

    /** @var array expAuditConfig::get() */
    protected $config;

    /** @var expAuditKeys */
    protected $keys;

    /** @var callable fn( $name, array $data, $channel ): array, builds the writer's own records */
    protected $factory;

    /** @var callable|null fn(): string, the UTC date of the file to write (tests) */
    protected $clock = null;

    /**
     * @param array $config
     * @param expAuditKeys $keys
     * @param callable $factory builds a record for the writer's own events (file open/close, chain repair)
     */
    public function __construct( array $config, expAuditKeys $keys, $factory )
    {
        $this->config = $config;
        $this->keys = $keys;
        $this->factory = $factory;
    }

    /** Sets the date source (tests): a callable returning 'YYYY-MM-DD'. */
    public function setClock( $clock )
    {
        $this->clock = $clock;
    }

    /** @return string The live directory */
    public function dir()
    {
        return $this->config['logDir'];
    }

    /** @return string The genesis value of a channel: sha256 of "exponential-audit:<installation id>:<channel>" */
    public function genesis( $channel )
    {
        return self::genesisFor( $this->keys->installationId(), $channel, $this->config['algorithm'] );
    }

    /**
     * @param string $installationId
     * @param string $channel
     * @param string $algorithm
     * @return string
     */
    public static function genesisFor( $installationId, $channel, $algorithm = 'sha256' )
    {
        return $algorithm . ':' . hash( $algorithm, 'exponential-audit:' . $installationId . ':' . $channel );
    }

    /**
     * Appends records to a channel.
     *
     * @param string $channel
     * @param array[] $records records without seq, prev and hash
     * @return array[] the records as written (with seq, prev, hash and file)
     * @throws RuntimeException when the directory or the file cannot be written
     */
    public function append( $channel, array $records )
    {
        if ( !$records )
            return array();
        if ( !preg_match( '/^[a-z][a-z0-9_]{0,31}$/', $channel ) )
            throw new RuntimeException( "Malformed audit channel name '$channel'" );
        $dir = $this->dir();
        if ( !self::ensureDirectory( $dir ) )
            throw new RuntimeException( "The audit directory $dir cannot be created" );

        $lockFile = $dir . '/.' . $channel . '.lock';
        $lockCreated = !is_file( $lockFile );
        $lock = @fopen( $lockFile, 'c' );
        if ( !$lock )
            throw new RuntimeException( "The audit lock $lockFile cannot be opened" );
        if ( $lockCreated )
            self::ownLikeParent( $lockFile, 0640 );
        try
        {
            if ( !flock( $lock, LOCK_EX ) )
                throw new RuntimeException( "The audit lock $lockFile cannot be taken" );
            return $this->appendLocked( $channel, $records );
        }
        finally
        {
            flock( $lock, LOCK_UN );
            fclose( $lock );
        }
    }

    /**
     * The append, under the channel's lock.
     */
    protected function appendLocked( $channel, array $records )
    {
        $dir = $this->dir();
        $date = $this->clock ? call_user_func( $this->clock ) : gmdate( 'Y-m-d' );
        $part = 1;
        while ( is_file( $dir . '/' . self::fileName( $channel, $date, $part + 1 ) ) )
            $part++;
        $path = $dir . '/' . self::fileName( $channel, $date, $part );
        clearstatcache( true, $path );
        $size = is_file( $path ) ? (int)filesize( $path ) : 0;
        $head = $size > 0 ? $this->tail( $path, $size ) : null;

        $lines = '';
        $written = array();
        $closeOut = '';
        $previous = null;

        if ( $head !== null && $head['name'] === self::FILE_CLOSE )
        {
            // closed by rotation: the next part follows it
            $previous = array( 'file' => basename( $path ), 'seq' => $head['seq'], 'hash' => $head['hash'] );
            $part++;
            $path = $dir . '/' . self::fileName( $channel, $date, $part );
            $head = null;
            $size = 0;
        }
        elseif ( $head !== null && $size >= $this->maxFileSize( $channel ) )
        {
            // past MaxFileSize: close this part, start the next
            $repair = $this->repairLine( $channel, $head, $size );
            $close = $this->build( self::FILE_CLOSE, array(
                'object' => array( 'type' => 'file', 'id' => basename( $path ) ),
                'after' => array( 'records' => $head['seq'] + ( $repair ? 1 : 0 ) + 1 ) ), $channel );
            list( $closeLine, $closeRecord ) = $this->chain( $close, $repair ? $repair['record']['seq'] : $head['seq'], $repair ? $repair['record']['hash'] : $head['hash'] );
            $closeOut = ( $repair ? $repair['prefix'] . $repair['line'] : '' ) . $closeLine;
            $this->write( $path, $closeOut, false );
            $previous = array( 'file' => basename( $path ), 'seq' => $closeRecord['seq'], 'hash' => $closeRecord['hash'] );
            $part++;
            $path = $dir . '/' . self::fileName( $channel, $date, $part );
            $head = null;
            $size = 0;
        }

        $seq = 0;
        $prev = null;
        if ( $head === null )
        {
            if ( $previous === null )
                $previous = $this->previousFile( $channel, basename( $path ) );
            $data = array( 'object' => array( 'type' => 'file', 'id' => basename( $path ) ) );
            if ( $previous !== null )
                $data['after'] = array( 'previous_file' => $previous['file'], 'previous_seq' => $previous['seq'], 'previous_hash' => $previous['hash'] );
            $open = $this->build( self::FILE_OPEN, $data, $channel );
            list( $line, $rec ) = $this->chain( $open, 0, $previous === null ? $this->genesis( $channel ) : $previous['hash'] );
            $lines .= $line;
            $seq = $rec['seq'];
            $prev = $rec['hash'];
        }
        else
        {
            $repair = $this->repairLine( $channel, $head, $size );
            if ( $repair )
            {
                $lines .= $repair['prefix'] . $repair['line'];
                $seq = $repair['record']['seq'];
                $prev = $repair['record']['hash'];
            }
            else
            {
                $seq = $head['seq'];
                $prev = $head['hash'];
            }
        }

        foreach ( $records as $record )
        {
            list( $line, $rec ) = $this->chain( $record, $seq, $prev );
            $lines .= $line;
            $seq = $rec['seq'];
            $prev = $rec['hash'];
            $rec['file'] = basename( $path );
            $written[] = $rec;
        }
        $this->write( $path, $lines, $size === 0 );
        return $written;
    }

    /**
     * Gives a record its seq, prev and hash.
     *
     * @param array $record
     * @param int $lastSeq
     * @param string $lastHash
     * @return array( line with "\n", record with seq, prev, hash )
     */
    public function chain( array $record, $lastSeq, $lastHash )
    {
        unset( $record['hash'], $record['file'] );
        $record['seq'] = (int)$lastSeq + 1;
        $record['prev'] = (string)$lastHash;
        $record['hash'] = self::hashOf( $record, $this->config['algorithm'] );
        return array( expAuditJson::encode( $record ) . "\n", $record );
    }

    /**
     * The hash of a record: algorithm + ":" + hex of the hash of its canonical form without "hash".
     *
     * @param array|stdClass $record
     * @param string $algorithm
     * @return string
     */
    public static function hashOf( $record, $algorithm = 'sha256' )
    {
        if ( $record instanceof stdClass )
        {
            $record = clone $record;
            unset( $record->hash );
        }
        else
            unset( $record['hash'] );
        return $algorithm . ':' . hash( $algorithm, expAuditJson::encode( $record ) );
    }

    /**
     * The repair of a torn tail, when there is one: the newline to add and the repair record.
     *
     * @param string $channel
     * @param array $head tail()
     * @param int $size
     * @return array|null prefix ("\n" or ''), line, record
     */
    protected function repairLine( $channel, array $head, $size )
    {
        if ( $head['torn'] === null )
            return null;
        $repair = $this->build( self::CHAIN_REPAIR, array(
            'object' => array( 'type' => 'file', 'id' => basename( $head['path'] ) ),
            'after' => array( 'offset' => $head['torn']['offset'], 'length' => $head['torn']['length'],
                              'last_seq' => $head['seq'], 'last_hash' => $head['hash'] ) ), $channel );
        list( $line, $rec ) = $this->chain( $repair, $head['seq'], $head['hash'] );
        return array( 'prefix' => $head['endsWithNewline'] ? '' : "\n", 'line' => $line, 'record' => $rec );
    }

    /**
     * Writes bytes at the end of a file, in one fwrite().
     *
     * @param string $path
     * @param string $bytes
     * @param bool $mayCreate
     */
    protected function write( $path, $bytes, $mayCreate )
    {
        $created = !is_file( $path );
        $h = @fopen( $path, 'ab' );
        if ( !$h )
            throw new RuntimeException( "The audit file $path cannot be opened for writing" );
        $n = fwrite( $h, $bytes );
        fflush( $h );
        fclose( $h );
        if ( $created )
            self::ownLikeParent( $path, 0640 );
        if ( $n !== strlen( $bytes ) )
            throw new RuntimeException( "The audit file $path was not written completely ($n of " . strlen( $bytes ) . ' bytes)' );
    }

    /**
     * The head of the chain in a file: the last valid record's seq, hash and name, and the torn tail if any.
     *
     * @param string $path
     * @param int|null $size
     * @return array|null seq, hash, name, torn (offset, length)|null, endsWithNewline, path; null for an empty file
     */
    public function tail( $path, $size = null )
    {
        $size = $size === null ? (int)@filesize( $path ) : $size;
        if ( $size <= 0 )
            return null;
        $h = @fopen( $path, 'rb' );
        if ( !$h )
            throw new RuntimeException( "The audit file $path cannot be read" );
        $window = $this->config['maxLineBytes'];
        $start = max( 0, $size - $window );
        fseek( $h, $start );
        $buf = stream_get_contents( $h );
        fclose( $h );
        $endsWithNewline = substr( $buf, -1 ) === "\n";

        $torn = null;
        $body = $buf;
        if ( !$endsWithNewline )
        {
            $nl = strrpos( $buf, "\n" );
            $tornStart = $nl === false ? 0 : $nl + 1;
            $torn = array( 'offset' => $start + $tornStart, 'length' => strlen( $buf ) - $tornStart );
            $body = $nl === false ? '' : substr( $buf, 0, $nl + 1 );
        }
        // the last complete lines, newest first; the first one that is a record is the head
        $lines = $body === '' ? array() : explode( "\n", rtrim( $body, "\n" ) );
        $offsetEnd = $start + strlen( $body );
        for ( $i = count( $lines ) - 1; $i >= 0; $i-- )
        {
            $line = $lines[$i];
            $lineStart = $offsetEnd - strlen( $line ) - 1;
            $offsetEnd = $lineStart;
            if ( $i === 0 && $start > 0 )
                break; // a partial first line of the window: read the whole file below
            $rec = self::parseRecord( $line );
            if ( $rec !== null )
                return array( 'seq' => $rec['seq'], 'hash' => $rec['hash'], 'name' => $rec['name'], 'torn' => $torn,
                              'endsWithNewline' => $endsWithNewline, 'path' => $path );
            // a complete line that is not a record: torn (counted from its start to the end of the file's
            // valid part only when nothing else is torn, so the repair names the first bad bytes)
            $torn = array( 'offset' => $lineStart, 'length' => ( $torn ? $torn['offset'] + $torn['length'] : $size ) - $lineStart );
        }
        // nothing usable in the window: walk the whole file
        return $this->tailByScan( $path, $size, $endsWithNewline );
    }

    /**
     * The head by reading the whole file (a last line longer than MaxLineBytes, or nothing valid near the end).
     */
    protected function tailByScan( $path, $size, $endsWithNewline )
    {
        $h = @fopen( $path, 'rb' );
        $last = null;
        $lastEnd = 0;
        $offset = 0;
        while ( $h && ( $line = fgets( $h ) ) !== false )
        {
            $len = strlen( $line );
            if ( substr( $line, -1 ) === "\n" )
            {
                $rec = self::parseRecord( rtrim( $line, "\n" ) );
                if ( $rec !== null )
                {
                    $last = $rec;
                    $lastEnd = $offset + $len;
                }
            }
            $offset += $len;
        }
        if ( $h )
            fclose( $h );
        $torn = $lastEnd < $size ? array( 'offset' => $lastEnd, 'length' => $size - $lastEnd ) : null;
        if ( $last === null )
        {
            // no record at all: chain from the genesis of nothing; the whole file is torn
            return array( 'seq' => 0, 'hash' => $this->config['algorithm'] . ':' . str_repeat( '0', 64 ), 'name' => '',
                          'torn' => array( 'offset' => 0, 'length' => $size ), 'endsWithNewline' => $endsWithNewline, 'path' => $path );
        }
        return array( 'seq' => $last['seq'], 'hash' => $last['hash'], 'name' => $last['name'], 'torn' => $torn,
                      'endsWithNewline' => $endsWithNewline, 'path' => $path );
    }

    /**
     * A line as a record (seq, hash, name), or null when it is not one.
     *
     * @param string $line
     * @return array|null
     */
    public static function parseRecord( $line )
    {
        if ( $line === '' || $line[0] !== '{' )
            return null;
        $rec = json_decode( $line, true );
        if ( !is_array( $rec ) || !isset( $rec['seq'], $rec['hash'], $rec['name'] ) || !is_int( $rec['seq'] ) )
            return null;
        return $rec;
    }

    /**
     * The newest file of a channel before $current, with its head.
     *
     * @param string $channel
     * @param string $current file name
     * @return array|null file, seq, hash
     */
    protected function previousFile( $channel, $current )
    {
        $files = self::channelFiles( $this->dir(), $channel );
        $prev = null;
        foreach ( $files as $f )
        {
            if ( self::compareFiles( $f, $current ) >= 0 )
                break;
            $prev = $f;
        }
        if ( $prev === null )
            return null;
        $head = $this->tail( $this->dir() . '/' . $prev );
        if ( $head === null )
            return null;
        return array( 'file' => $prev, 'seq' => $head['seq'], 'hash' => $head['hash'] );
    }

    /**
     * A channel's live files, oldest first (by date, then part).
     *
     * @param string $dir
     * @param string $channel
     * @return string[] file names
     */
    public static function channelFiles( $dir, $channel )
    {
        $files = array();
        foreach ( (array)@scandir( $dir ) as $f )
        {
            if ( is_string( $f ) && self::parseFileName( $f ) !== null && self::parseFileName( $f )['channel'] === $channel )
                $files[] = $f;
        }
        usort( $files, array( __CLASS__, 'compareFiles' ) );
        return $files;
    }

    /**
     * @param string $name content-2026-10-02.jsonl, content-2026-10-02.2.jsonl
     * @return array|null channel, date, part
     */
    public static function parseFileName( $name )
    {
        if ( !preg_match( '/^([a-z][a-z0-9_]{0,31})-(\d{4}-\d{2}-\d{2})(?:\.(\d+))?\.jsonl$/', $name, $m ) )
            return null;
        return array( 'channel' => $m[1], 'date' => $m[2], 'part' => isset( $m[3] ) && $m[3] !== '' ? (int)$m[3] : 1 );
    }

    /** Orders file names of one channel by date, then part. */
    public static function compareFiles( $a, $b )
    {
        $pa = self::parseFileName( $a );
        $pb = self::parseFileName( $b );
        if ( !$pa || !$pb )
            return strcmp( $a, $b );
        return array( $pa['date'], $pa['part'] ) <=> array( $pb['date'], $pb['part'] );
    }

    /** @return string <channel>-<date>.jsonl, <channel>-<date>.<part>.jsonl from part 2 on */
    public static function fileName( $channel, $date, $part = 1 )
    {
        return $channel . '-' . $date . ( $part > 1 ? '.' . $part : '' ) . '.jsonl';
    }

    /** @return int MaxFileSize of a channel */
    protected function maxFileSize( $channel )
    {
        return isset( $this->config['maxFileSize'][$channel] ) ? $this->config['maxFileSize'][$channel] : 64 * 1048576;
    }

    /** A record of the writer's own, through the factory. */
    protected function build( $name, array $data, $channel )
    {
        return call_user_func( $this->factory, $name, $data, $channel );
    }

    /**
     * Creates a directory and its missing parents, each with the owner, group and mode of the nearest existing
     * parent when running as root.
     *
     * @param string $dir
     * @return bool
     */
    public static function ensureDirectory( $dir )
    {
        if ( is_dir( $dir ) )
            return true;
        $missing = array();
        $parent = $dir;
        while ( !is_dir( $parent ) )
        {
            $missing[] = $parent;
            $next = dirname( $parent );
            if ( $next === $parent )
                return false;
            $parent = $next;
        }
        $mode = ( @fileperms( $parent ) & 0777 ) ?: 0750;
        // the audit directory is never world readable
        $mode &= 0770;
        foreach ( array_reverse( $missing ) as $d )
        {
            if ( !@mkdir( $d, $mode ) && !is_dir( $d ) )
                return false;
            @chmod( $d, $mode | 0700 );
            self::ownLikeParent( $d, null );
        }
        return is_dir( $dir );
    }

    /**
     * Gives a path the owner and group of its directory when running as root, and a mode.
     *
     * @param string $path
     * @param int|null $mode
     */
    public static function ownLikeParent( $path, $mode = null )
    {
        if ( $mode !== null )
            @chmod( $path, $mode );
        $parent = dirname( $path );
        clearstatcache( true, $parent );
        $uid = @fileowner( $parent );
        $gid = @filegroup( $parent );
        if ( !function_exists( 'posix_geteuid' ) || posix_geteuid() !== 0 )
        {
            // not root: at least the directory's group, when this user belongs to it (PHP-FPM's primary group
            // differs from the site's), so files written by root and by the site user look the same
            if ( $gid !== false && @filegroup( $path ) !== $gid )
                @chgrp( $path, $gid );
            return;
        }
        if ( $uid !== false && @fileowner( $path ) !== $uid )
            @chown( $path, $uid );
        if ( $gid !== false && @filegroup( $path ) !== $gid )
            @chgrp( $path, $gid );
    }
}
