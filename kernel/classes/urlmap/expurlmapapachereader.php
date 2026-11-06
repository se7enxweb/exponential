<?php
/**
 * File containing the expUrlMapApacheReader class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Reads the redirects of an Apache configuration or .htaccess file:
 *
 * - Redirect [status] /old /new, RedirectPermanent, RedirectTemp: a redirect of that one path
 * - RedirectMatch [status] ^/old/(.*)$ /new/$1: a wildcard (forward) when the expression is simple (expUrlMapRegex)
 * - RewriteRule ^/?old$ /new [R=301,L]: a redirect or wildcard (forward); without R an internal rewrite, which is an
 *   alias or a wildcard (direct); RewriteCond %{HTTP_HOST} ^host$ and %{QUERY_STRING} ^query$ before it give the
 *   old address's host and query, any other condition makes the rule not importable
 * - ServerName inside <VirtualHost> gives the host of the rules in that block; RewriteBase the base of relative targets
 * - "#exp:<kind> <old> <new>" comments (written by expUrlMapApacheWriter for the mappings a rule cannot express)
 *
 * A temporary redirect (302, 303, 307) is read as a redirect with a note: Exponential redirects with 301. Everything
 * a mapping cannot say (other flags and conditions, regular expressions beyond the simple ones, status 410) is a row
 * that is not importable, with the reason and the directive in the note. Other directives are ignored.
 */
class expUrlMapApacheReader extends expUrlMapReader
{
    /**
     * @param string $data
     * @return expUrlMapRow[]
     */
    function readString( $data )
    {
        $lines = explode( "\n", self::utf8Text( $data ) );
        $rows = array();
        $conditions = array();
        $virtualHost = false;
        $serverName = '';
        $base = '/';
        $count = count( $lines );
        for ( $i = 0; $i < $count; $i++ )
        {
            $number = $i + 1;
            $line = rtrim( $lines[$i] );
            while ( substr( $line, -1 ) === '\\' && $i + 1 < $count )
                $line = substr( $line, 0, -1 ) . rtrim( $lines[++$i] );
            $line = trim( $line );
            if ( $line === '' )
                continue;
            if ( $line[0] === '#' )
            {
                if ( preg_match( '/^#\s*exp:([a-z\-]+)\s+(.*)$/', $line, $m ) )
                {
                    $args = self::tokenize( $m[2] );
                    $rows[] = new expUrlMapRow( isset( $args[0] ) ? $args[0] : '', isset( $args[1] ) ? $args[1] : '', $m[1], '', '', '', $number );
                }
                continue;
            }
            if ( $line[0] === '<' )
            {
                if ( preg_match( '#^<VirtualHost\b#i', $line ) )
                {
                    $virtualHost = true;
                    $serverName = '';
                }
                elseif ( preg_match( '#^</VirtualHost\b#i', $line ) )
                {
                    $virtualHost = false;
                    $serverName = '';
                }
                continue;
            }
            $args = self::tokenize( $line );
            $directive = strtolower( $args[0] );
            switch ( $directive )
            {
                case 'servername':
                    if ( $virtualHost && isset( $args[1] ) )
                        $serverName = strtolower( preg_replace( '#^[a-z]+://#i', '', $args[1] ) );
                    break;
                case 'rewritebase':
                    if ( isset( $args[1] ) )
                        $base = rtrim( $args[1], '/' ) . '/';
                    break;
                case 'rewritecond':
                    $conditions[] = $args;
                    break;
                case 'rewriterule':
                    $rows[] = $this->rewriteRule( $args, $conditions, $serverName, $base, $line, $number );
                    $conditions = array();
                    break;
                case 'redirect':
                case 'redirectpermanent':
                case 'redirecttemp':
                case 'redirectmatch':
                    $rows[] = $this->redirect( $directive, $args, $serverName, $line, $number );
                    break;
            }
        }
        return $rows;
    }

    /**
     * Splits a directive into its arguments: by white space, with "..." and '...' quoting (a backslash before the
     * quote character escapes it; other backslashes stay).
     *
     * @param string $line
     * @return string[]
     */
    static function tokenize( $line )
    {
        $args = array();
        $length = strlen( $line );
        $i = 0;
        while ( $i < $length )
        {
            while ( $i < $length && ctype_space( $line[$i] ) )
                $i++;
            if ( $i >= $length )
                break;
            $quote = $line[$i];
            if ( $quote === '"' || $quote === "'" )
            {
                $value = '';
                $i++;
                while ( $i < $length && $line[$i] !== $quote )
                {
                    if ( $line[$i] === '\\' && $i + 1 < $length && $line[$i + 1] === $quote )
                        $i++;
                    $value .= $line[$i];
                    $i++;
                }
                $i++;
                $args[] = $value;
                continue;
            }
            $end = $i;
            while ( $end < $length && !ctype_space( $line[$end] ) )
                $end++;
            $args[] = substr( $line, $i, $end - $i );
            $i = $end;
        }
        return $args;
    }

    /**
     * @param string $directive
     * @param string[] $args
     * @param string $host
     * @param string $line
     * @param int $number
     * @return expUrlMapRow
     */
    private function redirect( $directive, array $args, $host, $line, $number )
    {
        array_shift( $args );
        $status = 302;
        if ( $directive === 'redirectpermanent' )
            $status = 301;
        elseif ( $directive !== 'redirecttemp' && $args && preg_match( '/^(permanent|temp|seeother|gone|\d{3})$/i', $args[0] ) )
        {
            $word = strtolower( array_shift( $args ) );
            $named = array( 'permanent' => 301, 'temp' => 302, 'seeother' => 303, 'gone' => 410 );
            $status = isset( $named[$word] ) ? $named[$word] : (int)$word;
        }
        $old = isset( $args[0] ) ? $args[0] : '';
        $new = isset( $args[1] ) ? $args[1] : '';
        if ( $status < 300 || $status > 399 )
            return expUrlMapRow::notImportable( $old, $new, 'status ' . $status . ' is not a redirect', $number, $line );
        if ( $old === '' || $new === '' )
            return expUrlMapRow::notImportable( $old, $new, 'the directive has no path or no target', $number, $line );
        $notes = array();
        if ( $directive === 'redirectmatch' )
        {
            $wildcard = expUrlMapRegex::toWildcard( $old );
            if ( isset( $wildcard['reason'] ) )
                return expUrlMapRow::notImportable( $old, $new, $wildcard['reason'], $number, $line );
            $target = expUrlMapRegex::toPlaceholders( $new, $wildcard['groups'], 'apache' );
            if ( isset( $target['reason'] ) )
                return expUrlMapRow::notImportable( $old, $new, $target['reason'], $number, $line );
            $path = $wildcard['pattern'];
            $new = $target['target'];
            $notes = $wildcard['notes'];
        }
        else
            $path = $old;
        if ( strpos( $path, '*' ) === false && $directive !== 'redirectmatch' )
            $kind = expUrlMapRow::KIND_REDIRECT;
        else
            $kind = strpos( $path, '*' ) === false ? expUrlMapRow::KIND_REDIRECT : expUrlMapRow::KIND_WILDCARD_FORWARD;
        if ( $status !== 301 && $status !== 308 )
            $notes[] = 'the source answered with ' . $status . '; Exponential redirects with 301';
        return new expUrlMapRow( ( $host !== '' ? '//' . $host : '' ) . $path, $new, $kind, '', '', implode( '; ', $notes ), $number );
    }

    /**
     * @param string[] $args
     * @param array $conditions the RewriteCond lines before it, as argument lists
     * @param string $host
     * @param string $base
     * @param string $line
     * @param int $number
     * @return expUrlMapRow
     */
    private function rewriteRule( array $args, array $conditions, $host, $base, $line, $number )
    {
        $pattern = isset( $args[1] ) ? $args[1] : '';
        $target = isset( $args[2] ) ? $args[2] : '';
        $flags = array();
        if ( isset( $args[3] ) )
        {
            foreach ( explode( ',', trim( $args[3], '[]' ) ) as $flag )
            {
                $flag = trim( $flag );
                if ( $flag === '' )
                    continue;
                $parts = explode( '=', $flag, 2 );
                $flags[strtolower( $parts[0] )] = isset( $parts[1] ) ? $parts[1] : '';
            }
        }
        if ( $pattern === '' || $target === '' )
            return expUrlMapRow::notImportable( $pattern, $target, 'the rule has no pattern or no target', $number, $line );
        if ( $pattern[0] === '!' )
            return expUrlMapRow::notImportable( $pattern, $target, 'a negated pattern cannot be expressed', $number, $line );
        if ( $target === '-' )
            return expUrlMapRow::notImportable( $pattern, $target, 'the rule does not change the address (target -)', $number, $line );
        $supported = array( 'r', 'redirect', 'l', 'last', 'nc', 'nocase', 'qsa', 'qsappend', 'qsd', 'qsdiscard', 'ne', 'noescape', 'end', 'pt', 'passthrough' );
        foreach ( array_keys( $flags ) as $flag )
        {
            if ( !in_array( $flag, $supported, true ) )
                return expUrlMapRow::notImportable( $pattern, $target, 'the flag ' . strtoupper( $flag ) . ' cannot be expressed', $number, $line );
        }
        $query = '';
        foreach ( $conditions as $condition )
        {
            $test = isset( $condition[1] ) ? $condition[1] : '';
            $conditionPattern = isset( $condition[2] ) ? $condition[2] : '';
            $conditionFlags = isset( $condition[3] ) ? strtolower( trim( $condition[3], '[]' ) ) : '';
            $literal = self::literal( $conditionPattern );
            if ( $conditionFlags !== '' && $conditionFlags !== 'nc' && $conditionFlags !== 'nocase' )
                $literal = null;
            if ( $literal !== null && ( $test === '%{HTTP_HOST}' || $test === '%{SERVER_NAME}' ) )
                $host = strtolower( $literal );
            elseif ( $literal !== null && $test === '%{QUERY_STRING}' && $conditionFlags === '' )
                $query = $literal;
            else
                return expUrlMapRow::notImportable( $pattern, $target, 'the condition "' . implode( ' ', array_slice( $condition, 1 ) ) . '" cannot be expressed', $number, $line );
        }
        $wildcard = expUrlMapRegex::toWildcard( $pattern );
        if ( isset( $wildcard['reason'] ) )
            return expUrlMapRow::notImportable( $pattern, $target, $wildcard['reason'], $number, $line );
        $absolute = (bool)preg_match( '#^https?://#i', $target );
        if ( !$absolute && $target[0] !== '/' )
            $target = $base . $target;
        if ( substr( $target, -1 ) === '?' )
            $target = substr( $target, 0, -1 );
        $converted = expUrlMapRegex::toPlaceholders( $target, $wildcard['groups'], 'apache' );
        if ( isset( $converted['reason'] ) )
            return expUrlMapRow::notImportable( $pattern, $target, $converted['reason'], $number, $line );
        $notes = $wildcard['notes'];
        $redirect = isset( $flags['r'] ) || isset( $flags['redirect'] ) || $absolute;
        $isWildcard = strpos( $wildcard['pattern'], '*' ) !== false;
        if ( $redirect )
        {
            $status = isset( $flags['r'] ) ? $flags['r'] : ( isset( $flags['redirect'] ) ? $flags['redirect'] : '' );
            $status = $status === '' ? 302 : ( strtolower( $status ) === 'permanent' ? 301 : ( strtolower( $status ) === 'temp' ? 302 : (int)$status ) );
            if ( $status < 300 || $status > 399 )
                return expUrlMapRow::notImportable( $pattern, $target, 'status ' . $status . ' is not a redirect', $number, $line );
            if ( $status !== 301 && $status !== 308 )
                $notes[] = 'the source answered with ' . $status . '; Exponential redirects with 301';
            $kind = $isWildcard ? expUrlMapRow::KIND_WILDCARD_FORWARD : expUrlMapRow::KIND_REDIRECT;
        }
        else
            $kind = $isWildcard ? expUrlMapRow::KIND_WILDCARD_DIRECT : expUrlMapRow::KIND_ALIAS;
        $old = ( $host !== '' ? '//' . $host : '' ) . $wildcard['pattern'] . ( $query !== '' ? '?' . $query : '' );
        return new expUrlMapRow( $old, $converted['target'], $kind, '', '', implode( '; ', $notes ), $number );
    }

    /**
     * The literal text of an expression "^text$" (escapes resolved), or null when it is more than literal text. A
     * "(www\.)?" in front of a host is left out.
     *
     * @param string $regex
     * @return string|null
     */
    private static function literal( $regex )
    {
        if ( !preg_match( '/^\^(.*)\$$/', $regex, $m ) )
            return null;
        $body = preg_replace( '/^\(www\\\\\.\)\?/', '', $m[1] );
        if ( preg_match( '/(?<!\\\\)[\[\]{}()|+*?^$]/', $body ) )
            return null;
        return preg_replace( '/\\\\(.)/', '$1', $body );
    }
}
