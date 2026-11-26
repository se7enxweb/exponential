<?php
/**
 * File containing the expOAuthPasswordGrant class
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The OAuth2 password grant (RFC 6749, section 4.3, resource owner password credentials) of the token endpoint.
 *
 * It is off for every client by default. rest.ini [OAuthSettings] PasswordGrantClients[] names the client identifiers
 * (ezprest_clients.client_id) that may use it: a first-party application the site trusts with its users' passwords,
 * such as the site's own mobile app. Another client gets unauthorized_client; while the list is empty the grant type
 * is not offered at all (unsupported_grant_type, as before).
 *
 * The user signs in exactly as with the REST interface's basic authentication (expRestPasswordAuthFilter): by login,
 * or by e-mail address when [UserSettings] AuthenticateMatch allows it, with the user's own hash type, an app password
 * of a two-step sign-in first; a wrong password counts as a failed login, so [UserSettings] MaxNumberOfFailedLogin
 * locks the account here as on the sign-in form. A disabled, unpublished or locked account gets no token. The second
 * sign-in step is asked as on the REST channel (expSignInSecondFactor, CHANNEL_REST, opt-in), with the one-time code
 * in the header X-Exp-OTP or the field otp, and an account that must change its password is refused.
 * eZUser::loginUser() and the login handlers are not used: they start a session and answer a second step with a
 * redirect to a web page, which a token endpoint cannot follow.
 *
 * Failed attempts are also limited per login name (PasswordGrantMaxFailures in PasswordGrantFailureWindow seconds),
 * independent of MaxNumberOfFailedLogin, which is off (0) by default. While the limit is reached, the grant is refused
 * before the password is looked at.
 *
 * The token is issued as for an authorization code: an access token and a refresh token, TokenTTL, refreshed with
 * grant_type=refresh_token. The user's authorization of the client (ezprest_authorized_clients) is recorded on the
 * first grant, so the refresh grant and the list of the user's applications work as after oauth/authorize.
 */
class expOAuthPasswordGrant
{
    const GRANT_TYPE = 'password';
    const DEFAULT_MAX_FAILURES = 10;
    const DEFAULT_FAILURE_WINDOW = 900;

    /**
     * The answer to wrong credentials and to an account that may not sign in: one text, so the answer does not tell
     * whether an account exists or is disabled.
     */
    const BAD_CREDENTIALS = 'The user name or password is not right.';

    /**
     * The client identifiers allowed the grant: rest.ini [OAuthSettings] PasswordGrantClients[], without empty
     * entries.
     *
     * @param eZINI|null $restINI
     * @return string[]
     */
    public static function clients( $restINI = null )
    {
        $restINI = $restINI === null ? eZINI::instance( 'rest.ini' ) : $restINI;
        if ( !$restINI->hasVariable( 'OAuthSettings', 'PasswordGrantClients' ) )
            return array();
        $clients = array();
        foreach ( (array)$restINI->variable( 'OAuthSettings', 'PasswordGrantClients' ) as $clientId )
        {
            $clientId = is_scalar( $clientId ) ? trim( (string)$clientId ) : '';
            if ( $clientId !== '' )
                $clients[] = $clientId;
        }
        return $clients;
    }

    /**
     * Whether $clientId is one of $allowed (compared as strings in constant time; an empty identifier never is).
     *
     * @param mixed $clientId
     * @param string[] $allowed
     * @return bool
     */
    public static function clientAllowed( $clientId, array $allowed )
    {
        if ( !is_scalar( $clientId ) || (string)$clientId === '' )
            return false;
        $found = false;
        foreach ( $allowed as $candidate )
        {
            if ( is_scalar( $candidate ) && (string)$candidate !== '' && hash_equals( (string)$candidate, (string)$clientId ) )
                $found = true;
        }
        return $found;
    }

    /**
     * The failure limit: max failures (0 = no limit) and window in seconds.
     *
     * @param eZINI|null $restINI
     * @return int[] array( max, window )
     */
    public static function limits( $restINI = null )
    {
        $restINI = $restINI === null ? eZINI::instance( 'rest.ini' ) : $restINI;
        $max = $restINI->hasVariable( 'OAuthSettings', 'PasswordGrantMaxFailures' )
            ? (int)$restINI->variable( 'OAuthSettings', 'PasswordGrantMaxFailures' ) : self::DEFAULT_MAX_FAILURES;
        $window = $restINI->hasVariable( 'OAuthSettings', 'PasswordGrantFailureWindow' )
            ? (int)$restINI->variable( 'OAuthSettings', 'PasswordGrantFailureWindow' ) : self::DEFAULT_FAILURE_WINDOW;
        return array( max( 0, $max ), max( 1, $window ) );
    }

    /**
     * Issues an access and a refresh token for a user's login and password.
     *
     * @param string $clientId
     * @param string|null $clientSecret null for a public client
     * @param string $username
     * @param string $password
     * @return ezpRestToken
     * @throws ezpOauthInvalidRequestException with the OAuth error code first in the message (invalid_client,
     *         unauthorized_client, invalid_grant)
     */
    public static function issue( $clientId, $clientSecret, $username, $password )
    {
        $restINI = eZINI::instance( 'rest.ini' );
        $client = ezpRestClient::fetchByClientId( $clientId );
        if ( !$client instanceof ezpRestClient || !expOAuthPkce::clientAuthenticates( $client, $clientSecret ) )
            throw new ezpOauthInvalidRequestException( ezpOauthTokenEndpointErrorType::INVALID_CLIENT );

        if ( !self::clientAllowed( $clientId, self::clients( $restINI ) ) )
            throw new ezpOauthInvalidRequestException( ezpOauthTokenEndpointErrorType::UNAUTHORIZED_CLIENT
                                                       . ': The client is not allowed the password grant.' );

        list( $max, $window ) = self::limits( $restINI );
        $key = self::throttleKey( $username );
        $state = self::loadState( $key );
        $now = time();
        $wait = self::throttleWait( $state, $max, $window, $now );
        if ( $wait > 0 )
        {
            eZDebug::writeNotice( "OAuth password grant refused: too many failed attempts for one login name, $wait s left", __METHOD__ );
            throw self::invalidGrant( "Too many failed sign-in attempts. Try again in $wait seconds." );
        }

        $filter = new expRestPasswordAuthFilter();
        $status = $filter->run( new ezcAuthenticationPasswordCredentials( (string)$username, (string)$password ) );
        $user = expRestPasswordAuthFilter::$user;
        $viaAppPassword = expRestPasswordAuthFilter::$viaAppPassword;

        if ( $status === expRestPasswordAuthFilter::STATUS_INVALID_CREDENTIALS )
        {
            if ( $max > 0 )
                self::saveState( $key, self::throttleRecord( $state, true, $window, $now ) );
            throw self::invalidGrant( self::BAD_CREDENTIALS );
        }

        $secondFactor = null;
        $passwordChange = false;
        if ( $status === ezcAuthenticationFilter::STATUS_OK && $user instanceof eZUser && ezpOauthUtility::userMayUseToken( $user ) )
        {
            // A password alone, on an account that needs a second step on the REST channel: the code in X-Exp-OTP or
            // otp, or an app password. An app password is the second factor itself.
            if ( !$viaAppPassword && class_exists( 'expSignInSecondFactor' ) )
                $secondFactor = expSignInSecondFactor::check( $user, expSignInSecondFactor::CHANNEL_REST, expSignInSecondFactor::presentedCode() );
            if ( $secondFactor === null && class_exists( 'expPasswordChangeRequired' ) )
                $passwordChange = expPasswordChangeRequired::refusesSignIn( $user, 'rest' );
        }

        $refusal = self::refusal( $status, $user, $secondFactor, $passwordChange );
        if ( $refusal !== null )
        {
            eZDebug::writeNotice( 'OAuth password grant refused for user #' . ( $user instanceof eZUser ? (int)$user->attribute( 'contentobject_id' ) : 0 ), __METHOD__ );
            throw new ezpOauthInvalidRequestException( $refusal );
        }

        if ( !empty( $state ) )
            self::saveState( $key, self::throttleRecord( $state, false, $window, $now ) );

        // the user hands the client the password: that is the user's authorization of it
        if ( !ezpRestAuthorizedClient::fetchForClientUser( $client, $user ) instanceof ezpRestAuthorizedClient )
            $client->authorizeFor( $user );

        $token = new ezpRestToken();
        $token->id = ezpRestToken::generateToken( '' );
        $token->refresh_token = ezpRestToken::generateToken( '' );
        $token->client_id = $clientId;
        $token->user_id = (int)$user->attribute( 'contentobject_id' );
        $token->expirytime = time() + (int)$restINI->variable( 'OAuthSettings', 'TokenTTL' );
        ezcPersistentSessionInstance::get()->save( $token );

        return $token;
    }

    /**
     * The decision after the password check, without settings or a database.
     *
     * @param int $status the status of expRestPasswordAuthFilter::run()
     * @param eZUser|null $user the user it signed in
     * @param array|null $secondFactor the refusal of expSignInSecondFactor::check(), null when none
     * @param bool $passwordChangeRequired whether the account must change its password first
     * @param callable|null $enabledAfterFailedLogin see ezpOauthUtility::userMayUseToken()
     * @return string|null null when a token may be issued, else the message of the refusal (error code first)
     */
    public static function refusal( $status, $user, $secondFactor, $passwordChangeRequired, $enabledAfterFailedLogin = null )
    {
        if ( $status !== ezcAuthenticationFilter::STATUS_OK || !ezpOauthUtility::userMayUseToken( $user, $enabledAfterFailedLogin ) )
            return ezpOauthTokenEndpointErrorType::INVALID_GRANT . ': ' . self::BAD_CREDENTIALS;
        if ( is_array( $secondFactor ) )
            return ezpOauthTokenEndpointErrorType::INVALID_GRANT . ': '
                   . ( isset( $secondFactor['reason'] ) ? $secondFactor['reason'] : 'second_factor_required' ) . ': '
                   . ( isset( $secondFactor['message'] ) ? $secondFactor['message'] : '' );
        if ( $passwordChangeRequired )
            return ezpOauthTokenEndpointErrorType::INVALID_GRANT . ': password_change_required: '
                   . 'The password of this account must be changed on the website first.';
        return null;
    }

    /**
     * The seconds a login name has to wait, 0 when it may try.
     *
     * @param array $state array( 'start' => first failure of the window, 'count' => failures )
     * @param int $max failures allowed in a window, 0 = no limit
     * @param int $window seconds
     * @param int $now
     * @return int
     */
    public static function throttleWait( array $state, $max, $window, $now )
    {
        $max = (int)$max;
        if ( $max <= 0 || empty( $state['count'] ) )
            return 0;
        $start = isset( $state['start'] ) ? (int)$state['start'] : 0;
        if ( $now < $start || $now - $start >= (int)$window )
            return 0;
        return (int)$state['count'] >= $max ? max( 1, $start + (int)$window - (int)$now ) : 0;
    }

    /**
     * The state after an attempt: a failure counts in the window (a new window when the last one is over), a
     * success clears it.
     *
     * @param array $state
     * @param bool $failed
     * @param int $window
     * @param int $now
     * @return array
     */
    public static function throttleRecord( array $state, $failed, $window, $now )
    {
        if ( !$failed )
            return array();
        $start = isset( $state['start'] ) ? (int)$state['start'] : 0;
        if ( empty( $state['count'] ) || $now < $start || $now - $start >= (int)$window )
            return array( 'start' => (int)$now, 'count' => 1 );
        return array( 'start' => $start, 'count' => (int)$state['count'] + 1 );
    }

    /**
     * The key of a login name's failure count: a hash, so the state files do not name logins.
     *
     * @param string $username
     * @return string
     */
    public static function throttleKey( $username )
    {
        $name = trim( is_scalar( $username ) ? (string)$username : '' );
        return hash( 'sha256', 'oauth-password-grant|' . ( function_exists( 'mb_strtolower' ) ? mb_strtolower( $name, 'UTF-8' ) : strtolower( $name ) ) );
    }

    /** @return string */
    protected static function stateDir()
    {
        return eZSys::cacheDirectory() . '/oauth/password-grant';
    }

    /**
     * @param string $key
     * @return array
     */
    protected static function loadState( $key )
    {
        $raw = @file_get_contents( self::stateDir() . '/' . $key . '.json' );
        $state = $raw !== false ? json_decode( $raw, true ) : null;
        return is_array( $state ) ? $state : array();
    }

    /**
     * Writes the state (temporary file and rename, no lock: two attempts at the same moment may count once).
     *
     * @param string $key
     * @param array $state
     */
    protected static function saveState( $key, array $state )
    {
        $dir = self::stateDir();
        if ( !is_dir( $dir ) && !@mkdir( $dir, eZDir::dirMode( 0770 ), true ) && !is_dir( $dir ) )
        {
            eZDebug::writeWarning( "Cannot create $dir: the password grant's failure limit is not kept", __METHOD__ );
            return;
        }
        $file = $dir . '/' . $key . '.json';
        $tmp = $file . '.' . getmypid() . '.' . mt_rand() . '.tmp';
        if ( @file_put_contents( $tmp, json_encode( $state ) ) === false )
            return;
        @chmod( $tmp, eZFile::fileMode( 0660 ) );
        if ( !@rename( $tmp, $file ) )
            @unlink( $tmp );
    }

    /**
     * @param string $description
     * @return ezpOauthInvalidRequestException
     */
    protected static function invalidGrant( $description )
    {
        return new ezpOauthInvalidRequestException( ezpOauthTokenEndpointErrorType::INVALID_GRANT . ': ' . $description );
    }
}
?>
