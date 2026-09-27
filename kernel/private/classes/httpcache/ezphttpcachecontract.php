<?php
/**
 * File containing the ezpHttpCacheContract class.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

/**
 * The role-aware HTTP cache contract: keys, context hashes, the entry and
 * record formats, tag validation and placeholder substitution.
 *
 * Deliberately free of every other Exponential class, INI and database: the
 * same file is used by the early exit (config.php, before the autoloader), by
 * the kernel's store path and by Exponential Velocity's parent process, so the
 * three produce the same keys and the same bytes by construction.
 *
 * Storage, under $config['dir']:
 *   contract.php          the configuration (written by the kernel)
 *   state.ser             { generation, genTime, tags: { tag => purgeTime } }
 *   e/<kk>/<key>.meta     entry metadata (JSON)
 *   e/<kk>/<key>.<h>.body entry body (h = start of its sha1), placeholders removed
 *   u/<uu>/<userID>.rec   user context record (JSON)
 *
 * See doc/bc/6.0/httpcache.md.
 */
class ezpHttpCacheContract
{
    const FORMAT = 2;

    /** Per-user limitation identifiers that make a context private. */
    const PER_USER_LIMITATIONS = array( 'Owner', 'ParentOwner', 'Group', 'ParentGroup', 'User_Section', 'User_Subtree' );

    /** @var array */
    public $config;

    /** @var array|null state read for this request */
    private $state = null;

    /** @var array hits/misses/reasons for the debug header */
    public $lastReason = '';

    public function __construct( array $config )
    {
        $this->config = $config + array(
            'enabled' => false, 'secret' => '', 'dir' => '', 'hosts' => array(),
            'sessionCookie' => array(), 'sessionSavePath' => '', 'formTokenSecret' => '',
            'formTokenIntention' => 'legacy', 'maxAge' => 3600, 'swr' => 60,
            'proxyHeaders' => true, 'apcu' => true, 'maxBodySize' => 2097152,
            'queryParameters' => array(),
        );
    }

    /**
     * The contract for the configuration file under $dir, or null when there
     * is none or it is switched off.
     */
    public static function fromDir( $dir )
    {
        $file = rtrim( $dir, '/' ) . '/contract.php';
        if ( !is_file( $file ) )
            return null;
        $config = @include $file;
        if ( !is_array( $config ) || empty( $config['enabled'] ) || strlen( (string)$config['secret'] ) < 32 )
            return null;
        return new self( $config );
    }

    // ── Keys ─────────────────────────────────────────────────────────────

    public function hmac( $data )
    {
        return hash_hmac( 'sha256', $data, $this->config['secret'] );
    }

    /** Context shared by users with the same roles and assignment limitations. */
    public function roleContext( array $roleIDs, array $limitValues, $siteaccess )
    {
        $roleIDs = array_map( 'intval', $roleIDs );
        sort( $roleIDs );
        $limitValues = array_map( 'strval', $limitValues );
        sort( $limitValues );
        return $this->hmac( 'r:' . implode( ',', $roleIDs ) . '|l:' . implode( ',', $limitValues )
            . '|sa:' . $siteaccess . '|g:' . $this->generation() );
    }

    /** Context of one user whose access depends on who they are. */
    public function privateContext( $userID, $siteaccess )
    {
        return $this->hmac( 'u:' . (int)$userID . '|sa:' . $siteaccess . '|g:' . $this->generation() );
    }

    public function anonymousContext( $siteaccess )
    {
        return $this->hmac( 'anon|sa:' . $siteaccess . '|g:' . $this->generation() );
    }

    /** Path and query, parameters sorted, no fragment. */
    public static function normalizeURI( $uri )
    {
        $uri = (string)$uri;
        $hash = strpos( $uri, '#' );
        if ( $hash !== false )
            $uri = substr( $uri, 0, $hash );
        $q = strpos( $uri, '?' );
        if ( $q === false )
            return $uri;
        $path = substr( $uri, 0, $q );
        $pairs = explode( '&', substr( $uri, $q + 1 ) );
        $pairs = array_filter( $pairs, 'strlen' );
        sort( $pairs, SORT_STRING );
        return $pairs ? $path . '?' . implode( '&', $pairs ) : $path;
    }

    /**
     * Whether a URI may be cached: no query string, or only parameters listed
     * in httpcache.ini QueryStringParameters[]. Anything else would let every
     * random query string add an entry (Exponential passes view parameters in
     * the path, /(offset)/10, so pages rarely need one).
     */
    public function queryAllowed( $uri )
    {
        $uri = (string)$uri;
        $q = strpos( $uri, '?' );
        if ( $q === false )
            return true;
        $hash = strpos( $uri, '#', $q );
        $query = $hash === false ? substr( $uri, $q + 1 ) : substr( $uri, $q + 1, $hash - $q - 1 );
        foreach ( explode( '&', $query ) as $pair )
        {
            if ( $pair === '' )
                continue;
            $name = urldecode( explode( '=', $pair, 2 )[0] );
            if ( !in_array( $name, (array)$this->config['queryParameters'], true ) )
                return false;
        }
        return true;
    }

    public function entryKey( $scheme, $host, $siteaccess, $uri, $context )
    {
        return $this->hmac( $scheme . '|' . strtolower( $host ) . '|' . $siteaccess . '|'
            . self::normalizeURI( $uri ) . '|' . $context );
    }

    // ── State: generation and tag purge times ────────────────────────────

    private function statePath()
    {
        return $this->config['dir'] . '/state.ser';
    }

    public function state()
    {
        if ( $this->state !== null )
            return $this->state;
        $raw = @file_get_contents( $this->statePath() );
        $state = $raw === false ? false : @unserialize( $raw, array( 'allowed_classes' => false ) );
        if ( !is_array( $state ) )
            $state = array( 'generation' => 1, 'genTime' => 0, 'tags' => array() );
        return $this->state = $state;
    }

    public function generation()
    {
        $s = $this->state();
        return (int)$s['generation'];
    }

    /** Apply $change to the state and write it atomically, under a lock. */
    public function updateState( $change )
    {
        $this->ensureDir( $this->config['dir'] );
        $lockPath = $this->config['dir'] . '/state.lock';
        $isNew = !is_file( $lockPath );
        $lock = @fopen( $lockPath, 'c' );
        if ( $lock )
        {
            if ( $isNew )
                $this->adopt( $lockPath );
            flock( $lock, LOCK_EX );
        }
        $this->state = null;
        $state = $this->state();
        $state = call_user_func( $change, $state );
        if ( !$this->atomicWrite( $this->statePath(), serialize( $state ) ) )
            error_log( 'exphttpcache: could not write ' . $this->statePath() . '; a purge is lost until entries expire' );
        $this->state = $state;
        if ( $lock )
        {
            flock( $lock, LOCK_UN );
            fclose( $lock );
        }
        return $state;
    }

    /** Everything stored before now becomes invalid; contexts change too. */
    public function bumpGeneration()
    {
        return $this->updateState( function ( $s ) {
            $s['generation'] = (int)$s['generation'] + 1;
            $s['genTime'] = microtime( true );
            $s['tags'] = array();
            return $s;
        } );
    }

    /** Entries carrying any of $tags become invalid. */
    public function purgeTags( array $tags )
    {
        if ( !$tags )
            return $this->state();
        $now = microtime( true );
        // A purge older than the longest entry life can no longer matter.
        $horizon = $now - (int)$this->config['maxAge'] - 1;
        return $this->updateState( function ( $s ) use ( $tags, $now, $horizon ) {
            foreach ( $s['tags'] as $t => $time )
            {
                if ( $time < $horizon )
                    unset( $s['tags'][$t] );
            }
            foreach ( $tags as $t )
                $s['tags'][(string)$t] = $now;
            return $s;
        } );
    }

    /** Whether an entry created at $created with $tags is still current. */
    public function isCurrent( $created, array $tags )
    {
        $s = $this->state();
        if ( $created <= $s['genTime'] )
            return false;
        foreach ( $tags as $t )
        {
            if ( isset( $s['tags'][$t] ) && $created <= $s['tags'][$t] )
                return false;
        }
        return true;
    }

    // ── Entries ──────────────────────────────────────────────────────────

    private function entryPath( $key, $ext )
    {
        return $this->config['dir'] . '/e/' . substr( $key, 0, 2 ) . '/' . $key . '.' . $ext;
    }

    /**
     * Store an entry. $placeholders: array of [offset, length, name] into $body,
     * where the value has already been cut out (length is the value's length
     * at store time, kept only for information).
     */
    /**
     * @param int|null $maxAge seconds, capped by the configured maxAge
     *        (a template's cache_ttl); null for the configured one
     */
    public function storeEntry( $key, $status, array $headers, $body, array $tags, array $placeholders, $maxAge = null )
    {
        if ( strlen( $body ) > $this->config['maxBodySize'] )
            return false;
        $limit = (int)$this->config['maxAge'];
        $meta = array(
            'v' => self::FORMAT, 'status' => (int)$status, 'headers' => $headers,
            'created' => microtime( true ),
            'maxAge' => $maxAge !== null && $maxAge > 0 ? min( (int)$maxAge, $limit ) : $limit,
            'swr' => (int)$this->config['swr'], 'tags' => array_values( array_unique( $tags ) ),
            'etag' => sha1( $body ), 'placeholders' => $placeholders, 'size' => strlen( $body ),
        );
        $this->ensureDir( dirname( $this->entryPath( $key, 'meta' ) ) );
        // The body is named after its hash, so metadata always points at its
        // own body: a reader holding the old metadata never gets the new body
        // (whose placeholder offsets differ). Body first, then the metadata.
        $old = json_decode( (string)@file_get_contents( $this->entryPath( $key, 'meta' ) ), true );
        $this->atomicWrite( $this->bodyPath( $key, $meta['etag'] ), $body );
        $this->atomicWrite( $this->entryPath( $key, 'meta' ), json_encode( $meta ) );
        if ( is_array( $old ) && isset( $old['etag'] ) && $old['etag'] !== $meta['etag'] )
            @unlink( $this->bodyPath( $key, $old['etag'] ) );
        if ( $this->apcuUsable() )
            @apcu_delete( 'exphttpcache:' . $key );
        // A render that refreshed an expired page is done.
        @unlink( $this->entryPath( $key, 'refresh' ) );
        $this->count( 'stores' );
        return $meta;
    }

    private function bodyPath( $key, $etag )
    {
        return $this->entryPath( $key, substr( (string)$etag, 0, 16 ) . '.body' );
    }

    /** [meta, body] for a current entry, or null. */
    public function loadEntry( $key )
    {
        $cached = $this->apcuUsable() ? @apcu_fetch( 'exphttpcache:' . $key ) : false;
        if ( is_array( $cached ) )
        {
            list( $meta, $body ) = $cached;
        }
        else
        {
            $raw = @file_get_contents( $this->entryPath( $key, 'meta' ) );
            if ( $raw === false )
                return $this->miss( 'no entry' );
            $meta = json_decode( $raw, true );
            if ( !is_array( $meta ) || ( $meta['v'] ?? 0 ) !== self::FORMAT )
                return $this->miss( 'bad entry' );
            $body = @file_get_contents( $this->bodyPath( $key, $meta['etag'] ?? '' ) );
            if ( $body === false || strlen( $body ) !== (int)$meta['size'] )
                return $this->miss( 'body missing' );
            if ( $this->apcuUsable() && strlen( $body ) <= 262144 )
                @apcu_store( 'exphttpcache:' . $key, array( $meta, $body ), (int)$meta['maxAge'] + (int)( $meta['swr'] ?? 0 ) );
        }
        // Purged first: a purge means the content changed, and that is never
        // served, not even stale.
        if ( !$this->isCurrent( $meta['created'], $meta['tags'] ) )
            return $this->miss( 'purged' );
        $age = microtime( true ) - $meta['created'];
        if ( $age > $meta['maxAge'] )
        {
            // Only out of date: within the stale-while-revalidate window it is
            // served as it is while one request renders it again.
            $swr = (int)( $meta['swr'] ?? 0 );
            if ( $swr <= 0 || $age > $meta['maxAge'] + $swr )
                return $this->miss( 'expired' );
            if ( $this->claimRefresh( $key ) )
                return $this->miss( 'expired, refreshing' );
            $meta['stale'] = true;
        }
        return array( $meta, $body );
    }

    /**
     * Whether this request is the one that renders an expired page again:
     * the first to create its refresh marker. A marker older than 30 seconds
     * belongs to a render that never finished and is taken over. A file, not
     * APCu, so Apache and Velocity (each with its own APCu) agree.
     */
    private function claimRefresh( $key )
    {
        $marker = $this->entryPath( $key, 'refresh' );
        $made = @filemtime( $marker );
        if ( $made !== false )
        {
            if ( time() - $made < 30 )
                return false;
            @unlink( $marker );
        }
        $fp = @fopen( $marker, 'x' );
        if ( !$fp )
            return false;
        fclose( $fp );
        $this->adopt( $marker );
        return true;
    }

    // ── User context records ─────────────────────────────────────────────

    private function recordPath( $userID )
    {
        $userID = (int)$userID;
        return $this->config['dir'] . '/u/' . ( $userID % 100 ) . '/' . $userID . '.rec';
    }

    public function storeRecord( $userID, $context, array $placeholders = array() )
    {
        $rec = array( 'ctx' => $context, 'gen' => $this->generation(), 'version' => sha1( $context . json_encode( $placeholders ) ),
            'placeholders' => $placeholders );
        $this->ensureDir( dirname( $this->recordPath( $userID ) ) );
        $this->atomicWrite( $this->recordPath( $userID ), json_encode( $rec ) );
        return $rec;
    }

    /** Forget a user's context record: their next request renders and records anew. */
    public function deleteRecord( $userID )
    {
        return @unlink( $this->recordPath( $userID ) );
    }

    public function loadRecord( $userID )
    {
        $raw = @file_get_contents( $this->recordPath( $userID ) );
        $rec = $raw === false ? null : json_decode( $raw, true );
        if ( !is_array( $rec ) || (int)( $rec['gen'] ?? 0 ) !== $this->generation() )
            return null;
        return $rec;
    }

    // ── Sessions (files handler) ─────────────────────────────────────────

    /** The user ID in a PHP session file, 0 for anonymous, null when unknown. */
    public function sessionUserID( $sessionID )
    {
        if ( !preg_match( '/^[a-zA-Z0-9,-]{22,256}$/', (string)$sessionID ) )
            return null;
        if ( $this->config['sessionSavePath'] === '' )
            return null;           // not a files handler: the early exit cannot know
        $raw = @file_get_contents( $this->config['sessionSavePath'] . '/sess_' . $sessionID );
        if ( $raw === false )
            return 0;              // no such session: anonymous
        if ( preg_match( '/(?:^|[;}])eZUserLoggedInID\|i:(\d+);/', $raw, $m ) )
            return (int)$m[1];
        if ( preg_match( '/(?:^|[;}])eZUserLoggedInID\|s:\d+:"(\d+)";/', $raw, $m ) )
            return (int)$m[1];
        return 0;
    }

    // ── Placeholders and serving ─────────────────────────────────────────

    public function formToken( $sessionID )
    {
        return sha1( $this->config['formTokenSecret'] . $this->config['formTokenIntention'] . $sessionID );
    }

    /** Values for the placeholders of an entry, or null when one is unresolvable. */
    public function placeholderValues( array $placeholders, $sessionID )
    {
        $values = array();
        foreach ( $placeholders as $p )
        {
            if ( $p[2] === 'form_token' )
            {
                if ( $sessionID === null )
                    return null;
                $values[$p[2]] = $this->formToken( $sessionID );
            }
            else
            {
                return null;       // unknown placeholder: fail safe
            }
        }
        return $values;
    }

    /** $body with every placeholder inserted at its offset. */
    public static function substitute( $body, array $placeholders, array $values )
    {
        if ( !$placeholders )
            return $body;
        $out = '';
        $pos = 0;
        foreach ( $placeholders as $p )
        {
            $out .= substr( $body, $pos, $p[0] - $pos ) . $values[$p[2]];
            $pos = $p[0];
        }
        return $out . substr( $body, $pos );
    }

    /**
     * The gzip form of a served page, without compressing the page per request.
     *
     * Compressing the whole page on every hit (gzencode) was the largest cost
     * of a hit: ~0.7 ms for the front page, and on a server that answers hits
     * in one process (Velocity's Q.web.appCache) a ceiling of ~630 signed-in
     * pages a second. The parts of the stored body between placeholders are
     * compressed once, each with a full flush so they stand alone, and kept in
     * APCu beside the entry; a request compresses only its placeholder values
     * (a form token: 40 bytes) and joins the pieces into one gzip member.
     * Without APCu it compresses the page as before.
     *
     * @param string $key the entry key
     * @param array $meta the entry's metadata (etag, placeholders, maxAge, swr)
     * @param string $body the stored body (placeholders removed)
     * @param array $values placeholder name => value
     * @param string $plain the page as served (substitute() of the above)
     * @return string
     */
    private function gzipEntry( $key, array $meta, $body, array $values, $plain )
    {
        if ( !$this->apcuUsable() || !function_exists( 'deflate_init' ) )
            return gzencode( $plain, 1 );
        $cacheKey = 'exphttpcache:gz:' . $key . ':' . ( $meta['etag'] ?? '' );
        $parts = @apcu_fetch( $cacheKey );
        if ( !is_array( $parts ) )
        {
            $parts = self::deflateParts( $body, $meta['placeholders'] );
            @apcu_store( $cacheKey, $parts, (int)( $meta['maxAge'] ?? 0 ) + (int)( $meta['swr'] ?? 0 ) );
        }
        return self::assembleGzip( $parts, $meta['placeholders'], $values, $plain );
    }

    /**
     * The parts of $body between placeholders, each compressed on its own
     * (raw deflate, ending in a full flush, so pieces can be joined in order).
     *
     * @return string[] one more than there are placeholders
     */
    public static function deflateParts( $body, array $placeholders )
    {
        $parts = array();
        $pos = 0;
        foreach ( $placeholders as $p )
        {
            $parts[] = self::rawDeflate( substr( $body, $pos, $p[0] - $pos ), 6 );
            $pos = $p[0];
        }
        $parts[] = self::rawDeflate( (string)substr( $body, $pos ), 6 );
        return $parts;
    }

    /**
     * One gzip member from pre-compressed parts and this request's values.
     * $plain is the uncompressed page, for the trailer's CRC-32 and length.
     */
    public static function assembleGzip( array $parts, array $placeholders, array $values, $plain )
    {
        // Header: deflate, no name or time, OS unknown.
        $gz = "\x1f\x8b\x08\x00\x00\x00\x00\x00\x00\xff";
        foreach ( $placeholders as $i => $p )
            $gz .= $parts[$i] . self::rawDeflate( (string)$values[$p[2]], 1 );
        $gz .= end( $parts );
        // An empty final block ends the deflate stream.
        $gz .= "\x03\x00";
        return $gz . pack( 'V', crc32( $plain ) & 0xFFFFFFFF ) . pack( 'V', strlen( $plain ) & 0xFFFFFFFF );
    }

    /** $data as raw deflate that stands alone: its own dictionary, ending in a full flush. */
    private static function rawDeflate( $data, $level )
    {
        $ctx = deflate_init( ZLIB_ENCODING_RAW, array( 'level' => $level ) );
        return deflate_add( $ctx, $data, ZLIB_FULL_FLUSH );
    }

    /**
     * Cut the given values out of $html, returning [body, placeholders]:
     * each occurrence of a value becomes a placeholder at its offset in the
     * returned body. Values must be long and unambiguous (a 40-hex token).
     */
    public static function extractPlaceholders( $html, array $values )
    {
        $found = array();
        foreach ( $values as $name => $value )
        {
            if ( $value === '' || strlen( $value ) < 16 )
                continue;
            $offset = 0;
            while ( ( $at = strpos( $html, $value, $offset ) ) !== false )
            {
                $found[] = array( $at, strlen( $value ), $name );
                $offset = $at + strlen( $value );
            }
        }
        usort( $found, function ( $a, $b ) { return $a[0] - $b[0]; } );
        $body = '';
        $pos = 0;
        $placeholders = array();
        foreach ( $found as $f )
        {
            $body .= substr( $html, $pos, $f[0] - $pos );
            $placeholders[] = array( strlen( $body ), $f[1], $f[2] );
            $pos = $f[0] + $f[1];
        }
        return array( $body . substr( $html, $pos ), $placeholders );
    }

    /**
     * The response for a request, or null for a miss. $request:
     *   scheme, host, uri, method, cookies (array), acceptEncoding, ifNoneMatch
     * Returns: [status, headers (array name => value), body]
     */
    public function serve( array $request )
    {
        $response = $this->lookup( $request );
        if ( $response !== null )
            $this->count( 'hits' );
        else if ( $this->lastReason !== 'host' && $this->lastReason !== 'method' )
        {
            // Other hosts (the admin host shares this code) and writes are
            // not cache traffic. The reason without detail keeps counters few.
            $this->count( 'misses' );
            $this->count( 'r:' . preg_replace( '/\s*\(.*$/', '', $this->lastReason !== '' ? $this->lastReason : 'no entry' ) );
        }
        return $response;
    }

    /**
     * Adds one to a statistics counter. Counted in APCu, which belongs to one
     * PHP pool or server; each writes its counters to stats/<id>.json at most
     * every 5 seconds so any of them can show the total (statistics()).
     */
    public function count( $name )
    {
        if ( !$this->apcuUsable() )
            return;
        $key = 'exphttpcache:stat:' . $name;
        if ( @apcu_inc( $key ) === false )
            @apcu_add( $key, 1 );
        if ( @apcu_add( 'exphttpcache:statflush', 1, 5 ) )
            $this->flushStatistics();
    }

    /** This APCu segment's counters: hits, misses, stores, reasons. */
    private function localStatistics()
    {
        $stats = array( 'hits' => 0, 'misses' => 0, 'stores' => 0, 'reasons' => array() );
        $info = @apcu_cache_info( false );
        foreach ( (array)( $info['cache_list'] ?? array() ) as $e )
        {
            $k = (string)( $e['info'] ?? $e['key'] ?? '' );
            if ( strncmp( $k, 'exphttpcache:stat:', 18 ) !== 0 )
                continue;
            $name = substr( $k, 18 );
            if ( strncmp( $name, 'r:', 2 ) === 0 )
                $stats['reasons'][substr( $name, 2 )] = (int)@apcu_fetch( $k );
            else if ( isset( $stats[$name] ) )
                $stats[$name] = (int)@apcu_fetch( $k );
        }
        return $stats;
    }

    /** A random identity for this APCu segment, made once. */
    private function statisticsID()
    {
        $id = @apcu_fetch( 'exphttpcache:statid' );
        if ( !is_string( $id ) )
        {
            @apcu_add( 'exphttpcache:statid', bin2hex( random_bytes( 6 ) ) );
            $id = (string)@apcu_fetch( 'exphttpcache:statid' );
        }
        return $id;
    }

    private function flushStatistics()
    {
        $dir = $this->config['dir'] . '/stats';
        $this->ensureDir( $dir );
        // A reset made from another server: start this one's counters again.
        $since = (int)@file_get_contents( $dir . '/since' );
        if ( $since > (int)@apcu_fetch( 'exphttpcache:statepoch' ) )
        {
            $this->clearLocalStatistics();
            @apcu_store( 'exphttpcache:statepoch', $since );
        }
        $stats = $this->localStatistics();
        $stats['sapi'] = PHP_SAPI;
        $stats['time'] = time();
        $this->atomicWrite( $dir . '/' . $this->statisticsID() . '.json', json_encode( $stats ) );
    }

    /**
     * Counters of every server since the last reset: hits, misses, stores,
     * reasons (reason => count), servers, since; null when none can count.
     * A server that has not written for a day is left out (restarted pools
     * start new files).
     */
    public function statistics()
    {
        $total = array( 'hits' => 0, 'misses' => 0, 'stores' => 0, 'reasons' => array(), 'servers' => 0,
            'since' => (int)@file_get_contents( $this->config['dir'] . '/stats/since' ) );
        $ownID = $this->apcuUsable() ? $this->statisticsID() : '';
        $sets = array();
        foreach ( glob( $this->config['dir'] . '/stats/*.json' ) ?: array() as $file )
        {
            if ( basename( $file, '.json' ) === $ownID )
                continue;
            $s = json_decode( (string)@file_get_contents( $file ), true );
            if ( is_array( $s ) && time() - (int)( $s['time'] ?? 0 ) < 86400 )
                $sets[] = $s;
        }
        if ( $ownID !== '' )
            $sets[] = $this->localStatistics();     // live, not the last write
        if ( !$sets )
            return null;
        foreach ( $sets as $s )
        {
            $total['servers']++;
            foreach ( array( 'hits', 'misses', 'stores' ) as $k )
                $total[$k] += (int)( $s[$k] ?? 0 );
            foreach ( (array)( $s['reasons'] ?? array() ) as $r => $n )
                $total['reasons'][$r] = ( $total['reasons'][$r] ?? 0 ) + (int)$n;
        }
        arsort( $total['reasons'] );
        return $total;
    }

    private function clearLocalStatistics()
    {
        $info = @apcu_cache_info( false );
        foreach ( (array)( $info['cache_list'] ?? array() ) as $e )
        {
            $k = (string)( $e['info'] ?? $e['key'] ?? '' );
            if ( strncmp( $k, 'exphttpcache:stat:', 18 ) === 0 )
                @apcu_delete( $k );
        }
    }

    /**
     * Starts the counters again. Other servers' APCu cannot be reached from
     * here: their files are removed, and each zeroes its own counters when it
     * next writes and finds the newer reset time (flushStatistics()).
     */
    public function resetStatistics()
    {
        $dir = $this->config['dir'] . '/stats';
        $this->ensureDir( $dir );
        $now = time();
        $this->atomicWrite( $dir . '/since', (string)$now );
        if ( $this->apcuUsable() )
        {
            $this->clearLocalStatistics();
            @apcu_store( 'exphttpcache:statepoch', $now );
        }
        foreach ( glob( $dir . '/*.json' ) ?: array() as $file )
            @unlink( $file );
    }

    /**
     * What is on disk: entries and their bytes, user records, and the state.
     * Stops counting after $limit entries (counted_all then false).
     */
    public function inventory( $limit = 50000 )
    {
        $inv = array( 'entries' => 0, 'bytes' => 0, 'records' => 0, 'counted_all' => true );
        foreach ( glob( $this->config['dir'] . '/e/*', GLOB_ONLYDIR ) ?: array() as $shard )
        {
            foreach ( glob( $shard . '/*' ) ?: array() as $file )
            {
                if ( substr( $file, -5 ) === '.meta' )
                    $inv['entries']++;
                $inv['bytes'] += (int)@filesize( $file );
            }
            if ( $inv['entries'] >= $limit )
            {
                $inv['counted_all'] = false;
                break;
            }
        }
        $inv['records'] = count( glob( $this->config['dir'] . '/u/*/*.rec' ) ?: array() );
        $this->state = null;
        $s = $this->state();
        $inv['generation'] = (int)$s['generation'];
        $inv['generation_time'] = (float)$s['genTime'];
        $inv['purged_tags'] = count( $s['tags'] );
        $inv['last_purge'] = max( array_merge( array( (float)$s['genTime'] ), array_values( $s['tags'] ) ) );
        return $inv;
    }

    private function lookup( array $request )
    {
        $this->lastReason = '';
        if ( !in_array( $request['method'] ?? 'GET', array( 'GET', 'HEAD' ), true ) )
            return $this->miss( 'method' );
        $host = strtolower( preg_replace( '/:\d+$/', '', (string)( $request['host'] ?? '' ) ) );
        if ( !isset( $this->config['hosts'][$host] ) )
            return $this->miss( 'host' );
        if ( !$this->queryAllowed( $request['uri'] ?? '/' ) )
            return $this->miss( 'query string' );
        $siteaccess = $this->config['hosts'][$host];
        // Without the right cookie name a signed-in visitor looks anonymous,
        // so an unknown name is a miss, never a guess.
        $cookieName = $this->config['sessionCookie'][$siteaccess] ?? null;
        if ( !is_string( $cookieName ) || $cookieName === '' )
            return $this->miss( 'session cookie name unknown' );
        $sessionID = $request['cookies'][$cookieName] ?? null;

        if ( $sessionID === null || $sessionID === '' )
        {
            $userID = 0;
            $sessionID = null;
        }
        else
        {
            $userID = $this->sessionUserID( $sessionID );
            if ( $userID === null )
                return $this->miss( 'session unreadable' );
        }

        if ( $userID === 0 )
        {
            $context = $this->anonymousContext( $siteaccess );
            $record = null;
        }
        else
        {
            $record = $this->loadRecord( $userID );
            if ( !$record )
                return $this->miss( 'no user record' );
            $context = $record['ctx'];
        }

        $key = $this->entryKey( $request['scheme'] ?? 'https', $host, $siteaccess, $request['uri'] ?? '/', $context );
        $entry = $this->loadEntry( $key );
        if ( !$entry )
            return null;
        list( $meta, $body ) = $entry;

        $values = $this->placeholderValues( $meta['placeholders'], $sessionID );
        if ( $values === null )
            return $this->miss( 'placeholder' );

        $etag = '"' . sha1( $meta['etag'] . ( $record ? $record['version'] : '' ) . ( $values ? sha1( json_encode( $values ) ) : '' ) ) . '"';
        $headers = $meta['headers'];
        $headers['ETag'] = $etag;
        $headers['Age'] = (string)max( 0, (int)( microtime( true ) - $meta['created'] ) );
        $headers['X-Exp-Cache'] = empty( $meta['stale'] ) ? 'HIT' : 'STALE';
        $headers['Cache-Control'] = $userID === 0 ? 'public, max-age=300' : 'private, no-cache, must-revalidate';
        if ( !empty( $this->config['proxyHeaders'] ) )
        {
            $headers['xkey'] = implode( ' ', $meta['tags'] );
            $headers['Surrogate-Key'] = implode( ' ', $meta['tags'] );
        }
        if ( isset( $request['ifNoneMatch'] ) && trim( $request['ifNoneMatch'] ) === $etag )
            return array( 304, $headers, '' );

        $out = self::substitute( $body, $meta['placeholders'], $values );
        $headers['Vary'] = 'Accept-Encoding, Cookie';
        if ( strpos( (string)( $request['acceptEncoding'] ?? '' ), 'gzip' ) !== false
            && strlen( $out ) > 1024 && function_exists( 'gzencode' ) )
        {
            $out = $this->gzipEntry( $key, $meta, $body, $values, $out );
            $headers['Content-Encoding'] = 'gzip';
        }
        $headers['Content-Length'] = (string)strlen( $out );
        $this->lastReason = 'hit';
        return array( (int)$meta['status'], $headers, ( $request['method'] ?? 'GET' ) === 'HEAD' ? '' : $out );
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    private function miss( $reason )
    {
        $this->lastReason = $reason;
        return null;
    }

    private function apcuUsable()
    {
        return !empty( $this->config['apcu'] ) && function_exists( 'apcu_enabled' ) && apcu_enabled();
    }

    public function ensureDir( $dir )
    {
        if ( !is_dir( $dir ) && @mkdir( $dir, 0770, true ) )
            $this->adopt( $dir );
    }

    public function atomicWrite( $path, $data )
    {
        $tmp = $path . '.' . getmypid() . '.' . mt_rand() . '.tmp';
        if ( @file_put_contents( $tmp, $data ) === false )
            return false;
        @chmod( $tmp, 0660 );
        $this->adopt( $tmp );
        return @rename( $tmp, $path );
    }

    /**
     * Removes what can no longer be served: expired, purged and old-format
     * entries, bodies no metadata points at, records of an old generation and
     * temporary files left by an interrupted write. Nothing younger than
     * $grace seconds is touched as an orphan, so a write in progress survives.
     *
     * @return array counts: entries, bodies, records, tmp, kept
     */
    public function gc( $grace = 120 )
    {
        $now = microtime( true );
        $counts = array( 'entries' => 0, 'bodies' => 0, 'records' => 0, 'tmp' => 0, 'kept' => 0 );
        $this->state = null;
        $old = function ( $file ) use ( $now, $grace ) {
            $m = @filemtime( $file );
            return $m !== false && $now - $m > $grace;
        };
        foreach ( glob( $this->config['dir'] . '/e/*', GLOB_ONLYDIR ) ?: array() as $shard )
        {
            $live = array();
            foreach ( glob( $shard . '/*.meta' ) ?: array() as $metaFile )
            {
                $meta = json_decode( (string)@file_get_contents( $metaFile ), true );
                $current = is_array( $meta ) && ( $meta['v'] ?? 0 ) === self::FORMAT
                    && $now - $meta['created'] <= $meta['maxAge']
                    && $this->isCurrent( $meta['created'], (array)$meta['tags'] );
                if ( $current )
                {
                    $live[basename( $metaFile, '.meta' ) . '.' . substr( $meta['etag'], 0, 16 ) . '.body'] = true;
                    $counts['kept']++;
                }
                else if ( @unlink( $metaFile ) )
                {
                    $counts['entries']++;
                }
            }
            foreach ( glob( $shard . '/*.body' ) ?: array() as $bodyFile )
            {
                if ( !isset( $live[basename( $bodyFile )] ) && $old( $bodyFile ) && @unlink( $bodyFile ) )
                    $counts['bodies']++;
            }
            foreach ( glob( $shard . '/*.tmp' ) ?: array() as $tmp )
            {
                if ( $old( $tmp ) && @unlink( $tmp ) )
                    $counts['tmp']++;
            }
        }
        $generation = $this->generation();
        foreach ( glob( $this->config['dir'] . '/u/*/*.rec' ) ?: array() as $recFile )
        {
            $rec = json_decode( (string)@file_get_contents( $recFile ), true );
            if ( ( !is_array( $rec ) || (int)( $rec['gen'] ?? 0 ) !== $generation ) && @unlink( $recFile ) )
                $counts['records']++;
        }
        return $counts;
    }

    /**
     * A root process (a CLI cache clear, a server running as root) hands what it
     * creates to the owner of the cache directory, or the web server could no
     * longer update it and later purges would be lost.
     */
    public function adopt( $path )
    {
        if ( !function_exists( 'posix_geteuid' ) || posix_geteuid() !== 0 )
            return;
        $owner = @stat( $this->config['dir'] );
        if ( $owner && $owner['uid'] !== 0 )
        {
            @chown( $path, $owner['uid'] );
            @chgrp( $path, $owner['gid'] );
        }
    }
}
