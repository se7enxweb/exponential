<?php
/**
 * File containing the expSignInSecondFactor class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The second step of signing in on the paths that do not go through the web login form: the REST interface with a
 * password (ezpRestBasicAuthStyle), WebDAV, SOAP and the expservices login. A two-step sign-in extension asks for its
 * second step only in the web login form (its login handler); the paths here check the password themselves, so an
 * account the extension requires a second step of could sign in with the password alone. Here the kernel asks the
 * extension, through the classes of site.ini [SignInSecondFactorSettings] Handlers[] (expSignInSecondFactorHandler),
 * before such a sign-in counts:
 *
 * - checkSignIn(): an account whose password was right may go on, or is refused with a reason
 *   (second_factor_required, second_factor_invalid, second_factor_setup_required). A one-time code the request
 *   carries (the header [SignInSecondFactorSettings] CodeHeader, X-Exp-OTP; the field otp of the expservices login) is
 *   passed along; the extension decides on which channels it counts.
 * - appPasswordUser(): a secret sent in place of the password that is one of the account's app passwords signs it in,
 *   for this request only (no session), on the channels the app password was made for. It is checked before the
 *   password, so it never counts as a failed password.
 *
 * Without a handler (no two-step extension, or one that does not register) nothing changes. Console commands do not
 * come here. Each refusal is recorded as access.user.second_factor.refused with the channel and the reason; the
 * extension records its app passwords through audit() (access.user.app_password.create, .revoke, .use), so every
 * name of the catalogue has a hook point in the kernel.
 *
 * Every protocol answers in its own way (refusalStatus(), wwwAuthenticate(), soapFault(), the REST status
 * expRestSecondFactorRequiredStatus). Nothing here keeps state between requests except lastRefusal(), which each
 * check() replaces, so persistent workers (Velocity) carry nothing over.
 *
 * Guide: doc/eng-us/features/6.0/second-factor-on-other-sign-in-paths.md
 */
class expSignInSecondFactor
{
    const CHANNEL_REST = 'rest';
    const CHANNEL_WEBDAV = 'webdav';
    const CHANNEL_SOAP = 'soap';
    const CHANNEL_SERVICES = 'expservices';

    /** The password was right; a second factor (an app password, or a code where the channel takes one) is missing */
    const REASON_REQUIRED = 'second_factor_required';

    /** A one-time code was sent and was not right (or not accepted on this channel for this account) */
    const REASON_INVALID = 'second_factor_invalid';

    /** The account has to set up its second step in the web interface first */
    const REASON_SETUP = 'second_factor_setup_required';

    const AUDIT_REFUSED = 'access.user.second_factor.refused';
    const AUDIT_APP_PASSWORD_CREATE = 'access.user.app_password.create';
    const AUDIT_APP_PASSWORD_REVOKE = 'access.user.app_password.revoke';
    const AUDIT_APP_PASSWORD_USE = 'access.user.app_password.use';

    /** Two-step sign-in turned on for an account (after: method) */
    const AUDIT_2FA_ENROLL = 'access.user.two_factor.enroll';
    /** Two-step sign-in turned off or reset (after: method) */
    const AUDIT_2FA_REMOVE = 'access.user.two_factor.remove';
    /** A recovery code signed in in place of the app's code (after: left) */
    const AUDIT_2FA_RECOVERY_USED = 'access.user.two_factor.recovery_used';
    /** A new set of recovery codes (after: count) */
    const AUDIT_2FA_RECOVERY_ISSUED = 'access.user.two_factor.recovery_issued';
    /** A wrong code at the second step of a web sign-in (after: method) */
    const AUDIT_2FA_VERIFY_FAILED = 'access.user.two_factor.verify.failed';

    /** The request header of a one-time code when site.ini does not name one */
    const DEFAULT_CODE_HEADER = 'X-Exp-OTP';

    /** The field of a one-time code in a form post (the expservices login) */
    const CODE_FIELD = 'otp';

    /** The response header that names the reason of a refusal, for clients that only see headers (WebDAV) */
    const REFUSAL_HEADER = 'X-Exp-Sign-In-Refused';

    /** @var expSignInSecondFactorHandler[]|null the handlers to use instead of site.ini's (tests) */
    public static $handlers = null;

    /** @var array|null the refusal of the last check() of this request */
    protected static $lastRefusal = null;

    /**
     * @return string[] the channels the kernel asks for
     */
    public static function channels()
    {
        return array( self::CHANNEL_REST, self::CHANNEL_WEBDAV, self::CHANNEL_SOAP, self::CHANNEL_SERVICES );
    }

    /**
     * @return string[] the reasons a refusal can carry
     */
    public static function reasons()
    {
        return array( self::REASON_REQUIRED, self::REASON_INVALID, self::REASON_SETUP );
    }

    /**
     * The handlers: the classes of site.ini [SignInSecondFactorSettings] Handlers[] that implement
     * expSignInSecondFactorHandler. A class that is missing or does not implement it is left out and logged.
     *
     * @return expSignInSecondFactorHandler[]
     */
    public static function handlers()
    {
        if ( is_array( static::$handlers ) )
            return static::$handlers;
        if ( !class_exists( 'eZINI' ) )
            return array();
        $ini = eZINI::instance();
        if ( !$ini->hasVariable( 'SignInSecondFactorSettings', 'Handlers' ) )
            return array();
        $handlers = array();
        foreach ( array_unique( array_filter( array_map( 'trim', (array)$ini->variable( 'SignInSecondFactorSettings', 'Handlers' ) ), 'strlen' ) ) as $class )
        {
            if ( class_exists( $class ) && is_subclass_of( $class, 'expSignInSecondFactorHandler' ) )
                $handlers[] = new $class();
            else if ( class_exists( 'eZDebug' ) )
                eZDebug::writeError( "site.ini [SignInSecondFactorSettings] Handlers[] names $class, which is no expSignInSecondFactorHandler", __METHOD__ );
        }
        return $handlers;
    }

    /**
     * Whether an account whose password was right may sign in through a channel. Records each refusal
     * (access.user.second_factor.refused).
     *
     * @param eZUser|null $user
     * @param string $channel a CHANNEL_* constant
     * @param string|null $code a one-time code the request carries
     * @return array|null null: it may; else the refusal: reason, message, status (401 or 403), channel
     */
    public static function check( $user, $channel, $code = null )
    {
        static::$lastRefusal = null;
        if ( !$user instanceof eZUser || $user->isAnonymous() )
            return null;
        $refusal = self::decide( static::handlers(), $user, $channel, $code );
        if ( $refusal === null )
            return null;
        static::$lastRefusal = $refusal;
        self::audit( self::AUDIT_REFUSED, $user, array( 'channel' => $refusal['channel'], 'code_sent' => $code !== null && $code !== '' ),
                     'refused', $refusal['reason'] );
        return $refusal;
    }

    /**
     * The decision without settings or a database: the first handler that refuses decides.
     *
     * @param expSignInSecondFactorHandler[] $handlers
     * @param eZUser $user
     * @param string $channel
     * @param string|null $code
     * @return array|null see check()
     */
    public static function decide( array $handlers, eZUser $user, $channel, $code )
    {
        $channel = self::channel( $channel );
        $code = is_string( $code ) && trim( $code ) !== '' ? trim( $code ) : null;
        foreach ( $handlers as $handler )
        {
            if ( !$handler instanceof expSignInSecondFactorHandler )
                continue;
            $answer = $handler->checkSignIn( $user, $channel, $code );
            if ( is_array( $answer ) )
                return self::refusal( isset( $answer['reason'] ) ? $answer['reason'] : '', isset( $answer['message'] ) ? $answer['message'] : '', $channel );
        }
        return null;
    }

    /**
     * A refusal in the form every channel answers from: a known reason (anything else is second_factor_required), a
     * message (the reason's own when none is given), the HTTP status (403 when only the web interface can help:
     * second_factor_setup_required; else 401, other credentials can) and the channel.
     *
     * @param string $reason
     * @param string $message
     * @param string $channel
     * @return array reason, message, status, channel
     */
    public static function refusal( $reason, $message, $channel )
    {
        $reason = in_array( $reason, self::reasons(), true ) ? $reason : self::REASON_REQUIRED;
        $message = is_string( $message ) ? trim( preg_replace( '/[\r\n]+/', ' ', $message ) ) : '';
        if ( $message === '' )
            $message = self::message( $reason );
        return array( 'reason' => $reason, 'message' => $message, 'status' => $reason === self::REASON_SETUP ? 403 : 401,
                      'channel' => self::channel( $channel ) );
    }

    /**
     * The sentence of a reason when the handler gives none.
     *
     * @param string $reason
     * @return string
     */
    public static function message( $reason )
    {
        switch ( $reason )
        {
            case self::REASON_SETUP:
                return 'This account needs a second sign-in step. Sign in to the site and set it up first, then make an app password for this client.';
            case self::REASON_INVALID:
                return 'The one-time code is not right, or is not accepted here. Send the code your authenticator app shows now, or use an app password.';
        }
        return 'This account needs a second sign-in step. The password alone is not enough here: use an app password made on the two-step sign-in page of the site.';
    }

    /**
     * @param string $channel
     * @return string a known channel, or "other"
     */
    public static function channel( $channel )
    {
        $channel = strtolower( trim( (string)$channel ) );
        return in_array( $channel, self::channels(), true ) ? $channel : 'other';
    }

    /**
     * @return array|null the refusal of the last check() of this request
     */
    public static function lastRefusal()
    {
        return static::$lastRefusal;
    }

    /**
     * The account an app password belongs to, when $secret is one that may be used on the channel (see
     * expSignInSecondFactorHandler::appPasswordUser()). Disabled accounts and accounts locked by failed sign-ins are
     * never returned.
     *
     * @param string $login
     * @param string $secret
     * @param string $channel
     * @return eZUser|null
     */
    public static function appPasswordUser( $login, $secret, $channel )
    {
        if ( !is_string( $login ) || trim( $login ) === '' || !is_string( $secret ) || $secret === '' )
            return null;
        $channel = self::channel( $channel );
        foreach ( static::handlers() as $handler )
        {
            $user = $handler->appPasswordUser( trim( $login ), $secret, $channel );
            if ( !$user instanceof eZUser || $user->isAnonymous() )
                continue;
            if ( !$user->isEnabled() || !eZUser::isEnabledAfterFailedLogin( (int)$user->attribute( 'contentobject_id' ) ) )
                return null;
            return $user;
        }
        return null;
    }

    /**
     * The request header of a one-time code: site.ini [SignInSecondFactorSettings] CodeHeader.
     *
     * @return string
     */
    public static function codeHeader()
    {
        if ( class_exists( 'eZINI' ) )
        {
            $ini = eZINI::instance();
            if ( $ini->hasVariable( 'SignInSecondFactorSettings', 'CodeHeader' ) )
            {
                $name = trim( (string)$ini->variable( 'SignInSecondFactorSettings', 'CodeHeader' ) );
                if ( preg_match( '/^[A-Za-z0-9-]+$/D', $name ) )
                    return $name;
            }
        }
        return self::DEFAULT_CODE_HEADER;
    }

    /**
     * The $_SERVER key of a request header: X-Exp-OTP is HTTP_X_EXP_OTP.
     *
     * @param string $header
     * @return string
     */
    public static function serverKey( $header )
    {
        return 'HTTP_' . strtoupper( str_replace( '-', '_', (string)$header ) );
    }

    /**
     * The one-time code a request carries: the header CodeHeader, else (a form post) the field otp. Only digits,
     * spaces and dashes; anything else is no code.
     *
     * @param array|null $server default $_SERVER
     * @param array|null $post default $_POST
     * @param string|null $header default codeHeader()
     * @return string|null
     */
    public static function presentedCode( $server = null, $post = null, $header = null )
    {
        $server = is_array( $server ) ? $server : $_SERVER;
        $post = is_array( $post ) ? $post : $_POST;
        $key = self::serverKey( $header === null ? self::codeHeader() : $header );
        $value = null;
        if ( isset( $server[$key] ) && is_string( $server[$key] ) && trim( $server[$key] ) !== '' )
            $value = $server[$key];
        else if ( isset( $post[self::CODE_FIELD] ) && is_string( $post[self::CODE_FIELD] ) && trim( $post[self::CODE_FIELD] ) !== '' )
            $value = $post[self::CODE_FIELD];
        if ( $value === null || strlen( $value ) > 32 || !preg_match( '/^[0-9 \-]+$/D', trim( $value ) ) )
            return null;
        return trim( $value );
    }

    /**
     * The WWW-Authenticate value of a refusal on a basic-auth channel.
     *
     * @param string $realm
     * @return string
     */
    public static function wwwAuthenticate( $realm )
    {
        return 'Basic realm="' . addcslashes( (string)$realm, '"\\' ) . '", charset="UTF-8"';
    }

    /**
     * The status line of a refusal.
     *
     * @param array $refusal see refusal()
     * @return string
     */
    public static function refusalStatus( array $refusal )
    {
        return isset( $refusal['status'] ) && (int)$refusal['status'] === 403 ? '403 Forbidden' : '401 Unauthorized';
    }

    /**
     * Sends the headers of a refusal with header(): the status, WWW-Authenticate (401 only: other credentials can
     * help), the reason in X-Exp-Sign-In-Refused and no caching. Works under Apache/PHP-FPM and Velocity alike.
     *
     * @param array $refusal see refusal()
     * @param string $realm
     */
    public static function sendRefusalHeaders( array $refusal, $realm )
    {
        if ( headers_sent() )
            return;
        $status = self::refusalStatus( $refusal );
        header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) && is_string( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1' ) . ' ' . $status, true );
        if ( strpos( $status, '401' ) === 0 )
            header( 'WWW-Authenticate: ' . self::wwwAuthenticate( $realm ), true );
        header( self::REFUSAL_HEADER . ': ' . ( isset( $refusal['reason'] ) ? $refusal['reason'] : self::REASON_REQUIRED ), true );
        header( 'Cache-Control: private, no-store', true );
    }

    /**
     * The SOAP 1.1 fault of a refusal (Client: the request must change).
     *
     * @param array $refusal see refusal()
     * @return string
     */
    public static function soapFault( array $refusal )
    {
        $reason = isset( $refusal['reason'] ) ? (string)$refusal['reason'] : self::REASON_REQUIRED;
        $message = isset( $refusal['message'] ) ? (string)$refusal['message'] : self::message( $reason );
        $esc = function ( $text ) {
            return htmlspecialchars( $text, ENT_QUOTES | ENT_XML1, 'UTF-8' );
        };
        return '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
             . '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">'
             . '<SOAP-ENV:Body><SOAP-ENV:Fault>'
             . '<faultcode>SOAP-ENV:Client</faultcode>'
             . '<faultstring>' . $esc( $message ) . '</faultstring>'
             . '<detail><reason>' . $esc( $reason ) . '</reason></detail>'
             . '</SOAP-ENV:Fault></SOAP-ENV:Body></SOAP-ENV:Envelope>';
    }

    /**
     * Signs an account in for this request only, without a session (an app password: no cookie may come of it that
     * the web interface would take as a sign-in).
     *
     * @param eZUser $user
     */
    public static function signInForRequest( eZUser $user )
    {
        $id = (int)$user->attribute( 'contentobject_id' );
        $GLOBALS['eZUserGlobalInstance_'] = $user;
        $GLOBALS['eZUserGlobalInstance_' . $id] = $user;
    }

    /**
     * Records an event of the catalogue for an account: a refusal, what a handler did with an app password
     * (AUDIT_APP_PASSWORD_CREATE, AUDIT_APP_PASSWORD_REVOKE, AUDIT_APP_PASSWORD_USE), or a change of the account's
     * two-step sign-in (AUDIT_2FA_*: turned on or off, recovery codes issued or used, a wrong code at the second step).
     * Never a secret, a hash or a code. The events reach the user audit log, the security activity (user/security) and
     * the security alerts (expUserSecurityAlerts).
     *
     * @param string $name
     * @param eZUser $user
     * @param array $after
     * @param string $result success, refused
     * @param string|null $reason
     */
    public static function audit( $name, eZUser $user, array $after = array(), $result = 'success', $reason = null )
    {
        if ( !class_exists( 'expAuditHook' ) )
            return;
        if ( !in_array( $name, array( self::AUDIT_REFUSED, self::AUDIT_APP_PASSWORD_CREATE, self::AUDIT_APP_PASSWORD_REVOKE,
                                      self::AUDIT_APP_PASSWORD_USE, self::AUDIT_2FA_ENROLL, self::AUDIT_2FA_REMOVE,
                                      self::AUDIT_2FA_RECOVERY_USED, self::AUDIT_2FA_RECOVERY_ISSUED, self::AUDIT_2FA_VERIFY_FAILED ), true ) )
            return;
        expAuditHook::emit( $name, function () use ( $user, $after, $result, $reason ) {
            $data = array( 'object' => expAuditHook::user( $user ), 'result' => (string)$result, 'after' => $after );
            if ( $reason !== null )
                $data['reason'] = (string)$reason;
            return $data;
        } );
    }
}

?>
