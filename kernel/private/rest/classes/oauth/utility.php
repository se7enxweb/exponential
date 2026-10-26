<?php
/**
 * File containing the ezpOuathUtility class
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * Functionality for working against the draft 10 of the oauth2 spec.
 *
 * @package rest
 */
class ezpOauthUtility extends ezpRestModel
{
    const AUTH_HEADER_NAME     = 'Authorization';
    const AUTH_CGI_HEADER_NAME = 'HTTP_AUTHORIZATION';

    /**
     * Retrieving token as per section 5 of draft-ietf-oauth-v2-10
     *
     * Token can be present inside the Authorize header, inside a URI query
     * parameter, or in the HTTP body.
     *
     * According to section 5.1 the header is the preferred way, and the query
     * component and HTTP body are only looked at if no such header can be found.
     *
     * Which of the three are looked at is set in rest.ini [OAuthSettings]
     * (AllowTokenInQuery, AllowTokenInBody), see tokenSources().
     *
     * @param ezcMvcRequest $request
     * @param array|null $sources the places to look at, in order ('header', 'get', 'post'); tokenSources() by default
     * @return string|null a value that is not a string (oauth_token[]=...) is no token
     */
    public static function getToken( ezcMvcRequest $request, $sources = null )
    {
        // 1. Should first extract required token from the request object
        //    as we know that the request parser does not support this at the
        //    moment we, will skip to the fallback right away. That is to say,
        //    ideally the request parser would make this header available to us,
        //    when available, automatically.

        $token = null;
        $checkStack = $sources === null ? self::tokenSources() : $sources;

        foreach ( $checkStack as $step )
        {
            switch ( $step )
            {
                case 'header':
                    $token = self::getTokenFromAuthorizationHeader();
                break;

                case 'get':
                    $token = self::getTokenFromQueryComponent( $request );
                break;

                case 'post':
                    $token = self::getTokenFromHttpBody( $request );
                break;
            }

            // a parameter posted as a list (oauth_token[]=...) is no token
            if ( isset( $token ) && !is_string( $token ) )
            {
                $token = null;
            }

            if ( isset( $token ) ) // Go out of the loop if we find the token during the iteration
            {
                break;
            }
        }

        // A client that still sends ?oauth_token= gets 401 since AllowTokenInQuery is disabled by default; say why in
        // the debug output, so the change is found from the site's side as well.
        if ( !isset( $token ) && !in_array( 'get', $checkStack, true ) && $request instanceof ezpRestRequest
             && isset( $request->get['oauth_token'] ) )
        {
            eZDebug::writeNotice( 'An OAuth token in the query string (oauth_token) was ignored: rest.ini [OAuthSettings] AllowTokenInQuery is not enabled; send it in the Authorization header', __METHOD__ );
        }

        return $token;
    }

    /**
     * The places an access token is taken from, in order: always the Authorization header, the
     * form body (RFC 6750 section 2.2) unless rest.ini [OAuthSettings] AllowTokenInBody=disabled,
     * the query string (RFC 6750 section 2.3) only with AllowTokenInQuery=enabled.
     *
     * The oauth_token query parameter used to be accepted always. A token in the address ends up
     * in web server and proxy access logs, the browser history and Referer headers, which RFC 6750
     * section 2.3 advises against, so it is off by default.
     *
     * @return array
     */
    public static function tokenSources()
    {
        $ini = eZINI::instance( 'rest.ini' );
        $sources = array( 'header' );
        if ( !$ini->hasVariable( 'OAuthSettings', 'AllowTokenInBody' )
             || $ini->variable( 'OAuthSettings', 'AllowTokenInBody' ) !== 'disabled' )
        {
            $sources[] = 'post';
        }
        if ( $ini->hasVariable( 'OAuthSettings', 'AllowTokenInQuery' )
             && $ini->variable( 'OAuthSettings', 'AllowTokenInQuery' ) === 'enabled' )
        {
            // the query before the body, the order used before this setting existed
            array_splice( $sources, 1, 0, array( 'get' ) );
        }
        return $sources;
    }


    /**
     * Extracts the OAuth token from the HTTP header, Authorization.
     *
     * The token is transmitted via the OAuth Authentication scheme ref.
     * Section 5.1.1.
     *
     * PHP does not expose the Authorization header unless it uses the 'Basic'
     * or 'Digest' schemes, and it is therefore extracted from the raw Apache
     * headers.
     *
     * On systems running CGI or Fast-CGI PHP makes this header available via
     * the <var>HTTP_AUTHORIZATION</var> header.
     * @link http://php.net/manual/en/features.http-auth.php
     * @throws ezpOauthInvalidRequestException
     * @return string The access token string.
     */
    protected static function getTokenFromAuthorizationHeader()
    {
        $token = null;
        $authHeader = self::getAuthorizationHeader();

        if ( isset( $authHeader ) )
        {
            // "OAuth <token>" (draft 10) and "Bearer <token>" (RFC 6750). A personal API key (expk_...) is not an
            // OAuth token: expApiKeyRest takes it from the header, so it is never looked up as one here.
            $tokenPattern = "/^(?P<authscheme>OAuth|Bearer)\s(?P<token>[a-zA-Z0-9]+)$/i";
            $match = preg_match( $tokenPattern, $authHeader, $m );
            if ( $match > 0 )
            {
                $token = $m['token'];
            }
        }


        return $token;
    }

    /**
     * The raw value of the request's Authorization header, or null when there is none.
     *
     * PHP does not expose the Authorization header unless it uses the 'Basic' or 'Digest' schemes, so it is read
     * from the raw Apache headers, else from HTTP_AUTHORIZATION (CGI, FastCGI, and servers that emulate
     * apache_request_headers() for the CLI SAPI without carrying it).
     *
     * @return string|null
     */
    public static function getAuthorizationHeader()
    {
        $authHeader = null;
        if ( function_exists( 'apache_request_headers' ) )
        {
            // Header names are case-insensitive; not every server keeps the client's case.
            foreach ( (array)apache_request_headers() as $name => $value )
            {
                if ( strcasecmp( $name, self::AUTH_HEADER_NAME ) === 0 )
                {
                    $authHeader = $value;
                    break;
                }
            }
        }
        // Also when apache_request_headers() exists but does not carry it, as
        // under a server that emulates the function for the CLI SAPI.
        if ( !isset( $authHeader ) && isset( $_SERVER[self::AUTH_CGI_HEADER_NAME] ) )
        {
            $authHeader = $_SERVER[self::AUTH_CGI_HEADER_NAME];
        }

        return is_string( $authHeader ) ? $authHeader : null;
    }

    /**
     * Extracts OAuth token query component aka GET parameter.
     *
     * For more information See section 5.1.2 of  oauth2.0 v10
     *
     * @throws ezpOauthInvalidRequestException
     * @param ezcMvcRequest $request
     * @return string The access token string
     */
    protected static function getTokenFromQueryComponent( ezpRestRequest $request )
    {
        $token = null;
        if( isset( $request->get['oauth_token'] ) )
        {
            //throw new ezpOauthInvalidRequestException( "OAuth token not found in query component." );
            $token = $request->get['oauth_token'];
        }

        return $token;
    }

    /**
     * Extracts OAuth token fro HTTP Post body.
     *
     * For more information see section 5.1.3 oauth2.0 v10
     * @param ezpRestRequest $request
     * @return string The access token string
     */
    protected static function getTokenFromHttpBody( ezpRestRequest $request )
    {
        $token = null;
        if ( isset( $request->post['oauth_token'] ) )
        {
            $token = $request->post['oauth_token'];
        }

        return $token;
    }

    /**
     * Handles a refresh_token request.
     * Returns the new token object as ezpRestToken
     * @param string $clientId Client identifier
     * @param string $clientSecret Client secret key
     * @param string $refreshToken Refresh token
     * @return ezpRestToken
     * @throws ezpOauthInvalidRequestException
     */
    public static function doRefreshToken( $clientId, $clientSecret, $refreshToken )
    {
        $client = ezpRestClient::fetchByClientId( $clientId );
        $tokenTTL = (int)eZINI::instance( 'rest.ini' )->variable( 'OAuthSettings', 'TokenTTL' );

        if (! ( $client instanceof ezpRestClient ) )
            throw new ezpOauthInvalidRequestException( ezpOauthTokenEndpointErrorType::INVALID_CLIENT );

        // a confidential client sends its secret; a public client (no secret) is identified by its client_id
        if ( !expOAuthPkce::clientAuthenticates( $client, $clientSecret ) )
            throw new ezpOauthInvalidRequestException( ezpOauthTokenEndpointErrorType::INVALID_CLIENT );

        $session = ezcPersistentSessionInstance::get();

        $q = $session->createFindQuery( 'ezpRestToken' );
        $q->where( $q->expr->eq( 'refresh_token', $q->bindValue( $refreshToken ) ) );
        $refreshInfo = $session->find( $q, 'ezpRestToken' );

        if ( empty( $refreshInfo) )
        {
            throw new ezpOauthInvalidRequestException( "Specified refresh-token does not exist." );
        }

        $refreshInfo = array_shift( $refreshInfo );

        // a refresh token is redeemed only by the client it was issued to
        if ( !self::clientMatches( $refreshInfo->client_id, $clientId ) )
            throw self::invalidGrant( 'Specified refresh-token does not exist.' );

        // A user disabled in the administration or locked by failed logins used to keep getting fresh
        // access tokens with a refresh token issued before; the API key and password filters refuse
        // such a user.
        $user = eZUser::fetch( $refreshInfo->user_id );
        self::checkGrantUser( $user, $refreshInfo->user_id, 'Specified refresh-token does not exist.' );

        // Refresh tokens used to live for ever: a leaked one gave access for as long as the user and the
        // client authorization existed. The token row carries no issue time; it is derived from the
        // access token's expiry, see refreshTokenExpired().
        $restINI = eZINI::instance( 'rest.ini' );
        $refreshTokenTTL = $restINI->hasVariable( 'OAuthSettings', 'RefreshTokenTTL' )
            ? (int)$restINI->variable( 'OAuthSettings', 'RefreshTokenTTL' )
            : 0;
        if ( self::refreshTokenExpired( $refreshInfo->expirytime, $tokenTTL, $refreshTokenTTL, time() ) )
        {
            $session->delete( $refreshInfo );
            throw self::invalidGrant( 'Specified refresh-token has expired.' );
        }

        // Validate client is still authorized, then validate code is not expired
        $authorized = ezpRestAuthorizedClient::fetchForClientUser( $client, $user );

        if ( !($authorized instanceof ezpRestAuthorizedClient ) )
        {
            throw new ezpOauthInvalidRequestException( ezpOauthTokenEndpointErrorType::INVALID_CLIENT );
        }


        $newToken = new ezpRestToken();
        $newToken->id = ezpRestToken::generateToken( '' );
        $newToken->refresh_token = ezpRestToken::generateToken( '' );
        $newToken->client_id = $clientId;
        $newToken->user_id = $refreshInfo->user_id;
        $newToken->expirytime = time() + $tokenTTL;

        $session->save( $newToken );
        $session->delete( $refreshInfo );

        return $newToken;
    }

    /**
     * Generates a new token against an authorization_code
     * Auth code is checked against clientId, clientSecret and redirectUri as registered for client in admin
     * Auth code is for one-use only and will be removed once the access token generated
     * @param string $clientId Client identifier
     * @param string $clientSecret Client secret key
     * @param string $authCode Authorization code provided by the client
     * @param string $redirectUri Redirect URI. Must be the same as registered in admin
     * @param string|null $codeVerifier PKCE code_verifier (RFC 7636), null when the request has none
     * @return ezpRestToken
     * @throws ezpOauthInvalidRequestException
     * @throws ezpOauthInvalidTokenException
     * @throws ezpOauthExpiredTokenException
     */
    public static function doRefreshTokenWithAuthorizationCode( $clientId, $clientSecret, $authCode, $redirectUri, $codeVerifier = null )
    {
        $client = ezpRestClient::fetchByClientId( $clientId );
        $restINI = eZINI::instance( 'rest.ini' );
        $tokenTTL = (int)$restINI->variable( 'OAuthSettings', 'TokenTTL' );

        if (! ( $client instanceof ezpRestClient ) )
            throw new ezpOauthInvalidRequestException( ezpOauthTokenEndpointErrorType::INVALID_CLIENT );

        // a confidential client sends its secret; a public client (no secret) is bound to its code by PKCE below
        if ( !expOAuthPkce::clientAuthenticates( $client, $clientSecret ) )
            throw new ezpOauthInvalidRequestException( ezpOauthTokenEndpointErrorType::INVALID_CLIENT );

        if ( !$client->isEndPointValid( $redirectUri ) )
            throw new ezpOauthInvalidRequestException( ezpOauthTokenEndpointErrorType::INVALID_REQUEST );

        $session = ezcPersistentSessionInstance::get();

        $q = $session->createFindQuery( 'ezpRestAuthcode' );
        $q->where( $q->expr->eq( 'id', $q->bindValue( $authCode ) ) );
        $codeInfo = $session->find( $q, 'ezpRestAuthcode' );

        if ( empty( $codeInfo ) )
        {
            throw new ezpOauthInvalidTokenException( "Specified authorization code does not exist." );
        }
        $codeInfo = array_shift( $codeInfo );

        // a code is redeemed only by the client it was issued to
        if ( !self::clientMatches( $codeInfo->client_id, $clientId ) )
            throw self::invalidGrant( 'Specified authorization code does not exist.' );

        // PKCE (RFC 7636, section 4.6): the code_verifier must match the challenge stored with the code. A failed
        // attempt uses the code up, so a verifier cannot be guessed over many requests.
        $pkceError = expOAuthPkce::checkRedemption( $client, $codeInfo, $codeVerifier, $restINI );
        if ( $pkceError !== null )
        {
            $session->delete( $codeInfo );
            throw self::invalidGrant( $pkceError );
        }

        // A user disabled or locked after authorizing the client used to get tokens for the code.
        $user = eZUser::fetch( $codeInfo->user_id );
        self::checkGrantUser( $user, $codeInfo->user_id, 'Specified authorization code does not exist.' );

        // Validate client is still authorized, then validate code is not expired
        $authorized = ezpRestAuthorizedClient::fetchForClientUser( $client, $user );

        if ( !($authorized instanceof ezpRestAuthorizedClient ) )
        {
            throw new ezpOauthInvalidRequestException( ezpOauthTokenEndpointErrorType::INVALID_CLIENT );
        }

        // Check expiry of authorization_code
        if ( $codeInfo->expirytime != 0 )
        {
            if ( $codeInfo->expirytime < time() )
            {
                $d = date( "c", $codeInfo->expirytime );
                throw new ezpOauthExpiredTokenException( "Authorization code expired on {$d}" );
            }
        }

        // code ok, create access and refresh tokens
        $accessToken = ezpRestToken::generateToken( '' );
        $refreshToken = ezpRestToken::generateToken( '' );

        $token = new ezpRestToken();
        $token->id = $accessToken;
        $token->refresh_token = $refreshToken;
        $token->client_id = $clientId;
        $token->user_id = $codeInfo->user_id;
        $token->expirytime = time() + $tokenTTL;

        $session = ezcPersistentSessionInstance::get();
        $session->save( $token );

        // After an auth code is used, we'll remove it so it is not abused
        $session->delete( $codeInfo );

        return $token;
    }

    /**
     * A refused grant (RFC 6749, section 5.2): 400 Bad Request, the body names invalid_grant and the reason.
     *
     * @param string $description
     * @return ezpOauthInvalidRequestException
     */
    protected static function invalidGrant( $description )
    {
        return new ezpOauthInvalidRequestException( ezpOauthTokenEndpointErrorType::INVALID_GRANT . ': ' . $description );
    }

    /**
     * Refuses a grant whose user may not use it (userMayUseToken()) with $description, the text the grant gives for a
     * code or refresh token of another client: the answer goes to whoever holds the token, and a reason of its own
     * would tell them that the account exists and is disabled or locked. The reason goes to the debug output.
     *
     * @param eZUser|null $user
     * @param int $userID the user id stored with the grant
     * @param string $description
     * @param callable|null $enabledAfterFailedLogin see userMayUseToken()
     * @throws ezpOauthInvalidRequestException
     */
    public static function checkGrantUser( $user, $userID, $description, $enabledAfterFailedLogin = null )
    {
        if ( self::userMayUseToken( $user, $enabledAfterFailedLogin ) )
            return;
        eZDebug::writeNotice( "OAuth grant refused: user #" . (int)$userID . " is missing, disabled or locked", __METHOD__ );
        throw self::invalidGrant( $description );
    }

    /**
     * Whether $user may still use an OAuth grant or access token: an existing user that is
     * enabled and not locked by failed logins, the rule expRestPasswordAuthFilter and
     * expApiKeyAuthFilter apply.
     *
     * @param eZUser|null $user
     * @param callable|null $enabledAfterFailedLogin lock check by user id, eZUser::isEnabledAfterFailedLogin() by default (tests pass their own)
     * @return bool
     */
    public static function userMayUseToken( $user, $enabledAfterFailedLogin = null )
    {
        if ( !$user instanceof eZUser || !$user->isEnabled() )
            return false;
        if ( $enabledAfterFailedLogin === null )
            $enabledAfterFailedLogin = array( 'eZUser', 'isEnabledAfterFailedLogin' );
        return (bool)call_user_func( $enabledAfterFailedLogin, $user->attribute( 'contentobject_id' ) );
    }

    /**
     * Whether a refresh token has outlived rest.ini [OAuthSettings] RefreshTokenTTL.
     *
     * ezprest_token stores one expiry per row, the access token's: issued at + TokenTTL. The issue
     * time is derived from it with the current TokenTTL, so changing TokenTTL shifts the deadline
     * of tokens issued before by the difference. Every refresh issues a new refresh token, so the
     * lifetime counts from the last refresh (a sliding window), not from the first authorization.
     * A row stored without expiry (0) has no issue time and is refused while a TTL is set.
     *
     * @param int $expirytime the row's expirytime
     * @param int $tokenTTL TokenTTL in seconds
     * @param int $refreshTokenTTL RefreshTokenTTL in seconds, 0 = refresh tokens do not expire
     * @param int $now
     * @return bool
     */
    public static function refreshTokenExpired( $expirytime, $tokenTTL, $refreshTokenTTL, $now )
    {
        $refreshTokenTTL = (int)$refreshTokenTTL;
        if ( $refreshTokenTTL <= 0 )
            return false;
        $expirytime = (int)$expirytime;
        if ( $expirytime <= 0 )
            return true;
        $issued = $expirytime - max( 0, (int)$tokenTTL );
        return $now > $issued + $refreshTokenTTL;
    }

    /**
     * Whether a grant (authorization code, refresh token) issued to $issuedClientId may be
     * redeemed by $requestingClientId (RFC 6749 sections 4.1.3 and 6). Both are the public client
     * identifier strings of ezprest_clients.client_id; they are compared as strings in constant
     * time, and an empty or non-scalar identifier never matches.
     *
     * @param mixed $issuedClientId
     * @param mixed $requestingClientId
     * @return bool
     */
    public static function clientMatches( $issuedClientId, $requestingClientId )
    {
        if ( !is_scalar( $issuedClientId ) || !is_scalar( $requestingClientId ) )
            return false;
        $issued = (string)$issuedClientId;
        return $issued !== '' && hash_equals( $issued, (string)$requestingClientId );
    }
}
?>
