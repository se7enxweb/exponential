<?php
/**
 * The log viewer's line parser (doc/eng-us/features/6.0/log-viewer.md, "The line format"); pure, no file, no database.
 *
 *  LV-01  eZDebug lines: date, ip, siteaccess and request; without siteaccess and request; a request with ] and a query
 *  LV-02  eZLog lines (with and without siteaccess and request), ISO and bracketed SQL times, JSON lines
 *  LV-03  continuation lines join the entry before; a chunk that starts inside an entry gives cont = true; offsets
 *  LV-04  the level: named by the message or the label line, else the file's, else notice
 *  LV-05  the source: the label, a class or file name, cli for a command line
 *  LV-06  error references, the message without the header fields, the cut at MAX_ENTRY_BYTES
 *  LV-07  parts(): every link type, masked values never linked, overlapping matches, the most links
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expLogParserTest extends PHPUnit\Framework\TestCase
{
    /** LV-01 */
    public function testDebugFormat()
    {
        $h = expLogParser::header( '[ Oct 17 2026 01:54:58 ] [192.0.2.4] [admin] [https://example.test/admin/content/view/full/2] eZTemplate:' );
        $this->assertSame( 'debug', $h['format'] );
        $this->assertSame( 'Oct 17 2026 01:54:58', $h['time_text'] );
        $this->assertSame( DateTime::createFromFormat( '!M d Y H:i:s', 'Oct 17 2026 01:54:58' )->getTimestamp(), $h['time'] );
        $this->assertSame( '192.0.2.4', $h['ip'] );
        $this->assertSame( 'admin', $h['siteaccess'] );
        $this->assertSame( 'https://example.test/admin/content/view/full/2', $h['url'] );
        $this->assertSame( 'eZTemplate:', $h['message'] );

        $h = expLogParser::header( '[ Oct 17 2026 01:54:58 ] [192.0.2.4] Old style message' );
        $this->assertSame( 'debug', $h['format'] );
        $this->assertSame( '', $h['siteaccess'] );
        $this->assertSame( '', $h['url'] );
        $this->assertSame( 'Old style message', $h['message'] );

        $h = expLogParser::header( '[ Oct 17 2026 01:54:58 ] [192.0.2.4] [site] [https://example.test/a?b[0]=1&c=d] kernel/content/view.php:' );
        $this->assertSame( 'https://example.test/a?b[0]=1&c=d', $h['url'] );
        $this->assertSame( 'kernel/content/view.php:', $h['message'] );

        $this->assertTrue( expLogParser::startsEntry( '[ Oct 17 2026 01:54:58 ] [x] y' ) );
        $this->assertFalse( expLogParser::startsEntry( '[note] not a date' ) );
        $this->assertFalse( expLogParser::startsEntry( '' ) );
        $this->assertFalse( expLogParser::startsEntry( '    at frame' ) );
    }

    /** LV-02 */
    public function testOtherFormats()
    {
        $h = expLogParser::header( '[ Oct 17 2026 01:00:00 ][ admin ][ https://example.test/x ] stored a file' );
        $this->assertSame( 'log', $h['format'] );
        $this->assertSame( 'admin', $h['siteaccess'] );
        $this->assertSame( 'https://example.test/x', $h['url'] );
        $this->assertSame( 'stored a file', $h['message'] );

        $h = expLogParser::header( '[ Oct 17 2026 01:00:00 ] plain eZLog' );
        $this->assertSame( 'log', $h['format'] );
        $this->assertSame( 'plain eZLog', $h['message'] );

        $h = expLogParser::header( '2026-10-17T01:00:00+00:00 hello world' );
        $this->assertSame( 'iso', $h['format'] );
        $this->assertSame( strtotime( '2026-10-17T01:00:00+00:00' ), $h['time'] );
        $this->assertSame( 'hello world', $h['message'] );
        $h = expLogParser::header( '[2026-10-17 01:00:00] bracketed' );
        $this->assertSame( 'iso', $h['format'] );
        $this->assertSame( 'bracketed', $h['message'] );

        $h = expLogParser::header( '{"time":"2026-10-17T01:00:00Z","level":"error","message":"boom","ip":"192.0.2.9","url":"/x"}' );
        $this->assertSame( 'json', $h['format'] );
        $this->assertSame( strtotime( '2026-10-17T01:00:00Z' ), $h['time'] );
        $this->assertSame( '192.0.2.9', $h['ip'] );
        $this->assertSame( '/x', $h['url'] );
        $this->assertStringStartsWith( 'error: boom', $h['message'] );
        $h = expLogParser::header( '{"ts":1792200000123,"msg":"ms time"}' );
        $this->assertSame( 1792200000, $h['time'] );
        $this->assertNull( expLogParser::header( 'just text' ) );
        $this->assertNull( expLogParser::header( '' ) );
    }

    /** LV-03 */
    public function testContinuationAndOffsets()
    {
        $chunk = "tail of an entry\n  and more\n[ Oct 17 2026 01:00:00 ] [192.0.2.1] [admin] [https://example.test/a] eZDB:\nquery failed\n  #0 trace\n[ Oct 17 2026 01:00:01 ] [192.0.2.1] [admin] [https://example.test/a] next\n";
        $entries = expLogParser::parse( $chunk, 1000, '' );
        $this->assertCount( 3, $entries );
        $this->assertTrue( $entries[0]['cont'] );
        $this->assertSame( "tail of an entry\n  and more", $entries[0]['text'] );
        $this->assertSame( 1000, $entries[0]['offset'] );
        $this->assertFalse( $entries[1]['cont'] );
        $this->assertSame( 1000 + strpos( $chunk, '[ Oct 17 2026 01:00:00' ), $entries[1]['offset'] );
        $this->assertSame( $entries[2]['offset'], $entries[1]['end'] );
        $this->assertSame( 1000 + strlen( $chunk ), $entries[2]['end'] );
        $this->assertStringContainsString( '#0 trace', $entries[1]['text'] );
        $this->assertSame( array(), expLogParser::parse( '', 0 ) );
    }

    /** LV-04 */
    public function testLevel()
    {
        $this->assertSame( 'error', expLogParser::level( 'Fatal error: x' ) );
        $this->assertSame( 'error', expLogParser::level( 'PHP Fatal error: x' ) );
        $this->assertSame( 'error', expLogParser::level( '[critical] x' ) );
        $this->assertSame( 'warning', expLogParser::level( 'Warning: x', 'error' ) );
        $this->assertSame( 'warning', expLogParser::level( 'Strict Standards: x' ) );
        $this->assertSame( 'notice', expLogParser::level( 'Deprecated: x' ) );
        $this->assertSame( 'notice', expLogParser::level( 'info: x' ) );
        $this->assertSame( 'debug', expLogParser::level( 'debug: x' ) );
        $this->assertSame( 'error', expLogParser::level( 'eZTemplate: no level word', 'error' ) );
        $this->assertSame( 'notice', expLogParser::level( 'no level word' ) );
        $this->assertSame( 'notice', expLogParser::level( 'no level word', 'bogus' ) );
    }

    /** LV-04: the label alone on the header line (eZDebug's "Label:"), the next line names the level */
    public function testLevelFromTheLineAfterTheLabel()
    {
        $e = expLogParser::parse( "[ Oct 17 2026 01:00:00 ] [192.0.2.1] [admin] [https://example.test/a] eZDB:\nWarning: slow\n", 0, 'notice' );
        $this->assertSame( 'warning', $e[0]['level'] );
        $e = expLogParser::parse( "[ Oct 17 2026 01:00:00 ] [192.0.2.1] [admin] [https://example.test/a] eZDB:\nsomething\n", 0, 'error' );
        $this->assertSame( 'error', $e[0]['level'] );
    }

    /** LV-05 */
    public function testSource()
    {
        $h = expLogParser::header( '[ Oct 17 2026 01:00:00 ] [192.0.2.1] [admin] [https://example.test/a] eZTemplate:' );
        $this->assertSame( 'eZTemplate', expLogParser::source( $h ) );
        $h = expLogParser::header( '[ Oct 17 2026 01:00:00 ] [192.0.2.1] [admin] [https://example.test/a] kernel/content/view.php:' );
        $this->assertSame( 'kernel/content/view.php', expLogParser::source( $h ) );
        $h = expLogParser::header( '[ Oct 17 2026 01:00:00 ] [host] [admin] [php runcronjobs.php -s admin] ' );
        $this->assertSame( 'cli', expLogParser::source( $h ) );
        $h = expLogParser::header( '[ Oct 17 2026 01:00:00 ] [192.0.2.1] [admin] [https://example.test/a] ' );
        $this->assertSame( '', expLogParser::source( $h ) );
    }

    /** LV-06 */
    public function testReferencesMessageAndCut()
    {
        $e = expLogParser::parse( "[ Oct 17 2026 01:00:00 ] [192.0.2.1] [admin] [https://example.test/a] Error page ERR-0123456789 shown\nagain ERR-0123456789 and ERR-ABCDEF0123\n" );
        $this->assertSame( array( 'ERR-0123456789', 'ERR-ABCDEF0123' ), $e[0]['references'] );
        $this->assertSame( "Error page ERR-0123456789 shown\nagain ERR-0123456789 and ERR-ABCDEF0123", $e[0]['message'] );
        $this->assertSame( 'admin', $e[0]['siteaccess'] );
        $this->assertSame( 'https://example.test/a', $e[0]['url'] );
        $this->assertFalse( $e[0]['truncated'] );

        $big = "[ Oct 17 2026 01:00:00 ] [192.0.2.1] [admin] [https://example.test/a] Big:\n" . str_repeat( str_repeat( 'x', 999 ) . "\n", 100 );
        $e = expLogParser::parse( $big );
        $this->assertCount( 1, $e );
        $this->assertTrue( $e[0]['truncated'] );
        $this->assertLessThanOrEqual( expLogParser::MAX_ENTRY_BYTES, strlen( $e[0]['text'] ) );
        $this->assertSame( strlen( $big ), $e[0]['end'] );
    }

    /** LV-07 */
    public function testParts()
    {
        $text = "ERR-0123456789 at https://example.test/content/view/full/77. node_id=12 ContentObjectID: 34 user_id=5 class 'article' in design/admin/templates/a/b.tpl";
        $parts = expLogParser::parts( $text );
        $this->assertSame( $text, implode( '', array_map( function ( $p ) { return $p[0]; }, $parts ) ), 'the parts give the text back' );
        $links = array();
        foreach ( $parts as $p )
            if ( isset( $p[1] ) )
                $links[] = array( $p[1], $p[2] );
        $this->assertSame( array(
            array( 'reference', 'ERR-0123456789' ),
            array( 'url', 'https://example.test/content/view/full/77' ), // the node inside the address is not linked again
            array( 'node', '12' ),
            array( 'object', '34' ),
            array( 'user', '5' ),
            array( 'class', 'article' ),
            array( 'file', 'design/admin/templates/a/b.tpl' ),
        ), $links );

        // masked values are never links
        $parts = expLogParser::parts( 'go https://***@example.test/x and https://example.test/?ip=192.0.x.x' );
        foreach ( $parts as $p )
            $this->assertArrayNotHasKey( 1, $p );
        $this->assertSame( array( array( 'node_id=***' ) ), expLogParser::parts( 'node_id=***' ) );

        // the most links
        $parts = expLogParser::parts( 'node 1 node 2 node 3', 2 );
        $this->assertSame( 2, count( array_filter( $parts, function ( $p ) { return isset( $p[1] ); } ) ) );
        $this->assertSame( array(), expLogParser::parts( '' ) );
    }
}
