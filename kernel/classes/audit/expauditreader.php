<?php
/**
 * Reads audit records from the live channel files: the newest records (read backwards from the end of the
 * files, so a large day file is not read whole), one record by id, the files of a channel. Used by exp:audit and
 * the audit/recent view; the database index of stage 4 replaces it for search.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditReader
{
    /** @var string */
    protected $dir;

    /**
     * @param string $dir the live directory
     */
    public function __construct( $dir )
    {
        $this->dir = rtrim( $dir, '/' );
    }

    /** @return string */
    public function dir()
    {
        return $this->dir;
    }

    /**
     * The channels that have files, and per channel its files with their sizes.
     *
     * @return array channel => array( files => array( name => bytes ), bytes, newest )
     */
    public function channels()
    {
        $out = array();
        foreach ( (array)@scandir( $this->dir ) as $f )
        {
            $p = is_string( $f ) ? expAuditWriter::parseFileName( $f ) : null;
            if ( !$p )
                continue;
            $out[$p['channel']]['files'][$f] = (int)@filesize( $this->dir . '/' . $f );
        }
        foreach ( $out as $channel => $info )
        {
            uksort( $info['files'], array( 'expAuditWriter', 'compareFiles' ) );
            $out[$channel]['files'] = $info['files'];
            $out[$channel]['bytes'] = array_sum( $info['files'] );
            $names = array_keys( $info['files'] );
            $out[$channel]['newest'] = end( $names );
        }
        ksort( $out );
        return $out;
    }

    /**
     * The newest records, newest first.
     *
     * @param int $limit
     * @param string|null $channel null = every channel
     * @param string|null $namePattern a taxonomy pattern (access.*, content.node.move)
     * @return array[] records (as arrays), each with 'file' and 'line'... 'file' only
     */
    public function latest( $limit = 100, $channel = null, $namePattern = null )
    {
        $limit = max( 1, (int)$limit );
        $all = array();
        foreach ( $this->channels() as $c => $info )
        {
            if ( $channel !== null && $channel !== '' && $c !== $channel )
                continue;
            $files = array_reverse( array_keys( $info['files'] ) );
            $found = 0;
            foreach ( $files as $file )
            {
                foreach ( $this->linesBackwards( $this->dir . '/' . $file ) as $line )
                {
                    $rec = json_decode( $line, true );
                    if ( !is_array( $rec ) || !isset( $rec['name'] ) )
                        continue;
                    if ( $namePattern !== null && $namePattern !== '' && expAuditTaxonomy::match( $namePattern, $rec['name'] ) < 0 )
                        continue;
                    $rec['file'] = $file;
                    $all[] = $rec;
                    if ( ++$found >= $limit )
                        break 2;
                }
            }
        }
        usort( $all, function ( $a, $b ) {
            $t = strcmp( isset( $b['time'] ) ? $b['time'] : '', isset( $a['time'] ) ? $a['time'] : '' );
            return $t !== 0 ? $t : strcmp( isset( $b['id'] ) ? $b['id'] : '', isset( $a['id'] ) ? $a['id'] : '' );
        } );
        return array_slice( $all, 0, $limit );
    }

    /**
     * One record by id, with where it is.
     *
     * @param string $id
     * @return array|null the record with 'file' and 'line'
     */
    public function find( $id )
    {
        if ( !preg_match( '/^[0-9A-HJKMNP-TV-Z]{26}$/', (string)$id ) )
            return null;
        $needle = '"id":"' . $id . '"';
        $files = array();
        foreach ( $this->channels() as $info )
            $files = array_merge( $files, array_keys( $info['files'] ) );
        // the id starts with its time: the files of that day first
        usort( $files, function ( $a, $b ) { return strcmp( $b, $a ); } );
        foreach ( $files as $file )
        {
            $h = @fopen( $this->dir . '/' . $file, 'rb' );
            $n = 0;
            while ( $h && ( $line = fgets( $h ) ) !== false )
            {
                $n++;
                if ( strpos( $line, $needle ) === false )
                    continue;
                $rec = json_decode( rtrim( $line, "\n" ), true );
                if ( is_array( $rec ) && isset( $rec['id'] ) && $rec['id'] === $id )
                {
                    fclose( $h );
                    $rec['file'] = $file;
                    $rec['line'] = $n;
                    $rec['raw'] = rtrim( $line, "\n" );
                    return $rec;
                }
            }
            if ( $h )
                fclose( $h );
        }
        return null;
    }

    /**
     * Other records of the same request, and the children of a record.
     *
     * @param array $record
     * @param int $limit
     * @return array siblings (same request id), children (parent = id)
     */
    public function related( array $record, $limit = 50 )
    {
        $siblings = array();
        $children = array();
        $requestId = isset( $record['request']['id'] ) ? $record['request']['id'] : null;
        foreach ( $this->latest( 2000 ) as $r )
        {
            if ( $r['id'] === $record['id'] )
                continue;
            if ( $requestId !== null && isset( $r['request']['id'] ) && $r['request']['id'] === $requestId && count( $siblings ) < $limit )
                $siblings[] = $r;
            if ( isset( $r['parent'] ) && $r['parent'] === $record['id'] && count( $children ) < $limit )
                $children[] = $r;
        }
        return array( 'siblings' => $siblings, 'children' => $children );
    }

    /**
     * The actor of a record in a few characters.
     *
     * @param array $r
     * @return string "editor1(14)", "os:alpha", "-"
     */
    public static function actorText( array $r )
    {
        $a = isset( $r['actor'] ) ? $r['actor'] : array();
        if ( isset( $a['login'] ) || isset( $a['user_id'] ) )
            return ( isset( $a['login'] ) ? $a['login'] : '?' ) . ( isset( $a['user_id'] ) ? '(' . $a['user_id'] . ')' : '' );
        if ( isset( $a['cli']['os_user'] ) )
            return 'os:' . $a['cli']['os_user'];
        return '-';
    }

    /**
     * The object of a record in a few characters.
     *
     * @param array $r
     * @return string "node 275 Workout", "setting site.ini/DebugSettings/DebugOutput"
     */
    public static function objectText( array $r )
    {
        $o = isset( $r['object'] ) ? $r['object'] : null;
        if ( !$o )
            return '-';
        $text = ( isset( $o['type'] ) ? $o['type'] : '?' ) . ( isset( $o['id'] ) ? ' ' . ( is_scalar( $o['id'] ) ? $o['id'] : json_encode( $o['id'] ) ) : '' );
        if ( isset( $o['name'] ) )
            $text .= ' ' . $o['name'];
        elseif ( isset( $o['login'] ) )
            $text .= ' ' . $o['login'];
        return expAuditPrivacy::cut( $text, 60 );
    }

    /**
     * The complete lines of a file, last first, read in blocks from the end.
     *
     * @param string $path
     * @return Generator|string[]
     */
    public function linesBackwards( $path )
    {
        $h = @fopen( $path, 'rb' );
        if ( !$h )
            return;
        fseek( $h, 0, SEEK_END );
        $pos = ftell( $h );
        $rest = '';
        $first = true;
        while ( $pos > 0 )
        {
            $read = min( 65536, $pos );
            $pos -= $read;
            fseek( $h, $pos );
            // fread() returns at most one buffer (8 KB) through a userland stream wrapper, as Velocity's file wrapper
            // is: read until the whole chunk is there, or records in the rest of it are never seen
            $data = '';
            while ( strlen( $data ) < $read )
            {
                $part = fread( $h, $read - strlen( $data ) );
                if ( $part === false || $part === '' )
                    break;
                $data .= $part;
            }
            $chunk = $data . $rest;
            $lines = explode( "\n", $chunk );
            $rest = array_shift( $lines );
            if ( $first )
            {
                // a torn tail (no newline yet) is not a record
                array_pop( $lines );
                $first = false;
            }
            for ( $i = count( $lines ) - 1; $i >= 0; $i-- )
                if ( $lines[$i] !== '' )
                    yield $lines[$i];
        }
        if ( $rest !== '' && !$first )
            yield $rest;
        fclose( $h );
    }
}
