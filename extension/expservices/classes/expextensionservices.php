<?php
/**
 * expextension: the extensions of the installation, read-only.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expExtensionServices extends expServiceBase
{
    public static $services = array(
        'list' => array( 'summary' => 'The active extensions with version, license and website where known',
            'access' => array( 'setup', 'system_info' ), 'write' => false, 'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of name, version' ),
        'available' => array( 'summary' => 'The extensions found in the extension directories (active or not)',
            'access' => array( 'setup', 'system_info' ), 'write' => false, 'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of name, active' ),
        'info' => array( 'summary' => 'The ezinfo of one extension: name, version, author, license, website',
            'access' => array( 'setup', 'system_info' ), 'write' => false, 'args' => array( 'name' => 'string' ), 'returns' => 'name, active, info' ),
        'isactive' => array( 'summary' => 'Whether an extension is active',
            'access' => array( 'setup', 'system_info' ), 'write' => false, 'args' => array( 'name' => 'string' ), 'returns' => 'name, active' ),
        'accessextensions' => array( 'summary' => 'The extensions active for the current siteaccess (ActiveAccessExtensions)',
            'access' => array( 'setup', 'system_info' ), 'write' => false, 'args' => array(), 'returns' => 'list of names' ),
        'designextensions' => array( 'summary' => 'The extensions that supply designs (DesignExtensions)',
            'access' => array( 'setup', 'system_info' ), 'write' => false, 'args' => array(), 'returns' => 'list of names' ),
        'settingsfiles' => array( 'summary' => 'The settings files an extension ships',
            'access' => array( 'setup', 'system_info' ), 'write' => false, 'args' => array( 'name' => 'string' ), 'returns' => 'list of relative paths' ),
        'directories' => array( 'summary' => 'The extension root directories',
            'access' => array( 'setup', 'system_info' ), 'write' => false, 'args' => array(), 'returns' => 'list of paths' ),
        'count' => array( 'summary' => 'The number of active and available extensions',
            'access' => array( 'setup', 'system_info' ), 'write' => false, 'args' => array(), 'returns' => 'active, available' ),
    );

    protected static function nameArg( $args )
    {
        $name = self::arg( $args, 0, 'string' );
        if ( !preg_match( '/^[A-Za-z0-9_.\-]+$/', $name ) )
            throw new expServiceException( 'Not an extension name', 400 );
        return $name;
    }

    protected static function rel( $path )
    {
        $root = rtrim( eZSys::rootDir(), '/' ) . '/';
        return strpos( $path, $root ) === 0 ? substr( $path, strlen( $root ) ) : $path;
    }

    protected static function exportInfo( $info )
    {
        $out = array();
        foreach ( (array)$info as $key => $value )
            if ( is_scalar( $value ) )
                $out[$key] = strip_tags( (string)$value );
        return $out;
    }

    public static function list( $args )
    {
        self::guard( __FUNCTION__ );
        $list = array();
        foreach ( eZExtension::activeExtensions() as $name )
        {
            $info = eZExtension::extensionInfo( $name );
            $list[] = array( 'name' => $name, 'version' => isset( $info['version'] ) ? $info['version'] : ( isset( $info['Version'] ) ? $info['Version'] : null ) );
        }
        return self::pageOf( $list, $args, 0, 1 );
    }

    public static function available( $args )
    {
        self::guard( __FUNCTION__ );
        $active = eZExtension::activeExtensions();
        $names = array();
        foreach ( eZExtension::extensionRootDirectories() as $root )
            foreach ( (array)glob( rtrim( $root, '/' ) . '/*', GLOB_ONLYDIR ) as $dir )
                $names[basename( $dir )] = true;
        ksort( $names );
        $list = array();
        foreach ( array_keys( $names ) as $name )
            $list[] = array( 'name' => $name, 'active' => in_array( $name, $active, true ) );
        return self::pageOf( $list, $args, 0, 1 );
    }

    public static function info( $args )
    {
        self::guard( __FUNCTION__ );
        $name = self::nameArg( $args );
        if ( eZExtension::extensionPath( $name ) === false )
            throw new expServiceException( "No extension '$name'", 404 );
        return self::ok( array( 'name' => $name, 'active' => in_array( $name, eZExtension::activeExtensions(), true ),
                               'info' => self::exportInfo( eZExtension::extensionInfo( $name ) ) ) );
    }

    public static function isactive( $args )
    {
        self::guard( __FUNCTION__ );
        $name = self::nameArg( $args );
        return self::ok( array( 'name' => $name, 'active' => in_array( $name, eZExtension::activeExtensions(), true ) ) );
    }

    public static function accessextensions( $args )
    {
        self::guard( __FUNCTION__ );
        $list = array_values( (array)eZINI::instance()->variable( 'ExtensionSettings', 'ActiveAccessExtensions' ) );
        return self::ok( $list, array( 'total' => count( $list ) ) );
    }

    public static function designextensions( $args )
    {
        self::guard( __FUNCTION__ );
        $list = array_values( (array)eZINI::instance()->variable( 'ExtensionSettings', 'DesignExtensions' ) );
        return self::ok( $list, array( 'total' => count( $list ) ) );
    }

    public static function settingsfiles( $args )
    {
        self::guard( __FUNCTION__ );
        $name = self::nameArg( $args );
        $path = eZExtension::extensionPath( $name );
        if ( $path === false )
            throw new expServiceException( "No extension '$name'", 404 );
        $list = array();
        foreach ( array_merge( (array)glob( $path . '/settings/*.ini*' ), (array)glob( $path . '/settings/*/*.ini*' ) ) as $file )
            $list[] = self::rel( $file );
        sort( $list );
        return self::ok( $list, array( 'total' => count( $list ) ) );
    }

    public static function directories( $args )
    {
        self::guard( __FUNCTION__ );
        $list = array_map( array( __CLASS__, 'rel' ), eZExtension::extensionRootDirectories() );
        return self::ok( array_values( $list ), array( 'total' => count( $list ) ) );
    }

    public static function count( $args )
    {
        self::guard( __FUNCTION__ );
        $available = 0;
        foreach ( eZExtension::extensionRootDirectories() as $root )
            $available += count( (array)glob( rtrim( $root, '/' ) . '/*', GLOB_ONLYDIR ) );
        return self::ok( array( 'active' => count( eZExtension::activeExtensions() ), 'available' => $available ) );
    }
}
