<?php
/**
 * Trash services: ezjscore/call/exptrash::<method>[::arg...]
 *
 * The content trash: list and look at trashed objects, restore them to their old or a new place, purge one or
 * several, and empty the trash (needs a confirmation field).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expTrashServices extends expContentServiceBase
{
    public static $services = array(
        'list' => array( 'summary' => 'Trashed objects, paged and sorted (sort: name, class_name, published, modified, section)', 'access' => array( 'content', 'restore' ), 'write' => false, 'args' => array( 'sort' => 'string', 'order' => 'string', 'limit' => 'int', 'offset' => 'int', 'filter' => 'json' ), 'returns' => 'page of trashed objects' ),
        'count' => array( 'summary' => 'Number of trashed objects', 'access' => array( 'content', 'restore' ), 'write' => false, 'args' => array(), 'returns' => '{count}' ),
        'get' => array( 'summary' => 'One trashed object', 'access' => array( 'content', 'restore' ), 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => 'trashed object' ),
        'isTrashed' => array( 'summary' => 'Whether an object is in the trash', 'access' => array( 'content', 'restore' ), 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => '{trashed}' ),
        'originalParent' => array( 'summary' => 'The node a trashed object was removed from, when it still exists in the same place', 'access' => array( 'content', 'restore' ), 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => 'node or null' ),
        'byClass' => array( 'summary' => 'Trashed objects of a class', 'access' => array( 'content', 'restore' ), 'write' => false, 'args' => array( 'class' => 'string', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page' ),
        'since' => array( 'summary' => 'Objects trashed since a time (timestamp or date)', 'access' => array( 'content', 'restore' ), 'write' => false, 'args' => array( 'since' => 'string', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page' ),
        'canEmpty' => array( 'summary' => 'Whether the current user may purge from the trash', 'access' => 'user', 'write' => false, 'args' => array(), 'returns' => '{can_empty}' ),
        'restore' => array( 'summary' => 'Restores an object to its original parent, or to POST parent_node_id', 'access' => array( 'content', 'restore' ), 'write' => true, 'args' => array( 'object_id' => 'int' ), 'returns' => 'node' ),
        'purge' => array( 'summary' => 'Removes one object from the trash for good', 'access' => array( 'content', 'cleantrash' ), 'write' => true, 'args' => array( 'object_id' => 'int' ), 'returns' => '{purged}' ),
        'purgeMany' => array( 'summary' => 'Purges several objects. POST: object_ids', 'access' => array( 'content', 'cleantrash' ), 'write' => true, 'args' => array(), 'returns' => '{purged}' ),
        'emptyTrash' => array( 'summary' => 'Empties the whole trash. POST: confirm=yes', 'access' => array( 'content', 'cleantrash' ), 'write' => true, 'args' => array(), 'returns' => '{purged}' ),
    );

    protected static function service()
    {
        if ( !class_exists( '\Exponential\Service\Trash' ) )
            require_once 'kernel/private/classes/services/trash.php';
    }

    protected static function exportTrashed( eZContentObjectTrashNode $t )
    {
        $parent = null;
        try { $parent = $t->originalParent(); } catch ( Throwable $e ) { }
        return array( 'object_id' => (int)$t->attribute( 'contentobject_id' ), 'node_id' => (int)$t->attribute( 'node_id' ), 'name' => $t->attribute( 'name' ),
                      'class' => $t->attribute( 'class_identifier' ), 'parent_node_id' => (int)$t->attribute( 'parent_node_id' ),
                      'original_parent_exists' => $parent instanceof eZContentObjectTreeNode, 'trashed' => self::iso( $t->attribute( 'trashed' ) ),
                      'section_id' => (int)$t->attribute( 'section_id' ), 'published' => self::iso( $t->attribute( 'published' ) ) );
    }

    protected static function trashParams( $sort, $order, $limit, $offset, array $extra = array() )
    {
        $allowed = array( 'name', 'class_name', 'published', 'modified', 'section', 'trashed' );
        if ( !in_array( $sort, $allowed, true ) )
            throw new expServiceException( 'Unknown sort, use one of ' . implode( ', ', $allowed ), 400 );
        if ( !in_array( $order, array( 'asc', 'desc' ), true ) )
            throw new expServiceException( 'order is asc or desc', 400 );
        $p = array( 'Limit' => $limit, 'Offset' => $offset, 'SortBy' => array( array( $sort, $order === 'asc' ) ), 'AttributeFilter' => false );
        return array_merge( $p, $extra );
    }

    protected static function pageOfTrash( array $params, $limit, $offset )
    {
        $total = (int)eZContentObjectTrashNode::trashListCount( array_diff_key( $params, array( 'Limit' => 1, 'Offset' => 1, 'SortBy' => 1 ) ) );
        $items = array();
        foreach ( $total ? (array)eZContentObjectTrashNode::trashList( $params ) : array() as $t )
            $items[] = self::exportTrashed( $t );
        return self::page( $items, $total, $offset, $limit );
    }

    public static function list( $args )
    {
        static::guard( __FUNCTION__ );
        $sort = self::arg( $args, 0, 'string', 'trashed' );
        $order = self::arg( $args, 1, 'string', 'desc' );
        list( $limit, $offset ) = self::paging( $args, 2, 3 );
        $filter = self::arg( $args, 4, 'json', array() );
        $extra = array();
        if ( !empty( $filter['name'] ) )
            $extra['ObjectNameFilter'] = (string)$filter['name'];
        return self::pageOfTrash( self::trashParams( $sort, $order, $limit, $offset, $extra ), $limit, $offset );
    }

    public static function count( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( array( 'count' => (int)eZContentObjectTrashNode::trashListCount( array( 'AttributeFilter' => false ) ) ) );
    }

    protected static function trashNode( $objectId )
    {
        $t = eZContentObjectTrashNode::fetchByContentObjectID( (int)$objectId );
        if ( !$t instanceof eZContentObjectTrashNode )
            throw new expServiceException( "Object $objectId is not in the trash", 404 );
        return $t;
    }

    public static function get( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( self::exportTrashed( self::trashNode( self::arg( $args, 0, 'int' ) ) ) );
    }

    public static function isTrashed( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( array( 'trashed' => eZContentObjectTrashNode::fetchByContentObjectID( self::arg( $args, 0, 'int' ) ) instanceof eZContentObjectTrashNode ) );
    }

    public static function originalParent( $args )
    {
        static::guard( __FUNCTION__ );
        $p = self::trashNode( self::arg( $args, 0, 'int' ) )->originalParent();
        return self::ok( $p instanceof eZContentObjectTreeNode ? self::exportNode( $p ) : null );
    }

    public static function byClass( $args )
    {
        static::guard( __FUNCTION__ );
        $class = self::contentClass( self::arg( $args, 0, 'string' ) );
        // class_identifier is not an attribute filter key: filter the list in PHP
        $all = array();
        foreach ( (array)eZContentObjectTrashNode::trashList( array( 'AttributeFilter' => false, 'SortBy' => array( array( 'published', false ) ) ) ) as $t )
            if ( $t->attribute( 'class_identifier' ) === $class->attribute( 'identifier' ) )
                $all[] = self::exportTrashed( $t );
        return self::pageOf( $all, $args, 1, 2 );
    }

    public static function since( $args )
    {
        static::guard( __FUNCTION__ );
        $since = self::arg( $args, 0, 'string' );
        $ts = ctype_digit( $since ) ? (int)$since : (int)strtotime( $since );
        if ( $ts <= 0 )
            throw new expServiceException( 'since is a timestamp or a date', 400 );
        list( $limit, $offset ) = self::paging( $args, 1, 2 );
        return self::pageOfTrash( self::trashParams( 'trashed', 'desc', $limit, $offset, array( 'TrashedFrom' => $ts ) ), $limit, $offset );
    }

    public static function canEmpty( $args )
    {
        static::guard( __FUNCTION__ );
        self::service();
        return self::ok( array( 'can_empty' => (bool)\Exponential\Service\Trash::canEmpty( eZUser::currentUser() ) ) );
    }

    public static function restore( $args )
    {
        static::guard( __FUNCTION__ );
        $id = self::arg( $args, 0, 'int' );
        $object = eZContentObject::fetch( $id );
        if ( !$object || (int)$object->attribute( 'status' ) !== eZContentObject::STATUS_ARCHIVED )
            throw new expServiceException( "Object $id is not in the trash", 404 );
        $trash = self::trashNode( $id );
        $parentId = self::post( 'parent_node_id', 'int', 0 );
        if ( !$parentId )
        {
            $orig = $trash->originalParent();
            if ( !$orig instanceof eZContentObjectTreeNode )
                throw new expServiceException( 'The original parent is gone; give parent_node_id', 409 );
            $parentId = (int)$orig->attribute( 'node_id' );
        }
        $parent = self::node( $parentId, 'read' );
        $class = $object->contentClass();
        if ( $parent->checkAccess( 'create', $class->attribute( 'id' ), $parent->object()->attribute( 'contentclass_id' ) ) != 1 )
            throw new expServiceException( 'You cannot create objects of this class there', 403 );
        $version = $object->attribute( 'current' );
        $db = eZDB::instance();
        $db->begin();
        foreach ( (array)$version->attribute( 'node_assignments' ) as $a )
            $a->purge();
        $version->assignToNode( $parentId, 1 );
        $object->setAttribute( 'status', eZContentObject::STATUS_DRAFT );
        $object->store();
        $version->setAttribute( 'status', eZContentObjectVersion::STATUS_DRAFT );
        $version->store();
        $object->restoreObjectAttributes();
        $result = eZOperationHandler::execute( 'content', 'publish', array( 'object_id' => $id, 'version' => $version->attribute( 'version' ) ) );
        if ( is_array( $result ) && isset( $result['status'] ) && $result['status'] != eZModuleOperationInfo::STATUS_CONTINUE )
        {
            $db->rollback();
            throw new expServiceException( 'The restore was not completed (a workflow stopped it)', 422 );
        }
        eZContentObject::clearCache();
        $object = eZContentObject::fetch( $id );
        eZContentObjectTrashNode::purgeForObject( $id );
        eZContentObject::fixReverseRelations( $id, 'restore', false );
        $db->commit();
        eZContentCacheManager::clearContentCacheIfNeeded( $id, $version->attribute( 'version' ) );
        return self::ok( self::exportNode( $object->attribute( 'main_node' ) ) );
    }

    public static function purge( $args )
    {
        static::guard( __FUNCTION__ );
        self::service();
        $id = self::arg( $args, 0, 'int' );
        self::trashNode( $id );
        $n = \Exponential\Service\Trash::purgeObjects( array( $id ) );
        return self::ok( array( 'purged' => (int)$n ) );
    }

    public static function purgeMany( $args )
    {
        static::guard( __FUNCTION__ );
        self::service();
        $ids = array_slice( array_unique( array_map( 'intval', self::post( 'object_ids', 'list' ) ) ), 0, 200 );
        foreach ( $ids as $id )
            self::trashNode( $id );
        return self::ok( array( 'purged' => (int)\Exponential\Service\Trash::purgeObjects( $ids ) ) );
    }

    public static function emptyTrash( $args )
    {
        static::guard( __FUNCTION__ );
        self::service();
        if ( self::post( 'confirm', 'string', '' ) !== 'yes' )
            throw new expServiceException( 'Emptying the trash needs the POST field confirm=yes', 400 );
        $r = \Exponential\Service\Trash::emptyTrash( 100, 0 );
        return self::ok( array( 'purged' => (int)( is_array( $r ) ? $r['purged'] : $r ) ) );
    }
}
