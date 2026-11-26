<?php
/**
 * File containing the expFirebirdSchema class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The schema handler of Firebird (expFirebirdDB): the database-independent schema (.dba) as Firebird DDL, and a
 * Firebird database read back into that schema (for the upgrade check and ezsqldumpschema.php).
 *
 * Types: int INTEGER, bigint BIGINT, tinyint/smallint SMALLINT, auto_increment INTEGER with a sequence and a trigger
 * (below), varchar(n) VARCHAR(n), char(n) CHAR(n), text and longtext BLOB SUB_TYPE TEXT, float FLOAT, double DOUBLE
 * PRECISION, decimal DECIMAL, timestamp/datetime TIMESTAMP, date DATE, time TIME, blob/longblob BLOB. Text is
 * UTF8 (the database's character set).
 *
 * Names: tables, columns and indexes unquoted (stored in upper case), a reserved word ("VALUE") quoted in upper case;
 * a primary key is the constraint PK_<table>.
 *
 * auto_increment: the sequence S_<table> (its description names the column) and the trigger T_<table>_BI, which
 * numbers a row that comes without id, NULL or 0, moves the sequence past an id an insert gives itself (so the
 * rows of the data files and the copies the kernel makes keep their ids), and keeps the id in the context variables
 * EXP_ID.<TABLE> and EXP_ID of the connection (expFirebirdDB::lastSerialID()).
 *
 * Index keys: Firebird indexes at most a quarter of a page per key (4,087 bytes with a page of 16 KB), counts four
 * bytes per character of UTF8, and adds a byte per four of a key of several columns. A BLOB cannot be a key. So a
 * text column in an index is VARCHAR(n) with the n that fits every index it is in (811 characters for the URL alias action
 * text with a page of 16 KB; the column's description says it was text), and a non-unique index that is still too
 * long keeps the columns that fit. The page size is read from the database when there is a connection, else 16 KB.
 */
class expFirebirdSchema extends eZDBSchemaInterface
{
    /** The page size assumed without a connection (CREATE DATABASE ... PAGE_SIZE 16384) */
    const DEFAULT_PAGE_SIZE = 16384;

    /** The description of a VARCHAR column that is text in the .dba */
    const TEXT_MARK = 'exp:longtext';

    /** @var string[] notes of the last generation (indexes changed to fit Firebird's limits) */
    public $Notes = array();

    /** @var int|null the page size of the connected database */
    protected $PageSize = null;

    function schemaType()
    {
        return 'firebird';
    }

    function schemaName()
    {
        return 'Firebird';
    }

    // ------------------------------------------------------------------ names

    function quoteSchemaIdentifier( $name )
    {
        return expFirebirdDialect::quoteIdentifierIfNeeded( $name );
    }

    function insertColumnName( $name )
    {
        return $this->quoteSchemaIdentifier( $name );
    }

    function reservedKeywordList()
    {
        return array_map( 'strtolower', expFirebirdDialect::RESERVED_WORDS );
    }

    /**
     * A name of the schema made of a prefix and a table name, within 63 characters (a long table name is cut and
     * given a hash, so two long names stay apart).
     *
     * @param string $prefix
     * @param string $table
     * @param string $suffix
     * @return string
     */
    public static function objectName( $prefix, $table, $suffix = '' )
    {
        $name = expFirebirdDialect::upper( $prefix . $table . $suffix );
        if ( strlen( $name ) <= expFirebirdDialect::MAX_IDENTIFIER_LENGTH )
            return $name;
        $hash = strtoupper( substr( md5( $table ), 0, 8 ) );
        return expFirebirdDialect::upper( substr( $prefix . $table, 0, expFirebirdDialect::MAX_IDENTIFIER_LENGTH - strlen( $suffix ) - 9 ) . '_' . $hash . $suffix );
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
                $n = (int)$length > 0 ? (int)$length : 255;
                return $n > 8191 ? 'BLOB SUB_TYPE TEXT' : "VARCHAR($n)";
            case 'char':
                $n = (int)$length > 0 ? (int)$length : 1;
                return $n > 8191 ? 'BLOB SUB_TYPE TEXT' : "CHAR($n)";
            case 'tinytext':
            case 'text':
            case 'mediumtext':
            case 'longtext':
                return 'BLOB SUB_TYPE TEXT';
            case 'float':
                return 'FLOAT';
            case 'double':
                return 'DOUBLE PRECISION';
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
                return 'BLOB SUB_TYPE BINARY';
        }
        throw new RuntimeException( "Unhandled column type: $type" );
    }

    /**
     * The .dba type of a Firebird column (RDB$FIELDS: type code, sub type, scale, precision, character length).
     *
     * @return string
     */
    function convertToStandardType( $typeCode, $subType, $scale, $precision, $charLength, &$length )
    {
        $length = null;
        $typeCode = (int)$typeCode;
        if ( in_array( $typeCode, array( 7, 8, 16, 26 ), true ) && ( (int)$subType > 0 || (int)$scale < 0 ) )
        {
            $length = ( (int)$precision > 0 ? (int)$precision : 18 ) . ',' . abs( (int)$scale );
            return 'decimal';
        }
        switch ( $typeCode )
        {
            case 7:
                $length = 6;
                return 'int';
            case 8:
                $length = 11;
                return 'int';
            case 16:
                $length = 20;
                return 'bigint';
            case 10:
                return 'float';
            case 27:
                return 'double';
            case 37:
                $length = (int)$charLength;
                return 'varchar';
            case 14:
                $length = (int)$charLength;
                return 'char';
            case 261:
                return (int)$subType === 1 ? 'longtext' : 'longblob';
            case 35:
            case 29:
                return 'timestamp';
            case 12:
                return 'date';
            case 13:
            case 28:
                return 'time';
            case 23:
                $length = 1;
                return 'int';
        }
        throw new RuntimeException( "Unhandled column type code: $typeCode" );
    }

    // ------------------------------------------------------------------ index keys

    /**
     * The page size of the connected database, or 16 KB.
     *
     * @return int
     */
    function pageSize()
    {
        if ( $this->PageSize === null )
        {
            $this->PageSize = self::DEFAULT_PAGE_SIZE;
            if ( $this->DBInstance instanceof expFirebirdDB && $this->DBInstance->isConnected() )
            {
                $rows = $this->DBInstance->arrayQuery( 'SELECT MON$PAGE_SIZE AS page_size FROM MON$DATABASE' );
                if ( is_array( $rows ) && isset( $rows[0]['page_size'] ) && (int)$rows[0]['page_size'] > 0 )
                    $this->PageSize = (int)$rows[0]['page_size'];
            }
        }
        return $this->PageSize;
    }

    /**
     * @param int $pageSize
     */
    function setPageSize( $pageSize )
    {
        $this->PageSize = (int)$pageSize;
    }

    /**
     * The longest key an index may have, in bytes: a quarter of the page less 9.
     *
     * @return int
     */
    function maxKeyBytes()
    {
        return (int)( $this->pageSize() / 4 ) - 9;
    }

    /**
     * The bytes of a column's value in a key: four per character of UTF8, eight for a number or a date (Firebird keys
     * every number as a double precision value, a BIGINT a little longer); null for a BLOB.
     *
     * @param array $def
     * @return int|null
     */
    function keyBytes( array $def )
    {
        $sqlType = $this->convertFromStandardType( $def['type'], isset( $def['length'] ) ? $def['length'] : null );
        if ( strpos( $sqlType, 'BLOB' ) === 0 )
            return null;
        if ( preg_match( '/^(?:VAR)?CHAR\((\d+)\)$/', $sqlType, $m ) )
            return 4 * (int)$m[1];
        $sizes = array( 'INTEGER' => 8, 'BIGINT' => 10, 'SMALLINT' => 8, 'FLOAT' => 8, 'DOUBLE PRECISION' => 8, 'TIMESTAMP' => 8,
                        'DATE' => 8, 'TIME' => 8 );
        return isset( $sizes[$sqlType] ) ? $sizes[$sqlType] : 16;
    }

    /**
     * The key length of an index of columns of $bytes each: a key of several columns stores five bytes per four.
     *
     * @param int[] $bytes
     * @return int
     */
    static function keyCost( array $bytes )
    {
        if ( count( $bytes ) <= 1 )
            return (int)array_sum( $bytes );
        $cost = 0;
        foreach ( $bytes as $b )
            $cost += 5 * (int)ceil( $b / 4 );
        return $cost;
    }

    /**
     * The fields of a table with every text column that is a key column of an index as VARCHAR(n), n the most
     * characters that fit every index the column is in.
     *
     * @param string $table
     * @param array $tableDef
     * @return array the fields
     */
    function indexedTextFields( $table, array $tableDef )
    {
        $fields = $tableDef['fields'];
        $budget = $this->maxKeyBytes();
        $lengths = array();
        foreach ( isset( $tableDef['indexes'] ) ? $tableDef['indexes'] : array() as $index => $def )
        {
            $names = array();
            foreach ( $def['fields'] as $fieldDef )
                $names[] = is_array( $fieldDef ) ? $fieldDef['name'] : $fieldDef;
            foreach ( $names as $name )
            {
                if ( !isset( $fields[$name] ) || !in_array( $fields[$name]['type'], array( 'tinytext', 'text', 'mediumtext', 'longtext' ), true ) )
                    continue;
                $other = array();
                foreach ( $names as $o )
                    if ( $o !== $name && isset( $fields[$o] ) )
                        $other[] = (int)$this->keyBytes( in_array( $fields[$o]['type'], array( 'tinytext', 'text', 'mediumtext', 'longtext' ), true )
                                                         ? array( 'type' => 'int' ) : $fields[$o] );
                if ( count( $names ) === 1 )
                    $n = intdiv( $budget, 4 );
                else
                    $n = intdiv( $budget - self::keyCost( array_merge( $other, array( 4 ) ) ) + 5, 5 );
                $lengths[$name] = isset( $lengths[$name] ) ? min( $lengths[$name], $n ) : $n;
            }
        }
        foreach ( $lengths as $name => $n )
        {
            $fields[$name]['type'] = 'varchar';
            $fields[$name]['length'] = max( 1, $n );
            $fields[$name]['firebird:indexed_text'] = true;
            $this->Notes[] = "$table.$name: text in the key of an index, VARCHAR($n)";
        }
        return $fields;
    }

    /**
     * The key columns of an index that fit: a non-unique index keeps the leading columns within the key length;
     * a primary or unique one keeps all (Firebird refuses it when it is too long, and that is reported).
     *
     * @return array( string[] columns, string[] notes )
     */
    function fittingIndexColumns( $table, $index, array $indexDef, array $fields )
    {
        $budget = $this->maxKeyBytes();
        $key = array();
        $bytes = array();
        $notes = array();
        foreach ( $indexDef['fields'] as $fieldDef )
        {
            $name = is_array( $fieldDef ) ? $fieldDef['name'] : $fieldDef;
            $size = isset( $fields[$name] ) ? $this->keyBytes( $fields[$name] ) : 8;
            if ( $size === null )
            {
                $notes[] = "$table.$index: $name is a BLOB, which cannot be an index key: left out";
                continue;
            }
            if ( $indexDef['type'] === 'non-unique' && self::keyCost( array_merge( $bytes, array( $size ) ) ) > $budget )
            {
                $notes[] = "$table.$index: $name would take the key past $budget bytes: left out";
                continue;
            }
            $bytes[] = $size;
            $key[] = $name;
        }
        if ( $indexDef['type'] !== 'non-unique' && self::keyCost( $bytes ) > $budget )
            $notes[] = "$table.$index: the key may take " . self::keyCost( $bytes ) . " bytes, more than the $budget of the page size";
        return array( $key, $notes );
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
                return is_numeric( $value ) ? expFirebirdDialect::literal( (float)$value ) : ( $value === '' ? '0' : 'NULL' );
            case 'blob':
            case 'mediumblob':
            case 'longblob':
                return "X'" . bin2hex( (string)$value ) . "'";
        }
        return expFirebirdDialect::stringLiteral( (string)$value );
    }

    function generateDefaultDef( $table, $field, $def )
    {
        if ( $def['type'] === 'auto_increment' || !array_key_exists( 'default', $def ) || $def['default'] === false )
            return '';
        if ( $def['default'] === '' && in_array( $def['type'], array( 'timestamp', 'datetime', 'date', 'time' ), true ) )
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
     * The statements of a sequence numbered column: the sequence, its description and the trigger.
     *
     * @param string $table
     * @param string $field
     * @return string[]
     */
    function autoIncrementSQLList( $table, $field )
    {
        $sequence = $this->sequenceName( $table );
        $column = 'NEW.' . $this->quoteSchemaIdentifier( $field );
        $context = expFirebirdDB::ID_CONTEXT . '.' . expFirebirdDialect::upper( $table );
        return array(
            "CREATE SEQUENCE $sequence",
            "COMMENT ON SEQUENCE $sequence IS " . expFirebirdDialect::stringLiteral( $field ),
            'CREATE TRIGGER ' . $this->triggerName( $table ) . ' FOR ' . $this->quoteSchemaIdentifier( $table ) . " ACTIVE BEFORE INSERT POSITION 0 AS\n"
          . "DECLARE VARIABLE moved BIGINT;\n"
          . "BEGIN\n"
          . "  IF ($column IS NULL OR $column = 0) THEN\n"
          . "    $column = NEXT VALUE FOR $sequence;\n"
          . "  ELSE IF ($column > GEN_ID($sequence, 0)) THEN\n"
          . "    moved = GEN_ID($sequence, $column - GEN_ID($sequence, 0));\n"
          . "  RDB\$SET_CONTEXT('USER_SESSION', " . expFirebirdDialect::stringLiteral( $context ) . ", $column);\n"
          . "  RDB\$SET_CONTEXT('USER_SESSION', '" . expFirebirdDB::ID_CONTEXT . "', $column);\n"
          . 'END',
        );
    }

    function generateAddIndexSql( $table, $index, $def, $params, $withClosure = true, $fields = array() )
    {
        // an upgrade adds an index to a table that exists: its column types come from the database
        if ( !$fields && $this->DBInstance instanceof expFirebirdDB && $this->DBInstance->isConnected() )
        {
            try
            {
                $fields = $this->fetchTableFields( $table, array() );
            }
            catch ( RuntimeException $e )
            {
                $fields = array();
            }
        }
        list( $key, $notes ) = $this->fittingIndexColumns( $table, $index, $def, $fields );
        $this->Notes = array_merge( $this->Notes, $notes );
        if ( !$key )
        {
            $this->Notes[] = "$table.$index: no column can be a key, the index is not made";
            return '';
        }
        $columns = implode( ', ', array_map( array( $this, 'quoteSchemaIdentifier' ), $key ) );
        if ( $def['type'] === 'primary' )
            $sql = 'ALTER TABLE ' . $this->quoteSchemaIdentifier( $table ) . ' ADD CONSTRAINT ' . $this->primaryKeyName( $table ) . " PRIMARY KEY ($columns)";
        else
            $sql = 'CREATE ' . ( $def['type'] === 'unique' ? 'UNIQUE ' : '' ) . 'INDEX ' . $this->indexName( $table, $index )
                 . ' ON ' . $this->quoteSchemaIdentifier( $table ) . " ($columns)";
        // the .dba's name of a renamed index, for the read back (only in an upgrade: the table's DDL adds it itself)
        if ( $def['type'] !== 'primary' && $withClosure && ( $comment = $this->indexComment( $table, $index ) ) !== null )
            $sql .= ";\n" . $comment;
        return $sql . ( $withClosure ? ";\n" : '' );
    }

    /**
     * An index name of the .dba as it may stand in Firebird (index names are unique in the whole database there).
     *
     * @param string $table
     * @param string $index
     * @return string
     */
    function indexName( $table, $index )
    {
        return expFirebirdDialect::quoteIdentifierIfNeeded( $this->storedIndexName( $table, $index ) );
    }

    /**
     * The name an index of the .dba gets in Firebird, where index names are unique in the whole database while the
     * .dba (as MySQL) names them per table (cjw_newsletter has edition_send_id on two tables): a name that does not
     * start with its table's name gets the table's name in front, a long one is cut with a hash. The .dba's name is
     * kept as the index's description (indexComment()), so the schema reads back as it was written.
     *
     * @param string $table
     * @param string $index
     * @return string
     */
    function storedIndexName( $table, $index )
    {
        $name = (string)$index;
        if ( stripos( $name, $table . '_' ) !== 0 && strcasecmp( $name, $table ) !== 0 )
            $name = $table . '_' . $name;
        if ( strlen( $name ) > expFirebirdDialect::MAX_IDENTIFIER_LENGTH || expFirebirdDialect::isReservedWord( $name ) )
            $name = self::objectName( 'I_', $table, '_' . substr( md5( $index ), 0, 6 ) );
        return $name;
    }

    /**
     * The statement that keeps the .dba's name of a renamed index as its description; null when the name is kept.
     *
     * @param string $table
     * @param string $index
     * @return string|null
     */
    function indexComment( $table, $index )
    {
        $stored = $this->storedIndexName( $table, $index );
        if ( strcasecmp( $stored, (string)$index ) === 0 )
            return null;
        return 'COMMENT ON INDEX ' . expFirebirdDialect::quoteIdentifierIfNeeded( $stored ) . ' IS ' . expFirebirdDialect::stringLiteral( 'exp:index:' . $index );
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
        $sql = 'ALTER TABLE ' . $this->quoteSchemaIdentifier( $table ) . ' ADD ' . $this->generateFieldDef( $table, $field, $def ) . ";\n";
        if ( $def['type'] === 'auto_increment' )
            foreach ( $this->autoIncrementSQLList( $table, $field ) as $statement )
                $sql .= $statement . ";\n";
        return $sql;
    }

    function generateDropFieldSql( $table, $field, $params )
    {
        return 'ALTER TABLE ' . $this->quoteSchemaIdentifier( $table ) . ' DROP ' . $this->quoteSchemaIdentifier( $field ) . ";\n";
    }

    function generateAlterFieldSql( $table, $field, $def, $params )
    {
        $t = $this->quoteSchemaIdentifier( $table );
        $f = $this->quoteSchemaIdentifier( $field );
        $sql = "ALTER TABLE $t ALTER COLUMN $f TYPE " . $this->convertFromStandardType( $def['type'], isset( $def['length'] ) ? $def['length'] : null ) . ";\n";
        if ( $def['type'] === 'auto_increment' )
            return $sql;
        $default = $this->generateDefaultDef( $table, $field, $def );
        $sql .= "ALTER TABLE $t ALTER COLUMN $f " . ( $default !== '' ? 'SET ' . $default : 'DROP DEFAULT' ) . ";\n";
        if ( !empty( $def['not_null'] ) && $default !== '' )
            $sql .= "UPDATE $t SET $f = " . substr( $default, 8 ) . " WHERE $f IS NULL;\n";
        $sql .= "ALTER TABLE $t ALTER COLUMN $f " . ( !empty( $def['not_null'] ) ? 'SET' : 'DROP' ) . " NOT NULL;\n";
        return $sql;
    }

    /**
     * The statements of one table: array( 'tables' => ..., 'constraints' => ..., 'indexes' => ..., 'sequences' => ... ).
     */
    function generateTableArrays( $table, $tableDef, $params, $withClosure = true )
    {
        $tableDef['fields'] = $this->indexedTextFields( $table, $tableDef );
        $end = $withClosure ? ';' : '';
        $arrays = array( 'tables' => array(), 'constraints' => array(), 'indexes' => array(), 'sequences' => array(), 'comments' => array() );
        $lines = array();
        foreach ( $tableDef['fields'] as $field => $def )
        {
            $lines[] = '  ' . $this->generateFieldDef( $table, $field, $def );
            if ( !empty( $def['firebird:indexed_text'] ) )
                $arrays['comments'][] = 'COMMENT ON COLUMN ' . $this->quoteSchemaIdentifier( $table ) . '.' . $this->quoteSchemaIdentifier( $field )
                                      . ' IS ' . expFirebirdDialect::stringLiteral( self::TEXT_MARK ) . $end;
            if ( $def['type'] === 'auto_increment' )
                foreach ( $this->autoIncrementSQLList( $table, $field ) as $statement )
                    $arrays['sequences'][] = $statement . $end;
        }
        $arrays['tables'][] = 'CREATE TABLE ' . $this->quoteSchemaIdentifier( $table ) . " (\n" . implode( ",\n", $lines ) . "\n)" . $end;
        foreach ( isset( $tableDef['indexes'] ) ? $tableDef['indexes'] : array() as $index => $def )
        {
            $sql = $this->generateAddIndexSql( $table, $index, $def, $params, false, $tableDef['fields'] );
            if ( $sql === '' )
                continue;
            if ( $def['type'] === 'primary' )
                $arrays['constraints'][] = $sql . $end;
            else
            {
                $arrays['indexes'][] = $sql . $end;
                $comment = $this->indexComment( $table, $index );
                if ( $comment !== null )
                    $arrays['indexes'][] = $comment . $end;
            }
        }
        return $arrays;
    }

    function generateTableSQLList( $table, $tableDef, $params, $separateTypes )
    {
        $arrays = $this->generateTableArrays( $table, $tableDef, $params, false );
        if ( $separateTypes )
            return $arrays;
        return array_merge( $arrays['tables'], $arrays['comments'], $arrays['constraints'], $arrays['indexes'], $arrays['sequences'] );
    }

    /**
     * The DDL of one table as an isql script: the trigger between SET TERM, since its body holds semicolons.
     */
    function generateTableSchema( $table, $tableDef, $params )
    {
        $arrays = $this->generateTableArrays( $table, $tableDef, $params, false );
        $out = array();
        foreach ( array_merge( $arrays['tables'], $arrays['comments'], $arrays['constraints'], $arrays['indexes'] ) as $sql )
            $out[] = $sql . ';';
        foreach ( $arrays['sequences'] as $sql )
            $out[] = strpos( $sql, 'CREATE TRIGGER' ) === 0 ? "SET TERM ^ ;\n$sql^\nSET TERM ; ^" : $sql . ';';
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
        return expFirebirdDialect::escapeString( $value );
    }

    // ------------------------------------------------------------------ read back

    /**
     * The schema of the connected Firebird database, in the database-independent form.
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
                if ( $this->isTemporaryTable( $table ) )
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
     * Whether a table is a global temporary table (the driver's CREATE TEMPORARY TABLE): not part of the schema.
     *
     * @param string $table
     * @return bool
     */
    function isTemporaryTable( $table )
    {
        $rows = $this->DBInstance->arrayQuery( 'SELECT RDB$RELATION_TYPE AS relation_type FROM RDB$RELATIONS WHERE RDB$RELATION_NAME = '
                                             . expFirebirdDialect::stringLiteral( expFirebirdDialect::upper( $table ) ) );
        return is_array( $rows ) && isset( $rows[0]['relation_type'] ) && in_array( (int)$rows[0]['relation_type'], array( 4, 5 ), true );
    }

    /**
     * The column numbered by a sequence of the schema handler, or null.
     *
     * @param string $table
     * @return string|null
     */
    function autoIncrementColumn( $table )
    {
        $rows = $this->DBInstance->arrayQuery( 'SELECT CAST(RDB$DESCRIPTION AS VARCHAR(255)) AS description FROM RDB$GENERATORS WHERE RDB$GENERATOR_NAME = '
                                             . expFirebirdDialect::stringLiteral( $this->sequenceName( $table ) ) );
        if ( !is_array( $rows ) || !isset( $rows[0]['description'] ) || $rows[0]['description'] === null )
            return null;
        return strtolower( trim( $rows[0]['description'] ) );
    }

    /**
     * The fields of a table in schema form.
     */
    function fetchTableFields( $table, $params )
    {
        $fields = array();
        $auto = $this->autoIncrementColumn( $table );
        $rows = $this->DBInstance->arrayQuery(
            'SELECT LOWER(TRIM(rf.RDB$FIELD_NAME)) AS name, f.RDB$FIELD_TYPE AS type_code, f.RDB$FIELD_SUB_TYPE AS sub_type,'
          . ' f.RDB$FIELD_SCALE AS scale, f.RDB$FIELD_PRECISION AS numeric_precision, f.RDB$CHARACTER_LENGTH AS char_len,'
          . ' COALESCE(rf.RDB$NULL_FLAG, f.RDB$NULL_FLAG, 0) AS not_null,'
          . ' CAST(COALESCE(rf.RDB$DEFAULT_SOURCE, f.RDB$DEFAULT_SOURCE) AS VARCHAR(1000)) AS default_source,'
          . ' CAST(rf.RDB$DESCRIPTION AS VARCHAR(255)) AS description'
          . ' FROM RDB$RELATION_FIELDS rf JOIN RDB$FIELDS f ON f.RDB$FIELD_NAME = rf.RDB$FIELD_SOURCE'
          . ' WHERE rf.RDB$RELATION_NAME = ' . expFirebirdDialect::stringLiteral( expFirebirdDialect::upper( $table ) )
          . ' ORDER BY rf.RDB$FIELD_POSITION' );
        foreach ( (array)$rows as $row )
        {
            $name = $row['name'];
            $field = array();
            if ( $auto !== null && $name === $auto )
            {
                $field['type'] = 'auto_increment';
                $field['default'] = false;
            }
            else
            {
                $type = $this->convertToStandardType( $row['type_code'], $row['sub_type'], $row['scale'], $row['numeric_precision'], $row['char_len'], $length );
                if ( $type === 'varchar' && trim( (string)$row['description'] ) === self::TEXT_MARK )
                {
                    $type = 'longtext';
                    $length = null;
                }
                $field['type'] = $type;
                if ( $length !== null )
                    $field['length'] = $length;
                $field['not_null'] = (string)$row['not_null'] === '1' ? '1' : '0';
                $default = $this->parseDefault( $row['default_source'] );
                // no DEFAULT clause: no default (a nullable column of the .dba with default NULL has DEFAULT NULL)
                $field['default'] = $default;
            }
            $fields[$name] = $field;
        }
        if ( !empty( $params['sort_columns'] ) )
            ksort( $fields );
        return $fields;
    }

    /**
     * The value of a DEFAULT clause of the catalogue ("DEFAULT 0", "DEFAULT ''", "DEFAULT NULL"); false without one.
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

    /**
     * The indexes of a table in schema form: PRIMARY for the primary key.
     */
    function fetchTableIndexes( $table, $params )
    {
        $rows = $this->DBInstance->arrayQuery(
            'SELECT LOWER(TRIM(i.RDB$INDEX_NAME)) AS index_name, CAST(i.RDB$DESCRIPTION AS VARCHAR(255)) AS description, i.RDB$UNIQUE_FLAG AS is_unique, LOWER(TRIM(s.RDB$FIELD_NAME)) AS field_name,'
          . ' s.RDB$FIELD_POSITION AS position_no, TRIM(c.RDB$CONSTRAINT_TYPE) AS constraint_type'
          . ' FROM RDB$INDICES i JOIN RDB$INDEX_SEGMENTS s ON s.RDB$INDEX_NAME = i.RDB$INDEX_NAME'
          . ' LEFT JOIN RDB$RELATION_CONSTRAINTS c ON c.RDB$INDEX_NAME = i.RDB$INDEX_NAME'
          . ' WHERE i.RDB$RELATION_NAME = ' . expFirebirdDialect::stringLiteral( expFirebirdDialect::upper( $table ) )
          . ' AND COALESCE(i.RDB$SYSTEM_FLAG, 0) = 0 ORDER BY 1, 5' );
        $indexes = array();
        foreach ( (array)$rows as $row )
        {
            $primary = $row['constraint_type'] === 'PRIMARY KEY';
            $name = $primary ? 'PRIMARY' : $row['index_name'];
            if ( !$primary && $row['description'] !== null && strpos( (string)$row['description'], 'exp:index:' ) === 0 )
                $name = substr( (string)$row['description'], 10 );
            if ( !isset( $indexes[$name] ) )
                $indexes[$name] = array( 'type' => $primary ? 'primary' : ( (string)$row['is_unique'] === '1' ? 'unique' : 'non-unique' ), 'fields' => array() );
            $indexes[$name]['fields'][] = $row['field_name'];
        }
        if ( !empty( $params['sort_indexes'] ) )
            ksort( $indexes );
        return $indexes;
    }
}
