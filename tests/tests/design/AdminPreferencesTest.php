<?php
/**
 * admin4's remembered choices (color mode, the side columns on a wide window, the closed groups of the left
 * menus) go through one helper, window.a4Pref, defined first in the head script. For a signed-in user each is an
 * eZPreference: parts/a4_preference_attributes.tpl writes them on <html> (both page layouts, outside any
 * cache-block) and the helper saves them through user/preferences/set_and_exit. An anonymous page keeps them in the
 * browser only. No script of these features reaches localStorage on its own.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminPreferencesTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminPreferencesTest extends PHPUnit\Framework\TestCase
{
    const NAMES = array( 'admin_color_mode', 'admin_left_sidebar', 'admin_right_sidebar', 'admin_navgroups' );

    private static function read( $path )
    {
        return (string)file_get_contents( dirname( __DIR__, 3 ) . '/' . $path );
    }

    public function testTheAttributesComeFromTheUsersPreferences()
    {
        $tpl = self::read( 'design/admin4/templates/parts/a4_preference_attributes.tpl' );
        $this->assertStringContainsString( "{if fetch( 'user', 'current_user' ).is_logged_in} data-a4-pref-url=\"{'/user/preferences/set_and_exit'|ezurl( 'no' )|wash}\"", $tpl );
        $this->assertStringContainsString( 'data-a4-pref-{$a4_pref|explode( \'_\' )|implode( \'-\' )}="{ezpreference( $a4_pref )|wash}"', $tpl );
        foreach ( self::NAMES as $name )
            $this->assertStringContainsString( "'$name'", $tpl );
        foreach ( array( 'admin4', 'admin4l' ) as $design )
        {
            $layout = self::read( "design/$design/templates/pagelayout.tpl" );
            $this->assertMatchesRegularExpression( "/<html [^\\n]*\\{include uri='design:parts\\/a4_preference_attributes\\.tpl'\\}>/", $layout, $design );
            // on <html>, before the first cache-block: every user gets their own values
            $this->assertLessThan( strpos( $layout, '{cache-block' ), strpos( $layout, 'a4_preference_attributes' ), $design );
        }
    }

    public function testOneHelperReadsAndSavesThem()
    {
        $head = self::read( 'design/admin4/templates/page_head_style.tpl' );
        $start = strpos( $head, 'window.a4Pref = {' );
        $this->assertNotFalse( $start );
        // the helper comes before the scripts that use it
        $this->assertLessThan( strpos( $head, "window.a4Pref.get('admin_color_mode'" ), $start );
        $this->assertStringContainsString( "root.getAttribute('data-a4-pref-url')", $head );
        $this->assertStringContainsString( "'data-a4-pref-' + name.replace(/_/g, '-')", $head );
        $this->assertStringContainsString( "method: 'POST', credentials: 'same-origin'", $head );
        $this->assertStringContainsString( "'ezxform_token=' + encodeURIComponent(token)", $head );
        // localStorage only for an anonymous page (no data-a4-pref-url) and once, to take an old choice along
        $this->assertStringContainsString( "if (!url) return local(legacyKey || 'exp-a4-' + name);", $head );
        $this->assertStringContainsString( "if (!url) { local(legacyKey || 'exp-a4-' + name, value); return; }", $head );
        $this->assertSame( 1, substr_count( $head, 'window.localStorage.setItem(' ), 'only the helper writes to localStorage' );
    }

    public function testEveryFeatureUsesTheHelper()
    {
        $head = self::read( 'design/admin4/templates/page_head_style.tpl' );
        $this->assertStringContainsString( "window.a4Pref.set('admin_color_mode', next, KEY)", $head );
        $menubar = self::read( 'design/admin4/javascript/ezadmin_menubar.js' );
        $this->assertStringContainsString( 'const prefs = { left: "admin_left_sidebar", right: "admin_right_sidebar" };', $menubar );
        $this->assertStringContainsString( 'pref.set(prefs[side], open ? "1" : "0");', $menubar );
        $this->assertStringNotContainsString( 'localStorage.setItem', $menubar );
        $admin4 = self::read( 'design/admin4/javascript/admin4.js' );
        $this->assertStringContainsString( "var NAME = 'admin_navgroups';", $admin4 );
        $this->assertStringContainsString( 'window.a4Pref.set(NAME, list.join(\',\'))', $admin4 );
        $this->assertStringNotContainsString( 'localStorage', $admin4 );
    }
}
