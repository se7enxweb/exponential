<?php
/**
 * The log viewer's "Wrap lines" choice (doc/en/us/features/6.0/log-viewer.md, "Wrapping long lines"): a user preference,
 * admin_logviewer_wrap, on by default, the same for every log and view.
 *
 *  LV-100  a stored '1' or '0' wins; no stored choice takes the default, which is on
 *  LV-101  [TailSettings] WrapLines: enabled by default, disabled turns the default off
 *  LV-102  a permalink never carries the choice, so opening another log or view keeps it
 *  LV-103  the page draws the stored choice on first paint (checkbox and list class), in both designs, and the script
 *          stores a change through user/preferences/set_and_exit and leaves the address alone
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expLogWrapPreferenceTest extends PHPUnit\Framework\TestCase
{
    /** LV-100 */
    public function testStoredChoiceAndDefault()
    {
        $this->assertTrue( expLogViewer::wrapFrom( '1', false ) );
        $this->assertFalse( expLogViewer::wrapFrom( '0', true ) );
        $this->assertTrue( expLogViewer::wrapFrom( false, true ), 'no stored choice: the default' );
        $this->assertTrue( expLogViewer::wrapFrom( null, true ) );
        $this->assertTrue( expLogViewer::wrapFrom( '', true ) );
        $this->assertFalse( expLogViewer::wrapFrom( 'x', false ) );
        $this->assertSame( 'admin_logviewer_wrap', expLogViewer::WRAP_PREFERENCE );
    }

    /** LV-101 */
    public function testDefaultIsOn()
    {
        $this->assertTrue( expLogViewerConfig::fromArray( array(), sys_get_temp_dir() )->enabled( 'TailSettings', 'WrapLines' ) );
        $off = expLogViewerConfig::fromArray( array( 'TailSettings' => array( 'WrapLines' => 'disabled' ) ), sys_get_temp_dir() );
        $this->assertFalse( $off->enabled( 'TailSettings', 'WrapLines' ) );
        $ini = file_get_contents( dirname( __DIR__, 4 ) . '/settings/logviewer.ini' );
        $this->assertMatchesRegularExpression( '/^WrapLines=enabled$/m', $ini );
    }

    /** LV-102 */
    public function testPermalinkDoesNotCarryTheChoice()
    {
        $state = expLogPermalink::state( array( 'wrap' => '1', 'level' => 'error' ) );
        $this->assertStringNotContainsString( '(wrap)', expLogPermalink::build( 'debug', $state, array( 'wrap' ) ) );
        $this->assertSame( 'setup/logs/view/error', expLogPermalink::build( 'error', array( 'wrap' => false ) ) );
    }

    /** LV-103 */
    public function testFirstPaintAndScript()
    {
        $root = dirname( __DIR__, 4 );
        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            $tpl = file_get_contents( "$root/design/$design/templates/setup/logs.tpl" );
            $this->assertStringContainsString( 'id="log-wrap" disabled="disabled"{if $log_wrap} checked="checked"{/if}', $tpl, $design );
            $this->assertStringContainsString( '{if $log_wrap} is-wrapped{/if}', $tpl, $design );
            $this->assertStringContainsString( "data-pref-url={'user/preferences/set_and_exit'|ezurl}", $tpl, $design );
            $js = file_get_contents( "$root/design/$design/javascript/page/setup_logs.js" );
            $this->assertStringContainsString( "savePreference( 'admin_logviewer_wrap'", $js, $design );
            $this->assertStringContainsString( 'wrap = !!state.wrap;', $js, $design );
            $this->assertStringNotContainsString( "path += '/(wrap)/1'", $js, $design );
        }
        $view = file_get_contents( "$root/kernel/private/classes/views/setup/logs.php" );
        $this->assertStringContainsString( '$viewer->wrapLines()', $view );
    }
}
