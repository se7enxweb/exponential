<?php
/**
 * File containing the eZPackageVersionTest class.
 *
 * How package versions are read, validated and ordered: Semantic Versioning 2.0.0
 * precedence (the examples of section 11 of the specification), the older forms
 * packages were made with ("1.0", "1.0-1", "3.4.0beta1", four parts) mixed in, and the
 * strict check new packages from the creation wizards must pass. No database needed.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class eZPackageVersionTest extends ezpTestCase
{
    /**
     * SemVer 2.0.0 section 11: every entry has a lower precedence than the next one.
     */
    public function testSemVerSpecificationPrecedence()
    {
        $ordered = array( '1.0.0-alpha', '1.0.0-alpha.1', '1.0.0-alpha.beta', '1.0.0-beta', '1.0.0-beta.2',
                          '1.0.0-beta.11', '1.0.0-rc.1', '1.0.0', '2.0.0', '2.1.0', '2.1.1' );
        for ( $i = 0; $i < count( $ordered ) - 1; ++$i )
        {
            $this->assertSame( -1, eZPackageVersion::compare( $ordered[$i], $ordered[$i + 1] ), $ordered[$i] . ' < ' . $ordered[$i + 1] );
            $this->assertSame( 1, eZPackageVersion::compare( $ordered[$i + 1], $ordered[$i] ), $ordered[$i + 1] . ' > ' . $ordered[$i] );
        }
        $shuffled = array_reverse( $ordered );
        $this->assertSame( $ordered, eZPackageVersion::sort( $shuffled ) );
    }

    public function testNumbersCompareNumerically()
    {
        $this->assertSame( -1, eZPackageVersion::compare( '1.9.0', '1.10.0' ) );
        $this->assertSame( -1, eZPackageVersion::compare( '1.0.9', '1.0.10' ) );
        $this->assertSame( -1, eZPackageVersion::compare( '1.0.0-beta.9', '1.0.0-beta.10' ) );
        $this->assertSame( -1, eZPackageVersion::compare( '1.0.0-1', '1.0.0-alpha' ), 'numeric identifiers before alphanumeric ones' );
    }

    public function testBuildMetadataIsIgnored()
    {
        $this->assertSame( 0, eZPackageVersion::compare( '1.0.0+20260930', '1.0.0' ) );
        $this->assertSame( 0, eZPackageVersion::compare( '1.0.0-beta+exp.sha.5114f85', '1.0.0-beta' ) );
    }

    public function testLegacyShortVersionsReadAsThreeParts()
    {
        $this->assertSame( 0, eZPackageVersion::compare( '1.0', '1.0.0' ) );
        $this->assertSame( 0, eZPackageVersion::compare( '1', '1.0.0' ) );
        $this->assertSame( -1, eZPackageVersion::compare( '1.0', '1.0.1' ) );
        $this->assertSame( 1, eZPackageVersion::compare( '1.1', '1.0.9' ) );
        $parsed = eZPackageVersion::parse( '1.0' );
        $this->assertSame( array( 1, 0, 0 ), $parsed['numbers'] );
        $this->assertFalse( $parsed['semver'] );
    }

    public function testLegacyLetterSuffixIsAPrerelease()
    {
        $this->assertSame( -1, eZPackageVersion::compare( '3.4.0beta1', '3.4.0' ) );
        $this->assertSame( 1, eZPackageVersion::compare( '3.4.0beta1', '3.3.9' ) );
        $this->assertSame( -1, eZPackageVersion::compare( '3.4.0alpha1', '3.4.0beta1' ) );
        $this->assertSame( 0, eZPackageVersion::compare( '3.4.0beta1', '3.4.0-beta1' ) );
        $this->assertSame( -1, eZPackageVersion::compare( '1.0rc2', '1.0' ) );
    }

    public function testLegacyFourPartsCompareAfterPatch()
    {
        $this->assertSame( -1, eZPackageVersion::compare( '1.2.3', '1.2.3.1' ) );
        $this->assertSame( 0, eZPackageVersion::compare( '1.2.3.0', '1.2.3' ) );
        $this->assertSame( -1, eZPackageVersion::compare( '1.2.3.4', '1.2.4' ) );
    }

    /**
     * "<number>-<release>" pairs (eZPackage::getVersion(), site package min-version): the
     * legacy release number is compared after the version number.
     */
    public function testVersionReleasePairs()
    {
        $this->assertSame( array( '1.0', 1 ), eZPackageVersion::splitRelease( '1.0-1' ) );
        $this->assertSame( array( '1.0.0-beta.1', 2 ), eZPackageVersion::splitRelease( '1.0.0-beta.1-2' ) );
        $this->assertSame( array( '1.0.0-beta', false ), eZPackageVersion::splitRelease( '1.0.0-beta' ) );
        $this->assertSame( 0, eZPackageVersion::compareFull( '1.0-1', '1.0.0-1' ) );
        $this->assertSame( -1, eZPackageVersion::compareFull( '1.0-1', '1.0-2' ) );
        $this->assertSame( -1, eZPackageVersion::compareFull( '1.0-2', '1.0.1-1' ) );
        $this->assertSame( -1, eZPackageVersion::compareFull( '1.0-9', '1.0-10' ) );
        $this->assertSame( 1, eZPackageVersion::compareFull( '1.0-1', '1.0' ), 'a missing release counts as 0' );
        $this->assertSame( 1, eZPackageVersion::compareFull( '1.0-1', 0 ), 'a missing requirement (0) is met' );
        $this->assertSame( -1, eZPackageVersion::compareFull( '1.0.0-beta.1-1', '1.0.0-1' ) );
        $this->assertSame( array( '0.9-3', '1.0-1', '1.0.0-2', '1.0.1-1', '1.2.3-1', '1.10-1', '2.0.0-alpha-1', '2.0-1' ),
                           eZPackageVersion::sort( array( '2.0-1', '1.10-1', '1.0.0-2', '2.0.0-alpha-1', '1.2.3-1', '0.9-3', '1.0.1-1', '1.0-1' ), true ) );
    }

    public function testUnreadableVersionsSortFirstInNaturalOrder()
    {
        $this->assertFalse( eZPackageVersion::parse( 'latest' ) );
        $this->assertFalse( eZPackageVersion::parse( '' ) );
        $this->assertSame( -1, eZPackageVersion::compare( 'latest', '0.0.1' ) );
        $this->assertSame( 1, eZPackageVersion::compare( '0.0.1', 'latest' ) );
        $this->assertSame( -1, eZPackageVersion::compare( 'build 9', 'build 10' ) );
    }

    public function testNewPackagesNeedStrictSemVer()
    {
        foreach ( array( '1.0.0', '1.2.3', '0.0.1', '10.20.30', '3.4.0-beta.1', '1.0.0-rc.1+build.5', '1.0.0+20260930' ) as $valid )
            $this->assertTrue( eZPackageVersion::isValidNew( $valid ), $valid );
        foreach ( array( '1.0', '1', '3.4.0beta1', '01.0.0', '1.0.0-', '1.0.0-01', '1.0.0+', 'v1.0.0', ' 1.0.0', '1.0.0 ', '1.2.3.4', '', 'abc',
                         '1.0.0-' . str_repeat( 'a', 30 ) ) as $invalid )
            $this->assertFalse( eZPackageVersion::isValidNew( $invalid ), var_export( $invalid, true ) );
        $this->assertFalse( eZPackageVersion::isValidNew( array( '1.0.0' ) ) );
        $this->assertTrue( eZPackageVersion::isValidNew( eZPackageVersion::DEFAULT_NEW_VERSION ) );
        $this->assertSame( '1.0.0', eZPackageVersion::DEFAULT_NEW_VERSION );
    }
}

?>
