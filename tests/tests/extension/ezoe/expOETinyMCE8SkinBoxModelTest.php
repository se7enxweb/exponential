<?php
/**
 * The o2k7 look of the TinyMCE 8 editor (tinymce8_ez/skins/o2k7/skin.css) sets the box model of its toolbar
 * buttons and icons itself, so the page around the editor cannot empty the toolbar.
 *
 * The editor is rendered inside the design's content edit form. A design rule for that form's buttons that is more
 * specific than the oxide skin's ".tox .tox-tbtn" (the media design's ".content-edit-attribute button", display
 * inline-block and padding) made the buttons inline-block; the .tox-icon span inside then stayed inline, its 20x20
 * did not apply and the icon sprite was drawn into a 0px wide box: every button was blank on the front sites.
 *
 *  SB-01 - the button rule makes the buttons flex boxes without padding
 *  SB-02 - the icon and the chevron of a button are blocks, so their width and height apply
 *  SB-03 - every icon sprite rule sizes the icon at 20x20
 *
 * Reads the stylesheet as a file. No database.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/ezoe/expOETinyMCE8SkinBoxModelTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expOETinyMCE8SkinBoxModelTest extends PHPUnit\Framework\TestCase
{
    const SKIN = 'extension/ezoe/design/standard/javascript/tinymce8_ez/skins/o2k7/skin.css';

    /**
     * Declarations of the rule whose selector list is exactly $selectors (whitespace normalised), comments removed.
     *
     * @return array<string,string> property => value
     */
    private function rule( array $selectors )
    {
        $css = preg_replace( '!/\*.*?\*/!s', '', file_get_contents( dirname( __DIR__, 4 ) . '/' . self::SKIN ) );
        $want = implode( ',', $selectors );
        preg_match_all( '/([^{}]+)\{([^{}]*)\}/', $css, $m, PREG_SET_ORDER );
        foreach ( $m as $r )
        {
            $sel = implode( ',', array_map( function ( $s ) { return preg_replace( '/\s+/', ' ', trim( $s ) ); }, explode( ',', $r[1] ) ) );
            if ( $sel !== $want )
                continue;
            $decl = array();
            foreach ( explode( ';', $r[2] ) as $d )
            {
                $p = explode( ':', $d, 2 );
                if ( count( $p ) === 2 )
                    $decl[strtolower( trim( $p[0] ) )] = trim( $p[1] );
            }
            return $decl;
        }
        $this->fail( 'No rule for ' . $want . ' in ' . self::SKIN );
    }

    public function testButtonsAreFlexBoxesWithoutPadding()
    {
        $d = $this->rule( array( '.tox.ezoe-skin-o2k7 .tox-tbtn', '.tox.ezoe-skin-o2k7 .tox-split-button' ) );
        $this->assertSame( 'flex', $d['display'] ?? null, 'SB-01 display' );
        $this->assertSame( '0', $d['padding'] ?? null, 'SB-01 padding' );
    }

    public function testIconAndChevronAreBlocks()
    {
        $d = $this->rule( array( '.tox.ezoe-skin-o2k7 .tox-tbtn .tox-icon', '.tox.ezoe-skin-o2k7 .tox-tbtn .tox-tbtn__select-chevron' ) );
        $this->assertSame( 'block', $d['display'] ?? null, 'SB-02 display' );
    }

    public function testSpriteIconsAreSized()
    {
        $css = file_get_contents( dirname( __DIR__, 4 ) . '/' . self::SKIN );
        $this->assertSame( 1, preg_match( '/\[data-mce-name="ezdisable"\] \.tox-icon\s*\{([^}]*)\}/', $css, $m ), 'SB-03 sprite rule' );
        $this->assertStringContainsString( 'width: 20px', $m[1], 'SB-03 width' );
        $this->assertStringContainsString( 'height: 20px', $m[1], 'SB-03 height' );
        $this->assertStringContainsString( 'icons.png', $m[1], 'SB-03 sprite' );
    }
}
