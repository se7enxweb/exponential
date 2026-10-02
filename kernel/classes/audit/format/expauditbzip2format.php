<?php
/**
 * The bzip2 archive format (PHP extension bz2): <file>.jsonl.bz2. Registered as
 * [AuditArchiveSettings] FormatHandlers[bzip2]=expAuditBzip2Format. Levels 1-9 (the block size).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditBzip2Format extends expAuditFormatBase
{
    public function name()
    {
        return 'bzip2';
    }

    public function problem()
    {
        return function_exists( 'bzopen' ) ? '' : 'the PHP extension bz2 is missing';
    }

    public function extension()
    {
        return '.bz2';
    }

    public function compress( $source, $target, $level )
    {
        $level = max( 1, min( 9, (int)$level ?: 9 ) );
        $context = stream_context_create( array( 'bzip2' => array( 'blocks' => $level ) ) );
        return self::compressThroughWrapper( 'compress.bzip2://', $source, $target, $context );
    }

    public function open( $archive )
    {
        return @fopen( 'compress.bzip2://' . $archive, 'rb' );
    }
}
