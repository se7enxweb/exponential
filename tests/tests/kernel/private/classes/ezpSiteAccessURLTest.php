<?php
/**
 * ezpSiteAccessURL::root(): the root URL of a siteaccess from the matching rules of site.ini (URI element or map,
 * host map, SiteURL), the scheme, host and port of this request, RemoveSiteAccessIfDefaultAccess, and its helpers
 * (host:port split, "a;b" map items, the URI element of a siteaccess).
 *
 * No database. The rules are injected into site.ini (eZINI::injectSettings(), put back in tearDown()) for
 * siteaccesses that do not exist (k1-*), and the request is $_SERVER over HTTPS.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class ezpSiteAccessURLTest extends PHPUnit\Framework\TestCase
{
    private $server;
    private $injected;
    private $index;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->server = $_SERVER;
        $property = new ReflectionProperty( 'eZINI', 'injectedSettings' );
        $this->injected = $property->getValue();
        foreach ( array( 'HTTP_X_FORWARDED_HOST', 'HTTP_X_FORWARDED_PROTO', 'HTTP_X_FORWARDED_PORT', 'HTTP_X_FORWARDED_SERVER', 'SERVER_PORT' ) as $key )
            unset( $_SERVER[$key] );
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['HTTP_HOST'] = 'www.k1.example.invalid';
        $index = trim( (string)eZSys::indexFile( false ), '/' );
        $this->index = $index !== '' ? $index . '/' : '';
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->server;
        eZINI::injectSettings( $this->injected );
        // Loading a siteaccess's settings registers eZExecution's shutdown and exception handlers once per process
        $current = set_exception_handler( null );
        restore_exception_handler();
        if ( $current === array( 'eZExecution', 'defaultExceptionHandler' ) && $this->handler !== $current )
            restore_exception_handler();
    }

    private $handler;

    protected function assertPreConditions(): void
    {
        $this->handler = set_exception_handler( null );
        restore_exception_handler();
    }

    private function rules( array $siteAccessSettings, $siteURL = null )
    {
        $settings = $this->injected;
        $settings['site.ini']['SiteSettings']['DefaultAccess'] = 'k1-eng';
        if ( $siteURL !== null )
            $settings['site.ini']['SiteSettings']['SiteURL'] = $siteURL;
        $settings['site.ini']['SiteAccessSettings'] = $siteAccessSettings + array(
            'AvailableSiteAccessList' => array( 'k1-eng', 'k1-ger', 'k1-admin' ),
            'MatchOrder' => 'uri',
            'URIMatchType' => 'element',
            'URIMatchElement' => '1',
            'HostMatchMapItems' => array(),
            'RemoveSiteAccessIfDefaultAccess' => 'disabled',
        );
        eZINI::injectSettings( $settings );
    }

    public function testUnknownSiteAccessHasNoUrl()
    {
        $this->rules( array() );
        $this->assertFalse( ezpSiteAccessURL::root( 'k1-nothere' ) );
    }

    public function testUriElementOnThisHost()
    {
        $this->rules( array() );
        $this->assertSame( 'https://www.k1.example.invalid/' . $this->index . 'k1-ger/', ezpSiteAccessURL::root( 'k1-ger' ) );
    }

    public function testDefaultSiteAccessWhenNoneIsGiven()
    {
        $this->rules( array() );
        $this->assertSame( 'https://www.k1.example.invalid/' . $this->index . 'k1-eng/', ezpSiteAccessURL::root() );
        $this->assertSame( ezpSiteAccessURL::root(), ezpSiteAccessURL::root( '' ) );
    }

    public function testDefaultSiteAccessLeftOutWhenRemoved()
    {
        $this->rules( array( 'RemoveSiteAccessIfDefaultAccess' => 'enabled' ) );
        $this->assertSame( 'https://www.k1.example.invalid/' . $this->index, ezpSiteAccessURL::root( 'k1-eng' ) );
        $this->assertSame( 'https://www.k1.example.invalid/' . $this->index . 'k1-ger/', ezpSiteAccessURL::root( 'k1-ger' ) );
    }

    public function testVisitorPortIsKept()
    {
        $this->rules( array() );
        $_SERVER['HTTP_HOST'] = 'www.k1.example.invalid:8443';
        $this->assertSame( 'https://www.k1.example.invalid:8443/' . $this->index . 'k1-ger/', ezpSiteAccessURL::root( 'k1-ger' ) );
    }

    public function testServerPortWhenTheHostHasNone()
    {
        $this->rules( array() );
        $_SERVER['SERVER_PORT'] = '8443';
        $this->assertSame( 'https://www.k1.example.invalid:8443/' . $this->index . 'k1-ger/', ezpSiteAccessURL::root( 'k1-ger' ) );
        $_SERVER['SERVER_PORT'] = '443';
        $this->assertSame( 'https://www.k1.example.invalid/' . $this->index . 'k1-ger/', ezpSiteAccessURL::root( 'k1-ger' ) );
    }

    public function testUriMapKey()
    {
        $this->rules( array( 'URIMatchType' => 'map', 'URIMatchMapItems' => array( 'de;k1-ger', 'broken', ';k1-eng', 'en;k1-eng' ) ) );
        $this->assertSame( 'https://www.k1.example.invalid/' . $this->index . 'de/', ezpSiteAccessURL::root( 'k1-ger' ) );
        $this->assertSame( 'https://www.k1.example.invalid/' . $this->index . 'en/', ezpSiteAccessURL::root( 'k1-eng' ) );
    }

    public function testHostMapWhenNotReachableByUri()
    {
        $this->rules( array( 'MatchOrder' => 'host', 'HostMatchMapItems' => array( 'admin.k1.example.invalid;k1-admin', 'de.k1.example.invalid:8080;k1-ger' ) ) );
        $this->assertSame( 'https://admin.k1.example.invalid/' . $this->index, ezpSiteAccessURL::root( 'k1-admin' ) );
        $this->assertSame( 'https://de.k1.example.invalid:8080/' . $this->index, ezpSiteAccessURL::root( 'k1-ger' ) );
    }

    public function testHostMapCarriesTheVisitorPort()
    {
        $this->rules( array( 'MatchOrder' => 'host', 'HostMatchMapItems' => array( 'admin.k1.example.invalid;k1-admin' ) ) );
        $_SERVER['HTTP_HOST'] = 'www.k1.example.invalid:8443';
        $this->assertSame( 'https://admin.k1.example.invalid:8443/' . $this->index, ezpSiteAccessURL::root( 'k1-admin' ) );
    }

    public function testThisHostOfAnotherSiteAccessIsNotUsedForUri()
    {
        $this->rules( array( 'MatchOrder' => array( 'host', 'uri' ),
                             'HostMatchMapItems' => array( 'WWW.k1.example.invalid;k1-eng', 'admin.k1.example.invalid;k1-admin' ) ) );
        $this->assertSame( 'https://admin.k1.example.invalid/' . $this->index . 'k1-admin/', ezpSiteAccessURL::root( 'k1-admin' ) );
        $this->assertSame( 'https://www.k1.example.invalid/' . $this->index . 'k1-eng/', ezpSiteAccessURL::root( 'k1-eng' ) );
    }

    public function testSiteUrlWhenNeitherUriNorHostMap()
    {
        $this->rules( array( 'MatchOrder' => 'host' ), 'http://shop.k1.example.invalid:9000/path' );
        $this->assertSame( 'https://shop.k1.example.invalid:9000/' . $this->index, ezpSiteAccessURL::root( 'k1-ger' ) );
    }

    public function testSiteUrlAtLocalhostMeansThisHost()
    {
        $this->rules( array( 'MatchOrder' => 'host' ), 'localhost' );
        $_SERVER['HTTP_HOST'] = 'www.k1.example.invalid:8443';
        $this->assertSame( 'https://www.k1.example.invalid:8443/' . $this->index, ezpSiteAccessURL::root( 'k1-ger' ) );
    }

    public function testUriElementsOtherThanOneAreNotReachable()
    {
        $this->rules( array( 'MatchOrder' => 'uri', 'URIMatchElement' => '2' ), 'shop.k1.example.invalid' );
        $this->assertSame( 'https://shop.k1.example.invalid/' . $this->index, ezpSiteAccessURL::root( 'k1-ger' ) );
    }

    private function call( $method, array $arguments )
    {
        $closure = Closure::bind( function ( $method, $arguments ) { return call_user_func_array( array( 'ezpSiteAccessURL', $method ), $arguments ); }, null, 'ezpSiteAccessURL' );
        return $closure( $method, $arguments );
    }

    public static function hostProvider()
    {
        return array(
            'plain' => array( 'a.example.invalid', array( 'a.example.invalid', null ) ),
            'port' => array( 'a.example.invalid:8080', array( 'a.example.invalid', '8080' ) ),
            'spaces' => array( ' a.example.invalid:81 ', array( 'a.example.invalid', '81' ) ),
            'ipv6 with port' => array( '[::1]:8080', array( '[::1]', '8080' ) ),
            'ipv6 without port' => array( '[::1]', array( '[::1]', null ) ),
            'port not a number' => array( 'a:b', array( 'a:b', null ) ),
            'empty' => array( '', array( '', null ) ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('hostProvider')]
    public function testSplitHost( $host, $expected )
    {
        $this->assertSame( $expected, $this->call( 'splitHost', array( $host ) ) );
    }

    public function testMapItemsSkipIncompleteEntries()
    {
        $ini = new eZINI( 'site.ini', 'settings', null, false, false, false, false, false );
        $this->assertSame( array(), $this->call( 'mapItems', array( $ini, 'HostMatchMapItems' ) ) );
        $ini->setVariable( 'SiteAccessSettings', 'HostMatchMapItems', array( 'a;x', 'b', ';y', 'c;', 'd;z;extra' ) );
        $this->assertSame( array( array( 'a', 'x' ), array( 'd', 'z' ) ), $this->call( 'mapItems', array( $ini, 'HostMatchMapItems' ) ) );
    }

    public function testUriSegmentOfOtherMatchTypesIsNull()
    {
        $ini = new eZINI( 'site.ini', 'settings', null, false, false, false, false, false );
        $ini->setVariable( 'SiteAccessSettings', 'URIMatchType', 'element' );
        $this->assertSame( 'k1-x', $this->call( 'uriSegment', array( $ini, 'k1-x' ) ) );
        $ini->setVariable( 'SiteAccessSettings', 'URIMatchType', 'text' );
        $this->assertNull( $this->call( 'uriSegment', array( $ini, 'k1-x' ) ) );
        $ini->setVariable( 'SiteAccessSettings', 'URIMatchType', 'map' );
        $this->assertNull( $this->call( 'uriSegment', array( $ini, 'k1-x' ) ) );
    }
}
