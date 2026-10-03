<?php
/**
 * Node services: ezjscore/call/expnode::<method>[::arg...]
 *
 * Reads and writes of the content tree: fetch by id, remote id and path, children and subtrees (paged, sorted,
 * filtered), counts, ancestors, siblings, data maps, rights, and create, move, copy, hide, swap, remove, sort and
 * priority. Large writes (move, copy, hide, reveal, remove of subtrees) take the POST field mode=job|now|auto and
 * run as content jobs when asked or large.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expNodeServices extends expContentServiceBase
{
    public static $services = array(
        'get' => array( 'summary' => 'A node with its object', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => 'node' ),
        'getMany' => array( 'summary' => 'Several nodes by id (unreadable and missing ones are left out)', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'node_ids' => 'list' ), 'returns' => 'nodes' ),
        'getByRemoteId' => array( 'summary' => 'A node by its remote id', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'remote_id' => 'string' ), 'returns' => 'node' ),
        'getByPath' => array( 'summary' => 'A node by its URL alias, e.g. Media/Images', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'path' => 'string' ), 'returns' => 'node' ),
        'getByObject' => array( 'summary' => 'The main node of an object', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => 'node' ),
        'exists' => array( 'summary' => 'Whether a node exists and is readable', 'access' => 'user', 'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => '{exists}' ),
        'children' => array( 'summary' => 'Children of a node, paged, sorted and filtered', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'node_id' => 'int', 'sort' => 'string', 'order' => 'string', 'limit' => 'int', 'offset' => 'int', 'filter' => 'json' ), 'returns' => 'page of nodes' ),
        'childrenCount' => array( 'summary' => 'Number of children', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'node_id' => 'int', 'filter' => 'json' ), 'returns' => '{count}' ),
        'childrenNames' => array( 'summary' => 'Light list of children: node id, object id, class, name', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'node_id' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page' ),
        'subtree' => array( 'summary' => 'Descendants of a node, paged, sorted and filtered (filter.depth limits the depth)', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'node_id' => 'int', 'sort' => 'string', 'order' => 'string', 'limit' => 'int', 'offset' => 'int', 'filter' => 'json' ), 'returns' => 'page of nodes' ),
        'subtreeCount' => array( 'summary' => 'Number of descendants', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'node_id' => 'int', 'filter' => 'json' ), 'returns' => '{count}' ),
        'byClass' => array( 'summary' => 'Descendants of one or more classes', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'node_id' => 'int', 'classes' => 'list', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page of nodes' ),
        'countByClass' => array( 'summary' => 'Facet: number of descendants per class (needs unrestricted read)', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'node_id' => 'int', 'depth' => 'int' ), 'returns' => 'class => count' ),
        'latest' => array( 'summary' => 'Most recently published descendants', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'node_id' => 'int', 'limit' => 'int', 'classes' => 'list' ), 'returns' => 'nodes' ),
        'modifiedSince' => array( 'summary' => 'Descendants published since a time (timestamp or date)', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'node_id' => 'int', 'since' => 'string', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page of nodes' ),
        'find' => array( 'summary' => 'Descendants whose name starts with a text', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'node_id' => 'int', 'name' => 'string', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page of nodes' ),
        'hidden' => array( 'summary' => 'Hidden nodes below a node', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'node_id' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page of nodes' ),
        'tree' => array( 'summary' => 'Nested children down to a depth, limited per level', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'node_id' => 'int', 'depth' => 'int', 'per_level' => 'int' ), 'returns' => 'nested nodes' ),
        'path' => array( 'summary' => 'The ancestors of a node from the root, with the node itself', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => 'nodes' ),
        'breadcrumb' => array( 'summary' => 'Light ancestors list: node id, name, url alias', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => 'list' ),
        'parent' => array( 'summary' => 'The parent node', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => 'node' ),
        'siblings' => array( 'summary' => 'Other children of the parent, paged', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'node_id' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page of nodes' ),
        'neighbours' => array( 'summary' => 'Previous and next sibling in the parent sort order', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => '{previous, next}' ),
        'dataMap' => array( 'summary' => 'The attributes of the node object with their values', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'node_id' => 'int', 'language' => 'string' ), 'returns' => 'identifier => attribute' ),
        'attribute' => array( 'summary' => 'One attribute of the node object', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'node_id' => 'int', 'identifier' => 'string', 'language' => 'string' ), 'returns' => 'attribute' ),
        'names' => array( 'summary' => 'The node name in every language', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => 'language => name' ),
        'url' => array( 'summary' => 'URL alias and system URL of a node', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => '{url_alias, system_url}' ),
        'sortInfo' => array( 'summary' => 'Sort field and order of the children, and the names allowed', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => '{field, order, fields}' ),
        'visibility' => array( 'summary' => 'Hidden and invisible flags with the nearest hidden ancestor', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => '{is_hidden, is_invisible, hidden_by}' ),
        'rights' => array( 'summary' => 'What the current user can do with the node', 'access' => 'user', 'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => 'right => bool' ),
        'creatableClasses' => array( 'summary' => 'Classes the current user can create below the node', 'access' => array( 'content', 'create' ), 'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => 'classes' ),
        'locations' => array( 'summary' => 'All nodes of the same object', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => 'nodes' ),
        'pendingDrafts' => array( 'summary' => 'Drafts waiting to be published below a node', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'node_id' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page' ),
        'create' => array( 'summary' => 'Creates and publishes an object below a node. POST: class, attributes (json identifier => string), language, remote_id', 'access' => array( 'content', 'create' ), 'write' => true, 'args' => array( 'parent_node_id' => 'int' ), 'returns' => 'node' ),
        'update' => array( 'summary' => 'Changes attributes of the node object and publishes a new version. POST: attributes (json), language', 'access' => array( 'content', 'edit' ), 'write' => true, 'args' => array( 'node_id' => 'int' ), 'returns' => 'node' ),
        'rename' => array( 'summary' => 'Renames the object of a node. POST: name', 'access' => array( 'content', 'edit' ), 'write' => true, 'args' => array( 'node_id' => 'int' ), 'returns' => 'node' ),
        'move' => array( 'summary' => 'Moves a node and its subtree. POST: mode', 'access' => array( 'content', 'move' ), 'write' => true, 'args' => array( 'node_id' => 'int', 'new_parent_node_id' => 'int' ), 'returns' => 'node or job' ),
        'copy' => array( 'summary' => 'Copies one node (object) below a node', 'access' => array( 'content', 'create' ), 'write' => true, 'args' => array( 'node_id' => 'int', 'new_parent_node_id' => 'int' ), 'returns' => 'new node' ),
        'copySubtree' => array( 'summary' => 'Copies a node with all its descendants. POST: mode, all_versions, keep_creator, keep_time', 'access' => array( 'content', 'create' ), 'write' => true, 'args' => array( 'node_id' => 'int', 'new_parent_node_id' => 'int' ), 'returns' => 'node or job' ),
        'hide' => array( 'summary' => 'Hides a node and its subtree. POST: mode', 'access' => array( 'content', 'hide' ), 'write' => true, 'args' => array( 'node_id' => 'int' ), 'returns' => 'node or job' ),
        'reveal' => array( 'summary' => 'Reveals a hidden node and its subtree. POST: mode', 'access' => array( 'content', 'hide' ), 'write' => true, 'args' => array( 'node_id' => 'int' ), 'returns' => 'node or job' ),
        'toggleHide' => array( 'summary' => 'Hides a visible node, reveals a hidden one', 'access' => array( 'content', 'hide' ), 'write' => true, 'args' => array( 'node_id' => 'int' ), 'returns' => 'node' ),
        'swap' => array( 'summary' => 'Swaps two nodes (their objects change places)', 'access' => array( 'content', 'edit' ), 'write' => true, 'args' => array( 'node_id' => 'int', 'other_node_id' => 'int' ), 'returns' => 'both nodes' ),
        'remove' => array( 'summary' => 'Removes a node and its subtree. POST: move_to_trash (default 1), mode', 'access' => array( 'content', 'remove' ), 'write' => true, 'args' => array( 'node_id' => 'int' ), 'returns' => '{removed} or job' ),
        'removeMany' => array( 'summary' => 'Removes several nodes. POST: node_ids, move_to_trash, mode', 'access' => array( 'content', 'remove' ), 'write' => true, 'args' => array(), 'returns' => '{removed} or job' ),
        'setSort' => array( 'summary' => 'Sets how the children are sorted', 'access' => array( 'content', 'edit' ), 'write' => true, 'args' => array( 'node_id' => 'int', 'field' => 'string', 'order' => 'string' ), 'returns' => 'node' ),
        'setPriority' => array( 'summary' => 'Sets the priority of a node', 'access' => array( 'content', 'edit' ), 'write' => true, 'args' => array( 'node_id' => 'int', 'priority' => 'int' ), 'returns' => 'node' ),
        'setPriorities' => array( 'summary' => 'Sets the priorities of children. POST: priorities (json node id => priority)', 'access' => array( 'content', 'edit' ), 'write' => true, 'args' => array( 'parent_node_id' => 'int' ), 'returns' => '{updated}' ),
        'setRemoteId' => array( 'summary' => 'Sets the remote id of a node', 'access' => array( 'content', 'edit' ), 'write' => true, 'args' => array( 'node_id' => 'int', 'remote_id' => 'string' ), 'returns' => 'node' ),
    );

    // ------------------------------------------------------------------ reads

    public static function get( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ) );
        return self::ok( self::exportNode( $node, true ) );
    }

    public static function getMany( $args )
    {
        static::guard( __FUNCTION__ );
        $ids = array_slice( array_map( 'intval', self::arg( $args, 0, 'list' ) ), 0, 100 );
        $out = array();
        foreach ( $ids as $id )
        {
            $node = eZContentObjectTreeNode::fetch( $id );
            if ( $node instanceof eZContentObjectTreeNode && $node->canRead() )
                $out[] = self::exportNode( $node );
        }
        return self::ok( $out, array( 'requested' => count( $ids ) ) );
    }

    public static function getByRemoteId( $args )
    {
        static::guard( __FUNCTION__ );
        $node = eZContentObjectTreeNode::fetchByRemoteID( self::arg( $args, 0, 'string' ) );
        if ( !$node )
            throw new expServiceException( 'No node with that remote id', 404 );
        return self::ok( self::exportNode( self::node( $node->attribute( 'node_id' ) ), true ) );
    }

    public static function getByPath( $args )
    {
        static::guard( __FUNCTION__ );
        $path = trim( self::arg( $args, 0, 'string' ), '/' );
        $node = $path === '' ? null : eZContentObjectTreeNode::fetchByURLPath( $path );
        if ( !$node )
            throw new expServiceException( "No node at '$path'", 404 );
        return self::ok( self::exportNode( self::node( $node->attribute( 'node_id' ) ), true ) );
    }

    public static function getByObject( $args )
    {
        static::guard( __FUNCTION__ );
        $object = self::object( self::arg( $args, 0, 'int' ) );
        $node = $object->attribute( 'main_node' );
        if ( !$node )
            throw new expServiceException( 'The object has no node', 404 );
        return self::ok( self::exportNode( self::node( $node->attribute( 'node_id' ) ), true ) );
    }

    public static function exists( $args )
    {
        static::guard( __FUNCTION__ );
        $node = eZContentObjectTreeNode::fetch( self::arg( $args, 0, 'int' ) );
        return self::ok( array( 'exists' => $node instanceof eZContentObjectTreeNode && $node->canRead() ) );
    }

    public static function children( $args )
    {
        static::guard( __FUNCTION__ );
        return self::nodeList( self::node( self::arg( $args, 0, 'int' ) ), $args, 1, 1 );
    }

    public static function childrenCount( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ) );
        $params = array_merge( array( 'Depth' => 1, 'DepthOperator' => 'eq' ), self::filterParams( self::arg( $args, 1, 'json', array() ) ) );
        return self::ok( array( 'count' => (int)$node->subTreeCount( $params ) ) );
    }

    public static function childrenNames( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ) );
        list( $limit, $offset ) = self::paging( $args, 1, 2 );
        $params = array( 'Depth' => 1, 'DepthOperator' => 'eq', 'Limit' => $limit, 'Offset' => $offset, 'SortBy' => $node->sortArray() );
        $total = $node->subTreeCount( array( 'Depth' => 1, 'DepthOperator' => 'eq' ) );
        $items = array();
        foreach ( (array)$node->subTree( $params ) as $child )
            $items[] = array( 'node_id' => (int)$child->attribute( 'node_id' ), 'object_id' => (int)$child->attribute( 'contentobject_id' ),
                              'class' => $child->attribute( 'class_identifier' ), 'name' => $child->attribute( 'name' ) );
        return self::page( $items, $total, $offset, $limit );
    }

    public static function subtree( $args )
    {
        static::guard( __FUNCTION__ );
        return self::nodeList( self::node( self::arg( $args, 0, 'int' ) ), $args, 1, 0 );
    }

    public static function subtreeCount( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ) );
        return self::ok( array( 'count' => (int)$node->subTreeCount( self::filterParams( self::arg( $args, 1, 'json', array() ) ) ) ) );
    }

    public static function byClass( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ) );
        $classes = self::arg( $args, 1, 'list' );
        $filter = array( 'class' => $classes );
        return self::nodeList( $node, array( $args[0], 'path', 'asc', isset( $args[2] ) ? $args[2] : null, isset( $args[3] ) ? $args[3] : null, $filter ), 1, 0 );
    }

    public static function countByClass( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ) );
        $depth = self::arg( $args, 1, 'int', 0 );
        $access = eZUser::currentUser()->hasAccessTo( 'content', 'read' );
        if ( $access['accessWord'] !== 'yes' )
            throw new expServiceException( 'Counting by class needs unrestricted read access', 403 );
        $db = eZDB::instance();
        $path = $db->escapeString( $node->attribute( 'path_string' ) );
        $cond = $depth > 0 ? ' AND t.depth <= ' . ( (int)$node->attribute( 'depth' ) + $depth ) : '';
        $rows = $db->arrayQuery( "SELECT c.identifier AS identifier, COUNT(*) AS cnt FROM ezcontentobject_tree t, ezcontentobject o, ezcontentclass c
                                   WHERE t.path_string LIKE '$path%' AND t.node_id <> " . (int)$node->attribute( 'node_id' ) . "
                                     AND o.id = t.contentobject_id AND c.id = o.contentclass_id AND c.version = 0 $cond
                                   GROUP BY c.identifier ORDER BY cnt DESC" );
        $out = array();
        foreach ( $rows as $r )
            $out[$r['identifier']] = (int)$r['cnt'];
        return self::ok( $out, array( 'node_id' => (int)$node->attribute( 'node_id' ) ) );
    }

    public static function latest( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ) );
        $limit = min( 100, max( 1, self::arg( $args, 1, 'int', 10 ) ) );
        $params = array( 'Limit' => $limit, 'SortBy' => array( array( 'published', false ) ), 'AsObject' => true );
        $classes = self::arg( $args, 2, 'list', array() );
        if ( $classes )
        {
            $params['ClassFilterType'] = 'include';
            $params['ClassFilterArray'] = $classes;
        }
        return self::ok( self::exportNodes( (array)$node->subTree( $params ) ) );
    }

    public static function modifiedSince( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ) );
        $since = self::arg( $args, 1, 'string' );
        $ts = ctype_digit( $since ) ? (int)$since : (int)strtotime( $since );
        if ( $ts <= 0 )
            throw new expServiceException( 'since is a timestamp or a date', 400 );
        $filter = array( 'from' => $ts );
        return self::nodeList( $node, array( $args[0], 'published', 'desc', isset( $args[2] ) ? $args[2] : null, isset( $args[3] ) ? $args[3] : null, $filter ), 1, 0 );
    }

    public static function find( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ) );
        $filter = array( 'name' => self::arg( $args, 1, 'string' ) );
        return self::nodeList( $node, array( $args[0], 'name', 'asc', isset( $args[2] ) ? $args[2] : null, isset( $args[3] ) ? $args[3] : null, $filter ), 1, 0 );
    }

    public static function hidden( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ) );
        list( $limit, $offset ) = self::paging( $args, 1, 2 );
        $db = eZDB::instance();
        $path = $db->escapeString( $node->attribute( 'path_string' ) );
        $rows = $db->arrayQuery( "SELECT node_id FROM ezcontentobject_tree WHERE path_string LIKE '$path%' AND is_hidden = 1 ORDER BY node_id" );
        $items = array();
        foreach ( $rows as $r )
        {
            $n = eZContentObjectTreeNode::fetch( (int)$r['node_id'] );
            if ( $n && $n->canRead() )
                $items[] = self::exportNode( $n );
        }
        return self::pageOf( $items, $args, 1, 2 );
    }

    public static function tree( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ) );
        $depth = min( 5, max( 1, self::arg( $args, 1, 'int', 2 ) ) );
        $per = min( 100, max( 1, self::arg( $args, 2, 'int', 25 ) ) );
        return self::ok( self::branch( $node, $depth, $per ) );
    }

    protected static function branch( eZContentObjectTreeNode $node, $depth, $per )
    {
        $row = array( 'node_id' => (int)$node->attribute( 'node_id' ), 'class' => $node->attribute( 'class_identifier' ),
                      'name' => $node->attribute( 'name' ), 'children_count' => (int)$node->childrenCount(), 'children' => array() );
        if ( $depth > 0 )
            foreach ( (array)$node->subTree( array( 'Depth' => 1, 'DepthOperator' => 'eq', 'Limit' => $per, 'SortBy' => $node->sortArray(), 'AsObject' => true ) ) as $c )
                $row['children'][] = self::branch( $c, $depth - 1, $per );
        return $row;
    }

    public static function path( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ) );
        $list = (array)$node->fetchPath();
        $list[] = $node;
        return self::ok( self::exportNodes( $list ) );
    }

    public static function breadcrumb( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ) );
        $list = (array)$node->fetchPath();
        $list[] = $node;
        $out = array();
        foreach ( $list as $n )
            $out[] = array( 'node_id' => (int)$n->attribute( 'node_id' ), 'name' => $n->attribute( 'name' ), 'url_alias' => $n->attribute( 'url_alias' ) );
        return self::ok( $out );
    }

    public static function parent( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ) );
        $parent = $node->fetchParent();
        if ( !$parent )
            throw new expServiceException( 'The node has no parent', 404 );
        return self::ok( self::exportNode( self::node( $parent->attribute( 'node_id' ) ) ) );
    }

    public static function siblings( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ) );
        $parent = self::node( $node->attribute( 'parent_node_id' ) );
        list( $limit, $offset ) = self::paging( $args, 1, 2 );
        $params = array( 'Depth' => 1, 'DepthOperator' => 'eq', 'SortBy' => $parent->sortArray(), 'AsObject' => true );
        $all = array();
        foreach ( (array)$parent->subTree( $params + array( 'Limit' => 500 ) ) as $n )
            if ( (int)$n->attribute( 'node_id' ) !== (int)$node->attribute( 'node_id' ) )
                $all[] = self::exportNode( $n );
        return self::pageOf( $all, $args, 1, 2 );
    }

    public static function neighbours( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ) );
        $parent = self::node( $node->attribute( 'parent_node_id' ) );
        $list = (array)$parent->subTree( array( 'Depth' => 1, 'DepthOperator' => 'eq', 'SortBy' => $parent->sortArray(), 'Limit' => 1000, 'AsObject' => true ) );
        $prev = $next = null;
        foreach ( $list as $i => $n )
            if ( (int)$n->attribute( 'node_id' ) === (int)$node->attribute( 'node_id' ) )
            {
                $prev = $i > 0 ? self::exportNode( $list[$i - 1] ) : null;
                $next = isset( $list[$i + 1] ) ? self::exportNode( $list[$i + 1] ) : null;
            }
        return self::ok( array( 'previous' => $prev, 'next' => $next ) );
    }

    public static function dataMap( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ) );
        return self::ok( self::exportDataMap( $node->object(), false, self::arg( $args, 1, 'string', false ) ) );
    }

    public static function attribute( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ) );
        $identifier = self::arg( $args, 1, 'string' );
        $map = $node->object()->fetchDataMap( false, self::arg( $args, 2, 'string', false ) ?: false );
        if ( !isset( $map[$identifier] ) )
            throw new expServiceException( "The object has no attribute '$identifier'", 404 );
        return self::ok( self::exportAttribute( $map[$identifier] ) );
    }

    public static function names( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ) );
        return self::ok( (object)$node->object()->names() );
    }

    public static function url( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ) );
        return self::ok( array( 'url_alias' => $node->urlAlias(), 'system_url' => 'content/view/full/' . (int)$node->attribute( 'node_id' ) ) );
    }

    public static function sortInfo( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ) );
        return self::ok( array( 'field' => eZContentObjectTreeNode::sortFieldName( $node->attribute( 'sort_field' ) ),
                                'order' => $node->attribute( 'sort_order' ) ? 'asc' : 'desc', 'fields' => self::$sortFields ) );
    }

    public static function visibility( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ) );
        $by = null;
        foreach ( (array)$node->fetchPath() as $ancestor )
            if ( $ancestor->attribute( 'is_hidden' ) )
                $by = (int)$ancestor->attribute( 'node_id' );
        if ( $node->attribute( 'is_hidden' ) )
            $by = (int)$node->attribute( 'node_id' );
        return self::ok( array( 'is_hidden' => (bool)$node->attribute( 'is_hidden' ), 'is_invisible' => (bool)$node->attribute( 'is_invisible' ), 'hidden_by' => $by ) );
    }

    public static function rights( $args )
    {
        static::guard( __FUNCTION__ );
        $node = eZContentObjectTreeNode::fetch( self::arg( $args, 0, 'int' ) );
        if ( !$node )
            throw new expServiceException( 'Node does not exist', 404 );
        $out = array();
        foreach ( array( 'read', 'edit', 'create', 'remove', 'hide', 'moveFrom', 'swap', 'addLocation', 'removeLocation' ) as $right )
            $out[$right] = (bool)$node->{'can' . ucfirst( $right )}();
        return self::ok( $out );
    }

    public static function creatableClasses( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ), 'create' );
        $out = array();
        foreach ( (array)$node->canCreateClassList() as $c )
            $out[] = array( 'id' => (int)$c['id'], 'identifier' => eZContentClass::classIdentifierByID( (int)$c['id'] ), 'name' => isset( $c['name'] ) ? $c['name'] : null );
        return self::ok( $out );
    }

    public static function locations( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ) );
        return self::ok( self::exportNodes( (array)$node->object()->assignedNodes() ) );
    }

    public static function pendingDrafts( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ) );
        list( $limit, $offset ) = self::paging( $args, 1, 2 );
        $ids = array( (int)$node->attribute( 'node_id' ) );
        $total = (int)eZNodeAssignment::fetchChildCountByVersionStatus( $ids, eZContentObjectVersion::STATUS_PENDING );
        $rows = $total ? eZNodeAssignment::fetchChildListByVersionStatus( $ids, eZContentObjectVersion::STATUS_PENDING, $limit, $offset ) : array();
        $items = array();
        foreach ( (array)$rows as $r )
            $items[] = is_object( $r ) ? array( 'object_id' => (int)$r->attribute( 'contentobject_id' ), 'version' => (int)$r->attribute( 'contentobject_version' ) )
                                       : array( 'object_id' => (int)$r['contentobject_id'], 'version' => (int)$r['contentobject_version'] );
        return self::page( $items, $total, $offset, $limit );
    }

    // ------------------------------------------------------------------ writes

    public static function create( $args )
    {
        static::guard( __FUNCTION__ );
        $parent = self::node( self::arg( $args, 0, 'int' ), 'create' );
        $class = self::contentClass( self::post( 'class', 'string' ) );
        $attrs = self::post( 'attributes', 'json', array() );
        if ( !is_array( $attrs ) )
            throw new expServiceException( 'attributes must be a JSON object', 400 );
        $allowed = false;
        foreach ( (array)$parent->canCreateClassList() as $c )
            if ( (int)$c['id'] === (int)$class->attribute( 'id' ) )
                $allowed = true;
        if ( !$allowed )
            throw new expServiceException( 'You cannot create ' . $class->attribute( 'identifier' ) . ' objects here', 403 );
        self::checkInput( $class, $attrs );
        self::checkRequired( $class, $attrs );
        $params = array( 'parent_node_id' => (int)$parent->attribute( 'node_id' ), 'class_identifier' => $class->attribute( 'identifier' ),
                         'creator_id' => (int)eZUser::currentUser()->attribute( 'contentobject_id' ), 'attributes' => $attrs );
        if ( $lang = self::post( 'language', 'string', '' ) )
            $params['language'] = self::languageCode( $lang );
        if ( $remote = self::post( 'remote_id', 'string', '' ) )
        {
            if ( eZContentObject::fetchByRemoteID( $remote ) )
                throw new expServiceException( 'That remote id is in use', 409 );
            $params['remote_id'] = $remote;
        }
        $object = eZContentFunctions::createAndPublishObject( $params );
        if ( !$object instanceof eZContentObject )
            throw new expServiceException( 'The object could not be created', 422 );
        $object = eZContentObject::fetch( (int)$object->attribute( 'id' ) );
        if ( (int)$object->attribute( 'status' ) !== eZContentObject::STATUS_PUBLISHED || !$object->attribute( 'main_node' ) )
            throw new expServiceException( 'The object was created but not published (a workflow may hold it); object id ' . $object->attribute( 'id' ), 422 );
        self::audit( 'service.node.create', array( 'verb' => 'create', 'x' => array( 'object_id' => (int)$object->attribute( 'id' ), 'parent_node_id' => (int)$parent->attribute( 'node_id' ) ) ) );
        return self::ok( self::exportNode( $object->attribute( 'main_node' ), true ) );
    }

    public static function update( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ), 'edit' );
        $attrs = self::post( 'attributes', 'json' );
        $object = self::updateObject( $node->object(), (array)$attrs, self::post( 'language', 'string', false ) );
        return self::ok( self::exportNode( eZContentObjectTreeNode::fetch( $node->attribute( 'node_id' ) ), true ) );
    }

    public static function rename( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ), 'edit' );
        $name = trim( self::post( 'name', 'string' ) );
        if ( $name === '' )
            throw new expServiceException( 'The name cannot be empty', 422 );
        $node->object()->rename( $name );
        return self::ok( self::exportNode( eZContentObjectTreeNode::fetch( $node->attribute( 'node_id' ) ) ) );
    }

    public static function move( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ), 'moveFrom' );
        $target = self::node( self::arg( $args, 1, 'int' ), 'read' );
        if ( !$target->canMoveTo( $node->object()->attribute( 'contentclass_id' ) ) )
            throw new expServiceException( 'You cannot move this node here', 403 );
        if ( in_array( (int)$node->attribute( 'node_id' ), $target->pathArray() ) || (int)$node->attribute( 'node_id' ) === (int)$target->attribute( 'node_id' ) )
            throw new expServiceException( 'A node cannot be moved into itself or its own subtree', 422 );
        if ( (int)$node->attribute( 'parent_node_id' ) === (int)$target->attribute( 'node_id' ) )
            throw new expServiceException( 'The node is under that parent already', 409 );
        $nodeId = (int)$node->attribute( 'node_id' );
        $objectId = (int)$node->attribute( 'contentobject_id' );
        $targetId = (int)$target->attribute( 'node_id' );
        $result = self::runOrJob( 'move', array( 'node_id' => $nodeId, 'new_parent_node_id' => $targetId, 'object_id' => $objectId ), function () use ( $nodeId, $objectId, $targetId ) {
            self::notLocked( array( $nodeId, $targetId ) );
            self::operation( 'move', array( 'node_id' => $nodeId, 'object_id' => $objectId, 'new_parent_node_id' => $targetId ),
                             function () use ( $nodeId, $objectId, $targetId ) { return eZContentOperationCollection::moveNode( $nodeId, $objectId, $targetId ); } );
            $moved = eZContentObjectTreeNode::fetch( $nodeId );
            if ( !$moved || (int)$moved->attribute( 'parent_node_id' ) !== $targetId )
                throw new expServiceException( 'The node was not moved', 422 );
            return self::exportNode( $moved );
        } );
        return $result;
    }

    public static function copy( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ), 'read' );
        $target = self::node( self::arg( $args, 1, 'int' ), 'create' );
        if ( !$target->canCreate() || !in_array( (int)$node->object()->attribute( 'contentclass_id' ), array_map( function ( $c ) { return (int)$c['id']; }, (array)$target->canCreateClassList() ), true ) )
            throw new expServiceException( 'You cannot create objects of this class there', 403 );
        if ( in_array( (int)$node->attribute( 'node_id' ), $target->pathArray() ) || (int)$node->attribute( 'node_id' ) === (int)$target->attribute( 'node_id' ) )
            throw new expServiceException( 'A node cannot be copied into itself', 422 );
        self::notLocked( array( (int)$target->attribute( 'node_id' ) ) );
        $result = eZContentOperationCollection::copyNode( (int)$node->attribute( 'node_id' ), (int)$node->attribute( 'contentobject_id' ), (int)$target->attribute( 'node_id' ) );
        if ( !$result )
            throw new expServiceException( 'The copy failed', 422 );
        $new = self::newestChild( $target->attribute( 'node_id' ) );
        return self::ok( $new ? self::exportNode( $new ) : null );
    }

    public static function copySubtree( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ), 'read' );
        $target = self::node( self::arg( $args, 1, 'int' ), 'create' );
        if ( in_array( (int)$node->attribute( 'node_id' ), $target->pathArray() ) || (int)$node->attribute( 'node_id' ) === (int)$target->attribute( 'node_id' ) )
            throw new expServiceException( 'A node cannot be copied into itself or its own subtree', 422 );
        $ini = eZINI::instance( 'content.ini' );
        $p = array( 'source_node_id' => (int)$node->attribute( 'node_id' ), 'destination_node_id' => (int)$target->attribute( 'node_id' ),
                    'all_versions' => self::post( 'all_versions', 'bool', $ini->variable( 'CopySettings', 'CopyVersions' ) === 'enabled' ),
                    'keep_creator' => self::post( 'keep_creator', 'bool', false ), 'keep_time' => self::post( 'keep_time', 'bool', false ) );
        $srcId = $p['source_node_id'];
        $dstId = $p['destination_node_id'];
        return self::runOrJob( 'copy', $p, function () use ( $p, $srcId, $dstId ) {
            self::notLocked( array( $dstId ) );
            $notifications = array();
            eZContentObjectTreeNodeOperations::copySubtree( $srcId, $dstId, $notifications, $p['all_versions'], $p['keep_creator'], $p['keep_time'] );
            if ( empty( $notifications['Result'] ) )
                throw new expServiceException( 'The copy failed: ' . implode( '; ', isset( $notifications['Errors'] ) ? $notifications['Errors'] : array() ), 422 );
            $new = self::newestChild( $dstId );
            return array( 'new_node' => $new ? self::exportNode( $new ) : null );
        } );
    }

    public static function hide( $args )
    {
        return self::changeVisibility( $args, true, __FUNCTION__ );
    }

    public static function reveal( $args )
    {
        return self::changeVisibility( $args, false, __FUNCTION__ );
    }

    protected static function changeVisibility( $args, $hide, $method )
    {
        static::guard( $method );
        $node = self::node( self::arg( $args, 0, 'int' ), 'hide' );
        if ( (bool)$node->attribute( 'is_hidden' ) === $hide )
            throw new expServiceException( $hide ? 'The node is hidden already' : 'The node is not hidden', 409 );
        $nodeId = (int)$node->attribute( 'node_id' );
        return self::runOrJob( $hide ? 'hide' : 'reveal', array( 'node_id' => $nodeId ), function () use ( $nodeId ) {
            self::notLocked( array( $nodeId ) );
            self::operation( 'hide', array( 'node_id' => $nodeId ), function () use ( $nodeId ) { return eZContentOperationCollection::changeHideStatus( $nodeId ); } );
            return self::exportNode( eZContentObjectTreeNode::fetch( $nodeId ) );
        } );
    }

    public static function toggleHide( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ), 'hide' );
        $id = (int)$node->attribute( 'node_id' );
        self::operation( 'hide', array( 'node_id' => $id ), function () use ( $id ) { return eZContentOperationCollection::changeHideStatus( $id ); } );
        return self::ok( self::exportNode( eZContentObjectTreeNode::fetch( $id ) ) );
    }

    public static function swap( $args )
    {
        static::guard( __FUNCTION__ );
        $a = self::node( self::arg( $args, 0, 'int' ), 'swap' );
        $b = self::node( self::arg( $args, 1, 'int' ), 'swap' );
        if ( (int)$a->attribute( 'node_id' ) === (int)$b->attribute( 'node_id' ) )
            throw new expServiceException( 'Cannot swap a node with itself', 422 );
        if ( in_array( (int)$a->attribute( 'node_id' ), $b->pathArray() ) || in_array( (int)$b->attribute( 'node_id' ), $a->pathArray() ) )
            throw new expServiceException( 'Cannot swap a node with one of its own ancestors or descendants', 422 );
        $ia = (int)$a->attribute( 'node_id' );
        $ib = (int)$b->attribute( 'node_id' );
        self::notLocked( array( $ia, $ib ) );
        self::operation( 'swap', array( 'node_id' => $ia, 'selected_node_id' => $ib, 'node_id_list' => array( $ia, $ib ) ),
                         function () use ( $ia, $ib ) { return eZContentOperationCollection::swapNode( $ia, $ib, array( $ia, $ib ) ); } );
        return self::ok( array( self::exportNode( eZContentObjectTreeNode::fetch( $ia ) ), self::exportNode( eZContentObjectTreeNode::fetch( $ib ) ) ) );
    }

    public static function remove( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ), 'remove' );
        return self::removeNodes( array( $node ), self::post( 'move_to_trash', 'bool', true ) );
    }

    public static function removeMany( $args )
    {
        static::guard( __FUNCTION__ );
        $ids = array_slice( array_unique( array_map( 'intval', self::post( 'node_ids', 'list' ) ) ), 0, 100 );
        $nodes = array();
        foreach ( $ids as $id )
            $nodes[] = self::node( $id, 'remove' );
        return self::removeNodes( $nodes, self::post( 'move_to_trash', 'bool', true ) );
    }

    /** Removal shared by remove and removeMany: the kernel's own checks (users, last locations), trash or delete, or a job. */
    public static function removeNodes( array $nodes, $toTrash )
    {
        $ids = array();
        foreach ( $nodes as $node )
        {
            if ( in_array( (int)$node->attribute( 'node_id' ), array( (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'RootNode' ),
                                                                       (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'MediaRootNode' ),
                                                                       (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'UserRootNode' ), 1 ), true ) )
                throw new expServiceException( 'The root nodes cannot be removed', 403 );
            $ids[] = (int)$node->attribute( 'node_id' );
        }
        return self::runOrJob( 'remove', array( 'node_ids' => $ids, 'move_to_trash' => (bool)$toTrash ), function () use ( $ids, $toTrash ) {
            self::notLocked( $ids );
            eZContentObjectTreeNode::removeSubtrees( $ids, (bool)$toTrash );
            foreach ( $ids as $id )
                if ( eZContentObjectTreeNode::fetch( $id, false, false ) )
                    throw new expServiceException( "The kernel did not remove node $id (not allowed for this node)", 422 );
            return array( 'removed' => $ids, 'trashed' => (bool)$toTrash );
        } );
    }

    public static function setSort( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ), 'edit' );
        $field = strtolower( self::arg( $args, 1, 'string' ) );
        if ( !in_array( $field, self::$sortFields, true ) )
            throw new expServiceException( 'Unknown sort field, use one of ' . implode( ', ', self::$sortFields ), 400 );
        $order = strtolower( self::arg( $args, 2, 'string', 'asc' ) );
        if ( !in_array( $order, array( 'asc', 'desc' ), true ) )
            throw new expServiceException( 'order is asc or desc', 400 );
        $id = (int)$node->attribute( 'node_id' );
        $fieldId = (int)eZContentObjectTreeNode::sortFieldID( $field );
        $dir = $order === 'asc' ? 1 : 0;
        self::operation( 'sort', array( 'node_id' => $id, 'sorting_field' => $fieldId, 'sorting_order' => $dir ),
                         function () use ( $id, $fieldId, $dir ) { return eZContentOperationCollection::changeSortOrder( $id, $fieldId, $dir ); } );
        return self::ok( self::exportNode( eZContentObjectTreeNode::fetch( $id ) ) );
    }

    public static function setPriority( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ), 'edit' );
        $priority = self::arg( $args, 1, 'int' );
        $parent = (int)$node->attribute( 'parent_node_id' );
        $id = (int)$node->attribute( 'node_id' );
        self::operation( 'updatepriority', array( 'node_id' => $parent, 'priority' => array( $priority ), 'priority_id' => array( $id ) ),
                         function () use ( $parent, $priority, $id ) { return eZContentOperationCollection::updatePriority( $parent, array( $priority ), array( $id ) ); } );
        return self::ok( self::exportNode( eZContentObjectTreeNode::fetch( $id ) ) );
    }

    public static function setPriorities( $args )
    {
        static::guard( __FUNCTION__ );
        $parent = self::node( self::arg( $args, 0, 'int' ), 'edit' );
        $map = self::post( 'priorities', 'json' );
        if ( !is_array( $map ) || !$map )
            throw new expServiceException( 'priorities is a JSON object node id => priority', 400 );
        $ids = $prios = array();
        foreach ( $map as $id => $prio )
        {
            $child = eZContentObjectTreeNode::fetch( (int)$id );
            if ( !$child || (int)$child->attribute( 'parent_node_id' ) !== (int)$parent->attribute( 'node_id' ) )
                throw new expServiceException( "Node $id is not a child of the parent", 422 );
            $ids[] = (int)$id;
            $prios[] = (int)$prio;
        }
        $pid = (int)$parent->attribute( 'node_id' );
        self::operation( 'updatepriority', array( 'node_id' => $pid, 'priority' => $prios, 'priority_id' => $ids ),
                         function () use ( $pid, $prios, $ids ) { return eZContentOperationCollection::updatePriority( $pid, $prios, $ids ); } );
        return self::ok( array( 'updated' => count( $ids ) ) );
    }

    public static function setRemoteId( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ), 'edit' );
        $remote = trim( self::arg( $args, 1, 'string' ) );
        if ( !preg_match( '/^[A-Za-z0-9_.:-]{1,100}$/', $remote ) )
            throw new expServiceException( 'The remote id may have letters, digits and _ . : - (up to 100)', 422 );
        $other = eZContentObjectTreeNode::fetchByRemoteID( $remote );
        if ( $other && (int)$other->attribute( 'node_id' ) !== (int)$node->attribute( 'node_id' ) )
            throw new expServiceException( 'That remote id is in use', 409 );
        $node->setAttribute( 'remote_id', $remote );
        $node->store();
        return self::ok( self::exportNode( eZContentObjectTreeNode::fetch( $node->attribute( 'node_id' ) ) ) );
    }
}
