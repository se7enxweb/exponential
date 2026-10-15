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
        $this->assertStringContainsString( '$known_icons|contains( $tile_icons[ $item.key ] )', self::read( self::TILES ), 'only a listed icon is drawn' );
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
        // the tiles' settings come from the menu's own section
        foreach ( array( 'LinkIcons', 'LinkDescriptions', 'LinkFigureTemplates' ) as $key )
            $this->assertStringContainsString( "ezini( \$ini_section, '$key', 'menu.ini' )", $menu );
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
        $this->assertStringContainsString( "\$tile_figures[ \$item.key ]|begins_with( 'design:' )", $tiles );
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
            $tpl->setVariable( 'groups', array( array( 'id' => 'more', 'name' => 'More', 'translated' => true, 'items' => array(
                array( 'key' => 'myreports', 'url' => 'myreports/list', 'name' => 'Reports', 'state' => 'link' ),
                array( 'key' => 'odd', 'url' => 'odd/list', 'name' => 'Odd', 'state' => 'link' ),
                array( 'key' => 'locked', 'url' => 'locked/list', 'name' => 'Locked', 'state' => 'no-access' ) ) ) ) );
            $tpl->setVariable( 'tile_icons', array( 'myreports' => 'bar-chart-line', 'odd' => 'x"><b>', 'locked' => 'bell' ) );
            $tpl->setVariable( 'tile_texts', array( 'myreports' => 'Monthly reports of the shop.', 'locked' => 'Locked away.' ) );
            $tpl->setVariable( 'tile_figures', array( 'myreports' => $figure, 'odd' => 'file:/etc/hosts', 'locked' => $figure ) );
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
