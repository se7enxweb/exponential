<?php
/**
 * File containing the eZPackageLicenseTest class.
 *
 * The licenses package.ini [LicenseSettings] offers to the package creation wizards: the
 * default list, the default license, the "GPL" alias older packages store, refusal of
 * anything not listed, and how a stored free-text license is described. Reads the
 * shipped settings; no database needed.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class eZPackageLicenseTest extends ezpTestCase
{
    public function testDefaultListIsCompleteAndGrouped()
    {
        $list = eZPackageLicense::licenseList();
        foreach ( array( 'GPL-2.0-or-later', 'GPL-2.0-only', 'GPL-3.0-or-later', 'GPL-3.0-only', 'LGPL-2.1-or-later',
                         'LGPL-2.1-only', 'LGPL-3.0-or-later', 'LGPL-3.0-only', 'GFDL-1.3-or-later', 'GFDL-1.3-only',
                         'CC0-1.0', 'LicenseRef-PDM-1.0' ) as $identifier )
            $this->assertArrayHasKey( $identifier, $list );
        foreach ( array( '1.0', '2.0', '2.5', '3.0', '4.0' ) as $version )
            foreach ( array( 'BY', 'BY-SA', 'BY-ND', 'BY-NC', 'BY-NC-SA', 'BY-NC-ND' ) as $kind )
                $this->assertArrayHasKey( "CC-$kind-$version", $list );
        foreach ( $list as $identifier => $license )
        {
            $this->assertNotEmpty( $license['name'], $identifier );
            $this->assertStringStartsWith( 'https://', $license['url'], $identifier );
        }
        $this->assertSame( 'Creative Commons Attribution-ShareAlike 4.0 International', $list['CC-BY-SA-4.0']['name'] );
        $this->assertSame( 'https://creativecommons.org/licenses/by-nd-nc/1.0/', $list['CC-BY-NC-ND-1.0']['url'] );

        $groups = eZPackageLicense::groupedList();
        $identifiers = array();
        $count = 0;
        foreach ( $groups as $group )
        {
            $identifiers[] = $group['identifier'];
            $count += count( $group['licenses'] );
        }
        $this->assertSame( array( 'software', 'documentation', 'cc-4.0', 'cc-3.0', 'cc-2.5', 'cc-2.0', 'cc-1.0', 'public-domain' ), $identifiers );
        $this->assertSame( count( $list ), $count );
    }

    public function testDefaultAndAlias()
    {
        $this->assertSame( 'GPL-2.0-or-later', eZPackageLicense::defaultIdentifier() );
        $this->assertSame( 'GPL-2.0-or-later', eZPackageLicense::normalize( 'GPL' ) );
        $this->assertSame( 'CC-BY-4.0', eZPackageLicense::normalize( 'CC-BY-4.0' ) );
    }

    public function testAnythingNotListedIsRefused()
    {
        foreach ( array( 'Beerware', 'gpl-2.0-or-later', '', ' ', 'GPL-2.0-or-later<script>', 'CC-BY-5.0', 'MIT' ) as $value )
            $this->assertFalse( eZPackageLicense::isAllowed( $value ), var_export( $value, true ) );
        $this->assertFalse( eZPackageLicense::isAllowed( array( 'GPL-2.0-or-later' ) ) );
        $this->assertFalse( eZPackageLicense::isAllowed( null ) );
    }

    public function testStoredValuesAreDescribed()
    {
        $old = eZPackageLicense::describe( 'GPL' );
        $this->assertTrue( $old['known'] );
        $this->assertSame( 'GPL-2.0-or-later', $old['identifier'] );
        $this->assertSame( 'GPL', $old['stored'] );

        $free = eZPackageLicense::describe( 'Some free text license' );
        $this->assertFalse( $free['known'] );
        $this->assertSame( 'Some free text license', $free['name'] );
        $this->assertFalse( $free['url'] );

        $this->assertFalse( eZPackageLicense::describe( '' ) );
        $this->assertFalse( eZPackageLicense::describe( false ) );
    }
}

?>
