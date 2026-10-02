<?php
/**
 * File containing the expDebugBarIPList class: the IPv4/IPv6 list engine of site.ini [DebugSettings]
 * DebugIPList[].
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * One line of DebugIPList[] is an entry:
 *
 *   <address>[/<prefix>] [; <label>] [; expires=<YYYY-MM-DDTHH:MM[:SS][Z|+HH:MM]>]
 *
 *   DebugIPList[]=203.0.113.7
 *   DebugIPList[]=10.0.0.0/8
 *   DebugIPList[]=2001:db8::/32 ; VPN
 *   DebugIPList[]=203.0.113.7/32 ; Laptop ; expires=2026-10-02T15:00
 *   DebugIPList[]=commandline
 *
 * Plain lines mean what they always meant. An expiry without an offset is in the server's time zone; an expired
 * entry no longer matches but stays in the list until someone removes it. eZDebug::isAllowedByCurrentIP() asks
 * match() when this class exists, so the debug bar's test and the real check are one implementation.
 *
 * Every function is static and works without the kernel (no eZINI), so eZDebug can call it at boot.
 */
class expDebugBarIPList
{
    /** The word eZDebug matches for command line scripts (no IP address). */
    const COMMANDLINE = 'commandline';

    /** The format an expiry is written in (server time zone, minutes). */
    const EXPIRY_FORMAT = 'Y-m-d\TH:i';

    // ------------------------------------------------------------------ addresses

    /**
     * Parses an address or a network.
     *
     * @param string $text '203.0.113.7', '203.0.113.0/24', '2001:db8::1', '2001:db8::/64', '::ffff:1.2.3.4'
     * @return array|string array( address (as given, trimmed), network (canonical, host bits cleared), prefix,
     *                      family 4|6, packed (network bytes), cidr (canonical network/prefix) ) or an error message
     */
    public static function parseAddress( $text )
    {
        $text = trim( (string)$text );
        if ( $text === '' )
            return 'empty address';
        $prefix = null;
        $address = $text;
        if ( strpos( $text, '/' ) !== false )
        {
            list( $address, $prefixText ) = explode( '/', $text, 2 );
            if ( !preg_match( '/^\d{1,3}$/', $prefixText ) )
                return "'$text': the prefix after / must be a number";
            $prefix = (int)$prefixText;
        }
        // brackets and zone ids are not part of an address in this list
        if ( strpbrk( $address, '[]%' ) !== false )
            return "'$text' is not an IPv4 or IPv6 address";
        $packed = @inet_pton( $address );
        if ( $packed === false || !filter_var( $address, FILTER_VALIDATE_IP ) )
            return "'$text' is not an IPv4 or IPv6 address";
        $family = strlen( $packed ) === 4 ? 4 : 6;
        $max = $family === 4 ? 32 : 128;
        if ( $prefix === null )
            $prefix = $max;
        if ( $prefix > $max )
            return "'$text': an IPv$family prefix is at most /$max";
        $network = self::maskPacked( $packed, $prefix );
        $canonical = inet_ntop( $network );
        return array( 'address' => $text, 'network' => $canonical, 'prefix' => $prefix, 'family' => $family,
                      'packed' => $network, 'cidr' => $canonical . '/' . $prefix,
                      'host_bits' => $network !== $packed );
    }

    /**
     * The address as bytes, with an IPv4-mapped IPv6 address (::ffff:a.b.c.d) turned into its IPv4 address, so a
     * dual-stack server that reports IPv4 clients that way still matches IPv4 entries.
     *
     * @param string $ip
     * @return string|false
     */
    public static function packedClient( $ip )
    {
        $ip = trim( (string)$ip );
        if ( $ip === '' || !filter_var( $ip, FILTER_VALIDATE_IP ) )
            return false;
        $packed = @inet_pton( $ip );
        if ( $packed === false )
            return false;
        if ( strlen( $packed ) === 16 && substr( $packed, 0, 12 ) === str_repeat( "\0", 10 ) . "\xff\xff" )
            return substr( $packed, 12 );
        return $packed;
    }

    /** The first $prefix bits of $packed, the rest cleared. */
    protected static function maskPacked( $packed, $prefix )
    {
        $out = '';
        $len = strlen( $packed );
        for ( $i = 0; $i < $len; $i++ )
        {
            $bits = max( 0, min( 8, $prefix - $i * 8 ) );
            $mask = $bits === 0 ? 0 : ( 0xff << ( 8 - $bits ) ) & 0xff;
            $out .= chr( ord( $packed[$i] ) & $mask );
        }
        return $out;
    }

    /**
     * Whether an address is inside a network.
     *
     * @param string $ip
     * @param string $network address or CIDR
     * @return bool
     */
    public static function contains( $network, $ip )
    {
        $net = is_array( $network ) ? $network : self::parseAddress( $network );
        $client = self::packedClient( $ip );
        if ( !is_array( $net ) || $client === false || strlen( $client ) !== strlen( $net['packed'] ) )
            return false;
        return self::maskPacked( $client, $net['prefix'] ) === $net['packed'];
    }

    /**
     * The entries to suggest for an address: itself (/32, /128) and its network (/24, /64).
     *
     * @param string $ip
     * @return array self, network, family (null when $ip is not an address)
     */
    public static function suggest( $ip )
    {
        $packed = self::packedClient( $ip );
        if ( $packed === false )
            return array( 'self' => null, 'network' => null, 'family' => null );
        $family = strlen( $packed ) === 4 ? 4 : 6;
        $self = inet_ntop( $packed ) . ( $family === 4 ? '/32' : '/128' );
        $netPrefix = $family === 4 ? 24 : 64;
        $network = inet_ntop( self::maskPacked( $packed, $netPrefix ) ) . '/' . $netPrefix;
        return array( 'self' => $self, 'network' => $network, 'family' => $family );
    }

    // ------------------------------------------------------------------ entries

    /**
     * Parses one line of the list.
     *
     * @param string $line
     * @param int|null $now Timestamp (null: time())
     * @return array line, address, network, prefix, family, cidr, label, expires (ISO 8601 with offset), expires_ts,
     *               expired, valid, error, plain (no label, no expiry), special ('commandline' or null)
     */
    public static function parseEntry( $line, $now = null )
    {
        $now = $now === null ? time() : (int)$now;
        $line = trim( (string)$line );
        $entry = array( 'line' => $line, 'address' => '', 'network' => null, 'prefix' => null, 'family' => null,
                        'cidr' => null, 'label' => '', 'expires' => null, 'expires_ts' => null, 'expired' => false,
                        'valid' => false, 'error' => null, 'plain' => true, 'special' => null );
        $parts = array_map( 'trim', explode( ';', $line ) );
        $entry['address'] = array_shift( $parts );
        foreach ( $parts as $part )
        {
            if ( $part === '' )
                continue;
            $entry['plain'] = false;
            if ( preg_match( '/^expires\s*=\s*(.*)$/i', $part, $m ) )
            {
                $ts = self::parseTime( $m[1] );
                if ( $ts === false )
                {
                    $entry['error'] = "the expiry '" . $m[1] . "' is not a date (YYYY-MM-DDTHH:MM)";
                    // an unreadable expiry never lets anyone in
                    $entry['expired'] = true;
                }
                else
                {
                    $entry['expires_ts'] = $ts;
                    $entry['expires'] = date( 'c', $ts );
                    $entry['expired'] = $ts <= $now;
                }
            }
            else if ( preg_match( '/^label\s*=\s*(.*)$/i', $part, $m ) )
                $entry['label'] = trim( $m[1] );
            else
                $entry['label'] = $entry['label'] === '' ? $part : $entry['label'] . ' ' . $part;
        }
        if ( strtolower( $entry['address'] ) === self::COMMANDLINE )
        {
            $entry['special'] = self::COMMANDLINE;
            $entry['valid'] = $entry['error'] === null;
            return $entry;
        }
        $parsed = self::parseAddress( $entry['address'] );
        if ( !is_array( $parsed ) )
        {
            $entry['error'] = $parsed;
            return $entry;
        }
        $entry['network'] = $parsed['network'];
        $entry['prefix'] = $parsed['prefix'];
        $entry['family'] = $parsed['family'];
        $entry['cidr'] = $parsed['cidr'];
        $entry['valid'] = $entry['error'] === null;
        return $entry;
    }

    /**
     * Writes an entry as one line.
     *
     * @param string $address address or CIDR (validated)
     * @param string $label
     * @param string|int|null $expires '' / null (until removed), a timestamp, '+1h', '+30m', '+2d', 'today',
     *                                 'tomorrow', or a date
     * @param int|null $now
     * @return string
     * @throws InvalidArgumentException for a bad address, label or expiry
     */
    public static function formatEntry( $address, $label = '', $expires = null, $now = null )
    {
        $address = trim( (string)$address );
        if ( strtolower( $address ) !== self::COMMANDLINE )
        {
            $parsed = self::parseAddress( $address );
            if ( !is_array( $parsed ) )
                throw new InvalidArgumentException( $parsed );
        }
        $label = trim( preg_replace( '/\s+/', ' ', (string)$label ) );
        if ( $label !== '' && ( strpbrk( $label, ";\r\n\0" ) !== false || strpos( $label, '##' ) !== false
                                || strpos( $label, '*/' ) !== false || preg_match( '/^(expires|label)\s*=/i', $label ) ) )
            throw new InvalidArgumentException( "A label cannot contain ';', '##', '*/' or a line break, nor start with expires=" );
        $line = $address;
        if ( $label !== '' )
            $line .= ' ; ' . $label;
        if ( $expires !== null && $expires !== '' && $expires !== false )
        {
            $ts = self::expiryTimestamp( $expires, $now );
            if ( $ts === false )
                throw new InvalidArgumentException( "The expiry '$expires' is not understood (+1h, +30m, +2d, today, tomorrow, YYYY-MM-DDTHH:MM)" );
            $line .= ' ; expires=' . date( self::EXPIRY_FORMAT, $ts );
        }
        return $line;
    }

    /**
     * An expiry as a timestamp: a timestamp, '+<n>m|h|d', 'today' (23:59 today), 'tomorrow' (23:59 tomorrow), or a
     * date.
     *
     * @param string|int $expires
     * @param int|null $now
     * @return int|false
     */
    public static function expiryTimestamp( $expires, $now = null )
    {
        $now = $now === null ? time() : (int)$now;
        if ( is_int( $expires ) )
            return $expires;
        $text = strtolower( trim( (string)$expires ) );
        if ( preg_match( '/^\+?(\d+)\s*(m|min|h|hour|hours|d|day|days)$/', $text, $m ) && strpos( $text, '+' ) === 0 )
        {
            $unit = $m[2][0] === 'm' ? 60 : ( $m[2][0] === 'h' ? 3600 : 86400 );
            return $now + (int)$m[1] * $unit;
        }
        if ( $text === 'today' )
            return mktime( 23, 59, 0, (int)date( 'n', $now ), (int)date( 'j', $now ), (int)date( 'Y', $now ) );
        if ( $text === 'tomorrow' )
            return mktime( 23, 59, 0, (int)date( 'n', $now ), (int)date( 'j', $now ) + 1, (int)date( 'Y', $now ) );
        return self::parseTime( $expires );
    }

    /**
     * A date as written in an entry: YYYY-MM-DD, YYYY-MM-DDTHH:MM, with seconds, with Z or an offset.
     *
     * @param string $text
     * @return int|false
     */
    public static function parseTime( $text )
    {
        $text = trim( (string)$text );
        if ( !preg_match( '/^\d{4}-\d{2}-\d{2}([T ]\d{2}:\d{2}(:\d{2})?)?(Z|[+-]\d{2}:?\d{2})?$/i', $text ) )
            return false;
        $ts = strtotime( $text );
        return $ts === false ? false : $ts;
    }

    // ------------------------------------------------------------------ the list

    /**
     * The entry of the list that lets an address in, null when none does. Expired and invalid entries never match.
     * For a request without an address (command line) the plain entry 'commandline' matches when $isShell.
     *
     * @param string|null $ip
     * @param string[] $lines DebugIPList
     * @param int|null $now
     * @param bool $isShell
     * @return array|null The parsed entry plus its index
     */
    public static function match( $ip, array $lines, $now = null, $isShell = false )
    {
        foreach ( array_values( $lines ) as $index => $line )
        {
            if ( !is_scalar( $line ) )
                continue;
            $entry = self::parseEntry( $line, $now );
            if ( !$entry['valid'] || $entry['expired'] )
                continue;
            $entry['index'] = $index;
            if ( $entry['special'] === self::COMMANDLINE )
            {
                if ( ( $ip === null || $ip === '' || $ip === false ) && $isShell )
                    return $entry;
                continue;
            }
            if ( $ip !== null && $ip !== '' && $ip !== false && self::contains( array( 'packed' => inet_pton( $entry['network'] ), 'prefix' => $entry['prefix'] ), $ip ) )
                return $entry;
        }
        return null;
    }

    /**
     * Every entry parsed, each with whether it matches $ip, and the warnings about the list as a whole.
     *
     * @param string[] $lines
     * @param string|null $ip The address to test (the current request's)
     * @param int|null $now
     * @return array entries, matched (entry or null), allowed, warnings (code, message)
     */
    public static function analyse( array $lines, $ip = null, $now = null )
    {
        $entries = array();
        $warnings = array();
        $expired = 0;
        $matched = self::match( $ip, $lines, $now );
        foreach ( array_values( $lines ) as $index => $line )
        {
            $entry = self::parseEntry( is_scalar( $line ) ? $line : '', $now );
            $entry['index'] = $index;
            $entry['matches'] = $matched !== null && $matched['index'] === $index;
            if ( $entry['error'] !== null )
                $warnings[] = array( 'code' => 'invalid', 'message' => "Entry " . ( $index + 1 ) . " ('{$entry['line']}'): {$entry['error']}" );
            if ( $entry['expired'] && $entry['error'] === null )
                $expired++;
            if ( $entry['valid'] && !$entry['expired'] && $entry['prefix'] === 0 )
                $warnings[] = array( 'code' => 'open', 'message' => "'{$entry['line']}' opens debug to every IPv{$entry['family']} address" );
            $entries[] = $entry;
        }
        if ( $expired )
            $warnings[] = array( 'code' => 'expired', 'message' => $expired . ' ' . ( $expired === 1 ? 'entry has expired and no longer matches' : 'entries have expired and no longer match' ) );
        $active = array_filter( $entries, function ( $e ) { return $e['valid'] && !$e['expired'] && $e['special'] === null; } );
        if ( !$active )
            $warnings[] = array( 'code' => 'empty', 'message' => 'No address can get debug output while DebugByIP is enabled: the list has no active entry' );
        else if ( $ip !== null && $ip !== '' && $matched === null )
            $warnings[] = array( 'code' => 'lockout', 'message' => "This list does not include your address $ip: with DebugByIP enabled you would no longer see debug output" );
        return array( 'entries' => $entries, 'matched' => $matched, 'allowed' => $matched !== null, 'warnings' => $warnings );
    }

    /**
     * Whether a list opens debug to every address of a family (an active /0 entry).
     *
     * @param string[] $lines
     * @param int|null $now
     * @return bool
     */
    public static function isOpen( array $lines, $now = null )
    {
        foreach ( $lines as $line )
        {
            $e = self::parseEntry( is_scalar( $line ) ? $line : '', $now );
            if ( $e['valid'] && !$e['expired'] && $e['prefix'] === 0 )
                return true;
        }
        return false;
    }

    /**
     * The address part of a line, for a caller that only knows plain lines: '' for an expired or unreadable entry.
     *
     * @param string $line
     * @param int|null $now
     * @return string
     */
    public static function addressOf( $line, $now = null )
    {
        $e = self::parseEntry( $line, $now );
        return $e['expired'] ? '' : $e['address'];
    }
}
