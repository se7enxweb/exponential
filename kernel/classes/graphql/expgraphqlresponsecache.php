<?php
/**
 * The response cache of the GraphQL API (graphql.ini ResponseCache, ResponseCacheTtl; doc/eng-us/features/6.0/
 * graphql.md, "Speed"): the answers of persisted queries, kept per siteaccess, schema, user rights (the role hash, as
 * the view cache keys them) and variables, and expired whenever content is published or the view caches are cleared
 * (eZContentCacheManager), when a class changes, when persisted queries are stored or removed, and by
 * ezcache --clear-id=graphql (tags content and ini).
 *
 * An anonymous GET of a persisted query (?id=..., or Apollo's extensions) is answered at the very start of
 * index_rest.php by serveEarly(), before autoload, settings, siteaccess, session or database: its key is made from
 * the raw request (host, scheme, address, query string, X-Siteaccess), which is all that decides its answer. That
 * part is plain PHP and needs no kernel class. Everything else (a signed-in request, a POST) is looked up by the
 * controller once the user is known.
 *
 * An entry is one file, var/cache/graphql/responses/<2>/<key>: a line of JSON (when it was made, until when it holds,
 * its ETag, schema hash and the anonymous rate limit) and the answer. var/cache/graphql/expired holds the time of the
 * last expiry; an entry made before it is stale. Nothing is kept in memory between requests.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expGraphQLResponseCache
{
    /** The format of an entry; an entry of another format is a miss. */
    const FORMAT = 1;

    /** @var string var/cache/graphql of the installation */
    protected $dir;

    /**
     * @param string $dir
     */
    public function __construct( $dir )
    {
        $this->dir = rtrim( (string)$dir, '/' );
    }

    /**
     * @param string $root the installation root
     * @return string
     */
    public static function directoryOf( $root )
    {
        return rtrim( (string)$root, '/' ) . '/var/cache/graphql';
    }

    /**
     * @return self the cache of this installation
     */
    public static function forInstallation()
    {
        return new self( self::directoryOf( expGraphQL::rootDir() ) );
    }

    /** @return string */
    public function directory()
    {
        return $this->dir;
    }

    /**
     * The key of an anonymous GET of a persisted query, from the raw request alone.
     *
     * @param array $server $_SERVER
     * @return string|null null: not a request the early cache answers (not a GET, credentials, a CORS request, not
     *                     /api/graphql, not a persisted query)
     */
    public static function anonymousKey( array $server )
    {
        $shape = self::requestShape( $server );
        if ( $shape === null || self::credential( $server ) !== null )
            return null;
        return sha1( json_encode( array( self::FORMAT, 'anonymous', $shape ) ) );
    }

    /**
     * The key of a signed-in GET of a persisted query: the raw request and the rights of its user.
     *
     * @param array $server $_SERVER
     * @param string $roleHash
     * @return string|null null: not a request the early cache answers
     */
    public static function signedInKey( array $server, $roleHash )
    {
        $shape = self::requestShape( $server );
        if ( $shape === null || self::credential( $server ) === null )
            return null;
        return sha1( json_encode( array( self::FORMAT, 'signed-in', (string)$roleHash, $shape ) ) );
    }

    /**
     * What decides the answer of a GET of a persisted query, from the raw request: scheme, host, path, query string,
     * X-Siteaccess and the Origin of a CORS request (its Access-Control headers are kept with the answer).
     *
     * @param array $server
     * @return array|null null: not a GET of a persisted query at /api/graphql, or HTTP basic authentication
     */
    protected static function requestShape( array $server )
    {
        if ( !isset( $server['REQUEST_METHOD'] ) || $server['REQUEST_METHOD'] !== 'GET' || !empty( $server['PHP_AUTH_USER'] ) )
            return null;
        $uri = isset( $server['REQUEST_URI'] ) ? (string)$server['REQUEST_URI'] : '';
        $path = (string)parse_url( $uri, PHP_URL_PATH );
        if ( !preg_match( '#/api/graphql/?$#', $path ) )
            return null;
        $query = isset( $server['QUERY_STRING'] ) ? (string)$server['QUERY_STRING'] : (string)parse_url( $uri, PHP_URL_QUERY );
        if ( !preg_match( '/(^|&)(id|extensions)=/', $query ) )
            return null;
        $https = !empty( $server['HTTPS'] ) && strtolower( (string)$server['HTTPS'] ) !== 'off';
        return array( $https, isset( $server['HTTP_HOST'] ) ? strtolower( (string)$server['HTTP_HOST'] ) : '', $path, $query,
                      isset( $server['HTTP_X_SITEACCESS'] ) ? (string)$server['HTTP_X_SITEACCESS'] : '',
                      isset( $server['HTTP_ORIGIN'] ) ? strtolower( (string)$server['HTTP_ORIGIN'] ) : '' );
    }

    /**
     * @param array $server
     * @return string|null the SHA-256 of the credentials a request carries (an OAuth token or a personal API key, in
     *                     Authorization or X-Api-Key), null without
     */
    public static function credential( array $server )
    {
        foreach ( array( 'HTTP_AUTHORIZATION', 'REDIRECT_HTTP_AUTHORIZATION', 'HTTP_X_API_KEY' ) as $name )
        {
            if ( !empty( $server[$name] ) )
                return hash( 'sha256', $name === 'HTTP_X_API_KEY' ? 'key ' . trim( (string)$server[$name] ) : trim( (string)$server[$name] ) );
        }
        return null;
    }

    /**
     * What the kernel learnt about credentials when it checked them: the rights of their user and the rate limit, so
     * that the early answer can serve that user's cached answers without the kernel. Kept CredentialCacheTtl seconds,
     * and forgotten whenever an API key, a role or a user's cached rights change (forgetCredentials()).
     *
     * @param string $credential credential()
     * @param array $data roleHash, identity, rateLimit, rateWindow, ttl
     * @return bool
     */
    public function writeCredential( $credential, array $data )
    {
        $ttl = isset( $data['ttl'] ) ? (int)$data['ttl'] : 0;
        if ( $ttl <= 0 )
            return false;
        unset( $data['ttl'] );
        $now = microtime( true );
        return self::writeFile( $this->dir . '/credentials/' . substr( $credential, 0, 2 ) . '/' . $credential,
                                json_encode( array( 'format' => self::FORMAT, 'created' => $now, 'expires' => $now + $ttl ) + $data ) );
    }

    /**
     * @param string $credential
     * @param float|null $now
     * @return array|null roleHash, identity, rateLimit, rateWindow; null: unknown or no longer valid
     */
    public function readCredential( $credential, $now = null )
    {
        $data = json_decode( (string)@file_get_contents( $this->dir . '/credentials/' . substr( $credential, 0, 2 ) . '/' . $credential ), true );
        if ( !is_array( $data ) || !isset( $data['format'], $data['created'], $data['expires'], $data['roleHash'] ) || $data['format'] !== self::FORMAT )
            return null;
        $now = $now === null ? microtime( true ) : (float)$now;
        $forgotten = @file_get_contents( $this->dir . '/credentials-forgotten' );
        if ( $now >= (float)$data['expires'] || ( is_string( $forgotten ) && (float)$data['created'] <= (float)$forgotten ) )
            return null;
        return $data;
    }

    /**
     * Forgets every credential the early answer knows: the next request with credentials is checked by the kernel.
     *
     * @return bool
     */
    public function forgetCredentials()
    {
        if ( !is_dir( $this->dir ) )
            return true;
        return self::writeFile( $this->dir . '/credentials-forgotten', sprintf( '%.6F', microtime( true ) ) );
    }

    /**
     * forgetCredentials() for this installation when it has a cache (an API key, a role or a user's rights changed).
     */
    public static function forgetCredentialsIfAvailable()
    {
        ( new self( self::directoryOf( dirname( __DIR__, 3 ) ) ) )->forgetCredentials();
    }

    /**
     * $mode within EZP_FILE_MODE_MAX (config.php; doc/eng-us/bc/6.0/file-modes.md), as eZFile::fileMode(): eZFile
     * answers when it is loaded; before the autoloader (the early answer of index_rest.php) the same rule is applied
     * here, as ezpHttpCacheContract does.
     *
     * @param int $mode
     * @return int
     */
    public static function fileMode( $mode )
    {
        if ( class_exists( 'eZFile', false ) )
            return eZFile::fileMode( $mode );
        $limit = self::modeLimit( 'EZP_FILE_MODE_MAX', 0600 );
        if ( $limit === null )
        {
            $dirLimit = self::modeLimit( 'EZP_DIR_MODE_MAX', 0700 );
            $limit = $dirLimit === null ? null : ( $dirLimit & 0666 );
        }
        return $limit === null ? (int)$mode : ( (int)$mode & $limit );
    }

    /**
     * $mode within EZP_DIR_MODE_MAX, as eZDir::dirMode(); see fileMode().
     *
     * @param int $mode
     * @return int
     */
    public static function dirMode( $mode )
    {
        if ( class_exists( 'eZDir', false ) && class_exists( 'eZFile', false ) )
            return eZDir::dirMode( $mode );
        $limit = self::modeLimit( 'EZP_DIR_MODE_MAX', 0700 );
        if ( $limit === null )
        {
            $fileLimit = self::modeLimit( 'EZP_FILE_MODE_MAX', 0600 );
            $limit = $fileLimit === null ? null : ( $fileLimit | ( ( $fileLimit & 0444 ) >> 2 ) );
        }
        return $limit === null ? (int)$mode : ( (int)$mode & $limit );
    }

    /**
     * The limit the constant $name sets, as eZFile reads it: an integer mode or a string of octal digits that leaves
     * the owner $ownerBits, else null (no limit).
     *
     * @param string $name
     * @param int $ownerBits
     * @return int|null
     */
    private static function modeLimit( $name, $ownerBits )
    {
        if ( !defined( $name ) )
            return null;
        $value = constant( $name );
        $mode = null;
        if ( is_int( $value ) )
            $mode = $value >= 0 && $value <= 0777 ? $value : ( preg_match( '/^[0-7]{3}$/', (string)$value ) ? octdec( (string)$value ) : null );
        else if ( is_string( $value ) && preg_match( '/^0*([0-7]{1,3})$/', trim( $value ), $match ) )
            $mode = octdec( $match[1] );
        return $mode !== null && ( $mode & $ownerBits ) === $ownerBits ? $mode : null;
    }

    /**
     * Makes a directory (and its parents) that every process of the site can write in: PHP-FPM runs as the site's
     * user and Velocity as another, and both write answers, counters and stamps here (as var/ is shared, 0777 like
     * site.ini [FileSettings] DirectoryPermissions).
     *
     * @param string $dir
     * @return bool
     */
    public static function makeDirectory( $dir )
    {
        if ( is_dir( $dir ) )
            return true;
        $parent = dirname( $dir );
        if ( $parent !== $dir && !is_dir( $parent ) && !self::makeDirectory( $parent ) )
            return false;
        if ( !@mkdir( $dir, self::dirMode( 0777 ) ) && !is_dir( $dir ) )
            return false;
        @chmod( $dir, self::dirMode( 0777 ) );
        return true;
    }

    /**
     * Writes a file under a temporary name and renames it.
     *
     * @param string $file
     * @param string $content
     * @return bool
     */
    protected static function writeFile( $file, $content )
    {
        $dir = dirname( $file );
        if ( !self::makeDirectory( $dir ) )
            return false;
        $tmp = $dir . '/.' . basename( $file ) . '.' . getmypid() . '.' . mt_rand() . '.tmp';
        if ( @file_put_contents( $tmp, $content ) === false || !@chmod( $tmp, self::fileMode( 0666 ) ) || !@rename( $tmp, $file ) )
        {
            @unlink( $tmp );
            return false;
        }
        return true;
    }

    /**
     * The key of a persisted query run by a user (and of an anonymous one the early cache does not answer).
     *
     * @param array $parts siteaccess, schema hash, role hash, persisted hash, variables, operation name, base URL
     * @return string
     */
    public static function userKey( array $parts )
    {
        return sha1( json_encode( array( self::FORMAT, 'user', $parts ) ) );
    }

    /**
     * @param eZUser $user
     * @return string the rights of a user, as the view cache keys them: its roles and the values of its limited
     *                role assignments
     */
    public static function roleHash( eZUser $user )
    {
        return sha1( implode( '.', (array)$user->roleIDList() ) . '|' . implode( '.', (array)$user->limitValueList() ) );
    }

    /**
     * @param string $key
     * @return string
     */
    protected function file( $key )
    {
        return $this->dir . '/responses/' . substr( $key, 0, 2 ) . '/' . $key;
    }

    /**
     * @param string $key
     * @param int|null $now
     * @return array|null body, etag, schemaHash, created, expires, rateLimit, rateWindow; null: no current entry
     */
    public function read( $key, $now = null )
    {
        $content = @file_get_contents( $this->file( $key ) );
        if ( !is_string( $content ) || ( $newline = strpos( $content, "\n" ) ) === false )
            return null;
        $meta = json_decode( substr( $content, 0, $newline ), true );
        if ( !is_array( $meta ) || !isset( $meta['format'], $meta['created'], $meta['expires'] ) || $meta['format'] !== self::FORMAT )
            return null;
        $now = $now === null ? microtime( true ) : (float)$now;
        if ( (float)$meta['created'] <= $this->expiredAt() || ( $meta['expires'] > 0 && $now >= (float)$meta['expires'] ) )
            return null;
        $meta['body'] = substr( $content, $newline + 1 );
        return $meta;
    }

    /**
     * @param string $key
     * @param string $body the JSON answer
     * @param array $meta etag, schemaHash, ttl (seconds; 0: until expired), rateLimit, rateWindow
     * @param float|null $now
     * @return bool
     */
    public function write( $key, $body, array $meta, $now = null )
    {
        $now = $now === null ? microtime( true ) : (float)$now;
        $ttl = isset( $meta['ttl'] ) ? (int)$meta['ttl'] : 0;
        unset( $meta['ttl'] );
        $meta = array( 'format' => self::FORMAT, 'created' => $now, 'expires' => $ttl > 0 ? $now + $ttl : 0 ) + $meta;
        $file = $this->file( $key );
        $dir = dirname( $file );
        if ( !self::makeDirectory( $dir ) )
            return false;
        $tmp = $dir . '/.' . $key . '.' . getmypid() . '.' . mt_rand() . '.tmp';
        if ( @file_put_contents( $tmp, json_encode( $meta, JSON_UNESCAPED_SLASHES ) . "\n" . $body ) === false || !@chmod( $tmp, self::fileMode( 0666 ) ) || !@rename( $tmp, $file ) )
        {
            @unlink( $tmp );
            return false;
        }
        return true;
    }

    /**
     * @return float when the cache was last expired (0: never)
     */
    public function expiredAt()
    {
        $stamp = @file_get_contents( $this->dir . '/expired' );
        return is_string( $stamp ) ? (float)$stamp : 0.0;
    }

    /**
     * Expires every entry made until now.
     *
     * @return bool
     */
    public function expire()
    {
        if ( !self::makeDirectory( $this->dir ) )
            return false;
        $tmp = $this->dir . '/.expired.' . getmypid() . '.' . mt_rand() . '.tmp';
        if ( @file_put_contents( $tmp, sprintf( '%.6F', microtime( true ) ) ) === false || !@chmod( $tmp, self::fileMode( 0666 ) ) || !@rename( $tmp, $this->dir . '/expired' ) )
        {
            @unlink( $tmp );
            return false;
        }
        return true;
    }

    /**
     * Expires the cache of this installation when it has one (an installation that never used the GraphQL API gets
     * no file). Called by eZContentCacheManager whenever view caches are cleared, and on class and persisted query
     * changes.
     */
    public static function expireIfAvailable()
    {
        $dir = self::directoryOf( dirname( __DIR__, 3 ) );
        if ( is_dir( $dir ) )
            ( new self( $dir ) )->expire();
    }

    /**
     * Removes every entry (ezcache --clear-id=graphql) and expires what a worker may still be writing.
     *
     * @return int how many entries were removed
     */
    public function clear()
    {
        $removed = 0;
        foreach ( is_dir( $this->dir . '/responses' ) ? (array)glob( $this->dir . '/responses/*', GLOB_ONLYDIR ) : array() as $sub )
        {
            foreach ( (array)glob( $sub . '/*' ) as $file )
            {
                if ( @unlink( $file ) )
                    $removed++;
            }
            @rmdir( $sub );
        }
        $this->expire();
        return $removed;
    }

    /**
     * The cache item of ezcache (kernel/classes/ezcache.php): the answers and the class catalogues of the schema.
     *
     * @param array $cacheItem
     */
    public static function clearCacheItem( $cacheItem = array() )
    {
        $cache = new self( self::directoryOf( dirname( __DIR__, 3 ) ) );
        $cache->clear();
        $catalogues = class_exists( 'eZSys', false ) ? eZSys::cacheDirectory() . '/graphql' : null;
        foreach ( $catalogues !== null && is_dir( $catalogues ) ? (array)glob( $catalogues . '/catalogue-*.php' ) : array() as $file )
            @unlink( $file );
    }

    /**
     * Answers an anonymous GET of a persisted query from the cache, before anything of the kernel is loaded. Counts
     * the request against the anonymous rate limit stored with the entry; over it, or without an entry, the request
     * goes on to the kernel, which answers it (429, or the query).
     *
     * @param string $root the installation root
     * @param array $server $_SERVER
     * @return bool the request was answered
     */
    public static function serveEarly( $root, array $server )
    {
        $cache = new self( self::directoryOf( $root ) );
        $credential = self::credential( $server );
        if ( $credential === null )
        {
            $key = self::anonymousKey( $server );
            $identity = 'ip:' . ( isset( $server['REMOTE_ADDR'] ) ? (string)$server['REMOTE_ADDR'] : '' );
            $rate = null;
        }
        else
        {
            // credentials the kernel checked a moment ago: their user's rights and rate limit
            $known = $cache->readCredential( $credential );
            if ( $known === null )
                return false;
            $key = self::signedInKey( $server, $known['roleHash'] );
            $identity = isset( $known['identity'] ) ? (string)$known['identity'] : '';
            $rate = array( isset( $known['rateLimit'] ) ? (int)$known['rateLimit'] : 0, isset( $known['rateWindow'] ) ? (int)$known['rateWindow'] : 60 );
        }
        if ( $key === null )
            return false;
        $entry = $cache->read( $key );
        if ( $entry === null )
            return false;
        if ( $rate === null )
            $rate = array( !empty( $entry['rateLimit'] ) ? (int)$entry['rateLimit'] : 0, isset( $entry['rateWindow'] ) ? (int)$entry['rateWindow'] : 60 );
        $rateHeaders = array();
        if ( $rate[0] > 0 )
        {
            if ( !class_exists( 'expGraphQLRateLimiter', false ) )
                require_once __DIR__ . '/expgraphqlratelimiter.php';
            $limiter = new expGraphQLRateLimiter( expGraphQLRateLimiter::directoryOf( $root ) );
            $hit = $limiter->hit( $identity, $rate[0], $rate[1] );
            if ( !$hit['allowed'] )
                return false;
            $rateHeaders = expGraphQLRateLimiter::headers( $hit );
        }
        $notModified = isset( $server['HTTP_IF_NONE_MATCH'] ) && self::etagMatches( (string)$server['HTTP_IF_NONE_MATCH'], (string)$entry['etag'] );
        http_response_code( $notModified ? 304 : 200 );
        foreach ( self::headers( $entry, 'HIT', $credential === null ) + $rateHeaders as $name => $value )
            header( "$name: $value" );
        // the Access-Control headers rest.ini [CorsSettings] gave this Origin when the answer was made
        foreach ( isset( $entry['cors'] ) && is_array( $entry['cors'] ) ? $entry['cors'] : array() as $line )
            header( (string)$line, false );
        if ( !$notModified )
            echo $entry['body'];
        return true;
    }

    /**
     * @param array $entry etag, schemaHash
     * @param string $state HIT, MISS or BYPASS
     * @param bool $anonymous
     * @return array the headers of a cached answer
     */
    public static function headers( array $entry, $state, $anonymous )
    {
        return array( 'Content-Type' => 'application/json; charset=UTF-8',
                      'ETag' => (string)$entry['etag'],
                      'Cache-Control' => $anonymous ? 'public, max-age=0, must-revalidate' : 'private, no-cache',
                      'X-GraphQL-Schema' => (string)$entry['schemaHash'],
                      'X-GraphQL-Cache' => (string)$state,
                      'Vary' => 'Origin, Authorization' );
    }

    /**
     * @param string $body
     * @return string the ETag of an answer
     */
    public static function etag( $body )
    {
        return '"' . substr( sha1( (string)$body ), 0, 32 ) . '"';
    }

    /**
     * @param string $ifNoneMatch the If-None-Match header
     * @param string $etag
     * @return bool
     */
    public static function etagMatches( $ifNoneMatch, $etag )
    {
        if ( trim( $ifNoneMatch ) === '*' )
            return true;
        // If-None-Match compares weakly (RFC 9110): W/"x" matches "x"
        if ( strncmp( $etag, 'W/', 2 ) === 0 )
            $etag = substr( $etag, 2 );
        foreach ( explode( ',', $ifNoneMatch ) as $candidate )
        {
            $candidate = trim( $candidate );
            if ( strncmp( $candidate, 'W/', 2 ) === 0 )
                $candidate = substr( $candidate, 2 );
            if ( $candidate !== '' && $candidate === $etag )
                return true;
        }
        return false;
    }

    /**
     * Whether the answer of a query may be kept: no errors, and no answer that names the user it ran as when it is
     * kept for every user of the same rights.
     *
     * @param array $answer
     * @param string $query
     * @param bool $signedIn
     * @param string|null $operation the operation that ran (query, mutation): the answer of a mutation is never kept
     * @return bool
     */
    public static function storable( array $answer, $query, $signedIn, $operation = null )
    {
        if ( !empty( $answer['errors'] ) || !isset( $answer['data'] ) )
            return false;
        if ( $operation === 'mutation' || ( $operation === null && preg_match( '/^\s*mutation\b/m', (string)$query ) ) )
            return false;
        return !$signedIn || !preg_match( '/\bcurrentUser\b/', (string)$query );
    }
}

?>
