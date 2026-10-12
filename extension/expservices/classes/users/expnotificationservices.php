<?php
/**
 * expnotification: the notification subscriptions of the current user (subtree rules, the collaboration types,
 * the digest settings) and the notification queue for administrators. Own data needs the notification/use
 * policy, the queue and other users' subscriptions notification/administrate.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expNotificationServices extends expUsersBase
{
    /**
     * The services (expServiceBase), declared here rather than assigned after the class: a persistent worker
     * (Velocity) puts the statics of a class loaded during a request back to their declared value before the
     * next one, and an assignment after the class would not run again.
     */
    public static $services = array(
        'settings' => array( 'summary' => 'Digest settings, subscription count and collaboration types of the current user', 'access' => array( 'notification', 'use' ), 'write' => false, 'args' => array(), 'returns' => 'digest, subscriptions, collaboration_types' ),
        'digestTypes' => array( 'summary' => 'The digest types', 'access' => array( 'notification', 'use' ), 'write' => false, 'args' => array(), 'returns' => 'id, name' ),
        'setDigest' => array( 'summary' => 'Set the digest settings (POST receive_digest, digest_type, day, time)', 'access' => array( 'notification', 'use' ), 'write' => true, 'args' => array( 'receive_digest' => 'bool', 'digest_type' => 'int', 'day' => 'string', 'time' => 'string' ), 'returns' => 'digest' ),
        'subscriptions' => array( 'summary' => 'The nodes the current user is subscribed to, paged', 'access' => array( 'notification', 'use' ), 'write' => false, 'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged rules' ),
        'subscriptionCount' => array( 'summary' => 'The number of subscriptions', 'access' => array( 'notification', 'use' ), 'write' => false, 'args' => array(), 'returns' => 'count' ),
        'isSubscribed' => array( 'summary' => 'Whether the current user is subscribed to a node', 'access' => array( 'notification', 'use' ), 'write' => false, 'args' => array( 'node' => 'int' ), 'returns' => 'subscribed' ),
        'subscribe' => array( 'summary' => 'Subscribe to a node and its subtree (POST node, use_digest)', 'access' => array( 'notification', 'use' ), 'write' => true, 'args' => array( 'node' => 'int', 'use_digest' => 'bool' ), 'returns' => 'rule' ),
        'unsubscribe' => array( 'summary' => 'Unsubscribe from a node (POST node)', 'access' => array( 'notification', 'use' ), 'write' => true, 'args' => array( 'node' => 'int' ), 'returns' => 'subscribed' ),
        'unsubscribeAll' => array( 'summary' => 'Remove all subscriptions of the current user (POST)', 'access' => array( 'notification', 'use' ), 'write' => true, 'args' => array(), 'returns' => 'removed' ),
        'setDigestForSubscription' => array( 'summary' => 'Turn the digest on or off for one subscription (POST node, use_digest)', 'access' => array( 'notification', 'use' ), 'write' => true, 'args' => array( 'node' => 'int', 'use_digest' => 'bool' ), 'returns' => 'rule' ),
        'collaborationTypes' => array( 'summary' => 'The collaboration notification types the current user receives', 'access' => array( 'notification', 'use' ), 'write' => false, 'args' => array(), 'returns' => 'types' ),
        'subscribeCollaboration' => array( 'summary' => 'Receive notifications of a collaboration type (POST type)', 'access' => array( 'notification', 'use' ), 'write' => true, 'args' => array( 'type' => 'string' ), 'returns' => 'subscribed' ),
        'unsubscribeCollaboration' => array( 'summary' => 'Stop receiving a collaboration type (POST type)', 'access' => array( 'notification', 'use' ), 'write' => true, 'args' => array( 'type' => 'string' ), 'returns' => 'subscribed' ),
        'handlers' => array( 'summary' => 'The notification handlers', 'access' => array( 'notification', 'administrate' ), 'write' => false, 'args' => array(), 'returns' => 'handler names' ),
        'eventTypes' => array( 'summary' => 'The available notification event types', 'access' => array( 'notification', 'administrate' ), 'write' => false, 'args' => array(), 'returns' => 'types' ),
        'subscriptionsOf' => array( 'summary' => 'The subscriptions of a user', 'access' => array( 'notification', 'administrate' ), 'write' => false, 'args' => array( 'id' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged rules' ),
        'subscribeUser' => array( 'summary' => 'Subscribe a user to a node (POST user, node, use_digest)', 'access' => array( 'notification', 'administrate' ), 'write' => true, 'args' => array( 'user' => 'int', 'node' => 'int', 'use_digest' => 'bool' ), 'returns' => 'rule' ),
        'unsubscribeUser' => array( 'summary' => 'Unsubscribe a user from a node (POST user, node)', 'access' => array( 'notification', 'administrate' ), 'write' => true, 'args' => array( 'user' => 'int', 'node' => 'int' ), 'returns' => 'subscribed' ),
        'subscribers' => array( 'summary' => 'The users subscribed to a node', 'access' => array( 'notification', 'administrate' ), 'write' => false, 'args' => array( 'node' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged subscribers' ),
        'events' => array( 'summary' => 'The notification events, newest first, paged', 'access' => array( 'notification', 'administrate' ), 'write' => false, 'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged events' ),
        'queue' => array( 'summary' => 'The size of the notification queue', 'access' => array( 'notification', 'administrate' ), 'write' => false, 'args' => array(), 'returns' => 'counts' ),
    );

    protected static function exportRule( eZSubtreeNotificationRule $rule )
    {
        $node = eZContentObjectTreeNode::fetch( (int)$rule->attribute( 'node_id' ) );
        return array( 'id' => (int)$rule->attribute( 'id' ), 'node_id' => (int)$rule->attribute( 'node_id' ),
                      'name' => $node ? $node->attribute( 'name' ) : null, 'use_digest' => (bool)$rule->attribute( 'use_digest' ),
                      'user_id' => (int)$rule->attribute( 'user_id' ) );
    }

    protected static function rulesOf( $userId )
    {
        return (array)eZSubtreeNotificationRule::fetchList( (int)$userId, true );
    }

    protected static function digest( $userId )
    {
        $s = eZGeneralDigestUserSettings::fetchByUserId( (int)$userId );
        return array( 'receive_digest' => $s ? (bool)$s->attribute( 'receive_digest' ) : false,
                      'digest_type' => $s ? (int)$s->attribute( 'digest_type' ) : eZGeneralDigestUserSettings::TYPE_NONE,
                      'day' => $s ? (string)$s->attribute( 'day' ) : '', 'time' => $s ? (string)$s->attribute( 'time' ) : '' );
    }

    public static function settings( array $a = array() )
    {
        self::guard( 'settings' );
        $id = (int)eZUser::currentUserID();
        $types = array();
        foreach ( (array)eZCollaborationNotificationRule::fetchList( $id ) as $r )
            $types[] = (string)$r->attribute( 'collab_identifier' );
        return self::ok( array( 'digest' => self::digest( $id ), 'subscriptions' => count( self::rulesOf( $id ) ), 'collaboration_types' => $types ) );
    }

    public static function digestTypes( array $a = array() )
    {
        self::guard( 'digestTypes' );
        return self::ok( array( array( 'id' => eZGeneralDigestUserSettings::TYPE_NONE, 'name' => 'none' ), array( 'id' => eZGeneralDigestUserSettings::TYPE_DAILY, 'name' => 'daily' ),
                                array( 'id' => eZGeneralDigestUserSettings::TYPE_WEEKLY, 'name' => 'weekly' ), array( 'id' => eZGeneralDigestUserSettings::TYPE_MONTHLY, 'name' => 'monthly' ) ) );
    }

    public static function setDigest( array $a = array() )
    {
        self::guard( 'setDigest' );
        $id = (int)eZUser::currentUserID();
        $receive = self::post( 'receive_digest', 'bool' );
        $type = self::post( 'digest_type', 'int', eZGeneralDigestUserSettings::TYPE_DAILY );
        $day = self::post( 'day', 'string', '' );
        $time = self::post( 'time', 'string', '' );
        if ( !in_array( $type, array( 0, 1, 2, 3 ), true ) )
            throw new expServiceException( 'digest_type must be 0 none, 1 weekly, 2 monthly or 3 daily', 422 );
        if ( $time !== '' && !preg_match( '/^([01]?\d|2[0-3]):[0-5]\d$/', $time ) )
            throw new expServiceException( 'time must be HH:MM', 422 );
        $s = eZGeneralDigestUserSettings::fetchByUserId( $id );
        if ( !$s instanceof eZGeneralDigestUserSettings )
            $s = eZGeneralDigestUserSettings::create( $id );
        $s->setAttribute( 'receive_digest', $receive ? 1 : 0 );
        $s->setAttribute( 'digest_type', $type );
        $s->setAttribute( 'day', $day );
        $s->setAttribute( 'time', $time );
        $s->store();
        return self::ok( self::digest( $id ) );
    }

    public static function subscriptions( array $a = array() )
    {
        self::guard( 'subscriptions' );
        return self::pageOf( array_map( array( 'expNotificationServices', 'exportRule' ), self::rulesOf( eZUser::currentUserID() ) ), $a, 0, 1 );
    }

    public static function subscriptionCount( array $a = array() )
    {
        self::guard( 'subscriptionCount' );
        return self::ok( array( 'count' => (int)eZSubtreeNotificationRule::fetchListCount( (int)eZUser::currentUserID() ) ) );
    }

    public static function isSubscribed( array $a = array() )
    {
        self::guard( 'isSubscribed' );
        $node = self::arg( $a, 0, 'int' );
        foreach ( self::rulesOf( eZUser::currentUserID() ) as $r )
            if ( (int)$r->attribute( 'node_id' ) === $node )
                return self::ok( array( 'node_id' => $node, 'subscribed' => true, 'use_digest' => (bool)$r->attribute( 'use_digest' ) ) );
        return self::ok( array( 'node_id' => $node, 'subscribed' => false, 'use_digest' => false ) );
    }

    public static function subscribe( array $a = array() )
    {
        self::guard( 'subscribe' );
        return self::addRule( (int)eZUser::currentUserID(), self::post( 'node', 'int' ), self::post( 'use_digest', 'bool', false ) );
    }

    protected static function addRule( $userId, $nodeId, $digest )
    {
        $node = self::node( $nodeId, 'read' );
        foreach ( self::rulesOf( $userId ) as $r )
            if ( (int)$r->attribute( 'node_id' ) === (int)$node->attribute( 'node_id' ) )
                throw new expServiceException( 'Already subscribed to this node', 409 );
        $rule = eZSubtreeNotificationRule::create( (int)$node->attribute( 'node_id' ), $userId, $digest ? 1 : 0 );
        $rule->store();
        return self::ok( self::exportRule( $rule ) );
    }

    public static function unsubscribe( array $a = array() )
    {
        self::guard( 'unsubscribe' );
        $node = self::post( 'node', 'int' );
        $found = false;
        foreach ( self::rulesOf( eZUser::currentUserID() ) as $r )
            if ( (int)$r->attribute( 'node_id' ) === $node )
                $found = true;
        if ( !$found )
            throw new expServiceException( 'Not subscribed to this node', 404 );
        eZSubtreeNotificationRule::removeByNodeAndUserID( (int)eZUser::currentUserID(), $node );
        return self::ok( array( 'node_id' => $node, 'subscribed' => false ) );
    }

    public static function unsubscribeAll( array $a = array() )
    {
        self::guard( 'unsubscribeAll' );
        $n = count( self::rulesOf( eZUser::currentUserID() ) );
        eZSubtreeNotificationRule::removeByUserID( (int)eZUser::currentUserID() );
        return self::ok( array( 'removed' => $n ) );
    }

    public static function setDigestForSubscription( array $a = array() )
    {
        self::guard( 'setDigestForSubscription' );
        $node = self::post( 'node', 'int' );
        $use = self::post( 'use_digest', 'bool' );
        foreach ( self::rulesOf( eZUser::currentUserID() ) as $r )
            if ( (int)$r->attribute( 'node_id' ) === $node )
            {
                $r->setAttribute( 'use_digest', $use ? 1 : 0 );
                $r->store();
                return self::ok( self::exportRule( $r ) );
            }
        throw new expServiceException( 'Not subscribed to this node', 404 );
    }

    public static function collaborationTypes( array $a = array() )
    {
        self::guard( 'collaborationTypes' );
        $out = array();
        foreach ( (array)eZCollaborationNotificationRule::fetchList( (int)eZUser::currentUserID() ) as $r )
            $out[] = (string)$r->attribute( 'collab_identifier' );
        return self::ok( $out );
    }

    public static function subscribeCollaboration( array $a = array() )
    {
        self::guard( 'subscribeCollaboration' );
        $ident = self::post( 'type', 'string' );
        if ( !self::knownCollaboration( $ident ) )
            throw new expServiceException( "Unknown collaboration type '$ident'", 422 );
        $id = (int)eZUser::currentUserID();
        eZCollaborationNotificationRule::removeByIdentifier( $ident, $id );
        $rule = eZCollaborationNotificationRule::create( $ident, $id );
        $rule->store();
        return self::ok( array( 'type' => $ident, 'subscribed' => true ) );
    }

    public static function unsubscribeCollaboration( array $a = array() )
    {
        self::guard( 'unsubscribeCollaboration' );
        $ident = self::post( 'type', 'string' );
        eZCollaborationNotificationRule::removeByIdentifier( $ident, (int)eZUser::currentUserID() );
        return self::ok( array( 'type' => $ident, 'subscribed' => false ) );
    }

    protected static function knownCollaboration( $ident )
    {
        $ini = eZINI::instance( 'collaboration.ini' );
        return in_array( $ident, (array)$ini->variable( 'HandlerSettings', 'Active' ), true );
    }

    public static function handlers( array $a = array() )
    {
        self::guard( 'handlers' );
        return self::ok( array_keys( eZNotificationEventFilter::availableHandlers() ) );
    }

    public static function eventTypes( array $a = array() )
    {
        self::guard( 'eventTypes' );
        return self::ok( array_values( (array)eZINI::instance( 'notification.ini' )->variable( 'NotificationEventHandlerSettings', 'AvailableNotificationEventTypes' ) ) );
    }

    public static function subscriptionsOf( array $a = array() )
    {
        self::guard( 'subscriptionsOf' );
        $user = self::user( self::arg( $a, 0, 'int' ) );
        return self::pageOf( array_map( array( 'expNotificationServices', 'exportRule' ), self::rulesOf( $user->attribute( 'contentobject_id' ) ) ), $a, 1, 2 );
    }

    public static function subscribeUser( array $a = array() )
    {
        self::guard( 'subscribeUser' );
        $user = self::user( self::post( 'user', 'int' ) );
        return self::addRule( (int)$user->attribute( 'contentobject_id' ), self::post( 'node', 'int' ), self::post( 'use_digest', 'bool', false ) );
    }

    public static function unsubscribeUser( array $a = array() )
    {
        self::guard( 'unsubscribeUser' );
        $user = self::user( self::post( 'user', 'int' ) );
        $node = self::post( 'node', 'int' );
        eZSubtreeNotificationRule::removeByNodeAndUserID( (int)$user->attribute( 'contentobject_id' ), $node );
        return self::ok( array( 'user' => (int)$user->attribute( 'contentobject_id' ), 'node_id' => $node, 'subscribed' => false ) );
    }

    public static function subscribers( array $a = array() )
    {
        self::guard( 'subscribers' );
        $node = self::arg( $a, 0, 'int' );
        $rows = eZDB::instance()->arrayQuery( 'SELECT id, user_id, use_digest FROM ezsubtree_notification_rule WHERE node_id = ' . $node . ' ORDER BY id' );
        $out = array();
        foreach ( $rows as $r )
        {
            $o = eZContentObject::fetch( (int)$r['user_id'] );
            $out[] = array( 'rule_id' => (int)$r['id'], 'user_id' => (int)$r['user_id'], 'name' => $o ? $o->attribute( 'name' ) : null, 'use_digest' => (bool)$r['use_digest'] );
        }
        return self::pageOf( $out, $a, 1, 2 );
    }

    public static function events( array $a = array() )
    {
        self::guard( 'events' );
        list( $limit, $offset ) = self::paging( $a, 0, 1 );
        $total = (int)self::value( 'SELECT COUNT(*) AS c FROM eznotificationevent' );
        $rows = eZDB::instance()->arrayQuery( 'SELECT id, event_type_string, status, data_int1 FROM eznotificationevent ORDER BY id DESC', array( 'limit' => $limit, 'offset' => $offset ) );
        $out = array();
        foreach ( $rows as $r )
            $out[] = array( 'id' => (int)$r['id'], 'type' => $r['event_type_string'], 'handled' => (int)$r['status'] === eZNotificationEvent::STATUS_HANDLED, 'data_int1' => (int)$r['data_int1'] );
        return self::page( $out, $total, $offset, $limit );
    }

    public static function queue( array $a = array() )
    {
        self::guard( 'queue' );
        return self::ok( array( 'events' => (int)self::value( 'SELECT COUNT(*) AS c FROM eznotificationevent' ),
                                'unhandled_events' => (int)self::value( 'SELECT COUNT(*) AS c FROM eznotificationevent WHERE status = ' . (int)eZNotificationEvent::STATUS_CREATED ),
                                'collections' => (int)self::value( 'SELECT COUNT(*) AS c FROM eznotificationcollection' ),
                                'items_waiting' => (int)self::value( 'SELECT COUNT(*) AS c FROM eznotificationcollection_item WHERE send_date = 0' ),
                                'subtree_rules' => (int)self::value( 'SELECT COUNT(*) AS c FROM ezsubtree_notification_rule' ) ) );
    }

    protected static function value( $sql )
    {
        $r = eZDB::instance()->arrayQuery( $sql );
        return isset( $r[0]['c'] ) ? $r[0]['c'] : 0;
    }
}
