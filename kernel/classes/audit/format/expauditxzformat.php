<?php
/**
 * The xz archive format: <file>.jsonl.xz, through the xz binary (no PHP extension is commonly available).
 * Registered as [AuditArchiveSettings] FormatHandlers[xz]=expAuditXzFormat. Levels 0-9.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditXzFormat extends expAuditFormatBase
{
    public function name()
    {
        return 'xz';
    }

    public function problem()
    {
        if ( !function_exists( 'proc_open' ) )
            return 'proc_open() is disabled';
        return self::binary( 'xz' ) ? '' : 'the xz binary is missing';
    }

    public function extension()
    {
        return '.xz';
    }

    public function compress( $source, $target, $level )
    {
        $level = max( 0, min( 9, (int)$level ) );
        return self::runFilter( array( self::binary( 'xz' ), '-' . $level, '-c', '-q' ), $source, $target );
    }

    public function open( $archive )
    {
        return self::openFilter( array( self::binary( 'xz' ), '-d', '-c', '-q' ), $archive );
    }
}
