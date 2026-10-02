<?php
/**
 * The common base of the shipped column families of the admin subitems list.
 *
 * A family class covers several columns that read the same kernel object (the node, the object,
 * its versions, its URL aliases ...). The [Column_<key>] block picks the column with Field=<name>,
 * and value() calls the family's method field<Name>() (field=parent_node_id -> fieldParentNodeId()).
 * An unknown Field, a missing object or any error gives null: a column never breaks the list.
 *
 * The families keep their per-request lookups in the shared memo (expSubitemsColumn::memo()), so
 * the columns of one row that read the same data -- the version rows, the data map, the URL alias
 * rows -- share one query. A family lists in prefetchSets() what it can load for a whole page at
 * once; prefetch() then runs prefetch<Set>( $nodes ) for the sets the column's Field= reads, which
 * fill the same memo buckets the field methods read, one query per page instead of one per row.
 * Guide: doc/bc/6.0/subitems-table-options.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

abstract class expSubitemsFieldColumn extends expSubitemsColumn
{
    /**
     * The value of the column for $node: the result of field<Field>(), null when the field is
     * unknown, not applicable to the node, or failed.
     */
    public function value( eZContentObjectTreeNode $node )
    {
        $method = self::fieldMethod( $this->field() );
        if ( $method === false || !method_exists( $this, $method ) )
            return null;

        try
        {
            return $this->$method( $node );
        }
        catch ( Throwable $e )
        {
            eZDebug::writeWarning( 'Column ' . $this->key . ': ' . $e->getMessage(), __METHOD__ );
            return null;
        }
    }

    /**
     * Runs prefetch<Set>( $nodes ) for every set of prefetchSets() that lists this column's Field=.
     */
    public function prefetch( array $nodes )
    {
        $field = $this->field();
        foreach ( static::prefetchSets() as $set => $fields )
        {
            if ( in_array( $field, $fields, true ) )
                $this->{'prefetch' . $set}( $nodes );
        }
    }

    /**
     * What the family can load for a whole page: set name => the Field= values that read it. The
     * family has a method prefetch<Set>( array $nodes ) for each set. Default: none.
     *
     * @return array
     */
    protected static function prefetchSets()
    {
        return array();
    }

    /** The column's Field= value. */
    protected function field()
    {
        return trim( (string)$this->setting( 'Field', '' ) );
    }

    /** The method name for a Field= value ("parent_node_id" -> "fieldParentNodeId"), false when malformed. */
    public static function fieldMethod( $field )
    {
        $field = trim( (string)$field );
        if ( $field === '' || !preg_match( '/^[a-z][a-z0-9_]*$/', $field ) )
            return false;
        return 'field' . str_replace( ' ', '', ucwords( str_replace( '_', ' ', $field ) ) );
    }

    /** The Field= names this family understands, read from its field*() methods. */
    public function fields()
    {
        $fields = array();
        foreach ( get_class_methods( $this ) as $method )
        {
            if ( strpos( $method, 'field' ) === 0 && !in_array( $method, array( 'field', 'fields', 'fieldMethod' ), true ) )
                $fields[] = strtolower( preg_replace( '/(?<!^)[A-Z]/', '_$0', substr( $method, 5 ) ) );
        }
        sort( $fields );
        return $fields;
    }

    /** The data maps of the page's objects, one query (the set "DataMap" of the families that read attributes). */
    protected function prefetchDataMap( array $nodes )
    {
        self::prefetchDataMaps( $nodes );
    }

    /** The node's object, or null. */
    protected static function object( eZContentObjectTreeNode $node )
    {
        $object = $node->attribute( 'object' );
        return $object instanceof eZContentObject ? $object : null;
    }

    /** An attribute of the node's object as an int, null when there is no object. */
    protected static function objectInt( eZContentObjectTreeNode $node, $attribute )
    {
        $object = self::object( $node );
        return $object ? (int)$object->attribute( $attribute ) : null;
    }

    /** The ids of the nodes' objects (each once). */
    protected static function objectIDs( array $nodes )
    {
        $ids = array();
        foreach ( $nodes as $node )
        {
            $id = (int)$node->attribute( 'contentobject_id' );
            if ( $id > 0 )
                $ids[$id] = $id;
        }
        return array_values( $ids );
    }

    /** The node ids of the nodes (each once). */
    protected static function nodeIDs( array $nodes )
    {
        $ids = array();
        foreach ( $nodes as $node )
        {
            $id = (int)$node->attribute( 'node_id' );
            if ( $id > 0 )
                $ids[$id] = $id;
        }
        return array_values( $ids );
    }

    /**
     * Counts the rows of a persistent table per value of $field, one grouped query, and remembers
     * the number for every id in the memo $bucket (0 for those without rows). Nothing is
     * remembered when the query fails, nor on MongoDB.
     *
     * @param string $bucket
     * @param array $definition an eZPersistentObject definition()
     * @param string $field the column grouped by
     * @param int[] $ids
     */
    protected static function prefetchCounts( $bucket, array $definition, $field, array $ids )
    {
        $ids = self::notMemoised( $bucket, $ids );
        if ( !$ids || !self::sqlDatabase() )
            return;
        $rows = eZPersistentObject::fetchObjectList( $definition, array( $field ), array( $field => array( $ids ) ),
                                                     null, null, false, array( $field ),
                                                     array( array( 'operation' => 'COUNT( * )', 'name' => 'row_count' ) ) );
        if ( !is_array( $rows ) )
            return;
        $counts = array_fill_keys( $ids, 0 );
        foreach ( $rows as $row )
            $counts[(int)$row[$field]] = (int)$row['row_count'];
        foreach ( $counts as $id => $count )
            self::remember( $bucket, $id, $count );
    }

    /** The ids of $ids the memo $bucket does not hold yet. */
    protected static function notMemoised( $bucket, array $ids )
    {
        return array_values( array_filter( $ids, function ( $id ) use ( $bucket ) { return !self::isMemoised( $bucket, $id ); } ) );
    }

    /**
     * The database for the grouped queries of the prefetch sets, null on MongoDB (no SQL there, and
     * its persistent object layer does not group): there the columns load per row as before.
     *
     * @return eZDBInterface|null
     */
    protected static function sqlDatabase()
    {
        $db = eZDB::instance();
        return $db->databaseName() === 'mongo' ? null : $db;
    }

    /** The data map of the node's object (memoised per object and version), an empty array when none. */
    protected static function dataMap( eZContentObjectTreeNode $node )
    {
        $object = self::object( $node );
        if ( !$object )
            return array();
        return self::memo( 'datamap', $object->attribute( 'id' ) . '/' . $object->attribute( 'current_version' ), function () use ( $node )
        {
            $map = $node->attribute( 'data_map' );
            return is_array( $map ) ? $map : array();
        } );
    }

    /** The attributes of the node's object whose datatype is one of $types (with content or not). */
    protected static function attributesOfType( eZContentObjectTreeNode $node, array $types )
    {
        $found = array();
        foreach ( self::dataMap( $node ) as $identifier => $attribute )
        {
            if ( in_array( $attribute->attribute( 'data_type_string' ), $types, true ) )
                $found[$identifier] = $attribute;
        }
        return $found;
    }

    /**
     * The first attribute of the object whose datatype is one of $types and that has content, or null.
     * $identifiers, when given, are tried first in that order.
     */
    protected static function firstAttribute( eZContentObjectTreeNode $node, array $types, array $identifiers = array() )
    {
        $map = self::dataMap( $node );
        foreach ( $identifiers as $identifier )
        {
            if ( isset( $map[$identifier] ) && in_array( $map[$identifier]->attribute( 'data_type_string' ), $types, true )
                 && $map[$identifier]->hasContent() )
                return $map[$identifier];
        }
        foreach ( self::attributesOfType( $node, $types ) as $attribute )
        {
            if ( $attribute->hasContent() )
                return $attribute;
        }
        return null;
    }

    /** A list setting (Name[]=...) as an array, whatever way it was written (see expSubitemsColumnRegistry::splitList()). */
    protected function listSetting( $name )
    {
        return expSubitemsColumnRegistry::splitList( $this->setting( $name, array() ) );
    }
}
