<?php
/**
 * The view counter gives each line of a shared log to the siteaccess the kernel's matching finds for it
 * (eZSiteAccess::matchAddress()): by the first path element (MatchOrder uri), else by the host, else DefaultAccess,
 * with the matched part of the path removed. A name that needs normalizing (bold-ger) does not redirect and end
 * the process, as it would for a web request with RedirectOnNormalize.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

#[\PHPUnit\Framework\Attributes\Group('kernel')]
class expViewCountSiteAccessMatchTest extends ezpTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        ezpINIHelper::setINISetting( 'site.ini', 'SiteSettings', 'DefaultAccess', 'site' );
        ezpINIHelper::setINISetting( 'site.ini', 'SiteAccessSettings', 'AvailableSiteAccessList', array( 'site', 'bold', 'bold_ger', 'admin', 'shop' ) );
        ezpINIHelper::setINISetting( 'site.ini', 'SiteAccessSettings', 'MatchOrder', 'uri;host' );
        ezpINIHelper::setINISetting( 'site.ini', 'SiteAccessSettings', 'URIMatchType', 'element' );
        ezpINIHelper::setINISetting( 'site.ini', 'SiteAccessSettings', 'URIMatchElement', '1' );
        ezpINIHelper::setINISetting( 'site.ini', 'SiteAccessSettings', 'HostMatchType', 'map' );
        ezpINIHelper::setINISetting( 'site.ini', 'SiteAccessSettings', 'HostMatchMapItems', array( 'shop.example.com;shop' ) );
        ezpINIHelper::setINISetting( 'site.ini', 'SiteAccessSettings', 'NormalizeSANames', 'enabled' );
        ezpINIHelper::setINISetting( 'site.ini', 'SiteAccessSettings', 'RedirectOnNormalize', 'enabled' );
    }

    public function tearDown(): void
    {
        ezpINIHelper::restoreINISettings();
        parent::tearDown();
    }

    private static function updater()
    {
        return new expViewCountUpdater( new expViewCountConfiguration( array( 'state_dir' => '' ), array() ) );
    }

    public function testTheFirstElementTheHostAndTheDefault()
    {
        $updater = self::updater();
        $this->assertSame( array( 'bold', 1 ), $updater->matchSiteAccess( 'www.example.com', 443, array( 'bold', 'News' ) ) );
        $this->assertSame( array( 'shop', 0 ), $updater->matchSiteAccess( 'shop.example.com', 443, array( 'Products' ) ) );
        $this->assertSame( array( 'site', 0 ), $updater->matchSiteAccess( 'www.example.com', 443, array( 'News' ) ) );
        $this->assertSame( array( 'site', 0 ), $updater->matchSiteAccess( '', 80, array() ) );
        $this->assertSame( array( 'admin', 1 ), $updater->matchSiteAccess( 'www.example.com', 443, array( 'admin', 'content', 'dashboard' ) ) );
    }

    public function testANameToNormalizeNeitherRedirectsNorEnds()
    {
        $this->assertSame( array( 'bold_ger', 1 ), self::updater()->matchSiteAccess( 'www.example.com', 443, array( 'bold-ger', 'Nachrichten' ) ) );
        $access = eZSiteAccess::matchAddress( '/nothing-here', 'www.example.com' );
        $this->assertSame( 'site', $access['name'] );
        $this->assertTrue( $access['unmatched'] );
    }
}
