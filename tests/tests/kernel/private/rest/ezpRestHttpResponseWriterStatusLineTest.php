<?php
/**
 * The HTTP status line of REST answers under the PHP built-in server.
 *
 * The REST status objects put the status into the response writer's header list as "HTTP/1.1 <code>", and
 * the writer sent every entry as header( "$name: $value" ). Apache and PHP-FPM read the code out of
 * "HTTP/1.1 401: ", the built-in server (php -S) sent it as the status line as it stood. The test starts a
 * throwaway "php -S" on a free port of 127.0.0.1 with testfiles/status_line_router.php and reads the raw
 * status line from a socket. No kernel or database is needed.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

class ezpRestHttpResponseWriterStatusLineTest extends PHPUnit\Framework\TestCase
{
    /** @var resource|null */
    protected static $server = null;

    /** @var int */
    protected static $port = 0;

    public static function setUpBeforeClass(): void
    {
        if ( !function_exists( 'proc_open' ) )
            return;

        // A free port: bind to port 0 and let the system choose.
        $socket = stream_socket_server( 'tcp://127.0.0.1:0', $errno, $errstr );
        if ( !$socket )
            return;
        $name = stream_socket_get_name( $socket, false );
        fclose( $socket );
        $port = (int)substr( $name, strrpos( $name, ':' ) + 1 );

        self::$server = proc_open(
            array( PHP_BINARY, '-S', '127.0.0.1:' . $port, __DIR__ . '/testfiles/status_line_router.php' ),
            array( 0 => array( 'file', '/dev/null', 'r' ), 1 => array( 'file', '/dev/null', 'w' ), 2 => array( 'file', '/dev/null', 'w' ) ),
            $pipes, __DIR__ );
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
                self::$port = $port;
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
            if ( !empty( $status['pid'] ) and function_exists( 'posix_kill' ) )
                @posix_kill( (int)$status['pid'], 15 );
            proc_close( self::$server );
        }
        self::$server = null;
        self::$port = 0;
    }

    protected function setUp(): void
    {
        if ( self::$port === 0 )
            $this->markTestSkipped( 'No local php -S server (proc_open missing, or it did not start)' );
    }

    /**
     * The raw head of the answer to GET $path, as lines.
     */
    private function head( $path, $protocol = 'HTTP/1.1' )
    {
        $connection = fsockopen( '127.0.0.1', self::$port, $errno, $errstr, 5 );
        $this->assertNotFalse( $connection, $errstr );
        stream_set_timeout( $connection, 10 );
        fwrite( $connection, "GET $path $protocol\r\nHost: 127.0.0.1\r\nConnection: close\r\n\r\n" );
        $data = stream_get_contents( $connection );
        fclose( $connection );

        return explode( "\r\n", explode( "\r\n\r\n", $data, 2 )[0] );
    }

    public static function statuses()
    {
        return array(
            'challenge without an error' => array( 'challenge', 401, 'Unauthorized', true ),
            'insufficient scope' => array( 'scope', 403, 'Forbidden', true ),
            'ezpRestHttpResponse' => array( 'http', 401, 'Unauthorized', false ),
            'ezpRestStatusResponse' => array( 'response', 404, 'Not Found', false ),
            'no status' => array( 'none', 200, 'OK', false ),
        );
    }

    /**
     * @dataProvider statuses
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('statuses')]
    public function testTheStatusLineIsWellFormed( $status, $code, $phrase, $challenge )
    {
        $head = $this->head( '/?status=' . $status );

        $this->assertSame( "HTTP/1.1 $code $phrase", $head[0] );
        $this->assertMatchesRegularExpression( '#^HTTP/\d\.\d \d{3} [^\r\n:]*$#', $head[0] );
        foreach ( array_slice( $head, 1 ) as $line )
        {
            $this->assertStringStartsNotWith( 'HTTP/', $line, 'a status line was sent as a header' );
        }
        $this->assertSame( $challenge, (bool)preg_grep( '/^WWW-Authenticate: /i', $head ) );
    }

    public function testTheStatusLineNamesTheProtocolOfAnHttp10Request()
    {
        $head = $this->head( '/?status=challenge', 'HTTP/1.0' );

        $this->assertSame( 'HTTP/1.0 401 Unauthorized', $head[0] );
    }

    public function testTakeStatusCodeRemovesEveryStatusEntryAndKeepsTheLast()
    {
        $headers = array( 'WWW-Authenticate' => 'x', 'HTTP/1.1 401' => '', 'Content-Type' => 'text/plain', 'HTTP/1.0 403' => 'Forbidden' );

        $this->assertSame( 403, ezpRestHttpResponseWriter::takeStatusCode( $headers ) );
        $this->assertSame( array( 'WWW-Authenticate' => 'x', 'Content-Type' => 'text/plain' ), $headers );

        $none = array( 'Content-Type' => 'text/plain' );
        $this->assertNull( ezpRestHttpResponseWriter::takeStatusCode( $none ) );
    }
}
?>
