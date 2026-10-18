<?php
/**
 * The link and image resolvers the viewer gives expHelpMarkdownRenderer (doc/features/6.0/help-system.md):
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
        $id = self::resolvePath( $href, $currentId );
        if ( $id === null || !expHelpDocumentIndex::instance()->isAllowed( $id ) )
            return null;
        $view = expHelpDocumentIndex::instance()->document( $id ) !== null ? 'viewer' : 'asset';
        return self::absolute( expHelp::url( $view, $id, rawurldecode( $anchor ) ) );
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
        $id = self::resolvePath( preg_replace( '/[?#].*$/', '', $src ), $currentId );
        if ( $id === null || expHelpDocumentIndex::instance()->asset( $id ) === null )
            return null;
        return self::absolute( expHelp::url( 'asset', $id ) );
    }

    /**
     * A relative path resolved against the directory of the current page, normalised; null when it leaves the root.
     * Ids are paths below doc/ ("features/6.0/x.md"); a leading "/" or "doc/" means from the doc root.
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
        if ( isset( $parts[0] ) && $parts[0] === 'doc' && count( $parts ) > 1 && $path[0] === '/' )
            array_shift( $parts );
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
