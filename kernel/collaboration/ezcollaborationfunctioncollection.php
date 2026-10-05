<?php
/**
 * File containing the eZCollaborationFunctionCollection class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZCollaborationFunctionCollection ezcontentfunctioncollection.php
  \brief The class eZCollaborationFunctionCollection does

*/


if ( !class_exists( 'eZCollaborationFunctionCollection', false ) ) {
class eZCollaborationFunctionCollection
{
    function fetchParticipant( $itemID, $participantID )
    {
        if ( $participantID === false )
        {
            $user = eZUser::currentUser();
            $participantID = $user->attribute( 'contentobject_id' );
        }
        $participant = eZCollaborationItemParticipantLink::fetch( $itemID, $participantID );
        if ( $participant === null )
        {
            $resultArray = array( 'error' => array( 'error_type' => 'kernel',
                                                    'error_code' => eZError::KERNEL_NOT_FOUND ) );
        }
        else
        {
            $resultArray = array( 'result' => $participant );
        }
        return $resultArray;
    }

    function fetchParticipantList( $itemID, $sortBy, $offset, $limit )
    {
        $itemParameters = array( 'item_id' => $itemID,
                                 'offset' => $offset,
                                 'limit' => $limit,
                                 'sort_by' => $sortBy );
        $children = eZCollaborationItemParticipantLink::fetchParticipantList( $itemParameters );
        if ( $children === null )
        {
            $resultArray = array( 'error' => array( 'error_type' => 'kernel',
                                                    'error_code' => eZError::KERNEL_NOT_FOUND ) );
        }
        else
        {
            $resultArray = array( 'result' => $children );
        }
        return $resultArray;
    }

    function fetchParticipantMap( $itemID, $sortBy, $offset, $limit, $field )
    {
        $itemParameters = array( 'item_id' => $itemID,
                                 'offset' => $offset,
                                 'limit' => $limit,
                                 'sort_by' => $sortBy );
        if ( $field !== false )
            $itemParameters['sort_field'] = $field;
        $children = eZCollaborationItemParticipantLink::fetchParticipantMap( $itemParameters );
        if ( $children === null )
        {
            $resultArray = array( 'error' => array( 'error_type' => 'kernel',
                                                    'error_code' => eZError::KERNEL_NOT_FOUND ) );
        }
        else
        {
            $resultArray = array( 'result' => $children );
        }
        return $resultArray;
    }

    function fetchMessageList( $itemID, $sortBy, $offset, $limit )
    {
        $itemParameters = array( 'item_id' => $itemID,
                                 'offset' => $offset,
                                 'limit' => $limit,
                                 'sort_by' => $sortBy );
        $children = eZCollaborationItemMessageLink::fetchItemList( $itemParameters );
        if ( $children === null )
        {
            $resultArray = array( 'error' => array( 'error_type' => 'kernel',
                                                    'error_code' => eZError::KERNEL_NOT_FOUND ) );
        }
        else
        {
            $resultArray = array( 'result' => $children );
        }
        return $resultArray;
    }

    function fetchItemList( $sortBy, $offset, $limit, $status, $isRead, $isActive, $parentGroupID )
    {
        $itemParameters = array( 'offset' => $offset,
                                 'limit' => $limit,
                                 'sort_by' => $sortBy,
                                 'is_read' => $isRead,
                                 'is_active' => $isActive,
                                 'parent_group_id' => $parentGroupID );
        if ( $status !== false )
            $itemParameters['status'] = $status;
        $children = eZCollaborationItem::fetchList( $itemParameters );
        if ( $children === null )
            return array( 'error' => array( 'error_type' => 'kernel',
                                            'error_code' => eZError::KERNEL_NOT_FOUND ) );
        return array( 'result' => $children );
    }

    function fetchItemCount( $isRead, $isActive, $parentGroupID, $status )
    {
        $itemParameters = array( 'is_read' => $isRead,
                                 'is_active' => $isActive,
                                 'parent_group_id' => $parentGroupID
                                 );
        if ( $status !== false )
            $itemParameters['status'] = $status;
        $count = eZCollaborationItem::fetchListCount( $itemParameters );
        return array( 'result' => $count );
    }

    function fetchGroupTree( $parentGroupID, $sortBy, $offset, $limit, $depth )
    {
        $treeParameters = array( 'parent_group_id' => $parentGroupID,
                                 'offset' => $offset,
                                 'limit' => $limit,
                                 'sort_by' => $sortBy,
                                 'depth' => $depth );
        $children = eZCollaborationGroup::subTree( $treeParameters );
        if ( $children === null )
            return array( 'error' => array( 'error_type' => 'kernel',
                                            'error_code' => eZError::KERNEL_NOT_FOUND ) );
        return array( 'result' => $children );
    }

    function fetchObjectTreeCount( $parentNodeID, $class_filter_type, $class_filter_array, $depth )
    {
        $node = eZContentObjectTreeNode::fetch( $parentNodeID );
        $childrenCount = $node->subTreeCount( array( 'Limitation' => null,
                                                     'ClassFilterType' => $class_filter_type,
                                                     'ClassFilterArray' => $class_filter_array,
                                                     'Depth' => $depth ) );
        if ( $childrenCount === null )
        {
            $resultArray = array( 'error' => array( 'error_type' => 'kernel',
                                                    'error_code' => eZError::KERNEL_NOT_FOUND ) );
        }
        else
        {
            $resultArray = array( 'result' => $childrenCount );
        }
        return $resultArray;
    }

    /**
     * The inbox of the current user (see expCollaborationInbox::fetch()).
     */
    function fetchInbox( $status, $role, $type, $groupID, $offset, $limit )
    {
        return array( 'result' => expCollaborationInbox::fetch( array( 'status' => $status, 'role' => $role, 'type' => $type,
                                                                      'group_id' => $groupID, 'offset' => $offset, 'limit' => $limit ) ) );
    }

    /**
     * One collaboration item as an inbox row (see expCollaborationInbox::fetchRow()).
     */
    function fetchInboxRow( $itemID )
    {
        $row = expCollaborationInbox::fetchRow( $itemID );
        if ( $row === false )
            return array( 'error' => array( 'error_type' => 'kernel',
                                            'error_code' => eZError::KERNEL_NOT_FOUND ) );
        return array( 'result' => $row );
    }

}
}


?>
