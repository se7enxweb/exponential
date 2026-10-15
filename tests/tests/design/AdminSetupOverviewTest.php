<?php
/**
 * admin4's Setup overview (setup/menu, the Setup tab's first page): every function of the Setup menu as a tile
 * with a Bootstrap icon, a name and one line of description, in the groups of the left menu. The links, groups and
 * access checks are the left menu's own (menu.ini [Leftmenu_setup], parts/ini_menu.tpl with menu_layout='tiles'),
 * so a link the user may not open has no tile and an extension's link shows under "More". The page draws no image
 * but the Bootstrap icon masks. doc/features/6.0/admin4-design.md ("Setup overview").
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminSetupOverviewTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminSetupOverviewTest extends PHPUnit\Framework\TestCase
{
    const PAGE = 'design/admin4/templates/setup/setupmenu.tpl';
    const FIGURES = 'design/admin4/templates/setup/setupmenu_figure.tpl';
    const TILES = 'design/admin4/templates/parts/ini_menu_tiles.tpl';
    const MENU = 'design/admin4/templates/parts/ini_menu.tpl';
    const SETUP_MENU = 'design/admin4/templates/parts/setup/menu.tpl';
    const CONTEXT = 'design/admin/setup/setupmenu';

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

    /** @return array<string, string> link key => icon class name (without a4-bi-) of the page's tiles hash */
    private static function tileIcons()
    {
        preg_match_all( "/^\s*'([a-z_]+)',\s*hash\( 'icon', 'a4-bi-([a-z0-9-]+)',\s*'text', '([^']+)'\|i18n\( '" . preg_quote( self::CONTEXT, '/' ) . "' \) \)/m",
                        self::read( self::PAGE ), $m, PREG_SET_ORDER );
        $icons = array();
        foreach ( $m as $tile )
        {
            if ( isset( $icons[$tile[1]] ) )
                $icons[$tile[1] . ' (twice)'] = $tile[2];
            $icons[$tile[1]] = $tile[2];
        }
        return $icons;
    }

    /** @return string[] the class names .a4-bi-<name> that components.css defines */
    private static function definedIcons()
    {
        preg_match_all( '/^\.a4-bi-([a-z0-9-]+) \{/m', self::read( 'design/admin4/stylesheets/components.css' ), $m );
        return $m[1];
    }

    public function testTheSetupTabOpensTheOverview()
    {
        $this->assertSame( 'setup/menu', self::iniSection( 'Topmenu_setup' )['URL']['default'] ?? null );
        $setup = self::iniSection( 'Leftmenu_setup' );
        $this->assertSame( 'overview', array_keys( $setup['Links'] )[0], 'the overview is the first link of the Setup menu' );
        $this->assertSame( 'setup/menu', $setup['Links']['overview'] );
        $this->assertSame( array( 'overview' ), $setup['LeadLinks'] ?? null, 'the overview comes before the groups' );
        $this->assertStringContainsString( "'overview',           'Overview'|i18n( 'design/admin/parts/setup/menu' )", self::read( self::SETUP_MENU ) );
        // in the left menu the overview stands before the groups, a plain row in their look, marked when current
        $this->assertStringContainsString( "<div class=\"a4-navlead\">{include uri='design:parts/ini_menu_items.tpl' items=\$lead_items", self::read( self::MENU ) );
        $this->assertStringContainsString( '#leftmenu .a4-navlead > .leftmenu-items { margin: 0 0 var(--a4-space-2); padding: 0; list-style: none; }', self::read( 'design/admin4/stylesheets/admin4.css' ) );
    }

    public function testTheOverviewOpensForEveryRoleThatHadTheTab()
    {
        // the tab opened setup/cache (managecache) before: such a role must still open the tab's page
        $module = self::read( 'kernel/setup/module.php' );
        $this->assertSame( 1, preg_match( "/\\\$ViewList\['menu'\] = array\(\s*'functions' => array\( '([^']+)' \)/", $module, $m ) );
        $functions = preg_split( '/\s+or\s+/', $m[1] );
        foreach ( array( 'setup', 'managecache', 'administrate', 'system_info' ) as $function )
            $this->assertContains( $function, $functions, "a role with setup/$function opens the overview" );
        $this->assertNotContains( 'install', $functions );
    }

    public function testEveryLinkOfTheSetupMenuHasATileWithABootstrapIcon()
    {
        $setup = self::iniSection( 'Leftmenu_setup' );
        $icons = self::tileIcons();
        $this->assertSame( array(), array_values( array_filter( array_keys( $icons ), function ( $key ) { return strpos( $key, '(twice)' ) !== false; } ) ),
                           'a link has two tiles' );
        $lead = $setup['LeadLinks'] ?? array();
        foreach ( array_keys( $setup['Links'] ) as $key )
        {
            if ( in_array( $key, $lead, true ) )
            {
                $this->assertArrayNotHasKey( $key, $icons, "the lead link $key is the overview itself and has no tile" );
                continue;
            }
            $this->assertArrayHasKey( $key, $icons, "Links[$key] of Leftmenu_setup has no icon and description on the overview" );
        }
        // the links that menu.ini keeps commented out and parts/setup/menu.tpl still names get a tile when enabled
        foreach ( array( 'roles_and_policies', 'templates', 'toolbar_management', 'global_setting', 'look_and_feel' ) as $key )
            $this->assertArrayHasKey( $key, $icons );

        $defined = self::definedIcons();
        $root = dirname( __DIR__, 3 );
        foreach ( $icons as $key => $icon )
        {
            $this->assertContains( $icon, $defined, "tile $key: .a4-bi-$icon is not in components.css" );
            $svg = (string)file_get_contents( "$root/share/icons/bootstrap-icons/svg/$icon.svg" );
            $this->assertStringStartsWith( '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-' . $icon . '"', $svg, "tile $key: $icon.svg is no Bootstrap icon" );
        }
        $this->assertSame( count( $icons ), count( array_unique( $icons ) ), 'two functions share one icon' );
        // a link the page does not know (an extension's) takes a Bootstrap icon as well
        $this->assertStringContainsString( '{else}a4-bi-plug{/if}', self::read( self::TILES ) );
        $this->assertContains( 'plug', $defined );
        $this->assertNotContains( 'plug', $icons, 'the extension icon is for unknown links only' );
    }

    public function testEveryGroupedLinkShowsOnceInItsGroup()
    {
        $setup = self::iniSection( 'Leftmenu_setup' );
        $placed = array();
        foreach ( $setup['Groups'] as $group )
            foreach ( $setup["GroupLinks_$group"] as $key )
                $placed[] = $key;
        $this->assertSame( array_values( array_unique( $placed ) ), $placed, 'a link is in two groups' );

        // the tiles are the menu's groups: one list per group, each item once, built where the menu builds them
        $menu = self::read( self::MENU );
        $branch = strpos( $menu, "{if eq( \$menu_layout, 'tiles' )}" );
        $this->assertNotFalse( $branch, 'ini_menu.tpl has no tiles branch' );
        $this->assertLessThan( $branch, strpos( $menu, '$grouped_keys|contains( $item.key )|not' ), 'the groups are built before the layout is chosen' );
        $this->assertLessThan( $branch, strpos( $menu, "'more', 'name', 'More'" ) );
        $this->assertStringContainsString( "{include uri='design:parts/ini_menu_tiles.tpl' groups=\$groups", $menu );
        $tiles = self::read( self::TILES );
        $this->assertSame( 1, substr_count( $tiles, '{foreach $groups as $group}' ) );
        $this->assertSame( 1, substr_count( $tiles, '{foreach $group.items as $item}' ) );
        $this->assertSame( 1, substr_count( $tiles, '<li class="a4-card a4-setup-tile' ) );
        // the lead links (the overview) are no tile
        $this->assertMatchesRegularExpression( '/\$lead_links\|contains\( \$item\.key \)\|not/', substr( $menu, $branch ) );
    }

    public function testTheTilesKeepTheMenusAccessChecks()
    {
        $menu = self::read( self::MENU );
        $branch = strpos( $menu, "{if eq( \$menu_layout, 'tiles' )}" );
        foreach ( array( "concat( 'PolicyList_', \$link_key )", "fetch( 'user', 'has_access_to'", "fetch( 'user', 'can_open', hash( 'uri', \$link_url ) )", '$hidden_links|contains( $link_key )' ) as $check )
        {
            $at = strpos( $menu, $check );
            $this->assertNotFalse( $at, "ini_menu.tpl lost $check" );
            $this->assertLessThan( $branch, $at, "$check must run before the tiles are drawn" );
        }
        // the page reads no link of its own: it hands the menu to ini_menu.tpl
        $page = self::read( self::PAGE );
        $this->assertStringContainsString( "{include uri='design:parts/setup/menu.tpl'}", $page );
        $this->assertStringNotContainsString( 'ezini(', $page );
        $this->assertStringNotContainsString( '|ezurl', $page );
        $tiles = self::read( self::TILES );
        $this->assertStringNotContainsString( 'ezini(', $tiles );
        $this->assertSame( 1, substr_count( $tiles, '|ezurl' ), 'a tile links only where the menu item does' );
        $this->assertStringContainsString( "{if eq( \$item.state, 'link' )}", $tiles, 'a tile without access (NoAccessLinks=disabled) is no link' );
        // the overview's own left menu stays the menu: the page's variables do not leak into the page layout
        $this->assertStringContainsString( '{undef $setup_menu_layout $setup_menu_figures $setup_menu_tiles}', $page );
        $this->assertStringContainsString( "{if \$setup_menu_layout|ne( 'tiles' )}", self::read( self::SETUP_MENU ), 'the width control is the left menu\'s only' );
    }

    public function testFiguresAreCheapAndOnlyForKnownLinks()
    {
        $setup = self::iniSection( 'Leftmenu_setup' );
        $figures = self::read( self::FIGURES );
        preg_match_all( "/\{case match='([a-z_]+)'\}/", $figures, $m );
        $this->assertNotEmpty( $m[1] );
        foreach ( $m[1] as $key )
            $this->assertArrayHasKey( $key, $setup['Links'], "a figure for $key, which is no link of the Setup menu" );
        preg_match_all( "/fetch\( '(\w+)', '(\w+)'/", $figures, $fetches, PREG_SET_ORDER );
        $allowed = array( 'class/list', 'section/list', 'content/translation_list', 'user/logged_in_count' );
        foreach ( $fetches as $fetch )
            $this->assertContains( "{$fetch[1]}/{$fetch[2]}", $allowed, 'a figure must come from one cheap fetch' );
        // included only for a tile that is drawn, so only for a function the user may open
        $this->assertStringContainsString( '{include uri=$tile_figures figure_key=$item.key}', self::read( self::TILES ) );
    }

    public function testThePageDrawsNoImageButBootstrapIcons()
    {
        foreach ( array( self::PAGE, self::FIGURES, self::TILES, 'design/admin/templates/setup/setupmenu.tpl' ) as $path )
        {
            $text = preg_replace( '/\{\*.*?\*\}/s', '', self::read( $path ) );
            $this->assertStringNotContainsString( 'ezimage', $text, $path );
            $this->assertStringNotContainsStringIgnoringCase( '<img', $text, $path );
            $this->assertStringNotContainsStringIgnoringCase( '<svg', $text, $path );
            $this->assertDoesNotMatchRegularExpression( '/\.(png|gif|jpe?g|webp|ico)\b/i', $text, $path );
        }
        $css = self::read( 'design/admin4/stylesheets/modules/setup.css' );
        $start = strpos( $css, 'setup/menu: the Setup overview' );
        $this->assertNotFalse( $start );
        $this->assertDoesNotMatchRegularExpression( '/url\(/', substr( $css, $start ), 'the overview\'s rules load no image' );
        // icons are aria-hidden: the tile's name is its link text
        $this->assertStringContainsString( '<span class="a4-setup-tile-icon" aria-hidden="true">', self::read( self::TILES ) );
    }

    public function testTheGridFocusAndModes()
    {
        $css = self::read( 'design/admin4/stylesheets/modules/setup.css' );
        $this->assertSame( $css, self::read( 'design/admin/stylesheets/modules/setup.css' ), 'both copies of setup.css are the same file' );
        $this->assertStringContainsString( '.a4-setup-overview { container-type: inline-size; }', $css );
        foreach ( array( 2, 3, 4 ) as $columns )
            $this->assertMatchesRegularExpression( "/@container \(min-width: \d+px\) \{ \.a4-setup-tiles \{ grid-template-columns: repeat\($columns, minmax\(0, 1fr\)\); \} \}/", $css );
        $this->assertStringContainsString( 'grid-template-columns: minmax(0, 1fr);', $css, 'one column on a phone' );
        $this->assertStringContainsString( 'a.a4-setup-tile-link:focus-visible::after { outline: 3px solid var(--a4-ring);', $css );
        $this->assertMatchesRegularExpression( '/@media \(forced-colors: active\) \{\s*\.a4-setup-tile-icon/', $css );
        // the tile is a card, so its padding follows the density
        $this->assertStringContainsString( '<li class="a4-card a4-setup-tile', self::read( self::TILES ) );
    }

    public function testEveryTextIsTranslated()
    {
        $sources = array();
        foreach ( array( self::PAGE, self::FIGURES, self::TILES, 'design/admin/templates/setup/setupmenu.tpl' ) as $path )
        {
            preg_match_all( "/'((?:[^'\\\\]|\\\\.)+)'\|i18n\( '" . preg_quote( self::CONTEXT, '/' ) . "'/", self::read( $path ), $m );
            $sources = array_merge( $sources, $m[1] );
        }
        $sources = array_unique( $sources );
        $this->assertGreaterThan( 40, count( $sources ) );
        foreach ( array( 'ger-DE', 'eng-US' ) as $locale )
        {
            $ts = self::read( "share/translations/$locale/translation.ts" );
            $start = strpos( $ts, '<name>' . self::CONTEXT . '</name>' );
            $this->assertNotFalse( $start, "$locale has no context " . self::CONTEXT );
            $context = substr( $ts, $start, strpos( $ts, '</context>', $start ) - $start );
            foreach ( $sources as $source )
                $this->assertStringContainsString( '<source>' . htmlspecialchars( $source, ENT_NOQUOTES ) . '</source>', $context, "$locale: $source" );
            $menu = strpos( $ts, '<name>design/admin/parts/setup/menu</name>' );
            $this->assertStringContainsString( '<source>Overview</source>', substr( $ts, $menu, strpos( $ts, '</context>', $menu ) - $menu ), "$locale: Overview" );
        }
        $this->assertStringContainsString( "<source>Overview</source>\n        <translation>Übersicht</translation>", self::read( 'share/translations/ger-DE/translation.ts' ) );
    }
}
