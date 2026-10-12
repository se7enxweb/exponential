<?php
/**
 * exprole: roles, their editing drafts and their assignments to users and groups.
 * A role is edited as a draft (draftCreate, draftAddPolicy, ..., publish) like the role editor does, or its
 * policies are changed directly with exppolicy. Assignments take an optional limitation (subtree node id or
 * section id).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expRoleServices extends expUsersBase
{
    /**
     * The services (expServiceBase), declared here rather than assigned after the class: a persistent worker
     * (Velocity) puts the statics of a class loaded during a request back to their declared value before the
     * next one, and an assignment after the class would not run again.
     */
    public static $services = array(
        'listAll' => array( 'summary' => 'All roles, paged', 'access' => array( 'role', 'list' ), 'write' => false, 'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged roles' ),
        'count' => array( 'summary' => 'The number of roles', 'access' => array( 'role', 'list' ), 'write' => false, 'args' => array(), 'returns' => 'count' ),
        'fetch' => array( 'summary' => 'A role by id', 'access' => array( 'role', 'list' ), 'write' => false, 'args' => array( 'id' => 'int' ), 'returns' => 'role' ),
        'view' => array( 'summary' => 'A role with its policies and assignments', 'access' => array( 'role', 'view' ), 'write' => false, 'args' => array( 'id' => 'int' ), 'returns' => 'role, policies, assignments' ),
        'byName' => array( 'summary' => 'A role by name', 'access' => array( 'role', 'list' ), 'write' => false, 'args' => array( 'name' => 'string' ), 'returns' => 'role' ),
        'exists' => array( 'summary' => 'Whether a role name exists', 'access' => array( 'role', 'list' ), 'write' => false, 'args' => array( 'name' => 'string' ), 'returns' => 'exists' ),
        'policies' => array( 'summary' => 'The policies of a role, paged', 'access' => array( 'role', 'view' ), 'write' => false, 'args' => array( 'id' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged policies' ),
        'policyCount' => array( 'summary' => 'The number of policies of a role', 'access' => array( 'role', 'view' ), 'write' => false, 'args' => array( 'id' => 'int' ), 'returns' => 'count' ),
        'assignments' => array( 'summary' => 'Who a role is assigned to, with limitations', 'access' => array( 'role', 'view' ), 'write' => false, 'args' => array( 'id' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged assignments' ),
        'assignmentsOf' => array( 'summary' => 'The roles assigned to a user or group object', 'access' => array( 'role', 'view' ), 'write' => false, 'args' => array( 'object' => 'int' ), 'returns' => 'assignments' ),
        'byLimitation' => array( 'summary' => 'Roles assigned with a limitation (Subtree/Section, value)', 'access' => array( 'role', 'view' ), 'write' => false, 'args' => array( 'identifier' => 'string', 'value' => 'string' ), 'returns' => 'roles' ),
        'create' => array( 'summary' => 'Create an empty role (POST name)', 'access' => array( 'role', 'edit' ), 'write' => true, 'args' => array( 'name' => 'string' ), 'returns' => 'role' ),
        'rename' => array( 'summary' => 'Rename a role (POST id, name)', 'access' => array( 'role', 'edit' ), 'write' => true, 'args' => array( 'id' => 'int', 'name' => 'string' ), 'returns' => 'role' ),
        'copy' => array( 'summary' => 'Copy a role with its policies (POST id, optional name)', 'access' => array( 'role', 'edit' ), 'write' => true, 'args' => array( 'id' => 'int', 'name' => 'string' ), 'returns' => 'role' ),
        'remove' => array( 'summary' => 'Remove a role (POST id, force when it is assigned)', 'access' => array( 'role', 'edit' ), 'write' => true, 'args' => array( 'id' => 'int', 'force' => 'bool' ), 'returns' => 'removed' ),
        'draftCreate' => array( 'summary' => 'Start editing: create (or return) the draft of a role (POST id)', 'access' => array( 'role', 'edit' ), 'write' => true, 'args' => array( 'id' => 'int' ), 'returns' => 'draft role' ),
        'draft' => array( 'summary' => 'The draft of a role', 'access' => array( 'role', 'edit' ), 'write' => false, 'args' => array( 'id' => 'int' ), 'returns' => 'draft role' ),
        'draftRename' => array( 'summary' => 'Rename the draft (POST id, name)', 'access' => array( 'role', 'edit' ), 'write' => true, 'args' => array( 'id' => 'int', 'name' => 'string' ), 'returns' => 'draft role' ),
        'draftAddPolicy' => array( 'summary' => 'Add a policy to the draft (POST id, module, function, limitations JSON)', 'access' => array( 'role', 'edit' ), 'write' => true, 'args' => array( 'id' => 'int', 'module' => 'string', 'function' => 'string', 'limitations' => 'json' ), 'returns' => 'policy' ),
        'draftRemovePolicy' => array( 'summary' => 'Remove a policy from the draft (POST id, policy)', 'access' => array( 'role', 'edit' ), 'write' => true, 'args' => array( 'id' => 'int', 'policy' => 'int' ), 'returns' => 'draft role' ),
        'draftDiscard' => array( 'summary' => 'Throw the draft away (POST id)', 'access' => array( 'role', 'edit' ), 'write' => true, 'args' => array( 'id' => 'int' ), 'returns' => 'draft_discarded' ),
        'publish' => array( 'summary' => 'Publish the draft: it replaces the policies of the role (POST id)', 'access' => array( 'role', 'edit' ), 'write' => true, 'args' => array( 'id' => 'int' ), 'returns' => 'role' ),
        'assign' => array( 'summary' => 'Assign a role to a user or group (POST id, object, limit subtree|section, limit_value)', 'access' => array( 'role', 'assign' ), 'write' => true, 'args' => array( 'id' => 'int', 'object' => 'int', 'limit' => 'string', 'limit_value' => 'string' ), 'returns' => 'assignments' ),
        'unassign' => array( 'summary' => 'Remove an assignment (POST id, object, optional assignment id)', 'access' => array( 'role', 'assign' ), 'write' => true, 'args' => array( 'id' => 'int', 'object' => 'int', 'assignment' => 'int' ), 'returns' => 'assignments' ),
        'assigned' => array( 'summary' => 'The objects (users, groups) a role is assigned to', 'access' => array( 'role', 'view' ), 'write' => false, 'args' => array( 'id' => 'int' ), 'returns' => 'objects' ),
        'usersWith' => array( 'summary' => 'The users and groups holding a role', 'access' => array( 'role', 'view' ), 'write' => false, 'args' => array( 'id' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged objects' ),
    );

    protected static function draftOf( eZRole $role, $required = true )
    {
        $draft = eZRole::fetch( 0, (int)$role->attribute( 'id' ) );
        if ( $required && !$draft instanceof eZRole )
            throw new expServiceException( 'The role has no draft; create one with draftCreate', 409 );
        return $draft instanceof eZRole ? $draft : null;
    }

    protected static function assignmentRows( eZRole $role )
    {
        $db = eZDB::instance();
        $rows = $db->arrayQuery( 'SELECT id, contentobject_id, limit_identifier, limit_value FROM ezuser_role WHERE role_id = ' . (int)$role->attribute( 'id' ) . ' ORDER BY id' );
        $out = array();
        foreach ( $rows as $r )
        {
            $object = eZContentObject::fetch( (int)$r['contentobject_id'] );
            $out[] = array( 'assignment_id' => (int)$r['id'], 'object_id' => (int)$r['contentobject_id'],
                            'name' => $object ? $object->attribute( 'name' ) : '', 'class_identifier' => $object ? $object->attribute( 'class_identifier' ) : '',
                            'limit_identifier' => (string)$r['limit_identifier'], 'limit_value' => (string)$r['limit_value'] );
        }
        return $out;
    }

    public static function listAll( array $a = array() )
    {
        self::guard( 'listAll' );
        list( $limit, $offset ) = self::paging( $a, 0, 1 );
        $roles = eZRole::fetchByOffset( $offset, $limit, true, true, true );
        return self::page( array_map( array( 'expUsersBase', 'exportRole' ), (array)$roles ), eZRole::roleCount(), $offset, $limit );
    }

    public static function count( array $a = array() )
    {
        self::guard( 'count' );
        return self::ok( array( 'count' => (int)eZRole::roleCount() ) );
    }

    public static function fetch( array $a = array() )
    {
        self::guard( 'fetch' );
        return self::ok( self::exportRole( self::role( self::arg( $a, 0, 'int' ) ) ) );
    }

    public static function view( array $a = array() )
    {
        self::guard( 'view' );
        $role = self::role( self::arg( $a, 0, 'int' ) );
        return self::ok( self::exportRole( $role, true ) + array( 'assignments' => self::assignmentRows( $role ), 'has_draft' => self::draftOf( $role, false ) !== null ) );
    }

    public static function byName( array $a = array() )
    {
        self::guard( 'byName' );
        $role = eZRole::fetchByName( self::arg( $a, 0, 'string' ) );
        if ( !$role instanceof eZRole )
            throw new expServiceException( 'No such role', 404 );
        return self::ok( self::exportRole( $role ) );
    }

    public static function exists( array $a = array() )
    {
        self::guard( 'exists' );
        return self::ok( array( 'exists' => eZRole::fetchByName( self::arg( $a, 0, 'string' ) ) instanceof eZRole ) );
    }

    public static function policies( array $a = array() )
    {
        self::guard( 'policies' );
        $role = self::role( self::arg( $a, 0, 'int' ) );
        return self::pageOf( array_map( array( 'expUsersBase', 'exportPolicy' ), $role->policyList() ), $a, 1, 2 );
    }

    public static function policyCount( array $a = array() )
    {
        self::guard( 'policyCount' );
        $role = self::role( self::arg( $a, 0, 'int' ) );
        return self::ok( array( 'count' => (int)$role->policyCount() ) );
    }

    public static function assignments( array $a = array() )
    {
        self::guard( 'assignments' );
        $role = self::role( self::arg( $a, 0, 'int' ) );
        return self::pageOf( self::assignmentRows( $role ), $a, 1, 2 );
    }

    public static function assignmentsOf( array $a = array() )
    {
        self::guard( 'assignmentsOf' );
        $objectId = self::arg( $a, 0, 'int' );
        $rows = eZDB::instance()->arrayQuery( 'SELECT id, role_id, limit_identifier, limit_value FROM ezuser_role WHERE contentobject_id = ' . $objectId . ' ORDER BY id' );
        $out = array();
        foreach ( $rows as $r )
        {
            $role = eZRole::fetch( (int)$r['role_id'] );
            $out[] = array( 'assignment_id' => (int)$r['id'], 'role_id' => (int)$r['role_id'], 'role' => $role ? $role->attribute( 'name' ) : '',
                            'limit_identifier' => (string)$r['limit_identifier'], 'limit_value' => (string)$r['limit_value'] );
        }
        return self::ok( $out );
    }

    public static function byLimitation( array $a = array() )
    {
        self::guard( 'byLimitation' );
        $roles = eZRole::fetchRolesByLimitation( self::arg( $a, 0, 'string' ), self::arg( $a, 1, 'string' ) );
        $out = array();
        foreach ( (array)$roles as $r )
            if ( $r['role'] instanceof eZRole )
                $out[] = self::exportRole( $r['role'] ) + array( 'assigned_to' => $r['user'] ? (int)$r['user']->attribute( 'id' ) : null );
        return self::ok( $out );
    }

    public static function create( array $a = array() )
    {
        self::guard( 'create' );
        $name = trim( self::post( 'name', 'string' ) );
        if ( $name === '' )
            throw new expServiceException( 'The role needs a name', 422 );
        if ( eZRole::fetchByName( $name ) instanceof eZRole )
            throw new expServiceException( 'A role with this name exists', 409 );
        $role = eZRole::createNew();
        $role->setAttribute( 'name', $name );
        $role->setAttribute( 'is_new', 0 );
        $role->store();
        self::audit( 'access.role.create', array( 'object' => array( 'type' => 'role', 'id' => (int)$role->attribute( 'id' ) ), 'after' => array( 'name' => $name ) ) );
        return self::ok( self::exportRole( $role ) );
    }

    public static function rename( array $a = array() )
    {
        self::guard( 'rename' );
        $role = self::role( self::post( 'id', 'int' ) );
        $name = trim( self::post( 'name', 'string' ) );
        if ( $name === '' )
            throw new expServiceException( 'The role needs a name', 422 );
        $other = eZRole::fetchByName( $name );
        if ( $other instanceof eZRole && (int)$other->attribute( 'id' ) !== (int)$role->attribute( 'id' ) )
            throw new expServiceException( 'A role with this name exists', 409 );
        $role->setAttribute( 'name', $name );
        $role->store();
        return self::ok( self::exportRole( $role ) );
    }

    public static function copy( array $a = array() )
    {
        self::guard( 'copy' );
        $role = self::role( self::post( 'id', 'int' ) );
        $new = $role->copy();
        $name = self::post( 'name', 'string', '' );
        if ( trim( $name ) !== '' )
        {
            $new->setAttribute( 'name', trim( $name ) );
            $new->store();
        }
        return self::ok( self::exportRole( $new, true ) );
    }

    public static function remove( array $a = array() )
    {
        self::guard( 'remove' );
        $role = self::role( self::post( 'id', 'int' ) );
        $assigned = count( self::assignmentRows( $role ) );
        if ( $assigned > 0 && !self::post( 'force', 'bool', false ) )
            throw new expServiceException( "The role is assigned $assigned times; send force=1 to remove it with its assignments", 409 );
        $id = (int)$role->attribute( 'id' );
        $name = $role->attribute( 'name' );
        $draft = self::draftOf( $role, false );
        if ( $draft )
            eZRole::removeRole( (int)$draft->attribute( 'id' ) );
        eZRole::removeRole( $id );
        eZUser::cleanupCache();
        self::audit( 'access.role.remove', array( 'object' => array( 'type' => 'role', 'id' => $id ), 'before' => array( 'name' => $name ) ) );
        return self::ok( array( 'id' => $id, 'removed' => true, 'assignments_removed' => $assigned ) );
    }

    public static function draftCreate( array $a = array() )
    {
        self::guard( 'draftCreate' );
        $role = self::role( self::post( 'id', 'int' ) );
        $draft = self::draftOf( $role, false );
        if ( !$draft )
            $draft = $role->createTemporaryVersion();
        return self::ok( self::exportRole( $draft, true ) );
    }

    public static function draft( array $a = array() )
    {
        self::guard( 'draft' );
        $role = self::role( self::arg( $a, 0, 'int' ) );
        return self::ok( self::exportRole( self::draftOf( $role ), true ) );
    }

    public static function draftRename( array $a = array() )
    {
        self::guard( 'draftRename' );
        $draft = self::draftOf( self::role( self::post( 'id', 'int' ) ) );
        $name = trim( self::post( 'name', 'string' ) );
        if ( $name === '' )
            throw new expServiceException( 'The role needs a name', 422 );
        $draft->setAttribute( 'name', $name );
        $draft->store();
        return self::ok( self::exportRole( $draft, true ) );
    }

    public static function draftAddPolicy( array $a = array() )
    {
        self::guard( 'draftAddPolicy' );
        $draft = self::draftOf( self::role( self::post( 'id', 'int' ) ) );
        $module = self::post( 'module', 'string' );
        $function = self::post( 'function', 'string' );
        $limits = self::checkedLimits( $module, $function, self::post( 'limitations', 'json', array() ) );
        $policy = $draft->appendPolicy( $module, $function, $limits );
        return self::ok( self::exportPolicy( $policy ) );
    }

    public static function draftRemovePolicy( array $a = array() )
    {
        self::guard( 'draftRemovePolicy' );
        $draft = self::draftOf( self::role( self::post( 'id', 'int' ) ) );
        $policy = eZPolicy::fetch( self::post( 'policy', 'int' ) );
        if ( !$policy instanceof eZPolicy || (int)$policy->attribute( 'role_id' ) !== (int)$draft->attribute( 'id' ) )
            throw new expServiceException( 'No such policy in the draft', 404 );
        $policy->removeThis();
        return self::ok( self::exportRole( $draft, true ) );
    }

    public static function draftDiscard( array $a = array() )
    {
        self::guard( 'draftDiscard' );
        $role = self::role( self::post( 'id', 'int' ) );
        $draft = self::draftOf( $role );
        $draft->removePolicies( true );
        eZRole::removeRole( (int)$draft->attribute( 'id' ) );
        return self::ok( array( 'id' => (int)$role->attribute( 'id' ), 'draft_discarded' => true ) );
    }

    public static function publish( array $a = array() )
    {
        self::guard( 'publish' );
        $role = self::role( self::post( 'id', 'int' ) );
        self::draftOf( $role );
        $role->revertFromTemporaryVersion();
        eZUser::cleanupCache();
        self::audit( 'access.role.change', array( 'object' => array( 'type' => 'role', 'id' => (int)$role->attribute( 'id' ) ) ) );
        return self::ok( self::exportRole( self::role( (int)$role->attribute( 'id' ) ), true ) );
    }

    protected static function limit( $identifier, $value )
    {
        $identifier = strtolower( (string)$identifier );
        if ( $identifier === '' )
            return array( '', '' );
        if ( !in_array( $identifier, array( 'subtree', 'section' ), true ) )
            throw new expServiceException( "limit must be 'subtree' (node id) or 'section' (section id)", 422 );
        if ( !is_numeric( $value ) )
            throw new expServiceException( 'limit_value must be the node or section id', 422 );
        if ( $identifier === 'subtree' && !eZContentObjectTreeNode::fetch( (int)$value ) )
            throw new expServiceException( "Node $value does not exist", 404 );
        if ( $identifier === 'section' && !eZSection::fetch( (int)$value ) )
            throw new expServiceException( "Section $value does not exist", 404 );
        return array( $identifier, (int)$value );
    }

    public static function assign( array $a = array() )
    {
        self::guard( 'assign' );
        $role = self::role( self::post( 'id', 'int' ) );
        $object = eZContentObject::fetch( self::post( 'object', 'int' ) );
        if ( !$object instanceof eZContentObject )
            throw new expServiceException( 'No such user or group', 404 );
        if ( !eZUser::isUserObject( $object ) && !in_array( $object->attribute( 'class_identifier' ), self::groupClassIdentifiers(), true ) )
            throw new expServiceException( 'Roles are assigned to users and user groups', 422 );
        list( $ident, $value ) = self::limit( self::post( 'limit', 'string', '' ), self::post( 'limit_value', 'string', '' ) );
        $role->assignToUser( (int)$object->attribute( 'id' ), $ident, $value );
        eZUser::cleanupCache();
        return self::ok( array( 'role' => (int)$role->attribute( 'id' ), 'object' => (int)$object->attribute( 'id' ), 'assignments' => self::assignmentRows( $role ) ) );
    }

    public static function unassign( array $a = array() )
    {
        self::guard( 'unassign' );
        $role = self::role( self::post( 'id', 'int' ) );
        $objectId = self::post( 'object', 'int' );
        $assignment = self::post( 'assignment', 'int', 0 );
        $found = false;
        foreach ( self::assignmentRows( $role ) as $row )
            if ( $row['object_id'] === $objectId && ( !$assignment || $row['assignment_id'] === $assignment ) )
                $found = true;
        if ( !$found )
            throw new expServiceException( 'The role is not assigned to this object', 404 );
        if ( $assignment )
            $role->removeUserAssignmentByID( $assignment );
        else
            $role->removeUserAssignment( $objectId );
        eZUser::cleanupCache();
        return self::ok( array( 'role' => (int)$role->attribute( 'id' ), 'object' => $objectId, 'assignments' => self::assignmentRows( $role ) ) );
    }

    public static function assigned( array $a = array() )
    {
        self::guard( 'assigned' );
        $role = self::role( self::arg( $a, 0, 'int' ) );
        $ids = array();
        foreach ( self::assignmentRows( $role ) as $r )
            $ids[$r['object_id']] = $r['name'];
        return self::ok( array( 'role' => (int)$role->attribute( 'id' ), 'objects' => $ids ) );
    }

    public static function usersWith( array $a = array() )
    {
        self::guard( 'usersWith' );
        $role = self::role( self::arg( $a, 0, 'int' ) );
        $out = array();
        foreach ( (array)$role->fetchUserByRole() as $u )
        {
            $o = eZContentObject::fetch( (int)( is_array( $u ) ? $u['user_object']->attribute( 'id' ) : $u->attribute( 'id' ) ) );
            if ( $o )
                $out[] = array( 'id' => (int)$o->attribute( 'id' ), 'name' => $o->attribute( 'name' ), 'class_identifier' => $o->attribute( 'class_identifier' ) );
        }
        return self::pageOf( $out, $a, 1, 2 );
    }
}
