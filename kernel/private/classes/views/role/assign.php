<?php
/**
 * The code of kernel/role/assign.php, moved into a class (#207 stage 1). The file kernel/role/assign.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/role/assign.php:
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

class Assign extends \Exponential\Runnable\ModuleView
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
        // The role must exist and be a saved one; the limitation is subtree, section or none, its value a number.
        // All three become part of addresses and of the stored assignment.
        $roleID = $Params['RoleID'];
        $role = ( is_scalar( $roleID ) && ctype_digit( (string)$roleID ) ) ? \eZRole::fetch( (int)$roleID ) : null;
        if ( !$role instanceof \eZRole || (int)$role->attribute( 'version' ) !== 0 )
            return $this->viewResult( null, $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
        $roleID = (int)$roleID;
        $limitIdent = $Params['LimitIdent'];
        $limitValue = $Params['LimitValue'];
        if ( $limitIdent !== null && $limitIdent !== false && $limitIdent !== '' && !\expRolePage::assignLimitType( $limitIdent ) )
        {
            \eZDebug::writeWarning( 'Unsupported assign limitation: ' . substr( (string)$limitIdent, 0, 50 ), 'role/assign' );
            return $this->viewResult( null, $Module->redirectTo( '/role/view/' . $roleID ) );
        }
        if ( isset( $limitValue ) && !ctype_digit( (string)$limitValue ) )
            $limitValue = null;

        if ( $http->hasPostVariable( 'AssignSectionCancelButton' ) )
        {
            $Module->redirectTo( '/role/view/' . $roleID );
        }

        if ( $http->hasPostVariable( 'BrowseCancelButton' ) )
        {
            if ( $http->hasPostVariable( 'BrowseCancelURI' ) )
            {
                // only a page of this site (eZRedirectManager's rules), else the role
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectTo(
                    \eZRedirectManager::returnURI( $Module, '/role/view/' . $roleID, $http->postVariable( 'BrowseCancelURI' ), array( 'session' => false ) ) ) );
            }
        }

        if ( $http->hasPostVariable( 'AssignSectionID' ) &&
             $http->hasPostVariable( 'SectionID' ) )
        {
            $sectionID = $http->postVariable( 'SectionID' );
            if ( $limitIdent === 'section' && is_scalar( $sectionID ) && ctype_digit( (string)$sectionID ) && \eZSection::fetch( (int)$sectionID ) )
                $Module->redirectTo( '/role/assign/' . $roleID . '/section/' . (int)$sectionID );
            else
                $Module->redirectTo( '/role/assign/' . $roleID . '/section' );
        }
        else if ( $http->hasPostVariable( 'BrowseActionName' ) and
                  $http->postVariable( 'BrowseActionName' ) == 'SelectObjectRelationNode' )
        {
            $selectedNodeIDArray = (array)$http->postVariable( 'SelectedNodeIDArray' );
            if ( count( $selectedNodeIDArray ) == 1 && is_scalar( $selectedNodeIDArray[0] ) && ctype_digit( (string)$selectedNodeIDArray[0] ) )
            {
                $limitValue = (int)$selectedNodeIDArray[0];
            }
            $Module->redirectTo( '/role/assign/' . $roleID . '/' . ( $limitIdent === 'subtree' ? 'subtree/' . (int)$limitValue : '' ) );
        }
        else if ( $http->hasPostVariable( 'BrowseActionName' ) and
                  $http->postVariable( 'BrowseActionName' ) == 'AssignRole' )
        {
            $selectedObjectIDArray = array();
            foreach ( (array)$http->postVariable( 'SelectedObjectIDArray' ) as $objectID )
            {
                if ( is_scalar( $objectID ) && ctype_digit( (string)$objectID ) )
                    $selectedObjectIDArray[(int)$objectID] = (int)$objectID;
            }
            if ( $limitIdent && !isset( $limitValue ) )
                $limitIdent = '';

            $db = \eZDB::instance();
            $db->begin();
            foreach ( $selectedObjectIDArray as $objectID )
            {
                $role->assignToUser( $objectID, $limitIdent, $limitValue );
            }
            // Clear role caches.
            \eZRole::expireCache();

            $db->commit();
            if ( count( $selectedObjectIDArray ) > 0 )
            {
                \eZContentCacheManager::clearAllContentCache();
            }

            /* Clean up policy cache */
            \eZUser::cleanupCache();

            $Module->redirectTo( '/role/view/' . $roleID );
        }
        else if ( is_string( $limitIdent ) && !isset( $limitValue ) )
        {
            switch( $limitIdent )
            {
                case 'subtree':
                {
                    \eZContentBrowse::browse( array( 'action_name' => 'SelectObjectRelationNode',
                                                    'from_page' => '/role/assign/' . $roleID . '/' . $limitIdent,
                                                    'cancel_page' => '/role/view/' . $roleID ),
                                             $Module );
                    return $this->viewResult( isset( $Result ) ? $Result : null, null );
                } break;

                case 'section':
                {
                    $sectionArray = \eZSection::fetchList( );
                    $tpl = \eZTemplate::factory();
                    $tpl->setVariable( 'section_array', $sectionArray );
                    $tpl->setVariable( 'role_id', $roleID );
                    $tpl->setVariable( 'limit_ident', $limitIdent );
                    $tpl->setVariable( 'role', $role );
                    $tpl->setVariable( 'role', $role );

                    $Result = array();
                    $Result['content'] = $tpl->fetch( 'design:role/assign_limited_section.tpl' );
                    $Result['path'] = array( array( 'url' => false,
                                                    'text' => \ezpI18n::tr( 'kernel/role', 'Limit on section' ) ) );
                    return $this->viewResult( isset( $Result ) ? $Result : null, null );
                } break;

                default:
                {
                    \eZDebug::writeWarning( 'Unsupported assign limitation: ' . $limitIdent );
                    $Module->redirectTo( '/role/view/' . $roleID );
                } break;
            }
        }
        else if ( is_numeric( $roleID ) )
        {
            \eZContentBrowse::browse( array( 'action_name' => 'AssignRole',
                                            'from_page' => '/role/assign/' . $roleID . '/' . $limitIdent . '/' . $limitValue,
                                            'cancel_page' => '/role/view/' . $roleID ),
                                     $Module );

            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
