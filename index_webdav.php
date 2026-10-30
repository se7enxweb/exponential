<?php
/**
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*
 * The WebDAV server. Named index_webdav.php like the other entry scripts at the root (index_rest.php,
 * index_treemenu.php, index_cluster.php); webdav.php, its name until 6.0.14, only includes this file and
 * will be removed in a later major version.
 */

// Set a default time zone if none is given. The time zone can be overriden
// in config.php or php.ini.
if ( !ini_get( "date.timezone" ) )
{
    date_default_timezone_set( "UTC" );
}

require 'autoload.php';

ignore_user_abort( true );
ob_start();

ini_set( "display_errors" , "0" );

error_reporting ( E_ALL );

// Turn off session stuff, isn't needed for WebDAV operations.
$GLOBALS['eZSiteBasics']['session-required'] = false;

/*! Reads settings from site.ini and passes them to eZDebug.
 */
function eZUpdateDebugSettings()
{
    $ini = eZINI::instance();
    $debugSettings = array();
    $debugSettings['debug-enabled'] = $ini->variable( 'DebugSettings', 'DebugOutput' ) == 'enabled';
    $debugSettings['debug-by-ip']   = $ini->variable( 'DebugSettings', 'DebugByIP' )   == 'enabled';
    $debugSettings['debug-ip-list'] = $ini->variable( 'DebugSettings', 'DebugIPList' );
    eZDebug::updateSettings( $debugSettings );
}

/*!
 Reads settings from i18n.ini and passes them to eZTextCodec.
*/
function eZUpdateTextCodecSettings()
{
    $ini = eZINI::instance( 'i18n.ini' );

    list( $i18nSettings['internal-charset'], $i18nSettings['http-charset'], $i18nSettings['mbstring-extension'] ) =
        $ini->variableMulti( 'CharacterSettings', array( 'Charset', 'HTTPCharset', 'MBStringExtension' ), array( false, false, 'enabled' ) );

    eZTextCodec::updateSettings( $i18nSettings );
}

// Initialize text codec settings
eZUpdateTextCodecSettings();

// Check for extension
eZExtension::activateExtensions( 'default' );
// Extension check end

// Now that all extensions are activated and siteaccess has been changed, reset
// all eZINI instances as they may not take into account siteaccess specific settings.
eZINI::resetAllInstances( false );

// Grab the main WebDAV setting (enable/disable) from the WebDAV ini file.
$webDavIni = eZINI::instance( 'webdav.ini' );
$enable = $webDavIni->variable( 'GeneralSettings', 'EnableWebDAV' );

function eZDBCleanup()
{
    if ( class_exists( 'ezdb' )
         and eZDB::hasInstance() )
    {
        $db = eZDB::instance();
        $db->setIsSQLOutputEnabled( false );
    }
}

function eZFatalError()
{
    eZDebug::setHandleType( eZDebug::HANDLE_NONE );
    eZWebDAVContentBackend::appendLogEntry( "****************************************" );
    eZWebDAVContentBackend::appendLogEntry( "Fatal error: Exponential did not finish its request" );
    eZWebDAVContentBackend::appendLogEntry( "The execution of Exponential was abruptly ended, the debug output is present below." );
    eZWebDAVContentBackend::appendLogEntry( "****************************************" );
    // $templateResult = null;
    // eZDisplayResult( $templateResult );
}

// Check and proceed only if WebDAV functionality is enabled:
if ( $enable === 'true' )
{
    eZExecution::addCleanupHandler( 'eZDBCleanup' );
    eZExecution::addFatalErrorHandler( 'eZFatalError' );
    eZDebug::setHandleType( eZDebug::HANDLE_FROM_PHP );

    if ( !isset( $_SERVER['REQUEST_URI'] ) or
         !isset( $_SERVER['REQUEST_METHOD'] ) )
    {
        // We stop the script if these are missing
        // e.g. if run from the shell
        eZExecution::cleanExit();
    }

    // The name this request came in through: index_webdav.php, or webdav.php through the forwarding shim.
    // eZSys::init() needs the file actually run to find the root and the index directory.
    $webdavEntryScript = expWebDAVEntryScript::current();

    eZModule::setGlobalPathList( array( "kernel" ) );
    eZWebDAVContentBackend::appendLogEntry( "========================================" );
    eZWebDAVContentBackend::appendLogEntry( "Requested URI is: " . $_SERVER['REQUEST_URI'], $webdavEntryScript );

    $ini = eZINI::instance( 'site.ini' );
    // Initialize/set the index file.
    eZSys::init( $webdavEntryScript, $ini->variable( 'SiteAccessSettings', 'ForceVirtualHost' ) === 'true' );

    if ( $webdavEntryScript === expWebDAVEntryScript::DEPRECATED_SCRIPT )
    {
        // One warning a day in warning.log until the virtual host and rewrite rules name index_webdav.php
        expWebDAVEntryScript::noteDeprecatedEntry();
    }

    // @as 2009-03-04 - added cleaning up of the REQUEST_URI and HTTP_DESTINATION
    $_SERVER['REQUEST_URI'] = urldecode( $_SERVER['REQUEST_URI'] );

    if ( isset( $_SERVER['HTTP_DESTINATION'] ) )
    {
        $_SERVER['HTTP_DESTINATION'] = urldecode( $_SERVER['HTTP_DESTINATION'] );
    }
    eZWebDAVContentBackend::appendLogEntry( "Used (cleaned) URI is: " . $_SERVER['REQUEST_URI'], $webdavEntryScript );

    // The top/root folder is publicly available (without auth): "", "/", "/index_webdav.php[/]"
    // and, through the shim, "/webdav.php[/]".
    if ( expWebDAVEntryScript::isRootRequest( $_SERVER['REQUEST_URI'] ) )
    {
        $server = ezcWebdavServer::getInstance();
        eZWebDAVContentBackend::usePathFactory( $server );

        $backend = new eZWebDAVContentBackend();
        // without an authorizer nothing checked what a request for the root could list; with it the
        // children of the root go through eZWebDAVContentBackendAuth::authorize() (user/login policy) too.
        $server->auth = new eZWebDAVContentBackendAuth();
        $server->handle( $backend );
    }
    // Else: need to login with username/password:
    else
    {
        // Create & initialize a new instance of the content server.
        $server = ezcWebdavServer::getInstance();
        eZWebDAVContentBackend::usePathFactory( $server );
        $server->pluginRegistry->registerPlugin(
            new ezcWebdavLockPluginConfiguration()
        );
        $backend = new eZWebDAVContentBackend();
        $server->auth = new eZWebDAVContentBackendAuth();

        // Get the name of the site that is being browsed.
        $currentSite = $backend->currentSiteFromPath( $_SERVER['REQUEST_URI'] );

        // Proceed only if the current site is valid:
        if ( $currentSite )
        {
            $backend->setCurrentSite( $currentSite );

            // Process the request.
            $server->handle( $backend );
        }
        // Else: site-name is invalid (was not among available sites).
        else
        {
            header( "HTTP/1.1 404 Not Found" );
        }
    }

    eZExecution::cleanExit();
}
// Else: WebDAV functionality is disabled, do nothing...
else
{
    header( "HTTP/1.1 404 Not Found" );
    print ( "WebDAV functionality is disabled!" );
}

?>
