<?php
/**
 * The code of kernel/class/translation.php, moved into a class (#207 stage 1). The file kernel/class/translation.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/class/translation.php:
 *
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Class
{

class Translation extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $module = $Params['Module'];


        if ( !$module->hasActionParameter( 'ClassID' ) )
        {
            \eZDebug::writeError( 'Missing ClassID parameter for action ' . $module->currentAction(),
                                 'class/translation' );
            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->redirectToView( 'grouplist' ) );
        }

        $classID = $module->actionParameter( 'ClassID' );

        if ( !$module->hasActionParameter( 'LanguageCode' ) )
        {
            \eZDebug::writeError( 'Missing LanguageCode parameter for action ' . $module->currentAction(),
                                 'class/translation' );
            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->redirectToView( 'view', array( $classID ) ) );
        }

        $languageCode = $module->actionParameter( 'LanguageCode' );

        if ( $module->isCurrentAction( 'Cancel' ) )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->redirectToView( 'view', array( $classID ), array( 'Language' => $languageCode ) ) );
        }

        $class = \eZContentClass::fetch( $classID );

        if ( !$class )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
        }

        if ( $module->isCurrentAction( 'UpdateInitialLanguage' ) )
        {
            if ( $module->hasActionParameter( 'InitialLanguageID' ) )
            {
                $newInitialLanguageID = $module->actionParameter( 'InitialLanguageID' );

                $language = \eZContentLanguage::fetch( $newInitialLanguageID );
                if ( $language )
                {
                    $class->setAttribute( 'initial_language_id', $newInitialLanguageID );
                    $class->setAlwaysAvailableLanguageID( $newInitialLanguageID );
                }
            }

            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->redirectToView( 'view', array( $classID ), array( 'Language' => $languageCode ) ) );
        }
        else if ( $module->isCurrentAction( 'RemoveTranslation' ) )
        {
            if ( !$module->hasActionParameter( 'LanguageID' ) )
            {
                return $this->viewResult( isset( $Result ) ? $Result : null,  $module->redirectToView( 'view', array( $classID ), array( 'Language' => $languageCode ) ) );
            }

            $languageIDArray = $module->actionParameter( 'LanguageID' );

            if ( $module->hasActionParameter( 'ConfirmRemoval' ) && $module->actionParameter( 'ConfirmRemoval' ) )
            {
                foreach( $languageIDArray as $languageID )
                {
                    if ( !$class->removeTranslation( $languageID ) )
                    {
                        \eZDebug::writeError( "Class with id " . $class->attribute( 'id' ) . ": cannot remove the translation with language id $languageID!", 'class/translation' );
                    }
                }

                //probably we've just removed translation we were viewing.
                if ( !$class->hasNameInLanguage( $languageCode ) )
                    $languageCode = $class->alwaysAvailableLanguageLocale();

                return $this->viewResult( isset( $Result ) ? $Result : null,  $module->redirectToView( 'view', array( $classID ), array( 'Language' => $languageCode ) ) );
            }

            $languages = array();
            foreach( $languageIDArray as $languageID )
            {
                $language = \eZContentLanguage::fetch( $languageID );
                if ( $language )
                {
                    $languages[] = $language;
                }
            }

            if ( !$languages )
            {
                return $this->viewResult( isset( $Result ) ? $Result : null,  $module->redirectToView( 'view', array( $classID ), array( $languageCode ) ) );
            }

            $tpl = \eZTemplate::factory();

            $tpl->setVariable( 'class_id', $classID );
            $tpl->setVariable( 'class', $class );
            $tpl->setVariable( 'language_code', $languageCode );
            $tpl->setVariable( 'languages', $languages );

            $Result = array();
            $Result['content'] = $tpl->fetch( 'design:class/removetranslation.tpl' );
            $Result['path'] = array( array( 'url' => false,
                                            'text' => \ezpI18n::tr( 'kernel/class', 'Remove translation' ) ) );

            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
