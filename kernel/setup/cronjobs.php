<?php
/**
 * File containing the setup/cronjobs view.
 *
 * The cronjobs console: what parts this installation defines, which scripts
 * each one runs, what is running now, and the output of the last run.
 *
 * Launching, stopping and clearing are actions on this view rather than views
 * of their own, so they go through the module's post action handling and its
 * single managecronjobs policy, the way setup/cache does.
 *
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

require_once 'kernel/setup/expcronjobrunner.php';

$Module = $Params['Module'];
$module = $Params['Module'];
$http = eZHTTPTool::instance();

// Every action answers with a redirect rather than a page, so the address the
// browser ends on is a plain get.
//
// Rendering the result of the post directly left the page as the response to a
// post: reloading it - which the console does once a job finishes, so the
// controls stop showing a Stop button for something that has ended - made the
// browser offer to resend the form, and confirming relaunched the job. Accept,
// finish, reload, offer, accept: the same cronjob started over and over for as
// long as the operator kept clicking through the prompt. With the redirect
// there is no post left to repeat.
//
// The message the action produced is carried across in the session, because a
// redirect cannot carry it and it should not be in the address bar.
$feedbackKey = 'eZCronjobFeedback';
$feedback = array();
$actionTaken = false;

if ( $module->isCurrentAction( 'LaunchCronjob' ) )
{
    $part = $module->hasActionParameter( 'CronjobPart' ) ? (string)$module->actionParameter( 'CronjobPart' ) : '';
    $siteaccess = $module->hasActionParameter( 'CronjobSiteAccess' ) ? (string)$module->actionParameter( 'CronjobSiteAccess' ) : '';

    $result = expCronjobRunner::launch( $part, $siteaccess );
    $feedback[] = array( 'ok' => $result['ok'], 'message' => $result['message'] );
    $actionTaken = true;
}

if ( $module->isCurrentAction( 'StopCronjob' ) )
{
    $result = expCronjobRunner::stop();
    $feedback[] = array( 'ok' => $result['ok'], 'message' => $result['message'] );
    $actionTaken = true;
}

if ( $module->isCurrentAction( 'ClearCronjobLog' ) )
{
    $result = expCronjobRunner::clearLogs();
    $feedback[] = array( 'ok' => $result['ok'], 'message' => $result['message'] );
    $actionTaken = true;
}

// Asked for the answer rather than a new page, so the console can act on it
// without the page going anywhere. The action above has already run either way;
// this only decides how it is reported.
if ( $actionTaken && $http->hasVariable( 'Ajax' ) )
{
    while ( ob_get_level() > 0 )
        ob_end_clean();

    header( 'Content-Type: application/json; charset=utf-8' );
    header( 'Cache-Control: no-cache, no-store, must-revalidate' );

    $status = expCronjobRunner::status();
    echo json_encode( array(
        'ok'         => $result['ok'],
        'message'    => $result['message'],
        'running'    => (bool)$status['running'],
        'part'       => $status['part'],
        'siteaccess' => $status['siteaccess'],
        'pid'        => (int)$status['pid'],
        'elapsed'    => (int)$status['elapsed'],
        'offset'     => file_exists( expCronjobRunner::logFile() ) ? filesize( expCronjobRunner::logFile() ) : 0 ) );

    eZExecution::cleanExit();
}

if ( $actionTaken )
{
    $http->setSessionVariable( $feedbackKey, $feedback );
    return $module->redirectTo( $module->functionURI( 'cronjobs' ) );
}

// Whatever the last action said, shown once and then forgotten.
if ( $http->hasSessionVariable( $feedbackKey ) )
{
    $feedback = (array)$http->sessionVariable( $feedbackKey );
    $http->removeSessionVariable( $feedbackKey );
}

$parts = expCronjobRunner::parts();
$status = expCronjobRunner::status();
$phpBinary = expCronjobRunner::phpBinary();

// The siteaccess a cronjob should run under by default is the site, not the
// administration interface this page is being served from: a job that acts on
// content acts on the content of the site that publishes it.
$siteAccessList = expCronjobRunner::siteAccessList();
$currentSiteAccess = isset( $GLOBALS['eZCurrentAccess']['name'] ) ? $GLOBALS['eZCurrentAccess']['name'] : '';
$defaultSiteAccess = '';
foreach ( $siteAccessList as $name )
{
    if ( $name !== $currentSiteAccess )
    {
        $defaultSiteAccess = $name;
        break;
    }
}
if ( $defaultSiteAccess === '' )
    $defaultSiteAccess = $currentSiteAccess;

$tpl = eZTemplate::factory();
$tpl->setVariable( 'cronjob_parts', $parts );
$tpl->setVariable( 'cronjob_status', $status );
$tpl->setVariable( 'cronjob_feedback', $feedback );
$tpl->setVariable( 'cronjob_siteaccess_list', $siteAccessList );
$tpl->setVariable( 'cronjob_default_siteaccess', $defaultSiteAccess );
$tpl->setVariable( 'cronjob_php_binary', $phpBinary === false ? '' : $phpBinary );
$tpl->setVariable( 'cronjob_log_file', expCronjobRunner::logFile() );
$tpl->setVariable( 'cronjob_error_file', expCronjobRunner::errorFile() );
$tpl->setVariable( 'cronjob_log', expCronjobRunner::tail( expCronjobRunner::logFile() ) );
// Where the log the page printed ends, so the stream continues from there
// rather than reprinting it. It is the size of the file, not the length of the
// tail above, which may have had its head trimmed off.
$tpl->setVariable( 'cronjob_log_offset',
                   file_exists( expCronjobRunner::logFile() ) ? filesize( expCronjobRunner::logFile() ) : 0 );
$tpl->setVariable( 'cronjob_errors', expCronjobRunner::tail( expCronjobRunner::errorFile(), 16384 ) );
$tpl->setVariable( 'cronjob_stream_url', 'setup/cronjobsstream' );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:setup/cronjobs.tpl' );
$Result['path'] = array(
    array( 'url' => false, 'text' => ezpI18n::tr( 'kernel/setup', 'Cronjobs' ) ) );

?>
