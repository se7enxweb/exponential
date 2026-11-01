<?php
/**
 * File containing the expMigrationYamlParser class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Reads the YAML of a migration file into PHP values, through symfony/yaml (the standalone component, ^5.4 on PHP 7.4
 * and 8.0, ^6.4 or ^7.0 on newer PHP). The component comes from the first of: the installation's Composer autoload
 * (composer.json requires it); migration.ini [YamlSettings] LibraryPath; the environment variable
 * MIGRATION_YAML_LIBRARY_PATH. LibraryPath is a Composer directory (vendor/autoload.php) or the package itself (the
 * folder with Yaml.php).
 *
 * Only plain YAML is read: no PHP objects, constants or enums (!php/... tags are refused), no custom tags; a date
 * stays the text it is written as, a duplicate key is an error. Without the component every YAML migration is
 * reported as not runnable with the reason (available(), problem()); nothing else of the kernel needs it.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationYamlParser
{
    /** The Composer package and the constraint composer.json names. */
    const LIBRARY_PACKAGE = 'symfony/yaml';
    const LIBRARY_CONSTRAINT = '^5.4 || ^6.4 || ^7.0';

    /** The class that tells the component is loaded. */
    const LIBRARY_CLASS = 'Symfony\\Component\\Yaml\\Yaml';

    /** @var bool|null whether the component is loaded; null: not looked for yet */
    private static $available = null;

    /** @var string the class looked for (a test names one that does not exist to simulate an installation without it) */
    private static $libraryClass = self::LIBRARY_CLASS;

    /**
     * Whether the YAML component can be used; loads it from LibraryPath the first time when Composer has not.
     *
     * @return bool
     */
    public static function available()
    {
        if ( self::$available !== null )
            return self::$available;
        if ( !class_exists( self::$libraryClass ) )
        {
            $path = self::libraryPath();
            if ( $path !== '' && self::$libraryClass === self::LIBRARY_CLASS )
                self::registerLibraryPath( $path );
        }
        self::$available = class_exists( self::$libraryClass );
        return self::$available;
    }

    /**
     * Why YAML migrations cannot be read, or null when they can.
     *
     * @return string|null
     */
    public static function problem()
    {
        if ( self::available() )
            return null;
        return self::LIBRARY_PACKAGE . ' is not installed, so YAML migrations cannot be read: composer require "'
               . self::LIBRARY_PACKAGE . ':' . self::LIBRARY_CONSTRAINT . '", or set [YamlSettings] LibraryPath in migration.ini';
    }

    /**
     * Tests: the class whose presence means "the component is loaded" (null: the real one). Forgets what available()
     * found.
     *
     * @param string|null $class
     */
    public static function useLibraryClass( $class = null )
    {
        self::$libraryClass = $class !== null ? (string)$class : self::LIBRARY_CLASS;
        self::$available = null;
    }

    /**
     * The YAML of a text as PHP values.
     *
     * @param string $text
     * @param string[] $errors the problem is added here, with its line ("line 4: ...")
     * @return mixed the value; null with an error, or for an empty document
     */
    public static function parse( $text, array &$errors = array() )
    {
        if ( !self::available() )
        {
            $errors[] = (string)self::problem();
            return null;
        }
        $class = self::LIBRARY_CLASS;
        $flags = defined( $class . '::PARSE_EXCEPTION_ON_INVALID_TYPE' ) ? constant( $class . '::PARSE_EXCEPTION_ON_INVALID_TYPE' ) : 0;
        try
        {
            return call_user_func( array( $class, 'parse' ), (string)$text, $flags );
        }
        catch ( Exception $e )
        {
            $errors[] = self::message( $e );
            return null;
        }
    }

    /**
     * The message of a parse exception, the line first. Pure.
     *
     * @param Exception $e
     * @return string
     */
    public static function message( Exception $e )
    {
        $line = method_exists( $e, 'getParsedLine' ) ? (int)$e->getParsedLine() : 0;
        $message = method_exists( $e, 'getRawMessage' ) ? (string)$e->getRawMessage() : (string)$e->getMessage();
        $message = rtrim( $message, '.' );
        return 'YAML' . ( $line > 0 ? ", line $line" : '' ) . ': ' . $message;
    }

    /**
     * The version of the component when Composer knows it, else null.
     *
     * @return string|null
     */
    public static function libraryVersion()
    {
        if ( !self::available() || !class_exists( '\Composer\InstalledVersions' ) )
            return null;
        try
        {
            if ( \Composer\InstalledVersions::isInstalled( self::LIBRARY_PACKAGE ) )
                return ltrim( (string)\Composer\InstalledVersions::getPrettyVersion( self::LIBRARY_PACKAGE ), 'v' );
        }
        catch ( Exception $e )
        {
        }
        return null;
    }

    /**
     * [YamlSettings] LibraryPath, else the environment variable MIGRATION_YAML_LIBRARY_PATH; empty when neither.
     *
     * @return string
     */
    public static function libraryPath()
    {
        $path = '';
        if ( class_exists( 'eZINI' ) )
        {
            try
            {
                $ini = eZINI::instance( 'migration.ini' );
                if ( $ini->hasVariable( 'YamlSettings', 'LibraryPath' ) )
                    $path = trim( (string)$ini->variable( 'YamlSettings', 'LibraryPath' ) );
            }
            catch ( Exception $e )
            {
            }
        }
        if ( $path === '' && getenv( 'MIGRATION_YAML_LIBRARY_PATH' ) )
            $path = trim( (string)getenv( 'MIGRATION_YAML_LIBRARY_PATH' ) );
        return $path;
    }

    /**
     * @param string $path a Composer directory or the package's folder, relative to the root or absolute
     */
    private static function registerLibraryPath( $path )
    {
        if ( $path[0] !== '/' && class_exists( 'eZSys' ) )
            $path = rtrim( (string)eZSys::rootDir(), '/' ) . '/' . $path;
        $path = rtrim( $path, '/' );
        foreach ( array( $path . '/vendor/autoload.php', $path . '/autoload.php' ) as $autoload )
        {
            if ( is_file( $autoload ) && is_file( dirname( $autoload ) . '/symfony/yaml/Yaml.php' ) )
            {
                require_once $autoload;
                return;
            }
        }
        foreach ( array( $path, $path . '/symfony/yaml', $path . '/vendor/symfony/yaml' ) as $package )
        {
            if ( is_file( $package . '/Yaml.php' ) )
            {
                $src = $package . '/';
                spl_autoload_register( function ( $class ) use ( $src )
                {
                    $prefix = 'Symfony\\Component\\Yaml\\';
                    if ( strncmp( $class, $prefix, strlen( $prefix ) ) !== 0 )
                        return;
                    $file = $src . str_replace( '\\', '/', substr( $class, strlen( $prefix ) ) ) . '.php';
                    if ( is_file( $file ) )
                        require $file;
                } );
                return;
            }
        }
    }
}
