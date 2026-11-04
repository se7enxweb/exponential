<?php
/**
 * expViewCountLogReader and expViewCountState resume reading by inode and byte offset: a second run reads only the
 * new lines, a line still being written waits, a rotated log (moved, copied and emptied, compressed) is read to its
 * end before the new one, a truncated log is read from its start and reported, a line that is too long is skipped,
 * two logs keep two state files, two siteaccesses keep two places in one log, the state is written whole and the
 * lock keeps a second run out.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/expViewCountTestFiles.php';

#[\PHPUnit\Framework\Attributes\Group('kernel')]
class expViewCountLogReaderTest extends PHPUnit\Framework\TestCase
{
    use expViewCountTestFiles;

    protected function tearDown(): void
    {
        $this->removeViewCountDir();
    }

    /**
     * Reads a log for sites the way the updater does and saves their entries.
     *
     * @return array hash lines (string[] read, without line ends), events, end
     */
    private function run_( $log, array $sites = array( 'site' ), array $suffixes = array( '.1', '.1.gz' ) )
    {
        $state = new expViewCountState( $this->viewCountDir() . '/state', $log );
        $reader = new expViewCountLogReader( $log, $suffixes, 2048 );
        $current = $reader->stat();
        $plans = array();
        foreach ( $sites as $site )
            $plans[$site] = $reader->plan( $state->entry( $site ), $current );
        $lines = array();
        $read = $reader->read( $plans, function ( $line, array $for ) use ( &$lines )
        {
            $lines[] = rtrim( $line, "\n" ) . ' <' . implode( ',', $for ) . '>';
        } );
        foreach ( $sites as $site )
        {
            $state->set( $site, array( 'inode' => $current['inode'], 'device' => $current['device'], 'offset' => $read['end'],
                                       'head' => $current['head'] !== '' ? $current['head'] : expViewCountLogReader::head( $log ) ) );
        }
        $this->assertTrue( $state->save() );
        return array( 'lines' => $lines, 'events' => array_map( function ( $p ) { return $p['event']; }, $plans ), 'end' => $read['end'],
                      'too_long' => $read['too_long'] );
    }

    public function testASecondRunReadsOnlyTheNewLines()
    {
        $log = $this->viewCountDir() . '/logs/access_log';
        file_put_contents( $log, "one\ntwo\n" );
        $first = $this->run_( $log );
        $this->assertSame( array( 'one <site>', 'two <site>' ), $first['lines'] );
        $this->assertSame( array( 'site' => 'new' ), $first['events'] );
        file_put_contents( $log, "three\n", FILE_APPEND );
        $second = $this->run_( $log );
        $this->assertSame( array( 'three <site>' ), $second['lines'] );
        $this->assertSame( array( 'site' => 'resumed' ), $second['events'] );
        $this->assertSame( array(), $this->run_( $log )['lines'] );
    }

    public function testALineStillBeingWrittenWaitsForTheNextRun()
    {
        $log = $this->viewCountDir() . '/logs/access_log';
        file_put_contents( $log, "one\ntw" );
        $this->assertSame( array( 'one <site>' ), $this->run_( $log )['lines'] );
        file_put_contents( $log, "o\n", FILE_APPEND );
        $this->assertSame( array( 'two <site>' ), $this->run_( $log )['lines'] );
    }

    public function testAMovedLogIsReadToItsEndBeforeTheNewOne()
    {
        $log = $this->viewCountDir() . '/logs/access_log';
        file_put_contents( $log, "one\n" );
        $this->run_( $log );
        file_put_contents( $log, "two\n", FILE_APPEND );
        rename( $log, $log . '.1' );                      // logrotate's create
        file_put_contents( $log, "three\n" );
        $run = $this->run_( $log );
        $this->assertSame( array( 'two <site>', 'three <site>' ), $run['lines'] );
        $this->assertSame( array( 'site' => 'rotated' ), $run['events'] );
        $this->assertSame( array(), $this->run_( $log )['lines'] );
    }

    public function testACopiedAndEmptiedLogIsFoundByItsFirstLine()
    {
        $log = $this->viewCountDir() . '/logs/access_log';
        file_put_contents( $log, "one\n" );
        $this->run_( $log );
        file_put_contents( $log, "two\n", FILE_APPEND );
        copy( $log, $log . '.1' );                        // copytruncate: same inode, emptied, written again
        file_put_contents( $log, "three\nfour\nfive\n" );
        $run = $this->run_( $log );
        $this->assertSame( array( 'two <site>', 'three <site>', 'four <site>', 'five <site>' ), $run['lines'] );
        $this->assertSame( array( 'site' => 'rotated' ), $run['events'] );
    }

    public function testACompressedRotatedCopyIsReadToo()
    {
        $log = $this->viewCountDir() . '/logs/access_log';
        file_put_contents( $log, "one\n" );
        $this->run_( $log );
        file_put_contents( $log, "two\n", FILE_APPEND );
        file_put_contents( $log . '.1.gz', gzencode( (string)file_get_contents( $log ) ) );
        unlink( $log );
        file_put_contents( $log, "three\n" );
        $run = $this->run_( $log );
        $this->assertSame( array( 'two <site>', 'three <site>' ), $run['lines'] );
        $this->assertSame( array( 'site' => 'rotated' ), $run['events'] );
    }

    public function testACompressedLogReadOnItsOwnIsReadOnce()
    {
        $log = $this->viewCountDir() . '/logs/access_log.2.gz';
        file_put_contents( $log, gzencode( "one\ntwo\n" ) );
        $this->assertSame( array( 'one <site>', 'two <site>' ), $this->run_( $log )['lines'] );
        $again = $this->run_( $log );
        $this->assertSame( array(), $again['lines'] );
        $this->assertSame( array( 'site' => 'resumed' ), $again['events'] );
    }

    public function testATruncatedLogWithoutACopyIsReadFromItsStart()
    {
        $log = $this->viewCountDir() . '/logs/access_log';
        file_put_contents( $log, "one\ntwo\n" );
        $this->run_( $log );
        file_put_contents( $log, "new\n" );                // same inode, shorter, nothing kept
        $run = $this->run_( $log );
        $this->assertSame( array( 'new <site>' ), $run['lines'] );
        $this->assertSame( array( 'site' => 'truncated' ), $run['events'] );
    }

    public function testALogReplacedByOneOfTheSameLengthIsNotTakenForTheOldOne()
    {
        $log = $this->viewCountDir() . '/logs/access_log';
        file_put_contents( $log, "aaa\nbbb\n" );
        $this->run_( $log );
        file_put_contents( $log, "ccc\nddd\neee\n" );       // same inode, emptied and written past the offset
        $run = $this->run_( $log );
        $this->assertSame( array( 'ccc <site>', 'ddd <site>', 'eee <site>' ), $run['lines'] );
        $this->assertSame( array( 'site' => 'truncated' ), $run['events'] );
    }

    public function testALineTooLongIsSkippedWhole()
    {
        $log = $this->viewCountDir() . '/logs/access_log';
        file_put_contents( $log, "one\n" . str_repeat( 'x', 5000 ) . "\ntwo\n" );
        $run = $this->run_( $log );
        $this->assertSame( array( 'one <site>', 'two <site>' ), $run['lines'] );
        $this->assertSame( 1, $run['too_long'] );
        $this->assertSame( filesize( $log ), $run['end'] );
    }

    public function testTwoLogsKeepTwoStateFiles()
    {
        $a = $this->viewCountDir() . '/logs/a/access_log';
        $b = $this->viewCountDir() . '/logs/b/access_log';
        mkdir( dirname( $a ) );
        mkdir( dirname( $b ) );
        file_put_contents( $a, "a1\n" );
        file_put_contents( $b, "b1\nb2\n" );
        $this->run_( $a, array( 'site' ) );
        $this->run_( $b, array( 'site' ) );
        $stateA = new expViewCountState( $this->viewCountDir() . '/state', $a );
        $stateB = new expViewCountState( $this->viewCountDir() . '/state', $b );
        $this->assertNotSame( $stateA->file(), $stateB->file() );
        $this->assertSame( 3, $stateA->entry( 'site' )['offset'] );
        $this->assertSame( 6, $stateB->entry( 'site' )['offset'] );
        file_put_contents( $a, "a2\n", FILE_APPEND );
        $this->assertSame( array( 'a2 <site>' ), $this->run_( $a )['lines'] );
        $this->assertSame( array(), $this->run_( $b )['lines'] );
    }

    public function testTwoSiteaccessesKeepTwoPlacesInOneLog()
    {
        $log = $this->viewCountDir() . '/logs/access_log';
        file_put_contents( $log, "one\ntwo\n" );
        $this->run_( $log, array( 'site' ) );
        file_put_contents( $log, "three\n", FILE_APPEND );
        // bold was not counted before: it reads from the start, site only the new line; each line once
        $run = $this->run_( $log, array( 'site', 'bold' ) );
        $this->assertSame( array( 'one <bold>', 'two <bold>', 'three <site,bold>' ), $run['lines'] );
        $this->assertSame( array( 'site' => 'resumed', 'bold' => 'new' ), $run['events'] );
    }

    public function testTheStateIsWrittenWholeAndReadBack()
    {
        $log = $this->viewCountDir() . '/logs/access_log';
        $state = new expViewCountState( $this->viewCountDir() . '/state', $log );
        $state->set( 'site', array( 'offset' => 10 ) );
        $this->assertTrue( $state->save() );
        $this->assertSame( array( basename( $state->file() ) ), array_values( array_filter( scandir( dirname( $state->file() ) ), function ( $f )
        {
            return $f[0] !== '.';
        } ) ), 'no temporary file is left' );
        $data = json_decode( file_get_contents( $state->file() ), true );
        $this->assertSame( expViewCountState::FORMAT, $data['format'] );
        $this->assertSame( $log, $data['log'] );
        $this->assertSame( 10, ( new expViewCountState( $this->viewCountDir() . '/state', $log ) )->entry( 'site' )['offset'] );
        $this->assertNull( ( new expViewCountState( $this->viewCountDir() . '/state', $log ) )->entry( 'bold' ) );
        // a broken file is an empty state, not an error
        file_put_contents( $state->file(), '{broken' );
        $this->assertSame( array(), ( new expViewCountState( $this->viewCountDir() . '/state', $log ) )->sites() );
    }

    public function testTheLockKeepsASecondRunOut()
    {
        $log = $this->viewCountDir() . '/logs/access_log';
        $first = new expViewCountState( $this->viewCountDir() . '/state', $log );
        $second = new expViewCountState( $this->viewCountDir() . '/state', $log );
        $this->assertTrue( $first->lock( 0 ) );
        $this->assertFalse( $second->lock( 0 ) );
        $first->unlock();
        $this->assertTrue( $second->lock( 0 ) );
        $second->unlock();
    }
}
