<?php
/**
 * The code of kernel/role/list.php, moved into a class (#207 stage 1). The file kernel/role/list.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/role/list.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Role
{

class ListView extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $http = \eZHTTPTool::instance();


        $Module = $Params['Module'];

        $offset = $Params['Offset'];

        // The page sizes on offer are configured, not written here. The preference
        // stores the position in that list counting from one, which is what it has
        // always stored - the old 1, 2 and 3 still mean the first, second and third
        // entry - so a site may change the sizes without resetting anyone's choice.
        $roleLimits = \eZRole::pageSizes();

        $limitChoice = (int)\eZPreferences::value( 'admin_role_list_limit' );
        if ( $limitChoice < 1 || $limitChoice > count( $roleLimits ) )
            $limitChoice = 1;

        $limit = $roleLimits[$limitChoice - 1];

        // How many roles Remove selected removed: false when it was not pressed, 0 when nothing was ticked
        $removedCount = false;
        if ( $http->hasPostVariable( 'RemoveButton' )  )
        {
            $removedCount = 0;
            if ( $http->hasPostVariable( 'DeleteIDArray' ) )
            {
                $deleteIDArray = array();
                foreach ( (array)$http->postVariable( 'DeleteIDArray' ) as $deleteID )
                {
                    // published roles only: a draft is removed by its editor's Cancel
                    $deleteRole = is_scalar( $deleteID ) && ctype_digit( (string)$deleteID ) ? \eZRole::fetch( (int)$deleteID ) : null;
                    if ( $deleteRole instanceof \eZRole && (int)$deleteRole->attribute( 'version' ) === 0 )
                        $deleteIDArray[(int)$deleteID] = (int)$deleteID;
                }
                $removedCount = count( $deleteIDArray );
                $db = \eZDB::instance();
                $db->begin();
                foreach ( $deleteIDArray as $deleteID )
                {
                    \eZRole::removeRole( $deleteID );
                }
                // Clear role caches.
                \eZRole::expireCache();

                // Clear all content cache.
                \eZContentCacheManager::clearAllContentCache();

                $db->commit();
            }
        }
        // An AssignRole browse result that came back here names no role ($role was never set, so it ended in a
        // fatal error). Assigning is done by role/assign/<role>, which the browse of role/view returns to.
        if ( $Module->isCurrentAction( 'AssignRole' ) )
        {
            \eZDebug::writeWarning( 'An AssignRole result reached role/list, which names no role; nothing assigned', 'role/list' );
        }

        if ( $http->hasPostVariable( 'NewButton' )  )
        {
            $role = \eZRole::createNew( );
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectToView( 'edit', array( $role->attribute( 'id' ) ) ) );
        }

        // Sorted before paging, not in the browser: the list is shown a page at a time, so
        // reordering the rows on screen would sort ten of however many there are. The order
        // comes off the address as (sort)/(dir) - name, id, policies or assigned - and the
        // search as ?q=, by name or id (expRolePage::listPage()).
        $userParameters = isset( $Params['UserParameters'] ) ? (array)$Params['UserParameters'] : array();
        $search = \expRolePage::normaliseSearch( $http->hasGetVariable( 'q' ) ? $http->getVariable( 'q' ) : '' );

        $listPage = \expRolePage::listPage( $search,
                                            isset( $userParameters['sort'] ) ? $userParameters['sort'] : 'name',
                                            isset( $userParameters['dir'] ) ? $userParameters['dir'] : 'asc',
                                            (int)$offset, $limit );
        $sortField = $listPage['sort'];
        $sortOrder = $listPage['dir'];
        $offset = $listPage['offset'];

        $viewParameters = array( 'offset' => $offset,
                                 'sort'   => $sortField,
                                 'dir'    => $sortOrder );
        $tpl = \eZTemplate::factory();

        $roles = $listPage['roles'];
        $roleCount = $listPage['total'];
        $tpl->setVariable( 'role_summaries', $listPage['summaries'] );
        $tpl->setVariable( 'role_match_count', $listPage['count'] );
        $tpl->setVariable( 'role_search', $search );
        $tpl->setVariable( 'role_search_suffix', $search === '' ? '' : '?q=' . rawurlencode( $search ) );
        $tpl->setVariable( 'role_count_sort', $listPage['count_sort'] );
        $tpl->setVariable( 'role_removed', $removedCount );
        // The temporary roles were fetched here and handed to the template, which has
        // never used them. The list is unbounded - one row per role being edited - and
        // every one of them was loaded with its policies on every view of this page.
        $tpl->setVariable( 'roles', $roles );
        $tpl->setVariable( 'role_count', $roleCount );
        // How many users and groups each role on the page is assigned to, in one query for
        // the page (eZRole::assignmentCounts()); role/view lists them.
        $roleIDs = array();
        foreach ( (array)$roles as $listedRole )
        {
            if ( $listedRole instanceof \eZRole )
                $roleIDs[] = (int)$listedRole->attribute( 'id' );
        }
        $tpl->setVariable( 'assignment_counts', \eZRole::assignmentCounts( $roleIDs ) );
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
                                        'text' => \ezpI18n::tr( 'kernel/role', 'Role list' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
