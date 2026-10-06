<?php
/**
 * Which REST routes need authentication and how it is run: ezpRestIniRouteFilter (rest.ini [RouteSettings]
 * SkipFilter[], controller_action or controller_*, per API version), ezpRestVersionedRoute (a route answers only for
 * its API version, and builds versioned URLs), and ezpRestAuthConfiguration (HTTPS required, the route filter, and
 * an authentication style that redirects or gives no user).
 *
 * No database: the authentication style is a stand-in. rest.ini settings and the prefix filter's static state are
 * set by the tests and put back.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group rest
 */

class ezpRestRouteSecurityTestStyle extends ezpRestAuthenticationStyle
{
    public $setupResult;
    public $authenticateResult;
    public $calls = array();

    public function setup( ezcMvcRequest $request )
    {
        $this->calls[] = 'setup';
        return $this->setupResult;
    }

    public function authenticate( $auth, ezcMvcRequest $request )
    {
        $this->calls[] = 'authenticate';
        return $this->authenticateResult;
    }
}

class ezpRestRouteSecurityTest extends PHPUnit\Framework\TestCase
{
    private $saved = array();
    private $statics = array();

    protected function setUp(): void
    {
        if ( !class_exists( 'ezcMvcRequest' ) )
            $this->markTestSkipped( 'the MVC component of the Zeta Components is not installed' );
        chdir( dirname( __DIR__, 5 ) );
        foreach ( array( array( 'ezpRestPrefixFilterInterface', 'version' ), array( 'ezpRestPrefixFilterInterface', 'apiProvider' ),
                         array( 'ezpRestPrefixFilterInterface', 'apiPrefix' ), array( 'ezpRestIniRouteFilter', 'skipRoutes' ),
                         array( 'ezpRestIniRouteFilter', 'parsedSkipRoutes' ) ) as $static )
        {
            $property = new ReflectionProperty( $static[0], $static[1] );
            $this->statics[] = array( $property, $property->getValue() );
        }
        $this->setStatic( 'ezpRestIniRouteFilter', 'parsedSkipRoutes', null );
    }

    protected function tearDown(): void
    {
        foreach ( $this->statics as $static )
            $static[0]->setValue( null, $static[1] );
        $this->statics = array();
        $ini = eZINI::instance( 'rest.ini' );
        foreach ( array_reverse( $this->saved ) as $entry )
        {
            if ( $entry[2] === null )
                $ini->removeSetting( $entry[0], $entry[1] );
            else
                $ini->setVariable( $entry[0], $entry[1], $entry[2][0] );
        }
        $this->saved = array();
    }

    private function setStatic( $class, $name, $value )
    {
        ( new ReflectionProperty( $class, $name ) )->setValue( null, $value );
    }

    private function setting( $group, $name, $value )
    {
        $ini = eZINI::instance( 'rest.ini' );
        $this->saved[] = array( $group, $name, $ini->hasVariable( $group, $name ) ? array( $ini->variable( $group, $name ) ) : null );
        $ini->setVariable( $group, $name, $value );
    }

    private function version( $version, $provider = null, $prefix = null )
    {
        $this->setStatic( 'ezpRestPrefixFilterInterface', 'version', $version );
        $this->setStatic( 'ezpRestPrefixFilterInterface', 'apiProvider', $provider );
        $this->setStatic( 'ezpRestPrefixFilterInterface', 'apiPrefix', $prefix );
    }

    // ---------------------------------------------------------------- prefix filter state

    public function testPrefixFilterDefaults()
    {
        $this->version( null );
        $this->assertSame( 1, ezpRestPrefixFilterInterface::getApiVersion() );
        $this->assertFalse( ezpRestPrefixFilterInterface::getApiProviderName() );
        $this->assertFalse( ezpRestPrefixFilterInterface::getApiPrefix() );
        $this->version( 3, 'k1', '/api' );
        $this->assertSame( 3, ezpRestPrefixFilterInterface::getApiVersion() );
        $this->assertSame( 'k1', ezpRestPrefixFilterInterface::getApiProviderName() );
        $this->assertSame( '/api', ezpRestPrefixFilterInterface::getApiPrefix() );
    }

    // ---------------------------------------------------------------- route filter

    private function routeFilter( array $skip )
    {
        $this->setting( 'RouteSettings', 'SkipFilter', $skip );
        return new ezpRestIniRouteFilter();
    }

    private static function info( $controller, $action )
    {
        return new ezcMvcRoutingInformation( '/x', $controller, $action );
    }

    public function testRoutesNotListedNeedAuthentication()
    {
        $this->version( null );
        $filter = $this->routeFilter( array( 'ezpRestErrorController_show' ) );
        $this->assertTrue( $filter->shallDoActionWithRoute( self::info( 'k1Controller', 'view' ) ) );
        $this->assertTrue( $filter->shallDoActionWithRoute( self::info( 'ezpRestErrorController', 'other' ) ) );
    }

    public function testListedActionIsSkippedForVersionOne()
    {
        $this->version( null );
        $filter = $this->routeFilter( array( 'ezpRestErrorController_show' ) );
        $this->assertFalse( $filter->shallDoActionWithRoute( self::info( 'ezpRestErrorController', 'show' ) ) );
        $this->version( 2 );
        $this->assertTrue( $filter->shallDoActionWithRoute( self::info( 'ezpRestErrorController', 'show' ) ), 'listed for version 1 only' );
    }

    public function testVersionAndWildcard()
    {
        $this->version( 2 );
        $filter = $this->routeFilter( array( 'k1Controller_*;2', 'k1Controller_edit;1' ) );
        $this->assertFalse( $filter->shallDoActionWithRoute( self::info( 'k1Controller', 'view' ) ) );
        $this->assertTrue( $filter->shallDoActionWithRoute( self::info( 'k1Controller', 'edit' ) ), 'the action rule wins over the wildcard' );
        $this->version( 1 );
        $this->assertTrue( $filter->shallDoActionWithRoute( self::info( 'k1Controller', 'view' ) ) );
        $this->assertFalse( $filter->shallDoActionWithRoute( self::info( 'k1Controller', 'edit' ) ) );
    }

    public function testShippedSettingsSkipTheAuthenticationRoutes()
    {
        $this->version( null );
        $filter = new ezpRestIniRouteFilter();
        foreach ( array( 'ezpRestErrorController_show', 'ezpRestAuthController_basicAuth', 'ezpRestAuthController_oauthRequired',
                         'ezpRestOauthTokenController_handleRequest' ) as $route )
        {
            list( $controller, $action ) = explode( '_', $route );
            $this->assertFalse( $filter->shallDoActionWithRoute( self::info( $controller, $action ) ), $route );
        }
        $this->assertInstanceOf( 'ezpRestIniRouteFilter', ezpRestRouteFilterInterface::getRouteFilter() );
    }

    public function testMissingRouteFilterClassIsReported()
    {
        $this->setting( 'RouteSettings', 'RouteSettingImpl', 'k1NoSuchRouteFilter' );
        $this->expectException( 'ezpRestRouteSecurityFilterNotFoundException' );
        ezpRestRouteFilterInterface::getRouteFilter();
    }

    // ---------------------------------------------------------------- versioned route

    private static function request( $uri, $protocol = 'http-get' )
    {
        $request = new ezpRestRequest( null, $protocol, '', $uri );
        $request->variables = array();
        return $request;
    }

    public function testVersionedRouteAnswersOnlyForItsVersion()
    {
        $route = new ezpRestVersionedRoute( new ezpMvcRailsRoute( '/content/node/:nodeId', 'k1Controller', 'view' ), '2' );
        $this->version( 2 );
        $request = self::request( '/content/node/5' );
        $info = $route->matches( $request );
        $this->assertSame( 'view', $info->action );
        $this->assertSame( '5', $request->variables['nodeId'] );
        $this->version( 1 );
        $this->assertNull( $route->matches( self::request( '/content/node/5' ) ) );
        $this->version( null );
        $this->assertNull( $route->matches( self::request( '/content/node/5' ) ) );
    }

    public function testVersionedRoutePrefixAndUrl()
    {
        $route = new ezpRestVersionedRoute( new ezpMvcRailsRoute( '/content/node/:nodeId', 'k1Controller', 'view' ), 2 );
        $route->prefix( '/api' );
        $this->version( 2, 'k1', '/api' );
        $this->assertNotNull( $route->matches( self::request( '/api/content/node/7' ) ) );
        $this->assertSame( '/api/k1/v2/content/node/7', $route->generateUrl( array( 'nodeId' => 7 ) ) );
        $this->version( 2, null, '/api' );
        $this->assertSame( '/api/v2/content/node/7', $route->generateUrl( array( 'nodeId' => 7 ) ) );
    }

    // ---------------------------------------------------------------- auth configuration

    private function authConfiguration( $request, $style )
    {
        $config = new ezpRestAuthConfiguration( new ezcMvcRoutingInformation( '/x', 'k1Controller', 'view' ), $request );
        if ( $style )
            $config->setFilter( $style );
        return $config;
    }

    public function testHttpsRequired()
    {
        $this->setting( 'Authentication', 'RequireHTTPS', 'enabled' );
        $request = self::request( '/x' );
        $request->isEncrypted = false;
        try
        {
            $this->authConfiguration( $request, null )->filter();
            $this->fail( 'no exception' );
        }
        catch ( ezpRestHTTPSRequiredException $e )
        {
            $this->assertTrue( $request->isEncrypted, 'the fatal error request that follows is not refused again' );
        }
    }

    public function testStyleThatRedirectsInSetup()
    {
        $this->version( null );
        $this->setting( 'Authentication', 'RequireHTTPS', 'disabled' );
        $this->setting( 'Authentication', 'RequireAuthentication', 'enabled' );
        $this->setting( 'RouteSettings', 'SkipFilter', array() );
        $this->setStatic( 'ezpRestIniRouteFilter', 'parsedSkipRoutes', null );
        $style = new ezpRestRouteSecurityTestStyle();
        $redirect = new ezcMvcInternalRedirect( self::request( '/api/auth/http-basic-auth' ) );
        $style->setupResult = $redirect;
        $this->assertSame( $redirect, $this->authConfiguration( self::request( '/x' ), $style )->filter() );
        $this->assertSame( array( 'setup' ), $style->calls );
    }

    public function testStyleThatRedirectsInAuthenticate()
    {
        $this->version( null );
        $this->setting( 'Authentication', 'RequireHTTPS', 'disabled' );
        $this->setting( 'Authentication', 'RequireAuthentication', 'enabled' );
        $this->setting( 'RouteSettings', 'SkipFilter', array() );
        $style = new ezpRestRouteSecurityTestStyle();
        $style->setupResult = 'credentials';
        $redirect = new ezcMvcInternalRedirect( self::request( '/api/auth/oauth/login' ) );
        $style->authenticateResult = $redirect;
        $this->assertSame( $redirect, $this->authConfiguration( self::request( '/x' ), $style )->filter() );
        $this->assertSame( array( 'setup', 'authenticate' ), $style->calls );
    }

    public function testStyleThatGivesNoUser()
    {
        $this->version( null );
        $this->setting( 'Authentication', 'RequireHTTPS', 'disabled' );
        $this->setting( 'Authentication', 'RequireAuthentication', 'enabled' );
        $this->setting( 'RouteSettings', 'SkipFilter', array() );
        $style = new ezpRestRouteSecurityTestStyle();
        $style->setupResult = 'credentials';
        $style->authenticateResult = null;
        $this->assertNull( $this->authConfiguration( self::request( '/x' ), $style )->filter() );
        $this->assertNull( $style->getUser() );
    }

    public function testMissingAuthenticationStyleIsReported()
    {
        $this->version( null );
        $this->setting( 'Authentication', 'RequireHTTPS', 'disabled' );
        $this->setting( 'Authentication', 'RequireAuthentication', 'enabled' );
        $this->setting( 'Authentication', 'AuthenticationStyle', 'k1NoSuchStyle' );
        $this->setting( 'RouteSettings', 'SkipFilter', array() );
        $this->expectException( 'ezpRestAuthStyleNotFoundException' );
        $this->authConfiguration( self::request( '/x' ), null )->filter();
    }

    public function testStyleKeepsThePrefixAndUser()
    {
        $style = new ezpRestRouteSecurityTestStyle();
        $prefix = ( new ReflectionProperty( 'ezpRestAuthenticationStyle', 'prefix' ) )->getValue( $style );
        $this->assertSame( eZINI::instance( 'rest.ini' )->variable( 'System', 'ApiPrefix' ), $prefix );
        $user = new eZUser( array( 'contentobject_id' => 14 ) );
        $style->setUser( $user );
        $this->assertSame( $user, $style->getUser() );
    }
}
