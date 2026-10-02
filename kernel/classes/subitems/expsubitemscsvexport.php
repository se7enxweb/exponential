<?php
/**
 * The CSV of the admin subitems list (content/subitemsexport/<parent node id>).
 *
 * Excel-friendly: UTF-8 with a byte order mark, comma separated, CRLF line ends, a header row with
 * the column names, then one row per child with each column's text(). Cells that start with
 * =, +, - or @ are prefixed with an apostrophe so a spreadsheet does not run them as formulas.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expSubitemsCSVExport
{
    const BOM = "\xEF\xBB\xBF";
    /** Rows fetched per subTree() call while exporting. */
    const BATCH = 100;

    /**
     * The download file name for a parent: its name, made safe, plus ".csv".
     *
     * @param string $name
     * @return string
     */
    public static function fileName( $name )
    {
        $name = trim( (string)$name );
        if ( class_exists( 'eZCharTransform' ) )
        {
            $trans = eZCharTransform::instance();
            $name = $trans->transformByGroup( $name, 'urlalias' );
        }
        $name = preg_replace( '/[^A-Za-z0-9._\-]+/', '-', $name );
        $name = trim( $name, '-._' );
        if ( $name === '' )
            $name = 'subitems';
        return substr( $name, 0, 100 ) . '.csv';
    }

    /**
     * A cell made safe for spreadsheets.
     *
     * @param string $text
     * @return string
     */
    public static function cell( $text )
    {
        $text = (string)$text;
        if ( $text !== '' && strpbrk( $text[0], "=+-@\t\r" ) !== false && !is_numeric( $text ) )
            $text = "'" . $text;
        return $text;
    }

    /**
     * Writes one CSV line.
     *
     * @param resource $handle
     * @param array $cells
     */
    public static function writeLine( $handle, array $cells )
    {
        $cells = array_map( array( __CLASS__, 'cell' ), $cells );
        fputcsv( $handle, $cells, ',', '"', '', "\r\n" );
    }

    /**
     * Writes the header row.
     *
     * @param resource $handle
     * @param expSubitemsColumn[] $columns
     * @param bool $bom whether to start with the UTF-8 byte order mark
     */
    public static function writeHeader( $handle, array $columns, $bom = true )
    {
        if ( $bom )
            fwrite( $handle, self::BOM );
        $names = array();
        foreach ( $columns as $column )
            $names[] = $column->name();
        self::writeLine( $handle, $names );
    }

    /**
     * Writes the rows of some nodes.
     *
     * @param resource $handle
     * @param eZContentObjectTreeNode[] $nodes
     * @param expSubitemsColumn[] $columns
     * @return int rows written
     */
    public static function writeRows( $handle, array $nodes, array $columns )
    {
        $n = 0;
        foreach ( $nodes as $node )
        {
            $cells = array();
            foreach ( $columns as $key => $column )
            {
                try
                {
                    $cells[] = $column->text( $node, $column->value( $node ) );
                }
                catch ( Exception $e )
                {
                    eZDebug::writeWarning( "Column '$key': " . $e->getMessage(), __METHOD__ );
                    $cells[] = '';
                }
            }
            self::writeLine( $handle, $cells );
            $n++;
        }
        return $n;
    }

    /**
     * Writes the whole export of a parent: header and every child the user can read, up to the
     * registry's CSVLimit, fetched in batches so memory stays flat.
     *
     * @param resource $handle
     * @param eZContentObjectTreeNode $parent
     * @param expSubitemsColumnRegistry $registry
     * @param expSubitemsColumn[] $columns
     * @param string $sortKey
     * @param bool $ascending
     * @return int rows written
     */
    public static function export( $handle, eZContentObjectTreeNode $parent, expSubitemsColumnRegistry $registry,
                                   array $columns, $sortKey, $ascending )
    {
        self::writeHeader( $handle, $columns );
        $limit = $registry->csvLimit();
        $written = 0;
        for ( $offset = 0; $offset < $limit; $offset += self::BATCH )
        {
            $fetched = expSubitemsServerFunctions::fetchChildren( $parent, $registry, min( self::BATCH, $limit - $offset ),
                                                                  $offset, $sortKey, $ascending );
            if ( !$fetched['nodes'] )
                break;
            $written += self::writeRows( $handle, $fetched['nodes'], $columns );
            if ( count( $fetched['nodes'] ) < self::BATCH )
                break;
            eZContentObject::clearCache();
        }
        return $written;
    }
}
