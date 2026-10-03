<?php
/**
 * ezjscore/call/expnewsletter::<service> - the cjw_newsletter extension: lists, editions, subscriber counts, and a
 * user's own subscriptions (subscribe and unsubscribe act on the logged-in user's own e-mail address only; a new
 * subscription starts pending and follows the extension's confirmation rules). Answers 404 "not available" when
 * the extension is inactive.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expNewsletterServices extends expServiceBase
{
    public static $services = array(
        'available' => array( 'summary' => 'Whether cjw_newsletter is active, and how many lists and newsletter users exist', 'access' => 'public',
            'write' => false, 'args' => array(), 'returns' => 'available, lists, users' ),
        'statuses' => array( 'summary' => 'The subscription status codes and names', 'access' => 'public', 'write' => false,
            'args' => array(), 'returns' => 'map code => name' ),
        'lists' => array( 'summary' => 'The newsletter lists the user may read', 'access' => array( 'content', 'read' ), 'write' => false,
            'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of lists' ),
        'list' => array( 'summary' => 'One newsletter list by node id', 'access' => array( 'content', 'read' ), 'write' => false,
            'args' => array( 'node' => 'int' ), 'returns' => 'list with sender and output formats' ),
        'subscriberscount' => array( 'summary' => 'The number of approved subscribers of a list', 'access' => array( 'content', 'read' ), 'write' => false,
            'args' => array( 'node' => 'int' ), 'returns' => 'count' ),
        'statistics' => array( 'summary' => 'Subscriptions of a list by status', 'access' => array( 'newsletter', 'subscription_list' ), 'write' => false,
            'args' => array( 'node' => 'int' ), 'returns' => 'all, pending, confirmed, approved, removed, bounced, blacklisted' ),
        'editions' => array( 'summary' => 'The editions (issues) below a list node', 'access' => array( 'content', 'read' ), 'write' => false,
            'args' => array( 'node' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of editions' ),
        'edition' => array( 'summary' => 'One edition by node id', 'access' => array( 'content', 'read' ), 'write' => false,
            'args' => array( 'node' => 'int' ), 'returns' => 'edition node' ),
        'mysubscriptions' => array( 'summary' => 'The logged-in user\'s own subscriptions (by their account e-mail)', 'access' => 'user', 'write' => false,
            'args' => array(), 'returns' => 'list of list, status' ),
        'subscribe' => array( 'summary' => 'Subscribes the logged-in user\'s own e-mail to a list', 'access' => 'user', 'write' => true,
            'args' => array( 'node' => 'int' ), 'returns' => 'the subscription; POST format (html or text, optional)' ),
        'unsubscribe' => array( 'summary' => 'Removes the logged-in user\'s own subscription to a list', 'access' => 'user', 'write' => true,
            'args' => array( 'node' => 'int' ), 'returns' => 'the subscription' ),
        'users' => array( 'summary' => 'The newsletter users (administration)', 'access' => array( 'newsletter', 'user_list' ), 'write' => false,
            'args' => array( 'limit' => 'int', 'offset' => 'int', 'search' => 'string' ), 'returns' => 'paged list of users' ),
        'usercount' => array( 'summary' => 'The number of newsletter users', 'access' => array( 'newsletter', 'user_list' ), 'write' => false,
            'args' => array(), 'returns' => 'count' ),
    );

    protected static function need()
    {
        if ( !class_exists( 'CjwNewsletterList' ) || !class_exists( 'CjwNewsletterSubscription' ) )
            throw new expServiceException( 'The cjw_newsletter extension is not available', 404 );
    }

    protected static function names()
    {
        return array( 0 => 'pending', 1 => 'confirmed', 2 => 'approved', 3 => 'removed_self', 4 => 'removed_admin',
                      6 => 'bounced_soft', 7 => 'bounced_hard', 8 => 'blacklisted' );
    }

    protected static function statusName( $code )
    {
        $n = self::names();
        return isset( $n[(int)$code] ) ? $n[(int)$code] : (string)$code;
    }

    protected static function listOf( $nodeId )
    {
        self::need();
        $node = self::node( $nodeId );
        $list = CjwNewsletterList::fetchByListObjectVersion( (int)$node->attribute( 'contentobject_id' ), (int)$node->object()->attribute( 'current_version' ) );
        if ( !is_object( $list ) )
            throw new expServiceException( 'The node is not a newsletter list', 404 );
        return array( $node, $list );
    }

    protected static function exportList( eZContentObjectTreeNode $node, $list )
    {
        return array( 'node_id' => (int)$node->attribute( 'node_id' ), 'object_id' => (int)$node->attribute( 'contentobject_id' ),
                      'name' => $node->attribute( 'name' ), 'url_alias' => $node->attribute( 'url_alias' ),
                      'sender_name' => $list->attribute( 'email_sender_name' ), 'main_siteaccess' => $list->attribute( 'main_siteaccess' ) );
    }

    public static function available( $args )
    {
        self::guard( __FUNCTION__ );
        $has = class_exists( 'CjwNewsletterList' ) && class_exists( 'CjwNewsletterUser' );
        $db = eZDB::instance();
        $count = function ( $t ) use ( $db, $has ) {
            if ( !$has )
                return 0;
            $r = $db->arrayQuery( "SELECT COUNT(*) AS n FROM $t" );
            return $r ? (int)$r[0]['n'] : 0;
        };
        return self::ok( array( 'available' => $has, 'lists' => $count( 'cjwnl_list' ), 'users' => $count( 'cjwnl_user' ) ) );
    }

    public static function statuses( $args )
    {
        self::guard( __FUNCTION__ );
        return self::ok( self::names() );
    }

    public static function lists( $args )
    {
        self::guard( __FUNCTION__ );
        self::need();
        list( $limit, $offset ) = self::paging( $args, 0, 1 );
        $rows = eZDB::instance()->arrayQuery( 'SELECT DISTINCT contentobject_id FROM cjwnl_list ORDER BY contentobject_id' );
        $items = array();
        foreach ( is_array( $rows ) ? $rows : array() as $r )
        {
            $object = eZContentObject::fetch( (int)$r['contentobject_id'] );
            $node = $object ? $object->attribute( 'main_node' ) : null;
            if ( !$node instanceof eZContentObjectTreeNode || !$node->canRead() )
                continue;
            $list = CjwNewsletterList::fetchByListObjectVersion( (int)$object->attribute( 'id' ), (int)$object->attribute( 'current_version' ) );
            if ( is_object( $list ) )
                $items[] = self::exportList( $node, $list );
        }
        return self::page( array_slice( $items, $offset, $limit ), count( $items ), $offset, $limit );
    }

    public static function list( $args )
    {
        self::guard( __FUNCTION__ );
        list( $node, $list ) = self::listOf( self::arg( $args, 0, 'int' ) );
        return self::ok( self::exportList( $node, $list ) );
    }

    public static function subscriberscount( $args )
    {
        self::guard( __FUNCTION__ );
        list( , $list ) = self::listOf( self::arg( $args, 0, 'int' ) );
        return self::ok( array( 'count' => (int)CjwNewsletterSubscription::fetchSubscriptionListByListIdCount( $list, CjwNewsletterSubscription::STATUS_APPROVED ) ) );
    }

    public static function statistics( $args )
    {
        self::guard( __FUNCTION__ );
        list( , $list ) = self::listOf( self::arg( $args, 0, 'int' ) );
        return self::ok( CjwNewsletterSubscription::fetchSubscriptionListStatistic( $list ) );
    }

    public static function editions( $args )
    {
        self::guard( __FUNCTION__ );
        self::need();
        $parent = self::node( self::arg( $args, 0, 'int' ) );
        list( $limit, $offset ) = self::paging( $args, 1, 2 );
        $params = array( 'ClassFilterType' => 'include', 'ClassFilterArray' => array( 'cjw_newsletter_edition' ), 'SortBy' => array( 'published', false ) );
        $total = eZContentObjectTreeNode::subTreeCountByNodeID( $params, (int)$parent->attribute( 'node_id' ) );
        $nodes = eZContentObjectTreeNode::subTreeByNodeID( $params + array( 'Limit' => $limit, 'Offset' => $offset ), (int)$parent->attribute( 'node_id' ) );
        $items = array();
        foreach ( is_array( $nodes ) ? $nodes : array() as $n )
            $items[] = array( 'node_id' => (int)$n->attribute( 'node_id' ), 'name' => $n->attribute( 'name' ), 'url_alias' => $n->attribute( 'url_alias' ),
                              'published' => self::iso( $n->object()->attribute( 'published' ) ) );
        return self::page( $items, $total, $offset, $limit );
    }

    public static function edition( $args )
    {
        self::guard( __FUNCTION__ );
        self::need();
        $n = self::node( self::arg( $args, 0, 'int' ) );
        if ( $n->attribute( 'class_identifier' ) !== 'cjw_newsletter_edition' )
            throw new expServiceException( 'The node is not a newsletter edition', 404 );
        return self::ok( array( 'node_id' => (int)$n->attribute( 'node_id' ), 'name' => $n->attribute( 'name' ), 'url_alias' => $n->attribute( 'url_alias' ),
                                'published' => self::iso( $n->object()->attribute( 'published' ) ), 'parent_node_id' => (int)$n->attribute( 'parent_node_id' ) ) );
    }

    /** The newsletter user of the logged-in user's account e-mail, or null. */
    protected static function ownUser( $create = false )
    {
        self::need();
        $user = eZUser::currentUser();
        $email = $user->attribute( 'email' );
        if ( $email === '' )
            throw new expServiceException( 'The account has no e-mail address', 422 );
        $nl = CjwNewsletterUser::fetchByEmail( $email );
        if ( !is_object( $nl ) && $create )
            $nl = CjwNewsletterUser::createUpdateNewsletterUser( $email, 0, '', '', (int)$user->attribute( 'contentobject_id' ),
                                                                 CjwNewsletterUser::STATUS_PENDING, 'expservices', '', '', '', '' );
        return is_object( $nl ) ? $nl : null;
    }

    protected static function exportSubscription( $s )
    {
        $object = eZContentObject::fetch( (int)$s->attribute( 'list_contentobject_id' ) );
        $node = $object ? $object->attribute( 'main_node' ) : null;
        return array( 'list_object_id' => (int)$s->attribute( 'list_contentobject_id' ), 'list_node_id' => $node ? (int)$node->attribute( 'node_id' ) : 0,
                      'list_name' => $object ? $object->attribute( 'name' ) : '', 'status' => self::statusName( $s->attribute( 'status' ) ),
                      'status_code' => (int)$s->attribute( 'status' ), 'created' => self::iso( $s->attribute( 'created' ) ) );
    }

    public static function mysubscriptions( $args )
    {
        self::guard( __FUNCTION__ );
        $nl = self::ownUser();
        $list = array();
        if ( $nl )
            foreach ( CjwNewsletterSubscription::fetchSubscriptionListByNewsletterUserId( (int)$nl->attribute( 'id' ) ) as $s )
                $list[] = self::exportSubscription( $s );
        return self::ok( $list );
    }

    public static function subscribe( $args )
    {
        self::guard( __FUNCTION__ );
        list( , $list ) = self::listOf( self::arg( $args, 0, 'int' ) );
        $format = self::post( 'format', 'string', 'html' );
        if ( !in_array( $format, array( 'html', 'text' ), true ) )
            throw new expServiceException( 'format is html or text', 422 );
        $nl = self::ownUser( true );
        $sub = CjwNewsletterSubscription::createUpdateNewsletterSubscription( (int)$list->attribute( 'contentobject_id' ), (int)$nl->attribute( 'id' ),
                                                                              array( $format === 'text' ? 1 : 0 ), CjwNewsletterSubscription::STATUS_PENDING );
        return self::ok( self::exportSubscription( $sub ) );
    }

    public static function unsubscribe( $args )
    {
        self::guard( __FUNCTION__ );
        list( , $list ) = self::listOf( self::arg( $args, 0, 'int' ) );
        $nl = self::ownUser();
        if ( !$nl )
            throw new expServiceException( 'You have no newsletter subscription', 404 );
        $sub = CjwNewsletterSubscription::removeSubscriptionByNewsletterUserSelf( (int)$list->attribute( 'contentobject_id' ), (int)$nl->attribute( 'id' ) );
        if ( !is_object( $sub ) )
            throw new expServiceException( 'You are not subscribed to this list', 404 );
        return self::ok( self::exportSubscription( $sub ) );
    }

    public static function users( $args )
    {
        self::guard( __FUNCTION__ );
        self::need();
        if ( !class_exists( 'CjwNewsletterUser' ) )
            throw new expServiceException( 'The cjw_newsletter extension is not available', 404 );
        list( $limit, $offset ) = self::paging( $args, 0, 1 );
        $search = self::arg( $args, 2, 'string', false );
        $items = array();
        foreach ( CjwNewsletterUser::fetchList( $limit, $offset, $search ) as $u )
            $items[] = array( 'id' => (int)$u->attribute( 'id' ), 'email' => $u->attribute( 'email' ), 'status' => self::statusName( $u->attribute( 'status' ) ),
                              'created' => self::iso( $u->attribute( 'created' ) ) );
        return self::page( $items, CjwNewsletterUser::fetchListCount( $search ), $offset, $limit );
    }

    public static function usercount( $args )
    {
        self::guard( __FUNCTION__ );
        self::need();
        return self::ok( array( 'count' => (int)CjwNewsletterUser::fetchListCount() ) );
    }
}
