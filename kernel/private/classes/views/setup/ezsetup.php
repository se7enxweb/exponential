<?php
/**
 * The code of kernel/setup/ezsetup.php, moved into a class (#207 stage 1). The file kernel/setup/ezsetup.php is one call to it.
 * Guide: doc/eng-us/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/setup/ezsetup.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Setup
{

class Ezsetup extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $GLOBALS['eZSiteBasics']['no-cache-adviced'] = false;

        // Avoid compiling templates just for 1 view to improve performance
        $GLOBALS['eZTemplateCompilerSettings']['compile'] = false;

        // Include common functions
        include_once( "kernel/setup/ezsetupcommon.php" );
        include_once( "kernel/setup/ezsetuptests.php" );
        include_once( 'kernel/setup/ezsetup_summary.php' );

        // Initialize template
        $tpl = \eZTemplate::instance();
        //$tpl->registerFunction( "section", new eZTemplateSectionFunction( "section" ) );
        //$tpl->registerFunction( "include", new eZTemplateIncludeFunction() );

        $ini = \eZINI::instance();
        if ( $ini->variable( 'TemplateSettings', 'Debug' ) == 'enabled' )
            \eZTemplate::setIsDebugEnabled( true );
        //eZDebug::setLogOnly( true );

        //$ini->setVariable( 'RegionalSettings', 'TextTranslation', 'disabled' );


        $Module = $Params['Module'];

        $tpl->setAutoloadPathList( $ini->variable( 'TemplateSettings', 'AutoloadPathList' ) );
        $tpl->autoload();

        $tpl->registerResource( \eZTemplateDesignResource::instance() );

        // Initialize HTTP variables
        $http = \eZHTTPTool::instance();

        $baseDir = 'kernel/setup/';

        // Load step list data. See this file for install step references.
        $stepDataFile = $baseDir . "steps/ezstep_data.php";
        $stepData = null;
        if ( file_exists( $stepDataFile ) )
        {
            include_once( $stepDataFile );
            $stepData = new \eZStepData();
        }
        if ( $stepData == null )
        {
            print "<h1>Setup step data file not found. Setup is exiting...</h1>"; //TODO : i18n translate
            eZDisplayResult( $templateResult );
            \eZExecution::cleanExit();
        }

        $persistenceList = eZSetupFetchPersistenceList();

        // Who may run the wizard, and when maintenance starts (expSetupWizardGuard):
        // - an installed site shows an explanation page first and writes nothing. When
        //   [SetupSettings] AllowWizardOnInstalledSite allows it (auto: CheckValidity=true
        //   routed the request here; enabled; never with disabled) that page offers "Run the
        //   setup wizard anyway", which leads to a confirm page; only the confirm page's POST
        //   with its one-time token starts the run;
        // - maintenance mode (var/maintenance.json, the wizard lease) never starts from a
        //   GET or because a page was shown: it starts with the first posted step (fresh
        //   installation) or the confirmed POST (installed site). This browser passes on a
        //   cookie, every other visitor gets the maintenance page rather than a second
        //   wizard. It ends with the last page, or by itself when the wizard is left
        //   (expMaintenance::WIZARD_LEASE, renewed by each posted step only).
        // var/log/setup.log: a run starts with the wizard's first page, posted steps continue it
        // Several nodes (expSetupSharedState): the lease, the run and the installed marker are in the
        // shared state directory, so a step that lands on another node continues the same run, and the
        // files the wizard writes (settings, packages) are copied to whichever node serves the next step
        $root = \eZSys::rootDir();
        $sharedDir = \expSetupSharedState::dir( $root );
        // No cache keeps a page of the wizard (the confirm page carries a one-time token)
        \expSetupWizardGuard::sendNoStoreHeaders();
        $isStepPost = $http->hasPostVariable( 'eZSetup_current_step' );
        $warmup = !empty( $GLOBALS['expVelocityWarmup'] );
        $validityCheck = !empty( $GLOBALS['eZSiteBasics']['validity-check-required'] );
        // Every form of the wizard posts here: through the validity check the site root, never a
        // /setup/... address (an admin module a frontend firewall refuses)
        $uriPrefix = strpos( \eZSys::serverVariable( 'PHP_SELF' ), '/ezsetup' ) ? '/ezsetup' : '';
        $script = \expSetupWizardGuard::formAction( $validityCheck, \eZSys::wwwDir(), \eZSys::indexFileName(),
                                                    \eZSys::indexFile(), $uriPrefix );
        $tpl->setVariable( 'script', $script );
        $installed = \expSetupWizardGuard::isInstalled( $root, $validityCheck );
        if ( $installed && $sharedDir !== null && !$warmup )
        {
            // Another node served the wizard's last steps: this node fetches the settings and packages it wrote
            $installedRun = \expSetupWizardGuard::sharedInstalledRun( $root );
            if ( $installedRun !== null && \expSetupSharedState::pull( $root, $sharedDir, $installedRun ) )
                \eZINI::resetAllInstances( false );
        }
        $allowedOnInstalled = \expSetupWizardGuard::allowedOnInstalledSite( $ini, $validityCheck );
        if ( $installed && !$allowedOnInstalled )
        {
            // Velocity's warm-up included: nothing to prepare, nothing written
            return $this->guardPage( $tpl, 'design:setup/init/installed.tpl',
                                     \ezpI18n::tr( 'design/standard/setup/init', 'Already installed' ), false );
        }
        $runAnyway = $installed && \expSetupWizardGuard::runAnywayPosted( $http );
        $confirmed = ( $installed && !$warmup ) ? \expSetupWizardGuard::confirmedByPost( $http ) : false;
        $leaseRenewed = false;
        if ( $isStepPost && !$warmup )
        {
            if ( \expSetupLog::resume() )
                $leaseRenewed = \expMaintenance::renewWizard( $root, (string)\expSetupLog::runId() );
        }
        $decision = \expSetupWizardGuard::decide( $installed, $allowedOnInstalled, $isStepPost, $confirmed, $leaseRenewed, $runAnyway );
        if ( $decision === \expSetupWizardGuard::OFFER )
        {
            // The explanation page with "Run the setup wizard anyway": nothing written
            return $this->guardPage( $tpl, 'design:setup/init/installed.tpl',
                                     \ezpI18n::tr( 'design/standard/setup/init', 'Already installed' ), true );
        }
        if ( $warmup )
        {
            // Velocity's warm-up render: not a visitor, so no run and no lease (a lease
            // would lock the browser on the wizard out with a cookie nobody holds)
            if ( $decision === \expSetupWizardGuard::CONFIRM )
                return $this->guardPage( $tpl, 'design:setup/init/confirm_reinstall.tpl',
                                         \ezpI18n::tr( 'design/standard/setup/init', 'Reinstall this site' ) );
        }
        else if ( $decision === \expSetupWizardGuard::CONFIRM )
        {
            $tpl->setVariable( 'confirm_token', \expSetupWizardGuard::issueConfirmToken() );
            $tpl->setVariable( 'confirm_button', \expSetupWizardGuard::CONFIRM_BUTTON );
            $tpl->setVariable( 'confirm_token_name', \expSetupWizardGuard::CONFIRM_TOKEN );
            return $this->guardPage( $tpl, 'design:setup/init/confirm_reinstall.tpl',
                                     \ezpI18n::tr( 'design/standard/setup/init', 'Reinstall this site' ) );
        }
        else if ( $decision === \expSetupWizardGuard::SHOW
                  && \expSetupWizardGuard::isAssetRequest( \eZSys::serverVariable( 'REQUEST_URI', true ) ) )
        {
            // A style sheet, script, image or /favicon.ico the web server did not serve itself: not the
            // wizard's first page, so no new run that would abandon the one in progress
            if ( !headers_sent() )
                header( ( $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1' ) . ' 404 Not Found', true, 404 );
            echo "Not found\n";
            \eZExecution::cleanExit();
        }
        else if ( $decision === \expSetupWizardGuard::SHOW )
        {
            // A fresh installation's first page: a run in the setup log, no maintenance yet
            \expSetupLog::startWeb();
        }
        else if ( $decision === \expSetupWizardGuard::BEGIN )
        {
            if ( $confirmed )
                \expSetupLog::startWeb( array( 'confirmed' => 'reinstall of an installed site' ) );
            else if ( !\expSetupLog::resume() )
                \expSetupLog::start( 'web setup wizard (joined at step ' . $http->postVariable( 'eZSetup_current_step' ) . ')' );
            if ( !\expMaintenance::beginWizard( $root, (string)\expSetupLog::runId() ) )
            {
                // Another browser's wizard holds the site (on this node or another): its maintenance page,
                // never a second wizard that would abandon it or install over it
                $held = \expMaintenance::wizardState( $root );
                \expSetupLog::detach( 'REFUSED (another setup wizard holds the site)' );
                if ( is_array( $held ) )
                {
                    \expMaintenance::send( $root, $held );
                    \eZExecution::cleanExit();
                }
            }
        }
        $setupRunId = (string)\expSetupLog::runId();
        // The files earlier steps of this run wrote on another node
        if ( $sharedDir !== null && !$warmup && $setupRunId !== '' && $decision !== \expSetupWizardGuard::SHOW
             && \expSetupSharedState::pull( $root, $sharedDir, $setupRunId ) )
        {
            // The settings this request read before are older than the files just written
            \eZINI::resetAllInstances( false );
            $ini = \eZINI::instance();
        }
        $result = null;

        // process previous step
        $previousStepClass = null;
        $step = null;
        $currentStep = null;

        if ( $http->hasPostVariable( 'eZSetup_back_button' ) ) // previous step selected
        {
            $previousStep = $http->postVariable( 'eZSetup_current_step' );
            $step = $stepData->previousStep( $previousStep );
            $goBack = true;
            while ( $goBack )
            {
                $includeFile = $baseDir .'steps/ezstep_'.$step['file'].'.php';

                if ( file_exists( $includeFile ) )
                {
                    include_once( $includeFile );
                    $className = 'eZStep'.$step['class'];
                    $stepObject = new $className( $tpl, $http, $ini, $persistenceList );

                    if ( $stepObject->init() === true )
                    {
                        $step = $stepData->previousStep( $step );
                        continue;
                    }
                }

                $goBack = false;
            }

        }
        else if ( $http->hasPostVariable( 'eZSetup_refresh_button' ) ) // refresh selected step
        {
            $step = $stepData->step( $http->postVariable( 'eZSetup_current_step' ) );
        }
        else if ( $http->hasPostVariable( 'eZSetup_next_button' ) || $http->hasPostVariable( 'eZSetup_current_step' ) ) // next step selected,
        {
            // first, input from step must be processed/checked (processPostData())
            $currentStep = $stepData->step( $http->postVariable( 'eZSetup_current_step' ) );

            $includeFile = $baseDir .'steps/ezstep_'.$currentStep['file'].'.php';
            $result = array();

            if ( file_exists( $includeFile ) )
            {
                include_once( $includeFile );
                $className = 'eZStep'.$currentStep['class'];
                $previousStepClass = new $className( $tpl, $http, $ini, $persistenceList );

                \expSetupLog::stepBegin( $currentStep['class'] . ' answers' );
                $processPostDataResult = $previousStepClass->processPostData();
                $persistenceList = $previousStepClass->PersistenceList;
                \expSetupLog::noteContext( $persistenceList );
                \expSetupLog::stepEnd( $processPostDataResult === false ? 'rejected (asked again)' : 'accepted' );

                if ( $processPostDataResult === false ) // processing previous input failed, step must be redone
                {
                    $step = $currentStep;
                }
                else if ( $processPostDataResult !== true ) // step to redo specified
                {
                    $step = $stepData->step( $processPostDataResult );
                }
                else
                {
                    $step = $stepData->nextStep( $currentStep );
                }
            }

        }
        else //First step, no params set.
        {
            $step = $stepData->step(0); //step contains file and class
        }

        $done = false;
        $result = null;

        while( !$done && $step != null )
        {
            // Some common variables for all steps ($script: expSetupWizardGuard::formAction() above)
            $tpl->setVariable( 'script', $script );

            $tpl->setVariable( "version", array( "text" => \ExponentialSDK::version(),
                                                 "major" => \ExponentialSDK::majorVersion(),
                                                 "minor" => \ExponentialSDK::minorVersion(),
                                                 "release" => \ExponentialSDK::release(),
                                                 "alias" => \ExponentialSDK::alias() ) );

            if ( $persistenceList === null )
                $persistenceList = eZSetupFetchPersistenceList();
            $tpl->setVariable( 'persistence_list', $persistenceList );

            // Try to include the relevant file
            $includeFile = $baseDir . 'steps/ezstep_'.$step['file'].'.php';
            $stepClass = false;
            if ( file_exists( $includeFile ) )
            {
                include_once( $includeFile );
                $className = 'eZStep'.$step['class'];

                if ( $step == $currentStep ) // if processing post data of current step failed, use same class object.
                {
                    $stepInstaller = $previousStepClass;
                }
                else
                {
                    $stepInstaller = new $className( $tpl, $http, $ini, $persistenceList );
                }

                \expSetupLog::stepBegin( $step['class'] );
                $result = $stepInstaller->init();
                \expSetupLog::noteContext( $stepInstaller->PersistenceList );

                if( $result === true )
                {
                    \expSetupLog::stepEnd( 'ok' );
                    $step = $stepData->nextStep( $step );
                }
                else if( is_int( $result ) || is_string( $result ) )
                {
                    \expSetupLog::stepEnd( 'redirected to ' . $result );
                    $step = $stepData->step( $result );
                }
                else
                {
                    \expSetupLog::stepEnd( 'shown' );
                    // The last page of the wizard: the installation is done
                    if ( $step['class'] === 'Final' )
                    {
                        \expSetupLog::finish( 'installed', true );
                        \expMaintenance::endWizard( \eZSys::rootDir(), $setupRunId );
                        // From now on setup/init explains that the site is installed
                        \expSetupWizardGuard::markInstalled( \eZSys::rootDir(), $setupRunId );
                    }
                    $tpl->setVariable( 'setup_current_step', $step['class'] ); // set current step
                    $result = $stepInstaller->display();
                    $result['help'] = $tpl->fetch( 'design:setup/init/'.$step['file'].'_help.tpl' );
                    $done = true;
                }
            }
            else
            {
                print( '<h1>Step '.$step['class'].' is not valid, no such file '.$includeFile.'. I\'m exiting...</h1>' ); //TODO : i18n
                eZDisplayResult( $templateResult );
                \eZExecution::cleanExit();
            }
        }

        // generate summary
        $summary = new \eZSetupSummary( $tpl, $persistenceList );
        $result['summary'] = $summary->summary();

        // Compute install progress
        $result['progress'] = $stepData->progress( $step );

        // Print debug information and exit.
        \eZDebug::addTimingPoint( "End" );
        // A step that ran long (a package import) holds the lease from its end, not its start
        if ( !$warmup && $setupRunId !== '' && ( $decision === \expSetupWizardGuard::BEGIN || $decision === \expSetupWizardGuard::CONTINUE_RUN ) )
        {
            \expMaintenance::renewWizard( $root, $setupRunId );
            // What this step wrote, for the node that serves the next one (and, once installed, every other node)
            if ( $sharedDir !== null )
            {
                $since = \expSetupLog::startedAt();
                \expSetupSharedState::push( $root, $sharedDir, \expSetupSharedState::mirroredPaths(),
                                            $since !== null ? $since : time() - \expMaintenance::WIZARD_LEASE, $setupRunId );
            }
        }
        \expSetupLog::suspend();

        return $this->viewResult( isset( $Result ) ? $Result : null,  $result );

        //eZDisplayResult( $templateResult );

        //eZExecution::cleanExit();

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }

    /**
     * A page of its own instead of a wizard step (installed site, confirm page), in
     * the frame the setup page layout expects. Writes nothing.
     */
    protected function guardPage( $tpl, $template, $title, $runAnyway = false )
    {
        $tpl->setVariable( 'kickstart_file', 'kickstart.ini' );
        $tpl->setVariable( 'run_anyway', (bool)$runAnyway );
        $tpl->setVariable( 'run_anyway_button', \expSetupWizardGuard::RUN_ANYWAY_BUTTON );
        return array( 'content' => $tpl->fetch( $template ),
                      'path' => array( array( 'text' => $title, 'url' => false ) ),
                      'help' => '',
                      'summary' => '',
                      'progress' => 0 );
    }
}

}
