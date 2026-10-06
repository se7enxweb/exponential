<?php
/**
 * The "API access" page of the signed in user: apikey/list (doc/guides/api-keys.md, "For site users").
 *
 * - Anonymous: what the page is for, a sign in form that comes back here, and the link to register.
 * - Signed in: the user's keys (name, prefix, scopes, created, last used, last address, expiry, status), revoke
 *   with a confirmation, and, with apikey/create, the form to make a key.
 * - A new key's full value is in the answer to the POST that made it and nowhere else: it is not stored, not put in
 *   the session and not shown again. The answer is sent with Cache-Control: no-store. A nonce kept in the session
 *   makes a reload of that POST say so instead of making a second key.
 *
 * Every POST carries the form token (ezformtoken). Making and revoking are recorded in the audit
 * (access.apikey.create, access.apikey.revoke).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

$module = $Params['Module'];
$http = eZHTTPTool::instance();
$user = eZUser::currentUser();
$tpl = eZTemplate::factory();

$Result = array();
$Result['path'] = array( array( 'url' => 'user/edit', 'text' => ezpI18n::tr( 'design/standard/apikey', 'My account' ) ),
                         array( 'url' => false, 'text' => ezpI18n::tr( 'design/standard/apikey', 'API access' ) ) );

$tpl->setVariable( 'module', $module );
$tpl->setVariable( 'keys_enabled', expApiKey::enabled() );

if ( !$user->isRegistered() )
{
    $tpl->setVariable( 'signed_in', false );
    $tpl->setVariable( 'can_register', true );
    $Result['content'] = $tpl->fetch( 'design:apikey/list.tpl' );
    return $Result;
}

$userID = (int)$user->attribute( 'contentobject_id' );
$catalogue = expApiKey::scopeCatalogue();
$canCreate = expApiKey::userCanCreate( $user );
$allowedScopes = $canCreate ? expApiKey::scopesForUser( $user, $catalogue ) : array();
$maxKeys = (int)expApiKey::setting( 'MaxKeysPerUser', 10 );
$maxDays = (int)expApiKey::setting( 'MaxExpiryDays', 365 );
$choices = expApiKey::expiryChoices();
$defaultDays = (int)expApiKey::setting( 'DefaultExpiryDays', 90 );
if ( !in_array( $defaultDays, $choices, true ) )
    $defaultDays = $choices ? $choices[0] : 0;

$errors = array();
$newKey = null;
$newToken = null;
$message = null;
$confirmKey = null;
$form = array( 'name' => '', 'scopes' => $allowedScopes ? array( $allowedScopes[0] ) : array(), 'expiry' => $defaultDays );

if ( $module->isCurrentAction( 'CreateKey' ) && expApiKey::enabled() )
{
    $form['name'] = expApiKey::cleanName( $module->actionParameter( 'Name' ) );
    $form['scopes'] = expApiKey::normaliseScopes( (array)$module->actionParameter( 'Scopes' ), $catalogue );
    $form['expiry'] = $module->actionParameter( 'Expiry' );
    $nonce = (string)$module->actionParameter( 'Nonce' );
    $expected = $http->hasSessionVariable( 'ExpApiKeyNonce' ) ? (string)$http->sessionVariable( 'ExpApiKeyNonce' ) : '';

    if ( !$canCreate )
        $errors[] = ezpI18n::tr( 'design/standard/apikey', 'You may not make API keys. Ask an administrator if you need one.' );
    elseif ( $expected === '' || !hash_equals( $expected, $nonce ) )
        $message = ezpI18n::tr( 'design/standard/apikey', 'This form was already sent. If a key was made, it is in the list below; its value cannot be shown again.' );
    else
    {
        if ( $form['name'] === '' )
            $errors[] = ezpI18n::tr( 'design/standard/apikey', 'Give the key a name, so you know later where it is used.' );
        $scopes = expApiKey::limitScopes( $form['scopes'], $allowedScopes );
        if ( !$scopes )
            $errors[] = ezpI18n::tr( 'design/standard/apikey', 'Choose at least one scope.' );
        elseif ( count( $scopes ) !== count( $form['scopes'] ) )
            $errors[] = ezpI18n::tr( 'design/standard/apikey', 'One of the chosen scopes is not available to you.' );
        $expires = in_array( (int)$form['expiry'], $choices, true ) ? expApiKey::expiryFor( $form['expiry'], time(), $maxDays ) : false;
        if ( $expires === false )
            $errors[] = ezpI18n::tr( 'design/standard/apikey', 'Choose one of the offered lifetimes.' );
        if ( $maxKeys > 0 && expApiKey::activeCountForUser( $userID ) >= $maxKeys )
            $errors[] = ezpI18n::tr( 'design/standard/apikey', 'You already have %count active keys, the most allowed. Revoke one you no longer use first.', null, array( '%count' => $maxKeys ) );

        if ( !$errors )
        {
            $http->removeSessionVariable( 'ExpApiKeyNonce' );
            $made = expApiKey::create( $userID, $form['name'], $scopes, $expires, $userID );
            $newKey = $made['key'];
            $newToken = $made['token'];
            if ( class_exists( 'expAudit' ) )
                expAudit::event( 'access.apikey.create', array( 'object' => $newKey->auditObject(),
                                                                 'after' => array( 'scopes' => $scopes,
                                                                                   'expires' => (int)$expires ) ) );
            $form = array( 'name' => '', 'scopes' => $allowedScopes ? array( $allowedScopes[0] ) : array(), 'expiry' => $defaultDays );
        }
    }
}
elseif ( $module->isCurrentAction( 'RevokeKey' ) )
{
    $key = expApiKey::fetch( (int)$module->actionParameter( 'KeyID' ) );
    if ( $key instanceof expApiKey && (int)$key->attribute( 'user_id' ) === $userID && !$key->attribute( 'revoked' ) )
        $confirmKey = $key;
}
elseif ( $module->isCurrentAction( 'ConfirmRevokeKey' ) )
{
    $key = expApiKey::fetch( (int)$module->actionParameter( 'KeyID' ) );
    // only the user's own key; someone else's id is treated like an unknown one
    if ( $key instanceof expApiKey && (int)$key->attribute( 'user_id' ) === $userID )
    {
        if ( $key->revoke( $userID ) )
        {
            if ( class_exists( 'expAudit' ) )
                expAudit::event( 'access.apikey.revoke', array( 'object' => $key->auditObject(),
                                                                 'before' => array( 'status' => 'active' ),
                                                                 'after' => array( 'status' => 'revoked', 'by' => 'owner' ) ) );
            $message = ezpI18n::tr( 'design/standard/apikey', 'The key "%name" is revoked. Requests with it are refused from now on.', null,
                                    array( '%name' => $key->attribute( 'name' ) ) );
        }
    }
}

// a fresh nonce for the form on this page
$nonce = bin2hex( random_bytes( 16 ) );
$http->setSessionVariable( 'ExpApiKeyNonce', $nonce );

if ( $newToken !== null )
{
    // the only answer that holds the key: never kept by a browser cache, a proxy or the history
    header( 'Cache-Control: no-store, private' );
    header( 'Pragma: no-cache' );
}

$apiBase = rtrim( eZSys::serverURL(), '/' ) . eZINI::instance( 'rest.ini' )->variable( 'System', 'ApiPrefix' );
$tpl->setVariable( 'example_url', $apiBase . expApiKey::examplePath() );

$tpl->setVariable( 'signed_in', true );
// the keys that still work first, then the expired ones, then the revoked ones; newest first within each
$rank = array( expApiKey::STATUS_ACTIVE => 0, expApiKey::STATUS_EXPIRED => 1, expApiKey::STATUS_REVOKED => 2 );
$keys = expApiKey::fetchListForUser( $userID );
usort( $keys, function ( $a, $b ) use ( $rank ) {
    $r = $rank[$a->statusAt()] - $rank[$b->statusAt()];
    if ( $r !== 0 )
        return $r;
    $r = (int)$b->attribute( 'created' ) - (int)$a->attribute( 'created' );
    return $r !== 0 ? $r : (int)$b->attribute( 'id' ) - (int)$a->attribute( 'id' );
} );
$tpl->setVariable( 'keys', $keys );
$tpl->setVariable( 'scope_catalogue', $catalogue );
$tpl->setVariable( 'allowed_scopes', $allowedScopes );
$tpl->setVariable( 'can_create', $canCreate );
$tpl->setVariable( 'expiry_choices', $choices );
$tpl->setVariable( 'max_keys', $maxKeys );
$tpl->setVariable( 'active_count', expApiKey::activeCountForUser( $userID ) );
$tpl->setVariable( 'form', $form );
$tpl->setVariable( 'errors', $errors );
$tpl->setVariable( 'message', $message );
$tpl->setVariable( 'new_key', $newKey );
$tpl->setVariable( 'new_token', $newToken );
$tpl->setVariable( 'confirm_key', $confirmKey );
$tpl->setVariable( 'nonce', $nonce );
$tpl->setVariable( 'api_base', $apiBase );
$tpl->setVariable( 'user', $user );

$Result['content'] = $tpl->fetch( 'design:apikey/list.tpl' );
return $Result;
?>
