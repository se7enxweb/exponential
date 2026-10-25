<?php
/**
 * File containing the expUserApproveType class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The workflow event "Approve user accounts" (doc/eng-us/bc/6.0/user-account-approval.md), modelled on the approval
 * event (ezapprove): on content/publish before, a user account whose version places it in a group that requires
 * approval waits until an approver accepts it. The approval event can only ask for a section; this one asks where the
 * account is placed, which is what decides the rights its user gets.
 *
 * The approval is the approval event's: an item of the collaboration handler "ezapprove" in the approvers' and the
 * author's collaboration lists, accepted or denied there; the item and the process are linked in ezapprove_items. On
 * top, the approvers get a mail with the link to the item (no key: they sign in), and the request and the decision are
 * recorded in the audit log (access.user.approval.request, .approve, .deny). After the decision the workflow continues
 * at once (expUserApproval::decided()), not at the next run of the workflow cronjob.
 *
 * Storage fields:  data_text1 - groups that require approval (user group object ids)
 *                  data_text2 - groups that need no approval (their members publish such accounts directly)
 *                  data_text3 - users who approve
 *                  data_text4 - groups who approve
 *                  data_int1  - 1: no mail to the approvers (0, the default, sends it)
 *
 * Which placements need approval: those of the version that the account does not have yet (a new account, or a new
 * location added in the edit form) and that lie in a required group. Someone who approves, or who is in a group that
 * needs no approval, publishes directly. Moving an account or adding a location without publishing is guarded by
 * expUserApproval::placementAllowed().
 *
 * @since 6.0.15
 * @package kernel
 */
class expUserApproveType extends eZApproveType
{
    const WORKFLOW_TYPE_STRING = 'expuserapprove';

    public function __construct()
    {
        eZWorkflowEventType::__construct( self::WORKFLOW_TYPE_STRING, ezpI18n::tr( 'kernel/workflow/event', 'Approve user accounts' ) );
        $this->setTriggerTypes( array( 'content' => array( 'publish' => array( 'before' ) ) ) );
    }

    function attributeDecoder( $event, $attr )
    {
        switch ( $attr )
        {
            case 'required_groups':
                return expUserApproval::idList( $event->attribute( 'data_text1' ) );
            case 'selected_usergroups':
                return expUserApproval::idList( $event->attribute( 'data_text2' ) );
            case 'approve_users':
                return expUserApproval::idList( $event->attribute( 'data_text3' ) );
            case 'approve_groups':
                return expUserApproval::idList( $event->attribute( 'data_text4' ) );
            case 'send_mail':
                return (int)$event->attribute( 'data_int1' ) !== 1;
        }
        return null;
    }

    function typeFunctionalAttributes()
    {
        return array( 'required_groups', 'selected_usergroups', 'approve_users', 'approve_groups', 'send_mail' );
    }

    function execute( $process, $event )
    {
        $parameters = $process->attribute( 'parameter_list' );
        $objectID = isset( $parameters['object_id'] ) ? (int)$parameters['object_id'] : 0;
        $versionID = isset( $parameters['version'] ) ? (int)$parameters['version'] : 0;
        $object = eZContentObject::fetch( $objectID );
        if ( !$object instanceof eZContentObject )
            return eZWorkflowType::STATUS_WORKFLOW_CANCELLED;
        $version = $object->version( $versionID );
        if ( !$version instanceof eZContentObjectVersion )
            return eZWorkflowType::STATUS_WORKFLOW_CANCELLED;
        // Only user accounts (and user groups, which bring every account in them along)
        if ( !expUserApproval::isAccountOrGroup( $object ) )
            return eZWorkflowType::STATUS_ACCEPTED;

        // The user who published: stored in the process the first time, read from there when the cronjob runs it
        if ( $process->attribute( 'user_id' ) == 0 )
        {
            $user = eZUser::currentUser();
            $process->setAttribute( 'user_id', $user->id() );
        }
        else
        {
            $user = eZUser::instance( $process->attribute( 'user_id' ) );
        }

        $db = eZDB::instance();
        $taskResult = $db->arrayQuery( 'SELECT collaboration_id FROM ezapprove_items WHERE workflow_process_id = ' . (int)$process->attribute( 'id' ) );
        $collaborationID = $taskResult ? (int)$taskResult[0]['collaboration_id'] : 0;
        // A decision was asked for already: whatever the placement is now, the approver's answer counts
        if ( $collaborationID )
        {
            if ( $process->attribute( 'event_state' ) == eZApproveType::COLLABORATION_NOT_CREATED )
            {
                eZApproveCollaborationHandler::activateApproval( $collaborationID );
                $process->setAttribute( 'event_state', eZApproveType::COLLABORATION_CREATED );
                $process->store();
                return eZWorkflowType::STATUS_DEFERRED_TO_CRON_REPEAT;
            }
            return $this->checkApproveCollaboration( $process, $event );
        }

        $config = expUserApproval::config( $event );
        $placements = expUserApproval::matchingPlacements( $object, $version, $config );
        if ( !$placements )
            return eZWorkflowType::STATUS_ACCEPTED;
        if ( expUserApproval::exemptBy( (int)$user->id(), expUserApproval::groupsOf( $user ), $config ) !== '' )
            return eZWorkflowType::STATUS_ACCEPTED;

        $approverIDs = expUserApproval::approverIDs( $config );
        // Nobody to approve: the account would wait for ever. Refused, and named in the log.
        if ( !$approverIDs )
        {
            eZDebug::writeError( "Object $objectID needs approval, but the event {$config['event_id']} names nobody who approves", __METHOD__ );
            $version->setAttribute( 'status', eZContentObjectVersion::STATUS_DRAFT );
            $version->sync();
            return eZWorkflowType::STATUS_WORKFLOW_CANCELLED;
        }
        $this->createApproveCollaboration( $process, $event, (int)$user->id(), $objectID, $versionID, $approverIDs );
        $rows = $db->arrayQuery( 'SELECT collaboration_id FROM ezapprove_items WHERE workflow_process_id = ' . (int)$process->attribute( 'id' ) );
        $collaborationID = $rows ? (int)$rows[0]['collaboration_id'] : 0;
        $this->setInformation( 'The account waits for approval' );
        $process->setAttribute( 'event_state', eZApproveType::COLLABORATION_CREATED );
        $process->store();
        expUserApproval::requested( $process, $config, $object, $versionID, $user, $placements, $approverIDs, $collaborationID );
        return eZWorkflowType::STATUS_DEFERRED_TO_CRON_REPEAT;
    }

    function validateHTTPInput( $http, $base, $workflowEvent, &$validation )
    {
        $reason = array();
        $state = eZInputValidator::STATE_ACCEPTED;
        if ( $http->hasSessionVariable( 'BrowseParameters' ) )
        {
            $browse = $http->sessionVariable( 'BrowseParameters' );
            $custom = isset( $browse['custom_action_data'] ) ? $browse['custom_action_data'] : array();
            if ( isset( $custom['event_id'] ) && $custom['event_id'] == $workflowEvent->attribute( 'id' )
                 && isset( $custom['browse_action'] ) && $custom['browse_action'] === 'AddRequiredGroups' )
            {
                if ( !$http->hasPostVariable( 'BrowseCancelButton' ) && $http->hasPostVariable( 'SelectedObjectIDArray' ) )
                    $state = $this->validateGroupIDList( (array)$http->postVariable( 'SelectedObjectIDArray' ), $reason );
            }
            else
            {
                return parent::validateHTTPInput( $http, $base, $workflowEvent, $validation );
            }
        }
        else
        {
            if ( $http->hasPostVariable( 'DeleteRequiredGroupIDArray_' . $workflowEvent->attribute( 'id' ) ) )
                return eZInputValidator::STATE_ACCEPTED;
            $state = parent::validateHTTPInput( $http, $base, $workflowEvent, $validation );
            if ( $state == eZInputValidator::STATE_INVALID )
                return $state;
            $required = $this->attributeDecoder( $workflowEvent, 'required_groups' );
            if ( !$required )
            {
                $state = eZInputValidator::STATE_INVALID;
                $reason['text'] = 'Choose at least one user group whose new accounts need approval.';
            }
            else
            {
                $state = $this->validateGroupIDList( $required, $reason );
            }
        }
        if ( $state == eZInputValidator::STATE_INVALID )
        {
            $validation['processed'] = true;
            $validation['events'][] = array( 'id' => $workflowEvent->attribute( 'id' ),
                                             'placement' => $workflowEvent->attribute( 'placement' ),
                                             'workflow_type' => &$this,
                                             'reason' => $reason );
        }
        return $state;
    }

    function fetchHTTPInput( $http, $base, $event )
    {
        $mailVar = $base . '_event_expuserapprove_mail_' . $event->attribute( 'id' );
        // the form sends the hidden field always and the checkbox when it is ticked
        if ( $http->hasPostVariable( $mailVar . '_shown' ) )
            $event->setAttribute( 'data_int1', $http->hasPostVariable( $mailVar ) ? 0 : 1 );

        if ( $http->hasSessionVariable( 'BrowseParameters' ) )
        {
            $browse = $http->sessionVariable( 'BrowseParameters' );
            $custom = isset( $browse['custom_action_data'] ) ? $browse['custom_action_data'] : array();
            if ( isset( $custom['event_id'] ) && $custom['event_id'] == $event->attribute( 'id' )
                 && isset( $custom['browse_action'] ) && $custom['browse_action'] === 'AddRequiredGroups' )
            {
                if ( !$http->hasPostVariable( 'BrowseCancelButton' ) && $http->hasPostVariable( 'SelectedObjectIDArray' ) )
                {
                    $ids = expUserApproval::idList( (array)$http->postVariable( 'SelectedObjectIDArray' ) );
                    $event->setAttribute( 'data_text1', implode( ',', array_values( array_unique( array_merge(
                        $this->attributeDecoder( $event, 'required_groups' ), $ids ) ) ) ) );
                    $http->removeSessionVariable( 'BrowseParameters' );
                }
                return;
            }
        }
        // the approvers and the groups without approval: as the approval event (sections, languages and versions are
        // not posted by this event's form, so they stay untouched)
        parent::fetchHTTPInput( $http, $base, $event );
    }

    function customWorkflowEventHTTPAction( $http, $action, $workflowEvent )
    {
        $eventID = $workflowEvent->attribute( 'id' );
        switch ( $action )
        {
            case 'AddRequiredGroups':
            {
                $groupClassNames = eZUser::fetchUserGroupClassNames();
                if ( count( $groupClassNames ) > 0 )
                {
                    $module =& $GLOBALS['eZRequestedModule'];
                    eZContentBrowse::browse( array( 'action_name' => 'SelectMultipleUsers',
                                                    'from_page' => '/workflow/edit/' . $workflowEvent->attribute( 'workflow_id' ),
                                                    'custom_action_data' => array( 'event_id' => $eventID, 'browse_action' => $action ),
                                                    'class_array' => $groupClassNames ),
                                             $module );
                }
            } break;

            case 'RemoveRequiredGroups':
            {
                if ( $http->hasPostVariable( 'DeleteRequiredGroupIDArray_' . $eventID ) )
                {
                    $workflowEvent->setAttribute( 'data_text1', implode( ',', array_diff( $this->attributeDecoder( $workflowEvent, 'required_groups' ),
                        expUserApproval::idList( (array)$http->postVariable( 'DeleteRequiredGroupIDArray_' . $eventID ) ) ) ) );
                }
            } break;

            default:
                parent::customWorkflowEventHTTPAction( $http, $action, $workflowEvent );
        }
    }

    function cleanupAfterRemoving( $attr = array() )
    {
        // A removed group or user leaves the lists of every such event
        if ( !isset( $attr['DeleteContentObject'] ) )
            return;
        $contentObjectID = (int)$attr['DeleteContentObject'];
        foreach ( (array)eZWorkflowEvent::fetchFilteredList( array( 'workflow_type_string' => expUserApproval::TYPE_STRING ) ) as $event )
        {
            $changed = false;
            foreach ( array( 'data_text1', 'data_text2', 'data_text3', 'data_text4' ) as $field )
            {
                $ids = expUserApproval::idList( $event->attribute( $field ) );
                if ( in_array( $contentObjectID, $ids, true ) )
                {
                    $event->setAttribute( $field, implode( ',', array_diff( $ids, array( $contentObjectID ) ) ) );
                    $changed = true;
                }
            }
            if ( $changed )
                $event->store();
        }
    }
}

eZWorkflowEventType::registerEventType( expUserApproveType::WORKFLOW_TYPE_STRING, 'expUserApproveType' );
