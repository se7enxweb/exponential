<?php
/**
 * admin4's left menu (parts/ini_menu.tpl, parts/ini_menu_items.tpl): a long menu can be arranged in groups from
 * menu.ini (Groups[], GroupNames[], GroupLinks_<group>[]); links in no group follow under "More"; the current page's
 * link carries aria-current="page"; a plain menu's list is named by its heading; the left column is the "Side menu"
 * navigation. The access checks per link are untouched.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminLeftMenuGroupsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminLeftMenuGroupsTest extends PHPUnit\Framework\TestCase
{
    private static function read( $path )
    {
        return (string)file_get_contents( dirname( __DIR__, 3 ) . '/' . $path );
    }

    /** @return array<string, string[]> the menu.ini values of one section, as eZINI reads arrays */
    private static function iniSection( $section )
    {
        $ini = self::read( 'settings/menu.ini' );
        $start = strpos( $ini, "\n[$section]\n" );
        if ( $start === false )
            return array();
        $body = substr( $ini, $start + strlen( $section ) + 3 );
        $next = strpos( $body, "\n[" );
        if ( $next !== false )
            $body = substr( $body, 0, $next );
        $values = array();
        foreach ( explode( "\n", $body ) as $line )
        {
            if ( preg_match( '/^(\w+)\[([^\]]*)\]=(.*)$/', trim( $line ), $m ) )
            {
                if ( $m[2] === '' )
                    $values[$m[1]][] = $m[3];
                else
                    $values[$m[1]][$m[2]] = $m[3];
            }
        }
        return $values;
    }

    public function testSetupGroupsNameEveryGroupAndHoldEveryLinkOnce()
    {
        $setup = self::iniSection( 'Leftmenu_setup' );
        $this->assertNotEmpty( $setup['Groups'] ?? array(), 'Leftmenu_setup has no Groups[]' );
        $links = array_keys( $setup['Links'] ?? array() );
        $seen = array();
        foreach ( $setup['Groups'] as $group )
        {
            $this->assertArrayHasKey( $group, $setup['GroupNames'] ?? array(), "group {$group} has no GroupNames[{$group}]" );
            $this->assertNotEmpty( $setup["GroupLinks_$group"] ?? array(), "group {$group} has no GroupLinks_{$group}[]" );
            foreach ( $setup["GroupLinks_$group"] as $key )
            {
                $this->assertNotContains( $key, $seen, "link $key is in two groups" );
                $this->assertContains( $key, $links, "GroupLinks_{$group}[] names $key, which is no Links[] key of Leftmenu_setup" );
                $seen[] = $key;
            }
        }
        // every active link of the setup menu sits in a group; extensions' links go to "More" by themselves
        foreach ( $links as $key )
            $this->assertContains( $key, $seen, "Links[{$key}] of Leftmenu_setup is in no group" );
    }

    public function testMenuTemplateGroupsWithDetailsAndKeepsTheFlatList()
    {
        $tpl = self::read( 'design/admin4/templates/parts/ini_menu.tpl' );
        // an empty Groups[] (reset in an override) gives the plain list: the branch counts, it does not ask hasVariable
        $this->assertStringContainsString( '{if $group_list|count}', $tpl );
        // a link named in two groups shows in the first only
        $this->assertStringContainsString( '$grouped_keys|contains( $item.key )|not', $tpl );
        $this->assertStringContainsString( "{\$group.name|d18n( \$i18n_section )|wash}", $tpl );
        $this->assertStringContainsString( '<details class="a4-navgroup"', $tpl );
        $this->assertStringContainsString( '<summary class="a4-nav-title">', $tpl );
        $this->assertStringContainsString( "'more', 'name', 'More'", $tpl, 'links in no group need the "More" group' );
        $this->assertStringContainsString( 'data-a4-current', $tpl, 'the group of the current page must stay open' );
        // without Groups[] the menu is one list, named by its heading
        $this->assertMatchesRegularExpression( "/\{else\}\s*\{include uri='design:parts\/ini_menu_items.tpl'[^}]*labelledby=/", $tpl );
        // the access checks are those of before: per link policies and can_open
        $this->assertStringContainsString( "concat( 'PolicyList_', \$link_key )", $tpl );
        $this->assertStringContainsString( "fetch( 'user', 'can_open'", $tpl );
    }

    public function testCurrentLinkCarriesAriaCurrent()
    {
        $items = self::read( 'design/admin4/templates/parts/ini_menu_items.tpl' );
        $this->assertStringContainsString( 'aria-current="page"', $items );
        $this->assertStringContainsString( 'aria-labelledby=', $items );
        $css = self::read( 'design/admin4/stylesheets/admin4.css' );
        $this->assertStringContainsString( 'a[aria-current="page"]', $css );
        // the current link's bar is an inset shadow on the left: rounded left corners bend it into a "("
        $this->assertSame( 1, preg_match( '/#leftmenu ul\.leftmenu-items li > div > a\[aria-current="page"\] \{([^}]*)\}/', $css, $current ) );
        $this->assertStringContainsString( 'box-shadow: inset 3px 0 0 var(--a4-accent);', $current[1] );
        $this->assertStringContainsString( 'border-radius: 0 var(--a4-radius-s) var(--a4-radius-s) 0;', $current[1],
                                           'The current link\'s left corners must be square, or its bar is drawn curved' );
        $this->assertStringContainsString( 'summary.a4-nav-title', $css );
        $this->assertMatchesRegularExpression( '/@media \(forced-colors: active\) \{\s*#leftmenu details\.a4-navgroup/', $css );
        $this->assertMatchesRegularExpression( '/@media \(prefers-reduced-motion: reduce\) \{\s*#leftmenu details\.a4-navgroup > summary\.a4-nav-title::before \{ transition: none; \}/', $css );
    }

    public function testLeftColumnIsTheSideMenuNavigation()
    {
        $this->assertStringContainsString(
            '<div id="leftmenu" class="sidebar left" role="navigation" aria-label="{\'Side menu\'|i18n( \'design/admin/pagelayout\' )|wash}">',
            self::read( 'design/admin4/templates/page_leftmenu.tpl' )
        );
    }

    public function testGroupStateIsKeptWithoutHidingTheCurrentPage()
    {
        $js = self::read( 'design/admin4/javascript/admin4.js' );
        $this->assertStringContainsString( "details.a4-navgroup[data-a4-navgroup]", $js );
        $this->assertStringContainsString( "hasAttribute('data-a4-current')", $js, 'a stored "closed" must not close the group of the current page' );
        $this->assertStringContainsString( 'localStorage', $js );
    }
}
