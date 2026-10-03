<?php
/**
 * The shared part of the users and access domains of expservices: the compact declaration format and the
 * helpers that encode users and groups without ever leaking password hashes, tokens or other users' e-mail.
 *
 * A declaration is one line: method => array( summary, access, write, args, returns ) where access is
 * 'public', 'user' or 'module/function', write is 'w' (POST + token, audited) or 'r', and args is
 * 'name:type,name:type' (type int|string|bool|json|list, the same as expServiceBase::arg()).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

abstract class expUsersBase extends expServiceBase
{
    /** Expands the compact declarations into the $services format of expServiceBase. */
    public static function specs( array $lines )
    {
        $out = array();
        foreach ( $lines as $method => $l )
        {
            $access = $l[1];
            if ( strpos( $access, '/' ) !== false )
                $access = explode( '/', $access, 2 );
            $args = array();
            if ( isset( $l[3] ) && $l[3] !== '' )
                foreach ( explode( ',', $l[3] ) as $a )
                {
                    $p = explode( ':', $a, 2 );
                    $args[$p[0]] = isset( $p[1] ) ? $p[1] : 'string';
                }
            $out[$method] = array( 'summary' => $l[0], 'access' => $access, 'write' => $l[2] === 'w',
                                   'args' => $args, 'returns' => isset( $l[4] ) ? $l[4] : '' );
        }
        return $out;
    }

    // ------------------------------------------------------------------ users

    /** @return eZUser @throws expServiceException 404 */
    protected static function user( $id )
    {
        $user = $id > 0 ? eZUser::fetch( (int)$id ) : null;
        if ( !$user instanceof eZUser )
            throw new expServiceException( "User $id does not exist", 404 );
        return $user;
    }

    /** @return eZContentObject the content object of a user @throws expServiceException 404 */
    protected static function userObject( $id )
    {
        $object = eZContentObject::fetch( (int)$id );
        if ( !$object instanceof eZContentObject || !eZUser::isUserObject( $object ) )
            throw new expServiceException( "User $id does not exist", 404 );
        return $object;
    }

    protected static function isSelf( $id )
    {
        $current = eZUser::currentUser();
        return $current->isRegistered() && (int)$current->attribute( 'contentobject_id' ) === (int)$id;
    }

    /** Whether the current user may see the private data (login, e-mail, state) of a user: self, or may edit the user. */
    protected static function sensitive( $id )
    {
        if ( self::isSelf( $id ) )
            return true;
        $object = eZContentObject::fetch( (int)$id );
        return $object instanceof eZContentObject && $object->canEdit();
    }

    /** @throws expServiceException 403 unless the current user may edit the user object */
    protected static function requireEdit( $id )
    {
        $object = self::userObject( $id );
        if ( !$object->canEdit() )
            throw new expServiceException( "No edit access to user $id", 403 );
        return $object;
    }

    /** Public data of a user; login, e-mail and account state only when sensitive() allows. Never a hash. */
    protected static function exportUser( eZUser $user, $detail = false )
    {
        $id = (int)$user->attribute( 'contentobject_id' );
        $object = $user->contentObject();
        $data = array( 'id' => $id,
                       'name' => $object instanceof eZContentObject ? $object->attribute( 'name' ) : '',
                       'node_id' => $object instanceof eZContentObject ? (int)$object->attribute( 'main_node_id' ) : 0,
                       'class_identifier' => $object instanceof eZContentObject ? $object->attribute( 'class_identifier' ) : '',
                       'is_anonymous' => $user->isAnonymous() );
        if ( self::sensitive( $id ) )
        {
            $data['login'] = $user->attribute( 'login' );
            $data['email'] = $user->attribute( 'email' );
            $data['is_enabled'] = (bool)$user->isEnabled();
            $data['is_locked'] = (bool)$user->isLocked();
            if ( $detail )
            {
                $data['login_count'] = (int)$user->loginCount();
                $data['last_visit'] = self::iso( $user->lastVisit() );
                $data['failed_login_attempts'] = (int)$user->failedLoginAttempts();
            }
        }
        if ( $detail )
            $data['groups'] = array_map( 'intval', $user->groups() );
        return $data;
    }

    /** @return int|null the class id of the first user class, for lookups */
    protected static function userClassIDs()
    {
        return eZUser::contentClassIDs();
    }

    /** @throws expServiceException 422 when login, e-mail or password are not acceptable or taken */
    protected static function validateNewAccount( $login, $email, $password, $exceptId = 0 )
    {
        $error = '';
        if ( $login === '' || !eZUser::validateLoginName( $login, $error ) )
            throw new expServiceException( 'The login name is not valid' . ( $error ? ": $error" : '' ), 422 );
        $existing = eZUser::fetchByName( $login );
        if ( $existing instanceof eZUser && (int)$existing->attribute( 'contentobject_id' ) !== (int)$exceptId )
            throw new expServiceException( 'The login name is already taken', 409 );
        if ( !eZMail::validate( $email ) )
            throw new expServiceException( 'The e-mail address is not valid', 422 );
        if ( eZUser::requireUniqueEmail() )
        {
            $other = eZUser::fetchByEmail( $email );
            if ( $other instanceof eZUser && (int)$other->attribute( 'contentobject_id' ) !== (int)$exceptId )
                throw new expServiceException( 'The e-mail address is already in use', 409 );
        }
        if ( $password !== null && !eZUser::validatePassword( $password ) )
            throw new expServiceException( 'The password is too short', 422 );
    }

    protected static function moduleFunctions( $module )
    {
        $m = eZModule::exists( $module );
        if ( !$m instanceof eZModule )
            throw new expServiceException( "Module '$module' does not exist", 404 );
        return (array)$m->attribute( 'available_functions' );
    }

    /** Validates module, function and limitation identifiers; returns the limitations as identifier => list. */
    protected static function checkedLimits( $module, $function, $limits )
    {
        if ( $module !== '*' )
        {
            $functions = self::moduleFunctions( $module );
            if ( $function !== '*' )
            {
                if ( !isset( $functions[$function] ) )
                    throw new expServiceException( "Module '$module' has no function '$function'", 422 );
                $allowed = array_keys( (array)$functions[$function] );
            }
            else
                $allowed = array();
        }
        else
            $allowed = array();
        $out = array();
        foreach ( (array)$limits as $identifier => $values )
        {
            if ( $function === '*' || $module === '*' )
                throw new expServiceException( 'A policy on all modules or functions takes no limitations', 422 );
            if ( !in_array( $identifier, $allowed, true ) )
                throw new expServiceException( "'$identifier' is not a limitation of $module/$function (" . implode( ', ', $allowed ) . ')', 422 );
            $values = is_array( $values ) ? $values : array( $values );
            foreach ( $values as $v )
                if ( !is_scalar( $v ) || $v === '' )
                    throw new expServiceException( "Limitation '$identifier' needs plain values", 422 );
            $out[$identifier] = array_map( 'strval', $values );
        }
        return $out;
    }

    // ------------------------------------------------------------------ groups

    /** @return eZContentObjectTreeNode a user group node @throws expServiceException 404 when it is not a group */
    protected static function groupNode( $nodeId, $function = 'read' )
    {
        $node = self::node( $nodeId, $function );
        $object = $node->object();
        if ( !$object instanceof eZContentObject || !in_array( $object->attribute( 'class_identifier' ), self::groupClassIdentifiers(), true ) )
            throw new expServiceException( "Node $nodeId is not a user group", 404 );
        return $node;
    }

    protected static function groupClassIdentifiers()
    {
        $ini = eZINI::instance( 'site.ini' );
        $ids = $ini->hasVariable( 'UserSettings', 'UserGroupClassID' ) ? (array)$ini->variable( 'UserSettings', 'UserGroupClassID' ) : array( 3 );
        $out = array( 'user_group' );
        foreach ( $ids as $cid )
        {
            $class = eZContentClass::fetch( (int)$cid );
            if ( $class instanceof eZContentClass )
                $out[] = $class->attribute( 'identifier' );
        }
        return array_values( array_unique( $out ) );
    }

    protected static function exportGroup( eZContentObjectTreeNode $node )
    {
        $object = $node->object();
        return array( 'node_id' => (int)$node->attribute( 'node_id' ), 'id' => (int)$node->attribute( 'contentobject_id' ),
                      'name' => $node->attribute( 'name' ), 'parent_node_id' => (int)$node->attribute( 'parent_node_id' ),
                      'depth' => (int)$node->attribute( 'depth' ), 'class_identifier' => $object ? $object->attribute( 'class_identifier' ) : '',
                      'children_count' => (int)$node->childrenCount() );
    }

    // ------------------------------------------------------------------ roles and policies

    protected static function role( $id, $version = 0 )
    {
        $role = eZRole::fetch( (int)$id );
        if ( !$role instanceof eZRole )
            throw new expServiceException( "Role $id does not exist", 404 );
        return $role;
    }

    protected static function exportRole( eZRole $role, $withPolicies = false )
    {
        $data = array( 'id' => (int)$role->attribute( 'id' ), 'name' => $role->attribute( 'name' ),
                       'is_new' => (bool)$role->attribute( 'is_new' ), 'version' => (int)$role->attribute( 'version' ),
                       'is_draft' => (int)$role->attribute( 'version' ) !== 0, 'policy_count' => (int)$role->policyCount() );
        if ( $withPolicies )
            $data['policies'] = array_map( array( 'expUsersBase', 'exportPolicy' ), $role->policyList() );
        return $data;
    }

    protected static function exportPolicy( eZPolicy $policy )
    {
        $limits = array();
        foreach ( $policy->limitationList() as $limitation )
            $limits[$limitation->attribute( 'identifier' )] = array_map( 'strval', $limitation->allValues() );
        return array( 'id' => (int)$policy->attribute( 'id' ), 'role_id' => (int)$policy->attribute( 'role_id' ),
                      'module' => $policy->attribute( 'module_name' ), 'function' => $policy->attribute( 'function_name' ),
                      'limitations' => (object)$limits );
    }

    protected static function pageSliceArgs( array $args, $i )
    {
        return self::paging( $args, $i, $i + 1 );
    }
}
