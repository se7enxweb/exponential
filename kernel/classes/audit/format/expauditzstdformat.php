<?php
/**
 * The zstd archive format: <file>.jsonl.zst, through the PHP extension zstd when it is loaded, else the zstd
 * binary. Registered as [AuditArchiveSettings] FormatHandlers[zstd]=expAuditZstdFormat. Levels 1-19.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditZstdFormat extends expAuditFormatBase
{
    public function name()
    {
        return 'zstd';
    }

    public function problem()
    {
        if ( self::hasExtension() )
            return '';
        if ( !function_exists( 'proc_open' ) )
            return 'neither the PHP extension zstd nor proc_open() for the zstd binary';
        return self::binary( 'zstd' ) ? '' : 'neither the PHP extension zstd nor the zstd binary is present';
    }

    /** @return bool The PHP extension zstd with its stream wrapper */
    protected static function hasExtension()
    {
        return function_exists( 'zstd_compress' ) && in_array( 'compress.zstd', stream_get_wrappers(), true );
    }

    public function extension()
    {
        return '.zst';
    }

    public function compress( $source, $target, $level )
    {
        $level = max( 1, min( 19, (int)$level ?: 19 ) );
        if ( self::hasExtension() )
        {
            $context = stream_context_create( array( 'zstd' => array( 'level' => $level ) ) );
            return self::compressThroughWrapper( 'compress.zstd://', $source, $target, $context );
        }
        return self::runFilter( array( self::binary( 'zstd' ), '-' . $level, '-c', '-q' ), $source, $target );
    }

    /**
     * The decompressed archive as a stream. With the PHP extension the archive is not read through its stream
     * wrapper: with zstd 0.18.0 a compress.zstd:// stream never reports its end (feof() stays false, fread() keeps
     * returning ''), so a reader looped forever (Redmine #16709). The archive is decompressed in chunks
     * (zstd_uncompress_init()/zstd_uncompress_add(), else zstd_uncompress() of the whole file) into php://temp,
     * which spills to a temporary file past 2 MB, and that stream is returned rewound.
     *
     * @param string $archive
     * @return resource|false
     */
    public function open( $archive )
    {
        if ( self::hasExtension() )
            return self::decompressToTemp( $archive );
        return self::openFilter( array( self::binary( 'zstd' ), '-d', '-c', '-q' ), $archive );
    }

    /**
     * Decompresses a .zst file with the PHP extension into a rewound php://temp stream, or false.
     *
     * @param string $archive
     * @return resource|false
     */
    protected static function decompressToTemp( $archive )
    {
        if ( !is_file( $archive ) )
            return false;
        $out = @fopen( 'php://temp/maxmemory:2097152', 'w+b' );
        if ( !$out )
            return false;
        $ok = false;
        if ( function_exists( 'zstd_uncompress_init' ) && function_exists( 'zstd_uncompress_add' ) )
        {
            $in = @fopen( $archive, 'rb' );
            $context = $in ? zstd_uncompress_init() : false;
            if ( $in && $context )
            {
                $ok = true;
                while ( ( $chunk = fread( $in, 1048576 ) ) !== false && $chunk !== '' )
                {
                    $plain = zstd_uncompress_add( $context, $chunk );
                    if ( $plain === false || ( $plain !== '' && fwrite( $out, $plain ) !== strlen( $plain ) ) )
                    {
                        $ok = false;
                        break;
                    }
                }
            }
            if ( $in )
                fclose( $in );
        }
        else
        {
            $plain = zstd_uncompress( (string)@file_get_contents( $archive ) );
            $ok = is_string( $plain ) && ( $plain === '' || fwrite( $out, $plain ) === strlen( $plain ) );
        }
        if ( !$ok )
        {
            fclose( $out );
            return false;
        }
        rewind( $out );
        return $out;
    }
}
