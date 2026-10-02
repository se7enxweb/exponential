<?php
/**
 * The code of kernel/user/setting.php, moved into a class (#207 stage 1). The file kernel/user/setting.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/user/setting.php:
 *
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\User
{

class Setting extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];
        if ( isset( $Params["UserID"] ) )
            $UserID = $Params["UserID"];

        $http = \eZHTTPTool::instance();

        $user = \eZUser::fetch( $UserID );
        if ( !$user )
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
        $userObject = $user->attribute( 'contentobject' );
        if ( !$userObject )
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
        $userSetting = \eZUserSetting::fetch( $UserID );

        if ( $http->hasPostVariable( "UpdateSettingButton" ) )
        {
            $isEnabled = 0;
            if ( $http->hasPostVariable( 'max_login' ) )
            {
                $maxLogin = $http->postVariable( 'max_login' );
            }
            else
            {
                $maxLogin = $userSetting->attribute( 'max_login' );
            }
            if ( $http->hasPostVariable( 'is_enabled' ) )
            {
                $isEnabled = 1;
            }

            if ( \eZOperationHandler::operationIsAvailable( 'user_setsettings' ) )
            {
                   $operationResult = \eZOperationHandler::execute( 'user',
                                                                   'setsettings', array( 'user_id'    => $UserID,
                                                                                          'is_enabled' => $isEnabled,
                                                                                          'max_login'  => $maxLogin ) );
            }
            else
            {
                \eZUserOperationCollection::setSettings( $UserID, $isEnabled, $maxLogin );
            }

            $Module->redirectTo( '/content/view/full/' . $userObject->attribute( 'main_node_id' ) );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        if ( $http->hasPostVariable( "CancelSettingButton" ) )
        {
            $Module->redirectTo( '/content/view/full/' . $userObject->attribute( 'main_node_id' ) );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        if ( $http->hasPostVariable( "ResetFailedLoginButton" ) )
        {
            // Reset number of failed login attempts
            \eZUser::setFailedLoginAttempts( $UserID, 0, true );
        }

        $failedLoginAttempts = $user->failedLoginAttempts();
        $maxFailedLoginAttempts = \eZUser::maxNumberOfFailedLogin();

        $Module->setTitle( "Edit user settings" );
        // Template handling

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( "module", $Module );
        $tpl->setVariable( "http", $http );
        $tpl->setVariable( "userID", $UserID );
        $tpl->setVariable( "user", $user );
        $tpl->setVariable( "userSetting", $userSetting );
        $tpl->setVariable( "failed_login_attempts", $failedLoginAttempts );
        $tpl->setVariable( "max_failed_login_attempts", $maxFailedLoginAttempts );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:user/setting.tpl" );
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/user', 'User' ),
                                        'url' => false ),
                                 array( 'text' => \ezpI18n::tr( 'kernel/user', 'Setting' ),
                                        'url' => false ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
