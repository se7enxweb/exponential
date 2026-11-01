<?php
/**
 * File containing the expStaticExtras class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The files a static host needs besides the pages (target setting Extras[]): .nojekyll, CNAME, 404.html, robots.txt
 * and sitemap.xml. Pure: each method returns the file's content; expStaticBuild writes them. 404.html is the site's
 * own 404 page, fetched and rewritten by the build (the host serves it for every missing address at any depth, so
 * its links are below the base path, never relative).
 */
class expStaticExtras
{
    /** The extras S1 writes, in the order they are written */
    const WRITTEN = array( 'nojekyll', 'cname', '404', 'robots', 'sitemap' );

    /**
     * .nojekyll: an empty file that tells GitHub Pages to publish the files as they are, without Jekyll (which leaves
     * out every file and folder whose name starts with _ or .).
     *
     * @return string
     */
    public static function nojekyll()
    {
        return '';
    }

    /**
     * CNAME: the custom domain of a GitHub Pages site, one line.
     *
     * @param string $domain
     * @return string|null null for a value that is not a host name
     */
    public static function cname( $domain )
    {
        $domain = strtolower( trim( (string)$domain ) );
        if ( !self::validDomain( $domain ) )
            return null;
        return $domain . "\n";
    }

    /**
     * Whether a string is a host name a custom domain can be (letters, digits, hyphens; at least two labels).
     *
     * @param string $domain
     * @return bool
     */
    public static function validDomain( $domain )
    {
        return strlen( (string)$domain ) <= 253
            && (bool)preg_match( '#^(?=.{1,253}$)([a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z][a-z0-9-]{0,61}[a-z0-9]$#i', (string)$domain );
    }

    /**
     * robots.txt: everything may be crawled, and the sitemap is named when the public address is known.
     *
     * @param string $publicUrl https://owner.github.io/repo/ ('' when unknown)
     * @param bool $sitemap whether a sitemap.xml is written
     * @return string
     */
    public static function robots( $publicUrl, $sitemap )
    {
        $text = "User-agent: *\nAllow: /\n";
        if ( $sitemap && $publicUrl !== '' )
            $text .= "\nSitemap: " . rtrim( (string)$publicUrl, '/' ) . "/sitemap.xml\n";
        return $text;
    }

    /**
     * sitemap.xml: every page of the export at its public address, with the date its content was last modified.
     *
     * @param string $publicUrl https://owner.github.io/repo/ (a sitemap needs absolute addresses)
     * @param array $pages the page's address in the export (expStaticLinkRewriter::pageUrl(), '' for the root) =>
     *        when it was modified (unix time) or null
     * @return string|null null when the public address is not known
     */
    public static function sitemap( $publicUrl, array $pages )
    {
        $publicUrl = trim( (string)$publicUrl );
        if ( !preg_match( '#^https?://[^/\s]+#i', $publicUrl ) )
            return null;
        $base = rtrim( $publicUrl, '/' ) . '/';
        ksort( $pages, SORT_STRING );
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
        foreach ( $pages as $url => $modified )
        {
            $xml .= '  <url><loc>' . htmlspecialchars( $base . $url, ENT_XML1 | ENT_QUOTES, 'UTF-8' ) . '</loc>';
            if ( $modified )
                $xml .= '<lastmod>' . gmdate( 'Y-m-d', (int)$modified ) . '</lastmod>';
            $xml .= "</url>\n";
        }
        return $xml . "</urlset>\n";
    }

    /**
     * An address on the site that is certainly missing, to fetch the site's own 404 page.
     *
     * @param string $token something unique (the run id)
     * @return string the path below the site
     */
    public static function missingPath( $token )
    {
        return '/static-publish-not-found-' . preg_replace( '#[^a-z0-9-]#', '', strtolower( (string)$token ) );
    }

    /**
     * A plain 404 page, for a site whose own 404 page cannot be used.
     *
     * @param string $home the address of the site's front page in the export
     * @return string
     */
    public static function fallbackNotFound( $home )
    {
        return "<!DOCTYPE html>\n<html lang=\"en\"><head><meta charset=\"utf-8\"><title>Page not found</title>"
             . "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\"></head>\n"
             . "<body><h1>Page not found</h1><p>The page you asked for is not here. <a href=\""
             . htmlspecialchars( (string)$home, ENT_QUOTES, 'UTF-8' ) . "\">Go to the front page</a>.</p></body></html>\n";
    }
}
