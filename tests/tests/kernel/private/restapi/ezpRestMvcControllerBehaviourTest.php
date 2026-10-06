<?php
/**
 * ezpRestMvcController without running an action: response groups (asked for and default ones), content variables,
 * the OPTIONS answer, the cache location, the cache key (the same request the same key, any difference another),
 * and the cache TTL and switch of rest.ini at action, controller and default level.
 *
 * No database and no cache is written: createResult() is not called. rest.ini settings and the prefix filter's
 * state are set by the test and put back.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group rest
 */

class ezpRestMvcControllerBehaviourTestRouter extends ezcMvcRouter
{
    public $info;

    public function createRoutes()
    {
        return array();
    }

    public function getRoutingInformation()
    {
        return $this->info;
    }
}

class ezpRestMvcControllerBehaviourTestController extends ezpRestMvcController
{
    public function call( $method, array $arguments = array() )
    {
        $reflection = new ReflectionMethod( 'ezpRestMvcController', $method );
        return $reflection->invokeArgs( $this, $arguments );
    }
}

class ezpRestMvcControllerBehaviourTest extends PHPUnit\Framework\TestCase
{
    private $saved = array();
    private $statics = array();

    protected function setUp(): void
    {
        if ( !class_exists( 'ezcMvcController' ) )
            $this->markTestSkipped( 'the MVC component of the Zeta Components is not installed' );
        chdir( dirname( __DIR__, 5 ) );
        foreach ( array( 'version', 'apiProvider' ) as $name )
        {
            $property = new ReflectionProperty( 'ezpRestPrefixFilterInterface', $name );
            $this->statics[] = array( $property, $property->getValue() );
        }
        ( new ReflectionProperty( 'ezpRestPrefixFilterInterface', 'version' ) )->setValue( null, 2 );
        ( new ReflectionProperty( 'ezpRestPrefixFilterInterface', 'apiProvider' ) )->setValue( null, 'k1' );
    }

    protected function tearDown(): void
    {
        foreach ( $this->statics as $static )
            $static[0]->setValue( null, $static[1] );
        $ini = eZINI::instance( 'rest.ini' );
        foreach ( array_reverse( $this->saved ) as $entry )
        {
            if ( $entry[2] === null )
            {
                if ( $ini->hasVariable( $entry[0], $entry[1] ) )
                    $ini->removeSetting( $entry[0], $entry[1] );
            }
            else
                $ini->setVariable( $entry[0], $entry[1], $entry[2][0] );
        }
        $this->saved = array();
    }

    private function setting( $group, $name, $value )
    {
        $ini = eZINI::instance( 'rest.ini' );
        $had = $ini->hasVariable( $group, $name );
        $this->saved[] = array( $group, $name, $had ? array( $ini->variable( $group, $name ) ) : null );
        if ( $value === null )
        {
            if ( $had )
                $ini->removeSetting( $group, $name );
        }
        else
            $ini->setVariable( $group, $name, $value );
    }

    private static function controller( array $variables = array(), array $contentVariables = array(), $action = 'view' )
    {
        $request = new ezpRestRequest( null, 'http-get', '', '/x' );
        $request->variables = $variables + array( 'ResponseGroups' => array() );
        $request->contentVariables = $contentVariables;
        $controller = new ezpRestMvcControllerBehaviourTestController( $action, $request );
        $router = new ezpRestMvcControllerBehaviourTestRouter( $request );
        $router->info = new ezcMvcRoutingInformation( '/x', 'K1\\Rest\\NodeController', $action );
        $controller->setRouter( $router );
        return $controller;
    }

    public function testResponseGroups()
    {
        $controller = self::controller( array( 'ResponseGroups' => array( 'Metadata' ) ) );
        $this->assertTrue( $controller->call( 'hasResponseGroup', array( 'Metadata' ) ) );
        $this->assertFalse( $controller->call( 'hasResponseGroup', array( 'Fields' ) ) );
        $controller->call( 'setDefaultResponseGroups', array( array( 'Fields', 'Metadata' ) ) );
        $this->assertTrue( $controller->call( 'hasResponseGroup', array( 'Fields' ) ) );
        $this->assertSame( array( 'Metadata', 'Fields' ), $controller->call( 'getResponseGroups' ) );
    }

    public function testContentVariables()
    {
        $controller = self::controller( array(), array( 'Translation' => 'eng-GB', 'Empty' => null ) );
        $this->assertTrue( $controller->call( 'hasContentVariable', array( 'Translation' ) ) );
        $this->assertFalse( $controller->call( 'hasContentVariable', array( 'Empty' ) ) );
        $this->assertSame( 'eng-GB', $controller->call( 'getContentVariable', array( 'Translation' ) ) );
        $this->assertNull( $controller->call( 'getContentVariable', array( 'Missing' ) ) );
        $this->assertSame( array( 'Translation' => 'eng-GB', 'Empty' => null ), $controller->call( 'getAllContentVariables' ) );
    }

    public function testHttpOptions()
    {
        $controller = self::controller( array( 'supported_http_methods' => array( 'GET', 'OPTIONS' ) ) );
        $result = $controller->doHttpOptions();
        $this->assertInstanceOf( 'ezpRestMvcResult', $result );
        $this->assertSame( 200, $result->status->code );
        $this->assertSame( 'Allowed methods are: GET, OPTIONS', $result->status->message );
        $this->assertSame( array( 'Allow' => 'GET, OPTIONS' ), $result->status->headers );
    }

    public function testCacheLocation()
    {
        $this->assertSame( 'k1/v2/K1.Rest.NodeController/view', self::controller()->getCacheLocation() );
    }

    public function testCacheKeyFollowsTheRequest()
    {
        $key = self::controller( array( 'nodeId' => '2' ), array( 'Translation' => 'eng-GB' ) )->call( 'generateCacheId' );
        $this->assertMatchesRegularExpression( '/^[0-9a-f]{32}$/', $key );
        $this->assertSame( $key, self::controller( array( 'nodeId' => '2' ), array( 'Translation' => 'eng-GB' ) )->call( 'generateCacheId' ) );
        $this->assertNotSame( $key, self::controller( array( 'nodeId' => '3' ), array( 'Translation' => 'eng-GB' ) )->call( 'generateCacheId' ) );
        $this->assertNotSame( $key, self::controller( array( 'nodeId' => '2' ), array( 'Translation' => 'ger-DE' ) )->call( 'generateCacheId' ) );
        $this->assertNotSame( $key, self::controller( array( 'nodeId' => '2' ), array( 'Translation' => 'eng-GB' ), 'list' )->call( 'generateCacheId' ) );
        ( new ReflectionProperty( 'ezpRestPrefixFilterInterface', 'version' ) )->setValue( null, 3 );
        $this->assertNotSame( $key, self::controller( array( 'nodeId' => '2' ), array( 'Translation' => 'eng-GB' ) )->call( 'generateCacheId' ) );
    }

    public function testCacheTtlByLevel()
    {
        $this->setting( 'CacheSettings', 'DefaultCacheTTL', '600' );
        $this->setting( 'K1\\Rest\\NodeController_CacheSettings', 'CacheTTL', null );
        $this->setting( 'K1\\Rest\\NodeController_view_CacheSettings', 'CacheTTL', null );
        $this->assertSame( 600, self::controller()->call( 'getActionTTL' ) );
        $this->setting( 'K1\\Rest\\NodeController_CacheSettings', 'CacheTTL', '60' );
        $this->assertSame( 60, self::controller()->call( 'getActionTTL' ) );
        $this->setting( 'K1\\Rest\\NodeController_view_CacheSettings', 'CacheTTL', '5' );
        $this->assertSame( 5, self::controller()->call( 'getActionTTL' ) );
        $this->assertSame( 60, self::controller( array(), array(), 'list' )->call( 'getActionTTL' ) );
    }

    public function testCacheSwitchByLevel()
    {
        $this->setting( 'CacheSettings', 'ApplicationCache', 'disabled' );
        $this->setting( 'K1\\Rest\\NodeController_view_CacheSettings', 'ApplicationCache', 'enabled' );
        $this->assertFalse( self::controller()->call( 'isCacheEnabled' ), 'the global switch wins' );
        $this->setting( 'CacheSettings', 'ApplicationCache', 'enabled' );
        $this->assertTrue( self::controller()->call( 'isCacheEnabled' ) );
        $this->setting( 'K1\\Rest\\NodeController_CacheSettings', 'ApplicationCache', 'disabled' );
        $this->assertTrue( self::controller()->call( 'isCacheEnabled' ), 'the action level wins over the controller' );
        $this->assertFalse( self::controller( array(), array(), 'list' )->call( 'isCacheEnabled' ) );
        $this->setting( 'K1\\Rest\\NodeController_CacheSettings', 'ApplicationCache', null );
        $this->setting( 'CacheSettings', 'ApplicationCacheDefault', 'enabled' );
        $this->assertTrue( self::controller( array(), array(), 'list' )->call( 'isCacheEnabled' ) );
        $this->setting( 'CacheSettings', 'ApplicationCacheDefault', 'disabled' );
        $this->assertFalse( self::controller( array(), array(), 'list' )->call( 'isCacheEnabled' ) );
    }
}
