<?php
/**
 * File containing the expMigrationYamlWriter class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Writes the YAML of a generated migration without a YAML library: maps and lists in block style, short lists of
 * scalars and small maps of scalars in flow style ({ a: 1 }, [ x, y ]), texts plain where YAML reads them back as the
 * same text and double-quoted otherwise (the escapes of JSON, which YAML reads the same), texts of several lines as
 * literal blocks (|). What it writes is read back by expMigrationYamlParser as the same value. Pure.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationYamlWriter
{
    /** The width up to which a list or map of scalars is written in one line. */
    const FLOW_WIDTH = 100;

    /** Plain words YAML reads as something other than a text. */
    const RESERVED = array( 'true', 'false', 'yes', 'no', 'on', 'off', 'null', 'y', 'n', '~' );

    /**
     * A document: the comment lines, then the value of the top. Pure.
     *
     * @param array $value the map of the document (steps, down ...)
     * @param string[] $comments lines written as "# <line>" above it (the header of a migration)
     * @return string
     */
    public static function document( array $value, array $comments = array() )
    {
        $out = '';
        foreach ( $comments as $line )
            $out .= rtrim( '# ' . str_replace( array( "\r", "\n" ), ' ', (string)$line ) ) . "\n";
        return $out . self::block( $value, 0 );
    }

    /**
     * A map or a list in block style, at an indentation. Pure.
     *
     * @param array $value
     * @param int $indent
     * @return string
     */
    public static function block( array $value, $indent = 0 )
    {
        $pad = str_repeat( ' ', $indent );
        if ( !$value )
            return $pad . "[]\n";
        $out = '';
        $list = self::isList( $value );
        foreach ( $value as $key => $item )
        {
            $lead = $list ? $pad . '- ' : $pad . self::key( $key ) . ':';
            // the maps of a list at the top (the steps) always in block style, one key per line
            $top = $list && $indent <= 4 && is_array( $item ) && !self::isList( $item );
            if ( is_array( $item ) && $item && ( $top || !self::flowable( $item, strlen( $lead ) ) ) )
            {
                if ( $list && !self::isList( $item ) )
                {
                    // the first key of a map in a list goes on the line of its dash
                    $inner = self::block( $item, $indent + 2 );
                    $out .= $lead . substr( $inner, $indent + 2 );
                }
                else
                    $out .= rtrim( $lead ) . "\n" . self::block( $item, $indent + ( $list ? 2 : 4 ) );
                continue;
            }
            if ( is_string( $item ) && strpos( $item, "\n" ) !== false && self::literalSafe( $item ) )
            {
                $out .= ( $list ? $lead : $lead . ' ' ) . ( substr( $item, -1 ) === "\n" ? '|' : '|-' ) . "\n";
                $inner = str_repeat( ' ', $indent + ( $list ? 2 : 4 ) );
                foreach ( explode( "\n", substr( $item, -1 ) === "\n" ? substr( $item, 0, -1 ) : $item ) as $line )
                    $out .= ( $line === '' ? '' : $inner . $line ) . "\n";
                continue;
            }
            $out .= ( $list ? $lead : $lead . ' ' ) . self::inline( $item ) . "\n";
        }
        return $out;
    }

    /**
     * A value in one line: a scalar, or a list or map in flow style. Pure.
     *
     * @param mixed $value
     * @return string
     */
    public static function inline( $value )
    {
        if ( $value === null )
            return 'null';
        if ( is_bool( $value ) )
            return $value ? 'true' : 'false';
        if ( is_int( $value ) )
            return (string)$value;
        if ( is_float( $value ) )
        {
            if ( is_nan( $value ) || is_infinite( $value ) )
                return self::quote( (string)$value );
            $text = var_export( $value, true );
            return strpbrk( $text, '.eE' ) === false ? $text . '.0' : $text;
        }
        if ( is_array( $value ) )
        {
            if ( !$value )
                return '[]';
            $parts = array();
            if ( self::isList( $value ) )
            {
                foreach ( $value as $item )
                    $parts[] = self::inline( $item );
                return '[ ' . implode( ', ', $parts ) . ' ]';
            }
            foreach ( $value as $key => $item )
                $parts[] = self::key( $key ) . ': ' . self::inline( $item );
            return '{ ' . implode( ', ', $parts ) . ' }';
        }
        return self::scalar( (string)$value );
    }

    /**
     * A text, plain when YAML reads it back as the same text, else double-quoted. Pure.
     *
     * @param string $text
     * @return string
     */
    public static function scalar( $text )
    {
        $text = (string)$text;
        if ( $text !== '' && preg_match( '/^[A-Za-z_\/][A-Za-z0-9_\/.\-]*$/', $text ) && !in_array( strtolower( $text ), self::RESERVED, true ) )
            return $text;
        return self::quote( $text );
    }

    /**
     * A key of a map. Pure.
     *
     * @param int|string $key
     * @return string
     */
    public static function key( $key )
    {
        if ( is_int( $key ) )
            return (string)$key;
        return self::scalar( (string)$key );
    }

    /**
     * A text in double quotes, with the escapes of JSON (which YAML reads the same). Pure.
     *
     * @param string $text
     * @return string
     */
    public static function quote( $text )
    {
        $json = json_encode( (string)$text, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE );
        return $json === false ? '""' : $json;
    }

    /**
     * Whether an array is a list (keys 0..n-1). Pure.
     *
     * @param array $value
     * @return bool
     */
    public static function isList( array $value )
    {
        return $value === array() || array_keys( $value ) === range( 0, count( $value ) - 1 );
    }

    /**
     * Whether an array goes in one line: scalars only (or small maps of scalars in a list) and short enough. Pure.
     *
     * @param array $value
     * @param int $lead the width already used on the line
     * @return bool
     */
    protected static function flowable( array $value, $lead )
    {
        foreach ( $value as $item )
        {
            if ( is_string( $item ) && strpos( $item, "\n" ) !== false )
                return false;
            if ( is_array( $item ) )
                foreach ( $item as $inner )
                    if ( is_array( $inner ) || ( is_string( $inner ) && strpos( $inner, "\n" ) !== false ) )
                        return false;
        }
        return $lead + strlen( self::inline( $value ) ) <= self::FLOW_WIDTH;
    }

    /**
     * Whether a text of several lines can be a literal block: no tabs, no trailing spaces on a line, no leading space
     * on the first line, and no carriage returns. Pure.
     *
     * @param string $text
     * @return bool
     */
    protected static function literalSafe( $text )
    {
        if ( strpbrk( $text, "\r\t" ) !== false || $text[0] === ' ' || $text[0] === "\n" )
            return false;
        if ( substr( $text, -2 ) === "\n\n" )
            return false;
        foreach ( explode( "\n", $text ) as $line )
            if ( $line !== rtrim( $line ) )
                return false;
        return preg_match( '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $text ) !== 1;
    }
}
