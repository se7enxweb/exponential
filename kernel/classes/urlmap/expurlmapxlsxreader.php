<?php
/**
 * File containing the expUrlMapXlsxReader class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Reads an Excel workbook (.xlsx): the first sheet's table, with a header row as in CSV (expUrlMapTable). A row's
 * line() is its row number in the sheet.
 */
class expUrlMapXlsxReader extends expUrlMapReader
{
    /**
     * @param string $data
     * @return expUrlMapRow[]
     */
    function readString( $data )
    {
        if ( $data === '' )
            return array();
        try
        {
            return expUrlMapTable::rowsFromTable( expSpreadsheetReader::readXlsxString( $data ) );
        }
        catch ( expSpreadsheetException $e )
        {
            throw new expUrlMapFormatException( $e->getMessage(), 0, $e );
        }
    }

    /**
     * @param string $path
     * @return expUrlMapRow[]
     */
    function readFile( $path )
    {
        if ( is_file( $path ) && filesize( $path ) === 0 )
            return array();
        try
        {
            return expUrlMapTable::rowsFromTable( expSpreadsheetReader::readXlsxFile( $path ) );
        }
        catch ( expSpreadsheetException $e )
        {
            throw new expUrlMapFormatException( $e->getMessage(), 0, $e );
        }
    }
}
