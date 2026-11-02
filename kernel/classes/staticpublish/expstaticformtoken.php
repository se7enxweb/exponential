<?php
/**
 * File containing the expStaticFormToken class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The token a form of a static copy carries to the form endpoint (static publishing,
 * doc/eng-us/specifications/6.0/static-publishing-form-endpoint.md): signed at build time for one target and one
 * content object, valid for [StaticPublishSettings] FormTokenDays (default 400) from the build. It holds no secret
 * and no session; it only shows that the form was written by a build of this installation for this target and this
 * object, so the endpoint takes no post made up elsewhere.
 *
 *   v1.<issued unix time>.<object id>.<HMAC-SHA256 of "staticpublish-form/1|target|object|issued", base64url>
 *
 * The key is derived from the installation's secret (expMailSecret::derive( 'staticpublish-form' )), so every server
 * of a cluster checks the same tokens, and a new secret ends every token in the copies (a new build makes new ones).
 *
 * @since 6.0.15
 * @package kernel
 */
class expStaticFormToken
{
    const VERSION = 'v1';

    /** The default lifetime of a token in days */
    const DAYS = 400;

    /** @var string|null tests only */
    protected static $testKey = null;

    /** @return string the raw key */
    public static function key()
    {
        if ( self::$testKey !== null )
            return self::$testKey;
        return expMailSecret::derive( 'staticpublish-form' );
    }

    /**
     * Tests only: a fixed key; null resets.
     *
     * @param string|null $key
     */
    public static function setKeyForTest( $key = null )
    {
        self::$testKey = $key;
    }

    /**
     * A token for a form of a target.
     *
     * @param string $targetId
     * @param int $objectId
     * @param int|null $issued unix time, default now
     * @param string|null $key the raw key, default key()
     * @return string
     */
    public static function sign( $targetId, $objectId, $issued = null, $key = null )
    {
        $issued = $issued === null ? time() : (int)$issued;
        return self::VERSION . '.' . $issued . '.' . (int)$objectId . '.' . self::mac( $targetId, $objectId, $issued, $key === null ? self::key() : $key );
    }

    /**
     * Why a token is not valid; '' when it is.
     *
     * @param string $token
     * @param string $targetId
     * @param int|string $objectId the object the post names
     * @param int $days the lifetime
     * @param int|null $now
     * @param string|null $key
     * @return string
     */
    public static function problem( $token, $targetId, $objectId, $days = self::DAYS, $now = null, $key = null )
    {
        $now = $now === null ? time() : (int)$now;
        if ( !preg_match( '#^v1\.(\d{1,12})\.(\d{1,12})\.([A-Za-z0-9_-]{43})$#', (string)$token, $m ) )
            return 'no token, or not one of a static build';
        if ( (string)$m[2] !== (string)(int)$objectId || !ctype_digit( (string)$objectId ) )
            return 'the token was made for another form';
        if ( !hash_equals( self::mac( $targetId, (int)$m[2], (int)$m[1], $key === null ? self::key() : $key ), $m[3] ) )
            return 'the token is not signed by this installation for this target';
        if ( (int)$m[1] > $now + 300 )
            return 'the token is from the future';
        if ( $days > 0 && $now - (int)$m[1] > (int)$days * 86400 )
            return 'the token has expired: publish the site again';
        return '';
    }

    /** @return string base64url of the HMAC */
    protected static function mac( $targetId, $objectId, $issued, $key )
    {
        $mac = hash_hmac( 'sha256', 'staticpublish-form/1|' . (string)$targetId . '|' . (int)$objectId . '|' . (int)$issued, (string)$key, true );
        return rtrim( strtr( base64_encode( $mac ), '+/', '-_' ), '=' );
    }
}
