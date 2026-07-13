<?php
/**
 * @description Kickstarter setup runner — runs the eZ Publish/Exponential CMS setup wizard using kickstart.ini and eZStep classes.
 * @package   kernel
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license   For full copyright and license information view LICENSE file.
 */

require_once 'kernel/setup/ezsetupcommon.php';
require_once 'kernel/setup/ezsetuptests.php';

class expKickstarter
{
    private $script;
    private $cli;
    private $tpl;
    private $http;
    private $ini;
    private $stepData;
    private $persistenceList = array();
    private $rootDir;
    private $argv;
    private $options;
    private $startStep;
    private $stopStep;
    private $force;

    public function __construct( $rootDir, $argv = null )
    {
        $this->rootDir = $rootDir;
        $this->argv    = is_array( $argv ) ? $argv : array();
    }

    public function run()
    {
        $this->bootstrap();

        if ( !file_exists( $this->rootDir . '/kickstart.ini' ) )
        {
            $this->cli->error( 'kickstart.ini not found. Generate it first with: ./bin/php/console exp:kickstarter ini' );
            return 1;
        }

        $this->stepData = new eZStepData();

        if ( !empty( $this->options['list-steps'] ) )
        {
            $this->listSteps();
            $this->script->shutdown( 0 );
        }

        if ( !empty( $this->options['dry-run'] ) )
        {
            $exitCode = $this->dryRun();
            if ( $exitCode != 0 )
            {
                $this->script->shutdown( $exitCode );
            }

            // In dry-run mode we run the wizard steps from DatabaseChoice through
            // Registration to verify that the remote packages can be downloaded,
            // then stop before CreateSites. DatabaseChoice is included so the
            // database type is available for DatabaseInit.
            $this->http->setPostVariable( 'eZSetupKickstartDryRun', true );
            $this->options['start-step'] = 'DatabaseChoice';
            $this->options['stop-step']  = 'Registration';
            $this->cleanupDryRun();
        }

        $this->startStep = $this->options['start-step'] ? $this->options['start-step'] : 'welcome';
        $this->stopStep  = $this->options['stop-step'] ? $this->options['stop-step'] : 'final';

        $startIndex = $this->stepIndex( $this->startStep );
        $stopIndex  = $this->stepIndex( $this->stopStep );

        if ( $startIndex === false )
        {
            $this->cli->error( 'Unknown start step: ' . $this->startStep );
            $this->script->shutdown( 1 );
        }
        if ( $stopIndex === false )
        {
            $this->cli->error( 'Unknown stop step: ' . $this->stopStep );
            $this->script->shutdown( 1 );
        }

        $createSitesIndex = $this->stepIndex( 'CreateSites' );
        if ( $createSitesIndex !== false && $startIndex <= $createSitesIndex && $stopIndex >= $createSitesIndex && !$this->force )
        {
            $this->cli->error( 'The CreateSites step will modify the database and site settings.' );
            $this->cli->output( 'Re-run with --force to confirm you want to install the site package.' );
            $this->script->shutdown( 1 );
        }

        $stepCount = $stopIndex - $startIndex + 1;
        $current   = 0;

        expScriptStatus::instance()->start( 'kickstart', 'Kickstart setup' );

        foreach ( $this->stepData->StepTable as $index => $step )
        {
            if ( $index < $startIndex )
                continue;
            if ( $index > $stopIndex )
                break;

            $current++;
            $className = $step['class'];
            expScriptStatus::instance()->update( 'Running: ' . $className, $current, $stepCount );

            $status = $this->executeStep( $step );
            if ( $status === false )
            {
                expScriptStatus::instance()->fail();
                $this->cli->output( '' );
                $this->cli->output( 'Setup failed on step: ' . $className );
                $this->script->shutdown( 1 );
            }
            if ( $status === 'break' )
            {
                break;
            }
        }

        expScriptStatus::instance()->end();

        if ( !empty( $this->options['dry-run'] ) )
        {
            $this->cli->output( '' );
            $this->cli->output( 'Dry-run completed: remote packages verified. Stopped before CreateSites.' );
            $this->script->shutdown( 0 );
        }

        $this->showSummary();
        $this->script->shutdown( 0 );
    }

    private function bootstrap()
    {
        $_SERVER['HTTP_HOST'] = isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : 'localhost';
        if ( date_default_timezone_get() === 'UTC' )
        {
            date_default_timezone_set( 'Europe/London' );
        }

        $this->script = eZScript::instance(
            array(
                'description'    => "Kickstart setup runner.\n\nRuns the Exponential CMS setup wizard using kickstart.ini.",
                'use-session'    => false,
                'use-modules'    => true,
                'use-extensions' => true,
                'site-access'    => 'plain',
            )
        );

        $this->script->startup();

        $cli = eZCLI::instance();
        $this->options = $cli->getOptions(
            '[start-step:][stop-step:][dry-run][list-steps][help][force]',
            '',
            $this->argv
        );
        $this->force = !empty( $this->options['force'] );

        if ( $this->options === false )
        {
            $this->script->shutdown( 1 );
        }

        if ( !empty( $this->options['help'] ) )
        {
            $this->showRunHelp();
            $this->script->shutdown( 0 );
        }

        $this->script->initialize();

        $this->clearKickstartCache();

        $this->cli = eZCLI::instance();
        $this->tpl = eZTemplate::instance();
        $this->http = eZHTTPTool::instance();
        $this->ini = eZINI::instance();

        // Remove any leftover dry-run repository from a previous run.
        $this->cleanupDryRun();

        return 0;
    }

    private function stepIndex( $className )
    {
        foreach ( $this->stepData->StepTable as $index => $step )
        {
            if ( strtolower( $step['class'] ) === strtolower( $className ) )
            {
                return $index;
            }
        }
        return false;
    }

    private function showRunHelp()
    {
        $this->cli->output( 'Usage: ./bin/php/console exp:kickstarter run [options]' );
        $this->cli->output( '       ./bin/php/kickstarter.php run [options]' );
        $this->cli->output( '' );
        $this->cli->output( 'Run the Exponential CMS setup wizard using kickstart.ini.' );
        $this->cli->output( '' );
        $this->cli->output( 'Options:' );
        $this->cli->output( '  --start-step=<step>  First step to run (default: welcome)' );
        $this->cli->output( '  --stop-step=<step>   Last step to run (default: final)' );
        $this->cli->output( '  --dry-run            Validate kickstart.ini, then run DatabaseChoice..Registration to test remote packages (stops before CreateSites)' );
        $this->cli->output( '  --list-steps         List all setup steps and exit' );
        $this->cli->output( '  --help, -h           Show this help' );
    }

    private function listSteps()
    {
        $this->cli->output( 'Setup steps:' );
        foreach ( $this->stepData->StepTable as $index => $step )
        {
            $count = ( !isset( $step['count_step'] ) || $step['count_step'] ) ? 'counted' : 'hidden';
            $this->cli->output( sprintf( '  [%2d] %-24s (%s)', $index, $step['class'], $count ) );
        }
    }

    private function dryRun()
    {
        $this->cli->output( 'Dry-run: validating kickstart.ini' );
        $kickstartIni = eZINI::instance( 'kickstart.ini', '.' );
        $groups = $kickstartIni->groups();
        if ( !is_array( $groups ) || count( $groups ) === 0 )
        {
            $this->cli->error( 'No groups found in kickstart.ini' );
            return 1;
        }
        $this->cli->output( 'Sections found: ' . implode( ', ', array_keys( $groups ) ) );
        $this->listSteps();
        return 0;
    }

    private function executeStep( $step )
    {
        $className = 'eZStep' . $step['class'];
        $method    = 'handle' . $step['class'];

        if ( !method_exists( $this, $method ) )
        {
            $stepObject = new $className( $this->tpl, $this->http, $this->ini, $this->persistenceList );
            $ret = $stepObject->init();
            if ( $ret === true )
                return true;
            if ( $ret === false )
                return $this->reportStepFailure( $step['class'], $stepObject );
            return $this->reportStepRedirect( $step['class'], $ret );
        }

        return $this->$method( $step );
    }

    private function handleWelcome()
    {
        $stepObject = new eZStepWelcome( $this->tpl, $this->http, $this->ini, $this->persistenceList );
        $this->http->setPostVariable( 'eZSetupWizardLanguage', 'eng-GB' );
        $stepObject->init();
        $stepObject->processPostData();
        return true;
    }

    private function handleSystemCheck()
    {
        $stepObject = new eZStepSystemCheck( $this->tpl, $this->http, $this->ini, $this->persistenceList );
        $ret = $stepObject->init();
        if ( $ret === true )
            return true;
        return $this->reportStepFailure( 'SystemCheck', $stepObject );
    }

    private function handleSystemFinetune()
    {
        $stepObject = new eZStepSystemFinetune( $this->tpl, $this->http, $this->ini, $this->persistenceList );
        $ret = $stepObject->init();
        if ( $ret === true )
            return true;
        $stepObject->processPostData();
        return true;
    }

    private function handleEmailSettings()
    {
        $stepObject = new eZStepEmailSettings( $this->tpl, $this->http, $this->ini, $this->persistenceList );
        $ret = $stepObject->init();
        if ( $ret === true )
            return true;
        return $this->reportStepFailure( 'EmailSettings', $stepObject );
    }

    private function handleDatabaseChoice()
    {
        $stepObject = new eZStepDatabaseChoice( $this->tpl, $this->http, $this->ini, $this->persistenceList );
        $ret = $stepObject->init();
        if ( $ret === true )
            return true;
        return $this->reportStepFailure( 'DatabaseChoice', $stepObject );
    }

    private function handleDatabaseInit()
    {
        $stepObject = new eZStepDatabaseInit( $this->tpl, $this->http, $this->ini, $this->persistenceList );
        $ret = $stepObject->init();
        if ( $ret === true )
            return true;
        return $this->reportStepFailure( 'DatabaseInit', $stepObject );
    }

    private function handleLanguageOptions()
    {
        $stepObject = new eZStepLanguageOptions( $this->tpl, $this->http, $this->ini, $this->persistenceList );
        $ret = $stepObject->init();
        if ( $ret === true )
            return true;
        return $this->reportStepFailure( 'LanguageOptions', $stepObject );
    }

    private function handleSiteTypes()
    {
        $stepObject = new eZStepSiteTypes( $this->tpl, $this->http, $this->ini, $this->persistenceList );
        $ret = $stepObject->init();

        // Dry-run imports into a temporary repository; clean it up as soon as
        // the step is done so it does not linger in var/storage/packages.
        if ( !empty( $this->options['dry-run'] ) )
        {
            $this->cleanupDryRun();
        }

        if ( $ret === true )
            return true;
        return $this->reportStepFailure( 'SiteTypes', $stepObject );
    }

    private function handlePackageLanguageOptions()
    {
        $stepObject = new eZStepPackageLanguageOptions( $this->tpl, $this->http, $this->ini, $this->persistenceList );
        $ret = $stepObject->init();
        if ( $ret === true )
            return true;

        $missed = $stepObject->MissedPackageLanguageList;
        if ( is_array( $missed ) && count( $missed ) > 0 )
        {
            $primary = isset( $this->persistenceList['regional_info']['primary_language'] )
                ? $this->persistenceList['regional_info']['primary_language']
                : 'eng-GB';
            $map = array();
            foreach ( $missed as $language )
            {
                $map[$language['locale']] = $primary;
            }
            $this->http->setPostVariable( 'eZSetupPackageLanguageMap', $map );
            $stepObject->processPostData();
            return true;
        }

        return $this->reportStepFailure( 'PackageLanguageOptions', $stepObject );
    }

    private function handleSiteAccess()
    {
        $stepObject = new eZStepSiteAccess( $this->tpl, $this->http, $this->ini, $this->persistenceList );
        $ret = $stepObject->init();
        if ( $ret === true )
            return true;
        return $this->reportStepFailure( 'SiteAccess', $stepObject );
    }

    private function handleSiteDetails()
    {
        $stepObject = new eZStepSiteDetails( $this->tpl, $this->http, $this->ini, $this->persistenceList );
        $ret = $stepObject->init();
        if ( $ret === true )
            return true;
        if ( is_string( $ret ) )
            return $this->reportStepRedirect( 'SiteDetails', $ret );
        return $this->reportStepFailure( 'SiteDetails', $stepObject );
    }

    private function handleSiteAdmin()
    {
        $stepObject = new eZStepSiteAdmin( $this->tpl, $this->http, $this->ini, $this->persistenceList );
        $ret = $stepObject->init();
        if ( $ret === true )
            return true;
        return $this->reportStepFailure( 'SiteAdmin', $stepObject );
    }

    private function handleSecurity()
    {
        $stepObject = new eZStepSecurity( $this->tpl, $this->http, $this->ini, $this->persistenceList );
        $ret = $stepObject->init();
        if ( $ret === true )
            return true;
        $stepObject->processPostData();
        return true;
    }

    private function handleRegistration()
    {
        $stepObject = new eZStepRegistration( $this->tpl, $this->http, $this->ini, $this->persistenceList );
        $ret = $stepObject->init();
        if ( $ret === true )
            return true;
        $stepObject->processPostData();
        return true;
    }

    private function handleCreateSites()
    {
        $stepObject = new eZStepCreateSites( $this->tpl, $this->http, $this->ini, $this->persistenceList );
        $ret = $stepObject->init();
        if ( $ret === true )
            return true;
        return $this->reportStepFailure( 'CreateSites', $stepObject );
    }

    private function handleFinal()
    {
        $stepObject = new eZStepFinal( $this->tpl, $this->http, $this->ini, $this->persistenceList );
        $stepObject->init();
        $stepObject->processPostData();
        return 'break';
    }

    private function reportStepFailure( $stepName, $stepObject )
    {
        $this->cli->output( '' );
        $this->cli->output( $this->cli->stylize( 'error', 'Step ' . $stepName . ' failed:' ) );

        if ( $stepObject instanceof eZStepSystemCheck &&
             $stepObject->Result !== null &&
             $stepObject->Result !== EZ_SETUP_TEST_SUCCESS )
        {
            foreach ( $stepObject->Results as $resultItem )
            {
                if ( $resultItem[0] != EZ_SETUP_TEST_SUCCESS )
                {
                    $msg = $resultItem[1];
                    if ( is_array( $resultItem[2] ) && !empty( $resultItem[2]['message'] ) )
                    {
                        $msg .= ' - ' . $resultItem[2]['message'];
                    }
                    $this->cli->output( '  - ' . $msg );
                }
            }
            return false;
        }

        if ( isset( $stepObject->Error ) && !empty( $stepObject->Error ) )
        {
            if ( is_int( $stepObject->Error ) )
            {
                $info = $this->databaseErrorInfo( $stepObject->Error );
                if ( is_array( $info ) && isset( $info['text'] ) )
                {
                    $this->cli->output( '  - ' . $info['text'] );
                }
                else
                {
                    $this->cli->output( '  - Database error code: ' . $stepObject->Error );
                }
            }
            elseif ( is_array( $stepObject->Error ) )
            {
                if ( isset( $stepObject->Error['errors'] ) && is_array( $stepObject->Error['errors'] ) )
                {
                    foreach ( $stepObject->Error['errors'] as $error )
                    {
                        if ( is_array( $error ) )
                        {
                            $this->cli->output( '  - ' . ( isset( $error['text'] ) ? $error['text'] : $error['code'] ) );
                        }
                        else
                        {
                            $this->cli->output( '  - ' . $error );
                        }
                    }
                }
                elseif ( isset( $stepObject->Error[0]['type'] ) && $stepObject->Error[0]['type'] === 'db' )
                {
                    $info = $this->databaseErrorInfo( $stepObject->Error[0]['error_code'] );
                    $this->cli->output( '  - ' . ( is_array( $info ) && isset( $info['text'] ) ? $info['text'] : 'Database error' ) );
                }
                elseif ( isset( $stepObject->Error[0] ) )
                {
                    $this->cli->output( '  - Error: ' . $stepObject->Error[0] );
                }
                else
                {
                    $this->cli->output( '  - ' . print_r( $stepObject->Error, true ) );
                }
            }
            return false;
        }

        if ( isset( $stepObject->ErrorMsg ) && $stepObject->ErrorMsg )
        {
            $this->cli->output( '  - ' . $stepObject->ErrorMsg );
            return false;
        }

        $this->cli->output( '  - Unknown failure' );
        return false;
    }

    private function reportStepRedirect( $stepName, $redirect )
    {
        $this->cli->output( '' );
        $this->cli->output( $this->cli->stylize( 'warning', 'Step ' . $stepName . ' requested redirect to: ' . $redirect ) );
        return false;
    }

    private function databaseErrorInfo( $errorCode )
    {
        $installer = new eZStepInstaller( $this->tpl, $this->http, $this->ini, $this->persistenceList, 'database', 'Database' );
        return $installer->databaseErrorInfo(
            array(
                'error_code' => $errorCode,
                'database_info' => isset( $this->persistenceList['database_info'] ) ? $this->persistenceList['database_info'] : array(),
            )
        );
    }

    private function clearKickstartCache()
    {
        // config.php disables EZP_INI_FILEMTIME_CHECK, so stale eZINI kickstart
        // caches will not be invalidated. Delete them explicitly before the setup
        // wizard reads kickstart.ini.
        $cacheDir = isset( $GLOBALS['eZINI_CONFIG_CACHE_DIR'] )
            ? $GLOBALS['eZINI_CONFIG_CACHE_DIR']
            : $this->rootDir . '/var/cache/ini/';

        foreach ( glob( $cacheDir . 'kickstart-*.php' ) ?: array() as $file )
        {
            @unlink( $file );
        }
    }

    private function cleanupDryRun()
    {
        $dryRunPath = $this->rootDir . '/var/storage/packages/dryrun';
        if ( is_dir( $dryRunPath ) )
        {
            eZPackage::removeFiles( $dryRunPath );
        }
    }

    private function showSummary()
    {
        $this->cli->output( '' );
        $this->cli->output( 'Setup summary' );
        $this->cli->output( str_repeat( '-', 50 ) );

        if ( !isset( $this->persistenceList['chosen_site_package']['0'] ) )
        {
            $this->cli->output( 'No site type was selected. Stopped at step: ' . $this->stopStep );
            return;
        }

        $installer = new eZStepInstaller( $this->tpl, $this->http, $this->ini, $this->persistenceList, 'summary', 'Summary' );
        $siteType = $installer->chosenSiteType();
        $urls     = $installer->siteaccessURLs();

        $this->cli->output( 'Site package: ' . $siteType['identifier'] );
        $this->cli->output( 'Title:        ' . $siteType['title'] );
        $this->cli->output( 'Database:     ' . $siteType['database'] );
        $this->cli->output( 'Access type:  ' . $siteType['access_type'] );
        $this->cli->output( 'Site URL:     ' . $urls['url'] );
        $this->cli->output( 'Admin URL:    ' . $urls['admin_url'] );
    }
}
