<?php
/**
 * admin4's left menu (parts/ini_menu.tpl, parts/ini_menu_items.tpl): a long menu can be arranged in groups from
 * menu.ini (Groups[], GroupNames[], GroupLinks_<group>[]); links in no group follow under "More"; the current page's
 * link carries aria-current="page"; a plain menu's list is named by its heading; the left column is the "Side menu"
 * navigation. The access checks per link are untouched. The Setup menu ends with the groups "AI" and "Updates" for
 * the links of the se7enx extensions (expai; ezupdate, git_manager) before "More"; a group without a link is not drawn.
 * TrailLinks[] moves links to the end of a menu, after the links extensions append and after every group, in all
 * three copies of the template (admin4, admin, adminui): the Dashboard menu closes with Change password, Edit profile.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminLeftMenuGroupsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminLeftMenuGroupsTest extends PHPUnit\Framework\TestCase
{
    /**
     * The Setup links of extensions that core menu.ini places in a group: the keys their menu.ini.append[.php] sets
     * in [Leftmenu_setup] Links[]. Without the extension the key is in no Links[] and the group shows nothing of it.
     */
    const EXTENSION_LINKS = array(
        'system'  => array( 'expqueue', 'dse' ),
        'ai'      => array( 'expai_summary', 'expai_models', 'expai_budgets' ),
        'updates' => array( 'ezupdate', 'git_manager', 'git_manager_backup' ),
    );

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
        $extensionLinks = array_merge( ...array_values( self::EXTENSION_LINKS ) );
        $seen = array();
        foreach ( $setup['Groups'] as $group )
        {
            $this->assertArrayHasKey( $group, $setup['GroupNames'] ?? array(), "group {$group} has no GroupNames[{$group}]" );
            // a group that adds another menu's links (GroupSections[]) needs no link of its own
            if ( !isset( $setup['GroupSections'][$group] ) )
                $this->assertNotEmpty( $setup["GroupLinks_$group"] ?? array(), "group {$group} has no GroupLinks_{$group}[]" );
            foreach ( $setup["GroupLinks_$group"] ?? array() as $key )
            {
                $this->assertNotContains( $key, $seen, "link $key is in two groups" );
                $this->assertContains( $key, array_merge( $links, $extensionLinks ), "GroupLinks_{$group}[] names $key, which is no Links[] key of Leftmenu_setup nor a known extension's" );
                $seen[] = $key;
            }
        }
        // the lead links (the overview) come before the groups and sit in none
        $lead = $setup['LeadLinks'] ?? array();
        foreach ( $lead as $key )
        {
            $this->assertContains( $key, $links, "LeadLinks[] names $key, which is no Links[] key of Leftmenu_setup" );
            $this->assertNotContains( $key, $seen, "lead link $key is in a group as well" );
        }
        // every other active link of the setup menu sits in a group; extensions' links go to "More" by themselves
        foreach ( array_diff( $links, $lead ) as $key )
            $this->assertContains( $key, $seen, "Links[{$key}] of Leftmenu_setup is in no group" );
    }

    /**
     * "Site and URLs" opens with Preload Sites, RSS and Packages and ends with RAD, in Links[] as well (the order a group and
     * a menu without groups draw). The classic menus (visual/menuconfig) are a link of the Design menu, after the toolbars,
     * not of Setup, and their page marks the Design tab.
     */
    public function testTheSiteGroupOpensWithPreloadRssPackagesAndTheClassicMenusAreInTheDesignMenu()
    {
        $setup = self::iniSection( 'Leftmenu_setup' );
        $site = array( 'preload', 'rss', 'packages', 'pdf_export', 'search_statistics', 'url_management', 'url_translator', 'url_wildcards', 'rad' );
        $this->assertSame( $site, $setup['GroupLinks_site'] );
        // a group draws its links in the order of Links[] (parts/ini_menu.tpl), so Links[] holds them in the same order
        $this->assertSame( $site, array_values( array_intersect( array_keys( $setup['Links'] ), $site ) ) );

        foreach ( array( 'Links', 'LinkIcons', 'LinkDescriptions' ) as $setting )
            $this->assertArrayNotHasKey( 'menu_management', $setup[$setting], "$setting [Leftmenu_setup]" );
        $this->assertArrayNotHasKey( 'PolicyList_menu_management', $setup );
        foreach ( $setup['Groups'] as $group )
            $this->assertNotContains( 'menu_management', $setup["GroupLinks_$group"] ?? array(), "GroupLinks_{$group}[]" );

        $design = self::iniSection( 'Leftmenu_design' );
        $this->assertSame( array( 'look_and_feel', 'toolbar_management', 'menu_management', 'template_editor' ), array_keys( $design['Links'] ) );
        $this->assertSame( 'visual/menuconfig', $design['Links']['menu_management'] );
        $this->assertSame( array( 'visual/*' ), $design['PolicyList_menu_management'] ?? null );
        $this->assertSame( 'menu-button-wide', $design['LinkIcons']['menu_management'] ?? null );
        $this->assertSame( 'The menus of the classic site designs.', $design['LinkDescriptions']['menu_management'] ?? null );

        // the page marks the Design tab, so the Design menu is drawn beside it
        $this->assertMatchesRegularExpression( '/\$ViewList\["menuconfig"\] = array\(\s*"script" => "menuconfig\.php",\s*\'default_navigation_part\' => \'ezvisualnavigationpart\',/', self::read( 'kernel/visual/module.php' ) );
        // both core copies of the Design menu name the link the same way (AdminUI's copy ships with exp_adminui)
        foreach ( array( 'design/admin', 'design/admin4' ) as $design )
        {
            $path = "$design/templates/parts/visual/menu.tpl";
            if ( is_file( dirname( __DIR__, 3 ) . '/' . $path ) )
                $this->assertStringContainsString( "'menu_management',    'Menus (classic)'|i18n( 'design/admin/parts/visual/menu' )", self::read( $path ), $path );
        }
    }

    public function testAiAndUpdatesAreTheLastGroupsBeforeMore()
    {
        $setup = self::iniSection( 'Leftmenu_setup' );
        $this->assertSame( array( 'content_model', 'site', 'design', 'workflow', 'users', 'audit', 'system', 'ai', 'updates' ), $setup['Groups'] );
        $this->assertSame( 'AI', $setup['GroupNames']['ai'] ?? null );
        $this->assertSame( 'Updates', $setup['GroupNames']['updates'] ?? null );
        $links = array_keys( $setup['Links'] ?? array() );
        foreach ( self::EXTENSION_LINKS as $group => $keys )
        {
            foreach ( $keys as $key )
            {
                $this->assertContains( $key, $setup["GroupLinks_$group"], "$key belongs in GroupLinks_{$group}[]" );
                // the core menu does not ship the link itself: the extension does, and only then it shows
                $this->assertNotContains( $key, $links, "Links[$key] is the extension's, not core's" );
            }
        }
        // the two new groups hold nothing but extension links: the core links stay where they were
        $this->assertSame( self::EXTENSION_LINKS['ai'], $setup['GroupLinks_ai'] );
        $this->assertSame( self::EXTENSION_LINKS['updates'], $setup['GroupLinks_updates'] );

        // "More" is built after every group of Groups[]: it stays last, and only links in no group go there
        $tpl = self::read( 'design/admin4/templates/parts/ini_menu.tpl' );
        $loop = strpos( $tpl, '{foreach $ini_menu_group_list as $group}' );
        $this->assertNotFalse( $loop );
        $this->assertGreaterThan( $loop, strpos( $tpl, "'more', 'name', 'More'" ) );
        // a group is added only when it holds a link the user may see: an empty group (no extension) is not drawn
        $this->assertMatchesRegularExpression( '/\{if \$group_items\|count\}\s*\{set \$ini_menu_groups = \$ini_menu_groups\|append\( hash\( \'id\', \$group,/', $tpl );
    }

    /**
     * Draws the Setup menu (parts/ini_menu.tpl of admin4, the real template engine, no database) with the real
     * Groups[] of Leftmenu_setup and the links that active extensions would add: three of expai, two of the update
     * extensions and one of an extension that names no group. Links of core are left out, so the core groups have no
     * link and must not be drawn; access checks per address are switched off, no link carries a PolicyList.
     */
    public function testExtensionLinksRenderInTheirGroupsAndEmptyGroupsAreLeftOut()
    {
        $links = array( 'overview' => 'setup/menu', 'myext_report' => 'myext/report', 'expai_summary' => 'expai/summary',
                        'git_manager_backup' => 'git_manager/backup', 'expai_models' => 'expai/models', 'ezupdate' => 'update/dashboard',
                        'expqueue' => 'expqueue/dashboard' );
        $names = array( 'overview' => 'Overview', 'myext_report' => 'My report', 'expai_summary' => 'EI: Summary',
                        'git_manager_backup' => 'Backup', 'expai_models' => 'EI: Models', 'ezupdate' => 'Updates and packages',
                        'expqueue' => 'Job queue' );

        $html = self::renderSetupMenu( $links, $names, 'menu' );
        preg_match_all( '/<details class="a4-navgroup" id="leftmenu-title-Leftmenu-setup-([a-z_]+)"/', $html, $m );
        $this->assertSame( array( 'system', 'ai', 'updates', 'more' ), $m[1], 'only groups with a link, in their order, "More" last' );
        $groups = array_combine( $m[1], array_slice( preg_split( '/<details class="a4-navgroup"/', $html ), 1 ) );
        $this->assertStringContainsString( '<summary class="a4-nav-title">AI</summary>', $groups['ai'] );
        $this->assertStringContainsString( '<summary class="a4-nav-title">Updates</summary>', $groups['updates'] );
        // the order inside a group is that of Links[]
        $this->assertMatchesRegularExpression( '#expai/summary".*expai/models"#s', $groups['ai'] );
        $this->assertMatchesRegularExpression( '#git_manager/backup".*update/dashboard"#s', $groups['updates'] );
        $this->assertStringContainsString( 'expqueue/dashboard"', $groups['system'] );
        $this->assertStringContainsString( 'myext/report"', $groups['more'] );
        $this->assertStringContainsString( ' open data-a4-current>', $groups['updates'], 'the group of the current page (update/dashboard) is marked' );
        foreach ( array( 'expai', 'update/', 'git_manager', 'expqueue' ) as $placed )
            $this->assertStringNotContainsString( $placed, $groups['more'], "$placed is grouped and must not show under More" );
        // the overview stands before the groups
        $this->assertLessThan( strpos( $html, '<details' ), strpos( $html, 'setup/menu"' ) );

        // the overview page draws the same groups as tiles, without the overview itself
        $tiles = self::renderSetupMenu( $links, $names, 'tiles' );
        preg_match_all( '/<h2 class="a4-h2 a4-setup-tilegroup-title" id="a4-tiles-Leftmenu-setup-([a-z_]+)">([^<]*)</', $tiles, $t );
        $this->assertSame( array( 'system', 'ai', 'updates', 'more' ), $t[1] );
        $this->assertSame( array( 'AI', 'Updates' ), array_slice( $t[2], 1, 2 ) );
        $this->assertStringContainsString( '<span class="a4-icon a4-bi-stars"></span>', $tiles );
        $this->assertStringContainsString( '<span class="a4-icon a4-bi-cloud-arrow-down"></span>', $tiles );
        $this->assertStringContainsString( '<span>What the AI features send where, on one printable page.</span>', $tiles );
        // the extension with no settings of its own draws the plug icon under "More"
        $more = substr( $tiles, strpos( $tiles, 'id="a4-tiles-Leftmenu-setup-more"' ) );
        $this->assertStringContainsString( '<span class="a4-icon a4-bi-plug"></span>', $more );
        $this->assertStringNotContainsString( 'setup/menu"', $tiles );

        // without a single extension link the AI and Updates groups are gone, and so is "More"
        $html = self::renderSetupMenu( array( 'overview' => 'setup/menu', 'expqueue' => 'expqueue/dashboard' ), $names, 'menu' );
        preg_match_all( '/<details class="a4-navgroup" id="leftmenu-title-Leftmenu-setup-([a-z_]+)"/', $html, $m );
        $this->assertSame( array( 'system' ), $m[1] );
    }

    /** The copies of the menu template: admin4's, the classic admin's and adminui's */
    const MENU_TEMPLATES = array(
        'admin4'  => 'design/admin4/templates/parts/ini_menu.tpl',
        'admin'   => 'design/admin/templates/parts/ini_menu.tpl',
        'adminui' => 'extension/exp_adminui/design/adminui/templates/parts/ini_menu.tpl',
    );

    /** @return string[] the link addresses of a drawn menu, in their order */
    private static function hrefs( $html )
    {
        preg_match_all( '/<a [^>]*?href="\/?([^"]*)"/', $html, $m );
        return array_map( function ( $url ) { return preg_replace( '#^.*?index\.php/?#', '', $url ); }, $m[1] );
    }

    /**
     * The Dashboard menu of settings/menu.ini as an extension (expai) appends to it: its Links[] in their order, then
     * the extension's, drawn in a section of its own so no PolicyList_<key>[] of the real menu asks for a user.
     */
    private static function dashboardMenu( $section, $layout, $template = 'admin4', array $settings = array() )
    {
        $my = self::iniSection( 'Leftmenu_my' );
        $links = array_merge( $my['Links'], array( 'expai_summary' => 'expai/summary', 'expai_vector' => 'expai/vector' ) );
        return self::renderMenu( self::MENU_TEMPLATES[$template], $section,
                                 array_merge( array( 'Links' => $links, 'LinkNames' => array(), 'TrailLinks' => $my['TrailLinks'] ?? array() ), $settings ),
                                 $layout, 'content/dashboard' );
    }

    public function testDashboardMenuOrderInEveryDesign()
    {
        $wanted = array( 'content/dashboard', 'collaboration/view/summary', 'content/pendinglist', 'content/draft', 'content/bookmark',
                         'mailpreferences/settings', 'notification/settings', 'audit/console', 'expai/summary', 'expai/vector',
                         'user/password', 'user/edit/(action)/edit' );
        $my = self::iniSection( 'Leftmenu_my' );
        $this->assertSame( array( 'change_password', 'edit_profile' ), $my['TrailLinks'] ?? null );
        foreach ( array_keys( self::MENU_TEMPLATES ) as $design )
        {
            $html = self::dashboardMenu( 'Leftmenu_trailtest', 'menu', $design );
            $this->assertSame( $wanted, self::hrefs( $html ), "the Dashboard menu of $design" );
            $this->assertSame( 1, substr_count( $html, '<ul' ), "$design draws the trail links in the one list" );
        }
        // a user without the audit console and the extension: the same order without them, Change password still
        // before Edit profile at the end (a trail link the menu does not draw is left out, nothing else moves)
        $html = self::renderMenu( self::MENU_TEMPLATES['admin4'], 'Leftmenu_trailtest',
                                  array( 'Links' => array_diff_key( $my['Links'], array( 'audit' => 1 ) ), 'LinkNames' => array(),
                                         'TrailLinks' => array( 'change_password', 'no_such_link', 'edit_profile', 'change_password' ) ),
                                  'menu', 'content/dashboard' );
        $this->assertSame( array_values( array_diff( $wanted, array( 'audit/console', 'expai/summary', 'expai/vector' ) ) ), self::hrefs( $html ) );
        // tiles without groups: the trail links are the last tiles
        $tiles = self::dashboardMenu( 'Leftmenu_trailtest', 'tiles' );
        $this->assertMatchesRegularExpression( '#expai/vector".*user/password".*user/edit/\(action\)/edit"#s', $tiles );
    }

    public function testTrailLinksFollowTheGroupsAndMore()
    {
        $links = array( 'overview' => 'setup/menu', 'myext_report' => 'myext/report', 'expai_summary' => 'expai/summary', 'expqueue' => 'expqueue/dashboard' );
        $html = self::renderMenu( self::MENU_TEMPLATES['admin4'], 'Leftmenu_setup',
                                  array( 'Links' => $links, 'LinkNames' => array(), 'TrailLinks' => array( 'expqueue', 'overview' ) ), 'menu', 'setup/menu' );
        preg_match_all( '/<details class="a4-navgroup" id="leftmenu-title-Leftmenu-setup-([a-z_]+)"/', $html, $m );
        $this->assertSame( array( 'ai', 'more' ), $m[1], 'a trail link sits in no group: System has no link left' );
        $this->assertStringNotContainsString( 'a4-navlead', $html, 'a key in LeadLinks[] and TrailLinks[] is a trail link' );
        $trail = substr( $html, strrpos( $html, '</details>' ) );
        $this->assertSame( 1, preg_match( '#^</details>\s*<div class="a4-navtrail">#', $trail ) );
        $this->assertSame( array( 'expqueue/dashboard', 'setup/menu' ), self::hrefs( $trail ) );
        $tiles = self::renderMenu( self::MENU_TEMPLATES['admin4'], 'Leftmenu_setup',
                                   array( 'Links' => $links, 'LinkNames' => array(), 'TrailLinks' => array( 'expqueue' ) ), 'tiles', 'setup/menu' );
        $this->assertSame( 'expqueue/dashboard', array_slice( self::hrefs( $tiles ), -1 )[0], 'the trail link is the last tile' );
        $this->assertStringContainsString( '#leftmenu .a4-navtrail > .leftmenu-items', self::read( 'design/admin4/stylesheets/admin4.css' ) );
    }

    /**
     * Fetches design/admin4/templates/parts/ini_menu.tpl for [Leftmenu_setup] with the given Links[] and LinkNames[]
     * in place of the menu's own, the site design admin4 and CheckViewAccess=disabled; restores every setting after.
     */
    /**
     * The classic design (design/admin, the classic siteaccess) draws the Setup tab's first page (setup/menu) as the
     * tiles of admin4, in the groups of the Setup menu, from its own copy of the menu template; its left menu stays one
     * list. Before, the page drew the setup toolbar of toolbar.ini, images the design does not have.
     */
    public function testTheClassicDesignDrawsTheSetupOverviewAsTiles()
    {
        $links = array( 'overview' => 'setup/menu', 'expqueue' => 'expqueue/dashboard', 'myext_report' => 'myext/report' );
        $tiles = self::renderMenu( self::MENU_TEMPLATES['admin'], 'Leftmenu_setup', array( 'Links' => $links, 'LinkNames' => array() ), 'tiles', 'setup/menu' );
        preg_match_all( '/<h2 class="a4-h2 a4-setup-tilegroup-title" id="a4-tiles-Leftmenu-setup-([a-z_]+)">/', $tiles, $m );
        $this->assertSame( array( 'system', 'more' ), $m[1], 'the groups of the Setup menu, the link in none under More' );
        $this->assertStringContainsString( '<span class="a4-icon a4-bi-list-task"></span>', $tiles, 'the icon of LinkIcons[expqueue]' );
        $this->assertSame( array( 'expqueue/dashboard', 'myext/report' ), self::hrefs( $tiles ), 'the overview itself is no tile' );
        $this->assertStringNotContainsString( 'leftmenu-items', $tiles );

        $menu = self::renderMenu( self::MENU_TEMPLATES['admin'], 'Leftmenu_setup', array( 'Links' => $links, 'LinkNames' => array() ), 'menu', 'setup/menu' );
        $this->assertSame( 1, substr_count( $menu, '<ul class="leftmenu-items">' ), 'the left menu stays one list' );
        $this->assertSame( array( 'setup/menu', 'expqueue/dashboard', 'myext/report' ), self::hrefs( $menu ) );

        $root = dirname( __DIR__, 3 );
        foreach ( array( 'templates/parts/ini_menu_tiles.tpl', 'templates/setup/setupmenu_figure.tpl' ) as $file )
            $this->assertFileEquals( "$root/design/admin4/$file", "$root/design/admin/$file", "both copies of $file are the same file" );
        $page = (string)file_get_contents( "$root/design/admin/templates/setup/setupmenu.tpl" );
        $this->assertStringContainsString( "{def \$setup_menu_layout = 'tiles'}", $page );
        $this->assertStringNotContainsString( 'tool_bar', $page, 'no toolbar of images the design does not have' );
        $this->assertStringContainsString( "'overview',           'Overview'|i18n( 'design/admin/parts/setup/menu' )",
                                           (string)file_get_contents( "$root/design/admin/templates/parts/setup/menu.tpl" ), 'the left menu names its first link' );
    }

    private static function renderSetupMenu( array $links, array $names, $layout )
    {
        return self::renderMenu( self::MENU_TEMPLATES['admin4'], 'Leftmenu_setup', array( 'Links' => $links, 'LinkNames' => $names ), $layout, 'update/dashboard' );
    }

    /**
     * Fetches a copy of parts/ini_menu.tpl for one menu.ini section with the given settings in place of its own, the
     * site design admin4 and CheckViewAccess=disabled; restores every setting after.
     */
    private static function renderMenu( $template, $section, array $settings, $layout, $uri )
    {
        $root = dirname( __DIR__, 3 );
        $menu = eZINI::instance( 'menu.ini' );
        $site = eZINI::instance( 'site.ini' );
        $savedIni = array(
            array( $menu, $section, 'TrailLinks' ),
            array( $menu, 'MenuAccessSettings', 'CheckViewAccess' ), array( $menu, 'MenuAccessSettings', 'NoAccessLinks' ),
            array( $site, 'DesignSettings', 'AdditionalSiteDesignList' ),
        );
        foreach ( array_keys( $settings ) as $name )
            if ( $name !== 'TrailLinks' )
                $savedIni[] = array( $menu, $section, $name );
        foreach ( $savedIni as $i => $entry )
            $savedIni[$i][] = $entry[0]->hasVariable( $entry[1], $entry[2] ) ? array( $entry[0]->variable( $entry[1], $entry[2] ) ) : null;
        $savedGlobals = array();
        foreach ( array( 'eZSiteBasics', 'eZOverrideTemplateCacheMap', 'eZTemplateDesignSetting', 'eZTemplateDesignResourceBases', 'eZCurrentAccess' ) as $name )
            $savedGlobals[$name] = array_key_exists( $name, $GLOBALS ) ? array( $GLOBALS[$name] ) : null;
        $handler = set_exception_handler( null );
        restore_exception_handler();
        $overrides = new ReflectionProperty( 'eZTemplateDesignResource', 'overrideArrayCache' );
        try
        {
            if ( $menu->hasVariable( $section, 'TrailLinks' ) )
                $menu->removeSetting( $section, 'TrailLinks' );
            foreach ( $settings as $name => $value )
                $menu->setVariable( $section, $name, $value );
            $menu->setVariable( 'MenuAccessSettings', 'CheckViewAccess', 'disabled' );
            $menu->setVariable( 'MenuAccessSettings', 'NoAccessLinks', 'enabled' );
            $site->setVariable( 'DesignSettings', 'AdditionalSiteDesignList', array() );
            $GLOBALS['eZSiteBasics']['no-cache-adviced'] = true;
            $GLOBALS['eZCurrentAccess'] = array( 'name' => 'menu_groups_test', 'type' => 1 );
            unset( $GLOBALS['eZOverrideTemplateCacheMap'], $GLOBALS['eZTemplateDesignResourceBases'] );
            $GLOBALS['eZTemplateDesignSetting'] = array( 'site' => 'admin4' );
            $overrides->setValue( null, null );
            $tpl = eZTemplate::factory();
            $tpl->setVariable( 'ini_section', $section );
            $tpl->setVariable( 'ui_context', 'navigation' );
            $tpl->setVariable( 'uri_string', $uri );
            $tpl->setVariable( 'menu_layout', $layout );
            $html = $tpl->fetch( "$root/$template" );
            PHPUnit\Framework\Assert::assertSame( array(), $tpl->errorLog() );
            return $html;
        }
        finally
        {
            // the next eZTemplate::factory() starts clean: no variable or error of this fetch is left on the singleton
            eZTemplate::resetInstance();
            foreach ( $savedIni as list( $ini, $block, $name, $value ) )
            {
                if ( $value === null )
                {
                    if ( $ini->hasVariable( $block, $name ) )
                        $ini->removeSetting( $block, $name );
                }
                else
                    $ini->setVariable( $block, $name, $value[0] );
            }
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
        }
    }

    public function testMenuTemplateGroupsWithDetailsAndKeepsTheFlatList()
    {
        $tpl = self::read( 'design/admin4/templates/parts/ini_menu.tpl' );
        // an empty Groups[] (reset in an override) gives the plain list: the branch counts, it does not ask hasVariable
        $this->assertStringContainsString( '{if $ini_menu_group_list|count}', $tpl );
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
        // the page itself is "page", a page below the link "true": the same mark for both
        $this->assertStringContainsString( '{if eq( $current_uri_string, $item.url )}', $items );
        $this->assertStringContainsString( 'aria-current="true"', $items );
        $this->assertStringContainsString( 'a[aria-current]', $css );
        // the current link's bar is an inset shadow on the left: rounded left corners bend it into a "("
        $this->assertSame( 1, preg_match( '/#leftmenu ul\.leftmenu-items li > div > a\[aria-current\] \{([^}]*)\}/', $css, $current ) );
        $this->assertStringContainsString( 'box-shadow: inset 3px 0 0 var(--a4-accent-text);', $current[1] );
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
        $this->assertStringContainsString( "var NAME = 'admin_navgroups';", $js, 'the closed groups are the eZPreference admin_navgroups (window.a4Pref)' );
    }
}
