<?php
/**
 * The code of kernel/package/install.php, moved into a class (#207 stage 1). The file kernel/package/install.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/package/install.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Package
{

class Install extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $http = \eZHTTPTool::instance();

        $module = $Params['Module'];
        $packageName = $Params['PackageName'];
        $installer = false;
        $currentItem = 0;
        $displayStep = false;

        // use session data when performing install steps (currentAction is set)
        if ( $module->currentAction() && $http->hasSessionVariable( 'eZPackageInstallerData' ) )
        {
            $persistentData = $http->sessionVariable( 'eZPackageInstallerData' );
            if ( isset( $persistentData['currentItem'] ) )
                $currentItem = $persistentData['currentItem'];
            $packageName = $persistentData['package_name'];
        }
        else
        {
            $persistentData = array();
            $persistentData['package_name'] = $packageName;
            $persistentData['currentItem'] = $currentItem;
            $persistentData['doItemInstall'] = false;
            $persistentData['error'] = array();
            $persistentData['error_default_actions'] = array();
        }

        if ( !\eZPackage::canUsePolicyFunction( 'install' ) )
            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );

        // Package installs can be large and otherwise hit web PHP execution timeouts.
        if ( function_exists( 'set_time_limit' ) )
        {
            @set_time_limit( 0 );
        }
        @ini_set( 'max_execution_time', '0' );

        // a package name that is a directory name (eZPackageRequestGuard), and the install policy for this
        // package's own type: the check above only asks whether the policy exists for any type
        $package = \eZPackageRequestGuard::isSafeName( $packageName ) ? \eZPackage::fetch( $packageName ) : false;
        if ( !$package )
            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
        if ( !$package->attribute( 'can_install' ) )
            return $this->viewResult( null, $module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );

        $installItemArray = $package->installItemsList( false, \eZSys::osType() );

        $tpl = \eZTemplate::factory();

        if ( $module->isCurrentAction( 'SkipPackage' ) )
        {
            $http->removeSessionVariable( 'eZPackageInstallerData' );
            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->redirectToView( 'view', array( 'full', $package->attribute( 'name' ) ) ) );
        }
        elseif ( $module->isCurrentAction( 'InstallPackage' ) )
        {
            $persistentData['doItemInstall'] = true;
        }
        elseif ( $module->isCurrentAction( 'HandleError' ) )
        {
            $persistentData['doItemInstall'] = true;

            // Choosing error action
            if ( $module->hasActionParameter( 'ActionID' ) )
            {
                $choosenAction = $module->actionParameter( 'ActionID' );

                $persistentData['error']['choosen_action'] = $choosenAction;
                if ( $module->hasActionParameter( 'RememberAction' ) )
                {
                    $errorCode = $persistentData['error']['error_code'];
                    $itemType = $installItemArray[$currentItem]['type'];
                    if ( !isset( $persistentData['error_default_actions'][$itemType] ) )
                        $persistentData['error_default_actions'][$itemType] = array();
                    $persistentData['error_default_actions'][$itemType][$errorCode] = $choosenAction;
                }
            }
            elseif ( !isset( $persistentData['error']['error_code'] ) )
            {
                // If this is an unhandled error, we are skipping this item
                $currentItem++;
            }
        }
        elseif ( $module->isCurrentAction( 'PackageStep' ) && !$persistentData['doItemInstall'] )
        {
            $installItem = $installItemArray[$currentItem];
            $installerType = $module->actionParameter( 'InstallerType' );
            $installer = \eZPackageInstallationHandler::instance( $package, $installerType, $installItem );
            $installer->generateStepMap( $package, $persistentData );
            $displayStep = true;
        }
        elseif ( !$persistentData['doItemInstall'] )
        {
            // Displaying a list of items to install
            $installElements = array();
            foreach ( $installItemArray as $installItem )
            {
                $handler = \eZPackage::packageHandler( $installItem['type'] );
                if ( $handler )
                {
                    $installElement = $handler->explainInstallItem( $package, $installItem );
                    if ( $installElement )
                    {
                        if ( isset( $installElement[0] ) )
                            $installElements = array_merge( $installElements, $installElement );
                        else
                            $installElements[] = $installElement;
                    }
                }
            }
            $tpl->setVariable( 'install_elements', $installElements );

            $templateName = 'design:package/install.tpl';
        }

        if ( $persistentData['doItemInstall'] )
        {
            if ( !isset( $persistentData['language_map'] ) || !is_array( $persistentData['language_map'] ) )
                $persistentData['language_map'] = $package->defaultLanguageMap();

            $packageINI = \eZINI::instance( 'package.ini' );
            $installItemCount = count( $installItemArray );
            $batchMaxItems = 8;
            if ( $packageINI->hasVariable( 'InstallerSettings', 'InstallBatchMaxItems' ) )
            {
                $batchMaxItems = (int)$packageINI->variable( 'InstallerSettings', 'InstallBatchMaxItems' );
            }
            if ( $batchMaxItems < 1 )
            {
                $batchMaxItems = 1;
            }

            $batchTimeBudget = 3.0;
            if ( $packageINI->hasVariable( 'InstallerSettings', 'InstallBatchTimeBudgetSeconds' ) )
            {
                $batchTimeBudget = (float)$packageINI->variable( 'InstallerSettings', 'InstallBatchTimeBudgetSeconds' );
            }
            if ( $batchTimeBudget <= 0 )
            {
                $batchTimeBudget = 1.0;
            }

            $batchStartedAt = microtime( true );
            $processedInBatch = 0;
            $mustRedirectForNextBatch = false;

            while ( $currentItem < $installItemCount )
            {
                if ( $processedInBatch >= $batchMaxItems )
                {
                    $mustRedirectForNextBatch = true;
                    break;
                }

                if ( ( microtime( true ) - $batchStartedAt ) >= $batchTimeBudget )
                {
                    $mustRedirectForNextBatch = true;
                    break;
                }

                $installItem = $installItemArray[$currentItem];
                if ( !isset( $installItem['type'] ) )
                {
                    $persistentData['error'] = array( 'error_code' => 'invalid_install_item',
                                                      'description' => \ezpI18n::tr( 'kernel/package', 'Package install item is invalid (missing type).' ) );
                    $templateName = "design:package/install_error.tpl";
                    break;
                }

                $installer = \eZPackageInstallationHandler::instance( $package, $installItem['type'], $installItem );

                if ( $installer && !isset( $persistentData['error']['choosen_action'] ) )
                {
                    $persistentData['doItemInstall'] = false;
                    $installer->generateStepMap( $package, $persistentData );
                    $displayStep = true;
                    break;
                }

                try
                {
                    $result = $package->installItem( $installItem, $persistentData );
                }
                catch ( \Exception $e )
                {
                    $persistentData['error'] = array( 'error_code' => 'exception',
                                                      'description' => \ezpI18n::tr( 'kernel/package', 'Install failed with exception: ' ) . $e->getMessage() );
                    \eZDebug::writeError( "Package install exception for item type '" . $installItem['type'] . "': " . $e->getMessage(), __METHOD__ );
                    $templateName = "design:package/install_error.tpl";
                    break;
                }

                if ( !$result )
                {
                    $templateName = "design:package/install_error.tpl";
                    break;
                }

                $persistentData['error'] = array();
                $currentItem++;
                ++$processedInBatch;
            }

            if ( !$displayStep &&
                 !isset( $templateName ) &&
                 $currentItem < $installItemCount &&
                 $mustRedirectForNextBatch )
            {
                $persistentData['currentItem'] = $currentItem;
                $http->setSessionVariable( 'eZPackageInstallerData', $persistentData );
                return $this->viewResult( isset( $Result ) ? $Result : null,  $module->redirectToView( 'install', array( $packageName ) ) );
            }
        }

        //$templateName = 'design:package/install.tpl';
        if ( $displayStep )
        {
            $currentStepID = false;
            if ( $module->hasActionParameter( 'InstallStepID' ) )
                $currentStepID = $module->actionParameter( 'InstallStepID' );
            $steps =& $installer->stepMap();
            if ( !isset( $steps['map'][$currentStepID] ) )
                $currentStepID = $steps['first']['id'];
            $errorList = array();
            $hasAdvanced = false;

            $lastStepID = $currentStepID;
            $initializeStep = true;
            if ( $module->hasActionParameter( 'PreviousStep' ) )
            {
                // Back: keeps this step's choices when they are valid, and returns to the previous step without
                // initializing it again, so its own choices are still there.
                $backErrorList = array();
                if ( $installer->validateStep( $package, $http, $currentStepID, $steps, $persistentData, $backErrorList ) != $currentStepID )
                {
                    $installer->commitStep( $package, $http, $steps['map'][$currentStepID], $persistentData, $tpl );
                }
                $previousStepID = $steps['map'][$currentStepID]['previous_step'];
                if ( $previousStepID && isset( $steps['map'][$previousStepID] ) )
                {
                    $currentStepID = $previousStepID;
                    $initializeStep = false;
                }
            }
            else if ( $module->hasActionParameter( 'NextStep' ) )
            {
                $hasAdvanced = true;
                $currentStepID = $installer->validateStep( $package, $http, $currentStepID, $steps, $persistentData, $errorList );
                if ( $currentStepID != $lastStepID )
                {
                    $lastStep =& $steps['map'][$lastStepID];
                    $installer->commitStep( $package, $http, $lastStep, $persistentData, $tpl );
                }
            }

            if ( $currentStepID )
            {
                $currentStep =& $steps['map'][$currentStepID];

                $stepTemplate = $installer->stepTemplate( $package, $installItem, $currentStep );
                $stepTemplateName = $stepTemplate['name'];
                $stepTemplatePath = $stepTemplate['path'];

                if ( $initializeStep )
                    $installer->initializeStep( $package, $http, $currentStep, $persistentData, $tpl, $module );

                //if ( $package )
                //    $persistentData['package_name'] = $package->attribute( 'name' );

                //$http->setSessionVariable( 'eZPackageInstallerData', $persistentData );

                $tpl->setVariable( 'installer', $installer );
                $tpl->setVariable( 'current_step', $currentStep );
                //$tpl->setVariable( 'persistent_data', $persistentData );
                $tpl->setVariable( 'error_list', $errorList );
                $tpl->setVariable( 'package', $package );

                $templateName = "$stepTemplatePath/$stepTemplateName";
            }
            else
            {
                $persistentData['doItemInstall'] = true;
                $installItem = $installItemArray[$currentItem];
                $result = $package->installItem( $installItem, $persistentData );
                if ( !$result )
                {
                    $templateName = "design:package/install_error.tpl";
                }
                else
                {
                    $currentItem++;
                    if ( $currentItem < count( $installItemArray ) )
                    {
                        $persistentData['error'] = array();
                        $persistentData['currentItem'] = $currentItem;
                        $http->setSessionVariable( 'eZPackageInstallerData', $persistentData );
                        return $this->viewResult( isset( $Result ) ? $Result : null,  $module->redirectToView( 'install', array( $packageName ) ) );
                    }
                }
            }
        }

        // Installation complete (all items are installed)
        if ( $currentItem >= count( $installItemArray ) )
        {
            $package->setInstalled();
            $http->removeSessionVariable( 'eZPackageInstallerData' );
            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->redirectToView( 'view', array( 'full', $package->attribute( 'name' ) ) ) );
        }

        $persistentData['currentItem'] = $currentItem;
        $http->setSessionVariable( 'eZPackageInstallerData', $persistentData );
        $tpl->setVariable( 'persistent_data', $persistentData );
        $tpl->setVariable( 'package', $package );

        $Result = array();
        $Result['content'] = $tpl->fetch( $templateName );
        $Result['path'] = array( array( 'url' => 'package/list',
                                        'text' => \ezpI18n::tr( 'kernel/package', 'Packages' ) ),
                                 array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'kernel/package', 'Install' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
