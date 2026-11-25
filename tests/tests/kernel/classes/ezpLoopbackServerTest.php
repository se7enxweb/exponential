<?php
/**
 * File containing the ezpLoopbackServerTest class.
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

use PHPUnit\Framework\TestCase;

/**
 * The built-in server a test starts through ezpLoopbackServer holds none of the test process's descriptors and
 * does not outlive it, not even when the test process is killed before its tearDown.
 */
class ezpLoopbackServerTest extends TestCase
{
    /** @var string a router whose path names this test's processes */
    private $router = '';

    protected function setUp(): void
    {
        if ( !function_exists( 'proc_open' ) || DIRECTORY_SEPARATOR !== '/' || !self::has( 'pgrep' ) )
            $this->markTestSkipped( 'Needs proc_open, a POSIX shell and pgrep.' );
        $this->router = sys_get_temp_dir() . '/ezploopback_' . getmypid() . '_' . bin2hex( random_bytes( 4 ) ) . '.php';
        file_put_contents( $this->router, "<?php echo 'ok';\n" );
    }

    protected function tearDown(): void
    {
        foreach ( $this->pids() as $pid )
            @posix_kill( $pid, 9 );
        @unlink( $this->router );
    }

    public function testServerHoldsNoDescriptorOfTheTestProcess()
    {
        if ( !self::has( 'lsof' ) )
            $this->markTestSkipped( 'Needs lsof.' );
        $held = tempnam( sys_get_temp_dir(), 'ezploopback_held_' );
        $handle = fopen( $held, 'r' );
        $port = self::freePort();
        $server = ezpLoopbackServer::start( array( PHP_BINARY, '-S', '127.0.0.1:' . $port, $this->router ),
                                            array( 'PHP_CLI_SERVER_WORKERS' => '2', 'PATH' => (string)getenv( 'PATH' ) ) );
        try
        {
            $this->assertTrue( self::answers( $port ), 'the server answers' );
            $pids = $this->pids();
            $this->assertNotEmpty( $pids );
            foreach ( $pids as $pid )
            {
                $open = (string)shell_exec( 'lsof -p ' . (int)$pid . ' 2>/dev/null' );
                $this->assertStringNotContainsString( basename( $held ), $open, "process $pid holds a file of the test process" );
            }
        }
        finally
        {
            ezpLoopbackServer::stop( $server );
            fclose( $handle );
            unlink( $held );
        }
        $this->assertTrue( $this->gone(), 'server and workers end with stop()' );
    }

    public function testServerAndWorkersEndWhenTheTestProcessIsKilled()
    {
        if ( !function_exists( 'posix_kill' ) )
            $this->markTestSkipped( 'Needs posix_kill.' );
        $port = self::freePort();
        // A stand-in for a test process that dies without its tearDown
        $code = 'require ' . var_export( realpath( __DIR__ . '/../../../toolkit/ezploopbackserver.php' ), true ) . ';'
              . '$s = ezpLoopbackServer::start( array( PHP_BINARY, "-S", "127.0.0.1:' . $port . '", ' . var_export( $this->router, true ) . ' ),'
              . ' array( "PHP_CLI_SERVER_WORKERS" => "2", "PATH" => (string)getenv( "PATH" ) ) );'
              . 'echo "started\n"; sleep( 60 );';
        $child = proc_open( array( PHP_BINARY, '-r', $code ), array( 1 => array( 'pipe', 'w' ), 2 => array( 'file', '/dev/null', 'w' ) ), $pipes );
        $this->assertSame( "started\n", fgets( $pipes[1] ) );
        $this->assertTrue( self::answers( $port ), 'the server answers' );
        $this->assertNotEmpty( $this->pids() );

        posix_kill( proc_get_status( $child )['pid'], 9 );
        fclose( $pipes[1] );
        proc_close( $child );

        $this->assertTrue( $this->gone(), 'server and workers end once the process that started them is gone' );
    }

    /** @return int[] the processes running this test's router */
    private function pids()
    {
        if ( $this->router === '' )
            return array();
        // pgrep runs without a shell: shell_exec()'s sh -c carries the pattern in its own command line, so pgrep -f
        // found that shell and the processes never looked gone
        $process = proc_open( array( 'pgrep', '-f', basename( $this->router ) ),
                              array( 1 => array( 'pipe', 'w' ), 2 => array( 'file', '/dev/null', 'w' ) ), $pipes );
        if ( !is_resource( $process ) )
            return array();
        $out = trim( (string)stream_get_contents( $pipes[1] ) );
        fclose( $pipes[1] );
        proc_close( $process );
        return $out === '' ? array() : array_map( 'intval', preg_split( '/\s+/', $out ) );
    }

    /** @return bool no process of this test's router is left after at most five seconds */
    private function gone()
    {
        for ( $i = 0; $i < 50; $i++ )
        {
            if ( !$this->pids() )
                return true;
            usleep( 100000 );
        }
        return false;
    }

    private static function answers( $port )
    {
        for ( $i = 0; $i < 100; $i++ )
        {
            $connection = @fsockopen( '127.0.0.1', $port, $errno, $errstr, 0.1 );
            if ( $connection )
            {
                fclose( $connection );
                return true;
            }
            usleep( 50000 );
        }
        return false;
    }

    private static function freePort()
    {
        $socket = stream_socket_server( 'tcp://127.0.0.1:0', $errno, $errstr );
        $name = stream_socket_get_name( $socket, false );
        fclose( $socket );
        return (int)substr( $name, strrpos( $name, ':' ) + 1 );
    }

    private static function has( $tool )
    {
        return trim( (string)shell_exec( 'command -v ' . escapeshellarg( $tool ) . ' 2>/dev/null' ) ) !== '';
    }
}
