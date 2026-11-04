<?php
/**
 * expViewCountLogParser reads the access log formats of Apache, nginx, Plesk and Velocity by a regular expression:
 * common, combined and vhost_combined, IPv6 clients, quotes inside a quoted field, fields after the user agent, and
 * a format of its own (LineRegexp). Lines that are not requests give null.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

#[\PHPUnit\Framework\Attributes\Group('kernel')]
class expViewCountLogParserTest extends PHPUnit\Framework\TestCase
{
    public function testTheCombinedFormat()
    {
        $r = ( new expViewCountLogParser() )->parse( '192.0.2.10 - - [03/Nov/2026:10:00:00 -0800] "GET /News/A-story?page=2 HTTP/1.1" 200 5120 "https://example.com/" "Mozilla/5.0 (X11) Firefox/140.0"' . "\n" );
        $this->assertSame( '', $r['host'] );
        $this->assertSame( '192.0.2.10', $r['ip'] );
        $this->assertSame( 'GET', $r['method'] );
        $this->assertSame( '/News/A-story?page=2', $r['target'] );
        $this->assertSame( 'HTTP/1.1', $r['protocol'] );
        $this->assertSame( 200, $r['status'] );
        $this->assertSame( 'https://example.com/', $r['referer'] );
        $this->assertSame( 'Mozilla/5.0 (X11) Firefox/140.0', $r['agent'] );
        $this->assertSame( 1793728800, expViewCountLogParser::timestamp( $r['time'] ) );
    }

    public function testTheCommonFormatHasNoAgent()
    {
        $r = ( new expViewCountLogParser() )->parse( '192.0.2.10 - frank [03/Nov/2026:10:00:00 +0000] "GET / HTTP/1.0" 304 -' );
        $this->assertSame( '/', $r['target'] );
        $this->assertSame( 304, $r['status'] );
        $this->assertSame( '', $r['agent'] );
    }

    public function testTheVhostCombinedFormatNamesItsHost()
    {
        $r = ( new expViewCountLogParser() )->parse( 'WWW.Example.com:443 192.0.2.10 - - [03/Nov/2026:10:00:00 +0000] "GET /a HTTP/2.0" 200 10 "-" "Firefox"' );
        $this->assertSame( 'www.example.com', $r['host'] );
        $this->assertSame( 443, $r['port'] );
        $this->assertSame( '192.0.2.10', $r['ip'] );
        $this->assertSame( 'HTTP/2.0', $r['protocol'] );
    }

    public function testAnIPv6ClientAndAQuoteInsideTheUserAgent()
    {
        $r = ( new expViewCountLogParser() )->parse( '2001:db8::1 - - [03/Nov/2026:10:00:00 +0000] "GET /b HTTP/1.1" 200 10 "-" "Agent \"quoted\" \\\\ end"' );
        $this->assertSame( '2001:db8::1', $r['ip'] );
        $this->assertSame( '', $r['host'] );
        $this->assertSame( 'Agent "quoted" \\ end', $r['agent'] );
    }

    public function testFieldsAfterTheUserAgentAreIgnored()
    {
        $parser = new expViewCountLogParser();
        $velocity = $parser->parse( '192.0.2.10 - - [24/Sep/2026:07:34:48 -0700] "GET / HTTP/1.1" 200 95061 "-" "Firefox" 0.9ms' );
        $this->assertSame( 'Firefox', $velocity['agent'] );
        $nginx = $parser->parse( '192.0.2.10 - - [24/Sep/2026:07:34:48 -0700] "GET /x HTTP/1.1" 200 1 "-" "Firefox" "198.51.100.7"' );
        $this->assertSame( '/x', $nginx['target'] );
    }

    public function testALineWithoutAClientAddress()
    {
        // Velocity writes an empty address when it does not know the peer
        $r = ( new expViewCountLogParser() )->parse( ' - - [25/Sep/2026:00:00:43 -0700] "GET / HTTP/1.1" 200 10323 "-" "Firefox"' );
        $this->assertSame( '', $r['ip'] );
        $this->assertSame( '', $r['host'] );
        $this->assertSame( '/', $r['target'] );
        $this->assertSame( 'Firefox', $r['agent'] );
    }

    public function testLinesThatAreNotRequests()
    {
        $parser = new expViewCountLogParser();
        $this->assertNull( $parser->parse( '' ) );
        $this->assertNull( $parser->parse( "garbage without brackets\n" ) );
        $this->assertNull( $parser->parse( '192.0.2.10 - - [03/Nov/2026:10:00:00 +0000] "GET / HTTP/1.1' ) );
        // a request line that is not METHOD target: parsed, but without method (the filter calls it malformed)
        $junk = $parser->parse( '192.0.2.10 - - [03/Nov/2026:10:00:00 +0000] "\x16\x03\x01" 400 0 "-" "-"' );
        $this->assertSame( '', $junk['method'] );
        $this->assertSame( 400, $junk['status'] );
        $dash = $parser->parse( '192.0.2.10 - - [03/Nov/2026:10:00:00 +0000] "-" 408 - "-" "-"' );
        $this->assertSame( '', $dash['target'] );
        $this->assertSame( 0, $parser->parse( '192.0.2.10 - - [x] "GET / HTTP/1.1" - - "-" "-"' )['status'] );
    }

    public function testThePathOfATarget()
    {
        $this->assertSame( '/a/b', expViewCountLogParser::path( '/a/b?x=1#top' ) );
        $this->assertSame( '/a', expViewCountLogParser::path( 'https://example.com/a?b' ) );
        $this->assertSame( '/', expViewCountLogParser::path( 'http://example.com' ) );
        $this->assertSame( '', expViewCountLogParser::path( '*' ) );
        $this->assertSame( '', expViewCountLogParser::path( 'example.com:443' ) );
    }

    public function testHostsWithPorts()
    {
        $this->assertSame( array( '2001:db8::1', 8443 ), expViewCountLogParser::splitHost( '[2001:DB8::1]:8443' ) );
        $this->assertSame( array( 'example.com', null ), expViewCountLogParser::splitHost( 'Example.COM' ) );
        $this->assertNull( expViewCountLogParser::timestamp( 'yesterday' ) );
    }

    public function testAFormatOfItsOwn()
    {
        $parser = new expViewCountLogParser( '~^(?P<time>\S+) (?P<host>\S+) "(?P<request>[^"]*)" (?P<status>\d+) "(?P<agent>[^"]*)"$~' );
        $r = $parser->parse( '2026-11-03T10:00:00Z shop.example.com "GET /c HTTP/1.1" 200 "Firefox"' );
        $this->assertSame( 'shop.example.com', $r['host'] );
        $this->assertSame( '/c', $r['target'] );
        $this->assertSame( 'Firefox', $r['agent'] );
    }

    public function testAFormatWithoutTheRequiredGroupsIsRefused()
    {
        $this->expectException( InvalidArgumentException::class );
        new expViewCountLogParser( '~^(?P<time>\S+)$~' );
    }

    public function testABrokenFormatIsRefused()
    {
        $this->expectException( InvalidArgumentException::class );
        new expViewCountLogParser( '~(?P<time>~' );
    }
}
