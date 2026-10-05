<?php
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * File containing the eZTrustedProxyTest class
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

/**
 * eZTrustedProxy on its own: no INI, no database, no $_SERVER.
 */
class eZTrustedProxyTest extends PHPUnit\Framework\TestCase
{
    public function testDefaultListIsLoopbackOnly()
    {
        $this->assertSame( array( '127.0.0.1', '::1' ), eZTrustedProxy::defaultList() );
    }

    public function testNormaliseListDropsEmptyAndInvalidEntries()
    {
        $this->assertSame(
            array( '10.0.0.0/8', '192.0.2.1', '2001:db8::/32', '::1' ),
            eZTrustedProxy::normaliseList( array( ' 10.0.0.0/8 ', '', '192.0.2.1', 'proxy.lan', '10.0.0.0/33',
                                                  '2001:db8::/32', '2001:db8::/129', '10.0.0.0/', '10.0.0.0/x', 42, null, '::1' ) )
        );
        $this->assertSame( array(), eZTrustedProxy::normaliseList( array() ) );
        $this->assertSame( array(), eZTrustedProxy::normaliseList( null ) );
        $this->assertSame( array( '192.0.2.1' ), eZTrustedProxy::normaliseList( '192.0.2.1' ) );
    }

    public static function providerNormaliseAddress()
    {
        return array(
            array( '192.0.2.1', '192.0.2.1' ),
            array( ' 192.0.2.1 ', '192.0.2.1' ),
            array( '192.0.2.1:4711', '192.0.2.1' ),
            array( '2001:db8::1', '2001:db8::1' ),
            array( '[2001:db8::1]', '2001:db8::1' ),
            array( '[2001:db8::1]:4711', '2001:db8::1' ),
            array( '::ffff:192.0.2.1', '::ffff:192.0.2.1' ),
            array( 'unknown', false ),
            array( '', false ),
            array( '999.0.0.1', false ),
            array( '192.0.2', false ),
            array( '192.0.2.1:', false ),
            array( '[192.0.2.1', false ),
            array( 'example.org', false ),
            array( '1.2.3.4<script>', false ),
            array( null, false ),
            array( array( '192.0.2.1' ), false ),
        );
    }

    #[DataProvider('providerNormaliseAddress')]
    public function testNormaliseAddress( $address, $expected )
    {
        $this->assertSame( $expected, eZTrustedProxy::normaliseAddress( $address ) );
    }

    public static function providerIsTrusted()
    {
        $list = array( '10.0.0.0/8', '192.0.2.10', '2001:db8::/32', '::1', '198.51.100.0/25' );
        return array(
            // IPv4 ranges and single addresses
            array( '10.0.0.1', $list, true ),
            array( '10.255.255.255', $list, true ),
            array( '11.0.0.1', $list, false ),
            array( '9.255.255.255', $list, false ),
            array( '192.0.2.10', $list, true ),
            array( '192.0.2.11', $list, false ),
            // a prefix that is not a multiple of 8
            array( '198.51.100.127', $list, true ),
            array( '198.51.100.128', $list, false ),
            // IPv6 ranges and single addresses, any spelling
            array( '2001:db8::1', $list, true ),
            array( '2001:DB8:0:0:0:0:0:1', $list, true ),
            array( '2001:db9::1', $list, false ),
            array( '::1', $list, true ),
            array( '0:0:0:0:0:0:0:1', $list, true ),
            array( '::2', $list, false ),
            // IPv4-mapped IPv6, as a dual-stack socket reports IPv4 peers
            array( '::ffff:10.1.2.3', $list, true ),
            array( '::ffff:11.1.2.3', $list, false ),
            array( '::ffff:127.0.0.1', array( '127.0.0.1' ), true ),
            array( '127.0.0.1', array( '::ffff:127.0.0.1' ), true ),
            array( '10.9.9.9', array( '::ffff:10.0.0.0/104' ), true ),
            array( '11.9.9.9', array( '::ffff:10.0.0.0/104' ), false ),
            // the families never match each other
            array( '10.0.0.1', array( '::/0' ), false ),
            array( '2001:db8::1', array( '0.0.0.0/0' ), false ),
            array( '203.0.113.9', array( '0.0.0.0/0' ), true ),
            // ports and brackets as proxies write them
            array( '10.0.0.1:4711', $list, true ),
            array( '[2001:db8::1]:443', $list, true ),
            // invalid addresses and lists trust nothing
            array( 'unknown', $list, false ),
            array( '', $list, false ),
            array( null, $list, false ),
            array( '10.0.0.1', array(), false ),
            array( '10.0.0.1', array( 'proxy.lan', '10.0.0.0/40' ), false ),
        );
    }

    #[DataProvider('providerIsTrusted')]
    public function testIsTrusted( $address, array $list, $expected )
    {
        $this->assertSame( $expected, eZTrustedProxy::isTrusted( $address, $list ) );
    }

    public static function providerClientAddress()
    {
        $trusted = array( '10.0.0.0/8', '2001:db8::/32' );
        return array(
            // A visitor connecting directly: the header is whatever it sent, never used.
            array( '203.0.113.9', '1.2.3.4', $trusted, '203.0.113.9' ),
            array( '203.0.113.9', '10.0.0.1', $trusted, '203.0.113.9' ),
            // One trusted proxy: the address it appended.
            array( '10.0.0.1', '1.2.3.4', $trusted, '1.2.3.4' ),
            // Spoofed left-most entries are skipped: the visitor sent "6.6.6.6, 7.7.7.7",
            // the proxy appended the visitor's real address.
            array( '10.0.0.1', '6.6.6.6, 7.7.7.7, 1.2.3.4', $trusted, '1.2.3.4' ),
            // Two trusted proxies: the first untrusted address from the right.
            array( '10.0.0.1', '1.2.3.4, 10.0.0.2', $trusted, '1.2.3.4' ),
            array( '10.0.0.1', '6.6.6.6, 1.2.3.4, 10.0.0.2', $trusted, '1.2.3.4' ),
            // The visitor claiming to be a trusted proxy changes nothing to the right of it.
            array( '10.0.0.1', '10.0.0.9, 1.2.3.4', $trusted, '1.2.3.4' ),
            // IPv6, with brackets and ports as some proxies write them.
            array( '2001:db8::5', '2001:db8:ffff::1, [2a00:1450::1]:4711, 2001:db8::6', $trusted, '2a00:1450::1' ),
            array( '::ffff:10.0.0.1', '192.0.2.44:5555', $trusted, '192.0.2.44' ),
            // Only trusted addresses: the farthest of them.
            array( '10.0.0.1', '10.0.0.3, 10.0.0.2', $trusted, '10.0.0.3' ),
            // Something that is not an address: the last trusted proxy before it.
            array( '10.0.0.1', 'unknown', $trusted, '10.0.0.1' ),
            array( '10.0.0.1', '1.2.3.4, unknown, 10.0.0.2', $trusted, '10.0.0.2' ),
            array( '10.0.0.1', '<script>, 1.2.3.4', $trusted, '1.2.3.4' ),
            // An empty header, or entries that are empty.
            array( '10.0.0.1', '', $trusted, '10.0.0.1' ),
            array( '10.0.0.1', null, $trusted, '10.0.0.1' ),
            array( '10.0.0.1', ' , ', $trusted, '10.0.0.1' ),
            array( '10.0.0.1', '1.2.3.4,', $trusted, '1.2.3.4' ),
            // Nothing trusted: REMOTE_ADDR, whatever the header.
            array( '10.0.0.1', '1.2.3.4', array(), '10.0.0.1' ),
            array( '127.0.0.1', '1.2.3.4', array(), '127.0.0.1' ),
            // No REMOTE_ADDR (the command line).
            array( null, '1.2.3.4', $trusted, null ),
        );
    }

    #[DataProvider('providerClientAddress')]
    public function testClientAddress( $remoteAddr, $header, array $trusted, $expected )
    {
        $this->assertSame( $expected, eZTrustedProxy::clientAddress( $remoteAddr, $header, $trusted ) );
    }

    public function testTrustedHops()
    {
        $trusted = array( '10.0.0.0/8' );
        $this->assertSame( 0, eZTrustedProxy::trustedHops( '203.0.113.9', '10.0.0.1', $trusted ) );
        $this->assertSame( 0, eZTrustedProxy::trustedHops( null, '10.0.0.1', $trusted ) );
        $this->assertSame( 0, eZTrustedProxy::trustedHops( '10.0.0.1', null, array() ) );
        $this->assertSame( 1, eZTrustedProxy::trustedHops( '10.0.0.1', null, $trusted ) );
        $this->assertSame( 1, eZTrustedProxy::trustedHops( '10.0.0.1', '1.2.3.4', $trusted ) );
        $this->assertSame( 2, eZTrustedProxy::trustedHops( '10.0.0.1', '1.2.3.4, 10.0.0.2', $trusted ) );
        // A trusted-looking address left of the client was sent by the client: not counted.
        $this->assertSame( 2, eZTrustedProxy::trustedHops( '10.0.0.1', '10.0.0.7, 1.2.3.4, 10.0.0.2', $trusted ) );
        $this->assertSame( 1, eZTrustedProxy::trustedHops( '10.0.0.1', 'unknown, 10.0.0.2, garbage', $trusted ) );
    }

    public function testForwardedValue()
    {
        // Not trusted: nothing.
        $this->assertNull( eZTrustedProxy::forwardedValue( 'https', 0 ) );
        $this->assertNull( eZTrustedProxy::forwardedValue( '', 1 ) );
        $this->assertNull( eZTrustedProxy::forwardedValue( null, 1 ) );
        // A proxy that sets the header.
        $this->assertSame( 'https', eZTrustedProxy::forwardedValue( ' https ', 1 ) );
        $this->assertSame( 'www.example.org', eZTrustedProxy::forwardedValue( 'www.example.org', 3 ) );
        // A proxy that appends: the client's own value on the left is skipped.
        $this->assertSame( 'www.example.org', eZTrustedProxy::forwardedValue( 'evil.example, www.example.org', 1 ) );
        $this->assertSame( 'http', eZTrustedProxy::forwardedValue( 'https, http', 1 ) );
        // Two trusted proxies: the value the outer one wrote.
        $this->assertSame( 'www.example.org', eZTrustedProxy::forwardedValue( 'www.example.org, proxy.lan', 2 ) );
        $this->assertSame( 'www.example.org', eZTrustedProxy::forwardedValue( 'evil.example, www.example.org, proxy.lan', 2 ) );
    }
}
