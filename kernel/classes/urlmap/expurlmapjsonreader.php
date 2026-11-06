<?php
/**
 * File containing the expUrlMapJsonReader class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Reads JSON URL maps: an array of objects with the canonical keys (other names of the columns as in CSV headers are
 * accepted), optionally starting with the header object {"schema": "exp-url-map/1"}; an object
 * {"schema": "exp-url-map/1", "rows": [...]} is read too. A row's line() is its entry number in the array (from 1,
 * the header not counted).
 */
class expUrlMapJsonReader extends expUrlMapReader
{
    const SCHEMA = 'exp-url-map/1';

    /**
     * @param string $data
     * @return expUrlMapRow[]
     */
    function readString( $data )
    {
        $text = self::utf8Text( $data );
        if ( trim( $text ) === '' )
            return array();
        $decoded = json_decode( $text, true, 64 );
        if ( $decoded === null && json_last_error() !== JSON_ERROR_NONE )
            throw new expUrlMapFormatException( 'The file is not valid JSON: ' . json_last_error_msg() );
        if ( is_array( $decoded ) && isset( $decoded['rows'] ) && is_array( $decoded['rows'] ) )
        {
            self::checkSchema( $decoded );
            $entries = $decoded['rows'];
        }
        elseif ( is_array( $decoded ) && ( !$decoded || array_keys( $decoded ) === range( 0, count( $decoded ) - 1 ) ) )
        {
            $entries = $decoded;
            if ( $entries && is_array( $entries[0] ) && isset( $entries[0]['schema'] ) && !isset( $entries[0]['old_url'] ) )
            {
                self::checkSchema( $entries[0] );
                array_shift( $entries );
            }
        }
        else
            throw new expUrlMapFormatException( 'The JSON is not an array of rows (objects with old_url and new_url)' );

        $rows = array();
        foreach ( array_values( $entries ) as $index => $entry )
        {
            $number = $index + 1;
            if ( !is_array( $entry ) || ( $entry && array_keys( $entry ) === range( 0, count( $entry ) - 1 ) ) )
            {
                $rows[] = expUrlMapRow::notImportable( '', '', 'entry ' . $number . ' is not an object', $number );
                continue;
            }
            $values = array();
            $nested = false;
            foreach ( $entry as $key => $value )
            {
                $column = expUrlMapTable::columnOf( $key );
                if ( $column === null || isset( $values[$column] ) )
                    continue;
                if ( is_array( $value ) )
                    $nested = $column;
                $values[$column] = is_bool( $value ) ? ( $value ? 'true' : 'false' ) : ( is_scalar( $value ) ? (string)$value : '' );
            }
            $row = expUrlMapRow::fromArray( $values, $number );
            if ( $nested !== false )
                $row->addProblem( $nested . ' is not a string' );
            $rows[] = $row;
        }
        return $rows;
    }

    /**
     * @param array $header
     */
    private static function checkSchema( array $header )
    {
        if ( isset( $header['schema'] ) && $header['schema'] !== self::SCHEMA )
            throw new expUrlMapFormatException( 'The JSON schema "' . ( is_scalar( $header['schema'] ) ? $header['schema'] : '?' ) . '" is not ' . self::SCHEMA );
    }
}
