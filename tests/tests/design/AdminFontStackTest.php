<?php
/**
 * admin4's typeface: --a4-font is the Helvetica Neue stack with its emoji fallbacks, and every text rule of the
 * admin4 stylesheets names var(--a4-font) (or var(--a4-mono) for code) instead of a font list of its own. Icon
 * fonts (IcoMoon), inherit and a var() fallback (core.css) stay allowed.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminFontStackTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminFontStackTest extends PHPUnit\Framework\TestCase
{
    const STACK = '"Helvetica Neue", "Noto Sans", "Liberation Sans", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"';

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function stylesheets()
    {
        $files = array();
        foreach ( array( 'design/admin4/stylesheets', 'design/admin4l/stylesheets' ) as $dir )
        {
            if ( !is_dir( self::root() . '/' . $dir ) )
            {
                continue;
            }
            $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( self::root() . '/' . $dir, FilesystemIterator::SKIP_DOTS ) );
            foreach ( $it as $file )
            {
                if ( substr( $file->getFilename(), -4 ) === '.css' && substr( $file->getFilename(), -8 ) !== '.min.css' )
                {
                    $files[] = substr( $file->getPathname(), strlen( self::root() ) + 1 );
                }
            }
        }
        sort( $files );
        return $files;
    }

    public function testTheFontTokenIsTheHelveticaNeueStack()
    {
        foreach ( array( 'design/admin4/stylesheets/components.css', 'design/admin/stylesheets/components.css' ) as $path )
        {
            $css = (string)file_get_contents( self::root() . '/' . $path );
            $this->assertSame( 1, preg_match_all( '/--a4-font:\s*([^;]+);/', $css, $m ), "$path declares --a4-font once" );
            $this->assertSame( self::STACK, trim( $m[1][0] ), "$path: --a4-font" );
        }
    }

    public function testTextRulesUseTheFontTokens()
    {
        $problems = array();
        $files = self::stylesheets();
        $this->assertNotEmpty( $files );
        foreach ( $files as $path )
        {
            $css = preg_replace( '#/\*.*?\*/#s', '', (string)file_get_contents( self::root() . '/' . $path ) );
            preg_match_all( '/(?<![\w-])font-family\s*:\s*([^;}]+)/i', $css, $m );
            foreach ( $m[1] as $value )
            {
                $value = trim( preg_replace( '/\s*!important\s*$/i', '', trim( $value ) ) );
                // core.css is also loaded by the standard popup, print and redirect layouts, which do not load
                // components.css: there the token carries a fallback after the comma
                if ( preg_match( '/^(var\(--a4-(font|mono)(,[^()]*)?\)|inherit)$/', $value ) || stripos( $value, 'IcoMoon' ) !== false )
                {
                    continue;
                }
                $problems[] = "$path: font-family: $value";
            }
        }
        $this->assertSame( array(), $problems, 'use var(--a4-font) for text and var(--a4-mono) for code' );
    }

    public function testTheBodyAndTheEditorContentUseTheFontToken()
    {
        $core = (string)file_get_contents( self::root() . '/design/admin4/stylesheets/core.css' );
        $this->assertMatchesRegularExpression( '/^body\b[^{]*\{[^}]*font-family:\s*var\(--a4-font[,)]/m', $core );
        $editor = (string)file_get_contents( self::root() . '/design/admin4/stylesheets/editor_content.css' );
        $this->assertMatchesRegularExpression( '/body:is\(\.mceContentBody, \.mce-content-body\) \{[^}]*font-family:\s*var\(--a4-font\)/', $editor );
    }
}
