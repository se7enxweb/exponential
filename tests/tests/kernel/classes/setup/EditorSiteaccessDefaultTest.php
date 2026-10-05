<?php
/**
 * The editor siteaccess's default never shares the public or admin siteaccess's port, host or path: with
 * exp:install --access=port the editor took 8080, the public site's port, and --access=host gave it edit.localhost.
 * No database; exp:install is run with --print only (it writes nothing then).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class EditorSiteaccessDefaultTest extends PHPUnit\Framework\TestCase
{
    public function testPortIsSitePortPlusTwoAndNeverTaken()
    {
        $this->assertSame( 8082, eZStepSiteAccess::distinctEditorAccessValue( 'port', '8080', '8081' ) );
        $this->assertSame( 9002, eZStepSiteAccess::distinctEditorAccessValue( 'port', 9000, 9001 ) );
        $this->assertSame( 8083, eZStepSiteAccess::distinctEditorAccessValue( 'port', '8080', '8082' ) );
    }

    public function testHostIsEditDotThePublicHost()
    {
        $this->assertSame( 'edit.example.com', eZStepSiteAccess::distinctEditorAccessValue( 'hostname', 'www.example.com', 'admin.example.com' ) );
        $this->assertSame( 'edit.example.org', eZStepSiteAccess::distinctEditorAccessValue( 'hostname', 'example.org', 'admin.example.org' ) );
        $this->assertSame( 'editor.example.com', eZStepSiteAccess::distinctEditorAccessValue( 'hostname', 'www.example.com', 'edit.example.com' ) );
    }

    public function testUrlPathAvoidsTakenNames()
    {
        $this->assertSame( 'editor', eZStepSiteAccess::distinctEditorAccessValue( 'url', 'site', 'admin' ) );
        $this->assertSame( 'site_editor', eZStepSiteAccess::distinctEditorAccessValue( 'url', 'site', 'editor' ) );
    }

    public function testSiteTypeValueIsKeptWhenGiven()
    {
        $this->assertSame( 9999, eZStepSiteAccess::editorAccessValueFor( array( 'access_type' => 'port', 'access_type_value' => 8080,
                                                                                 'admin_access_type_value' => 8081, 'editor_access_type_value' => 9999 ) ) );
        $this->assertSame( 8082, eZStepSiteAccess::editorAccessValueFor( array( 'access_type' => 'port', 'access_type_value' => 8080,
                                                                                 'admin_access_type_value' => 8081 ) ) );
    }

    private function installPrint( array $args )
    {
        $root = dirname( __DIR__, 5 );
        $cmd = array_merge( array( PHP_BINARY, $root . '/bin/php/install.php' ), $args, array( '--print' ) );
        $proc = proc_open( $cmd, array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes, $root );
        $out = stream_get_contents( $pipes[1] );
        $err = stream_get_contents( $pipes[2] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        return array( proc_close( $proc ), $out, $err );
    }

    public function testExpInstallPortGivesTheEditorItsOwnPort()
    {
        list( $status, $out ) = $this->installPrint( array( '--access=port' ) );
        $this->assertSame( 0, $status );
        $this->assertStringContainsString( "\nAccessPort=8080\n", $out );
        $this->assertStringContainsString( "\nEditorAccessPort=8082\n", $out );
    }

    public function testExpInstallHostGivesTheEditorAHostOfTheSite()
    {
        list( $status, $out ) = $this->installPrint( array( '--host=www.example.com', '--admin-host=admin.example.com' ) );
        $this->assertSame( 0, $status );
        $this->assertStringContainsString( "\nEditorAccessHostname=edit.example.com\n", $out );
    }

    public function testExpInstallRefusesAnEditorPortThatIsTaken()
    {
        list( $status, , $err ) = $this->installPrint( array( '--access=port', '--editor-port=8080' ) );
        $this->assertSame( 1, $status );
        $this->assertStringContainsString( 'must differ', $err );
    }
}
