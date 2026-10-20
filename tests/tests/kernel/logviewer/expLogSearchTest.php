<?php
/**
 * Searching log files within limits (doc/en/us/features/6.0/log-viewer.md, "Searching"), on synthetic files.
 *
 *  LV-40  criteria(): levels, from/to (a day ends at 23:59:59), ip, reference, a text too long, an invalid expression
 *  LV-41  compile() and textMatches(): literal and regular expression, case, a ~ in the pattern
 *  LV-42  filters: level, siteaccess, request, ip (its start), reference; the text as the viewer shows it (redact)
 *  LV-43  a time range starts at the first entry of the range (seekTime) and reads a small part of a big file
 *  LV-44  paging: MaxResults stops with a cursor, and the next request goes on from it, nothing lost or repeated
 *  LV-45  the byte budget and the time budget stop with a cursor
 *  LV-46  a catastrophic expression stops with regex_limit, quickly, and pcre.backtrack_limit and pcre.jit are restored
 *  LV-47  compressed rotations are searched; several files in order; an invalid search reads nothing
 *  LV-48  with the viewer's redactor the request and the client address are compared as shown: a masked part of
 *         either is not found by guessing it; without masking they are compared as written; the values of secret
 *         settings are not found even unmasked
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expLogViewerTestFixtures.php';

class expLogSearchTest extends PHPUnit\Framework\TestCase
{
    const START = 1792195200; // a fixed time, as Unix time

    /** @var string */
    protected $root;

    protected function setUp(): void
    {
        $this->root = expLogViewerTestFixtures::root( 'lvsearch' );
    }

    protected function tearDown(): void
    {
        expLogViewerTestFixtures::remove( $this->root );
    }

    /** A file in var/log as the locator resolves it */
    protected function file( $name, $content, array $values = array() )
    {
        file_put_contents( $this->root . '/var/log/' . $name, $content );
        $l = new expLogFileLocator( expLogViewerTestFixtures::config( $this->root, $values ), 'var/log' );
        return $l->resolve( 'var/log/' . $name );
    }

    protected function search( array $values = array() )
    {
        return new expLogSearch( expLogViewerTestFixtures::config( $this->root, $values ) );
    }

    /** LV-40 */
    public function testCriteria()
    {
        $c = expLogSearch::criteria( array( 'q' => "  find\x01me ", 'levels' => 'error, Warning,bogus', 'from' => '2026-10-17', 'to' => '2026-10-17',
                                            'ip' => '192.0.2.', 'reference' => 'err-0123456789', 'siteaccess' => 'admin', 'uri' => '/content' ) );
        $this->assertSame( 'findme', $c['q'] );
        $this->assertSame( array( 'error', 'warning' ), $c['levels'] );
        $this->assertSame( strtotime( '2026-10-17' ), $c['from'] );
        $this->assertSame( strtotime( '2026-10-17' ) + 86399, $c['to'] );
        $this->assertSame( '192.0.2.', $c['ip'] );
        $this->assertSame( 'ERR-0123456789', $c['reference'] );
        $this->assertSame( array(), $c['invalid'] );

        $c = expLogSearch::criteria( array( 'levels' => array( 'error', 'warning', 'notice', 'debug' ), 'from' => '1792195200' ) );
        $this->assertSame( array(), $c['levels'], 'all levels is no filter' );
        $this->assertSame( 1792195200, $c['from'] );

        $c = expLogSearch::criteria( array( 'q' => str_repeat( 'a', 30 ), 'from' => 'not a time', 'ip' => 'x; DROP', 'reference' => 'ERR-XYZ' ), 20 );
        $this->assertSame( 20, strlen( $c['q'] ) );
        $this->assertSame( 'too_long', $c['invalid']['q'] );
        $this->assertSame( 'not_a_time', $c['invalid']['from'] );
        $this->assertSame( 'not_an_address', $c['invalid']['ip'] );
        $this->assertSame( '', $c['ip'] );
        $this->assertSame( 'not_a_reference', $c['invalid']['reference'] );

        $c = expLogSearch::criteria( array( 'q' => '([', 'regex' => '1' ) );
        $this->assertSame( 'invalid_regex', $c['invalid']['q'] );
        $c = expLogSearch::criteria( array( 'q' => array( 'x' ) ) );
        $this->assertSame( '', $c['q'] );
    }

    /** LV-41 */
    public function testCompileAndMatch()
    {
        $lit = expLogSearch::compile( 'Fatal', false, false );
        $this->assertTrue( expLogSearch::textMatches( $lit, false, 'a fatal one' ) );
        $this->assertFalse( expLogSearch::textMatches( $lit, true, 'a fatal one' ) );
        $this->assertTrue( expLogSearch::textMatches( expLogSearch::compile( '', false, false ), false, 'anything' ) );
        $re = expLogSearch::compile( 'node \d+', true, false );
        $this->assertSame( '', $re['error'] );
        $this->assertTrue( expLogSearch::textMatches( $re, false, 'NODE 12' ) );
        $this->assertFalse( expLogSearch::textMatches( expLogSearch::compile( 'node \d+', true, true ), true, 'NODE 12' ) );
        $tilde = expLogSearch::compile( 'a~b', true, false );
        $this->assertSame( '', $tilde['error'] );
        $this->assertTrue( expLogSearch::textMatches( $tilde, false, 'xa~by' ) );
        $this->assertSame( 'invalid_regex', expLogSearch::compile( '(?<', true, false )['error'] );
    }

    /** LV-42 */
    public function testFilters()
    {
        $t = date( 'M d Y H:i:s', self::START );
        $content = "[ $t ] [192.0.2.1] [admin] [https://example.test/admin/content/view/full/2] Warning: slow query\n"
                 . "[ $t ] [198.51.100.2] [site] [https://example.test/news] Error: failed ERR-0123456789 for jane@example.com\n"
                 . "[ $t ] [192.0.2.3] [admin] [https://example.test/admin/setup/cache] Notice: cleared\n";
        $file = $this->file( 'mixed.log', $content );
        $s = $this->search();
        $run = function ( array $in, $redact = null ) use ( $s, $file ) {
            $r = $s->run( array( $file ), expLogSearch::criteria( $in ), '', $redact );
            return array_map( function ( $e ) { return $e['url']; }, $r['entries'] );
        };
        $this->assertSame( array( 'https://example.test/news' ), $run( array( 'levels' => 'error' ) ) );
        $this->assertSame( array( 'https://example.test/admin/content/view/full/2', 'https://example.test/news' ), $run( array( 'levels' => 'error,warning' ) ) );
        $this->assertSame( array( 'https://example.test/news' ), $run( array( 'siteaccess' => 'SITE' ) ) );
        $this->assertSame( array( 'https://example.test/admin/setup/cache' ), $run( array( 'uri' => 'setup/' ) ) );
        $this->assertSame( array( 'https://example.test/admin/content/view/full/2', 'https://example.test/admin/setup/cache' ), $run( array( 'ip' => '192.0.2.' ) ) );
        $this->assertSame( array( 'https://example.test/news' ), $run( array( 'reference' => 'ERR-0123456789' ) ) );
        $this->assertSame( array(), $run( array( 'reference' => 'ERR-9999999999' ) ) );
        $this->assertSame( array( 'https://example.test/news' ), $run( array( 'q' => 'JANE' ) ) );
        $redactor = new expLogRedactor();
        $redact = function ( $text ) use ( $redactor ) { return $redactor->text( $text ); };
        $this->assertSame( array(), $run( array( 'q' => 'jane' ), $redact ), 'a masked address is not found by its hidden value' );
        $this->assertSame( array( 'https://example.test/news' ), $run( array( 'q' => 'failed' ), $redact ) );
        $this->assertSame( array( 'https://example.test/admin/content/view/full/2' ), $run( array( 'q' => 'slow\s+QUERY', 'regex' => 1 ) ) );
    }

    /** LV-48 */
    public function testMaskedCriteriaCompareWhatIsShown()
    {
        $t = date( 'M d Y H:i:s', self::START );
        $content = "[ $t ] [203.0.113.7] [admin] [https://example.test/user/reset?token=abc123def456&mail=jane@example.com] Error: reset failed\n"
                 . "[ $t ] [203.0.200.9] [admin] [https://example.test/news] Error: DB login with Sup3rS3cretValue refused\n"
                 . "[ $t ] [2001:db8:85a3::8a2e:370:7334] [admin] [https://example.test/v6] Error: from v6\n";
        $file = $this->file( 'masked.log', $content );
        $s = $this->search();
        $run = function ( array $in, $redact = null ) use ( $s, $file ) {
            $r = $s->run( array( $file ), expLogSearch::criteria( $in ), '', $redact );
            return array_map( function ( $e ) { return $e['url']; }, $r['entries'] );
        };
        $reset = 'https://example.test/user/reset?token=abc123def456&mail=jane@example.com';
        $news = 'https://example.test/news';
        $v6 = 'https://example.test/v6';
        $masked = new expLogRedactor( true, true, true, array( 'Sup3rS3cretValue' ) );
        $unmasked = new expLogRedactor( false, false, false, array( 'Sup3rS3cretValue' ) );

        // shown as 203.0.x.x: the shown prefix finds both, any guess at the hidden part finds nothing
        $this->assertSame( array( $reset, $news ), $run( array( 'ip' => '203.0.' ), $masked ) );
        $this->assertSame( array(), $run( array( 'ip' => '203.0.1' ), $masked ), 'the host part of a masked address is not an oracle' );
        $this->assertSame( array(), $run( array( 'ip' => '203.0.113' ), $masked ) );
        $this->assertSame( array(), $run( array( 'ip' => '203.0.113.7' ), $masked ) );
        $this->assertSame( array( $v6 ), $run( array( 'ip' => '2001:db8:' ), $masked ) );
        $this->assertSame( array(), $run( array( 'ip' => '2001:db8:85a3' ), $masked ) );
        // the request: a masked token or e-mail address is not found by its hidden value, the rest is
        $this->assertSame( array(), $run( array( 'uri' => 'token=abc' ), $masked ), 'a masked part of the request is not an oracle' );
        $this->assertSame( array(), $run( array( 'uri' => 'jane@' ), $masked ) );
        $this->assertSame( array( $reset ), $run( array( 'uri' => '/user/reset?token=' ), $masked ) );
        // nor the text
        $this->assertSame( array(), $run( array( 'q' => 'Sup3rS3cret' ), $masked ) );

        // unmasked (setup/unmasklogs, Show unmasked): compared as written, as without a redactor
        foreach ( array( null, $unmasked ) as $redact )
        {
            $this->assertSame( array( $reset ), $run( array( 'ip' => '203.0.113' ), $redact ) );
            $this->assertSame( array( $reset ), $run( array( 'ip' => '203.0.113.7' ), $redact ) );
            $this->assertSame( array( $v6 ), $run( array( 'ip' => '2001:db8:85a3' ), $redact ) );
            $this->assertSame( array( $reset ), $run( array( 'uri' => 'mail=jane@example.com' ), $redact ) );
            $this->assertSame( array( $news ), $run( array( 'q' => 'refused' ), $redact ) );
        }
        // the value of a secret setting and a token in an address are hidden from every viewer, so they are not found
        // unmasked either
        $this->assertSame( array( $news ), $run( array( 'q' => 'Sup3rS3cret' ) ), 'without a redactor: as written' );
        $this->assertSame( array(), $run( array( 'q' => 'Sup3rS3cret' ), $unmasked ) );
        $this->assertSame( array( $reset ), $run( array( 'uri' => 'token=abc123' ) ), 'without a redactor: as written' );
        $this->assertSame( array(), $run( array( 'uri' => 'token=abc123' ), $unmasked ) );

        // entryMatches() alone
        $entry = expLogParser::parse( "[ $t ] [203.0.113.7] [admin] [https://example.test/a?token=abc123def456] Error: x\n" )[0];
        $c = expLogSearch::criteria( array( 'ip' => '203.0.113', 'uri' => 'token=abc' ) );
        $this->assertSame( 1, expLogSearch::entryMatches( $entry, $c ) );
        $this->assertSame( 1, expLogSearch::entryMatches( $entry, expLogSearch::criteria( array( 'ip' => '203.0.113' ) ), $unmasked ) );
        $this->assertSame( 0, expLogSearch::entryMatches( $entry, $c, $unmasked ), 'the token is hidden unmasked too' );
        $this->assertSame( 0, expLogSearch::entryMatches( $entry, $c, $masked ) );
        $this->assertSame( 0, expLogSearch::entryMatches( $entry, expLogSearch::criteria( array( 'ip' => '203.0.113' ) ), $masked ) );
        $this->assertSame( 0, expLogSearch::entryMatches( $entry, expLogSearch::criteria( array( 'uri' => 'token=abc' ) ), $masked ) );
        $this->assertSame( 1, expLogSearch::entryMatches( $entry, expLogSearch::criteria( array( 'ip' => '203.0.', 'uri' => 'token=' ) ), $masked ) );
    }

    /** LV-43 */
    public function testTimeRangeSeeks()
    {
        $content = expLogViewerTestFixtures::debugLines( 40000, self::START );
        $file = $this->file( 'big.log', $content );
        $this->assertGreaterThan( 3000000, $file['size'] );
        $from = self::START + 30000;
        $offset = expLogSearch::seekTime( $file['path'], $from, $file['size'] );
        $this->assertGreaterThan( (int)( $file['size'] * 0.6 ), $offset );
        $this->assertSame( "\n", $content[$offset - 1], 'starts at a line' );
        // the first entry after it
        $first = expLogParser::parse( substr( $content, $offset, 4096 ) );
        $first = $first[0]['cont'] ? $first[1] : $first[0];

        $this->assertLessThanOrEqual( $from, $first['time'], 'never late' );
        $this->assertSame( 0, expLogSearch::seekTime( $file['path'], self::START - 100, $file['size'] ) );

        $r = $this->search()->run( array( $file ), expLogSearch::criteria( array( 'from' => (string)$from, 'to' => (string)( $from + 4 ) ) ) );
        $this->assertCount( 5, $r['entries'] );
        $this->assertSame( $from, $r['entries'][0]['time'] );
        $this->assertLessThan( $file['size'] * 0.4, $r['scanned'], 'most of the file is never read' );
        $this->assertSame( '', $r['cursor'] );
    }

    /** LV-44 */
    public function testPaging()
    {
        $file = $this->file( 'page.log', expLogViewerTestFixtures::debugLines( 10, self::START ) );
        $s = $this->search( array( 'SearchSettings' => array( 'MaxResults' => 3 ) ) );
        $c = expLogSearch::criteria( array( 'q' => 'Error number' ) );
        $seen = array();
        $cursor = '';
        $requests = 0;
        do
        {
            $r = $s->run( array( $file ), $c, $cursor );
            $this->assertLessThanOrEqual( 3, count( $r['entries'] ) );
            foreach ( $r['entries'] as $e )
                $seen[] = (int)preg_replace( '/\D+/', '', strstr( $e['text'], 'number' ) );
            $cursor = $r['cursor'];
            if ( $cursor !== '' )
                $this->assertSame( 'results', $r['stopped'] );
            $requests++;
        }
        while ( $cursor !== '' && $requests < 10 );
        $this->assertSame( range( 0, 9 ), $seen );
        $this->assertSame( 4, $requests );
        $this->assertSame( 'var/log/page.log', $r['files'][0] );
    }

    /** LV-45 */
    public function testBudgets()
    {
        $file = $this->file( 'budget.log', expLogViewerTestFixtures::debugLines( 4000, self::START ) );
        $c = expLogSearch::criteria( array( 'q' => 'never in the file' ) );
        $r = $this->search( array( 'SearchSettings' => array( 'MaxBytesPerRequest' => 65536 ) ) )->run( array( $file ), $c );
        $this->assertSame( 'bytes', $r['stopped'] );
        $this->assertMatchesRegularExpression( '/^0:\d+$/', $r['cursor'] );
        $this->assertLessThan( 200000, $r['scanned'] );
        $offset = (int)substr( $r['cursor'], 2 );
        $this->assertSame( '[', file_get_contents( $file['path'], false, null, $offset, 1 ), 'the cursor is the start of an entry' );

        $s = $this->search();
        $s->timeBudget = 0.0;
        $r = $s->run( array( $file ), $c );
        $this->assertSame( 'time', $r['stopped'] );
        $this->assertNotSame( '', $r['cursor'] );
        $r2 = $s->run( array( $file ), $c, $r['cursor'] );
        $this->assertGreaterThan( (int)substr( $r['cursor'], 2 ), (int)substr( $r2['cursor'], 2 ), 'the next request goes on' );
    }

    /** LV-46 */
    public function testCatastrophicRegex()
    {
        $file = $this->file( 'evil.log', str_repeat( 'a', 5000 ) . "b\n" . "[ Oct 17 2026 01:00:00 ] [x] after\n" );
        $limit = ini_get( 'pcre.backtrack_limit' );
        $jit = ini_get( 'pcre.jit' );
        $s = $this->search( array( 'SearchSettings' => array( 'BacktrackLimit' => 1000 ) ) );
        $t = microtime( true );
        $r = $s->run( array( $file ), expLogSearch::criteria( array( 'q' => '(a+)+$', 'regex' => 1 ) ) );
        $this->assertLessThan( 2.0, microtime( true ) - $t );
        $this->assertSame( 'regex_limit', $r['error'] );
        $this->assertSame( 'regex_limit', $r['stopped'] );
        $this->assertSame( $limit, ini_get( 'pcre.backtrack_limit' ) );
        $this->assertSame( $jit, ini_get( 'pcre.jit' ) );

        $this->expectException( expLogSearchLimitException::class );
        ini_set( 'pcre.jit', '0' );
        ini_set( 'pcre.backtrack_limit', '1000' );
        try
        {
            expLogSearch::textMatches( expLogSearch::compile( '(a+)+$', true, false ), false, str_repeat( 'a', 5000 ) . 'b' );
        }
        finally
        {
            ini_set( 'pcre.backtrack_limit', (string)$limit );
            ini_set( 'pcre.jit', (string)$jit );
        }
    }

    /** LV-47 */
    public function testGzipOrderAndInvalid()
    {
        $plain = $this->file( 'error.log', expLogViewerTestFixtures::debugLines( 3, self::START + 100 ) );
        file_put_contents( $this->root . '/var/log/error.log.2.gz', gzencode( expLogViewerTestFixtures::debugLines( 3, self::START ) ) );
        $l = new expLogFileLocator( expLogViewerTestFixtures::config( $this->root ), 'var/log' );
        $gz = $l->resolve( 'var/log/error.log.2.gz' );
        $r = $this->search()->run( array( $plain, $gz ), expLogSearch::criteria( array( 'q' => 'number 1 ' ) ) );
        $this->assertSame( array( 'var/log/error.log', 'var/log/error.log.2.gz' ), array_column( $r['entries'], 'file' ) );
        $this->assertSame( array( 'var/log/error.log', 'var/log/error.log.2.gz' ), $r['files'] );
        $this->assertSame( 'error', $r['entries'][1]['level'] );

        // a cursor into the second file starts there
        $r = $this->search()->run( array( $plain, $gz ), expLogSearch::criteria( array( 'q' => 'number' ) ), '1:0' );
        $this->assertSame( array( 'var/log/error.log.2.gz' ), array_unique( array_column( $r['entries'], 'file' ) ) );

        $r = $this->search()->run( array( $plain ), expLogSearch::criteria( array( 'from' => 'nope' ) ) );
        $this->assertSame( 'invalid', $r['error'] );
        $this->assertSame( 0, $r['scanned'] );
    }
}
