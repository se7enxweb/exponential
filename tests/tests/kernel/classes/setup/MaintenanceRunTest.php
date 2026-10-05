<?php
/**
 * A setup run keeps a maintenance window that was already open: the kickstarter replaced var/maintenance.json with
 * its own and deleted it at the end, ending someone else's window. Files under var/tmp/maintenance-run-tests/ only.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class MaintenanceRunTest extends PHPUnit\Framework\TestCase
{
    private $root;

    protected function setUp(): void
    {
        $this->root = dirname( __DIR__, 5 ) . '/var/tmp/maintenance-run-tests/' . getmypid() . '-' . $this->name();
        if ( !is_dir( $this->root . '/var' ) )
            mkdir( $this->root . '/var', 0775, true );
        @unlink( $this->root . '/' . expMaintenance::MARKER );
    }

    protected function tearDown(): void
    {
        @unlink( $this->root . '/' . expMaintenance::MARKER );
    }

    public function testExistingMarkerIsKeptAndSurvivesTheRun()
    {
        $marker = $this->root . '/' . expMaintenance::MARKER;
        $original = json_encode( array( 'reason' => 'manual', 'message' => 'Planned window', 'since' => 1 ) ) . "\n";
        file_put_contents( $marker, $original );

        $this->assertSame( 'existing', expMaintenance::beginRun( $this->root, 'run-1', array( 'reason' => 'setup' ) ) );
        $this->assertSame( $original, file_get_contents( $marker ), 'the open window is not replaced' );

        $this->assertFalse( expMaintenance::disable( $this->root, 'run-1' ), 'the run does not end a window it did not open' );
        $this->assertSame( $original, file_get_contents( $marker ) );
    }

    public function testWithoutMarkerTheRunSwitchesItOnAndOff()
    {
        $marker = $this->root . '/' . expMaintenance::MARKER;
        $handlerBefore = set_exception_handler( null );
        restore_exception_handler();
        $this->assertSame( 'enabled', expMaintenance::beginRun( $this->root, 'run-2', array( 'reason' => 'setup' ) ) );
        // the settings layer installs eZExecution's exception handler once, on first use; not this test's to keep
        $handlerAfter = set_exception_handler( null );
        restore_exception_handler();
        if ( $handlerAfter !== $handlerBefore )
            restore_exception_handler();
        $state = json_decode( file_get_contents( $marker ), true );
        $this->assertSame( 'run-2', $state['run'] );
        $this->assertTrue( expMaintenance::disable( $this->root, 'run-2' ) );
        $this->assertFileDoesNotExist( $marker );
    }
}
