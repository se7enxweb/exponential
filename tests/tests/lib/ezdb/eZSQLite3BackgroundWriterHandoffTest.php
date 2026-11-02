<?php
/**
 * Background writers on SQLite give the write lock to a waiting writer between their transactions.
 *
 * A loop that commits and begins its next transaction at once (the indexcontent cronjob part, one transaction per
 * object; a content job's batches) took the driver's writer gate back within 0.2 ms of every commit, long before a
 * waiting writer's next try (every 1-15 ms), and so held SQLite's write lock for minutes in short transactions. On
 * the demo (2026-11-02, a help manuals content job queuing objects for indexing and runcronjobs.php's indexcontent
 * indexing them) two such loops passed the lock between them and a user's save failed after its 25 s wait with
 * "database is busy". A writer that finds the lock free but others waiting now lets them go first.
 *
 * Forked processes run the loops on a SQLite file in var/tmp; this process is the page that saves.
 *
 *  BWH-01  behind one background loop of back-to-back transactions, a writer's transaction starts within 2 s
 *  BWH-02  behind two such loops (the demo's cron part and content job), it starts within 3 s, and both loops go on
 *          committing (neither starves the other)
 *  BWH-03  a writer alone sees nobody waiting (no hand-off pause), and a waiting writer is seen
 *  BWH-04  indexcontent indexes a moved subtree in one transaction per object, not inside the moved object's
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class eZSQLite3BackgroundWriterHandoffTest extends PHPUnit\Framework\TestCase
{
    /** How long a background loop runs, in seconds: well above the waits asserted below */
    const LOOP_SECONDS = 6;

    /** How long one of its transactions holds the lock, in ms (an object's indexing on the demo: 0.1 - 2.4 s) */
    const TRANSACTION_MS = 80;

    /** @var string */
    private $file;

    /** @var int[] */
    private $children = array();

    private static function root()
    {
        return dirname( __DIR__, 4 );
    }

    private function connect()
    {
        $db = new eZSQLite3DB( array(
            'server' => '', 'port' => '', 'user' => '', 'password' => '', 'database' => $this->file,
            'use_slave_server' => false, 'slave_server' => '', 'slave_port' => '', 'slave_user' => '', 'slave_password' => '',
            'slave_database' => '', 'socket' => false, 'charset' => 'utf-8', 'is_internal_charset' => true,
            'builtin_encoding' => false, 'connect_retries' => 0, 'use_persistent_connection' => 'disabled' ) );
        $this->assertTrue( $db->isConnected(), 'the SQLite file could not be opened' );
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        return $db;
    }

    protected function setUp(): void
    {
        if ( !extension_loaded( 'sqlite3' ) )
            $this->markTestSkipped( 'SQLite3 extension is not loaded' );
        if ( !function_exists( 'pcntl_fork' ) || !function_exists( 'posix_kill' ) )
            $this->markTestSkipped( 'pcntl and posix are needed to run the background writers' );
        $dir = self::root() . '/var/tmp';
        if ( !is_dir( $dir ) )
            mkdir( $dir, 0775, true );
        $this->file = $dir . '/writer-handoff-' . getmypid() . '-' . mt_rand() . '.db';
        $db = $this->connect();
        $db->query( 'CREATE TABLE written ( id INTEGER PRIMARY KEY AUTOINCREMENT, writer VARCHAR(20) NOT NULL )' );
        $db->close();
    }

    protected function tearDown(): void
    {
        foreach ( $this->children as $pid )
        {
            posix_kill( $pid, SIGKILL );
            pcntl_waitpid( $pid, $status );
        }
        $this->children = array();
        foreach ( array( '', '-wal', '-shm', '-journal', '.writer-lock', '.writer-queue' ) as $suffix )
        {
            if ( $this->file && file_exists( $this->file . $suffix ) )
                unlink( $this->file . $suffix );
        }
    }

    /**
     * Starts a process that writes in back-to-back transactions for LOOP_SECONDS, as indexcontent does.
     * The child ends with SIGKILL: no PHPUnit shutdown, no destructors of the parent's objects.
     */
    private function startLoop( $name )
    {
        $pid = pcntl_fork();
        $this->assertNotSame( -1, $pid, 'fork failed' );
        if ( $pid === 0 )
        {
            try
            {
                $db = $this->connect();
                $until = microtime( true ) + self::LOOP_SECONDS;
                while ( microtime( true ) < $until )
                {
                    $db->begin();
                    $db->query( "INSERT INTO written ( writer ) VALUES ( '" . $name . "' )" );
                    usleep( self::TRANSACTION_MS * 1000 );
                    $db->commit();
                }
                $db->close();
            }
            catch ( Throwable $e )
            {
            }
            posix_kill( getmypid(), SIGKILL );
        }
        $this->children[] = $pid;
    }

    /** Waits until every loop has committed once, so they are running when the test writes */
    private function waitForLoops( array $names )
    {
        $db = $this->connect();
        $until = microtime( true ) + 5;
        do
        {
            $seen = array();
            foreach ( $db->arrayQuery( 'SELECT DISTINCT writer FROM written' ) as $row )
                $seen[] = $row['writer'];
            if ( !array_diff( $names, $seen ) )
                break;
            usleep( 20000 );
        } while ( microtime( true ) < $until );
        $db->close();
        $this->assertEmpty( array_diff( $names, $seen ), 'the background writers did not start' );
    }

    /** One transaction of the page, timed from its start to its commit */
    private function pageSave( eZSQLite3DB $db )
    {
        $start = microtime( true );
        $db->begin();
        $db->query( "INSERT INTO written ( writer ) VALUES ( 'page' )" );
        $db->commit();
        return microtime( true ) - $start;
    }

    private function rows( eZSQLite3DB $db, $writer )
    {
        return (int)$db->arrayQuery( "SELECT COUNT(*) AS n FROM written WHERE writer = '" . $writer . "'" )[0]['n'];
    }

    /** BWH-01 */
    public function testAWriterGetsInBetweenTheTransactionsOfABackgroundLoop()
    {
        $this->startLoop( 'cron' );
        $this->waitForLoops( array( 'cron' ) );

        $db = $this->connect();
        $seconds = $this->pageSave( $db );
        $this->assertLessThan( 2.0, $seconds, 'the save waited for the whole background loop' );
        $this->assertSame( 1, $this->rows( $db, 'page' ) );
        $db->close();
    }

    /** BWH-02 */
    public function testAWriterGetsInBetweenTwoBackgroundLoopsAndNeitherLoopStarves()
    {
        $this->startLoop( 'cron' );
        $this->startLoop( 'job' );
        $this->waitForLoops( array( 'cron', 'job' ) );

        $db = $this->connect();
        $longest = 0;
        for ( $i = 0; $i < 3; $i++ )
            $longest = max( $longest, $this->pageSave( $db ) );
        $this->assertLessThan( 3.0, $longest, 'a save waited behind the two background loops' );
        $this->assertSame( 3, $this->rows( $db, 'page' ) );

        $cron = $this->rows( $db, 'cron' );
        $job = $this->rows( $db, 'job' );
        sleep( 1 );
        $this->assertGreaterThan( $cron, $this->rows( $db, 'cron' ), 'the cron loop starved' );
        $this->assertGreaterThan( $job, $this->rows( $db, 'job' ), 'the job loop starved' );
        $db->close();
    }

    /** BWH-03 */
    public function testAWriterAloneDoesNotPause()
    {
        // Not timed (a loaded machine's fsync alone can exceed HANDOFF_MS): what decides the pause is whether the
        // queue file shows a waiting writer, and with nobody waiting it does not.
        $db = $this->connect();
        $waiting = new ReflectionMethod( 'eZSQLite3DB', 'writersWaiting' );
        $queueOf = new ReflectionMethod( 'eZSQLite3DB', 'writerQueue' );
        $queue = $queueOf->invoke( $db );
        $this->assertIsResource( $queue, 'the queue file next to the database could not be opened' );
        $this->assertFalse( $waiting->invoke( $db, $queue ), 'a writer alone saw others waiting' );
        for ( $i = 0; $i < 20; $i++ )
            $this->pageSave( $db );
        $this->assertFalse( $waiting->invoke( $db, $queue ), 'a save left the queue marked as waiting' );
        $this->assertSame( 20, $this->rows( $db, 'page' ) );

        // a writer that waits (holds the shared lock) is seen, and no longer once it is in
        $other = fopen( $this->file . '.writer-queue', 'r' );
        $this->assertTrue( flock( $other, LOCK_SH ) );
        $this->assertTrue( $waiting->invoke( $db, $queue ), 'a waiting writer was not seen' );
        flock( $other, LOCK_UN );
        fclose( $other );
        $this->assertFalse( $waiting->invoke( $db, $queue ) );
        $db->close();
    }

    /** BWH-04 */
    public function testIndexcontentIndexesAMovedSubtreeInShortTransactions()
    {
        $source = file_get_contents( self::root() . '/kernel/private/classes/cronjobs/indexcontent.php' );
        $this->assertNotFalse( $source );
        $this->assertSame( 1, preg_match(
            '/\$db->begin\(\);\s*\$searchEngine->addObject\( \$childObject, false \);\s*\$db->commit\(\);/', $source ),
            'every object of a moved subtree is indexed in its own transaction' );
        $this->assertSame( 1, preg_match(
            "/if \( \\\$object && \\\$action == 'index_moved_node' \)\s*\{\s*\\\$db->commit\(\);/", $source ),
            'the moved object\'s transaction ends before its subtree is indexed' );
    }
}
?>
