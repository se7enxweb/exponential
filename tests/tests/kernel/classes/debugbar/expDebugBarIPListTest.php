<?php
/**
 * The IP list engine of the Exp Debug bar: IPv4 and IPv6 addresses and CIDR networks, entries with a label and an
 * expiry, the match eZDebug's IP check uses, and the warnings of a list. Needs no kernel and no database.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/debugbar/expDebugBarIPListTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expDebugBarIPListTest extends PHPUnit\Framework\TestCase
{
    /** 2026-10-02 12:00 in the server's time zone */
    protected $now;

    public static function setUpBeforeClass(): void
    {
        if ( !class_exists( 'expDebugBarIPList' ) )
            require_once dirname( __DIR__, 5 ) . '/kernel/classes/debugbar/expdebugbariplist.php';
    }

    public function setUp(): void
    {
        $this->now = mktime( 12, 0, 0, 10, 2, 2026 );
    }

    public function testParseAddressIPv4AndIPv6()
    {
        $a = expDebugBarIPList::parseAddress( '203.0.113.7' );
        $this->assertSame( 4, $a['family'] );
        $this->assertSame( 32, $a['prefix'] );
        $this->assertSame( '203.0.113.7/32', $a['cidr'] );

        $n = expDebugBarIPList::parseAddress( '203.0.113.77/24' );
        $this->assertSame( '203.0.113.0', $n['network'] );
        $this->assertTrue( $n['host_bits'] );

        $six = expDebugBarIPList::parseAddress( '2001:DB8:0:0:1::1/64' );
        $this->assertSame( 6, $six['family'] );
        $this->assertSame( '2001:db8::/64', $six['cidr'] );
        $this->assertSame( 128, expDebugBarIPList::parseAddress( '::1' )['prefix'] );
    }

    public function testParseAddressRefusesWhatIsNoAddress()
    {
        foreach ( array( '', 'bogus', '300.1.1.1', '1.2.3', '10.0.0.0/33', '2001:db8::/129', '10.0.0.0/x', 'fe80::1%eth0', '[::1]', '1.2.3.4/-1' ) as $bad )
            $this->assertIsString( expDebugBarIPList::parseAddress( $bad ), "'$bad' is refused" );
    }

    public function testContainsBothFamilies()
    {
        $this->assertTrue( expDebugBarIPList::contains( '203.0.113.0/24', '203.0.113.200' ) );
        $this->assertFalse( expDebugBarIPList::contains( '203.0.113.0/24', '203.0.114.1' ) );
        $this->assertTrue( expDebugBarIPList::contains( '10.0.0.0/8', '10.255.1.2' ) );
        $this->assertTrue( expDebugBarIPList::contains( '0.0.0.0/0', '192.0.2.1' ) );
        $this->assertTrue( expDebugBarIPList::contains( '203.0.113.7', '203.0.113.7' ) );
        $this->assertFalse( expDebugBarIPList::contains( '203.0.113.7', '203.0.113.8' ) );
        // prefixes that do not end on a byte
        $this->assertTrue( expDebugBarIPList::contains( '198.51.100.64/26', '198.51.100.127' ) );
        $this->assertFalse( expDebugBarIPList::contains( '198.51.100.64/26', '198.51.100.128' ) );

        $this->assertTrue( expDebugBarIPList::contains( '2001:db8::/32', '2001:db8:ffff::1' ) );
        $this->assertFalse( expDebugBarIPList::contains( '2001:db8::/32', '2001:db9::1' ) );
        $this->assertTrue( expDebugBarIPList::contains( '2001:db8:abcd:12::/63', '2001:db8:abcd:13::5' ) );
        $this->assertFalse( expDebugBarIPList::contains( '2001:db8::1', '2001:db8::2' ), 'a plain IPv6 entry is one address' );
        // families never mix
        $this->assertFalse( expDebugBarIPList::contains( '::/0', '192.0.2.1' ) );
        $this->assertFalse( expDebugBarIPList::contains( '0.0.0.0/0', '2001:db8::1' ) );
    }

    public function testIPv4MappedClientMatchesIPv4Entries()
    {
        $this->assertTrue( expDebugBarIPList::contains( '203.0.113.0/24', '::ffff:203.0.113.9' ) );
        $this->assertSame( "\xcb\x00\x71\x09", expDebugBarIPList::packedClient( '::ffff:203.0.113.9' ) );
        $this->assertFalse( expDebugBarIPList::packedClient( 'nonsense' ) );
    }

    public function testParseEntryWithLabelAndExpiry()
    {
        $e = expDebugBarIPList::parseEntry( '203.0.113.7/32 ; Laptop ; expires=2026-10-02T15:00', $this->now );
        $this->assertTrue( $e['valid'] );
        $this->assertSame( '203.0.113.7/32', $e['address'] );
        $this->assertSame( 'Laptop', $e['label'] );
        $this->assertSame( mktime( 15, 0, 0, 10, 2, 2026 ), $e['expires_ts'] );
        $this->assertFalse( $e['expired'] );
        $this->assertFalse( $e['plain'] );

        $late = expDebugBarIPList::parseEntry( '203.0.113.7 ; expires=2026-10-02T11:59', $this->now );
        $this->assertTrue( $late['expired'] );

        $label = expDebugBarIPList::parseEntry( '2001:db8::/32;label=VPN office', $this->now );
        $this->assertSame( 'VPN office', $label['label'] );

        $zone = expDebugBarIPList::parseEntry( '203.0.113.7 ; expires=2026-10-02T15:00Z', $this->now );
        $this->assertSame( gmmktime( 15, 0, 0, 10, 2, 2026 ), $zone['expires_ts'] );

        $plain = expDebugBarIPList::parseEntry( '10.0.0.0/8', $this->now );
        $this->assertTrue( $plain['plain'] );
        $this->assertSame( 8, $plain['prefix'] );

        $bad = expDebugBarIPList::parseEntry( '10.0.0.1 ; expires=soon', $this->now );
        $this->assertFalse( $bad['valid'] );
        $this->assertTrue( $bad['expired'], 'an unreadable expiry never lets anyone in' );

        $cl = expDebugBarIPList::parseEntry( 'commandline' );
        $this->assertSame( 'commandline', $cl['special'] );
        $this->assertTrue( $cl['valid'] );
    }

    public function testFormatEntry()
    {
        $this->assertSame( '203.0.113.7/32', expDebugBarIPList::formatEntry( '203.0.113.7/32' ) );
        $this->assertSame( '203.0.113.0/24 ; Office', expDebugBarIPList::formatEntry( '203.0.113.0/24', '  Office ' ) );
        $this->assertSame( '2001:db8::/64 ; Home ; expires=2026-10-02T13:00',
                           expDebugBarIPList::formatEntry( '2001:db8::/64', 'Home', '+1h', $this->now ) );
        $this->assertSame( '203.0.113.7 ; expires=2026-10-02T23:59', expDebugBarIPList::formatEntry( '203.0.113.7', '', 'today', $this->now ) );
        $this->assertSame( '203.0.113.7 ; expires=2026-10-03T23:59', expDebugBarIPList::formatEntry( '203.0.113.7', '', 'tomorrow', $this->now ) );
        $this->assertSame( '203.0.113.7 ; expires=2026-10-02T12:30', expDebugBarIPList::formatEntry( '203.0.113.7', '', '+30m', $this->now ) );
        $this->assertSame( '203.0.113.7 ; expires=2026-10-04T12:00', expDebugBarIPList::formatEntry( '203.0.113.7', '', '+2d', $this->now ) );
        $this->assertSame( '203.0.113.7 ; expires=2026-12-24T18:00', expDebugBarIPList::formatEntry( '203.0.113.7', '', '2026-12-24T18:00', $this->now ) );

        // what formatEntry writes, parseEntry reads back
        $line = expDebugBarIPList::formatEntry( '2001:db8::/64', 'Home', '+1h', $this->now );
        $e = expDebugBarIPList::parseEntry( $line, $this->now );
        $this->assertSame( array( '2001:db8::/64', 'Home', $this->now + 3600 ), array( $e['address'], $e['label'], $e['expires_ts'] ) );

        foreach ( array( array( 'bogus', '', null ), array( '10.0.0.1', 'a;b', null ), array( '10.0.0.1', 'x ## y', null ),
                         array( '10.0.0.1', 'expires=1', null ), array( '10.0.0.1', '', 'next week' ), array( '10.0.0.1', 'a */ b', null ) ) as $case )
        {
            try
            {
                expDebugBarIPList::formatEntry( $case[0], $case[1], $case[2], $this->now );
                $this->fail( 'refused: ' . json_encode( $case ) );
            }
            catch ( InvalidArgumentException $e )
            {
                $this->assertNotSame( '', $e->getMessage() );
            }
        }
    }

    public function testMatchReturnsTheFirstActiveEntry()
    {
        $list = array( '203.0.113.7 ; Old ; expires=2026-10-01T00:00', 'nonsense', '203.0.113.0/24 ; Office', '2001:db8::/32 ; VPN', 'commandline' );
        $m = expDebugBarIPList::match( '203.0.113.7', $list, $this->now );
        $this->assertSame( 2, $m['index'], 'the expired entry is skipped, the network matches' );
        $this->assertSame( 'Office', $m['label'] );
        $this->assertSame( 3, expDebugBarIPList::match( '2001:db8:1::2', $list, $this->now )['index'] );
        $this->assertNull( expDebugBarIPList::match( '198.51.100.1', $list, $this->now ) );
        $this->assertSame( 4, expDebugBarIPList::match( null, $list, $this->now, true )['index'], 'commandline for a shell without address' );
        $this->assertNull( expDebugBarIPList::match( null, $list, $this->now, false ) );
        $this->assertNull( expDebugBarIPList::match( '203.0.113.7', array(), $this->now ) );
    }

    public function testAnalyseWarnings()
    {
        $codes = function ( array $a ) { return array_column( $a['warnings'], 'code' ); };

        $a = expDebugBarIPList::analyse( array( '203.0.113.0/24', '0.0.0.0/0 ; everyone', 'bad', '10.0.0.1 ; expires=2020-01-01T00:00' ), '203.0.113.5', $this->now );
        $this->assertTrue( $a['allowed'] );
        $this->assertSame( array( 'open', 'invalid', 'expired' ), $codes( $a ) );
        $this->assertTrue( $a['entries'][0]['matches'] );
        $this->assertFalse( $a['entries'][1]['matches'] );

        $lock = expDebugBarIPList::analyse( array( '198.51.100.0/24' ), '203.0.113.5', $this->now );
        $this->assertFalse( $lock['allowed'] );
        $this->assertSame( array( 'lockout' ), $codes( $lock ) );

        $empty = expDebugBarIPList::analyse( array( 'commandline' ), '203.0.113.5', $this->now );
        $this->assertSame( array( 'empty' ), $codes( $empty ) );

        $this->assertTrue( expDebugBarIPList::isOpen( array( '::/0' ), $this->now ) );
        $this->assertFalse( expDebugBarIPList::isOpen( array( '::/0 ; expires=2020-01-01T00:00' ), $this->now ) );
    }

    public function testSuggest()
    {
        $this->assertSame( array( 'self' => '203.0.113.7/32', 'network' => '203.0.113.0/24', 'family' => 4 ), expDebugBarIPList::suggest( '203.0.113.7' ) );
        $this->assertSame( array( 'self' => '2001:db8:1:2:3:4:5:6/128', 'network' => '2001:db8:1:2::/64', 'family' => 6 ),
                           expDebugBarIPList::suggest( '2001:db8:1:2:3:4:5:6' ) );
        $this->assertSame( '203.0.113.0/24', expDebugBarIPList::suggest( '::ffff:203.0.113.7' )['network'] );
        $this->assertNull( expDebugBarIPList::suggest( '' )['self'] );
    }

    public function testAddressOf()
    {
        $this->assertSame( '10.0.0.0/8', expDebugBarIPList::addressOf( '10.0.0.0/8 ; LAN', $this->now ) );
        $this->assertSame( '', expDebugBarIPList::addressOf( '10.0.0.0/8 ; expires=2020-01-01T00:00', $this->now ) );
    }
}
