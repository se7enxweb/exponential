<?php
/**
 * File containing the expMailToken class.
 *
 * The links in mail that work without login: unsubscribe (one click, RFC 8058), manage (the preference page) and
 * confirm (a double opt-in). A token is the recipient, the purpose, the category and the expiry, encrypted and
 * authenticated with AES-256-GCM under a key derived (HMAC-SHA-256) from the site secret (expMailSecret): it cannot
 * be read (an address in it stays hidden), changed or made without the secret. Nothing is stored per token.
 *
 * \code
 * $token = expMailToken::create( $recipient, 'unsubscribe', 'newsletter' );
 * $payload = expMailToken::verify( $token, 'unsubscribe' );   // null when not valid; expMailToken::lastError() says why
 * $recipient = expMailRecipient::fromPayload( $payload );
 * \endcode
 *
 * Payload keys: purpose, user (user id, 0 without account), email (only for an address without account), category
 * ('' for none), pending (expmail_pending id of a confirmation, 0 otherwise), issued, expires (0 never).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expMailToken
{
    const PURPOSES = array( 'unsubscribe', 'manage', 'confirm' );
    const VERSION = 'm1';
    const CIPHER = 'aes-256-gcm';
    const AAD = 'exp-mail-token';

    /** @var string|null why the last verify()/decode() failed: malformed, tampered, purpose, expired */
    protected static $lastError = null;

    /**
     * @param expMailRecipient $recipient
     * @param string $purpose unsubscribe|manage|confirm
     * @param string|null $category
     * @param int|null $ttl seconds; null: mailpreferences.ini [TokenSettings] TTL[<purpose>]; 0 never expires
     * @param array $extra 'pending' => expmail_pending id
     * @return string URL safe
     */
    public static function create( expMailRecipient $recipient, $purpose, $category = null, $ttl = null, array $extra = array() )
    {
        if ( !in_array( $purpose, self::PURPOSES, true ) )
            throw new InvalidArgumentException( "Not a token purpose: '$purpose'" );
        if ( $ttl === null )
            $ttl = self::defaultTTL( $purpose );
        $now = time();
        $data = array( 'p' => $purpose, 't' => $now, 'x' => $ttl > 0 ? $now + (int)$ttl : 0 );
        if ( $recipient->userId() > 0 )
            $data['u'] = $recipient->userId();
        if ( $recipient->userId() === 0 || !empty( $extra['email'] ) )
        {
            if ( $recipient->email() === '' )
                throw new InvalidArgumentException( 'A link for an address without account needs the address' );
            $data['e'] = $recipient->email();
        }
        if ( $category !== null && $category !== '' )
            $data['c'] = (string)$category;
        if ( !empty( $extra['pending'] ) )
            $data['i'] = (int)$extra['pending'];
        $plain = json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
        $iv = random_bytes( 12 );
        $tag = '';
        $cipher = openssl_encrypt( $plain, self::CIPHER, expMailSecret::derive( 'token' ), OPENSSL_RAW_DATA, $iv, $tag, self::AAD, 16 );
        if ( $cipher === false )
            throw new RuntimeException( 'The link could not be encrypted' );
        return self::VERSION . self::base64url( $iv . $cipher . $tag );
    }

    /**
     * @param string $token
     * @param string $purpose the purpose the token must have
     * @return array|null the payload, or null (lastError() says why)
     */
    public static function verify( $token, $purpose )
    {
        $payload = self::decode( $token );
        if ( $payload === null )
            return null;
        if ( $payload['purpose'] !== $purpose )
        {
            self::$lastError = 'purpose';
            return null;
        }
        return $payload;
    }

    /**
     * Checks the token without asking for a purpose.
     *
     * @param string $token
     * @param int|null $now for tests
     * @return array|null
     */
    public static function decode( $token, $now = null )
    {
        self::$lastError = null;
        $token = trim( (string)$token );
        if ( strlen( $token ) < 40 || strlen( $token ) > 2048 || strncmp( $token, self::VERSION, strlen( self::VERSION ) ) !== 0 )
        {
            self::$lastError = 'malformed';
            return null;
        }
        $raw = self::unbase64url( substr( $token, strlen( self::VERSION ) ) );
        if ( $raw === null || strlen( $raw ) < 12 + 16 + 2 )
        {
            self::$lastError = 'malformed';
            return null;
        }
        $iv = substr( $raw, 0, 12 );
        $tag = substr( $raw, -16 );
        $cipher = substr( $raw, 12, -16 );
        $plain = openssl_decrypt( $cipher, self::CIPHER, expMailSecret::derive( 'token' ), OPENSSL_RAW_DATA, $iv, $tag, self::AAD );
        $data = $plain === false ? null : json_decode( $plain, true );
        if ( !is_array( $data ) || !isset( $data['p'] ) || !in_array( $data['p'], self::PURPOSES, true ) )
        {
            self::$lastError = 'tampered';
            return null;
        }
        $payload = array( 'purpose' => $data['p'], 'user' => isset( $data['u'] ) ? (int)$data['u'] : 0,
                          'email' => isset( $data['e'] ) ? (string)$data['e'] : '', 'category' => isset( $data['c'] ) ? (string)$data['c'] : '',
                          'pending' => isset( $data['i'] ) ? (int)$data['i'] : 0, 'issued' => isset( $data['t'] ) ? (int)$data['t'] : 0,
                          'expires' => isset( $data['x'] ) ? (int)$data['x'] : 0 );
        if ( $payload['expires'] > 0 && $payload['expires'] < ( $now === null ? time() : (int)$now ) )
        {
            self::$lastError = 'expired';
            return null;
        }
        if ( $payload['user'] === 0 && $payload['email'] === '' )
        {
            self::$lastError = 'tampered';
            return null;
        }
        return $payload;
    }

    /** @return string|null malformed, tampered, purpose, expired; null after a valid token */
    public static function lastError()
    {
        return self::$lastError;
    }

    /**
     * The shortest lifetime of the unsubscribe and manage links of a mail: 60 days. CASL wants the unsubscribe
     * mechanism of a message to work for 60 days after it was sent, CAN-SPAM for 30.
     */
    const MIN_LINK_TTL = 5184000;

    /**
     * The lifetime of a link of this purpose ([TokenSettings] TTL[<purpose>]). For unsubscribe and manage a value
     * between 0 (never expires) and MIN_LINK_TTL is raised to MIN_LINK_TTL (belowMinimumTTL() lists it for the status).
     *
     * @param string $purpose
     * @return int seconds, 0 never
     */
    public static function defaultTTL( $purpose )
    {
        $configured = self::configuredTTL( $purpose );
        if ( $configured > 0 && $configured < self::MIN_LINK_TTL && in_array( $purpose, array( 'unsubscribe', 'manage' ), true ) )
        {
            if ( empty( self::$ttlNoted[$purpose] ) )
            {
                self::$ttlNoted[$purpose] = true;
                eZDebug::writeWarning( "[TokenSettings] TTL[$purpose]=$configured is below 60 days, the legal minimum of a link in a mail: 60 days are used", __METHOD__ );
            }
            return self::MIN_LINK_TTL;
        }
        return $configured;
    }

    /** @var bool[] purpose => the raised lifetime was noted in this process */
    protected static $ttlNoted = array();

    /** @return int the lifetime the settings give, 0 never */
    protected static function configuredTTL( $purpose )
    {
        $ini = eZINI::instance( 'mailpreferences.ini' );
        $ttl = $ini->hasVariable( 'TokenSettings', 'TTL' ) ? (array)$ini->variable( 'TokenSettings', 'TTL' ) : array();
        if ( isset( $ttl[$purpose] ) && is_numeric( $ttl[$purpose] ) )
            return max( 0, (int)$ttl[$purpose] );
        return $purpose === 'confirm' ? 604800 : 0;
    }

    /** @return int[] purpose => configured seconds, for the unsubscribe and manage links set below MIN_LINK_TTL */
    public static function belowMinimumTTL()
    {
        $out = array();
        foreach ( array( 'unsubscribe', 'manage' ) as $purpose )
        {
            $configured = self::configuredTTL( $purpose );
            if ( $configured > 0 && $configured < self::MIN_LINK_TTL )
                $out[$purpose] = $configured;
        }
        return $out;
    }

    /**
     * The absolute URL of a view of the mailpreferences module with the token.
     *
     * @param string $view unsubscribe|manage|confirm
     * @param string $token
     * @return string
     */
    public static function url( $view, $token )
    {
        return self::baseURL() . '/mailpreferences/' . $view . '/' . rawurlencode( $token );
    }

    /** @return string scheme and host (and siteaccess path) the links point to, no trailing slash */
    public static function baseURL()
    {
        $ini = eZINI::instance( 'mailpreferences.ini' );
        $base = $ini->hasVariable( 'LinkSettings', 'BaseURL' ) ? trim( (string)$ini->variable( 'LinkSettings', 'BaseURL' ) ) : '';
        if ( $base === '' )
        {
            $site = eZINI::instance();
            $siteURL = '';
            $default = $site->variable( 'SiteSettings', 'DefaultAccess' );
            if ( $default && class_exists( 'eZSiteAccess' ) )
            {
                try
                {
                    $saIni = eZSiteAccess::getIni( $default, 'site.ini' );
                    if ( $saIni && $saIni->hasVariable( 'SiteSettings', 'SiteURL' ) )
                        $siteURL = trim( (string)$saIni->variable( 'SiteSettings', 'SiteURL' ) );
                }
                catch ( Throwable $e )
                {
                }
            }
            if ( $siteURL === '' )
                $siteURL = trim( (string)$site->variable( 'SiteSettings', 'SiteURL' ) );
            $base = preg_match( '#^https?://#i', $siteURL ) ? $siteURL : 'https://' . $siteURL;
        }
        return rtrim( $base, '/' );
    }

    protected static function base64url( $raw )
    {
        return rtrim( strtr( base64_encode( $raw ), '+/', '-_' ), '=' );
    }

    protected static function unbase64url( $text )
    {
        if ( preg_match( '/[^A-Za-z0-9_-]/', $text ) )
            return null;
        $raw = base64_decode( strtr( $text, '-_', '+/' ), true );
        return $raw === false ? null : $raw;
    }
}
