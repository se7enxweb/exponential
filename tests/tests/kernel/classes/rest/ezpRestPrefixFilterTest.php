<?php
/**
 * Tests of the REST prefix filter (ezpRestDefaultRegexpPrefixFilter): the API provider and version read off the
 * request URI, the URI without them, the defaults when they are missing, and that a request does not keep the
 * provider and version of the request before it (they are static, and a persistent worker serves many requests).
 *
 * Requests are ezpRestRequest objects built by the test. No database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group rest
 */

class ezpRestPrefixFilterTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        if ( !class_exists( 'ezcMvcRequest' ) )
            $this->markTestSkipped( 'the MVC component of the Zeta Components is not installed' );
    }

    protected function tearDown(): void
    {
        // leave no provider or version behind for other tests
        if ( class_exists( 'ezcMvcRequest' ) )
            self::filter( '/' );
    }

    private static function filter( $uri, $prefix = '/api' )
    {
        $request = new ezpRestRequest();
        $request->uri = $uri;
        $filter = new ezpRestDefaultRegexpPrefixFilter( $request, $prefix );
        $filter->filter();
        $filter->filterRequestUri();
        return $request;
    }

    public static function uriProvider()
    {
        return array(
            'provider and version' => array( '/api/ezp/v2/content/node/2', 'ezp', 2, '/api/content/node/2' ),
            'version 1'            => array( '/api/ezp/v1/content', 'ezp', 1, '/api/content' ),
            'no version'           => array( '/api/ezp/content/node/2', 'ezp', 1, '/api/content/node/2' ),
            'other provider'       => array( '/api/myapp/v12/items', 'myapp', 12, '/api/items' ),
            'not under the prefix' => array( '/other/ezp/v2/x', false, 1, '/other/ezp/v2/x' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('uriProvider')]
    public function testProviderVersionAndUri( $uri, $provider, $version, $filteredUri )
    {
        $request = self::filter( $uri );
        $this->assertSame( $provider, ezpRestPrefixFilterInterface::getApiProviderName() );
        $this->assertSame( $version, ezpRestPrefixFilterInterface::getApiVersion() );
        $this->assertSame( $filteredUri, $request->uri );
        $this->assertSame( '/api', ezpRestPrefixFilterInterface::getApiPrefix() );
    }

    public function testARequestDoesNotKeepTheVersionOfThePreviousOne()
    {
        self::filter( '/api/ezp/v3/content' );
        $this->assertSame( 3, ezpRestPrefixFilterInterface::getApiVersion() );
        self::filter( '/somewhere/else' );
        $this->assertSame( 1, ezpRestPrefixFilterInterface::getApiVersion() );
        $this->assertFalse( ezpRestPrefixFilterInterface::getApiProviderName() );
    }

    public function testPrefixWithSpecialCharacters()
    {
        $request = self::filter( '/rest.v/ezp/v2/x', '/rest.v' );
        $this->assertSame( 2, ezpRestPrefixFilterInterface::getApiVersion() );
        $this->assertSame( '/rest.v/x', $request->uri );
        self::filter( '/restXv/ezp/v2/x', '/rest.v' );
        $this->assertFalse( ezpRestPrefixFilterInterface::getApiProviderName(), 'the dot is no wildcard' );
    }
}
