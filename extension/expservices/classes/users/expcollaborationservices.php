<?php
/**
 * expcollaboration: the collaboration items of the current user (approvals and other handler items), their
 * messages, participants and groups, and the approve and deny decisions where the item's workflow allows them.
 * An item is only visible to its participants; to everybody else it does not exist (404).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expCollaborationServices extends expUsersBase
{
    public static $services = array();

    protected static function me()
    {
        return (int)eZUser::currentUserID();
    }

    /** @return eZCollaborationItem the item, for a participant only */
    protected static function item( $id )
    {
        $item = eZCollaborationItem::fetch( (int)$id );
        if ( !$item instanceof eZCollaborationItem || !$item->userIsParticipant( eZUser::currentUser() ) )
            throw new expServiceException( "Collaboration item $id does not exist", 404 );
        return $item;
    }

    protected static function scalars( $content )
    {
        $out = array();
        if ( is_array( $content ) )
            foreach ( $content as $k => $v )
                if ( is_scalar( $v ) || $v === null )
                    $out[$k] = $v;
        return (object)$out;
    }

    protected static function exportItem( eZCollaborationItem $item )
    {
        $status = $item->userStatus();
        $statusNames = array( eZCollaborationItem::STATUS_ACTIVE => 'active', eZCollaborationItem::STATUS_INACTIVE => 'inactive', eZCollaborationItem::STATUS_ARCHIVE => 'archive' );
        return array( 'id' => (int)$item->attribute( 'id' ), 'type' => $item->attribute( 'type_identifier' ), 'title' => (string)$item->title(),
                      'status' => isset( $statusNames[(int)$item->attribute( 'status' )] ) ? $statusNames[(int)$item->attribute( 'status' )] : 'unknown',
                      'creator_id' => (int)$item->attribute( 'creator_id' ), 'is_creator' => (bool)$item->isCreator(),
                      'created' => self::iso( $item->attribute( 'created' ) ), 'modified' => self::iso( $item->attribute( 'modified' ) ),
                      'is_active' => $status ? (bool)$status->attribute( 'is_active' ) : true,
                      'unread' => $status ? (int)$status->attribute( 'last_read' ) < (int)$item->attribute( 'modified' ) : true,
                      'message_count' => (int)$item->messageCount(), 'unread_messages' => (int)$item->unreadMessageCount(),
                      'content' => self::scalars( $item->content() ) );
    }

    protected static function exportCollabGroup( eZCollaborationGroup $g )
    {
        return array( 'id' => (int)$g->attribute( 'id' ), 'title' => $g->attribute( 'title' ), 'parent_id' => (int)$g->attribute( 'parent_group_id' ),
                      'depth' => (int)$g->attribute( 'depth' ), 'is_open' => (bool)$g->attribute( 'is_open' ) );
    }

    protected static function group( $id )
    {
        $g = eZCollaborationGroup::fetch( (int)$id, self::me() );
        if ( !$g instanceof eZCollaborationGroup )
            throw new expServiceException( "Collaboration group $id does not exist", 404 );
        return $g;
    }

    protected static function filters( array $a, $first )
    {
        $p = array();
        $status = self::arg( $a, $first, 'string', '' );
        if ( $status !== '' )
        {
            $map = array( 'active' => eZCollaborationItem::STATUS_ACTIVE, 'inactive' => eZCollaborationItem::STATUS_INACTIVE, 'archive' => eZCollaborationItem::STATUS_ARCHIVE );
            if ( !isset( $map[$status] ) )
                throw new expServiceException( 'status must be active, inactive or archive', 400 );
            $p['status'] = array( $map[$status] );
        }
        $read = self::arg( $a, $first + 1, 'string', '' );
        if ( $read !== '' )
            $p['is_read'] = self::cast( $read, 'bool', 'is_read' );
        $group = self::arg( $a, $first + 2, 'int', 0 );
        if ( $group > 0 )
            $p['parent_group_id'] = $group;
        return $p;
    }

    public static function items( array $a = array() )
    {
        self::guard( 'items' );
        list( $limit, $offset ) = self::paging( $a, 0, 1 );
        $p = self::filters( $a, 2 );
        $total = (int)eZCollaborationItem::fetchListCount( $p );
        $items = (array)eZCollaborationItem::fetchList( $p + array( 'limit' => $limit, 'offset' => $offset ) );
        return self::page( array_map( array( 'expCollaborationServices', 'exportItem' ), $items ), $total, $offset, $limit );
    }

    public static function itemCount( array $a = array() )
    {
        self::guard( 'itemCount' );
        return self::ok( array( 'count' => (int)eZCollaborationItem::fetchListCount( self::filters( $a, 0 ) ) ) );
    }

    public static function summary( array $a = array() )
    {
        self::guard( 'summary' );
        return self::ok( array( 'active' => (int)eZCollaborationItem::fetchListCount( array( 'status' => array( eZCollaborationItem::STATUS_ACTIVE ) ) ),
                                'unread' => (int)eZCollaborationItem::fetchListCount( array( 'is_read' => false ) ),
                                'archive' => (int)eZCollaborationItem::fetchListCount( array( 'status' => array( eZCollaborationItem::STATUS_ARCHIVE ) ) ),
                                'pending_approvals' => count( self::pendingItems() ) ) );
    }


    public static function fetch( array $a = array() )
    {
        self::guard( 'fetch' );
        return self::ok( self::exportItem( self::item( self::arg( $a, 0, 'int' ) ) ) );
    }

    public static function messages( array $a = array() )
    {
        self::guard( 'messages' );
        $item = self::item( self::arg( $a, 0, 'int' ) );
        $out = array();
        foreach ( (array)eZCollaborationItemMessageLink::fetchItemList( array( 'item_id' => (int)$item->attribute( 'id' ) ) ) as $link )
        {
            $m = $link->simpleMessage();
            $author = eZContentObject::fetch( (int)$link->attribute( 'participant_id' ) );
            $out[] = array( 'id' => (int)$link->attribute( 'message_id' ), 'type' => (int)$link->attribute( 'message_type' ),
                            'text' => $m ? (string)$m->attribute( 'data_text1' ) : '', 'author_id' => (int)$link->attribute( 'participant_id' ),
                            'author' => $author ? $author->attribute( 'name' ) : null, 'created' => self::iso( $link->attribute( 'created' ) ) );
        }
        return self::pageOf( $out, $a, 1, 2 );
    }

    public static function messageCount( array $a = array() )
    {
        self::guard( 'messageCount' );
        $item = self::item( self::arg( $a, 0, 'int' ) );
        return self::ok( array( 'count' => (int)$item->messageCount(), 'unread' => (int)$item->unreadMessageCount() ) );
    }

    public static function addMessage( array $a = array() )
    {
        self::guard( 'addMessage' );
        $item = self::item( self::post( 'item', 'int' ) );
        $text = trim( self::post( 'text', 'string' ) );
        if ( $text === '' || strlen( $text ) > 65000 )
            throw new expServiceException( 'The message needs 1 to 65000 characters', 422 );
        if ( !$item->useMessages() )
            throw new expServiceException( 'This kind of item takes no messages', 409 );
        $isApprove = $item->attribute( 'type_identifier' ) === 'ezapprove';
        $message = eZCollaborationSimpleMessage::create( $isApprove ? 'ezapprove_comment' : $item->attribute( 'type_identifier' ) . '_comment', $text );
        $message->store();
        $link = eZCollaborationItemMessageLink::addMessage( $item, $message, $isApprove ? eZApproveCollaborationHandler::MESSAGE_TYPE_APPROVE : 1 );
        if ( !$link )
            throw new expServiceException( 'The message could not be added', 422 );
        return self::ok( array( 'item' => (int)$item->attribute( 'id' ), 'message' => (int)$message->attribute( 'id' ) ) );
    }

    public static function participants( array $a = array() )
    {
        self::guard( 'participants' );
        $item = self::item( self::arg( $a, 0, 'int' ) );
        $out = array();
        foreach ( (array)$item->participantList() as $p )
        {
            $o = eZContentObject::fetch( (int)$p->attribute( 'participant_id' ) );
            $out[] = array( 'id' => (int)$p->attribute( 'participant_id' ), 'name' => $o ? $o->attribute( 'name' ) : null,
                            'type' => eZCollaborationItemParticipantLink::typeString( $p->attribute( 'participant_type' ) ),
                            'role' => eZCollaborationItemParticipantLink::roleString( $p->attribute( 'participant_role' ) ) );
        }
        return self::ok( $out );
    }

    public static function markRead( array $a = array() )
    {
        self::guard( 'markRead' );
        $item = self::item( self::post( 'item', 'int' ) );
        eZCollaborationItemStatus::setLastRead( (int)$item->attribute( 'id' ), self::me() );
        $item->setLastRead( self::me() );
        return self::ok( array( 'item' => (int)$item->attribute( 'id' ), 'read' => true ) );
    }

    public static function setActive( array $a = array() )
    {
        self::guard( 'setActive' );
        $item = self::item( self::post( 'item', 'int' ) );
        $active = self::post( 'active', 'bool' );
        $item->setIsActive( $active, self::me() );
        return self::ok( array( 'item' => (int)$item->attribute( 'id' ), 'active' => $active ) );
    }

    public static function handlers( array $a = array() )
    {
        self::guard( 'handlers' );
        return self::ok( array_values( (array)eZINI::instance( 'collaboration.ini' )->variable( 'HandlerSettings', 'Active' ) ) );
    }

    // ------------------------------------------------------------------ groups

    public static function groups( array $a = array() )
    {
        self::guard( 'groups' );
        $groups = eZPersistentObject::fetchObjectList( eZCollaborationGroup::definition(), null, array( 'user_id' => self::me() ), array( 'path_string' => 'asc' ), null, true );
        return self::pageOf( array_map( array( 'expCollaborationServices', 'exportCollabGroup' ), (array)$groups ), $a, 0, 1 );
    }

    public static function groupInfo( array $a = array() )
    {
        self::guard( 'groupInfo' );
        return self::ok( self::exportCollabGroup( self::group( self::arg( $a, 0, 'int' ) ) ) );
    }

    public static function groupItems( array $a = array() )
    {
        self::guard( 'groupItems' );
        $g = self::group( self::arg( $a, 0, 'int' ) );
        list( $limit, $offset ) = self::paging( $a, 1, 2 );
        $total = (int)$g->itemCount();
        return self::page( array_map( array( 'expCollaborationServices', 'exportItem' ), (array)$g->itemList( array( 'limit' => $limit, 'offset' => $offset ) ) ), $total, $offset, $limit );
    }

    public static function groupCreate( array $a = array() )
    {
        self::guard( 'groupCreate' );
        $title = trim( self::post( 'title', 'string' ) );
        $parent = self::post( 'parent', 'int', 0 );
        if ( $title === '' || strlen( $title ) > 255 )
            throw new expServiceException( 'The group needs a title of 1 to 255 characters', 422 );
        if ( $parent > 0 )
            self::group( $parent );
        return self::ok( self::exportCollabGroup( eZCollaborationGroup::instantiate( self::me(), $title, $parent ) ) );
    }

    public static function groupRename( array $a = array() )
    {
        self::guard( 'groupRename' );
        $g = self::group( self::post( 'group', 'int' ) );
        $title = trim( self::post( 'title', 'string' ) );
        if ( $title === '' || strlen( $title ) > 255 )
            throw new expServiceException( 'The group needs a title of 1 to 255 characters', 422 );
        $g->setAttribute( 'title', $title );
        $g->setAttribute( 'modified', time() );
        $g->store();
        return self::ok( self::exportCollabGroup( $g ) );
    }

    public static function groupRemove( array $a = array() )
    {
        self::guard( 'groupRemove' );
        $g = self::group( self::post( 'group', 'int' ) );
        if ( (int)$g->itemCount() > 0 )
            throw new expServiceException( 'The group still holds items', 409 );
        $children = eZPersistentObject::fetchObjectList( eZCollaborationGroup::definition(), null, array( 'parent_group_id' => (int)$g->attribute( 'id' ) ), null, null, true );
        if ( $children )
            throw new expServiceException( 'The group has subgroups', 409 );
        $id = (int)$g->attribute( 'id' );
        eZPersistentObject::removeObject( eZCollaborationGroup::definition(), array( 'id' => $id ) );
        return self::ok( array( 'group' => $id, 'removed' => true ) );
    }

    public static function moveToGroup( array $a = array() )
    {
        self::guard( 'moveToGroup' );
        $item = self::item( self::post( 'item', 'int' ) );
        $g = self::group( self::post( 'group', 'int' ) );
        $id = (int)$item->attribute( 'id' );
        eZPersistentObject::removeObject( eZCollaborationItemGroupLink::definition(), array( 'collaboration_id' => $id, 'user_id' => self::me() ) );
        eZCollaborationItemGroupLink::addItem( (int)$g->attribute( 'id' ), $id, self::me() );
        return self::ok( array( 'item' => $id, 'group' => (int)$g->attribute( 'id' ) ) );
    }

    // ------------------------------------------------------------------ approvals

    protected static function approverOf( eZCollaborationItem $item )
    {
        foreach ( (array)$item->participantList() as $p )
            if ( (int)$p->attribute( 'participant_id' ) === self::me() && (int)$p->attribute( 'participant_role' ) === eZCollaborationItemParticipantLink::ROLE_APPROVER )
                return true;
        return false;
    }

    protected static function pendingItems()
    {
        $out = array();
        foreach ( (array)eZCollaborationItem::fetchList( array( 'status' => array( eZCollaborationItem::STATUS_ACTIVE ) ) ) as $item )
            if ( $item->attribute( 'type_identifier' ) === 'ezapprove' && (int)$item->attribute( 'data_int3' ) === eZApproveCollaborationHandler::STATUS_WAITING && self::approverOf( $item ) )
                $out[] = $item;
        return $out;
    }

    protected static function approvalState( eZCollaborationItem $item )
    {
        $names = array( eZApproveCollaborationHandler::STATUS_WAITING => 'waiting', eZApproveCollaborationHandler::STATUS_ACCEPTED => 'accepted',
                        eZApproveCollaborationHandler::STATUS_DENIED => 'denied', eZApproveCollaborationHandler::STATUS_DEFERRED => 'deferred' );
        $s = (int)$item->attribute( 'data_int3' );
        return array( 'item' => (int)$item->attribute( 'id' ), 'state' => isset( $names[$s] ) ? $names[$s] : 'unknown', 'can_decide' => $s === eZApproveCollaborationHandler::STATUS_WAITING && self::approverOf( $item ),
                      'content_object_id' => (int)$item->attribute( 'data_int1' ), 'version' => (int)$item->attribute( 'data_int2' ) );
    }

    public static function pendingApprovals( array $a = array() )
    {
        self::guard( 'pendingApprovals' );
        return self::pageOf( array_map( array( 'expCollaborationServices', 'exportItem' ), self::pendingItems() ), $a, 0, 1 );
    }


    public static function approvalStatus( array $a = array() )
    {
        self::guard( 'approvalStatus' );
        $item = self::item( self::arg( $a, 0, 'int' ) );
        if ( $item->attribute( 'type_identifier' ) !== 'ezapprove' )
            throw new expServiceException( 'This is not an approval item', 409 );
        return self::ok( self::approvalState( $item ) );
    }

    protected static function decide( $accept )
    {
        $item = self::item( self::post( 'item', 'int' ) );
        if ( $item->attribute( 'type_identifier' ) !== 'ezapprove' )
            throw new expServiceException( 'This is not an approval item', 409 );
        if ( !self::approverOf( $item ) )
            throw new expServiceException( 'You are not an approver of this item', 403 );
        if ( (int)$item->attribute( 'data_int3' ) !== eZApproveCollaborationHandler::STATUS_WAITING )
            throw new expServiceException( 'This approval has been decided', 409 );
        $comment = trim( self::post( 'comment', 'string', '' ) );
        $item->setAttribute( 'data_int3', $accept ? eZApproveCollaborationHandler::STATUS_ACCEPTED : eZApproveCollaborationHandler::STATUS_DENIED );
        $item->setAttribute( 'status', eZCollaborationItem::STATUS_INACTIVE );
        $item->setAttribute( 'modified', time() );
        $item->setIsActive( false );
        if ( $comment !== '' )
        {
            $message = eZCollaborationSimpleMessage::create( 'ezapprove_comment', $comment );
            $message->store();
            eZCollaborationItemMessageLink::addMessage( $item, $message, eZApproveCollaborationHandler::MESSAGE_TYPE_APPROVE );
        }
        $item->sync();
        return self::ok( self::approvalState( $item ) );
    }

    public static function approve( array $a = array() )
    {
        self::guard( 'approve' );
        return self::decide( true );
    }

    public static function deny( array $a = array() )
    {
        self::guard( 'deny' );
        return self::decide( false );
    }
}

expCollaborationServices::$services = expUsersBase::specs( array(
    'items' => array( 'The collaboration items of the current user, paged (status active|inactive|archive, is_read, group)', 'user', 'r', 'limit:int,offset:int,status:string,is_read:bool,group:int', 'paged items' ),
    'itemCount' => array( 'The number of items, same filters', 'user', 'r', 'status:string,is_read:bool,group:int', 'count' ),
    'summary' => array( 'Active, unread, archived and pending approval counts', 'user', 'r', '', 'counts' ),
    'fetch' => array( 'One item, for participants', 'user', 'r', 'id:int', 'item' ),
    'messages' => array( 'The messages of an item, paged', 'user', 'r', 'id:int,limit:int,offset:int', 'paged messages' ),
    'messageCount' => array( 'The message and unread message count of an item', 'user', 'r', 'id:int', 'count, unread' ),
    'addMessage' => array( 'Add a message to an item (POST item, text)', 'user', 'w', 'item:int,text:string', 'message' ),
    'participants' => array( 'The participants of an item with type and role', 'user', 'r', 'id:int', 'participants' ),
    'markRead' => array( 'Mark an item read (POST item)', 'user', 'w', 'item:int', 'read' ),
    'setActive' => array( 'Show or hide an item in the current user\'s list (POST item, active)', 'user', 'w', 'item:int,active:bool', 'active' ),
    'handlers' => array( 'The active collaboration handlers', 'user', 'r', '', 'identifiers' ),
    'groups' => array( 'The collaboration groups of the current user', 'user', 'r', 'limit:int,offset:int', 'paged groups' ),
    'groupInfo' => array( 'One collaboration group', 'user', 'r', 'id:int', 'group' ),
    'groupItems' => array( 'The items of a group, paged', 'user', 'r', 'id:int,limit:int,offset:int', 'paged items' ),
    'groupCreate' => array( 'Create a group (POST title, parent)', 'user', 'w', 'title:string,parent:int', 'group' ),
    'groupRename' => array( 'Rename a group (POST group, title)', 'user', 'w', 'group:int,title:string', 'group' ),
    'groupRemove' => array( 'Remove an empty group (POST group)', 'user', 'w', 'group:int', 'removed' ),
    'moveToGroup' => array( 'Put an item in a group (POST item, group)', 'user', 'w', 'item:int,group:int', 'item, group' ),
    'pendingApprovals' => array( 'The approvals waiting for the current user', 'user', 'r', 'limit:int,offset:int', 'paged items' ),
    'approvalStatus' => array( 'The state of an approval item and whether the current user may decide', 'user', 'r', 'id:int', 'state, can_decide' ),
    'approve' => array( 'Approve a waiting item as an approver (POST item, comment)', 'user', 'w', 'item:int,comment:string', 'approval state' ),
    'deny' => array( 'Deny a waiting item as an approver (POST item, comment)', 'user', 'w', 'item:int,comment:string', 'approval state' ),
) );
