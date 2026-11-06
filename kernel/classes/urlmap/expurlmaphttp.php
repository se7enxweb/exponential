<?php
/**
 * File containing the expUrlMapHttp class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The HTTP requests of the URL map helpers (expUrlMapSitemapSource reads an old site's sitemap, expUrlMapTester
 * requests every old address): GET only, http and https only, never following a redirect itself (the caller sees
 * each hop), with a timeout, a user agent, a size limit on the body, the proxy of site.ini [ProxySettings], and
 * several requests at a time through curl_multi.
 *
 * The kernel has no general HTTP client: expLinkCheck::curlFetch() reads only the status of a link and
 * eZStaticCache::fetchPages() only the bodies of 2xx answers, so neither tells where a redirect leads. Tests replace
 * this class by a subclass that answers from a table (get() and getMany() are the only entry points).
 *
 * An answer is an array: url, status (0 without one), location (the Location header, absolute), body, error (null
 * or the reason), truncated (the body was cut at max_bytes), content_type.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUrlMapHttp
{
    /** The user agent of the requests */
    const USER_AGENT = 'Exponential URL map (+https://exponential.earth)';

    /** @var array timeout, connect_timeout, max_bytes, user_agent, insecure, proxy, with_body */
    protected $options;

    /**
     * @param array $options timeout (seconds, default 15), connect_timeout (5), max_bytes (the longest body read,
     *                       default 50 MB), user_agent, insecure (true: no TLS verification, for self-signed test
     *                       hosts), proxy (default site.ini [ProxySettings] ProxyServer), with_body (default true),
     *                       public_only (true: never a private or local address, checked by name and the connection
     *                       pinned to the checked address: for addresses someone typed in the administration),
     *                       resolver (callable( string $host ): string[], default expLinkCheck::dnsResolve)
     */
    public function __construct( array $options = array() )
    {
        $this->options = $options + array( 'timeout' => 15, 'connect_timeout' => 5, 'max_bytes' => 52428800, 'user_agent' => self::USER_AGENT,
                                           'insecure' => false, 'proxy' => null, 'with_body' => true, 'public_only' => false,
                                           'resolver' => null );
    }

    /** @return array the options in effect */
    public function options()
    {
        return $this->options;
    }

    /**
     * One GET request.
     *
     * @param string $url
     * @return array the answer (see the class)
     */
    public function get( $url )
    {
        $answers = $this->getMany( array( $url ), 1 );
        return $answers[$url];
    }

    /**
     * Several GET requests, up to $concurrency at a time.
     *
     * @param string[] $urls
     * @param int $concurrency
     * @return array url => answer, in the order of $urls
     */
    public function getMany( array $urls, $concurrency = 8 )
    {
        $urls = array_values( array_unique( array_map( 'strval', $urls ) ) );
        $result = array();
        $todo = array();
        foreach ( $urls as $url )
        {
            $problem = self::problem( $url );
            if ( $problem !== null )
                $result[$url] = self::answer( $url, 0, null, '', $problem );
            else
                $todo[] = $url;
        }
        if ( $todo && !function_exists( 'curl_multi_init' ) )
        {
            foreach ( $todo as $url )
                $result[$url] = self::answer( $url, 0, null, '', 'the curl extension is missing' );
            $todo = array();
        }
        $concurrency = max( 1, min( 64, (int)$concurrency ) );
        while ( $todo )
        {
            $chunk = array_splice( $todo, 0, $concurrency );
            $result = $this->runChunk( $chunk ) + $result;
        }
        $ordered = array();
        foreach ( $urls as $url )
            $ordered[$url] = $result[$url];
        return $ordered;
    }

    /**
     * Why an address is not requested, or null: only http and https with a host.
     *
     * @param string $url
     * @return string|null
     */
    public static function problem( $url )
    {
        $parts = parse_url( (string)$url );
        $scheme = isset( $parts['scheme'] ) ? strtolower( $parts['scheme'] ) : '';
        if ( $scheme !== 'http' && $scheme !== 'https' )
            return 'not an http or https address: ' . $url;
        if ( !isset( $parts['host'] ) || $parts['host'] === '' )
            return 'no host in ' . $url;
        return null;
    }

    /**
     * The public address a request to an address may connect to, or why it may not: every address of the host must
     * be a public one (not loopback, private, link-local or reserved; expLinkCheck::isPrivateAddress()).
     *
     * @param string $url
     * @param callable|null $resolver callable( string $host ): string[] (default expLinkCheck::dnsResolve)
     * @return array|string host, port, ip; or the reason
     */
    public static function publicAddress( $url, $resolver = null )
    {
        $parts = parse_url( (string)$url );
        $host = isset( $parts['host'] ) ? strtolower( trim( $parts['host'], '[]' ) ) : '';
        if ( $host === '' )
            return 'no host in ' . $url;
        $port = isset( $parts['port'] ) ? (int)$parts['port'] : ( isset( $parts['scheme'] ) && strtolower( $parts['scheme'] ) === 'http' ? 80 : 443 );
        $addresses = filter_var( $host, FILTER_VALIDATE_IP ) ? array( $host )
                   : (array)call_user_func( is_callable( $resolver ) ? $resolver : array( 'expLinkCheck', 'dnsResolve' ), $host );
        if ( !$addresses )
            return 'the host ' . $host . ' is not found';
        foreach ( $addresses as $address )
        {
            if ( expLinkCheck::isPrivateAddress( (string)$address ) )
                return 'the host ' . $host . ' has a private or local address, which is not fetched';
        }
        return array( 'host' => $host, 'port' => $port, 'ip' => (string)$addresses[0] );
    }

    /**
     * An address relative to another, as a browser resolves a Location header. Pure.
     *
     * @param string $base
     * @param string $location
     * @return string
     */
    public static function absolute( $base, $location )
    {
        $location = trim( (string)$location );
        if ( $location === '' )
            return (string)$base;
        if ( preg_match( '#^[a-z][a-z0-9+.\-]*:#i', $location ) )
            return $location;
        $parts = parse_url( (string)$base );
        $origin = ( isset( $parts['scheme'] ) ? $parts['scheme'] : 'http' ) . '://' . ( isset( $parts['host'] ) ? $parts['host'] : '' )
                . ( isset( $parts['port'] ) ? ':' . $parts['port'] : '' );
        if ( substr( $location, 0, 2 ) === '//' )
            return ( isset( $parts['scheme'] ) ? $parts['scheme'] : 'http' ) . ':' . $location;
        if ( $location[0] === '/' )
            return $origin . $location;
        $path = isset( $parts['path'] ) ? $parts['path'] : '/';
        if ( $location[0] === '?' )
            return $origin . $path . $location;
        return $origin . substr( $path, 0, (int)strrpos( $path, '/' ) + 1 ) . $location;
    }

    /**
     * @param string $url
     * @param int $status
     * @param string|null $location
     * @param string $body
     * @param string|null $error
     * @param bool $truncated
     * @param string $contentType
     * @return array an answer
     */
    public static function answer( $url, $status, $location = null, $body = '', $error = null, $truncated = false, $contentType = '' )
    {
        return array( 'url' => (string)$url, 'status' => (int)$status, 'location' => $location !== null && $location !== '' ? self::absolute( $url, $location ) : null,
                      'body' => (string)$body, 'error' => $error !== null && $error !== '' ? (string)$error : null, 'truncated' => (bool)$truncated,
                      'content_type' => (string)$contentType );
    }

    /**
     * The requests of one chunk at the same time.
     *
     * @param string[] $urls
     * @return array url => answer
     */
    protected function runChunk( array $urls )
    {
        $multi = curl_multi_init();
        $handles = array();
        $bodies = array();
        $truncated = array();
        $result = array();
        foreach ( $urls as $i => $url )
        {
            $bodies[$i] = '';
            $truncated[$i] = false;
            $options = $this->curlOptions( $bodies[$i], $truncated[$i] );
            if ( !empty( $this->options['public_only'] ) )
            {
                $pinned = self::publicAddress( $url, $this->options['resolver'] );
                if ( !is_array( $pinned ) )
                {
                    $result[$url] = self::answer( $url, 0, null, '', $pinned );
                    continue;
                }
                // connect to the address that was checked, not to whatever the name resolves to a moment later
                if ( !isset( $options[CURLOPT_PROXY] ) )
                    $options[CURLOPT_RESOLVE] = array( $pinned['host'] . ':' . $pinned['port'] . ':' . ( strpos( $pinned['ip'], ':' ) !== false ? '[' . $pinned['ip'] . ']' : $pinned['ip'] ) );
            }
            $ch = curl_init( $url );
            curl_setopt_array( $ch, $options );
            curl_multi_add_handle( $multi, $ch );
            $handles[$i] = $ch;
        }
        if ( !$handles )
        {
            curl_multi_close( $multi );
            return $result;
        }
        do
        {
            $status = curl_multi_exec( $multi, $running );
            if ( $running && $status === CURLM_OK )
                curl_multi_select( $multi, 1.0 );
        }
        while ( $running && $status === CURLM_OK );
        foreach ( $handles as $i => $ch )
        {
            $code = (int)curl_getinfo( $ch, CURLINFO_RESPONSE_CODE );
            $error = curl_error( $ch );
            $location = curl_getinfo( $ch, CURLINFO_REDIRECT_URL );
            $type = curl_getinfo( $ch, CURLINFO_CONTENT_TYPE );
            // a body cut at max_bytes ends the transfer with a write error: the answer is still the answer
            if ( $truncated[$i] && $code > 0 )
                $error = '';
            $result[$urls[$i]] = self::answer( $urls[$i], $code, $location ? (string)$location : null, $bodies[$i],
                                               $error !== '' ? $error : ( $code === 0 ? 'no answer' : null ), $truncated[$i], is_string( $type ) ? $type : '' );
            curl_multi_remove_handle( $multi, $ch );
            if ( PHP_VERSION_ID < 80000 ) curl_close( $ch ); // no effect since PHP 8.0, deprecated in 8.5
        }
        curl_multi_close( $multi );
        return $result;
    }

    /**
     * @param string $body filled with the body
     * @param bool $truncated set when the body was cut
     * @return array
     */
    protected function curlOptions( &$body, &$truncated )
    {
        $max = max( 1, (int)$this->options['max_bytes'] );
        $withBody = !empty( $this->options['with_body'] );
        $options = array( CURLOPT_FOLLOWLOCATION => false,
                          CURLOPT_HTTPGET => true,
                          CURLOPT_CONNECTTIMEOUT => max( 1, (int)$this->options['connect_timeout'] ),
                          CURLOPT_TIMEOUT => max( 1, (int)$this->options['timeout'] ),
                          CURLOPT_USERAGENT => (string)$this->options['user_agent'],
                          CURLOPT_SSL_VERIFYPEER => empty( $this->options['insecure'] ),
                          CURLOPT_SSL_VERIFYHOST => empty( $this->options['insecure'] ) ? 2 : 0,
                          CURLOPT_ENCODING => '',
                          CURLOPT_HTTPHEADER => array( 'Accept: */*' ),
                          CURLOPT_WRITEFUNCTION => function ( $handle, $data ) use ( &$body, &$truncated, $max, $withBody )
                          {
                              if ( !$withBody )
                              {
                                  $truncated = true;
                                  return -1;
                              }
                              $room = $max - strlen( $body );
                              if ( strlen( $data ) > $room )
                              {
                                  $body .= substr( $data, 0, max( 0, $room ) );
                                  $truncated = true;
                                  return -1;
                              }
                              $body .= $data;
                              return strlen( $data );
                          } );
        if ( defined( 'CURLOPT_PROTOCOLS_STR' ) )
            $options[CURLOPT_PROTOCOLS_STR] = 'http,https';
        elseif ( defined( 'CURLOPT_PROTOCOLS' ) )
            $options[CURLOPT_PROTOCOLS] = CURLPROTO_HTTP | CURLPROTO_HTTPS;
        $proxy = $this->options['proxy'];
        if ( $proxy === null && class_exists( 'eZINI', false ) )
        {
            $ini = eZINI::instance();
            $proxy = $ini->hasVariable( 'ProxySettings', 'ProxyServer' ) ? (string)$ini->variable( 'ProxySettings', 'ProxyServer' ) : '';
            if ( $proxy !== '' && $ini->hasVariable( 'ProxySettings', 'User' ) && $ini->variable( 'ProxySettings', 'User' ) )
                $options[CURLOPT_PROXYUSERPWD] = $ini->variable( 'ProxySettings', 'User' ) . ':' . ( $ini->hasVariable( 'ProxySettings', 'Password' ) ? $ini->variable( 'ProxySettings', 'Password' ) : '' );
        }
        if ( $proxy )
            $options[CURLOPT_PROXY] = (string)$proxy;
        return $options;
    }
}
