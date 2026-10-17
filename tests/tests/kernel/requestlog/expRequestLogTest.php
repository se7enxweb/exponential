<?php
/**
 * The request log (expRequestLog, site.ini [RequestLogSettings]): one line per request for a page that does not
 * exist, in 404.log, or the classifier's line in security.log, never both, and never the old error line beside it.
 *
 *  RL-1  the 404 line and its eZLog prefix
 *  RL-2  addresses: query values as ***, secrets and e-mail addresses in the path masked, control characters
 *        removed, spaces encoded, cut at MaxURILength
 *  RL-3  client addresses masked (IPv4 a.b.x.x, IPv6 prefix x:x), unmasked with MaskIP=disabled
 *  RL-4  the user agent: 8 hex of its sha1 and the agent cut to 60 characters, without quotes
 *  RL-5  one line per request: the first reason wins; handleError() then finish() writes nothing more
 *  RL-6  NotFoundLog=disabled: reason() and handleError() return false, the caller writes its old line, no 404.log
 *  RL-7  401 and 403 return false (the caller writes its notice), and so do 301 and a fault
 *  RL-8  a classifier verdict: the security path for any status, no 404 line, no old line
 *  RL-9  the node or object of the view (NodeID, ObjectID) and the fallback kernel:<type>/<number>
 *  RL-10 finish(): a reason the error module never saw ends as a 404 line, or as its old error line otherwise
 *  RL-11 the call sites are guarded: ezpkernelweb.php (reason, noteView, begin in run, finish in shutdown), ezmodule.php
 *        (Undefined view of the requested module, noteView in handleError), error/view.php (handleError)
 *
 * The log is written below var/tmp of the installation, never var/log. No database.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/requestlog/expRequestLogTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expRequestLogTest extends PHPUnit\Framework\TestCase
{
    private $dir;

    /** @var array the statuses the stub classifier was asked with */
    private $classified = array();

    /** @var string[] the old error lines finish() wrote */
    private $legacy = array();

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->dir = 'var/tmp/requestlog_test_' . uniqid( '', true );
        mkdir( $this->dir, 0700, true );
        expRequestLog::reset();
        expRequestLog::setLogDirectory( $this->dir );
        expRequestLog::setSettings( array( 'NotFoundLog' => 'enabled', 'NotFoundLogFile' => '404.log',
                                           'MaskIP' => 'enabled', 'MaxURILength' => '300' ) );
        expRequestLog::setRequest( array(
            'method' => 'GET',
            'uri' => '/site/no-such-page?q=secret+search',
            'siteaccess' => 'site',
            'referrer' => '',
            'ua' => 'Mozilla/5.0 (X11; Linux x86_64)',
            'ip' => '203.0.113.7',
        ) );
        $this->classified = array();
        $this->stubClassifier( false );
        $this->legacy = array();
        $test = $this;
        expRequestLog::setLegacyWriter( function ( $message ) use ( $test ) {
            $test->noteLegacy( $message );
        } );
    }

    public function noteLegacy( $message )
    {
        $this->legacy[] = $message;
    }

    protected function tearDown(): void
    {
        expRequestLog::reset();
        expRequestLog::setClassifier( null );
        expRequestLog::setLogDirectory( null );
        expRequestLog::setRequest( null );
        expRequestLog::setSettings( null );
        expRequestLog::setLegacyWriter( null );
        foreach ( glob( $this->dir . '/*' ) ?: array() as $file )
            unlink( $file );
        if ( is_dir( $this->dir ) )
            rmdir( $this->dir );
    }

    /**
     * A classifier in place of expRequestClassifier: $verdict says whether it found one.
     *
     * @param bool $verdict
     */
    private function stubClassifier( $verdict )
    {
        $test = $this;
        expRequestLog::setClassifier( function ( $status ) use ( $test, $verdict ) {
            $test->noteClassified( $status );
            return $verdict;
        } );
    }

    public function noteClassified( $status )
    {
        $this->classified[] = $status;
    }

    /** @return string[] the lines of the test's 404.log */
    private function lines()
    {
        $file = $this->dir . '/404.log';
        if ( !is_file( $file ) )
            return array();
        return array_values( array_filter( explode( "\n", file_get_contents( $file ) ), 'strlen' ) );
    }

    /** RL-1 */
    public function testNotFoundLineFormat()
    {
        $line = expRequestLog::notFoundLine( array(
            'status' => 404, 'method' => 'GET', 'uri' => '/site/no-such-page?q=foo', 'siteaccess' => 'site',
            'reason' => 'module:no-such-page', 'node' => null, 'referrer' => '',
            'ua' => 'Mozilla/5.0 (X11; Linux x86_64)' ) );
        $hash = substr( sha1( 'Mozilla/5.0 (X11; Linux x86_64)' ), 0, 8 );
        $this->assertSame( '404 GET /site/no-such-page?q=*** siteaccess=site reason=module:no-such-page node=- referrer=- ua='
                           . $hash . ' "Mozilla/5.0 (X11; Linux x86_64)"', $line );

        $line = expRequestLog::notFoundLine( array(
            'status' => 410, 'method' => 'get', 'uri' => '/content/view/full/99', 'siteaccess' => '',
            'reason' => 'node:99', 'node' => 99, 'referrer' => 'https://example.com/a?token=abc', 'ua' => '' ) );
        $this->assertSame( '410 - /content/view/full/99 siteaccess=- reason=node:99 node=99 referrer=https://example.com/a?token=*** ua=- ""', $line );

        $time = mktime( 9, 0, 0, 10, 17, 2026 );
        $this->assertSame( '[ Oct 17 2026 09:00:00 ] [203.0.x.x] 404 GET /', expRequestLog::formatLine( '404 GET /', '203.0.x.x', $time ) );
        $this->assertSame( '[ Oct 17 2026 09:00:00 ] [-] x', expRequestLog::formatLine( 'x', '', $time ) );
    }

    /** RL-2 */
    public function testUriRedaction()
    {
        $this->assertSame( '/a/b?x=***&y=&flag&z=***', expRequestLog::formatUri( '/a/b?x=1&y=&flag&z=jane@example.com' ) );
        $this->assertSame( '/user/j***@***.com', expRequestLog::formatUri( '/user/jane.doe@example.com' ) );
        $this->assertSame( '/a%20b/c%22d', expRequestLog::formatUri( "/a b/c\"d\x00\x1f\x7f" ) );
        $this->assertSame( '/page', expRequestLog::formatUri( '/page#frag' ) );
        $this->assertSame( '/', expRequestLog::formatUri( '' ) );
        $long = '/' . str_repeat( 'a', 400 );
        $this->assertSame( substr( $long, 0, 300 ) . '...', expRequestLog::formatUri( $long ) );
        $this->assertSame( '/abcd...', expRequestLog::formatUri( '/abcdefgh', 5 ) );
        // the value of a query is never written, also not a secret in its name
        $this->assertStringNotContainsString( 'hunter2', expRequestLog::formatUri( '/login?password=hunter2' ) );
    }

    /** RL-3 */
    public function testIpMasking()
    {
        $this->assertSame( '203.0.x.x', expRequestLog::formatIp( '203.0.113.7' ) );
        $this->assertSame( '2001:db8:x:x', expRequestLog::formatIp( '2001:db8:85a3::8a2e:370:7334' ) );
        $this->assertSame( '::x', expRequestLog::formatIp( '::1' ) );
        $this->assertSame( '-', expRequestLog::formatIp( '' ) );
        $this->assertSame( 'x', expRequestLog::formatIp( '300.1.1.1' ), 'an address neither pattern knows is never written in full' );
        $this->assertSame( '203.0.113.7', expRequestLog::formatIp( '203.0.113.7', false ) );

        expRequestLog::reason( 'module', 'no-such-page' );
        expRequestLog::handleError( 404, 'kernel', 20 );
        $lines = $this->lines();
        $this->assertCount( 1, $lines );
        $this->assertStringContainsString( '] [203.0.x.x] 404 GET', $lines[0] );
        $this->assertStringNotContainsString( '203.0.113.7', $lines[0] );

        expRequestLog::reset();
        expRequestLog::setSettings( array( 'MaskIP' => 'disabled' ) );
        expRequestLog::reason( 'module', 'x' );
        expRequestLog::handleError( 404, 'kernel', 20 );
        $lines = $this->lines();
        $this->assertStringContainsString( '] [203.0.113.7] 404 GET', $lines[1] );
    }

    /** RL-4 */
    public function testUserAgent()
    {
        $ua = 'Mozilla/5.0 "quoted" ' . "\x01" . str_repeat( 'x', 100 );
        $out = expRequestLog::formatUserAgent( $ua );
        $this->assertMatchesRegularExpression( '/^ua=[0-9a-f]{8} "[^"]{60}"$/', $out );
        $this->assertSame( 'ua=' . substr( sha1( $ua ), 0, 8 ) . ' "Mozilla/5.0 quoted ' . str_repeat( 'x', 41 ) . '"', $out );
        $this->assertSame( 'ua=- ""', expRequestLog::formatUserAgent( '' ) );
        $this->assertSame( 'ua=' . substr( sha1( 'curl/8.5.0' ), 0, 8 ) . ' "curl/8.5.0"', expRequestLog::formatUserAgent( 'curl/8.5.0' ) );
    }

    /** RL-5 */
    public function testOneLinePerRequest()
    {
        $this->assertTrue( expRequestLog::reason( 'module', 'no-such-page', null, 'Undefined module: no-such-page' ) );
        $this->assertTrue( expRequestLog::reason( 'view', 'other/view' ) );
        $this->assertTrue( expRequestLog::handleError( 404, 'kernel', 20 ) );
        // a second error page in the same request (an embedded one) writes nothing more
        $this->assertTrue( expRequestLog::handleError( 404, 'kernel', 2 ) );
        expRequestLog::finish( 404 );
        $lines = $this->lines();
        $this->assertCount( 1, $lines );
        $this->assertStringContainsString( ' 404 GET /site/no-such-page?q=*** siteaccess=site reason=module:no-such-page node=- referrer=- ua=', $lines[0] );
        $this->assertSame( array( 404 ), $this->classified, 'classified once per request' );

        // the next request starts afresh
        expRequestLog::reset();
        expRequestLog::reason( 'disabled', 'setup/rad' );
        expRequestLog::handleError( 404, 'kernel', 20 );
        $lines = $this->lines();
        $this->assertCount( 2, $lines );
        $this->assertStringContainsString( 'reason=disabled:setup/rad', $lines[1] );
    }

    /** RL-6 */
    public function testNotFoundLogDisabled()
    {
        expRequestLog::setSettings( array( 'NotFoundLog' => 'disabled' ) );
        $this->assertFalse( expRequestLog::enabled() );
        $this->assertFalse( expRequestLog::reason( 'module', 'no-such-page' ) );
        $this->assertFalse( expRequestLog::handleError( 404, 'kernel', 20 ) );
        expRequestLog::finish( 404 );
        $this->assertSame( array(), $this->lines() );
        $this->assertFileDoesNotExist( $this->dir . '/404.log' );
        // a missing group or variable is enabled (the defaults)
        expRequestLog::setSettings( array() );
        $this->assertTrue( expRequestLog::enabled() );
        $this->assertSame( $this->dir . '/404.log', expRequestLog::logFile() );
        expRequestLog::setSettings( array( 'NotFoundLogFile' => '../../etc/passwd' ) );
        $this->assertSame( $this->dir . '/passwd', expRequestLog::logFile(), 'the file stays in the log directory' );
    }

    /** RL-7 */
    public function testAccessAnswersAndFaultsStayWithTheCaller()
    {
        $this->assertFalse( expRequestLog::handleError( 403, 'kernel', 1 ) );
        expRequestLog::reset();
        $this->assertFalse( expRequestLog::handleError( 401, 'kernel', 1 ) );
        expRequestLog::reset();
        $this->assertFalse( expRequestLog::handleError( 301, 'kernel', 4 ) );
        expRequestLog::reset();
        $this->assertFalse( expRequestLog::handleError( null, 'kernel', 50 ) );
        expRequestLog::reset();
        $this->assertFalse( expRequestLog::handleError( 500, 'kernel', 50 ) );
        expRequestLog::finish();
        $this->assertSame( array(), $this->lines() );
    }

    /** RL-8 */
    public function testVerdictGoesToTheSecurityPathOnly()
    {
        $this->stubClassifier( true );
        foreach ( array( 404, 403, 401, 200, 500 ) as $status )
        {
            expRequestLog::reset();
            expRequestLog::reason( 'module', 'wp-login.php' );
            $this->assertTrue( expRequestLog::handleError( $status, 'kernel', 20 ), "status $status" );
            expRequestLog::finish( $status );
        }
        $this->assertSame( array(), $this->lines(), 'no 404 line for a probe' );
        $this->assertSame( array( 404, 403, 401, 200, 500 ), $this->classified );

        // a 200 classified at the end of the request (finish), once
        $this->classified = array();
        expRequestLog::reset();
        expRequestLog::finish( 200 );
        expRequestLog::finish( 200 );
        $this->assertSame( array( 200 ), $this->classified );

        // no verdict: a 200 writes nothing
        $this->stubClassifier( false );
        expRequestLog::reset();
        expRequestLog::finish( 200 );
        $this->assertSame( array(), $this->lines() );
    }

    /** RL-8: a classifier that fails is a request without verdict, never a broken page */
    public function testFailingClassifier()
    {
        expRequestLog::setClassifier( function () { throw new RuntimeException( 'broken rule set' ); } );
        expRequestLog::reason( 'module', 'x' );
        $this->assertTrue( expRequestLog::handleError( 404, 'kernel', 20 ) );
        $this->assertCount( 1, $this->lines() );
    }

    /** RL-9 */
    public function testNodeObjectAndFallbackReasons()
    {
        expRequestLog::noteView( 'content', 'view', array( 'ViewMode' => 'full', 'NodeID' => '99999999' ) );
        // the error module's own parameters never replace the view's
        expRequestLog::noteView( 'error', 'view', array( 'Type' => 'kernel' ) );
        // eZModule::handleError() notes only when the kernel noted nothing
        expRequestLog::noteView( 'content', 'view', array( 'NodeID' => '5' ), false );
        expRequestLog::handleError( 404, 'kernel', 2 );
        $this->assertStringContainsString( 'reason=node:99999999 node=99999999 ', expRequestLog::lastLine() );

        expRequestLog::reset();
        expRequestLog::noteView( 'content', 'edit', array( 'ObjectID' => '123', 'EditVersion' => '2' ) );
        expRequestLog::handleError( 404, 'kernel', 3 );
        $this->assertStringContainsString( 'reason=object:123 node=- ', expRequestLog::lastLine() );

        expRequestLog::reset();
        expRequestLog::handleError( 404, 'kernel', 21 );
        $this->assertStringContainsString( 'reason=kernel:kernel/21 node=- ', expRequestLog::lastLine() );

        // a recorded reason wins over the node, the node is still named
        expRequestLog::reset();
        expRequestLog::noteView( 'content', 'view', array( 'NodeID' => '7' ) );
        expRequestLog::reason( 'urlalias', '/old/path' );
        expRequestLog::handleError( 404, 'kernel', 2 );
        $this->assertStringContainsString( 'reason=urlalias:/old/path node=7 ', expRequestLog::lastLine() );
        $this->assertCount( 4, $this->lines() );
    }

    /** RL-10 */
    public function testFinishWritesWhatIsPending()
    {
        // a not-found the error module did not answer (the status came another way)
        expRequestLog::reason( 'module', 'nosuchmodule', null, 'Undefined module: nosuchmodule' );
        expRequestLog::finish( 404 );
        $this->assertCount( 1, $this->lines() );
        $this->assertStringContainsString( 'reason=module:nosuchmodule', expRequestLog::lastLine() );

        // error.ini sends the visitor elsewhere (a redirect): no 404 line; the old error line goes to error.log
        expRequestLog::reset();
        expRequestLog::reason( 'module', 'other', null, 'Undefined module: other' );
        $this->assertFalse( expRequestLog::handleError( null, 'kernel', 20 ) );
        expRequestLog::finish();
        $this->assertCount( 1, $this->lines() );
        $this->assertSame( array( 'Undefined module: other' ), $this->legacy );

        // a probe never gets its old line either
        $this->stubClassifier( true );
        expRequestLog::reset();
        expRequestLog::reason( 'module', 'wp-admin', null, 'Undefined module: wp-admin' );
        expRequestLog::finish( 302 );
        $this->assertSame( array( 'Undefined module: other' ), $this->legacy );
        $this->assertCount( 1, $this->lines() );
    }

    /** RL-11 */
    public function testCallSitesAreGuarded()
    {
        $web = file_get_contents( 'kernel/private/classes/ezpkernelweb.php' );
        $this->assertStringContainsString( "if ( !class_exists( 'expRequestLog' ) || !expRequestLog::reason( 'module', \$moduleName, null, \"Undefined module: \$moduleName\" ) )", $web );
        $this->assertStringContainsString( "!expRequestLog::reason( 'disabled', \$disabledDetail, null, \$disabledLine )", $web );
        $this->assertSame( 2, substr_count( $web, "expRequestLog::reason( 'view', \$moduleName . '/' . \$functionName );" ) );
        $this->assertStringContainsString( 'expRequestLog::finish();', $web );
        $this->assertStringContainsString( 'expRequestLog::begin();', $web );
        $this->assertStringContainsString( 'expRequestLog::noteView(', $web );
        // every call (not a comment) has its class_exists() guard on its own line or the line before (a Velocity
        // worker keeps the autoload map of its warm-up, which may not know the class); the try block of finish() is
        // two lines below its guard
        $lines = explode( "\n", $web );
        foreach ( $lines as $i => $line )
        {
            if ( strpos( $line, 'expRequestLog::' ) === false || preg_match( '/^\s*(\/\/|\*)/', $line ) )
                continue;
            $context = implode( "\n", array_slice( $lines, max( 0, $i - 4 ), 5 ) );
            $this->assertStringContainsString( "class_exists( 'expRequestLog' )", $context, 'line ' . ( $i + 1 ) . ': ' . trim( $line ) );
        }

        $module = file_get_contents( 'lib/ezutils/classes/ezmodule.php' );
        $this->assertStringContainsString( "!class_exists( 'expRequestLog' )", $module );
        $this->assertStringContainsString( "expRequestLog::reason( 'view'", $module );
        $this->assertStringContainsString( "class_exists( 'expRequestLog' ) && isset( \$GLOBALS['eZRequestedModuleParams']['module_name'] )", $module );

        $error = file_get_contents( 'kernel/private/classes/views/error/view.php' );
        $this->assertStringContainsString( "class_exists( 'expRequestLog' )", $error );
        $this->assertStringContainsString( '\expRequestLog::handleError( $httpErrorCode, $errorType, $errorNumber )', $error );
        $this->assertStringContainsString( 'Error ocurred using URI: ', $error, 'the old line stays for everything else' );
    }

    /** The log file is created writable for the other user (Velocity runs as root, PHP-FPM as the site user) */
    public function testNewFileIsGroupWritable()
    {
        expRequestLog::reason( 'module', 'x' );
        expRequestLog::handleError( 404, 'kernel', 20 );
        clearstatcache();
        $expected = eZFile::fileMode( octdec( eZINI::instance()->variable( 'FileSettings', 'LogFilePermissions' ) ) ) & 0777;
        $this->assertSame( decoct( $expected ), decoct( fileperms( $this->dir . '/404.log' ) & 0777 ) );
    }
}
