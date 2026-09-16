<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$http = eZHTTPTool::instance();
$Module = $Params['Module'];
$roleID = $Params['RoleID'];

$role = eZRole::fetch( $roleID );

if ( !$role )
{
    $Module->redirectTo( '/role/list/' );
    return;
}

// Redirect to role edit
if ( $http->hasPostVariable( 'EditRoleButton' ) )
{
    $Module->redirectTo( '/role/edit/' . $roleID );
    return;
}

// Redirect to content node browse in the user tree
if ( $http->hasPostVariable( 'AssignRoleButton' ) )
{
    eZContentBrowse::browse( array( 'action_name' => 'AssignRole',
                                    'from_page' => '/role/assign/' . $roleID,
                                    'cancel_page' => '/role/view/'. $roleID ),
                             $Module );

    return;
}
else if ( $http->hasPostVariable( 'AssignRoleLimitedButton' ) )
{
    $Module->redirectTo( '/role/assign/' . $roleID . '/' . $http->postVariable( 'AssignRoleType' ) );
    return;
}

// Assign the role for a user or group
if ( $Module->isCurrentAction( 'AssignRole' ) )
{
    $selectedObjectIDArray = eZContentBrowse::result( 'AssignRole' );

    $assignedUserIDArray = $role->fetchUserID();

    $db = eZDB::instance();
    $db->begin();
    foreach ( $selectedObjectIDArray as $objectID )
    {
        if ( !in_array(  $objectID, $assignedUserIDArray ) )
        {
            $role->assignToUser( $objectID );
        }
    }
    /* Clean up policy cache */
    eZUser::cleanupCache();

    // Clear role caches.
    eZRole::expireCache();

    // Clear all content cache.
    eZContentCacheManager::clearAllContentCache();

    $db->commit();
}

// Remove the role assignment
if ( $http->hasPostVariable( 'RemoveRoleAssignmentButton' ) )
{
    $idArray = $http->postVariable( "IDArray" );

    $db = eZDB::instance();
    $db->begin();
    foreach ( $idArray as $id )
    {
        $role->removeUserAssignmentByID( $id );
    }
    /* Clean up policy cache */
    eZUser::cleanupCache();

    // Clear role caches.
    eZRole::expireCache();

    // Clear all content cache.
    eZContentCacheManager::clearAllContentCache();

    $db->commit();
}

$tpl = eZTemplate::factory();

$userArray = $role->fetchUserByRole();

// The policy list is paged. $role.policies is every policy the role has, which
// is what the permission system needs and what a screen must not ask for: on an
// installation whose roles carry policies in the millions, loading them all to
// draw twenty five exhausts memory before the first row is written.
//
// The offset arrives as (policy_offset) rather than (offset), so a page that
// grows a second list later does not find the two moving together.
$userParameters = isset( $Params['UserParameters'] ) ? (array)$Params['UserParameters'] : array();

$policyLimit = (int)eZINI::instance( 'site.ini' )->variable( 'RoleSettings', 'PoliciesPerPage' );
if ( $policyLimit < 1 )
    $policyLimit = 25;

$policyOffset = isset( $userParameters['policy_offset'] ) ? (int)$userParameters['policy_offset'] : 0;
if ( $policyOffset < 0 )
    $policyOffset = 0;

$policyCount = $role->policyCount();
$policies    = $role->policyPage( $policyOffset, $policyLimit );

$tpl->setVariable( 'policy_count', $policyCount );
// Editing a role works on a temporary version, which is a row of its own with
// an id of its own. Paging must not put that id in the address: the page is
// /role/edit/<the role>, and a link to /role/edit/<the draft> edits the draft
// directly, so Apply would then write back to the wrong row.
$tpl->setVariable( 'policy_page_uri', '/role/view/' . (int)$roleID );
$tpl->setVariable( 'policy_limit', $policyLimit );
$tpl->setVariable( 'view_parameters', array_merge( array( 'policy_offset' => $policyOffset ),
                                                   $userParameters ) );
$tpl->setVariable( 'policies', $policies );
$tpl->setVariable( 'module', $Module );
$tpl->setVariable( 'role', $role );

$tpl->setVariable( 'user_array', $userArray );

$Module->setTitle( 'View role - ' . $role->attribute( 'name' ) );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:role/view.tpl' );
$Result['path'] = array( array( 'text' => 'Role',
                                'url' => 'role/list' ),
                         array( 'text' => $role->attribute( 'name' ),
                                'url' => false ) );

?>
