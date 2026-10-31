<?php
/**
 * The shared dependency sort of the kernel's ordered registries (upgrade steps, migrations), without a database.
 *
 *  DO-01 the priority order is kept where nothing waits; a dependency moves an id behind what it waits for
 *  DO-02 dependencies outside the list are ignored, repeated ids and dependencies count once
 *  DO-03 a cycle (and what waits for it) is appended in priority order and reported as blocked; cycle() names it
 *  DO-04 the same result as the loop expUpgradeRegistry::order() used before it called expDependencyOrder, on
 *        random graphs
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expDependencyOrderTest extends PHPUnit\Framework\TestCase
{
    public static function setUpBeforeClass(): void
    {
        require_once dirname( __DIR__, 4 ) . '/kernel/classes/expdependencyorder.php';
    }

    /** DO-01 */
    public function testPriorityOrderIsKeptWhereNothingWaits()
    {
        $blocked = array( 'stale' );
        $this->assertSame( array( 'a', 'b', 'c' ), expDependencyOrder::sort( array( 'a', 'b', 'c' ), array(), $blocked ) );
        $this->assertSame( array(), $blocked );
        $this->assertSame( array(), expDependencyOrder::sort( array(), array() ) );
    }

    /** DO-01 */
    public function testADependencyMovesAnIdBehindWhatItWaitsFor()
    {
        $this->assertSame( array( 'b', 'c', 'a', 'd' ), expDependencyOrder::sort( array( 'a', 'b', 'c', 'd' ), array( 'a' => array( 'c' ) ) ) );
        // the earliest ready id always comes next: a's chain is pulled ahead of d, which waits for nothing
        $this->assertSame( array( 'c', 'b', 'a', 'd' ), expDependencyOrder::sort( array( 'a', 'b', 'c', 'd' ), array( 'a' => array( 'b' ), 'b' => array( 'c' ) ) ) );
        $this->assertSame( array( 'b', 'a', 'c' ), expDependencyOrder::sort( array( 'a', 'b', 'c' ), array( 'a' => array( 'b' ), 'c' => array( 'a' ) ) ) );
    }

    /** DO-02 */
    public function testUnknownDependenciesAndRepeatsCountOnce()
    {
        $blocked = array();
        $this->assertSame( array( 'a', 'b' ), expDependencyOrder::sort( array( 'a', 'b', 'a' ), array( 'a' => array( 'missing', 'missing' ) ), $blocked ) );
        $this->assertSame( array(), $blocked );
        $this->assertSame( array( 'b', 'a' ), expDependencyOrder::sort( array( 'a', 'b' ), array( 'a' => array( 'b', 'b' ) ) ) );
    }

    /** DO-03 */
    public function testCyclesAreBlockedAndNamed()
    {
        $requires = array( 'a' => array( 'b' ), 'b' => array( 'c' ), 'c' => array( 'a' ), 'd' => array( 'a' ) );
        $blocked = array();
        $this->assertSame( array( 'e', 'a', 'b', 'c', 'd' ), expDependencyOrder::sort( array( 'a', 'b', 'c', 'd', 'e' ), $requires, $blocked ) );
        $this->assertSame( array( 'a', 'b', 'c', 'd' ), $blocked );
        $this->assertSame( array( 'a', 'b', 'c', 'a' ), expDependencyOrder::cycle( $blocked, $requires ) );
        $this->assertSame( array( 'a', 'b', 'c', 'a' ), expDependencyOrder::cycle( array( 'd', 'a', 'b', 'c' ), $requires ), 'd only waits for the cycle' );

        $blocked = array();
        $this->assertSame( array( 'x' ), expDependencyOrder::sort( array( 'x' ), array( 'x' => array( 'x' ) ), $blocked ) );
        $this->assertSame( array( 'x' ), $blocked, 'an id that waits for itself' );
        $this->assertSame( array( 'x', 'x' ), expDependencyOrder::cycle( $blocked, array( 'x' => array( 'x' ) ) ) );
        $this->assertSame( array(), expDependencyOrder::cycle( array(), $requires ) );
    }

    /**
     * The loop of expUpgradeRegistry::order() before 6.0.15's migrations: each round the first remaining id whose
     * dependencies are all placed.
     */
    private static function previousOrder( array $ids, array $requires, &$cycle )
    {
        $known = array_flip( $ids );
        $ordered = array();
        $placed = array();
        $remaining = $ids;
        $cycle = array();
        while ( $remaining )
        {
            $progress = false;
            foreach ( $remaining as $k => $id )
            {
                $ready = true;
                foreach ( isset( $requires[$id] ) ? $requires[$id] : array() as $dep )
                    if ( isset( $known[$dep] ) && !isset( $placed[$dep] ) )
                        $ready = false;
                if ( !$ready )
                    continue;
                $ordered[] = $id;
                $placed[$id] = true;
                unset( $remaining[$k] );
                $progress = true;
                break;
            }
            if ( !$progress )
            {
                $cycle = array_values( $remaining );
                foreach ( $remaining as $id )
                    $ordered[] = $id;
                break;
            }
        }
        return $ordered;
    }

    /** DO-04 */
    public function testSameResultAsThePreviousRegistryLoop()
    {
        mt_srand( 287 );
        for ( $round = 0; $round < 300; $round++ )
        {
            $n = mt_rand( 1, 25 );
            $ids = array();
            for ( $i = 0; $i < $n; $i++ )
                $ids[] = 's' . $i;
            shuffle( $ids );
            $requires = array();
            foreach ( $ids as $id )
                for ( $j = mt_rand( 0, 3 ); $j > 0; $j-- )
                    $requires[$id][] = mt_rand( 0, 9 ) === 0 ? 'unknown' : $ids[mt_rand( 0, $n - 1 )];
            $previousCycle = array();
            $expected = self::previousOrder( $ids, $requires, $previousCycle );
            $blocked = array();
            $this->assertSame( $expected, expDependencyOrder::sort( $ids, $requires, $blocked ), "round $round" );
            $this->assertSame( $previousCycle, $blocked, "round $round" );
        }
    }
}
