<?php
/**
 * File containing the expContentPreviewLink class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Share links to the preview of a draft: a signed, expiring and revocable address with which a reviewer without an
 * account sees one version of one object in one language (content/sharedpreview/<token>), and nothing else.
 * Guide: doc/eng-us/features/6.0/preview-links.md
 *
 *   $made = expContentPreviewLink::create( $version, 'eng-GB', 86400 );  // array( link, token, path ) or array( error )
 *   $list = expContentPreviewLink::listFor( $objectID );                // the object's links, newest first
 *   expContentPreviewLink::revoke( $made['link']['id'] );
 *   $open = expContentPreviewLink::open( $token );                      // what content/sharedpreview shows
 *
 * - The token is <id>.<signature>: 24 hex digits naming the link and the HMAC-SHA256 of the link's object, version,
 *   language and expiry under the key in the secret store (expSecrets, the secret content.previewlink.key, made on
 *   the first link). Removing that secret on Setup > Secrets ends every link at once.
 * - Each link is a row of ezsite_data (exp_previewlink_<id>): who made it and when, its expiry, whether it was
 *   revoked, how often it was opened. A link works only while it is not revoked or expired, the version is still a
 *   draft (or pending approval), and the user who made it still exists and is enabled.
 * - Making a link needs the version's content/versionread and the object's content/edit. Making, revoking and
 *   opening are recorded in the audit (content.previewlink.create, .revoke, .open).
 * - content.ini [PreviewLinkSettings]: Enabled, DefaultLifetime, MaxLifetime, MaxActivePerVersion.
 *
 * The signing, the token's form, the lifetime and the state of a link are pure (tested without a database).
 *
 * @since 6.0.15
 * @package kernel
 */
class expContentPreviewLink
{
    /** The name of the signing key in the secret store */
    const SECRET = 'content.previewlink.key';

    /** The prefix of the ezsite_data rows */
    const ROW_PREFIX = 'exp_previewlink_';

    /** A token: the link's id and the signature */
    const TOKEN_PATTERN = '/^([0-9a-f]{24})\.([A-Za-z0-9_-]{43})$/';

    /** The shortest lifetime: five minutes */
    const MIN_LIFETIME = 300;

    /** When the settings do not say: one day, at most seven days, 20 links per version */
    const DEFAULT_LIFETIME = 86400;
    const MAX_LIFETIME = 604800;
    const MAX_ACTIVE = 20;

    /** How long an ended link stays in the list (then its row is removed): seven days */
    const KEEP_ENDED = 604800;

    /** The version states a link shows: a draft and a draft waiting for approval */
    const SHOWN_STATES = array( 0, 2 );

    // ------------------------------------------------------------------------------------------------ pure

    /**
     * The signature of a link (base64url of HMAC-SHA256).
     *
     * @param string $key
     * @param array $link id, object, version, language, expires
     * @return string
     */
    public static function sign( $key, array $link )
    {
        $data = implode( '|', array( 'exp-preview-link', (string)$link['id'], (int)$link['object'], (int)$link['version'],
                                     (string)$link['language'], (int)$link['expires'] ) );
        return rtrim( strtr( base64_encode( hash_hmac( 'sha256', $data, (string)$key, true ) ), '+/', '-_' ), '=' );
    }

    /**
     * @param string $key
     * @param array $link
     * @return string the link's token
     */
    public static function token( $key, array $link )
    {
        return (string)$link['id'] . '.' . self::sign( $key, $link );
    }

    /**
     * @param string $token
     * @return array|null id and signature of a token of the right form, null for anything else
     */
    public static function parse( $token )
    {
        if ( !is_string( $token ) || strlen( $token ) > 80 || !preg_match( self::TOKEN_PATTERN, $token, $m ) )
            return null;
        return array( 'id' => $m[1], 'signature' => $m[2] );
    }

    /**
     * @param string $key
     * @param array $link the stored link
     * @param string $signature the token's
     * @return bool the signature is the link's
     */
    public static function signatureMatches( $key, array $link, $signature )
    {
        return is_string( $key ) && $key !== '' && hash_equals( self::sign( $key, $link ), (string)$signature );
    }

    /**
     * @param int|null $asked seconds (null: the default)
     * @param array $settings defaultLifetime, maxLifetime
     * @return int between five minutes and the longest lifetime
     */
    public static function lifetime( $asked, array $settings )
    {
        $max = max( self::MIN_LIFETIME, (int)$settings['maxLifetime'] );
        $seconds = $asked === null ? (int)$settings['defaultLifetime'] : (int)$asked;
        return max( self::MIN_LIFETIME, min( $seconds, $max ) );
    }

    /**
     * @param array $link
     * @param int $now
     * @return string active, expired or revoked
     */
    public static function state( array $link, $now )
    {
        if ( !empty( $link['revoked'] ) )
            return 'revoked';
        return (int)$link['expires'] <= (int)$now ? 'expired' : 'active';
    }

    /**
     * @param array $link
     * @param int $now
     * @return bool an ended link whose row can go (KEEP_ENDED after it ended)
     */
    public static function prunable( array $link, $now )
    {
        $ended = !empty( $link['revoked'] ) ? (int)$link['revoked'] : (int)$link['expires'];
        return self::state( $link, $now ) !== 'active' && $ended + self::KEEP_ENDED < (int)$now;
    }

    /**
     * @param string $json a row's value
     * @return array|null the link, null for anything that is not one
     */
    public static function decode( $json )
    {
        $data = json_decode( (string)$json, true );
        if ( !is_array( $data ) || !isset( $data['id'], $data['object'], $data['version'], $data['language'], $data['expires'] )
             || !preg_match( '/^[0-9a-f]{24}$/', (string)$data['id'] ) )
            return null;
        return array(
            'id' => (string)$data['id'], 'object' => (int)$data['object'], 'version' => (int)$data['version'],
            'language' => (string)$data['language'], 'expires' => (int)$data['expires'],
            'created' => isset( $data['created'] ) ? (int)$data['created'] : 0,
            'creator' => isset( $data['creator'] ) ? (int)$data['creator'] : 0,
            'note' => isset( $data['note'] ) ? (string)$data['note'] : '',
            'revoked' => isset( $data['revoked'] ) ? (int)$data['revoked'] : 0,
            'revoker' => isset( $data['revoker'] ) ? (int)$data['revoker'] : 0,
            'opened' => isset( $data['opened'] ) ? (int)$data['opened'] : 0,
            'lastOpened' => isset( $data['lastOpened'] ) ? (int)$data['lastOpened'] : 0,
        );
    }

    /**
     * @return array enabled, defaultLifetime, maxLifetime, maxActive (content.ini [PreviewLinkSettings])
     */
    public static function settings()
    {
        $ini = eZINI::instance( 'content.ini' );
        $get = function ( $name, $default ) use ( $ini ) {
            return $ini->hasVariable( 'PreviewLinkSettings', $name ) ? $ini->variable( 'PreviewLinkSettings', $name ) : $default;
        };
        return array(
            'enabled' => !in_array( strtolower( (string)$get( 'Enabled', 'true' ) ), array( 'false', 'disabled', '0' ), true ),
            'defaultLifetime' => (int)$get( 'DefaultLifetime', self::DEFAULT_LIFETIME ),
            'maxLifetime' => (int)$get( 'MaxLifetime', self::MAX_LIFETIME ),
            'maxActive' => max( 1, (int)$get( 'MaxActivePerVersion', self::MAX_ACTIVE ) ),
        );
    }

    // ------------------------------------------------------------------------------------------------ the key

    /**
     * @return bool links can be made here: switched on and a secret store to keep the key in
     */
    public static function available()
    {
        $settings = self::settings();
        return $settings['enabled'] && class_exists( 'expSecrets' ) && expSecrets::installed() && class_exists( 'expSecretKey' )
               && expSecretKey::supported();
    }

    /**
     * @param bool $create make the key when there is none (the first link)
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
                             'Signs the share links to draft previews (content/sharedpreview). Removing it ends every link.' );
            $key = expSecrets::get( self::SECRET );
        }
        return is_string( $key ) && $key !== '' ? $key : null;
    }

    // ------------------------------------------------------------------------------------------------ links

    /**
     * @param eZContentObjectVersion $version
     * @return bool the current user may share this version: versionread on it and edit on its object
     */
    public static function mayShare( eZContentObjectVersion $version )
    {
        $object = $version->contentObject();
        return $object instanceof eZContentObject && (bool)$version->attribute( 'can_read' ) && (bool)$object->canEdit();
    }

    /**
     * A new link to the preview of a version.
     *
     * @param eZContentObjectVersion $version a draft or a draft waiting for approval
     * @param string $language a language of the version
     * @param int|null $lifetime seconds (null: DefaultLifetime; at most MaxLifetime)
     * @param string $note what the link is for (who it went to), at most 100 characters
     * @return array link, token, path (content/sharedpreview/<token>); or error: unavailable, not_allowed, not_draft,
     *               language, too_many
     */
    public static function create( eZContentObjectVersion $version, $language, $lifetime = null, $note = '' )
    {
        $settings = self::settings();
        if ( !self::available() )
            return array( 'error' => 'unavailable' );
        if ( !in_array( (int)$version->attribute( 'status' ), self::SHOWN_STATES, true ) )
            return array( 'error' => 'not_draft' );
        if ( !self::mayShare( $version ) )
            return array( 'error' => 'not_allowed' );
        $language = (string)$language;
        if ( !in_array( $language, array_map( 'strval', (array)$version->translationList( false, false ) ), true ) )
            return array( 'error' => 'language' );
        $objectID = (int)$version->attribute( 'contentobject_id' );
        $now = time();
        $active = 0;
        foreach ( self::listFor( $objectID ) as $link )
            if ( $link['version'] === (int)$version->attribute( 'version' ) && $link['state'] === 'active' )
                $active++;
        if ( $active >= $settings['maxActive'] )
            return array( 'error' => 'too_many' );
        $key = self::key( true );
        if ( $key === null )
            return array( 'error' => 'unavailable' );
        $link = array( 'id' => bin2hex( random_bytes( 12 ) ), 'object' => $objectID, 'version' => (int)$version->attribute( 'version' ),
                       'language' => $language, 'expires' => $now + self::lifetime( $lifetime, $settings ), 'created' => $now,
                       'creator' => (int)eZUser::currentUserID(), 'note' => mb_substr( trim( (string)$note ), 0, 100 ),
                       'revoked' => 0, 'revoker' => 0, 'opened' => 0, 'lastOpened' => 0 );
        self::store( $link );
        self::audit( 'content.previewlink.create', $link, array( 'after' => array( 'version' => $link['version'], 'language' => $language,
                                                                                    'expires' => $link['expires'], 'note' => $link['note'] ) ) );
        $token = self::token( $key, $link );
        return array( 'link' => $link + array( 'state' => 'active' ), 'token' => $token, 'path' => 'content/sharedpreview/' . $token );
    }

    /**
     * The links of an object, newest first; ended links are kept seven days, then removed.
     *
     * @param int $objectID
     * @return array[] each link with its state (active, expired, revoked)
     */
    public static function listFor( $objectID )
    {
        $now = time();
        $out = array();
        foreach ( self::rows() as $row )
        {
            $link = self::decode( (string)$row->attribute( 'value' ) );
            if ( $link === null )
                continue;
            if ( self::prunable( $link, $now ) )
            {
                $row->remove();
                continue;
            }
            if ( $link['object'] !== (int)$objectID )
                continue;
            $out[] = $link + array( 'state' => self::state( $link, $now ) );
        }
        usort( $out, function ( $a, $b ) { return $b['created'] - $a['created']; } );
        return $out;
    }

    /**
     * The token of an active link again (to copy it once more), for a user who made the link or may share its
     * version. Anybody else gets null: a token opens the draft without signing in, so listing the links of an
     * object must not hand it to a user who could not have created it.
     *
     * @param array $link
     * @return string|null
     */
    public static function tokenFor( array $link )
    {
        $key = self::key();
        if ( $key === null || self::state( $link, time() ) !== 'active' )
            return null;
        if ( (int)( $link['creator'] ?? 0 ) !== (int)eZUser::currentUserID() )
        {
            $object = eZContentObject::fetch( (int)( $link['object'] ?? 0 ) );
            $version = $object instanceof eZContentObject ? $object->version( (int)( $link['version'] ?? 0 ) ) : null;
            if ( !( $version instanceof eZContentObjectVersion && self::mayShare( $version ) ) )
                return null;
        }
        return self::token( $key, $link );
    }

    /**
     * Ends a link at once, for a user who may share its version or made it.
     *
     * @param string $id
     * @return bool
     */
    public static function revoke( $id )
    {
        $link = self::fetch( (string)$id );
        if ( $link === null || !empty( $link['revoked'] ) )
            return false;
        $object = eZContentObject::fetch( $link['object'] );
        $version = $object instanceof eZContentObject ? $object->version( $link['version'] ) : null;
        $userID = (int)eZUser::currentUserID();
        if ( $link['creator'] !== $userID && !( $version instanceof eZContentObjectVersion && self::mayShare( $version ) ) )
            return false;
        $link['revoked'] = time();
        $link['revoker'] = $userID;
        self::store( $link );
        self::audit( 'content.previewlink.revoke', $link, array( 'before' => array( 'expires' => $link['expires'] ), 'after' => array( 'revoked' => true ) ) );
        return true;
    }

    /**
     * What content/sharedpreview shows for a token: the link with its object and version, or why not.
     *
     * @param string $token
     * @return array link, object, version; or error: invalid (no such link, or the signature is not its), expired,
     *               revoked, gone (the version is no draft any more, or its maker is gone)
     */
    public static function open( $token )
    {
        $parts = self::parse( $token );
        if ( $parts === null )
            return array( 'error' => 'invalid' );
        $link = self::fetch( $parts['id'] );
        $key = self::key();
        if ( $link === null || $key === null || !self::signatureMatches( $key, $link, $parts['signature'] ) )
            return array( 'error' => 'invalid' );
        $state = self::state( $link, time() );
        if ( $state !== 'active' )
            return array( 'error' => $state, 'link' => $link );
        $object = eZContentObject::fetch( $link['object'] );
        $version = $object instanceof eZContentObject ? $object->version( $link['version'] ) : null;
        $creator = eZUser::fetch( $link['creator'] );
        if ( !$version instanceof eZContentObjectVersion || !in_array( (int)$version->attribute( 'status' ), self::SHOWN_STATES, true )
             || !$creator instanceof eZUser || !$creator->isEnabled() )
            return array( 'error' => 'gone', 'link' => $link );
        $link['opened']++;
        $link['lastOpened'] = time();
        self::store( $link );
        self::audit( 'content.previewlink.open', $link, array( 'after' => array( 'opened' => $link['opened'] ) ) );
        return array( 'link' => $link, 'object' => $object, 'version' => $version );
    }

    // ------------------------------------------------------------------------------------------------ storage

    /** @return eZSiteData[] */
    protected static function rows()
    {
        return eZSiteData::fetchList( self::ROW_PREFIX . '*', 0, 5000 );
    }

    /**
     * @param string $id
     * @return array|null
     */
    protected static function fetch( $id )
    {
        if ( !preg_match( '/^[0-9a-f]{24}$/', (string)$id ) )
            return null;
        $row = eZSiteData::fetchByName( self::ROW_PREFIX . $id );
        $link = $row instanceof eZSiteData ? self::decode( (string)$row->attribute( 'value' ) ) : null;
        return $link !== null && $link['id'] === $id ? $link : null;
    }

    /**
     * @param array $link
     * @return void
     */
    protected static function store( array $link )
    {
        unset( $link['state'] );
        $name = self::ROW_PREFIX . $link['id'];
        $row = eZSiteData::fetchByName( $name );
        if ( !$row instanceof eZSiteData )
            $row = eZSiteData::create( $name, '' );
        $row->setAttribute( 'value', json_encode( $link ) );
        $row->store();
    }

    /**
     * @param string $event
     * @param array $link
     * @param array $data
     * @return void
     */
    protected static function audit( $event, array $link, array $data )
    {
        if ( !class_exists( 'expAuditHook' ) )
            return;
        expAuditHook::emit( $event, array( 'object' => array( 'type' => 'version', 'id' => (string)$link['version'], 'object_id' => $link['object'],
                                                               'name' => 'preview link ' . substr( $link['id'], 0, 6 ) ) ) + $data );
    }
}

?>
