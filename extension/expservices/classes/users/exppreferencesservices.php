<?php
/**
 * exppreferences: the preferences of the current user (the key/value store the admin uses for the tree
 * state, page sizes, ...). Another user's preferences are only read or changed with the setup/administrate policy.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expPreferencesServices extends expUsersBase
{
    public static $services = array();

    protected static function name( $name )
    {
        $name = trim( (string)$name );
        if ( $name === '' || strlen( $name ) > 100 || !preg_match( '/^[A-Za-z0-9_.\\-]+$/', $name ) )
            throw new expServiceException( 'A preference name is 1 to 100 characters of letters, digits, _ . -', 422 );
        return $name;
    }

    protected static function value( $v )
    {
        if ( !is_scalar( $v ) )
            throw new expServiceException( 'A preference value is plain text', 422 );
        return (string)$v;
    }

    public static function get( array $a = array() )
    {
        self::guard( 'get' );
        $name = self::name( self::arg( $a, 0, 'string' ) );
        $v = eZPreferences::value( $name );
        return self::ok( array( 'name' => $name, 'value' => $v === false ? null : $v, 'exists' => $v !== false ) );
    }

    public static function getMany( array $a = array() )
    {
        self::guard( 'getMany' );
        $out = array();
        foreach ( self::arg( $a, 0, 'list' ) as $n )
        {
            $v = eZPreferences::value( self::name( $n ) );
            $out[$n] = $v === false ? null : $v;
        }
        return self::ok( (object)$out );
    }

    public static function listAll( array $a = array() )
    {
        self::guard( 'listAll' );
        $rows = array();
        foreach ( eZPreferences::values() as $k => $v )
            $rows[] = array( 'name' => $k, 'value' => $v );
        return self::pageOf( $rows, $a, 0, 1 );
    }

    public static function byPrefix( array $a = array() )
    {
        self::guard( 'byPrefix' );
        $prefix = self::arg( $a, 0, 'string' );
        $rows = array();
        foreach ( eZPreferences::values() as $k => $v )
            if ( strpos( $k, $prefix ) === 0 )
                $rows[] = array( 'name' => $k, 'value' => $v );
        return self::pageOf( $rows, $a, 1, 2 );
    }

    public static function exists( array $a = array() )
    {
        self::guard( 'exists' );
        return self::ok( array( 'exists' => eZPreferences::value( self::name( self::arg( $a, 0, 'string' ) ) ) !== false ) );
    }

    public static function count( array $a = array() )
    {
        self::guard( 'count' );
        return self::ok( array( 'count' => count( eZPreferences::values() ) ) );
    }

    public static function set( array $a = array() )
    {
        self::guard( 'set' );
        $name = self::name( self::post( 'name', 'string' ) );
        $value = self::value( self::post( 'value', 'string' ) );
        if ( !eZPreferences::setValue( $name, $value ) )
            throw new expServiceException( 'The preference could not be stored', 422 );
        return self::ok( array( 'name' => $name, 'value' => $value ) );
    }

    public static function setMany( array $a = array() )
    {
        self::guard( 'setMany' );
        $values = self::post( 'values', 'json' );
        if ( !is_array( $values ) || !$values || count( $values ) > 100 )
            throw new expServiceException( 'values must be a JSON object of 1 to 100 name => value', 400 );
        foreach ( $values as $n => $v )
        {
            self::name( $n );
            self::value( $v );
        }
        foreach ( $values as $n => $v )
            eZPreferences::setValue( $n, (string)$v );
        return self::ok( array( 'stored' => array_keys( $values ) ) );
    }

    public static function remove( array $a = array() )
    {
        self::guard( 'remove' );
        $name = self::name( self::post( 'name', 'string' ) );
        $db = eZDB::instance();
        $db->query( 'DELETE FROM ezpreferences WHERE user_id = ' . (int)eZUser::currentUserID() . " AND name = '" . $db->escapeString( $name ) . "'" );
        eZPreferences::storeInSession( $name, false );
        return self::ok( array( 'name' => $name, 'removed' => true ) );
    }

    public static function increment( array $a = array() )
    {
        self::guard( 'increment' );
        $name = self::name( self::post( 'name', 'string' ) );
        $by = self::post( 'by', 'int', 1 );
        $v = eZPreferences::value( $name );
        if ( $v !== false && !preg_match( '/^-?\d+$/', (string)$v ) )
            throw new expServiceException( 'The preference is not a number', 409 );
        $new = (int)( $v === false ? 0 : $v ) + $by;
        eZPreferences::setValue( $name, (string)$new );
        return self::ok( array( 'name' => $name, 'value' => $new ) );
    }

    public static function listOf( array $a = array() )
    {
        self::guard( 'listOf' );
        $user = self::user( self::arg( $a, 0, 'int' ) );
        $rows = array();
        foreach ( eZPreferences::values( $user ) as $k => $v )
            $rows[] = array( 'name' => $k, 'value' => $v );
        return self::pageOf( $rows, $a, 1, 2 );
    }

    public static function getOf( array $a = array() )
    {
        self::guard( 'getOf' );
        $user = self::user( self::arg( $a, 0, 'int' ) );
        $name = self::name( self::arg( $a, 1, 'string' ) );
        $v = eZPreferences::value( $name, $user );
        return self::ok( array( 'user' => (int)$user->attribute( 'contentobject_id' ), 'name' => $name, 'value' => $v === false ? null : $v ) );
    }

    public static function setOf( array $a = array() )
    {
        self::guard( 'setOf' );
        $user = self::user( self::post( 'user', 'int' ) );
        $name = self::name( self::post( 'name', 'string' ) );
        $value = self::value( self::post( 'value', 'string' ) );
        eZPreferences::setValue( $name, $value, (int)$user->attribute( 'contentobject_id' ) );
        return self::ok( array( 'user' => (int)$user->attribute( 'contentobject_id' ), 'name' => $name, 'value' => $value ) );
    }
}

expPreferencesServices::$services = expUsersBase::specs( array(
    'get' => array( 'One preference of the current user', 'user', 'r', 'name:string', 'name, value, exists' ),
    'getMany' => array( 'Several preferences of the current user (comma separated names)', 'user', 'r', 'names:list', 'name => value' ),
    'listAll' => array( 'All preferences of the current user, paged', 'user', 'r', 'limit:int,offset:int', 'paged name, value' ),
    'byPrefix' => array( 'The preferences whose name starts with a prefix', 'user', 'r', 'prefix:string,limit:int,offset:int', 'paged name, value' ),
    'exists' => array( 'Whether a preference is set', 'user', 'r', 'name:string', 'exists' ),
    'count' => array( 'The number of preferences of the current user', 'user', 'r', '', 'count' ),
    'set' => array( 'Set a preference (POST name, value)', 'user', 'w', 'name:string,value:string', 'name, value' ),
    'setMany' => array( 'Set up to 100 preferences (POST values JSON)', 'user', 'w', 'values:json', 'stored' ),
    'remove' => array( 'Remove a preference (POST name)', 'user', 'w', 'name:string', 'removed' ),
    'increment' => array( 'Add to a numeric preference (POST name, by)', 'user', 'w', 'name:string,by:int', 'value' ),
    'listOf' => array( 'The preferences of another user', 'setup/administrate', 'r', 'id:int,limit:int,offset:int', 'paged name, value' ),
    'getOf' => array( 'One preference of another user', 'setup/administrate', 'r', 'id:int,name:string', 'value' ),
    'setOf' => array( 'Set a preference of another user (POST user, name, value)', 'setup/administrate', 'w', 'user:int,name:string,value:string', 'value' ),
) );
