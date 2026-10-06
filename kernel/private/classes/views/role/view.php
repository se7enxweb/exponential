<?php
/**
 * The code of kernel/role/view.php, moved into a class (#207 stage 1). The file kernel/role/view.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/role/view.php:
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

class View extends \Exponential\Runnable\ModuleView
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
        $roleID = $Params['RoleID'];

        $role = \eZRole::fetch( $roleID );

        if ( !$role )
        {
            $Module->redirectTo( '/role/list/' );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        // The two lists of the page keep their own place in the address: the policies on
        // (policy_offset), (policy_sort) and (policy_dir), the users and groups the role is
        // assigned to on (assignment_offset) and (assignment_filter). Paging or sorting one
        // keeps the other where it is.
        $userParameters = isset( $Params['UserParameters'] ) ? (array)$Params['UserParameters'] : array();
        $assignmentFilter = \eZRole::normaliseAssignmentFilter( isset( $userParameters['assignment_filter'] ) ? $userParameters['assignment_filter'] : '' );

        // The name filter of the assignments. The field is sent with the form of the page, so
        // the filter button is also the first button of the form (pressing Enter in the field
        // must not edit the role); the filter goes into the address, which can be bookmarked
        // and is kept by both pagers.
        if ( $http->hasPostVariable( 'AssignmentFilterButton' ) || $http->hasPostVariable( 'AssignmentFilterClearButton' ) )
        {
            $newFilter = $http->hasPostVariable( 'AssignmentFilterClearButton' ) ? ''
                       : \eZRole::normaliseAssignmentFilter( $http->postVariable( 'AssignmentFilter', '' ) );
            $Module->redirectTo( '/role/view/' . (int)$roleID . self::policyUriSuffix( $userParameters )
                                 . self::assignmentUriSuffix( 0, $newFilter ) );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        // Redirect to role edit
        if ( $http->hasPostVariable( 'EditRoleButton' ) )
        {
            $Module->redirectTo( '/role/edit/' . $roleID );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        // Redirect to content node browse in the user tree
        if ( $http->hasPostVariable( 'AssignRoleButton' ) )
        {
            \eZContentBrowse::browse( array( 'action_name' => 'AssignRole',
                                            'from_page' => '/role/assign/' . $roleID,
                                            'cancel_page' => '/role/view/'. $roleID ),
                                     $Module );

            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }
        else if ( $http->hasPostVariable( 'AssignRoleLimitedButton' ) )
        {
            $Module->redirectTo( '/role/assign/' . $roleID . '/' . $http->postVariable( 'AssignRoleType' ) );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        // Assign the role for a user or group
        if ( $Module->isCurrentAction( 'AssignRole' ) )
        {
            $selectedObjectIDArray = \eZContentBrowse::result( 'AssignRole' );

            $assignedUserIDArray = $role->fetchUserID();

            $db = \eZDB::instance();
            $db->begin();
            foreach ( $selectedObjectIDArray as $objectID )
            {
                if ( !in_array(  $objectID, $assignedUserIDArray ) )
                {
                    $role->assignToUser( $objectID );
                }
            }
            /* Clean up policy cache */
            \eZUser::cleanupCache();

            // Clear role caches.
            \eZRole::expireCache();

            // Clear all content cache.
            \eZContentCacheManager::clearAllContentCache();

            $db->commit();
        }

        // Remove the role assignment
        if ( $http->hasPostVariable( 'RemoveRoleAssignmentButton' ) )
        {
            $idArray = $http->postVariable( "IDArray" );

            $db = \eZDB::instance();
            $db->begin();
            foreach ( $idArray as $id )
            {
                $role->removeUserAssignmentByID( $id );
            }
            /* Clean up policy cache */
            \eZUser::cleanupCache();

            // Clear role caches.
            \eZRole::expireCache();

            // Clear all content cache.
            \eZContentCacheManager::clearAllContentCache();

            $db->commit();
        }

        $tpl = \eZTemplate::factory();

        // The policy list is paged. $role.policies is every policy the role has, which
        // is what the permission system needs and what a screen must not ask for: on an
        // installation whose roles carry policies in the millions, loading them all to
        // draw twenty five exhausts memory before the first row is written.
        //
        // The offset arrives as (policy_offset) rather than (offset), so a page that
        // grows a second list later does not find the two moving together.
        $policyLimit = (int)\eZINI::instance( 'site.ini' )->variable( 'RoleSettings', 'PoliciesPerPage' );
        if ( $policyLimit < 1 )
            $policyLimit = 25;

        $policyOffset = isset( $userParameters['policy_offset'] ) ? (int)$userParameters['policy_offset'] : 0;
        if ( $policyOffset < 0 )
            $policyOffset = 0;

        // Sorted by the database, as in role/edit and for the same reason: the list is
        // paged. (policy_sort)/(policy_dir) sit next to (policy_offset); the default,
        // id ascending, is the role's own order.
        $policySort = isset( $userParameters['policy_sort'] ) ? (string)$userParameters['policy_sort'] : 'id';
        if ( !isset( \eZRole::sortColumnsForPolicyList()[$policySort] ) )
            $policySort = 'id';
        $policyDir = ( isset( $userParameters['policy_dir'] ) && strtolower( $userParameters['policy_dir'] ) === 'desc' ) ? 'desc' : 'asc';

        $policyCount = $role->policyCount();
        $policies    = $role->policyPage( $policyOffset, $policyLimit, $policySort, $policyDir );

        // The users and groups the role is assigned to are paged the same way, on
        // (assignment_offset), sorted by name: a role given to thousands of users loaded every
        // one of them with its object. A page costs the same few queries whatever its size
        // (eZRole::assignmentPage()). AssignmentsPerPage=0 lists all of them on one page.
        $assignmentLimit = (int)\eZINI::instance( 'site.ini' )->variable( 'RoleSettings', 'AssignmentsPerPage' );
        $assignmentOffset = isset( $userParameters['assignment_offset'] ) ? max( 0, (int)$userParameters['assignment_offset'] ) : 0;
        $assignmentTotal = $role->assignmentCount();
        $assignmentCount = $assignmentFilter === '' ? $assignmentTotal : $role->assignmentCount( $assignmentFilter );
        if ( $assignmentLimit < 1 )
        {
            $assignmentLimit = max( 1, $assignmentCount );
            $assignmentOffset = 0;
        }
        // Past the end - the last ones of the last page were just removed, or the address is
        // an old one - shows the last page rather than an empty list.
        if ( $assignmentOffset >= $assignmentCount )
            $assignmentOffset = $assignmentCount > 0 ? (int)( floor( ( $assignmentCount - 1 ) / $assignmentLimit ) * $assignmentLimit ) : 0;
        $userArray = $assignmentCount > 0 ? $role->assignmentPage( $assignmentOffset, $assignmentLimit, $assignmentFilter ) : array();

        $tpl->setVariable( 'assignment_count', $assignmentCount );
        $tpl->setVariable( 'assignment_total', $assignmentTotal );
        $tpl->setVariable( 'assignment_orphan_count', $assignmentTotal > 0 ? $role->assignmentOrphanCount() : 0 );
        $tpl->setVariable( 'assignment_limit', $assignmentLimit );
        $tpl->setVariable( 'assignment_offset', $assignmentOffset );
        $tpl->setVariable( 'assignment_filter', $assignmentFilter );

        $policyParameters = array( 'policy_offset' => $policyOffset,
                                   'policy_sort'   => $policySort,
                                   'policy_dir'    => $policyDir );
        // The address parts of each list, for the links that change the other one: the sort
        // headings of the policies keep the assignments' place, the pager of the assignments
        // in design/standard keeps the policies' place, and the form posts back to both.
        $tpl->setVariable( 'policy_uri_suffix', self::policyUriSuffix( $policyParameters ) );
        $tpl->setVariable( 'assignment_uri_suffix', self::assignmentUriSuffix( $assignmentOffset, $assignmentFilter ) );
        $tpl->setVariable( 'assignment_filter_uri_suffix', self::assignmentUriSuffix( 0, $assignmentFilter ) );

        $tpl->setVariable( 'policy_sort', array( 'field'     => $policySort,
                                                 'direction' => $policyDir,
                                                 'opposite'  => $policyDir === 'asc' ? 'desc' : 'asc' ) );

        $tpl->setVariable( 'policy_count', $policyCount );
        // Editing a role works on a temporary version, which is a row of its own with
        // an id of its own. Paging must not put that id in the address: the page is
        // /role/edit/<the role>, and a link to /role/edit/<the draft> edits the draft
        // directly, so Apply would then write back to the wrong row.
        $tpl->setVariable( 'policy_page_uri', '/role/view/' . (int)$roleID );
        $tpl->setVariable( 'policy_limit', $policyLimit );
        // The filter goes into the pagers' links encoded: the navigator writes the view
        // parameters into the address as they are.
        $tpl->setVariable( 'view_parameters', array_merge( $userParameters, $policyParameters,
                                                           array( 'assignment_offset' => $assignmentOffset,
                                                                  'assignment_filter' => rawurlencode( $assignmentFilter ) ) ) );
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

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }

    /**
     * The part of the address that holds the place of the policy list, only what differs
     * from the default (first page, sorted by id ascending).
     *
     * @param array $parameters policy_offset, policy_sort, policy_dir as they arrived or were corrected
     * @return string
     */
    static function policyUriSuffix( array $parameters )
    {
        $suffix = '';
        $offset = isset( $parameters['policy_offset'] ) ? max( 0, (int)$parameters['policy_offset'] ) : 0;
        if ( $offset > 0 )
            $suffix .= '/(policy_offset)/' . $offset;
        $sort = isset( $parameters['policy_sort'] ) ? (string)$parameters['policy_sort'] : 'id';
        if ( $sort !== 'id' && isset( \eZRole::sortColumnsForPolicyList()[$sort] ) )
            $suffix .= '/(policy_sort)/' . $sort;
        if ( isset( $parameters['policy_dir'] ) && strtolower( (string)$parameters['policy_dir'] ) === 'desc' )
            $suffix .= '/(policy_dir)/desc';
        return $suffix;
    }

    /**
     * The part of the address that holds the place of the assignment list: its offset when
     * not the first page and its name filter, encoded, when there is one.
     *
     * @param int $offset
     * @param string $filter
     * @return string
     */
    static function assignmentUriSuffix( $offset, $filter )
    {
        $suffix = '';
        if ( (int)$offset > 0 )
            $suffix .= '/(assignment_offset)/' . (int)$offset;
        $filter = \eZRole::normaliseAssignmentFilter( $filter );
        if ( $filter !== '' )
            $suffix .= '/(assignment_filter)/' . rawurlencode( $filter );
        return $suffix;
    }
}

}
