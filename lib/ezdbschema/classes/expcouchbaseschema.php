<?php
/**
 * File containing the expCouchbaseSchema class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The schema handler of Couchbase (dbschema.ini: couchbase). A .dba table becomes a collection of the connection's
 * scope with a primary index and one index per index of the definition (built together, deferred), and its
 * definition is kept in exp_couchbase_schema, where expCouchbaseDB reads the columns' types, defaults and the key.
 *
 * The SQL it writes is a CREATE TABLE in MySQL's form, which expCouchbaseDB answers by making the collection: the
 * statements read the same as those of the other handlers, and a schema file written here can be read by a person.
 * Documents keep any value, so no type is ever changed on the server: ALTER changes the kept definition.
 *
 * @since 6.0.15
 * @package lib
 */
class expCouchbaseSchema extends eZDBSchemaInterface
{
    function schemaType()
    {
        return 'couchbase';
    }

    function schemaName()
    {
        return 'Couchbase';
    }

    function isMultiInsertSupported()
    {
        return true;
    }

    function escapeSQLString( $value )
    {
        return str_replace( array( '\\', "'" ), array( '\\\\', "\\'" ), (string)$value );
    }

    /**
     * The name of an index in its collection (index names are per collection in Couchbase).
     *
     * @param string $table
     * @param string $index
     * @return string
     */
    public static function indexName( $table, $index )
    {
        $name = preg_replace( '/[^A-Za-z0-9_]+/', '_', (string)$index );
        if ( $name === '' || !ctype_alpha( $name[0] ) )
            $name = 'i_' . $name;
        return strlen( $name ) > 200 ? substr( $name, 0, 190 ) . '_' . substr( md5( $name ), 0, 8 ) : $name;
    }

    /**
     * The SQL++ that makes a table's indexes: index name => CREATE INDEX (deferred: BUILD INDEX builds them together).
     *
     * @param string $table
     * @param array $definition .dba table
     * @param bool $defer
     * @param bool $primary with the primary index (#primary)
     * @return array name => statement
     */
    public static function indexStatements( $table, array $definition, $defer = true, $primary = true )
    {
        $q = expCouchbaseDialect::quoteName( $table );
        $with = $defer ? ' WITH {"defer_build": true}' : '';
        $list = array();
        if ( $primary )
            $list['#primary'] = "CREATE PRIMARY INDEX IF NOT EXISTS ON $q$with";
        $seen = array();
        foreach ( isset( $definition['indexes'] ) ? (array)$definition['indexes'] : array() as $name => $index )
        {
            if ( !isset( $index['fields'] ) || !$index['fields'] )
                continue;
            $fields = array();
            foreach ( $index['fields'] as $field )
                $fields[] = expCouchbaseDialect::quoteName( strtolower( is_array( $field ) ? $field['name'] : $field ) );
            // the primary key is the document's key: an index on its columns serves range scans and joins
            $signature = implode( ',', $fields );
            if ( isset( $seen[$signature] ) )
                continue;
            $seen[$signature] = true;
            $indexName = self::indexName( $table, isset( $index['type'] ) && $index['type'] === 'primary' ? 'pk_' . $table : $name );
            $list[$indexName] = 'CREATE INDEX ' . expCouchbaseDialect::quoteName( $indexName ) . " IF NOT EXISTS ON $q(" . implode( ', ', $fields ) . ")$with";
        }
        return $list;
    }

    /**
     * A column of the CREATE TABLE this handler writes.
     */
    protected function columnSQL( $name, array $field )
    {
        $type = isset( $field['type'] ) ? (string)$field['type'] : 'varchar';
        $sql = $name . ' ' . ( $type === 'auto_increment' ? 'int' : $type );
        if ( !empty( $field['length'] ) && in_array( $type, array( 'varchar', 'char', 'int', 'decimal' ), true ) )
            $sql .= '(' . (int)$field['length'] . ')';
        if ( !empty( $field['not_null'] ) )
            $sql .= ' NOT NULL';
        if ( isset( $field['default'] ) && $field['default'] !== false && $field['default'] !== null )
            $sql .= ' DEFAULT ' . ( is_int( $field['default'] ) || is_float( $field['default'] ) || ( $type !== 'varchar' && $type !== 'char' && $type !== 'longtext' && $type !== 'text' && is_numeric( $field['default'] ) )
                                    ? $field['default'] : "'" . $this->escapeSQLString( $field['default'] ) . "'" );
        if ( $type === 'auto_increment' )
            $sql .= ' AUTO_INCREMENT';
        return $sql;
    }

    function generateTableSQLList( $tableName, $table, $params, $separateTypes )
    {
        $tableName = strtolower( $tableName );
        $parts = array();
        foreach ( $table['fields'] as $name => $field )
            $parts[] = '  ' . $this->columnSQL( strtolower( $name ), (array)$field );
        foreach ( isset( $table['indexes'] ) ? $table['indexes'] : array() as $indexName => $index )
        {
            $fields = array();
            foreach ( $index['fields'] as $field )
                $fields[] = strtolower( is_array( $field ) ? $field['name'] : $field );
            if ( isset( $index['type'] ) && $index['type'] === 'primary' )
                $parts[] = '  PRIMARY KEY (' . implode( ', ', $fields ) . ')';
            else
                $parts[] = '  ' . ( isset( $index['type'] ) && $index['type'] === 'unique' ? 'UNIQUE KEY ' : 'KEY ' ) . $indexName . ' (' . implode( ', ', $fields ) . ')';
        }
        return array( 'CREATE TABLE ' . $tableName . " (\n" . implode( ",\n", $parts ) . "\n)" );
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
        return 'ALTER TABLE ' . $table_name . ' ADD ' . $this->columnSQL( strtolower( $field_name ), (array)$def ) . ";\n";
    }

    function generateAlterFieldSql( $table_name, $field_name, $def, $params )
    {
        return 'ALTER TABLE ' . $table_name . ' MODIFY ' . $this->columnSQL( strtolower( $field_name ), (array)$def ) . ";\n";
    }

    function generateDropFieldSql( $table_name, $field_name, $params )
    {
        return 'ALTER TABLE ' . $table_name . ' DROP COLUMN ' . strtolower( $field_name ) . ";\n";
    }

    function generateAddIndexSql( $table_name, $index_name, $def, $params, $withClosure = true )
    {
        $fields = array();
        foreach ( $def['fields'] as $field )
            $fields[] = strtolower( is_array( $field ) ? $field['name'] : $field );
        if ( isset( $def['type'] ) && $def['type'] === 'primary' )
            return '-- ' . $table_name . ': the primary key of a Couchbase table is the key of its documents' . "\n";
        return 'CREATE ' . ( isset( $def['type'] ) && $def['type'] === 'unique' ? 'UNIQUE ' : '' ) . 'INDEX ' . $index_name . ' ON ' . $table_name
             . ' (' . implode( ', ', $fields ) . ')' . ( $withClosure ? ";\n" : '' );
    }

    function generateDropIndexSql( $table_name, $index_name, $def, $params = array() )
    {
        return 'DROP INDEX ' . $index_name . ' ON ' . $table_name . ";\n";
    }

    function generateDropTable( $table )
    {
        return 'DROP TABLE IF EXISTS ' . $table . ";\n";
    }

    /**
     * The tables of the scope as kept in exp_couchbase_schema.
     */
    function schema( $params = array() )
    {
        $params = array_merge( array( 'meta_data' => false, 'format' => 'generic', 'sort_columns' => true, 'sort_indexes' => true ), $params );
        if ( $this->Schema === false )
        {
            $schema = array();
            $db = $this->DBInstance;
            if ( !$db instanceof expCouchbaseDB || !$db->isConnected() )
                return array();
            $collections = array_flip( (array)$db->relationList( eZDBInterface::RELATION_TABLE ) );
            foreach ( $db->definitions( true ) as $table => $definition )
            {
                if ( !isset( $collections[$table] ) )
                    continue;
                if ( isset( $params['table_include'] ) && is_array( $params['table_include'] ) && !in_array( $table, $params['table_include'] ) )
                    continue;
                $fields = array();
                foreach ( (array)$definition['fields'] as $name => $field )
                    $fields[strtolower( $name )] = (array)$field;
                if ( $params['sort_columns'] )
                    ksort( $fields );
                $indexes = isset( $definition['indexes'] ) ? (array)$definition['indexes'] : array();
                if ( $params['sort_indexes'] )
                    ksort( $indexes );
                $schema[$table] = array( 'name' => $table, 'fields' => $fields, 'indexes' => $indexes );
            }
            ksort( $schema );
            $this->Schema = $schema;
        }
        $this->transformSchema( $this->Schema, $params['format'] == 'local' );
        return $this->Schema;
    }
}
?>
