<?php
/**
 * The code of kernel/setup/cronjobs.php, moved into a class (#207 stage 1). The file kernel/setup/cronjobs.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/setup/cronjobs.php:
 *
 *
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
 *
 */

namespace Exponential\View\Kernel\Setup
{

class Cronjobs extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        require_once 'kernel/setup/expcronjobrunner.php';

        $Module = $Params['Module'];
        $module = $Params['Module'];
        $http = \eZHTTPTool::instance();

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

            // Wrapped, as all three are. Whatever goes wrong in a runner - a disk that
            // will not take the log, a php binary that disappeared, a process that
            // cannot be signalled - the answer has to be an answer, because the console
            // is waiting for one and an error page tells it nothing it can act on.
            $result = \expCronjobRunner::attempt( 'launch', array( $part, $siteaccess ) );
            $feedback[] = array( 'ok' => $result['ok'], 'message' => $result['message'] );
            $actionTaken = true;
        }

        if ( $module->isCurrentAction( 'LaunchCronjobScript' ) )
        {
            // "part|script", as the row's button sends it.
            $target = $module->hasActionParameter( 'CronjobTarget' ) ? (string)$module->actionParameter( 'CronjobTarget' ) : '';
            $siteaccess = $module->hasActionParameter( 'CronjobSiteAccess' ) ? (string)$module->actionParameter( 'CronjobSiteAccess' ) : '';

            $part = '';
            $script = '';
            if ( strpos( $target, '|' ) !== false )
                list( $part, $script ) = explode( '|', $target, 2 );

            $result = \expCronjobRunner::attempt( 'launchScript', array( $part, $script, $siteaccess ) );
            $feedback[] = array( 'ok' => $result['ok'], 'message' => $result['message'] );
            $actionTaken = true;
        }

        if ( $module->isCurrentAction( 'StopCronjob' ) )
        {
            $result = \expCronjobRunner::attempt( 'stop' );
            $feedback[] = array( 'ok' => $result['ok'], 'message' => $result['message'] );
            $actionTaken = true;
        }

        if ( $module->isCurrentAction( 'ClearCronjobLog' ) )
        {
            $result = \expCronjobRunner::attempt( 'clearLogs' );
            $feedback[] = array( 'ok' => $result['ok'], 'message' => $result['message'] );
            $actionTaken = true;
        }

        // Asked for the answer rather than a new page, so the console can act on it
        // without the page going anywhere. The action above has already run either way;
        // this only decides how it is reported.
        if ( $actionTaken && $http->hasVariable( 'Ajax' ) )
        {
            // Nothing but the json may reach the browser. Debug output is on by default
            // on an administration siteaccess and is appended to whatever a request
            // produces, which would leave the console parsing a page of debug tables
            // and reporting that the server answered unexpectedly.
            \eZDebug::updateSettings( array( 'debug-enabled' => false ) );

            // not a loop on ob_get_level(): a persistent worker's own buffer cannot be removed, and the
            // loop would never end there (eZExecution::discardOutputBuffers())
            if ( method_exists( '\eZExecution', 'discardOutputBuffers' ) )
                \eZExecution::discardOutputBuffers();
            else
                while ( ob_get_level() > 0 && @ob_end_clean() );

            // Built before anything is sent, so a failure here still leaves a clean
            // response rather than half a document.
            try
            {
                $status = \expCronjobRunner::status();
                $answer = array(
                    'ok'         => (bool)$result['ok'],
                    'message'    => (string)$result['message'],
                    'running'    => (bool)$status['running'],
                    'part'       => (string)$status['part'],
                    'siteaccess' => (string)$status['siteaccess'],
                    'pid'        => (int)$status['pid'],
                    'elapsed'    => (int)$status['elapsed'],
                    // Into the log this job writes, which is what the stream follows.
                    'log'        => isset( $status['log'] ) ? (string)$status['log'] : '',
                    'offset'     => ( isset( $status['log'] ) && $status['log'] !== '' && file_exists( $status['log'] ) )
                                  ? filesize( $status['log'] ) : 0 );
            }
            catch ( \Exception $e )
            {
                $answer = array( 'ok' => false,
                                 'message' => 'The cronjob console failed after the action ran: ' . $e->getMessage(),
                                 'running' => false, 'part' => '', 'siteaccess' => '',
                                 'pid' => 0, 'elapsed' => 0, 'offset' => 0 );
                \eZDebug::writeError( $e->getMessage(), $this->scriptFile() );
            }

            $json = json_encode( $answer );
            if ( $json === false )
                $json = '{"ok":false,"message":"The answer could not be encoded.","running":false,"part":"","siteaccess":"","pid":0,"elapsed":0,"offset":0}';

            header( 'Content-Type: application/json; charset=utf-8' );
            header( 'Cache-Control: no-cache, no-store, must-revalidate' );
            header( 'Content-Length: ' . strlen( $json ) );

            echo $json;
            \eZExecution::cleanExit();
        }

        if ( $actionTaken )
        {
            $http->setSessionVariable( $feedbackKey, $feedback );
            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->redirectTo( $module->functionURI( 'cronjobs' ) ) );
        }

        // Whatever the last action said, shown once and then forgotten.
        if ( $http->hasSessionVariable( $feedbackKey ) )
        {
            $feedback = (array)$http->sessionVariable( $feedbackKey );
            $http->removeSessionVariable( $feedbackKey );
        }

        $parts = \expCronjobRunner::parts();
        $status = \expCronjobRunner::status();
        $phpBinary = \expCronjobRunner::phpBinary();

        // The siteaccess a cronjob should run under by default is the site, not the
        // administration interface this page is being served from: a job that acts on
        // content acts on the content of the site that publishes it.
        $siteAccessList = \expCronjobRunner::siteAccessList();
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

        $tpl = \eZTemplate::factory();
        // Each part gets the crontab line that would run it on a schedule, and each
        // script the directory it was found in, so the list can say where a thing lives
        // without the template working it out.
        // What the crontab actually says, read rather than assumed, so the page can
        // show what is scheduled instead of only what could be.
        $cronjobCrontab = \expCronjobRunner::installedCrontab();
        $cronjobScheduled = \expCronjobRunner::scheduledParts();

        // The newest run of each part, whole or one script of it, so every part can
        // say when it last ran from here and how that went.
        $cronjobLastRuns = array();
        foreach ( \expCronjobRunner::history( 100 ) as $run )
        {
            if ( isset( $run['part'] ) && !isset( $cronjobLastRuns[$run['part']] ) )
                $cronjobLastRuns[$run['part']] = $run;
        }

        // A command is shown with the siteaccess cut out of it, so the page can put
        // back whichever site is chosen in the select without asking the server.
        $siteAccessMark = "\x01";
        $splitCommand = function ( $command ) use ( $siteAccessMark )
        {
            $pieces = explode( $siteAccessMark, $command, 2 );
            return array( 'head' => $pieces[0], 'tail' => isset( $pieces[1] ) ? $pieces[1] : '' );
        };

        $summary = array( 'parts' => count( $parts ), 'scheduled' => 0, 'unscheduled' => 0,
                          'attention' => 0, 'scripts' => 0 );
        $now = time();

        foreach ( $parts as $index => $part )
        {
            $parts[$index]['crontab'] = \expCronjobRunner::crontabLine( $part['name'], $defaultSiteAccess );
            $parts[$index]['schedule'] = \expCronjobRunner::crontabSchedule( $part['name'] );
            $parts[$index]['scheduled'] = isset( $cronjobScheduled[$part['name']] );
            $parts[$index]['log'] = \expCronjobRunner::targetLogFile( $part['name'] );

            // The schedule that counts is the one the crontab really has; the
            // suggested one only when nothing schedules the part.
            $schedule = $parts[$index]['scheduled']
                      ? \expCronjobRunner::scheduleOfLine( $cronjobScheduled[$part['name']] )
                      : $parts[$index]['schedule'];
            $parts[$index]['schedule_active'] = $schedule === false ? '' : $schedule;
            $parts[$index]['schedule_text'] = $schedule === false ? '' : \expCronjobRunner::describeSchedule( $schedule );
            $parts[$index]['next_run'] = ( $parts[$index]['scheduled'] && $schedule !== false )
                                       ? (int)\expCronjobRunner::nextRun( $schedule, $now ) : 0;
            $parts[$index]['last_run'] = isset( $cronjobLastRuns[$part['name']] ) ? $cronjobLastRuns[$part['name']] : false;
            $parts[$index]['command'] = $splitCommand( \expCronjobRunner::commandLine( $part['name'], $siteAccessMark ) );
            $parts[$index]['attention'] = $part['forbidden'] || $part['missing'] > 0
                                        || ( $parts[$index]['last_run'] !== false && $parts[$index]['last_run']['errors'] > 0 );

            $searchText = array( $part['name'], $part['label'] );
            foreach ( $part['scripts'] as $scriptIndex => $script )
            {
                $parts[$index]['scripts'][$scriptIndex]['directory'] =
                    $script['path'] === false ? '' : dirname( $script['path'] );
                $parts[$index]['scripts'][$scriptIndex]['log'] =
                    \expCronjobRunner::targetLogFile( $part['name'], $script['name'] );
                $parts[$index]['scripts'][$scriptIndex]['description'] =
                    \expCronjobRunner::scriptDescription( $script['path'] );
                $parts[$index]['scripts'][$scriptIndex]['command'] =
                    $splitCommand( \expCronjobRunner::commandLine( $part['name'], $siteAccessMark, $script['name'] ) );
                $searchText[] = $script['name'];
                $searchText[] = $parts[$index]['scripts'][$scriptIndex]['description'];
            }
            // The scripts by name, for the line a folded script list shows.
            $scriptNames = array();
            foreach ( $part['scripts'] as $script )
                $scriptNames[] = $script['name'];
            $parts[$index]['script_names'] = implode( ', ', $scriptNames );
            // What the filter on the page matches against, lower case.
            $parts[$index]['search'] = mb_strtolower( implode( ' ', $searchText ), 'UTF-8' );

            $summary['scripts'] += count( $part['scripts'] );
            $summary[$parts[$index]['scheduled'] ? 'scheduled' : 'unscheduled']++;
            if ( $parts[$index]['attention'] )
                $summary['attention']++;
        }

        // The parts and their scripts are the long list on this page - it grows with
        // every extension that adds one - so the table draws a page of them. The
        // select above the table still offers all of them: it is a way of choosing a
        // part to run, and a part missing from it could not be chosen at all.
        $pageCount  = count( $parts );
        $pageLimit  = \expAdminPagination::limit( 'setup/cronjobs' );
        $pageOffset = \expAdminPagination::offset( $Params );

        $tpl->setVariable( 'cronjob_parts', $parts );
        $tpl->setVariable( 'cronjob_parts_page', \expAdminPagination::page( $parts, $pageOffset, $pageLimit ) );
        $tpl->setVariable( 'cronjob_parts_count', $pageCount );
        $tpl->setVariable( 'limit', $pageLimit );
        $tpl->setVariable( 'view_parameters', array( 'offset' => $pageOffset ) );
        // On disk, named by no part, so never run by anything.
        $availableScripts = \expCronjobRunner::availableScripts();
        foreach ( $availableScripts as $spareIndex => $spare )
            $availableScripts[$spareIndex]['description'] = \expCronjobRunner::scriptDescription( $spare['path'] );
        $tpl->setVariable( 'cronjob_available_scripts', $availableScripts );
        // What ran, when, and whether it complained.
        $cronjobHistory = \expCronjobRunner::history( 20 );
        $tpl->setVariable( 'cronjob_history', $cronjobHistory );
        $tpl->setVariable( 'cronjob_last_run', $cronjobHistory ? $cronjobHistory[0] : false );
        $tpl->setVariable( 'cronjob_summary', $summary );
        // Joined here, not in the template. A newline written between two template
        // tags is whitespace between tags, and the engine drops it - which turned the
        // crontab block into one unreadable run-on line.
        $cronjobSuggested = array();
        foreach ( $parts as $part )
        {
            if ( $part['forbidden'] || $part['scheduled'] )
                continue;
            $cronjobSuggested[] = $part['crontab'];
        }

        $tpl->setVariable( 'cronjob_crontab', $cronjobCrontab );
        $tpl->setVariable( 'cronjob_crontab_current', implode( "\n", $cronjobCrontab['lines'] ) );
        $tpl->setVariable( 'cronjob_crontab_suggested', implode( "\n", $cronjobSuggested ) );
        $tpl->setVariable( 'cronjob_scheduled', $cronjobScheduled );
        $tpl->setVariable( 'cronjob_root', \expCronjobRunner::installationRoot() );
        $tpl->setVariable( 'cronjob_status', $status );
        $tpl->setVariable( 'cronjob_feedback', $feedback );
        $tpl->setVariable( 'cronjob_siteaccess_list', $siteAccessList );
        $tpl->setVariable( 'cronjob_default_siteaccess', $defaultSiteAccess );
        $tpl->setVariable( 'cronjob_php_binary', $phpBinary === false ? '' : $phpBinary );
        // The log of whatever ran last, since nothing writes to the shared one now.
        $cronjobRecent = \expCronjobRunner::history( 1 );
        $cronjobLogFile = isset( $cronjobRecent[0]['log'] ) && $cronjobRecent[0]['log'] !== ''
                        ? $cronjobRecent[0]['log'] : \expCronjobRunner::logFile();

        $tpl->setVariable( 'cronjob_log_file', $cronjobLogFile );
        $tpl->setVariable( 'cronjob_error_file', \expCronjobRunner::errorFile() );
        $tpl->setVariable( 'cronjob_log', \expCronjobRunner::tail( $cronjobLogFile ) );
        // Where the log the page printed ends, so the stream continues from there
        // rather than reprinting it. It is the size of the file, not the length of the
        // tail above, which may have had its head trimmed off.
        $tpl->setVariable( 'cronjob_log_offset',
                           file_exists( $cronjobLogFile ) ? filesize( $cronjobLogFile ) : 0 );
        $tpl->setVariable( 'cronjob_errors', \expCronjobRunner::tail( \expCronjobRunner::errorFile(), 16384 ) );
        $tpl->setVariable( 'cronjob_stream_url', 'setup/cronjobsstream' );

        // The cross site request token, and the field it belongs in.
        //
        // ezformtoken refuses any post from a logged in user that does not carry it,
        // by throwing rather than answering, so a request without it comes back as an
        // error page. The form on this page has the token injected into it, but the
        // console builds its own request, so it is given the value directly rather
        // than left to find it in the document.
        // The field name is taken from the class constant, not from getFormField():
        // that method is protected, and method_exists() says yes to a protected method,
        // so asking for it would have been a fatal error on every load of this page.
        // The constant is the field ezformtoken checks second, whatever a site may have
        // renamed the first one to, so it is always accepted.
        $cronjobFormField = '';
        $cronjobFormToken = '';
        if ( class_exists( 'ezxFormToken' ) )
        {
            $cronjobFormField = defined( 'ezxFormToken::FORM_FIELD' ) ? (string)\ezxFormToken::FORM_FIELD : 'ezxform_token';
            try
            {
                $cronjobFormToken = (string)\ezxFormToken::getToken();
            }
            catch ( \Exception $e )
            {
                // No token to be had; the console falls back to submitting the form,
                // which carries one of its own.
                $cronjobFormToken = '';
            }
        }
        $tpl->setVariable( 'cronjob_form_field', $cronjobFormField );
        $tpl->setVariable( 'cronjob_form_token', $cronjobFormToken );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:setup/cronjobs.tpl' );
        $Result['path'] = array(
            array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/setup', 'Cronjobs' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
