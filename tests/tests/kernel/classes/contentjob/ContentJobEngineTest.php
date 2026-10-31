<?php
/**
 * The content job engine (kernel/classes/contentjob/) without a database: the store, the locks, the type
 * registry, the job's states and the worker's batches, run against a job type whose "database" is a JSON file
 * with a transaction of its own, so every guarantee of the engine can be checked exactly, kill -9 included
 * (the worker runs in a forked child that sends itself SIGKILL at the chosen point).
 *
 *  CJ-01 store: a job written is read back the same, no temporary file is left, a replacement is atomic
 *  CJ-02 store: invalid ids (paths, dots, upper case, too short) are refused everywhere
 *  CJ-03 store: update() is serialized by its flock: 8 processes x 25 increments = 200, none lost
 *  CJ-04 store: appended lines come back in order, the tail gives the last ones
 *  CJ-05 store: ids() lists jobs newest first and not their side files or the lock index
 *  CJ-06 locks: the overlap rules of subtree and node locks, both directions
 *  CJ-07 locks: an overlapping job is refused with a message naming the holder; the lock is free once done
 *  CJ-08 locks: 6 processes create jobs on one subtree at the same moment: exactly one gets it
 *  CJ-09 registry: names and classes are checked; unknown, missing and non-implementing classes are refused
 *  CJ-10 shouldRunAsJob(): at or above SynchronousLimit, always with 0
 *  CJ-11 permission refusal: a refused job writes no file and holds no lock
 *  CJ-12 worker: runs to done, every item exactly once, a log line per batch, the locks given back
 *  CJ-13 a failing batch is rolled back (nothing of it done), the job failed and resumable; the resume does the rest once
 *  CJ-14 kill -9 before the commit, after the commit, after afterBatch() and after the checkpoint, in the first and a
 *        middle batch: the cronjob part resumes it and the end state equals an uninterrupted run
 *  CJ-15 cancel: a queued job at once; a running job after its current batch, the rest left untouched
 *  CJ-16 a second worker for a running job leaves at once (exit 2) and changes nothing
 *  CJ-17 stale detection: running without a worker; queued without one after QueuedGrace; not a fresh queued job
 *  CJ-18 a job that starts MaxAttempts times without finishing a batch is failed, not retried for ever
 *  CJ-19 the checkpoint saved after a batch is what the next run starts from
 *  CJ-20 files created as root belong to the owner of the store's directory (when run as root)
 *  CJ-21 spawn() detaches: it returns at once while the worker still runs (setsid -f, no shell pipe), the worker runs on
 *  CJ-22 the mode: job always a job, now never (refused above NowLimit with code 413), auto = defaultMode(); kept with the job
 *  CJ-23 the job records where it was created and runs, the last batches, who cancelled and resumed it
 *  CJ-24 every type locks what it works on (subtree, or a target as a node lock); the selection types count without a database
 *  CJ-25 the state type per object, the state lists mocked (untestable on the demonstration server: its only group, ez_lock, is internal): assign,
 *        leave alone what has the state, skip with a warning what the user may not assign, once per object, idempotent
 *  CJ-26 a worker that cannot be started fails the job at once with the real reason and gives back its lock; resume takes it again
 *  CJ-27 a queued job no worker started within QueuedTimeout fails and stops blocking its subtree (not while a worker holds it)
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group contentjob
 */

/**
 * The test type's database: item => times processed, in a JSON file, with begin/commit/rollback.
 */
class ezpTestContentJobDB
{
    public static $file;
    public static $pending = null;

    public static function reset( $items )
    {
        self::$pending = null;
        file_put_contents( self::$file, json_encode( array_fill_keys( range( 1, $items ), 0 ) ) );
    }

    public static function read()
    {
        return json_decode( file_get_contents( self::$file ), true );
    }

    public static function begin()
    {
        self::$pending = self::read();
    }

    public static function commit()
    {
        $tmp = self::$file . '.tmp';
        file_put_contents( $tmp, json_encode( self::$pending ) );
        rename( $tmp, self::$file );
        self::$pending = null;
    }

    public static function rollback()
    {
        self::$pending = null;
    }
}

/**
 * Where a forked worker kills itself: point => batch number (1-based).
 */
class ezpTestContentJobKill
{
    public static $point = null;
    public static $batch = 0;
    public static $current = 0;

    public static function at( $point )
    {
        if ( self::$point === $point && self::$current === self::$batch )
            posix_kill( getmypid(), SIGKILL );
    }
}

class ezpTestContentJobType implements expContentJobType
{
    public static $failAtBatch = 0;
    public static $cancelAtBatch = 0;
    /** @var array batch number => how many more tries of it meet a busy database inside the batch (after writing) */
    public static $busyInBatch = array();

    public function validate( array $params, eZUser $user )
    {
        if ( (int) $user->attribute( 'contentobject_id' ) === 99 )
            throw new expContentJobException( 'You do not have permission to process these items.' );
        return array( 'path' => isset( $params['path'] ) ? $params['path'] : '/1/2/', 'items' => isset( $params['items'] ) ? (int) $params['items'] : 10 );
    }

    public function countNodes( array $params )
    {
        return isset( $params['items'] ) ? (int) $params['items'] : 10;
    }

    public function locks( array $params )
    {
        return array( array( 'path' => $params['path'], 'mode' => expContentJobLock::SUBTREE ) );
    }

    public function describe( array $params )
    {
        return 'Process ' . $params['items'] . ' items';
    }

    public function prepare( expContentJob $job )
    {
        $cp =& $job->checkpoint();
        $cp += array( 'batches_seen' => 0, 'runs' => 0 );
        $cp['runs']++;
    }

    public function runBatch( expContentJob $job, $batchSize )
    {
        ezpTestContentJobKill::$current = (int) $job->progress()['batch'] + 1;
        $cp =& $job->checkpoint();
        $cp['batches_seen']++;
        if ( self::$failAtBatch && ezpTestContentJobKill::$current === self::$failAtBatch )
        {
            // half the batch is written, then it fails: the rollback must undo that half
            $n = 0;
            foreach ( ezpTestContentJobDB::$pending as $item => $count )
                if ( $count === 0 && $n++ < 1 )
                    ezpTestContentJobDB::$pending[$item]++;
            throw new expContentJobException( 'simulated database error' );
        }
        if ( !empty( self::$busyInBatch[ezpTestContentJobKill::$current] ) )
        {
            // what the driver throws when a statement inside the transaction meets a lock: half the batch is written
            self::$busyInBatch[ezpTestContentJobKill::$current]--;
            $n = 0;
            foreach ( ezpTestContentJobDB::$pending as $item => $count )
                if ( $count === 0 && $n++ < 2 )
                    ezpTestContentJobDB::$pending[$item]++;
            throw new eZDBException( 'database is locked', 5 );
        }
        if ( self::$cancelAtBatch && ezpTestContentJobKill::$current === self::$cancelAtBatch )
            expContentJob::fetch( $job->id() )->cancel();
        // idempotent: the next items not done in the database
        $done = 0;
        foreach ( ezpTestContentJobDB::$pending as $item => $count )
        {
            if ( $count === 0 && $done < $batchSize )
            {
                ezpTestContentJobDB::$pending[$item]++;
                $done++;
            }
        }
        ezpTestContentJobKill::at( 'before-commit' );
        $left = count( array_filter( ezpTestContentJobDB::$pending, function ( $c ) { return $c === 0; } ) );
        return array( 'done' => $done, 'done_total' => count( ezpTestContentJobDB::$pending ) - $left, 'finished' => $left === 0,
                      'message' => "$done items" );
    }

    public function afterBatch( expContentJob $job )
    {
        ezpTestContentJobKill::at( 'after-batch' );
    }

    public function finish( expContentJob $job )
    {
        $job->setResult( 'items', count( ezpTestContentJobDB::read() ) );
    }
}

class ezpTestContentJobNotAType
{
}

class ezpTestContentJobWorker extends expContentJobWorker
{
    /** @var array batch number => how many more starts of its transaction find the database busy */
    public static $busyAtBegin = array();
    /** @var string|null a file the pauses after a busy database are appended to (seconds, one per line); no real sleep */
    public static $pauseFile = null;
    /** @var bool clearCaches() throws: an error outside every try of the run */
    public static $throwInClearCaches = false;

    protected function switchUser( expContentJob $job )
    {
    }

    protected function begin()
    {
        $batch = $this->job ? (int) $this->job->progress()['batch'] + 1 : 0;
        if ( !empty( self::$busyAtBegin[$batch] ) )
        {
            // what eZSQLite3DB::begin() throws after SQLiteTransactionWait without the write lock: nothing written
            self::$busyAtBegin[$batch]--;
            throw new eZDBException( 'database is busy: the transaction could not start within 1 s, another write held the lock all that time; nothing was written', 5 );
        }
        ezpTestContentJobDB::begin();
    }

    protected function sleepWithHeartbeat( expContentJob $job, $seconds )
    {
        if ( self::$pauseFile !== null )
            file_put_contents( self::$pauseFile, $seconds . "\n", FILE_APPEND );
        $this->beat( $job );
    }

    protected function commit()
    {
        ezpTestContentJobDB::commit();
        ezpTestContentJobKill::at( 'after-commit' );
        return true;
    }

    protected function rollback()
    {
        ezpTestContentJobDB::rollback();
    }

    protected function clearCaches()
    {
        if ( self::$throwInClearCaches )
            throw new RuntimeException( 'simulated error outside the batch' );
        // called right after the checkpoint is saved
        ezpTestContentJobKill::at( 'after-save' );
    }
}

/**
 * CJ-32: a job type on a real SQLite database: each batch records the next items in the table processed.
 */
class ezpTestContentJobSqliteType extends ezpTestContentJobType
{
    public function prepare( expContentJob $job )
    {
        $cp =& $job->checkpoint();
        $cp += array( 'runs' => 0 );
        $cp['runs']++;
    }

    public function runBatch( expContentJob $job, $batchSize )
    {
        $db = eZDB::instance();
        $done = $db->arrayQuery( 'SELECT item FROM processed' );
        $seen = array_fill_keys( array_map( 'intval', array_column( $done, 'item' ) ), true );
        $total = (int) $job->params()['items'];
        $n = 0;
        for ( $item = 1; $item <= $total && $n < $batchSize; $item++ )
        {
            if ( isset( $seen[$item] ) )
                continue;
            $db->query( 'INSERT INTO processed ( item, pid ) VALUES ( ' . $item . ', ' . getmypid() . ' )' );
            $seen[$item] = true;
            $n++;
        }
        return array( 'done' => $n, 'done_total' => count( $seen ), 'finished' => count( $seen ) >= $total, 'message' => "$n items" );
    }

    public function afterBatch( expContentJob $job )
    {
    }

    public function finish( expContentJob $job )
    {
    }
}

/**
 * CJ-32: the real worker (begin, commit and rollback of eZDB) without the user switch.
 */
class ezpTestContentJobSqliteWorker extends expContentJobWorker
{
    protected function switchUser( expContentJob $job )
    {
        eZDB::setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
    }
}

/**
 * The state type with the object states mocked: objectID => array( current, allowed ); assigned states recorded.
 */
class ezpTestContentJobStateType extends expContentJobStateSubtree
{
    public $states = array();
    public $assigned = array();

    protected function objectStates( $objectID )
    {
        return isset( $this->states[$objectID] ) ? $this->states[$objectID] : null;
    }

    protected function assignState( $objectID, $stateID )
    {
        $this->assigned[] = array( $objectID, $stateID );
        $this->states[$objectID]['current'][] = $stateID;
    }

    public function batch( expContentJob $job, array $rows, array &$cp )
    {
        $this->processNodes( $job, $rows, $cp );
    }
}

class ContentJobEngineTest extends PHPUnit\Framework\TestCase
{
    private $dir;

    protected function setUp(): void
    {
        $base = dirname( __DIR__, 5 ) . '/var/tmp/contentjobs/a';
        if ( !is_dir( $base ) )
            mkdir( $base, 0770, true );
        $this->dir = $base . '/unit-' . getmypid() . '-' . bin2hex( random_bytes( 3 ) );
        mkdir( $this->dir, 0770 );
        expContentJobStore::setDirectory( $this->dir . '/jobs' );
        expContentJob::$settings = array( 'SynchronousLimit' => 50, 'BatchSize' => 3, 'BatchPause' => 0, 'QueuedGrace' => 60,
                                          'MaxAttempts' => 5, 'KeepDays' => 30, 'PhpBinary' => '',
                                          'JobTypes' => array( 'test' => 'ezpTestContentJobType' ) );
        ezpTestContentJobDB::$file = $this->dir . '/db.json';
        ezpTestContentJobDB::reset( 10 );
        ezpTestContentJobKill::$point = null;
        ezpTestContentJobType::$failAtBatch = 0;
        ezpTestContentJobType::$cancelAtBatch = 0;
        ezpTestContentJobType::$busyInBatch = array();
        ezpTestContentJobWorker::$busyAtBegin = array();
        ezpTestContentJobWorker::$throwInClearCaches = false;
        ezpTestContentJobWorker::$pauseFile = $this->dir . '/pauses';
    }

    protected function tearDown(): void
    {
        // the test's own scratch directory
        foreach ( array_merge( (array) glob( $this->dir . '/jobs/{,.}*', GLOB_BRACE ), (array) glob( $this->dir . '/*' ) ) as $f )
            if ( is_file( $f ) )
                unlink( $f );
        if ( is_dir( $this->dir . '/jobs' ) )
            rmdir( $this->dir . '/jobs' );
        rmdir( $this->dir );
        expContentJobStore::setDirectory( null );
        expContentJob::$settings = null;
        ezpTestContentJobWorker::$pauseFile = null;
    }

    private function user( $id = 14 )
    {
        return new eZUser( array( 'contentobject_id' => $id ) );
    }

    private function create( $path = '/1/2/42/', $items = 10, $user = 14 )
    {
        return expContentJob::create( 'test', array( 'path' => $path, 'items' => $items ), $this->user( $user ) );
    }

    /**
     * Forks a child that runs $body and then always ends with SIGKILL (no PHPUnit shutdown handlers in the child).
     *
     * The children used to end with a posix_kill() after their work, so an exception or a failed assertion in the
     * child (testWorkerRunsToDoneEachItemOnce under a German locale) skipped it: the copy of the PHPUnit
     * process ran the rest of the suite next to the parent, and its later SIGKILL ended runs at random places
     * (exit 137). The kill is in a finally now, and an exception is shown on stderr.
     *
     * @return int the child's pid
     */
    private static function fork( callable $body )
    {
        $pid = pcntl_fork();
        if ( $pid === -1 )
            self::fail( 'could not fork' );
        if ( $pid === 0 )
        {
            // exit() or a fatal error in $body skips the finally below; the shutdown function still ends the child
            register_shutdown_function( static function () { posix_kill( getmypid(), SIGKILL ); } );
            try
            {
                $body();
            }
            catch ( \Throwable $e )
            {
                fwrite( STDERR, 'forked child ' . getmypid() . ': ' . get_class( $e ) . ': ' . $e->getMessage() . "\n" );
            }
            finally
            {
                posix_kill( getmypid(), SIGKILL );
                exit( 255 ); // not reached
            }
        }
        return $pid;
    }

    /**
     * Waits for the children; one still running after $seconds is killed and the test fails, the parent never hangs.
     *
     * @return array pid => wait status
     */
    private static function waitFor( array $pids, $seconds = 60 )
    {
        $statuses = array();
        $deadline = microtime( true ) + $seconds;
        while ( count( $statuses ) < count( $pids ) )
        {
            foreach ( $pids as $pid )
            {
                if ( array_key_exists( $pid, $statuses ) )
                    continue;
                $r = pcntl_waitpid( $pid, $status, WNOHANG );
                if ( $r === $pid || $r === -1 )
                    $statuses[$pid] = $r === $pid ? $status : null;
            }
            if ( count( $statuses ) < count( $pids ) )
            {
                if ( microtime( true ) > $deadline )
                {
                    foreach ( $pids as $pid )
                    {
                        if ( !array_key_exists( $pid, $statuses ) )
                        {
                            posix_kill( $pid, SIGKILL );
                            pcntl_waitpid( $pid, $status );
                        }
                    }
                    self::fail( "forked children did not end within $seconds s and were killed" );
                }
                usleep( 5000 );
            }
        }
        return $statuses;
    }

    /** Runs a worker in a forked child; returns its exit code, or 'killed'. */
    private function runForked( $id, $killPoint = null, $killBatch = 0 )
    {
        $codeFile = $this->dir . '/exit-code';
        @unlink( $codeFile );
        $pid = self::fork( function () use ( $id, $killPoint, $killBatch, $codeFile )
        {
            ezpTestContentJobKill::$point = $killPoint;
            ezpTestContentJobKill::$batch = $killBatch;
            $code = ( new ezpTestContentJobWorker() )->run( $id );
            file_put_contents( $codeFile, (string) $code );
        } );
        self::waitFor( array( $pid ) );
        // the child always ends by SIGKILL; without the file it was killed before the worker returned
        if ( !is_file( $codeFile ) )
            return 'killed';
        return (int) file_get_contents( $codeFile );
    }

    private function counts()
    {
        return array_count_values( array_map( 'strval', ezpTestContentJobDB::read() ) );
    }

    /** CJ-01 */
    public function testStoreRoundTripAndAtomicWrite()
    {
        $data = array( 'id' => 'abc-123456', 'state' => 'queued', 'list' => array( 1, 2, 3 ), 'text' => "ünïcode\n\"quoted\"" );
        expContentJobStore::write( 'abc-123456', $data );
        $this->assertSame( $data, expContentJobStore::read( 'abc-123456' ) );
        $data['state'] = 'running';
        expContentJobStore::write( 'abc-123456', $data );
        $this->assertSame( 'running', expContentJobStore::read( 'abc-123456' )['state'] );
        $this->assertSame( array(), glob( $this->dir . '/jobs/*.tmp.*' ), 'no temporary file is left' );
        $this->assertNull( expContentJobStore::read( 'abc-999999' ) );
        $this->assertFileExists( $this->dir . '/jobs/.htaccess' );
    }

    /** CJ-02 */
    public function testStoreRejectsInvalidIDs()
    {
        foreach ( array( '../etc/passwd', 'a.b.c.d.e', 'ABCDEFG', 'abc', '', '/abs-123456', "abc-123\n", 42 ) as $bad )
        {
            $this->assertFalse( expContentJobStore::validID( $bad ), var_export( $bad, true ) );
            $this->assertNull( expContentJobStore::read( $bad ) );
            $this->assertNull( expContentJob::fetch( $bad ) );
        }
        $this->expectException( expContentJobException::class );
        expContentJobStore::path( '../x-123456' );
    }

    /** CJ-03 */
    public function testStoreUpdateIsSerializedAcrossProcesses()
    {
        expContentJobStore::write( 'cnt-000001', array( 'n' => 0 ) );
        $pids = array();
        for ( $p = 0; $p < 8; $p++ )
        {
            $pids[] = self::fork( function ()
            {
                for ( $i = 0; $i < 25; $i++ )
                    expContentJobStore::update( 'cnt-000001', function ( array &$d ) { $d['n']++; } );
            } );
        }
        self::waitFor( $pids );
        $this->assertSame( 200, expContentJobStore::read( 'cnt-000001' )['n'] );
    }

    /** CJ-04 */
    public function testStoreAppendAndTail()
    {
        expContentJobStore::write( 'log-000001', array() );
        for ( $i = 1; $i <= 300; $i++ )
            expContentJobStore::append( 'log-000001', '.log', array( "line $i" ) );
        $all = expContentJobStore::lines( 'log-000001', '.log' );
        $this->assertCount( 300, $all );
        $this->assertSame( 'line 1', $all[0] );
        $this->assertSame( array( 'line 298', 'line 299', 'line 300' ), expContentJobStore::lines( 'log-000001', '.log', 3 ) );
        $this->assertSame( array(), expContentJobStore::lines( 'log-000002', '.log', 3 ) );
    }

    /** CJ-05 */
    public function testStoreIDsNewestFirstWithoutSideFiles()
    {
        expContentJobStore::write( '20260101-000000-aaaaaaaa', array() );
        expContentJobStore::write( '20260102-000000-bbbbbbbb', array() );
        expContentJobStore::writeSide( '20260102-000000-bbbbbbbb', '.nodes.json', array( 1, 2 ) );
        expContentJobStore::writeJSON( expContentJobStore::directory() . '/locks.json', new stdClass() );
        $this->assertSame( array( '20260102-000000-bbbbbbbb', '20260101-000000-aaaaaaaa' ), expContentJobStore::ids() );
    }

    /** CJ-06 */
    public function testLockOverlapRules()
    {
        $s = function ( $p ) { return array( 'path' => $p, 'mode' => 'subtree' ); };
        $n = function ( $p ) { return array( 'path' => $p, 'mode' => 'node' ); };
        $this->assertTrue( expContentJobLock::overlaps( $s( '/1/2/42/' ), $s( '/1/2/42/' ) ) );
        $this->assertTrue( expContentJobLock::overlaps( $s( '/1/2/42/' ), $s( '/1/2/42/50/' ) ), 'descendant' );
        $this->assertTrue( expContentJobLock::overlaps( $s( '/1/2/42/50/' ), $s( '/1/2/42/' ) ), 'ancestor' );
        $this->assertFalse( expContentJobLock::overlaps( $s( '/1/2/42/' ), $s( '/1/2/43/' ) ), 'sibling' );
        $this->assertFalse( expContentJobLock::overlaps( $s( '/1/2/4/' ), $s( '/1/2/42/' ) ), 'prefix of a number is not an ancestor' );
        // a node lock (a copy's destination): its ancestors and itself overlap, its descendants and siblings do not
        $this->assertTrue( expContentJobLock::overlaps( $s( '/1/2/' ), $n( '/1/2/42/' ) ) );
        $this->assertTrue( expContentJobLock::overlaps( $s( '/1/2/42/' ), $n( '/1/2/42/' ) ) );
        $this->assertFalse( expContentJobLock::overlaps( $s( '/1/2/42/50/' ), $n( '/1/2/42/' ) ) );
        $this->assertFalse( expContentJobLock::overlaps( $s( '/1/2/43/' ), $n( '/1/2/42/' ) ) );
        $this->assertTrue( expContentJobLock::overlaps( $n( '/1/2/42/' ), $s( '/1/2/' ) ) );
        $this->assertFalse( expContentJobLock::overlaps( $n( '/1/2/42/' ), $n( '/1/2/42/' ) ), 'two copies into one place' );
        $this->assertFalse( expContentJobLock::overlaps( $s( 'nonsense' ), $s( '/1/' ) ) );
        $this->assertSame( '/1/2/42/', expContentJobLock::normalizePath( '1/2/42' ) );
    }

    /** CJ-07 */
    public function testOverlappingJobIsRefusedUntilTheFirstIsDone()
    {
        $first = $this->create( '/1/2/42/' );
        try
        {
            $this->create( '/1/2/42/50/' );
            $this->fail( 'an overlapping job was accepted' );
        }
        catch ( expContentJobException $e )
        {
            $this->assertStringContainsString( $first->id(), $e->getMessage() );
            $this->assertSame( 409, $e->getCode() );
        }
        $this->assertSame( $first->id(), expContentJobLock::check( '/1/2/' )->id(), 'an operation on an ancestor is refused' );
        $this->assertSame( $first->id(), expContentJobLock::check( '/1/2/42/77/' )->id(), 'and one inside' );
        $this->assertFalse( expContentJobLock::check( '/1/2/43/' ) );
        $this->assertFalse( expContentJobLock::check( '/1/2/42/', $first->id() ), 'the job itself is not refused' );
        $other = $this->create( '/1/2/43/' );
        $this->assertSame( 'queued', $other->state() );

        $this->assertSame( 0, $this->runForked( $first->id() ) );
        $this->assertFalse( expContentJobLock::check( '/1/2/42/' ), 'free once done' );
        $this->assertSame( 'queued', $this->create( '/1/2/42/50/', 1 )->state() );
    }

    /** CJ-08 */
    public function testConcurrentCreatesOnlyOneGetsTheLock()
    {
        $pids = array();
        for ( $p = 0; $p < 6; $p++ )
        {
            $pids[] = self::fork( function () use ( $p )
            {
                try
                {
                    $this->create( '/1/2/60/' );
                    file_put_contents( $this->dir . "/won-$p", '1' );
                }
                catch ( expContentJobException $e )
                {
                    file_put_contents( $this->dir . "/lost-$p", $e->getMessage() );
                }
            } );
        }
        self::waitFor( $pids );
        $this->assertCount( 1, glob( $this->dir . '/won-*' ) );
        $this->assertCount( 5, glob( $this->dir . '/lost-*' ) );
        $this->assertCount( 1, expContentJob::listFor( null ), 'the refused ones wrote no job' );
        $this->assertCount( 1, expContentJobLock::active() );
    }

    /** CJ-09 */
    public function testTypeRegistry()
    {
        expContentJob::$settings['JobTypes'] = array( 'test' => 'ezpTestContentJobType', 'Bad Name' => 'ezpTestContentJobType',
                                                      'missing' => 'ezpNoSuchClassAnywhere', 'nottype' => 'ezpTestContentJobNotAType', 'empty' => '' );
        $this->assertSame( array( 'test', 'missing', 'nottype' ), array_keys( expContentJob::types() ) );
        $this->assertInstanceOf( 'ezpTestContentJobType', expContentJob::handler( 'test' ) );
        foreach ( array( 'missing' => 'does not exist', 'nottype' => 'does not implement', 'Bad Name' => 'Unknown', 'other' => 'Unknown' ) as $type => $message )
        {
            try
            {
                expContentJob::handler( $type );
                $this->fail( "type $type accepted" );
            }
            catch ( expContentJobException $e )
            {
                $this->assertStringContainsString( $message, $e->getMessage() );
            }
        }
        expContentJob::$settings = null;
        $this->assertSame( array( 'remove', 'copy', 'move', 'hide', 'reveal', 'section', 'state', 'addlocation', 'removelocation', 'packageinstall', 'auditexport', 'helpmanuals' ),
                           array_keys( expContentJob::$defaults['JobTypes'] ) );
        foreach ( expContentJob::$defaults['JobTypes'] as $type => $class )
        {
            $this->assertTrue( class_exists( $class ), "$type: $class is autoloaded" );
            $this->assertTrue( in_array( 'expContentJobType', class_implements( $class ), true ), "$type: $class is a job type" );
            $this->assertFalse( ( new ReflectionClass( $class ) )->isAbstract(), "$type: $class can be created" );
        }
        // content.ini registers the same types
        $ini = file_get_contents( dirname( __DIR__, 5 ) . '/settings/content.ini' );
        foreach ( expContentJob::$defaults['JobTypes'] as $type => $class )
            $this->assertStringContainsString( "JobTypes[$type]=$class", $ini );
    }

    /** CJ-10 */
    public function testShouldRunAsJob()
    {
        $this->assertFalse( expContentJob::shouldRunAsJob( 'test', array( 'items' => 49 ) ) );
        $this->assertTrue( expContentJob::shouldRunAsJob( 'test', array( 'items' => 50 ) ) );
        expContentJob::$settings['SynchronousLimit'] = 0;
        $this->assertTrue( expContentJob::shouldRunAsJob( 'test', array( 'items' => 1 ) ) );
        $this->assertSame( 7, expContentJob::countNodes( 'test', array( 'items' => 7 ) ) );
    }

    /** CJ-11 */
    public function testPermissionRefusalWritesNothing()
    {
        try
        {
            $this->create( '/1/2/42/', 10, 99 );
            $this->fail( 'a refused user got a job' );
        }
        catch ( expContentJobException $e )
        {
            $this->assertStringContainsString( 'permission', $e->getMessage() );
        }
        $this->assertSame( array(), expContentJobStore::ids() );
        $this->assertFalse( expContentJobLock::check( '/1/2/42/' ) );
    }

    /** CJ-12 */
    public function testWorkerRunsToDoneEachItemOnce()
    {
        $job = $this->create( '/1/2/42/', 10 );
        $this->assertSame( 'queued', $job->state() );
        $this->assertSame( 10, $job->progress()['total'] );
        $this->assertSame( 0, $this->runForked( $job->id() ) );
        $job = expContentJob::fetch( $job->id() );
        $this->assertSame( 'done', $job->state() );
        $this->assertSame( array( '1' => 10 ), $this->counts() );
        $p = $job->progress();
        $this->assertSame( array( 10, 10, 100, 4 ), array( $p['done'], $p['total'], $p['percent'], $p['batch'] ) );
        $this->assertCount( 4, preg_grep( '/batch \d+: \d+ done .* peak [\d.]+ MB/', $job->log( 100 ) ) );
        $this->assertSame( array(), expContentJobLock::locksOf( $job->id() ) );
        $this->assertFalse( expContentJobLock::check( '/1/2/42/' ) );
        $this->assertSame( 10, $job->result()['items'] );
        $this->assertSame( 2, $this->runForked( $job->id() ), 'a done job does not run again' );
        $this->assertSame( array( '1' => 10 ), $this->counts() );
    }

    /**
     * CJ-12b: the log line per batch uses a dot as decimal separator whatever LC_NUMERIC says. With %f a German
     * locale (config.php calling setlocale( LC_ALL, "de_DE.UTF-8" )) wrote "in 0,01 s, peak 30,0 MB".
     */
    public function testWorkerLogLineUsesADotUnderACommaLocale()
    {
        $previous = setlocale( LC_NUMERIC, '0' );
        $comma = setlocale( LC_NUMERIC, 'de_DE.UTF-8', 'de_DE.utf8', 'de_DE', 'German_Germany.1252' );
        if ( $comma === false || sprintf( '%.1f', 1.5 ) !== '1,5' )
        {
            setlocale( LC_NUMERIC, $previous );
            $this->markTestSkipped( 'no locale with a decimal comma is installed' );
        }
        try
        {
            $job = $this->create( '/1/2/42/', 10 );
            // the forked child inherits the comma locale
            $this->assertSame( 0, $this->runForked( $job->id() ) );
        }
        finally
        {
            setlocale( LC_NUMERIC, $previous );
        }
        $lines = preg_grep( '/^.*batch \d+: /', expContentJob::fetch( $job->id() )->log( 100 ) );
        $this->assertCount( 4, $lines );
        foreach ( $lines as $line )
        {
            $this->assertMatchesRegularExpression( '/ in \d+\.\d\d s, peak \d+\.\d MB, memory \d+\.\d MB/', $line );
            $this->assertStringNotContainsString( ',0 MB', $line );
        }
    }

    /** CJ-13 */
    public function testFailedBatchIsRolledBackAndResumeFinishesOnce()
    {
        $job = $this->create( '/1/2/42/', 10 );
        ezpTestContentJobType::$failAtBatch = 2;
        $this->assertSame( 1, $this->runForked( $job->id() ) );
        $job = expContentJob::fetch( $job->id() );
        $this->assertSame( 'failed', $job->state() );
        $this->assertStringContainsString( 'simulated database error', $job->error() );
        $this->assertSame( array( '1' => 3, '0' => 7 ), $this->counts(), 'batch 1 done, nothing of batch 2' );
        $this->assertSame( $job->id(), expContentJobLock::check( '/1/2/42/' )->id(), 'a failed job keeps its lock' );
        $this->assertTrue( $job->canResume() );

        ezpTestContentJobType::$failAtBatch = 0;
        $this->assertTrue( $job->resume( false ) );
        $this->assertSame( 0, $this->runForked( $job->id() ) );
        $this->assertSame( 'done', expContentJob::fetch( $job->id() )->state() );
        $this->assertSame( array( '1' => 10 ), $this->counts() );
    }

    /** CJ-14 */
    public function testKillAtEveryPointThenResumeEqualsUninterruptedRun()
    {
        // the uninterrupted run
        $ref = $this->create( '/1/2/90/', 10 );
        $this->assertSame( 0, $this->runForked( $ref->id() ) );
        $refState = ezpTestContentJobDB::read();
        $refProgress = expContentJob::fetch( $ref->id() )->progress();

        $n = 0;
        foreach ( array( 'before-commit', 'after-commit', 'after-batch', 'after-save' ) as $point )
        {
            foreach ( array( 1, 3 ) as $batch )
            {
                ezpTestContentJobDB::reset( 10 );
                $job = $this->create( '/1/2/' . ( 100 + $n++ ) . '/', 10 );
                $this->assertSame( 'killed', $this->runForked( $job->id(), $point, $batch ), "$point in batch $batch" );
                $killed = expContentJob::fetch( $job->id() );
                $this->assertSame( 'running', $killed->state(), 'a killed worker leaves the job running' );
                $this->assertFalse( $killed->workerAlive() );
                $this->assertTrue( $killed->isStale() );

                // the cronjob part picks it up (queued again; the worker is run here instead of spawned)
                $this->assertSame( 1, \Exponential\Cronjob\Kernel\Contentjobs::resumeStale( null, false ) );
                $this->assertSame( 0, $this->runForked( $job->id() ), "resume after $point in batch $batch" );
                $done = expContentJob::fetch( $job->id() );
                $this->assertSame( 'done', $done->state() );
                $this->assertSame( $refState, ezpTestContentJobDB::read(), "every item exactly once after $point in batch $batch" );
                $this->assertSame( $refProgress['done'], $done->progress()['done'], "progress after $point in batch $batch" );
                $this->assertFalse( expContentJobLock::check( '/1/2/' . ( 100 + $n - 1 ) . '/' ) );
                $this->assertSame( 2, $done->attempts() );
            }
        }
    }

    /** CJ-15 */
    public function testCancel()
    {
        $queued = $this->create( '/1/2/42/', 10 );
        $this->assertTrue( $queued->cancel() );
        $this->assertSame( 'cancelled', expContentJob::fetch( $queued->id() )->state() );
        $this->assertFalse( expContentJobLock::check( '/1/2/42/' ) );
        $this->assertSame( 2, $this->runForked( $queued->id() ) );
        $this->assertSame( array( '0' => 10 ), $this->counts() );
        $this->assertFalse( expContentJob::fetch( $queued->id() )->cancel(), 'a cancelled job cannot be cancelled again' );
        $this->assertFalse( expContentJob::fetch( $queued->id() )->resume( false ), 'nor resumed' );

        // running: the cancel arrives during batch 2 (the worker holds the run lock), it stops after that batch
        $running = $this->create( '/1/2/43/', 10 );
        ezpTestContentJobType::$cancelAtBatch = 2;
        $this->assertSame( 0, $this->runForked( $running->id() ) );
        $job = expContentJob::fetch( $running->id() );
        $this->assertSame( 'cancelled', $job->state() );
        $this->assertSame( array( '1' => 6, '0' => 4 ), $this->counts(), 'two batches done, the rest untouched' );
        $this->assertFalse( expContentJobLock::check( '/1/2/43/' ) );
        $this->assertNotEmpty( preg_grep( '/cancel requested/', $job->log( 50 ) ) );
    }

    /** CJ-16 */
    public function testSecondWorkerForTheSameJobLeaves()
    {
        $job = $this->create( '/1/2/42/', 10 );
        $fh = fopen( expContentJobStore::path( $job->id(), '.run.lock' ), 'c' );
        $this->assertTrue( flock( $fh, LOCK_EX | LOCK_NB ) );
        $this->assertTrue( $job->workerAlive() );
        $this->assertFalse( $job->canResume(), 'a job with a live worker is not resumable' );
        $this->assertSame( 2, $this->runForked( $job->id() ) );
        $this->assertSame( array( '0' => 10 ), $this->counts() );
        $this->assertSame( 'queued', expContentJob::fetch( $job->id() )->state() );
        flock( $fh, LOCK_UN );
        fclose( $fh );
        $this->assertSame( 0, $this->runForked( $job->id() ) );
        $this->assertSame( array( '1' => 10 ), $this->counts() );
    }

    /** CJ-17 */
    public function testStaleDetection()
    {
        $fresh = $this->create( '/1/2/42/', 10 );
        $this->assertFalse( $fresh->isStale(), 'a fresh queued job waits for its spawned worker' );
        expContentJobStore::update( $fresh->id(), function ( array &$d ) { $d['heartbeat'] = time() - 61; } );
        $this->assertTrue( expContentJob::fetch( $fresh->id() )->isStale(), 'queued without a worker after QueuedGrace' );
        expContentJobStore::update( $fresh->id(), function ( array &$d ) { $d['heartbeat'] = time(); $d['state'] = 'running'; } );
        $this->assertTrue( expContentJob::fetch( $fresh->id() )->isStale(), 'running without a worker' );
        $done = $this->create( '/1/2/43/', 1 );
        $this->assertSame( 0, $this->runForked( $done->id() ) );
        $this->assertFalse( expContentJob::fetch( $done->id() )->isStale() );
        $this->assertSame( 1, \Exponential\Cronjob\Kernel\Contentjobs::resumeStale( null, false ) );
        $this->assertSame( 'queued', expContentJob::fetch( $fresh->id() )->state() );
    }

    /** CJ-18 */
    public function testStallLimit()
    {
        expContentJob::$settings['MaxAttempts'] = 3;
        $job = $this->create( '/1/2/42/', 10 );
        $results = array();
        for ( $i = 0; $i < 4; $i++ )
        {
            $results[] = $this->runForked( $job->id(), 'before-commit', 1 );
            $state = expContentJob::fetch( $job->id() )->state();
            if ( $state === 'failed' )
                break;
            \Exponential\Cronjob\Kernel\Contentjobs::resumeStale( null, false );
        }
        $job = expContentJob::fetch( $job->id() );
        $this->assertSame( array( 'killed', 'killed', 'killed', 1 ), $results );
        $this->assertSame( 'failed', $job->state() );
        $this->assertStringContainsString( 'without a batch done', $job->error() );
        $this->assertSame( array( '0' => 10 ), $this->counts() );
        // fixed, resumed: it runs again from the start of its stall count
        $this->assertTrue( $job->resume( false ) );
        $this->assertSame( 0, $this->runForked( $job->id() ) );
        $this->assertSame( array( '1' => 10 ), $this->counts() );
    }

    /** CJ-19 */
    public function testCheckpointIsWhatTheNextRunStartsFrom()
    {
        $job = $this->create( '/1/2/42/', 10 );
        $this->assertSame( 'killed', $this->runForked( $job->id(), 'after-save', 2 ) );
        $cp = expContentJob::fetch( $job->id() )->checkpoint();
        $this->assertSame( array( 'batches_seen' => 2, 'runs' => 1 ), $cp, 'the checkpoint of batch 2 was saved' );
        $this->assertSame( 'killed', $this->runForked( $job->id(), 'before-commit', 3 ) );
        $cp = expContentJob::fetch( $job->id() )->checkpoint();
        $this->assertSame( array( 'batches_seen' => 2, 'runs' => 2 ), $cp, 'the batch that died left no checkpoint' );
        $this->assertSame( 0, $this->runForked( $job->id() ) );
        $this->assertSame( 4, expContentJob::fetch( $job->id() )->checkpoint()['batches_seen'] );
        $this->assertSame( array( '1' => 10 ), $this->counts() );
    }

    /** CJ-22 */
    public function testModeChoiceAndNowLimit()
    {
        expContentJob::$settings['NowLimit'] = 100;
        $this->assertTrue( expContentJob::shouldRunAsJob( 'test', array( 'items' => 3, 'mode' => 'job' ) ), 'job: always a job' );
        $this->assertFalse( expContentJob::shouldRunAsJob( 'test', array( 'items' => 80, 'mode' => 'now' ) ), 'now: in the request' );
        $this->assertFalse( expContentJob::shouldRunAsJob( 'test', array( 'items' => 100, 'mode' => 'now' ) ), 'now at the ceiling' );
        try
        {
            expContentJob::shouldRunAsJob( 'test', array( 'items' => 101, 'mode' => 'now' ) );
            $this->fail( 'now above NowLimit was accepted' );
        }
        catch ( expContentJobException $e )
        {
            $this->assertSame( 413, $e->getCode() );
            $this->assertStringContainsString( '101 nodes, more than the 100', $e->getMessage() );
        }
        $this->assertFalse( expContentJob::nowAllowed( 'test', array( 'items' => 101 ) ) );
        $this->assertSame( 'now', expContentJob::defaultMode( 'test', array( 'items' => 49 ) ) );
        $this->assertSame( 'job', expContentJob::defaultMode( 'test', array( 'items' => 50 ) ) );
        $this->assertTrue( expContentJob::shouldRunAsJob( 'test', array( 'items' => 50, 'mode' => 'auto' ) ) );
        $this->assertFalse( expContentJob::shouldRunAsJob( 'test', array( 'items' => 49 ) ) );
        expContentJob::$settings['NowLimit'] = 0;
        $this->assertTrue( expContentJob::nowAllowed( 'test', array( 'items' => 1000000 ) ), '0 = no ceiling' );

        // the mode is kept with the job and not handed to the type
        $job = expContentJob::create( 'test', array( 'path' => '/1/2/42/', 'items' => 4, 'mode' => 'job' ), $this->user() );
        $this->assertSame( 'job', $job->get( 'mode' ) );
        $this->assertArrayNotHasKey( 'mode', $job->params() );
        $odd = expContentJob::create( 'test', array( 'path' => '/1/2/43/', 'items' => 4, 'mode' => 'bogus' ), $this->user() );
        $this->assertSame( 'auto', $odd->get( 'mode' ) );
    }

    /** CJ-23 */
    public function testJobRecordsServerBatchesCancelAndResume()
    {
        $job = $this->create( '/1/2/42/', 10 );
        $a = $job->toArray();
        $this->assertSame( 'cli', $a['server']['created']['server'] );
        $this->assertSame( getmypid(), $a['server']['created']['pid'] );
        ezpTestContentJobType::$failAtBatch = 3;
        $this->assertSame( 1, $this->runForked( $job->id() ) );
        $a = expContentJob::fetch( $job->id() )->toArray();
        $this->assertCount( 2, $a['batches'] );
        $this->assertSame( array( 1, 2 ), array_column( $a['batches'], 'n' ) );
        $this->assertSame( array( 3, 3 ), array_column( $a['batches'], 'done' ) );
        foreach ( array( 'seconds', 'peak_mb', 'at' ) as $key )
            $this->assertArrayHasKey( $key, $a['batches'][0] );
        $this->assertSame( 'cli', $a['server']['worker']['server'] );
        $this->assertGreaterThan( 0, $a['worker_pid'] );
        $this->assertNotSame( getmypid(), $a['worker_pid'], 'the pid of the worker, not of the creator' );
        ezpTestContentJobType::$failAtBatch = 0;
        $this->assertTrue( expContentJob::fetch( $job->id() )->resume( false ) );
        $a = expContentJob::fetch( $job->id() )->toArray();
        $this->assertCount( 1, $a['resumed'] );
        $this->assertSame( 'failed', $a['resumed'][0]['was'] );
        $this->assertTrue( expContentJob::fetch( $job->id() )->cancel() );
        $a = expContentJob::fetch( $job->id() )->toArray();
        $this->assertSame( 'cancelled', $a['state'] );
        $this->assertGreaterThan( 0, $a['cancelled_at'] );
        $this->assertArrayHasKey( 'cancelled_by', $a );
        $this->assertSame( 0, $a['error_node_id'] );
        $e = expContentJobException::forNode( 'gone', 77 );
        $this->assertSame( 77, $e->nodeID );
    }

    /** CJ-24 */
    public function testEveryTypeLocksWhatItWorksOn()
    {
        $subtree = array( 'remove' => array( 'roots' => array( array( 'node_id' => 42, 'path' => '/1/2/42/', 'trash' => true ) ), 'node_ids' => array( 42 ) ),
                          'hide' => array( 'node_id' => 42, 'path' => '/1/2/42/' ),
                          'reveal' => array( 'node_id' => 42, 'path' => '/1/2/42/' ),
                          'section' => array( 'node_id' => 42, 'path' => '/1/2/42/', 'section_id' => 3, 'section_name' => 'x' ),
                          'state' => array( 'node_id' => 42, 'path' => '/1/2/42/', 'state_id' => 3, 'state_name' => 'x' ),
                          'removelocation' => array( 'node_ids' => array( 42 ), 'paths' => array( '/1/2/42/' ) ) );
        foreach ( $subtree as $type => $params )
        {
            $class = expContentJob::$defaults['JobTypes'][$type];
            $locks = ( new $class() )->locks( $params );
            $this->assertSame( array( array( 'path' => '/1/2/42/', 'mode' => 'subtree' ) ), $locks, $type );
        }
        $move = ( new expContentJobMoveSubtree() )->locks( array( 'source_path' => '/1/2/42/', 'destination_path' => '/1/2/50/' ) );
        $this->assertSame( array( array( 'path' => '/1/2/42/', 'mode' => 'subtree' ), array( 'path' => '/1/2/50/', 'mode' => 'node' ) ), $move );
        $copy = ( new expContentJobCopySubtree() )->locks( array( 'source_path' => '/1/2/42/', 'destination_path' => '/1/2/50/' ) );
        $this->assertSame( $move, $copy );
        // a move out of a subtree a job removes, or into it, is refused; a move into a sibling of a copy target is not
        $this->assertTrue( expContentJobLock::overlaps( $move[0], array( 'path' => '/1/2/42/7/', 'mode' => 'subtree' ) ) );
        $this->assertTrue( expContentJobLock::overlaps( $move[1], array( 'path' => '/1/2/', 'mode' => 'subtree' ) ) );
        $this->assertFalse( expContentJobLock::overlaps( $move[1], array( 'path' => '/1/2/51/', 'mode' => 'subtree' ) ) );
        // the selection types count their selection, without the database
        $this->assertSame( 3, ( new expContentJobAddLocation() )->countNodes( array( 'node_ids' => array( 5, 6, 6, 7, 'x' ) ) ) );
        $this->assertSame( 2, ( new expContentJobRemoveLocation() )->countNodes( array( 'node_ids' => array( 5, 6 ) ) ) );
    }

    /** CJ-25 */
    public function testStateTypeAssignsPerObjectWithItsPolicy()
    {
        $this->assertSame( 'assign', expContentJobStateSubtree::decide( 5, array( 1, 3 ), array( 4, 5 ) ) );
        $this->assertSame( 'already', expContentJobStateSubtree::decide( 5, array( 5 ), array() ), 'already in the state: left alone, even when not allowed' );
        $this->assertSame( 'denied', expContentJobStateSubtree::decide( 5, array( 1 ), array( 4 ) ) );
        $this->assertSame( 'assign', expContentJobStateSubtree::decide( '5', array( '1' ), array( '5' ) ), 'ids as strings from the database' );

        $type = new ezpTestContentJobStateType();
        $type->states = array( 101 => array( 'current' => array( 1 ), 'allowed' => array( 1, 2 ) ),     // assign
                               102 => array( 'current' => array( 2 ), 'allowed' => array( 1, 2 ) ),     // already
                               103 => array( 'current' => array( 1 ), 'allowed' => array( 1 ) ),        // denied
                               104 => array( 'current' => array( 1 ), 'allowed' => array( 2 ) ) );      // assign
        $job = new expContentJob( array( 'id' => 'state-test-1', 'type' => 'state', 'state' => 'running', 'params' => array( 'state_id' => 2 ) ) );
        $cp = array( 'changed' => 0, 'skipped' => 0, 'warnings' => array(), 'warning_count' => 0 );
        // 104 has two locations in the batch, 105 no longer exists
        $rows = array( array( 'contentobject_id' => 101 ), array( 'contentobject_id' => 102 ), array( 'contentobject_id' => 103 ),
                       array( 'contentobject_id' => 104 ), array( 'contentobject_id' => 104 ), array( 'contentobject_id' => 105 ) );
        $type->batch( $job, $rows, $cp );
        $this->assertSame( array( array( 101, 2 ), array( 104, 2 ) ), $type->assigned, 'once per object, through the kernel operation' );
        $this->assertSame( 2, $cp['changed'] );
        $this->assertSame( 1, $cp['skipped'] );
        $this->assertStringContainsString( 'Object (ID = 103)', $cp['warnings'][0] );

        // the same batch again (a crash between commit and checkpoint): nothing is assigned twice
        $type->batch( $job, $rows, $cp );
        $this->assertCount( 2, $type->assigned );
        $this->assertSame( 2, $cp['changed'] );
    }

    /** CJ-21 */
    public function testSpawnDetachesAndReturnsBeforeTheWorkerEnds()
    {
        $job = $this->create( '/1/2/42/', 10 );
        $marker = $this->dir . '/worker-ended';
        expContentJob::$spawnArgv = function ( $job ) use ( $marker ) {
            // a "worker" that takes 3 seconds, then leaves a marker
            return array( '/bin/sh', '-c', 'sleep 3; echo ended > ' . escapeshellarg( $marker ) );
        };
        try
        {
            $t0 = microtime( true );
            $this->assertTrue( $job->spawn() );
            $elapsed = microtime( true ) - $t0;
        }
        finally
        {
            expContentJob::$spawnArgv = null;
        }
        $this->assertLessThan( 1.0, $elapsed, 'spawn() returns at once, it does not wait for the worker' );
        $this->assertFileDoesNotExist( $marker, 'the worker was still running when spawn() returned' );
        $this->assertSame( 'queued', expContentJob::fetch( $job->id() )->state() );
        $deadline = microtime( true ) + 10;
        while ( !is_file( $marker ) && microtime( true ) < $deadline )
            usleep( 100000 );
        $this->assertFileExists( $marker, 'the detached worker ran to its end' );
        $this->assertFileExists( expContentJobStore::path( $job->id(), '.out' ) );
    }

    /** CJ-26 */
    public function testFailedStartFailsTheJobWithTheReasonAndGivesBackItsLock()
    {
        $job = $this->create( '/1/2/42/', 10 );
        expContentJob::$spawnArgv = function ( $job ) {
            return array( '/nonexistent/php-binary', 'x' );
        };
        try
        {
            $this->assertFalse( $job->spawn() );
        }
        finally
        {
            expContentJob::$spawnArgv = null;
        }
        $failed = expContentJob::fetch( $job->id() );
        $this->assertSame( 'failed', $failed->state() );
        $this->assertStringContainsString( 'could not be started', $failed->error() );
        $this->assertStringContainsString( 'exit code', $failed->error(), 'the real reason, not a generic message' );
        $this->assertStringContainsString( 'php-binary', $failed->error(), 'the shell error names the command' );
        $this->assertFalse( expContentJobLock::check( '/1/2/42/' ), 'nothing was done: the lock is given back' );

        // resume takes the lock again; with another job on the subtree meanwhile it is refused
        $other = $this->create( '/1/2/42/7/', 1 );
        $this->assertFalse( $failed->resume( false ), 'the subtree is taken by another job' );
        $this->assertTrue( $other->cancel() );
        $this->assertTrue( expContentJob::fetch( $job->id() )->resume( false ) );
        $this->assertSame( $job->id(), expContentJobLock::check( '/1/2/42/' )->id() );
        $this->assertSame( 0, $this->runForked( $job->id() ) );
        $this->assertSame( array( '1' => 10 ), $this->counts() );
    }

    /** CJ-27 */
    public function testUnstartedJobExpiresAfterQueuedTimeout()
    {
        expContentJob::$settings['QueuedTimeout'] = 300;
        $job = $this->create( '/1/2/42/', 10 );
        $this->assertSame( $job->id(), expContentJobLock::check( '/1/2/42/' )->id() );
        expContentJobStore::update( $job->id(), function ( array &$d ) { $d['heartbeat'] = time() - 299; } );
        $this->assertSame( 'queued', expContentJob::fetch( $job->id() )->state(), 'not yet' );
        expContentJobStore::update( $job->id(), function ( array &$d ) { $d['heartbeat'] = time() - 301; } );
        // a new job on the subtree is not refused by the expired one
        $new = $this->create( '/1/2/42/9/', 1 );
        $this->assertSame( 'queued', $new->state() );
        $this->assertFalse( expContentJobLock::check( '/1/2/43/' ) );
        $this->assertSame( $new->id(), expContentJobLock::check( '/1/2/42/' )->id(), 'only the new job holds a lock now' );
        $old = expContentJob::fetch( $job->id() );
        $this->assertSame( 'failed', $old->state() );
        $this->assertStringContainsString( 'within 300 seconds', $old->error() );
        // with a worker holding the run lock, it is not expired however old
        expContentJob::$settings['QueuedTimeout'] = 1;
        expContentJobStore::update( $new->id(), function ( array &$d ) { $d['heartbeat'] = time() - 10; } );
        $fh = fopen( expContentJobStore::path( $new->id(), '.run.lock' ), 'c' );
        flock( $fh, LOCK_EX );
        $this->assertSame( 'queued', expContentJob::fetch( $new->id() )->state() );
        flock( $fh, LOCK_UN );
        fclose( $fh );
        expContentJob::$settings['QueuedTimeout'] = 0;
        $this->assertSame( 'queued', expContentJob::fetch( $new->id() )->state(), '0 = never' );
    }

    /** CJ-20 */
    public function testFilesCreatedAsRootBelongToTheDirectoryOwner()
    {
        if ( !function_exists( 'posix_geteuid' ) || posix_geteuid() !== 0 )
            $this->markTestSkipped( 'runs as root only' );
        $owner = fileowner( dirname( $this->dir ) );
        if ( $owner === 0 )
            $this->markTestSkipped( 'the scratch directory belongs to root' );
        $job = $this->create( '/1/2/42/', 4 );
        $this->assertSame( 0, $this->runForked( $job->id() ) );
        $files = glob( $this->dir . '/jobs/{,.}*', GLOB_BRACE );
        $checked = 0;
        foreach ( array_merge( array( $this->dir . '/jobs' ), $files ) as $f )
        {
            if ( basename( $f ) === '.' || basename( $f ) === '..' )
                continue;
            $this->assertSame( $owner, fileowner( $f ), basename( $f ) . ' belongs to the site user' );
            $checked++;
        }
        $this->assertGreaterThanOrEqual( 6, $checked );
    }

    /** @return int[] the pauses the test worker was asked for after a busy database, in seconds */
    private function pauses()
    {
        $file = $this->dir . '/pauses';
        return is_file( $file ) ? array_map( 'intval', file( $file, FILE_IGNORE_NEW_LINES ) ) : array();
    }

    /** Ages the job's heartbeat (the failed job's grace before the cronjob part resumes it) */
    private function age( $id, $seconds = 61 )
    {
        expContentJobStore::update( $id, function ( array &$d ) use ( $seconds ) { $d['heartbeat'] = time() - $seconds; } );
    }

    /** CJ-28 */
    public function testBusyDatabaseIsRetriedAndEveryItemDoneOnce()
    {
        expContentJob::$settings['BusyRetries'] = 3;
        expContentJob::$settings['BusyBackoffMax'] = 60;
        // the start of batch 1's transaction twice, a statement inside batch 3 once (after writing two items)
        ezpTestContentJobWorker::$busyAtBegin = array( 1 => 2 );
        ezpTestContentJobType::$busyInBatch = array( 3 => 1 );
        $job = $this->create( '/1/2/42/', 10 );
        $this->assertSame( 0, $this->runForked( $job->id() ) );
        $job = expContentJob::fetch( $job->id() );
        $this->assertSame( 'done', $job->state() );
        $this->assertSame( array( '1' => 10 ), $this->counts(), 'every item exactly once: the half-written try was rolled back' );
        $p = $job->progress();
        $this->assertSame( array( 10, 10, 4 ), array( $p['done'], $p['total'], $p['batch'] ) );
        $this->assertSame( array( 1, 2, 1 ), $this->pauses(), 'the pause doubles per try of a batch and starts again for the next one' );
        $cp = $job->checkpoint();
        $this->assertSame( 4, $cp['batches_seen'], 'the tries that were rolled back left nothing in the checkpoint' );
        $this->assertSame( 4, $cp['runs'], 'prepare() ran again before each retry, as on a resume' );
        $log = $job->log( 100 );
        $this->assertCount( 3, preg_grep( '/the database is busy .* rolled back, try \d of 4 in \d+ s from the last checkpoint/', $log ) );
        $this->assertCount( 1, preg_grep( '/batch 1: committed at try 3/', $log ) );
        $this->assertCount( 1, preg_grep( '/batch 3: committed at try 2/', $log ) );
        $this->assertSame( 1, $job->attempts(), 'one worker did it all' );
        $this->assertFalse( expContentJobLock::check( '/1/2/42/' ) );

        // the pause: 1, 2, 4 ... seconds up to BusyBackoffMax
        expContentJob::$settings['BusyBackoffMax'] = 5;
        $this->assertSame( array( 1, 2, 4, 5, 5 ), array_map( array( 'expContentJobWorker', 'busyPause' ), array( 1, 2, 3, 4, 5 ) ) );
        expContentJob::$settings['BusyBackoffMax'] = 0;
        $this->assertSame( 0, expContentJobWorker::busyPause( 3 ) );

        // what counts as temporary
        foreach ( array( new eZDBException( 'x', 5 ), new eZDBException( 'x', 6 ), new eZDBException( 'Lock wait timeout exceeded; try restarting transaction', 1205 ),
                         new eZDBException( 'Deadlock found when trying to get lock', 1213 ), new RuntimeException( 'database is locked' ),
                         new RuntimeException( 'wrapped', 0, new eZDBException( 'database is busy: ...', 5 ) ) ) as $e )
            $this->assertTrue( expContentJobWorker::isTemporaryError( $e ), $e->getMessage() );
        foreach ( array( new eZDBException( 'UNIQUE constraint failed', 19 ), new eZDBException( 'Duplicate entry', 1062 ),
                         new expContentJobException( 'simulated database error' ) ) as $e )
            $this->assertFalse( expContentJobWorker::isTemporaryError( $e ), $e->getMessage() );
    }

    /** CJ-29 */
    public function testRetriesRunOutAndTheJobFailsWithTheTemporaryFlag()
    {
        expContentJob::$settings['BusyRetries'] = 2;
        ezpTestContentJobType::$busyInBatch = array( 2 => 99 );
        $job = $this->create( '/1/2/42/', 10 );
        $this->assertSame( 1, $this->runForked( $job->id() ) );
        $job = expContentJob::fetch( $job->id() );
        $this->assertSame( 'failed', $job->state() );
        $this->assertTrue( $job->temporaryError() );
        $this->assertStringContainsString( 'the database stayed busy (3 tries): database is locked', $job->error() );
        $this->assertSame( array( '1' => 3, '0' => 7 ), $this->counts(), 'batch 1 done, nothing of the three tries of batch 2' );
        $this->assertSame( 1, $job->checkpoint()['batches_seen'], 'the failed job keeps the checkpoint of the last batch done' );
        $this->assertSame( array( 1, 2 ), $this->pauses() );
        $this->assertTrue( $job->isStalled() );
        $this->assertSame( $job->id(), expContentJobLock::check( '/1/2/42/' )->id(), 'a failed job keeps its lock' );
        $this->assertFalse( $job->autoResumable(), 'not within QueuedGrace of the failure' );
        $this->age( $job->id() );
        $this->assertTrue( expContentJob::fetch( $job->id() )->autoResumable() );

        // a failure that is not temporary is not flagged, so it waits for a person
        ezpTestContentJobDB::reset( 10 );
        ezpTestContentJobType::$busyInBatch = array();
        ezpTestContentJobType::$failAtBatch = 2;
        $other = $this->create( '/1/2/43/', 10 );
        $this->assertSame( 1, $this->runForked( $other->id() ) );
        $other = expContentJob::fetch( $other->id() );
        $this->assertFalse( $other->temporaryError() );
        $this->age( $other->id() );
        $this->assertFalse( expContentJob::fetch( $other->id() )->autoResumable() );
        $this->assertFalse( expContentJob::fetch( $other->id() )->isStalled() );

        // an error outside every try of the run (here: while clearing the caches after a batch) fails the job with
        // the reason, instead of ending the process with the job 'running' and no worker
        ezpTestContentJobType::$failAtBatch = 0;
        ezpTestContentJobWorker::$throwInClearCaches = true;
        $third = $this->create( '/1/2/44/', 10 );
        $this->assertSame( 1, $this->runForked( $third->id() ) );
        $third = expContentJob::fetch( $third->id() );
        $this->assertSame( 'failed', $third->state() );
        $this->assertStringContainsString( 'the worker stopped: simulated error outside the batch', $third->error() );
        $this->assertFalse( $third->temporaryError() );
    }

    /** CJ-30 */
    public function testCronResumesATemporaryFailureUpToMaxAttemptsCountedFromTheLastBatch()
    {
        expContentJob::$settings['MaxAttempts'] = 2;
        expContentJob::$settings['BusyRetries'] = 0;
        ezpTestContentJobWorker::$busyAtBegin = array( 1 => 99 );
        $job = $this->create( '/1/2/42/', 10 );
        $id = $job->id();
        $this->assertSame( 1, $this->runForked( $id ) );
        $this->assertTrue( expContentJob::fetch( $id )->temporaryError() );
        $this->assertSame( 0, \Exponential\Cronjob\Kernel\Contentjobs::resumeStale( null, false ), 'not within QueuedGrace' );
        for ( $i = 1; $i <= 2; $i++ )
        {
            $this->age( $id );
            $this->assertSame( 1, \Exponential\Cronjob\Kernel\Contentjobs::resumeStale( null, false ), "automatic resume $i" );
            $resumed = expContentJob::fetch( $id );
            $this->assertSame( 'queued', $resumed->state() );
            $this->assertSame( $i, $resumed->autoResumes() );
            $this->assertNotEmpty( preg_grep( "/resumed automatically \\($i of 2\\)/", $resumed->log( 50 ) ) );
            $this->assertSame( 1, $this->runForked( $id ) );
        }
        $this->age( $id );
        $this->assertSame( 0, \Exponential\Cronjob\Kernel\Contentjobs::resumeStale( null, false ), 'MaxAttempts automatic resumes in a row: it waits for a person' );
        $this->assertSame( 'failed', expContentJob::fetch( $id )->state() );
        $this->assertSame( array( '0' => 10 ), $this->counts() );

        // a person resumes it (the count starts again); batch 1 goes through, batch 2 meets the busy database
        $this->assertTrue( expContentJob::fetch( $id )->resume( false ) );
        $this->assertSame( 0, expContentJob::fetch( $id )->autoResumes() );
        ezpTestContentJobWorker::$busyAtBegin = array( 2 => 99 );
        $this->age( $id );
        $this->assertSame( 1, $this->runForked( $id ) );
        $this->age( $id );
        $this->assertSame( 1, \Exponential\Cronjob\Kernel\Contentjobs::resumeStale( null, false ) );
        $this->assertSame( 1, $this->runForked( $id ), 'batch 2 is still busy' );
        $this->assertSame( 1, expContentJob::fetch( $id )->autoResumes(), 'a run without a batch done keeps the count' );
        // batch 2 goes through now, batch 3 is busy: the batch done sets the count back, so the cronjob part goes on
        ezpTestContentJobWorker::$busyAtBegin = array( 3 => 99 );
        $this->age( $id );
        $this->assertSame( 1, \Exponential\Cronjob\Kernel\Contentjobs::resumeStale( null, false ) );
        $this->assertSame( 2, expContentJob::fetch( $id )->autoResumes() );
        $this->assertSame( 1, $this->runForked( $id ) );
        $this->assertSame( 0, expContentJob::fetch( $id )->autoResumes(), 'counted again from 0 after a batch done' );
        $this->assertSame( 2, expContentJob::fetch( $id )->progress()['batch'] );
        $this->age( $id );
        $this->assertSame( 1, \Exponential\Cronjob\Kernel\Contentjobs::resumeStale( null, false ) );
        ezpTestContentJobWorker::$busyAtBegin = array();
        $this->assertSame( 0, $this->runForked( $id ) );
        $this->assertSame( 'done', expContentJob::fetch( $id )->state() );
        $this->assertSame( array( '1' => 10 ), $this->counts() );
    }

    /** CJ-31 */
    public function testBusyRunEndsAsAnUninterruptedRun()
    {
        // the uninterrupted run
        $ref = $this->create( '/1/2/90/', 10 );
        $this->assertSame( 0, $this->runForked( $ref->id() ) );
        $refState = ezpTestContentJobDB::read();
        $ref = expContentJob::fetch( $ref->id() );

        // the same job meets a busy database at the start of batch 1, twice inside batch 2, and inside batch 3 more
        // often than BusyRetries allows: it fails, the cronjob part resumes it, the resumed worker finishes it
        ezpTestContentJobDB::reset( 10 );
        expContentJob::$settings['BusyRetries'] = 2;
        ezpTestContentJobWorker::$busyAtBegin = array( 1 => 1 );
        ezpTestContentJobType::$busyInBatch = array( 2 => 2, 3 => 3 );
        $job = $this->create( '/1/2/91/', 10 );
        $this->assertSame( 1, $this->runForked( $job->id() ) );
        $failed = expContentJob::fetch( $job->id() );
        $this->assertSame( 'failed', $failed->state() );
        $this->assertTrue( $failed->temporaryError() );
        $this->assertSame( 2, $failed->progress()['batch'] );
        $this->age( $job->id() );
        $this->assertSame( 1, \Exponential\Cronjob\Kernel\Contentjobs::resumeStale( null, false ) );
        // the database is free again
        ezpTestContentJobWorker::$busyAtBegin = array();
        ezpTestContentJobType::$busyInBatch = array();
        $this->assertSame( 0, $this->runForked( $job->id() ) );
        $done = expContentJob::fetch( $job->id() );
        $this->assertSame( 'done', $done->state() );
        $this->assertSame( $refState, ezpTestContentJobDB::read(), 'every item exactly once, as in the uninterrupted run' );
        $this->assertSame( $ref->progress()['done'], $done->progress()['done'] );
        $this->assertSame( $ref->progress()['batch'], $done->progress()['batch'] );
        $this->assertSame( $ref->checkpoint()['batches_seen'], $done->checkpoint()['batches_seen'] );
        $this->assertSame( $ref->result(), $done->result() );
        $this->assertFalse( $done->temporaryError() );
        $this->assertSame( 0, $done->autoResumes() );
        $this->assertFalse( expContentJobLock::check( '/1/2/91/' ) );
    }

    /**
     * CJ-32: on a real SQLite database (a file in the test's scratch directory) a second connection holds the write
     * lock (BEGIN IMMEDIATE) for 2.5 s; with SQLiteTransactionWait=1 the worker's first starts give up with "database
     * is busy", it waits, tries again and finishes, every item recorded once.
     */
    public function testRealSqliteWriteLockHeldByAnotherConnectionIsWaitedOut()
    {
        if ( !extension_loaded( 'sqlite3' ) || !function_exists( 'pcntl_fork' ) )
            $this->markTestSkipped( 'needs sqlite3 and pcntl' );
        $file = $this->dir . '/busy.sqlite';
        $setup = new SQLite3( $file );
        $setup->exec( 'PRAGMA journal_mode=WAL' );
        $setup->exec( 'CREATE TABLE processed ( item INTEGER NOT NULL, pid INTEGER NOT NULL )' );
        $setup->close();
        expContentJob::$settings['JobTypes'] = array( 'test' => 'ezpTestContentJobSqliteType' );
        expContentJob::$settings['BusyRetries'] = 6;
        expContentJob::$settings['BusyBackoffMax'] = 1;
        $job = $this->create( '/1/2/42/', 10 );
        $id = $job->id();

        $held = $this->dir . '/held';
        $holder = self::fork( function () use ( $file, $held )
        {
            $other = new SQLite3( $file );
            $other->busyTimeout( 5000 );
            $other->exec( 'BEGIN IMMEDIATE' );
            touch( $held );
            usleep( 2500000 );
            $other->exec( 'COMMIT' );
            $other->close();
        } );
        $deadline = microtime( true ) + 10;
        while ( !is_file( $held ) && microtime( true ) < $deadline )
            usleep( 10000 );
        $this->assertFileExists( $held, 'the second connection holds the write lock' );

        $codeFile = $this->dir . '/exit-code';
        $worker = self::fork( function () use ( $id, $file, $codeFile )
        {
            ezpINIHelper::setINISetting( 'site.ini', 'DatabaseSettings', 'SQLiteTransactionWait', '1' );
            ezpINIHelper::setINISetting( 'site.ini', 'DatabaseSettings', 'Transactions', 'enabled' );
            $db = new eZSQLite3DB( array(
                'server' => '', 'port' => '', 'user' => '', 'password' => '', 'database' => $file,
                'use_slave_server' => false, 'slave_server' => '', 'slave_port' => '', 'slave_user' => '', 'slave_password' => '',
                'slave_database' => '', 'socket' => false, 'charset' => 'utf-8', 'is_internal_charset' => true,
                'builtin_encoding' => false, 'connect_retries' => 0, 'use_persistent_connection' => 'disabled' ) );
            eZDB::setInstance( $db );
            $code = ( new ezpTestContentJobSqliteWorker() )->run( $id );
            $db->close();
            file_put_contents( $codeFile, (string) $code );
        } );
        self::waitFor( array( $holder, $worker ), 60 );
        $this->assertFileExists( $codeFile, 'the worker returned' );
        $this->assertSame( '0', file_get_contents( $codeFile ) );

        $job = expContentJob::fetch( $id );
        $this->assertSame( 'done', $job->state(), implode( "\n", $job->log( 30 ) ) );
        $log = $job->log( 100 );
        $this->assertNotEmpty( preg_grep( '/batch 1: the database is busy \(database is busy: the transaction could not start within 1 s/', $log ),
                               implode( "\n", $log ) );
        $this->assertNotEmpty( preg_grep( '/batch 1: committed at try \d/', $log ) );
        $check = new SQLite3( $file );
        $counts = array();
        $r = $check->query( 'SELECT item, COUNT(*) AS c FROM processed GROUP BY item ORDER BY item' );
        while ( $row = $r->fetchArray( SQLITE3_ASSOC ) )
            $counts[(int) $row['item']] = (int) $row['c'];
        $check->close();
        $this->assertSame( array_fill_keys( range( 1, 10 ), 1 ), $counts, 'every item recorded exactly once' );
        $this->assertSame( 10, $job->progress()['done'] );
    }
}
