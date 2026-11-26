<?php
/**
 * eZExecution::isCleaningUp(): the request reached its clean-up, so a request that ends there without a PHP error
 * is not written as "Fatal error" by the kernel's fatal error handler (LiteSpeed ends some requests there once their
 * whole answer has been sent). No database.
 *
 *  EC-01  off for a new request, on once cleanup() started, off again for the next request (a persistent worker)
 *  EC-02  the kernel's fatal error handler writes a notice for it, and still the "Fatal error" line for a real one
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class eZExecutionCleaningUpTest extends PHPUnit\Framework\TestCase
{
    private $time;

    protected function setUp(): void
    {
        $this->time = isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? $_SERVER['REQUEST_TIME_FLOAT'] : null;
    }

    protected function tearDown(): void
    {
        if ( $this->time === null )
            unset( $_SERVER['REQUEST_TIME_FLOAT'] );
        else
            $_SERVER['REQUEST_TIME_FLOAT'] = $this->time;
    }

    /** EC-01 */
    public function testTheCleanUpIsNotedPerRequest()
    {
        $_SERVER['REQUEST_TIME_FLOAT'] = 1763100000.25;
        eZExecution::isCleanExit();   // notes this request
        $this->assertFalse( eZExecution::isCleaningUp() );
        eZExecution::cleanup();
        $this->assertTrue( eZExecution::isCleaningUp() );
        $_SERVER['REQUEST_TIME_FLOAT'] = 1763100001.5;   // the next request of the same process
        $this->assertFalse( eZExecution::isCleaningUp() );
    }

    /** EC-02 */
    public function testTheFatalHandlerWritesANoticeForAnEndInTheCleanUp()
    {
        $code = (string)file_get_contents( dirname( __DIR__, 4 ) . '/kernel/private/classes/ezpkernelweb.php' );
        $this->assertStringContainsString( "if ( eZExecution::isCleaningUp() && !( \$last && ( \$last['type'] & \$fatal ) ) )", $code );
        $this->assertStringContainsString( "'The request ended during its clean-up, after its answer was sent'", $code );
        $this->assertStringContainsString( "eZLog::write( 'Fatal error ' . \$reference", $code, 'a real fatal error is still written' );
    }
}
