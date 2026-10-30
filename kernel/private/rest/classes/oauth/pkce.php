<?php
/**
 * File containing the expOAuthPkce class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package oauth
 */

/**
 * Proof Key for Code Exchange (PKCE, RFC 7636) for the OAuth authorization code flow.
 *
 * oauth/authorize takes code_challenge and code_challenge_method, checks them with checkChallenge() and stores them
 * with the authorization code (ezprest_authcode.code_challenge, .code_challenge_method). The token end point checks
 * the code_verifier of the token request against them with verify() before it issues a token.
 *
 * Settings, rest.ini [OAuthSettings]:
 * - RequirePkceForPublicClients (enabled by default): a client without a client secret must use PKCE.
 * - RequirePkceForConfidentialClients (disabled by default): every client must use PKCE.
 * - AllowPlainPkce (disabled by default): accept the method "plain"; only S256 is accepted otherwise.
 *
 * No database and no kernel: tested in tests/tests/kernel/private/oauth/expOAuthPkceTest.php.
 */
class expOAuthPkce
{
    const METHOD_S256 = 'S256';
    const METHOD_PLAIN = 'plain';

    /** RFC 7636, section 4.1: 43 to 128 characters of [A-Z] / [a-z] / [0-9] / "-" / "." / "_" / "~" */
    const VERIFIER_PATTERN = '/^[A-Za-z0-9\-._~]{43,128}\z/';

    /** An S256 challenge is the base64url encoding (no padding) of 32 bytes: 43 characters */
    const S256_CHALLENGE_PATTERN = '/^[A-Za-z0-9\-_]{43}\z/';

    /**
     * Checks the PKCE parameters of an authorization request.
     *
     * @param mixed $challenge code_challenge, false or null when the request has none
     * @param mixed $method code_challenge_method, false or null when the request has none
     * @param bool $allowPlain AllowPlainPkce
     * @return string|null null when the parameters are acceptable (or absent), otherwise the error_description for
     *                     error=invalid_request
     */
    public static function checkChallenge( $challenge, $method, $allowPlain )
    {
        $hasChallenge = $challenge !== false && $challenge !== null;
        $hasMethod = $method !== false && $method !== null;

        if ( !$hasChallenge )
            return $hasMethod ? 'code_challenge_method without code_challenge' : null;

        if ( !is_string( $challenge ) || ( $hasMethod && !is_string( $method ) ) )
            return 'Invalid code_challenge';

        // RFC 7636, section 4.3: a missing method means "plain"
        $method = $hasMethod ? $method : self::METHOD_PLAIN;

        if ( $method === self::METHOD_S256 )
            return preg_match( self::S256_CHALLENGE_PATTERN, $challenge ) ? null : 'Invalid code_challenge';

        if ( $method === self::METHOD_PLAIN )
        {
            if ( !$allowPlain )
                return 'code_challenge_method plain is not allowed, use S256';
            return preg_match( self::VERIFIER_PATTERN, $challenge ) ? null : 'Invalid code_challenge';
        }

        return 'Unsupported code_challenge_method, use S256';
    }

    /**
     * The method stored with a code for a challenge that checkChallenge() accepted.
     *
     * @param mixed $method code_challenge_method, false or null when the request has none
     * @return string
     */
    public static function normalizeMethod( $method )
    {
        return ( $method === false || $method === null ) ? self::METHOD_PLAIN : (string)$method;
    }

    /**
     * Verifies a code_verifier against the stored challenge (constant time).
     *
     * @param mixed $verifier code_verifier of the token request
     * @param string $challenge the stored code_challenge
     * @param string $method the stored code_challenge_method
     * @param bool $allowPlain AllowPlainPkce, checked again: a code stored while plain was allowed is not redeemed
     *                         with plain after it has been switched off
     * @return bool
     */
    public static function verify( $verifier, $challenge, $method, $allowPlain )
    {
        if ( !is_string( $verifier ) || !is_string( $challenge ) || $challenge === ''
             || !preg_match( self::VERIFIER_PATTERN, $verifier ) )
            return false;

        if ( $method === self::METHOD_S256 )
            return hash_equals( $challenge, self::s256( $verifier ) );

        if ( $method === self::METHOD_PLAIN && $allowPlain )
            return hash_equals( $challenge, $verifier );

        return false;
    }

    /**
     * BASE64URL-ENCODE(SHA256(ASCII(code_verifier))), RFC 7636 section 4.2.
     *
     * @param string $verifier
     * @return string
     */
    public static function s256( $verifier )
    {
        return rtrim( strtr( base64_encode( hash( 'sha256', $verifier, true ) ), '+/', '-_' ), '=' );
    }

    /**
     * A public client has no client secret: it cannot keep one (a mobile or browser application) and is identified
     * by its client_id alone.
     *
     * @param ezpRestClient|object $client
     * @return bool
     */
    public static function isPublicClient( $client )
    {
        return !isset( $client->client_secret ) || !is_string( $client->client_secret ) || $client->client_secret === '';
    }

    /**
     * Whether a client must use PKCE under the settings.
     *
     * @param ezpRestClient|object $client
     * @param eZINI $restINI rest.ini
     * @return bool
     */
    public static function isRequiredFor( $client, $restINI )
    {
        if ( self::isPublicClient( $client ) )
            return self::setting( $restINI, 'RequirePkceForPublicClients', true );
        return self::setting( $restINI, 'RequirePkceForConfidentialClients', false );
    }

    /**
     * Client authentication at the token end point. A confidential client must send its secret. A public client has
     * none and sends none; its code is bound to it by PKCE instead (checkRedemption()).
     *
     * @param ezpRestClient|object $client
     * @param mixed $clientSecret client_secret of the token request, null when it has none
     * @return bool
     */
    public static function clientAuthenticates( $client, $clientSecret )
    {
        if ( self::isPublicClient( $client ) )
            return $clientSecret === null || $clientSecret === '';
        return $client->validateSecret( $clientSecret );
    }

    /**
     * Decides whether an authorization code may be redeemed with the code_verifier of a token request.
     *
     * - A code stored with a challenge needs a code_verifier that matches it.
     * - A code stored without one is refused when the request sends a code_verifier (a client that thinks it used
     *   PKCE did not), and when the settings require PKCE for the client.
     *
     * @param ezpRestClient|object $client the authenticated client
     * @param ezpRestAuthcode|object $code the stored code
     * @param mixed $verifier code_verifier of the token request, null when it has none
     * @param eZINI $restINI rest.ini
     * @return string|null null when the code may be redeemed, otherwise the error_description for error=invalid_grant
     */
    public static function checkRedemption( $client, $code, $verifier, $restINI )
    {
        $challenge = isset( $code->code_challenge ) ? $code->code_challenge : null;
        $hasVerifier = $verifier !== null && $verifier !== false;

        if ( is_string( $challenge ) && $challenge !== '' )
        {
            if ( !$hasVerifier )
                return 'Missing code_verifier';
            $method = isset( $code->code_challenge_method ) ? $code->code_challenge_method : self::METHOD_PLAIN;
            return self::verify( $verifier, $challenge, $method, self::plainAllowed( $restINI ) ) ? null : 'Invalid code_verifier';
        }

        if ( $hasVerifier )
            return 'code_verifier for a code issued without code_challenge';
        if ( self::isRequiredFor( $client, $restINI ) )
            return 'PKCE is required for this client';
        return null;
    }

    /**
     * @param eZINI $restINI rest.ini
     * @return bool AllowPlainPkce
     */
    public static function plainAllowed( $restINI )
    {
        return self::setting( $restINI, 'AllowPlainPkce', false );
    }

    /** @return bool an enabled/disabled setting of [OAuthSettings], $default when it is not set */
    protected static function setting( $restINI, $name, $default )
    {
        if ( !is_object( $restINI ) || !$restINI->hasVariable( 'OAuthSettings', $name ) )
            return $default;
        $value = strtolower( trim( (string)$restINI->variable( 'OAuthSettings', $name ) ) );
        if ( $value === 'enabled' )
            return true;
        if ( $value === 'disabled' )
            return false;
        return $default;
    }
}
