<?php
/**
 * File containing the expStaticLinkChecker class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Checks a static export the way a browser would use it: every page (*.html) and style sheet (*.css) is read, each
 * address in it (href, src, srcset, poster, the content of social card <meta> tags, url() and @import) is resolved
 * against the address the file is served at when the export is mounted at a path (/ for a site at the root of a
 * host, /repo/ for a GitHub project site), and must name a file of the export: a folder means its index.html, as
 * every static host serves it. Addresses on other hosts, mailto:, tel:, data: and fragments of the same page are
 * not checked. 404.html is served at any address, so its links are resolved against the mount itself.
 *
 * Reads files only; no network.
 */
class expStaticLinkChecker
{
    /** Folders of an export that are not part of the site */
    const SKIP_DIRS = array( '.git', '.static-publish' );

    /**
     * @param string $dir the export
     * @param string $mount the path the export is served at (/ or /repo/)
     * @param array $options max_broken (how many broken links to list, default 200), skip (files not read: 404.html
     *        when the export is checked below another path than the base path it was built for, since its links
     *        start at that base path)
     * @return array files (pages and style sheets read), links (addresses checked), broken (file, link, resolved),
     *         broken_count, outside (addresses that leave the mount, counted as broken too)
     */
    public static function check( $dir, $mount = '/', array $options = array() )
    {
        $dir = rtrim( str_replace( '\\', '/', (string)$dir ), '/' );
        $mount = expStaticLinkRewriter::normaliseBasePath( $mount );
        $maxBroken = isset( $options['max_broken'] ) ? (int)$options['max_broken'] : 200;
        $skip = isset( $options['skip'] ) ? (array)$options['skip'] : array();
        $result = array( 'mount' => $mount, 'files' => 0, 'links' => 0, 'broken' => array(), 'broken_count' => 0, 'skipped' => array() );
        $exists = array();
        foreach ( self::files( $dir ) as $file )
        {
            if ( in_array( $file, $skip, true ) )
            {
                $result['skipped'][] = $file;
                continue;
            }
            $extension = strtolower( (string)pathinfo( $file, PATHINFO_EXTENSION ) );
            if ( $extension !== 'html' && $extension !== 'htm' && $extension !== 'css' )
                continue;
            $result['files']++;
            $text = (string)file_get_contents( $dir . '/' . $file );
            $refs = $extension === 'css' ? self::cssReferences( $text ) : self::htmlReferences( $text );
            $base = $file === '404.html' ? $mount : $mount . ( strpos( $file, '/' ) === false ? '' : substr( $file, 0, strrpos( $file, '/' ) + 1 ) );
            foreach ( array_unique( $refs ) as $ref )
            {
                $resolved = self::resolve( $ref, $base );
                if ( $resolved === null )
                    continue;
                $result['links']++;
                if ( !isset( $exists[$resolved] ) )
                    $exists[$resolved] = self::exists( $dir, $mount, $resolved );
                if ( $exists[$resolved] )
                    continue;
                $result['broken_count']++;
                if ( count( $result['broken'] ) < $maxBroken )
                    $result['broken'][] = array( 'file' => $file, 'link' => $ref, 'resolved' => $resolved );
            }
        }
        return $result;
    }

    /**
     * The files of an export, relative, / separated, sorted; without SKIP_DIRS.
     *
     * @param string $dir
     * @return string[]
     */
    public static function files( $dir )
    {
        $dir = rtrim( str_replace( '\\', '/', (string)$dir ), '/' );
        $out = array();
        if ( !is_dir( $dir ) )
            return $out;
        $stack = array( '' );
        while ( $stack )
        {
            $relative = array_pop( $stack );
            $handle = opendir( $dir . ( $relative === '' ? '' : '/' . $relative ) );
            if ( $handle === false )
                continue;
            while ( ( $entry = readdir( $handle ) ) !== false )
            {
                if ( $entry === '.' || $entry === '..' )
                    continue;
                $path = $relative === '' ? $entry : $relative . '/' . $entry;
                if ( is_dir( $dir . '/' . $path ) )
                {
                    if ( !( $relative === '' && in_array( $entry, self::SKIP_DIRS, true ) ) )
                        $stack[] = $path;
                }
                else
                    $out[] = $path;
            }
            closedir( $handle );
        }
        sort( $out, SORT_STRING );
        return $out;
    }

    /** @return string[] the addresses of a page */
    public static function htmlReferences( $html )
    {
        $html = expStaticPostProcessor::checked( preg_replace( array( '#<!--.*?-->#s', '#(<script\b[^>]*>).*?(</script\s*>)#is' ), array( '', '$1$2' ), (string)$html ), 'linkchecker' );
        $refs = array();
        if ( preg_match_all( '#<([a-zA-Z][a-zA-Z0-9-]*)(\s[^>]*)>#s', $html, $tags, PREG_SET_ORDER ) )
            foreach ( $tags as $tag )
            {
                if ( !preg_match_all( '#\s([a-zA-Z_:][-a-zA-Z0-9_:.]*)\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s"\'>]+))#', $tag[2], $attributes, PREG_SET_ORDER ) )
                    continue;
                $values = array();
                foreach ( $attributes as $a )
                {
                    $value = isset( $a[4] ) && $a[4] !== '' ? $a[4] : ( isset( $a[3] ) && $a[3] !== '' ? $a[3] : $a[2] );
                    $values[strtolower( $a[1] )] = html_entity_decode( $value, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
                }
                foreach ( array( 'href', 'src', 'poster', 'data-src' ) as $attribute )
                    if ( isset( $values[$attribute] ) )
                        $refs[] = $values[$attribute];
                foreach ( array( 'srcset', 'data-srcset' ) as $attribute )
                    if ( isset( $values[$attribute] ) )
                        foreach ( expStaticLinkRewriter::parseSrcset( $values[$attribute] ) as $candidate )
                            $refs[] = $candidate[0];
                if ( isset( $values['style'] ) )
                    $refs = array_merge( $refs, self::cssReferences( $values['style'] ) );
            }
        if ( preg_match_all( '#<style\b[^>]*>(.*?)</style\s*>#is', $html, $styles ) )
            foreach ( $styles[1] as $css )
                $refs = array_merge( $refs, self::cssReferences( $css ) );
        return $refs;
    }

    /** @return string[] the addresses of a style sheet */
    public static function cssReferences( $css )
    {
        $refs = array();
        if ( preg_match_all( '#url\(\s*(["\']?)([^"\')]*)\1\s*\)#i', (string)$css, $m ) )
            foreach ( $m[2] as $value )
                $refs[] = trim( $value );
        if ( preg_match_all( '#@import\s+(["\'])([^"\']+)\1#i', (string)$css, $m ) )
            foreach ( $m[2] as $value )
                $refs[] = trim( $value );
        return $refs;
    }

    /**
     * An address resolved against the folder a file is served from: the absolute path without query and fragment,
     * or null for an address that is not checked (another host, mailto:, data:, a fragment, empty).
     *
     * @param string $ref
     * @param string $base the folder of the file, with a trailing slash (/repo/about-us/)
     * @return string|null
     */
    public static function resolve( $ref, $base )
    {
        $ref = trim( (string)$ref );
        if ( $ref === '' || $ref[0] === '#' || $ref[0] === '?' || strpos( $ref, '//' ) === 0 || preg_match( '#^[a-z][a-z0-9+.-]*:#i', $ref ) )
            return null;
        $ref = (string)preg_replace( '~[?#].*$~s', '', $ref );
        if ( $ref === '' )
            return null;
        $path = $ref[0] === '/' ? $ref : $base . $ref;
        return expStaticLinkRewriter::removeDotSegments( $path );
    }

    /** Whether a resolved path names a file of the export mounted at $mount. */
    private static function exists( $dir, $mount, $resolved )
    {
        $mountNoSlash = rtrim( $mount, '/' );
        if ( $resolved !== $mountNoSlash && strpos( $resolved, $mount ) !== 0 && $mount !== '/' )
            return false;
        $relative = rawurldecode( ltrim( (string)substr( $resolved, strlen( $mountNoSlash ) ), '/' ) );
        if ( $relative === '' || substr( $resolved, -1 ) === '/' )
            return is_file( $dir . '/' . ( $relative === '' ? '' : rtrim( $relative, '/' ) . '/' ) . 'index.html' );
        if ( is_file( $dir . '/' . $relative ) )
            return true;
        // a folder without its slash: the host redirects to it
        return is_dir( $dir . '/' . $relative ) && is_file( $dir . '/' . $relative . '/index.html' );
    }
}
