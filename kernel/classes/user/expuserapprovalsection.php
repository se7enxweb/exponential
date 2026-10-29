<?php
/**
 * File containing the expUserApprovalSection class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The section based approval of new staff accounts (doc/eng-us/bc/6.0/user-account-approval.md, "Two ways"): the
 * optional alternative to the event "Approve user accounts" (expUserApproval, the default), built only from what the
 * kernel has had for a long time:
 * - a section "Staff" (identifier staff) assigned to the Administrator users and Editors groups, so every account in
 *   them, and every new one, is in that section (Members stay in Users);
 * - the plain approval event (ezapprove) on content/publish before: section Staff, approvers and the group that needs
 *   no approval the Administrator users, new objects only (the first version).
 * The approvers get the same mail with the link to the item in the administration (expUserApproval::sectionRequested()),
 * approve and deny are audited and continue the workflow at once (expUserApproval::decided()).
 *
 * Opt-in: bin/php/adduserapproval.php --mode=section (a dry run unless --apply), or the setup step with site.ini
 * [UserSettings] AccountApprovalMode=section. Choose one of the two: when the event "Approve user accounts" runs on
 * content/publish before, an account would be asked twice, and the plan refuses (plan() errors).
 *
 * staffGroupSpecs(), eventFields() and sectionPolicyWarnings() work on plain values and are tested without a database.
 */
class expUserApprovalSection
{
    const SECTION_NAME = 'Staff';
    const SECTION_IDENTIFIER = 'staff';
    const NAVIGATION_PART = 'ezusernavigationpart';

    const WORKFLOW_NAME = 'Approve new staff accounts';
    const EVENT_DESCRIPTION = 'Approve new accounts in section Staff';

    /**
     * The default groups of the section: Administrator users and Editors (remote ids of the stock groups).
     *
     * @return string[] expMemberManagement::resolveGroup() specs
     */
    public static function staffGroupSpecs()
    {
        return array( expUserApproval::ADMINISTRATORS, expUserApproval::EDITORS );
    }

    /**
     * The storage fields of the approval event (eZApproveType: data_text1 sections, data_text2 groups without approval,
     * data_text3 approving users, data_text4 approving groups, data_int2 languages, data_int3 version option).
     *
     * @param int $sectionID
     * @param int[] $approverGroupIDs
     * @param int[] $exemptGroupIDs
     * @return array field => value
     */
    public static function eventFields( $sectionID, array $approverGroupIDs, array $exemptGroupIDs )
    {
        return array( 'description' => self::EVENT_DESCRIPTION,
                      'data_text1' => (string)(int)$sectionID,
                      'data_text2' => implode( ',', expUserApproval::idList( $exemptGroupIDs ) ),
                      'data_text3' => '',
                      'data_text4' => implode( ',', expUserApproval::idList( $approverGroupIDs ) ),
                      'data_int2' => 0,
                      'data_int3' => eZApproveType::VERSION_OPTION_FIRST_ONLY );
    }

    /**
     * The policies limited to a section the staff accounts leave: after the change they no longer cover those
     * accounts, so the command lists them for a look.
     *
     * @param array[] $rows role, module, function, section (rows of the policies with a Section limitation)
     * @param int[] $leftSectionIDs the sections the staff groups are in now
     * @return string[] one line per policy
     */
    public static function sectionPolicyWarnings( array $rows, array $leftSectionIDs )
    {
        $left = array_map( 'intval', $leftSectionIDs );
        $lines = array();
        foreach ( $rows as $row )
        {
            if ( !in_array( (int)$row['section'], $left, true ) )
                continue;
            $lines[] = sprintf( 'role "%s": %s/%s limited to section %d', $row['role'], $row['module'], $row['function'], (int)$row['section'] );
        }
        return array_values( array_unique( $lines ) );
    }

    /**
     * The section Staff, or null.
     *
     * @return eZSection|null
     */
    public static function findSection()
    {
        $section = eZSection::fetchByIdentifier( self::SECTION_IDENTIFIER );
        return $section instanceof eZSection ? $section : null;
    }

    /**
     * The event "Approve user accounts" (the group based approval) in the workflow content/publish before runs, or
     * null: the two ways together would ask twice for one account.
     *
     * @return eZWorkflowEvent|null
     */
    public static function groupEventOnTrigger()
    {
        $trigger = expUserApproval::publishTrigger();
        if ( !$trigger )
            return null;
        $events = eZWorkflowEvent::fetchFilteredList( array( 'workflow_id' => (int)$trigger->attribute( 'workflow_id' ), 'version' => 0,
                                                             'workflow_type_string' => expUserApproval::TYPE_STRING ) );
        return $events ? $events[0] : null;
    }

    /**
     * The approval event of this setup in a workflow (an ezapprove event whose sections include Staff), or null.
     *
     * @param int $workflowID
     * @param int $sectionID
     * @return eZWorkflowEvent|null
     */
    public static function findEvent( $workflowID, $sectionID )
    {
        if ( !$sectionID )
            return null;
        foreach ( (array)eZWorkflowEvent::fetchFilteredList( array( 'workflow_id' => (int)$workflowID, 'version' => 0,
                                                                     'workflow_type_string' => 'event_ezapprove' ) ) as $event )
        {
            if ( in_array( (int)$sectionID, expUserApproval::idList( $event->attribute( 'data_text1' ) ), true ) )
                return $event;
        }
        return null;
    }

    /**
     * The section based setup on a trigger, when there is one: the event (any ezapprove event whose sections include
     * Staff) in the workflow content/publish before runs, or null. The group based setup's command warns with it.
     *
     * @return eZWorkflowEvent|null
     */
    public static function eventOnTrigger()
    {
        $section = self::findSection();
        $trigger = expUserApproval::publishTrigger();
        return $section && $trigger ? self::findEvent( (int)$trigger->attribute( 'workflow_id' ), (int)$section->attribute( 'id' ) ) : null;
    }

    /**
     * What the setup does on this installation, with every group resolved.
     *
     * @param array $options staff, approvers, exempt (expMemberManagement::resolveGroup() spec lists)
     * @return array options + staff_groups, approver_groups, exempt_groups, errors, warnings
     */
    public static function plan( array $options = array() )
    {
        $options += array( 'staff' => self::staffGroupSpecs(), 'approvers' => array( expUserApproval::ADMINISTRATORS ),
                           'exempt' => array( expUserApproval::ADMINISTRATORS ) );
        $errors = array();
        $plan = $options;
        $plan['staff_groups'] = expMemberManagement::resolveGroups( $options['staff'], $errors );
        $plan['approver_groups'] = expMemberManagement::resolveGroups( $options['approvers'], $errors );
        $plan['exempt_groups'] = expMemberManagement::resolveGroups( $options['exempt'], $errors );
        if ( !$plan['staff_groups'] )
            $errors[] = 'no group for the section ' . self::SECTION_NAME;
        if ( !$plan['approver_groups'] )
            $errors[] = 'no approving group';
        $groupEvent = self::groupEventOnTrigger();
        if ( $groupEvent )
            $errors[] = 'the event "Approve user accounts" (group based approval, event ' . (int)$groupEvent->attribute( 'id' ) . ') runs on '
                      . 'content/publish before: with both, an account would be asked for twice. Choose one: remove that event in '
                      . 'Setup > Workflows first, or keep the group based approval';
        $plan['warnings'] = array();
        $section = self::findSection();
        $current = array();
        foreach ( $plan['staff_groups'] as $group )
        {
            $object = eZContentObject::fetch( (int)$group['object_id'] );
            if ( $object && ( !$section || (int)$object->attribute( 'section_id' ) !== (int)$section->attribute( 'id' ) ) )
                $current[] = (int)$object->attribute( 'section_id' );
        }
        if ( $current )
            $plan['warnings'] = self::sectionPolicyWarnings( self::sectionPolicyRows(), array_unique( $current ) );
        $plan['errors'] = array_values( array_unique( $errors ) );
        return $plan;
    }

    /**
     * The policies with a Section limitation, one row per section value.
     *
     * @return array[] role, module, function, section
     */
    public static function sectionPolicyRows()
    {
        $db = eZDB::instance();
        $rows = $db->arrayQuery( "SELECT r.name AS role, p.module_name AS module, p.function_name AS fn, v.value AS section
                                  FROM ezrole r, ezpolicy p, ezpolicy_limitation l, ezpolicy_limitation_value v
                                  WHERE p.role_id = r.id AND l.policy_id = p.id AND v.limitation_id = l.id
                                    AND r.version = 0 AND l.identifier = 'Section'" );
        $out = array();
        foreach ( (array)$rows as $row )
            $out[] = array( 'role' => $row['role'], 'module' => $row['module'], 'function' => $row['fn'], 'section' => (int)$row['section'] );
        return $out;
    }

    /**
     * What the setup would change.
     *
     * @param array $plan plan()
     * @return array section (create|has), section_object, assign (groups with the number of objects that change
     *               section: group, objects), workflow (create|has|none), trigger (create|has|add_event), target_workflow,
     *               event (create|update|has), event_object
     */
    public static function changes( array $plan )
    {
        $section = self::findSection();
        $changes = array( 'section' => $section ? 'has' : 'create', 'section_object' => $section, 'assign' => array(),
                          'workflow' => 'create', 'trigger' => 'create', 'target_workflow' => null, 'event' => 'create', 'event_object' => null );
        $sectionID = $section ? (int)$section->attribute( 'id' ) : 0;
        foreach ( $plan['staff_groups'] as $group )
        {
            $node = eZContentObjectTreeNode::fetch( (int)$group['node_id'] );
            $object = $node ? $node->object() : null;
            if ( !$object )
                continue;
            $objectIDs = eZContentObjectTreeNode::sectionAssignmentObjectIDs( $node, eZSection::SCOPE_SUBTREE, false );
            $count = 0;
            if ( $objectIDs )
            {
                $db = eZDB::instance();
                $rows = $db->arrayQuery( 'SELECT COUNT(*) AS n FROM ezcontentobject WHERE section_id <> ' . $sectionID . ' AND '
                                         . $db->generateSQLINStatement( $objectIDs, 'id', false, true, 'int' ) );
                $count = $rows ? (int)$rows[0]['n'] : 0;
            }
            if ( $count > 0 )
                $changes['assign'][] = array( 'group' => $group, 'objects' => $count );
        }
        $trigger = expUserApproval::publishTrigger();
        $workflow = null;
        foreach ( (array)eZWorkflow::fetchList() as $candidate )
            if ( $candidate->attribute( 'name' ) === self::WORKFLOW_NAME )
                $workflow = $candidate;
        if ( $workflow )
            $changes['workflow'] = 'has';
        $target = $trigger ? eZWorkflow::fetch( (int)$trigger->attribute( 'workflow_id' ) ) : $workflow;
        if ( $trigger )
        {
            $changes['trigger'] = $workflow && (int)$trigger->attribute( 'workflow_id' ) === (int)$workflow->attribute( 'id' ) ? 'has' : 'add_event';
            if ( $changes['trigger'] === 'add_event' )
                $changes['workflow'] = 'none';
        }
        $changes['target_workflow'] = $target instanceof eZWorkflow ? $target : null;
        if ( $target instanceof eZWorkflow && $sectionID )
        {
            $event = self::findEvent( (int)$target->attribute( 'id' ), $sectionID );
            if ( $event )
            {
                $changes['event_object'] = $event;
                $groups = expUserApproval::idList( $event->attribute( 'data_text4' ) );
                $exempt = expUserApproval::idList( $event->attribute( 'data_text2' ) );
                $changes['event'] = array_diff( array_map( 'intval', array_column( $plan['approver_groups'], 'object_id' ) ), $groups )
                                    || array_diff( array_map( 'intval', array_column( $plan['exempt_groups'], 'object_id' ) ), $exempt )
                                    ? 'update' : 'has';
            }
        }
        return $changes;
    }

    /**
     * Applies a plan without errors: the section, its assignment to the groups' subtrees (objects that had the
     * group's section), the workflow with the approval event on content/publish before (or the event first in the
     * workflow already there). Adds only what is missing.
     *
     * @param array $plan plan()
     * @param int $adminID the creator of the workflow
     * @return array changes() before applying
     */
    public static function apply( array $plan, $adminID = 14 )
    {
        if ( $plan['errors'] )
            throw new RuntimeException( 'The plan has errors: ' . implode( '; ', $plan['errors'] ) );
        $changes = self::changes( $plan );
        $db = eZDB::instance();
        $db->begin();
        $section = $changes['section_object'];
        if ( !$section instanceof eZSection )
        {
            $section = new eZSection( array( 'name' => self::SECTION_NAME, 'identifier' => self::SECTION_IDENTIFIER,
                                             'navigation_part_identifier' => self::NAVIGATION_PART, 'locale' => '' ) );
            $section->store();
        }
        $sectionID = (int)$section->attribute( 'id' );
        foreach ( $changes['assign'] as $assign )
        {
            $node = eZContentObjectTreeNode::fetch( (int)$assign['group']['node_id'] );
            $object = $node ? $node->object() : null;
            if ( $object )
                eZContentObjectTreeNode::assignSectionToSubTree( (int)$node->attribute( 'node_id' ), $sectionID, (int)$object->attribute( 'section_id' ) );
        }

        $target = $changes['target_workflow'];
        if ( !$target instanceof eZWorkflow )
        {
            $groupID = 0;
            foreach ( (array)eZWorkflowGroup::fetchList() as $group )
                if ( $group->attribute( 'name' ) === expUserApproval::WORKFLOW_GROUP_NAME )
                    $groupID = (int)$group->attribute( 'id' );
            if ( !$groupID )
            {
                $group = eZWorkflowGroup::create( $adminID );
                $group->setAttribute( 'name', expUserApproval::WORKFLOW_GROUP_NAME );
                $group->store();
                $groupID = (int)$group->attribute( 'id' );
            }
            $target = eZWorkflow::create( $adminID );
            $target->setAttribute( 'name', self::WORKFLOW_NAME );
            $target->setAttribute( 'version', 0 );
            $target->store();
            eZWorkflowGroupLink::create( (int)$target->attribute( 'id' ), 0, $groupID, expUserApproval::WORKFLOW_GROUP_NAME )->store();
        }
        if ( $changes['trigger'] === 'create' )
            eZTrigger::createNew( 'content', 'publish', 'b', (int)$target->attribute( 'id' ) );

        $approverIDs = array_map( 'intval', array_column( $plan['approver_groups'], 'object_id' ) );
        $exemptIDs = array_map( 'intval', array_column( $plan['exempt_groups'], 'object_id' ) );
        $event = self::findEvent( (int)$target->attribute( 'id' ), $sectionID );
        if ( !$event )
        {
            $workflowID = (int)$target->attribute( 'id' );
            // first in the workflow: an account that waits does not start the workflow's other events
            foreach ( (array)eZWorkflowEvent::fetchFilteredList( array( 'workflow_id' => $workflowID, 'version' => 0 ) ) as $other )
            {
                $other->setAttribute( 'placement', (int)$other->attribute( 'placement' ) + 1 );
                $other->store();
            }
            $event = eZWorkflowEvent::create( $workflowID, 'event_ezapprove' );
            $event->setAttribute( 'version', 0 );
            $event->setAttribute( 'placement', 1 );
            foreach ( self::eventFields( $sectionID, $approverIDs, $exemptIDs ) as $field => $value )
                $event->setAttribute( $field, $value );
            $event->store();
        }
        else
        {
            $event->setAttribute( 'data_text4', implode( ',', expUserApproval::idList( array_merge( expUserApproval::idList( $event->attribute( 'data_text4' ) ), $approverIDs ) ) ) );
            $event->setAttribute( 'data_text2', implode( ',', expUserApproval::idList( array_merge( expUserApproval::idList( $event->attribute( 'data_text2' ) ), $exemptIDs ) ) ) );
            $event->store();
        }
        $db->commit();
        if ( $changes['assign'] )
        {
            eZRole::expireCache();
            eZUser::cleanupCache();
        }
        return $changes;
    }

    /**
     * The setup step's opt-in (site.ini [UserSettings] AccountApprovalMode=section): the section based setup.
     *
     * @return string what happened, for the setup log
     */
    public static function ensure()
    {
        $plan = self::plan();
        if ( $plan['errors'] )
            return 'section based account approval skipped: ' . implode( '; ', $plan['errors'] );
        $changes = self::apply( $plan );
        return sprintf( 'section based account approval: section %s, %d group(s) assigned, workflow %s, trigger %s, event %s',
                        $changes['section'], count( $changes['assign'] ), $changes['workflow'], $changes['trigger'], $changes['event'] );
    }

    /**
     * Which setup the setup step runs: site.ini [UserSettings] AccountApprovalMode (group, the default; section; or
     * none).
     *
     * @param string|null $value null reads the setting
     * @return string group, section or none
     */
    public static function mode( $value = null )
    {
        if ( $value === null )
        {
            $ini = eZINI::instance();
            $value = $ini->hasVariable( 'UserSettings', 'AccountApprovalMode' ) ? $ini->variable( 'UserSettings', 'AccountApprovalMode' ) : 'group';
        }
        $value = strtolower( trim( (string)$value ) );
        return in_array( $value, array( 'group', 'section', 'none' ), true ) ? $value : 'group';
    }
}
