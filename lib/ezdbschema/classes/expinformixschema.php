<?php
/**
 * File containing the expInformixSchema class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The schema handler of Informix (expInformixDB): the database-independent schema (.dba) as Informix DDL, and an
 * Informix database read back into that schema.
 *
 * Types: int INTEGER, bigint BIGINT, tinyint/smallint SMALLINT, auto_increment SERIAL, varchar(n) and char(n)
 * VARCHAR(4n) up to 255 bytes and LVARCHAR(4n) above (Informix counts bytes, UTF-8 takes up to four per
 * character), text and longtext LVARCHAR of the bytes the row leaves (a row is at most 32,767 bytes), the documents of
 * any length (expDb2Schema::LARGE_TEXT_COLUMNS) CLOB in the server's default sbspace (SBSPACENAME), float SMALLFLOAT,
 * double FLOAT, decimal DECIMAL, timestamp/datetime DATETIME YEAR TO SECOND, date DATE, time DATETIME HOUR TO SECOND,
 * blob BLOB. A CLOB compares with = (it needs the default sbspace for that) but not with LIKE.
 *
 * Names: unquoted, in lower case; a primary key is the constraint pk_<table>. Informix has no descriptions
 * (COMMENT ON), so a column read back is what Informix has: LVARCHAR(32739) as longtext, VARCHAR and LVARCHAR as
 * varchar of a quarter of their bytes.
 *
 * Index keys: Informix keys at most about a fifth of the page of the dbspace the table is in: an Exponential
 * database needs a dbspace of 16 KB pages (3,200 bytes per key) for the kernel's indexes of text. A text column in
 * an index is LVARCHAR with the length that fits every index it is in.
 *
 * @since 6.0.15
 * @package lib
 */
class expInformixSchema extends expDb2Schema
{
    /** The page size assumed without a connection (a dbspace of 16 KB pages) */
    const DEFAULT_PAGE_SIZE = 16384;

    /** The length in bytes of a text column (longtext) */
    const TEXT_LENGTH = 32739;

    /** The longest VARCHAR, in bytes; LVARCHAR above */
    const MAX_VARCHAR_BYTES = 255;

    const DIALECT = 'expInformixDialect';

    /** The longest row Informix stores, less room for its overhead */
    const MAX_ROW_BYTES = 32000;

    /** The fewest bytes a text column gets when a row's text columns share what is left */
    const TEXT_SHARE_MIN = 4000;

    function schemaType()
    {
        return 'informix';
    }

    function schemaName()
    {
        return 'Informix';
    }

    function primaryKeyName( $table )
    {
        return strtolower( static::objectName( 'PK_', $table ) );
    }

    /**
     * @param int $characters
     * @return string VARCHAR(n) or LVARCHAR(n) of four bytes per character
     */
    static function textType( $characters )
    {
        $bytes = min( 4 * max( 1, (int)$characters ), self::TEXT_LENGTH );
        return $bytes <= self::MAX_VARCHAR_BYTES ? "VARCHAR($bytes)" : "LVARCHAR($bytes)";
    }

    function convertFromStandardType( $type, $length = null )
    {
        switch ( $type )
        {
            case 'auto_increment':
                return 'SERIAL';
            case 'varchar':
            case 'char':
                return static::textType( (int)$length > 0 ? (int)$length : ( $type === 'char' ? 1 : 255 ) );
            case 'tinytext':
            case 'text':
            case 'mediumtext':
            case 'longtext':
                return 'LVARCHAR(' . self::TEXT_LENGTH . ')';
            case 'clob':
                return 'CLOB';
            case 'float':
                return 'SMALLFLOAT';
            case 'double':
                return 'FLOAT';
            case 'timestamp':
            case 'datetime':
                return 'DATETIME YEAR TO SECOND';
            case 'time':
                return 'DATETIME HOUR TO SECOND';
            case 'blob':
            case 'mediumblob':
            case 'longblob':
                return 'BLOB';
        }
        return parent::convertFromStandardType( $type, $length );
    }

    /**
     * The .dba type of an Informix column (syscolumns.coltype without the NOT NULL bit, collength, extended_id).
     *
     * @return string
     */
    function informixStandardType( $typeCode, $collength, $extendedId, &$length )
    {
        $length = null;
        switch ( (int)$typeCode & 255 )
        {
            case 1:
                $length = 6;
                return 'int';
            case 2:
                $length = 11;
                return 'int';
            case 6:
                return 'auto_increment';
            case 17:
            case 52:
                $length = 20;
                return 'bigint';
            case 18:
            case 53:
                return 'auto_increment';
            case 4:
                return 'float';
            case 3:
                return 'double';
            case 5:
                $length = ( (int)$collength >> 8 ) . ',' . ( (int)$collength & 255 );
                return 'decimal';
            case 0:
            case 13:
            case 15:
            case 16:
            case 40:
            case 43:
                $bytes = (int)$collength;
                if ( ( (int)$typeCode & 255 ) === 13 || ( (int)$typeCode & 255 ) === 16 )
                    $bytes = $bytes & 255;
                if ( $bytes >= self::TEXT_LENGTH )
                    return 'longtext';
                $length = max( 1, intdiv( $bytes, 4 ) );
                return 'varchar';
            case 7:
                return 'date';
            case 10:
                return (int)$collength === 1642 ? 'datetime' : ( (int)$collength < 1000 ? 'time' : 'timestamp' );
            case 41:
                // the extended types of the catalogue: 10 CLOB, 11 BLOB
                return (int)$extendedId === 10 ? 'longtext' : 'longblob';
        }
        throw new RuntimeException( "Unhandled Informix column type: $typeCode" );
    }

    function pageSize()
    {
        if ( $this->PageSize === null )
        {
            $this->PageSize = self::DEFAULT_PAGE_SIZE;
            if ( $this->DBInstance instanceof expInformixDB && $this->DBInstance->isConnected() )
            {
                $result = $this->DBInstance->Transport->run( "SELECT d.pagesize AS page_size FROM sysmaster:sysdatabases b, sysmaster:sysdbspaces d"
                                                           . " WHERE b.name = DBINFO('dbname') AND d.dbsnum = PARTDBSNUM(b.partnum)" );
                if ( $result !== false && isset( $result['rows'][0]['page_size'] ) && (int)$result['rows'][0]['page_size'] > 0 )
                    $this->PageSize = (int)$result['rows'][0]['page_size'];
            }
        }
        return $this->PageSize;
    }

    /**
     * About a fifth of the page per key.
     *
     * @return int
     */
    function maxKeyBytes()
    {
        return (int)( $this->pageSize() / 5 ) - 50;
    }

    function keyBytes( array $def )
    {
        if ( isset( $def['informix:bytes'] ) )
            return (int)$def['informix:bytes'] + 2;
        $sqlType = $this->convertFromStandardType( $def['type'], isset( $def['length'] ) ? $def['length'] : null );
        if ( $sqlType === 'BLOB' || $sqlType === 'CLOB' )
            return null;
        if ( preg_match( '/^L?VARCHAR\((\d+)\)$/', $sqlType, $m ) )
            return (int)$m[1] + 2;
        $sizes = array( 'INTEGER' => 4, 'SERIAL' => 4, 'BIGINT' => 8, 'SMALLINT' => 2, 'SMALLFLOAT' => 4, 'FLOAT' => 8, 'DATE' => 4 );
        return isset( $sizes[$sqlType] ) ? $sizes[$sqlType] : 11;
    }

    /**
     * The fields with every text column in a key as varchar of the characters that fit (Informix: LVARCHAR).
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
            foreach ( $names as $name )
            {
                if ( !isset( $fields[$name] ) || !in_array( $fields[$name]['type'], $textTypes, true ) )
                    continue;
                $n = intdiv( intdiv( $budget - $other, max( 1, $texts ) ) - 2, 4 );
                $lengths[$name] = isset( $lengths[$name] ) ? min( $lengths[$name], $n ) : $n;
            }
        }
        foreach ( $lengths as $name => $n )
        {
            $fields[$name]['type'] = 'varchar';
            $fields[$name]['length'] = max( 1, $n );
            $this->Notes[] = "$table.$name: text in the key of an index, " . static::textType( $n );
        }
        // the documents of any length: CLOB, in the server's default sbspace
        foreach ( $fields as $name => $def )
            if ( !isset( $lengths[$name] ) && in_array( $def['type'], $textTypes, true ) && static::isLargeText( $table, $name ) )
                $fields[$name]['type'] = 'clob';
        return $this->fitRow( $table, $fields );
    }

    /**
     * The fields with byte lengths that keep a row within Informix's 32,767 bytes: four bytes per character while
     * they fit; else the text columns share what the other columns leave (at least TEXT_SHARE_MIN bytes each), and
     * if that is still too much the character columns get two bytes per character.
     *
     * @param string $table
     * @param array $fields
     * @return array the fields, each text or character column with 'informix:bytes'
     */
    function fitRow( $table, array $fields )
    {
        $textTypes = array( 'tinytext', 'text', 'mediumtext', 'longtext' );
        $budget = self::MAX_ROW_BYTES;
        foreach ( array( 4, 2 ) as $perCharacter )
        {
            $fixed = 0;
            $texts = array();
            foreach ( $fields as $name => $def )
            {
                if ( in_array( $def['type'], $textTypes, true ) )
                    $texts[] = $name;
                else if ( in_array( $def['type'], array( 'varchar', 'char' ), true ) )
                    $fixed += min( $perCharacter * max( 1, (int)( isset( $def['length'] ) ? $def['length'] : 255 ) ), self::TEXT_LENGTH ) + 3;
                else
                    $fixed += $def['type'] === 'clob' ? 80 : 12;
            }
            $share = $texts ? intdiv( $budget - $fixed, count( $texts ) ) - 3 : 0;
            if ( !$texts || $share >= self::TEXT_SHARE_MIN || $perCharacter === 2 )
                break;
        }
        $share = min( max( $share, 256 ), self::TEXT_LENGTH );
        foreach ( $fields as $name => $def )
        {
            if ( in_array( $def['type'], $textTypes, true ) )
            {
                $fields[$name]['informix:bytes'] = $share;
                if ( $share < self::TEXT_LENGTH )
                    $this->Notes[] = "$table.$name: text of at most $share bytes (the row's limit)";
            }
            else if ( in_array( $def['type'], array( 'varchar', 'char' ), true ) )
                $fields[$name]['informix:bytes'] = min( $perCharacter * max( 1, (int)( isset( $def['length'] ) ? $def['length'] : 255 ) ), self::TEXT_LENGTH );
        }
        return $fields;
    }

    function generateFieldDef( $table, $field, $def )
    {
        $type = isset( $def['informix:bytes'] ) ? ( $def['informix:bytes'] <= self::MAX_VARCHAR_BYTES ? 'VARCHAR(' . $def['informix:bytes'] . ')' : 'LVARCHAR(' . $def['informix:bytes'] . ')' )
                                                : $this->convertFromStandardType( $def['type'], isset( $def['length'] ) ? $def['length'] : null );
        $sql = $this->quoteSchemaIdentifier( $field ) . ' ' . $type;
        if ( $def['type'] === 'auto_increment' )
            return $sql . ' NOT NULL';
        $default = $this->generateDefaultDef( $table, $field, $def );
        if ( $default !== '' )
            $sql .= ' ' . $default;
        return $sql . ( !empty( $def['not_null'] ) ? ' NOT NULL' : '' );
    }

    function literalFor( array $def, $value )
    {
        if ( in_array( $def['type'], array( 'blob', 'mediumblob', 'longblob' ), true ) && $value !== null && $value !== false )
            return 'NULL';
        return parent::literalFor( $def, $value );
    }

    /** SERIAL numbers its rows: no sequence, no trigger */
    function autoIncrementSQLList( $table, $field )
    {
        return array();
    }

    function generateAddIndexSql( $table, $index, $def, $params, $withClosure = true, $fields = array() )
    {
        if ( !$fields && $this->DBInstance instanceof expInformixDB && $this->DBInstance->isConnected() )
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
            $sql = 'ALTER TABLE ' . $this->quoteSchemaIdentifier( $table ) . " ADD CONSTRAINT PRIMARY KEY ($columns) CONSTRAINT " . $this->primaryKeyName( $table );
        else
            $sql = 'CREATE ' . ( $def['type'] === 'unique' ? 'UNIQUE ' : '' ) . 'INDEX ' . $this->indexName( $table, $index )
                 . ' ON ' . $this->quoteSchemaIdentifier( $table ) . " ($columns)";
        return $sql . ( $withClosure ? ";\n" : '' );
    }

    /** Informix has no descriptions: the index keeps its stored name */
    function indexComment( $table, $index )
    {
        return null;
    }

    function generateDropFieldSql( $table, $field, $params )
    {
        return 'ALTER TABLE ' . $this->quoteSchemaIdentifier( $table ) . ' DROP ' . $this->quoteSchemaIdentifier( $field ) . ";\n";
    }

    function generateAlterFieldSql( $table, $field, $def, $params )
    {
        $t = $this->quoteSchemaIdentifier( $table );
        $f = $this->quoteSchemaIdentifier( $field );
        $sql = '';
        $default = $this->generateDefaultDef( $table, $field, $def );
        if ( !empty( $def['not_null'] ) && $default !== '' )
            $sql .= "UPDATE $t SET $f = " . substr( $default, 8 ) . " WHERE $f IS NULL;\n";
        return $sql . "ALTER TABLE $t MODIFY (" . $this->generateFieldDef( $table, $field, $def ) . ");\n";
    }

    function generateTableArrays( $table, $tableDef, $params, $withClosure = true )
    {
        $arrays = parent::generateTableArrays( $table, $tableDef, $params, $withClosure );
        // no COMMENT ON in Informix
        $arrays['comments'] = array();
        return $arrays;
    }

    function schemaFileHeader()
    {
        return "-- Written by expInformixSchema for Informix 14.10 and 15 (dbaccess <database> <file>), for a database in a dbspace of 16 KB pages\n\n";
    }

    // ------------------------------------------------------------------ read back

    function autoIncrementColumn( $table )
    {
        return null;
    }

    function fetchTableFields( $table, $params )
    {
        $fields = array();
        foreach ( (array)$this->DBInstance->tableColumns( $table ) as $row )
        {
            $type = $this->informixStandardType( $row['type_code'], $row['length'], $row['extended_id'], $length );
            $field = array( 'type' => $type );
            if ( $type === 'auto_increment' )
                $field['default'] = false;
            else
            {
                if ( $length !== null )
                    $field['length'] = $length;
                $field['not_null'] = ( (int)$row['type_code'] & 256 ) ? '1' : '0';
                $field['default'] = $this->informixDefault( $row['default_type'], $row['default_value'], $type );
            }
            $fields[strtolower( $row['name'] )] = $field;
        }
        if ( !empty( $params['sort_columns'] ) )
            ksort( $fields );
        return $fields;
    }

    /**
     * The default of sysdefaults: type L (a literal, its text in default), N (NULL), none: false.
     *
     * @return mixed
     */
    function informixDefault( $type, $value, $columnType )
    {
        if ( $type === null || $type === '' )
            return false;
        if ( $type === 'N' )
            return null;
        if ( $type !== 'L' )
            return false;
        $value = rtrim( (string)$value );
        if ( in_array( $columnType, array( 'int', 'bigint', 'decimal', 'float', 'double' ), true ) )
        {
            // numbers are stored as their text after a binary prefix of some versions
            if ( preg_match( '/(-?\d+(?:\.\d+)?)\s*$/', $value, $m ) )
                return strpos( $m[1], '.' ) === false ? (int)$m[1] : (float)$m[1];
        }
        return $value;
    }

    function fetchTableIndexes( $table, $params )
    {
        $rows = $this->DBInstance->arrayQuery(
            'SELECT TRIM(i.idxname) AS index_name, i.idxtype AS idx_type, c.constrtype AS constraint_type,'
          . ' i.part1, i.part2, i.part3, i.part4, i.part5, i.part6, i.part7, i.part8'
          . ' FROM sysindexes i JOIN systables t ON t.tabid = i.tabid LEFT JOIN sysconstraints c ON c.idxname = i.idxname AND c.tabid = i.tabid'
          . ' WHERE t.tabname = ' . expInformixDialect::stringLiteral( expInformixDialect::fold( $table ) ) . ' ORDER BY 1' );
        $columns = array();
        foreach ( (array)$this->DBInstance->arrayQuery( 'SELECT c.colno, TRIM(c.colname) AS name FROM syscolumns c JOIN systables t ON t.tabid = c.tabid WHERE t.tabname = '
                                                        . expInformixDialect::stringLiteral( expInformixDialect::fold( $table ) ) ) as $c )
            $columns[(int)$c['colno']] = strtolower( $c['name'] );
        $indexes = array();
        foreach ( (array)$rows as $row )
        {
            $primary = $row['constraint_type'] === 'P';
            $name = $primary ? 'PRIMARY' : strtolower( $row['index_name'] );
            $parts = array();
            for ( $n = 1; $n <= 8; $n++ )
                if ( (int)$row['part' . $n] !== 0 && isset( $columns[abs( (int)$row['part' . $n] )] ) )
                    $parts[] = $columns[abs( (int)$row['part' . $n] )];
            $indexes[$name] = array( 'type' => $primary ? 'primary' : ( $row['idx_type'] === 'U' ? 'unique' : 'non-unique' ), 'fields' => $parts );
        }
        if ( !empty( $params['sort_indexes'] ) )
            ksort( $indexes );
        return $indexes;
    }
}
