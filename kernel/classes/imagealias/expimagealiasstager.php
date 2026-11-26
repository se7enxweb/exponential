<?php
/**
 * File containing the expImageAliasStager class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Staging of images: a site that is not the production site (image.ini [AliasStaging] Environment other than pro, a
 * SyncFrom environment with an [AliasSource_<environment>] BaseURL) fetches the stored images it is missing, the
 * originals and the aliases, from that source installation, so a development or staging site needs no copy of
 * var/storage.
 *
 * - On request (expImageAliasServer): a missing alias is fetched from the source first; when the source does not have
 *   it, its original is fetched and the alias is made here. A missing original asked for by its storage path is
 *   fetched. All within [AliasSettings] MaxWait, then the placeholder.
 * - Resync (resync(), for setup/aliases and exp:image:alias resync): the files the database lists for the images of
 *   a scope (the installation, a node or subtree, classes, attributes, aliases, changed since), missing ones only
 *   (the default) or also those that differ (size, then a hash), with a dry run and a progress callback. Never
 *   writes the database: it fetches the files the alias lists already name.
 *
 * The source URL of a file is BaseURL plus its storage path, or what [AliasStaging] PathMap[] maps the path's prefix
 * to. Credentials are read from expSecrets by the name in Credential= ("user:password" for basic authentication or
 * "Bearer <token>"); they never appear in a setting, the log, the state file or a message.
 *
 * What is fetched is checked before it is stored: HTTP 200, an image MIME type (the Content-Type header) equal to
 * the one of the file's extension, the content itself read as that type (getimagesize(); an SVG must be an svg
 * document), and at most MaxFileSize bytes. An HTML error page is never stored as an image. Files are stored through
 * the cluster file handler (eZFS or a database cluster).
 *
 * Circuit breaker: after FailuresBeforePause failures in a row (no answer, a timeout, a refused or invalid answer, a
 * file the source does not have either as alias or original) the source is paused for PauseSeconds; meanwhile no
 * request waits for it (the placeholder is answered at once). When a pause starts, a warning is kept for the
 * administration and an e-mail goes to AlertRecipients[] (else site.ini [MailSettings] AdminEmail), at most once an
 * hour per source. The state is a small file per source in var/<site>/cache/imagealias-staging/.
 *
 * @since 6.0.15
 * @package kernel
 */
class expImageAliasStager
{
    /** The environments an installation can be (image.ini [AliasStaging] Environment) */
    const ENVIRONMENTS = array( 'alpha', 'dev', 'beta', 'stage', 'pro', 'post', 'arc' );

    /** Seconds between two alert e-mails about one source */
    const ALERT_INTERVAL = 3600;

    /** The results of fetch() that say the source is not working (they count for the circuit breaker) */
    const FAILURES = array( 'down', 'timeout', 'unauthorized', 'invalid', 'too-large', 'http', 'no-credential', 'bad-source' );

    /** @var array the [AliasStaging] settings (expImageAliasSettings::staging()) */
    protected $staging;

    /** @var string the source environment ('' = none) */
    protected $sourceName;

    /** @var array base_url, credential */
    protected $source;

    /** @var string|null the directory of the state files */
    protected $stateDirectory;

    /** @var callable|null function( $method, $url, array $headers, $timeout, $maxBytes ): response array (tests) */
    protected $transport = null;

    /** @var callable|null function( $subject, $body, array $recipients ): bool (tests) */
    protected $mailer = null;

    /** @var callable|null function(): int, the time (tests) */
    protected $clock = null;

    /** @var array alias name => storage path: files fetched from the source that eZImageManager takes as made */
    protected static $adopted = array();

    /**
     * @param array|null $staging the [AliasStaging] settings, as expImageAliasSettings::staging() gives them (null:
     *                            read them)
     * @param array|null $source base_url, credential of the source (null: [AliasSource_<SyncFrom>])
     * @param string|null $stateDirectory where the state files are kept (null: var/<site>/cache/imagealias-staging)
     */
    public function __construct( ?array $staging = null, ?array $source = null, $stateDirectory = null )
    {
        $this->staging = ( $staging === null ? expImageAliasSettings::staging() : $staging ) + array(
            'environment' => 'pro', 'sync_from' => '', 'path_map' => array(), 'timeout' => 5, 'failures_before_pause' => 5,
            'pause_seconds' => 300, 'alert_email' => true, 'alert_recipients' => array(), 'max_file_size' => 67108864 );
        $this->sourceName = (string)$this->staging['sync_from'];
        $this->source = $source === null
                      ? ( $this->sourceName !== '' ? expImageAliasSettings::source( $this->sourceName ) : array( 'base_url' => '', 'credential' => '' ) )
                      : $source + array( 'base_url' => '', 'credential' => '' );
        $this->source['base_url'] = rtrim( trim( (string)$this->source['base_url'] ), '/' );
        $this->stateDirectory = $stateDirectory;
    }

    /**
     * The stager of this request's settings, or null when this installation does not stage (Environment pro, no
     * SyncFrom, no BaseURL).
     *
     * @return expImageAliasStager|null
     */
    public static function forRequest()
    {
        $staging = expImageAliasSettings::staging();
        if ( $staging['environment'] === 'pro' || $staging['sync_from'] === '' )
            return null;
        $stager = new self( $staging );
        return $stager->isActive() ? $stager : null;
    }

    /**
     * Whether this stager fetches anything: not production, a source with a base URL.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->staging['environment'] !== 'pro' && $this->sourceName !== '' && $this->source['base_url'] !== '';
    }

    /** @return string this installation's environment */
    public function environment()
    {
        return (string)$this->staging['environment'];
    }

    /** @return string the source environment */
    public function sourceName()
    {
        return $this->sourceName;
    }

    /** @return string the source's base URL (no credentials: a URL with user information is refused) */
    public function baseUrl()
    {
        return $this->source['base_url'];
    }

    /**
     * Sets the HTTP transport (tests): function( $method, $url, array $headers, $timeout, $maxBytes ) returning
     * array( status, type, length, body, error ); null: the built-in one (request()).
     *
     * @param callable|null $transport
     */
    public function setTransport( $transport )
    {
        $this->transport = $transport;
    }

    /**
     * Sets how the alert is sent (tests): function( $subject, $body, array $recipients ): bool; null: the kernel's
     * mail transport.
     *
     * @param callable|null $mailer
     */
    public function setMailer( $mailer )
    {
        $this->mailer = $mailer;
    }

    /**
     * Sets the clock (tests): function(): int; null: time().
     *
     * @param callable|null $clock
     */
    public function setClock( $clock )
    {
        $this->clock = $clock;
    }

    // ------------------------------------------------------------------------------------------------ paths

    /**
     * The URL of a storage file at the source: the longest PathMap[] prefix of the path replaced by what it maps to
     * (a path below BaseURL, or a whole http(s) URL), else BaseURL plus the path. Each path segment is URL encoded.
     *
     * @param string $localPath var/<site>/storage/...
     * @return string|null null for a path outside the storage, a source without a base URL or a base URL with user
     *                     information (credentials go in Credential=, never in the URL)
     */
    public function sourceUrl( $localPath )
    {
        $localPath = ltrim( (string)$localPath, '/' );
        if ( !expImageAliasUrl::validStoragePath( $localPath ) || !self::validBaseUrl( $this->source['base_url'] ) )
            return null;
        $best = null;
        foreach ( (array)$this->staging['path_map'] as $line )
        {
            $parts = explode( ';', (string)$line, 2 );
            if ( count( $parts ) !== 2 )
                continue;
            $from = trim( trim( $parts[0] ), '/' );
            $to = trim( $parts[1] );
            if ( $from === '' || $to === '' || ( $localPath !== $from && strpos( $localPath, $from . '/' ) !== 0 ) )
                continue;
            if ( $best === null || strlen( $from ) > strlen( $best[0] ) )
                $best = array( $from, $to );
        }
        $rest = $localPath;
        $base = $this->source['base_url'];
        if ( $best !== null )
        {
            $rest = ltrim( substr( $localPath, strlen( $best[0] ) ), '/' );
            if ( preg_match( '#^https?://#i', $best[1] ) )
            {
                if ( !self::validBaseUrl( rtrim( $best[1], '/' ) ) )
                    return null;
                $base = rtrim( $best[1], '/' );
            }
            else
                $base .= '/' . trim( $best[1], '/' );
        }
        $encoded = implode( '/', array_map( 'rawurlencode', explode( '/', $rest ) ) );
        return rtrim( $base, '/' ) . ( $encoded !== '' ? '/' . $encoded : '' );
    }

    /**
     * Whether a base URL may be used: http or https, a host, no user information, no query or fragment.
     *
     * @param string $url
     * @return bool
     */
    public static function validBaseUrl( $url )
    {
        $url = (string)$url;
        if ( $url === '' || !preg_match( '#^https?://#i', $url ) || preg_match( '/[\s\x00-\x1F\x7F]/', $url ) )
            return false;
        $parts = parse_url( $url );
        return is_array( $parts ) && !empty( $parts['host'] ) && !isset( $parts['user'] ) && !isset( $parts['pass'] )
               && !isset( $parts['query'] ) && !isset( $parts['fragment'] );
    }

    // ------------------------------------------------------------------------------------------------ credentials

    /**
     * The Authorization header value for the source's credential: "Basic <base64>" for "user:password", "Bearer
     * <token>" as it is. Never logged or shown.
     *
     * @return string|null|false null: no credential configured; false: one is configured but the secret is missing or
     *                           not usable
     */
    protected function authorization()
    {
        $name = trim( (string)$this->source['credential'] );
        if ( $name === '' )
            return null;
        $value = class_exists( 'expSecrets' ) ? expSecrets::get( $name ) : null;
        return self::authorizationFor( $value );
    }

    /**
     * The Authorization header value for a credential's value (see authorization()).
     *
     * @param string|null $value
     * @return string|false false for a value that is neither "user:password" nor "Bearer <token>"
     */
    public static function authorizationFor( $value )
    {
        if ( !is_string( $value ) )
            return false;
        $value = trim( $value );
        if ( preg_match( '/^bearer\s+(\S+)$/i', $value, $match ) )
            return 'Bearer ' . $match[1];
        if ( strpos( $value, ':' ) > 0 && !preg_match( '/[\r\n]/', $value ) )
            return 'Basic ' . base64_encode( $value );
        return false;
    }

    /**
     * A text with the credential's value and its header form taken out (for a message that might quote an error).
     *
     * @param string $text
     * @return string
     */
    protected function hideCredential( $text )
    {
        $text = (string)$text;
        $name = trim( (string)$this->source['credential'] );
        if ( $name === '' || !class_exists( 'expSecrets' ) )
            return $text;
        $value = expSecrets::get( $name );
        if ( !is_string( $value ) || strlen( trim( $value ) ) < 3 )
            return $text;
        $hidden = array( trim( $value ), base64_encode( trim( $value ) ), rawurlencode( trim( $value ) ) );
        if ( preg_match( '/^bearer\s+(\S+)$/i', trim( $value ), $match ) )
            $hidden[] = $match[1];
        foreach ( $hidden as $secret )
        {
            if ( strlen( $secret ) >= 3 )
                $text = str_replace( $secret, '[secret ' . $name . ']', $text );
        }
        return $text;
    }

    // ------------------------------------------------------------------------------------------------ fetching

    /**
     * Fetches one storage file from the source and stores it, when the circuit breaker lets it.
     *
     * @param string $localPath var/<site>/storage/...
     * @param float|null $maxSeconds the time this may take (at most Timeout); null: Timeout
     * @param array $options overwrite => store over an existing file; dry_run => fetch and check, store nothing;
     *                       compare => only store when the size or the hash differs from the local file
     * @return array result (ok, same, missing, paused, inactive, down, timeout, unauthorized, invalid, too-large,
     *               http, no-credential, bad-source), reason (a short text, never a credential), ms, bytes, stored
     */
    public function fetch( $localPath, $maxSeconds = null, array $options = array() )
    {
        $started = microtime( true );
        $localPath = ltrim( (string)$localPath, '/' );
        $out = array( 'result' => 'inactive', 'reason' => '', 'ms' => 0, 'bytes' => 0, 'stored' => false );
        if ( !$this->isActive() )
            return $out;
        if ( $this->isPaused() )
            return array( 'result' => 'paused', 'reason' => 'source paused' ) + $out;
        $url = $this->sourceUrl( $localPath );
        if ( $url === null )
        {
            $out = array( 'result' => 'bad-source', 'reason' => 'no source URL for the path' ) + $out;
            return $this->finish( $localPath, $out, $started );
        }
        $authorization = $this->authorization();
        if ( $authorization === false )
        {
            $out = array( 'result' => 'no-credential', 'reason' => 'the credential secret is missing or not user:password / Bearer <token>' ) + $out;
            return $this->finish( $localPath, $out, $started );
        }
        $timeout = (float)$this->staging['timeout'];
        if ( $maxSeconds !== null )
            $timeout = min( $timeout, (float)$maxSeconds );
        if ( $timeout < 0.05 )
            return array( 'result' => 'timeout', 'reason' => 'no time left' ) + $out;
        $headers = array( 'Accept' => '*/*', 'User-Agent' => 'Exponential image staging (' . $this->environment() . ')' );
        if ( is_string( $authorization ) )
            $headers['Authorization'] = $authorization;
        $maxBytes = (int)$this->staging['max_file_size'];
        $response = $this->transport !== null
                  ? call_user_func( $this->transport, 'GET', $url, $headers, $timeout, $maxBytes )
                  : self::request( 'GET', $url, $headers, $timeout, $maxBytes );
        unset( $headers['Authorization'], $authorization );
        $response = (array)$response + array( 'status' => 0, 'type' => '', 'length' => null, 'body' => '', 'error' => '' );
        $check = self::validate( $localPath, $response, $maxBytes );
        $out['bytes'] = strlen( (string)$response['body'] );
        if ( $check !== null )
        {
            $out = array( 'result' => $check[0], 'reason' => $this->hideCredential( $check[1] ) ) + $out;
            return $this->finish( $localPath, $out, $started );
        }
        $body = (string)$response['body'];
        $exists = expImageAliasServer::exists( $localPath );
        if ( $exists && !empty( $options['compare'] ) && self::sameAsLocal( $localPath, $body ) )
        {
            $out['result'] = 'same';
            return $this->finish( $localPath, $out, $started );
        }
        if ( empty( $options['dry_run'] ) && ( !$exists || !empty( $options['overwrite'] ) ) )
        {
            if ( !$this->store( $localPath, $body ) )
            {
                $out = array( 'result' => 'invalid', 'reason' => 'the file could not be stored' ) + $out;
                return $this->finish( $localPath, $out, $started );
            }
            $out['stored'] = true;
        }
        $out['result'] = 'ok';
        return $this->finish( $localPath, $out, $started );
    }

    /**
     * Counts the outcome of a fetch for the circuit breaker and logs it.
     *
     * @param string $localPath
     * @param array $out
     * @param float $started
     * @return array $out with ms
     */
    protected function finish( $localPath, array $out, $started )
    {
        $out['ms'] = (int)round( ( microtime( true ) - $started ) * 1000 );
        if ( in_array( $out['result'], self::FAILURES, true ) )
            $this->recordFailure( $out['result'] . ': ' . $out['reason'] );
        elseif ( $out['result'] === 'ok' || $out['result'] === 'same' )
            $this->recordSuccess();
        // A file found unchanged at the source is no event (an overwrite resync would write one line per file)
        if ( $out['result'] === 'same' )
            return $out;
        expImageAliasLog::write( in_array( $out['result'], array( 'ok', 'same', 'missing' ), true ) ? 'stage' : 'fail', array(
            'result' => $out['result'], 'ms' => $out['ms'], 'alias' => '-', 'source' => $this->sourceName, 'bytes' => $out['bytes'],
            'stored' => $out['stored'] ? 1 : 0, 'path' => $localPath ) );
        return $out;
    }

    /**
     * Whether a fetched answer may be stored as the file $localPath; null when it may, else array( result, reason ).
     *
     * @param string $localPath
     * @param array $response status, type, length, body, error
     * @param int $maxBytes
     * @return array|null
     */
    public static function validate( $localPath, array $response, $maxBytes )
    {
        $status = (int)( $response['status'] ?? 0 );
        $error = (string)( $response['error'] ?? '' );
        if ( $error === 'too-large' )
            return array( 'too-large', 'larger than ' . (int)$maxBytes . ' bytes' );
        if ( $status === 0 )
            return array( $error === 'timeout' ? 'timeout' : 'down', $error !== '' ? $error : 'no answer' );
        if ( $status === 404 || $status === 410 )
            return array( 'missing', 'HTTP ' . $status );
        if ( $status === 401 || $status === 403 )
            return array( 'unauthorized', 'HTTP ' . $status );
        if ( $status !== 200 )
            return array( 'http', 'HTTP ' . $status );
        $body = (string)( $response['body'] ?? '' );
        if ( $body === '' )
            return array( 'invalid', 'empty answer' );
        if ( $maxBytes > 0 && strlen( $body ) > $maxBytes )
            return array( 'too-large', 'larger than ' . (int)$maxBytes . ' bytes' );
        $expected = self::normalizeMime( (string)eZMimeType::findByURL( $localPath )['name'] );
        if ( strpos( $expected, 'image/' ) !== 0 )
            return array( 'invalid', 'not an image path' );
        $type = self::normalizeMime( (string)( $response['type'] ?? '' ) );
        if ( self::looksLikeHtml( $body ) )
            return array( 'invalid', 'an HTML page, not an image (' . ( $type !== '' ? $type : 'no type' ) . ')' );
        if ( $type !== $expected )
            return array( 'invalid', 'type ' . ( $type !== '' ? $type : 'none' ) . ' for a ' . $expected . ' file' );
        if ( $expected === 'image/svg+xml' )
        {
            if ( !self::isSvgDocument( $body ) )
                return array( 'invalid', 'not an SVG document' );
            return null;
        }
        $size = @getimagesizefromstring( $body );
        // PHP before 8.2 reads no AVIF size (8.1: 0 x 0, 8.0: no image at all): an AVIF with its signature is taken
        if ( $expected === 'image/avif' && ( !is_array( $size ) || empty( $size[0] ) || empty( $size[1] ) )
             && expImageFormatConverter::headHasSignature( substr( $body, 0, 16 ), 'avif' ) )
            return null;
        if ( !is_array( $size ) || empty( $size[0] ) || empty( $size[1] ) )
            return array( 'invalid', 'not a readable image' );
        if ( self::normalizeMime( (string)( $size['mime'] ?? '' ) ) !== $expected )
            return array( 'invalid', 'the content is ' . (string)( $size['mime'] ?? 'unknown' ) . ', not ' . $expected );
        return null;
    }

    /**
     * A MIME type without parameters, lower case, with the old names of JPEG and PNG mapped.
     *
     * @param string $type
     * @return string
     */
    public static function normalizeMime( $type )
    {
        $type = strtolower( trim( explode( ';', (string)$type )[0] ) );
        $map = array( 'image/jpg' => 'image/jpeg', 'image/pjpeg' => 'image/jpeg', 'image/x-png' => 'image/png',
                      'image/x-ms-bmp' => 'image/bmp', 'image/x-bmp' => 'image/bmp' );
        return isset( $map[$type] ) ? $map[$type] : $type;
    }

    /**
     * @param string $body
     * @return bool whether the start of $body is an HTML document
     */
    protected static function looksLikeHtml( $body )
    {
        $start = strtolower( ltrim( substr( (string)$body, 0, 512 ), "\xEF\xBB\xBF \t\r\n" ) );
        return strpos( $start, '<!doctype html' ) === 0 || strpos( $start, '<html' ) === 0 || strpos( $start, '<head' ) === 0
               || strpos( $start, '<body' ) === 0;
    }

    /**
     * @param string $body
     * @return bool whether $body is an XML document whose root is svg
     */
    protected static function isSvgDocument( $body )
    {
        if ( !class_exists( 'DOMDocument' ) )
            return false;
        $doc = new DOMDocument();
        $previous = libxml_use_internal_errors( true );
        $ok = $doc->loadXML( (string)$body, LIBXML_NONET );
        libxml_clear_errors();
        libxml_use_internal_errors( $previous );
        return $ok && $doc->documentElement !== null && strtolower( $doc->documentElement->localName ) === 'svg';
    }

    /**
     * Whether the local file has the same size and content as $body.
     *
     * @param string $localPath
     * @param string $body
     * @return bool
     */
    protected static function sameAsLocal( $localPath, $body )
    {
        if ( is_file( $localPath ) )
        {
            clearstatcache( true, $localPath );
            if ( filesize( $localPath ) !== strlen( $body ) )
                return false;
            return hash_file( 'sha256', $localPath ) === hash( 'sha256', $body );
        }
        $handler = eZClusterFileHandler::instance( $localPath );
        $size = $handler->size();
        if ( is_numeric( $size ) && (int)$size !== strlen( $body ) )
            return false;
        $contents = $handler->fetchContents();
        return is_string( $contents ) && hash( 'sha256', $contents ) === hash( 'sha256', $body );
    }

    /**
     * Stores a fetched file through the cluster file handler (atomically on eZFS).
     *
     * @param string $localPath
     * @param string $body
     * @return bool
     */
    protected function store( $localPath, $body )
    {
        try
        {
            $mime = eZMimeType::findByURL( $localPath );
            eZClusterFileHandler::instance()->fileStoreContents( $localPath, $body, 'image', $mime['name'] );
        }
        catch ( Throwable $e )
        {
            eZDebug::writeError( "Storing the staged file $localPath failed: " . $this->hideCredential( $e->getMessage() ), __METHOD__ );
            return false;
        }
        return expImageAliasServer::exists( $localPath );
    }

    /**
     * One HTTP request (curl when available, else a PHP stream), redirects not followed, the body read up to
     * $maxBytes.
     *
     * @param string $method GET or HEAD
     * @param string $url
     * @param array $headers name => value
     * @param float $timeout seconds for the whole request
     * @param int $maxBytes
     * @return array status (0 when there was no answer), type, length, body, error (timeout, down, too-large, or '')
     */
    public static function request( $method, $url, array $headers, $timeout, $maxBytes )
    {
        $out = array( 'status' => 0, 'type' => '', 'length' => null, 'body' => '', 'error' => '' );
        if ( !preg_match( '#^https?://#i', (string)$url ) )
        {
            $out['error'] = 'down';
            return $out;
        }
        $lines = array();
        foreach ( $headers as $name => $value )
            $lines[] = $name . ': ' . str_replace( array( "\r", "\n" ), '', (string)$value );
        $timeoutMs = max( 50, (int)round( (float)$timeout * 1000 ) );
        if ( function_exists( 'curl_init' ) )
        {
            $body = '';
            $tooLarge = false;
            $handle = curl_init( (string)$url );
            $options = array(
                CURLOPT_CUSTOMREQUEST => $method === 'HEAD' ? 'HEAD' : 'GET',
                CURLOPT_NOBODY => $method === 'HEAD',
                CURLOPT_HTTPHEADER => $lines,
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_CONNECTTIMEOUT_MS => $timeoutMs,
                CURLOPT_TIMEOUT_MS => $timeoutMs,
                CURLOPT_NOSIGNAL => true,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_ENCODING => '',
                CURLOPT_WRITEFUNCTION => function ( $curl, $data ) use ( &$body, &$tooLarge, $maxBytes ) {
                    if ( $maxBytes > 0 && strlen( $body ) + strlen( $data ) > $maxBytes )
                    {
                        $tooLarge = true;
                        return 0;
                    }
                    $body .= $data;
                    return strlen( $data );
                },
            );
            if ( defined( 'CURLOPT_PROTOCOLS_STR' ) )
                $options[CURLOPT_PROTOCOLS_STR] = 'http,https';
            elseif ( defined( 'CURLOPT_PROTOCOLS' ) )
                $options[CURLOPT_PROTOCOLS] = CURLPROTO_HTTP | CURLPROTO_HTTPS;
            curl_setopt_array( $handle, $options );
            curl_exec( $handle );
            $errno = curl_errno( $handle );
            $out['status'] = (int)curl_getinfo( $handle, CURLINFO_RESPONSE_CODE );
            $out['type'] = (string)curl_getinfo( $handle, CURLINFO_CONTENT_TYPE );
            $length = curl_getinfo( $handle, CURLINFO_CONTENT_LENGTH_DOWNLOAD );
            $out['length'] = is_numeric( $length ) && $length >= 0 ? (int)$length : null;
            if ( PHP_VERSION_ID < 80000 )
                curl_close( $handle );
            unset( $handle );
            if ( $tooLarge )
            {
                $out['error'] = 'too-large';
                $out['status'] = 0;
                return $out;
            }
            if ( $errno !== 0 )
            {
                $out['error'] = $errno === 28 ? 'timeout' : 'down';
                $out['status'] = 0;
                return $out;
            }
            $out['body'] = $body;
            return $out;
        }
        // Without curl: a stream, the same limits
        $context = stream_context_create( array(
            'http' => array( 'method' => $method === 'HEAD' ? 'HEAD' : 'GET', 'header' => implode( "\r\n", $lines ),
                             'timeout' => $timeoutMs / 1000, 'follow_location' => 0, 'max_redirects' => 1, 'ignore_errors' => true ),
            'ssl' => array( 'verify_peer' => true, 'verify_peer_name' => true ) ) );
        $started = microtime( true );
        $stream = @fopen( (string)$url, 'rb', false, $context );
        if ( $stream === false )
        {
            $out['error'] = ( microtime( true ) - $started ) * 1000 >= $timeoutMs - 50 ? 'timeout' : 'down';
            return $out;
        }
        stream_set_timeout( $stream, (int)ceil( $timeoutMs / 1000 ) );
        $meta = stream_get_meta_data( $stream );
        foreach ( (array)( $meta['wrapper_data'] ?? array() ) as $line )
        {
            if ( preg_match( '#^HTTP/\S+\s+(\d{3})#', (string)$line, $match ) )
                $out['status'] = (int)$match[1];
            elseif ( stripos( (string)$line, 'content-type:' ) === 0 )
                $out['type'] = trim( substr( (string)$line, 13 ) );
            elseif ( stripos( (string)$line, 'content-length:' ) === 0 )
                $out['length'] = (int)trim( substr( (string)$line, 15 ) );
        }
        $body = $method === 'HEAD' ? '' : (string)stream_get_contents( $stream, $maxBytes > 0 ? $maxBytes + 1 : -1 );
        $meta = stream_get_meta_data( $stream );
        fclose( $stream );
        if ( !empty( $meta['timed_out'] ) )
            return array( 'status' => 0, 'error' => 'timeout' ) + $out;
        if ( $maxBytes > 0 && strlen( $body ) > $maxBytes )
            return array( 'status' => 0, 'error' => 'too-large' ) + $out;
        $out['body'] = $body;
        return $out;
    }

    // ------------------------------------------------------------------------------------------------ on request

    /**
     * Fetches the alias file $target from the source, for expImageAliasServer: the alias itself first; when the source
     * does not have it and the original is missing here too, the original (the alias is then made here). Within
     * $maxSeconds.
     *
     * @param string $target the alias's storage path
     * @param string $originalPath the original's storage path ('' when unknown)
     * @param float $maxSeconds
     * @return string alias (the alias file is here now: adopt it), original (the original is here: make the alias),
     *                local (the original was here already: make the alias), pending (the source is paused, slow or
     *                down: try later), missing (the source has neither)
     */
    public function stageAlias( $target, $originalPath, $maxSeconds )
    {
        $started = microtime( true );
        $alias = $this->fetch( $target, $maxSeconds );
        if ( $alias['result'] === 'ok' )
            return 'alias';
        if ( $alias['result'] !== 'missing' )
            return $alias['result'] === 'invalid' || $alias['result'] === 'too-large' ? 'missing' : 'pending';
        if ( $originalPath === '' )
            return 'missing';
        if ( expImageAliasServer::exists( $originalPath ) )
            return 'local';
        $left = (float)$maxSeconds - ( microtime( true ) - $started );
        $original = $this->fetch( $originalPath, $left );
        if ( $original['result'] === 'ok' )
            return 'original';
        if ( $original['result'] === 'missing' )
        {
            // The source has neither the alias nor its original: that counts against it (a wrong BaseURL or PathMap
            // gives nothing but 404)
            $this->recordFailure( 'missing: neither ' . basename( $target ) . ' nor its original at the source' );
            return 'missing';
        }
        return $original['result'] === 'invalid' || $original['result'] === 'too-large' ? 'missing' : 'pending';
    }

    /**
     * Fetches a missing original asked for by its storage path, for expImageAliasServer.
     *
     * @param string $path
     * @param float $maxSeconds
     * @return string ok, pending (paused, slow, down) or missing
     */
    public function stageFile( $path, $maxSeconds )
    {
        $result = $this->fetch( $path, $maxSeconds );
        if ( $result['result'] === 'ok' )
            return 'ok';
        if ( $result['result'] === 'missing' )
        {
            $this->recordFailure( 'missing: ' . basename( $path ) . ' not at the source' );
            return 'missing';
        }
        return $result['result'] === 'invalid' || $result['result'] === 'too-large' ? 'missing' : 'pending';
    }

    /**
     * Runs $callback while eZImageManager::createImageAlias() takes the file $path as the alias $aliasName (a file
     * fetched from the source, so the alias list and ezimagefile are written as render mode writes them, without the
     * original).
     *
     * @param string $aliasName
     * @param string $path
     * @param callable $callback
     * @return mixed what $callback returns
     */
    public static function adopting( $aliasName, $path, $callback )
    {
        $before = self::$adopted;
        self::$adopted[(string)$aliasName] = (string)$path;
        try
        {
            return call_user_func( $callback );
        }
        finally
        {
            self::$adopted = $before;
        }
    }

    /**
     * The file eZImageManager::createImageAlias() takes as the alias $aliasName (see adopting()), when it is there.
     *
     * @param string $aliasName
     * @return string|null
     */
    public static function adoptedFile( $aliasName )
    {
        if ( !isset( self::$adopted[$aliasName] ) )
            return null;
        $path = self::$adopted[$aliasName];
        return eZClusterFileHandler::instance()->fileExists( $path ) ? $path : null;
    }

    // ------------------------------------------------------------------------------------------------ circuit breaker

    /**
     * @return int
     */
    protected function now()
    {
        return $this->clock !== null ? (int)call_user_func( $this->clock ) : time();
    }

    /**
     * Whether the source is paused (circuit breaker open) now.
     *
     * @return bool
     */
    public function isPaused()
    {
        $state = $this->readState();
        return (int)$state['paused_until'] > $this->now();
    }

    /**
     * The state of the source, for the administration: failures in a row, paused until, the last error and success,
     * the warning kept when the source was paused, the last alert.
     *
     * @return array source, base_url, failures, paused, paused_until, last_error, last_error_time, last_success,
     *               last_alert, warning
     */
    public function status()
    {
        $state = $this->readState();
        return array( 'source' => $this->sourceName, 'base_url' => $this->baseUrl(), 'environment' => $this->environment(),
                      'active' => $this->isActive(), 'paused' => (int)$state['paused_until'] > $this->now() ) + $state;
    }

    /**
     * Ends a pause and forgets the failures (the administration's "try again"); the warning goes too. The time of the
     * last alert stays, so a source that fails again within the hour sends no second mail, unless $forgetAlert (a
     * test of the alert).
     *
     * @param bool $forgetAlert
     */
    public function resetBreaker( $forgetAlert = false )
    {
        $this->updateState( function ( array $state ) use ( $forgetAlert ) {
            $state['failures'] = 0;
            $state['paused_until'] = 0;
            $state['warning'] = null;
            if ( $forgetAlert )
                $state['last_alert'] = 0;
            return $state;
        } );
    }

    /**
     * Counts one failure; the FailuresBeforePause-th in a row pauses the source for PauseSeconds, keeps a warning and
     * sends the alert (at most once an hour).
     *
     * @param string $reason never a credential
     */
    public function recordFailure( $reason )
    {
        $now = $this->now();
        $limit = max( 1, (int)$this->staging['failures_before_pause'] );
        $pause = max( 0, (int)$this->staging['pause_seconds'] );
        $reason = $this->hideCredential( substr( (string)$reason, 0, 300 ) );
        $alert = false;
        $paused = false;
        $state = $this->updateState( function ( array $state ) use ( $now, $limit, $pause, $reason, &$alert, &$paused ) {
            $state['failures'] = (int)$state['failures'] + 1;
            $state['last_error'] = $reason;
            $state['last_error_time'] = $now;
            if ( $state['failures'] >= $limit && (int)$state['paused_until'] <= $now && $pause > 0 )
            {
                $state['paused_until'] = $now + $pause;
                $state['pauses'] = (int)$state['pauses'] + 1;
                $paused = true;
                $state['warning'] = array( 'time' => $now, 'until' => $now + $pause, 'failures' => $state['failures'], 'reason' => $reason );
                if ( (int)$state['last_alert'] + self::ALERT_INTERVAL <= $now )
                {
                    $state['last_alert'] = $now;
                    $alert = true;
                }
            }
            return $state;
        } );
        if ( $paused )
        {
            expImageAliasLog::write( 'fail', array( 'result' => 'paused', 'alias' => '-', 'source' => $this->sourceName,
                                                    'failures' => (int)$state['failures'], 'seconds' => $pause, 'path' => $reason ) );
            eZDebug::writeWarning( "Image staging: the source '{$this->sourceName}' is paused for $pause seconds after "
                                   . (int)$state['failures'] . " failures (last: $reason)", __METHOD__ );
        }
        if ( $alert && !empty( $this->staging['alert_email'] ) )
            $this->sendAlert( $state );
    }

    /**
     * Forgets the failures in a row after a file came through.
     */
    public function recordSuccess()
    {
        $now = $this->now();
        $state = $this->readState();
        // A file write only when something changes (or once a minute), not for every image of a page
        if ( (int)$state['failures'] === 0 && (int)$state['last_success'] > $now - 60 )
            return;
        $this->updateState( function ( array $state ) use ( $now ) {
            $state['failures'] = 0;
            $state['last_success'] = $now;
            return $state;
        } );
    }

    /**
     * Mails the alert that the source was paused: to AlertRecipients[], else site.ini [MailSettings] AdminEmail.
     *
     * @param array $state
     * @return bool
     */
    protected function sendAlert( array $state )
    {
        $recipients = array();
        foreach ( (array)$this->staging['alert_recipients'] as $recipient )
        {
            if ( trim( (string)$recipient ) !== '' )
                $recipients[] = trim( (string)$recipient );
        }
        if ( !$recipients && class_exists( 'eZINI' ) )
        {
            $admin = trim( (string)eZINI::instance()->variable( 'MailSettings', 'AdminEmail' ) );
            if ( $admin !== '' )
                $recipients[] = $admin;
        }
        if ( !$recipients )
            return false;
        $site = class_exists( 'eZINI' ) ? (string)eZINI::instance()->variable( 'SiteSettings', 'SiteName' ) : '';
        $subject = 'Image staging paused: source ' . $this->sourceName . ( $site !== '' ? ' (' . $site . ')' : '' );
        $body = "The images of this " . $this->environment() . " installation are fetched from the source '" . $this->sourceName
              . "' (" . $this->baseUrl() . ").\n\n"
              . "After " . (int)$state['failures'] . " failures in a row the source is paused until "
              . date( 'Y-m-d H:i:s', (int)$state['paused_until'] ) . "; meanwhile missing images are shown as placeholders.\n\n"
              . "Last error: " . (string)$state['last_error'] . "\n\n"
              . "Settings: image.ini [AliasStaging] and [AliasSource_" . $this->sourceName . "]. History: var/log/"
              . expImageAliasLog::FILE . ".\nThis mail is sent at most once an hour per source.\n";
        $body = $this->hideCredential( $body );
        $sent = false;
        try
        {
            if ( $this->mailer !== null )
                $sent = (bool)call_user_func( $this->mailer, $subject, $body, $recipients );
            else
            {
                $mail = new eZMail();
                $ini = eZINI::instance();
                $sender = (string)$ini->variable( 'MailSettings', 'EmailSender' );
                if ( $sender === '' )
                    $sender = (string)$ini->variable( 'MailSettings', 'AdminEmail' );
                $mail->setSender( $sender );
                $mail->setReceiver( array_shift( $recipients ) );
                foreach ( $recipients as $recipient )
                    $mail->addReceiver( $recipient );
                $mail->setSubject( $subject );
                $mail->setBody( $body );
                $sent = (bool)eZMailTransport::send( $mail );
            }
        }
        catch ( Throwable $e )
        {
            eZDebug::writeError( 'Sending the image staging alert failed: ' . $this->hideCredential( $e->getMessage() ), __METHOD__ );
        }
        expImageAliasLog::write( $sent ? 'stage' : 'fail', array( 'result' => $sent ? 'alert-sent' : 'alert-failed', 'alias' => '-',
                                                                   'source' => $this->sourceName, 'path' => '-' ) );
        return $sent;
    }

    // ------------------------------------------------------------------------------------------------ state file

    /**
     * @return string the directory of the state files
     */
    public function stateDirectory()
    {
        if ( $this->stateDirectory !== null )
            return rtrim( $this->stateDirectory, '/' );
        return rtrim( (string)eZSys::cacheDirectory(), '/' ) . '/imagealias-staging';
    }

    /**
     * @return string the state file of the source
     */
    protected function stateFile()
    {
        return $this->stateDirectory() . '/' . ( $this->sourceName !== '' ? $this->sourceName : 'none' ) . '.json';
    }

    /**
     * @return array the state of the source (defaults when there is none)
     */
    public function readState()
    {
        $defaults = array( 'failures' => 0, 'paused_until' => 0, 'pauses' => 0, 'last_error' => '', 'last_error_time' => 0,
                           'last_success' => 0, 'last_alert' => 0, 'warning' => null );
        $file = $this->stateFile();
        if ( !is_file( $file ) )
            return $defaults;
        $data = json_decode( (string)@file_get_contents( $file ), true );
        return is_array( $data ) ? array_intersect_key( $data, $defaults ) + $defaults : $defaults;
    }

    /**
     * Changes the state under a lock (a lock file of its own; the state file is replaced atomically, so processes of
     * other users can replace it too).
     *
     * @param callable $change function( array $state ): array
     * @return array the new state
     */
    protected function updateState( $change )
    {
        $directory = $this->stateDirectory();
        if ( !is_dir( $directory ) )
            eZDir::mkdir( $directory, false, true );
        $lockFile = $this->stateFile() . '.lock';
        $handle = @fopen( $lockFile, 'c' );
        if ( $handle === false )
            $handle = @fopen( $lockFile, 'r' );
        if ( $handle !== false )
            @flock( $handle, LOCK_EX );
        try
        {
            $state = call_user_func( $change, $this->readState() );
            $written = eZFile::create( basename( $this->stateFile() ), $directory, json_encode( $state ), true );
            if ( !$written )
                eZDebug::writeWarning( 'Cannot write the image staging state ' . $this->stateFile(), __METHOD__ );
        }
        finally
        {
            if ( $handle !== false )
            {
                @flock( $handle, LOCK_UN );
                @fclose( $handle );
            }
        }
        return $state;
    }

    // ------------------------------------------------------------------------------------------------ resync

    /**
     * Fetches again the files the database lists for the images of a scope (setup/aliases, exp:image:alias resync).
     * Never writes the database. Stops when the source is paused.
     *
     * @param array $options node => a node id (that node's object), subtree => a node id (the node and everything
     *                       below), class => class identifiers, attribute => attribute identifiers, alias => alias
     *                       names ('original' for the originals; default: the original and every alias the image
     *                       lists), since => a Unix time (objects modified since), overwrite => replace files that
     *                       differ (size, then a hash; default: missing files only), dry_run => fetch nothing / store
     *                       nothing, limit => at most this many images, run => a run id
     * @param callable|null $progress function( array $progress ): done, total, path, result, counts, run
     * @return array run, result (done, paused, inactive, failed), images, files, counts, ms, scope
     */
    public function resync( array $options = array(), $progress = null )
    {
        $started = microtime( true );
        $run = isset( $options['run'] ) && preg_match( '/^[A-Za-z0-9_.-]{1,64}$/', (string)$options['run'] ) ? (string)$options['run'] : self::newRunId();
        $scope = self::describeScope( $options );
        $summary = array( 'run' => $run, 'result' => 'done', 'images' => 0, 'files' => 0, 'counts' => self::emptyCounts(),
                          'ms' => 0, 'scope' => $scope, 'source' => $this->sourceName );
        if ( !$this->isActive() )
        {
            $summary['result'] = 'inactive';
            return $summary;
        }
        expImageAliasLog::write( 'run-start', array( 'result' => 'resync', 'run' => $run, 'alias' => '-', 'source' => $this->sourceName,
                                                     'mode' => !empty( $options['overwrite'] ) ? 'overwrite' : 'missing',
                                                     'dry_run' => !empty( $options['dry_run'] ) ? 1 : 0, 'path' => $scope ) );
        try
        {
            $total = self::countImages( $options );
            $done = 0;
            foreach ( self::imageFiles( $options ) as $image )
            {
                $summary['images']++;
                $files = self::filterFiles( $image['files'], isset( $options['alias'] ) ? (array)$options['alias'] : array() );
                $result = $this->resyncFiles( $files, $options, $run, $summary['counts'] );
                $summary['files'] += count( $files );
                $done++;
                if ( $progress !== null )
                    call_user_func( $progress, array( 'run' => $run, 'done' => $done, 'total' => $total, 'path' => $image['label'],
                                                      'result' => $result, 'counts' => $summary['counts'] ) );
                if ( $result === 'paused' )
                {
                    $summary['result'] = 'paused';
                    break;
                }
            }
        }
        catch ( Throwable $e )
        {
            $summary['result'] = 'failed';
            eZDebug::writeError( 'The image resync failed: ' . $this->hideCredential( $e->getMessage() ), __METHOD__ );
        }
        $summary['ms'] = (int)round( ( microtime( true ) - $started ) * 1000 );
        $fields = array( 'result' => $summary['result'], 'ms' => $summary['ms'], 'run' => $run, 'alias' => '-', 'source' => $this->sourceName,
                         'images' => $summary['images'], 'files' => $summary['files'] );
        foreach ( $summary['counts'] as $name => $count )
            $fields[str_replace( '-', '_', $name )] = $count;
        $fields['path'] = $scope;
        expImageAliasLog::write( 'run-end', $fields );
        return $summary;
    }

    /**
     * The resync of one image's files (the part of resync() without the database).
     *
     * @param array $files alias name => storage path
     * @param array $options overwrite, dry_run
     * @param string $run
     * @param array $counts the counters, added to
     * @return string done or paused
     */
    public function resyncFiles( array $files, array $options, $run, array &$counts )
    {
        $overwrite = !empty( $options['overwrite'] );
        $dryRun = !empty( $options['dry_run'] );
        foreach ( $files as $aliasName => $path )
        {
            if ( $this->isPaused() )
                return 'paused';
            $exists = expImageAliasServer::exists( $path );
            if ( $exists && !$overwrite )
            {
                $counts['present']++;
                continue;
            }
            if ( !$exists && $dryRun )
            {
                // A dry run asks nothing of the source for a missing file
                $counts['would-fetch']++;
                expImageAliasLog::write( 'stage', array( 'result' => 'would-fetch', 'run' => $run, 'alias' => $aliasName, 'source' => $this->sourceName, 'path' => $path ) );
                continue;
            }
            $result = $this->fetch( $path, null, array( 'overwrite' => $exists, 'compare' => $exists, 'dry_run' => $dryRun ) );
            switch ( $result['result'] )
            {
                case 'ok':
                    $name = $exists ? ( $dryRun ? 'would-replace' : 'replaced' ) : 'fetched';
                    break;
                case 'same':
                case 'missing':
                case 'paused':
                    $name = $result['result'];
                    break;
                default:
                    $name = 'failed';
            }
            $counts[$name]++;
            if ( $name !== 'same' )
                expImageAliasLog::write( $name === 'failed' ? 'fail' : 'stage', array( 'result' => $name, 'run' => $run, 'alias' => $aliasName,
                                                                                       'source' => $this->sourceName, 'reason' => $result['result'], 'path' => $path ) );
            if ( $name === 'paused' )
                return 'paused';
        }
        return $this->isPaused() ? 'paused' : 'done';
    }

    /**
     * @return array the counters of a resync
     */
    public static function emptyCounts()
    {
        return array( 'fetched' => 0, 'replaced' => 0, 'same' => 0, 'present' => 0, 'missing' => 0, 'failed' => 0, 'paused' => 0,
                      'would-fetch' => 0, 'would-replace' => 0 );
    }

    /**
     * @return string a new run id (time and random)
     */
    public static function newRunId()
    {
        return 'rs' . date( 'YmdHis' ) . '-' . bin2hex( random_bytes( 3 ) );
    }

    /**
     * The scope of a resync as one line (the log).
     *
     * @param array $options
     * @return string
     */
    public static function describeScope( array $options )
    {
        $parts = array();
        foreach ( array( 'node', 'subtree', 'since', 'limit' ) as $key )
        {
            if ( isset( $options[$key] ) && (int)$options[$key] > 0 )
                $parts[] = $key . '=' . (int)$options[$key];
        }
        foreach ( array( 'class', 'attribute', 'alias' ) as $key )
        {
            if ( !empty( $options[$key] ) )
                $parts[] = $key . '=' . implode( ',', array_map( 'strval', (array)$options[$key] ) );
        }
        if ( !empty( $options['overwrite'] ) )
            $parts[] = 'overwrite';
        if ( !empty( $options['dry_run'] ) )
            $parts[] = 'dry-run';
        return $parts ? implode( ' ', $parts ) : 'installation';
    }

    /**
     * The files of an image a resync handles: those of $aliases ('original' for the original), all when empty.
     *
     * @param array $files alias name => path
     * @param string[] $aliases
     * @return array
     */
    public static function filterFiles( array $files, array $aliases )
    {
        $aliases = array_values( array_filter( array_map( 'strval', $aliases ), 'strlen' ) );
        if ( !$aliases )
            return $files;
        return array_intersect_key( $files, array_flip( $aliases ) );
    }

    /**
     * The files an image attribute's stored XML lists: the original and every alias, as alias name => storage path.
     *
     * @param string $xml the attribute's data_text
     * @return array
     */
    public static function filesOfXml( $xml )
    {
        $files = array();
        if ( !is_string( $xml ) || trim( $xml ) === '' )
            return $files;
        $doc = new DOMDocument();
        $previous = libxml_use_internal_errors( true );
        $ok = $doc->loadXML( $xml, LIBXML_NONET );
        libxml_clear_errors();
        libxml_use_internal_errors( $previous );
        if ( !$ok || $doc->documentElement === null )
            return $files;
        $original = (string)$doc->documentElement->getAttribute( 'url' );
        if ( $original !== '' && expImageAliasUrl::validStoragePath( $original ) )
            $files['original'] = $original;
        foreach ( $doc->getElementsByTagName( 'alias' ) as $alias )
        {
            $name = (string)$alias->getAttribute( 'name' );
            $url = (string)$alias->getAttribute( 'url' );
            if ( $name !== '' && $name !== 'original' && $url !== '' && expImageAliasUrl::validStoragePath( $url ) && $url !== $original )
                $files[$name] = $url;
        }
        return $files;
    }

    /**
     * The SQL condition and joins of a resync scope.
     *
     * @param array $options
     * @return array array( joins, conditions )
     */
    protected static function scopeSql( array $options )
    {
        $db = eZDB::instance();
        $joins = '';
        $where = array( 'o.status = ' . (int)eZContentObject::STATUS_PUBLISHED, "a.data_type_string = 'ezimage'", 'a.version = o.current_version' );
        if ( !empty( $options['attribute'] ) )
        {
            $joins .= ' JOIN ezcontentclass_attribute ca ON ca.id = a.contentclassattribute_id AND ca.version = 0';
            $where[] = 'ca.identifier IN (' . implode( ', ', array_map( function ( $value ) use ( $db ) {
                return "'" . $db->escapeString( (string)$value ) . "'";
            }, (array)$options['attribute'] ) ) . ')';
        }
        if ( !empty( $options['class'] ) )
        {
            $joins .= ' JOIN ezcontentclass c ON c.id = o.contentclass_id AND c.version = 0';
            $where[] = 'c.identifier IN (' . implode( ', ', array_map( function ( $value ) use ( $db ) {
                return "'" . $db->escapeString( (string)$value ) . "'";
            }, (array)$options['class'] ) ) . ')';
        }
        if ( isset( $options['since'] ) && (int)$options['since'] > 0 )
            $where[] = 'o.modified >= ' . (int)$options['since'];
        if ( isset( $options['subtree'] ) && (int)$options['subtree'] > 0 )
        {
            $node = eZContentObjectTreeNode::fetch( (int)$options['subtree'], false, false );
            $path = is_array( $node ) && isset( $node['path_string'] ) ? (string)$node['path_string'] : '/-1/';
            $where[] = "o.id IN (SELECT t.contentobject_id FROM ezcontentobject_tree t WHERE t.path_string LIKE '"
                     . $db->escapeString( $path ) . "%')";
        }
        if ( isset( $options['node'] ) && (int)$options['node'] > 0 )
            $where[] = 'o.id IN (SELECT t.contentobject_id FROM ezcontentobject_tree t WHERE t.node_id = ' . (int)$options['node'] . ')';
        return array( $joins, $where );
    }

    /**
     * How many images a resync scope has.
     *
     * @param array $options
     * @return int
     */
    public static function countImages( array $options )
    {
        list( $joins, $where ) = self::scopeSql( $options );
        $rows = eZDB::instance()->arrayQuery( 'SELECT COUNT(*) AS count FROM ezcontentobject_attribute a JOIN ezcontentobject o ON o.id = a.contentobject_id'
                                              . $joins . ' WHERE ' . implode( ' AND ', $where ) );
        $count = isset( $rows[0]['count'] ) ? (int)$rows[0]['count'] : 0;
        return isset( $options['limit'] ) && (int)$options['limit'] > 0 ? min( $count, (int)$options['limit'] ) : $count;
    }

    /**
     * The images of a resync scope, in pages of 100: label, attribute, version, language, files (alias => path).
     *
     * @param array $options
     * @return Generator
     */
    public static function imageFiles( array $options )
    {
        list( $joins, $where ) = self::scopeSql( $options );
        $db = eZDB::instance();
        $limit = isset( $options['limit'] ) && (int)$options['limit'] > 0 ? (int)$options['limit'] : 0;
        $given = 0;
        $offset = 0;
        while ( true )
        {
            $rows = $db->arrayQuery( 'SELECT a.id, a.version, a.language_code, a.contentobject_id, a.data_text FROM ezcontentobject_attribute a'
                                     . ' JOIN ezcontentobject o ON o.id = a.contentobject_id' . $joins . ' WHERE ' . implode( ' AND ', $where )
                                     . ' ORDER BY a.id, a.language_code', array( 'offset' => $offset, 'limit' => 100 ) );
            if ( !$rows )
                return;
            foreach ( $rows as $row )
            {
                yield array( 'label' => 'object ' . (int)$row['contentobject_id'] . ' attribute ' . (int)$row['id'] . '-' . (int)$row['version'] . '-' . $row['language_code'],
                             'attribute' => (int)$row['id'], 'version' => (int)$row['version'], 'language' => (string)$row['language_code'],
                             'files' => self::filesOfXml( (string)$row['data_text'] ) );
                if ( $limit > 0 && ++$given >= $limit )
                    return;
            }
            if ( count( $rows ) < 100 )
                return;
            $offset += 100;
        }
    }
}
