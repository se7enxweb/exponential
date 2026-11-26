<?php
/**
 * File containing the expSQLServerSchema class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The schema handler of Microsoft SQL Server (expSQLServerDB): the database-independent schema (.dba) as SQL Server
 * DDL, and a SQL Server database read back into that schema (for the upgrade check and ezsqldumpschema.php).
 *
 * Types: int INT, bigint BIGINT, tinyint/smallint SMALLINT (SQL Server's TINYINT has no negative numbers),
 * auto_increment INT IDENTITY(1,1), varchar(n) NVARCHAR(n) (NVARCHAR(MAX) past 4,000), char(n) NCHAR(n), text and
 * longtext NVARCHAR(MAX), float REAL, double FLOAT, decimal DECIMAL, timestamp/datetime DATETIME2(0), date DATE,
 * time TIME(0), blob/longblob VARBINARY(MAX). Text is NVARCHAR throughout: Unicode whatever the collation.
 *
 * Names: a primary key is the constraint PK_<table>, a column default DF_<table>_<column> (so a column can be
 * dropped: SQL Server refuses to drop a column while a default constraint is on it, and a default it named itself
 * has a random name). Index names are unique per table, as in MySQL.
 *
 * Index keys: SQL Server indexes at most 900 bytes of a clustered key and 1,700 of a nonclustered one, and no
 * NVARCHAR(MAX) column at all. MySQL's prefix lengths (mysql:length) do not exist here. So a MAX column of an index
 * and, of a non-unique index, every column past 1,700 bytes go into INCLUDE (...) (still in the index, not in its
 * key); an index left without key columns is not made; a primary key past 900 bytes is NONCLUSTERED. A unique index
 * past 1,700 bytes keeps its columns (a longer value is refused on insert, error 1946) and is reported.
 */
class expSQLServerSchema extends eZDBSchemaInterface
{
    /** Largest key of a clustered index (the primary key), in bytes */
    const MAX_CLUSTERED_KEY_BYTES = 900;

    /** Largest key of a nonclustered index, in bytes */
    const MAX_NONCLUSTERED_KEY_BYTES = 1700;

    /** Characters of a text column that is an index key: 1,680 bytes, within the 1,700 of a key with a few numbers */
    const INDEXED_TEXT_LENGTH = 840;

    /** @var string[] notes of the last generation (indexes changed to fit SQL Server's limits) */
    public $Notes = array();

    function schemaType()
    {
        return 'sqlserver';
    }

    function schemaName()
    {
        return 'Microsoft SQL Server';
    }

    // ------------------------------------------------------------------ names

    function quoteSchemaIdentifier( $name )
    {
        return expSQLServerDialect::isReservedWord( $name ) ? expSQLServerDialect::quoteIdentifier( $name ) : $name;
    }

    function insertColumnName( $name )
    {
        return $this->quoteSchemaIdentifier( $name );
    }

    function reservedKeywordList()
    {
        return array_map( 'strtolower', expSQLServerDialect::RESERVED_WORDS );
    }

    /**
     * @param string $table
     * @return string the primary key constraint's name
     */
    function primaryKeyName( $table )
    {
        return 'PK_' . $table;
    }

    /**
     * @param string $table
     * @param string $field
     * @return string the default constraint's name
     */
    function defaultConstraintName( $table, $field )
    {
        return 'DF_' . $table . '_' . $field;
    }

    // ------------------------------------------------------------------ types

    /**
     * The SQL Server type of a schema type.
     *
     * @param string $type
     * @param mixed $length
     * @return string
     * @throws RuntimeException for a type the schema does not know
     */
    function convertFromStandardType( $type, $length = null )
    {
        switch ( $type )
        {
            case 'int':
            case 'mediumint':
                return 'INT';
            case 'bigint':
                return 'BIGINT';
            case 'tinyint':
            case 'smallint':
                return 'SMALLINT';
            case 'auto_increment':
                return 'INT IDENTITY(1,1)';
            case 'varchar':
                $n = (int)$length > 0 ? (int)$length : 255;
                return $n > expSQLServerDialect::MAX_NVARCHAR_LENGTH ? 'NVARCHAR(MAX)' : "NVARCHAR($n)";
            case 'char':
                $n = (int)$length > 0 ? (int)$length : 1;
                return $n > expSQLServerDialect::MAX_NVARCHAR_LENGTH ? 'NVARCHAR(MAX)' : "NCHAR($n)";
            case 'tinytext':
            case 'text':
            case 'mediumtext':
            case 'longtext':
                return 'NVARCHAR(MAX)';
            case 'float':
                return 'REAL';
            case 'double':
                return 'FLOAT';
            case 'decimal':
                return 'DECIMAL(' . ( preg_match( '/^\s*\d+\s*(,\s*\d+\s*)?$/', (string)$length ) ? preg_replace( '/\s+/', '', (string)$length ) : '18,0' ) . ')';
            case 'timestamp':
            case 'datetime':
                return 'DATETIME2(0)';
            case 'date':
                return 'DATE';
            case 'time':
                return 'TIME(0)';
            case 'blob':
            case 'mediumblob':
            case 'longblob':
                return 'VARBINARY(MAX)';
        }
        throw new RuntimeException( "Unhandled column type: $type" );
    }

    /**
     * The schema type of a SQL Server column (as sys.columns describes it).
     *
     * @param string $type sys.types name
     * @param int $maxLength bytes, -1 for MAX
     * @param int $precision
     * @param int $scale
     * @param mixed $length set to the schema length
     * @return string
     * @throws RuntimeException for a type the schema cannot describe
     */
    function convertToStandardType( $type, $maxLength, $precision, $scale, &$length )
    {
        $length = null;
        $maxLength = (int)$maxLength;
        switch ( strtolower( $type ) )
        {
            case 'int':
                $length = 11;
                return 'int';
            case 'bigint':
                $length = 20;
                return 'bigint';
            case 'smallint':
            case 'tinyint':
            case 'bit':
                $length = 6;
                return 'int';
            case 'nvarchar':
            case 'nchar':
                if ( $maxLength === -1 || ( strtolower( $type ) === 'nvarchar' && $maxLength === 2 * self::INDEXED_TEXT_LENGTH ) )
                    return 'longtext';
                $length = intdiv( $maxLength, 2 );
                return strtolower( $type ) === 'nchar' ? 'char' : 'varchar';
            case 'varchar':
            case 'char':
                if ( $maxLength === -1 )
                    return 'longtext';
                $length = $maxLength;
                return strtolower( $type );
            case 'ntext':
            case 'text':
                return 'longtext';
            case 'real':
                return 'float';
            case 'float':
                return 'double';
            case 'decimal':
            case 'numeric':
            case 'money':
                $length = (int)$precision . ',' . (int)$scale;
                return 'decimal';
            case 'datetime2':
            case 'datetime':
            case 'smalldatetime':
                return 'timestamp';
            case 'date':
                return 'date';
            case 'time':
                return 'time';
            case 'varbinary':
            case 'binary':
            case 'image':
                return 'longblob';
        }
        throw new RuntimeException( "Unhandled column type: $type" );
    }

    /**
     * Bytes a column takes at most in an index key; null for a column that cannot be a key (MAX types).
     *
     * @param array $def
     * @return int|null
     */
    function keyBytes( array $def )
    {
        $sqlType = $this->convertFromStandardType( $def['type'], isset( $def['length'] ) ? $def['length'] : null );
        if ( strpos( $sqlType, '(MAX)' ) !== false )
            return null;
        if ( preg_match( '/^N(?:VAR)?CHAR\((\d+)\)$/', $sqlType, $m ) )
            return 2 * (int)$m[1];
        $sizes = array( 'INT' => 4, 'BIGINT' => 8, 'SMALLINT' => 2, 'INT IDENTITY(1,1)' => 4, 'REAL' => 4, 'FLOAT' => 8,
                        'DATETIME2(0)' => 6, 'DATE' => 3, 'TIME(0)' => 3 );
        if ( isset( $sizes[$sqlType] ) )
            return $sizes[$sqlType];
        if ( strpos( $sqlType, 'DECIMAL' ) === 0 )
            return 17;
        return 8;
    }

    // ------------------------------------------------------------------ DDL

    /**
     * A literal for a column default (or a data value) of a schema type.
     *
     * @param array $def
     * @param mixed $value
     * @return string
     */
    function literalFor( array $def, $value )
    {
        if ( $value === null || $value === false )
            return 'NULL';
        switch ( $def['type'] )
        {
            case 'int':
            case 'bigint':
            case 'smallint':
            case 'tinyint':
            case 'mediumint':
            case 'auto_increment':
                return is_numeric( $value ) ? (string)(int)$value : ( $value === '' ? '0' : 'NULL' );
            case 'float':
            case 'double':
            case 'decimal':
                return is_numeric( $value ) ? expSQLServerDialect::literal( (float)$value ) : ( $value === '' ? '0' : 'NULL' );
            case 'blob':
            case 'mediumblob':
            case 'longblob':
                return expSQLServerDialect::binaryLiteral( (string)$value );
        }
        return expSQLServerDialect::stringLiteral( (string)$value );
    }

    /**
     * The DEFAULT clause of a column (with its constraint name when $table is given), or ''.
     *
     * @param string|false $table
     * @param string $field
     * @param array $def
     * @return string
     */
    function generateDefaultDef( $table, $field, $def )
    {
        if ( $def['type'] === 'auto_increment' || !array_key_exists( 'default', $def ) || $def['default'] === false )
            return '';
        // a NOT NULL number or date column of a MySQL schema reaches the schema with default '' (MySQL's implicit one)
        if ( $def['default'] === '' && in_array( $def['type'], array( 'timestamp', 'datetime', 'date', 'time' ), true ) )
            return '';
        if ( $def['default'] === null )
        {
            if ( !empty( $def['not_null'] ) )
                return '';
            $value = 'NULL';
        }
        else
            $value = $this->literalFor( $def, $def['default'] );
        $name = $table ? 'CONSTRAINT ' . expSQLServerDialect::quoteIdentifier( $this->defaultConstraintName( $table, $field ) ) . ' ' : '';
        return $name . 'DEFAULT ' . $value;
    }

    /**
     * A column definition: name, type, [CONSTRAINT DF_... DEFAULT ...], NULL / NOT NULL.
     *
     * @param string $table
     * @param string $field
     * @param array $def
     * @return string
     */
    function generateFieldDef( $table, $field, $def )
    {
        $sql = $this->quoteSchemaIdentifier( $field ) . ' ' . $this->convertFromStandardType( $def['type'], isset( $def['length'] ) ? $def['length'] : null );
        if ( $def['type'] === 'auto_increment' )
            return $sql . ' NOT NULL';
        $default = $this->generateDefaultDef( $table, $field, $def );
        if ( $default !== '' )
            $sql .= ' ' . $default;
        return $sql . ( !empty( $def['not_null'] ) ? ' NOT NULL' : ' NULL' );
    }

    /**
     * The columns of an index split into key columns and INCLUDE columns so that the key fits SQL Server's limits.
     *
     * @param string $table
     * @param string $index
     * @param array $indexDef
     * @param array $fields the table's field definitions
     * @return array( string[] key columns, string[] included columns, string[] notes )
     */
    function splitIndexColumns( $table, $index, array $indexDef, array $fields )
    {
        $limit = $indexDef['type'] === 'primary' ? self::MAX_NONCLUSTERED_KEY_BYTES : self::MAX_NONCLUSTERED_KEY_BYTES;
        $key = array();
        $include = array();
        $notes = array();
        $bytes = 0;
        foreach ( $indexDef['fields'] as $fieldDef )
        {
            $name = is_array( $fieldDef ) ? $fieldDef['name'] : $fieldDef;
            $size = isset( $fields[$name] ) ? $this->keyBytes( $fields[$name] ) : 8;
            if ( $size === null )
            {
                $include[] = $name;
                $notes[] = "$table.$index: $name is NVARCHAR(MAX) or VARBINARY(MAX), which cannot be an index key: it is an included column";
                continue;
            }
            if ( $indexDef['type'] === 'non-unique' && $bytes + $size > $limit )
            {
                $include[] = $name;
                $notes[] = "$table.$index: $name would take the key past $limit bytes: it is an included column";
                continue;
            }
            $bytes += $size;
            $key[] = $name;
        }
        if ( $indexDef['type'] !== 'non-unique' && $bytes > self::MAX_NONCLUSTERED_KEY_BYTES )
            $notes[] = "$table.$index: the key may take $bytes bytes, more than SQL Server's " . self::MAX_NONCLUSTERED_KEY_BYTES
                     . ': a row with longer values is refused (error 1946)';
        return array( $key, $include, $notes );
    }

    /**
     * @return string CREATE INDEX / ALTER TABLE ADD CONSTRAINT ... PRIMARY KEY, or '' when the index cannot be made
     */
    function generateAddIndexSql( $table, $index, $def, $params, $withClosure = true, $fields = array() )
    {
        // an upgrade adds an index to a table that exists: its column types come from the database
        if ( !$fields && $this->DBInstance instanceof expSQLServerDB && $this->DBInstance->isConnected() )
        {
            try
            {
                $fields = $this->fetchTableFields( $table, array( 'sort_columns' => false ) );
            }
            catch ( RuntimeException $e )
            {
                $fields = array();
            }
        }
        list( $key, $include, $notes ) = $this->splitIndexColumns( $table, $index, $def, $fields );
        $this->Notes = array_merge( $this->Notes, $notes );
        if ( !$key )
        {
            $this->Notes[] = "$table.$index: no column can be a key, the index is not made";
            return '';
        }
        $columns = implode( ', ', array_map( array( $this, 'quoteSchemaIdentifier' ), $key ) );
        if ( $def['type'] === 'primary' )
        {
            $bytes = 0;
            foreach ( $key as $name )
                $bytes += isset( $fields[$name] ) ? (int)$this->keyBytes( $fields[$name] ) : 8;
            $clustered = $bytes > self::MAX_CLUSTERED_KEY_BYTES ? ' NONCLUSTERED' : '';
            $sql = 'ALTER TABLE ' . $this->quoteSchemaIdentifier( $table ) . ' ADD CONSTRAINT '
                 . expSQLServerDialect::quoteIdentifier( $this->primaryKeyName( $table ) ) . " PRIMARY KEY$clustered ($columns)";
        }
        else
        {
            $sql = 'CREATE ' . ( $def['type'] === 'unique' ? 'UNIQUE ' : '' ) . 'INDEX ' . expSQLServerDialect::quoteIdentifier( $index )
                 . ' ON ' . $this->quoteSchemaIdentifier( $table ) . " ($columns)";
            if ( $include )
                $sql .= ' INCLUDE (' . implode( ', ', array_map( array( $this, 'quoteSchemaIdentifier' ), $include ) ) . ')';
        }
        return $sql . ( $withClosure ? ";\n" : '' );
    }

    function generateDropIndexSql( $table, $index, $def, $withClosure = true )
    {
        if ( $def['type'] === 'primary' )
            $sql = 'ALTER TABLE ' . $this->quoteSchemaIdentifier( $table ) . ' DROP CONSTRAINT ' . expSQLServerDialect::quoteIdentifier( $this->primaryKeyName( $table ) );
        else
            $sql = 'DROP INDEX ' . expSQLServerDialect::quoteIdentifier( $index ) . ' ON ' . $this->quoteSchemaIdentifier( $table );
        return $sql . ( $withClosure ? ";\n" : '' );
    }

    function generateAddFieldSql( $table, $field, $def, $params )
    {
        // a NOT NULL column with a default fills the rows the table already has with that default
        return 'ALTER TABLE ' . $this->quoteSchemaIdentifier( $table ) . ' ADD ' . $this->generateFieldDef( $table, $field, $def ) . ";\n";
    }

    function generateDropFieldSql( $table, $field, $params )
    {
        return 'ALTER TABLE ' . $this->quoteSchemaIdentifier( $table ) . ' DROP CONSTRAINT IF EXISTS '
             . expSQLServerDialect::quoteIdentifier( $this->defaultConstraintName( $table, $field ) ) . ";\n"
             . 'ALTER TABLE ' . $this->quoteSchemaIdentifier( $table ) . ' DROP COLUMN ' . $this->quoteSchemaIdentifier( $field ) . ";\n";
    }

    function generateAlterFieldSql( $table, $field, $def, $params )
    {
        $t = $this->quoteSchemaIdentifier( $table );
        $sql = "ALTER TABLE $t DROP CONSTRAINT IF EXISTS " . expSQLServerDialect::quoteIdentifier( $this->defaultConstraintName( $table, $field ) ) . ";\n";
        if ( $def['type'] !== 'auto_increment' )
        {
            $default = $this->generateDefaultDef( false, $field, $def );
            if ( !empty( $def['not_null'] ) && $default !== '' )
                $sql .= 'UPDATE ' . $t . ' SET ' . $this->quoteSchemaIdentifier( $field ) . ' = ' . substr( $default, 8 )
                      . ' WHERE ' . $this->quoteSchemaIdentifier( $field ) . " IS NULL;\n";
            $sql .= "ALTER TABLE $t ALTER COLUMN " . $this->quoteSchemaIdentifier( $field ) . ' '
                  . $this->convertFromStandardType( $def['type'], isset( $def['length'] ) ? $def['length'] : null )
                  . ( !empty( $def['not_null'] ) ? ' NOT NULL' : ' NULL' ) . ";\n";
            if ( $default !== '' )
                $sql .= "ALTER TABLE $t ADD CONSTRAINT " . expSQLServerDialect::quoteIdentifier( $this->defaultConstraintName( $table, $field ) )
                      . ' ' . $default . ' FOR ' . $this->quoteSchemaIdentifier( $field ) . ";\n";
        }
        return $sql;
    }

    /**
     * The statements of one table: array( 'tables' => ..., 'indexes' => ..., 'constraints' => ... ).
     */
    function generateTableArrays( $table, $tableDef, $params, $withClosure = true )
    {
        $tableDef['fields'] = $this->indexedTextFields( $table, $tableDef );
        $arrays = array( 'tables' => array(), 'indexes' => array(), 'constraints' => array() );
        $lines = array();
        foreach ( $tableDef['fields'] as $field => $def )
            $lines[] = '  ' . $this->generateFieldDef( $table, $field, $def );
        $arrays['tables'][] = 'CREATE TABLE ' . $this->quoteSchemaIdentifier( $table ) . " (\n" . implode( ",\n", $lines ) . "\n)" . ( $withClosure ? ';' : '' );
        $indexes = isset( $tableDef['indexes'] ) ? $tableDef['indexes'] : array();
        foreach ( $indexes as $index => $def )
        {
            $sql = $this->generateAddIndexSql( $table, $index, $def, $params, false, $tableDef['fields'] );
            if ( $sql === '' )
                continue;
            if ( $def['type'] === 'primary' )
                $arrays['constraints'][] = $sql . ( $withClosure ? ';' : '' );
            else
                $arrays['indexes'][] = $sql . ( $withClosure ? ';' : '' );
        }
        return $arrays;
    }

    /**
     * The fields of a table with every text column that is a key column of an index as NVARCHAR(840): an index
     * cannot have an NVARCHAR(MAX) key. Such a column holds at most 840 characters on SQL Server (a longer value is
     * refused, error 2628); the URL alias text, path strings and image paths of the kernel are far shorter.
     *
     * @param string $table
     * @param array $tableDef
     * @return array the fields
     */
    function indexedTextFields( $table, array $tableDef )
    {
        $fields = $tableDef['fields'];
        foreach ( isset( $tableDef['indexes'] ) ? $tableDef['indexes'] : array() as $index => $def )
            foreach ( $def['fields'] as $fieldDef )
            {
                $name = is_array( $fieldDef ) ? $fieldDef['name'] : $fieldDef;
                if ( isset( $fields[$name] ) && in_array( $fields[$name]['type'], array( 'tinytext', 'text', 'mediumtext', 'longtext' ), true ) )
                {
                    $fields[$name]['type'] = 'varchar';
                    $fields[$name]['length'] = self::INDEXED_TEXT_LENGTH;
                    $fields[$name]['sqlserver:indexed_text'] = true;
                    $this->Notes[] = "$table.$name: text in the key of $index, NVARCHAR(" . self::INDEXED_TEXT_LENGTH . ')';
                }
            }
        return $fields;
    }

    function generateTableSQLList( $table, $tableDef, $params, $separateTypes )
    {
        $arrays = $this->generateTableArrays( $table, $tableDef, $params, false );
        if ( $separateTypes )
            return $arrays;
        // the primary key before the other indexes: it is the clustered index the others point at
        return array_merge( $arrays['tables'], $arrays['constraints'], $arrays['indexes'] );
    }

    function generateTableSchema( $table, $tableDef, $params )
    {
        $arrays = $this->generateTableArrays( $table, $tableDef, $params, true );
        return implode( "\n", array_merge( $arrays['tables'], $arrays['constraints'], $arrays['indexes'] ) ) . "\n";
    }

    function generateSchemaFile( $schema, $params = array() )
    {
        $this->Notes = array();
        $parts = array();
        foreach ( $schema as $table => $tableDef )
        {
            if ( $table === '_info' )
                continue;
            $parts[] = $this->generateTableSchema( $table, $tableDef, $params );
        }
        return implode( "\n", $parts );
    }

    function generateDropTable( $table )
    {
        return 'DROP TABLE ' . $this->quoteSchemaIdentifier( $table ) . ";\n";
    }

    /**
     * The inserts of a table's data; rows that give an identity column a value switch IDENTITY_INSERT on around them.
     */
    function generateTableInsertSQLList( $tableName, $tableDef, $dataEntries, $params, $withClosure = true )
    {
        $list = parent::generateTableInsertSQLList( $tableName, $tableDef, $dataEntries, $params, $withClosure );
        if ( !$list )
            return $list;
        foreach ( $dataEntries['fields'] as $field )
        {
            if ( isset( $tableDef['fields'][$field] ) && $tableDef['fields'][$field]['type'] === 'auto_increment' )
            {
                $t = $this->quoteSchemaIdentifier( $tableName );
                array_unshift( $list, "SET IDENTITY_INSERT $t ON" . ( $withClosure ? ';' : '' ) );
                $list[] = "SET IDENTITY_INSERT $t OFF" . ( $withClosure ? ';' : '' );
                break;
            }
        }
        return $list;
    }

    function generateDataValueTextSQL( $fieldDef, $value )
    {
        return $this->literalFor( $fieldDef, $value );
    }

    function escapeSQLString( $value )
    {
        return expSQLServerDialect::escapeString( $value );
    }

    // ------------------------------------------------------------------ read back

    /**
     * The schema of the connected SQL Server database, in the database-independent form.
     */
    function schema( $params = array() )
    {
        $params = array_merge( array( 'meta_data' => false, 'format' => 'generic', 'sort_columns' => true, 'sort_indexes' => true ), $params );
        if ( $this->Schema === false )
        {
            $schema = array();
            $tables = $this->DBInstance->arrayQuery( 'SELECT name FROM sys.tables WHERE is_ms_shipped = 0 ORDER BY name', array( 'column' => 'name' ) );
            foreach ( (array)$tables as $table )
            {
                if ( isset( $params['table_include'] ) && is_array( $params['table_include'] ) && !in_array( $table, $params['table_include'] ) )
                    continue;
                $schema[$table] = array( 'name' => $table, 'fields' => $this->fetchTableFields( $table, $params ),
                                         'indexes' => $this->fetchTableIndexes( $table, $params ) );
            }
            $this->transformSchema( $schema, $params['format'] == 'local' );
            ksort( $schema );
            $this->Schema = $schema;
        }
        else
            $this->transformSchema( $this->Schema, $params['format'] == 'local' );
        return $this->Schema;
    }

    /**
     * The fields of a table in schema form.
     */
    function fetchTableFields( $table, $params )
    {
        $fields = array();
        $rows = $this->DBInstance->arrayQuery(
            'SELECT c.name, t.name AS type, c.max_length, c.precision, c.scale, c.is_nullable, c.is_identity, dc.definition AS default_value'
          . ' FROM sys.columns c JOIN sys.types t ON t.user_type_id = c.user_type_id'
          . ' LEFT JOIN sys.default_constraints dc ON dc.object_id = c.default_object_id'
          . ' WHERE c.object_id = OBJECT_ID(' . expSQLServerDialect::stringLiteral( $table ) . ') ORDER BY c.column_id' );
        foreach ( (array)$rows as $row )
        {
            $field = array();
            if ( (string)$row['is_identity'] === '1' )
            {
                $fields[$row['name']] = array( 'type' => 'auto_increment', 'default' => false );
                continue;
            }
            $field['type'] = $this->convertToStandardType( $row['type'], $row['max_length'], $row['precision'], $row['scale'], $length );
            if ( $length !== null )
                $field['length'] = $length;
            $notNull = (string)$row['is_nullable'] === '0';
            if ( $notNull )
                $field['not_null'] = '1';
            $default = $row['default_value'] === null ? ( $notNull ? false : null ) : $this->parseDefault( $row['default_value'] );
            if ( in_array( $field['type'], array( 'int', 'bigint' ), true ) && $default !== null && $default !== false )
                $default = (int)$default;
            else if ( in_array( $field['type'], array( 'float', 'double' ), true ) && $default !== null && $default !== false )
                $default = (float)$default;
            else if ( $field['type'] === 'longtext' )
                $default = false;
            $field['default'] = $default;
            $fields[$row['name']] = $field;
        }
        if ( $params['sort_columns'] )
            ksort( $fields );
        return $fields;
    }

    /**
     * A default as SQL Server stores it ("((0))", "(N'')", "('a''b')", "(NULL)") as its value.
     *
     * @param string $definition
     * @return mixed
     */
    function parseDefault( $definition )
    {
        $value = trim( (string)$definition );
        while ( strlen( $value ) >= 2 && $value[0] === '(' && substr( $value, -1 ) === ')' )
            $value = trim( substr( $value, 1, -1 ) );
        if ( strcasecmp( $value, 'NULL' ) === 0 )
            return null;
        if ( preg_match( "/^N?'(.*)'$/s", $value, $m ) )
            return str_replace( "''", "'", $m[1] );
        return $value;
    }

    /**
     * The indexes of a table in schema form; the primary key PK_<table> as PRIMARY.
     */
    function fetchTableIndexes( $table, $params )
    {
        $indexes = array();
        $rows = $this->DBInstance->arrayQuery(
            'SELECT i.name AS index_name, i.is_primary_key, i.is_unique, c.name AS column_name, ic.key_ordinal'
          . ' FROM sys.indexes i JOIN sys.index_columns ic ON ic.object_id = i.object_id AND ic.index_id = i.index_id'
          . ' JOIN sys.columns c ON c.object_id = ic.object_id AND c.column_id = ic.column_id'
          . ' WHERE i.object_id = OBJECT_ID(' . expSQLServerDialect::stringLiteral( $table ) . ') AND i.type > 0 AND ic.is_included_column = 0'
          . ' ORDER BY i.name, ic.key_ordinal' );
        foreach ( (array)$rows as $row )
        {
            $name = $row['index_name'];
            if ( (string)$row['is_primary_key'] === '1' )
            {
                if ( $name === $this->primaryKeyName( $table ) )
                    $name = 'PRIMARY';
                $type = 'primary';
            }
            else
                $type = (string)$row['is_unique'] === '1' ? 'unique' : 'non-unique';
            if ( !isset( $indexes[$name] ) )
                $indexes[$name] = array( 'type' => $type, 'fields' => array() );
            $indexes[$name]['fields'][] = $row['column_name'];
        }
        if ( $params['sort_indexes'] )
            ksort( $indexes );
        return $indexes;
    }
}
