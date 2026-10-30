<?php
/**
 * File containing the expWebAuthnCbor class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The part of CBOR (RFC 8949) that WebAuthn needs: decoding the attestation object of a new passkey and the COSE
 * public key inside its authenticator data. Integers (also 64-bit), byte and text strings, arrays, maps, tags (their
 * value is returned), true, false, null, undefined and floats. Indefinite lengths are refused: authenticators send
 * the canonical (CTAP2) encoding, which never uses them.
 *
 * A byte string and a text string both come back as a PHP string; the caller knows from the structure which one it
 * expects. Map keys are integers or strings, as PHP array keys. Nesting deeper than MAX_DEPTH and lengths beyond the
 * input are refused with an InvalidArgumentException, so a crafted input cannot exhaust memory or the stack.
 */
class expWebAuthnCbor
{
    /** Nested arrays and maps deeper than this are refused */
    const MAX_DEPTH = 16;

    /**
     * Decodes one complete item; trailing bytes are an error.
     *
     * @param string $bytes
     * @return mixed
     * @throws InvalidArgumentException
     */
    public static function decode( $bytes )
    {
        $offset = 0;
        $value = self::decodeItem( (string)$bytes, $offset, 0 );
        if ( $offset !== strlen( $bytes ) )
            throw new InvalidArgumentException( 'CBOR: unexpected bytes after the item' );
        return $value;
    }

    /**
     * Decodes the item that starts at $offset and moves $offset behind it (the COSE key in authenticator data is
     * followed by extensions, so its length is only known by decoding it).
     *
     * @param string $bytes
     * @param int $offset
     * @return mixed
     * @throws InvalidArgumentException
     */
    public static function decodeFirst( $bytes, &$offset )
    {
        return self::decodeItem( (string)$bytes, $offset, 0 );
    }

    /**
     * @param string $bytes
     * @param int $offset
     * @param int $depth
     * @return mixed
     */
    protected static function decodeItem( $bytes, &$offset, $depth )
    {
        if ( $depth > self::MAX_DEPTH )
            throw new InvalidArgumentException( 'CBOR: nested too deeply' );
        $initial = ord( self::take( $bytes, $offset, 1 ) );
        $major = $initial >> 5;
        $info = $initial & 0x1f;

        if ( $major === 7 )
            return self::decodeSimple( $bytes, $offset, $info );

        $length = self::argument( $bytes, $offset, $info );
        switch ( $major )
        {
            case 0:
                return $length;
            case 1:
                return -1 - $length;
            case 2:
            case 3:
                return self::take( $bytes, $offset, $length );
            case 4:
                self::plausibleCount( $bytes, $offset, $length );
                $list = array();
                for ( $i = 0; $i < $length; $i++ )
                    $list[] = self::decodeItem( $bytes, $offset, $depth + 1 );
                return $list;
            case 5:
                self::plausibleCount( $bytes, $offset, $length * 2 );
                $map = array();
                for ( $i = 0; $i < $length; $i++ )
                {
                    $key = self::decodeItem( $bytes, $offset, $depth + 1 );
                    if ( !is_int( $key ) && !is_string( $key ) )
                        throw new InvalidArgumentException( 'CBOR: a map key must be an integer or a string' );
                    if ( array_key_exists( $key, $map ) )
                        throw new InvalidArgumentException( 'CBOR: a map key appears twice' );
                    $map[$key] = self::decodeItem( $bytes, $offset, $depth + 1 );
                }
                return $map;
            case 6:
                // a tag: its value is what counts here
                return self::decodeItem( $bytes, $offset, $depth + 1 );
        }
        throw new InvalidArgumentException( 'CBOR: unknown major type' );
    }

    /**
     * The argument of an item: the value itself (0..23) or the 1, 2, 4 or 8 bytes after it.
     */
    protected static function argument( $bytes, &$offset, $info )
    {
        if ( $info < 24 )
            return $info;
        switch ( $info )
        {
            case 24:
                return ord( self::take( $bytes, $offset, 1 ) );
            case 25:
                return unpack( 'n', self::take( $bytes, $offset, 2 ) )[1];
            case 26:
                return unpack( 'N', self::take( $bytes, $offset, 4 ) )[1];
            case 27:
                $parts = unpack( 'N2', self::take( $bytes, $offset, 8 ) );
                if ( $parts[1] > 0x7fffffff )
                    throw new InvalidArgumentException( 'CBOR: integer out of range' );
                return ( $parts[1] << 32 ) | $parts[2];
        }
        throw new InvalidArgumentException( 'CBOR: indefinite or reserved length' );
    }

    /**
     * Simple values and floats (major type 7).
     */
    protected static function decodeSimple( $bytes, &$offset, $info )
    {
        switch ( $info )
        {
            case 20:
                return false;
            case 21:
                return true;
            case 22:
            case 23:
                return null;
            case 24:
                self::take( $bytes, $offset, 1 );
                return null;
            case 25:
                return self::halfFloat( unpack( 'n', self::take( $bytes, $offset, 2 ) )[1] );
            case 26:
                return unpack( 'G', self::take( $bytes, $offset, 4 ) )[1];
            case 27:
                return unpack( 'E', self::take( $bytes, $offset, 8 ) )[1];
        }
        if ( $info < 20 )
            return null;
        throw new InvalidArgumentException( 'CBOR: unsupported simple value' );
    }

    /**
     * IEEE 754 half precision to a PHP float.
     */
    protected static function halfFloat( $half )
    {
        $exp = ( $half >> 10 ) & 0x1f;
        $mant = $half & 0x3ff;
        if ( $exp === 0 )
            $value = $mant * pow( 2, -24 );
        elseif ( $exp !== 31 )
            $value = ( $mant + 1024 ) * pow( 2, $exp - 25 );
        else
            $value = $mant === 0 ? INF : NAN;
        return ( $half & 0x8000 ) ? -$value : $value;
    }

    /**
     * A count of items cannot be larger than the bytes left (each item takes at least one).
     */
    protected static function plausibleCount( $bytes, $offset, $count )
    {
        if ( $count > strlen( $bytes ) - $offset )
            throw new InvalidArgumentException( 'CBOR: more items announced than bytes left' );
    }

    /**
     * @return string the next $length bytes
     */
    protected static function take( $bytes, &$offset, $length )
    {
        if ( $length < 0 || $offset + $length > strlen( $bytes ) )
            throw new InvalidArgumentException( 'CBOR: unexpected end of data' );
        $part = (string)substr( $bytes, $offset, $length );
        $offset += $length;
        return $part;
    }
}
