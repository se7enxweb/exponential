<?php
/**
 * File containing the expUrlMapRegex class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Converts between the regular expressions of web server rewrite rules (Apache, nginx) and Exponential's wildcard
 * patterns ("/old/*" with "{1}" placeholders in the target), for the expressions a wildcard can say:
 *
 * - anchored with "^"; a "/" or "/?" after it is optional (per-directory rules have no leading slash)
 * - literal text, with "\" escapes of punctuation; an unescaped "." is read as a literal dot, as it is meant in nearly
 *   every rule that has one
 * - the groups (.*), (.+), (.*?) and ([^/]+) / ([^/]*), each a "*"; a bare .* is a "*" too
 * - an optional trailing slash ("/?") before the end, which is dropped (a trailing slash does not matter)
 * - "$" at the end; without it the pattern matches every path that starts with it, which is a "*" at the end
 *
 * Anything else (classes such as \d, [a-z], alternatives, quantifiers, non-capturing groups) gives a reason instead.
 */
class expUrlMapRegex
{
    /**
     * @param string $regex
     * @return array array( 'pattern' => '/old/*', 'groups' => array( group number => star number ), 'notes' => [] ) or
     *               array( 'reason' => why it cannot be a wildcard )
     */
    static function toWildcard( $regex )
    {
        $regex = (string)$regex;
        if ( $regex === '' || $regex[0] !== '^' )
            return array( 'reason' => 'the pattern ' . $regex . ' is not anchored at the start with ^' );
        $body = substr( $regex, 1 );
        $anchored = false;
        if ( substr( $body, -1 ) === '$' && substr( $body, -2 ) !== '\\$' )
        {
            $anchored = true;
            $body = substr( $body, 0, -1 );
        }
        if ( substr( $body, 0, 2 ) === '/?' )
            $body = substr( $body, 2 );
        elseif ( substr( $body, 0, 1 ) === '/' )
            $body = substr( $body, 1 );
        elseif ( substr( $body, 0, 2 ) === '\\/' )
            $body = substr( $body, 2 );
        if ( substr( $body, -2 ) === '/?' )
            $body = substr( $body, 0, -2 );

        $pattern = '/';
        $groups = array();
        $notes = array();
        $group = 0;
        $star = 0;
        $length = strlen( $body );
        $i = 0;
        while ( $i < $length )
        {
            $char = $body[$i];
            if ( $char === '\\' )
            {
                $next = $i + 1 < $length ? $body[$i + 1] : '';
                if ( $next === '' || ctype_alnum( $next ) )
                    return array( 'reason' => 'the pattern ' . $regex . ' uses \\' . $next . ', which a wildcard cannot express' );
                $pattern .= $next;
                $i += 2;
                continue;
            }
            if ( $char === '(' )
            {
                $matched = null;
                foreach ( array( '(.*)', '(.+)', '(.*?)', '(.+?)', '([^/]+)', '([^/]*)' ) as $simple )
                {
                    if ( substr( $body, $i, strlen( $simple ) ) === $simple )
                    {
                        $matched = $simple;
                        break;
                    }
                }
                if ( $matched === null )
                {
                    $end = strpos( $body, ')', $i );
                    $shown = $end === false ? substr( $body, $i ) : substr( $body, $i, $end - $i + 1 );
                    return array( 'reason' => 'the group ' . $shown . ' in ' . $regex . ' is not a simple (.*) group' );
                }
                if ( $matched[1] === '[' )
                    $notes[] = $matched . ' is read as *, which also matches a /';
                $group++;
                $star++;
                $groups[$group] = $star;
                $pattern .= '*';
                $i += strlen( $matched );
                continue;
            }
            if ( $char === '.' && $i + 1 < $length && ( $body[$i + 1] === '*' || $body[$i + 1] === '+' ) )
            {
                $star++;
                $pattern .= '*';
                $i += 2;
                if ( $i < $length && $body[$i] === '?' )
                    $i++;
                continue;
            }
            if ( strpos( '[]{}|+*?$)', $char ) !== false )
                return array( 'reason' => 'the pattern ' . $regex . ' uses ' . $char . ', which a wildcard cannot express' );
            $pattern .= $char;
            $i++;
        }
        if ( !$anchored && substr( $pattern, -1 ) !== '*' )
            $pattern .= '*';
        $pattern = expUrlMapRow::normalizePath( $pattern );
        return array( 'pattern' => $pattern, 'groups' => $groups, 'notes' => array_values( array_unique( $notes ) ) );
    }

    /**
     * Replaces the back-references ($1, \1 or ${1} style as given by $style) of a rewrite target with placeholders.
     *
     * @param string $target
     * @param array $groups group number => star number, from toWildcard()
     * @param string $style "apache" ($N, \$ for a literal dollar; %N and %{...} cannot be expressed) or "nginx"
     *                      ($N; other $variables cannot be expressed, but $scheme://$host and $http_host in front
     *                      are the same host and dropped)
     * @return array array( 'target' => ... ) or array( 'reason' => ... )
     */
    static function toPlaceholders( $target, array $groups, $style )
    {
        $target = (string)$target;
        if ( $style === 'nginx' )
            $target = preg_replace( '#^(?:\$scheme|https?)://\$(?:host|http_host|server_name)(?=/|$)#', '', $target );
        if ( $style === 'apache' && preg_match( '/%(\d|\{)/', $target, $m ) )
            return array( 'reason' => 'the target ' . $target . ' uses %' . $m[1] . ' (a condition back-reference or a server variable)' );
        if ( preg_match( '/(?<!\\\\)\$(?!\d)/', $target ) )
            return array( 'reason' => 'the target ' . $target . ' uses a variable, which a mapping cannot express' );
        $reason = null;
        $result = preg_replace_callback( '/(?<!\\\\)\$(\d+)/', function( $m ) use ( $groups, &$reason, $target )
        {
            $number = (int)$m[1];
            if ( !isset( $groups[$number] ) )
            {
                $reason = 'the target ' . $target . ' uses $' . $number . ', which the pattern does not capture';
                return $m[0];
            }
            return '{' . $groups[$number] . '}';
        }, $target );
        if ( $reason !== null )
            return array( 'reason' => $reason );
        if ( $style === 'apache' )
            $result = str_replace( '\\$', '$', $result );
        return array( 'target' => $result );
    }

    /**
     * The regular expression of a path or wildcard pattern.
     *
     * @param string $path a normalized path, "*" for the wildcard parts
     * @param bool $optionalLeadingSlash "^/?" instead of "^/" (Apache rules that work in .htaccess and server
     *                                   context alike)
     * @param bool $optionalTrailingSlash "/?" before "$" for a path without "*" at the end
     * @return string
     */
    static function fromWildcard( $path, $optionalLeadingSlash, $optionalTrailingSlash )
    {
        $path = (string)$path;
        $body = ltrim( $path, '/' );
        $parts = explode( '*', $body );
        $quoted = array();
        foreach ( $parts as $part )
            $quoted[] = self::quote( $part );
        $regex = '^' . ( $optionalLeadingSlash ? '/?' : '/' ) . implode( '(.*)', $quoted );
        if ( $optionalTrailingSlash && $body !== '' && substr( $body, -1 ) !== '*' )
            $regex .= '/?';
        return $regex . '$';
    }

    /**
     * {N} placeholders as back-references $N.
     *
     * @param string $target
     * @param bool $escapeDollar write a literal $ as \$ (Apache)
     * @return string
     */
    static function fromPlaceholders( $target, $escapeDollar )
    {
        $target = (string)$target;
        if ( $escapeDollar )
            $target = str_replace( '$', '\\$', $target );
        return preg_replace_callback( '/\{(\d+)\}/', function( $m )
        {
            return '$' . $m[1];
        }, $target );
    }

    /**
     * Escapes the characters that mean something in a regular expression (but not "/").
     *
     * @param string $text
     * @return string
     */
    static function quote( $text )
    {
        return preg_replace( '/[.\\\\+*?\[\]^$(){}=!<>|:\-#"\']/', '\\\\$0', (string)$text );
    }
}
