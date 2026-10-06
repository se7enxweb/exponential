<?php
/**
 * The code of kernel/state/groups.php, moved into a class (#207 stage 1). The file kernel/state/groups.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/state/groups.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\State
{

class Groups extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];
        $offset = $Params['Offset'];

        $listLimitPreferenceName = 'admin_state_group_list_limit';
        $listLimitPreferenceValue = \eZPreferences::value( $listLimitPreferenceName );

        // The sizes on offer are configured, not written here; the preference holds
        // the position in that list, which is what it has always held.
        list( $limit, $limitChoice, $limitChoices ) =
            \expAdminPagination::chosen( 'state/groups', $listLimitPreferenceName );

        $languages = \eZContentLanguage::fetchList();

        $tpl = \eZTemplate::factory();
        $http = \eZHTTPTool::instance();
        $feedback = array();
        $confirmRemove = false;

        \eZDebug::writeDebug( $Module->currentAction() );
        if ( $Module->isCurrentAction( 'Remove' ) && $Module->hasActionParameter( 'RemoveIDList' ) )
        {
            $removeIDList = (array)$Module->actionParameter( 'RemoveIDList' );

            // Removing a group takes its states off every object and cannot be undone, so the
            // page first says what goes and what it touches; the second post carries ConfirmRemove.
            if ( !$http->hasPostVariable( 'ConfirmRemove' ) )
            {
                $confirmRemove = self::removalPreview( $removeIDList );
            }
            else
            {
                $removedNames = array();
                foreach ( $removeIDList as $removeID )
                {
                    $group = \eZContentObjectStateGroup::fetchById( $removeID );
                    if ( $group && !$group->isInternal() )
                    {
                        // Audit (doc/bc/6.0/audit.md, content.state.remove)
                        if ( class_exists( 'expAuditHook' ) )
                            \expAuditHook::emit( 'content.state.remove', array(
                                'object' => array( 'type' => 'state_group', 'id' => (int)$removeID, 'identifier' => (string)$group->attribute( 'identifier' ) ),
                                'before' => array( 'identifier' => (string)$group->attribute( 'identifier' ) ) ) );
                        $removedNames[] = $group->attribute( 'current_translation' )
                                        ? $group->attribute( 'current_translation' )->attribute( 'name' ) : $group->attribute( 'identifier' );
                        \eZContentObjectStateGroup::removeByID( $removeID );
                        \ezpEvent::getInstance()->notify( 'content/state/group/cache', array( $removeID ) );
                    }
                }
                if ( $removedNames )
                    $feedback[] = array( 'ok' => true, 'message' => \ezpI18n::tr( 'design/admin/state/groups', 'Removed: %names.', null,
                                                                                    array( '%names' => implode( ', ', $removedNames ) ) ) );
            }
        }
        else if ( $Module->isCurrentAction( 'Create' ) )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectTo( 'state/group_edit' ) );
        }

        $groups = \eZContentObjectStateGroup::fetchByOffset( $limit, $offset );
        $groupCount = \eZPersistentObject::count( \eZContentObjectStateGroup::definition() );

        // What the page says about each group: its states in order with the objects in each,
        // and the roles whose policies limit by it.
        $references = self::references();
        $groupsInfo = array();
        foreach ( $groups as $group )
            $groupsInfo[] = self::describeGroup( $group, $references );

        $viewParameters = array( 'offset' => $offset );

        $tpl->setVariable( 'limit', $limit );
        $tpl->setVariable( 'limit_choices', $limitChoices );
        $tpl->setVariable( 'limit_choice', $limitChoice );
        $tpl->setVariable( 'list_limit_preference_name', $listLimitPreferenceName );
        $tpl->setVariable( 'list_limit_preference_value', $listLimitPreferenceValue );
        $tpl->setVariable( 'groups', $groups );
        $tpl->setVariable( 'group_count', $groupCount );
        $tpl->setVariable( 'view_parameters', $viewParameters );
        $tpl->setVariable( 'languages', $languages );
        $tpl->setVariable( 'groups_info', $groupsInfo );
        $tpl->setVariable( 'state_summary', self::overviewFigures( $groupsInfo, $groupCount ) );
        $tpl->setVariable( 'state_feedback', $feedback );
        $tpl->setVariable( 'confirm_remove', $confirmRemove );

        $Result = array(
            'content' => $tpl->fetch( 'design:state/groups.tpl' ),
            'path'    => array(
                array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/state', 'State' ) ),
                array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/state', 'Groups' ) )
            )
        );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }

    /*
     * Helpers the state views share. The ones that take arrays need no database and are
     * tested on their own (tests/tests/kernel/private/views/StateViewHelpersTest.php).
     */

    /**
     * The name a policy limitation gives a state group: StateGroup_<identifier>. Groups whose
     * identifier starts with "ez" belong to the system and are not offered to policies, so
     * they have none.
     *
     * @param string $identifier
     * @return string
     */
    public static function limitationKey( $identifier )
    {
        $identifier = (string)$identifier;
        if ( $identifier === '' || strncmp( $identifier, 'ez', 2 ) === 0 )
            return '';
        return 'StateGroup_' . $identifier;
    }

    /**
     * The roles whose policies name state groups, by group identifier. A policy names a group
     * in two ways: a StateGroup_<identifier> limitation (the policy applies only to objects in
     * the chosen states of that group) and a NewState limitation of state/assign (the states it
     * lets a user set), whose values are state ids.
     *
     * @param array $rows policy limitation values: role_id, role_name, module_name, function_name, identifier, value
     * @param array $groupOfState state id => group identifier
     * @return array group identifier => list of array( role_id, role_name, policies (module/function list),
     *               condition (bool), new_state (bool), state_ids (int list) ), by role name
     */
    public static function referencesByGroup( array $rows, array $groupOfState )
    {
        $byGroup = array();
        foreach ( $rows as $row )
        {
            $limitation = (string)$row['identifier'];
            $stateID = (int)$row['value'];
            if ( strncmp( $limitation, 'StateGroup_', 11 ) === 0 )
            {
                $groupIdentifier = substr( $limitation, 11 );
                $kind = 'condition';
            }
            else if ( $limitation === 'NewState' && isset( $groupOfState[$stateID] ) )
            {
                $groupIdentifier = $groupOfState[$stateID];
                $kind = 'new_state';
            }
            else
                continue;

            $roleID = (int)$row['role_id'];
            if ( !isset( $byGroup[$groupIdentifier][$roleID] ) )
                $byGroup[$groupIdentifier][$roleID] = array( 'role_id' => $roleID, 'role_name' => (string)$row['role_name'],
                                                             'policies' => array(), 'condition' => false,
                                                             'new_state' => false, 'state_ids' => array() );
            $entry =& $byGroup[$groupIdentifier][$roleID];
            $policy = $row['module_name'] . '/' . $row['function_name'];
            if ( !in_array( $policy, $entry['policies'], true ) )
                $entry['policies'][] = $policy;
            $entry[$kind] = true;
            if ( $stateID > 0 && !in_array( $stateID, $entry['state_ids'], true ) )
                $entry['state_ids'][] = $stateID;
            unset( $entry );
        }

        foreach ( $byGroup as $groupIdentifier => $roles )
        {
            usort( $roles, function ( $a, $b ) { return strcasecmp( $a['role_name'], $b['role_name'] ); } );
            foreach ( $roles as $i => $role )
                sort( $roles[$i]['policies'] );
            $byGroup[$groupIdentifier] = $roles;
        }
        return $byGroup;
    }

    /**
     * The roles of a group's references that name one state.
     *
     * @param array $roles one group's entry of referencesByGroup()
     * @param int $stateID
     * @return array
     */
    public static function rolesNamingState( array $roles, $stateID )
    {
        $named = array();
        foreach ( $roles as $role )
            if ( in_array( (int)$stateID, $role['state_ids'], true ) )
                $named[] = $role;
        return $named;
    }

    /**
     * What removing some states of a group does: the states that go, how many objects are in
     * them, and the state those objects are moved to - the first state that stays, in the
     * group's order, which is what eZContentObjectStateGroup::removeStatesByID() does. With no
     * state left the objects simply have no state in the group any more.
     *
     * @param array $states the group's states in order: array( id, name, identifier, object_count )
     * @param array $removeIDs
     * @return array( removed (states), objects (int), target (state or false), remaining (int), all (bool) )
     */
    public static function removalConsequence( array $states, array $removeIDs )
    {
        $removeIDs = array_map( 'intval', $removeIDs );
        $removed = array();
        $target = false;
        $remaining = 0;
        $objects = 0;
        foreach ( $states as $state )
        {
            if ( in_array( (int)$state['id'], $removeIDs, true ) )
            {
                $removed[] = $state;
                $objects += (int)$state['object_count'];
            }
            else
            {
                $remaining++;
                if ( $target === false )
                    $target = $state;
            }
        }
        return array( 'removed' => $removed, 'objects' => $objects, 'target' => $target,
                      'remaining' => $remaining, 'all' => $remaining === 0 && count( $removed ) > 0 );
    }

    /**
     * The figures at the top of the groups page, from the groups on it.
     *
     * @param array $groupsInfo describeGroup() of each group
     * @param int $groupCount all groups, not only this page's
     * @return array( groups, custom, system, states, roles )
     */
    public static function overviewFigures( array $groupsInfo, $groupCount )
    {
        $figures = array( 'groups' => (int)$groupCount, 'custom' => 0, 'system' => 0, 'states' => 0, 'roles' => 0 );
        $roles = array();
        foreach ( $groupsInfo as $info )
        {
            $figures[$info['internal'] ? 'system' : 'custom']++;
            $figures['states'] += count( $info['states'] );
            foreach ( $info['roles'] as $role )
                $roles[$role['role_id']] = true;
        }
        $figures['roles'] = count( $roles );
        return $figures;
    }

    /**
     * The policy limitations that name state groups or states, read once per page. Empty on a
     * database that cannot be read, so the page still shows the groups.
     *
     * @return array see referencesByGroup()
     */
    public static function references()
    {
        try
        {
            $db = \eZDB::instance();
            $rows = $db->arrayQuery( "SELECT r.id AS role_id, r.name AS role_name, p.module_name, p.function_name,
                                             l.identifier, v.value
                                      FROM ezpolicy_limitation l, ezpolicy p, ezrole r, ezpolicy_limitation_value v
                                      WHERE l.policy_id = p.id AND p.role_id = r.id AND v.limitation_id = l.id
                                        AND r.version = 0
                                        AND ( l.identifier LIKE 'StateGroup%' OR l.identifier = 'NewState' )" );
            if ( !is_array( $rows ) || !$rows )
                return array();
            $groupOfState = array();
            foreach ( $db->arrayQuery( "SELECT s.id, g.identifier FROM ezcobj_state s, ezcobj_state_group g WHERE s.group_id = g.id" ) as $row )
                $groupOfState[(int)$row['id']] = $row['identifier'];
            return self::referencesByGroup( $rows, $groupOfState );
        }
        catch ( \Throwable $e )
        {
            \eZDebug::writeError( 'State views: ' . $e->getMessage(), __METHOD__ );
            return array();
        }
    }

    /**
     * A group as the state pages show it: names in the current language, its states in order
     * with the objects in each, its languages, and the roles that limit by it.
     *
     * @param \eZContentObjectStateGroup $group
     * @param array $references references()
     * @param string $locale a translation to show the states in, when they have it
     * @return array
     */
    public static function describeGroup( \eZContentObjectStateGroup $group, array $references, $locale = '' )
    {
        $locale = self::knownLocale( $locale );
        $identifier = (string)$group->attribute( 'identifier' );
        $translation = $group->attribute( 'current_translation' );
        $states = array();
        $objects = 0;
        foreach ( (array)$group->attribute( 'states' ) as $position => $state )
        {
            if ( $locale !== '' )
                $state->setCurrentLanguage( $locale );
            $stateTranslation = $state->attribute( 'current_translation' );
            $count = (int)$state->attribute( 'object_count' );
            $objects += $count;
            $locales = array();
            foreach ( (array)$state->attribute( 'languages' ) as $language )
                $locales[] = $language->attribute( 'locale' );
            $states[] = array( 'id' => (int)$state->attribute( 'id' ),
                               'identifier' => (string)$state->attribute( 'identifier' ),
                               'name' => $stateTranslation ? (string)$stateTranslation->attribute( 'name' ) : (string)$state->attribute( 'identifier' ),
                               'description' => $stateTranslation ? (string)$stateTranslation->attribute( 'description' ) : '',
                               'priority' => (int)$state->attribute( 'priority' ),
                               'position' => $position + 1,
                               'is_default' => $position === 0,
                               'object_count' => $count,
                               'locales' => $locales );
        }
        $locales = array();
        foreach ( (array)$group->attribute( 'languages' ) as $language )
            $locales[] = $language->attribute( 'locale' );
        $default = $group->attribute( 'default_language' );

        return array( 'id' => (int)$group->attribute( 'id' ),
                      'identifier' => $identifier,
                      'name' => $translation ? (string)$translation->attribute( 'name' ) : $identifier,
                      'description' => $translation ? (string)$translation->attribute( 'description' ) : '',
                      'internal' => (bool)$group->isInternal(),
                      'limitation' => self::limitationKey( $identifier ),
                      'states' => $states,
                      'default_state' => $states ? $states[0] : false,
                      'objects' => $objects,
                      'locales' => $locales,
                      'default_locale' => $default ? (string)$default->attribute( 'locale' ) : '',
                      'roles' => isset( $references[$identifier] ) ? $references[$identifier] : array() );
    }

    /**
     * A locale from the address, when it names a language of this installation; '' otherwise.
     * The kernel's setCurrentLanguage() stops the request on a locale it does not know.
     *
     * @param string|null $locale
     * @return string
     */
    public static function knownLocale( $locale )
    {
        $locale = (string)$locale;
        if ( $locale === '' || !preg_match( '/^[a-z]{3}-[A-Z]{2}(@\w+)?$/', $locale ) )
            return '';
        return \eZContentLanguage::fetchByLocale( $locale ) ? $locale : '';
    }

    /**
     * The confirmation of removing groups: each group with its states and objects, the roles
     * that limit by it, and the system groups that are left alone.
     *
     * @param array $removeIDList
     * @return array( groups, skipped, objects, ids )
     */
    protected static function removalPreview( array $removeIDList )
    {
        $references = self::references();
        $preview = array( 'groups' => array(), 'skipped' => array(), 'objects' => 0, 'ids' => array() );
        foreach ( $removeIDList as $removeID )
        {
            $group = \eZContentObjectStateGroup::fetchById( (int)$removeID );
            if ( !$group )
                continue;
            $info = self::describeGroup( $group, $references );
            if ( $info['internal'] )
            {
                $preview['skipped'][] = $info;
                continue;
            }
            $preview['groups'][] = $info;
            $preview['objects'] += $info['objects'];
            $preview['ids'][] = $info['id'];
        }
        return $preview;
    }
}

}
