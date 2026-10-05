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
}
