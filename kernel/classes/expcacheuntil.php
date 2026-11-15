<?php
/**
 * File containing the expCacheUntil class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The expiry of the page being rendered: the earliest moment any template of this request said its output stops
 * being true ({cache_until( 'tomorrow' )}, {cache_until( $event_start )}, {cache_until( 0 )}), and every page cache
 * of the request keeps the page no longer than that.
 *
 * A template reports to the request, not through a template variable, so a call works wherever the template runs:
 * the content view, an {include}, a layout block in the pagelayout, the body of a {cache-block}, a Smarty or Twig
 * template (the operator is bridged). Every call lowers the expiry, never raises it ("earliest wins").
 *
 * Where the expiry goes (each one only when a template set an expiry; a request without a call is unchanged):
 *  - the content view cache: eZNodeviewfunctions keeps the absolute expiry in $Result['cache_expires'] and reports it
 *    again when the entry is replayed, so the caches after it get the remaining time, not the original lifetime;
 *    cache_ttl of {set-block scope=root variable=cache_ttl} becomes time() + cache_ttl when the view is generated;
 *  - the template-block cache: a block whose body called cache_until() is kept with its expiry (wrapBlock()) and
 *    expires then; a block read from the cache reports its expiry to the page again;
 *  - the role-aware HTTP cache (ezpHttpCacheListener, ezpHttpCacheContract): maxAge = min(expiry - now, MaxAge),
 *    no stale-while-revalidate past the expiry, the max-age of an answer from it never beyond the expiry;
 *  - the response headers (sendResponseHeaders()): max-age and s-maxage of Cache-Control lowered, s-maxage added
 *    where a shared cache would otherwise keep the page longer (Velocity's DefaultTtl), Expires lowered,
 *    X-LiteSpeed-Cache-Control: no-cache for a page that must not be kept, and X-Exp-Cache-Until.
 *
 * Time strings are read in the site's time zone (site.ini [TimeZoneSettings] TimeZone, else PHP's), and a time of
 * day that has passed today means its next occurrence ('midnight', 'noon', '18:00', 'monday' on a Monday evening).
 *
 * Guide: doc/eng-us/guides/caching-time-dependent-content.md; feature page: doc/eng-us/features/6.0/cache-until.md.
 *
 * @since 6.0.15
 * @package kernel
 */
class expCacheUntil
{
    /** What a cache-block file with an expiry starts with: the mark, the Unix time, a NUL, then the block's text. */
    const BLOCK_MARK = "\0exp-cache-until:";

    /** Calls kept for the debug output at most; a call in a loop of a long list runs once per item. */
    const MAX_CALLS = 100;

    /** The response header that names the expiry. */
    const HEADER = 'X-Exp-Cache-Until';

    /** @var int|null the earliest expiry of this request (Unix time; 0: do not keep the page), null: none set */
    protected static $expires = null;

    /** @var string|null what set it: the template, "cache_ttl", "view cache", "cache-block" */
    protected static $source = null;

    /** @var array scope id => array( 'expires' => int|null, 'source' => string|null ), innermost last */
    protected static $scopes = array();

    /** @var int */
    protected static $nextScope = 1;

    /** @var array list of array( 'argument', 'expires', 'source', 'problem' ) for the debug output */
    protected static $calls = array();

    /** @var int calls in this request, also those past MAX_CALLS */
    protected static $callCount = 0;

    /** @var int|null a fixed clock (tests); null: time() */
    protected static $clock = null;

    /** @var string|null a fixed time zone (tests); null: the site's */
    protected static $zoneName = null;

    /** @var DateTimeZone|null the zone of this request, read once */
    protected static $zone = null;

    /** @var int|null|false Velocity's DefaultTtl, read once per request; false: not read yet */
    protected static $sharedDefault = false;

    /** A new request: no expiry, no scopes. ezpKernelWeb calls it at the start of every request (persistent workers). */
    public static function reset()
    {
        self::$expires = null;
        self::$source = null;
        self::$scopes = array();
        self::$nextScope = 1;
        self::$calls = array();
        self::$callCount = 0;
        self::$zone = null;
        self::$sharedDefault = false;
    }

    /**
     * Fixes the clock (tests); null puts time() back.
     *
     * @param int|null $now
     */
    public static function setClock( $now )
    {
        self::$clock = $now === null ? null : (int)$now;
    }

    /**
     * Fixes the time zone (tests); null puts the site's back.
     *
     * @param string|null $name
     */
    public static function setTimeZone( $name )
    {
        self::$zoneName = $name === null ? null : (string)$name;
        self::$zone = null;
    }

    /**
     * Fixes the lifetime a shared cache gives a page without one (tests); null reads velocity.ini again.
     *
     * @param int|null $seconds
     */
    public static function setSharedCacheDefault( $seconds )
    {
        self::$sharedDefault = $seconds === null ? false : max( 0, (int)$seconds );
    }

    /** @return int the current Unix time */
    public static function now()
    {
        return self::$clock !== null ? self::$clock : time();
    }

    /**
     * The zone time strings are read in: site.ini [TimeZoneSettings] TimeZone when it names a zone, else PHP's
     * default (which ezpKernelWeb already set to that setting when it is not empty).
     *
     * @return DateTimeZone
     */
    public static function timeZone()
    {
        if ( self::$zone !== null )
            return self::$zone;
        $name = self::$zoneName;
        if ( $name === null && class_exists( 'eZINI' ) )
        {
            $ini = eZINI::instance();
            if ( $ini->hasVariable( 'TimeZoneSettings', 'TimeZone' ) )
                $name = trim( (string)$ini->variable( 'TimeZoneSettings', 'TimeZone' ) );
        }
        try
        {
            self::$zone = new DateTimeZone( $name !== null && $name !== '' ? $name : date_default_timezone_get() );
        }
        catch ( Throwable $e )
        {
            self::$zone = new DateTimeZone( date_default_timezone_get() );
        }
        return self::$zone;
    }

    /**
     * The Unix time a value of cache_until() stands for.
     *
     *  - false, 0, '0': 0, the page must not be kept at all
     *  - an integer, a numeric string or a DateTimeInterface: that Unix time (one at or before now means the same as 0)
     *  - any other string: a date or relative time as DateTimeImmutable::modify() reads it from now ('tomorrow',
     *    'next hour' = one hour from now, '+15 minutes', 'next monday', '2026-12-24 18:00'), in the site's time zone;
     *    a date without a time keeps the current time of day ('2026-12-24 00:00' for midnight); a time of day that
     *    has already passed today is its next occurrence ('midnight', 'noon', '18:00')
     *  - null, true, '', an array or a string that is not a time: null, the call is ignored ($problem says why)
     *
     * @param mixed $value
     * @param int|null $now
     * @param string|null $problem why the value was ignored or is in the past, null when it is a time to come
     * @return int|null
     */
    public static function resolve( $value, $now = null, &$problem = null )
    {
        $problem = null;
        $now = $now === null ? self::now() : (int)$now;
        if ( $value === false || $value === 0 || $value === 0.0 || $value === '0' )
            return 0;
        if ( $value instanceof DateTimeInterface )
            $value = $value->getTimestamp();
        if ( $value === null || $value === true || $value === '' || is_array( $value ) || is_object( $value ) )
        {
            $problem = 'no time given (' . ( is_object( $value ) ? get_class( $value ) : gettype( $value ) ) . ')';
            return null;
        }
        if ( is_int( $value ) || is_float( $value ) || ( is_string( $value ) && preg_match( '/^\s*-?\d+(\.\d+)?\s*$/', $value ) ) )
        {
            $timestamp = (int)$value;
            if ( $timestamp <= $now )
            {
                $problem = 'the time ' . $timestamp . ' has passed; for a lifetime in seconds write \'+N seconds\'';
                return 0;
            }
            return $timestamp;
        }
        $text = trim( (string)$value );
        $timestamp = self::modify( $text, $now );
        if ( $timestamp === null )
        {
            $problem = "'" . $text . "' is not a date or relative time";
            return null;
        }
        if ( $timestamp <= $now && self::rolls( $text ) )
        {
            // a time of day that passed today: the same one tomorrow ('midnight', 'noon', '18:00', 'monday' on Monday)
            $next = self::modify( $text, $now + 86400 );
            if ( $next !== null && $next > $now )
                return $next;
        }
        if ( $timestamp <= $now )
        {
            $problem = "'" . $text . "' is not in the future";
            return 0;
        }
        return $timestamp;
    }

    /**
     * $text read as a date or relative time from $base, in the site's zone; null when it is not one.
     *
     * @param string $text
     * @param int $base
     * @return int|null
     */
    protected static function modify( $text, $base )
    {
        try
        {
            $date = new DateTimeImmutable( '@' . (int)$base );
            $date = $date->setTimezone( self::timeZone() );
            $result = @$date->modify( $text );
        }
        catch ( Throwable $e )
        {
            return null;
        }
        return $result instanceof DateTimeInterface ? $result->getTimestamp() : null;
    }

    /**
     * Whether a string that resolved to the past may mean its next occurrence: not a date, not backwards in time.
     *
     * @param string $text
     * @return bool
     */
    protected static function rolls( $text )
    {
        return !preg_match( '/^\s*-|\bago\b|\blast\b|\byesterday\b|\bprevious\b|\d{4}-\d{1,2}-\d{1,2}|\d{1,2}\.\d{1,2}\.\d{2,4}|\d{1,2}\/\d{1,2}\/\d{2,4}|@/i', $text );
    }

    /**
     * Lowers the expiry of this request, and of every open scope (a view or cache-block being generated), to
     * $timestamp when it is earlier. 0 (or any time that has passed): the page must not be kept.
     *
     * @param int $timestamp
     * @param string $source what set it, for the debug output
     * @return int the expiry of the request now
     */
    public static function limit( $timestamp, $source = '' )
    {
        $timestamp = max( 0, (int)$timestamp );
        if ( self::$expires === null || $timestamp < self::$expires )
        {
            self::$expires = $timestamp;
            self::$source = (string)$source;
        }
        foreach ( self::$scopes as $id => $scope )
        {
            if ( $scope['expires'] === null || $timestamp < $scope['expires'] )
                self::$scopes[$id] = array( 'expires' => $timestamp, 'source' => (string)$source );
        }
        return self::$expires;
    }

    /**
     * What {cache_until( $value )} does: resolve() and limit(). A value that is not a time is ignored, with a
     * warning in the debug output.
     *
     * @param mixed $value
     * @param string $source the template that called it
     * @return int|null the Unix time the value stands for, null when ignored
     */
    public static function request( $value, $source = '' )
    {
        $problem = null;
        $timestamp = self::resolve( $value, null, $problem );
        self::$callCount++;
        if ( count( self::$calls ) < self::MAX_CALLS )
            self::$calls[] = array( 'argument' => is_scalar( $value ) || $value === null ? $value : gettype( $value ),
                                    'expires' => $timestamp, 'source' => (string)$source, 'problem' => $problem );
        if ( $problem !== null && class_exists( 'eZDebug' ) && eZDebug::isDebugEnabled() )
        {
            $where = $source !== '' ? ' in ' . $source : '';
            if ( $timestamp === null )
                eZDebug::writeWarning( 'cache_until() ignored' . $where . ': ' . $problem, __METHOD__ );
            else
                eZDebug::writeNotice( 'cache_until()' . $where . ': ' . $problem . '; the page is not kept', __METHOD__ );
        }
        if ( $timestamp !== null )
            self::limit( $timestamp, $source );
        return $timestamp;
    }

    /**
     * Seconds from now until the time $value stands for (resolve()); 0 for a time that has passed, false and 0;
     * null when $value is not a time.
     *
     * @param mixed $value
     * @param int|null $now
     * @return int|null
     */
    public static function secondsUntil( $value, $now = null )
    {
        $now = $now === null ? self::now() : (int)$now;
        $timestamp = self::resolve( $value, $now );
        if ( $timestamp === null )
            return null;
        return max( 0, $timestamp - $now );
    }

    /** @return int|null the expiry of this request (0: not to be kept), null when no template set one */
    public static function expires()
    {
        return self::$expires;
    }

    /** @return string|null what set the expiry */
    public static function source()
    {
        return self::$source;
    }

    /**
     * Seconds the page may still be kept: 0 when it must not be kept, null when no template set an expiry.
     *
     * @param int|null $now
     * @return int|null
     */
    public static function remaining( $now = null )
    {
        if ( self::$expires === null )
            return null;
        return max( 0, self::$expires - ( $now === null ? self::now() : (int)$now ) );
    }

    /**
     * Starts a scope: the content view or a cache-block being generated. Calls from here on lower the scope's expiry
     * as well as the request's, until endScope().
     *
     * @return int the scope id for endScope()
     */
    public static function beginScope()
    {
        $id = self::$nextScope++;
        self::$scopes[$id] = array( 'expires' => null, 'source' => null );
        return $id;
    }

    /**
     * Ends a scope and every scope begun inside it that was never ended.
     *
     * @param int $id what beginScope() returned
     * @return array|null array( 'expires' => int|null, 'source' => string|null ); null when the scope is not open
     */
    public static function endScope( $id )
    {
        if ( !isset( self::$scopes[$id] ) )
            return null;
        $scope = self::$scopes[$id];
        foreach ( array_keys( self::$scopes ) as $open )
        {
            if ( $open >= $id )
                unset( self::$scopes[$open] );
        }
        return $scope;
    }

    // ── Cache-block files ────────────────────────────────────────────────

    /**
     * What a cache-block file holds: the text alone when the block set no expiry (as before), else the mark, the
     * expiry and the text.
     *
     * @param string $text
     * @param int|null $expires
     * @return string
     */
    public static function wrapBlock( $text, $expires )
    {
        if ( $expires === null )
            return (string)$text;
        return self::BLOCK_MARK . max( 0, (int)$expires ) . "\0" . (string)$text;
    }

    /**
     * The text and the expiry of a cache-block file (wrapBlock()).
     *
     * @param string $data
     * @return array array( text, expires|null )
     */
    public static function unwrapBlock( $data )
    {
        $data = (string)$data;
        $length = strlen( self::BLOCK_MARK );
        if ( strncmp( $data, self::BLOCK_MARK, $length ) !== 0 )
            return array( $data, null );
        $end = strpos( $data, "\0", $length );
        if ( $end === false )
            return array( $data, null );
        return array( (string)substr( $data, $end + 1 ), (int)substr( $data, $length, $end - $length ) );
    }

    // ── Response headers ─────────────────────────────────────────────────

    /**
     * Seconds a shared cache in front keeps a page that names no lifetime of its own: Velocity's
     * velocity.ini [CacheSettings] DefaultTtl when its response cache is enabled, else 0.
     *
     * @return int
     */
    public static function sharedCacheDefault()
    {
        if ( self::$sharedDefault !== false )
            return (int)self::$sharedDefault;
        self::$sharedDefault = 0;
        if ( class_exists( 'eZINI' ) && eZINI::exists( 'velocity.ini' ) )
        {
            $ini = eZINI::instance( 'velocity.ini' );
            if ( $ini->hasVariable( 'CacheSettings', 'Enabled' ) && $ini->variable( 'CacheSettings', 'Enabled' ) === 'enabled'
                 && $ini->hasVariable( 'CacheSettings', 'DefaultTtl' ) )
                self::$sharedDefault = max( 0, (int)$ini->variable( 'CacheSettings', 'DefaultTtl' ) );
        }
        return (int)self::$sharedDefault;
    }

    /**
     * A Cache-Control value lowered to $remaining seconds: every max-age and s-maxage above it is set to it. When the
     * value names neither, s-maxage=$remaining is added for shared caches if they would otherwise keep the page
     * longer: always for 0, else only below $sharedDefault (the lifetime a shared cache gives a page without one).
     * A private or no-store value gets no s-maxage. Nothing is ever raised.
     *
     * @param string $value
     * @param int $remaining
     * @param int $sharedDefault
     * @return string
     */
    public static function adjustCacheControl( $value, $remaining, $sharedDefault = 0 )
    {
        $remaining = max( 0, (int)$remaining );
        $parts = array_values( array_filter( array_map( 'trim', explode( ',', (string)$value ) ), 'strlen' ) );
        $hasAge = false;
        $isPrivate = false;
        foreach ( $parts as $i => $part )
        {
            if ( preg_match( '/^(max-age|s-maxage)\s*=\s*"?(\d+)"?$/i', $part, $m ) )
            {
                $hasAge = true;
                if ( (int)$m[2] > $remaining )
                    $parts[$i] = strtolower( $m[1] ) . '=' . $remaining;
            }
            else if ( preg_match( '/^(private|no-store)$/i', $part ) )
            {
                $isPrivate = true;
            }
        }
        if ( !$hasAge && !$isPrivate && ( $remaining === 0 || $remaining < (int)$sharedDefault ) )
            $parts[] = 's-maxage=' . $remaining;
        return implode( ', ', $parts );
    }

    /**
     * The Expires header for an expiry: null when $value already says the same time or an earlier one (the kernel's
     * default is in 1997), else the HTTP date of $expires.
     *
     * @param string|null $value
     * @param int $expires
     * @return string|null
     */
    public static function adjustExpires( $value, $expires )
    {
        if ( $value === null )
            return null;
        $current = strtotime( (string)$value );
        if ( $current !== false && $current <= $expires )
            return null;
        return gmdate( 'D, d M Y H:i:s', max( 0, (int)$expires ) ) . ' GMT';
    }

    /**
     * The value of the X-Exp-Cache-Until header: the expiry in the site's zone (ISO 8601), "0" for a page that must
     * not be kept; with $source, '; source="..."'.
     *
     * @param int $expires
     * @param string|null $source
     * @return string
     */
    public static function headerValue( $expires, $source = null )
    {
        if ( (int)$expires <= 0 )
            $value = '0';
        else
            $value = ( new DateTimeImmutable( '@' . (int)$expires ) )->setTimezone( self::timeZone() )->format( DATE_ATOM );
        if ( $source !== null && $source !== '' )
            $value .= '; source="' . str_replace( array( '"', "\r", "\n" ), array( "'", ' ', ' ' ), $source ) . '"';
        return $value;
    }

    /**
     * The headers of this response, name (lower case) => value, the last of each name.
     *
     * @return array
     */
    protected static function sentHeaders()
    {
        $headers = array();
        foreach ( headers_list() as $line )
        {
            $pos = strpos( $line, ':' );
            if ( $pos !== false )
                $headers[strtolower( trim( substr( $line, 0, $pos ) ) )] = trim( substr( $line, $pos + 1 ) );
        }
        return $headers;
    }

    /**
     * The changes the response headers need for this request's expiry: name => value (null removes nothing; every
     * name listed is replaced). Empty when no template set an expiry.
     *
     * @param array $headers the response's headers, lower-case name => value
     * @param bool $anonymous the visitor is not signed in (the LiteSpeed header goes to anonymous visitors only)
     * @param bool $liteSpeedMaxAge site.ini [CacheUntilSettings] LiteSpeedMaxAge=enabled
     * @param bool $withSource the header names what set the expiry (debug output on)
     * @return array
     */
    public static function headerChanges( array $headers, $anonymous = true, $liteSpeedMaxAge = false, $withSource = false )
    {
        if ( self::$expires === null )
            return array();
        $remaining = self::remaining();
        $changes = array();
        $cacheControl = isset( $headers['cache-control'] ) ? $headers['cache-control'] : 'no-cache, must-revalidate';
        $adjusted = self::adjustCacheControl( $cacheControl, $remaining, self::sharedCacheDefault() );
        if ( $adjusted !== $cacheControl || !isset( $headers['cache-control'] ) )
            $changes['Cache-Control'] = $adjusted;
        $expires = self::adjustExpires( isset( $headers['expires'] ) ? $headers['expires'] : null, self::now() + $remaining );
        if ( $expires !== null )
            $changes['Expires'] = $expires;
        // LiteSpeed keeps a page for the max-age of its rewrite rule whatever Cache-Control says (with "ignore response
        // Cache-Control", the default of many hosts); its own header is always read. "no-cache" only ever keeps a page
        // out; a lifetime is sent only when the site asked for it, as it could also make a page cacheable.
        if ( $remaining === 0 )
            $changes['X-LiteSpeed-Cache-Control'] = 'no-cache';
        else if ( $liteSpeedMaxAge && $anonymous && !preg_match( '/\b(private|no-store)\b/i', $adjusted ) )
            $changes['X-LiteSpeed-Cache-Control'] = 'max-age=' . $remaining;
        $changes[self::HEADER] = self::headerValue( self::$expires, $withSource ? self::$source : null );
        return $changes;
    }

    /**
     * Applies this request's expiry to the response headers (headerChanges()), before the page is sent. Called by
     * ezpKernelWeb after the pagelayout ran. Does nothing when no template set an expiry or the headers are gone.
     */
    public static function sendResponseHeaders()
    {
        if ( self::$expires === null || headers_sent() )
            return;
        $ini = class_exists( 'eZINI' ) ? eZINI::instance() : null;
        $liteSpeed = $ini && $ini->hasVariable( 'CacheUntilSettings', 'LiteSpeedMaxAge' )
                     && $ini->variable( 'CacheUntilSettings', 'LiteSpeedMaxAge' ) === 'enabled';
        $anonymous = !class_exists( 'eZUser' ) || !eZUser::isCurrentUserRegistered();
        $debug = class_exists( 'eZDebug' ) && eZDebug::isDebugEnabled();
        $headers = self::sentHeaders();
        $changes = self::headerChanges( $headers, $anonymous, $liteSpeed, $debug );
        foreach ( $changes as $name => $value )
            header( $name . ': ' . $value, true );
        // A Cache-Control that now names a lifetime contradicts the HTTP/1.0 "Pragma: no-cache" (ezpKernelWeb drops it
        // the same way when [HTTPHeaderSettings] makes a page cacheable)
        if ( isset( $changes['Cache-Control'] ) && self::remaining() > 0 && preg_match( '/\bmax-age=\d/i', $changes['Cache-Control'] )
             && !preg_match( '/\bno-cache\b/i', $changes['Cache-Control'] ) && isset( $headers['pragma'] ) )
            header_remove( 'Pragma' );
        if ( $debug )
            eZDebug::writeNotice( self::summary(), __METHOD__ );
    }

    /**
     * One line for the debug output: the expiry, what set it and how many calls there were.
     *
     * @return string
     */
    public static function summary()
    {
        if ( self::$expires === null )
            return 'cache_until: no expiry set';
        $remaining = self::remaining();
        return 'cache_until: ' . ( $remaining === 0 ? 'the page is not kept'
                                    : 'kept until ' . self::headerValue( self::$expires ) . ' (' . $remaining . ' s)' )
               . ( self::$source ? ', set by ' . self::$source : '' )
               . ', ' . self::$callCount . ' call' . ( self::$callCount === 1 ? '' : 's' );
    }

    /**
     * What the debug bar shows: the expiry, its remaining seconds, what set it, the calls.
     *
     * @return array|null null when no template set an expiry
     */
    public static function report()
    {
        if ( self::$expires === null )
            return null;
        return array( 'expires' => self::$expires, 'remaining' => self::remaining(), 'source' => self::$source,
                      'text' => self::headerValue( self::$expires ), 'calls' => self::$calls, 'call_count' => self::$callCount );
    }
}
?>
