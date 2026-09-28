<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$module = $Params['Module'];
$mode = $Params['Mode'];

if ( $mode and $mode === 'php' )
{
    phpinfo();
    eZExecution::cleanExit();
}


$http = eZHTTPTool::instance();
$ini = eZINI::instance();
$tpl = eZTemplate::factory();
$db = eZDB::instance();

// Whether the "OPcache and APCu" box links to Setup > Caches, where both can
// be emptied: only for a user who may clear caches there.
$cacheAccess = eZUser::currentUser()->hasAccessTo( 'setup', 'managecache' );
$canFlushCaches = $cacheAccess['accessWord'] !== 'no';

try
{
    $info = ezcSystemInfo::getInstance();
}
catch ( ezcSystemInfoReaderCantScanOSException $e )
{
    $info = null;
    eZDebug::writeNotice( "Could not read system information, returned: '" . $e->getMessage(). "'", 'system/info' );
}

if ( $info instanceof ezcSystemInfo )
{
    // Workaround until ezcTemplate is used, as properties can not be accessed directly in ezp templates.
    $systemInfo = array(
        'cpu_type' => $info->cpuType,
        'cpu_speed' => $info->cpuSpeed,
        'cpu_count' =>$info->cpuCount,
        'memory_size' => $info->memorySize
    );

    if ( $info->phpAccelerator !== null )
    {
        $phpAcceleratorInfo = array(   'name' => $info->phpAccelerator->name,
                                       'url' => $info->phpAccelerator->url,
                                       'enabled' => $info->phpAccelerator->isEnabled,
                                       'version_integer' => $info->phpAccelerator->versionInt,
                                       'version_string' => $info->phpAccelerator->versionString
        );
    }
    else
    {
        $phpAcceleratorInfo = array();
    }
}
else
{
       $systemInfo = array(
        'cpu_type' => '',
        'cpu_speed' => '',
        'cpu_count' => '',
        'memory_size' => ''
    );
    $phpAcceleratorInfo = array();
}

// The web server's name and version when this page is served by Exponential
// Velocity's own server. The engine inside it is Qbix, and the Qbix names it
// still reports (SERVER_SOFTWARE "QbixServer/1.5.0", the upstream base it is
// built on) say nothing about which release is running: that is the fork's
// release line, v0.0.4.x, plus the build. One place, used by every box below.
$velocityBrand = false;
if ( defined( 'QBIX_SERVER_VERSION' ) )
{
    $brandVersion = function_exists( 'qbix_version_label' ) ? qbix_version_label()
                  : ( defined( 'QBIX_SHIP_VERSION' ) ? (string)QBIX_SHIP_VERSION : '' );
    // Without a release tag to read (a package with no git), the installed
    // package's version is the release; the upstream base never is.
    if ( strncmp( $brandVersion, 'v0.', 3 ) !== 0 && class_exists( '\Composer\InstalledVersions' ) )
    {
        foreach ( array( 'se7enxweb/exponential-velocity', 'se7enxweb/qbix-webserver' ) as $package )
        {
            if ( \Composer\InstalledVersions::isInstalled( $package ) )
            {
                $brandVersion = (string)\Composer\InstalledVersions::getPrettyVersion( $package );
                break;
            }
        }
    }
    $velocityBrand = array(
        'name'    => 'Exponential Velocity (vc)',
        'version' => $brandVersion,
        'label'   => trim( 'Exponential Velocity ' . $brandVersion ),
    );
}
// What each exp:velocity engine is, in words, for the boxes below.
$velocityEngineNames = array(
    'qbix'       => ezpI18n::tr( 'design/admin/setup/info', 'Velocity\'s own server' ),
    'frankenphp' => 'FrankenPHP',
    'php'        => ezpI18n::tr( 'design/admin/setup/info', 'PHP\'s built-in web server' ),
);

$webserverInfo = false;
if ( function_exists( 'apache_get_version' ) )
{
    $webserverInfo = array( 'name' => 'Apache',
                            'modules' => false,
                            'version' => apache_get_version() );
    if ( function_exists( 'apache_get_modules' ) )
        $webserverInfo['modules'] = apache_get_modules();
}
elseif ( PHP_SAPI === 'cli-server' )
{
    // PHP's built-in web server (Velocity's php engine, or php -S by hand).
    // Worth naming plainly: it is a development server, and a page that says
    // "cli-server" and nothing else does not make that obvious.
    $workers = (int)getenv( 'PHP_CLI_SERVER_WORKERS' );
    $webserverInfo = array(
        'name'    => ezpI18n::tr( 'design/admin/setup/info', 'PHP built-in web server' ),
        'version' => PHP_VERSION,
        'modules' => array( 'SAPI: cli-server',
                            $workers > 1 ? ezpI18n::tr( 'design/admin/setup/info', '%count worker processes', null, array( '%count' => $workers ) ) : ezpI18n::tr( 'design/admin/setup/info', 'one request at a time' ),
                            ezpI18n::tr( 'design/admin/setup/info', 'a development server, not for production' ) ),
    );
    if ( getenv( 'EXP_VELOCITY_ENGINE' ) !== false )
        $webserverInfo['modules'][] = ezpI18n::tr( 'design/admin/setup/info', 'started by exp:velocity' );
}
elseif ( PHP_SAPI === 'frankenphp' )
{
    // FrankenPHP (Velocity's frankenphp engine, or run by hand). Its
    // SERVER_SOFTWARE is the bare name, so the version comes from what
    // exp:velocity puts in the environment: "FrankenPHP v1.12.7 PHP 8.5.11
    // Caddy v2.11.4". Classic mode runs each request on a clean state, as
    // php-fpm does; worker mode keeps a script in memory across requests,
    // which matters for everything else on this page as it does under Qbix.
    $engine = isset( $_SERVER['EXP_VELOCITY_ENGINE'] ) ? (string)$_SERVER['EXP_VELOCITY_ENGINE']
            : (string)getenv( 'EXP_VELOCITY_ENGINE' );
    $webserverInfo = array(
        'name'    => 'FrankenPHP',
        'version' => preg_match( '/FrankenPHP v?(\S+)/', $engine, $m ) ? $m[1] : '',
        'modules' => false
    );
    $runtime = array( 'SAPI: frankenphp' );
    if ( preg_match( '/Caddy v?(\S+)/', $engine, $m ) )
        $runtime[] = 'Caddy ' . $m[1];
    $runtime[] = !empty( $_SERVER['FRANKENPHP_WORKER'] )
               ? 'worker mode (the script stays in memory between requests)'
               : 'classic mode (a pool of PHP threads, a clean state per request)';
    if ( $engine !== '' )
        $runtime[] = 'started by exp:velocity';
    if ( eZSys::isSSLNow() )
        $runtime[] = 'TLS';
    $webserverInfo['modules'] = $runtime;
}
elseif ( isset( $_SERVER['SERVER_SOFTWARE'] ) && trim( (string)$_SERVER['SERVER_SOFTWARE'] ) !== '' )
{
    // apache_get_version() exists only in the Apache module SAPI, so this page
    // said it could not extract anything about the web server whenever the
    // application was not run that way -- under FPM behind Apache or nginx,
    // and under a persistent-worker server such as QbixServer, which runs it
    // inside a CLI-family SAPI. In every one of those cases the server does
    // say who it is, in SERVER_SOFTWARE, conventionally as "Name/version".
    $software = trim( (string)$_SERVER['SERVER_SOFTWARE'] );
    $slash = strpos( $software, '/' );
    $webserverInfo = array(
        'name'    => $slash === false ? $software : substr( $software, 0, $slash ),
        'version' => $slash === false ? '' : substr( $software, $slash + 1 ),
        'modules' => false
    );

    // There is no apache_get_modules() equivalent here, and inventing one
    // would be worse than saying nothing. What a reader actually wants from
    // this box is how the application is being run, so report that instead:
    // the SAPI, and whether the process handling this request will outlive it.
    $runtime = array( 'SAPI: ' . php_sapi_name() );
    if ( defined( 'QBIX_SERVER_VERSION' ) )
    {
        // A persistent worker handles many requests in one process, which is
        // the single most important thing to know when reading any other
        // number on this page: statics, registries and open handles survive
        // between requests here and do not under one process per request.
        //
        // The server can also run a fresh worker per request, and then none
        // of that is true. This said "persistent workers" either way.
        $forkPerRequest = class_exists( 'Q_Config', false )
                       && Q_Config::get( 'Q', 'webserver', 'forkPerRequest', false );
        $runtime[] = $forkPerRequest ? 'a fresh worker per request' : 'persistent workers';
        if ( class_exists( 'Q_WebServer_Compat', false ) )
            $runtime[] = 'source transform compatibility layer';
    }
    if ( eZSys::isSSLNow() )
        $runtime[] = 'TLS';
    $webserverInfo['modules'] = $runtime;
}
if ( $velocityBrand && $webserverInfo )
{
    // SERVER_SOFTWARE names the engine inside (QbixServer/1.5.0), not the
    // server that is running.
    $webserverInfo['name'] = $velocityBrand['name'];
    $webserverInfo['version'] = $velocityBrand['version'];
}

$tpl->setVariable( 'ezpublish_version', ExponentialSDK::version() . " (" . ExponentialSDK::alias() . ")" );
$tpl->setVariable( 'ezpublish_extensions', eZExtension::activeExtensions() );
$tpl->setVariable( 'php_version', phpversion() );
$tpl->setVariable( 'php_accelerator', $phpAcceleratorInfo );
// How this request is being executed, and out of what.
//
// The engine can be loaded from a thousand files on disk or from one archive,
// and which it is changes where a stack trace points, what a file listing
// means, and whether an edited kernel file has any effect at all. That is not
// something a person -- or an agent reading this page to find its bearings --
// should have to deduce from a path in an error message.
$engineInfo = array(
    'source'        => 'disk',
    'root'          => defined( 'EXP_ROOT_DIR' ) ? EXP_ROOT_DIR : '(not published)',
    'archive'       => '',
    'archive_built' => '',
    'archive_bytes' => 0,
    'archive_files' => 0,
    'version'       => '',
    'matches_repo'  => '',
    'phar_wrapper'  => in_array( 'phar', stream_get_wrappers() ) ? 'registered' : 'unregistered',
    'phar_readonly' => ini_get( 'phar.readonly' ) ? 'on' : 'off',
    'opcache'       => 'not loaded',
);

if ( function_exists( 'opcache_get_status' ) )
{
    $opcacheStatus = @opcache_get_status( false );
    $engineInfo['opcache'] = ( is_array( $opcacheStatus ) && !empty( $opcacheStatus['opcache_enabled'] ) )
                           ? 'enabled, ' . number_format( (int)$opcacheStatus['opcache_statistics']['num_cached_scripts'] ) . ' scripts cached'
                           : 'loaded but not enabled';
}

// How the archive is switched on and off, and how the running server picks up
// a rebuilt one, depends on the server this request came through: the switch
// is an environment variable, and every server hands one to PHP differently.
// This page said only that the variable was not set, which left a reader to
// work out where to set it for the server they happen to be running.
$engineArchivePath = '<archive>';
if ( !class_exists( 'expPhar' ) && file_exists( 'kernel/classes/expphar.php' ) )
    @include_once( 'kernel/classes/expphar.php' );
if ( class_exists( 'expPhar' ) )
    $engineArchivePath = expPhar::enginePath();
$velocityEngine = isset( $_SERVER['EXP_VELOCITY_ENGINE'] ) ? (string)$_SERVER['EXP_VELOCITY_ENGINE']
                : (string)getenv( 'EXP_VELOCITY_ENGINE' );
$serverSoftware = strtolower( isset( $_SERVER['SERVER_SOFTWARE'] ) ? (string)$_SERVER['SERVER_SOFTWARE'] : '' );
$velocitySwitch = array(
    'on'      => 'set [ServerSettings] EnginePhar=enabled (exp:velocity config set ServerSettings EnginePhar enabled), then exp:velocity restart',
    'off'     => 'set [ServerSettings] EnginePhar=disabled, then exp:velocity restart',
    'restart' => 'exp:velocity restart',
);
if ( defined( 'QBIX_SERVER_VERSION' ) )
{
    $engineServer = array( 'server' => $velocityBrand ? $velocityBrand['label'] : 'Exponential Velocity' ) + $velocitySwitch;
    $engineServer['on'] .= ' -- or, when the server is not started by exp:velocity, put EXP_ENGINE_PHAR='
                         . $engineArchivePath . ' in its environment';
}
elseif ( PHP_SAPI === 'frankenphp' && $velocityEngine !== '' )
{
    $engineServer = array( 'server' => ezpI18n::tr( 'design/admin/setup/info', 'FrankenPHP, started by exp:velocity' ) ) + $velocitySwitch;
}
elseif ( PHP_SAPI === 'cli-server' && $velocityEngine !== '' )
{
    $engineServer = array( 'server' => ezpI18n::tr( 'design/admin/setup/info', 'PHP\'s built-in web server, started by exp:velocity' ) ) + $velocitySwitch;
}
elseif ( PHP_SAPI === 'cli-server' )
{
    $engineServer = array(
        'server'  => ezpI18n::tr( 'design/admin/setup/info', 'PHP\'s built-in web server' ),
        'on'      => 'start php -S with EXP_ENGINE_PHAR=' . $engineArchivePath . ' in its environment',
        'off'     => 'start php -S without EXP_ENGINE_PHAR',
        'restart' => 'restart php -S',
    );
}
elseif ( PHP_SAPI === 'frankenphp' )
{
    $engineServer = array(
        'server'  => 'FrankenPHP',
        'on'      => 'add env EXP_ENGINE_PHAR "' . $engineArchivePath . '" to the php or php_server directive of the Caddyfile, then frankenphp reload',
        'off'     => 'remove env EXP_ENGINE_PHAR from the Caddyfile, then frankenphp reload',
        'restart' => 'frankenphp reload',
    );
}
elseif ( PHP_SAPI === 'apache2handler' || function_exists( 'apache_get_version' ) )
{
    $engineServer = array(
        'server'  => 'Apache (mod_php)',
        'on'      => 'SetEnv EXP_ENGINE_PHAR ' . $engineArchivePath . ' in the virtual host, then apachectl graceful',
        'off'     => 'remove the SetEnv EXP_ENGINE_PHAR line, then apachectl graceful',
        'restart' => 'apachectl graceful',
    );
}
elseif ( PHP_SAPI === 'fpm-fcgi' && strpos( $serverSoftware, 'nginx' ) === 0 )
{
    $engineServer = array(
        'server'  => 'nginx + php-fpm',
        'on'      => 'fastcgi_param EXP_ENGINE_PHAR ' . $engineArchivePath . '; in the location that passes requests to PHP,'
                   . ' then nginx -s reload (or env[EXP_ENGINE_PHAR] = ' . $engineArchivePath . ' in the php-fpm pool)',
        'off'     => 'remove the fastcgi_param (or env[]) line, then reload nginx (or php-fpm)',
        'restart' => 'reload php-fpm',
    );
}
elseif ( PHP_SAPI === 'fpm-fcgi' )
{
    $engineServer = array(
        'server'  => ( strpos( $serverSoftware, 'apache' ) === 0 ? 'Apache' : 'the web server' ) . ' + php-fpm',
        'on'      => 'env[EXP_ENGINE_PHAR] = ' . $engineArchivePath . ' in the php-fpm pool (or SetEnv in an Apache virtual host), then reload php-fpm',
        'off'     => 'remove the env[EXP_ENGINE_PHAR] line, then reload php-fpm',
        'restart' => 'reload php-fpm',
    );
}
else
{
    $engineServer = array(
        'server'  => PHP_SAPI,
        'on'      => 'put EXP_ENGINE_PHAR=' . $engineArchivePath . ' in the environment of the process that runs PHP, then restart it',
        'off'     => 'remove EXP_ENGINE_PHAR from that environment, then restart it',
        'restart' => 'restart the process that runs PHP',
    );
}
$engineInfo['server'] = $engineServer['server'];
$engineInfo['switch_on'] = $engineServer['on'];
$engineInfo['switch_off'] = $engineServer['off'];

if ( defined( 'EXP_ENGINE_PHAR' ) )
{
    $engineInfo['source'] = 'archive';
    $engineInfo['archive'] = EXP_ENGINE_PHAR;

    if ( file_exists( EXP_ENGINE_PHAR ) )
    {
        $engineInfo['archive_built'] = date( 'Y-m-d H:i:s', filemtime( EXP_ENGINE_PHAR ) );
        $engineInfo['archive_bytes'] = filesize( EXP_ENGINE_PHAR );
    }

    // Read through the wrapper the bootstrap deliberately keeps registered in
    // this mode; there is no other way to reach inside the archive.
    $engineVersion = @file_get_contents( 'phar://' . EXP_ENGINE_PHAR . '/ENGINE_VERSION' );
    if ( $engineVersion !== false )
        $engineInfo['version'] = trim( $engineVersion );

    $engineManifest = @include( 'phar://' . EXP_ENGINE_PHAR . '/MANIFEST.php' );
    if ( is_array( $engineManifest ) )
        $engineInfo['archive_files'] = count( $engineManifest );
}

// Whether the archive was built from what is on disk now. A mismatch is not an
// error -- the archive only has to carry the classes it carries -- but it is
// the first thing worth knowing when an edit to a kernel file appears to do
// nothing.
if ( class_exists( 'expPhar' ) || file_exists( 'kernel/classes/expphar.php' ) )
{
    if ( !class_exists( 'expPhar' ) )
        @include_once( 'kernel/classes/expphar.php' );

    if ( class_exists( 'expPhar' ) )
    {
        if ( $engineInfo['source'] === 'disk' )
        {
            $builtPath = expPhar::enginePath();
            if ( file_exists( $builtPath ) )
            {
                $engineInfo['archive'] = $builtPath . ' (built, not in use)';
                $engineInfo['archive_built'] = date( 'Y-m-d H:i:s', filemtime( $builtPath ) );
                $engineInfo['archive_bytes'] = filesize( $builtPath );

                // Read it even though it is not in use. An archive that no
                // longer matches the working tree is exactly what someone
                // needs to know before switching to it, and the wrapper is
                // unregistered in this mode, so the service class restores it
                // around the read and puts it back.
                $built = expPhar::info();
                if ( !empty( $built['data']['version'] ) )
                    $engineInfo['version'] = $built['data']['version'];
            }
            else
            {
                $engineInfo['archive'] = '(none built)';
            }
        }

        $repoVersion = expPhar::version();
        // Say what differs and what to do about it, not just that something
        // does. "NO" on its own leaves a reader to work out which of the two
        // versions is which, whether it matters, and what would put it right.
        $engineInfo['matches_repo'] = $engineInfo['version'] === ''
            ? $repoVersion . ' (no archive to compare)'
            : ( $engineInfo['version'] === $repoVersion
                ? 'yes, ' . $repoVersion
                : 'no' );

        $engineInfo['archive_version'] = $engineInfo['version'];
        $engineInfo['tree_version'] = $repoVersion;
        $engineInfo['stale_reason'] = '';
        $engineInfo['stale_fix'] = '';

        if ( $engineInfo['version'] !== '' && $engineInfo['version'] !== $repoVersion )
        {
            // The two strings are "<base>-<short sha>[-dirty]", so the parts
            // that differ say which kind of staleness this is.
            $archiveParts = explode( '-', $engineInfo['version'] );
            $treeParts    = explode( '-', $repoVersion );
            $archiveSha   = isset( $archiveParts[1] ) ? $archiveParts[1] : '';
            $treeSha      = isset( $treeParts[1] ) ? $treeParts[1] : '';
            $archiveDirty = in_array( 'dirty', $archiveParts, true );
            $treeDirty    = in_array( 'dirty', $treeParts, true );

            $reasons = array();
            if ( $archiveSha !== $treeSha && $archiveSha !== '' && $treeSha !== '' )
                $reasons[] = 'it was built from commit ' . $archiveSha
                           . ' and the working tree is now at ' . $treeSha;
            else if ( $archiveSha === $treeSha && ( $archiveDirty || $treeDirty ) )
                $reasons[] = 'it was built from the same commit, but files have been'
                           . ' edited since without being committed';
            else
                $reasons[] = 'the archive reports ' . $engineInfo['version']
                           . ' and the working tree reports ' . $repoVersion;

            if ( $treeDirty )
                $reasons[] = 'the working tree has uncommitted changes, so a rebuild'
                           . ' will capture whatever is on disk right now';

            $engineInfo['stale_reason'] = implode( '; ', $reasons );
            $engineInfo['stale_fix'] = 'php bin/php/phar.php build --allow-root-user'
                . ( $engineInfo['source'] === 'archive'
                    ? ', then ' . $engineServer['restart'] . ' so the server opens the new archive'
                    : '' );
        }
    }
}

// The two caches PHP keeps in shared memory, whatever serves the page: the
// opcode cache (compiled scripts) and APCu (data). Both belong to the process
// that answered this request -- a php-fpm pool, a Qbix server and its forked
// workers, a FrankenPHP process and its threads -- so a command-line script,
// another pool or another engine has its own, with other figures.
$phpCaches = array( 'opcache' => false, 'apcu' => false );
$megabytes = function ( $bytes ) { return number_format( $bytes / 1048576, 1 ) . ' MB'; };
if ( function_exists( 'opcache_get_status' ) )
{
    $opStatus = @opcache_get_status( false );
    $opConfig = function_exists( 'opcache_get_configuration' ) ? @opcache_get_configuration() : false;
    $directives = is_array( $opConfig ) && isset( $opConfig['directives'] ) ? $opConfig['directives'] : array();
    $enabled = is_array( $opStatus ) && !empty( $opStatus['opcache_enabled'] );
    $opcache = array(
        'enabled'  => $enabled,
        'version'  => is_array( $opConfig ) && isset( $opConfig['version']['version'] ) ? $opConfig['version']['version'] : '',
        'why_off'  => $enabled ? '' : ( ( PHP_SAPI === 'cli' && empty( $directives['opcache.enable_cli'] ) )
                                        ? 'opcache.enable_cli is off for this command-line server'
                                        : 'opcache.enable is off' ),
        'figures'  => array(),
        'bars'     => array(),
        'settings' => array(),
    );
    // The segment's size. Under FrankenPHP (PHP 8.5, ZTS) the configuration
    // reports memory_consumption as 0 and used_memory as minus the free
    // memory, while ini_get() has the real value and the segment is that
    // size; so the size is taken from ini_get() when the directive says 0.
    $segment = isset( $directives['opcache.memory_consumption'] ) ? (int)$directives['opcache.memory_consumption'] : 0;
    if ( $segment <= 0 )
    {
        $iniSize = trim( (string)ini_get( 'opcache.memory_consumption' ) );
        $segment = (int)$iniSize * ( preg_match( '/[gG]$/', $iniSize ) ? 1073741824 : 1048576 );
    }
    if ( $enabled )
    {
        $mem = $opStatus['memory_usage'];
        $stats = $opStatus['opcache_statistics'];
        $used = $mem['used_memory'] >= 0 ? $mem['used_memory']
              : max( 0, $segment - $mem['free_memory'] - $mem['wasted_memory'] );
        $total = $used + $mem['free_memory'] + $mem['wasted_memory'];
        $opcache['bars'] = array(
            array( 'label' => 'Memory', 'percent' => $total > 0 ? round( 100 * $used / $total ) : 0,
                   'text' => $megabytes( $used ) . ' of ' . $megabytes( $total )
                           . ( $mem['wasted_memory'] > 0 ? ', ' . $megabytes( $mem['wasted_memory'] ) . ' wasted' : '' ) ),
            array( 'label' => 'Scripts', 'percent' => $stats['max_cached_keys'] > 0 ? round( 100 * $stats['num_cached_scripts'] / $stats['max_cached_keys'] ) : 0,
                   'text' => number_format( $stats['num_cached_scripts'] ) . ' of ' . number_format( $stats['max_cached_keys'] ) . ' keys' ),
            array( 'label' => 'Hit rate', 'percent' => round( $stats['opcache_hit_rate'] ),
                   'text' => number_format( $stats['opcache_hit_rate'], 1 ) . ' % (' . number_format( $stats['hits'] ) . ' hits, '
                           . number_format( $stats['misses'] ) . ' misses)' ),
        );
        if ( isset( $opStatus['interned_strings_usage'] ) && $opStatus['interned_strings_usage']['buffer_size'] > 0 )
            $opcache['bars'][] = array( 'label' => 'Interned strings',
                'percent' => round( 100 * $opStatus['interned_strings_usage']['used_memory'] / $opStatus['interned_strings_usage']['buffer_size'] ),
                'text' => $megabytes( $opStatus['interned_strings_usage']['used_memory'] ) . ' of '
                        . $megabytes( $opStatus['interned_strings_usage']['buffer_size'] ) );
        $opcache['figures'] = array(
            'restarts' => ( $stats['oom_restarts'] + $stats['hash_restarts'] + $stats['manual_restarts'] ) === 0 ? 'none'
                        : $stats['oom_restarts'] . ' out of memory, ' . $stats['hash_restarts'] . ' hash table full, '
                          . $stats['manual_restarts'] . ' manual',
        );
        if ( !empty( $opStatus['restart_pending'] ) )
            $opcache['figures']['reset'] = 'pending -- it happens once the server has no request in progress';
        if ( !empty( $opStatus['cache_full'] ) )
            $opcache['figures']['cache full'] = 'yes -- raise opcache.memory_consumption or max_accelerated_files';
    }
    // What decides whether an edited or regenerated PHP file is picked up.
    $validate = !empty( $directives['opcache.validate_timestamps'] );
    $opcache['settings'] = array(
        'memory_consumption' => $segment > 0 ? $megabytes( $segment ) : '',
        'validate_timestamps' => $validate ? 'on' : 'off (an edited PHP file is not noticed until a restart)',
        'revalidate_freq' => isset( $directives['opcache.revalidate_freq'] ) ? $directives['opcache.revalidate_freq'] . ' s' : '',
        'jit' => !empty( $opStatus['jit']['enabled'] ) ? 'on' : 'off',
    );
    if ( ini_get( 'opcache.restrict_api' ) )
        $opcache['settings']['restrict_api'] = (string)ini_get( 'opcache.restrict_api' );
    $phpCaches['opcache'] = $opcache;
}
if ( function_exists( 'apcu_cache_info' ) )
{
    $enabled = function_exists( 'apcu_enabled' ) && apcu_enabled();
    $apcu = array(
        'enabled'  => $enabled,
        'version'  => (string)phpversion( 'apcu' ),
        'why_off'  => $enabled ? '' : ( ( PHP_SAPI === 'cli' && !ini_get( 'apc.enable_cli' ) )
                                        ? 'apc.enable_cli is off for this command-line server'
                                        : 'apc.enabled is off' ),
        'figures'  => array(),
        'bars'     => array(),
        'settings' => array(
            'shm_size'   => (string)ini_get( 'apc.shm_size' ),
            'ttl'        => (string)ini_get( 'apc.ttl' ) . ' s',
        ),
    );
    // Only a command-line server (Qbix, php -S) depends on enable_cli.
    if ( PHP_SAPI === 'cli' || PHP_SAPI === 'cli-server' )
        $apcu['settings']['enable_cli'] = ini_get( 'apc.enable_cli' ) ? 'on' : 'off';
    if ( $enabled )
    {
        $cacheInfo = @apcu_cache_info( true );
        $smaInfo = function_exists( 'apcu_sma_info' ) ? @apcu_sma_info( true ) : false;
        if ( is_array( $cacheInfo ) )
        {
            $hits = (int)$cacheInfo['num_hits'];
            $misses = (int)$cacheInfo['num_misses'];
            $size = is_array( $smaInfo ) ? $smaInfo['num_seg'] * $smaInfo['seg_size'] : 0;
            $taken = $size > 0 ? $size - $smaInfo['avail_mem'] : (int)$cacheInfo['mem_size'];
            $apcu['bars'] = array(
                array( 'label' => 'Memory', 'percent' => $size > 0 ? round( 100 * $taken / $size ) : 0,
                       'text' => $megabytes( $taken ) . ( $size > 0 ? ' of ' . $megabytes( $size ) : ' used' ) ),
                array( 'label' => 'Hit rate', 'percent' => ( $hits + $misses ) > 0 ? round( 100 * $hits / ( $hits + $misses ) ) : 0,
                       'text' => ( $hits + $misses ) > 0
                               ? number_format( 100 * $hits / ( $hits + $misses ), 1 ) . ' % (' . number_format( $hits ) . ' hits, '
                                 . number_format( $misses ) . ' misses)'
                               : 'no lookups yet' ),
            );
            $apcu['figures'] = array(
                'entries' => number_format( (int)$cacheInfo['num_entries'] ),
                'since'   => date( 'Y-m-d H:i:s', (int)$cacheInfo['start_time'] ),
            );
        }
    }
    $phpCaches['apcu'] = $apcu;
}
$tpl->setVariable( 'php_caches', $phpCaches );
$tpl->setVariable( 'can_flush_caches', $canFlushCaches );

// The Velocity engine this page is being served by, in detail: its role, its
// address, the views it answers itself and who may open them. A site runs one
// engine; the others of this installation that happen to be running as well
// -- a test setup -- are only named, in one line. Under Apache, php-fpm or a
// server exp:velocity does not know, there is no such box.
$velocityInfo = false;
$servingEngine = PHP_SAPI === 'frankenphp' ? 'frankenphp'
               : ( PHP_SAPI === 'cli-server' ? 'php'
               : ( defined( 'QBIX_SERVER_VERSION' ) ? 'qbix' : null ) );
if ( $servingEngine !== null && class_exists( 'expVelocity' ) )
{
    $velocity = expVelocity::create( 'velocity.ini', $servingEngine );

    // The port this request arrived on is the fact; the configured one says
    // whether exp:velocity started this server or something else did (a
    // hand-written php -S, a Qbix server run from its own script).
    // SERVER_PORT is the port of this request; Q_WebServer::$port is only the
    // plain-HTTP one, wrong when the page came in over HTTPS on its own port
    // (links came out as https://host:<http port>/ and failed).
    $servedPort = !empty( $_SERVER['SERVER_PORT'] )
                ? (int)$_SERVER['SERVER_PORT']
                : ( $servingEngine === 'qbix' && class_exists( 'Q_WebServer', false ) && Q_WebServer::$port
                    ? (int)Q_WebServer::$port : 0 );
    $configured = in_array( $servedPort, array_filter( array( $velocity->httpPort(), $velocity->httpsPort() ) ), true );
    $status = $configured ? $velocity->status() : array();

    $bind = $velocity->bindHost();
    $local = in_array( $bind, array( '', '127.0.0.1', 'localhost', '::1', '[::1]' ), true );
    $requestHost = preg_replace( '/:\d+$/', '', eZSys::hostname() );
    $scheme = eZSys::isSSLNow() ? 'https' : 'http';
    $base = $scheme . '://' . $requestHost . ':' . $servedPort;

    // A Qbix server says itself whether a token or remote access is set; that
    // wins over velocity.ini, which may not be what it was started with.
    $token = null;
    $remote = null;
    $panelPassword = false;
    if ( $servingEngine === 'qbix' && class_exists( 'Q_Config', false ) )
    {
        $dashboard = Q_Config::get( 'Q', 'dashboard', array() );
        $token = is_array( $dashboard ) && !empty( $dashboard['token'] );
        $remote = is_array( $dashboard ) && !empty( $dashboard['remote'] );
        // The panel class is loaded on its first request only; the file it
        // reads is APP_DIR/local/panel.json.
        if ( class_exists( 'Q_WebServer_Panel' ) )
            $panelPassword = Q_WebServer_Panel::hasPassword();
        elseif ( defined( 'APP_DIR' ) && is_readable( APP_DIR . '/local/panel.json' ) )
        {
            $panel = json_decode( (string)file_get_contents( APP_DIR . '/local/panel.json' ), true );
            $panelPassword = !empty( $panel['passwordHash'] );
        }
    }

    // Velocity's own server names itself with the brand and release worked
    // out once at the top ($velocityBrand).
    $ownServerVersion = $servingEngine === 'qbix' && $velocityBrand ? $velocityBrand['label'] : '';

    $views = array();
    foreach ( $velocity->views( $token, $remote, $panelPassword ) as $view )
        $views[] = array(
            'path'        => $view[0],
            // Only the server's own paths are links; a socket file is not.
            'url'         => strncmp( $view[0], '/Q/', 3 ) === 0 ? $base . $view[0] : '',
            'type'        => $view[1],
            'access'      => $view[2],
            'description' => $view[3],
        );

    // Other engines of this installation that are running too, with the
    // addresses they answer on (HTTPS first). A site serves from one; a second
    // one is usually left from a test or a benchmark.
    $others = array();
    foreach ( expVelocity::engines() as $other )
    {
        if ( $other === $servingEngine )
            continue;
        $engine = expVelocity::create( 'velocity.ini', $other );
        if ( !$engine->isRunning() )
            continue;
        $urls = array();
        if ( $engine->httpsPort() )
            $urls[] = 'https://' . $requestHost . ':' . (int)$engine->httpsPort() . '/';
        if ( $engine->httpPort() )
            $urls[] = 'http://' . $requestHost . ':' . (int)$engine->httpPort() . '/';
        $others[] = array(
            'name' => isset( $velocityEngineNames[$other] ) ? $velocityEngineNames[$other] : $other,
            'engine' => $other,
            'role' => $engine->role(),
            'urls' => $urls,
        );
    }

    $roleText = array(
        'recommended'  => ezpI18n::tr( 'design/admin/setup/info', 'recommended for every stage -- development, alpha, beta, demo, stable and production -- and the fastest of the engines' ),
        'production'   => ezpI18n::tr( 'design/admin/setup/info', 'production-ready; Velocity\'s own server (qbix) is the recommended engine' ),
        'development'  => ezpI18n::tr( 'design/admin/setup/info', 'development only -- for any other stage, run Velocity\'s own server (qbix)' ),
    );
    $velocityInfo = array(
        'engine'     => $servingEngine,
        'role'       => $velocity->role(),
        'role_text'  => isset( $roleText[$velocity->role()] ) ? $roleText[$velocity->role()] : $velocity->role(),
        'is_default' => $velocity->isDefault(),
        'default'    => expVelocity::defaultEngine(),
        'velocity'   => $configured,
        'url'        => $base . '/',
        'port'       => $servedPort,
        'bind'       => $bind === '' ? '127.0.0.1' : $bind,
        // Only known for a server started from velocity.ini; one started by
        // hand binds wherever its command line said.
        'reach'      => !$configured ? ezpI18n::tr( 'design/admin/setup/info', 'wherever it was started to listen (not by exp:velocity, so its Host is not known here)' )
                        : ( $local ? ezpI18n::tr( 'design/admin/setup/info', 'this machine only (Host=%host), or through a proxy or tunnel', null, array( '%host' => ( $bind === '' ? '127.0.0.1' : $bind ) ) )
                                   : ezpI18n::tr( 'design/admin/setup/info', 'every machine that reaches %address', null, array( '%address' => $bind . ':' . $servedPort ) ) ),
        'version'    => $ownServerVersion !== '' ? $ownServerVersion
                        : ( isset( $status['version'] ) && $status['version'] !== '' ? $status['version'] : '' ),
        'engine_name' => isset( $velocityEngineNames[$servingEngine] ) ? $velocityEngineNames[$servingEngine] : $servingEngine,
        'brand'      => $velocityBrand ? $velocityBrand['name'] : 'Exponential Velocity',
        'pid'        => isset( $status['parent'] ) && $status['parent'] ? $status['parent'] : '',
        'processes'  => isset( $status['processes'] ) ? $status['processes'] : 0,
        'config'     => isset( $status['caddyfile'] ) ? $status['caddyfile'] : '',
        'log'        => isset( $status['log'] ) ? $status['log'] : '',
        'notes'      => method_exists( $velocity, 'ignoredSettings' ) ? $velocity->ignoredSettings() : array(),
        'views'      => $views,
        'others'     => $others,
    );
}
$tpl->setVariable( 'velocity_info', $velocityInfo );

// The response cache in front of this installation, when a Qbix server runs it.
//
// A hit never reaches PHP, so nothing else on this page can say whether the
// cache is on, what it keeps or how much of the traffic it answers. This
// request runs in a worker the server forked after reading its configuration
// and initialising the cache, so what the worker holds are the settings the
// running server actually uses -- including the defaults it filled in for
// anything the configuration left out, which the configuration file cannot
// show. Velocity, a hand-written server.json and a preset all end up here.
$responseCache = false;
if ( defined( 'QBIX_SERVER_VERSION' ) && class_exists( 'Q_WebServer_Cache', false ) )
{
    $config = class_exists( 'Q_Config', false )
            ? (array)Q_Config::get( 'Q', 'web', 'cache', array() ) : array();
    $sweep = isset( $config['sweep'] ) ? (array)$config['sweep'] : array();

    // APCu is shared memory created before the fork, so this process sees the
    // entries the parent stored -- provided the extension is switched on for
    // command-line PHP, which it is not unless apc.enable_cli says so.
    $apcuUsable = function_exists( 'apcu_enabled' ) && apcu_enabled();

    $responseCache = array(
        'enabled'              => (bool)Q_WebServer_Cache::$enabled,
        'default_ttl'          => (int)Q_WebServer_Cache::$defaultTtl,
        'dir'                  => (string)Q_WebServer_Cache::$dir,
        'apcu_configured'      => (bool)Q_WebServer_Cache::$apcuEnabled,
        'apcu_usable'          => $apcuUsable,
        'apcu_max_size'        => (int)Q_WebServer_Cache::$apcuMaxSize,
        'skip_cookies'         => array_values( (array)Q_WebServer_Cache::$skipCookies ),
        'stale_while_revalidate' => (int)Q_WebServer_Cache::$staleWhileRevalidate,
        'negative_ttl'         => (int)Q_WebServer_Cache::$negativeTtl,
        'minify_html'          => (bool)Q_WebServer_Cache::$minifyHtml,
        'sweep_every'          => isset( $sweep['every'] ) ? (int)$sweep['every'] : 300,
        'sweep_max_age'        => isset( $sweep['maxAge'] ) ? (int)$sweep['maxAge'] : 0,
        'file_mode'            => isset( $config['fileMode'] ) ? (string)$config['fileMode'] : '',
        'dir_mode'             => isset( $config['dirMode'] ) ? (string)$config['dirMode'] : '',
        'apcu_entries'         => false,
        'apcu_bytes'           => 0,
        'apcu_segment'         => 0,
        'apcu_free'            => 0,
        'file_readable'        => false,
        'file_entries'         => 0,
        'file_bytes'           => 0,
        'file_counted_all'     => true,
        'stats_available'      => false,
        'hits'                 => 0,
        'misses'               => 0,
        'hit_rate'             => 0,
    );

    // What is in shared memory. The validator index sits under qcache:v:
    // beside the pages and is counted apart from them.
    if ( $apcuUsable )
    {
        $info = @apcu_cache_info( false );
        if ( is_array( $info ) && isset( $info['cache_list'] ) )
        {
            $entries = 0;
            $bytes = 0;
            foreach ( $info['cache_list'] as $item )
            {
                $key = isset( $item['info'] ) ? (string)$item['info']
                     : ( isset( $item['key'] ) ? (string)$item['key'] : '' );
                if ( strpos( $key, 'qcache:' ) !== 0 || strpos( $key, 'qcache:v:' ) === 0 )
                    continue;
                $entries++;
                $bytes += isset( $item['mem_size'] ) ? (int)$item['mem_size'] : 0;
            }
            $responseCache['apcu_entries'] = $entries;
            $responseCache['apcu_bytes'] = $bytes;
        }
        $sma = @apcu_sma_info( true );
        if ( is_array( $sma ) )
        {
            $responseCache['apcu_segment'] = (int)$sma['num_seg'] * (int)$sma['seg_size'];
            $responseCache['apcu_free'] = (int)$sma['avail_mem'];
        }
    }

    // What is on disk. Every entry is written there, whatever APCu holds,
    // because shared memory does not survive a restart. Counted up to a limit
    // so that a large cache does not make this page slow; past it the page
    // says "at least".
    $dir = $responseCache['dir'];
    if ( $dir !== '' && is_dir( $dir ) )
    {
        $entries = 0;
        $bytes = 0;
        $limit = 20000;
        try
        {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ) );
            foreach ( $files as $file )
            {
                if ( !$file->isFile() )
                    continue;
                if ( ++$entries > $limit )
                {
                    $entries = $limit;
                    $responseCache['file_counted_all'] = false;
                    break;
                }
                $bytes += $file->getSize();
            }
        }
        catch ( Exception $e )
        {
            // An unreadable directory is reported as no count, not as an error.
            $entries = false;
        }
        $responseCache['file_readable'] = $entries !== false;
        $responseCache['file_entries'] = (int)$entries;
        $responseCache['file_bytes'] = $bytes;
    }

    // Hits and misses are counted by the parent, which answers the hits; this
    // worker's copy of the counters stopped at the fork. The parent publishes
    // them at /Q/stats, answered in its own event loop, so asking costs this
    // request a loopback round trip and no second worker.
    //
    // The port is the server's own, not SERVER_PORT: requests that go through
    // the worker pool are handed SERVER_PORT=8080 whatever the server listens
    // on, because the pool reads it from the parent's $_SERVER, which a
    // command-line process does not fill in.
    $port = class_exists( 'Q_WebServer', false ) ? (int)Q_WebServer::$port : 0;
    if ( $responseCache['enabled'] && $port > 0 )
    {
        $context = stream_context_create( array( 'http' => array( 'timeout' => 1 ) ) );
        $stats = json_decode( (string)@file_get_contents(
            'http://127.0.0.1:' . $port . '/Q/stats', false, $context ), true );
        if ( is_array( $stats ) && isset( $stats['cache']['hits'] ) )
        {
            $responseCache['stats_available'] = true;
            $responseCache['hits'] = (int)$stats['cache']['hits'];
            $responseCache['misses'] = (int)$stats['cache']['misses'];
            $responseCache['hit_rate'] = round( (float)$stats['cache']['hitRate'], 1 );
        }
    }
}

$tpl->setVariable( 'response_cache', $responseCache );

// The role-aware HTTP cache (settings/httpcache.ini). Its hits are answered
// before the kernel boots, so this is the one place that shows them.
$httpCache = false;
if ( class_exists( 'ezpHttpCacheContract' ) )
{
    $hcIni = eZINI::instance( 'httpcache.ini' );
    $hcDir = eZSys::cacheDirectory() . '/exphttpcache';
    $hcContract = ezpHttpCacheContract::fromDir( $hcDir );
    $hcMessage = '';

    if ( $hcContract && $canFlushCaches && $http->hasPostVariable( 'HttpCacheAction' ) )
    {
        switch ( $http->postVariable( 'HttpCacheAction' ) )
        {
            case 'purge':
                $hcContract->bumpGeneration();
                $hcMessage = ezpI18n::tr( 'design/admin/setup/info', 'Every cached page was purged; each is rendered again on its next request.' );
                break;
            case 'gc':
                $c = $hcContract->gc();
                $hcMessage = ezpI18n::tr( 'design/admin/setup/info', 'Removed %entries dead entries, %bodies orphaned bodies and %records old user records.', null,
                    array( '%entries' => $c['entries'], '%bodies' => $c['bodies'], '%records' => $c['records'] ) );
                break;
            case 'reset':
                $hcContract->resetStatistics();
                $hcMessage = ezpI18n::tr( 'design/admin/setup/info', 'The counters were reset.' );
                break;
        }
    }

    $hcEnabled = $hcIni->variable( 'HttpCacheSettings', 'Enabled' ) === 'enabled';
    $httpCache = array(
        'enabled'      => $hcEnabled,
        // Enabled in the settings but no page stored yet: the contract is
        // written by the first request on a cached siteaccess.
        'started'      => (bool)$hcContract,
        'message'      => $hcMessage,
        'dir'          => $hcDir,
        'bars'         => array(),
        'figures'      => array(),
        'settings'     => array(),
        'reasons'      => array(),
        'stats'        => false,
    );
    if ( $hcContract )
    {
        $inv = $hcContract->inventory();
        $stats = $hcContract->statistics();
        $when = function ( $t ) {
            return $t > 0 ? date( 'Y-m-d H:i:s', (int)$t ) : ezpI18n::tr( 'design/admin/setup/info', 'never' );
        };
        if ( $stats !== null )
        {
            $lookups = $stats['hits'] + $stats['misses'];
            $httpCache['stats'] = true;
            $httpCache['bars'][] = array(
                'label' => 'Hit rate',
                'percent' => $lookups > 0 ? round( 100 * $stats['hits'] / $lookups ) : 0,
                'text' => $lookups > 0
                    ? number_format( 100 * $stats['hits'] / $lookups, 1 ) . ' % (' . number_format( $stats['hits'] ) . ' hits, ' . number_format( $stats['misses'] ) . ' misses)'
                    : ezpI18n::tr( 'design/admin/setup/info', 'no lookups yet' ),
            );
            $httpCache['figures']['pages stored'] = number_format( $stats['stores'] );
            $httpCache['figures']['counting since'] = $when( $stats['since'] );
            $httpCache['figures']['servers counting'] = $stats['servers'];
            foreach ( array_slice( $stats['reasons'], 0, 8, true ) as $reason => $n )
                $httpCache['reasons'][] = array( 'reason' => $reason, 'count' => number_format( $n ),
                    'percent' => $stats['misses'] > 0 ? round( 100 * $n / $stats['misses'] ) : 0 );
        }
        $maxBody = (int)$hcContract->config['maxBodySize'];
        $httpCache['figures'] = array(
            'entries' => ( $inv['counted_all'] ? '' : '≥ ' ) . number_format( $inv['entries'] ),
            'on disk' => number_format( $inv['bytes'] / 1048576, 1 ) . ' MB',
            'user contexts' => number_format( $inv['records'] ),
            'generation' => $inv['generation'] . ' (' . $when( $inv['generation_time'] ) . ')',
            'purged tags' => number_format( $inv['purged_tags'] ),
            'last purge' => $when( $inv['last_purge'] ),
        ) + $httpCache['figures'];
        $httpCache['settings'] = array(
            'CachedSiteAccesses' => implode( ', ', (array)$hcIni->variable( 'HttpCacheSettings', 'CachedSiteAccesses' ) ),
            'hosts' => implode( ', ', array_keys( (array)$hcContract->config['hosts'] ) ),
            'MaxAge' => (int)$hcContract->config['maxAge'] . ' s',
            'ContentChangePurges' => (string)$hcIni->variable( 'HttpCacheSettings', 'ContentChangePurges' ),
            'sessions' => $hcContract->config['sessionSavePath'] !== ''
                ? ezpI18n::tr( 'design/admin/setup/info', 'files (signed-in visitors cached)' )
                : ezpI18n::tr( 'design/admin/setup/info', 'not readable before the kernel (signed-in visitors not served early)' ),
            'APCu' => ( $hcContract->config['apcu'] ? 'on' : 'off' ) . ( $stats === null ? ' (' . ezpI18n::tr( 'design/admin/setup/info', 'not usable in this PHP' ) . ')' : '' ),
            'ProxyHeaders' => $hcContract->config['proxyHeaders'] ? 'xkey, Surrogate-Key' : 'off',
            'MaxBodySize' => number_format( $maxBody / 1048576, 1 ) . ' MB',
        );
    }
}
$tpl->setVariable( 'http_cache', $httpCache );

// Database queries: the SQL query cache (eZDBQueryCache, settings/querycache.ini)
// and the SQL profile of recent requests (eZDBInterface::profileSQL(),
// var/tmp/sql_profile.on). See doc/bc/6.0/sql-query-cache.md.
// SQL engines only; the MongoDB driver keeps its own profile.
$sqlProfile = false;
$dbClass = get_class( $db );
$isMongo = stripos( $dbClass, 'mongo' ) !== false;
$varTmp = eZSys::rootDir() . '/var/tmp';
$sqlSentinel = $varTmp . '/sql_profile.on';
$sqlMessage = '';
if ( !$isMongo && $canFlushCaches && $http->hasPostVariable( 'SQLProfileAction' ) )
{
    if ( $http->postVariable( 'SQLProfileAction' ) === 'on' )
    {
        if ( @touch( $sqlSentinel ) )
        {
            // Every server writes the log, whoever it runs as.
            @chmod( $sqlSentinel, 0666 );
            $sqlMessage = ezpI18n::tr( 'design/admin/setup/info', 'The SQL profile is on: every request now adds a line.' );
        }
    }
    else if ( $http->postVariable( 'SQLProfileAction' ) === 'off' )
        $sqlMessage = ( !is_file( $sqlSentinel ) || @unlink( $sqlSentinel ) )
            ? ezpI18n::tr( 'design/admin/setup/info', 'The SQL profile is off.' ) : '';
}
if ( !$isMongo && $canFlushCaches && class_exists( 'eZDBQueryCache' ) && $http->hasPostVariable( 'QueryCacheAction' ) )
{
    if ( $http->postVariable( 'QueryCacheAction' ) === 'clear' )
    {
        eZDBQueryCache::clearAll();
        $sqlMessage = ezpI18n::tr( 'design/admin/setup/info', 'The SQL query cache was cleared.' );
    }
    else if ( $http->postVariable( 'QueryCacheAction' ) === 'reset' )
    {
        eZDBQueryCache::resetStats();
        $sqlMessage = ezpI18n::tr( 'design/admin/setup/info', 'The query cache counters of this server were reset.' );
    }
}
$queryCache = false;
if ( !$isMongo && class_exists( 'eZDBQueryCache' ) )
{
    // This request's own lookups are not in the totals yet: add them first.
    eZDBQueryCache::flushStats();
    $queryCache = eZDBQueryCache::status();
    $recent = array();
    foreach ( $queryCache['recent_writes'] as $table => $when )
        $recent[] = array( 'table' => $table, 'when' => $when, 'ago' => max( 0, time() - (int)$when ) );
    $queryCache['recent_writes'] = $recent;
    $queryCache['memory_kb'] = round( $queryCache['memory'] / 1024 );
}
$tpl->setVariable( 'query_cache', $queryCache );
$sqlProfile = array(
    'engine'  => $dbClass,
    'mongo'   => $isMongo,
    'on'      => !$isMongo && is_file( $sqlSentinel ),
    'message' => $sqlMessage,
    'rows'    => array(),
    'summary' => array(),
);
if ( !$isMongo && is_readable( $varTmp . '/sql_profile.log' ) )
{
    $lines = @file( $varTmp . '/sql_profile.log', FILE_IGNORE_NEW_LINES ) ?: array();
    $rows = array();
    foreach ( array_reverse( $lines ) as $line )
    {
        if ( !preg_match( '/^(\d\d:\d\d:\d\d)\s+(\S+)\s+(\d+) statements\s+(\d+) selects\s+(\d+) distinct\s+(\d+) repeats \| db\s+([\d.]+) ms, repeats\s+([\d.]+) ms/', $line, $m ) )
            continue;
        $statements = (int)$m[3];
        $db_ms = (float)$m[7];
        // Design B: a warm shared cache answers each statement in ~15 µs.
        $shared = max( 0.0, $db_ms - $statements * 0.015 );
        $rows[] = array( 'time' => $m[1], 'uri' => $m[2], 'statements' => $statements, 'distinct' => (int)$m[5],
                         'repeats' => (int)$m[6], 'db_ms' => round( $db_ms, 1 ), 'memo_ms' => round( (float)$m[8], 1 ),
                         'shared_ms' => round( $shared, 1 ) );
        if ( count( $rows ) >= 12 )
            break;
    }
    $sqlProfile['rows'] = $rows;
    if ( $rows )
    {
        $n = count( $rows );
        $sum = function ( $k ) use ( $rows ) { return array_sum( array_column( $rows, $k ) ); };
        $sqlProfile['summary'] = array(
            'requests'   => $n,
            'statements' => round( $sum( 'statements' ) / $n ),
            'repeats'    => round( $sum( 'repeats' ) / $n ),
            'repeat_pct' => $sum( 'statements' ) ? round( 100 * $sum( 'repeats' ) / $sum( 'statements' ) ) : 0,
            'db_ms'      => round( $sum( 'db_ms' ) / $n, 1 ),
            'memo_ms'    => round( $sum( 'memo_ms' ) / $n, 1 ),
            'shared_ms'  => round( $sum( 'shared_ms' ) / $n, 1 ),
        );
    }
}
$tpl->setVariable( 'sql_profile', $sqlProfile );
$tpl->setVariable( 'engine_info', $engineInfo );
$tpl->setVariable( 'webserver_info', $webserverInfo );
$tpl->setVariable( 'database_info', $db->databaseName() );
$tpl->setVariable( 'database_charset', $db->charset() );
$tpl->setVariable( 'database_object', $db );
$tpl->setVariable( 'php_loaded_extensions', get_loaded_extensions() );
$tpl->setVariable( 'autoload_functions', spl_autoload_functions() );

// Workaround until ezcTemplate
// The new system info class uses properties instead of attributes, so the
// values are not immediately available in the old template engine.
$tpl->setVariable( 'system_info', $systemInfo );

$phpINI = array();
foreach ( array( 'safe_mode', 'register_globals', 'file_uploads' ) as $iniName )
{
    $phpINI[ $iniName ] = ini_get( $iniName ) != 0;
}
foreach ( array( 'open_basedir', 'post_max_size', 'memory_limit', 'max_execution_time' ) as $iniName )
{
    $value = ini_get( $iniName );
    if ( $value !== '' )
        $phpINI[$iniName] = $value;
}
$tpl->setVariable( 'php_ini', $phpINI );

$Result = array();
$Result['content'] = $tpl->fetch( "design:setup/info.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'kernel/setup', 'System information' ) ) );

?>
