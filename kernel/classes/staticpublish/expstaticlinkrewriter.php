<?php
/**
 * File containing the expStaticLinkRewriter class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Rewrites the links of rendered pages and style sheets for a static copy of a site (static publishing): every
 * address that points into the site becomes a relative path to the exported file, or a path below the target's base
 * path (/repo/ of a GitHub project site). Pure: no database, no file system, no network; it is told which pages and
 * files the export holds and answers with the rewritten text and what it referred to.
 *
 * What counts as the site: the addresses of the given origins (scheme, host and port: the address the build fetched
 * from and the site's own SiteURL), below the site path (the siteaccess prefix, /site, or the folder of the
 * installation). /index.php is dropped wherever it stands. Files of the host (var/, design/, extension/<x>/design/,
 * share/) are files whatever the site path, and keep their path in the export.
 *
 * A page lives in the export as <path>/index.html and is linked as <path>/ (the form every static host serves); the
 * site root is index.html. A link to a page the export does not hold (a module view such as the search or the login,
 * a page with a query string, a page left out) goes to the live site when a live address is known, so it keeps
 * working; a link outside the site (another siteaccess, the administration) does too. Fragments (#top) and the query
 * of a file (style.css?x=1) are kept. Addresses that are not links (mailto:, tel:, data:, javascript:) and links to
 * other hosts are left alone.
 *
 * The specification: doc/eng-us/specifications/6.0/static-publishing-build-format.md.
 */
class expStaticLinkRewriter
{
    /** Links relative to the file that holds them: ../about-us/ */
    const MODE_RELATIVE = 'relative';

    /** Links below the base path: /repo/about-us/ */
    const MODE_BASE_PATH = 'base-path';

    /** Paths of the host that are files, whatever the site path */
    const FILE_ROOTS = '#^/(var/|design/|extension/[^/]+/design/|share/|lib/ezjscore/|favicon\.ico$|apple-touch-icon[^/]*$)#';

    /** File extensions of addresses that are files and not pages */
    const FILE_EXTENSIONS = 'jpg,jpeg,png,gif,webp,svg,ico,bmp,tif,tiff,avif,css,js,mjs,map,woff,woff2,ttf,eot,otf,mp3,mp4,m4a,m4v,mov,avi,mkv,webm,ogg,oga,ogv,wav,flac,pdf,doc,docx,xls,xlsx,ppt,pptx,odt,ods,odp,rtf,txt,csv,epub,mobi,zip,tar,gz,tgz,bz2,xz,7z,rar,json,xml,rss,atom,ics,vcf,swf';

    /** Attributes that hold one address */
    const URL_ATTRIBUTES = array( 'href', 'src', 'poster', 'action', 'data-src', 'data-href', 'data-background', 'background', 'cite', 'longdesc', 'formaction', 'data', 'manifest' );

    /** Attributes that hold a list of image candidates */
    const SRCSET_ATTRIBUTES = array( 'srcset', 'data-srcset', 'imagesrcset' );

    /** <meta> names and properties whose content is an address (social cards) */
    const META_URL = '#^(og:(url|image|image:url|image:secure_url|audio|video|video:url)|twitter:(image|image:src|url)|msapplication-tileimage|thumbnail)$#i';

    private $origins = array();
    private $sitePath = '';
    private $mode = self::MODE_RELATIVE;
    private $basePath = '/';
    private $publicUrl = '';
    private $liveUrl = '';
    private $pages = null;
    private $files = null;
    private $fileExtensions = array();

    /** page path => true, of the pages found in the text rewritten since reset() */
    private $foundPages = array();

    /** output path => source path, of the files found since reset() */
    private $foundFiles = array();

    private $counts = array( 'rewritten' => 0, 'live' => 0, 'external' => 0 );

    /**
     * @param array $options origins (array of scheme://host[:port]), site_path ('' or /site), mode (relative or
     *        base-path), base_path (/repo/; the mount of base-path links), public_url (https://owner.github.io/repo/:
     *        absolute addresses for canonical, alternate and social card links; empty: they are rewritten like any
     *        link), live_url (where links to what the export does not hold go: https://www.example.com; empty: left
     *        as they are), pages (callable( $pagePath, $query ) => bool: whether the export holds the page; null:
     *        every page without a query), files (callable( $outputPath ) => bool: whether the export holds the
     *        file; null: every file)
     */
    public function __construct( array $options = array() )
    {
        foreach ( isset( $options['origins'] ) ? (array)$options['origins'] : array() as $origin )
        {
            $key = self::originKey( (string)$origin );
            if ( $key !== '' )
                $this->origins[$key] = true;
        }
        $this->sitePath = isset( $options['site_path'] ) ? self::trimSlash( (string)$options['site_path'] ) : '';
        $this->mode = isset( $options['mode'] ) && $options['mode'] === self::MODE_BASE_PATH ? self::MODE_BASE_PATH : self::MODE_RELATIVE;
        $this->basePath = self::normaliseBasePath( isset( $options['base_path'] ) ? (string)$options['base_path'] : '/' );
        $this->publicUrl = isset( $options['public_url'] ) && $options['public_url'] !== '' ? rtrim( (string)$options['public_url'], '/' ) . '/' : '';
        $this->liveUrl = isset( $options['live_url'] ) ? rtrim( (string)$options['live_url'], '/' ) : '';
        $this->pages = isset( $options['pages'] ) && is_callable( $options['pages'] ) ? $options['pages'] : null;
        $this->files = isset( $options['files'] ) && is_callable( $options['files'] ) ? $options['files'] : null;
        $this->fileExtensions = array_flip( explode( ',', self::FILE_EXTENSIONS ) );
    }

    /**
     * A base path in its one form: a leading and a trailing slash, no empty, dot or dot-dot segment. '' and '/' are
     * the root.
     *
     * @param string $path
     * @return string
     */
    public static function normaliseBasePath( $path )
    {
        $segments = array();
        foreach ( explode( '/', str_replace( '\\', '/', (string)$path ) ) as $segment )
            if ( $segment !== '' && $segment !== '.' && $segment !== '..' )
                $segments[] = $segment;
        return $segments ? '/' . implode( '/', $segments ) . '/' : '/';
    }

    /** @return string scheme-less host[:port] of an address, lower case; '' for none */
    private static function originKey( $url )
    {
        if ( strpos( $url, '://' ) === false )
            $url = 'http://' . $url;
        $host = parse_url( $url, PHP_URL_HOST );
        if ( !is_string( $host ) || $host === '' )
            return '';
        $port = parse_url( $url, PHP_URL_PORT );
        return strtolower( $host ) . ( $port ? ':' . $port : '' );
    }

    /** @return string the path without its trailing slashes; '' for the root */
    private static function trimSlash( $path )
    {
        $path = rtrim( str_replace( '\\', '/', $path ), '/' );
        if ( $path !== '' && $path[0] !== '/' )
            $path = '/' . $path;
        return $path;
    }

    /** Forgets the pages, files and counts found so far. */
    public function reset()
    {
        $this->foundPages = array();
        $this->foundFiles = array();
        $this->counts = array( 'rewritten' => 0, 'live' => 0, 'external' => 0 );
    }

    /** @return array page path => true, of the internal page links found since reset() */
    public function foundPages()
    {
        return $this->foundPages;
    }

    /** @return array output path => source path (the path on the host), of the files found since reset() */
    public function foundFiles()
    {
        return $this->foundFiles;
    }

    /** @return array rewritten, live (sent to the live site), external (left alone) */
    public function counts()
    {
        return $this->counts;
    }

    /**
     * Where a page is written in the export: index.html for the root, <path>/index.html for the others, the path
     * itself for one that ends in .html or .htm. Decoded (%20 is a space in the file name), with / separators.
     * null for a path that cannot be a file on every system: a segment with < > : " \ | ? * or a control character,
     * a dot segment, or one that ends in a dot or a space (Windows drops those).
     *
     * @param string $pagePath the page's path in the site, /about-us
     * @return string|null
     */
    public static function pageFile( $pagePath )
    {
        $path = rawurldecode( trim( (string)$pagePath, '/' ) );
        if ( $path === '' )
            return 'index.html';
        foreach ( explode( '/', $path ) as $segment )
            if ( !self::safeSegment( $segment ) )
                return null;
        if ( preg_match( '#\.html?$#i', $path ) )
            return $path;
        return $path . '/index.html';
    }

    /**
     * Whether a path in the export is safe on every system (the same rules as pageFile()).
     *
     * @param string $path relative, / separated
     * @return bool
     */
    public static function safePath( $path )
    {
        $path = (string)$path;
        if ( $path === '' || $path[0] === '/' )
            return false;
        foreach ( explode( '/', $path ) as $segment )
            if ( !self::safeSegment( $segment ) )
                return false;
        return true;
    }

    private static function safeSegment( $segment )
    {
        if ( $segment === '' || $segment === '.' || $segment === '..' )
            return false;
        if ( preg_match( '#[<>:"\\\\|?*\x00-\x1f]#', $segment ) )
            return false;
        $last = substr( $segment, -1 );
        if ( $last === '.' || $last === ' ' )
            return false;
        return !preg_match( '#^(con|prn|aux|nul|com[1-9]|lpt[1-9])(\..*)?$#i', $segment );
    }

    /**
     * The address a page is linked by inside the export, relative to the export's root: '' for the root, about-us/
     * for the others, the file for one that ends in .html.
     *
     * @param string $pageFile pageFile()
     * @return string
     */
    public static function pageUrl( $pageFile )
    {
        if ( $pageFile === 'index.html' )
            return '';
        if ( substr( $pageFile, -11 ) === '/index.html' )
            return self::encodePath( substr( $pageFile, 0, -10 ) );
        return self::encodePath( $pageFile );
    }

    /** @return string the path with each segment percent-encoded as an address needs it (/ kept) */
    public static function encodePath( $path )
    {
        $out = array();
        foreach ( explode( '/', (string)$path ) as $segment )
            $out[] = str_replace( array( '%28', '%29', '%2C', '%40', '%7E', '%21', '%3D', '%2B' ), array( '(', ')', ',', '@', '~', '!', '=', '+' ), rawurlencode( $segment ) );
        return implode( '/', $out );
    }

    /**
     * What an address points to, as the export sees it.
     *
     * @param string $url the address as written
     * @param string $documentPath the path on the host of the document that holds it (/site/about-us), for relative
     *        addresses
     * @return array kind (page, file, outside, external, none), path (the host path), page (the page path in the
     *         site, for a page), output (the file in the export, for a file), query (with ?), fragment (with #)
     */
    public function classify( $url, $documentPath )
    {
        $url = trim( (string)$url );
        $none = array( 'kind' => 'none', 'path' => '', 'page' => '', 'output' => '', 'query' => '', 'fragment' => '' );
        if ( $url === '' || $url[0] === '#' || $url[0] === '?' )
            return $none;
        if ( preg_match( '#^[a-z][a-z0-9+.-]*:#i', $url ) && !preg_match( '#^https?://#i', $url ) )
            return $none;

        $fragment = '';
        $hash = strpos( $url, '#' );
        if ( $hash !== false )
        {
            $fragment = substr( $url, $hash );
            $url = substr( $url, 0, $hash );
        }
        $query = '';
        $mark = strpos( $url, '?' );
        if ( $mark !== false )
        {
            $query = substr( $url, $mark );
            $url = substr( $url, 0, $mark );
        }

        if ( strpos( $url, '//' ) === 0 || preg_match( '#^https?://#i', $url ) )
        {
            $absolute = strpos( $url, '//' ) === 0 ? 'http:' . $url : $url;
            if ( !isset( $this->origins[self::originKey( $absolute )] ) )
                return array( 'kind' => 'external' ) + $none;
            $path = (string)parse_url( $absolute, PHP_URL_PATH );
            if ( $path === '' )
                $path = '/';
        }
        else if ( $url !== '' && $url[0] === '/' )
            $path = $url;
        else
        {
            $documentPath = (string)$documentPath;
            $dir = substr( $documentPath, 0, (int)strrpos( $documentPath, '/' ) + 1 );
            $path = ( $dir === '' ? '/' : $dir ) . $url;
        }

        $path = self::removeDotSegments( $path );
        // /index.php/about and /index.php are the site's addresses without it
        $path = preg_replace( '#/index\.php(?=/|$)#', '', $path );
        if ( $path === '' )
            $path = '/';

        $out = array( 'path' => $path, 'query' => $query, 'fragment' => $fragment, 'page' => '', 'output' => '' );
        if ( preg_match( self::FILE_ROOTS, $path ) )
            return array( 'kind' => 'file', 'output' => rawurldecode( ltrim( $path, '/' ) ) ) + $out;

        if ( $this->sitePath !== '' )
        {
            if ( $path !== $this->sitePath && strpos( $path, $this->sitePath . '/' ) !== 0 )
                return array( 'kind' => 'outside' ) + $out;
            $page = (string)substr( $path, strlen( $this->sitePath ) );
        }
        else
            $page = $path;
        $page = $page === '' ? '/' : $page;
        if ( strlen( $page ) > 1 )
            $page = rtrim( $page, '/' );

        $extension = strtolower( (string)pathinfo( $page, PATHINFO_EXTENSION ) );
        if ( $extension !== '' && isset( $this->fileExtensions[$extension] ) )
            return array( 'kind' => 'file', 'page' => '', 'output' => rawurldecode( ltrim( $page, '/' ) ) ) + $out;

        return array( 'kind' => 'page', 'page' => $page ) + $out;
    }

    /** Resolves . and .. segments of an absolute path (RFC 3986 5.2.4); never above the root. */
    public static function removeDotSegments( $path )
    {
        $trailing = substr( $path, -1 ) === '/' || preg_match( '#/\.\.?$#', $path );
        $out = array();
        foreach ( explode( '/', $path ) as $segment )
        {
            if ( $segment === '' || $segment === '.' )
                continue;
            if ( $segment === '..' )
            {
                array_pop( $out );
                continue;
            }
            $out[] = $segment;
        }
        return '/' . implode( '/', $out ) . ( $out && $trailing ? '/' : '' );
    }

    /**
     * The path from a file in the export to another: ../../about-us/ from about-us/team/index.html. Both relative
     * to the export's root; a target that ends in / (or is '') is a folder.
     *
     * @param string $fromFile the file holding the link
     * @param string $target the address in the export (pageUrl() or a file)
     * @return string
     */
    public static function relative( $fromFile, $target )
    {
        $fromDir = strpos( $fromFile, '/' ) === false ? array() : explode( '/', substr( $fromFile, 0, strrpos( $fromFile, '/' ) ) );
        $isDir = $target === '' || substr( $target, -1 ) === '/';
        $to = $target === '' ? array() : explode( '/', rtrim( $target, '/' ) );
        $common = 0;
        $max = min( count( $fromDir ), count( $to ) );
        while ( $common < $max && $fromDir[$common] === $to[$common] )
            $common++;
        $parts = array_merge( array_fill( 0, count( $fromDir ) - $common, '..' ), array_slice( $to, $common ) );
        $path = implode( '/', $parts );
        if ( $isDir )
            return $path === '' ? './' : $path . '/';
        return $path === '' ? './' : $path;
    }

    /**
     * The address of something in the export, as a link from a file of the export.
     *
     * @param string $target the address relative to the export's root (pageUrl() or an encoded file path)
     * @param string $fromFile the file holding the link
     * @param bool $absolute an absolute address (canonical and social card links) when a public address is known
     * @return string
     */
    public function link( $target, $fromFile, $absolute = false )
    {
        if ( $absolute && $this->publicUrl !== '' )
            return $this->publicUrl . $target;
        if ( $this->mode === self::MODE_BASE_PATH )
            return $this->basePath . $target;
        return self::relative( $fromFile, $target );
    }

    /**
     * One address rewritten.
     *
     * @param string $url
     * @param string $documentPath the host path of the document holding it
     * @param string $fromFile its file in the export
     * @param bool $absolute an absolute address is wanted (canonical, alternate, social cards)
     * @return string
     */
    public function rewriteUrl( $url, $documentPath, $fromFile, $absolute = false )
    {
        $c = $this->classify( $url, $documentPath );
        if ( $c['kind'] === 'none' )
            return $url;
        if ( $c['kind'] === 'external' )
        {
            $this->counts['external']++;
            return $url;
        }
        if ( $c['kind'] === 'file' )
        {
            if ( !self::safePath( $c['output'] ) )
                return $this->live( $url, $c );
            $this->foundFiles[$c['output']] = $c['path'];
            if ( $this->files !== null && !call_user_func( $this->files, $c['output'] ) )
                return $this->live( $url, $c );
            $this->counts['rewritten']++;
            return $this->link( self::encodePath( $c['output'] ), $fromFile, $absolute ) . $c['query'] . $c['fragment'];
        }
        if ( $c['kind'] === 'page' )
        {
            $file = self::pageFile( $c['page'] );
            if ( $c['query'] === '' && $file !== null )
                $this->foundPages[$c['page']] = true;
            $held = $file !== null && ( $this->pages === null ? $c['query'] === '' : (bool)call_user_func( $this->pages, $c['page'], $c['query'] ) );
            if ( !$held )
                return $this->live( $url, $c );
            $this->counts['rewritten']++;
            return $this->link( self::pageUrl( (string)$file ), $fromFile, $absolute ) . $c['query'] . $c['fragment'];
        }
        return $this->live( $url, $c );
    }

    /** The address on the live site, or the address unchanged when no live site is known. */
    private function live( $url, array $c )
    {
        if ( $this->liveUrl === '' )
        {
            $this->counts['external']++;
            return $url;
        }
        $this->counts['live']++;
        return $this->liveUrl . $c['path'] . $c['query'] . $c['fragment'];
    }

    /**
     * A srcset value rewritten: each candidate's address, the width or density kept.
     *
     * @param string $value
     * @param string $documentPath
     * @param string $fromFile
     * @return string
     */
    public function rewriteSrcset( $value, $documentPath, $fromFile )
    {
        $out = array();
        foreach ( self::parseSrcset( $value ) as $candidate )
            $out[] = $this->rewriteUrl( $candidate[0], $documentPath, $fromFile ) . ( $candidate[1] !== '' ? ' ' . $candidate[1] : '' );
        return implode( ', ', $out );
    }

    /**
     * The candidates of a srcset value as the HTML standard splits them: an address up to white space (a comma at
     * its end ends the candidate), then the descriptors up to the next comma.
     *
     * @param string $value
     * @return array of array( address, descriptors )
     */
    public static function parseSrcset( $value )
    {
        $value = (string)$value;
        $length = strlen( $value );
        $i = 0;
        $out = array();
        while ( $i < $length )
        {
            while ( $i < $length && ( ctype_space( $value[$i] ) || $value[$i] === ',' ) )
                $i++;
            if ( $i >= $length )
                break;
            $start = $i;
            while ( $i < $length && !ctype_space( $value[$i] ) )
                $i++;
            $url = substr( $value, $start, $i - $start );
            $descriptors = '';
            if ( substr( $url, -1 ) === ',' )
                $url = rtrim( $url, ',' );
            else
            {
                $start = $i;
                $depth = 0;
                while ( $i < $length && ( $value[$i] !== ',' || $depth > 0 ) )
                {
                    if ( $value[$i] === '(' )
                        $depth++;
                    else if ( $value[$i] === ')' && $depth > 0 )
                        $depth--;
                    $i++;
                }
                $descriptors = trim( substr( $value, $start, $i - $start ) );
            }
            if ( $url !== '' )
                $out[] = array( $url, $descriptors );
        }
        return $out;
    }

    /**
     * A style sheet rewritten: the addresses of url() and @import.
     *
     * @param string $css
     * @param string $documentPath the host path of the style sheet (or of the page for a <style> block)
     * @param string $fromFile its file in the export (or the page's)
     * @return string
     */
    public function rewriteCss( $css, $documentPath, $fromFile )
    {
        $rewriter = $this;
        $css = preg_replace_callback( '#url\(\s*(["\']?)([^"\')]*)\1\s*\)#i', function ( $m ) use ( $rewriter, $documentPath, $fromFile )
        {
            $value = trim( $m[2] );
            if ( $value === '' || stripos( $value, 'data:' ) === 0 )
                return $m[0];
            $new = $rewriter->rewriteUrl( $value, $documentPath, $fromFile );
            return $new === $value ? $m[0] : 'url(' . $m[1] . $new . $m[1] . ')';
        }, (string)$css );
        return (string)preg_replace_callback( '#(@import\s+)(["\'])([^"\']+)\2#i', function ( $m ) use ( $rewriter, $documentPath, $fromFile )
        {
            $new = $rewriter->rewriteUrl( $m[3], $documentPath, $fromFile );
            return $m[1] . $m[2] . $new . $m[2];
        }, (string)$css );
    }

    /**
     * A page rewritten: the addresses in its tags (href, src, srcset, poster, action and the others of
     * URL_ATTRIBUTES), the url() of style attributes and <style> blocks, and the social card <meta> tags. The
     * content of <script> and comments is left alone; so is everything that is not an address.
     *
     * @param string $html
     * @param string $documentPath the page's path on the host (/site/about-us)
     * @param string $fromFile its file in the export (about-us/index.html)
     * @return string
     */
    public function rewriteHtml( $html, $documentPath, $fromFile )
    {
        $html = (string)$html;
        $parts = preg_split( '#(<!--.*?-->|<script\b[^>]*>.*?</script\s*>|<style\b[^>]*>.*?</style\s*>)#is', $html, -1, PREG_SPLIT_DELIM_CAPTURE );
        if ( $parts === false )
            return $html;
        $out = '';
        foreach ( $parts as $i => $part )
        {
            if ( $i % 2 === 0 )
            {
                $out .= $this->rewriteTags( $part, $documentPath, $fromFile );
                continue;
            }
            if ( strpos( $part, '<!--' ) === 0 )
            {
                $out .= $part;
                continue;
            }
            $end = strpos( $part, '>' );
            $open = substr( $part, 0, $end + 1 );
            $rest = substr( $part, $end + 1 );
            if ( stripos( $open, '<style' ) === 0 )
            {
                $close = strripos( $rest, '</style' );
                $out .= $this->rewriteTags( $open, $documentPath, $fromFile )
                      . $this->rewriteCss( substr( $rest, 0, $close ), $documentPath, $fromFile ) . substr( $rest, $close );
            }
            else if ( preg_match( '#\btype\s*=\s*(["\']?)application/ld\+json\1#i', $open ) )
                $out .= $open . $this->rewriteJsonLd( $rest, $documentPath, $fromFile );
            else
                $out .= $this->rewriteTags( $open, $documentPath, $fromFile ) . $rest;
        }
        return $out;
    }

    /**
     * Structured data (<script type="application/ld+json">): its absolute addresses of the site are rewritten as
     * absolute addresses (below the public address when one is known), so the copy names neither the address the build
     * fetched from nor a page it does not hold. Nothing else in it changes; escaped slashes (\/) stay escaped.
     *
     * @param string $json the script's content and its closing tag
     * @param string $documentPath
     * @param string $fromFile
     * @return string
     */
    private function rewriteJsonLd( $json, $documentPath, $fromFile )
    {
        $rewriter = $this;
        return (string)preg_replace_callback( '#"(https?:(?:\\\\?/){2}[^"\s<>]+)"#i', function ( $m ) use ( $rewriter, $documentPath, $fromFile )
        {
            $escaped = strpos( $m[1], '\\/' ) !== false;
            $url = str_replace( '\\/', '/', $m[1] );
            $new = $rewriter->rewriteUrl( $url, $documentPath, $fromFile, true );
            if ( $new === $url )
                return $m[0];
            return '"' . ( $escaped ? str_replace( '/', '\\/', $new ) : $new ) . '"';
        }, (string)$json );
    }

    /** The tags of a piece of markup with no script, style or comment in it. */
    private function rewriteTags( $markup, $documentPath, $fromFile )
    {
        $rewriter = $this;
        return (string)preg_replace_callback( '#<([a-zA-Z][a-zA-Z0-9:-]*)(\s[^>]*?)?(/?)>#s', function ( $m ) use ( $rewriter, $documentPath, $fromFile )
        {
            if ( !isset( $m[2] ) || $m[2] === '' )
                return $m[0];
            $attributes = $rewriter->rewriteAttributes( strtolower( $m[1] ), $m[2], $documentPath, $fromFile );
            return $attributes === $m[2] ? $m[0] : '<' . $m[1] . $attributes . $m[3] . '>';
        }, $markup );
    }

    /**
     * The attributes of one tag rewritten. Public for the callback; use rewriteHtml().
     *
     * @param string $tag lower case
     * @param string $attributes the attribute text, with its leading white space
     * @param string $documentPath
     * @param string $fromFile
     * @return string
     */
    public function rewriteAttributes( $tag, $attributes, $documentPath, $fromFile )
    {
        if ( !preg_match_all( '#(\s+)([a-zA-Z_:][-a-zA-Z0-9_:.]*)(?:(\s*=\s*)(?:"([^"]*)"|\'([^\']*)\'|([^\s"\'>]+)))?#', $attributes, $all, PREG_SET_ORDER | PREG_OFFSET_CAPTURE ) )
            return $attributes;

        // which attribute names this tag has, for <meta> and <link>
        $names = array();
        foreach ( $all as $a )
            if ( isset( $a[3] ) && $a[3][1] >= 0 )
                $names[strtolower( $a[2][0] )] = html_entity_decode( self::attributeValue( $a ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );

        $absolute = false;
        if ( $tag === 'link' && isset( $names['rel'] ) && preg_match( '#\b(canonical|alternate|shortlink)\b#i', $names['rel'] ) )
            $absolute = true;
        $metaUrl = $tag === 'meta' && (
                       ( isset( $names['property'] ) && preg_match( self::META_URL, $names['property'] ) ) ||
                       ( isset( $names['name'] ) && preg_match( self::META_URL, $names['name'] ) ) ||
                       ( isset( $names['itemprop'] ) && preg_match( '#^(image|url|thumbnailurl)$#i', $names['itemprop'] ) ) );

        $out = '';
        $position = 0;
        foreach ( $all as $a )
        {
            if ( !isset( $a[3] ) || $a[3][1] < 0 )
                continue;
            $name = strtolower( $a[2][0] );
            $raw = self::attributeValue( $a );
            $value = html_entity_decode( $raw, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
            $new = $value;
            if ( in_array( $name, self::URL_ATTRIBUTES, true ) )
                $new = $this->rewriteUrl( $value, $documentPath, $fromFile, $absolute );
            else if ( in_array( $name, self::SRCSET_ATTRIBUTES, true ) )
                $new = $this->rewriteSrcset( $value, $documentPath, $fromFile );
            else if ( $name === 'style' && stripos( $value, 'url(' ) !== false )
                $new = $this->rewriteCss( $value, $documentPath, $fromFile );
            else if ( $name === 'content' && $metaUrl )
                $new = $this->rewriteUrl( $value, $documentPath, $fromFile, true );
            if ( $new === $value )
                continue;
            $start = $a[0][1];
            $end = $start + strlen( $a[0][0] );
            $quote = isset( $a[5] ) && $a[5][1] >= 0 ? "'" : '"';
            $out .= substr( $attributes, $position, $start - $position ) . $a[1][0] . $a[2][0] . $a[3][0] . $quote
                  . htmlspecialchars( $new, ENT_QUOTES, 'UTF-8' ) . $quote;
            $position = $end;
        }
        return $out === '' ? $attributes : $out . substr( $attributes, $position );
    }

    /** @return string the raw value of a matched attribute, whichever quoting it had */
    private static function attributeValue( array $a )
    {
        foreach ( array( 4, 5, 6 ) as $i )
            if ( isset( $a[$i] ) && $a[$i][1] >= 0 )
                return $a[$i][0];
        return '';
    }
}
