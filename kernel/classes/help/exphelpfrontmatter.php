<?php
/**
 * File containing the expHelpFrontMatter class.
 *
 * The front matter of a help page (doc/eng-us/features/6.0/help-system.md, "Front matter"): a block at the very top of a
 * Markdown file between two lines of "---", one "key: value" per line. Only this flat subset of YAML is read, which is
 * all a help page needs and leaves nothing to execute:
 *
 *   ---
 *   title: Editing content
 *   audience: editor, admin          (or [editor, admin], or "- editor" lines below "audience:")
 *   category: editing
 *   order: 30
 *   summary: One sentence for the cards and the search results.
 *   keywords: draft, publish
 *   featured: true
 *   context: content/edit, content/view
 *   ---
 *
 * Keys are lower-cased, values may be quoted, a "#" starts a comment only after a space. A block that does not close
 * within MAX_LINES lines is no front matter: the text is then returned unchanged.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpFrontMatter
{
    /** The longest front matter block read, in lines */
    const MAX_LINES = 60;

    /** The keys whose value is always a list */
    const LIST_KEYS = array( 'audience', 'keywords', 'tags', 'context' );

    /**
     * Splits a text into its front matter and the rest.
     *
     * @param string $text the whole file
     * @return array array( array $meta, string $body ); $meta is empty and $body is $text when there is no front matter
     */
    public static function split( $text )
    {
        $text = (string)$text;
        if ( strncmp( $text, "\xEF\xBB\xBF", 3 ) === 0 )
            $text = substr( $text, 3 );
        if ( !preg_match( '/^---[ \t]*\r?\n/', $text, $open ) )
            return array( array(), $text );

        $offset = strlen( $open[0] );
        $lines = array();
        for ( $i = 0; $i < self::MAX_LINES; ++$i )
        {
            $end = strpos( $text, "\n", $offset );
            $line = $end === false ? substr( $text, $offset ) : substr( $text, $offset, $end - $offset );
            $line = rtrim( $line, "\r" );
            if ( preg_match( '/^(---|\.\.\.)[ \t]*$/', $line ) )
            {
                $body = $end === false ? '' : substr( $text, $end + 1 );
                return array( self::parse( $lines ), $body );
            }
            if ( $end === false )
                break;
            $lines[] = $line;
            $offset = $end + 1;
        }
        return array( array(), $text );
    }

    /**
     * Reads the lines of a front matter block.
     *
     * @param string[] $lines
     * @return array key => string|bool|int|string[]
     */
    public static function parse( array $lines )
    {
        $meta = array();
        $listKey = null;
        foreach ( $lines as $line )
        {
            if ( trim( $line ) === '' || preg_match( '/^\s*#/', $line ) )
                continue;
            if ( $listKey !== null && preg_match( '/^\s*-\s+(.*)$/', $line, $m ) )
            {
                $item = self::scalar( self::stripComment( $m[1] ) );
                if ( $item !== '' )
                    $meta[$listKey][] = $item;
                continue;
            }
            $listKey = null;
            if ( !preg_match( '/^([A-Za-z][A-Za-z0-9_-]{0,40})\s*:\s*(.*)$/', $line, $m ) )
                continue;
            $key = strtolower( str_replace( '-', '_', $m[1] ) );
            $raw = self::stripComment( $m[2] );
            if ( $raw === '' )
            {
                // "key:" alone: a list in "- item" lines may follow
                $meta[$key] = array();
                $listKey = $key;
                continue;
            }
            if ( in_array( $key, self::LIST_KEYS, true ) || ( $raw[0] === '[' && substr( $raw, -1 ) === ']' ) )
                $meta[$key] = self::listValue( $raw );
            else
                $meta[$key] = self::typed( self::scalar( $raw ) );
        }
        foreach ( self::LIST_KEYS as $key )
            if ( isset( $meta[$key] ) && !is_array( $meta[$key] ) )
                $meta[$key] = self::listValue( (string)$meta[$key] );
        return $meta;
    }

    /**
     * A list value: "a, b", "[a, b]" or one item.
     *
     * @param string $raw
     * @return string[]
     */
    public static function listValue( $raw )
    {
        $raw = trim( (string)$raw );
        if ( $raw !== '' && $raw[0] === '[' && substr( $raw, -1 ) === ']' )
            $raw = substr( $raw, 1, -1 );
        $items = array();
        foreach ( explode( ',', $raw ) as $item )
        {
            $item = self::scalar( $item );
            if ( $item !== '' )
                $items[] = $item;
        }
        return $items;
    }

    /**
     * Removes a trailing " # comment" outside quotes.
     *
     * @param string $raw
     * @return string
     */
    protected static function stripComment( $raw )
    {
        $raw = trim( $raw );
        if ( $raw === '' || $raw[0] === '"' || $raw[0] === "'" )
            return $raw;
        $pos = strpos( $raw, ' #' );
        return $pos === false ? $raw : rtrim( substr( $raw, 0, $pos ) );
    }

    /**
     * A scalar without its quotes, unescaped as YAML does: in double quotes \" \\ \/ \t \n \r \0 and \uXXXX, in
     * single quotes '' (an unknown escape is kept as written).
     *
     * @param string $raw
     * @return string
     */
    protected static function scalar( $raw )
    {
        $raw = trim( (string)$raw );
        $len = strlen( $raw );
        if ( $len >= 2 && $raw[0] === "'" && $raw[$len - 1] === "'" )
            return str_replace( "''", "'", substr( $raw, 1, -1 ) );
        if ( $len >= 2 && $raw[0] === '"' && $raw[$len - 1] === '"' )
            return self::unescapeDoubleQuoted( substr( $raw, 1, -1 ) );
        return $raw;
    }

    /**
     * The escapes of a double-quoted YAML scalar.
     *
     * @param string $inner the text between the quotes
     * @return string
     */
    protected static function unescapeDoubleQuoted( $inner )
    {
        $map = array( '"' => '"', '\\' => '\\', '/' => '/', 't' => "\t", 'n' => "\n", 'r' => "\r", '0' => "\0", ' ' => ' ' );
        return preg_replace_callback( '/\\\\(u[0-9A-Fa-f]{4}|.)/su', function ( $m ) use ( $map )
        {
            if ( $m[1][0] === 'u' && strlen( $m[1] ) === 5 )
                return mb_convert_encoding( pack( 'n', hexdec( substr( $m[1], 1 ) ) ), 'UTF-8', 'UTF-16BE' );
            return isset( $map[$m[1]] ) ? $map[$m[1]] : $m[0];
        }, $inner );
    }

    /**
     * true/false and integers as such, everything else as the string.
     *
     * @param string $value
     * @return string|bool|int
     */
    protected static function typed( $value )
    {
        $lower = strtolower( $value );
        if ( $lower === 'true' || $lower === 'yes' )
            return true;
        if ( $lower === 'false' || $lower === 'no' )
            return false;
        if ( preg_match( '/^-?\d{1,9}$/', $value ) )
            return (int)$value;
        return $value;
    }
}
