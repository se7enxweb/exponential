<?php
/**
 * Shared code of the archive format handlers (doc/bc/6.0/audit.md, "Rotation, archives and retention"):
 * streaming compression through a PHP stream wrapper, or through a binary (xz, zstd) run without a shell.
 *
 * A stream returned by open() is closed with expAuditFormatBase::close( $stream ), which also ends the
 * decompressing process of a binary handler.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

abstract class expAuditFormatBase implements expAuditFormatHandler
{
    /** @var array (int)stream id => process resource, for the binary handlers */
    protected static $processes = array();

    /** @var array binary name => path or false */
    protected static $binaries = array();

    /**
     * Copies a file into a writable stream in chunks.
     *
     * @param string $source
     * @param resource $out
     * @return bool
     */
    protected static function copyInto( $source, $out )
    {
        $in = @fopen( $source, 'rb' );
        if ( !$in )
            return false;
        $ok = true;
        while ( !feof( $in ) )
        {
            $chunk = fread( $in, 1048576 );
            if ( $chunk === false )
            {
                $ok = false;
                break;
            }
            if ( $chunk !== '' && fwrite( $out, $chunk ) !== strlen( $chunk ) )
            {
                $ok = false;
                break;
            }
        }
        fclose( $in );
        return $ok;
    }

    /**
     * Compresses through a stream wrapper (compress.zlib://, compress.bzip2://).
     *
     * @param string $wrapper
     * @param string $source
     * @param string $target
     * @param resource|null $context
     * @return bool
     */
    protected static function compressThroughWrapper( $wrapper, $source, $target, $context = null )
    {
        $out = $context ? @fopen( $wrapper . $target, 'wb', false, $context ) : @fopen( $wrapper . $target, 'wb' );
        if ( !$out )
            return false;
        $ok = self::copyInto( $source, $out );
        $ok = fclose( $out ) && $ok;
        return $ok && is_file( $target );
    }

    /**
     * The path of a binary, or false.
     *
     * @param string $name
     * @return string|false
     */
    public static function binary( $name )
    {
        if ( array_key_exists( $name, self::$binaries ) )
            return self::$binaries[$name];
        foreach ( array( '/usr/bin/', '/bin/', '/usr/local/bin/' ) as $dir )
        {
            if ( is_file( $dir . $name ) && is_executable( $dir . $name ) )
                return self::$binaries[$name] = $dir . $name;
        }
        return self::$binaries[$name] = false;
    }

    /**
     * Runs a binary with stdin from a file and stdout into a file (no shell).
     *
     * @param array $command
     * @param string $source
     * @param string $target
     * @return bool
     */
    protected static function runFilter( array $command, $source, $target )
    {
        if ( !function_exists( 'proc_open' ) )
            return false;
        $proc = @proc_open( $command, array( 0 => array( 'file', $source, 'r' ), 1 => array( 'file', $target, 'w' ),
                                             2 => array( 'pipe', 'w' ) ), $pipes );
        if ( !is_resource( $proc ) )
            return false;
        stream_get_contents( $pipes[2] );
        fclose( $pipes[2] );
        return proc_close( $proc ) === 0 && is_file( $target );
    }

    /**
     * A stream of a binary's stdout, its stdin read from a file.
     *
     * @param array $command
     * @param string $source
     * @return resource|false
     */
    protected static function openFilter( array $command, $source )
    {
        if ( !function_exists( 'proc_open' ) || !is_file( $source ) )
            return false;
        $proc = @proc_open( $command, array( 0 => array( 'file', $source, 'r' ), 1 => array( 'pipe', 'w' ),
                                             2 => array( 'file', '/dev/null', 'w' ) ), $pipes );
        if ( !is_resource( $proc ) )
            return false;
        self::$processes[(int)$pipes[1]] = $proc;
        return $pipes[1];
    }

    /**
     * Closes a stream returned by open().
     *
     * @param resource $stream
     * @return bool true when the stream (and its process) ended cleanly
     */
    public static function close( $stream )
    {
        if ( !is_resource( $stream ) )
            return false;
        $id = (int)$stream;
        fclose( $stream );
        if ( isset( self::$processes[$id] ) )
        {
            $code = proc_close( self::$processes[$id] );
            unset( self::$processes[$id] );
            return $code === 0;
        }
        return true;
    }

    /**
     * The sha256 and byte count of what a stream returns.
     *
     * @param resource $stream
     * @return array sha256, bytes
     */
    public static function digestStream( $stream )
    {
        $ctx = hash_init( 'sha256' );
        $bytes = 0;
        while ( !feof( $stream ) )
        {
            $chunk = fread( $stream, 1048576 );
            if ( $chunk === false || $chunk === '' )
            {
                if ( feof( $stream ) || $chunk === false )
                    break;
                continue;
            }
            hash_update( $ctx, $chunk );
            $bytes += strlen( $chunk );
        }
        return array( 'sha256' => hash_final( $ctx ), 'bytes' => $bytes );
    }
}
