<?php
/**
 * The catalogue and the declaration of every service of every domain (this covers the classes of all agents:
 * a domain registered in ezjscore.ini is walked as soon as it exists).
 *
/**
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expServicesCoreTestCase.php';

class expServicesCatalogTest extends expServicesCoreTestCase
{
    /** @return array 'domain::method' => array( domain, method ) for the data provider */
    public static function allServices()
    {
        self::boot();
        $list = array();
        if ( self::$bootError !== null )
            return array( 'no kernel' => array( null, null ) );
        foreach ( expServicesCatalog::all() as $d )
            $list[$d['domain'] . '::' . $d['method']] = array( $d['domain'], $d['method'] );
        return $list;
    }

    protected function descriptor( $domain, $method )
    {
        if ( $domain === null )
            $this->markTestSkipped( 'no kernel' );
        foreach ( expServicesCatalog::all( $domain ) as $d )
            if ( $d['method'] === $method )
                return $d;
        $this->fail( "$domain::$method is not in the catalogue" );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'allServices' )]
    public function testServiceIsFullyDeclared( $domain, $method )
    {
        $d = $this->descriptor( $domain, $method );
        $this->assertIsString( $d['summary'] );
        $this->assertNotSame( '', trim( $d['summary'] ), 'summary' );
        $this->assertIsArray( $d['args'], 'args' );
        $this->assertNotSame( '', trim( (string)$d['returns'] ), 'returns' );
        $this->assertIsBool( $d['write'] );
        $access = $d['access'];
        $this->assertTrue( $access === 'public' || $access === 'user'
            || ( is_array( $access ) && count( $access ) === 2 && is_string( $access[0] ) && is_string( $access[1] ) ), 'access is public, user or array( module, function )' );
        if ( $d['write'] )
            $this->assertNotSame( 'public', $access, 'a write is never public' );
        foreach ( $d['args'] as $name => $type )
        {
            $this->assertIsString( $name );
            $this->assertIsString( $type );
        }
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'allServices' )]
    public function testServiceMethodIsCallable( $domain, $method )
    {
        $d = $this->descriptor( $domain, $method );
        $this->assertTrue( is_callable( array( $d['class'], $method ) ), $d['class'] . '::' . $method . ' is callable' );
        $ref = new ReflectionMethod( $d['class'], $method );
        $this->assertTrue( $ref->isStatic() && $ref->isPublic(), 'public static' );
        $this->assertTrue( is_subclass_of( $d['class'], 'expServiceBase' ) );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'allServices' )]
    public function testServiceHasACallUrl( $domain, $method )
    {
        $d = $this->descriptor( $domain, $method );
        $this->assertStringStartsWith( 'ezjscore/call/exp' . $domain . '::' . $method, $d['call'] );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'allServices' )]
    public function testServiceIsRoutable( $domain, $method )
    {
        $this->descriptor( $domain, $method );
        $router = ezjscServerRouter::getInstance( array( 'exp' . $domain, $method ), true, true );
        $this->assertNotNull( $router, "exp$domain::$method is routable by ezjscore" );
    }

    public function testCatalogEnvelope()
    {
        $r = $this->ok( 'expServicesCatalog', 'catalog' );
        $this->assertGreaterThanOrEqual( 80, count( $r['data'] ) );
        $this->assertSame( count( $r['data'] ), $r['meta']['total'] );
        foreach ( array( 'domain', 'class', 'method', 'summary', 'access', 'write', 'args', 'returns', 'call' ) as $k )
            $this->assertArrayHasKey( $k, $r['data'][0] );
    }

    public function testCatalogOneDomain()
    {
        $r = $this->ok( 'expServicesCatalog', 'catalog', array( 'session' ) );
        $this->assertCount( count( expSessionServices::$services ), $r['data'] );
        foreach ( $r['data'] as $d )
            $this->assertSame( 'session', $d['domain'] );
    }

    public function testCatalogUnknownDomain()
    {
        $this->assertError( $this->call( 'expServicesCatalog', 'catalog', array( 'nothing' ) ), 404 );
    }

    public function testSchema()
    {
        $r = $this->ok( 'expServicesCatalog', 'schema', array( 'system' ) );
        $this->assertSame( 'expSystemServices', $r['data']['class'] );
        $this->assertCount( count( expSystemServices::$services ), $r['data']['services'] );
    }

    public function testSchemaNeedsDomain()
    {
        $this->assertError( $this->call( 'expServicesCatalog', 'schema' ), 400 );
        $this->assertError( $this->call( 'expServicesCatalog', 'schema', array( 'zzz' ) ), 404 );
    }

    public function testService()
    {
        $r = $this->ok( 'expServicesCatalog', 'service', array( 'system', 'version' ) );
        $this->assertSame( 'ezjscore/call/expsystem::version', $r['data']['call'] );
        $this->assertError( $this->call( 'expServicesCatalog', 'service', array( 'system', 'zzz' ) ), 404 );
    }

    public function testDomains()
    {
        $r = $this->ok( 'expServicesCatalog', 'domains' );
        $byDomain = array();
        foreach ( $r['data'] as $d )
            $byDomain[$d['domain']] = $d;
        foreach ( array( 'session', 'system', 'ini', 'cache', 'cronjob', 'extension', 'package', 'workflow', 'velocity', 'rad', 'debug', 'services' ) as $core )
            $this->assertArrayHasKey( $core, $byDomain, "domain $core" );
    }

    public function testVersion()
    {
        $r = $this->ok( 'expServicesCatalog', 'version' );
        $this->assertSame( expServicesCatalog::VERSION, $r['data']['expservices'] );
        $this->assertSame( eZPublishSDK::version(), $r['data']['exponential'] );
    }

    public function testVersionMatchesTheExtensionFiles()
    {
        $root = dirname( __DIR__, 5 ) . '/extension/expservices';
        $this->assertStringContainsString( "'Version'   => \"" . expServicesCatalog::VERSION . "\"", file_get_contents( $root . '/ezinfo.php' ) );
        $this->assertStringContainsString( '<version>' . expServicesCatalog::VERSION . '</version>', file_get_contents( $root . '/extension.xml' ) );
    }

    public function testCatalogIsPublic()
    {
        $this->loginAnonymous();
        $r = $this->call( 'expServicesCatalog', 'catalog' );
        $this->assertTrue( $r['ok'] );
    }

    public function testEveryDomainClassIsRegistered()
    {
        $classes = expServicesCatalog::domainClasses();
        $this->assertSame( count( $classes ), count( array_unique( $classes ) ), 'one class per domain' );
        foreach ( $classes as $domain => $class )
            $this->assertSame( $class, ezjscServerRouter::getInstance( array( 'exp' . $domain, 'zz' ), true, false ) ? $class : null );
    }

    public function testNoServiceNameIsDeclaredTwice()
    {
        $seen = array();
        foreach ( expServicesCatalog::all() as $d )
        {
            $key = $d['domain'] . '::' . $d['method'];
            $this->assertArrayNotHasKey( $key, $seen );
            $seen[$key] = true;
        }
    }

    public function testTotalServicesOfTheCoreDomains()
    {
        $n = 0;
        foreach ( array( 'session', 'system', 'ini', 'cache', 'cronjob', 'extension', 'package', 'workflow', 'velocity', 'rad', 'debug', 'services' ) as $d )
            $n += count( expServicesCatalog::all( $d ) );
        $this->assertGreaterThanOrEqual( 80, $n );
    }
}
