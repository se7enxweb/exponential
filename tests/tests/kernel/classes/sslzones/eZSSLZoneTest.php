<?php
/**
 * Tests of the SSL zone decisions of eZSSLZone that need no database and no redirect: whether the zones are
 * enabled, how a module/view is found in ModuleViewAccessMode (the exact view before module/*), which views keep
 * the current access mode, and whether a node path lies in one of the zone subtrees (from the in-memory cache of
 * zone path strings, which a test fills). A decision that matches the current access mode does not redirect.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class eZSSLZoneTest extends PHPUnit\Framework\TestCase
{
    private $globals = array();
    private $https;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        foreach ( array( 'eZSSLZoneEnabled', 'eZSSLZonesCachedPathStrings' ) as $name )
            $this->globals[$name] = array_key_exists( $name, $GLOBALS ) ? array( $GLOBALS[$name] ) : null;
        $this->https = $_SERVER['HTTPS'] ?? null;
        unset( $GLOBALS['eZSSLZoneEnabled'] );
        ezpINIHelper::setINISetting( 'site.ini', 'SSLZoneSettings', 'SSLZones', 'enabled' );
        ezpINIHelper::setINISetting( 'site.ini', 'SSLZoneSettings', 'ModuleViewAccessMode', array(
            'shop/*' => 'ssl', 'shop/basket' => 'keep', 'user/login' => 'ssl', 'content/*' => 'keep', 'content/edit' => 'ssl' ) );
        $GLOBALS['eZSSLZonesCachedPathStrings'] = array( 'shop' => '/1/2/60/', 'account' => '/1/2/70/' );
    }

    protected function tearDown(): void
    {
        ezpINIHelper::restoreINISettings();
        foreach ( $this->globals as $name => $value )
        {
            if ( $value === null )
                unset( $GLOBALS[$name] );
            else
                $GLOBALS[$name] = $value[0];
        }
        if ( $this->https === null )
            unset( $_SERVER['HTTPS'] );
        else
            $_SERVER['HTTPS'] = $this->https;
    }

    public function testEnabledFollowsTheSettingAndIsRemembered()
    {
        $this->assertTrue( eZSSLZone::enabled() );
        ezpINIHelper::setINISetting( 'site.ini', 'SSLZoneSettings', 'SSLZones', 'disabled' );
        $this->assertTrue( eZSSLZone::enabled(), 'remembered for the request' );
        unset( $GLOBALS['eZSSLZoneEnabled'] );
        $this->assertFalse( eZSSLZone::enabled() );
    }

    public function testViewIsInArray()
    {
        $views = array( 'shop/*', 'user/login' );
        $this->assertSame( 2, eZSSLZone::viewIsInArray( 'user', 'login', $views ) );
        $this->assertSame( 1, eZSSLZone::viewIsInArray( 'shop', 'confirmorder', $views ) );
        $this->assertSame( 0, eZSSLZone::viewIsInArray( 'user', 'logout', $views ) );
        $this->assertSame( 2, eZSSLZone::viewIsInArray( 'shop', 'basket', array( 'shop/*', 'shop/basket' ) ) );
    }

    public function testKeepModeViews()
    {
        $this->assertTrue( eZSSLZone::isKeepModeView( 'content', 'view' ), 'content/* is keep' );
        $this->assertFalse( eZSSLZone::isKeepModeView( 'content', 'edit' ), 'the exact ssl entry wins over content/*' );
        $this->assertTrue( eZSSLZone::isKeepModeView( 'shop', 'basket' ), 'the exact keep entry wins over shop/*' );
        $this->assertFalse( eZSSLZone::isKeepModeView( 'shop', 'checkout' ) );
        $this->assertTrue( eZSSLZone::isKeepModeView( 'search', 'search' ), 'a view named nowhere keeps its mode' );
    }

    public function testNodePathInAZone()
    {
        $this->assertTrue( eZSSLZone::checkNodePath( 'content', 'view', '/1/2/60/', false ) );
        $this->assertTrue( eZSSLZone::checkNodePath( 'content', 'view', '/1/2/60/61/62/', false ) );
        $this->assertFalse( eZSSLZone::checkNodePath( 'content', 'view', '/1/2/600/', false ) );
        $this->assertFalse( eZSSLZone::checkNodePath( 'content', 'view', '/1/2/', false ) );
        $this->assertTrue( eZSSLZone::checkNodePath( 'content', 'view', '/1/2/70/71/', false ) );
    }

    public function testNodePathOfAViewThatDecidesItsModeItselfIsNotChecked()
    {
        $this->assertNull( eZSSLZone::checkNodePath( 'shop', 'checkout', '/1/2/60/', false ) );
    }

    public function testNothingIsCheckedWhenDisabled()
    {
        $GLOBALS['eZSSLZoneEnabled'] = false;
        $this->assertNull( eZSSLZone::checkNodePath( 'content', 'view', '/1/2/60/', false ) );
        $this->assertNull( eZSSLZone::checkModuleView( 'user', 'login' ) );
    }

    public function testAMatchingAccessModeDoesNotRedirect()
    {
        $_SERVER['HTTPS'] = 'on';
        $this->assertTrue( eZSSLZone::checkNodePath( 'content', 'view', '/1/2/60/', true ), 'in the zone and already on HTTPS' );
        $this->assertFalse( $GLOBALS['eZSSLZoneEnabled'], 'after the decision no inner module redirects again' );

        unset( $GLOBALS['eZSSLZoneEnabled'] );
        unset( $_SERVER['HTTPS'] );
        eZSSLZone::checkModuleView( 'content', 'view' );
        $this->assertTrue( $GLOBALS['eZSSLZoneEnabled'], 'a keep view does not decide' );
        eZSSLZone::checkModuleView( 'search', 'search' );
        $this->assertFalse( $GLOBALS['eZSSLZoneEnabled'], 'a plain view over plain HTTP: decided, no redirect' );
    }

    public function testSwitchWithoutADecisionDoesNothing()
    {
        $GLOBALS['eZSSLZoneEnabled'] = true;
        eZSSLZone::switchIfNeeded( null );
        $this->assertTrue( $GLOBALS['eZSSLZoneEnabled'] );
    }

    public function testCachedZonesAreUsed()
    {
        $this->assertSame( array( 'shop' => '/1/2/60/', 'account' => '/1/2/70/' ), eZSSLZone::getSSLZones() );
        $this->assertStringEndsWith( '/ssl_zones_cache.php', eZSSLZone::cacheFileName() );
    }
}
