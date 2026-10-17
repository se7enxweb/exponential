<?php
/**
 * Every web request is classified exactly once, whichever way it ends (expRequestLog::begin() and finish(),
 * doc/features/6.0/request-logs.md): the normal end (ezpKernelWeb::shutdown()), eZExecution::cleanExit() (a download,
 * an ezjscore call, a redirect, a view that sends its own output), the error module, or both.
 *
 *  RE-1  begin() registers finish() as eZExecution's clean-up handler, once, however often it is called in a request
 *  RE-2  a download probe that ends through the clean-up (cleanExit) has one line in security.log
 *  RE-3  an ezjscore call with an injection in its query has one line in security.log
 *  RE-4  a normal download and a normal ezjscore call are not flagged: no line in security.log or 404.log
 *  RE-5  the normal end then the clean-up, and the error module then the clean-up: still one line
 *  RE-6  a real eZExecution::cleanExit() in a process of its own (as under PHP-FPM): one line
 *  RE-7  a persistent worker (Velocity): requests in a row in one process, each with its own REQUEST_TIME_FLOAT,
 *        each probe once, nothing carried into the next request, one handler per request
 *
 * The real classifier (built-in rule sets) with a test configuration; logs below var/tmp of the installation. No
 * database, no kernel bootstrap. eZExecution's statics, the exception handler and REQUEST_TIME_FLOAT are put back
 * afterwards; the shutdown functions registered here belong to requests that are over when PHPUnit ends.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/requestlog/expRequestLogRequestEndTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expRequestLogRequestEndTest extends PHPUnit\Framework\TestCase
{
    const DOWNLOAD_PROBE = '/content/download/../../etc/passwd';
    const EZJSCORE_PROBE = '/ezjscore/call/ezjscnode::subtree?id=1%27%20UNION%20SELECT%20password%20FROM%20ezuser--';
    const DOWNLOAD = '/content/download/57/312/file/annual-report.pdf';
    const EZJSCORE = '/ezjscore/call/ezjscnode::subtree::2::10?ContentType=json';

    private static $names = array( 'requestKey', 'hasCleanExit', 'shutdownHandle', 'fatalErrorHandlers', 'cleanupHandlers' );
    private $saved = array();
    private $props = array();
    private $server = array();
    private $access;
    private $exceptionHandler;
    private $dir;
    private $time = 5000.0;

    protected function setUp(): void
    {
        foreach ( self::$names as $name )
        {
            $this->props[$name] = new ReflectionProperty( 'eZExecution', $name );
            if ( PHP_VERSION_ID < 80100 )
                $this->props[$name]->setAccessible( true );
            $this->saved[$name] = $this->props[$name]->getValue();
        }
        foreach ( array( 'REQUEST_TIME_FLOAT', 'REQUEST_METHOD', 'REQUEST_URI', 'REMOTE_ADDR', 'HTTP_USER_AGENT', 'HTTP_REFERER' ) as $key )
            $this->server[$key] = array_key_exists( $key, $_SERVER ) ? array( $_SERVER[$key] ) : null;
        $this->access = array_key_exists( 'eZCurrentAccess', $GLOBALS ) ? array( $GLOBALS['eZCurrentAccess'] ) : null;
        $this->exceptionHandler = set_exception_handler( null );

        chdir( dirname( __DIR__, 4 ) );
        $this->dir = getcwd() . '/var/tmp/requestend_test_' . bin2hex( random_bytes( 6 ) );
        mkdir( $this->dir, 0700, true );
        expRequestLog::reset();
        expRequestLog::setClassifier( null );
        expRequestLog::setLogDirectory( $this->dir );
        expRequestLog::setSettings( array( 'NotFoundLog' => 'enabled', 'NotFoundLogFile' => '404.log',
                                           'MaskIP' => 'enabled', 'MaxURILength' => '300' ) );
        expRequestLog::setLegacyWriter( static function ( $message ) {} );
    }

    protected function tearDown(): void
    {
        foreach ( self::$names as $name )
            $this->props[$name]->setValue( null, $this->saved[$name] );
        foreach ( $this->server as $key => $value )
        {
            if ( $value === null )
                unset( $_SERVER[$key] );
            else
                $_SERVER[$key] = $value[0];
        }
        if ( $this->access === null )
            unset( $GLOBALS['eZCurrentAccess'] );
        else
            $GLOBALS['eZCurrentAccess'] = $this->access[0];
        set_exception_handler( $this->exceptionHandler );

        expRequestLog::reset();
        expRequestLog::setLogDirectory( null );
        expRequestLog::setSettings( null );
        expRequestLog::setLegacyWriter( null );
        expRequestClassifier::reset( true );
        self::remove( $this->dir );
    }

    private static function remove( $dir )
    {
        if ( !is_dir( $dir ) )
            return;
        foreach ( scandir( $dir ) as $name )
        {
            if ( $name === '.' || $name === '..' )
                continue;
            $path = $dir . '/' . $name;
            if ( is_dir( $path ) && !is_link( $path ) )
                self::remove( $path );
            else
                unlink( $path );
        }
        rmdir( $dir );
    }

    /** The classifier with the built-in rule sets, writing below the test's directory */
    private function classifier()
    {
        return new expRequestClassifier( array(
            'compileCache' => false,
            'salt' => 'test-salt',
            'cacheDir' => $this->dir . '/cache',
            'logDir' => $this->dir,
        ) );
    }

    /**
     * What a new request does up to the view, as ezpKernelWeb does it: a new REQUEST_TIME_FLOAT (a persistent
     * worker sets one per request), the request in $_SERVER, the classifier reset (the kernel's constructor) and
     * begin() (run()).
     */
    private function startRequest( $uri )
    {
        $this->time += 1.25;
        $_SERVER['REQUEST_TIME_FLOAT'] = $this->time;
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = $uri;
        $_SERVER['REMOTE_ADDR'] = '203.0.113.9';
        $_SERVER['HTTP_USER_AGENT'] = 'curl/8.5.0';
        unset( $_SERVER['HTTP_REFERER'] );
        $GLOBALS['eZCurrentAccess'] = array( 'name' => 'site', 'type' => 1, 'uri_part' => array() );
        expRequestLog::reset();
        expRequestClassifier::reset();
        expRequestClassifier::setInstance( $this->classifier() );
        expRequestLog::begin();
    }

    /** The end of eZExecution::cleanExit() without its exit */
    private function endByCleanExit()
    {
        eZExecution::cleanup();
        eZExecution::setCleanExit();
    }

    /** The end of ezpKernelWeb::shutdown() */
    private function endNormally()
    {
        expRequestLog::finish();
        eZExecution::cleanup();
        eZExecution::setCleanExit();
    }

    /** @return string[] the lines of a log in the test's directory */
    private function lines( $file )
    {
        $path = $this->dir . '/' . $file;
        clearstatcache( true, $path );
        return is_file( $path ) ? file( $path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES ) : array();
    }

    /** @return string[] the security.log lines of the request with this path */
    private function securityLinesFor( $uri )
    {
        $path = parse_url( $uri, PHP_URL_PATH );
        $out = array();
        foreach ( $this->lines( 'security.log' ) as $line )
        {
            if ( strpos( $line, ' ' . expRequestClassifier::uriForLog( $path ) ) !== false || strpos( $line, ' ' . $path ) !== false )
                $out[] = $line;
        }
        return $out;
    }

    /** @return int how many of this request's clean-up handlers are expRequestLog::finish() */
    private function finishHandlers()
    {
        $n = 0;
        foreach ( eZExecution::cleanupHandlers() as $handler )
        {
            if ( $handler === array( 'expRequestLog', 'finish' ) )
                $n++;
        }
        return $n;
    }

    /** RE-1 */
    public function testBeginRegistersTheCleanupHandlerOnce()
    {
        $this->startRequest( self::DOWNLOAD );
        expRequestLog::begin();
        expRequestLog::begin();
        $this->assertSame( 1, $this->finishHandlers() );
        $this->assertFalse( expRequestLog::finished() );
        $this->endByCleanExit();
        $this->assertTrue( expRequestLog::finished() );
    }

    /** RE-2 */
    public function testDownloadProbeEndingInCleanExitIsClassified()
    {
        $this->startRequest( self::DOWNLOAD_PROBE );
        $this->endByCleanExit();
        $this->assertCount( 1, $this->lines( 'security.log' ) );
        $this->assertCount( 1, $this->securityLinesFor( self::DOWNLOAD_PROBE ) );
        $this->assertSame( array(), $this->lines( '404.log' ) );
    }

    /** RE-3 */
    public function testEzjscoreInjectionEndingInCleanExitIsClassified()
    {
        $this->startRequest( self::EZJSCORE_PROBE );
        $this->endByCleanExit();
        $lines = $this->lines( 'security.log' );
        $this->assertCount( 1, $lines );
        $this->assertStringContainsString( 'injection', $lines[0] );
        $this->assertStringContainsString( '/ezjscore/call/', $lines[0] );
        // the client address is masked (the query stays: it is the evidence, doc/features/6.0/request-logs.md)
        $this->assertStringContainsString( '[203.0.x.x]', $lines[0] );
        $this->assertStringNotContainsString( '203.0.113.9', $lines[0] );
    }

    /** RE-4 */
    public function testNormalDownloadAndEzjscoreCallAreNotFlagged()
    {
        foreach ( array( self::DOWNLOAD, self::EZJSCORE ) as $uri )
        {
            $this->startRequest( $uri );
            $this->endByCleanExit();
            $this->assertTrue( expRequestLog::finished(), $uri );
        }
        $this->assertSame( array(), $this->lines( 'security.log' ) );
        $this->assertSame( array(), $this->lines( '404.log' ) );
    }

    /** RE-5 */
    public function testEveryEndingTogetherWritesOneLine()
    {
        // the normal end: shutdown() calls finish(), then eZExecution::cleanup() calls it again
        $this->startRequest( self::DOWNLOAD_PROBE );
        $this->endNormally();
        $this->assertCount( 1, $this->lines( 'security.log' ) );

        // the error module, then a clean exit and the normal end on top
        $this->startRequest( self::EZJSCORE_PROBE );
        $this->assertTrue( expRequestLog::handleError( 404, 'kernel', 1 ) );
        $this->endByCleanExit();
        $this->endNormally();
        $this->assertCount( 2, $this->lines( 'security.log' ) );
        $this->assertSame( array(), $this->lines( '404.log' ) );

        // a not-found that is no probe: its 404 line once, whether the clean-up or shutdown ends it
        $this->startRequest( '/site/no-such-page' );
        expRequestLog::reason( 'module', 'no-such-page' );
        expRequestLog::finish( 404 );
        $this->endByCleanExit();
        $this->assertCount( 1, $this->lines( '404.log' ) );
        $this->assertCount( 2, $this->lines( 'security.log' ) );
    }

    /** RE-6 */
    public function testRealCleanExitInAProcessOfItsOwn()
    {
        $script = __DIR__ . '/fixtures/request_ending_in_clean_exit.php';
        foreach ( array( self::DOWNLOAD_PROBE, self::DOWNLOAD ) as $uri )
        {
            $command = escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( $script ) . ' ' . escapeshellarg( getcwd() )
                     . ' ' . escapeshellarg( $this->dir ) . ' ' . escapeshellarg( $uri ) . ' 2>&1';
            $output = array();
            exec( $command, $output, $code );
            $this->assertSame( 0, $code, implode( "\n", $output ) );
            $this->assertSame( 'file body', implode( "\n", $output ), 'the clean exit ends the request' );
        }
        $this->assertCount( 1, $this->lines( 'security.log' ), 'the probe once, the normal download not' );
        $this->assertCount( 1, $this->securityLinesFor( self::DOWNLOAD_PROBE ) );
        $this->assertSame( array(), $this->lines( '404.log' ) );
    }

    /** RE-7 */
    public function testPersistentWorkerClassifiesEachRequestOnce()
    {
        $requests = array(
            array( self::DOWNLOAD_PROBE, 'cleanExit' ),
            array( self::DOWNLOAD, 'cleanExit' ),
            array( self::EZJSCORE_PROBE, 'cleanExit' ),
            array( self::EZJSCORE, 'cleanExit' ),
            array( '/site/Fit-Healthy', 'normal' ),
            array( self::DOWNLOAD_PROBE, 'normal' ),
            array( self::DOWNLOAD, 'normal' ),
            array( self::EZJSCORE_PROBE, 'cleanExit' ),
        );
        $expected = array( self::DOWNLOAD_PROBE => 0, self::EZJSCORE_PROBE => 0 );
        foreach ( $requests as $i => $request )
        {
            list( $uri, $end ) = $request;
            $this->startRequest( $uri );
            $this->assertSame( 1, $this->finishHandlers(), "request $i: one handler, none left from the request before" );
            if ( $end === 'cleanExit' )
                $this->endByCleanExit();
            else
                $this->endNormally();
            if ( isset( $expected[$uri] ) )
                $expected[$uri]++;
            $this->assertCount( array_sum( $expected ), $this->lines( 'security.log' ), "request $i ($uri)" );
        }
        $this->assertCount( 2, $this->securityLinesFor( self::DOWNLOAD_PROBE ) );
        $this->assertCount( 2, $this->securityLinesFor( self::EZJSCORE_PROBE ) );
        $this->assertSame( array(), $this->lines( '404.log' ) );

        // a clean-up run again after the request has ended (a worker's next request has not begun) adds nothing
        eZExecution::cleanup();
        $this->assertCount( 4, $this->lines( 'security.log' ) );
    }
}
