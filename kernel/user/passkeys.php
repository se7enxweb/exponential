<?php
/**
 * The passkeys of the signed-in user's own account: user/passkeys (doc/eng-us/guides/passkeys.md).
 *
 * - Not signed in: sent to the sign-in, which comes back here.
 * - Signed in: the passkeys (name, made, last used, synced or on this device, algorithm), "Add a passkey" (the browser's
 *   passkey dialog, then a name), Rename and Remove (after a confirmation). Every change is a POST with the form token
 *   and is answered with a redirect back here (a reload repeats nothing); the outcome is shown once from the session.
 * - "Verify it's you" asks before the page opens ([ReauthGroup_account] Rules[]=user/passkeys *).
 *
 * The page offers nothing while passkeys are switched off ([PasskeySettings] Passkeys) or the table exppasskey is
 * missing (update/common/scripts/6.0/createpasskeytable.php). Changes are recorded in the audit (access.passkey.*).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/** @var array $Params */

$module = $Params['Module'];
$http = eZHTTPTool::instance();
$user = eZUser::currentUser();

if ( !$user->isRegistered() )
{
    $http->setSessionVariable( 'RedirectAfterLogin', '/user/passkeys' );
    return $module->redirectTo( '/user/login' );
}

$service = new expPasskeyService();
$available = $service->available();
$userID = (int)$user->attribute( 'contentobject_id' );
$self = '/user/passkeys';
$flash = function ( $kind, $text ) use ( $http ) {
    $http->setSessionVariable( 'ExpPasskeyFlash', array( 'kind' => $kind, 'text' => $text ) );
};
$confirm = null;

if ( $available && $_SERVER['REQUEST_METHOD'] === 'POST' )
{
    $post = $_POST;
    $id = isset( $post['PasskeyID'] ) && is_numeric( $post['PasskeyID'] ) ? (int)$post['PasskeyID'] : 0;
    if ( $http->hasPostVariable( 'RegisterPasskeyButton' ) )
    {
        $result = $service->register( $user, $post, isset( $post['PasskeyName'] ) ? $post['PasskeyName'] : '' );
        if ( $result['ok'] )
            $flash( 'ok', ezpI18n::tr( 'design/standard/user/passkeys', 'The passkey "%name" is added. You can sign in with it from now on.', null,
                                       array( '%name' => $result['passkey']->attribute( 'name' ) ) ) );
        else
            $flash( 'bad', expPasskeyService::errorText( $result['error'] ) );
        return $module->redirectTo( $self );
    }
    if ( $http->hasPostVariable( 'RenamePasskeyButton' ) )
    {
        $passkey = $service->rename( $user, $id, isset( $post['PasskeyNewName'] ) ? $post['PasskeyNewName'] : '' );
        if ( $passkey )
            $flash( 'ok', ezpI18n::tr( 'design/standard/user/passkeys', 'The passkey is now called "%name".', null, array( '%name' => $passkey->attribute( 'name' ) ) ) );
        return $module->redirectTo( $self );
    }
    if ( $http->hasPostVariable( 'ConfirmRemovePasskeyButton' ) )
    {
        $passkey = expPasskey::fetch( $id );
        $name = $passkey ? (string)$passkey->attribute( 'name' ) : '';
        if ( $service->remove( $user, $id ) )
            $flash( 'ok', ezpI18n::tr( 'design/standard/user/passkeys', 'The passkey "%name" is removed. It cannot sign in to this account any more.', null, array( '%name' => $name ) ) );
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
$canAdd = $available && count( $passkeys ) < $settings->maxPerUser();

$tpl = eZTemplate::factory();
$tpl->setVariable( 'available', $available );
$tpl->setVariable( 'switched_off', !$settings->enabled() );
$tpl->setVariable( 'passkeys', $passkeys );
$tpl->setVariable( 'can_add', $canAdd );
$tpl->setVariable( 'max_per_user', $settings->maxPerUser() );
$tpl->setVariable( 'options_json', $canAdd ? json_encode( $service->registrationOptions( $user ), JSON_UNESCAPED_SLASHES ) : '' );
$tpl->setVariable( 'default_name', expPasskeyService::defaultName() );
$tpl->setVariable( 'message', $message );
$tpl->setVariable( 'confirm_passkey', $confirm );
$tpl->setVariable( 'reauth_seconds', expReauth::secondsLeft( $user ) );

header( 'Cache-Control: no-store, private' );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:user/passkeys.tpl' );
$Result['path'] = array( array( 'url' => 'user/edit', 'text' => ezpI18n::tr( 'design/standard/user/passkeys', 'My account' ) ),
                         array( 'url' => false, 'text' => ezpI18n::tr( 'design/standard/user/passkeys', 'Passkeys' ) ) );
return $Result;
