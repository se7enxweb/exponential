<?php
/**
 * The script of ezmultiupload's quick location buttons (6.1.8, ezmultiupload/quick_locations.tpl), run in node on the
 * mini DOM (tests/tests/design/js/multiupload_quick_locations.js on mini_dom.js); no database. Skipped without node
 * (failed with CI=1) and for a release of the extension without the buttons.
 *
 *  QJ-01  the new page (expmultiupload.js): Media, the configured target, is the one pressed button; the buttons are
 *         <button type="button"> with an accessible text
 *  QJ-02  a click on Images: only Images pressed, Media within with its screen reader text, the hidden field
 *         exp-mu-location-id 51, the link "Media / Images", the change announced and remembered, the new target
 *         checked on the server; the current button again changes nothing
 *  QJ-03  a click on a site: Websites within, Media no longer, hidden field and link follow
 *  QJ-04  the classic page (ezmultiupload_upload.js on a stand-in for Exp.upload, Exp.util and Exp.ready, without
 *         jQuery on the page; a jQuery-like stand-in for the releases before 6.1.14): the upload address,
 *         the shown location, aria-pressed and within follow a click (also one on the button's inner span); a click
 *         during an upload changes nothing
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/ezmultiupload/expMultiuploadQuickLocationsScriptTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__, 2 ) . '/design/AdminNodeBinary.php';

class expMultiuploadQuickLocationsScriptTest extends PHPUnit\Framework\TestCase
{
    private static function root()
    {
        return dirname( __DIR__, 4 );
    }

    /** QJ-01 .. QJ-04 */
    public function testTheQuickLocationButtonsMoveTheTarget()
    {
        $script = self::root() . '/extension/ezmultiupload/design/standard/javascript/expmultiupload.js';
        if ( !is_file( $script ) )
        {
            $this->markTestSkipped( 'extension/ezmultiupload is not installed' );
        }
        if ( strpos( (string)file_get_contents( $script ), 'data-exp-mu-quick-location' ) === false )
        {
            $this->markTestSkipped( 'this release of ezmultiupload has no quick location buttons' );
        }
        $node = AdminNodeBinary::require();

        $output = array();
        $exit = null;
        exec( escapeshellarg( $node ) . ' ' . escapeshellarg( dirname( __DIR__, 2 ) . '/design/js/multiupload_quick_locations.js' ) . ' '
              . escapeshellarg( $script ) . ' '
              . escapeshellarg( dirname( $script ) . '/ezmultiupload_upload.js' ) . ' 2>&1', $output, $exit );
        $text = implode( "\n", $output );

        $this->assertSame( 0, $exit, 'the harness passed: ' . $text );
        $this->assertNotEmpty( preg_grep( '/^PASS keyboard: /', $output ), 'the keyboard checks ran: ' . $text );
        $this->assertNotEmpty( preg_grep( '/^PASS new page: after a site /', $output ), 'the new page checks ran: ' . $text );
        $this->assertSame( array(), array_values( preg_grep( '/^FAIL/', $output ) ), $text );
        $this->assertSame( 'PASS', (string)end( $output ), $text );
    }
}
