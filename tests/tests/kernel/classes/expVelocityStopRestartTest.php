<?php
/**
 * File containing the expVelocityStopRestartTest class.
 *
 * stop, restart and status of the qbix engine against real servers on a port
 * of their own: every process of the installation is found and ended, not
 * only the one in the pid file; a stop that the server does not act on ends
 * in SIGKILL; a restart that was interrupted half-way is finished by the
 * next one; a restart succeeds only when a new parent answers; and status
 * says "running" only for a server that takes a request.
 *
 * Starts servers, so it needs Linux (/proc), pcntl, posix and the engine's
 * server script; it is skipped otherwise. The servers run from this
 * installation with HTTPS, the layout tree, the engine archive and the
 * response cache off, their pid files and logs in a directory of their own.
 * EXP_VELOCITY_TEST_SCRIPT names another server script (an engine checkout).
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class expVelocityStopRestartTest extends ezpTestCase
{
    /** @var string */
    private $dir;

    /** @var int */
    private $port;

    /** @var bool whether this case started a server */
    private $launched = false;

    /** @var expVelocity|null */
    private $velocity;

    public function setUp(): void
    {
        parent::setUp();
        if ( !is_dir( '/proc/self/fd' ) || !function_exists( 'posix_kill' ) || !function_exists( 'pcntl_fork' ) )
            $this->markTestSkipped( 'needs Linux /proc, posix and pcntl' );

        $this->dir = eZSys::rootDir() . '/var/tmp/velocity-stop-test-' . getmypid();
        eZDir::mkdir( $this->dir . '/run', false, true );
        $this->port = $this->freePort();
        if ( !$this->port )
            $this->markTestSkipped( 'no free port' );
    }

    public function tearDown(): void
    {
        if ( $this->velocity !== null && $this->launched )
        {
            // Whatever a failed case left running: only processes whose output
            // goes to this test's own log directory, so that a fault in
            // processIDs() cannot take anything else down with it.
            $dir = realpath( $this->dir . '/run' ) . '/';
            foreach ( $this->velocity->processIDs() as $pid )
                foreach ( array( 1, 2 ) as $fd )
                    if ( strpos( (string)@readlink( '/proc/' . $pid . '/fd/' . $fd ), $dir ) === 0 )
                    {
                        @posix_kill( $pid, SIGKILL );
                        break;
                    }
        }
        $this->launched = false;
        $this->velocity = null;
        ezpINIHelper::restoreINISettings();
        if ( $this->dir && is_dir( $this->dir ) )
            eZDir::recursiveDelete( $this->dir );
        parent::tearDown();
    }

    /**
     * A qbix engine on a port of its own.
     *
     * @param int $instances
     * @return expVelocity
     */
    private function engine( $instances = 1, $stopTimeout = 8 )
    {
        $settings = array(
            array( 'ServerSettings', 'Host', '127.0.0.1' ),
            array( 'ServerSettings', 'Port', (string)$this->port ),
            array( 'ServerSettings', 'Workers', '2' ),
            array( 'ServerSettings', 'SpareWorkers', '0' ),
            array( 'ServerSettings', 'Instances', (string)$instances ),
            array( 'ServerSettings', 'PidFile', $this->dir . '/run/server.pid' ),
            array( 'ServerSettings', 'LogFile', $this->dir . '/run/console.log' ),
            array( 'ServerSettings', 'EnginePhar', 'disabled' ),
            array( 'HTTPSSettings', 'Enabled', 'false' ),
            array( 'LayoutSettings', 'ConfDir', 'disabled' ),
            array( 'CacheSettings', 'Enabled', 'disabled' ),
            array( 'LogSettings', 'Dir', $this->dir . '/log' ),
            array( 'ControlSettings', 'StopTimeout', (string)$stopTimeout ),
            array( 'ControlSettings', 'ShutdownTimeout', '5' ),
        );
        if ( getenv( 'EXP_VELOCITY_TEST_SCRIPT' ) )
            $settings[] = array( 'ServerSettings', 'ScriptPath', getenv( 'EXP_VELOCITY_TEST_SCRIPT' ) );
        foreach ( $settings as $s )
            ezpINIHelper::setINISetting( 'velocity.ini', $s[0], $s[1], $s[2] );

        $this->velocity = expVelocity::create( 'velocity.ini', 'qbix' );
        if ( !is_file( $this->velocity->scriptPath() ) )
            $this->markTestSkipped( 'no engine server script at ' . $this->velocity->scriptPath() );
        return $this->velocity;
    }

    private function freePort()
    {
        for ( $i = 0; $i < 40; ++$i )
        {
            $port = 18100 + random_int( 0, 1800 );
            $probe = @stream_socket_server( 'tcp://127.0.0.1:' . $port, $errno, $errstr );
            if ( $probe )
            {
                fclose( $probe );
                return $port;
            }
        }
        return 0;
    }

    private function started( expVelocity $velocity )
    {
        $found = $velocity->processIDs();
        $this->assertSame( array(), $found, 'nothing of this test runs before its start: '
            . implode( '; ', array_map( array( $velocity, 'describeProcess' ), $found ) ) );
        $this->launched = true;
        $result = $velocity->start();
        $this->assertTrue( $result['ok'], 'start: ' . $result['message'] . "\n" . $this->log( $velocity ) );
        // The answer comes from the server's own process: wait for it, as the
        // pool may still be forking.
        $deadline = microtime( true ) + 20;
        while ( !$velocity->answers( 1.0 ) && microtime( true ) < $deadline )
            usleep( 250000 );
        $this->assertTrue( $velocity->answers(), 'the started server answers' . "\n" . $this->log( $velocity ) );
        return $velocity->parentIDs();
    }

    /**
     * restart(), leaving the exception handler as it was: the audit record
     * the verb writes sets one up the first time.
     */
    private function restart( expVelocity $velocity )
    {
        $handler = function_exists( 'get_exception_handler' ) ? get_exception_handler() : null;
        $result = $velocity->restart();
        if ( function_exists( 'get_exception_handler' ) && get_exception_handler() !== $handler )
            restore_exception_handler();
        return $result;
    }

    private function log( expVelocity $velocity )
    {
        $text = '';
        for ( $i = 0; $i < $velocity->instances(); $i++ )
            $text .= implode( '', array_slice( @file( $velocity->logFile( $i ) ) ?: array(), -8 ) );
        return $text;
    }

    private function alive( $pid )
    {
        $stat = @file_get_contents( '/proc/' . (int)$pid . '/stat' );
        return $stat !== false && !in_array( substr( $stat, strrpos( $stat, ')' ) + 2, 1 ), array( 'Z', 'X' ), true );
    }

    /**
     * The zygote names no script ("qbixserver: zygote"), and a server's
     * processes outlive it when it is killed: both are this installation's.
     */
    public function testProcessIDsFindsTheWholeFamily()
    {
        $velocity = $this->engine();
        $parents = $this->started( $velocity );
        $pids = $velocity->processIDs();

        $this->assertContains( $parents[0], $pids );
        $zygote = false;
        foreach ( $pids as $pid )
            if ( strpos( (string)@file_get_contents( '/proc/' . $pid . '/cmdline' ), 'qbixserver: zygote' ) === 0 )
                $zygote = true;
        $this->assertTrue( $zygote, 'the zygote is among them' );
        $this->assertNotContains( getmypid(), $pids, 'never the caller' );
        $this->assertTrue( $velocity->stop()['ok'] );
    }

    /**
     * Only a PHP binary running the script, and never the server's own
     * --stop and --reload, nor a wrapper or a shell that names the script.
     */
    public function testRunsServerScriptArgv()
    {
        $method = new ReflectionMethod( 'expVelocity', 'runsServerScriptArgv' );
        $script = __FILE__;
        $this->assertTrue( $method->invoke( null, array( '/usr/bin/php', '-d', 'x=1', $script, '--port=1' ), array( $script ) ) );
        $this->assertTrue( $method->invoke( null, array( 'php8.5', $script ), array( $script ) ) );
        $this->assertFalse( $method->invoke( null, array( 'timeout', '280', 'php', $script ), array( $script ) ) );
        $this->assertFalse( $method->invoke( null, array( '/bin/bash', '-c', 'pgrep -f ' . $script ), array( $script ) ) );
        $this->assertFalse( $method->invoke( null, array( '/bin/sh', '-c', 'sleep 1', $script ), array( $script ) ) );
        $this->assertFalse( $method->invoke( null, array( 'php', $script, '--stop', '--pid=x' ), array( $script ) ) );
        $this->assertFalse( $method->invoke( null, array( 'php', '/elsewhere/qbixserver.php' ), array( $script ) ) );
    }

    /**
     * Failure B of 2026-10-07: the first instance's parent gone with its pid
     * file, the others still serving. status names it; restart ends every
     * old process and starts new ones that answer.
     */
    public function testRestartFinishesAnInterruptedRestart()
    {
        $velocity = $this->engine( 2 );
        $parents = $this->started( $velocity );
        $this->assertCount( 2, $parents );
        $before = $velocity->processIDs();

        posix_kill( $parents[0], SIGKILL );
        @unlink( $velocity->pidFile( 0 ) );
        usleep( 500000 );

        $status = $velocity->status();
        $this->assertTrue( $status['running'] );
        $this->assertSame( 'running', $status['state'], 'the other instance still answers' );
        $this->assertNotEmpty( $status['problems'] );
        $this->assertStringContainsString( 'instance 0 has no running parent', implode( "\n", $status['problems'] ) );

        $result = $this->restart( $velocity );
        $this->assertTrue( $result['ok'], $result['message'] . "\n" . $this->log( $velocity ) );
        $this->assertStringContainsString( 'answers', $result['message'] );
        $now = $velocity->parentIDs();
        $this->assertCount( 2, $now );
        $this->assertSame( array(), array_values( array_intersect( $now, $before ) ), 'only new parents' );
        foreach ( $before as $pid )
            $this->assertFalse( $this->alive( $pid ) && in_array( $pid, $velocity->processIDs(), true ), "old process $pid is gone" );
        $this->assertSame( array(), $velocity->health( $velocity->processIDs() )['problems'] );
        $this->assertTrue( $velocity->stop()['ok'] );
        $this->assertSame( array(), $velocity->processIDs() );
        $this->assertSame( array(), $velocity->listeningPorts(), 'the port is free' );
    }

    /**
     * Failure A of 2026-10-07, as near as a test gets: a parent that accepts
     * nothing and acts on no signal it can catch (stopped here; blocked in
     * futex_do_wait there). status says so; stop ends it with SIGKILL after
     * StopTimeout; restart then starts a new one that answers.
     */
    public function testAStuckParentIsReportedAndKilled()
    {
        $velocity = $this->engine( 1, 3 );
        $parents = $this->started( $velocity );
        posix_kill( $parents[0], SIGSTOP );
        // Connections the stuck server never accepts.
        $queued = array();
        for ( $i = 0; $i < 3; $i++ )
            $queued[] = @stream_socket_client( 'tcp://127.0.0.1:' . $this->port, $errno, $errstr, 1 );

        $status = $velocity->status();
        $this->assertTrue( $status['running'] );
        $this->assertSame( 'not answering', $status['state'] );
        $this->assertFalse( $status['answering'] );
        $problems = implode( "\n", $status['problems'] );
        $this->assertStringContainsString( 'not accepted', $problems );
        $this->assertStringContainsString( 'does not answer', $problems );

        $t = microtime( true );
        $result = $this->restart( $velocity );
        $this->assertTrue( $result['ok'], $result['message'] . "\n" . $this->log( $velocity ) );
        $this->assertStringContainsString( 'were killed', $result['message'] );
        $this->assertLessThan( 30, microtime( true ) - $t );
        $this->assertFalse( $this->alive( $parents[0] ), 'the stuck parent is gone' );
        $this->assertNotSame( $parents, $velocity->parentIDs() );
        $this->assertSame( 'running', $velocity->status()['state'] );
        foreach ( $queued as $s )
            if ( $s )
                fclose( $s );
        $this->assertTrue( $velocity->stop()['ok'] );
    }

    /**
     * SIGKILL to the parent: nothing it started goes on listening or
     * serving, and what is left is found and ended by stop.
     */
    public function testNoChildServesAfterTheParentIsKilled()
    {
        $velocity = $this->engine();
        $parents = $this->started( $velocity );
        $family = array_diff( $velocity->processIDs(), $parents );
        $this->assertNotEmpty( $family );

        posix_kill( $parents[0], SIGKILL );
        usleep( 500000 );
        $this->assertSame( array(), $velocity->listeningPorts(), 'nothing listens on the port' );
        $this->assertFalse( $velocity->answers( 1.0 ) );

        $result = $velocity->stop();
        $this->assertTrue( $result['ok'], $result['message'] );
        $this->assertSame( array(), $velocity->processIDs() );
        foreach ( $family as $pid )
            $this->assertFalse( $this->alive( $pid ), "process $pid is gone" );
    }

    /** A start over leftovers ends them first; a start over a working server refuses. */
    public function testStartOverLeftoversAndOverARunningServer()
    {
        $velocity = $this->engine();
        $parents = $this->started( $velocity );
        $again = $velocity->start();
        $this->assertFalse( $again['ok'] );
        $this->assertSame( 'already running', $again['message'] );

        // A server that does not answer is still a server: start leaves it
        // alone (restart is the verb that replaces it).
        posix_kill( $parents[0], SIGSTOP );
        $again = $velocity->start();
        $this->assertFalse( $again['ok'] );
        $this->assertStringContainsString( 'does not answer', $again['message'] );
        $this->assertTrue( $this->alive( $parents[0] ) );

        // Its pid file gone as well: leftovers of an interrupted stop.
        @unlink( $velocity->pidFile() );
        $result = $velocity->start();
        $this->assertTrue( $result['ok'], $result['message'] . "\n" . $this->log( $velocity ) );
        $this->assertStringContainsString( 'leftover', $result['message'] );
        $this->assertFalse( $this->alive( $parents[0] ) );
        $this->assertTrue( $velocity->stop()['ok'] );
    }
}
