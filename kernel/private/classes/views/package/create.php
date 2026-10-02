<?php
/**
 * The code of kernel/package/create.php, moved into a class (#207 stage 1). The file kernel/package/create.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/package/create.php:
 *
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Package
{

class Create extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $module = $Params['Module'];

        $http = \eZHTTPTool::instance();

        $creator = false;
        $initializeStep = false;
        if ( $module->isCurrentAction( 'CreatePackage' ) )
        {
            $creatorID = $module->actionParameter( 'CreatorItemID' );
            if ( $creatorID )
            {
                $creator = \eZPackageCreationHandler::instance( $creatorID );
                $persistentData = array();
                $http->setSessionVariable( 'eZPackageCreatorData' . $creatorID, $persistentData );
                $initializeStep = true;
                $package = false;
                if ( isset( $persistentData['package_name'] ) )
                    $package = \eZPackage::fetch( $persistentData['package_name'] );
                $creator->generateStepMap( $package, $persistentData );
            }
        }
        else if ( $module->isCurrentAction( 'PackageStep' ) )
        {
            if ( $module->hasActionParameter( 'CreatorItemID' ) )
            {
                $creatorID = $module->actionParameter( 'CreatorItemID' );
                $creator = \eZPackageCreationHandler::instance( $creatorID );
                if ( $http->hasSessionVariable( 'eZPackageCreatorData' . $creatorID ) )
                    $persistentData = $http->sessionVariable( 'eZPackageCreatorData' . $creatorID );
                else
                    $persistentData = array();
                $package = false;
                if ( isset( $persistentData['package_name'] ) )
                    $package = \eZPackage::fetch( $persistentData['package_name'] );
                $creator->generateStepMap( $package, $persistentData );
            }
        }

        $tpl = \eZTemplate::factory();

        $templateName = 'design:package/create.tpl';
        if ( $creator )
        {
            $currentStepID = false;
            if ( $module->hasActionParameter( 'CreatorStepID' ) )
                $currentStepID = $module->actionParameter( 'CreatorStepID' );
            $steps =& $creator->stepMap();
            if ( !isset( $steps['map'][$currentStepID] ) )
                $currentStepID = $steps['first']['id'];
            $errorList = array();
            $hasAdvanced = false;

            $lastStepID = $currentStepID;
            if ( $module->hasActionParameter( 'PreviousStep' ) )
            {
                // Back: what was entered on this step is kept when it is valid, as Next would keep it, but an error
                // never holds the visitor here. The step returned to is not initialized again: its initializer resets
                // its fields, and they hold what was entered there before.
                $backErrorList = array();
                if ( $creator->validateStep( $package, $http, $currentStepID, $steps, $persistentData, $backErrorList ) != $currentStepID )
                {
                    $creator->commitStep( $package, $http, $steps['map'][$currentStepID], $persistentData, $tpl );
                }
                $previousStepID = $steps['map'][$currentStepID]['previous_step'];
                if ( $previousStepID && isset( $steps['map'][$previousStepID] ) )
                {
                    $currentStepID = $previousStepID;
                    $initializeStep = false;
                }
                else
                {
                    // Back from the first step is the choice of wizard.
                    $http->removeSessionVariable( 'eZPackageCreatorData' . $creatorID );
                    return $this->viewResult( isset( $Result ) ? $Result : null,  $module->redirectToView( 'create' ) );
                }
            }
            else if ( $module->hasActionParameter( 'NextStep' ) )
            {
                $hasAdvanced = true;
                $currentStepID = $creator->validateStep( $package, $http, $currentStepID, $steps, $persistentData, $errorList );
                if ( $currentStepID != $lastStepID )
                {
                    $lastStep =& $steps['map'][$lastStepID];
                    $creator->commitStep( $package, $http, $lastStep, $persistentData, $tpl );
                    $initializeStep = true;
                }
            }

            if ( $currentStepID )
            {
                $currentStep =& $steps['map'][$currentStepID];

                $stepTemplate = $creator->stepTemplate( $currentStep );
                $stepTemplateName = $stepTemplate['name'];
                $stepTemplateDir = $stepTemplate['dir'];

                // A step is initialized the first time it is entered only: its initializer fills in defaults, and on a return
                // to it (Back, then Next again) it would overwrite what was entered there.
                if ( $initializeStep && empty( $persistentData['entered_steps'][$currentStepID] ) )
                {
                    $creator->initializeStep( $package, $http, $currentStep, $persistentData, $tpl );
                    $persistentData['entered_steps'][$currentStepID] = true;
                }

                $creator->loadStep( $package, $http, $currentStepID, $persistentData, $tpl, $module );
                if ( $package )
                    $persistentData['package_name'] = $package->attribute( 'name' );

                $http->setSessionVariable( 'eZPackageCreatorData' . $creatorID, $persistentData );

                $tpl->setVariable( 'creator', $creator );
                $tpl->setVariable( 'current_step', $currentStep );
                $tpl->setVariable( 'persistent_data', $persistentData );
                $tpl->setVariable( 'error_list', $errorList );
                $tpl->setVariable( 'package', $package );

                $templateName = "design:package/$stepTemplateDir/$stepTemplateName";
            }
            else
            {
                $creator->finalize( $package, $http, $persistentData );
                $package->setAttribute( 'is_active', true );
                $http->removeSessionVariable( 'eZPackageCreatorData' . $creatorID );
                if ( $package )
                    return $this->viewResult( isset( $Result ) ? $Result : null,  $module->redirectToView( 'view', array( 'full', $package->attribute( 'name' ) ) ) );
                else
                    return $this->viewResult( isset( $Result ) ? $Result : null,  $module->redirectToView( 'list' ) );
            }
        }
        else
        {
            $creators =& \eZPackageCreationHandler::creatorList( true );

            $tpl->setVariable( 'creator_list', $creators );
        }

        $Result = array();
        $Result['content'] = $tpl->fetch( $templateName );
        $Result['path'] = array( array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'kernel/package', 'Create package' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
