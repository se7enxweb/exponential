<?php
/**
 * File containing the expVelocityWatchdogTest class.
 *
 * The watchdog's decision and one run of it, without a server: an engine that
 * is down is started unless it was stopped on purpose, a restart is under way,
 * the watchdog is paused, a lock is held or it is backing off; one that runs is
 * never touched. The run uses a stand-in engine and a directory of its own.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class expVelocityWatchdogTest extends ezpTestCase
{
    /** @var string */
    private $dir;

    public function setUp(): void
    {
        parent::setUp();
        $this->dir = eZSys::rootDir() . '/var/tmp/velocity-watchdog-test-' . getmypid();
        eZDir::mkdir( $this->dir . '/run', false, true );
        eZDir::mkdir( $this->dir . '/conf-available', false, true );
        eZDir::mkdir( $this->dir . '/conf-enabled', false, true );
        putenv( 'INVOCATION_ID' );
    }

    public function tearDown(): void
    {
        eZDir::recursiveDelete( $this->dir );
        parent::tearDown();
    }

    private function facts( array $over = array() )
    {
        return $over + array( 'running' => false, 'now' => 10000, 'timeout' => 300, 'backoff' => 600 );
    }

    public function testRunningServerIsLeftAlone()
    {
        $d = expVelocityWatchdog::decide( $this->facts( array( 'running' => true,
            'restart' => array( 'started' => 1, 'pid' => 1 ), 'restartPidAlive' => false ) ) );
        $this->assertSame( 'none', $d['action'] );
        $this->assertSame( 'running', $d['reason'] );
    }

    public function testDownServerIsStarted()
    {
        $d = expVelocityWatchdog::decide( $this->facts() );
        $this->assertSame( 'start', $d['action'] );
        $this->assertSame( 'down', $d['reason'] );
    }

    public function testExplicitStopStaysStopped()
    {
        $d = expVelocityWatchdog::decide( $this->facts( array( 'stopped' => array( 'since' => 9000, 'by' => 'stop' ) ) ) );
        $this->assertSame( 'skip', $d['action'] );
        $this->assertSame( 'intentionally-stopped', $d['reason'] );
    }

    public function testExplicitStopWinsOverStaleRestart()
    {
        $d = expVelocityWatchdog::decide( $this->facts( array( 'stopped' => array(), 'restart' => array( 'started' => 1 ) ) ) );
        $this->assertSame( 'intentionally-stopped', $d['reason'] );
    }

    public function testRestartInProgressIsNotFought()
    {
        $d = expVelocityWatchdog::decide( $this->facts( array(
            'restart' => array( 'started' => 9900, 'pid' => 4242 ), 'restartPidAlive' => true ) ) );
        $this->assertSame( 'skip', $d['action'] );
        $this->assertSame( 'restart-in-progress', $d['reason'] );
    }

    public function testRestartWhoseProcessIsGoneIsStale()
    {
        $d = expVelocityWatchdog::decide( $this->facts( array(
            'restart' => array( 'started' => 9990, 'pid' => 4242 ), 'restartPidAlive' => false ) ) );
        $this->assertSame( 'start', $d['action'] );
        $this->assertSame( 'restart-stale', $d['reason'] );
    }

    public function testRestartOlderThanTimeoutIsStale()
    {
        $d = expVelocityWatchdog::decide( $this->facts( array(
            'restart' => array( 'started' => 9000, 'pid' => 4242 ), 'restartPidAlive' => true ) ) );
        $this->assertSame( 'restart-stale', $d['reason'] );
    }

    public function testPausedDoesNothing()
    {
        $d = expVelocityWatchdog::decide( $this->facts( array( 'paused' => true ) ) );
        $this->assertSame( 'paused', $d['reason'] );
    }

    public function testHeldLockWaitsOnlyUntilTimeout()
    {
        $d = expVelocityWatchdog::decide( $this->facts( array( 'lockHeld' => true, 'downSince' => 9900 ) ) );
        $this->assertSame( 'lock-held', $d['reason'] );
        $d = expVelocityWatchdog::decide( $this->facts( array( 'lockHeld' => true, 'downSince' => 9000 ) ) );
        $this->assertSame( 'start', $d['action'] );
    }

    public function testBackoffAfterRepeatedFailures()
    {
        $d = expVelocityWatchdog::decide( $this->facts( array( 'failures' => 3, 'lastAttempt' => 9800 ) ) );
        $this->assertSame( 'backoff', $d['reason'] );
        $d = expVelocityWatchdog::decide( $this->facts( array( 'failures' => 3, 'lastAttempt' => 9000 ) ) );
        $this->assertSame( 'start', $d['action'] );
        $d = expVelocityWatchdog::decide( $this->facts( array( 'failures' => 2, 'lastAttempt' => 9990 ) ) );
        $this->assertSame( 'start', $d['action'] );
    }

    public function testDecisionAgreesWithMarkersShouldStart()
    {
        $now = 10000;
        $cases = array(
            array( false, null, null, false ),
            array( true, null, null, false ),
            array( false, array(), null, false ),
            array( false, null, array( 'started' => 9950, 'pid' => 1 ), true ),
            array( false, null, array( 'started' => 9950, 'pid' => 1 ), false ),
            array( false, null, array( 'started' => 1, 'pid' => 1 ), true ),
        );
        foreach ( $cases as $i => $c )
        {
            $d = expVelocityWatchdog::decide( $this->facts( array( 'running' => $c[0], 'stopped' => $c[1],
                'restart' => $c[2], 'restartPidAlive' => $c[3] ) ) );
            $this->assertSame( expVelocityMarkers::shouldStart( $c[0], $c[1], $c[2], $c[3], $now, 300 ),
                               $d['action'] === 'start', "case $i" );
        }
    }

    public function testRunStartsDownEngineAndLogs()
    {
        $engine = new expVelocityWatchdogTestEngine();
        $dog = new expVelocityWatchdogTestDog( $engine, $this->dir );
        $r = $dog->run();
        $this->assertTrue( $r['started'] );
        $this->assertSame( 'down', $r['reason'] );
        $this->assertSame( 1, $engine->starts );
        $this->assertStringContainsString( 'action=start reason=down', file_get_contents( $dog->logFile() ) );

        // Now it runs: the next check does nothing and logs nothing.
        $size = filesize( $dog->logFile() );
        $r = $dog->run();
        $this->assertSame( 'running', $r['reason'] );
        $this->assertSame( 1, $engine->starts );
        clearstatcache();
        $this->assertSame( $size, filesize( $dog->logFile() ) );
    }

    public function testRunRespectsStoppedMarker()
    {
        $engine = new expVelocityWatchdogTestEngine();
        $dog = new expVelocityWatchdogTestDog( $engine, $this->dir );
        ( new expVelocityMarkers( $this->dir . '/run' ) )->markStopped( 'stop' );
        $r = $dog->run();
        $this->assertSame( 'intentionally-stopped', $r['reason'] );
        $this->assertSame( 0, $engine->starts );
        $this->assertStringContainsString( 'reason=intentionally-stopped', file_get_contents( $dog->logFile() ) );
    }

    public function testRunRespectsLiveRestartMarker()
    {
        $engine = new expVelocityWatchdogTestEngine();
        $dog = new expVelocityWatchdogTestDog( $engine, $this->dir );
        ( new expVelocityMarkers( $this->dir . '/run' ) )->write( expVelocityMarkers::RESTART,
            array( 'started' => time(), 'pid' => getmypid(), 'unit' => null, 'by' => 'restart' ) );
        $r = $dog->run();
        $this->assertSame( 'restart-in-progress', $r['reason'] );
        $this->assertSame( 0, $engine->starts );
    }

    public function testRunClearsStaleRestartMarkerWhenItStarts()
    {
        $engine = new expVelocityWatchdogTestEngine();
        $dog = new expVelocityWatchdogTestDog( $engine, $this->dir );
        $markers = new expVelocityMarkers( $this->dir . '/run' );
        // A pid that cannot run: the restart's process is gone.
        $markers->write( expVelocityMarkers::RESTART, array( 'started' => time() - 10, 'pid' => 2147483646, 'unit' => null, 'by' => 'restart' ) );
        $r = $dog->run();
        $this->assertSame( 'restart-stale', $r['reason'] );
        $this->assertTrue( $r['started'] );
        $this->assertNull( $markers->read( expVelocityMarkers::RESTART ) );
    }

    public function testStartFailureStillClearsStaleRestartMarker()
    {
        $engine = new expVelocityWatchdogTestEngine();
        $engine->failStart = true;
        $dog = new expVelocityWatchdogTestDog( $engine, $this->dir );
        $markers = new expVelocityMarkers( $this->dir . '/run' );
        $markers->write( expVelocityMarkers::RESTART, array( 'started' => 1, 'pid' => getmypid(), 'unit' => null, 'by' => 'restart' ) );
        $this->assertSame( 'restart-stale', $dog->run()['reason'] );
        $this->assertNull( $markers->read( expVelocityMarkers::RESTART ) );
        // The next check sees a plain down server.
        $this->assertSame( 'down', $dog->run()['reason'] );
    }

    public function testRunPausedWhenConfDisabled()
    {
        $engine = new expVelocityWatchdogTestEngine();
        $dog = new expVelocityWatchdogTestDog( $engine, $this->dir );
        file_put_contents( $dog->confFile( false ), $dog->confText() );
        $this->assertTrue( $dog->isPaused() );
        $r = $dog->run();
        $this->assertSame( 'paused', $r['reason'] );
        symlink( '../conf-available/watchdog.conf', $dog->confFile( true ) );
        $this->assertFalse( $dog->isPaused() );
    }

    public function testFailedStartsCountAndBackOff()
    {
        $engine = new expVelocityWatchdogTestEngine();
        $engine->failStart = true;
        $dog = new expVelocityWatchdogTestDog( $engine, $this->dir );
        for ( $i = 0; $i < 3; $i++ )
            $this->assertFalse( $dog->run()['ok'] );
        $this->assertSame( 3, $engine->starts );
        $this->assertSame( 'backoff', $dog->run()['reason'] );
        $this->assertSame( 3, $engine->starts );
    }

    public function testUnitsNameTheInstallation()
    {
        $dog = new expVelocityWatchdogTestDog( new expVelocityWatchdogTestEngine(), $this->dir );
        $service = $dog->serviceUnit();
        $this->assertStringContainsString( 'Type=oneshot', $service );
        $this->assertStringContainsString( 'KillMode=process', $service );
        $this->assertStringContainsString( eZSys::rootDir() . '/bin/php/velocity.php watchdog run', $service );
        $this->assertStringContainsString( 'OnUnitActiveSec=' . $dog->interval(), $dog->timerUnit() );
        $conf = json_decode( $dog->confText(), true );
        $this->assertSame( $dog->restartTimeout(), $conf['watchdog']['restartTimeout'] );
    }
}

/**
 * An engine that only pretends: running or not, a start that works or fails.
 */
class expVelocityWatchdogTestEngine extends expVelocity
{
    public $running = false;
    public $failStart = false;
    public $starts = 0;

    public function __construct()
    {
        parent::__construct( 'velocity.ini' );
    }

    public function engineName()
    {
        return 'qbix';
    }

    public function isRunning()
    {
        return $this->running;
    }

    public function start()
    {
        $this->starts++;
        if ( $this->failStart )
            return array( 'ok' => false, 'message' => 'refused (test)' );
        $this->running = true;
        return array( 'ok' => true, 'message' => 'started (test)' );
    }
}

/**
 * The watchdog with its files in the test's directory.
 */
class expVelocityWatchdogTestDog extends expVelocityWatchdog
{
    private $base;

    public function __construct( expVelocity $velocity, $base )
    {
        parent::__construct( $velocity, $base . '/run' );
        $this->base = $base;
    }

    public function confFile( $enabled = true )
    {
        return $this->base . '/conf-' . ( $enabled ? 'enabled' : 'available' ) . '/watchdog.conf';
    }

    public function logFile()
    {
        return $this->base . '/velocity-watchdog.log';
    }
}
