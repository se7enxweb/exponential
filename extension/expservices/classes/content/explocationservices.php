<?php
/**
 * Location services: ezjscore/call/explocation::<method>[::arg...]
 *
 * The places (nodes) an object lives at: list, main location, parents, node assignments, and writes that add or
 * remove locations and set the main location. Large changes route through content jobs (POST mode).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expLocationServices extends expContentServiceBase
{
    public static $services = array(
        'list' => array( 'summary' => 'All locations (nodes) of an object', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => 'nodes' ),
        'count' => array( 'summary' => 'Number of locations of an object', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => '{count}' ),
        'main' => array( 'summary' => 'The main location of an object', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => 'node' ),
        'parents' => array( 'summary' => 'The parent nodes of the locations of an object', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => 'nodes' ),
        'assignments' => array( 'summary' => 'The node assignments of an object (current version)', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => 'assignments' ),
        'isMain' => array( 'summary' => 'Whether a node is the main location of its object', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => '{is_main}' ),
        'canAdd' => array( 'summary' => 'Whether the current user can add the object to a node', 'access' => 'user', 'write' => false, 'args' => array( 'object_id' => 'int', 'parent_node_id' => 'int' ), 'returns' => '{can_add, reason}' ),
        'canRemove' => array( 'summary' => 'Whether a location can be removed (it is not the last one and has no children)', 'access' => 'user', 'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => '{can_remove, reason}' ),
        'candidates' => array( 'summary' => 'Container nodes below a node where an object of a class can be added', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'node_id' => 'int', 'class' => 'string', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page of nodes' ),
        'orphans' => array( 'summary' => 'Published objects that have no node (needs unrestricted read)', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page of objects' ),
        'add' => array( 'summary' => 'Adds a location of an object below a node', 'access' => array( 'content', 'manage_locations' ), 'write' => true, 'args' => array( 'object_id' => 'int', 'parent_node_id' => 'int' ), 'returns' => 'locations' ),
        'addMany' => array( 'summary' => 'Adds the objects of several nodes below one node. POST: node_ids, mode', 'access' => array( 'content', 'manage_locations' ), 'write' => true, 'args' => array( 'target_node_id' => 'int' ), 'returns' => '{added} or job' ),
        'remove' => array( 'summary' => 'Removes one location of an object (not the last one)', 'access' => array( 'content', 'manage_locations' ), 'write' => true, 'args' => array( 'node_id' => 'int' ), 'returns' => 'locations' ),
        'removeMany' => array( 'summary' => 'Removes several locations. POST: node_ids, mode', 'access' => array( 'content', 'manage_locations' ), 'write' => true, 'args' => array(), 'returns' => '{removed} or job' ),
        'setMain' => array( 'summary' => 'Makes a location the main one', 'access' => array( 'content', 'manage_locations' ), 'write' => true, 'args' => array( 'node_id' => 'int' ), 'returns' => 'locations' ),
    );

    protected static function locationsOf( eZContentObject $o )
    {
        eZContentObject::clearCache();
        $o = eZContentObject::fetch( (int)$o->attribute( 'id' ) );
        return self::exportNodes( (array)$o->assignedNodes() );
    }

    public static function list( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( self::locationsOf( self::object( self::arg( $args, 0, 'int' ) ) ) );
    }

    public static function count( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( array( 'count' => (int)self::object( self::arg( $args, 0, 'int' ) )->assignedNodeCount() ) );
    }

    public static function main( $args )
    {
        static::guard( __FUNCTION__ );
        $n = self::object( self::arg( $args, 0, 'int' ) )->attribute( 'main_node' );
        if ( !$n )
            throw new expServiceException( 'The object has no location', 404 );
        return self::ok( self::exportNode( $n ) );
    }

    public static function parents( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        $out = array();
        foreach ( (array)$o->assignedNodes() as $n )
        {
            $p = $n->fetchParent();
            if ( $p && $p->canRead() )
                $out[] = self::exportNode( $p );
        }
        return self::ok( $out );
    }

    public static function assignments( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        $out = array();
        foreach ( (array)eZNodeAssignment::fetchForObject( $o->attribute( 'id' ), $o->attribute( 'current_version' ), 0, true ) as $a )
            $out[] = array( 'id' => (int)$a->attribute( 'id' ), 'parent_node' => (int)$a->attribute( 'parent_node' ), 'is_main' => (bool)$a->attribute( 'is_main' ),
                            'op_code' => (int)$a->attribute( 'op_code' ), 'remote_id' => $a->attribute( 'remote_id' ) );
        return self::ok( $out );
    }

    public static function isMain( $args )
    {
        static::guard( __FUNCTION__ );
        $n = self::node( self::arg( $args, 0, 'int' ) );
        return self::ok( array( 'is_main' => (int)$n->attribute( 'node_id' ) === (int)$n->attribute( 'main_node_id' ) ) );
    }

    /** @return string|null why the object cannot be added below the parent, null when it can */
    protected static function addProblem( eZContentObject $o, eZContentObjectTreeNode $parent )
    {
        if ( !$parent->canCreate() )
            return 'No create access below the node';
        $allowed = array();
        foreach ( (array)$parent->canCreateClassList() as $c )
            $allowed[] = (int)$c['id'];
        if ( !in_array( (int)$o->attribute( 'contentclass_id' ), $allowed, true ) )
            return 'Objects of this class cannot be created there';
        foreach ( (array)$o->assignedNodes() as $n )
            if ( (int)$n->attribute( 'parent_node_id' ) === (int)$parent->attribute( 'node_id' ) )
                return 'The object is there already';
        foreach ( (array)$o->assignedNodes() as $n )
            if ( in_array( (int)$n->attribute( 'node_id' ), $parent->pathArray() ) || (int)$n->attribute( 'node_id' ) === (int)$parent->attribute( 'node_id' ) )
                return 'The node is inside the object\'s own subtree';
        return null;
    }

    public static function canAdd( $args )
    {
        static::guard( __FUNCTION__ );
        $o = eZContentObject::fetch( self::arg( $args, 0, 'int' ) );
        $p = eZContentObjectTreeNode::fetch( self::arg( $args, 1, 'int' ) );
        if ( !$o || !$p )
            throw new expServiceException( 'Object or node does not exist', 404 );
        $reason = self::addProblem( $o, $p );
        return self::ok( array( 'can_add' => $reason === null, 'reason' => $reason ) );
    }

    protected static function removeProblem( eZContentObjectTreeNode $n )
    {
        $o = $n->object();
        if ( !$n->canRemoveLocation() && !$n->canRemove() )
            return 'No access to remove this location';
        if ( (int)$o->assignedNodeCount() < 2 )
            return 'This is the only location of the object; remove the object instead';
        if ( (int)$n->childrenCount( false ) > 0 )
            return 'The node has children; remove it with its subtree instead';
        return null;
    }

    public static function canRemove( $args )
    {
        static::guard( __FUNCTION__ );
        $n = eZContentObjectTreeNode::fetch( self::arg( $args, 0, 'int' ) );
        if ( !$n )
            throw new expServiceException( 'Node does not exist', 404 );
        $reason = self::removeProblem( $n );
        return self::ok( array( 'can_remove' => $reason === null, 'reason' => $reason ) );
    }

    public static function candidates( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ) );
        $class = self::contentClass( self::arg( $args, 1, 'string' ) );
        $filter = array( 'class' => array() );
        $containers = array();
        foreach ( (array)eZContentClass::fetchAllClasses( true, false ) as $c )
            if ( $c->attribute( 'is_container' ) )
                $containers[] = $c->attribute( 'identifier' );
        list( $limit, $offset ) = self::paging( $args, 2, 3 );
        $items = array();
        foreach ( (array)$node->subTree( array( 'ClassFilterType' => 'include', 'ClassFilterArray' => $containers, 'Limit' => 500, 'AsObject' => true, 'SortBy' => array( array( 'path', true ) ) ) ) as $n )
            if ( $n->canCreate() )
            {
                foreach ( (array)$n->canCreateClassList() as $c )
                    if ( (int)$c['id'] === (int)$class->attribute( 'id' ) )
                    {
                        $items[] = self::exportNode( $n );
                        break;
                    }
            }
        return self::pageOf( $items, $args, 2, 3 );
    }

    public static function orphans( $args )
    {
        static::guard( __FUNCTION__ );
        $access = eZUser::currentUser()->hasAccessTo( 'content', 'read' );
        if ( $access['accessWord'] !== 'yes' )
            throw new expServiceException( 'Listing objects without a node needs unrestricted read access', 403 );
        list( $limit, $offset ) = self::paging( $args, 0, 1 );
        $db = eZDB::instance();
        $from = 'FROM ezcontentobject o WHERE o.status = 1 AND NOT EXISTS ( SELECT 1 FROM ezcontentobject_tree t WHERE t.contentobject_id = o.id )';
        $total = (int)$db->arrayQuery( "SELECT COUNT(*) AS cnt $from" )[0]['cnt'];
        $items = array();
        foreach ( $db->arrayQuery( "SELECT o.id AS id $from ORDER BY o.id", array( 'limit' => $limit, 'offset' => $offset ) ) as $r )
        {
            $o = eZContentObject::fetch( (int)$r['id'] );
            if ( $o )
                $items[] = self::exportObject( $o );
        }
        return self::page( $items, $total, $offset, $limit );
    }

    public static function add( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        $parent = self::node( self::arg( $args, 1, 'int' ), 'read' );
        if ( !$o->canEdit() && !eZUser::currentUser()->attribute( 'has_manage_locations' ) )
            throw new expServiceException( 'No access to change the locations of this object', 403 );
        if ( $reason = self::addProblem( $o, $parent ) )
            throw new expServiceException( $reason, strpos( $reason, 'access' ) !== false || strpos( $reason, 'cannot' ) !== false ? 403 : 409 );
        self::notLocked( array( (int)$parent->attribute( 'node_id' ) ) );
        $mainId = (int)$o->attribute( 'main_node_id' );
        $oid = (int)$o->attribute( 'id' );
        $pid = (int)$parent->attribute( 'node_id' );
        self::operation( 'addlocation', array( 'node_id' => $mainId, 'object_id' => $oid, 'select_node_id_array' => array( $pid ) ),
                         function () use ( $mainId, $oid, $pid ) { return eZContentOperationCollection::addAssignment( $mainId, $oid, array( $pid ) ); } );
        $nodes = self::locationsOf( $o );
        $found = false;
        foreach ( $nodes as $n )
            if ( $n['parent_node_id'] === $pid )
                $found = true;
        if ( !$found )
            throw new expServiceException( 'The location was not added', 422 );
        return self::ok( $nodes );
    }

    public static function addMany( $args )
    {
        static::guard( __FUNCTION__ );
        $target = self::node( self::arg( $args, 0, 'int' ), 'read' );
        $ids = array_slice( array_unique( array_map( 'intval', self::post( 'node_ids', 'list' ) ) ), 0, 500 );
        foreach ( $ids as $id )
        {
            $n = self::node( $id, 'read' );
            if ( $reason = self::addProblem( $n->object(), $target ) )
                throw new expServiceException( "Node $id: $reason", 422 );
        }
        $tid = (int)$target->attribute( 'node_id' );
        return self::runOrJob( 'addlocation', array( 'node_ids' => $ids, 'target_node_id' => $tid ), function () use ( $ids, $tid ) {
            $done = array();
            foreach ( $ids as $id )
            {
                $n = eZContentObjectTreeNode::fetch( $id );
                $oid = (int)$n->attribute( 'contentobject_id' );
                $main = (int)$n->attribute( 'main_node_id' );
                self::operation( 'addlocation', array( 'node_id' => $main, 'object_id' => $oid, 'select_node_id_array' => array( $tid ) ),
                                 function () use ( $main, $oid, $tid ) { return eZContentOperationCollection::addAssignment( $main, $oid, array( $tid ) ); } );
                $done[] = $oid;
            }
            return array( 'added' => $done );
        } );
    }

    public static function remove( $args )
    {
        static::guard( __FUNCTION__ );
        $n = self::node( self::arg( $args, 0, 'int' ), 'read' );
        if ( $reason = self::removeProblem( $n ) )
            throw new expServiceException( $reason, strpos( $reason, 'access' ) !== false ? 403 : 409 );
        self::notLocked( array( (int)$n->attribute( 'node_id' ) ) );
        $o = $n->object();
        $id = (int)$n->attribute( 'node_id' );
        self::operation( 'removelocation', array( 'node_list' => array( $id ) ), function () use ( $id ) { return eZContentOperationCollection::removeNodes( array( $id ) ); } );
        return self::ok( self::locationsOf( $o ) );
    }

    public static function removeMany( $args )
    {
        static::guard( __FUNCTION__ );
        $ids = array_slice( array_unique( array_map( 'intval', self::post( 'node_ids', 'list' ) ) ), 0, 500 );
        foreach ( $ids as $id )
        {
            $n = self::node( $id, 'read' );
            if ( $reason = self::removeProblem( $n ) )
                throw new expServiceException( "Node $id: $reason", 422 );
        }
        return self::runOrJob( 'removelocation', array( 'node_ids' => $ids ), function () use ( $ids ) {
            self::operation( 'removelocation', array( 'node_list' => $ids ), function () use ( $ids ) { return eZContentOperationCollection::removeNodes( $ids ); } );
            return array( 'removed' => $ids );
        } );
    }

    public static function setMain( $args )
    {
        static::guard( __FUNCTION__ );
        $n = self::node( self::arg( $args, 0, 'int' ), 'edit' );
        $o = $n->object();
        if ( (int)$n->attribute( 'node_id' ) === (int)$n->attribute( 'main_node_id' ) )
            throw new expServiceException( 'This is the main location already', 409 );
        $id = (int)$n->attribute( 'node_id' );
        $oid = (int)$o->attribute( 'id' );
        $parent = (int)$n->attribute( 'parent_node_id' );
        self::operation( 'updatemainassignment', array( 'main_assignment_id' => $id, 'object_id' => $oid, 'main_assignment_parent_id' => $parent ),
                         function () use ( $id, $oid, $parent ) { return eZContentOperationCollection::updateMainAssignment( $id, $oid, $parent ); } );
        return self::ok( self::locationsOf( $o ) );
    }
}
