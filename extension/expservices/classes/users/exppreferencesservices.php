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
    /**
     * The services (expServiceBase), declared here rather than assigned after the class: a persistent worker
     * (Velocity) puts the statics of a class loaded during a request back to their declared value before the
     * next one, and an assignment after the class would not run again.
     */
    public static $services = array(
        'get' => array( 'summary' => 'One preference of the current user', 'access' => 'user', 'write' => false, 'args' => array( 'name' => 'string' ), 'returns' => 'name, value, exists' ),
        'getMany' => array( 'summary' => 'Several preferences of the current user (comma separated names)', 'access' => 'user', 'write' => false, 'args' => array( 'names' => 'list' ), 'returns' => 'name => value' ),
        'listAll' => array( 'summary' => 'All preferences of the current user, paged', 'access' => 'user', 'write' => false, 'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged name, value' ),
        'byPrefix' => array( 'summary' => 'The preferences whose name starts with a prefix', 'access' => 'user', 'write' => false, 'args' => array( 'prefix' => 'string', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged name, value' ),
        'exists' => array( 'summary' => 'Whether a preference is set', 'access' => 'user', 'write' => false, 'args' => array( 'name' => 'string' ), 'returns' => 'exists' ),
        'count' => array( 'summary' => 'The number of preferences of the current user', 'access' => 'user', 'write' => false, 'args' => array(), 'returns' => 'count' ),
        'set' => array( 'summary' => 'Set a preference (POST name, value)', 'access' => 'user', 'write' => true, 'args' => array( 'name' => 'string', 'value' => 'string' ), 'returns' => 'name, value' ),
        'setMany' => array( 'summary' => 'Set up to 100 preferences (POST values JSON)', 'access' => 'user', 'write' => true, 'args' => array( 'values' => 'json' ), 'returns' => 'stored' ),
        'remove' => array( 'summary' => 'Remove a preference (POST name)', 'access' => 'user', 'write' => true, 'args' => array( 'name' => 'string' ), 'returns' => 'removed' ),
        'increment' => array( 'summary' => 'Add to a numeric preference (POST name, by)', 'access' => 'user', 'write' => true, 'args' => array( 'name' => 'string', 'by' => 'int' ), 'returns' => 'value' ),
        'listOf' => array( 'summary' => 'The preferences of another user', 'access' => array( 'setup', 'administrate' ), 'write' => false, 'args' => array( 'id' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged name, value' ),
        'getOf' => array( 'summary' => 'One preference of another user', 'access' => array( 'setup', 'administrate' ), 'write' => false, 'args' => array( 'id' => 'int', 'name' => 'string' ), 'returns' => 'value' ),
        'setOf' => array( 'summary' => 'Set a preference of another user (POST user, name, value)', 'access' => array( 'setup', 'administrate' ), 'write' => true, 'args' => array( 'user' => 'int', 'name' => 'string', 'value' => 'string' ), 'returns' => 'value' ),
    );

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
