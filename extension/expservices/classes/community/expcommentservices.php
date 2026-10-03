<?php
/**
 * ezjscore/call/expcomment::<method>: comments on any content (content objects of the comment class below the
 * commented node) and the reviews of products (the review class, with a rating). The content policies decide who
 * may write, edit and remove; moderators hide and reveal.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expCommentServices extends expCommunityBase
{
    public static $services = array(
        'list' => array( 'summary' => 'The comments below a node, oldest first or newest first', 'access' => 'public', 'write' => false,
            'args' => array( 'node_id' => 'int', 'limit' => 'int', 'offset' => 'int', 'newest_first' => 'bool' ), 'returns' => 'paged list of comments' ),
        'view' => array( 'summary' => 'One comment', 'access' => 'public', 'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => 'comment' ),
        'count' => array( 'summary' => 'How many comments a node has', 'access' => 'public', 'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => 'count' ),
        'latest' => array( 'summary' => 'The latest comments, everywhere or below a node', 'access' => 'public', 'write' => false, 'args' => array( 'limit' => 'int', 'parent_node_id' => 'int' ), 'returns' => 'list of comments' ),
        'byUser' => array( 'summary' => 'The comments a user wrote', 'access' => 'public', 'write' => false, 'args' => array( 'user_id' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of comments' ),
        'mine' => array( 'summary' => 'The comments the logged-in user wrote', 'access' => 'user', 'write' => false, 'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of comments' ),
        'search' => array( 'summary' => 'Words in comment subjects, authors and texts', 'access' => 'public', 'write' => false,
            'args' => array( 'text' => 'string', 'parent_node_id' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of comments' ),
        'hidden' => array( 'summary' => 'The hidden comments below a node (moderation queue)', 'access' => array( 'content', 'hide' ), 'write' => false,
            'args' => array( 'parent_node_id' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of hidden comments' ),
        'canComment' => array( 'summary' => 'Whether the current user may comment on a node', 'access' => 'public', 'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => 'can' ),
        'create' => array( 'summary' => 'Comments on a node (fields subject, author, message as JSON or POST fields)', 'access' => array( 'content', 'create' ), 'write' => true,
            'args' => array( 'node_id' => 'int POST', 'fields' => 'json POST' ), 'returns' => 'the comment' ),
        'edit' => array( 'summary' => 'Changes a comment', 'access' => array( 'content', 'edit' ), 'write' => true, 'args' => array( 'node_id' => 'int POST', 'fields' => 'json POST' ), 'returns' => 'the comment' ),
        'remove' => array( 'summary' => 'Removes a comment to the trash', 'access' => array( 'content', 'remove' ), 'write' => true, 'args' => array( 'node_id' => 'int POST' ), 'returns' => 'removed node' ),
        'hide' => array( 'summary' => 'Hides a comment (moderation)', 'access' => array( 'content', 'hide' ), 'write' => true, 'args' => array( 'node_id' => 'int POST' ), 'returns' => 'the comment' ),
        'unhide' => array( 'summary' => 'Shows a hidden comment again', 'access' => array( 'content', 'hide' ), 'write' => true, 'args' => array( 'node_id' => 'int POST' ), 'returns' => 'the comment' ),
        'reviews' => array( 'summary' => 'The reviews of a product with the average rating', 'access' => 'public', 'write' => false,
            'args' => array( 'node_id' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of reviews, meta average and count' ),
        'ratingSummary' => array( 'summary' => 'Average rating and the count per star of a product', 'access' => 'public', 'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => 'average, count, stars' ),
        'addReview' => array( 'summary' => 'Reviews a product (fields title, rating 1-5, author, body)', 'access' => array( 'content', 'create' ), 'write' => true,
            'args' => array( 'node_id' => 'int POST', 'fields' => 'json POST' ), 'returns' => 'the review' ),
    );

    protected static function comment( $id, $fn = 'read' )
    {
        return self::item( $id, array( self::classOf( 'comment' ), self::classOf( 'review' ) ), $fn );
    }

    public static function list( array $args )
    {
        self::guard( 'list' );
        $n = self::node( self::arg( $args, 0, 'int' ), 'read' );
        return self::children( $n, array( self::classOf( 'comment' ) ), $args, 1, 2, self::arg( $args, 3, 'bool', false ), 1 );
    }

    public static function view( array $args )
    {
        self::guard( 'view' );
        return self::ok( expCommerceExport::contentItem( self::comment( self::arg( $args, 0, 'int' ) ) ) );
    }

    public static function count( array $args )
    {
        self::guard( 'count' );
        $n = self::node( self::arg( $args, 0, 'int' ), 'read' );
        return self::ok( array( 'count' => (int)eZContentObjectTreeNode::subTreeCountByNodeID( array( 'ClassFilterType' => 'include', 'ClassFilterArray' => array( self::classOf( 'comment' ) ),
            'Depth' => 1, 'DepthOperator' => 'eq' ), $n->attribute( 'node_id' ) ) ) );
    }

    public static function latest( array $args )
    {
        self::guard( 'latest' );
        $limit = min( self::arg( $args, 0, 'int', 10 ), 100 );
        $parent = self::arg( $args, 1, 'int', self::root() );
        self::node( $parent, 'read' );
        $items = array();
        foreach ( (array)eZContentObjectTreeNode::subTreeByNodeID( array( 'ClassFilterType' => 'include', 'ClassFilterArray' => array( self::classOf( 'comment' ) ),
            'SortBy' => array( array( 'published', false ), array( 'node_id', false ) ), 'Limit' => $limit ), $parent ) as $n )
            $items[] = expCommerceExport::contentItem( $n );
        return self::ok( $items );
    }

    public static function byUser( array $args )
    {
        self::guard( 'byUser' );
        return self::ownedBy( array( self::classOf( 'comment' ) ), self::arg( $args, 0, 'int' ), $args, 1, 2 );
    }

    public static function mine( array $args )
    {
        self::guard( 'mine' );
        return self::ownedBy( array( self::classOf( 'comment' ) ), eZUser::currentUser()->attribute( 'contentobject_id' ), $args, 0, 1 );
    }

    public static function search( array $args )
    {
        self::guard( 'search' );
        $parent = self::arg( $args, 1, 'int', self::root() );
        self::node( $parent, 'read' );
        return self::searchIn( array( self::classOf( 'comment' ) ), self::arg( $args, 0, 'string' ), $parent, $args, 2, 3 );
    }

    public static function hidden( array $args )
    {
        self::guard( 'hidden' );
        $parent = self::arg( $args, 0, 'int', self::root() );
        self::node( $parent, 'read' );
        $nodes = eZContentObjectTreeNode::subTreeByNodeID( array( 'ClassFilterType' => 'include', 'ClassFilterArray' => array( self::classOf( 'comment' ), self::classOf( 'review' ) ),
            'IgnoreVisibility' => true, 'SortBy' => array( array( 'published', false ), array( 'node_id', false ) ), 'Limit' => 1000 ), $parent );
        $items = array();
        foreach ( (array)$nodes as $n )
            if ( $n->attribute( 'is_hidden' ) )
                $items[] = expCommerceExport::contentItem( $n );
        return self::pageOf( $items, $args, 1, 2 );
    }

    public static function canComment( array $args )
    {
        self::guard( 'canComment' );
        $n = self::node( self::arg( $args, 0, 'int' ), 'read' );
        return self::ok( array( 'can' => self::canCreateClass( $n, self::classOf( 'comment' ) ) ) );
    }

    public static function create( array $args )
    {
        self::guard( 'create' );
        $n = self::node( self::post( 'node_id', 'int' ), 'read' );
        return self::ok( expCommerceExport::contentItem( self::createChild( $n, self::classOf( 'comment' ) ) ) );
    }

    public static function edit( array $args )
    {
        self::guard( 'edit' );
        return self::ok( expCommerceExport::contentItem( self::editNode( self::comment( self::post( 'node_id', 'int' ), 'edit' ) ) ) );
    }

    public static function remove( array $args )
    {
        self::guard( 'remove' );
        return self::ok( self::removeNode( self::comment( self::post( 'node_id', 'int' ), 'remove' ) ) );
    }

    public static function hide( array $args )
    {
        self::guard( 'hide' );
        return self::ok( self::setHidden( self::comment( self::post( 'node_id', 'int' ) ), true ) );
    }

    public static function unhide( array $args )
    {
        self::guard( 'unhide' );
        return self::ok( self::setHidden( self::comment( self::post( 'node_id', 'int' ) ), false ) );
    }

    protected static function allReviews( eZContentObjectTreeNode $product )
    {
        $items = array();
        foreach ( (array)eZContentObjectTreeNode::subTreeByNodeID( array( 'ClassFilterType' => 'include', 'ClassFilterArray' => array( self::classOf( 'review' ) ),
            'SortBy' => array( array( 'published', false ), array( 'node_id', false ) ), 'Depth' => 1, 'DepthOperator' => 'eq', 'Limit' => 1000 ), $product->attribute( 'node_id' ) ) as $n )
            $items[] = expCommerceExport::contentItem( $n );
        return $items;
    }

    protected static function stars( array $items )
    {
        $stars = array( 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0 );
        $sum = 0;
        $n = 0;
        foreach ( $items as $i )
        {
            $r = isset( $i['fields']['rating'] ) ? (int)$i['fields']['rating'] : 0;
            if ( $r >= 1 && $r <= 5 )
            {
                $stars[$r]++;
                $sum += $r;
                $n++;
            }
        }
        return array( 'average' => $n ? round( $sum / $n, 2 ) : null, 'count' => $n, 'stars' => $stars );
    }

    public static function reviews( array $args )
    {
        self::guard( 'reviews' );
        $p = self::node( self::arg( $args, 0, 'int' ), 'read' );
        $items = self::allReviews( $p );
        $s = self::stars( $items );
        list( $limit, $offset ) = self::paging( $args, 1, 2 );
        $r = self::page( array_slice( $items, $offset, $limit ), count( $items ), $offset, $limit );
        $r['meta'] = array_merge( (array)$r['meta'], array( 'average' => $s['average'], 'rated' => $s['count'] ) );
        return $r;
    }

    public static function ratingSummary( array $args )
    {
        self::guard( 'ratingSummary' );
        return self::ok( self::stars( self::allReviews( self::node( self::arg( $args, 0, 'int' ), 'read' ) ) ) );
    }

    public static function addReview( array $args )
    {
        self::guard( 'addReview' );
        $p = self::node( self::post( 'node_id', 'int' ), 'read' );
        $fields = self::post( 'fields', 'json', array() );
        if ( isset( $fields['rating'] ) && ( !is_numeric( $fields['rating'] ) || $fields['rating'] < 1 || $fields['rating'] > 5 ) )
            throw new expServiceException( 'The rating is 1 to 5', 422 );
        return self::ok( expCommerceExport::contentItem( self::createChild( $p, self::classOf( 'review' ) ) ) );
    }
}
