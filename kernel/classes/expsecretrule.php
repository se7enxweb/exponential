<?php
/**
 * File containing the expSecretRule class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The one rule of what is a secret, shared by everything that shows or records settings and values:
 * exp:ini and expIniEditor, the audit log (through expIniEditor::isSecret()), the settings pages
 * (expSettingsSecretRule) and Setup > System information with exp:system:info (expSystemReportMask).
 *
 * Each of them keeps its own public API and its own mask text; what counts as a secret is decided here, so they
 * cannot disagree. The callers may add to it (the settings pages' MaskedNameList[], the report's request
 * names), never take away from it.
 *
 *  - isSecretName(): a name holds a secret when it contains password, passwd, passphrase, secret, token, salt,
 *    credential, privatekey or apikey (any case), ends in pwd or dsn, is "key", or ends in Key, _key or -key
 *    (ApiKey, LicenseKey, license_key) - except well-known names that are not secrets (SortKey, CacheKey,
 *    PrimaryKey ...). A name only starting with Key (KeyField, KeywordList) is not a secret.
 *  - maskInline(): a value of any name can carry a secret inside it: the password of user:password@ in an
 *    address (with or without a scheme) and the value of password=..., token: ..., 'secret' => '...' pairs.
 *    Only that part is replaced; the rest of the value stays readable.
 *
 * Tests: tests/tests/kernel/classes/expSecretRuleTest.php, and those of every caller.
 */
class expSecretRule
{
    /** Name fragments that make a value secret, anywhere in the name, any case */
    protected static $words = array( 'password', 'passwd', 'passphrase', 'secret', 'token', 'salt', 'credential',
                                     'privatekey', 'apikey' );

    /** Names that end in Key and are not secrets (lower case) */
    protected static $notSecret = array( 'sortkey', 'cachekey', 'primarykey', 'foreignkey', 'indexkey', 'shortcutkey',
                                         'hotkey', 'groupkey', 'sectionkey', 'languagekey', 'idkey' );

    /**
     * Whether a name marks its value as a secret.
     *
     * @param string $name 'Password', 'TransportPassword', 'license_key', 'DatabaseDsn' ...
     * @return bool
     */
    public static function isSecretName( $name )
    {
        $name = (string)$name;
        if ( substr( $name, -2 ) === '[]' )
            $name = substr( $name, 0, -2 );
        if ( $name === '' )
            return false;
        $lower = strtolower( $name );
        foreach ( self::$words as $word )
        {
            if ( strpos( $lower, $word ) !== false )
                return true;
        }
        // DbPwd, db_pwd; DSN, DatabaseDsn, cache_dsn
        if ( preg_match( '#(pwd|dsn)$#', $lower ) )
            return true;
        if ( in_array( $lower, self::$notSecret, true ) )
            return false;
        if ( $lower === 'key' )
            return true;
        // ApiKey, LicenseKey, license_key, LICENSE-KEY; not KeyField, Keywords, MonkeyList
        return (bool)preg_match( '#[a-z0-9]Key$#', $name ) || (bool)preg_match( '#[_-]key$#i', $name );
    }

    /**
     * A value with the secrets inside it replaced by $mask, the rest kept.
     *
     * @param string $value
     * @param string $mask what a secret is shown as
     * @return string
     */
    public static function maskInline( $value, $mask = '***' )
    {
        if ( !is_string( $value ) || $value === '' )
            return $value;
        $mask = (string)$mask;
        return self::walkInline( $value, function () use ( $mask ) { return $mask; } );
    }

    /**
     * The secrets inside a value, as maskInline() finds them: one entry per secret, with the secret as it stands
     * ('secret', quotes included for a quoted value) and the user name of an address ('user', null elsewhere). A
     * caller can then tell a real secret from an example such as user:pass@ (eZExtensionPackageHandler).
     *
     * @param string $value
     * @return array[] array( 'secret' => string, 'user' => string|null ), in the order found
     */
    public static function inlineSecretParts( $value )
    {
        $parts = array();
        if ( !is_string( $value ) || $value === '' )
            return $parts;
        self::walkInline( $value, function ( $secret, $user ) use ( &$parts )
        {
            $parts[] = array( 'secret' => $secret, 'user' => $user );
            return $secret;
        } );
        return $parts;
    }

    /**
     * $value with every inline secret replaced by $replace( secret, user ): the one place the inline patterns live.
     *
     * @param string $value
     * @param callable $replace
     * @return string|null null when a pattern failed (a PCRE error): the callers read that as a secret
     */
    private static function walkInline( $value, $replace )
    {
        // scheme://user:password@host, also without a user (scheme://:password@host)
        $value = preg_replace_callback( '#(\b[a-z][a-z0-9+.\-]*://([^\s:/@"\'<>]*):)([^\s@/"\'<>]+)@#i',
            function ( $m ) use ( $replace ) { return $m[1] . $replace( $m[3], $m[2] ) . '@'; }, $value );
        if ( $value === null )
            return null;
        // user:password@host without a scheme, when that is the whole value
        $value = preg_replace_callback( '#^(([\w.%+\-]+):)(?!//)([^\s@/]+)(@[\w.\-]+(:\d+)?(/\S*)?)$#',
            function ( $m ) use ( $replace ) { return $m[1] . $replace( $m[3], $m[2] ) . $m[4]; }, $value );
        if ( $value === null )
            return null;
        // password=..., db_password: ..., 'secret' => '...', ?token=...: the value up to ; & , or white space
        $value = preg_replace_callback(
            '/(\b(?:[A-Za-z0-9]*[_.\-]?(?:password|passwd|passphrase|secret|token|apikey|api_key|api-key)[A-Za-z0-9_.\-]*|(?:pwd|pass|salt)(?![A-Za-z0-9]))[\'"]?\s*(?:=>|=|:(?=\s)))(\s*)("[^"]*"|\'[^\']*\'|[^\s&;,]+)/i',
            function ( $m ) use ( $replace ) { return $m[1] . $m[2] . $replace( $m[3], null ); }, $value );
        return $value;
    }

    /**
     * Whether a value carries a secret that maskInline() replaces.
     *
     * @param string $value
     * @return bool
     */
    public static function hasInlineSecret( $value )
    {
        return is_string( $value ) && $value !== '' && self::maskInline( $value, "\0" ) !== $value;
    }
}
