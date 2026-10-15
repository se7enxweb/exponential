<?php
/**
 * The code of kernel/user/success.php, moved into a class (#207 stage 1). The file kernel/user/success.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/user/success.php:
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

class Success extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];
        $Module->setTitle( "Successful registration" );
        // Template handling

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( "module", $Module );
        $ini = \eZINI::instance();

        $verifyUserEmail = $ini->variable( 'UserSettings', 'VerifyUserType' ) === "email";
        $tpl->setVariable( "verify_user_email", $verifyUserEmail );

        // The state of the account user/register made in this session (RegisteredUserID, kept for an hour), else of
        // the signed-in user, else what the settings promise
        $registeredUser = false;
        $state = self::registrationState( \eZHTTPTool::instance(), \eZUser::currentUser(), $verifyUserEmail, $registeredUser );
        $tpl->setVariable( "registration_state", $state );
        $tpl->setVariable( "registered_user", $registeredUser );
        $tpl->setVariable( "current_user_signed_in", \eZUser::currentUser()->isRegistered() );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:user/success.tpl" );
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/user', 'User' ),
                                        'url' => false ),
                                 array( 'text' => \ezpI18n::tr( 'kernel/user', 'Success' ),
                                        'url' => false ) );
        if ( $ini->variable( 'SiteSettings', 'LoginPage' ) == 'custom' )
            $Result['pagelayout'] = 'loginpagelayout.tpl';

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }

    /**
     * The state the page reports, one of:
     *   verify_email      the account waits for the link of the activation mail (it has an account key)
     *   pending_approval  the account waits for an administrator (not published, or disabled without a key)
     *   active            the account is active and nobody is signed in with it here: the user can sign in
     *   signed_in         the user is signed in (the new account, or a signed-in user without a registration here)
     *   error             the session names an account that no longer exists
     *
     * @param \eZHTTPTool $http
     * @param \eZUser $currentUser
     * @param bool $verifyUserEmail site.ini [UserSettings] VerifyUserType is email
     * @param \eZUser|false $registeredUser set to the account of the registration, when there is one
     * @return string
     */
    public static function registrationState( $http, $currentUser, $verifyUserEmail, &$registeredUser )
    {
        $registeredUser = false;
        $registeredID = 0;
        if ( $http->hasSessionVariable( 'RegisteredUserID' ) )
        {
            $value = $http->sessionVariable( 'RegisteredUserID' );
            if ( is_array( $value ) && isset( $value['id'], $value['time'] ) && (int)$value['time'] > time() - 3600 )
                $registeredID = (int)$value['id'];
        }
        $signedIn = $currentUser instanceof \eZUser && $currentUser->isRegistered();

        if ( $registeredID > 0 )
        {
            $user = \eZUser::fetch( $registeredID );
            if ( !$user instanceof \eZUser )
                return 'error';
            $registeredUser = $user;
            if ( $signedIn && (int)$currentUser->attribute( 'contentobject_id' ) === $registeredID )
                return 'signed_in';
            $key = \eZPersistentObject::fetchObject( \eZUserAccountKey::definition(), null, array( 'user_id' => $registeredID ) );
            if ( $key instanceof \eZUserAccountKey )
                return 'verify_email';
            $object = $user->attribute( 'contentobject' );
            $published = $object instanceof \eZContentObject && (int)$object->attribute( 'status' ) === \eZContentObject::STATUS_PUBLISHED;
            if ( !$published || !$user->isEnabled( false ) )
                return 'pending_approval';
            return 'active';
        }
        if ( $signedIn )
            return 'signed_in';
        return $verifyUserEmail ? 'verify_email' : 'active';
    }
}

}
