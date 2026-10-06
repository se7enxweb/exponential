<?php
/**
 * ezpRestDebugPHPFormatter, the debug output of the REST interface: log entries keyed by verbosity, source and
 * category, the stack trace of the last entry, and the timing accumulator (per group: elements with elapsed time,
 * share of the group, count, average and switches; totals for groups with more than one timer), including timers
 * that took no measurable time.
 *
 * No database. Timers and log entries are plain objects with the properties the formatter reads.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group rest
 */

class ezpRestDebugPHPFormatterTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        if ( !interface_exists( 'ezcDebugOutputFormatter' ) )
            $this->markTestSkipped( 'the Debug component of the Zeta Components is not installed' );
    }

    private static function timer( $group, $name, $start, $stop, array $switches = array() )
    {
        $timer = new stdClass();
        $timer->group = $group;
        $timer->name = $name;
        $timer->startTime = $start;
        $timer->stopTime = $stop;
        $timer->elapsedTime = $stop - $start;
        $timer->switchTime = array();
        foreach ( $switches as $switchName => $time )
        {
            $switch = new stdClass();
            $switch->name = $switchName;
            $switch->time = $time;
            $timer->switchTime[] = $switch;
        }
        return $timer;
    }

    private static function entry( $verbosity, $source, $category, $message )
    {
        $entry = new stdClass();
        $entry->verbosity = $verbosity;
        $entry->source = $source;
        $entry->category = $category;
        $entry->message = $message;
        return $entry;
    }

    public function testEmptyOutput()
    {
        $formatter = new ezpRestDebugPHPFormatter();
        $this->assertSame( array( 'Logs' => array(), 'Timer' => array() ), $formatter->generateOutput( array(), array() ) );
    }

    public function testLogEntriesAreKeyedBySourceAndCategory()
    {
        $formatter = new ezpRestDebugPHPFormatter();
        $log = $formatter->getLog( array( self::entry( 1, 'rest', 'auth', 'first' ), self::entry( 2, 'rest', 'route', 'second' ),
                                          self::entry( 1, 'rest', 'auth', 'replaces first' ) ) );
        $this->assertSame( array( '1: rest::auth' => 'replaces first', '2: rest::route' => 'second' ), $log );
    }

    public function testOneGroupWithOneTimer()
    {
        $formatter = new ezpRestDebugPHPFormatter();
        $timers = $formatter->getTimingsAccumulator( array( self::timer( 'kernel', 'route', 10.0, 10.5, array( 'auth' => 10.25 ) ) ) );
        $this->assertSame( array( 'kernel' ), array_keys( $timers ) );
        $element = $timers['kernel']->elements[0];
        $this->assertSame( 'route', $element->name );
        $this->assertSame( '0.50000', $element->elapsed );
        $this->assertSame( '100.00 %', $element->percent );
        $this->assertSame( 1, $element->count );
        $this->assertSame( '0.50000', $element->average );
        $this->assertCount( 1, $element->switches );
        $this->assertSame( 'auth', $element->switches[0]->name );
        $this->assertSame( '0.25000', $element->switches[0]->elapsed );
        $this->assertSame( '50.00 %', $element->switches[0]->percent );
        $this->assertObjectNotHasProperty( 'totalElapsed', $timers['kernel'] );
    }

    public function testGroupWithSeveralTimersHasTotals()
    {
        $formatter = new ezpRestDebugPHPFormatter();
        $timers = $formatter->getTimingsAccumulator( array(
            self::timer( 'g', 'a', 0.0, 1.0 ),
            self::timer( 'g', 'b', 1.0, 4.0 ),
            self::timer( 'g', 'a', 4.0, 5.0 ),
            self::timer( 'other', 'x', 0.0, 2.0 ),
        ) );
        $this->assertSame( array( 'g', 'other' ), array_keys( $timers ) );
        $g = $timers['g'];
        $this->assertSame( '5.00000', $g->totalElapsed );
        $this->assertSame( 3, $g->count );
        $this->assertSame( '1.66667', $g->average );
        $this->assertSame( array( 'a', 'b' ), array( $g->elements[0]->name, $g->elements[1]->name ) );
        $this->assertSame( 2, $g->elements[0]->count );
        $this->assertSame( '2.00000', $g->elements[0]->elapsed );
        $this->assertSame( '40.00 %', $g->elements[0]->percent );
        $this->assertSame( '1.00000', $g->elements[0]->average );
        $this->assertSame( '60.00 %', $g->elements[1]->percent );
    }

    public function testTimersThatTookNoTimeDoNotDivideByZero()
    {
        $formatter = new ezpRestDebugPHPFormatter();
        $timers = $formatter->getTimingsAccumulator( array( self::timer( 'g', 'instant', 3.0, 3.0, array( 's' => 3.0 ) ) ) );
        $element = $timers['g']->elements[0];
        $this->assertSame( '0.00000', $element->elapsed );
        $this->assertSame( '0.00 %', $element->percent );
        $this->assertSame( '0.00 %', $element->switches[0]->percent );
    }
}
