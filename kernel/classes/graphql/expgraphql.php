<?php
/**
 * The GraphQL API of the kernel (settings/graphql.ini; doc/eng-us/features/6.0/graphql.md): whether it is switched
 * on for the current siteaccess, its settings, and the library it runs on (webonyx/graphql-php 15), loaded from the
 * installation's Composer autoload or from [GraphQLSettings] LibraryPath. The library is optional (composer.json only
 * suggests it): without it the API reports it missing and the rest of the kernel runs as before.
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

    /** The releases to install: 15.32.3 and later, the ones no security advisory affects. */
    const LIBRARY_CONSTRAINT = '^15.32.3';

    /** A GraphQL library is loaded when this class exists (every version has it). */
    const LIBRARY_ENTRY_CLASS = '\GraphQL\GraphQL';

    /** Only webonyx/graphql-php 15 has this class (14 and the 0.13 line call it ValidationContext). */
    const LIBRARY_PROBE_CLASS = '\GraphQL\Validator\QueryValidationContext';

    /** libraryState(): webonyx/graphql-php 15 is loaded. */
    const LIBRARY_AVAILABLE = 'available';

    /** libraryState(): no GraphQL library is installed. */
    const LIBRARY_MISSING = 'missing';

    /** libraryState(): GraphQL\ classes of another major version are loaded. */
    const LIBRARY_INCOMPATIBLE = 'incompatible';

    /** @var bool|null whether the library could be loaded (null: not tried yet) */
    private static $libraryLoaded = null;

    /** @var string|null LIBRARY_AVAILABLE, LIBRARY_MISSING or LIBRARY_INCOMPATIBLE (null: not tried yet) */
    private static $libraryState = null;

    /** @var string see useLibraryClasses() */
    private static $libraryEntryClass = self::LIBRARY_ENTRY_CLASS;

    /** @var string see useLibraryClasses() */
    private static $libraryProbeClass = self::LIBRARY_PROBE_CLASS;

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
     * Loads webonyx/graphql-php 15, from the first of: the installation's Composer autoload (the kernel only
     * suggests it: composer require webonyx/graphql-php); [GraphQLSettings] LibraryPath; the environment variable
     * GRAPHQL_LIBRARY_PATH. LibraryPath is a Composer directory (vendor/autoload.php) or the package itself
     * (src/GraphQL.php).
     *
     * The library is optional. Without it, or with GraphQL\ classes of another major version (a platform that
     * installs ibexa/graphql-php 0.13 or graphql-php 14 for its own GraphQL bundle), this answers false, the API
     * reports why (libraryState(), libraryProblem()) and nothing else of the kernel is affected.
     *
     * @return bool
     */
    public static function loadLibrary()
    {
        if ( self::$libraryLoaded === true )
            return true;
        if ( !class_exists( self::$libraryEntryClass ) )
        {
            $libraryPath = trim( (string)self::setting( 'LibraryPath', '' ) );
            if ( $libraryPath === '' && getenv( 'GRAPHQL_LIBRARY_PATH' ) )
                $libraryPath = (string)getenv( 'GRAPHQL_LIBRARY_PATH' );
            if ( $libraryPath !== '' )
                self::registerLibraryPath( $libraryPath );
        }
        if ( !class_exists( self::$libraryEntryClass ) )
            self::$libraryState = self::LIBRARY_MISSING;
        else if ( !class_exists( self::$libraryProbeClass ) )
            self::$libraryState = self::LIBRARY_INCOMPATIBLE;
        else
            self::$libraryState = self::LIBRARY_AVAILABLE;
        self::$libraryLoaded = self::$libraryState === self::LIBRARY_AVAILABLE;
        if ( !self::$libraryLoaded )
            eZDebug::writeError( 'GraphQL API unavailable: ' . self::libraryProblem(), __METHOD__ );
        return self::$libraryLoaded;
    }

    /**
     * @return string LIBRARY_AVAILABLE, LIBRARY_MISSING or LIBRARY_INCOMPATIBLE
     */
    public static function libraryState()
    {
        if ( self::$libraryState === null )
            self::loadLibrary();
        return self::$libraryState;
    }

    /**
     * @return string|null why the API cannot run (English, for logs and the console), or null when it can
     */
    public static function libraryProblem()
    {
        $state = self::libraryState();
        if ( $state === self::LIBRARY_MISSING )
            return self::LIBRARY_PACKAGE . ' ' . self::LIBRARY_MAJOR . ' is not installed: composer require ' .
                   self::LIBRARY_PACKAGE . ':' . self::LIBRARY_CONSTRAINT . ', or set [GraphQLSettings] LibraryPath in graphql.ini';
        if ( $state === self::LIBRARY_INCOMPATIBLE )
        {
            $found = self::foundLibrary();
            return 'the GraphQL\\ classes loaded are not ' . self::LIBRARY_PACKAGE . ' ' . self::LIBRARY_MAJOR .
                   ( $found !== null ? ' but ' . $found : '' ) . ' (another package of this installation requires that version)';
        }
        return null;
    }

    /**
     * @return string|null the Composer package (and version) that provides the GraphQL\ classes, when Composer knows
     */
    public static function foundLibrary()
    {
        if ( !class_exists( '\Composer\InstalledVersions' ) )
            return null;
        foreach ( array( 'ibexa/graphql-php', 'se7enxweb/graphql-php', self::LIBRARY_PACKAGE ) as $package )
        {
            try
            {
                $version = \Composer\InstalledVersions::isInstalled( $package ) ? \Composer\InstalledVersions::getPrettyVersion( $package ) : null;
            }
            catch ( Exception $e )
            {
                $version = null;
            }
            if ( $version !== null )
                return $package . ' ' . ltrim( (string)$version, 'v' );
        }
        return null;
    }

    /**
     * Tests: the class whose presence means "a GraphQL library is loaded" and the class only webonyx/graphql-php 15
     * has; a name that does not exist simulates an installation without the library (or with another version).
     * Both null: the real ones. Forgets what loadLibrary() found.
     *
     * @param string|null $entryClass
     * @param string|null $probeClass
     */
    public static function useLibraryClasses( $entryClass = null, $probeClass = null )
    {
        self::$libraryEntryClass = $entryClass !== null ? (string)$entryClass : self::LIBRARY_ENTRY_CLASS;
        self::$libraryProbeClass = $probeClass !== null ? (string)$probeClass : self::LIBRARY_PROBE_CLASS;
        self::resetLibrary();
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
        self::$libraryState = null;
    }
}

?>
