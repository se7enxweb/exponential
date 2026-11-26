<?php
/**
 * File containing the ezpRestOauthTokenController class
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * Controller for OAuth token endpoint.
 *
 * @throws ezpOauthExpiredTokenException|ezpOauthInvalidRequestException|ezpOauthInvalidTokenException
 *
 */
class ezpRestOauthTokenController extends ezcMvcController
{
    /**
     * Handles the POST request which is sent to obtain token data.
     *
     * Supported: the authorization code grant (section 4.1.3), the refresh token grant (section 6) and, for the
     * clients of rest.ini [OAuthSettings] PasswordGrantClients[] only, the password grant (section 4.3).
     *
     * @return ezcMvcResult
     */
    public function doHandleRequest()
    {
        // Check that the correct params are present
        // Params for token endpoint per section 4
        // REQUIRED: grant_type, client_id, and client_secret for a confidential client
        // OPTIONAL: scope
        //
        // Supported grant types are: authorization_code, refresh_token and, for the clients of
        // rest.ini [OAuthSettings] PasswordGrantClients[] only, password
        //
        // Additional params for 'authorization_code', Section 4.1.1
        // REQUIRED: code, redirect_uri
        //
        // Additional param for 'refresh_token", Section 4.1.4
        // REQUIRED: refresh_token
        //
        // Additional params for 'password', Section 4.3.2
        // REQUIRED: username, password (OPTIONAL: otp, the one-time code of a second sign-in step)

        // A public client (one stored without a secret, RFC 6749 section 2.1) sends no client_secret; it is bound to
        // its authorization code by PKCE (code_verifier, RFC 7636 section 4.5) instead. Whether a client must send
        // a secret is decided in ezpOauthUtility, where the client is loaded.
        // OPTIONAL: client_secret, code_verifier

        // Defining the required params for each stage of operation
        $initialRequiredParams = array( 'grant_type', 'client_id' );

        // params for grant_type=authorization_code
        $codeRequiredParams = array( 'code', 'redirect_uri' );

        // params for grant_type=refresh_token
        $refreshRequiredParams = array( 'refresh_token' );

        // params for grant_type=password
        $passwordRequiredParams = array( 'username', 'password' );

        $this->checkParams( $initialRequiredParams );

        // We can get the first set of required params
        $grant_type = $this->request->post['grant_type'];
        $client_id = $this->request->post['client_id'];
        $client_secret = isset( $this->request->post['client_secret'] ) && is_string( $this->request->post['client_secret'] )
                         ? $this->request->post['client_secret'] : null;
        $code_verifier = isset( $this->request->post['code_verifier'] ) && is_string( $this->request->post['code_verifier'] )
                         ? $this->request->post['code_verifier'] : null;
        $tokenTTL = (int)eZINI::instance( 'rest.ini' )->variable( 'OAuthSettings', 'TokenTTL' );

        if ( !$this->validateGrantType( $grant_type ) )
        {
            throw new ezpOauthInvalidRequestException( ezpOauthTokenEndpointErrorType::UNSUPPORTED_GRANT_TYPE );
        }

        $result = new ezcMvcResult();
        $newToken = null;

        switch ( $grant_type )
        {
            case 'authorization_code':
                $this->checkParams( $codeRequiredParams );
                $newToken = ezpOauthUtility::doRefreshTokenWithAuthorizationCode( $client_id, $client_secret, $this->request->post['code'], $this->request->post['redirect_uri'], $code_verifier );
                break;

            case 'refresh_token':
                $this->checkParams( $refreshRequiredParams );
                $newToken = ezpOauthUtility::doRefreshToken( $client_id, $client_secret, $this->request->post['refresh_token'] );
                break;

            case expOAuthPasswordGrant::GRANT_TYPE:
                $this->checkParams( $passwordRequiredParams );
                $newToken = expOAuthPasswordGrant::issue( $client_id, $client_secret, $this->request->post['username'], $this->request->post['password'] );
                break;
        }

        if ( !$newToken instanceof ezpRestToken )
        {
            throw new ezpOauthInvalidTokenException( ezpOauthTokenEndpointErrorType::INVALID_REQUEST );
        }

        $result->variables['access_token'] = $newToken->id;
        $result->variables['refresh_token'] = $newToken->refresh_token;
        $result->variables['expires_in'] = $tokenTTL;
        return $result;
    }

    protected function validateClient( $client_id, $client_secret )
    {

    }

    protected function checkParams( $paramsToCheck )
    {
        // $missingParams used to be reset inside the loop, so only a missing last parameter was
        // refused: a request without client_id went on to fetchByClientId( null ) with
        // "Undefined array key" warnings. Every missing parameter is reported now.
        $missingParams = self::missingParams( $this->request->post, $paramsToCheck );
        if ( !empty( $missingParams ) )
        {
            throw new ezpOauthInvalidRequestException( 'Missing ' . implode( ', ', $missingParams ) . ( count( $missingParams ) > 1 ? ' parameters.' : ' parameter.' ) );
        }
        // A parameter posted as a list (client_id[]=..., refresh_token[]=...) reached the client and token lookups
        // as an array, where the database layer failed with an error page instead of an OAuth answer.
        $invalidParams = self::invalidParams( $this->request->post, $paramsToCheck );
        if ( !empty( $invalidParams ) )
        {
            throw new ezpOauthInvalidRequestException( 'Invalid ' . implode( ', ', $invalidParams ) . ( count( $invalidParams ) > 1 ? ' parameters.' : ' parameter.' ) );
        }
    }

    /**
     * The names in $paramsToCheck whose value in $post is present but not a string (a list posted as name[]=...).
     *
     * @param array $post
     * @param array $paramsToCheck
     * @return array
     */
    public static function invalidParams( $post, $paramsToCheck )
    {
        $invalidParams = array();
        foreach ( $paramsToCheck as $param )
        {
            if ( isset( $post[$param] ) && !is_string( $post[$param] ) )
            {
                $invalidParams[] = $param;
            }
        }
        return $invalidParams;
    }

    /**
     * The names in $paramsToCheck that $post does not carry, in their order.
     *
     * @param array $post
     * @param array $paramsToCheck
     * @return array
     */
    public static function missingParams( $post, $paramsToCheck )
    {
        $missingParams = array();
        foreach( $paramsToCheck as $param )
        {
            if ( !isset( $post[$param] ) )
            {
                $missingParams[] = $param;
            }
        }
        return $missingParams;
    }

    protected function validateGrantType( $grant )
    {
        return in_array( $grant, self::grantTypes( expOAuthPasswordGrant::clients() ), true );
    }

    /**
     * The grant types the endpoint takes: authorization_code and refresh_token, and password while rest.ini
     * [OAuthSettings] PasswordGrantClients[] names a client (expOAuthPasswordGrant). Without such a client the
     * password grant answers unsupported_grant_type, as before it existed.
     *
     * @param string[] $passwordGrantClients
     * @return string[]
     */
    public static function grantTypes( array $passwordGrantClients )
    {
        $grantTypes = array( 'authorization_code', 'refresh_token' );
        if ( !empty( $passwordGrantClients ) )
            $grantTypes[] = expOAuthPasswordGrant::GRANT_TYPE;
        return $grantTypes;
    }
}
