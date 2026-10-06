<?php
/**
 * One cache preload at a time (expPreloadLock) and what a run records (expPreloadHistory), in a scratch
 * directory under var/tmp: no database, no request, no preload.
 *
 *  PH-01 the lock is exclusive: a second holder is refused until the first lets go, and is free again after
 *  PH-02 the lock goes with its holder: an object that is gone holds nothing
 *  PH-03 a run's status is created, merged and read back; ids that are not 16 hex characters are refused
 *  PH-04 a run that says it is running but whose process is gone is reported as ended, not as running
 *  PH-05 a run that never wrote its process counts as starting for a minute, then as failed to start, with what
 *        it wrote to its error file
 *  PH-06 the runs are listed newest first, the one going on is current(), and prune() keeps the newest and never
 *        the one going on
 *  PH-07 a stop is asked for a running run only, and seen by the run
 *  PH-08 failures keep the address, the status code and three linking pages, with how many more
 *  PH-09 a run of an older version that wrote only its events is read from them
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class expPreloadLockAndHistoryTest extends PHPUnit\Framework\TestCase
{
    private $dir;

    protected function setUp(): void
    {
        $this->dir = getcwd() . '/var/tmp/preload-test-' . getmypid() . '-' . bin2hex( random_bytes( 4 ) );
        mkdir( $this->dir, 0775, true );
    }

    protected function tearDown(): void
    {
        foreach ( glob( $this->dir . '/{,.}*', GLOB_BRACE ) ?: array() as $file )
            if ( is_file( $file ) )
                unlink( $file );
        if ( is_dir( $this->dir ) )
            rmdir( $this->dir );
    }

    /** PH-01 */
    public function testTheLockIsExclusive()
    {
        $first = new expPreloadLock( $this->dir . '/run.lock' );
        $second = new expPreloadLock( $this->dir . '/run.lock' );
        $this->assertFalse( $second->isLocked() );
        $this->assertTrue( $first->acquire() );
        $this->assertTrue( $first->acquire(), 'taking it again is a no-op' );
        $this->assertTrue( $first->isHeld() );
        $this->assertTrue( $second->isLocked() );
        $this->assertFalse( $second->acquire() );
        $first->release();
        $this->assertFalse( $first->isHeld() );
        $this->assertFalse( $second->isLocked() );
        $this->assertTrue( $second->acquire() );
        $second->release();
    }

    /** PH-02 */
    public function testTheLockGoesWithItsHolder()
    {
        $holder = new expPreloadLock( $this->dir . '/sub/run.lock' );
        $this->assertTrue( $holder->acquire(), 'the directory is made' );
        unset( $holder );
        $this->assertFalse( ( new expPreloadLock( $this->dir . '/sub/run.lock' ) )->isLocked() );
        @unlink( $this->dir . '/sub/run.lock' );
        @rmdir( $this->dir . '/sub' );
    }

    /** PH-03 */
    public function testStatusCreatedUpdatedRead()
    {
        $history = new expPreloadHistory( $this->dir );
        $id = expPreloadHistory::newID();
        $this->assertTrue( expPreloadHistory::isID( $id ) );
        $this->assertFalse( expPreloadHistory::isID( '../../etc/passwd' ) );
        $this->assertFalse( expPreloadHistory::isID( 'ABCDEF0123456789' ) );
        $this->assertFalse( $history->create( 'not-an-id', array() ) );

        $status = $history->create( $id, array( 'siteaccess' => 'site', 'options' => array( 'max_pages' => 3 ) ) );
        $this->assertSame( 'starting', $status['state'] );
        $this->assertFileExists( $this->dir . '/' . $id . '.json' );
        $this->assertFileExists( $this->dir . '/' . $id . '.jsonl' );

        $history->update( $id, array( 'state' => 'finished', 'counts' => array( 'fetched' => 3 ) + expPreloadHistory::emptyCounts(), 'seconds' => 1.5 ) );
        $read = $history->read( $id );
        $this->assertSame( 'finished', $read['state'] );
        $this->assertFalse( $read['running'] );
        $this->assertSame( 3, $read['counts']['fetched'] );
        $this->assertSame( 1.5, $read['seconds'] );
        $this->assertSame( 'site', $read['siteaccess'] );
        $this->assertFalse( $history->read( '0000000000000000' ) );
    }

    /** PH-04 */
    public function testARunWhoseProcessIsGoneHasEnded()
    {
        $history = new expPreloadHistory( $this->dir );
        $id = expPreloadHistory::newID();
        $history->create( $id, array() );
        $history->update( $id, array( 'state' => 'running', 'pid' => getmypid() ) );
        $this->assertTrue( $history->read( $id )['running'], 'this process is there' );

        $history->update( $id, array( 'state' => 'running', 'pid' => 2147483646 ) );
        $read = $history->read( $id );
        $this->assertFalse( $read['running'] );
        $this->assertSame( 'died', $read['state'] );
        $this->assertNotSame( '', $read['message'] );
        $this->assertFalse( $history->current() );
        $this->assertFalse( expPreloadHistory::processAlive( 0 ) );
    }

    /** PH-05 */
    public function testARunThatNeverStarted()
    {
        $history = new expPreloadHistory( $this->dir );
        $id = expPreloadHistory::newID();
        $created = $history->create( $id, array() );
        $this->assertTrue( $history->read( $id, (int)$created['started'] + 10 )['running'] );
        $late = $history->read( $id, (int)$created['started'] + expPreloadHistory::START_GRACE + 5 );
        $this->assertFalse( $late['running'] );
        $this->assertSame( 'failed', $late['state'] );
        $this->assertStringContainsString( 'from the shell', $late['message'] );

        // what the run wrote to its error file before it could write its status is the reason given
        $this->assertStringStartsWith( '/', $history->errorFile( $id ) );
        file_put_contents( $history->errorFile( $id ), "PHP Fatal error:  <b>Something</b> broke\n" );
        $late = $history->read( $id, (int)$created['started'] + expPreloadHistory::START_GRACE + 5 );
        $this->assertSame( 'The preload did not start: PHP Fatal error: Something broke', $late['message'] );
    }

    /** PH-06 */
    public function testRunsCurrentAndPrune()
    {
        $history = new expPreloadHistory( $this->dir );
        $ids = array();
        for ( $i = 0; $i < 5; $i++ )
        {
            $ids[$i] = expPreloadHistory::newID();
            $history->create( $ids[$i], array( 'started' => 1000 + $i ) );
            $history->update( $ids[$i], array( 'state' => 'finished' ) );
        }
        $history->update( $ids[0], array( 'state' => 'running', 'pid' => getmypid() ) );

        $runs = $history->runs();
        $this->assertSame( array( $ids[4], $ids[3], $ids[2], $ids[1], $ids[0] ), array_column( $runs, 'id' ) );
        $this->assertCount( 2, $history->runs( 2 ) );
        $this->assertSame( $ids[0], $history->current()['id'] );

        $this->assertSame( 2, $history->prune( 2 ), 'the two older finished runs; the running one stays' );
        $this->assertSame( array( $ids[4], $ids[3], $ids[0] ), array_column( $history->runs(), 'id' ) );
        $this->assertFileDoesNotExist( $this->dir . '/' . $ids[1] . '.jsonl' );
    }

    /** PH-07 */
    public function testStop()
    {
        $history = new expPreloadHistory( $this->dir );
        $id = expPreloadHistory::newID();
        $history->create( $id, array() );
        $history->update( $id, array( 'state' => 'finished' ) );
        $this->assertFalse( $history->requestStop( $id ), 'a finished run is not stopped' );
        $history->update( $id, array( 'state' => 'running', 'pid' => getmypid() ) );
        $this->assertFalse( $history->stopRequested( $id ) );
        $this->assertTrue( $history->requestStop( $id ) );
        $this->assertTrue( $history->stopRequested( $id ) );
        $this->assertFalse( $history->requestStop( 'nonsense' ) );
    }

    /** PH-08 */
    public function testFailures()
    {
        $failures = expPreloadHistory::failuresOf( array(
            array( 'url' => 'https://example.test/gone', 'status' => 404, 'reason' => 'Missing pages (404)',
                   'referrers' => array( 'https://example.test/a', 'https://example.test/b', 'https://example.test/c', 'https://example.test/d' ), 'more' => 2 ),
            array( 'url' => 'https://example.test/', 'status' => 0, 'reason' => 'No response', 'referrers' => array() ),
        ) );
        $this->assertSame( array( 'url' => 'https://example.test/gone', 'status' => 404, 'reason' => 'Missing pages (404)',
                                  'referrers' => array( 'https://example.test/a', 'https://example.test/b', 'https://example.test/c' ), 'more' => 3 ), $failures[0] );
        $this->assertSame( 0, $failures[1]['status'] );
        $this->assertSame( array(), $failures[1]['referrers'] );

        $history = new expPreloadHistory( $this->dir );
        $id = expPreloadHistory::newID();
        $history->create( $id, array() );
        $many = array_fill( 0, expPreloadHistory::MAX_FAILURES + 7, $failures[0] );
        $read = $history->update( $id, array( 'failures' => $many ) );
        $this->assertCount( expPreloadHistory::MAX_FAILURES, $read['failures'] );
        $this->assertSame( 7, $read['failures_more'] );
    }

    /** PH-09 */
    public function testAnOlderRunFromItsEvents()
    {
        $id = 'abcdef0123456789';
        file_put_contents( $this->dir . '/' . $id . '.jsonl', implode( "\n", array(
            json_encode( array( 'type' => 'info', 'message' => 'Preloader started.', 'time' => 1000 ) ),
            json_encode( array( 'type' => 'info', 'message' => 'Base url: https://example.test, siteaccess prefix /bold', 'time' => 1000 ) ),
            json_encode( array( 'type' => 'report', 'message' => '...', 'time' => 1010,
                                'broken' => array( array( 'url' => 'https://example.test/x', 'status' => 404, 'referrers' => array() ) ) ) ),
            json_encode( array( 'type' => 'done', 'message' => '...', 'time' => 1010, 'seconds' => 9.5,
                                'counts' => array( 'fetched' => 12, 'broken' => 1 ) ) ),
            json_encode( array( 'type' => 'end', 'message' => '', 'time' => 1010 ) ),
        ) ) . "\n" );
        $read = ( new expPreloadHistory( $this->dir ) )->read( $id );
        $this->assertSame( 'finished', $read['state'] );
        $this->assertSame( 'https://example.test', $read['base_url'] );
        $this->assertSame( 12, $read['counts']['fetched'] );
        $this->assertSame( 0, $read['counts']['images'] );
        $this->assertSame( 9.5, $read['seconds'] );
        $this->assertSame( 404, $read['failures'][0]['status'] );
        $this->assertSame( 1000.0, $read['started'] );
    }
}
