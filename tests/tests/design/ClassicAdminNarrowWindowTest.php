<?php
/**
 * The classic administration design (design/admin) at narrow widths: the right column's tools keep inside the
 * column (no minimum width wider than it, the interface choices one full-width button a row), and below 760px the
 * page stacks its columns, wraps its header, scrolls wide tables inside their own box and makes its controls and
 * text fields 44px high. The page layouts carry the viewport meta so a phone lays the page out at its own width, and the left menu's
 * width control leaves the main column's margin alone while the columns are stacked.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/ClassicAdminNarrowWindowTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class ClassicAdminNarrowWindowTest extends PHPUnit\Framework\TestCase
{
    private static function read( $path )
    {
        $file = dirname( __DIR__, 3 ) . '/' . $path;
        self::assertFileExists( $file );
        return (string)file_get_contents( $file );
    }

    public function testRightColumnToolsKeepInsideTheColumn()
    {
        $css = self::read( 'design/admin/stylesheets/pagelayout.css' );
        $this->assertDoesNotMatchRegularExpression( '/#rightmenu-design > div\s*\{[^}]*min-width:\s*12em/', $css );
        $this->assertMatchesRegularExpression( '/#rightmenu-design > div\s*\{[^}]*min-width:\s*0;/', $css );
        $this->assertStringContainsString( '#uiswitch-tool .a4-btn { box-sizing: border-box; width: 100%; min-width: 0; white-space: normal;', $css );
        $this->assertStringContainsString( '#uiswitch-tool select { width: 100%; }', $css );
    }

    public function testNarrowWindowsStackTheColumns()
    {
        $css = self::read( 'design/admin/stylesheets/pagelayout.css' );
        $at = strpos( $css, '@media (max-width: 760px) {' );
        $this->assertNotFalse( $at, 'the narrow-window block' );
        $block = substr( $css, $at );
        foreach ( array( ':root { --a4-control-h: 44px; }', '#page #columns { display: flex; flex-direction: column; }',
                         '#page #columns #maincontent { order: 1; margin: 0 !important;', '#page #columns #leftmenu { order: 2; float: none;',
                         '#page #columns #rightmenu { order: 2; float: none;', '#header-design { display: flex; flex-wrap: wrap;',
                         '#header-topmenu ul { display: flex; flex-wrap: wrap;', 'overflow-x: auto; }' ) as $rule )
            $this->assertStringContainsString( $rule, $block, $rule );
        // the block sets no colour (tokens only, and nothing to keep in step with a theme)
        $this->assertDoesNotMatchRegularExpression( '/(^|[;{\s])(color|background(-color)?)\s*:/', $block );
    }

    public function testNarrowWindowsGiveTextFieldsTheControlHeight()
    {
        // the relation list's "Find objects" field was 21px high beside its button: a target under 24px at 375px
        $css = self::read( 'design/admin/stylesheets/pagelayout.css' );
        $block = substr( $css, (int)strpos( $css, '@media (max-width: 760px) {' ) );
        $this->assertMatchesRegularExpression( '/#maincontent :is\(input:not\(\[type\]\), input\[type=text\], input\[type=search\][^)]*\)[^{]*\{ min-height: var\(--a4-control-h\); \}/', $block );
        $template = self::read( 'design/standard/templates/content/datatype/edit/ezobjectrelationlist.tpl' );
        $this->assertStringContainsString( '<input type="text" class="halfbox hide ezobject-relation-search-text"', $template );
    }

    public function testPageLayoutsCarryTheViewportMeta()
    {
        foreach ( array( 'design/admin/templates/pagelayout.tpl', 'design/admin/templates/loginpagelayout.tpl' ) as $path )
            $this->assertStringContainsString( '<meta name="viewport" content="width=device-width, initial-scale=1" />', self::read( $path ), $path );
    }

    public function testWidthControlLeavesStackedColumnsAlone()
    {
        $js = self::read( 'design/admin/javascript/leftmenu_widthcontrol.js' );
        $this->assertStringContainsString( "getComputedStyle( \$leftmenu[0] ).cssFloat !== 'none'", $js );
    }
}
