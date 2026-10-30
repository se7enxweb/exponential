<?php
/**
 * File containing the expDataSyncDefinition, expDataSyncLink and expDataSyncConflict classes.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A synchronisation between content and a data table (table exp_data_sync; doc/eng-us/features/6.0/data-tables.md,
 * "Synchronisation"): the persistent class, the content class and subtree, the map of columns to content, the key,
 * the direction (content_to_table, table_to_content, two_way), the mode (once, scheduled, live) and the conflict rule
 * (newest, content, table, ask). expDataSync runs it.
 *
 * @since 6.0.15
 * @package kernel
 */
class expDataSyncDefinition extends eZPersistentObject
{
    const TABLE = 'exp_data_sync';
    const DIRECTIONS = array( 'content_to_table', 'table_to_content', 'two_way' );
    const MODES = array( 'once', 'scheduled', 'live' );
    const RULES = array( 'newest', 'content', 'table', 'ask' );

    public static function definition()
    {
        static $definition = null;
        if ( $definition === null )
        {
            $int = function ( $name ) { return array( 'name' => $name, 'datatype' => 'integer', 'default' => 0, 'required' => true ); };
            $str = function ( $name ) { return array( 'name' => $name, 'datatype' => 'string', 'default' => '', 'required' => true ); };
            $text = function ( $name ) { return array( 'name' => $name, 'datatype' => 'text', 'default' => '', 'required' => false ); };
            $definition = array(
                'fields' => array(
                    'id' => $int( 'ID' ), 'name' => $str( 'Name' ), 'direction' => $str( 'Direction' ), 'run_mode' => $str( 'RunMode' ),
                    'class_name' => $str( 'ClassName' ), 'content_class' => $str( 'ContentClass' ),
                    'subtree_node_id' => $int( 'SubtreeNodeID' ) + array( 'foreign_class' => 'eZContentObjectTreeNode', 'foreign_attribute' => 'node_id', 'multiplicity' => '1..*' ),
                    'map' => $text( 'Map' ), 'key_columns' => $str( 'KeyColumns' ), 'conflict_rule' => $str( 'ConflictRule' ),
                    'interval_seconds' => $int( 'IntervalSeconds' ), 'last_run' => $int( 'LastRun' ), 'next_run' => $int( 'NextRun' ),
                    'last_run_id' => $int( 'LastRunID' ), 'state' => $str( 'State' ), 'options' => $text( 'Options' ),
                    'created' => $int( 'Created' ), 'modified' => $int( 'Modified' ),
                    'creator_id' => $int( 'CreatorID' ) + array( 'foreign_class' => 'eZUser', 'foreign_attribute' => 'contentobject_id', 'multiplicity' => '1..*' ),
                ),
                'keys' => array( 'id' ),
                'increment_key' => 'id',
                'function_attributes' => array( 'map_array' => 'mapArray', 'options_array' => 'optionsArray', 'keys_array' => 'keysArray',
                                                'open_conflicts' => 'openConflictCount' ),
                'sort' => array( 'name' => 'asc' ),
                'class_name' => 'expDataSyncDefinition',
                'name' => self::TABLE,
            );
        }
        return $definition;
    }

    /** @return expDataSyncDefinition[] */
    public static function fetchList( $conditions = null )
    {
        $list = self::fetchObjectList( self::definition(), null, $conditions, array( 'name' => 'asc' ) );
        return is_array( $list ) ? $list : array();
    }

    /** @return expDataSyncDefinition|null */
    public static function fetchByID( $id )
    {
        return self::fetchObject( self::definition(), null, array( 'id' => (int)$id ) );
    }

    /** @return array column => source (@object_id, @node_id, ..., or an attribute identifier) */
    public function mapArray()
    {
        $map = json_decode( (string)$this->attribute( 'map' ), true );
        return is_array( $map ) ? $map : array();
    }

    /** @return array */
    public function optionsArray()
    {
        $options = json_decode( (string)$this->attribute( 'options' ), true );
        return is_array( $options ) ? $options : array();
    }

    /** @return string[] the key columns */
    public function keysArray()
    {
        return array_values( array_filter( array_map( 'trim', explode( ',', (string)$this->attribute( 'key_columns' ) ) ), 'strlen' ) );
    }

    /** @return int */
    public function openConflictCount()
    {
        return expDataSync::installed() ? (int)eZPersistentObject::count( expDataSyncConflict::definition(), array( 'sync_id' => (int)$this->attribute( 'id' ), 'state' => 'open' ) ) : 0;
    }
}

/**
 * The pairing of a row and a content object of a synchronisation (table exp_data_sync_link): the row's key, the object,
 * and the hashes and times of both sides at the last synchronisation, which tell what changed since.
 *
 * @since 6.0.15
 * @package kernel
 */
class expDataSyncLink extends eZPersistentObject
{
    const TABLE = 'exp_data_sync_link';

    public static function definition()
    {
        static $definition = null;
        if ( $definition === null )
        {
            $int = function ( $name ) { return array( 'name' => $name, 'datatype' => 'integer', 'default' => 0, 'required' => true ); };
            $str = function ( $name ) { return array( 'name' => $name, 'datatype' => 'string', 'default' => '', 'required' => true ); };
            $definition = array(
                'fields' => array(
                    'id' => $int( 'ID' ), 'sync_id' => $int( 'SyncID' ), 'row_key' => $str( 'RowKey' ),
                    'object_id' => $int( 'ObjectID' ) + array( 'foreign_class' => 'eZContentObject', 'foreign_attribute' => 'id', 'multiplicity' => '1..*' ),
                    'row_hash' => $str( 'RowHash' ), 'content_hash' => $str( 'ContentHash' ),
                    'row_modified' => $int( 'RowModified' ), 'content_modified' => $int( 'ContentModified' ), 'synced' => $int( 'Synced' ),
                ),
                'keys' => array( 'id' ),
                'increment_key' => 'id',
                'sort' => array( 'id' => 'asc' ),
                'class_name' => 'expDataSyncLink',
                'name' => self::TABLE,
            );
        }
        return $definition;
    }
}

/**
 * A conflict of a two-way synchronisation with the rule "ask" (table exp_data_sync_conflict): both sides changed since
 * the last synchronisation; the values of both are kept until someone takes one in the administration.
 *
 * @since 6.0.15
 * @package kernel
 */
class expDataSyncConflict extends eZPersistentObject
{
    const TABLE = 'exp_data_sync_conflict';

    public static function definition()
    {
        static $definition = null;
        if ( $definition === null )
        {
            $int = function ( $name ) { return array( 'name' => $name, 'datatype' => 'integer', 'default' => 0, 'required' => true ); };
            $str = function ( $name ) { return array( 'name' => $name, 'datatype' => 'string', 'default' => '', 'required' => true ); };
            $text = function ( $name ) { return array( 'name' => $name, 'datatype' => 'text', 'default' => '', 'required' => false ); };
            $definition = array(
                'fields' => array(
                    'id' => $int( 'ID' ), 'sync_id' => $int( 'SyncID' ), 'link_id' => $int( 'LinkID' ),
                    'object_id' => $int( 'ObjectID' ) + array( 'foreign_class' => 'eZContentObject', 'foreign_attribute' => 'id', 'multiplicity' => '1..*' ),
                    'row_key' => $str( 'RowKey' ), 'content_values' => $text( 'ContentValues' ), 'row_values' => $text( 'RowValues' ),
                    'detected' => $int( 'Detected' ), 'state' => $str( 'State' ), 'resolution' => $str( 'Resolution' ),
                    'resolved' => $int( 'Resolved' ),
                    'resolver_id' => $int( 'ResolverID' ) + array( 'foreign_class' => 'eZUser', 'foreign_attribute' => 'contentobject_id', 'multiplicity' => '1..*' ),
                ),
                'keys' => array( 'id' ),
                'increment_key' => 'id',
                'function_attributes' => array( 'content_array' => 'contentArray', 'row_array' => 'rowArray', 'differences' => 'differences' ),
                'sort' => array( 'detected' => 'desc' ),
                'class_name' => 'expDataSyncConflict',
                'name' => self::TABLE,
            );
        }
        return $definition;
    }

    /** @return array */
    public function contentArray()
    {
        $values = json_decode( (string)$this->attribute( 'content_values' ), true );
        return is_array( $values ) ? $values : array();
    }

    /** @return array */
    public function rowArray()
    {
        $values = json_decode( (string)$this->attribute( 'row_values' ), true );
        return is_array( $values ) ? $values : array();
    }

    /** @return array list of column, content, row for the columns that differ */
    public function differences()
    {
        $content = $this->contentArray();
        $row = $this->rowArray();
        $out = array();
        foreach ( array_unique( array_merge( array_keys( $content ), array_keys( $row ) ) ) as $column )
        {
            $a = isset( $content[$column] ) ? (string)$content[$column] : '';
            $b = isset( $row[$column] ) ? (string)$row[$column] : '';
            if ( $a !== $b )
                $out[] = array( 'column' => $column, 'content' => mb_substr( $a, 0, 300 ), 'row' => mb_substr( $b, 0, 300 ) );
        }
        return $out;
    }
}
?>
