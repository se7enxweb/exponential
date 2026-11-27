<?php
/**
 * File containing the expClickHouseSchema class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The schema handler of ClickHouse (expClickHouseDB): a table of the database-independent schema (.dba) as a ClickHouse
 * MergeTree table, and a ClickHouse database read back into that schema (for the data tables).
 *
 * - Every table is ENGINE = MergeTree ORDER BY its primary key (tuple() without one), with the block number columns
 *   that ClickHouse's lightweight UPDATE needs. The key orders the parts and is what reads by key use; it is not
 *   enforced: a second row with the same key is kept.
 * - A unique index is not enforced either: it is left out, with a note in the debug output. A non-unique index becomes
 *   a data-skipping index (minmax).
 * - A column without NOT NULL is Nullable(...). Types: Int32, Int64, Int16, String (varchar, char and text alike),
 *   Float32, Float64, Decimal(p, s), DateTime, Date. A serial column is Int32 with the comment 'auto_increment' (which
 *   tells it apart when it is read back): its values come from expClickHouseDB::nextSerialID().
 *
 * @since 6.0.15
 * @package lib
 */
class expClickHouseSchema extends eZPgsqlSchema
{
    /** The comment of a serial column */
    const SERIAL_COMMENT = 'auto_increment';

    function schemaType()
    {
        return 'clickhouse';
    }

    function schemaName()
    {
        return 'ClickHouse';
    }

    function isMultiInsertSupported()
    {
        return true;
    }

    function wireProfile( $params = array() )
    {
        return null;
    }

    function generateTableArrays( $table, $table_def, $params, $withClosure = true )
    {
        $arrays = array( 'sequences' => array(), 'tables' => array(), 'indexes' => array(), 'constraints' => array() );
        $lines = array();
        foreach ( $table_def['fields'] as $field_name => $field_def )
            $lines[] = '  ' . $this->generateFieldDef( $table, $field_name, $field_def, true, $params );
        $order = array();
        $indexes = isset( $table_def['indexes'] ) ? $table_def['indexes'] : array();
        foreach ( $indexes as $index_name => $index_def )
        {
            if ( $index_def['type'] === 'primary' )
                $order = self::fieldNames( $index_def );
            elseif ( $index_def['type'] === 'non-unique' )
                $lines[] = '  INDEX ' . self::quote( $index_name ) . ' (' . implode( ', ', array_map( array( __CLASS__, 'quote' ), self::fieldNames( $index_def ) ) ) . ') TYPE minmax GRANULARITY 1';
            elseif ( $index_def['type'] === 'unique' )
                eZDebug::writeNotice( "ClickHouse does not enforce unique keys: the unique index $index_name of $table is left out", __METHOD__ );
        }
        // a key column must not be Nullable
        $sql = 'CREATE TABLE IF NOT EXISTS ' . self::quote( $table ) . " (\n" . implode( ",\n", $lines ) . "\n)"
             . "\nENGINE = MergeTree ORDER BY " . ( $order ? '(' . implode( ', ', array_map( array( __CLASS__, 'quote' ), $order ) ) . ')' : 'tuple()' )
             . "\nSETTINGS enable_block_number_column = 1, enable_block_offset_column = 1";
        $arrays['tables'][] = $sql . ( $withClosure ? ';' : '' );
        return $arrays;
    }

    /**
     * The names of an index's columns.
     */
    static function fieldNames( array $index_def )
    {
        $names = array();
        foreach ( $index_def['fields'] as $field )
            $names[] = is_array( $field ) ? $field['name'] : $field;
        return $names;
    }

    /**
     * A name in backticks.
     */
    static function quote( $name )
    {
        return '`' . str_replace( array( '\\', '`' ), array( '\\\\', '\\`' ), (string)$name ) . '`';
    }

    function generateFieldDef( $table_name, $field_name, $def, $add_default_not_null = true, $params = array() )
    {
        if ( $def['type'] === 'auto_increment' )
            return self::quote( $field_name ) . " Int32 COMMENT '" . self::SERIAL_COMMENT . "'";
        $type = self::columnType( $def['type'], isset( $def['length'] ) ? $def['length'] : null );
        $notNull = !empty( $def['not_null'] );
        $sql = self::quote( $field_name ) . ' ' . ( $notNull ? $type : "Nullable($type)" );
        if ( $add_default_not_null )
        {
            $default = $this->generateDefaultDef( false, false, $def, $params );
            if ( $default )
                $sql .= ' ' . $default;
        }
        return $sql;
    }

    /**
     * The ClickHouse type of a standard (.dba) type (pure).
     *
     * @param string $type
     * @param mixed $length
     * @return string
     */
    static function columnType( $type, $length )
    {
        switch ( $type )
        {
            case 'int':
            case 'mediumint':
                return 'Int32';
            case 'tinyint':
            case 'smallint':
                return 'Int16';
            case 'bigint':
                return 'Int64';
            case 'varchar':
            case 'char':
            case 'text':
            case 'mediumtext':
            case 'longtext':
            case 'blob':
            case 'mediumblob':
            case 'longblob':
                return 'String';
            case 'float':
                return 'Float32';
            case 'double':
                return 'Float64';
            case 'decimal':
                $parts = array_map( 'intval', explode( ',', (string)$length ) );
                return 'Decimal(' . ( !empty( $parts[0] ) ? $parts[0] : 10 ) . ', ' . ( isset( $parts[1] ) ? $parts[1] : 0 ) . ')';
            case 'timestamp':
            case 'datetime':
                return 'DateTime';
            case 'date':
                return 'Date';
            case 'time':
                return 'String';
        }
        throw new RuntimeException( "Unhandled column type: $type" );
    }

    function generateDefaultDef( $table_name, $field_name, $def, $params )
    {
        if ( !array_key_exists( 'default', $def ) || $def['default'] === false || $def['default'] === null )
            return '';
        $numeric = in_array( $def['type'], array( 'int', 'bigint', 'smallint', 'tinyint', 'mediumint', 'decimal', 'float', 'double' ), true );
        if ( $def['default'] === '' && ( $numeric || in_array( $def['type'], array( 'timestamp', 'datetime', 'date' ), true ) ) )
            return '';
        return 'DEFAULT ' . ( $numeric && is_numeric( $def['default'] ) ? (string)$def['default'] : "'" . expClickHouseDialect::escape( $def['default'] ) . "'" );
    }

    function generateAddFieldSql( $table_name, $field_name, $def, $params )
    {
        return 'ALTER TABLE ' . self::quote( $table_name ) . ' ADD COLUMN IF NOT EXISTS ' . $this->generateFieldDef( $table_name, $field_name, $def, true, $params ) . ";\n";
    }

    function generateAlterFieldSql( $table_name, $field_name, $def, $params )
    {
        return 'ALTER TABLE ' . self::quote( $table_name ) . ' MODIFY COLUMN ' . $this->generateFieldDef( $table_name, $field_name, $def, true, $params ) . ";\n";
    }

    function generateDropFieldSql( $table_name, $field_name, $params )
    {
        return 'ALTER TABLE ' . self::quote( $table_name ) . ' DROP COLUMN IF EXISTS ' . self::quote( $field_name ) . ";\n";
    }

    function generateAddIndexSql( $table_name, $index_name, $def, $params, $withClosure = true )
    {
        if ( $def['type'] !== 'non-unique' )
            return '';
        return 'ALTER TABLE ' . self::quote( $table_name ) . ' ADD INDEX IF NOT EXISTS ' . self::quote( $index_name ) . ' ('
             . implode( ', ', array_map( array( __CLASS__, 'quote' ), self::fieldNames( $def ) ) ) . ') TYPE minmax GRANULARITY 1' . ( $withClosure ? ";\n" : '' );
    }

    function generateDropIndexSql( $table_name, $index_name, $def, $withClosure = true )
    {
        if ( $def['type'] !== 'non-unique' )
            return '';
        return 'ALTER TABLE ' . self::quote( $table_name ) . ' DROP INDEX IF EXISTS ' . self::quote( $index_name ) . ( $withClosure ? ";\n" : '' );
    }

    function generateTableInsertSQLList( $tableName, $tableDef, $dataEntries, $params, $withClosure = true )
    {
        return eZDBSchemaInterface::generateTableInsertSQLList( $tableName, $tableDef, $dataEntries, $params + array( 'allow_multi_insert' => true ), $withClosure );
    }

    function escapeSQLString( $value )
    {
        return expClickHouseDialect::escape( $value );
    }

    function insertColumnName( $name )
    {
        return self::quote( $name );
    }

    function generateDropTable( $table )
    {
        return 'DROP TABLE IF EXISTS ' . self::quote( $table ) . ";\n";
    }

    function generateSchemaFile( $schema, $params = array() )
    {
        $parts = array();
        foreach ( $schema as $table => $tableDef )
        {
            if ( $table === '_info' )
                continue;
            $arrays = $this->generateTableArrays( $table, $tableDef, $params, true );
            $parts[] = implode( "\n", $arrays['tables'] );
        }
        return implode( "\n\n", $parts ) . "\n";
    }

    // ------------------------------------------------------------------ reading a database back

    function schema( $params = array() )
    {
        $params = array_merge( array( 'meta_data' => false, 'format' => 'generic', 'sort_columns' => true, 'sort_indexes' => true ), $params );
        if ( $this->Schema === false )
        {
            $schema = array();
            $tables = $this->DBInstance->arrayQuery( "SELECT name FROM system.tables WHERE database = currentDatabase() AND NOT is_temporary AND engine LIKE '%MergeTree' ORDER BY name" );
            foreach ( (array)$tables as $row )
            {
                $table = $row['name'];
                if ( isset( $params['table_include'] ) && is_array( $params['table_include'] ) && !in_array( $table, $params['table_include'] ) )
                    continue;
                $schema[$table] = array( 'name' => $table,
                                         'fields' => $this->fetchTableFields( $table, $params ),
                                         'indexes' => $this->fetchTableIndexes( $table, $params ) );
            }
            $this->transformSchema( $schema, $params['format'] == 'local' );
            ksort( $schema );
            $this->Schema = $schema;
        }
        else
        {
            $this->transformSchema( $this->Schema, $params['format'] == 'local' );
        }
        return $this->Schema;
    }

    function fetchTableFields( $table, $params )
    {
        $fields = array();
        $rows = $this->DBInstance->arrayQuery( "SELECT name, type, default_kind, default_expression, comment FROM system.columns
                                                WHERE database = currentDatabase() AND table = '" . $this->DBInstance->escapeString( $table ) . "' ORDER BY position" );
        foreach ( (array)$rows as $row )
        {
            if ( $row['comment'] === self::SERIAL_COMMENT )
            {
                $fields[$row['name']] = array( 'type' => 'auto_increment', 'default' => false );
                continue;
            }
            $nullable = strpos( $row['type'], 'Nullable(' ) === 0;
            $length = null;
            $field = array( 'type' => self::standardType( $row['type'], $length ) );
            if ( $length !== null )
                $field['length'] = $length;
            $field['not_null'] = $nullable ? 0 : '1';
            $default = $row['default_kind'] === 'DEFAULT' ? $row['default_expression'] : null;
            $field['default'] = $default === null ? ( $nullable ? null : false ) : self::defaultValue( $default );
            $fields[$row['name']] = $field;
        }
        if ( $params['sort_columns'] )
            ksort( $fields );
        return $fields;
    }

    /**
     * The standard (.dba) type of a ClickHouse type (pure).
     *
     * @param string $type
     * @param mixed $length
     * @return string
     */
    static function standardType( $type, &$length )
    {
        $type = preg_replace( '/^(Nullable|LowCardinality)\((.*)\)$/', '$2', (string)$type );
        $type = preg_replace( '/^(Nullable|LowCardinality)\((.*)\)$/', '$2', $type );
        if ( preg_match( '/^Decimal(?:32|64|128|256)?\((\d+),\s*(\d+)\)$/', $type, $m ) )
        {
            $length = $m[1] . ',' . $m[2];
            return 'decimal';
        }
        switch ( preg_replace( '/\(.*$/', '', $type ) )
        {
            case 'Int64':
            case 'UInt64':
            case 'UInt32':
                $length = 20;
                return 'bigint';
            case 'Int32':
                $length = 11;
                return 'int';
            case 'Int16':
            case 'Int8':
            case 'UInt16':
            case 'UInt8':
            case 'Bool':
                $length = 6;
                return 'int';
            case 'Float64':
                return 'double';
            case 'Float32':
                return 'float';
            case 'DateTime':
            case 'DateTime64':
                return 'timestamp';
            case 'Date':
            case 'Date32':
                return 'date';
        }
        return 'longtext';
    }

    /**
     * The value of a ClickHouse default expression ('abc', 0) (pure).
     */
    static function defaultValue( $default )
    {
        $default = trim( (string)$default );
        if ( strtoupper( $default ) === 'NULL' )
            return null;
        if ( preg_match( "/^'((?:[^'\\\\]|\\\\.)*)'$/s", $default, $m ) )
            return stripcslashes( $m[1] );
        return $default;
    }

    function fetchTableIndexes( $table, $params )
    {
        $indexes = array();
        $escaped = $this->DBInstance->escapeString( $table );
        $key = $this->DBInstance->arrayQuery( "SELECT sorting_key FROM system.tables WHERE database = currentDatabase() AND name = '$escaped'" );
        if ( !empty( $key[0]['sorting_key'] ) )
            $indexes['PRIMARY'] = array( 'type' => 'primary', 'fields' => array_map( function ( $f ) { return trim( $f, " `" ); }, explode( ',', $key[0]['sorting_key'] ) ) );
        $rows = $this->DBInstance->arrayQuery( "SELECT name, expr FROM system.data_skipping_indices WHERE database = currentDatabase() AND table = '$escaped' ORDER BY name" );
        foreach ( (array)$rows as $row )
            $indexes[$row['name']] = array( 'type' => 'non-unique', 'fields' => array_map( function ( $f ) { return trim( $f, " `()" ); }, explode( ',', $row['expr'] ) ) );
        if ( $params['sort_indexes'] )
            ksort( $indexes );
        return $indexes;
    }
}
