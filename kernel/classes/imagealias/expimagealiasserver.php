<?php
/**
 * File containing the expImageAliasServer class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Answers one request for an image alias: the view content/image (handleParams()) and index_image.php, the entry
 * script the web server hands a missing storage file to (handleStoragePath()).
 *
 * 1. Verify: a signed URL (expImageAliasUrl) is the permission to read and to make the alias; an unsigned or wrongly
 *    signed one only serves an alias that exists.
 * 2. Serve, or make: an alias that exists is answered at once (no database for a signed URL on a single server).
 *    A missing one is made within [AliasSettings] MaxWait seconds: the attribute is loaded and its render-mode
 *    eZImageAliasHandler::imageAlias() makes that one alias, so the file and the database writes (the alias list of
 *    the attribute, ezimagefile) are exactly those of render mode.
 * 3. Answer: the file (a redirect to the storage URL, or the file itself; [AliasSettings] Delivery) with long-lived
 *    cache headers, or the placeholder (expImagePlaceholder) with no-store: 200 while a signed image is still being
 *    made, 404 for a refused or unknown one.
 *
 * The decisions return a response array (status, headers, body or file) and emit() sends it, so they can be tested
 * without a web server.
 *
 * @since 6.0.15
 * @package kernel
 */
class expImageAliasServer
{
    /** @var float when the request began (for MaxWait) */
    protected $start;

    /** @var bool the request came for a storage path (index_image.php): never answered with a redirect to itself */
    protected $storageRequest = false;

    public function __construct()
    {
        $this->start = microtime( true );
    }

    // ------------------------------------------------------------------------------------------------ entry points

    /**
     * The view content/image: its module view parameters, in order.
     *
     * @param array $params
     * @return array the response
     */
    public function handleParams( array $params )
    {
        $parsed = expImageAliasUrl::parse( $params );
        if ( $parsed === null )
            return $this->placeholder( null, null, 404, 'unknown' );
        switch ( $parsed['form'] )
        {
            case 'token':
            case 'ids-token':
                return $this->handleSigned( $parsed );
            case 'ids':
                return $this->handleIds( $parsed );
            case 'node':
                return $this->handleNode( $parsed );
        }
        return $this->placeholder( null, null, 404, 'unknown' );
    }

    /**
     * A storage path the web server did not find (index_image.php): served when it is there now, made when it names
     * a configured alias of the published version of a stored image, else the placeholder with 404.
     *
     * @param string $path the request path (with or without the leading slash and the site's www directory)
     * @return array the response
     */
    public function handleStoragePath( $path )
    {
        $this->storageRequest = true;
        $path = self::normalizeRequestPath( $path );
        if ( !expImageAliasUrl::validStoragePath( $path ) )
            return $this->placeholder( null, null, 404, 'not-storage' );
        if ( self::exists( $path ) )
            return $this->deliver( $path, null );

        $aliasNames = array_keys( eZImageManager::factory()->aliasList() );
        $parsed = expImageAliasUrl::parseStoragePath( $path, $aliasNames );
        if ( $parsed === null || !$parsed['candidates'] || $parsed['versioned'] )
            return $this->placeholder( null, null, 404, $parsed !== null && $parsed['versioned'] ? 'versioned' : 'unknown' );

        $attribute = $this->publishedImageAttribute( $parsed['attribute'], $parsed['version'], $parsed['language'] );
        if ( $attribute === null )
            return $this->placeholder( null, null, 404, 'unpublished' );
        $handler = $attribute->attribute( 'content' );
        $aliasList = $handler instanceof eZImageAliasHandler ? $handler->aliasList() : array();
        $original = isset( $aliasList['original'] ) ? $aliasList['original'] : null;
        if ( !$original || (string)$original['url'] === '' || dirname( (string)$original['url'] ) !== $parsed['directory'] )
            return $this->placeholder( null, null, 404, 'other-image' );
        foreach ( $parsed['candidates'] as $candidate )
        {
            if ( $candidate['basename'] !== (string)$original['basename'] )
                continue;
            $made = $this->generate( $attribute, $candidate['alias'], $path );
            if ( self::exists( $path ) )
                return $this->deliver( $path, null );
            if ( $made === 'timeout' )
                return $this->placeholder( null, null, 200, 'pending', $candidate['alias'], $path );
        }
        return $this->placeholder( null, null, 404, 'not-made', null, $path );
    }

    // ------------------------------------------------------------------------------------------------ forms

    /**
     * @param array $parsed
     * @return array
     */
    protected function handleSigned( array $parsed )
    {
        $check = expImageAliasUrl::verify( $parsed['token'] );
        $payload = $check['payload'];
        if ( $payload === null )
            return $this->placeholder( null, null, 404, 'malformed' );
        $width = isset( $payload['w'] ) ? $payload['w'] : null;
        $height = isset( $payload['h'] ) ? $payload['h'] : null;
        // The file name and, in the ids-token form, the ids of the URL must be those the token signs
        if ( $parsed['file'] !== (string)$payload['u']
             || ( $parsed['form'] === 'ids-token' && ( (int)$parsed['attribute'] !== (int)$payload['a'] || (int)$parsed['version'] !== (int)$payload['v']
                                                      || $parsed['language'] !== (string)$payload['l'] || $parsed['alias'] !== (string)$payload['n'] ) ) )
            return $this->placeholder( $width, $height, 404, 'mismatch' );

        $target = expImageAliasUrl::targetPath( $payload );
        $mime = isset( $payload['m'] ) ? (string)$payload['m'] : null;
        // r: the file there was outdated when the page was made (other alias settings); a valid URL makes it again
        // unless it was made since
        $outdatedBefore = isset( $payload['r'] ) ? (int)$payload['r'] : 0;
        if ( self::exists( $target ) && ( !$check['valid'] || $outdatedBefore === 0 || self::modified( $target ) >= $outdatedBefore ) )
            return $this->deliver( $target, $mime );
        if ( !$check['valid'] )
            return $this->placeholder( $width, $height, 404, $check['reason'], (string)$payload['n'], $target );

        $attribute = eZContentObjectAttribute::fetch( (int)$payload['a'], (int)$payload['v'] );
        if ( !$attribute instanceof eZContentObjectAttribute || $attribute->attribute( 'language_code' ) !== (string)$payload['l']
             || $attribute->attribute( 'data_type_string' ) !== 'ezimage' )
            return $this->placeholder( $width, $height, 404, 'gone', (string)$payload['n'], $target );
        $made = $this->generate( $attribute, (string)$payload['n'], $target );
        if ( self::exists( $target ) && ( $made === 'ok' || $outdatedBefore === 0 || self::modified( $target ) >= $outdatedBefore ) )
            return $this->deliver( $target, $mime );
        // Still being made by another process: the browser asks again (swap script)
        if ( $made === 'timeout' )
            return $this->placeholder( $width, $height, 200, 'pending', (string)$payload['n'], $target );
        return $this->placeholder( $width, $height, 404, 'not-made', (string)$payload['n'], $target );
    }

    /**
     * The unsigned ids form: an alias of the published version that exists.
     *
     * @param array $parsed
     * @return array
     */
    protected function handleIds( array $parsed )
    {
        $attribute = $this->publishedImageAttribute( $parsed['attribute'], $parsed['version'], $parsed['language'] );
        if ( $attribute === null || (int)$attribute->attribute( 'version' ) !== (int)$parsed['version'] )
            return $this->placeholder( null, null, 404, 'unpublished' );
        return $this->existingAlias( $attribute, $parsed['alias'], $parsed['file'] );
    }

    /**
     * The unsigned node form: an alias of the image attribute of a visible node that exists.
     *
     * @param array $parsed
     * @return array
     */
    protected function handleNode( array $parsed )
    {
        $node = eZContentObjectTreeNode::fetch( (int)$parsed['node'] );
        if ( !$node instanceof eZContentObjectTreeNode || ( $node->attribute( 'is_invisible' ) && !eZContentObjectTreeNode::showInvisibleNodes() ) )
            return $this->placeholder( null, null, 404, 'unknown' );
        $object = $node->attribute( 'object' );
        if ( !$object instanceof eZContentObject || (int)$object->attribute( 'status' ) !== eZContentObject::STATUS_PUBLISHED )
            return $this->placeholder( null, null, 404, 'unpublished' );
        $dataMap = $object->dataMap();
        if ( !isset( $dataMap[$parsed['identifier']] ) || $dataMap[$parsed['identifier']]->attribute( 'data_type_string' ) !== 'ezimage' )
            return $this->placeholder( null, null, 404, 'unknown' );
        return $this->existingAlias( $dataMap[$parsed['identifier']], $parsed['alias'], null );
    }

    /**
     * Serves the alias $aliasName of $attribute when its file exists (and is named $file, when given).
     *
     * @param eZContentObjectAttribute $attribute
     * @param string $aliasName
     * @param string|null $file
     * @return array
     */
    protected function existingAlias( $attribute, $aliasName, $file )
    {
        $handler = $attribute->attribute( 'content' );
        if ( !$handler instanceof eZImageAliasHandler )
            return $this->placeholder( null, null, 404, 'unknown' );
        $aliasList = $handler->aliasList();
        if ( !isset( $aliasList[$aliasName] ) || (string)$aliasList[$aliasName]['url'] === ''
             || ( $file !== null && basename( (string)$aliasList[$aliasName]['url'] ) !== $file ) )
            return $this->placeholder( null, null, 404, 'unsigned-missing' );
        $path = (string)$aliasList[$aliasName]['url'];
        if ( !self::exists( $path ) )
            return $this->placeholder( $aliasList[$aliasName]['width'] ?? null, $aliasList[$aliasName]['height'] ?? null, 404, 'unsigned-missing' );
        return $this->deliver( $path, (string)$aliasList[$aliasName]['mime_type'] );
    }

    /**
     * The attribute $attributeID in $language of the published version of its object, when its image lies in the
     * directory of version $version (an image shared between versions keeps the directory of the version it was
     * uploaded in); null otherwise.
     *
     * @param int $attributeID
     * @param int $version
     * @param string $language
     * @return eZContentObjectAttribute|null
     */
    protected function publishedImageAttribute( $attributeID, $version, $language )
    {
        $named = eZContentObjectAttribute::fetch( (int)$attributeID, (int)$version );
        if ( !$named instanceof eZContentObjectAttribute || $named->attribute( 'language_code' ) !== $language
             || $named->attribute( 'data_type_string' ) !== 'ezimage' )
            return null;
        $object = eZContentObject::fetch( (int)$named->attribute( 'contentobject_id' ) );
        if ( !$object instanceof eZContentObject || (int)$object->attribute( 'status' ) !== eZContentObject::STATUS_PUBLISHED )
            return null;
        $currentVersion = (int)$object->attribute( 'current_version' );
        if ( $currentVersion === (int)$version )
            return $named;
        $published = eZContentObjectAttribute::fetch( (int)$attributeID, $currentVersion );
        return $published instanceof eZContentObjectAttribute && $published->attribute( 'language_code' ) === $language ? $published : null;
    }

    // ------------------------------------------------------------------------------------------------ making

    /**
     * Makes the alias $aliasName of $attribute as render mode does, waiting at most what is left of MaxWait for
     * another process making it.
     *
     * @param eZContentObjectAttribute $attribute
     * @param string $aliasName
     * @param string $target the file expected (for the log)
     * @return string ok|timeout|failed
     */
    protected function generate( $attribute, $aliasName, $target )
    {
        $handler = $attribute->attribute( 'content' );
        if ( !$handler instanceof eZImageAliasHandler || !eZImageManager::factory()->hasAlias( $aliasName ) )
            return 'failed';
        $left = max( 0.0, expImageAliasSettings::maxWait() - ( microtime( true ) - $this->start ) );
        $before = expImageAliasLock::setWaitLimit( $left );
        $started = microtime( true );
        $timedOut = false;
        try
        {
            $alias = expImageAliasSettings::withRender( function () use ( $handler, $aliasName ) {
                // An entry whose file is gone is made again (the XML entry is replaced)
                $handler->forgetMissingAliasFile( $aliasName );
                return $handler->imageAlias( $aliasName );
            } );
        }
        catch ( Throwable $e )
        {
            $alias = null;
            eZDebug::writeError( "Making the alias '$aliasName' of $target failed: " . $e->getMessage(), __METHOD__ );
        }
        finally
        {
            expImageAliasLock::setWaitLimit( $before );
        }
        $ms = (int)round( ( microtime( true ) - $started ) * 1000 );
        $ok = is_array( $alias ) && (string)( $alias['url'] ?? '' ) !== '';
        if ( !$ok && $ms >= (int)( $left * 1000 ) )
            $timedOut = true;
        expImageAliasLog::write( $ok ? 'generate' : 'fail', array( 'result' => $ok ? 'ok' : ( $timedOut ? 'timeout' : 'failed' ), 'ms' => $ms,
                                                                    'alias' => $aliasName, 'path' => $target ) );
        return $ok ? 'ok' : ( $timedOut ? 'timeout' : 'failed' );
    }

    // ------------------------------------------------------------------------------------------------ answers

    /**
     * The answer with an existing alias file.
     *
     * @param string $path
     * @param string|null $mime
     * @return array
     */
    public function deliver( $path, $mime )
    {
        if ( $mime === null || $mime === '' )
        {
            $mimeData = eZMimeType::findByFileContents( $path );
            $mime = $mimeData['name'];
        }
        $cacheControl = 'public, max-age=' . expImageAliasSettings::cacheMaxAge();
        if ( !$this->storageRequest && $this->deliveryMethod( $path ) === 'redirect' )
        {
            return array( 'status' => 302, 'body' => '', 'file' => null,
                          'headers' => array( 'Location' => rtrim( (string)eZSys::wwwDir(), '/' ) . '/' . self::encodePath( $path ),
                                              'Cache-Control' => $cacheControl, 'Content-Length' => '0' ) );
        }
        $headers = array( 'Content-Type' => $mime, 'Cache-Control' => $cacheControl . ', immutable',
                          'X-Content-Type-Options' => 'nosniff' );
        if ( $mime === 'image/svg+xml' )
            $headers['Content-Security-Policy'] = "sandbox; default-src 'none'; style-src 'unsafe-inline'";
        if ( is_file( $path ) )
        {
            $size = filesize( $path );
            $mtime = filemtime( $path );
            $headers['Content-Length'] = (string)$size;
            $headers['Last-Modified'] = gmdate( 'D, d M Y H:i:s', (int)$mtime ) . ' GMT';
            return array( 'status' => 200, 'headers' => $headers, 'body' => null, 'file' => $path );
        }
        // A database cluster: the file from the cluster storage
        $fileHandler = eZClusterFileHandler::instance( $path );
        $contents = $fileHandler->fetchContents();
        if ( !is_string( $contents ) )
            return $this->placeholder( null, null, 404, 'unreadable', null, $path );
        $headers['Content-Length'] = (string)strlen( $contents );
        $mtime = $fileHandler->mtime();
        if ( $mtime )
            $headers['Last-Modified'] = gmdate( 'D, d M Y H:i:s', (int)$mtime ) . ' GMT';
        return array( 'status' => 200, 'headers' => $headers, 'body' => $contents, 'file' => null );
    }

    /**
     * How an existing alias is answered: redirect or stream ([AliasSettings] Delivery; auto: the redirect where the
     * web server serves the storage, the file itself on a database cluster and for drafts).
     *
     * @param string $path
     * @return string redirect|stream
     */
    public function deliveryMethod( $path )
    {
        $setting = expImageAliasSettings::delivery();
        if ( $setting !== 'auto' )
            return $setting;
        if ( expImageAliasUrl::isVersionedPath( $path ) )
            return 'stream';
        if ( interface_exists( 'ezpDatabaseBasedClusterFileHandler' )
             && eZClusterFileHandler::instance() instanceof ezpDatabaseBasedClusterFileHandler )
            return 'stream';
        return 'redirect';
    }

    /**
     * The placeholder answer (expImagePlaceholder), logged.
     *
     * @param mixed $width
     * @param mixed $height
     * @param int $status 200 while a signed image is being made, 404 otherwise
     * @param string $reason
     * @param string|null $alias
     * @param string|null $path
     * @return array
     */
    public function placeholder( $width, $height, $status, $reason, $alias = null, $path = null )
    {
        expImageAliasLog::write( 'placeholder', array( 'result' => $reason, 'status' => (int)$status, 'alias' => $alias === null ? '-' : $alias,
                                                       'path' => $path === null ? '-' : $path ) );
        if ( !expImageAliasSettings::placeholderEnabled() )
        {
            return array( 'status' => (int)$status, 'body' => '', 'file' => null,
                          'headers' => array( 'Cache-Control' => 'no-store, max-age=0', 'Content-Length' => '0', 'X-Image-Placeholder' => '1' ) );
        }
        $body = expImagePlaceholder::svg( $width, $height );
        return array( 'status' => (int)$status, 'body' => $body, 'file' => null,
                      'headers' => expImagePlaceholder::headers( $body, $status === 200 && expImageAliasSettings::swapScriptEnabled() ) );
    }

    /**
     * Sends a response: status, headers, then the body or the file (nothing for HEAD). A conditional request for an
     * unchanged file is answered 304.
     *
     * @param array $response
     */
    public static function emit( array $response )
    {
        $status = (int)$response['status'];
        $headers = $response['headers'];
        if ( $status === 200 && isset( $headers['Last-Modified'] ) && isset( $_SERVER['HTTP_IF_MODIFIED_SINCE'] )
             && strtotime( $_SERVER['HTTP_IF_MODIFIED_SINCE'] ) >= strtotime( $headers['Last-Modified'] ) )
        {
            $status = 304;
            unset( $headers['Content-Length'], $headers['Content-Type'] );
            $response['body'] = '';
            $response['file'] = null;
        }
        if ( !headers_sent() )
        {
            while ( ob_get_level() > 0 && @ob_end_clean() );
            http_response_code( $status );
            header_remove( 'Set-Cookie' );
            header_remove( 'Pragma' );
            header_remove( 'Expires' );
            // Server-Timing is added to (other code, like the request shield, sends its own entries)
            foreach ( $headers as $name => $value )
                header( $name . ': ' . $value, $name !== 'Server-Timing' );
        }
        if ( isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] === 'HEAD' )
            return;
        if ( !empty( $response['file'] ) )
            readfile( $response['file'] );
        elseif ( isset( $response['body'] ) && $response['body'] !== '' )
            echo $response['body'];
    }

    // ------------------------------------------------------------------------------------------------ helpers

    /**
     * Whether a storage file exists (the cluster handler: a file on eZFS, the metadata on a database cluster).
     *
     * @param string $path
     * @return bool
     */
    public static function exists( $path )
    {
        if ( !expImageAliasUrl::validStoragePath( $path ) )
            return false;
        if ( is_file( $path ) )
            return true;
        if ( interface_exists( 'ezpDatabaseBasedClusterFileHandler' ) )
        {
            $handler = eZClusterFileHandler::instance( $path );
            if ( $handler instanceof ezpDatabaseBasedClusterFileHandler )
                return (bool)$handler->exists();
        }
        return false;
    }

    /**
     * When a storage file was last written (unix time; 0 when not known).
     *
     * @param string $path
     * @return int
     */
    public static function modified( $path )
    {
        if ( is_file( $path ) )
        {
            clearstatcache( true, $path );
            return (int)filemtime( $path );
        }
        $mtime = eZClusterFileHandler::instance( $path )->mtime();
        return is_numeric( $mtime ) ? (int)$mtime : 0;
    }

    /**
     * A request path as a storage path: no query string, no leading slash, the site's www directory and
     * "index_image.php/" (a server without rewrite rules) taken off, URL decoded.
     *
     * @param string $path
     * @return string
     */
    public static function normalizeRequestPath( $path )
    {
        $path = (string)$path;
        $query = strpos( $path, '?' );
        if ( $query !== false )
            $path = substr( $path, 0, $query );
        $path = rawurldecode( $path );
        $www = class_exists( 'eZSys' ) ? trim( (string)eZSys::wwwDir(), '/' ) : '';
        $path = ltrim( $path, '/' );
        if ( $www !== '' && strpos( $path, $www . '/' ) === 0 )
            $path = substr( $path, strlen( $www ) + 1 );
        if ( strpos( $path, 'index_image.php/' ) === 0 )
            $path = substr( $path, strlen( 'index_image.php/' ) );
        return $path;
    }

    /**
     * @param string $path
     * @return string the path with each segment URL encoded
     */
    protected static function encodePath( $path )
    {
        return implode( '/', array_map( 'rawurlencode', explode( '/', (string)$path ) ) );
    }

    // ------------------------------------------------------------------------------------------------ index_image.php

    /**
     * What index_image.php answers for the path elements left after the siteaccess was matched: content/image with
     * its view parameters (handleParams()), or a storage path (handleStoragePath()).
     *
     * content/image reaches this script where a web server sends it here (Velocity, FrankenPHP, the rules of
     * .htaccess_root): the same answer as the view, without the module, the template engine and the page kernel.
     * More parts than any URL form has are no form, as in the view.
     *
     * @param string[] $elements the path elements after the siteaccess
     * @return array array( 'params', string[] ) or array( 'storage', null )
     */
    public static function entryRoute( array $elements )
    {
        $elements = array_values( array_filter( array_map( 'strval', $elements ), 'strlen' ) );
        if ( count( $elements ) >= 2 && $elements[0] === 'content' && $elements[1] === 'image' )
        {
            $parts = array_slice( $elements, 2 );
            return array( 'params', count( $parts ) > 6 ? array() : $parts );
        }
        return array( 'storage', null );
    }

    /**
     * index_image.php: the light entry script for storage paths the web server did not find, and for content/image
     * where the web server routes it here. No session, no module, no template: the settings, the siteaccess (for its
     * image.ini and database), then handleStoragePath() or handleParams().
     *
     * Also run by a persistent worker (Velocity, FrankenPHP worker mode), between page requests of any siteaccess:
     * the kernel's per-request resets run first, as ezpKernelWeb runs them, and every setting is read for this
     * request's siteaccess. Also included by the cluster gateway (index_cluster.php) for an image the cluster does
     * not have yet (ezpClusterGateway::retrieve()).
     */
    public static function runEntryScript()
    {
        // The request scope: the query cache's memo, a transaction a request before left open, the reset hooks
        // (a persistent worker that served a page before)
        if ( !class_exists( 'expKernelServiceScope' ) )
            require_once __DIR__ . '/../expkernelservicescope.php';
        expKernelServiceScope::resetRequest();

        // eZUpdateDebugSettings(), which the siteaccess change calls; this script leaves eZDebug alone
        require_once __DIR__ . '/../../private/classes/debug_settings_functions.php';
        eZDebugSettingsMode( 'none' );
        eZExecution::addFatalErrorHandler( function () {
            if ( !headers_sent() )
                header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1' ) . ' 500 Internal Server Error' );
        } );
        eZDebug::setHandleType( eZDebug::HANDLE_FROM_PHP );

        // eZSys works with index.php while the request came for index_image.php (or index_cluster.php)
        foreach ( array( 'SCRIPT_FILENAME', 'PHP_SELF', 'SCRIPT_NAME' ) as $name )
        {
            if ( isset( $_SERVER[$name] ) )
                $_SERVER[$name] = str_replace( array( '/index_image.php', '/index_cluster.php' ), '/index.php', $_SERVER[$name] );
        }
        $ini = eZINI::instance();
        $timezone = $ini->variable( 'TimeZoneSettings', 'TimeZone' );
        if ( $timezone )
            putenv( "TZ=$timezone" );
        $requestURI = eZSys::serverVariable( 'REQUEST_URI' );
        $GLOBALS['eZGlobalRequestURI'] = $requestURI;
        eZSys::init( 'index.php', $ini->variable( 'SiteAccessSettings', 'ForceVirtualHost' ) === 'true' );
        unset( $GLOBALS['eZURIRequestInstance'] );
        $uri = eZURI::instance( eZSys::requestURI() );
        $GLOBALS['eZRequestedURI'] = $uri;
        eZExtension::activateExtensions( 'default' );
        // The host without the port, as ezpKernelWeb matches it (HostMatchMapItems name hosts, not ports: on
        // Velocity's :8080 the host carries the port)
        $access = eZSiteAccess::match( $uri, preg_replace( '/:\d+$/', '', (string)eZSys::hostname() ), eZSys::serverPort(), eZSys::indexFile() );
        eZSiteAccess::change( $access );
        eZExtension::activateExtensions( 'access' );
        // The settings of this siteaccess, not those a request before left in a persistent worker
        eZINI::resetAllInstances( false );

        $server = new self();
        list( $route, $parts ) = self::entryRoute( $uri->elements( false ) );
        if ( $route === 'params' )
            self::emit( $server->handleParams( $parts ) );
        else
            self::emit( $server->handleStoragePath( (string)$requestURI ) );

        eZExecution::cleanup();
        eZExecution::setCleanExit();
    }
}
