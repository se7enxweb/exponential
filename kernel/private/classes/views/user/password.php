<?php
/**
 * The code of kernel/user/password.php, moved into a class (#207 stage 1). The file kernel/user/password.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 *
 * The rules, the inline errors per field, the success state, ending the other sessions and the "your password
 * was changed" mail: doc/features/6.0/modern-password-change.md (rules in expPasswordPolicy).
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

        $oldPassword = '';
        $newPassword = '';
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

        $policy = new \expPasswordPolicy( $ini );
        $fieldIDs = \expPasswordPolicy::fieldIDs();
        $fieldErrors = array( 'oldPassword' => array(), 'newPassword' => array(), 'confirmPassword' => array() );
        $formErrors = array();
        $failedRules = array();
        $passwordChanged = false;
        $sessionsEnded = null;
        $notificationSent = false;

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
            if ( !is_string( $oldPassword ) ) $oldPassword = "";
            if ( !is_string( $newPassword ) ) $newPassword = "";
            if ( !is_string( $confirmPassword ) ) $confirmPassword = "";

            $login = $user->attribute( "login" );
            $type = $user->attribute( "password_hash_type" );
            $hash = $user->attribute( "password_hash" );
            $site = $user->site();
            if ( $user->authenticateHash( $login, $oldPassword, $site, $type, $hash ) )
            {
                // every problem of the new password at once: the rules and the confirmation
                $failedRules = $policy->validate( $newPassword, $user );
                foreach ( $failedRules as $ruleID )
                    $fieldErrors['newPassword'][] = $policy->errorText( $ruleID );
                if ( $newPassword !== $confirmPassword )
                {
                    $newPasswordNotMatch = 1;
                    $fieldErrors['confirmPassword'][] = \ezpI18n::tr( 'kernel/user/password', 'The two new passwords do not match.' );
                }
                if ( in_array( \expPasswordPolicy::RULE_LENGTH, $failedRules, true ) )
                    $newPasswordTooShort = 1;

                if ( !$failedRules && !$newPasswordNotMatch )
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
                    // the stored hash says whether it went through (a workflow can stop the operation)
                    $stored = \eZUser::fetch( $UserID );
                    $passwordChanged = $stored instanceof \eZUser && \expPasswordPolicy::isCurrentPassword( $stored, $newPassword );
                    if ( $passwordChanged )
                    {
                        $sessionsEnded = $policy->endOtherSessions( (int)$UserID );
                        $notificationSent = $policy->sendChangedMail( $stored, $sessionsEnded );
                    }
                    else
                        $formErrors[] = \ezpI18n::tr( 'kernel/user/password', 'The password could not be changed. Please try again later.' );
                }
                // the old flags for templates that know only those: a failure they cannot name shows no message
                // at all rather than their "successfully changed"
                $message = ( $passwordChanged || $newPasswordNotMatch || $newPasswordTooShort || $formErrors ) ? true : 0;
            }
            else
            {
                $oldPasswordNotValid = 1;
                $message = true;
                $fieldErrors['oldPassword'][] = \ezpI18n::tr( 'kernel/user/password', 'Your current password is not correct.' );
            }
        }
        // a typed password is never written back into the page
        $oldPassword = '';
        $newPassword = '';
        $confirmPassword = '';

        // Audit (doc/bc/6.0/audit.md, access.user.password.change.failed): a refused change, never a password; a
        // change that succeeds is recorded by eZUser::store() (access.user.password.change)
        if ( ( $oldPasswordNotValid || $newPasswordNotMatch || $failedRules ) && class_exists( 'expAuditHook' ) )
            \expAuditHook::emit( 'access.user.password.change.failed', array( 'object' => \expAuditHook::user( (int)$UserID ),
                'result' => 'refused', 'reason' => $oldPasswordNotValid ? 'credentials' : 'validation',
                'after' => array( 'rule' => $oldPasswordNotValid ? 'old_password' : ( $newPasswordNotMatch ? 'confirmation' : $failedRules[0] ) ) ) );

        if ( $http->hasPostVariable( "CancelButton" ) )
        {
            if ( $http->hasPostVariable( "RedirectOnCancel" ) )
            {
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectTo( $http->postVariable( "RedirectOnCancel" ) ) );
            }
            \eZRedirectManager::redirectTo( $Module, $redirectionURI );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        $errorSummary = array();
        foreach ( $formErrors as $text )
            $errorSummary[] = array( 'field' => '', 'field_id' => '', 'text' => $text );
        foreach ( $fieldErrors as $field => $texts )
            foreach ( $texts as $text )
                $errorSummary[] = array( 'field' => $field, 'field_id' => $fieldIDs[$field], 'text' => $text );

        $jsConfig = $policy->clientConfig( $user );

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
        // the modern page (doc/features/6.0/modern-password-change.md); old templates ignore these
        $tpl->setVariable( "password_changed", $passwordChanged );
        $tpl->setVariable( "min_length", $policy->minLength() );
        $tpl->setVariable( "password_rules", $policy->rulesForTemplate( $failedRules ) );
        $tpl->setVariable( "failed_rules", $failedRules );
        $tpl->setVariable( "field_errors", $fieldErrors );
        $tpl->setVariable( "field_ids", $fieldIDs );
        $tpl->setVariable( "error_summary", $errorSummary );
        $tpl->setVariable( "has_errors", count( $errorSummary ) > 0 );
        $tpl->setVariable( "sessions_ended", $sessionsEnded );
        $tpl->setVariable( "notification_sent", $notificationSent );
        $tpl->setVariable( "redirect_uri", (string)$redirectionURI );
        $tpl->setVariable( "password_js_config", $jsConfig );
        $tpl->setVariable( "password_js_config_json", json_encode( $jsConfig, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR ) );

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
