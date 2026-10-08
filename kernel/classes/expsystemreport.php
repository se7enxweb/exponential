<?php
/**
 * File containing the expSystemReport class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The facts of Setup > System information and of exp:system:info, in sections, with health checks.
 *
 * Two halves:
 *
 *  - collect() reads the facts of the process that runs it: the server that answered (Apache with PHP-FPM, Exponential
 *    Velocity, FrankenPHP, PHP's built-in server or the command line), PHP and its limits, OPcache and APCu as this
 *    process sees them, the database, storage, caches, cronjobs, mail, locale, debug settings and the extensions.
 *    It only reads: nothing is written, cleared or started. Sizes of directories and of a database on a server are
 *    only measured when asked for (option 'sizes'), within a time budget.
 *  - Everything else works on that array of facts and is pure: cards() for the page, checks() for the health
 *    checks with what to do, toArray() and toText() for the report. The tests build facts by hand
 *    (tests/tests/kernel/classes/expSystemReportTest.php), so no database is needed.
 *
 * Every value that leaves the class has passed expSystemReportMask: no password, key, token, session id or DSN
 * with credentials, and paths relative to the installation.
 */
class expSystemReport
{
    const OK = 'ok';
    const WARN = 'warn';
    const FAIL = 'fail';
    const INFO = 'info';

    /** PHP extensions the kernel cannot run without */
    const REQUIRED_EXTENSIONS = 'ctype,dom,iconv,json,libxml,mbstring,pcre,session,simplexml,spl,xml,zlib';
    /** PHP extensions a site normally needs (images, http, intl, the caches) */
    const RECOMMENDED_EXTENSIONS = 'curl,fileinfo,intl,xsl,Zend OPcache,apcu';

    /** The end of security support of each PHP branch (php.net/supported-versions) */
    protected static $phpSupportEnds = array(
        '7.4' => '2022-11-28', '8.0' => '2023-11-26', '8.1' => '2025-12-31', '8.2' => '2026-12-31',
        '8.3' => '2027-12-31', '8.4' => '2028-12-31', '8.5' => '2029-12-31',
    );

    /** The PHP extension each database engine needs */
    protected static $databaseExtensions = array(
        'mysql' => 'mysqli', 'mysqli' => 'mysqli', 'postgresql' => 'pgsql', 'pgsql' => 'pgsql', 'sqlite' => 'sqlite3',
        'sqlite3' => 'sqlite3', 'oracle' => 'oci8', 'mongodb' => 'mongodb',
    );

    /** @var array */
    protected $facts;
    /** @var bool whether texts go through ezpI18n */
    protected $translate;
    /** @var array|null */
    protected $checks = null;

    /**
     * @param array $facts what collect() returns, or the same shape built by hand
     * @param array $options 'translate' => bool (default false)
     */
    public function __construct( array $facts, array $options = array() )
    {
        $this->facts = $facts + array( 'root' => '', 'time' => time() );
        $this->translate = !empty( $options['translate'] ) && class_exists( 'ezpI18n' );
    }

    /**
     * The report of this process.
     *
     * @param array $options 'sizes' => bool, 'database' => bool (default true), 'translate' => bool
     * @return expSystemReport
     */
    public static function gather( array $options = array() )
    {
        return new self( self::collect( $options ), $options );
    }

    // ── Collecting ──────────────────────────────────────────────────────────────────────────────────────────────

    /**
     * Reads the facts of this process. Each part is guarded: one that fails is recorded under 'errors' and the
     * others are still read.
     *
     * @param array $options
     * @return array
     */
    public static function collect( array $options = array() )
    {
        $options += array( 'sizes' => false, 'database' => true, 'budget' => 3.0 );
        $root = class_exists( 'eZSys' ) ? rtrim( (string)eZSys::rootDir(), '/' ) : rtrim( (string)getcwd(), '/' );
        $facts = array( 'root' => $root, 'time' => time(), 'sizes' => (bool)$options['sizes'], 'errors' => array(), 'ms' => array() );
        $deadline = microtime( true ) + (float)$options['budget'];
        $parts = array(
            'exponential' => function () { return self::collectExponential(); },
            'site'        => function () { return self::collectSite(); },
            'server'      => function () { return self::collectServer(); },
            'php'         => function () { return self::collectPhp(); },
            'opcache'     => function () { return self::collectOpcache(); },
            'apcu'        => function () { return self::collectApcu(); },
            'database'    => function () use ( $options ) { return $options['database'] ? self::collectDatabase( $options['sizes'] ) : array( 'skipped' => true ); },
            'storage'     => function () use ( $options, $root, $deadline ) { return self::collectStorage( $root, $options['sizes'], $deadline ); },
            'caches'      => function () use ( $options, $deadline ) { return self::collectCaches( $options['sizes'], $deadline ); },
            'cronjobs'    => function () { return self::collectCronjobs(); },
            'mail'        => function () { return self::collectMail(); },
            'locale'      => function () { return self::collectLocale(); },
            'debug'       => function () { return self::collectDebug(); },
            'extensions'  => function () { return self::collectExtensions(); },
            'host'        => function () { return self::collectHost(); },
        );
        foreach ( $parts as $name => $read )
        {
            $start = microtime( true );
            try
            {
                $facts[$name] = $read();
            }
            catch ( Throwable $e )
            {
                $facts[$name] = array();
                $facts['errors'][$name] = get_class( $e ) . ': ' . $e->getMessage();
            }
            $facts['ms'][$name] = round( ( microtime( true ) - $start ) * 1000, 1 );
        }
        return $facts;
    }

    protected static function ini( $block, $name, $default = null, $file = 'site.ini' )
    {
        if ( !class_exists( 'eZINI' ) )
            return $default;
        $ini = eZINI::instance( $file );
        return $ini->hasVariable( $block, $name ) ? $ini->variable( $block, $name ) : $default;
    }

    protected static function enabled( $value )
    {
        return in_array( strtolower( (string)$value ), array( 'enabled', 'true', '1', 'on', 'yes' ), true );
    }

    protected static function collectExponential()
    {
        $facts = array( 'name' => 'Exponential', 'version' => '', 'state' => '', 'alias' => '', 'schema' => '', 'build' => '' );
        if ( class_exists( 'ExponentialSDK' ) )
        {
            $facts['version'] = ExponentialSDK::version( true, false, false );
            $facts['state'] = (string)ExponentialSDK::state();
            $facts['alias'] = (string)ExponentialSDK::alias();
        }
        // The version the database was installed or upgraded to (ezsite_data), when there is a database
        if ( class_exists( 'eZDB' ) && eZDB::hasInstance() && eZDB::instance()->isConnected() )
        {
            try
            {
                $rows = @eZDB::instance()->arrayQuery( "SELECT value FROM ezsite_data WHERE name='ezpublish-version'" );
                if ( is_array( $rows ) && isset( $rows[0]['value'] ) )
                    $facts['schema'] = (string)$rows[0]['value'];
            }
            catch ( Throwable $e )
            {
                // a database without ezsite_data (MongoDB) has no schema version to show
            }
        }
        // The engine archive's build, when the engine runs from one
        if ( defined( 'EXP_ENGINE_PHAR' ) )
        {
            $build = @file_get_contents( 'phar://' . EXP_ENGINE_PHAR . '/ENGINE_VERSION' );
            if ( $build !== false )
                $facts['build'] = trim( $build );
        }
        return $facts;
    }

    protected static function collectSite()
    {
        $facts = array( 'siteaccess' => '', 'public_siteaccess' => '', 'site_url' => '', 'site_url_placeholder' => false );
        if ( class_exists( 'eZSiteAccess' ) )
        {
            $current = eZSiteAccess::current();
            $facts['siteaccess'] = isset( $current['name'] ) ? (string)$current['name'] : '';
        }
        $facts['public_siteaccess'] = (string)self::ini( 'SiteSettings', 'DefaultAccess', '' );
        $facts['site_url'] = trim( (string)self::ini( 'SiteSettings', 'SiteURL', '' ) );
        $host = strtolower( preg_replace( '#^[a-z][a-z0-9+.-]*://#i', '', $facts['site_url'] ) );
        $host = preg_replace( '#[:/].*$#', '', $host );
        $facts['site_url_placeholder'] = $host === '' || in_array( $host, array( 'localhost', '127.0.0.1', '[::1]', '::1' ), true );
        return $facts;
    }

    protected static function collectServer()
    {
        $software = isset( $_SERVER['SERVER_SOFTWARE'] ) ? trim( (string)$_SERVER['SERVER_SOFTWARE'] ) : '';
        $facts = array(
            'kind' => self::serverKind( PHP_SAPI, $software, defined( 'QBIX_SERVER_VERSION' ) ),
            'sapi' => PHP_SAPI,
            'software' => $software,
            'name' => '',
            'version' => '',
            'tls' => class_exists( 'eZSys' ) ? (bool)eZSys::isSSLNow() : !empty( $_SERVER['HTTPS'] ),
            'port' => 0,
            'workers' => array(),
        );
        $host = isset( $_SERVER['HTTP_HOST'] ) ? (string)$_SERVER['HTTP_HOST'] : '';
        $facts['port'] = preg_match( '/:(\d+)$/', $host, $m ) ? (int)$m[1]
                       : ( isset( $_SERVER['SERVER_PORT'] ) ? (int)$_SERVER['SERVER_PORT'] : 0 );
        $slash = strpos( $software, '/' );
        $facts['name'] = $slash === false ? $software : substr( $software, 0, $slash );
        $facts['version'] = $slash === false ? '' : substr( $software, $slash + 1 );

        switch ( $facts['kind'] )
        {
            case 'velocity':
                $facts['name'] = 'Exponential Velocity';
                $facts['version'] = self::velocityVersion();
                $fork = class_exists( 'Q_Config', false ) ? (bool)Q_Config::get( 'Q', 'webserver', 'forkPerRequest', false )
                      : self::enabled( self::ini( 'ServerSettings', 'ForkPerRequest', 'disabled', 'velocity.ini' ) );
                $facts['workers'] = array(
                    'model' => $fork ? 'fork-per-request' : 'persistent',
                    'configured' => (int)self::ini( 'ServerSettings', 'Workers', 0, 'velocity.ini' ),
                    'spare' => (int)self::ini( 'ServerSettings', 'SpareWorkers', 0, 'velocity.ini' ),
                    'archive' => defined( 'EXP_ENGINE_PHAR' ),
                    'pid' => function_exists( 'getmypid' ) ? (int)getmypid() : 0,
                );
                break;
            case 'apache-fpm':
            case 'nginx-fpm':
            case 'fpm':
                $facts['workers'] = array( 'model' => 'process-per-request' );
                if ( function_exists( 'fpm_get_status' ) )
                {
                    $status = @fpm_get_status();
                    if ( is_array( $status ) )
                    {
                        $facts['workers'] += array(
                            'pool' => isset( $status['pool'] ) ? (string)$status['pool'] : '',
                            'manager' => isset( $status['process-manager'] ) ? (string)$status['process-manager'] : '',
                            'active' => isset( $status['active-processes'] ) ? (int)$status['active-processes'] : 0,
                            'idle' => isset( $status['idle-processes'] ) ? (int)$status['idle-processes'] : 0,
                            'total' => isset( $status['total-processes'] ) ? (int)$status['total-processes'] : 0,
                            'max_children_reached' => isset( $status['max-children-reached'] ) ? (int)$status['max-children-reached'] : 0,
                            'accepted' => isset( $status['accepted-conn'] ) ? (int)$status['accepted-conn'] : 0,
                            'since' => isset( $status['start-time'] ) ? (int)$status['start-time'] : 0,
                        );
                    }
                }
                if ( $facts['kind'] === 'apache-fpm' && $facts['name'] === '' )
                    $facts['name'] = 'Apache';
                break;
            case 'apache-mod_php':
                $facts['name'] = 'Apache';
                if ( function_exists( 'apache_get_version' ) && preg_match( '#Apache/(\S+)#', (string)apache_get_version(), $m ) )
                    $facts['version'] = $m[1];
                $facts['workers'] = array( 'model' => 'apache-module' );
                break;
            case 'frankenphp':
                $facts['name'] = 'FrankenPHP';
                $engine = isset( $_SERVER['EXP_VELOCITY_ENGINE'] ) ? (string)$_SERVER['EXP_VELOCITY_ENGINE'] : (string)getenv( 'EXP_VELOCITY_ENGINE' );
                $facts['version'] = preg_match( '/FrankenPHP v?(\S+)/', $engine, $m ) ? $m[1] : '';
                $facts['workers'] = array( 'model' => !empty( $_SERVER['FRANKENPHP_WORKER'] ) ? 'persistent' : 'thread-per-request' );
                break;
            case 'cli-server':
                $facts['name'] = 'PHP built-in web server';
                $facts['version'] = PHP_VERSION;
                $facts['workers'] = array( 'model' => 'development', 'configured' => max( 1, (int)getenv( 'PHP_CLI_SERVER_WORKERS' ) ) );
                break;
            case 'cli':
                $facts['name'] = 'command line';
                $facts['workers'] = array( 'model' => 'command-line' );
                break;
        }
        return $facts;
    }

    /**
     * Which kind of server runs this process.
     *
     * @param string $sapi
     * @param string $software SERVER_SOFTWARE
     * @param bool $velocity whether Exponential Velocity's own server runs it
     * @return string velocity, frankenphp, cli-server, apache-mod_php, apache-fpm, nginx-fpm, fpm, cli or other
     */
    public static function serverKind( $sapi, $software, $velocity )
    {
        if ( $velocity )
            return 'velocity';
        $software = strtolower( (string)$software );
        switch ( $sapi )
        {
            case 'frankenphp': return 'frankenphp';
            case 'cli-server': return 'cli-server';
            case 'apache2handler': return 'apache-mod_php';
            case 'fpm-fcgi':
            case 'cgi-fcgi':
                if ( strpos( $software, 'nginx' ) === 0 )
                    return 'nginx-fpm';
                return strpos( $software, 'apache' ) === 0 ? 'apache-fpm' : 'fpm';
            case 'cli':
                return 'cli';
        }
        return 'other';
    }

    protected static function velocityVersion()
    {
        $version = function_exists( 'qbix_version_label' ) ? (string)qbix_version_label()
                 : ( defined( 'QBIX_SHIP_VERSION' ) ? (string)QBIX_SHIP_VERSION : '' );
        if ( strncmp( $version, 'v0.', 3 ) !== 0 && class_exists( '\Composer\InstalledVersions' ) )
        {
            foreach ( array( 'se7enxweb/exponential-velocity', 'se7enxweb/qbix-webserver' ) as $package )
            {
                if ( \Composer\InstalledVersions::isInstalled( $package ) )
                    return (string)\Composer\InstalledVersions::getPrettyVersion( $package );
            }
        }
        return $version;
    }

    protected static function collectPhp()
    {
        $extensions = get_loaded_extensions();
        natcasesort( $extensions );
        $disabled = array_values( array_filter( array_map( 'trim', explode( ',', (string)ini_get( 'disable_functions' ) ) ), 'strlen' ) );
        return array(
            'version' => PHP_VERSION,
            'minimum' => (string)self::ini( 'phpversion', 'MinimumVersion', '8.0.0', 'setup.ini' ),
            'sapi' => PHP_SAPI,
            'zts' => defined( 'PHP_ZTS' ) && PHP_ZTS,
            'os' => PHP_OS_FAMILY,
            'uname' => php_uname( 's' ) . ' ' . php_uname( 'r' ) . ' ' . php_uname( 'm' ),
            'memory_limit' => (string)ini_get( 'memory_limit' ),
            'max_execution_time' => (int)ini_get( 'max_execution_time' ),
            'upload_max_filesize' => (string)ini_get( 'upload_max_filesize' ),
            'post_max_size' => (string)ini_get( 'post_max_size' ),
            'max_file_uploads' => (int)ini_get( 'max_file_uploads' ),
            'max_input_vars' => (int)ini_get( 'max_input_vars' ),
            'file_uploads' => (bool)ini_get( 'file_uploads' ),
            'display_errors' => self::enabled( ini_get( 'display_errors' ) ) || strtolower( (string)ini_get( 'display_errors' ) ) === 'stdout',
            'open_basedir' => (string)ini_get( 'open_basedir' ) !== '',
            'date_timezone' => (string)ini_get( 'date.timezone' ),
            'disabled_functions' => $disabled,
            'extensions' => array_values( $extensions ),
        );
    }

    protected static function collectOpcache()
    {
        $disabled = array_map( 'trim', explode( ',', (string)ini_get( 'disable_functions' ) ) );
        $facts = array(
            'loaded' => extension_loaded( 'Zend OPcache' ),
            'enabled' => false,
            'status' => false,
            'status_note' => '',
            'cli' => PHP_SAPI === 'cli',
            'enable_cli' => (bool)ini_get( 'opcache.enable_cli' ),
            'memory' => self::bytes( (string)ini_get( 'opcache.memory_consumption' ), 1048576 ),
            'validate_timestamps' => (bool)ini_get( 'opcache.validate_timestamps' ),
            'revalidate_freq' => (int)ini_get( 'opcache.revalidate_freq' ),
            'file_update_protection' => (int)ini_get( 'opcache.file_update_protection' ),
            'jit' => (string)ini_get( 'opcache.jit' ),
            'jit_buffer' => self::bytes( (string)ini_get( 'opcache.jit_buffer_size' ) ),
            'jit_on' => null,
        );
        if ( !$facts['loaded'] )
            return $facts;
        $facts['enabled'] = PHP_SAPI === 'cli' ? (bool)ini_get( 'opcache.enable' ) && $facts['enable_cli'] : (bool)ini_get( 'opcache.enable' );
        if ( in_array( 'opcache_get_status', $disabled, true ) )
        {
            $facts['status_note'] = 'disabled_function';
            return $facts;
        }
        if ( !function_exists( 'opcache_get_status' ) )
            return $facts;
        $status = @opcache_get_status( false );
        if ( !is_array( $status ) )
        {
            $facts['status_note'] = ini_get( 'opcache.restrict_api' ) ? 'restrict_api' : 'unavailable';
            return $facts;
        }
        $facts['status'] = true;
        $facts['enabled'] = !empty( $status['opcache_enabled'] );
        if ( !$facts['enabled'] )
            return $facts;
        $stats = $status['opcache_statistics'];
        $mem = $status['memory_usage'];
        $used = $mem['used_memory'] >= 0 ? (int)$mem['used_memory'] : max( 0, $facts['memory'] - $mem['free_memory'] - $mem['wasted_memory'] );
        $facts += array(
            'hits' => (int)$stats['hits'],
            'misses' => (int)$stats['misses'],
            'hit_rate' => round( (float)$stats['opcache_hit_rate'], 1 ),
            'scripts' => (int)$stats['num_cached_scripts'],
            'max_keys' => (int)$stats['max_cached_keys'],
            'used' => $used,
            'free' => (int)$mem['free_memory'],
            'wasted' => (int)$mem['wasted_memory'],
            'restarts' => (int)$stats['oom_restarts'] + (int)$stats['hash_restarts'],
            'cache_full' => !empty( $status['cache_full'] ),
        );
        $facts['jit_on'] = isset( $status['jit']['on'] ) ? (bool)$status['jit']['on'] : null;
        return $facts;
    }

    protected static function collectApcu()
    {
        $facts = array( 'loaded' => extension_loaded( 'apcu' ), 'enabled' => false, 'version' => (string)phpversion( 'apcu' ) );
        if ( !$facts['loaded'] || !function_exists( 'apcu_enabled' ) || !apcu_enabled() )
            return $facts;
        $facts['enabled'] = true;
        $info = @apcu_cache_info( true );
        $sma = function_exists( 'apcu_sma_info' ) ? @apcu_sma_info( true ) : false;
        if ( is_array( $info ) )
        {
            $hits = (int)$info['num_hits'];
            $misses = (int)$info['num_misses'];
            $facts['entries'] = (int)$info['num_entries'];
            $facts['hit_rate'] = ( $hits + $misses ) > 0 ? round( 100 * $hits / ( $hits + $misses ), 1 ) : null;
        }
        if ( is_array( $sma ) )
        {
            $facts['size'] = (int)$sma['num_seg'] * (int)$sma['seg_size'];
            $facts['free'] = (int)$sma['avail_mem'];
        }
        return $facts;
    }

    protected static function collectDatabase( $withSize )
    {
        $facts = array(
            'implementation' => (string)self::ini( 'DatabaseSettings', 'DatabaseImplementation', '' ),
            'server' => (string)self::ini( 'DatabaseSettings', 'Server', '' ),
            'port' => (string)self::ini( 'DatabaseSettings', 'Port', '' ),
            'socket' => (string)self::ini( 'DatabaseSettings', 'Socket', '' ),
            'name' => (string)self::ini( 'DatabaseSettings', 'Database', '' ),
            'user' => (string)self::ini( 'DatabaseSettings', 'User', '' ),
            'connected' => false,
            'engine' => '',
            'class' => '',
            'version' => '',
            'charset' => '',
            'tables' => null,
            'size' => null,
            'file' => '',
            'slave' => false,
        );
        if ( !class_exists( 'eZDB' ) )
            return $facts;
        $db = eZDB::instance();
        $facts['class'] = get_class( $db );
        $facts['engine'] = (string)$db->databaseName();
        $facts['connected'] = (bool)$db->isConnected();
        $facts['charset'] = (string)$db->charset();
        $facts['slave'] = !empty( $db->UseSlaveServer );
        if ( !$facts['connected'] )
            return $facts;
        $version = $db->databaseServerVersion();
        if ( is_array( $version ) )
            $version = isset( $version['string'] ) && is_string( $version['string'] ) ? $version['string']
                     : ( isset( $version['values'] ) ? implode( '.', (array)$version['values'] ) : '' );
        $facts['version'] = is_string( $version ) ? $version : '';
        if ( stripos( $facts['class'], 'mongo' ) === false )
        {
            $tables = $db->eZTableList();
            $facts['tables'] = is_array( $tables ) ? count( $tables ) : null;
        }
        $engine = strtolower( $facts['engine'] );
        if ( strpos( $engine, 'sqlite' ) === 0 && class_exists( 'eZSQLite3DB' ) && method_exists( 'eZSQLite3DB', 'filePath' ) )
        {
            $file = eZSQLite3DB::filePath( $facts['name'] );
            $facts['file'] = (string)$file;
            if ( is_file( $file ) )
                $facts['size'] = (int)filesize( $file );
        }
        elseif ( $withSize && $engine === 'mysql' )
        {
            $rows = $db->arrayQuery( 'SELECT SUM(data_length + index_length) AS size FROM information_schema.tables WHERE table_schema = DATABASE()' );
            $facts['size'] = isset( $rows[0]['size'] ) ? (int)$rows[0]['size'] : null;
        }
        elseif ( $withSize && $engine === 'postgresql' )
        {
            $rows = $db->arrayQuery( 'SELECT pg_database_size(current_database()) AS size' );
            $facts['size'] = isset( $rows[0]['size'] ) ? (int)$rows[0]['size'] : null;
        }
        return $facts;
    }

    protected static function collectStorage( $root, $withSizes, $deadline )
    {
        $var = class_exists( 'eZSys' ) ? (string)eZSys::varDirectory() : 'var';
        $absolute = function ( $path ) use ( $root ) { return $path !== '' && $path[0] === '/' ? $path : $root . '/' . $path; };
        $varPath = $absolute( $var );
        $facts = array(
            'var' => $varPath,
            'var_writable' => is_dir( $varPath ) && is_writable( $varPath ),
            'cache' => class_exists( 'eZSys' ) ? $absolute( (string)eZSys::cacheDirectory() ) : $varPath . '/cache',
            'storage' => class_exists( 'eZSys' ) ? $absolute( (string)eZSys::storageDirectory() ) : $varPath . '/storage',
            // Where the logs of this site go: eZDebug's directory (var/log, or the log directory of the site with
            // site.ini [FileSettings] UseGlobalLogDir=disabled)
            'log' => $absolute( class_exists( 'eZDebug' ) ? rtrim( (string)eZDebug::instance()->logDirectory(), '/' ) : 'var/log' ),
            'disk_free' => @disk_free_space( $varPath ) ?: null,
            'disk_total' => @disk_total_space( $varPath ) ?: null,
            'var_size' => null,
            'var_counted_all' => true,
        );
        $facts['cache_writable'] = is_dir( $facts['cache'] ) && is_writable( $facts['cache'] );
        $facts['storage_writable'] = is_dir( $facts['storage'] ) && is_writable( $facts['storage'] );
        // var/log takes what is logged before the siteaccess is known; the log directory of the site (also
        // storage.log, eZSys::logDirectory()) what is logged after. Each is created on the first entry, so one that
        // does not exist yet counts as writable when the directory it would be created in is.
        $logDirs = array( $root . '/var/log', $facts['log'] );
        if ( class_exists( 'eZSys' ) )
            $logDirs[] = $absolute( (string)eZSys::logDirectory() );
        $facts['log_writable'] = true;
        foreach ( array_unique( $logDirs ) as $logDir )
        {
            $existing = $logDir;
            while ( !is_dir( $existing ) && dirname( $existing ) !== $existing )
                $existing = dirname( $existing );
            if ( !is_writable( $existing ) )
                $facts['log_writable'] = false;
        }
        if ( $withSizes )
        {
            $size = self::directorySize( $varPath, $deadline );
            $facts['var_size'] = $size['bytes'];
            $facts['var_counted_all'] = $size['complete'];
        }
        return $facts;
    }

    /**
     * The size of a directory, stopped at the deadline.
     *
     * @param string $dir
     * @param float $deadline microtime
     * @return array bytes, files, complete
     */
    public static function directorySize( $dir, $deadline )
    {
        $result = array( 'bytes' => 0, 'files' => 0, 'complete' => true );
        if ( !is_dir( $dir ) )
            return array( 'bytes' => null, 'files' => 0, 'complete' => true );
        try
        {
            $files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ),
                                                    RecursiveIteratorIterator::LEAVES_ONLY, RecursiveIteratorIterator::CATCH_GET_CHILD );
            foreach ( $files as $file )
            {
                if ( ( ++$result['files'] & 63 ) === 0 && microtime( true ) > $deadline )
                {
                    $result['complete'] = false;
                    break;
                }
                if ( !$file->isLink() && $file->isFile() )
                    $result['bytes'] += $file->getSize();
            }
        }
        catch ( Exception $e )
        {
            $result['complete'] = false;
        }
        return $result;
    }

    protected static function collectCaches( $withSizes, $deadline )
    {
        $facts = array(
            'view' => self::enabled( self::ini( 'ContentSettings', 'ViewCaching', 'enabled' ) ),
            'template_compile' => self::enabled( self::ini( 'TemplateSettings', 'TemplateCompile', 'enabled' ) ),
            'template_cache' => self::enabled( self::ini( 'TemplateSettings', 'TemplateCache', 'enabled' ) ),
            'override' => self::enabled( self::ini( 'OverrideSettings', 'Cache', 'enabled' ) ),
            'static' => self::enabled( self::ini( 'ContentSettings', 'StaticCache', 'disabled' ) ),
            'http' => class_exists( 'eZINI' ) && self::ini( 'HttpCacheSettings', 'Enabled', 'disabled', 'httpcache.ini' ) === 'enabled',
            'query' => class_exists( 'eZINI' ) ? (string)self::ini( 'QueryCacheSettings', 'Mode', '', 'querycache.ini' ) : '',
            'response' => defined( 'QBIX_SERVER_VERSION' ) && class_exists( 'Q_WebServer_Cache', false ) ? (bool)Q_WebServer_Cache::$enabled : null,
            'dirs' => array(),
        );
        $cacheDir = class_exists( 'eZSys' ) ? (string)eZSys::cacheDirectory() : '';
        if ( $cacheDir !== '' && is_dir( $cacheDir ) )
        {
            foreach ( (array)@scandir( $cacheDir ) as $entry )
            {
                if ( $entry === '.' || $entry === '..' || !is_dir( $cacheDir . '/' . $entry ) )
                    continue;
                $facts['dirs'][$entry] = null;
                if ( $withSizes && microtime( true ) < $deadline )
                {
                    $size = self::directorySize( $cacheDir . '/' . $entry, $deadline );
                    $facts['dirs'][$entry] = $size['complete'] ? $size['bytes'] : null;
                }
            }
            ksort( $facts['dirs'] );
        }
        return $facts;
    }

    protected static function collectCronjobs()
    {
        $facts = array( 'last' => null, 'source' => '', 'part' => '' );
        $var = class_exists( 'eZSys' ) ? rtrim( (string)eZSys::varDirectory(), '/' ) : 'var';
        $root = class_exists( 'eZSys' ) ? rtrim( (string)eZSys::rootDir(), '/' ) : '.';
        if ( $var !== '' && $var[0] !== '/' )
            $var = $root . '/' . $var;
        // Runs started from Setup > Cronjobs (read as it is; the page that keeps it settles finished runs)
        $history = $var . '/cronjobs/history.json';
        if ( is_readable( $history ) )
        {
            $runs = json_decode( (string)file_get_contents( $history ), true );
            if ( is_array( $runs ) && isset( $runs[0]['started'] ) )
            {
                $facts['last'] = (int)$runs[0]['started'];
                $facts['source'] = 'history';
                $facts['part'] = isset( $runs[0]['part'] ) ? (string)$runs[0]['part'] : '';
            }
        }
        // Runs from a crontab write their output to a log of their own
        // (var/log, and the log directory of the site: site.ini [FileSettings] LogDir, LogVarDir)
        $siteLog = class_exists( 'eZSys' ) ? rtrim( (string)eZSys::logDirectory(), '/' ) : $var . '/log';
        if ( $siteLog !== '' && $siteLog[0] !== '/' )
            $siteLog = $root . '/' . $siteLog;
        $logs = array_merge( (array)glob( $root . '/var/log/cronjob*.log' ), (array)glob( $siteLog . '/cronjob*.log' ), (array)glob( $var . '/cronjobs/*.log' ) );
        foreach ( array_filter( $logs ) as $log )
        {
            $time = @filemtime( $log );
            if ( $time && @filesize( $log ) > 0 && ( $facts['last'] === null || $time > $facts['last'] ) )
            {
                $facts['last'] = (int)$time;
                $facts['source'] = 'log';
                $facts['part'] = preg_replace( '/^cronjob-?|\.log$/', '', basename( $log ) );
            }
        }
        return $facts;
    }

    protected static function collectMail()
    {
        $transport = strtolower( (string)self::ini( 'MailSettings', 'Transport', 'sendmail' ) );
        return array(
            'transport' => $transport,
            'server' => (string)self::ini( 'MailSettings', 'TransportServer', '' ),
            'port' => (string)self::ini( 'MailSettings', 'TransportPort', '' ),
            'encryption' => (string)self::ini( 'MailSettings', 'TransportConnectionType', '' ),
            'login' => (string)self::ini( 'MailSettings', 'TransportUser', '' ) !== '',
            'sender_set' => (string)self::ini( 'MailSettings', 'EmailSender', '' ) !== '' || (string)self::ini( 'MailSettings', 'AdminEmail', '' ) !== '',
        );
    }

    protected static function collectLocale()
    {
        return array(
            'locale' => (string)self::ini( 'RegionalSettings', 'Locale', '' ),
            'content_locale' => (string)self::ini( 'RegionalSettings', 'ContentObjectLocale', '' ),
            'languages' => array_values( array_filter( (array)self::ini( 'RegionalSettings', 'SiteLanguageList', array() ), 'strlen' ) ),
            'timezone_setting' => (string)self::ini( 'TimeZoneSettings', 'TimeZone', '' ),
            'timezone' => date_default_timezone_get(),
            'php_timezone' => (string)ini_get( 'date.timezone' ),
            'now' => date( 'Y-m-d H:i:s T' ),
        );
    }

    protected static function collectDebug()
    {
        return array(
            'output' => self::enabled( self::ini( 'DebugSettings', 'DebugOutput', 'disabled' ) ),
            'by_ip' => self::enabled( self::ini( 'DebugSettings', 'DebugByIP', 'disabled' ) ),
            'by_user' => self::enabled( self::ini( 'DebugSettings', 'DebugByUser', 'disabled' ) ),
            'template' => self::enabled( self::ini( 'TemplateSettings', 'Debug', 'disabled' ) ),
            'used_templates' => self::enabled( self::ini( 'TemplateSettings', 'ShowUsedTemplates', 'disabled' ) ),
            'sql' => self::enabled( self::ini( 'DatabaseSettings', 'SQLOutput', 'disabled' ) ),
            'display_errors' => self::enabled( ini_get( 'display_errors' ) ) || strtolower( (string)ini_get( 'display_errors' ) ) === 'stdout',
        );
    }

    protected static function collectExtensions()
    {
        if ( !class_exists( 'eZExtension' ) )
            return array();
        $list = array();
        foreach ( eZExtension::activeExtensions() as $extension )
        {
            $version = '';
            $name = $extension;
            // extension.xml <metadata> or ezinfo.php, then composer.json: no walk through the extension's files
            $info = class_exists( 'ezpExtension' ) ? ezpExtension::getInstance( $extension )->getInfo() : null;
            if ( is_array( $info ) )
            {
                foreach ( $info as $key => $value )
                {
                    if ( !is_string( $value ) || trim( $value ) === '' || strpos( trim( $value ), '//' ) === 0 )
                        continue;
                    if ( strtolower( $key ) === 'version' && $version === '' )
                        $version = trim( $value );
                    if ( strtolower( $key ) === 'name' )
                        $name = trim( strip_tags( $value ) );
                }
            }
            $path = eZExtension::extensionPath( $extension );
            if ( $version === '' && $path && is_readable( $path . '/composer.json' ) )
            {
                $composer = json_decode( (string)file_get_contents( $path . '/composer.json' ), true );
                if ( isset( $composer['version'] ) && is_string( $composer['version'] ) )
                    $version = $composer['version'];
            }
            $list[] = array( 'extension' => $extension, 'name' => $name, 'version' => $version );
        }
        return $list;
    }

    protected static function collectHost()
    {
        $facts = array( 'cpu' => '', 'cpus' => null, 'memory' => null, 'load' => null );
        if ( function_exists( 'sys_getloadavg' ) )
        {
            $load = @sys_getloadavg();
            if ( is_array( $load ) )
                $facts['load'] = array_map( function ( $v ) { return round( $v, 2 ); }, $load );
        }
        if ( is_readable( '/proc/cpuinfo' ) )
        {
            $cpuinfo = (string)@file_get_contents( '/proc/cpuinfo' );
            $facts['cpus'] = preg_match_all( '/^processor\s*:/m', $cpuinfo );
            if ( preg_match( '/^model name\s*:\s*(.+)$/m', $cpuinfo, $m ) )
                $facts['cpu'] = trim( $m[1] );
        }
        if ( is_readable( '/proc/meminfo' ) && preg_match( '/^MemTotal:\s*(\d+)\s*kB/m', (string)@file_get_contents( '/proc/meminfo' ), $m ) )
            $facts['memory'] = (int)$m[1] * 1024;
        return $facts;
    }

    // ── Values ──────────────────────────────────────────────────────────────────────────────────────────────────

    /**
     * A php.ini size ("128M", "1G", "-1", "512") in bytes; -1 stays -1.
     *
     * @param string $value
     * @param int $unit the unit of a bare number (bytes, or 1048576 for opcache.memory_consumption)
     * @return int
     */
    public static function bytes( $value, $unit = 1 )
    {
        $value = trim( (string)$value );
        if ( $value === '' )
            return 0;
        if ( $value === '-1' )
            return -1;
        if ( !preg_match( '/^(\d+(?:\.\d+)?)\s*([kmgt]?)b?$/i', $value, $m ) )
            return (int)$value;
        $factor = array( '' => $unit, 'k' => 1024, 'm' => 1048576, 'g' => 1073741824, 't' => 1099511627776 );
        return (int)round( (float)$m[1] * $factor[strtolower( $m[2] )] );
    }

    /**
     * Bytes as "1.5 GB".
     *
     * @param int|null $bytes
     * @return string
     */
    public static function size( $bytes )
    {
        if ( $bytes === null )
            return '';
        if ( $bytes < 0 )
            return 'unlimited';
        $units = array( 'B', 'KB', 'MB', 'GB', 'TB' );
        $i = 0;
        $value = (float)$bytes;
        while ( $value >= 1024 && $i < count( $units ) - 1 )
        {
            $value /= 1024;
            $i++;
        }
        return ( $i === 0 ? (string)(int)$value : number_format( $value, $value >= 100 ? 0 : 1 ) ) . ' ' . $units[$i];
    }

    protected function t( $text, array $params = array() )
    {
        if ( $this->translate )
            return ezpI18n::tr( 'design/admin/setup/info', $text, null, $params );
        return strtr( $text, $params );
    }

    protected function yes( $flag )
    {
        return $flag ? $this->t( 'on' ) : $this->t( 'off' );
    }

    protected function ago( $time )
    {
        $seconds = max( 0, (int)$this->facts['time'] - (int)$time );
        if ( $seconds < 120 )
            return $this->t( '%n seconds ago', array( '%n' => $seconds ) );
        if ( $seconds < 7200 )
            return $this->t( '%n minutes ago', array( '%n' => (int)floor( $seconds / 60 ) ) );
        if ( $seconds < 172800 )
            return $this->t( '%n hours ago', array( '%n' => (int)floor( $seconds / 3600 ) ) );
        return $this->t( '%n days ago', array( '%n' => (int)floor( $seconds / 86400 ) ) );
    }

    protected function fact( $part, $key, $default = null )
    {
        return isset( $this->facts[$part] ) && is_array( $this->facts[$part] ) && array_key_exists( $key, $this->facts[$part] )
             ? $this->facts[$part][$key] : $default;
    }

    /**
     * The facts as read, unmasked: for the view's own variables only, never for output.
     *
     * @return array
     */
    public function facts()
    {
        return $this->facts;
    }

    /**
     * The name of the server that answered, as the page states it ("Apache + PHP-FPM", "Exponential Velocity").
     *
     * @return string
     */
    public function serverLabel()
    {
        $kind = $this->fact( 'server', 'kind', 'other' );
        $labels = array(
            'velocity' => 'Exponential Velocity',
            'apache-fpm' => 'Apache + PHP-FPM',
            'nginx-fpm' => 'nginx + PHP-FPM',
            'fpm' => 'PHP-FPM',
            'apache-mod_php' => 'Apache (mod_php)',
            'frankenphp' => 'FrankenPHP',
            'cli-server' => $this->t( 'PHP built-in web server' ),
            'cli' => $this->t( 'Command line' ),
        );
        return isset( $labels[$kind] ) ? $labels[$kind] : (string)$this->fact( 'server', 'name', $kind );
    }

    /**
     * How the server runs PHP, in words.
     *
     * @return string
     */
    public function workerModel()
    {
        $w = (array)$this->fact( 'server', 'workers', array() );
        $model = isset( $w['model'] ) ? $w['model'] : '';
        switch ( $model )
        {
            case 'persistent':
                return $this->fact( 'server', 'kind' ) === 'velocity'
                    ? $this->t( 'persistent workers: one process answers many requests and keeps its classes and caches between them' )
                    : $this->t( 'worker mode: the script stays in memory between requests' );
            case 'fork-per-request':
                return $this->t( 'a fresh worker forked for every request' );
            case 'process-per-request':
                return $this->t( 'a pool of PHP processes, a clean state for every request' );
            case 'apache-module':
                return $this->t( 'PHP inside the Apache processes' );
            case 'thread-per-request':
                return $this->t( 'a pool of PHP threads, a clean state for every request' );
            case 'development':
                return $this->t( 'a development server, not for production' );
            case 'command-line':
                return $this->t( 'one command-line process' );
        }
        return '';
    }

    // ── Health checks ───────────────────────────────────────────────────────────────────────────────────────────

    /**
     * The health checks: id, state (ok, warn, fail, info), title, detail and fix.
     *
     * @return array
     */
    public function checks()
    {
        if ( $this->checks !== null )
            return $this->checks;
        $checks = array();
        $add = function ( $id, $state, $title, $detail = '', $fix = '' ) use ( &$checks ) {
            $checks[] = array( 'id' => $id, 'state' => $state, 'title' => $title, 'detail' => $detail, 'fix' => $fix );
        };
        $web = !in_array( $this->fact( 'server', 'kind' ), array( 'cli' ), true );
        $velocity = $this->fact( 'server', 'kind' ) === 'velocity';

        // PHP version
        $php = (string)$this->fact( 'php', 'version', PHP_VERSION );
        $minimum = (string)$this->fact( 'php', 'minimum', '8.0.0' );
        $branch = implode( '.', array_slice( explode( '.', $php ), 0, 2 ) );
        $today = date( 'Y-m-d', (int)$this->facts['time'] );
        if ( version_compare( $php, $minimum, '<' ) )
            $add( 'php_version', self::FAIL, $this->t( 'PHP %version is below the supported minimum %minimum', array( '%version' => $php, '%minimum' => $minimum ) ),
                  '', $this->t( 'Upgrade PHP to a supported version (8.3 or later).' ) );
        elseif ( isset( self::$phpSupportEnds[$branch] ) && self::$phpSupportEnds[$branch] < $today )
            $add( 'php_version', self::WARN, $this->t( 'PHP %branch no longer gets security fixes', array( '%branch' => $branch ) ),
                  $this->t( 'Its security support ended on %date.', array( '%date' => self::$phpSupportEnds[$branch] ) ),
                  $this->t( 'Plan an upgrade to a supported PHP branch.' ) );
        else
            $add( 'php_version', self::OK, $this->t( 'PHP %version is supported', array( '%version' => $php ) ),
                  isset( self::$phpSupportEnds[$branch] ) ? $this->t( 'Security fixes until %date.', array( '%date' => self::$phpSupportEnds[$branch] ) ) : '' );

        // PHP extensions
        $loaded = array_map( 'strtolower', (array)$this->fact( 'php', 'extensions', array() ) );
        $missing = array();
        foreach ( explode( ',', self::REQUIRED_EXTENSIONS ) as $ext )
            if ( !in_array( strtolower( $ext ), $loaded, true ) )
                $missing[] = $ext;
        $engine = strtolower( (string)$this->fact( 'database', 'engine', '' ) ?: (string)$this->fact( 'database', 'implementation', '' ) );
        if ( isset( self::$databaseExtensions[$engine] ) && !in_array( self::$databaseExtensions[$engine], $loaded, true ) )
            $missing[] = self::$databaseExtensions[$engine];
        if ( $missing )
            $add( 'php_extensions', self::FAIL, $this->t( 'PHP extensions the kernel needs are missing: %list', array( '%list' => implode( ', ', $missing ) ) ),
                  '', $this->t( 'Install and enable them for the PHP that serves the site, then reload it.' ) );
        else
            $add( 'php_extensions', self::OK, $this->t( 'Every PHP extension the kernel needs is loaded' ) );
        $recommended = array();
        foreach ( explode( ',', self::RECOMMENDED_EXTENSIONS ) as $ext )
            if ( !in_array( strtolower( $ext ), $loaded, true ) )
                $recommended[] = $ext;
        if ( !in_array( 'gd', $loaded, true ) && !in_array( 'imagick', $loaded, true ) )
            $recommended[] = 'gd / imagick';
        if ( $recommended )
            $add( 'php_extensions_recommended', self::WARN, $this->t( 'Recommended PHP extensions are missing: %list', array( '%list' => implode( ', ', $recommended ) ) ),
                  $this->t( 'Images, link checks, translations or the caches work without them only in part.' ) );

        // OPcache
        if ( !$this->fact( 'opcache', 'loaded', false ) || !$this->fact( 'opcache', 'enabled', false ) )
        {
            if ( $this->fact( 'server', 'kind' ) !== 'cli' )
                $add( 'opcache', self::WARN, $this->t( 'OPcache is off' ), $this->t( 'Every request compiles every PHP file again; pages take several times as long.' ),
                      $velocity ? $this->t( 'Set opcache.enable_cli=1 in [PHPSettings] IniOptions[] of velocity.ini, then restart Velocity.' )
                                : $this->t( 'Set opcache.enable=1 in the php.ini of the PHP that serves the site, then reload it.' ) );
        }
        elseif ( !$this->fact( 'opcache', 'status', false ) )
            $add( 'opcache', self::OK, $this->t( 'OPcache is on' ),
                  $this->fact( 'opcache', 'status_note' ) === 'disabled_function'
                      ? $this->t( 'Its statistics are not available: opcache_get_status is in disable_functions of this PHP.' )
                      : $this->t( 'Its statistics are not available to this script (opcache.restrict_api).' ) );
        elseif ( $this->fact( 'opcache', 'cache_full', false ) )
            $add( 'opcache', self::WARN, $this->t( 'OPcache is full' ), $this->t( 'Scripts that do not fit are compiled on every request.' ),
                  $this->t( 'Raise opcache.memory_consumption or opcache.max_accelerated_files.' ) );
        elseif ( ( $this->fact( 'opcache', 'hits', 0 ) + $this->fact( 'opcache', 'misses', 0 ) ) > 5000 && $this->fact( 'opcache', 'hit_rate', 100 ) < 90 && !$velocity )
            $add( 'opcache', self::WARN, $this->t( 'OPcache hit rate is %rate %', array( '%rate' => $this->fact( 'opcache', 'hit_rate' ) ) ),
                  $this->t( 'Below 90 %, many scripts are compiled again; the cache may be too small or reset too often.' ),
                  $this->t( 'Raise opcache.memory_consumption and check that nothing resets the cache.' ) );
        else
            $add( 'opcache', self::OK, $this->t( 'OPcache is on' ) );
        if ( $velocity && $this->fact( 'opcache', 'enabled', false ) && $this->fact( 'opcache', 'file_update_protection', 0 ) > 0 )
            $add( 'opcache_file_update_protection', self::WARN,
                  $this->t( 'opcache.file_update_protection is %n under Velocity', array( '%n' => $this->fact( 'opcache', 'file_update_protection' ) ) ),
                  $this->t( 'A persistent worker never caches a file written less than that many seconds before the request started, so recently deployed files are compiled on every request.' ),
                  $this->t( 'Set IniOptions[]=opcache.file_update_protection=0 in [PHPSettings] of velocity.ini, then restart Velocity.' ) );

        // Limits
        $memory = self::bytes( (string)$this->fact( 'php', 'memory_limit', '' ) );
        if ( $memory > 0 && $memory < 128 * 1048576 )
            $add( 'memory_limit', self::FAIL, $this->t( 'memory_limit is %limit', array( '%limit' => $this->fact( 'php', 'memory_limit' ) ) ),
                  $this->t( 'Publishing, image variations and cache clearing need more.' ), $this->t( 'Set memory_limit to 256M or more.' ) );
        elseif ( $memory > 0 && $memory < 256 * 1048576 )
            $add( 'memory_limit', self::WARN, $this->t( 'memory_limit is %limit', array( '%limit' => $this->fact( 'php', 'memory_limit' ) ) ),
                  $this->t( 'Large imports and image variations can run out.' ), $this->t( 'Set memory_limit to 256M or more.' ) );
        else
            $add( 'memory_limit', self::OK, $this->t( 'memory_limit is %limit', array( '%limit' => $memory < 0 ? $this->t( 'unlimited' ) : $this->fact( 'php', 'memory_limit' ) ) ) );
        $time = (int)$this->fact( 'php', 'max_execution_time', 0 );
        if ( $web && $time > 0 && $time < 30 )
            $add( 'max_execution_time', self::WARN, $this->t( 'max_execution_time is %n seconds', array( '%n' => $time ) ),
                  $this->t( 'Publishing large objects and clearing caches can take longer.' ), $this->t( 'Set max_execution_time to 30 or more.' ) );

        // Storage
        if ( !$this->fact( 'storage', 'var_writable', true ) )
            $add( 'var_writable', self::FAIL, $this->t( 'The var directory is not writable' ),
                  $this->t( 'Caches, uploaded files, logs and sessions cannot be written.' ),
                  $this->t( 'Give the user the web server runs as write access to var/ and everything below it.' ) );
        elseif ( !$this->fact( 'storage', 'cache_writable', true ) || !$this->fact( 'storage', 'storage_writable', true ) )
            $add( 'var_writable', self::FAIL, $this->t( 'The cache or storage directory is not writable' ), '',
                  $this->t( 'Give the user the web server runs as write access to var/ and everything below it.' ) );
        elseif ( !$this->fact( 'storage', 'log_writable', true ) )
            $add( 'var_writable', self::WARN, $this->t( 'The log directory is not writable' ),
                  $this->t( 'Errors and warnings of the site are not recorded.' ),
                  $this->t( 'Give the user the web server runs as write access to var/log and to the log directory of the site.' ) );
        else
            $add( 'var_writable', self::OK, $this->t( 'The var directory is writable' ) );
        $free = $this->fact( 'storage', 'disk_free' );
        $total = $this->fact( 'storage', 'disk_total' );
        if ( $free !== null && $total )
        {
            $percent = 100 * $free / $total;
            $state = ( $free < 1073741824 || $percent < 5 ) ? self::FAIL : ( ( $free < 5 * 1073741824 || $percent < 10 ) ? self::WARN : self::OK );
            $add( 'disk_free', $state, $this->t( '%free free on the disk of var/ (%percent %)', array( '%free' => self::size( $free ), '%percent' => (int)round( $percent ) ) ),
                  '', $state === self::OK ? '' : $this->t( 'Free space: old logs, var/tmp and caches can be cleared; uploads and the database need room to grow.' ) );
        }

        // Debug
        if ( $this->fact( 'debug', 'output', false ) && !$this->fact( 'debug', 'by_ip', false ) && !$this->fact( 'debug', 'by_user', false ) )
            $add( 'debug_output', self::FAIL, $this->t( 'Debug output is shown to every visitor' ),
                  $this->t( 'It shows SQL, templates, file paths and timings to anyone.' ),
                  $this->t( 'Set [DebugSettings] DebugByIP=enabled with DebugIPList[], or DebugOutput=disabled, in site.ini.' ) );
        elseif ( $this->fact( 'debug', 'output', false ) )
            $add( 'debug_output', self::INFO, $this->t( 'Debug output is on for chosen addresses or users only' ),
                  $this->t( 'Fine for a staging site; switch it off on a production site.' ) );
        else
            $add( 'debug_output', self::OK, $this->t( 'Debug output is off' ) );
        $devSettings = array();
        if ( $this->fact( 'debug', 'template', false ) )
            $devSettings[] = '[TemplateSettings] Debug';
        if ( $this->fact( 'debug', 'used_templates', false ) )
            $devSettings[] = '[TemplateSettings] ShowUsedTemplates';
        if ( $this->fact( 'debug', 'sql', false ) )
            $devSettings[] = '[DatabaseSettings] SQLOutput';
        if ( $devSettings )
            $add( 'debug_settings', self::WARN, $this->t( 'Development settings are on: %list', array( '%list' => implode( ', ', $devSettings ) ) ),
                  $this->t( 'They slow every page down and can put debug comments into pages and mails.' ),
                  $this->t( 'Set them to disabled in site.ini on a production site.' ) );
        if ( $web && $this->fact( 'php', 'display_errors', false ) )
            $add( 'display_errors', self::WARN, $this->t( 'PHP shows errors in the page (display_errors)' ),
                  $this->t( 'Error messages can show file paths and settings to visitors.' ),
                  $this->t( 'Set display_errors=Off and log_errors=On for the PHP that serves the site.' ) );

        // Caches
        $off = array();
        if ( !$this->fact( 'caches', 'view', true ) )
            $off[] = '[ContentSettings] ViewCaching';
        if ( !$this->fact( 'caches', 'template_compile', true ) )
            $off[] = '[TemplateSettings] TemplateCompile';
        if ( !$this->fact( 'caches', 'template_cache', true ) )
            $off[] = '[TemplateSettings] TemplateCache';
        if ( !$this->fact( 'caches', 'override', true ) )
            $off[] = '[OverrideSettings] Cache';
        if ( $off )
            $add( 'caches', self::WARN, $this->t( 'Caches are switched off: %list', array( '%list' => implode( ', ', $off ) ) ),
                  $this->t( 'Usual while templates are being developed; every page is slower until they are on.' ),
                  $this->t( 'Set them to enabled in site.ini on a production site.' ) );
        else
            $add( 'caches', self::OK, $this->t( 'The view, template and override caches are on' ) );

        // Site URL
        if ( $this->fact( 'site', 'site_url_placeholder', false ) )
            $add( 'site_url', self::WARN, $this->t( 'SiteURL is not an address visitors can reach' ),
                  $this->t( 'Mails, feeds and links made outside a request (cronjobs, notifications) use it.' ),
                  $this->t( 'Set [SiteSettings] SiteURL in the siteaccess settings.' ) );

        // Cronjobs
        $last = $this->fact( 'cronjobs', 'last' );
        if ( $last === null )
            $add( 'cronjobs', self::WARN, $this->t( 'No cronjob run was found' ),
                  $this->t( 'Without cronjobs, notifications, link checks, the trash and timed publishing do not run.' ),
                  $this->t( 'Schedule runcronjobs.php in the crontab of the user the site runs as (Setup > Cronjobs shows the lines).' ) );
        elseif ( (int)$this->facts['time'] - (int)$last > 2 * 86400 )
            $add( 'cronjobs', self::WARN, $this->t( 'The last cronjob run was %ago', array( '%ago' => $this->ago( $last ) ) ),
                  '', $this->t( 'Check the crontab of the user the site runs as (Setup > Cronjobs).' ) );
        else
            $add( 'cronjobs', self::OK, $this->t( 'Cronjobs ran %ago', array( '%ago' => $this->ago( $last ) ) ) );

        // Mail
        $transport = (string)$this->fact( 'mail', 'transport', '' );
        if ( $transport === 'file' )
            $add( 'mail', self::INFO, $this->t( 'Mail is written to files, not sent' ),
                  $this->t( 'Right for a test site; a production site needs sendmail or SMTP.' ), $this->t( 'Set [MailSettings] Transport in site.ini.' ) );
        elseif ( $transport === 'smtp' && (string)$this->fact( 'mail', 'server', '' ) === '' )
            $add( 'mail', self::FAIL, $this->t( 'SMTP is chosen, but no SMTP server is set' ), '', $this->t( 'Set [MailSettings] TransportServer in site.ini.' ) );

        // Database
        if ( $this->fact( 'database', 'skipped', false ) )
        {
        }
        elseif ( !$this->fact( 'database', 'connected', false ) )
            $add( 'database', self::FAIL, $this->t( 'There is no database connection' ), '', $this->t( 'Check [DatabaseSettings] in site.ini and that the database server runs.' ) );
        else
        {
            $charset = strtolower( (string)$this->fact( 'database', 'charset', '' ) );
            if ( $charset !== '' && !in_array( $charset, array( 'utf-8', 'utf8', 'utf8mb4' ), true ) )
                $add( 'database_charset', self::WARN, $this->t( 'The database character set is %charset', array( '%charset' => $charset ) ),
                      $this->t( 'Exponential stores text as UTF-8.' ), $this->t( 'Convert the database to UTF-8 (bin/php/ezconvertdbcharset.php).' ) );
            else
                $add( 'database', self::OK, $this->t( 'The database is connected' ) );
        }

        // Time zone
        if ( (string)$this->fact( 'locale', 'timezone_setting', '' ) === '' && (string)$this->fact( 'locale', 'php_timezone', '' ) === '' )
            $add( 'timezone', self::WARN, $this->t( 'No time zone is set' ),
                  $this->t( 'PHP falls back to UTC, so dates and timed publishing can be hours off.' ),
                  $this->t( 'Set date.timezone in php.ini or [TimeZoneSettings] TimeZone in site.ini.' ) );

        $order = array( self::FAIL => 0, self::WARN => 1, self::INFO => 2, self::OK => 3 );
        $position = array_flip( array_column( $checks, 'id' ) );
        usort( $checks, function ( $a, $b ) use ( $order, $position ) {
            return $order[$a['state']] <=> $order[$b['state']] ?: $position[$a['id']] <=> $position[$b['id']];
        } );
        return $this->checks = $checks;
    }

    /**
     * How many checks are in each state, and the worst state.
     *
     * @return array ok, warn, fail, info, worst
     */
    public function summary()
    {
        $count = array( self::OK => 0, self::WARN => 0, self::FAIL => 0, self::INFO => 0 );
        foreach ( $this->checks() as $check )
            $count[$check['state']]++;
        $count['worst'] = $count[self::FAIL] ? self::FAIL : ( $count[self::WARN] ? self::WARN : self::OK );
        return $count;
    }

    // ── Cards ───────────────────────────────────────────────────────────────────────────────────────────────────

    /**
     * The overview: one card per area, each with label/value rows (masked) and the worst state of its checks.
     *
     * @return array
     */
    public function cards()
    {
        $root = (string)$this->facts['root'];
        $path = function ( $p ) use ( $root ) { return expSystemReportMask::path( (string)$p, $root ); };
        $states = array();
        foreach ( $this->checks() as $check )
            $states[$check['id']] = $check['state'];
        $worst = function ( array $ids ) use ( $states ) {
            $result = self::OK;
            foreach ( $ids as $id )
            {
                if ( !isset( $states[$id] ) )
                    continue;
                if ( $states[$id] === self::FAIL )
                    return self::FAIL;
                if ( $states[$id] === self::WARN )
                    $result = self::WARN;
            }
            return $result;
        };
        $cards = array();

        // Exponential
        $rows = array(
            array( $this->t( 'Version' ), trim( $this->fact( 'exponential', 'version', '' ) . ' ' . $this->fact( 'exponential', 'state', '' ) ) ),
            array( $this->t( 'Release line' ), (string)$this->fact( 'exponential', 'alias', '' ) ),
        );
        if ( $this->fact( 'exponential', 'schema' ) )
            $rows[] = array( $this->t( 'Database schema' ), (string)$this->fact( 'exponential', 'schema' ) );
        if ( $this->fact( 'exponential', 'build' ) )
            $rows[] = array( $this->t( 'Engine archive build' ), (string)$this->fact( 'exponential', 'build' ) );
        $rows[] = array( $this->t( 'Siteaccess' ), (string)$this->fact( 'site', 'siteaccess', '' ) );
        $rows[] = array( $this->t( 'Extensions' ), (string)count( (array)$this->facts['extensions'] ) );
        $cards[] = array( 'id' => 'exponential', 'title' => 'Exponential', 'state' => $worst( array( 'site_url' ) ), 'rows' => $rows );

        // Server
        $w = (array)$this->fact( 'server', 'workers', array() );
        $rows = array(
            array( $this->t( 'Answered by' ), trim( $this->serverLabel() . ' ' . ( $this->fact( 'server', 'kind' ) === 'velocity' ? $this->fact( 'server', 'version', '' ) : '' ) ) ),
            array( $this->t( 'Web server' ), (string)$this->fact( 'server', 'software', '' ) !== '' ? (string)$this->fact( 'server', 'software' ) : $this->t( 'not stated' ) ),
            array( $this->t( 'Worker model' ), $this->workerModel() ),
        );
        if ( !empty( $w['pool'] ) )
            $rows[] = array( $this->t( 'PHP-FPM pool' ), $w['pool'] . ( !empty( $w['manager'] ) ? ' (pm = ' . $w['manager'] . ')' : '' ) );
        if ( isset( $w['total'] ) )
            $rows[] = array( $this->t( 'Processes' ), $this->t( '%active busy, %idle idle, %total in all', array( '%active' => $w['active'], '%idle' => $w['idle'], '%total' => $w['total'] ) )
                             . ( !empty( $w['max_children_reached'] ) ? ' · ' . $this->t( 'max_children reached %n times', array( '%n' => $w['max_children_reached'] ) ) : '' ) );
        if ( !empty( $w['configured'] ) && $this->fact( 'server', 'kind' ) === 'velocity' )
            $rows[] = array( $this->t( 'Workers' ), $this->t( '%n configured, %spare spare', array( '%n' => $w['configured'], '%spare' => isset( $w['spare'] ) ? $w['spare'] : 0 ) ) );
        if ( $this->fact( 'server', 'kind' ) === 'velocity' )
            $rows[] = array( $this->t( 'Engine' ), !empty( $w['archive'] ) ? $this->t( 'from the engine archive' ) : $this->t( 'from the files on disk' ) );
        $rows[] = array( $this->t( 'Port' ), $this->fact( 'server', 'port' ) ? $this->fact( 'server', 'port' ) . ( $this->fact( 'server', 'tls' ) ? ' (HTTPS)' : ' (HTTP)' ) : '' );
        $cards[] = array( 'id' => 'server', 'title' => $this->t( 'Server' ), 'state' => self::OK, 'rows' => $rows );

        // PHP
        $time = (int)$this->fact( 'php', 'max_execution_time', 0 );
        $rows = array(
            array( $this->t( 'Version' ), (string)$this->fact( 'php', 'version', '' ) . ( $this->fact( 'php', 'zts' ) ? ' (ZTS)' : '' ) ),
            array( 'SAPI', (string)$this->fact( 'php', 'sapi', '' ) ),
            array( 'memory_limit', (string)$this->fact( 'php', 'memory_limit', '' ) ),
            array( 'max_execution_time', $time === 0 ? $this->t( 'no limit' ) : $time . ' s' ),
            array( $this->t( 'Uploads' ), $this->fact( 'php', 'file_uploads', true )
                ? $this->t( '%upload per file, %post per request', array( '%upload' => (string)$this->fact( 'php', 'upload_max_filesize', '' ), '%post' => (string)$this->fact( 'php', 'post_max_size', '' ) ) )
                : $this->t( 'off' ) ),
            array( 'max_input_vars', (string)$this->fact( 'php', 'max_input_vars', '' ) ),
            array( $this->t( 'Operating system' ), (string)$this->fact( 'php', 'uname', '' ) ),
        );
        $cards[] = array( 'id' => 'php', 'title' => 'PHP', 'state' => $worst( array( 'php_version', 'php_extensions', 'php_extensions_recommended', 'memory_limit', 'max_execution_time', 'display_errors' ) ), 'rows' => $rows );

        // OPcache
        $rows = array();
        if ( !$this->fact( 'opcache', 'loaded', false ) )
            $rows[] = array( $this->t( 'Status' ), $this->t( 'not loaded' ) );
        else
        {
            $rows[] = array( $this->t( 'Status' ), $this->fact( 'opcache', 'enabled', false ) ? $this->t( 'on' ) : $this->t( 'off' ) );
            if ( $this->fact( 'opcache', 'status', false ) && $this->fact( 'opcache', 'enabled', false ) )
            {
                $rows[] = array( $this->t( 'Hit rate' ), $this->fact( 'opcache', 'hit_rate' ) . ' % (' . number_format( (int)$this->fact( 'opcache', 'hits' ) ) . ' / ' . number_format( (int)$this->fact( 'opcache', 'misses' ) ) . ')' );
                $rows[] = array( $this->t( 'Memory' ), $this->t( '%used of %total', array( '%used' => self::size( $this->fact( 'opcache', 'used' ) ), '%total' => self::size( (int)$this->fact( 'opcache', 'used' ) + (int)$this->fact( 'opcache', 'free' ) + (int)$this->fact( 'opcache', 'wasted' ) ) ) ) );
                $rows[] = array( $this->t( 'Scripts' ), number_format( (int)$this->fact( 'opcache', 'scripts' ) ) . ' / ' . number_format( (int)$this->fact( 'opcache', 'max_keys' ) ) );
            }
            elseif ( $this->fact( 'opcache', 'enabled', false ) )
            {
                $rows[] = array( $this->t( 'Statistics' ), $this->fact( 'opcache', 'status_note' ) === 'disabled_function'
                    ? $this->t( 'not available: opcache_get_status is disabled in this PHP' ) : $this->t( 'not available to this script' ) );
                $rows[] = array( $this->t( 'Memory' ), self::size( $this->fact( 'opcache', 'memory' ) ) );
            }
            $jit = $this->fact( 'opcache', 'jit_on' );
            if ( $jit === null )
                $jit = $this->fact( 'opcache', 'jit_buffer', 0 ) > 0 && !in_array( strtolower( (string)$this->fact( 'opcache', 'jit', '' ) ), array( '', '0', 'off', 'disable' ), true );
            $rows[] = array( 'JIT', ( $jit ? $this->t( 'on' ) : $this->t( 'off' ) ) . ( (string)$this->fact( 'opcache', 'jit', '' ) !== '' ? ' (opcache.jit=' . $this->fact( 'opcache', 'jit' ) . ')' : '' ) );
            $rows[] = array( 'validate_timestamps', $this->fact( 'opcache', 'validate_timestamps', true )
                ? ( (int)$this->fact( 'opcache', 'revalidate_freq', 2 ) === 0 ? $this->t( 'on, on every request' )
                    : $this->t( 'on, every %n s', array( '%n' => (int)$this->fact( 'opcache', 'revalidate_freq', 2 ) ) ) )
                : $this->t( 'off: an edited file is seen after a restart' ) );
            if ( $this->fact( 'server', 'kind' ) === 'velocity' || $this->fact( 'opcache', 'file_update_protection', 2 ) !== 2 )
                $rows[] = array( 'file_update_protection', (int)$this->fact( 'opcache', 'file_update_protection', 2 ) . ' s' );
        }
        $rows[] = array( 'APCu', $this->fact( 'apcu', 'enabled', false )
            ? $this->t( 'on' ) . ( $this->fact( 'apcu', 'size' ) ? ', ' . $this->t( '%free free of %size', array( '%free' => self::size( $this->fact( 'apcu', 'free' ) ), '%size' => self::size( $this->fact( 'apcu', 'size' ) ) ) ) : '' )
            : ( $this->fact( 'apcu', 'loaded', false ) ? $this->t( 'off' ) : $this->t( 'not loaded' ) ) );
        $cards[] = array( 'id' => 'opcache', 'title' => $this->t( 'OPcache and APCu' ), 'state' => $worst( array( 'opcache', 'opcache_file_update_protection' ) ), 'rows' => $rows );

        // Database
        if ( !$this->fact( 'database', 'skipped', false ) )
        {
            $rows = array(
                array( $this->t( 'Engine' ), trim( (string)$this->fact( 'database', 'engine', '' ) . ' ' . (string)$this->fact( 'database', 'version', '' ) ) ),
                array( $this->t( 'Driver' ), (string)$this->fact( 'database', 'class', '' ) ),
            );
            if ( $this->fact( 'database', 'file' ) )
                $rows[] = array( $this->t( 'File' ), $path( $this->fact( 'database', 'file' ) ) );
            else
            {
                $server = (string)$this->fact( 'database', 'server', '' );
                $rows[] = array( $this->t( 'Server' ), $server !== '' ? $server . ( $this->fact( 'database', 'port' ) ? ':' . $this->fact( 'database', 'port' ) : '' )
                    : ( $this->fact( 'database', 'socket' ) ? $path( $this->fact( 'database', 'socket' ) ) : $this->t( 'default' ) ) );
                $rows[] = array( $this->t( 'Database' ), (string)$this->fact( 'database', 'name', '' ) );
            }
            $rows[] = array( $this->t( 'Character set' ), (string)$this->fact( 'database', 'charset', '' ) );
            $rows[] = array( $this->t( 'Tables' ), $this->fact( 'database', 'tables' ) === null ? '' : (string)$this->fact( 'database', 'tables' ) );
            $rows[] = array( $this->t( 'Size' ), $this->fact( 'database', 'size' ) !== null ? self::size( $this->fact( 'database', 'size' ) ) : $this->t( 'measured on request' ) );
            if ( $this->fact( 'database', 'slave' ) )
                $rows[] = array( $this->t( 'Read replica' ), $this->t( 'on' ) );
            $cards[] = array( 'id' => 'database', 'title' => $this->t( 'Database' ), 'state' => $worst( array( 'database', 'database_charset' ) ), 'rows' => $rows );
        }

        // Storage
        $free = $this->fact( 'storage', 'disk_free' );
        $total = $this->fact( 'storage', 'disk_total' );
        $rows = array(
            array( $this->t( 'var directory' ), $path( $this->fact( 'storage', 'var', '' ) ) . ' · ' . ( $this->fact( 'storage', 'var_writable', false ) ? $this->t( 'writable' ) : $this->t( 'not writable' ) ) ),
            array( $this->t( 'Log directory' ), $this->fact( 'storage', 'log', '' ) === '' ? '' : $path( $this->fact( 'storage', 'log', '' ) ) . ' · ' . ( $this->fact( 'storage', 'log_writable', true ) ? $this->t( 'writable' ) : $this->t( 'not writable' ) ) ),
            array( $this->t( 'Size of var' ), $this->fact( 'storage', 'var_size' ) !== null
                ? ( $this->fact( 'storage', 'var_counted_all', true ) ? '' : '≥ ' ) . self::size( $this->fact( 'storage', 'var_size' ) ) : $this->t( 'measured on request' ) ),
            array( $this->t( 'Free disk' ), $free !== null && $total ? $this->t( '%free of %total', array( '%free' => self::size( $free ), '%total' => self::size( $total ) ) ) : '' ),
        );
        $cards[] = array( 'id' => 'storage', 'title' => $this->t( 'Storage' ), 'state' => $worst( array( 'var_writable', 'disk_free' ) ), 'rows' => $rows );

        // Caches
        $rows = array(
            array( $this->t( 'View cache' ), $this->yes( $this->fact( 'caches', 'view', true ) ) ),
            array( $this->t( 'Template compiling' ), $this->yes( $this->fact( 'caches', 'template_compile', true ) ) ),
            array( $this->t( 'Template cache' ), $this->yes( $this->fact( 'caches', 'template_cache', true ) ) ),
            array( $this->t( 'Override cache' ), $this->yes( $this->fact( 'caches', 'override', true ) ) ),
            array( $this->t( 'Static cache' ), $this->yes( $this->fact( 'caches', 'static', false ) ) ),
            array( $this->t( 'HTTP cache' ), $this->yes( $this->fact( 'caches', 'http', false ) ) ),
            array( $this->t( 'SQL query cache' ), (string)$this->fact( 'caches', 'query', '' ) !== '' ? (string)$this->fact( 'caches', 'query' ) : $this->t( 'off' ) ),
        );
        if ( $this->fact( 'caches', 'response' ) !== null )
            $rows[] = array( $this->t( 'Velocity response cache' ), $this->yes( $this->fact( 'caches', 'response' ) ) );
        $cards[] = array( 'id' => 'caches', 'title' => $this->t( 'Caches' ), 'state' => $worst( array( 'caches' ) ), 'rows' => $rows );

        // Cronjobs, mail, locale
        $last = $this->fact( 'cronjobs', 'last' );
        $rows = array(
            array( $this->t( 'Last run' ), $last === null ? $this->t( 'none found' ) : date( 'Y-m-d H:i', (int)$last ) . ' (' . $this->ago( $last ) . ')' ),
            array( $this->t( 'Seen in' ), $last === null ? '' : ( $this->fact( 'cronjobs', 'source' ) === 'history'
                ? $this->t( 'runs started from Setup > Cronjobs' ) : $this->t( 'the cronjob log' ) ) . ( $this->fact( 'cronjobs', 'part' ) ? ' (' . $this->fact( 'cronjobs', 'part' ) . ')' : '' ) ),
        );
        $cards[] = array( 'id' => 'cronjobs', 'title' => $this->t( 'Cronjobs' ), 'state' => $worst( array( 'cronjobs' ) ), 'rows' => $rows );

        $transport = (string)$this->fact( 'mail', 'transport', '' );
        $rows = array( array( $this->t( 'Transport' ), $transport ) );
        if ( $transport === 'smtp' )
        {
            $rows[] = array( $this->t( 'SMTP server' ), (string)$this->fact( 'mail', 'server', '' ) . ( $this->fact( 'mail', 'port' ) ? ':' . $this->fact( 'mail', 'port' ) : '' ) );
            $rows[] = array( $this->t( 'Encryption' ), (string)$this->fact( 'mail', 'encryption', '' ) !== '' ? (string)$this->fact( 'mail', 'encryption' ) : $this->t( 'none' ) );
            $rows[] = array( $this->t( 'Sign-in' ), $this->fact( 'mail', 'login', false ) ? $this->t( 'yes (credentials hidden)' ) : $this->t( 'no' ) );
        }
        $rows[] = array( $this->t( 'Sender address' ), $this->fact( 'mail', 'sender_set', false ) ? $this->t( 'set' ) : $this->t( 'not set' ) );
        $cards[] = array( 'id' => 'mail', 'title' => $this->t( 'Mail' ), 'state' => $worst( array( 'mail' ) ), 'rows' => $rows );

        $rows = array(
            array( $this->t( 'Locale' ), (string)$this->fact( 'locale', 'locale', '' ) ),
            array( $this->t( 'Content languages' ), implode( ', ', (array)$this->fact( 'locale', 'languages', array() ) ) ),
            array( $this->t( 'Time zone' ), (string)$this->fact( 'locale', 'timezone', '' ) . ( $this->fact( 'locale', 'timezone_setting' ) ? ' (site.ini)' : ( $this->fact( 'locale', 'php_timezone' ) ? ' (php.ini)' : '' ) ) ),
            array( $this->t( 'Server time' ), (string)$this->fact( 'locale', 'now', '' ) ),
        );
        $cards[] = array( 'id' => 'locale', 'title' => $this->t( 'Locale and time' ), 'state' => $worst( array( 'timezone' ) ), 'rows' => $rows );

        // Host
        $load = $this->fact( 'host', 'load' );
        $rows = array(
            array( $this->t( 'Processor' ), trim( (string)$this->fact( 'host', 'cpu', '' ) . ( $this->fact( 'host', 'cpus' ) ? ' × ' . $this->fact( 'host', 'cpus' ) : '' ) ) ),
            array( $this->t( 'Memory' ), self::size( $this->fact( 'host', 'memory' ) ) ),
            array( $this->t( 'Load' ), is_array( $load ) ? implode( ' · ', $load ) : '' ),
        );
        $cards[] = array( 'id' => 'host', 'title' => $this->t( 'Machine' ), 'state' => self::OK, 'rows' => $rows );

        // Masked and without empty rows
        foreach ( $cards as $c => $card )
        {
            $keep = array();
            foreach ( $card['rows'] as $row )
            {
                if ( (string)$row[1] === '' )
                    continue;
                $keep[] = array( 'label' => $row[0], 'value' => expSystemReportMask::paths( (string)$row[1], $root ) );
            }
            $cards[$c]['rows'] = $keep;
        }
        return $cards;
    }

    // ── Output ──────────────────────────────────────────────────────────────────────────────────────────────────

    /**
     * The whole report, masked: for the template, JSON and the text report.
     *
     * @return array
     */
    public function toArray()
    {
        $root = (string)$this->facts['root'];
        $facts = $this->facts;
        unset( $facts['root'] );
        // The database login is never part of a report
        if ( isset( $facts['database']['user'] ) )
            $facts['database']['user'] = expSystemReportMask::secret( $facts['database']['user'] );
        return array(
            'generated' => date( 'c', (int)$this->facts['time'] ),
            'server' => $this->serverLabel(),
            'server_kind' => (string)$this->fact( 'server', 'kind', '' ),
            'worker_model' => $this->workerModel(),
            'summary' => $this->summary(),
            'checks' => expSystemReportMask::report( $this->checks(), $root ),
            'cards' => $this->cards(),
            'extensions' => expSystemReportMask::report( (array)$this->facts['extensions'], $root ),
            'php_extensions' => (array)$this->fact( 'php', 'extensions', array() ),
            'facts' => expSystemReportMask::report( $facts, $root ),
        );
    }

    /**
     * The report as plain text, for a support request.
     *
     * @return string
     */
    public function toText()
    {
        $data = $this->toArray();
        $lines = array();
        $lines[] = 'Exponential system information';
        $lines[] = str_repeat( '=', 30 );
        $lines[] = 'Generated: ' . $data['generated'];
        $lines[] = 'Answered by: ' . $data['server'] . ( $data['worker_model'] !== '' ? ' (' . $data['worker_model'] . ')' : '' );
        $s = $data['summary'];
        $lines[] = sprintf( 'Health: %d ok, %d warnings, %d failures, %d notes', $s['ok'], $s['warn'], $s['fail'], $s['info'] );
        foreach ( $data['cards'] as $card )
        {
            $lines[] = '';
            $lines[] = '[' . $card['title'] . ']';
            foreach ( $card['rows'] as $row )
                $lines[] = sprintf( '  %-24s %s', $row['label'], $row['value'] );
        }
        $lines[] = '';
        $lines[] = '[Health checks]';
        foreach ( $data['checks'] as $check )
        {
            $lines[] = sprintf( '  %-5s %s', strtoupper( $check['state'] ), $check['title'] );
            if ( $check['detail'] !== '' )
                $lines[] = '        ' . $check['detail'];
            if ( $check['fix'] !== '' )
                $lines[] = '        -> ' . $check['fix'];
        }
        $lines[] = '';
        $lines[] = '[Extensions]';
        foreach ( $data['extensions'] as $extension )
            $lines[] = sprintf( '  %-36s %s', $extension['extension'], $extension['version'] !== '' ? $extension['version'] : '-' );
        $lines[] = '';
        $lines[] = '[PHP extensions]';
        $lines[] = '  ' . wordwrap( implode( ', ', $data['php_extensions'] ), 110, "\n  " );
        return implode( "\n", $lines ) . "\n";
    }
}
