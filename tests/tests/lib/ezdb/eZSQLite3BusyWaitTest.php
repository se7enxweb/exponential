<?php
/**
 * Work on SQLite waits for another writer's lock and succeeds, instead of failing at once.
 *
 * On the documentation install the cronjobs (runcronjobs.php frequent and global) stopped with "A database
 * transaction in Exponential failed" after the transaction wait of a web request (25 s) while an import held the
 * write lock, and an INSERT of an external link outside a transaction failed with "database is locked": it waited
 * only in SQLite's busy handler while the import's writers, queued on the driver's writer gate, took the lock back
 * within milliseconds of each commit. Now a single write queues on the same gate, a lock SQLite reports at once is
 * tried again within the wait, the command line waits longer than a web request, and a wait that runs out says the
 * database was busy and for how long.
 *
 * The DB-less cases read the driver and settings/site.ini. The engine cases use a SQLite file in var/tmp and a
 * forked process that holds the write lock.
 *
 *  SBW-01  isWriteStatement() tells writes from reads and transaction control
 *  SBW-02  isBusyCode() covers SQLITE_BUSY and SQLITE_LOCKED with their extended codes, nothing else
 *  SBW-03  settings/site.ini ships the four waits, and the driver's fallbacks equal them
 *  SBW-04  this process (PHPUnit, the command line) gets the command line's wait as its busy_timeout
 *  SBW-05  a single INSERT outside a transaction waits for a lock held by another process and succeeds
 *  SBW-06  ... and fails cleanly after SQLiteCLIBusyTimeout, with "database is busy" and the time waited
 *  SBW-07  a transaction waits for the lock and commits; after SQLiteCLITransactionWait it fails with nothing written
 *  SBW-08  a single INSERT gets in between the back-to-back transactions of a background loop (the import's link
 *          pass) within 2 s
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class eZSQLite3BusyWaitTest extends PHPUnit\Framework\TestCase
{
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
        return $db;
    }

    private function needEngine()
    {
        if ( !extension_loaded( 'sqlite3' ) )
            $this->markTestSkipped( 'SQLite3 extension is not loaded' );
        if ( !function_exists( 'pcntl_fork' ) || !function_exists( 'posix_kill' ) )
            $this->markTestSkipped( 'pcntl and posix are needed for the process that holds the lock' );
        $dir = self::root() . '/var/tmp';
        if ( !is_dir( $dir ) )
            mkdir( $dir, 0775, true );
        $this->file = $dir . '/sqlite-busy-wait-' . getmypid() . '-' . mt_rand() . '.db';
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
        ezpINIHelper::restoreINISettings();
        if ( $this->file )
        {
            foreach ( array( '', '-wal', '-shm', '-journal', '.writer-lock', '.writer-queue', '.held' ) as $suffix )
            {
                if ( file_exists( $this->file . $suffix ) )
                    unlink( $this->file . $suffix );
            }
        }
        $this->file = null;
    }

    /**
     * Starts a process that holds the write lock for $seconds, with a plain SQLite3 connection (outside the
     * driver's gate, as another program would), and waits until it holds it.
     */
    private function holdLock( $seconds )
    {
        $pid = pcntl_fork();
        $this->assertNotSame( -1, $pid, 'fork failed' );
        if ( $pid === 0 )
        {
            try
            {
                $other = new SQLite3( $this->file );
                $other->busyTimeout( 5000 );
                $other->exec( 'BEGIN IMMEDIATE' );
                $other->exec( "INSERT INTO written ( writer ) VALUES ( 'holder' )" );
                touch( $this->file . '.held' );
                usleep( (int)( $seconds * 1000000 ) );
                $other->exec( 'COMMIT' );
                $other->close();
            }
            catch ( Throwable $e )
            {
            }
            posix_kill( getmypid(), SIGKILL );
        }
        $this->children[] = $pid;
        $until = microtime( true ) + 10;
        while ( !is_file( $this->file . '.held' ) && microtime( true ) < $until )
            usleep( 5000 );
        $this->assertFileExists( $this->file . '.held', 'the other process holds the write lock' );
    }

    private function rows( eZSQLite3DB $db, $writer )
    {
        return (int)$db->arrayQuery( "SELECT COUNT(*) AS n FROM written WHERE writer = '" . $writer . "'" )[0]['n'];
    }

    /** SBW-01 */
    public function testWriteStatementsAreToldFromReads()
    {
        foreach ( array( "INSERT INTO ezurl ( url ) VALUES ( 'x' )", "INSERT OR REPLACE INTO ezurl VALUES (1)",
                         'UPDATE a SET b=1', 'DELETE FROM a', 'REPLACE INTO a VALUES (1)', 'CREATE TABLE a ( b INT )',
                         'DROP TABLE a', 'ALTER TABLE a ADD c INT', 'PRAGMA journal_mode = wal;', 'PRAGMA wal_checkpoint(TRUNCATE)',
                         'WITH x AS ( SELECT 1 ) INSERT INTO a SELECT * FROM x', "  -- note\n/* a */ UPDATE a SET b=2", 'VACUUM', 'ANALYZE' ) as $sql )
            $this->assertTrue( eZSQLite3DB::isWriteStatement( $sql ), $sql );
        foreach ( array( 'SELECT 1', ' select * from a', 'EXPLAIN QUERY PLAN SELECT 1', 'PRAGMA journal_mode', 'PRAGMA table_info(a)',
                         'BEGIN IMMEDIATE', 'COMMIT', 'ROLLBACK', 'SAVEPOINT s', 'RELEASE s', "/* x */\n SELECT 2" ) as $sql )
            $this->assertFalse( eZSQLite3DB::isWriteStatement( $sql ), $sql );
        // only the start is read: a long statement costs nothing
        $this->assertTrue( eZSQLite3DB::isWriteStatement( 'INSERT INTO a VALUES (' . str_repeat( "'x',", 500000 ) . "'x')" ) );
    }

    /** SBW-02 */
    public function testBusyCodes()
    {
        foreach ( array( 5, 6, 261 /* BUSY_RECOVERY */, 517 /* BUSY_SNAPSHOT */, 262 /* LOCKED_SHAREDCACHE */ ) as $code )
            $this->assertTrue( eZSQLite3DB::isBusyCode( $code ), (string)$code );
        foreach ( array( 0, 1, 8, 19, 2067 /* CONSTRAINT_UNIQUE */ ) as $code )
            $this->assertFalse( eZSQLite3DB::isBusyCode( $code ), (string)$code );
    }

    /** SBW-03 */
    public function testShippedSettingsAndDriverFallbacksAgree()
    {
        $ini = file_get_contents( self::root() . '/settings/site.ini' );
        $this->assertSame( 1, preg_match( '/^\[DatabaseSettings\]\s*$(.*?)^\[/ms', $ini, $section ) );
        $expected = array( 'SQLiteTransactionWait' => 25, 'SQLiteCLITransactionWait' => 300,
                           'SQLiteBusyTimeout' => 5000, 'SQLiteCLIBusyTimeout' => 30000 );
        foreach ( $expected as $key => $value )
        {
            $this->assertSame( 1, preg_match( '/^' . $key . '=(\d+)\s*$/m', $section[1], $m ), "[DatabaseSettings] sets $key" );
            $this->assertSame( $value, (int)$m[1], $key );
        }
        $source = file_get_contents( self::root() . '/lib/ezdb/classes/ezsqlite3db.php' );
        $this->assertSame( 1, preg_match( '/\$ms = \$web \? (\d+) : (\d+);/', $source, $m ), 'configuredStatementWaitMs() has literal defaults' );
        $this->assertSame( array( 5000, 30000 ), array( (int)$m[1], (int)$m[2] ) );
        $this->assertSame( 1, preg_match( '/function transactionWaitMs\(\)\s*\{\s*\$seconds = (\d+);.*?\$seconds = (\d+);/s', $source, $m ) );
        $this->assertSame( array( 25, 300 ), array( (int)$m[1], (int)$m[2] ) );
    }

    /** SBW-04 */
    public function testTheCommandLineGetsItsWait()
    {
        $this->needEngine();
        $this->assertFalse( eZSQLite3DB::isWebProcess(), 'PHPUnit runs on the command line' );
        $db = $this->connect();
        $this->assertSame( 30000, $db->statementWaitMs() );
        $this->assertSame( 30000, (int)$db->arrayQuery( 'PRAGMA busy_timeout' )[0]['timeout'] );
        $this->assertSame( 'wal', strtolower( $db->arrayQuery( 'PRAGMA journal_mode' )[0]['journal_mode'] ) );
        $db->close();
    }

    /** SBW-05 */
    public function testASingleWriteWaitsForAHeldLockAndSucceeds()
    {
        $this->needEngine();
        $db = $this->connect();
        $this->holdLock( 1.5 );
        $start = microtime( true );
        $this->assertTrue( $db->query( "INSERT INTO written ( writer ) VALUES ( 'single' )" ), (string)$db->errorMessage() );
        $waited = microtime( true ) - $start;
        $this->assertGreaterThan( 0.5, $waited, 'the lock was not held while the statement ran' );
        $this->assertLessThan( 10, $waited );
        $this->assertSame( 1, $this->rows( $db, 'single' ) );
        $this->assertSame( 1, $this->rows( $db, 'holder' ), 'the holder committed first' );
        $db->close();
    }

    /** SBW-06 */
    public function testASingleWriteFailsCleanlyAfterItsWait()
    {
        $this->needEngine();
        ezpINIHelper::setINISetting( 'site.ini', 'DatabaseSettings', 'SQLiteCLIBusyTimeout', '800' );
        $db = $this->connect();
        $this->assertSame( 800, $db->statementWaitMs() );
        $this->holdLock( 4 );
        $start = microtime( true );
        $this->assertFalse( $db->query( "INSERT INTO written ( writer ) VALUES ( 'late' )" ) );
        $waited = microtime( true ) - $start;
        $this->assertGreaterThan( 0.7, $waited, 'it gave up before its wait' );
        $this->assertLessThan( 2.5, $waited, 'it waited far beyond its wait' );
        $this->assertTrue( eZSQLite3DB::isBusyCode( $db->errorNumber() ) );
        $this->assertStringStartsWith( 'database is busy: the statement waited ', $db->errorMessage() );
        $this->assertStringContainsString( 'limit 0.8 s, [DatabaseSettings] SQLiteCLIBusyTimeout', $db->errorMessage() );
        // the wait is back for the next statement
        $this->assertSame( 800, (int)$db->arrayQuery( 'PRAGMA busy_timeout' )[0]['timeout'] );
        $db->close();
    }

    /** SBW-07 */
    public function testATransactionWaitsAndCommitsOrFailsWithNothingWritten()
    {
        $this->needEngine();
        $db = $this->connect();
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        $this->holdLock( 1.5 );
        $db->begin();
        $db->query( "INSERT INTO written ( writer ) VALUES ( 'tx' )" );
        $db->commit();
        $this->assertSame( 1, $this->rows( $db, 'tx' ) );
        $db->close();

        foreach ( $this->children as $pid )
            pcntl_waitpid( $pid, $status );
        $this->children = array();
        unlink( $this->file . '.held' );

        ezpINIHelper::setINISetting( 'site.ini', 'DatabaseSettings', 'SQLiteCLITransactionWait', '1' );
        $db = $this->connect();
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        $this->holdLock( 4 );
        $start = microtime( true );
        try
        {
            $db->begin();
            $this->fail( 'the transaction started while another process held the lock' );
        }
        catch ( eZDBException $e )
        {
            $this->assertStringStartsWith( 'database is busy: the transaction could not start within 1 s (waited ', $e->getMessage() );
            $this->assertStringContainsString( '[DatabaseSettings] SQLiteCLITransactionWait', $e->getMessage() );
            $this->assertStringContainsString( 'nothing was written', $e->getMessage() );
        }
        $waited = microtime( true ) - $start;
        $this->assertGreaterThan( 0.9, $waited );
        $this->assertLessThan( 3, $waited );
        $db->close();
    }

    /** SBW-08 */
    public function testASingleWriteGetsInBetweenABackgroundLoopsTransactions()
    {
        $this->needEngine();
        $pid = pcntl_fork();
        $this->assertNotSame( -1, $pid, 'fork failed' );
        if ( $pid === 0 )
        {
            try
            {
                // The import's link pass: back-to-back transactions on the driver's gate, 80 ms each, for 8 s
                $loop = $this->connect();
                $until = microtime( true ) + 8;
                while ( microtime( true ) < $until )
                {
                    $loop->begin();
                    $loop->query( "INSERT INTO written ( writer ) VALUES ( 'loop' )" );
                    usleep( 80000 );
                    $loop->commit();
                }
                $loop->close();
            }
            catch ( Throwable $e )
            {
            }
            posix_kill( getmypid(), SIGKILL );
        }
        $this->children[] = $pid;

        $db = $this->connect();
        $until = microtime( true ) + 5;
        while ( $this->rows( $db, 'loop' ) < 2 && microtime( true ) < $until )
            usleep( 20000 );
        $this->assertGreaterThanOrEqual( 2, $this->rows( $db, 'loop' ), 'the background loop did not start' );

        for ( $i = 0; $i < 3; $i++ )
        {
            $start = microtime( true );
            $this->assertTrue( $db->query( "INSERT INTO written ( writer ) VALUES ( 'link' )" ), (string)$db->errorMessage() );
            $this->assertLessThan( 2, microtime( true ) - $start, 'the single write stood behind the loop' );
        }
        $this->assertSame( 3, $this->rows( $db, 'link' ) );
        $db->close();
    }
}
