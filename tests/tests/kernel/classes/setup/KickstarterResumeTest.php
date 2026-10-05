<?php
/**
 * --start-step at a later step cannot resume an earlier run: the steps keep the database type, the chosen package
 * and the system check results in the process only. Such a start is refused, naming the earliest step that works.
 * No database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class KickstarterResumeTest extends PHPUnit\Framework\TestCase
{
    private static function steps()
    {
        $classes = array();
        foreach ( ( new eZStepData() )->StepTable as $step )
            $classes[] = $step['class'];
        return $classes;
    }

    public function testFullRunIsFine()
    {
        $this->assertNull( expKickstarter::resumeProblem( self::steps(), 'welcome', 'final' ) );
    }

    public function testDryRunRangeIsFine()
    {
        $this->assertNull( expKickstarter::resumeProblem( self::steps(), expKickstarter::DRY_RUN_START, expKickstarter::DRY_RUN_STOP ) );
    }

    public function testStartAtSiteDetailsIsRefusedNamingWelcome()
    {
        $problem = expKickstarter::resumeProblem( self::steps(), 'SiteDetails', 'final' );
        $this->assertNotNull( $problem );
        $this->assertStringContainsString( '--start-step=SiteDetails cannot work', $problem );
        $this->assertStringContainsString( '--start-step=Welcome', $problem );
    }

    public function testStartAtDatabaseInitWithoutChoiceIsRefused()
    {
        $problem = expKickstarter::resumeProblem( self::steps(), 'DatabaseInit', 'DatabaseInit' );
        $this->assertStringContainsString( '--start-step=DatabaseChoice', (string)$problem );
    }

    public function testStartAtCreateSitesIsRefused()
    {
        $this->assertNotNull( expKickstarter::resumeProblem( self::steps(), 'CreateSites', 'Final' ) );
    }

    public function testStepsThatNeedNothingEarlierCanStartAlone()
    {
        $this->assertNull( expKickstarter::resumeProblem( self::steps(), 'EmailSettings', 'EmailSettings' ) );
        $this->assertNull( expKickstarter::resumeProblem( self::steps(), 'DatabaseChoice', 'DatabaseInit' ) );
    }
}
