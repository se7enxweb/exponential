<?php
/**
 * The zstd archive format: <file>.jsonl.zst, through the PHP extension zstd when it is loaded, else the zstd
 * binary. Registered as [AuditArchiveSettings] FormatHandlers[zstd]=expAuditZstdFormat. Levels 1-19.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
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

    public function open( $archive )
    {
        if ( self::hasExtension() )
            return @fopen( 'compress.zstd://' . $archive, 'rb' );
        return self::openFilter( array( self::binary( 'zstd' ), '-d', '-c', '-q' ), $archive );
    }
}
