<?php
/**
 * The gzip archive format (zlib, always available): <file>.jsonl.gz. Registered as
 * [AuditArchiveSettings] FormatHandlers[gzip]=expAuditGzipFormat. Levels 1-9.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditGzipFormat extends expAuditFormatBase
{
    public function name()
    {
        return 'gzip';
    }

    public function problem()
    {
        return function_exists( 'gzopen' ) ? '' : 'the PHP extension zlib is missing';
    }

    public function extension()
    {
        return '.gz';
    }

    public function compress( $source, $target, $level )
    {
        $level = max( 1, min( 9, (int)$level ?: 9 ) );
        $out = @gzopen( $target, 'wb' . $level );
        if ( !$out )
            return false;
        $in = @fopen( $source, 'rb' );
        if ( !$in )
        {
            gzclose( $out );
            return false;
        }
        $ok = true;
        while ( !feof( $in ) )
        {
            $chunk = fread( $in, 1048576 );
            if ( $chunk === false )
            {
                $ok = false;
                break;
            }
            if ( $chunk !== '' && gzwrite( $out, $chunk ) !== strlen( $chunk ) )
            {
                $ok = false;
                break;
            }
        }
        fclose( $in );
        return gzclose( $out ) && $ok;
    }

    public function open( $archive )
    {
        return @fopen( 'compress.zlib://' . $archive, 'rb' );
    }
}
