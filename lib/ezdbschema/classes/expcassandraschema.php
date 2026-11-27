<?php
/**
 * File containing the expCassandraSchema class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The schema handler of Apache Cassandra and ScyllaDB (expCassandraDB, expScyllaDB): the database-independent schema
 * (.dba) as CQL, and a keyspace read back into that schema.
 *
 * A table of the schema becomes:
 *
 * - CREATE TABLE IF NOT EXISTS with the CQL type of each field (expCassandraDialect::cqlType(): int, bigint,
 *   double, decimal, text, blob) and the primary key: the first field of the schema's key is the partition key, the
 *   others clustering columns, so a lookup by the first field (or by all of them) is a key lookup. A table without a
 *   primary key gets one of its own: exp_row_id uuid, filled by the driver.
 * - CREATE INDEX IF NOT EXISTS (a secondary index) on the first field of every other index that is no key column;
 *   CQL's indexes have one column. Unique indexes are not enforced: Cassandra has no constraint but the key.
 * - The .dba definition itself, as JSON in exp_cassandra_schema: CQL keeps no NOT NULL, no default, no length and no
 *   auto_increment, so the driver reads them there (defaults on insert, the counted-up column) and schema() gives the
 *   definition back as it was written.
 *
 * Tables Exponential did not make (an import source) are read from system_schema: their types mapped by
 * expCassandraDialect::dbaField(), their key, their single-column indexes.
 *
 * @since 6.0.15
 * @package lib
 */
class expCassandraSchema extends eZDBSchemaInterface
{
    /** The column a table without a primary key is given */
    const ROW_ID = 'exp_row_id';

    function schemaType()
    {
        return is_object( $this->DBInstance ) && in_array( $this->DBInstance->databaseName(), array( 'cassandra', 'scylladb' ), true )
            ? $this->DBInstance->databaseName() : 'cassandra';
    }

    function schemaName()
    {
        return $this->schemaType() === 'scylladb' ? 'ScyllaDB' : 'Apache Cassandra';
    }

    function isMultiInsertSupported()
    {
        return false;
    }

    function escapeSQLString( $value )
    {
        return str_replace( "'", "''", (string)$value );
    }

    /**
     * The key of a table: partition (the first field of the primary key), clustering (the others), and whether the
     * table gets exp_row_id (pure).
     *
     * @param array $table dba table
     * @return array partition, clustering, row_id
     */
    public static function keyOf( array $table )
    {
        $primary = array();
        foreach ( isset( $table['indexes'] ) ? $table['indexes'] : array() as $index )
            if ( isset( $index['type'] ) && $index['type'] === 'primary' )
                foreach ( $index['fields'] as $field )
                    $primary[] = strtolower( is_array( $field ) ? $field['name'] : $field );
        if ( !$primary )
            return array( 'partition' => array( self::ROW_ID ), 'clustering' => array(), 'row_id' => true );
        return array( 'partition' => array( $primary[0] ), 'clustering' => array_slice( $primary, 1 ), 'row_id' => false );
    }

    /**
     * The name of a secondary index: unique in the keyspace (CQL), so the table's name in front when it is not.
     *
     * @param string $table
     * @param string $index
     * @return string
     */
    public static function indexName( $table, $index )
    {
        $name = strtolower( preg_replace( '/[^A-Za-z0-9_]+/', '_', (string)$index ) );
        if ( strpos( $name, strtolower( $table ) . '_' ) !== 0 )
            $name = strtolower( $table ) . '__' . $name;
        if ( strlen( $name ) > 48 )
            $name = substr( $name, 0, 39 ) . '_' . substr( md5( $name ), 0, 8 );
        return $name;
    }

    /**
     * The CQL of one table: the driver's tables, CREATE TABLE, the indexes, the definition (pure).
     *
     * @param string $tableName
     * @param array $table dba table
     * @param array $params
     * @param bool $separateTypes unused
     * @return string[]
     */
    function generateTableSQLList( $tableName, $table, $params, $separateTypes )
    {
        $q = array( 'expCassandraDialect', 'quoteName' );
        $tableName = strtolower( $tableName );
        $key = self::keyOf( $table );
        $columns = array();
        $types = array();
        if ( $key['row_id'] )
            $columns[] = '  ' . call_user_func( $q, self::ROW_ID ) . ' uuid';
        foreach ( $table['fields'] as $name => $field )
        {
            $name = strtolower( $name );
            $types[$name] = expCassandraDialect::cqlType( $field );
            $columns[] = '  ' . call_user_func( $q, $name ) . ' ' . $types[$name];
        }
        $partition = '(' . implode( ', ', array_map( $q, $key['partition'] ) ) . ')';
        $columns[] = '  PRIMARY KEY (' . $partition . ( $key['clustering'] ? ', ' . implode( ', ', array_map( $q, $key['clustering'] ) ) : '' ) . ')';
        $list = array();
        $list[] = 'CREATE TABLE IF NOT EXISTS ' . call_user_func( $q, expCassandraDB::SCHEMA_TABLE ) . ' (table_name text PRIMARY KEY, definition text, created timestamp)';
        foreach ( $table['fields'] as $field )
            if ( isset( $field['type'] ) && $field['type'] === 'auto_increment' )
            {
                $list[] = 'CREATE TABLE IF NOT EXISTS ' . call_user_func( $q, expCassandraDB::SEQUENCE_TABLE ) . ' (name text PRIMARY KEY, value bigint)';
                break;
            }
        $list[] = 'CREATE TABLE IF NOT EXISTS ' . call_user_func( $q, $tableName ) . " (\n" . implode( ",\n", $columns ) . "\n)";
        $keys = array_merge( $key['partition'], $key['clustering'] );
        $indexed = array();
        foreach ( isset( $table['indexes'] ) ? $table['indexes'] : array() as $indexName => $index )
        {
            if ( !isset( $index['fields'] ) || ( isset( $index['type'] ) && $index['type'] === 'primary' ) )
                continue;
            $first = reset( $index['fields'] );
            $first = strtolower( is_array( $first ) ? $first['name'] : $first );
            // the partition key needs no index; a column of a one-column partition key cannot have one
            if ( $first === $key['partition'][0] || isset( $indexed[$first] ) || !isset( $types[$first] ) )
                continue;
            $indexed[$first] = true;
            $list[] = 'CREATE INDEX IF NOT EXISTS ' . call_user_func( $q, self::indexName( $tableName, $indexName ) ) . ' ON ' . call_user_func( $q, $tableName )
                    . ' (' . call_user_func( $q, $first ) . ')';
        }
        $definition = array( 'name' => $tableName, 'fields' => $table['fields'], 'indexes' => isset( $table['indexes'] ) ? $table['indexes'] : array() );
        $list[] = 'REPLACE INTO ' . call_user_func( $q, expCassandraDB::SCHEMA_TABLE ) . ' (table_name, definition) VALUES ('
                . expCassandraDialect::stringLiteral( $tableName ) . ', '
                . expCassandraDialect::stringLiteral( json_encode( $definition, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) ) . ')';
        return $list;
    }

    function generateTableSchema( $tableName, $table, $params )
    {
        return implode( ";\n", $this->generateTableSQLList( $tableName, $table, $params, false ) ) . ";\n";
    }

    function generateSchemaFile( $schema, $params = array() )
    {
        $parts = array();
        foreach ( $schema as $table => $tableDef )
        {
            if ( $table === '_info' )
                continue;
            $parts[] = $this->generateTableSchema( $table, $tableDef, $params );
        }
        return implode( "\n", $parts );
    }

    function generateAddFieldSql( $table_name, $field_name, $def, $params )
    {
        return 'ALTER TABLE ' . expCassandraDialect::quoteName( $table_name ) . ' ADD ' . expCassandraDialect::quoteName( $field_name ) . ' '
             . expCassandraDialect::cqlType( $def ) . ";\n";
    }

    /**
     * CQL changes no column type but to a compatible one: an altered field is written as a note.
     */
    function generateAlterFieldSql( $table_name, $field_name, $def, $params )
    {
        return '-- ' . $table_name . '.' . $field_name . ': Cassandra keeps the type ' . expCassandraDialect::cqlType( $def ) . " (CQL changes no type)\n";
    }

    function generateDropFieldSql( $table_name, $field_name, $params )
    {
        return 'ALTER TABLE ' . expCassandraDialect::quoteName( $table_name ) . ' DROP ' . expCassandraDialect::quoteName( $field_name ) . ";\n";
    }

    function generateAddIndexSql( $table_name, $index_name, $def, $params, $withClosure = true )
    {
        if ( isset( $def['type'] ) && $def['type'] === 'primary' )
            return '-- ' . $table_name . ': the primary key of a Cassandra table is part of CREATE TABLE' . "\n";
        $first = reset( $def['fields'] );
        $first = is_array( $first ) ? $first['name'] : $first;
        return 'CREATE INDEX IF NOT EXISTS ' . expCassandraDialect::quoteName( self::indexName( $table_name, $index_name ) ) . ' ON '
             . expCassandraDialect::quoteName( $table_name ) . ' (' . expCassandraDialect::quoteName( $first ) . ')' . ( $withClosure ? ";\n" : '' );
    }

    function generateDropIndexSql( $table_name, $index_name, $def, $params = array() )
    {
        return 'DROP INDEX IF EXISTS ' . expCassandraDialect::quoteName( self::indexName( $table_name, $index_name ) ) . ";\n";
    }

    function generateDropTable( $table )
    {
        return 'DROP TABLE IF EXISTS ' . expCassandraDialect::quoteName( $table ) . ";\n";
    }

    // ------------------------------------------------------------------ reading a keyspace back

    function schema( $params = array() )
    {
        $params = array_merge( array( 'meta_data' => false, 'format' => 'generic', 'sort_columns' => true, 'sort_indexes' => true ), $params );
        if ( $this->Schema === false )
        {
            $schema = array();
            $db = $this->DBInstance;
            if ( !$db instanceof expCassandraDB || !$db->isConnected() )
                return array();
            foreach ( (array)$db->relationList( eZDBInterface::RELATION_TABLE ) as $table )
            {
                if ( isset( $params['table_include'] ) && is_array( $params['table_include'] ) && !in_array( $table, $params['table_include'] ) )
                    continue;
                $description = $db->tableDescription( $table );
                if ( $description === null )
                    continue;
                $schema[$table] = self::tableFromDescription( $description, $params );
            }
            ksort( $schema );
            $this->Schema = $schema;
        }
        $this->transformSchema( $this->Schema, $params['format'] == 'local' );
        return $this->Schema;
    }

    /**
     * The dba table of a table the driver described (pure): the definition the schema handler wrote, or one made from
     * the CQL types.
     *
     * @param array $description expCassandraDB::tableDescription()
     * @param array $params sort_columns, sort_indexes
     * @return array
     */
    public static function tableFromDescription( array $description, array $params = array() )
    {
        if ( !empty( $description['definition']['fields'] ) )
        {
            $fields = array();
            foreach ( $description['definition']['fields'] as $name => $field )
                $fields[strtolower( $name )] = $field;
            $indexes = isset( $description['definition']['indexes'] ) ? (array)$description['definition']['indexes'] : array();
        }
        else
        {
            $keys = array_merge( $description['partition'], array_keys( $description['clustering'] ) );
            $fields = array();
            foreach ( $description['columns'] as $name => $type )
                $fields[$name] = expCassandraDialect::dbaField( $type, in_array( $name, $keys, true ) );
            $indexes = array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => $keys ) );
            foreach ( $description['indexes'] as $column => $index )
                $indexes[$index] = array( 'type' => 'non-unique', 'fields' => array( $column ) );
        }
        if ( !empty( $params['sort_columns'] ) )
            ksort( $fields );
        if ( !empty( $params['sort_indexes'] ) )
            ksort( $indexes );
        return array( 'name' => $description['name'], 'fields' => $fields, 'indexes' => $indexes );
    }

    /**
     * Creates the tables of the schema that are missing (and the data, when asked) through the driver.
     */
    function insertSchema( $params = array() )
    {
        $db = $this->DBInstance;
        if ( $db instanceof expCassandraDB )
            $db->forgetTables();
        $ok = parent::insertSchema( $params );
        if ( $db instanceof expCassandraDB )
            $db->forgetTables();
        return $ok;
    }
}
?>
