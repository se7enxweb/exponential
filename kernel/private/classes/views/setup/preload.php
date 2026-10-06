<?php
/**
 * The code of kernel/setup/preload.php, moved into a class (#207 stage 1). The file kernel/setup/preload.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/setup/preload.php:
 *
 *
 * File containing the setup/preload view.
 *
 * Renders the preloader's console. The page itself does no work: it opens an
 * EventSource against setup/preloadstream and prints what arrives, so the
 * operator sees each page warm as it happens instead of waiting on one long
 * request that a proxy is free to time out.
 *
 * Ported from kernel/ezsitemanager/admin/preload.php in exponentialbasic, which
 * did the same thing against a hand rolled template engine.
 *
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Setup
{

/**
 * Setup > Preload: what is running, the form that starts a run or shows what it would do (the dry run), and the
 * last runs. A run never happens inside this request: expPreloadJob::start() starts it in the background, and the
 * page follows it (setup/preloadjob with javascript, a reload without). The form posts the names setup/preloadjob
 * takes (Action, SiteAccess, MaxPages, MaxDepth, JobID), so it works without javascript too.
 * Guide: doc/guides/preloading-caches.md
 */
class Preload extends \Exponential\Runnable\ModuleView
{
    const FEEDBACK_KEY = 'ExpPreloadFeedback';

    public function run( array $scope )
    {
        $Module = $scope['Params']['Module'];
        $http = \eZHTTPTool::instance();

        // The site to warm is chosen, not inferred: this view is served from the administration siteaccess, whose
        // own SiteURL is the administration host.
        $current = isset( $GLOBALS['eZCurrentAccess']['name'] ) ? $GLOBALS['eZCurrentAccess']['name'] : '';
        $targets = \expPreloadJob::targets( $current );
        $names = array();
        foreach ( $targets as $target )
            $names[] = $target['name'];

        $form = array(
            'siteaccess' => isset( $targets[0] ) ? $targets[0]['name'] : '',
            'max_pages' => \expPreloadJob::DEFAULT_MAX_PAGES,
            'max_depth' => \expPreloadJob::DEFAULT_MAX_DEPTH,
            'images' => false,
        );
        $feedback = array();
        $plan = false;

        if ( $_SERVER['REQUEST_METHOD'] === 'POST' && $http->hasPostVariable( 'Action' ) )
        {
            $action = (string)$http->postVariable( 'Action' );
            if ( $action === 'start' || $action === 'dryrun' )
            {
                $siteaccess = $http->hasPostVariable( 'SiteAccess' ) ? (string)$http->postVariable( 'SiteAccess' ) : $form['siteaccess'];
                // Only a siteaccess the page offers: nothing else reaches a command line.
                if ( !in_array( $siteaccess, $names, true ) )
                    $siteaccess = '';
                $options = \expPreloadJob::options( array(
                    'max_pages' => $http->hasPostVariable( 'MaxPages' ) ? $http->postVariable( 'MaxPages' ) : null,
                    'max_depth' => $http->hasPostVariable( 'MaxDepth' ) ? $http->postVariable( 'MaxDepth' ) : null,
                    'images' => $http->hasPostVariable( 'Images' ) && $http->postVariable( 'Images' ),
                ) );
                $form = array( 'siteaccess' => $siteaccess ) + $options;

                if ( $siteaccess === '' )
                    $feedback[] = array( 'type' => 'unknown_siteaccess', 'message' => '' );
                else if ( $action === 'dryrun' )
                    $plan = \expPreloadJob::plan( $siteaccess, $options );
                else
                {
                    $error = '';
                    $busy = \expPreloadJob::running() !== false;
                    $id = $busy ? false : \expPreloadJob::start( $siteaccess, $options['max_pages'], $options['max_depth'], $error, $options );
                    if ( $id !== false )
                    {
                        $http->setSessionVariable( self::FEEDBACK_KEY, array( array( 'type' => 'started', 'message' => $siteaccess ) ) );
                        return $this->viewResult( null, $Module->redirectTo( $Module->functionURI( 'preload' ) ) );
                    }
                    $feedback[] = array( 'type' => $busy ? 'busy' : 'error', 'message' => $error );
                }
            }
            else if ( $action === 'stop' )
            {
                $id = $http->hasPostVariable( 'JobID' ) ? (string)$http->postVariable( 'JobID' ) : '';
                $stopping = \expPreloadJob::stop( $id );
                $http->setSessionVariable( self::FEEDBACK_KEY, array( array( 'type' => $stopping ? 'stopping' : 'not_running', 'message' => '' ) ) );
                return $this->viewResult( null, $Module->redirectTo( $Module->functionURI( 'preload' ) ) );
            }
        }

        // What the last action said, shown once.
        if ( $http->hasSessionVariable( self::FEEDBACK_KEY ) )
        {
            $feedback = array_merge( (array)$http->sessionVariable( self::FEEDBACK_KEY ), $feedback );
            $http->removeSessionVariable( self::FEEDBACK_KEY );
        }

        $runs = array();
        foreach ( \expPreloadJob::history()->runs( 10 ) as $status )
            $runs[] = \expPreloadJob::publicStatus( $status );
        $running = \expPreloadJob::running();

        $selected = false;
        foreach ( $targets as $target )
            if ( $target['name'] === $form['siteaccess'] )
                $selected = $target;
        $preview = \expPreloadJob::plan( $form['siteaccess'], $form );

        $tpl = \eZTemplate::factory();
        // the variables of the earlier page, kept
        $tpl->setVariable( 'targets', $targets );
        $tpl->setVariable( 'selected_siteaccess', $form['siteaccess'] );
        $tpl->setVariable( 'base_url', $preview['base_url'] );
        $tpl->setVariable( 'start_urls', $preview['start_urls'] );
        $tpl->setVariable( 'stream_url', 'setup/preloadstream' );
        $tpl->setVariable( 'default_max_pages', \expPreloadJob::DEFAULT_MAX_PAGES );
        $tpl->setVariable( 'default_max_depth', \expPreloadJob::DEFAULT_MAX_DEPTH );
        // the run going on, the last runs, the form and the dry run
        $tpl->setVariable( 'job_url', 'setup/preloadjob' );
        $tpl->setVariable( 'form', $form );
        $tpl->setVariable( 'selected_target', $selected );
        $tpl->setVariable( 'max_pages_limit', \expPreloadJob::MAX_PAGES_LIMIT );
        $tpl->setVariable( 'max_depth_limit', \expPreloadJob::MAX_DEPTH_LIMIT );
        $tpl->setVariable( 'running', $running === false ? false : \expPreloadJob::publicStatus( $running ) );
        $tpl->setVariable( 'runs', $runs );
        $tpl->setVariable( 'plan', $plan );
        $tpl->setVariable( 'feedback', $feedback );
        $tpl->setVariable( 'servers', \expPreloadJob::servers() );
        $tpl->setVariable( 'command_line', $preview['command'] );
        $tpl->setVariable( 'runs_directory', \expPreloadJob::directory() );
        $tpl->setVariable( 'root_dir', \eZSys::rootDir() );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:setup/preload.tpl' );
        $Result['path'] = array(
            array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/setup', 'Preload' ) ) );

        return $this->viewResult( $Result, null );
    }
}

}
