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
 * The system report is made to be pasted into a support request, so everything in it is read as public. The rules:
 *
 *  - A value whose name says it is a secret (password, secret, token, key, salt, credentials, session, cookie,
 *    authorization, DSN) is never shown: only whether it is set.
 *  - Credentials inside an address ("mysql://user:password@host/db", "https://user:pass@host") are cut out, and
 *    "password=..." style pairs in free text lose their value.
 *  - A long run of hexadecimal or base64 characters (a session id, a token, a key) is replaced.
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
     * Name fragments that make a value secret. Matched against the key in any case, with "_", "-" and "." ignored.
     */
    const SECRET_NAME = '/(pass(word|wd|phrase)?|pwd|secret|token|apikey|privatekey|salt|credential|session(id)?|cookie|authorization|auth(user|key)|dsn|signature|clientsecret|certificatekey|^key$|keyfile$)/i';

    /**
     * Whether a setting or field of this name holds a secret.
     *
     * @param string $name
     * @return bool
     */
    public static function isSecretName( $name )
    {
        $plain = preg_replace( '/[\s_.\-\[\]]+/', '', (string)$name );
        return $plain !== '' && preg_match( self::SECRET_NAME, $plain ) === 1;
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
     * An address without the credentials in it: "mysql://user:pw@host/db" becomes "mysql://***@host/db".
     *
     * @param string $text
     * @return string
     */
    public static function dsn( $text )
    {
        return preg_replace( '#\b([a-z][a-z0-9+.\-]*://)(?:[^\s/@:"\'<>]+(?::[^\s/@"\'<>]*)?|:[^\s/@"\'<>]+)@#i', '$1' . self::HIDDEN . '@', (string)$text );
    }

    /**
     * Free text with every secret cut out: credentials in addresses, the values of "password=..." style pairs
     * (also "password: ...", and inside query strings) and long hexadecimal or base64 runs.
     *
     * @param string $text
     * @return string
     */
    public static function text( $text )
    {
        $text = self::dsn( (string)$text );
        // name=value, name: value, "name" => "value"
        $text = preg_replace(
            '/\b([A-Za-z_.\-]*(?:pass(?:word|wd|phrase)?|pwd|secret|token|api[_\-]?key|private[_\-]?key|salt|session(?:_?id)?|sid|auth)[A-Za-z_.\-]*)([\'"]?\s*(?:=>|=|:)\s*)("[^"]*"|\'[^\']*\'|[^\s&;,]+)/i',
            '$1$2' . self::HIDDEN, $text );
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
