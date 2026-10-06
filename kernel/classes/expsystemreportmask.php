<?php
/**
 * File containing the expSystemReportMask class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * What Setup > System information, its text report and exp:system:info may show of a value.
 *
 * The system report is made to be pasted into a support request, so everything in it is read as public. What is a
 * secret is decided by the shared rule, expSecretRule (also that of exp:ini, the audit log and the settings
 * pages); this class adds what only a report about a request needs, and the shape of its output:
 *
 *  - A value whose name is a secret (expSecretRule::isSecretName(), plus a session id, a cookie, an
 *    Authorization or a Signature value) is never shown: only whether it is set.
 *  - Secrets inside a value are replaced by expSecretRule::maskInline(): the password of user:password@ in an
 *    address, the values of "password=..." style pairs; here also session ids and cookies given as pairs.
 *  - A long run of letters and digits with a digit in it (a session id, a token, a key) is replaced.
 *  - Paths inside the installation are shown relative to it ("var/site/cache"); the installation root itself is
 *    ".", and a path under a web or home directory outside it keeps only its last two parts. System paths such as
 *    /etc/vc or /usr/bin/php are kept: they are the same on every machine and say where to look.
 *
 * Every method is static and pure, so the rules are tested without a database (tests/tests/kernel/classes/
 * expSystemReportMaskTest.php).
 */
class expSystemReportMask
{
    const HIDDEN = '***';

    /**
     * Names only a request carries whose values are secrets, on top of expSecretRule: whole names, any case, with
     * "_", "-" and "." ignored. (Settings such as SessionTimeout or AuthorizationURL are not secrets.)
     */
    const REQUEST_SECRET_NAME = '/^(session(id)?|sessid|sid|ezsessid|phpsessid|cookies?|authorization|proxyauthorization|signature|csrftoken|formtoken)$/i';

    /**
     * Whether a setting or field of this name holds a secret.
     *
     * @param string $name
     * @return bool
     */
    public static function isSecretName( $name )
    {
        $plain = preg_replace( '/[\s_.\-\[\]]+/', '', (string)$name );
        if ( $plain === '' )
            return false;
        return expSecretRule::isSecretName( (string)$name ) || preg_match( self::REQUEST_SECRET_NAME, $plain ) === 1;
    }

    /**
     * A secret value as it may be shown: whether it is set, never what it is.
     *
     * @param mixed $value
     * @return string
     */
    public static function secret( $value )
    {
        return ( $value === null || $value === false || $value === '' || $value === array() ) ? '(not set)' : '(set, hidden)';
    }

    /**
     * An address without the password in it: "mysql://user:pw@host/db" becomes "mysql://user:***@host/db".
     *
     * @param string $text
     * @return string
     */
    public static function dsn( $text )
    {
        return expSecretRule::maskInline( (string)$text, self::HIDDEN );
    }

    /**
     * Free text with every secret cut out: passwords in addresses, the values of "password=..." style pairs
     * (also "password: ...", and inside query strings), session ids and cookies given as pairs, and long runs of
     * letters and digits.
     *
     * @param string $text
     * @return string
     */
    public static function text( $text )
    {
        $text = expSecretRule::maskInline( (string)$text, self::HIDDEN );
        // a session id or cookie given as a pair: eZSESSID=..., sid=..., Cookie: ...
        $text = preg_replace( '/\b((?:[A-Za-z]*sess(?:ion)?_?id|sid|cookie)[\'"]?\s*(?:=>|=|:(?=\s)))(\s*)("[^"]*"|\'[^\']*\'|[^\s&;,]+)/i',
                              '${1}${2}' . self::HIDDEN, $text );
        // a run of 26 or more letters and digits with a digit in it is a key, a token or a session id, never a fact
        // (names such as explayouts_content_browser_core have no digits and are split by underscores)
        $text = preg_replace_callback( '/(?<![A-Za-z0-9])[A-Za-z0-9]{26,}={0,2}(?![A-Za-z0-9])/', function ( $m ) {
            return preg_match( '/\d/', $m[0] ) && preg_match( '/[A-Za-z]/', $m[0] ) ? expSystemReportMask::HIDDEN : $m[0];
        }, $text );
        return $text;
    }

    /**
     * A path as it may be shown.
     *
     * @param string $path
     * @param string $root the installation root (absolute, without a trailing slash)
     * @return string
     */
    public static function path( $path, $root )
    {
        $path = (string)$path;
        $root = rtrim( (string)$root, '/' );
        if ( $path === '' )
            return '';
        if ( $root !== '' )
        {
            if ( $path === $root )
                return '.';
            if ( strpos( $path, $root . '/' ) === 0 )
                return substr( $path, strlen( $root ) + 1 );
        }
        if ( preg_match( '#^/(?:var/www|home|srv|web|root)(?:/|$)#', $path ) )
        {
            $parts = array_values( array_filter( explode( '/', $path ), 'strlen' ) );
            return count( $parts ) > 2 ? '…/' . implode( '/', array_slice( $parts, -2 ) ) : '…/' . implode( '/', $parts );
        }
        return $path;
    }

    /**
     * Every path in free text as path() shows it, then text().
     *
     * @param string $text
     * @param string $root
     * @return string
     */
    public static function paths( $text, $root )
    {
        $text = (string)$text;
        $root = rtrim( (string)$root, '/' );
        if ( $root !== '' )
        {
            $text = str_replace( $root . '/', '', $text );
            $text = preg_replace( '#' . preg_quote( $root, '#' ) . '(?![\w.\-])#', '.', $text );
        }
        $text = preg_replace_callback( '#/(?:var/www|home|srv|web|root)/[^\s\'"<>,;)]*#', function ( $m ) {
            return expSystemReportMask::path( $m[0], '' );
        }, $text );
        return self::text( $text );
    }

    /**
     * A whole report: every value of a secret name is hidden, every string has its paths and secrets masked.
     * Keys stay as they are.
     *
     * @param mixed $value
     * @param string $root
     * @param string|int $name the key the value was found under
     * @return mixed
     */
    public static function report( $value, $root, $name = '' )
    {
        if ( is_string( $name ) && $name !== '' && self::isSecretName( $name ) )
            return self::secret( $value );
        if ( is_array( $value ) )
        {
            $masked = array();
            foreach ( $value as $key => $item )
                $masked[$key] = self::report( $item, $root, $key );
            return $masked;
        }
        if ( is_string( $value ) )
            return self::paths( $value, $root );
        return $value;
    }
}
