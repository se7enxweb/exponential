<?php
/**
 * CORS of the REST interface (expRestCors, rest.ini [CorsSettings]): off by default with no header at all, the
 * Access-Control-* headers only for an allowed origin, "Vary: Origin" while it is on, a preflight answered without
 * running the request, and no wildcard together with credentials.
 *
 * No kernel and no database: the class is given its settings and $_SERVER-like arrays.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/private/restapi/expRestCorsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 * @group rest
 */

class expRestCorsTest extends PHPUnit\Framework\TestCase
{
    const APP = 'https://app.example.com';

    private static function cors( array $settings = array() )
    {
        return new expRestCors( $settings + array( 'enabled' => true, 'origins' => array( self::APP ) ) );
    }

    private static function preflightServer( $origin, $method = 'GET', $headers = null )
    {
        $server = array( 'REQUEST_METHOD' => 'OPTIONS', 'HTTP_ORIGIN' => $origin,
                         'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => $method );
        if ( $headers !== null )
            $server['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'] = $headers;
        return $server;
    }

    private static function names( array $headers )
    {
        return array_map( function ( $line ) { return strtolower( strstr( $line, ':', true ) ); }, $headers );
    }

    public function testOffByDefaultSendsNothing()
    {
        $cors = new expRestCors();
        $this->assertFalse( $cors->isEnabled() );
        $this->assertSame( array(), $cors->responseHeaders( array( 'HTTP_ORIGIN' => self::APP ) ) );
        $this->assertFalse( $cors->isPreflight( 'OPTIONS', self::preflightServer( self::APP ) ) );
        $this->assertFalse( $cors->isOriginAllowed( self::APP ) );
    }

    public function testOffByDefaultInTheShippedSettings()
    {
        $ini = (string)file_get_contents( dirname( __DIR__, 5 ) . '/settings/rest.ini' );
        $this->assertMatchesRegularExpression( '/^\[CorsSettings\]$/m', $ini );
        $this->assertMatchesRegularExpression( '/^Cors=disabled$/m', $ini );
        $this->assertMatchesRegularExpression( '/^AllowCredentials=disabled$/m', $ini );
        $this->assertDoesNotMatchRegularExpression( '/^AllowedOrigins\[\]=\S/m', $ini, 'an origin is allowed as shipped' );
    }

    public function testAnAllowedOriginGetsItsOriginBack()
    {
        $headers = self::cors( array( 'exposed_headers' => array( 'ETag' ) ) )->responseHeaders( array( 'HTTP_ORIGIN' => self::APP ) );
        $this->assertContains( 'Access-Control-Allow-Origin: ' . self::APP, $headers );
        $this->assertContains( 'Access-Control-Expose-Headers: ETag', $headers );
        $this->assertContains( 'Vary: Origin', $headers );
        $this->assertNotContains( 'access-control-allow-credentials', self::names( $headers ) );
    }

    public static function disallowedOriginProvider()
    {
        return array(
            'other host'          => array( 'https://evil.example.com' ),
            'suffix of the host'  => array( 'https://app.example.com.evil.example' ),
            'other scheme'        => array( 'http://app.example.com' ),
            'other port'          => array( 'https://app.example.com:8443' ),
            'null'                => array( 'null' ),
            'with a path'         => array( self::APP . '/' ),
            'header injection'    => array( self::APP . "\r\nSet-Cookie: x=1" ),
            'no Origin'           => array( null ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('disallowedOriginProvider')]
    public function testADisallowedOriginGetsNoAccessControlHeader( $origin )
    {
        $server = $origin === null ? array() : array( 'HTTP_ORIGIN' => $origin );
        $this->assertSame( array( 'Vary: Origin' ), self::cors()->responseHeaders( $server ) );
    }

    public function testOriginsAreComparedWithoutCaseOfSchemeAndHost()
    {
        $this->assertTrue( self::cors()->isOriginAllowed( 'HTTPS://App.Example.com' ) );
        $this->assertTrue( self::cors( array( 'origins' => array( 'HTTP://LOCALHOST:3000' ) ) )->isOriginAllowed( 'http://localhost:3000' ) );
    }

    public function testAnAllowedPreflightIsAnsweredWith204()
    {
        $cors = self::cors( array( 'max_age' => 300 ) );
        $server = self::preflightServer( self::APP, 'POST', 'authorization, Content-Type' );
        $this->assertTrue( $cors->isPreflight( 'OPTIONS', $server ) );
        $answer = $cors->preflight( $server );
        $this->assertSame( 204, $answer['status'] );
        $this->assertContains( 'Access-Control-Allow-Origin: ' . self::APP, $answer['headers'] );
        $this->assertContains( 'Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE', $answer['headers'] );
        $this->assertContains( 'Access-Control-Max-Age: 300', $answer['headers'] );
        $this->assertContains( 'Vary: Origin', $answer['headers'] );
        $this->assertContains( 'access-control-allow-headers', self::names( $answer['headers'] ) );
    }

    public static function refusedPreflightProvider()
    {
        return array(
            'disallowed origin'    => array( self::preflightServer( 'https://evil.example.com' ) ),
            'disallowed method'    => array( self::preflightServer( self::APP, 'TRACE' ) ),
            'disallowed header'    => array( self::preflightServer( self::APP, 'GET', 'Authorization, X-Evil' ) ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('refusedPreflightProvider')]
    public function testARefusedPreflightHasNoAccessControlHeader( array $server )
    {
        $answer = self::cors()->preflight( $server );
        $this->assertSame( 403, $answer['status'] );
        $this->assertSame( array( 'Vary: Origin' ), $answer['headers'] );
    }

    public function testOnlyOptionsWithOriginAndRequestMethodIsAPreflight()
    {
        $cors = self::cors();
        $this->assertFalse( $cors->isPreflight( 'GET', self::preflightServer( self::APP ) ) );
        $this->assertFalse( $cors->isPreflight( 'OPTIONS', array( 'HTTP_ORIGIN' => self::APP ) ) );
        $this->assertFalse( $cors->isPreflight( 'OPTIONS', array( 'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'GET' ) ) );
    }

    public function testCredentialsSendTheOriginNeverAWildcard()
    {
        $cors = self::cors( array( 'origins' => array( '*', self::APP ), 'credentials' => true ) );
        $this->assertFalse( $cors->isOriginAllowed( 'https://evil.example.com' ), 'a wildcard with credentials allowed any origin' );
        $headers = $cors->responseHeaders( array( 'HTTP_ORIGIN' => self::APP ) );
        $this->assertContains( 'Access-Control-Allow-Origin: ' . self::APP, $headers );
        $this->assertContains( 'Access-Control-Allow-Credentials: true', $headers );
    }

    public function testAWildcardWithoutCredentialsAllowsAnyOrigin()
    {
        $cors = self::cors( array( 'origins' => array( '*' ) ) );
        $headers = $cors->responseHeaders( array( 'HTTP_ORIGIN' => 'https://any.example.org' ) );
        $this->assertContains( 'Access-Control-Allow-Origin: *', $headers );
        $this->assertFalse( $cors->isOriginAllowed( 'null' ) );
    }

    public function testSettingsAreReadFromRestIni()
    {
        $ini = new class
        {
            public $values = array( 'Cors' => 'enabled', 'AllowedOrigins' => array( 'https://app.example.com' ),
                                    'AllowedMethods' => array( 'GET' ), 'MaxAge' => '0', 'AllowCredentials' => 'disabled' );
            public function hasVariable( $section, $name ) { return $section === 'CorsSettings' && isset( $this->values[$name] ); }
            public function variable( $section, $name ) { return $this->values[$name]; }
        };
        $cors = expRestCors::fromIni( $ini );
        $this->assertTrue( $cors->isEnabled() );
        $answer = $cors->preflight( self::preflightServer( self::APP, 'GET' ) );
        $this->assertSame( 204, $answer['status'] );
        $this->assertContains( 'Access-Control-Allow-Methods: GET', $answer['headers'] );
        $this->assertNotContains( 'access-control-max-age', self::names( $answer['headers'] ) );
        $this->assertSame( 403, $cors->preflight( self::preflightServer( self::APP, 'DELETE' ) )['status'] );
    }
}
