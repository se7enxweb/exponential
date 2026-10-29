<?php
/**
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \brief The SOAP entry script: answers every SOAP request of the installation.

  It was called soap.php until Exponential 6.0.15 and is named like the other
  front controllers now (index.php, index_rest.php, index_treemenu.php), so one
  rule in the web server can let all of them through. soap.php stays as a shim
  that only includes this file; see doc/eng-us/bc/6.0/soap-entry-script-index-soap.md.

  The server is off unless settings/soap.ini [GeneralSettings] EnableSOAP=true.
  The functions it offers come from the extensions in
  [ExtensionSettings] SOAPExtensions[] (extension/<name>/soap/initialize.php).
  A second sign-in step never applies here: an account that needs one signs in
  with an app password.
*/

ob_start();

ini_set( "display_errors" , "0" );

// Set a default time zone if none is given. The time zone can be overriden
// in config.php or php.ini.
if ( !ini_get( "date.timezone" ) )
{
    date_default_timezone_set( "UTC" );
}

require 'autoload.php';

/*!
 Reads settings from site.ini and passes them to eZDebug.
*/
function eZUpdateDebugSettings()
{
    $ini = eZINI::instance();

    list( $debugSettings['debug-enabled'], $debugSettings['debug-by-ip'], $debugSettings['debug-by-user'], $debugSettings['debug-ip-list'], $debugSettings['debug-user-list'] ) =
        $ini->variableMulti( 'DebugSettings', array( 'DebugOutput', 'DebugByIP', 'DebugByUser', 'DebugIPList', 'DebugUserIDList' ), array ( 'enabled', 'enabled', 'enabled' ) );
    eZDebug::updateSettings( $debugSettings );
}

$ini = eZINI::instance();

// Initialize/set the index file: the script the request named, index_soap.php,
// or soap.php when it came through the forwarding shim of that name (the URIs
// and the siteaccess match are read relative to it).
$soapEntryScript = isset( $_SERVER['SCRIPT_FILENAME'] ) && basename( (string)$_SERVER['SCRIPT_FILENAME'] ) === 'soap.php'
    ? 'soap.php'
    : 'index_soap.php';
eZSys::init( $soapEntryScript,$ini->variable( 'SiteAccessSettings', 'ForceVirtualHost' ) === 'true' );
$uri = eZURI::instance( eZSys::requestURI() );
$GLOBALS['eZRequestedURI'] = $uri;

// Check for extension
eZExtension::activateExtensions( 'default' );
// Extension check end

// Activate correct siteaccess
$soapINI = eZINI::instance( 'soap.ini' );
if ( $soapINI->variable( 'GeneralSettings', 'UseDefaultAccess' ) === 'enabled' )
{
    $access = array( 'name' => $ini->variable( 'SiteSettings', 'DefaultAccess' ),
                     'type' => eZSiteAccess::TYPE_DEFAULT );
}
else
{
    $access = eZSiteAccess::match( $uri,
                          eZSys::hostname(),
                          eZSys::serverPort(),
                          eZSys::indexFile() );
}
$access = eZSiteAccess::change( $access );
// Siteaccess activation end

// Check for activating Debug by user ID (Final checking. The first was in eZDebug::updateSettings())
eZDebug::checkDebugByUser();

// Check for siteaccess extension
eZExtension::activateExtensions( 'access' );
// Siteaccess extension check end

// Now that all extensions are activated and siteaccess has been changed, reset
// all eZINI instances as they may not take into account siteaccess specific settings.
eZINI::resetAllInstances( false );

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

// Initialize module loading
$moduleRepositories = eZModule::activeModuleRepositories();
eZModule::setGlobalPathList( $moduleRepositories );

// Load soap extensions
$enableSOAP = $soapINI->variable( 'GeneralSettings', 'EnableSOAP' );

if ( $enableSOAP == 'true' )
{
    // Login if we have username and password.
    // An app password of the account (two-step sign-in, expSignInSecondFactor) signs in for this request only
    $soapAppUser = null;
    if ( eZHTTPTool::username() and eZHTTPTool::password() and class_exists( 'expSignInSecondFactor' ) )
    {
        $soapAppUser = expSignInSecondFactor::appPasswordUser( eZHTTPTool::username(), eZHTTPTool::password(), expSignInSecondFactor::CHANNEL_SOAP );
        // an account marked "change password at next sign-in" sets it in the web interface first
        if ( $soapAppUser instanceof eZUser && !( class_exists( 'expPasswordChangeRequired' ) && expPasswordChangeRequired::refusesSignIn( $soapAppUser, 'soap' ) ) )
            expSignInSecondFactor::signInForRequest( $soapAppUser );
    }
    if ( !$soapAppUser instanceof eZUser and eZHTTPTool::username() and eZHTTPTool::password() )
    {
        $soapUser = eZUser::loginUser( eZHTTPTool::username(), eZHTTPTool::password() );
        // a password alone is not enough for an account that needs a second sign-in step: 401 (403 when it has to
        // set the step up first) with WWW-Authenticate and a SOAP fault that names the reason
        $soapRefusal = class_exists( 'expSignInSecondFactor' )
            ? expSignInSecondFactor::check( $soapUser, expSignInSecondFactor::CHANNEL_SOAP, expSignInSecondFactor::presentedCode() )
            : null;
        if ( $soapRefusal !== null )
        {
            eZUser::logoutCurrent();
            expSignInSecondFactor::sendRefusalHeaders( $soapRefusal, 'Exponential SOAP' );
            if ( !headers_sent() )
                header( 'Content-Type: text/xml; charset=UTF-8' );
            echo expSignInSecondFactor::soapFault( $soapRefusal );
            ob_end_flush();
            eZExecution::cleanExit();
        }
        // an account marked "change password at next sign-in" sets it in the web interface first
        if ( class_exists( 'expPasswordChangeRequired' ) && expPasswordChangeRequired::refusesSignIn( $soapUser, 'soap' ) )
            eZUser::logoutCurrent();
    }

    $server = new eZSOAPServer();

    foreach( $soapINI->variable( 'ExtensionSettings', 'SOAPExtensions' ) as $extension )
    {
        $extensionPath = eZExtension::extensionPath( $extension );
        if ( $extensionPath !== false )
        {
            include_once( $extensionPath . '/soap/initialize.php' );
        }
    }

    $server->processRequest();
}

ob_end_flush();

eZExecution::cleanExit();

?>
