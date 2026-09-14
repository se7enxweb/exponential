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

$feedback = array();

if ( $module->isCurrentAction( 'LaunchCronjob' ) )
{
    $part = $module->hasActionParameter( 'CronjobPart' ) ? (string)$module->actionParameter( 'CronjobPart' ) : '';
    $siteaccess = $module->hasActionParameter( 'CronjobSiteAccess' ) ? (string)$module->actionParameter( 'CronjobSiteAccess' ) : '';

    $result = expCronjobRunner::launch( $part, $siteaccess );
    $feedback[] = array( 'ok' => $result['ok'], 'message' => $result['message'] );
}

if ( $module->isCurrentAction( 'StopCronjob' ) )
{
    $result = expCronjobRunner::stop();
    $feedback[] = array( 'ok' => $result['ok'], 'message' => $result['message'] );
}

if ( $module->isCurrentAction( 'ClearCronjobLog' ) )
{
    $result = expCronjobRunner::clearLogs();
    $feedback[] = array( 'ok' => $result['ok'], 'message' => $result['message'] );
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
