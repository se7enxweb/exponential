<?php
/**
 * File containing the expMigrationStepHttp class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The step type "http": one HTTP request to another system, for a migration that has to tell it something (a search
 * service, a CDN, a webhook receiver) or read something from it.
 *
 *   - type: http
 *     method: POST                       GET (the default), POST, PUT, PATCH, DELETE, HEAD
 *     url: https://cdn.example.com/purge
 *     headers: { Accept: application/json }
 *     secret_headers: { X-Api-Key: cdn.api_key }   header => the name of a secret (Setup > Secrets, or env:VAR)
 *     bearer: cdn.token                  Authorization: Bearer <the secret>
 *     json: { paths: [ "/news/*" ] }     a body as JSON (Content-Type: application/json); or body: a text
 *     sign: migration.webhook            X-Exponential-Timestamp and X-Exponential-Signature as webhooks of Exponential
 *                                        sign them (expGraphQLWebhooks::signature()), with this secret
 *     expect: [ 200, 202 ]               the answers that pass (default: any 2xx)
 *     timeout: 30                        seconds
 *     references: { purged: status }     status, body, json
 *
 * Secrets are named, never written in the migration or in its log: their values reach only the request. No redirect is
 * followed, only http and https are spoken, and the URL carries no user or password. A dry run sends nothing. Each
 * request is recorded in the audit log (system.migration.http: method, host and path, status; never the query, a
 * header or the body). A request cannot be taken back: a migration with this step needs a down: (which can send the
 * request that undoes it) or a rollback to its caption.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationStepHttp extends expMigrationStepTypeBase
{
    /** The methods the step sends. */
    const METHODS = array( 'GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD' );

    /** The most of an answer's body kept for references:. */
    const MAX_BODY = 65536;

    /** @var callable|null function( string $method, string $url, string[] $headers, string|null $body, int $timeout ): array( status, body, error ) */
    protected static $transport;

    /**
     * Replaces how requests are sent (a test's); null puts curl back.
     *
     * @param callable|null $transport
     */
    public static function setTransport( $transport )
    {
        self::$transport = $transport;
    }

    /** @return string */
    public function name()
    {
        return 'http';
    }

    /** @return array */
    public function schema()
    {
        return array(
            'description' => 'Sends one HTTP request to another system, with secrets by name and an optional signature, and keeps the answer for references',
            'keys' => array(
                'method' => array( 'type' => 'string', 'default' => 'GET', 'values' => self::METHODS, 'description' => 'the method' ),
                'url' => array( 'type' => 'string', 'required' => true, 'description' => 'http:// or https:// with a host, without a user or password' ),
                'headers' => array( 'type' => 'map', 'description' => 'header => value (no secrets here: secret_headers:)' ),
                'secret_headers' => array( 'type' => 'map', 'description' => 'header => the name of a secret (Setup > Secrets, or env:<VARIABLE>)' ),
                'bearer' => array( 'type' => 'string', 'description' => 'the name of a secret sent as Authorization: Bearer <value>' ),
                'body' => array( 'type' => 'string', 'description' => 'the body as a text' ),
                'json' => array( 'type' => 'any', 'description' => 'the body as JSON (Content-Type: application/json)' ),
                'sign' => array( 'type' => 'string', 'description' => 'the name of a secret that signs the request as webhooks of Exponential are signed' ),
                'expect' => array( 'type' => 'list', 'description' => 'the status codes that pass (default: any 2xx)' ),
                'timeout' => array( 'type' => 'int', 'default' => 30, 'description' => 'seconds' ),
            ),
            'values' => array(
                'status' => 'the status code of the answer',
                'body' => 'the body of the answer (at most 64 KB)',
                'json' => 'the body decoded as JSON (null when it is not)',
            ),
            'example' => "- type: http\n  method: POST\n  url: https://cdn.example.com/purge\n  bearer: cdn.token\n"
                       . "  json: { paths: [ \"/news/*\" ] }\n  expect: [ 200, 202 ]\n  references: { purge_status: status }\n",
        );
    }

    /**
     * @param array $step
     * @return string[]
     */
    public function validate( array $step )
    {
        $errors = array();
        if ( isset( $step['url'] ) && !expMigrationReferences::contains( $step['url'] ) )
        {
            $problem = self::urlProblem( (string)$step['url'] );
            if ( $problem !== '' )
                $errors[] = "url: $problem";
        }
        if ( array_key_exists( 'body', $step ) && array_key_exists( 'json', $step ) )
            $errors[] = 'body: and json: exclude each other';
        if ( isset( $step['method'] ) && in_array( strtoupper( (string)$step['method'] ), array( 'GET', 'HEAD' ), true )
             && ( array_key_exists( 'body', $step ) || array_key_exists( 'json', $step ) ) )
            $errors[] = 'a ' . strtoupper( (string)$step['method'] ) . ' request has no body';
        foreach ( array( 'headers', 'secret_headers' ) as $key )
            if ( isset( $step[$key] ) && is_array( $step[$key] ) )
                foreach ( $step[$key] as $name => $value )
                {
                    if ( !preg_match( '/^[A-Za-z0-9-]+$/', (string)$name ) )
                        $errors[] = "$key.$name: the name of a header";
                    elseif ( !is_scalar( $value ) || preg_match( '/[\r\n]/', (string)$value ) )
                        $errors[] = "$key.$name: one line of text";
                    elseif ( $key === 'secret_headers' && !expMigrationReferences::contains( $value ) && !expSecrets::validName( (string)$value ) )
                        $errors[] = "$key.$name: the name of a secret";
                }
        foreach ( array( 'bearer', 'sign' ) as $key )
            if ( isset( $step[$key] ) && !expMigrationReferences::contains( $step[$key] ) && !expSecrets::validName( (string)$step[$key] ) )
                $errors[] = "$key: the name of a secret";
        if ( isset( $step['expect'] ) && is_array( $step['expect'] ) )
            foreach ( $step['expect'] as $i => $code )
                if ( !is_int( $code ) || $code < 100 || $code > 599 )
                    $errors[] = "expect[$i]: a status code (100 to 599)";
        if ( isset( $step['timeout'] ) && is_int( $step['timeout'] ) && ( $step['timeout'] < 1 || $step['timeout'] > 600 ) )
            $errors[] = 'timeout: 1 to 600 seconds';
        return $errors;
    }

    /**
     * Why a URL cannot be asked: http or https, a host, no user or password. Pure.
     *
     * @param string $url
     * @return string '' when it can
     */
    public static function urlProblem( $url )
    {
        $parts = parse_url( (string)$url );
        if ( !is_array( $parts ) || empty( $parts['host'] ) || !isset( $parts['scheme'] ) || !in_array( strtolower( $parts['scheme'] ), array( 'http', 'https' ), true ) )
            return 'the URL must be http:// or https:// with a host';
        if ( isset( $parts['user'] ) || isset( $parts['pass'] ) )
            return 'the URL must not carry a user or a password (use bearer: or secret_headers:)';
        return '';
    }

    /**
     * The URL as the log and the audit show it: scheme, host, port and path, never the query. Pure.
     *
     * @param string $url
     * @return string
     */
    public static function shown( $url )
    {
        $parts = parse_url( (string)$url );
        if ( !is_array( $parts ) || !isset( $parts['host'] ) )
            return '(no URL)';
        return ( isset( $parts['scheme'] ) ? $parts['scheme'] : 'http' ) . '://' . $parts['host'] . ( isset( $parts['port'] ) ? ':' . $parts['port'] : '' )
             . ( isset( $parts['path'] ) ? $parts['path'] : '/' ) . ( isset( $parts['query'] ) ? '?...' : '' );
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function plan( array $step, $context )
    {
        $extras = array();
        if ( !empty( $step['bearer'] ) )
            $extras[] = 'a bearer token';
        if ( !empty( $step['sign'] ) )
            $extras[] = 'signed';
        if ( array_key_exists( 'json', $step ) )
            $extras[] = 'a JSON body';
        elseif ( array_key_exists( 'body', $step ) )
            $extras[] = 'a body';
        return array( 'send ' . strtoupper( (string)$step['method'] ) . ' ' . self::shown( (string)$step['url'] ) . ( $extras ? ' (' . implode( ', ', $extras ) . ')' : '' ) . ' (not sent in a dry run)' );
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return array
     */
    public function apply( array $step, $context )
    {
        $method = strtoupper( (string)$step['method'] );
        $url = (string)$step['url'];
        $problem = self::urlProblem( $url );
        if ( $problem !== '' )
            throw new RuntimeException( "url: $problem" );
        $headers = array();
        foreach ( isset( $step['headers'] ) ? (array)$step['headers'] : array() as $name => $value )
            $headers[(string)$name] = (string)$value;
        foreach ( isset( $step['secret_headers'] ) ? (array)$step['secret_headers'] : array() as $name => $secret )
            $headers[(string)$name] = expSecrets::required( (string)$secret, "secret_headers.$name" );
        if ( !empty( $step['bearer'] ) )
            $headers['Authorization'] = 'Bearer ' . expSecrets::required( (string)$step['bearer'], 'bearer' );
        $body = null;
        if ( array_key_exists( 'json', $step ) )
        {
            $body = json_encode( $step['json'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
            $headers['Content-Type'] = 'application/json';
        }
        elseif ( array_key_exists( 'body', $step ) )
            $body = (string)$step['body'];
        if ( !empty( $step['sign'] ) )
        {
            $timestamp = time();
            $headers['X-Exponential-Timestamp'] = (string)$timestamp;
            $headers['X-Exponential-Signature'] = expGraphQLWebhooks::signature( expSecrets::required( (string)$step['sign'], 'sign' ), $timestamp, (string)$body );
        }
        if ( !isset( $headers['User-Agent'] ) )
            $headers['User-Agent'] = 'Exponential migration';
        $lines = array();
        list( $status, $answer, $error ) = self::send( $method, $url, $headers, $body, (int)$step['timeout'] );
        $passes = $error === null && ( isset( $step['expect'] ) && $step['expect'] ? in_array( $status, array_map( 'intval', (array)$step['expect'] ), true )
                                                                                  : $status >= 200 && $status <= 299 );
        if ( $context instanceof expMigrationContext )
            $context->audit( 'system.migration.http', array( 'object' => array( 'type' => 'url', 'id' => self::shown( $url ) ), 'verb' => strtolower( $method ),
                                                             'result' => $passes ? 'success' : 'failure',
                                                             'x' => array( 'status' => $status, 'run_id' => $context->runId() ) ) );
        if ( $error !== null )
            throw new RuntimeException( "$method " . self::shown( $url ) . ": $error" );
        if ( !$passes )
            throw new RuntimeException( "$method " . self::shown( $url ) . " answered $status" . ( isset( $step['expect'] ) && $step['expect'] ? ' (expected ' . implode( ', ', (array)$step['expect'] ) . ')' : '' ) );
        $answer = (string)$answer;
        $json = json_decode( $answer, true );
        $lines[] = "$method " . self::shown( $url ) . " answered $status";
        $values = array( 'status' => $status, 'body' => strlen( $answer ) > self::MAX_BODY ? substr( $answer, 0, self::MAX_BODY ) : $answer,
                         'json' => json_last_error() === JSON_ERROR_NONE ? $json : null );
        return self::result( $lines, $values );
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $headers name => value
     * @param string|null $body
     * @param int $timeout
     * @return array( int status, string body, string|null error )
     */
    protected static function send( $method, $url, array $headers, $body, $timeout )
    {
        $lines = array();
        foreach ( $headers as $name => $value )
            $lines[] = $name . ': ' . $value;
        if ( self::$transport !== null )
        {
            $result = call_user_func( self::$transport, $method, $url, $lines, $body, $timeout );
            return array( (int)$result[0], isset( $result[1] ) ? (string)$result[1] : '', isset( $result[2] ) ? $result[2] : null );
        }
        if ( !function_exists( 'curl_init' ) )
            return array( 0, '', 'the PHP extension curl is missing' );
        $ch = curl_init( $url );
        $options = array(
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $lines,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => max( 1, (int)$timeout ),
            CURLOPT_CONNECTTIMEOUT => min( max( 1, (int)$timeout ), 10 ),
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
            CURLOPT_NOBODY => $method === 'HEAD',
        );
        if ( $body !== null )
            $options[CURLOPT_POSTFIELDS] = $body;
        curl_setopt_array( $ch, $options );
        $answer = curl_exec( $ch );
        $status = (int)curl_getinfo( $ch, CURLINFO_RESPONSE_CODE );
        $error = curl_errno( $ch ) ? curl_error( $ch ) : null;
        unset( $ch );
        return array( $status, is_string( $answer ) ? $answer : '', $error );
    }
}
