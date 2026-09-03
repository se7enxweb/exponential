<?php
/**
 * File containing the expInfo class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/*!
  \class expInfo expinfo.php
  \brief Stable, read-only API for querying eZ Publish extension metadata.

  Provides both PHP and template-level access to active and available
  extension information, parsing both extension.xml and legacy ezinfo.php
  files in a backwards-compatible way.
*/

class expInfo
{
    /**
     * In-memory cache of extension metadata, keyed by extension name.
     *
     * @var array|null
     */
    private static $infoCache = null;

    /**
     * In-memory cache of active extension names.
     *
     * @var array|null
     */
    private static $activeCache = null;

    /**
     * List of metadata fields normalised from extension.xml / ezinfo.php.
     *
     * @var array
     */
    private static $metaFields = array(
        'name', 'description', 'summary', 'version', 'copyright',
        'author', 'license', 'info_url'
    );

    /**
     * Return metadata for all active extensions.
     *
     * @return array Extension info arrays keyed by extension directory name.
     */
    public static function activeExtensions()
    {
        $result = array();
        $active = self::activeNames();
        $all = self::allInfo();

        foreach ( $active as $name )
        {
            if ( isset( $all[$name] ) )
            {
                $all[$name]['active'] = true;
                $result[$name] = $all[$name];
            }
        }

        return $result;
    }

    /**
     * Return metadata for every extension available on disk, with an
     * 'active' boolean for each entry.
     *
     * @return array Extension info arrays keyed by extension directory name.
     */
    public static function availableExtensions()
    {
        $result = array();
        $active = self::activeNames();
        $all = self::allInfo();

        foreach ( $all as $name => $info )
        {
            $info['active'] = in_array( $name, $active, true );
            $result[$name] = $info;
        }

        return $result;
    }

    /**
     * Return whether a named extension is currently active.
     *
     * @param string $name Extension directory name.
     * @return bool
     */
    public static function hasActiveExtension( $name )
    {
        return in_array( (string) $name, self::activeNames(), true );
    }

    /**
     * Return metadata for a single extension, or false if not found.
     *
     * The returned array always contains the following keys:
     * extension_name, name, description, summary, version, copyright,
     * author, license, info_url, mtime, mtime_formatted, active.
     *
     * @param string $name Extension directory name.
     * @param bool $activeOnly If true, return false for inactive extensions.
     * @return array|bool
     */
    public static function extensionInfo( $name, $activeOnly = false )
    {
        $all = self::allInfo();
        $name = (string) $name;

        if ( !isset( $all[$name] ) )
            return false;

        if ( $activeOnly && !self::hasActiveExtension( $name ) )
            return false;

        $info = $all[$name];
        $info['active'] = self::hasActiveExtension( $name );
        return $info;
    }

    /**
     * Return the list of currently active extension directory names.
     *
     * @return array
     */
    public static function activeNames()
    {
        if ( self::$activeCache === null )
        {
            self::$activeCache = eZExtension::activeExtensions();
        }
        return self::$activeCache;
    }

    /**
     * Build and cache metadata for all available extensions.
     *
     * @return array
     */
    private static function allInfo()
    {
        if ( self::$infoCache !== null )
            return self::$infoCache;

        self::$infoCache = array();
        $available = self::availableNames();

        foreach ( $available as $name )
        {
            $info = self::fetchInfo( $name );
            if ( $info !== null )
                self::$infoCache[$name] = $info;
        }

        return self::$infoCache;
    }

    /**
     * Return the list of all extension directory names found on disk.
     *
     * Later extension roots override earlier ones, matching eZ behaviour.
     *
     * @return array
     */
    private static function availableNames()
    {
        $available = array();

        foreach ( eZExtension::extensionRootDirectories() as $root )
        {
            if ( !is_dir( $root ) )
                continue;

            foreach ( eZDir::findSubItems( $root, 'dl' ) as $name )
            {
                $available[$name] = $name;
            }
        }

        return $available;
    }

    /**
     * Read and normalise extension metadata for a single extension.
     *
     * @param string $name Extension directory name.
     * @return array|null
     */
    private static function fetchInfo( $name )
    {
        $path = eZExtension::extensionPath( $name );
        if ( $path === false )
            return null;

        // Use eZExtension for mtime and composer version fallback
        $baseInfo = eZExtension::extensionInfo( $name );
        if ( !is_array( $baseInfo ) )
            $baseInfo = array();

        $info = $baseInfo;

        // Read extension.xml if present
        $xmlFile = $path . '/extension.xml';
        $xmlLoaded = false;
        if ( is_readable( $xmlFile ) )
        {
            libxml_use_internal_errors( true );
            $xml = @simplexml_load_file( $xmlFile );
            if ( $xml !== false )
            {
                $xmlLoaded = true;
                $metadata = isset( $xml->metadata ) ? $xml->metadata : $xml;
                foreach ( self::$metaFields as $field )
                {
                    if ( !isset( $metadata->$field ) )
                        continue;
                    $value = trim( (string) $metadata->$field );
                    if ( $value !== '' )
                    {
                        $info[$field] = $value;
                    }
                }
            }
        }

        // Fall back to legacy ezinfo.php
        if ( !$xmlLoaded )
        {
            $infoFile = $path . '/ezinfo.php';
            if ( is_readable( $infoFile ) )
            {
                include_once( $infoFile );
                $className = $name . 'Info';
                if ( class_exists( $className ) )
                {
                    $infoObj = new $className();
                    if ( is_callable( array( $infoObj, 'info' ) ) )
                    {
                        $ezInfo = call_user_func_array( array( $infoObj, 'info' ), array() );
                        if ( is_array( $ezInfo ) )
                        {
                            foreach ( $ezInfo as $key => $value )
                            {
                                if ( !is_string( $value ) || $value === '' )
                                    continue;
                                $lkey = strtolower( $key );
                                if ( in_array( $lkey, self::$metaFields, true ) )
                                {
                                    if ( !isset( $info[$lkey] ) || $info[$lkey] === '' || $info[$lkey] === false )
                                        $info[$lkey] = $value;
                                }
                            }
                        }
                    }
                }
            }
        }

        // Normalise summary -> description
        if ( ( !isset( $info['description'] ) || $info['description'] === '' || $info['description'] === false ) &&
             isset( $info['summary'] ) && $info['summary'] !== '' )
        {
            $info['description'] = $info['summary'];
        }

        // Strip HTML tags from text fields for safe display
        $textFields = array( 'name', 'description', 'summary', 'copyright', 'author', 'license' );
        foreach ( $textFields as $tf )
        {
            if ( isset( $info[$tf] ) && is_string( $info[$tf] ) )
                $info[$tf] = trim( strip_tags( $info[$tf] ) );
        }

        if ( isset( $info['info_url'] ) && is_string( $info['info_url'] ) )
            $info['info_url'] = trim( strip_tags( $info['info_url'] ) );

        // Normalise empty version values
        if ( isset( $info['version'] ) && ( $info['version'] === '' || $info['version'] === false ) )
            $info['version'] = false;

        // Ensure a human-readable name exists
        if ( !isset( $info['name'] ) || $info['name'] === '' )
            $info['name'] = $name;

        $info['extension_name'] = $name;

        if ( !isset( $info['mtime'] ) )
            $info['mtime'] = 0;
        if ( !isset( $info['mtime_formatted'] ) )
            $info['mtime_formatted'] = ( $info['mtime'] > 0 ) ? date( 'Y-m-d H:i', $info['mtime'] ) : false;

        // Guarantee all expected keys exist, even if empty/false
        foreach ( self::$metaFields as $field )
        {
            if ( !isset( $info[$field] ) )
                $info[$field] = false;
        }
        if ( !isset( $info['extension_name'] ) )
            $info['extension_name'] = $name;
        if ( !isset( $info['mtime'] ) )
            $info['mtime'] = 0;
        if ( !isset( $info['mtime_formatted'] ) )
            $info['mtime_formatted'] = false;

        // Build a normalised meta wrapper for easy template access
        $info['meta'] = array();
        $metaFields = array( 'description', 'copyright', 'author', 'license', 'info_url' );
        foreach ( $metaFields as $mf )
        {
            if ( isset( $info[$mf] ) && is_string( $info[$mf] ) && $info[$mf] !== '' )
                $info['meta'][$mf] = $info[$mf];
        }

        return $info;
    }

    /**
     * In-memory cache for kernelInfo() so a single request pays the cost once.
     *
     * @var array|null
     */
    private static $kernelCache = null;

    /**
     * Return a massive, read-only array describing the installation kernel.
     *
     * Includes eZ Publish SDK versions, PHP runtime, server/OS details,
     * database metadata (with secrets redacted), active/available extensions,
     * INI/design/siteaccess settings, cache state, git status, composer
     * packages, and filesystem timestamps.
     *
     * @param string|false $section If a top-level section name is supplied only
     *                              that section is returned (e.g. 'version').
     * @return array
     */
    public static function kernelInfo( $section = false )
    {
        if ( self::$kernelCache === null )
        {
            self::$kernelCache = self::buildKernelInfo();
        }

        if ( $section !== false && is_string( $section ) && isset( self::$kernelCache[$section] ) )
        {
            return self::$kernelCache[$section];
        }

        return self::$kernelCache;
    }

    /**
     * Build the kernel info array. Separated from kernelInfo() for caching.
     *
     * @return array
     */
    private static function buildKernelInfo()
    {
        $info = array();

        // --- eZ Publish SDK version (lib/version.php) ----------------------
        if ( class_exists( 'eZPublishSDK' ) )
        {
            $info['version'] = array(
                'major'          => eZPublishSDK::majorVersion(),
                'minor'          => eZPublishSDK::minorVersion(),
                'release'        => eZPublishSDK::release(),
                'state'          => eZPublishSDK::state(),
                'development'    => eZPublishSDK::developmentVersion(),
                'alias'          => eZPublishSDK::alias(),
                'edition'        => eZPublishSDK::EDITION,
                'full'           => eZPublishSDK::version( true, false, true ),
                'full_alias'     => eZPublishSDK::version( true, true, true ),
            );
        }
        else
        {
            $info['version'] = array( 'full' => 'unknown' );
        }

        // --- PHP runtime ---------------------------------------------------
        $info['php'] = array(
            'version'            => phpversion(),
            'sapi'               => php_sapi_name(),
            'os'                 => PHP_OS,
            'uname'              => php_uname(),
            'loaded_extensions'  => get_loaded_extensions(),
            'memory_limit'       => ini_get( 'memory_limit' ),
            'max_execution_time' => ini_get( 'max_execution_time' ),
            'post_max_size'      => ini_get( 'post_max_size' ),
            'upload_max_filesize'=> ini_get( 'upload_max_filesize' ),
            'max_input_vars'     => ini_get( 'max_input_vars' ),
            'default_socket_timeout' => ini_get( 'default_socket_timeout' ),
            'timezone'           => date_default_timezone_get(),
            'expose_php'         => ini_get( 'expose_php' ) == '1',
        );

        $info['memory'] = array(
            'usage'              => memory_get_usage( true ),
            'peak_usage'         => memory_get_peak_usage( true ),
        );

        // --- Server / environment ------------------------------------------
        $server = isset( $_SERVER ) ? $_SERVER : array();
        $info['server'] = array(
            'hostname'           => function_exists( 'gethostname' ) ? gethostname() : false,
            'software'           => isset( $server['SERVER_SOFTWARE'] ) ? $server['SERVER_SOFTWARE'] : false,
            'http_host'          => isset( $server['HTTP_HOST'] ) ? $server['HTTP_HOST'] : false,
            'document_root'      => isset( $server['DOCUMENT_ROOT'] ) ? $server['DOCUMENT_ROOT'] : false,
            'kernel_root'        => eZSys::rootDir(),
            'var_dir'            => eZSys::varDirectory(),
            'cache_dir'          => eZSys::cacheDirectory(),
            'site_dir'           => eZSys::siteDir(),
            'www_dir'            => eZSys::wwwDir(),
        );

        // --- Database ------------------------------------------------------
        $info['database'] = array();
        if ( class_exists( 'eZDB' ) )
        {
            $db = false;
            $dbError = null;
            try
            {
                $db = eZDB::instance();
            }
            catch ( Exception $e )
            {
                $dbError = $e->getMessage();
            }

            if ( $db !== false && is_object( $db ) )
            {
                $dbType = method_exists( $db, 'databaseName' ) ? $db->databaseName() : 'unknown';
                $dbVersion = false;

                if ( $dbType === 'mysql' || $dbType === 'mysqli' )
                {
                    $rows = $db->arrayQuery( 'SELECT VERSION() AS v' );
                    if ( is_array( $rows ) && isset( $rows[0]['v'] ) )
                        $dbVersion = $rows[0]['v'];
                }
                elseif ( $dbType === 'postgresql' )
                {
                    $rows = $db->arrayQuery( 'SELECT version() AS v' );
                    if ( is_array( $rows ) && isset( $rows[0]['v'] ) )
                        $dbVersion = $rows[0]['v'];
                }
                elseif ( $dbType === 'sqlite' || $dbType === 'sqlite3' )
                {
                    $rows = $db->arrayQuery( 'SELECT sqlite_version() AS v' );
                    if ( is_array( $rows ) && isset( $rows[0]['v'] ) )
                        $dbVersion = $rows[0]['v'];
                }

                $installVersion = false;
                $installRelease = false;
                try
                {
                    $rows = $db->arrayQuery( "SELECT `value` AS v FROM ezsite_data WHERE name='ezpublish-version'" );
                    if ( is_array( $rows ) && isset( $rows[0]['v'] ) )
                        $installVersion = $rows[0]['v'];

                    $rows = $db->arrayQuery( "SELECT `value` AS v FROM ezsite_data WHERE name='ezpublish-release'" );
                    if ( is_array( $rows ) && isset( $rows[0]['v'] ) )
                        $installRelease = $rows[0]['v'];
                }
                catch ( Exception $e )
                {
                }

                $info['database'] = array(
                    'type'            => $dbType,
                    'version'         => $dbVersion,
                    'database_name'   => $db->DB,
                    'server'          => isset( $db->Server ) ? $db->Server : false,
                    'user'            => isset( $db->User ) ? $db->User : false,
                    'password'        => '***',
                    'charset'         => isset( $db->Charset ) ? $db->Charset : false,
                    'is_connected'    => $db->isConnected(),
                    'install_version' => $installVersion,
                    'install_release' => $installRelease,
                );
            }
            else
            {
                $info['database'] = array( 'error' => $dbError );
            }
        }

        // --- eZ INI settings (redacted where needed) -----------------------
        $ini = eZINI::instance( 'site.ini' );
        $info['ini'] = array(
            'site_name'                => $ini->hasVariable( 'SiteSettings', 'SiteName' ) ? $ini->variable( 'SiteSettings', 'SiteName' ) : false,
            'site_url'                 => $ini->hasVariable( 'SiteSettings', 'SiteURL' ) ? $ini->variable( 'SiteSettings', 'SiteURL' ) : false,
            'siteaccess'               => isset( $GLOBALS['eZCurrentAccess']['name'] ) ? $GLOBALS['eZCurrentAccess']['name'] : false,
            'default_access'           => $ini->hasVariable( 'SiteAccessSettings', 'DefaultAccess' ) ? $ini->variable( 'SiteAccessSettings', 'DefaultAccess' ) : false,
            'available_siteaccess_list'=> $ini->hasVariable( 'SiteAccessSettings', 'AvailableSiteAccessList' ) ? $ini->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' ) : array(),
            'site_design'              => $ini->hasVariable( 'DesignSettings', 'SiteDesign' ) ? $ini->variable( 'DesignSettings', 'SiteDesign' ) : false,
            'additional_site_designs'  => $ini->hasVariable( 'DesignSettings', 'AdditionalSiteDesignList' ) ? $ini->variable( 'DesignSettings', 'AdditionalSiteDesignList' ) : array(),
            'locale'                   => $ini->hasVariable( 'RegionalSettings', 'Locale' ) ? $ini->variable( 'RegionalSettings', 'Locale' ) : false,
            'site_languages'           => $ini->hasVariable( 'RegionalSettings', 'SiteLanguageList' ) ? $ini->variable( 'RegionalSettings', 'SiteLanguageList' ) : array(),
            'text_translation'         => $ini->hasVariable( 'RegionalSettings', 'TextTranslation' ) ? $ini->variable( 'RegionalSettings', 'TextTranslation' ) : false,
            'database'                 => array(
                'type'     => $ini->hasVariable( 'DatabaseSettings', 'Type' ) ? $ini->variable( 'DatabaseSettings', 'Type' ) : false,
                'server'   => $ini->hasVariable( 'DatabaseSettings', 'Server' ) ? $ini->variable( 'DatabaseSettings', 'Server' ) : false,
                'user'     => $ini->hasVariable( 'DatabaseSettings', 'User' ) ? $ini->variable( 'DatabaseSettings', 'User' ) : false,
                'password' => '***',
                'database' => $ini->hasVariable( 'DatabaseSettings', 'Database' ) ? $ini->variable( 'DatabaseSettings', 'Database' ) : false,
                'charset'  => $ini->hasVariable( 'DatabaseSettings', 'Charset' ) ? $ini->variable( 'DatabaseSettings', 'Charset' ) : false,
            ),
            'file_settings'            => array(
                'var_dir'    => $ini->hasVariable( 'FileSettings', 'VarDir' ) ? $ini->variable( 'FileSettings', 'VarDir' ) : false,
                'cache_dir'  => $ini->hasVariable( 'FileSettings', 'CacheDir' ) ? $ini->variable( 'FileSettings', 'CacheDir' ) : false,
                'storage_dir'=> $ini->hasVariable( 'FileSettings', 'StorageDir' ) ? $ini->variable( 'FileSettings', 'StorageDir' ) : false,
            ),
        );

        // --- Current user --------------------------------------------------
        $info['user'] = array( 'id' => false, 'login' => false );
        if ( class_exists( 'eZUser' ) )
        {
            $currentUser = eZUser::currentUser();
            if ( is_object( $currentUser ) )
            {
                $info['user']['id'] = $currentUser->attribute( 'contentobject_id' );
                $info['user']['login'] = $currentUser->attribute( 'login' );
            }
        }

        // --- Extensions summary --------------------------------------------
        $info['extensions'] = array(
            'active_count'   => count( self::activeNames() ),
            'available_count'=> count( self::availableNames() ),
            'active'         => array_values( self::activeNames() ),
            'available'      => array_values( self::availableNames() ),
        );

        // --- Cache / filesystem state --------------------------------------
        $info['cache'] = array(
            'dir'            => eZSys::cacheDirectory(),
            'size_bytes'     => self::directorySize( eZSys::cacheDirectory() ),
            'compiled_template_count' => self::countCompiledTemplates(),
        );

        $info['filesystem'] = array(
            'root_size_bytes' => self::directorySize( eZSys::rootDir() ),
            'var_size_bytes'  => self::directorySize( eZSys::varDirectory() ),
            'extension_size_bytes' => self::directorySize( eZSys::rootDir() . '/extension' ),
            'free_bytes'      => @disk_free_space( eZSys::rootDir() ),
            'total_bytes'     => @disk_total_space( eZSys::rootDir() ),
        );

        // --- Timestamps of key files ---------------------------------------
        $info['timestamps'] = array(
            'lib_version_php'  => file_exists( eZSys::rootDir() . '/lib/version.php' ) ? filemtime( eZSys::rootDir() . '/lib/version.php' ) : false,
            'index_php'        => file_exists( eZSys::rootDir() . '/index.php' ) ? filemtime( eZSys::rootDir() . '/index.php' ) : false,
            'composer_lock'    => file_exists( eZSys::rootDir() . '/composer.lock' ) ? filemtime( eZSys::rootDir() . '/composer.lock' ) : false,
            'autoload_kernel'  => file_exists( eZSys::rootDir() . '/autoload/ezp_kernel.php' ) ? filemtime( eZSys::rootDir() . '/autoload/ezp_kernel.php' ) : false,
            'now'              => time(),
        );

        // --- Git status ----------------------------------------------------
        $info['git'] = self::gitInfo();

        // --- Composer packages ---------------------------------------------
        $info['composer'] = self::composerInfo();

        return $info;
    }

    /**
     * Return size of a directory in bytes using du, falling back to false.
     *
     * @param string $dir
     * @return int|false
     */
    private static function directorySize( $dir )
    {
        if ( !is_dir( $dir ) || !is_readable( $dir ) )
            return false;

        $out = array();
        $ret = 0;
        @exec( 'du -sb ' . escapeshellarg( $dir ) . ' 2>/dev/null', $out, $ret );

        if ( $ret === 0 && isset( $out[0] ) )
        {
            $parts = explode( "\t", $out[0] );
            return (int) $parts[0];
        }

        return false;
    }

    /**
     * Count compiled PHP templates in the cache directory.
     *
     * @return int|false
     */
    private static function countCompiledTemplates()
    {
        $cacheDir = eZSys::cacheDirectory();
        if ( !is_dir( $cacheDir ) )
            return false;

        $out = array();
        $ret = 0;
        @exec( 'find ' . escapeshellarg( $cacheDir ) . ' -type f -name "*.php" 2>/dev/null | wc -l', $out, $ret );

        if ( $ret === 0 && isset( $out[0] ) )
            return (int) trim( $out[0] );

        return false;
    }

    /**
     * Return git repository metadata, or false values if git is not usable.
     *
     * @return array
     */
    private static function gitInfo()
    {
        $root = eZSys::rootDir();
        $gitDir = $root . '/.git';

        if ( !is_dir( $gitDir ) )
            return array( 'branch' => false, 'last_commit' => false, 'last_commit_date' => false, 'dirty_count' => false );

        $info = array( 'branch' => false, 'last_commit' => false, 'last_commit_date' => false, 'dirty_count' => false );

        $out = array();
        $ret = 0;
        @exec( 'cd ' . escapeshellarg( $root ) . ' && git rev-parse --abbrev-ref HEAD 2>/dev/null', $out, $ret );
        if ( $ret === 0 && isset( $out[0] ) )
            $info['branch'] = $out[0];

        $out = array();
        $ret = 0;
        @exec( 'cd ' . escapeshellarg( $root ) . ' && git log -1 --format="%H%x00%ci" 2>/dev/null', $out, $ret );
        if ( $ret === 0 && isset( $out[0] ) )
        {
            $parts = explode( "\x00", $out[0] );
            if ( isset( $parts[0] ) )
                $info['last_commit'] = $parts[0];
            if ( isset( $parts[1] ) )
                $info['last_commit_date'] = trim( $parts[1] );
        }

        $out = array();
        $ret = 0;
        @exec( 'cd ' . escapeshellarg( $root ) . ' && git status --porcelain 2>/dev/null | wc -l', $out, $ret );
        if ( $ret === 0 && isset( $out[0] ) )
            $info['dirty_count'] = (int) trim( $out[0] );

        return $info;
    }

    /**
     * Return composer installed package summary.
     *
     * @return array
     */
    private static function composerInfo()
    {
        $installedFile = eZSys::rootDir() . '/vendor/composer/installed.php';
        if ( !is_readable( $installedFile ) )
            return array( 'root' => false, 'packages' => array(), 'count' => 0 );

        $installed = include $installedFile;
        if ( !is_array( $installed ) || !isset( $installed['versions'] ) )
            return array( 'root' => false, 'packages' => array(), 'count' => 0 );

        $packages = array();
        foreach ( $installed['versions'] as $name => $meta )
        {
            $packages[$name] = array(
                'version'       => isset( $meta['pretty_version'] ) ? $meta['pretty_version'] : ( isset( $meta['version'] ) ? $meta['version'] : 'unknown' ),
                'reference'     => isset( $meta['reference'] ) ? $meta['reference'] : false,
                'type'          => isset( $meta['type'] ) ? $meta['type'] : false,
                'dev'           => isset( $meta['dev_requirement'] ) ? (bool) $meta['dev_requirement'] : false,
            );
        }

        return array(
            'root'     => isset( $installed['root'] ) ? $installed['root'] : false,
            'packages' => $packages,
            'count'    => count( $packages ),
        );
    }
}

?>