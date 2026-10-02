<?php
/**
 * Rotation by day, archives, manifests, verification, restore, retention and key rotation (doc/bc/6.0/audit.md,
 * "Rotation, archives and retention (Q8)", "The archive manifest and its HMAC", acceptance tests E3 and the archive
 * cases T9-T12 of the tamper test).
 *
 *  AR-01 — rotation by day: a channel nobody wrote to since midnight gets system.audit.file.close from rotate();
 *          the next day's file opens linked to it; the chain is intact with no notice
 *  AR-02 — each format handler (gzip, bzip2, xz, zstd, zip; a missing one falls back to gzip): the days older than
 *          LiveDays are archived with a signed manifest, read back, the live files removed; the archives verify
 *          intact, the live chain verifies intact from the archived file it starts from, and a restored day is
 *          byte-identical to the live file that was archived
 *  AR-03 — T9 one byte of an archive changed: archive_sha256; T10 a manifest edited: hmac_invalid; T11 a manifest
 *          signed with a key id not in the settings: unknown_key; T12 one day's manifest and archive removed:
 *          previous_manifest; T0 untouched: intact
 *  AR-04 — retention: a dry run lists what is due and removes nothing; the real run removes archives older than
 *          ArchiveDays, lists them with their sha256 in the ledger and in system.audit.purge, keeps the keys in use,
 *          and what is left still verifies intact (the first manifest's link is in the ledger)
 *  AR-05 — key rotation: a new signing key becomes active, later manifests carry it, older ones still verify
 *  AR-06 — the daily run of the cronjob part: rotation, verification, archiving and the checkpoint in one go
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group audit
 */

require_once __DIR__ . '/fixtures/expaudittestfixtures.php';

class expAuditArchiveTest extends PHPUnit\Framework\TestCase
{
    const DAY1 = 1790899200; // 2026-10-02T00:00:00Z
    const DAY = 86400;

    protected $dir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dir = expAuditTestFixtures::setUp( $this->name(), array( 'AuditChannel_content/LiveDays' => '2', 'AuditChannel_content/ArchiveDays' => '5' ) );
    }

    protected function tearDown(): void
    {
        expAuditTestFixtures::tearDown();
        parent::tearDown();
    }

    /** Writes $n content events on each of $days days. */
    protected function days( $days, $n = 4, $first = 0 )
    {
        for ( $d = $first; $d < $first + $days; $d++ )
        {
            for ( $i = 0; $i < $n; $i++ )
            {
                expAudit::setNow( self::DAY1 + $d * self::DAY + 3600 + $i );
                expAudit::event( 'content.node.move', array( 'object' => array( 'type' => 'node', 'id' => $d * 100 + $i, 'name' => "Node $d-$i" ) ) );
            }
            expAudit::flush();
        }
    }

    protected function archiver( $day )
    {
        return new expAuditArchiver( expAuditConfig::get(), null, self::DAY1 + $day * self::DAY + 7200 );
    }

    /** @return array file => sha256 of the live content files */
    protected function liveHashes()
    {
        $out = array();
        foreach ( expAuditWriter::channelFiles( $this->dir . 'log', 'content' ) as $f )
            $out[$f] = hash_file( 'sha256', $this->dir . 'log/' . $f );
        return $out;
    }

    /** AR-01 */
    public function testRotationByDay()
    {
        $this->days( 1 );
        $writer = expAudit::writerFor( expAuditConfig::get() );
        $writer->setClock( function () { return '2026-10-02'; } );
        $this->assertNull( $writer->rotate( 'content' ), 'today\'s file is not rotated' );
        $writer->setClock( function () { return '2026-10-03'; } );
        $this->assertSame( 'content-2026-10-02.jsonl', $writer->rotate( 'content', true )['file'], 'dry run' );
        $closed = $writer->rotate( 'content' );
        $this->assertSame( 'content-2026-10-02.jsonl', $closed['file'] );
        $this->assertSame( 6, $closed['seq'], 'open, four events, close' );
        $this->assertNull( $writer->rotate( 'content' ), 'closed once' );
        $this->days( 1, 2, 1 );
        $files = expAuditWriter::channelFiles( $this->dir . 'log', 'content' );
        $open = json_decode( file( $this->dir . 'log/' . $files[1] )[0], true );
        $this->assertSame( $closed['hash'], $open['after']['previous_hash'] );
        $r = expAuditTestFixtures::verifier( $this->dir )->verifyChannel( 'content' );
        $this->assertSame( 'intact', $r['result'] );
        $this->assertSame( array(), $r['notices'] );
    }

    /** AR-02 */
    public function testEachFormat()
    {
        $base = $this->dir;
        foreach ( array( 'gzip', 'bzip2', 'xz', 'zstd', 'zip' ) as $format )
        {
            $this->dir = expAuditTestFixtures::setUp( $this->name() . '-' . $format,
                                                      array( 'AuditChannel_content/LiveDays' => '2', 'AuditChannel_content/ArchiveFormat' => $format ) );
            $this->days( 5 );
            $before = $this->liveHashes();
            $handler = expAuditFormatRegistry::get( $format );
            $expected = $handler && $handler->problem() === '' ? $format : 'gzip';
            $archiver = $this->archiver( 5 );
            $this->assertSame( array( '2026-10-02', '2026-10-03', '2026-10-04' ), array_keys( $archiver->dueFiles( 'content' ) ),
                               'older than LiveDays (2) before 2026-10-07' );
            $r = $archiver->archive( 'content' );
            $this->assertCount( 3, $r, "$format: three days archived" );
            foreach ( $r as $date => $day )
            {
                $this->assertArrayNotHasKey( 'error', $day, "$format $date" );
                $this->assertSame( $expected, $day['handler'] );
                $this->assertSame( 'intact', $day['verification'] );
                $this->assertSame( array( "content-$date.jsonl" ), $day['removed'], 'the live file is removed after the read-back' );
                $m = json_decode( file_get_contents( $day['manifest'] ), true );
                $this->assertSame( 'exponential-audit-manifest', $m['format'] );
                $this->assertSame( expAuditTestFixtures::KEY_ID, $m['key_id'] );
                $this->assertStringStartsWith( 'hmac-sha256:', $m['hmac'] );
                $this->assertSame( $before["content-$date.jsonl"], $m['files'][0]['sha256'] );
                $this->assertSame( 0440, fileperms( dirname( $day['manifest'] ) . '/' . $m['files'][0]['archive'] ) & 0777 );
            }
            $this->assertSame( array( 'content-2026-10-05.jsonl', 'content-2026-10-06.jsonl' ), expAuditWriter::channelFiles( $this->dir . 'log', 'content' ) );
            $v = $archiver->verifyArchives( 'content' );
            $this->assertSame( 'intact', $v['result'], "$format: " . json_encode( $v['breaks'] ) );
            $this->assertSame( 3, $v['manifests'] );
            $this->assertSame( 3 * 6 - 0, $v['records'], 'open, 4 events and the day close, per day' );
            $live = $archiver->verifier()->verifyChannel( 'content' );
            $this->assertSame( 'intact', $live['result'], "$format: the live chain starts from the archived file: " . json_encode( $live['breaks'] ) );
            $blind = new expAuditVerifier( $this->dir . 'log', new expAuditKeys( expAuditConfig::get() ) );
            $blind->setOrigins( function () { return array(); } );
            $this->assertSame( 'no_origin', $blind->verifyChannel( 'content' )['breaks'][0]['kind'] ?? 'none',
                               'without the archive the origin is unknown' );
            // restore round trip
            $res = $archiver->restore( 'content', '2026-10-03' );
            $this->assertNull( $res['error'] );
            $path = $this->dir . 'log/restored/content-2026-10-03.jsonl';
            $this->assertSame( array( $path => true ), $res['files'] );
            $this->assertSame( $before['content-2026-10-03.jsonl'], hash_file( 'sha256', $path ), "$format: byte-identical" );
            $this->assertSame( array( 'content-2026-10-05.jsonl', 'content-2026-10-06.jsonl' ), expAuditWriter::channelFiles( $this->dir . 'log', 'content' ),
                               'restored files are not live files' );
            $this->assertSame( 'intact', $archiver->verifyArchives( 'content' )['result'] );
        }
        $this->dir = $base;
    }

    /** AR-03 */
    public function testArchiveTamperCases()
    {
        $this->days( 6 );
        $archiver = $this->archiver( 6 );
        $archiver->archive( 'content' );
        $manifests = $archiver->manifests( 'content' );
        $this->assertCount( 4, $manifests );
        $copy = function ( $case ) {
            $to = $this->dir . 'case-' . $case . '/';
            $src = $this->dir . 'log';
            $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $src, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::SELF_FIRST );
            mkdir( $to . 'log', 0750, true );
            foreach ( $it as $f )
            {
                $target = $to . 'log/' . substr( $f->getPathname(), strlen( $src ) + 1 );
                if ( $f->isDir() )
                    @mkdir( $target, 0750, true );
                else
                {
                    copy( $f->getPathname(), $target );
                    chmod( $target, 0640 );
                }
            }
            return $to;
        };
        $verify = function ( $to ) {
            expAuditTestFixtures::configure( $to, array( 'keyDir' => $this->dir . 'keys' ) ); // the same keys
            $r = ( new expAuditArchiver( expAuditConfig::get(), null, self::DAY1 + 5 * self::DAY ) )->verifyArchives( 'content' );
            expAuditTestFixtures::configure( $this->dir, array() );
            return $r;
        };
        $rel = function ( $path ) { return substr( $path, strlen( $this->dir . 'log/' ) ); };

        // T0
        $t0 = $verify( $copy( 't0' ) );
        $this->assertSame( 'intact', $t0['result'], json_encode( $t0['breaks'] ) );
        // T9
        $to = $copy( 't9' );
        $m = json_decode( file_get_contents( $manifests['2026-10-03'] ), true );
        $archive = $to . 'log/' . dirname( $rel( $manifests['2026-10-03'] ) ) . '/' . $m['files'][0]['archive'];
        $bytes = file_get_contents( $archive );
        $bytes[intdiv( strlen( $bytes ), 2 )] = chr( ord( $bytes[intdiv( strlen( $bytes ), 2 )] ) ^ 1 );
        file_put_contents( $archive, $bytes );
        $r = $verify( $to );
        $this->assertSame( 'broken', $r['result'] );
        $this->assertSame( 'archive_sha256', $r['breaks'][0]['kind'] );
        // T10
        $to = $copy( 't10' );
        $mp = $to . 'log/' . $rel( $manifests['2026-10-04'] );
        $m = json_decode( file_get_contents( $mp ), true );
        $m['files'][0]['records'] = 5;
        file_put_contents( $mp, expAuditJson::encode( $m ) . "\n" );
        $kinds = array_column( $verify( $to )['breaks'], 'kind' );
        $this->assertSame( 'hmac_invalid', $kinds[0] );
        $this->assertContains( 'content', $kinds, 'the count no longer matches the archive either' );
        // T11
        $to = $copy( 't11' );
        $mp = $to . 'log/' . $rel( $manifests['2026-10-05'] );
        $m = expAuditJson::decode( trim( file_get_contents( $mp ) ) );
        unset( $m->hmac );
        $m->key_id = 'k9-20261002-deadbeef';
        $m->hmac = 'hmac-sha256:' . hash_hmac( 'sha256', expAuditJson::encode( $m ), random_bytes( 32 ) );
        file_put_contents( $mp, expAuditJson::encode( $m ) . "\n" );
        $r = $verify( $to );
        $this->assertSame( array( 'unknown_key' ), array_values( array_unique( array_column( $r['breaks'], 'kind' ) ) ) );
        // T12
        $to = $copy( 't12' );
        $mp = $to . 'log/' . $rel( $manifests['2026-10-03'] );
        $m = json_decode( file_get_contents( $mp ), true );
        mkdir( $to . 'moved-away' );
        rename( $mp, $to . 'moved-away/' . basename( $mp ) );
        rename( dirname( $mp ) . '/' . $m['files'][0]['archive'], $to . 'moved-away/' . $m['files'][0]['archive'] );
        $r = $verify( $to );
        $this->assertSame( 'previous_manifest', $r['breaks'][0]['kind'] );
        $this->assertSame( 'content-2026-10-04.manifest.json', $r['breaks'][0]['manifest'] );
    }

    /** AR-04 */
    public function testRetention()
    {
        $this->days( 9 );
        $archiver = $this->archiver( 8 );
        $archiver->archive( 'content' );
        $this->assertCount( 6, $archiver->manifests( 'content' ), '2026-10-02 to 07 archived' );
        $later = $this->archiver( 13 ); // 2026-10-15: ArchiveDays 5 => before 2026-10-10
        $dry = $later->purge( 'content', true );
        $this->assertSame( array( 'content-2026-10-02.manifest.json', 'content-2026-10-03.manifest.json', 'content-2026-10-04.manifest.json',
                                  'content-2026-10-05.manifest.json', 'content-2026-10-06.manifest.json', 'content-2026-10-07.manifest.json' ), $dry['purged'] );
        $this->assertCount( 6, $later->manifests( 'content' ), 'a dry run removes nothing' );
        $mid = $this->archiver( 10 ); // 2026-10-12 => before 2026-10-07
        $r = $mid->purge( 'content' );
        $this->assertCount( 5, $r['purged'] );
        $this->assertCount( 1, $mid->manifests( 'content' ) );
        $this->assertSame( array( expAuditTestFixtures::KEY_ID ), $r['keys_needed'], 'the key of the kept archive' );
        $ledger = $mid->purgedLedger( 'content' );
        $this->assertCount( 5, $ledger );
        $this->assertSame( 64, strlen( $ledger[0]['files'][0]['sha256'] ) );
        $v = $mid->verifyArchives( 'content' );
        $this->assertSame( 'intact', $v['result'], 'the first kept manifest links into the ledger: ' . json_encode( $v['breaks'] ) );
        $this->assertSame( 'intact', $mid->verifier()->verifyChannel( 'content' )['result'] );
        $sysFiles = expAuditWriter::channelFiles( $this->dir . 'log', 'system' );
        $purge = array_values( array_filter( array_map( 'json_decode', file( $this->dir . 'log/' . end( $sysFiles ), FILE_IGNORE_NEW_LINES ) ),
                                             function ( $r ) { return $r->name === 'system.audit.purge'; } ) );
        $this->assertCount( 1, $purge );
        $this->assertCount( 5, $purge[0]->after->archives );
    }

    /** AR-05 */
    public function testKeyRotation()
    {
        $this->days( 5 );
        $this->archiver( 3 )->archive( 'content' );
        $keys = new expAuditKeys( expAuditConfig::get() );
        $r = $keys->rotate();
        $this->assertArrayNotHasKey( 'error', $r );
        $this->assertSame( expAuditTestFixtures::KEY_ID, $r['old'] );
        $this->assertStringStartsWith( 'k2-', $r['new'] );
        $this->assertFileExists( $this->dir . 'keys/audit.ini.append.php' );
        $this->assertStringContainsString( 'ActiveSigningKey=' . $r['new'], file_get_contents( $this->dir . 'keys/audit.ini.append.php' ) );
        $this->assertCount( 2, $keys->listKeys() );
        $this->days( 2, 4, 5 );
        $archiver = $this->archiver( 6 );
        $archiver->archive( 'content' );
        $m = $archiver->manifests( 'content' );
        $this->assertSame( expAuditTestFixtures::KEY_ID, json_decode( file_get_contents( $m['2026-10-02'] ), true )['key_id'] );
        $this->assertSame( $r['new'], json_decode( file_get_contents( $m['2026-10-05'] ), true )['key_id'] );
        $v = $archiver->verifyArchives( 'content' );
        $this->assertSame( 'intact', $v['result'] );
        $this->assertSame( array( expAuditTestFixtures::KEY_ID, $r['new'] ), $v['keys'] );
    }

    /** AR-06 */
    public function testDailyRun()
    {
        $this->days( 5 );
        expAudit::setNow( null );
        $m = new expAuditMaintenance( null, self::DAY1 + 6 * self::DAY + 3600 );
        $r = $m->run( array( 'daily' => true ) );
        $this->assertFalse( $r['locked'] );
        $this->assertSame( 'content-2026-10-06.jsonl', $r['daily']['rotated']['content']['file'], 'the idle channel\'s last day closed' );
        $this->assertSame( 'intact', $r['daily']['verified']['content'] );
        $this->assertSame( array( '2026-10-02', '2026-10-03', '2026-10-04', '2026-10-05' ), array_keys( $r['daily']['archived']['content'] ), 'older than LiveDays (2) on 2026-10-08' );
        $this->assertFalse( $m->dailyDue(), 'once a day' );
    }
}
