<?php
/**
 * ezjscore/call/expreply::<method>: the replies of a topic (content objects of the reply class below a topic).
 * Policies of the content module decide who may write, edit and remove; moderators hide and reveal.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expReplyServices extends expCommunityBase
{
    public static $services = array(
        'list' => array( 'summary' => 'The replies of a topic, oldest first', 'access' => 'public', 'write' => false,
            'args' => array( 'topic_node_id' => 'int', 'limit' => 'int', 'offset' => 'int', 'newest_first' => 'bool' ), 'returns' => 'paged list of replies' ),
        'view' => array( 'summary' => 'One reply', 'access' => 'public', 'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => 'reply' ),
        'count' => array( 'summary' => 'How many replies a topic has', 'access' => 'public', 'write' => false, 'args' => array( 'topic_node_id' => 'int' ), 'returns' => 'count' ),
        'latest' => array( 'summary' => 'The latest replies, everywhere or below a node', 'access' => 'public', 'write' => false, 'args' => array( 'limit' => 'int', 'parent_node_id' => 'int' ), 'returns' => 'list of replies' ),
        'byUser' => array( 'summary' => 'The replies a user wrote', 'access' => 'public', 'write' => false, 'args' => array( 'user_id' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of replies' ),
        'mine' => array( 'summary' => 'The replies the logged-in user wrote', 'access' => 'user', 'write' => false, 'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of replies' ),
        'search' => array( 'summary' => 'Words in reply subjects and texts', 'access' => 'public', 'write' => false,
            'args' => array( 'text' => 'string', 'topic_node_id' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of replies' ),
        'quote' => array( 'summary' => 'A reply as a quote to start an answer from: "Re: subject" and the quoted text', 'access' => 'public', 'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => 'subject, message' ),
        'create' => array( 'summary' => 'Writes a reply to a topic (fields subject, message as JSON or POST fields)', 'access' => array( 'content', 'create' ), 'write' => true,
            'args' => array( 'topic_node_id' => 'int POST', 'fields' => 'json POST' ), 'returns' => 'the reply' ),
        'edit' => array( 'summary' => 'Changes a reply', 'access' => array( 'content', 'edit' ), 'write' => true, 'args' => array( 'node_id' => 'int POST', 'fields' => 'json POST' ), 'returns' => 'the reply' ),
        'remove' => array( 'summary' => 'Removes a reply to the trash', 'access' => array( 'content', 'remove' ), 'write' => true, 'args' => array( 'node_id' => 'int POST' ), 'returns' => 'removed node' ),
        'hide' => array( 'summary' => 'Hides a reply (moderation)', 'access' => array( 'content', 'hide' ), 'write' => true, 'args' => array( 'node_id' => 'int POST' ), 'returns' => 'the reply' ),
        'unhide' => array( 'summary' => 'Shows a hidden reply again', 'access' => array( 'content', 'hide' ), 'write' => true, 'args' => array( 'node_id' => 'int POST' ), 'returns' => 'the reply' ),
    );

    protected static function reply( $id, $fn = 'read' )
    {
        return self::item( $id, array( self::classOf( 'reply' ) ), $fn );
    }

    protected static function topic( $id )
    {
        return self::item( $id, array( self::classOf( 'topic' ) ), 'read' );
    }

    public static function list( array $args )
    {
        self::guard( 'list' );
        $t = self::topic( self::arg( $args, 0, 'int' ) );
        return self::children( $t, array( self::classOf( 'reply' ) ), $args, 1, 2, self::arg( $args, 3, 'bool', false ), 0 );
    }

    public static function view( array $args )
    {
        self::guard( 'view' );
        return self::ok( expCommerceExport::contentItem( self::reply( self::arg( $args, 0, 'int' ) ) ) );
    }

    public static function count( array $args )
    {
        self::guard( 'count' );
        $t = self::topic( self::arg( $args, 0, 'int' ) );
        return self::ok( array( 'count' => (int)eZContentObjectTreeNode::subTreeCountByNodeID( array( 'ClassFilterType' => 'include', 'ClassFilterArray' => array( self::classOf( 'reply' ) ) ), $t->attribute( 'node_id' ) ) ) );
    }

    public static function latest( array $args )
    {
        self::guard( 'latest' );
        $limit = min( self::arg( $args, 0, 'int', 10 ), 100 );
        $parent = self::arg( $args, 1, 'int', self::root() );
        self::node( $parent, 'read' );
        $items = array();
        foreach ( (array)eZContentObjectTreeNode::subTreeByNodeID( array( 'ClassFilterType' => 'include', 'ClassFilterArray' => array( self::classOf( 'reply' ) ),
            'SortBy' => array( array( 'published', false ), array( 'node_id', false ) ), 'Limit' => $limit ), $parent ) as $n )
            $items[] = expCommerceExport::contentItem( $n );
        return self::ok( $items );
    }

    public static function byUser( array $args )
    {
        self::guard( 'byUser' );
        return self::ownedBy( array( self::classOf( 'reply' ) ), self::arg( $args, 0, 'int' ), $args, 1, 2 );
    }

    public static function mine( array $args )
    {
        self::guard( 'mine' );
        return self::ownedBy( array( self::classOf( 'reply' ) ), eZUser::currentUser()->attribute( 'contentobject_id' ), $args, 0, 1 );
    }

    public static function search( array $args )
    {
        self::guard( 'search' );
        $parent = self::arg( $args, 1, 'int', self::root() );
        self::node( $parent, 'read' );
        return self::searchIn( array( self::classOf( 'reply' ) ), self::arg( $args, 0, 'string' ), $parent, $args, 2, 3 );
    }

    public static function quote( array $args )
    {
        self::guard( 'quote' );
        $i = expCommerceExport::contentItem( self::reply( self::arg( $args, 0, 'int' ) ) );
        $subject = isset( $i['fields']['subject'] ) ? $i['fields']['subject'] : $i['name'];
        $message = isset( $i['fields']['message'] ) ? $i['fields']['message'] : '';
        $author = $i['owner_name'] ?: 'unknown';
        return self::ok( array( 'subject' => preg_match( '/^Re:/i', $subject ) ? $subject : 'Re: ' . $subject,
            'message' => '[quote=' . $author . "]\n" . $message . "\n[/quote]\n" ) );
    }

    public static function create( array $args )
    {
        self::guard( 'create' );
        $t = self::topic( self::post( 'topic_node_id', 'int' ) );
        return self::ok( expCommerceExport::contentItem( self::createChild( $t, self::classOf( 'reply' ) ) ) );
    }

    public static function edit( array $args )
    {
        self::guard( 'edit' );
        return self::ok( expCommerceExport::contentItem( self::editNode( self::reply( self::post( 'node_id', 'int' ), 'edit' ) ) ) );
    }

    public static function remove( array $args )
    {
        self::guard( 'remove' );
        return self::ok( self::removeNode( self::reply( self::post( 'node_id', 'int' ), 'remove' ) ) );
    }

    public static function hide( array $args )
    {
        self::guard( 'hide' );
        return self::ok( self::setHidden( self::reply( self::post( 'node_id', 'int' ) ), true ) );
    }

    public static function unhide( array $args )
    {
        self::guard( 'unhide' );
        return self::ok( self::setHidden( self::reply( self::post( 'node_id', 'int' ) ), false ) );
    }
}
