<?php
/**
 * TinyMCE 8's interface (toolbar, menus, dialogs, status bar) follows admin4's light and dark mode like the editor's
 * content: an editor made on a dark page gets the skin oxide-dark, a change of the mode swaps the loaded oxide and
 * oxide-dark stylesheets, a skin a site configured is kept, TinyMCE 3 keeps its skin. admin4.js's editor block runs
 * in node against a stand-in for the browser and TinyMCE 8 (tests/tests/design/js/editor_skin.js; skipped without
 * node). No database.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminEditorSkinTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminEditorSkinTest extends PHPUnit\Framework\TestCase
{
    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    /** @return array name => scenario */
    private function scenarios()
    {
        $node = trim( (string)shell_exec( 'command -v node 2>/dev/null' ) );
        if ( $node === '' )
            $this->markTestSkipped( 'node is not installed' );
        $out = (string)shell_exec( escapeshellarg( $node ) . ' ' . escapeshellarg( __DIR__ . '/js/editor_skin.js' ) . ' '
                                   . escapeshellarg( self::root() . '/design/admin4/javascript/admin4.js' ) );
        $s = array();
        foreach ( array_filter( explode( "\n", $out ) ) as $line )
        {
            $row = json_decode( $line, true );
            $this->assertIsArray( $row, 'the harness ran: ' . $out );
            $s[$row['name']] = $row;
        }
        $this->assertCount( 5, $s );
        return $s;
    }

    public function testAnEditorStartsWithTheSkinOfThePagesMode()
    {
        $s = $this->scenarios();
        $this->assertTrue( $s['dark page']['hooked'], 'the editor manager is watched without the edit form stylesheets as well' );
        $this->assertSame( array( 'oxide-dark' ), $s['dark page']['skins'] );
        $this->assertSame( array( 'oxide' ), $s['light page']['skins'] );
        $this->assertSame( array( 'myskin' ), $s['configured skin']['skins'], 'a skin the site configured stays' );
    }

    public function testAChangeOfTheModeSwapsTheLoadedSkin()
    {
        $s = $this->scenarios();
        $this->assertSame( array( '/t8/skins/ui/oxide/skin.min.css' ), $s['dark to light']['page'] );
        $this->assertSame( array( '/t8/skins/ui/oxide/content.min.css', '/t8/skins/content/default/content.min.css' ), $s['dark to light']['frame'] );
        $this->assertSame( array( '/t8/skins/ui/oxide-dark/skin.min.css' ), $s['light to dark']['page'] );
        $this->assertSame( array( '/t8/skins/ui/oxide-dark/content.min.css', '/t8/skins/content/default/content.min.css' ), $s['light to dark']['frame'] );
    }

    public function testTheDarkSkinShipsAndTinyMCE3IsLeftAlone()
    {
        $base = self::root() . '/extension/ezoe/design/standard/javascript/tinymce8/skins/ui/';
        foreach ( array( 'skin.min.css', 'content.min.css' ) as $file )
        {
            $this->assertFileExists( $base . 'oxide/' . $file );
            $this->assertFileExists( $base . 'oxide-dark/' . $file );
        }
        $js = (string)file_get_contents( self::root() . '/design/admin4/javascript/admin4.js' );
        $this->assertStringContainsString( "if (!v3 && mce.on) {", $js, 'the skin is set for TinyMCE 8 editors only' );
    }
}
