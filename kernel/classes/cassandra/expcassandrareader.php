<?php
/**
 * File containing the expCassandraReader class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Reads the tables of an Apache Cassandra or ScyllaDB keyspace for an import (expCassandraImport): the tables of the
 * keyspace from system_schema, their rows page by page. It describes a table the way the readers of the Access import
 * do (expAccessReader, whose import engine expCassandraImport uses), so the CQL types are given in that vocabulary:
 *
 *   int, smallint, tinyint -> integer          bigint, counter -> bigint          varint -> decimal (38 digits)
 *   float, double -> double                    decimal -> decimal                 boolean -> boolean
 *   text, varchar, ascii -> text (255 when it is a key column, long text otherwise)
 *   uuid, timeuuid -> guid                     timestamp, date -> datetime ('Y-m-d H:i:s', UTC)
 *   time, duration, inet -> text               blob -> binary (base64)
 *   list, set, map, tuple, user types, vectors -> memo (JSON)
 *
 * The key is the table's partition key and clustering columns; the indexes are its secondary indexes on one column.
 * The driver's own tables (exp_cassandra_sequence, exp_cassandra_schema) are not listed.
 *
 * @since 6.0.15
 * @package kernel
 */
class expCassandraReader extends expAccessReader
{
    /** @var expCassandraDB */
    protected $db;

    /**
     * @param expCassandraDB $db an open connection to the keyspace
     * @throws RuntimeException when it is not open
     */
    public function __construct( $db )
    {
        if ( !$db instanceof expCassandraDB || !$db->isConnected() )
            throw new RuntimeException( 'The Cassandra or ScyllaDB connection to read from is not open' );
        $this->db = $db;
        $this->file = $db->databaseName() . '://' . implode( ',', expCassandraDB::hosts( $db->Server ) ) . '/' . $db->keyspace();
    }

    public function name()
    {
        return $this->db->databaseName();
    }

    /** @return string the product and version of the server */
    public function format()
    {
        $version = $this->db->databaseServerVersion();
        return is_array( $version ) ? $version['name'] . ' ' . $version['string'] : '';
    }

    public function tables()
    {
        $tables = $this->db->relationList( eZDBInterface::RELATION_TABLE );
        return is_array( $tables ) ? $tables : array();
    }

    public function table( $table )
    {
        $description = $this->db->tableDescription( $table );
        if ( $description === null )
            throw new RuntimeException( "The keyspace has no table '$table'" );
        $keys = array_merge( $description['partition'], array_keys( $description['clustering'] ) );
        $fields = array();
        foreach ( $description['columns'] as $name => $type )
            $fields[$name] = self::field( $type, in_array( $name, $keys, true ) );
        $indexes = array();
        foreach ( $description['indexes'] as $column => $index )
            $indexes[$index] = array( 'unique' => false, 'fields' => array( $column ) );
        return array( 'fields' => $fields, 'primary' => $keys, 'indexes' => $indexes );
    }

    /**
     * The import's field of a CQL type (pure).
     *
     * @param string $cqlType
     * @param bool $key
     * @return array type, length, scale, not_null, auto
     */
    public static function field( $cqlType, $key = false )
    {
        $type = strtolower( trim( (string)$cqlType ) );
        $field = array( 'type' => 'memo', 'length' => 0, 'scale' => 0, 'not_null' => $key, 'auto' => false );
        switch ( $type )
        {
            case 'int':
            case 'smallint':
            case 'tinyint':
                $field['type'] = 'integer';
                break;
            case 'bigint':
            case 'counter':
                $field['type'] = 'bigint';
                break;
            case 'varint':
                $field['type'] = 'decimal';
                $field['length'] = 38;
                break;
            case 'float':
            case 'double':
                $field['type'] = 'double';
                break;
            case 'decimal':
                $field['type'] = 'decimal';
                $field['length'] = 38;
                $field['scale'] = 10;
                break;
            case 'boolean':
                $field['type'] = 'boolean';
                break;
            case 'text':
            case 'varchar':
            case 'ascii':
                $field['type'] = 'text';
                $field['length'] = $key ? 255 : 0;
                break;
            case 'uuid':
            case 'timeuuid':
                $field['type'] = 'guid';
                break;
            case 'timestamp':
            case 'date':
                $field['type'] = 'datetime';
                break;
            case 'time':
            case 'duration':
            case 'inet':
                $field['type'] = 'text';
                $field['length'] = 64;
                break;
            case 'blob':
                $field['type'] = 'binary';
                break;
        }
        return $field;
    }

    public function rows( $table, callable $each )
    {
        $description = $this->db->tableDescription( $table );
        if ( $description === null )
            throw new RuntimeException( "The keyspace has no table '$table'" );
        $types = $description['columns'];
        $count = $this->db->cqlEach( 'SELECT * FROM ' . expCassandraDialect::quoteName( $table ), array(), function ( $row ) use ( $each, $types ) {
            $values = array();
            foreach ( $row as $name => $value )
                $values[$name] = expCassandraReader::value( $value, isset( $types[$name] ) ? $types[$name] : 'text' );
            $each( $values );
        } );
        if ( $count === false )
            throw new RuntimeException( "The rows of $table could not be read: " . $this->db->errorMessage() );
        return $count;
    }

    /**
     * A value of the server as the import writes it (pure).
     *
     * @param mixed $value
     * @param string $cqlType
     * @return mixed
     */
    public static function value( $value, $cqlType )
    {
        if ( $value === null )
            return null;
        $type = strtolower( trim( (string)$cqlType ) );
        if ( $type === 'blob' )
            return new expAccessBinary( (string)$value );
        if ( $type === 'timestamp' || $type === 'date' )
            return expAccessReader::date( is_int( $value ) ? gmdate( 'Y-m-d H:i:s', (int)floor( $value / 1000 ) ) : $value );
        if ( is_bool( $value ) )
            return $value ? 1 : 0;
        if ( is_array( $value ) || is_object( $value ) )
            return json_encode( $value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
        return $value;
    }
}
?>
