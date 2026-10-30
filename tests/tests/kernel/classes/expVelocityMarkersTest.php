<?php
/**
 * File containing the expVelocityMarkersTest class.
 *
 * The state files that tell an explicit stop from a restart that was cut off
 * (stopped.json, restart-in-progress.json), the pure decision whether a server
 * that is down may be started, and the detached restart's command and result
 * hand-over. No server and no database: everything runs in a scratch
 * directory under var/tmp.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class expVelocityMarkersTest extends ezpTestCase
{
    /** Above any pid_max Linux allows (4194304), so never a running process. */
    const DEAD_PID = 4194305;

    /** @var string */
    protected $dir;

    public function setUp(): void
    {
        parent::setUp();
        $this->dir = eZSys::rootDir() . '/var/tmp/velocity-markers-test-' . getmypid();
        @mkdir( $this->dir, 0775, true );
        putenv( expVelocityRestartUnit::TOKEN_ENV );
        putenv( expVelocityRestartUnit::SWITCH_ENV );
    }

    public function tearDown(): void
    {
        putenv( expVelocityRestartUnit::TOKEN_ENV );
        putenv( expVelocityRestartUnit::SWITCH_ENV );
        foreach ( (array)glob( $this->dir . '/*' ) as $file )
            @unlink( $file );
        @rmdir( $this->dir );
        parent::tearDown();
    }

    public function testPathKeepsTheMarkerInsideItsDirectory()
    {
        $markers = new expVelocityMarkers( $this->dir . '/' );
        $this->assertSame( $this->dir, $markers->dir() );
        $this->assertSame( $this->dir . '/stopped.json', $markers->path( expVelocityMarkers::STOPPED ) );
        $this->assertSame( $this->dir . '/restart-in-progress.json', $markers->path( expVelocityMarkers::RESTART ) );
        $this->assertSame( $this->dir . '/....etcpasswd.json', $markers->path( '../../etc/passwd' ) );
    }

    public function testWriteReadClear()
    {
        $markers = new expVelocityMarkers( $this->dir );
        $this->assertNull( $markers->read( 'x' ) );
        $this->assertTrue( $markers->clear( 'x' ), 'clearing a marker that is not there succeeds' );

        $this->assertTrue( $markers->write( 'x', array( 'a' => 1 ) ) );
        $this->assertSame( array( 'a' => 1 ), $markers->read( 'x' ) );
        $this->assertSame( array(), glob( $this->dir . '/*.tmp' ), 'no temporary file is left by an atomic write' );

        $this->assertTrue( $markers->clear( 'x' ) );
        $this->assertNull( $markers->read( 'x' ) );
    }

    public function testAnUnreadableMarkerStillCounts()
    {
        $markers = new expVelocityMarkers( $this->dir );
        file_put_contents( $markers->path( expVelocityMarkers::STOPPED ), "not json\n" );
        $this->assertSame( array(), $markers->read( expVelocityMarkers::STOPPED ) );
        $this->assertTrue( $markers->isStopped(), 'a damaged stopped marker still keeps the server down' );
    }

    public function testWriteCreatesTheDirectory()
    {
        $markers = new expVelocityMarkers( $this->dir . '/sub' );
        $this->assertTrue( $markers->write( 'x', array() ) );
        $markers->clear( 'x' );
        @rmdir( $this->dir . '/sub' );
    }

    public function testClearStaleRestartRemovesOnlyADeadProcessMarker()
    {
        $markers = new expVelocityMarkers( $this->dir );
        $this->assertFalse( $markers->clearStaleRestart(), 'no marker, nothing removed' );

        $markers->write( expVelocityMarkers::RESTART, array( 'started' => time(), 'pid' => getmypid() ) );
        $this->assertFalse( $markers->clearStaleRestart(), 'this process\'s own marker stays' );
        $this->assertNotNull( $markers->read( expVelocityMarkers::RESTART ) );

        $markers->write( expVelocityMarkers::RESTART, array( 'started' => time(), 'pid' => self::DEAD_PID ) );
        $this->assertTrue( $markers->clearStaleRestart(), 'a killed restart\'s marker goes' );
        $this->assertNull( $markers->read( expVelocityMarkers::RESTART ) );
    }

    public function testMarkStopped()
    {
        $markers = new expVelocityMarkers( $this->dir );
        $this->assertFalse( $markers->isStopped() );
        $this->assertTrue( $markers->markStopped( 'stop' ) );
        $this->assertTrue( $markers->isStopped() );
        $data = $markers->read( expVelocityMarkers::STOPPED );
        $this->assertSame( 'stop', $data['by'] );
        $this->assertSame( getmypid(), $data['pid'] );
        $this->assertGreaterThan( time() - 60, $data['since'] );
        $this->assertNotSame( '', $data['user'] );
        $markers->clear( expVelocityMarkers::STOPPED );
        $this->assertFalse( $markers->isStopped() );
    }

    public function testRestartHolds()
    {
        $now = 1000000;
        $young = array( 'started' => $now - 10, 'pid' => 1 );
        $this->assertTrue( expVelocityMarkers::restartHolds( $young, true, $now, 300 ) );
        $this->assertFalse( expVelocityMarkers::restartHolds( $young, false, $now, 300 ), 'its process is gone: a cut-off restart' );
        $this->assertFalse( expVelocityMarkers::restartHolds( array( 'started' => $now - 300 ), true, $now, 300 ), 'older than the timeout' );
        $this->assertTrue( expVelocityMarkers::restartHolds( array( 'started' => $now - 299 ), true, $now, 300 ) );
        $this->assertFalse( expVelocityMarkers::restartHolds( array(), true, $now, 300 ), 'no start time: not trusted' );
        $this->assertTrue( expVelocityMarkers::restartHolds( array( 'started' => $now - 200 ), true, $now, 0 ),
            'a timeout of 0 means the default (' . expVelocityMarkers::DEFAULT_RESTART_TIMEOUT . ' s)' );
        $this->assertFalse( expVelocityMarkers::restartHolds( array( 'started' => $now - 400 ), true, $now, -1 ) );
    }

    public function testRestartInProgressReadsTheMarkerAndThePid()
    {
        $markers = new expVelocityMarkers( $this->dir );
        $this->assertFalse( $markers->restartInProgress() );

        $markers->write( expVelocityMarkers::RESTART, array( 'started' => time(), 'pid' => getmypid() ) );
        $this->assertTrue( $markers->restartInProgress( 300 ) );
        $this->assertFalse( $markers->restartInProgress( 300, time() + 301 ) );

        $markers->write( expVelocityMarkers::RESTART, array( 'started' => time(), 'pid' => self::DEAD_PID ) );
        $this->assertFalse( $markers->restartInProgress( 300 ), 'the restart process was killed' );
    }

    public function testPidAlive()
    {
        $this->assertTrue( expVelocityMarkers::pidAlive( getmypid() ) );
        $this->assertFalse( expVelocityMarkers::pidAlive( 0 ) );
        $this->assertFalse( expVelocityMarkers::pidAlive( -1 ) );
        $this->assertFalse( expVelocityMarkers::pidAlive( self::DEAD_PID ) );
    }

    /**
     * The watchdog's whole decision.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('shouldStartCases')]
    public function testShouldStart( $expected, $running, $stopped, $restart, $alive, $why )
    {
        $this->assertSame( $expected, expVelocityMarkers::shouldStart( $running, $stopped, $restart, $alive, 1000000, 300 ), $why );
    }

    public static function shouldStartCases()
    {
        $now = 1000000;
        $young = array( 'started' => $now - 30, 'pid' => 123 );
        $old = array( 'started' => $now - 3600, 'pid' => 123 );
        $stopped = array( 'since' => $now - 5, 'by' => 'stop' );
        return array(
            array( false, true, null, null, false, 'running: nothing to do' ),
            array( false, true, $stopped, null, false, 'running, even with a stale stopped marker: nothing to do' ),
            array( true, false, null, null, false, 'down by accident (no marker): start' ),
            array( false, false, $stopped, null, false, 'explicit stop: stays stopped' ),
            array( false, false, array(), null, false, 'a damaged stopped marker still holds' ),
            array( false, false, $stopped, $old, false, 'explicit stop wins over a stale restart' ),
            array( false, false, null, $young, true, 'a restart is under way: do not fight it' ),
            array( true, false, null, $young, false, 'the restart process was killed: start' ),
            array( true, false, null, $old, true, 'the restart hangs past the timeout: start' ),
            array( true, false, null, array(), true, 'a damaged restart marker does not hold forever' ),
        );
    }

    public function testValidToken()
    {
        $this->assertTrue( expVelocityRestartUnit::validToken( 'a1b2c3d4e5f60718' ) );
        $this->assertFalse( expVelocityRestartUnit::validToken( '' ) );
        $this->assertFalse( expVelocityRestartUnit::validToken( 'abc' ) );
        $this->assertFalse( expVelocityRestartUnit::validToken( '../../etc/passwd' ) );
        $this->assertFalse( expVelocityRestartUnit::validToken( 'A1B2C3D4E5F60718' ) );
        $this->assertFalse( expVelocityRestartUnit::validToken( "a1b2c3d4\n" ) );
    }

    public function testUnitName()
    {
        $now = mktime( 7, 44, 5, 10, 17, 2026 );
        $this->assertSame( 'vc-restart-20261017-074405-a1b2c3d4', expVelocityRestartUnit::unitName( 'a1b2c3d4e5f60718', $now ) );
    }

    public function testDetachedCommandInATransientScope()
    {
        $cmd = expVelocityRestartUnit::detachedCommand( '/usr/bin/php', '/srv/x/bin/php/velocity.php',
            array( 'restart', '--json', "--engine=it's" ), 'a1b2c3d4e5f60718', '/srv/x/var/run/restart-a1.log',
            '/usr/bin/setsid', '/usr/bin/systemd-run', 'vc-restart-20261017-074405-a1b2c3d4' );
        $this->assertStringStartsWith( "'/usr/bin/systemd-run' '--scope' '--quiet' '--collect' '--unit=vc-restart-20261017-074405-a1b2c3d4' '--' '/usr/bin/setsid' '-f' 'env' 'EXP_VELOCITY_RESTART_TOKEN=a1b2c3d4e5f60718' '/usr/bin/php' '/srv/x/bin/php/velocity.php' 'restart' '--json'", $cmd );
        $this->assertStringContainsString( escapeshellarg( "--engine=it's" ), $cmd, 'arguments are quoted' );
        $this->assertStringEndsWith( " > '/srv/x/var/run/restart-a1.log' 2>&1 < /dev/null", $cmd,
            'no terminal: a closed terminal cannot reach it' );
    }

    public function testDetachedCommandWithoutSystemd()
    {
        foreach ( array( false, '' ) as $none )
        {
            $cmd = expVelocityRestartUnit::detachedCommand( '/usr/bin/php', '/v.php', array( 'restart' ), 'a1b2c3d4',
                '/l.log', '/usr/bin/setsid', $none, 'vc-restart-x' );
            $this->assertStringStartsWith( "'/usr/bin/setsid' '-f' 'env' 'EXP_VELOCITY_RESTART_TOKEN=a1b2c3d4' '/usr/bin/php' '/v.php' 'restart'", $cmd );
            $this->assertStringNotContainsString( 'systemd-run', $cmd );
        }
    }

    public function testEnabledSwitches()
    {
        $this->assertFalse( expVelocityRestartUnit::enabled( 'disabled' ) );
        $this->assertFalse( expVelocityRestartUnit::enabled( ' Off ' ) );

        putenv( expVelocityRestartUnit::SWITCH_ENV . '=0' );
        $this->assertFalse( expVelocityRestartUnit::enabled( 'enabled' ), 'EXP_VELOCITY_DETACHED_RESTART=0 turns it off for one call' );
        putenv( expVelocityRestartUnit::SWITCH_ENV );

        putenv( expVelocityRestartUnit::TOKEN_ENV . '=a1b2c3d4e5f60718' );
        $this->assertTrue( expVelocityRestartUnit::isChild() );
        $this->assertFalse( expVelocityRestartUnit::enabled( 'enabled' ), 'the detached process never detaches again' );
        putenv( expVelocityRestartUnit::TOKEN_ENV );
        $this->assertFalse( expVelocityRestartUnit::isChild() );

        if ( expVelocityRestartUnit::findExecutable( 'setsid' ) !== false && function_exists( 'exec' ) )
            $this->assertTrue( expVelocityRestartUnit::enabled( 'enabled' ) );
    }

    public function testFilesOfAToken()
    {
        $unit = new expVelocityRestartUnit( $this->dir . '/' );
        $this->assertSame( $this->dir . '/restart-a1b2c3d4.result.json', $unit->resultFile( 'a1b2c3d4' ) );
        $this->assertSame( $this->dir . '/restart-a1b2c3d4.log', $unit->logFile( 'a1b2c3d4' ) );
    }

    public function testWaitHandsOverTheResultAndRemovesIt()
    {
        $unit = new expVelocityRestartUnit( $this->dir );
        file_put_contents( $unit->logFile( 'a1b2c3d4' ), "restarted\n" );
        file_put_contents( $unit->resultFile( 'a1b2c3d4' ), json_encode( array( 'code' => 0 ) ) );
        $this->assertSame( array( 0, "restarted\n" ), $unit->wait( 'a1b2c3d4', array( $this->dir ), 5 ) );
        $this->assertFileDoesNotExist( $unit->resultFile( 'a1b2c3d4' ) );
        $this->assertFileDoesNotExist( $unit->logFile( 'a1b2c3d4' ) );

        file_put_contents( $unit->resultFile( 'b1b2c3d4' ), 'garbage' );
        list( $code ) = $unit->wait( 'b1b2c3d4', array( $this->dir ), 5 );
        $this->assertSame( 1, $code, 'an unreadable result is a failure' );
    }

    public function testWaitNoticesADeadChild()
    {
        $unit = new expVelocityRestartUnit( $this->dir );
        ( new expVelocityMarkers( $this->dir ) )->write( expVelocityMarkers::RESTART,
            array( 'started' => time(), 'pid' => self::DEAD_PID, 'token' => 'c1b2c3d4' ) );
        $begun = microtime( true );
        list( $code, $output ) = $unit->wait( 'c1b2c3d4', array( $this->dir ), 5 );
        $this->assertSame( 1, $code );
        $this->assertStringContainsString( 'ended without a result', $output );
        $this->assertStringContainsString( 'exp:velocity start', $output );
        $this->assertLessThan( 3, microtime( true ) - $begun, 'does not wait out the timeout' );
    }

    public function testChildMarksAndUnmarks()
    {
        putenv( expVelocityRestartUnit::TOKEN_ENV . '=d1b2c3d4e5f60718' );
        $markers = new expVelocityMarkers( $this->dir );
        expVelocityRestartUnit::beginChild( array( $this->dir, $this->dir ), 'deploy' );
        $marker = $markers->read( expVelocityMarkers::RESTART );
        $this->assertSame( getmypid(), $marker['pid'] );
        $this->assertSame( 'd1b2c3d4e5f60718', $marker['token'] );
        $this->assertSame( 'deploy', $marker['by'] );
        $this->assertTrue( $markers->restartInProgress( 300 ) );

        expVelocityRestartUnit::childDone( true );
        $this->assertNull( $markers->read( expVelocityMarkers::RESTART ), 'the marker goes when the restart is done' );
        $unit = new expVelocityRestartUnit( $this->dir );
        $result = json_decode( file_get_contents( $unit->resultFile( 'd1b2c3d4e5f60718' ) ), true );
        $this->assertSame( 0, $result['code'] );

        // Once done, a second call (the shutdown function) changes nothing.
        unlink( $unit->resultFile( 'd1b2c3d4e5f60718' ) );
        expVelocityRestartUnit::childShutdown();
        $this->assertFileDoesNotExist( $unit->resultFile( 'd1b2c3d4e5f60718' ) );
    }

    public function testBeginChildDoesNothingOutsideTheChild()
    {
        expVelocityRestartUnit::beginChild( array( $this->dir ) );
        $this->assertNull( ( new expVelocityMarkers( $this->dir ) )->read( expVelocityMarkers::RESTART ) );
    }
}
