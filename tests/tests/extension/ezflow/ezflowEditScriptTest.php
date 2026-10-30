<?php
/**
 * ezflow's edit script (design/standard/javascript/ezflow_edit.js), in node against the small stand-in for the DOM
 * (tests/tests/design/js/mini_dom.js) and on the templates that use it; no database, no installed site.
 *
 * - EZF-01: the block remove button (data-ezflow-confirm) asks first and sets the form's DiscardConfirm to 0, as its
 *   onclick="return confirmDiscard( ... )" did until 6.1.8; "Cancel" stops the click. The "Discard draft" button
 *   (data-ezflow-discard) does the same. The script sets itself up once, however often it is printed.
 * - EZF-02: the templates name the attributes the script reads: the block remove button data-ezflow-confirm, the
 *   admin frontpage edit view's "Discard draft" button data-ezflow-discard next to a DiscardConfirm field, and both
 *   views load ezflow_edit.js.
 *
 * Checks ezflow from 6.1.10 on (6.1.9 moved the handlers into the script and lost the DiscardConfirm part); skipped
 * when extension/ezflow is not in the checkout or older.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/ezflow/ezflowEditScriptTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class ezflowEditScriptTest extends PHPUnit\Framework\TestCase
{
    const SINCE = '6.1.10';

    private static function dir()
    {
        return dirname( __DIR__, 4 ) . '/extension/ezflow';
    }

    protected function setUp(): void
    {
        $xml = self::dir() . '/extension.xml';
        if ( !is_file( $xml ) )
            $this->markTestSkipped( 'extension/ezflow is not in this checkout' );
        $version = preg_match( '#<version>\s*([0-9.]+)\s*</version>#', (string)file_get_contents( $xml ), $m ) ? $m[1] : '0';
        if ( version_compare( $version, self::SINCE, '<' ) )
            $this->markTestSkipped( "extension/ezflow is $version, the checks start at " . self::SINCE );
    }

    private function read( $path )
    {
        $file = self::dir() . '/' . $path;
        $this->assertFileExists( $file );
        return (string)file_get_contents( $file );
    }

    /** EZF-01 */
    public function testBothConfirmButtonsSetDiscardConfirmToZero()
    {
        $node = trim( (string)shell_exec( 'command -v node 2>/dev/null' ) );
        if ( $node === '' )
        {
            if ( getenv( 'CI' ) )
                $this->fail( 'node is not installed' );
            $this->markTestSkipped( 'node is not installed' );
        }
        $script = self::dir() . '/design/standard/javascript/ezflow_edit.js';
        $this->assertFileExists( $script );
        $out = (string)shell_exec( escapeshellarg( $node ) . ' ' . escapeshellarg( __DIR__ . '/js/ezflow_edit.js' ) . ' '
                                   . escapeshellarg( $script ) . ' 2>&1' );
        $c = array();
        foreach ( array_filter( explode( "\n", $out ) ) as $line )
        {
            $row = json_decode( $line, true );
            $this->assertIsArray( $row, "the harness ran: $out" );
            $c[$row['name']] = $row;
        }
        $this->assertCount( 6, $c, $out );

        $question = array( 'Are you sure you want to remove this block?' );
        $this->assertSame( $question, $c['remove confirmed']['asked'] );
        $this->assertFalse( $c['remove confirmed']['prevented'], 'a confirmed remove goes on' );
        $this->assertSame( '0', $c['remove confirmed']['discardConfirm'], 'the remove button sets DiscardConfirm to 0' );
        $this->assertSame( $question, $c['remove cancelled']['asked'] );
        $this->assertTrue( $c['remove cancelled']['prevented'], '"Cancel" stops the remove' );
        $this->assertSame( '0', $c['remove cancelled']['discardConfirm'], 'set before the question, as confirmDiscard() did' );

        $question = array( 'Are you sure you want to discard the draft?' );
        $this->assertSame( $question, $c['discard confirmed']['asked'] );
        $this->assertFalse( $c['discard confirmed']['prevented'] );
        $this->assertSame( '0', $c['discard confirmed']['discardConfirm'] );
        $this->assertSame( 1, $c['discard confirmed']['submitted'], 'the form is sent' );
        $this->assertTrue( $c['discard cancelled']['prevented'] );
        $this->assertSame( 0, $c['discard cancelled']['submitted'], 'nothing is sent after "Cancel"' );

        $this->assertSame( 1, $c['remove without field']['asked'], 'a form without DiscardConfirm still asks' );
        $this->assertFalse( $c['remove without field']['prevented'] );
        $this->assertSame( 1, $c['once']['asked'], 'printed twice, it asks once' );
    }

    /** EZF-02 */
    public function testTheTemplatesNameWhatTheScriptReads()
    {
        $block = $this->read( 'design/standard/templates/block/edit/edit.tpl' );
        $this->assertMatchesRegularExpression( '#<input id="block-remove-\{\$block_id\}"[^>]*\sdata-ezflow-confirm="#', $block );
        $this->assertStringNotContainsString( 'onclick=', $block );

        $admin = $this->read( 'design/admin/override/templates/edit/frontpage.tpl' );
        $this->assertMatchesRegularExpression( '#name="DiscardButton"[^>]*\sdata-ezflow-discard="#', $admin );
        $this->assertMatchesRegularExpression( '#<input type="hidden" name="DiscardConfirm" value="1" />#', $admin );
        $this->assertStringContainsString( "'ezflow_edit.js'", $admin );

        $this->assertStringContainsString( "'ezflow_edit.js'", $this->read( 'design/standard/templates/content/datatype/edit/ezpage.tpl' ) );
        $this->assertStringContainsString( "'ezflow_edit.js'", $this->read( 'design/ezflow/override/templates/edit/frontpage.tpl' ) );
    }
}
