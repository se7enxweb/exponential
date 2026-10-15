<?php
/**
 * File containing the expUserDeactivation class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * "Deactivate and move" and "Reactivate and move back": instead of removing a user account (users never go to the
 * trash, so removing one cannot be undone), the account is disabled and all its locations are brought together in
 * one user group for deactivated accounts; the locations it had are remembered, and reactivating it enables it and
 * puts it back where it was.
 *
 * Off unless switched on: site.ini [UserSettings] DeactivateAndMove=enabled. The group is the node of
 * [UserSettings] DeactivatedUserGroupNodeID when that is set, otherwise the user group with the remote id
 * REMOTE_ID; when neither exists it is created on first use below the user root node (content.ini [NodeSettings]
 * UserRootNode), hidden, without roles, by the user who acts and only when that user may create a user group there
 * (bin/php/ezcreatedeactivatedusergroup.php creates it beforehand).
 *
 * The remembered locations are one row of ezsite_data (eZSiteData) per account, named SITE_DATA_PREFIX . user id,
 * holding JSON: the parent of the main location, the parents of the other locations, the group, when and by whom.
 * No new table: the row goes through eZPersistentObject on every database engine the kernel supports, it is removed
 * when the account is reactivated, and Setup > Site data shows it (node ids only, no names).
 *
 * Every change goes through the kernel's own operations (user setsettings, content move, removelocation,
 * addlocation, hide), which also run their workflow triggers and write their own audit records; this class adds
 * access.user.deactivate_move, access.user.reactivate_move and access.user.deactivated_group.create.
 *
 * Which account an action may touch is decided by decide(), a plain function over the state accountState() reads;
 * where an account is put back is planned by planRestore(), also a plain function.
 *
 * Guide: doc/features/6.0/user-hygiene.md, section "Deactivate and move"
 */
class expUserDeactivation
{
    /** The remote id of the group for deactivated accounts when no node id is configured */
    const REMOTE_ID = 'exp_deactivated_users_group';

    /** The name of the ezsite_data row of an account's remembered locations, followed by its user id */
    const SITE_DATA_PREFIX = 'exp_user_deactivation_';

    /** The actions */
    const ACTIONS = array( 'deactivate_move', 'reactivate_move' );

    /** The most accounts one action takes */
    const MAX_IDS = 100;

    /** The translation context of the texts of this class and of its page */
    const CONTEXT = 'design/admin/user/deactivate';

    /**
     * Whether the feature is switched on: [UserSettings] DeactivateAndMove=enabled. Anything else, a missing setting
     * included, is off.
     *
     * @param eZINI|null $ini
     * @return bool
     */
    public static function isEnabled( $ini = null )
    {
        $ini = $ini ?: eZINI::instance();
        return $ini->hasVariable( 'UserSettings', 'DeactivateAndMove' )
            && self::isEnabledValue( $ini->variable( 'UserSettings', 'DeactivateAndMove' ) );
    }

    /**
     * @param mixed $value the value of [UserSettings] DeactivateAndMove
     * @return bool
     */
    public static function isEnabledValue( $value )
    {
        return is_string( $value ) && strtolower( trim( $value ) ) === 'enabled';
    }

    /**
     * The node id of [UserSettings] DeactivatedUserGroupNodeID, 0 when it is not set.
     *
     * @param eZINI|null $ini
     * @return int
     */
    public static function configuredNodeID( $ini = null )
    {
        $ini = $ini ?: eZINI::instance();
        if ( !$ini->hasVariable( 'UserSettings', 'DeactivatedUserGroupNodeID' ) )
            return 0;
        return self::normaliseNodeID( $ini->variable( 'UserSettings', 'DeactivatedUserGroupNodeID' ) );
    }

    /**
     * @param mixed $value
     * @return int a positive node id, or 0
     */
    public static function normaliseNodeID( $value )
    {
        $value = is_scalar( $value ) ? trim( (string)$value ) : '';
        return ctype_digit( $value ) && (int)$value > 0 ? (int)$value : 0;
    }

    /**
     * The group's node: the configured node, else the main node of the object with REMOTE_ID; null when there is
     * none (yet). A configured node that does not exist is null too, and is never replaced by a new group.
     *
     * @return eZContentObjectTreeNode|null
     */
    public static function groupNode()
    {
        $configured = self::configuredNodeID();
        if ( $configured > 0 )
        {
            $node = eZContentObjectTreeNode::fetch( $configured );
            return $node instanceof eZContentObjectTreeNode ? $node : null;
        }
        $object = eZContentObject::fetchByRemoteID( self::REMOTE_ID );
        if ( !$object instanceof eZContentObject || (int)$object->attribute( 'status' ) !== eZContentObject::STATUS_PUBLISHED )
            return null;
        $node = $object->mainNode();
        return $node instanceof eZContentObjectTreeNode ? $node : null;
    }

    /**
     * Whether the group may be created on first use: no node id is configured and nothing has the remote id.
     *
     * @return bool
     */
    public static function groupMayBeCreated()
    {
        return self::configuredNodeID() === 0 && !eZContentObject::fetchByRemoteID( self::REMOTE_ID ) instanceof eZContentObject;
    }

    /**
     * The node the group is created below: content.ini [NodeSettings] UserRootNode.
     *
     * @return eZContentObjectTreeNode|null
     */
    public static function groupParentNode()
    {
        $id = (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'UserRootNode' );
        $node = $id > 0 ? eZContentObjectTreeNode::fetch( $id ) : null;
        return $node instanceof eZContentObjectTreeNode ? $node : null;
    }

    /**
     * Whether the current user may create the group: content/create of the user group class below the user root
     * node.
     *
     * @return bool
     */
    public static function canCreateGroup()
    {
        $parent = self::groupParentNode();
        if ( !$parent )
            return false;
        $classID = (int)eZINI::instance()->variable( 'UserSettings', 'UserGroupClassID' );
        $parentObject = $parent->object();
        return $classID > 0 && $parentObject instanceof eZContentObject
            && $parent->checkAccess( 'create', $classID, $parentObject->attribute( 'contentclass_id' ) ) == 1;
    }

    /**
     * Creates the group below the user root node with REMOTE_ID, published by the current user, hidden, without
     * roles, in one transaction, and records access.user.deactivated_group.create. Nothing is checked here but
     * whether the group may be created at all (groupMayBeCreated()); the caller checks the right
     * (canCreateGroup()), the script runs as the administrator.
     *
     * @param string|null $name the group's name, "Deactivated users" when null
     * @param int $sectionID 0: the section of the parent
     * @return array ok (bool), node (eZContentObjectTreeNode|null), error (string: exists, no_parent, no_class, failed)
     */
    public static function createGroup( $name = null, $sectionID = 0 )
    {
        if ( !self::groupMayBeCreated() )
            return array( 'ok' => false, 'node' => null, 'error' => 'exists' );
        $parent = self::groupParentNode();
        if ( !$parent )
            return array( 'ok' => false, 'node' => null, 'error' => 'no_parent' );
        $class = eZContentClass::fetch( (int)eZINI::instance()->variable( 'UserSettings', 'UserGroupClassID' ) );
        if ( !$class instanceof eZContentClass )
            return array( 'ok' => false, 'node' => null, 'error' => 'no_class' );
        if ( $name === null || trim( (string)$name ) === '' )
            $name = ezpI18n::tr( self::CONTEXT, 'Deactivated users' );

        $attributes = array();
        foreach ( $class->fetchAttributes() as $classAttribute )
        {
            $identifier = $classAttribute->attribute( 'identifier' );
            if ( $identifier === 'name' )
                $attributes['name'] = (string)$name;
            elseif ( $identifier === 'description' && $classAttribute->attribute( 'data_type_string' ) === 'eztext' )
                $attributes['description'] = ezpI18n::tr( self::CONTEXT, 'Disabled accounts, kept instead of removed. No roles; hidden.' );
        }
        if ( !isset( $attributes['name'] ) )
            return array( 'ok' => false, 'node' => null, 'error' => 'no_class' );

        $db = eZDB::instance();
        $db->begin();
        $params = array( 'parent_node_id' => (int)$parent->attribute( 'node_id' ),
                         'class_identifier' => $class->attribute( 'identifier' ),
                         'creator_id' => (int)eZUser::currentUserID(),
                         'remote_id' => self::REMOTE_ID,
                         'attributes' => $attributes );
        if ( (int)$sectionID > 0 )
            $params['section_id'] = (int)$sectionID;
        $object = eZContentFunctions::createAndPublishObject( $params );
        $node = $object instanceof eZContentObject ? $object->mainNode() : null;
        if ( !$node instanceof eZContentObjectTreeNode )
        {
            $db->rollback();
            return array( 'ok' => false, 'node' => null, 'error' => 'failed' );
        }
        if ( (int)$sectionID > 0 && (int)$object->attribute( 'section_id' ) !== (int)$sectionID )
            eZContentObjectTreeNode::assignSectionToSubTree( (int)$node->attribute( 'node_id' ), (int)$sectionID );
        eZContentObjectTreeNode::hideSubTree( $node );
        $db->commit();
        eZSearch::updateNodeVisibility( (int)$node->attribute( 'node_id' ), 'hide' );
        eZContentCacheManager::clearContentCacheIfNeeded( (int)$object->attribute( 'id' ) );

        $node = eZContentObjectTreeNode::fetch( (int)$node->attribute( 'node_id' ) );
        if ( class_exists( 'expAuditHook' ) )
            expAuditHook::emit( 'access.user.deactivated_group.create', array(
                'object' => array( 'type' => 'node', 'id' => (int)$node->attribute( 'node_id' ) ),
                'after' => array( 'node_id' => (int)$node->attribute( 'node_id' ), 'object_id' => (int)$object->attribute( 'id' ),
                                  'parent_node_id' => (int)$parent->attribute( 'node_id' ), 'remote_id' => self::REMOTE_ID,
                                  'section_id' => (int)$object->attribute( 'section_id' ), 'hidden' => true ) ) );
        return array( 'ok' => true, 'node' => $node, 'error' => '' );
    }

    /**
     * @param mixed $action
     * @return string|null one of ACTIONS
     */
    public static function normaliseAction( $action )
    {
        return is_string( $action ) && in_array( $action, self::ACTIONS, true ) ? $action : null;
    }

    /* ---- the remembered locations ---- */

    /**
     * @param int $userID
     * @return string the name of the ezsite_data row
     */
    public static function recordName( $userID )
    {
        return self::SITE_DATA_PREFIX . (int)$userID;
    }

    /**
     * The JSON of a record: main (the parent node of the main location), parents (the parent nodes of the other
     * locations), group (the group's node), at (time), by (the user who acted).
     *
     * @param array $record
     * @return string
     */
    public static function encodeRecord( array $record )
    {
        $record = self::normaliseRecord( $record );
        return json_encode( $record === null ? array() : $record );
    }

    /**
     * A record from its JSON, null when it is not one.
     *
     * @param mixed $json
     * @return array|null
     */
    public static function decodeRecord( $json )
    {
        $data = is_string( $json ) ? json_decode( $json, true ) : null;
        return is_array( $data ) ? self::normaliseRecord( $data ) : null;
    }

    /**
     * @param array $data
     * @return array|null v, main, parents (unique, without main), group, at, by; null without a main parent
     */
    public static function normaliseRecord( array $data )
    {
        $main = isset( $data['main'] ) ? self::normaliseNodeID( $data['main'] ) : 0;
        if ( $main === 0 )
            return null;
        $parents = array();
        foreach ( isset( $data['parents'] ) && is_array( $data['parents'] ) ? $data['parents'] : array() as $parent )
        {
            $parent = self::normaliseNodeID( $parent );
            if ( $parent > 0 && $parent !== $main )
                $parents[$parent] = $parent;
        }
        return array( 'v' => 1, 'main' => $main, 'parents' => array_values( $parents ),
                      'group' => isset( $data['group'] ) ? self::normaliseNodeID( $data['group'] ) : 0,
                      'at' => isset( $data['at'] ) ? max( 0, (int)$data['at'] ) : 0,
                      'by' => isset( $data['by'] ) ? max( 0, (int)$data['by'] ) : 0 );
    }

    /**
     * The remembered locations of an account, null when there are none.
     *
     * @param int $userID
     * @return array|null see normaliseRecord()
     */
    public static function remembered( $userID )
    {
        $row = eZSiteData::fetchByName( self::recordName( $userID ) );
        return $row instanceof eZSiteData ? self::decodeRecord( (string)$row->attribute( 'value' ) ) : null;
    }

    /**
     * @param int $userID
     * @param array $record
     */
    public static function remember( $userID, array $record )
    {
        eZSiteData::createNew( self::recordName( $userID ), self::encodeRecord( $record ) );
    }

    /**
     * @param int $userID
     */
    public static function forget( $userID )
    {
        $row = eZSiteData::fetchByName( self::recordName( $userID ) );
        if ( $row instanceof eZSiteData )
            $row->remove();
    }

    /* ---- the decisions ---- */

    /**
     * Whether an action may be done to one account, decided again at the moment it is done: 'do', or why not:
     * protected (see expUserHygiene::protectedIDs()), gone (no object, or one the user who acts may not read: the
     * two are one answer), no_object, not_user (the object is not a user account), denied (the user who acts may not edit the account's object, may not read, move or remove
     * one of its locations, or may not place it where it goes), no_location (no main location), has_children (a
     * location has sub-items), no_group (deactivate: no group and it may not be created), unactivated (reactivate:
     * the account never activated), no_target (reactivate: neither a remembered location nor the default placement
     * may take it), unchanged (already in the state the action makes).
     *
     * @param string $action one of ACTIONS
     * @param array|null $state see accountState(); null when gone
     * @param int $userID
     * @param int[] $protected
     * @return string
     */
    public static function decide( $action, $state, $userID, array $protected )
    {
        if ( in_array( (int)$userID, array_map( 'intval', $protected ), true ) )
            return 'protected';
        if ( !is_array( $state ) || empty( $state['exists'] ) )
            return 'gone';
        if ( empty( $state['has_object'] ) )
            return 'no_object';
        if ( empty( $state['is_user'] ) )
            return 'not_user';
        if ( empty( $state['can_edit'] ) )
            return 'denied';
        if ( empty( $state['main_node_id'] ) )
            return 'no_location';
        $locations = isset( $state['locations'] ) && is_array( $state['locations'] ) ? $state['locations'] : array();
        foreach ( $locations as $location )
        {
            if ( empty( $location['readable'] ) )
                return 'denied';
        }

        if ( $action === 'deactivate_move' )
        {
            if ( empty( $state['enabled'] ) && !empty( $state['in_group'] ) && count( $locations ) <= 1 )
                return 'unchanged';
            if ( empty( $state['group_ok'] ) )
                return empty( $state['group_exists'] ) ? 'no_group' : 'denied';
            foreach ( $locations as $location )
            {
                if ( !empty( $location['is_main'] ) )
                {
                    // the main location moves into the group, unless it is there already
                    if ( empty( $location['in_group'] ) && empty( $location['movable'] ) )
                        return 'denied';
                }
                elseif ( empty( $location['removable'] ) )
                    return 'denied';
                if ( !empty( $location['children'] ) )
                    return 'has_children';
            }
            return 'do';
        }

        if ( $action === 'reactivate_move' )
        {
            if ( empty( $state['enabled'] ) && !empty( $state['has_key'] ) )
                return 'unactivated';
            if ( !empty( $state['enabled'] ) && empty( $state['in_group'] ) && empty( $state['remembered'] ) )
                return 'unchanged';
            foreach ( $locations as $location )
            {
                if ( !empty( $location['is_main'] ) && !empty( $location['in_group'] ) && empty( $location['movable'] ) )
                    return 'denied';
            }
            if ( !empty( $state['in_group'] ) && empty( $state['target_ok'] ) )
                return 'no_target';
            return 'do';
        }
        return 'unchanged';
    }

    /**
     * Where an account goes back to: the remembered main parent when it still exists and may take the account,
     * else the fallback ([UserSettings] DefaultUserPlacement) on the same terms; the other remembered parents that
     * still exist and may take it are added, the rest are counted as not restored (gone and not allowed together, so
     * the count tells nothing about nodes the user who acts may not see).
     *
     * @param array|null $record see normaliseRecord()
     * @param callable $place ( int $nodeID ) => bool whether that node exists, may be read and may take the account
     * @param int $fallbackID
     * @param int $currentMainParent the parent of the account's main location now
     * @return array main (parent node id, 0 = none), fallback (bool), add (parent node ids), not_restored (int)
     */
    public static function planRestore( $record, $place, $fallbackID, $currentMainParent = 0 )
    {
        $plan = array( 'main' => 0, 'fallback' => false, 'add' => array(), 'not_restored' => 0 );
        $record = is_array( $record ) ? self::normaliseRecord( $record ) : null;
        if ( $record !== null )
        {
            if ( call_user_func( $place, $record['main'] ) )
                $plan['main'] = $record['main'];
            else
                $plan['not_restored']++;
        }
        if ( $plan['main'] === 0 )
        {
            $fallbackID = self::normaliseNodeID( $fallbackID );
            if ( $fallbackID > 0 && call_user_func( $place, $fallbackID ) )
            {
                $plan['main'] = $fallbackID;
                $plan['fallback'] = true;
            }
        }
        if ( $record !== null )
        {
            foreach ( $record['parents'] as $parent )
            {
                if ( $parent === $plan['main'] || $parent === (int)$currentMainParent )
                    continue;
                if ( call_user_func( $place, $parent ) )
                    $plan['add'][] = $parent;
                else
                    $plan['not_restored']++;
            }
        }
        return $plan;
    }

    /**
     * Decides for each account and does the action to those it may, each decided again just before.
     *
     * @param string $action
     * @param int[] $ids
     * @param callable $state ( int $id ) => array|null see decide()
     * @param callable $perform ( string $action, int $id ) => array|bool, false or ok=false when it failed
     * @param int[] $protected
     * @param bool $dryRun only decide (the confirmation page)
     * @return array done (ids), skipped (reason => ids), failed (ids), details (id => what perform returned)
     */
    public static function applyWith( $action, array $ids, $state, $perform, array $protected, $dryRun = false )
    {
        $result = array( 'done' => array(), 'skipped' => array(), 'failed' => array(), 'details' => array() );
        $protected = array_map( 'intval', $protected );
        foreach ( array_slice( expUnactivatedUsers::normaliseIDs( $ids ), 0, self::MAX_IDS ) as $id )
        {
            $decision = in_array( $id, $protected, true ) ? 'protected' : self::decide( $action, call_user_func( $state, $id ), $id, $protected );
            if ( $decision !== 'do' )
            {
                $result['skipped'][$decision][] = $id;
                continue;
            }
            if ( $dryRun )
            {
                $result['done'][] = $id;
                continue;
            }
            $done = call_user_func( $perform, $action, $id );
            $ok = is_array( $done ) ? !empty( $done['ok'] ) : (bool)$done;
            if ( is_array( $done ) )
                $result['details'][$id] = $done;
            if ( $ok )
                $result['done'][] = $id;
            else
                $result['failed'][] = $id;
        }
        return $result;
    }

    /* ---- this installation ---- */

    /**
     * The state decide() needs, read from this installation for the current user.
     *
     * @param int $id
     * @param eZContentObjectTreeNode|null $group the group's node, null when it does not exist yet
     * @param bool $groupCreatable whether the current user may create the group on first use
     * @return array|null
     */
    public static function accountState( $id, $group = null, $groupCreatable = false )
    {
        $id = (int)$id;
        $object = eZContentObject::fetch( $id );
        // an object the user who acts may not read is "gone" to them: no answer tells whether it exists or what it is
        if ( !$object instanceof eZContentObject || !$object->canRead() )
            return null;
        $state = array( 'exists' => true, 'has_object' => true, 'is_user' => eZUser::fetch( $id ) instanceof eZUser );
        if ( !$state['is_user'] )
            return $state;

        $setting = eZUserSetting::fetch( $id );
        $groupID = $group instanceof eZContentObjectTreeNode ? (int)$group->attribute( 'node_id' ) : 0;
        $classID = (int)$object->attribute( 'contentclass_id' );
        $state += array(
            'can_edit' => $object->canEdit(),
            'enabled' => !$setting || (int)$setting->attribute( 'is_enabled' ) !== 0,
            'has_key' => eZUserAccountKey::fetchByUserID( $id ) instanceof eZUserAccountKey,
            'main_node_id' => (int)$object->attribute( 'main_node_id' ),
            'locations' => array(),
            'in_group' => false,
            'group_exists' => $groupID > 0,
            'group_ok' => $groupID > 0 ? ( $group->canRead() && $group->canMoveTo( $classID ) ) : (bool)$groupCreatable,
            'remembered' => false,
            'target_ok' => false,
        );
        $mainParent = 0;
        foreach ( $object->assignedNodes( true ) as $node )
        {
            $isMain = (int)$node->attribute( 'node_id' ) === (int)$node->attribute( 'main_node_id' );
            $inGroup = $groupID > 0 && (int)$node->attribute( 'parent_node_id' ) === $groupID;
            if ( $isMain )
            {
                $mainParent = (int)$node->attribute( 'parent_node_id' );
                $state['in_group'] = $inGroup;
            }
            $state['locations'][] = array(
                'node_id' => (int)$node->attribute( 'node_id' ),
                'parent_node_id' => (int)$node->attribute( 'parent_node_id' ),
                'is_main' => $isMain,
                'in_group' => $inGroup,
                'readable' => $node->canRead(),
                'movable' => $isMain && $node->canMoveFrom(),
                'removable' => !$isMain && ( $node->canRemove() || $node->canRemoveLocation() ),
                'children' => (int)$node->childrenCount( false ) > 0,
            );
        }
        $record = self::remembered( $id );
        $state['remembered'] = $record !== null;
        $plan = self::planRestore( $record, function ( $nodeID ) use ( $classID ) { return self::mayPlace( $nodeID, $classID ); },
                                   self::fallbackNodeID(), $mainParent );
        $state['target_ok'] = $plan['main'] > 0;
        return $state;
    }

    /**
     * [UserSettings] DefaultUserPlacement, where an account goes back to when its main location is not remembered
     * or gone.
     *
     * @return int
     */
    public static function fallbackNodeID()
    {
        return self::normaliseNodeID( eZINI::instance()->variable( 'UserSettings', 'DefaultUserPlacement' ) );
    }

    /**
     * Whether a node exists, the current user may read it and may place an object of the class there
     * (content/create of that class, or the right to add a location there).
     *
     * @param int $nodeID
     * @param int $classID
     * @return bool
     */
    public static function mayPlace( $nodeID, $classID )
    {
        $node = eZContentObjectTreeNode::fetch( (int)$nodeID );
        if ( !$node instanceof eZContentObjectTreeNode || !$node->canRead() )
            return false;
        $parentObject = $node->object();
        if ( !$parentObject instanceof eZContentObject || (int)$parentObject->attribute( 'status' ) !== eZContentObject::STATUS_PUBLISHED )
            return false;
        return $node->checkAccess( 'create', (int)$classID, $parentObject->attribute( 'contentclass_id' ) ) == 1 || $node->canAddLocation();
    }

    /**
     * Runs a content operation through the operation handler (its workflow triggers included), or the operation
     * collection's method when the operation is not available, as the content module's views do.
     *
     * @param string $operation move, removelocation, addlocation
     * @param array $parameters
     * @return bool
     */
    protected static function operation( $operation, array $parameters )
    {
        if ( eZOperationHandler::operationIsAvailable( 'content_' . $operation ) )
        {
            $result = eZOperationHandler::execute( 'content', $operation, $parameters, null, true );
            return is_array( $result ) && isset( $result['status'] ) && $result['status'] == eZModuleOperationInfo::STATUS_CONTINUE;
        }
        switch ( $operation )
        {
            case 'move':
                $result = eZContentOperationCollection::moveNode( $parameters['node_id'], $parameters['object_id'], $parameters['new_parent_node_id'] );
                break;
            case 'removelocation':
                $result = eZContentOperationCollection::removeNodes( $parameters['node_list'] );
                break;
            case 'addlocation':
                $result = eZContentOperationCollection::addAssignment( $parameters['node_id'], $parameters['object_id'], $parameters['select_node_id_array'] );
                break;
            default:
                return false;
        }
        return is_array( $result ) && !empty( $result['status'] );
    }

    /**
     * Deactivates one account and brings its locations together in the group: remembers where it was, disables it
     * (expUserHygiene::perform(), which ends its sessions and records access.user.disable), removes the locations
     * other than the main one and moves the main one into the group. One transaction; nothing is kept when a step
     * fails. Records access.user.deactivate_move with the locations before and after.
     *
     * @param int $id
     * @param eZContentObjectTreeNode $group
     * @return array ok, error
     */
    public static function deactivate( $id, eZContentObjectTreeNode $group )
    {
        $id = (int)$id;
        $object = eZContentObject::fetch( $id );
        if ( !$object instanceof eZContentObject )
            return array( 'ok' => false, 'error' => 'gone' );
        $groupID = (int)$group->attribute( 'node_id' );
        $setting = eZUserSetting::fetch( $id );
        $wasEnabled = !$setting || (int)$setting->attribute( 'is_enabled' ) !== 0;

        $main = null;
        $others = array();
        foreach ( $object->assignedNodes( true ) as $node )
        {
            if ( (int)$node->attribute( 'node_id' ) === (int)$node->attribute( 'main_node_id' ) )
                $main = $node;
            else
                $others[] = $node;
        }
        if ( !$main )
            return array( 'ok' => false, 'error' => 'no_location' );

        $before = array( 'main' => (int)$main->attribute( 'parent_node_id' ), 'parents' => array() );
        foreach ( $others as $node )
            $before['parents'][] = (int)$node->attribute( 'parent_node_id' );
        $record = self::remembered( $id );
        // already in the group: keep what was remembered when it was moved there
        $keepRecord = $record !== null && $before['main'] === $groupID;

        $db = eZDB::instance();
        $db->begin();
        if ( !$keepRecord && $before['main'] !== $groupID )
            self::remember( $id, array( 'main' => $before['main'], 'parents' => $before['parents'], 'group' => $groupID,
                                        'at' => time(), 'by' => (int)eZUser::currentUserID() ) );
        $ok = !$wasEnabled || expUserHygiene::perform( 'disable', $id );
        $removeIDs = array();
        foreach ( $others as $node )
            $removeIDs[] = (int)$node->attribute( 'node_id' );
        if ( $ok && $removeIDs )
            $ok = self::operation( 'removelocation', array( 'node_list' => $removeIDs ) );
        if ( $ok && $before['main'] !== $groupID )
            $ok = self::operation( 'move', array( 'node_id' => (int)$main->attribute( 'node_id' ), 'object_id' => $id, 'new_parent_node_id' => $groupID ) );
        if ( !$ok )
        {
            $db->rollback();
            return array( 'ok' => false, 'error' => 'failed' );
        }
        $db->commit();
        eZContentObject::clearCache( array( $id ) );
        eZContentCacheManager::clearContentCacheIfNeeded( $id );

        if ( class_exists( 'expAuditHook' ) )
            expAuditHook::emit( 'access.user.deactivate_move', array( 'object' => expAuditHook::user( $id ),
                'before' => array( 'enabled' => $wasEnabled, 'main_parent_node_id' => $before['main'], 'parent_node_ids' => $before['parents'] ),
                'after' => array( 'enabled' => false, 'main_parent_node_id' => $groupID, 'parent_node_ids' => array(),
                                  'removed_node_ids' => $removeIDs, 'remembered' => !$keepRecord && $before['main'] !== $groupID ) ) );
        return array( 'ok' => true, 'error' => '' );
    }

    /**
     * Reactivates one account and puts it back: enables it (expUserHygiene::perform(), access.user.enable), moves
     * the main location to the remembered parent or the fallback (planRestore()) when it is in the group, adds the
     * other remembered locations that may be restored, and forgets the record. One transaction. Records
     * access.user.reactivate_move.
     *
     * @param int $id
     * @param eZContentObjectTreeNode|null $group
     * @return array ok, error, fallback (bool), not_restored (int), restored (parent node ids)
     */
    public static function reactivate( $id, $group = null )
    {
        $id = (int)$id;
        $object = eZContentObject::fetch( $id );
        if ( !$object instanceof eZContentObject )
            return array( 'ok' => false, 'error' => 'gone' );
        $groupID = $group instanceof eZContentObjectTreeNode ? (int)$group->attribute( 'node_id' ) : 0;
        $classID = (int)$object->attribute( 'contentclass_id' );
        $main = $object->mainNode();
        if ( !$main instanceof eZContentObjectTreeNode )
            return array( 'ok' => false, 'error' => 'no_location' );
        $mainParent = (int)$main->attribute( 'parent_node_id' );
        $inGroup = $groupID > 0 && $mainParent === $groupID;
        $setting = eZUserSetting::fetch( $id );
        $wasEnabled = !$setting || (int)$setting->attribute( 'is_enabled' ) !== 0;

        $record = self::remembered( $id );
        $plan = self::planRestore( $record, function ( $nodeID ) use ( $classID ) { return self::mayPlace( $nodeID, $classID ); },
                                   self::fallbackNodeID(), $mainParent );
        if ( $inGroup && $plan['main'] === 0 )
            return array( 'ok' => false, 'error' => 'no_target' );
        // not in the group (moved by hand meanwhile): the main location stays where it is
        if ( !$inGroup )
        {
            $plan['main'] = $mainParent;
            $plan['fallback'] = false;
        }
        $currentParents = array();
        foreach ( $object->assignedNodes( true ) as $node )
            $currentParents[] = (int)$node->attribute( 'parent_node_id' );
        $add = array_values( array_diff( $plan['add'], $currentParents ) );

        $db = eZDB::instance();
        $db->begin();
        $ok = $wasEnabled || expUserHygiene::perform( 'enable', $id );
        if ( $ok && $inGroup )
            $ok = self::operation( 'move', array( 'node_id' => (int)$main->attribute( 'node_id' ), 'object_id' => $id, 'new_parent_node_id' => $plan['main'] ) );
        if ( $ok && $add )
            $ok = self::operation( 'addlocation', array( 'node_id' => (int)$main->attribute( 'node_id' ), 'object_id' => $id, 'select_node_id_array' => $add ) );
        if ( !$ok )
        {
            $db->rollback();
            return array( 'ok' => false, 'error' => 'failed' );
        }
        self::forget( $id );
        $db->commit();
        eZContentObject::clearCache( array( $id ) );
        eZContentCacheManager::clearContentCacheIfNeeded( $id );

        if ( class_exists( 'expAuditHook' ) )
            expAuditHook::emit( 'access.user.reactivate_move', array( 'object' => expAuditHook::user( $id ),
                'before' => array( 'enabled' => $wasEnabled, 'main_parent_node_id' => $mainParent, 'parent_node_ids' => $currentParents ),
                'after' => array( 'enabled' => true, 'main_parent_node_id' => $plan['main'], 'added_parent_node_ids' => $add,
                                  'fallback' => $plan['fallback'], 'not_restored' => $plan['not_restored'], 'remembered' => $record !== null ) ) );
        return array( 'ok' => true, 'error' => '', 'fallback' => $plan['fallback'], 'not_restored' => $plan['not_restored'], 'restored' => $add );
    }

    /**
     * The groups of an account the confirmation page lists (name of each parent the current user may read; the
     * caller only lists accounts decide() allowed, whose every location may be read) and where it goes.
     *
     * @param int $id
     * @param string $action
     * @param eZContentObjectTreeNode|null $group
     * @return array name, groups (names), target (names), fallback (bool), not_restored (int)
     */
    public static function preview( $id, $action, $group = null )
    {
        $object = eZContentObject::fetch( (int)$id );
        $preview = array( 'id' => (int)$id, 'name' => '', 'groups' => array(), 'target' => array(), 'fallback' => false, 'not_restored' => 0 );
        if ( !$object instanceof eZContentObject )
            return $preview;
        $preview['name'] = (string)$object->attribute( 'name' );
        $mainParent = 0;
        $parentIDs = array();
        foreach ( $object->assignedNodes( true ) as $node )
        {
            $parentIDs[] = (int)$node->attribute( 'parent_node_id' );
            if ( (int)$node->attribute( 'node_id' ) === (int)$node->attribute( 'main_node_id' ) )
                $mainParent = (int)$node->attribute( 'parent_node_id' );
        }
        $preview['groups'] = self::readableNames( $parentIDs );
        if ( $action === 'deactivate_move' )
        {
            $preview['target'] = $group instanceof eZContentObjectTreeNode ? self::readableNames( array( (int)$group->attribute( 'node_id' ) ) )
                                                                          : array( ezpI18n::tr( self::CONTEXT, 'Deactivated users (created now)' ) );
            return $preview;
        }
        $classID = (int)$object->attribute( 'contentclass_id' );
        $plan = self::planRestore( self::remembered( (int)$id ), function ( $nodeID ) use ( $classID ) { return self::mayPlace( $nodeID, $classID ); },
                                   self::fallbackNodeID(), $mainParent );
        $groupID = $group instanceof eZContentObjectTreeNode ? (int)$group->attribute( 'node_id' ) : 0;
        $main = $groupID > 0 && $mainParent === $groupID ? $plan['main'] : $mainParent;
        $preview['target'] = self::readableNames( array_merge( array( $main ), $plan['add'] ) );
        $preview['fallback'] = $main === $plan['main'] && $plan['fallback'];
        $preview['not_restored'] = $plan['not_restored'];
        return $preview;
    }

    /**
     * The names of the nodes the current user may read, in the given order; the others are left out.
     *
     * @param int[] $nodeIDs
     * @return string[]
     */
    public static function readableNames( array $nodeIDs )
    {
        $names = array();
        foreach ( $nodeIDs as $nodeID )
        {
            $node = (int)$nodeID > 0 ? eZContentObjectTreeNode::fetch( (int)$nodeID ) : null;
            if ( $node instanceof eZContentObjectTreeNode && $node->canRead() )
                $names[(int)$nodeID] = (string)$node->attribute( 'name' );
        }
        return array_values( $names );
    }

    /**
     * What the node view and the context menu need for one object, for the current user: deactivate and
     * reactivate (whether to offer each). Both false when the feature is off, the user lacks user/deactivate, the
     * object is no user account, is protected or may not be edited; the page asked then decides again.
     *
     * @param int $objectID
     * @return array deactivate, reactivate
     */
    public static function offers( $objectID )
    {
        $offers = array( 'deactivate' => false, 'reactivate' => false );
        if ( !self::isEnabled() )
            return $offers;
        $user = eZUser::currentUser();
        $access = $user->hasAccessTo( 'user', 'deactivate' );
        if ( !isset( $access['accessWord'] ) || $access['accessWord'] === 'no' )
            return $offers;
        $objectID = (int)$objectID;
        if ( in_array( $objectID, expUserHygiene::protectedIDs( $user->attribute( 'contentobject_id' ) ), true ) )
            return $offers;
        $object = eZContentObject::fetch( $objectID );
        if ( !$object instanceof eZContentObject || !eZUser::fetch( $objectID ) instanceof eZUser || !$object->canEdit() )
            return $offers;
        $group = self::groupNode();
        $groupID = $group ? (int)$group->attribute( 'node_id' ) : 0;
        $setting = eZUserSetting::fetch( $objectID );
        $enabled = !$setting || (int)$setting->attribute( 'is_enabled' ) !== 0;
        $main = $object->mainNode();
        $inGroup = $groupID > 0 && $main instanceof eZContentObjectTreeNode && (int)$main->attribute( 'parent_node_id' ) === $groupID;
        $remembered = self::remembered( $objectID ) !== null;
        $offers['deactivate'] = $enabled || !$inGroup;
        $offers['reactivate'] = !$enabled || $inGroup || $remembered;
        return $offers;
    }
}
