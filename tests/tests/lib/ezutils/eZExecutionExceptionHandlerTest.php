<?php
/**
 * eZExecution::registerShutdownHandler() and the exception handler in a persistent worker.
 *
 * Exponential Velocity serves many requests in one PHP process and takes away the exception handler a request
 * installed once the request has ended, while eZExecution's statics, among them the flag that the handlers are
 * registered, are put back to what they were after the warm-up. The handler was installed only together with that
 * flag, so from a worker's second request on there was none: an uncaught exception or error got the server's own
 * error page and nothing in error.log (seen as an HTTP 500 of the rich text editor's upload with no trace).
 *
 *  EH-01 - With the flag set and no handler in place, registerShutdownHandler() installs defaultExceptionHandler
 *  EH-02 - A handler that other code installed is kept
 *  EH-03 - currentExceptionHandler() reads the handler without changing PHP's stack of handlers
 *
 * No database, no kernel bootstrap.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezutils
 */

class eZExecutionExceptionHandlerTest extends PHPUnit\Framework\TestCase
{
    private $flag;
    private $handlers = array();

    protected function setUp(): void
    {
        $this->flag = new ReflectionProperty( 'eZExecution', 'shutdownHandle' );
        if ( PHP_VERSION_ID < 80100 )
            $this->flag->setAccessible( true );
        $this->handlers = array( 'flag' => $this->flag->getValue() );
        // the test's own baseline: no handler, as in a worker after its first request
        $this->handlers['previous'] = set_exception_handler( null );
        // as after the warm-up: the shutdown functions are registered, the flag says so
        $this->flag->setValue( null, true );
    }

    protected function tearDown(): void
    {
        $this->flag->setValue( null, $this->handlers['flag'] );
        set_exception_handler( $this->handlers['previous'] );
    }

    /** EH-01 */
    public function testTheHandlerIsInstalledAgainWhenNoneIsInPlace()
    {
        $this->assertNull( eZExecution::currentExceptionHandler() );
        eZExecution::registerShutdownHandler();
        $this->assertSame( array( 'eZExecution', 'defaultExceptionHandler' ), eZExecution::currentExceptionHandler() );

        // a second call in the same request pushes nothing more
        eZExecution::registerShutdownHandler();
        restore_exception_handler();
        $this->assertNull( eZExecution::currentExceptionHandler(), 'one handler was installed, not two' );
    }

    /** EH-02 */
    public function testAHandlerOfOtherCodeIsKept()
    {
        $own = static function ( $e ) {};
        set_exception_handler( $own );
        eZExecution::registerShutdownHandler();
        $this->assertSame( $own, eZExecution::currentExceptionHandler() );
        restore_exception_handler();
    }

    /** EH-03 */
    public function testReadingTheHandlerChangesNothing()
    {
        $own = static function ( $e ) {};
        set_exception_handler( $own );
        $this->assertSame( $own, eZExecution::currentExceptionHandler() );
        $this->assertSame( $own, eZExecution::currentExceptionHandler() );
        restore_exception_handler();
        $this->assertNull( eZExecution::currentExceptionHandler() );
    }
}
