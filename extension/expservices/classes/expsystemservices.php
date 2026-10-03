<?php
/**
 * expsystem: what the installation is and runs on.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expSystemServices extends expServiceBase
{
    public static $services = array(
        'version' => array( 'summary' => 'The Exponential version, release, state and the PHP version',
            'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'version, major, minor, release, state, php' ),
        'info' => array( 'summary' => 'Site name, URLs, siteaccess, database type, locale',
            'access' => array( 'setup', 'system_info' ), 'write' => false, 'args' => array(), 'returns' => 'installation overview' ),
        'settings' => array( 'summary' => 'The public site settings: site name, default access, locale, design',
            'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'site_name, site_url, default_access, locale, design' ),
        'time' => array( 'summary' => 'The server time, time zone and the time of the request',
            'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'timestamp, iso, timezone' ),
        'php' => array( 'summary' => 'PHP version, SAPI, limits and OPcache state',
            'access' => array( 'setup', 'system_info' ), 'write' => false, 'args' => array(), 'returns' => 'version, sapi, memory_limit, max_execution_time, opcache' ),
        'phpextensions' => array( 'summary' => 'The loaded PHP extensions with their versions',
            'access' => array( 'setup', 'system_info' ), 'write' => false, 'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of name, version' ),
        'database' => array( 'summary' => 'The database type, name, server version and connection state (no credentials)',
            'access' => array( 'setup', 'system_info' ), 'write' => false, 'args' => array(), 'returns' => 'type, name, version, connected' ),
        'siteaccesses' => array( 'summary' => 'The available siteaccesses',
            'access' => array( 'setup', 'system_info' ), 'write' => false, 'args' => array(), 'returns' => 'list of siteaccess names' ),
        'siteaccess' => array( 'summary' => 'The siteaccess of this request',
            'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'name, type' ),
        'languages' => array( 'summary' => 'The content languages of the installation',
            'access' => 'user', 'write' => false, 'args' => array(), 'returns' => 'list of id, locale, name, disabled' ),
        'locales' => array( 'summary' => 'The locales the installation knows',
            'access' => 'user', 'write' => false, 'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of locale codes' ),
        'health' => array( 'summary' => 'Quick checks: database connection, var and cache directories writable',
            'access' => array( 'setup', 'system_info' ), 'write' => false, 'args' => array(), 'returns' => 'ok and a list of checks' ),
        'directories' => array( 'summary' => 'The var, storage and cache directories (relative to the installation) and whether they are writable',
            'access' => array( 'setup', 'system_info' ), 'write' => false, 'args' => array(), 'returns' => 'list of name, path, writable' ),
        'statistics' => array( 'summary' => 'Counts: content objects, nodes, classes, users, sessions',
            'access' => array( 'setup', 'system_info' ), 'write' => false, 'args' => array(), 'returns' => 'object counts' ),
        'load' => array( 'summary' => 'The server load average and the PHP memory use of this request',
            'access' => array( 'setup', 'system_info' ), 'write' => false, 'args' => array(), 'returns' => 'loadavg, memory' ),
        'urls' => array( 'summary' => 'Host, root URL, index file and server URL of this request',
            'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'host, www_dir, index_file, server_url' ),
    );

    protected static function rel( $path )
    {
        $root = rtrim( eZSys::rootDir(), '/' ) . '/';
        return strpos( $path, $root ) === 0 ? substr( $path, strlen( $root ) ) : $path;
    }

    public static function version( $args )
    {
        self::guard( __FUNCTION__ );
        return self::ok( array(
            'version' => eZPublishSDK::version(), 'major' => eZPublishSDK::majorVersion(), 'minor' => eZPublishSDK::minorVersion(),
            'release' => eZPublishSDK::release(), 'state' => eZPublishSDK::state(), 'php' => PHP_VERSION ) );
    }

    public static function info( $args )
    {
        self::guard( __FUNCTION__ );
        $ini = eZINI::instance();
        $db = eZDB::instance();
        return self::ok( array(
            'site_name' => $ini->variable( 'SiteSettings', 'SiteName' ),
            'site_url' => $ini->variable( 'SiteSettings', 'SiteURL' ),
            'version' => eZPublishSDK::version(),
            'php' => PHP_VERSION,
            'database' => $db->databaseName(),
            'database_type' => $ini->variable( 'DatabaseSettings', 'DatabaseImplementation' ),
            'siteaccess' => isset( $GLOBALS['eZCurrentAccess']['name'] ) ? $GLOBALS['eZCurrentAccess']['name'] : null,
            'locale' => eZLocale::currentLocaleCode(),
            'extensions' => count( eZExtension::activeExtensions() ),
        ) );
    }

    public static function settings( $args )
    {
        self::guard( __FUNCTION__ );
        $ini = eZINI::instance();
        return self::ok( array(
            'site_name' => $ini->variable( 'SiteSettings', 'SiteName' ),
            'site_url' => $ini->variable( 'SiteSettings', 'SiteURL' ),
            'default_access' => $ini->variable( 'SiteSettings', 'DefaultAccess' ),
            'locale' => eZLocale::currentLocaleCode(),
            'design' => $ini->variable( 'DesignSettings', 'SiteDesign' ),
        ) );
    }

    public static function time( $args )
    {
        self::guard( __FUNCTION__ );
        return self::ok( array( 'timestamp' => time(), 'iso' => gmdate( 'c' ), 'timezone' => date_default_timezone_get() ) );
    }

    public static function php( $args )
    {
        self::guard( __FUNCTION__ );
        $opcache = function_exists( 'opcache_get_status' ) ? @opcache_get_status( false ) : false;
        return self::ok( array(
            'version' => PHP_VERSION, 'sapi' => PHP_SAPI, 'os' => PHP_OS,
            'memory_limit' => ini_get( 'memory_limit' ), 'max_execution_time' => (int)ini_get( 'max_execution_time' ),
            'upload_max_filesize' => ini_get( 'upload_max_filesize' ), 'post_max_size' => ini_get( 'post_max_size' ),
            'opcache' => is_array( $opcache ) ? array( 'enabled' => !empty( $opcache['opcache_enabled'] ),
                                                       'cached_scripts' => isset( $opcache['opcache_statistics']['num_cached_scripts'] ) ? (int)$opcache['opcache_statistics']['num_cached_scripts'] : null ) : null,
        ) );
    }

    public static function phpextensions( $args )
    {
        self::guard( __FUNCTION__ );
        $list = array();
        $names = get_loaded_extensions();
        sort( $names, SORT_NATURAL | SORT_FLAG_CASE );
        foreach ( $names as $name )
            $list[] = array( 'name' => $name, 'version' => phpversion( $name ) ?: null );
        return self::pageOf( $list, $args, 0, 1 );
    }

    public static function database( $args )
    {
        self::guard( __FUNCTION__ );
        $db = eZDB::instance();
        return self::ok( array(
            'type' => eZINI::instance()->variable( 'DatabaseSettings', 'DatabaseImplementation' ),
            'name' => $db->databaseName(), 'version' => $db->databaseServerVersion(), 'connected' => (bool)$db->isConnected() ) );
    }

    public static function siteaccesses( $args )
    {
        self::guard( __FUNCTION__ );
        $list = array_values( eZINI::instance()->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' ) );
        return self::ok( $list, array( 'total' => count( $list ) ) );
    }

    public static function siteaccess( $args )
    {
        self::guard( __FUNCTION__ );
        $a = isset( $GLOBALS['eZCurrentAccess'] ) ? $GLOBALS['eZCurrentAccess'] : array();
        return self::ok( array( 'name' => isset( $a['name'] ) ? $a['name'] : null, 'type' => isset( $a['type'] ) ? (int)$a['type'] : null ) );
    }

    public static function languages( $args )
    {
        self::guard( __FUNCTION__ );
        $list = array();
        foreach ( (array)eZContentLanguage::fetchList() as $l )
            $list[] = array( 'id' => (int)$l->attribute( 'id' ), 'locale' => $l->attribute( 'locale' ),
                             'name' => $l->attribute( 'name' ), 'disabled' => (bool)$l->attribute( 'disabled' ) );
        return self::ok( $list, array( 'total' => count( $list ) ) );
    }

    public static function locales( $args )
    {
        self::guard( __FUNCTION__ );
        $all = array_values( (array)eZLocale::localeList( false, false ) );
        sort( $all );
        return self::pageOf( $all, $args, 0, 1 );
    }

    public static function health( $args )
    {
        self::guard( __FUNCTION__ );
        $checks = array();
        $checks[] = array( 'name' => 'database', 'ok' => (bool)eZDB::instance()->isConnected() );
        foreach ( array( 'var' => eZSys::varDirectory(), 'cache' => eZSys::cacheDirectory(), 'storage' => eZSys::storageDirectory() ) as $name => $dir )
            $checks[] = array( 'name' => $name . ' directory writable', 'ok' => is_writable( $dir ) );
        $ok = true;
        foreach ( $checks as $c )
            $ok = $ok && $c['ok'];
        return self::ok( array( 'ok' => $ok, 'checks' => $checks ) );
    }

    public static function directories( $args )
    {
        self::guard( __FUNCTION__ );
        $list = array();
        foreach ( array( 'var' => eZSys::varDirectory(), 'storage' => eZSys::storageDirectory(), 'cache' => eZSys::cacheDirectory() ) as $name => $dir )
            $list[] = array( 'name' => $name, 'path' => self::rel( $dir ), 'writable' => is_writable( $dir ) );
        return self::ok( $list, array( 'total' => count( $list ) ) );
    }

    public static function statistics( $args )
    {
        self::guard( __FUNCTION__ );
        $db = eZDB::instance();
        $count = function ( $sql ) use ( $db ) {
            $r = $db->arrayQuery( $sql );
            return $r ? (int)$r[0]['n'] : 0;
        };
        return self::ok( array(
            'objects' => $count( 'SELECT COUNT(*) AS n FROM ezcontentobject' ),
            'published_objects' => $count( 'SELECT COUNT(*) AS n FROM ezcontentobject WHERE status = 1' ),
            'nodes' => $count( 'SELECT COUNT(*) AS n FROM ezcontentobject_tree' ),
            'classes' => $count( 'SELECT COUNT(*) AS n FROM ezcontentclass WHERE version = 0' ),
            'users' => $count( 'SELECT COUNT(*) AS n FROM ezuser' ),
            'sessions' => $count( 'SELECT COUNT(*) AS n FROM ezsession' ) ) );
    }

    public static function load( $args )
    {
        self::guard( __FUNCTION__ );
        $load = function_exists( 'sys_getloadavg' ) ? sys_getloadavg() : false;
        return self::ok( array( 'loadavg' => $load ?: null, 'memory' => array( 'used' => memory_get_usage( true ), 'peak' => memory_get_peak_usage( true ) ) ) );
    }

    public static function urls( $args )
    {
        self::guard( __FUNCTION__ );
        return self::ok( array( 'host' => eZSys::hostname(), 'www_dir' => eZSys::wwwDir(), 'index_file' => eZSys::indexFile( false ), 'server_url' => eZSys::serverURL() ) );
    }
}
