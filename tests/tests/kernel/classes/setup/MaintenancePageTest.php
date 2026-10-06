<?php
/**
 * The Setup > Maintenance page's data (\Exponential\View\Kernel\Setup\Maintenance): the allow-list field, the
 * description of the state for the status banner, the live preview's template, and a change from the audit as
 * the page lists it. No database, no marker: nothing here switches maintenance on or off.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use Exponential\View\Kernel\Setup\Maintenance;

class MaintenancePageTest extends PHPUnit\Framework\TestCase
{
    public function testAddressesAreSplitAndWhatIsNoAddressIsReturnedApart()
    {
        list( $valid, $rejected ) = Maintenance::parseAddresses( "203.0.113.7, 2001:db8::1\n198.51.100.0/24;office  203.0.113.7" );
        $this->assertSame( array( '203.0.113.7', '2001:db8::1' ), $valid );
        $this->assertSame( array( '198.51.100.0/24', 'office' ), $rejected );
        $this->assertSame( array( array(), array() ), Maintenance::parseAddresses( "  ,\n " ) );
    }

    public function testOffDescribesNothing()
    {
        $info = Maintenance::describe( false, 1000, '203.0.113.7' );
        $this->assertFalse( $info['on'] );
        $this->assertSame( array(), $info['allow_ips'] );
    }

    public function testManualWindowWithTimeLeft()
    {
        $now = 1800000000;
        $info = Maintenance::describe( array( 'reason' => 'manual', 'since' => $now - 600, 'until' => $now + 61,
                                              'message' => ' Back soon ', 'allow_ips' => array( '203.0.113.7' ),
                                              'allow_paths' => array( '/admin' ), 'by' => 'admin' ), $now, '203.0.113.7' );
        $this->assertTrue( $info['on'] );
        $this->assertSame( 'manual', $info['reason'] );
        $this->assertSame( 10, $info['running_minutes'] );
        $this->assertSame( 2, $info['remaining_minutes'], 'a part of a minute left counts as a minute' );
        $this->assertFalse( $info['overdue'] );
        $this->assertSame( 'Back soon', $info['message'] );
        $this->assertTrue( $info['custom_message'] );
        $this->assertTrue( $info['admin_open'] );
        $this->assertTrue( $info['client_allowed'] );
        $this->assertSame( expMaintenance::DEFAULT_PAGE, $info['page'] );
    }

    public function testExpectedEndPassedIsOverdueBecauseMaintenanceNeverEndsByItself()
    {
        $now = 1800000000;
        $info = Maintenance::describe( array( 'reason' => 'manual', 'since' => $now - 7200, 'until' => $now - 1500,
                                              'allow_paths' => array( 'admin/' ) ), $now, '198.51.100.1' );
        $this->assertTrue( $info['overdue'] );
        $this->assertSame( 25, $info['overdue_minutes'] );
        $this->assertSame( 0, $info['remaining_minutes'] );
        $this->assertTrue( $info['admin_open'], 'the path is compared as the front controller compares it' );
        $this->assertFalse( $info['client_allowed'] );
    }

    public function testInstallationsAndUnreadableMarkersAreToldApart()
    {
        $this->assertSame( 'install', Maintenance::describe( array( 'reason' => 'setup', 'run' => 'r1' ), 1 )['reason'] );
        $this->assertSame( 'setup', Maintenance::describe( array( 'reason' => 'setup', 'run' => 'r1', 'lease' => 99 ), 1 )['reason'] );
        $this->assertSame( 'unknown', Maintenance::describe( array( 'reason' => 'unknown' ), 1 )['reason'] );
        $this->assertSame( 'unknown', Maintenance::describe( array( 'reason' => '<b>' ), 1 )['reason'] );
        $info = Maintenance::describe( array( 'reason' => 'unknown' ), 1 );
        $this->assertFalse( $info['admin_open'] );
        $this->assertSame( 0, $info['since'] );
    }

    public function testThePreviewTemplateHasBothSlotsAndThePageFallsBackToTheDefaultTexts()
    {
        $root = dirname( __DIR__, 5 );
        $preview = Maintenance::previewTemplate( $root, array( 'reason' => 'manual', 'page' => '' ) );
        $this->assertSame( expMaintenance::DEFAULT_PAGE, $preview['file'] );
        $this->assertStringContainsString( Maintenance::MESSAGE_SLOT, $preview['template'] );
        $this->assertStringContainsString( Maintenance::UNTIL_SLOT, $preview['template'] );
        // the view's copies of the page's default texts must stay what expMaintenance::page() writes
        $this->assertStringContainsString( htmlspecialchars( Maintenance::DEFAULT_MESSAGE ), $preview['html'] );
        $this->assertStringContainsString( htmlspecialchars( Maintenance::DEFAULT_UNTIL ), $preview['html'] );
        $this->assertStringNotContainsString( Maintenance::MESSAGE_SLOT, $preview['html'] );
        $this->assertStringNotContainsString( 'onclick', $preview['html'], 'the frame runs no scripts, so none are handed to it' );
        $this->assertSame( '<p>a</p><button type="button">b</button>',
                           Maintenance::withoutScripts( '<p>a</p><script>x()</script><button type="button" onclick="location.reload()">b</button>' ) );
    }

    public function testThePreviewEscapesTheMessage()
    {
        $root = dirname( __DIR__, 5 );
        $preview = Maintenance::previewTemplate( $root, array( 'reason' => 'manual', 'message' => '<script>x</script>' ) );
        $this->assertStringNotContainsString( '<script>x', $preview['html'] );
        $this->assertStringContainsString( '&lt;script&gt;x', $preview['html'] );
    }

    public function testDurationsInWords()
    {
        $this->assertSame( '', Maintenance::durationText( 0 ) );
        $this->assertSame( '45 min', Maintenance::durationText( 45 ) );
        $this->assertSame( '2 h', Maintenance::durationText( 120 ) );
        $this->assertSame( '1 h 30 min', Maintenance::durationText( 90 ) );
        $this->assertSame( '1 day', Maintenance::durationText( 1440 ) );
        $this->assertSame( '3 days 4 h', Maintenance::durationText( 3 * 1440 + 4 * 60 + 59 ) );
        $presets = Maintenance::presets();
        $this->assertSame( array( 15, '15 min' ), array( $presets[0]['minutes'], $presets[0]['label'] ) );
        $this->assertSame( array( 'on', 'off', 'status' ), array_column( Maintenance::commands(), 'key' ) );
        $this->assertStringContainsString( '--allow-admin', Maintenance::commands()[0]['command'] );
    }

    public function testAuditRowsBecomeOnOffAndChanged()
    {
        $row = function ( array $before, array $after, array $actor = array( 'login' => 'admin' ) ) {
            return array( 'id' => '01M46RDFPMMMGK173V5857K6GN', 'time_ms' => 1800000000123, 'login' => null,
                          'record' => json_encode( array( 'actor' => $actor, 'before' => $before, 'after' => $after ) ) );
        };
        $on = Maintenance::historyRow( $row( array( 'mode' => 'off' ), array( 'mode' => 'on', 'reason' => 'manual', 'until' => 1800003600 ) ) );
        $this->assertSame( 'on', $on['mode'] );
        $this->assertSame( 'manual', $on['reason'] );
        $this->assertSame( 1800003600, $on['until'] );
        $this->assertSame( 1800000000, $on['time'] );
        $this->assertSame( 'admin', $on['login'] );
        $this->assertFalse( $on['cli'] );

        $off = Maintenance::historyRow( $row( array( 'mode' => 'on', 'reason' => 'setup' ), array( 'mode' => 'off' ),
                                              array( 'login' => 'admin', 'cli' => array( 'command' => 'x' ) ) ) );
        $this->assertSame( 'off', $off['mode'] );
        $this->assertSame( 'setup', $off['reason'] );
        $this->assertTrue( $off['cli'] );

        $changed = Maintenance::historyRow( $row( array( 'mode' => 'on' ), array( 'mode' => 'on', 'reason' => 'manual' ) ) );
        $this->assertSame( 'changed', $changed['mode'] );

        $this->assertNull( Maintenance::historyRow( array( 'record' => 'not json' ) ) );
    }
}
