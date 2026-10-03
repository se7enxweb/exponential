<?php
/**
 * ezjscore/call/exptopic::<method>: the topics of a forum (content objects of the topic class). Reading is public
 * as far as the content policies let visitors read; writing, editing and removing follow the policies, so a
 * member edits and removes their own topics when the role has the owner limitation.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expTopicServices extends expCommunityBase
{
    public static $services = array(
        'list' => array( 'summary' => 'The topics of a forum, sticky ones first, then newest first', 'access' => 'public', 'write' => false,
            'args' => array( 'forum_node_id' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of topics with reply counts' ),
        'view' => array( 'summary' => 'One topic with its text and reply count', 'access' => 'public', 'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => 'topic' ),
        'count' => array( 'summary' => 'How many topics a forum has', 'access' => 'public', 'write' => false, 'args' => array( 'forum_node_id' => 'int' ), 'returns' => 'count' ),
        'latest' => array( 'summary' => 'The latest topics, in one forum or everywhere', 'access' => 'public', 'write' => false, 'args' => array( 'limit' => 'int', 'forum_node_id' => 'int' ), 'returns' => 'list of topics' ),
        'sticky' => array( 'summary' => 'The sticky topics of a forum', 'access' => 'public', 'write' => false, 'args' => array( 'forum_node_id' => 'int' ), 'returns' => 'list of topics' ),
        'byUser' => array( 'summary' => 'The topics a user started', 'access' => 'public', 'write' => false, 'args' => array( 'user_id' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of topics' ),
        'mine' => array( 'summary' => 'The topics the logged-in user started', 'access' => 'user', 'write' => false, 'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of topics' ),
        'search' => array( 'summary' => 'Words in topic subjects and texts', 'access' => 'public', 'write' => false,
            'args' => array( 'text' => 'string', 'forum_node_id' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of topics' ),
        'lastReply' => array( 'summary' => 'The newest reply of a topic', 'access' => 'public', 'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => 'reply or null' ),
        'canCreate' => array( 'summary' => 'Whether the current user may start a topic in a forum', 'access' => 'public', 'write' => false, 'args' => array( 'forum_node_id' => 'int' ), 'returns' => 'can' ),
        'create' => array( 'summary' => 'Starts a topic in a forum (fields subject, message, sticky as JSON or POST fields)', 'access' => array( 'content', 'create' ), 'write' => true,
            'args' => array( 'forum_node_id' => 'int POST', 'fields' => 'json POST' ), 'returns' => 'the topic' ),
        'edit' => array( 'summary' => 'Changes subject or message of a topic', 'access' => array( 'content', 'edit' ), 'write' => true, 'args' => array( 'node_id' => 'int POST', 'fields' => 'json POST' ), 'returns' => 'the topic' ),
        'remove' => array( 'summary' => 'Removes a topic with its replies to the trash', 'access' => array( 'content', 'remove' ), 'write' => true, 'args' => array( 'node_id' => 'int POST' ), 'returns' => 'removed node' ),
        'setSticky' => array( 'summary' => 'Makes a topic sticky or not (moderation)', 'access' => array( 'content', 'edit' ), 'write' => true, 'args' => array( 'node_id' => 'int POST', 'sticky' => 'bool POST' ), 'returns' => 'the topic' ),
        'move' => array( 'summary' => 'Moves a topic to another forum', 'access' => array( 'content', 'edit' ), 'write' => true, 'args' => array( 'node_id' => 'int POST', 'forum_node_id' => 'int POST' ), 'returns' => 'the topic' ),
        'hide' => array( 'summary' => 'Hides a topic with its replies from visitors (moderation)', 'access' => array( 'content', 'hide' ), 'write' => true, 'args' => array( 'node_id' => 'int POST' ), 'returns' => 'the topic' ),
        'unhide' => array( 'summary' => 'Shows a hidden topic again', 'access' => array( 'content', 'hide' ), 'write' => true, 'args' => array( 'node_id' => 'int POST' ), 'returns' => 'the topic' ),
    );

    protected static function topic( $id, $fn = 'read' )
    {
        return self::item( $id, array( self::classOf( 'topic' ) ), $fn );
    }

    protected static function encode( eZContentObjectTreeNode $n )
    {
        $i = expCommerceExport::contentItem( $n );
        $i['replies'] = (int)eZContentObjectTreeNode::subTreeCountByNodeID( array( 'ClassFilterType' => 'include', 'ClassFilterArray' => array( self::classOf( 'reply' ) ) ), $n->attribute( 'node_id' ) );
        $i['sticky'] = isset( $i['fields'][self::stickyField()] ) ? (bool)$i['fields'][self::stickyField()] : false;
        return $i;
    }

    protected static function stickyField()
    {
        $ini = eZINI::instance( 'expservices.ini' );
        return $ini->hasVariable( 'Community', 'TopicStickyAttribute' ) ? $ini->variable( 'Community', 'TopicStickyAttribute' ) : 'sticky';
    }

    public static function list( array $args )
    {
        self::guard( 'list' );
        $forum = self::item( self::arg( $args, 0, 'int' ), array( self::classOf( 'forum' ) ), 'read' );
        list( $limit, $offset ) = self::paging( $args, 1, 2 );
        $nodes = eZContentObjectTreeNode::subTreeByNodeID( array( 'ClassFilterType' => 'include', 'ClassFilterArray' => array( self::classOf( 'topic' ) ),
            'SortBy' => array( array( 'published', false ), array( 'node_id', false ) ), 'Depth' => 1, 'DepthOperator' => 'eq', 'Limit' => 1000 ), $forum->attribute( 'node_id' ) );
        $items = array();
        foreach ( (array)$nodes as $n )
            $items[] = self::encode( $n );
        usort( $items, function ( $a, $b ) { return (int)$b['sticky'] <=> (int)$a['sticky']; } );
        return self::page( array_slice( $items, $offset, $limit ), count( $items ), $offset, $limit );
    }

    public static function view( array $args )
    {
        self::guard( 'view' );
        return self::ok( self::encode( self::topic( self::arg( $args, 0, 'int' ) ) ) );
    }

    public static function count( array $args )
    {
        self::guard( 'count' );
        $forum = self::item( self::arg( $args, 0, 'int' ), array( self::classOf( 'forum' ) ), 'read' );
        return self::ok( array( 'count' => (int)eZContentObjectTreeNode::subTreeCountByNodeID( array( 'ClassFilterType' => 'include', 'ClassFilterArray' => array( self::classOf( 'topic' ) ) ), $forum->attribute( 'node_id' ) ) ) );
    }

    public static function latest( array $args )
    {
        self::guard( 'latest' );
        $limit = min( self::arg( $args, 0, 'int', 10 ), 100 );
        $parent = self::arg( $args, 1, 'int', self::root() );
        self::node( $parent, 'read' );
        $items = array();
        foreach ( (array)eZContentObjectTreeNode::subTreeByNodeID( array( 'ClassFilterType' => 'include', 'ClassFilterArray' => array( self::classOf( 'topic' ) ),
            'SortBy' => array( array( 'published', false ), array( 'node_id', false ) ), 'Limit' => $limit ), $parent ) as $n )
            $items[] = self::encode( $n );
        return self::ok( $items );
    }

    public static function sticky( array $args )
    {
        self::guard( 'sticky' );
        $forum = self::item( self::arg( $args, 0, 'int' ), array( self::classOf( 'forum' ) ), 'read' );
        $items = array();
        foreach ( (array)eZContentObjectTreeNode::subTreeByNodeID( array( 'ClassFilterType' => 'include', 'ClassFilterArray' => array( self::classOf( 'topic' ) ),
            'Depth' => 1, 'DepthOperator' => 'eq', 'Limit' => 1000 ), $forum->attribute( 'node_id' ) ) as $n )
        {
            $e = self::encode( $n );
            if ( $e['sticky'] )
                $items[] = $e;
        }
        return self::ok( $items );
    }

    public static function byUser( array $args )
    {
        self::guard( 'byUser' );
        return self::ownedBy( array( self::classOf( 'topic' ) ), self::arg( $args, 0, 'int' ), $args, 1, 2 );
    }

    public static function mine( array $args )
    {
        self::guard( 'mine' );
        return self::ownedBy( array( self::classOf( 'topic' ) ), eZUser::currentUser()->attribute( 'contentobject_id' ), $args, 0, 1 );
    }

    public static function search( array $args )
    {
        self::guard( 'search' );
        $parent = self::arg( $args, 1, 'int', self::root() );
        self::node( $parent, 'read' );
        return self::searchIn( array( self::classOf( 'topic' ) ), self::arg( $args, 0, 'string' ), $parent, $args, 2, 3 );
    }

    public static function lastReply( array $args )
    {
        self::guard( 'lastReply' );
        $t = self::topic( self::arg( $args, 0, 'int' ) );
        $r = eZContentObjectTreeNode::subTreeByNodeID( array( 'ClassFilterType' => 'include', 'ClassFilterArray' => array( self::classOf( 'reply' ) ),
            'SortBy' => array( array( 'published', false ), array( 'node_id', false ) ), 'Limit' => 1 ), $t->attribute( 'node_id' ) );
        return self::ok( $r ? expCommerceExport::contentItem( $r[0] ) : null );
    }

    public static function canCreate( array $args )
    {
        self::guard( 'canCreate' );
        $forum = self::item( self::arg( $args, 0, 'int' ), array( self::classOf( 'forum' ) ), 'read' );
        return self::ok( array( 'can' => self::canCreateClass( $forum, self::classOf( 'topic' ) ) ) );
    }

    public static function create( array $args )
    {
        self::guard( 'create' );
        $forum = self::item( self::post( 'forum_node_id', 'int' ), array( self::classOf( 'forum' ) ), 'read' );
        return self::ok( self::encode( self::createChild( $forum, self::classOf( 'topic' ) ) ) );
    }

    public static function edit( array $args )
    {
        self::guard( 'edit' );
        return self::ok( self::encode( self::editNode( self::topic( self::post( 'node_id', 'int' ), 'edit' ) ) ) );
    }

    public static function remove( array $args )
    {
        self::guard( 'remove' );
        return self::ok( self::removeNode( self::topic( self::post( 'node_id', 'int' ), 'remove' ) ) );
    }

    public static function setSticky( array $args )
    {
        self::guard( 'setSticky' );
        $t = self::topic( self::post( 'node_id', 'int' ), 'edit' );
        $field = self::stickyField();
        if ( !isset( self::fieldsOf( self::classOf( 'topic' ) )[$field] ) )
            throw new expServiceException( "The topic class has no sticky attribute '$field'", 422 );
        self::$postData = array( 'fields' => array( $field => self::post( 'sticky', 'bool' ) ? '1' : '0' ) );
        return self::ok( self::encode( self::editNode( $t ) ) );
    }

    public static function move( array $args )
    {
        self::guard( 'move' );
        $t = self::topic( self::post( 'node_id', 'int' ), 'edit' );
        $target = self::item( self::post( 'forum_node_id', 'int' ), array( self::classOf( 'forum' ) ), 'read' );
        return self::ok( self::moveNode( $t, $target ) );
    }

    public static function hide( array $args )
    {
        self::guard( 'hide' );
        return self::ok( self::setHidden( self::topic( self::post( 'node_id', 'int' ) ), true ) );
    }

    public static function unhide( array $args )
    {
        self::guard( 'unhide' );
        return self::ok( self::setHidden( self::topic( self::post( 'node_id', 'int' ) ), false ) );
    }
}
