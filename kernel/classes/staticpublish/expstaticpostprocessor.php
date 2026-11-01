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
        $html = preg_replace( '#<!--\s*(START|STOP): including template:.*?-->\n?#s', '', $html, -1, $count );
        $removed['template_comments'] += $count;

        $field = preg_quote( self::TOKEN_FIELD, '#' );
        $html = preg_replace( '#\s*<input\b(?=[^>]*\bname=(["\'])' . $field . '\1)[^>]*>#i', '', $html, -1, $count );
        $removed['tokens'] += $count;
        $html = preg_replace( '#\s*<meta\b(?=[^>]*\bname=(["\'])csrf-(param|token|token-x)\1)[^>]*>#i', '', $html, -1, $count );
        $removed['tokens'] += $count;
        $html = preg_replace( '#\s*<span\b(?=[^>]*\bid=(["\'])' . $field . '[a-z_]*_js\1)[^>]*>\s*</span>#i', '', $html, -1, $count );
        $removed['tokens'] += $count;

        foreach ( $sessionNames as $name )
        {
            $name = preg_quote( (string)$name, '#' );
            $html = preg_replace( '#([?&]|&amp;)' . $name . '[A-Za-z0-9_]*=[A-Za-z0-9,-]+(&amp;|&)?#', '$1', $html, -1, $count );
            $removed['session_ids'] += $count;
        }
        // an address left with a bare ? or & at its end
        $html = preg_replace( '#(href|src|action)=(["\'])([^"\']*?)(\?|&amp;|&)\2#i', '$1=$2$3$2', $html );

        $html = preg_replace( '#\s*<meta\b(?=[^>]*http-equiv=(["\'])Content-Security-Policy\1)(?=[^>]*nonce-)[^>]*>#i', '', $html, -1, $count );
        $removed['csp'] += $count;
        $html = preg_replace( '#\s+nonce=(["\'])[^"\']*\1#i', '', $html, -1, $count );
        $removed['nonces'] += $count;

        return array( 'html' => (string)$html, 'removed' => $removed );
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
        if ( preg_match( '#<div\s+id=(["\'])debug\1|id=(["\'])debug-end\2|<!--\s*START: including template|id=(["\'])exp-debug-summary\3|<!--/?exp-debug-bar-->|data-exp-debug-bar=#i', $html ) )
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
