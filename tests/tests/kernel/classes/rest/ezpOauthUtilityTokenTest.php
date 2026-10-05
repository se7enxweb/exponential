<?php
/**
 * Tests of how the REST layer finds the OAuth access token of a request (ezpOauthUtility::getToken()): the
 * Authorization header ("OAuth <token>", from HTTP_AUTHORIZATION), then the oauth_token query parameter, then the
 * oauth_token body parameter, in that order; other schemes and malformed tokens are not taken.
 *
 * The request is an ezpRestRequest built by the test and the header is set in $_SERVER and put back. No database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group rest
 */

class ezpOauthUtilityTokenTest extends PHPUnit\Framework\TestCase
{
    private $authorization;

    protected function setUp(): void
    {
        if ( !class_exists( 'ezcMvcRequest' ) )
            $this->markTestSkipped( 'the MVC component of the Zeta Components is not installed' );
        $this->authorization = array_key_exists( 'HTTP_AUTHORIZATION', $_SERVER ) ? array( $_SERVER['HTTP_AUTHORIZATION'] ) : null;
        unset( $_SERVER['HTTP_AUTHORIZATION'] );
    }

    protected function tearDown(): void
    {
        if ( $this->authorization === null )
            unset( $_SERVER['HTTP_AUTHORIZATION'] );
        else
            $_SERVER['HTTP_AUTHORIZATION'] = $this->authorization[0];
    }

    private static function request( $get = array(), $post = array() )
    {
        $request = new ezpRestRequest();
        $request->get = $get;
        $request->post = $post;
        return $request;
    }

    public static function tokenProvider()
    {
        return array(
            'header'                 => array( 'OAuth abc123', array(), array(), 'abc123' ),
            'header before query'    => array( 'OAuth fromheader', array( 'oauth_token' => 'fromquery' ), array(), 'fromheader' ),
            'query'                  => array( null, array( 'oauth_token' => 'fromquery' ), array(), 'fromquery' ),
            'query before body'      => array( null, array( 'oauth_token' => 'fromquery' ), array( 'oauth_token' => 'frombody' ), 'fromquery' ),
            'body'                   => array( null, array(), array( 'oauth_token' => 'frombody' ), 'frombody' ),
            'basic scheme'           => array( 'Basic dXNlcjpwYXNz', array(), array(), null ),
            'bearer scheme'          => array( 'Bearer abc123', array(), array(), null ),
            'bad characters'         => array( 'OAuth abc-123', array(), array(), null ),
            'two tokens'             => array( 'OAuth abc def', array(), array(), null ),
            'bad header then query'  => array( 'Basic x', array( 'oauth_token' => 'q' ), array(), 'q' ),
            'nothing'                => array( null, array(), array(), null ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('tokenProvider')]
    public function testGetToken( $header, $get, $post, $expected )
    {
        if ( $header !== null )
            $_SERVER['HTTP_AUTHORIZATION'] = $header;
        $this->assertSame( $expected, ezpOauthUtility::getToken( self::request( $get, $post ) ) );
    }
}
