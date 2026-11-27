<?php
/**
 * File containing the expMonetDBSchema class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The schema handler of MonetDB (expMonetDBDB): the database-independent schema (.dba) as MonetDB DDL, and a MonetDB
 * schema read back into that schema (for the upgrade check, the data tables and ezsqldumpschema.php).
 *
 * MonetDB takes PostgreSQL's type names and most of its DDL, so this is eZPgsqlSchema with these differences:
 *
 * - A serial column reads its sequence with DEFAULT NEXT VALUE FOR <table>_<column>_seq (CREATE SEQUENCE ... AS INTEGER
 *   START WITH 1); MonetDB reports the id an INSERT took from it, which is what expMonetDBDB::lastSerialID() returns.
 * - The primary key is part of CREATE TABLE.
 * - A unique index is a UNIQUE constraint: MonetDB accepts CREATE UNIQUE INDEX but does not enforce it.
 * - Defaults are plain literals: MonetDB has no :: casts.
 * - Index names are unique per schema: an index name another table has already becomes <table>__<index>.
 * - No setval() after the data: expMonetDBDB::correctSequenceValues() restarts the sequences after the data, as
 *   eZDBSchemaInterface::insertSchema() calls it.
 * - BLOB instead of bytea; several rows per INSERT.
 *
 * @since 6.0.15
 * @package lib
 */
class expMonetDBSchema extends eZPgsqlSchema
{
    function schemaType()
    {
        return 'monetdb';
    }

    function schemaName()
    {
        return 'MonetDB';
    }

    function isMultiInsertSupported()
    {
        return true;
    }

    function wireProfile( $params = array() )
    {
        return null;
    }

    /**
     * The sequences, the table (with its primary key and unique constraints) and the indexes of one table.
     */
    function generateTableArrays( $table, $table_def, $params, $withClosure = true )
    {
        $spacing = !empty( $params['compatible_sql'] ) ? '    ' : '  ';
        $arrays = array( 'sequences' => array(), 'tables' => array(), 'indexes' => array(), 'constraints' => array() );
        foreach ( $table_def['fields'] as $field_name => $field_def )
            if ( $field_def['type'] == 'auto_increment' )
                $arrays['sequences'][] = "CREATE SEQUENCE {$table}_{$field_name}_seq AS INTEGER START WITH 1" . ( $withClosure ? ';' : '' );

        $lines = array();
        foreach ( $table_def['fields'] as $field_name => $field_def )
            $lines[] = $spacing . $this->generateFieldDef( $table, $field_name, $field_def, true, $params );
        $indexes = isset( $table_def['indexes'] ) ? $table_def['indexes'] : array();
        foreach ( $indexes as $index_name => $index_def )
            if ( $index_def['type'] === 'primary' )
                $lines[] = $spacing . 'PRIMARY KEY ( ' . $this->indexColumnList( $index_def ) . ' )';
        foreach ( $indexes as $index_name => $index_def )
            if ( $index_def['type'] === 'unique' )
                $lines[] = $spacing . 'CONSTRAINT ' . $this->physicalIndexName( $table, $index_name ) . ' UNIQUE ( ' . $this->indexColumnList( $index_def ) . ' )';
        $arrays['tables'][] = "CREATE TABLE IF NOT EXISTS $table (\n" . implode( ",\n", $lines ) . ( $withClosure ? "\n);" : "\n)" );

        foreach ( $indexes as $index_name => $index_def )
            if ( $index_def['type'] === 'non-unique' )
                $arrays['indexes'][] = $this->generateAddIndexSql( $table, $index_name, $index_def, $params, $withClosure );
        return $arrays;
    }

    /**
     * A column: PostgreSQL's type names, NEXT VALUE FOR a serial, defaults without casts.
     */
    function generateFieldDef( $table_name, $field_name, $def, $add_default_not_null = true, $params = array() )
    {
        $sql = $this->quoteSchemaIdentifier( $field_name ) . ' ';
        if ( $def['type'] == 'auto_increment' )
            return $sql . "integer DEFAULT NEXT VALUE FOR {$table_name}_{$field_name}_seq NOT NULL";
        $length = isset( $def['length'] ) ? $def['length'] : null;
        $type = self::columnType( $def['type'], $length );
        $sql .= $type;
        if ( $add_default_not_null )
        {
            $default = $this->generateDefaultDef( false, false, $def, $params );
            if ( $default )
                $sql .= ' ' . rtrim( $default );
            if ( !empty( $def['not_null'] ) )
                $sql .= ' NOT NULL';
        }
        return $sql;
    }

    /**
     * The MonetDB type of a standard (.dba) type, with its length where the type takes one (pure).
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
                return 'integer';
            case 'tinyint':
            case 'smallint':
                return 'smallint';
            case 'bigint':
                return 'bigint';
            case 'varchar':
                return 'character varying(' . ( $length ? (int)$length : 255 ) . ')';
            case 'char':
                return 'character(' . ( $length ? (int)$length : 1 ) . ')';
            case 'text':
            case 'mediumtext':
            case 'longtext':
                return 'text';
            case 'blob':
            case 'mediumblob':
            case 'longblob':
                return 'blob';
            case 'float':
                return 'real';
            case 'double':
                return 'double precision';
            case 'decimal':
                return 'numeric' . ( $length ? '(' . preg_replace( '/[^0-9,]/', '', (string)$length ) . ')' : '' );
            case 'timestamp':
            case 'datetime':
                return 'timestamp';
            case 'date':
                return 'date';
            case 'time':
                return 'time';
        }
        throw new RuntimeException( "Unhandled column type: $type" );
    }

    /**
     * DEFAULT <literal>, without PostgreSQL's casts; nothing for a MySQL-style '' default of a number or date column.
     */
    function generateDefaultDef( $table_name, $field_name, $def, $params )
    {
        if ( !array_key_exists( 'default', $def ) || $def['default'] === false )
            return $table_name && $field_name ? false : '';
        if ( $def['default'] === null )
            return $table_name && $field_name ? "ALTER TABLE $table_name ALTER $field_name SET DEFAULT NULL" : 'DEFAULT NULL';
        $numeric = in_array( $def['type'], array( 'int', 'bigint', 'smallint', 'tinyint', 'mediumint', 'decimal', 'float', 'double' ), true );
        if ( $def['default'] === '' && ( $numeric || in_array( $def['type'], array( 'timestamp', 'datetime', 'date', 'time' ), true ) ) )
            return $table_name && $field_name ? false : '';
        $value = $numeric && is_numeric( $def['default'] ) ? (string)$def['default'] : "'" . expMonetDBDialect::escape( $def['default'] ) . "'";
        return ( $table_name && $field_name ? "ALTER TABLE $table_name ALTER " . $this->quoteSchemaIdentifier( $field_name ) . ' SET ' : '' ) . 'DEFAULT ' . $value;
    }

    function generateAddFieldSql( $table_name, $field_name, $def, $params )
    {
        return "ALTER TABLE $table_name ADD COLUMN " . $this->generateFieldDef( $table_name, $field_name, $def, true, $params ) . ";\n";
    }

    /**
     * A column changed. MonetDB cannot change a column's type in place (no ALTER ... TYPE): the column is copied into a
     * new one of the new type, dropped, and the new one renamed. That fails for a column of a key or an index (drop and
     * make those around it); NOT NULL and the default are set afterwards.
     */
    function generateAlterFieldSql( $table_name, $field_name, $def, $params )
    {
        $name = $this->quoteSchemaIdentifier( $field_name );
        $length = isset( $def['length'] ) ? $def['length'] : null;
        $sql = array();
        if ( $def['type'] !== 'auto_increment' )
        {
            $type = self::columnType( $def['type'], $length );
            $copy = $this->quoteSchemaIdentifier( $field_name . '__new' );
            $sql[] = "ALTER TABLE $table_name ADD COLUMN $copy $type;";
            $sql[] = "UPDATE $table_name SET $copy = CAST( $name AS $type );";
            $sql[] = "ALTER TABLE $table_name DROP COLUMN $name;";
            $sql[] = "ALTER TABLE $table_name RENAME COLUMN $copy TO $name;";
        }
        $default = $this->generateDefaultDef( $table_name, $field_name, $def, $params );
        if ( $default )
            $sql[] = $default . ';';
        $sql[] = "ALTER TABLE $table_name ALTER $name " . ( !empty( $def['not_null'] ) ? 'SET NOT NULL' : 'SET NULL' ) . ';';
        return implode( "\n", $sql ) . "\n";
    }

    /**
     * The columns of an index, quoted where they are reserved words.
     */
    function indexColumnList( array $index_def )
    {
        $names = array();
        foreach ( $index_def['fields'] as $field )
            $names[] = $this->quoteSchemaIdentifier( is_array( $field ) ? $field['name'] : $field );
        return implode( ', ', $names );
    }

    /**
     * Names PostgreSQL and MonetDB reserve, quoted (unquoted names fold to lower case: the kernel's are).
     */
    function quoteSchemaIdentifier( $name )
    {
        if ( in_array( strtolower( $name ), array_keys( expMonetDBDialect::RESERVED_COLUMNS ), true ) || in_array( $name, $this->reservedKeywordList() ) )
            return '"' . str_replace( '"', '""', $name ) . '"';
        return $name;
    }

    /**
     * An index's (or unique constraint's) name in the database: unique per schema in MonetDB. A name another
     * table's index has already becomes <table>__<index>. Only a connected MonetDB database is asked.
     */
    function physicalIndexName( $table, $index )
    {
        if ( strpos( $index, $table . '_' ) === 0 || !$this->DBInstance instanceof expMonetDBDB || !$this->DBInstance->isConnected() )
            return $index;
        $owner = $this->DBInstance->arrayQuery( "SELECT t.name AS table_name FROM sys.idxs i JOIN sys.tables t ON t.id = i.table_id
                                                 WHERE t.schema_id = (SELECT id FROM sys.schemas WHERE name = CURRENT_SCHEMA)
                                                 AND i.name = '" . $this->DBInstance->escapeString( $index ) . "'" );
        if ( empty( $owner ) || $owner[0]['table_name'] === $table )
            return $index;
        return $table . '__' . $index;
    }

    function generateAddIndexSql( $table_name, $index_name, $def, $params, $withClosure = true )
    {
        $columns = $this->indexColumnList( $def );
        switch ( $def['type'] )
        {
            case 'primary':
                $sql = "ALTER TABLE $table_name ADD PRIMARY KEY ( $columns )";
                break;
            case 'unique':
                $sql = "ALTER TABLE $table_name ADD CONSTRAINT " . $this->physicalIndexName( $table_name, $index_name ) . " UNIQUE ( $columns )";
                break;
            default:
                $sql = 'CREATE INDEX ' . $this->physicalIndexName( $table_name, $index_name ) . " ON $table_name ( $columns )";
        }
        return $sql . ( $withClosure ? ";\n" : '' );
    }

    function generateDropIndexSql( $table_name, $index_name, $def, $withClosure = true )
    {
        if ( $def['type'] === 'primary' )
            $sql = "ALTER TABLE $table_name DROP CONSTRAINT " . $this->primaryKeyName( $table_name );
        elseif ( $def['type'] === 'unique' )
            $sql = "ALTER TABLE $table_name DROP CONSTRAINT " . $this->physicalIndexName( $table_name, $index_name );
        else
            $sql = 'DROP INDEX ' . $this->physicalIndexName( $table_name, $index_name );
        return $sql . ( $withClosure ? ";\n" : '' );
    }

    /**
     * The name MonetDB gives a primary key in CREATE TABLE: <table>_<first column>_pkey; read from the catalogue when
     * connected.
     */
    function primaryKeyName( $table )
    {
        if ( $this->DBInstance instanceof expMonetDBDB && $this->DBInstance->isConnected() )
        {
            $rows = $this->DBInstance->arrayQuery( "SELECT k.name FROM sys.keys k JOIN sys.tables t ON t.id = k.table_id
                                                    WHERE t.schema_id = (SELECT id FROM sys.schemas WHERE name = CURRENT_SCHEMA)
                                                    AND t.name = '" . $this->DBInstance->escapeString( $table ) . "' AND k.type = 0" );
            if ( !empty( $rows ) )
                return $rows[0]['name'];
        }
        return $table . '_pkey';
    }

    /**
     * The rows of a table, without PostgreSQL's setval() (see the class).
     */
    function generateTableInsertSQLList( $tableName, $tableDef, $dataEntries, $params, $withClosure = true )
    {
        return eZDBSchemaInterface::generateTableInsertSQLList( $tableName, $tableDef, $dataEntries, $params + array( 'allow_multi_insert' => true ), $withClosure );
    }

    function escapeSQLString( $value )
    {
        return expMonetDBDialect::escape( $value );
    }

    /**
     * A file of the whole schema (kernel/sql/monetdb/kernel_schema.sql): per table its sequences, the table and its
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

    function generateDropTable( $table )
    {
        return "DROP TABLE $table CASCADE;\n";
    }

    // ------------------------------------------------------------------ reading a database back

    function schema( $params = array() )
    {
        $params = array_merge( array( 'meta_data' => false, 'format' => 'generic', 'sort_columns' => true, 'sort_indexes' => true ), $params );
        if ( $this->Schema === false )
        {
            $schema = array();
            $tables = $this->DBInstance->arrayQuery( "SELECT name FROM sys.tables WHERE schema_id = (SELECT id FROM sys.schemas WHERE name = CURRENT_SCHEMA)
                                                      AND NOT system AND type = 0 ORDER BY name" );
            foreach ( (array)$tables as $row )
            {
                $table = $row['name'];
                // the driver's write lock is no table of the site
                if ( $table === expMonetDBDB::LOCK_TABLE )
                    continue;
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
        $rows = $this->DBInstance->arrayQuery( "SELECT c.name, c.type, c.type_digits, c.type_scale, c.\"null\" AS nullable, c.\"default\" AS def
                                                FROM sys.columns c JOIN sys.tables t ON t.id = c.table_id
                                                WHERE t.schema_id = (SELECT id FROM sys.schemas WHERE name = CURRENT_SCHEMA)
                                                AND t.name = '" . $this->DBInstance->escapeString( $table ) . "' ORDER BY c.number" );
        foreach ( (array)$rows as $row )
        {
            $default = $row['def'];
            $notNull = $row['nullable'] === '0';
            $field = array();
            if ( $default !== null && stripos( $default, 'next value for' ) === 0 )
            {
                $field['type'] = 'auto_increment';
                $field['default'] = false;
            }
            else
            {
                $length = null;
                $field['type'] = self::standardType( $row['type'], (int)$row['type_digits'], (int)$row['type_scale'], $length );
                if ( $length !== null )
                    $field['length'] = $length;
                $field['not_null'] = $notNull ? '1' : 0;
                $field['default'] = $default === null ? ( $notNull ? false : null ) : self::defaultValue( $default );
            }
            $fields[$row['name']] = $field;
        }
        if ( $params['sort_columns'] )
            ksort( $fields );
        return $fields;
    }

    /**
     * The standard (.dba) type of a MonetDB column type (pure).
     *
     * @param string $type sys.columns.type
     * @param int $digits type_digits
     * @param int $scale type_scale
     * @param mixed $length the length of the .dba type, or null
     * @return string
     */
    static function standardType( $type, $digits, $scale, &$length )
    {
        switch ( strtolower( (string)$type ) )
        {
            case 'bigint':
            case 'hugeint':
                $length = 20;
                return 'bigint';
            case 'int':
                $length = 11;
                return 'int';
            case 'smallint':
            case 'tinyint':
            case 'boolean':
                $length = 6;
                return 'int';
            case 'varchar':
                $length = $digits;
                return 'varchar';
            case 'char':
                $length = $digits;
                return 'char';
            case 'clob':
            case 'blob':
                return 'longtext';
            case 'double':
                return 'double';
            case 'real':
                return 'float';
            case 'decimal':
                $length = $digits . ',' . $scale;
                return 'decimal';
            case 'timestamp':
            case 'timestamptz':
                return 'timestamp';
            case 'date':
                return 'date';
            case 'time':
            case 'timetz':
                return 'time';
        }
        return 'longtext';
    }

    /**
     * The value of a MonetDB column default ('abc', 0, NULL) (pure).
     *
     * @param string $default
     * @return string|null
     */
    static function defaultValue( $default )
    {
        $default = trim( (string)$default );
        if ( strtoupper( $default ) === 'NULL' )
            return null;
        if ( preg_match( "/^'((?:[^'\\\\]|''|\\\\.)*)'$/s", $default, $m ) )
            return str_replace( array( "''", '\\\\' ), array( "'", '\\' ), $m[1] );
        return $default;
    }

    function fetchTableIndexes( $table, $params )
    {
        $indexes = array();
        $escaped = $this->DBInstance->escapeString( $table );
        $where = "t.schema_id = (SELECT id FROM sys.schemas WHERE name = CURRENT_SCHEMA) AND t.name = '$escaped'";
        $keys = $this->DBInstance->arrayQuery( "SELECT k.name AS key_name, k.type AS key_type, o.name AS column_name, o.nr
                                                FROM sys.keys k JOIN sys.tables t ON t.id = k.table_id JOIN sys.objects o ON o.id = k.id
                                                WHERE $where ORDER BY k.name, o.nr" );
        $keyNames = array();
        foreach ( (array)$keys as $row )
        {
            $keyNames[$row['key_name']] = true;
            if ( $row['key_type'] === '0' )
            {
                if ( !isset( $indexes['PRIMARY'] ) )
                    $indexes['PRIMARY'] = array( 'type' => 'primary', 'fields' => array() );
                $indexes['PRIMARY']['fields'][] = $row['column_name'];
            }
            elseif ( $row['key_type'] === '1' )
            {
                $name = strpos( $row['key_name'], $table . '__' ) === 0 ? substr( $row['key_name'], strlen( $table ) + 2 ) : $row['key_name'];
                if ( !isset( $indexes[$name] ) )
                    $indexes[$name] = array( 'type' => 'unique', 'fields' => array() );
                $indexes[$name]['fields'][] = $row['column_name'];
            }
        }
        $rows = $this->DBInstance->arrayQuery( "SELECT i.name AS index_name, o.name AS column_name, o.nr
                                                FROM sys.idxs i JOIN sys.tables t ON t.id = i.table_id JOIN sys.objects o ON o.id = i.id
                                                WHERE $where ORDER BY i.name, o.nr" );
        foreach ( (array)$rows as $row )
        {
            if ( isset( $keyNames[$row['index_name']] ) )
                continue;
            $name = strpos( $row['index_name'], $table . '__' ) === 0 ? substr( $row['index_name'], strlen( $table ) + 2 ) : $row['index_name'];
            if ( !isset( $indexes[$name] ) )
                $indexes[$name] = array( 'type' => 'non-unique', 'fields' => array() );
            $indexes[$name]['fields'][] = $row['column_name'];
        }
        if ( $params['sort_indexes'] )
            ksort( $indexes );
        return $indexes;
    }
}
