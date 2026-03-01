<?php
/// ###exp_feature_g1006_ez2014.11### role/view - limit - pagenavigator #2677 ///
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

if ( defined( 'exp_feature_ENABLE_ROLE_VIEW_LIMIT' ) &&
     constant( 'exp_feature_ENABLE_ROLE_VIEW_LIMIT' ) === true )
{
    // ###exp_feature_g1006_ez2014.11### //
    // -- BEGIN -- //

    $viewParameters = array( 'offset' => 0,
                             'namefilter' => '',
                             'limit' => 50 );

    $userParameters = $Params['UserParameters'];
    $viewParameters = array_merge( $viewParameters, $userParameters );

    // use limit as viewparameter to have the possibility to get all users (old behaviour)
    // by adding (limit)/20000
    $limit = $viewParameters['limit'];
    /*$limitArray = array( 50, 10, 25, 50 );
    $limitArrayKey = eZPreferences::value( 'admin_role_item_list_limit' );

    // get user limit preference
    if ( isset( $limitArray[ $limitArrayKey ] ) )
    {
        $limit =  $limitArray[ $limitArrayKey ];
    }
    */

    $tpl->setVariable( 'view_parameters', $viewParameters );

    $tpl->setVariable( 'limit', $limit );


    $offset = $viewParameters[ 'offset' ];

    $tpl->setVariable( 'limit', $limit );

    $userArrayCount = fetchUserByRoleIdCount( $role->ID );
    $userArray = fetchUserByRoleId( $role->ID, $offset, $limit );

    // -- END -- //
}
else
{
    $userArray = $role->fetchUserByRole();
}

$policies = $role->attribute( 'policies' );
$tpl->setVariable( 'policies', $policies );
$tpl->setVariable( 'module', $Module );
$tpl->setVariable( 'role', $role );

$tpl->setVariable( 'user_array', $userArray );
// ###exp_feature_g1006_ez2014.11### //
if( isset( $userArrayCount ) )
{
    $tpl->setVariable( 'user_array_count', $userArrayCount );
}

$Module->setTitle( 'View role - ' . $role->attribute( 'name' ) );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:role/view.tpl' );
$Result['path'] = array( array( 'text' => 'Role',
                                'url' => 'role/list' ),
                         array( 'text' => $role->attribute( 'name' ),
                                'url' => false ) );



function fetchUserByRoleIdCount( $roleId )
{
    $roleId = (int) $roleId;
    $db = eZDB::instance();

    $query = "SELECT   count( ezuser_role.contentobject_id ) as count
              FROM
                     ezuser_role
              WHERE
                     ezuser_role.role_id = '$roleId';";

    $userRoleArray = $db->arrayQuery( $query );

    return $userRoleArray[0]['count'];
}


/*!
 * copiert aus eZRole und modifiziert für offset & limit
 *
\return the users and user groups assigned to the current role.
*/
function fetchUserByRoleId( $roleId, $offset = 0, $limit = 20 )
{
    $roleId = (int) $roleId;
    $db = eZDB::instance();

    $query = "SELECT
                         ezuser_role.contentobject_id as user_id,
                         ezuser_role.limit_value,
                         ezuser_role.limit_identifier,
                         ezuser_role.id
                      FROM
                         ezuser_role, ezcontentobject
                      WHERE
                        ezuser_role.contentobject_id = ezcontentobject.id
                        AND  ezuser_role.role_id = '$roleId'
                        ORDER BY ezcontentobject.name ASC
                      LIMIT $offset, $limit";

    $userRoleArray = $db->arrayQuery( $query );
    $userRoles = array();
    foreach ( $userRoleArray as $userRole )
    {
        $role = array();
        $role['user_object'] = eZContentObject::fetch( $userRole['user_id'] );
        $role['user_role_id'] = $userRole['id'];
        $role['limit_ident'] = $userRole['limit_identifier'];
        $role['limit_value'] = $userRole['limit_value'];

        $userRoles[] = $role;
    }
    return $userRoles;
}

?>
