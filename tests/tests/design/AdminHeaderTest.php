<?php
/**
 * admin4's header, checked on the files alone (no database):
 *
 * - one row of 56px from 1280px (logo, main menu, search, site, light/dark, user menu, logout), two rows of 48 + 40px from
 *   576px, one row of 56px on a phone; --header-height defaults to these heights on the root and admin4.js sets the
 *   measured one there, so everything that keeps clear of the header reads the same value;
 * - the main menu stays on one line: the tabs that do not fit go into a "More" disclosure (aria-expanded,
 *   aria-controls), hidden until the script needs it; without the script the row scrolls;
 * - logout is an icon button of its own beside the user menu, named "Logout (<login>)", one click away; the user
 *   menu is a disclosure with the user's name and links; the header's cache blocks are keyed by the user;
 * - the name at the top of the user menu links to the full view of the user's own object, when the user may read its
 *   main node and not on edit and browse pages (rendered with the real template engine, no database);
 * - "Open the site" is the first of the header's tools, a round icon button before light/dark, the user menu and
 *   logout, in the markup in the order they are drawn; the logo stands alone;
 * - "/" puts the focus in the header search; the new rules take their colours from the tokens.
 *
 * doc/bc/6.0/admin4-styling.md
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminHeaderTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminBaseStylesheetsTest.php';

class AdminHeaderTest extends PHPUnit\Framework\TestCase
{
    private static function file( $path )
    {
        return (string)file_get_contents( dirname( __DIR__, 3 ) . '/' . $path );
    }

    private static function css( $file )
    {
        return preg_replace( '#/\*.*?\*/#s', '', self::file( 'design/admin4/stylesheets/' . $file ) );
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
            for ( $depth = 1, $from = $i; $i < strlen( $css ) && $depth > 0; $i++ )
            {
                if ( $css[$i] === '{' ) $depth++;
                if ( $css[$i] === '}' ) $depth--;
            }
            $inner .= substr( $css, $from, $i - 1 - $from ) . "\n";
            $offset = $i;
        }
        return $inner;
    }

    public function testTheHeaderHasOneRowFrom1280TwoFrom576AndOneOnAPhone()
    {
        $css = self::css( 'admin4.css' );
        $base = AdminBaseStylesheetsTest::rules( preg_replace( '/@media[^{]*\{((?:[^{}]*\{[^{}]*\})*)[^{}]*\}/s', '', $css ) );
        $this->assertSame( '56px', $base[':root']['--header-height'] );
        $tablet = AdminBaseStylesheetsTest::rules( self::media( $css, 'screen and (min-width: 576px)' ) );
        $this->assertSame( '88px', $tablet[':root']['--header-height'] );
        $this->assertSame( '0 0 100%', $tablet['.navbar-menu']['flex'], 'the menu is the second row' );
        $this->assertSame( '5', $tablet['.navbar-menu']['order'] );
        $wide = AdminBaseStylesheetsTest::rules( self::media( $css, 'screen and (min-width: 1280px)' ) );
        $this->assertSame( '56px', $wide[':root']['--header-height'] );
        $this->assertSame( 'nowrap', $wide['.navbar-main']['flex-wrap'] );
        $this->assertSame( '2', $wide['.navbar-menu']['order'], 'the menu stands between the logo and the tools' );
        $this->assertSame( '1 1 0', $wide['.navbar-menu']['flex'], 'its width does not depend on the tabs, so fitting them cannot loop' );
        $phone = AdminBaseStylesheetsTest::rules( self::media( $css, 'screen and (max-width: 575.98px)' ) );
        $this->assertSame( 'nowrap', $phone['.navbar-main']['flex-wrap'] );
        $this->assertSame( 'var(--header-height)', $phone['.navbar-menu.show']['padding-top'] );

        // one value for everything that keeps clear of the header: on the root, not on .dashboard-flex
        $this->assertDoesNotMatchRegularExpression( '/\.dashboard-flex\s*\{[^}]*--header-height\s*:/', self::css( 'pagelayout.css' ) );
        $js = self::file( 'design/admin4/javascript/admin4.js' );
        $this->assertStringContainsString( "var root = document.documentElement;", $js );
        $this->assertStringContainsString( "root.style.setProperty('--header-height', px + 'px');", $js );
        $this->assertStringContainsString( 'observer.observe(header);', $js );
    }

    public function testTheMenuOverflowsIntoAMoreDisclosure()
    {
        foreach ( array( 'design/admin4/templates/page_topmenu.tpl', 'design/admin4l/templates/explayouts/block/admin_tab_menu.tpl' ) as $path )
        {
            $tpl = self::file( $path );
            $this->assertStringContainsString( '<li class="a4-nav-more" hidden>', $tpl, $path );
            $this->assertStringContainsString( '<button type="button" class="a4-nav-more-toggle" aria-expanded="false" aria-controls="a4-nav-more-list">{\'More\'|i18n( \'design/admin/pagelayout\' )|wash}', $tpl, $path );
            $this->assertStringContainsString( '<ul id="a4-nav-more-list" class="a4-nav-more-list" hidden></ul>', $tpl, $path );
            $this->assertLessThan( strpos( $tpl, '<li class="header-search-mobile">' ), strpos( $tpl, '<li class="a4-nav-more" hidden>' ), "$path: More is the row's last tab" );
            $this->assertStringNotContainsString( 'Logout: ', $tpl, "$path: logout left the menu row" );
        }
        $js = self::file( 'design/admin4/javascript/admin4.js' );
        foreach ( array( "new ResizeObserver(fit).observe(nav);", "list.insertBefore(tabs[i], list.firstChild);",
                         "if (li.parentNode !== bar || li.nextSibling !== next)", "if (!wide.matches && !list.firstChild) return;",
                         "var more = document.querySelector('#header .a4-nav-more');",
                         "toggle.classList.toggle('has-current', !!list.querySelector('[aria-current]'));",
                         "button.setAttribute('aria-expanded', state ? 'true' : 'false');", "e.key === 'Escape'", "e.key === 'ArrowDown'",
                         "nav.classList.toggle('a4-nav-fits', wide.matches);", "window.matchMedia('(min-width: 576px)')" ) as $needle )
            $this->assertStringContainsString( $needle, $js, $needle );
        // the tab the user is on stays recognisable inside "More"
        $rules = AdminBaseStylesheetsTest::rules( self::css( 'admin4.css' ) );
        $this->assertSame( 'var(--a4-accent-strong)', $rules['.a4-nav-more-toggle.has-current']['background-color'] );
        $this->assertSame( 'none', $rules['.a4-nav-more[hidden], .a4-nav-more-list[hidden]']['display'] );
    }

    public function testLogoutIsAnIconButtonBesideTheUserMenu()
    {
        $tpl = self::file( 'design/admin4/templates/parts/a4_header_user.tpl' );
        $this->assertStringContainsString( '{if $current_user.is_logged_in}', $tpl, 'anonymous pages have no one to log out' );
        $this->assertStringContainsString( "\$a4_logout_label = 'Logout (%login)'|i18n( 'design/admin/pagelayout',, hash( '%login', \$current_user.login ) )", $tpl );
        $this->assertStringContainsString( '<a href={\'/user/logout\'|ezurl} id="header-usermenu-logout" class="a4-header-btn a4-logout" aria-label="{$a4_logout_label|wash}" title="{$a4_logout_label|wash}"><span class="a4-icon a4-bi-box-arrow-right" aria-hidden="true"></span></a>', $tpl );
        // not inside the user menu: one click
        $menu = substr( $tpl, strpos( $tpl, '<div id="a4-user-menu"' ) );
        $menu = substr( $menu, 0, strpos( $menu, '</ul>' ) );
        $this->assertStringNotContainsString( 'user/logout', $menu );

        $this->assertMatchesRegularExpression( '#<button type="button" id="a4-user-toggle" class="a4-header-btn a4-user-toggle" aria-expanded="false" aria-controls="a4-user-menu"#', $tpl );
        $this->assertStringContainsString( '<div id="a4-user-menu" class="a4-user-menu" hidden>', $tpl );
        $this->assertStringContainsString( '<span class="a4-sr-only">{$a4_user_label|wash}</span>', $tpl, 'the icon button has a name' );
        foreach ( array( "'Change information'", "'Change password'", "'My e-mail preferences'", '{$a4_user_name|wash}', '{$a4_user_login|wash}' ) as $needle )
            $this->assertStringContainsString( $needle, $tpl, $needle );

        foreach ( array( 'design/admin4/templates/page_topmenu.tpl', 'design/admin4l/templates/explayouts/block/admin_theme_switch.tpl' ) as $path )
            $this->assertStringContainsString( "{include uri='design:parts/a4_header_user.tpl'}", self::file( $path ), $path );
        $this->assertStringContainsString( 'disclosure(document.getElementById(\'a4-user-toggle\'), document.getElementById(\'a4-user-menu\'));', self::file( 'design/admin4/javascript/admin4.js' ) );

        // the user's name and links are the user's own: the header's cache blocks are keyed by the user's object id
        foreach ( array( 'design/admin4/templates/pagelayout.tpl', 'design/admin4l/templates/pagelayout.tpl' ) as $path )
            $this->assertSame( 2, preg_match_all( '/\{cache-block keys=array\( [^)]*\$user_hash, \$current_user\.contentobject_id, /', self::file( $path ) ), $path );

        // 36px round targets with a focus ring
        $rules = AdminBaseStylesheetsTest::rules( self::css( 'admin4.css' ) );
        $this->assertSame( '36px', $rules['.a4-header-btn']['width'] );
        $this->assertSame( '36px', $rules['.a4-header-btn']['height'] );
        $this->assertSame( '3px solid var(--a4-ring)', $rules['.a4-header-btn:focus-visible']['outline'] );
        foreach ( array( 'ger-DE' => 'Abmelden (%login)', 'eng-US' => 'Logout (%login)' ) as $language => $translation )
            $this->assertStringContainsString( "<source>Logout (%login)</source>\n        <translation>$translation</translation>", self::file( "share/translations/$language/translation.ts" ) );
    }

    /**
     * Renders parts/a4_header_user.tpl with the real template engine for a user given as nested arrays, no database:
     * a copy of the template in var/tmp answers its one access check, fetch( 'user', 'can_open' ) for user/password
     * (expViewAccess reads the current user from the database), with true(); everything else is the template as is.
     */
    private function renderUserMenu( array $user, $uiContext = 'navigation' )
    {
        $root = dirname( __DIR__, 3 );
        $check = "fetch( 'user', 'can_open', hash( 'uri', 'user/password' ) )";
        $source = (string)file_get_contents( $root . '/design/admin4/templates/parts/a4_header_user.tpl' );
        $this->assertSame( 1, substr_count( $source, $check ), 'the one database access of the template' );
        $dir = $root . '/var/tmp/phpunit-header-user-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( $dir, 0777, true );
        $copy = $dir . '/a4_header_user.tpl';
        file_put_contents( $copy, str_replace( $check, 'true()', $source ) );
        $basics = array_key_exists( 'eZSiteBasics', $GLOBALS ) ? array( $GLOBALS['eZSiteBasics'] ) : null;
        // not compiled: each case renders the same template with other variables
        $GLOBALS['eZSiteBasics']['no-cache-adviced'] = true;
        try
        {
            $tpl = eZTemplate::factory();
            $tpl->setVariable( 'current_user', $user );
            $tpl->setVariable( 'ui_context', $uiContext );
            $tpl->setVariable( 'ui_context_edit', $uiContext === 'edit' );
            $html = $tpl->fetch( $copy );
            $this->assertSame( array(), $tpl->errorLog() );
            return $html;
        }
        finally
        {
            eZTemplate::resetInstance();
            if ( $basics === null )
                unset( $GLOBALS['eZSiteBasics'] );
            else
                $GLOBALS['eZSiteBasics'] = $basics[0];
            unlink( $copy );
            rmdir( $dir );
        }
    }
    private static function user( $objectId, $login, $name, $mainNode )
    {
        return array( 'is_logged_in' => true, 'login' => $login, 'contentobject_id' => $objectId,
                      'contentobject' => array( 'name' => $name, 'can_edit' => true, 'main_node' => $mainNode ) );
    }

    /**
     * The name at the top of the user menu (display name and login) links to the URL alias of the main node of the
     * signed-in user's own object, as the right column's "Current user" box links the name: only with a main node the
     * user may read, and not on edit and browse pages, where the menu's links lead nowhere. The link is a plain link
     * of the disclosure (no menu role), named by the text it shows, with "Open your user object" as its tooltip and
     * description. The header's cache blocks are keyed by the user's role hash and object id, so one user's link is
     * never served to another.
     */
    public function testTheNameLinksToTheUsersOwnObject()
    {
        $hint = ezpI18n::tr( 'design/admin/pagelayout', 'Open your user object' );
        $html = $this->renderUserMenu( self::user( 14, 'admin', 'Administrator User', array( 'node_id' => 2042, 'url_alias' => 'Users/Administrator-users/Administrator-User', 'can_read' => true ) ) );
        $this->assertSame( 1, preg_match( '#<a id="a4-user-menu-self" class="a4-user-menu-self" href="([^"]*)" title="([^"]*)" aria-describedby="a4-user-menu-self-hint">\s*<span class="a4-user-menu-display">Administrator User</span>\s*<span class="a4-user-menu-login" translate="no">admin</span>\s*</a>#', $html, $link ), $html );
        $this->assertMatchesRegularExpression( '#/Users/Administrator-users/Administrator-User$#', $link[1], 'the URL alias of the main node' );
        // tooltip and description: the same translated hint, whatever the language the test runs in
        $this->assertSame( 1, preg_match( '#<span id="a4-user-menu-self-hint" hidden>([^<]+)</span>#', $html, $described ) );
        $this->assertSame( $link[2], $described[1] );
        $this->assertSame( $hint, html_entity_decode( $link[2], ENT_QUOTES | ENT_HTML5 ) );
        $this->assertStringNotContainsString( 'role="menuitem"', $html, 'a disclosure of plain links' );
        // the first link of the panel: ArrowDown on the button reaches it first (links() in admin4.js takes a[href])
        $this->assertLessThan( strpos( $html, '<ul class="a4-user-menu-links">' ), strpos( $html, 'id="a4-user-menu-self"' ) );
        $this->assertStringContainsString( "return Array.prototype.filter.call(panel.querySelectorAll('a[href]'), function (a) {", self::file( 'design/admin4/javascript/admin4.js' ) );

        // no read access, no main node, an edit or browse page: the name as text, as before
        $text = '#<p class="a4-user-menu-name">\s*<span class="a4-user-menu-display">Administrator User</span>\s*<span class="a4-user-menu-login" translate="no">admin</span>\s*</p>#';
        $cases = array(
            'no read access' => array( self::user( 14, 'admin', 'Administrator User', array( 'node_id' => 2042, 'can_read' => false ) ), 'navigation' ),
            'no main node' => array( self::user( 14, 'admin', 'Administrator User', false ), 'navigation' ),
            'edit page' => array( self::user( 14, 'admin', 'Administrator User', array( 'node_id' => 2042, 'can_read' => true ) ), 'edit' ),
            'browse page' => array( self::user( 14, 'admin', 'Administrator User', array( 'node_id' => 2042, 'can_read' => true ) ), 'browse' ) );
        foreach ( $cases as $case => list( $user, $context ) )
        {
            $html = $this->renderUserMenu( $user, $context );
            $this->assertMatchesRegularExpression( $text, $html, $case );
            $this->assertStringNotContainsString( 'content/view/full', $html, $case );
            $this->assertStringNotContainsString( 'a4-user-menu-self', $html, $case );
        }

        // two users, two links: each names its own node; a node without a URL alias is linked by its id
        $other = $this->renderUserMenu( self::user( 99, 'editor', 'Editor', array( 'node_id' => 3077, 'url_alias' => '', 'can_read' => true ) ) );
        $this->assertMatchesRegularExpression( '#href="[^"]*/content/view/full/3077"#', $other );
        $this->assertStringNotContainsString( '2042', $other );

        // the cache: page_header.tpl (and with it this template) is drawn inside the header's cache blocks, which are
        // keyed by the page context ($ui_context: edit and browse apart), the role hash ($user_hash: roles and limitation
        // values decide can_read) and the object id (whose main node the link names), in admin4 and admin4l
        foreach ( array( 'design/admin4/templates/pagelayout.tpl', 'design/admin4l/templates/pagelayout.tpl' ) as $path )
        {
            $layout = self::file( $path );
            // the innermost cache block around page_header.tpl, whatever the order of its keys
            $at = strpos( $layout, "{include uri='design:page_header.tpl'}" );
            $this->assertNotFalse( $at, $path );
            $open = strrpos( substr( $layout, 0, $at ), '{cache-block keys=array(' );
            $this->assertNotFalse( $open, $path );
            $this->assertStringNotContainsString( '{/cache-block}', substr( $layout, $open, $at - $open ), "$path: page_header.tpl is inside the block" );
            $keys = substr( $layout, $open, strpos( $layout, "\n", $open ) - $open );
            foreach ( array( '$ui_context,', '$user_hash,', '$current_user.contentobject_id,' ) as $key )
                $this->assertStringContainsString( ' ' . $key, $keys, "$path: $key" );
            $block = array( 1 => substr( $layout, $open, strpos( $layout, '{/cache-block}', $at ) - $open ) );
        }
        // admin4l draws the header zone (admin_theme_switch.tpl includes this template) in the same block
        $this->assertStringContainsString( "{include uri='design:explayouts/admin_zone.tpl' layout=\$admin_layout zone_identifier='header'}", $block[1] );
        $this->assertStringContainsString( "{include uri='design:page_topmenu.tpl'}", self::file( 'design/admin4/templates/page_header.tpl' ) );
        $this->assertStringContainsString( '$user_hash = concat( $current_user.role_id_list|implode( \',\' ), \',\', $current_user.limited_assignment_value_list|implode( \',\' )', self::file( 'design/admin4/templates/pagelayout.tpl' ) );

        // hover and focus as the menu's links, an underline on hover in forced colors
        $css = self::css( 'admin4.css' );
        $rules = AdminBaseStylesheetsTest::rules( $css );
        $this->assertSame( $rules['.a4-user-menu-links a:hover']['background-color'], $rules['.a4-user-menu-self:hover']['background-color'] );
        $this->assertSame( $rules['.a4-user-menu-links a:focus-visible']['outline'], $rules['.a4-user-menu-self:focus-visible']['outline'] );
        $this->assertSame( 'none', $rules['.a4-user-menu-self']['text-decoration'] );
        $forced = AdminBaseStylesheetsTest::rules( $css, true );
        $this->assertSame( 'underline', $forced['.a4-user-menu-links a:hover, .a4-user-menu-self:hover']['text-decoration'] );

        foreach ( array( 'ger-DE' => 'Eigenes Benutzerobjekt öffnen', 'eng-US' => 'Open your user object' ) as $language => $translation )
            $this->assertStringContainsString( "<source>Open your user object</source>\n        <translation>$translation</translation>", self::file( "share/translations/$language/translation.ts" ) );
    }

    /**
     * "Open the site" left the logo for the tools: the first of them, before light/dark, the user menu and logout, and
     * a round 36px icon button as they are. The tools stand in the markup in the order they are drawn, so the Tab
     * key goes search, site, light/dark, user menu, logout (the main menu and the phone menu button stay before them
     * in the markup, as before); the CSS gives the site link the order of the light/dark switch, so the markup
     * decides, and on a phone it is the one that pushes the tools to the row's end.
     */
    public function testTheSiteLinkIsTheFirstOfTheTools()
    {
        $link = '<a class="site-preview a4-header-btn" href="{siteaccess_url()|wash}" title="{$a4_site_label|wash}" aria-label="{$a4_site_label|wash}">';
        $part = self::file( 'design/admin4/templates/parts/a4_header_site.tpl' );
        $this->assertSame( 1, substr_count( $part, $link ), 'one link, on edit pages as on the others' );
        $this->assertStringContainsString( "{def \$a4_site_label = 'Open the site'|i18n( 'design/admin/pagelayout' )", $part );
        $this->assertStringNotContainsString( 'target=', $part, 'it opens in the same window: no "new window" hint needed' );
        $this->assertLessThan( strpos( $part, '<div id="a4-site-menu-wrap"' ), strpos( $part, $link ), 'the link, then the site menu that takes its place' );
        $include = "{include uri='design:parts/a4_header_site.tpl'}";
        $top = self::file( 'design/admin4/templates/page_topmenu.tpl' );
        $this->assertSame( 1, substr_count( $top, $include ) );
        $order = array( '<div id="header-logo"', '<div id="a4-navbar-menu"', '<div id="header-search"', $include,
                        '<div id="header-theme"', "{include uri='design:parts/a4_header_user.tpl'}", '<div class="sidebar-controls">' );
        $last = -1;
        foreach ( $order as $needle )
        {
            $at = strpos( $top, $needle );
            $this->assertNotFalse( $at, $needle );
            $this->assertGreaterThan( $last, $at, "page_topmenu.tpl: $needle is out of order" );
            $last = $at;
        }
        $logo = substr( $top, strpos( $top, '<div id="header-logo"' ), strpos( $top, '<div class="navbar-icon"' ) - strpos( $top, '<div id="header-logo"' ) );
        $this->assertStringNotContainsString( 'siteaccess_url()', $logo, 'the logo stands alone' );

        // admin4l: the tools block carries it, before light/dark; the logo block no longer does
        $tools = self::file( 'design/admin4l/templates/explayouts/block/admin_theme_switch.tpl' );
        $this->assertSame( 1, substr_count( $tools, $include ) );
        $this->assertLessThan( strpos( $tools, '<div id="header-theme"' ), strpos( $tools, $include ) );
        $this->assertStringNotContainsString( 'siteaccess_url()', self::file( 'design/admin4l/templates/explayouts/block/admin_logo.tpl' ) );

        // the header's cache blocks keep siteaccess_url() in their keys, and the site menu's key after it
        foreach ( array( 'design/admin4/templates/pagelayout.tpl', 'design/admin4l/templates/pagelayout.tpl' ) as $path )
            $this->assertSame( 2, preg_match_all( '/\{cache-block keys=array\( [^\n}]*siteaccess_url\(\), siteaccess_menu_key\(\)/', self::file( $path ) ), $path );

        $css = self::css( 'admin4.css' );
        $tablet = AdminBaseStylesheetsTest::rules( self::media( $css, 'screen and (min-width: 576px)' ) );
        $this->assertSame( '3', $tablet['.header-site, .header-theme']['order'], 'same order as light/dark: the markup puts it first' );
        $this->assertSame( '4', $tablet['.header-user']['order'] );
        $phone = AdminBaseStylesheetsTest::rules( self::media( $css, 'screen and (max-width: 575.98px)' ) );
        $this->assertSame( 'auto', $phone['.header-site']['margin-left'], 'the tools stand together at the row\'s end' );
        $this->assertArrayNotHasKey( '.header-theme', $phone, 'light/dark no longer pushes away from the site link' );
        $rules = AdminBaseStylesheetsTest::rules( $css );
        $this->assertSame( 'flex', $rules['.header-site, .header-user']['display'] );
        $this->assertDoesNotMatchRegularExpression( '/\.site-preview\s*\{/', $css, 'the link takes .a4-header-btn as it is' );
        $this->assertDoesNotMatchRegularExpression( '/\.site-preview\b/', self::css( 'pagelayout.css' ), 'no rules for the old place beside the logo' );
    }

    public function testTheSlashKeyFocusesTheHeaderSearch()
    {
        $search = self::file( 'design/admin4/templates/page_search.tpl' );
        $this->assertStringContainsString( '{if $box|eq( \'\' )} aria-keyshortcuts="/" data-short-placeholder="1"{/if}', $search );
        // the narrow header field: a short placeholder; the scope ("Search in all content") stays its name and title
        $this->assertStringContainsString( 'placeholder="{if $box|eq( \'\' )}{\'Search...\'|i18n( \'design/admin/pagelayout\' )|wash}{else}{$search_title|wash}{/if}" aria-label="{$search_title|wash}"', $search );
        $this->assertStringContainsString( "if ( !field.hasAttribute( 'data-short-placeholder' ) ) field.placeholder = label;", $search );
        $js = self::file( 'design/admin4/javascript/admin4.js' );
        $this->assertStringContainsString( "if (e.key !== '/' || e.ctrlKey || e.metaKey || e.altKey || e.isComposing || e.defaultPrevented) return;", $js );
        $this->assertStringContainsString( "/^(INPUT|TEXTAREA|SELECT)$/.test(t.tagName)", $js, 'typing a slash in a field stays a slash' );
        $this->assertStringContainsString( "t.closest('[role=\"textbox\"], [role=\"combobox\"], [role=\"dialog\"], [aria-modal=\"true\"], dialog')", $js, 'nor in a text widget or a dialog' );
    }

    /** The header's new rules take every colour from a token (or a system colour in forced colors) */
    public function testTheNewRulesUseNoLiteralColours()
    {
        $css = self::css( 'admin4.css' );
        $rules = AdminBaseStylesheetsTest::rules( $css );
        $checked = 0;
        foreach ( $rules as $selector => $declarations )
        {
            if ( !preg_match( '/\.a4-(nav-more|header-btn|user-menu|logout)/', $selector ) )
                continue;
            foreach ( $declarations as $property => $value )
            {
                $checked++;
                $this->assertDoesNotMatchRegularExpression( '/#[0-9a-f]{3,8}\b|\brgba?\(|\bhsla?\(|\b(white|black|red|gray|grey)\b/i', $value, "$selector { $property: $value }" );
            }
        }
        $this->assertGreaterThan( 40, $checked, 'the header rules were not read' );
        $forced = AdminBaseStylesheetsTest::rules( $css, true );
        $this->assertSame( 'ButtonText', $forced['.a4-header-btn, .a4-nav-more-list, .a4-user-menu']['border-color'] );
    }
}
