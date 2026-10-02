<?php
/**
 * The privacy rules of audit records (doc/bc/6.0/audit.md, "Fields", "Never recorded"; settings
 * [AuditPrivacySettings] and [AuditRecordSettings] BeforeAfter, MaxValueLength).
 *
 * Per field: full (as is), truncate (a shorter form per field), hash ("h:" + the first 16 hex digits of
 * HMAC-SHA-256 with the installation's pseudonym key), off (left out). The session can only be hashed or off.
 * Whatever the settings say, the values of NeverRecord[] keys and of names expIniEditor::isSecret() recognises
 * never reach a record, and the path parameters of SecretPathViews[] are never written.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditPrivacy
{
    const SECRET = '[secret]';

    /** @var array */
    protected $config;

    /** @var expAuditKeys|null */
    protected $keys;

    /**
     * @param array $config expAuditConfig::get()
     * @param expAuditKeys|null $keys for the hash option (without keys, hash falls back to off)
     */
    public function __construct( array $config, ?expAuditKeys $keys = null )
    {
        $this->config = $config;
        $this->keys = $keys;
    }

    /**
     * The option of a field.
     *
     * @param string $field actor.ip, email, ...
     * @param string $default
     * @return string full | truncate | hash | off
     */
    public function option( $field, $default = 'full' )
    {
        $o = isset( $this->config['privacy'][$field] ) ? strtolower( $this->config['privacy'][$field] ) : $default;
        if ( !in_array( $o, array( 'full', 'truncate', 'hash', 'off' ), true ) )
            $o = $default;
        if ( $field === 'actor.session' && $o !== 'off' )
            $o = 'hash';
        return $o;
    }

    /**
     * Applies the rules to a record (in place).
     *
     * @param array $record
     * @return array
     */
    public function apply( array $record )
    {
        if ( isset( $record['actor'] ) && is_array( $record['actor'] ) )
            $record['actor'] = $this->actor( $record['actor'] );
        if ( isset( $record['request'] ) && is_array( $record['request'] ) )
        {
            if ( isset( $record['request']['url'] ) )
                $this->field( $record['request'], 'url', 'request.url', array( $this, 'truncateUrl' ) );
            if ( isset( $record['request']['host'] ) )
                $this->field( $record['request'], 'host', 'request.host', null );
        }
        foreach ( array( 'object', 'target' ) as $part )
        {
            if ( isset( $record[$part] ) && is_array( $record[$part] ) )
            {
                $record[$part] = $this->scrub( $record[$part] );
                if ( isset( $record[$part]['name'] ) )
                    $this->field( $record[$part], 'name', 'object.name', function ( $v ) { return expAuditPrivacy::cut( $v, 64 ); } );
                $record[$part] = $this->personFields( $record[$part] );
                $record[$part] = $this->limit( $record[$part] );
            }
        }
        $beforeAfter = isset( $this->config['beforeAfter'] ) ? $this->config['beforeAfter'] : 'enabled';
        foreach ( array( 'before', 'after' ) as $part )
        {
            if ( !isset( $record[$part] ) || !is_array( $record[$part] ) )
                continue;
            if ( $beforeAfter === 'disabled' )
            {
                unset( $record[$part] );
                continue;
            }
            $record[$part] = $this->personFields( $this->scrub( $record[$part] ) );
            $record[$part] = $beforeAfter === 'keys' ? $this->keysOnly( $record[$part] ) : $this->limit( $record[$part] );
        }
        if ( isset( $record['x'] ) && is_array( $record['x'] ) )
            $record['x'] = $this->limit( $this->scrub( $record['x'] ) );
        return $record;
    }

    /**
     * @param array $actor
     * @return array
     */
    protected function actor( array $actor )
    {
        if ( isset( $actor['login'] ) )
            $this->field( $actor, 'login', 'actor.login', null );
        if ( isset( $actor['ip'] ) )
            $this->field( $actor, 'ip', 'actor.ip', array( $this, 'truncateIp' ) );
        if ( isset( $actor['ua'] ) )
            $this->field( $actor, 'ua', 'actor.ua', array( __CLASS__, 'truncateUserAgent' ) );
        if ( isset( $actor['session'] ) )
            $this->field( $actor, 'session', 'actor.session', null );
        if ( isset( $actor['cli'] ) && is_array( $actor['cli'] ) && isset( $actor['cli']['os_user'] ) )
            $this->field( $actor['cli'], 'os_user', 'actor.cli.os_user', null );
        if ( isset( $actor['impersonator'] ) && is_array( $actor['impersonator'] ) && isset( $actor['impersonator']['login'] ) )
            $this->field( $actor['impersonator'], 'login', 'actor.login', null );
        return $actor;
    }

    /**
     * Applies one field's option.
     *
     * @param array $container
     * @param string $key
     * @param string $field the privacy field name
     * @param callable|null $truncate
     */
    protected function field( array &$container, $key, $field, $truncate )
    {
        $value = $container[$key];
        if ( $value === null || $value === '' )
            return;
        switch ( $this->option( $field, $field === 'actor.ip' || $field === 'request.url' || $field === 'actor.ua' ? 'truncate' : 'full' ) )
        {
            case 'off':
                unset( $container[$key] );
                return;
            case 'hash':
                $h = $this->hash( (string)$value );
                if ( $h === null )
                    unset( $container[$key] );
                else
                    $container[$key] = $h;
                return;
            case 'truncate':
                if ( $truncate )
                    $container[$key] = call_user_func( $truncate, (string)$value );
                elseif ( $field !== 'request.host' && $field !== 'actor.login' && $field !== 'actor.cli.os_user' )
                    $container[$key] = (string)$value;
                else
                {
                    $h = $this->hash( (string)$value );
                    $container[$key] = $h === null ? '' : $h;
                }
                return;
            default:
                if ( $field === 'request.url' )
                    $container[$key] = $this->secretPath( (string)$value );
        }
    }

    /**
     * "h:" + 16 hex digits of HMAC-SHA-256 with the pseudonym key; null without a key.
     *
     * @param string $value
     * @return string|null
     */
    public function hash( $value )
    {
        $key = $this->keys ? $this->keys->pseudonymKey() : null;
        if ( $key === null || $key === '' )
            return null;
        return 'h:' . substr( hash_hmac( 'sha256', (string)$value, $key ), 0, 16 );
    }

    /**
     * The person fields inside object, target, before and after: email and attempted_login (and keys ending in
     * _email), under their own options (hash by default).
     *
     * @param array $data
     * @return array
     */
    protected function personFields( array $data )
    {
        foreach ( $data as $k => $v )
        {
            if ( !is_string( $k ) )
                continue;
            if ( is_array( $v ) )
            {
                $data[$k] = $this->personFields( $v );
                continue;
            }
            $field = null;
            if ( $k === 'email' || substr( $k, -6 ) === '_email' )
                $field = 'email';
            elseif ( $k === 'attempted_login' )
                $field = 'attempted_login';
            if ( $field === null || $v === null || $v === '' )
                continue;
            switch ( $this->option( $field, 'hash' ) )
            {
                case 'off':
                    unset( $data[$k] );
                    break;
                case 'full':
                    break;
                default:
                    $h = $this->hash( (string)$v );
                    if ( $h === null )
                        unset( $data[$k] );
                    else
                        $data[$k] = $h;
            }
        }
        return $data;
    }

    /**
     * Removes NeverRecord[] keys and masks secrets, at every depth.
     *
     * @param array $data
     * @return array
     */
    public function scrub( array $data )
    {
        $never = isset( $this->config['neverRecord'] ) ? $this->config['neverRecord'] : array();
        foreach ( $data as $k => $v )
        {
            if ( is_string( $k ) && self::isNeverRecorded( $k, $never ) )
            {
                unset( $data[$k] );
                continue;
            }
            if ( is_string( $k ) && self::isSecretName( $k ) )
            {
                $data[$k] = is_array( $v ) ? self::SECRET : ( $v === null || $v === '' ? $v : self::SECRET );
                continue;
            }
            if ( is_array( $v ) )
                $data[$k] = $this->scrub( $v );
        }
        return $data;
    }

    /**
     * @param string $key
     * @param string[] $never
     * @return bool
     */
    public static function isNeverRecorded( $key, array $never )
    {
        // exact names: the 4.x attribute "Hash" is never recorded, the record's own "hash" field is not that
        $k = trim( $key, " :\t" );
        return in_array( $k, $never, true );
    }

    /**
     * A name whose value is a secret (expIniEditor::isSecret() when the class is there; its rules otherwise).
     * The record's own chain fields (hash, previous_hash, last_hash, key_id) are not secrets.
     *
     * @param string $name
     * @return bool
     */
    public static function isSecretName( $name )
    {
        $name = trim( (string)$name, " :\t" );
        if ( in_array( strtolower( $name ), array( 'hash', 'previous_hash', 'last_hash', 'key_id', 'prev', 'sha256' ), true ) )
            return false;
        if ( class_exists( 'expIniEditor' ) )
            return expIniEditor::isSecret( $name );
        return (bool)preg_match( '/password|passwd|passphrase|secret|token|salt|credential|privatekey|apikey/i', $name );
    }

    /**
     * Cuts long strings (MaxValueLength) at every depth.
     *
     * @param array $data
     * @return array
     */
    protected function limit( array $data )
    {
        $max = isset( $this->config['maxValueLength'] ) ? (int)$this->config['maxValueLength'] : 512;
        foreach ( $data as $k => $v )
        {
            if ( is_string( $v ) )
                $data[$k] = self::cut( $v, $max );
            elseif ( is_array( $v ) )
                $data[$k] = $this->limit( $v );
        }
        return $data;
    }

    /**
     * BeforeAfter=keys: the keys stay, every value is replaced by its sha256.
     *
     * @param array $data
     * @return array
     */
    protected function keysOnly( array $data )
    {
        foreach ( $data as $k => $v )
            $data[$k] = is_array( $v ) ? $this->keysOnly( $v ) : ( $v === null ? null : 'sha256:' . hash( 'sha256', is_bool( $v ) ? ( $v ? 'true' : 'false' ) : (string)$v ) );
        return $data;
    }

    /**
     * @param string $value
     * @param int $max characters
     * @return string The value, cut to $max characters with "…" appended when longer
     */
    public static function cut( $value, $max )
    {
        $value = (string)$value;
        if ( function_exists( 'mb_strlen' ) )
        {
            if ( mb_strlen( $value, 'UTF-8' ) <= $max )
                return $value;
            return mb_substr( $value, 0, $max, 'UTF-8' ) . '…';
        }
        return strlen( $value ) <= $max ? $value : substr( $value, 0, $max ) . '…';
    }

    /**
     * An address as its network: 203.0.113.7 -> 203.0.113.0/24, 2001:db8:12:3::1 -> 2001:db8:12::/48.
     * Anything else (a host name, "commandline") is hashed when a key exists, else left out.
     *
     * @param string $ip
     * @return string
     */
    public function truncateIp( $ip )
    {
        $ip = trim( $ip );
        $packed = @inet_pton( $ip );
        if ( $packed === false )
        {
            $h = $this->hash( $ip );
            return $h === null ? '' : $h;
        }
        $v4 = strlen( $packed ) === 4;
        $prefix = $v4 ? $this->config['ipv4Prefix'] : $this->config['ipv6Prefix'];
        $bytes = array_values( unpack( 'C*', $packed ) );
        $bits = $prefix;
        foreach ( $bytes as $i => $b )
        {
            if ( $bits >= 8 )
            {
                $bits -= 8;
                continue;
            }
            $bytes[$i] = $bits > 0 ? $b & ( 0xFF << ( 8 - $bits ) ) & 0xFF : 0;
            $bits = 0;
        }
        return inet_ntop( pack( 'C*', ...$bytes ) ) . '/' . $prefix;
    }

    /**
     * The path of a URL with query values replaced by "…" and the parameters of SecretPathViews[] views removed.
     *
     * @param string $url
     * @return string
     */
    public function truncateUrl( $url )
    {
        $path = $this->secretPath( $url );
        $q = strpos( $path, '?' );
        if ( $q === false )
            return $path;
        $query = substr( $path, $q + 1 );
        $path = substr( $path, 0, $q );
        $names = array();
        foreach ( explode( '&', $query ) as $pair )
        {
            if ( $pair === '' )
                continue;
            $name = urldecode( strtok( $pair, '=' ) );
            $names[] = rawurlencode( $name ) . '=…';
        }
        return $names ? $path . '?' . implode( '&', $names ) : $path;
    }

    /**
     * Replaces the path parameters of SecretPathViews[] (user/activate/<hash>) by "…", with or without a
     * siteaccess prefix, and the query values of secret-looking parameter names.
     *
     * @param string $url
     * @return string
     */
    public function secretPath( $url )
    {
        $q = strpos( $url, '?' );
        $path = $q === false ? $url : substr( $url, 0, $q );
        $query = $q === false ? null : substr( $url, $q + 1 );
        foreach ( isset( $this->config['secretPathViews'] ) ? $this->config['secretPathViews'] : array() as $view )
        {
            $view = trim( $view, '/' );
            if ( $view === '' )
                continue;
            $pattern = '#(^|/)(' . preg_quote( $view, '#' ) . ')/[^?]*#i';
            $path = preg_replace( $pattern, '$1$2/…', $path );
        }
        if ( $query !== null )
        {
            $pairs = array();
            foreach ( explode( '&', $query ) as $pair )
            {
                $name = urldecode( (string)strtok( $pair, '=' ) );
                $pairs[] = ( self::isSecretName( $name ) || self::isNeverRecorded( $name, isset( $this->config['neverRecord'] ) ? $this->config['neverRecord'] : array() ) )
                           ? rawurlencode( $name ) . '=…' : $pair;
            }
            $path .= '?' . implode( '&', $pairs );
        }
        return $path;
    }

    /**
     * Browser family and major version, and the OS family: "Firefox 131 / Linux".
     *
     * @param string $ua
     * @return string
     */
    public static function truncateUserAgent( $ua )
    {
        $ua = (string)$ua;
        $browser = null;
        $rules = array(
            'Edge' => '#Edg(?:e|A|iOS)?/(\d+)#',
            'Opera' => '#(?:OPR|Opera)/(\d+)#',
            'Samsung Internet' => '#SamsungBrowser/(\d+)#',
            'HeadlessChrome' => '#HeadlessChrome/(\d+)#',
            'Chrome' => '#(?:Chrome|CriOS)/(\d+)#',
            'Firefox' => '#(?:Firefox|FxiOS)/(\d+)#',
            'Safari' => '#Version/(\d+)[^ ]* (?:Mobile/[^ ]+ )?Safari/#',
            'curl' => '#^curl/(\d+)#',
            'Wget' => '#^Wget/(\d+)#',
            'python-requests' => '#python-requests/(\d+)#',
            'Go' => '#^Go-http-client/(\d+)#',
        );
        foreach ( $rules as $name => $re )
        {
            if ( preg_match( $re, $ua, $m ) )
            {
                $browser = $name . ' ' . $m[1];
                break;
            }
        }
        if ( $browser === null )
        {
            if ( preg_match( '#^([A-Za-z][A-Za-z0-9._-]{0,30})(?:/(\d+))?#', $ua, $m ) )
                $browser = $m[1] . ( isset( $m[2] ) ? ' ' . $m[2] : '' );
            else
                $browser = 'unknown';
        }
        $os = null;
        $osRules = array( 'Android' => '#Android#', 'iOS' => '#iPhone|iPad|iPod#', 'Windows' => '#Windows#',
                          'ChromeOS' => '#CrOS#', 'macOS' => '#Mac OS X|Macintosh#', 'Linux' => '#Linux|X11#' );
        foreach ( $osRules as $name => $re )
        {
            if ( preg_match( $re, $ua ) )
            {
                $os = $name;
                break;
            }
        }
        return $os ? "$browser / $os" : $browser;
    }
}
