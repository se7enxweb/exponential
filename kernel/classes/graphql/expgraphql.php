<?php
/**
 * The GraphQL API of the kernel (settings/graphql.ini; doc/eng-us/features/6.0/graphql.md): whether it is switched
 * on for the current siteaccess, its settings, and the library it runs on (webonyx/graphql-php 15), loaded from the
 * installation's Composer autoload or from [GraphQLSettings] LibraryPath.
 *
 * Nothing here keeps per-request state: a persistent worker (Velocity) reads the settings of each request from
 * eZINI, which the kernel resets between requests. Only the fact that the library was loaded is kept, as its
 * classes stay loaded.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expGraphQL
{
    /** The Composer package of the library. */
    const LIBRARY_PACKAGE = 'webonyx/graphql-php';

    /** The library's major version the kernel is written for. */
    const LIBRARY_MAJOR = 15;

    /** @var bool|null whether the library could be loaded (null: not tried yet) */
    private static $libraryLoaded = null;

    /**
     * @return eZINI
     */
    public static function ini()
    {
        return eZINI::instance( 'graphql.ini' );
    }

    /**
     * A setting of [GraphQLSettings], or $default when it is not set.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public static function setting( $name, $default = null )
    {
        $ini = self::ini();
        if ( !$ini->hasVariable( 'GraphQLSettings', $name ) )
            return $default;
        return $ini->variable( 'GraphQLSettings', $name );
    }

    /**
     * @param string $name
     * @param int $default
     * @return int a positive whole number setting of [GraphQLSettings]
     */
    public static function intSetting( $name, $default )
    {
        $value = (int)self::setting( $name, $default );
        return $value > 0 ? $value : (int)$default;
    }

    /**
     * @param string $name
     * @return string[] a list setting of [GraphQLSettings], without empty entries
     */
    public static function listSetting( $name )
    {
        $value = self::setting( $name, array() );
        if ( !is_array( $value ) )
            return array();
        return array_values( array_filter( array_map( 'trim', $value ), 'strlen' ) );
    }

    /**
     * Whether the API answers in the current siteaccess (or in $siteAccess): Enabled=enabled, and the siteaccess is
     * in SiteAccessList[] when that is not empty.
     *
     * @param string|null $siteAccess
     * @return bool
     */
    public static function isEnabled( $siteAccess = null )
    {
        if ( self::setting( 'Enabled', 'disabled' ) !== 'enabled' )
            return false;
        $list = self::listSetting( 'SiteAccessList' );
        if ( !$list )
            return true;
        if ( $siteAccess === null )
            $siteAccess = self::currentSiteAccess();
        return in_array( $siteAccess, $list, true );
    }

    /**
     * @return string the name of the current siteaccess ('' when none was chosen)
     */
    public static function currentSiteAccess()
    {
        $access = eZSiteAccess::current();
        return is_array( $access ) && isset( $access['name'] ) ? (string)$access['name'] : '';
    }

    /**
     * Loads webonyx/graphql-php, from the first of: the installation's Composer autoload (composer.json requires
     * it); [GraphQLSettings] LibraryPath; the environment variable GRAPHQL_LIBRARY_PATH. LibraryPath is a Composer
     * directory (vendor/autoload.php) or the package itself (src/GraphQL.php).
     *
     * @return bool
     */
    public static function loadLibrary()
    {
        if ( self::$libraryLoaded === true )
            return true;
        if ( !class_exists( '\GraphQL\GraphQL' ) )
        {
            $libraryPath = trim( (string)self::setting( 'LibraryPath', '' ) );
            if ( $libraryPath === '' && getenv( 'GRAPHQL_LIBRARY_PATH' ) )
                $libraryPath = (string)getenv( 'GRAPHQL_LIBRARY_PATH' );
            if ( $libraryPath !== '' )
                self::registerLibraryPath( $libraryPath );
        }
        self::$libraryLoaded = class_exists( '\GraphQL\GraphQL' );
        if ( !self::$libraryLoaded )
            eZDebug::writeError( 'webonyx/graphql-php 15 not found: install it with Composer or set [GraphQLSettings] LibraryPath in graphql.ini', __METHOD__ );
        return self::$libraryLoaded;
    }

    /**
     * @param string $libraryPath
     */
    private static function registerLibraryPath( $libraryPath )
    {
        if ( $libraryPath[0] !== '/' )
            $libraryPath = self::rootDir() . '/' . $libraryPath;
        $libraryPath = rtrim( $libraryPath, '/' );
        foreach ( array( $libraryPath . '/vendor/autoload.php', $libraryPath . '/autoload.php' ) as $autoload )
        {
            if ( is_file( $autoload ) && is_file( dirname( $autoload ) . '/webonyx/graphql-php/src/GraphQL.php' ) )
            {
                require_once $autoload;
                return;
            }
        }
        $src = $libraryPath . '/src/';
        if ( is_file( $src . 'GraphQL.php' ) )
        {
            spl_autoload_register( function ( $class ) use ( $src )
            {
                if ( strncmp( $class, 'GraphQL\\', 8 ) !== 0 )
                    return;
                $file = $src . str_replace( '\\', '/', substr( $class, 8 ) ) . '.php';
                if ( is_file( $file ) )
                    require $file;
            } );
        }
    }

    /**
     * @return string|null the installed version of the library (15.x.y), or null when it is not loaded or unknown
     */
    public static function libraryVersion()
    {
        if ( !self::loadLibrary() )
            return null;
        if ( class_exists( '\Composer\InstalledVersions' ) )
        {
            foreach ( \Composer\InstalledVersions::getAllRawData() as $data )
            {
                if ( isset( $data['versions'][self::LIBRARY_PACKAGE]['pretty_version'] ) )
                    return ltrim( (string)$data['versions'][self::LIBRARY_PACKAGE]['pretty_version'], 'v' );
            }
        }
        // The package's directory: a Composer vendor directory keeps installed.json two levels up
        $reflection = new ReflectionClass( '\GraphQL\GraphQL' );
        $installed = dirname( (string)$reflection->getFileName(), 4 ) . '/composer/installed.json';
        if ( is_file( $installed ) )
        {
            $data = json_decode( (string)file_get_contents( $installed ), true );
            $packages = isset( $data['packages'] ) ? $data['packages'] : ( is_array( $data ) ? $data : array() );
            foreach ( $packages as $package )
            {
                if ( isset( $package['name'], $package['version'] ) && $package['name'] === self::LIBRARY_PACKAGE )
                    return ltrim( (string)$package['version'], 'v' );
            }
        }
        return null;
    }

    /**
     * @return string the installation root
     */
    public static function rootDir()
    {
        return dirname( __DIR__, 3 );
    }

    /**
     * Forgets whether the library was loaded (tests).
     */
    public static function resetLibrary()
    {
        self::$libraryLoaded = null;
    }
}

?>
