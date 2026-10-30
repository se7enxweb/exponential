<?php
/**
 * The rate limits of the GraphQL API (graphql.ini RateLimit, RateLimitAnonymous, RateLimitWindow): how many requests
 * a user, a personal API key or (anonymous) an IP address may send in a window of time. A fixed window per identity,
 * counted in a small file under var/cache/graphql/rate/ with an exclusive lock, so every PHP-FPM worker and every
 * Velocity worker counts the same requests, without a database connection and without shared memory.
 *
 * Plain PHP with no kernel class: the response cache uses it at the very start of index_rest.php, before the
 * settings are read, to count a request it answers from the cache.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expGraphQLRateLimiter
{
    /** @var string */
    protected $dir;

    /**
     * @param string $dir the directory of the counters
     */
    public function __construct( $dir )
    {
        $this->dir = rtrim( (string)$dir, '/' );
    }

    /**
     * @param string $root the installation root
     * @return string the directory of the counters of an installation
     */
    public static function directoryOf( $root )
    {
        return rtrim( (string)$root, '/' ) . '/var/cache/graphql/rate';
    }

    /**
     * Counts a request of an identity, unless it is over the limit.
     *
     * @param string $identity user:14, key:3, ip:192.0.2.1
     * @param int $limit requests per window; 0 or less: no limit
     * @param int $window seconds
     * @param int|null $now
     * @return array allowed (bool), limit, remaining, reset (seconds until the window ends)
     */
    public function hit( $identity, $limit, $window, $now = null )
    {
        $limit = (int)$limit;
        $window = max( 1, (int)$window );
        $now = $now === null ? time() : (int)$now;
        if ( $limit <= 0 )
            return array( 'allowed' => true, 'limit' => 0, 'remaining' => 0, 'reset' => 0 );
        $key = sha1( (string)$identity );
        $dir = $this->dir . '/' . substr( $key, 0, 2 );
        if ( !self::makeDirectory( $dir ) )
            return array( 'allowed' => true, 'limit' => $limit, 'remaining' => $limit, 'reset' => $window ); // no place to count: never lock anybody out
        $file = $dir . '/' . $key;
        $new = !is_file( $file );
        $handle = @fopen( $file, 'c+' );
        if ( $new && $handle !== false )
            @chmod( $file, self::fileMode( 0666 ) ); // counted by the site's PHP-FPM user and by Velocity's alike
        if ( $handle === false )
            return array( 'allowed' => true, 'limit' => $limit, 'remaining' => $limit, 'reset' => $window );
        flock( $handle, LOCK_EX );
        $line = (string)stream_get_contents( $handle );
        $start = $now;
        $count = 0;
        if ( preg_match( '/^(\d+) (\d+)$/', trim( $line ), $m ) && (int)$m[1] + $window > $now )
        {
            $start = (int)$m[1];
            $count = (int)$m[2];
        }
        $allowed = $count < $limit;
        if ( $allowed )
        {
            $count++;
            ftruncate( $handle, 0 );
            rewind( $handle );
            fwrite( $handle, $start . ' ' . $count );
            fflush( $handle );
        }
        flock( $handle, LOCK_UN );
        fclose( $handle );
        if ( mt_rand( 1, 2000 ) === 1 )
            $this->prune( $window, $now );
        return array( 'allowed' => $allowed, 'limit' => $limit, 'remaining' => max( 0, $limit - $count ), 'reset' => max( 1, $start + $window - $now ) );
    }

    /**
     * @param int $mode
     * @return int $mode within EZP_FILE_MODE_MAX (expGraphQLResponseCache::fileMode(), loaded with this class early)
     */
    protected static function fileMode( $mode )
    {
        if ( !class_exists( 'expGraphQLResponseCache', false ) )
            require_once __DIR__ . '/expgraphqlresponsecache.php';
        return expGraphQLResponseCache::fileMode( $mode );
    }

    /**
     * @param int $mode
     * @return int $mode within EZP_DIR_MODE_MAX (expGraphQLResponseCache::dirMode())
     */
    protected static function dirMode( $mode )
    {
        if ( !class_exists( 'expGraphQLResponseCache', false ) )
            require_once __DIR__ . '/expgraphqlresponsecache.php';
        return expGraphQLResponseCache::dirMode( $mode );
    }

    /**
     * Makes a directory (and its parents) every process of the site can write in (PHP-FPM and Velocity run as
     * different users).
     *
     * @param string $dir
     * @return bool
     */
    protected static function makeDirectory( $dir )
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
     * Removes the counters of one of the 256 directories whose window ended long ago (an address that came once).
     *
     * @param int $window
     * @param int $now
     * @return int how many were removed
     */
    public function prune( $window, $now = null )
    {
        $now = $now === null ? time() : (int)$now;
        $dir = $this->dir . '/' . sprintf( '%02x', mt_rand( 0, 255 ) );
        $removed = 0;
        foreach ( is_dir( $dir ) ? (array)glob( $dir . '/*' ) : array() as $file )
        {
            $modified = @filemtime( $file );
            if ( $modified !== false && $modified < $now - 2 * max( 1, (int)$window ) && @unlink( $file ) )
                $removed++;
        }
        return $removed;
    }

    /**
     * Counts a request of the current HTTP request (forRequest()) in the counters of the installation.
     *
     * @param string $authMode
     * @param eZUser|null $user
     * @param int|null $apiKeyId
     * @return array|null the answer of hit(), null when the request is not limited
     */
    public static function hitRequest( $authMode, $user, $apiKeyId )
    {
        $rate = self::forRequest( $authMode, $user, $apiKeyId, isset( $_SERVER['REMOTE_ADDR'] ) ? (string)$_SERVER['REMOTE_ADDR'] : '' );
        if ( $rate['identity'] === null || $rate['limit'] <= 0 )
            return null;
        $limiter = new self( self::directoryOf( expGraphQL::rootDir() ) );
        return $limiter->hit( $rate['identity'], $rate['limit'], $rate['window'] );
    }

    /**
     * The answer of a request over its limit (HTTP 429).
     *
     * @param array $hit
     * @return array
     */
    public static function refusal( array $hit )
    {
        return expGraphQLServer::refusal( expGraphQLMessage::tr( 'Too many requests: at most %limit in %window seconds. Try again in %reset seconds.',
                                                                 array( '%limit' => (int)$hit['limit'], '%window' => (int)expGraphQL::intSetting( 'RateLimitWindow', 60 ),
                                                                        '%reset' => (int)$hit['reset'] ) ), expGraphQLError::RATE_LIMITED );
    }

    /**
     * The headers that tell a client its limit.
     *
     * @param array $hit the answer of hit()
     * @return array name => value
     */
    public static function headers( array $hit )
    {
        if ( empty( $hit['limit'] ) )
            return array();
        $headers = array( 'X-RateLimit-Limit' => (string)$hit['limit'], 'X-RateLimit-Remaining' => (string)$hit['remaining'],
                          'X-RateLimit-Reset' => (string)$hit['reset'] );
        if ( !$hit['allowed'] )
            $headers['Retry-After'] = (string)$hit['reset'];
        return $headers;
    }

    /**
     * The identity and the limit of a request (graphql.ini): a personal API key by its id, a signed-in user by its
     * id, an anonymous request by its IP address.
     *
     * @param string $authMode an AUTH_* constant of expGraphQLContext
     * @param eZUser|null $user
     * @param int|null $apiKeyId
     * @param string $address the client's IP address
     * @return array identity, limit, window; null identity: not limited (the console)
     */
    public static function forRequest( $authMode, $user, $apiKeyId, $address )
    {
        $window = expGraphQL::intSetting( 'RateLimitWindow', 60 );
        if ( $authMode === expGraphQLContext::AUTH_CONSOLE )
            return array( 'identity' => null, 'limit' => 0, 'window' => $window );
        if ( $authMode === expGraphQLContext::AUTH_ANONYMOUS || !$user instanceof eZUser || $user->isAnonymous() )
            return array( 'identity' => 'ip:' . (string)$address, 'limit' => (int)expGraphQL::setting( 'RateLimitAnonymous', 120 ), 'window' => $window );
        $identity = $apiKeyId ? 'key:' . (int)$apiKeyId : 'user:' . (int)$user->id();
        return array( 'identity' => $identity, 'limit' => (int)expGraphQL::setting( 'RateLimit', 600 ), 'window' => $window );
    }
}

?>
