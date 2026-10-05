<?php
/**
 * The setup's registration step sends nothing unless asked, never to the old upstream address, and the
 * kickstarter's dry run stops before the steps that write a password or send mail. No database, no mail.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class RegistrationAndDryRunTest extends PHPUnit\Framework\TestCase
{
    public function testSendDefaultsToFalse()
    {
        $this->assertFalse( eZStepRegistration::kickstartSend( array() ), 'a [registration] section without Send= sends nothing' );
        $this->assertFalse( eZStepRegistration::kickstartSend( array( 'Continue' => 'true', 'UserData' => array() ) ) );
        $this->assertFalse( eZStepRegistration::kickstartSend( array( 'Send' => 'false' ) ) );
        $this->assertFalse( eZStepRegistration::kickstartSend( false ) );
        $this->assertTrue( eZStepRegistration::kickstartSend( array( 'Send' => 'true' ) ) );
    }

    public function testTheOldUpstreamAddressIsNeverUsed()
    {
        $source = file_get_contents( dirname( __DIR__, 5 ) . '/kernel/setup/steps/ezstep_registration.php' );
        $this->assertStringNotContainsString( 'registerezsite@ez.no', $source );
        $this->assertDoesNotMatchRegularExpression( "/setReceiver\\(\\s*'[^']*@ez\\.no'/", $source );
    }

    public function testDryRunStopsBeforeSiteAdminAndRegistration()
    {
        $steps = array();
        foreach ( ( new eZStepData() )->StepTable as $index => $step )
            $steps[$step['class']] = $index;
        $this->assertArrayHasKey( expKickstarter::DRY_RUN_STOP, $steps );
        $this->assertLessThan( $steps['SiteAdmin'], $steps[expKickstarter::DRY_RUN_STOP], 'SiteAdmin may write var/log/initial-admin-password' );
        $this->assertLessThan( $steps['Registration'], $steps[expKickstarter::DRY_RUN_STOP], 'Registration may send mail' );
        $this->assertGreaterThanOrEqual( $steps['SiteTypes'], $steps[expKickstarter::DRY_RUN_STOP], 'the packages are still checked' );
    }
}
