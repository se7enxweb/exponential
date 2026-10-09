<?php
/**
 * File containing the Exponential\Service\TrashList class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

namespace Exponential\Service;

/**
 * What the trash view (content/trash) shows about each item, its filters and its summary line.
 * Who moved an item to the trash comes from the trash row (trashed_by, trashed_via), for items trashed before
 * those columns existed from what TrashRecord's file still holds; everything else is read from the trash rows,
 * the archived object and the tree. Guide: doc/bc/6.0/trash.md
 *
 *   TrashList::filters( $viewParameters )        the filters in the URL: (trashed_by) (class) (from) (to)
 *   TrashList::filterURI( $filters )             the same as a URL part
 *   TrashList::listParams( $filters, $context )  eZContentObjectTrashNode::trashList() parameters
 *   TrashList::context()                         the trash rows (light) and the recorded entries, read once
 *   TrashList::describe( $nodes, $context )      one hash per trash node for the template
 *   TrashList::summary( $context )               items, removed directly, below them, oldest
 *   TrashList::userOptions( $context )           who trashed what is in the trash now
 *   TrashList::classOptions()                    the classes of what is in the trash now
 */
class TrashList
{
    /**
     * @param array $viewParameters
     * @return array( 'trashed_by' => int|'unknown'|false, 'class' => int|false, 'from' => 'Y-m-d'|false, 'to' => 'Y-m-d'|false )
     */
    public static function filters( array $viewParameters )
    {
        $filters = array( 'trashed_by' => false, 'class' => false, 'from' => false, 'to' => false );
        if ( isset( $viewParameters['trashed_by'] ) )
        {
            $by = (string)$viewParameters['trashed_by'];
            if ( $by === 'unknown' )
                $filters['trashed_by'] = 'unknown';
            else if ( ctype_digit( $by ) && (int)$by > 0 )
                $filters['trashed_by'] = (int)$by;
        }
        if ( isset( $viewParameters['class'] ) && ctype_digit( (string)$viewParameters['class'] ) && (int)$viewParameters['class'] > 0 )
            $filters['class'] = (int)$viewParameters['class'];
        foreach ( array( 'from', 'to' ) as $key )
        {
            if ( isset( $viewParameters[$key] ) && self::isDate( (string)$viewParameters[$key] ) )
                $filters[$key] = (string)$viewParameters[$key];
        }
        return $filters;
    }

    /**
     * @param string $date
     * @return bool Y-m-d and a real date
     */
    public static function isDate( $date )
    {
        if ( !preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/', $date, $m ) )
            return false;
        return checkdate( (int)$m[2], (int)$m[3], (int)$m[1] );
    }

    /**
     * @param array $filters from filters()
     * @return string e.g. "/(trashed_by)/14/(from)/2026-10-01", or ''
     */
    public static function filterURI( array $filters )
    {
        $uri = '';
        foreach ( array( 'trashed_by', 'class', 'from', 'to' ) as $key )
        {
            if ( isset( $filters[$key] ) && $filters[$key] !== false && $filters[$key] !== '' )
                $uri .= '/(' . $key . ')/' . rawurlencode( (string)$filters[$key] );
        }
        return $uri;
    }

    /**
     * @param array $filters
     * @return bool any filter is set
     */
    public static function isFiltered( array $filters )
    {
        return self::filterURI( $filters ) !== '';
    }

    /**
     * The trash rows without joins (one table, so it reads on every engine) and the recorded entries.
     *
     * @return array( 'rows' => array( node_id => row ), 'records' => array( object id => entry ) ) records only those matching a row
     */
    public static function context()
    {
        $db = \eZDB::instance();
        $rows = array();
        // all columns: before the database update has added trashed_by and trashed_via the view keeps working
        $result = $db->arrayQuery( 'SELECT * FROM ezcontentobject_trash' );
        foreach ( is_array( $result ) ? $result : array() as $row )
            $rows[(int)$row['node_id']] = $row;

        $records = array();
        $map = null;
        foreach ( $rows as $row )
        {
            if ( isset( $row['trashed_by'] ) && (int)$row['trashed_by'] > 0 )
            {
                $records[(int)$row['contentobject_id']] = array( 'user_id' => (int)$row['trashed_by'], 'user_name' => '',
                                                                 'via' => isset( $row['trashed_via'] ) ? (string)$row['trashed_via'] : '',
                                                                 'source' => 'row' );
                continue;
            }
            // trashed before the columns existed: what the old file holds, read once and only when needed
            if ( $map === null )
                $map = class_exists( 'Exponential\\Service\\TrashRecord' ) ? TrashRecord::all() : array();
            $entry = $map ? TrashRecord::entryFor( $map, $row['contentobject_id'], $row['node_id'], $row['trashed'] ) : null;
            if ( $entry )
                $records[(int)$row['contentobject_id']] = array_merge( $entry, array( 'source' => 'file' ) );
        }
        return array( 'rows' => $rows, 'records' => $records );
    }

    /**
     * @param array $filters
     * @param array $context from context()
     * @return array extra parameters for eZContentObjectTrashNode::trashList()
     */
    public static function listParams( array $filters, array $context )
    {
        $params = array();
        if ( $filters['class'] )
            $params['ClassIDList'] = array( $filters['class'] );
        if ( $filters['from'] )
            $params['TrashedFrom'] = (int)strtotime( $filters['from'] . ' 00:00:00' );
        if ( $filters['to'] )
            $params['TrashedTo'] = (int)strtotime( $filters['to'] . ' 23:59:59' );
        // the column in SQL; the few rows known only from the old file as a list of their objects
        if ( $filters['trashed_by'] === 'unknown' )
        {
            $params['TrashedByUnknown'] = true;
            $params['TrashedByFileObjectIDList'] = array();
            foreach ( $context['records'] as $objectID => $entry )
            {
                if ( isset( $entry['source'] ) && $entry['source'] === 'file' )
                    $params['TrashedByFileObjectIDList'][] = (int)$objectID;
            }
        }
        else if ( $filters['trashed_by'] )
        {
            $params['TrashedBy'] = (int)$filters['trashed_by'];
            $params['TrashedByFileObjectIDList'] = array();
            foreach ( $context['records'] as $objectID => $entry )
            {
                if ( isset( $entry['source'] ) && $entry['source'] === 'file' && (int)$entry['user_id'] === (int)$filters['trashed_by'] )
                    $params['TrashedByFileObjectIDList'][] = (int)$objectID;
            }
        }
        return $params;
    }

    /**
     * @param array $context
     * @return array( 'items' => int, 'top' => int, 'below' => int, 'oldest' => int|false, 'recorded' => int )
     */
    public static function summary( array $context )
    {
        $top = 0;
        $oldest = false;
        foreach ( $context['rows'] as $row )
        {
            if ( !isset( $context['rows'][(int)$row['parent_node_id']] ) )
                $top++;
            if ( $oldest === false || (int)$row['trashed'] < $oldest )
                $oldest = (int)$row['trashed'];
        }
        $items = count( $context['rows'] );
        return array( 'items' => $items, 'top' => $top, 'below' => $items - $top, 'oldest' => $oldest,
                      'recorded' => count( $context['records'] ) );
    }

    /**
     * @param array $context
     * @return array of hash( 'id', 'name', 'count' ), by name
     */
    public static function userOptions( array $context )
    {
        $users = array();
        foreach ( $context['records'] as $entry )
        {
            $id = (int)$entry['user_id'];
            if ( !isset( $users[$id] ) )
                $users[$id] = array( 'id' => $id, 'name' => self::userName( $id, $entry['user_name'] ), 'count' => 0 );
            $users[$id]['count']++;
        }
        usort( $users, function ( $a, $b ) { return strcasecmp( $a['name'], $b['name'] ); } );
        return $users;
    }

    /**
     * @return array of hash( 'id', 'name' ), by name
     */
    public static function classOptions()
    {
        $db = \eZDB::instance();
        $result = $db->arrayQuery( 'SELECT DISTINCT ezcontentobject.contentclass_id AS id FROM ezcontentobject_trash, ezcontentobject '
                                 . 'WHERE ezcontentobject.id = ezcontentobject_trash.contentobject_id' );
        $classes = array();
        foreach ( is_array( $result ) ? $result : array() as $row )
        {
            $class = \eZContentClass::fetch( (int)$row['id'] );
            if ( $class )
                $classes[] = array( 'id' => (int)$row['id'], 'name' => (string)$class->attribute( 'name' ) );
        }
        usort( $classes, function ( $a, $b ) { return strcasecmp( $a['name'], $b['name'] ); } );
        return $classes;
    }

    /**
     * One hash per trash node with everything the trash view shows about it.
     *
     * @param \eZContentObjectTrashNode[] $trashNodes
     * @param array $context
     * @return array
     */
    public static function describe( array $trashNodes, array $context )
    {
        $rows = $context['rows'];

        // the ancestors of every item on the page, in the tree or in the trash, read once
        $ancestorIDs = array();
        foreach ( $trashNodes as $trashNode )
        {
            foreach ( self::ancestorIDs( $trashNode->attribute( 'path_string' ) ) as $id )
                $ancestorIDs[$id] = $id;
        }
        $treeNodes = array();
        if ( $ancestorIDs )
        {
            $list = \eZContentObjectTreeNode::fetch( array_values( $ancestorIDs ), false, true );
            if ( $list instanceof \eZContentObjectTreeNode )
                $list = array( $list );
            foreach ( is_array( $list ) ? $list : array() as $node )
                $treeNodes[(int)$node->attribute( 'node_id' )] = $node;
        }
        $trashedObjectIDs = array();
        foreach ( $ancestorIDs as $id )
        {
            if ( !isset( $treeNodes[$id] ) && isset( $rows[$id] ) )
                $trashedObjectIDs[] = (int)$rows[$id]['contentobject_id'];
        }
        $trashedObjects = $trashedObjectIDs ? \eZContentObject::fetchIDArray( $trashedObjectIDs ) : array();

        $items = array();
        foreach ( $trashNodes as $trashNode )
        {
            $object = $trashNode->attribute( 'object' );
            $objectID = (int)$trashNode->attribute( 'contentobject_id' );
            $nodeID = (int)$trashNode->attribute( 'node_id' );
            $pathString = (string)$trashNode->attribute( 'path_string' );
            $parentNodeID = (int)$trashNode->attribute( 'parent_node_id' );

            // the original place, with names
            $path = array();
            foreach ( self::ancestorIDs( $pathString ) as $id )
            {
                if ( isset( $treeNodes[$id] ) )
                    $path[] = array( 'name' => (string)$treeNodes[$id]->attribute( 'name' ), 'state' => 'tree',
                                     'url' => (string)$treeNodes[$id]->attribute( 'url_alias' ), 'object_id' => 0 );
                else if ( isset( $rows[$id] ) )
                {
                    $parentObjectID = (int)$rows[$id]['contentobject_id'];
                    $name = isset( $trashedObjects[$parentObjectID] ) ? (string)$trashedObjects[$parentObjectID]->attribute( 'name' ) : '#' . $id;
                    $path[] = array( 'name' => $name, 'state' => 'trash', 'url' => false, 'object_id' => $parentObjectID );
                }
                else
                    $path[] = array( 'name' => '#' . $id, 'state' => 'gone', 'url' => false, 'object_id' => 0 );
            }

            // can it go back where it was: the parent in the tree at the same place, in the trash, moved, gone
            $parentState = 'gone';
            $parentTrashObjectID = 0;
            if ( isset( $rows[$parentNodeID] ) )
            {
                $parentState = 'trash';
                $parentTrashObjectID = (int)$rows[$parentNodeID]['contentobject_id'];
            }
            else if ( isset( $treeNodes[$parentNodeID] ) )
            {
                $parentState = $trashNode->originalParent() ? 'exists' : 'moved';
            }

            // nodes that were below it: trash rows under its path
            $below = 0;
            foreach ( $rows as $row )
            {
                if ( (int)$row['node_id'] !== $nodeID && strpos( (string)$row['path_string'], $pathString ) === 0 )
                    $below++;
            }

            $owner = ( $object && $object->attribute( 'owner_id' ) ) ? \eZContentObject::fetch( (int)$object->attribute( 'owner_id' ) ) : null;
            $version = $object ? $object->currentVersion() : null;
            $modifier = $version ? $version->attribute( 'creator' ) : null;

            $languages = array();
            if ( $object )
            {
                foreach ( (array)$object->allLanguages() as $language )
                    $languages[] = array( 'locale' => (string)$language->attribute( 'locale' ), 'name' => (string)$language->attribute( 'name' ) );
            }

            // still placed in the tree: then it is not really gone
            $otherLocations = array();
            $located = \eZContentObjectTreeNode::fetchByContentObjectID( $objectID );
            foreach ( is_array( $located ) ? $located : array() as $node )
                $otherLocations[] = array( 'node_id' => (int)$node->attribute( 'node_id' ), 'name' => (string)$node->attribute( 'name' ),
                                           'url' => (string)$node->attribute( 'url_alias' ),
                                           'path' => (string)$node->attribute( 'path_identification_string' ) );

            $section = $object ? \eZSection::fetch( $object->attribute( 'section_id' ) ) : null;

            $trashedBy = false;
            if ( isset( $context['records'][$objectID] ) )
            {
                $entry = $context['records'][$objectID];
                $trashedBy = array( 'user_id' => (int)$entry['user_id'],
                                    'name' => self::userName( (int)$entry['user_id'], $entry['user_name'] ),
                                    'via' => (string)$entry['via'] );
            }

            $items[] = array( 'node' => $trashNode,
                              'object' => $object,
                              'object_id' => $objectID,
                              'node_id' => $nodeID,
                              'trashed' => (int)$trashNode->attribute( 'trashed' ),
                              'trashed_by' => $trashedBy,
                              'section_name' => $section ? (string)$section->attribute( 'name' ) : false,
                              'owner_name' => $owner ? (string)$owner->attribute( 'name' ) : false,
                              'owner_id' => $owner ? (int)$owner->attribute( 'id' ) : 0,
                              'modifier_name' => $modifier ? (string)$modifier->attribute( 'name' ) : false,
                              'modifier_id' => $modifier ? (int)$modifier->attribute( 'id' ) : 0,
                              'published' => $object ? (int)$object->attribute( 'published' ) : 0,
                              'modified' => $object ? (int)$object->attribute( 'modified' ) : 0,
                              'languages' => $languages,
                              'other_locations' => $otherLocations,
                              'subtree_count' => $below,
                              'path' => $path,
                              'parent_state' => $parentState,
                              'parent_trash_object_id' => $parentTrashObjectID );
        }
        return $items;
    }

    /**
     * @param string $pathString "/1/2/43/120/130/"
     * @return int[] the ancestors without the root node 1 and without the node itself: 2, 43, 120
     */
    protected static function ancestorIDs( $pathString )
    {
        $ids = array_values( array_filter( array_map( 'intval', explode( '/', trim( (string)$pathString, '/' ) ) ) ) );
        array_pop( $ids );
        return array_values( array_filter( $ids, function ( $id ) { return $id > 1; } ) );
    }

    /**
     * The user's current name, or the one recorded when the user no longer exists.
     *
     * @param int $userID
     * @param string $recordedName
     * @return string
     */
    protected static function userName( $userID, $recordedName )
    {
        $object = $userID > 0 ? \eZContentObject::fetch( $userID ) : null;
        if ( $object )
            return (string)$object->attribute( 'name' );
        return $recordedName !== '' ? (string)$recordedName : '#' . (int)$userID;
    }
}
