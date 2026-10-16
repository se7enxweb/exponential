<?php
/**
 * admin4's typeface: --a4-font is the Helvetica Neue stack with the system fonts of other scripts (--a4-font-intl:
 * CJK, Thai, Devanagari, Arabic, Hebrew) between the Latin faces and the generic family, and its emoji fallbacks after
 * it; every text rule of the
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
    const STACK = '"Helvetica Neue", "Noto Sans", "Liberation Sans", Arial, var(--a4-font-intl), sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"';

    /** The Latin text faces, in this order, before anything else */
    const LATIN = array( '"Helvetica Neue"', '"Noto Sans"', '"Liberation Sans"', 'Arial' );

    /** Generic families and keywords: none of them may sit in the fallback lists of other scripts (a generic family
        there would end the stack early and hide the faces after it) */
    const GENERIC = array( 'serif', 'sans-serif', 'monospace', 'cursive', 'fantasy', 'system-ui', 'ui-serif', 'ui-sans-serif',
        'ui-monospace', 'ui-rounded', 'emoji', 'math', 'fangsong', 'inherit', 'initial', 'unset', 'revert' );

    /** Splits a font list at its top-level commas */
    private static function faces( $list )
    {
        return array_values( array_filter( array_map( 'trim', explode( ',', $list ) ), 'strlen' ) );
    }

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
        $this->assertSame( array( '"Helvetica Neue"', '"Noto Sans"', '"Liberation Sans"', 'Arial', 'var(--a4-font-intl)', 'sans-serif' ), array_slice( $faces, 0, $generic + 1 ),
            'the text faces first (Latin, then the other scripts), the generic family last of them, so a text character never takes an emoji face' );
        $this->assertSame( array( '"Apple Color Emoji"', '"Segoe UI Emoji"', '"Segoe UI Symbol"', '"Noto Color Emoji"' ), array_slice( $faces, $generic + 1 ),
            'then the color emoji of macOS, Windows (its color face before its monochrome symbols) and Linux' );
    }

    /** The system fonts of scripts other than Latin (CJK, Thai, Devanagari, Arabic, Hebrew) sit between the Latin
        faces and the generic family: Latin text keeps its face (a face is only used for the letters the faces before
        it lack), and a CJK or Thai letter finds a named face before the browser's generic fallback. --a4-font-intl
        is declared once, starts with --a4-font-cjk, and names fonts only; --a4-font-cjk is reordered by interface
        language only (:root:lang()), never by redeclaring --a4-font */
    public function testTheOtherScriptsComeAfterTheLatinFacesAndBeforeTheGenericFamily()
    {
        $emoji = array( '"Apple Color Emoji"', '"Segoe UI Emoji"', '"Segoe UI Symbol"', '"Noto Color Emoji"' );
        foreach ( array( 'design/admin4/stylesheets/components.css', 'design/admin/stylesheets/components.css' ) as $path )
        {
            $css = preg_replace( '#/\*.*?\*/#s', '', (string)file_get_contents( self::root() . '/' . $path ) );

            $this->assertSame( 1, preg_match_all( '/--a4-font-intl:\s*([^;]+);/', $css, $m ), "$path declares --a4-font-intl once" );
            $intl = self::faces( $m[1][0] );
            $this->assertSame( 'var(--a4-font-cjk)', $intl[0], "$path: --a4-font-intl starts with the CJK faces" );

            preg_match_all( '/([^{}]+)\{([^{}]*)\}/', $css, $rules, PREG_SET_ORDER );
            $cjk = array();
            foreach ( $rules as $rule )
            {
                if ( !preg_match( '/--a4-font-cjk:\s*([^;]+);/', $rule[2], $d ) )
                {
                    continue;
                }
                $selector = trim( preg_replace( '/\s+/', ' ', $rule[1] ) );
                $cjk[$selector] = self::faces( $d[1] );
                if ( $selector !== ':root' )
                {
                    foreach ( array_map( 'trim', explode( ',', $selector ) ) as $part )
                        $this->assertMatchesRegularExpression( '/^:root:lang\([A-Za-z-]+\)$/', $part, "$path: --a4-font-cjk is reordered by interface language only" );
                    $this->assertDoesNotMatchRegularExpression( '/--a4-font(-intl)?:/', $rule[2], "$path: $selector reorders --a4-font-cjk, not the stack" );
                }
            }
            $this->assertArrayHasKey( ':root', $cjk, "$path declares --a4-font-cjk on :root" );
            $this->assertGreaterThanOrEqual( 3, count( $cjk ), "$path: Japanese, Korean and Traditional Chinese have their own order" );

            foreach ( $cjk as $selector => $cjkFaces )
            {
                $resolved = array();
                foreach ( self::faces( self::STACK ) as $face )
                {
                    if ( $face === 'var(--a4-font-intl)' )
                    {
                        foreach ( $intl as $intlFace )
                            $resolved = array_merge( $resolved, $intlFace === 'var(--a4-font-cjk)' ? $cjkFaces : array( $intlFace ) );
                        continue;
                    }
                    $resolved[] = $face;
                }
                $generic = array_search( 'sans-serif', $resolved, true );
                $this->assertSame( self::LATIN, array_slice( $resolved, 0, count( self::LATIN ) ), "$path ($selector): the Latin faces first" );
                $this->assertSame( $emoji, array_slice( $resolved, $generic + 1 ), "$path ($selector): the emoji faces after the generic family" );

                $other = array_slice( $resolved, count( self::LATIN ), $generic - count( self::LATIN ) );
                $this->assertGreaterThan( 10, count( $other ), "$path ($selector): faces for the other scripts" );
                $this->assertSame( $other, array_values( array_unique( $other ) ), "$path ($selector): no face twice" );
                foreach ( $other as $face )
                {
                    $this->assertMatchesRegularExpression( '/^"[^"]+"$/', $face, "$path ($selector): a named font, not a keyword or a var()" );
                    $this->assertNotContains( $face, self::LATIN, "$path ($selector): a Latin face is not repeated after the others" );
                    $this->assertNotContains( $face, $emoji, "$path ($selector): no emoji face before the generic family" );
                    $this->assertNotContains( strtolower( trim( $face, '"' ) ), self::GENERIC, "$path ($selector): no generic family among the named faces" );
                }
                // a CJK face for every system: macOS / Windows / Linux
                $this->assertNotEmpty( array_intersect( array( '"PingFang SC"', '"PingFang TC"', '"Hiragino Sans"', '"Apple SD Gothic Neo"' ), $cjkFaces ), "$path ($selector): a macOS CJK face" );
                $this->assertNotEmpty( array_intersect( array( '"Microsoft YaHei"', '"Microsoft JhengHei"', '"Yu Gothic"', '"Meiryo"', '"Malgun Gothic"' ), $cjkFaces ), "$path ($selector): a Windows CJK face" );
                $this->assertNotEmpty( preg_grep( '/^"Noto Sans CJK /', $cjkFaces ), "$path ($selector): a Linux (Noto) CJK face" );
            }
        }
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
