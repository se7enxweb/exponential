<?php
/**
 * Preview tokens of the GraphQL API: a short-lived, signed permission to read one version of one content object
 * (a draft) in one language. The mutation createPreviewToken makes one for a user who may read that version; the
 * query preview(token:) reads the version with it, also for a client that is not signed in (a front end showing an
 * editor the page before it is published). A token names its object, version, language, the user who made it and
 * when it expires; it is signed with HMAC-SHA256 under a key derived from the installation's secret key
 * (expSecretKey: EXP_SECRET_KEY or var/secrets/exp_secret.key), so it is checked without a database and no table
 * is needed. Rotating the secret key ends every token.
 *
 * A token says what may be read; preview() still reads it through the kernel and checks that the version is there
 * and its object is of a class the schema shows. The user who made the token must still exist.
 *
 *   exp1.<base64url of {"o":58,"v":4,"l":"eng-GB","u":14,"e":1767229200}>.<base64url of the HMAC>
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expGraphQLPreviewToken
{
    /** The prefix of every token; raised when the format changes. */
    const PREFIX = 'exp1';

    /** The lifetime of a token when graphql.ini PreviewTokenTtl does not say: one hour. */
    const DEFAULT_TTL = 3600;

    /** The longest lifetime when graphql.ini MaxPreviewTokenTtl does not say: one day. */
    const MAX_TTL = 86400;

    /** @var string the signing key (binary) */
    protected $key;

    /**
     * @param string $secret the installation's secret (any length; the signing key is derived from it)
     */
    public function __construct( $secret )
    {
        $this->key = hash_hmac( 'sha256', 'exp-graphql-preview-token', (string)$secret, true );
    }

    /**
     * @return self|null the tokens of this installation (null: no secret key could be read or made)
     */
    public static function fromSettings()
    {
        $secret = class_exists( 'expSecretKey' ) ? expSecretKey::current( true ) : null;
        if ( !is_string( $secret ) || $secret === '' )
        {
            eZDebug::writeError( 'No secret key for GraphQL preview tokens (expSecretKey)', __METHOD__ );
            return null;
        }
        return new self( $secret );
    }

    /**
     * @param int|null $ttl seconds asked for (null: PreviewTokenTtl)
     * @return int the lifetime of a token: at least 60 seconds, at most MaxPreviewTokenTtl
     */
    public static function lifetime( $ttl = null )
    {
        $max = expGraphQL::intSetting( 'MaxPreviewTokenTtl', self::MAX_TTL );
        $ttl = $ttl === null ? expGraphQL::intSetting( 'PreviewTokenTtl', self::DEFAULT_TTL ) : (int)$ttl;
        return max( 60, min( $ttl, $max ) );
    }

    /**
     * @param int $objectId
     * @param int $version
     * @param string $language
     * @param int $userId the user who makes the token (the content object id of the user)
     * @param int $expires a Unix time
     * @return string
     */
    public function issue( $objectId, $version, $language, $userId, $expires )
    {
        $payload = self::base64url( json_encode( array( 'o' => (int)$objectId, 'v' => (int)$version, 'l' => (string)$language,
                                                        'u' => (int)$userId, 'e' => (int)$expires ) ) );
        return self::PREFIX . '.' . $payload . '.' . self::base64url( hash_hmac( 'sha256', self::PREFIX . '.' . $payload, $this->key, true ) );
    }

    /**
     * @param string $token
     * @param int|null $now
     * @return array|null objectId, version, language, userId, expires; null when the token is not one, its signature
     *                    does not match, or it has expired
     */
    public function verify( $token, $now = null )
    {
        $parts = explode( '.', (string)$token );
        if ( count( $parts ) !== 3 || $parts[0] !== self::PREFIX || strlen( (string)$token ) > 1024 )
            return null;
        $expected = self::base64url( hash_hmac( 'sha256', $parts[0] . '.' . $parts[1], $this->key, true ) );
        if ( !hash_equals( $expected, $parts[2] ) )
            return null;
        $data = json_decode( (string)self::unbase64url( $parts[1] ), true );
        if ( !is_array( $data ) || !isset( $data['o'], $data['v'], $data['l'], $data['u'], $data['e'] ) )
            return null;
        if ( (int)$data['e'] < ( $now === null ? time() : (int)$now ) )
            return null;
        return array( 'objectId' => (int)$data['o'], 'version' => (int)$data['v'], 'language' => (string)$data['l'],
                      'userId' => (int)$data['u'], 'expires' => (int)$data['e'] );
    }

    /**
     * @param string $bytes
     * @return string
     */
    protected static function base64url( $bytes )
    {
        return rtrim( strtr( base64_encode( (string)$bytes ), '+/', '-_' ), '=' );
    }

    /**
     * @param string $text
     * @return string|false
     */
    protected static function unbase64url( $text )
    {
        return base64_decode( strtr( (string)$text, '-_', '+/' ), true );
    }
}

?>
