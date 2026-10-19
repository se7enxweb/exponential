<?php
/**
 * eZUpdatePackageServers::releasePath() (ezupdate 1.1.24): the release's folder on a package server, x.y/x.y.z for a
 * stable release, the state appended otherwise; ExponentialSDK::packageServerPath() when the kernel has it, the same
 * rule for an older kernel. Before 1.1.24 the built-in server's fallback address ended in 6.0/6.0.15stable, a folder
 * the package server does not have. No database.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/ezupdate/ezUpdatePackageServersReleasePathTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class ezUpdateReleasePathOldStableSDK
{
    static function version( $withRelease = true, $asAlias = false, $withState = true )
    {
        return '5.4' . ( $withRelease ? '.3' : '' ) . ( $withState ? 'stable' : '' );
    }
    static function state() { return 'stable'; }
}

class ezUpdateReleasePathOldAlphaSDK
{
    static function version( $withRelease = true, $asAlias = false, $withState = true )
    {
        return '6.0' . ( $withRelease ? '.16' : '' ) . ( $withState ? 'alpha1' : '' );
    }
    static function state() { return 'alpha1'; }
}

class ezUpdatePackageServersReleasePathTest extends PHPUnit\Framework\TestCase
{
    public static function setUpBeforeClass(): void
    {
        $root = dirname( __DIR__, 4 );
        require_once $root . '/lib/version.php';
        $file = $root . '/extension/ezupdate/classes/ezupdatepackageservers.php';
        if ( !class_exists( 'eZUpdatePackageServers', false ) && is_file( $file ) )
            require_once $file;
    }

    protected function setUp(): void
    {
        if ( !class_exists( 'eZUpdatePackageServers' ) || !method_exists( 'eZUpdatePackageServers', 'releasePath' ) )
            $this->markTestSkipped( 'ezupdate 1.1.24 or newer is not installed.' );
    }

    public function testUsesTheKernelsPackageServerPath()
    {
        $this->assertSame( ExponentialSDK::packageServerPath(), eZUpdatePackageServers::releasePath( 'ExponentialSDK' ) );
        $this->assertStringNotContainsString( 'stable', eZUpdatePackageServers::releasePath( 'ExponentialSDK' ) );
    }

    public function testOlderKernelStableReleaseHasNoStateWord()
    {
        $this->assertSame( '5.4/5.4.3', eZUpdatePackageServers::releasePath( 'ezUpdateReleasePathOldStableSDK' ) );
    }

    public function testOlderKernelPrereleaseKeepsItsState()
    {
        $this->assertSame( '6.0/6.0.16alpha1', eZUpdatePackageServers::releasePath( 'ezUpdateReleasePathOldAlphaSDK' ) );
    }
}
