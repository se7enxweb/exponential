<?php
/**
 * The GraphQL end point in the REST layer, without a database: its routes, the optional authentication of
 * /api/graphql (credentials when the request presents some, the anonymous user otherwise), the API key scope
 * graphql:read and the JSON of an answer.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 * @group rest
 */

class expGraphQLRestTest extends PHPUnit\Framework\TestCase
{
    /** @var array */
    protected $server;

    protected function setUp(): void
    {
        if ( !class_exists( 'ezcMvcRequest' ) )
            $this->markTestSkipped( 'the MVC component of the Zeta Components is not installed' );
        $this->server = $_SERVER;
        unset( $_SERVER['HTTP_AUTHORIZATION'], $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] );
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->server;
    }

    public function testTheProviderIsRegisteredWithItsRoutes()
    {
        $providers = eZINI::instance( 'rest.ini' )->variable( 'ApiProvider', 'ProviderClass' );
        $this->assertSame( 'expGraphQLRestProvider', $providers['graphql'] );
        $provider = new expGraphQLRestProvider();
        $this->assertSame( array( 'expGraphQLQuery', 'expGraphQLQuerySlash', 'expGraphQLPlayground' ), array_keys( $provider->getRoutes() ) );
        $this->assertInstanceOf( 'expGraphQLRestViewController', $provider->getViewController() );
        $cache = eZINI::instance( 'rest.ini' )->variable( 'expGraphQLRestController_CacheSettings', 'ApplicationCache' );
        $this->assertSame( 'disabled', $cache, 'a GraphQL answer depends on the user: never in the REST application cache' );
    }

    public function testTheEndPointAuthenticatesOnlyWhatPresentsCredentials()
    {
        $this->assertTrue( ezpRestAuthConfiguration::isOptional( new ezcMvcRoutingInformation( '', 'expGraphQLRestController', 'query' ) ) );
        $this->assertFalse( ezpRestAuthConfiguration::isOptional( new ezcMvcRoutingInformation( '', 'expHelpRestController', 'topic' ) ) );

        $request = new ezpRestRequest();
        $request->get = array();
        $request->post = array();
        $this->assertFalse( ezpRestAuthConfiguration::presentsCredentials( $request ) );
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer abc';
        $this->assertTrue( ezpRestAuthConfiguration::presentsCredentials( $request ), 'a wrong token is refused, never run as anonymous' );
        unset( $_SERVER['HTTP_AUTHORIZATION'] );
        $request->get = array( 'oauth_token' => 'abc' );
        $this->assertTrue( ezpRestAuthConfiguration::presentsCredentials( $request ) );
    }

    public function testATokenOrAKeySignsInForTheRequestOnly()
    {
        // no session, no cookie and no row in "Your devices" for every request of a client with a bearer credential
        $oauth = ( new ReflectionClass( 'ezpRestOauthAuthenticationStyle' ) )->newInstanceWithoutConstructor();
        $basic = ( new ReflectionClass( 'ezpRestBasicAuthStyle' ) )->newInstanceWithoutConstructor();
        $before = array( ezpOauthFilter::$tokenInfo, expApiKeyAuthFilter::$key );
        try
        {
            expApiKeyRest::reset();
            ezpOauthFilter::$tokenInfo = null;
            $this->assertFalse( ezpRestAuthConfiguration::signsInForRequestOnly( $oauth ), 'no token: the anonymous user' );
            $this->assertFalse( ezpRestAuthConfiguration::signsInForRequestOnly( $basic ) );
            $this->assertFalse( ezpRestAuthConfiguration::signsInForRequestOnly( null ) );
            ezpOauthFilter::$tokenInfo = ( new ReflectionClass( 'ezpRestToken' ) )->newInstanceWithoutConstructor();
            $this->assertTrue( ezpRestAuthConfiguration::signsInForRequestOnly( $oauth ), 'an OAuth token' );
            $this->assertFalse( ezpRestAuthConfiguration::signsInForRequestOnly( $basic ), 'a password keeps its session' );
            ezpOauthFilter::$tokenInfo = null;
            expApiKeyAuthFilter::$key = new expApiKey( array( 'id' => 1, 'user_id' => 14, 'scopes' => 'graphql:read' ) );
            $this->assertTrue( ezpRestAuthConfiguration::signsInForRequestOnly( $oauth ), 'a personal API key' );
        }
        finally
        {
            list( ezpOauthFilter::$tokenInfo, expApiKeyAuthFilter::$key ) = $before;
        }
        $config = file_get_contents( dirname( __DIR__, 5 ) . '/kernel/private/rest/classes/auth/auth_configuration.php' );
        $this->assertStringContainsString( 'ezpOauthFilter::$tokenInfo = null;', $config, 'reset per request: a worker keeps statics' );
        $this->assertStringContainsString( 'self::signsInForRequestOnly( $this->filter )', $config );
    }

    public function testAKeyNeedsTheScopeGraphqlRead()
    {
        $ini = eZINI::instance( 'rest.ini' );
        $catalogue = expApiKey::scopeCatalogue( $ini );
        $this->assertSame( 'content/read', $catalogue['graphql:read']['policy'] );
        $routeScopes = $ini->variable( 'ApiKeySettings', 'RouteScopes' );
        $this->assertSame( 'graphql:read', expApiKey::routeScope( 'expGraphQLRestController', 'query', 'http-post', $routeScopes, 'read' ) );
        $this->assertSame( array( 'read', 'graphql:read' ), expApiKey::normaliseScopes( 'graphql:read read nonsense', $catalogue ) );
    }

    public function testTheAnswerIsJson()
    {
        $this->assertSame( '{"data":{"name":"Künstler / Ärzte"}}', expGraphQLRestResponse::encode( array( 'data' => array( 'name' => 'Künstler / Ärzte' ) ) ) );
        $this->assertSame( '{"data":{"x":"a' . "\u{FFFD}" . 'b"}}', expGraphQLRestResponse::encode( array( 'data' => array( 'x' => "a\xC3b" ) ) ) );
        $refusal = expGraphQLServer::refusal( 'off', expGraphQLError::DISABLED );
        $this->assertSame( 'DISABLED', $refusal['errors'][0]['extensions']['code'] );
    }
}
