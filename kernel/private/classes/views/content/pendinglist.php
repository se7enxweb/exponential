<?php
/**
 * The code of kernel/content/pendinglist.php, moved into a class (#207 stage 1). The file kernel/content/pendinglist.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 *
 * The pending page of the current user ("My pending items"): the versions the user sent for publishing that a
 * workflow holds, and the versions waiting for the user's approval, each with what holds it (the approval and its
 * approvers, the workflow processes), when it was sent and where it goes. A version waiting for the user's approval is
 * shown only when content/versionread allows it. The list (filters, order, paging, figures) is worked out by
 * expContentPendingList, which needs no database; this view reads the versions, approvals and processes.
 * Guide: doc/guides/drafts-and-pending.md
 */
/*
 * The original header of kernel/content/pendinglist.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Content
{

class Pendinglist extends \Exponential\Runnable\ModuleView
{
    /** The view name of the page sizes (admininterface.ini [PaginationSettings]) and the preference of the chosen one */
    const LIST_VIEW = 'content/pendinglist';
    const LIMIT_PREFERENCE = 'admin_pending_list_limit';

    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];
        $offset = \expAdminPagination::offset( $Params );
        $filters = \expContentPendingList::filters( isset( $Params['UserParameters'] ) ? (array)$Params['UserParameters'] : array() );

        $user = \eZUser::currentUser();
        $rows = $user->isRegistered() ? self::rows( $user ) : array();
        $overview = \expContentPendingList::overview( $rows );
        $selected = \expContentPendingList::select( $rows, $filters );
        $count = count( $selected );
        list( $limit, $limitChoice, $limitChoices ) = \expAdminPagination::chosen( self::LIST_VIEW, self::LIMIT_PREFERENCE );
        if ( $offset >= $count )
            $offset = 0;

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'view_parameters', array( 'offset' => $offset ) );
        $tpl->setVariable( 'pending_rows', array_values( \expAdminPagination::page( $selected, $offset, $limit ) ) );
        $tpl->setVariable( 'pending_count', $count );
        $tpl->setVariable( 'pending_limit', $limit );
        $tpl->setVariable( 'pending_limit_choices', $limitChoices );
        $tpl->setVariable( 'pending_filters', $filters );
        $tpl->setVariable( 'pending_suffix', \expContentPendingList::suffix( $filters ) );
        $tpl->setVariable( 'pending_links', \expContentPendingList::links( $filters, $overview['classes'] ) );
        $tpl->setVariable( 'pending_overview', $overview );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:content/pendinglist.tpl' );
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/content', 'My pending list' ), 'url' => false ) );
        return $this->viewResult( $Result, null );
    }

    /**
     * Every row: the user's own pending versions and the versions whose approval waits for the user (as a user or
     * through one of their groups), each once.
     *
     * @param \eZUser $user
     * @return array rows of expContentPendingList::row()
     */
    protected static function rows( $user )
    {
        $userID = (int)$user->attribute( 'contentobject_id' );
        $participantIDs = array_values( array_unique( array_merge( array( $userID ), array_map( 'intval', (array)$user->groups() ) ) ) );
        $versions = array();
        $mine = array();
        $approve = array();
        foreach ( \eZContentObjectVersion::fetchForUser( $userID, \eZContentObjectVersion::STATUS_PENDING ) as $version )
        {
            $versions[(int)$version->attribute( 'id' )] = $version;
            $mine[(int)$version->attribute( 'id' )] = true;
        }
        foreach ( self::approvalsWaitingFor( $participantIDs ) as $item )
        {
            $version = \eZContentObjectVersion::fetchVersion( (int)$item->attribute( 'data_int2' ), (int)$item->attribute( 'data_int1' ) );
            if ( !$version instanceof \eZContentObjectVersion )
                continue;
            $versions[(int)$version->attribute( 'id' )] = $version;
            $approve[(int)$version->attribute( 'id' )] = true;
        }

        $rows = array();
        $names = array();
        foreach ( $versions as $id => $version )
        {
            $object = $version->attribute( 'contentobject' );
            if ( !$object instanceof \eZContentObject )
                continue;
            $class = $object->attribute( 'content_class' );
            $creator = $version->attribute( 'creator' );
            $nodeID = (int)$object->attribute( 'main_node_id' );
            $parentID = $nodeID ? (int)$object->attribute( 'main_parent_node_id' ) : (int)$version->attribute( 'main_parent_node_id' );
            $approval = self::approvalOf( (int)$object->attribute( 'id' ), (int)$version->attribute( 'version' ) );
            $approvers = array();
            $takesPart = false;
            if ( $approval )
            {
                foreach ( \eZPersistentObject::fetchObjectList( \eZCollaborationItemParticipantLink::definition(), null,
                                                               array( 'collaboration_id' => (int)$approval->attribute( 'id' ) ), null, null, true ) as $link )
                {
                    if ( in_array( (int)$link->attribute( 'participant_id' ), $participantIDs, true ) )
                        $takesPart = true;
                    if ( (int)$link->attribute( 'participant_role' ) === \eZCollaborationItemParticipantLink::ROLE_APPROVER )
                        $approvers[] = self::objectName( (int)$link->attribute( 'participant_id' ), $names );
                }
            }
            $workflows = array();
            foreach ( \eZPersistentObject::fetchObjectList( \eZWorkflowProcess::definition(), null,
                                                           array( 'content_id' => (int)$object->attribute( 'id' ),
                                                                  'content_version' => (int)$version->attribute( 'version' ) ), null, null, true ) as $process )
            {
                $workflow = \eZWorkflow::fetch( (int)$process->attribute( 'workflow_id' ) );
                $workflows[] = $workflow ? (string)$workflow->attribute( 'name' ) : '#' . (int)$process->attribute( 'workflow_id' );
            }
            $locale = (string)$version->initialLanguageCode();
            $language = \eZContentLanguage::fetchByLocale( $locale );
            $rows[] = \expContentPendingList::row( array(
                'id' => $id, 'version' => $version->attribute( 'version' ), 'object_id' => $object->attribute( 'id' ),
                'name' => $version->versionName(), 'class_identifier' => $class ? $class->attribute( 'identifier' ) : '',
                'class_name' => $class ? $class->attribute( 'name' ) : '', 'language' => $locale,
                'language_name' => $language ? $language->attribute( 'name' ) : $locale,
                'creator_id' => $version->attribute( 'creator_id' ), 'creator_name' => $creator ? $creator->attribute( 'name' ) : '',
                'sent' => $version->attribute( 'modified' ), 'is_new' => (int)$object->attribute( 'status' ) === \eZContentObject::STATUS_DRAFT,
                'node_id' => $nodeID, 'location' => $parentID ? self::nodeName( $parentID, $names ) : '',
                'mine' => isset( $mine[$id] ), 'approver' => isset( $approve[$id] ),
                'approval_id' => $approval ? $approval->attribute( 'id' ) : 0,
                'approval_state' => $approval ? (int)$approval->attribute( 'data_int3' ) : 0,
                'approvers' => array_values( array_unique( $approvers ) ), 'approval_link' => $takesPart,
                'workflows' => array_values( array_unique( $workflows ) ),
                'can_versionread' => (bool)$version->attribute( 'can_read' ) ) );
        }
        return $rows;
    }

    /** @return \eZCollaborationItem[] the active approvals still waiting whose approvers include one of $participantIDs */
    protected static function approvalsWaitingFor( array $participantIDs )
    {
        $links = \eZPersistentObject::fetchObjectList( \eZCollaborationItemParticipantLink::definition(), null,
                                                      array( 'participant_id' => array( $participantIDs ),
                                                             'participant_role' => \eZCollaborationItemParticipantLink::ROLE_APPROVER ), null, null, true );
        $items = array();
        foreach ( (array)$links as $link )
        {
            $item = \eZCollaborationItem::fetch( (int)$link->attribute( 'collaboration_id' ) );
            if ( $item && $item->attribute( 'type_identifier' ) === 'ezapprove' &&
                 (int)$item->attribute( 'status' ) === \eZCollaborationItem::STATUS_ACTIVE &&
                 (int)$item->attribute( 'data_int3' ) === \eZApproveCollaborationHandler::STATUS_WAITING )
                $items[(int)$item->attribute( 'id' )] = $item;
        }
        return array_values( $items );
    }

    /** @return \eZCollaborationItem|null the newest approval of a version */
    protected static function approvalOf( $objectID, $version )
    {
        $items = \eZPersistentObject::fetchObjectList( \eZCollaborationItem::definition(), null,
                                                      array( 'type_identifier' => 'ezapprove', 'data_int1' => (int)$objectID, 'data_int2' => (int)$version ),
                                                      array( 'id' => 'desc' ), array( 'offset' => 0, 'length' => 1 ), true );
        return $items ? $items[0] : null;
    }

    private static function objectName( $id, array &$names )
    {
        if ( !isset( $names['o' . $id] ) )
        {
            $object = \eZContentObject::fetch( $id );
            $names['o' . $id] = $object ? (string)$object->attribute( 'name' ) : '#' . $id;
        }
        return $names['o' . $id];
    }

    private static function nodeName( $id, array &$names )
    {
        if ( !isset( $names['n' . $id] ) )
        {
            $node = \eZContentObjectTreeNode::fetch( $id );
            $names['n' . $id] = $node ? (string)$node->attribute( 'name' ) : '';
        }
        return $names['n' . $id];
    }
}

}
