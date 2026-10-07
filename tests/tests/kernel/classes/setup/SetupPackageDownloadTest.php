<?php
/**
 * The setup's package download (eZStepSiteTypes::downloadFile()) tries a failed
 * download again and says why it failed: HTTP status, curl error, bytes received.
 * A local PHP web server in var/tmp serves the fixtures; tiny retry delays.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class SetupPackageDownloadTest extends PHPUnit\Framework\TestCase
{
    private static $docRoot;
    private static $outDir;
    private static $port;
    private static $server;
    private static $pipes;

    public static function setUpBeforeClass(): void
    {
        if ( !extension_loaded( 'curl' ) )
            return;

        $root = dirname( __DIR__, 5 ) . '/var/tmp/setup-package-download-test-' . getmypid();
        self::$docRoot = $root . '/docroot';
        self::$outDir = $root . '/out';
        @mkdir( self::$docRoot, 0777, true );
        @mkdir( self::$outDir, 0777, true );
        file_put_contents( self::$docRoot . '/router.php', self::routerSource() );

        self::$port = self::freePort();
        $command = escapeshellarg( PHP_BINARY ) . ' -S 127.0.0.1:' . self::$port . ' -t ' . escapeshellarg( self::$docRoot ) .
                   ' ' . escapeshellarg( self::$docRoot . '/router.php' );
        self::$server = proc_open( 'exec ' . $command, array( 0 => array( 'pipe', 'r' ), 1 => array( 'file', $root . '/server.log', 'a' ),
                                                             2 => array( 'file', $root . '/server.log', 'a' ) ), self::$pipes );

        // Wait until it accepts connections
        for ( $i = 0; $i < 100; ++$i )
        {
            $socket = @fsockopen( '127.0.0.1', self::$port, $errno, $errstr, 0.1 );
            if ( $socket )
            {
                fclose( $socket );
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
        if ( self::$docRoot )
        {
            $root = dirname( self::$docRoot );
            foreach ( array_merge( glob( self::$docRoot . '/*' ), glob( self::$outDir . '/*' ) ) as $file )
                @unlink( $file );
            @rmdir( self::$docRoot );
            @rmdir( self::$outDir );
            @unlink( $root . '/server.log' );
            @rmdir( $root );
        }
    }

    protected function setUp(): void
    {
        if ( !extension_loaded( 'curl' ) )
            $this->markTestSkipped( 'curl is not loaded' );
        foreach ( glob( self::$outDir . '/*' ) as $file )
            @unlink( $file );
        @unlink( self::$docRoot . '/flaky.count' );
    }

    private static function routerSource()
    {
        return <<<'ROUTER'
<?php
$path = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
$package = gzencode( str_repeat( 'package data ', 100 ) );
switch ( $path )
{
    case '/ok.ezpkg':
        header( 'Content-Type: application/octet-stream' );
        echo $package;
        break;
    case '/flaky.ezpkg':
        $counter = __DIR__ . '/flaky.count';
        $count = file_exists( $counter ) ? (int)file_get_contents( $counter ) : 0;
        file_put_contents( $counter, $count + 1 );
        if ( $count === 0 )
        {
            http_response_code( 503 );
            echo 'republishing';
            break;
        }
        echo $package;
        break;
    case '/empty.ezpkg':
        break;
    case '/truncated.ezpkg':
        header( 'Content-Length: ' . strlen( $package ) );
        echo substr( $package, 0, 10 );
        break;
    case '/page.ezpkg':
        echo '<html>not a package</html>';
        break;
    default:
        http_response_code( 404 );
        echo 'File not found';
}
ROUTER;
    }

    private static function freePort()
    {
        $socket = stream_socket_server( 'tcp://127.0.0.1:0', $errno, $errstr );
        $name = stream_socket_get_name( $socket, false );
        fclose( $socket );
        return (int)substr( $name, strrpos( $name, ':' ) + 1 );
    }

    private function downloader( $transport = 'curl' )
    {
        $class = new ReflectionClass( 'eZStepSiteTypes' );
        $step = $class->newInstanceWithoutConstructor();
        $step->DownloadTransport = $transport;
        $step->DownloadSettings = array( 'DownloadAttempts' => 3,
                                         'DownloadRetryDelays' => array( 0.01, 0.02 ),
                                         'DownloadTimeout' => 10,
                                         'DownloadConnectTimeout' => 5 );
        return $step;
    }

    private function url( $name, $port = null )
    {
        return 'http://127.0.0.1:' . ( $port ?: self::$port ) . '/' . $name;
    }

    public function testSuccess()
    {
        $step = $this->downloader();
        $file = $step->downloadFile( $this->url( 'ok.ezpkg' ), self::$outDir );
        $this->assertSame( self::$outDir . '/ok.ezpkg', $file );
        $this->assertSame( "\x1f\x8b", substr( file_get_contents( $file ), 0, 2 ) );
        $this->assertSame( 1, $step->DownloadAttemptCount );
        $this->assertFalse( $step->ErrorMsg );
    }

    public function testForcedFileName()
    {
        $step = $this->downloader();
        $file = $step->downloadFile( $this->url( 'ok.ezpkg' ), self::$outDir, 'index.xml' );
        $this->assertSame( self::$outDir . '/index.xml', $file );
    }

    public function testNotFoundSaysTheStatus()
    {
        $step = $this->downloader();
        $this->assertFalse( $step->downloadFile( $this->url( 'missing.ezpkg' ), self::$outDir ) );
        $this->assertSame( 3, $step->DownloadAttemptCount );
        $this->assertStringContainsString( 'HTTP 404', $step->ErrorMsg );
        $this->assertStringContainsString( 'bytes received', $step->ErrorMsg );
        $this->assertFileDoesNotExist( self::$outDir . '/missing.ezpkg' );
    }

    public function testRetrySucceedsAfterServiceUnavailable()
    {
        $step = $this->downloader();
        $file = $step->downloadFile( $this->url( 'flaky.ezpkg' ), self::$outDir );
        $this->assertSame( self::$outDir . '/flaky.ezpkg', $file );
        $this->assertSame( 2, $step->DownloadAttemptCount );
        $this->assertSame( "\x1f\x8b", substr( file_get_contents( $file ), 0, 2 ) );
    }

    public function testEmptyFileFails()
    {
        $step = $this->downloader();
        $this->assertFalse( $step->downloadFile( $this->url( 'empty.ezpkg' ), self::$outDir ) );
        $this->assertStringContainsString( 'empty file, 0 bytes received', $step->ErrorMsg );
        $this->assertFileDoesNotExist( self::$outDir . '/empty.ezpkg' );
    }

    public function testTruncatedFileFails()
    {
        $step = $this->downloader();
        $this->assertFalse( $step->downloadFile( $this->url( 'truncated.ezpkg' ), self::$outDir ) );
        $this->assertSame( 3, $step->DownloadAttemptCount );
        $this->assertMatchesRegularExpression( '/curl error 18|truncated/', $step->ErrorMsg );
        $this->assertStringContainsString( '10 bytes received', $step->ErrorMsg );
        $this->assertFileDoesNotExist( self::$outDir . '/truncated.ezpkg' );
    }

    public function testPageInsteadOfPackageFails()
    {
        $step = $this->downloader();
        $this->assertFalse( $step->downloadFile( $this->url( 'page.ezpkg' ), self::$outDir ) );
        $this->assertStringContainsString( 'not a gzip-compressed package', $step->ErrorMsg );
    }

    public function testConnectionRefusedFailsFastWithTheReason()
    {
        $step = $this->downloader();
        $started = microtime( true );
        $this->assertFalse( $step->downloadFile( $this->url( 'ok.ezpkg', self::freePort() ), self::$outDir ) );
        $this->assertLessThan( 5, microtime( true ) - $started );
        $this->assertSame( 3, $step->DownloadAttemptCount );
        $this->assertMatchesRegularExpression( '/curl error 7: .*(refused|connect)/i', $step->ErrorMsg );
        $this->assertFileDoesNotExist( self::$outDir . '/ok.ezpkg' );
    }

    public function testStreamTransportReportsTheStatusAndRetries()
    {
        $step = $this->downloader( 'stream' );
        $this->assertFalse( $step->downloadFile( $this->url( 'missing.ezpkg' ), self::$outDir ) );
        $this->assertStringContainsString( 'HTTP 404', $step->ErrorMsg );
        $this->assertSame( 3, $step->DownloadAttemptCount );

        $step = $this->downloader( 'stream' );
        $this->assertSame( self::$outDir . '/flaky.ezpkg', $step->downloadFile( $this->url( 'flaky.ezpkg' ), self::$outDir ) );
        $this->assertSame( 2, $step->DownloadAttemptCount );
    }

    public function testPackageErrorMessageNamesTheReason()
    {
        $step = new class extends eZStepSiteTypes
        {
            public function __construct()
            {
            }

            function downloadFile( $url, $outDir, $forcedFileName = false )
            {
                $this->DownloadAttemptCount = 3;
                $this->ErrorMsg = 'HTTP 404, 9 bytes received';
                return false;
            }
        };
        $this->assertFalse( $step->downloadAndImportPackage( 'sevenx_multisite', $this->url( 'missing.ezpkg' ), false, false, true ) );
        $this->assertStringContainsString( "Download of package 'sevenx_multisite' failed after 3 attempts: HTTP 404, 9 bytes received.",
                                           $step->ErrorMsg );
    }
}
