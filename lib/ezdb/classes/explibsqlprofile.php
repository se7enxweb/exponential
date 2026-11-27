<?php
/**
 * File containing the expLibSQLProfile class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * What differs between the services of the libSQL family that expLibSQLDB reaches over HTTP (pure; no connection):
 *
 *  - libsql: a libSQL server of one's own (sqld, the libsql-server container), plain HTTP on port 8080 by default,
 *    an auth token only when the server is started with one (--auth-jwt-key-file).
 *  - turso: Turso's cloud (libsql://<database>-<organisation>.turso.io), HTTPS on 443, a database token always.
 *
 * The settings are site.ini [LibSQLSettings]; the 'libsql' parameters of the driver (the installer's) replace them.
 *
 * @since 6.0.15
 */
class expLibSQLProfile
{
    /** The settings and their defaults: [LibSQLSettings] of site.ini */
    const DEFAULTS = array( 'URL' => '', 'TLS' => 'auto', 'AuthToken' => '', 'Timeout' => '30', 'ConnectAttempts' => '3', 'ConnectWait' => '200',
                              'KeepStream' => 'enabled' );

    /**
     * The profiles by name.
     *
     * @return array name => array( label, port, tls, token_required, tested )
     */
    public static function all()
    {
        return array(
            'libsql' => array( 'name' => 'libsql', 'label' => 'libSQL server (sqld)', 'port' => '8080', 'tls' => false,
                               'token_required' => false, 'tested' => true ),
            'turso' => array( 'name' => 'turso', 'label' => 'Turso', 'port' => '443', 'tls' => true,
                              'token_required' => true, 'tested' => false ),
        );
    }

    /**
     * @param string $name
     * @return array|null
     */
    public static function get( $name )
    {
        $all = self::all();
        $name = strtolower( (string)$name );
        return isset( $all[$name] ) ? $all[$name] : null;
    }

    /**
     * The profile with its settings: the defaults, replaced by the given ones (unknown names are left out).
     *
     * @param array $profile
     * @param array $settings
     * @return array the profile, with 'settings'
     */
    public static function apply( array $profile, array $settings )
    {
        $values = self::DEFAULTS;
        foreach ( $settings as $name => $value )
        {
            if ( array_key_exists( $name, $values ) && is_scalar( $value ) )
                $values[$name] = trim( (string)$value );
        }
        $profile['settings'] = $values;
        return $profile;
    }

    /**
     * The URL of the server: [LibSQLSettings] URL when set (libsql://, https://, http://), else built from Server and
     * Port, https when TLS=enabled, or TLS=auto with the profile's TLS or port 443. A Server that is a URL itself
     * (libsql://exp-acme.turso.io, what Turso's dashboard shows) is used as it is.
     *
     * @param array $profile from apply()
     * @param string $server
     * @param string|int $port
     * @return string
     */
    public static function url( array $profile, $server, $port )
    {
        $settings = isset( $profile['settings'] ) ? $profile['settings'] : self::DEFAULTS;
        if ( $settings['URL'] !== '' )
            return expLibSQLConnection::normalizeUrl( $settings['URL'] );
        $server = trim( (string)$server );
        if ( preg_match( '#^[a-z]+://#i', $server ) )
            return expLibSQLConnection::normalizeUrl( $server );
        if ( $server === '' )
            $server = '127.0.0.1';
        $port = trim( (string)$port ) !== '' ? (string)(int)$port : (string)$profile['port'];
        $tls = strtolower( $settings['TLS'] );
        $https = $tls === 'enabled' || $tls === 'true' || ( $tls === 'auto' && ( $profile['tls'] || $port === '443' ) );
        if ( strpos( $server, ':' ) !== false && $server[0] !== '[' )
            $server = '[' . $server . ']';   // an IPv6 address
        $defaultPort = $https ? '443' : '80';
        return ( $https ? 'https' : 'http' ) . '://' . $server . ( $port !== $defaultPort ? ':' . $port : '' );
    }

    /**
     * The auth token: [LibSQLSettings] AuthToken, else the Password of [DatabaseSettings], else the environment's
     * LIBSQL_AUTH_TOKEN (TURSO_AUTH_TOKEN for Turso); '' for none.
     *
     * @param array $profile from apply()
     * @param string $password
     * @return string
     */
    public static function token( array $profile, $password )
    {
        $settings = isset( $profile['settings'] ) ? $profile['settings'] : self::DEFAULTS;
        if ( $settings['AuthToken'] !== '' )
            return $settings['AuthToken'];
        if ( (string)$password !== '' )
            return (string)$password;
        foreach ( $profile['name'] === 'turso' ? array( 'TURSO_AUTH_TOKEN', 'LIBSQL_AUTH_TOKEN' ) : array( 'LIBSQL_AUTH_TOKEN' ) as $name )
        {
            $value = getenv( $name );
            if ( is_string( $value ) && $value !== '' )
                return $value;
        }
        return '';
    }
}
