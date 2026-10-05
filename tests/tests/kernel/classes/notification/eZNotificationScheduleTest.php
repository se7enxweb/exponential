<?php
/**
 * eZNotificationSchedule::setDateForItem() computes the next send date of a digest. A digest handler that needs only
 * the date calls it without an item; that used to end in "Call to a member function setAttribute() on null".
 *
 * The method needs no database: the item is a stand-in that records what it is given.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/notification/eZNotificationScheduleTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class eZNotificationScheduleTest extends PHPUnit\Framework\TestCase
{
    private $timezone;

    /**
     * setDateForItem() adds a fixed number of seconds and ignores a change between summer and winter time, so the
     * hour and weekday checks run in UTC.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->timezone = date_default_timezone_get();
        date_default_timezone_set( 'UTC' );
    }

    protected function tearDown(): void
    {
        date_default_timezone_set( $this->timezone );
        parent::tearDown();
    }

    public function testWithoutItemOnlyTheDateIsComputed()
    {
        $before = time();
        $sendDate = eZNotificationSchedule::setDateForItem( null, array( 'frequency' => 'day', 'hour' => 8 ) );

        $this->assertIsInt( $sendDate );
        $this->assertGreaterThan( $before, $sendDate );
        $this->assertLessThanOrEqual( $before + 24 * 3600, $sendDate );
        $this->assertSame( 8, (int)date( 'G', $sendDate ) );
    }

    public function testTheDateIsSetOnTheItem()
    {
        $item = new class
        {
            public $attributes = array();

            public function setAttribute( $name, $value )
            {
                $this->attributes[$name] = $value;
            }
        };

        $sendDate = eZNotificationSchedule::setDateForItem( $item, array( 'frequency' => 'week', 'day' => 1, 'hour' => 8 ) );

        $this->assertSame( array( 'send_date' => $sendDate ), $item->attributes );
        $this->assertSame( 1, (int)date( 'w', $sendDate ) );
    }

    public function testSettingsThatAreNotAnArrayGiveFalse()
    {
        $this->assertFalse( eZNotificationSchedule::setDateForItem( null, false ) );
    }

    /**
     * @return array settings, "now" in Europe/Berlin, expected send date in Europe/Berlin
     */
    public static function berlinDatesProvider()
    {
        return array(
            // the hour of the clock, not a fixed number of seconds: across the end of summer time (25 October
            // 2026, 03:00 -> 02:00) the digest still goes out at 08:00
            'weekly across the end of summer time' =>
                array( array( 'frequency' => 'week', 'day' => 1, 'hour' => 8 ), '2026-10-24 12:00', '2026-10-26 08:00' ),
            'daily across the end of summer time' =>
                array( array( 'frequency' => 'day', 'hour' => 8 ), '2026-10-24 12:00', '2026-10-25 08:00' ),
            'daily across the start of summer time' =>
                array( array( 'frequency' => 'day', 'hour' => 8 ), '2026-03-28 12:00', '2026-03-29 08:00' ),
            'daily, the hour still ahead today' =>
                array( array( 'frequency' => 'day', 'hour' => 18 ), '2026-10-05 12:00', '2026-10-05 18:00' ),
            'daily, the hour is now: tomorrow' =>
                array( array( 'frequency' => 'day', 'hour' => 12 ), '2026-10-05 12:30', '2026-10-06 12:00' ),
            'weekly, today but the hour passed: next week' =>
                array( array( 'frequency' => 'week', 'day' => 1, 'hour' => 8 ), '2026-10-05 09:00', '2026-10-12 08:00' ),
            // a monthly day the next month does not have falls on its last day, not into the month after
            'monthly day 31 from January to February' =>
                array( array( 'frequency' => 'month', 'day' => 31, 'hour' => 8 ), '2026-01-31 12:00', '2026-02-28 08:00' ),
            'monthly day 31 in a month with 30 days' =>
                array( array( 'frequency' => 'month', 'day' => 31, 'hour' => 8 ), '2026-04-10 12:00', '2026-04-30 08:00' ),
            'monthly from December to January' =>
                array( array( 'frequency' => 'month', 'day' => 15, 'hour' => 8 ), '2026-12-20 12:00', '2027-01-15 08:00' ),
            'monthly, later this month' =>
                array( array( 'frequency' => 'month', 'day' => 20, 'hour' => 8 ), '2026-10-05 12:00', '2026-10-20 08:00' ),
        );
    }

    /**
     * @dataProvider berlinDatesProvider
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'berlinDatesProvider' )]
    public function testSendDateIsTheChosenHourOfTheLocalClock( $settings, $now, $expected )
    {
        date_default_timezone_set( 'Europe/Berlin' );
        $sendDate = eZNotificationSchedule::setDateForItem( null, $settings, strtotime( $now ) );
        $this->assertSame( $expected, date( 'Y-m-d H:i', $sendDate ) );
    }

    public function testAnUnknownFrequencyGivesTheGivenTime()
    {
        $this->assertSame( 1790000000, eZNotificationSchedule::nextSendDate( array( 'frequency' => 'year', 'hour' => 8 ), 1790000000 ) );
    }
}
