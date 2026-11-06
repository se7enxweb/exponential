<?php
/**
 * File containing the expUrlMapCsvWriter class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Writes CSV (RFC 4180): a header row with the canonical columns, CRLF line ends, fields quoted when they hold the
 * delimiter, a quote, a line break or leading or trailing spaces. Options: "delimiter" (default ","), "bom" (default
 * true: a UTF-8 byte order mark, so that spreadsheet programs read the text as UTF-8).
 */
class expUrlMapCsvWriter extends expUrlMapWriter
{
    /**
     * @param expUrlMapRow[] $rows
     * @return string
     */
    function writeString( array $rows )
    {
        $delimiter = (string)$this->option( 'delimiter', ',' );
        $out = $this->option( 'bom', true ) ? "\xEF\xBB\xBF" : '';
        foreach ( expUrlMapTable::tableFromRows( $rows ) as $cells )
        {
            $fields = array();
            foreach ( $cells as $cell )
                $fields[] = self::field( (string)$cell, $delimiter );
            $out .= implode( $delimiter, $fields ) . "\r\n";
        }
        return $out;
    }

    /**
     * @param string $value
     * @param string $delimiter
     * @return string
     */
    static function field( $value, $delimiter )
    {
        if ( $value === '' )
            return '';
        if ( strpbrk( $value, $delimiter . "\"\r\n" ) !== false || trim( $value ) !== $value )
            return '"' . str_replace( '"', '""', $value ) . '"';
        return $value;
    }
}
