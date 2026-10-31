<?php
/**
 * Publishing at a date and taking down at a date, the kernel's way (expContentSchedule): which date field of a class
 * the wait-until-date event and the hide cronjob read, and what is written into a draft.
 *
 * No database, no files.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/expContentScheduleTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class expContentScheduleTest extends PHPUnit\Framework\TestCase
{
    private static function attributes()
    {
        return array(
            array( 'id' => 10, 'identifier' => 'title', 'datatype' => 'ezstring' ),
            array( 'id' => 11, 'identifier' => 'publish_date', 'datatype' => 'ezdatetime' ),
            array( 'id' => 12, 'identifier' => 'hide_date', 'datatype' => 'ezdate' ),
        );
    }

    public function testPublishAttributeIsTheDateFieldAWaitEventLists()
    {
        $this->assertSame( array( 'identifier' => 'publish_date', 'datatype' => 'ezdatetime', 'precision' => 'datetime' ),
                           expContentSchedule::publishAttribute( self::attributes(), array( 99, '11' ) ) );
        $this->assertNull( expContentSchedule::publishAttribute( self::attributes(), array( 99 ) ), 'not listed' );
        $this->assertNull( expContentSchedule::publishAttribute( self::attributes(), array( 10 ) ), 'not a date' );
        $this->assertNull( expContentSchedule::publishAttribute( self::attributes(), array() ) );
    }

    public function testTakeDownAttributeNeedsTheHideSettingAndARoot()
    {
        $map = array( 'article' => 'hide_date' );
        $this->assertSame( array( 'identifier' => 'hide_date', 'datatype' => 'ezdate', 'precision' => 'date' ),
                           expContentSchedule::takeDownAttribute( self::attributes(), 'article', $map, array( 2 ), '/1/2/59/' ) );
        $this->assertNotNull( expContentSchedule::takeDownAttribute( self::attributes(), 'article', $map, array( 2 ), '/1/2/' ), 'directly below the root' );
        $this->assertNull( expContentSchedule::takeDownAttribute( self::attributes(), 'article', $map, array( 2 ), '/1/' ), 'the root itself or above' );
        $this->assertNull( expContentSchedule::takeDownAttribute( self::attributes(), 'article', $map, array( 5 ), '/1/2/59/' ), 'not below a root' );
        $this->assertNull( expContentSchedule::takeDownAttribute( self::attributes(), 'article', $map, array( 9 ), '/1/2/59/' ), '/59/ is not /9/' );
        $this->assertNull( expContentSchedule::takeDownAttribute( self::attributes(), 'folder', $map, array( 2 ), '/1/2/' ), 'class not in the map' );
        $this->assertNull( expContentSchedule::takeDownAttribute( self::attributes(), 'article', array( 'article' => 'title' ), array( 2 ), '/1/2/' ), 'not a date' );
    }

    public function testPlanWritesTheDatesAndTellsWhetherThePublishWaits()
    {
        $publish = array( 'identifier' => 'publish_date', 'datatype' => 'ezdatetime', 'precision' => 'datetime' );
        $hide = array( 'identifier' => 'hide_date', 'datatype' => 'ezdatetime', 'precision' => 'datetime' );
        $now = 1000000;

        $p = expContentSchedule::plan( $publish, $hide, $now + 600, $now + 3600, $now );
        $this->assertNull( $p['error'] );
        $this->assertTrue( $p['deferred'] );
        $this->assertSame( array( 'publish_date' => $now + 600, 'hide_date' => $now + 3600 ), $p['writes'] );

        $p = expContentSchedule::plan( $publish, $hide, null, null, $now, 0 );
        $this->assertSame( array(), $p['writes'], 'publish now: nothing written' );
        $this->assertFalse( $p['deferred'] );

        $p = expContentSchedule::plan( $publish, $hide, null, null, $now, $now + 99 );
        $this->assertSame( array( 'publish_date' => $now ), $p['writes'], 'publish now with a date in the future on the draft: now' );
        $this->assertFalse( $p['deferred'] );

        $p = expContentSchedule::plan( $publish, $hide, null, 0, $now, 0 );
        $this->assertSame( array( 'hide_date' => 0 ), $p['writes'], 'the take-down date cleared' );

        $this->assertSame( 'no_publish_date', expContentSchedule::plan( null, $hide, $now + 1, null, $now )['error'] );
        $this->assertNull( expContentSchedule::plan( null, $hide, $now - 1, null, $now )['error'], 'a date passed: published now, no field needed' );
        $this->assertSame( 'no_take_down_date', expContentSchedule::plan( $publish, null, null, $now + 9, $now )['error'] );
        $this->assertSame( 'take_down_before_publish', expContentSchedule::plan( $publish, $hide, $now + 100, $now + 50, $now )['error'] );
        $this->assertSame( 'take_down_before_publish', expContentSchedule::plan( $publish, $hide, null, $now - 5, $now )['error'] );
    }

    public function testADateFieldGetsTheStartOfTheDay()
    {
        $publish = array( 'identifier' => 'publish_date', 'datatype' => 'ezdate', 'precision' => 'date' );
        $at = mktime( 15, 30, 0, 11, 2, 2026 );
        $p = expContentSchedule::plan( $publish, null, $at, null, $at - 86400 * 3 );
        $this->assertSame( array( 'publish_date' => mktime( 0, 0, 0, 11, 2, 2026 ) ), $p['writes'] );
        $this->assertSame( mktime( 0, 0, 0, 11, 2, 2026 ), expContentSchedule::dayStart( $at ) );
    }
}
