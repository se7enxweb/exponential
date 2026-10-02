<?php
/**
 * The common base of the shipped column families of the admin subitems list.
 *
 * A family class covers several columns that read the same kernel object (the node, the object,
 * its versions, its URL aliases ...). The [Column_<key>] block picks the column with Field=<name>,
 * and value() calls the family's method field<Name>() (field=parent_node_id -> fieldParentNodeId()).
 * An unknown Field, a missing object or any error gives null: a column never breaks the list.
 *
 * The families keep small per-request memos (see memo()), so the columns of one row that read the
 * same data -- the version statistics, the data map, the URL alias rows -- share one query.
 * Guide: doc/bc/6.0/subitems-table-options.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

abstract class expSubitemsFieldColumn extends expSubitemsColumn
{
    /** @var array per-request memo: bucket => key => value */
    protected static $memo = array();

    /**
     * The value of the column for $node: the result of field<Field>(), null when the field is
     * unknown, not applicable to the node, or failed.
     */
    public function value( eZContentObjectTreeNode $node )
    {
        $method = self::fieldMethod( $this->setting( 'Field', '' ) );
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
            if ( strpos( $method, 'field' ) === 0 && $method !== 'fields' && $method !== 'fieldMethod' )
                $fields[] = strtolower( preg_replace( '/(?<!^)[A-Z]/', '_$0', substr( $method, 5 ) ) );
        }
        sort( $fields );
        return $fields;
    }

    /**
     * Runs $compute once per request for ($bucket, $key) and returns the remembered result.
     */
    protected static function memo( $bucket, $key, $compute )
    {
        if ( !isset( self::$memo[$bucket] ) || !array_key_exists( $key, self::$memo[$bucket] ) )
        {
            // keep the memo bounded: one admin page is at most a few hundred rows
            if ( isset( self::$memo[$bucket] ) && count( self::$memo[$bucket] ) > 2000 )
                self::$memo[$bucket] = array();
            self::$memo[$bucket][$key] = $compute();
        }
        return self::$memo[$bucket][$key];
    }

    /** Forgets every memo (tests, and long running workers between requests). */
    public static function resetMemo()
    {
        self::$memo = array();
    }

    /** The node's object, or null. */
    protected static function object( eZContentObjectTreeNode $node )
    {
        $object = $node->attribute( 'object' );
        return $object instanceof eZContentObject ? $object : null;
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
        foreach ( $map as $attribute )
        {
            if ( in_array( $attribute->attribute( 'data_type_string' ), $types, true ) && $attribute->hasContent() )
                return $attribute;
        }
        return null;
    }

    /** A list setting (Name[]=...) as an array, whatever way it was written. */
    protected function listSetting( $name )
    {
        $value = $this->setting( $name, array() );
        if ( !is_array( $value ) )
            $value = $value === '' || $value === null ? array() : explode( ';', (string)$value );
        return array_values( array_filter( array_map( 'trim', $value ), 'strlen' ) );
    }

    /** A human readable byte size: 1.4 MB, 820 kB, 12 B. */
    public static function formatBytes( $bytes )
    {
        $bytes = (float)$bytes;
        foreach ( array( 'B', 'kB', 'MB', 'GB', 'TB' ) as $i => $unit )
        {
            if ( $bytes < 1024 || $unit === 'TB' )
                return ( $i === 0 ? (string)(int)$bytes : number_format( $bytes, $bytes < 10 ? 1 : 0 ) ) . ' ' . $unit;
            $bytes /= 1024;
        }
        return '';
    }

    /** "3 days ago", "in 2 hours", "just now" for a timestamp, relative to $now. */
    public static function formatAge( $timestamp, $now = null )
    {
        $timestamp = (int)$timestamp;
        if ( $timestamp <= 0 )
            return null;
        $now = $now === null ? time() : (int)$now;
        $diff = $now - $timestamp;
        $future = $diff < 0;
        $diff = abs( $diff );

        $units = array( array( 31536000, 'year', 'years' ), array( 2592000, 'month', 'months' ),
                        array( 604800, 'week', 'weeks' ), array( 86400, 'day', 'days' ),
                        array( 3600, 'hour', 'hours' ), array( 60, 'minute', 'minutes' ) );
        foreach ( $units as $unit )
        {
            if ( $diff >= $unit[0] )
            {
                $n = (int)floor( $diff / $unit[0] );
                $text = $n . ' ' . ezpI18n::tr( 'design/admin/node/view/full', $n === 1 ? $unit[1] : $unit[2] );
                return $future
                    ? ezpI18n::tr( 'design/admin/node/view/full', 'in %1', null, array( $text ) )
                    : ezpI18n::tr( 'design/admin/node/view/full', '%1 ago', null, array( $text ) );
            }
        }
        return ezpI18n::tr( 'design/admin/node/view/full', 'just now' );
    }
}
