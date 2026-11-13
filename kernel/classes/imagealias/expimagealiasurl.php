<?php
/**
 * File containing the expImageAliasUrl class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The URLs of on-demand image aliases, their signatures, and the alias a template is given before it is made.
 *
 * URL forms (image.ini [AliasSettings] OnDemandURL; every form is understood whatever the setting):
 *
 *   token      content/image/<token>/<file name>
 *   ids-token  content/image/<attribute>/<version>/<language>/<alias>/<token>/<file name>
 *   ids        content/image/<attribute>/<version>/<language>/<alias>/<file name>
 *   node       content/image/<node>/<attribute identifier>/<alias>
 *   storage    the plain storage path (var/<site>/storage/images/.../<attribute>-<version>-<language>/<file name>)
 *
 * The token is base64url(JSON payload) "." base64url(HMAC-SHA256 of the payload part, first 16 bytes). The payload:
 * a (attribute id), v (version), l (language), n (alias), f (format, only when one), o (storage path of the original),
 * u (the alias's file name, in the original's directory), w/h (computed size), m (MIME type), x (expiry, drafts and
 * previews only). The key is the secret image_alias_signing (Setup > Secrets, made on first use); after a change of
 * the key (rotateKey()) the previous one, image_alias_signing_previous, stays valid for [AliasSettings]
 * SigningKeyGrace seconds.
 *
 * A valid signature is the permission to read the image and to make the alias; an unsigned or wrongly signed URL only
 * serves an alias that exists.
 *
 * The alias a template gets in on-demand mode (computedEntry()) has the file name, MIME type, width and height the
 * conversion will give: the file name and type from eZImageManager::aliasTargetPlan() (the same choices as
 * convert()), the size from the geometry filters, the focal point and the crop rectangle (expImageFocalPoint::plan()),
 * through the aliases it is made from.
 *
 * @since 6.0.15
 * @package kernel
 */
class expImageAliasUrl
{
    /** The secret that signs the URLs */
    const SECRET = 'image_alias_signing';

    /** The key before the last change, "<valid until>:<key>" */
    const SECRET_PREVIOUS = 'image_alias_signing_previous';

    /** Bytes of the HMAC kept in a token */
    const SIGNATURE_BYTES = 16;

    /** Seconds the keys are kept in the process (persistent workers) before they are read again */
    const KEY_TTL = 300;

    /** @var array|null current => string|null, previous => string|null, previous_until => int, read => int */
    protected static $keys = null;

    /** @var bool keys set by setKeys() (tests): never read from the store */
    protected static $fixedKeys = false;

    // ------------------------------------------------------------------------------------------------ keys

    /**
     * Sets the keys for this process (tests, tools); never read from or written to the secret store.
     *
     * @param string $current
     * @param string|null $previous
     * @param int $previousUntil unix time the previous key is accepted until
     */
    public static function setKeys( $current, $previous = null, $previousUntil = 0 )
    {
        self::$keys = array( 'current' => (string)$current === '' ? null : (string)$current,
                             'previous' => $previous === null || $previous === '' ? null : (string)$previous,
                             'previous_until' => (int)$previousUntil, 'read' => time() );
        self::$fixedKeys = true;
    }

    /**
     * Forgets the keys of this process: they are read again from the secret store.
     */
    public static function resetKeys()
    {
        self::$keys = null;
        self::$fixedKeys = false;
    }

    /**
     * The signing keys.
     *
     * @param bool $create make the key when there is none (the first URL)
     * @return array current => string|null, previous => string|null, previous_until => int
     */
    public static function keys( $create = false )
    {
        if ( self::$fixedKeys )
            return self::$keys;
        if ( self::$keys !== null && time() - self::$keys['read'] < self::KEY_TTL && ( self::$keys['current'] !== null || !$create ) )
            return self::$keys;
        $current = null;
        $previous = null;
        $previousUntil = 0;
        if ( class_exists( 'expSecrets' ) )
        {
            try
            {
                $current = expSecrets::get( self::SECRET );
                if ( ( $current === null || $current === '' ) && $create && self::storeAvailable() )
                {
                    expSecrets::set( self::SECRET, bin2hex( random_bytes( 32 ) ), 'image',
                                     'Signs the URLs of image aliases that are made on demand (content/image). Changing it ends the old URLs after the grace period.' );
                    $current = expSecrets::get( self::SECRET );
                }
                $previousRaw = expSecrets::get( self::SECRET_PREVIOUS );
                if ( is_string( $previousRaw ) && preg_match( '/^(\d+):(.+)$/s', $previousRaw, $match ) )
                {
                    $previousUntil = (int)$match[1];
                    $previous = $match[2];
                }
            }
            catch ( Throwable $e )
            {
                eZDebug::writeError( 'The image alias signing key could not be read: ' . $e->getMessage(), __METHOD__ );
            }
        }
        self::$keys = array( 'current' => is_string( $current ) && $current !== '' ? $current : null,
                             'previous' => $previous, 'previous_until' => $previousUntil, 'read' => time() );
        return self::$keys;
    }

    /**
     * Changes the signing key: the current key becomes the previous one, accepted for SigningKeyGrace seconds more.
     *
     * @return bool
     */
    public static function rotateKey()
    {
        if ( !self::storeAvailable() )
            return false;
        $keys = self::keys( true );
        if ( $keys['current'] === null )
            return false;
        $until = time() + expImageAliasSettings::signingKeyGrace();
        $ok = expSecrets::set( self::SECRET_PREVIOUS, $until . ':' . $keys['current'], 'image',
                               'The image alias signing key before the last change, accepted until the time in front of it.' )
           && expSecrets::set( self::SECRET, bin2hex( random_bytes( 32 ) ), 'image' );
        self::resetKeys();
        return $ok;
    }

    /**
     * @return bool keys can be stored here
     */
    protected static function storeAvailable()
    {
        return class_exists( 'expSecrets' ) && expSecrets::installed() && class_exists( 'expSecretKey' ) && expSecretKey::supported();
    }

    // ------------------------------------------------------------------------------------------------ tokens

    /**
     * The signed token of $payload, or null without a key.
     *
     * @param array $payload
     * @return string|null
     */
    public static function token( array $payload )
    {
        $keys = self::keys( true );
        if ( $keys['current'] === null )
            return null;
        $part = self::base64UrlEncode( json_encode( $payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
        return $part . '.' . self::signature( $part, $keys['current'] );
    }

    /**
     * Checks a token.
     *
     * @param string $token
     * @param int|null $now
     * @return array valid => bool, reason => ok|malformed|signature|expired, payload => array|null (also for a token
     *               whose signature is wrong: only to size a placeholder, never trusted)
     */
    public static function verify( $token, $now = null )
    {
        $now = $now === null ? time() : (int)$now;
        $result = array( 'valid' => false, 'reason' => 'malformed', 'payload' => null );
        if ( !is_string( $token ) || !preg_match( '/^([A-Za-z0-9_-]{8,4096})\.([A-Za-z0-9_-]{16,64})$/', $token, $match ) )
            return $result;
        $payload = self::decodePayload( $match[1] );
        if ( $payload === null )
            return $result;
        $result['payload'] = $payload;
        $result['reason'] = 'signature';
        $keys = self::keys();
        $candidates = array();
        if ( $keys['current'] !== null )
            $candidates[] = $keys['current'];
        if ( $keys['previous'] !== null && $keys['previous_until'] >= $now )
            $candidates[] = $keys['previous'];
        $signed = false;
        foreach ( $candidates as $key )
        {
            if ( hash_equals( self::signature( $match[1], $key ), $match[2] ) )
            {
                $signed = true;
                break;
            }
        }
        if ( !$signed )
            return $result;
        if ( !empty( $payload['x'] ) && (int)$payload['x'] < $now )
        {
            $result['reason'] = 'expired';
            return $result;
        }
        $result['valid'] = true;
        $result['reason'] = 'ok';
        return $result;
    }

    /**
     * The payload of a token part, checked for its shape (not for its signature), or null.
     *
     * @param string $part
     * @return array|null
     */
    public static function decodePayload( $part )
    {
        $json = self::base64UrlDecode( (string)$part );
        if ( $json === null )
            return null;
        $payload = json_decode( $json, true );
        if ( !is_array( $payload ) )
            return null;
        foreach ( array( 'a', 'v', 'l', 'n', 'o', 'u' ) as $key )
        {
            if ( !isset( $payload[$key] ) || !is_scalar( $payload[$key] ) )
                return null;
        }
        if ( !ctype_digit( (string)$payload['a'] ) || !ctype_digit( (string)$payload['v'] )
             || !self::validLanguage( $payload['l'] ) || !self::validAliasName( $payload['n'] )
             || !self::validFileName( $payload['u'] ) || !self::validStoragePath( $payload['o'] ) )
            return null;
        return $payload;
    }

    /**
     * The storage path of the alias a payload names: the original's directory and the file name.
     *
     * @param array $payload
     * @return string
     */
    public static function targetPath( array $payload )
    {
        return dirname( (string)$payload['o'] ) . '/' . (string)$payload['u'];
    }

    /**
     * @param string $part
     * @param string $key
     * @return string
     */
    protected static function signature( $part, $key )
    {
        return self::base64UrlEncode( substr( hash_hmac( 'sha256', (string)$part, (string)$key, true ), 0, self::SIGNATURE_BYTES ) );
    }

    // ------------------------------------------------------------------------------------------------ URLs

    /**
     * The URL (without the site's www directory: templates add it with ezroot) of an alias in one of the forms.
     *
     * @param array $payload a token payload (see the class)
     * @param string $form token|ids-token|ids|node|storage
     * @param array $options node => node id, identifier => attribute identifier (node form), prefix => the access
     *                       path before content/image (default: the current siteaccess's, see accessPrefix())
     * @return string|null null when the form cannot be built (no key for a signed form, no node)
     */
    public static function build( array $payload, $form, array $options = array() )
    {
        $prefix = array_key_exists( 'prefix', $options ) ? (string)$options['prefix'] : self::accessPrefix();
        $base = $prefix . 'content/image/';
        $file = rawurlencode( (string)$payload['u'] );
        switch ( $form )
        {
            case 'storage':
                return self::targetPath( $payload );
            case 'ids':
                return $base . (int)$payload['a'] . '/' . (int)$payload['v'] . '/' . $payload['l'] . '/' . $payload['n'] . '/' . $file;
            case 'node':
                if ( empty( $options['node'] ) || empty( $options['identifier'] ) )
                    return null;
                return $base . (int)$options['node'] . '/' . rawurlencode( (string)$options['identifier'] ) . '/' . $payload['n'];
            case 'ids-token':
                $token = self::token( $payload );
                if ( $token === null )
                    return null;
                return $base . (int)$payload['a'] . '/' . (int)$payload['v'] . '/' . $payload['l'] . '/' . $payload['n'] . '/' . $token . '/' . $file;
            case 'token':
            default:
                $token = self::token( $payload );
                if ( $token === null )
                    return null;
                return $base . $token . '/' . $file;
        }
    }

    /**
     * The path in front of content/image for the current siteaccess ("" with host matching or the default
     * siteaccess, "eng/" with URI matching, "index.php/eng/" without rewrite rules).
     *
     * @return string
     */
    public static function accessPrefix()
    {
        if ( !class_exists( 'eZSys' ) )
            return '';
        $index = trim( (string)eZSys::indexFile( true ), '/' );
        return $index === '' ? '' : $index . '/';
    }

    /**
     * Reads the module view parameters of content/image into one of the forms.
     *
     * @param array $params the view's parameters in order (empty ones are left out)
     * @return array|null form => token|ids-token|ids|node, and token, file, attribute, version, language, alias, node,
     *                    identifier as the form has them; null for anything else
     */
    public static function parse( array $params )
    {
        $params = array_values( array_filter( array_map( 'strval', $params ), 'strlen' ) );
        switch ( count( $params ) )
        {
            case 2:
                if ( strpos( $params[0], '.' ) === false || !self::validFileName( rawurldecode( $params[1] ) ) )
                    return null;
                return array( 'form' => 'token', 'token' => $params[0], 'file' => rawurldecode( $params[1] ) );
            case 3:
                if ( !ctype_digit( $params[0] ) || !preg_match( '/^[A-Za-z0-9_]{1,64}$/', rawurldecode( $params[1] ) ) || !self::validAliasName( $params[2] ) )
                    return null;
                return array( 'form' => 'node', 'node' => (int)$params[0], 'identifier' => rawurldecode( $params[1] ), 'alias' => $params[2] );
            case 5:
            case 6:
                if ( !ctype_digit( $params[0] ) || !ctype_digit( $params[1] ) || !self::validLanguage( $params[2] ) || !self::validAliasName( $params[3] ) )
                    return null;
                $result = array( 'form' => count( $params ) === 6 ? 'ids-token' : 'ids', 'attribute' => (int)$params[0],
                                 'version' => (int)$params[1], 'language' => $params[2], 'alias' => $params[3] );
                $file = rawurldecode( $params[count( $params ) - 1] );
                if ( !self::validFileName( $file ) )
                    return null;
                $result['file'] = $file;
                if ( count( $params ) === 6 )
                {
                    if ( strpos( $params[4], '.' ) === false )
                        return null;
                    $result['token'] = $params[4];
                }
                return $result;
        }
        return null;
    }

    /**
     * Reads a storage path of an image alias: the attribute, version and language of its directory, the file name,
     * and the aliases the file name can be (configured aliases only, longest name first).
     *
     * @param string $path var/<site>/storage/images/.../<attribute>-<version>-<language>/<file> or
     *                     var/<site>/storage/images-versioned/<attribute>/<version>-<language>/<file>
     * @param string[] $aliasNames the configured aliases
     * @return array|null storage, versioned, attribute, version, language, directory, file, candidates =>
     *                    array of array( alias, basename, suffix )
     */
    public static function parseStoragePath( $path, array $aliasNames )
    {
        $path = ltrim( (string)$path, '/' );
        if ( !self::validStoragePath( $path ) )
            return null;
        if ( preg_match( '#^(var/(?:[^/]+/)?storage)/([A-Za-z0-9_-]+)/(.+)/(\d+)-(\d+)-([A-Za-z]{2,4}-[A-Za-z]{2,4})/([^/]+)$#', $path, $m ) )
        {
            $result = array( 'storage' => $m[1], 'versioned' => false, 'attribute' => (int)$m[4], 'version' => (int)$m[5],
                             'language' => $m[6], 'file' => $m[7] );
        }
        elseif ( preg_match( '#^(var/(?:[^/]+/)?storage)/([A-Za-z0-9_-]+)/(\d+)/(\d+)-([A-Za-z]{2,4}-[A-Za-z]{2,4})/([^/]+)$#', $path, $m ) )
        {
            $result = array( 'storage' => $m[1], 'versioned' => true, 'attribute' => (int)$m[3], 'version' => (int)$m[4],
                             'language' => $m[5], 'file' => $m[6] );
        }
        else
            return null;
        $result['directory'] = dirname( $path );
        $dot = strrpos( $result['file'], '.' );
        if ( $dot === false )
            return null;
        $stem = substr( $result['file'], 0, $dot );
        $suffix = substr( $result['file'], $dot + 1 );
        usort( $aliasNames, function ( $a, $b ) { return strlen( $b ) - strlen( $a ); } );
        $candidates = array();
        foreach ( $aliasNames as $alias )
        {
            if ( $alias === 'original' || !self::validAliasName( $alias ) )
                continue;
            $tail = '_' . $alias;
            if ( strlen( $stem ) > strlen( $tail ) && substr( $stem, -strlen( $tail ) ) === $tail )
                $candidates[] = array( 'alias' => $alias, 'basename' => substr( $stem, 0, -strlen( $tail ) ), 'suffix' => $suffix );
        }
        $result['candidates'] = $candidates;
        return $result;
    }

    // ------------------------------------------------------------------------------------------------ computed aliases

    /**
     * The alias $aliasName as a template gets it in on-demand mode, without converting: URL in the configured form,
     * the storage path it will have (full_path), file name, MIME type and size as the conversion will give them.
     *
     * @param string $aliasName
     * @param array $aliasList the image's alias list (eZImageAliasHandler::aliasList(): original and the aliases made)
     * @param array $focalData focal => array|null, crop => array|null
     * @param array $attribute id, version, language_code, contentobject_id, contentclass_attribute_identifier
     * @param array $options form => URL form (default: the setting), node => node id (node form), now => time
     * @return array|null
     */
    public static function computedEntry( $aliasName, array $aliasList, array $focalData, array $attribute, array $options = array() )
    {
        if ( !isset( $aliasList['original'] ) || (string)( $aliasList['original']['url'] ?? '' ) === '' )
            return null;
        $original = $aliasList['original'];
        $target = self::computeTarget( $aliasName, $original, $focalData, $aliasList );
        if ( $target === null )
            return null;
        $imageManager = eZImageManager::factory();
        $mime = $target['mime'];
        $targetPath = $mime['url'];
        $now = isset( $options['now'] ) ? (int)$options['now'] : time();
        $payload = array( 'a' => (int)$attribute['id'], 'v' => (int)$attribute['version'], 'l' => (string)$attribute['language_code'],
                          'n' => (string)$aliasName, 'o' => (string)$original['url'], 'u' => basename( $targetPath ) );
        if ( $target['width'] )
        {
            $payload['w'] = (int)$target['width'];
            $payload['h'] = (int)$target['height'];
        }
        $payload['m'] = (string)$mime['name'];
        // The file there is outdated: made again unless it is newer than this
        if ( !empty( $options['outdated'] ) )
            $payload['r'] = $now;
        if ( self::isVersionedPath( $targetPath ) )
            $payload['x'] = $now + expImageAliasSettings::draftTokenLifetime();
        // The alias must lie next to the original (the token names its directory); otherwise the storage path
        if ( dirname( $targetPath ) !== dirname( (string)$original['url'] ) || !self::validFileName( $payload['u'] ) )
            return null;

        $form = isset( $options['form'] ) ? $options['form'] : expImageAliasSettings::urlForm();
        $buildOptions = array();
        if ( $form === 'node' )
        {
            $buildOptions['node'] = isset( $options['node'] ) ? $options['node'] : null;
            $buildOptions['identifier'] = isset( $attribute['contentclass_attribute_identifier'] ) ? $attribute['contentclass_attribute_identifier'] : null;
        }
        if ( array_key_exists( 'prefix', $options ) )
            $buildOptions['prefix'] = $options['prefix'];
        $url = self::build( $payload, $form, $buildOptions );
        // A signed form without a key (no secret store) or a node form without a node: the storage path, which
        // index_image.php can still make
        if ( $url === null )
            $url = $targetPath;

        $aliasDefinition = $imageManager->alias( $aliasName );
        return array(
            'name' => $aliasName,
            'width' => $target['width'],
            'height' => $target['height'],
            'mime_type' => $mime['name'],
            'filename' => $mime['filename'],
            'suffix' => $mime['suffix'],
            'dirpath' => $mime['dirpath'],
            'basename' => $original['basename'],
            'alternative_text' => $original['alternative_text'] ?? '',
            'text' => $original['text'] ?? '',
            'original_filename' => $original['original_filename'] ?? '',
            'url' => $url,
            'full_path' => $targetPath,
            'storage_url' => $targetPath,
            'alias_key' => $aliasDefinition ? $aliasDefinition['alias_key'] : '',
            'timestamp' => $original['timestamp'] ?? '',
            'is_valid' => true,
            'is_new' => false,
            'filesize' => false,
            'info' => false,
            'on_demand' => true,
        );
    }

    /**
     * An alias that exists, as a template gets it in on-demand mode when its block lists formats to negotiate
     * (Formats[], Negotiate): the entry as it is, with the signed content/image URL in place of the storage URL, so
     * the browser is answered in the format it accepts. full_path and storage_url keep the file.
     *
     * Only the signed forms (token, ids-token) negotiate: the unsigned ones never make a variant, and the storage form
     * is the web server's.
     *
     * @param array $entry the alias's entry of the alias list
     * @param array $original the original's entry
     * @param array $attribute id, version, language_code
     * @param array $options form => URL form (default: the setting), prefix => access path, now => time
     * @return array|null null when the entry stays as it is
     */
    public static function negotiatedEntry( array $entry, array $original, array $attribute, array $options = array() )
    {
        $form = isset( $options['form'] ) ? $options['form'] : expImageAliasSettings::urlForm();
        if ( !in_array( $form, array( 'token', 'ids-token' ), true ) )
            return null;
        $path = (string)( $entry['url'] ?? '' );
        $name = (string)( $entry['name'] ?? '' );
        if ( $path === '' || $name === '' || $name === 'original' || empty( $entry['is_valid'] ) || !self::validStoragePath( $path )
             || dirname( $path ) !== dirname( (string)( $original['url'] ?? '' ) ) || !self::validFileName( basename( $path ) ) )
            return null;
        $payload = array( 'a' => (int)$attribute['id'], 'v' => (int)$attribute['version'], 'l' => (string)$attribute['language_code'],
                          'n' => $name, 'o' => (string)$original['url'], 'u' => basename( $path ) );
        if ( !empty( $entry['width'] ) && !empty( $entry['height'] ) )
        {
            $payload['w'] = (int)$entry['width'];
            $payload['h'] = (int)$entry['height'];
        }
        if ( (string)( $entry['mime_type'] ?? '' ) !== '' )
            $payload['m'] = (string)$entry['mime_type'];
        if ( self::isVersionedPath( $path ) )
            $payload['x'] = ( isset( $options['now'] ) ? (int)$options['now'] : time() ) + expImageAliasSettings::draftTokenLifetime();
        $buildOptions = array_key_exists( 'prefix', $options ) ? array( 'prefix' => $options['prefix'] ) : array();
        $url = self::build( $payload, $form, $buildOptions );
        if ( $url === null )
            return null;
        $entry['url'] = $url;
        $entry['full_path'] = $path;
        $entry['storage_url'] = $path;
        $entry['negotiated'] = true;
        return $entry;
    }

    /**
     * The alias file $aliasName will be made as, and its size, through the aliases it is made from.
     *
     * @param string $aliasName
     * @param array $original the original's entry (url, basename, width, height, info)
     * @param array $focalData focal, crop
     * @param array $aliasList the aliases made so far: one of them that an alias is made from is used as it is
     * @return array|null mime => MIME structure of the file, width, height (false when not known), filters
     */
    public static function computeTarget( $aliasName, array $original, array $focalData, array $aliasList = array() )
    {
        $imageManager = eZImageManager::factory();
        $definitions = $imageManager->aliasList();
        if ( $aliasName === 'original' || !isset( $definitions[$aliasName] ) )
            return null;
        $chain = array();
        // As createImageAlias(): a reference that is not defined is replaced by the original
        foreach ( expImageFocalPoint::referenceChain( $definitions, $aliasName ) as $name )
            $chain[] = $name;
        $chain[] = $aliasName;

        $source = (string)$original['url'];
        $width = (int)( $original['width'] ?? 0 );
        $height = (int)( $original['height'] ?? 0 );
        $size = $width > 0 && $height > 0 ? array( $width, $height ) : false;
        $sourceMime = eZMimeType::findByFileContents( $source );
        $info = isset( $original['info'] ) && is_array( $original['info'] ) ? $original['info'] : null;
        $hasFocal = !empty( $focalData['focal'] ) || !empty( $focalData['crop'] );
        $result = null;
        foreach ( $chain as $name )
        {
            // An alias this one is made from that exists already: its file and size
            if ( $name !== $aliasName && isset( $aliasList[$name]['url'] ) && (string)$aliasList[$name]['url'] !== '' )
            {
                $source = (string)$aliasList[$name]['url'];
                $w = (int)( $aliasList[$name]['width'] ?? 0 );
                $h = (int)( $aliasList[$name]['height'] ?? 0 );
                $size = $w > 0 && $h > 0 ? array( $w, $h ) : false;
                $newMime = eZMimeType::findByFileContents( $source );
                if ( $newMime['name'] !== $sourceMime['name'] )
                    $info = null;
                $sourceMime = $newMime;
                continue;
            }
            $parameters = array( 'basename' => (string)$original['basename'] );
            if ( $info !== null )
                $parameters['source_info'] = $info;
            if ( $size )
                $parameters['source_size'] = $size;
            if ( $hasFocal )
                $parameters['focal'] = array( 'focal' => $focalData['focal'] ?? null, 'crop' => $focalData['crop'] ?? null,
                                              'width' => $width, 'height' => $height );
            $plan = $imageManager->aliasTargetPlan( $name, $source, $parameters );
            if ( !$plan )
                return null;
            $newSize = $size ? self::stepsSize( $plan['steps'], $size ) : false;
            if ( $plan['mime']['name'] !== $sourceMime['name'] )
                $info = null;
            $sourceMime = $plan['mime'];
            $source = (string)$plan['mime']['url'];
            $size = $newSize;
            $result = array( 'mime' => $plan['mime'], 'width' => $size ? $size[0] : false, 'height' => $size ? $size[1] : false,
                             'filters' => $plan['filters'] );
        }
        return $result;
    }

    /**
     * The size after the conversion steps of a plan (eZImageManager::aliasTargetPlan()): each filter as the handler
     * that runs it computes sizes (its filterResultSize(): GD truncates, ImageMagick rounds); a handler without that
     * method is followed with expImageFocalPoint::plan(). False when a filter's result size is not known.
     *
     * @param array $steps handler, filters
     * @param int[] $size array( width, height ) before
     * @return int[]|false
     */
    public static function stepsSize( array $steps, array $size )
    {
        list( $width, $height ) = array_values( $size );
        foreach ( $steps as $step )
        {
            foreach ( (array)$step['filters'] as $filter )
            {
                if ( is_object( $step['handler'] ) && method_exists( $step['handler'], 'filterResultSize' ) )
                    $result = $step['handler']->filterResultSize( $filter, $width, $height );
                else
                {
                    $plan = expImageFocalPoint::plan( array( $filter ), $width, $height, null, null );
                    $result = $plan['width'] === false ? null : array( $plan['width'], $plan['height'] );
                }
                if ( !is_array( $result ) || $result[0] <= 0 || $result[1] <= 0 )
                    return false;
                list( $width, $height ) = $result;
            }
        }
        return array( (int)$width, (int)$height );
    }

    // ------------------------------------------------------------------------------------------------ checks

    /**
     * @param string $path
     * @return bool the path lies in the versioned images (drafts and previews)
     */
    public static function isVersionedPath( $path )
    {
        $versioned = 'images-versioned';
        if ( class_exists( 'eZINI' ) )
        {
            $ini = eZINI::instance( 'image.ini' );
            if ( $ini->hasVariable( 'FileSettings', 'VersionedImages' ) )
                $versioned = (string)$ini->variable( 'FileSettings', 'VersionedImages' );
        }
        return strpos( '/' . ltrim( (string)$path, '/' ), '/storage/' . $versioned . '/' ) !== false;
    }

    /**
     * @param mixed $name
     * @return bool
     */
    public static function validAliasName( $name )
    {
        return is_string( $name ) && preg_match( '/^[A-Za-z0-9_-]{1,64}$/', $name ) === 1;
    }

    /**
     * @param mixed $language
     * @return bool
     */
    public static function validLanguage( $language )
    {
        return is_string( $language ) && preg_match( '/^[A-Za-z]{2,4}-[A-Za-z]{2,4}$/', $language ) === 1;
    }

    /**
     * A file name of one path segment: no slash, no NUL, not . or .., at most 255 bytes.
     *
     * @param mixed $file
     * @return bool
     */
    public static function validFileName( $file )
    {
        return is_string( $file ) && $file !== '' && $file !== '.' && $file !== '..' && strlen( $file ) <= 255
               && strpbrk( $file, "/\\\0" ) === false && preg_match( '/[\x00-\x1F\x7F]/', $file ) !== 1;
    }

    /**
     * A relative path in a storage directory, without . or .. segments.
     *
     * @param mixed $path
     * @return bool
     */
    public static function validStoragePath( $path )
    {
        if ( !is_string( $path ) || $path === '' || strlen( $path ) > 4096 || $path[0] === '/'
             || strpos( $path, "\0" ) !== false || strpos( $path, '\\' ) !== false || preg_match( '/[\x00-\x1F\x7F]/', $path ) )
            return false;
        if ( !preg_match( '#^var/(?:[^/]+/)?storage/#', $path ) )
            return false;
        foreach ( explode( '/', $path ) as $segment )
        {
            if ( $segment === '' || $segment === '.' || $segment === '..' )
                return false;
        }
        return true;
    }

    /**
     * @param string $data
     * @return string
     */
    public static function base64UrlEncode( $data )
    {
        return rtrim( strtr( base64_encode( (string)$data ), '+/', '-_' ), '=' );
    }

    /**
     * @param string $text
     * @return string|null
     */
    public static function base64UrlDecode( $text )
    {
        if ( !preg_match( '/^[A-Za-z0-9_-]*$/', (string)$text ) )
            return null;
        $decoded = base64_decode( strtr( (string)$text, '-_', '+/' ), true );
        return $decoded === false ? null : $decoded;
    }
}
