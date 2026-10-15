<?php
/**
 * admin4 has one density. The compact view and its header switch were removed (October 2026, "one well-tuned
 * density"): no switch in the header, no script that applies a density, no html[data-a4-density] rule in any admin4
 * stylesheet, no compact value of a token. The tokens the density used to set stay as single values in the normal
 * token block (cards, cells, lists, controls, gutter). A value an earlier version kept in the browser is dropped,
 * a stored eZPreference admin_density is no longer read.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminDensityTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminDensityTest extends PHPUnit\Framework\TestCase
{
    /** The tokens the compact view used to set anew; each now has one value */
    const SPACING_TOKENS = array( '--a4-gutter' => 'var(--a4-space-5)', '--a4-control-h' => '32px', '--a4-list-pad' => 'var(--a4-cell-pad-y) var(--a4-cell-pad-x)',
                                  '--a4-cell-pad-y' => 'var(--a4-space-2)', '--a4-cell-pad-x' => 'var(--a4-space-3)',
                                  '--a4-card-pad-y' => 'var(--a4-space-3)', '--a4-card-pad-x' => 'var(--a4-space-4)' );

    private static function file( $path )
    {
        return (string)file_get_contents( dirname( __DIR__, 3 ) . '/' . $path );
    }

    public function testNoStylesheetHasADensityRule()
    {
        $root = dirname( __DIR__, 3 );
        foreach ( array_merge( glob( "$root/design/admin4/stylesheets/{,*/}*.css", GLOB_BRACE ) ?: array(),
                               array( "$root/design/admin/stylesheets/components.css" ) ) as $file )
        {
            $css = preg_replace( '#/\*.*?\*/#s', '', (string)file_get_contents( $file ) );
            $this->assertStringNotContainsString( 'data-a4-density', $css, basename( $file ) . ' styles a density that no longer exists' );
            $this->assertStringNotContainsString( 'a4-density-toggle', $css, basename( $file ) );
        }
    }

    public function testEachSpacingTokenHasOneValue()
    {
        $components = preg_replace( '#/\*.*?\*/#s', '', self::file( 'design/admin4/stylesheets/components.css' ) );
        foreach ( self::SPACING_TOKENS as $token => $value )
        {
            $this->assertSame( 1, preg_match_all( '/^\s*' . preg_quote( $token, '/' ) . ':/m', $components ), "$token is defined once" );
            $this->assertMatchesRegularExpression( '/^\s*' . preg_quote( $token, '/' ) . ':\s*' . preg_quote( $value, '/' ) . ';/m', $components, "$token is $value" );
        }
        $this->assertFileEquals( dirname( __DIR__, 3 ) . '/design/admin4/stylesheets/components.css', dirname( __DIR__, 3 ) . '/design/admin/stylesheets/components.css' );
    }

    public function testTablesAndCardsReadTheTokens()
    {
        $components = self::file( 'design/admin4/stylesheets/components.css' );
        $this->assertMatchesRegularExpression( '/\.a4-table td\s*\{\s*padding: var\(--a4-cell-pad-y\) var\(--a4-cell-pad-x\);/', $components );
        $this->assertMatchesRegularExpression( '/\.a4-card \{[^}]*padding: var\(--a4-card-pad-y\) var\(--a4-card-pad-x\);/', $components );
        $this->assertStringContainsString( 'table.list th, table.list td { padding: var(--a4-list-pad); }', self::file( 'design/admin4/stylesheets/admin4.css' ) );
    }

    public function testTheHeaderHasNoDensitySwitch()
    {
        foreach ( array( 'design/admin4/templates/page_topmenu.tpl', 'design/admin4l/templates/explayouts/block/admin_theme_switch.tpl' ) as $path )
        {
            $tpl = self::file( $path );
            $this->assertStringNotContainsString( 'a4-density-toggle', $tpl, $path );
            $this->assertStringNotContainsString( "'Compact view'", $tpl, $path );
        }
        $this->assertStringNotContainsString( "'admin_density'", self::file( 'design/admin4/templates/parts/a4_preference_attributes.tpl' ), 'the preference is no longer read' );
    }

    public function testTheHeadScriptOnlyDropsAnOldStoredValue()
    {
        $head = self::file( 'design/admin4/templates/page_head_style.tpl' );
        $this->assertStringNotContainsString( "setAttribute('data-a4-density'", $head );
        $this->assertStringNotContainsString( "a4Pref.get('admin_density'", $head );
        $this->assertStringNotContainsString( "a4Pref.set('admin_density'", $head );
        // a value an earlier version kept in this browser is removed, guarded for blocked storage
        $this->assertStringContainsString( "try { window.localStorage.removeItem('exp-admin4-density'); } catch (e) {", $head );
    }

    public function testEveryControlHasTheOneControlHeight()
    {
        $css = preg_replace( '#/\*.*?\*/#s', '', self::file( 'design/admin4/stylesheets/components.css' ) );
        foreach ( array( '.a4-btn {', ".a4-input,\n.a4-field select,", '.a4-chip span {', ".a4-tabs a,\n.a4-tabs button,", ".a4-sizes a,\n.a4-sizes span.current {" ) as $start )
        {
            $at = strpos( $css, $start );
            $this->assertNotFalse( $at, "no rule $start" );
            $rule = substr( $css, $at, strpos( $css, '}', $at ) - $at );
            $this->assertStringContainsString( 'min-height: var(--a4-control-h);', $rule, "$start is not one control high (32px)" );
        }
        // field text as the components' fields: the legacy text fields too
        $this->assertStringContainsString( "font-size: var(--a4-text-m);   /* field text as .a4-input's */", self::file( 'design/admin4/stylesheets/admin4.css' ) );
    }
}
