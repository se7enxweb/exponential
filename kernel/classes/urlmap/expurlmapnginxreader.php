<?php
/**
 * File containing the expUrlMapNginxReader class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Reads the redirects of an nginx configuration:
 *
 * - "rewrite ^/old$ /new permanent;" (redirect is 302, last and break are internal rewrites, which are aliases or
 *   wildcards (direct)); simple regular expressions become wildcards (expUrlMapRegex)
 * - "return 301 /new;" inside "location = /old", or inside a regular expression location "location ~ ^/old/(.*)$";
 *   an "if ($args = ...)" around the return gives the old address's query
 * - "map $uri $new { /old /new; ~^/old/(.*)$ /new/$1; }" (also $request_uri, $document_uri): each entry a redirect;
 *   the "return 301 $new;" that uses the map gives no row of its own
 * - server_name inside a server block gives the host of the rows in that block
 * - "#exp:<kind> <old> <new>" comments (written by expUrlMapNginxWriter for the mappings a rule cannot express)
 *
 * Everything else that redirects (a return in a prefix location or at the server level, other status codes, other
 * variables, rewrite inside another if) is a row that is not importable, with the reason. Other directives are
 * ignored.
 */
class expUrlMapNginxReader extends expUrlMapReader
{
    /** @var expUrlMapRow[] */
    private $rows;
    /** @var string[] the variables that maps set */
    private $mapTargets;

    /**
     * @param string $data
     * @return expUrlMapRow[]
     */
    function readString( $data )
    {
        $tree = self::parse( self::utf8Text( $data ) );
        $this->rows = array();
        $this->mapTargets = array();
        $this->collectMapTargets( $tree );
        $this->walk( $tree, array( 'host' => '', 'location' => null, 'if' => null ) );
        $rows = $this->rows;
        $this->rows = array();
        return $rows;
    }

    /**
     * The configuration as a tree of statements: each array( 'words' => [...], 'line' => n, 'children' => null or
     * a list ) or array( 'comment' => text, 'line' => n ).
     *
     * @param string $text
     * @return array
     */
    static function parse( $text )
    {
        $tokens = self::tokens( $text );
        $position = 0;
        return self::build( $tokens, $position );
    }

    /**
     * @param string $text
     * @return array list of array( type (word, ;, {, }, comment), value, line )
     */
    private static function tokens( $text )
    {
        $tokens = array();
        $length = strlen( $text );
        $line = 1;
        $i = 0;
        while ( $i < $length )
        {
            $c = $text[$i];
            if ( $c === "\n" )
            {
                $line++;
                $i++;
            }
            elseif ( ctype_space( $c ) )
                $i++;
            elseif ( $c === '#' )
            {
                $end = strpos( $text, "\n", $i );
                $end = $end === false ? $length : $end;
                $tokens[] = array( 'comment', substr( $text, $i + 1, $end - $i - 1 ), $line );
                $i = $end;
            }
            elseif ( $c === ';' || $c === '{' || $c === '}' )
            {
                $tokens[] = array( $c, $c, $line );
                $i++;
            }
            elseif ( $c === '"' || $c === "'" )
            {
                $start = $line;
                $value = '';
                $i++;
                while ( $i < $length && $text[$i] !== $c )
                {
                    if ( $text[$i] === '\\' && $i + 1 < $length && ( $text[$i + 1] === '"' || $text[$i + 1] === "'" ) )
                        $i++;
                    if ( $text[$i] === "\n" )
                        $line++;
                    $value .= $text[$i];
                    $i++;
                }
                $i++;
                $tokens[] = array( 'word', $value, $start );
            }
            else
            {
                $start = $i;
                while ( $i < $length && !ctype_space( $text[$i] ) && strpos( ';{}', $text[$i] ) === false )
                    $i++;
                $tokens[] = array( 'word', substr( $text, $start, $i - $start ), $line );
            }
        }
        return $tokens;
    }

    /**
     * @param array $tokens
     * @param int $position advanced past the closing "}" of the block (or to the end)
     * @return array
     */
    private static function build( array $tokens, &$position )
    {
        $nodes = array();
        $words = array();
        $line = 0;
        $count = count( $tokens );
        while ( $position < $count )
        {
            list( $type, $value, $tokenLine ) = $tokens[$position++];
            if ( $type === 'word' )
            {
                if ( !$words )
                    $line = $tokenLine;
                $words[] = $value;
            }
            elseif ( $type === 'comment' )
            {
                if ( !$words )
                    $nodes[] = array( 'comment' => $value, 'line' => $tokenLine );
            }
            elseif ( $type === ';' )
            {
                if ( $words )
                    $nodes[] = array( 'words' => $words, 'line' => $line, 'children' => null );
                $words = array();
            }
            elseif ( $type === '{' )
            {
                $nodes[] = array( 'words' => $words, 'line' => $words ? $line : $tokenLine, 'children' => self::build( $tokens, $position ) );
                $words = array();
            }
            else
                break;
        }
        if ( $words )
            $nodes[] = array( 'words' => $words, 'line' => $line, 'children' => null );
        return $nodes;
    }

    /**
     * @param array $nodes
     */
    private function collectMapTargets( array $nodes )
    {
        foreach ( $nodes as $node )
        {
            if ( !isset( $node['words'] ) )
                continue;
            if ( $node['children'] !== null && $node['words'] && $node['words'][0] === 'map' && isset( $node['words'][2] ) )
                $this->mapTargets[] = $node['words'][2];
            if ( $node['children'] )
                $this->collectMapTargets( $node['children'] );
        }
    }

    /**
     * @param array $nodes
     * @param array $context host, location (null or array( type, value )), if (null, or the query of an
     *                       "if ($args = ...)", or false for another condition)
     */
    private function walk( array $nodes, array $context )
    {
        foreach ( $nodes as $node )
        {
            if ( isset( $node['comment'] ) )
            {
                if ( preg_match( '/^\s*exp:([a-z\-]+)\s+(.*)$/', $node['comment'], $m ) )
                {
                    $args = expUrlMapApacheReader::tokenize( $m[2] );
                    $this->rows[] = new expUrlMapRow( isset( $args[0] ) ? $args[0] : '', isset( $args[1] ) ? $args[1] : '', $m[1], '', '', '', $node['line'] );
                }
                continue;
            }
            $words = $node['words'];
            if ( !$words )
                continue;
            $name = $words[0];
            if ( $node['children'] !== null )
            {
                if ( $name === 'server' )
                    $this->walk( $node['children'], array( 'host' => self::serverName( $node['children'] ), 'location' => null, 'if' => null ) );
                elseif ( $name === 'location' )
                    $this->walk( $node['children'], array( 'location' => self::location( $words ) ) + $context );
                elseif ( $name === 'if' )
                    $this->walk( $node['children'], array( 'if' => self::ifQuery( $words ) ) + $context );
                elseif ( $name === 'map' )
                    $this->map( $words, $node['children'], $context, $node['line'] );
                else
                    $this->walk( $node['children'], $context );
                continue;
            }
            if ( $name === 'return' )
                $this->returnStatement( $words, $context, $node['line'] );
            elseif ( $name === 'rewrite' )
                $this->rewrite( $words, $context, $node['line'] );
        }
    }

    /**
     * @param array $children
     * @return string the first plain server name, or ''
     */
    private static function serverName( array $children )
    {
        foreach ( $children as $child )
        {
            if ( isset( $child['words'] ) && $child['children'] === null && $child['words'] && $child['words'][0] === 'server_name' )
            {
                foreach ( array_slice( $child['words'], 1 ) as $name )
                {
                    if ( $name !== '_' && $name !== '' && strpbrk( $name, '*~$' ) === false )
                        return strtolower( rtrim( $name, '.' ) );
                }
            }
        }
        return '';
    }

    /**
     * @param string[] $words
     * @return array type (exact, regex, prefix, named) and value
     */
    private static function location( array $words )
    {
        $modifier = isset( $words[1] ) ? $words[1] : '';
        $value = isset( $words[2] ) ? $words[2] : '';
        if ( $modifier === '=' )
            return array( 'exact', $value );
        if ( $modifier === '~' || $modifier === '~*' )
            return array( 'regex', $value );
        if ( $modifier === '^~' )
            return array( 'prefix', $value );
        if ( $modifier !== '' && $modifier[0] === '@' )
            return array( 'named', $modifier );
        return array( 'prefix', $modifier );
    }

    /**
     * @param string[] $words
     * @return string|false the query of "if ($args = ...)", false for any other condition
     */
    private static function ifQuery( array $words )
    {
        $condition = implode( ' ', array_slice( $words, 1 ) );
        if ( preg_match( '/^\(\s*\$(?:args|query_string)\s*=\s*(.*?)\s*\)$/', $condition, $m ) )
            return $m[1];
        return false;
    }

    /**
     * @param string[] $words
     * @param array $context
     * @param int $line
     */
    private function returnStatement( array $words, array $context, $line )
    {
        $text = implode( ' ', $words ) . ';';
        $args = array_slice( $words, 1 );
        if ( count( $args ) === 1 && !ctype_digit( $args[0] ) )
            array_unshift( $args, '302' );
        $code = isset( $args[0] ) ? (int)$args[0] : 0;
        $url = isset( $args[1] ) ? $args[1] : '';
        if ( in_array( $url, $this->mapTargets, true ) )
            return;
        $location = $context['location'];
        $old = $location ? $location[1] : '';
        if ( !in_array( $code, array( 301, 302, 303, 307, 308 ), true ) || $url === '' )
        {
            $this->rows[] = expUrlMapRow::notImportable( $old, $url, 'return ' . $code . ' does not redirect', $line, $text );
            return;
        }
        if ( $context['if'] === false )
        {
            $this->rows[] = expUrlMapRow::notImportable( $old, $url, 'the return is inside an if whose condition cannot be expressed', $line, $text );
            return;
        }
        if ( !$location || $location[0] === 'prefix' || $location[0] === 'named' )
        {
            $reason = !$location ? 'a return outside a location answers every request of the server'
                                 : 'a return in a prefix location matches every path that starts with ' . $old . '; use location = or a regular expression';
            $this->rows[] = expUrlMapRow::notImportable( $old, $url, $reason, $line, $text );
            return;
        }
        $groups = array();
        $notes = array();
        $path = $old;
        if ( $location[0] === 'regex' )
        {
            $wildcard = expUrlMapRegex::toWildcard( $old );
            if ( isset( $wildcard['reason'] ) )
            {
                $this->rows[] = expUrlMapRow::notImportable( $old, $url, $wildcard['reason'], $line, $text );
                return;
            }
            $path = $wildcard['pattern'];
            $groups = $wildcard['groups'];
            $notes = $wildcard['notes'];
        }
        $this->addRedirect( $context, $path, $url, $groups, $notes, $code, $line, $text );
    }

    /**
     * @param string[] $words
     * @param array $context
     * @param int $line
     */
    private function rewrite( array $words, array $context, $line )
    {
        $text = implode( ' ', $words ) . ';';
        $regex = isset( $words[1] ) ? $words[1] : '';
        $replacement = isset( $words[2] ) ? $words[2] : '';
        $flag = isset( $words[3] ) ? $words[3] : '';
        if ( $context['if'] !== null )
        {
            $this->rows[] = expUrlMapRow::notImportable( $regex, $replacement, 'a rewrite inside an if cannot be expressed', $line, $text );
            return;
        }
        $wildcard = expUrlMapRegex::toWildcard( $regex );
        if ( isset( $wildcard['reason'] ) )
        {
            $this->rows[] = expUrlMapRow::notImportable( $regex, $replacement, $wildcard['reason'], $line, $text );
            return;
        }
        if ( substr( $replacement, -1 ) === '?' )
            $replacement = substr( $replacement, 0, -1 );
        $absolute = (bool)preg_match( '#^https?://#i', $replacement );
        if ( $flag === 'permanent' || $flag === 'redirect' || ( $flag === '' && $absolute ) )
        {
            $this->addRedirect( $context, $wildcard['pattern'], $replacement, $wildcard['groups'], $wildcard['notes'], $flag === 'permanent' ? 301 : 302, $line, $text );
            return;
        }
        if ( $flag !== '' && $flag !== 'last' && $flag !== 'break' )
        {
            $this->rows[] = expUrlMapRow::notImportable( $regex, $replacement, 'the flag ' . $flag . ' is not known', $line, $text );
            return;
        }
        $converted = expUrlMapRegex::toPlaceholders( $replacement, $wildcard['groups'], 'nginx' );
        if ( isset( $converted['reason'] ) )
        {
            $this->rows[] = expUrlMapRow::notImportable( $regex, $replacement, $converted['reason'], $line, $text );
            return;
        }
        $kind = strpos( $wildcard['pattern'], '*' ) !== false ? expUrlMapRow::KIND_WILDCARD_DIRECT : expUrlMapRow::KIND_ALIAS;
        $this->rows[] = new expUrlMapRow( self::withHost( $context, $wildcard['pattern'] ), $converted['target'], $kind, '', '', implode( '; ', $wildcard['notes'] ), $line );
    }

    /**
     * @param string[] $words
     * @param array $children
     * @param array $context
     * @param int $line
     */
    private function map( array $words, array $children, array $context, $line )
    {
        $source = isset( $words[1] ) ? $words[1] : '';
        if ( !in_array( $source, array( '$uri', '$request_uri', '$document_uri' ), true ) )
        {
            $this->rows[] = expUrlMapRow::notImportable( $source, isset( $words[2] ) ? $words[2] : '', 'a map of ' . $source . ' is not a map of paths', $line, implode( ' ', $words ) );
            return;
        }
        foreach ( $children as $child )
        {
            if ( !isset( $child['words'] ) || $child['children'] !== null || count( $child['words'] ) < 2 )
                continue;
            list( $key, $value ) = $child['words'];
            if ( in_array( $key, array( 'default', 'hostnames', 'volatile', 'include' ), true ) )
                continue;
            $text = $key . ' ' . $value . ';';
            $groups = array();
            $notes = array();
            $path = $key;
            if ( $key !== '' && $key[0] === '~' )
            {
                $wildcard = expUrlMapRegex::toWildcard( ltrim( substr( $key, 1 ), '*' ) );
                if ( isset( $wildcard['reason'] ) )
                {
                    $this->rows[] = expUrlMapRow::notImportable( $key, $value, $wildcard['reason'], $child['line'], $text );
                    continue;
                }
                $path = $wildcard['pattern'];
                $groups = $wildcard['groups'];
                $notes = $wildcard['notes'];
            }
            $this->addRedirect( array( 'if' => null ) + $context, $path, $value, $groups, $notes, 301, $child['line'], $text );
        }
    }

    /**
     * @param array $context
     * @param string $path
     * @param string $target
     * @param array $groups
     * @param string[] $notes
     * @param int $code
     * @param int $line
     * @param string $text
     */
    private function addRedirect( array $context, $path, $target, array $groups, array $notes, $code, $line, $text )
    {
        $converted = expUrlMapRegex::toPlaceholders( $target, $groups, 'nginx' );
        if ( isset( $converted['reason'] ) )
        {
            $this->rows[] = expUrlMapRow::notImportable( $path, $target, $converted['reason'], $line, $text );
            return;
        }
        if ( $code !== 301 && $code !== 308 )
            $notes[] = 'the source answered with ' . $code . '; Exponential redirects with 301';
        $old = self::withHost( $context, $path );
        if ( is_string( $context['if'] ) && $context['if'] !== '' )
            $old .= '?' . $context['if'];
        $kind = strpos( $path, '*' ) !== false ? expUrlMapRow::KIND_WILDCARD_FORWARD : expUrlMapRow::KIND_REDIRECT;
        $this->rows[] = new expUrlMapRow( $old, $converted['target'], $kind, '', '', implode( '; ', $notes ), $line );
    }

    /**
     * @param array $context
     * @param string $path
     * @return string
     */
    private static function withHost( array $context, $path )
    {
        return ( $context['host'] !== '' ? '//' . $context['host'] : '' ) . $path;
    }
}
