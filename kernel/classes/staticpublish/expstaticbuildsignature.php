<?php
/**
 * File containing the expStaticBuildSignature class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The signed request header of a static publishing build, and the kernel's answer to it: a page the build fetches
 * from this server is rendered without debug output, as an anonymous visitor outside site.ini [DebugSettings]
 * DebugIPList[] sees it, although the server's own addresses are on that list.
 *
 * The build (expStaticBuild) sends with every request it makes
 *
 *   X-Exp-Static-Build: v1.<issued unix time>.<nonce, 32 hex>.<HMAC-SHA256 of "staticpublish-nodebug/1|issued|nonce", base64url>
 *
 * signed afresh for each request. The key is derived from the installation's secret
 * (expMailSecret::derive( 'staticpublish-nodebug' ), the secret the form tokens of static copies and the mail
 * preference links are signed with), so every server of a cluster and Velocity check the same signature.
 *
 * eZDebug::updateSettings() asks noDebugRequested() and, when it says yes, turns debug output off for the request:
 * the debug report and the debug bar, DebugByUser, the template debug comments (TemplateSettings Debug,
 * ShowTemplatePathComments, ShowXHTMLCode). The header can only ever turn debug output off, never on, and it counts
 * only when all of this holds:
 *
 *  - the request comes from the server itself: REMOTE_ADDR (never a forwarded header) is a loopback address, the
 *    address the request came in on (SERVER_ADDR) or an address of one of the server's network interfaces;
 *  - the installation has a secret (none is made to check a header);
 *  - the signature is right, it was issued at most LIFETIME seconds ago (and not more than SKEW in the future);
 *  - its nonce has not been used more than MAX_USES times (the request and the redirects curl follows with the
 *    same headers): each use is a file made exclusively under var/cache/staticbuild-nonces, so FPM processes and
 *    Velocity workers share the count; files older than the lifetime are removed as new ones come.
 *
 * Anything else leaves the request as it was: debug output as site.ini decides it.
 *
 * @since 6.0.15
 * @package kernel
 */
class expStaticBuildSignature
{
    /** The request header */
    const HEADER = 'X-Exp-Static-Build';

    /** The header as PHP and Velocity put it into $_SERVER */
    const SERVER_KEY = 'HTTP_X_EXP_STATIC_BUILD';

    const VERSION = 'v1';

    /** Seconds a signature is good for after it was issued */
    const LIFETIME = 120;

    /** Seconds a signature may be issued in the future (clocks of a cluster) */
    const SKEW = 30;

    /** How many requests one signature may serve: the request and the redirects curl follows (MAXREDIRS 3) */
    const MAX_USES = 4;

    /** @var string|null tests only: the raw key */
    protected static $testKey = null;

    /** @var string|null tests only: the directory of the nonce files */
    protected static $nonceDir = null;

    /** @var array|null tests only: the server's own addresses */
    protected static $testAddresses = null;

    /** @var array|null the server's own addresses, once per process */
    protected static $addresses = null;

    /** @var bool|null this request's verdict, once per request */
    protected static $verdict = null;

    /** @var string why the last request asked was not taken as a build's ('' when it was) */
    protected static $reason = '';

    /** @var bool debug output was turned off for this request (restored at the next) */
    protected static $applied = false;

    /** @return string the raw key, the installation's secret made when there is none (the build's side) */
    public static function key()
    {
        if ( self::$testKey !== null )
            return self::$testKey;
        return expMailSecret::derive( 'staticpublish-nodebug' );
    }

    /** @return string|null the raw key when the installation has a secret, null otherwise (the checking side) */
    protected static function existingKey()
    {
        if ( self::$testKey !== null )
            return self::$testKey;
        if ( !class_exists( 'expMailSecret' ) || !expMailSecret::exists() )
            return null;
        return expMailSecret::derive( 'staticpublish-nodebug' );
    }

    /**
     * Tests only: a fixed key, the directory of the nonce files and the server's own addresses; null resets each.
     *
     * @param string|null $key
     * @param string|null $nonceDir
     * @param array|null $addresses
     */
    public static function setForTest( $key = null, $nonceDir = null, ?array $addresses = null )
    {
        self::$testKey = $key;
        self::$nonceDir = $nonceDir;
        self::$testAddresses = $addresses;
        self::$addresses = null;
        self::$verdict = null;
        self::$reason = '';
    }

    /**
     * A signature for one request of a build.
     *
     * @param int|null $issued unix time, default now
     * @param string|null $nonce 32 hex digits, default random
     * @param string|null $key the raw key, default key()
     * @return string
     */
    public static function sign( $issued = null, $nonce = null, $key = null )
    {
        $issued = $issued === null ? time() : (int)$issued;
        $nonce = $nonce === null ? bin2hex( random_bytes( 16 ) ) : strtolower( (string)$nonce );
        return self::VERSION . '.' . $issued . '.' . $nonce . '.' . self::mac( $issued, $nonce, $key === null ? self::key() : $key );
    }

    /** @return string the header line for a request: "X-Exp-Static-Build: <signature>" */
    public static function headerLine()
    {
        return self::HEADER . ': ' . self::sign();
    }

    /**
     * Why a signature is not valid; '' when it is. Pure: the uses of its nonce are counted by claim().
     *
     * @param string $token
     * @param int|null $now
     * @param string|null $key the raw key, default key()
     * @return string
     */
    public static function problem( $token, $now = null, $key = null )
    {
        $parts = self::parse( $token );
        if ( $parts === null )
            return 'not a static build signature';
        $now = $now === null ? time() : (int)$now;
        if ( $parts['issued'] > $now + self::SKEW )
            return 'issued in the future';
        if ( $parts['issued'] < $now - self::LIFETIME )
            return 'expired';
        $expected = self::mac( $parts['issued'], $parts['nonce'], $key === null ? self::key() : (string)$key );
        if ( !hash_equals( $expected, $parts['mac'] ) )
            return 'the signature does not match';
        return '';
    }

    /**
     * @param string $token
     * @return array|null issued, nonce, mac
     */
    public static function parse( $token )
    {
        if ( !is_string( $token ) || strlen( $token ) > 200 ||
             !preg_match( '/^' . self::VERSION . '\.([0-9]{1,12})\.([0-9a-f]{32})\.([A-Za-z0-9_-]{43})$/', trim( $token ), $m ) )
            return null;
        return array( 'issued' => (int)$m[1], 'nonce' => $m[2], 'mac' => $m[3] );
    }

    protected static function mac( $issued, $nonce, $key )
    {
        $mac = hash_hmac( 'sha256', 'staticpublish-nodebug/1|' . (int)$issued . '|' . (string)$nonce, (string)$key, true );
        return rtrim( strtr( base64_encode( $mac ), '+/', '-_' ), '=' );
    }

    /**
     * Counts one use of a nonce; false when it was used MAX_USES times already or cannot be counted.
     *
     * @param string $nonce
     * @param int $issued
     * @param int|null $now
     * @return bool
     */
    public static function claim( $nonce, $issued, $now = null )
    {
        if ( !preg_match( '/^[0-9a-f]{32}$/', (string)$nonce ) )
            return false;
        $now = $now === null ? time() : (int)$now;
        $dir = self::nonceDirectory();
        if ( !is_dir( $dir ) )
        {
            if ( !@mkdir( $dir, eZDir::dirMode( 0770 ), true ) && !is_dir( $dir ) )
                return false;
            // Velocity runs as root, PHP-FPM as the site's user: the directory belongs to whoever owns var/cache
            if ( class_exists( 'expAuditWriter' ) )
                expAuditWriter::ownLikeParent( $dir, 0770 );
        }
        $claimed = false;
        for ( $use = 1; $use <= self::MAX_USES; $use++ )
        {
            $handle = @fopen( $dir . '/' . (int)$issued . '-' . $nonce . '.' . $use, 'x' );
            if ( $handle !== false )
            {
                fclose( $handle );
                $claimed = true;
                break;
            }
        }
        if ( $claimed && $use === 1 )
            self::removeOldNonces( $dir, $now );
        return $claimed;
    }

    /** The nonce files of signatures that have expired; their names start with the time they were issued. */
    protected static function removeOldNonces( $dir, $now )
    {
        $handle = @opendir( $dir );
        if ( $handle === false )
            return;
        $limit = $now - self::LIFETIME - self::SKEW;
        while ( ( $name = readdir( $handle ) ) !== false )
        {
            if ( preg_match( '/^([0-9]{1,12})-[0-9a-f]{32}\.[0-9]+$/', $name, $m ) && (int)$m[1] < $limit )
                @unlink( $dir . '/' . $name );
        }
        closedir( $handle );
    }

    /** @return string the directory the uses of the nonces are counted in */
    public static function nonceDirectory()
    {
        if ( self::$nonceDir !== null )
            return rtrim( self::$nonceDir, '/' );
        $root = defined( 'EXP_ROOT_DIR' ) ? EXP_ROOT_DIR : getcwd();
        return rtrim( (string)$root, '/' ) . '/var/cache/staticbuild-nonces';
    }

    /**
     * Whether an address is the server's own: loopback, or one of serverAddresses().
     *
     * @param string $address
     * @param array|null $own default serverAddresses()
     * @return bool
     */
    public static function isLocalAddress( $address, ?array $own = null )
    {
        $address = strtolower( trim( (string)$address ) );
        if ( $address === '' || @inet_pton( $address ) === false )
            return false;
        if ( strpos( $address, '::ffff:' ) === 0 && filter_var( substr( $address, 7 ), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) )
            $address = substr( $address, 7 );
        if ( $address === '::1' || preg_match( '/^127\.[0-9]+\.[0-9]+\.[0-9]+$/', $address ) )
            return true;
        $packed = inet_pton( $address );
        foreach ( $own === null ? self::serverAddresses() : $own as $candidate )
        {
            $candidate = @inet_pton( trim( (string)$candidate ) );
            if ( $candidate !== false && $candidate === $packed )
                return true;
        }
        return false;
    }

    /** @return string[] the addresses of this server: SERVER_ADDR and those of its network interfaces */
    public static function serverAddresses()
    {
        if ( self::$testAddresses !== null )
            return self::$testAddresses;
        $addresses = array();
        if ( isset( $_SERVER['SERVER_ADDR'] ) && is_string( $_SERVER['SERVER_ADDR'] ) )
            $addresses[] = $_SERVER['SERVER_ADDR'];
        if ( self::$addresses === null )
        {
            self::$addresses = array();
            if ( function_exists( 'net_get_interfaces' ) )
            {
                foreach ( (array)@net_get_interfaces() as $interface )
                    foreach ( isset( $interface['unicast'] ) ? (array)$interface['unicast'] : array() as $unicast )
                        if ( isset( $unicast['address'] ) && filter_var( $unicast['address'], FILTER_VALIDATE_IP ) )
                            self::$addresses[] = preg_replace( '/%.*$/', '', $unicast['address'] );
            }
        }
        return array_values( array_unique( array_merge( $addresses, self::$addresses ) ) );
    }

    /**
     * Whether this request is one of a static build of this server that asks for no debug output; decided once per
     * request (resetRequest() starts the next).
     *
     * @param array|null $server default $_SERVER
     * @param int|null $now
     * @return bool
     */
    public static function noDebugRequested( ?array $server = null, $now = null )
    {
        if ( self::$verdict !== null )
            return self::$verdict;
        $server = $server === null ? $_SERVER : $server;
        if ( !isset( $server[self::SERVER_KEY] ) || !is_string( $server[self::SERVER_KEY] ) )
        {
            self::$reason = 'no signature';
            return self::$verdict = false;
        }
        // the address the connection came from: a header (X-Forwarded-For) is never taken for it
        $remote = isset( $server['REMOTE_ADDR'] ) ? (string)$server['REMOTE_ADDR'] : '';
        if ( !self::isLocalAddress( $remote ) )
        {
            self::$reason = 'not from this server';
            return self::$verdict = false;
        }
        $key = self::existingKey();
        if ( $key === null )
        {
            self::$reason = 'the installation has no secret';
            return self::$verdict = false;
        }
        $problem = self::problem( $server[self::SERVER_KEY], $now, $key );
        if ( $problem !== '' )
        {
            self::$reason = $problem;
            return self::$verdict = false;
        }
        $parts = self::parse( $server[self::SERVER_KEY] );
        if ( !self::claim( $parts['nonce'], $parts['issued'], $now ) )
        {
            self::$reason = 'used up';
            return self::$verdict = false;
        }
        self::$reason = '';
        return self::$verdict = true;
    }

    /** @return string why the request was not taken as a static build's ('' when it was) */
    public static function reason()
    {
        return self::$reason;
    }

    /**
     * Turns debug output off for this request: what eZDebug::updateSettings() decided, DebugByUser and the
     * template debug comments. Never turns anything on.
     */
    public static function applyNoDebug()
    {
        $GLOBALS['eZDebugEnabled'] = false;
        $GLOBALS['eZDebugAllowed'] = false;
        $GLOBALS['eZDebugAllowedByIP'] = false;
        $GLOBALS['eZDebugCollect'] = false;
        unset( $GLOBALS['eZDebugUserIDList'], $GLOBALS['eZDebugIPMatch'] );
        foreach ( self::TEMPLATE_GLOBALS as $name )
        {
            // what the template engine had before, given back at the next request (a persistent worker)
            if ( !self::$applied )
                self::$saved[$name] = array_key_exists( $name, $GLOBALS ) ? array( $GLOBALS[$name] ) : null;
            $GLOBALS[$name] = false;
        }
        self::$applied = true;
    }

    /** The template engine's debug switches applyNoDebug() turns off */
    const TEMPLATE_GLOBALS = array( 'eZTemplateDebugInternalsEnabled', 'eZTemplateShowTemplatePathComments', 'eZTemplateDebugXHTMLCodeEnabled' );

    /** @var array the template engine's debug switches before applyNoDebug(): name => array( value ) or null (unset) */
    protected static $saved = array();

    /**
     * A new request (expKernelServiceScope's reset hook): the verdict is made again, and in a persistent worker the
     * template debug switches a static build's request turned off are given back as they were.
     */
    public static function resetRequest()
    {
        self::$verdict = null;
        self::$reason = '';
        if ( self::$applied )
        {
            foreach ( self::$saved as $name => $value )
            {
                if ( $value === null )
                    unset( $GLOBALS[$name] );
                else
                    $GLOBALS[$name] = $value[0];
            }
            self::$saved = array();
            self::$applied = false;
        }
    }
}
