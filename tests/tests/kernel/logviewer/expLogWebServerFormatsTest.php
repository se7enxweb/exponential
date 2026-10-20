<?php
/**
 * The web server's log formats in the log viewer's parser (doc/eng-us/features/6.0/log-viewer.md, "Web server logs"). Synthetic
 * lines only (documentation addresses).
 *
 *  LV-90  access log, common and combined: client, time with its zone, request path as the request, status as the level
 *         (5xx error, 4xx warning, else notice), the message; every line is an entry
 *  LV-91  a line that is not an access line continues the entry before it; a request line that is not
 *         "METHOD path PROTOCOL" (a probe) is still an entry
 *  LV-92  Apache 2.4 error log: time with microseconds, module:level, pid and tid, client without its port, the level
 *         (error, crit, warn, notice, info, debug, trace), a line without a client
 *  LV-93  the eZDebug and eZLog formats still win on their own lines, and startsEntry() knows the new headers
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expLogWebServerFormatsTest extends PHPUnit\Framework\TestCase
{
    /** LV-90 */
    public function testAccessLog()
    {
        $chunk = '192.0.2.9 - - [17/Oct/2026:01:00:00 +0200] "GET /admin/setup/logs?x=1 HTTP/1.1" 200 5120' . "\n"
               . '198.51.100.7 - jane [17/Oct/2026:01:00:01 +0200] "POST /user/login HTTP/2.0" 302 0 "https://example.test/" "Mozilla/5.0 (X11)"' . "\n"
               . '2001:db8::1 - - [17/Oct/2026:01:00:02 +0000] "GET /missing HTTP/1.1" 404 196 "-" "curl/8.0"' . "\n"
               . '203.0.113.5 - - [17/Oct/2026:01:00:03 -0500] "GET /boom HTTP/1.1" 503 - "-" "-"' . "\n";
        $e = expLogParser::parse( $chunk, 1000, '' );
        $this->assertCount( 4, $e );
        $this->assertSame( array( 'notice', 'notice', 'warning', 'error' ), array_column( $e, 'level' ) );
        $this->assertSame( array( '192.0.2.9', '198.51.100.7', '2001:db8::1', '203.0.113.5' ), array_column( $e, 'ip' ) );
        $this->assertSame( array( '/admin/setup/logs?x=1', '/user/login', '/missing', '/boom' ), array_column( $e, 'url' ) );
        $this->assertSame( 'access', $e[0]['format'] );
        $this->assertSame( 1000, $e[0]['offset'] );
        $this->assertFalse( $e[0]['cont'] );
        $this->assertSame( gmmktime( 23, 0, 0, 10, 16, 2026 ), $e[0]['time'], 'the zone of the line counts' );
        $this->assertSame( gmmktime( 6, 0, 3, 10, 17, 2026 ), $e[3]['time'] );
        $this->assertSame( '17/Oct/2026:01:00:00 +0200', $e[0]['time_text'] );
        $this->assertStringContainsString( 'GET /admin/setup/logs?x=1 200 5120', $e[0]['message'] );
        $this->assertStringContainsString( 'Mozilla/5.0 (X11)', $e[1]['message'] );
        $this->assertSame( '', $e[0]['siteaccess'] );
    }

    /** LV-91 */
    public function testContinuationsAndProbes()
    {
        $chunk = 'stray text from somewhere' . "\n"
               . '192.0.2.9 - - [17/Oct/2026:01:00:00 +0200] "GET / HTTP/1.1" 200 1' . "\n"
               . '  a wrapped continuation' . "\n"
               . '192.0.2.9 - - [17/Oct/2026:01:00:01 +0200] "\x16\x03\x01" 400 157 "-" "-"' . "\n"
               . '192.0.2.9 - - [17/Oct/2026:01:00:02 +0200] "-" 408 0 "-" "-"' . "\n";
        $e = expLogParser::parse( $chunk, 0, '' );
        $this->assertCount( 4, $e );
        $this->assertTrue( $e[0]['cont'], 'a chunk that starts inside an entry' );
        $this->assertStringContainsString( 'a wrapped continuation', $e[1]['text'] );
        $this->assertSame( 'warning', $e[2]['level'], 'a probe that is no request is an entry all the same' );
        $this->assertSame( '', $e[2]['url'] );
        $this->assertSame( 'warning', $e[3]['level'] );
        $this->assertFalse( expLogParser::startsEntry( 'GET / HTTP/1.1" 200 1' ) );
        $this->assertFalse( expLogParser::startsEntry( 'host - - [not a time] "GET / HTTP/1.1" 200 1' ) );
    }

    /** LV-92 */
    public function testApacheErrorLog()
    {
        $chunk = '[Fri Oct 17 01:00:00.123456 2026] [proxy_fcgi:error] [pid 12:tid 34] [client 192.0.2.9:5555] AH01071: Got error \'Primary script unknown\'' . "\n"
               . '[Fri Oct 17 01:00:01.5 2026] [core:crit] [pid 12] [client [2001:db8::1]:443] AH00037: Symbolic link not allowed' . "\n"
               . '[Fri Oct 17 01:00:02 2026] [mpm_event:notice] [pid 1:tid 1] AH00489: Apache configured -- resuming normal operations' . "\n"
               . '[Fri Oct 17 01:00:03.1 2026] [ssl:warn] [pid 1] AH01909: certificate does not include an ID' . "\n"
               . '[Fri Oct 17 01:00:04.1 2026] [authz_core:debug] [pid 1] mod_authz_core.c(815): AH01626' . "\n"
               . '[Fri Oct 17 01:00:05.1 2026] [rewrite:trace3] [pid 1] applying pattern' . "\n"
               . '[Fri Oct  3 01:00:06.1 2026] [core:info] [pid 1] a single digit day' . "\n";
        $e = expLogParser::parse( $chunk, 0, '' );
        $this->assertCount( 7, $e );
        $this->assertSame( array( 'error', 'error', 'notice', 'warning', 'debug', 'debug', 'notice' ), array_column( $e, 'level' ) );
        $this->assertSame( '192.0.2.9', $e[0]['ip'], 'the client without its port' );
        $this->assertSame( '', $e[2]['ip'], 'a line without a client' );
        $this->assertSame( 'apache_error', $e[0]['format'] );
        $this->assertNotNull( $e[0]['time'] );
        $this->assertSame( $e[0]['time'] + 1, $e[1]['time'], 'microseconds are cut, not misread' );
        $this->assertNotNull( $e[6]['time'] );
        $this->assertStringContainsString( 'AH01071', $e[0]['message'] );
        $this->assertStringStartsWith( 'error: ', $e[0]['message'] );
        $this->assertTrue( expLogParser::startsEntry( '[Fri Oct 17 01:00:00.123456 2026] [core:error] x' ) );
        $this->assertTrue( expLogParser::startsEntry( '[Fri Oct  3 01:00:00 2026] [core:error] x' ) );
    }

    /** LV-93 */
    public function testKernelFormatsStillWin()
    {
        $e = expLogParser::parse( "[ Oct 17 2026 01:00:00 ] [192.0.2.1] [admin] [https://example.test/a] eZDB:\nWarning: slow\n"
                                  . "[ Oct 17 2026 01:00:01 ][ admin ][ https://example.test/b ] stored\n", 0, 'error' );
        $this->assertCount( 2, $e );
        $this->assertSame( array( 'debug', 'log' ), array_column( $e, 'format' ) );
        $this->assertSame( 'warning', $e[0]['level'] );
        $this->assertSame( 'error', $e[1]['level'], 'the file level for a line that names none' );
    }
}
