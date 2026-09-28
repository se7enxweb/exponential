<?php
/**
 * ezpTemplateOverrides: the checks and numbers behind reordering the
 * overrides of a template on Design > Templates.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and others. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class ezpTemplateOverridesTest extends ezpTestCase
{
    /** A reorder is taken as it is when it holds exactly the overrides shown. */
    public function testReorderAcceptsAPermutation()
    {
        $this->assertSame( array( 'b', 'c', 'a' ), ezpTemplateOverrides::reorder( array( 'a', 'b', 'c' ), array( 'b', 'c', 'a' ) ) );
    }

    /** One missing, added, swapped or twice: refused. */
    public function testReorderRefusesAnythingButAPermutation()
    {
        $shown = array( 'a', 'b', 'c' );
        $this->assertFalse( ezpTemplateOverrides::reorder( $shown, array( 'a', 'b' ) ) );
        $this->assertFalse( ezpTemplateOverrides::reorder( $shown, array( 'a', 'b', 'c', 'd' ) ) );
        $this->assertFalse( ezpTemplateOverrides::reorder( $shown, array( 'a', 'b', 'd' ) ) );
        $this->assertFalse( ezpTemplateOverrides::reorder( $shown, array( 'a', 'a', 'b', 'c' ) ) );
    }

    /** Priorities count in tens from the first. */
    public function testPriorities()
    {
        $this->assertSame( array( 'c' => 10, 'a' => 20, 'b' => 30 ), ezpTemplateOverrides::priorities( array( 'c', 'a', 'b' ) ) );
    }

    /** A siteaccess name that could leave settings/siteaccess is not one. */
    public function testSiteAccessNameIsChecked()
    {
        $bad = new ezpTemplateOverrides( '../override' );
        $this->assertFalse( $bad->dir() );
        $good = new ezpTemplateOverrides( 'site' );
        $this->assertSame( 'settings/siteaccess/site', $good->dir() );
    }
}
