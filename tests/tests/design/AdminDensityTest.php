<?php
/**
 * admin4's density switch: html[data-a4-density="compact"] sets only the density tokens anew, keeps every target at
 * 24px or more (WCAG 2.5.8) and leaves the content tree's row pitch alone; the header button and the script that
 * applies the choice before the page is drawn, like the light/dark mode.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminDensityTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminDensityTest extends PHPUnit\Framework\TestCase
{
    /** The tokens the compact block may set; anything else would make the density change more than spacing */
    const DENSITY_TOKENS = array( '--a4-gutter', '--a4-control-h', '--a4-list-pad', '--a4-cell-pad-y', '--a4-cell-pad-x', '--a4-card-pad-y', '--a4-card-pad-x' );

    private static function file( $path )
    {
        return (string)file_get_contents( dirname( __DIR__, 3 ) . '/' . $path );
    }

    /** @return array<string,string> the declarations of the compact block */
    private static function compactBlock()
    {
        $css = preg_replace( '#/\*.*?\*/#s', '', self::file( 'design/admin4/stylesheets/components.css' ) );
        if ( !preg_match( '/html\[data-a4-density="compact"\]\s*\{([^}]*)\}/', $css, $m ) )
            return array();
        $declarations = array();
        foreach ( array_filter( array_map( 'trim', explode( ';', $m[1] ) ) ) as $declaration )
        {
            list( $property, $value ) = array_map( 'trim', explode( ':', $declaration, 2 ) );
            $declarations[$property] = $value;
        }
        return $declarations;
    }

    /** @return array<string,int> the px value of every --a4-space-N token */
    private static function spaceScale()
    {
        preg_match_all( '/(--a4-space-\d+):\s*(\d+)px/', self::file( 'design/admin4/stylesheets/components.css' ), $m, PREG_SET_ORDER );
        $scale = array();
        foreach ( $m as $token )
            $scale[$token[1]] = (int)$token[2];
        return $scale;
    }

    public function testTheCompactBlockSetsOnlyTheDensityTokens()
    {
        $block = self::compactBlock();
        $this->assertNotEmpty( $block, 'components.css has no html[data-a4-density="compact"] block' );
        foreach ( array_keys( $block ) as $property )
            $this->assertContains( $property, self::DENSITY_TOKENS, "the compact block sets $property, which is not a density token" );
        foreach ( self::DENSITY_TOKENS as $token )
        {
            $this->assertArrayHasKey( $token, $block, "the compact block does not set $token" );
            $this->assertMatchesRegularExpression( '/^\s*' . preg_quote( $token, '/' ) . ':/m', self::file( 'design/admin4/stylesheets/components.css' ), "$token has no normal value" );
        }
    }

    public function testCompactControlsKeepTheir24pxTarget()
    {
        $value = self::compactBlock()['--a4-control-h'] ?? '';
        $scale = self::spaceScale();
        // the value is a sum of space tokens (calc( var(--a4-space-N) + var(--a4-space-M) )) or a px length
        if ( preg_match_all( '/var\((--a4-space-\d+)\)/', $value, $m ) )
            $px = array_sum( array_map( function ( $token ) use ( $scale ) { return $scale[$token] ?? 0; }, $m[1] ) );
        else
            $px = (int)$value;
        $this->assertGreaterThanOrEqual( 24, $px, "compact --a4-control-h is $px px, below the 24px target of WCAG 2.5.8" );
    }

    public function testTheContentTreeKeepsItsRowPitch()
    {
        $root = dirname( __DIR__, 3 );
        foreach ( glob( "$root/design/admin4/stylesheets/{,*/}*.css", GLOB_BRACE ) ?: array() as $file )
        {
            $css = preg_replace( '#/\*.*?\*/#s', '', (string)file_get_contents( $file ) );
            preg_match_all( '/([^{}]*data-a4-density[^{}]*)\{/', $css, $m );
            foreach ( $m[1] as $selector )
            {
                $this->assertDoesNotMatchRegularExpression( '/content_tree_menu|contentstructure/', $selector,
                    basename( $file ) . ': the density must not tighten the content tree below its 24px row pitch' );
                $this->assertSame( 'html[data-a4-density="compact"]', trim( $selector ),
                    basename( $file ) . ': density is set only by the token block, rules read the tokens' );
            }
        }
    }

    public function testTheNormalDensityKeepsTheEarlierSpacing()
    {
        $components = self::file( 'design/admin4/stylesheets/components.css' );
        $normal = array( '--a4-list-pad' => '.4em', '--a4-cell-pad-y' => 'var(--a4-space-2)', '--a4-cell-pad-x' => 'var(--a4-space-3)',
                         '--a4-card-pad-y' => 'var(--a4-space-3)', '--a4-card-pad-x' => 'var(--a4-space-4)', '--a4-control-h' => '32px' );
        foreach ( $normal as $token => $value )
            $this->assertMatchesRegularExpression( '/^\s*' . preg_quote( $token, '/' ) . ':\s*' . preg_quote( $value, '/' ) . ';/m', $components,
                "$token must stay $value in the normal density (it is what the lists, tables, cards and controls had)" );
        $scale = self::spaceScale();
        $this->assertSame( array( 8, 12, 16 ), array( $scale['--a4-space-2'], $scale['--a4-space-3'], $scale['--a4-space-4'] ) );
    }

    public function testTablesAndCardsReadTheTokens()
    {
        $components = self::file( 'design/admin4/stylesheets/components.css' );
        $this->assertMatchesRegularExpression( '/\.a4-table td\s*\{\s*padding: var\(--a4-cell-pad-y\) var\(--a4-cell-pad-x\);/', $components );
        $this->assertMatchesRegularExpression( '/\.a4-card \{[^}]*padding: var\(--a4-card-pad-y\) var\(--a4-card-pad-x\);/', $components );
        $this->assertStringContainsString( 'table.list th, table.list td { padding: var(--a4-list-pad); }', self::file( 'design/admin4/stylesheets/admin4.css' ) );
        $this->assertFileEquals( dirname( __DIR__, 3 ) . '/design/admin4/stylesheets/components.css', dirname( __DIR__, 3 ) . '/design/admin/stylesheets/components.css' );
    }

    public function testTheSwitchIsAPressedButtonAppliedBeforeTheFirstPaint()
    {
        $menu = self::file( 'design/admin4/templates/page_topmenu.tpl' );
        $this->assertMatchesRegularExpression( '/<button type="button" id="a4-density-toggle"[^>]*aria-pressed="false"/', $menu );
        $this->assertStringContainsString( "{'Compact view'|i18n( 'design/admin/pagelayout' )}", $menu, 'the button needs one fixed name; aria-pressed carries the state' );

        $head = self::file( 'design/admin4/templates/page_head_style.tpl' );
        $this->assertStringContainsString( "'exp-admin4-density'", $head );
        $this->assertStringContainsString( "root.setAttribute('data-a4-density'", $head );
        $this->assertStringContainsString( "function stored() { return window.a4Pref.get('admin_density', KEY); }", $head );
        $this->assertStringContainsString( "window.a4Pref.set('admin_density', next, KEY);", $head );
        $this->assertLessThan( strpos( $head, '{/literal}' ), strpos( $head, 'exp-admin4-density' ), 'the density script must sit in the head script, before the page is drawn' );
    }

    public function testTheTextsAreTranslated()
    {
        foreach ( array( 'eng-US', 'ger-DE', 'untranslated' ) as $locale )
        {
            $ts = self::file( "share/translations/$locale/translation.ts" );
            foreach ( array( 'Compact view', 'Switch between normal and compact view' ) as $source )
                $this->assertStringContainsString( "<source>$source</source>", $ts, "$locale lacks \"$source\"" );
        }
    }
}
