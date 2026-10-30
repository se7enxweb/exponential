<?php
/**
 * The passkeys of an account: user/passkeys (doc/eng-us/guides/passkeys.md).
 *
 * - Not signed in: sent to the sign-in, which comes back here.
 * - user/passkeys, one's own: the passkeys (name, authenticator, made, last used, synced or on this device,
 *   algorithm), "Add a passkey" (the browser's passkey dialog, then a name), Rename and Remove (after a confirmation).
 *   The last passkey cannot be removed while the account could not sign in without it (expPasskeyPolicy). When the
 *   passkey policy sent the user here (required, or preferred once per session) the page says so; after a passkey is
 *   added, or with "Not now" (preferred only), it goes on where the user was going.
 * - user/passkeys/<user id>, another account's: the same list for a viewer with the policy user/passkeys_manage,
 *   with Revoke (a lost or stolen device; access.passkey.revoke). Nothing can be added or renamed there.
 * - Every change is a POST with the form token and is answered with a redirect back here (a reload repeats nothing);
 *   the outcome is shown once from the session.
 * - "Verify it's you" asks before the page opens ([ReauthGroup_account] Rules[]=user/passkeys *).
 * - Templates of [PasskeySettings] ConsoleSlots[] are added below the list (an extension's own section).
 *
 * The page offers nothing while passkeys are switched off ([PasskeySettings] Passkeys) or the table exppasskey is
 * missing (update/common/scripts/6.0/createpasskeytable.php). Changes are recorded in the audit (access.passkey.*).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/** @var array $Params */

$module = $Params['Module'];
$http = eZHTTPTool::instance();
$viewer = eZUser::currentUser();

if ( !$viewer->isRegistered() )
{
    $http->setSessionVariable( 'RedirectAfterLogin', '/user/passkeys' );
    return $module->redirectTo( '/user/login' );
}

$viewerID = (int)$viewer->attribute( 'contentobject_id' );
$userID = isset( $Params['UserID'] ) && is_numeric( $Params['UserID'] ) && (int)$Params['UserID'] > 0 ? (int)$Params['UserID'] : $viewerID;
$own = $userID === $viewerID;
if ( !$own )
{
    $access = $viewer->hasAccessTo( 'user', 'passkeys_manage' );
    if ( !isset( $access['accessWord'] ) || $access['accessWord'] === 'no' )
        return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}
$account = $own ? $viewer : eZUser::fetch( $userID );
if ( !$account instanceof eZUser )
    return $module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );

$service = new expPasskeyService();
$available = $service->available();
$self = $own ? '/user/passkeys' : '/user/passkeys/' . $userID;
$flash = function ( $kind, $text ) use ( $http ) {
    $http->setSessionVariable( 'ExpPasskeyFlash', array( 'kind' => $kind, 'text' => $text ) );
};
$confirm = null;

if ( $available && $_SERVER['REQUEST_METHOD'] === 'POST' )
{
    $post = $_POST;
    $id = isset( $post['PasskeyID'] ) && is_numeric( $post['PasskeyID'] ) ? (int)$post['PasskeyID'] : 0;
    if ( $own && $http->hasPostVariable( 'SkipPasskeyButton' ) )
    {
        // "Not now" of the suggestion (preferred): on where the user was going
        $continue = expPasskeyPolicy::takeContinueURI();
        return $module->redirectTo( $continue !== '' ? $continue : '/' );
    }
    if ( $own && $http->hasPostVariable( 'RegisterPasskeyButton' ) )
    {
        $result = $service->register( $viewer, $post, isset( $post['PasskeyName'] ) ? $post['PasskeyName'] : '' );
        if ( $result['ok'] )
        {
            $flash( 'ok', ezpI18n::tr( 'design/standard/user/passkeys', 'The passkey "%name" is added. You can sign in with it from now on.', null,
                                       array( '%name' => $result['passkey']->attribute( 'name' ) ) ) );
            // the policy sent the user here: on where they were going
            $continue = expPasskeyPolicy::takeContinueURI();
            if ( $continue !== '' )
                return $module->redirectTo( $continue );
        }
        else
            $flash( 'bad', expPasskeyService::errorText( $result['error'] ) );
        return $module->redirectTo( $self );
    }
    if ( $own && $http->hasPostVariable( 'RenamePasskeyButton' ) )
    {
        $passkey = $service->rename( $viewer, $id, isset( $post['PasskeyNewName'] ) ? $post['PasskeyNewName'] : '' );
        if ( $passkey )
            $flash( 'ok', ezpI18n::tr( 'design/standard/user/passkeys', 'The passkey is now called "%name".', null, array( '%name' => $passkey->attribute( 'name' ) ) ) );
        return $module->redirectTo( $self );
    }
    if ( $http->hasPostVariable( 'ConfirmRemovePasskeyButton' ) )
    {
        $passkey = expPasskey::fetch( $id );
        $name = $passkey ? (string)$passkey->attribute( 'name' ) : '';
        if ( $own )
        {
            $why = $service->removeChecked( $viewer, $id );
            if ( $why === null )
                $flash( 'ok', ezpI18n::tr( 'design/standard/user/passkeys', 'The passkey "%name" is removed. It cannot sign in to this account any more.', null, array( '%name' => $name ) ) );
            elseif ( $why === 'last' )
                $flash( 'bad', ezpI18n::tr( 'design/standard/user/passkeys', 'This is your last passkey and your account cannot sign in without one. Add another passkey first.' ) );
        }
        elseif ( $service->revoke( $account, $id ) )
            $flash( 'ok', ezpI18n::tr( 'design/standard/user/passkeys', 'The passkey "%name" is revoked. It cannot sign in to this account any more.', null, array( '%name' => $name ) ) );
        return $module->redirectTo( $self );
    }
    if ( $http->hasPostVariable( 'RemovePasskeyButton' ) )
    {
        $passkey = expPasskey::fetch( $id );
        if ( $passkey && (int)$passkey->attribute( 'user_id' ) === $userID )
            $confirm = $passkey;
    }
}

$message = null;
if ( $http->hasSessionVariable( 'ExpPasskeyFlash' ) )
{
    $message = $http->sessionVariable( 'ExpPasskeyFlash' );
    $http->removeSessionVariable( 'ExpPasskeyFlash' );
}

$passkeys = $available ? expPasskey::fetchByUser( $userID ) : array();
$settings = $service->settings();
$canAdd = $own && $available && count( $passkeys ) < $settings->maxPerUser();
$policy = expPasskeyPolicy::forUser( $account );
$ini = eZINI::instance();
$slots = $ini->hasVariable( 'PasskeySettings', 'ConsoleSlots' ) ? array_values( array_filter( array_map( 'trim', (array)$ini->variable( 'PasskeySettings', 'ConsoleSlots' ) ), 'strlen' ) ) : array();
$slots = array_values( array_filter( $slots, function ( $s ) { return strpos( $s, 'design:' ) === 0 && strpos( $s, '..' ) === false; } ) );
$sentHere = $own && !$passkeys && $policy['policy'] !== expPasskeyPolicy::OPTIONAL && $http->hasSessionVariable( expPasskeyPolicy::SESSION_CONTINUE );

$tpl = eZTemplate::factory();
$tpl->setVariable( 'available', $available );
$tpl->setVariable( 'switched_off', !$settings->enabled() );
$tpl->setVariable( 'passkeys', $passkeys );
$tpl->setVariable( 'can_add', $canAdd );
$tpl->setVariable( 'max_per_user', $settings->maxPerUser() );
$tpl->setVariable( 'options_json', $canAdd ? json_encode( $service->registrationOptions( $viewer ), JSON_UNESCAPED_SLASHES ) : '' );
$tpl->setVariable( 'default_name', expPasskeyService::defaultName() );
$tpl->setVariable( 'message', $message );
$tpl->setVariable( 'confirm_passkey', $confirm );
$tpl->setVariable( 'reauth_seconds', expReauth::secondsLeft( $viewer ) );
// another account's passkeys (administrator view): the account, and Revoke instead of Remove
$tpl->setVariable( 'own', $own );
$tpl->setVariable( 'account', $account );
$tpl->setVariable( 'action_uri', $self );
// the policy: optional, preferred, required; whether the password signs in; whether the policy sent the user here
$tpl->setVariable( 'policy', $policy['policy'] );
$tpl->setVariable( 'password_sign_in', $policy['password_sign_in'] );
$tpl->setVariable( 'sent_by_policy', $sentHere );
$tpl->setVariable( 'last_locked', $own && count( $passkeys ) === 1
                                   && !expPasskeyPolicy::mayRemove( $policy, 1, expPasskeyPolicy::hasPassword( $account ) ) );
$tpl->setVariable( 'console_slots', $slots );

header( 'Cache-Control: no-store, private' );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:user/passkeys.tpl' );
$Result['path'] = $own
    ? array( array( 'url' => 'user/edit', 'text' => ezpI18n::tr( 'design/standard/user/passkeys', 'My account' ) ),
             array( 'url' => false, 'text' => ezpI18n::tr( 'design/standard/user/passkeys', 'Passkeys' ) ) )
    : array( array( 'url' => 'user/edit/' . $userID, 'text' => (string)$account->attribute( 'login' ) ),
             array( 'url' => false, 'text' => ezpI18n::tr( 'design/standard/user/passkeys', 'Passkeys' ) ) );
return $Result;
