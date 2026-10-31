<?php
/**
 * File containing the expMigrationLocator class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Finds the migration files of an installation.
 *
 *   migration.ini [MigrationSettings] DirectoryList[]     the site's directories (source site), default migrations
 *   extension/<name>/<ExtensionDirectory>/                 every active extension's (source extension:<name>)
 *   var/<storage>/packages/<repository>/<package>/<ExtensionDirectory>/
 *                                                          every installed package's (source package:<name>)
 *
 * Only the files directly in a directory are migrations; subdirectories (status files, logs) are not searched. A file
 * whose name does not start with YYYYMMDDHHMMSS_ (a README, a .gitkeep) is not a migration and is skipped; one that
 * does but is not a valid migration name is reported in errors(). With [MigrationSettings] Enabled=disabled nothing
 * is found.
 *
 * Duplicate ids and the order are the graph's business (expMigrationGraph); the locator returns the definitions in
 * the order of the directories and, within one, by file name.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationLocator
{
    /** @var array[] array( 'source' => ..., 'path' => ... ) */
    protected $directories = array();

    /** @var string[] */
    protected $errors = array();

    /**
     * @param array[] $directories each array( 'source' => kernel|site|extension:<name>|package:<name>, 'path' => dir )
     */
    public function __construct( array $directories )
    {
        foreach ( $directories as $directory )
            if ( isset( $directory['source'], $directory['path'] ) )
                $this->directories[] = array( 'source' => (string)$directory['source'], 'path' => rtrim( (string)$directory['path'], '/' ) );
    }

    /**
     * The locator of the installation's settings.
     *
     * @param eZINI|null $ini migration.ini
     * @param eZINI|null $siteINI site.ini, for the active extensions
     * @return expMigrationLocator
     */
    public static function fromSettings( $ini = null, $siteINI = null )
    {
        $ini = $ini ?: eZINI::instance( 'migration.ini' );
        return new self( self::directories( $ini, $siteINI ) );
    }

    /**
     * The directories the settings name, existing or not.
     *
     * @param eZINI $ini migration.ini
     * @param eZINI|null $siteINI
     * @return array[]
     */
    public static function directories( $ini, $siteINI = null )
    {
        $settings = self::settings( $ini );
        if ( !$settings['enabled'] )
            return array();
        $repositories = array();
        foreach ( eZPackage::packageRepositories() as $repository )
            $repositories[] = $repository['path'];
        return self::layout( $settings, eZSys::rootDir(), eZExtension::baseDirectory( $siteINI ),
                             eZExtension::activeExtensions( false, $siteINI ), $repositories );
    }

    /**
     * [MigrationSettings] of migration.ini, with the defaults of a missing setting.
     *
     * @param eZINI $ini
     * @return array 'enabled' => bool, 'directories' => string[], 'extension_directory' => string
     */
    public static function settings( $ini )
    {
        $get = function ( $name, $default ) use ( $ini ) {
            return $ini->hasVariable( 'MigrationSettings', $name ) ? $ini->variable( 'MigrationSettings', $name ) : $default;
        };
        $directories = array();
        foreach ( (array)$get( 'DirectoryList', array( 'migrations' ) ) as $dir )
            if ( ( $dir = trim( (string)$dir ) ) !== '' )
                $directories[] = $dir;
        $sub = trim( (string)$get( 'ExtensionDirectory', 'migrations' ), '/ ' );
        return array(
            'enabled' => $get( 'Enabled', 'enabled' ) === 'enabled',
            'directories' => $directories,
            'extension_directory' => $sub === '' ? 'migrations' : $sub,
        );
    }

    /**
     * The directories to search. Pure apart from looking which package directories exist.
     *
     * @param array $settings as settings() returns them
     * @param string $root the installation's root
     * @param string $extensionBase the extension directory, relative to the root or absolute
     * @param string[] $extensions the active extensions
     * @param string[] $repositories the package repositories' paths, relative to the root or absolute
     * @return array[]
     */
    public static function layout( array $settings, $root, $extensionBase, array $extensions, array $repositories )
    {
        $root = rtrim( (string)$root, '/' );
        $absolute = function ( $path ) use ( $root ) {
            $path = rtrim( (string)$path, '/' );
            return $path !== '' && $path[0] === '/' ? $path : "$root/$path";
        };
        $sub = $settings['extension_directory'];

        $directories = array();
        foreach ( $settings['directories'] as $dir )
            $directories[] = array( 'source' => 'site', 'path' => $absolute( $dir ) );

        $base = $absolute( $extensionBase );
        foreach ( $extensions as $extension )
            $directories[] = array( 'source' => "extension:$extension", 'path' => "$base/$extension/$sub" );

        foreach ( self::packageDirectories( array_map( $absolute, $repositories ), $sub ) as $directory )
            $directories[] = $directory;
        return $directories;
    }

    /**
     * The migration directories of the installed packages (the ones that have one; the first repository that has a
     * package of a name counts).
     *
     * @param string[] $repositories
     * @param string $sub
     * @return array[]
     */
    protected static function packageDirectories( array $repositories, $sub )
    {
        $directories = array();
        $seen = array();
        foreach ( $repositories as $path )
        {
            if ( !is_dir( $path ) )
                continue;
            foreach ( (array)scandir( $path ) as $package )
            {
                if ( $package === '' || $package[0] === '.' || isset( $seen[$package] ) || !is_dir( "$path/$package/$sub" ) )
                    continue;
                $seen[$package] = true;
                $directories[] = array( 'source' => "package:$package", 'path' => "$path/$package/$sub" );
            }
        }
        return $directories;
    }

    /** @return array[] */
    public function searchedDirectories()
    {
        return $this->directories;
    }

    /**
     * Every migration found, in the order of the directories, then by file name. Files of one id in one directory
     * that differ only in the engine are one definition.
     *
     * @return expMigrationDefinition[]
     */
    public function find()
    {
        $this->errors = array();
        $definitions = array();
        foreach ( $this->directories as $directory )
        {
            $path = $directory['path'];
            if ( !is_dir( $path ) )
                continue;
            $names = scandir( $path );
            if ( $names === false )
            {
                $this->errors[] = "$path cannot be read";
                continue;
            }
            $here = array();
            foreach ( $names as $basename )
            {
                $parts = expMigrationDefinition::parseName( $basename );
                if ( $parts === null || !is_file( "$path/$basename" ) )
                    continue;
                if ( isset( $parts['error'] ) )
                {
                    $this->errors[] = "$path/" . $parts['error'];
                    continue;
                }
                $id = $parts['id'];
                if ( !isset( $here[$id] ) )
                {
                    $here[$id] = new expMigrationDefinition( $id, $directory['source'], $parts['type'] );
                }
                else if ( $here[$id]->type() !== $parts['type'] )
                {
                    $this->errors[] = "$path/$basename: the migration $id already has a ." . $here[$id]->type() . ' file; one migration has one type';
                    continue;
                }
                $here[$id]->addFile( "$path/$basename", $parts['engine'] );
            }
            foreach ( $here as $definition )
            {
                foreach ( $definition->errors() as $error )
                    $this->errors[] = $error;
                $definitions[] = $definition;
            }
        }
        return $definitions;
    }

    /** @return string[] problems of the last find() */
    public function errors()
    {
        return $this->errors;
    }
}
