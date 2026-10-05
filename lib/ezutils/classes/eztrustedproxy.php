<?php
/**
 * File containing the eZTrustedProxy class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package lib
 */

/**
 * Decides which forwarded request headers can be believed.
 *
 * X-Forwarded-For, X-Forwarded-Proto, X-Forwarded-Host, X-Forwarded-Port and
 * X-Forwarded-Server are ordinary request headers: any visitor can send them.
 * They mean something only when a proxy the site operates wrote them, and the
 * one thing the web server knows for certain is the address of the peer that
 * opened the connection, REMOTE_ADDR. So the headers are honoured only when
 * that peer is in the configured list of trusted proxies
 * (site.ini [HTTPHeaderSettings] TrustedProxies[]), and a list a proxy appends
 * to is read from the right: every entry up to and including the first one
 * that is not a trusted proxy was written by a trusted proxy, everything to its
 * left was written by the visitor.
 *
 * Deliberately free of every other Exponential class, INI and database: it is
 * used by eZSys, and by the HTTP cache contract before the kernel starts and in
 * Exponential Velocity's parent process, so all of them reach the same answer.
 *
 * See doc/bc/6.0/trusted-proxies.md.
 */
class eZTrustedProxy
{
    /**
     * The proxies trusted when site.ini does not say: the loopback addresses.
     *
     * Nothing outside the machine can open a connection from them, so they
     * stand only for a proxy running on the same host (a TLS terminator,
     * Varnish, a local web server in front of PHP).
     *
     * @return array
     */
    public static function defaultList()
    {
        return array( '127.0.0.1', '::1' );
    }

    /**
     * The usable entries of a configured list: trimmed, empty ones dropped, and
     * every entry that is neither an IP address nor an address/prefix range
     * left out, so a typo trusts nothing rather than something unintended.
     *
     * @param mixed $list array of strings (a single string is accepted too)
     * @return array
     */
    public static function normaliseList( $list )
    {
        $result = array();
        foreach ( (array)$list as $entry )
        {
            if ( !is_string( $entry ) )
                continue;
            $entry = trim( $entry );
            if ( $entry === '' )
                continue;
            if ( self::parseRange( $entry ) !== false )
                $result[] = $entry;
        }
        return $result;
    }

    /**
     * An address as a forwarded header or REMOTE_ADDR carries it, reduced to
     * the bare IP address: whitespace, the brackets of "[2001:db8::1]" and a
     * port ("192.0.2.1:4711", "[2001:db8::1]:4711") removed.
     *
     * @param mixed $address
     * @return string|false the address, or false when it is not a valid IP address
     */
    public static function normaliseAddress( $address )
    {
        if ( !is_string( $address ) )
            return false;
        $address = trim( $address );
        if ( $address === '' )
            return false;
        if ( $address[0] === '[' )
        {
            // [IPv6] or [IPv6]:port
            if ( !preg_match( '/^\[([^\]]+)\](?::\d{1,5})?$/', $address, $matches ) )
                return false;
            $address = $matches[1];
        }
        else if ( preg_match( '/^(\d{1,3}(?:\.\d{1,3}){3}):\d{1,5}$/', $address, $matches ) )
        {
            // IPv4:port
            $address = $matches[1];
        }
        if ( filter_var( $address, FILTER_VALIDATE_IP ) === false )
            return false;
        return $address;
    }

    /**
     * Whether $address is one of the $trusted addresses or inside one of its
     * ranges. IPv4 and IPv6 alike; an IPv4-mapped IPv6 address
     * (::ffff:192.0.2.1, what a dual-stack socket reports) counts as its IPv4
     * address.
     *
     * @param string $address
     * @param array $trusted entries as normaliseList() returns them
     * @return bool
     */
    public static function isTrusted( $address, array $trusted )
    {
        $address = self::normaliseAddress( $address );
        if ( $address === false || !$trusted )
            return false;
        $packed = self::pack( $address );
        if ( $packed === false )
            return false;
        foreach ( $trusted as $entry )
        {
            $range = self::parseRange( $entry );
            if ( $range === false )
                continue;
            list( $network, $bits ) = $range;
            if ( strlen( $network ) !== strlen( $packed ) )
                continue;
            if ( self::inRange( $packed, $network, $bits ) )
                return true;
        }
        return false;
    }

    /**
     * How many proxies in front of this server can be vouched for: 0 when
     * $remoteAddr is not trusted (forwarded headers must then be ignored),
     * otherwise 1 for it plus one for every trusted address read from the
     * right of $forwardedFor before the first one that is not.
     *
     * Used to pick the value a trusted proxy wrote from a header that proxies
     * append to, see forwardedValue().
     *
     * @param string|null $remoteAddr REMOTE_ADDR
     * @param string|null $forwardedFor the X-Forwarded-For header, if any
     * @param array $trusted
     * @return int
     */
    public static function trustedHops( $remoteAddr, $forwardedFor, array $trusted )
    {
        if ( !self::isTrusted( $remoteAddr, $trusted ) )
            return 0;
        $hops = 1;
        foreach ( array_reverse( self::splitList( $forwardedFor ) ) as $entry )
        {
            if ( !self::isTrusted( $entry, $trusted ) )
                break;
            ++$hops;
        }
        return $hops;
    }

    /**
     * The address of the client.
     *
     * $remoteAddr when it is not a trusted proxy, or when the header is
     * missing. Otherwise $header (X-Forwarded-For, or a single-address header
     * such as X-Real-IP) is read from the right: trusted proxies are skipped
     * and the first address that is not one is the client. Everything to the
     * left of it was sent by that client and is never looked at.
     *
     * Should the walk reach an entry that is not an IP address ("unknown",
     * garbage) or run out of entries, the last trusted proxy passed is the
     * farthest address that can be vouched for, and that is returned.
     *
     * @param string|null $remoteAddr REMOTE_ADDR
     * @param string|null $header the value of the configured client IP header
     * @param array $trusted
     * @return string|null
     */
    public static function clientAddress( $remoteAddr, $header, array $trusted )
    {
        if ( !self::isTrusted( $remoteAddr, $trusted ) )
            return $remoteAddr;
        $entries = self::splitList( $header );
        if ( !$entries )
            return $remoteAddr;
        $last = $remoteAddr;
        foreach ( array_reverse( $entries ) as $entry )
        {
            $address = self::normaliseAddress( $entry );
            if ( $address === false )
                return $last;
            if ( !self::isTrusted( $address, $trusted ) )
                return $address;
            $last = $address;
        }
        return $last;
    }

    /**
     * The value of a forwarded header that the outermost trusted proxy wrote.
     *
     * A proxy either sets such a header or appends its own value to it. Of a
     * list, the entry $hops places from the right is the one the outermost
     * trusted proxy added; anything to its left came from the client. A header
     * with fewer entries than that was set, not appended to, and its first
     * entry is the answer.
     *
     * @param string|null $value the header
     * @param int $hops trustedHops() for the request; 0 means not trusted
     * @return string|null null when not trusted or empty
     */
    public static function forwardedValue( $value, $hops )
    {
        $hops = (int)$hops;
        if ( $hops < 1 )
            return null;
        $entries = self::splitList( $value );
        if ( !$entries )
            return null;
        $index = max( 0, count( $entries ) - $hops );
        return $entries[$index];
    }

    /**
     * The comma-separated entries of a header, trimmed, empty ones dropped.
     *
     * @param mixed $value
     * @return array
     */
    private static function splitList( $value )
    {
        if ( !is_string( $value ) || $value === '' )
            return array();
        $result = array();
        foreach ( explode( ',', $value ) as $entry )
        {
            $entry = trim( $entry );
            if ( $entry !== '' )
                $result[] = $entry;
        }
        return $result;
    }

    /**
     * @param string $address a valid IP address
     * @return string|false 4 bytes for IPv4 (IPv4-mapped IPv6 included), 16 for IPv6
     */
    private static function pack( $address )
    {
        $packed = @inet_pton( $address );
        if ( $packed === false )
            return false;
        if ( strlen( $packed ) === 16 && substr( $packed, 0, 12 ) === "\0\0\0\0\0\0\0\0\0\0\xff\xff" )
            return substr( $packed, 12 );
        return $packed;
    }

    /**
     * @param string $entry "192.0.2.1", "10.0.0.0/8", "2001:db8::/32", ...
     * @return array|false ( packed network, prefix bits )
     */
    private static function parseRange( $entry )
    {
        $entry = trim( (string)$entry );
        $bits = null;
        $slash = strpos( $entry, '/' );
        if ( $slash !== false )
        {
            $prefix = substr( $entry, $slash + 1 );
            $entry = substr( $entry, 0, $slash );
            if ( $prefix === '' || !ctype_digit( $prefix ) )
                return false;
            $bits = (int)$prefix;
        }
        $address = self::normaliseAddress( $entry );
        if ( $address === false )
            return false;
        $packed = self::pack( $address );
        if ( $packed === false )
            return false;
        $max = strlen( $packed ) * 8;
        if ( $bits === null )
            $bits = $max;
        else if ( strlen( $packed ) === 4 && strpos( $address, ':' ) !== false )
            // ::ffff:10.0.0.0/104 is 10.0.0.0/8
            $bits -= 96;
        if ( $bits < 0 || $bits > $max )
            return false;
        return array( $packed, $bits );
    }

    /**
     * @param string $packed
     * @param string $network same length as $packed
     * @param int $bits
     * @return bool
     */
    private static function inRange( $packed, $network, $bits )
    {
        $whole = intdiv( $bits, 8 );
        if ( $whole > 0 && strncmp( $packed, $network, $whole ) !== 0 )
            return false;
        $rest = $bits % 8;
        if ( $rest === 0 )
            return true;
        $mask = ( 0xff << ( 8 - $rest ) ) & 0xff;
        return ( ord( $packed[$whole] ) & $mask ) === ( ord( $network[$whole] ) & $mask );
    }
}
