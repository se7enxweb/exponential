<?php
/**
 * The scripts of the admin designs, beyond what the menu, tree and keyboard tests hold: no implicit globals and no
 * page-wide handler assignments in the shared menu and tree scripts, a tree node that survives a class the cached
 * parameters do not know and can be loaded again after an error, both tab switch links handled by the script, a
 * menubar script that leaves nothing in the global scope, and the small fixes of the relations search, the upload
 * dialog and the sub-items table. Where node is installed, every script of the admin designs passes node --check;
 * without it that test is skipped.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminScriptsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminScriptsTest extends PHPUnit\Framework\TestCase
{
    /** Designs whose copy of a script is kept identical to admin4's (AdminContextMenuTest holds the menu ones) */
    const COPIES = array( 'ezajaxrelations_jquery.js', 'expajaxuploader.js' );

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function read( $path )
    {
        $file = self::root() . '/' . $path;
        if ( !is_file( $file ) )
            throw new RuntimeException( "$path is missing" );
        return (string)file_get_contents( $file );
    }

    /** The source without its comments, so a comment cannot satisfy or fail an assertion on code */
    private static function code( $path )
    {
        return preg_replace( array( '#/\*.*?\*/#s', '#(?<![:\'"\\\\])//[^\n]*#' ), '', self::read( $path ) );
    }

    public function testThePopupMenuSetsNoGlobalAndNoPageHandler()
    {
        foreach ( array( 'admin4', 'admin' ) as $design )
        {
            $path = "design/$design/javascript/popupmenu/ezpopupmenu.js";
            $js = self::code( $path );
            $this->assertStringContainsString( 'var alignElement = document.getElementById( alignItem );', $js, $path );
            $this->assertStringContainsString( 'var parentElement = document.getElementById(', $js, $path );
            $this->assertDoesNotMatchRegularExpression( '/^\s*(alignElement|parentElement)\s*=/m', $js, $path );
            // the right-edge correction needs the parent menu
            $this->assertStringContainsString( 'if ( parentElement && ( screenData.ScrollX + screenData.Width ) < ( newX + menuElement.offsetWidth ) )', $js, $path );
            // a press outside the menus is heard beside the page's own handlers, not instead of them
            $this->assertDoesNotMatchRegularExpression( '/document\.onmouse(down|over)\s*=/', $js, $path );
            $this->assertStringContainsString( "document.addEventListener( 'mousedown',", $js, $path );
            $this->assertStringContainsString( 'menu.contains( e.target )', $js, $path );
        }
    }

    public function testTheTreeSurvivesAnUnknownClassAndRetriesAFailedLoad()
    {
        foreach ( array( 'admin4', 'admin' ) as $design )
        {
            $path = "design/$design/javascript/contentstructuremenu_dynamic.js";
            $js = self::code( $path );
            $this->assertStringContainsString( "var itemClass = params.classes[item.class_id] || { identifier: '', name: '' };", $js, $path );
            $this->assertStringNotContainsString( 'params.classes[item.class_id].', $js, $path );
            $this->assertStringContainsString( "if ( divElement.className == 'error' )", $js, $path );
            $this->assertDoesNotMatchRegularExpression( '/aElement\.onclick\s*=\s*function\s*\(\s*\)\s*\{\s*return false;/', $js, $path );
            $this->assertStringContainsString( "toggle.className !== 'openclose-busy' && toggle.onclick", $js, $path );
            $this->assertStringContainsString( 'var expireDate = new Date();', $js, $path );
        }
    }

    public function testBothTabSwitchLinksAreHandledByTheScript()
    {
        foreach ( array( 'admin4', 'admin' ) as $design )
        {
            $path = "design/$design/javascript/node_tabs.js";
            $js = self::code( $path );
            $this->assertStringContainsString( "$('a.show-hide-tabs').on( 'click', NodeTab.toggleClick );", $js, $path );
            $this->assertStringNotContainsString( "$('#maincontent-hide').on( 'click'", $js, $path );
            $this->assertStringContainsString( 'var expireDate = new Date();', $js, $path );
        }
    }

    public function testTheMenubarScriptLeavesNothingGlobal()
    {
        $path = 'design/admin4/javascript/ezadmin_menubar.js';
        $js = self::code( $path );
        $this->assertSame( 0, preg_match_all( '/^(function|const|let|var)\s/m', $js ), "$path: a declaration at the top level is a global" );
        $this->assertMatchesRegularExpression( '/^\(\(\$\) => \{$/m', $js );
        $this->assertStringEndsWith( "})(jQuery);\n", self::read( $path ) );
        // the header height is admin4.js's, the table wrapper was never called
        $this->assertStringNotContainsString( 'adjustHeaderHeight', $js );
        $this->assertStringNotContainsString( 'wrapTable', $js );
        $this->assertStringContainsString( "flex.style.setProperty('--header-height', px + 'px');", self::read( 'design/admin4/javascript/admin4.js' ) );
    }

    public function testAdminFourShipsNoClassicColumnGrip()
    {
        $this->assertFileDoesNotExist( self::root() . '/design/admin4/javascript/leftmenu_widthcontrol.js' );
        $this->assertFileExists( self::root() . '/design/admin/javascript/leftmenu_widthcontrol.js' );
        $this->assertDoesNotMatchRegularExpression( '/^BackendJavaScriptList\[\]=leftmenu_widthcontrol\.js/m', self::read( 'settings/design.ini' ) );
    }

    public function testTheRelationsSearchAndTheUploadDialog()
    {
        $relations = self::code( 'design/admin4/javascript/ezajaxrelations_jquery.js' );
        $this->assertStringNotContainsString( "'keypress'", $relations );
        $this->assertStringNotContainsString( 'e.which', $relations );
        $this->assertStringContainsString( "if ( e.key === 'Enter' && !e.isComposing )", $relations );
        $this->assertStringNotContainsString( 'alert( data.content.error_text )', $relations );
        $this->assertStringContainsString( 'var message = data && ( data.error_text || ( data.content && data.content.error_text ) );', $relations );

        $uploader = self::code( 'design/admin4/javascript/expajaxuploader.js' );
        $this->assertStringContainsString( "if (typeof json.html !== 'string') {", $uploader );
        $this->assertStringContainsString( 'try { html = decodeURIComponent(json.html); } catch (e) {', $uploader );
        $this->assertSame( 1, substr_count( $uploader, 'decodeURIComponent(' ) );
        $this->assertStringContainsString( "\$hiddenPlace.find('input[data-exp-uploader-field]').remove();", $uploader );

        foreach ( self::COPIES as $file )
            $this->assertFileEquals( self::root() . "/design/admin4/javascript/$file", self::root() . "/design/admin/javascript/$file",
                "design/admin/javascript/$file and design/admin4's copy differ: change both copies together" );
    }

    public function testTheSubItemsTable()
    {
        foreach ( array( 'admin4', 'admin' ) as $design )
        {
            $path = "design/$design/javascript/ezajaxsubitems_expdatatable.js";
            $js = self::code( $path );
            // one row each: an id would repeat
            $this->assertStringNotContainsString( 'id="thumbfield"', $js, $path );
            $this->assertStringContainsString( '<div class="thumbfield"></div>', $js, $path );
            // the id of the item to disable, as ezpopupmenu.js compares it with the element's id
            $this->assertStringContainsString( "var createhereMenu = (confObj.classesString != '') ? -1 : 'child-menu-create-here';", $js, $path );
            $this->assertStringContainsString( 'id="child-menu-create-here"', self::read( "design/$design/templates/popupmenu/popup_subitems_menu.tpl" ) );
        }
    }

    /** Every script of the admin designs parses: node --check, where node is installed */
    public function testEveryAdminScriptParses()
    {
        $node = trim( (string)shell_exec( 'command -v node 2>/dev/null' ) );
        if ( $node === '' )
            $this->markTestSkipped( 'node is not installed' );
        $failed = array();
        $count = 0;
        foreach ( array( 'admin4', 'admin4l', 'admin3', 'admin' ) as $design )
        {
            $dir = self::root() . "/design/$design/javascript";
            if ( !is_dir( $dir ) )
                continue;
            foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ) ) as $file )
            {
                $name = $file->getFilename();
                if ( substr( $name, -3 ) !== '.js' || substr( $name, -7 ) === '.min.js' )
                    continue;
                $count++;
                exec( escapeshellarg( $node ) . ' --check ' . escapeshellarg( $file->getPathname() ) . ' 2>&1', $output, $status );
                if ( $status !== 0 )
                    $failed[] = substr( $file->getPathname(), strlen( self::root() ) + 1 );
                $output = array();
            }
        }
        $this->assertGreaterThan( 10, $count );
        $this->assertSame( array(), $failed, 'node --check fails for these scripts' );
    }
}
