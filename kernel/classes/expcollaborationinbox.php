<?php
/**
 * File containing the expCollaborationInbox class.
 *
 * The collaboration summary as an inbox: every item of the current user with its state (waiting, approved,
 * denied), its role for the user (approver or author), unread messages, group and subject, filtered by state,
 * role, type and group, counted and paged. The collaboration templates fetch it with
 * fetch( 'collaboration', 'inbox', hash( ... ) ) and fetch( 'collaboration', 'inbox_row', hash( 'item_id', ... ) ).
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expCollaborationInbox
{
    const STATE_WAITING = 'waiting';
    const STATE_APPROVED = 'approved';
    const STATE_DENIED = 'denied';
    const STATE_OPEN = 'open';
    const STATE_CLOSED = 'closed';

    /** The most items of one user that are looked at; the newest first. */
    const MAX_ITEMS = 1000;

    /** @var array filter values that are accepted */
    private static $states = array( 'all', 'waiting', 'approved', 'denied', 'open', 'closed' );
    private static $roles = array( 'all', 'approver', 'author' );

    /**
     * The state of an item: waiting, approved or denied for an approval; open or closed for any other type.
     *
     * An extension's collaboration handler can say the state of its own items: when the handler has a method
     * inboxState( $item ) that returns one of waiting, approved, denied, open or closed, that is the state (an
     * approval type of an extension then shows in the inbox as ezapprove does). Any other answer, or no such
     * method, keeps the state from the item's status.
     *
     * @param eZCollaborationItem $item
     * @return string
     */
    public static function stateOf( $item )
    {
        $type = $item->attribute( 'type_identifier' );
        if ( $type !== 'ezapprove' && is_string( $type ) && $type !== ''
             && in_array( $type, (array)eZCollaborationItemHandler::activeHandlers(), true ) )
        {
            $handler = eZCollaborationItemHandler::instantiate( $type );
            if ( is_object( $handler ) && method_exists( $handler, 'inboxState' ) )
            {
                $state = $handler->inboxState( $item );
                if ( is_string( $state ) && in_array( $state, self::$states, true ) && $state !== 'all' )
                    return $state;
            }
        }
        if ( $type === 'ezapprove' )
        {
            switch ( (int)$item->attribute( 'data_int3' ) )
            {
                case eZApproveCollaborationHandler::STATUS_ACCEPTED:
                    return self::STATE_APPROVED;
                case eZApproveCollaborationHandler::STATUS_DENIED:
                case eZApproveCollaborationHandler::STATUS_DEFERRED:
                    return self::STATE_DENIED;
                default:
                    return self::STATE_WAITING;
            }
        }
        return (int)$item->attribute( 'status' ) === eZCollaborationItem::STATUS_ACTIVE ? self::STATE_OPEN : self::STATE_CLOSED;
    }

    /**
     * The inbox of the current user.
     *
     * Parameters (all optional): status (all|waiting|approved|denied|open|closed), role (all|approver|author),
     * type (a type identifier or 'all'), group_id (0 = every group), offset, limit (default 10).
     *
     * @param array $parameters
     * @return array items (the page), total, counts, types, status, role, type, group_id, offset, limit, user_id
     */
    public static function fetch( $parameters = array() )
    {
        $parameters = array_merge( array( 'status' => 'all', 'role' => 'all', 'type' => 'all', 'group_id' => 0,
                                          'offset' => 0, 'limit' => 10 ), $parameters );
        $status = in_array( $parameters['status'], self::$states, true ) ? $parameters['status'] : 'all';
        $role = in_array( $parameters['role'], self::$roles, true ) ? $parameters['role'] : 'all';
        $type = is_string( $parameters['type'] ) && preg_match( '/^[a-z0-9_]{1,40}$/', $parameters['type'] ) ? $parameters['type'] : 'all';
        $groupID = (int)$parameters['group_id'];
        $offset = max( 0, (int)$parameters['offset'] );
        $limit = max( 1, min( 100, (int)$parameters['limit'] ) );

        $user = eZUser::currentUser();
        $userID = (int)$user->attribute( 'contentobject_id' );

        $all = eZCollaborationItem::fetchList( array( 'status' => array( eZCollaborationItem::STATUS_ACTIVE, eZCollaborationItem::STATUS_INACTIVE ),
                                                      'sort_by' => array( 'modified', false ),
                                                      'offset' => 0, 'limit' => self::MAX_ITEMS ) );
        if ( !is_array( $all ) )
            $all = array();

        $groups = self::groupMap( $userID );
        $unread = self::unreadMap( $userID, $all );

        $counts = array( 'all' => 0, 'waiting' => 0, 'approved' => 0, 'denied' => 0, 'open' => 0, 'closed' => 0,
                         'waiting_for_me' => 0, 'waiting_for_others' => 0, 'unread_items' => 0, 'unread_messages' => 0 );
        $types = array();
        $matching = array();
        foreach ( $all as $item )
        {
            $itemID = (int)$item->attribute( 'id' );
            $state = self::stateOf( $item );
            $isCreator = (int)$item->attribute( 'creator_id' ) === $userID;
            $itemType = $item->attribute( 'type_identifier' );
            $itemGroup = isset( $groups[$itemID] ) ? $groups[$itemID] : 0;
            $itemUnread = isset( $unread[$itemID] ) ? $unread[$itemID] : 0;

            if ( !isset( $types[$itemType] ) )
                $types[$itemType] = self::typeName( $item );
            ++$counts['all'];
            ++$counts[$state];
            if ( $state === self::STATE_WAITING )
                ++$counts[$isCreator ? 'waiting_for_others' : 'waiting_for_me'];
            if ( $itemUnread > 0 )
            {
                ++$counts['unread_items'];
                $counts['unread_messages'] += $itemUnread;
            }

            if ( $status !== 'all' && $state !== $status )
                continue;
            if ( $role === 'approver' && $isCreator )
                continue;
            if ( $role === 'author' && !$isCreator )
                continue;
            if ( $type !== 'all' && $itemType !== $type )
                continue;
            if ( $groupID > 0 && $itemGroup !== $groupID )
                continue;
            $matching[] = array( $item, $state, $isCreator, $itemGroup, $itemUnread );
        }

        $page = array();
        foreach ( array_slice( $matching, $offset, $limit ) as $m )
            $page[] = self::row( $m[0], $m[1], $m[2], $m[3], $m[4], $userID );

        return array( 'items' => $page, 'total' => count( $matching ), 'counts' => $counts, 'types' => $types,
                      'status' => $status, 'role' => $role, 'type' => $type, 'group_id' => $groupID,
                      'offset' => $offset, 'limit' => $limit, 'user_id' => $userID,
                      'main_group_id' => (int)eZCollaborationProfile::instance( $userID )->attribute( 'main_group' ) );
    }

    /**
     * One item as an inbox row, or false when the user is not a participant of it.
     *
     * @param int $itemID
     * @return array|false
     */
    public static function fetchRow( $itemID )
    {
        $item = eZCollaborationItem::fetch( (int)$itemID );
        if ( !$item )
            return false;
        $user = eZUser::currentUser();
        if ( !$item->userIsParticipant( $user ) )
            return false;
        $userID = (int)$user->attribute( 'contentobject_id' );
        $groups = self::groupMap( $userID );
        $unread = self::unreadMap( $userID, array( $item ) );
        $id = (int)$item->attribute( 'id' );
        return self::row( $item, self::stateOf( $item ), (int)$item->attribute( 'creator_id' ) === $userID,
                          isset( $groups[$id] ) ? $groups[$id] : 0, isset( $unread[$id] ) ? $unread[$id] : 0, $userID );
    }

    private static function typeName( $item )
    {
        $handler = $item->attribute( 'handler' );
        if ( $handler )
        {
            $info = $handler->attribute( 'info' );
            if ( isset( $info['type-name'] ) )
                return $info['type-name'];
        }
        return $item->attribute( 'type_identifier' );
    }

    /** @return array collaboration id => group id, for the user */
    private static function groupMap( $userID )
    {
        $map = array();
        $db = eZDB::instance();
        if ( $db->databaseName() === 'mongo' )
        {
            $rows = $db->aggregate( 'ezcollab_item_group_link', array( array( '$match' => array( 'user_id' => (int)$userID ) ) ) );
        }
        else
        {
            $rows = $db->arrayQuery( 'SELECT collaboration_id, group_id FROM ezcollab_item_group_link WHERE user_id=' . (int)$userID );
        }
        foreach ( (array)$rows as $r )
            $map[(int)$r['collaboration_id']] = (int)$r['group_id'];
        return $map;
    }

    /**
     * The unread messages of every item, messages the user wrote himself not counted.
     *
     * @return array collaboration id => number of unread messages
     */
    private static function unreadMap( $userID, $items )
    {
        $map = array();
        if ( !$items )
            return $map;
        $db = eZDB::instance();
        if ( $db->databaseName() === 'mongo' )
        {
            foreach ( $items as $item )
            {
                $status = $item->attribute( 'user_status' );
                $lastRead = $status ? (int)$status->attribute( 'last_read' ) : 0;
                $n = eZCollaborationItemMessageLink::fetchItemCount( array( 'item_id' => $item->attribute( 'id' ),
                                                                            'conditions' => array( 'modified' => array( '>', $lastRead ) ) ) );
                if ( $n )
                    $map[(int)$item->attribute( 'id' )] = (int)$n;
            }
            return $map;
        }
        $sql = 'SELECT l.collaboration_id AS cid, COUNT(*) AS n
                FROM ezcollab_item_message_link l, ezcollab_item_status s
                WHERE s.collaboration_id = l.collaboration_id AND s.user_id = ' . (int)$userID . '
                  AND l.modified > s.last_read AND l.participant_id <> ' . (int)$userID . '
                GROUP BY l.collaboration_id';
        foreach ( $db->arrayQuery( $sql ) as $r )
            $map[(int)$r['cid']] = (int)$r['n'];
        return $map;
    }

    private static function row( $item, $state, $isCreator, $groupID, $unread, $userID )
    {
        $id = (int)$item->attribute( 'id' );
        $row = array( 'id' => $id, 'item' => $item, 'type' => $item->attribute( 'type_identifier' ), 'type_name' => self::typeName( $item ),
                      'state' => $state, 'is_creator' => $isCreator, 'is_approver' => !$isCreator,
                      'created' => (int)$item->attribute( 'created' ), 'modified' => (int)$item->attribute( 'modified' ),
                      'unread_messages' => (int)$unread, 'is_unread' => $unread > 0 || !self::isRead( $item ),
                      'group_id' => $groupID, 'group_title' => '', 'title' => '', 'object_id' => 0, 'version' => 0,
                      'author_id' => (int)$item->attribute( 'creator_id' ), 'author_name' => '',
                      'message_count' => 0, 'last_message' => false, 'url' => 'collaboration/item/full/' . $id );
        if ( $groupID )
        {
            $group = eZCollaborationGroup::fetch( $groupID, $userID );
            if ( $group )
                $row['group_title'] = $group->attribute( 'title' );
        }
        $author = eZContentObject::fetch( $row['author_id'] );
        if ( $author )
            $row['author_name'] = $author->attribute( 'name' );

        if ( $row['type'] === 'ezapprove' )
        {
            $row['object_id'] = (int)$item->attribute( 'data_int1' );
            $row['version'] = (int)$item->attribute( 'data_int2' );
            $row['title'] = self::approvalTitle( $row['object_id'], $row['version'] );
        }
        if ( $row['title'] === '' )
            $row['title'] = (string)$item->attribute( 'title' );

        if ( $item->attribute( 'use_messages' ) )
        {
            $row['message_count'] = (int)eZCollaborationItemMessageLink::fetchItemCount( array( 'item_id' => $id ) );
            if ( $row['message_count'] )
            {
                $links = eZPersistentObject::fetchObjectList( eZCollaborationItemMessageLink::definition(), null,
                                                              array( 'collaboration_id' => $id ),
                                                              array( 'created' => 'desc', 'id' => 'desc' ),
                                                              array( 'offset' => 0, 'limit' => 1 ) );
                if ( $links )
                {
                    $last = $links[0];
                    $message = $last->attribute( 'simple_message' );
                    $speaker = eZContentObject::fetch( (int)$last->attribute( 'participant_id' ) );
                    $row['last_message'] = array( 'text' => $message ? (string)$message->attribute( 'data_text1' ) : '',
                                                  'author' => $speaker ? $speaker->attribute( 'name' ) : '',
                                                  'created' => (int)$last->attribute( 'created' ) );
                }
            }
        }
        return $row;
    }

    private static function isRead( $item )
    {
        $status = $item->attribute( 'user_status' );
        return $status ? (bool)$status->attribute( 'is_read' ) : true;
    }

    /** The title of the content an approval is about: computed from the version's own attributes, as a draft has no stored name. */
    public static function approvalTitle( $objectID, $versionNumber )
    {
        $object = eZContentObject::fetch( $objectID );
        if ( !$object )
            return '';
        $name = '';
        $version = $versionNumber ? $object->version( $versionNumber ) : false;
        if ( $version )
        {
            $class = $object->attribute( 'content_class' );
            if ( $class )
                $name = trim( (string)$class->contentObjectName( $object, $versionNumber, $version->initialLanguageCode() ) );
            if ( $name === '' )
                $name = trim( (string)$version->attribute( 'name' ) );
        }
        if ( $name === '' )
            $name = (string)$object->attribute( 'name' );
        return $name;
    }
}

?>
