<?php
/**
 * The error digest of the log viewer (doc/eng-us/features/6.0/log-viewer.md, "Error digest"), without a database.
 *
 *  LV-130  headline(): the first line, with the next one when the first is only a label
 *  LV-131  normalize(): numbers, hex, uuids, quoted values, paths and ERR references set aside; whitespace collapsed
 *  LV-132  literalOf(): the longest part without a placeholder or a mask, at least 4 characters
 *  LV-133  the known list: parsed, added, removed, bounded, malformed ids dropped
 *  LV-134  digestFile(): only the window, only the levels, grouped and counted, most frequent first, the newest sample
 *  LV-135  masked before grouping: no e-mail or IP address in the text, the sample or the search literal
 *  LV-136  known groups hidden (counted) or listed marked; MaxMessages caps the groups
 *  LV-137  bounded: the byte cap stops the read and says so; the window's start is found by seekTime
 *  LV-138  the links: the filtered permalink (literal, level, from) and the newest entry (paused at its offset)
 *  LV-139  the view's paths, the settings bounds, and the template: both designs equal, no inline CSS, POST with token
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expLogDigestTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    protected $dir;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir() . '/expLogDigestTest-' . getmypid() . '-' . mt_rand();
        mkdir( $this->dir . '/var/log', 0777, true );
    }

    protected function tearDown(): void
    {
        foreach ( (array)glob( $this->dir . '/var/log/*' ) as $f )
            @unlink( $f );
        @rmdir( $this->dir . '/var/log' );
        @rmdir( $this->dir . '/var' );
        @rmdir( $this->dir );
    }

    protected function entry( $t, $label, $message, $ip = '192.0.2.7' )
    {
        return '[ ' . date( 'M d Y H:i:s', $t ) . " ] [$ip] [admin] [https://example.test/x] $label:\n$message\n";
    }

    protected function digest( array $values = array() )
    {
        $config = expLogViewerConfig::fromArray( $values, $this->dir );
        return array( new expLogDigest( $config, new expLogRedactor( true, true, true, array() ) ), new expLogFileLocator( $config, 'var/log' ) );
    }

    /** LV-130 */
    public function testHeadline()
    {
        $this->assertSame( 'eZDB: Query failed', expLogDigest::headline( "eZDB:\nQuery failed\nmore" ) );
        $this->assertSame( 'Error: x happened', expLogDigest::headline( "Error: x happened\nstack" ) );
        $this->assertSame( 'only', expLogDigest::headline( 'only' ) );
    }

    /** LV-131 */
    public function testNormalize()
    {
        $this->assertSame( 'Node # not found', expLogDigest::normalize( 'Node 12 not found' ) );
        $this->assertSame( expLogDigest::normalize( 'Node 12 not found' ), expLogDigest::normalize( 'Node  9031 not   found' ) );
        $this->assertSame( "Template '?' missing in <path>", expLogDigest::normalize( "Template 'foo.tpl' missing in design/admin4/templates" ) );
        $this->assertSame( 'key <hex> and <hex> and <uuid>', expLogDigest::normalize( 'key 0xff12 and deadbeef01 and 123e4567-e89b-12d3-a456-426614174000' ) );
        $this->assertSame( 'ref ERR-? seen', expLogDigest::normalize( 'ref ERR-0123456789 seen' ) );
        $this->assertSame( 'eZ4 says "?"', expLogDigest::normalize( 'eZ4 says "hello world"' ) );
        $this->assertLessThanOrEqual( expLogDigest::MAX_TEXT, strlen( expLogDigest::normalize( str_repeat( 'word ', 200 ) ) ) );
    }

    /** LV-132 */
    public function testLiteralOf()
    {
        $this->assertSame( 'not found in section', expLogDigest::literalOf( 'Node # not found in section #' ) );
        $this->assertSame( 'Query failed', expLogDigest::searchLine( "eZDB:\nQuery failed\nmore" ) );
        $this->assertSame( 'Error: x', expLogDigest::searchLine( "Error: x\nstack" ) );
        $this->assertSame( '', expLogDigest::literalOf( '# # <hex>' ) );
        $this->assertSame( 'user', expLogDigest::literalOf( 'user *** at #' ) );
        $this->assertStringNotContainsString( '***', expLogDigest::literalOf( 'mail for j***@***.com failed badly' ) );
        $this->assertLessThanOrEqual( 80, strlen( expLogDigest::literalOf( str_repeat( 'a', 200 ) ) ) );
    }

    /** LV-133 */
    public function testKnownList()
    {
        $this->assertSame( array(), expLogDigest::knownFrom( null ) );
        $this->assertSame( array( 'aaaaaaaaaaaa', 'bbbbbbbbbbbb' ), expLogDigest::knownFrom( 'aaaaaaaaaaaa,bad,bbbbbbbbbbbb,aaaaaaaaaaaa' ) );
        $v = expLogDigest::withKnown( '', 'cccccccccccc', true );
        $this->assertSame( 'cccccccccccc', $v );
        $this->assertSame( '', expLogDigest::withKnown( $v, 'cccccccccccc', false ) );
        $this->assertSame( 'cccccccccccc', expLogDigest::withKnown( $v, 'not-an-id', true ) );
        $many = '';
        for ( $i = 0; $i < 250; $i++ )
            $many = expLogDigest::withKnown( $many, substr( sha1( (string)$i ), 0, 12 ), true );
        $this->assertCount( expLogDigest::MAX_KNOWN, expLogDigest::knownFrom( $many ) );
        $this->assertContains( substr( sha1( '249' ), 0, 12 ), expLogDigest::knownFrom( $many ), 'the newest is kept' );
    }

    /** LV-134, LV-135 */
    public function testDigestFile()
    {
        $now = time();
        $log = $this->entry( $now - 7200 * 20, 'eZDB', 'Error: Node 1 not found' ); // outside 24 h
        for ( $i = 0; $i < 5; $i++ )
            $log .= $this->entry( $now - 3600 + $i, 'eZDB', 'Error: Node ' . ( 100 + $i ) . ' not found' );
        $log .= $this->entry( $now - 60, 'kernel', 'Warning: mail to jane.doe@example.com from 198.51.100.4 failed' );
        $log .= $this->entry( $now - 30, 'kernel', 'Notice: just a notice 5' );
        file_put_contents( $this->dir . '/var/log/error.log', $log );
        list( $digest, $loc ) = $this->digest();
        $file = $loc->resolveKey( 'error' );
        $d = $digest->digestFile( $file, $now - 86400 );
        $this->assertSame( 'error', $d['key'] );
        $this->assertSame( 6, $d['total'], 'errors and warnings of the window; the old entry and the notice left out' );
        $this->assertCount( 2, $d['groups'] );
        $g = $d['groups'][0];
        $this->assertSame( 5, $g['count'] );
        $this->assertSame( 'eZDB: Error: Node # not found', $g['text'] );
        $this->assertSame( 'eZDB: Error: Node 104 not found', $g['sample'], 'the newest entry is the sample' );
        $this->assertSame( 'error', $g['level'] );
        $this->assertSame( $now - 3600 + 4, $g['last'] );
        $this->assertSame( $now - 3600, $g['first'] );
        $w = $d['groups'][1];
        $this->assertSame( 'warning', $w['level'] );
        foreach ( array( $w['text'], $w['sample'], $w['literal'] ) as $s )
        {
            $this->assertStringNotContainsString( 'jane.doe@example.com', $s );
            $this->assertStringNotContainsString( '198.51.100.4', $s );
        }
        $this->assertSame( expLogDigest::groupId( 'error', $g['text'] ), $g['id'] );
        $this->assertSame( 'Error: Node', $g['literal'], 'from the message line, which the file holds as it is' );
    }

    /** LV-136 */
    public function testKnownAndCap()
    {
        $now = time();
        $log = '';
        for ( $i = 0; $i < 4; $i++ )
            $log .= $this->entry( $now - 100 + $i, 'eZDB', 'Error: distinct problem ' . str_repeat( 'x', $i + 1 ) );
        file_put_contents( $this->dir . '/var/log/error.log', $log );
        list( $digest, $loc ) = $this->digest( array( 'DigestSettings' => array( 'MaxMessages' => 2 ) ) );
        $file = $loc->resolveKey( 'error' );
        $all = $digest->digestFile( $file, $now - 3600 );
        $this->assertCount( 2, $all['groups'], 'MaxMessages' );
        $known = array( $all['groups'][0]['id'] );
        $hidden = $digest->digestFile( $file, $now - 3600, $known, false );
        $this->assertSame( 1, $hidden['hidden'] );
        $this->assertNotContains( $known[0], array_column( $hidden['groups'], 'id' ) );
        $shown = $digest->digestFile( $file, $now - 3600, $known, true );
        $this->assertSame( 0, $shown['hidden'] );
        $marked = array_values( array_filter( $shown['groups'], function ( $g ) use ( $known ) { return $g['id'] === $known[0]; } ) );
        $this->assertTrue( $marked[0]['known'] );
    }

    /** LV-137 */
    public function testBounded()
    {
        $now = time();
        $old = '';
        while ( strlen( $old ) < 400000 )
            $old .= $this->entry( $now - 90000, 'eZDB', 'Error: old ' . strlen( $old ) );
        $new = '';
        while ( strlen( $new ) < 300000 )
            $new .= $this->entry( $now - 50, 'eZDB', 'Error: fresh problem' );
        file_put_contents( $this->dir . '/var/log/error.log', $old . $new );
        list( $digest, $loc ) = $this->digest( array( 'DigestSettings' => array( 'MaxBytesPerFile' => 65536 ) ) );
        $file = $loc->resolveKey( 'error' );
        $d = $digest->digestFile( $file, $now - 3600 );
        $this->assertTrue( $d['stopped'] );
        $this->assertLessThanOrEqual( 65536, $d['scanned'] );
        $this->assertSame( 'eZDB: Error: fresh problem', $d['groups'][0]['text'], 'the read starts in the window (seekTime)' );
        list( $digest2, ) = $this->digest();
        $full = $digest2->digestFile( $file, $now - 3600 );
        $this->assertFalse( $full['stopped'] );
        $this->assertLessThan( strlen( $old . $new ), $full['scanned'] + 1, 'never more than the file' );
    }

    /** LV-138 */
    public function testLinks()
    {
        $since = mktime( 10, 0, 0, 10, 17, 2026 );
        $g = array( 'key' => 'error', 'level' => 'error', 'literal' => 'not found', 'last_offset' => 4096 );
        $this->assertSame( 'setup/logs/view/error/(level)/error/(search)/not%20found/(from)/2026-10-17%2010:00', expLogDigest::link( $g, $since ) );
        $g['literal'] = '';
        $this->assertStringNotContainsString( '(search)', expLogDigest::link( $g, $since ) );
        $this->assertSame( 'setup/logs/view/error/(offset)/4096/(follow)/0', expLogDigest::entryLink( $g ) );
    }

    /** LV-139 */
    public function testViewPathsSettingsAndTemplates()
    {
        $this->assertSame( 'setup/logsdigest', \Exponential\View\Kernel\Setup\Logsdigest::path( '', 24, false, 24 ) );
        $this->assertSame( 'setup/logsdigest/error/(hours)/6/(known)/1', \Exponential\View\Kernel\Setup\Logsdigest::path( 'error', 6, true, 24 ) );
        list( $digest, ) = $this->digest( array( 'DigestSettings' => array( 'Hours' => 9999, 'MaxMessages' => 0 ) ) );
        $this->assertSame( 168, $digest->setting( 'Hours' ) );
        $this->assertSame( 1, $digest->setting( 'MaxMessages' ) );
        $this->assertSame( array( 'error', 'warning' ), $digest->levels() );
        $root = dirname( __DIR__, 4 );
        $a = file_get_contents( "$root/design/admin/templates/setup/logs_digest.tpl" );
        $this->assertSame( $a, file_get_contents( "$root/design/admin4/templates/setup/logs_digest.tpl" ) );
        $this->assertDoesNotMatchRegularExpression( '/<style|style="/i', $a );
        $this->assertStringContainsString( 'method="post"', $a );
        $this->assertStringContainsString( 'name="{$digest_token_field|wash}" value="{$digest_token|wash}"', $a );
        $ini = file_get_contents( "$root/settings/logviewer.ini" );
        $this->assertMatchesRegularExpression( '/^\[DigestSettings\]$/m', $ini );
        $this->assertMatchesRegularExpression( '/^Hours=24$/m', $ini );
    }
}
