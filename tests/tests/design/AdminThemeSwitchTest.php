<?php
/**
 * The header's mode switch steps light -> dark -> system: "system" follows prefers-color-scheme, the stylesheets keep
 * reading html[data-a4-theme] (light|dark), the switch shows the chosen mode by html[data-a4-theme-choice], and a
 * copy of the switch without data-label-system keeps stepping between light and dark.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminThemeSwitchTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminThemeSwitchTest extends PHPUnit\Framework\TestCase
{
    const SWITCHES = array( 'design/admin4/templates/page_topmenu.tpl', 'design/admin4l/templates/explayouts/block/admin_theme_switch.tpl' );

    private static function read( $path )
    {
        return (string)file_get_contents( dirname( __DIR__, 3 ) . '/' . $path );
    }

    public function testTheScriptKnowsTheSystemMode()
    {
        $js = self::read( 'design/admin4/templates/page_head_style.tpl' );
        $this->assertStringContainsString( "matchMedia( '(prefers-color-scheme: dark)' )", $js );
        $this->assertStringContainsString( "root.setAttribute('data-a4-theme', dark ? 'dark' : 'light')", $js,
            'the stylesheets read data-a4-theme, which must stay light or dark' );
        $this->assertStringContainsString( "root.setAttribute('data-a4-theme-choice', choice)", $js );
        // the system mode follows a change of the system while the page is open
        $this->assertMatchesRegularExpression( "/addEventListener\\('change', follow\\)/", $js );
        // light is still the default without a stored choice
        $this->assertStringContainsString( "value === 'dark' || value === 'system' ? value : 'light'", $js );
        // an older copy of the switch (no data-label-system) keeps stepping between light and dark
        $this->assertStringContainsString( "b.hasAttribute('data-label-system')", $js );
    }

    public function testEverySwitchOffersAndShowsTheSystemMode()
    {
        foreach ( self::SWITCHES as $path )
        {
            $tpl = self::read( $path );
            $this->assertStringContainsString( 'data-label-system=', $tpl, $path );
            $this->assertStringContainsString( 'a4-icon-system', $tpl, $path );
            // three states: aria-pressed (two states) would announce the wrong thing
            $this->assertStringNotContainsString( 'aria-pressed', $tpl, $path );
        }
    }

    public function testTheIconFollowsTheChoice()
    {
        $css = self::read( 'design/admin4/stylesheets/admin4.css' );
        foreach ( array( 'html[data-a4-theme-choice="dark"] .a4-theme-toggle .a4-icon-moon',
                         'html[data-a4-theme-choice="system"] .a4-theme-toggle .a4-icon-system' ) as $selector )
            $this->assertStringContainsString( $selector, $css );
        $components = self::read( 'design/admin4/stylesheets/components.css' );
        $this->assertStringContainsString( '.a4-bi-circle-half {', $components );
        $this->assertFileExists( dirname( __DIR__, 3 ) . '/share/icons/bootstrap-icons/svg/circle-half.svg' );
    }
}
