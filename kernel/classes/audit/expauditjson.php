<?php
/**
 * The canonical JSON of audit records (doc/bc/6.0/audit.md, "Canonical JSON").
 *
 * Objects have their keys sorted by their UTF-8 bytes at every depth, lists keep their order, there is no
 * whitespace outside strings, strings are escaped only where JSON requires it (no escaping of "/" or of non-ASCII
 * characters, control characters as \u00xx in lower case except \b \f \n \r \t), numbers are integers, and an
 * empty object stays {} while an empty list is []. A PHP array is a list when its keys are 0, 1, 2 ... in order;
 * an empty object must be given as a stdClass (or as expAuditJson::object()).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditJson
{
    /**
     * The canonical form of a value.
     *
     * @param mixed $value array, stdClass, string, int, bool, null (a float is written as a string)
     * @return string
     */
    public static function encode( $value )
    {
        if ( $value === null )
            return 'null';
        if ( $value === true )
            return 'true';
        if ( $value === false )
            return 'false';
        if ( is_int( $value ) )
            return (string)$value;
        if ( is_float( $value ) )
            return self::string( self::floatText( $value ) );
        if ( is_string( $value ) )
            return self::string( $value );
        if ( $value instanceof stdClass )
            return self::object( get_object_vars( $value ) );
        if ( is_array( $value ) )
        {
            if ( $value === array() )
                return '[]';
            if ( array_keys( $value ) === range( 0, count( $value ) - 1 ) )
            {
                $parts = array();
                foreach ( $value as $item )
                    $parts[] = self::encode( $item );
                return '[' . implode( ',', $parts ) . ']';
            }
            return self::object( $value );
        }
        if ( is_object( $value ) && method_exists( $value, '__toString' ) )
            return self::string( (string)$value );
        if ( is_object( $value ) )
            return self::object( get_object_vars( $value ) );
        return self::string( (string)$value );
    }

    /**
     * @param array $members key => value
     * @return string
     */
    protected static function object( array $members )
    {
        if ( !$members )
            return '{}';
        $sorted = array();
        foreach ( $members as $k => $v )
            $sorted[(string)$k] = $v;
        // byte order of the keys (strcmp), never PHP's numeric-string comparison
        uksort( $sorted, 'strcmp' );
        $parts = array();
        foreach ( $sorted as $k => $v )
            $parts[] = self::string( (string)$k ) . ':' . self::encode( $v );
        return '{' . implode( ',', $parts ) . '}';
    }

    /**
     * A JSON string: escaped only where JSON requires it. Invalid UTF-8 is replaced by U+FFFD first, so the
     * record stays valid JSON (the bytes hashed are the bytes written).
     *
     * @param string $s
     * @return string
     */
    public static function string( $s )
    {
        $s = (string)$s;
        if ( !preg_match( '//u', $s ) )
            $s = self::toValidUtf8( $s );
        $json = json_encode( $s, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_LINE_TERMINATORS );
        if ( $json === false )
            $json = json_encode( self::toValidUtf8( $s ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_LINE_TERMINATORS | JSON_INVALID_UTF8_SUBSTITUTE );
        // json_encode writes \u001f in lower case already, and \b \f \n \r \t in their short forms
        return $json;
    }

    /** @return string The string with every invalid UTF-8 sequence replaced by U+FFFD */
    public static function toValidUtf8( $s )
    {
        if ( function_exists( 'mb_convert_encoding' ) )
        {
            $prev = ini_get( 'mbstring.substitute_character' );
            @ini_set( 'mbstring.substitute_character', '65533' );
            $out = mb_convert_encoding( $s, 'UTF-8', 'UTF-8' );
            @ini_set( 'mbstring.substitute_character', $prev );
            return $out;
        }
        return (string)json_decode( json_encode( $s, JSON_INVALID_UTF8_SUBSTITUTE ) );
    }

    /** @return string A float as text without exponent noise (records never hold floats) */
    protected static function floatText( $f )
    {
        if ( is_nan( $f ) || is_infinite( $f ) )
            return (string)$f;
        $t = rtrim( rtrim( sprintf( '%.6F', $f ), '0' ), '.' );
        return $t === '-0' ? '0' : $t;
    }

    /**
     * Parses one line into the structure encode() takes: objects as stdClass, lists as arrays, so {} and []
     * stay apart.
     *
     * @param string $line
     * @return mixed|null null when the line is not JSON
     */
    public static function decode( $line )
    {
        $value = json_decode( $line, false, 512, JSON_BIGINT_AS_STRING );
        if ( $value === null && trim( $line ) !== 'null' )
            return null;
        return $value;
    }

    /**
     * A stdClass record as an associative array, recursively (objects to arrays; an empty object becomes a
     * stdClass again so encode() keeps it {}).
     *
     * @param mixed $value
     * @return mixed
     */
    public static function toArray( $value )
    {
        if ( $value instanceof stdClass )
        {
            $vars = get_object_vars( $value );
            if ( !$vars )
                return new stdClass();
            $out = array();
            foreach ( $vars as $k => $v )
                $out[$k] = self::toArray( $v );
            return $out;
        }
        if ( is_array( $value ) )
            return array_map( array( __CLASS__, 'toArray' ), $value );
        return $value;
    }

    /** @return string "sha256:" + hex of the canonical form */
    public static function hash( $value, $algorithm = 'sha256' )
    {
        $algorithm = in_array( $algorithm, hash_algos(), true ) ? $algorithm : 'sha256';
        return $algorithm . ':' . hash( $algorithm, self::encode( $value ) );
    }
}
