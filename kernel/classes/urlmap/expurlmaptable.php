<?php
/**
 * File containing the expUrlMapTable class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The table part shared by CSV and the spreadsheets: a header row is found and its column names mapped to the
 * canonical columns (with the common English and German names), and the other rows become expUrlMapRow objects.
 * Without a header the columns are in the canonical order: old_url, new_url, kind, siteaccess, language, note.
 */
class expUrlMapTable
{
    /** @var array other names of the columns (compared in lower case, with "_", "-" and spaces removed) */
    const SYNONYMS = array(
        'old_url' => array( 'oldurl', 'old', 'oldpath', 'oldaddress', 'from', 'source', 'sourceurl', 'url', 'alteurl', 'alt', 'alteadresse', 'quelle', 'von' ),
        'new_url' => array( 'newurl', 'new', 'newpath', 'newaddress', 'to', 'target', 'targeturl', 'destination', 'neueurl', 'neu', 'neueadresse', 'ziel', 'nach' ),
        'kind' => array( 'kind', 'type', 'art', 'typ' ),
        'siteaccess' => array( 'siteaccess', 'site', 'sitezugang' ),
        'language' => array( 'language', 'lang', 'locale', 'sprache' ),
        'note' => array( 'note', 'notes', 'comment', 'comments', 'notiz', 'bemerkung', 'kommentar', 'hinweis' ),
    );

    /**
     * @param string $name
     * @return string|null the canonical column of a header cell, or null
     */
    static function columnOf( $name )
    {
        $key = strtolower( preg_replace( '/[\s_\-]+/u', '', trim( (string)$name ) ) );
        foreach ( self::SYNONYMS as $column => $names )
        {
            if ( in_array( $key, $names, true ) )
                return $column;
        }
        return null;
    }

    /**
     * Whether a table row is a header: it names old_url or new_url, and its first cell is not an address.
     *
     * @param string[] $cells
     * @return bool
     */
    static function isHeader( array $cells )
    {
        $first = isset( $cells[0] ) ? trim( (string)$cells[0] ) : '';
        if ( $first === '' || $first[0] === '/' || preg_match( '#^[a-z]+://#i', $first ) )
            return false;
        foreach ( $cells as $cell )
        {
            $column = self::columnOf( $cell );
            if ( $column === 'old_url' || $column === 'new_url' )
                return true;
        }
        return false;
    }

    /**
     * Rows from a table.
     *
     * @param array $table line number => list of cell strings (keys of the cells are column indexes from 0)
     * @return expUrlMapRow[]
     */
    static function rowsFromTable( array $table )
    {
        $map = null;
        $rows = array();
        foreach ( $table as $line => $cells )
        {
            $cells = self::denseCells( $cells );
            if ( self::isBlank( $cells ) )
                continue;
            $first = ltrim( isset( $cells[0] ) ? $cells[0] : '' );
            if ( $first !== '' && $first[0] === '#' )
                continue;
            if ( $map === null )
            {
                if ( self::isHeader( $cells ) )
                {
                    $map = array();
                    foreach ( $cells as $index => $cell )
                    {
                        $column = self::columnOf( $cell );
                        if ( $column !== null && !in_array( $column, $map, true ) )
                            $map[$index] = $column;
                    }
                    continue;
                }
                $map = expUrlMapRow::COLUMNS;
            }
            $values = array();
            foreach ( $map as $index => $column )
                $values[$column] = isset( $cells[$index] ) ? $cells[$index] : '';
            $row = expUrlMapRow::fromArray( $values, $line );
            $extra = array_diff_key( $cells, $map );
            foreach ( $extra as $value )
            {
                if ( trim( $value ) !== '' && $map === expUrlMapRow::COLUMNS )
                {
                    $row->addProblem( 'the row has more than ' . count( $map ) . ' columns' );
                    break;
                }
            }
            $rows[] = $row;
        }
        return $rows;
    }

    /**
     * The table of rows: the header row with the canonical column names, then one row per expUrlMapRow.
     *
     * @param expUrlMapRow[] $rows
     * @return array list of lists of strings
     */
    static function tableFromRows( array $rows )
    {
        $table = array( expUrlMapRow::COLUMNS );
        foreach ( $rows as $row )
            $table[] = array_values( $row->toArray() );
        return $table;
    }

    /**
     * @param array $cells
     * @return string[] a list from index 0 with every gap filled with ''
     */
    private static function denseCells( array $cells )
    {
        if ( !$cells )
            return array();
        $max = max( array_keys( $cells ) );
        $dense = array();
        for ( $i = 0; $i <= $max; $i++ )
            $dense[$i] = isset( $cells[$i] ) ? (string)$cells[$i] : '';
        return $dense;
    }

    /**
     * @param string[] $cells
     * @return bool
     */
    private static function isBlank( array $cells )
    {
        foreach ( $cells as $cell )
        {
            if ( trim( $cell ) !== '' )
                return false;
        }
        return true;
    }
}
