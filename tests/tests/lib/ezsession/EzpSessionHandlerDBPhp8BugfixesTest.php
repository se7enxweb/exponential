<?php
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * PHPUnit 10 tests for the PHP 8 compatibility bugfixes applied to
 * ezpSessionHandlerDB (lib/ezsession/classes/ezpsessionhandlerdb.php).
 *
 * Three bugs are covered:
 *
 *   Bug1 — read() must return '' (empty string) on failure, not false.
 *           PHP 8 enforces the SessionHandlerInterface contract strictly;
 *           returning false causes a recursive session restart.
 *
 *   Bug2 — gc() WHERE clause used $maxLifeTime (a duration in seconds from
 *           PHP's automatic GC, e.g. 1440) instead of time().
 *           ezsession.expiration_time is an absolute Unix timestamp, so
 *           'expiration_time < 1440' never matched any row.
 *
 *   Bug3 — gc() timeout guard computed elapsed time as ($stopTime - $maxLifeTime)
 *           which is meaningless / wrong. A dedicated $gcStartTime = time()
 *           is now captured before the loop.
 *
 * These tests use lightweight hand-rolled stubs for all eZ kernel dependencies
 * (eZDB, eZINI, eZSession, ezpEvent) so no live database or eZ kernel boot is
 * required. The stubs live in a separate file and are loaded only in the test
 * process so they do not shadow the real kernel classes for other suites.
 *
 * @group ezsession
 * @package tests
 */

/**
 * @covers ezpSessionHandlerDB
 */
#[RunTestsInSeparateProcesses]
class EzpSessionHandlerDBPhp8BugfixesTest extends \PHPUnit\Framework\TestCase
{
    private ezpSessionHandlerDB $handler;
    private StubSessionEZDB $db;

    public static function setUpBeforeClass(): void
    {
        // Load the stubs and the classes under test inside the isolated process.
        // ezpsessionhandler.php has zero external eZ dependencies; load it first
        // so that the DB handler (which extends it) can resolve its parent class.
        require_once __DIR__ . '/../../../../lib/ezsession/classes/ezpsessionhandler.php';
        require_once __DIR__ . '/../../../../lib/ezsession/classes/ezpsessionhandlerdb.php';
        require_once __DIR__ . '/EzpSessionHandlerDBPhp8BugfixesTestStubs.php';
    }

    protected function setUp(): void
    {
        $this->db      = new StubSessionEZDB( connected: true, queryResult: false );
        eZDB::setInstance( $this->db );
        eZSession::setUserID( 0 );
        eZINI::instance()->setVariable( 'Session', 'SessionTimeout', 3600 );
        // handler with $userHasCookie = false so reads always short-circuit
        // to the "no-cookie" path and return false/'' without a DB query.
        $this->handler = new ezpSessionHandlerDB( false );
    }

    // =========================================================================
    // Bug 1 — read() must return '' not false
    // =========================================================================

    /**
     * @test
     * Bug1a: read() returns '' (not false) when the DB is not connected.
     *
     * PHP 8 SessionHandlerInterface::read() must return string|false where
     * false signals a fatal error; '' signals "no data" and allows the session
     * to start cleanly. The pre-patch code returned false in both failure paths.
     */
    public function testReadReturnsEmptyStringWhenDatabaseNotConnected(): void
    {
        $this->db->connected = false;
        $handler = new ezpSessionHandlerDB( true ); // userHasCookie=true to reach DB path

        $result = $handler->read( 'test-session-id' );

        $this->assertSame(
            '',
            $result,
            'read() must return empty string (not false) when DB is not connected (PHP 8 requirement)'
        );
        $this->assertNotFalse(
            $result,
            'read() must NOT return false — PHP 8 rejects false from session read handlers'
        );
    }

    /**
     * @test
     * Bug1b: read() returns '' (not false) when no session row is found.
     *
     * arrayQuery returns false when no rows match. The pre-patch code then
     * returned false from read(), causing PHP 8 to fail the session start.
     */
    public function testReadReturnsEmptyStringWhenNoSessionRowFound(): void
    {
        // DB connected but query returns no rows (false = zero results)
        $this->db->connected   = true;
        $this->db->queryResult = false;
        $handler = new ezpSessionHandlerDB( true ); // userHasCookie=true

        $result = $handler->read( 'nonexistent-session-id' );

        $this->assertSame(
            '',
            $result,
            'read() must return empty string (not false) when no session record exists in DB'
        );
        $this->assertNotFalse(
            $result,
            'read() must NOT return false — returning false causes recursive session restart in PHP 8'
        );
    }

    /**
     * @test
     * Bug1c: read() returns the data string when a session row IS found.
     * (Regression guard — the happy path must be unchanged.)
     */
    public function testReadReturnsSessionDataWhenRowFound(): void
    {
        $sessionData = 'serialized|session:data:here';
        $now         = time();
        $this->db->connected   = true;
        $this->db->queryResult = [
            [
                'data'            => $sessionData,
                'user_id'         => 42,
                'expiration_time' => $now + 3600,
            ]
        ];
        $handler = new ezpSessionHandlerDB( true ); // userHasCookie=true

        $result = $handler->read( 'existing-session-id' );

        $this->assertSame(
            $sessionData,
            $result,
            'read() must return session data string when a matching row is found'
        );
        $this->assertSame( 42, eZSession::userID() );
    }

    /**
     * @test
     * Bug1d: read() return type is always string (never false) on both failure paths.
     */
    public function testReadReturnTypeIsAlwaysString(): void
    {
        // Path 1: DB not connected
        $this->db->connected = false;
        $handler = new ezpSessionHandlerDB( true );
        $this->assertIsString( $handler->read( 'sid1' ), 'return must be string when DB disconnected' );

        // Path 2: DB connected, no row
        $this->db->connected   = true;
        $this->db->queryResult = false;
        $handler2 = new ezpSessionHandlerDB( true );
        $this->assertIsString( $handler2->read( 'sid2' ), 'return must be string when no row found' );
    }

    // =========================================================================
    // Bug 2 — gc() WHERE clause must use time() not $maxLifeTime
    // =========================================================================

    /**
     * @test
     * Bug2a: The iterating gc() path queries with 'expiration_time < <unix_timestamp>'
     * not 'expiration_time < <duration_seconds>'.
     *
     * PHP's automatic GC calls gc($maxLifeTime) where $maxLifeTime is the value of
     * session.gc_maxlifetime (e.g. 1440 seconds). ezsession stores expiration_time
     * as an absolute Unix timestamp (time() + SessionTimeout ~= 1700000000+).
     * 'expiration_time < 1440' would never match — the fix uses time().
     */
    public function testGcIteratingPathUsesCurrentTimestampNotMaxLifetime(): void
    {
        // gcSessionsPrIteration > 0 → iterating path; return no rows so loop exits.
        $this->db->queryResult = false; // arrayQuery returns false → no rows → loop exits
        $this->handler->gcSessionsPrIteration = 50;

        $beforeCall = time();
        $this->handler->gc( 1440 ); // 1440 = typical session.gc_maxlifetime in seconds
        $afterCall  = time();

        $this->assertNotEmpty( $this->db->selectQueries, 'gc() must issue at least one SELECT query' );

        $query = $this->db->selectQueries[0];

        // Extract the numeric value after 'expiration_time < '
        preg_match( '/expiration_time < (\d+)/', $query, $m );
        $this->assertNotEmpty( $m, "Could not find 'expiration_time < <number>' in query: $query" );

        $threshold = (int) $m[1];

        // The threshold must be a Unix timestamp (>> 1440), not a duration.
        $this->assertGreaterThan(
            $beforeCall - 1,
            $threshold,
            'gc() WHERE threshold must be >= current Unix timestamp, not a duration like 1440'
        );
        $this->assertLessThanOrEqual(
            $afterCall,
            $threshold,
            'gc() WHERE threshold must be <= time() at time of call'
        );
        $this->assertGreaterThan(
            100000,
            $threshold,
            'gc() WHERE threshold must be a Unix timestamp (>> 1440); got ' . $threshold
        );
    }

    /**
     * @test
     * Bug2b: The non-iterating gc() path (gcSessionsPrIteration = false) also
     * uses time() in the DELETE query, not $maxLifeTime.
     */
    public function testGcNonIteratingPathUsesCurrentTimestampNotMaxLifetime(): void
    {
        $this->handler->gcSessionsPrIteration = false;

        $beforeCall = time();
        $this->handler->gc( 1440 );
        $afterCall  = time();

        $this->assertNotEmpty( $this->db->deleteQueries, 'gc() non-iterating must issue a DELETE query' );

        $query = $this->db->deleteQueries[0];
        preg_match( '/expiration_time < (\d+)/', $query, $m );
        $this->assertNotEmpty( $m, "Could not find 'expiration_time < <number>' in DELETE query: $query" );

        $threshold = (int) $m[1];
        $this->assertGreaterThan(
            $beforeCall - 1,
            $threshold,
            'gc() non-iterating DELETE threshold must be >= current Unix timestamp'
        );
        $this->assertGreaterThan(
            100000,
            $threshold,
            'gc() non-iterating threshold must be Unix timestamp, not duration 1440; got ' . $threshold
        );
    }

    /**
     * @test
     * Bug2c: $maxLifeTime = 1440 must never appear literally in the WHERE clause.
     */
    public function testGcDoesNotUseDurationLiteralInWhereClause(): void
    {
        $this->db->queryResult = false;
        $this->handler->gcSessionsPrIteration = 50;
        $this->handler->gc( 1440 );

        foreach ( $this->db->selectQueries as $q )
        {
            $this->assertStringNotContainsString(
                'expiration_time < 1440',
                $q,
                'gc() must not compare expiration_time against 1440 (a duration, not a timestamp)'
            );
        }
    }

    // =========================================================================
    // Bug 3 — gc() timeout guard must use $gcStartTime not $maxLifeTime
    // =========================================================================

    /**
     * @test
     * Bug3a: gc() completes without timing out when there is ample max_execution_time.
     *
     * This is primarily a regression guard for the timeout calculation. With the
     * old code ($stopTime - $maxLifeTime), when $maxLifeTime is 1440 the subtraction
     * produces a result like (time() - 1440) ≈ 1700000000 which far exceeds any
     * $maxExecutionTime, instantly marking every GC run as timed-out.
     *
     * With the fix ($stopTime - $gcStartTime), elapsed time is near zero for the
     * first iteration, so the timeout guard does not fire prematurely.
     */
    public function testGcDoesNotPrematurelyTimeOutWithReasonableMaxLifetime(): void
    {
        // Set a high execution time via ini so the timeout guard has plenty of room.
        $old = ini_set( 'max_execution_time', '300' );

        // One iteration returns rows, second returns nothing (loop exits).
        $callCount = 0;
        $db = $this->createStub( StubSessionEZDB::class );

        $db->method( 'isConnected' )->willReturn( true );
        $db->method( 'escapeString' )->willReturnArgument( 0 );
        $db->method( 'query' )->willReturn( true );
        $db->method( 'arrayQuery' )->willReturnCallback( function() use ( &$callCount ) {
            $callCount++;
            // First call: return one row; second call: return nothing → loop exits.
            return $callCount === 1 ? [ 'session-key-abc' ] : false;
        } );

        eZDB::setInstance( $db );
        $handler = new ezpSessionHandlerDB( false );
        $handler->gcSessionsPrIteration = 50;

        $result = $handler->gc( 1440 );

        // GC must report it completed (not timed out).
        $this->assertTrue( $result, 'gc() must return true (completed) when there is ample execution time left' );

        ini_set( 'max_execution_time', $old );
    }

    /**
     * @test
     * Bug3b: With the old code, $remaningTime = $maxExecutionTime - GC_TIMEOUT_MARGIN
     *         - (time() - 1440) was astronomically negative (≈ -1700000000), so
     *         the timeout guard always fired after the first iteration.
     *         With the fix, elapsed time is ~0, so $remaningTime ≈ 295 (positive).
     *
     * We verify this by checking that gc() actually processes a second batch of
     * rows rather than bailing out after the first one.
     */
    public function testGcTimeoutGuardUsesElapsedTimeNotMaxLifetime(): void
    {
        ini_set( 'max_execution_time', '300' );

        $callCount = 0;
        $db = $this->createStub( StubSessionEZDB::class );
        $db->method( 'isConnected' )->willReturn( true );
        $db->method( 'escapeString' )->willReturnArgument( 0 );
        $db->method( 'query' )->willReturn( true );
        $db->method( 'arrayQuery' )->willReturnCallback( function() use ( &$callCount ) {
            $callCount++;
            if ( $callCount <= 2 )
                return [ 'session-key-' . $callCount ];
            return false; // exit loop on 3rd call
        } );

        eZDB::setInstance( $db );
        $handler = new ezpSessionHandlerDB( false );
        $handler->gcSessionsPrIteration = 1;

        $handler->gc( 1440 );

        // With the old buggy code the timeout guard would fire after iteration 1
        // (because remaningTime ≈ -1700000000 < 0 < (stopTime - startTime)).
        // With the fix, it must reach at least iteration 2.
        $this->assertGreaterThanOrEqual(
            2,
            $callCount,
            'gc() must not time-out after the first iteration when max_execution_time is 300s; ' .
            'Bug3 would cause remaningTime ≈ -1.7 billion which triggers a false timeout'
        );
    }

    // =========================================================================
    // Regression guards — unchanged behaviour
    // =========================================================================

    /**
     * @test
     * The non-iterating gc() path (gcSessionsPrIteration = false) must still
     * return true.
     */
    public function testGcNonIteratingPathReturnsTrue(): void
    {
        $this->handler->gcSessionsPrIteration = false;
        $result = $this->handler->gc( 1440 );
        $this->assertTrue( $result );
    }

    /**
     * @test
     * gc() with iterating path and no expired sessions returns true (completed).
     */
    public function testGcIteratingPathReturnsTrueWhenNoExpiredSessions(): void
    {
        $this->db->queryResult = false; // no rows → loop body never executes
        $this->handler->gcSessionsPrIteration = 50;
        $result = $this->handler->gc( 1440 );
        $this->assertTrue( $result );
    }

    /**
     * @test
     * verify the class still implements read/write/destroy/gc/open/close
     * as required by SessionHandlerInterface (PHP 8 strict check).
     */
    public function testHandlerImplementsRequiredMethods(): void
    {
        $this->assertTrue( method_exists( $this->handler, 'read' ) );
        $this->assertTrue( method_exists( $this->handler, 'write' ) );
        $this->assertTrue( method_exists( $this->handler, 'destroy' ) );
        $this->assertTrue( method_exists( $this->handler, 'gc' ) );
    }
}
