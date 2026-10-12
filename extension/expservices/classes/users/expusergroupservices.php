<?php
/**
 * expusergroup: the user group tree and its members. Groups are addressed by node id, users by object id.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expUserGroupServices extends expUsersBase
{
    /**
     * The services (expServiceBase), declared here rather than assigned after the class: a persistent worker
     * (Velocity) puts the statics of a class loaded during a request back to their declared value before the
     * next one, and an assignment after the class would not run again.
     */
    public static $services = array(
        'root' => array( 'summary' => 'The users root node, the default placement and the group classes', 'access' => 'user', 'write' => false, 'args' => array(), 'returns' => 'node_id' ),
        'tree' => array( 'summary' => 'The group tree below a node, to a depth (default the users root, depth 3)', 'access' => 'user', 'write' => false, 'args' => array( 'group' => 'int', 'depth' => 'int' ), 'returns' => 'nested groups' ),
        'fetch' => array( 'summary' => 'A user group by node id', 'access' => 'user', 'write' => false, 'args' => array( 'group' => 'int' ), 'returns' => 'group' ),
        'children' => array( 'summary' => 'The subgroups of a group, paged', 'access' => 'user', 'write' => false, 'args' => array( 'group' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged groups' ),
        'subgroupCount' => array( 'summary' => 'The number of subgroups', 'access' => 'user', 'write' => false, 'args' => array( 'group' => 'int' ), 'returns' => 'count' ),
        'path' => array( 'summary' => 'The path from the root to a group', 'access' => 'user', 'write' => false, 'args' => array( 'group' => 'int' ), 'returns' => 'nodes' ),
        'parent' => array( 'summary' => 'The parent group', 'access' => 'user', 'write' => false, 'args' => array( 'group' => 'int' ), 'returns' => 'group' ),
        'search' => array( 'summary' => 'Search groups by name', 'access' => 'user', 'write' => false, 'args' => array( 'query' => 'string', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged groups' ),
        'members' => array( 'summary' => 'The users directly in a group, paged', 'access' => 'user', 'write' => false, 'args' => array( 'group' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged users' ),
        'memberCount' => array( 'summary' => 'The number of users directly in a group', 'access' => 'user', 'write' => false, 'args' => array( 'group' => 'int' ), 'returns' => 'count' ),
        'isMember' => array( 'summary' => 'Whether a user is directly in a group', 'access' => 'user', 'write' => false, 'args' => array( 'user' => 'int', 'group' => 'int' ), 'returns' => 'member' ),
        'ofUser' => array( 'summary' => 'The groups of a user', 'access' => 'user', 'write' => false, 'args' => array( 'id' => 'int' ), 'returns' => 'groups' ),
        'rolesOf' => array( 'summary' => 'The roles assigned to a group', 'access' => array( 'role', 'list' ), 'write' => false, 'args' => array( 'group' => 'int' ), 'returns' => 'roles' ),
        'create' => array( 'summary' => 'Create a group (POST parent, name, class, fields)', 'access' => 'user', 'write' => true, 'args' => array( 'parent' => 'int', 'name' => 'string', 'class' => 'string', 'fields' => 'json' ), 'returns' => 'group' ),
        'rename' => array( 'summary' => 'Rename a group (POST group, name)', 'access' => 'user', 'write' => true, 'args' => array( 'group' => 'int', 'name' => 'string' ), 'returns' => 'group' ),
        'remove' => array( 'summary' => 'Move a group to the trash (POST group, force for a group with children)', 'access' => 'user', 'write' => true, 'args' => array( 'group' => 'int', 'force' => 'bool' ), 'returns' => 'removed' ),
        'addMember' => array( 'summary' => 'Add a user to a group, a new location (POST group, user)', 'access' => 'user', 'write' => true, 'args' => array( 'group' => 'int', 'user' => 'int' ), 'returns' => 'member' ),
        'removeMember' => array( 'summary' => 'Remove a user from a group, never the last one (POST group, user)', 'access' => 'user', 'write' => true, 'args' => array( 'group' => 'int', 'user' => 'int' ), 'returns' => 'member' ),
        'moveMember' => array( 'summary' => 'Move a user from one group to another (POST user, from, to)', 'access' => 'user', 'write' => true, 'args' => array( 'user' => 'int', 'from' => 'int', 'to' => 'int' ), 'returns' => 'from, to' ),
        'addMembers' => array( 'summary' => 'Add up to 100 users to a group (POST group, users)', 'access' => 'user', 'write' => true, 'args' => array( 'group' => 'int', 'users' => 'list' ), 'returns' => 'added, skipped' ),
    );

    protected static function rootNodeId()
    {
        return (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'UserRootNode' );
    }

    public static function root( array $a = array() )
    {
        self::guard( 'root' );
        return self::ok( array( 'node_id' => self::rootNodeId(), 'default_placement' => (int)eZINI::instance()->variable( 'UserSettings', 'DefaultUserPlacement' ),
                                'group_classes' => self::groupClassIdentifiers() ) );
    }

    public static function tree( array $a = array() )
    {
        self::guard( 'tree' );
        $rootId = self::arg( $a, 0, 'int', self::rootNodeId() );
        $depth = self::arg( $a, 1, 'int', 3 );
        if ( $depth < 1 || $depth > 8 )
            throw new expServiceException( 'depth must be between 1 and 8', 400 );
        $root = self::groupNode( $rootId );
        $count = 0;
        return self::ok( self::branch( $root, $depth, $count ), array( 'groups' => $count ) );
    }

    protected static function branch( eZContentObjectTreeNode $node, $depth, &$count )
    {
        $count++;
        $data = self::exportGroup( $node );
        $data['groups'] = array();
        if ( $depth > 1 )
            foreach ( self::childGroups( $node ) as $child )
                $data['groups'][] = self::branch( $child, $depth - 1, $count );
        return $data;
    }

    protected static function childGroups( eZContentObjectTreeNode $node, $limit = 500, $offset = 0 )
    {
        return (array)eZContentObjectTreeNode::subTreeByNodeID( array( 'ClassFilterType' => 'include', 'ClassFilterArray' => self::groupClassIdentifiers(),
            'Depth' => 1, 'DepthOperator' => 'eq', 'Limit' => $limit, 'Offset' => $offset, 'SortBy' => array( array( 'name', true ) ) ), $node->attribute( 'node_id' ) );
    }

    public static function fetch( array $a = array() )
    {
        self::guard( 'fetch' );
        return self::ok( self::exportGroup( self::groupNode( self::arg( $a, 0, 'int' ) ) ) );
    }

    public static function children( array $a = array() )
    {
        self::guard( 'children' );
        $node = self::groupNode( self::arg( $a, 0, 'int' ) );
        list( $limit, $offset ) = self::paging( $a, 1, 2 );
        $total = (int)eZContentObjectTreeNode::subTreeCountByNodeID( array( 'ClassFilterType' => 'include', 'ClassFilterArray' => self::groupClassIdentifiers(),
            'Depth' => 1, 'DepthOperator' => 'eq' ), $node->attribute( 'node_id' ) );
        return self::page( array_map( array( 'expUsersBase', 'exportGroup' ), self::childGroups( $node, $limit, $offset ) ), $total, $offset, $limit );
    }

    public static function subgroupCount( array $a = array() )
    {
        self::guard( 'subgroupCount' );
        $node = self::groupNode( self::arg( $a, 0, 'int' ) );
        return self::ok( array( 'node_id' => (int)$node->attribute( 'node_id' ), 'count' => (int)eZContentObjectTreeNode::subTreeCountByNodeID(
            array( 'ClassFilterType' => 'include', 'ClassFilterArray' => self::groupClassIdentifiers(), 'Depth' => 1, 'DepthOperator' => 'eq' ), $node->attribute( 'node_id' ) ) ) );
    }

    public static function path( array $a = array() )
    {
        self::guard( 'path' );
        $node = self::groupNode( self::arg( $a, 0, 'int' ) );
        $out = array();
        foreach ( $node->attribute( 'path' ) as $p )
            $out[] = array( 'node_id' => (int)$p->attribute( 'node_id' ), 'name' => $p->attribute( 'name' ) );
        $out[] = array( 'node_id' => (int)$node->attribute( 'node_id' ), 'name' => $node->attribute( 'name' ) );
        return self::ok( $out );
    }

    public static function parent( array $a = array() )
    {
        self::guard( 'parent' );
        $node = self::groupNode( self::arg( $a, 0, 'int' ) );
        return self::ok( self::exportGroup( self::groupNode( $node->attribute( 'parent_node_id' ) ) ) );
    }

    public static function search( array $a = array() )
    {
        self::guard( 'search' );
        $q = trim( self::arg( $a, 0, 'string' ) );
        if ( strlen( $q ) < 2 )
            throw new expServiceException( 'The search text needs 2 characters or more', 400 );
        $out = array();
        foreach ( (array)eZContentObjectTreeNode::subTreeByNodeID( array( 'ClassFilterType' => 'include', 'ClassFilterArray' => self::groupClassIdentifiers(),
                  'AttributeFilter' => array( array( 'name', 'like', '*' . $q . '*' ) ), 'SortBy' => array( array( 'name', true ) ) ), self::rootNodeId() ) as $n )
            $out[] = self::exportGroup( $n );
        return self::pageOf( $out, $a, 1, 2 );
    }

    public static function members( array $a = array() )
    {
        self::guard( 'members' );
        return expUserServices::listByGroup( $a );
    }

    public static function memberCount( array $a = array() )
    {
        self::guard( 'memberCount' );
        $node = self::groupNode( self::arg( $a, 0, 'int' ) );
        return self::ok( array( 'node_id' => (int)$node->attribute( 'node_id' ), 'count' => (int)eZContentObjectTreeNode::subTreeCountByNodeID(
            array( 'ClassFilterType' => 'include', 'ClassFilterArray' => eZUser::fetchUserClassNames(), 'Depth' => 1, 'DepthOperator' => 'eq' ), $node->attribute( 'node_id' ) ) ) );
    }

    public static function isMember( array $a = array() )
    {
        self::guard( 'isMember' );
        $user = self::user( self::arg( $a, 0, 'int' ) );
        $group = self::groupNode( self::arg( $a, 1, 'int' ) );
        $direct = (int)$group->attribute( 'contentobject_id' );
        return self::ok( array( 'member' => in_array( $direct, array_map( 'intval', $user->groups() ), true ) ) );
    }

    public static function ofUser( array $a = array() )
    {
        self::guard( 'ofUser' );
        return expUserServices::groupsOf( $a );
    }

    public static function rolesOf( array $a = array() )
    {
        self::guard( 'rolesOf' );
        $group = self::groupNode( self::arg( $a, 0, 'int' ) );
        $out = array();
        foreach ( (array)eZRole::fetchByUser( array( (int)$group->attribute( 'contentobject_id' ) ) ) as $role )
            $out[] = self::exportRole( $role );
        return self::ok( $out );
    }

    public static function create( array $a = array() )
    {
        self::guard( 'create' );
        $parent = self::groupNode( self::post( 'parent', 'int' ) );
        $name = trim( self::post( 'name', 'string' ) );
        $classIdentifier = self::post( 'class', 'string', 'user_group' );
        $fields = self::post( 'fields', 'json', array() );
        $class = eZContentClass::fetchByIdentifier( $classIdentifier );
        if ( $name === '' )
            throw new expServiceException( 'The group needs a name', 422 );
        if ( !$class instanceof eZContentClass || !in_array( $classIdentifier, self::groupClassIdentifiers(), true ) )
            throw new expServiceException( "'$classIdentifier' is not a user group class", 422 );
        if ( !$parent->checkAccess( 'create', $class->attribute( 'id' ), $parent->object()->attribute( 'contentclass_id' ) ) )
            throw new expServiceException( 'No access to create groups here', 403 );
        $attributes = array( 'name' => $name );
        foreach ( (array)$fields as $k => $v )
            if ( is_scalar( $v ) )
                $attributes[$k] = (string)$v;
        $object = eZContentFunctions::createAndPublishObject( array( 'parent_node_id' => $parent->attribute( 'node_id' ), 'class_identifier' => $classIdentifier,
                                                                      'creator_id' => (int)eZUser::currentUserID(), 'attributes' => $attributes ) );
        if ( !$object instanceof eZContentObject || !$object->mainNode() )
            throw new expServiceException( 'The group could not be created', 422 );
        return self::ok( self::exportGroup( $object->mainNode() ) );
    }

    public static function rename( array $a = array() )
    {
        self::guard( 'rename' );
        $node = self::groupNode( self::post( 'group', 'int' ), 'edit' );
        $name = trim( self::post( 'name', 'string' ) );
        if ( $name === '' )
            throw new expServiceException( 'The group needs a name', 422 );
        if ( !eZContentFunctions::updateAndPublishObject( $node->object(), array( 'attributes' => array( 'name' => $name ) ) ) )
            throw new expServiceException( 'The group could not be renamed', 422 );
        return self::ok( self::exportGroup( eZContentObjectTreeNode::fetch( $node->attribute( 'node_id' ) ) ) );
    }

    public static function remove( array $a = array() )
    {
        self::guard( 'remove' );
        $node = self::groupNode( self::post( 'group', 'int' ), 'remove' );
        $force = self::post( 'force', 'bool', false );
        $nodeId = (int)$node->attribute( 'node_id' );
        if ( $nodeId === self::rootNodeId() )
            throw new expServiceException( 'The users root cannot be removed', 409 );
        $children = (int)$node->childrenCount( false );
        if ( $children > 0 && !$force )
            throw new expServiceException( "The group has $children children; send force=1 to move the whole subtree to the trash", 409 );
        eZContentObjectTreeNode::removeSubtrees( array( $nodeId ), true );
        return self::ok( array( 'node_id' => $nodeId, 'removed_children' => $children, 'to_trash' => true ) );
    }

    /** @return eZContentObjectTreeNode|null the location of a user under a group */
    protected static function memberNode( eZContentObject $user, $groupNodeId )
    {
        foreach ( $user->assignedNodes() as $n )
            if ( (int)$n->attribute( 'parent_node_id' ) === (int)$groupNodeId )
                return $n;
        return null;
    }

    public static function addMember( array $a = array() )
    {
        self::guard( 'addMember' );
        $group = self::groupNode( self::post( 'group', 'int' ) );
        $object = self::userObject( self::post( 'user', 'int' ) );
        if ( self::memberNode( $object, $group->attribute( 'node_id' ) ) )
            throw new expServiceException( 'The user is already a member of this group', 409 );
        $main = $object->mainNode();
        if ( !$main )
            throw new expServiceException( 'The user has no location', 409 );
        eZContentOperationCollection::addAssignment( $main->attribute( 'node_id' ), $object->attribute( 'id' ), array( (int)$group->attribute( 'node_id' ) ) );
        if ( !self::memberNode( eZContentObject::fetch( $object->attribute( 'id' ) ), $group->attribute( 'node_id' ) ) )
            throw new expServiceException( 'No access to add users to this group', 403 );
        self::audit( 'access.group.member.add', array( 'object' => array( 'type' => 'user', 'id' => (int)$object->attribute( 'id' ) ),
                                                      'target' => array( 'type' => 'node', 'id' => (int)$group->attribute( 'node_id' ) ) ) );
        return self::ok( array( 'user' => (int)$object->attribute( 'id' ), 'group' => (int)$group->attribute( 'node_id' ), 'member' => true ) );
    }

    public static function removeMember( array $a = array() )
    {
        self::guard( 'removeMember' );
        $group = self::groupNode( self::post( 'group', 'int' ) );
        $object = self::userObject( self::post( 'user', 'int' ) );
        $node = self::memberNode( $object, $group->attribute( 'node_id' ) );
        if ( !$node )
            throw new expServiceException( 'The user is not a member of this group', 404 );
        if ( count( $object->assignedNodes() ) < 2 )
            throw new expServiceException( 'A user needs at least one group; add another first or remove the user', 409 );
        if ( !$node->canRemove() )
            throw new expServiceException( 'No access to remove this membership', 403 );
        eZContentOperationCollection::removeNodes( array( (int)$node->attribute( 'node_id' ) ) );
        self::audit( 'access.group.member.remove', array( 'object' => array( 'type' => 'user', 'id' => (int)$object->attribute( 'id' ) ),
                                                         'target' => array( 'type' => 'node', 'id' => (int)$group->attribute( 'node_id' ) ) ) );
        return self::ok( array( 'user' => (int)$object->attribute( 'id' ), 'group' => (int)$group->attribute( 'node_id' ), 'member' => false ) );
    }

    public static function moveMember( array $a = array() )
    {
        self::guard( 'moveMember' );
        $from = self::groupNode( self::post( 'from', 'int' ) );
        $to = self::groupNode( self::post( 'to', 'int' ) );
        $object = self::userObject( self::post( 'user', 'int' ) );
        $node = self::memberNode( $object, $from->attribute( 'node_id' ) );
        if ( !$node )
            throw new expServiceException( 'The user is not a member of the source group', 404 );
        if ( self::memberNode( $object, $to->attribute( 'node_id' ) ) )
            throw new expServiceException( 'The user is already a member of the target group', 409 );
        if ( !$node->canMoveFrom() || !$to->canMoveTo( $object->attribute( 'contentclass_id' ) ) )
            throw new expServiceException( 'No access to move this user', 403 );
        $r = eZContentOperationCollection::moveNode( $node->attribute( 'node_id' ), $object->attribute( 'id' ), $to->attribute( 'node_id' ) );
        if ( empty( $r['status'] ) )
            throw new expServiceException( 'The user could not be moved', 422 );
        eZUser::purgeUserCacheByUserId( (int)$object->attribute( 'id' ) );
        return self::ok( array( 'user' => (int)$object->attribute( 'id' ), 'from' => (int)$from->attribute( 'node_id' ), 'to' => (int)$to->attribute( 'node_id' ) ) );
    }

    public static function addMembers( array $a = array() )
    {
        self::guard( 'addMembers' );
        $ids = array_map( 'intval', self::post( 'users', 'list' ) );
        if ( !$ids || count( $ids ) > 100 )
            throw new expServiceException( 'users must list 1 to 100 user ids', 400 );
        $done = array();
        $group = self::post( 'group', 'int' );
        $saved = self::$postData;
        foreach ( $ids as $id )
        {
            self::$postData = array( 'group' => $group, 'user' => $id );
            try { self::addMember(); $done[] = $id; } catch ( expServiceException $e ) { }
        }
        self::$postData = $saved;
        return self::ok( array( 'added' => $done, 'skipped' => count( $ids ) - count( $done ) ) );
    }
}
