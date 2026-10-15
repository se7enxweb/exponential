<?php
/**
 * admin4's typeface: --a4-font is the Helvetica Neue stack with its emoji fallbacks, and every text rule of the
 * admin4 stylesheets names var(--a4-font) (or var(--a4-mono) for code) instead of a font list of its own. Icon
 * fonts (IcoMoon), inherit and a var() fallback (core.css) stay allowed. The emoji faces follow the text faces, and
 * no font is downloaded: the one @font-face is the icon font from the design's folder.
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

    public function testTheEmojiFacesComeAfterTheTextFaces()
    {
        $faces = array_map( 'trim', explode( ',', self::STACK ) );
        $generic = array_search( 'sans-serif', $faces, true );
        $this->assertSame( array( '"Helvetica Neue"', '"Noto Sans"', '"Liberation Sans"', 'Arial', 'sans-serif' ), array_slice( $faces, 0, $generic + 1 ),
            'the text faces first, the generic family last of them, so a text character never takes an emoji face' );
        $this->assertSame( array( '"Apple Color Emoji"', '"Segoe UI Emoji"', '"Segoe UI Symbol"', '"Noto Color Emoji"' ), array_slice( $faces, $generic + 1 ),
            'then the color emoji of macOS, Windows (its color face before its monochrome symbols) and Linux' );
    }

    /** System fonts only: the one @font-face of admin4 is the IcoMoon icon font, from the design's own folder; no
        stylesheet or template of admin4 or admin4l loads a font from elsewhere */
    public function testNoWebFontIsDownloaded()
    {
        $faces = array();
        foreach ( self::stylesheets() as $path )
        {
            $css = preg_replace( '#/\*.*?\*/#s', '', (string)file_get_contents( self::root() . '/' . $path ) );
            preg_match_all( '/@font-face\s*\{([^}]*)\}/i', $css, $m );
            foreach ( $m[1] as $body )
            {
                $this->assertMatchesRegularExpression( '/font-family:\s*[\'"]?IcoMoon/i', $body, "$path: only the icon font" );
                preg_match_all( '/url\(\s*[\'"]?([^\'")]+)/i', $body, $urls );
                foreach ( $urls[1] as $url )
                    $this->assertStringStartsWith( '../fonts/', $url, "$path: the icon font from the design's folder" );
                $faces[] = $path;
            }
            $this->assertDoesNotMatchRegularExpression( '/@import[^;]*(font|googleapis)/i', $css, "$path imports no font" );
        }
        $this->assertSame( array( 'design/admin4/stylesheets/core.css' ), $faces );

        foreach ( array( 'design/admin4/templates', 'design/admin4l/templates' ) as $dir )
        {
            $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( self::root() . '/' . $dir, FilesystemIterator::SKIP_DOTS ) );
            foreach ( $it as $file )
            {
                if ( substr( $file->getFilename(), -4 ) !== '.tpl' )
                    continue;
                $this->assertDoesNotMatchRegularExpression( '#fonts\.(googleapis|gstatic|bunny)|use\.typekit|\.woff2?\b#i',
                    (string)file_get_contents( $file->getPathname() ), $file->getPathname() . ' loads no web font' );
            }
        }
    }

    public function testTheBodyAndTheEditorContentUseTheFontToken()
    {
        $core = (string)file_get_contents( self::root() . '/design/admin4/stylesheets/core.css' );
        $this->assertMatchesRegularExpression( '/^body\b[^{]*\{[^}]*font-family:\s*var\(--a4-font[,)]/m', $core );
        $editor = (string)file_get_contents( self::root() . '/design/admin4/stylesheets/editor_content.css' );
        $this->assertMatchesRegularExpression( '/body:is\(\.mceContentBody, \.mce-content-body\) \{[^}]*font-family:\s*var\(--a4-font\)/', $editor );
    }
}
