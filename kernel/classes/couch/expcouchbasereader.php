<?php
/**
 * File containing the expCouchbaseReader class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Reads the collections of a Couchbase scope for an import (exp:couchbase:import), in the vocabulary of the Access
 * import's readers (expAccessReader), whose import engine (expAccessImport) makes the tables and copies the rows.
 *
 * A collection the Couchbase driver made (its definition kept in exp_couchbase_schema) is read with that
 * definition: its columns, types and key. Any other collection holds documents of any shape: its columns are the
 * top-level fields of a sample of its documents (--sample), plus doc_id, the document's key, as the primary key.
 * The types are inferred (infer()):
 *
 *   only integers -> integer (bigint beyond 32 bits)    numbers -> double     true/false -> boolean
 *   text -> text (255) when no value is longer, memo otherwise                   objects, arrays -> memo (JSON)
 *
 * A field that a later document has but the sample had not is left out (the import names it in its notes: raise
 * SampleSize). Rows are read in the order of their keys, a page at a time.
 *
 * @since 6.0.15
 * @package kernel
 */
class expCouchbaseReader extends expAccessReader
{
    /** The documents a collection's columns are inferred from */
    const SAMPLE_SIZE = 500;

    /** The column of a document's key (its id): doc_id, not _id, which the import would name id like a field of the documents */
    const KEY_COLUMN = 'doc_id';

    /** Documents a page */
    const PAGE_SIZE = 1000;

    /** @var expCouchbaseDB */
    protected $db;

    /** @var array table => description */
    protected $tables = array();

    /** @var int */
    protected $sampleSize;

    /**
     * @param expCouchbaseDB $db an open connection to the scope
     * @param int $sampleSize
     * @throws RuntimeException when it is not open
     */
    public function __construct( $db, $sampleSize = self::SAMPLE_SIZE )
    {
        if ( !$db instanceof expCouchbaseDB || !$db->isConnected() )
            throw new RuntimeException( 'The Couchbase connection to read from is not open' );
        $this->db = $db;
        $this->sampleSize = max( 1, (int)$sampleSize );
        $this->file = 'couchbase://' . $db->bucket() . '.' . $db->scope();
    }

    public function name()
    {
        return 'couchbase';
    }

    public function format()
    {
        $version = $this->db->databaseServerVersion();
        return $version['name'] . ' ' . $version['string'];
    }

    public function tables()
    {
        $tables = $this->db->relationList( eZDBInterface::RELATION_TABLE );
        return is_array( $tables ) ? $tables : array();
    }

    public function table( $table )
    {
        if ( isset( $this->tables[$table] ) )
            return $this->tables[$table];
        $definition = $this->db->tableDefinition( $table );
        if ( $definition )
            return $this->tables[$table] = self::fromDefinition( $definition );
        $sample = $this->db->send( 'SELECT RAW t FROM ' . expCouchbaseDialect::quoteName( $table ) . ' AS t LIMIT ' . $this->sampleSize );
        if ( $sample === false )
            throw new RuntimeException( "The collection $table cannot be read: " . $this->db->errorMessage() );
        return $this->tables[$table] = self::infer( $sample, self::KEY_COLUMN );
    }

    public function rows( $table, callable $each )
    {
        $description = $this->table( $table );
        $q = expCouchbaseDialect::quoteName( $table );
        $last = null;
        $count = 0;
        $keyed = in_array( self::KEY_COLUMN, $description['primary'], true );
        do
        {
            $where = 'META(t).id IS NOT MISSING' . ( $last !== null ? ' AND META(t).id > ' . expCouchbaseDialect::stringLiteral( $last ) : '' );
            $page = $this->db->send( "SELECT META(t).id AS `_exp_key`, t AS `_exp_doc` FROM $q AS t WHERE $where ORDER BY META(t).id LIMIT " . self::PAGE_SIZE );
            if ( $page === false )
                throw new RuntimeException( "The documents of $table cannot be read: " . $this->db->errorMessage() );
            foreach ( $page as $item )
            {
                $doc = isset( $item['_exp_doc'] ) && is_array( $item['_exp_doc'] ) ? $item['_exp_doc'] : array();
                $row = array();
                foreach ( $description['fields'] as $name => $field )
                {
                    if ( $name === self::KEY_COLUMN && $keyed )
                        $row[$name] = (string)$item['_exp_key'];
                    else
                        $row[$name] = self::value( array_key_exists( $name, $doc ) ? $doc[$name] : null, $field['type'] );
                }
                $each( $row );
                $count++;
                $last = (string)$item['_exp_key'];
            }
        }
        while ( count( $page ) === self::PAGE_SIZE );
        return $count;
    }

    /**
     * The import's description of a table the driver made, from its .dba definition (pure).
     *
     * @param array $definition
     * @return array fields, primary, indexes
     */
    public static function fromDefinition( array $definition )
    {
        $fields = array();
        foreach ( (array)$definition['fields'] as $name => $field )
        {
            $field = (array)$field;
            $type = strtolower( isset( $field['type'] ) ? (string)$field['type'] : 'varchar' );
            $length = isset( $field['length'] ) ? (int)$field['length'] : 0;
            $out = array( 'type' => 'memo', 'length' => 0, 'scale' => 0, 'not_null' => !empty( $field['not_null'] ), 'auto' => $type === 'auto_increment' );
            if ( $type === 'auto_increment' )
                $out['type'] = 'counter';
            elseif ( in_array( $type, array( 'int', 'integer', 'smallint', 'tinyint', 'mediumint', 'bool', 'boolean' ), true ) )
                $out['type'] = 'integer';
            elseif ( $type === 'bigint' )
                $out['type'] = 'bigint';
            elseif ( in_array( $type, array( 'float', 'double', 'real' ), true ) )
                $out['type'] = 'double';
            elseif ( in_array( $type, array( 'decimal', 'numeric' ), true ) )
            {
                $out['type'] = 'decimal';
                $out['length'] = $length ? $length : 18;
                $out['scale'] = isset( $field['scale'] ) ? (int)$field['scale'] : 4;
            }
            elseif ( in_array( $type, array( 'varchar', 'char' ), true ) && $length > 0 && $length <= 255 )
            {
                $out['type'] = 'text';
                $out['length'] = $length;
            }
            $fields[strtolower( $name )] = $out;
        }
        $primary = array();
        $indexes = array();
        foreach ( isset( $definition['indexes'] ) ? (array)$definition['indexes'] : array() as $name => $index )
        {
            $columns = array();
            foreach ( $index['fields'] as $f )
                $columns[] = strtolower( is_array( $f ) ? $f['name'] : $f );
            if ( isset( $index['type'] ) && $index['type'] === 'primary' )
                $primary = $columns;
            else
                $indexes[$name] = array( 'unique' => isset( $index['type'] ) && $index['type'] === 'unique', 'fields' => $columns );
        }
        return array( 'fields' => $fields, 'primary' => $primary, 'indexes' => $indexes );
    }

    /**
     * The import's description of documents of any shape, from a sample (pure): the key field first, then the
     * top-level fields in the order they were met.
     *
     * @param array[] $sample the documents
     * @param string|null $keyField the column of the document's key (_id), or null
     * @param string[] $skip fields left out
     * @return array fields, primary, indexes
     */
    public static function infer( array $sample, $keyField = self::KEY_COLUMN, array $skip = array() )
    {
        $seen = array();
        foreach ( $sample as $doc )
        {
            if ( !is_array( $doc ) )
                continue;
            foreach ( $doc as $name => $value )
            {
                if ( in_array( $name, $skip, true ) || $name === $keyField )
                    continue;
                if ( !isset( $seen[$name] ) )
                    $seen[$name] = array( 'int' => 0, 'big' => 0, 'num' => 0, 'bool' => 0, 'text' => 0, 'long' => 0, 'json' => 0, 'null' => 0 );
                $s =& $seen[$name];
                if ( $value === null )
                    $s['null']++;
                elseif ( is_bool( $value ) )
                    $s['bool']++;
                elseif ( is_int( $value ) )
                {
                    $s['int']++;
                    if ( $value > 2147483647 || $value < -2147483648 )
                        $s['big']++;
                }
                elseif ( is_float( $value ) )
                    $s['num']++;
                elseif ( is_string( $value ) )
                {
                    $s['text']++;
                    if ( mb_strlen( $value ) > 255 )
                        $s['long']++;
                }
                else
                    $s['json']++;
                unset( $s );
            }
        }
        $fields = array();
        if ( $keyField !== null )
            $fields[$keyField] = array( 'type' => 'text', 'length' => 255, 'scale' => 0, 'not_null' => true, 'auto' => false );
        foreach ( $seen as $name => $s )
        {
            $field = array( 'type' => 'memo', 'length' => 0, 'scale' => 0, 'not_null' => false, 'auto' => false );
            if ( $s['json'] )
                $field['type'] = 'memo';
            elseif ( $s['text'] )
            {
                if ( !$s['long'] )
                {
                    $field['type'] = 'text';
                    $field['length'] = 255;
                }
            }
            elseif ( $s['num'] )
                $field['type'] = 'double';
            elseif ( $s['int'] )
                $field['type'] = $s['big'] ? 'bigint' : 'integer';
            elseif ( $s['bool'] )
                $field['type'] = 'boolean';
            $fields[(string)$name] = $field;
        }
        return array( 'fields' => $fields, 'primary' => $keyField !== null ? array( $keyField ) : array(), 'indexes' => array() );
    }

    /**
     * A field's value as the import writes it (pure).
     *
     * @param mixed $value
     * @param string $type the import's type
     * @return mixed
     */
    public static function value( $value, $type )
    {
        if ( $value === null )
            return null;
        if ( is_bool( $value ) )
            return $value ? 1 : 0;
        if ( is_array( $value ) || is_object( $value ) )
            return json_encode( $value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
        if ( in_array( $type, array( 'integer', 'bigint', 'counter', 'boolean' ), true ) && !is_int( $value ) )
            return is_numeric( $value ) ? (int)$value : null;
        if ( $type === 'double' && !is_int( $value ) && !is_float( $value ) )
            return is_numeric( $value ) ? (float)$value : null;
        if ( ( $type === 'text' || $type === 'memo' ) && !is_string( $value ) )
            return (string)$value;
        return $value;
    }
}
?>
