<?php
/**
 * expViewCountConfiguration reads logfile.ini and site.ini: the single-site mode of the cronjob (the siteaccess of
 * the run, with the old [AccessLogFileSettings] unchanged), a list of siteaccesses, the PathPrefix of site.ini when
 * logfile.ini names none, the state directory, and siteaccesses that cannot be counted. Siteaccesses that share a
 * log are one job, matched per line; a siteaccess with a log of its own is not matched.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

#[\PHPUnit\Framework\Attributes\Group('kernel')]
class expViewCountConfigurationTest extends ezpTestCase
{
    private $access;

    public function setUp(): void
    {
        parent::setUp();
        $this->access = array_key_exists( 'eZCurrentAccess', $GLOBALS ) ? array( $GLOBALS['eZCurrentAccess'] ) : null;
        $GLOBALS['eZCurrentAccess'] = array( 'name' => 'site', 'type' => eZSiteAccess::TYPE_DEFAULT, 'uri_part' => array() );
        ezpINIHelper::setINISetting( 'site.ini', 'SiteAccessSettings', 'AvailableSiteAccessList', array( 'site', 'admin' ) );
        ezpINIHelper::setINISetting( 'site.ini', 'SiteAccessSettings', 'PathPrefix', 'Fit-Healthy' );
        ezpINIHelper::setINISetting( 'site.ini', 'SiteSettings', 'IndexPage', '/content/view/full/60' );
        ezpINIHelper::setINISetting( 'site.ini', 'DesignSettings', 'SiteDesign', 'standard' );
        ezpINIHelper::setINISetting( 'logfile.ini', 'AccessLogFileSettings', 'StorageDir', '/var/log/httpd' );
        ezpINIHelper::setINISetting( 'logfile.ini', 'AccessLogFileSettings', 'LogFileName', 'access_log' );
        ezpINIHelper::setINISetting( 'logfile.ini', 'AccessLogFileSettings', 'SitePrefix', array( '', 'mysite' ) );
        ezpINIHelper::setINISetting( 'logfile.ini', 'AccessLogFileSettings', 'PathPrefix', array( '' ) );
        ezpINIHelper::setINISetting( 'logfile.ini', 'ViewCountSettings', 'StateDir', 'var/viewcount-test' );
    }

    public function tearDown(): void
    {
        ezpINIHelper::restoreINISettings();
        if ( $this->access === null )
            unset( $GLOBALS['eZCurrentAccess'] );
        else
            $GLOBALS['eZCurrentAccess'] = $this->access[0];
        parent::tearDown();
    }

    public function testTheSingleSiteModeReadsTheOldSettings()
    {
        $config = expViewCountConfiguration::fromINI();
        $this->assertSame( 'single', $config->source()['mode'] );
        $jobs = $config->jobs();
        $this->assertCount( 1, $jobs );
        $this->assertFalse( $jobs[0]['match'] );
        $site = $jobs[0]['sites']['site'];
        $this->assertSame( '/var/log/httpd/access_log', $site->logFile );
        $this->assertSame( array( 'mysite' ), $site->sitePrefixes );
        $this->assertSame( array( 'Fit-Healthy' ), $site->pathPrefixes, 'the PathPrefix of site.ini, as logfile.ini names none' );
        $this->assertSame( '/content/view/full/60', $site->indexPage );
        $this->assertSame( eZSys::rootDir() . '/var/viewcount-test', $config->settings()['state_dir'] );
        $this->assertSame( 'auto', $config->settings()['match'] );
        $this->assertSame( array( 'GET' ), $config->settings()['filter']['methods'] );
    }

    public function testAListNamesTheSiteaccesses()
    {
        ezpINIHelper::setINISetting( 'logfile.ini', 'AccessLogFileSettings', 'PathPrefix', array( 'Other' ) );
        $config = expViewCountConfiguration::fromINI( array( 'site', 'nosuch' ) );
        $this->assertSame( 'list', $config->source()['mode'] );
        $this->assertSame( array( 'nosuch' => 'not in AvailableSiteAccessList' ), $config->skipped() );
        $this->assertSame( array( 'Other' ), $config->jobs()[0]['sites']['site']->pathPrefixes );
    }

    public function testNoLogNoJob()
    {
        ezpINIHelper::setINISetting( 'logfile.ini', 'AccessLogFileSettings', 'StorageDir', '' );
        $config = expViewCountConfiguration::fromINI();
        $this->assertSame( array(), $config->jobs() );
        $this->assertCount( 1, $config->skipped() );
    }

    public function testSiteaccessesSharingALogAreOneMatchedJob()
    {
        $shared = new expViewCountSite( array( 'name' => 'site', 'logFile' => '/logs/access_log' ) );
        $bold = new expViewCountSite( array( 'name' => 'bold', 'logFile' => '/logs/access_log' ) );
        $own = new expViewCountSite( array( 'name' => 'korg', 'logFile' => '/logs/korg/access_log' ) );
        $config = new expViewCountConfiguration( array(), array( $shared, $bold, $own ) );
        $jobs = $config->jobs();
        $this->assertCount( 2, $jobs );
        $this->assertSame( array( 'site', 'bold' ), array_keys( $jobs[0]['sites'] ) );
        $this->assertTrue( $jobs[0]['match'] );
        $this->assertFalse( $jobs[1]['match'] );
        $never = new expViewCountConfiguration( array( 'match' => 'disabled' ), array( $shared, $bold ) );
        $this->assertFalse( $never->jobs()[0]['match'] );
        $always = new expViewCountConfiguration( array( 'match' => 'enabled' ), array( $own ) );
        $this->assertTrue( $always->jobs()[0]['match'] );
    }

    public function testAnUnknownPropertyIsRefused()
    {
        $this->expectException( InvalidArgumentException::class );
        new expViewCountSite( array( 'nosuch' => 1 ) );
    }
}
