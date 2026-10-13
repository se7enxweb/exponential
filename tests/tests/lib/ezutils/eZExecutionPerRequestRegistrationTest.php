<?php
/**
 * eZExecution's handlers, clean exit flag and shutdown functions belong to one request.
 *
 * A persistent Exponential Velocity worker serves many requests in one PHP process. Between them it puts every
 * class's statics back to what they were after its warm-up, and it runs and forgets the shutdown functions a
 * request registered at the end of that request. eZExecution kept "the shutdown functions are registered" in a
 * static flag, which the warm-up had set, and appended to handler lists that the warm-up had filled: from a
 * worker's second request on no shutdown function was registered at all (no clean-up and no error page after a
 * fatal error, no eZSession::stop()), and every request ran the warm-up's handlers beside its own. These tests put
 * the statics into that restored state and play several requests through it.
 *
 *  PR-01 - In a new request the restored warm-up state is dropped: only this request's handlers, no clean exit,
 *          the shutdown functions registered again
 *  PR-02 - Within one request a handler added twice is kept once, and the shutdown functions are registered once
 *  PR-03 - A shutdown function runs only in the request that registered it (not the warm-up's in a worker's first)
 *  PR-04 - Requests in a row: one handler each, nothing accumulates
 *  PR-05 - The first call in a process (statics still declared) keeps what is there
 *  PR-06 - expAudit registers its handlers again for each request
 *
 * No database, no kernel bootstrap. Every static is put back afterwards; the shutdown functions the tests register
 * belong to requests that are over when PHPUnit ends, so they do nothing then.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezutils
 */

class eZExecutionPerRequestRegistrationTest extends PHPUnit\Framework\TestCase
{
    private static $names = array( 'requestKey', 'hasCleanExit', 'shutdownHandle', 'fatalErrorHandlers', 'cleanupHandlers' );
    private $saved = array();
    private $props = array();
    private $requestTime;
    private $hadRequestTime;
    private $exceptionHandler;

    protected function setUp(): void
    {
        foreach ( self::$names as $name )
        {
            $this->props[$name] = new ReflectionProperty( 'eZExecution', $name );
            if ( PHP_VERSION_ID < 80100 )
                $this->props[$name]->setAccessible( true );
            $this->saved[$name] = $this->props[$name]->getValue();
        }
        $this->hadRequestTime = array_key_exists( 'REQUEST_TIME_FLOAT', $_SERVER );
        $this->requestTime = $this->hadRequestTime ? $_SERVER['REQUEST_TIME_FLOAT'] : null;
        $this->exceptionHandler = set_exception_handler( null );
    }

    protected function tearDown(): void
    {
        foreach ( self::$names as $name )
            $this->props[$name]->setValue( null, $this->saved[$name] );
        if ( $this->hadRequestTime )
            $_SERVER['REQUEST_TIME_FLOAT'] = $this->requestTime;
        else
            unset( $_SERVER['REQUEST_TIME_FLOAT'] );
        set_exception_handler( $this->exceptionHandler );
    }

    /** The statics as a worker puts them back before each request: the warm-up's. */
    private function restoreWarmupState( array $fatal, array $cleanup )
    {
        $this->props['requestKey']->setValue( null, '1000.5' );
        $this->props['hasCleanExit']->setValue( null, true );
        $this->props['shutdownHandle']->setValue( null, true );
        $this->props['fatalErrorHandlers']->setValue( null, $fatal );
        $this->props['cleanupHandlers']->setValue( null, $cleanup );
    }

    private function startRequest( $time )
    {
        $_SERVER['REQUEST_TIME_FLOAT'] = $time;
    }

    /** PR-01 */
    public function testANewRequestStartsFromWhatAFreshProcessHas()
    {
        $warmFatal = static function () {};
        $warmCleanup = static function () {};
        $this->restoreWarmupState( array( $warmFatal ), array( $warmCleanup ) );
        $this->startRequest( 2000.25 );

        $this->assertFalse( eZExecution::isCleanExit(), 'a request starts unclean, as under PHP-FPM' );
        $this->assertFalse( $this->props['shutdownHandle']->getValue(), 'the shutdown functions are not registered yet' );

        $fatal = static function () {};
        $cleanup = static function () {};
        eZExecution::addFatalErrorHandler( $fatal );
        eZExecution::addCleanupHandler( $cleanup );

        $this->assertSame( array( $fatal ), eZExecution::fatalErrorHandlers() );
        $this->assertSame( array( $cleanup ), eZExecution::cleanupHandlers() );
        $this->assertTrue( $this->props['shutdownHandle']->getValue(), 'registered again for this request' );
        $this->assertSame( '2000.25', $this->props['requestKey']->getValue() );
        $this->assertSame( array( 'eZExecution', 'defaultExceptionHandler' ), eZExecution::currentExceptionHandler() );
    }

    /** PR-02 */
    public function testWithinOneRequestEverythingIsRegisteredOnce()
    {
        $this->restoreWarmupState( array(), array() );
        $this->startRequest( 2001.5 );
        $named = array( 'eZExecutionPerRequestRegistrationTest', 'noop' );
        eZExecution::addCleanupHandler( $named );
        eZExecution::addCleanupHandler( $named );
        eZExecution::addFatalErrorHandler( 'eZExecutionPerRequestRegistrationTestNoop' );
        eZExecution::addFatalErrorHandler( 'eZExecutionPerRequestRegistrationTestNoop' );
        $this->assertSame( array( $named ), eZExecution::cleanupHandlers() );
        $this->assertSame( array( 'eZExecutionPerRequestRegistrationTestNoop' ), eZExecution::fatalErrorHandlers() );

        // registered once: the second call finds the flag set and pushes no second exception handler
        eZExecution::registerShutdownHandler();
        restore_exception_handler();
        $this->assertNull( eZExecution::currentExceptionHandler(), 'one exception handler was installed, not two' );
    }

    /** PR-03 */
    public function testAShutdownFunctionRunsOnlyInItsOwnRequest()
    {
        $calls = 0;
        $count = function () use ( &$calls ) { $calls++; };
        $this->startRequest( 3000.75 );
        eZExecution::runForRequest( '1000.5', $count );
        $this->assertSame( 0, $calls, "the warm-up's shutdown function does nothing in a worker's request" );
        eZExecution::runForRequest( eZExecution::requestKey(), $count );
        $this->assertSame( 1, $calls );
        $this->assertSame( '3000.75', eZExecution::requestKey() );

        unset( $_SERVER['REQUEST_TIME_FLOAT'] );
        $this->assertSame( '', eZExecution::requestKey(), 'without REQUEST_TIME_FLOAT the process is one request' );
        eZExecution::runForRequest( '', $count );
        $this->assertSame( 2, $calls );
    }

    /** PR-04 */
    public function testHandlersDoNotAccumulateOverRequests()
    {
        $warm = static function () {};
        for ( $i = 1; $i <= 5; $i++ )
        {
            // what the worker does between requests: back to the warm-up's statics
            $this->restoreWarmupState( array( $warm ), array( $warm ) );
            $this->startRequest( 4000 + $i / 10 );
            eZExecution::setCleanExit( false );
            eZExecution::addFatalErrorHandler( static function () {} );
            eZExecution::addCleanupHandler( static function () {} );
            $this->assertCount( 1, eZExecution::fatalErrorHandlers(), "request $i" );
            $this->assertCount( 1, eZExecution::cleanupHandlers(), "request $i" );
            $this->assertNotContains( $warm, eZExecution::cleanupHandlers(), "request $i" );
            eZExecution::setCleanExit();
            $this->assertTrue( eZExecution::isCleanExit() );
            restore_exception_handler();
        }

        // and without the restore (a class loaded after the worker's snapshot keeps its statics): the same
        $this->props['requestKey']->setValue( null, '5000.1' );
        for ( $i = 2; $i <= 4; $i++ )
        {
            $this->startRequest( 5000 + $i / 10 );
            eZExecution::addFatalErrorHandler( static function () {} );
            $this->assertCount( 1, eZExecution::fatalErrorHandlers(), "request $i" );
            restore_exception_handler();
        }
    }

    /** PR-05 */
    public function testTheFirstCallOfAProcessKeepsWhatIsThere()
    {
        $handler = static function () {};
        $this->props['requestKey']->setValue( null, null );
        $this->props['cleanupHandlers']->setValue( null, array( $handler ) );
        $this->props['hasCleanExit']->setValue( null, true );
        $this->startRequest( 6000.5 );
        $this->assertSame( array( $handler ), eZExecution::cleanupHandlers() );
        $this->assertTrue( eZExecution::isCleanExit() );
        $this->assertSame( '6000.5', $this->props['requestKey']->getValue() );
    }

    /** PR-06 */
    public function testTheAuditRegistersItsHandlersForEachRequest()
    {
        if ( !class_exists( 'expAudit' ) )
            $this->markTestSkipped( 'expAudit is not available' );
        $flag = new ReflectionProperty( 'expAudit', 'handlersRegistered' );
        if ( PHP_VERSION_ID < 80100 )
            $flag->setAccessible( true );
        $before = $flag->getValue();
        try
        {
            $flag->setValue( null, true );
            expAudit::resetRequest();
            $this->assertFalse( $flag->getValue(), 'a new request registers the cleanup and fatal error handlers again' );
        }
        finally
        {
            $flag->setValue( null, $before );
        }
    }

    public static function noop()
    {
    }
}

function eZExecutionPerRequestRegistrationTestNoop()
{
}
