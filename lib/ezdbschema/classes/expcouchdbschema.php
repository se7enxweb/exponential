<?php
/**
 * File containing the expCouchDBSchema class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The schema handler of Apache CouchDB (dbschema.ini: couchdb). It writes the same CREATE TABLE as the Couchbase
 * handler; expCouchDBDB answers it by keeping the table's definition (the document exp_couchdb_schema:<table>) and
 * making a Mango index per index of it. Reading a database back gives the definitions kept there.
 *
 * @since 6.0.15
 * @package lib
 */
class expCouchDBSchema extends expCouchbaseSchema
{
    function schemaType()
    {
        return 'couchdb';
    }

    function schemaName()
    {
        return 'Apache CouchDB';
    }

    function escapeSQLString( $value )
    {
        return str_replace( "'", "''", (string)$value );
    }

    function generateAlterFieldSql( $table_name, $field_name, $def, $params )
    {
        return 'ALTER TABLE ' . $table_name . ' MODIFY ' . $this->columnSQL( strtolower( $field_name ), (array)$def ) . ";\n";
    }

    function schema( $params = array() )
    {
        $params = array_merge( array( 'meta_data' => false, 'format' => 'generic', 'sort_columns' => true, 'sort_indexes' => true ), $params );
        if ( $this->Schema === false )
        {
            $schema = array();
            $db = $this->DBInstance;
            if ( !$db instanceof expCouchDBDB || !$db->isConnected() )
                return array();
            foreach ( $db->definitions( true ) as $table => $definition )
            {
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
            $this->Schema = $schema;
        }
        $this->transformSchema( $this->Schema, $params['format'] == 'local' );
        return $this->Schema;
    }
}
?>
