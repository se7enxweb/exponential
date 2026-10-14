<?php
/**
 * File containing the global eZ Publish 4 functions
 *
 * @deprecated Since 5.0
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

/**
 * @deprecated Since 5.0
 * @return string|null the debug report, which the caller prints; null when debug output is off
 */
function eZDisplayDebug()
{
    $ini = eZINI::instance();

    if ( $ini->variable( 'DebugSettings', 'DebugOutput' ) != 'enabled' )
        return null;

    $scriptStopTime = microtime( true );

    $type = $ini->variable( "DebugSettings", "Debug" );
    //eZDebug::setHandleType( eZDebug::HANDLE_NONE );
    if ( $type == "inline" or $type == "popup" )
    {
        $asHtml = true;

        if ( $ini->variable( "DebugSettings", "DebugToolbar" ) == 'enabled' &&
             $ini->variable( "SiteAccessSettings", "CheckValidity" ) !== 'true' &&
             $asHtml == true &&
             !$GLOBALS['eZRedirection'] )

        {
            $tpl = eZTemplate::factory();
            $result = "<tr><td colspan='2'>" . $tpl->fetch( 'design:setup/debug_toolbar.tpl' ) . "</td></tr>";
            eZDebug::appendTopReport( "Debug toolbar", $result );
        }

        eZDebug::appendBottomReport( 'Template Usage Statistics', eZTemplatesStatisticsReporter::generateStatistics( $asHtml ) );

        eZDebug::setScriptStop( $scriptStopTime );
        $report = eZDebug::printReport( $type == "popup", $asHtml, true, false, true,
            true, $ini->variable( "DebugSettings", "DisplayIncludedFiles" ) == 'enabled' );
        eZDebugReportNoStore( $report );
        return $report;
    }
    return null;
}

/**
 * A page that carries the debug report belongs to this request and this viewer (its timings, queries and messages,
 * and a report shown by IP to that address only): "private, no-store", so no shared cache keeps it -- Velocity's
 * response cache had kept an anonymous page with the report of a request from a listed address, and answered every
 * visitor with it -- and the browser does not replay it either. The role-aware page cache refuses such a page on its
 * own (ezpHttpCacheListener, "BYPASS (debug report)").
 *
 * @param string|null $report what eZDebug::printReport() returned
 * @return bool whether the header was sent
 */
function eZDebugReportNoStore( $report )
{
    if ( !is_string( $report ) || trim( $report ) === '' || headers_sent() )
        return false;
    header( 'Cache-Control: private, no-store' );
    header_remove( 'Expires' );
    header_remove( 'Pragma' );
    return true;
}

/**
 * @deprecated Since 5.0
 * @param string|null $templateResult
 */
function eZDisplayResult( $templateResult )
{
    ob_start();
    if ( $templateResult !== null )
    {
        $templateResult = ezpEvent::getInstance()->filter( 'response/preoutput', $templateResult );
        $debugMarker = '<!--DEBUG_REPORT-->';
        $pos = strpos( $templateResult, $debugMarker );
        if ( $pos !== false )
        {
            $debugMarkerLength = strlen( $debugMarker );
            echo substr( $templateResult, 0, $pos );
            echo eZDisplayDebug();
            echo substr( $templateResult, $pos + $debugMarkerLength );
        }
        else
        {
            echo $templateResult, eZDisplayDebug();
        }
    }
    else
    {
        echo eZDisplayDebug();
    }
    echo ezpEvent::getInstance()->filter( 'response/output', ob_get_clean() );
}

// eZUpdateDebugSettings() -- reads the settings from site.ini and passes them
// to eZDebug -- is shared with the other front controllers, so it lives in a
// file of its own that every one of them includes once.
require_once __DIR__ . '/debug_settings_functions.php';

/**
 * Appends a new warning item to the warning list.
 * a $parameters must contain an error and text key.
 *
 * @deprecated Since 5.0
 * @param array $parameters
 */
function eZAppendWarningItem( $parameters = array() )
{
    global $warningList;
    $parameters += array(
        'error' => false,
        'text' => false,
        'identifier' => false
    );
    $warningList[] = array(
        'error' => $parameters['error'],
        'text' => $parameters['text'],
        'identifier' => $parameters['identifier'],
    );
}

/**
 * @deprecated Since 5.0
 * @param \eZURI $uri
 * @param null|array $check
 * @param null|\eZModule $module ByRef, will be set to a eZModule instace based on $moduleName
 * @param string $moduleName
 * @param string $functionName
 * @param array $params
 * @return bool
 */
function fetchModule( eZURI $uri, $check, &$module, &$moduleName, &$functionName, &$params )
{
    $moduleName = $uri->element();
    if ( $check !== null && isset( $check["module"] ) )
        $moduleName = $check["module"];

    // Try to fetch the module object
    $module = eZModule::exists( $moduleName );
    if ( !( $module instanceof eZModule ) )
    {
        return false;
    }

    $uri->increase();
    $functionName = "";
    if ( !$module->singleFunction() )
    {
        $functionName = $uri->element();
        $uri->increase();
    }
    // Override it if required
    if ( $check !== null && isset( $check["function"] ) )
        $functionName = $check["function"];

    $params = $uri->elements( false );
    return true;
}
