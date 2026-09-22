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
        $runtime[] = 'persistent workers';
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
