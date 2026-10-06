<?php
/**
 * ezpTopologicalSort and ezpTopologicalSortNode: the order of extensions with dependencies (each name comes after
 * every name it depends on), the order of independent names kept, cycles reported as false, nodes that are only
 * named as a dependency, and the node bookkeeping.
 *
 * No database and no siteaccess.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class ezpTopologicalSortTest extends PHPUnit\Framework\TestCase
{
    private function assertBefore( array $sorted, $first, $second )
    {
        $a = array_search( $first, $sorted, true );
        $b = array_search( $second, $sorted, true );
        $this->assertNotFalse( $a, "$first missing" );
        $this->assertNotFalse( $b, "$second missing" );
        $this->assertLessThan( $b, $a, "$first must come before $second in " . implode( ',', $sorted ) );
    }

    public function testEmptyGraphSortsToEmptyList()
    {
        $sort = new ezpTopologicalSort();
        $this->assertSame( array(), $sort->sort() );
    }

    public function testIndependentNamesKeepTheirOrder()
    {
        $sort = new ezpTopologicalSort( array_fill_keys( range( 'a', 'z' ), null ) );
        $this->assertSame( range( 'a', 'z' ), $sort->sort() );
    }

    public function testEveryNameComesAfterItsDependencies()
    {
        $sort = new ezpTopologicalSort( array( 'a' => null, 'c' => 'b', 'b' => 'a', 'd' => 'b', 'e' => array( 'd', 'c' ) ) );
        $sorted = $sort->sort();
        $this->assertCount( 5, $sorted );
        $this->assertBefore( $sorted, 'a', 'b' );
        $this->assertBefore( $sorted, 'b', 'c' );
        $this->assertBefore( $sorted, 'b', 'd' );
        $this->assertBefore( $sorted, 'c', 'e' );
        $this->assertBefore( $sorted, 'd', 'e' );
    }

    public function testNameOnlyGivenAsDependencyIsIncluded()
    {
        $sort = new ezpTopologicalSort( array( 'site' => array( 'design', 'core' ) ) );
        $sorted = $sort->sort();
        $this->assertCount( 3, $sorted );
        $this->assertSame( 'site', $sorted[2] );
        $this->assertEqualsCanonicalizing( array( 'design', 'core' ), array_slice( $sorted, 0, 2 ) );
    }

    public function testComplexGraph()
    {
        $graph = array( 'a' => null, 'c' => 'b', 'b' => 'a', 'd' => 'b', 'e' => array( 'd', 'c' ), 'f' => range( 'a', 'e' ),
                        'g' => range( 'h', 'k' ), 'h' => array( 'j', 'k' ), 'k' => 'j', 'j' => 'e', 'l' => 'm', 'm' => 'n',
                        'o' => 'p', 'p' => 'g' );
        $sort = new ezpTopologicalSort( $graph );
        $sorted = $sort->sort();
        $this->assertCount( 16, $sorted );
        $this->assertSame( count( $sorted ), count( array_unique( $sorted ) ) );
        foreach ( $graph as $name => $dependencies )
            foreach ( (array)$dependencies as $dependency )
                $this->assertBefore( $sorted, $dependency, $name );
    }

    public function testRepeatedDependencyCountsOnce()
    {
        $sort = new ezpTopologicalSort( array( 'b' => array( 'a', 'a' ), 'a' => null ) );
        $this->assertSame( array( 'a', 'b' ), $sort->sort() );
    }

    public static function cycleProvider()
    {
        return array(
            'two' => array( array( 'a' => 'b', 'b' => 'a' ) ),
            'self' => array( array( 'a' => 'a' ) ),
            'three' => array( array( 'a' => 'b', 'b' => 'c', 'c' => 'a' ) ),
            'cycle behind a free name' => array( array( 'x' => null, 'a' => array( 'x', 'b' ), 'b' => 'a' ) ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('cycleProvider')]
    public function testCycleGivesFalse( $graph )
    {
        $sort = new ezpTopologicalSort( $graph );
        $this->assertFalse( $sort->sort() );
    }

    public function testNodeBookkeeping()
    {
        $parent = new ezpTopologicalSortNode( 'p' );
        $child = new ezpTopologicalSortNode( 'c' );
        $other = new ezpTopologicalSortNode( 'o' );
        $this->assertSame( 'p', $parent->name );
        $this->assertSame( 0, $child->parentCount() );
        $child->registerParent( $parent );
        $child->registerParent( $parent );
        $this->assertSame( 1, $child->parentCount() );
        $child->registerParent( $other );
        $this->assertSame( 2, $child->parentCount() );
        $child->unregisterParent( $parent );
        $this->assertSame( 1, $child->parentCount() );

        $this->assertFalse( $parent->popChild() );
        $parent->registerChild( $child );
        $parent->registerChild( $child );
        $parent->registerChild( $other );
        $popped = array( $parent->popChild()->name, $parent->popChild()->name );
        $this->assertEqualsCanonicalizing( array( 'c', 'o' ), $popped );
        $this->assertFalse( $parent->popChild() );
    }
}
