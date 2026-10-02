<?php
/**
 * An archive format (gzip, bzip2, xz, zstd, zip), registered in audit.ini:
 *   [AuditArchiveSettings]
 *   FormatHandlers[<name>]=<class implementing expAuditFormatHandler>
 * Counted by the RAD survey (registry "auditformats"). Archives are written from stage 5 on.
 * Guide: doc/bc/6.0/audit.md ("Rotation, archives and retention").
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

interface expAuditFormatHandler
{
    /** @return string */
    public function name();

    /** @return string '' when available (PHP extension or binary present), else why not */
    public function problem();

    /** @return string The file extension, e.g. '.gz' */
    public function extension();

    /** @return bool false on failure */
    public function compress( $source, $target, $level );

    /** @return resource A stream reading the plain lines (for verify, reindex, restore) */
    public function open( $archive );
}
