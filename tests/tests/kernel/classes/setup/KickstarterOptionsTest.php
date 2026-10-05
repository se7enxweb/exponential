<?php
/**
 * The options of "exp:kickstarter run": exp:install passes --allow-root-user on to the kickstarter, and the standard
 * options every eZScript takes must not stop a run with "invalid option". No database, no settings.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class KickstarterOptionsTest extends PHPUnit\Framework\TestCase
{
    public function testAllowRootUserIsAccepted()
    {
        $options = expKickstarter::parseRunOptions( array( '--force', '--allow-root-user' ) );
        $this->assertIsArray( $options, '--allow-root-user, as exp:install passes it on, is accepted' );
        $this->assertTrue( (bool)$options['force'] );
    }

    public function testStandardScriptOptionsAreAccepted()
    {
        $options = expKickstarter::parseRunOptions( array( '--dry-run', '--no-colors', '-q', '--siteaccess=plain', '--logfiles', '-v', '--start-step=Welcome' ) );
        $this->assertIsArray( $options );
        $this->assertTrue( (bool)$options['dry-run'] );
        $this->assertSame( 'Welcome', $options['start-step'] );
    }

    public function testUnknownOptionIsStillRefused()
    {
        ob_start();
        $options = expKickstarter::parseRunOptions( array( '--no-such-option' ) );
        ob_end_clean();
        $this->assertFalse( $options );
    }
}
