<?php
/**
 * Tests of the REST routes ezpMvcRailsRoute and ezpMvcRegexpRoute: a matching URI gives the controller and the
 * action of the request's HTTP method with the URI's variables and the defaults, OPTIONS is always answered with
 * the supported methods, a method the route does not map is refused with the allowed ones
 * (ezpRouteMethodNotAllowedException), a URI that does not match gives null, and prefixes are applied.
 *
 * Requests are ezpRestRequest objects built by the test. No database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group rest
 */

class ezpRestRoutesTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        if ( !class_exists( 'ezcMvcRequest' ) )
            $this->markTestSkipped( 'the MVC component of the Zeta Components is not installed' );
    }

    private static function request( $uri, $protocol = 'http-get' )
    {
        $request = new ezpRestRequest();
        $request->uri = $uri;
        $request->protocol = $protocol;
        $request->variables = array();
        return $request;
    }

    private static function railsRoute()
    {
        return new ezpMvcRailsRoute( '/content/node/:nodeId', 'T1Controller',
                                     array( 'http-get' => 'viewNode', 'http-delete' => 'removeNode' ),
                                     array( 'format' => 'json' ) );
    }

    public function testRailsRouteMatchesWithVariablesAndDefaults()
    {
        $request = self::request( '/content/node/42' );
        $info = self::railsRoute()->matches( $request );
        $this->assertInstanceOf( 'ezcMvcRoutingInformation', $info );
        $this->assertSame( 'T1Controller', $info->controllerClass );
        $this->assertSame( 'viewNode', $info->action );
        $this->assertSame( '42', $request->variables['nodeId'] );
        $this->assertSame( 'json', $request->variables['format'] );
    }

    public function testRailsRouteChoosesTheActionOfTheMethod()
    {
        $info = self::railsRoute()->matches( self::request( '/content/node/42', 'http-delete' ) );
        $this->assertSame( 'removeNode', $info->action );
    }

    public function testRailsRouteAnswersOptionsWithTheSupportedMethods()
    {
        $request = self::request( '/content/node/42', 'http-options' );
        $info = self::railsRoute()->matches( $request );
        $this->assertSame( 'httpOptions', $info->action );
        $this->assertSame( array( 'GET', 'DELETE', 'OPTIONS' ), $request->variables['supported_http_methods'] );
    }

    public function testRailsRouteRefusesAMethodItDoesNotMap()
    {
        try
        {
            self::railsRoute()->matches( self::request( '/content/node/42', 'http-put' ) );
            $this->fail( 'no exception' );
        }
        catch ( ezpRouteMethodNotAllowedException $e )
        {
            $this->assertSame( array( 'GET', 'DELETE', 'OPTIONS' ), $e->getAllowedMethods() );
        }
    }

    public function testRailsRouteDoesNotMatchAnotherUri()
    {
        $this->assertNull( self::railsRoute()->matches( self::request( '/content/object/42' ) ) );
    }

    public function testRailsRouteWithAStringActionAndAProtocol()
    {
        $route = new ezpMvcRailsRoute( '/ping', 'T1Controller', 'ping' );
        $this->assertSame( 'ping', $route->matches( self::request( '/ping' ) )->action );
        $legacy = new ezpMvcRailsRoute( '/ping', 'T1Controller', 'store', array(), 'http-post' );
        $this->assertSame( 'store', $legacy->matches( self::request( '/ping', 'http-post' ) )->action );
        $this->expectException( 'ezpRouteMethodNotAllowedException' );
        $legacy->matches( self::request( '/ping', 'http-get' ) );
    }

    public function testRailsRoutePrefix()
    {
        $route = self::railsRoute();
        $route->prefix( '/api/ezp' );
        $this->assertNotNull( $route->matches( self::request( '/api/ezp/content/node/7' ) ) );
        $this->assertNull( $route->matches( self::request( '/content/node/7' ) ) );
    }

    public function testRegexpRouteKeepsOnlyNamedVariables()
    {
        $route = new ezpMvcRegexpRoute( '@^/content/(?P<nodeId>\d+)/(\w+)$@', 'T1Controller', array( 'http-get' => 'view' ) );
        $request = self::request( '/content/42/full' );
        $info = $route->matches( $request );
        $this->assertSame( 'view', $info->action );
        $this->assertSame( array( 'nodeId' => '42' ), $request->variables );
        $this->assertNull( $route->matches( self::request( '/content/abc/full' ) ) );
    }

    public function testRegexpRoutePrefixKeepsTheDelimiter()
    {
        $route = new ezpMvcRegexpRoute( '@^/items$@', 'T1Controller', 'list' );
        $route->prefix( '/api' );
        $this->assertNotNull( $route->matches( self::request( '/api/items' ) ) );
    }

    /**
     * A versioned route answers only for its API version, which the prefix filter reads off the URI.
     */
    public function testVersionedRouteMatchesOnlyItsVersion()
    {
        $route = new ezpRestVersionedRoute( new ezpMvcRailsRoute( '/content/node/:nodeId', 'T1Controller', 'view' ), 2 );
        $request = self::request( '/api/ezp/v2/content/node/5' );
        $filter = new ezpRestDefaultRegexpPrefixFilter( $request, '/api' );
        $filter->filter();
        $filter->filterRequestUri();
        $route->prefix( '/api' );
        try
        {
            $this->assertNotNull( $route->matches( $request ) );
            $this->assertSame( '/api/ezp/v2/content/node/9', $route->generateUrl( array( 'nodeId' => 9 ) ) );

            $other = self::request( '/api/ezp/v1/content/node/5' );
            $filter = new ezpRestDefaultRegexpPrefixFilter( $other, '/api' );
            $filter->filter();
            $filter->filterRequestUri();
            $this->assertNull( $route->matches( $other ) );
        }
        finally
        {
            $reset = new ezpRestDefaultRegexpPrefixFilter( self::request( '/' ), '/api' );
            $reset->filter();
        }
    }

    public function testRegexpRouteRefusesAMethodItDoesNotMap()
    {
        $route = new ezpMvcRegexpRoute( '@^/items$@', 'T1Controller', 'list' );
        $this->expectException( 'ezpRouteMethodNotAllowedException' );
        $route->matches( self::request( '/items', 'http-post' ) );
    }
}
