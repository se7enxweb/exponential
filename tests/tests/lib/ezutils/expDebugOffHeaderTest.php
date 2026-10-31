<?php
/**
 * X-Exp-Debug: off switches the debug report off for one request, even from an address on DebugIPList, so
 * exp:benchmark measures the page a visitor gets (cacheable). The header can never switch the report on.
 * DB-less.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expDebugOffHeaderTest extends PHPUnit\Framework\TestCase
{
    private $saved = array();

    protected function setUp(): void
    {
        foreach ( array( 'REMOTE_ADDR', 'HTTP_X_EXP_DEBUG' ) as $key )
            $this->saved[$key] = isset( $_SERVER[$key] ) ? $_SERVER[$key] : null;
        $_SERVER['REMOTE_ADDR'] = '203.0.113.9';
    }

    protected function tearDown(): void
    {
        foreach ( $this->saved as $key => $value )
        {
            if ( $value === null )
                unset( $_SERVER[$key] );
            else
                $_SERVER[$key] = $value;
        }
    }

    private function allowed( array $list )
    {
        $check = new ReflectionMethod( 'eZDebug', 'isAllowedByCurrentIP' );
        $check->setAccessible( true );
        return $check->invoke( null, $list );
    }

    public function testListedAddressGetsTheReport()
    {
        unset( $_SERVER['HTTP_X_EXP_DEBUG'] );
        $this->assertTrue( $this->allowed( array( '203.0.113.9' ) ) );
    }

    public function testHeaderOffSwitchesTheReportOff()
    {
        $_SERVER['HTTP_X_EXP_DEBUG'] = ' OFF ';
        $this->assertFalse( $this->allowed( array( '203.0.113.9' ) ) );
    }

    public function testOtherValuesChangeNothing()
    {
        $_SERVER['HTTP_X_EXP_DEBUG'] = 'on';
        $this->assertTrue( $this->allowed( array( '203.0.113.9' ) ) );
        $this->assertFalse( $this->allowed( array( '198.51.100.1' ) ), 'never switches the report on' );
    }

    public function testBenchmarkSendsTheHeader()
    {
        $lines = new ReflectionMethod( 'expBenchmarkHttp', 'headerLines' );
        $lines->setAccessible( true );
        $this->assertContains( 'X-Exp-Debug: off', $lines->invoke( null, array( 'headers' => array(), 'encoding' => '' ) ) );
    }
}
