<?php
/**
 * File containing the expStaticServe class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The local preview of a static export (exp:static:serve): which file answers a request when the export is served
 * below a path, the way GitHub Pages serves a project site. A folder answers with its index.html, a folder without
 * its trailing slash is redirected to it, the root of the host is redirected to the mount, and everything missing
 * gets 404.html with status 404. Nothing outside the export's folder is ever served, and nothing in .git or
 * .static-publish.
 *
 * resolve() is pure apart from looking at files; the router (expstaticserverouter.php, run by PHP's built-in web
 * server) sends what it answers.
 */
class expStaticServe
{
    /** Content types of the files a static site holds */
    const TYPES = array(
        'html' => 'text/html; charset=utf-8', 'htm' => 'text/html; charset=utf-8', 'css' => 'text/css; charset=utf-8',
        'js' => 'text/javascript; charset=utf-8', 'mjs' => 'text/javascript; charset=utf-8', 'json' => 'application/json',
        'xml' => 'application/xml', 'txt' => 'text/plain; charset=utf-8', 'svg' => 'image/svg+xml', 'png' => 'image/png',
        'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'gif' => 'image/gif', 'webp' => 'image/webp', 'avif' => 'image/avif',
        'ico' => 'image/x-icon', 'woff' => 'font/woff', 'woff2' => 'font/woff2', 'ttf' => 'font/ttf', 'otf' => 'font/otf',
        'eot' => 'application/vnd.ms-fontobject', 'pdf' => 'application/pdf', 'mp4' => 'video/mp4', 'webm' => 'video/webm',
        'mp3' => 'audio/mpeg', 'ogg' => 'audio/ogg', 'zip' => 'application/zip', 'map' => 'application/json',
    );

    /**
     * What answers a request.
     *
     * @param string $root the export's folder
     * @param string $mount the path it is served below (/ or /repo/)
     * @param string $uriPath the path of the request (encoded, as it arrives)
     * @return array status (200, 301, 404), file (the file to send, or null), location (for 301), type
     */
    public static function resolve( $root, $mount, $uriPath )
    {
        $root = rtrim( str_replace( '\\', '/', (string)$root ), '/' );
        $mount = expStaticLinkRewriter::normaliseBasePath( $mount );
        $path = '/' . ltrim( (string)$uriPath, '/' );
        $notFound = is_file( $root . '/404.html' ) ? $root . '/404.html' : null;
        $missing = array( 'status' => 404, 'file' => $notFound, 'location' => '', 'type' => self::TYPES['html'] );

        if ( $mount !== '/' && $path === rtrim( $mount, '/' ) )
            return array( 'status' => 301, 'file' => null, 'location' => $mount, 'type' => '' );
        if ( $mount !== '/' && $path === '/' )
            return array( 'status' => 301, 'file' => null, 'location' => $mount, 'type' => '' );
        if ( strpos( $path, $mount ) !== 0 )
            return $missing;
        $relative = rawurldecode( (string)substr( $path, strlen( $mount ) ) );
        $isDir = $relative === '' || substr( $relative, -1 ) === '/';
        $relative = trim( $relative, '/' );
        if ( $relative !== '' )
        {
            $clean = expStaticManifest::normalisePath( $relative );
            if ( $clean === null || preg_match( '#(^|/)\.(git|static-publish)(/|$)#', $clean ) || strpos( $clean, "\0" ) !== false )
                return $missing;
            $relative = $clean;
        }
        $file = $root . ( $relative === '' ? '' : '/' . $relative );
        if ( $isDir )
            $file .= '/index.html';
        else if ( is_dir( $file ) )
            return is_file( $file . '/index.html' ) ? array( 'status' => 301, 'file' => null, 'location' => $path . '/', 'type' => '' ) : $missing;
        if ( !is_file( $file ) )
            return $missing;
        $real = realpath( $file );
        $realRoot = realpath( $root );
        if ( $real === false || $realRoot === false || strpos( str_replace( '\\', '/', $real ), str_replace( '\\', '/', $realRoot ) . '/' ) !== 0 )
            return $missing;
        return array( 'status' => 200, 'file' => $file, 'location' => '', 'type' => self::type( $file ) );
    }

    /** @return string the content type of a file by its extension */
    public static function type( $file )
    {
        $extension = strtolower( (string)pathinfo( (string)$file, PATHINFO_EXTENSION ) );
        return isset( self::TYPES[$extension] ) ? self::TYPES[$extension] : 'application/octet-stream';
    }

    /**
     * The command line of the preview server: PHP's built-in web server with the router, as an argument list (no
     * shell, so paths with spaces work on every system).
     *
     * @param string $php the PHP binary
     * @param string $host
     * @param int $port
     * @param string $root the export's folder
     * @return string[]
     */
    public static function command( $php, $host, $port, $root )
    {
        return array( (string)$php, '-S', $host . ':' . (int)$port, '-t', (string)$root, __DIR__ . DIRECTORY_SEPARATOR . 'expstaticserverouter.php' );
    }
}
