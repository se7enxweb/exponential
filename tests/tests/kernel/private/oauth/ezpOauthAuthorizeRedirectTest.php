<?php
/**
 * oauth/authorize redirects only to the end point of a known client (RFC 6749, section 4.1.2.1): with no client ID,
 * an unknown client or a redirect_uri that is not the client's end point it answers 400 Bad Request with no
 * Location, where it sent the browser to any redirect_uri of the request before. Errors after the end point is
 * validated (a missing response_type, access_denied) still go to the client's end point.
 *
 * The view runs in a PHP process of its own with stand-ins for the kernel classes it calls before the login
 * (fixtures/authorize_harness.php). No kernel, database or siteaccess.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/private/oauth/ezpOauthAuthorizeRedirectTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 * @group oauth
 */

class ezpOauthAuthorizeRedirectTest extends PHPUnit\Framework\TestCase
{
    const ENDPOINT = 'https://client.example/callback';

    /** @return array the JSON object the harness printed */
    private function authorize( array $get, array $post = array(), array $extra = array() )
    {
        $line = array( PHP_BINARY, '-n', '-d', 'display_errors=stderr', __DIR__ . '/fixtures/authorize_harness.php',
                       json_encode( array( 'get' => $get, 'post' => $post ) + $extra ) );
        $process = proc_open( $line, array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes );
        $this->assertIsResource( $process );
        $out = stream_get_contents( $pipes[1] );
        $err = stream_get_contents( $pipes[2] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        $this->assertSame( 0, proc_close( $process ), $out . $err );
        $this->assertSame( '', $err );
        $result = json_decode( $out, true );
        $this->assertIsArray( $result, $out );
        return $result;
    }

    public static function refusedProvider()
    {
        return array(
            'no redirect_uri'         => array( array( 'client_id' => 'known-client', 'response_type' => 'code' ) ),
            'no client ID'            => array( array( 'redirect_uri' => 'https://evil.example/', 'response_type' => 'code' ) ),
            'unknown client'          => array( array( 'redirect_uri' => 'https://evil.example/', 'client_id' => 'nobody', 'response_type' => 'code' ) ),
            'foreign redirect_uri'    => array( array( 'redirect_uri' => 'https://evil.example/', 'client_id' => 'known-client', 'response_type' => 'code' ) ),
            'longer redirect_uri'     => array( array( 'redirect_uri' => self::ENDPOINT . '.evil.example/', 'client_id' => 'known-client', 'response_type' => 'code' ) ),
            'no client, no type'      => array( array( 'redirect_uri' => 'https://evil.example/' ) ),
            'redirect_uri as array'   => array( array( 'redirect_uri' => array( self::ENDPOINT ), 'client_id' => 'known-client', 'response_type' => 'code' ) ),
            'client ID as array'      => array( array( 'redirect_uri' => self::ENDPOINT, 'client_id' => array( 'known-client' ), 'response_type' => 'code' ) ),
            'empty redirect_uri of a client stored without end point' => array( array( 'redirect_uri' => '', 'client_id' => 'no-endpoint-client', 'response_type' => 'code' ) ),
            'unknown client by POST'  => array( array(), array( 'AuthorizeButton' => '1', 'redirect_uri' => 'https://evil.example/', 'client_id' => 'nobody' ) ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('refusedProvider')]
    public function testAnUnvalidatedRedirectUriIsNotFollowed( array $get, array $post = array() )
    {
        $this->assertSame( array( 'status' => '400 Bad Request', 'location' => null ), $this->authorize( $get, $post ) );
    }

    public function testAMissingResponseTypeGoesToTheClientsEndPoint()
    {
        $this->assertSame(
            array( 'status' => '302 Found',
                   'location' => self::ENDPOINT . '?error=invalid_request&error_description=' . urlencode( 'Missing response_type parameter' ) ),
            $this->authorize( array( 'redirect_uri' => self::ENDPOINT, 'client_id' => 'known-client' ) ) );
    }

    public function testAValidRequestGoesOnToTheLogin()
    {
        $this->assertSame( array( 'reached' => 'login' ),
                           $this->authorize( array( 'redirect_uri' => self::ENDPOINT, 'client_id' => 'known-client', 'response_type' => 'code' ) ) );
    }

    private static function validRequest()
    {
        return array( 'redirect_uri' => self::ENDPOINT, 'client_id' => 'known-client', 'response_type' => 'code' );
    }

    public static function deniedAccessProvider()
    {
        return array(
            // the result of the old check "!$access['accessWord'] == 'yes'" was false for every one of these
            'no policy'                    => array( array( 'accessWord' => 'no' ) ),
            'limited to another siteaccess' => array( array( 'accessWord' => 'limited', 'policies' => array( array( 'SiteAccess' => array( (string)crc32( 'other' ) ) ) ) ) ),
            'limited without policies'     => array( array( 'accessWord' => 'limited', 'policies' => array() ) ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('deniedAccessProvider')]
    public function testASignedInUserWithoutThePolicyIsSentBackWithAccessDenied( array $access )
    {
        $this->assertSame(
            array( 'status' => '302 Found',
                   'location' => self::ENDPOINT . '?error=access_denied&error_description=' . urlencode( 'The user may not authorize clients' ) ),
            $this->authorize( self::validRequest(), array(), array( 'user' => $access, 'siteaccess' => 'site' ) ) );
    }

    public static function allowedAccessProvider()
    {
        return array(
            'every function'             => array( array( 'accessWord' => 'yes' ) ),
            'limited to this siteaccess' => array( array( 'accessWord' => 'limited', 'policies' => array( array( 'SiteAccess' => array( sprintf( '%u', crc32( 'site' ) ) ) ) ) ) ),
            'limited, no siteaccess limitation' => array( array( 'accessWord' => 'limited', 'policies' => array( array( 'Section' => array( '1' ) ) ) ) ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('allowedAccessProvider')]
    public function testASignedInUserWithThePolicyGoesOnToTheAuthorization( array $access )
    {
        $this->assertSame( array( 'reached' => 'authorization' ),
                           $this->authorize( self::validRequest(), array(), array( 'user' => $access, 'siteaccess' => 'site' ) ) );
    }

    public function testThePolicyCheckCanBeSwitchedOffForAnUpgrade()
    {
        $this->assertSame( array( 'reached' => 'authorization' ),
                           $this->authorize( self::validRequest(), array(),
                                             array( 'user' => array( 'accessWord' => 'no' ), 'requirePolicy' => 'disabled' ) ) );
        $this->assertSame( '302 Found',
                           $this->authorize( self::validRequest(), array(),
                                             array( 'user' => array( 'accessWord' => 'no' ), 'requirePolicy' => 'enabled' ) )['status'] );
    }

    public function testTheAutoloadGeneratorFindsNoClassInTheHarness()
    {
        // the stand-ins carry kernel class names; declared as classes, ezpgenerateautoloads.php -s would map them to
        // the harness, a script that runs when it is loaded
        $tokens = token_get_all( (string)file_get_contents( __DIR__ . '/fixtures/authorize_harness.php' ) );
        $this->assertNotEmpty( $tokens );
        foreach ( $tokens as $token )
            $this->assertFalse( is_array( $token ) && in_array( $token[0], array( T_CLASS, T_INTERFACE, T_TRAIT ), true ), 'a class declaration in the harness' );
    }
}
