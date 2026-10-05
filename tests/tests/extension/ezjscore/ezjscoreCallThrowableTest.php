<?php
/**
 * ezjscore/call answers a failing server function with an error text inside the response.
 *
 * multipleezjscServerCalls() caught only Exception, so a PHP Error (a TypeError, a call to an
 * undefined method, ...) escaped and turned the JSON or XML body into an HTML fatal page, which
 * could show server file paths. An Exception still passes its message to the client; any other
 * Throwable is logged and answered with a generic text.
 *
 * The server functions are stand-ins (ezjscoreCallThrowableTestRouter), so no kernel, database
 * or siteaccess is needed.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/ezjscore/ezjscoreCallThrowableTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

$ezjscoreRoot = dirname( __DIR__, 4 ) . '/extension/ezjscore';
require_once $ezjscoreRoot . '/classes/ezjscserverrouter.php';
require_once $ezjscoreRoot . '/classes/ezjscajaxcontent.php';
require_once $ezjscoreRoot . '/classes/runnable/views/ezjscore/call.php';
unset( $ezjscoreRoot );

/**
 * A server function router that returns a fixed value or throws a fixed Throwable.
 */
class ezjscoreCallThrowableTestRouter extends ezjscServerRouter
{
    /** @var Throwable|null thrown by call() when set */
    public $throwable = null;

    /** @var mixed returned by call() when nothing is thrown */
    public $result = null;

    public function __construct( $throwable = null, $result = null )
    {
        $this->throwable = $throwable;
        $this->result = $result;
    }

    public function call( &$environmentArguments = array(), $isPackeStage = false )
    {
        if ( $this->throwable !== null )
        {
            throw $this->throwable;
        }
        return $this->result;
    }
}

class ezjscoreCallThrowableTest extends PHPUnit\Framework\TestCase
{
    /**
     * Runs one call through multipleezjscServerCalls() and returns the decoded response.
     *
     * @param ezjscServerRouter|string $call
     * @return array
     */
    protected static function callResponse( $call )
    {
        $responses = self::runCalls( array( $call ) );
        return json_decode( $responses[0], true );
    }

    /**
     * Runs the calls through multipleezjscServerCalls() and returns the encoded responses.
     *
     * Logging an error the first time sets eZDebug up, which resets the PHP error handler;
     * the handler found before is put back, so a test leaves the handler as it found it.
     *
     * @param array $calls
     * @return array
     */
    protected static function runCalls( $calls )
    {
        $probe = function() { return false; };
        $handler = set_error_handler( $probe );
        restore_error_handler();

        $responses = multipleezjscServerCalls( $calls, 'json' );

        $current = set_error_handler( $probe );
        restore_error_handler();
        if ( $current !== $handler && $handler !== null )
        {
            set_error_handler( $handler );
        }
        return $responses;
    }

    public function testResultIsReturnedAsContent()
    {
        $response = self::callResponse( new ezjscoreCallThrowableTestRouter( null, array( 'answer' => 42 ) ) );
        $this->assertSame( '', $response['error_text'] );
        $this->assertSame( array( 'answer' => 42 ), $response['content'] );
    }

    public function testExceptionMessageReachesTheClient()
    {
        $response = self::callResponse( new ezjscoreCallThrowableTestRouter( new RuntimeException( 'Node 42 not found' ) ) );
        $this->assertSame( 'Node 42 not found', $response['error_text'] );
        $this->assertSame( '', $response['content'] );
    }

    public function testErrorGivesAGenericText()
    {
        $error = new Error( 'Call to undefined method in /var/www/site/extension/mine/classes/server.php' );
        $response = self::callResponse( new ezjscoreCallThrowableTestRouter( $error ) );
        $this->assertSame( 'Internal error in the server function', $response['error_text'] );
        $this->assertSame( '', $response['content'] );
    }

    public function testTypeErrorGivesAGenericText()
    {
        $response = self::callResponse( new ezjscoreCallThrowableTestRouter( new TypeError( 'Argument #1 must be of type array, null given' ) ) );
        $this->assertSame( 'Internal error in the server function', $response['error_text'] );
    }

    /**
     * The generic text carries no part of the Error message, so no server path reaches the client.
     */
    public function testErrorMessageDoesNotLeak()
    {
        $error = new Error( 'Failed in /var/www/site/kernel/secret.php' );
        $responses = self::runCalls( array( new ezjscoreCallThrowableTestRouter( $error ) ) );
        $this->assertStringNotContainsString( '/var/www/site', $responses[0] );
    }

    /**
     * One failing call does not break the others of the same request.
     */
    public function testFailingCallDoesNotStopTheOthers()
    {
        $responses = self::runCalls( array(
            new ezjscoreCallThrowableTestRouter( new Error( 'broken' ) ),
            new ezjscoreCallThrowableTestRouter( null, 'ok' ),
        ) );
        $this->assertCount( 2, $responses );
        $second = json_decode( $responses[1], true );
        $this->assertSame( 'ok', $second['content'] );
        $this->assertSame( '', $second['error_text'] );
    }

    public function testNotARouterIsReportedEscaped()
    {
        $response = self::callResponse( 'nofunction<script>' );
        $this->assertStringContainsString( 'Not a valid ezjscServerRouter argument', $response['error_text'] );
        $this->assertStringNotContainsString( '<script>', $response['error_text'] );
    }
}
