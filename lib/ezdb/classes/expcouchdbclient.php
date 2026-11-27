<?php
/**
 * File containing the expCouchDBClient class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * Apache CouchDB's HTTP API, as the CouchDB driver (expCouchDBDB) and the import (expCouchDBReader) use it: JSON
 * requests over PHP's curl extension, one handle per client so the connection is reused. CouchDB has no other
 * protocol, so this is the only way in; no library is needed.
 *
 * Tests give the client a stand-in: any callable( $method, $path, $body ) returning array( status, decoded body ).
 *
 * @since 6.0.15
 * @package lib
 */
class expCouchDBClient
{
    /** @var string http(s)://host:port */
    protected $base;

    /** @var string */
    protected $user;

    /** @var string */
    protected $password;

    /** @var array curl options of TLS and timeouts */
    protected $options;

    /** @var resource|CurlHandle|null */
    protected $handle = null;

    /** @var callable|null a stand-in (tests) */
    protected $transport = null;

    /** @var int requests sent */
    public $Requests = 0;

    /**
     * @param string $server host, host:port or http(s)://host:port
     * @param int $port 5984 (6984 with TLS) when the server names none
     * @param string $user
     * @param string $password
     * @param array $settings TLS, TLSCAFile, TLSVerifyPeer, ConnectTimeout, RequestTimeout; 'transport' => a stand-in
     */
    public function __construct( $server, $port, $user, $password, array $settings = array() )
    {
        $tls = isset( $settings['TLS'] ) && $settings['TLS'] === 'enabled';
        $host = trim( (string)$server );
        $host = $host === '' ? '127.0.0.1' : explode( ',', $host )[0];
        $scheme = $tls ? 'https' : 'http';
        if ( preg_match( '#^(https?)://(.+)$#i', $host, $m ) )
        {
            $scheme = strtolower( $m[1] );
            $host = $m[2];
        }
        $host = rtrim( $host, '/' );
        if ( !preg_match( '/:\d+$/', $host ) )
            $host .= ':' . ( $port ? (int)$port : ( $scheme === 'https' ? 6984 : 5984 ) );
        $this->base = $scheme . '://' . $host;
        $this->user = (string)$user;
        $this->password = (string)$password;
        $this->options = array(
            CURLOPT_CONNECTTIMEOUT => isset( $settings['ConnectTimeout'] ) ? max( 1, (int)$settings['ConnectTimeout'] ) : 5,
            CURLOPT_TIMEOUT => isset( $settings['RequestTimeout'] ) ? max( 1, (int)$settings['RequestTimeout'] ) : 60,
        );
        if ( !empty( $settings['TLSCAFile'] ) )
            $this->options[CURLOPT_CAINFO] = (string)$settings['TLSCAFile'];
        if ( isset( $settings['TLSVerifyPeer'] ) && $settings['TLSVerifyPeer'] === 'disabled' )
        {
            $this->options[CURLOPT_SSL_VERIFYPEER] = false;
            $this->options[CURLOPT_SSL_VERIFYHOST] = 0;
        }
        if ( isset( $settings['transport'] ) && is_callable( $settings['transport'] ) )
            $this->transport = $settings['transport'];
    }

    /** @return string */
    public function base()
    {
        return $this->base;
    }

    /** Whether PHP can reach CouchDB (curl) */
    public static function available()
    {
        return function_exists( 'curl_init' );
    }

    /**
     * One request.
     *
     * @param string $method GET, PUT, POST, DELETE, HEAD
     * @param string $path /db/_find ...
     * @param mixed $body sent as JSON (null: none)
     * @return array( int status (0: no answer), mixed decoded body )
     */
    public function request( $method, $path, $body = null )
    {
        $this->Requests++;
        if ( $this->transport !== null )
            return call_user_func( $this->transport, $method, $path, $body );
        if ( $this->handle === null )
            $this->handle = curl_init();
        $options = $this->options + array(
            CURLOPT_URL => $this->base . $path,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $this->user . ':' . $this->password,
            CURLOPT_HTTPHEADER => array( 'Content-Type: application/json', 'Accept: application/json' ),
            CURLOPT_POSTFIELDS => $body === null ? '' : json_encode( $body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_PRESERVE_ZERO_FRACTION ),
            CURLOPT_NOBODY => $method === 'HEAD',
        );
        if ( $body === null )
            unset( $options[CURLOPT_POSTFIELDS] );
        curl_setopt_array( $this->handle, $options );
        $raw = curl_exec( $this->handle );
        if ( $raw === false )
        {
            $error = curl_error( $this->handle );
            $this->handle = null;
            return array( 0, array( 'error' => 'unreachable', 'reason' => $error ) );
        }
        $status = (int)curl_getinfo( $this->handle, CURLINFO_HTTP_CODE );
        $decoded = $raw === '' ? null : json_decode( (string)$raw, true );
        return array( $status, $decoded );
    }

    /**
     * A database name in a path (CouchDB's names may hold / and +).
     *
     * @param string $name
     * @return string
     */
    public static function path( $name )
    {
        return '/' . rawurlencode( (string)$name );
    }

    /**
     * Whether a name is a valid CouchDB database name (pure).
     *
     * @param string $name
     * @return bool
     */
    public static function validDatabaseName( $name )
    {
        return (bool)preg_match( '#^[a-z][a-z0-9_$()+/-]{0,237}$#', (string)$name );
    }

    public function close()
    {
        $this->handle = null;
    }
}
?>
