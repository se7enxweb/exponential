<?php
/**
 * File containing the expUpgradeCatalog class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * What the database itself says about a table: its columns, the length of a text column, its indexes as lists of
 * columns. Asked of the engine's catalog (information_schema, pg_indexes, PRAGMA, user_ind_columns), never inferred
 * from a query that fails. Indexes are compared by their columns, not their names: the schema handlers name an index
 * differently per engine (SQLite prefixes the table, Oracle shortens long names).
 *
 * Every method returns null when the catalog cannot be read.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeCatalog
{
    /**
     * The column names of a table, lower case.
     *
     * @param eZDBInterface $db
     * @param string $table
     * @return string[]|null null when the table does not exist or the catalog cannot be read
     */
    public static function columns( $db, $table )
    {
        $exists = expKernelTableSchema::exists( $table, $db, true );
        if ( $exists !== true )
            return null;
        if ( expKernelTableSchema::type( $db ) === 'mongodb' )
            return null;
        $columns = expDataTableDescriber::columns( $db, $table );
        if ( !$columns )
            return null;
        return array_map( 'strtolower', array_keys( $columns ) );
    }

    /**
     * Whether a table has a column: true, false, or null (no table, or the catalog cannot be read).
     *
     * @param eZDBInterface $db
     * @param string $table
     * @param string $column
     * @return bool|null
     */
    public static function hasColumn( $db, $table, $column )
    {
        $columns = self::columns( $db, $table );
        if ( $columns === null )
            return null;
        return in_array( strtolower( $column ), $columns, true );
    }

    /**
     * The declared maximum length of a text column, 0 when the engine keeps none (SQLite), null when unknown.
     *
     * @param eZDBInterface $db
     * @param string $table
     * @param string $column
     * @return int|null
     */
    public static function columnLength( $db, $table, $column )
    {
        if ( !self::safe( $table ) || !self::safe( $column ) )
            return null;
        switch ( expKernelTableSchema::type( $db ) )
        {
            case 'mysql':
                $sql = "SELECT character_maximum_length AS len FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = '$table' AND column_name = '$column'";
                break;
            case 'postgresql':
                $sql = "SELECT character_maximum_length AS len FROM information_schema.columns WHERE table_schema = current_schema() AND table_name = '$table' AND column_name = '$column'";
                break;
            case 'oracle':
                $sql = "SELECT char_length AS len FROM user_tab_columns WHERE table_name = '" . strtoupper( $table ) . "' AND column_name = '" . strtoupper( $column ) . "'";
                break;
            case 'sqlite':
            case 'mongodb':
                return 0;
            default:
                return null;
        }
        $rows = expKernelTableSchema::tryArrayQuery( $db, $sql );
        if ( !$rows )
            return null;
        $row = array_change_key_case( (array)$rows[0], CASE_LOWER );
        return isset( $row['len'] ) ? (int)$row['len'] : 0;
    }

    /**
     * The indexes of a table: name => array( 'unique' => bool, 'fields' => string[] lower case, in order ).
     *
     * @param eZDBInterface $db
     * @param string $table
     * @return array|null
     */
    public static function indexes( $db, $table )
    {
        if ( !self::safe( $table ) )
            return null;
        $type = expKernelTableSchema::type( $db );
        $indexes = array();
        switch ( $type )
        {
            case 'mysql':
                $rows = expKernelTableSchema::tryArrayQuery( $db, "SELECT index_name AS iname, column_name AS cname, non_unique AS nu, seq_in_index AS seq FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = '$table' ORDER BY index_name, seq_in_index" );
                if ( $rows === null )
                    return null;
                foreach ( $rows as $row )
                {
                    $row = array_change_key_case( (array)$row, CASE_LOWER );
                    $name = (string)$row['iname'];
                    if ( !isset( $indexes[$name] ) )
                        $indexes[$name] = array( 'unique' => !(int)$row['nu'], 'fields' => array() );
                    $indexes[$name]['fields'][] = strtolower( (string)$row['cname'] );
                }
                return $indexes;
            case 'postgresql':
                $rows = expKernelTableSchema::tryArrayQuery( $db, "SELECT indexname AS iname, indexdef AS idef FROM pg_indexes WHERE schemaname = current_schema() AND tablename = '$table'" );
                if ( $rows === null )
                    return null;
                foreach ( $rows as $row )
                {
                    $row = array_change_key_case( (array)$row, CASE_LOWER );
                    $def = (string)$row['idef'];
                    if ( !preg_match( '/\(([^)]*)\)\s*$/', $def, $m ) )
                        continue;
                    $fields = array();
                    foreach ( explode( ',', $m[1] ) as $f )
                        $fields[] = strtolower( trim( preg_replace( '/\s+(ASC|DESC).*$/i', '', trim( $f ) ), '" ' ) );
                    $indexes[(string)$row['iname']] = array( 'unique' => stripos( $def, 'CREATE UNIQUE' ) === 0, 'fields' => $fields );
                }
                return $indexes;
            case 'sqlite':
                $list = expKernelTableSchema::tryArrayQuery( $db, "PRAGMA index_list($table)" );
                if ( $list === null )
                    return null;
                foreach ( $list as $row )
                {
                    $row = array_change_key_case( (array)$row, CASE_LOWER );
                    $name = (string)$row['name'];
                    if ( !self::safe( $name ) )
                        continue;
                    $info = expKernelTableSchema::tryArrayQuery( $db, "PRAGMA index_info($name)" );
                    $fields = array();
                    foreach ( (array)$info as $col )
                    {
                        $col = array_change_key_case( (array)$col, CASE_LOWER );
                        $fields[(int)$col['seqno']] = strtolower( (string)$col['name'] );
                    }
                    ksort( $fields );
                    $indexes[$name] = array( 'unique' => !empty( $row['unique'] ), 'fields' => array_values( $fields ) );
                }
                return $indexes;
            case 'oracle':
                $upper = strtoupper( $table );
                $rows = expKernelTableSchema::tryArrayQuery( $db, "SELECT c.index_name AS iname, c.column_name AS cname, i.uniqueness AS uq FROM user_ind_columns c JOIN user_indexes i ON i.index_name = c.index_name WHERE c.table_name = '$upper' ORDER BY c.index_name, c.column_position" );
                if ( $rows === null )
                    return null;
                foreach ( $rows as $row )
                {
                    $row = array_change_key_case( (array)$row, CASE_LOWER );
                    $name = (string)$row['iname'];
                    if ( !isset( $indexes[$name] ) )
                        $indexes[$name] = array( 'unique' => strtoupper( (string)$row['uq'] ) === 'UNIQUE', 'fields' => array() );
                    $indexes[$name]['fields'][] = strtolower( (string)$row['cname'] );
                }
                return $indexes;
        }
        return null;
    }

    /**
     * Whether a table has an index on exactly these columns, in this order (true, false, null when unknown). With
     * $unique true only a unique index counts.
     *
     * @param eZDBInterface $db
     * @param string $table
     * @param string[] $fields
     * @param bool $unique
     * @return bool|null
     */
    public static function hasIndex( $db, $table, array $fields, $unique = false )
    {
        $indexes = self::indexes( $db, $table );
        if ( $indexes === null )
            return null;
        $fields = array_map( 'strtolower', array_values( $fields ) );
        foreach ( $indexes as $index )
        {
            if ( $index['fields'] === $fields && ( !$unique || $index['unique'] ) )
                return true;
        }
        return false;
    }

    /**
     * Runs SQL text of one or more statements (as a schema handler writes it, each ending in ";\n"), raising on the
     * first error.
     *
     * @param eZDBInterface $db
     * @param string $sql
     * @return string[] the statements that ran
     * @throws Exception
     */
    public static function execute( $db, $sql )
    {
        $ran = array();
        $before = expAuditIndexSchema::errorHandling( $db );
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        try
        {
            foreach ( self::statements( $sql ) as $statement )
            {
                if ( $db->query( $statement ) === false )
                    throw new RuntimeException( 'the statement failed: ' . $statement );
                $ran[] = $statement;
            }
        }
        finally
        {
            $db->setErrorHandling( $before );
        }
        return $ran;
    }

    /**
     * The statements of SQL text: split at ";" that ends a line, comments and empty parts left out.
     *
     * @param string $sql
     * @return string[]
     */
    public static function statements( $sql )
    {
        $out = array();
        foreach ( preg_split( '/;\s*(\r?\n|$)/', (string)$sql ) as $part )
        {
            $lines = array();
            foreach ( preg_split( '/\r?\n/', $part ) as $line )
                if ( !preg_match( '/^\s*--/', $line ) )
                    $lines[] = $line;
            $part = trim( implode( "\n", $lines ) );
            if ( $part !== '' )
                $out[] = $part;
        }
        return $out;
    }

    /**
     * @param string $name
     * @return bool
     */
    protected static function safe( $name )
    {
        return (bool)preg_match( '/^[A-Za-z_][A-Za-z0-9_]{0,127}$/', (string)$name );
    }
}
