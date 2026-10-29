<?php
/**
 * File containing the expUserApproval class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Approval of new accounts in chosen user groups (doc/eng-us/bc/6.0/user-account-approval.md): the workflow event
 * "Approve user accounts" (expUserApproveType, on content/publish before) holds a user account that would be placed in
 * a group that requires approval until an approver accepts it in the collaboration system, as the approval event
 * (ezapprove) does for content. By default an editor who creates an account in the Editors group waits for the
 * Administrator users.
 *
 * This class holds what the event, the collaboration handler, the move and location operations and the setup command
 * share:
 * - the event's settings (config()) and the active events (activeEvents(): those of a workflow that a content/publish
 *   before trigger runs);
 * - which placements of a version need approval (matchingPlacements()) and who needs none (isExempt());
 * - the guard of the operations that place an account without publishing it, move and add location
 *   (placementAllowed()): someone who would wait for approval when publishing is refused there;
 * - the request (requested(): audit record and mail to the approvers with the link to the approval item, never a key)
 *   and the decision (decided(): audit record, the workflow continues at once instead of at the next cronjob run);
 * - the setup of a default installation and of one that exists (plan(), apply(), ensure(); bin/php/adduserapproval.php).
 *
 * idList(), placedBelow(), exemptBy() and the plan's policy lines work on plain values and are tested without a database.
 */
class expUserApproval
{
    const TYPE_STRING = 'event_expuserapprove';

    /** The default setup: Editors wait for the Administrator users (remote ids of the stock groups) */
    const EDITORS = 'remote:3c160cca19fb135f83bd02d911f04db2';
    const ADMINISTRATORS = 'remote:9b47a45624b023b1a76c73b74d704acf';

    const WORKFLOW_NAME = 'Approve new editor accounts';
    const WORKFLOW_GROUP_NAME = 'Standard';
    const ROLE_NAME = 'Editor account requests';

    /** The mail category: essential, the mail gate never holds it back */
    const MAIL_CATEGORY = 'security';

    /** @var array|null the active events' configurations, per request */
    protected static $active = null;

    /** @var array test override of activeConfigs() */
    protected static $testConfigs = null;

    // ---- plain values -------------------------------------------------------------------------------------------

    /**
     * A comma separated list of ids as integers (empty values and non-numbers left out).
     *
     * @param string|array $value
     * @return int[]
     */
    public static function idList( $value )
    {
        $parts = is_array( $value ) ? $value : explode( ',', (string)$value );
        $ids = array();
        foreach ( $parts as $part )
        {
            $part = trim( (string)$part );
            if ( $part !== '' && ctype_digit( $part ) && (int)$part > 0 )
                $ids[] = (int)$part;
        }
        return array_values( array_unique( $ids ) );
    }

    /**
     * The parent paths that lie in (or are) one of the required groups' paths.
     *
     * @param string[] $parentPaths path strings of the parents an account is placed below
     * @param string[] $requiredPaths path strings of the groups that require approval
     * @return string[] the parent paths that need approval
     */
    public static function placedBelow( array $parentPaths, array $requiredPaths )
    {
        $matched = array();
        foreach ( $parentPaths as $path )
        {
            foreach ( $requiredPaths as $required )
            {
                if ( $required !== '' && strpos( (string)$path, (string)$required ) === 0 )
                {
                    $matched[] = (string)$path;
                    break;
                }
            }
        }
        return array_values( array_unique( $matched ) );
    }

    /**
     * Why someone needs no approval, from the event's settings: 'approver' (one of the approving users, or a member
     * of an approving group), 'excluded' (a member of a group that needs no approval), or '' (needs approval).
     *
     * @param int $userID
     * @param int[] $groupIDs the user's groups (object ids)
     * @param array $config config()
     * @return string
     */
    public static function exemptBy( $userID, array $groupIDs, array $config )
    {
        $own = array_merge( array_map( 'intval', $groupIDs ), array( (int)$userID ) );
        if ( in_array( (int)$userID, $config['approve_users'], true ) || array_intersect( $own, $config['approve_groups'] ) )
            return 'approver';
        if ( array_intersect( $own, $config['excluded_groups'] ) )
            return 'excluded';
        return '';
    }

    /**
     * An event's settings.
     *
     * @param eZWorkflowEvent $event
     * @return array required_groups, excluded_groups, approve_users, approve_groups (int[]), mail (bool), event_id, workflow_id
     */
    public static function config( $event )
    {
        return array( 'required_groups' => self::idList( $event->attribute( 'data_text1' ) ),
                      'excluded_groups' => self::idList( $event->attribute( 'data_text2' ) ),
                      'approve_users' => self::idList( $event->attribute( 'data_text3' ) ),
                      'approve_groups' => self::idList( $event->attribute( 'data_text4' ) ),
                      // data_int1: 0 (the default of a new event) sends the mail, 1 does not
                      'mail' => (int)$event->attribute( 'data_int1' ) !== 1,
                      'event_id' => (int)$event->attribute( 'id' ),
                      'workflow_id' => (int)$event->attribute( 'workflow_id' ) );
    }

    // ---- the installation -----------------------------------------------------------------------------------------

    /**
     * The configurations of the events that run: an "Approve user accounts" event of a workflow that a content/publish
     * before trigger starts (version 0, the stored workflow). Kept for the request.
     *
     * @return array[] config()
     */
    public static function activeConfigs()
    {
        if ( self::$testConfigs !== null )
            return self::$testConfigs;
        if ( self::$active !== null )
            return self::$active;
        $configs = array();
        try
        {
            $workflowIDs = array();
            foreach ( (array)eZTrigger::fetchList( array( 'module' => 'content', 'function' => 'publish', 'connectType' => 'b' ) ) as $trigger )
                $workflowIDs[] = (int)$trigger->attribute( 'workflow_id' );
            if ( $workflowIDs )
            {
                foreach ( (array)eZWorkflowEvent::fetchFilteredList( array( 'workflow_type_string' => self::TYPE_STRING, 'version' => 0 ) ) as $event )
                {
                    if ( in_array( (int)$event->attribute( 'workflow_id' ), $workflowIDs, true ) )
                        $configs[] = self::config( $event );
                }
            }
        }
        catch ( Throwable $e )
        {
            eZDebug::writeError( 'The approval events could not be read: ' . $e->getMessage(), __METHOD__ );
        }
        self::$active = $configs;
        return $configs;
    }

    /**
     * Forgets what was read (tests, after a workflow changed in the same request).
     *
     * @param array[]|null $configs test configurations to use instead of the database; null reads it again
     */
    public static function reset( $configs = null )
    {
        self::$active = null;
        self::$testConfigs = $configs;
    }

    /**
     * Forgets the configurations read in this request, keeping a test override: at the start of every request
     * (expKernelServiceScope::DEFAULT_RESET_HOOKS, a persistent worker kept them from its first request on) and
     * after an event's lists changed (expUserApproveType::cleanupAfterRemoving()).
     */
    public static function resetRequest()
    {
        self::$active = null;
    }

    /**
     * The path strings of every location of the given groups.
     *
     * @param int[] $groupIDs object ids
     * @return string[]
     */
    public static function groupPaths( array $groupIDs )
    {
        $paths = array();
        foreach ( $groupIDs as $groupID )
        {
            $group = eZContentObject::fetch( (int)$groupID );
            if ( !$group instanceof eZContentObject )
                continue;
            foreach ( (array)$group->assignedNodes( false ) as $row )
                $paths[] = (string)$row['path_string'];
        }
        return array_values( array_unique( $paths ) );
    }

    /**
     * Whether an object is a user account or a user group (what can bring a person into a group).
     *
     * @param eZContentObject $object
     * @return bool
     */
    public static function isAccountOrGroup( $object )
    {
        if ( !$object instanceof eZContentObject )
            return false;
        $classID = (int)$object->attribute( 'contentclass_id' );
        if ( in_array( $classID, array_map( 'intval', (array)eZUser::contentClassIDs() ), true ) )
            return true;
        $groupClasses = array();
        foreach ( (array)eZUser::fetchUserGroupClassNames() as $identifier )
            $groupClasses[] = (int)eZContentClass::classIDByIdentifier( $identifier );
        return in_array( $classID, $groupClasses, true );
    }

    /**
     * The placements of a version that need approval under an event's settings: the parents of its node assignments
     * that the object is not already placed below, lying in a required group. Removals are left out.
     *
     * @param eZContentObject $object
     * @param eZContentObjectVersion $version
     * @param array $config config()
     * @return eZContentObjectTreeNode[] the parent nodes
     */
    public static function matchingPlacements( $object, $version, array $config )
    {
        $requiredPaths = self::groupPaths( $config['required_groups'] );
        if ( !$requiredPaths )
            return array();
        $current = array_map( 'intval', (array)$object->parentNodeIDArray() );
        $parents = array();
        foreach ( (array)$version->attribute( 'node_assignments' ) as $assignment )
        {
            $op = (int)$assignment->attribute( 'op_code' );
            if ( $op === eZNodeAssignment::OP_CODE_REMOVE || $op === eZNodeAssignment::OP_CODE_REMOVE_NOP )
                continue;
            $parentID = (int)$assignment->attribute( 'parent_node' );
            if ( in_array( $parentID, $current, true ) )
                continue;
            $parent = eZContentObjectTreeNode::fetch( $parentID );
            if ( $parent instanceof eZContentObjectTreeNode )
                $parents[(string)$parent->attribute( 'path_string' )] = $parent;
        }
        $matched = self::placedBelow( array_keys( $parents ), $requiredPaths );
        $result = array();
        foreach ( $matched as $path )
            $result[] = $parents[$path];
        return $result;
    }

    /**
     * The users who approve under an event's settings: the approving users and every user below the approving groups.
     *
     * @param array $config config()
     * @return int[]
     */
    public static function approverIDs( array $config )
    {
        $ids = $config['approve_users'];
        $userClassIDs = eZUser::contentClassIDs();
        foreach ( $config['approve_groups'] as $groupID )
        {
            $group = eZContentObject::fetch( $groupID );
            if ( !$group instanceof eZContentObject )
                continue;
            foreach ( (array)$group->attribute( 'assigned_nodes' ) as $node )
            {
                foreach ( (array)$node->subTree( array( 'ClassFilterType' => 'include', 'ClassFilterArray' => $userClassIDs,
                                                        'Limitation' => array() ) ) as $userNode )
                    $ids[] = (int)$userNode->attribute( 'contentobject_id' );
            }
        }
        return array_values( array_filter( array_unique( array_map( 'intval', $ids ) ) ) );
    }

    /**
     * The groups of a user, for exemptBy().
     *
     * @param eZUser $user
     * @return int[]
     */
    public static function groupsOf( $user )
    {
        return $user instanceof eZUser ? array_map( 'intval', (array)$user->attribute( 'groups' ) ) : array();
    }

    // ---- the guard of move and add location -------------------------------------------------------------------

    /**
     * Whether a user may place an account (or a user group) below a node without publishing it (content/move, add
     * location): refused when the node lies in a group that requires approval and the user would need approval to
     * publish such a placement. Every refusal is recorded (access.user.approval.refused).
     *
     * @param eZContentObject $object
     * @param eZContentObjectTreeNode|int $parentNode
     * @param int|bool $userID false: the current user
     * @return bool
     */
    public static function placementAllowed( $object, $parentNode, $userID = false )
    {
        $configs = self::activeConfigs();
        if ( !$configs || !self::isAccountOrGroup( $object ) )
            return true;
        if ( !$parentNode instanceof eZContentObjectTreeNode )
            $parentNode = eZContentObjectTreeNode::fetch( (int)$parentNode );
        if ( !$parentNode instanceof eZContentObjectTreeNode )
            return true;
        $user = $userID ? eZUser::fetch( (int)$userID ) : eZUser::currentUser();
        $userID = $user instanceof eZUser ? (int)$user->attribute( 'contentobject_id' ) : 0;
        $path = (string)$parentNode->attribute( 'path_string' );
        foreach ( $configs as $config )
        {
            if ( !self::placedBelow( array( $path ), self::groupPaths( $config['required_groups'] ) ) )
                continue;
            if ( self::exemptBy( $userID, self::groupsOf( $user ), $config ) !== '' )
                continue;
            if ( class_exists( 'expAuditHook' ) )
                expAuditHook::emit( 'access.user.approval.refused', function () use ( $object, $parentNode, $config ) {
                    return array( 'object' => expAuditHook::userOrGroup( (int)$object->attribute( 'id' ) ),
                                  'target' => expAuditHook::node( $parentNode ),
                                  'verb' => 'place', 'result' => 'refused', 'reason' => 'approval required',
                                  'after' => array( 'workflow' => $config['workflow_id'], 'event' => $config['event_id'] ) );
                } );
            eZDebug::writeWarning( sprintf( 'Placing object %d below node %d needs approval: create the account there and publish it instead',
                                            (int)$object->attribute( 'id' ), (int)$parentNode->attribute( 'node_id' ) ), __METHOD__ );
            return false;
        }
        return true;
    }

    // ---- request and decision -----------------------------------------------------------------------------------

    /**
     * The address of an approval item in the administration (the approver signs in there; the link carries no key).
     *
     * @param int $collaborationID
     * @param string|null $base null: the administration's base address (expRegistrationAdminAction::adminBaseURL())
     * @return string
     */
    public static function itemURL( $collaborationID, $base = null )
    {
        if ( $base === null )
            $base = class_exists( 'expRegistrationAdminAction' ) ? expRegistrationAdminAction::adminBaseURL() : '';
        return rtrim( (string)$base, '/' ) . '/collaboration/item/full/' . (int)$collaborationID;
    }

    /**
     * After the event created an approval item: the audit record and the mail to the approvers.
     *
     * @param eZWorkflowProcess $process
     * @param array $config config()
     * @param eZContentObject $object the account
     * @param int $versionID
     * @param eZUser $requester who published
     * @param eZContentObjectTreeNode[] $placements the parents that need approval
     * @param int[] $approverIDs
     * @param int $collaborationID
     * @return int the number of mails sent
     */
    public static function requested( $process, array $config, $object, $versionID, $requester, array $placements, array $approverIDs, $collaborationID )
    {
        $sent = 0;
        if ( $config['mail'] )
        {
            try
            {
                $sent = self::sendMail( $object, $requester, $placements, $approverIDs, $collaborationID, null, (int)$versionID );
            }
            catch ( Throwable $e )
            {
                eZDebug::writeError( 'The approval mail could not be sent: ' . $e->getMessage(), __METHOD__ );
            }
        }
        if ( class_exists( 'expAuditHook' ) )
        {
            $objectID = (int)$object->attribute( 'id' );
            $groups = array();
            foreach ( $placements as $parent )
                $groups[] = array( 'node' => (int)$parent->attribute( 'node_id' ), 'name' => (string)$parent->attribute( 'name' ) );
            expAuditHook::emit( 'access.user.approval.request', function () use ( $objectID, $versionID, $groups, $approverIDs, $collaborationID, $sent, $config, $process ) {
                return array( 'object' => expAuditHook::userOrGroup( $objectID ),
                              'target' => array( 'type' => 'collaboration', 'id' => (int)$collaborationID ),
                              'after' => array( 'version' => (int)$versionID, 'groups' => $groups, 'approvers' => count( $approverIDs ),
                                                'mails' => $sent, 'workflow' => $config['workflow_id'],
                                                'process' => (int)$process->attribute( 'id' ) ) );
            } );
        }
        return $sent;
    }

    /**
     * The mail to the approvers: who asked, for which account and group, and the link to the approval item. One mail
     * per approver with an e-mail address; nothing in it approves or denies.
     *
     * @param eZContentObject $object
     * @param eZUser $requester
     * @param eZContentObjectTreeNode[] $placements
     * @param int[] $approverIDs
     * @param int $collaborationID
     * @param callable|null $sender ( eZMail $mail ) => bool; null sends with eZMailTransport::send()
     * @param int|bool $versionID the version that waits (its name is the account's name in the mail)
     * @return int mails sent
     */
    public static function sendMail( $object, $requester, array $placements, array $approverIDs, $collaborationID, $sender = null, $versionID = false )
    {
        $ini = eZINI::instance();
        $account = eZUser::fetch( (int)$object->attribute( 'id' ) );
        // the name of the version that waits (a new account has no published name yet)
        $accountName = (string)$object->name( $versionID );
        $groupNames = array();
        foreach ( $placements as $parent )
            $groupNames[] = (string)$parent->attribute( 'name' );
        $emailSender = $ini->variable( 'MailSettings', 'EmailSender' );
        if ( !$emailSender )
            $emailSender = $ini->variable( 'MailSettings', 'AdminEmail' );
        $url = self::itemURL( $collaborationID );
        $sent = 0;
        foreach ( $approverIDs as $approverID )
        {
            $approver = eZUser::fetch( (int)$approverID );
            if ( !$approver instanceof eZUser || !eZMail::validate( (string)$approver->attribute( 'email' ) ) )
                continue;
            $tpl = eZTemplate::factory();
            $tpl->setVariable( 'approver', $approver );
            $tpl->setVariable( 'requester', $requester );
            $tpl->setVariable( 'object', $object );
            $tpl->setVariable( 'account', $account );
            $tpl->setVariable( 'account_name', $accountName );
            $tpl->setVariable( 'group_names', $groupNames );
            $tpl->setVariable( 'approval_url', $url );
            $tpl->setVariable( 'site_name', $ini->variable( 'SiteSettings', 'SiteName' ) );
            $body = $tpl->fetch( 'design:workflow/eventtype/expuserapprove_mail.tpl' );
            $subject = $tpl->hasVariable( 'subject' ) ? (string)$tpl->variable( 'subject' )
                     : ezpI18n::tr( 'kernel/workflow/event', 'A new user account waits for your approval' );
            $mail = new eZMail();
            $mail->setSender( $emailSender );
            $mail->setContentType( $ini->variable( 'MailSettings', 'ContentType' ) );
            $mail->setReceiver( (string)$approver->attribute( 'email' ) );
            $mail->setSubject( trim( $subject ) );
            if ( class_exists( 'expMailTemplateBody' ) )
                expMailTemplateBody::apply( $mail, $tpl, $body );
            else
                $mail->setBody( $body );
            $mail->setCategory( self::MAIL_CATEGORY );
            if ( is_callable( $sender ) ? call_user_func( $sender, $mail ) : eZMailTransport::send( $mail ) )
                $sent++;
        }
        return $sent;
    }

    /**
     * The workflow process an approval item of this event belongs to, or null (an item of the approval event, or one
     * whose process has ended).
     *
     * @param int $collaborationID
     * @return eZWorkflowProcess|null
     */
    public static function processOfItem( $collaborationID )
    {
        $db = eZDB::instance();
        $rows = $db->arrayQuery( 'SELECT workflow_process_id FROM ezapprove_items WHERE collaboration_id = ' . (int)$collaborationID );
        foreach ( $rows as $row )
        {
            $process = eZWorkflowProcess::fetch( (int)$row['workflow_process_id'] );
            if ( !$process instanceof eZWorkflowProcess )
                continue;
            $event = eZWorkflowEvent::fetch( (int)$process->attribute( 'event_id' ) );
            if ( $event instanceof eZWorkflowEvent && $event->attribute( 'workflow_type_string' ) === self::TYPE_STRING )
                return $process;
        }
        return null;
    }

    /**
     * The process an approval item of the plain approval event (ezapprove) belongs to when the item holds a user
     * account or user group: the section based setup (expUserApprovalSection, a Staff section on the approval
     * event), or any approval event whose sections cover accounts. Null otherwise.
     *
     * @param int $collaborationID
     * @return eZWorkflowProcess|null
     */
    public static function sectionProcessOfItem( $collaborationID )
    {
        $db = eZDB::instance();
        $rows = $db->arrayQuery( 'SELECT workflow_process_id FROM ezapprove_items WHERE collaboration_id = ' . (int)$collaborationID );
        foreach ( $rows as $row )
        {
            $process = eZWorkflowProcess::fetch( (int)$row['workflow_process_id'] );
            if ( !$process instanceof eZWorkflowProcess )
                continue;
            $event = eZWorkflowEvent::fetch( (int)$process->attribute( 'event_id' ) );
            if ( !$event instanceof eZWorkflowEvent || $event->attribute( 'workflow_type_string' ) !== 'event_ezapprove' )
                continue;
            $parameters = $process->attribute( 'parameter_list' );
            $objectID = isset( $parameters['object_id'] ) ? (int)$parameters['object_id'] : 0;
            if ( $objectID && self::isAccountOrGroup( eZContentObject::fetch( $objectID ) ) )
                return $process;
        }
        return null;
    }

    /**
     * Which approval of accounts an item belongs to: group (the event "Approve user accounts"), section (the plain
     * approval event holding a user account or group), or null (any other item).
     *
     * @param int $collaborationID
     * @return string|null
     */
    public static function modeOfItem( $collaborationID )
    {
        if ( self::processOfItem( $collaborationID ) !== null )
            return 'group';
        if ( self::sectionProcessOfItem( $collaborationID ) !== null )
            return 'section';
        return null;
    }

    /**
     * After the plain approval event (ezapprove) created an item for a user account or user group (the section based
     * setup): the mail with the link to the item in the administration to every approver, whether or not they
     * subscribed to collaboration notifications, and the audit record, as for the event "Approve user accounts".
     *
     * @param eZWorkflowProcess $process
     * @param eZWorkflowEvent $event the approval event
     * @param eZContentObject $object the account or group
     * @param int $versionID
     * @param eZUser $requester who published
     * @param int[] $approverIDs the users the event asks
     * @param callable|null $sender sendMail()'s sender (tests)
     * @return int the number of mails sent
     */
    public static function sectionRequested( $process, $event, $object, $versionID, $requester, array $approverIDs, $sender = null )
    {
        $db = eZDB::instance();
        $rows = $db->arrayQuery( 'SELECT collaboration_id FROM ezapprove_items WHERE workflow_process_id = ' . (int)$process->attribute( 'id' ) );
        $collaborationID = $rows ? (int)$rows[0]['collaboration_id'] : 0;
        // the groups the version places the account in
        $placements = array();
        $version = $object->version( (int)$versionID );
        if ( $version instanceof eZContentObjectVersion )
        {
            foreach ( (array)$version->attribute( 'node_assignments' ) as $assignment )
            {
                $parent = eZContentObjectTreeNode::fetch( (int)$assignment->attribute( 'parent_node' ) );
                if ( $parent instanceof eZContentObjectTreeNode )
                    $placements[] = $parent;
            }
        }
        $approverIDs = array_values( array_unique( array_map( 'intval', $approverIDs ) ) );
        $sent = 0;
        if ( $collaborationID )
        {
            try
            {
                $sent = self::sendMail( $object, $requester, $placements, $approverIDs, $collaborationID, $sender, (int)$versionID );
            }
            catch ( Throwable $e )
            {
                eZDebug::writeError( 'The approval mail could not be sent: ' . $e->getMessage(), __METHOD__ );
            }
        }
        if ( class_exists( 'expAuditHook' ) )
        {
            $objectID = (int)$object->attribute( 'id' );
            $groups = array();
            foreach ( $placements as $parent )
                $groups[] = array( 'node' => (int)$parent->attribute( 'node_id' ), 'name' => (string)$parent->attribute( 'name' ) );
            $workflowID = (int)$event->attribute( 'workflow_id' );
            expAuditHook::emit( 'access.user.approval.request', function () use ( $objectID, $versionID, $groups, $approverIDs, $collaborationID, $sent, $workflowID, $process ) {
                return array( 'object' => expAuditHook::userOrGroup( $objectID ),
                              'target' => array( 'type' => 'collaboration', 'id' => (int)$collaborationID ),
                              'after' => array( 'version' => (int)$versionID, 'groups' => $groups, 'approvers' => count( $approverIDs ),
                                                'mails' => $sent, 'workflow' => $workflowID,
                                                'process' => (int)$process->attribute( 'id' ), 'mode' => 'section' ) );
            } );
        }
        return $sent;
    }

    /**
     * After an approver accepted or denied an item (eZApproveCollaborationHandler): when the item is one of this event,
     * or one of the plain approval event that holds a user account or group (modeOfItem()),
     * the audit record, then the workflow continues at once (accepted: the account is published and can sign in;
     * denied: the version is a draft of its author again), as the workflow cronjob would at its next run.
     *
     * @param eZCollaborationItem $item
     * @param bool $accepted
     * @return string|null what happened; null when the item is not one of this event
     */
    public static function decided( $item, $accepted )
    {
        $collaborationID = (int)$item->attribute( 'id' );
        $mode = self::modeOfItem( $collaborationID );
        if ( $mode === null )
            return null;
        $process = $mode === 'group' ? self::processOfItem( $collaborationID ) : self::sectionProcessOfItem( $collaborationID );
        $objectID = (int)$item->attribute( 'data_int1' );
        $versionID = (int)$item->attribute( 'data_int2' );
        if ( class_exists( 'expAuditHook' ) )
        {
            $name = $accepted ? 'access.user.approval.approve' : 'access.user.approval.deny';
            expAuditHook::emit( $name, function () use ( $objectID, $versionID, $collaborationID, $process, $mode ) {
                return array( 'object' => expAuditHook::userOrGroup( $objectID ),
                              'target' => array( 'type' => 'collaboration', 'id' => $collaborationID ),
                              'after' => array( 'version' => $versionID, 'process' => (int)$process->attribute( 'id' ),
                                                'requested_by' => (int)$process->attribute( 'user_id' ), 'mode' => $mode ) );
            } );
        }
        return self::resume( (int)$process->attribute( 'id' ) );
    }

    /**
     * Runs one deferred workflow process now, as the workflow cronjob does (cronjobs/workflow.php): the process runs
     * its events; when it is done, the operation it held (the publication) is executed; when it was cancelled, it and
     * its mementos are removed. A failure leaves the process to the cronjob.
     *
     * @param int $processID
     * @return string done, cancelled, waiting, or failed
     */
    public static function resume( $processID )
    {
        $process = eZWorkflowProcess::fetch( (int)$processID );
        if ( !$process instanceof eZWorkflowProcess || (int)$process->attribute( 'status' ) !== eZWorkflow::STATUS_DEFERRED_TO_CRON )
            return 'waiting';
        $db = eZDB::instance();
        try
        {
            $db->begin();
            $workflow = eZWorkflow::fetch( $process->attribute( 'workflow_id' ) );
            $workflowEvent = $process->attribute( 'event_id' ) != 0 ? eZWorkflowEvent::fetch( $process->attribute( 'event_id' ) ) : null;
            $eventLog = array();
            $process->run( $workflow, $workflowEvent, $eventLog );
            $status = (int)$process->attribute( 'status' );
            if ( $status !== eZWorkflow::STATUS_DONE )
            {
                if ( in_array( $status, array( eZWorkflow::STATUS_RESET, eZWorkflow::STATUS_FAILED, eZWorkflow::STATUS_NONE,
                                               eZWorkflow::STATUS_CANCELLED, eZWorkflow::STATUS_BUSY ), true ) )
                {
                    if ( $bodyMemento = eZOperationMemento::fetchMain( $process->attribute( 'memento_key' ) ) )
                        $bodyMemento->remove();
                    foreach ( (array)eZOperationMemento::fetchList( $process->attribute( 'memento_key' ) ) as $memento )
                        $memento->remove();
                }
                if ( $status === eZWorkflow::STATUS_CANCELLED )
                {
                    $process->removeThis();
                    $db->commit();
                    return 'cancelled';
                }
                $process->store();
                $db->commit();
                return 'waiting';
            }
            $bodyMemento = eZOperationMemento::fetchChild( $process->attribute( 'memento_key' ) );
            $mainMemento = $bodyMemento ? $bodyMemento->attribute( 'main_memento' ) : null;
            if ( !$bodyMemento || !$mainMemento )
            {
                $db->commit();
                return 'failed';
            }
            $mementoData = $bodyMemento->data();
            $mementoData['main_memento'] = $mainMemento;
            $mementoData['skip_trigger'] = true;
            $mementoData['memento_key'] = $process->attribute( 'memento_key' );
            $bodyMemento->remove();
            $operationParameters = isset( $mementoData['parameters'] ) ? $mementoData['parameters'] : array();
            eZOperationHandler::execute( $mementoData['module_name'], $mementoData['operation_name'], $operationParameters, $mementoData );
            $process->removeThis();
            $db->commit();
            if ( class_exists( 'eZStaticCache' ) )
                eZStaticCache::executeActions();
            return 'done';
        }
        catch ( Throwable $e )
        {
            eZDebug::writeError( "Workflow process $processID could not continue, the cronjob will: " . $e->getMessage(), __METHOD__ );
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            return 'failed';
        }
    }

    // ---- setup --------------------------------------------------------------------------------------------------

    /**
     * The policies of the requesting groups' role: read the required group's node (to open it) and create user
     * accounts directly below it. Nothing else: the accounts there stay out of the requesters' reach.
     *
     * @param int[] $requiredNodeIDs the required groups' nodes
     * @param int $userClassID
     * @param int $groupClassID
     * @return array[] module, function, limitations (identifier => sorted string[])
     */
    public static function requestPolicies( array $requiredNodeIDs, $userClassID, $groupClassID )
    {
        $nodes = array_map( 'strval', array_map( 'intval', $requiredNodeIDs ) );
        sort( $nodes );
        return array( array( 'module' => 'content', 'function' => 'read', 'limitations' => array( 'Node' => $nodes ) ),
                      array( 'module' => 'content', 'function' => 'create',
                             'limitations' => array( 'Class' => array( (string)(int)$userClassID ), 'Node' => $nodes,
                                                     'ParentClass' => array( (string)(int)$groupClassID ) ) ) );
    }

    /**
     * What the setup does on this installation, with every group resolved (expMemberManagement::resolveGroup() specs).
     *
     * @param array $options required, approvers, requesters (spec lists), role (bool: give the requesters the role)
     * @return array options + required_groups, approver_groups, requester_groups (resolved), policies, errors
     */
    public static function plan( array $options = array() )
    {
        $options += array( 'required' => array( self::EDITORS ), 'approvers' => array( self::ADMINISTRATORS ),
                           'requesters' => array( self::EDITORS ), 'role' => true );
        $errors = array();
        $plan = $options;
        $plan['required_groups'] = expMemberManagement::resolveGroups( $options['required'], $errors );
        $plan['approver_groups'] = expMemberManagement::resolveGroups( $options['approvers'], $errors );
        $plan['requester_groups'] = $options['role'] ? expMemberManagement::resolveGroups( $options['requesters'], $errors ) : array();
        if ( !$plan['required_groups'] )
            $errors[] = 'no group that requires approval';
        if ( !$plan['approver_groups'] )
            $errors[] = 'no approving group';
        // An approving group below a required group would approve accounts of its own group
        foreach ( $plan['approver_groups'] as $approver )
            if ( self::placedBelow( array( $approver['path_string'] ), array_column( $plan['required_groups'], 'path_string' ) ) )
                $errors[] = "the approving group {$approver['name']} lies in a group that requires approval";
        $userClassID = (int)eZContentClass::classIDByIdentifier( 'user' );
        $groupClassID = (int)eZContentClass::classIDByIdentifier( 'user_group' );
        if ( $options['role'] && ( !$userClassID || !$groupClassID ) )
            $errors[] = 'the classes user and user_group are needed for the requesters\' role';
        $plan['policies'] = $options['role'] && $plan['required_groups']
            ? self::requestPolicies( array_column( $plan['required_groups'], 'node_id' ), $userClassID, $groupClassID ) : array();
        $plan['errors'] = array_values( array_unique( $errors ) );
        return $plan;
    }

    /**
     * The workflow of the setup (by name), or null.
     *
     * @return eZWorkflow|null
     */
    public static function findWorkflow()
    {
        foreach ( (array)eZWorkflow::fetchList() as $workflow )
            if ( $workflow->attribute( 'name' ) === self::WORKFLOW_NAME )
                return $workflow;
        return null;
    }

    /**
     * The content/publish before trigger, or null.
     *
     * @return eZTrigger|null
     */
    public static function publishTrigger()
    {
        $list = eZTrigger::fetchList( array( 'module' => 'content', 'function' => 'publish', 'connectType' => 'b' ) );
        return $list ? $list[0] : null;
    }

    /**
     * What the setup would change: each entry a line for the command's output, keyed by what it is.
     *
     * @param array $plan plan()
     * @return array workflow (create|has), trigger (create|has|add_event: another workflow runs on the trigger, the
     *               event goes first into it), event (create|update|has, in which workflow), role (create|has|null),
     *               add (policies the role lacks), assign (groups)
     */
    public static function changes( array $plan )
    {
        $trigger = self::publishTrigger();
        $workflow = self::findWorkflow();
        $target = $trigger ? eZWorkflow::fetch( (int)$trigger->attribute( 'workflow_id' ) ) : $workflow;
        $changes = array( 'workflow' => $workflow ? 'has' : 'create', 'trigger' => 'create', 'event' => 'create', 'event_object' => null,
                          'target_workflow' => $target, 'role' => null, 'role_object' => null, 'add' => array(), 'assign' => array() );
        if ( $trigger )
        {
            $changes['trigger'] = $workflow && (int)$trigger->attribute( 'workflow_id' ) === (int)$workflow->attribute( 'id' ) ? 'has' : 'add_event';
            if ( $changes['trigger'] === 'add_event' )
                $changes['workflow'] = 'none';
        }
        if ( $target instanceof eZWorkflow )
        {
            foreach ( (array)eZWorkflowEvent::fetchFilteredList( array( 'workflow_id' => (int)$target->attribute( 'id' ), 'version' => 0,
                                                                         'workflow_type_string' => self::TYPE_STRING ) ) as $event )
            {
                $changes['event_object'] = $event;
                $config = self::config( $event );
                $changes['event'] = array_diff( array_column( $plan['required_groups'], 'object_id' ), $config['required_groups'] )
                                    || array_diff( array_column( $plan['approver_groups'], 'object_id' ), $config['approve_groups'] )
                                    ? 'update' : 'has';
                break;
            }
        }
        if ( $plan['role'] )
        {
            $role = eZRole::fetchByName( self::ROLE_NAME );
            $role = $role instanceof eZRole ? $role : null;
            $changes['role'] = $role ? 'has' : 'create';
            $changes['role_object'] = $role;
            $changes['add'] = expMemberManagement::missing( $plan['policies'], $role ? expMemberManagement::policiesOf( $role ) : array() );
            foreach ( $plan['requester_groups'] as $group )
                if ( !$role || !expMemberManagement::isAssigned( $role, $group['object_id'] ) )
                    $changes['assign'][] = $group;
        }
        return $changes;
    }

    /**
     * Applies a plan without errors: the workflow with its event on the content/publish before trigger (or the event
     * first in the workflow already there), and the requesters' role. Adds only what is missing; a group already in
     * the event's lists stays.
     *
     * @param array $plan plan()
     * @param int $adminID the creator of the workflow
     * @return array changes() before applying
     */
    public static function apply( array $plan, $adminID = 14 )
    {
        $changes = self::changes( $plan );
        $db = eZDB::instance();
        $db->begin();
        $target = $changes['target_workflow'];
        if ( $changes['workflow'] === 'create' || ( !$target instanceof eZWorkflow && $changes['trigger'] === 'create' ) )
        {
            $groupID = 0;
            $groupName = self::WORKFLOW_GROUP_NAME;
            foreach ( (array)eZWorkflowGroup::fetchList() as $group )
            {
                if ( $group->attribute( 'name' ) === self::WORKFLOW_GROUP_NAME )
                    $groupID = (int)$group->attribute( 'id' );
            }
            if ( !$groupID )
            {
                $group = eZWorkflowGroup::create( $adminID );
                $group->setAttribute( 'name', $groupName );
                $group->store();
                $groupID = (int)$group->attribute( 'id' );
            }
            $workflow = eZWorkflow::create( $adminID );
            $workflow->setAttribute( 'name', self::WORKFLOW_NAME );
            $workflow->setAttribute( 'version', 0 );
            $workflow->store();
            eZWorkflowGroupLink::create( (int)$workflow->attribute( 'id' ), 0, $groupID, $groupName )->store();
            $target = $workflow;
        }
        else if ( $changes['workflow'] === 'has' && !$target instanceof eZWorkflow )
        {
            $target = self::findWorkflow();
        }
        if ( $changes['trigger'] === 'create' )
            eZTrigger::createNew( 'content', 'publish', 'b', (int)$target->attribute( 'id' ) );

        $requiredIDs = array_column( $plan['required_groups'], 'object_id' );
        $approverIDs = array_column( $plan['approver_groups'], 'object_id' );
        if ( $changes['event'] === 'create' )
        {
            $workflowID = (int)$target->attribute( 'id' );
            // first in the workflow: an account that waits for approval does not start the workflow's other events
            foreach ( (array)eZWorkflowEvent::fetchFilteredList( array( 'workflow_id' => $workflowID, 'version' => 0 ) ) as $other )
            {
                $other->setAttribute( 'placement', (int)$other->attribute( 'placement' ) + 1 );
                $other->store();
            }
            $event = eZWorkflowEvent::create( $workflowID, self::TYPE_STRING );
            $event->setAttribute( 'version', 0 );
            $event->setAttribute( 'placement', 1 );
            $event->setAttribute( 'description', 'Approve new accounts in ' . implode( ', ', array_column( $plan['required_groups'], 'name' ) ) );
            $event->setAttribute( 'data_text1', implode( ',', $requiredIDs ) );
            // the approvers need no approval themselves; the excluded groups start empty
            $event->setAttribute( 'data_text2', '' );
            $event->setAttribute( 'data_text3', '' );
            $event->setAttribute( 'data_text4', implode( ',', $approverIDs ) );
            $event->setAttribute( 'data_int1', 0 );
            $event->store();
        }
        else if ( $changes['event'] === 'update' )
        {
            $event = $changes['event_object'];
            $config = self::config( $event );
            $event->setAttribute( 'data_text1', implode( ',', array_values( array_unique( array_merge( $config['required_groups'], $requiredIDs ) ) ) ) );
            $event->setAttribute( 'data_text4', implode( ',', array_values( array_unique( array_merge( $config['approve_groups'], $approverIDs ) ) ) ) );
            $event->store();
        }

        if ( $plan['role'] )
        {
            $role = $changes['role_object'];
            if ( !$role instanceof eZRole )
            {
                $role = eZRole::create( self::ROLE_NAME );
                $role->store();
            }
            foreach ( $changes['add'] as $policy )
                $role->appendPolicy( $policy['module'], $policy['function'], $policy['limitations'] );
            foreach ( $changes['assign'] as $group )
                $role->assignToUser( $group['object_id'] );
        }
        $db->commit();
        if ( $changes['add'] || $changes['assign'] || $changes['role'] === 'create' )
        {
            eZRole::expireCache();
            eZUser::cleanupCache();
        }
        self::reset();
        return $changes;
    }

    /**
     * The setup step's guarantee (setup wizard, kickstarter): the default approval is set up. Skipped when the
     * stock groups are missing.
     *
     * @return string what happened, for the setup log
     */
    public static function ensure()
    {
        $plan = self::plan();
        if ( $plan['errors'] )
            return 'user account approval skipped: ' . implode( '; ', $plan['errors'] );
        $changes = self::apply( $plan );
        return sprintf( 'user account approval: workflow %s, trigger %s, event %s, role %s, %d polic%s added, assigned to %d group(s)',
                        $changes['workflow'], $changes['trigger'], $changes['event'], (string)$changes['role'],
                        count( $changes['add'] ), count( $changes['add'] ) === 1 ? 'y' : 'ies', count( $changes['assign'] ) );
    }
}
