<?php
/**
 * The zip archive format (PHP extension zip): <file>.jsonl.zip, one archive per live file holding that one file,
 * so a single file can be restored. Registered as [AuditArchiveSettings] FormatHandlers[zip]=expAuditZipFormat.
 * Levels 0-9 (deflate).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditZipFormat extends expAuditFormatBase
{
    public function name()
    {
        return 'zip';
    }

    public function problem()
    {
        return class_exists( 'ZipArchive' ) ? '' : 'the PHP extension zip is missing';
    }

    public function extension()
    {
        return '.zip';
    }

    public function compress( $source, $target, $level )
    {
        $zip = new ZipArchive();
        if ( $zip->open( $target, ZipArchive::CREATE | ZipArchive::OVERWRITE ) !== true )
            return false;
        $entry = basename( $source );
        if ( !$zip->addFile( $source, $entry ) )
        {
            $zip->close();
            return false;
        }
        if ( method_exists( $zip, 'setCompressionName' ) )
            $zip->setCompressionName( $entry, ZipArchive::CM_DEFLATE, max( 0, min( 9, (int)$level ) ) );
        return $zip->close() && is_file( $target );
    }

    public function open( $archive )
    {
        $zip = new ZipArchive();
        if ( $zip->open( $archive ) !== true || $zip->numFiles < 1 )
            return false;
        $entry = $zip->getNameIndex( 0 );
        $zip->close();
        return @fopen( 'zip://' . $archive . '#' . $entry, 'rb' );
    }
}
