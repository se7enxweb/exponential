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

    /** @return string[] design.ini [AdminDesignSettings] IconList[] */
    private static function iconList()
    {
        $ini = self::read( 'settings/design.ini' );
        preg_match_all( '/^IconList\[\]=(\S+)$/m', $ini, $m );
        return $m[1];
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
        $this->assertArrayNotHasKey( 'overview', $setup['LinkIcons'] ?? array(), 'the overview is the page itself and has no tile' );
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

    public function testEveryLinkOfTheSetupMenuHasAnIconAndADescriptionInMenuIni()
    {
        $setup = self::iniSection( 'Leftmenu_setup' );
        $icons = $setup['LinkIcons'] ?? array();
        $texts = $setup['LinkDescriptions'] ?? array();
        foreach ( array_diff( array_keys( $setup['Links'] ), $setup['LeadLinks'] ?? array() ) as $key )
        {
            $this->assertArrayHasKey( $key, $icons, "Links[$key] of Leftmenu_setup has no LinkIcons[$key]" );
            $this->assertNotSame( '', trim( $texts[$key] ?? '' ), "Links[$key] of Leftmenu_setup has no LinkDescriptions[$key]" );
        }
        // the links that menu.ini keeps commented out and parts/setup/menu.tpl still names get a tile when enabled
        foreach ( array( 'roles_and_policies', 'templates', 'toolbar_management', 'global_setting', 'look_and_feel' ) as $key )
        {
            $this->assertArrayHasKey( $key, $icons );
            $this->assertArrayHasKey( $key, $texts );
        }
        $this->assertSame( array(), array_values( array_diff( array_keys( $texts ), array_keys( $icons ) ) ), 'a description without an icon' );

        $listed = self::iconList();
        $root = dirname( __DIR__, 3 );
        foreach ( $icons as $key => $icon )
        {
            $this->assertMatchesRegularExpression( '/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $icon, "LinkIcons[$key]" );
            $this->assertContains( $icon, $listed, "LinkIcons[$key]=$icon is not in design.ini IconList[], so the tile would draw the plug icon" );
            $svg = (string)file_get_contents( "$root/share/icons/bootstrap-icons/svg/$icon.svg" );
            $this->assertStringStartsWith( '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-' . $icon . '"', $svg, "LinkIcons[$key]: $icon.svg is no Bootstrap icon" );
        }
        $this->assertSame( count( $icons ), count( array_unique( $icons ) ), 'two functions share one icon' );
        $this->assertNotContains( 'plug', $icons, 'the plug icon is for links without an icon of their own' );
        $this->assertStringContainsString( "{set \$tile_icon = 'plug'", self::read( self::TILES ) );
        $this->assertStringContainsString( '$known_icons|contains( $item.icon )', self::read( self::TILES ), 'only a listed icon is drawn' );
    }

    public function testTheIconListIsEveryMaskRuleOfComponents()
    {
        preg_match_all( '/^\.a4-bi-([a-z0-9-]+) \{/m', self::read( 'design/admin4/stylesheets/components.css' ), $m );
        $listed = self::iconList();
        $this->assertSame( array_values( array_unique( $listed ) ), $listed, 'IconList[] names an icon twice' );
        $this->assertEqualsCanonicalizing( $m[1], $listed, 'design.ini IconList[] and the .a4-bi-* rules of components.css differ' );
        $this->assertContains( 'plug', $listed );
    }

    public function testEveryGroupedLinkShowsOnceInItsGroup()
    {
        $setup = self::iniSection( 'Leftmenu_setup' );
        $placed = array();
        foreach ( $setup['Groups'] as $group )
            foreach ( $setup["GroupLinks_$group"] ?? array() as $key )
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
        // the tiles' settings come from the section each link comes from, read before the tiles are drawn
        foreach ( array( 'LinkIcons', 'LinkDescriptions', 'LinkFigureTemplates' ) as $key )
        {
            $at = strpos( $menu, "ezini( \$section, '$key', 'menu.ini' )" );
            $this->assertNotFalse( $at, "ini_menu.tpl does not read $key of the link's section" );
            $this->assertLessThan( $branch, $at );
        }
        $this->assertSame( 1, substr_count( $tiles, '|ezurl' ), 'a tile links only where the menu item does' );
        $this->assertStringContainsString( "{if eq( \$item.state, 'link' )}", $tiles, 'a tile without access (NoAccessLinks=disabled) is no link' );
        // the overview's own left menu stays the menu: the page's variables do not leak into the page layout
        $this->assertStringContainsString( '{undef $setup_menu_layout}', $page );
        $this->assertStringContainsString( "{if \$setup_menu_layout|ne( 'tiles' )}", self::read( self::SETUP_MENU ), 'the width control is the left menu\'s only' );
    }

    public function testFiguresAreCheapAndOnlyOnTilesTheUserMayOpen()
    {
        $setup = self::iniSection( 'Leftmenu_setup' );
        $named = $setup['LinkFigureTemplates'] ?? array();
        $this->assertNotEmpty( $named );
        foreach ( $named as $key => $template )
        {
            $this->assertArrayHasKey( $key, $setup['Links'], "LinkFigureTemplates[$key] for no link of the Setup menu" );
            $this->assertSame( 'design:setup/setupmenu_figure.tpl', $template );
        }
        $figures = self::read( self::FIGURES );
        preg_match_all( "/\{case match='([a-z_]+)'\}/", $figures, $m );
        $this->assertEqualsCanonicalizing( array_keys( $named ), $m[1], 'the core figure template and LinkFigureTemplates[] name different keys' );
        preg_match_all( "/fetch\( '(\w+)', '(\w+)'/", $figures, $fetches, PREG_SET_ORDER );
        $allowed = array( 'class/list', 'section/list', 'content/translation_list', 'user/logged_in_count' );
        foreach ( $fetches as $fetch )
            $this->assertContains( "{$fetch[1]}/{$fetch[2]}", $allowed, 'a figure must come from one cheap fetch' );
        // the active extensions that exist, not the names in the settings
        $this->assertStringContainsString( "expinfo( 'active' )|count", $figures );
        $this->assertStringNotContainsString( 'ActiveExtensions', preg_replace( '/\{\*.*?\*\}/s', '', $figures ) );
        // one and many: two strings each
        foreach ( array( 'class' => 'classes', 'section' => 'sections', 'language' => 'languages' ) as $one => $many )
        {
            $this->assertStringContainsString( "'1 $one'|i18n", $figures );
            $this->assertStringContainsString( "'%count $many'|i18n", $figures );
        }
        $this->assertStringContainsString( "'1 user signed in'|i18n", $figures );

        // the figure only in the branch of a tile the user may open: its fetch asks no rights of its own
        $tiles = self::read( self::TILES );
        $link = strpos( $tiles, "{if eq( \$item.state, 'link' )}" );
        $else = strpos( $tiles, '{else}', $link );
        $include = strpos( $tiles, '{include uri=$tile_figure figure_key=$item.key}' );
        $this->assertNotFalse( $include );
        $this->assertSame( 1, substr_count( $tiles, '{include uri=$tile_figure' ) );
        $this->assertGreaterThan( $link, $include, 'the figure is included before the link branch' );
        $this->assertLessThan( $else, $include, 'the figure is included outside the link branch' );
        $this->assertStringContainsString( "\$item.figure|begins_with( 'design:' )", $tiles );
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
        // the tile is a card of the shared components
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
        $this->assertGreaterThan( 12, count( $sources ) );
        // the descriptions of menu.ini are translated in the menu's context, as LinkNames[] are
        $descriptions = array_values( self::iniSection( 'Leftmenu_setup' )['LinkDescriptions'] ?? array() );
        $this->assertGreaterThan( 30, count( $descriptions ) );
        foreach ( array( 'ger-DE', 'eng-US' ) as $locale )
        {
            $ts = self::read( "share/translations/$locale/translation.ts" );
            $start = strpos( $ts, '<name>' . self::CONTEXT . '</name>' );
            $this->assertNotFalse( $start, "$locale has no context " . self::CONTEXT );
            $context = substr( $ts, $start, strpos( $ts, '</context>', $start ) - $start );
            foreach ( $sources as $source )
                $this->assertStringContainsString( '<source>' . htmlspecialchars( $source, ENT_NOQUOTES ) . '</source>', $context, "$locale: $source" );
            $menu = strpos( $ts, '<name>design/admin/parts/setup/menu</name>' );
            $menuContext = substr( $ts, $menu, strpos( $ts, '</context>', $menu ) - $menu );
            $this->assertStringContainsString( '<source>Overview</source>', $menuContext, "$locale: Overview" );
            foreach ( $descriptions as $source )
                $this->assertStringContainsString( '<source>' . htmlspecialchars( $source, ENT_NOQUOTES ) . '</source>', $menuContext, "$locale: $source" );
        }
        $this->assertStringContainsString( "<source>Overview</source>\n        <translation>Übersicht</translation>", self::read( 'share/translations/ger-DE/translation.ts' ) );
    }

    /**
     * Draws the tiles of a fixture menu as an extension would set it up: a link with icon, description and figure,
     * one with an unlisted icon and a figure template outside design:, one the user may not open
     * (NoAccessLinks=disabled). The real template engine, no database.
     */
    public function testTheTilesOfAnExtensionsLinkRenderFromItsSettings()
    {
        $root = dirname( __DIR__, 3 );
        $dir = "$root/design/standard/templates/tests_setupoverview_" . getmypid();
        $saved = array_key_exists( 'eZCurrentAccess', $GLOBALS ) ? array( $GLOBALS['eZCurrentAccess'] ) : null;
        $handler = set_exception_handler( null );
        restore_exception_handler();
        // the fixture template is new: no override cache file, no override list from an earlier fetch
        $savedGlobals = array();
        foreach ( array( 'eZSiteBasics', 'eZOverrideTemplateCacheMap' ) as $name )
            $savedGlobals[$name] = array_key_exists( $name, $GLOBALS ) ? array( $GLOBALS[$name] ) : null;
        $GLOBALS['eZSiteBasics']['no-cache-adviced'] = true;
        unset( $GLOBALS['eZOverrideTemplateCacheMap'] );
        $overrides = new ReflectionProperty( 'eZTemplateDesignResource', 'overrideArrayCache' );
        $overrides->setValue( null, null );
        mkdir( $dir );
        try
        {
            file_put_contents( "$dir/figure.tpl", '<span class="a4-setup-tile-figure">figure of {$figure_key|wash}</span>' );
            $GLOBALS['eZCurrentAccess'] = array( 'name' => 'tiles_test', 'type' => 1 );
            $tpl = eZTemplate::factory();
            $figure = 'design:' . basename( $dir ) . '/figure.tpl';
            // the items as ini_menu.tpl builds them: icon, description, its context and figure of the link's section
            $context = 'design/admin/parts/setup/menu';
            $tpl->setVariable( 'groups', array( array( 'id' => 'more', 'name' => 'More', 'translated' => true, 'items' => array(
                array( 'key' => 'myreports', 'url' => 'myreports/list', 'name' => 'Reports', 'state' => 'link', 'group' => '',
                       'icon' => 'bar-chart-line', 'text' => 'Monthly reports of the shop.', 'context' => $context, 'figure' => $figure ),
                array( 'key' => 'odd', 'url' => 'odd/list', 'name' => 'Odd', 'state' => 'link', 'group' => '',
                       'icon' => 'x"><b>', 'text' => '', 'context' => $context, 'figure' => 'file:/etc/hosts' ),
                array( 'key' => 'locked', 'url' => 'locked/list', 'name' => 'Locked', 'state' => 'no-access', 'group' => '',
                       'icon' => 'bell', 'text' => 'Locked away.', 'context' => $context, 'figure' => $figure ) ) ) ) );
            $tpl->setVariable( 'known_icons', array( 'bar-chart-line', 'bell', 'plug' ) );
            $tpl->setVariable( 'tile_id_prefix', 'fx' );
            $tpl->setVariable( 'i18n_section', 'design/admin/parts/setup/menu' );
            $html = $tpl->fetch( "$root/" . self::TILES );
            $this->assertSame( array(), $tpl->errorLog() );
        }
        finally
        {
            array_map( 'unlink', glob( "$dir/*" ) );
            rmdir( $dir );
            $overrides->setValue( null, null );
            foreach ( $savedGlobals as $name => $value )
            {
                if ( $value === null )
                    unset( $GLOBALS[$name] );
                else
                    $GLOBALS[$name] = $value[0];
            }
            // the kernel's bootstrap installs its exception handler; the test leaves the one it found
            for ( $i = 0; $i < 5; $i++ )
            {
                $current = set_exception_handler( null );
                restore_exception_handler();
                if ( $current === $handler )
                    break;
                restore_exception_handler();
            }
            if ( $saved === null )
                unset( $GLOBALS['eZCurrentAccess'] );
            else
                $GLOBALS['eZCurrentAccess'] = $saved[0];
        }
        $tiles = preg_split( '/<li class="a4-card a4-setup-tile/', $html );
        $this->assertCount( 4, $tiles, 'three tiles' );
        // the extension's link: its icon, its description and its figure, both as the link's description
        $this->assertStringContainsString( '<span class="a4-icon a4-bi-bar-chart-line"></span>', $tiles[1] );
        $this->assertMatchesRegularExpression( '#<a class="a4-setup-tile-link" href="[^"]*myreports/list" aria-describedby="fx-myreports-text">Reports</a>#', $tiles[1] );
        $this->assertStringContainsString( '<span>Monthly reports of the shop.</span>', $tiles[1] );
        $this->assertStringContainsString( 'figure of myreports', $tiles[1] );
        // an icon that is not listed draws the plug, and nothing of the setting reaches the page
        $this->assertStringContainsString( '<span class="a4-icon a4-bi-plug"></span>', $tiles[2] );
        $this->assertStringNotContainsString( '<b>', $html );
        $this->assertStringNotContainsString( 'aria-describedby', $tiles[2], 'no description, no figure: nothing describes the link' );
        $this->assertStringNotContainsString( 'hosts', $html );
        // a function the user may not open: no link and no figure
        $this->assertStringContainsString( 'is-disabled', $tiles[3] );
        $this->assertStringNotContainsString( '<a ', $tiles[3] );
        $this->assertStringContainsString( 'Locked away.', $tiles[3] );
        $this->assertStringNotContainsString( 'figure of locked', $tiles[3] );
    }

    /**
     * The Design and Audit tabs can leave the top menu without losing a page: each has a group of the Setup menu
     * (GroupTabs[]) that lists its own menu (GroupSections[]) and its tab's page, shown only while the tab is not in
     * the top menu; by default every tab stays (HiddenTabs[] is empty). doc/features/6.0/admin4-design.md,
     * "Moving a tab into Setup".
     */
    public function testTheDesignAndAuditTabsHaveAGroupOfTheSetupMenu()
    {
        $setup = self::iniSection( 'Leftmenu_setup' );
        $top = self::iniSection( 'TopAdminMenu' );
        $this->assertSame( array(), $top['HiddenTabs'] ?? array(), 'every tab stays in the top menu by default' );
        $this->assertSame( array( 'design' => 'design', 'audit' => 'audit' ), $setup['GroupTabs'] ?? null );
        $this->assertSame( array( 'design' => 'Leftmenu_design', 'audit' => 'Leftmenu_audit' ), $setup['GroupSections'] ?? null );
        $setupMenu = self::read( self::SETUP_MENU );
        $listed = self::iconList();
        $root = dirname( __DIR__, 3 );
        foreach ( $setup['GroupTabs'] as $group => $tab )
        {
            $this->assertContains( $group, $setup['Groups'] );
            $this->assertArrayHasKey( $group, $setup['GroupNames'] );
            $this->assertContains( $tab, $top['Tabs'], "GroupTabs[$group] names no tab of Tabs[]" );
            $tabSection = self::iniSection( "Topmenu_$tab" );
            $section = self::iniSection( $setup['GroupSections'][$group] );
            $this->assertNotEmpty( $section['Links'] ?? array() );
            // the tab's own page is in the group: a link of its menu, or a link of the Setup menu in the group
            $urls = array_values( $section['Links'] );
            foreach ( $setup["GroupLinks_$group"] ?? array() as $key )
            {
                $urls[] = $setup['Links'][$key];
                // the Setup link to the tab's page asks what the tab asks
                if ( $setup['Links'][$key] === $tabSection['URL']['default'] )
                    $this->assertSame( $tabSection['PolicyList'], $setup["PolicyList_$key"] ?? null, "PolicyList_{$key}[]" );
            }
            $this->assertContains( $tabSection['URL']['default'], $urls, "the page of the tab $tab is not in the group $group" );
            // every link of the added menu has its tile: an icon of IconList[] with its file, a description, a name
            foreach ( array_keys( $section['Links'] ) as $key )
            {
                $icon = $section['LinkIcons'][$key] ?? '';
                $this->assertContains( $icon, $listed, "{$setup['GroupSections'][$group]} LinkIcons[$key]" );
                $this->assertFileExists( "$root/share/icons/bootstrap-icons/svg/$icon.svg" );
                $this->assertNotSame( '', trim( $section['LinkDescriptions'][$key] ?? '' ), "{$setup['GroupSections'][$group]} LinkDescriptions[$key]" );
                if ( !isset( $section['LinkNames'][$key] ) )
                    $this->assertStringContainsString( "'$group-$key',", $setupMenu, "parts/setup/menu.tpl names no $group-$key" );
            }
            $this->assertSame( count( $section['LinkIcons'] ), count( array_unique( $section['LinkIcons'] ) ), "two links of $group share one icon" );
            // the descriptions are translated in the added menu's context
            $context = 'design/admin/parts/' . explode( '_', $setup['GroupSections'][$group] )[1] . '/menu';
            foreach ( array( 'ger-DE', 'eng-US' ) as $locale )
            {
                $ts = self::read( "share/translations/$locale/translation.ts" );
                $start = strpos( $ts, "<name>$context</name>" );
                $this->assertNotFalse( $start, "$locale has no context $context" );
                $block = substr( $ts, $start, strpos( $ts, '</context>', $start ) - $start );
                foreach ( $section['LinkDescriptions'] as $text )
                    $this->assertStringContainsString( '<source>' . htmlspecialchars( $text, ENT_NOQUOTES ) . '</source>', $block, "$locale: $text" );
            }
        }
        // the audit menu's links into channels the user may not read stay out of the Setup menu too
        $this->assertStringContainsString( "section_hidden_links=hash( 'Leftmenu_audit', fetch( 'audit', 'hidden_menu_links', hash( 'section', 'Leftmenu_audit' ) ) )", $setupMenu );

        // ini_menu.tpl: a group of a shown tab is not drawn and its links go nowhere else; an added section is
        // checked like the menu's own links (its PolicyList[], per link PolicyList_<key>[] and the address)
        $menu = self::read( self::MENU );
        $this->assertStringContainsString( '{if and( $group_tab, $top_tabs|contains( $group_tab ), $hidden_tabs|contains( $group_tab )|not )}', $menu );
        $this->assertStringContainsString( '{if $tab_links|contains( $link_key )}{continue}{/if}', $menu );
        $this->assertStringContainsString( "{foreach ezini( \$section, 'PolicyList', 'menu.ini' ) as \$policy}", $menu );
        $this->assertStringContainsString( "ezini_hasvariable( \$section, concat( 'PolicyList_', \$link_key ), 'menu.ini' )", $menu );
        $this->assertStringContainsString( 'eq( $item.group, $group )', $menu, 'an added link is in its group' );
        $this->assertStringContainsString( '$added_urls|contains( $item.url|trim( \'/\' ) )|not', $menu, 'a Setup link the added menu has too shows once' );
        $this->assertMatchesRegularExpression( '/\{elseif \$section_hidden\|contains\( \$link_key \)\}\s*\{\* [^*]+\*\}\s*\{set \$added_urls = \$added_urls\|append\( \$link_url\|trim\( \'\/\' \) \)\}/', $menu, 'a link hidden in the added menu keeps the Setup link to its address out' );
    }

    /**
     * A page of a tab the Setup menu stands in for marks the Setup tab and draws the Setup menu while the tab is not
     * in the top menu, and keeps its own tab and menu otherwise. Drawn with the real template engine and menu.ini.
     */
    public function testAPageOfAMovedTabMarksTheSetupTab()
    {
        $root = dirname( __DIR__, 3 );
        $ini = eZINI::instance( 'menu.ini' );
        $hidden = $ini->hasVariable( 'TopAdminMenu', 'HiddenTabs' ) ? $ini->variable( 'TopAdminMenu', 'HiddenTabs' ) : null;
        $tabs = $ini->variable( 'TopAdminMenu', 'Tabs' );
        // not compiled: a compiled template holds the menu.ini values it was compiled with
        $basics = array_key_exists( 'eZSiteBasics', $GLOBALS ) ? array( $GLOBALS['eZSiteBasics'] ) : null;
        $GLOBALS['eZSiteBasics']['no-cache-adviced'] = true;
        $part = function ( $identifier ) use ( $root )
        {
            $tpl = eZTemplate::factory();
            $tpl->setVariable( 'navigation_part', array( 'identifier' => $identifier ) );
            $tpl->setVariable( 'module_result', array( 'navigation_part' => $identifier ) );
            $out = trim( $tpl->fetch( "$root/design/admin4/templates/parts/a4_navigation_part.tpl" ) );
            $this->assertSame( array(), $tpl->errorLog() );
            return $out;
        };
        try
        {
            // the tabs of settings/menu.ini, whatever a siteaccess or extension of this installation restates
            $ini->setVariable( 'TopAdminMenu', 'Tabs', self::iniSection( 'TopAdminMenu' )['Tabs'] );
            $ini->setVariable( 'TopAdminMenu', 'HiddenTabs', array() );
            $this->assertSame( 'expauditnavigationpart', $part( 'expauditnavigationpart' ), 'the audit tab is shown: its pages are its own' );
            $ini->setVariable( 'TopAdminMenu', 'HiddenTabs', array( 'audit', 'design' ) );
            $this->assertSame( 'ezsetupnavigationpart', $part( 'expauditnavigationpart' ) );
            $this->assertSame( 'ezsetupnavigationpart', $part( 'ezvisualnavigationpart' ) );
            $this->assertSame( 'ezcontentnavigationpart', $part( 'ezcontentnavigationpart' ), 'a tab that stays keeps its pages' );
            $ini->setVariable( 'TopAdminMenu', 'HiddenTabs', array( 'dashboard' ) );
            $this->assertSame( 'ezmynavigationpart', $part( 'ezmynavigationpart' ), 'no Setup group stands in for the dashboard' );
        }
        finally
        {
            $ini->setVariable( 'TopAdminMenu', 'Tabs', $tabs );
            if ( $hidden === null )
                $ini->removeSetting( 'TopAdminMenu', 'HiddenTabs' );
            else
                $ini->setVariable( 'TopAdminMenu', 'HiddenTabs', $hidden );
            if ( $basics === null )
                unset( $GLOBALS['eZSiteBasics'] );
            else
                $GLOBALS['eZSiteBasics'] = $basics[0];
        }
        // the top menu and the left menu of admin4 and admin4l both ask it
        foreach ( array( 'design/admin4/templates/page_topmenu.tpl', 'design/admin4/templates/page_leftmenu.tpl',
                         'design/admin4l/templates/explayouts/block/admin_tab_menu.tpl', 'design/admin4l/templates/explayouts/block/admin_left_menu.tpl' ) as $path )
            $this->assertStringContainsString( "{include uri='design:parts/a4_navigation_part.tpl'}{/set-block}", self::read( $path ), $path );
        $this->assertStringContainsString( 'navigationpart_identifier=$a4_current_part|trim', self::read( 'design/admin4/templates/page_topmenu.tpl' ) );
    }

    public function testWithoutTheDashboardTabTheLogoLeadsToTheDashboard()
    {
        foreach ( array( 'design/admin4/templates/page_topmenu.tpl', 'design/admin4l/templates/explayouts/block/admin_logo.tpl' ) as $path )
        {
            $tpl = self::read( $path );
            $this->assertSame( 2, substr_count( $tpl, '<a class="brand" href={$a4_brand_url|ezurl}' ), $path );
            $this->assertStringContainsString( "ezini( 'TopAdminMenu', 'HiddenTabs', 'menu.ini' )|contains( 'dashboard' )", $tpl, $path );
            $this->assertStringContainsString( "fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'dashboard' ) )", $tpl, $path );
            $this->assertStringContainsString( "ezini( 'Topmenu_dashboard', 'URL', 'menu.ini' )['default']", $tpl, $path );
        }
        $this->assertSame( 'content/dashboard', self::iniSection( 'Topmenu_dashboard' )['URL']['default'] );
    }

    /**
     * The access expression of the view setup/menu, evaluated by eZUser::hasAccessToView() for users whose policies
     * are given here instead of read from the database.
     *
     * @dataProvider accessProvider
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'accessProvider' )]
    public function testWhoOpensTheOverview( array $functions, $expected )
    {
        $user = new class( $functions ) extends eZUser
        {
            private $granted;
            public function __construct( array $granted ) { $this->granted = $granted; }
            public function hasAccessTo( $module, $function = false )
            {
                return array( 'accessWord' => in_array( "$module/$function", $this->granted, true ) ? 'yes' : 'no',
                              'accessList' => array( 'FunctionRequired' => array( 'Module' => $module, 'Function' => $function ) ) );
            }
        };
        $root = dirname( __DIR__, 3 );
        $module = new eZModule( "$root/kernel/setup", "$root/kernel/setup/module.php", 'setup' );
        $params = array();
        $this->assertSame( $expected, $user->hasAccessToView( $module, 'menu', $params ) );
    }

    public static function accessProvider()
    {
        return array(
            'cache management only (the tab\'s former page)' => array( array( 'setup/managecache' ), true ),
            'setup'                                         => array( array( 'setup/setup' ), true ),
            'system information only'                       => array( array( 'setup/system_info' ), true ),
            'nothing'                                       => array( array(), false ),
            'install only'                                  => array( array( 'setup/install' ), false ),
            'another module'                                => array( array( 'class/edit' ), false ),
        );
    }
}
