<?php
/**
 * File containing the expUserGroupManager class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * User groups and role assignments as the console does them (exp:user:groups, exp:user:roles, exp:user:whoami), the
 * way the administration does them: a group is a content object of a user group class created, renamed (a new
 * version) and moved through the content operations and removed to the trash; a role is assigned and unassigned with
 * eZRole::assignToUser() and removeUserAssignmentByID(), which write access.role.assign and access.role.unassign to
 * the audit, and the role, policy and content caches are cleared afterwards as role/assign does.
 *
 * Every change is planned first and done only with $apply (see expUserManager).
 *
 * Guide: doc/eng-us/guides/user-management-from-the-console.md
 */
class expUserGroupManager
{
    /** The actions of exp:user:groups */
    const GROUP_ACTIONS = array( 'list', 'create', 'rename', 'move', 'delete' );

    /** The actions of exp:user:roles */
    const ROLE_ACTIONS = array( 'list', 'assign', 'unassign' );

    /** The prefix that names a group as the target of a role */
    const GROUP_PREFIX = 'group:';

    /** @return int the node of the users root (content.ini [NodeSettings] UserRootNode, 5 by default) */
    public static function rootNodeID()
    {
        $ini = eZINI::instance( 'content.ini' );
        $id = $ini->hasVariable( 'NodeSettings', 'UserRootNode' ) ? (int)$ini->variable( 'NodeSettings', 'UserRootNode' ) : 0;
        return $id > 0 ? $id : 5;
    }

    /**
     * A result of an action (expUserManager's form).
     *
     * @return array
     */
    protected static function result()
    {
        return array( 'changes' => array(), 'applied' => false, 'errors' => array(), 'warnings' => array() );
    }

    /**
     * The group below a group (or the users root), as a tree: every user group with its node id, name, parent, depth
     * below the start, direct members (accounts placed in it) and subgroups.
     *
     * @param string $ref '' for the users root
     * @return array groups (node_id, name, parent_node_id, depth, members, subgroups), error
     */
    public static function tree( $ref = '' )
    {
        if ( trim( (string)$ref ) === '' )
            $start = eZContentObjectTreeNode::fetch( self::rootNodeID() );
        else
        {
            $found = expUserManager::findGroup( $ref );
            if ( !$found['node'] )
                return array( 'groups' => array(), 'error' => $found['error'] );
            $start = $found['node'];
        }
        if ( !$start instanceof eZContentObjectTreeNode )
            return array( 'groups' => array(), 'error' => 'the users root node ' . self::rootNodeID() . ' does not exist ([NodeSettings] UserRootNode)' );
        $db = eZDB::instance();
        $classIDs = expUserManager::groupClassIDs();
        $userClassIDs = array_map( 'intval', (array)eZUser::contentClassIDs() );
        $path = $db->escapeString( (string)$start->attribute( 'path_string' ) );
        $rows = $db->arrayQuery( 'SELECT t.node_id, t.parent_node_id, t.depth, t.path_string, o.name FROM ezcontentobject_tree t'
                                 . ' INNER JOIN ezcontentobject o ON o.id = t.contentobject_id'
                                 . " WHERE t.path_string LIKE '$path%' AND " . $db->generateSQLINStatement( $classIDs, 'o.contentclass_id', false, true, 'int' )
                                 . ' ORDER BY t.path_string' );
        $counts = array();
        foreach ( (array)$db->arrayQuery( 'SELECT t.parent_node_id, o.contentclass_id, COUNT( * ) AS c FROM ezcontentobject_tree t'
                                          . ' INNER JOIN ezcontentobject o ON o.id = t.contentobject_id'
                                          . " WHERE t.path_string LIKE '$path%' GROUP BY t.parent_node_id, o.contentclass_id" ) as $row )
        {
            $parent = (int)$row['parent_node_id'];
            if ( !isset( $counts[$parent] ) )
                $counts[$parent] = array( 'members' => 0, 'subgroups' => 0 );
            if ( in_array( (int)$row['contentclass_id'], $userClassIDs, true ) )
                $counts[$parent]['members'] += (int)$row['c'];
            else if ( in_array( (int)$row['contentclass_id'], $classIDs, true ) )
                $counts[$parent]['subgroups'] += (int)$row['c'];
        }
        $startDepth = (int)$start->attribute( 'depth' );
        $groups = array();
        foreach ( (array)$rows as $row )
        {
            $id = (int)$row['node_id'];
            $groups[] = array( 'node_id' => $id, 'name' => (string)$row['name'], 'parent_node_id' => (int)$row['parent_node_id'],
                               'depth' => (int)$row['depth'] - $startDepth,
                               'members' => isset( $counts[$id] ) ? $counts[$id]['members'] : 0,
                               'subgroups' => isset( $counts[$id] ) ? $counts[$id]['subgroups'] : 0 );
        }
        return array( 'groups' => $groups, 'error' => '' );
    }

    /**
     * Creates a user group below a group (default the users root), published with its workflows.
     *
     * @param string $name
     * @param string $parentRef
     * @param bool $apply
     * @return array changes, applied, errors, warnings, node_id
     */
    public static function create( $name, $parentRef, $apply )
    {
        $r = self::result();
        $name = trim( (string)$name );
        if ( $name === '' )
            $r['errors'][] = 'name the new group with --name';
        $parent = self::groupOrRoot( $parentRef, $r );
        $class = eZContentClass::fetchByIdentifier( 'user_group' );
        if ( !$class instanceof eZContentClass )
            $r['errors'][] = 'the content class user_group does not exist';
        if ( $r['errors'] )
            return $r;
        $r['changes'][] = sprintf( 'create the user group %s below %s (node %d)', $name, $parent->attribute( 'name' ), $parent->attribute( 'node_id' ) );
        if ( !$apply )
            return $r;
        $object = eZContentFunctions::createAndPublishObject( array( 'parent_node_id' => (int)$parent->attribute( 'node_id' ), 'class_identifier' => 'user_group',
                                                                      'creator_id' => (int)eZUser::currentUserID(), 'attributes' => array( 'name' => $name ) ) );
        $node = $object instanceof eZContentObject ? $object->mainNode() : null;
        if ( !$node instanceof eZContentObjectTreeNode )
        {
            $r['errors'][] = 'the group could not be created' . ( $object instanceof eZContentObject ? ' (its publish waits for a workflow)' : '' );
            return $r;
        }
        $r['node_id'] = (int)$node->attribute( 'node_id' );
        $r['applied'] = true;
        return $r;
    }

    /**
     * Renames a user group: a new version with the new name, published with its workflows.
     *
     * @param string $ref
     * @param string $name
     * @param bool $apply
     * @return array
     */
    public static function rename( $ref, $name, $apply )
    {
        $r = self::result();
        $node = self::group( $ref, $r );
        $name = trim( (string)$name );
        if ( $name === '' )
            $r['errors'][] = 'give the new name with --name';
        if ( $r['errors'] )
            return $r;
        if ( $name === (string)$node->attribute( 'name' ) )
        {
            $r['warnings'][] = "the group is named $name already: nothing to do";
            return $r;
        }
        $r['node_id'] = (int)$node->attribute( 'node_id' );
        $r['changes'][] = sprintf( 'rename the user group %s (node %d) to %s', $node->attribute( 'name' ), $node->attribute( 'node_id' ), $name );
        if ( !$apply )
            return $r;
        if ( !eZContentFunctions::updateAndPublishObject( $node->object(), array( 'attributes' => array( 'name' => $name ) ) ) )
            $r['errors'][] = 'the new name could not be published (see the debug log)';
        else
            $r['applied'] = true;
        return $r;
    }

    /**
     * Moves a user group below another group (or the users root), as Move in the administration does; never below
     * itself.
     *
     * @param string $ref
     * @param string $toRef
     * @param bool $apply
     * @return array
     */
    public static function move( $ref, $toRef, $apply )
    {
        $r = self::result();
        $node = self::group( $ref, $r );
        if ( trim( (string)$toRef ) === '' )
            $r['errors'][] = 'name the new parent group with --to (a node id or name)';
        $to = $r['errors'] ? null : self::groupOrRoot( $toRef, $r );
        if ( $r['errors'] )
            return $r;
        $r['node_id'] = (int)$node->attribute( 'node_id' );
        if ( (int)$node->attribute( 'node_id' ) === self::rootNodeID() )
            $r['errors'][] = 'the users root is never moved';
        else if ( strpos( (string)$to->attribute( 'path_string' ), (string)$node->attribute( 'path_string' ) ) === 0 )
            $r['errors'][] = 'a group cannot be moved below itself';
        else if ( (int)$node->attribute( 'parent_node_id' ) === (int)$to->attribute( 'node_id' ) )
            $r['warnings'][] = 'the group is below ' . $to->attribute( 'name' ) . ' already: nothing to do';
        if ( $r['errors'] || $r['warnings'] )
            return $r;
        $r['changes'][] = sprintf( 'move the user group %s (node %d) below %s (node %d)', $node->attribute( 'name' ), $node->attribute( 'node_id' ),
                                   $to->attribute( 'name' ), $to->attribute( 'node_id' ) );
        if ( !$apply )
            return $r;
        $result = eZOperationHandler::operationIsAvailable( 'content_move' )
                ? eZOperationHandler::execute( 'content', 'move', array( 'node_id' => (int)$node->attribute( 'node_id' ),
                                                                       'object_id' => (int)$node->attribute( 'contentobject_id' ),
                                                                       'new_parent_node_id' => (int)$to->attribute( 'node_id' ) ), null, true )
                : eZContentOperationCollection::moveNode( (int)$node->attribute( 'node_id' ), (int)$node->attribute( 'contentobject_id' ), (int)$to->attribute( 'node_id' ) );
        eZContentObject::clearCache();
        $moved = eZContentObjectTreeNode::fetch( (int)$node->attribute( 'node_id' ) );
        if ( !$moved instanceof eZContentObjectTreeNode || (int)$moved->attribute( 'parent_node_id' ) !== (int)$to->attribute( 'node_id' ) )
            $r['errors'][] = 'the group was not moved (a workflow may have stopped it'
                           . ( is_array( $result ) && isset( $result['status'] ) ? ', status ' . $result['status'] : '' ) . ')';
        else
        {
            eZUser::cleanupCache();
            $r['applied'] = true;
        }
        return $r;
    }

    /**
     * Removes an empty user group to the trash, as the administration does by default; refused for the users root
     * and for a group that still has members or subgroups (move them first: removing a group never removes accounts).
     *
     * @param string $ref
     * @param bool $apply
     * @return array
     */
    public static function delete( $ref, $apply )
    {
        $r = self::result();
        $node = self::group( $ref, $r );
        if ( $r['errors'] )
            return $r;
        $r['node_id'] = (int)$node->attribute( 'node_id' );
        if ( (int)$node->attribute( 'node_id' ) === self::rootNodeID() )
            $r['errors'][] = 'the users root is never removed';
        else
        {
            $children = (int)$node->subTreeCount( array( 'Limitation' => array(), 'IgnoreVisibility' => true ) );
            if ( $children > 0 )
                $r['errors'][] = sprintf( 'the group %s still holds %d account(s) or group(s): move them first (exp:user:edit --remove-group, exp:user:groups move); removing a group never removes accounts',
                                          $node->attribute( 'name' ), $children );
            $roles = (array)eZRole::fetchByUser( array( (int)$node->attribute( 'contentobject_id' ) ) );
            if ( $roles )
                $r['warnings'][] = 'its role assignments go with it: ' . implode( ', ', array_map( function ( $role ) { return $role->attribute( 'name' ); }, $roles ) );
        }
        if ( $r['errors'] )
            return $r;
        $r['changes'][] = sprintf( 'move the empty user group %s (node %d) to the trash', $node->attribute( 'name' ), $node->attribute( 'node_id' ) );
        if ( !$apply )
            return $r;
        eZContentObjectTreeNode::removeSubtrees( array( (int)$node->attribute( 'node_id' ) ), true );
        eZContentObject::clearCache();
        if ( eZContentObjectTreeNode::fetch( (int)$node->attribute( 'node_id' ) ) instanceof eZContentObjectTreeNode )
            $r['errors'][] = 'the group is still there: the user who acts may not remove it, or see the debug log';
        else
        {
            eZUser::cleanupCache();
            $r['applied'] = true;
        }
        return $r;
    }

    /**
     * The group a reference names, an error in the result when none.
     *
     * @param string $ref
     * @param array $r
     * @return eZContentObjectTreeNode|null
     */
    protected static function group( $ref, array &$r )
    {
        if ( trim( (string)$ref ) === '' )
        {
            $r['errors'][] = 'name the group: its node id or its name';
            return null;
        }
        $found = expUserManager::findGroup( $ref );
        if ( !$found['node'] )
            $r['errors'][] = $found['error'];
        return $found['node'];
    }

    /**
     * The group a reference names, or the users root for ''.
     *
     * @param string $ref
     * @param array $r
     * @return eZContentObjectTreeNode|null
     */
    protected static function groupOrRoot( $ref, array &$r )
    {
        if ( trim( (string)$ref ) !== '' && (int)$ref !== self::rootNodeID() )
            return self::group( $ref, $r );
        $root = eZContentObjectTreeNode::fetch( self::rootNodeID() );
        if ( !$root instanceof eZContentObjectTreeNode )
            $r['errors'][] = 'the users root node ' . self::rootNodeID() . ' does not exist ([NodeSettings] UserRootNode)';
        return $root;
    }

    // ---- roles ------------------------------------------------------------------------------------------------------

    /**
     * The role a reference names: an id, or a name (any case).
     *
     * @param string $ref
     * @return array role (eZRole|null), error
     */
    public static function findRole( $ref )
    {
        $ref = trim( (string)$ref );
        if ( $ref === '' )
            return array( 'role' => null, 'error' => 'name the role: its id or its name' );
        $role = ctype_digit( $ref ) ? eZRole::fetch( (int)$ref ) : null;
        if ( !$role instanceof eZRole )
        {
            $db = eZDB::instance();
            $rows = $db->arrayQuery( "SELECT id FROM ezrole WHERE version = 0 AND LOWER( name ) = '" . $db->escapeString( mb_strtolower( $ref ) ) . "'" );
            if ( count( (array)$rows ) > 1 )
                return array( 'role' => null, 'error' => count( $rows ) . " roles are named $ref: name the role by its id" );
            $role = $rows ? eZRole::fetch( (int)$rows[0]['id'] ) : null;
        }
        return $role instanceof eZRole ? array( 'role' => $role, 'error' => '' ) : array( 'role' => null, 'error' => "no role has the id or name $ref" );
    }

    /**
     * Whom a role target names: a user (login, e-mail or id) or a group (group:<node id or name>). Pure on the kind.
     *
     * @param string $ref
     * @return string user, group or ''
     */
    public static function targetKind( $ref )
    {
        $ref = trim( (string)$ref );
        if ( $ref === '' )
            return '';
        return stripos( $ref, self::GROUP_PREFIX ) === 0 ? 'group' : 'user';
    }

    /**
     * The object a role target names.
     *
     * @param string $ref
     * @return array object_id (0 when none), type (user, group), label, node (the group's node), error
     */
    public static function findTarget( $ref )
    {
        $ref = trim( (string)$ref );
        $out = array( 'object_id' => 0, 'type' => self::targetKind( $ref ), 'label' => '', 'node' => null, 'error' => '' );
        if ( $out['type'] === '' )
            $out['error'] = 'name whom: a user (login, e-mail or id) or group:<node id or name>';
        else if ( $out['type'] === 'group' )
        {
            $found = expUserManager::findGroup( substr( $ref, strlen( self::GROUP_PREFIX ) ) );
            if ( !$found['node'] )
                $out['error'] = $found['error'];
            else
            {
                $out['node'] = $found['node'];
                $out['object_id'] = (int)$found['node']->attribute( 'contentobject_id' );
                $out['label'] = 'the group ' . $found['node']->attribute( 'name' ) . ' (node ' . $found['node']->attribute( 'node_id' ) . ')';
            }
        }
        else
        {
            $found = expUserManager::find( $ref );
            if ( !$found['user'] )
                $out['error'] = $found['error'];
            else
            {
                $out['object_id'] = (int)$found['user']->attribute( 'contentobject_id' );
                $out['label'] = 'the account ' . $found['user']->attribute( 'login' ) . ' (id ' . $out['object_id'] . ')';
            }
        }
        return $out;
    }

    /**
     * The role assignments that reach a target: its own and those of the groups above it, each with the role, where
     * it comes from and its limitation.
     *
     * @param array $target see findTarget()
     * @return array[] role, role_id, via, via_name, limitation, id (the assignment)
     */
    public static function assignments( array $target )
    {
        if ( $target['type'] === 'user' )
        {
            $user = eZUser::fetch( (int)$target['object_id'] );
            return $user instanceof eZUser ? expUserManager::roleAssignments( $user ) : array();
        }
        $holders = array( (int)$target['object_id'] );
        $node = $target['node'];
        foreach ( explode( '/', trim( (string)$node->attribute( 'path_string' ), '/' ) ) as $ancestor )
        {
            if ( !ctype_digit( $ancestor ) || (int)$ancestor <= 1 || (int)$ancestor === (int)$node->attribute( 'node_id' ) )
                continue;
            $n = eZContentObjectTreeNode::fetch( (int)$ancestor );
            if ( $n instanceof eZContentObjectTreeNode )
                $holders[] = (int)$n->attribute( 'contentobject_id' );
        }
        return self::rowsFor( $holders );
    }

    /**
     * The assignments of these holders, by role name.
     *
     * @param int[] $holders
     * @return array[]
     */
    protected static function rowsFor( array $holders )
    {
        $db = eZDB::instance();
        $holders = array_values( array_unique( array_filter( array_map( 'intval', $holders ) ) ) );
        if ( !$holders )
            return array();
        $out = array();
        foreach ( (array)$db->arrayQuery( 'SELECT ur.id, ur.contentobject_id, ur.limit_identifier, ur.limit_value, r.id AS role_id, r.name, o.name AS via_name'
                                          . ' FROM ezuser_role ur INNER JOIN ezrole r ON r.id = ur.role_id LEFT JOIN ezcontentobject o ON o.id = ur.contentobject_id'
                                          . ' WHERE r.version = 0 AND ' . $db->generateSQLINStatement( $holders, 'ur.contentobject_id', false, true, 'int' )
                                          . ' ORDER BY r.name, ur.contentobject_id' ) as $row )
            $out[] = array( 'role' => (string)$row['name'], 'role_id' => (int)$row['role_id'], 'via' => (int)$row['contentobject_id'],
                            'via_name' => (string)$row['via_name'],
                            'limitation' => (string)$row['limit_identifier'] !== '' ? $row['limit_identifier'] . ':' . $row['limit_value'] : '',
                            'id' => (int)$row['id'] );
        return $out;
    }

    /**
     * The limitation of an assignment from --subtree and --section, checked: identifier ('' none, subtree, section) and
     * value (a node id, a section id), as eZRole::assignToUser() takes them, and the form stored (Subtree:<path>,
     * Section:<id>).
     *
     * @param string $subtree a node id or ''
     * @param string $section a section id or identifier or ''
     * @return array identifier, value, stored, error
     */
    public static function limitation( $subtree, $section )
    {
        $subtree = trim( (string)$subtree );
        $section = trim( (string)$section );
        $out = array( 'identifier' => '', 'value' => '', 'stored' => '', 'error' => '' );
        if ( $subtree !== '' && $section !== '' )
            $out['error'] = '--subtree and --section exclude each other: a role is assigned with one limitation at a time';
        else if ( $subtree !== '' )
        {
            $node = ctype_digit( $subtree ) ? eZContentObjectTreeNode::fetch( (int)$subtree, false, false ) : null;
            if ( !$node )
                $out['error'] = "--subtree: no node has the id $subtree";
            else
                $out = array( 'identifier' => 'subtree', 'value' => (string)(int)$subtree, 'stored' => 'Subtree:' . $node['path_string'], 'error' => '' );
        }
        else if ( $section !== '' )
        {
            $found = ctype_digit( $section ) ? eZSection::fetch( (int)$section ) : eZSection::fetchByIdentifier( $section );
            if ( !$found instanceof eZSection )
                $out['error'] = "--section: no section has the id or identifier $section";
            else
                $out = array( 'identifier' => 'section', 'value' => (string)(int)$found->attribute( 'id' ), 'stored' => 'Section:' . (int)$found->attribute( 'id' ),
                              'error' => '' );
        }
        return $out;
    }

    /**
     * Assigns a role to a user or group, with or without a limitation, as role/assign does.
     *
     * @param eZRole $role
     * @param array $target see findTarget()
     * @param array $limitation see limitation()
     * @param bool $apply
     * @return array
     */
    public static function assign( eZRole $role, array $target, array $limitation, $apply )
    {
        $r = self::result();
        foreach ( self::rowsFor( array( $target['object_id'] ) ) as $row )
            if ( $row['role_id'] === (int)$role->attribute( 'id' ) && $row['limitation'] === $limitation['stored'] )
            {
                $r['warnings'][] = $target['label'] . ' has the role ' . $role->attribute( 'name' ) . ( $limitation['stored'] !== '' ? ' (' . $limitation['stored'] . ')' : '' )
                                 . ' already: nothing to do';
                return $r;
            }
        $r['changes'][] = 'assign the role ' . $role->attribute( 'name' ) . ' to ' . $target['label']
                        . ( $limitation['stored'] !== '' ? ', limited to ' . $limitation['stored'] : ', without a limitation' );
        if ( !$apply )
            return $r;
        $db = eZDB::instance();
        $db->begin();
        try
        {
            $done = $role->assignToUser( (int)$target['object_id'], $limitation['identifier'], $limitation['value'] );
            eZRole::expireCache();
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();
        self::clearCaches();
        if ( !$done )
            $r['errors'][] = 'the role was not assigned (see the debug log)';
        else
            $r['applied'] = true;
        return $r;
    }

    /**
     * Removes the assignments of a role from a user or group: all of them, or only the one with the limitation
     * given.
     *
     * @param eZRole $role
     * @param array $target
     * @param array $limitation
     * @param bool $apply
     * @return array
     */
    public static function unassign( eZRole $role, array $target, array $limitation, $apply )
    {
        $r = self::result();
        $ids = array();
        foreach ( self::rowsFor( array( $target['object_id'] ) ) as $row )
            if ( $row['role_id'] === (int)$role->attribute( 'id' ) && ( $limitation['stored'] === '' || $row['limitation'] === $limitation['stored'] ) )
            {
                $ids[] = $row['id'];
                $r['changes'][] = 'unassign the role ' . $role->attribute( 'name' ) . ' from ' . $target['label']
                                . ( $row['limitation'] !== '' ? ' (' . $row['limitation'] . ')' : ' (without a limitation)' );
            }
        if ( !$ids )
        {
            $r['warnings'][] = $target['label'] . ' has no such assignment of the role ' . $role->attribute( 'name' ) . ': nothing to do'
                             . ( $target['type'] === 'user' ? ' (a role that comes from a group is unassigned from the group: group:<node>)' : '' );
            return $r;
        }
        if ( !$apply )
            return $r;
        $db = eZDB::instance();
        $db->begin();
        try
        {
            foreach ( $ids as $id )
                $role->removeUserAssignmentByID( $id );
            eZRole::expireCache();
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();
        self::clearCaches();
        $r['applied'] = true;
        return $r;
    }

    /** The caches role/assign clears after a change of assignments: content and the users' policies. */
    protected static function clearCaches()
    {
        eZContentCacheManager::clearAllContentCache();
        eZUser::cleanupCache();
    }
}
