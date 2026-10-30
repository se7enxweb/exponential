<?php
/**
 * File containing the oauthadmin/keys view definition
 *
 * The personal API keys of every user (doc/eng-us/guides/api-keys.md, "For administrators"): figures, a search over key
 * names, prefixes and owners (/(q)/<text>), a status filter (/(status)/active|expired|revoked), one user's keys
 * (/(user)/<id>), paging, and the selection to revoke (oauthadmin/keyaction asks to confirm).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/** @var array $Params */

$module = $Params['Module'];
$http = eZHTTPTool::instance();

$status = isset( $Params['Status'] ) ? (string)$Params['Status'] : '';
if ( !in_array( $status, array( '', expApiKey::STATUS_ACTIVE, expApiKey::STATUS_EXPIRED, expApiKey::STATUS_REVOKED ), true ) )
    $status = '';
$userID = isset( $Params['UserID'] ) ? (int)$Params['UserID'] : 0;
$search = expViewParameters::text( $Params, 'q', 'Query' );
if ( function_exists( 'mb_substr' ) )
    $search = mb_substr( $search, 0, 100, 'UTF-8' );

// The filter form is posted (q, Status, UserID) and answered with 303 to the view's own parameters,
// /(status)/.../(user)/.../(q)/..., so the pager and the links keep them. A query string is not read.
if ( expViewParameters::redirectsPost( $_POST, array( 'q', 'Status', 'UserID' ) ) )
{
    $wantStatus = isset( $_POST['Status'] ) ? expViewParameters::posted( $_POST, 'Status' ) : $status;
    $wantUser = isset( $_POST['UserID'] ) ? (int)expViewParameters::posted( $_POST, 'UserID' ) : $userID;
    $wantSearch = trim( expViewParameters::posted( $_POST, 'q' ) );
    if ( function_exists( 'mb_substr' ) )
        $wantSearch = mb_substr( $wantSearch, 0, 100, 'UTF-8' );
    expViewParameters::redirect( 'oauthadmin/keys' . expViewParameters::suffix( array(
        'status' => in_array( $wantStatus, array( expApiKey::STATUS_ACTIVE, expApiKey::STATUS_EXPIRED, expApiKey::STATUS_REVOKED ), true ) ? $wantStatus : '',
        'user' => $wantUser > 0 ? $wantUser : '',
        'q' => $wantSearch ) ) );
}

$limit = expAdminPagination::limit( 'oauthadmin/keys', 25 );
$offset = expAdminPagination::offset( $Params );
$filter = array( 'status' => $status, 'user_id' => $userID, 'search' => $search );

$keys = expApiKey::fetchList( $filter, $offset, $limit );
$count = expApiKey::fetchListCount( $filter );

$user = $userID ? eZUser::fetch( $userID ) : null;
$userObject = $userID ? eZContentObject::fetch( $userID ) : null;
$catalogue = expApiKey::scopeCatalogue();

$viewParameters = array( 'offset' => $offset );
if ( $status !== '' )
    $viewParameters['status'] = $status;
if ( $userID )
    $viewParameters['user'] = $userID;

$tpl = eZTemplate::factory();
$tpl->setVariable( 'module', $module );
$tpl->setVariable( 'keys', $keys );
$tpl->setVariable( 'key_count', $count );
$tpl->setVariable( 'key_counts', expApiKey::statusCounts( $userID ) );
$tpl->setVariable( 'status', $status );
$tpl->setVariable( 'search', $search );
$tpl->setVariable( 'filter_user_id', $userID );
$tpl->setVariable( 'filter_user', $user );
$tpl->setVariable( 'filter_user_name', $userObject ? $userObject->attribute( 'name' ) : '' );
$tpl->setVariable( 'scope_catalogue', $catalogue );
$tpl->setVariable( 'keys_enabled', expApiKey::enabled() );
$tpl->setVariable( 'rate_limit', (int)expApiKey::setting( 'RateLimitPerMinute', 0 ) );
$tpl->setVariable( 'rate_limit_available', expApiKey::rateLimitAvailable() );
$tpl->setVariable( 'limit', $limit );
$tpl->setVariable( 'view_parameters', $viewParameters );
$tpl->setVariable( 'page_uri_suffix', $search !== '' ? expViewParameters::suffix( array( 'q' => $search ) ) : false );
$tpl->setVariable( 'redirect_uri', '/' . $module->currentRedirectionURI() );
// how many keys the confirmation before this page revoked (once)
$revoked = 0;
if ( $http->hasSessionVariable( 'OAuthAdminKeyMessage' ) )
{
    $revoked = (int)$http->sessionVariable( 'OAuthAdminKeyMessage' );
    $http->removeSessionVariable( 'OAuthAdminKeyMessage' );
}
$tpl->setVariable( 'revoked_count', $revoked );

$path = array( array( 'url' => 'oauthadmin/list', 'text' => ezpI18n::tr( 'kernel/oauthadmin', 'oAuth admin' ) ),
               array( 'url' => $userID ? 'oauthadmin/keys' : false, 'text' => ezpI18n::tr( 'kernel/oauthadmin', 'API keys' ) ) );
if ( $userObject )
    $path[] = array( 'url' => false, 'text' => $userObject->attribute( 'name' ) );

$Result = array();
$Result['path'] = $path;
$Result['content'] = $tpl->fetch( 'design:oauthadmin/keys.tpl' );
return $Result;
?>
