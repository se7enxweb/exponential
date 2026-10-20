<?php
/**
 * The link and image resolvers the viewer gives expHelpMarkdownRenderer (doc/eng-us/features/6.0/help-system.md):
 * a relative link to an allowed page becomes its viewer URL, a relative image an asset URL; http(s) links stay
 * (the renderer adds rel="noopener"); everything else (javascript:, data:, a page outside the help, ..) is dropped.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpLinks
{
    /**
     * @param string $href as written in the page
     * @param string $currentId the id of the page it is in
     * @return string|null the URL to write, null to drop the link (its text stays)
     */
    public static function link( $href, $currentId = '' )
    {
        $href = trim( (string)$href );
        if ( $href === '' )
            return null;
        if ( $href[0] === '#' )
            return '#' . rawurlencode( rawurldecode( substr( $href, 1 ) ) );
        if ( preg_match( '#^(https?:)?//#i', $href ) || preg_match( '#^mailto:[^\s<>"]+$#i', $href ) )
            return preg_match( '#^//#', $href ) ? 'https:' . $href : $href;
        if ( preg_match( '#^[a-z][a-z0-9+.-]*:#i', $href ) )
            return null;

        $anchor = '';
        if ( ( $hash = strpos( $href, '#' ) ) !== false )
        {
            $anchor = substr( $href, $hash + 1 );
            $href = substr( $href, 0, $hash );
        }
        $href = preg_replace( '/\?.*$/', '', $href );
        $id = self::resolveId( $href, $currentId );
        if ( $id === null || !expHelpDocumentIndex::instance()->isAllowed( $id ) )
            return null;
        $index = expHelpDocumentIndex::instance();
        $view = $index->document( $id ) !== null ? 'viewer' : 'asset';
        $anchor = rawurldecode( $anchor );
        // an anchor written for another language's version of the page: the same heading of the page shown
        if ( $anchor !== '' && $view === 'viewer' && method_exists( $index, 'anchorOf' ) )
            $anchor = $index->anchorOf( $id, $anchor );
        return self::absolute( expHelp::url( $view, $id, $anchor ) );
    }

    /**
     * @param string $src as written in the page
     * @param string $currentId
     * @return string|null the image URL, null to drop the image (its alt text stays)
     */
    public static function image( $src, $currentId = '' )
    {
        $src = trim( (string)$src );
        if ( preg_match( '#^https://#i', $src ) )
            return $src;
        if ( $src === '' || preg_match( '#^[a-z][a-z0-9+.-]*:|^//#i', $src ) )
            return null;
        $id = self::resolveId( preg_replace( '/[?#].*$/', '', $src ), $currentId );
        if ( $id === null || expHelpDocumentIndex::instance()->asset( $id ) === null )
            return null;
        return self::absolute( expHelp::url( 'asset', $id ) );
    }

    /**
     * image() with the size of a served image, for the renderer's image_resolver: array( 'url', 'width', 'height' ),
     * so the page reserves the image's room and nothing moves when it loads (layout shift). An https image, or a file
     * whose size cannot be read (SVG), is its address only.
     *
     * @param string $src as written in the page
     * @param string $currentId
     * @return array|string|null
     */
    public static function sizedImage( $src, $currentId = '' )
    {
        $url = self::image( $src, $currentId );
        if ( $url === null || preg_match( '#^https://#i', trim( (string)$src ) ) )
            return $url;
        $id = self::resolveId( preg_replace( '/[?#].*$/', '', trim( (string)$src ) ), $currentId );
        $file = $id === null ? null : expHelpDocumentIndex::instance()->asset( $id );
        $size = $file !== null && is_file( $file ) ? @getimagesize( $file ) : false;
        if ( !is_array( $size ) || empty( $size[0] ) || empty( $size[1] ) )
            return $url;
        return array( 'url' => $url, 'width' => (int)$size[0], 'height' => (int)$size[1] );
    }

    /**
     * The page or image id a link of a page names. A relative link is resolved against the file the page was read
     * from (doc/eng-us/features/6.0/x.md, or its translation doc/ger-de/...), as GitHub resolves it, and the file's id is
     * taken from the index (expHelpDocumentIndex::idOf(): the language directory and a translated name are taken
     * away, doc/changelogs/x.md is changelogs/x.md). "/doc/..." names a file from the installation root. Anything
     * else falls back to resolvePath() (relative to the id).
     *
     * @param string $href without query
     * @param string $currentId
     * @param expHelpDocumentIndex|null $index default expHelpDocumentIndex::instance()
     * @return string|null
     */
    public static function resolveId( $href, $currentId = '', ?expHelpDocumentIndex $index = null )
    {
        $raw = (string)$href;
        $href = str_replace( '\\', '/', rawurldecode( $raw ) );
        if ( $href === '' || strpos( $href, "\0" ) !== false )
            return null;
        $index = $index !== null ? $index : expHelpDocumentIndex::instance();
        if ( $href[0] === '/' )
        {
            $file = strpos( ltrim( $href, '/' ), expHelpLanguage::DOC_ROOT . '/' ) === 0 ? expHelpAllowlist::normalize( $href ) : null;
            $id = $file !== null ? $index->idOf( $file ) : null;
            return $id !== null ? $id : self::resolvePath( $raw, $currentId );
        }
        $base = null;
        if ( (string)$currentId !== '' )
        {
            $document = $index->document( $currentId );
            $base = $document !== null && isset( $document['path'] ) ? (string)$document['path'] : $index->pathOf( $currentId );
        }
        if ( $base === null || $base === '' )
            return self::resolvePath( $raw, $currentId );
        // relative to the file the page was read from; a link that leaves the documentation roots names no page
        $file = self::resolvePathFrom( $href, $base );
        return $file === null ? null : $index->idOf( $file );
    }

    /**
     * A relative path resolved against the directory of a file (both relative to the installation root),
     * normalised; null when it leaves the installation root.
     *
     * @param string $path
     * @param string $file
     * @return string|null
     */
    public static function resolvePathFrom( $path, $file )
    {
        $parts = explode( '/', trim( (string)$file, '/' ) );
        array_pop( $parts );
        foreach ( explode( '/', (string)$path ) as $segment )
        {
            if ( $segment === '' || $segment === '.' )
                continue;
            if ( $segment === '..' )
            {
                if ( !$parts )
                    return null;
                array_pop( $parts );
                continue;
            }
            $parts[] = $segment;
        }
        return $parts ? implode( '/', $parts ) : null;
    }

    /**
     * A relative path resolved against the directory of the current page id, normalised; null when it leaves the
     * root. Ids are paths below doc/eng-us/ ("features/6.0/x.md"); a leading "/" or "/doc/" means from the doc root.
     *
     * @param string $path
     * @param string $currentId
     * @return string|null
     */
    public static function resolvePath( $path, $currentId = '' )
    {
        $path = str_replace( '\\', '/', rawurldecode( (string)$path ) );
        if ( $path === '' || strpos( $path, "\0" ) !== false )
            return null;
        if ( $path[0] === '/' )
            $base = array();
        else
        {
            $base = explode( '/', trim( (string)$currentId, '/' ) );
            array_pop( $base );
        }
        $parts = $base;
        foreach ( explode( '/', ltrim( $path, '/' ) ) as $segment )
        {
            if ( $segment === '' || $segment === '.' )
                continue;
            if ( $segment === '..' )
            {
                if ( !$parts )
                    return null;
                array_pop( $parts );
                continue;
            }
            $parts[] = $segment;
        }
        // "/doc/<file>": the file's id (doc/eng-us/x.md and a translation's doc/ger-de/... are x.md, doc/changelogs/x.md is
        // changelogs/x.md)
        if ( isset( $parts[0] ) && $parts[0] === 'doc' && count( $parts ) > 1 && $path[0] === '/' )
            return class_exists( 'expHelpLanguage' ) ? expHelpLanguage::fromIni()->idOfValue( implode( '/', $parts ) )
                                                     : implode( '/', array_slice( $parts, 1 ) );
        return $parts ? implode( '/', $parts ) : null;
    }

    /**
     * A module-relative URL with the siteaccess prefix, as ezurl writes it.
     *
     * @param string $url
     * @return string
     */
    public static function absolute( $url )
    {
        eZURI::transformURI( $url );
        return $url;
    }
}

?>
