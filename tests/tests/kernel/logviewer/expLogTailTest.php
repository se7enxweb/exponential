<?php
/**
 * Following a log file by byte offset (doc/eng-us/features/6.0/log-viewer.md, "Following a file"), on synthetic files in a
 * temporary directory.
 *
 *  LV-10  the first look: the last InitialBytes, from the first whole line, up to the end
 *  LV-11  a file that did not grow is answered by one stat(): no fopen()
 *  LV-12  a line still being written is held back, and delivered once it ends
 *  LV-13  a file that grew more than a poll is caught up with more = true, in whole lines, nothing lost
 *  LV-14  a line longer than a poll is cut at the poll's size rather than never delivered
 *  LV-15  rotation (another inode) and truncation (smaller than the offset) start again at 0 with reset = true
 *  LV-16  the chunk cache: a second reader is answered without opening the file; no hit after truncation, for
 *         another inode, or with ChunkCacheTTL = 0
 *  LV-17  wholeLines(); the end of a compressed rotation; a missing file
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expLogViewerTestFixtures.php';

class expLogTailTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    protected $root;

    /** @var string */
    protected $file;

    protected function setUp(): void
    {
        $this->root = expLogViewerTestFixtures::root( 'lvtail' );
        $this->file = $this->root . '/var/log/error.log';
    }

    protected function tearDown(): void
    {
        expLogViewerTestFixtures::remove( $this->root );
        clearstatcache();
    }

    /** 100-byte lines */
    protected function lines( $count, $from = 0 )
    {
        $out = '';
        for ( $i = $from; $i < $from + $count; $i++ )
            $out .= str_pad( 'line ' . $i . ' ', 99, 'x' ) . "\n";
        return $out;
    }

    protected function tail( array $values = array(), $cache = false )
    {
        $values += array( 'TailSettings' => array() );
        $values['TailSettings'] += array( 'InitialBytes' => 1024 );
        return new expLogTail( expLogViewerTestFixtures::config( $this->root, $values ), $cache ? $this->root . '/cache' : false );
    }

    /** LV-10 */
    public function testInitialTail()
    {
        file_put_contents( $this->file, $this->lines( 100 ) );
        $r = $this->tail()->read( $this->file, -1 );
        $this->assertLessThanOrEqual( 1024, strlen( $r['data'] ) );
        $this->assertGreaterThan( 800, strlen( $r['data'] ) );
        $this->assertSame( 10000, $r['next'] );
        $this->assertSame( 10000, $r['size'] );
        $this->assertSame( 0, $r['offset'] % 100, 'starts at a whole line' );
        $this->assertSame( substr( file_get_contents( $this->file ), $r['offset'] ), $r['data'] );
        $this->assertFalse( $r['reset'] );

        // a file smaller than InitialBytes is shown whole
        file_put_contents( $this->file, $this->lines( 3 ) );
        $r = $this->tail()->read( $this->file, -1 );
        $this->assertSame( 0, $r['offset'] );
        $this->assertSame( 300, strlen( $r['data'] ) );
    }

    /** LV-11 */
    public function testNoGrowthNoOpen()
    {
        file_put_contents( $this->file, $this->lines( 10 ) );
        $t = $this->tail();
        $first = $t->read( $this->file, -1 );
        $r = $t->read( $this->file, $first['next'], $first['ino'] );
        $this->assertSame( '', $r['data'] );
        $this->assertSame( 0, $t->lastStats['open'] );
        $this->assertSame( 1, $t->lastStats['stat'] );
        $this->assertSame( $first['next'], $r['next'] );
        $this->assertFalse( $r['more'] );
    }

    /** LV-12 */
    public function testPartialLine()
    {
        file_put_contents( $this->file, $this->lines( 10 ) );
        $t = $this->tail();
        file_put_contents( $this->file, 'half a line', FILE_APPEND );
        $r = $t->read( $this->file, 1000 );
        $this->assertSame( '', $r['data'] );
        $this->assertSame( 1000, $r['next'] );
        file_put_contents( $this->file, " and the rest\n", FILE_APPEND );
        $r = $t->read( $this->file, 1000 );
        $this->assertSame( "half a line and the rest\n", $r['data'] );
        $this->assertSame( 1025, $r['next'] );

        // whole lines and a partial one: the whole ones now
        file_put_contents( $this->file, $this->lines( 2, 50 ) . 'tail', FILE_APPEND );
        $r = $t->read( $this->file, 1025 );
        $this->assertSame( $this->lines( 2, 50 ), $r['data'] );
        $this->assertSame( 1225, $r['next'] );
    }

    /** LV-13 */
    public function testCatchUp()
    {
        file_put_contents( $this->file, $this->lines( 100 ) );
        $t = $this->tail();
        $offset = 0;
        $read = '';
        $polls = 0;
        do
        {
            $r = $t->read( $this->file, $offset, 0, 1024 );
            $this->assertLessThanOrEqual( 1024, strlen( $r['data'] ) );
            $this->assertSame( "\n", substr( $r['data'], -1 ) );
            $read .= $r['data'];
            $offset = $r['next'];
            $polls++;
        }
        while ( $r['more'] && $polls < 50 );
        $this->assertSame( file_get_contents( $this->file ), $read );
        $this->assertGreaterThanOrEqual( 10, $polls );
        $this->assertFalse( $r['more'] );
    }

    /** LV-14 */
    public function testLineLongerThanAPoll()
    {
        file_put_contents( $this->file, str_repeat( 'y', 3000 ) . "\n" );
        $t = $this->tail();
        $r = $t->read( $this->file, 0, 0, 1024 );
        $this->assertSame( 1024, strlen( $r['data'] ) );
        $this->assertSame( 1024, $r['next'] );
        $this->assertTrue( $r['more'] );
    }

    /** LV-15 */
    public function testRotationAndTruncation()
    {
        file_put_contents( $this->file, $this->lines( 10 ) );
        $t = $this->tail();
        $first = $t->read( $this->file, -1 );

        // truncated and written again, smaller than the offset
        file_put_contents( $this->file, $this->lines( 2 ) );
        $r = $t->read( $this->file, $first['next'], $first['ino'] );
        $this->assertTrue( $r['reset'] );
        $this->assertSame( 0, $r['offset'] );
        $this->assertSame( $this->lines( 2 ), $r['data'] );

        // rotated: another inode, even when it has grown past the offset
        rename( $this->file, $this->file . '.1' );
        file_put_contents( $this->file, $this->lines( 5, 500 ) );
        clearstatcache();
        $ino = fileinode( $this->file . '.1' );
        $r = $t->read( $this->file, 100, $ino );
        $this->assertTrue( $r['reset'] );
        $this->assertSame( 0, $r['offset'] );
        $this->assertSame( $this->lines( 5, 500 ), $r['data'] );
    }

    /** LV-16 */
    public function testChunkCache()
    {
        file_put_contents( $this->file, $this->lines( 50 ) );
        $first = $this->tail( array(), true );
        $r1 = $first->read( $this->file, -1 );
        $this->assertSame( 1, $first->lastStats['open'] );

        // a second reader of the same end, and a re-read of bytes already read
        $second = $this->tail( array(), true );
        $r2 = $second->read( $this->file, -1 );
        $this->assertSame( $r1['data'], $r2['data'] );
        $this->assertSame( 0, $second->lastStats['open'] );
        $this->assertSame( 1, $second->lastStats['cache_hit'] );
        $this->assertTrue( $r2['cached'] );
        $r3 = $second->read( $this->file, $r1['next'] - 200, $r1['ino'] );
        $this->assertSame( 0, $second->lastStats['open'] );
        $this->assertSame( substr( file_get_contents( $this->file ), -200 ), $r3['data'] );

        // new bytes after the cached ones: only those are read
        file_put_contents( $this->file, $this->lines( 1, 900 ), FILE_APPEND );
        $r4 = $second->read( $this->file, $r1['next'] - 200, $r1['ino'] );
        $this->assertSame( 1, $second->lastStats['cache_hit'] );
        $this->assertSame( 100, $second->lastStats['bytes_read'] );
        $this->assertSame( substr( file_get_contents( $this->file ), -300 ), $r4['data'] );

        // truncated (same inode, smaller): no hit
        file_put_contents( $this->file, $this->lines( 30, 2000 ) );
        $third = $this->tail( array(), true );
        $r5 = $third->read( $this->file, -1 );
        $this->assertSame( 0, $third->lastStats['cache_hit'] );
        $this->assertSame( substr( file_get_contents( $this->file ), $r5['offset'] ), $r5['data'] );

        // another inode: no hit
        $copy = $this->file . '.new';
        file_put_contents( $copy, file_get_contents( $this->file ) );
        rename( $copy, $this->file );
        clearstatcache();
        $fourth = $this->tail( array(), true );
        $fourth->read( $this->file, -1 );
        $this->assertSame( 0, $fourth->lastStats['cache_hit'] );

        // the cache switched off
        $off = $this->tail( array( 'TailSettings' => array( 'ChunkCacheTTL' => 0 ) ), true );
        $off->read( $this->file, -1 );
        $off->read( $this->file, -1 );
        $this->assertSame( 0, $off->lastStats['cache_hit'] );
        $this->assertSame( 1, $off->lastStats['open'] );
    }

    /** LV-17 */
    public function testHelpersGzipAndMissing()
    {
        $this->assertSame( array( '', 5 ), expLogTail::wholeLines( '', 5, 10, false ) );
        $this->assertSame( array( "a\n", 7 ), expLogTail::wholeLines( "a\n", 5, 10, false ) );
        $this->assertSame( array( "a\n", 7 ), expLogTail::wholeLines( "a\nbc", 5, 10, false ) );
        $this->assertSame( array( '', 5 ), expLogTail::wholeLines( 'abc', 5, 10, false ) );
        $this->assertSame( array( 'abc', 8 ), expLogTail::wholeLines( 'abc', 5, 10, true ) );

        $content = $this->lines( 100 );
        file_put_contents( $this->file . '.2.gz', gzencode( $content ) );
        $g = expLogTail::lastBytesOfGzip( $this->file . '.2.gz', 1024, 1048576 );
        $this->assertTrue( $g['complete'] );
        $this->assertSame( 10000, $g['size'] );
        $this->assertLessThanOrEqual( 1024, strlen( $g['data'] ) );
        $this->assertSame( substr( $content, $g['offset'] ), $g['data'] );
        $this->assertSame( 0, $g['offset'] % 100 );
        // the limit is checked between blocks of 256 KB: a 1 MB file with a limit of 300 KB stops early
        file_put_contents( $this->file . '.3.gz', gzencode( $this->lines( 10000 ) ) );
        $limited = expLogTail::lastBytesOfGzip( $this->file . '.3.gz', 1024, 300000 );
        $this->assertLessThan( 1000000, $limited['size'] );
        $this->assertFalse( $limited['complete'] );

        $r = $this->tail()->read( $this->root . '/var/log/none.log', 0 );
        $this->assertTrue( $r['missing'] );
        $this->assertSame( '', $r['data'] );
    }
}
