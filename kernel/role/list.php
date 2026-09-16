<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$http = eZHTTPTool::instance();


$Module = $Params['Module'];

$offset = $Params['Offset'];

// The page sizes on offer are configured, not written here. The preference
// stores the position in that list counting from one, which is what it has
// always stored - the old 1, 2 and 3 still mean the first, second and third
// entry - so a site may change the sizes without resetting anyone's choice.
$roleLimits = eZRole::pageSizes();

$limitChoice = (int)eZPreferences::value( 'admin_role_list_limit' );
if ( $limitChoice < 1 || $limitChoice > count( $roleLimits ) )
    $limitChoice = 1;

$limit = $roleLimits[$limitChoice - 1];

if ( $http->hasPostVariable( 'RemoveButton' )  )
{
   if ( $http->hasPostVariable( 'DeleteIDArray' ) )
    {
        $deleteIDArray = $http->postVariable( 'DeleteIDArray' );
        $db = eZDB::instance();
        $db->begin();
        foreach ( $deleteIDArray as $deleteID )
        {
            eZRole::removeRole( $deleteID );
        }
        // Clear role caches.
        eZRole::expireCache();

        // Clear all content cache.
        eZContentCacheManager::clearAllContentCache();

        $db->commit();
    }
}
// Redirect to content node browse in the user tree
// Assign the role for a user or group
if ( $Module->isCurrentAction( 'AssignRole' ) )
{
    $selectedObjectIDArray = eZContentBrowse::result( 'AssignRole' );

    foreach ( $selectedObjectIDArray as $objectID )
    {
        $role->assignToUser( $objectID );
    }
    // Clear role caches.
    eZRole::expireCache();

    // Clear all content cache.
    eZContentCacheManager::clearAllContentCache();
}

if ( $http->hasPostVariable( 'NewButton' )  )
{
    $role = eZRole::createNew( );
    return $Module->redirectToView( 'edit', array( $role->attribute( 'id' ) ) );
}

// Sorted by the database, not in the browser: the list is shown a page at a
// time, so reordering the rows on screen would sort ten of however many there
// are. The column is checked against eZRole::sortColumnsForList() inside
// fetchByOffset(), so it can come straight off the address.
$userParameters = isset( $Params['UserParameters'] ) ? (array)$Params['UserParameters'] : array();

$sortField = isset( $userParameters['sort'] ) ? (string)$userParameters['sort'] : 'name';
$sortOrder = ( isset( $userParameters['dir'] ) && strtolower( $userParameters['dir'] ) === 'desc' ) ? 'desc' : 'asc';

$columns = eZRole::sortColumnsForList();
if ( !isset( $columns[$sortField] ) )
    $sortField = 'name';

$viewParameters = array( 'offset' => $offset,
                         'sort'   => $sortField,
                         'dir'    => $sortOrder );
$tpl = eZTemplate::factory();

$roles = eZRole::fetchByOffset( $offset, $limit, $asObject = true, $ignoreTemp = true, $ignoreNew = true,
                                $sortField, $sortOrder );
$roleCount = eZRole::roleCount();
// The temporary roles were fetched here and handed to the template, which has
// never used them. The list is unbounded - one row per role being edited - and
// every one of them was loaded with its policies on every view of this page.
$tpl->setVariable( 'roles', $roles );
$tpl->setVariable( 'role_count', $roleCount );
$tpl->setVariable( 'module', $Module );
$tpl->setVariable( 'view_parameters', $viewParameters );
$tpl->setVariable( 'limit', $limit );
$tpl->setVariable( 'limit_choices', $roleLimits );
$tpl->setVariable( 'limit_choice', $limitChoice );
$tpl->setVariable( 'role_sort', array( 'field'     => $sortField,
                                       'direction' => $sortOrder,
                                       'opposite'  => $sortOrder === 'asc' ? 'desc' : 'asc' ) );


$Result = array();
$Result['content'] = $tpl->fetch( 'design:role/list.tpl' );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'kernel/role', 'Role list' ) ) );
?>
