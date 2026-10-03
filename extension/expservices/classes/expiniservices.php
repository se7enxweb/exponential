<?php
/**
 * expini: reads the settings (INI files), read-only; secrets are masked as exp:ini masks them.
 *
/**
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expIniServices extends expServiceBase
{
    public static $services = array(
        'files' => array( 'summary' => 'The INI files of the installation (kernel, override and extension settings)',
            'access' => array( 'setup', 'setup' ), 'write' => false, 'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of ini name, origin, path' ),
        'groups' => array( 'summary' => 'The groups (blocks) of an INI file as the current siteaccess reads it',
            'access' => array( 'setup', 'setup' ), 'write' => false, 'args' => array( 'file' => 'string', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of group names' ),
        'variables' => array( 'summary' => 'The variables of one group with their values (secrets masked)',
            'access' => array( 'setup', 'setup' ), 'write' => false, 'args' => array( 'file' => 'string', 'group' => 'string' ), 'returns' => 'map of variable to value' ),
        'get' => array( 'summary' => 'One setting value as the current siteaccess reads it (secrets masked)',
            'access' => array( 'setup', 'setup' ), 'write' => false, 'args' => array( 'file' => 'string', 'group' => 'string', 'variable' => 'string' ), 'returns' => 'file, group, variable, value, masked' ),
        'has' => array( 'summary' => 'Whether a group or a variable exists',
            'access' => array( 'setup', 'setup' ), 'write' => false, 'args' => array( 'file' => 'string', 'group' => 'string', 'variable' => 'string' ), 'returns' => 'exists' ),
        'search' => array( 'summary' => 'Finds variables whose name contains a text, in one INI file (secrets masked)',
            'access' => array( 'setup', 'setup' ), 'write' => false, 'args' => array( 'file' => 'string', 'text' => 'string', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of group, variable, value' ),
        'siteaccesses' => array( 'summary' => 'The siteaccesses that can have their own settings',
            'access' => array( 'setup', 'setup' ), 'write' => false, 'args' => array(), 'returns' => 'list of siteaccess names' ),
        'scopes' => array( 'summary' => 'The settings scopes exp:ini knows (global, siteaccesses, extensions)',
            'access' => array( 'setup', 'setup' ), 'write' => false, 'args' => array(), 'returns' => 'list of scope descriptors' ),
        'secret' => array( 'summary' => 'Whether a variable name is treated as a secret (masked)',
            'access' => array( 'setup', 'setup' ), 'write' => false, 'args' => array( 'variable' => 'string' ), 'returns' => 'variable, secret' ),
        'activeextensions' => array( 'summary' => 'The active extensions in the order the settings read them',
            'access' => array( 'setup', 'setup' ), 'write' => false, 'args' => array(), 'returns' => 'list of extension names' ),
    );

    /** The INI object of a file name (site.ini, not a path), 400 for anything else. */
    protected static function ini( $file )
    {
        if ( !preg_match( '/^[a-z0-9_\-]+\.ini$/i', $file ) )
            throw new expServiceException( 'The file is an INI file name such as site.ini', 400 );
        $ini = eZINI::instance( $file );
        if ( !$ini->groups() )
            throw new expServiceException( "No settings found in $file", 404 );
        return $ini;
    }

    /** A value, masked when its variable name is a secret. */
    protected static function shown( $variable, $value )
    {
        return expIniEditor::isSecret( $variable ) ? expIniEditor::maskValue( $value ) : $value;
    }

    public static function files( $args )
    {
        self::guard( __FUNCTION__ );
        $list = array();
        foreach ( expRADSurvey::iniFiles() as $f )
            $list[] = array( 'ini' => $f['ini'], 'origin' => $f['origin'], 'path' => $f['path'] );
        usort( $list, function ( $a, $b ) { return strcmp( $a['ini'] . $a['path'], $b['ini'] . $b['path'] ); } );
        return self::pageOf( $list, $args, 0, 1 );
    }

    public static function groups( $args )
    {
        self::guard( __FUNCTION__ );
        $ini = self::ini( self::arg( $args, 0, 'string' ) );
        $names = array_keys( $ini->groups() );
        return self::pageOf( $names, $args, 1, 2 );
    }

    public static function variables( $args )
    {
        self::guard( __FUNCTION__ );
        $ini = self::ini( self::arg( $args, 0, 'string' ) );
        $group = self::arg( $args, 1, 'string' );
        if ( !$ini->hasGroup( $group ) )
            throw new expServiceException( "No group '$group'", 404 );
        $out = array();
        foreach ( $ini->group( $group ) as $variable => $value )
            $out[$variable] = self::shown( $variable, $value );
        return self::ok( $out, array( 'total' => count( $out ) ) );
    }

    public static function get( $args )
    {
        self::guard( __FUNCTION__ );
        $file = self::arg( $args, 0, 'string' );
        $ini = self::ini( $file );
        $group = self::arg( $args, 1, 'string' );
        $variable = self::arg( $args, 2, 'string' );
        if ( !$ini->hasVariable( $group, $variable ) )
            throw new expServiceException( "No setting $group/$variable in $file", 404 );
        return self::ok( array( 'file' => $file, 'group' => $group, 'variable' => $variable,
                               'value' => self::shown( $variable, $ini->variable( $group, $variable ) ),
                               'masked' => expIniEditor::isSecret( $variable ) ) );
    }

    public static function has( $args )
    {
        self::guard( __FUNCTION__ );
        $ini = self::ini( self::arg( $args, 0, 'string' ) );
        $group = self::arg( $args, 1, 'string' );
        $variable = self::arg( $args, 2, 'string', null );
        return self::ok( array( 'exists' => $variable === null ? $ini->hasGroup( $group ) : $ini->hasVariable( $group, $variable ) ) );
    }

    public static function search( $args )
    {
        self::guard( __FUNCTION__ );
        $ini = self::ini( self::arg( $args, 0, 'string' ) );
        $text = strtolower( self::arg( $args, 1, 'string' ) );
        $found = array();
        foreach ( $ini->groups() as $group => $values )
            foreach ( $values as $variable => $value )
                if ( strpos( strtolower( $variable ), $text ) !== false )
                    $found[] = array( 'group' => $group, 'variable' => $variable, 'value' => self::shown( $variable, $value ) );
        return self::pageOf( $found, $args, 2, 3 );
    }

    public static function siteaccesses( $args )
    {
        self::guard( __FUNCTION__ );
        $list = array_values( expIniEditor::knownSiteAccesses() );
        return self::ok( $list, array( 'total' => count( $list ) ) );
    }

    public static function scopes( $args )
    {
        self::guard( __FUNCTION__ );
        $list = array();
        foreach ( expIniEditor::scopes() as $key => $scope )
            $list[] = array( 'key' => is_string( $key ) ? $key : (string)$scope->name(), 'name' => (string)$scope->name() );
        return self::ok( $list, array( 'total' => count( $list ) ) );
    }

    public static function secret( $args )
    {
        self::guard( __FUNCTION__ );
        $variable = self::arg( $args, 0, 'string' );
        return self::ok( array( 'variable' => $variable, 'secret' => expIniEditor::isSecret( $variable ) ) );
    }

    public static function activeextensions( $args )
    {
        self::guard( __FUNCTION__ );
        $list = array_values( (array)eZINI::instance()->variable( 'ExtensionSettings', 'ActiveExtensions' ) );
        return self::ok( $list, array( 'total' => count( $list ) ) );
    }
}
