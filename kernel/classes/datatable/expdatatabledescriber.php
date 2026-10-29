<?php
/**
 * File containing the expDataTableDescriber class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A data table as its database has it (doc/eng-us/features/6.0/data-tables.md, "Describe"): whether the table
 * exists, its columns from the engine's own catalogue (information_schema, PRAGMA table_info, user_tab_columns; a
 * sample of documents on MongoDB), its row count, and how it differs from the class's definition():
 *
 *   missing_table       the definition's table is not there
 *   missing_column      a field of the definition has no column
 *   extra_column        a column the definition does not know (harmless; listed)
 *   type_mismatch       an integer field over a text column, or a text field over a number column
 *
 *   $description = expDataTableDescriber::describe( expDataTableCatalogue::instance()->entry( 'eZViewCounter' ) );
 *
 * @since 6.0.15
 * @package kernel
 */
class expDataTableDescriber
{
    /**
     * Describes an entry of the catalogue on its connection.
     *
     * @param array $entry
     * @param eZDBInterface|null $db the connection (the entry's when null)
     * @return array exists (bool|null), columns (name => type, nullable, default), rows (int|null), drift (list of
     *               kind, column, detail), engine, connection, error
     */
    public static function describe( array $entry, $db = null )
    {
        $result = array( 'exists' => null, 'columns' => array(), 'rows' => null, 'drift' => array(), 'engine' => '',
                         'connection' => $entry['connection'] !== '' ? $entry['connection'] : expDataConnections::SITE, 'error' => '' );
        if ( $entry['table'] === '' )
        {
            $result['error'] = $entry['error'] !== '' ? $entry['error'] : 'The class has no table.';
            return $result;
        }
        try
        {
            $db = $db ?: expDataConnections::db( $entry['connection'] );
        }
        catch ( Throwable $e )
        {
            $result['error'] = $e->getMessage();
            return $result;
        }
        $result['engine'] = expKernelTableSchema::type( $db );
        $result['exists'] = expKernelTableSchema::exists( $entry['table'], $db, true );
        if ( $result['exists'] !== true )
        {
            if ( $result['exists'] === false )
                $result['drift'][] = array( 'kind' => 'missing_table', 'column' => '', 'detail' => $entry['table'] );
            return $result;
        }
        $result['columns'] = self::columns( $db, $entry['table'] );
        $result['rows'] = self::rowCount( $db, $entry['table'] );
        $result['drift'] = array_merge( $result['drift'], self::drift( $entry, $result['columns'], $result['engine'] ) );
        return $result;
    }

    /**
     * The columns of a table as the engine reports them.
     *
     * @param eZDBInterface $db
     * @param string $table
     * @return array name => array( type, nullable, default )
     */
    public static function columns( $db, $table )
    {
        $table = (string)$table;
        if ( !preg_match( '/^[A-Za-z_][A-Za-z0-9_]{0,127}$/', $table ) )
            return array();
        $type = expKernelTableSchema::type( $db );
        $sql = null;
        switch ( $type )
        {
            case 'mysql':
                $sql = "SELECT column_name AS name, data_type AS type, is_nullable AS nullable, column_default AS def FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = '$table' ORDER BY ordinal_position";
                break;
            case 'postgresql':
                $sql = "SELECT column_name AS name, data_type AS type, is_nullable AS nullable, column_default AS def FROM information_schema.columns WHERE table_schema = current_schema() AND table_name = '$table' ORDER BY ordinal_position";
                break;
            case 'sqlserver':
                $sql = "SELECT column_name AS name, data_type AS type, is_nullable AS nullable, column_default AS def FROM information_schema.columns WHERE table_name = '$table' ORDER BY ordinal_position";
                break;
            case 'oracle':
                $upper = strtoupper( $table );
                $sql = "SELECT column_name AS name, data_type AS type, nullable AS nullable, data_default AS def FROM user_tab_columns WHERE table_name = '$upper' ORDER BY column_id";
                break;
            case 'sqlite':
                $sql = "PRAGMA table_info($table)";
                break;
            case 'mongodb':
                return self::mongoColumns( $db, $table );
        }
        if ( $sql === null )
            return array();
        $rows = expKernelTableSchema::tryArrayQuery( $db, $sql );
        $columns = array();
        foreach ( (array)$rows as $row )
        {
            $row = array_change_key_case( (array)$row, CASE_LOWER );
            if ( $type === 'sqlite' )
            {
                $columns[(string)$row['name']] = array( 'type' => strtolower( (string)$row['type'] ), 'nullable' => empty( $row['notnull'] ),
                                                        'default' => isset( $row['dflt_value'] ) ? (string)$row['dflt_value'] : null );
                continue;
            }
            if ( !isset( $row['name'] ) )
                continue;
            $nullable = strtoupper( (string)( isset( $row['nullable'] ) ? $row['nullable'] : 'YES' ) );
            $columns[strtolower( (string)$row['name'] )] = array( 'type' => strtolower( (string)$row['type'] ),
                                                                   'nullable' => $nullable === 'YES' || $nullable === 'Y',
                                                                   'default' => isset( $row['def'] ) ? (string)$row['def'] : null );
        }
        return $columns;
    }

    /**
     * The number of rows, or null when it cannot be counted.
     *
     * @param eZDBInterface $db
     * @param string $table
     * @return int|null
     */
    public static function rowCount( $db, $table )
    {
        if ( !preg_match( '/^[A-Za-z_][A-Za-z0-9_]{0,127}$/', (string)$table ) )
            return null;
        if ( expKernelTableSchema::type( $db ) === 'mongodb' )
        {
            try
            {
                return method_exists( $db, 'count' ) ? (int)$db->count( $table, array() ) : null;
            }
            catch ( Throwable $e )
            {
                return null;
            }
        }
        $rows = expKernelTableSchema::tryArrayQuery( $db, "SELECT COUNT(*) AS row_count FROM $table" );
        if ( !$rows )
            return null;
        $row = array_change_key_case( (array)$rows[0], CASE_LOWER );
        return isset( $row['row_count'] ) ? (int)$row['row_count'] : null;
    }

    /**
     * How the columns differ from the definition (pure).
     *
     * @param array $entry
     * @param array $columns name => type, nullable, default
     * @param string $engine
     * @return array list of kind, column, detail
     */
    public static function drift( array $entry, array $columns, $engine = '' )
    {
        $drift = array();
        $lower = array();
        foreach ( $columns as $name => $column )
            $lower[strtolower( (string)$name )] = $column;
        $known = array();
        foreach ( $entry['fields'] as $name => $field )
        {
            $column = $field['short_name'] !== '' && !isset( $lower[strtolower( $name )] ) ? $field['short_name'] : $name;
            $known[strtolower( $column )] = true;
            if ( !isset( $lower[strtolower( $column )] ) )
            {
                // MongoDB: a sample holds only the fields its documents have
                if ( $engine !== 'mongodb' )
                    $drift[] = array( 'kind' => 'missing_column', 'column' => $name, 'detail' => $field['datatype'] );
                continue;
            }
            $type = $lower[strtolower( $column )]['type'];
            $numberColumn = (bool)preg_match( '/int|number|numeric|decimal|serial|bit|bool/', $type );
            $textColumn = (bool)preg_match( '/char|text|clob|string/', $type );
            if ( $field['datatype'] === 'integer' && $textColumn )
                $drift[] = array( 'kind' => 'type_mismatch', 'column' => $name, 'detail' => "integer over $type" );
            elseif ( in_array( $field['datatype'], array( 'string', 'text' ), true ) && $numberColumn && $engine !== 'sqlite' )
                $drift[] = array( 'kind' => 'type_mismatch', 'column' => $name, 'detail' => $field['datatype'] . " over $type" );
        }
        foreach ( array_keys( $lower ) as $name )
        {
            if ( !isset( $known[$name] ) && $name !== '_id' )
                $drift[] = array( 'kind' => 'extra_column', 'column' => $name, 'detail' => $lower[$name]['type'] );
        }
        return $drift;
    }

    /**
     * The fields of a MongoDB collection from a sample of its documents.
     *
     * @param eZDBInterface $db
     * @param string $collection
     * @return array
     */
    protected static function mongoColumns( $db, $collection )
    {
        $sample = 50;
        try
        {
            $ini = eZINI::instance( 'datatable.ini' );
            if ( $ini->hasVariable( 'DataTableSettings', 'MongoSampleSize' ) )
                $sample = max( 1, min( 1000, (int)$ini->variable( 'DataTableSettings', 'MongoSampleSize' ) ) );
            $rows = method_exists( $db, 'aggregate' ) ? $db->aggregate( $collection, array( array( '$limit' => $sample ) ) ) : array();
        }
        catch ( Throwable $e )
        {
            return array();
        }
        $columns = array();
        foreach ( (array)$rows as $row )
        {
            $row = is_object( $row ) && method_exists( $row, 'getArrayCopy' ) ? $row->getArrayCopy() : (array)$row;
            foreach ( $row as $name => $value )
            {
                if ( isset( $columns[$name] ) )
                    continue;
                $columns[(string)$name] = array( 'type' => is_int( $value ) ? 'int' : ( is_float( $value ) ? 'double' : ( is_bool( $value ) ? 'bool' : ( is_string( $value ) ? 'string' : 'object' ) ) ),
                                                 'nullable' => true, 'default' => null );
            }
        }
        return $columns;
    }
}
?>
