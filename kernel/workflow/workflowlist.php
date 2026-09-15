<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = $Params['Module'];
$WorkflowGroupID = null;
if ( isset( $Params["GroupID"] ) )
    $WorkflowGroupID = $Params["GroupID"];

// $execStack = eZExecutionStack::instance();
// $execStack->clear();
// $execStack->addEntry( $Module->functionURI( 'list' ),
//                       $Module->attribute( 'name' ), 'list' );

$http = eZHTTPTool::instance();

if ( $http->hasPostVariable( 'NewWorkflowButton' ) )
{
    if ( $http->hasPostVariable( "CurrentGroupID" ) )
        $GroupID = $http->postVariable( "CurrentGroupID" );
    if ( $http->hasPostVariable( "CurrentGroupName" ) )
        $GroupName = $http->postVariable( "CurrentGroupName" );
    $params = array( null, $GroupID, $GroupName );
    $Module->run( 'edit', $params );
    return;
}

/**
 * Removes a workflow and everything that belongs to it.
 *
 * Its trigger, its group links, its events and both of its versions - the
 * published one and the temporary one an unfinished edit leaves behind. Doing
 * less than this is what leaves a workflow list that looks empty over a
 * database that is not.
 *
 * @param int $workflowID
 */
function removeWorkflowCompletely( $workflowID )
{
    $workflowID = (int) $workflowID;

    eZTrigger::removeTriggerForWorkflow( $workflowID );

    // Version 0 is the published workflow and version 1 the temporary one; a
    // workflow that has never been edited has only the first.
    foreach ( array( 0, 1 ) as $version )
    {
        eZWorkflowGroupLink::removeWorkflowMembers( $workflowID, $version );

        $workflow = eZWorkflow::fetch( $workflowID, true, $version );

        // removeThis( true ) takes the events of that version with it.
        if ( $workflow instanceof eZWorkflow )
            $workflow->removeThis( true );
    }
}

if ( $http->hasPostVariable( 'DeleteButton' ) and
     $http->hasPostVariable( 'Workflow_id_checked' ) )
{
    if ( $http->hasPostVariable( 'CurrentGroupID' ) )
    {
        // If CurrentGroupID variable exist, delete in that group only:
        $groupID = $http->postVariable( 'CurrentGroupID' );
        $workflowIDs = $http->postVariable( 'Workflow_id_checked' );
        foreach ( $workflowIDs as $workflowID )
        {
            // for all workflows which are tagged for deleting:
            $workflow = eZWorkflow::fetch( $workflowID );
            if ( $workflow )
            {
                $workflowInGroups = $workflow->attribute( 'ingroup_list' );
                if ( count( $workflowInGroups ) == 1 )
                {
                    // The last group it belonged to, so it is being removed
                    // rather than unfiled: really remove it. This used only to
                    // disable it - the comment here said "delete (=disable)" -
                    // while the branch that did the removing read a post
                    // variable no form has ever sent, so nothing ever reached
                    // it and no workflow was ever actually deleted.
                    removeWorkflowCompletely( $workflowID );
                }
                else
                {
                    // if there is more than 1 group, remove only from the group:
                    eZWorkflowFunctions::removeGroup( $workflowID, 0, array( $groupID ) );
                }

            }
            else
            {
                // No row for it any more; clear whatever it left behind.
                removeWorkflowCompletely( $workflowID );
            }
        }
    }
    else
    {
        // Removed from the list rather than from a group, so there is no group
        // to leave them in.
        foreach ( (array) $http->postVariable( 'Workflow_id_checked' ) as $workflowID )
            removeWorkflowCompletely( $workflowID );
    }
}

/*$workflows = eZWorkflow::fetchList();
$workflowList = array();
foreach( array_keys( $workflows ) as $workflowID )
{
    $workflow = $workflows[$workflowID];
    $workflowList[$workflow->attribute( 'id' )] = $workflow;
}
*/
$user = eZUser::currentUser();

$list_in_group = eZWorkflowGroupLink::fetchWorkflowList( 0, $WorkflowGroupID, $asObject = true);

$workflow_list = eZWorkflow::fetchList( );

$list = array();
foreach( $workflow_list as $workflow )
{
    foreach( $list_in_group as $inGroup )
    {
        if ( $workflow->attribute( 'id' ) === $inGroup->attribute( 'workflow_id' ) )
        {
            $list[] = $workflow;
        }
    }
}

$templist_in_group = eZWorkflowGroupLink::fetchWorkflowList( 1, $WorkflowGroupID, $asObject = true);
$tempworkflow_list = eZWorkflow::fetchList( 1 );

$temp_list =array();
foreach( $tempworkflow_list as $tmpWorkflow )
{
    foreach ( $templist_in_group as $tmpInGroup )
    {
        if ( $tmpWorkflow->attribute( 'id' ) === $tmpInGroup->attribute( 'workflow_id' ) )
        {
            $temp_list[] = $tmpWorkflow;
        }
    }
}

$Module->setTitle( ezpI18n::tr( 'kernel/workflow', 'Workflow list of group' ) . ' ' . $WorkflowGroupID );

$WorkflowgroupInfo =  eZWorkflowGroup::fetch( $WorkflowGroupID );
if ( !$WorkflowgroupInfo )
{
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
}


$tpl = eZTemplate::factory();
$tpl->setVariable( "temp_workflow_list", $temp_list );
$tpl->setVariable( "group_id", $WorkflowGroupID );
$WorkflowGroupName = $WorkflowgroupInfo->attribute("name");
$tpl->setVariable( "group", $WorkflowgroupInfo );
$tpl->setVariable( "group_name", $WorkflowGroupName );
$tpl->setVariable( 'workflow_list', $list );
$tpl->setVariable( 'module', $Module );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:workflow/workflowlist.tpl' );
$Result['path'] = array( array( 'text' => ezpI18n::tr( 'kernel/workflow', 'Workflow' ),
                                'url' => false ),
                         array( 'text' => ezpI18n::tr( 'kernel/workflow', 'List' ),
                                'url' => false ) );
?>
