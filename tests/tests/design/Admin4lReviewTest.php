<?php
/**
 * admin4l after the admin4 review of 15.10.2026, checked on the files alone (no database):
 *
 *  AL-R1 - the page keys its cache blocks by the layout id AND the admin generation: the fetch operator already
 *          unwraps the function's 'result', so the key list is imploded, not read with ".result.0" (which does not
 *          exist and left the generation out); the raw GET SectionID in the cache keys is cast to a number
 *  AL-R2 - the blocks admin_toolbar_right and admin_toolbar_developer draw the toolbars of toolbar.ini as admin4 does
 *  AL-R3 - the tab menu block draws only <li> items: the page wraps the topmenu zone in #a4-navbar-menu > ul
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
}
