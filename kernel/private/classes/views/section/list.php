<?php
/**
 * The code of kernel/section/list.php, moved into a class (#207 stage 1). The file kernel/section/list.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 * The section pages and what they show: doc/guides/sections.md
 */
/*
 * The original header of kernel/section/list.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Section
{

class ListView extends \Exponential\Runnable\ModuleView
{
    /** The session variable that carries what the last save or removal did to the list it redirects to. */
    const FEEDBACK = 'ExpSectionFeedback';

    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $http = \eZHTTPTool::instance();
        $Module = $Params['Module'];
        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'module', $Module );

        $offset = $Params['Offset'];

        // The sizes on offer are configured, not written here; the preference holds
        // the position in that list, which is what it has always held.
        list( $limit, $limitChoice, $limitChoices ) = \expAdminPagination::chosen( 'section/list', 'admin_section_list_limit' );

        if ( $http->hasPostVariable( 'CreateSectionButton' ) )
        {
            $Module->redirectTo( $Module->functionURI( "edit" ) . '/0/' );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        if ( $http->hasPostVariable( 'RemoveSectionButton' ) )
        {
            $currentUser = \eZUser::currentUser();
            $accessResult = $currentUser->hasAccessTo( 'section', 'edit' );
            if ( $accessResult['accessWord'] == 'yes' )
            {
                if ( $http->hasPostVariable( 'SectionIDArray' ) )
                {
                    $sectionIDArray = $http->postVariable( 'SectionIDArray' );

                    $sections = array();
                    $sectionIDs = array();
                    $sectionsUnallowed = array();
                    foreach ( $sectionIDArray as $sectionID )
                    {
                        $section = \eZSection::fetch( $sectionID );
                        if ( is_object( $section ) )
                        {
                            if ( $section->canBeRemoved() )
                            {
                                $sections[] = $section;
                                $sectionIDs[] = $sectionID;
                            }
                            else
                            {
                                $sectionsUnallowed[] = $section;
                            }
                        }
                    }

                    if ( count( $sections) > 0 or
                         count( $sectionsUnallowed ) > 0 )
                    {
                        $http->setSessionVariable( 'SectionIDArray', $sectionIDs );
                        $tpl->setVariable( 'delete_result', $sections ); // deprecated, left for BC
                        $tpl->setVariable( 'allowed_sections', $sections );
                        $tpl->setVariable( 'unallowed_sections', $sectionsUnallowed );
                        // why a section cannot go: its objects, the policies and the role assignments naming it
                        $tpl->setVariable( 'section_overview', self::overviewFromDatabase()['sections'] );

                        $Result = array();
                        $Result['content'] = $tpl->fetch( "design:section/confirmremove.tpl" );
                        $Result['path'] = array( array( 'url' => 'section/list',
                                                        'text' => \ezpI18n::tr( 'kernel/section', 'Sections' ) ),
                                                 array( 'url' => false,
                                                        'text' => \ezpI18n::tr( 'design/admin/section/confirmremove', 'Confirm section removal' ) ) );
                        return $this->viewResult( isset( $Result ) ? $Result : null, null );
                    }
                }
                else
                {
                    $http->setSessionVariable( self::FEEDBACK, array( 'type' => 'none_selected' ) );
                }
            }
            else
            {
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );
            }
        }

        // Cancel on the confirmation: the sections it offered are no longer up for removal
        if ( $http->hasPostVariable( 'CancelButton' ) && $http->hasSessionVariable( 'SectionIDArray' ) )
        {
            $http->removeSessionVariable( 'SectionIDArray' );
        }

        if ( $http->hasPostVariable( 'ConfirmRemoveSectionButton' ) )
        {
            $currentUser = \eZUser::currentUser();
            $accessResult = $currentUser->hasAccessTo( 'section', 'edit' );
            if ( $accessResult['accessWord'] == 'yes' )
            {
                if ( $http->hasSessionVariable( 'SectionIDArray' ) )
                {
                    $sectionIDArray = $http->sessionVariable( 'SectionIDArray' );
                    $removedNames = array();

                    $db = \eZDB::instance();
                    $db->begin();
                    foreach ( $sectionIDArray as $sectionID )
                    {
                        $section = \eZSection::fetch( $sectionID );
                        if ( is_object( $section ) and
                             $section->canBeRemoved() )
                        {
                            // Clear content cache if needed
                            \eZContentCacheManager::clearContentCacheIfNeededBySectionID( $sectionID );
                            // Audit (doc/bc/6.0/audit.md, content.section.remove)
                            if ( class_exists( 'expAuditHook' ) )
                                \expAuditHook::emit( 'content.section.remove', array( 'object' => \expAuditHook::section( $section ),
                                    'before' => array( 'name' => (string)$section->attribute( 'name' ), 'identifier' => (string)$section->attribute( 'identifier' ) ) ) );
                            $removedNames[] = (string)$section->attribute( 'name' );
                            $section->remove();
                            \ezpEvent::getInstance()->notify( 'content/section/cache', array( $sectionID ) );
                        }
                    }
                    $db->commit();
                    // the confirmation is used up: a second post of the same form removes nothing
                    $http->removeSessionVariable( 'SectionIDArray' );
                    if ( $removedNames )
                        $http->setSessionVariable( self::FEEDBACK, array( 'type' => 'removed', 'names' => $removedNames ) );
                }
            }
            else
            {
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );
            }
        }

        $viewParameters = array( 'offset' => $offset );
        $sectionArray = \eZSection::fetchByOffset( $offset, $limit );
        $sectionCount = \eZSection::sectionCount();

        $currentUser = \eZUser::currentUser();
        $allowedAssignSectionList = $currentUser->canAssignSectionList();
        $editAccess = $currentUser->hasAccessTo( 'section', 'edit' );

        $overview = self::overviewFromDatabase();

        $tpl->setVariable( "limit", $limit );
        $tpl->setVariable( 'limit_choices', $limitChoices );
        $tpl->setVariable( 'limit_choice', $limitChoice );
        $tpl->setVariable( 'section_array', $sectionArray );
        $tpl->setVariable( 'section_count', $sectionCount );
        $tpl->setVariable( 'view_parameters', $viewParameters );
        $tpl->setVariable( 'allowed_assign_sections', $allowedAssignSectionList );
        $tpl->setVariable( 'section_overview', $overview['sections'] );
        $tpl->setVariable( 'section_summary', $overview['summary'] );
        $tpl->setVariable( 'section_can_edit', $editAccess['accessWord'] != 'no' );
        $tpl->setVariable( 'section_feedback', self::takeFeedback( $http ) );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:section/list.tpl" );
        $Result['path'] = array( array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'kernel/section', 'Sections' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }

    /**
     * What the last save or removal left for the list, read once.
     *
     * @return array|false
     */
    public static function takeFeedback( \eZHTTPTool $http )
    {
        if ( !$http->hasSessionVariable( self::FEEDBACK ) )
            return false;
        $feedback = $http->sessionVariable( self::FEEDBACK );
        $http->removeSessionVariable( self::FEEDBACK );
        return is_array( $feedback ) && isset( $feedback['type'] ) ? $feedback : false;
    }

    /**
     * The overview of every section, read with three grouped queries however many sections there are (one per
     * section and status on MongoDB, which has no joins).
     *
     * @return array see overview()
     */
    public static function overviewFromDatabase()
    {
        $sections = array();
        foreach ( \eZSection::fetchList( false ) as $row )
            $sections[] = $row;
        list( $countRows, $policyRows, $assignmentRows ) = self::usageRows( $sections );
        return self::overview( $sections, $countRows, $policyRows, $assignmentRows, \eZNavigationPart::fetchList() );
    }

    /**
     * The raw usage of the sections: objects per section and status, the policies whose Section limitation names a
     * section, and the role assignments limited to a section.
     *
     * @param array $sections rows of ezsection
     * @return array array( count rows (section_id, status, count), policy rows (value, role_id, role_name,
     *               module_name, function_name), assignment rows (limit_value, role_id, contentobject_id) )
     */
    public static function usageRows( array $sections )
    {
        $db = \eZDB::instance();
        if ( $db->databaseName() === 'mongo' )
            return self::usageRowsOneByOne( $sections );

        $countRows = $db->arrayQuery( 'SELECT section_id, status, COUNT(*) AS count FROM ezcontentobject GROUP BY section_id, status' );
        $policyRows = $db->arrayQuery( "SELECT ezpolicy_limitation_value.value AS value, ezpolicy.role_id AS role_id, ezrole.name AS role_name,
                                               ezpolicy.module_name AS module_name, ezpolicy.function_name AS function_name
                                        FROM ezpolicy_limitation
                                        JOIN ezpolicy_limitation_value ON ezpolicy_limitation_value.limitation_id = ezpolicy_limitation.id
                                        JOIN ezpolicy ON ezpolicy.id = ezpolicy_limitation.policy_id
                                        LEFT JOIN ezrole ON ezrole.id = ezpolicy.role_id
                                        WHERE ezpolicy_limitation.identifier = 'Section'" );
        // stored as 'Section' (eZRole::assignToUser); compared without case, as MySQL would anyway
        $assignmentRows = $db->arrayQuery( "SELECT limit_value, role_id, contentobject_id FROM ezuser_role WHERE LOWER(limit_identifier) = 'section'" );
        return array( is_array( $countRows ) ? $countRows : array(), is_array( $policyRows ) ? $policyRows : array(),
                      is_array( $assignmentRows ) ? $assignmentRows : array() );
    }

    /**
     * usageRows() through the kernel's own per-section lookups, for databases without joins or grouping in SQL.
     */
    private static function usageRowsOneByOne( array $sections )
    {
        $countRows = $policyRows = $assignmentRows = array();
        foreach ( $sections as $section )
        {
            $id = (int)$section['id'];
            foreach ( array( \eZContentObject::STATUS_DRAFT, \eZContentObject::STATUS_PUBLISHED, \eZContentObject::STATUS_ARCHIVED ) as $status )
            {
                $rows = \eZPersistentObject::fetchObjectList( \eZContentObject::definition(), array(), array( 'section_id' => $id, 'status' => $status ),
                                                             false, null, false, false, array( array( 'operation' => 'count( id )', 'name' => 'count' ) ) );
                $count = isset( $rows[0]['count'] ) ? (int)$rows[0]['count'] : 0;
                if ( $count )
                    $countRows[] = array( 'section_id' => $id, 'status' => $status, 'count' => $count );
            }
            foreach ( \eZPolicyLimitation::findByType( 'Section', $id, true, false ) as $limitation )
            {
                $policy = $limitation->policy();
                if ( !$policy )
                    continue;
                $role = \eZRole::fetch( $policy->attribute( 'role_id' ) );
                $policyRows[] = array( 'value' => $id, 'role_id' => $policy->attribute( 'role_id' ), 'role_name' => $role ? $role->attribute( 'name' ) : null,
                                       'module_name' => $policy->attribute( 'module_name' ), 'function_name' => $policy->attribute( 'function_name' ) );
            }
            foreach ( \eZRole::fetchRolesByLimitation( 'section', $id ) as $userRole )
            {
                $assignmentRows[] = array( 'limit_value' => $id, 'role_id' => $userRole['role'] ? $userRole['role']->attribute( 'id' ) : 0,
                                           'contentobject_id' => $userRole['user'] ? $userRole['user']->attribute( 'id' ) : 0 );
            }
        }
        return array( $countRows, $policyRows, $assignmentRows );
    }

    /**
     * What the section pages say about each section, from the raw rows. No database.
     *
     * Per section: name, identifier, navigation part (its name, and whether menu.ini knows it), objects by status
     * (published, drafts, archived, all), the roles whose policies are limited to the section with those policies'
     * module/function, the number of such policies, the number of role assignments limited to it, whether it can be
     * removed (nothing of the three: the same test as eZSection::canBeRemoved()), what needs attention (no
     * identifier, a navigation part menu.ini does not list), and the lower-case text the page's search looks in.
     * The summary counts sections, published objects, sections used by roles, empty and removable sections and those
     * needing attention.
     *
     * @param array $sections rows of ezsection (id, name, identifier, navigation_part_identifier)
     * @param array $countRows (section_id, status, count)
     * @param array $policyRows (value, role_id, role_name, module_name, function_name)
     * @param array $assignmentRows (limit_value, role_id, contentobject_id)
     * @param array $navigationParts identifier => array( 'name' => ..., 'identifier' => ... ) as eZNavigationPart::fetchList()
     * @return array array( 'sections' => id => info, 'summary' => counts )
     */
    public static function overview( array $sections, array $countRows, array $policyRows, array $assignmentRows, array $navigationParts )
    {
        $counts = array();
        foreach ( $countRows as $row )
        {
            $id = (int)$row['section_id'];
            $status = (int)$row['status'];
            if ( !isset( $counts[$id] ) )
                $counts[$id] = array( 'published' => 0, 'drafts' => 0, 'archived' => 0, 'objects' => 0 );
            $key = $status === 1 ? 'published' : ( $status === 0 ? 'drafts' : ( $status === 2 ? 'archived' : null ) );
            if ( $key !== null )
                $counts[$id][$key] += (int)$row['count'];
            $counts[$id]['objects'] += (int)$row['count'];
        }

        $roles = $policyCounts = array();
        foreach ( $policyRows as $row )
        {
            if ( !is_numeric( $row['value'] ) )
                continue;
            $id = (int)$row['value'];
            $roleID = (int)$row['role_id'];
            $policyCounts[$id] = ( isset( $policyCounts[$id] ) ? $policyCounts[$id] : 0 ) + 1;
            if ( !isset( $roles[$id][$roleID] ) )
                $roles[$id][$roleID] = array( 'id' => $roleID,
                                              'name' => isset( $row['role_name'] ) && $row['role_name'] !== null ? (string)$row['role_name'] : '#' . $roleID,
                                              'functions' => array() );
            $function = $row['module_name'] . '/' . $row['function_name'];
            if ( !in_array( $function, $roles[$id][$roleID]['functions'], true ) )
                $roles[$id][$roleID]['functions'][] = $function;
        }

        $assignments = $assignmentRoles = array();
        foreach ( $assignmentRows as $row )
        {
            if ( !is_numeric( $row['limit_value'] ) )
                continue;
            $id = (int)$row['limit_value'];
            $assignments[$id] = ( isset( $assignments[$id] ) ? $assignments[$id] : 0 ) + 1;
            $assignmentRoles[$id][(int)$row['role_id']] = true;
        }

        $result = array();
        $summary = array( 'sections' => 0, 'published' => 0, 'in_roles' => 0, 'empty' => 0, 'removable' => 0, 'attention' => 0 );
        foreach ( $sections as $section )
        {
            $id = (int)$section['id'];
            $count = isset( $counts[$id] ) ? $counts[$id] : array( 'published' => 0, 'drafts' => 0, 'archived' => 0, 'objects' => 0 );
            $roleList = isset( $roles[$id] ) ? array_values( $roles[$id] ) : array();
            usort( $roleList, function ( $a, $b ) { return strcasecmp( $a['name'], $b['name'] ); } );
            foreach ( $roleList as $i => $role )
                sort( $roleList[$i]['functions'] );

            $part = (string)$section['navigation_part_identifier'];
            $partKnown = isset( $navigationParts[$part] );
            $identifier = trim( (string)$section['identifier'] );
            $policies = isset( $policyCounts[$id] ) ? $policyCounts[$id] : 0;
            $assigned = isset( $assignments[$id] ) ? $assignments[$id] : 0;

            $info = array(
                'id' => $id,
                'name' => (string)$section['name'],
                'identifier' => $identifier,
                'navigation_part' => $part,
                'navigation_part_name' => $partKnown ? (string)$navigationParts[$part]['name'] : $part,
                'navigation_part_known' => $partKnown,
                'published' => $count['published'],
                'drafts' => $count['drafts'],
                'archived' => $count['archived'],
                'objects' => $count['objects'],
                'roles' => $roleList,
                'role_count' => count( $roleList ),
                'policy_count' => $policies,
                'assignment_count' => $assigned,
                'assignment_role_count' => isset( $assignmentRoles[$id] ) ? count( $assignmentRoles[$id] ) : 0,
                'used_by_roles' => $policies > 0 || $assigned > 0,
                'removable' => $count['objects'] === 0 && $policies === 0 && $assigned === 0,
                'missing_identifier' => $identifier === '',
                'attention' => $identifier === '' || !$partKnown,
            );
            $words = array( $info['name'], $identifier, (string)$id, $part, $info['navigation_part_name'] );
            foreach ( $roleList as $role )
                $words[] = $role['name'];
            $info['search'] = function_exists( 'mb_strtolower' ) ? mb_strtolower( implode( ' ', $words ), 'UTF-8' ) : strtolower( implode( ' ', $words ) );
            $result[$id] = $info;

            $summary['sections']++;
            $summary['published'] += $info['published'];
            if ( $info['used_by_roles'] ) $summary['in_roles']++;
            if ( $info['published'] === 0 ) $summary['empty']++;
            if ( $info['removable'] ) $summary['removable']++;
            if ( $info['attention'] ) $summary['attention']++;
        }
        return array( 'sections' => $result, 'summary' => $summary );
    }
}

}
