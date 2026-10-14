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
        // the title names the next step, a press is announced by the status region, other tabs follow
        $this->assertStringContainsString( "b.getAttribute('data-title-' + choice)", $js );
        $this->assertStringContainsString( "document.getElementById('a4-theme-status')", $js );
        $this->assertStringContainsString( "window.addEventListener('storage'", $js );
    }

    public function testEverySwitchOffersAndShowsTheSystemMode()
    {
        foreach ( self::SWITCHES as $path )
        {
            $tpl = self::read( $path );
            // the name says the state ("Colour mode: ..."), the title the next step
            foreach ( array( 'light', 'dark', 'system' ) as $mode )
            {
                $this->assertMatchesRegularExpression( "/data-label-$mode=\"\{'Colour mode: $mode'/", $tpl, $path );
                $this->assertStringContainsString( "data-title-$mode=", $tpl, $path );
            }
            // all three icons are admin icons (masks in the text colour, also in dark and forced colours)
            foreach ( array( 'sun' => 'sun', 'moon' => 'moon', 'system' => 'circle-half' ) as $icon => $bi )
                $this->assertMatchesRegularExpression( '/<span class="a4-icon a4-bi-' . $bi . ' a4-icon-' . $icon . '" aria-hidden="true"><\/span>/', $tpl, $path );
            $this->assertStringNotContainsString( '<svg', $tpl, $path );
            $this->assertStringContainsString( '<span id="a4-theme-status" class="a4-sr-only" role="status"></span>', $tpl, $path );
            // three states: aria-pressed (two states) would announce the wrong thing
            // (the density switch beside it is a two-state toggle and keeps its aria-pressed)
            $this->assertMatchesRegularExpression( '/<button type="button" id="a4-theme-toggle"[^>]*>/', $tpl, $path );
            preg_match( '/<button type="button" id="a4-theme-toggle"[^>]*>/', $tpl, $button );
            $this->assertStringNotContainsString( 'aria-pressed', $button[0] ?? '', $path );
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
