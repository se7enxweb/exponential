<?php
/**
 * The import of the 4.x text audit logs (doc/bc/6.0/audit.md, "Import of the old logs (Z4)", acceptance test E4).
 *
 *  IM-01 — the current header form and the older one without siteaccess and address; rotated copies first
 *  IM-02 — records are marked imported with their source (file, line, sha256), carry no seq/prev/hash (outside
 *          the chain), go to <LogDir>/imported/<channel>-<date>.jsonl by the mapped name, with the privacy rules
 *          applied (the address truncated, the attempted login of an unknown account hashed, HashKey never kept)
 *  IM-03 — the counts equal the entries; a dry run writes nothing; a second import is skipped (same sha256); a file
 *          that grew is imported from where the last import stopped; ids are stable
 *  IM-04 — the originals are archived into <ArchiveDir>/legacy with a manifest whose HMAC verifies; with
 *          keepOriginals they stay, otherwise they are removed; the live chains are untouched
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group audit
 */

require_once __DIR__ . '/fixtures/expaudittestfixtures.php';

class expAuditImportTest extends PHPUnit\Framework\TestCase
{
    protected $dir;

    protected $old;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dir = expAuditTestFixtures::setUp( $this->name(), array( 'AuditSettings/AuditFileNames' => array(
            'user-login' => 'login.log', 'user-failed-login' => 'failed_login.log', 'user-forgotpassword-fail' => 'forgot.log' ) ) );
        date_default_timezone_set( 'UTC' );
        $this->old = $this->dir . 'old';
        mkdir( $this->old );
        file_put_contents( $this->old . '/login.log.1',
            "[ Sep 30 2026 08:00:01 ] [203.0.113.7] [editor1:14]\nUser id: 14\nUser login: editor1\n\n" );
        file_put_contents( $this->old . '/login.log',
            "[ Oct 02 2026 15:37:00 ][ admin ][ https://example.com:8080/admin/user/login ] [203.0.113.8] [anonymous:10]\nUser id: 14\nUser login: editor1\n\n" .
            "[ Oct 02 2026 15:40:00 ][ admin ][ https://example.com/admin/user/login ] [2001:db8:12:34::1] [anonymous:10]\nUser id: 15\nUser login: editor2\n\n" );
        file_put_contents( $this->old . '/failed_login.log',
            "[ Oct 01 2026 22:10:00 ][ admin ][ https://example.com/admin/user/login ] [203.0.113.9] [anonymous:10]\nUser login: my-secret-password\nComment: Failed login attempt: eZUser::loginUser()\n\n" );
        file_put_contents( $this->old . '/forgot.log',
            "[ Oct 01 2026 09:00:00 ][ site ][ https://example.com/user/forgotpassword/abc ] [203.0.113.10] [anonymous:10]\nHashKey: 0123456789abcdef\nComment: HashKey not found\n\n" );
    }

    protected function tearDown(): void
    {
        expAuditTestFixtures::tearDown();
        parent::tearDown();
    }

    /** @return array[] The imported records */
    protected function imported()
    {
        $out = array();
        foreach ( glob( $this->dir . 'log/imported/*.jsonl' ) as $f )
            foreach ( file( $f, FILE_IGNORE_NEW_LINES ) as $l )
                $out[basename( $f )][] = json_decode( $l, true );
        ksort( $out );
        return $out;
    }

    /** IM-01 .. IM-04 */
    public function testImport()
    {
        $importer = new expAuditImporter( $this->old );
        $this->assertSame( array( $this->old . '/login.log.1', $this->old . '/login.log', $this->old . '/failed_login.log', $this->old . '/forgot.log' ),
                           array_keys( $importer->files() ), 'rotated copies first' );

        $dry = $importer->import( array( 'dryRun' => true ) );
        $this->assertSame( 5, $dry['records'] );
        $this->assertSame( array(), glob( $this->dir . 'log/imported/*.jsonl' ), 'a dry run writes nothing' );

        $r = $importer->import( array( 'keepOriginals' => true ) );
        $this->assertSame( 5, $r['records'] );
        foreach ( $r['files'] as $path => $e )
            $this->assertSame( $e['entries'], $e['records'], basename( $path ) . ': every entry imported' );
        $files = $this->imported();
        $this->assertSame( array( 'access-2026-09-30.jsonl', 'access-2026-10-01.jsonl', 'access-2026-10-02.jsonl' ), array_keys( $files ) );

        $older = $files['access-2026-09-30.jsonl'][0];
        $this->assertSame( 'access.session.login', $older['name'] );
        $this->assertTrue( $older['imported'] );
        $this->assertSame( array( 'file' => 'login.log.1', 'line' => 1, 'sha256' => hash_file( 'sha256', $this->old . '/login.log.1' ) ), $older['source'] );
        $this->assertSame( '2026-09-30T08:00:01.000Z', $older['time'] );
        $this->assertSame( '203.0.113.0/24', $older['actor']['ip'], 'the privacy rules: the address truncated' );
        $this->assertSame( 14, $older['actor']['user_id'] );
        foreach ( array( 'seq', 'prev', 'hash' ) as $k )
            $this->assertArrayNotHasKey( $k, $older, 'outside the chain' );

        $current = $files['access-2026-10-02.jsonl'];
        $this->assertCount( 2, $current );
        $this->assertSame( 'admin', $current[0]['request']['siteaccess'] );
        $this->assertSame( '/admin/user/login', $current[0]['request']['url'] );
        $this->assertSame( 'editor1', $current[0]['actor']['login'], 'the user who logged in, not anonymous' );
        $this->assertSame( '2001:db8:12::/48', $current[1]['actor']['ip'] );

        $day1 = $files['access-2026-10-01.jsonl'];
        $failed = $day1[0]['name'] === 'access.session.login.failed' ? $day1[0] : $day1[1];
        $reset = $day1[0]['name'] === 'access.session.login.failed' ? $day1[1] : $day1[0];
        $this->assertSame( 'failed', $failed['result'] );
        $this->assertStringStartsWith( 'h:', $failed['object']['attempted_login'], 'an unknown account\'s typed login is hashed' );
        $this->assertStringNotContainsString( 'my-secret-password', json_encode( $files ) );
        $this->assertSame( 'access.user.password.reset.failed', $reset['name'] );
        $this->assertSame( 'unknown_key', $reset['reason'] );
        $this->assertStringNotContainsString( '0123456789abcdef', json_encode( $files ), 'HashKey is never kept' );
        $this->assertSame( '/user/forgotpassword/…', $reset['request']['url'], 'a secret path parameter is cut' );

        // IM-03: again
        $again = $importer->import( array( 'keepOriginals' => true ) );
        $this->assertSame( 0, $again['records'] );
        foreach ( $again['files'] as $e )
            $this->assertSame( 'imported before (same sha256)', $e['skipped'] );
        file_put_contents( $this->old . '/login.log', "[ Oct 02 2026 16:00:00 ][ admin ][ https://example.com/admin/user/login ] [203.0.113.8] [anonymous:10]\nUser id: 14\nUser login: editor1\n\n", FILE_APPEND );
        $grown = $importer->import( array( 'keepOriginals' => true ) );
        $this->assertSame( 1, $grown['records'], 'only the entry added since' );
        $this->assertCount( 3, $this->imported()['access-2026-10-02.jsonl'] );
        $ids = array_column( $this->imported()['access-2026-10-02.jsonl'], 'id' );
        $this->assertSame( $ids, array_unique( $ids ) );
        $this->assertSame( $older['id'], expAuditImporter::stableId( strtotime( '2026-09-30 08:00:01 UTC' ) * 1000, $older['source']['sha256'] . ':1' ), 'stable ids' );

        // IM-04
        $this->assertNotNull( $r['archive'] );
        $m = json_decode( file_get_contents( $r['archive'] ), true );
        $this->assertSame( 'exponential-audit-legacy-manifest', $m['format'] );
        $this->assertCount( 4, $m['files'] );
        $unsigned = expAuditJson::decode( trim( file_get_contents( $r['archive'] ) ) );
        unset( $unsigned->hmac );
        $this->assertSame( 'ok', ( new expAuditKeys( expAuditConfig::get() ) )->verify( $unsigned, $m['hmac'], $m['key_id'] ) );
        foreach ( $m['files'] as $f )
        {
            $h = new expAuditGzipFormat();
            $s = $h->open( dirname( $r['archive'] ) . '/' . $f['archive'] );
            $this->assertSame( $f['sha256'], expAuditFormatBase::digestStream( $s )['sha256'] );
            expAuditFormatBase::close( $s );
        }
        $this->assertFileExists( $this->old . '/login.log', 'keepOriginals' );
        $removing = new expAuditImporter( $this->old );
        file_put_contents( $this->old . '/failed_login.log', "[ Oct 02 2026 01:00:00 ][ admin ][ https://example.com/admin/user/login ] [203.0.113.9] [anonymous:10]\nUser login: someone\n\n", FILE_APPEND );
        $r2 = $removing->import( array( 'file' => 'failed_login.log' ) );
        $this->assertSame( 1, $r2['records'] );
        $this->assertFileDoesNotExist( $this->old . '/failed_login.log', 'archived, then removed' );

        // the import events are in the chain, the imported records are not
        $system = expAuditWriter::channelFiles( $this->dir . 'log', 'system' );
        $names = array_map( function ( $l ) { return json_decode( $l, true )['name']; }, file( $this->dir . 'log/' . $system[0], FILE_IGNORE_NEW_LINES ) );
        $this->assertContains( 'system.audit.import', $names );
        $this->assertSame( array(), expAuditWriter::channelFiles( $this->dir . 'log', 'access' ), 'no live access file was written' );
        $this->assertSame( 'intact', expAuditTestFixtures::verifier( $this->dir )->verifyChannel( 'system' )['result'] );
    }
}
