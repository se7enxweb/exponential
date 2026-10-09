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
 *   TrashList::context()                         whether the columns exist, the entries of the old file
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
     * What the view needs about the whole trash, kept small: whether the columns trashed_by and trashed_via exist,
     * and the entries of the old file <VarDir>/trash/trashed.json for rows without a trashed_by (read only when the
     * file exists). Everything else is asked of the database for the page: a trash of tens of thousands of rows is
     * not read whole.
     *
     * @return array( 'columns' => bool, 'records' => array( object id => entry ) ) records: those from the old file
     *               that match a trash row
     */
    public static function context()
    {
        $db = \eZDB::instance();
        $columns = TrashRecord::columnsExist( $db );
        $records = array();
        $map = TrashRecord::all();
        if ( $map )
        {
            // rows trashed before the columns existed; after movetrashrecords.php there are few or none
            $result = $db->arrayQuery( 'SELECT node_id, contentobject_id, trashed FROM ezcontentobject_trash'
                                     . ( $columns ? ' WHERE trashed_by = 0' : '' ) );
            foreach ( is_array( $result ) ? $result : array() as $row )
            {
                $entry = TrashRecord::entryFor( $map, $row['contentobject_id'], $row['node_id'], $row['trashed'] );
                if ( $entry )
                    $records[(int)$row['contentobject_id']] = array_merge( $entry, array( 'source' => 'file' ) );
            }
        }
        return array( 'columns' => $columns, 'records' => $records );
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
        if ( $filters['trashed_by'] && empty( $context['columns'] ) )
        {
            // before the database update: only the old file knows anything
            if ( $filters['trashed_by'] === 'unknown' )
                $params['ExcludeContentObjectIDList'] = array_keys( $context['records'] );
            else
            {
                $params['ContentObjectIDList'] = array();
                foreach ( $context['records'] as $objectID => $entry )
                {
                    if ( (int)$entry['user_id'] === (int)$filters['trashed_by'] )
                        $params['ContentObjectIDList'][] = (int)$objectID;
                }
            }
        }
        else if ( $filters['trashed_by'] === 'unknown' )
        {
            $params['TrashedByUnknown'] = true;
            $params['TrashedByFileObjectIDList'] = array();
            foreach ( $context['records'] as $objectID => $entry )
            {
                $params['TrashedByFileObjectIDList'][] = (int)$objectID;
            }
        }
        else if ( $filters['trashed_by'] )
        {
            $params['TrashedBy'] = (int)$filters['trashed_by'];
            $params['TrashedByFileObjectIDList'] = array();
            foreach ( $context['records'] as $objectID => $entry )
            {
                if ( (int)$entry['user_id'] === (int)$filters['trashed_by'] )
                    $params['TrashedByFileObjectIDList'][] = (int)$objectID;
            }
        }
        return $params;
    }

    /**
     * Counted by the database.
     *
     * @param array $context
     * @return array( 'items' => int, 'top' => int, 'below' => int, 'oldest' => int|false, 'recorded' => int )
     */
    public static function summary( array $context )
    {
        $db = \eZDB::instance();
        $totals = $db->arrayQuery( 'SELECT COUNT(*) AS items, MIN(trashed) AS oldest'
                                 . ( !empty( $context['columns'] ) ? ', SUM( CASE WHEN trashed_by > 0 THEN 1 ELSE 0 END ) AS recorded' : '' )
                                 . ' FROM ezcontentobject_trash' );
        // removed directly: the parent is not in the trash too
        $top = $db->arrayQuery( 'SELECT COUNT(*) AS top FROM ezcontentobject_trash t WHERE NOT EXISTS '
                              . '( SELECT 1 FROM ezcontentobject_trash p WHERE p.node_id = t.parent_node_id )' );
        $items = isset( $totals[0]['items'] ) ? (int)$totals[0]['items'] : 0;
        $topCount = isset( $top[0]['top'] ) ? (int)$top[0]['top'] : 0;
        $recorded = isset( $totals[0]['recorded'] ) ? (int)$totals[0]['recorded'] : 0;
        return array( 'items' => $items, 'top' => $topCount, 'below' => $items - $topCount,
                      'oldest' => $items > 0 ? (int)$totals[0]['oldest'] : false,
                      'recorded' => $recorded + count( $context['records'] ) );
    }

    /**
     * @param array $context
     * @return array of hash( 'id', 'name', 'count' ), by name
     */
    public static function userOptions( array $context )
    {
        $users = array();
        if ( !empty( $context['columns'] ) )
        {
            $db = \eZDB::instance();
            $result = $db->arrayQuery( 'SELECT trashed_by, COUNT(*) AS items FROM ezcontentobject_trash WHERE trashed_by > 0 GROUP BY trashed_by' );
            foreach ( is_array( $result ) ? $result : array() as $row )
            {
                $id = (int)$row['trashed_by'];
                $users[$id] = array( 'id' => $id, 'name' => self::userName( $id, '' ), 'count' => (int)$row['items'] );
            }
        }
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
        $db = \eZDB::instance();

        // the ancestors of every item on the page, in the tree or in the trash, read once
        $ancestorIDs = array();
        foreach ( $trashNodes as $trashNode )
        {
            foreach ( self::ancestorIDs( $trashNode->attribute( 'path_string' ) ) as $id )
                $ancestorIDs[$id] = $id;
        }
        // the trash rows among them (the parent is one of them, unless it is the root)
        $rows = array();
        if ( $ancestorIDs )
        {
            $result = $db->arrayQuery( 'SELECT node_id, contentobject_id FROM ezcontentobject_trash WHERE '
                                     . $db->generateSQLINStatement( array_values( $ancestorIDs ), 'node_id', false, true, 'int' ) );
            foreach ( is_array( $result ) ? $result : array() as $row )
                $rows[(int)$row['node_id']] = $row;
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

            // nodes that were below it: trash rows under its path, counted from the index on path_string
            $below = 0;
            $condition = self::belowCondition( $db, $pathString );
            if ( $condition !== false )
            {
                $result = $db->arrayQuery( 'SELECT COUNT(*) AS below FROM ezcontentobject_trash WHERE ' . $condition );
                $below = isset( $result[0]['below'] ) ? (int)$result[0]['below'] : 0;
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
            $trashedByID = !empty( $context['columns'] ) ? (int)$trashNode->attribute( 'trashed_by' ) : 0;
            if ( $trashedByID > 0 )
            {
                $trashedBy = array( 'user_id' => $trashedByID, 'name' => self::userName( $trashedByID, '' ),
                                    'via' => (string)$trashNode->attribute( 'trashed_via' ) );
            }
            else if ( isset( $context['records'][$objectID] ) )
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
     * The SQL condition for the trash rows below the path $pathString, answered from the index on path_string.
     * SQLite does not use an index for LIKE (it compares case-insensitively), so it gets a range that holds exactly
     * the strings that start with the path: "/1/2/43/" < path_string < "/1/2/430" ("0" follows "/"). The other
     * engines get LIKE with a fixed prefix, which they answer from the index: a range is not safe there, because a
     * locale collation (PostgreSQL with en_US.UTF-8) ignores the slashes when it compares.
     *
     * @param \eZDBInterface $db
     * @param string $pathString "/1/2/43/"
     * @return string|false false for a path that is not one
     */
    public static function belowCondition( $db, $pathString )
    {
        $pathString = (string)$pathString;
        if ( !preg_match( '#^/([0-9]+/)+$#', $pathString ) )
            return false;
        $path = $db->escapeString( $pathString );
        if ( $db->databaseName() === 'sqlite' )
            return "path_string > '$path' AND path_string < '" . $db->escapeString( substr( $pathString, 0, -1 ) . '0' ) . "'";
        return "path_string LIKE '$path%' AND path_string <> '$path'";
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
