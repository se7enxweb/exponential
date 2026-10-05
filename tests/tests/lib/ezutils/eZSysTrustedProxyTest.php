<?php
/**
 * File containing the eZSysTrustedProxyTest class
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

/**
 * eZSys::isSSLNow(), clientIP() and hostname() believe forwarded headers only
 * from the proxies in site.ini [HTTPHeaderSettings] TrustedProxies[].
 * No database; the INI values are set in memory and restored.
 */
class eZSysTrustedProxyTest extends ezpTestCase
{
    const SERVER_KEYS = array( 'REMOTE_ADDR', 'HTTPS', 'HTTP_HOST', 'SERVER_PORT',
                               'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED_PROTO', 'HTTP_X_FORWARDED_PORT',
                               'HTTP_X_FORWARDED_SERVER', 'HTTP_X_FORWARDED_HOST', 'HTTP_X_REAL_IP' );

    private $server = array();
    private $settings = array();

    public function setUp(): void
    {
        parent::setUp();
        foreach ( self::SERVER_KEYS as $key )
        {
            $this->server[$key] = array_key_exists( $key, $_SERVER ) ? array( $_SERVER[$key] ) : null;
            unset( $_SERVER[$key] );
        }
        $ini = eZINI::instance();
        foreach ( array( array( 'HTTPHeaderSettings', 'TrustedProxies' ),
                         array( 'HTTPHeaderSettings', 'ClientIpByCustomHTTPHeader' ),
                         array( 'SiteSettings', 'SSLPort' ),
                         array( 'SiteSettings', 'SSLProxyServerName' ),
                         array( 'SiteSettings', 'SiteURL' ) ) as $setting )
        {
            list( $block, $name ) = $setting;
            $this->settings[] = array( $block, $name, $ini->hasVariable( $block, $name ), $ini->hasVariable( $block, $name ) ? $ini->variable( $block, $name ) : null );
        }
        $ini->setVariable( 'SiteSettings', 'SSLPort', '443' );
        $ini->setVariable( 'SiteSettings', 'SSLProxyServerName', 'lb1' );
        $ini->setVariable( 'SiteSettings', 'SiteURL', 'site.example' );
        $ini->setVariable( 'HTTPHeaderSettings', 'ClientIpByCustomHTTPHeader', 'X-Forwarded-For' );
        $ini->setVariable( 'HTTPHeaderSettings', 'TrustedProxies', array( '10.0.0.0/8', '2001:db8::/32' ) );
        unset( $GLOBALS['eZSysServerPort'] );
    }

    public function tearDown(): void
    {
        foreach ( $this->server as $key => $value )
        {
            if ( $value === null )
                unset( $_SERVER[$key] );
            else
                $_SERVER[$key] = $value[0];
        }
        $ini = eZINI::instance();
        foreach ( $this->settings as $setting )
        {
            list( $block, $name, $had, $value ) = $setting;
            if ( $had )
                $ini->setVariable( $block, $name, $value );
            else
                $ini->removeSetting( $block, $name );
        }
        unset( $GLOBALS['eZSysServerPort'] );
        parent::tearDown();
    }

    // ── isSSLNow ────────────────────────────────────────────────────────

    public function testUntrustedClientSendingForwardedProtoIsNotSSL()
    {
        $_SERVER['REMOTE_ADDR'] = '203.0.113.9';
        $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
        $this->assertFalse( eZSys::isSSLNow() );
        $this->assertSame( 'http', eZSys::serverProtocol() );
    }

    public function testUntrustedClientSendingForwardedPortOrServerIsNotSSL()
    {
        $_SERVER['REMOTE_ADDR'] = '203.0.113.9';
        $_SERVER['HTTP_X_FORWARDED_PORT'] = '443';
        $this->assertFalse( eZSys::isSSLNow() );
        unset( $_SERVER['HTTP_X_FORWARDED_PORT'] );
        $_SERVER['HTTP_X_FORWARDED_SERVER'] = 'lb1';
        $this->assertFalse( eZSys::isSSLNow() );
    }

    public function testNoRemoteAddrBelievesNoForwardedHeader()
    {
        $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
        $this->assertFalse( eZSys::isSSLNow() );
    }

    public function testTrustedProxyForwardedProtoIsSSL()
    {
        $_SERVER['REMOTE_ADDR'] = '10.0.0.5';
        $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
        $this->assertTrue( eZSys::isSSLNow() );
        $this->assertSame( 'https', eZSys::serverProtocol() );
        $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'HTTPS';
        $this->assertTrue( eZSys::isSSLNow() );
        $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'http';
        $this->assertFalse( eZSys::isSSLNow() );
    }

    public function testTrustedProxyForwardedPortAndServerAreSSL()
    {
        $_SERVER['REMOTE_ADDR'] = '2001:db8::7';
        $_SERVER['HTTP_X_FORWARDED_PORT'] = '443';
        $this->assertTrue( eZSys::isSSLNow() );
        $_SERVER['HTTP_X_FORWARDED_PORT'] = '80';
        $this->assertFalse( eZSys::isSSLNow() );
        unset( $_SERVER['HTTP_X_FORWARDED_PORT'] );
        $_SERVER['HTTP_X_FORWARDED_SERVER'] = 'lb1';
        $this->assertTrue( eZSys::isSSLNow() );
        $_SERVER['HTTP_X_FORWARDED_SERVER'] = 'other';
        $this->assertFalse( eZSys::isSSLNow() );
    }

    public function testHttpsServerVariableNeedsNoProxy()
    {
        // What Apache, nginx, FPM and Velocity's TLS listener set: no header involved.
        $_SERVER['REMOTE_ADDR'] = '203.0.113.9';
        $_SERVER['HTTPS'] = 'on';
        $this->assertTrue( eZSys::isSSLNow() );
        $_SERVER['HTTPS'] = 'off';
        $this->assertFalse( eZSys::isSSLNow() );
    }

    public function testEmptySettingTrustsNoProxy()
    {
        eZINI::instance()->setVariable( 'HTTPHeaderSettings', 'TrustedProxies', array() );
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '1.2.3.4';
        $this->assertFalse( eZSys::isSSLNow() );
        $this->assertSame( '127.0.0.1', eZSys::clientIP() );
        $this->assertSame( array(), eZSys::trustedProxies() );
        $this->assertFalse( eZSys::isFromTrustedProxy() );
    }

    public function testMissingSettingTrustsLoopbackOnly()
    {
        eZINI::instance()->removeSetting( 'HTTPHeaderSettings', 'TrustedProxies' );
        $this->assertSame( array( '127.0.0.1', '::1' ), eZSys::trustedProxies() );
        $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
        $_SERVER['REMOTE_ADDR'] = '::1';
        $this->assertTrue( eZSys::isSSLNow() );
        $_SERVER['REMOTE_ADDR'] = '10.0.0.5';
        $this->assertFalse( eZSys::isSSLNow() );
    }

    public function testInvalidSettingEntriesAreIgnored()
    {
        eZINI::instance()->setVariable( 'HTTPHeaderSettings', 'TrustedProxies', array( 'proxy.lan', '10.0.0.0/99', '', '192.0.2.10' ) );
        $this->assertSame( array( '192.0.2.10' ), eZSys::trustedProxies() );
        $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
        $_SERVER['REMOTE_ADDR'] = '10.0.0.5';
        $this->assertFalse( eZSys::isSSLNow() );
        $_SERVER['REMOTE_ADDR'] = '192.0.2.10';
        $this->assertTrue( eZSys::isSSLNow() );
    }

    // ── clientIP ────────────────────────────────────────────────────────

    public function testClientIPIgnoresForwardedForFromUntrustedClient()
    {
        $_SERVER['REMOTE_ADDR'] = '203.0.113.9';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '1.2.3.4';
        $this->assertSame( '203.0.113.9', eZSys::clientIP() );
    }

    public function testClientIPFromTrustedProxyWalksFromTheRight()
    {
        $_SERVER['REMOTE_ADDR'] = '10.0.0.1';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '1.2.3.4, 10.0.0.1';
        $this->assertSame( '1.2.3.4', eZSys::clientIP() );
        // Left-most entries are what the client sent: ignored.
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '127.0.0.1, 6.6.6.6, 1.2.3.4';
        $this->assertSame( '1.2.3.4', eZSys::clientIP() );
        $_SERVER['HTTP_X_FORWARDED_FOR'] = 'not-an-ip';
        $this->assertSame( '10.0.0.1', eZSys::clientIP() );
        $this->assertTrue( eZSys::isFromTrustedProxy() );
    }

    public function testClientIPWithoutHeaderSettingIsRemoteAddr()
    {
        eZINI::instance()->setVariable( 'HTTPHeaderSettings', 'ClientIpByCustomHTTPHeader', 'false' );
        $_SERVER['REMOTE_ADDR'] = '10.0.0.1';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '1.2.3.4';
        $this->assertSame( '10.0.0.1', eZSys::clientIP() );
    }

    public function testClientIPFromASingleAddressHeader()
    {
        eZINI::instance()->setVariable( 'HTTPHeaderSettings', 'ClientIpByCustomHTTPHeader', 'X-Real-IP' );
        $_SERVER['REMOTE_ADDR'] = '2001:db8::1';
        $_SERVER['HTTP_X_REAL_IP'] = '2a00:1450::1';
        $this->assertSame( '2a00:1450::1', eZSys::clientIP() );
        $_SERVER['REMOTE_ADDR'] = '2a00:1450::99';
        $this->assertSame( '2a00:1450::99', eZSys::clientIP() );
    }

    // ── hostname ────────────────────────────────────────────────────────

    public function testForwardedHostOnlyFromTrustedProxy()
    {
        $_SERVER['HTTP_HOST'] = 'site.example';
        $_SERVER['HTTP_X_FORWARDED_HOST'] = 'evil.example';
        $_SERVER['REMOTE_ADDR'] = '203.0.113.9';
        $this->assertSame( 'site.example', eZSys::hostname() );
        $_SERVER['REMOTE_ADDR'] = '10.0.0.5';
        $this->assertSame( 'evil.example', eZSys::hostname() );
        // A proxy that appends: the client's own value on the left is skipped.
        $_SERVER['HTTP_X_FORWARDED_HOST'] = 'evil.example, www.example.org';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '203.0.113.9';
        $this->assertSame( 'www.example.org', eZSys::hostname() );
    }

    public function testForwardedHostPortDoesNotMakeAnUntrustedRequestSSL()
    {
        $_SERVER['HTTP_HOST'] = 'site.example';
        $_SERVER['HTTP_X_FORWARDED_HOST'] = 'site.example:443';
        $_SERVER['REMOTE_ADDR'] = '203.0.113.9';
        $this->assertFalse( eZSys::isSSLNow() );
        $this->assertSame( 'http://site.example', eZSys::serverURL() );
    }
}
