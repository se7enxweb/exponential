<?php
/**
 * File containing the oauthadmin/keyaction view definition
 *
 * Revokes personal API keys for an administrator: the keys selected on oauthadmin/keys (RevokeKeyIDArray[]), a
 * page that names them and asks to confirm, then the revocation (ConfirmRevoke). Each revoked key is recorded as
 * access.apikey.revoke with the administrator as the actor. Afterwards the page goes back to the list it came from
 * (RedirectURI, a path of this site only).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/** @var array $Params */

$module = $Params['Module'];

$redirect = (string)$module->actionParameter( 'RedirectURI' );
// only a path of this site: never an absolute or protocol-relative address
if ( $redirect === '' || $redirect[0] !== '/' || strpos( $redirect, '//' ) === 0 || preg_match( '#[\r\n\\\\]#', $redirect ) )
    $redirect = '/oauthadmin/keys';

if ( !$module->isCurrentAction( 'RevokeKeyList' ) && !$module->isCurrentAction( 'RevokeOneKey' ) )
    return $module->redirectTo( $redirect );

// the Revoke button of one row carries its key's id; Revoke selected the checked rows
$keyIDs = $module->isCurrentAction( 'RevokeOneKey' )
        ? array( $module->actionParameter( 'KeyID' ) )
        : (array)$module->actionParameter( 'KeyIDList' );
$keys = array();
foreach ( $keyIDs as $keyID )
{
    $key = expApiKey::fetch( (int)$keyID );
    if ( $key instanceof expApiKey && !$key->attribute( 'revoked' ) )
        $keys[$key->attribute( 'id' )] = $key;
}
if ( !$keys )
    return $module->redirectTo( $redirect );

if ( $module->hasActionParameter( 'ConfirmRevoke' ) && $module->actionParameter( 'ConfirmRevoke' ) )
{
    $adminID = eZUser::currentUserID();
    foreach ( $keys as $key )
    {
        if ( $key->revoke( $adminID ) && class_exists( 'expAudit' ) )
            expAudit::event( 'access.apikey.revoke', array( 'object' => $key->auditObject(),
                                                             'target' => expAuditHook::user( (int)$key->attribute( 'user_id' ) ),
                                                             'before' => array( 'status' => 'active' ),
                                                             'after' => array( 'status' => 'revoked', 'by' => 'administrator' ) ) );
    }
    eZHTTPTool::instance()->setSessionVariable( 'OAuthAdminKeyMessage', count( $keys ) );
    return $module->redirectTo( $redirect );
}

$tpl = eZTemplate::factory();
$tpl->setVariable( 'module', $module );
$tpl->setVariable( 'keys', array_values( $keys ) );
$tpl->setVariable( 'redirect_uri', $redirect );
$tpl->setVariable( 'scope_catalogue', expApiKey::scopeCatalogue() );

$Result = array();
$Result['path'] = array( array( 'url' => 'oauthadmin/list', 'text' => ezpI18n::tr( 'kernel/oauthadmin', 'oAuth admin' ) ),
                         array( 'url' => 'oauthadmin/keys', 'text' => ezpI18n::tr( 'kernel/oauthadmin', 'API keys' ) ),
                         array( 'url' => false, 'text' => ezpI18n::tr( 'kernel/oauthadmin', 'Confirm revocation' ) ) );
$Result['content'] = $tpl->fetch( 'design:oauthadmin/key_revoke_confirmation.tpl' );
return $Result;
?>
