<?php
/**
 * File containing the AdminContextMenuNestedSubmenuTest class.
 *
 * The nested submenus of the admin context menus (Advanced > Export > Subtree of class, #481): where ezpopupmenu.js
 * places a submenu beside its parent inside the visible area (_horizontalSubPlace(), _subVerticalPlace(), run in
 * node), the 'onshow' hook of a submenu filled in when it opens, and the Export menu's templates (entry parameters,
 * policy, no inline CSS, the same in design/admin and design/admin4). No database needed.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminContextMenuNestedSubmenuTest extends PHPUnit\Framework\TestCase
{
    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function js()
    {
        return (string)file_get_contents( self::root() . '/design/admin4/javascript/popupmenu/ezpopupmenu.js' );
    }

    /** Runs the named functions of ezpopupmenu.js in node over the cases: name => [arguments, expected] */
    private function runInNode( array $functions, $call, array $cases )
    {
        $node = trim( (string)shell_exec( 'command -v node 2>/dev/null' ) );
        if ( $node === '' )
            $this->markTestSkipped( 'node is not installed' );
        $source = '';
        foreach ( $functions as $function )
        {
            $this->assertSame( 1, preg_match( '/function ' . $function . '\(.*?\n\}\n/s', self::js(), $m ), "ezpopupmenu.js has no $function()" );
            $source .= $m[0];
        }
        $script = $source . 'const cases = ' . json_encode( $cases ) . ';'
                . 'const out = {}; for (const k in cases) { out[k] = ' . $call . '; }'
                . 'process.stdout.write(JSON.stringify(out));';
        $file = tempnam( sys_get_temp_dir(), 'ezpop' ) . '.js';
        file_put_contents( $file, $script );
        $result = json_decode( (string)shell_exec( escapeshellarg( $node ) . ' ' . escapeshellarg( $file ) ), true );
        unlink( $file );
        $this->assertIsArray( $result );
        return $result;
    }

    /**
     * Beside the parent: right when it fits, else left, else the side with more room moved inside the area, at the
     * left edge when wider than the area (a phone).
     */
    public function testANestedSubmenuStaysInsideTheVisibleWidth()
    {
        // areaLeft, areaRight, parentLeft, parentRight, width, overlap => left
        $cases = array(
            'fits on the right'          => array( array( 0, 1440, 400, 620, 220, 4 ), 616 ),
            'flips to the left'          => array( array( 0, 960, 600, 820, 220, 4 ), 384 ),
            'phone: right has more room' => array( array( 0, 390, 20, 240, 220, 4 ), 170 ),
            'phone: left has more room'  => array( array( 0, 390, 150, 370, 220, 4 ), 0 ),
            'wider than the window'      => array( array( 0, 300, 50, 250, 340, 4 ), 0 ),
            'scrolled page'              => array( array( 100, 1060, 700, 920, 220, 4 ), 484 ),
        );
        $result = $this->runInNode( array( '_horizontalSubPlace' ), '_horizontalSubPlace(...cases[k][0])', $cases );
        foreach ( $cases as $name => $case )
        {
            $this->assertSame( $case[1], $result[$name], $name );
            list( $areaLeft, $areaRight, , , $width ) = $case[0];
            $this->assertGreaterThanOrEqual( $areaLeft, $result[$name], "$name: not left of the area" );
            if ( $width <= $areaRight - $areaLeft )
                $this->assertLessThanOrEqual( $areaRight, $result[$name] + $width, "$name: not right of the area" );
        }
    }

    /** Up from the bottom edge, never above the area's top; a height limit only when taller than the area */
    public function testANestedSubmenuStaysInsideTheVisibleHeight()
    {
        // areaTop, areaBottom, top, height => [top, maxHeight]
        $cases = array(
            'fits'                => array( array( 60, 900, 300, 200 ), array( 300, null ) ),
            'moved up'            => array( array( 60, 900, 800, 200 ), array( 700, null ) ),
            'under the header'    => array( array( 60, 900, 20, 200 ), array( 60, null ) ),
            'taller than the area' => array( array( 60, 500, 300, 600 ), array( 60, 440 ) ),
        );
        $result = $this->runInNode( array( '_subVerticalPlace' ), '(r => [r.top, r.maxHeight])(_subVerticalPlace(...cases[k][0]))', $cases );
        foreach ( $cases as $name => $case )
            $this->assertSame( $case[1], $result[$name], $name );
    }

    public function testASubmenuCanBeFilledInWhenItOpens()
    {
        $js = self::js();
        $this->assertStringContainsString( "var onShow = menuArray[menuID]['onshow'];", $js );
        $this->assertStringContainsString( '_decorate( menuID );', $js, 'new items get their roles' );
        $this->assertMatchesRegularExpression( '/onShow\( document\.getElementById\( menuID \), CurrentSubstituteValues, function\(\)/', $js );
    }

    /** The Export submenu: the package entries for the policy, the extension hooks, the keyboard pattern's markup */
    public function testTheExportMenuTemplate()
    {
        $tpl = (string)file_get_contents( self::root() . '/design/admin4/templates/popupmenu/popup_sub_advance_menu.tpl' );
        $this->assertFileEquals( self::root() . '/design/admin4/templates/popupmenu/popup_sub_advance_menu.tpl',
            self::root() . '/design/admin/templates/popupmenu/popup_sub_advance_menu.tpl', 'design/admin and design/admin4 differ' );
        $this->assertStringContainsString( "fetch( 'package', 'content_export_access' )", $tpl );
        $this->assertStringContainsString( "'ExportMenuTemplateArray'", $tpl );
        $this->assertStringContainsString( "'ExportSubMenuTemplateArray'", $tpl );
        $this->assertStringContainsString( 'id="menu-export" class="more"', $tpl, 'Export opens a submenu (aria-haspopup from the class more)' );
        $this->assertStringContainsString( "menuArray['ExportMenu'] = {ldelim} 'depth': 2 {rdelim};", $tpl );
        $this->assertStringContainsString( 'package/create/(creator)/ezcontentobject/(node)/%nodeID%', $tpl );
        $this->assertDoesNotMatchRegularExpression( '/<style|style="/i', $tpl, 'no inline CSS' );
        $this->assertDoesNotMatchRegularExpression( '/onclick="[^"]*submitForm/', substr( $tpl, strpos( $tpl, 'set-block variable=$export_package_items' ), strpos( $tpl, '{/set-block}' ) - strpos( $tpl, 'set-block variable=$export_package_items' ) ),
            'an entry opens a form, never submits one' );
    }
}
