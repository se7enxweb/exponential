<?php
/**
 * The apikey module: personal API keys (publishing keys) of the signed in user (doc/guides/api-keys.md).
 *
 * apikey/list is the "API access" page of the user's account: the user's keys, a form to make one (name, scopes,
 * lifetime; the key is shown once) and revoking. It is in site.ini [RoleSettings] PolicyOmitList[] because it checks
 * for itself: anonymous visitors get the sign in and register links, every signed in user sees and can revoke
 * their own keys, and only a user with apikey/create may make new ones.
 *
 * apikey/create: may make keys. Its Scope limitation narrows which scopes a key may be given; each scope also
 * needs the policy it stands for (rest.ini [ApiKeySettings] Scopes[]), so a key never exceeds its owner.
 * No role has it in a new installation; give it to the roles that may publish through the API.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

$Module = array( 'name' => 'API keys',
                 'variable_params' => true );

$ViewList = array();

$ViewList['list'] = array(
    'script' => 'list.php',
    'default_navigation_part' => 'ezmynavigationpart',
    'single_post_actions' => array( 'CreateKeyButton' => 'CreateKey',
                                    'RevokeKeyButton' => 'RevokeKey',
                                    'ConfirmRevokeKeyButton' => 'ConfirmRevokeKey' ),
    'post_action_parameters' => array( 'CreateKey' => array( 'Name' => 'ApiKeyName',
                                                             'Scopes' => 'ApiKeyScopes',
                                                             'Expiry' => 'ApiKeyExpiry',
                                                             'Nonce' => 'ApiKeyNonce' ),
                                       'RevokeKey' => array( 'KeyID' => 'RevokeKeyButton' ),
                                       'ConfirmRevokeKey' => array( 'KeyID' => 'RevokeKeyID' ) ),
    'params' => array() );

// The scopes a key may be given, from rest.ini, for the Scope limitation of apikey/create.
$scopeValues = array();
if ( class_exists( 'expApiKey' ) )
{
    foreach ( expApiKey::scopeCatalogue() as $scopeID => $scope )
        $scopeValues[] = array( 'Name' => $scope['name'], 'value' => $scopeID );
}

$FunctionList = array();
$FunctionList['create'] = array(
    'Scope' => array(
        'name' => 'Scope',
        'values' => $scopeValues,
    ),
);

?>
