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
 *  MM-04 - from 576px (the one-line main menu, also 960px at 200% zoom), not in the phone menu, hidden in the "More" list, with forced-colors and reduced-motion rules
 *  MM-05 - the strings in ger-DE and eng-US
 *  MM-06 - the script: click, ArrowDown, Escape, Tab, a click outside, the delayed hover preview (300 ms in, 400 ms
 *          out, mouse only; the focus coming in keeps it), the width, the "More" list, one load and a failed one
 *          (a4_mega_menu.js run in node, tests/tests/design/js/mega_menu.js; skipped without node)
 *  MM-09 - the load of the links: one request, retried once after a passing failure, aborts are no error, the
 *          signed-out state, "Try again", the fragment marker, one binding (js/mega_menu_load.js on mini_dom.js)
 *  MM-08 - the Users tab: [Topmenu_users] MegaMenuTemplate draws [Leftmenu_user_mega], which adds [Leftmenu_user] and
 *          [Leftmenu_my] instead of restating their links; its MegaMenuLinks[] names existing links with icons,
 *          descriptions and names in ger-DE and eng-US
 *  MM-07 - curated: menu.ini [Leftmenu_setup] MegaMenuLinks[] names at most 19 existing links; the panel's template
 *          shows only those, in that order, the user may open, each in its group, groups without one left out, at most
 *          19; with no MegaMenuLinks[] every link (parts/a4_mega_menu.tpl rendered with eZTemplate, no database)
 *  MM-08 - with a tab moved into Setup (GroupSections[]) a chosen key whose link is not drawn takes the group's link
 *          of the same address (audit: the audit group's Settings); audit-<key> chooses a group link directly
 *  MM-10 - the Dashboard tab: [Topmenu_dashboard] MegaMenuTemplate draws [Leftmenu_my_mega], whose groups add
 *          [Leftmenu_my], [Leftmenu_audit] and [Leftmenu_design] (a group may add several sections, separated by ";");
 *          its MegaMenuLinks[] names at most 18 links, every link of Exponential Intelligence among them, with icons,
 *          descriptions and names in every complete catalogue; a link the user may not open or of an inactive
 *          extension is left out; MegaMenuLayout=wide sets the panel in up to four balanced columns
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminMegaMenuTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminNodeBinary.php';

require_once __DIR__ . '/AdminFiles.php';

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
        $this->assertStringContainsString( '#   MegaMenuTabs[]=dashboard', $section );

        // the header asks for the setting, and for the user's access to the fragment, before it draws a button
        // one template draws the tabs for every header: admin4's page_topmenu.tpl and admin4l's tab menu block
        foreach ( array( 'design/admin4/templates/page_topmenu.tpl', 'design/admin4l/templates/explayouts/block/admin_tab_menu.tpl' ) as $header )
        {
            $this->assertStringContainsString( "{include uri='design:parts/a4_topmenu_items.tpl' navigationpart_identifier=\$a4_current_part|trim}", self::read( $header ), $header );
            $this->assertStringNotContainsString( '{foreach topmenu(', self::read( $header ), "$header draws no tabs of its own" );
        }
        $top = self::read( 'design/admin4/templates/parts/a4_topmenu_items.tpl' );
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
        $this->assertStringContainsString( "aria-label=\"{'%tab menu'|i18n( 'design/admin/pagelayout',, hash( '%tab', \$menu_item.name ) )|wash}\"", $tag );
        $this->assertStringContainsString( "data-a4-mega-src={concat( 'user/megamenu/', \$item_mega )|ezurl}", $tag );
        $this->assertStringContainsString( '<span class="a4-icon a4-bi-chevron-down" aria-hidden="true"></span>', $item );
        // the panel it controls: hidden, a status line until the links arrive, the link to the tab
        $this->assertStringContainsString( '<div id="a4-mega-{$item_mega|wash}" class="a4-mega-panel{if ezini_hasvariable( concat( \'Topmenu_\', $item_mega ), \'MegaMenuLayout\', \'menu.ini\' )}', $item );
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
        // a bare answer for a script, whatever the verdict: no page layout, no debug report, private, its own end
        $this->assertStringNotContainsString( "\$Result['pagelayout']", $view );
        $this->assertStringNotContainsString( 'handleError(', $view, 'no error page in the page layout' );
        $this->assertStringContainsString( "header( 'Cache-Control: private, no-store' );", $view );
        $this->assertStringContainsString( "header( 'Vary: Cookie' );", $view );
        $this->assertStringContainsString( "header( 'X-Exp-Mega: ' . \$verdict );", $view );
        $this->assertStringContainsString( "'debug-enabled' => false", $view );
        $this->assertStringContainsString( "\$tpl->fetch( 'design:user/megamenu.tpl' )", $view );
        $this->assertStringContainsString( '\eZExecution::cleanExit();', $view );
        // cleanExit() ends a Velocity request by throwing: never inside a catch
        $this->assertDoesNotMatchRegularExpression( '/\bcatch\s*\(/', $view );
        // the debug report is off and the session released before anything is drawn, the verdict made before both
        $fetch = strpos( $view, "\$tpl->fetch(" );
        $this->assertLessThan( $fetch, strpos( $view, "'debug-enabled' => false" ) );
        $this->assertLessThan( $fetch, strpos( $view, 'self::releaseSession();' ) );
        $this->assertLessThan( strpos( $view, 'self::releaseSession();' ), strpos( $view, '$verdict = self::verdict(' ) );
        $this->assertStringContainsString( 'new \eZTopMenuOperator()', $view, 'the header\'s own verdict on the tab' );
        $this->assertStringContainsString( '$user->canLoginToSiteAccess( $access )', $view, 'the kernel\'s user/login check, made by the view' );
        // every visitor reaches the view, which makes the checks itself (a refusal is a 403 for the script, not a page)
        $site = self::read( 'settings/site.ini' );
        $this->assertStringContainsString( "\nAnonymousAccessList[]=user/megamenu\n", $site );
        $this->assertStringContainsString( "\nPolicyOmitList[]=user/megamenu\n", $site );
        $this->assertStringContainsString( "'Exponential\\\\View\\\\Kernel\\\\User\\\\Megamenu'", self::read( 'autoload/ezp_kernel.php' ) );
        $this->assertStringNotContainsString( 'Setup\\\\Megamenu', self::read( 'autoload/ezp_kernel.php' ) );
        $this->assertStringContainsString( '\Exponential\View\Kernel\User\Megamenu::main(', self::read( 'kernel/user/megamenu.php' ) );

        // the fragment: the tab's MegaMenuTemplate, which draws the tab's menu as the mega menu's groups
        $fragment = self::read( 'design/admin4/templates/user/megamenu.tpl' );
        $this->assertStringContainsString( '<div class="a4-mega-groups" data-a4-mega-groups data-a4-mega-fragment="{$mega_tab|wash}">', $fragment );
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
        $this->assertStringContainsString( "{include uri='design:parts/a4_mega_menu.tpl' groups=\$ini_menu_groups", $menu );
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
    public function testItShowsWhereTheMainMenuIsOneLine()
    {
        $css = self::css();
        $outside = preg_replace( '/@media[^{]*\{(?:[^{}]*\{[^{}]*\})*[^{}]*\}/s', '', $css );
        $this->assertMatchesRegularExpression( '/\.a4-mega-toggle,\s*\.a4-mega-panel \{ display: none; \}/', $outside, 'hidden below 576px (the phone menu)' );
        $wide = self::media( $css, 'screen and (min-width: 576px)' );
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
        $forced = self::media( $css, 'screen and (min-width: 576px) and (forced-colors: active)' );
        $this->assertStringContainsString( '.a4-mega-panel { border-color: CanvasText; }', $forced );
        $this->assertStringContainsString( '.a4-mega-toggle .a4-icon { transition: none; }', self::media( $css, '(prefers-reduced-motion: reduce)' ) );
        // the script agrees on the width
        $this->assertStringContainsString( "window.matchMedia('(min-width: 576px)')", self::read( 'design/admin4/javascript/a4_mega_menu.js' ) );
    }

    /** MM-05 */
    public function testTheStringsAreTranslated()
    {
        $strings = array( '%tab menu' => 'Menü „%tab“', 'Loading the %tab menu' => 'Menü „%tab“ wird geladen',
                          'The %tab menu could not be loaded.' => 'Das Menü „%tab“ konnte nicht geladen werden.',
                          'Go to %tab' => 'Weiter zu „%tab“', 'Try again' => 'Erneut versuchen',
                          'You are signed out. Sign in again to see the %tab menu.' => 'Sie sind abgemeldet. Melden Sie sich erneut an, um das Menü „%tab“ zu sehen.',
                          'Sign in' => 'Anmelden' );
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
        $this->assertLessThanOrEqual( 19, count( $choice ), 'at most 19 links' );
        $this->assertSame( array_values( array_unique( $choice ) ), $choice );
        // every key is a link of the menu or of one of its groups (the extensions' links are named in GroupLinks_*)
        preg_match_all( '/^(?:Links\[([a-z0-9_]+)\]=|GroupLinks_[a-z_]+\[\]=([a-z0-9_]+)$)/m', $section, $known );
        $known = array_filter( array_merge( $known[1], $known[2] ) );
        foreach ( $choice as $key )
            $this->assertContains( $key, $known, "MegaMenuLinks[]=$key is no link of [Leftmenu_setup]" );
        // the AI and Updates groups are in the choice, shown when their extensions are active
        foreach ( array( 'expai_summary', 'expai_models', 'expai_budgets', 'ezupdate', 'git_manager' ) as $key )
            $this->assertContains( $key, $choice );
        // the owner's additions of 6.0.15: Collected information (Content model), RSS, Preload sites and Packages (Site and URLs), after Sections
        $this->assertSame( array( 'classes', 'sections', 'collected', 'rss', 'preload', 'packages' ), array_slice( $choice, 0, 6 ) );
        $this->assertSame( array( 'audit', 'oauth_list' ), array_slice( $choice, 6, 2 ), 'oAuth admin follows Audit in Users and messages' );
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
    public function testAtMostNineteenLinksShow()
    {
        $groups = self::fakeGroups( 7, 3 );
        $keys = array();
        foreach ( $groups as $group )
            foreach ( $group['items'] as $item )
                $keys[] = $item['key'];
        $shown = self::renderGroups( $keys, $groups );
        $this->assertSame( 19, count( call_user_func_array( 'array_merge', array_values( $shown ) ) ) );
        $this->assertSame( array( 'L3_1', 'L3_2', 'L3_3', 'L3_4', 'L3_5' ), $shown['g3'], 'the first nineteen chosen' );
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
        $node = AdminNodeBinary::require();
        $out = (string)shell_exec( escapeshellarg( $node ) . ' ' . escapeshellarg( __DIR__ . '/js/mega_menu.js' ) . ' '
                                   . escapeshellarg( self::root() . '/design/admin4/javascript/a4_mega_menu.js' ) . ' 2>&1' );
        $s = array();
        foreach ( array_filter( explode( "\n", $out ) ) as $line )
        {
            $row = json_decode( $line, true );
            $this->assertIsArray( $row, 'the harness ran: ' . $out );
            $s[$row['name']] = $row;
        }
        $this->assertCount( 40, $s );

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
        $closed( 'below 576px a click opens nothing' );
        $this->assertSame( 0, $s['below 576px a click opens nothing']['fetches'] );
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
        // a server error is tried once more (2 requests) before the panel says so; the next opening loads again
        $this->assertSame( 2, $s['a failed load says so']['fetches'] );
        $this->assertSame( 3, $s['a failed load is tried again']['fetches'] );
        $this->assertSame( 'all link', $s['arrowdown after a failed load focuses the overview link']['focus'] );
        // a request without an answer is given up after 10 s and tried once more: busy until then, the menu when the
        // second answers, the error when it does not either, and the next opening loads again
        $this->assertSame( 'true', $s['a load without an answer is still busy before 10s']['busy'] );
        $this->assertNull( $s['a load without an answer is still busy before 10s']['status'] );
        $this->assertSame( 'groups', $s['a load without an answer is tried once more']['content'] );
        $this->assertSame( 2, $s['a load without an answer is tried once more']['fetches'] );
        $this->assertSame( 'could not be loaded', $s['a load without an answer twice says so']['status'] );
        $this->assertNull( $s['a load without an answer twice says so']['busy'] );
        $this->assertSame( 3, $s['a load without an answer is tried again']['fetches'] );
        $this->assertSame( 'groups', $s['a load without an answer is tried again']['content'] );

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
        // a panel opened on purpose is not replaced by the hover preview of another tab
        $open( 'two tabs: hovering the second keeps the first opened by a click' );
        $second( 'two tabs: hovering the second keeps the first opened by a click', false );
    }

    /**
     * MM-09 - the load of the links (#577): one request per tab and page with the session's cookie, never from the
     * HTTP cache; a request the browser stops is no error; a passing failure (no connection, 5xx, no answer, not the
     * menu) is tried once more; 401, 403 or a sign-in page show the signed-out state with a link to sign in; a
     * failure for good offers "Try again" and keeps the link to the tab's page; the menu is the fragment's marked
     * element (data-a4-mega-fragment); the script run twice binds once. a4_mega_menu.js in node against mini_dom.js.
     */
    public function testTheLoadOfTheLinksSurvivesFailures()
    {
        $node = AdminNodeBinary::require();
        $out = (string)shell_exec( escapeshellarg( $node ) . ' ' . escapeshellarg( __DIR__ . '/js/mega_menu_load.js' ) . ' '
                                   . escapeshellarg( self::root() . '/design/admin4/javascript/a4_mega_menu.js' ) . ' 2>&1' );
        $s = array();
        foreach ( array_filter( explode( "\n", $out ) ) as $line )
        {
            $row = json_decode( $line, true );
            $this->assertIsArray( $row, 'the harness ran: ' . $out );
            $s[$row['name']] = $row;
        }
        $this->assertCount( 19, $s, $out );

        $menu = function ( array $state, $requests, $name ) {
            $this->assertTrue( $state['menu'], $name );
            $this->assertSame( $requests, $state['requests'], $name );
            $this->assertNull( $state['busy'], $name );
            $this->assertNull( $state['retry'], $name );
            $this->assertTrue( $state['open'], $name );
        };
        $menu( $s['success'], 1, 'success' );
        $this->assertSame( '/admin/user/megamenu/setup', $s['success']['url'] );
        $this->assertSame( 'same-origin', $s['success']['credentials'] );
        $this->assertSame( 'no-store', $s['success']['cache'] );
        $this->assertSame( 'text/html', $s['success']['accept'] );
        $this->assertSame( 'XMLHttpRequest', $s['success']['xrw'] );
        $menu( $s['success is cached'], 1, 'success is cached' );

        // stopped by the browser: no error, the loading line; the next opening loads again
        $during = $s['abort is no error']['during'];
        $this->assertSame( 'LOADING', $during['status'] );
        $this->assertNull( $during['retry'] );
        $this->assertNull( $during['busy'] );
        $menu( $s['abort is no error']['after'], 2, 'abort: next opening' );
        $menu( $s['abort is tried again'], 2, 'abort is tried again' );
        // the page being left by another link: the network error of the stopped request (Firefox) is no error either
        $during = $s['navigation network error is no error']['during'];
        $this->assertSame( 'LOADING', $during['status'] );
        $this->assertNull( $during['retry'] );
        $this->assertSame( 1, $during['requests'], 'not tried again while the page is left' );
        $menu( $s['navigation network error is no error']['after'], 2, 'navigation network error: next opening' );

        // one request in flight, closing cancels nothing
        $this->assertSame( 'true', $s['in-flight request is reused']['closed']['busy'] );
        $menu( $s['in-flight request is reused']['after'], 1, 'in-flight request is reused' );

        // a passing failure is tried once more after 600 ms, without showing the error in between
        $this->assertSame( 'LOADING', $s['retry then success']['waiting']['status'] );
        $this->assertSame( 1, $s['retry then success']['waiting']['requests'] );
        $menu( $s['retry then success']['after'], 2, 'retry then success' );
        $menu( $s['timeout then success'], 2, 'timeout then success' );

        // a failure for good: the error, "Try again" (kept under the click, disabled while it loads), the tab's link
        $failed = $s['failure offers try again'];
        $this->assertSame( 'ERROR', $failed['failed']['status'] );
        $this->assertSame( 'TRY AGAIN', $failed['failed']['retry'] );
        $this->assertTrue( $failed['failed']['goTo'] );
        $this->assertTrue( $failed['clicked']['open'], 'the click on "Try again" is not one outside' );
        $this->assertTrue( $failed['clicked']['retryDisabled'] );
        $menu( $failed['after'], 3, 'try again' );
        foreach ( array( 'no marker is no menu' => 2, 'not found is not retried' => 1 ) as $name => $requests )
        {
            $this->assertSame( 'ERROR', $s[$name]['status'], $name );
            $this->assertSame( 'TRY AGAIN', $s[$name]['retry'], $name );
            $this->assertSame( $requests, $s[$name]['requests'], $name );
            $this->assertTrue( $s[$name]['goTo'], $name );
        }
        $this->assertSame( 'LOADING', $s['reopening after a failure loads again']['status'] );
        $this->assertSame( 2, $s['reopening after a failure loads again']['requests'] );

        // signed out: not tried again, a link to sign in instead of the error
        $this->assertSame( 'ERROR', $s['denied verdict is no sign-in']['status'], 'X-Exp-Mega: denied' );
        $this->assertNull( $s['denied verdict is no sign-in']['signin'] );
        $this->assertSame( 1, $s['denied verdict is no sign-in']['requests'] );
        foreach ( array( 'signed out 403', 'signed out 401', 'signed out verdict', 'signed out redirect', 'signed out page' ) as $name )
        {
            $this->assertSame( 'SIGNED OUT', $s[$name]['status'], $name );
            $this->assertSame( array( 'text' => 'SIGN IN', 'href' => '/admin/user/login' ), $s[$name]['signin'], $name );
            $this->assertNull( $s[$name]['retry'], $name );
            $this->assertSame( 1, $s[$name]['requests'], $name );
            $this->assertTrue( $s[$name]['goTo'], $name );
        }

        // the script twice on the page: one binding, one request, the click opens
        $menu( $s['double init'], 1, 'double init' );
        $this->assertSame( 'true', $s['double init']['expanded'] );
        $this->assertTrue( $s['double init']['bound'] );

        // the panel carries the texts and the sign-in address the script shows
        $item = self::read( 'design/admin4/templates/page_topmenuitem.tpl' );
        foreach ( array( 'data-a4-mega-retry=', 'data-a4-mega-signed-out=', 'data-a4-mega-signin=', "data-a4-mega-login={'user/login'|ezurl}" ) as $attribute )
            $this->assertStringContainsString( $attribute, $item );
        $this->assertStringContainsString( 'data-a4-mega-fragment="{$mega_tab|wash}"', self::read( 'design/standard/templates/user/megamenu.tpl' ) );
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

    /** Renders page_topmenuitem.tpl for one tab with the header's hash of mega menu tabs; returns the HTML */
    private static function renderItem( array $item, array $megaTabs )
    {
        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'menu_item', $item + array( 'url' => 'x', 'name' => 'X', 'tooltip' => '', 'position' => 'middle',
                                                       'enabled' => true, 'access' => true ) );
        $tpl->setVariable( 'navigationpart_identifier', '' );
        $tpl->setVariable( 'a4_mega_tabs', $megaTabs );
        return $tpl->fetch( 'file:design/admin4/templates/page_topmenuitem.tpl' );
    }

    /** MM-02: the item, rendered: a button and panel per tab the header names, none for the others */
    public function testEachTabGetsItsOwnButtonAndPanel()
    {
        $tabs = array( 'ezusernavigationpart' => 'users', 'ezsetupnavigationpart' => 'setup' );
        $users = self::renderItem( array( 'navigationpart_identifier' => 'ezusernavigationpart', 'url' => 'content/view/full/5', 'name' => 'Users' ), $tabs );
        $this->assertStringContainsString( '<li class="a4-mega-item">', $users );
        $this->assertMatchesRegularExpression( '#<button type="button" class="a4-mega-toggle" aria-expanded="false" aria-controls="a4-mega-users"#', $users );
        $this->assertMatchesRegularExpression( '#data-a4-mega-src="[^"]*/user/megamenu/users"#', $users );
        $this->assertStringContainsString( '<div id="a4-mega-users" class="a4-mega-panel" hidden', $users );
        $this->assertMatchesRegularExpression( '#aria-label="[^"]*Users[^"]*"#', $users, 'the button names the tab' );
        $this->assertMatchesRegularExpression( '#<p class="a4-mega-all"><a href="[^"]*/content/view/full/5">#', $users, 'the way to the tab\'s page' );

        $setup = self::renderItem( array( 'navigationpart_identifier' => 'ezsetupnavigationpart', 'url' => 'setup/menu', 'name' => 'Setup' ), $tabs );
        $this->assertMatchesRegularExpression( '#data-a4-mega-src="[^"]*/user/megamenu/setup"#', $setup );
        $this->assertStringContainsString( 'aria-controls="a4-mega-setup"', $setup );

        // a tab the header does not name, one the user may not open, a disabled one, and no hash at all: no button
        foreach ( array( self::renderItem( array( 'navigationpart_identifier' => 'ezcontentnavigationpart' ), $tabs ),
                         self::renderItem( array( 'navigationpart_identifier' => 'ezusernavigationpart', 'access' => false ), $tabs ),
                         self::renderItem( array( 'navigationpart_identifier' => 'ezusernavigationpart', 'enabled' => false ), $tabs ),
                         self::renderItem( array( 'navigationpart_identifier' => 'ezusernavigationpart' ), array() ) ) as $html )
        {
            $this->assertStringNotContainsString( 'a4-mega', $html );
        }
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

    /**
     * MM-03: run() itself refuses the anonymous user for a tab that is switched on with a bare 403 (X-Exp-Mega:
     * signed-out), and a tab that is not with a 404 (unknown), before it looks at the user; never a page, never a body
     */
    public function testTheViewRefusesTheAnonymousUser()
    {
        $this->menuIni( array( 'TopAdminMenu' => array( 'MegaMenuTabs' => array( 'setup' ) ) ) );
        $key = 'eZUserGlobalInstance_';
        $saved = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
        $GLOBALS[$key] = new eZUser( array( 'contentobject_id' => eZUser::anonymousId(), 'login' => 'anonymous', 'email' => '' ) );
        try
        {
            $this->assertFalse( eZUser::currentUser()->isRegistered(), 'the stand-in is the anonymous user' );
            $view = self::capturingView();
            $this->assertSame( array( 'signed-out', 403, '' ), $view->run( array( 'Params' => array( 'Module' => null, 'Tab' => 'setup' ) ) ) );
            $this->assertSame( array( 'unknown', 404, '' ), $view->run( array( 'Params' => array( 'Module' => null, 'Tab' => 'content' ) ) ) );
            $this->assertSame( array( 'unknown', 404, '' ), $view->run( array( 'Params' => array( 'Module' => null ) ) ), 'no tab' );
        }
        finally
        {
            if ( $saved === null )
                unset( $GLOBALS[$key] );
            else
                $GLOBALS[$key] = $saved[0];
        }
    }

    /** MM-03: the verdict, in its order: the tab, the sign-in, the siteaccess, the tab open to the user */
    public function testTheVerdictChecksTheTabTheSignInTheSiteaccessAndTheTabsAccess()
    {
        $view = 'Exponential\View\Kernel\User\Megamenu';
        $tab = array( 'NavigationPartIdentifier' => 'mmtnavigationpart', 'Name' => 'MMT', 'URL' => array( 'default' => 'mmt/view' ),
                      'Enabled' => array( 'default' => 'true' ), 'Shown' => array( 'default' => 'true' ) );
        $ini = $this->menuIni( array(
            'TopAdminMenu' => array( 'Tabs' => array( 'mmt' ), 'HiddenTabs' => array(), 'SetupTabs' => array() ),
            'MenuAccessSettings' => array( 'CheckViewAccess' => 'disabled' ),
            'Topmenu_mmt' => $tab,
        ) );
        $template = 'design:parts/setup/mega_menu.tpl';
        $anonymous = new eZUser( array( 'contentobject_id' => eZUser::anonymousId(), 'login' => 'anonymous', 'email' => '' ) );
        $member = self::standInUser( true );
        $outsider = self::standInUser( false );
        $access = array( 'name' => 'admin' );

        $this->assertSame( 'unknown', $view::verdict( 'mmt', null, $member, $access, $ini ), 'a tab the view does not serve, whoever asks' );
        $this->assertSame( 'unknown', $view::verdict( 'mmt', null, $anonymous, $access, $ini ) );
        $this->assertSame( 'signed-out', $view::verdict( 'mmt', $template, $anonymous, $access, $ini ) );
        $this->assertSame( 'denied', $view::verdict( 'mmt', $template, $outsider, $access, $ini ), 'no user/login for this siteaccess' );
        $this->assertSame( 'ok', $view::verdict( 'mmt', $template, $member, $access, $ini ) );
        $this->menuIni( array( 'TopAdminMenu' => array( 'HiddenTabs' => array( 'mmt' ) ) ) );
        $this->assertSame( 'denied', $view::verdict( 'mmt', $template, $member, $access, $ini ), 'a tab not open to the user' );

        // each verdict has its status: the fragment 200, a tab not served 404, every refusal 403
        $this->assertSame( array( 'ok' => 200, 'unknown' => 404, 'signed-out' => 403, 'denied' => 403 ), $view::STATUS );
    }

    /** MM-03: the fragment loses the templates' indentation between its tags, nothing else */
    public function testTheFragmentIsSentWithoutTheTemplatesIndentation()
    {
        $view = 'Exponential\View\Kernel\User\Megamenu';
        $html = "\n  <div class=\"a4-mega-groups\" data-a4-mega-groups>\n\n      <section>\n        <h2>Setup  menu</h2>\n"
              . "        <a href=\"/admin/setup/cache\">Caches</a>\n      </section>\n  </div>\n";
        $this->assertSame( '<div class="a4-mega-groups" data-a4-mega-groups> <section> <h2>Setup  menu</h2> <a href="/admin/setup/cache">Caches</a> </section> </div>',
                           $view::compact( $html ) );
        // an element whose white space counts keeps the fragment as it is
        $pre = "<div data-a4-mega-groups>\n  <pre>a\n  b</pre>\n</div>";
        $this->assertSame( $pre, $view::compact( "\n" . $pre . "\n" ) );
        $this->assertSame( '', $view::compact( '' ) );
    }

    /** the view with send() answering its arguments instead of ending the request */
    private static function capturingView()
    {
        return new class extends \Exponential\View\Kernel\User\Megamenu {
            protected function send( $verdict, $body )
            {
                return array( $verdict, self::STATUS[$verdict], $body );
            }
        };
    }

    /** a signed-in user (no database) who may, or may not, log in to the siteaccess */
    private static function standInUser( $mayLogIn )
    {
        $user = new class( array( 'contentobject_id' => 987654321, 'login' => 'mmtuser', 'email' => '' ) ) extends eZUser {
            public $mayLogIn = true;
            public function isRegistered() { return true; }
            public function canLoginToSiteAccess( $access ) { return $this->mayLogIn; }
        };
        $user->mayLogIn = $mayLogIn;
        return $user;
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
        // the left menu of the tab: its links (Check user accounts since user/hygiene), no groups
        $this->assertSame( array( 'roles_and_policies' => 'role/list', 'unactivated' => 'user/unactivated', 'user_hygiene' => 'user/hygiene' ),
                           $ini['Leftmenu_user']['Links'] );
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
        $this->assertLessThanOrEqual( 19, count( $choice ) );
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
                                           AdminFiles::read( self::root() . "/share/icons/bootstrap-icons/svg/$icon.svg" ), "$icon.svg" );
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

    /** MM-09: what the settings name and the menus cannot use is ignored, and named in the debug report */
    public function testIgnoredSettingsAreNamedInTheDebugReport()
    {
        // the check is included where the script is loaded, so only while a tab is named
        $head = self::read( 'design/admin4/templates/page_head_script.tpl' );
        $this->assertLessThan( strpos( $head, "{include uri='design:parts/a4_mega_menu_check.tpl'}" ),
                               strpos( $head, "{ezscript_load( array( 'a4_mega_menu.js' ) )}" ) );
        // each tab's section is named for the check
        $ini = self::read( 'settings/menu.ini' );
        $this->assertStringContainsString( "MegaMenuSection=Leftmenu_setup", $ini );
        $this->assertStringContainsString( "MegaMenuSection=Leftmenu_user_mega", $ini );

        // the sections as in settings/menu.ini, set here so the check does not depend on a cached menu.ini
        $this->menuIni( array(
            'TopAdminMenu' => array( 'MegaMenuTabs' => array( 'setup', 'users', 'mmt_unknown' ) ),
            'Topmenu_setup' => array( 'NavigationPartIdentifier' => 'ezsetupnavigationpart', 'MegaMenuTemplate' => 'design:parts/setup/mega_menu.tpl' ),
            'Topmenu_users' => array( 'NavigationPartIdentifier' => 'ezusernavigationpart', 'MegaMenuTemplate' => 'design:parts/user/mega_menu.tpl',
                                      'MegaMenuSection' => 'Leftmenu_user_mega' ),
            'Leftmenu_user' => array( 'Links' => array( 'roles_and_policies' => 'role/list', 'unactivated' => 'user/unactivated' ) ),
            'Leftmenu_user_mega' => array( 'Links' => array( 'accounts' => 'content/view/full/5', 'sessions' => 'setup/session' ),
                                           'GroupSections' => array( 'access' => 'Leftmenu_user' ),
                                           'MegaMenuLinks' => array( 'accounts', 'access-roles_and_policies', 'mmt_nolink', 'access-mmt_nolink', 'mmtgroup-x' ) ),
        ) );
        $messages = self::debugMessagesOf( 'file:design/admin4/templates/parts/a4_mega_menu_check.tpl' );
        $text = implode( "\n", $messages );
        $this->assertStringContainsString( 'MegaMenuTabs[]=mmt_unknown: no [Topmenu_mmt_unknown] MegaMenuTemplate', $text );
        $this->assertStringContainsString( '[Leftmenu_user_mega] MegaMenuLinks[]=mmt_nolink:', $text );
        $this->assertStringContainsString( '[Leftmenu_user_mega] MegaMenuLinks[]=access-mmt_nolink:', $text );
        $this->assertStringContainsString( '[Leftmenu_user_mega] MegaMenuLinks[]=mmtgroup-x:', $text );
        $this->assertStringNotContainsString( 'MegaMenuLinks[]=accounts:', $text, 'a link of the section is known' );
        $this->assertStringNotContainsString( 'MegaMenuLinks[]=access-roles_and_policies:', $text, 'a link of an added section is known' );
        $this->assertStringNotContainsString( 'MegaMenuTabs[]=setup:', $text );
        $this->assertStringNotContainsString( 'MegaMenuTabs[]=users:', $text );

        $this->menuIni( array( 'Leftmenu_user_mega' => array( 'MegaMenuLinks' => array_fill( 0, 20, 'accounts' ) ) ) );
        $this->assertStringContainsString( '20 keys, the mega menu shows the first 19', implode( "\n", self::debugMessagesOf( 'file:design/admin4/templates/parts/a4_mega_menu_check.tpl' ) ) );
    }

    /** The links of Exponential Intelligence (extension expai) the Dashboard's mega menu offers: [section, key] */
    const DASHBOARD_EI_LINKS = array( array( 'Leftmenu_my', 'expai' ), array( 'Leftmenu_my', 'expai_vector' ),
                                      array( 'Leftmenu_my', 'expai_chain' ), array( 'Leftmenu_my', 'expai_log' ),
                                      array( 'Leftmenu_my', 'expai_help' ), array( 'Leftmenu_design', 'expai_alttext' ),
                                      array( 'Leftmenu_audit', 'expai_events' ) );

    /** Links other code appends to the sections (extensions, the help center): not in core menu.ini's Links[] */
    const DASHBOARD_APPENDED_LINKS = array( 'expai', 'expai_vector', 'expai_chain', 'expai_log', 'expai_help', 'expai_alttext',
                                            'expai_events', 'help_center' );

    /** The complete kernel catalogues (doc/eng-us/languages.md), English and the template for new ones */
    const COMPLETE_CATALOGUES = array( 'eng-US', 'ger-DE', 'fre-FR', 'esl-ES', 'esl-MX', 'ukr-UA', 'heb-IL', 'lat-VA', 'tlh-QO', 'ara-SA' );

    /** MM-10 */
    public function testTheDashboardTabReusesTheMenusThatDefineItsLinks()
    {
        $ini = self::iniSections();
        $tab = $ini['Topmenu_dashboard'];
        $this->assertSame( 'design:parts/my/mega_menu.tpl', $tab['MegaMenuTemplate'] ?? null );
        $this->assertSame( 'Leftmenu_my_mega', $tab['MegaMenuSection'] ?? null );
        $this->assertSame( 'wide', $tab['MegaMenuLayout'] ?? null );
        $this->assertSame( 'ezmynavigationpart', $tab['NavigationPartIdentifier'] );
        $this->assertSame( array( 'content/dashboard' ), $tab['PolicyList'], 'the tab is access checked' );
        $this->assertFileExists( self::root() . '/design/admin4/templates/parts/my/mega_menu.tpl' );

        $mega = $ini['Leftmenu_my_mega'];
        $this->assertSame( array( 'overview', 'work', 'activity', 'ei', 'account' ), $mega['Groups'] );
        $this->assertSame( array( 'overview' => 'Leftmenu_my', 'work' => 'Leftmenu_my', 'activity' => 'Leftmenu_audit',
                                  'ei' => 'Leftmenu_my;Leftmenu_design', 'account' => 'Leftmenu_my' ), $mega['GroupSections'] );
        // no links of its own: every link is one of the menu that defines it, with its policies
        $this->assertArrayNotHasKey( 'Links', $mega );
        $this->assertDoesNotMatchRegularExpression( '/^(Links|PolicyList_[a-z_]+)\[[a-z_]*\]=/m',
                                                     self::sectionText( 'Leftmenu_my_mega' ), 'restates no link or policy' );
        // the left menu of the tab stays one list
        $this->assertArrayNotHasKey( 'Groups', $ini['Leftmenu_my'] );
        $this->assertStringContainsString( "{include uri='design:parts/ini_menu.tpl' ini_section='Leftmenu_my' i18n_hash=", self::read( 'design/admin4/templates/parts/my/menu.tpl' ) );

        // the template: the section, as the mega menu's groups, with the audit menu's hidden channel links
        $template = self::read( 'design/admin4/templates/parts/my/mega_menu.tpl' );
        $this->assertStringContainsString( "{include uri='design:parts/ini_menu.tpl' ini_section='Leftmenu_my_mega'", $template );
        $this->assertStringContainsString( "menu_layout='tiles'", $template );
        $this->assertStringContainsString( "tiles_layout='mega'", $template );
        $this->assertStringContainsString( "section_hidden_links=hash( 'Leftmenu_audit', fetch( 'audit', 'hidden_menu_links', hash( 'section', 'Leftmenu_audit' ) ) )", $template );
        // a group adds one section or several, in that order
        $menu = self::read( 'design/admin4/templates/parts/ini_menu.tpl' );
        $this->assertStringContainsString( "{foreach \$group_section|explode( ';' ) as \$group_section_name}", $menu );
        $this->assertStringContainsString( "hash( 'section', \$group_section_name, 'group', \$group )", $menu );
        $this->assertStringContainsString( "{foreach \$mmc_added[ \$mmc_parts[0] ]|explode( ';' ) as \$mmc_added_section}",
                                           self::read( 'design/admin4/templates/parts/a4_mega_menu_check.tpl' ) );
    }

    /** The text of one section of settings/menu.ini, up to the next section */
    private static function sectionText( $name )
    {
        $ini = self::read( 'settings/menu.ini' );
        $section = substr( $ini, strpos( $ini, "\n[$name]" ) );
        return substr( $section, 0, strpos( $section, "\n[", 2 ) ?: strlen( $section ) );
    }

    /** MM-10 */
    public function testTheDashboardChoiceCoversTheTabWithEveryEiLink()
    {
        $ini = self::iniSections();
        $mega = $ini['Leftmenu_my_mega'];
        $choice = $mega['MegaMenuLinks'];
        $this->assertMatchesRegularExpression( '/^MegaMenuLinks\[\]$/m', self::sectionText( 'Leftmenu_my_mega' ), 'the list is reset before it is set' );
        $this->assertLessThanOrEqual( 18, count( $choice ), 'at most 18 links' );
        $this->assertGreaterThanOrEqual( 16, count( $choice ), 'almost every function of the tab' );
        $this->assertSame( array_values( array_unique( $choice ) ), $choice );

        // [group, section, key] of each chosen link: the key is a link of a section its group adds
        $chosen = array();
        foreach ( $choice as $key )
        {
            $this->assertStringContainsString( '-', $key, "MegaMenuLinks[]=$key: the section has no links of its own" );
            list( $group, $link ) = explode( '-', $key, 2 );
            $this->assertArrayHasKey( $group, $mega['GroupSections'], "MegaMenuLinks[]=$key: no such group adds a section" );
            $sections = explode( ';', $mega['GroupSections'][$group] );
            $found = null;
            foreach ( $sections as $section )
                if ( isset( $ini[$section]['Links'][$link] ) )
                    $found = $section;
            if ( $found === null )
            {
                // a link the extension or the help appends: in one of the group's sections, never restated in core
                $this->assertContains( $link, self::DASHBOARD_APPENDED_LINKS, "MegaMenuLinks[]=$key is no link of " . implode( ', ', $sections ) );
                foreach ( self::DASHBOARD_EI_LINKS as $ei )
                    if ( $ei[1] === $link )
                        $found = $ei[0];
                $found = $found ?? 'Leftmenu_my';
                $this->assertContains( $found, $sections, "$key is in a group that adds [$found]" );
            }
            $chosen[$key] = array( $group, $found, $link );
        }
        // every function of the tab's menu but the shop's
        foreach ( array_keys( $ini['Leftmenu_my']['Links'] ) as $link )
        {
            if ( in_array( $link, array( 'preferred_currency', 'audit' ), true ) )
                continue;
            $this->assertNotEmpty( array_filter( $chosen, function ( $c ) use ( $link ) { return $c[1] === 'Leftmenu_my' && $c[2] === $link; } ),
                                   "[Leftmenu_my] $link is in the Dashboard's mega menu" );
        }
        // the audit console of the tab's menu (audit/console) as the audit menu's console
        $this->assertSame( $ini['Leftmenu_my']['Links']['audit'], $ini['Leftmenu_audit']['Links']['console'] );
        $this->assertContains( 'activity-console', $choice );
        // every link of Exponential Intelligence
        foreach ( self::DASHBOARD_EI_LINKS as list( $section, $link ) )
            $this->assertNotEmpty( array_filter( $chosen, function ( $c ) use ( $section, $link ) { return $c[1] === $section && $c[2] === $link; } ),
                                   "the EI link [$section] $link is in the Dashboard's mega menu" );
        $this->assertContains( 'overview-help_center', $choice, 'the Help center is in the Dashboard\'s mega menu' );
        // the order of the left menu: the audit before Exponential Intelligence, Change password and Edit profile last
        $this->assertLessThan( array_search( 'ei-expai', $choice ), array_search( 'activity-console', $choice ) );
        $this->assertSame( array( 'account-change_password', 'account-edit_profile' ), array_slice( $choice, -2 ) );
        $this->assertSame( 'overview-dashboard', $choice[0], 'the tab\'s own page first' );
    }

    /** MM-10 */
    public function testTheDashboardLinksHaveIconsAndStringsInEveryCompleteCatalogue()
    {
        $ini = self::iniSections();
        $mega = $ini['Leftmenu_my_mega'];
        preg_match_all( '/^IconList\[\]=(\S+)$/m', self::read( 'settings/design.ini' ), $icons );
        $hash = self::read( 'design/admin4/templates/parts/my/mega_menu.tpl' );
        $strings = array();
        foreach ( $mega['MegaMenuLinks'] as $key )
        {
            list( $group, $link ) = explode( '-', $key, 2 );
            $section = null;
            foreach ( explode( ';', $mega['GroupSections'][$group] ) as $candidate )
                if ( isset( $ini[$candidate]['LinkIcons'][$link] ) )
                    $section = $section ?? $candidate;
            if ( $link === 'help_center' && $section === null )
                continue;   // the help center's own icon and description come with its link
            $this->assertNotNull( $section, "LinkIcons[$link] in a section of the group $group" );
            $icon = $ini[$section]['LinkIcons'][$link];
            $this->assertContains( $icon, $icons[1], "LinkIcons[$link]=$icon is not in design.ini IconList[]" );
            $this->assertFileExists( self::root() . "/share/icons/bootstrap-icons/svg/$icon.svg" );
            $text = $ini[$section]['LinkDescriptions'][$link] ?? null;
            $this->assertNotNull( $text, "[$section] LinkDescriptions[$link]" );
            $strings[] = array( 'design/admin/parts/' . explode( '_', $section )[1] . '/menu', $text );
            // the name: the template's i18n_hash, or the link's LinkNames[] (the extensions' links)
            if ( preg_match( "/^\s*'" . preg_quote( $key, '/' ) . "',\s+'([^']+)'\|i18n\( '([^']+)' \),$/m", $hash, $name ) )
                $strings[] = array( $name[2], $name[1] );
            else
                $this->assertContains( $link, self::DASHBOARD_APPENDED_LINKS, "$key has a name in parts/my/mega_menu.tpl or LinkNames[]" );
        }
        foreach ( $mega['GroupNames'] as $name )
            $strings[] = array( 'design/admin/parts/my/menu', $name );
        foreach ( self::COMPLETE_CATALOGUES as $language )
        {
            $dom = new DOMDocument();
            $this->assertTrue( $dom->load( self::root() . "/share/translations/$language/translation.ts" ) );
            $xpath = new DOMXPath( $dom );
            foreach ( $strings as list( $context, $source ) )
            {
                if ( $source === 'Help center' )
                    continue;   // added with the help center's link
                $found = $xpath->query( "//context[name='$context']/message[source=" . self::xpathString( $source ) . ']/translation[not(@type)]' );
                $this->assertGreaterThanOrEqual( 1, $found->length, "$language, $context: $source" );
                $this->assertNotSame( '', trim( $found->item( 0 )->textContent ), "$language, $context: $source" );
            }
        }
    }

    /**
     * MM-10: the groups as ini_menu.tpl hands them over for the Dashboard: only the chosen links the user may open, in
     * their groups and order; a link of an inactive extension is not in the groups at all, and a group left without a
     * link is not drawn
     */
    public function testTheDashboardShowsOnlyTheLinksTheUserMayOpen()
    {
        $item = function ( $key, $state = 'link' ) {
            return array( 'key' => $key, 'url' => 'x/' . $key, 'name' => strtoupper( $key ), 'state' => $state, 'group' => explode( '-', $key )[0],
                          'icon' => 'gear', 'text' => '', 'context' => 'design/admin/parts/my/menu', 'figure' => '' );
        };
        $choice = self::iniSections()['Leftmenu_my_mega']['MegaMenuLinks'];
        // every group adds all of [Leftmenu_my]: the overview group holds the drafts too, but shows only its choice
        $groups = array(
            array( 'id' => 'overview', 'name' => 'Overview', 'items' => array( $item( 'overview-dashboard' ), $item( 'overview-my_drafts' ) ) ),
            array( 'id' => 'work', 'name' => 'Your work', 'items' => array( $item( 'work-dashboard' ), $item( 'work-my_drafts' ), $item( 'work-my_pending', 'no-access' ),
                                                                            $item( 'work-collaboration', 'disabled' ), $item( 'work-my_bookmarks' ) ) ),
            array( 'id' => 'activity', 'name' => 'Activity and audit', 'items' => array( $item( 'activity-console', 'no-access' ) ) ),
            array( 'id' => 'account', 'name' => 'My account', 'items' => array( $item( 'account-edit_profile' ), $item( 'account-change_password' ) ) ),
        );
        // no ei group: expai is not active (its links are in no section), and the audit console is closed to this user
        $this->assertSame( array( 'overview' => array( 'OVERVIEW-DASHBOARD' ), 'work' => array( 'WORK-MY_DRAFTS', 'WORK-MY_BOOKMARKS' ),
                                  'account' => array( 'ACCOUNT-CHANGE_PASSWORD', 'ACCOUNT-EDIT_PROFILE' ) ),
                           self::renderGroups( $choice, $groups ) );
        // with expai: its group after the audit, in the order of the choice
        $groups[] = array( 'id' => 'ei', 'name' => 'Exponential Intelligence', 'items' => array( $item( 'ei-expai_log' ), $item( 'ei-expai' ), $item( 'ei-look_and_feel' ),
                                                                                              $item( 'ei-expai_alttext' ), $item( 'ei-expai_vector', 'no-access' ) ) );
        $groups[2]['items'][0]['state'] = 'link';
        $shown = self::renderGroups( $choice, $groups );
        $this->assertSame( array( 'overview', 'work', 'activity', 'ei', 'account' ), array_keys( $shown ) );
        $this->assertSame( array( 'EI-EXPAI', 'EI-EXPAI_ALTTEXT', 'EI-EXPAI_LOG' ), $shown['ei'], 'the design menu\'s other links are not chosen' );
    }

    /** MM-10: MegaMenuLayout=wide: up to four balanced columns that fit 960px at 200% zoom; other tabs as before */
    public function testTheDashboardPanelIsWide()
    {
        $tabs = array( 'ezmynavigationpart' => 'dashboard', 'ezusernavigationpart' => 'users' );
        $dashboard = self::renderItem( array( 'navigationpart_identifier' => 'ezmynavigationpart', 'url' => 'content/dashboard', 'name' => 'Dashboard' ), $tabs );
        $this->assertStringContainsString( '<div id="a4-mega-dashboard" class="a4-mega-panel a4-mega-wide" hidden', $dashboard );
        $this->assertMatchesRegularExpression( '#data-a4-mega-src="[^"]*/user/megamenu/dashboard"#', $dashboard );
        $users = self::renderItem( array( 'navigationpart_identifier' => 'ezusernavigationpart', 'url' => 'content/view/full/5', 'name' => 'Users' ), $tabs );
        $this->assertStringContainsString( '<div id="a4-mega-users" class="a4-mega-panel" hidden', $users );

        $wide = self::media( self::css(), 'screen and (min-width: 576px)' );
        $this->assertStringContainsString( '.a4-mega-panel.a4-mega-wide {', $wide );
        $this->assertStringContainsString( '    width: min(calc(4 * 15rem + 3 * var(--a4-space-5) + 2 * var(--a4-space-5) + 2px), calc(100vw - 2 * var(--a4-space-5)));', $wide );
        // every panel under its own tab: the script sets --a4-mega-start (the tab's start edge, kept inside the window,
        // mirrored right to left) through the CSSOM; without the script the panel stays centred
        $this->assertMatchesRegularExpression( '/\.a4-mega-panel\.is-anchored \{\s*inset-inline-start: var\(--a4-mega-start, var\(--a4-space-5\)\);\s*inset-inline-end: auto;\s*margin-inline: 0;/', $wide );
        $js = self::read( 'design/admin4/javascript/a4_mega_menu.js' );
        $this->assertStringContainsString( "panel.style.setProperty('--a4-mega-start', Math.round(start) + 'px');", $js );
        $this->assertStringContainsString( "var start = rtl ? width - tab.right : tab.left;", $js );
        $this->assertStringContainsString( "start = Math.max(margin, Math.min(start, width - margin - panel.offsetWidth));", $js );
        $this->assertStringNotContainsString( 'setAttribute(\'style\'', $js, 'no style attribute (CSP)' );
        // the caret inside the tab's pill: 12px more than a tab without a mega menu, the selected pill keeps its shape
        $this->assertStringContainsString( '.navbar-menu .navbar-bar > li.a4-mega-item > a.selected:hover { padding-inline-end: var(--a4-space-5); }', $wide );
        $this->assertMatchesRegularExpression( '/\.navbar-bar > li\.a4-mega-item > \.a4-mega-toggle \{\s*position: absolute;\s*inset-inline-end: 0;/', $wide );
        $this->assertStringContainsString( '.navbar-bar > li.a4-mega-item > a.selected + .a4-mega-toggle { color: var(--a4-on-accent); }', $wide );
        // the row's tab icons from 1600px only, so the tabs keep their room at 1440px and 960px
        $this->assertStringContainsString( '.navbar-menu .navbar-bar > li > a > .a4-icon { display: none; }',
                                           self::media( self::css(), 'screen and (min-width: 576px) and (max-width: 1599.98px)' ) );
        $this->assertStringContainsString( 'columns: 15rem 4;', $wide );
        $this->assertMatchesRegularExpression( '/\.a4-mega-wide \.a4-mega-group \{[^}]*break-inside: avoid;/', $wide, 'a group is never split' );
        // three columns of 15rem (16px) and their gaps fit 960px less the card's insets and padding: no inner scroll there
        $this->assertLessThanOrEqual( 960 - 2 * 24 - 2 * 24 - 2, 3 * 15 * 16 + 2 * 24 );
    }

    /** MM-10: the check knows the links of every section a group adds */
    public function testTheCheckReadsEverySectionOfAGroup()
    {
        $this->menuIni( array(
            // the same tabs as testIgnoredSettingsAreNamedInTheDebugReport: the compiled check holds the constant list of tabs
            'TopAdminMenu' => array( 'MegaMenuTabs' => array( 'setup', 'users', 'mmt_unknown' ) ),
            'Topmenu_users' => array( 'NavigationPartIdentifier' => 'ezusernavigationpart', 'MegaMenuTemplate' => 'design:parts/user/mega_menu.tpl',
                                      'MegaMenuSection' => 'Leftmenu_mmt_multi' ),
            'Leftmenu_my' => array( 'Links' => array( 'dashboard' => 'content/dashboard' ) ),
            'Leftmenu_design' => array( 'Links' => array( 'look_and_feel' => 'content/edit/54' ) ),
            'Leftmenu_mmt_multi' => array( 'GroupSections' => array( 'ei' => 'Leftmenu_my;Leftmenu_design' ),
                                           'MegaMenuLinks' => array( 'ei-dashboard', 'ei-look_and_feel', 'ei-mmt_nolink' ) ),
        ) );
        $text = implode( "\n", self::debugMessagesOf( 'file:design/admin4/templates/parts/a4_mega_menu_check.tpl' ) );
        $this->assertStringNotContainsString( 'MegaMenuLinks[]=ei-dashboard:', $text, 'a link of the first section' );
        $this->assertStringNotContainsString( 'MegaMenuLinks[]=ei-look_and_feel:', $text, 'a link of the second section' );
        $this->assertStringContainsString( '[Leftmenu_mmt_multi] MegaMenuLinks[]=ei-mmt_nolink:', $text );
    }

    /** Renders a template with the debug report switched on; returns the debug messages it wrote */
    private static function debugMessagesOf( $uri )
    {
        $debug = eZDebug::instance();
        $saved = array( isset( $GLOBALS['eZDebugEnabled'] ) ? $GLOBALS['eZDebugEnabled'] : null, $debug->ShowTypes,
                        $debug->HandleType, $debug->MessageOutput, $debug->DebugStrings );
        $GLOBALS['eZDebugEnabled'] = true;
        $debug->ShowTypes = $debug->ShowTypes | eZDebug::SHOW_DEBUG;
        $debug->HandleType = eZDebug::HANDLE_NONE;
        $debug->MessageOutput = eZDebug::OUTPUT_MESSAGE_STORE;
        $debug->DebugStrings = array();
        try
        {
            eZTemplate::factory()->fetch( $uri );
            $messages = array();
            foreach ( $debug->DebugStrings as $entry )
                $messages[] = is_array( $entry ) && isset( $entry['String'] ) ? (string)$entry['String'] : '';
        }
        finally
        {
            if ( $saved[0] === null ) unset( $GLOBALS['eZDebugEnabled'] ); else $GLOBALS['eZDebugEnabled'] = $saved[0];
            list( , $debug->ShowTypes, $debug->HandleType, $debug->MessageOutput, $debug->DebugStrings ) = $saved;
        }
        return $messages;
    }
}
