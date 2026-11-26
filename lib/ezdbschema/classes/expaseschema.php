<?php
/**
 * File containing the expASESchema class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The schema handler of SAP Adaptive Server Enterprise (expASEDB): the database-independent schema (.dba) as ASE
 * DDL, and an ASE database read back into that schema.
 *
 * Types: int INT, bigint BIGINT, tinyint/smallint SMALLINT, auto_increment INT IDENTITY, varchar(n) and char(n)
 * UNIVARCHAR(n) (UNITEXT past 4,000 characters), text and longtext UNITEXT, float REAL, double DOUBLE PRECISION,
 * decimal DECIMAL, timestamp/datetime BIGDATETIME, date DATE, time TIME, blob/longblob IMAGE. Text is Unicode
 * (UTF-16 in the database) whatever the server's character set.
 *
 * Every column says NULL or NOT NULL: an ASE column without either is NOT NULL (unless the database has
 * "allow nulls by default"). A primary key is the constraint PK_<table>; index names are unique per table, as in MySQL.
 *
 * Index keys: a UNITEXT cannot be a key, so a text column of an index is UNIVARCHAR(1000) (2,000 bytes: an ASE server
 * with 16 KB pages takes keys of that size; a server with 2 KB pages does not, see the driver's documentation).
 *
 * Untested against a server (see expASEDialect).
 */
class expASESchema extends eZDBSchemaInterface
{
    /** The characters of a text column that is an index key */
    const INDEXED_TEXT_LENGTH = 1000;

    /** @var string[] notes of the last generation */
    public $Notes = array();

    function schemaType()
    {
        return 'ase';
    }

    function schemaName()
    {
        return 'SAP ASE';
    }

    function quoteSchemaIdentifier( $name )
    {
        return expASEDialect::quoteIdentifierIfNeeded( $name );
    }

    function insertColumnName( $name )
    {
        return $this->quoteSchemaIdentifier( $name );
    }

    function reservedKeywordList()
    {
        return array_map( 'strtolower', expASEDialect::RESERVED_WORDS );
    }

    function primaryKeyName( $table )
    {
        $name = 'PK_' . $table;
        return strlen( $name ) <= expASEDialect::MAX_IDENTIFIER_LENGTH ? $name : 'PK_' . substr( $table, 0, 200 ) . '_' . substr( md5( $table ), 0, 8 );
    }

    // ------------------------------------------------------------------ types

    function convertFromStandardType( $type, $length = null )
    {
        switch ( $type )
        {
            case 'int':
            case 'mediumint':
                return 'INT';
            case 'auto_increment':
                return 'INT IDENTITY';
            case 'bigint':
                return 'BIGINT';
            case 'tinyint':
            case 'smallint':
                return 'SMALLINT';
            case 'varchar':
            case 'char':
                $n = (int)$length > 0 ? (int)$length : ( $type === 'char' ? 1 : 255 );
                return $n > expASEDialect::MAX_UNIVARCHAR_LENGTH ? 'UNITEXT' : "UNIVARCHAR($n)";
            case 'tinytext':
            case 'text':
            case 'mediumtext':
            case 'longtext':
                return 'UNITEXT';
            case 'float':
                return 'REAL';
            case 'double':
                return 'DOUBLE PRECISION';
            case 'decimal':
                return 'DECIMAL(' . ( preg_match( '/^\s*\d+\s*(,\s*\d+\s*)?$/', (string)$length ) ? preg_replace( '/\s+/', '', (string)$length ) : '18,0' ) . ')';
            case 'timestamp':
            case 'datetime':
                return 'BIGDATETIME';
            case 'date':
                return 'DATE';
            case 'time':
                return 'TIME';
            case 'blob':
            case 'mediumblob':
            case 'longblob':
                return 'IMAGE';
        }
        throw new RuntimeException( "Unhandled column type: $type" );
    }

    /**
     * The .dba type of an ASE column (systypes name, syscolumns length in bytes, precision, scale).
     *
     * @return string
     */
    function convertToStandardType( $typeName, $bytes, $precision, $scale, &$length )
    {
        $length = null;
        switch ( strtolower( trim( (string)$typeName ) ) )
        {
            case 'int':
            case 'intn':
            case 'smallint':
            case 'tinyint':
            case 'unsigned int':
                $length = 11;
                return 'int';
            case 'bigint':
                $length = 20;
                return 'bigint';
            case 'real':
                return 'float';
            case 'float':
            case 'double precision':
            case 'floatn':
                return 'double';
            case 'decimal':
            case 'numeric':
            case 'decimaln':
            case 'numericn':
                $length = ( (int)$precision > 0 ? (int)$precision : 18 ) . ',' . max( 0, (int)$scale );
                return 'decimal';
            case 'univarchar':
            case 'unichar':
                $length = intdiv( (int)$bytes, 2 );
                return 'varchar';
            case 'varchar':
            case 'nvarchar':
            case 'char':
            case 'nchar':
                $length = (int)$bytes;
                return 'varchar';
            case 'unitext':
            case 'text':
                return 'longtext';
            case 'image':
            case 'varbinary':
            case 'binary':
                return 'longblob';
            case 'bigdatetime':
            case 'datetime':
            case 'smalldatetime':
            case 'datetimn':
                return 'timestamp';
            case 'date':
            case 'daten':
                return 'date';
            case 'time':
            case 'timen':
            case 'bigtime':
                return 'time';
            case 'bit':
                $length = 1;
                return 'int';
        }
        throw new RuntimeException( "Unhandled column type: $typeName" );
    }

    /**
     * The fields with every text column of an index as UNIVARCHAR(1000).
     *
     * @param string $table
     * @param array $tableDef
     * @return array
     */
    function indexedTextFields( $table, array $tableDef )
    {
        $fields = $tableDef['fields'];
        foreach ( isset( $tableDef['indexes'] ) ? $tableDef['indexes'] : array() as $def )
        {
            foreach ( $def['fields'] as $fieldDef )
            {
                $name = is_array( $fieldDef ) ? $fieldDef['name'] : $fieldDef;
                if ( !isset( $fields[$name] ) || !empty( $fields[$name]['ase:indexed_text'] ) )
                    continue;
                if ( $this->convertFromStandardType( $fields[$name]['type'], isset( $fields[$name]['length'] ) ? $fields[$name]['length'] : null ) !== 'UNITEXT' )
                    continue;
                $fields[$name]['type'] = 'varchar';
                $fields[$name]['length'] = self::INDEXED_TEXT_LENGTH;
                $fields[$name]['ase:indexed_text'] = true;
                $this->Notes[] = "$table.$name: text in the key of an index, UNIVARCHAR(" . self::INDEXED_TEXT_LENGTH . ')';
            }
        }
        return $fields;
    }

    // ------------------------------------------------------------------ DDL

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
                return is_numeric( $value ) ? expASEDialect::literal( (float)$value ) : ( $value === '' ? '0' : 'NULL' );
            case 'blob':
            case 'mediumblob':
            case 'longblob':
                return '0x' . bin2hex( (string)$value );
        }
        return expASEDialect::stringLiteral( (string)$value );
    }

    function generateDefaultDef( $table, $field, $def )
    {
        if ( $def['type'] === 'auto_increment' || !array_key_exists( 'default', $def ) || $def['default'] === false || $def['default'] === null )
            return '';
        if ( $def['default'] === '' && in_array( $def['type'], array( 'timestamp', 'datetime', 'date', 'time' ), true ) )
            return '';
        if ( in_array( $this->convertFromStandardType( $def['type'], isset( $def['length'] ) ? $def['length'] : null ), array( 'UNITEXT', 'IMAGE' ), true ) )
            return '';
        return 'DEFAULT ' . $this->literalFor( $def, $def['default'] );
    }

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

    function generateAddIndexSql( $table, $index, $def, $params, $withClosure = true, $fields = array() )
    {
        $columns = array();
        foreach ( $def['fields'] as $fieldDef )
            $columns[] = $this->quoteSchemaIdentifier( is_array( $fieldDef ) ? $fieldDef['name'] : $fieldDef );
        $columns = implode( ', ', $columns );
        if ( $def['type'] === 'primary' )
            $sql = 'ALTER TABLE ' . $this->quoteSchemaIdentifier( $table ) . ' ADD CONSTRAINT ' . $this->primaryKeyName( $table ) . " PRIMARY KEY ($columns)";
        else
            $sql = 'CREATE ' . ( $def['type'] === 'unique' ? 'UNIQUE ' : '' ) . 'INDEX ' . $this->quoteSchemaIdentifier( $index )
                 . ' ON ' . $this->quoteSchemaIdentifier( $table ) . " ($columns)";
        return $sql . ( $withClosure ? ";\n" : '' );
    }

    function generateDropIndexSql( $table, $index, $def, $withClosure = true )
    {
        if ( $def['type'] === 'primary' )
            $sql = 'ALTER TABLE ' . $this->quoteSchemaIdentifier( $table ) . ' DROP CONSTRAINT ' . $this->primaryKeyName( $table );
        else
            $sql = 'DROP INDEX ' . $this->quoteSchemaIdentifier( $table ) . '.' . $this->quoteSchemaIdentifier( $index );
        return $sql . ( $withClosure ? ";\n" : '' );
    }

    function generateAddFieldSql( $table, $field, $def, $params )
    {
        return 'ALTER TABLE ' . $this->quoteSchemaIdentifier( $table ) . ' ADD ' . $this->generateFieldDef( $table, $field, $def ) . ";\n";
    }

    function generateDropFieldSql( $table, $field, $params )
    {
        return 'ALTER TABLE ' . $this->quoteSchemaIdentifier( $table ) . ' DROP ' . $this->quoteSchemaIdentifier( $field ) . ";\n";
    }

    function generateAlterFieldSql( $table, $field, $def, $params )
    {
        $t = $this->quoteSchemaIdentifier( $table );
        $sql = '';
        $default = $this->generateDefaultDef( $table, $field, $def );
        if ( !empty( $def['not_null'] ) && $default !== '' )
            $sql .= "UPDATE $t SET " . $this->quoteSchemaIdentifier( $field ) . ' = ' . substr( $default, 8 ) . ' WHERE ' . $this->quoteSchemaIdentifier( $field ) . " IS NULL;\n";
        return $sql . "ALTER TABLE $t MODIFY " . $this->generateFieldDef( $table, $field, $def ) . ";\n";
    }

    /**
     * The statements of one table: array( 'tables' => ..., 'constraints' => ..., 'indexes' => ... ).
     */
    function generateTableArrays( $table, $tableDef, $params, $withClosure = true )
    {
        $tableDef['fields'] = $this->indexedTextFields( $table, $tableDef );
        $end = $withClosure ? ';' : '';
        $arrays = array( 'tables' => array(), 'constraints' => array(), 'indexes' => array() );
        $lines = array();
        foreach ( $tableDef['fields'] as $field => $def )
            $lines[] = '  ' . $this->generateFieldDef( $table, $field, $def );
        $arrays['tables'][] = 'CREATE TABLE ' . $this->quoteSchemaIdentifier( $table ) . " (\n" . implode( ",\n", $lines ) . "\n)" . $end;
        foreach ( isset( $tableDef['indexes'] ) ? $tableDef['indexes'] : array() as $index => $def )
        {
            $sql = $this->generateAddIndexSql( $table, $index, $def, $params, false );
            $arrays[$def['type'] === 'primary' ? 'constraints' : 'indexes'][] = $sql . $end;
        }
        return $arrays;
    }

    function generateTableSQLList( $table, $tableDef, $params, $separateTypes )
    {
        $arrays = $this->generateTableArrays( $table, $tableDef, $params, false );
        if ( $separateTypes )
            return $arrays;
        return array_merge( $arrays['tables'], $arrays['constraints'], $arrays['indexes'] );
    }

    /**
     * The DDL of one table as an isql script (each statement ended by "go").
     */
    function generateTableSchema( $table, $tableDef, $params )
    {
        $out = array();
        foreach ( $this->generateTableSQLList( $table, $tableDef, $params, false ) as $sql )
            $out[] = $sql . "\ngo";
        return implode( "\n", $out ) . "\n";
    }

    function generateSchemaFile( $schema, $params = array() )
    {
        $this->Notes = array();
        $parts = array();
        foreach ( $schema as $table => $tableDef )
            if ( $table !== '_info' )
                $parts[] = $this->generateTableSchema( $table, $tableDef, $params );
        return implode( "\n", $parts );
    }

    function generateDropTable( $table )
    {
        return 'DROP TABLE ' . $this->quoteSchemaIdentifier( $table ) . ";\n";
    }

    function generateDataValueTextSQL( $fieldDef, $value )
    {
        return $this->literalFor( $fieldDef, $value );
    }

    function escapeSQLString( $value )
    {
        return expASEDialect::escapeString( $value );
    }

    // ------------------------------------------------------------------ read back

    function schema( $params = array() )
    {
        $params = array_merge( array( 'meta_data' => false, 'format' => 'generic', 'sort_columns' => true, 'sort_indexes' => true ), $params );
        if ( $this->Schema === false )
        {
            $schema = array();
            foreach ( (array)$this->DBInstance->relationList( eZDBInterface::RELATION_TABLE ) as $table )
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

    function fetchTableFields( $table, $params )
    {
        $fields = array();
        foreach ( (array)$this->DBInstance->tableColumns( $table ) as $row )
        {
            $field = array();
            if ( (string)$row['identity'] === '1' )
            {
                $field['type'] = 'auto_increment';
                $field['default'] = false;
            }
            else
            {
                $field['type'] = $this->convertToStandardType( $row['type'], $row['max_length'], $row['precision'], $row['scale'], $length );
                if ( $length !== null )
                    $field['length'] = $length;
                $field['not_null'] = (string)$row['nullable'] === '1' ? '0' : '1';
                $field['default'] = $this->parseDefault( $row['default'] );
            }
            $fields[$row['name']] = $field;
        }
        if ( !empty( $params['sort_columns'] ) )
            ksort( $fields );
        return $fields;
    }

    /**
     * The value of a default of the catalogue (syscomments: "DEFAULT 0", "DEFAULT ''"); false without one.
     *
     * @param string|null $definition
     * @return mixed
     */
    function parseDefault( $definition )
    {
        $definition = trim( (string)$definition );
        if ( $definition === '' )
            return false;
        if ( stripos( $definition, 'DEFAULT' ) === 0 )
            $definition = trim( substr( $definition, 7 ) );
        if ( strcasecmp( $definition, 'NULL' ) === 0 )
            return null;
        if ( preg_match( "/^'(.*)'$/s", $definition, $m ) )
            return str_replace( "''", "'", $m[1] );
        if ( is_numeric( $definition ) )
            return strpos( $definition, '.' ) === false ? (int)$definition : (float)$definition;
        return $definition;
    }

    function fetchTableIndexes( $table, $params )
    {
        $indexes = array();
        $rows = $this->DBInstance->arrayQuery( 'SELECT i.name, i.indid, i.status, i.status2 FROM sysindexes i WHERE i.id = OBJECT_ID('
                                             . expASEDialect::stringLiteral( $table ) . ') AND i.indid > 0 AND i.indid < 255' );
        foreach ( (array)$rows as $row )
        {
            $primary = ( (int)$row['status'] & 2048 ) === 2048;
            $unique = ( (int)$row['status'] & 2 ) === 2;
            $columns = array();
            for ( $k = 1; $k <= 31; $k++ )
            {
                $name = $this->DBInstance->arrayQuery( 'SELECT index_col(' . expASEDialect::stringLiteral( $table ) . ', ' . (int)$row['indid'] . ", $k) AS name" );
                if ( !is_array( $name ) || !isset( $name[0]['name'] ) || $name[0]['name'] === null || $name[0]['name'] === '' )
                    break;
                $columns[] = $name[0]['name'];
            }
            $indexes[$primary ? 'PRIMARY' : $row['name']] = array( 'type' => $primary ? 'primary' : ( $unique ? 'unique' : 'non-unique' ), 'fields' => $columns );
        }
        if ( !empty( $params['sort_indexes'] ) )
            ksort( $indexes );
        return $indexes;
    }
}
