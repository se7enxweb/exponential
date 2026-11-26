<?php
/**
 * File containing the expContentHostPreview class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The preview of a version on the host of the siteaccess it is shown in. The administration runs on its own host;
 * a site matched by host (wedding.example.com) can only show its real menus, links, canonical address and cookies
 * when the preview is rendered there. The editor has no session on that host, so the administration hands the
 * browser a signed, short-lived token for exactly one version (content/hostpreview/<token>), and the site shows that
 * version once.
 * Guide: doc/eng-us/features/6.0/preview-in-owning-siteaccess.md
 *
 *   $token = expContentHostPreview::issue( $version, 'ger-DE', 'wedding_ger', 'full', 0 ); // or null
 *   $shown = expContentHostPreview::redeem( $token );  // array( claims, object, version ) or array( error )
 *
 * - The token is <claims>.<signature>: the claims as base64url JSON (user, object, version, language, siteaccess,
 *   view mode, placement, expiry, nonce, the origin of the administration) and their HMAC-SHA256 under the secret
 *   content.hostpreview.key of the secret store (expSecrets, made with the first token). Removing the secret on
 *   Setup > Secrets ends every token at once.
 * - A token works once: issuing it writes the row exp_hostpv_<nonce> of ezsite_data; redeeming renames the row to
 *   a name only this request knows and removes it, so a second request with the same token finds nothing.
 * - It expires after site.ini [SiteAccessSettings] PreviewTokenLifetime seconds (default 300, at most 900), works
 *   only on the siteaccess it names, only while the user who asked for it exists and is enabled, and only for the
 *   version it names. It grants nothing else: the page around the version is what a visitor of that siteaccess
 *   sees, no session is started and nobody is signed in.
 * - Issuing needs content/versionread of the version (content/versionview checks it before it asks for a token).
 *
 * encode(), decode() and frameAncestors() are pure; check() needs only the store of used tokens (tests give it one
 * in memory with setStore()).
 *
 * @since 6.0.15
 * @package kernel
 */
class expContentHostPreview
{
    /** The name of the signing key in the secret store */
    const SECRET = 'content.hostpreview.key';

    /** The prefix of the ezsite_data rows of issued tokens (the column takes 60 characters) */
    const ROW_PREFIX = 'exp_hostpv_';

    /** The lifetime of a token: five minutes by default, between one and fifteen */
    const DEFAULT_LIFETIME = 300;
    const MIN_LIFETIME = 60;
    const MAX_LIFETIME = 900;

    /** The longest token taken */
    const MAX_LENGTH = 2048;

    /** The context of the signature */
    const CONTEXT = 'exp-host-preview-v1';

    /** @var array|null issue and claim callables instead of ezsite_data (tests) */
    protected static $store = null;

    // ------------------------------------------------------------------------------------------------ pure

    /**
     * @param string $data
     * @return string
     */
    protected static function b64( $data )
    {
        return rtrim( strtr( base64_encode( (string)$data ), '+/', '-_' ), '=' );
    }

    /**
     * The token of a set of claims.
     *
     * @param string $key
     * @param array $claims u (user id), o (object id), v (version), l (language), s (siteaccess), m (view mode),
     *                      p (placement, 0 for the main one), e (expiry), n (nonce, 24 hex digits), a (origin of the
     *                      administration, scheme://host[:port])
     * @return string
     */
    public static function encode( $key, array $claims )
    {
        $payload = self::b64( json_encode( self::normalize( $claims ) ) );
        return $payload . '.' . self::b64( hash_hmac( 'sha256', self::CONTEXT . '|' . $payload, (string)$key, true ) );
    }

    /**
     * The claims of a token, when its signature is right and it has not expired.
     *
     * @param string $key
     * @param string $token
     * @param int $now
     * @return array claims, or error: invalid (no token of this form, or the signature is not right), expired
     */
    public static function decode( $key, $token, $now )
    {
        if ( !is_string( $key ) || $key === '' || !is_string( $token ) || strlen( $token ) > self::MAX_LENGTH
             || !preg_match( '/^([A-Za-z0-9_-]+)\.([A-Za-z0-9_-]{43})$/', $token, $m ) )
            return array( 'error' => 'invalid' );
        $expected = self::b64( hash_hmac( 'sha256', self::CONTEXT . '|' . $m[1], $key, true ) );
        if ( !hash_equals( $expected, $m[2] ) )
            return array( 'error' => 'invalid' );
        $json = base64_decode( strtr( $m[1], '-_', '+/' ), true );
        $claims = is_string( $json ) ? json_decode( $json, true ) : null;
        if ( !is_array( $claims ) || !self::valid( $claims ) )
            return array( 'error' => 'invalid' );
        $claims = self::normalize( $claims );
        if ( $claims['e'] <= (int)$now )
            return array( 'error' => 'expired', 'claims' => $claims );
        return array( 'claims' => $claims );
    }

    /**
     * @param array $claims
     * @return bool every claim is there and of its form
     */
    public static function valid( array $claims )
    {
        foreach ( array( 'u', 'o', 'v', 'l', 's', 'm', 'p', 'e', 'n', 'a' ) as $name )
            if ( !array_key_exists( $name, $claims ) )
                return false;
        return (int)$claims['u'] > 0 && (int)$claims['o'] > 0 && (int)$claims['v'] > 0 && (int)$claims['e'] > 0 && (int)$claims['p'] >= 0
            && preg_match( '/^[a-z]{2,4}-[A-Za-z]{2,4}$/', (string)$claims['l'] )
            && preg_match( '/^[A-Za-z0-9_-]+$/', (string)$claims['s'] )
            && preg_match( '/^[A-Za-z0-9_-]+$/', (string)$claims['m'] )
            && preg_match( '/^[0-9a-f]{24}$/', (string)$claims['n'] )
            && self::validOrigin( (string)$claims['a'] );
    }

    /**
     * @param array $claims
     * @return array the claims with their types, in their order
     */
    protected static function normalize( array $claims )
    {
        return array( 'u' => (int)$claims['u'], 'o' => (int)$claims['o'], 'v' => (int)$claims['v'], 'l' => (string)$claims['l'],
                      's' => (string)$claims['s'], 'm' => (string)$claims['m'], 'p' => (int)$claims['p'], 'e' => (int)$claims['e'],
                      'n' => (string)$claims['n'], 'a' => (string)$claims['a'] );
    }

    /**
     * @param string $origin
     * @return bool scheme://host[:port] and nothing else
     */
    public static function validOrigin( $origin )
    {
        return (bool)preg_match( '#^https?://[A-Za-z0-9.-]+(:\d{1,5})?$#', (string)$origin );
    }

    /**
     * @param int|null $asked seconds (null: the default)
     * @return int between MIN_LIFETIME and MAX_LIFETIME
     */
    public static function lifetime( $asked )
    {
        $seconds = $asked === null || (int)$asked <= 0 ? self::DEFAULT_LIFETIME : (int)$asked;
        return max( self::MIN_LIFETIME, min( $seconds, self::MAX_LIFETIME ) );
    }

    /**
     * A Content-Security-Policy with the origin of the administration added to its frame-ancestors, so the
     * administration may show the preview in its frame; a policy without frame-ancestors stays as it is.
     *
     * @param string $policy
     * @param string $origin
     * @return string
     */
    public static function frameAncestors( $policy, $origin )
    {
        if ( !self::validOrigin( $origin ) )
            return (string)$policy;
        $parts = array_map( 'trim', explode( ';', (string)$policy ) );
        foreach ( $parts as $i => $part )
        {
            if ( preg_match( '/^frame-ancestors(\s+(.*))?$/i', $part, $m ) )
            {
                $sources = isset( $m[2] ) ? preg_split( '/\s+/', trim( $m[2] ) ) : array();
                $sources = array_values( array_diff( array_filter( $sources ), array( "'none'" ) ) );
                if ( !in_array( $origin, $sources, true ) )
                    $sources[] = $origin;
                $parts[$i] = 'frame-ancestors ' . implode( ' ', $sources );
            }
        }
        return implode( '; ', array_filter( $parts, 'strlen' ) );
    }

    // ------------------------------------------------------------------------------------------------ the key

    /**
     * @return bool tokens can be made here: a secret store to keep the key in
     */
    public static function available()
    {
        return class_exists( 'expSecrets' ) && expSecrets::installed() && class_exists( 'expSecretKey' ) && expSecretKey::supported();
    }

    /**
     * @param bool $create make the key when there is none (the first token)
     * @return string|null
     */
    protected static function key( $create = false )
    {
        if ( !class_exists( 'expSecrets' ) )
            return null;
        $key = expSecrets::get( self::SECRET );
        if ( ( $key === null || $key === '' ) && $create && self::available() )
        {
            expSecrets::set( self::SECRET, bin2hex( random_bytes( 32 ) ), 'content',
                             'Signs the one-time tokens with which a version preview is shown on the host of its siteaccess (content/hostpreview).' );
            $key = expSecrets::get( self::SECRET );
        }
        return is_string( $key ) && $key !== '' ? $key : null;
    }

    /**
     * @return int site.ini [SiteAccessSettings] PreviewTokenLifetime
     */
    public static function configuredLifetime()
    {
        $ini = eZINI::instance();
        return self::lifetime( $ini->hasVariable( 'SiteAccessSettings', 'PreviewTokenLifetime' )
                               ? (int)$ini->variable( 'SiteAccessSettings', 'PreviewTokenLifetime' ) : null );
    }

    // ------------------------------------------------------------------------------------------------ tokens

    /**
     * A token for the preview of a version on a siteaccess. The caller has checked content/versionread.
     *
     * @param eZContentObjectVersion $version
     * @param string $language
     * @param string $siteaccess
     * @param string $viewMode
     * @param int $placementID the node assignment shown (0: the main one)
     * @param string $origin the administration's scheme://host[:port]
     * @return string|null null when no token can be made (no secret store, no key)
     */
    public static function issue( eZContentObjectVersion $version, $language, $siteaccess, $viewMode, $placementID, $origin )
    {
        if ( !self::available() )
            return null;
        $key = self::key( true );
        if ( $key === null )
            return null;
        $now = time();
        $claims = array( 'u' => (int)eZUser::currentUserID(), 'o' => (int)$version->attribute( 'contentobject_id' ),
                         'v' => (int)$version->attribute( 'version' ), 'l' => (string)$language, 's' => (string)$siteaccess,
                         'm' => (string)$viewMode, 'p' => max( 0, (int)$placementID ), 'e' => $now + self::configuredLifetime(),
                         'n' => bin2hex( random_bytes( 12 ) ), 'a' => (string)$origin );
        if ( !self::valid( $claims ) )
            return null;
        if ( !call_user_func( self::store()['issue'], $claims['n'], $claims['e'], $now ) )
            return null;
        return self::encode( $key, $claims );
    }

    /**
     * What content/hostpreview shows for a token, on the current siteaccess; the token is used up by it.
     *
     * @param string $token
     * @param string $siteaccess the current siteaccess
     * @return array claims, object, version; or error: invalid, expired, siteaccess (another one's token), used
     *               (shown before), gone (no such version or user any more)
     */
    public static function redeem( $token, $siteaccess )
    {
        $key = self::key();
        if ( $key === null )
            return array( 'error' => 'invalid' );
        $checked = self::check( $key, $token, $siteaccess, time() );
        if ( isset( $checked['error'] ) )
            return $checked;
        $claims = $checked['claims'];
        $user = eZUser::fetch( $claims['u'] );
        $object = eZContentObject::fetch( $claims['o'] );
        $version = $object instanceof eZContentObject ? $object->version( $claims['v'] ) : null;
        if ( !$user instanceof eZUser || !$user->isEnabled() || !$version instanceof eZContentObjectVersion
             || !in_array( $claims['l'], array_map( 'strval', (array)$version->translationList( false, false ) ), true ) )
            return array( 'error' => 'gone' );
        return array( 'claims' => $claims, 'object' => $object, 'version' => $version );
    }

    /**
     * The checks of a token that need no content: the signature, the expiry, the siteaccess, and that it was not
     * used before (it is used up by this call when it passes the others).
     *
     * @param string $key
     * @param string $token
     * @param string $siteaccess the current siteaccess
     * @param int $now
     * @return array claims; or error: invalid, expired, siteaccess, used
     */
    public static function check( $key, $token, $siteaccess, $now )
    {
        $decoded = self::decode( $key, $token, $now );
        if ( isset( $decoded['error'] ) )
            return $decoded;
        $claims = $decoded['claims'];
        if ( $claims['s'] !== (string)$siteaccess )
            return array( 'error' => 'siteaccess' );
        if ( !call_user_func( self::store()['claim'], $claims['n'] ) )
            return array( 'error' => 'used' );
        return array( 'claims' => $claims );
    }

    // ------------------------------------------------------------------------------------------------ storage

    /**
     * For tests: issue( nonce, expires, now ): bool and claim( nonce ): bool instead of ezsite_data; null restores.
     *
     * @param array|null $store
     * @return void
     */
    public static function setStore( $store )
    {
        self::$store = $store;
    }

    /**
     * @return array issue and claim
     */
    protected static function store()
    {
        if ( self::$store !== null )
            return self::$store;
        return array(
            'issue' => function ( $nonce, $expires, $now )
            {
                self::prune( $now );
                eZSiteData::createNew( self::ROW_PREFIX . $nonce, (string)(int)$expires );
                return true;
            },
            'claim' => function ( $nonce )
            {
                // a compare-and-set on the name: the row is renamed to a name only this request knows; a second
                // request with the same token renames nothing and finds no row of its own
                $db = eZDB::instance();
                $issued = self::ROW_PREFIX . $nonce;
                $claimed = self::ROW_PREFIX . $nonce . '_' . bin2hex( random_bytes( 6 ) );
                $db->query( "UPDATE ezsite_data SET name='" . $db->escapeString( $claimed ) . "' WHERE name='" . $db->escapeString( $issued ) . "'" );
                $row = eZSiteData::fetchByName( $claimed );
                if ( !$row instanceof eZSiteData )
                    return false;
                $row->remove();
                return true;
            },
        );
    }

    /**
     * Removes the rows of tokens that expired and were never used.
     *
     * @param int $now
     * @return void
     */
    protected static function prune( $now )
    {
        foreach ( eZSiteData::fetchList( self::ROW_PREFIX . '*', 0, 200 ) as $row )
        {
            if ( (int)$row->attribute( 'value' ) < (int)$now )
                $row->remove();
        }
    }
}

?>
