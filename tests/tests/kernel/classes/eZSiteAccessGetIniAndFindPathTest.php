<?php
/**
 * eZSiteAccess::getIni() reads the settings of another siteaccess without changing the current one, and
 * eZSiteAccess::findPathToSiteAccess() finds a siteaccess's settings directory only when it is in
 * AvailableSiteAccessList. These were the tests of eZSiteAccess_Test, which lived twice under file names that differ
 * only in case (eZSiteAccess_Test.php and ezsiteaccess_test.php); they live once here, under a name no other test file
 * shares in any case. No database. Runs in its own process, because it changes the global site.ini.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/eZSiteAccessGetIniAndFindPathTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

#[RunTestsInSeparateProcesses]
class eZSiteAccessGetIniAndFindPathTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
    }

    /**
     * The current siteaccess is untouched when another siteaccess's settings are read, and those settings are the
     * other siteaccess's (settings/siteaccess/plain says SiteDesign=plain).
     */
    public function testGetIniReadsAnotherSiteAccessWithoutChangingTheCurrentOne()
    {
        $current = eZSiteAccess::current();
        $ini = eZSiteAccess::getIni( 'plain' );
        $this->assertEquals( $current, eZSiteAccess::current() );
        $this->assertSame( 'plain', $ini->variable( 'DesignSettings', 'SiteDesign' ) );
    }

    public function testFindPathToSiteAccessNeedsTheSiteAccessToBeAvailable()
    {
        $ini = eZINI::instance();
        $siteAccessList = $ini->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' );

        try
        {
            $ini->setVariable( 'SiteAccessSettings', 'AvailableSiteAccessList', array( 'eng' ) );
            $this->assertFalse( eZSiteAccess::findPathToSiteAccess( 'plain' ) );

            $ini->setVariable( 'SiteAccessSettings', 'AvailableSiteAccessList', array( 'plain' ) );
            $this->assertSame( 'settings/siteaccess/plain', eZSiteAccess::findPathToSiteAccess( 'plain' ) );
        }
        finally
        {
            $ini->setVariable( 'SiteAccessSettings', 'AvailableSiteAccessList', $siteAccessList );
        }
    }
}
