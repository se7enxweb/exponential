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
    /** The design/admin and design/admin4 copies of a template are the same */
    private function template( $path )
    {
        $this->assertFileEquals( self::root() . "/design/admin4/templates/$path", self::root() . "/design/admin/templates/$path",
            "design/admin and design/admin4 differ: $path" );
        $tpl = (string)file_get_contents( self::root() . "/design/admin4/templates/$path" );
        $this->assertDoesNotMatchRegularExpression( '/<style|style="/i', $tpl, "$path: no inline CSS" );
        return $tpl;
    }

    /** One definition of the export entries (popup_export_items.tpl), used by Advanced > Export and the node view's menu */
    public function testTheExportEntriesAreDefinedOnce()
    {
        $items = $this->template( 'popupmenu/popup_export_items.tpl' );
        $this->assertStringContainsString( "fetch( 'package', 'content_export_access' )", $items );
        $this->assertStringContainsString( "'ExportMenuTemplateArray'", $items );
        $this->assertStringContainsString( "'ImportExportMenuTemplateArray'", $items );
        $this->assertStringContainsString( 'package/create/(creator)/ezcontentobject/(node)/%nodeID%', $items );
        $this->assertStringNotContainsString( 'submitForm', $items, 'an entry opens a form, never submits one' );

        $advanced = $this->template( 'popupmenu/popup_sub_advance_menu.tpl' );
        $this->assertStringContainsString( "popupmenu/popup_export_items.tpl' menu_id='ExportMenu' item_prefix='menu-export' sub_menu_depth=3 with_import=false()", $advanced );
        $this->assertStringContainsString( 'id="menu-export" class="more"', $advanced, 'Export opens a submenu (aria-haspopup from the class more)' );
        $this->assertStringContainsString( "menuArray['ExportMenu'] = {ldelim} 'depth': 2 {rdelim};", $advanced );
        $this->assertStringNotContainsString( 'package/create/', $advanced, 'the entries are not repeated here' );

        $menu = $this->template( 'popupmenu/popup_import_export_menu.tpl' );
        $this->assertStringContainsString( "popupmenu/popup_export_items.tpl' menu_id='ImportExportMenu' item_prefix='node-export' sub_menu_depth=1 with_import=true()", $menu );
        $this->assertStringContainsString( "menuArray['ImportExportMenu'] = {ldelim} 'depth': 0", $menu );
        $this->assertStringContainsString( "popupmenu/popup_import_export_menu.tpl", $this->template( 'popupmenu/popup_menu.tpl' ) );
    }

    /** The node view's Import/Export button: a menu button after Upload and before Move, published objects only */
    public function testTheImportExportButton()
    {
        $button = $this->template( 'node/view/import_export_button.tpl' );
        $this->assertStringContainsString( 'eq( $node.object.status, 1 )', $button, 'published objects only (no drafts, no edit pages)' );
        $this->assertStringContainsString( "popupmenu/popup_export_items.tpl' menu_id='ImportExportMenu' item_prefix='node-export' sub_menu_depth=1 with_import=true()", $button,
            'shown only when the shared definition has an entry' );
        $this->assertStringContainsString( 'role="button" aria-haspopup="menu" aria-expanded="false"', $button );
        $this->assertStringContainsString( "ezpopmenu_showTopLevel( event, 'ImportExportMenu'", $button );
        $this->assertStringContainsString( "'%canImport%', {\$node_can_import}", $button );
        $this->assertStringContainsString( 'and( $node.object.content_class.is_container, $node.can_create )', $button );
        $this->assertStringContainsString( 'a4-bi-arrow-down-up', $button );
        foreach ( array( 'admin4' => 'node/view/a4_full_body.tpl', 'admin' => 'node/view/full.tpl' ) as $design => $view )
        {
            $text = (string)file_get_contents( self::root() . "/design/$design/templates/$view" );
            $upload = strpos( $text, "node/view/upload_button.tpl" );
            $here = strpos( $text, "node/view/import_export_button.tpl' node=\$node" );
            $move = strpos( $text, 'name="MoveNodeButton"' );
            $this->assertNotFalse( $here, "$design: the button is included" );
            $this->assertTrue( $upload < $here && $here < $move, "$design: after Upload, before Move" );
        }
    }

    public function testAnItemCanBeShownOnlyWhenAValueIsSet()
    {
        $js = self::js();
        $this->assertStringContainsString( "var shownIf = menuArray[menuID]['elements'][i]['shown_if'];", $js );
        $this->assertStringContainsString( "hrefElement.style.display = ( shownValue && String( shownValue ) !== '0' ) ? '' : 'none';", $js );
    }
}
