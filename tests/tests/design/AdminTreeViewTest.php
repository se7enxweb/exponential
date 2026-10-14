<?php
/**
 * The content tree of the admin designs as a WAI-ARIA tree (contentstructuremenu_dynamic.js): the list is a tree,
 * each node's name link a tree item with level, position and aria-expanded, the sub items a group the item owns,
 * one tree item in the Tab order, and the keys of the tree view pattern. The behaviour itself is in the script;
 * this test holds the hooks it relies on and keeps the admin and admin4 copies equal.
 * doc/features/6.0/admin4-design.md, "Keyboard".
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminTreeViewTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminTreeViewTest extends PHPUnit\Framework\TestCase
{
    const SCRIPT = 'design/admin4/javascript/contentstructuremenu_dynamic.js';
    const TEMPLATE = 'design/admin4/templates/contentstructuremenu/content_structure_menu_dynamic.tpl';

    private static function file( $path )
    {
        return (string)file_get_contents( dirname( __DIR__, 3 ) . '/' . $path );
    }

    public function testTheTemplateMakesTheListATree()
    {
        $tpl = self::file( self::TEMPLATE );
        $this->assertStringContainsString( "treeMenu.tree( document.getElementById( 'content_tree_menu' ) );", $tpl );
        $this->assertStringContainsString( "\"tree\":\"{'Content tree'|i18n(\"design/admin/contentstructuremenu\")|wash(\"javascript\")}\"", $tpl );
        // the tree is set up before the first load, which decorates what it adds
        $this->assertLessThan( strpos( $tpl, 'treeMenu.load( false' ), strpos( $tpl, 'treeMenu.tree(' ) );
    }

    public function testTheTreeCarriesTheRolesAndStates()
    {
        $js = self::file( self::SCRIPT );
        foreach ( array( "setAttribute( 'role', 'tree' )", "setAttribute( 'role', 'treeitem' )", "setAttribute( 'role', 'group' )",
                         "li.setAttribute( 'role', 'none' )", "'aria-level'", "'aria-setsize'", "'aria-posinset'",
                         "'aria-owns'", "setAttribute( 'aria-current', 'page' )", "'aria-label', i18n.tree" ) as $needle )
            $this->assertStringContainsString( $needle, $js, $needle );
        // every load decorates the items it adds
        $this->assertStringContainsString( "thisThis.decorate( divElement );", $js );
        // the tree item follows the open/close state
        $this->assertMatchesRegularExpression( "#function _toggleState\\( a, state \\)\\s*\\{.*?_ownItem\\( a\\.parentNode \\).*?item\\.setAttribute\\( 'aria-expanded'#s", $js );
    }

    public function testOneTreeItemIsInTheTabOrder()
    {
        $js = self::file( self::SCRIPT );
        $this->assertStringContainsString( "item.setAttribute( 'tabindex', '-1' );", $js );
        $this->assertStringContainsString( "item.setAttribute( 'tabindex', '0' );", $js );
        // the open/close button and the node icon leave the Tab order and the accessibility tree
        $this->assertStringContainsString( "c.setAttribute( 'tabindex', '-1' );", $js );
        $this->assertStringContainsString( "c.setAttribute( 'aria-hidden', 'true' );", $js );
        // a focus that lands on them (a click, a menu giving it back) goes to the tree item
        $this->assertStringContainsString( "addEventListener( 'focusin'", $js );
    }

    public function testTheKeysOfTheTreeViewPattern()
    {
        $js = self::file( self::SCRIPT );
        foreach ( array( "'ArrowDown'", "'ArrowUp'", "'ArrowRight'", "'ArrowLeft'", "'Home'", "'End'" ) as $key )
            $this->assertStringContainsString( "case " . $key . ":", $js, $key );
        // jQuery 4 has no jQuery.trim
        $this->assertStringNotContainsString( 'jQuery.trim', $js );
    }

    /** The classic admin design shares the script and its settings template */
    public function testTheAdminAndAdmin4CopiesAreTheSame()
    {
        $this->assertSame( self::file( self::SCRIPT ), self::file( str_replace( 'design/admin4/', 'design/admin/', self::SCRIPT ) ) );
        $this->assertSame( self::file( self::TEMPLATE ), self::file( str_replace( 'design/admin4/', 'design/admin/', self::TEMPLATE ) ) );
    }

    public function testTheTreeLabelIsTranslated()
    {
        foreach ( array( 'ger-DE', 'eng-US' ) as $locale )
        {
            $ts = self::file( 'share/translations/' . $locale . '/translation.ts' );
            $this->assertStringContainsString( '<source>Content tree</source>', $ts, $locale );
        }
    }
}
