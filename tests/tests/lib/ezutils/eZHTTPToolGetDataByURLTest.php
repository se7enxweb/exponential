<?php
/**
 * eZHTTPTool::getDataByURL() fetches only http and https and never returns an error page as data:
 *
 *  GDU-01  file://, php://, ftp://, data:, gopher://, a path, an address without a host, control characters and
 *          non-strings are refused before any request (isHTTPURL())
 *  GDU-02  a status >= 400 is false, also after a redirect; 200 is the body, also after a redirect
 *  GDU-03  $justCheckURL: true for 200, false for 404 and 500
 *  GDU-04  a redirect to file:// is not followed
 *  GDU-05  responseStatus(): the status of the last response in a stream's headers (the fopen() way), with the
 *          headers of a real stream from the server
 *  GDU-06  an address without a scheme is read as http (withDefaultScheme()): "127.0.0.1:<port>/ok" and
 *          "//127.0.0.1:<port>/ok" return the body, as RSS sources entered without "http://" did before
 *
 * GDU-02 to GDU-05 use PHP's built-in server on a free port of 127.0.0.1 (fixtures/http_status_router.php), no
 * other network; the proxy of site.ini is switched off for the test.
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezutils
 */

class eZHTTPToolGetDataByURLTest extends PHPUnit\Framework\TestCase
{
    private static $server;
    private static $base;
    private $proxy;

    public static function setUpBeforeClass(): void
    {
        $probe = stream_socket_server( 'tcp://127.0.0.1:0', $errno, $errstr );
        if ( !$probe )
            return;
        $port = (int)substr( strrchr( stream_socket_get_name( $probe, false ), ':' ), 1 );
        fclose( $probe );
        $command = array( PHP_BINARY, '-n', '-S', '127.0.0.1:' . $port, __DIR__ . '/fixtures/http_status_router.php' );
        $null = DIRECTORY_SEPARATOR === '\\' ? 'NUL' : '/dev/null';
        self::$server = proc_open( $command, array( 0 => array( 'pipe', 'r' ), 1 => array( 'file', $null, 'a' ), 2 => array( 'file', $null, 'a' ) ), $pipes );
        if ( !is_resource( self::$server ) )
            return;
        for ( $i = 0; $i < 100; $i++ )
        {
            $socket = @fsockopen( '127.0.0.1', $port, $errno, $errstr, 0.1 );
            if ( $socket )
            {
                fclose( $socket );
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
            proc_terminate( self::$server );
            proc_close( self::$server );
        }
        self::$server = null;
        self::$base = null;
    }

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $ini = eZINI::instance();
        $this->proxy = $ini->hasVariable( 'ProxySettings', 'ProxyServer' ) ? $ini->variable( 'ProxySettings', 'ProxyServer' ) : null;
        $ini->setVariable( 'ProxySettings', 'ProxyServer', '' );
    }

    protected function tearDown(): void
    {
        if ( $this->proxy !== null )
            eZINI::instance()->setVariable( 'ProxySettings', 'ProxyServer', $this->proxy );
    }

    private function base()
    {
        if ( self::$base === null )
            $this->fail( 'the built-in server did not start' );
        return self::$base;
    }

    /** GDU-01 */
    public function testOnlyHTTPAndHTTPSWithAHostAreFetched()
    {
        $hosts = PHP_OS_FAMILY === 'Windows' ? 'C:/Windows/System32/drivers/etc/hosts' : '/etc/hosts';
        foreach ( array( 'file://' . $hosts, 'FILE://' . $hosts, $hosts, 'php://filter/resource=' . $hosts, 'php://memory',
                         'ftp://127.0.0.1/x', 'data:text/plain,abc', 'gopher://127.0.0.1:25/x', 'dict://127.0.0.1:11211/stat',
                         'http:///x', 'http:/x', "http://127.0.0.1/x\r\nHost: evil", '' ) as $url )
        {
            $this->assertFalse( eZHTTPTool::isHTTPURL( $url ), json_encode( $url ) );
            $this->assertFalse( eZHTTPTool::getDataByURL( $url ), json_encode( $url ) );
            $this->assertFalse( eZHTTPTool::getDataByURL( $url, true ), json_encode( $url ) );
        }
        foreach ( array( null, false, 42, array( 'http://127.0.0.1/' ), '//127.0.0.1/x' ) as $url )
            $this->assertFalse( eZHTTPTool::isHTTPURL( $url ), json_encode( $url ) );
        foreach ( array( 'http://example.com', 'HTTPS://example.com/feed.xml?x=1', 'http://127.0.0.1:8080/x' ) as $url )
            $this->assertTrue( eZHTTPTool::isHTTPURL( $url ), $url );
    }

    /** GDU-02 */
    public function testAStatusOf400OrMoreIsFalse()
    {
        $base = $this->base();
        $this->assertSame( 'the feed', eZHTTPTool::getDataByURL( $base . '/ok' ) );
        $this->assertSame( 'the feed', eZHTTPTool::getDataByURL( $base . '/moved' ) );
        foreach ( array( '/missing', '/broken', '/moved-missing' ) as $path )
            $this->assertFalse( eZHTTPTool::getDataByURL( $base . $path ), $path );
    }

    /** GDU-03 */
    public function testJustCheckingTheURL()
    {
        $base = $this->base();
        $this->assertTrue( eZHTTPTool::getDataByURL( $base . '/ok', true ) );
        $this->assertTrue( eZHTTPTool::getDataByURL( $base . '/moved', true ) );
        foreach ( array( '/missing', '/broken', '/moved-missing' ) as $path )
            $this->assertFalse( eZHTTPTool::getDataByURL( $base . $path, true ), $path );
    }

    /** GDU-04 */
    public function testARedirectToAFileIsNotFollowed()
    {
        $this->assertFalse( eZHTTPTool::getDataByURL( $this->base() . '/moved-file' ) );
    }

    /** GDU-06 */
    public function testAnAddressWithoutSchemeIsReadAsHTTP()
    {
        foreach ( array( 'www.example.com/feed' => 'http://www.example.com/feed', 'example.com' => 'http://example.com',
                         'localhost:8080/x' => 'http://localhost:8080/x', 'www.example.com:8080' => 'http://www.example.com:8080',
                         '//example.com/x' => 'http://example.com/x', 'http://example.com' => 'http://example.com',
                         'HTTPS://example.com' => 'HTTPS://example.com', 'file:///etc/hosts' => 'file:///etc/hosts',
                         'data:text/plain,abc' => 'data:text/plain,abc', '/etc/hosts' => '/etc/hosts', '\\server\x' => '\\server\x',
                         '' => '' ) as $url => $expected )
            $this->assertSame( $expected, eZHTTPTool::withDefaultScheme( $url ), $url );
        $this->assertNull( eZHTTPTool::withDefaultScheme( null ) );

        $hostAndPort = substr( $this->base(), strlen( 'http://' ) );
        $this->assertSame( 'the feed', eZHTTPTool::getDataByURL( $hostAndPort . '/ok' ) );
        $this->assertSame( 'the feed', eZHTTPTool::getDataByURL( '//' . $hostAndPort . '/ok' ) );
        $this->assertTrue( eZHTTPTool::getDataByURL( $hostAndPort . '/ok', true ) );
        $this->assertFalse( eZHTTPTool::getDataByURL( $hostAndPort . '/missing' ) );
    }

    /** GDU-05 */
    public function testResponseStatusOfAStream()
    {
        $this->assertSame( 0, eZHTTPTool::responseStatus( null ) );
        $this->assertSame( 0, eZHTTPTool::responseStatus( array() ) );
        $this->assertSame( 0, eZHTTPTool::responseStatus( array( 'Content-Type: text/html' ) ) );
        $this->assertSame( 404, eZHTTPTool::responseStatus( array( 'HTTP/1.1 302 Found', 'Location: /x', 'HTTP/1.0 404 Not Found', 'X-A: HTTP/1.1 200 OK' ) ) );
        $this->assertSame( 200, eZHTTPTool::responseStatus( array( 'HTTP/2 200' ) ) );

        // what fopen() hands over with ignore_errors, after a redirect
        $context = stream_context_create( array( 'http' => array( 'ignore_errors' => true ) ) );
        foreach ( array( '/moved-missing' => 404, '/broken' => 500, '/moved' => 200 ) as $path => $status )
        {
            $fid = fopen( $this->base() . $path, 'r', false, $context );
            $this->assertNotFalse( $fid, $path );
            $metaData = stream_get_meta_data( $fid );
            fclose( $fid );
            $this->assertSame( $status, eZHTTPTool::responseStatus( $metaData['wrapper_data'] ), $path );
        }
    }
}
