<?php
/**
 * admin4's mega menu of the Setup tab (prototype, opt-in), checked on the files alone (no database):
 *
 *  MM-01 - off by default: menu.ini [TopAdminMenu] MegaMenuTabs[] is empty, the header draws the button and the page
 *          loads the script only while the setting names a tab
 *  MM-02 - the disclosure navigation pattern: the tab stays a link, a button beside it with aria-expanded,
 *          aria-controls and a name, a hidden panel of plain links with a heading per group; no role="menu"
 *  MM-03 - one source and the rights: the header carries no link of the panel (it sits in the header's cache block,
 *          keyed by the user's roles); the links come from setup/megamenu, a view without page layout with the
 *          functions of setup/menu, drawn from menu.ini [Leftmenu_setup] by parts/ini_menu.tpl, without figures
 *  MM-04 - from 1280px only, hidden in the "More" list, with forced-colors and reduced-motion rules
 *  MM-05 - the strings in ger-DE and eng-US
 *  MM-06 - the script: click, ArrowDown, Escape, Tab, a click outside, the delayed hover preview (300 ms in, 400 ms
 *          out, mouse only; the focus coming in keeps it), the width, the "More" list, one load and a failed one
 *          (a4_mega_menu.js run in node, tests/tests/design/js/mega_menu.js; skipped without node)
 *  MM-07 - curated: menu.ini [Leftmenu_setup] MegaMenuLinks[] names at most 12 existing links; the panel's template
 *          shows only those, in that order, the user may open, each in its group, groups without one left out, at most
 *          12; with no MegaMenuLinks[] every link (parts/a4_mega_menu.tpl rendered with eZTemplate, no database)
 *  MM-08 - with a tab moved into Setup (GroupSections[]) a chosen key whose link is not drawn takes the group's link
 *          of the same address (audit: the audit group's Settings); audit-<key> chooses a group link directly
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminMegaMenuTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminMegaMenuTest extends PHPUnit\Framework\TestCase
{
    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function read( $path )
    {
        $text = file_get_contents( self::root() . '/' . $path );
        self::assertIsString( $text, "$path is readable" );
        return $text;
    }

    private static function css()
    {
        return preg_replace( '#/\*.*?\*/#s', '', self::read( 'design/admin4/stylesheets/admin4.css' ) );
    }

    /** The inner text of every "@media <query> { ... }" block of $css, joined */
    private static function media( $css, $query )
    {
        $inner = '';
        $offset = 0;
        $head = '@media ' . $query . ' {';
        while ( ( $start = strpos( $css, $head, $offset ) ) !== false )
        {
            $i = $start + strlen( $head );
            $depth = 1;
            $from = $i;
            for ( ; $i < strlen( $css ) && $depth > 0; $i++ )
            {
                if ( $css[$i] === '{' ) $depth++;
                if ( $css[$i] === '}' ) $depth--;
            }
            $inner .= substr( $css, $from, $i - 1 - $from ) . "\n";
            $offset = $i;
        }
        return $inner;
    }

    /** MM-01 */
    public function testItIsOffUntilATabIsNamed()
    {
        $ini = self::read( 'settings/menu.ini' );
        $this->assertMatchesRegularExpression( '/^\[TopAdminMenu\]$/m', $ini );
        $section = substr( $ini, strpos( $ini, '[TopAdminMenu]' ) );
        $section = substr( $section, 0, strpos( $section, "\n[", 1 ) );
        $this->assertMatchesRegularExpression( '/^MegaMenuTabs\[\]$/m', $section, 'MegaMenuTabs[] is declared, empty' );
        $this->assertDoesNotMatchRegularExpression( '/^MegaMenuTabs\[\]=/m', $ini, 'no tab has a mega menu by default' );
        $this->assertStringContainsString( '#   MegaMenuTabs[]=setup', $section, 'the comment says how to switch it on' );

        // the header asks for the setting, and for the user's access to the fragment, before it draws a button
        $top = self::read( 'design/admin4/templates/page_topmenu.tpl' );
        $check = strpos( $top, "ezini_hasvariable( 'TopAdminMenu', 'MegaMenuTabs', 'menu.ini' )" );
        $this->assertNotFalse( $check );
        $this->assertStringContainsString( "ezini( 'TopAdminMenu', 'MegaMenuTabs', 'menu.ini' )|contains( 'setup' )", $top );
        $this->assertStringContainsString( "fetch( 'user', 'can_open', hash( 'uri', 'setup/megamenu' ) )", $top );
        $this->assertStringContainsString( '$ui_context_edit|not', $top, 'no mega menu on edit pages' );
        $this->assertLessThan( strpos( $top, '{foreach topmenu(' ), strpos( $top, "{set \$a4_mega_part = ezini( 'Topmenu_setup', 'NavigationPartIdentifier', 'menu.ini' )}" ) );
        // the item draws a button only for the part the header chose
        $item = self::read( 'design/admin4/templates/page_topmenuitem.tpl' );
        $this->assertStringContainsString( '{if is_set( $a4_mega_part )}', $item );
        $this->assertStringContainsString( 'eq( $a4_mega_part, $menu_item.navigationpart_identifier )', $item );

        // the script is loaded only while the setting names a tab
        $head = self::read( 'design/admin4/templates/page_head_script.tpl' );
        $load = strpos( $head, "{ezscript_load( array( 'a4_mega_menu.js' ) )}" );
        $this->assertNotFalse( $load );
        $this->assertLessThan( $load, strpos( $head, "{if ezini( 'TopAdminMenu', 'MegaMenuTabs', 'menu.ini' )|count}" ) );
        $this->assertFileExists( self::root() . '/design/admin4/javascript/a4_mega_menu.js' );
    }

    /** MM-02 */
    public function testTheButtonIsADisclosureBesideTheTabLink()
    {
        $item = self::read( 'design/admin4/templates/page_topmenuitem.tpl' );
        // the tab is still a link, drawn before the button
        $link = strpos( $item, '<a href={$menu_item.url|ezurl}' );
        $button = strpos( $item, '<button type="button" class="a4-mega-toggle"' );
        $this->assertNotFalse( $link );
        $this->assertNotFalse( $button );
        $this->assertLessThan( $button, $link );
        $tag = substr( $item, $button, strpos( $item, '>', strpos( $item, 'data-a4-mega-src', $button ) ) - $button );
        $this->assertStringContainsString( 'aria-expanded="false"', $tag );
        $this->assertStringContainsString( 'aria-controls="a4-mega-setup"', $tag );
        $this->assertStringContainsString( "aria-label=\"{'Open the Setup menu'|i18n( 'design/admin/pagelayout' )|wash}\"", $tag );
        $this->assertStringContainsString( "data-a4-mega-src={'setup/megamenu'|ezurl}", $tag );
        $this->assertStringContainsString( '<span class="a4-icon a4-bi-chevron-down" aria-hidden="true"></span>', $item );
        // the panel it controls: hidden, a status line until the links arrive, the overview link
        $this->assertStringContainsString( '<div id="a4-mega-setup" class="a4-mega-panel" hidden', $item );
        $this->assertStringContainsString( '<p class="a4-mega-status" role="status">', $item );
        $this->assertStringContainsString( "<a href={'setup/menu'|ezurl}>{'All Setup functions'|i18n( 'design/admin/pagelayout' )|wash}", $item );

        // the groups: a section with a heading each, plain links in lists; no menu roles anywhere
        $groups = self::read( 'design/admin4/templates/parts/a4_mega_menu.tpl' );
        $this->assertStringContainsString( '<section class="a4-mega-group" aria-labelledby=', $groups );
        $this->assertStringContainsString( '<h2 class="a4-mega-group-title" id=', $groups );
        $this->assertStringContainsString( '<ul class="a4-mega-links">', $groups );
        $this->assertStringContainsString( '<a href={$item.url|ezurl}{if $mega_text} aria-describedby=', $groups );
        foreach ( array( 'design/admin4/templates/page_topmenuitem.tpl', 'design/admin4/templates/parts/a4_mega_menu.tpl',
                         'design/admin4/templates/setup/megamenu.tpl', 'design/admin4/javascript/a4_mega_menu.js' ) as $file )
        {
            $this->assertStringNotContainsString( 'role="menu', self::read( $file ), "$file: a disclosure of links, not a menu" );
            $this->assertStringNotContainsString( "'menuitem'", self::read( $file ), $file );
        }
    }

    /** MM-03 */
    public function testTheLinksAreLoadedFromTheSetupMenuSource()
    {
        // the header, where the button is, is cached per role set
        $layout = self::read( 'design/admin4/templates/pagelayout.tpl' );
        $this->assertMatchesRegularExpression( "/\{cache-block keys=array\( \\\$ui_context, \\\$ui_component, \\\$user_hash,[^}]*\}\s*\{\* HEADER/", $layout );
        // ... and carries no link of the panel: no menu section is read there
        foreach ( array( 'design/admin4/templates/page_topmenu.tpl', 'design/admin4/templates/page_topmenuitem.tpl' ) as $file )
        {
            $text = self::read( $file );
            $this->assertStringNotContainsString( 'Leftmenu_setup', $text, $file );
            $this->assertStringNotContainsString( 'ini_menu', $text, $file );
        }

        // the view: the functions of the overview, ajax, without page layout and kept by no shared cache
        $module = self::read( 'kernel/setup/module.php' );
        $this->assertMatchesRegularExpression( "/\\\$ViewList\['menu'\] = array\(\s*'functions' => array\( '([^']+)' \)/", $module );
        preg_match( "/\\\$ViewList\['menu'\] = array\(\s*'functions' => array\( '([^']+)' \)/", $module, $menu );
        preg_match( "/\\\$ViewList\['megamenu'\] = array\((.*?)\);/s", $module, $mega );
        $this->assertNotEmpty( $mega, 'setup/megamenu is a view' );
        $this->assertStringContainsString( "'functions' => array( '" . $menu[1] . "' )", $mega[1] );
        $this->assertStringContainsString( "'script' => 'megamenu.php'", $mega[1] );
        $this->assertStringContainsString( "'ui_context' => 'ajax'", $mega[1] );
        $view = self::read( 'kernel/private/classes/views/setup/megamenu.php' );
        $this->assertStringContainsString( "\$Result['pagelayout'] = false;", $view );
        $this->assertStringContainsString( "header( 'Cache-Control: private, no-store' );", $view );
        $this->assertStringContainsString( "'debug-enabled' => false", $view );
        $this->assertStringContainsString( "\$tpl->fetch( 'design:setup/megamenu.tpl' )", $view );
        $this->assertStringContainsString( "'Exponential\\\\View\\\\Kernel\\\\Setup\\\\Megamenu'", self::read( 'autoload/ezp_kernel.php' ) );
        $this->assertStringContainsString( '\Exponential\View\Kernel\Setup\Megamenu::main(', self::read( 'kernel/setup/megamenu.php' ) );

        // the fragment: the Setup menu's own template, as for the overview's tiles, drawn as the mega menu's groups
        $fragment = self::read( 'design/admin4/templates/setup/megamenu.tpl' );
        $this->assertStringContainsString( '<div class="a4-mega-groups" data-a4-mega-groups>', $fragment );
        $this->assertStringContainsString( "{include uri='design:parts/setup/menu.tpl'}", $fragment );
        $this->assertStringContainsString( "\$setup_menu_layout = 'tiles'", $fragment );
        $this->assertStringContainsString( "\$setup_tiles_layout = 'mega'", $fragment );
        $this->assertStringNotContainsString( 'ezini(', $fragment, 'no second configuration' );
        $this->assertStringContainsString( 'tiles_layout=$setup_tiles_layout', self::read( 'design/admin4/templates/parts/setup/menu.tpl' ) );
        $menu = self::read( 'design/admin4/templates/parts/ini_menu.tpl' );
        $this->assertStringContainsString( "{if eq( \$tiles_layout, 'mega' )}", $menu );
        $this->assertStringContainsString( "{include uri='design:parts/a4_mega_menu.tpl' groups=\$groups", $menu );
        // the access checks run before either is drawn
        $this->assertLessThan( strpos( $menu, "{if eq( \$tiles_layout, 'mega' )}" ), strpos( $menu, "fetch( 'user', 'can_open', hash( 'uri', \$link_url ) )" ) );
        // no figures, no counts in the menu
        $groups = self::read( 'design/admin4/templates/parts/a4_mega_menu.tpl' );
        $this->assertStringNotContainsString( 'item.figure', $groups );
        $this->assertStringNotContainsString( 'fetch(', $groups );
        $this->assertStringContainsString( '$known_icons|contains( $item.icon )', $groups, 'only icons of IconList[]' );
        // a classic design gets a pointer to the Setup menu instead of an error
        $this->assertStringContainsString( 'data-a4-mega-groups', self::read( 'design/standard/templates/setup/megamenu.tpl' ) );
    }

    /** MM-04 */
    public function testItShowsFromOneHeaderRowOnly()
    {
        $css = self::css();
        $outside = preg_replace( '/@media[^{]*\{(?:[^{}]*\{[^{}]*\})*[^{}]*\}/s', '', $css );
        $this->assertMatchesRegularExpression( '/\.a4-mega-toggle,\s*\.a4-mega-panel \{ display: none; \}/', $outside, 'hidden below 1280px' );
        $wide = self::media( $css, 'screen and (min-width: 1280px)' );
        $this->assertStringContainsString( '.a4-mega-toggle {', $wide );
        $this->assertMatchesRegularExpression( '/\.a4-nav-more-list \.a4-mega-toggle,\s*\.a4-nav-more-list \.a4-mega-panel \{ display: none; \}/', $wide );
        $this->assertStringContainsString( '.a4-mega-panel[hidden] { display: none; }', $wide );
        // as wide as its groups need, up to three of 17rem side by side; descriptions are not cut
        $this->assertStringContainsString( 'width: fit-content;', $wide );
        $this->assertStringContainsString( 'max-width: calc(3 * 17rem + 2 * var(--a4-space-6));', $wide, 'at most three groups side by side' );
        $this->assertStringContainsString( 'flex: 0 0 17rem;', $wide );
        $this->assertDoesNotMatchRegularExpression( '/\.a4-mega-text \{[^}]*(ellipsis|nowrap)/', $wide );
        $this->assertStringContainsString( '.a4-mega-panel::before {', $wide, 'the hover bridge' );
        $this->assertStringContainsString( '.a4-mega-toggle:focus-visible { outline: 3px solid var(--a4-ring);', $wide );
        $forced = self::media( $css, 'screen and (min-width: 1280px) and (forced-colors: active)' );
        $this->assertStringContainsString( '.a4-mega-panel { border-color: CanvasText; }', $forced );
        $this->assertStringContainsString( '.a4-mega-toggle .a4-icon { transition: none; }', self::media( $css, '(prefers-reduced-motion: reduce)' ) );
        // the script agrees on the width
        $this->assertStringContainsString( "window.matchMedia('(min-width: 1280px)')", self::read( 'design/admin4/javascript/a4_mega_menu.js' ) );
    }

    /** MM-05 */
    public function testTheStringsAreTranslated()
    {
        $strings = array( 'Open the Setup menu' => 'Setup-Menü öffnen', 'Loading the Setup menu' => 'Setup-Menü wird geladen',
                          'The Setup menu could not be loaded.' => 'Das Setup-Menü konnte nicht geladen werden.',
                          'All Setup functions' => 'Alle Setup-Funktionen' );
        foreach ( array( 'ger-DE', 'eng-US' ) as $language )
        {
            $dom = new DOMDocument();
            $this->assertTrue( $dom->load( self::root() . "/share/translations/$language/translation.ts" ) );
            $xpath = new DOMXPath( $dom );
            foreach ( $strings as $source => $german )
            {
                $found = $xpath->query( "//context[name='design/admin/pagelayout']/message[source=" . self::xpathString( $source ) . ']/translation' );
                $this->assertSame( 1, $found->length, "$language: $source" );
                $this->assertSame( $language === 'ger-DE' ? $german : $source, $found->item( 0 )->textContent, "$language: $source" );
            }
        }
    }

    private static function xpathString( $s )
    {
        return strpos( $s, "'" ) === false ? "'$s'" : '"' . $s . '"';
    }

    /** Renders parts/a4_mega_menu.tpl with groups of fake links; returns [ group id => [ link names ] ] */
    private static function renderGroups( array $megaLinks, array $groups, array $megaUrls = array() )
    {
        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'groups', $groups );
        $tpl->setVariable( 'mega_links', $megaLinks );
        $tpl->setVariable( 'mega_urls', $megaUrls );
        $tpl->setVariable( 'mega_id_prefix', 'mm' );
        $tpl->setVariable( 'i18n_section', 'design/admin/parts/setup/menu' );
        $tpl->setVariable( 'known_icons', array( 'gear' ) );
        $html = $tpl->fetch( 'file:design/admin4/templates/parts/a4_mega_menu.tpl' );
        $out = array();
        preg_match_all( '#<section class="a4-mega-group" aria-labelledby="mm-([a-z0-9_]+)">(.*?)</section>#s', $html, $sections, PREG_SET_ORDER );
        foreach ( $sections as $section )
        {
            preg_match_all( '#<(?:a href="[^"]*"[^>]*|span class="a4-mega-name")>([^<]+)<#', $section[2], $names );
            $out[$section[1]] = $names[1];
        }
        return $out;
    }

    private static function fakeGroups( $perGroup = 3, $count = 3 )
    {
        $groups = array();
        for ( $g = 1; $g <= $count; $g++ )
        {
            $items = array();
            for ( $i = 1; $i <= $perGroup; $i++ )
                $items[] = array( 'key' => "l{$g}_{$i}", 'url' => "x/l{$g}_{$i}", 'name' => "L{$g}_{$i}", 'state' => 'link', 'group' => '',
                                  'icon' => 'gear', 'text' => '', 'context' => 'design/admin/parts/setup/menu', 'figure' => '' );
            $groups[] = array( 'id' => "g$g", 'name' => "Group $g", 'items' => $items );
        }
        return $groups;
    }

    /** MM-07 */
    public function testTheDefaultChoiceIsShortAndNamesExistingLinks()
    {
        $ini = self::read( 'settings/menu.ini' );
        $section = substr( $ini, strpos( $ini, "\n[Leftmenu_setup]" ) );
        $section = substr( $section, 0, strpos( $section, "\n[", 2 ) );
        preg_match_all( '/^MegaMenuLinks\[\]=(\S+)$/m', $section, $m );
        $choice = $m[1];
        $this->assertMatchesRegularExpression( '/^MegaMenuLinks\[\]$/m', $section, 'the list is reset before it is set' );
        $this->assertGreaterThanOrEqual( 6, count( $choice ) );
        $this->assertLessThanOrEqual( 12, count( $choice ), 'at most 12 links' );
        $this->assertSame( array_values( array_unique( $choice ) ), $choice );
        // every key is a link of the menu or of one of its groups (the extensions' links are named in GroupLinks_*)
        preg_match_all( '/^(?:Links\[([a-z0-9_]+)\]=|GroupLinks_[a-z_]+\[\]=([a-z0-9_]+)$)/m', $section, $known );
        $known = array_filter( array_merge( $known[1], $known[2] ) );
        foreach ( $choice as $key )
            $this->assertContains( $key, $known, "MegaMenuLinks[]=$key is no link of [Leftmenu_setup]" );
        // the AI and Updates groups are in the choice, shown when their extensions are active
        foreach ( array( 'expai_summary', 'ezupdate', 'git_manager' ) as $key )
            $this->assertContains( $key, $choice );
        // the template reads the choice from the menu's own section, nowhere else
        $this->assertStringContainsString( "mega_links=cond( ezini_hasvariable( \$ini_section, 'MegaMenuLinks', 'menu.ini' ), ezini( \$ini_section, 'MegaMenuLinks', 'menu.ini' ), array() )",
                                           self::read( 'design/admin4/templates/parts/ini_menu.tpl' ) );
    }

    /** MM-07 */
    public function testOnlyTheChosenLinksShowInTheirGroupsAndOrder()
    {
        $groups = self::fakeGroups();
        $groups[0]['items'][2]['state'] = 'no-access';   // l1_3: the user may not open it
        $groups[1]['items'][0]['state'] = 'disabled';    // l2_1: disabled in this context

        $shown = self::renderGroups( array( 'l3_2', 'l1_2', 'l1_3', 'nothing', 'l2_1', 'l1_1', 'l1_2' ), $groups );
        // groups in the order of their first chosen link, links in the chosen order; no link the user may not open,
        // no disabled one, no unknown key, none twice; group 2 has no chosen link left and is left out
        $this->assertSame( array( 'g3' => array( 'L3_2' ), 'g1' => array( 'L1_2', 'L1_1' ) ), $shown );
    }

    /** MM-07 */
    public function testAtMostTwelveLinksShow()
    {
        $groups = self::fakeGroups( 5, 3 );
        $keys = array();
        foreach ( $groups as $group )
            foreach ( $group['items'] as $item )
                $keys[] = $item['key'];
        $shown = self::renderGroups( $keys, $groups );
        $this->assertSame( 12, count( call_user_func_array( 'array_merge', array_values( $shown ) ) ) );
        $this->assertSame( array( 'L3_1', 'L3_2' ), $shown['g3'], 'the first twelve chosen' );
    }

    /**
     * MM-08: a tab moved into Setup (SetupTabs[]/HiddenTabs[] with GroupTabs[audit], GroupSections[audit]=
     * Leftmenu_audit): its group brings the links of [Leftmenu_audit] as audit-<key>, and the Setup link audit
     * (audit/settings) is not drawn, as its address is the group's audit-settings. The chosen key audit then takes
     * that link; audit-<key> chooses one of the group's links directly.
     */
    public function testAChosenLinkOfAMovedTabFallsBackToTheGroupsLinkWithItsAddress()
    {
        // the settings that make it happen are those of core menu.ini
        $ini = self::read( 'settings/menu.ini' );
        $setup = substr( $ini, strpos( $ini, "\n[Leftmenu_setup]" ) );
        $setup = substr( $setup, 0, strpos( $setup, "\n[", 2 ) );
        $this->assertStringContainsString( "\nGroupSections[audit]=Leftmenu_audit\n", $setup );
        $this->assertStringContainsString( "\nLinks[audit]=audit/settings\n", $setup );
        $this->assertMatchesRegularExpression( '/^MegaMenuLinks\[\]=audit$/m', $setup );
        $audit = substr( $ini, strpos( $ini, "\n[Leftmenu_audit]" ) );
        $audit = substr( $audit, 0, strpos( $audit, "\n[", 2 ) );
        $this->assertStringContainsString( "\nLinks[settings]=audit/settings\n", $audit, 'the audit group has a link with the address of the Setup link audit' );
        $this->assertStringContainsString( "mega_urls=ezini( \$ini_section, 'Links', 'menu.ini' )", self::read( 'design/admin4/templates/parts/ini_menu.tpl' ) );

        // the groups as ini_menu.tpl hands them over with the audit tab moved into Setup
        $item = function ( $key, $url, $name, $group = '' ) {
            return array( 'key' => $key, 'url' => $url, 'name' => $name, 'state' => 'link', 'group' => $group,
                          'icon' => 'gear', 'text' => '', 'context' => 'design/admin/parts/setup/menu', 'figure' => '' );
        };
        $groups = array(
            array( 'id' => 'content_model', 'name' => 'Content model', 'items' => array( $item( 'classes', 'class/grouplist', 'Classes' ) ) ),
            array( 'id' => 'audit', 'name' => 'Audit', 'items' => array( $item( 'audit-dashboard', 'audit/dashboard', 'Dashboard', 'audit' ),
                                                                         $item( 'audit-console', 'audit/console', 'Console', 'audit' ),
                                                                         $item( 'audit-settings', '/audit/settings', 'Settings', 'audit' ) ) ),
        );
        $urls = array( 'classes' => 'class/grouplist', 'audit' => 'audit/settings' );

        $this->assertSame( array( 'content_model' => array( 'Classes' ), 'audit' => array( 'Settings' ) ),
                           self::renderGroups( array( 'classes', 'audit' ), $groups, $urls ), 'audit takes the group link of the same address' );
        $this->assertSame( array( 'audit' => array( 'Console', 'Settings' ) ),
                           self::renderGroups( array( 'audit-console', 'audit', 'audit-settings' ), $groups, $urls ), 'a link chosen twice shows once' );
        // only a link a group adds stands in: a key of this menu that is drawn is taken by its key
        $groups[0]['items'][] = $item( 'other', 'audit/settings', 'Other' );
        $this->assertSame( array( 'audit' => array( 'Settings' ) ), self::renderGroups( array( 'audit' ), $groups, $urls ) );
        // with the tab in the top menu the Setup link audit is drawn itself and is taken by its key
        $groups[1]['items'][] = $item( 'audit', 'audit/settings', 'Audit' );
        $this->assertSame( array( 'audit' => array( 'Audit' ) ), self::renderGroups( array( 'audit' ), $groups, $urls ) );
    }

    /** MM-07 */
    public function testWithoutAChoiceEveryLinkShows()
    {
        $groups = self::fakeGroups( 5, 3 );
        $groups[0]['items'][4]['state'] = 'no-access';
        $shown = self::renderGroups( array(), $groups );
        $this->assertCount( 3, $shown );
        $this->assertSame( 15, count( call_user_func_array( 'array_merge', array_values( $shown ) ) ), 'no limit, and a link without access shows its name, as in the menu' );
    }

    /** MM-06 */
    public function testTheScriptOpensAndClosesTheMenu()
    {
        $node = trim( (string)shell_exec( 'command -v node 2>/dev/null' ) );
        if ( $node === '' )
            $this->markTestSkipped( 'node is not installed' );
        $out = (string)shell_exec( escapeshellarg( $node ) . ' ' . escapeshellarg( __DIR__ . '/js/mega_menu.js' ) . ' '
                                   . escapeshellarg( self::root() . '/design/admin4/javascript/a4_mega_menu.js' ) . ' 2>&1' );
        $s = array();
        foreach ( array_filter( explode( "\n", $out ) ) as $line )
        {
            $row = json_decode( $line, true );
            $this->assertIsArray( $row, 'the harness ran: ' . $out );
            $s[$row['name']] = $row;
        }
        $this->assertCount( 31, $s );

        $open = function ( $name ) use ( $s ) {
            $this->assertSame( 'true', $s[$name]['expanded'], $name );
            $this->assertTrue( $s[$name]['open'], $name );
            $this->assertTrue( $s[$name]['itemClass'], $name );
        };
        $closed = function ( $name ) use ( $s ) {
            $this->assertSame( 'false', $s[$name]['expanded'], $name );
            $this->assertFalse( $s[$name]['open'], $name );
        };

        // click, ArrowDown; one load, the links in place, the page shown marked
        $open( 'click opens and loads' );
        $this->assertSame( 1, $s['click opens and loads']['fetches'] );
        $this->assertSame( 'groups', $s['click opens and loads']['content'] );
        $this->assertNull( $s['click opens and loads']['busy'] );
        $this->assertSame( array( 'first link' ), $s['click opens and loads']['current'] );
        $this->assertNull( $s['click opens and loads']['allCurrent'] );
        $this->assertSame( 'page', $s['the overview link is marked on the overview']['allCurrent'] );
        $this->assertSame( array(), $s['the overview link is marked on the overview']['current'] );
        $closed( 'click again closes' );
        $open( 'second opening loads nothing more' );
        $this->assertSame( 1, $s['second opening loads nothing more']['fetches'] );
        $open( 'arrowdown opens and focuses the first link' );
        $this->assertSame( 'first link', $s['arrowdown opens and focuses the first link']['focus'] );

        // Escape, Tab, a click
        $closed( 'escape in the panel closes and focuses the button' );
        $this->assertSame( 'toggle', $s['escape in the panel closes and focuses the button']['focus'] );
        $closed( 'escape with the focus elsewhere closes and keeps it' );
        $this->assertSame( 'page link', $s['escape with the focus elsewhere closes and keeps it']['focus'] );
        $this->assertSame( array(), $s['escape while closed does nothing']['log'] );
        $closed( 'click outside closes' );
        $open( 'click inside keeps it' );
        $closed( 'tab out of it closes' );
        $open( 'tab inside keeps it' );

        // the hover preview: delayed in and out, mouse only, not while another menu is open
        $closed( 'hover 299ms opens nothing' );
        $open( 'hover 300ms opens the preview' );
        $open( 'leaving for 399ms keeps the preview' );
        $closed( 'leaving for 400ms closes the preview' );
        $open( 'back within the delay keeps the preview' );
        $closed( 'leaving before the delay opens nothing' );
        $this->assertSame( 0, $s['leaving before the delay opens nothing']['fetches'] );
        $open( 'a click keeps the preview open' );
        $open( 'focus coming into a preview keeps it open' );
        $this->assertSame( 'all link', $s['focus coming into a preview keeps it open']['focus'] );
        $closed( 'touch never previews' );
        $closed( 'a coarse pointer never previews' );
        $closed( 'no preview while another menu is open' );

        // the width
        $closed( 'below 1280px a click opens nothing' );
        $this->assertSame( 0, $s['below 1280px a click opens nothing']['fetches'] );
        $closed( 'getting narrower closes it' );
        // the "More" list of the header
        $closed( 'in the More list a click opens nothing' );
        $this->assertSame( 0, $s['in the More list a click opens nothing']['fetches'] );
        $closed( 'moving into the More list closes it' );

        // a failed load says so, is tried again, and leaves the overview link to go to
        foreach ( array( 'a failed load says so', 'a page in place of the fragment is no menu' ) as $name )
        {
            $open( $name );
            $this->assertSame( 'could not be loaded', $s[$name]['status'], $name );
            $this->assertNull( $s[$name]['busy'], $name );
        }
        $this->assertSame( 2, $s['a failed load is tried again']['fetches'] );
        $this->assertSame( 'all link', $s['arrowdown after a failed load focuses the overview link']['focus'] );
    }
}
