<?php
/**
 * ExponentialSDK::packageServerPath(), the directory of this release on a package server: "x.y/x.y.z" for a stable
 * release, with the state appended otherwise. Before 6.0.15 the setup wizard always appended the state and asked
 * for .../6.0/6.0.15stable/index.xml, which the package server answers with 404; the server's layout is
 * <RemotePackagesIndexURLBase>/6.0/6.0.15/index.xml.
 *
 * No kernel bootstrap, no database, no network.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 */

class ExponentialSDKPackageServerPathTest extends PHPUnit\Framework\TestCase
{
    public static function setUpBeforeClass(): void
    {
        require_once dirname( __DIR__, 4 ) . '/lib/version.php';
    }

    public function testPathIsMajorMinorThenFullRelease()
    {
        $expected = ExponentialSDK::VERSION_MAJOR . '.' . ExponentialSDK::VERSION_MINOR . '/'
                  . ExponentialSDK::VERSION_MAJOR . '.' . ExponentialSDK::VERSION_MINOR . '.' . ExponentialSDK::VERSION_RELEASE;
        $state = (string)ExponentialSDK::VERSION_STATE;
        if ( $state !== '' && strtolower( $state ) !== 'stable' )
        {
            $expected .= $state;
        }
        $this->assertSame( $expected, ExponentialSDK::packageServerPath() );
    }

    public function testStableReleaseCarriesNoStateWord()
    {
        if ( strtolower( (string)ExponentialSDK::VERSION_STATE ) !== 'stable' )
        {
            $this->markTestSkipped( 'This build is not a stable release.' );
        }
        $this->assertStringNotContainsString( 'stable', ExponentialSDK::packageServerPath() );
        $this->assertMatchesRegularExpression( '#^\d+\.\d+/\d+\.\d+\.\d+$#', ExponentialSDK::packageServerPath() );
    }

    /**
     * The setup wizard's fallback (RemotePackagesIndexURL empty) builds the URL with this method, not with version().
     */
    public function testSetupWizardFallbackUsesThePackageServerPath()
    {
        $source = file_get_contents( dirname( __DIR__, 4 ) . '/kernel/setup/steps/ezstep_site_types.php' );
        $this->assertStringContainsString( 'ExponentialSDK::packageServerPath()', $source );
        $this->assertStringNotContainsString( "ExponentialSDK::version() . '/'", $source );
    }
}
