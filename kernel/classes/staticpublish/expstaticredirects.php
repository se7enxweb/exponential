<?php
/**
 * File containing the expStaticRedirects class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The redirect stubs of a static copy (target extra "redirects"): a static host has no URL aliases, so every old or
 * extra address of a published page gets a small page of its own that sends the visitor on (a meta refresh, a
 * canonical link and a plain link; robots noindex). The addresses come from:
 *
 *   history   the addresses a node had before it was renamed or moved (ezurlalias_ml, is_original = 0)
 *   aliases   the custom aliases that point at a node (ezurlalias_ml, is_alias = 1)
 *   wildcards the URL wildcards without a * (ezurlwildcard): a fixed address to a fixed one
 *
 * Only addresses of nodes the build holds become stubs, and never one where the build has a page or file of its own.
 * rows() reads the database; stubs() and stub() are pure.
 *
 * @since 6.0.15
 * @package kernel
 */
class expStaticRedirects
{
    /** The most stubs a build writes */
    const MAX = 20000;

    /**
     * The alias and wildcard rows of the database.
     *
     * @param eZDBInterface $db
     * @return array aliases (rows: id, parent, lang_mask, text, action, is_original, is_alias), wildcards (rows:
     *         source_url, destination_url)
     */
    public static function rows( $db )
    {
        $aliases = array();
        foreach ( (array)expKernelTableSchema::tryArrayQuery( $db, 'SELECT id, parent, lang_mask, text, action, is_original, is_alias FROM ezurlalias_ml' ) as $row )
            $aliases[] = array_change_key_case( (array)$row, CASE_LOWER );
        $wildcards = array();
        foreach ( (array)expKernelTableSchema::tryArrayQuery( $db, 'SELECT source_url, destination_url FROM ezurlwildcard' ) as $row )
            $wildcards[] = array_change_key_case( (array)$row, CASE_LOWER );
        return array( 'aliases' => $aliases, 'wildcards' => $wildcards );
    }

    /**
     * The full path of every alias row (its texts up to the root). Pure.
     *
     * @param array $rows
     * @return array index of the row => path without the leading slash
     */
    public static function paths( array $rows )
    {
        // an element of the tree by its id: the current (is_original) one first
        $byId = array();
        foreach ( $rows as $row )
        {
            $id = (int)$row['id'];
            if ( !isset( $byId[$id] ) || ( (int)$row['is_original'] === 1 && (int)$byId[$id]['is_original'] !== 1 ) )
                $byId[$id] = $row;
        }
        $cache = array();
        $path = function ( $id, $depth = 0 ) use ( &$path, &$cache, $byId )
        {
            if ( $id === 0 )
                return '';
            if ( isset( $cache[$id] ) )
                return $cache[$id];
            if ( !isset( $byId[$id] ) || $depth > 50 )
                return null;
            $parent = $path( (int)$byId[$id]['parent'], $depth + 1 );
            if ( $parent === null )
                return null;
            return $cache[$id] = ( $parent === '' ? '' : $parent . '/' ) . (string)$byId[$id]['text'];
        };
        $out = array();
        foreach ( $rows as $i => $row )
        {
            $parent = $path( (int)$row['parent'] );
            if ( $parent !== null && (string)$row['text'] !== '' )
                $out[$i] = ( $parent === '' ? '' : $parent . '/' ) . (string)$row['text'];
        }
        return $out;
    }

    /**
     * The stubs a build writes. Pure.
     *
     * @param array $rows as rows()
     * @param array $nodePages node id => the page path the build holds for it (/about-us)
     * @param callable $taken callable( $file ) => bool: the build has this file already
     * @param array $pagePaths page path => true: every page the build holds (for wildcards to a page)
     * @return array page path of the stub (/old-name) => array( to: page path, source: history|alias|wildcard )
     */
    public static function stubs( array $rows, array $nodePages, callable $taken, array $pagePaths = array() )
    {
        $stubs = array();
        $aliases = isset( $rows['aliases'] ) ? $rows['aliases'] : array();
        $paths = self::paths( $aliases );
        foreach ( $aliases as $i => $row )
        {
            if ( !isset( $paths[$i] ) || !preg_match( '#^eznode:(\d+)$#', (string)$row['action'], $m ) || !isset( $nodePages[(int)$m[1]] ) )
                continue;
            $history = (int)$row['is_original'] === 0;
            $alias = (int)$row['is_alias'] === 1;
            if ( !$history && !$alias )
                continue;
            $from = '/' . $paths[$i];
            $to = (string)$nodePages[(int)$m[1]];
            if ( strcasecmp( rtrim( $from, '/' ), rtrim( $to, '/' ) ) !== 0 )
                self::put( $stubs, $from, $to, $history ? 'history' : 'alias', $taken );
        }
        foreach ( isset( $rows['wildcards'] ) ? $rows['wildcards'] : array() as $row )
        {
            $source = trim( (string)$row['source_url'], '/' );
            $destination = trim( (string)$row['destination_url'], '/' );
            if ( $source === '' || strpos( $source, '*' ) !== false || strpos( $destination, '{' ) !== false )
                continue;
            $to = '/' . $destination;
            if ( isset( $pagePaths[$to] ) || isset( $pagePaths[strtolower( $to )] ) )
                self::put( $stubs, '/' . $source, $to, 'wildcard', $taken );
        }
        ksort( $stubs, SORT_STRING );
        return array_slice( $stubs, 0, self::MAX, true );
    }

    /**
     * Where a redirect the crawl met ends: the redirects that follow it one after the other are followed in the map,
     * at most $max of them. Pure.
     *
     * @param array $redirects url => array( status, location ), as expPreloadRunner::redirects()
     * @param string $from the address that answered with a redirect
     * @param int $max the most redirects in a row
     * @return array to (the address the chain ends on), hops, error ('' or why it ends nowhere: a loop, a chain
     *         too long, no Location)
     */
    public static function resolveChain( array $redirects, $from, $max = 5 )
    {
        $seen = array( (string)$from => true );
        $to = (string)$from;
        $hops = 0;
        while ( isset( $redirects[$to] ) )
        {
            if ( $hops >= (int)$max )
                return array( 'to' => $to, 'hops' => $hops, 'error' => sprintf( 'chain longer than %d', (int)$max ) );
            $next = isset( $redirects[$to]['location'] ) ? (string)$redirects[$to]['location'] : '';
            if ( $next === '' )
                return array( 'to' => $to, 'hops' => $hops, 'error' => 'without a Location' );
            $hops++;
            if ( isset( $seen[$next] ) )
                return array( 'to' => $next, 'hops' => $hops, 'error' => 'loop' );
            $seen[$next] = true;
            $to = $next;
        }
        return array( 'to' => $to, 'hops' => $hops, 'error' => '' );
    }

    /** Adds one stub unless the build has that file already or it is not a file name. */
    private static function put( array &$stubs, $from, $to, $source, callable $taken )
    {
        $file = expStaticLinkRewriter::pageFile( $from );
        if ( $file === null || isset( $stubs[$from] ) || call_user_func( $taken, $file ) )
            return;
        $stubs[$from] = array( 'to' => $to, 'source' => $source, 'file' => $file );
    }

    /**
     * One stub page.
     *
     * @param string $link the address of the page it sends to, as a link from the stub
     * @param string $canonical its absolute address ('' when the public address is not known)
     * @return string
     */
    public static function stub( $link, $canonical = '' )
    {
        $h = htmlspecialchars( (string)$link, ENT_QUOTES, 'UTF-8' );
        return "<!DOCTYPE html>\n<html lang=\"en\"><head><meta charset=\"utf-8\"><title>Moved</title>\n"
             . "<meta http-equiv=\"refresh\" content=\"0; url=$h\">\n<meta name=\"robots\" content=\"noindex\">\n"
             . ( $canonical !== '' ? '<link rel="canonical" href="' . htmlspecialchars( (string)$canonical, ENT_QUOTES, 'UTF-8' ) . "\">\n" : '' )
             . "</head><body><p>This page has moved: <a href=\"$h\">$h</a></p></body></html>\n";
    }
}
