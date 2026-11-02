<?php
/**
 * The schedule of a target (expStaticSchedule): pure; times in UTC for the test.
 *
 *  SC-01 valid and invalid expressions, macros
 *  SC-02 matching: lists, ranges, steps, Sunday as 0 and 7, day of month or day of week when both are restricted
 *  SC-03 the last match and when a target is due (once per matching time, caught up within a day)
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expStaticScheduleTest extends PHPUnit\Framework\TestCase
{
    private $zone;

    protected function setUp(): void
    {
        $this->zone = date_default_timezone_get();
        date_default_timezone_set( 'UTC' );
    }

    protected function tearDown(): void
    {
        date_default_timezone_set( $this->zone );
    }

    /** SC-01 */
    public function testValid()
    {
        foreach ( array( '0 3 * * *', '*/15 * * * *', '0 0-6/2 1,15 * 1-5', '@daily', '@HOURLY', '30 4 * 12 7' ) as $ok )
            $this->assertTrue( expStaticSchedule::valid( $ok ), $ok );
        foreach ( array( '', '* * * *', '60 * * * *', '* 24 * * *', '* * 0 * *', '* * * 13 *', '* * * * 8', '5-1 * * * *', '*/0 * * * *', 'a * * * *', '@yearly' ) as $bad )
            $this->assertFalse( expStaticSchedule::valid( $bad ), $bad );
    }

    /** SC-02 */
    public function testMatches()
    {
        $sunday = gmmktime( 3, 0, 0, 11, 1, 2026 ); // Sunday 1 November 2026, 03:00
        $this->assertTrue( expStaticSchedule::matches( expStaticSchedule::parse( '0 3 * * *' ), $sunday ) );
        $this->assertFalse( expStaticSchedule::matches( expStaticSchedule::parse( '0 4 * * *' ), $sunday ) );
        $this->assertTrue( expStaticSchedule::matches( expStaticSchedule::parse( '*/15 * * * 7' ), $sunday ) );
        $this->assertTrue( expStaticSchedule::matches( expStaticSchedule::parse( '0 3 * * 0' ), $sunday ) );
        $this->assertFalse( expStaticSchedule::matches( expStaticSchedule::parse( '0 3 * * 1-5' ), $sunday ) );
        // both restricted: day 1 matches although Sunday is not Monday
        $this->assertTrue( expStaticSchedule::matches( expStaticSchedule::parse( '0 3 1 * 1' ), $sunday ) );
        $this->assertFalse( expStaticSchedule::matches( expStaticSchedule::parse( '0 3 2 * 1' ), $sunday ) );
        $this->assertTrue( expStaticSchedule::matches( expStaticSchedule::parse( '@monthly' ), gmmktime( 0, 0, 0, 11, 1, 2026 ) ) );
    }

    /** SC-03 */
    public function testDue()
    {
        $now = gmmktime( 3, 7, 30, 11, 1, 2026 );
        $this->assertSame( gmmktime( 3, 0, 0, 11, 1, 2026 ), expStaticSchedule::lastMatch( '0 3 * * *', $now ) );
        $this->assertSame( gmmktime( 3, 5, 0, 11, 1, 2026 ), expStaticSchedule::lastMatch( '*/5 * * * *', $now ) );
        $this->assertNull( expStaticSchedule::lastMatch( '0 3 1 * *', gmmktime( 2, 0, 0, 11, 3, 2026 ) ) );
        $this->assertNull( expStaticSchedule::lastMatch( 'bad', $now ) );
        $this->assertTrue( expStaticSchedule::due( '0 3 * * *', 0, $now ) );
        $this->assertTrue( expStaticSchedule::due( '0 3 * * *', gmmktime( 3, 0, 0, 10, 31, 2026 ), $now ) );
        $this->assertFalse( expStaticSchedule::due( '0 3 * * *', gmmktime( 3, 1, 0, 11, 1, 2026 ), $now ) );
    }
}
