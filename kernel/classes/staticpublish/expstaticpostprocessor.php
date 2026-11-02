<?php
/**
 * File containing the expStaticPostProcessor class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * What a page of a static copy must not carry, taken out (strip()) and checked for afterwards (refusals()). Pure: the
 * markup in, the markup and what was taken out back.
 *
 * strip() always runs and removes:
 *  - the debug output: the debug bar between <!--exp-debug-bar--> and <!--/exp-debug-bar--> (expDebugBarReport), the
 *    classic report that starts with <div id="debug"> and ends with <a id="debug-end"></a> (with the script after
 *    it), and the template debug comments (<!-- START: including template ... -->);
 *  - form tokens: the hidden ezxform_token fields, the csrf-param and csrf-token <meta> tags and the hidden span of
 *    a page without <head>;
 *  - session parts: session ids in addresses (eZSESSID...=, PHPSESSID=);
 *  - Content Security Policy nonces: nonce="..." attributes, and a CSP <meta> tag that names a nonce (a nonce is
 *    for one response; in a static file it would block the page's own scripts).
 *
 * refusals() names what makes a page unfit for publishing after strip(): debug output or a form token that is
 * still there, and a session cookie the response set (the page was made for a session, so it may hold what only
 * that session may see). The build leaves such a page out and fails.
 */
class expStaticPostProcessor
{
    /** The names a session cookie starts with (site.ini [Session] SessionNamePrefix and PHP's default) */
    const SESSION_NAMES = array( 'eZSESSID', 'PHPSESSID' );

    /** The form token field (ezxFormToken::FORM_FIELD) */
    const TOKEN_FIELD = 'ezxform_token';

    /**
     * The page without debug output, form tokens, session ids and nonces.
     *
     * @param string $html
     * @param array $sessionNames the names session cookies start with (default SESSION_NAMES)
     * @return array html, removed (debug, template_comments, tokens, session_ids, nonces, csp: how many of each)
     */
    public static function strip( $html, array $sessionNames = array() )
    {
        $sessionNames = $sessionNames ?: self::SESSION_NAMES;
        $removed = array( 'debug' => 0, 'template_comments' => 0, 'tokens' => 0, 'session_ids' => 0, 'nonces' => 0, 'csp' => 0 );
        $html = (string)$html;

        // the debug bar (expDebugBarReport): everything between its markers, its style sheets and scripts included
        while ( ( $start = strpos( $html, '<!--exp-debug-bar-->' ) ) !== false )
        {
            $end = strpos( $html, '<!--/exp-debug-bar-->', $start );
            if ( $end === false )
                break;
            $html = substr( $html, 0, $start ) . substr( $html, $end + strlen( '<!--/exp-debug-bar-->' ) );
            $removed['debug']++;
        }
        // the debug report: from its <div id="debug"> to the anchor after it, and the script that follows it
        while ( preg_match( '#<div\s+id=(["\'])debug\1#i', $html, $m, PREG_OFFSET_CAPTURE ) )
        {
            $start = $m[0][1];
            if ( !preg_match( '#<a\s+id=(["\'])debug-end\1\s*>\s*</a>#i', $html, $e, PREG_OFFSET_CAPTURE, $start ) )
                break;
            $end = $e[0][1] + strlen( $e[0][0] );
            if ( preg_match( '#\G\s*<script\b[^>]*>.*?</script\s*>#is', $html, $s, 0, $end ) && strpos( $s[0], 'debug' ) !== false )
                $end += strlen( $s[0] );
            $html = substr( $html, 0, $start ) . substr( $html, $end );
            $removed['debug']++;
        }
        $html = self::checked( preg_replace( '#<!--\s*(START|STOP): including template:.*?-->\n?#s', '', $html, -1, $count ), 'strip' );
        $removed['template_comments'] += $count;

        $field = preg_quote( self::TOKEN_FIELD, '#' );
        $html = self::checked( preg_replace( '#\s*<input\b(?=[^>]*\bname=(["\'])' . $field . '\1)[^>]*>#i', '', $html, -1, $count ), 'strip' );
        $removed['tokens'] += $count;
        $html = self::checked( preg_replace( '#\s*<meta\b(?=[^>]*\bname=(["\'])csrf-(param|token|token-x)\1)[^>]*>#i', '', $html, -1, $count ), 'strip' );
        $removed['tokens'] += $count;
        $html = self::checked( preg_replace( '#\s*<span\b(?=[^>]*\bid=(["\'])' . $field . '[a-z_]*_js\1)[^>]*>\s*</span>#i', '', $html, -1, $count ), 'strip' );
        $removed['tokens'] += $count;

        foreach ( $sessionNames as $name )
        {
            $name = preg_quote( (string)$name, '#' );
            $html = self::checked( preg_replace( '#([?&]|&amp;)' . $name . '[A-Za-z0-9_]*=[A-Za-z0-9,-]+(&amp;|&)?#', '$1', $html, -1, $count ), 'strip' );
            $removed['session_ids'] += $count;
        }
        // an address left with a bare ? or & at its end
        $html = self::checked( preg_replace( '#(href|src|action)=(["\'])([^"\']*?)(\?|&amp;|&)\2#i', '$1=$2$3$2', $html ), 'strip' );

        $html = self::checked( preg_replace( '#\s*<meta\b(?=[^>]*http-equiv=(["\'])Content-Security-Policy\1)(?=[^>]*nonce-)[^>]*>#i', '', $html, -1, $count ), 'strip' );
        $removed['csp'] += $count;
        $html = self::checked( preg_replace( '#\s+nonce=(["\'])[^"\']*\1#i', '', $html, -1, $count ), 'strip' );
        $removed['nonces'] += $count;

        return array( 'html' => (string)$html, 'removed' => $removed );
    }

    /** The marks of debug output in a page: the debug report, the debug bar, the template debug comments */
    const DEBUG_PATTERN = '#<div\s+id=(["\'])debug\1|id=(["\'])debug-end\2|<!--\s*START: including template|id=(["\'])exp-debug-summary\3|<!--/?exp-debug-bar-->|data-exp-debug-bar=#i';

    /**
     * The debug output a page came with, before strip(): what of it is in the html, and the response headers whose
     * name speaks of debugging (X-Debug-Token, X-Exp-Debug-...). A build that signed its requests
     * (expStaticBuildSignature) gets none from this server; when it still does, the page is refused, not stripped.
     *
     * @param string $html the page as the server sent it
     * @param string[] $headerNames the names of the response headers
     * @return string[] empty when there is none
     */
    public static function debugOutput( $html, array $headerNames = array() )
    {
        $found = array();
        if ( preg_match( self::DEBUG_PATTERN, (string)$html, $m ) )
            $found[] = trim( $m[0] );
        foreach ( $headerNames as $name )
            if ( stripos( (string)$name, 'debug' ) !== false )
                $found[] = 'the header ' . $name;
        return $found;
    }

    /**
     * Why a page cannot be published, after strip(); empty when it can.
     *
     * @param string $html the stripped page
     * @param array $cookies the names of the cookies the response set
     * @param array $sessionNames the names session cookies start with (default SESSION_NAMES)
     * @return string[]
     */
    public static function refusals( $html, array $cookies = array(), array $sessionNames = array() )
    {
        $sessionNames = $sessionNames ?: self::SESSION_NAMES;
        $reasons = array();
        foreach ( $cookies as $cookie )
            foreach ( $sessionNames as $name )
                if ( stripos( (string)$cookie, (string)$name ) === 0 )
                {
                    $reasons[] = 'the response set the session cookie ' . $cookie;
                    break;
                }
        if ( preg_match( self::DEBUG_PATTERN, $html ) )
            $reasons[] = 'debug output';
        $field = preg_quote( self::TOKEN_FIELD, '#' );
        if ( preg_match( '#<input\b[^>]*\bname=(["\'])' . $field . '\1|<meta\b[^>]*\bname=(["\'])csrf-token|\bid=(["\'])' . $field . '[a-z_]*_js\3[^>]*\btitle=#i', $html ) )
            $reasons[] = 'a form token';
        foreach ( $sessionNames as $name )
            if ( preg_match( '#[?&;]' . preg_quote( (string)$name, '#' ) . '[A-Za-z0-9_]*=#', $html ) )
            {
                $reasons[] = 'a session id in an address';
                break;
            }
        return $reasons;
    }

    /** The files precompress() writes a .gz (and .br) for */
    const COMPRESSIBLE = array( 'html', 'htm', 'css', 'js', 'mjs', 'json', 'xml', 'svg', 'txt', 'map', 'webmanifest' );

    /** The smallest file precompress() compresses */
    const COMPRESS_MIN_BYTES = 1024;

    /** The files fingerprint() renames */
    const FINGERPRINTED = array( 'css', 'js' );

    /**
     * PostProcess[]=minify: a page with less white space and no comments. Conservative: the content of <pre>,
     * <textarea>, <script> and <style> is left alone, so are conditional comments; runs of white space become one
     * (a line break when they held one), so inline text keeps its spaces.
     *
     * @param string $html
     * @return string
     */
    public static function minify( $html )
    {
        $html = str_replace( "\x00", '', (string)$html );
        $kept = array();
        $text = self::checked( preg_replace_callback( '#<(pre|textarea|script|style)\b[^>]*>.*?</\1\s*>|<!--\[if.*?<!\[endif\]-->#is', function ( $m ) use ( &$kept )
        {
            $kept[] = $m[0];
            return "\x00" . ( count( $kept ) - 1 ) . "\x00";
        }, $html ), 'minify' );
        $text = self::checked( preg_replace( '#<!--.*?-->#s', '', $text ), 'minify' );
        $text = self::checked( preg_replace_callback( '#\s{2,}#', function ( $m ) { return strpos( $m[0], "\n" ) !== false ? "\n" : ' '; }, $text ), 'minify' );
        // the placeholder's NUL is written as a PCRE escape: a NUL byte inside a pattern is an error before PHP 8.2
        return self::checked( preg_replace_callback( '#\x00(\d+)\x00#', function ( $m ) use ( $kept ) { return $kept[(int)$m[1]]; }, $text ), 'minify' );
    }

    /**
     * The result of a preg_replace*() call, or an exception when PCRE failed (null: a pattern the PCRE of this PHP
     * rejects, the backtrack or recursion limit, malformed UTF-8). A failure must stop the build, never turn a page
     * into an empty file.
     *
     * @param string|null $result
     * @param string $what the step, for the message
     * @return string
     * @throws RuntimeException
     */
    public static function checked( $result, $what )
    {
        if ( $result === null )
            throw new RuntimeException( 'Static publishing: the step ' . $what . ' failed in PCRE: ' . preg_last_error_msg() );
        return (string)$result;
    }

    /**
     * PostProcess[]=precompress: the .gz (and, when PHP has the brotli extension, the .br) of a file, for hosts that
     * serve precompressed files (GitHub Pages compresses by itself; others, nginx's gzip_static, do not). Pure.
     *
     * @param string $path the file in the build
     * @param string $content
     * @return array file => content (empty for a file that is not compressed)
     */
    public static function precompress( $path, $content )
    {
        $extension = strtolower( (string)pathinfo( (string)$path, PATHINFO_EXTENSION ) );
        if ( !in_array( $extension, self::COMPRESSIBLE, true ) || strlen( (string)$content ) < self::COMPRESS_MIN_BYTES )
            return array();
        $out = array();
        $gz = gzencode( (string)$content, 9 );
        if ( $gz !== false && strlen( $gz ) < strlen( (string)$content ) )
            $out[$path . '.gz'] = $gz;
        if ( function_exists( 'brotli_compress' ) )
        {
            $br = brotli_compress( (string)$content, 11 );
            if ( is_string( $br ) && strlen( $br ) < strlen( (string)$content ) )
                $out[$path . '.br'] = $br;
        }
        return $out;
    }

    /**
     * PostProcess[]=fingerprint: the name of a style sheet or script with the hash of its content (site.css becomes
     * site.3f2a9c1b.css), so a host may let browsers keep it forever; never a version query. Pure.
     *
     * @param string $path
     * @param string $content
     * @return string|null null for a file that is not fingerprinted
     */
    public static function fingerprintName( $path, $content )
    {
        $path = (string)$path;
        $extension = strtolower( (string)pathinfo( $path, PATHINFO_EXTENSION ) );
        if ( !in_array( $extension, self::FINGERPRINTED, true ) )
            return null;
        return substr( $path, 0, -strlen( $extension ) - 1 ) . '.' . substr( hash( 'sha256', (string)$content ), 0, 8 ) . '.' . $extension;
    }

    /**
     * The references of a page or style sheet to fingerprinted files, renamed. A reference is renamed only when it
     * resolves (relative to the document, below the base path, or below the public address) to a file of the map.
     * Pure.
     *
     * @param string $text the page or style sheet
     * @param string $fromFile its file in the build
     * @param array $map old file => new file (both relative to the build's root)
     * @param string $basePath /repo/ or /
     * @param string $publicUrl https://owner.github.io/repo/ or ''
     * @return string
     */
    public static function renameReferences( $text, $fromFile, array $map, $basePath = '/', $publicUrl = '' )
    {
        if ( !$map )
            return (string)$text;
        $basenames = array();
        foreach ( array_keys( $map ) as $old )
            $basenames[basename( (string)$old )] = true;
        $fromDir = strpos( (string)$fromFile, '/' ) === false ? '' : substr( (string)$fromFile, 0, strrpos( (string)$fromFile, '/' ) );
        $basePath = '/' . trim( (string)$basePath, '/' ) . '/';
        $basePath = $basePath === '//' ? '/' : $basePath;
        $publicUrl = rtrim( (string)$publicUrl, '/' ) . '/';
        return self::checked( preg_replace_callback( '#(["\'(=,\s])([^"\'()\s<>,]*?)([^"\'()\s<>,/]+\.(?:css|js))(?=[?\#"\')\s>,])#i',
            function ( $m ) use ( $basenames, $map, $fromDir, $basePath, $publicUrl )
            {
                if ( !isset( $basenames[$m[3]] ) )
                    return $m[0];
                $ref = $m[2] . $m[3];
                if ( $publicUrl !== '/' && stripos( $ref, $publicUrl ) === 0 )
                    $path = substr( $ref, strlen( $publicUrl ) );
                else if ( preg_match( '#^[a-z][a-z0-9+.-]*:|^//#i', $ref ) )
                    return $m[0];
                else if ( $ref[0] === '/' )
                {
                    if ( strpos( $ref, $basePath ) !== 0 )
                        return $m[0];
                    $path = substr( $ref, strlen( $basePath ) );
                }
                else
                    $path = ltrim( expStaticLinkRewriter::removeDotSegments( '/' . ( $fromDir !== '' ? $fromDir . '/' : '' ) . $ref ), '/' );
                $path = rawurldecode( ltrim( $path, '/' ) );
                if ( !isset( $map[$path] ) )
                    return $m[0];
                return $m[1] . $m[2] . basename( (string)$map[$path] );
            }, (string)$text ), 'renameReferences' );
    }

    /**
     * The names of the cookies in a response's header lines (Set-Cookie).
     *
     * @param string[] $headerLines
     * @return string[]
     */
    public static function cookieNames( array $headerLines )
    {
        $names = array();
        foreach ( $headerLines as $line )
            if ( preg_match( '#^set-cookie:\s*([^=;\s]+)=#i', trim( (string)$line ), $m ) )
                $names[] = $m[1];
        return array_values( array_unique( $names ) );
    }
}
