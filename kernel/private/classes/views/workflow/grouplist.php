<?php
/**
 * The code of kernel/workflow/grouplist.php, moved into a class (#207 stage 1). The file kernel/workflow/grouplist.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of ./kernel/workflow/grouplist.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace
{
// Kept for code outside that may call it. The page itself no longer does: it removes through a confirmation and
// Grouplist::removeGroups(), which keeps a workflow that is also in another group, removes disabled workflows too
// and takes their events and versions with them.
if ( !function_exists( 'removeSelectedGroups' ) ) {
function removeSelectedGroups( $http, &$groups, $base )
{
    if ( $http->hasPostVariable( "DeleteGroupButton" ) )
    {
        if ( eZHTTPPersistence::splitSelected( $base,
                                               $groups, $http, "id",
                                               $keepers, $rejects ) )
        {
            $groups = $keepers;
            foreach( $rejects as $reject )
            {
                $group_id = $reject->attribute("id");

                // Remove all workflows in current group
                $list_in_group = eZWorkflowGroupLink::fetchWorkflowList( 0, $group_id, $asObject = true);
                $workflow_list = eZWorkflow::fetchList( );

                $list = array();
                foreach( $workflow_list as $workflow )
                {
                    foreach( $list_in_group as $group )
                    {
                        $id = $workflow->attribute("id");
                        $workflow_id = $group->attribute("workflow_id");
                        if ( $id === $workflow_id )
                        {
                            $list[] = $workflow;
                        }
                    }
                }
                foreach ( $list as $workFlow )
                {
                  eZTrigger::removeTriggerForWorkflow( $workFlow->attribute( 'id' ) );
                  $workFlow->remove();
                }

                $reject->remove( );
                eZWorkflowGroupLink::removeGroupMembers( $group_id );
            }
        }
    }
}
}
}


namespace Exponential\View\Kernel\Workflow
{

class Grouplist extends \Exponential\Runnable\ModuleView
{
    /** The session variable that carries the result of a removal over the redirect back to the list. */
    const FEEDBACK_KEY = 'eZWorkflowGroupListFeedback';

    /** The translation context of the texts the view writes. */
    const CONTEXT = 'design/admin/workflow/grouplist';

    /**
     * Group ids from a form: whole positive numbers, once each.
     *
     * @param mixed $value
     * @return int[]
     */
    public static function idList( $value )
    {
        return \Exponential\View\Kernel\Rss\ListView::idList( $value );
    }

    /**
     * Every link of a published workflow to a group. One query.
     *
     * @return array of hash( workflow_id, group_id )
     */
    public static function links()
    {
        $rows = \eZDB::instance()->arrayQuery( 'SELECT workflow_id, group_id FROM ezworkflow_group_link WHERE workflow_version = 0' );
        $links = array();
        foreach ( is_array( $rows ) ? $rows : array() as $row )
        {
            $row = array_change_key_case( $row, CASE_LOWER );
            $links[] = array( 'workflow_id' => (int)$row['workflow_id'], 'group_id' => (int)$row['group_id'] );
        }
        return $links;
    }

    /**
     * What removing groups does to their workflows. A workflow that belongs only to groups being removed is
     * removed with them (with its trigger, events and versions), as the list always did; one that also belongs to
     * a group that stays only leaves the removed groups, as workflow/workflowlist does when a workflow is removed
     * from one of its groups.
     *
     * @param array $links links()
     * @param int[] $groupIDs the groups to remove
     * @return array remove (workflow ids), unlink (workflow id => ids of the groups it stays in)
     */
    public static function removalPlan( array $links, array $groupIDs )
    {
        $groupsOf = array();
        foreach ( $links as $link )
            $groupsOf[$link['workflow_id']][$link['group_id']] = true;
        $plan = array( 'remove' => array(), 'unlink' => array() );
        foreach ( $groupsOf as $workflowID => $groups )
        {
            $groups = array_keys( $groups );
            if ( !array_intersect( $groups, $groupIDs ) )
                continue;
            $staying = array_values( array_diff( $groups, $groupIDs ) );
            if ( $staying )
                $plan['unlink'][$workflowID] = $staying;
            else
                $plan['remove'][] = $workflowID;
        }
        sort( $plan['remove'] );
        ksort( $plan['unlink'] );
        return $plan;
    }

    /**
     * The trigger labels of each workflow ("Before publishing content").
     *
     * @return array workflow id => string[]
     */
    public static function triggersByWorkflow()
    {
        $labels = array();
        foreach ( (array)\eZTrigger::fetchList() as $trigger )
        {
            $function = (string)$trigger->attribute( 'function_name' );
            $labels[(int)$trigger->attribute( 'workflow_id' )][] = Processlist::triggerLabel(
                (string)$trigger->attribute( 'module_name' ), $function,
                ( $trigger->attribute( 'connect_type' ) === 'b' ? 'pre_' : 'post_' ) . $function );
        }
        return $labels;
    }

    /**
     * One card per group: its workflows with their events, triggers, waiting processes and other groups, when it
     * or one of its workflows last changed, and what removing it would do.
     *
     * @param array $groups eZWorkflowGroup objects (this page)
     * @param array $links links()
     * @param array $facts workflow id => hash( id, name, enabled, events, modified ) (workflowFacts())
     * @param array $triggers triggersByWorkflow()
     * @param array $waiting workflow id => waiting processes
     * @param array $groupNames group id => name, of every group
     * @return array group id => hash
     */
    public static function overview( array $groups, array $links, array $facts, array $triggers, array $waiting, array $groupNames )
    {
        $workflowsOf = array();
        $groupsOf = array();
        foreach ( $links as $link )
        {
            $workflowsOf[$link['group_id']][] = $link['workflow_id'];
            $groupsOf[$link['workflow_id']][] = $link['group_id'];
        }
        $overview = array();
        foreach ( $groups as $group )
        {
            $groupID = (int)$group->attribute( 'id' );
            $plan = self::removalPlan( $links, array( $groupID ) );
            $workflows = array();
            $lastModified = (int)$group->attribute( 'modified' );
            $search = array( mb_strtolower( (string)$group->attribute( 'name' ) ), (string)$groupID );
            $info = array( 'workflows' => array(), 'workflow_count' => 0, 'enabled' => 0, 'triggered' => 0, 'waiting' => 0,
                           'removes' => count( $plan['remove'] ), 'unlinks' => count( $plan['unlink'] ),
                           'removes_triggered' => 0, 'removes_waiting' => 0 );
            foreach ( array_unique( isset( $workflowsOf[$groupID] ) ? $workflowsOf[$groupID] : array() ) as $workflowID )
            {
                if ( !isset( $facts[$workflowID] ) )
                    continue;
                $fact = $facts[$workflowID];
                $others = array();
                foreach ( $groupsOf[$workflowID] as $otherID )
                    if ( $otherID !== $groupID && isset( $groupNames[$otherID] ) )
                        $others[] = array( 'id' => $otherID, 'name' => $groupNames[$otherID] );
                $labels = isset( $triggers[$workflowID] ) ? $triggers[$workflowID] : array();
                $wait = isset( $waiting[$workflowID] ) ? (int)$waiting[$workflowID] : 0;
                $workflows[] = array( 'id' => $workflowID, 'name' => $fact['name'], 'enabled' => $fact['enabled'],
                                      'events' => $fact['events'], 'modified' => $fact['modified'],
                                      'triggers' => $labels, 'waiting' => $wait, 'other_groups' => $others,
                                      'removed_with_group' => in_array( $workflowID, $plan['remove'], true ) );
                $info['workflow_count']++;
                if ( $fact['enabled'] )
                    $info['enabled']++;
                if ( $labels )
                    $info['triggered']++;
                $info['waiting'] += $wait;
                if ( in_array( $workflowID, $plan['remove'], true ) )
                {
                    if ( $labels )
                        $info['removes_triggered']++;
                    $info['removes_waiting'] += $wait;
                }
                $lastModified = max( $lastModified, $fact['modified'] );
                $search[] = mb_strtolower( $fact['name'] );
                foreach ( $labels as $label )
                    $search[] = mb_strtolower( $label );
            }
            usort( $workflows, function ( $a, $b ) { return strcasecmp( $a['name'], $b['name'] ); } );
            $info['workflows'] = $workflows;
            $info['last_modified'] = $lastModified;
            $info['search'] = implode( ' ', $search );
            $overview[$groupID] = $info;
        }
        return $overview;
    }

    /**
     * Removes groups as removalPlan() says, in one transaction.
     *
     * @param int[] $groupIDs
     * @return array names (of the groups removed), removed (workflows), unlinked (workflows)
     */
    public static function removeGroups( array $groupIDs )
    {
        $plan = self::removalPlan( self::links(), $groupIDs );
        $names = array();
        $db = \eZDB::instance();
        $db->begin();
        foreach ( $plan['remove'] as $workflowID )
            self::removeWorkflowCompletely( $workflowID );
        foreach ( $plan['unlink'] as $workflowID => $staying )
            foreach ( $groupIDs as $groupID )
                \eZWorkflowGroupLink::removeByID( $workflowID, 0, $groupID );
        foreach ( $groupIDs as $groupID )
        {
            $group = \eZWorkflowGroup::fetch( $groupID );
            if ( $group )
            {
                $names[] = (string)$group->attribute( 'name' );
                $group->remove();
            }
            \eZWorkflowGroupLink::removeGroupMembers( $groupID );
        }
        $db->commit();
        return array( 'names' => $names, 'removed' => count( $plan['remove'] ), 'unlinked' => count( $plan['unlink'] ) );
    }

    /**
     * A workflow and everything that belongs to it: its trigger, its group links, its events and both versions.
     * The same as workflow/workflowlist does.
     *
     * @param int $workflowID
     */
    public static function removeWorkflowCompletely( $workflowID )
    {
        $workflowID = (int)$workflowID;
        \eZTrigger::removeTriggerForWorkflow( $workflowID );
        foreach ( array( 0, 1 ) as $version )
        {
            \eZWorkflowGroupLink::removeWorkflowMembers( $workflowID, $version );
            $workflow = \eZWorkflow::fetch( $workflowID, true, $version );
            if ( $workflow instanceof \eZWorkflow )
                $workflow->removeThis( true );
        }
    }

    /**
     * The names of every workflow group.
     *
     * @return array group id => name
     */
    public static function groupNames()
    {
        $names = array();
        foreach ( (array)\eZWorkflowGroup::fetchList( true ) as $group )
            $names[(int)$group->attribute( 'id' )] = (string)$group->attribute( 'name' );
        return $names;
    }

    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];

        $http = \eZHTTPTool::instance();

        if ( $http->hasPostVariable( "EditGroupButton" ) && $http->hasPostVariable( "EditGroupID" ) )
        {
            $Module->redirectTo( $Module->functionURI( "groupedit" ) . "/" . $http->postVariable( "EditGroupID" ) );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        if ( $http->hasPostVariable( "NewGroupButton" ) )
        {
            $params = array();

            $Module->redirectTo( $Module->functionURI( "groupedit" ) );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        $feedback = $http->hasSessionVariable( self::FEEDBACK_KEY ) ? $http->sessionVariable( self::FEEDBACK_KEY ) : false;
        if ( $feedback !== false )
            $http->removeSessionVariable( self::FEEDBACK_KEY );

        // Removing takes two steps. Remove selected only shows what would go: the workflows removed with the
        // groups, their triggers and waiting processes, and the workflows that stay in another group
        // (design:workflow/confirmremovegroup.tpl). Its button posts the same ids with ConfirmRemoveButton, and
        // only then is anything removed; the result comes back after a redirect.
        if ( $http->hasPostVariable( 'DeleteGroupButton' ) )
        {
            $ids = self::idList( $http->hasPostVariable( 'ContentClass_id_checked' ) ? $http->postVariable( 'ContentClass_id_checked' ) : array() );
            $groups = array();
            foreach ( $ids as $id )
            {
                $group = \eZWorkflowGroup::fetch( $id );
                if ( $group )
                    $groups[] = $group;
            }
            if ( !$groups )
            {
                $feedback = array( 'type' => $ids ? 'gone' : 'none_selected' );
            }
            else if ( !$http->hasPostVariable( 'ConfirmRemoveButton' ) )
            {
                $groupIDs = array();
                foreach ( $groups as $group )
                    $groupIDs[] = (int)$group->attribute( 'id' );
                $links = self::links();
                $facts = \Exponential\View\Kernel\Trigger\ListView::workflowFacts();
                $waiting = \Exponential\View\Kernel\Trigger\ListView::waitingByWorkflow();
                $tpl = \eZTemplate::factory();
                $tpl->setVariable( 'module', $Module );
                $tpl->setVariable( 'remove_groups', $groups );
                $tpl->setVariable( 'remove_overview', self::overview( $groups, $links, $facts, self::triggersByWorkflow(), $waiting, self::groupNames() ) );
                $tpl->setVariable( 'remove_plan', self::removalPlan( $links, $groupIDs ) );
                $Result = array();
                $Result['content'] = $tpl->fetch( 'design:workflow/confirmremovegroup.tpl' );
                $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/workflow', 'Workflow' ), 'url' => false ),
                                         array( 'text' => \ezpI18n::tr( 'kernel/workflow', 'Group list' ), 'url' => 'workflow/grouplist' ),
                                         array( 'text' => \ezpI18n::tr( self::CONTEXT, 'Confirm removal' ), 'url' => false ) );
                return $this->viewResult( $Result, null );
            }
            else
            {
                $groupIDs = array();
                foreach ( $groups as $group )
                    $groupIDs[] = (int)$group->attribute( 'id' );
                $done = self::removeGroups( $groupIDs );
                $http->setSessionVariable( self::FEEDBACK_KEY, array( 'type' => 'removed' ) + $done );
                $Module->redirectTo( $Module->functionURI( 'grouplist' ) );
                return $this->viewResult( null, null );
            }
        }

        $Module->setTitle( \ezpI18n::tr( 'kernel/workflow', 'Workflow group list' ) );
        $tpl = \eZTemplate::factory();

        $list = \eZWorkflowGroup::fetchList( true );

        $groupCount  = count( $list );
        $groupLimit  = \expAdminPagination::limit( 'workflow/grouplist' );
        $groupOffset = \expAdminPagination::offset( $Params );
        $list        = \expAdminPagination::page( $list, $groupOffset, $groupLimit );

        $tpl->setVariable( "group_count", $groupCount );
        $tpl->setVariable( "limit", $groupLimit );
        $tpl->setVariable( "view_parameters", array( 'offset' => $groupOffset ) );
        $tpl->setVariable( "groups", $list );
        $tpl->setVariable( "module", $Module );

        $links = self::links();
        $facts = \Exponential\View\Kernel\Trigger\ListView::workflowFacts();
        $waiting = \Exponential\View\Kernel\Trigger\ListView::waitingByWorkflow();
        $grouped = array();
        foreach ( $links as $link )
            $grouped[$link['workflow_id']] = true;
        $summary = array( 'groups' => $groupCount, 'workflows' => count( $facts ), 'enabled' => 0, 'triggered' => 0,
                          'ungrouped' => 0, 'waiting' => array_sum( $waiting ) );
        $triggers = self::triggersByWorkflow();
        foreach ( $facts as $id => $fact )
        {
            if ( $fact['enabled'] )
                $summary['enabled']++;
            if ( isset( $triggers[$id] ) )
                $summary['triggered']++;
            if ( !isset( $grouped[$id] ) )
                $summary['ungrouped']++;
        }
        $tpl->setVariable( 'group_overview', self::overview( $list, $links, $facts, $triggers, $waiting, self::groupNames() ) );
        $tpl->setVariable( 'group_summary', $summary );
        $tpl->setVariable( 'group_feedback', $feedback );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:workflow/grouplist.tpl" );
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/workflow', 'Workflow' ),
                                        'url' => false ),
                                 array( 'text' => \ezpI18n::tr( 'kernel/workflow', 'Group list' ),
                                        'url' => false ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
