<?php
/**
 * File containing the expContentFinderInput class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * What the content finder (module contentfinder, doc/features/6.0/content-finder.md) makes of its one search field:
 * the kinds of id or path the text can be, each normalised. Pure: no database, no settings, no request.
 *
 * parse( $text, $type ) gives the queries to run, in order, as array( 'type' => ..., 'value' => ... ):
 *
 *  - object, node, attribute, version: a whole number 1 to 2147483647 (an optional leading '#');
 *  - remote:   a remote id, any printable text up to 100 characters;
 *  - urlalias: a URL alias path without scheme, host, query or fragment, slashes trimmed;
 *  - file:     a path below var/ (array( 'path' => ..., 'prefix' => bool )): a full URL or a var/... path, decoded
 *              once; a trailing '/' or '*' asks for every file below that prefix.
 *
 * With type auto the text decides: a number is tried as each of the four ids; "node:42" (object:, attribute:,
 * version:, remote:, alias:, file:, or the first letter) names the type; a URL or path is a file below var/, a
 * system URL (content/view/full/42, content/download/<object>/<attribute>) or a URL alias; 32 hexadecimal digits are
 * a remote id; anything else is a remote id or a URL alias. A text with control characters, a backslash or a '..'
 * segment is refused (error 'invalid'), as is one longer than MAX_LENGTH ('too_long').
 */
class expContentFinderInput
{
    /** The longest search text taken */
    const MAX_LENGTH = 1024;

    /** The largest id: a signed 32-bit integer, what every id column holds */
    const MAX_ID = 2147483647;

    /** The longest remote id (the column is varchar(100)) */
    const MAX_REMOTE_ID = 100;

    /** What trim() takes off the ends: spaces, tabs and line ends, never a NUL byte (refused as a control character) */
    const SPACE = " \t\n\r\x0B";

    /** The id types */
    const ID_TYPES = array( 'object', 'node', 'attribute', 'version' );

    /**
     * The types of the type selector, auto first.
     *
     * @return string[]
     */
    public static function types()
    {
        return array( 'auto', 'object', 'node', 'attribute', 'version', 'remote', 'urlalias', 'file' );
    }

    /**
     * @param mixed $type
     * @return string a type of types(); auto for anything else
     */
    public static function type( $type )
    {
        return is_string( $type ) && in_array( $type, self::types(), true ) ? $type : 'auto';
    }

    /**
     * @param mixed $text the search text
     * @param mixed $type a type of types()
     * @return array array( 'text' => trimmed text, 'type' => the type, 'queries' => list of array( type, value ),
     *               'error' => '' | 'empty' | 'too_long' | 'invalid' )
     */
    public static function parse( $text, $type = 'auto' )
    {
        $type = self::type( $type );
        $text = is_scalar( $text ) ? trim( (string)$text, self::SPACE ) : '';
        $result = array( 'text' => $text, 'type' => $type, 'queries' => array(), 'error' => '' );
        if ( $text === '' )
        {
            $result['error'] = 'empty';
            return $result;
        }
        if ( strlen( $text ) > self::MAX_LENGTH )
        {
            // the start of it, shown back: whole characters only, and only when it is printable
            $start = function_exists( 'mb_strcut' ) ? mb_strcut( $text, 0, 64, 'UTF-8' ) : substr( $text, 0, 64 );
            $result['text'] = self::isPrintable( $start ) ? $start : '';
            $result['error'] = 'too_long';
            return $result;
        }
        if ( !self::isPrintable( $text ) )
        {
            $result['text'] = '';
            $result['error'] = 'invalid';
            return $result;
        }

        if ( $type === 'auto' && preg_match( '/^(object|node|attribute|version|remote|alias|urlalias|file|[onavrf]):\s*(.+)$/i', $text, $m ) )
        {
            $named = strtolower( $m[1] );
            $map = array( 'o' => 'object', 'n' => 'node', 'a' => 'attribute', 'v' => 'version', 'r' => 'remote', 'f' => 'file', 'alias' => 'urlalias' );
            $queries = self::queriesFor( isset( $map[$named] ) ? $map[$named] : $named, trim( $m[2], self::SPACE ) );
        }
        else if ( $type === 'auto' )
            $queries = self::guess( $text );
        else
            $queries = self::queriesFor( $type, $text );

        if ( !$queries )
            $result['error'] = 'invalid';
        $result['queries'] = $queries;
        return $result;
    }

    /**
     * The queries of a text with type auto.
     *
     * @param string $text
     * @return array
     */
    protected static function guess( $text )
    {
        $id = self::id( $text );
        if ( $id !== false )
        {
            $queries = array();
            foreach ( self::ID_TYPES as $idType )
                $queries[] = array( 'type' => $idType, 'value' => $id );
            return $queries;
        }

        $isURL = (bool)preg_match( '#^[a-z][a-z0-9+.\-]*://#i', $text );
        if ( $isURL || strpos( $text, '/' ) !== false )
        {
            $path = self::urlPath( $text );
            if ( $path === false )
                return array();
            $system = self::systemURL( $path );
            if ( $system )
                return $system;
            if ( preg_match( '#^(var/|.*/storage/)#', ltrim( $path, '/' ) ) )
                return self::queriesFor( 'file', $text );
            return self::queriesFor( 'urlalias', $text );
        }

        if ( preg_match( '/^[0-9a-f]{32}$/i', $text ) )
            return self::queriesFor( 'remote', $text );

        return array_merge( self::queriesFor( 'remote', $text ), self::queriesFor( 'urlalias', $text ) );
    }

    /**
     * The query of one type, or none when the text is not one.
     *
     * @param string $type
     * @param string $text
     * @return array
     */
    protected static function queriesFor( $type, $text )
    {
        switch ( $type )
        {
            case 'object':
            case 'node':
            case 'attribute':
            case 'version':
                $value = self::id( $text );
                break;
            case 'remote':
                $value = self::remoteID( $text );
                break;
            case 'urlalias':
                $value = self::urlAlias( $text );
                break;
            case 'file':
                $value = self::filePath( $text );
                break;
            default:
                $value = false;
        }
        return $value === false ? array() : array( array( 'type' => $type, 'value' => $value ) );
    }

    /**
     * Whether the text has no control characters (C0, DEL, C1) and is valid UTF-8.
     *
     * @param string $text
     * @return bool
     */
    public static function isPrintable( $text )
    {
        $text = (string)$text;
        if ( preg_match( '//u', $text ) !== 1 )
            return false;
        return !preg_match( '/[\x00-\x1F\x7F]|\xC2[\x80-\x9F]/', $text );
    }

    /**
     * @param mixed $text
     * @return int|false a whole number from 1 to MAX_ID
     */
    public static function id( $text )
    {
        if ( !is_scalar( $text ) || !preg_match( '/^\s*#?([0-9]{1,10})\s*$/', (string)$text, $m ) )
            return false;
        $digits = ltrim( $m[1], '0' );
        if ( $digits === '' || strlen( $digits ) > 10 || ( strlen( $digits ) === 10 && strcmp( $digits, (string)self::MAX_ID ) > 0 ) )
            return false;
        return (int)$digits;
    }

    /**
     * @param mixed $text
     * @return string|false
     */
    public static function remoteID( $text )
    {
        $text = is_scalar( $text ) ? trim( (string)$text, self::SPACE ) : '';
        if ( $text === '' || strlen( $text ) > self::MAX_REMOTE_ID || !self::isPrintable( $text ) )
            return false;
        return $text;
    }

    /**
     * The path of a URL or path: scheme and host, query and fragment removed, percent-decoded once, repeated
     * slashes made one. False for one with control characters, a backslash or a '.' or '..' segment.
     *
     * @param mixed $text
     * @return string|false the path, with its leading and trailing slashes as given (one of each at most)
     */
    public static function urlPath( $text )
    {
        $text = is_scalar( $text ) ? trim( (string)$text, self::SPACE ) : '';
        if ( $text === '' )
            return false;
        if ( preg_match( '#^[a-z][a-z0-9+.\-]*://#i', $text ) )
        {
            if ( !preg_match( '#^(?:https?|ftp)://#i', $text ) )
                return false;
            $path = parse_url( $text, PHP_URL_PATH );
            if ( $path === false )
                return false;
            $text = $path === null ? '/' : (string)$path;
        }
        else
            $text = (string)preg_replace( '/[?#].*$/s', '', $text );
        $text = rawurldecode( $text );
        if ( !self::isPrintable( $text ) || strpos( $text, '\\' ) !== false )
            return false;
        $text = (string)preg_replace( '#/{2,}#', '/', $text );
        foreach ( explode( '/', $text ) as $segment )
        {
            if ( $segment === '..' || $segment === '.' )
                return false;
        }
        return $text;
    }

    /**
     * @param mixed $text
     * @return string|false the URL alias path without leading or trailing slashes; false for an empty one
     */
    public static function urlAlias( $text )
    {
        $path = self::urlPath( $text );
        if ( $path === false )
            return false;
        $path = trim( $path, '/' );
        if ( $path === '' || strlen( $path ) > self::MAX_LENGTH )
            return false;
        return $path;
    }

    /**
     * @param mixed $text
     * @return array|false array( 'path' => 'var/...', 'prefix' => bool ); a trailing '*' or '/' asks for the prefix
     */
    public static function filePath( $text )
    {
        $text = is_scalar( $text ) ? trim( (string)$text, self::SPACE ) : '';
        $prefix = false;
        if ( substr( $text, -1 ) === '*' )
        {
            $prefix = true;
            $text = rtrim( substr( $text, 0, -1 ), self::SPACE );
            if ( strpos( $text, '*' ) !== false )
                return false;
        }
        $path = self::urlPath( $text );
        if ( $path === false )
            return false;
        if ( substr( $path, -1 ) === '/' )
            $prefix = true;
        $path = ltrim( $path, '/' );
        if ( strpos( $path, '*' ) !== false )
            return false;
        // only what the installation keeps below var/; the bare var/ itself would list everything
        if ( !preg_match( '#^var/[^/]#', $path ) || strlen( $path ) > self::MAX_LENGTH )
            return false;
        if ( $prefix && strlen( rtrim( $path, '/' ) ) < strlen( 'var/x/storage' ) )
            return false;
        return array( 'path' => $path, 'prefix' => $prefix );
    }

    /**
     * The ids in a system URL: content/view/<view mode>/<node id> and content/download/<object id>/<attribute id>,
     * with or without a siteaccess in front.
     *
     * @param string $path from urlPath()
     * @return array the queries; none for any other path
     */
    public static function systemURL( $path )
    {
        $path = trim( (string)$path, '/' );
        if ( preg_match( '#(?:^|/)content/view/[a-z0-9_]+/([0-9]{1,10})(?:/|$)#i', $path, $m ) )
        {
            $id = self::id( $m[1] );
            return $id === false ? array() : array( array( 'type' => 'node', 'value' => $id ) );
        }
        if ( preg_match( '#(?:^|/)content/download/([0-9]{1,10})/([0-9]{1,10})(?:/|$)#i', $path, $m ) )
        {
            $id = self::id( $m[2] );
            return $id === false ? array() : array( array( 'type' => 'attribute', 'value' => $id ) );
        }
        return array();
    }

    /**
     * A LIKE pattern matching every text that starts with $prefix: % and _ and the escape character itself escaped
     * with '!'. Use with " ESCAPE '!'", which MySQL, PostgreSQL, SQLite and Oracle all understand. The result still
     * has to go through the database's escapeString().
     *
     * @param string $prefix
     * @return string
     */
    public static function likePrefix( $prefix )
    {
        return strtr( (string)$prefix, array( '!' => '!!', '%' => '!%', '_' => '!_' ) ) . '%';
    }
}

?>
