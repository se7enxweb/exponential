<?php
/**
 * File containing the expUrlMapSpreadsheetWriter class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Writes a URL map as a spreadsheet of one sheet ("URL map"): the header row with the canonical columns (bold,
 * frozen), then one row per mapping, every cell text. The option "type" is "xlsx" (default) or "ods".
 */
class expUrlMapSpreadsheetWriter extends expUrlMapWriter
{
    const SHEET = 'URL map';

    /**
     * @param expUrlMapRow[] $rows
     * @return string
     */
    function writeString( array $rows )
    {
        try
        {
            if ( $this->type() === 'ods' )
                return expSpreadsheetWriter::odsString( expUrlMapTable::tableFromRows( $rows ), $this->sheetOptions() );
            return expSpreadsheetWriter::xlsxString( expUrlMapTable::tableFromRows( $rows ), $this->sheetOptions() );
        }
        catch ( expSpreadsheetException $e )
        {
            throw new expUrlMapFormatException( $e->getMessage(), 0, $e );
        }
    }

    /**
     * @param string $path
     * @param expUrlMapRow[] $rows
     */
    function writeFile( $path, array $rows )
    {
        try
        {
            if ( $this->type() === 'ods' )
                expSpreadsheetWriter::writeOdsFile( $path, expUrlMapTable::tableFromRows( $rows ), $this->sheetOptions() );
            else
                expSpreadsheetWriter::writeXlsxFile( $path, expUrlMapTable::tableFromRows( $rows ), $this->sheetOptions() );
        }
        catch ( expSpreadsheetException $e )
        {
            throw new expUrlMapFormatException( $e->getMessage(), 0, $e );
        }
    }

    /**
     * @return string
     */
    private function type()
    {
        return $this->option( 'type', 'xlsx' ) === 'ods' ? 'ods' : 'xlsx';
    }

    /**
     * @return array
     */
    private function sheetOptions()
    {
        return array( 'sheet' => self::SHEET, 'header' => true, 'widths' => array( 45, 45, 18, 14, 10, 40 ) );
    }
}
