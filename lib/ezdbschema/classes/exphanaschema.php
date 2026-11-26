<?php
/**
 * File containing the expHANASchema class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The schema handler of SAP HANA (expHANADB): the database-independent schema (.dba) as HANA DDL, and a HANA schema
 * read back into that schema (for the upgrade check and ezsqldumpschema.php).
 *
 * Tables are column tables. Types: int INTEGER, bigint BIGINT, tinyint/smallint SMALLINT, auto_increment INTEGER with
 * a sequence and a trigger (below), varchar(n) NVARCHAR(n), char(n) NVARCHAR(n) with the comment exp:char (HANA's
 * NCHAR pads with spaces, MySQL's CHAR does not), text and longtext NCLOB, float REAL, double DOUBLE, decimal
 * DECIMAL, timestamp/datetime TIMESTAMP, date DATE, time TIME, blob/longblob BLOB.
 *
 * Names: tables, columns and indexes unquoted (stored in upper case), a reserved word quoted in upper case; a primary
 * key is the constraint PK_<table>. Index names are unique in a whole schema in HANA, while the .dba (as MySQL) names
 * them per table: an index is stored as <table>$<index>, and read back without the table.
 *
 * auto_increment: the sequence S_<table> and the trigger T_<table>_BI, which numbers a row that comes without id,
 * NULL or 0 and moves the sequence past an id an insert gives itself (so the rows of the data files and the copies
 * the kernel makes keep their ids). The column's comment says it is the one (exp:auto_increment).
 *
 * Index keys: an NCLOB cannot be a key. So a text column in an index is NVARCHAR(5000) (the column's comment says it
 * was text: exp:longtext).
 */
class expHANASchema extends eZDBSchemaInterface
{
    /** The comment of an NVARCHAR column that is text in the .dba */
    const TEXT_MARK = 'exp:longtext';

    /** The comment of an NVARCHAR column that is char in the .dba (HANA's NCHAR pads with spaces, MySQL's CHAR does not) */
    const CHAR_MARK = 'exp:char';

    /** The comment of the column a sequence numbers */
    const AUTO_MARK = 'exp:auto_increment';

    /** What separates the table from the index in a stored index name */
    const INDEX_SEPARATOR = '$';

    /** @var string[] notes of the last generation (indexes changed to fit HANA's limits) */
    public $Notes = array();

    function schemaType()
    {
        return 'hana';
    }

    function schemaName()
    {
        return 'SAP HANA';
    }

    // ------------------------------------------------------------------ names

    function quoteSchemaIdentifier( $name )
    {
        return expHANADialect::quoteIdentifierIfNeeded( $name );
    }

    function insertColumnName( $name )
    {
        return $this->quoteSchemaIdentifier( $name );
    }

    function reservedKeywordList()
    {
        return array_map( 'strtolower', expHANADialect::RESERVED_WORDS );
    }

    /**
     * A name of the schema made of a prefix and a table name, within 127 characters (a long table name is cut and
     * given a hash, so two long names stay apart).
     *
     * @param string $prefix
     * @param string $table
     * @param string $suffix
     * @return string
     */
    public static function objectName( $prefix, $table, $suffix = '' )
    {
        $name = expHANADialect::upper( $prefix . $table . $suffix );
        if ( strlen( $name ) <= expHANADialect::MAX_IDENTIFIER_LENGTH )
            return $name;
        $hash = strtoupper( substr( md5( $table ), 0, 8 ) );
        return expHANADialect::upper( substr( $prefix . $table, 0, expHANADialect::MAX_IDENTIFIER_LENGTH - strlen( $suffix ) - 9 ) . '_' . $hash . $suffix );
    }

    function primaryKeyName( $table )
    {
        return self::objectName( 'PK_', $table );
    }

    function sequenceName( $table )
    {
        return self::objectName( 'S_', $table );
    }

    function triggerName( $table )
    {
        return self::objectName( 'T_', $table, '_BI' );
    }

    // ------------------------------------------------------------------ types

    function convertFromStandardType( $type, $length = null )
    {
        switch ( $type )
        {
            case 'int':
            case 'mediumint':
            case 'auto_increment':
                return 'INTEGER';
            case 'bigint':
                return 'BIGINT';
            case 'tinyint':
            case 'smallint':
                return 'SMALLINT';
            case 'varchar':
            case 'char':
                $n = (int)$length > 0 ? (int)$length : ( $type === 'char' ? 1 : 255 );
                return $n > expHANADialect::MAX_NVARCHAR_LENGTH ? 'NCLOB' : "NVARCHAR($n)";
            case 'tinytext':
            case 'text':
            case 'mediumtext':
            case 'longtext':
                return 'NCLOB';
            case 'float':
                return 'REAL';
            case 'double':
                return 'DOUBLE';
            case 'decimal':
                return 'DECIMAL(' . ( preg_match( '/^\s*\d+\s*(,\s*\d+\s*)?$/', (string)$length ) ? preg_replace( '/\s+/', '', (string)$length ) : '18,0' ) . ')';
            case 'timestamp':
            case 'datetime':
                return 'TIMESTAMP';
            case 'date':
                return 'DATE';
            case 'time':
                return 'TIME';
            case 'blob':
            case 'mediumblob':
            case 'longblob':
                return 'BLOB';
        }
        throw new RuntimeException( "Unhandled column type: $type" );
    }

    /**
     * The .dba type of a HANA column (SYS.TABLE_COLUMNS: DATA_TYPE_NAME, LENGTH, SCALE).
     *
     * @return string
     */
    function convertToStandardType( $typeName, $length, $scale, &$outLength )
    {
        $outLength = null;
        switch ( strtoupper( trim( (string)$typeName ) ) )
        {
            case 'TINYINT':
            case 'SMALLINT':
                $outLength = 6;
                return 'int';
            case 'INTEGER':
                $outLength = 11;
                return 'int';
            case 'BIGINT':
                $outLength = 20;
                return 'bigint';
            case 'REAL':
                return 'float';
            case 'DOUBLE':
                return 'double';
            case 'DECIMAL':
            case 'SMALLDECIMAL':
                $outLength = ( (int)$length > 0 ? (int)$length : 18 ) . ',' . max( 0, (int)$scale );
                return 'decimal';
            case 'VARCHAR':
            case 'NVARCHAR':
            case 'ALPHANUM':
            case 'SHORTTEXT':
                $outLength = (int)$length;
                return 'varchar';
            case 'CHAR':
            case 'NCHAR':
                $outLength = (int)$length;
                return 'char';
            case 'CLOB':
            case 'NCLOB':
            case 'TEXT':
                return 'longtext';
            case 'BLOB':
            case 'VARBINARY':
                return 'longblob';
            case 'TIMESTAMP':
            case 'SECONDDATE':
                return 'timestamp';
            case 'DATE':
                return 'date';
            case 'TIME':
                return 'time';
            case 'BOOLEAN':
                $outLength = 1;
                return 'int';
        }
        throw new RuntimeException( "Unhandled column type: $typeName" );
    }

    /**
     * The fields of a table with every text column that is a key column of an index as NVARCHAR(5000).
     *
     * @param string $table
     * @param array $tableDef
     * @return array the fields
     */
    function indexedTextFields( $table, array $tableDef )
    {
        $fields = $tableDef['fields'];
        foreach ( isset( $tableDef['indexes'] ) ? $tableDef['indexes'] : array() as $index => $def )
        {
            foreach ( $def['fields'] as $fieldDef )
            {
                $name = is_array( $fieldDef ) ? $fieldDef['name'] : $fieldDef;
                if ( !isset( $fields[$name] ) || !empty( $fields[$name]['hana:indexed_text'] ) )
                    continue;
                $isText = in_array( $fields[$name]['type'], array( 'tinytext', 'text', 'mediumtext', 'longtext' ), true )
                       || ( in_array( $fields[$name]['type'], array( 'varchar', 'char' ), true ) && (int)( isset( $fields[$name]['length'] ) ? $fields[$name]['length'] : 0 ) > expHANADialect::MAX_NVARCHAR_LENGTH );
                if ( !$isText )
                    continue;
                $fields[$name]['type'] = 'varchar';
                $fields[$name]['length'] = expHANADialect::MAX_NVARCHAR_LENGTH;
                $fields[$name]['hana:indexed_text'] = true;
                $this->Notes[] = "$table.$name: text in the key of an index, NVARCHAR(" . expHANADialect::MAX_NVARCHAR_LENGTH . ')';
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
                return is_numeric( $value ) ? expHANADialect::literal( (float)$value ) : ( $value === '' ? '0' : 'NULL' );
            case 'blob':
            case 'mediumblob':
            case 'longblob':
                return "X'" . bin2hex( (string)$value ) . "'";
        }
        return expHANADialect::stringLiteral( (string)$value );
    }

    function generateDefaultDef( $table, $field, $def )
    {
        if ( $def['type'] === 'auto_increment' || !array_key_exists( 'default', $def ) || $def['default'] === false )
            return '';
        if ( $def['default'] === '' && in_array( $def['type'], array( 'timestamp', 'datetime', 'date', 'time' ), true ) )
            return '';
        // an NCLOB takes no default but NULL
        if ( $this->convertFromStandardType( $def['type'], isset( $def['length'] ) ? $def['length'] : null ) === 'NCLOB' && $def['default'] !== null )
            return '';
        if ( $def['default'] === null )
            return !empty( $def['not_null'] ) ? '' : 'DEFAULT NULL';
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
        return $sql . ( !empty( $def['not_null'] ) ? ' NOT NULL' : '' );
    }

    /**
     * The statements of a sequence numbered column: the sequence, the column's comment and the trigger.
     *
     * @param string $table
     * @param string $field
     * @return string[]
     */
    function autoIncrementSQLList( $table, $field )
    {
        $sequence = $this->sequenceName( $table );
        $column = $this->quoteSchemaIdentifier( $field );
        return array(
            "CREATE SEQUENCE $sequence START WITH 1 INCREMENT BY 1 NO CYCLE",
            'COMMENT ON COLUMN ' . $this->quoteSchemaIdentifier( $table ) . ".$column IS " . expHANADialect::stringLiteral( self::AUTO_MARK ),
            'CREATE TRIGGER ' . $this->triggerName( $table ) . ' BEFORE INSERT ON ' . $this->quoteSchemaIdentifier( $table )
          . " REFERENCING NEW ROW new_row FOR EACH ROW\n"
          . "BEGIN\n"
          . "  DECLARE next_id BIGINT;\n"
          . "  IF :new_row.$column IS NULL OR :new_row.$column = 0 THEN\n"
          . "    SELECT $sequence.NEXTVAL INTO next_id FROM DUMMY;\n"
          . "    new_row.$column = :next_id;\n"
          . "  ELSE\n"
          . "    SELECT $sequence.NEXTVAL INTO next_id FROM DUMMY;\n"
          . "    WHILE :next_id < :new_row.$column DO\n"
          . "      SELECT $sequence.NEXTVAL INTO next_id FROM DUMMY;\n"
          . "    END WHILE;\n"
          . "  END IF;\n"
          . 'END',
        );
    }

    function generateAddIndexSql( $table, $index, $def, $params, $withClosure = true, $fields = array() )
    {
        $key = array();
        foreach ( $def['fields'] as $fieldDef )
        {
            $name = is_array( $fieldDef ) ? $fieldDef['name'] : $fieldDef;
            $key[] = $this->quoteSchemaIdentifier( $name );
        }
        $columns = implode( ', ', $key );
        if ( $def['type'] === 'primary' )
            $sql = 'ALTER TABLE ' . $this->quoteSchemaIdentifier( $table ) . ' ADD CONSTRAINT ' . $this->primaryKeyName( $table ) . " PRIMARY KEY ($columns)";
        else
            $sql = 'CREATE ' . ( $def['type'] === 'unique' ? 'UNIQUE ' : '' ) . 'INDEX ' . $this->indexName( $table, $index )
                 . ' ON ' . $this->quoteSchemaIdentifier( $table ) . " ($columns)";
        return $sql . ( $withClosure ? ";\n" : '' );
    }

    /**
     * The name an index of the .dba has in HANA, quoted: <TABLE>$<INDEX> (cut with a hash when too long).
     *
     * @param string $table
     * @param string $index
     * @return string
     */
    function indexName( $table, $index )
    {
        return expHANADialect::quoteIdentifier( self::storedIndexName( $table, $index ) );
    }

    /**
     * @param string $table
     * @param string $index
     * @return string
     */
    public static function storedIndexName( $table, $index )
    {
        $name = $table . self::INDEX_SEPARATOR . $index;
        if ( strlen( $name ) > expHANADialect::MAX_IDENTIFIER_LENGTH )
            $name = self::objectName( '', $table, self::INDEX_SEPARATOR . substr( $index, 0, 40 ) . '_' . substr( md5( $index ), 0, 6 ) );
        return expHANADialect::upper( $name );
    }

    /**
     * The .dba's name of a stored index name (without the table and the separator).
     *
     * @param string $table
     * @param string $stored
     * @return string
     */
    public static function dbaIndexName( $table, $stored )
    {
        $stored = strtolower( (string)$stored );
        $prefix = strtolower( $table ) . self::INDEX_SEPARATOR;
        return strpos( $stored, $prefix ) === 0 ? substr( $stored, strlen( $prefix ) ) : $stored;
    }

    function generateDropIndexSql( $table, $index, $def, $withClosure = true )
    {
        if ( $def['type'] === 'primary' )
            $sql = 'ALTER TABLE ' . $this->quoteSchemaIdentifier( $table ) . ' DROP CONSTRAINT ' . $this->primaryKeyName( $table );
        else
            $sql = 'DROP INDEX ' . $this->indexName( $table, $index );
        return $sql . ( $withClosure ? ";\n" : '' );
    }

    function generateAddFieldSql( $table, $field, $def, $params )
    {
        $sql = 'ALTER TABLE ' . $this->quoteSchemaIdentifier( $table ) . ' ADD (' . $this->generateFieldDef( $table, $field, $def ) . ");\n";
        if ( $def['type'] === 'auto_increment' )
            foreach ( $this->autoIncrementSQLList( $table, $field ) as $statement )
                $sql .= $statement . ";\n";
        return $sql;
    }

    function generateDropFieldSql( $table, $field, $params )
    {
        return 'ALTER TABLE ' . $this->quoteSchemaIdentifier( $table ) . ' DROP (' . $this->quoteSchemaIdentifier( $field ) . ");\n";
    }

    function generateAlterFieldSql( $table, $field, $def, $params )
    {
        $t = $this->quoteSchemaIdentifier( $table );
        $sql = '';
        $default = $this->generateDefaultDef( $table, $field, $def );
        if ( !empty( $def['not_null'] ) && $default !== '' )
            $sql .= "UPDATE $t SET " . $this->quoteSchemaIdentifier( $field ) . ' = ' . substr( $default, 8 ) . ' WHERE ' . $this->quoteSchemaIdentifier( $field ) . " IS NULL;\n";
        return $sql . "ALTER TABLE $t ALTER (" . $this->generateFieldDef( $table, $field, $def ) . ");\n";
    }

    /**
     * The statements of one table: array( 'tables' => ..., 'comments' => ..., 'constraints' => ..., 'indexes' => ...,
     * 'sequences' => ... ).
     */
    function generateTableArrays( $table, $tableDef, $params, $withClosure = true )
    {
        $tableDef['fields'] = $this->indexedTextFields( $table, $tableDef );
        $end = $withClosure ? ';' : '';
        $arrays = array( 'tables' => array(), 'comments' => array(), 'constraints' => array(), 'indexes' => array(), 'sequences' => array() );
        $lines = array();
        foreach ( $tableDef['fields'] as $field => $def )
        {
            $lines[] = '  ' . $this->generateFieldDef( $table, $field, $def );
            $mark = !empty( $def['hana:indexed_text'] ) ? self::TEXT_MARK
                  : ( $def['type'] === 'char' && $this->convertFromStandardType( 'char', isset( $def['length'] ) ? $def['length'] : null ) !== 'NCLOB' ? self::CHAR_MARK : null );
            if ( $mark !== null )
                $arrays['comments'][] = 'COMMENT ON COLUMN ' . $this->quoteSchemaIdentifier( $table ) . '.' . $this->quoteSchemaIdentifier( $field )
                                      . ' IS ' . expHANADialect::stringLiteral( $mark ) . $end;
            if ( $def['type'] === 'auto_increment' )
                foreach ( $this->autoIncrementSQLList( $table, $field ) as $statement )
                    $arrays['sequences'][] = $statement . $end;
        }
        $arrays['tables'][] = 'CREATE ' . $this->tableType() . ' TABLE ' . $this->quoteSchemaIdentifier( $table ) . " (\n" . implode( ",\n", $lines ) . "\n)" . $end;
        foreach ( isset( $tableDef['indexes'] ) ? $tableDef['indexes'] : array() as $index => $def )
        {
            $sql = $this->generateAddIndexSql( $table, $index, $def, $params, false, $tableDef['fields'] );
            if ( $def['type'] === 'primary' )
                $arrays['constraints'][] = $sql . $end;
            else
                $arrays['indexes'][] = $sql . $end;
        }
        return $arrays;
    }

    /**
     * COLUMN, or ROW with [HANASettings] TableType=row (HANA's row store: faster single-row writes, all in memory).
     *
     * @return string
     */
    function tableType()
    {
        return $this->DBInstance instanceof expHANADB && strtolower( trim( (string)$this->DBInstance->Settings['TableType'] ) ) === 'row' ? 'ROW' : 'COLUMN';
    }

    function generateTableSQLList( $table, $tableDef, $params, $separateTypes )
    {
        $arrays = $this->generateTableArrays( $table, $tableDef, $params, false );
        if ( $separateTypes )
            return $arrays;
        return array_merge( $arrays['tables'], $arrays['comments'], $arrays['constraints'], $arrays['indexes'], $arrays['sequences'] );
    }

    /**
     * The DDL of one table as a script of hdbsql (-c ';' -mm): the trigger's body holds semicolons, so it ends with a
     * line of its own that has the separator only.
     */
    function generateTableSchema( $table, $tableDef, $params )
    {
        $arrays = $this->generateTableArrays( $table, $tableDef, $params, false );
        $out = array();
        foreach ( array_merge( $arrays['tables'], $arrays['comments'], $arrays['constraints'], $arrays['indexes'], $arrays['sequences'] ) as $sql )
            $out[] = strpos( $sql, 'CREATE TRIGGER' ) === 0 ? "$sql\n;" : $sql . ';';
        return implode( "\n", $out ) . "\n";
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

    function generateDataValueTextSQL( $fieldDef, $value )
    {
        return $this->literalFor( $fieldDef, $value );
    }

    function escapeSQLString( $value )
    {
        return expHANADialect::escapeString( $value );
    }

    // ------------------------------------------------------------------ read back

    /**
     * The schema of the connected HANA schema, in the database-independent form.
     */
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

    /**
     * The fields of a table in schema form.
     */
    function fetchTableFields( $table, $params )
    {
        $fields = array();
        $rows = $this->DBInstance->arrayQuery(
            'SELECT LOWER(COLUMN_NAME) AS name, DATA_TYPE_NAME AS type_name, LENGTH AS length, SCALE AS scale, IS_NULLABLE AS nullable,'
          . ' DEFAULT_VALUE AS default_value, COMMENTS AS comments FROM SYS.TABLE_COLUMNS'
          . ' WHERE SCHEMA_NAME = CURRENT_SCHEMA AND TABLE_NAME = ' . expHANADialect::stringLiteral( expHANADialect::upper( $table ) )
          . ' ORDER BY POSITION' );
        foreach ( (array)$rows as $row )
        {
            $name = $row['name'];
            $comment = trim( (string)$row['comments'] );
            $field = array();
            if ( $comment === self::AUTO_MARK )
            {
                $field['type'] = 'auto_increment';
                $field['default'] = false;
            }
            else
            {
                $type = $this->convertToStandardType( $row['type_name'], $row['length'], $row['scale'], $length );
                if ( $type === 'varchar' && $comment === self::TEXT_MARK )
                {
                    $type = 'longtext';
                    $length = null;
                }
                else if ( $type === 'varchar' && $comment === self::CHAR_MARK )
                    $type = 'char';
                $field['type'] = $type;
                if ( $length !== null )
                    $field['length'] = $length;
                $field['not_null'] = strtoupper( (string)$row['nullable'] ) === 'FALSE' ? '1' : '0';
                $field['default'] = $this->parseDefault( $row['default_value'], $type );
            }
            $fields[$name] = $field;
        }
        if ( !empty( $params['sort_columns'] ) )
            ksort( $fields );
        return $fields;
    }

    /**
     * The value of a column default of the catalogue (SYS.TABLE_COLUMNS.DEFAULT_VALUE is the value as text, without
     * quotes); false without one.
     *
     * @param string|null $value
     * @param string $type the .dba type
     * @return mixed
     */
    function parseDefault( $value, $type = 'varchar' )
    {
        if ( $value === null )
            return false;
        $value = (string)$value;
        if ( strcasecmp( $value, 'NULL' ) === 0 )
            return null;
        if ( in_array( $type, array( 'int', 'bigint', 'auto_increment' ), true ) && is_numeric( $value ) )
            return (int)$value;
        if ( in_array( $type, array( 'float', 'double', 'decimal' ), true ) && is_numeric( $value ) )
            return (float)$value;
        return $value;
    }

    /**
     * The indexes of a table in schema form: PRIMARY for the primary key.
     */
    function fetchTableIndexes( $table, $params )
    {
        $rows = $this->DBInstance->arrayQuery(
            'SELECT INDEX_NAME AS index_name, CONSTRAINT AS constraint_type, COLUMN_NAME AS field_name, POSITION AS position_no'
          . ' FROM SYS.INDEX_COLUMNS WHERE SCHEMA_NAME = CURRENT_SCHEMA AND TABLE_NAME = ' . expHANADialect::stringLiteral( expHANADialect::upper( $table ) )
          . ' ORDER BY INDEX_NAME, POSITION' );
        $indexes = array();
        foreach ( (array)$rows as $row )
        {
            $constraint = strtoupper( (string)$row['constraint_type'] );
            $primary = $constraint === 'PRIMARY KEY';
            $name = $primary ? 'PRIMARY' : self::dbaIndexName( $table, $row['index_name'] );
            if ( !isset( $indexes[$name] ) )
                $indexes[$name] = array( 'type' => $primary ? 'primary' : ( strpos( $constraint, 'UNIQUE' ) !== false ? 'unique' : 'non-unique' ), 'fields' => array() );
            $indexes[$name]['fields'][] = strtolower( $row['field_name'] );
        }
        if ( !empty( $params['sort_indexes'] ) )
            ksort( $indexes );
        return $indexes;
    }
}
