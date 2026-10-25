<?php
/**
 * Tests of how the REST layer finds the OAuth access token of a request (ezpOauthUtility::getToken()): the
 * Authorization header ("OAuth <token>" or "Bearer <token>", RFC 6750, from HTTP_AUTHORIZATION), then the oauth_token
 * query parameter, then the oauth_token body parameter, in that order; other schemes, malformed tokens and personal
 * API keys (expk_..., which expApiKeyRest takes) are not taken. The query parameter is only read with rest.ini
 * [OAuthSettings] AllowTokenInQuery=enabled, the body unless AllowTokenInBody=disabled.
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
            'bearer scheme'          => array( 'Bearer abc123', array(), array(), 'abc123' ),
            'bearer lower case'      => array( 'bearer abc123', array(), array(), 'abc123' ),
            'bearer api key'         => array( 'Bearer expk_abcdefghijkl_' . str_repeat( 'a', 40 ), array(), array(), null ),
            'bad characters'         => array( 'OAuth abc-123', array(), array(), null ),
            'two tokens'             => array( 'OAuth abc def', array(), array(), null ),
            'bad header then query'  => array( 'Basic x', array( 'oauth_token' => 'q' ), array(), 'q' ),
            'query list'             => array( null, array( 'oauth_token' => array( 'a' ) ), array(), null ),
            'body list'              => array( null, array(), array( 'oauth_token' => array( 'a' ) ), null ),
            'query list then body'   => array( null, array( 'oauth_token' => array( 'a' ) ), array( 'oauth_token' => 'b' ), 'b' ),
            'nothing'                => array( null, array(), array(), null ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('tokenProvider')]
    public function testGetToken( $header, $get, $post, $expected )
    {
        if ( $header !== null )
            $_SERVER['HTTP_AUTHORIZATION'] = $header;
        // every source, in the order of AllowTokenInQuery=enabled
        $this->assertSame( $expected, ezpOauthUtility::getToken( self::request( $get, $post ), array( 'header', 'get', 'post' ) ) );
    }

    private function withSettings( array $settings, callable $test )
    {
        $ini = eZINI::instance( 'rest.ini' );
        $saved = array();
        foreach ( $settings as $name => $value )
        {
            $saved[$name] = $ini->hasVariable( 'OAuthSettings', $name ) ? array( $ini->variable( 'OAuthSettings', $name ) ) : null;
            $ini->setVariable( 'OAuthSettings', $name, $value );
        }
        try
        {
            $test();
        }
        finally
        {
            foreach ( $saved as $name => $value )
            {
                if ( $value === null )
                    $ini->removeSetting( 'OAuthSettings', $name );
                else
                    $ini->setVariable( 'OAuthSettings', $name, $value[0] );
            }
        }
    }

    public function testShippedSettingsTakeHeaderAndBodyButNotQuery()
    {
        $ini = eZINI::instance( 'rest.ini' );
        $this->assertSame( 'disabled', $ini->variable( 'OAuthSettings', 'AllowTokenInQuery' ) );
        $this->assertSame( 'enabled', $ini->variable( 'OAuthSettings', 'AllowTokenInBody' ) );
        $this->withSettings( array( 'AllowTokenInQuery' => 'disabled', 'AllowTokenInBody' => 'enabled' ), function()
        {
            $this->assertSame( array( 'header', 'post' ), ezpOauthUtility::tokenSources() );
            $this->assertNull( ezpOauthUtility::getToken( self::request( array( 'oauth_token' => 'fromquery' ) ) ), 'a token in the address is not taken' );
            $this->assertSame( 'frombody', ezpOauthUtility::getToken( self::request( array( 'oauth_token' => 'fromquery' ), array( 'oauth_token' => 'frombody' ) ) ) );
            $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer fromheader';
            $this->assertSame( 'fromheader', ezpOauthUtility::getToken( self::request( array( 'oauth_token' => 'q' ), array( 'oauth_token' => 'b' ) ) ) );
        } );
    }

    public function testQueryOnlyWhenEnabled()
    {
        $this->withSettings( array( 'AllowTokenInQuery' => 'enabled', 'AllowTokenInBody' => 'enabled' ), function()
        {
            $this->assertSame( array( 'header', 'get', 'post' ), ezpOauthUtility::tokenSources() );
            $this->assertSame( 'fromquery', ezpOauthUtility::getToken( self::request( array( 'oauth_token' => 'fromquery' ), array( 'oauth_token' => 'frombody' ) ) ) );
        } );
        $this->withSettings( array( 'AllowTokenInQuery' => 'yes' ), function()
        {
            $this->assertNotContains( 'get', ezpOauthUtility::tokenSources(), 'only "enabled" turns the query on' );
        } );
    }

    public function testHeaderOnly()
    {
        $this->withSettings( array( 'AllowTokenInQuery' => 'disabled', 'AllowTokenInBody' => 'disabled' ), function()
        {
            $this->assertSame( array( 'header' ), ezpOauthUtility::tokenSources() );
            $this->assertNull( ezpOauthUtility::getToken( self::request( array( 'oauth_token' => 'q' ), array( 'oauth_token' => 'b' ) ) ) );
        } );
    }
}
