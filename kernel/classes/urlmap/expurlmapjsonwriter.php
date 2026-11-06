<?php
/**
 * File containing the expUrlMapJsonWriter class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Writes JSON: an array whose first entry is the header {"schema": "exp-url-map/1"}, then one object per row with
 * all six canonical keys, pretty-printed, UTF-8 and slashes unescaped, "\n" line ends and a final line end.
 */
class expUrlMapJsonWriter extends expUrlMapWriter
{
    /**
     * @param expUrlMapRow[] $rows
     * @return string
     */
    function writeString( array $rows )
    {
        $entries = array( array( 'schema' => expUrlMapJsonReader::SCHEMA ) );
        foreach ( $rows as $row )
            $entries[] = $row->toArray();
        $json = json_encode( $entries, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE );
        if ( $json === false )
            throw new expUrlMapFormatException( 'The rows cannot be written as JSON: ' . json_last_error_msg() );
        return $json . "\n";
    }
}
