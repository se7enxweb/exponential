<?php
/**
 * File containing the expSettingsSecretRule class: which settings hold secrets, and how their values are shown.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The rule the settings pages (settings/view, settings/edit) mask values by.
 *
 * A setting is a secret when its name
 *  - is one expIniEditor::isSecret() recognises (the rule of exp:ini and of the audit log; it cannot be switched
 *    off here, so the page never shows more than the command line does), or
 *  - matches an entry of site.ini [SettingsViewSettings] MaskedNameList[] and none of UnmaskedNameList[].
 *    An entry without '*' matches anywhere in the name, ignoring case ("password" matches TransportPassword);
 *    an entry with '*' is a pattern for the whole name and does respect case ("*Key" matches LicenseKey, not
 *    Monkey).
 *
 * The value of a secret is never shown: maskValue() says whether it is set or empty, and, when
 * MaskRevealLastCharacters is above 0, the last characters of a value at least four times that long.
 *
 * Any value, of any setting, can also carry a password inside it: a URL or DSN with user:password@ in it
 * (mysql://user:password@host/db) or a connection string with password=...; maskInline() replaces just that part.
 *
 * A secret's value is also left out of searching (matchesSearch()): a search that finds a setting by a
 * fragment of its value would reveal the value one guess at a time.
 */
class expSettingsSecretRule
{
    /** What a masked value is shown as */
    const MASK = '••••••••';

    /** The entries used when site.ini has no MaskedNameList */
    protected static $defaultKeys = array( 'password', 'passwd', 'passphrase', 'secret', 'token', 'salt', 'credential',
                                           'privatekey', 'apikey', 'Key', '*Key', '*_key', '*_KEY', '*-key', 'DSN', '*Dsn', '*DSN' );

    /** The entries used when site.ini has no UnmaskedNameList */
    protected static $defaultNotSecret = array( 'SortKey', 'CacheKey', 'PrimaryKey', 'ForeignKey', 'IndexKey',
                                                'ShortcutKey', 'HotKey', 'GroupKey', 'SectionKey', 'LanguageKey',
                                                'IdKey' );

    /** @var string[] */
    protected $keys;

    /** @var string[] */
    protected $notSecret;

    /** @var int */
    protected $revealLast;

    /**
     * @param string[]|null $keys MaskedNameList entries; null: the built-in list
     * @param string[]|null $notSecret UnmaskedNameList entries; null: the built-in list
     * @param int $revealLast How many trailing characters of a long secret to show (0: none)
     */
    public function __construct( ?array $keys = null, ?array $notSecret = null, $revealLast = 0 )
    {
        $this->keys = self::cleanList( $keys !== null ? $keys : self::$defaultKeys );
        $this->notSecret = self::cleanList( $notSecret !== null ? $notSecret : self::$defaultNotSecret );
        $this->revealLast = max( 0, min( 4, (int)$revealLast ) );
    }

    /**
     * The rule as site.ini [SettingsViewSettings] configures it.
     *
     * @param eZINI|null $ini
     * @return expSettingsSecretRule
     */
    public static function fromIni( $ini = null )
    {
        $ini = $ini ?: eZINI::instance();
        $block = 'SettingsViewSettings';
        $keys = $ini->hasVariable( $block, 'MaskedNameList' ) ? (array)$ini->variable( $block, 'MaskedNameList' ) : null;
        $not = $ini->hasVariable( $block, 'UnmaskedNameList' ) ? (array)$ini->variable( $block, 'UnmaskedNameList' ) : null;
        $reveal = $ini->hasVariable( $block, 'MaskRevealLastCharacters' ) ? (int)$ini->variable( $block, 'MaskRevealLastCharacters' ) : 0;
        return new self( $keys ?: null, $not, $reveal );
    }

    protected static function cleanList( array $list )
    {
        $clean = array();
        foreach ( $list as $item )
        {
            $item = trim( (string)$item );
            if ( $item !== '' && !in_array( $item, $clean, true ) )
                $clean[] = $item;
        }
        return $clean;
    }

    /**
     * Whether a setting's name marks its value as a secret.
     *
     * @param string $name 'Password', 'TransportPassword', 'Scripts[]' ...
     * @return bool
     */
    public function isSecretName( $name )
    {
        $name = (string)$name;
        if ( substr( $name, -2 ) === '[]' )
            $name = substr( $name, 0, -2 );
        if ( $name === '' )
            return false;
        if ( class_exists( 'expIniEditor' ) && expIniEditor::isSecret( $name ) )
            return true;
        foreach ( $this->notSecret as $entry )
        {
            if ( self::entryMatches( $entry, $name, true ) )
                return false;
        }
        foreach ( $this->keys as $entry )
        {
            if ( self::entryMatches( $entry, $name, false ) )
                return true;
        }
        return false;
    }

    /**
     * @param string $entry A list entry: a word (found anywhere, any case) or a '*' pattern (the whole name, case kept)
     * @param string $name
     * @param bool $whole A word has to be the whole name (UnmaskedNameList: SortKey exempts SortKey, not MySortKeyX)
     * @return bool
     */
    protected static function entryMatches( $entry, $name, $whole )
    {
        if ( strpos( $entry, '*' ) !== false )
            return fnmatch( $entry, $name );
        // a capitalised single word ("Key") is the whole name; a lower-case word is a part of it
        if ( $whole || ctype_upper( $entry[0] ) )
            return strcasecmp( $entry, $name ) === 0;
        return stripos( $name, $entry ) !== false;
    }

    /**
     * The value of a secret as it may be shown: set (with the last characters when configured) or empty.
     *
     * @param string|array|null $value
     * @return array state (set, empty, array), text, count (elements of an array)
     */
    public function maskValue( $value )
    {
        if ( is_array( $value ) )
        {
            $set = 0;
            foreach ( $value as $v )
            {
                if ( !is_array( $v ) && (string)$v !== '' )
                    ++$set;
            }
            return array( 'state' => 'array', 'text' => self::MASK, 'count' => count( $value ), 'set' => $set );
        }
        $value = (string)$value;
        if ( $value === '' )
            return array( 'state' => 'empty', 'text' => '', 'count' => 0, 'set' => 0 );
        $text = self::MASK;
        $length = function_exists( 'mb_strlen' ) ? mb_strlen( $value, 'UTF-8' ) : strlen( $value );
        if ( $this->revealLast > 0 && $length >= 4 * $this->revealLast )
            $text .= function_exists( 'mb_substr' ) ? mb_substr( $value, -$this->revealLast, null, 'UTF-8' ) : substr( $value, -$this->revealLast );
        return array( 'state' => 'set', 'text' => $text, 'count' => 1, 'set' => 1 );
    }

    /**
     * A value of any setting with an embedded password replaced: the password of user:password@ in a URL or DSN,
     * and the value of password=, pwd=, pass=, secret=, token=, apikey= in a connection or query string.
     *
     * @param string $value
     * @return string
     */
    public static function maskInline( $value )
    {
        if ( !is_string( $value ) || $value === '' )
            return $value;
        // scheme://user:password@host (also without a user: scheme://:password@host)
        $value = preg_replace( '#(\b[a-z][a-z0-9+.\-]*://[^\s:/@]*:)[^\s@/]+@#i', '$1' . self::MASK . '@', $value );
        // user:password@host without a scheme, when that is the whole value
        $value = preg_replace( '#^([\w.%+\-]+:)(?!//)[^\s@/]+(@[\w.\-]+(:\d+)?(/\S*)?)$#', '$1' . self::MASK . '$2', $value );
        // password=..., pwd=... up to ; & , or white space
        $value = preg_replace( '#(\b(?:password|passwd|pwd|pass|secret|token|apikey|api_key)\s*=\s*)[^;&,\s]+#i', '$1' . self::MASK, $value );
        return $value;
    }

    /**
     * Whether a value holds an embedded password that maskInline() hides.
     *
     * @param string $value
     * @return bool
     */
    public static function hasInlineSecret( $value )
    {
        return is_string( $value ) && $value !== '' && self::maskInline( $value ) !== $value;
    }

    /**
     * A value of a setting as the page may show it: masked entirely for a secret, else with embedded passwords
     * masked; arrays element by element (their keys are kept).
     *
     * @param string $name
     * @param string|array|null $value
     * @return string|array|null
     */
    public function displayValue( $name, $value )
    {
        if ( $this->isSecretName( $name ) )
        {
            if ( is_array( $value ) )
            {
                $masked = array();
                foreach ( $value as $k => $v )
                    $masked[$k] = (string)$v === '' ? '' : self::MASK;
                return $masked;
            }
            $m = $this->maskValue( $value );
            return $m['text'];
        }
        if ( is_array( $value ) )
        {
            $shown = array();
            foreach ( $value as $k => $v )
                $shown[$k] = is_array( $v ) ? $v : self::maskInline( (string)$v );
            return $shown;
        }
        return $value === null ? null : self::maskInline( (string)$value );
    }

    /**
     * Whether a setting matches a search: the block, the name, or - unless the setting is a secret or the value
     * holds an embedded password - the value (any element of an array, and its keys). Case is ignored.
     *
     * @param string $query
     * @param string $block
     * @param string $name
     * @param string|array|null $value
     * @return bool
     */
    public function matchesSearch( $query, $block, $name, $value )
    {
        $query = trim( (string)$query );
        if ( $query === '' )
            return true;
        if ( stripos( (string)$block, $query ) !== false || stripos( (string)$name, $query ) !== false )
            return true;
        if ( $this->isSecretName( $name ) )
            return false;
        foreach ( is_array( $value ) ? $value : array( $value ) as $k => $v )
        {
            if ( is_array( $v ) )
                continue;
            if ( is_string( $k ) && stripos( $k, $query ) !== false )
                return true;
            // search the value as it is shown, so an embedded password is never found by its characters
            if ( stripos( self::maskInline( (string)$v ), $query ) !== false )
                return true;
        }
        return false;
    }
}
