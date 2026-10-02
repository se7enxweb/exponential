<?php
/**
 * The code of kernel/user/password.php, moved into a class (#207 stage 1). The file kernel/user/password.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/user/password.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\User
{

class Password extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $ini = \eZINI::instance();
        $currentUser = \eZUser::currentUser();
        $currentUserID = $currentUser->attribute( "contentobject_id" );
        $http = \eZHTTPTool::instance();
        $Module = $Params['Module'];
        $message = 0;
        $oldPasswordNotValid = 0;
        $newPasswordNotMatch = 0;
        $newPasswordTooShort = 0;
        $userRedirectURI = '';

        $userRedirectURI = $Module->actionParameter( 'UserRedirectURI' );

        $userRedirectURI = $http->postVariable( 'RedirectURI', $http->sessionVariable( 'LastAccessesURI', '/' ) );

        $redirectionURI = $userRedirectURI;
        if ( $redirectionURI == '' )
             $redirectionURI = $ini->variable( 'SiteSettings', 'DefaultPage' );

        if( !isset( $oldPassword ) )
            $oldPassword = '';

        if( !isset( $newPassword ) )
            $newPassword = '';

        if( !isset( $confirmPassword ) )
            $confirmPassword = '';

        if ( is_numeric( $Params["UserID"] ) )
            $UserID = $Params["UserID"];
        else
            $UserID = $currentUserID;

        $user = \eZUser::fetch( $UserID );
        if ( !$user )
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
        $currentUser = \eZUser::currentUser();
        if ( $currentUser->attribute( 'contentobject_id' ) != $user->attribute( 'contentobject_id' ) or
             !$currentUser->isRegistered() )
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );

        if ( $http->hasPostVariable( "OKButton" ) )
        {
            if ( $http->hasPostVariable( "oldPassword" ) )
            {
                $oldPassword = $http->postVariable( "oldPassword" );
            }
            if ( $http->hasPostVariable( "newPassword" ) )
            {
                $newPassword = $http->postVariable( "newPassword" );
            }
            if ( $http->hasPostVariable( "confirmPassword" ) )
            {
                $confirmPassword = $http->postVariable( "confirmPassword" );
            }
            // A form can post any of these as an array; treat that as not given
            if ( isset( $oldPassword ) && !is_string( $oldPassword ) ) $oldPassword = "";
            if ( isset( $newPassword ) && !is_string( $newPassword ) ) $newPassword = "";
            if ( isset( $confirmPassword ) && !is_string( $confirmPassword ) ) $confirmPassword = "";

            $login = $user->attribute( "login" );
            $type = $user->attribute( "password_hash_type" );
            $hash = $user->attribute( "password_hash" );
            $site = $user->site();
            if ( $user->authenticateHash( $login, $oldPassword, $site, $type, $hash ) )
            {
                if (  $newPassword == $confirmPassword )
                {
                    $minPasswordLength = $ini->hasVariable( 'UserSettings', 'MinPasswordLength' ) ? $ini->variable( 'UserSettings', 'MinPasswordLength' ) : 3;

                    if ( strlen( $newPassword ) < $minPasswordLength )
                    {
                        $newPasswordTooShort = 1;
                    }
                    else
                    {
                        // Change user password
                        if ( \eZOperationHandler::operationIsAvailable( 'user_password' ) )
                        {
                            $operationResult = \eZOperationHandler::execute( 'user',
                                                                            'password', array( 'user_id'    => $UserID,
                                                                                               'new_password'  => $newPassword ) );
                        }
                        else
                        {
                            \eZUserOperationCollection::password( $UserID, $newPassword );
                        }
                    }
                    $message = true;
                    $newPassword = '';
                    $oldPassword = '';
                    $confirmPassword = '';

                }
                else
                {
                    $newPassword = "";
                    $confirmPassword = "";
                    $newPasswordNotMatch = 1;
                    $message = true;
                }
            }
            else
            {
                $oldPassword = "";
                $oldPasswordNotValid = 1;
                $message = true;
            }
        }

        if ( $http->hasPostVariable( "CancelButton" ) )
        {
            if ( $http->hasPostVariable( "RedirectOnCancel" ) )
            {
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectTo( $http->postVariable( "RedirectOnCancel" ) ) );
            }
            \eZRedirectManager::redirectTo( $Module, $redirectionURI );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        $Module->setTitle( "Edit user information" );
        // Template handling

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( "module", $Module );
        $tpl->setVariable( "http", $http );
        $tpl->setVariable( "userID", $UserID );
        $tpl->setVariable( "userAccount", $user );
        $tpl->setVariable( "oldPassword", $oldPassword );
        $tpl->setVariable( "newPassword", $newPassword );
        $tpl->setVariable( "confirmPassword", $confirmPassword );
        $tpl->setVariable( "oldPasswordNotValid", $oldPasswordNotValid );
        $tpl->setVariable( "newPasswordNotMatch", $newPasswordNotMatch );
        $tpl->setVariable( "newPasswordTooShort", $newPasswordTooShort );
        $tpl->setVariable( "message", $message );

        $Result = array();
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/user', 'User' ),
                                        'url' => false ),
                                 array( 'text' => \ezpI18n::tr( 'kernel/user', 'Change password' ),
                                        'url' => false ) );
        $Result['content'] = $tpl->fetch( "design:user/password.tpl" );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
