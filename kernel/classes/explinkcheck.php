<?php
/**
 * File containing the expLinkCheck class.
 *
 * Decides whether a link of published content works, for the link check cronjob (linkcheck.php) and the link list
 * (url/list). Every outside effect - the HTTP request, the DNS lookup, the content lookup, the alias lookup, the
 * MX lookup, the clock and sleeping - is a callable, so the decisions can be tested without a network or a
 * database. Settings: cronjob.ini [linkCheckSettings]. Guide: doc/guides/urls-and-aliases.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expLinkCheck
{
    /** The link works. */
    const VALID = 'valid';
    /** The link does not work. */
    const INVALID = 'invalid';
    /** Not decided (not tested, or a passing failure such as 429): the stored state is kept. */
    const UNKNOWN = 'unknown';

    /** @var array see defaults() */
    public $settings;

    /** @var int HTTP requests sent so far */
    public $requests = 0;

    /** @var callable( string $method, string $url, string|null $ip, array $settings ): array( 'status' => int, 'location' => string|null, 'error' => string|null ) */
    protected $fetcher;
    /** @var callable( string $host ): string[] the addresses of a host */
    protected $resolver;
    /** @var callable( string $kind, int $id ): string 'visible', 'hidden' or 'missing' */
    protected $contentLookup;
    /** @var callable( string $path ): bool whether a path of this site is a URL alias or module view */
    protected $internalLookup;
    /** @var callable( string $domain ): bool whether a mail domain has a mail server */
    protected $mxLookup;
    /** @var callable(): float the time in seconds */
    protected $clock;
    /** @var callable( int $microseconds ) */
    protected $sleeper;

    /** @var array host => time of its last request */
    protected $lastRequest = array();

    /**
     * @param array $settings see defaults()
     * @param array $callables fetcher, resolver, content, internal, mx, clock, sleep: what is not given is the real one
     */
    public function __construct( array $settings = array(), array $callables = array() )
    {
        $this->settings = array_merge( self::defaults(), $settings );
        $this->fetcher = isset( $callables['fetcher'] ) ? $callables['fetcher'] : array( __CLASS__, 'curlFetch' );
        $this->resolver = isset( $callables['resolver'] ) ? $callables['resolver'] : array( __CLASS__, 'dnsResolve' );
        $this->contentLookup = isset( $callables['content'] ) ? $callables['content'] : array( __CLASS__, 'contentState' );
        $this->internalLookup = isset( $callables['internal'] ) ? $callables['internal'] : array( __CLASS__, 'internalExists' );
        $this->mxLookup = isset( $callables['mx'] ) ? $callables['mx'] : function ( $domain ) { return checkdnsrr( $domain, 'MX' ); };
        $this->clock = isset( $callables['clock'] ) ? $callables['clock'] : function () { return microtime( true ); };
        $this->sleeper = isset( $callables['sleep'] ) ? $callables['sleep'] : 'usleep';
    }

    /**
     * The settings and their defaults, the keys of cronjob.ini [linkCheckSettings].
     *
     * @return array
     */
    public static function defaults()
    {
        return array( 'Timeout' => 15,             // seconds a request may take
                      'ConnectTimeout' => 5,       // seconds to connect
                      'MaxRedirects' => 5,         // redirects followed before the link counts as broken
                      'HostDelay' => 1000,         // milliseconds between two requests to the same host
                      'RecheckInterval' => 72000,  // seconds before a checked link is checked again (0: every run)
                      'MaxURLsPerRun' => 0,        // links checked in one run (0: all)
                      'UserAgent' => 'Exponential Link Validator (+https://exponential.earth)',
                      'AllowedPrivateHosts' => array(), // hosts, addresses or IPv4 ranges (a.b.c.d/n) on a private network that may be tested
                      'SiteURL' => array() );      // the site's own addresses, for paths that are not URL aliases
    }

    /**
     * The settings of cronjob.ini [linkCheckSettings], with defaults for what it does not set.
     *
     * @return array
     */
    public static function settingsFromIni()
    {
        $ini = eZINI::instance( 'cronjob.ini' );
        $settings = self::defaults();
        foreach ( $settings as $key => $default )
        {
            if ( !$ini->hasVariable( 'linkCheckSettings', $key ) )
                continue;
            $value = $ini->variable( 'linkCheckSettings', $key );
            if ( is_array( $default ) )
                $settings[$key] = array_values( array_filter( array_map( 'trim', (array)$value ), 'strlen' ) );
            else if ( is_int( $default ) )
                $settings[$key] = max( 0, (int)$value );
            else if ( trim( (string)$value ) !== '' )
                $settings[$key] = trim( (string)$value );
        }
        return $settings;
    }

    /**
     * The kind of an address: 'web' (http, https, ftp), 'mailto', 'content' (ezlocation://, eznode://,
     * ezobject://, the links of rich text), 'file' (never tested: it names a file on the server) or 'internal'
     * (a path, or any other scheme, which is looked up as a path of this site as it always was).
     *
     * @param string $url
     * @return string
     */
    public static function kind( $url )
    {
        $url = trim( (string)$url );
        if ( preg_match( '#^(https?|ftp)://#i', $url ) )
            return 'web';
        if ( preg_match( '#^mailto:#i', $url ) )
            return 'mailto';
        if ( preg_match( '#^(ezlocation|eznode|ezobject)://[0-9]+#i', $url ) )
            return 'content';
        if ( preg_match( '#^file:#i', $url ) )
            return 'file';
        return 'internal';
    }

    /**
     * Whether a link works.
     *
     * @param string $url
     * @return array( 'result' => VALID|INVALID|UNKNOWN, 'reason' => string )
     */
    public function check( $url )
    {
        $url = trim( (string)$url );
        switch ( self::kind( $url ) )
        {
            case 'web':
                return $this->checkWeb( $url );
            case 'mailto':
                return $this->checkMail( $url );
            case 'content':
                return $this->checkContent( $url );
            case 'file':
                return self::result( self::UNKNOWN, 'file address, not tested' );
        }
        return $this->checkInternal( $url );
    }

    /**
     * A rich text link to a node (ezlocation://, eznode://) or an object (ezobject://): valid while its target
     * exists, is published and is not hidden.
     */
    public function checkContent( $url )
    {
        preg_match( '#^(ezlocation|eznode|ezobject)://([0-9]+)#i', $url, $matches );
        $kind = strtolower( $matches[1] ) === 'ezobject' ? 'object' : 'node';
        $state = call_user_func( $this->contentLookup, $kind, (int)$matches[2] );
        if ( $state === 'visible' )
            return self::result( self::VALID, $kind . ' ' . (int)$matches[2] . ' is published' );
        if ( $state === 'hidden' )
            return self::result( self::INVALID, $kind . ' ' . (int)$matches[2] . ' is hidden or not published' );
        return self::result( self::INVALID, $kind . ' ' . (int)$matches[2] . ' does not exist' );
    }

    /**
     * mailto: as it always was: valid when the domain has a mail server (MX record).
     */
    public function checkMail( $url )
    {
        $address = trim( preg_replace( '#^mailto:#i', '', $url ) );
        $address = preg_replace( '#\?.*$#', '', $address );
        $at = strrpos( $address, '@' );
        if ( $at === false || $at === strlen( $address ) - 1 )
            return self::result( self::INVALID, 'no domain in the e-mail address' );
        $domain = substr( $address, $at + 1 );
        if ( call_user_func( $this->mxLookup, $domain ) )
            return self::result( self::VALID, 'mail server found' );
        return self::result( self::INVALID, 'no mail server for ' . $domain );
    }

    /**
     * A path of this site, or another scheme, as it always was: valid when it is a URL alias or module view,
     * else when one of the site's addresses (SiteURL[]) answers for it.
     */
    public function checkInternal( $url )
    {
        if ( call_user_func( $this->internalLookup, $url ) )
            return self::result( self::VALID, 'URL alias or module view' );
        foreach ( $this->settings['SiteURL'] as $siteURL )
        {
            $siteURL = rtrim( trim( $siteURL ), '/' );
            if ( $siteURL === '' || self::kind( $siteURL ) !== 'web' )
                continue;
            $host = strtolower( (string)parse_url( $siteURL, PHP_URL_HOST ) );
            $answer = $this->checkWeb( $siteURL . '/' . ltrim( $url, '/' ), $host );
            if ( $answer['result'] === self::VALID )
                return self::result( self::VALID, 'answered by ' . $siteURL );
        }
        return self::result( self::INVALID, 'not a URL alias or module view of this site' );
    }

    /**
     * An http, https or ftp address: a HEAD request, a GET when HEAD fails, redirects followed up to
     * MaxRedirects, every hop's host resolved and refused when it is on a private, loopback or link-local
     * network (unless allowed), the connection pinned to the address that was checked, TLS verified.
     *
     * @param string $url
     * @param string|null $allowedHost a host that may be private (the site's own address)
     * @return array see check()
     */
    public function checkWeb( $url, $allowedHost = null )
    {
        $redirects = 0;
        while ( true )
        {
            $parts = parse_url( $url );
            $scheme = isset( $parts['scheme'] ) ? strtolower( $parts['scheme'] ) : '';
            $host = isset( $parts['host'] ) ? strtolower( trim( $parts['host'], '[]' ) ) : '';
            if ( !in_array( $scheme, array( 'http', 'https', 'ftp' ), true ) || $host === '' )
                return self::result( self::INVALID, 'not a web address: ' . $url );

            $ip = null;
            if ( !$this->hostAllowed( $host, $allowedHost ) )
            {
                $addresses = filter_var( $host, FILTER_VALIDATE_IP ) ? array( $host ) : (array)call_user_func( $this->resolver, $host );
                if ( !$addresses )
                    return self::result( self::INVALID, 'host not found: ' . $host );
                foreach ( $addresses as $address )
                {
                    if ( self::isPrivateAddress( $address ) && !$this->addressAllowed( $address ) )
                        return self::result( self::UNKNOWN, 'private or local address, not tested: ' . $host );
                }
                $ip = $addresses[0];
            }

            $answer = $this->request( 'HEAD', $url, $host, $ip );
            if ( $answer['error'] !== null || $answer['status'] >= 400 || ( $answer['status'] < 200 && $scheme !== 'ftp' ) )
            {
                // some servers refuse or mishandle HEAD: ask once more with GET
                $answer = $this->request( 'GET', $url, $host, $ip );
            }

            $status = (int)$answer['status'];
            if ( $status >= 300 && $status < 400 && $answer['location'] )
            {
                if ( ++$redirects > (int)$this->settings['MaxRedirects'] )
                    return self::result( self::INVALID, 'more than ' . (int)$this->settings['MaxRedirects'] . ' redirects' );
                $url = self::absoluteURL( $url, $answer['location'] );
                continue;
            }
            if ( $scheme === 'ftp' && $answer['error'] === null )
                return self::result( self::VALID, 'ftp answered' );
            if ( $status >= 200 && $status < 300 )
                return self::result( self::VALID, 'HTTP ' . $status . ( $redirects ? ' after ' . $redirects . ' redirects' : '' ) );
            if ( $status === 429 || $status === 503 )
                return self::result( self::UNKNOWN, 'HTTP ' . $status . ', try again later' );
            // a site that refuses automated requests (401, 403, or a code of its own such as 999) says nothing
            // about whether people can open the page
            if ( $status === 401 || $status === 403 || $status >= 600 )
                return self::result( self::UNKNOWN, 'HTTP ' . $status . ', refused to the link check' );
            if ( $status >= 300 )
                return self::result( self::INVALID, 'HTTP ' . $status );
            return self::result( self::INVALID, $answer['error'] !== null ? $answer['error'] : 'no answer' );
        }
    }

    /**
     * One request, after waiting HostDelay since the last request to the same host.
     */
    protected function request( $method, $url, $host, $ip )
    {
        $delay = (int)$this->settings['HostDelay'] / 1000;
        if ( $delay > 0 && isset( $this->lastRequest[$host] ) )
        {
            $wait = $this->lastRequest[$host] + $delay - call_user_func( $this->clock );
            if ( $wait > 0 )
                call_user_func( $this->sleeper, (int)round( $wait * 1000000 ) );
        }
        $this->requests++;
        $answer = call_user_func( $this->fetcher, $method, $url, $ip, $this->settings );
        $this->lastRequest[$host] = call_user_func( $this->clock );
        return array( 'status' => isset( $answer['status'] ) ? (int)$answer['status'] : 0,
                      'location' => isset( $answer['location'] ) && $answer['location'] !== '' ? (string)$answer['location'] : null,
                      'error' => isset( $answer['error'] ) && $answer['error'] !== '' ? (string)$answer['error'] : null );
    }

    /**
     * Whether a host may be tested whatever its addresses: the site's own address, or a host of
     * AllowedPrivateHosts[].
     */
    public function hostAllowed( $host, $allowedHost = null )
    {
        $host = strtolower( $host );
        if ( $allowedHost !== null && $host === strtolower( $allowedHost ) )
            return true;
        foreach ( $this->settings['AllowedPrivateHosts'] as $allowed )
        {
            if ( strtolower( trim( $allowed ) ) === $host )
                return true;
        }
        return false;
    }

    /**
     * Whether a private address may be tested: it, or an IPv4 range a.b.c.d/n containing it, is in
     * AllowedPrivateHosts[].
     */
    public function addressAllowed( $ip )
    {
        foreach ( $this->settings['AllowedPrivateHosts'] as $allowed )
        {
            $allowed = trim( $allowed );
            if ( $allowed === $ip )
                return true;
            if ( strpos( $allowed, '/' ) !== false && filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) )
            {
                list( $network, $bits ) = explode( '/', $allowed, 2 );
                $bits = (int)$bits;
                if ( filter_var( $network, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) && $bits >= 0 && $bits <= 32 )
                {
                    $mask = $bits === 0 ? 0 : ( -1 << ( 32 - $bits ) ) & 0xFFFFFFFF;
                    if ( ( ip2long( $ip ) & $mask ) === ( ip2long( $network ) & $mask ) )
                        return true;
                }
            }
        }
        return false;
    }

    /**
     * Whether an address is on a private, loopback, link-local, shared or otherwise reserved network, where a
     * request from the server could reach what the outside cannot (SSRF). Anything that is not an address
     * counts as private.
     *
     * @param string $ip
     * @return bool
     */
    public static function isPrivateAddress( $ip )
    {
        $ip = trim( (string)$ip, '[] ' );
        if ( !filter_var( $ip, FILTER_VALIDATE_IP ) )
            return true;
        // an IPv4 address written as IPv6 (::ffff:127.0.0.1) is judged as the IPv4 address
        if ( preg_match( '#^::ffff:([0-9.]+)$#i', $ip, $matches ) )
            return self::isPrivateAddress( $matches[1] );
        if ( !filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) )
            return true;
        if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) )
        {
            $long = ip2long( $ip );
            foreach ( array( '0.0.0.0/8', '10.0.0.0/8', '100.64.0.0/10', '127.0.0.0/8', '169.254.0.0/16', '172.16.0.0/12',
                             '192.0.0.0/24', '192.168.0.0/16', '198.18.0.0/15', '224.0.0.0/4', '240.0.0.0/4' ) as $range )
            {
                list( $network, $bits ) = explode( '/', $range );
                $mask = ( -1 << ( 32 - (int)$bits ) ) & 0xFFFFFFFF;
                if ( ( $long & $mask ) === ( ip2long( $network ) & $mask ) )
                    return true;
            }
            return false;
        }
        $packed = inet_pton( $ip );
        if ( $packed === false )
            return true;
        $first = ord( $packed[0] );
        $second = ord( $packed[1] );
        // ::, ::1, fc00::/7 (unique local), fe80::/10 (link-local), ff00::/8 (multicast)
        if ( $packed === str_repeat( "\0", 16 ) || $packed === str_repeat( "\0", 15 ) . "\1" )
            return true;
        return ( $first & 0xFE ) === 0xFC || ( $first === 0xFE && ( $second & 0xC0 ) === 0x80 ) || $first === 0xFF;
    }

    /**
     * The absolute address of a redirect's Location, relative to the address that answered with it.
     *
     * @param string $base
     * @param string $location
     * @return string
     */
    public static function absoluteURL( $base, $location )
    {
        $location = trim( $location );
        if ( preg_match( '#^[a-z][a-z0-9+.-]*://#i', $location ) )
            return $location;
        $parts = parse_url( $base );
        $origin = $parts['scheme'] . '://' . $parts['host'] . ( isset( $parts['port'] ) ? ':' . $parts['port'] : '' );
        if ( strpos( $location, '//' ) === 0 )
            return $parts['scheme'] . ':' . $location;
        if ( strpos( $location, '/' ) === 0 )
            return $origin . $location;
        if ( strpos( $location, '?' ) === 0 )
            return $origin . ( isset( $parts['path'] ) ? $parts['path'] : '/' ) . $location;
        $path = isset( $parts['path'] ) ? $parts['path'] : '/';
        $dir = substr( $path, 0, strrpos( $path, '/' ) + 1 );
        return $origin . ( $dir === '' ? '/' : $dir ) . $location;
    }

    /**
     * @return array( 'result' => string, 'reason' => string )
     */
    protected static function result( $result, $reason )
    {
        return array( 'result' => $result, 'reason' => $reason );
    }

    /**
     * The real fetcher: one request with curl, redirects not followed (checkWeb() follows them so each hop is
     * checked), the connection pinned to the checked address, TLS verified, only http, https and ftp, and a GET
     * stopped after the first bytes.
     *
     * @return array( 'status' => int, 'location' => string|null, 'error' => string|null )
     */
    public static function curlFetch( $method, $url, $ip, array $settings )
    {
        if ( !extension_loaded( 'curl' ) )
            return array( 'status' => 0, 'location' => null, 'error' => 'the curl extension is missing' );
        $ch = curl_init( $url );
        $options = array( CURLOPT_RETURNTRANSFER => false,
                          CURLOPT_FOLLOWLOCATION => false,
                          CURLOPT_CONNECTTIMEOUT => max( 1, (int)$settings['ConnectTimeout'] ),
                          CURLOPT_TIMEOUT => max( 1, (int)$settings['Timeout'] ),
                          CURLOPT_SSL_VERIFYPEER => true,
                          CURLOPT_SSL_VERIFYHOST => 2,
                          CURLOPT_USERAGENT => (string)$settings['UserAgent'],
                          CURLOPT_HEADER => false,
                          CURLOPT_NOBODY => $method === 'HEAD',
                          CURLOPT_WRITEFUNCTION => function ( $handle, $data ) { return -1; }, // the status is enough
                          CURLOPT_HTTPHEADER => array( 'Accept: */*' ) );
        if ( defined( 'CURLOPT_PROTOCOLS_STR' ) )
        {
            $options[CURLOPT_PROTOCOLS_STR] = 'http,https,ftp';
        }
        else if ( defined( 'CURLOPT_PROTOCOLS' ) )
        {
            $options[CURLOPT_PROTOCOLS] = CURLPROTO_HTTP | CURLPROTO_HTTPS | CURLPROTO_FTP;
        }
        if ( $method === 'GET' )
            $options[CURLOPT_HTTPGET] = true;

        $ini = eZINI::instance();
        $proxy = $ini->hasVariable( 'ProxySettings', 'ProxyServer' ) ? $ini->variable( 'ProxySettings', 'ProxyServer' ) : false;
        if ( $proxy )
        {
            $options[CURLOPT_PROXY] = $proxy;
            $user = $ini->hasVariable( 'ProxySettings', 'User' ) ? $ini->variable( 'ProxySettings', 'User' ) : false;
            if ( $user )
                $options[CURLOPT_PROXYUSERPWD] = $user . ':' . ( $ini->hasVariable( 'ProxySettings', 'Password' ) ? $ini->variable( 'ProxySettings', 'Password' ) : '' );
        }
        else if ( $ip !== null )
        {
            // connect to the address that was checked, not to whatever the name resolves to a moment later
            $parts = parse_url( $url );
            $port = isset( $parts['port'] ) ? (int)$parts['port'] : ( strtolower( $parts['scheme'] ) === 'https' ? 443 : ( strtolower( $parts['scheme'] ) === 'ftp' ? 21 : 80 ) );
            $options[CURLOPT_RESOLVE] = array( $parts['host'] . ':' . $port . ':' . ( strpos( $ip, ':' ) !== false ? '[' . $ip . ']' : $ip ) );
        }
        curl_setopt_array( $ch, $options );
        $ok = curl_exec( $ch );
        $errno = curl_errno( $ch );
        $status = (int)curl_getinfo( $ch, CURLINFO_RESPONSE_CODE );
        $location = curl_getinfo( $ch, CURLINFO_REDIRECT_URL );
        $error = curl_error( $ch );
        // a GET stopped by the write function after its first bytes ends in "write error" (23): that is a success
        if ( $errno === 23 && $status > 0 )
            $error = '';
        return array( 'status' => $status, 'location' => $location ? (string)$location : null,
                      'error' => ( $ok === false && $error !== '' ) ? $error : null );
    }

    /**
     * The real resolver: the IPv4 and IPv6 addresses of a host.
     *
     * @return string[]
     */
    public static function dnsResolve( $host )
    {
        $addresses = array();
        $records = @dns_get_record( $host, DNS_A | DNS_AAAA );
        foreach ( is_array( $records ) ? $records : array() as $record )
        {
            if ( isset( $record['ip'] ) )
                $addresses[] = $record['ip'];
            if ( isset( $record['ipv6'] ) )
                $addresses[] = $record['ipv6'];
        }
        if ( !$addresses )
        {
            $ipv4 = @gethostbynamel( $host );
            $addresses = is_array( $ipv4 ) ? $ipv4 : array();
        }
        return array_values( array_unique( $addresses ) );
    }

    /**
     * The real content lookup: 'visible' when the node (or the object's main node) exists, its object is
     * published and it is neither hidden nor under a hidden node; 'hidden' when it exists otherwise; 'missing'.
     *
     * @param string $kind 'node' or 'object'
     * @param int $id
     * @return string
     */
    public static function contentState( $kind, $id )
    {
        if ( $kind === 'object' )
        {
            $object = eZContentObject::fetch( (int)$id );
            if ( !$object instanceof eZContentObject )
                return 'missing';
            if ( (int)$object->attribute( 'status' ) !== eZContentObject::STATUS_PUBLISHED )
                return 'hidden';
            $node = $object->attribute( 'main_node' );
        }
        else
        {
            $node = eZContentObjectTreeNode::fetch( (int)$id );
        }
        if ( !$node instanceof eZContentObjectTreeNode )
            return $kind === 'object' ? 'hidden' : 'missing';
        $object = $node->object();
        if ( !$object instanceof eZContentObject || (int)$object->attribute( 'status' ) !== eZContentObject::STATUS_PUBLISHED )
            return 'hidden';
        if ( $node->attribute( 'is_hidden' ) || $node->attribute( 'is_invisible' ) )
            return 'hidden';
        return 'visible';
    }

    /**
     * The real alias lookup: whether a path is a URL alias or module view of this site.
     *
     * @param string $path
     * @return bool
     */
    public static function internalExists( $path )
    {
        $ini = eZINI::instance();
        $siteAccesses = $ini->hasVariable( 'SiteAccessSettings', 'AvailableSiteAccessList' ) ? (array)$ini->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' ) : array();
        $path = self::internalPath( $path, $siteAccesses );
        if ( $path === '' )
            return true; // the front page
        if ( eZURLAliasML::urlToAction( $path ) )
            return true; // a module view such as content/view/full/2 or user/login
        $copy = $path;
        return (bool)eZURLAliasML::translate( $copy );
    }

    /**
     * The path of a link to this site as the URL alias system knows it: without the query, the fragment, the
     * slashes around it and a leading siteaccess name (/admin/content/dashboard is content/dashboard).
     *
     * @param string $path
     * @param string[] $siteAccesses the names of the siteaccesses
     * @return string
     */
    public static function internalPath( $path, array $siteAccesses = array() )
    {
        $path = preg_replace( '/[?#].*$/', '', trim( (string)$path ) );
        $path = trim( $path, '/' );
        $parts = explode( '/', $path, 2 );
        if ( count( $parts ) && $parts[0] !== '' && in_array( $parts[0], $siteAccesses, true ) )
            $path = isset( $parts[1] ) ? $parts[1] : '';
        return $path;
    }
}

?>
