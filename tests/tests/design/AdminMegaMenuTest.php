<?php
/**
 * admin4's mega menu of the Setup and Users tabs (prototype, opt-in), checked on the files alone (no database):
 *
 *  MM-01 - off by default: menu.ini [TopAdminMenu] MegaMenuTabs[] is empty, the header draws the button and the page
 *          loads the script only while the setting names a tab
 *  MM-02 - the disclosure navigation pattern: the tab stays a link, a button beside it with aria-expanded,
 *          aria-controls and a name, a hidden panel of plain links with a heading per group; no role="menu"
 *  MM-03 - one source and the rights: the header carries no link of the panel (it sits in the header's cache block,
 *          keyed by the user's roles); the links come from user/megamenu/<tab>, a view without page layout that
 *          answers only for a tab of MegaMenuTabs[] with a MegaMenuTemplate that is open to the user (the topmenu
 *          operator's verdict), drawn from the tab's menu.ini section by parts/ini_menu.tpl, without figures
 *  MM-04 - from 1280px only, hidden in the "More" list, with forced-colors and reduced-motion rules
 *  MM-05 - the strings in ger-DE and eng-US
 *  MM-06 - the script: click, ArrowDown, Escape, Tab, a click outside, the delayed hover preview (300 ms in, 400 ms
 *          out, mouse only; the focus coming in keeps it), the width, the "More" list, one load and a failed one
 *          (a4_mega_menu.js run in node, tests/tests/design/js/mega_menu.js; skipped without node)
 *  MM-08 - the Users tab: [Topmenu_users] MegaMenuTemplate draws [Leftmenu_user_mega], which adds [Leftmenu_user] and
 *          [Leftmenu_my] instead of restating their links; its MegaMenuLinks[] names existing links with icons,
 *          descriptions and names in ger-DE and eng-US
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

    /** settings/menu.ini as [ section => [ key => value | [ key => value ] ] ], arrays without their reset line */
    private static function iniSections()
    {
        $out = array();
        $section = null;
        foreach ( explode( "\n", self::read( 'settings/menu.ini' ) ) as $line )
        {
            if ( preg_match( '/^\[([^\]]+)\]\s*$/', $line, $m ) )
            {
                $section = $m[1];
                $out[$section] = $out[$section] ?? array();
            }
            elseif ( $section !== null && preg_match( '/^([A-Za-z0-9_]+)\[([^\]]*)\]=(.*)$/', $line, $m ) )
            {
                if ( $m[2] === '' )
                    $out[$section][$m[1]][] = $m[3];
                else
                    $out[$section][$m[1]][$m[2]] = $m[3];
            }
            elseif ( $section !== null && preg_match( '/^([A-Za-z0-9_]+)=(.*)$/', $line, $m ) )
                $out[$section][$m[1]] = $m[2];
        }
        return $out;
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
        $this->assertStringContainsString( '#   MegaMenuTabs[]=users', $section );

        // the header asks for the setting, and for the user's access to the fragment, before it draws a button
        $top = self::read( 'design/admin4/templates/page_topmenu.tpl' );
        $check = strpos( $top, "ezini_hasvariable( 'TopAdminMenu', 'MegaMenuTabs', 'menu.ini' )" );
        $this->assertNotFalse( $check );
        $this->assertStringContainsString( "{foreach ezini( 'TopAdminMenu', 'MegaMenuTabs', 'menu.ini' ) as \$a4_mega_tab}", $top );
        $this->assertStringContainsString( "fetch( 'user', 'can_open', hash( 'uri', 'user/megamenu' ) )", $top );
        $this->assertStringContainsString( '$ui_context_edit|not', $top, 'no mega menu on edit pages' );
        // a tab named there takes part only with its MegaMenuTemplate; the header maps its navigation part to the tab
        $this->assertStringContainsString( "ezini_hasvariable( concat( 'Topmenu_', \$a4_mega_tab ), 'MegaMenuTemplate', 'menu.ini' )", $top );
        $this->assertLessThan( strpos( $top, '{foreach topmenu(' ), strpos( $top, "{set \$a4_mega_tabs = \$a4_mega_tabs|merge( hash( ezini( concat( 'Topmenu_', \$a4_mega_tab ), 'NavigationPartIdentifier', 'menu.ini' ), \$a4_mega_tab ) )}" ) );
        $this->assertStringNotContainsString( "|contains( 'setup' )", $top, 'no tab is named in the header' );
        // the item draws a button only for a tab the header chose, enabled and open to the user
        $item = self::read( 'design/admin4/templates/page_topmenuitem.tpl' );
        $this->assertStringContainsString( '{if is_set( $a4_mega_tabs )}', $item );
        $this->assertStringContainsString( 'is_set( $a4_mega_tabs[ $menu_item.navigationpart_identifier ] ), $menu_item.enabled, or( is_unset( $menu_item.access ), $menu_item.access )', $item );

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
        $this->assertStringContainsString( 'aria-controls="a4-mega-{$item_mega|wash}"', $tag );
        $this->assertStringContainsString( "aria-label=\"{'Open the %tab menu'|i18n( 'design/admin/pagelayout',, hash( '%tab', \$menu_item.name ) )|wash}\"", $tag );
        $this->assertStringContainsString( "data-a4-mega-src={concat( 'user/megamenu/', \$item_mega )|ezurl}", $tag );
        $this->assertStringContainsString( '<span class="a4-icon a4-bi-chevron-down" aria-hidden="true"></span>', $item );
        // the panel it controls: hidden, a status line until the links arrive, the link to the tab
        $this->assertStringContainsString( '<div id="a4-mega-{$item_mega|wash}" class="a4-mega-panel" hidden', $item );
        $this->assertStringContainsString( '<p class="a4-mega-status" role="status">', $item );
        // the way to the tab's own page, which stays when the links cannot be loaded
        $this->assertStringContainsString( "<a href={\$menu_item.url|ezurl}>{'Go to %tab'|i18n( 'design/admin/pagelayout',, hash( '%tab', \$menu_item.name ) )|wash}", $item );

        // the groups: a section with a heading each, plain links in lists; no menu roles anywhere
        $groups = self::read( 'design/admin4/templates/parts/a4_mega_menu.tpl' );
        $this->assertStringContainsString( '<section class="a4-mega-group" aria-labelledby=', $groups );
        $this->assertStringContainsString( '<h2 class="a4-mega-group-title" id=', $groups );
        $this->assertStringContainsString( '<ul class="a4-mega-links">', $groups );
        $this->assertStringContainsString( '<a href={$item.url|ezurl}{if $mega_text} aria-describedby=', $groups );
        foreach ( array( 'design/admin4/templates/page_topmenuitem.tpl', 'design/admin4/templates/parts/a4_mega_menu.tpl',
                         'design/admin4/templates/user/megamenu.tpl', 'design/admin4/javascript/a4_mega_menu.js' ) as $file )
        {
            $this->assertStringNotContainsString( 'role="menu', self::read( $file ), "$file: a disclosure of links, not a menu" );
            $this->assertStringNotContainsString( "'menuitem'", self::read( $file ), $file );
        }
    }

    /** MM-03 */
    public function testTheLinksAreLoadedFromTheTabsMenuSource()
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

        // the view: one for every tab, in the user module with the policy of any user of the siteaccess (the tabs belong
        // to different modules); ajax, without page layout and kept by no shared cache
        $module = self::read( 'kernel/user/module.php' );
        preg_match( "/\\\$ViewList\['megamenu'\] = array\((.*?)\);/s", $module, $mega );
        $this->assertNotEmpty( $mega, 'user/megamenu is a view' );
        $this->assertStringContainsString( "'functions' => array( 'login' )", $mega[1] );
        $this->assertStringContainsString( "'script' => 'megamenu.php'", $mega[1] );
        $this->assertStringContainsString( "'ui_context' => 'ajax'", $mega[1] );
        $this->assertStringContainsString( "'params' => array( 'Tab' )", $mega[1] );
        $this->assertFileDoesNotExist( self::root() . '/kernel/setup/megamenu.php', 'one view for every tab, no second one for Setup' );
        $this->assertStringNotContainsString( "\$ViewList['megamenu']", self::read( 'kernel/setup/module.php' ) );
        $view = self::read( 'kernel/private/classes/views/user/megamenu.php' );
        $this->assertStringContainsString( "\$Result['pagelayout'] = false;", $view );
        $this->assertStringContainsString( "header( 'Cache-Control: private, no-store' );", $view );
        $this->assertStringContainsString( "'debug-enabled' => false", $view );
        $this->assertStringContainsString( "\$tpl->fetch( 'design:user/megamenu.tpl' )", $view );
        // the tab is checked before anything is drawn: not a tab of the setting (not available), not open (denied)
        $fetch = strpos( $view, "\$tpl->fetch(" );
        $this->assertLessThan( $fetch, strpos( $view, 'return $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE' ) );
        $this->assertLessThan( $fetch, strpos( $view, 'return $Module->handleError( \eZError::KERNEL_ACCESS_DENIED' ) );
        $this->assertStringContainsString( 'new \eZTopMenuOperator()', $view, 'the header\'s own verdict on the tab' );
        $this->assertStringContainsString( "'Exponential\\\\View\\\\Kernel\\\\User\\\\Megamenu'", self::read( 'autoload/ezp_kernel.php' ) );
        $this->assertStringNotContainsString( 'Setup\\\\Megamenu', self::read( 'autoload/ezp_kernel.php' ) );
        $this->assertStringContainsString( '\Exponential\View\Kernel\User\Megamenu::main(', self::read( 'kernel/user/megamenu.php' ) );

        // the fragment: the tab's MegaMenuTemplate, which draws the tab's menu as the mega menu's groups
        $fragment = self::read( 'design/admin4/templates/user/megamenu.tpl' );
        $this->assertStringContainsString( '<div class="a4-mega-groups" data-a4-mega-groups>', $fragment );
        $this->assertStringContainsString( '{include uri=$mega_template}', $fragment );
        $this->assertStringNotContainsString( 'ezini(', $fragment, 'no second configuration' );
        $setup = self::read( 'design/admin4/templates/parts/setup/mega_menu.tpl' );
        $this->assertStringContainsString( "{include uri='design:parts/setup/menu.tpl' setup_menu_layout='tiles' setup_tiles_layout='mega'}", $setup );
        $this->assertStringContainsString( 'tiles_layout=$setup_tiles_layout', self::read( 'design/admin4/templates/parts/setup/menu.tpl' ) );
        $users = self::read( 'design/admin4/templates/parts/user/mega_menu.tpl' );
        $this->assertStringContainsString( "{include uri='design:parts/ini_menu.tpl' ini_section='Leftmenu_user_mega'", $users );
        $this->assertStringContainsString( "menu_layout='tiles'", $users );
        $this->assertStringContainsString( "tiles_layout='mega'", $users );
        // every tab with a template names one that exists in admin4
        $ini = self::iniSections();
        foreach ( array( 'setup' => 'parts/setup/mega_menu.tpl', 'users' => 'parts/user/mega_menu.tpl' ) as $tab => $file )
        {
            $this->assertSame( "design:$file", $ini["Topmenu_$tab"]['MegaMenuTemplate'] ?? null, "[Topmenu_$tab] MegaMenuTemplate" );
            $this->assertFileExists( self::root() . "/design/admin4/templates/$file" );
        }
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
        // a classic design gets an empty fragment instead of an error; the panel's link to the tab stays
        $this->assertStringContainsString( 'data-a4-mega-groups', self::read( 'design/standard/templates/user/megamenu.tpl' ) );
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
        $strings = array( 'Open the %tab menu' => 'Menü „%tab“ öffnen', 'Loading the %tab menu' => 'Menü „%tab“ wird geladen',
                          'The %tab menu could not be loaded.' => 'Das Menü „%tab“ konnte nicht geladen werden.',
                          'Go to %tab' => 'Weiter zu „%tab“' );
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
        $this->assertCount( 35, $s );

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

        // two tabs: one panel at a time, whatever opens the other (a click, the hover preview)
        $second = function ( $name, $open ) use ( $s ) {
            $this->assertSame( $open ? 'true' : 'false', $s[$name]['secondExpanded'], $name );
            $this->assertSame( $open, $s[$name]['secondOpen'], $name );
            $this->assertSame( $open, $s[$name]['secondItemClass'], $name );
        };
        $closed( 'two tabs: opening the second closes the first' );
        $second( 'two tabs: opening the second closes the first', true );
        $this->assertSame( 2, $s['two tabs: opening the second closes the first']['fetches'], 'each tab loads its own links' );
        $open( 'two tabs: opening the first again closes the second' );
        $second( 'two tabs: opening the first again closes the second', false );
        $closed( 'two tabs: a preview of the second closes the first' );
        $second( 'two tabs: a preview of the second closes the first', true );
        $closed( 'two tabs: each opens on its own' );
        $second( 'two tabs: each opens on its own', true );
    }

    /** menu.ini with the given values set in this process; undone in tearDown() */
    private function menuIni( array $values )
    {
        $ini = eZINI::instance( 'menu.ini' );
        foreach ( $values as $section => $keys )
            foreach ( $keys as $key => $value )
            {
                $this->iniBackup[] = array( $section, $key, $ini->hasVariable( $section, $key ) ? $ini->variable( $section, $key ) : null );
                $ini->setVariable( $section, $key, $value );
            }
        return $ini;
    }

    private $iniBackup = array();

    protected function tearDown(): void
    {
        $ini = eZINI::instance( 'menu.ini' );
        foreach ( array_reverse( $this->iniBackup ) as $entry )
        {
            if ( $entry[2] === null )
                $ini->removeSetting( $entry[0], $entry[1] );
            else
                $ini->setVariable( $entry[0], $entry[1], $entry[2] );
        }
        $this->iniBackup = array();
        parent::tearDown();
    }

    /** MM-03: the view answers only for a tab of MegaMenuTabs[] with a template of the design */
    public function testTheViewServesOnlyTheTabsSwitchedOn()
    {
        $view = 'Exponential\View\Kernel\User\Megamenu';
        $ini = $this->menuIni( array(
            'TopAdminMenu' => array( 'MegaMenuTabs' => array( 'setup', 'users', 'mmt_none', 'mmt_file', 'mmt_up' ) ),
            'Topmenu_mmt_none' => array( 'NavigationPartIdentifier' => 'mmtnone' ),
            'Topmenu_mmt_file' => array( 'NavigationPartIdentifier' => 'mmtfile', 'MegaMenuTemplate' => 'file:/etc/passwd' ),
            'Topmenu_mmt_up' => array( 'NavigationPartIdentifier' => 'mmtup', 'MegaMenuTemplate' => 'design:../../settings/x.tpl' ),
        ) );
        $this->assertSame( 'design:parts/setup/mega_menu.tpl', $view::templateFor( 'setup', $ini ) );
        $this->assertSame( 'design:parts/user/mega_menu.tpl', $view::templateFor( 'users', $ini ) );
        $this->assertNull( $view::templateFor( 'content', $ini ), 'a tab not named in MegaMenuTabs[]' );
        $this->assertNull( $view::templateFor( 'mmt_none', $ini ), 'a tab without MegaMenuTemplate' );
        $this->assertNull( $view::templateFor( 'mmt_file', $ini ), 'only a design: template' );
        $this->assertNull( $view::templateFor( 'mmt_up', $ini ), 'no path out of the design' );
        $this->assertNull( $view::templateFor( 'Setup', $ini ), 'the tab as named, nothing else' );
        $this->assertNull( $view::templateFor( '', $ini ) );

        $this->menuIni( array( 'TopAdminMenu' => array( 'MegaMenuTabs' => array() ) ) );
        $this->assertNull( $view::templateFor( 'setup', $ini ), 'off by default' );
    }

    /** MM-03: the view's verdict on the tab is the header's: shown, enabled and open to the user */
    public function testTheViewServesOnlyATabTheUserHas()
    {
        $view = 'Exponential\View\Kernel\User\Megamenu';
        // a tab without policies or address check (no database needed), then hidden, disabled, not shown, not listed
        $tab = array( 'NavigationPartIdentifier' => 'mmtnavigationpart', 'Name' => 'MMT', 'URL' => array( 'default' => 'mmt/view' ),
                      'Enabled' => array( 'default' => 'true' ), 'Shown' => array( 'default' => 'true' ) );
        $ini = $this->menuIni( array(
            'TopAdminMenu' => array( 'Tabs' => array( 'mmt' ), 'HiddenTabs' => array(), 'SetupTabs' => array() ),
            'MenuAccessSettings' => array( 'CheckViewAccess' => 'disabled' ),
            'Topmenu_mmt' => $tab,
        ) );
        $this->assertTrue( $view::tabIsOpen( 'mmt', $ini ) );

        $this->menuIni( array( 'TopAdminMenu' => array( 'HiddenTabs' => array( 'mmt' ) ) ) );
        $this->assertFalse( $view::tabIsOpen( 'mmt', $ini ), 'a hidden tab' );
        $this->menuIni( array( 'TopAdminMenu' => array( 'HiddenTabs' => array() ),
                               'Topmenu_mmt' => array( 'Enabled' => array( 'default' => 'true', 'navigation' => 'false' ) ) ) );
        $this->assertFalse( $view::tabIsOpen( 'mmt', $ini ), 'a disabled tab' );
        $this->menuIni( array( 'Topmenu_mmt' => array( 'Enabled' => array( 'default' => 'true' ), 'Shown' => array( 'default' => 'true', 'navigation' => 'false' ) ) ) );
        $this->assertFalse( $view::tabIsOpen( 'mmt', $ini ), 'a tab not shown' );
        $this->menuIni( array( 'Topmenu_mmt' => array( 'Shown' => array( 'default' => 'true' ) ), 'TopAdminMenu' => array( 'Tabs' => array() ) ) );
        $this->assertFalse( $view::tabIsOpen( 'mmt', $ini ), 'a tab not in Tabs[]' );
    }

    /** MM-08 */
    public function testTheUsersTabReusesTheMenusThatDefineItsLinks()
    {
        $ini = self::iniSections();
        $mega = $ini['Leftmenu_user_mega'];
        $this->assertSame( array( 'users', 'access', 'account' ), $mega['Groups'] );
        $this->assertSame( array( 'access' => 'Leftmenu_user', 'account' => 'Leftmenu_my' ), $mega['GroupSections'] );
        // its own links: only the two the tab has no menu entry for; the tab's page is the first
        $this->assertSame( array( 'accounts' => 'content/view/full/5', 'sessions' => 'setup/session' ), $mega['Links'] );
        $this->assertSame( $ini['Topmenu_users']['URL']['default'], $mega['Links']['accounts'] );
        $this->assertSame( $ini['Topmenu_users']['PolicyList'], $mega['PolicyList_accounts'] );
        $this->assertSame( $ini['Leftmenu_setup']['PolicyList_sessions'], $mega['PolicyList_sessions'], 'the policy of the Setup menu\'s link' );
        // the left menu of the tab is unchanged: its two links, no groups
        $this->assertSame( array( 'roles_and_policies' => 'role/list', 'unactivated' => 'user/unactivated' ), $ini['Leftmenu_user']['Links'] );
        $this->assertArrayNotHasKey( 'Groups', $ini['Leftmenu_user'] );
        $this->assertStringContainsString( "{include uri='design:parts/ini_menu.tpl' ini_section='Leftmenu_user' i18n_hash=", self::read( 'design/admin4/templates/parts/user/menu.tpl' ) );
    }

    /** MM-08 */
    public function testTheUsersChoiceNamesExistingLinksWithIconsAndStrings()
    {
        $ini = self::iniSections();
        $mega = $ini['Leftmenu_user_mega'];
        $choice = $mega['MegaMenuLinks'];
        $this->assertMatchesRegularExpression( '/^MegaMenuLinks\[\]$/m', substr( self::read( 'settings/menu.ini' ), strpos( self::read( 'settings/menu.ini' ), '[Leftmenu_user_mega]' ) ), 'the list is reset before it is set' );
        $this->assertGreaterThanOrEqual( 6, count( $choice ) );
        $this->assertLessThanOrEqual( 12, count( $choice ) );
        $this->assertSame( array_values( array_unique( $choice ) ), $choice );

        preg_match_all( '/^IconList\[\]=(\S+)$/m', self::read( 'settings/design.ini' ), $icons );
        $hash = self::read( 'design/admin4/templates/parts/user/mega_menu.tpl' );
        $strings = array();
        foreach ( $choice as $key )
        {
            // an own link, or <group>-<key> of the section the group adds
            if ( strpos( $key, '-' ) === false )
            {
                $section = 'Leftmenu_user_mega';
                $link = $key;
                $this->assertContains( $key, $mega['GroupLinks_users'] ?? array(), "$key is in a group" );
            }
            else
            {
                list( $group, $link ) = explode( '-', $key, 2 );
                $this->assertArrayHasKey( $group, $mega['GroupSections'], "MegaMenuLinks[]=$key: no such group adds a section" );
                $section = $mega['GroupSections'][$group];
            }
            $this->assertArrayHasKey( $link, $ini[$section]['Links'] ?? array(), "MegaMenuLinks[]=$key is no link of [$section]" );
            $icon = $ini[$section]['LinkIcons'][$link] ?? null;
            $this->assertNotNull( $icon, "[$section] LinkIcons[$link]" );
            $this->assertContains( $icon, $icons[1], "LinkIcons[$link]=$icon is not in design.ini IconList[]" );
            $this->assertStringStartsWith( '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-' . $icon . '"',
                                           (string)file_get_contents( self::root() . "/share/icons/bootstrap-icons/svg/$icon.svg" ), "$icon.svg" );
            $text = $ini[$section]['LinkDescriptions'][$link] ?? null;
            $this->assertNotNull( $text, "[$section] LinkDescriptions[$link]" );
            // the description is translated in its section's context, the name comes from the template's i18n_hash
            $strings[] = array( 'design/admin/parts/' . explode( '_', $section )[1] . '/menu', $text );
            $this->assertMatchesRegularExpression( "/^\s*'" . preg_quote( $key, '/' ) . "',\s+'([^']+)'\|i18n\( '([^']+)' \),$/m", $hash, "the name of $key" );
            preg_match( "/^\s*'" . preg_quote( $key, '/' ) . "',\s+'([^']+)'\|i18n\( '([^']+)' \),$/m", $hash, $name );
            $strings[] = array( $name[2], $name[1] );
        }
        foreach ( $mega['GroupNames'] as $name )
            $strings[] = array( 'design/admin/parts/user/menu', $name );
        foreach ( array( 'ger-DE', 'eng-US' ) as $language )
        {
            $dom = new DOMDocument();
            $this->assertTrue( $dom->load( self::root() . "/share/translations/$language/translation.ts" ) );
            $xpath = new DOMXPath( $dom );
            foreach ( $strings as list( $context, $source ) )
            {
                $found = $xpath->query( "//context[name='$context']/message[source=" . self::xpathString( $source ) . ']/translation' );
                $this->assertGreaterThanOrEqual( 1, $found->length, "$language, $context: $source" );
                $this->assertNotSame( '', trim( $found->item( 0 )->textContent ), "$language, $context: $source" );
            }
        }
    }
}
