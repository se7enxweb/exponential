<?php
/**
 * ezpActiveExtensions::merge(): what Setup > Extensions saves.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and others. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class ezpActiveExtensionsTest extends ezpTestCase
{
    private $available = array( 'a', 'b', 'c', 'd', 'e', 'f' );

    /** Saving one page keeps the active extensions of the other pages. */
    public function testExtensionsNotShownKeepTheirState()
    {
        // Page 1 shows a, b, c; d and e are active on page 2.
        $this->assertSame( array( 'a', 'd', 'e' ),
            ezpActiveExtensions::merge( array( 'a', 'd', 'e' ), array( 'a' ), array( 'a', 'b', 'c' ), $this->available ) );
    }

    /** An extension shown and unchecked is switched off; one checked is added at the end. */
    public function testShownExtensionsFollowTheForm()
    {
        $this->assertSame( array( 'd', 'b' ),
            ezpActiveExtensions::merge( array( 'a', 'd' ), array( 'b' ), array( 'a', 'b', 'c' ), $this->available ) );
    }

    /** The order of the active extensions is kept. */
    public function testOrderIsKept()
    {
        $this->assertSame( array( 'c', 'a', 'b' ),
            ezpActiveExtensions::merge( array( 'c', 'a', 'b' ), array( 'a', 'b', 'c' ), array( 'a', 'b', 'c' ), $this->available ) );
    }

    /** Access extensions are never moved into ActiveExtensions; unknown names are ignored. */
    public function testAccessExtensionsAndUnknownNames()
    {
        $this->assertSame( array( 'a' ),
            ezpActiveExtensions::merge( array( 'a' ), array( 'a', 'f', 'nope', '../x' ), array( 'a', 'f' ), $this->available, array( 'f' ) ) );
    }

    /** The case that destroyed a site's list: saving page 1 of 3 without the shown list. */
    public function testAFullListSurvivesSavingAnyPage()
    {
        $active = array( 'a', 'b', 'c', 'd', 'e', 'f' );
        foreach ( array( array( 'a', 'b' ), array( 'c', 'd' ), array( 'e', 'f' ) ) as $page )
            $this->assertSame( $active, ezpActiveExtensions::merge( $active, $page, $page, $this->available ), implode( ',', $page ) );
    }
}
