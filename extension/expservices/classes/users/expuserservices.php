<?php
/**
 * expuser: look up, list, search and administer users.
 * ezjscore/call/expuser::<method>[::arg...]. Users are addressed by their content object id; private data
 * (login, e-mail, account state) is only returned for oneself or for users the caller may edit; no password
 * hash, token or account key is ever returned.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expUserServices extends expUsersBase
{
    /**
     * The services (expServiceBase), declared here rather than assigned after the class: a persistent worker
     * (Velocity) puts the statics of a class loaded during a request back to their declared value before the
     * next one, and an assignment after the class would not run again.
     */
    public static $services = array(
        'current' => array( 'summary' => 'The current user: id, name, groups, and the private data of oneself', 'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'user' ),
        'fetch' => array( 'summary' => 'A user by content object id; login, e-mail and state only for oneself or users the caller may edit', 'access' => 'user', 'write' => false, 'args' => array( 'id' => 'int' ), 'returns' => 'user' ),
        'byLogin' => array( 'summary' => 'A user by login name', 'access' => 'user', 'write' => false, 'args' => array( 'login' => 'string' ), 'returns' => 'user' ),
        'byEmail' => array( 'summary' => 'A user by e-mail address, only when the caller may see that user\'s private data', 'access' => 'user', 'write' => false, 'args' => array( 'email' => 'string' ), 'returns' => 'user' ),
        'exists' => array( 'summary' => 'Whether a login name is taken', 'access' => 'public', 'write' => false, 'args' => array( 'login' => 'string' ), 'returns' => 'exists' ),
        'search' => array( 'summary' => 'Search users by login or name (2 characters or more)', 'access' => 'user', 'write' => false, 'args' => array( 'query' => 'string', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged users' ),
        'listAll' => array( 'summary' => 'All users, paged', 'access' => 'user', 'write' => false, 'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged users' ),
        'count' => array( 'summary' => 'The number of users', 'access' => 'user', 'write' => false, 'args' => array(), 'returns' => 'count' ),
        'listByGroup' => array( 'summary' => 'The users directly in a user group (group node id), paged', 'access' => 'user', 'write' => false, 'args' => array( 'group' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged users' ),
        'loggedIn' => array( 'summary' => 'The users with an active session', 'access' => array( 'setup', 'administrate' ), 'write' => false, 'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged users' ),
        'loggedInCount' => array( 'summary' => 'Logged in registered and anonymous sessions', 'access' => array( 'setup', 'administrate' ), 'write' => false, 'args' => array(), 'returns' => 'registered, anonymous' ),
        'isOnline' => array( 'summary' => 'Whether a user has an active session', 'access' => 'user', 'write' => false, 'args' => array( 'id' => 'int' ), 'returns' => 'online' ),
        'profile' => array( 'summary' => 'The content attributes of a user (not the account), oneself when no id', 'access' => 'user', 'write' => false, 'args' => array( 'id' => 'int' ), 'returns' => 'fields' ),
        'updateProfile' => array( 'summary' => 'Update profile attributes of a user the caller may edit (POST id, fields JSON)', 'access' => 'user', 'write' => true, 'args' => array( 'id' => 'int', 'fields' => 'json' ), 'returns' => 'updated' ),
        'create' => array( 'summary' => 'Create a user in a group (POST group, login, email, password, fields, enabled, class)', 'access' => 'user', 'write' => true, 'args' => array( 'group' => 'int', 'login' => 'string', 'email' => 'string', 'password' => 'string', 'fields' => 'json', 'enabled' => 'bool', 'class' => 'string' ), 'returns' => 'user' ),
        'remove' => array( 'summary' => 'Move a user to the trash (POST id)', 'access' => 'user', 'write' => true, 'args' => array( 'id' => 'int' ), 'returns' => 'removed nodes' ),
        'enable' => array( 'summary' => 'Enable an account (POST id)', 'access' => 'user', 'write' => true, 'args' => array( 'id' => 'int' ), 'returns' => 'enabled' ),
        'disable' => array( 'summary' => 'Disable an account and end its sessions (POST id)', 'access' => 'user', 'write' => true, 'args' => array( 'id' => 'int' ), 'returns' => 'enabled' ),
        'unlock' => array( 'summary' => 'Reset the failed login counter (POST id)', 'access' => 'user', 'write' => true, 'args' => array( 'id' => 'int' ), 'returns' => 'failed_login_attempts' ),
        'setPassword' => array( 'summary' => 'Set another user\'s password, ends their sessions (POST id, password)', 'access' => 'user', 'write' => true, 'args' => array( 'id' => 'int', 'password' => 'string' ), 'returns' => 'changed' ),
        'changeEmail' => array( 'summary' => 'Change the e-mail address of a user the caller may edit (POST id, email)', 'access' => 'user', 'write' => true, 'args' => array( 'id' => 'int', 'email' => 'string' ), 'returns' => 'email' ),
        'changeLogin' => array( 'summary' => 'Change the login name of a user the caller may edit (POST id, login)', 'access' => 'user', 'write' => true, 'args' => array( 'id' => 'int', 'login' => 'string' ), 'returns' => 'login' ),
        'loginInfo' => array( 'summary' => 'Login count, last visit, failed attempts, lock and online state', 'access' => 'user', 'write' => false, 'args' => array( 'id' => 'int' ), 'returns' => 'login info' ),
        'lastVisit' => array( 'summary' => 'The last visit of a user', 'access' => 'user', 'write' => false, 'args' => array( 'id' => 'int' ), 'returns' => 'last_visit' ),
        'settings' => array( 'summary' => 'Account settings: enabled and the maximum number of logins', 'access' => 'user', 'write' => false, 'args' => array( 'id' => 'int' ), 'returns' => 'is_enabled, max_login' ),
        'setMaxLogin' => array( 'summary' => 'Change the maximum number of simultaneous logins (POST id, max_login)', 'access' => 'user', 'write' => true, 'args' => array( 'id' => 'int', 'max_login' => 'int' ), 'returns' => 'max_login' ),
        'validateLogin' => array( 'summary' => 'Check a login name against the validation rules and whether it is taken', 'access' => 'public', 'write' => false, 'args' => array( 'login' => 'string' ), 'returns' => 'valid, error, taken' ),
        'validatePassword' => array( 'summary' => 'Check a password against the length rule', 'access' => 'public', 'write' => false, 'args' => array( 'password' => 'string' ), 'returns' => 'valid' ),
        'passwordPolicy' => array( 'summary' => 'The password and e-mail rules of the site', 'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'min_length, generated_length, unique_email' ),
        'generatePassword' => array( 'summary' => 'A generated password', 'access' => 'user', 'write' => false, 'args' => array( 'length' => 'int' ), 'returns' => 'password' ),
        'rolesOf' => array( 'summary' => 'The roles a user has, directly and through groups', 'access' => array( 'role', 'list' ), 'write' => false, 'args' => array( 'id' => 'int' ), 'returns' => 'roles' ),
        'groupsOf' => array( 'summary' => 'The groups a user is a member of', 'access' => 'user', 'write' => false, 'args' => array( 'id' => 'int' ), 'returns' => 'groups' ),
        'effectivePolicies' => array( 'summary' => 'The effective module/function access of a user, paged', 'access' => array( 'role', 'list' ), 'write' => false, 'args' => array( 'id' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'module, function, unlimited, policies' ),
        'limitations' => array( 'summary' => 'The limited role assignments of a user', 'access' => array( 'role', 'list' ), 'write' => false, 'args' => array( 'id' => 'int' ), 'returns' => 'assignments, values' ),
        'hasAccess' => array( 'summary' => 'Whether the current user has access to a module and function', 'access' => 'public', 'write' => false, 'args' => array( 'module' => 'string', 'function' => 'string' ), 'returns' => 'access, allowed' ),
        'hasAccessOf' => array( 'summary' => 'Whether a given user has access to a module and function', 'access' => array( 'role', 'list' ), 'write' => false, 'args' => array( 'id' => 'int', 'module' => 'string', 'function' => 'string' ), 'returns' => 'access, allowed' ),
        'canNode' => array( 'summary' => 'The current user\'s rights on a node: read, edit, remove, create, move, hide, translate', 'access' => 'public', 'write' => false, 'args' => array( 'node' => 'int' ), 'returns' => 'rights' ),
        'accessSummary' => array( 'summary' => 'The modules and functions the current user has any access to', 'access' => 'user', 'write' => false, 'args' => array(), 'returns' => 'module => functions' ),
        'canLoginTo' => array( 'summary' => 'Whether a user may log in to a siteaccess', 'access' => array( 'role', 'list' ), 'write' => false, 'args' => array( 'id' => 'int', 'siteaccess' => 'string' ), 'returns' => 'allowed' ),
        'hashTypes' => array( 'summary' => 'The password hash types and the default', 'access' => 'user', 'write' => false, 'args' => array(), 'returns' => 'types, default' ),
        'userClasses' => array( 'summary' => 'The content class identifiers of users and of user groups', 'access' => 'user', 'write' => false, 'args' => array(), 'returns' => 'user, group' ),
        'anonymous' => array( 'summary' => 'The id of the anonymous user', 'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'id' ),
    );

    public static function current( array $a = array() )
    {
        self::guard( 'current' );
        $user = eZUser::currentUser();
        return self::ok( self::exportUser( $user, true ) + array( 'is_registered' => (bool)$user->isRegistered() ) );
    }

    public static function fetch( array $a = array() )
    {
        self::guard( 'fetch' );
        return self::ok( self::exportUser( self::user( self::arg( $a, 0, 'int' ) ), true ) );
    }

    public static function byLogin( array $a = array() )
    {
        self::guard( 'byLogin' );
        $user = eZUser::fetchByName( self::arg( $a, 0, 'string' ) );
        if ( !$user instanceof eZUser )
            throw new expServiceException( 'No such user', 404 );
        return self::ok( self::exportUser( $user, true ) );
    }

    public static function byEmail( array $a = array() )
    {
        self::guard( 'byEmail' );
        $user = eZUser::fetchByEmail( self::arg( $a, 0, 'string' ) );
        if ( !$user instanceof eZUser || !self::sensitive( $user->attribute( 'contentobject_id' ) ) )
            throw new expServiceException( 'No such user', 404 );
        return self::ok( self::exportUser( $user, true ) );
    }

    public static function exists( array $a = array() )
    {
        self::guard( 'exists' );
        return self::ok( array( 'exists' => eZUser::fetchByName( self::arg( $a, 0, 'string' ) ) instanceof eZUser ) );
    }

    public static function search( array $a = array() )
    {
        self::guard( 'search' );
        $q = trim( self::arg( $a, 0, 'string' ) );
        if ( strlen( $q ) < 2 )
            throw new expServiceException( 'The search text needs 2 characters or more', 400 );
        list( $limit, $offset ) = self::paging( $a, 1, 2 );
        $db = eZDB::instance();
        $like = "'%" . $db->escapeString( $q ) . "%'";
        $from = "FROM ezuser u INNER JOIN ezcontentobject o ON o.id = u.contentobject_id WHERE ( u.login LIKE $like OR o.name LIKE $like )";
        $total = (int)self::firstValue( $db->arrayQuery( "SELECT COUNT(*) AS c $from" ), 'c' );
        $rows = $db->arrayQuery( "SELECT u.contentobject_id AS id $from ORDER BY u.contentobject_id", array( 'limit' => $limit, 'offset' => $offset ) );
        return self::page( self::exportRows( $rows ), $total, $offset, $limit );
    }

    public static function listAll( array $a = array() )
    {
        self::guard( 'listAll' );
        list( $limit, $offset ) = self::paging( $a, 0, 1 );
        $db = eZDB::instance();
        $total = (int)self::firstValue( $db->arrayQuery( 'SELECT COUNT(*) AS c FROM ezuser' ), 'c' );
        $rows = $db->arrayQuery( 'SELECT contentobject_id AS id FROM ezuser ORDER BY contentobject_id', array( 'limit' => $limit, 'offset' => $offset ) );
        return self::page( self::exportRows( $rows ), $total, $offset, $limit );
    }

    public static function count( array $a = array() )
    {
        self::guard( 'count' );
        return self::ok( array( 'count' => (int)self::firstValue( eZDB::instance()->arrayQuery( 'SELECT COUNT(*) AS c FROM ezuser' ), 'c' ) ) );
    }

    public static function listByGroup( array $a = array() )
    {
        self::guard( 'listByGroup' );
        $group = self::groupNode( self::arg( $a, 0, 'int' ) );
        list( $limit, $offset ) = self::paging( $a, 1, 2 );
        $params = array( 'ClassFilterType' => 'include', 'ClassFilterArray' => eZUser::fetchUserClassNames(),
                         'Depth' => 1, 'DepthOperator' => 'eq' );
        $total = (int)eZContentObjectTreeNode::subTreeCountByNodeID( $params, $group->attribute( 'node_id' ) );
        $nodes = eZContentObjectTreeNode::subTreeByNodeID( $params + array( 'Limit' => $limit, 'Offset' => $offset,
                                                           'SortBy' => array( array( 'name', true ) ) ), $group->attribute( 'node_id' ) );
        $items = array();
        foreach ( (array)$nodes as $node )
        {
            $user = eZUser::fetch( (int)$node->attribute( 'contentobject_id' ) );
            if ( $user instanceof eZUser )
                $items[] = self::exportUser( $user );
        }
        return self::page( $items, $total, $offset, $limit );
    }

    public static function loggedIn( array $a = array() )
    {
        self::guard( 'loggedIn' );
        list( $limit, $offset ) = self::paging( $a, 0, 1 );
        $users = eZUser::fetchLoggedInList( true, $offset, $limit );
        $items = array();
        foreach ( (array)$users as $u )
            $items[] = self::exportUser( $u instanceof eZUser ? $u : eZUser::fetch( (int)$u['contentobject_id'] ) );
        return self::page( $items, eZUser::fetchLoggedInCount(), $offset, $limit );
    }

    public static function loggedInCount( array $a = array() )
    {
        self::guard( 'loggedInCount' );
        return self::ok( array( 'registered' => (int)eZUser::fetchLoggedInCount(), 'anonymous' => (int)eZUser::fetchAnonymousCount() ) );
    }

    public static function isOnline( array $a = array() )
    {
        self::guard( 'isOnline' );
        $id = self::arg( $a, 0, 'int' );
        self::user( $id );
        return self::ok( array( 'id' => $id, 'online' => (bool)eZUser::isUserLoggedIn( $id ) ) );
    }

    public static function profile( array $a = array() )
    {
        self::guard( 'profile' );
        $id = self::arg( $a, 0, 'int', 0 );
        if ( $id === 0 )
            $id = (int)eZUser::currentUserID();
        $object = self::userObject( $id );
        if ( !$object->canRead() )
            throw new expServiceException( "No read access to user $id", 403 );
        $fields = array();
        foreach ( $object->dataMap() as $identifier => $attribute )
        {
            if ( $attribute->attribute( 'data_type_string' ) === 'ezuser' )
                continue;
            $fields[$identifier] = array( 'type' => $attribute->attribute( 'data_type_string' ), 'value' => (string)$attribute->toString() );
        }
        return self::ok( array( 'id' => $id, 'name' => $object->attribute( 'name' ), 'fields' => (object)$fields ) );
    }

    public static function updateProfile( array $a = array() )
    {
        self::guard( 'updateProfile' );
        $id = self::post( 'id', 'int' );
        $object = self::requireEdit( $id );
        $fields = self::post( 'fields', 'json' );
        if ( !is_array( $fields ) || !$fields )
            throw new expServiceException( 'fields must be a JSON object of attribute identifier => value', 400 );
        $map = $object->dataMap();
        foreach ( $fields as $identifier => $value )
        {
            if ( !isset( $map[$identifier] ) )
                throw new expServiceException( "Unknown attribute '$identifier'", 422 );
            if ( $map[$identifier]->attribute( 'data_type_string' ) === 'ezuser' )
                throw new expServiceException( 'The account attribute is changed with the account services', 422 );
            if ( !is_scalar( $value ) )
                throw new expServiceException( "Attribute '$identifier' needs a text value", 422 );
        }
        if ( !eZContentFunctions::updateAndPublishObject( $object, array( 'attributes' => $fields ) ) )
            throw new expServiceException( 'The profile could not be updated', 422 );
        return self::ok( array( 'id' => $id, 'updated' => array_keys( $fields ) ) );
    }

    public static function create( array $a = array() )
    {
        self::guard( 'create' );
        $group = self::groupNode( self::post( 'group', 'int' ), 'read' );
        $login = trim( self::post( 'login', 'string' ) );
        $email = trim( self::post( 'email', 'string' ) );
        $password = self::post( 'password', 'string' );
        $fields = self::post( 'fields', 'json', array() );
        $enabled = self::post( 'enabled', 'bool', true );
        $classIdentifier = self::post( 'class', 'string', 'user' );
        $class = eZContentClass::fetchByIdentifier( $classIdentifier );
        if ( !$class instanceof eZContentClass || !in_array( $classIdentifier, eZUser::fetchUserClassNames(), true ) )
            throw new expServiceException( "'$classIdentifier' is not a user class", 422 );
        if ( !$group->checkAccess( 'create', $class->attribute( 'id' ), $group->object()->attribute( 'contentclass_id' ) ) )
            throw new expServiceException( 'No access to create users in this group', 403 );
        self::validateNewAccount( $login, $email, $password );
        $type = eZUser::hashType();
        $hash = eZUser::createHash( $login, $password, eZUser::site(), $type );
        $account = $login . '|' . $email . '|' . $hash . '|' . eZUser::passwordHashTypeName( $type ) . '|' . ( $enabled ? 1 : 0 );
        $attributes = array( 'user_account' => $account );
        foreach ( (array)$fields as $k => $v )
            if ( $k !== 'user_account' && is_scalar( $v ) )
                $attributes[$k] = (string)$v;
        $object = eZContentFunctions::createAndPublishObject( array( 'parent_node_id' => $group->attribute( 'node_id' ),
                                                                      'class_identifier' => $classIdentifier,
                                                                      'creator_id' => (int)eZUser::currentUserID(),
                                                                      'attributes' => $attributes ) );
        if ( !$object instanceof eZContentObject )
            throw new expServiceException( 'The user could not be created', 422 );
        $user = eZUser::fetch( $object->attribute( 'id' ) );
        if ( !$user instanceof eZUser )
            throw new expServiceException( 'The user object was created without an account', 422 );
        return self::ok( self::exportUser( $user, true ) );
    }

    public static function remove( array $a = array() )
    {
        self::guard( 'remove' );
        $id = self::post( 'id', 'int' );
        $object = self::userObject( $id );
        if ( self::isSelf( $id ) )
            throw new expServiceException( 'You cannot remove yourself', 409 );
        if ( $id === (int)eZUser::anonymousId() )
            throw new expServiceException( 'The anonymous user cannot be removed', 409 );
        $nodes = array();
        foreach ( $object->assignedNodes() as $node )
        {
            if ( !$node->canRemove() )
                throw new expServiceException( "No remove access to node " . $node->attribute( 'node_id' ), 403 );
            $nodes[] = (int)$node->attribute( 'node_id' );
        }
        if ( !$nodes )
            throw new expServiceException( 'The user has no location', 409 );
        eZContentObjectTreeNode::removeSubtrees( $nodes, true );
        return self::ok( array( 'id' => $id, 'removed_nodes' => $nodes, 'to_trash' => true ) );
    }

    public static function enable( array $a = array() )
    {
        self::guard( 'enable' );
        return self::setEnabled( self::post( 'id', 'int' ), true );
    }

    public static function disable( array $a = array() )
    {
        self::guard( 'disable' );
        $id = self::post( 'id', 'int' );
        if ( self::isSelf( $id ) )
            throw new expServiceException( 'You cannot disable yourself', 409 );
        if ( $id === (int)eZUser::anonymousId() )
            throw new expServiceException( 'The anonymous user cannot be disabled', 409 );
        return self::setEnabled( $id, false );
    }

    protected static function setEnabled( $id, $enabled )
    {
        self::requireEdit( $id );
        $setting = eZUserSetting::fetch( $id );
        $max = $setting instanceof eZUserSetting ? (int)$setting->attribute( 'max_login' ) : 0;
        $r = eZUserOperationCollection::setSettings( $id, $enabled ? 1 : 0, $max );
        if ( empty( $r['status'] ) )
            throw new expServiceException( 'The account state could not be changed', 422 );
        return self::ok( array( 'id' => $id, 'enabled' => $enabled ) );
    }

    public static function unlock( array $a = array() )
    {
        self::guard( 'unlock' );
        $id = self::post( 'id', 'int' );
        self::requireEdit( $id );
        eZUser::setFailedLoginAttempts( $id, 0, true );
        return self::ok( array( 'id' => $id, 'failed_login_attempts' => 0 ) );
    }

    public static function setPassword( array $a = array() )
    {
        self::guard( 'setPassword' );
        $id = self::post( 'id', 'int' );
        self::requireEdit( $id );
        $pw = self::post( 'password', 'string' );
        if ( !eZUser::validatePassword( $pw ) )
            throw new expServiceException( 'The password is too short', 422 );
        $r = eZUserOperationCollection::password( $id, $pw );
        if ( empty( $r['status'] ) )
            throw new expServiceException( 'The password could not be changed', 422 );
        eZUser::removeSessionData( $id );
        return self::ok( array( 'id' => $id, 'changed' => true ) );
    }

    public static function changeEmail( array $a = array() )
    {
        self::guard( 'changeEmail' );
        $id = self::post( 'id', 'int' );
        self::requireEdit( $id );
        $email = trim( self::post( 'email', 'string' ) );
        $user = self::user( $id );
        self::validateNewAccount( $user->attribute( 'login' ), $email, null, $id );
        // a new address takes effect once it is confirmed from the new mailbox (mailpreferences.ini [EmailChangeSettings])
        $change = class_exists( 'expMailAddressChange' ) ? expMailAddressChange::request( $user, $email ) : 'changed';
        if ( $change !== 'changed' )
            return self::ok( array( 'id' => $id, 'email' => $user->attribute( 'email' ), 'pending' => $change === 'pending_confirmation' ) );
        $user->setAttribute( 'email', $email );
        $user->store();
        return self::ok( array( 'id' => $id, 'email' => $email ) );
    }

    public static function changeLogin( array $a = array() )
    {
        self::guard( 'changeLogin' );
        $id = self::post( 'id', 'int' );
        self::requireEdit( $id );
        $login = trim( self::post( 'login', 'string' ) );
        $user = self::user( $id );
        self::validateNewAccount( $login, $user->attribute( 'email' ), null, $id );
        $user->setAttribute( 'login', $login );
        $user->store();
        return self::ok( array( 'id' => $id, 'login' => $login ) );
    }

    public static function loginInfo( array $a = array() )
    {
        self::guard( 'loginInfo' );
        $id = self::arg( $a, 0, 'int' );
        $user = self::user( $id );
        if ( !self::sensitive( $id ) )
            throw new expServiceException( "No access to the account data of user $id", 403 );
        $setting = eZUserSetting::fetch( $id );
        return self::ok( array( 'id' => $id, 'login_count' => (int)$user->loginCount(), 'last_visit' => self::iso( $user->lastVisit() ),
                                'failed_login_attempts' => (int)$user->failedLoginAttempts(), 'is_locked' => (bool)$user->isLocked(),
                                'is_enabled' => (bool)$user->isEnabled(), 'max_login' => $setting ? (int)$setting->attribute( 'max_login' ) : 0,
                                'online' => (bool)eZUser::isUserLoggedIn( $id ) ) );
    }

    public static function lastVisit( array $a = array() )
    {
        self::guard( 'lastVisit' );
        $id = self::arg( $a, 0, 'int' );
        $user = self::user( $id );
        if ( !self::sensitive( $id ) )
            throw new expServiceException( "No access to the account data of user $id", 403 );
        return self::ok( array( 'id' => $id, 'last_visit' => self::iso( $user->lastVisit() ) ) );
    }

    public static function settings( array $a = array() )
    {
        self::guard( 'settings' );
        $id = self::arg( $a, 0, 'int' );
        self::user( $id );
        if ( !self::sensitive( $id ) )
            throw new expServiceException( "No access to the account data of user $id", 403 );
        $setting = eZUserSetting::fetch( $id );
        return self::ok( array( 'id' => $id, 'is_enabled' => $setting ? (bool)$setting->attribute( 'is_enabled' ) : true,
                                'max_login' => $setting ? (int)$setting->attribute( 'max_login' ) : 0 ) );
    }

    public static function setMaxLogin( array $a = array() )
    {
        self::guard( 'setMaxLogin' );
        $id = self::post( 'id', 'int' );
        self::requireEdit( $id );
        $max = self::post( 'max_login', 'int' );
        if ( $max < 0 )
            throw new expServiceException( 'max_login must be 0 or more', 422 );
        $user = self::user( $id );
        $r = eZUserOperationCollection::setSettings( $id, $user->isEnabled() ? 1 : 0, $max );
        if ( empty( $r['status'] ) )
            throw new expServiceException( 'The setting could not be changed', 422 );
        return self::ok( array( 'id' => $id, 'max_login' => $max ) );
    }

    public static function validateLogin( array $a = array() )
    {
        self::guard( 'validateLogin' );
        $login = self::arg( $a, 0, 'string' );
        $error = '';
        $valid = eZUser::validateLoginName( $login, $error );
        return self::ok( array( 'valid' => (bool)$valid, 'error' => (string)$error, 'taken' => eZUser::fetchByName( $login ) instanceof eZUser ) );
    }

    public static function validatePassword( array $a = array() )
    {
        self::guard( 'validatePassword' );
        return self::ok( array( 'valid' => (bool)eZUser::validatePassword( self::arg( $a, 0, 'string' ) ) ) );
    }

    public static function passwordPolicy( array $a = array() )
    {
        self::guard( 'passwordPolicy' );
        $ini = eZINI::instance();
        return self::ok( array( 'min_length' => (int)$ini->variable( 'UserSettings', 'MinPasswordLength' ),
                                'generated_length' => (int)$ini->variable( 'UserSettings', 'GeneratePasswordLength' ),
                                'unique_email' => (bool)eZUser::requireUniqueEmail() ) );
    }

    public static function generatePassword( array $a = array() )
    {
        self::guard( 'generatePassword' );
        $length = self::arg( $a, 0, 'int', (int)eZINI::instance()->variable( 'UserSettings', 'GeneratePasswordLength' ) );
        if ( $length < 4 || $length > 64 )
            throw new expServiceException( 'length must be between 4 and 64', 400 );
        return self::ok( array( 'password' => eZUser::createPassword( $length ) ) );
    }

    public static function rolesOf( array $a = array() )
    {
        self::guard( 'rolesOf' );
        $user = self::user( self::arg( $a, 0, 'int' ) );
        $out = array();
        foreach ( (array)$user->roles() as $role )
            $out[] = self::exportRole( $role );
        return self::ok( $out );
    }

    public static function groupsOf( array $a = array() )
    {
        self::guard( 'groupsOf' );
        $user = self::user( self::arg( $a, 0, 'int' ) );
        $out = array();
        foreach ( $user->groups( true ) as $g )
        {
            $node = $g->mainNode();
            if ( $node instanceof eZContentObjectTreeNode )
                $out[] = self::exportGroup( $node );
        }
        return self::ok( $out );
    }

    public static function effectivePolicies( array $a = array() )
    {
        self::guard( 'effectivePolicies' );
        $user = self::user( self::arg( $a, 0, 'int' ) );
        $access = $user->accessArray();
        $out = array();
        foreach ( $access as $module => $functions )
            foreach ( $functions as $function => $policies )
                $out[] = array( 'module' => $module, 'function' => $function,
                                'unlimited' => isset( $policies['*'] ) && ( $policies['*'] === '*' || in_array( '*', (array)$policies['*'] ) ),
                                'policies' => is_array( $policies ) ? count( $policies ) : 1 );
        return self::pageOf( $out, $a, 1, 2 );
    }

    public static function limitations( array $a = array() )
    {
        self::guard( 'limitations' );
        $user = self::user( self::arg( $a, 0, 'int' ) );
        return self::ok( array( 'assignments' => $user->limitList(), 'values' => array_values( (array)$user->limitValueList() ) ) );
    }

    public static function hasAccess( array $a = array() )
    {
        self::guard( 'hasAccess' );
        $module = self::arg( $a, 0, 'string' );
        $function = self::arg( $a, 1, 'string', false );
        $r = eZUser::currentUser()->hasAccessTo( $module, $function );
        return self::ok( array( 'module' => $module, 'function' => $function ?: null, 'access' => $r['accessWord'],
                                'allowed' => $r['accessWord'] !== 'no' ) );
    }

    public static function hasAccessOf( array $a = array() )
    {
        self::guard( 'hasAccessOf' );
        $user = self::user( self::arg( $a, 0, 'int' ) );
        $module = self::arg( $a, 1, 'string' );
        $function = self::arg( $a, 2, 'string', false );
        $r = $user->hasAccessTo( $module, $function );
        return self::ok( array( 'id' => (int)$user->attribute( 'contentobject_id' ), 'module' => $module,
                                'function' => $function ?: null, 'access' => $r['accessWord'], 'allowed' => $r['accessWord'] !== 'no' ) );
    }

    public static function canNode( array $a = array() )
    {
        self::guard( 'canNode' );
        $node = eZContentObjectTreeNode::fetch( self::arg( $a, 0, 'int' ) );
        if ( !$node instanceof eZContentObjectTreeNode )
            throw new expServiceException( 'No such node', 404 );
        $out = array( 'node_id' => (int)$node->attribute( 'node_id' ) );
        foreach ( array( 'read', 'edit', 'remove', 'create', 'move', 'hide', 'translate', 'manageLocations' ) as $f )
        {
            $m = 'can' . ucfirst( $f );
            if ( method_exists( $node, $m ) )
                $out[$f] = (bool)$node->$m();
        }
        return self::ok( $out );
    }

    public static function accessSummary( array $a = array() )
    {
        self::guard( 'accessSummary' );
        $out = array();
        foreach ( eZUser::currentUser()->accessArray() as $module => $functions )
            $out[$module] = array_keys( $functions );
        return self::ok( (object)$out );
    }

    public static function canLoginTo( array $a = array() )
    {
        self::guard( 'canLoginTo' );
        $user = self::user( self::arg( $a, 0, 'int' ) );
        $sa = self::arg( $a, 1, 'string' );
        return self::ok( array( 'siteaccess' => $sa, 'allowed' => (bool)$user->canLoginToSiteAccess( $sa ) ) );
    }

    public static function hashTypes( array $a = array() )
    {
        self::guard( 'hashTypes' );
        $out = array();
        foreach ( array( 1, 2, 3, 4, 5, 6, 7 ) as $id )
        {
            $name = eZUser::passwordHashTypeName( $id );
            if ( $name )
                $out[] = array( 'id' => $id, 'name' => $name );
        }
        return self::ok( array( 'types' => $out, 'default' => eZUser::passwordHashTypeName( eZUser::hashType() ) ) );
    }

    public static function userClasses( array $a = array() )
    {
        self::guard( 'userClasses' );
        return self::ok( array( 'user' => eZUser::fetchUserClassNames(), 'group' => array_values( eZUser::fetchUserGroupClassNames() ) ) );
    }

    public static function anonymous( array $a = array() )
    {
        self::guard( 'anonymous' );
        return self::ok( array( 'id' => (int)eZUser::anonymousId() ) );
    }

    // ------------------------------------------------------------------ helpers

    protected static function exportRows( array $rows )
    {
        $items = array();
        foreach ( $rows as $row )
        {
            $user = eZUser::fetch( (int)$row['id'] );
            if ( $user instanceof eZUser )
                $items[] = self::exportUser( $user );
        }
        return $items;
    }

    protected static function firstValue( $rows, $key )
    {
        return isset( $rows[0][$key] ) ? $rows[0][$key] : 0;
    }
}
