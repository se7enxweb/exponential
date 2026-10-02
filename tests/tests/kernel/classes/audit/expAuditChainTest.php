<?php
/**
 * The hash chain, its files and its verification (doc/bc/6.0/audit.md, "The hash chain", "Verification",
 * acceptance tests B2 and the file cases of the tamper test, T0-T8).
 *
 *  CH-01 — genesis: the first record of a channel links to sha256("exponential-audit:<installation>:<channel>");
 *          another installation's genesis is no_origin
 *  CH-02 — every file starts with system.audit.file.open naming the previous file, its last seq and hash; seq
 *          restarts at 1 in each file; the chain runs through the day files
 *  CH-03 — rotation by size: a file past MaxFileSize gets system.audit.file.close (after.records) and the next
 *          part .2, .3 ... is opened in the same append; the chain verifies intact
 *  CH-04 — appends of several processes at once (pcntl_fork) never interleave: every line a record, one chain
 *  CH-05 — the tamper test on a copy of a channel of 1 000 records over three day files:
 *          T0 no change: intact, 1 000 events, the time span right
 *          T1 one byte of one record's object.name: altered at that line
 *          T2 one line removed: link and gap at the next line
 *          T3 two adjacent lines swapped: reordered
 *          T4 a forged line with a correct hash inserted: link at the line after it
 *          T5 the last 100 records changed and the chain recomputed without the key: rewritten at the checkpoint
 *          T6 the middle day file removed: missing_file
 *          T7 the last 10 lines of a closed day cut off: truncated
 *          T8 a writer killed in the middle of a line: the next append repairs it (system.audit.chain.repair);
 *             verification says repaired, the torn bytes stay in the file
 *  CH-06 — an edited line that is still valid JSON but not canonical (a space added) is noncanonical
 *  CH-07 — a checkpoint whose HMAC is wrong is checkpoint_invalid; one signed with an unknown key id is unknown_key
 *  CH-08 — verify --date checks one day and still checks its link to the day before
 *
 * Writes only into var/tmp/audit-tests/; no database, no live settings.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group audit
 */

require_once __DIR__ . '/fixtures/expaudittestfixtures.php';

class expAuditChainTest extends PHPUnit\Framework\TestCase
{
    const DAY1 = 1790899200; // 2026-10-02T00:00:00Z
    const DAY = 86400;

    protected $dir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dir = expAuditTestFixtures::setUp( $this->name() );
    }

    protected function tearDown(): void
    {
        expAuditTestFixtures::tearDown();
        parent::tearDown();
    }

    /**
     * Writes $count content events at $start, one millisecond apart, and flushes.
     */
    protected function writeEvents( $count, $start, $offset = 0 )
    {
        for ( $i = 0; $i < $count; $i++ )
        {
            expAudit::setNow( $start + ( $i + 1 ) / 1000 );
            expAudit::event( 'content.node.move', array( 'object' => array( 'type' => 'node', 'id' => $offset + $i, 'name' => 'Node ' . ( $offset + $i ) ),
                                                         'target' => array( 'type' => 'node', 'id' => 2 ) ) );
        }
        expAudit::flush();
    }

    /** @return string A copy of the log directory to tamper with */
    protected function copyLog( $case )
    {
        $copy = $this->dir . 'case-' . $case . '/';
        mkdir( $copy . 'log', 0750, true );
        foreach ( glob( $this->dir . 'log/*.jsonl' ) as $f )
            copy( $f, $copy . 'log/' . basename( $f ) );
        return $copy;
    }

    /** @return array The first break of a verification, with all kinds of that line */
    protected function verifyCopy( $copy, $channel = 'content' )
    {
        return expAuditTestFixtures::verifier( $copy )->verifyChannel( $channel );
    }

    protected function kindsAt( array $result, $file, $line )
    {
        $kinds = array();
        foreach ( $result['breaks'] as $b )
            if ( $b['file'] === $file && $b['line'] === $line )
                $kinds[] = $b['kind'];
        return $kinds;
    }

    /** CH-01 */
    public function testGenesis()
    {
        $this->writeEvents( 3, self::DAY1 );
        $records = expAuditTestFixtures::records( $this->dir, 'content' );
        $this->assertSame( 'system.audit.file.open', $records[0]['name'] );
        $this->assertSame( 'sha256:' . hash( 'sha256', 'exponential-audit:' . expAuditTestFixtures::INSTALLATION . ':content' ), $records[0]['prev'] );
        $this->assertArrayNotHasKey( 'after', $records[0], 'the first file names no previous file' );
        $this->assertSame( 'intact', expAuditTestFixtures::verifier( $this->dir )->verifyChannel( 'content' )['result'] );

        $other = expAuditTestFixtures::keys();
        $other['installation'] = '00000000-0000-4000-8000-000000000000';
        expAuditKeys::setKnown( $other, $this->dir . 'keys' );
        $r = expAuditTestFixtures::verifier( $this->dir )->verifyChannel( 'content' );
        $this->assertSame( 'broken', $r['result'] );
        $this->assertSame( 'no_origin', $r['breaks'][0]['kind'] );
    }

    /** CH-02 */
    public function testDayFilesAreLinked()
    {
        $this->writeEvents( 5, self::DAY1 );
        $this->writeEvents( 5, self::DAY1 + self::DAY, 5 );
        $files = expAuditWriter::channelFiles( $this->dir . 'log', 'content' );
        $this->assertSame( array( 'content-2026-10-02.jsonl', 'content-2026-10-03.jsonl' ), $files );
        $day1 = file( $this->dir . 'log/' . $files[0], FILE_IGNORE_NEW_LINES );
        $day2 = file( $this->dir . 'log/' . $files[1], FILE_IGNORE_NEW_LINES );
        $last = json_decode( end( $day1 ), true );
        $open = json_decode( $day2[0], true );
        $this->assertSame( 'system.audit.file.open', $open['name'] );
        $this->assertSame( 1, $open['seq'] );
        $this->assertSame( $last['hash'], $open['prev'] );
        $this->assertSame( array( 'previous_file' => $files[0], 'previous_hash' => $last['hash'], 'previous_seq' => $last['seq'] ),
                           array( 'previous_file' => $open['after']['previous_file'], 'previous_hash' => $open['after']['previous_hash'],
                                  'previous_seq' => $open['after']['previous_seq'] ) );
        // rotation by day: the first write of the next day closed the day before
        $this->assertSame( 'system.audit.file.close', $last['name'] );
        $this->assertSame( 7, $last['after']['records'] );
        $r = expAuditTestFixtures::verifier( $this->dir )->verifyChannel( 'content' );
        $this->assertSame( 'intact', $r['result'] );
        $this->assertSame( 13, $r['records'], 'two opens, ten events and the close of the first day' );
        $this->assertSame( array(), $r['notices'] );
    }

    /** CH-03 */
    public function testRotationBySize()
    {
        expAuditTestFixtures::configure( $this->dir, array( 'AuditRotationSettings/MaxFileSize' => '4K', 'AuditBufferSettings/MaxEvents' => '3' ) );
        $this->writeEvents( 30, self::DAY1 );
        $files = expAuditWriter::channelFiles( $this->dir . 'log', 'content' );
        $this->assertGreaterThanOrEqual( 3, count( $files ) );
        $this->assertSame( 'content-2026-10-02.2.jsonl', $files[1] );
        $first = file( $this->dir . 'log/' . $files[0], FILE_IGNORE_NEW_LINES );
        $close = json_decode( end( $first ), true );
        $this->assertSame( 'system.audit.file.close', $close['name'] );
        $this->assertSame( $close['seq'], $close['after']['records'] );
        $second = file( $this->dir . 'log/' . $files[1], FILE_IGNORE_NEW_LINES );
        $open = json_decode( $second[0], true );
        $this->assertSame( $close['hash'], $open['prev'] );
        $r = expAuditTestFixtures::verifier( $this->dir )->verifyChannel( 'content' );
        $this->assertSame( 'intact', $r['result'], json_encode( $r['breaks'] ) );
        $this->assertSame( array(), $r['notices'] );
        $events = expAuditTestFixtures::events( $this->dir, 'content' );
        $this->assertCount( 30, $events );
    }

    /** CH-04 */
    public function testConcurrentAppends()
    {
        if ( !function_exists( 'pcntl_fork' ) || !function_exists( 'posix_kill' ) )
            $this->markTestSkipped( 'pcntl is not available' );
        $children = array();
        for ( $p = 0; $p < 4; $p++ )
        {
            $pid = pcntl_fork();
            if ( $pid === 0 )
            {
                $_SERVER['REQUEST_TIME_FLOAT'] = microtime( true ) + $p;
                for ( $i = 0; $i < 150; $i++ )
                    expAudit::event( 'access.session.login', array( 'object' => array( 'type' => 'user', 'id' => $p * 1000 + $i ) ) );
                // no PHPUnit shutdown in a child
                posix_kill( getmypid(), SIGKILL );
            }
            $children[] = $pid;
        }
        foreach ( $children as $pid )
            pcntl_waitpid( $pid, $status );
        $r = expAuditTestFixtures::verifier( $this->dir )->verifyChannel( 'access' );
        $this->assertSame( 'intact', $r['result'], json_encode( array_slice( $r['breaks'], 0, 3 ) ) );
        $events = expAuditTestFixtures::events( $this->dir, 'access' );
        $this->assertCount( 600, $events );
        $this->assertCount( 600, array_unique( array_column( array_column( $events, 'object' ), 'id' ) ) );
    }

    /**
     * 1 000 content events over three day files, and a checkpoint at the end.
     *
     * @return string[] the day files
     */
    protected function thousand()
    {
        $this->writeEvents( 333, self::DAY1 );
        $this->writeEvents( 333, self::DAY1 + self::DAY, 333 );
        $this->writeEvents( 334, self::DAY1 + 2 * self::DAY, 666 );
        expAudit::setNow( self::DAY1 + 2 * self::DAY + 3600 );
        $this->assertNotNull( expAudit::checkpoint() );
        return expAuditWriter::channelFiles( $this->dir . 'log', 'content' );
    }

    /** CH-05 */
    public function testTamperCases()
    {
        $files = $this->thousand();
        $this->assertCount( 3, $files );

        // T0
        $r = $this->verifyCopy( $this->copyLog( 't0' ) );
        $this->assertSame( 'intact', $r['result'] );
        $this->assertSame( 1005, $r['records'], '1 000 events, three file open records and the closes of the first two days' );
        // the span runs from the first file's open record (written at the first flush) to the last event
        $this->assertStringStartsWith( '2026-10-02T00:00:00.', $r['first_time'] );
        $this->assertStringStartsWith( '2026-10-04T', $r['last_time'] );
        $this->assertGreaterThanOrEqual( 1, $r['checkpoints'], 'the daily ones and the one written at the end' );
        $this->assertSame( 'intact', $this->verifyCopy( $this->copyLog( 't0s' ), 'system' )['result'] );

        $edit = function ( $copy, $file, callable $change ) {
            $lines = file( $copy . 'log/' . $file, FILE_IGNORE_NEW_LINES );
            $lines = $change( $lines );
            file_put_contents( $copy . 'log/' . $file, implode( "\n", $lines ) . "\n" );
        };

        // T1: one byte of one record's object.name
        $copy = $this->copyLog( 't1' );
        $edit( $copy, $files[1], function ( $l ) { $l[99] = str_replace( '"name":"Node ', '"name":"Mode ', $l[99] ); return $l; } );
        $r = $this->verifyCopy( $copy );
        $this->assertSame( 'broken', $r['result'] );
        $this->assertSame( array( 'altered' ), $this->kindsAt( $r, $files[1], 100 ) );
        $this->assertCount( 1, $r['breaks'], 'only that line' );

        // T2: one line removed
        $copy = $this->copyLog( 't2' );
        $edit( $copy, $files[1], function ( $l ) { array_splice( $l, 49, 1 ); return $l; } );
        $r = $this->verifyCopy( $copy );
        $kinds = $this->kindsAt( $r, $files[1], 50 );
        $this->assertContains( 'link', $kinds );
        $this->assertContains( 'gap', $kinds );

        // T3: two adjacent lines swapped
        $copy = $this->copyLog( 't3' );
        $edit( $copy, $files[1], function ( $l ) { $t = $l[60]; $l[60] = $l[61]; $l[61] = $t; return $l; } );
        $r = $this->verifyCopy( $copy );
        $this->assertContains( 'reordered', array_column( $r['breaks'], 'kind' ) );
        $this->assertSame( 61, $r['breaks'][0]['line'] );

        // T4: a forged line with a correctly computed hash
        $copy = $this->copyLog( 't4' );
        $edit( $copy, $files[1], function ( $l ) {
            $before = json_decode( $l[69], true );
            $forged = $before;
            $forged['id'] = expAudit::ulid();
            $forged['object']['name'] = 'Forged';
            $forged['seq'] = $before['seq'] + 1;
            $forged['prev'] = $before['hash'];
            $forged['hash'] = expAuditWriter::hashOf( $forged );
            array_splice( $l, 70, 0, array( expAuditJson::encode( $forged ) ) );
            return $l;
        } );
        $r = $this->verifyCopy( $copy );
        $this->assertSame( array(), $this->kindsAt( $r, $files[1], 71 ), 'the forged line itself is consistent' );
        $this->assertContains( 'link', $this->kindsAt( $r, $files[1], 72 ) );

        // T5: the last 100 records changed and the chain recomputed, without the key
        $copy = $this->copyLog( 't5' );
        copy( $this->dir . 'log/' . expAuditWriter::channelFiles( $this->dir . 'log', 'system' )[0], $copy . 'log/' . expAuditWriter::channelFiles( $this->dir . 'log', 'system' )[0] );
        $writer = expAudit::writerFor( expAuditConfig::get() );
        $edit( $copy, $files[2], function ( $l ) use ( $writer ) {
            $n = count( $l );
            $prev = json_decode( $l[$n - 101], true );
            for ( $i = $n - 100; $i < $n; $i++ )
            {
                $r = json_decode( $l[$i], true );
                $r['object']['name'] = 'Rewritten';
                list( $line, $rec ) = $writer->chain( $r, $prev['seq'], $prev['hash'] );
                $l[$i] = rtrim( $line, "\n" );
                $prev = $rec;
            }
            return $l;
        } );
        $r = $this->verifyCopy( $copy );
        $this->assertSame( array( 'rewritten' ), array_values( array_unique( array_column( $r['breaks'], 'kind' ) ) ) );
        $this->assertSame( 335, $r['breaks'][0]['line'], 'the checkpoint named the last record of that file' );

        // T6: the middle day file removed (moved out of the directory)
        $copy = $this->copyLog( 't6' );
        mkdir( $copy . 'moved-away' );
        rename( $copy . 'log/' . $files[1], $copy . 'moved-away/' . $files[1] );
        $r = $this->verifyCopy( $copy );
        $this->assertSame( 'missing_file', $r['breaks'][0]['kind'] );
        $this->assertSame( $files[2], $r['breaks'][0]['file'] );

        // T7: the last 10 lines of a closed day cut off
        $copy = $this->copyLog( 't7' );
        $edit( $copy, $files[0], function ( $l ) { return array_slice( $l, 0, -10 ); } );
        $r = $this->verifyCopy( $copy );
        $this->assertSame( 'truncated', $r['breaks'][0]['kind'] );
        $this->assertSame( $files[1], $r['breaks'][0]['file'] );
        $this->assertSame( 1, $r['breaks'][0]['line'] );
    }

    /** CH-05 T8 */
    public function testTornLineIsRepaired()
    {
        $this->writeEvents( 5, self::DAY1 );
        $file = $this->dir . 'log/content-2026-10-02.jsonl';
        $size = filesize( $file );
        // a writer killed in the middle of its fwrite(): half a record, no newline
        $half = substr( expAuditJson::encode( array( 'v' => 1, 'name' => 'content.node.move', 'seq' => 7, 'hash' => 'sha256:x' ) ), 0, 30 );
        file_put_contents( $file, $half, FILE_APPEND );
        $r = expAuditTestFixtures::verifier( $this->dir )->verifyChannel( 'content' );
        $this->assertSame( 'intact', $r['result'] );
        $this->assertSame( 'torn_tail', $r['notices'][0]['kind'] );

        $this->writeEvents( 2, self::DAY1 + 10, 100 );
        $content = file_get_contents( $file );
        $this->assertStringContainsString( $half . "\n", $content, 'the torn bytes stay' );
        $records = expAuditTestFixtures::records( $this->dir, 'content' );
        $repair = null;
        foreach ( $records as $rec )
            if ( is_array( $rec ) && $rec['name'] === 'system.audit.chain.repair' )
                $repair = $rec;
        $this->assertNotNull( $repair );
        $this->assertSame( $size, $repair['after']['offset'] );
        $this->assertSame( strlen( $half ), $repair['after']['length'] );
        $r = expAuditTestFixtures::verifier( $this->dir )->verifyChannel( 'content' );
        $this->assertSame( 'repaired', $r['result'], json_encode( $r['breaks'] ) );
        $this->assertCount( 1, $r['repairs'] );
        $this->assertSame( 9, $r['records'], 'open, 5, repair, 2' );
    }

    /** CH-06 */
    public function testNoncanonical()
    {
        $this->writeEvents( 3, self::DAY1 );
        $file = $this->dir . 'log/content-2026-10-02.jsonl';
        $lines = file( $file, FILE_IGNORE_NEW_LINES );
        $lines[2] = str_replace( '","name":', '", "name":', $lines[2] );
        file_put_contents( $file, implode( "\n", $lines ) . "\n" );
        $r = expAuditTestFixtures::verifier( $this->dir )->verifyChannel( 'content' );
        $this->assertSame( array( 'noncanonical' ), $this->kindsAt( $r, 'content-2026-10-02.jsonl', 3 ) );
    }

    /** CH-07 */
    public function testCheckpointSignature()
    {
        $this->writeEvents( 3, self::DAY1 );
        expAudit::setNow( self::DAY1 + 60 );
        $id = expAudit::checkpoint();
        $system = $this->dir . 'log/system-2026-10-02.jsonl';
        $this->assertSame( 'intact', expAuditTestFixtures::verifier( $this->dir )->verifyChannel( 'system' )['result'] );
        $lines = file( $system, FILE_IGNORE_NEW_LINES );
        $cp = null;
        foreach ( $lines as $i => $l )
            if ( strpos( $l, $id ) !== false )
                $cp = $i;
        $this->assertNotNull( $cp );
        $rec = json_decode( $lines[$cp], true );
        $this->assertSame( expAuditTestFixtures::KEY_ID, $rec['after']['key_id'] );
        $this->assertStringStartsWith( 'hmac-sha256:', $rec['after']['hmac'] );

        // someone with write access but without the key: a forged HMAC, the chain recomputed from there
        $writer = expAudit::writerFor( expAuditConfig::get() );
        $forge = function ( array $after ) use ( $lines, $cp, $writer, $system ) {
            $copy = $lines;
            $r = json_decode( $copy[$cp], true );
            $r['after'] = $after;
            $prev = json_decode( $copy[$cp - 1], true );
            for ( $i = $cp; $i < count( $copy ); $i++ )
            {
                $x = $i === $cp ? $r : json_decode( $copy[$i], true );
                list( $line, $rec ) = $writer->chain( $x, $prev['seq'], $prev['hash'] );
                $copy[$i] = rtrim( $line, "\n" );
                $prev = $rec;
            }
            file_put_contents( $system, implode( "\n", $copy ) . "\n" );
        };
        $after = $rec['after'];
        $after['hmac'] = 'hmac-sha256:' . str_repeat( 'a', 64 );
        $forge( $after );
        $this->assertSame( array( 'checkpoint_invalid' ), array_column( expAuditTestFixtures::verifier( $this->dir )->verifyChannel( 'system' )['breaks'], 'kind' ) );
        $after = $rec['after'];
        $after['key_id'] = 'k9-20990101-deadbeef';
        $forge( $after );
        $this->assertSame( array( 'unknown_key' ), array_column( expAuditTestFixtures::verifier( $this->dir )->verifyChannel( 'system' )['breaks'], 'kind' ) );
    }

    /** CH-08 */
    public function testVerifyOneDate()
    {
        $this->writeEvents( 4, self::DAY1 );
        $this->writeEvents( 4, self::DAY1 + self::DAY, 4 );
        $v = expAuditTestFixtures::verifier( $this->dir );
        $r = $v->verifyChannel( 'content', array( 'date' => '2026-10-03' ) );
        $this->assertSame( 'intact', $r['result'] );
        $this->assertSame( 1, $r['files'] );
        $this->assertSame( 5, $r['records'] );
        // the link to the day before is still checked
        $file = $this->dir . 'log/content-2026-10-02.jsonl';
        $lines = file( $file, FILE_IGNORE_NEW_LINES );
        array_pop( $lines );
        file_put_contents( $file, implode( "\n", $lines ) . "\n" );
        $r = $v->verifyChannel( 'content', array( 'date' => '2026-10-03' ) );
        $this->assertSame( 'truncated', $r['breaks'][0]['kind'] );
    }
}
