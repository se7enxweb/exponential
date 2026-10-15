<?php
/**
 * admin4's header, checked on the files alone (no database):
 *
 * - one row of 56px from 1280px (logo, main menu, search, light/dark, user menu, logout), two rows of 48 + 40px from
 *   576px, one row of 56px on a phone; --header-height defaults to these heights on the root and admin4.js sets the
 *   measured one there, so everything that keeps clear of the header reads the same value;
 * - the main menu stays on one line: the tabs that do not fit go into a "More" disclosure (aria-expanded,
 *   aria-controls), hidden until the script needs it; without the script the row scrolls;
 * - logout is an icon button of its own beside the user menu, named "Logout (<login>)", one click away; the user
 *   menu is a disclosure with the user's name and links; the header's cache blocks are keyed by the user;
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
        foreach ( array( "new ResizeObserver(fit).observe(nav);", "list.insertBefore(tabs[i], list.firstChild);", "bar.insertBefore(li, more);",
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

    public function testTheSlashKeyFocusesTheHeaderSearch()
    {
        $this->assertStringContainsString( '{if $box|eq( \'\' )} aria-keyshortcuts="/"{/if}', self::file( 'design/admin4/templates/page_search.tpl' ) );
        $js = self::file( 'design/admin4/javascript/admin4.js' );
        $this->assertStringContainsString( "if (e.key !== '/' || e.ctrlKey || e.metaKey || e.altKey || e.defaultPrevented) return;", $js );
        $this->assertStringContainsString( "/^(INPUT|TEXTAREA|SELECT)$/.test(t.tagName)", $js, 'typing a slash in a field stays a slash' );
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
