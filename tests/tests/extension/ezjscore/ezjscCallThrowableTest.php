<?php
/**
 * ezjscore/call: a PHP Error in a server function answers a generic text (details to the log), an Exception keeps its
 * message, and an expservices answer keeps its { ok, error: { code, message } } envelope.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/ezjscore/ezjscCallThrowableTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/../expservices/core/expServicesCoreTestCase.php';
require_once __DIR__ . '/../../../../extension/ezjscore/classes/runnable/views/ezjscore/call.php';

class ezjscCallThrowableRouter extends ezjscServerRouter
{
    public $action;
    public function __construct( $action ) { $this->action = $action; }
    public function call( &$environmentArguments = array(), $isPackeStage = false ) { return call_user_func( $this->action ); }
}

class ezjscCallThrowableTest extends PHPUnit\Framework\TestCase
{
    private function answer( callable $action )
    {
        $out = multipleezjscServerCalls( array( new ezjscCallThrowableRouter( $action ) ), 'json' );
        $this->assertCount( 1, $out );
        $decoded = json_decode( $out[0], true );
        $this->assertIsArray( $decoded, 'the body is JSON: ' . $out[0] );
        return $decoded;
    }

    public function testErrorGivesGenericTextWithoutPaths()
    {
        $answer = $this->answer( function () { return strlen( array() ); } );
        $this->assertSame( 'Internal error in the server function', $answer['error_text'] );
        $this->assertStringNotContainsString( '.php', json_encode( $answer ) );
        $this->assertStringNotContainsString( '/var/www', json_encode( $answer ) );
    }

    public function testExceptionKeepsItsMessage()
    {
        $answer = $this->answer( function () { throw new RuntimeException( 'Visible message' ); } );
        $this->assertSame( 'Visible message', $answer['error_text'] );
    }

    public function testSuccessIsUnchanged()
    {
        $answer = $this->answer( function () { return 'fine'; } );
        $this->assertSame( '', $answer['error_text'] );
        $this->assertSame( 'fine', $answer['content'] );
    }

    public function testExpservicesEnvelopeSurvivesTheCall()
    {
        ezpLiveInstallation::requireOrSkip();
        $answer = $this->answer( function () { return expServiceBase::invoke( 'expServicesFixtureServices', 'typeError', array() ); } );
        $this->assertSame( '', $answer['error_text'] );
        $this->assertFalse( $answer['content']['ok'] );
        $this->assertSame( 500, $answer['content']['error']['code'] );
        $this->assertArrayHasKey( 'message', $answer['content']['error'] );

        $answer = $this->answer( function () { return expServiceBase::invoke( 'expServicesFixtureServices', 'fails', array() ); } );
        $this->assertSame( array( 'ok' => false, 'error' => array( 'code' => 422, 'message' => 'bad input' ) ), $answer['content'] );
    }
}
