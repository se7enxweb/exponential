<?php
/**
 * File containing the ezpHttpCacheContract class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
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
 * three produce the same keys and the same bytes by construction. The one
 * exception is eZTrustedProxy, itself free of everything else, which it loads
 * by path so that the kernel and the lookup trust the same forwarded headers.
 *
 * Storage, under $config['dir']:
 *   contract.php          the configuration (written by the kernel)
 *   state.ser             { generation, genTime, tags: { tag => purgeTime } }
 *   e/<kk>/<key>.meta     entry metadata (JSON)
 *   e/<kk>/<key>.<h>.body entry body (h = start of its sha1), placeholders removed
 *   u/<uu>/<userID>.rec   user context record (JSON)
 *
 * See doc/eng-us/bc/6.0/httpcache.md.
 */
if ( !class_exists( 'eZTrustedProxy', false ) )
    require_once dirname( __DIR__, 4 ) . '/lib/ezutils/classes/eztrustedproxy.php';
// eZFile and eZDir are not loaded here, although the modes come from them: Velocity's parent process loads this file
// before its source transform is in place, and every worker inherits the classes it compiled. eZFile compiled there
// kept PHP's own header(), which does nothing under the CLI SAPI, so every content/download on Velocity went out as
// text/html without its Content-Type, Content-Disposition or 206. See fileMode() and dirMode() below.

class ezpHttpCacheContract
{
    const FORMAT = 2;

    /** Per-user limitation identifiers that make a context private. */
    const PER_USER_LIMITATIONS = array( 'Owner', 'ParentOwner', 'Group', 'ParentGroup', 'User_Section', 'User_Subtree' );

    /**
     * Most purged tags the state keeps: it is read by every lookup, so more
     * (a large subtree changed under ContentChangePurges=tags) purge every
     * page instead -- each entry carries ez-all.
     */
    const MAX_PURGED_TAGS = 10000;

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
            'tagHeader' => '', 'apcu' => true, 'maxBodySize' => 2097152,
            'queryParameters' => array(), 'sslPort' => '', 'sslProxyServerName' => '',
            // A contract written before the setting existed trusts what the
            // kernel trusts without site.ini saying otherwise.
            'trustedProxies' => eZTrustedProxy::defaultList(),
            // Who sees the debug bar (site.ini [DebugSettings]): such a visitor is answered from the cache with the
            // bar of the render that stored the page (debugBar()); empty or not enabled: as before
            'debug' => array(),
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

    /**
     * The address a request may be answered from when its own query is not
     * cacheable: what request-shield names in REQUEST_SHIELD_CACHE_LOOKUP
     * (set cache-unknown-query hit-only) -- the same path, only the query
     * parameters a cache key holds. Taken only for the same path and without
     * a fragment, so it can never name another page; null otherwise.
     */
    public static function shieldLookupUri( $uri, $lookup )
    {
        if ( !is_string( $lookup ) || $lookup === '' || $lookup[0] !== '/' || strpos( $lookup, '#' ) !== false )
            return null;
        $path = function ( $u ) {
            $u = (string)$u;
            $cut = strcspn( $u, '?#' );
            return substr( $u, 0, $cut );
        };
        return $path( $uri ) === $path( $lookup ) ? $lookup : null;
    }

    /** $uri with only the query parameters a cache key holds (QueryStringParameters), without a fragment. */
    public function keyedUri( $uri )
    {
        $uri = (string)$uri;
        $hash = strpos( $uri, '#' );
        if ( $hash !== false )
            $uri = substr( $uri, 0, $hash );
        $q = strpos( $uri, '?' );
        if ( $q === false )
            return $uri;
        $kept = array();
        foreach ( explode( '&', substr( $uri, $q + 1 ) ) as $pair )
        {
            if ( $pair !== '' && in_array( urldecode( explode( '=', $pair, 2 )[0] ), (array)$this->config['queryParameters'], true ) )
                $kept[] = $pair;
        }
        return substr( $uri, 0, $q ) . ( $kept ? '?' . implode( '&', $kept ) : '' );
    }

    // ── Origin: the scheme and host the kernel sees ─────────────────────

    /**
     * The scheme and host of a request as the kernel works them out, from
     * $_SERVER-style variables: eZSys::hostname() (X-Forwarded-Host first,
     * then Host) and eZSys::isSSLNow() (HTTPS, the port against SSLPort, then
     * X-Forwarded-Proto, X-Forwarded-Port, X-Forwarded-Server), with the
     * site.ini values the kernel wrote into the contract.
     *
     * As in the kernel, the forwarded headers count only when REMOTE_ADDR is
     * one of the trusted proxies ([HTTPHeaderSettings] TrustedProxies[]);
     * without REMOTE_ADDR they never do. See doc/eng-us/bc/6.0/trusted-proxies.md.
     *
     * The key of a stored page is made of these, so the lookup has to arrive
     * at the same ones. Behind a load balancer that ends TLS and forwards to
     * host:port, only the forwarded headers say that the visitor asked for
     * https://www.example.com and not http://exp:8080 -- the kernel renders
     * and stores the page for the former, and a lookup taking the latter
     * never found it.
     *
     * @param array $server $_SERVER, or HTTP_* variables built from headers
     * @return array ( 'https'|'http', host as the kernel has it, port included if sent )
     */
    public function requestOrigin( array $server )
    {
        $host = '';
        $hops = eZTrustedProxy::trustedHops(
            isset( $server['REMOTE_ADDR'] ) ? (string)$server['REMOTE_ADDR'] : null,
            isset( $server['HTTP_X_FORWARDED_FOR'] ) ? (string)$server['HTTP_X_FORWARDED_FOR'] : null,
            eZTrustedProxy::normaliseList( $this->config['trustedProxies'] ?? array() ) );
        $forwarded = (string)( $server['HTTP_X_FORWARDED_HOST'] ?? '' );
        if ( $forwarded !== '' )
            $host = (string)eZTrustedProxy::forwardedValue( $forwarded, $hops );
        if ( $host === '' )
            $host = (string)( $server['HTTP_HOST'] ?? '' );

        $https = (string)( $server['HTTPS'] ?? '' );
        if ( $https !== '' && strtolower( $https ) !== 'off' )
            return array( 'https', $host );

        $sslPort = (string)( $this->config['sslPort'] ?? '' );
        $port = preg_match( '/.*:([0-9]+)/', $host, $regs ) ? (int)$regs[1] : (int)( $server['SERVER_PORT'] ?? 0 );
        if ( !$port )
            $port = 80;
        $ssl = ( $port == ( $sslPort !== '' && $sslPort !== '0' ? $sslPort : 443 ) );
        if ( !$ssl && $hops > 0 )
        {
            if ( isset( $server['HTTP_X_FORWARDED_PROTO'] ) )
            {
                $proto = eZTrustedProxy::forwardedValue( (string)$server['HTTP_X_FORWARDED_PROTO'], $hops );
                $ssl = ( $proto !== null && strtolower( $proto ) === 'https' );
            }
            else if ( isset( $server['HTTP_X_FORWARDED_PORT'] ) )
            {
                $forwardedPort = eZTrustedProxy::forwardedValue( (string)$server['HTTP_X_FORWARDED_PORT'], $hops );
                $ssl = ( $forwardedPort !== null && $forwardedPort == $sslPort );
            }
            else if ( isset( $server['HTTP_X_FORWARDED_SERVER'] ) )
            {
                $forwardedServer = eZTrustedProxy::forwardedValue( (string)$server['HTTP_X_FORWARDED_SERVER'], $hops );
                $ssl = ( $forwardedServer !== null && (string)( $this->config['sslProxyServerName'] ?? '' ) == $forwardedServer );
            }
        }
        return array( $ssl ? 'https' : 'http', $host );
    }

    /**
     * $_SERVER-style variables for a request handed to serve(): its own
     * 'server' when the caller has one (the early exit), else built from its
     * 'headers' (the web server's process), else just its host and scheme
     * (a web server that passes nothing else).
     */
    private static function serverVariables( array $request )
    {
        if ( isset( $request['server'] ) && is_array( $request['server'] ) )
            return $request['server'];
        $server = array();
        foreach ( (array)( $request['headers'] ?? array() ) as $name => $value )
        {
            if ( is_string( $value ) )
                $server['HTTP_' . strtoupper( str_replace( '-', '_', (string)$name ) )] = $value;
        }
        if ( !isset( $server['HTTP_HOST'] ) )
            $server['HTTP_HOST'] = (string)( $request['host'] ?? '' );
        if ( ( $request['scheme'] ?? '' ) === 'https' )
            $server['HTTPS'] = 'on';
        if ( isset( $request['port'] ) )
            $server['SERVER_PORT'] = (string)$request['port'];
        // The peer of the connection, when the web server says: without it no
        // forwarded header is believed (see requestOrigin()).
        if ( isset( $request['remoteAddr'] ) && is_string( $request['remoteAddr'] ) )
            $server['REMOTE_ADDR'] = $request['remoteAddr'];
        return $server;
    }

    // ── Siteaccess ───────────────────────────────────────────────────────

    /**
     * The siteaccess the kernel will match for $host and $uri, or null when
     * that cannot be known before the kernel starts.
     *
     * The same rules as eZSiteAccess::match(), on the site.ini values the
     * kernel wrote into the contract ('match'): StaticMatch, MatchOrder with
     * uri (URIMatchType element or map), host (HostMatchType map, with
     * HostMatchMethod and the method of an item) and host_uri
     * (HostUriMatchMapItems, every host match method), then DefaultAccess --
     * unless DefaultHostUriMatchMapItems has an entry for the host, where the
     * browser's language decides (null).
     * Anything else -- port, servervar, index, the text and regexp types, a
     * name the kernel would normalise -- is null, and the page is not served
     * early. The kernel stores a page only when this gives the siteaccess it
     * matched itself, so a rule answered wrongly here costs a render, never a
     * page of another siteaccess.
     *
     * A contract written before 'match' existed answers from its host map.
     *
     * @param string $host the Host header; a port is ignored, as the kernel ignores it
     * @param string $uri the request URI, path and query
     * @return string|null
     */
    public function resolveSiteAccess( $host, $uri )
    {
        $host = preg_replace( '/:\d+$/', '', (string)$host );
        $m = $this->config['match'] ?? null;
        if ( !is_array( $m ) )
            return $this->config['hosts'][strtolower( $host )] ?? null;

        if ( (string)( $m['static'] ?? '' ) !== '' )
            return (string)$m['static'];
        $default = (string)( $m['default'] ?? '' );
        $order = (array)( $m['order'] ?? array() );
        if ( $order === array( 'none' ) )
            return $default;
        $list = (array)( $m['list'] ?? array() );

        // eZURI: the path without its leading slash, decoded, split at "/".
        $path = (string)$uri;
        foreach ( array( '#', '?' ) as $cut )
        {
            $at = strpos( $path, $cut );
            if ( $at !== false )
                $path = substr( $path, 0, $at );
        }
        if ( $path === '' || $path[0] !== '/' )
            $path = '/' . $path;
        // eZSys strips the index file from the request URI; not worth
        // following here, the kernel has not seen such a URL cached anyway.
        if ( preg_match( '#^/index[^/]*\.php(/|$)#', $path ) )
            return null;
        $path = urldecode( substr( $path, 1 ) );
        $elements = explode( '/', $path );

        foreach ( $order as $probe )
        {
            $name = '';
            switch ( $probe )
            {
                case 'uri':
                    $type = $m['uriType'] ?? '';
                    if ( $type === 'map' )
                    {
                        // Loose == and in_array() on purpose: eZSiteAccess::match()
                        // compares these the same way, and this has to arrive at
                        // the siteaccess the kernel does, not a stricter one.
                        foreach ( (array)( $m['uriMap'] ?? array() ) as $item )
                        {
                            if ( isset( $item[0], $item[1] ) && $item[0] == $elements[0] && in_array( $item[1], $list ) )
                                return (string)$item[1];
                        }
                    }
                    else if ( $type === 'element' )
                    {
                        $name = implode( '_', array_slice( $elements, 0, (int)( $m['uriElement'] ?? 1 ) ) );
                    }
                    else if ( $type === 'text' || $type === 'regexp' )
                    {
                        return null;
                    }
                    break;

                case 'host':
                    $type = $m['hostType'] ?? '';
                    if ( $type === 'map' )
                    {
                        $hostMethod = (string)( $m['hostMethod'] ?? 'strict' );
                        foreach ( (array)( $m['hostMap'] ?? array() ) as $item )
                        {
                            if ( !isset( $item[0], $item[1] ) || $item[0] === '' )
                                continue;
                            $method = isset( $item[2] ) && trim( $item[2] ) !== '' ? trim( $item[2] ) : $hostMethod;
                            if ( self::hostMatches( $host, $item[0], $method ) )
                                return (string)$item[1];
                        }
                    }
                    else if ( $type === 'element' || $type === 'text' || $type === 'regexp' )
                    {
                        return null;
                    }
                    break;

                case 'host_uri':
                    $uriString = implode( '/', $elements );
                    foreach ( (array)( $m['hostUri'] ?? array() ) as $item )
                    {
                        if ( !isset( $item[0], $item[1], $item[2] ) )
                            continue;
                        list( $matchHost, $matchURI, $matchAccess ) = $item;
                        $method = $item[3] ?? ( $m['hostUriMethod'] ?? 'strict' );
                        if ( $matchURI !== '' && !@preg_match( "@^$matchURI\b@u", $uriString ) )
                            continue;
                        if ( self::hostMatches( $host, $matchHost, $method ) )
                            return (string)$matchAccess;
                    }
                    break;

                case 'port':
                case 'servervar':
                case 'index':
                    // Depend on what the early exit does not have (the port
                    // settings, the server variable, the index file).
                    return null;
            }

            if ( $name != '' )
            {
                $clean = preg_replace( array( '/[^a-zA-Z0-9]+/', '/_+/', '/^_/', '/_$/' ), array( '_', '_', '', '' ), $name );
                if ( in_array( $clean, $list ) )
                {
                    // The kernel would normalise the name (and may redirect).
                    return $clean === $name ? $name : null;
                }
            }
        }
        // No probe matched: where DefaultHostUriMatchMapItems has an entry for the host, the kernel chooses by the
        // browser's language or sends it on, which is not known here
        foreach ( (array)( $m['defaultHostUri'] ?? array() ) as $item )
        {
            if ( !isset( $item[2] ) )
                continue;
            $method = isset( $item[3] ) && trim( $item[3] ) !== '' && trim( $item[3] ) !== 'default'
                ? trim( $item[3] ) : (string)( $m['hostUriMethod'] ?? 'strict' );
            if ( self::hostMatches( $host, (string)$item[0], $method ) )
                return null;
        }
        return $default;
    }

    /**
     * The host comparison of eZSiteAccess::hostMatches(), which the early exit cannot load: lower case, without a
     * trailing dot; strict, start, end or part. A Unicode host name is left to the kernel (no match here, so the
     * page is not served early).
     *
     * @param string $host
     * @param string $matchHost
     * @param string $method
     * @return bool
     */
    private static function hostMatches( $host, $matchHost, $method )
    {
        $host = strtolower( rtrim( trim( (string)$host ), '.' ) );
        $matchHost = strtolower( rtrim( trim( (string)$matchHost ), '.' ) );
        if ( preg_match( '/[\x80-\xff]/', $host . $matchHost ) )
            return false;
        switch ( $method )
        {
            case 'strict': return $matchHost === $host;
            case 'start':  return strpos( $host, $matchHost ) === 0;
            case 'end':    return $matchHost !== '' && substr( $host, -strlen( $matchHost ) ) === $matchHost;
            case 'part':   return strpos( $host, $matchHost ) !== false;
        }
        return false;
    }

    /**
     * Whether pages of $siteaccess are cached (httpcache.ini CachedSiteAccesses).
     */
    public function cachesSiteAccess( $siteaccess )
    {
        $cached = $this->config['siteaccesses'] ?? array_values( (array)$this->config['hosts'] );
        return is_string( $siteaccess ) && $siteaccess !== '' && in_array( $siteaccess, (array)$cached, true );
    }

    /**
     * The purge-tag header of a page, as name => value: one header, named by
     * httpcache.ini [HttpCacheSettings] TagHeader (xkey for Varnish's xkey
     * vmod, Surrogate-Key for Fastly, Cache-Tag for Cloudflare), or none when
     * TagHeader is disabled, the default. The tags name the page's nodes,
     * objects and layouts, so they are sent only where a purging proxy in front
     * reads them or while debugging. The same for a page just rendered (MISS)
     * and one served from here (HIT), in the early exit and in Velocity.
     *
     * A contract written before TagHeader existed carries no tagHeader and
     * gets none, whatever its old proxyHeaders said.
     *
     * @param array $tags
     * @return array
     */
    public function tagHeaders( array $tags )
    {
        $name = (string)( $this->config['tagHeader'] ?? '' );
        if ( $name === '' || !$tags )
            return array();
        return array( $name => implode( ' ', $tags ) );
    }

    /**
     * The header name TagHeader asks for, or '' for none: disabled, empty or
     * not a valid header name is none. ProxyHeaders=enabled, the setting before
     * TagHeader, still means xkey when TagHeader says nothing else.
     *
     * @param string $tagHeader [HttpCacheSettings] TagHeader
     * @param string $proxyHeaders [HttpCacheSettings] ProxyHeaders, when set
     * @return string
     */
    public static function tagHeaderName( $tagHeader, $proxyHeaders = '' )
    {
        $tagHeader = trim( (string)$tagHeader );
        if ( $tagHeader === '' || in_array( strtolower( $tagHeader ), array( 'disabled', 'false', 'off', 'none' ), true ) )
            return strtolower( trim( (string)$proxyHeaders ) ) === 'enabled' ? 'xkey' : '';
        // An HTTP token, and none of the framing headers.
        if ( !preg_match( '/^[A-Za-z0-9!#$%&\'*+.^_`|~-]+$/', $tagHeader )
             || in_array( strtolower( $tagHeader ), array( 'content-length', 'transfer-encoding', 'connection', 'content-type', 'set-cookie' ), true ) )
            return '';
        return $tagHeader;
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
            if ( count( $s['tags'] ) > self::MAX_PURGED_TAGS )
                $s['tags'] = array( 'ez-all' => $now );
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

    /**
     * [meta, body] for a current entry, or null. $mayRefresh false: a request
     * that never stores the page (request-shield's lookup) is served an
     * expired page as stale and never becomes the one to render it again.
     */
    public function loadEntry( $key, $mayRefresh = true )
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
            if ( $mayRefresh && $this->claimRefresh( $key ) )
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
        else if ( $this->lastReason !== 'siteaccess' && $this->lastReason !== 'method' )
        {
            // Siteaccesses that are not cached (the admin shares this code) and writes are
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
        list( $scheme, $originHost ) = $this->requestOrigin( self::serverVariables( $request ) );
        $host = strtolower( preg_replace( '/:\d+$/', '', $originHost ) );
        $siteaccess = $this->resolveSiteAccess( $originHost, $request['uri'] ?? '/' );
        if ( !$this->cachesSiteAccess( $siteaccess ) )
            return $this->miss( 'siteaccess' );
        $uri = $request['uri'] ?? '/';
        // The debug bar's full-render switch, the view parameter (exp_render)/full, is never part of a key: the
        // kernel takes it out of the address too (expRenderSwitch). It only means something for a debug viewer.
        if ( !class_exists( 'expRenderSwitch', false ) )
            require_once __DIR__ . '/exprenderswitch.php';
        list( $uri, $fullAsked ) = expRenderSwitch::strip( $uri );
        $viaShield = false;
        if ( !$this->queryAllowed( $uri ) )
        {
            // request-shield in front (set cache-unknown-query hit-only) names the page without the
            // parameters no key holds: answered from it, if kept; never stored for this address (the
            // listener sees the query is not allowed). Taken only when it names exactly this request's
            // own parameters the key holds: a shield whose cache-query leaves out one of
            // QueryStringParameters (page) would otherwise answer /news?page=2&x=7 with /news, and a
            // value from anywhere else can name no other variant.
            $lookup = self::shieldLookupUri( $uri, $request['lookupUri'] ?? null );
            if ( $lookup === null || self::normalizeURI( $lookup ) !== self::normalizeURI( $this->keyedUri( $uri ) ) )
                return $this->miss( 'query string' );
            $uri = $lookup;
            $viaShield = true;
        }
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

        // A visitor the debug bar is shown to has entries of their own, stored with the bar of their render
        // (lookupDebug()). Asked without the visitor's address (the server process in front of the workers), a
        // user who has such entries is never answered with a page without the bar: the worker asks again.
        $debug = $this->debugViewer( $request, $userID );
        if ( $debug === null && $userID !== 0 && is_file( $this->debugMarkerPath( $userID ) ) )
            return $this->miss( 'debug viewer, address not known here' );
        if ( $debug === true )
        {
            if ( $fullAsked )
                return $this->miss( 'full render asked' );
            if ( $viaShield )
                return $this->miss( 'debug viewer, request-shield lookup' );
            return $this->lookupDebug( $request, $scheme, $host, $siteaccess, $uri, $context, $userID, $sessionID );
        }

        $key = $this->entryKey( $scheme, $host, $siteaccess, $uri, $context );
        $entry = $this->loadEntry( $key, !$viaShield );
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
        $headers['X-Exp-Cache'] = ( empty( $meta['stale'] ) ? 'HIT' : 'STALE' ) . ( $viaShield ? ' (request-shield lookup)' : '' );
        // A lookup answers an address no cache may keep: a proxy in front must not keep it either.
        $headers['Cache-Control'] = $userID !== 0 ? 'private, no-cache, must-revalidate' : ( $viaShield ? 'private, max-age=300' : 'public, max-age=300' );
        // Only the one TagHeader asks for, never one an older entry kept.
        unset( $headers['xkey'], $headers['Surrogate-Key'], $headers['Cache-Tag'] );
        $headers = $this->tagHeaders( $meta['tags'] ) + $headers;
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

    // ── The debug bar's visitors ─────────────────────────────────────────

    /**
     * The address of the client, as the kernel takes it (eZSys::clientIP(): REMOTE_ADDR, or the header of
     * [HTTPHeaderSettings] ClientIpByCustomHTTPHeader through a trusted proxy), or null when the request does not
     * carry it (the server process in front of the workers asks without it).
     *
     * @param array $request as serve() takes it, with 'server' ($_SERVER) or 'remoteAddr' and 'headers'
     * @return string|null
     */
    public function clientAddress( array $request )
    {
        $server = isset( $request['server'] ) && is_array( $request['server'] ) ? $request['server'] : array();
        $remote = isset( $request['remoteAddr'] ) ? (string)$request['remoteAddr'] : (string)( $server['REMOTE_ADDR'] ?? '' );
        if ( $remote === '' )
            return null;
        $header = (string)( $this->config['debug']['clientIpHeader'] ?? '' );
        if ( $header !== '' && $header !== 'false' )
        {
            $value = $server['HTTP_' . str_replace( '-', '_', strtoupper( $header ) )] ?? null;
            if ( $value === null && isset( $request['headers'] ) && is_array( $request['headers'] ) )
                $value = $request['headers'][strtolower( $header )] ?? null;
            if ( is_string( $value ) && $value !== '' )
                return (string)eZTrustedProxy::clientAddress( $remote, $value, (array)$this->config['trustedProxies'] );
        }
        return $remote;
    }

    /**
     * Whether the debug bar is shown to this request, as the kernel decides it (eZDebug: DebugOutput, then
     * DebugByIP with DebugIPList[] and DebugByUser with DebugUserIDList[]): true, false, or null when that depends
     * on an address the request does not carry. Only with DebugByIP or DebugByUser on; with both off the bar is
     * shown to everybody and nothing changes here.
     *
     * @param array $request
     * @param int $userID
     * @return bool|null
     */
    public function debugViewer( array $request, $userID )
    {
        $d = (array)( $this->config['debug'] ?? array() );
        if ( empty( $d['output'] ) || ( empty( $d['byIP'] ) && empty( $d['byUser'] ) ) )
            return false;
        if ( !empty( $d['byUser'] ) && !in_array( (int)$userID, array_map( 'intval', (array)( $d['userIDs'] ?? array() ) ), true ) )
            return false;
        if ( empty( $d['byIP'] ) )
            return true;
        $ip = $this->clientAddress( $request );
        if ( $ip === null )
            return null;
        if ( !class_exists( 'expDebugBarIPList', false ) )
            require_once dirname( __DIR__, 4 ) . '/kernel/classes/debugbar/expdebugbariplist.php';
        return expDebugBarIPList::match( $ip, (array)( $d['ipList'] ?? array() ) ) !== null;
    }

    /** The context of a debug viewer's entries: their own, beside the role context of their pages */
    public function debugContext( $context, $userID )
    {
        return $context . '|debug|u' . (int)$userID;
    }

    /** The file that says a user has entries with the debug bar (debugViewer(), lookup()) */
    public function debugMarkerPath( $userID )
    {
        $userID = (int)$userID;
        return $this->config['dir'] . '/u/' . ( $userID % 100 ) . '/' . $userID . '.debug';
    }

    private function debugReportPath( $key, $etag )
    {
        return $this->entryPath( $key, substr( (string)$etag, 0, 16 ) . '.dbg' );
    }

    /**
     * Keeps the debug bar of the render that stored the entry $key ($meta: what storeEntry() gave back), to be put
     * back at $offset of the body when the entry is served to a debug viewer. Readable by the owner only (0600):
     * the bar holds SQL, settings and messages of that render. A user's marker file says that they have such entries.
     *
     * @param string $key
     * @param array $meta
     * @param int $offset where the bar stood in the stored body
     * @param string $html the bar
     * @param array $served the bar's "served" block (expDebugBarReport::servedInfo()) with render_ms
     * @param int $userID
     * @param int|null $before how many of the entry's placeholders stand before the bar (a placeholder right before
     *                         it and one right after it have the same offset); null: those before $offset
     * @return bool
     */
    public function storeDebugReport( $key, array $meta, $offset, $html, array $served, $userID, $before = null )
    {
        $path = $this->debugReportPath( $key, $meta['etag'] );
        if ( $before === null )
        {
            $before = 0;
            foreach ( (array)$meta['placeholders'] as $p )
                $before += $p[0] < $offset ? 1 : 0;
        }
        $data = json_encode( array( 'offset' => (int)$offset, 'before' => (int)$before, 'html' => (string)$html,
                                    'served' => $served, 'stored' => (float)$meta['created'] ) );
        if ( $data === false || !$this->atomicWrite( $path, $data ) )
            return false;
        @chmod( $path, self::fileMode( 0600 ) );
        $marker = $this->debugMarkerPath( $userID );
        if ( (int)$userID !== 0 && !is_file( $marker ) )
        {
            $this->ensureDir( dirname( $marker ) );
            $this->atomicWrite( $marker, (string)time() );
        }
        return true;
    }

    /**
     * The answer for a debug viewer: their own entry for the page with the bar of the render that stored it, put back
     * where it stood, its first chip saying "Cached" with a link to a full render, the page time chip and the data
     * block giving this answer's time, and each tab saying which render its figures are from (debugBar()). A stale
     * entry is not served: the viewer gets a fresh render.
     */
    private function lookupDebug( array $request, $scheme, $host, $siteaccess, $uri, $context, $userID, $sessionID )
    {
        $start = isset( $request['start'] ) ? (float)$request['start'] : microtime( true );
        $key = $this->entryKey( $scheme, $host, $siteaccess, $uri, $this->debugContext( $context, $userID ) );
        $entry = $this->loadEntry( $key, false );
        if ( !$entry )
            return null;
        list( $meta, $body ) = $entry;
        if ( !empty( $meta['stale'] ) )
            return $this->miss( 'expired' );
        $raw = @file_get_contents( $this->debugReportPath( $key, $meta['etag'] ) );
        $report = $raw === false ? null : json_decode( $raw, true );
        if ( !is_array( $report ) || !isset( $report['html'], $report['offset'], $report['served'] ) || $report['offset'] > strlen( $body ) )
            return $this->miss( 'no debug report' );
        $values = $this->placeholderValues( $meta['placeholders'], $sessionID );
        if ( $values === null )
            return $this->miss( 'placeholder' );

        $created = (float)$meta['created'];
        $html = (string)$report['html'];
        // the form token of the session that stored the bar, in its forms: this visitor's instead
        $stored = (string)( $report['served']['form_token'] ?? '' );
        if ( strlen( $stored ) >= 16 )
            $html = str_replace( $stored, $sessionID !== null ? $this->formToken( $sessionID ) : '', $html );
        unset( $report['served']['form_token'] );
        $bar = $this->debugBar( $html, (array)$report['served'], $created, $start, $uri );
        $offset = (int)$report['offset'];
        // the placeholders after the bar move with it
        $before = isset( $report['before'] ) ? (int)$report['before'] : PHP_INT_MAX;
        $placeholders = array();
        foreach ( array_values( $meta['placeholders'] ) as $i => $p )
        {
            if ( $i >= $before )
                $p[0] += strlen( $bar );
            $placeholders[] = $p;
        }
        $out = self::substitute( substr_replace( $body, $bar, $offset, 0 ), $placeholders, $values );

        $headers = $meta['headers'];
        unset( $headers['xkey'], $headers['Surrogate-Key'], $headers['Cache-Tag'], $headers['ETag'] );
        $headers['Age'] = (string)max( 0, (int)( microtime( true ) - $created ) );
        $headers['X-Exp-Cache'] = 'HIT (debug viewer, bar of the render at ' . date( 'H:i:s', (int)$created ) . ')';
        // the bar belongs to this viewer and this answer
        $headers['Cache-Control'] = 'private, no-store';
        $headers['Vary'] = 'Accept-Encoding, Cookie';
        $headers['Content-Length'] = (string)strlen( $out );
        $this->lastReason = 'hit';
        return array( (int)$meta['status'], $headers, ( $request['method'] ?? 'GET' ) === 'HEAD' ? '' : $out );
    }

    /**
     * The stored bar as the answer from the cache shows it: "Cached" (with the time of this answer, when the page
     * was stored and the time of its render in the title) and "Render now" in place of "Rendered", this answer's time
     * in the page time chip, "From the render at ..." at the head of every tab, a line on this request in the Timing
     * tab, and the data block saying the same (served, summary.time, server_summary.time). Every other part of the bar
     * is the render's, unchanged.
     *
     * @param string $html the stored bar
     * @param array $served its "served" block (expDebugBarReport::servedInfo(), render_ms set when stored)
     * @param float $created when the page was stored
     * @param float $start when this request started
     * @param string $uri the page's address (without the switch)
     * @return string
     */
    public function debugBar( $html, array $served, $created, $start, $uri )
    {
        if ( !class_exists( 'expRenderSwitch', false ) )
            require_once __DIR__ . '/exprenderswitch.php';
        $e = function ( $value ) { return htmlspecialchars( (string)$value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 ); };
        $labels = (array)( $served['labels'] ?? array() );
        $label = function ( $name, $default, array $values = array() ) use ( $labels ) {
            return strtr( isset( $labels[$name] ) ? (string)$labels[$name] : $default, $values );
        };
        $now = microtime( true );
        $ms = max( 0.0, ( $now - $start ) * 1000 );
        $renderMs = isset( $served['render_ms'] ) ? (float)$served['render_ms'] : 0.0;
        $values = array( '%ms' => number_format( $ms, 1, '.', '' ), '%stored' => date( 'H:i:s', (int)$created ),
                         '%render' => number_format( $renderMs, 0, '.', '' ),
                         '%age' => (string)max( 0, (int)( $now - $created ) ) );

        $chip = '<span class="exp-debug-served" data-served="cache" title="'
              . $e( $label( 'cache_title', 'Served from the page cache in %ms ms. The page was stored at %stored by a render of %render ms, %age s ago.', $values ) ) . '">'
              . $e( $label( 'cache', 'Cached' ) ) . '</span>'
              . '<a class="exp-debug-served-action" href="' . $e( expRenderSwitch::add( $uri ) ) . '">' . $e( $label( 'render_now', 'Render now' ) ) . '</a>';
        $html = self::replaceBetween( $html, '<!--exp-served-->', '<!--/exp-served-->', $chip );

        // the page time chip gives this answer's time
        $seconds = number_format( $ms / 1000, 3, '.', '' ) . ' s';
        $html = self::replaceBetween( $html, '<!--exp-served-time-->', '<!--/exp-served-time-->', null,
            function ( $chipHtml ) use ( $seconds ) { return preg_replace( '/\d+\.\d{3} s/', $seconds, $chipHtml ); } );

        // every tab: whose figures these are; Timing: this request
        $from = '<p class="exp-debug-hint exp-debug-from-render">' . $e( $label( 'from_render', 'From the render at %stored (%render ms)', $values ) ) . '</p>';
        $thisRequest = '<p class="exp-debug-hint exp-debug-from-render" data-this-request="1">' . $e( $label( 'this_request', 'This request: served from the page cache in %ms ms.', $values ) ) . '</p>';
        $html = preg_replace_callback( '#(<section class="exp-debug-panel"[^>]*id="exp-debug-panel-([a-z0-9_-]+)"[^>]*>\s*<h3 class="exp-debug-panel-title">.*?</h3>)#s',
            function ( $m ) use ( $from, $thisRequest ) { return $m[1] . ( $m[2] === 'timing' ? $thisRequest : '' ) . $from; }, $html );

        // the data block
        $html = preg_replace_callback( '#(<script type="application/json" id="exp-debug-data">)(.*?)(</script>)#s',
            function ( $m ) use ( $ms, $created, $renderMs, $now ) {
                $data = json_decode( $m[2], true );
                if ( !is_array( $data ) )
                    return $m[0];
                $data['served'] = array( 'by' => 'cache', 'render_time' => round( $renderMs / 1000, 4 ),
                                         'stored_at' => date( 'c', (int)$created ), 'age' => max( 0, (int)( $now - $created ) ),
                                         'time' => round( $ms / 1000, 4 ) )
                                  + (array)( $data['served'] ?? array() );
                if ( isset( $data['summary']['time'] ) )
                    $data['summary']['time'] = round( $ms / 1000, 4 );
                if ( isset( $data['summary']['served'] ) )
                    $data['summary']['served'] = $data['served'];
                if ( isset( $data['server_summary']['time']['total_ms'] ) )
                    $data['server_summary']['time']['total_ms'] = round( $ms, 1 );
                $json = json_encode( $data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR );
                return $json === false ? $m[0] : $m[1] . $json . $m[3];
            }, $html );
        return $html;
    }

    /**
     * $html with what stands between $start and $end (both kept) replaced by $with, or passed through $map.
     */
    private static function replaceBetween( $html, $start, $end, $with, $map = null )
    {
        $a = strpos( $html, $start );
        if ( $a === false )
            return $html;
        $a += strlen( $start );
        $b = strpos( $html, $end, $a );
        if ( $b === false )
            return $html;
        $inner = $map !== null ? $map( substr( $html, $a, $b - $a ) ) : (string)$with;
        return substr( $html, 0, $a ) . $inner . substr( $html, $b );
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
        if ( !is_dir( $dir ) && @mkdir( $dir, self::dirMode( 0770 ), true ) )
            $this->adopt( $dir );
    }

    /**
     * $mode within EZP_FILE_MODE_MAX, as eZFile::fileMode(). eZFile answers when it is loaded already (the kernel's
     * store path); before the autoloader (the early exit) and in Velocity's parent the same rule is applied here,
     * because loading eZFile from this file is what must not happen (see the top of the file).
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
     * The limit the constant $name sets, as eZFile::limitFromConstant() and eZFile::modeFromSetting() read it: an
     * integer mode or a string of octal digits that leaves the owner $ownerBits, else null (no limit). eZFile reports
     * an unusable value to the error log; it is loaded on every request, so that is not repeated here.
     *
     * @param string $name EZP_FILE_MODE_MAX or EZP_DIR_MODE_MAX
     * @param int $ownerBits The rights the owner must keep
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

    public function atomicWrite( $path, $data )
    {
        $tmp = $path . '.' . getmypid() . '.' . mt_rand() . '.tmp';
        if ( @file_put_contents( $tmp, $data ) === false )
            return false;
        @chmod( $tmp, self::fileMode( 0660 ) );
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
            // the debug bars kept beside the bodies (storeDebugReport()) go with them
            foreach ( glob( $shard . '/*.dbg' ) ?: array() as $dbgFile )
            {
                if ( !isset( $live[substr( basename( $dbgFile ), 0, -4 ) . '.body'] ) && $old( $dbgFile ) && @unlink( $dbgFile ) )
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
