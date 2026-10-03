<?php
/**
 * Relation services: ezjscore/call/exprelation::<method>[::arg...]
 *
 * Relations between content objects in both directions, by type (common, embed, link, attribute) and by
 * attribute, counts, and writes that add or remove a relation.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expRelationServices extends expContentServiceBase
{
    public static $services = array(
        'related' => array( 'summary' => 'Objects an object relates to (of the current version), paged. type: common, embed, link, attribute or all', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int', 'type' => 'string', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page of relations' ),
        'reverse' => array( 'summary' => 'Objects that relate to an object, paged', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int', 'type' => 'string', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page of relations' ),
        'counts' => array( 'summary' => 'Relation counts per type and direction', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => '{related, reverse}' ),
        'relatedCount' => array( 'summary' => 'Number of relations from an object', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int', 'type' => 'string' ), 'returns' => '{count}' ),
        'reverseCount' => array( 'summary' => 'Number of objects relating to an object', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int', 'type' => 'string' ), 'returns' => '{count}' ),
        'reverseCountForNodes' => array( 'summary' => 'Reverse relation counts for a list of nodes', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'node_ids' => 'list' ), 'returns' => 'node id => count' ),
        'byAttribute' => array( 'summary' => 'Objects related through one attribute', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int', 'identifier' => 'string' ), 'returns' => 'objects' ),
        'exists' => array( 'summary' => 'Whether a relation between two objects exists, and its types', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'from_object_id' => 'int', 'to_object_id' => 'int' ), 'returns' => '{exists, types}' ),
        'neighbours' => array( 'summary' => 'Both directions at once: related and reverse related, light', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int', 'limit' => 'int' ), 'returns' => '{related, reverse}' ),
        'broken' => array( 'summary' => 'Relations of an object that point at objects that are gone or not published', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => 'relations' ),
        'types' => array( 'summary' => 'The relation type names and their codes', 'access' => 'user', 'write' => false, 'args' => array(), 'returns' => 'name => code' ),
        'add' => array( 'summary' => 'Adds a relation from an object to another. POST: type (common, embed, link; default common), version', 'access' => array( 'content', 'edit' ), 'write' => true, 'args' => array( 'from_object_id' => 'int', 'to_object_id' => 'int' ), 'returns' => '{added}' ),
        'remove' => array( 'summary' => 'Removes a relation. POST: type, version', 'access' => array( 'content', 'edit' ), 'write' => true, 'args' => array( 'from_object_id' => 'int', 'to_object_id' => 'int' ), 'returns' => '{removed}' ),
        'removeAll' => array( 'summary' => 'Removes every common relation of an object (embed, link and attribute relations belong to the content)', 'access' => array( 'content', 'edit' ), 'write' => true, 'args' => array( 'object_id' => 'int' ), 'returns' => '{removed}' ),
        'replace' => array( 'summary' => 'Makes the common relations of an object exactly the given list. POST: object_ids', 'access' => array( 'content', 'edit' ), 'write' => true, 'args' => array( 'object_id' => 'int' ), 'returns' => '{added, removed}' ),
    );

    protected static $codes = array( 'common' => 1, 'embed' => 2, 'link' => 4, 'attribute' => 8 );

    protected static function typeMask( $type )
    {
        if ( $type === '' || $type === 'all' )
            return 15;
        if ( !isset( self::$codes[$type] ) )
            throw new expServiceException( 'type is one of all, ' . implode( ', ', array_keys( self::$codes ) ), 400 );
        return self::$codes[$type];
    }

    protected static function typeNames( $mask )
    {
        $out = array();
        foreach ( self::$codes as $name => $code )
            if ( $mask & $code )
                $out[] = $name;
        return $out;
    }

    /** Rows of relations from ($from) or to an object, current versions of published objects only. */
    protected static function rows( eZContentObject $o, $reverse, $mask )
    {
        $db = eZDB::instance();
        $id = (int)$o->attribute( 'id' );
        if ( $reverse )
            $sql = "SELECT l.from_contentobject_id AS other, l.relation_type AS type, l.contentclassattribute_id AS attr
                      FROM ezcontentobject_link l, ezcontentobject f
                     WHERE l.to_contentobject_id = $id AND f.id = l.from_contentobject_id AND f.current_version = l.from_contentobject_version
                       AND f.status = 1 AND ( l.relation_type & $mask ) > 0 ORDER BY l.id";
        else
            $sql = "SELECT l.to_contentobject_id AS other, l.relation_type AS type, l.contentclassattribute_id AS attr
                      FROM ezcontentobject_link l
                     WHERE l.from_contentobject_id = $id AND l.from_contentobject_version = " . (int)$o->attribute( 'current_version' ) . "
                       AND ( l.relation_type & $mask ) > 0 ORDER BY l.id";
        return $db->arrayQuery( $sql );
    }

    protected static function readableRelations( eZContentObject $o, $reverse, $mask )
    {
        $out = array();
        foreach ( self::rows( $o, $reverse, $mask ) as $r )
        {
            $other = eZContentObject::fetch( (int)$r['other'] );
            if ( !$other || (int)$other->attribute( 'status' ) !== eZContentObject::STATUS_PUBLISHED || !$other->canRead() )
                continue;
            $ca = (int)$r['attr'] ? eZContentClassAttribute::fetch( (int)$r['attr'] ) : null;
            $out[] = array( 'object_id' => (int)$other->attribute( 'id' ), 'name' => $other->attribute( 'name' ), 'class' => $other->attribute( 'class_identifier' ),
                            'main_node_id' => (int)$other->attribute( 'main_node_id' ), 'types' => self::typeNames( (int)$r['type'] ),
                            'attribute' => $ca ? $ca->attribute( 'identifier' ) : null );
        }
        return $out;
    }

    public static function related( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        return self::pageOf( self::readableRelations( $o, false, self::typeMask( self::arg( $args, 1, 'string', 'all' ) ) ), $args, 2, 3 );
    }

    public static function reverse( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        return self::pageOf( self::readableRelations( $o, true, self::typeMask( self::arg( $args, 1, 'string', 'all' ) ) ), $args, 2, 3 );
    }

    public static function counts( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        $out = array( 'related' => array(), 'reverse' => array() );
        foreach ( self::$codes as $name => $code )
        {
            $out['related'][$name] = count( self::rows( $o, false, $code ) );
            $out['reverse'][$name] = count( self::rows( $o, true, $code ) );
        }
        return self::ok( $out );
    }

    public static function relatedCount( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        return self::ok( array( 'count' => count( self::rows( $o, false, self::typeMask( self::arg( $args, 1, 'string', 'all' ) ) ) ) ) );
    }

    public static function reverseCount( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        return self::ok( array( 'count' => count( self::rows( $o, true, self::typeMask( self::arg( $args, 1, 'string', 'all' ) ) ) ) ) );
    }

    public static function reverseCountForNodes( $args )
    {
        static::guard( __FUNCTION__ );
        $ids = array_slice( array_map( 'intval', self::arg( $args, 0, 'list' ) ), 0, 100 );
        $allowed = array();
        foreach ( $ids as $id )
        {
            $n = eZContentObjectTreeNode::fetch( $id );
            if ( $n && $n->canRead() )
                $allowed[] = $id;
        }
        $counts = $allowed ? eZContentObjectTreeNode::reverseRelatedCount( $allowed ) : array();
        $out = array();
        foreach ( $allowed as $id )
            $out[$id] = isset( $counts[$id] ) ? (int)$counts[$id] : 0;
        return self::ok( (object)$out );
    }

    public static function byAttribute( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        $identifier = self::arg( $args, 1, 'string' );
        $out = array();
        foreach ( self::readableRelations( $o, false, 15 ) as $r )
            if ( $r['attribute'] === $identifier )
                $out[] = $r;
        return self::ok( $out );
    }

    public static function exists( $args )
    {
        static::guard( __FUNCTION__ );
        $from = self::object( self::arg( $args, 0, 'int' ) );
        $to = self::arg( $args, 1, 'int' );
        $mask = 0;
        foreach ( self::rows( $from, false, 15 ) as $r )
            if ( (int)$r['other'] === $to )
                $mask |= (int)$r['type'];
        return self::ok( array( 'exists' => $mask > 0, 'types' => self::typeNames( $mask ) ) );
    }

    public static function neighbours( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        $limit = min( 100, max( 1, self::arg( $args, 1, 'int', 20 ) ) );
        return self::ok( array( 'related' => array_slice( self::readableRelations( $o, false, 15 ), 0, $limit ), 'reverse' => array_slice( self::readableRelations( $o, true, 15 ), 0, $limit ) ) );
    }

    public static function broken( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        $out = array();
        foreach ( self::rows( $o, false, 15 ) as $r )
        {
            $other = eZContentObject::fetch( (int)$r['other'] );
            if ( !$other || (int)$other->attribute( 'status' ) !== eZContentObject::STATUS_PUBLISHED )
                $out[] = array( 'object_id' => (int)$r['other'], 'types' => self::typeNames( (int)$r['type'] ), 'problem' => $other ? 'not published' : 'object is gone' );
        }
        return self::ok( $out );
    }

    public static function types( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( (object)self::$codes );
    }

    protected static function writableType( $type )
    {
        if ( !in_array( $type, array( 'common', 'embed', 'link' ), true ) )
            throw new expServiceException( 'type is common, embed or link (attribute relations follow the attribute content)', 400 );
        return self::$codes[$type];
    }

    protected static function fromVersion( eZContentObject $o, $version )
    {
        return $version ? (int)self::version( $o, $version, true )->attribute( 'version' ) : (int)$o->attribute( 'current_version' );
    }

    public static function add( $args )
    {
        static::guard( __FUNCTION__ );
        $from = self::object( self::arg( $args, 0, 'int' ), 'edit' );
        $to = self::object( self::arg( $args, 1, 'int' ) );
        if ( (int)$from->attribute( 'id' ) === (int)$to->attribute( 'id' ) )
            throw new expServiceException( 'An object cannot relate to itself', 422 );
        $type = self::writableType( self::post( 'type', 'string', 'common' ) );
        $v = self::fromVersion( $from, self::post( 'version', 'int', 0 ) );
        $existing = 0;
        foreach ( self::rows( $from, false, 15 ) as $r )
            if ( (int)$r['other'] === (int)$to->attribute( 'id' ) )
                $existing |= (int)$r['type'];
        if ( $existing & $type && $v === (int)$from->attribute( 'current_version' ) )
            throw new expServiceException( 'That relation exists already', 409 );
        $from->addContentObjectRelation( (int)$to->attribute( 'id' ), $v, 0, $type );
        return self::ok( array( 'added' => array( 'from' => (int)$from->attribute( 'id' ), 'to' => (int)$to->attribute( 'id' ), 'type' => array_search( $type, self::$codes ), 'version' => $v ) ) );
    }

    public static function remove( $args )
    {
        static::guard( __FUNCTION__ );
        $from = self::object( self::arg( $args, 0, 'int' ), 'edit' );
        $toId = self::arg( $args, 1, 'int' );
        $type = self::writableType( self::post( 'type', 'string', 'common' ) );
        $v = self::fromVersion( $from, self::post( 'version', 'int', 0 ) );
        $found = false;
        foreach ( self::rows( $from, false, $type ) as $r )
            if ( (int)$r['other'] === $toId )
                $found = true;
        if ( !$found && $v === (int)$from->attribute( 'current_version' ) )
            throw new expServiceException( 'No such relation', 404 );
        $from->removeContentObjectRelation( $toId, $v, 0, $type );
        return self::ok( array( 'removed' => array( 'from' => (int)$from->attribute( 'id' ), 'to' => $toId, 'type' => array_search( $type, self::$codes ), 'version' => $v ) ) );
    }

    public static function removeAll( $args )
    {
        static::guard( __FUNCTION__ );
        $from = self::object( self::arg( $args, 0, 'int' ), 'edit' );
        $v = (int)$from->attribute( 'current_version' );
        $removed = array();
        foreach ( self::rows( $from, false, 1 ) as $r )
        {
            $from->removeContentObjectRelation( (int)$r['other'], $v, 0, 1 );
            $removed[] = (int)$r['other'];
        }
        return self::ok( array( 'removed' => $removed ) );
    }

    public static function replace( $args )
    {
        static::guard( __FUNCTION__ );
        $from = self::object( self::arg( $args, 0, 'int' ), 'edit' );
        $wanted = array_unique( array_map( 'intval', self::post( 'object_ids', 'list', array() ) ) );
        foreach ( $wanted as $id )
        {
            self::object( $id );
            if ( $id === (int)$from->attribute( 'id' ) )
                throw new expServiceException( 'An object cannot relate to itself', 422 );
        }
        $v = (int)$from->attribute( 'current_version' );
        $have = array();
        foreach ( self::rows( $from, false, 1 ) as $r )
            $have[] = (int)$r['other'];
        $removed = array_diff( $have, $wanted );
        $added = array_diff( $wanted, $have );
        foreach ( $removed as $id )
            $from->removeContentObjectRelation( $id, $v, 0, 1 );
        foreach ( $added as $id )
            $from->addContentObjectRelation( $id, $v, 0, 1 );
        return self::ok( array( 'added' => array_values( $added ), 'removed' => array_values( $removed ) ) );
    }
}
