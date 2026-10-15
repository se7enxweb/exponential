<?php
/**
 * File containing the expContentCheckUnreferenced class.
 *
 * The assets below a node that nothing uses (doc/features/6.0/content-checks.md, "Unreferenced objects"): published
 * objects of an asset class ([UnreferencedSettings] AssetClassList[]: images, files, media) whose main location is
 * below the node, that the current user may read, and that nothing uses:
 *   - no relation row from a counting version of another object (expContentCheck, "what counts as a reference"),
 *     checked in SQL;
 *   - nothing in the reference index (expContentCheckReferences: fields, links and embeds by id or path, layouts,
 *     settings, templates, roles, shop, newsletter and the other configured tables);
 *   - every location's parent only stores it (StorageParentClassList[], a folder): below a gallery, an article or
 *     any other content the parent's template shows it;
 *   - no dynamic collection (a layout's or a newsletter article pool's) lists it.
 * A page, a folder, a user or a settings object is never listed: it is reached through the tree. The candidates
 * come from one SQL query in the chosen order (CandidateLimit), are judged in PHP, and the page is a slice of what
 * is left; the sizes, locations and whether the user may remove them are then read for that page only.
 *
 * What the user may not read is left out everywhere: from the list, the count, the pages, the locations and the
 * sub-items shown. Whether a readable object is unreferenced is decided against every reference, also from objects
 * the user may not read, and an object with sub-items the user may not read is not offered for the trash: otherwise
 * content in use would go to the trash. That a readable object is missing from the list is therefore a weak hint
 * that something refers to it; nothing about that something (number, name, id, class) is shown anywhere, and there
 * is no view that says why an object is not listed.
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
     * The classes the list covers: assets (images, files, media) that only matter when something uses them. A
     * page, a folder, a user or a setting object is reached through the tree, by navigation, menus and searches,
     * whether or not anything links to it, so it is never "unreferenced".
     *
     * @return string[] class identifiers
     */
    public static function assetClasses()
    {
        $list = expContentCheck::setting( 'UnreferencedSettings', 'AssetClassList', array( 'image', 'file' ) );
        $clean = array();
        foreach ( is_array( $list ) ? $list : array() as $identifier )
        {
            if ( is_string( $identifier ) && preg_match( '/^[a-z0-9_]{1,50}$/i', $identifier ) )
                $clean[] = $identifier;
        }
        return array_values( array_unique( $clean ) );
    }

    /**
     * The classes of a parent that only stores its children (a folder of images): an asset below any other parent
     * (a gallery, an article, an event) is shown by that parent's template and counts as used.
     *
     * @return string[]
     */
    public static function storageParentClasses()
    {
        $list = expContentCheck::setting( 'UnreferencedSettings', 'StorageParentClassList', array( 'folder' ) );
        return array_values( array_filter( is_array( $list ) ? $list : array(), function ( $identifier ) {
            return is_string( $identifier ) && preg_match( '/^[a-z0-9_]{1,50}$/i', $identifier );
        } ) );
    }

    /**
     * The FROM and WHERE of the candidates (no database access): published assets with their main location below
     * the node and no relation row from a counting version. The reference index decides the rest (fetch()).
     *
     * @param array $params from parameters()
     * @param string $subtreePattern from expContentCheck::subtreePattern()
     * @param array $permission array( 'from' => joins starting with a comma or empty, 'where' => conditions starting with AND or empty )
     * @param string[]|null $assetClasses null: assetClasses()
     * @return array array( 'from' => string, 'where' => string )
     */
    public static function conditions( array $params, $subtreePattern, array $permission, $assetClasses = null )
    {
        if ( !preg_match( '#^/([0-9]+/)+%$#', (string)$subtreePattern ) )
            throw new InvalidArgumentException( 'not a subtree pattern' );
        $assetClasses = $assetClasses === null ? self::assetClasses() : (array)$assetClasses;
        $quoted = array();
        foreach ( $assetClasses as $identifier )
        {
            if ( is_string( $identifier ) && preg_match( '/^[a-z0-9_]{1,50}$/i', $identifier ) )
                $quoted[] = "'" . $identifier . "'";
        }
        $where = array( 'ezcontentobject_tree.contentobject_id = ezcontentobject.id',
                        'ezcontentobject_tree.node_id = ezcontentobject_tree.main_node_id',
                        "ezcontentobject_tree.path_string LIKE '" . $subtreePattern . "'",
                        'ezcontentobject_tree.node_id <> ' . (int)$params['node_id'],
                        'ezcontentobject.status = ' . eZContentObject::STATUS_PUBLISHED,
                        'ezcontentclass.id = ezcontentobject.contentclass_id',
                        'ezcontentclass.version = ' . eZContentClass::VERSION_STATUS_DEFINED,
                        // no asset class configured: nothing is listed
                        'ezcontentclass.identifier IN ( ' . ( $quoted ? implode( ', ', $quoted ) : "''" ) . ' )' );
        if ( $params['class_ids'] )
            $where[] = 'ezcontentobject.contentclass_id IN ( ' . expContentCheck::inList( $params['class_ids'] ) . ' )';
        if ( !$params['containers'] )
            $where[] = 'NOT EXISTS ( SELECT 1 FROM ezcontentobject_tree cc_child WHERE cc_child.parent_node_id = ezcontentobject_tree.node_id )';
        $where[] = 'NOT ' . expContentCheck::referenceExistsSQL( 'ezcontentobject.id' );
        return array( 'from' => 'ezcontentobject, ezcontentobject_tree, ezcontentclass' . ( isset( $permission['from'] ) ? (string)$permission['from'] : '' ),
                      'where' => implode( ' AND ', $where ) . ( isset( $permission['where'] ) ? ' ' . (string)$permission['where'] : '' ) );
    }

    /**
     * The count and the list query of the candidates (no database access).
     *
     * @param array $params from parameters()
     * @param string $subtreePattern
     * @param array $permission
     * @param string[]|null $assetClasses
     * @return array array( 'count' => SQL, 'list' => SQL without LIMIT )
     */
    public static function queries( array $params, $subtreePattern, array $permission, $assetClasses = null )
    {
        $c = self::conditions( $params, $subtreePattern, $permission, $assetClasses );
        $column = self::SORTS[isset( self::SORTS[$params['sort']] ) ? $params['sort'] : 'name'];
        $direction = $params['order'] === 'desc' ? 'DESC' : 'ASC';
        $order = $column . ' ' . $direction . ( $column !== 'ezcontentobject.id' ? ', ezcontentobject.id ' . $direction : '' );
        return array( 'count' => 'SELECT COUNT(*) AS row_count FROM ' . $c['from'] . ' WHERE ' . $c['where'],
                      'list' => 'SELECT ezcontentobject.id AS object_id, ezcontentobject.name AS object_name,' .
                                ' ezcontentobject.contentclass_id AS class_id, ezcontentclass.identifier AS class_identifier,' .
                                ' ezcontentobject.modified AS modified, ezcontentobject.published AS published,' .
                                ' ezcontentobject.current_version AS current_version, ezcontentobject.remote_id AS remote_id,' .
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
     * One page of the list. The candidates come from SQL in the chosen order; those the reference index or the
     * parent rule finds used are left out; the count is of what is left.
     *
     * @param array $params from parameters()
     * @return array array( 'node' => start node or null, 'count' => int (only objects the user may read),
     *               'rows' => array of rows (describe()), 'index' => built, seconds, stale, failed,
     *               'without_location' => int|null (published objects without any location; null for a user
     *               whose content/read is limited), 'error' => '' | not_found | database | no_index | too_many |
     *               incomplete )
     */
    public static function fetch( array $params )
    {
        $result = array( 'node' => null, 'count' => 0, 'rows' => array(), 'index' => null, 'without_location' => null, 'error' => '' );
        if ( !expContentCheck::isSupportedDatabase() )
            return array_merge( $result, array( 'error' => 'database' ) );
        $node = expContentCheck::readableNode( $params['node_id'] );
        $pattern = $node ? expContentCheck::subtreePattern( $node->attribute( 'path_string' ) ) : null;
        if ( !$node || $pattern === null )
            return array_merge( $result, array( 'error' => 'not_found' ) );
        $result['node'] = $node;

        $index = expContentCheckReferences::index();
        if ( !$index )
            return array_merge( $result, array( 'error' => 'no_index' ) );
        $result['index'] = array( 'built' => (int)$index['built'], 'seconds' => $index['seconds'], 'stale' => !empty( $index['stale'] ),
                                  'failed' => isset( $index['failed'] ) ? $index['failed'] : array() );
        // a source that could not be read: nothing can be called unreferenced
        if ( !empty( $index['failed'] ) )
            return array_merge( $result, array( 'error' => 'incomplete' ) );

        $db = eZDB::instance();
        $limit = expContentCheck::intSetting( 'UnreferencedSettings', 'CandidateLimit', 20000, 100, 500000 );
        $permission = self::permission( $node->attribute( 'path_string' ), (int)$node->attribute( 'node_id' ) );
        try
        {
            $queries = self::queries( $params, $pattern, $permission );
            $candidates = $db->arrayQuery( $queries['list'], array( 'offset' => 0, 'limit' => $limit + 1 ) );
        }
        finally
        {
            if ( !empty( $permission['temp_tables'] ) )
                $db->dropTempTableList( $permission['temp_tables'] );
        }
        $candidates = is_array( $candidates ) ? $candidates : array();
        if ( count( $candidates ) > $limit )
            return array_merge( $result, array( 'error' => 'too_many' ) );

        $unreferenced = array();
        foreach ( self::judge( $candidates, $index ) as $i => $reason )
        {
            if ( $reason === null )
                $unreferenced[] = $candidates[$i];
        }
        $result['count'] = count( $unreferenced );
        $result['rows'] = self::describe( array_slice( $unreferenced, (int)$params['offset'], (int)$params['limit'] ) );
        $read = expContentCheck::readPermission();
        expContentCheck::dropPermission( $read );
        if ( !$read['limited'] )
            $result['without_location'] = self::withoutLocationCount();
        return $result;
    }

    /**
     * Why each candidate is used, or null when nothing uses it.
     *
     * @param array $candidates rows with object_id, remote_id, class_identifier
     * @param array $index from expContentCheckReferences::index()
     * @return array by the candidates' keys: string|null
     */
    public static function judge( array $candidates, array $index )
    {
        $objectIDs = array();
        foreach ( $candidates as $row )
            $objectIDs[] = (int)$row['object_id'];
        $locations = self::locations( $objectIDs );
        $storage = self::storageParentClasses();
        $reasons = array();
        foreach ( $candidates as $key => $row )
        {
            $objectID = (int)$row['object_id'];
            $reasons[$key] = expContentCheckReferences::reason( $index, array( 'object_id' => $objectID,
                                                                                'remote_id' => (string)$row['remote_id'],
                                                                                'class_identifier' => (string)$row['class_identifier'],
                                                                                'locations' => isset( $locations[$objectID] ) ? $locations[$objectID] : array() ),
                                                                $storage );
        }
        return $reasons;
    }

    /**
     * Every location of the objects with what judging needs: node, parent and its class, path, remote id, alias.
     * A fixed number of queries for any number of objects (chunks of 500).
     *
     * @param int[] $objectIDs
     * @return array by object id: list of array( 'node_id', 'parent_node_id', 'path_string', 'remote_id', 'path', 'parent_class' )
     */
    public static function locations( array $objectIDs )
    {
        $db = eZDB::instance();
        $locations = array();
        foreach ( array_chunk( array_values( array_unique( array_map( 'intval', $objectIDs ) ) ), 500 ) as $chunk )
        {
            $rows = $db->arrayQuery( 'SELECT t.contentobject_id AS object_id, t.node_id AS node_id, t.parent_node_id AS parent_node_id,' .
                                     ' t.path_string AS path_string, t.remote_id AS remote_id, t.path_identification_string AS path,' .
                                     ' pc.identifier AS parent_class' .
                                     ' FROM ezcontentobject_tree t' .
                                     ' LEFT JOIN ezcontentobject_tree pt ON pt.node_id = t.parent_node_id' .
                                     ' LEFT JOIN ezcontentobject po ON po.id = pt.contentobject_id' .
                                     ' LEFT JOIN ezcontentclass pc ON pc.id = po.contentclass_id AND pc.version = ' . eZContentClass::VERSION_STATUS_DEFINED .
                                     ' WHERE t.contentobject_id IN ( ' . expContentCheck::inList( $chunk ) . ' )' );
            foreach ( is_array( $rows ) ? $rows : array() as $row )
            {
                $row['parent_class'] = (string)$row['parent_class'];
                $locations[(int)$row['object_id']][] = $row;
            }
        }
        return $locations;
    }

    /**
     * The published objects that have no location at all: nothing can reach them; they are counted, not listed.
     *
     * @return int
     */
    public static function withoutLocationCount()
    {
        $rows = eZDB::instance()->arrayQuery( 'SELECT COUNT(*) AS row_count FROM ezcontentobject WHERE status = ' . eZContentObject::STATUS_PUBLISHED .
                                              ' AND NOT EXISTS ( SELECT 1 FROM ezcontentobject_tree WHERE ezcontentobject_tree.contentobject_id = ezcontentobject.id )' );
        return isset( $rows[0]['row_count'] ) ? (int)$rows[0]['row_count'] : 0;
    }

    /**
     * Of the given objects, those that nothing uses now, judged against a fresh reference index (the trash action
     * asks this again before it acts): published assets with no relation row and nothing in the index.
     *
     * @param int[] $objectIDs
     * @param bool $fresh build the reference index now
     * @return int[]
     */
    public static function unreferencedAmong( array $objectIDs, $fresh = true )
    {
        $objectIDs = expContentCheck::idList( $objectIDs );
        if ( !$objectIDs )
            return array();
        $index = expContentCheckReferences::index( $fresh );
        if ( !$index || !empty( $index['failed'] ) )
            return array();
        $quoted = array();
        foreach ( self::assetClasses() as $identifier )
            $quoted[] = "'" . $identifier . "'";
        if ( !$quoted )
            return array();
        $rows = eZDB::instance()->arrayQuery( 'SELECT ezcontentobject.id AS object_id, ezcontentobject.remote_id AS remote_id, ezcontentclass.identifier AS class_identifier' .
                                              ' FROM ezcontentobject, ezcontentclass' .
                                              ' WHERE ezcontentobject.id IN ( ' . expContentCheck::inList( $objectIDs ) . ' )' .
                                              ' AND ezcontentobject.status = ' . eZContentObject::STATUS_PUBLISHED .
                                              ' AND ezcontentclass.id = ezcontentobject.contentclass_id' .
                                              ' AND ezcontentclass.version = ' . eZContentClass::VERSION_STATUS_DEFINED .
                                              ' AND ezcontentclass.identifier IN ( ' . implode( ', ', $quoted ) . ' )' .
                                              ' AND NOT ' . expContentCheck::referenceExistsSQL( 'ezcontentobject.id' ) );
        $rows = is_array( $rows ) ? array_values( $rows ) : array();
        $ids = array();
        foreach ( self::judge( $rows, $index ) as $i => $reason )
        {
            if ( $reason === null )
                $ids[] = (int)$rows[$i]['object_id'];
        }
        return $ids;
    }

    /**
     * Why objects are used (the CLI's --why): the relation row, the reference found or the parent rule.
     *
     * @param int[] $objectIDs
     * @return array by object id: string (what uses it) or null (unused); objects that are not assets say so
     */
    public static function reasons( array $objectIDs )
    {
        $objectIDs = expContentCheck::idList( $objectIDs );
        $result = array();
        if ( !$objectIDs )
            return $result;
        $db = eZDB::instance();
        $index = expContentCheckReferences::index();
        $assets = self::assetClasses();
        $rows = $db->arrayQuery( 'SELECT ezcontentobject.id AS object_id, ezcontentobject.remote_id AS remote_id, ezcontentobject.status AS status,' .
                                 ' ezcontentclass.identifier AS class_identifier' .
                                 ' FROM ezcontentobject, ezcontentclass WHERE ezcontentobject.id IN ( ' . expContentCheck::inList( $objectIDs ) . ' )' .
                                 ' AND ezcontentclass.id = ezcontentobject.contentclass_id AND ezcontentclass.version = ' . eZContentClass::VERSION_STATUS_DEFINED );
        $rows = is_array( $rows ) ? array_values( $rows ) : array();
        $links = array();
        foreach ( (array)$db->arrayQuery( 'SELECT ezcontentobject.id AS object_id FROM ezcontentobject WHERE ezcontentobject.id IN ( ' . expContentCheck::inList( $objectIDs ) . ' )' .
                                          ' AND ' . expContentCheck::referenceExistsSQL( 'ezcontentobject.id' ) ) as $row )
            $links[(int)$row['object_id']] = true;
        $judged = $index ? self::judge( $rows, $index ) : array();
        foreach ( $rows as $i => $row )
        {
            $id = (int)$row['object_id'];
            if ( !in_array( (string)$row['class_identifier'], $assets, true ) )
                $result[$id] = 'not an asset class (' . $row['class_identifier'] . '): reached through the tree';
            else if ( (int)$row['status'] !== eZContentObject::STATUS_PUBLISHED )
                $result[$id] = 'not published';
            else if ( isset( $links[$id] ) )
                $result[$id] = 'object relation, link or embed (ezcontentobject_link)';
            else
                $result[$id] = $index ? $judged[$i] : 'no reference index';
        }
        return $result;
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

        // the locations and sub-items the page shows: only those the user may read. Whether a row may be moved to
        // the trash counts every sub-item (allChildren): a hidden one keeps the object, it is never shown as a number
        $read = expContentCheck::readPermission();
        $locations = array();
        $children = array();
        $allChildren = array();
        try
        {
            foreach ( (array)$db->arrayQuery( 'SELECT ezcontentobject_tree.contentobject_id AS object_id, COUNT(*) AS row_count' .
                                              ' FROM ezcontentobject_tree, ezcontentobject' . $read['from'] .
                                              ' WHERE ezcontentobject.id = ezcontentobject_tree.contentobject_id' .
                                              ' AND ezcontentobject_tree.contentobject_id IN ( ' . expContentCheck::inList( $objectIDs ) . ' ) ' . $read['where'] .
                                              ' GROUP BY ezcontentobject_tree.contentobject_id' ) as $row )
                $locations[(int)$row['object_id']] = (int)$row['row_count'];
            foreach ( (array)$db->arrayQuery( 'SELECT ezcontentobject_tree.parent_node_id AS parent_id, COUNT(*) AS row_count' .
                                              ' FROM ezcontentobject_tree, ezcontentobject' . $read['from'] .
                                              ' WHERE ezcontentobject.id = ezcontentobject_tree.contentobject_id' .
                                              ' AND ezcontentobject_tree.parent_node_id IN ( ' . expContentCheck::inList( $nodeIDs ) . ' ) ' . $read['where'] .
                                              ' GROUP BY ezcontentobject_tree.parent_node_id' ) as $row )
                $children[(int)$row['parent_id']] = (int)$row['row_count'];
        }
        finally
        {
            expContentCheck::dropPermission( $read );
        }
        foreach ( (array)$db->arrayQuery( 'SELECT parent_node_id, COUNT(*) AS row_count FROM ezcontentobject_tree WHERE parent_node_id IN ( ' .
                                          expContentCheck::inList( $nodeIDs ) . ' ) GROUP BY parent_node_id' ) as $row )
            $allChildren[(int)$row['parent_node_id']] = (int)$row['row_count'];

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
            $anyChild = isset( $allChildren[$nodeID] ) && $allChildren[$nodeID] > 0;
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
                                  'can_trash' => !$anyChild && !empty( $removable[$nodeID] ) );
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
