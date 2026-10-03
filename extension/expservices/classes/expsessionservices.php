<?php
/**
 * expsession: the session login of remote apps. A non-browser client keeps the session cookie (eZSESSID...)
 * and sends the form token (expsession::token) with every write.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expSessionServices extends expServiceBase
{
    public static $services = array(
        'login' => array( 'summary' => 'Signs in with POST username and password; the session cookie of the response is the session',
            'access' => 'public', 'write' => false, 'args' => array( 'username' => 'string (POST)', 'password' => 'string (POST)' ),
            'returns' => 'the user (whoami) and the form token for writes' ),
        'logout' => array( 'summary' => 'Signs out (POST with the form token)',
            'access' => 'user', 'write' => true, 'args' => array(), 'returns' => 'logged_out' ),
        'whoami' => array( 'summary' => 'The current user: id, login, name, email, registered, groups',
            'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'user descriptor (anonymous when not signed in)' ),
        'token' => array( 'summary' => 'The form token of this session for writes (field ezxform_token or header X-CSRF-Token)',
            'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'token (null when the form token protection is off), field, header' ),
        'ping' => array( 'summary' => 'Keeps the session alive and tells whether it is signed in',
            'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'registered, time' ),
        'access' => array( 'summary' => 'Whether the user has module/function (accessWord yes, no or limited)',
            'access' => 'user', 'write' => false, 'args' => array( 'module' => 'string', 'function' => 'string' ),
            'returns' => 'module, function, access_word' ),
        'roles' => array( 'summary' => 'The roles of the user with the policies of each (module, function)',
            'access' => 'user', 'write' => false, 'args' => array(), 'returns' => 'list of role id, name, policies' ),
        'groups' => array( 'summary' => 'The user groups of the user',
            'access' => 'user', 'write' => false, 'args' => array(), 'returns' => 'list of object id, name' ),
    );

    /** The public description of a user (never the password hash). */
    public static function exportUser( eZUser $user )
    {
        $registered = $user->isRegistered();
        $groups = array();
        if ( $registered )
            foreach ( (array)$user->attribute( 'groups' ) as $id )
                $groups[] = (int)$id;
        $object = $user->attribute( 'contentobject' );
        return array(
            'id' => (int)$user->attribute( 'contentobject_id' ),
            'login' => $registered ? $user->attribute( 'login' ) : null,
            'name' => $object ? $object->attribute( 'name' ) : null,
            'email' => $registered ? $user->attribute( 'email' ) : null,
            'registered' => (bool)$registered,
            'anonymous' => !$registered,
            'groups' => $groups,
        );
    }

    public static function login( $args )
    {
        self::guard( __FUNCTION__ );
        if ( !isset( $_SERVER['REQUEST_METHOD'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' )
        {
            if ( self::$trustRequest !== true )
                throw new expServiceException( 'Signing in is sent with POST (username, password)', 403 );
        }
        $username = self::post( 'username', 'string' );
        $password = self::post( 'password', 'string' );
        $user = eZUser::loginUser( $username, $password );
        if ( !$user instanceof eZUser )
            throw new expServiceException( 'Wrong user name or password', 401 );
        return self::ok( array( 'user' => self::exportUser( $user ), 'token' => self::formToken(), 'token_field' => 'ezxform_token' ) );
    }

    public static function logout( $args )
    {
        self::guard( __FUNCTION__ );
        eZUser::logoutCurrent();
        return self::ok( array( 'logged_out' => true ) );
    }

    public static function whoami( $args )
    {
        self::guard( __FUNCTION__ );
        return self::ok( self::exportUser( eZUser::currentUser() ) );
    }

    public static function token( $args )
    {
        self::guard( __FUNCTION__ );
        return self::ok( array( 'token' => self::formToken(), 'field' => 'ezxform_token', 'header' => 'X-CSRF-Token' ) );
    }

    public static function ping( $args )
    {
        self::guard( __FUNCTION__ );
        return self::ok( array( 'registered' => (bool)eZUser::currentUser()->isRegistered(), 'time' => time() ) );
    }

    public static function access( $args )
    {
        self::guard( __FUNCTION__ );
        $module = self::arg( $args, 0, 'string' );
        $function = self::arg( $args, 1, 'string' );
        if ( !preg_match( '/^[a-z0-9_]+$/i', $module . $function ) )
            throw new expServiceException( 'module and function are names', 400 );
        $result = eZUser::currentUser()->hasAccessTo( $module, $function );
        return self::ok( array( 'module' => $module, 'function' => $function,
                                'access_word' => isset( $result['accessWord'] ) ? $result['accessWord'] : 'no' ) );
    }

    public static function roles( $args )
    {
        self::guard( __FUNCTION__ );
        $list = array();
        foreach ( (array)eZUser::currentUser()->roles() as $role )
        {
            $policies = array();
            foreach ( (array)$role->attribute( 'policies' ) as $policy )
                $policies[] = array( 'module' => $policy->attribute( 'module_name' ), 'function' => $policy->attribute( 'function_name' ) );
            $list[] = array( 'id' => (int)$role->attribute( 'id' ), 'name' => $role->attribute( 'name' ), 'policies' => $policies );
        }
        return self::ok( $list, array( 'total' => count( $list ) ) );
    }

    public static function groups( $args )
    {
        self::guard( __FUNCTION__ );
        $list = array();
        foreach ( (array)eZUser::currentUser()->attribute( 'groups' ) as $id )
        {
            $object = eZContentObject::fetch( (int)$id );
            if ( $object )
                $list[] = array( 'id' => (int)$id, 'name' => $object->attribute( 'name' ) );
        }
        return self::ok( $list, array( 'total' => count( $list ) ) );
    }
}
