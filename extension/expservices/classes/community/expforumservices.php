<?php
/**
 * ezjscore/call/expforum::<method>: forums (content objects of the forum class, kept in forum containers).
 * The classes are configured in expservices.ini [Community]. Creating, editing and removing follow the content
 * policies, so a forum is managed by whoever the roles allow.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expForumServices extends expCommunityBase
{
    public static $services = array(
        'containers' => array( 'summary' => 'The forum containers (the "forums" class) of the site', 'access' => 'public', 'write' => false,
            'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of containers' ),
        'list' => array( 'summary' => 'The forums below a node (a container, or the content root: all forums)', 'access' => 'public', 'write' => false,
            'args' => array( 'parent_node_id' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of forums with topic counts' ),
        'view' => array( 'summary' => 'One forum with its topic and reply counts and the latest topic', 'access' => 'public', 'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => 'forum' ),
        'stats' => array( 'summary' => 'Topics, replies and the latest activity of a forum', 'access' => 'public', 'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => 'topics, replies, last' ),
        'latest' => array( 'summary' => 'The latest topics over all forums', 'access' => 'public', 'write' => false, 'args' => array( 'limit' => 'int' ), 'returns' => 'list of topics' ),
        'search' => array( 'summary' => 'Words in forum names and descriptions', 'access' => 'public', 'write' => false,
            'args' => array( 'text' => 'string', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of forums' ),
        'path' => array( 'summary' => 'The path (container, forum) of a node for a breadcrumb', 'access' => 'public', 'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => 'list of nodes' ),
        'fields' => array( 'summary' => 'The fields a forum has and the classes in use', 'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'classes, fields' ),
        'create' => array( 'summary' => 'Creates a forum in a container (fields as JSON or POST fields: name, description)', 'access' => array( 'content', 'create' ), 'write' => true,
            'args' => array( 'parent_node_id' => 'int POST', 'fields' => 'json POST' ), 'returns' => 'the forum' ),
        'edit' => array( 'summary' => 'Changes the fields of a forum', 'access' => array( 'content', 'edit' ), 'write' => true,
            'args' => array( 'node_id' => 'int POST', 'fields' => 'json POST' ), 'returns' => 'the forum' ),
        'remove' => array( 'summary' => 'Removes a forum with its topics to the trash', 'access' => array( 'content', 'remove' ), 'write' => true, 'args' => array( 'node_id' => 'int POST' ), 'returns' => 'removed node' ),
        'hide' => array( 'summary' => 'Hides a forum from visitors', 'access' => array( 'content', 'hide' ), 'write' => true, 'args' => array( 'node_id' => 'int POST' ), 'returns' => 'the forum' ),
        'unhide' => array( 'summary' => 'Shows a hidden forum again', 'access' => array( 'content', 'hide' ), 'write' => true, 'args' => array( 'node_id' => 'int POST' ), 'returns' => 'the forum' ),
    );

    protected static function forum( $id, $fn = 'read' )
    {
        return self::item( $id, array( self::classOf( 'forum' ) ), $fn );
    }

    protected static function count( $nodeId, $class )
    {
        return (int)eZContentObjectTreeNode::subTreeCountByNodeID( array( 'ClassFilterType' => 'include', 'ClassFilterArray' => array( $class ) ), $nodeId );
    }

    protected static function encode( eZContentObjectTreeNode $n )
    {
        $i = expCommerceExport::contentItem( $n );
        $id = (int)$n->attribute( 'node_id' );
        $i['topics'] = self::count( $id, self::classOf( 'topic' ) );
        $i['replies'] = self::count( $id, self::classOf( 'reply' ) );
        return $i;
    }

    public static function containers( array $args )
    {
        self::guard( 'containers' );
        return self::children( self::node( self::root(), 'read' ), array( self::classOf( 'container' ) ), $args, 0, 1, false, 0 );
    }

    public static function list( array $args )
    {
        self::guard( 'list' );
        $parent = self::node( self::arg( $args, 0, 'int', self::root() ), 'read' );
        list( $limit, $offset ) = self::paging( $args, 1, 2 );
        $p = array( 'ClassFilterType' => 'include', 'ClassFilterArray' => array( self::classOf( 'forum' ) ), 'SortBy' => array( array( 'name', true ) ), 'Limit' => $limit, 'Offset' => $offset );
        $items = array();
        foreach ( (array)eZContentObjectTreeNode::subTreeByNodeID( $p, $parent->attribute( 'node_id' ) ) as $n )
            $items[] = self::encode( $n );
        return self::page( $items, eZContentObjectTreeNode::subTreeCountByNodeID( $p, $parent->attribute( 'node_id' ) ), $offset, $limit );
    }

    public static function view( array $args )
    {
        self::guard( 'view' );
        $n = self::forum( self::arg( $args, 0, 'int' ) );
        $out = self::encode( $n );
        $last = eZContentObjectTreeNode::subTreeByNodeID( array( 'ClassFilterType' => 'include', 'ClassFilterArray' => array( self::classOf( 'topic' ) ),
            'SortBy' => array( array( 'published', false ), array( 'node_id', false ) ), 'Limit' => 1 ), $n->attribute( 'node_id' ) );
        $out['latest_topic'] = $last ? expCommerceExport::contentItem( $last[0] ) : null;
        return self::ok( $out );
    }

    public static function stats( array $args )
    {
        self::guard( 'stats' );
        $n = self::forum( self::arg( $args, 0, 'int' ) );
        $id = (int)$n->attribute( 'node_id' );
        $last = eZContentObjectTreeNode::subTreeByNodeID( array( 'ClassFilterType' => 'include', 'ClassFilterArray' => array( self::classOf( 'topic' ), self::classOf( 'reply' ) ),
            'SortBy' => array( array( 'published', false ), array( 'node_id', false ) ), 'Limit' => 1 ), $id );
        return self::ok( array( 'node_id' => $id, 'topics' => self::count( $id, self::classOf( 'topic' ) ), 'replies' => self::count( $id, self::classOf( 'reply' ) ),
            'last' => $last ? self::iso( $last[0]->attribute( 'object' )->attribute( 'published' ) ) : null ) );
    }

    public static function latest( array $args )
    {
        self::guard( 'latest' );
        $limit = min( self::arg( $args, 0, 'int', 10 ), 100 );
        $items = array();
        foreach ( (array)eZContentObjectTreeNode::subTreeByNodeID( array( 'ClassFilterType' => 'include', 'ClassFilterArray' => array( self::classOf( 'topic' ) ),
            'SortBy' => array( array( 'published', false ), array( 'node_id', false ) ), 'Limit' => $limit ), self::root() ) as $n )
            $items[] = expCommerceExport::contentItem( $n );
        return self::ok( $items );
    }

    public static function search( array $args )
    {
        self::guard( 'search' );
        return self::searchIn( array( self::classOf( 'forum' ) ), self::arg( $args, 0, 'string' ), self::root(), $args, 1, 2 );
    }

    public static function path( array $args )
    {
        self::guard( 'path' );
        $n = self::node( self::arg( $args, 0, 'int' ), 'read' );
        $out = array();
        foreach ( (array)$n->attribute( 'path' ) as $p )
            $out[] = array( 'node_id' => (int)$p->attribute( 'node_id' ), 'name' => $p->attribute( 'name' ), 'class' => $p->attribute( 'class_identifier' ) );
        $out[] = array( 'node_id' => (int)$n->attribute( 'node_id' ), 'name' => $n->attribute( 'name' ), 'class' => $n->attribute( 'class_identifier' ) );
        return self::ok( $out );
    }

    public static function fields( array $args )
    {
        self::guard( 'fields' );
        $classes = array();
        foreach ( array( 'container', 'forum', 'topic', 'reply', 'comment' ) as $role )
        {
            $c = self::classOf( $role );
            $classes[$role] = array( 'class' => $c, 'exists' => (bool)eZContentClass::fetchByIdentifier( $c ), 'fields' => eZContentClass::fetchByIdentifier( $c ) ? self::fieldsOf( $c ) : array() );
        }
        return self::ok( $classes );
    }

    public static function create( array $args )
    {
        self::guard( 'create' );
        $parent = self::node( self::post( 'parent_node_id', 'int' ), 'create' );
        return self::ok( self::encode( self::createChild( $parent, self::classOf( 'forum' ) ) ) );
    }

    public static function edit( array $args )
    {
        self::guard( 'edit' );
        return self::ok( self::encode( self::editNode( self::forum( self::post( 'node_id', 'int' ), 'edit' ) ) ) );
    }

    public static function remove( array $args )
    {
        self::guard( 'remove' );
        return self::ok( self::removeNode( self::forum( self::post( 'node_id', 'int' ), 'remove' ) ) );
    }

    public static function hide( array $args )
    {
        self::guard( 'hide' );
        return self::ok( self::setHidden( self::forum( self::post( 'node_id', 'int' ), 'read' ), true ) );
    }

    public static function unhide( array $args )
    {
        self::guard( 'unhide' );
        return self::ok( self::setHidden( self::forum( self::post( 'node_id', 'int' ), 'read' ), false ) );
    }
}
