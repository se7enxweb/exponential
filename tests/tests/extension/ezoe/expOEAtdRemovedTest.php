<?php
/**
 * The After the Deadline spell checker is removed from the online editor: the view ezoe/atd_rpc sent the editor's
 * text over plain HTTP to service.afterthedeadline.com (a service that no longer answers) and appended the debug
 * report to the answer.
 *
 *  AR-01 - the ezoe module has no view atd_rpc, and neither its entry point nor its class is there
 *  AR-02 - the TinyMCE 3 plugin AtD and its settings (Plugins[], Buttons[], [AtD]) and the editor's atd_ options
 *          are gone
 *
 * No database.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/ezoe/expOEAtdRemovedTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expOEAtdRemovedTest extends PHPUnit\Framework\TestCase
{
    const EZOE = 'extension/ezoe';

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
    }

    /** AR-01 */
    public function testTheModuleHasNoAtdView()
    {
        $ViewList = array();
        include self::EZOE . '/modules/ezoe/module.php';
        $this->assertArrayHasKey( 'spellcheck_rpc', $ViewList, 'the module file was read' );
        $this->assertArrayNotHasKey( 'atd_rpc', $ViewList );
        $this->assertFileDoesNotExist( self::EZOE . '/modules/ezoe/atd_rpc.php' );
        $this->assertFileDoesNotExist( self::EZOE . '/classes/runnable/views/ezoe/atd_rpc.php' );
    }

    /** AR-02 */
    public function testThePluginAndItsSettingsAreGone()
    {
        $this->assertDirectoryDoesNotExist( self::EZOE . '/design/standard/javascript/plugins/AtD' );
        $ini = file_get_contents( self::EZOE . '/settings/ezoe.ini' );
        $this->assertStringNotContainsString( '[AtD]', $ini );
        $this->assertStringNotContainsString( 'Plugins[]=AtD', $ini );
        $this->assertStringNotContainsString( 'Buttons[]=AtD', $ini );
        $this->assertStringNotContainsString( 'afterthedeadline', $ini );
        $template = file_get_contents( self::EZOE . '/design/standard/templates/content/datatype/edit/ezxmltext_ezoe.tpl' );
        $this->assertStringNotContainsString( 'atd_', $template );
        $this->assertStringContainsString( 'spellchecker_rpc_url', $template, 'the TinyMCE spell checker stays' );
    }
}
