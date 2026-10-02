<?php
/**
 * The code of kernel/user/forgotpassword.php, moved into a class (#207 stage 1). The file kernel/user/forgotpassword.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/user/forgotpassword.php:
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

class Forgotpassword extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'generated', false );
        $tpl->setVariable( 'wrong_email', false );
        $tpl->setVariable( 'link', false );
        $tpl->setVariable( 'wrong_key', false );

        $http = \eZHTTPTool::instance();
        $module = $Params['Module'];
        $hashKey = $Params["HashKey"];
        $ini = \eZINI::instance();

        if ( strlen( $hashKey ) == 32 )
        {
            $forgotPasswdObj = \eZForgotPassword::fetchByKey( $hashKey );
            if ( $forgotPasswdObj )
            {
                $userID = $forgotPasswdObj->attribute( 'user_id' );
                $user   = \eZUser::fetch( $userID  );
                $email  = $user->attribute( 'email' );

                $ini = \eZINI::instance();
                $passwordLength = $ini->variable( "UserSettings", "GeneratePasswordLength" );
                $newPassword = \eZUser::createPassword( $passwordLength );

                $userToSendEmail = $user;

                $db = \eZDB::instance();
                $db->begin();

                // Change user password; the audit records it as the reset below, not as a password change of its own
                $changePassword = function () use ( $userID, $newPassword ) {
                    if ( \eZOperationHandler::operationIsAvailable( 'user_password' ) )
                    {
                        \eZOperationHandler::execute( 'user',
                                                      'password', array( 'user_id'    => $userID,
                                                                         'new_password'  => $newPassword ) );
                    }
                    else
                    {
                        \eZUserOperationCollection::password( $userID, $newPassword );
                    }
                };
                if ( class_exists( 'expAuditHook' ) )
                    \expAuditHook::muted( 'access.user.password.change', $changePassword );
                else
                    $changePassword();

                $receiver = $email;
                $mail = new \eZMail();
                if ( !$mail->validate( $receiver ) )
                {
                }

                $tpl = \eZTemplate::factory();
                $tpl->setVariable( 'user', $userToSendEmail );
                $tpl->setVariable( 'object', $userToSendEmail->attribute( 'contentobject' ) );
                $tpl->setVariable( 'password', $newPassword );

                $templateResult = $tpl->fetch( 'design:user/forgotpasswordmail.tpl' );
                $emailSender = $ini->variable( 'MailSettings', 'EmailSender' );
                if ( !$emailSender )
                    $emailSender = $ini->variable( 'MailSettings', 'AdminEmail' );
                $mail->setSender( $emailSender );
                $mail->setReceiver( $receiver );
                $subject = \ezpI18n::tr( 'kernel/user/register', 'Registration info' );
                if ( $tpl->hasVariable( 'subject' ) )
                    $subject = $tpl->variable( 'subject' );
                if ( $tpl->hasVariable( 'content_type' ) )
                    $mail->setContentType( $tpl->variable( 'content_type' ) );
                $mail->setSubject( $subject );
                $mail->setBody( $templateResult );
                $mailResult = \eZMailTransport::send( $mail );
                $tpl->setVariable( 'generated', true );
                $tpl->setVariable( 'email', $email );
                $forgotPasswdObj->remove();
                $db->commit();
                // Audit (doc/bc/6.0/audit.md, access.user.password.reset): never the hash key, never the password
                if ( class_exists( 'expAuditHook' ) )
                    \expAuditHook::emit( 'access.user.password.reset', array( 'object' => \expAuditHook::user( $user ),
                        'after' => array( 'mail_sent' => (bool)$mailResult ) ) );
            }
            else
            {
                $tpl->setVariable( 'wrong_key', true );
                if ( class_exists( 'expAuditHook' ) )
                    \expAuditHook::emit( 'access.user.password.reset.failed', array( 'object' => array( 'type' => 'user' ),
                        'result' => 'refused', 'reason' => 'unknown_key' ) );
            }
        }
        else if ( strlen( $hashKey ) > 4 )
        {
            $tpl->setVariable( 'wrong_key', true );
            if ( class_exists( 'expAuditHook' ) )
                \expAuditHook::emit( 'access.user.password.reset.failed', array( 'object' => array( 'type' => 'user' ),
                    'result' => 'refused', 'reason' => 'unknown_key' ) );
        }

        if ( $module->isCurrentAction( "Generate" ) )
        {
            $ini = \eZINI::instance();
            $passwordLength = $ini->variable( "UserSettings", "GeneratePasswordLength" );
            $password = \eZUser::createPassword( $passwordLength );
            $passwordConfirm = $password;

        //    $http->setSessionVariable( "GeneratedPassword", $password );

            if ( $module->hasActionParameter( "Email" ) )
            {
                $email = $module->actionParameter( "Email" );
                // A form can post the address as an array; treat that as not given
                if ( !is_string( $email ) ) $email = "";
                if ( trim( $email ) != "" )
                {
                    $users = \eZPersistentObject::fetchObjectList( \eZUser::definition(),
                                                               null,
                                                               array( 'email' => $email ),
                                                               null,
                                                               null,
                                                               true );
                }

                $random_bytes = false;
                if ( function_exists( "openssl_random_pseudo_bytes" ) )
                {
                    $is_crypto_strong = false;
                    $random_bytes = openssl_random_pseudo_bytes( 32, $is_crypto_strong );
                    if ( $random_bytes === false )
                    {
                        \eZDebug::writeWarning('openssl_random_pseudo_bytes() cannot generate random data, falling back to insecure mt_rand(). ' .
                            'Please check your PHP installation.' );
                    }
                    else if ( $is_crypto_strong === false )
                    {
                        \eZDebug::writeWarning('openssl_random_pseudo_bytes() could not use a cryptographically strong algorithm. ' .
                            'Please check your PHP installation.' );
                    }
                }
                else
                {
                    \eZDebug::writeWarning('openssl_random_pseudo_bytes() is not available, falling back to insecure mt_rand(). ' .
                        'Please install the openssl PHP extension.' );
                }
                if ( $random_bytes === false )
                {
                    $random_bytes = mt_rand(); // Not secure, but should not happen since SSL is required, and anyway admins have been warned.
                }

                if ( isset($users) && count($users) > 0 )
                {
                    $user   = $users[0];
                    $time   = time();
                    $userID = $user->id();
                    $hashKey = md5($userID . ':' . microtime() . ':' . $random_bytes );

                    // Create forgot password object
                    if ( \eZOperationHandler::operationIsAvailable( 'user_forgotpassword' ) )
                    {
                        $operationResult = \eZOperationHandler::execute( 'user',
                                                                        'forgotpassword', array( 'user_id'    => $userID,
                                                                                                 'password_hash'  => $hashKey,
                                                                                                 'time' => $time ) );
                    }
                    else
                    {
                        \eZUserOperationCollection::forgotpassword( $userID, $hashKey, $time );
                    }

                    $userToSendEmail = $user;
                    $receiver = $email;

                    $mail = new \eZMail();
                    if ( !$mail->validate( $receiver ) )
                    {
                    }

                    $tpl = \eZTemplate::factory();
                    $tpl->setVariable( 'user', $userToSendEmail );
                    $tpl->setVariable( 'object', $userToSendEmail->attribute( 'contentobject' ) );
                    $tpl->setVariable( 'password', $password );
                    $tpl->setVariable( 'link', true );
                    $tpl->setVariable( 'hash_key', $hashKey );
                    $templateResult = $tpl->fetch( 'design:user/forgotpasswordmail.tpl' );
                    if ( $tpl->hasVariable( 'content_type' ) )
                        $mail->setContentType( $tpl->variable( 'content_type' ) );
                    $emailSender = $ini->variable( 'MailSettings', 'EmailSender' );
                    if ( !$emailSender )
                        $emailSender = $ini->variable( 'MailSettings', 'AdminEmail' );
                    $mail->setSender( $emailSender );
                    $mail->setReceiver( $receiver );
                    $subject = \ezpI18n::tr( 'kernel/user/register', 'Registration info' );
                    if ( $tpl->hasVariable( 'subject' ) )
                        $subject = $tpl->variable( 'subject' );
                    $mail->setSubject( $subject );
                    $mail->setBody( $templateResult );
                    $mailResult = \eZMailTransport::send( $mail );
                    $tpl->setVariable( 'email', $email );
                    // Audit (doc/bc/6.0/audit.md, access.user.password.reset.request): never the hash key
                    if ( class_exists( 'expAuditHook' ) )
                        \expAuditHook::emit( 'access.user.password.reset.request', array( 'object' => \expAuditHook::user( $user ),
                            'after' => array( 'mail_sent' => (bool)$mailResult ) ) );

                }
                else if ( trim( $email ) !== '' && \eZMail::validate( trim( $email ) )
                          && !preg_match( '/[<>"\'&\\\\]/', $email ) )
                {
                    // No user has this address. Answer exactly as for one that
                    // does, so the form cannot be used to find out which addresses
                    // have an account. The address is echoed by the page, and older
                    // designs print it unescaped, so only a plain address (no quoted
                    // local part, nothing HTML could read as markup) gets this far.
                    $tpl->setVariable( 'link', true );
                    $tpl->setVariable( 'email', $email );
                    // Audit: the address that has no account, only ever hashed (the privacy rule of email)
                    if ( class_exists( 'expAuditHook' ) )
                        \expAuditHook::emit( 'access.user.password.reset.failed', array( 'object' => array( 'type' => 'user', 'email' => trim( $email ) ),
                            'result' => 'refused', 'reason' => 'unknown_email' ) );
                }
                else
                {
                    // Not an email address at all: saying so reveals nothing.
                    $tpl->setVariable( 'wrong_email', $email );
                    if ( class_exists( 'expAuditHook' ) )
                        \expAuditHook::emit( 'access.user.password.reset.failed', array( 'object' => array( 'type' => 'user' ),
                            'result' => 'refused', 'reason' => 'validation' ) );
                }
            }
        }

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:user/forgotpassword.tpl' );
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/user', 'User' ),
                                        'url' => false ),
                                 array( 'text' => \ezpI18n::tr( 'kernel/user', 'Forgot password' ),
                                        'url' => false ) );

        if ( $ini->variable( 'SiteSettings', 'LoginPage' ) == 'custom' )
        {
            $Result['pagelayout'] = 'loginpagelayout.tpl';
        }

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
