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

$webserverInfo = false;
if ( function_exists( 'apache_get_version' ) )
{
    $webserverInfo = array( 'name' => 'Apache',
                            'modules' => false,
                            'version' => apache_get_version() );
    if ( function_exists( 'apache_get_modules' ) )
        $webserverInfo['modules'] = apache_get_modules();
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

$tpl->setVariable( 'ezpublish_version', eZPublishSDK::version() . " (" . eZPublishSDK::alias() . ")" );
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
                    ? ', then restart the application server so it opens the new archive'
                    : '' );
        }
    }
}

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
