<?php
/**
 * File containing the expDuckDBSchema class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The schema handler of DuckDB (expDuckDBDB): the database-independent schema (.dba) as DuckDB DDL, and a DuckDB
 * database read back into that schema (for the upgrade check and ezsqldumpschema.php).
 *
 * DuckDB takes PostgreSQL's DDL with three differences, so this is eZPgsqlSchema with them:
 *
 * - A primary key is part of CREATE TABLE. DuckDB 2.0 has no ALTER TABLE ONLY, and 1.x refuses to add a primary key to
 *   a table that has an index already.
 * - CREATE SEQUENCE without CACHE, CREATE INDEX without USING btree (DuckDB's indexes are ART indexes).
 * - No setval() after the data: DuckDB 1.x has none; expDuckDBDB::correctSequenceValues() moves the sequences on
 *   after the data instead, as eZDBSchemaInterface::insertSchema() calls it.
 *
 * Types are PostgreSQL's names, which DuckDB knows: integer, bigint, smallint, character varying(n) (VARCHAR: DuckDB
 * does not enforce the length), text, real, double precision, numeric, timestamp, date, time.
 */
class expDuckDBSchema extends eZPgsqlSchema
{
    function schemaType()
    {
        return 'duckdb';
    }

    function schemaName()
    {
        return 'DuckDB';
    }

    /**
     * The sequences, the table (with its primary key) and the indexes of one table.
     */
    function generateTableArrays( $table, $table_def, $params, $withClosure = true )
    {
        $arrays = parent::generateTableArrays( $table, $table_def, $params, $withClosure );
        foreach ( $arrays['sequences'] as $i => $sql )
            $arrays['sequences'][$i] = self::withoutCache( $sql );
        foreach ( $arrays['indexes'] as $i => $sql )
            $arrays['indexes'][$i] = self::withoutUsing( $sql );

        // the primary key into CREATE TABLE
        $primary = null;
        foreach ( $table_def['indexes'] as $index_def )
            if ( $index_def['type'] === 'primary' )
                $primary = $index_def;
        if ( $primary !== null && $arrays['tables'] )
        {
            $spacing = !empty( $params['compatible_sql'] ) ? '    ' : '  ';
            $create = $arrays['tables'][0];
            $close = strrpos( $create, "\n)" );
            if ( $close !== false )
                $arrays['tables'][0] = substr( $create, 0, $close ) . ",\n" . $spacing . 'PRIMARY KEY ( ' . $this->indexColumnList( $primary ) . ' )' . substr( $create, $close );
        }
        $arrays['constraints'] = array();
        return $arrays;
    }

    /**
     * The columns of an index, quoted where they are reserved words.
     *
     * @param array $index_def
     * @return string
     */
    function indexColumnList( array $index_def )
    {
        $names = array();
        foreach ( $index_def['fields'] as $field )
        {
            $name = is_array( $field ) ? $field['name'] : $field;
            $names[] = in_array( $name, $this->reservedKeywordList() ) ? '"' . $name . '"' : $name;
        }
        return implode( ', ', $names );
    }

    /**
     * An index's name in the database. Index names are unique per schema in DuckDB, as in PostgreSQL, while MySQL
     * names them per table: extension schemas written for MySQL give indexes of different tables one name. A name
     * another table's index has already becomes <table>__<index> (the rule eZPgsqlSchema uses), so those tables are
     * made all the same. Only a connected DuckDB database is asked; a dump made elsewhere keeps the names.
     */
    function physicalIndexName( $table, $index )
    {
        if ( strpos( $index, $table . '_' ) === 0 || !$this->DBInstance instanceof expDuckDBDB || !$this->DBInstance->isConnected() )
            return $index;
        $owner = $this->DBInstance->arrayQuery( "SELECT table_name FROM duckdb_indexes() WHERE schema_name = 'main' AND index_name = '"
                                                . $this->DBInstance->escapeString( $index ) . "'" );
        if ( empty( $owner ) || $owner[0]['table_name'] === $table )
            return $index;
        return $table . '__' . $index;
    }

    function generateAddIndexSql( $table_name, $index_name, $def, $params, $withClosure = true )
    {
        if ( $def['type'] === 'primary' )
            return "ALTER TABLE $table_name ADD PRIMARY KEY ( " . $this->indexColumnList( $def ) . ' )' . ( $withClosure ? ";\n" : '' );
        return self::withoutUsing( parent::generateAddIndexSql( $table_name, $index_name, $def, $params, $withClosure ) );
    }

    /**
     * The rows of a table, without PostgreSQL's setval() (see the class).
     */
    function generateTableInsertSQLList( $tableName, $tableDef, $dataEntries, $params, $withClosure = true )
    {
        return eZDBSchemaInterface::generateTableInsertSQLList( $tableName, $tableDef, $dataEntries, $params, $withClosure );
    }

    /**
     * A file of the whole schema (kernel/sql/duckdb/kernel_schema.sql): per table its sequences, the table and its
     * indexes, in that order.
     */
    function generateSchemaFile( $schema, $params = array() )
    {
        $parts = array();
        foreach ( $schema as $table => $tableDef )
        {
            if ( $table === '_info' )
                continue;
            $arrays = $this->generateTableArrays( $table, $tableDef, $params, true );
            $parts[] = implode( "\n", array_merge( $arrays['sequences'], $arrays['tables'], $arrays['indexes'] ) );
        }
        return implode( "\n\n", $parts ) . "\n";
    }

    /**
     * CREATE SEQUENCE without CACHE.
     *
     * @param string $sql
     * @return string
     */
    static function withoutCache( $sql )
    {
        return preg_replace( '/\s+CACHE\s+\d+/', '', (string)$sql );
    }

    /**
     * CREATE INDEX without USING btree.
     *
     * @param string $sql
     * @return string
     */
    static function withoutUsing( $sql )
    {
        return preg_replace( '/\s+USING\s+btree\b/i', '', (string)$sql );
    }

    // ------------------------------------------------------------------ reading a database back

    function schema( $params = array() )
    {
        $params = array_merge( array( 'meta_data' => false, 'format' => 'generic', 'sort_columns' => true, 'sort_indexes' => true ), $params );
        if ( $this->Schema === false )
        {
            $schema = array();
            $tables = $this->DBInstance->arrayQuery( "SELECT table_name FROM duckdb_tables() WHERE schema_name = 'main' AND NOT temporary AND NOT internal ORDER BY table_name" );
            foreach ( (array)$tables as $row )
            {
                $table = $row['table_name'];
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
        $rows = $this->DBInstance->arrayQuery( "SELECT column_name, data_type, is_nullable, column_default, character_maximum_length, numeric_precision, numeric_scale
                                                FROM duckdb_columns() WHERE schema_name = 'main' AND table_name = '" . $this->DBInstance->escapeString( $table ) . "'
                                                ORDER BY column_index" );
        foreach ( (array)$rows as $row )
        {
            $default = $row['column_default'];
            $notNull = in_array( strtolower( (string)$row['is_nullable'] ), array( '0', 'false' ), true );
            $field = array();
            if ( $default !== null && preg_match( "/^nextval\\(/i", $default ) )
            {
                $field['type'] = 'auto_increment';
                $field['default'] = false;
            }
            else
            {
                $length = null;
                $field['type'] = self::standardType( $row['data_type'], $length );
                if ( $field['type'] === 'decimal' && $row['numeric_precision'] !== null )
                    $length = $row['numeric_precision'] . ',' . $row['numeric_scale'];
                if ( $field['type'] === 'varchar' && $row['character_maximum_length'] !== null )
                    $length = (int)$row['character_maximum_length'];
                if ( $length !== null )
                    $field['length'] = $length;
                $field['not_null'] = $notNull ? '1' : 0;
                $field['default'] = $default === null ? ( $notNull ? false : null ) : self::defaultValue( $default );
            }
            $fields[$row['column_name']] = $field;
        }
        if ( $params['sort_columns'] )
            ksort( $fields );
        return $fields;
    }

    /**
     * The standard (.dba) type of a DuckDB column type.
     *
     * @param string $type
     * @param mixed $length set for the integer types (their display width in .dba files)
     * @return string
     */
    static function standardType( $type, &$length )
    {
        $type = strtoupper( trim( (string)$type ) );
        $base = preg_replace( '/\(.*$/', '', $type );
        switch ( $base )
        {
            case 'BIGINT':
            case 'HUGEINT':
                $length = 20;
                return 'bigint';
            case 'INTEGER':
            case 'UINTEGER':
                $length = 11;
                return 'int';
            case 'SMALLINT':
            case 'TINYINT':
            case 'USMALLINT':
            case 'UTINYINT':
            case 'BOOLEAN':
                $length = 6;
                return 'int';
            case 'VARCHAR':
                return 'varchar';
            case 'TEXT':
            case 'BLOB':
                return 'longtext';
            case 'DOUBLE':
                return 'double';
            case 'FLOAT':
            case 'REAL':
                return 'float';
            case 'DECIMAL':
            case 'NUMERIC':
                return 'decimal';
            case 'TIMESTAMP':
            case 'TIMESTAMP WITH TIME ZONE':
            case 'DATETIME':
                return 'timestamp';
            case 'DATE':
                return 'date';
            case 'TIME':
                return 'time';
        }
        return 'longtext';
    }

    /**
     * The value of a DuckDB column default ('abc', 0, CAST('x' AS VARCHAR), -1.5 ...).
     *
     * @param string $default
     * @return string|null
     */
    static function defaultValue( $default )
    {
        $default = trim( (string)$default );
        if ( strtoupper( $default ) === 'NULL' || preg_match( '/^CAST\(\s*NULL\s+AS\s+/i', $default ) )
            return null;
        if ( preg_match( "/^CAST\\(\\s*('(?:[^']|'')*'|[-0-9.e+]+)\\s+AS\\s+[A-Z ()0-9,]+\\)$/i", $default, $m ) )
            $default = $m[1];
        if ( preg_match( "/^'((?:[^']|'')*)'$/s", $default, $m ) )
            return str_replace( "''", "'", $m[1] );
        return $default;
    }

    function fetchTableIndexes( $table, $params )
    {
        $indexes = array();
        $escaped = $this->DBInstance->escapeString( $table );
        $keys = $this->DBInstance->arrayQuery( "SELECT constraint_type, CAST( constraint_column_names AS VARCHAR ) AS constraint_column_names FROM duckdb_constraints()
                                                WHERE schema_name = 'main' AND table_name = '$escaped' AND constraint_type IN ( 'PRIMARY KEY' )" );
        foreach ( (array)$keys as $row )
        {
            $columns = self::listValue( $row['constraint_column_names'] );
            if ( $columns )
                $indexes['PRIMARY'] = array( 'type' => 'primary', 'fields' => $columns );
        }
        $rows = $this->DBInstance->arrayQuery( "SELECT index_name, is_unique, CAST( expressions AS VARCHAR ) AS expressions FROM duckdb_indexes()
                                                WHERE schema_name = 'main' AND table_name = '$escaped' ORDER BY index_name" );
        foreach ( (array)$rows as $row )
        {
            $fields = array();
            foreach ( self::listValue( $row['expressions'] ) as $expression )
                $fields[] = trim( $expression, '"' );
            $indexes[$row['index_name']] = array( 'type' => in_array( strtolower( (string)$row['is_unique'] ), array( '1', 'true' ), true ) ? 'unique' : 'non-unique',
                                                  'fields' => $fields );
        }
        if ( $params['sort_indexes'] )
            ksort( $indexes );
        return $indexes;
    }

    /**
     * A DuckDB list as the driver returns it ([a, b] or ['a', 'b']) as an array of strings.
     *
     * @param string|null $text
     * @return string[]
     */
    static function listValue( $text )
    {
        $text = trim( (string)$text );
        if ( $text === '' || $text === '[]' )
            return array();
        $text = trim( $text, '[]' );
        $out = array();
        foreach ( explode( ',', $text ) as $part )
        {
            $part = trim( $part );
            $part = trim( $part, "'" );
            if ( $part !== '' )
                $out[] = $part;
        }
        return $out;
    }
}
