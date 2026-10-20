<?php
/**
 * admin4l after the admin4 review of 15.10.2026, checked on the files alone (no database):
 *
 *  AL-R1 - the page keys its cache blocks by the layout id AND the admin generation: the fetch operator already
 *          unwraps the function's 'result', so the key list is imploded, not read with ".result.0" (which does not
 *          exist and left the generation out); the raw GET SectionID in the cache keys is cast to a number
 *  AL-R2 - the blocks admin_toolbar_right and admin_toolbar_developer draw the toolbars of toolbar.ini as admin4 does
 *  AL-R3 - the tab menu block draws only <li> items: the page wraps the topmenu zone in #a4-navbar-menu > ul
 *  AL-R7 - the page parts popup menu, debug area and overlay are drawn once, from the footer zone: the zone
 *          template skips them in every other zone (else #overlay-mask, #ajaxuploader-loader and the debug marker
 *          appear twice); rendered with a rebuilt layout
 *  AL-R8 - the zone template includes only admin_* block definitions; rendered with a rebuilt layout
 *  AL-R9 - the page reads the safety switch through the fetch function admin_layouts_enabled
 *
 * Run: php vendor/bin/phpunit tests/tests/design/Admin4lReviewTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class Admin4lReviewTest extends PHPUnit\Framework\TestCase
{
    private static function read( $path )
    {
        $file = dirname( __DIR__, 3 ) . '/' . $path;
        self::assertFileExists( $file );
        return (string)file_get_contents( $file );
    }

    /** AL-R1 */
    public function testTheCacheKeyOfThePageIncludesTheAdminGeneration()
    {
        $page = self::read( 'design/admin4l/templates/pagelayout.tpl' );
        $this->assertStringNotContainsString( '.result.0', $page, 'the fetch operator already unwraps "result"' );
        $this->assertMatchesRegularExpression( "/'admin_layout_cache_key'[^\n]*\\)\\s*\\)\\|implode\\( '-' \\)/", $page, 'the whole key list goes into the layout id' );
        // what the template now does with the unwrapped list: [generation, siteaccess, module, view, userhash]
        $key = array( '7', 'site_admin', 'content', 'view', '1,2,' );
        $this->assertSame( '100001-7-site_admin-content-view-1,2,', '100001-' . implode( '-', $key ) );
    }

    /** AL-R1 */
    public function testTheGetSectionIdInTheCacheKeysIsANumber()
    {
        $page = self::read( 'design/admin4l/templates/pagelayout.tpl' );
        $this->assertStringContainsString( "ezhttp( 'SectionID', 'get' )|int", $page );
        $this->assertDoesNotMatchRegularExpression( "/ezhttp\\( 'SectionID', 'get' \\)\\s*\\)/", $page, 'no raw value left' );
    }

    /** AL-R2 */
    public function testTheToolbarBlocksDrawTheToolbarsOfAdmin4()
    {
        $this->assertStringContainsString( "{tool_bar name='admin_right' view='full'}", self::read( 'design/admin4l/templates/explayouts/block/admin_toolbar_right.tpl' ) );
        $this->assertStringContainsString( "{tool_bar name='admin_developer' view='full'}", self::read( 'design/admin4l/templates/explayouts/block/admin_toolbar_developer.tpl' ) );
        // admin4 draws the same two toolbars, in this order
        $admin4 = self::read( 'design/admin4/templates/pagelayout.tpl' );
        $right = strpos( $admin4, "{tool_bar name='admin_right' view='full'}" );
        $developer = strpos( $admin4, "{tool_bar name='admin_developer' view='full'}" );
        $this->assertNotFalse( $right );
        $this->assertNotFalse( $developer );
        $this->assertLessThan( $developer, $right );
    }

    /** AL-R3 */
    public function testTheTabMenuBlockDrawsOnlyListItems()
    {
        $tpl = self::read( 'design/admin4l/templates/explayouts/block/admin_tab_menu.tpl' );
        $body = preg_replace( '/\{\*.*?\*\}/s', '', $tpl );
        $this->assertStringNotContainsString( 'class="navbar-menu', $body, 'the page draws the menu panel' );
        $this->assertStringNotContainsString( 'class="navbar-bar', $body, 'the page draws the list' );
        // every top-level element of the block is an <li> (the nested ul of "More" lives inside one)
        $depth = 0;
        preg_match_all( '#<(/?)(li|ul|div)\b[^>]*>#', $body, $m, PREG_SET_ORDER );
        foreach ( $m as $tag )
        {
            if ( $tag[1] === '' )
            {
                if ( $depth === 0 )
                    $this->assertSame( 'li', $tag[2], 'top-level element ' . $tag[0] );
                $depth++;
            }
            else
                $depth--;
        }
        $this->assertSame( 0, $depth, 'balanced' );
        // the page has the wrapper the items sit in
        $page = self::read( 'design/admin4l/templates/pagelayout.tpl' );
        $this->assertMatchesRegularExpression( '#id="a4-navbar-menu" class="navbar-menu[^>]*>\s*<ul class="navbar-bar">\s*\{\$topmenu_html\}#', $page );
    }

    /**
     * Renders the real admin_zone.tpl with a rebuilt layout. The one change to the template: the block includes point
     * at stub templates in a temporary directory that print "[<identifier>]" (the real blocks need the design
     * resources). The conditions under test are the template's own.
     */
    private function renderZone( array $blocks, $zone, array $params = array() )
    {
        $root = dirname( __DIR__, 3 );
        $dir = $root . '/var/tmp/al_zone_' . uniqid( '', true );
        mkdir( $dir, 0700, true );
        $text = self::read( 'design/admin4l/templates/explayouts/admin_zone.tpl' );
        $this->assertStringContainsString( "'design:explayouts/block/'", $text );
        file_put_contents( "$dir/zone.tpl", str_replace( "'design:explayouts/block/'", var_export( "file:$dir/", true ), $text ) );
        $zoneBlocks = array();
        foreach ( $blocks as $identifier )
        {
            file_put_contents( "$dir/$identifier.tpl", "[$identifier]" );
            $zoneBlocks[] = array( 'parent_id' => 0, 'definition_identifier' => $identifier );
        }
        $layout = array( 'zones' => array( array( 'identifier' => $zone, 'blocks' => $zoneBlocks ) ) );
        $saved = array_key_exists( 'eZTemplateCompilerSettings', $GLOBALS ) ? $GLOBALS['eZTemplateCompilerSettings'] : null;
        try
        {
            $outputs = array();
            foreach ( array( false, true ) as $compiled )
            {
                $GLOBALS['eZTemplateCompilerSettings']['compile'] = $compiled;
                $GLOBALS['eZTemplateCompilerSettings']['compilation-directory'] = $dir;
                eZTemplate::resetInstance();
                $tpl = new eZTemplate();
                $tpl->autoload();
                $tpl->setVariable( 'layout', $layout );
                $tpl->setVariable( 'zone_identifier', $zone );
                foreach ( $params as $name => $value )
                    $tpl->setVariable( $name, $value );
                $outputs[] = preg_replace( '/\s+/', '', (string)$tpl->fetch( "$dir/zone.tpl" ) );
            }
            $this->assertSame( $outputs[0], $outputs[1], 'interpreted and compiled agree' );
            return $outputs[0];
        }
        finally
        {
            if ( $saved === null )
                unset( $GLOBALS['eZTemplateCompilerSettings'] );
            else
                $GLOBALS['eZTemplateCompilerSettings'] = $saved;
            eZTemplate::resetInstance();
            foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::CHILD_FIRST ) as $f )
                $f->isDir() ? rmdir( $f->getPathname() ) : unlink( $f->getPathname() );
            rmdir( $dir );
        }
    }

    /** AL-R7 */
    public function testThePagePartsAreDrawnOnlyFromTheFooterZone()
    {
        $parts = array( 'admin_popup_menu', 'admin_debug_area', 'admin_overlay' );
        $this->assertSame( '[admin_module_result]', $this->renderZone( array_merge( $parts, array( 'admin_module_result' ) ), 'main' ), 'main: the page parts stay out' );
        $this->assertSame( '', $this->renderZone( $parts, 'left' ), 'left' );
        $this->assertSame( '[admin_popup_menu][admin_debug_area][admin_overlay]', $this->renderZone( $parts, 'footer' ), 'footer draws them' );
        $this->assertSame( '[admin_overlay]', $this->renderZone( $parts, 'footer', array( 'only' => array( 'admin_overlay' ) ) ), 'footer with only' );
        $this->assertSame( '[admin_footer]', $this->renderZone( array_merge( $parts, array( 'admin_footer' ) ), 'footer', array( 'skip' => $parts ) ), 'footer with skip' );
        // the page asks for them from the footer zone only
        $page = self::read( 'design/admin4l/templates/pagelayout.tpl' );
        foreach ( $parts as $block )
        {
            $this->assertMatchesRegularExpression( "/zone_identifier='footer' only=array\\( '" . $block . "' \\)/", $page, $block );
            $this->assertDoesNotMatchRegularExpression( "/zone_identifier='(?!footer')[a-z_]+' only=array\\( '" . $block . "'/", $page, $block );
        }
    }

    /** AL-R8 */
    public function testTheZoneTemplateDrawsOnlyAdminBlocks()
    {
        $this->assertSame( '[admin_logo]', $this->renderZone( array( 'not_admin_block', 'admin_logo', 'adminx' ), 'header' ) );
        $this->assertSame( '', $this->renderZone( array( 'title' ), 'header' ) );
        foreach ( glob( dirname( __DIR__, 3 ) . '/design/admin4l/templates/explayouts/block/*.tpl' ) as $file )
            $this->assertStringStartsWith( 'admin_', basename( $file ) );
    }

    /** AL-R9 */
    public function testThePageReadsTheSafetySwitchThroughTheFetchFunction()
    {
        $page = self::read( 'design/admin4l/templates/pagelayout.tpl' );
        $this->assertStringContainsString( "{if fetch( 'explayouts', 'admin_layouts_enabled' )}", $page );
        $this->assertStringNotContainsString( "ezini( 'AdminLayoutSettings', 'Enabled'", $page );
    }
}
