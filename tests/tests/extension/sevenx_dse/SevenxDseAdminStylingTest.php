<?php
/**
 * The Database Source Editor (sevenx_dse, dse/dashboard) follows the administration's styling rules
 * (doc/eng-us/bc/6.0/admin4-styling.md), checked on the files alone; no database.
 *
 * - No <style> block, no style="" attribute and no inline <script> body in any sevenx_dse template.
 * - The view and the embedded AdminNeo print no inline styles into the page (the editor's layout is in dse.css).
 * - design.ini [StylesheetSettings] BackendCSSFileList loads adminneo/adminneo-embedded.css and then dse.css, and both
 *   exist.
 * - dse.css: every rule is scoped to .a4-dse-*, colors and radii come from the --a4-* tokens only (no color literal,
 *   no radius but a token or 0), and the only custom properties it sets are AdminNeo's variables, each to a token.
 * - adminneo-embedded.css: every rule is scoped to the editor (.a4-dse-editor, the dark rules to
 *   html[data-a4-theme="dark"] .a4-dse-editor), and the file is what adminneo/build-embedded-css.php builds from
 *   AdminNeo's stylesheets now (it was rebuilt after they changed).
 * - The gate page uses the shared components and none of the old .exp-* look classes.
 * - Every view of the dse module sits in the Setup navigation part, so its left column is the kernel's Setup menu,
 *   which ends with the admin4 width switch (parts/left_width_control.tpl), and the menu links the dashboard.
 * - Every string of the gate page and of the "Change database" link is translated in eng-US and ger-DE.
 *
 * Skips when extension/sevenx_dse is not installed; SEVENX_DSE_EXTENSION_DIR=<dir> checks a clone instead.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/sevenx_dse/SevenxDseAdminStylingTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class SevenxDseAdminStylingTest extends PHPUnit\Framework\TestCase
{
    const MODULE_CSS = 'design/standard/stylesheets/dse.css';
    const EMBEDDED_CSS = 'design/standard/stylesheets/adminneo/adminneo-embedded.css';
    const ROOT = '.a4-dse-editor';
    const DARK_ROOT = 'html[data-a4-theme="dark"] .a4-dse-editor';

    private static $root;
    private static $ext;

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 4 );
        self::$ext = getenv( 'SEVENX_DSE_EXTENSION_DIR' ) ?: self::$root . '/extension/sevenx_dse';
    }

    protected function setUp(): void
    {
        if ( !is_dir( self::$ext . '/design/standard/templates/dse' ) )
        {
            $this->markTestSkipped( 'extension/sevenx_dse is not installed' );
        }
    }

    private static function read( $relative )
    {
        return (string)file_get_contents( self::$ext . '/' . $relative );
    }

    private static function templates()
    {
        $list = array();
        $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( self::$ext . '/design', FilesystemIterator::SKIP_DOTS ) );
        foreach ( $it as $file )
        {
            if ( preg_match( '/\.tpl~?$/', $file->getFilename() ) )
            {
                $list[] = substr( $file->getPathname(), strlen( self::$ext ) + 1 );
            }
        }
        sort( $list );
        return $list;
    }

    private static function withoutComments( $source )
    {
        return preg_replace( '/\{\*.*?\*\}/s', '', $source );
    }

    public static function inlineCssCount( $source )
    {
        $source = self::withoutComments( $source );
        return preg_match_all( '/<style\b/i', $source ) + preg_match_all( '/(?<![\w.\-])style\s*=\s*["\'{]/i', $source );
    }

    public static function inlineScriptCount( $source )
    {
        $source = self::withoutComments( $source );
        return preg_match_all( '/<script\b(?![^>]*\bsrc\s*=)[^>]*>\s*[^<\s]/i', $source );
    }

    /**
     * The rules of a stylesheet as [selectors, body] pairs, @media/@supports/@container blocks opened
     */
    public static function rules( $css )
    {
        $css = preg_replace( '~/\*.*?\*/~s', '', $css );
        $rules = array();
        $length = strlen( $css );
        $i = 0;
        while ( $i < $length )
        {
            $open = strpos( $css, '{', $i );
            if ( $open === false )
            {
                break;
            }
            $prelude = trim( substr( $css, $i, $open - $i ) );
            $depth = 0;
            for ( $close = $open; $close < $length; $close++ )
            {
                if ( $css[$close] === '{' )
                {
                    $depth++;
                }
                else if ( $css[$close] === '}' && --$depth === 0 )
                {
                    break;
                }
            }
            $body = substr( $css, $open + 1, $close - $open - 1 );
            $i = $close + 1;
            if ( preg_match( '/^@(media|supports|container)\b/i', $prelude ) )
            {
                $rules = array_merge( $rules, self::rules( $body ) );
            }
            else if ( $prelude !== '' && $prelude[0] !== '@' )
            {
                $rules[] = array( array_map( 'trim', explode( ',', $prelude ) ), $body );
            }
        }
        return $rules;
    }

    public function testCheckersCatchWhatTheyShould()
    {
        $this->assertSame( 2, self::inlineCssCount( '<style>a{}</style><p style="color:red">' ) );
        $this->assertSame( 0, self::inlineCssCount( '{* <p style="x"> *}<p data-style="x">' ) );
        $this->assertSame( 1, self::inlineScriptCount( '<script>go()</script><script src="x.js"></script>' ) );
        $this->assertSame( 0, self::inlineScriptCount( '<script src={\'javascript/a.js\'|ezdesign}></script>' ) );
        $rules = self::rules( '.a{color:red}@media (max-width:1px){.b,.c{margin:0}}' );
        $this->assertSame( array( array( '.a' ), array( '.b', '.c' ) ), array( $rules[0][0], $rules[1][0] ) );
    }

    public function testNoInlineCssOrScriptInAnyTemplate()
    {
        $templates = self::templates();
        $this->assertNotEmpty( $templates );
        $found = array();
        foreach ( $templates as $path )
        {
            $this->assertStringEndsNotWith( '~', $path, 'an editor backup file is shipped' );
            $source = self::read( $path );
            if ( self::inlineCssCount( $source ) || self::inlineScriptCount( $source ) )
            {
                $found[] = $path;
            }
        }
        $this->assertSame( array(), $found, 'templates with a <style> block, a style="" attribute or an inline <script> body' );
    }

    public function testTheViewAndAdminNeoPrintNoInlineStyles()
    {
        $view = self::read( 'classes/runnable/views/dse/dashboard.php' );
        $this->assertSame( 0, preg_match_all( '/style\s*=/i', $view ), 'the dashboard view prints a style attribute' );
        $this->assertStringContainsString( 'class="a4-dse-editor', $view );
        $design = self::read( 'adminneo/include/design.inc.php' );
        $this->assertStringNotContainsString( '$neo_embedded ?', $design, 'AdminNeo prints inline styles when embedded' );
        $this->assertStringNotContainsString( "navigation-button' style=", $design );
    }

    public function testStylesheetsAreListedAndExist()
    {
        $ini = self::read( 'settings/design.ini.append' );
        $this->assertMatchesRegularExpression( '/\[StylesheetSettings\][^\[]*BackendCSSFileList\[\]=adminneo\/adminneo-embedded\.css\s+BackendCSSFileList\[\]=dse\.css/', $ini );
        $this->assertFileExists( self::$ext . '/' . self::MODULE_CSS );
        $this->assertFileExists( self::$ext . '/' . self::EMBEDDED_CSS );
    }

    public function testModuleStylesheetIsScopedAndTokenOnly()
    {
        $css = self::read( self::MODULE_CSS );
        $plain = preg_replace( '~/\*.*?\*/~s', '', $css );
        $this->assertSame( 0, preg_match_all( '/#[0-9a-f]{3,8}\b(?![\w-])|\b(?:rgba?|hsla?)\s*\(|\b(?:white|black)\b/i', preg_replace( '/#(?:adminneo-root|navigation-panel|navigation-button|content|help)\b/', '', $plain ) ), 'a color literal in dse.css' );
        $rules = self::rules( $css );
        $this->assertNotEmpty( $rules );
        foreach ( $rules as $rule )
        {
            foreach ( $rule[0] as $selector )
            {
                $this->assertMatchesRegularExpression( '/^(?:html\[data-a4-theme="dark"\] )?\.a4-dse-/', $selector, 'a rule of dse.css outside .a4-dse-*' );
            }
            foreach ( array_filter( array_map( 'trim', explode( ';', $rule[1] ) ) ) as $declaration )
            {
                list( $property, $value ) = array_map( 'trim', explode( ':', $declaration, 2 ) );
                if ( preg_match( '/radius/', $property ) )
                {
                    $this->assertMatchesRegularExpression( '/^(?:0|var\(--a4-radius[\w-]*\))$/', $value, "$property: $value" );
                }
                if ( strpos( $property, '--' ) === 0 )
                {
                    $this->assertStringStartsNotWith( '--a4-', $property, 'dse.css defines a token of its own' );
                    $this->assertMatchesRegularExpression( '/^var\(--a4-[\w-]+\)$/', $value, "AdminNeo's $property is not set to a token" );
                }
            }
        }
    }

    public function testEmbeddedStylesheetIsScopedAndCurrent()
    {
        $css = self::read( self::EMBEDDED_CSS );
        foreach ( self::rules( $css ) as $rule )
        {
            foreach ( $rule[0] as $selector )
            {
                $this->assertTrue( strpos( $selector, self::ROOT ) === 0 || strpos( $selector, self::DARK_ROOT ) === 0,
                    "an AdminNeo rule that reaches outside the editor: $selector" );
            }
        }
        require_once self::$ext . '/adminneo/build-embedded-css.php';
        $dir = self::$ext . '/design/standard/stylesheets/adminneo/';
        foreach ( array( 'default-blue.css' => self::ROOT, 'jush.css' => self::ROOT, 'default-blue-dark.css' => self::DARK_ROOT, 'jush-dark.css' => self::DARK_ROOT ) as $file => $root )
        {
            $this->assertStringContainsString( "/* $file */\n" . \AdminNeo\dse_css_scope( (string)file_get_contents( $dir . $file ), $root ), $css,
                "adminneo-embedded.css is not rebuilt from $file (php adminneo/build-embedded-css.php)" );
        }
    }

    public function testGatePageUsesTheSharedComponents()
    {
        $gate = self::withoutComments( self::read( 'design/standard/templates/dse/gate.tpl' ) );
        foreach ( array( 'a4-page', 'a4-notice is-bad', 'a4-figures', 'a4-cards', 'a4-check', 'a4-toolbar', 'a4-btn a4-btn-danger' ) as $class )
        {
            $this->assertStringContainsString( $class, $gate );
        }
        $this->assertSame( 0, preg_match_all( '/class="[^"]*\bexp-/', $gate ), 'old .exp-* look classes on the gate page' );
        $this->assertStringNotContainsString( 'exp_style.tpl', $gate );
        $this->assertFileDoesNotExist( self::$ext . '/design/standard/templates/dse/exp_style.tpl' );
        // the acknowledgement form posts as before
        $this->assertStringContainsString( 'name="dse_ack_warning" value="1"', $gate );
        $this->assertStringContainsString( 'id="dse-ack-backup"', $gate );
    }

    public function testViewsUseTheSetupMenuWithTheWidthSwitch()
    {
        $module = self::read( 'modules/dse/module.php' );
        $this->assertSame( 2, substr_count( $module, "'default_navigation_part' => 'ezsetupnavigationpart'" ) );
        $this->assertStringContainsString( 'Links[dse]=dse/dashboard', self::read( 'settings/menu.ini.append' ) );
        $menu = self::$root . '/design/admin4/templates/parts/setup/menu.tpl';
        if ( !is_file( $menu ) )
        {
            $this->markTestSkipped( 'no admin4 design in this installation' );
        }
        $this->assertStringContainsString( "{include uri='design:parts/left_width_control.tpl'}", (string)file_get_contents( $menu ) );
    }

    public function testStringsAreTranslated()
    {
        preg_match_all( "/\\{'((?:[^'\\\\]|\\\\.)+)'\\|i18n\\( 'extension\\/sevenx_dse'/", self::read( 'design/standard/templates/dse/gate.tpl' ), $m );
        $strings = array_merge( array_unique( $m[1] ), array( 'Change database', 'Return to the database selector' ) );
        $this->assertGreaterThan( 20, count( $strings ) );
        foreach ( array( 'eng-US', 'ger-DE' ) as $language )
        {
            $ts = self::read( "translations/$language/translation.ts" );
            foreach ( $strings as $string )
            {
                $this->assertStringContainsString( '<source>' . htmlspecialchars( $string, ENT_NOQUOTES ) . '</source>', $ts, "$language lacks: $string" );
            }
        }
    }
}
