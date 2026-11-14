<?php
/**
 * Answering image alias requests (expImageAliasServer), the placeholder, the lock, the log and the mode switch,
 * without a database:
 *
 *  IAS-01 - an alias that exists is answered from a signed URL without the database: a redirect to the storage URL
 *           (Delivery auto on a single server), the file itself when asked (Delivery stream) or for a storage path
 *  IAS-02 - a wrong signature still serves an existing file; for a missing one it gets the placeholder with 404
 *  IAS-03 - a URL whose file name or ids differ from what the token signs is refused; unknown URLs and paths outside
 *           the storage get the placeholder with 404
 *  IAS-04 - the placeholder: an SVG of the computed size, light and dark, no text, never cached, marked for the swap
 *           script only while an image is being made (200)
 *  IAS-05 - the lock: one per alias file, a second taker waits at most its limit and is told it timed out; the lock
 *           file is removed when given back
 *  IAS-06 - the log: one line per event, read back newest first, a URL with "]" in the request context; a timeout
 *           is event defer (deferred to a job), not fail, older "fail result=timeout" lines of an alias read as defer
 *  IAS-07 - the mode switch: on-demand by default, render on the command line and inside withRender()
 *  IAS-08 - request paths: the www directory, index_image.php/ and the query string are taken off
 *  IAS-09 - a web server in the CLI SAPI (Velocity, the built-in server) follows the settings like PHP-FPM: a request
 *           method and a Host header make it a web process; without them the command line rule (render) stays; a web
 *           process never lowers its priority
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/imagealias/expImageAliasServerTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

/** The server with the delivery setting fixed */
class expImageAliasServerTestServer extends expImageAliasServer
{
    /** @var string|null */
    public $method = null;

    public function deliveryMethod( $path )
    {
        return $this->method !== null ? $this->method : parent::deliveryMethod( $path );
    }
}

class expImageAliasServerTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $base;

    /** @var string */
    private $dir;

    /** @var string|false */
    private $cwd = false;

    public static function setUpBeforeClass(): void
    {
        eZExecution::registerShutdownHandler();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->cwd = getcwd();
        chdir( dirname( __DIR__, 5 ) );
        $this->base = 'var/tmp/storage/images/exp_ias_' . getmypid() . '_' . mt_rand();
        $this->dir = $this->base . '/5-1-eng-GB';
        mkdir( $this->dir, 0700, true );
        copy( dirname( __DIR__, 4 ) . '/tests/lib/ezimage/data/andernach.jpg', $this->dir . '/andernach.jpg' );
        copy( dirname( __DIR__, 4 ) . '/tests/lib/ezimage/data/andernach.jpg', $this->dir . '/andernach_small.jpg' );
        expImageAliasUrl::setKeys( str_repeat( 's', 64 ) );
        expImageAliasLock::setDirectory( $this->base . '/locks' );
        expImageAliasLog::setDirectory( $this->base . '/log' );
    }

    protected function tearDown(): void
    {
        expImageAliasUrl::resetKeys();
        expImageAliasLock::setDirectory( null );
        expImageAliasLog::setDirectory( null );
        expImageAliasLock::setWaitLimit( null );
        expImageAliasSettings::setMode( null );
        foreach ( array( $this->dir, $this->base . '/locks', $this->base . '/log' ) as $dir )
        {
            foreach ( (array)glob( $dir . '/*' ) as $file )
                @unlink( $file );
            @rmdir( $dir );
        }
        @rmdir( $this->base );
        if ( $this->cwd !== false )
            chdir( $this->cwd );
        parent::tearDown();
    }

    private function payload( $file, array $extra = array() )
    {
        return $extra + array( 'a' => 5, 'v' => 1, 'l' => 'eng-GB', 'n' => 'small', 'o' => $this->dir . '/andernach.jpg', 'u' => $file,
                               'w' => 160, 'h' => 120, 'm' => 'image/jpeg' );
    }

    private static function params( $url )
    {
        return explode( '/', substr( $url, strpos( $url, 'content/image/' ) + strlen( 'content/image/' ) ) );
    }

    /** IAS-01 */
    public function testAnExistingAliasIsServedWithoutTheDatabase()
    {
        $url = expImageAliasUrl::build( $this->payload( 'andernach_small.jpg' ), 'token', array( 'prefix' => '' ) );
        $server = new expImageAliasServerTestServer();
        $server->method = 'redirect';
        $response = $server->handleParams( self::params( $url ) );
        $this->assertSame( 302, $response['status'] );
        $this->assertStringEndsWith( '/' . $this->dir . '/andernach_small.jpg', $response['headers']['Location'] );
        $this->assertStringContainsString( 'max-age=' . expImageAliasSettings::cacheMaxAge(), $response['headers']['Cache-Control'] );

        $server->method = 'stream';
        $response = $server->handleParams( self::params( $url ) );
        $this->assertSame( 200, $response['status'] );
        $this->assertSame( $this->dir . '/andernach_small.jpg', $response['file'] );
        $this->assertSame( 'image/jpeg', $response['headers']['Content-Type'] );
        $this->assertSame( (string)filesize( $this->dir . '/andernach_small.jpg' ), $response['headers']['Content-Length'] );
        $this->assertStringContainsString( 'immutable', $response['headers']['Cache-Control'] );
        $this->assertArrayNotHasKey( 'X-Image-Placeholder', $response['headers'] );

        // The ids-token form, and a storage path: always the file (a redirect would point at itself)
        $server->method = 'redirect';
        $this->assertSame( 302, $server->handleParams( self::params( expImageAliasUrl::build( $this->payload( 'andernach_small.jpg' ), 'ids-token', array( 'prefix' => '' ) ) ) )['status'] );
        $storage = ( new expImageAliasServerTestServer() )->handleStoragePath( '/' . $this->dir . '/andernach_small.jpg' );
        $this->assertSame( 200, $storage['status'] );
        $this->assertSame( $this->dir . '/andernach_small.jpg', $storage['file'] );

        // A draft is streamed with Delivery=auto
        $this->assertSame( 'stream', ( new expImageAliasServer() )->deliveryMethod( 'var/site/storage/images-versioned/5/2-eng-GB/a_small.jpg' ) );
    }

    /** IAS-02 */
    public function testAWrongSignatureOnlyServesExistingFiles()
    {
        $token = expImageAliasUrl::token( $this->payload( 'andernach_small.jpg' ) );
        expImageAliasUrl::setKeys( str_repeat( 'x', 64 ) );
        $server = new expImageAliasServerTestServer();
        $server->method = 'redirect';
        $this->assertSame( 302, $server->handleParams( array( $token, 'andernach_small.jpg' ) )['status'], 'exists: served' );

        expImageAliasUrl::setKeys( str_repeat( 's', 64 ) );
        $missing = expImageAliasUrl::token( $this->payload( 'andernach_large.jpg', array( 'n' => 'large', 'w' => 640, 'h' => 427 ) ) );
        expImageAliasUrl::setKeys( str_repeat( 'x', 64 ) );
        $response = $server->handleParams( array( $missing, 'andernach_large.jpg' ) );
        $this->assertSame( 404, $response['status'] );
        $this->assertSame( '1', $response['headers']['X-Image-Placeholder'] );
        $this->assertStringContainsString( 'width="640" height="427"', $response['body'] );
        $this->assertArrayNotHasKey( 'Server-Timing', $response['headers'], 'refused: the swap script does not ask again' );
        $this->assertFileDoesNotExist( $this->dir . '/andernach_large.jpg', 'nothing made' );

        // An expired draft token: the same
        expImageAliasUrl::setKeys( str_repeat( 's', 64 ) );
        $expired = expImageAliasUrl::token( $this->payload( 'andernach_large.jpg', array( 'x' => time() - 10 ) ) );
        $this->assertSame( 404, $server->handleParams( array( $expired, 'andernach_large.jpg' ) )['status'] );
    }

    /** IAS-03 */
    public function testMismatchesAndUnknownURLsAreRefused()
    {
        $server = new expImageAliasServerTestServer();
        $server->method = 'redirect';
        $token = expImageAliasUrl::token( $this->payload( 'andernach_small.jpg' ) );
        // The signed file name is andernach_small.jpg; the original next to it may not be asked for with it
        $this->assertSame( 404, $server->handleParams( array( $token, 'andernach.jpg' ) )['status'] );
        $this->assertSame( 404, $server->handleParams( array( '6', '1', 'eng-GB', 'small', $token, 'andernach_small.jpg' ) )['status'], 'other ids' );
        $this->assertSame( 404, $server->handleParams( array( '5', '1', 'eng-GB', 'large', $token, 'andernach_small.jpg' ) )['status'], 'other alias' );
        $this->assertSame( 404, $server->handleParams( array( 'x', 'y', 'z', 'w' ) )['status'] );
        $this->assertSame( 404, $server->handleParams( array() )['status'] );
        foreach ( array( 'settings/site.ini', '/var/site/storage/../../settings/site.ini', 'index.php', 'var/site/cache/x/1-1-eng-GB/a_small.jpg' ) as $path )
        {
            $response = ( new expImageAliasServer() )->handleStoragePath( $path );
            $this->assertSame( 404, $response['status'], $path );
            $this->assertNull( $response['file'], $path );
        }
    }

    /** IAS-04 */
    public function testThePlaceholder()
    {
        $svg = expImagePlaceholder::svg( 300, 168 );
        $xml = simplexml_load_string( $svg );
        $this->assertNotFalse( $xml, 'well-formed' );
        $this->assertSame( array( '300', '168', '0 0 300 168' ), array( (string)$xml['width'], (string)$xml['height'], (string)$xml['viewBox'] ) );
        $this->assertStringContainsString( 'prefers-color-scheme:dark', $svg, 'follows dark mode' );
        $this->assertDoesNotMatchRegularExpression( '/<(text|image|script|a|foreignObject)\b/', $svg, 'no text, no logo, nothing active' );
        $this->assertStringNotContainsString( 'Exponential', $svg );

        $this->assertSame( array( 300, 200 ), expImagePlaceholder::size( null, false ), 'unknown size' );
        $this->assertSame( array( 120, 80 ), expImagePlaceholder::size( 120, null ) );
        $this->assertSame( array( 8000, 8000 ), expImagePlaceholder::size( 99999, '99999' ), 'bounded' );
        $this->assertSame( array( 1, 1 ), expImagePlaceholder::size( 1.4, '1' ) );

        $server = new expImageAliasServer();
        $pending = $server->placeholder( 160, 120, 200, 'pending' );
        $this->assertSame( 200, $pending['status'] );
        $this->assertSame( 'no-store, max-age=0', $pending['headers']['Cache-Control'] );
        $this->assertSame( 'image/svg+xml; charset=utf-8', $pending['headers']['Content-Type'] );
        $this->assertSame( expImageAliasSettings::swapScriptEnabled() ? 'exp-image-placeholder' : null, $pending['headers']['Server-Timing'] ?? null );
        $this->assertSame( (string)strlen( $pending['body'] ), $pending['headers']['Content-Length'] );
        $refused = $server->placeholder( 160, 120, 404, 'signature' );
        $this->assertArrayNotHasKey( 'Server-Timing', $refused['headers'] );
    }

    /** IAS-05 */
    public function testTheLockOfOneAliasFile()
    {
        $first = new expImageAliasLock( $this->dir . '/andernach_large.jpg' );
        $this->assertTrue( $first->acquire( 1 ) );
        $this->assertFalse( $first->waited() );
        $this->assertFileExists( $first->lockFile() );

        // Another alias of the same image is not held up
        $other = new expImageAliasLock( $this->dir . '/andernach_small.jpg' );
        $this->assertTrue( $other->acquire( 0 ) );
        $other->release();

        $second = new expImageAliasLock( $this->dir . '/andernach_large.jpg' );
        $start = microtime( true );
        $this->assertFalse( $second->acquire( 0.3 ) );
        $elapsed = microtime( true ) - $start;
        $this->assertTrue( $second->timedOut() );
        $this->assertTrue( $second->waited() );
        $this->assertGreaterThanOrEqual( 0.29, $elapsed, 'waited its limit' );
        $this->assertLessThan( 2.0, $elapsed, 'and not for ever' );

        $first->release();
        $this->assertFileDoesNotExist( $first->lockFile(), 'removed when given back' );
        $this->assertTrue( $second->acquire( 0 ) );
        $second->release();

        // The process's limit (content/image: MaxWait) over LockTimeout
        $before = expImageAliasLock::setWaitLimit( 1.5 );
        $this->assertSame( 1.5, expImageAliasLock::waitLimit() );
        expImageAliasLock::setWaitLimit( $before );
        $this->assertEquals( expImageAliasSettings::lockTimeout(), expImageAliasLock::waitLimit() );
    }

    /** IAS-06 */
    public function testTheLog()
    {
        $this->assertTrue( expImageAliasLog::write( 'generate', array( 'result' => 'ok', 'ms' => 12, 'alias' => 'small', 'path' => 'var/site/storage/images/a b/1-1-eng-GB/a_small.jpg' ) ) );
        $this->assertTrue( expImageAliasLog::write( 'placeholder', array( 'result' => 'signature', 'alias' => 'large', 'status' => 404 ) ) );
        $this->assertFalse( expImageAliasLog::write( 'no-such-event' ) );
        $lines = expImageAliasLog::read( 10 );
        $this->assertCount( 2, $lines );
        $this->assertSame( array( 'placeholder', 'signature', 'large' ), array( $lines[0]['event'], $lines[0]['result'], $lines[0]['alias'] ), 'newest first' );
        $this->assertSame( '404', $lines[0]['fields']['status'] );
        $this->assertSame( array( 'generate', 12, 'var/site/storage/images/a b/1-1-eng-GB/a_small.jpg' ), array( $lines[1]['event'], $lines[1]['ms'], $lines[1]['path'] ) );
        $this->assertCount( 1, expImageAliasLog::read( 10, 'generate' ) );

        // A line as eZLog writes it, the request context a URL with "]"
        $entry = expImageAliasLog::parse( '[ Nov 13 2026 09:12:01 ][ site ][ https://www.example.org/content/image/a]b/x.jpg ] event=fail result=failed ms=2000 alias=small path=var/x' );
        $this->assertSame( array( 'site', 'fail', 'failed', 2000, 'var/x' ), array( $entry['siteaccess'], $entry['event'], $entry['result'], $entry['ms'], $entry['path'] ) );

        // A timeout (no conversion slot or lock within the wait) is deferred to a job, no failure: event defer
        $this->assertSame( array( 'generate', 'defer', 'fail', 'fail' ), array_map( array( 'expImageAliasLog', 'eventFor' ), array( 'ok', 'timeout', 'failed', '' ) ) );
        $this->assertTrue( expImageAliasLog::write( expImageAliasLog::eventFor( 'timeout' ), array( 'result' => 'timeout', 'ms' => 2000, 'alias' => 'small', 'path' => 'var/y' ) ) );
        $this->assertSame( array( 'defer', 'timeout' ), array( expImageAliasLog::read( 1 )[0]['event'], expImageAliasLog::read( 1 )[0]['result'] ) );
        $this->assertCount( 1, expImageAliasLog::read( 10, 'defer' ) );
        // both places that log making an alias or a variant go through eventFor(), none writes fail itself
        $server = file_get_contents( dirname( __DIR__, 5 ) . '/kernel/classes/imagealias/expimagealiasserver.php' );
        $this->assertSame( 2, substr_count( $server, 'expImageAliasLog::write( expImageAliasLog::eventFor(' ) );
        $this->assertStringNotContainsString( "'fail'", $server );
        // a line from before the defer event: an alias's timeout reads as defer (the line itself is not changed); a
        // staging fetch that timed out names its source and stays a failure of the source
        $old = expImageAliasLog::parse( '[ Nov 13 2026 09:12:01 ][ site ][ /x ] event=fail result=timeout ms=2000 alias=small path=var/x' );
        $this->assertSame( array( 'defer', 'fail' ), array( $old['event'], $old['fields']['event'] ) );
        $this->assertSame( 'fail', expImageAliasLog::parse( '[ Nov 13 2026 09:12:01 ][ site ][ /x ] event=fail result=timeout ms=5000 alias=- source=pro path=var/x' )['event'] );
        $this->assertNull( expImageAliasLog::parse( 'something else' ) );
        $this->assertStringNotContainsString( "\n", expImageAliasLog::format( 'serve', array( 'alias' => "a\nb", 'path' => "x\ny" ) ) );
    }

    /** IAS-07 */
    public function testTheModeSwitch()
    {
        $this->assertFalse( expImageAliasSettings::isOnDemand(), 'the command line converts at once' );
        expImageAliasSettings::setMode( 'on-demand' );
        $this->assertTrue( expImageAliasSettings::isOnDemand() );
        $inside = expImageAliasSettings::withRender( function () {
            return expImageAliasSettings::isOnDemand();
        } );
        $this->assertFalse( $inside, 'content/image makes the alias in render mode' );
        $this->assertTrue( expImageAliasSettings::isOnDemand(), 'and back after it' );
        expImageAliasSettings::setMode( 'render' );
        $this->assertFalse( expImageAliasSettings::isOnDemand() );
        expImageAliasSettings::setMode( 'nonsense' );
        $this->assertFalse( expImageAliasSettings::isOnDemand(), 'an unknown mode is no mode: the command line rule' );
        $this->assertContains( expImageAliasSettings::configuredMode(), array( 'on-demand', 'render' ) );
        $this->assertContains( expImageAliasSettings::urlForm(), array( 'token', 'ids-token', 'ids', 'node', 'storage' ) );
    }

    /** IAS-08 */
    public function testRequestPaths()
    {
        $this->assertSame( 'var/site/storage/images/a/1-1-eng-GB/a_small.jpg', expImageAliasServer::normalizeRequestPath( '/var/site/storage/images/a/1-1-eng-GB/a_small.jpg?x=1' ) );
        $this->assertSame( 'var/site/storage/images/a/1-1-eng-GB/a b_small.jpg', expImageAliasServer::normalizeRequestPath( '/index_image.php/var/site/storage/images/a/1-1-eng-GB/a%20b_small.jpg' ) );
    }

    /** IAS-09 */
    public function testAWebServerInTheCliSapiFollowsTheSettings()
    {
        $saved = array();
        foreach ( array( 'REQUEST_METHOD', 'HTTP_HOST' ) as $key )
            $saved[$key] = isset( $_SERVER[$key] ) ? $_SERVER[$key] : null;
        try
        {
            unset( $_SERVER['REQUEST_METHOD'], $_SERVER['HTTP_HOST'] );
            $this->assertFalse( expImageAliasSettings::isWebProcess(), 'a command line script' );
            $this->assertFalse( expImageAliasSettings::isOnDemand(), 'the command line converts at once' );

            // What a Velocity worker (PHP_SAPI cli) has while it answers a browser
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $_SERVER['HTTP_HOST'] = 'www.example.org';
            $this->assertTrue( expImageAliasSettings::isWebProcess() );
            $this->assertSame( expImageAliasSettings::configuredMode() === expImageAliasSettings::MODE_ON_DEMAND,
                               expImageAliasSettings::isOnDemand(), 'a request follows the settings, as under PHP-FPM' );
            $this->assertFalse( expImageAliasSettings::withRender( function () {
                return expImageAliasSettings::isOnDemand();
            } ), 'content/image still makes the alias in render mode' );
            expImageAliasSettings::setMode( 'render' );
            $this->assertFalse( expImageAliasSettings::isOnDemand(), 'the process mode wins' );
            expImageAliasSettings::setMode( null );
            $this->assertNull( expImageAliasThrottle::lowerPriority( 10 ), 'a web server worker is never reniced' );
        }
        finally
        {
            foreach ( $saved as $key => $value )
            {
                if ( $value === null )
                    unset( $_SERVER[$key] );
                else
                    $_SERVER[$key] = $value;
            }
        }
    }
}
