<?php
/**
 * File containing the expContentCheckUnreferenced class.
 *
 * The objects below a node that nothing refers to (doc/features/6.0/content-checks.md, "Unreferenced objects"):
 * published objects whose main location is below the node, that no other object refers to (expContentCheck,
 * "what counts as a reference"), and that the current user may read. One query counts them and one query reads a
 * page of them, each with the permission condition of content/read in its SQL; the sizes, locations and whether
 * the user may remove them are then read for that page only, a fixed number of queries per page.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expContentCheckUnreferenced
{
    /** The orders the list offers, the first is the default, with their columns */
    const SORTS = array( 'name' => 'ezcontentobject.name',
                         'class' => 'ezcontentclass.identifier',
                         'modified' => 'ezcontentobject.modified',
                         'published' => 'ezcontentobject.published',
                         'id' => 'ezcontentobject.id' );

    /** The datatypes whose files are measured for the size column */
    const FILE_DATATYPES = array( 'ezbinaryfile', 'ezmedia', 'ezimage' );

    /** At most this many classes are filtered on */
    const MAX_CLASSES = 50;

    /**
     * The parameters of a list from the request, each checked.
     *
     * @param array $input NodeID, ClassID (array or text), Sort, Order (asc, desc), Containers (1), Offset, Limit
     * @param int $defaultNodeID
     * @return array array( 'node_id', 'class_ids', 'sort', 'order', 'containers', 'offset', 'limit' )
     */
    public static function parameters( array $input, $defaultNodeID = 2 )
    {
        $sort = isset( $input['Sort'] ) && is_string( $input['Sort'] ) && isset( self::SORTS[$input['Sort']] ) ? $input['Sort'] : 'name';
        $order = isset( $input['Order'] ) && is_string( $input['Order'] ) && in_array( $input['Order'], array( 'asc', 'desc' ), true )
               ? $input['Order'] : ( in_array( $sort, array( 'modified', 'published' ), true ) ? 'desc' : 'asc' );
        $limit = isset( $input['Limit'] ) ? (int)$input['Limit'] : 25;
        return array( 'node_id' => expContentCheck::id( isset( $input['NodeID'] ) ? $input['NodeID'] : null, $defaultNodeID ),
                      'class_ids' => expContentCheck::idList( isset( $input['ClassID'] ) ? $input['ClassID'] : array(), self::MAX_CLASSES ),
                      'sort' => $sort,
                      'order' => $order,
                      'containers' => !empty( $input['Containers'] ) && is_scalar( $input['Containers'] ) && (string)$input['Containers'] === '1',
                      'offset' => isset( $input['Offset'] ) && is_numeric( $input['Offset'] ) ? max( 0, (int)$input['Offset'] ) : 0,
                      'limit' => max( 1, min( 200, $limit ) ) );
    }

    /**
     * The FROM and WHERE of the list (no database access).
     *
     * @param array $params from parameters()
     * @param string $subtreePattern from expContentCheck::subtreePattern()
     * @param array $permission array( 'from' => joins starting with a comma or empty, 'where' => conditions starting with AND or empty )
     * @return array array( 'from' => string, 'where' => string )
     */
    public static function conditions( array $params, $subtreePattern, array $permission )
    {
        if ( !preg_match( '#^/([0-9]+/)+%$#', (string)$subtreePattern ) )
            throw new InvalidArgumentException( 'not a subtree pattern' );
        $where = array( 'ezcontentobject_tree.contentobject_id = ezcontentobject.id',
                        'ezcontentobject_tree.node_id = ezcontentobject_tree.main_node_id',
                        "ezcontentobject_tree.path_string LIKE '" . $subtreePattern . "'",
                        'ezcontentobject_tree.node_id <> ' . (int)$params['node_id'],
                        'ezcontentobject.status = ' . eZContentObject::STATUS_PUBLISHED,
                        'ezcontentclass.id = ezcontentobject.contentclass_id',
                        'ezcontentclass.version = ' . eZContentClass::VERSION_STATUS_DEFINED );
        if ( $params['class_ids'] )
            $where[] = 'ezcontentobject.contentclass_id IN ( ' . expContentCheck::inList( $params['class_ids'] ) . ' )';
        if ( !$params['containers'] )
            $where[] = 'NOT EXISTS ( SELECT 1 FROM ezcontentobject_tree cc_child WHERE cc_child.parent_node_id = ezcontentobject_tree.node_id )';
        $where[] = 'NOT ' . expContentCheck::referenceExistsSQL( 'ezcontentobject.id' );
        return array( 'from' => 'ezcontentobject, ezcontentobject_tree, ezcontentclass' . ( isset( $permission['from'] ) ? (string)$permission['from'] : '' ),
                      'where' => implode( ' AND ', $where ) . ( isset( $permission['where'] ) ? ' ' . (string)$permission['where'] : '' ) );
    }

    /**
     * The count and the list query (no database access).
     *
     * @param array $params from parameters()
     * @param string $subtreePattern
     * @param array $permission
     * @return array array( 'count' => SQL, 'list' => SQL without LIMIT )
     */
    public static function queries( array $params, $subtreePattern, array $permission )
    {
        $c = self::conditions( $params, $subtreePattern, $permission );
        $column = self::SORTS[isset( self::SORTS[$params['sort']] ) ? $params['sort'] : 'name'];
        $direction = $params['order'] === 'desc' ? 'DESC' : 'ASC';
        $order = $column . ' ' . $direction . ( $column !== 'ezcontentobject.id' ? ', ezcontentobject.id ' . $direction : '' );
        return array( 'count' => 'SELECT COUNT(*) AS row_count FROM ' . $c['from'] . ' WHERE ' . $c['where'],
                      'list' => 'SELECT ezcontentobject.id AS object_id, ezcontentobject.name AS object_name,' .
                                ' ezcontentobject.contentclass_id AS class_id, ezcontentclass.identifier AS class_identifier,' .
                                ' ezcontentobject.modified AS modified, ezcontentobject.published AS published,' .
                                ' ezcontentobject.current_version AS current_version,' .
                                ' ezcontentobject_tree.node_id AS node_id, ezcontentobject_tree.path_identification_string AS node_path,' .
                                ' ezcontentobject_tree.is_hidden AS is_hidden, ezcontentobject_tree.is_invisible AS is_invisible' .
                                ' FROM ' . $c['from'] . ' WHERE ' . $c['where'] . ' ORDER BY ' . $order );
    }

    /**
     * The permission condition of content/read for the current user.
     *
     * @param string $subtreePath the path_string of the start node (lets the kernel leave out policies that cannot apply)
     * @return array array( 'from', 'where', 'temp_tables' )
     */
    public static function permission( $subtreePath, $nodeID = 0 )
    {
        $limitation = false;
        $limitationList = eZContentObjectTreeNode::getLimitationList( $limitation );
        $scope = eZContentObjectTreeNode::permissionFetchScope( $limitationList, (int)$nodeID, array( (string)$subtreePath ), array() );
        return eZContentObjectTreeNode::createPermissionCheckingSQL( $limitationList, 'ezcontentobject_tree', 'ezcontentobject_tree', $scope );
    }

    /**
     * One page of the list.
     *
     * @param array $params from parameters()
     * @return array array( 'node' => start node or null, 'count' => int, 'rows' => array of rows (describe()),
     *               'error' => '' | not_found | database )
     */
    public static function fetch( array $params )
    {
        $result = array( 'node' => null, 'count' => 0, 'rows' => array(), 'error' => '' );
        if ( !expContentCheck::isSupportedDatabase() )
            return array_merge( $result, array( 'error' => 'database' ) );
        $node = expContentCheck::readableNode( $params['node_id'] );
        $pattern = $node ? expContentCheck::subtreePattern( $node->attribute( 'path_string' ) ) : null;
        if ( !$node || $pattern === null )
            return array_merge( $result, array( 'error' => 'not_found' ) );
        $result['node'] = $node;

        $db = eZDB::instance();
        $permission = self::permission( $node->attribute( 'path_string' ), (int)$node->attribute( 'node_id' ) );
        try
        {
            $queries = self::queries( $params, $pattern, $permission );
            $count = $db->arrayQuery( $queries['count'] );
            $result['count'] = isset( $count[0]['row_count'] ) ? (int)$count[0]['row_count'] : 0;
            $rows = array();
            if ( $result['count'] > $params['offset'] )
                $rows = $db->arrayQuery( $queries['list'], array( 'offset' => (int)$params['offset'], 'limit' => (int)$params['limit'] ) );
        }
        finally
        {
            if ( !empty( $permission['temp_tables'] ) )
                $db->dropTempTableList( $permission['temp_tables'] );
        }
        $result['rows'] = self::describe( is_array( $rows ) ? $rows : array() );
        return $result;
    }

    /**
     * Of the given objects, those that nothing refers to now (the trash action asks this again before it acts).
     *
     * @param int[] $objectIDs
     * @return int[]
     */
    public static function unreferencedAmong( array $objectIDs )
    {
        $objectIDs = expContentCheck::idList( $objectIDs );
        if ( !$objectIDs )
            return array();
        $rows = eZDB::instance()->arrayQuery( 'SELECT ezcontentobject.id AS object_id FROM ezcontentobject' .
                                              ' WHERE ezcontentobject.id IN ( ' . expContentCheck::inList( $objectIDs ) . ' )' .
                                              ' AND NOT ' . expContentCheck::referenceExistsSQL( 'ezcontentobject.id' ) );
        $ids = array();
        foreach ( is_array( $rows ) ? $rows : array() as $row )
            $ids[] = (int)$row['object_id'];
        return $ids;
    }

    /**
     * The rows of a page with what the page shows besides the row: class name, size of the files, other locations,
     * sub-items and whether the user may move it to the trash. A fixed number of queries for the whole page.
     *
     * @param array $rows rows of the list query
     * @return array
     */
    public static function describe( array $rows )
    {
        if ( !$rows )
            return array();
        $db = eZDB::instance();
        $objectIDs = array();
        $nodeIDs = array();
        foreach ( $rows as $row )
        {
            $objectIDs[] = (int)$row['object_id'];
            $nodeIDs[] = (int)$row['node_id'];
        }

        $locations = array();
        foreach ( (array)$db->arrayQuery( 'SELECT contentobject_id, COUNT(*) AS row_count FROM ezcontentobject_tree WHERE contentobject_id IN ( ' .
                                          expContentCheck::inList( $objectIDs ) . ' ) GROUP BY contentobject_id' ) as $row )
            $locations[(int)$row['contentobject_id']] = (int)$row['row_count'];
        $children = array();
        foreach ( (array)$db->arrayQuery( 'SELECT parent_node_id, COUNT(*) AS row_count FROM ezcontentobject_tree WHERE parent_node_id IN ( ' .
                                          expContentCheck::inList( $nodeIDs ) . ' ) GROUP BY parent_node_id' ) as $row )
            $children[(int)$row['parent_node_id']] = (int)$row['row_count'];

        $removable = array();
        $nodes = eZContentObjectTreeNode::fetch( $nodeIDs );
        if ( $nodes instanceof eZContentObjectTreeNode )
            $nodes = array( $nodes );
        foreach ( is_array( $nodes ) ? $nodes : array() as $node )
            $removable[(int)$node->attribute( 'node_id' )] = (bool)$node->canRemove();

        $files = self::files( $objectIDs );
        $classNames = self::classNames();
        $described = array();
        foreach ( $rows as $row )
        {
            $objectID = (int)$row['object_id'];
            $nodeID = (int)$row['node_id'];
            $childCount = isset( $children[$nodeID] ) ? $children[$nodeID] : 0;
            $described[] = array( 'object_id' => $objectID,
                                  'node_id' => $nodeID,
                                  'name' => (string)$row['object_name'],
                                  'class_id' => (int)$row['class_id'],
                                  'class_identifier' => (string)$row['class_identifier'],
                                  'class_name' => isset( $classNames[(int)$row['class_id']] ) ? $classNames[(int)$row['class_id']] : (string)$row['class_identifier'],
                                  'modified' => (int)$row['modified'],
                                  'published' => (int)$row['published'],
                                  'path' => (string)$row['node_path'],
                                  'hidden' => (int)$row['is_hidden'] === 1 || (int)$row['is_invisible'] === 1,
                                  'locations' => isset( $locations[$objectID] ) ? $locations[$objectID] : 1,
                                  'children' => $childCount,
                                  'size' => isset( $files[$objectID] ) ? $files[$objectID]['size'] : null,
                                  'files' => isset( $files[$objectID] ) ? $files[$objectID]['count'] : 0,
                                  'can_trash' => $childCount === 0 && !empty( $removable[$nodeID] ) );
        }
        return $described;
    }

    /**
     * The names of the content classes, by id.
     *
     * @return string[]
     */
    public static function classNames()
    {
        $names = array();
        $classes = eZContentClass::fetchList( eZContentClass::VERSION_STATUS_DEFINED, true, false, array( 'identifier' => 'asc' ) );
        foreach ( is_array( $classes ) ? $classes : array() as $class )
            $names[(int)$class->attribute( 'id' )] = (string)$class->attribute( 'name' );
        return $names;
    }

    /**
     * The files of the current versions of objects (binary file, media, image) and their size on disk or in the
     * cluster: three queries for any number of objects, then one size lookup per file.
     *
     * @param int[] $objectIDs
     * @return array by object id: array( 'size' => bytes, 'count' => files )
     */
    public static function files( array $objectIDs )
    {
        $objectIDs = expContentCheck::idList( $objectIDs, 1000 );
        if ( !$objectIDs )
            return array();
        $db = eZDB::instance();
        $types = array();
        foreach ( self::FILE_DATATYPES as $type )
            $types[] = "'" . $db->escapeString( $type ) . "'";
        $attributes = $db->arrayQuery( 'SELECT ezcontentobject_attribute.id AS attribute_id, ezcontentobject_attribute.version AS version,' .
                                       ' ezcontentobject_attribute.contentobject_id AS object_id, ezcontentobject_attribute.data_type_string AS datatype,' .
                                       ' ezcontentobject_attribute.data_text AS data_text' .
                                       ' FROM ezcontentobject_attribute, ezcontentobject' .
                                       ' WHERE ezcontentobject.id IN ( ' . expContentCheck::inList( $objectIDs ) . ' )' .
                                       ' AND ezcontentobject_attribute.contentobject_id = ezcontentobject.id' .
                                       ' AND ezcontentobject_attribute.version = ezcontentobject.current_version' .
                                       ' AND ezcontentobject_attribute.data_type_string IN ( ' . implode( ', ', $types ) . ' )' );
        $paths = array();
        $byAttribute = array( 'ezbinaryfile' => array(), 'ezmedia' => array() );
        foreach ( is_array( $attributes ) ? $attributes : array() as $row )
        {
            $objectID = (int)$row['object_id'];
            if ( $row['datatype'] === 'ezimage' )
            {
                $path = self::imagePath( (string)$row['data_text'] );
                if ( $path !== '' )
                    $paths[$objectID][$path] = true;
            }
            else if ( isset( $byAttribute[$row['datatype']] ) )
                $byAttribute[$row['datatype']][(int)$row['attribute_id'] . '-' . (int)$row['version']] = $objectID;
        }
        foreach ( array( 'ezbinaryfile' => 'eZBinaryFile', 'ezmedia' => 'eZMedia' ) as $table => $class )
        {
            if ( !$byAttribute[$table] || !class_exists( $class ) )
                continue;
            $attributeIDs = array();
            foreach ( array_keys( $byAttribute[$table] ) as $key )
                $attributeIDs[] = (int)$key;
            $rows = $db->arrayQuery( 'SELECT * FROM ' . $table . ' WHERE contentobject_attribute_id IN ( ' . expContentCheck::inList( array_unique( $attributeIDs ) ) . ' )' );
            foreach ( is_array( $rows ) ? $rows : array() as $row )
            {
                $key = (int)$row['contentobject_attribute_id'] . '-' . (int)$row['version'];
                if ( !isset( $byAttribute[$table][$key] ) )
                    continue;
                $file = new $class( $row );
                $path = (string)$file->filePath();
                if ( $path !== '' )
                    $paths[$byAttribute[$table][$key]][$path] = true;
            }
        }

        $files = array();
        foreach ( $paths as $objectID => $list )
        {
            $size = 0;
            $count = 0;
            foreach ( array_keys( $list ) as $path )
            {
                $handler = eZClusterFileHandler::instance( $path );
                if ( !$handler->exists() )
                    continue;
                $size += (int)$handler->size();
                $count++;
            }
            $files[$objectID] = array( 'size' => $count ? $size : null, 'count' => $count );
        }
        return $files;
    }

    /**
     * The path of the original image in an image attribute's XML.
     *
     * @param string $xml
     * @return string
     */
    public static function imagePath( $xml )
    {
        if ( !preg_match( '/<ezimage\b[^>]*\surl="([^"]*)"/', (string)$xml, $match ) )
            return '';
        $path = html_entity_decode( $match[1], ENT_QUOTES | ENT_XML1, 'UTF-8' );
        // a path inside the installation only
        if ( $path === '' || strpos( $path, '..' ) !== false || $path[0] === '/' || strpos( $path, '://' ) !== false )
            return '';
        return $path;
    }
}

?>
