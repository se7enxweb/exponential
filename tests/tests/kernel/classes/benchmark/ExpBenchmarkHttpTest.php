<?php
/**
 * The HTTP path of exp:benchmark against a throwaway "php -S" server on 127.0.0.1, which the test starts on a
 * free port and stops again. Nothing leaves the machine; no kernel or database is needed.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/benchmark/ExpBenchmarkHttpTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group benchmark
 */

require_once __DIR__ . '/../../../../../kernel/classes/expbenchmark.php';
require_once __DIR__ . '/../../../../../kernel/classes/expbenchmarkhttp.php';

class ExpBenchmarkHttpTest extends \PHPUnit\Framework\TestCase
{
    /** @var resource|null */
    protected static $server = null;

    /** @var string */
    protected static $base = '';

    public static function setUpBeforeClass(): void
    {
        if ( !function_exists( 'curl_multi_init' ) or !function_exists( 'proc_open' ) )
            return;

        // A free port: bind to port 0 and let the system choose.
        $socket = stream_socket_server( 'tcp://127.0.0.1:0', $errno, $errstr );
        if ( !$socket )
            return;
        $name = stream_socket_get_name( $socket, false );
        fclose( $socket );
        $port = (int)substr( $name, strrpos( $name, ':' ) + 1 );

        $environment = getenv();
        $environment['PHP_CLI_SERVER_WORKERS'] = '4';   // several requests at once
        self::$server = proc_open(
            array( PHP_BINARY, '-S', '127.0.0.1:' . $port, __DIR__ . '/stub_server.php' ),
            array( 0 => array( 'file', '/dev/null', 'r' ), 1 => array( 'file', '/dev/null', 'w' ), 2 => array( 'file', '/dev/null', 'w' ) ),
            $pipes, __DIR__, $environment );
        if ( !is_resource( self::$server ) )
        {
            self::$server = null;
            return;
        }

        // Wait until it answers, at most five seconds.
        for ( $i = 0; $i < 100; $i++ )
        {
            $connection = @fsockopen( '127.0.0.1', $port, $errno, $errstr, 0.1 );
            if ( $connection )
            {
                fclose( $connection );
                self::$base = 'http://127.0.0.1:' . $port;
                return;
            }
            usleep( 50000 );
        }
    }

    public static function tearDownAfterClass(): void
    {
        if ( is_resource( self::$server ) )
        {
            $status = proc_get_status( self::$server );
            proc_terminate( self::$server );
            // The workers of php -S are its children; a terminated parent takes them along.
            if ( !empty( $status['pid'] ) and function_exists( 'posix_kill' ) )
                @posix_kill( (int)$status['pid'], 15 );
            proc_close( self::$server );
        }
        self::$server = null;
        self::$base = '';
    }

    protected function setUp(): void
    {
        if ( self::$base === '' )
            $this->markTestSkipped( 'No local php -S server (curl or proc_open missing, or it did not start)' );
    }

    protected function options( $extra = array() )
    {
        return array_merge( array( 'requests' => 12, 'concurrency' => 3, 'warmup' => 1, 'rounds' => 2, 'timeout' => 10 ), $extra );
    }

    public function testEveryRequestIsMadeAndTimed()
    {
        $rows = expBenchmarkHttp::run( array( self::$base ), array( '/' ), $this->options() );
        $this->assertCount( 1, $rows );
        $row = $rows[0];
        $this->assertSame( self::$base . ' /', $row['key'] );
        $this->assertSame( 12, $row['n'] );
        $this->assertSame( 12, $row['requests'] );
        $this->assertSame( 0, $row['errors'] );
        $this->assertSame( array( '200' => 12 ), $row['status'] );
        $this->assertSame( array( 'HIT' => 12 ), $row['cache'] );
        $this->assertSame( 1000, $row['bytes'] );
        $this->assertGreaterThan( 0, $row['min'] );
        $this->assertGreaterThanOrEqual( $row['min'], $row['median'] );
        $this->assertGreaterThanOrEqual( $row['median'], $row['p95'] );
        $this->assertGreaterThanOrEqual( $row['p95'], $row['max'] );
        $this->assertGreaterThan( 0, $row['rps'] );
    }

    public function testColdRequestsCarryAUniqueQueryString()
    {
        $rows = expBenchmarkHttp::run( array( self::$base ), array( '/' ), $this->options( array( 'cold' => true ) ) );
        // The stub answers MISS exactly when the cache-busting parameter is there.
        $this->assertSame( array( 'MISS' => 12 ), $rows[0]['cache'] );
    }

    public function testErrorsAreCountedAndNamed()
    {
        $rows = expBenchmarkHttp::run( array( self::$base ), array( '/missing' ), $this->options( array( 'requests' => 4 ) ) );
        $this->assertSame( 4, $rows[0]['errors'] );
        $this->assertSame( array( '404' => 4 ), $rows[0]['status'] );
        $this->assertSame( array( 'HTTP 404' => 4 ), $rows[0]['error_messages'] );
    }

    public function testARefusedConnectionIsAnErrorWithoutATime()
    {
        // Port 1 on loopback: nothing listens there.
        $rows = expBenchmarkHttp::run( array( 'http://127.0.0.1:1' ), array( '/' ), $this->options( array( 'requests' => 2, 'warmup' => 0 ) ) );
        $this->assertSame( 2, $rows[0]['errors'] );
        $this->assertSame( 0, $rows[0]['n'] );
        $this->assertNull( $rows[0]['median'] );
        $this->assertSame( array( 'failed' => 2 ), $rows[0]['status'] );
    }

    public function testBasicAuthIsSent()
    {
        $without = expBenchmarkHttp::run( array( self::$base ), array( '/auth' ), $this->options( array( 'requests' => 2 ) ) );
        $this->assertSame( array( '401' => 2 ), $without[0]['status'] );
        $with = expBenchmarkHttp::run( array( self::$base ), array( '/auth' ), $this->options( array( 'requests' => 2, 'auth' => 'bench:secret' ) ) );
        $this->assertSame( array( '200' => 2 ), $with[0]['status'] );
    }

    public function testTheSlowPathTakesItsTimeAndConcurrencyOverlapsIt()
    {
        $rows = expBenchmarkHttp::run( array( self::$base ), array( '/slow' ), $this->options( array( 'requests' => 8, 'concurrency' => 4, 'rounds' => 1 ) ) );
        $this->assertGreaterThanOrEqual( 20.0, $rows[0]['min'] );
        // One at a time, 8 requests of 20 ms cannot exceed 50 per second; four at a time they do.
        $this->assertGreaterThan( 50.0, $rows[0]['rps'] );
    }

    public function testTwoTargetsGiveOneRowEachAndAPair()
    {
        // The same server under two names is enough to see the A/B bookkeeping.
        $other = str_replace( '127.0.0.1', 'localhost', self::$base );
        $targets = array( self::$base, $other );
        $rows = expBenchmarkHttp::run( $targets, array( '/', '/slow' ), $this->options( array( 'requests' => 4 ) ) );
        $this->assertCount( 4, $rows );
        $pairs = expBenchmark::pairs( $rows, $targets );
        $this->assertCount( 2, $pairs );
        $this->assertSame( self::$base, $pairs[0]['base'] );
        $this->assertSame( $other, $pairs[0]['other'] );
        $this->assertNotNull( $pairs[0]['median_ratio'] );
    }
}
