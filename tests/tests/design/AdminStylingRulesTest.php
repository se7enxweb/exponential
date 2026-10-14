<?php
/**
 * The administration's styling rules (doc/bc/6.0/admin4-styling.md), checked on the files alone; no database, no
 * installation.
 *
 * - No <style> block and no style="" attribute in the templates of the admin designs (admin4, admin4l, admin3,
 *   admin2, admin); template comments {* ... *} are not read. Files still being migrated are in ALLOWLIST, which
 *   only shrinks: a listed file that has no inline CSS any more fails the test until it is taken off the list.
 *   EXCEPTIONS are the documented, permanent ones.
 * - The module stylesheets (stylesheets/modules/*.css of admin4 and admin) take colours, radii, spacing and fonts
 *   from the --a4-* tokens only: no colour literal, no radius but a token, 0 or 50%, no spacing length but 0, 1px,
 *   2px or a token, no font family or pixel font size of their own, no custom property of their own (no local
 *   aliases), and every rule names an .a4- class.
 * - components.css: every colour token of :root has a value for html[data-a4-theme="dark"], and the copies in
 *   design/admin4 and design/admin are the same file.
 * - Every file of design.ini [StylesheetSettings] AdminComponentCSSFileList exists in both designs.
 * - admin4's frame (admin4.css) and its page stylesheets (PAGE_STYLESHEETS) take their colours from the tokens too:
 *   no colour literal outside a custom property's own definition, so every colour has its dark value. The other
 *   base stylesheets inherited from admin3 (pagelayout, core, content, theme/*) are overridden through tokens in
 *   admin4.css and are not checked yet.
 * - No :focus or :focus-visible rule of an admin4 stylesheet sets its outline to none or 0.
 * - components.css sets the colour scheme of both themes and has forced-colors and touch rules; admin4.css stops
 *   motion for prefers-reduced-motion; no admin4 stylesheet transitions "all".
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminStylingRulesTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminStylingRulesTest extends PHPUnit\Framework\TestCase
{
    /**
     * The admin designs whose templates may carry no inline CSS
     */
    const DESIGNS = array( 'admin4', 'admin4l', 'admin3', 'admin2', 'admin' );

    /**
     * Permanent exceptions, path => why (each is documented in doc/bc/6.0/admin4-styling.md, "Exceptions")
     */
    const EXCEPTIONS = array(
        'design/admin/templates/pagelayout.tpl' => 'the column widths of the editor\'s preferences, from menu.ini or dragged by hand, before the page is drawn',
    );

    /**
     * Templates still being moved to the stylesheets. This list only shrinks; it is empty when the work is done.
     */
    const ALLOWLIST = array(
    );

    /**
     * Token name prefixes that are the same in light and dark mode (sizes, radius, type), so need no dark value
     */
    const THEME_INDEPENDENT = array( '--a4-space-', '--a4-text-', '--a4-radius', '--a4-font', '--a4-mono', '--a4-leading' );

    private static $root;

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 3 );
    }

    /**
     * Returns the number of <style> blocks plus style="" attributes in a template, comments left out
     */
    public static function inlineCssCount( $source )
    {
        $source = preg_replace( '/\{\*.*?\*\}/s', '', $source );
        return preg_match_all( '/<style\b/i', $source ) + preg_match_all( '/(?<![\w.\-])style\s*=\s*["\'{]/i', $source );
    }

    /**
     * Returns the templates (paths from the root) of the admin designs that carry inline CSS
     */
    private static function templatesWithInlineCss()
    {
        $found = array();
        foreach ( self::DESIGNS as $design )
        {
            $dir = self::$root . "/design/$design/templates";
            if ( !is_dir( $dir ) )
            {
                continue;
            }
            $files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ) );
            foreach ( $files as $file )
            {
                if ( substr( $file->getFilename(), -4 ) === '.tpl' && self::inlineCssCount( file_get_contents( $file->getPathname() ) ) > 0 )
                {
                    $found[] = substr( $file->getPathname(), strlen( self::$root ) + 1 );
                }
            }
        }
        sort( $found );
        return $found;
    }

    public function testDetectsStyleBlocksAndAttributesButNotComments()
    {
        $this->assertSame( 1, self::inlineCssCount( "<style>\n.a { color: red; }\n</style>" ) );
        $this->assertSame( 1, self::inlineCssCount( '<div STYLE="display:none">' ) );
        $this->assertSame( 1, self::inlineCssCount( "<p style='margin:0'>" ) );
        $this->assertSame( 0, self::inlineCssCount( '{* <style> and style="" in a comment *}<p class="a4-muted">' ) );
        $this->assertSame( 0, self::inlineCssCount( '<script>el.style.display = "none"; node.style="x";</script>' ) );
        $this->assertSame( 0, self::inlineCssCount( '<link rel="stylesheet" data-style="x">' ) );
    }

    public function testAdminTemplatesCarryNoInlineCss()
    {
        $found = self::templatesWithInlineCss();
        $new = array_values( array_diff( $found, self::ALLOWLIST, array_keys( self::EXCEPTIONS ) ) );
        $this->assertSame( array(), $new,
            "These templates have a <style> block or a style=\"\" attribute. Move the CSS to the design's stylesheets " .
            "(components.css classes, or stylesheets/modules/<group>.css): doc/bc/6.0/admin4-styling.md" );
    }

    public function testAllowlistOnlyShrinks()
    {
        $found = self::templatesWithInlineCss();
        $done = array_values( array_diff( self::ALLOWLIST, $found ) );
        $this->assertSame( array(), $done, 'These templates carry no inline CSS any more: take them off ALLOWLIST' );
        $this->assertSame( array_values( array_unique( self::ALLOWLIST ) ), array_values( self::ALLOWLIST ), 'ALLOWLIST names a file twice' );
    }

    public function testExceptionsStillNeedTheirInlineCss()
    {
        foreach ( self::EXCEPTIONS as $path => $why )
        {
            $file = self::$root . '/' . $path;
            $this->assertFileExists( $file );
            $this->assertSame( 1, self::inlineCssCount( file_get_contents( $file ) ), "$path: one inline style only ($why)" );
        }
    }

    /**
     * Returns the declarations of a stylesheet as array( selector, property, value ), comments left out;
     * @media and @supports blocks are opened, @keyframes are skipped
     */
    private static function declarations( $css )
    {
        $css = preg_replace( '#/\*.*?\*/#s', '', $css );
        $css = preg_replace( '/@keyframes[^{]*\{(?:[^{}]*\{[^{}]*\})*[^{}]*\}/s', '', $css );
        $css = preg_replace( '/@(media|supports|container)[^{]*\{((?:[^{}]*\{[^{}]*\})*)[^{}]*\}/s', '$2', $css );
        $list = array();
        preg_match_all( '/([^{}]+)\{([^{}]*)\}/s', $css, $rules, PREG_SET_ORDER );
        foreach ( $rules as $rule )
        {
            $selector = trim( preg_replace( '/\s+/', ' ', $rule[1] ) );
            foreach ( explode( ';', $rule[2] ) as $declaration )
            {
                if ( strpos( $declaration, ':' ) === false )
                {
                    continue;
                }
                list( $property, $value ) = explode( ':', $declaration, 2 );
                $list[] = array( $selector, strtolower( trim( $property ) ), trim( $value ) );
            }
        }
        return $list;
    }

    /**
     * Returns what is wrong with one declaration of a module stylesheet, or null
     */
    public static function moduleDeclarationProblem( $selector, $property, $value )
    {
        $plain = preg_replace( '/var\(\s*--[\w-]+\s*\)/', 'var()', strtolower( $value ) );
        if ( strpos( $property, '--' ) === 0 )
        {
            return 'declares its own custom property (a local alias); use the --a4-* tokens';
        }
        if ( preg_match( '/#[0-9a-f]{3,8}\b|\b(rgba?|hsla?|hwb|lab|lch|oklab|oklch|color-mix)\(/', $plain ) )
        {
            return 'a colour literal; use a --a4-* colour token';
        }
        $named = 'white|black|red|green|blue|orange|gray|grey|silver|yellow|purple|navy|maroon|teal|olive|lime|aqua|fuchsia|pink|brown|gold|beige|ivory|tan|crimson|coral|salmon|khaki|violet|indigo|cyan|magenta|whitesmoke|gainsboro|lightgray|lightgrey|darkgray|darkgrey';
        if ( preg_match( '/(^|[\s,(])(' . $named . ')($|[\s,)])/', $plain ) && !preg_match( '/^(transition|animation|will-change|content|grid-template-areas|grid-area)/', $property ) )
        {
            return 'a named colour; use a --a4-* colour token';
        }
        if ( strpos( $property, 'radius' ) !== false && !preg_match( '/^((var\(\)|0|50%|inherit)\s*)+$/', $plain ) )
        {
            return 'a radius of its own; use var(--a4-radius-s) (or 0, 50%)';
        }
        if ( preg_match( '/^(margin|padding|gap|row-gap|column-gap)(-|$)/', $property ) )
        {
            $rest = preg_replace( '/var\(\)|calc\([^)]*\)|\b(0|1px|2px|-1px|-2px|auto|inherit|0 auto)\b/', '', $plain );
            if ( preg_match( '/\d/', $rest ) )
            {
                return 'a spacing length of its own; use the --a4-space-* tokens';
            }
        }
        if ( $property === 'font-family' && $plain !== 'inherit' && !preg_match( '/^var\(\)$/', $plain ) )
        {
            return 'a font of its own; use var(--a4-font) or var(--a4-mono)';
        }
        if ( ( $property === 'font-size' || $property === 'font' ) && preg_match( '/\d+(\.\d+)?px/', $plain ) )
        {
            return 'a pixel font size; use the --a4-text-* tokens';
        }
        return null;
    }

    public function testModuleRuleCheckerCatchesWhatItShould()
    {
        $this->assertNotNull( self::moduleDeclarationProblem( '.a4-x', 'color', '#c2410c' ) );
        $this->assertNotNull( self::moduleDeclarationProblem( '.a4-x', 'background', 'white' ) );
        $this->assertNotNull( self::moduleDeclarationProblem( '.a4-x', 'border', '1px solid rgba(0,0,0,.1)' ) );
        $this->assertNotNull( self::moduleDeclarationProblem( '.a4-x', 'border-radius', '12px' ) );
        $this->assertNotNull( self::moduleDeclarationProblem( '.a4-x', '--rl-ink', 'var(--a4-ink)' ) );
        $this->assertNotNull( self::moduleDeclarationProblem( '.a4-x', 'padding', '10px 14px' ) );
        $this->assertNotNull( self::moduleDeclarationProblem( '.a4-x', 'font-size', '13px' ) );
        $this->assertNull( self::moduleDeclarationProblem( '.a4-x', 'color', 'var(--a4-muted)' ) );
        $this->assertNull( self::moduleDeclarationProblem( '.a4-x', 'border-radius', 'var(--a4-radius-s)' ) );
        $this->assertNull( self::moduleDeclarationProblem( '.a4-x', 'padding', 'var(--a4-space-2) 0' ) );
        $this->assertNull( self::moduleDeclarationProblem( '.a4-x', 'grid-template-columns', 'repeat(auto-fit, minmax(220px, 1fr))' ) );
        $this->assertNull( self::moduleDeclarationProblem( '.a4-x', 'border', '1px solid var(--a4-line)' ) );
    }

    public function testModuleStylesheetsUseOnlyTokens()
    {
        $problems = array();
        foreach ( array( 'admin4', 'admin' ) as $design )
        {
            foreach ( glob( self::$root . "/design/$design/stylesheets/modules/*.css" ) as $file )
            {
                $name = substr( $file, strlen( self::$root ) + 1 );
                foreach ( self::declarations( file_get_contents( $file ) ) as $d )
                {
                    list( $selector, $property, $value ) = $d;
                    if ( strpos( $selector, '.a4-' ) === false )
                    {
                        $problems[] = "$name: \"$selector\" names no .a4- class";
                    }
                    $problem = self::moduleDeclarationProblem( $selector, $property, $value );
                    if ( $problem !== null )
                    {
                        $problems[] = "$name: $selector { $property: $value } -- $problem";
                    }
                }
            }
        }
        $this->assertSame( array(), array_values( array_unique( $problems ) ) );
    }

    /**
     * Returns the custom properties declared in the first block whose selector is exactly $selector
     */
    private static function tokensOf( $css, $selector )
    {
        $css = preg_replace( '#/\*.*?\*/#s', '', $css );
        if ( !preg_match( '/(^|\})\s*' . preg_quote( $selector, '/' ) . '\s*\{([^}]*)\}/s', $css, $m ) )
        {
            return array();
        }
        preg_match_all( '/(--[\w-]+)\s*:/', $m[2], $names );
        return $names[1];
    }

    public function testEveryColourTokenHasADarkValue()
    {
        $css = file_get_contents( self::$root . '/design/admin4/stylesheets/components.css' );
        $light = self::tokensOf( $css, ':root' );
        $dark = self::tokensOf( $css, 'html[data-a4-theme="dark"]' );
        $this->assertNotEmpty( $light );
        $missing = array();
        foreach ( $light as $token )
        {
            foreach ( self::THEME_INDEPENDENT as $prefix )
            {
                if ( strpos( $token, $prefix ) === 0 )
                {
                    continue 2;
                }
            }
            if ( !in_array( $token, $dark, true ) )
            {
                $missing[] = $token;
            }
        }
        $this->assertSame( array(), $missing, 'tokens without a value for html[data-a4-theme="dark"] in components.css' );
        $this->assertSame( array(), array_values( array_diff( $dark, $light ) ), 'dark tokens that have no light value' );
    }

    /**
     * admin4 stylesheets outside modules/ that take every colour from the tokens
     */
    const PAGE_STYLESHEETS = array( 'admin4.css', 'ezinfo.css', 'packagecompare.css', 'setup-extensions.css', 'visual-templateview.css' );

    /**
     * Declarations (stylesheet => selector => properties) that keep a colour literal on purpose
     */
    const COLOUR_EXCEPTIONS = array(
        // explayouts paints its own navigation column navy on its pages; admin4's card takes that navy there
        'admin4.css' => array( '#page.ezexplayoutsuinavigationpart #leftmenu-design' => array( 'background-color', 'box-shadow' ) ),
    );

    public function testPageStylesheetsUseOnlyColourTokens()
    {
        $problems = array();
        foreach ( self::PAGE_STYLESHEETS as $file )
        {
            $path = self::$root . '/design/admin4/stylesheets/' . $file;
            $this->assertFileExists( $path );
            $exceptions = isset( self::COLOUR_EXCEPTIONS[$file] ) ? self::COLOUR_EXCEPTIONS[$file] : array();
            foreach ( self::declarations( file_get_contents( $path ) ) as $d )
            {
                list( $selector, $property, $value ) = $d;
                if ( strpos( $property, '--' ) === 0 || ( isset( $exceptions[$selector] ) && in_array( $property, $exceptions[$selector], true ) ) )
                {
                    continue;
                }
                $problem = self::moduleDeclarationProblem( $selector, $property, $value );
                if ( $problem !== null && strpos( $problem, 'colour' ) !== false )
                {
                    $problems[] = "design/admin4/stylesheets/$file: $selector { $property: $value } -- $problem";
                }
            }
        }
        $this->assertSame( array(), array_values( array_unique( $problems ) ) );
    }

    /**
     * Returns true when a selector (a list) has a :focus or :focus-visible state that is not exempt: :focus-within is
     * another state, and :focus:not(:focus-visible) is the mouse-focus case that may drop the outline
     */
    public static function selectorHasFocus( $selector )
    {
        foreach ( explode( ',', $selector ) as $part )
        {
            $part = str_replace( ':focus:not(:focus-visible)', '', $part );
            if ( preg_match( '/:focus(-visible)?(?![\w-])/', $part ) )
            {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns true when an outline declaration draws nothing: style none or hidden, or width 0, in any order
     */
    public static function outlineIsRemoved( $property, $value )
    {
        $value = strtolower( trim( preg_replace( '/\s*!important\s*$/i', '', $value ) ) );
        $words = preg_split( '/\s+/', $value );
        if ( $property === 'outline-style' )
        {
            return in_array( $value, array( 'none', 'hidden' ), true );
        }
        if ( $property === 'outline-width' )
        {
            return (bool)preg_match( '/^0([a-z]+)?$/', $value );
        }
        if ( $property !== 'outline' )
        {
            return false;
        }
        foreach ( $words as $word )
        {
            if ( in_array( $word, array( 'none', 'hidden' ), true ) || preg_match( '/^0([a-z]+)?$/', $word ) )
            {
                return true;
            }
        }
        return false;
    }

    public function testFocusOutlineCheckerCatchesWhatItShould()
    {
        foreach ( array( 'none', '0', '0 none', 'none 0', 'medium none', '0px none', 'none !important' ) as $value )
        {
            $this->assertTrue( self::outlineIsRemoved( 'outline', $value ), "outline: $value" );
        }
        $this->assertTrue( self::outlineIsRemoved( 'outline-style', 'hidden' ) );
        $this->assertTrue( self::outlineIsRemoved( 'outline-width', '0' ) );
        $this->assertFalse( self::outlineIsRemoved( 'outline', '2px solid transparent' ) );
        $this->assertFalse( self::outlineIsRemoved( 'outline', '3px solid var(--a4-ring)' ) );
        $this->assertTrue( self::selectorHasFocus( 'a:focus' ) );
        $this->assertTrue( self::selectorHasFocus( 'a:hover, a:focus-visible' ) );
        $this->assertFalse( self::selectorHasFocus( '.box:focus-within' ) );
        $this->assertFalse( self::selectorHasFocus( 'a:focus:not(:focus-visible)' ) );
        $this->assertFalse( self::selectorHasFocus( 'a:hover' ) );
    }

    /**
     * A :focus or :focus-visible rule of admin4 that takes the outline away leaves nothing in forced-colors mode,
     * where box-shadow rings are not drawn: it keeps an outline (a transparent one is enough, forced colors paint it)
     */
    public function testNoFocusRuleRemovesTheOutline()
    {
        $problems = array();
        $files = array_merge(
            glob( self::$root . '/design/admin4/stylesheets/*.css' ),
            glob( self::$root . '/design/admin4/stylesheets/*/*.css' )
        );
        $this->assertNotEmpty( $files );
        foreach ( $files as $file )
        {
            $name = substr( $file, strlen( self::$root ) + 1 );
            foreach ( self::declarations( file_get_contents( $file ) ) as $d )
            {
                list( $selector, $property, $value ) = $d;
                if ( self::selectorHasFocus( $selector ) && self::outlineIsRemoved( $property, $value ) )
                {
                    $problems[] = "$name: $selector { $property: $value }";
                }
            }
        }
        $this->assertSame( array(), $problems, 'focus rules without an outline; keep one (outline: 2px solid transparent next to a box-shadow ring)' );
    }

    /**
     * The modes the browser asks for: the colour scheme of each theme, forced colors and touch in components.css,
     * reduced motion and no transition of "all" in admin4's stylesheets
     */
    public function testAdmin4FollowsTheModesTheBrowserAsksFor()
    {
        $dir = self::$root . '/design/admin4/stylesheets';
        $components = preg_replace( '#/\*.*?\*/#s', '', file_get_contents( "$dir/components.css" ) );
        $this->assertMatchesRegularExpression( '/(^|\})\s*:root\s*\{[^}]*color-scheme:\s*light/s', $components );
        $this->assertMatchesRegularExpression( '/html\[data-a4-theme="dark"\]\s*\{[^}]*color-scheme:\s*dark/s', $components );
        $this->assertMatchesRegularExpression( '/@media \(forced-colors: active\)\s*\{.*?\.a4-dot, \.a4-meter > span, \.a4-bars > \* \{ forced-color-adjust: none; \}/s', $components );
        // the touch rule must outweigh .a4-field input[type=text] (0,2,1): one :not() per excluded type
        $this->assertMatchesRegularExpression( '/@media \(pointer: coarse\)\s*\{[^}]*\.a4-field input:not\(\[type=checkbox\]\):not\(\[type=radio\]\)[^{]*\{ font-size: var\(--a4-text-l\); \}/s', $components );
        $frame = preg_replace( '#/\*.*?\*/#s', '', file_get_contents( "$dir/admin4.css" ) );
        $this->assertMatchesRegularExpression( '/@media \(prefers-reduced-motion: reduce\)\s*\{\s*\*, \*::before, \*::after\s*\{[^}]*transition-duration/s', $frame );
        $this->assertMatchesRegularExpression( '/@media \(pointer: coarse\)\s*\{[^}]*#page input:not\(\[type=checkbox\]\)[^{]*\{ font-size: var\(--a4-text-l\); \}/s', $frame );
        $all = array();
        foreach ( array_merge( glob( "$dir/*.css" ), glob( "$dir/*/*.css" ) ) as $file )
        {
            foreach ( self::declarations( file_get_contents( $file ) ) as $d )
            {
                if ( preg_match( '/^(-\w+-)?transition(-property)?$/', $d[1] ) && preg_match( '/(^|,)\s*all\b/', $d[2] ) )
                {
                    $all[] = substr( $file, strlen( self::$root ) + 1 ) . ': ' . $d[0];
                }
            }
        }
        $this->assertSame( array(), $all, 'transition: all; name the properties' );
    }

    /**
     * The sidebar script stores a choice in one place only (store(), for a click on a wide window), reads the width
     * through matchMedia, and reaches localStorage only inside try blocks
     */
    public function testSidebarScriptStoresOnlyAWideWindowsClick()
    {
        $js = file_get_contents( self::$root . '/design/admin4/javascript/ezadmin_menubar.js' );
        $start = strpos( $js, 'function sidebarToggle()' );
        $end = strpos( $js, '// Navbar menu toggling feature' );
        $this->assertNotFalse( $start );
        $this->assertNotFalse( $end );
        $sidebar = substr( $js, $start, $end - $start );
        $this->assertSame( 1, substr_count( $sidebar, 'setItem(' ), 'setItem only inside store()' );
        $this->assertMatchesRegularExpression( '/function store\(side, open\) \{[^}]*try \{\s*window\.localStorage\.setItem/s', $sidebar );
        $this->assertStringContainsString( 'window.matchMedia("(max-width: 1023px)")', $sidebar );
        $this->assertStringNotContainsString( '$(window).width()', $sidebar );
        $this->assertSame( 0, preg_match_all( '/(?<!window\.)localStorage/', $sidebar ), 'localStorage only as window.localStorage, inside try' );
        $this->assertSame( 3, substr_count( $sidebar, 'try {' ), 'reading, storing and the clean-up of the old keys each in a try block' );
    }

    public function testComponentsAreTheSameInAdminAndAdmin4()
    {
        $this->assertFileEquals( self::$root . '/design/admin4/stylesheets/components.css', self::$root . '/design/admin/stylesheets/components.css',
            'design/admin/stylesheets/components.css is a copy of the admin4 file; change admin4 and copy it' );
    }

    public function testEveryListedStylesheetExists()
    {
        $ini = file_get_contents( self::$root . '/settings/design.ini' );
        preg_match_all( '/^AdminComponentCSSFileList\[\]=(\S+)\s*$/m', $ini, $m );
        $this->assertContains( 'components.css', $m[1] );
        foreach ( $m[1] as $file )
        {
            foreach ( array( 'admin4', 'admin' ) as $design )
            {
                $this->assertFileExists( self::$root . "/design/$design/stylesheets/$file" );
            }
        }
    }
}
