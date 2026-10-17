<?php
/**
 * File containing the oauth/authorize view definition
 *
 * @param string $_GET[client_id] the client application identifier, as in ezpRestClient
 * @param string $_GET[redirect_uri] the URI the view should redirect to in case of success
 * @param string $_GET[response_type] the requested response type. Can be code_and_token, code, or token
 * @param string $_GET[scope] the permissions scope the client requests (optional)
 * @param string $_GET[state] Not implemented yet (optional)
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package oauth
 */

if ( !function_exists( 'expModuleViewOauthAuthorizeError' ) ) {
/**
 * oAuth error handler function. Terminates execution after redirecting.
 *
 * @param string $redirectUri The URI the error should be sent to
 * @param string $errorCode The error code, as defined in section 3.2.1
 * @param string $message A human readable error message explaining the error
 *
 * @return void
 */
function expModuleViewOauthAuthorizeError( $redirectUri, $errorCode, $message = null )
{
    $location = "{$redirectUri}?error=" . urlencode( $errorCode );
    if( $message !== null )
        $location .= '&error_description=' . urlencode( $message );
    expModuleViewOauthAuthorizeResponse( '302 Found', $location );
}
}

if ( !function_exists( 'expModuleViewOauthAuthorizeResponse' ) ) {
/**
 * oAuth2 response handler function. Terminates execution after sending the headers.
 *
 * @param string $httpHeader The HTTP header to be sent as a response
 * @param string $location The location to redirect to. No redirection is done if not provided.
 *
 * @return void
 */
function expModuleViewOauthAuthorizeResponse( $httpHeader, $location = null )
{
    header( "HTTP/1.1 $httpHeader" );
    if ( $location !== null )
        // debug stuff: echo "header( \"Location: $location\" );\n";
        header( "Location: $location" );
    eZExecution::cleanExit();
}
}

if ( !function_exists( 'expModuleViewOauthAuthorizeGetHTTPVariable' ) ) {
/**
 * Helper function that reads an HTTP variable over GET or POST, depending on the stage.
 * The POST variables AuthorizeButton and DenyButton will make the function read from POST
 *
 * @param string $variable
 * @return mixed The parameter, or false if not set
 */
function expModuleViewOauthAuthorizeGetHTTPVariable( $variable )
{
    // Computed on every call, not kept in function statics: this function is
    // declared once per persistent worker, so a static $hasPost would be fixed
    // by the first request and later Authorize/Deny POSTs would be read as GET.
    $http = eZHTTPTool::instance();
    $hasPost = $http->hasPostVariable( 'AuthorizeButton' ) || $http->hasPostVariable( 'DenyButton' );

    if ( $hasPost )
        return $http->hasPostVariable( $variable ) ? $http->postVariable( $variable ) : false;
    else
        return $http->hasGetVariable( $variable ) ? $http->getVariable( $variable ) : false;
}
}

$module = $Params['Module'];

// First check for mandatory parameters
$http = eZHTTPTool::instance();
$restINI = eZINI::instance( 'rest.ini' );

// Redirect URI, client ID and the client's end point are checked first, without redirecting: until the redirect_uri
// is known to be the end point of a registered client it is the requester's address, and an error sent there would
// make this view an open redirector. RFC 6749, section 4.1.2.1: inform the resource owner, do not redirect.
if ( !is_string( $pRedirectUri = expModuleViewOauthAuthorizeGetHTTPVariable( 'redirect_uri' ) ) )
{
    expModuleViewOauthAuthorizeResponse( '400 Bad Request' );
}

// Client ID
if ( !is_string( $pClientId = expModuleViewOauthAuthorizeGetHTTPVariable( 'client_id' ) ) )
{
    expModuleViewOauthAuthorizeResponse( '400 Bad Request' );
}

// Try loading the REST client based on the ID
$application = ezpRestClient::fetchByClientId( $pClientId );
if ( !$application instanceof ezpRestClient )
{
    expModuleViewOauthAuthorizeResponse( '400 Bad Request' );
}

// The client is found, validate the redirect_uri; an empty one is no end point, even for a client stored without one
// (the code or token would go to the relative address "?access_token=...")
if ( $pRedirectUri === '' || !$application->isEndPointValid( $pRedirectUri ) )
{
    expModuleViewOauthAuthorizeResponse( '400 Bad Request' );
}

// From here on errors are sent to the client's end point

// Requested response type
if ( ( $pResponseType = expModuleViewOauthAuthorizeGetHTTPVariable( 'response_type' ) ) === false )
{
    expModuleViewOauthAuthorizeError( $pRedirectUri, 'invalid_request', "Missing response_type parameter" );
    // redirect to the redirect_uri with GET parameter error=invalid_request
    // @todo mismatch between chapters 3.2's example (error=access-denied) and error codes list at 3.2.1 (error=access_denied)
}

// Scope
$pScope = expModuleViewOauthAuthorizeGetHTTPVariable( 'scope' );

// @todo The spec mentions (FIXME:chapter) throwing an error if the request has extra parameters. Check this.

// authentication
$user = eZUser::currentUser();

// login is like REALLY required here. But we can't use the standard policy check, as it won't redirect w/ GET parameters
if ( !$user->isRegistered() )
{
    $redirectUri = str_replace( eZSys::indexDir(), '', $_SERVER['REQUEST_URI'] );

    $tpl = eZTemplate::factory();

    $tpl->setVariable( 'redirect_uri', $redirectUri, 'User' );
    $tpl->setVariable( 'site_access', array( 'allowed' => true ) );

    $result = array();
    $result['content'] = $tpl->fetch( 'design:user/login.tpl' );
    $result['pagelayout'] = false;
    $result['path'] = array( array( 'text' => ezpI18n::tr( 'kernel/user', 'User' ),
                                    'url' => false ),
                             array( 'text' => ezpI18n::tr( 'kernel/user', 'Login' ),
                                    'url' => false ) );
    $result['pagelayout'] = 'oauthloginpagelayout.tpl';

    return $result;
}
// Signed in, but without the policy oauth/authorize (for this siteaccess): the user may not authorize clients. The
// redirect_uri is a validated end point of the client here, so the client is told with access_denied (RFC 6749,
// section 4.1.2.1). Before 6.0.15 a negation bound before the comparison made this check always pass.
if ( !expOAuthAuthorizePolicy::userMayAuthorize( $user, expOAuthAuthorizePolicy::currentSiteAccessName(), $restINI ) )
{
    expModuleViewOauthAuthorizeError( $pRedirectUri, 'access_denied', 'The user may not authorize clients' );
}

// Everything is valid. Process to app authorization.
if ( !$application->isAuthorizedByUser( $pScope, eZUser::currentUser() ) )
{
    $http = eZHTTPTool::instance();

    // authorization denied: redirect
    if ( $http->hasPostVariable( 'DenyButton' ) )
    {
        expModuleViewOauthAuthorizeError( $pRedirectUri, 'access_denied' );
    }

    elseif ( $http->hasPostVariable( 'AuthorizeButton' ) )
    {
        $application->authorizeFor( $user );
    }

    // show authorization form
    if ( !$application->isAuthorizedByUser( $pScope, eZUser::currentUser() ) )
    {
        $tpl = eZTemplate::factory();

        $tpl->setVariable( 'application', $application );
        $tpl->setVariable( 'module', $module );

        $parameters = array( 'client_id' => $pClientId,
                             'response_type' => $pResponseType,
                             'scope' => $pScope,
                             'redirect_uri' => $pRedirectUri );
        $tpl->setVariable( 'httpParameters', $parameters );

        $result = array();
        $result['content'] = $tpl->fetch( 'design:oauth/authorize.tpl' );
        $result['pagelayout'] = 'oauthloginpagelayout.tpl';
        $result['path'] = array( array( 'text' => ezpI18n::tr( 'kernel/user', 'oAuth' ),
                                        'url' => false ),
                                 array( 'text' => ezpI18n::tr( 'kernel/user', 'authorization' ),
                                        'url' => false ) );

        return $result;
    }
}

$tokenTTL = (int)$restINI->variable( 'OAuthSettings', 'TokenTTL' );
if ( $pResponseType == 'token')
{
    // At this point, the we know the user HAS granted access, and can hand over a token
    $rAccessToken = ezpRestToken::generateToken( $pScope );
    $rRefreshToken = ezpRestToken::generateToken( $pScope );

    $token = new ezpRestToken();
    $token->id = $rAccessToken;
    $token->refresh_token = $rRefreshToken;
    $token->client_id = $pClientId;
    $token->user_id = $user->attribute( 'contentobject_id' );
    $token->expirytime = time() + $tokenTTL;

    $session = ezcPersistentSessionInstance::get();
    $session->save( $token );


    // The user has a agreed to authorize this app.
    // Redirect to the redirect_uri with these parameters:
    // - code, only if request_type == code OR code_and_token @todo Implement
    // - access_token, only if request_type == token OR code_and_token
    // - expires_in, the token lifetime in seconds
    // - scope, the permission scope the provided code / token grants, if different from the requested one (not implemented yet)
    // - state, not implemented yet (state persistency related)
    $parameters = array();

    $rExpiresIn = $tokenTTL;

    $parameters[] = 'access_token=' . urlencode( $rAccessToken );
    $parameters[] = 'refresh_token=' . urlencode( $rRefreshToken );
    $parameters[] = "expires_in=$rExpiresIn";
    $location = "{$pRedirectUri}?" . implode( '&', $parameters );

    expModuleViewOauthAuthorizeResponse( '302 Found', $location );
}
elseif ( $pResponseType ==  'code')
{
    // At this point, the we know the user HAS granted access, and can hand over a token
    $rCode = ezpRestToken::generateToken( $pScope );

    $code = new ezpRestAuthcode();
    $code->id = $rCode;
    $code->client_id = $pClientId;
    $code->user_id = $user->attribute( 'contentobject_id' );
    $code->expirytime = time() + $tokenTTL;

    $session = ezcPersistentSessionInstance::get();
    $session->save( $code );


    // The user has a agreed to authorize this app.
    // Redirect to the redirect_uri with these parameters:
    // - code, only if request_type == code OR code_and_token @todo Implement
    // - access_token, only if request_type == token OR code_and_token
    // - expires_in, the token lifetime in seconds
    // - scope, the permission scope the provided code / token grants, if different from the requested one (not implemented yet)
    // - state, not implemented yet (state persistency related)
    $parameters = array();

    $rExpiresIn = $tokenTTL;

    $parameters[] = 'code=' . urlencode( $rCode );
    $parameters[] = "expires_in=$rExpiresIn";
    $location = "{$pRedirectUri}?" . implode( '&', $parameters );

    expModuleViewOauthAuthorizeResponse( '302 Found', $location );
}



?>
