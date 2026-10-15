<?php
/**
 * ezjscnode::updatepriority's lists for admin4's drag and drop reordering of the sub items: the compact
 * "node_id:priority" lists and their limits, the check of the priorities the table showed (a conflict when one
 * changed or is no longer a child), and the renumbering of all children in steps of 10 with the page's rows in their
 * new order at their place, ascending and descending. The helpers alone: no database.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/ezjscore/ezjscServerFunctionsNodePriorityTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class ezjscServerFunctionsNodePriorityTest extends PHPUnit\Framework\TestCase
{
    public function testPriorityListsAreParsedStrictly()
    {
        $this->assertSame( array(), ezjscServerFunctionsNode::parsePriorityList( '' ) );
        $this->assertSame( array( 12 => 10, 7 => -5, 99 => 0 ), ezjscServerFunctionsNode::parsePriorityList( '12:10, 7:-5,99:0' ) );
        $this->assertSame( array( 3 => 2147483647 ), ezjscServerFunctionsNode::parsePriorityList( '3:2147483647' ) );
        foreach ( array( '0:1', '-3:1', 'a:1', '3', '3:', '3:1.5', '3:1;DROP', '3:2147483648', '3:1,', array( '3:1' ) ) as $bad )
        {
            try
            {
                ezjscServerFunctionsNode::parsePriorityList( $bad );
                $this->fail( 'accepted: ' . var_export( $bad, true ) );
            }
            catch ( InvalidArgumentException $e )
            {
                $this->addToAssertionCount( 1 );
            }
        }
        $this->expectException( InvalidArgumentException::class );
        ezjscServerFunctionsNode::parsePriorityList( implode( ',', array_fill( 0, 10001, '1:1' ) ) );
    }

    public function testIDListsAreParsedStrictly()
    {
        $this->assertSame( array( 4, 3, 9 ), ezjscServerFunctionsNode::parseIDList( '4,3, 9' ) );
        foreach ( array( '', '4,,3', '4,4', '0', '4;3', 'x', array() ) as $bad )
        {
            try
            {
                ezjscServerFunctionsNode::parseIDList( $bad );
                $this->fail( 'accepted: ' . var_export( $bad, true ) );
            }
            catch ( InvalidArgumentException $e )
            {
                $this->addToAssertionCount( 1 );
            }
        }
    }

    public function testChangedOrMissingPrioritiesAreConflicts()
    {
        $current = array( 1 => 10, 2 => 20, 3 => 30 );
        $this->assertSame( array(), ezjscServerFunctionsNode::priorityConflicts( array( 1 => 10, 3 => 30 ), $current ) );
        $this->assertSame( array( 2 ), ezjscServerFunctionsNode::priorityConflicts( array( 2 => 25, 3 => 30 ), $current ) );
        $this->assertSame( array( 2, 8 ), ezjscServerFunctionsNode::priorityConflicts( array( 8 => 0, 2 => 0 ), $current ), 'not a child' );
        $this->assertSame( array(), ezjscServerFunctionsNode::priorityConflicts( array(), $current ) );
    }

    public function testRenumberingPutsThePageInPlace()
    {
        // seven children, a page of three at offset 2 moved to 5, 3, 4
        $all = array( 1, 2, 3, 4, 5, 6, 7 );
        $this->assertSame( array( 1 => 10, 2 => 20, 5 => 30, 3 => 40, 4 => 50, 6 => 60, 7 => 70 ),
                           ezjscServerFunctionsNode::renumberedOrder( $all, array( 5, 3, 4 ), 2, true ) );
        // descending: the first row gets the highest value
        $this->assertSame( array( 1 => 70, 2 => 60, 5 => 50, 3 => 40, 4 => 30, 6 => 20, 7 => 10 ),
                           ezjscServerFunctionsNode::renumberedOrder( $all, array( 5, 3, 4 ), 2, false ) );
        // the rows outside the page keep the server's order (equal priorities by node id); the page lands at its offset
        $this->assertSame( array( 2 => 10, 3 => 20, 4 => 30, 1 => 40 ),
                           ezjscServerFunctionsNode::renumberedOrder( array( 2, 1, 3, 4 ), array( 3, 4 ), 1, true ) );
        // a row that is no longer a child is left out; an offset past the end puts the page last
        $this->assertSame( array( 1 => 10, 2 => 20, 3 => 30 ),
                           ezjscServerFunctionsNode::renumberedOrder( array( 1, 2, 3 ), array( 3, 9 ), 50, true ) );
    }
}
