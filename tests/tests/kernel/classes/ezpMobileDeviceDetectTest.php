<?php
/**
 * Tests of the mobile device detection (ezpMobileDeviceDetect with ezpMobileDeviceRegexpFilter::process()): WAP
 * headers, the four-letter user agent codes and the user agent expressions, with the alias of the matching one.
 *
 * The request headers are set in $_SERVER and the settings in site.ini by the test, and both are put back. No
 * database; redirect() is not called (it sends headers and ends the request).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class ezpMobileDeviceDetectTest extends PHPUnit\Framework\TestCase
{
    const HEADERS = array( 'HTTP_USER_AGENT', 'HTTP_ACCEPT', 'HTTP_X_WAP_PROFILE', 'HTTP_PROFILE' );

    private $server = array();

    private $settings = array();

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        foreach ( self::HEADERS as $name )
        {
            $this->server[$name] = array_key_exists( $name, $_SERVER ) ? array( $_SERVER[$name] ) : null;
            unset( $_SERVER[$name] );
        }
        $ini = eZINI::instance();
        foreach ( array( 'MobileUserAgentCodes', 'MobileUserAgentRegexps' ) as $name )
            $this->settings[$name] = $ini->hasVariable( 'SiteAccessSettings', $name ) ? array( $ini->variable( 'SiteAccessSettings', $name ) ) : null;
        $ini->setVariable( 'SiteAccessSettings', 'MobileUserAgentCodes', 'noki|sams|sony' );
        $ini->setVariable( 'SiteAccessSettings', 'MobileUserAgentRegexps', array(
            'AndroidDevice' => '/android.*mobile/i',
            'AndroidTabletDevice' => '/android(?!.*mobile)/i',
            'IPhoneDevice' => '/(iphone|ipod)/i',
            'IPadDevice' => '/ipad/i' ) );
    }

    protected function tearDown(): void
    {
        foreach ( $this->server as $name => $value )
        {
            if ( $value === null )
                unset( $_SERVER[$name] );
            else
                $_SERVER[$name] = $value[0];
        }
        $ini = eZINI::instance();
        foreach ( $this->settings as $name => $value )
        {
            if ( $value === null )
                $ini->removeSetting( 'SiteAccessSettings', $name );
            else
                $ini->setVariable( 'SiteAccessSettings', $name, $value[0] );
        }
    }

    private static function detect( $headers )
    {
        foreach ( $headers as $name => $value )
            $_SERVER[$name] = $value;
        $detect = new ezpMobileDeviceDetect( new ezpMobileDeviceRegexpFilter() );
        $detect->process();
        return $detect;
    }

    public static function userAgentProvider()
    {
        return array(
            'desktop firefox' => array( 'Mozilla/5.0 (X11; Linux x86_64; rv:130.0) Gecko/20100101 Firefox/130.0', false, '' ),
            'android phone'   => array( 'Mozilla/5.0 (Linux; Android 14; Pixel 8) AppleWebKit/537.36 Mobile Safari/537.36', true, 'AndroidDevice' ),
            'android tablet'  => array( 'Mozilla/5.0 (Linux; Android 14; SM-X710) AppleWebKit/537.36 Safari/537.36', true, 'AndroidTabletDevice' ),
            'iphone'          => array( 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X)', true, 'IPhoneDevice' ),
            'ipad'            => array( 'Mozilla/5.0 (iPad; CPU OS 17_0 like Mac OS X)', true, 'IPadDevice' ),
            'user agent code' => array( 'Nokia6230/2.0', true, '' ),
            'empty'           => array( '', false, '' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('userAgentProvider')]
    public function testUserAgents( $userAgent, $mobile, $alias )
    {
        $detect = self::detect( array( 'HTTP_USER_AGENT' => $userAgent ) );
        $this->assertSame( $mobile, $detect->isMobileDevice() );
        $this->assertSame( $alias, $detect->getUserAgentAlias() );
    }

    public static function wapProvider()
    {
        return array(
            'wap profile header'      => array( array( 'HTTP_X_WAP_PROFILE' => 'http://t1.example.invalid/uaprof.xml' ) ),
            'profile header'          => array( array( 'HTTP_PROFILE' => 'http://t1.example.invalid/uaprof.xml' ) ),
            'wml accepted later'      => array( array( 'HTTP_ACCEPT' => 'text/html, text/vnd.wap.wml' ) ),
            'wml accepted first'      => array( array( 'HTTP_ACCEPT' => 'text/vnd.wap.wml, text/html' ) ),
            'wap xhtml accepted first' => array( array( 'HTTP_ACCEPT' => 'application/vnd.wap.xhtml+xml' ) ),
        );
    }

    /**
     * A WAP type at the very start of the Accept header was not seen: strpos() gives 0 there, and the check was
     * "> 0".
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('wapProvider')]
    public function testWapRequestsAreMobile( $headers )
    {
        $this->assertTrue( self::detect( $headers + array( 'HTTP_USER_AGENT' => 'Desktop/1.0' ) )->isMobileDevice() );
    }

    public function testOrdinaryAcceptHeaderIsNotMobile()
    {
        $detect = self::detect( array( 'HTTP_USER_AGENT' => 'Desktop/1.0', 'HTTP_ACCEPT' => 'text/html,application/xhtml+xml' ) );
        $this->assertFalse( $detect->isMobileDevice() );
    }

    public function testTheFilterIsKept()
    {
        $filter = new ezpMobileDeviceRegexpFilter();
        $detect = new ezpMobileDeviceDetect( $filter );
        $this->assertSame( $filter, $detect->getFilter() );
    }
}
