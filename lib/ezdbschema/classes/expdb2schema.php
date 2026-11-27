<?php
/**
 * File containing the expDb2Schema class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The schema handler of Db2 (expDb2DB): the database-independent schema (.dba) as Db2 DDL, and a Db2 database read
 * back into that schema (for the upgrade check and ezsqldumpschema.php).
 *
 * Types: int INTEGER, bigint BIGINT, tinyint/smallint SMALLINT, auto_increment INTEGER with a sequence and a trigger
 * (below), varchar(n) and char(n) VARCHAR(n CODEUNITS32) (n characters, as MySQL counts them; a char's description
 * says it was one), text and longtext VARCHAR(32672) (bytes; Db2's longest text that compares, sorts and takes
 * DISTINCT like a string) or, for the columns that hold documents of any length (LARGE_TEXT_COLUMNS), CLOB(1G),
 * float REAL, double DOUBLE, decimal DECIMAL, timestamp/datetime TIMESTAMP, date DATE, time TIME, blob BLOB(1G).
 *
 * Names: tables, columns and indexes unquoted (stored in upper case), a reserved word ("USER") quoted in upper
 * case; a primary key is the constraint PK_<table>.
 *
 * auto_increment: the sequence S_<table> (its description is table.column) and the trigger T_<table>_BI, which
 * numbers a row that comes without id, NULL or 0. expDb2DB::lastSerialID() reads PREVIOUS VALUE FOR the sequence,
 * expDb2DB::correctSequenceValues() moves the sequences past the ids a data file brought.
 *
 * Index keys: Db2 keys at most a quarter of a page (8 KB with pages of 32 KB, which an Exponential database needs:
 * CREATE DATABASE ... PAGESIZE 32 K), and counts a VARCHAR(n CODEUNITS32) as 4n bytes. A text column in an index
 * is VARCHAR(n CODEUNITS32) with the n that fits every index it is in (its description says it was text), and a
 * non-unique index that is still too long keeps the columns that fit.
 *
 * @since 6.0.15
 * @package lib
 */
class expDb2Schema extends eZDBSchemaInterface
{
    /** The page size assumed without a connection (CREATE DATABASE ... PAGESIZE 32 K) */
    const DEFAULT_PAGE_SIZE = 32768;

    /** The length in bytes of a text column (longtext) that is no CLOB */
    const TEXT_LENGTH = 32672;

    /** The longest VARCHAR(n CODEUNITS32) */
    const MAX_VARCHAR_CHARACTERS = 8168;

    /** The description of a column that is text in the .dba but VARCHAR here */
    const TEXT_MARK = 'exp:longtext';

    /** The description of a column that is char(n) in the .dba */
    const CHAR_MARK = 'exp:char';

    /** The description of a column that is tinyint in the .dba */
    const TINYINT_MARK = 'exp:tinyint';

    /**
     * The text columns that hold documents of any length: CLOB(1G). A CLOB compares with = and LIKE, but cannot be
     * sorted, grouped, taken DISTINCT or indexed, which these columns never are.
     *
     * @var string[]
     */
    const LARGE_TEXT_COLUMNS = array(
        'ezcontentobject_attribute.data_text', 'ezinfocollection_attribute.data_text', 'ezsession.data',
        'ezoperation_memento.memento_data', 'ezworkflow_process.parameters', 'ezmodule_run.module_data',
        'ezmessage.body', 'expaudit_event.record', 'exp_migration_recording_event.payload',
        'exp_urlmap_batch_item.before_image', 'exp_urlmap_batch_item.after_image',
        'exp_data_sync_conflict.content_values', 'exp_data_sync_conflict.row_values',
        'expuser_audit.value_before', 'expuser_audit.value_after', 'eznotificationcollection.data_text',
    );

    /** The description of a CLOB column (its .dba type) */
    const CLOB_MARK = 'exp:clob';

    /** The dialect class */
    const DIALECT = 'expDb2Dialect';

    /** @var string[] notes of the last generation (indexes changed to fit the key limit) */
    public $Notes = array();

    /** @var int|null the page size of the connected database */
    protected $PageSize = null;

    function schemaType()
    {
        return 'db2';
    }

    function schemaName()
    {
        return 'Db2';
    }

    // ------------------------------------------------------------------ names

    function quoteSchemaIdentifier( $name )
    {
        $dialect = static::DIALECT;
        return $dialect::quoteIdentifierIfNeeded( $name );
    }

    function insertColumnName( $name )
    {
        return $this->quoteSchemaIdentifier( $name );
    }

    function reservedKeywordList()
    {
        $dialect = static::DIALECT;
        return array_map( 'strtolower', $dialect::RESERVED_WORDS );
    }

    /**
     * A name of the schema made of a prefix and a table name, within the longest name (a long table name is cut and
     * given a hash, so two long names stay apart), in the case of an unquoted name.
     *
     * @param string $prefix
     * @param string $table
     * @param string $suffix
     * @return string
     */
    public static function objectName( $prefix, $table, $suffix = '' )
    {
        $dialect = static::DIALECT;
        $name = $dialect::fold( $prefix . $table . $suffix );
        if ( strlen( $name ) <= $dialect::MAX_IDENTIFIER_LENGTH )
            return $name;
        $hash = strtoupper( substr( md5( $table ), 0, 8 ) );
        return $dialect::fold( substr( $prefix . $table, 0, $dialect::MAX_IDENTIFIER_LENGTH - strlen( $suffix ) - 9 ) . '_' . $hash . $suffix );
    }

    function primaryKeyName( $table )
    {
        return static::objectName( 'PK_', $table );
    }

    function sequenceName( $table )
    {
        return static::objectName( 'S_', $table );
    }

    function triggerName( $table )
    {
        return static::objectName( 'T_', $table, '_BI' );
    }

    // ------------------------------------------------------------------ types

    /**
     * Whether a text column of the .dba is a CLOB here.
     *
     * @param string $table
     * @param string $field
     * @return bool
     */
    static function isLargeText( $table, $field )
    {
        return in_array( strtolower( $table . '.' . $field ), static::LARGE_TEXT_COLUMNS, true );
    }

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
                return $n > self::MAX_VARCHAR_CHARACTERS ? 'VARCHAR(' . self::TEXT_LENGTH . ')' : "VARCHAR($n CODEUNITS32)";
            case 'tinytext':
            case 'text':
            case 'mediumtext':
            case 'longtext':
                return 'VARCHAR(' . self::TEXT_LENGTH . ')';
            case 'clob':
                return 'CLOB(1G)';
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
                return 'BLOB(1G)';
        }
        throw new RuntimeException( "Unhandled column type: $type" );
    }

    /**
     * The .dba type of a Db2 column (SYSCAT.COLUMNS: TYPENAME, LENGTH, SCALE, STRINGUNITSLENGTH, TYPESTRINGUNITS)
     * and its description (the marks of the schema handler).
     *
     * @return string
     */
    function convertToStandardType( $typeName, $lengthBytes, $scale, $unitsLength, $units, $remarks, &$length )
    {
        $length = null;
        $remarks = trim( (string)$remarks );
        switch ( strtoupper( trim( (string)$typeName ) ) )
        {
            case 'SMALLINT':
                if ( $remarks === self::TINYINT_MARK )
                {
                    $length = 4;
                    return 'int';
                }
                $length = 6;
                return 'int';
            case 'INTEGER':
                $length = 11;
                return 'int';
            case 'BIGINT':
                $length = 20;
                return 'bigint';
            case 'REAL':
                return 'float';
            case 'DOUBLE':
                return 'double';
            case 'DECIMAL':
                $length = (int)$lengthBytes . ',' . (int)$scale;
                return 'decimal';
            case 'VARCHAR':
            case 'CHARACTER':
            case 'CHAR':
                if ( $remarks === self::TEXT_MARK || (int)$lengthBytes === self::TEXT_LENGTH )
                    return 'longtext';
                $length = strtoupper( (string)$units ) === 'CODEUNITS32' && (int)$unitsLength > 0 ? (int)$unitsLength : (int)$lengthBytes;
                return $remarks === self::CHAR_MARK ? 'char' : 'varchar';
            case 'CLOB':
            case 'DBCLOB':
                return 'longtext';
            case 'TIMESTAMP':
                return 'timestamp';
            case 'DATE':
                return 'date';
            case 'TIME':
                return 'time';
            case 'BLOB':
                return 'longblob';
            case 'BOOLEAN':
                $length = 1;
                return 'int';
        }
        throw new RuntimeException( "Unhandled column type: $typeName" );
    }

    // ------------------------------------------------------------------ index keys

    /**
     * The page size of the connected database's default tablespace, or 32 KB.
     *
     * @return int
     */
    function pageSize()
    {
        if ( $this->PageSize === null )
        {
            $this->PageSize = self::DEFAULT_PAGE_SIZE;
            if ( $this->DBInstance instanceof expDb2DB && $this->DBInstance->isConnected() )
            {
                $rows = $this->DBInstance->arrayQuery( "SELECT PAGESIZE AS page_size FROM SYSCAT.TABLESPACES WHERE TBSPACE = 'USERSPACE1'" );
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
     * The longest key an index may have, in bytes: a quarter of the page, less room for the overhead.
     *
     * @return int
     */
    function maxKeyBytes()
    {
        return (int)( $this->pageSize() / 4 ) - 100;
    }

    /**
     * The bytes of a column's value in a key: 4 per character of a VARCHAR(n CODEUNITS32) and 2 for its length,
     * the size of a number or a date; null for a CLOB or BLOB.
     *
     * @param array $def
     * @return int|null
     */
    function keyBytes( array $def )
    {
        $sqlType = $this->convertFromStandardType( $def['type'], isset( $def['length'] ) ? $def['length'] : null );
        if ( strpos( $sqlType, 'CLOB' ) === 0 || strpos( $sqlType, 'BLOB' ) === 0 )
            return null;
        if ( preg_match( '/^VARCHAR\((\d+) CODEUNITS32\)$/', $sqlType, $m ) )
            return 4 * (int)$m[1] + 2;
        if ( preg_match( '/^VARCHAR\((\d+)\)$/', $sqlType, $m ) )
            return (int)$m[1] + 2;
        $sizes = array( 'INTEGER' => 4, 'BIGINT' => 8, 'SMALLINT' => 2, 'REAL' => 4, 'DOUBLE' => 8, 'TIMESTAMP' => 10, 'DATE' => 4, 'TIME' => 3 );
        return isset( $sizes[$sqlType] ) ? $sizes[$sqlType] : 16;
    }

    /**
     * The fields of a table with every text column that is a key column of an index as VARCHAR(n CODEUNITS32), n
     * the most characters that fit every index the column is in, and the large text columns as CLOB.
     *
     * @param string $table
     * @param array $tableDef
     * @return array the fields
     */
    function indexedTextFields( $table, array $tableDef )
    {
        $fields = $tableDef['fields'];
        $textTypes = array( 'tinytext', 'text', 'mediumtext', 'longtext' );
        $budget = $this->maxKeyBytes();
        $lengths = array();
        foreach ( isset( $tableDef['indexes'] ) ? $tableDef['indexes'] : array() as $index => $def )
        {
            $names = array();
            foreach ( $def['fields'] as $fieldDef )
                $names[] = is_array( $fieldDef ) ? $fieldDef['name'] : $fieldDef;
            foreach ( $names as $name )
            {
                if ( !isset( $fields[$name] ) || !in_array( $fields[$name]['type'], $textTypes, true ) )
                    continue;
                $other = 0;
                $texts = 0;
                foreach ( $names as $o )
                {
                    if ( !isset( $fields[$o] ) )
                        continue;
                    if ( in_array( $fields[$o]['type'], $textTypes, true ) )
                        $texts++;
                    else
                        $other += (int)$this->keyBytes( $fields[$o] );
                }
                $n = intdiv( intdiv( $budget - $other, max( 1, $texts ) ) - 2, 4 );
                $n = min( $n, self::MAX_VARCHAR_CHARACTERS );
                $lengths[$name] = isset( $lengths[$name] ) ? min( $lengths[$name], $n ) : $n;
            }
        }
        foreach ( $lengths as $name => $n )
        {
            $fields[$name]['type'] = 'varchar';
            $fields[$name]['length'] = max( 1, $n );
            $fields[$name]['db2:mark'] = self::TEXT_MARK;
            $this->Notes[] = "$table.$name: text in the key of an index, VARCHAR($n CODEUNITS32)";
        }
        foreach ( $fields as $name => $def )
        {
            if ( isset( $lengths[$name] ) )
                continue;
            if ( in_array( $def['type'], $textTypes, true ) && static::isLargeText( $table, $name ) )
            {
                $fields[$name]['type'] = 'clob';
                $fields[$name]['db2:mark'] = self::CLOB_MARK;
            }
            else if ( $def['type'] === 'char' )
                $fields[$name]['db2:mark'] = self::CHAR_MARK;
            else if ( $def['type'] === 'tinyint' )
                $fields[$name]['db2:mark'] = self::TINYINT_MARK;
        }
        return $fields;
    }

    /**
     * The key columns of an index that fit: a non-unique index keeps the leading columns within the key length;
     * a primary or unique one keeps all (Db2 refuses it when it is too long, and that is reported).
     *
     * @return array( string[] columns, string[] notes )
     */
    function fittingIndexColumns( $table, $index, array $indexDef, array $fields )
    {
        $budget = $this->maxKeyBytes();
        $key = array();
        $total = 0;
        $notes = array();
        foreach ( $indexDef['fields'] as $fieldDef )
        {
            $name = is_array( $fieldDef ) ? $fieldDef['name'] : $fieldDef;
            $size = isset( $fields[$name] ) ? $this->keyBytes( $fields[$name] ) : 8;
            if ( $size === null )
            {
                $notes[] = "$table.$index: $name is a LOB, which cannot be an index key: left out";
                continue;
            }
            if ( $indexDef['type'] === 'non-unique' && $total + $size > $budget )
            {
                $notes[] = "$table.$index: $name would take the key past $budget bytes: left out";
                continue;
            }
            $total += $size;
            $key[] = $name;
        }
        if ( $indexDef['type'] !== 'non-unique' && $total > $budget )
            $notes[] = "$table.$index: the key may take $total bytes, more than the $budget of the page size";
        return array( $key, $notes );
    }

    // ------------------------------------------------------------------ DDL

    function literalFor( array $def, $value )
    {
        $dialect = static::DIALECT;
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
                return is_numeric( $value ) ? $dialect::literal( (float)$value ) : ( $value === '' ? '0' : 'NULL' );
            case 'blob':
            case 'mediumblob':
            case 'longblob':
                return "BLOB(X'" . bin2hex( (string)$value ) . "')";
        }
        return $dialect::stringLiteral( (string)$value );
    }

    function generateDefaultDef( $table, $field, $def )
    {
        if ( $def['type'] === 'auto_increment' || !array_key_exists( 'default', $def ) || $def['default'] === false )
            return '';
        if ( in_array( $def['type'], array( 'clob', 'blob', 'mediumblob', 'longblob' ), true ) )
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
     * The statements of a sequence numbered column: the sequence, its description (table.column) and the trigger.
     *
     * @param string $table
     * @param string $field
     * @return string[]
     */
    function autoIncrementSQLList( $table, $field )
    {
        $dialect = static::DIALECT;
        $sequence = $this->sequenceName( $table );
        $column = 'N.' . $this->quoteSchemaIdentifier( $field );
        return array(
            "CREATE SEQUENCE $sequence AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20",
            "COMMENT ON SEQUENCE $sequence IS " . $dialect::stringLiteral( strtolower( $table . '.' . $field ) ),
            'CREATE TRIGGER ' . $this->triggerName( $table ) . ' NO CASCADE BEFORE INSERT ON ' . $this->quoteSchemaIdentifier( $table )
          . " REFERENCING NEW AS N FOR EACH ROW WHEN ($column IS NULL OR $column = 0) SET $column = NEXT VALUE FOR $sequence",
        );
    }

    function generateAddIndexSql( $table, $index, $def, $params, $withClosure = true, $fields = array() )
    {
        // an upgrade adds an index to a table that exists: its column types come from the database
        if ( !$fields && $this->DBInstance instanceof expDb2DB && $this->DBInstance->isConnected() )
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
     * An index name of the .dba as it may stand here (index names are unique in the whole schema).
     *
     * @param string $table
     * @param string $index
     * @return string
     */
    function indexName( $table, $index )
    {
        $dialect = static::DIALECT;
        return $dialect::quoteIdentifierIfNeeded( $this->storedIndexName( $table, $index ) );
    }

    /**
     * The name an index of the .dba gets, where index names are unique in the whole schema while the .dba (as MySQL)
     * names them per table: a name that does not start with its table's name gets the table's name in front, a long
     * one is cut with a hash. The .dba's name is kept as the index's description (indexComment()).
     *
     * @param string $table
     * @param string $index
     * @return string
     */
    function storedIndexName( $table, $index )
    {
        $dialect = static::DIALECT;
        $name = (string)$index;
        if ( stripos( $name, $table . '_' ) !== 0 && strcasecmp( $name, $table ) !== 0 )
            $name = $table . '_' . $name;
        if ( strlen( $name ) > $dialect::MAX_IDENTIFIER_LENGTH || $dialect::isReservedWord( $name ) )
            $name = static::objectName( 'I_', $table, '_' . substr( md5( $index ), 0, 6 ) );
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
        $dialect = static::DIALECT;
        $stored = $this->storedIndexName( $table, $index );
        if ( strcasecmp( $stored, (string)$index ) === 0 )
            return null;
        return 'COMMENT ON INDEX ' . $dialect::quoteIdentifierIfNeeded( $stored ) . ' IS ' . $dialect::stringLiteral( 'exp:index:' . $index );
    }

    function generateDropIndexSql( $table, $index, $def, $withClosure = true )
    {
        if ( $def['type'] === 'primary' )
            $sql = 'ALTER TABLE ' . $this->quoteSchemaIdentifier( $table ) . ' DROP CONSTRAINT ' . $this->primaryKeyName( $table );
        else
            $sql = 'DROP INDEX ' . $this->indexName( $table, $index );
        return $sql . ( $withClosure ? ";\n" : '' );
    }

    /**
     * The statement that makes a table usable again after an ALTER that left it in reorg pending state.
     *
     * @param string $table
     * @return string
     */
    function reorgSql( $table )
    {
        $dialect = static::DIALECT;
        return "CALL SYSPROC.ADMIN_CMD(" . $dialect::stringLiteral( 'REORG TABLE ' . $dialect::fold( $table ) ) . ')';
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
        return 'ALTER TABLE ' . $this->quoteSchemaIdentifier( $table ) . ' DROP COLUMN ' . $this->quoteSchemaIdentifier( $field ) . ";\n"
             . $this->reorgSql( $table ) . ";\n";
    }

    function generateAlterFieldSql( $table, $field, $def, $params )
    {
        $t = $this->quoteSchemaIdentifier( $table );
        $f = $this->quoteSchemaIdentifier( $field );
        // a changed type can leave the table in reorg pending state, where even an UPDATE is refused (SQL0668N)
        $sql = "ALTER TABLE $t ALTER COLUMN $f SET DATA TYPE " . $this->convertFromStandardType( $def['type'], isset( $def['length'] ) ? $def['length'] : null ) . ";\n"
             . $this->reorgSql( $table ) . ";\n";
        if ( $def['type'] !== 'auto_increment' )
        {
            $default = $this->generateDefaultDef( $table, $field, $def );
            $sql .= "ALTER TABLE $t ALTER COLUMN $f " . ( $default !== '' ? 'SET ' . $default : 'DROP DEFAULT' ) . ";\n";
            if ( !empty( $def['not_null'] ) && $default !== '' )
                $sql .= "UPDATE $t SET $f = " . substr( $default, 8 ) . " WHERE $f IS NULL;\n";
            $sql .= "ALTER TABLE $t ALTER COLUMN $f " . ( !empty( $def['not_null'] ) ? 'SET' : 'DROP' ) . " NOT NULL;\n";
        }
        return $sql . $this->reorgSql( $table ) . ";\n";
    }

    /**
     * The statements of one table: array( 'tables' => ..., 'constraints' => ..., 'indexes' => ..., 'sequences' =>
     * ..., 'comments' => ... ).
     */
    function generateTableArrays( $table, $tableDef, $params, $withClosure = true )
    {
        $dialect = static::DIALECT;
        $tableDef['fields'] = $this->indexedTextFields( $table, $tableDef );
        $end = $withClosure ? ';' : '';
        $arrays = array( 'tables' => array(), 'constraints' => array(), 'indexes' => array(), 'sequences' => array(), 'comments' => array() );
        $lines = array();
        foreach ( $tableDef['fields'] as $field => $def )
        {
            $lines[] = '  ' . $this->generateFieldDef( $table, $field, $def );
            if ( !empty( $def['db2:mark'] ) )
                $arrays['comments'][] = 'COMMENT ON COLUMN ' . $this->quoteSchemaIdentifier( $table ) . '.' . $this->quoteSchemaIdentifier( $field )
                                      . ' IS ' . $dialect::stringLiteral( $def['db2:mark'] ) . $end;
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
     * The DDL of one table as a db2 CLP script (db2 -tvf), one statement per line ending in ";".
     */
    function generateTableSchema( $table, $tableDef, $params )
    {
        $arrays = $this->generateTableArrays( $table, $tableDef, $params, false );
        $out = array();
        foreach ( array_merge( $arrays['tables'], $arrays['comments'], $arrays['constraints'], $arrays['indexes'], $arrays['sequences'] ) as $sql )
            $out[] = $sql . ';';
        return implode( "\n", $out ) . "\n";
    }

    /**
     * The first lines of a schema file.
     *
     * @return string
     */
    function schemaFileHeader()
    {
        return "-- Written by expDb2Schema for Db2 11.5 and 12.1 (db2 -tvf <file>), for a database of PAGESIZE 32 K and CODESET UTF-8\n\n";
    }

    function generateSchemaFile( $schema, $params = array() )
    {
        $this->Notes = array();
        $parts = array( rtrim( $this->schemaFileHeader() ) . "\n" );
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
        $dialect = static::DIALECT;
        return $dialect::escapeString( $value );
    }

    // ------------------------------------------------------------------ read back

    /**
     * The schema of the connected database, in the database-independent form.
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
     * The column numbered by a sequence of the schema handler, or null.
     *
     * @param string $table
     * @return string|null
     */
    function autoIncrementColumn( $table )
    {
        $dialect = static::DIALECT;
        $rows = $this->DBInstance->arrayQuery( 'SELECT CAST(REMARKS AS VARCHAR(254)) AS description FROM SYSCAT.SEQUENCES WHERE SEQSCHEMA = CURRENT SCHEMA AND SEQNAME = '
                                             . $dialect::stringLiteral( $this->sequenceName( $table ) ) );
        if ( !is_array( $rows ) || !isset( $rows[0]['description'] ) || $rows[0]['description'] === null )
            return null;
        $parts = explode( '.', strtolower( trim( $rows[0]['description'] ) ) );
        return end( $parts );
    }

    /**
     * The fields of a table in schema form.
     */
    function fetchTableFields( $table, $params )
    {
        $fields = array();
        $auto = $this->autoIncrementColumn( $table );
        foreach ( (array)$this->DBInstance->tableColumns( $table ) as $row )
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
                $type = $this->convertToStandardType( $row['type'], $row['length'], $row['scale'], $row['units_length'], $row['units'], $row['remarks'], $length );
                $field['type'] = $type;
                if ( $length !== null )
                    $field['length'] = $length;
                $field['not_null'] = (string)$row['nullable'] === 'N' ? '1' : '0';
                $field['default'] = $this->parseDefault( $row['default_value'] );
            }
            $fields[$name] = $field;
        }
        if ( !empty( $params['sort_columns'] ) )
            ksort( $fields );
        return $fields;
    }

    /**
     * The value of a column's default of the catalogue ("0", "''", "NULL"); false without one.
     *
     * @param string|null $definition
     * @return mixed
     */
    function parseDefault( $definition )
    {
        if ( $definition === null )
            return false;
        $definition = trim( (string)$definition );
        if ( $definition === '' )
            return false;
        if ( strcasecmp( $definition, 'NULL' ) === 0 )
            return null;
        if ( preg_match( "/^'(.*)'$/s", $definition, $m ) )
            return str_replace( "''", "'", $m[1] );
        if ( is_numeric( $definition ) )
            return strpos( $definition, '.' ) === false && stripos( $definition, 'e' ) === false ? (int)$definition : (float)$definition;
        return $definition;
    }

    /**
     * The indexes of a table in schema form: PRIMARY for the primary key.
     */
    function fetchTableIndexes( $table, $params )
    {
        $dialect = static::DIALECT;
        $rows = $this->DBInstance->arrayQuery(
            'SELECT LOWER(INDNAME) AS index_name, UNIQUERULE AS rule, COLNAMES AS columns, CAST(REMARKS AS VARCHAR(254)) AS description'
          . ' FROM SYSCAT.INDEXES WHERE TABSCHEMA = CURRENT SCHEMA AND TABNAME = ' . $dialect::stringLiteral( $dialect::fold( $table ) ) . ' ORDER BY 1' );
        $indexes = array();
        foreach ( (array)$rows as $row )
        {
            $primary = $row['rule'] === 'P';
            $name = $primary ? 'PRIMARY' : $row['index_name'];
            if ( !$primary && $row['description'] !== null && strpos( (string)$row['description'], 'exp:index:' ) === 0 )
                $name = substr( (string)$row['description'], 10 );
            $columns = array();
            foreach ( preg_split( '/(?=[+-])/', (string)$row['columns'], -1, PREG_SPLIT_NO_EMPTY ) as $column )
                $columns[] = strtolower( substr( $column, 1 ) );
            $indexes[$name] = array( 'type' => $primary ? 'primary' : ( $row['rule'] === 'U' ? 'unique' : 'non-unique' ), 'fields' => $columns );
        }
        if ( !empty( $params['sort_indexes'] ) )
            ksort( $indexes );
        return $indexes;
    }
}
