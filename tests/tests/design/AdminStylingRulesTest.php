<?php
/**
 * The administration's styling rules (doc/bc/6.0/admin4-styling.md), checked on the files alone; no database, no
 * installation.
 *
 * - No <style> block and no style="" attribute in the templates of the admin designs (admin4, admin4l, admin3,
 *   admin), in templates/ and override/templates/; template comments {* ... *} are not read. A listed design that
 *   does not exist fails the test instead of being skipped. Files still being migrated are in ALLOWLIST, which
 *   only shrinks: a listed file that has no inline CSS any more fails the test until it is taken off the list.
 *   EXCEPTIONS are the documented, permanent ones.
 * - The module stylesheets (stylesheets/modules/*.css of admin4 and admin) take colours, radii, spacing and fonts
 *   from the --a4-* tokens only: no colour literal, no radius but a token, 0 or 50%, no spacing length but 0, 1px,
 *   2px or a token, no font family or pixel font size of their own, no custom property of their own (no local
 *   aliases), and every rule names an .a4- class.
 * - components.css: every colour token of :root has a value for html[data-a4-theme="dark"], and the copies in
 *   design/admin4 and design/admin are the same file.
 * - Every file of design.ini [StylesheetSettings] AdminComponentCSSFileList exists in both designs.
 * - admin4's frame (admin4.css), its page stylesheets (PAGE_STYLESHEETS) and the base stylesheets inherited from
 *   admin3 (BASE_STYLESHEETS: core, content, pagelayout, theme/*) take their colours from the tokens too: no colour
 *   literal outside a custom property's own definition, so every colour has its dark value. Legacy literals that
 *   are kept on purpose would be listed in BASE_COLOUR_ALLOWLIST, which only shrinks (it is empty).
 * - No :focus or :focus-visible rule of an admin4 stylesheet sets its outline to none or 0.
 * - components.css sets the colour scheme of both themes and has forced-colors and touch rules; admin4.css stops
 *   motion for prefers-reduced-motion; no admin4 stylesheet transitions "all".
 * - components.css and admin4.css take font sizes and spacing from the --a4-text-* and --a4-space-* scales.
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
    const DESIGNS = array( 'admin4', 'admin4l', 'admin3', 'admin' );

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
    const THEME_INDEPENDENT = array( '--a4-space-', '--a4-text-', '--a4-radius', '--a4-font', '--a4-mono', '--a4-leading', '--a4-control-h', '--a4-content-max', '--a4-gutter', '--a4-list-pad', '--a4-cell-pad-', '--a4-card-pad-', '--a4-z-' );

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
     * Returns the template directories of the admin designs that exist: templates/ (which every listed design must
     * have) and override/templates/ (where a design has one)
     */
    private static function templateDirectories()
    {
        $dirs = array();
        foreach ( self::DESIGNS as $design )
        {
            foreach ( array( 'templates', 'override/templates' ) as $sub )
            {
                $dir = self::$root . "/design/$design/$sub";
                if ( is_dir( $dir ) )
                {
                    $dirs[] = $dir;
                }
            }
        }
        return $dirs;
    }

    /**
     * Returns the templates (paths from the root) of the admin designs that carry inline CSS
     */
    private static function templatesWithInlineCss()
    {
        $found = array();
        foreach ( self::templateDirectories() as $dir )
        {
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

    public function testEveryListedDesignExists()
    {
        foreach ( self::DESIGNS as $design )
        {
            $this->assertDirectoryExists( self::$root . "/design/$design/templates",
                "design/$design is listed in DESIGNS but has no templates: take it off the list rather than skip it" );
        }
    }

    public function testOverrideTemplatesAreScanned()
    {
        $dirs = array_map( function ( $dir ) { return substr( $dir, strlen( self::$root ) + 1 ); }, self::templateDirectories() );
        $this->assertContains( 'design/admin4/override/templates', $dirs );
        $this->assertContains( 'design/admin/override/templates', $dirs );
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
    const PAGE_STYLESHEETS = array( 'admin4.css', 'ezinfo.css', 'packagecompare.css', 'visual-templateview.css' );

    /**
     * admin4's base stylesheets, inherited from admin3 and loaded on every admin4 page before admin4.css: the same
     * rule. A colour admin4.css overrides today is still a token here, so the next selector that slips past the
     * override (a dark-mode link at 1.8:1, a yellow diff mark under light text) cannot show it.
     */
    const BASE_STYLESHEETS = array( 'core.css', 'content.css', 'pagelayout.css', 'theme/rounded.css', 'theme/admin_datatable.css', 'theme/modalwindow.css' );

    /**
     * Colour literals of the base stylesheets kept on purpose, as "file: selector { property: value }". It only
     * shrinks: an entry whose declaration is gone fails the test until it is taken off. Empty since 2026-10-14.
     */
    const BASE_COLOUR_ALLOWLIST = array(
    );

    /**
     * Returns the colour literals of admin4 stylesheets (paths under design/admin4/stylesheets) as
     * "file: selector { property: value } -- problem"
     */
    private static function colourLiterals( array $files )
    {
        $problems = array();
        foreach ( $files as $file )
        {
            $path = self::$root . '/design/admin4/stylesheets/' . $file;
            foreach ( self::declarations( (string)file_get_contents( $path ) ) as $d )
            {
                list( $selector, $property, $value ) = $d;
                if ( strpos( $property, '--' ) === 0 )
                {
                    continue;
                }
                $problem = self::moduleDeclarationProblem( $selector, $property, $value );
                if ( $problem !== null && strpos( $problem, 'colour' ) !== false )
                {
                    $problems["$file: $selector { $property: $value }"] = $problem;
                }
            }
        }
        return $problems;
    }

    public function testBaseStylesheetsUseOnlyColourTokens()
    {
        foreach ( self::BASE_STYLESHEETS as $file )
        {
            $this->assertFileExists( self::$root . '/design/admin4/stylesheets/' . $file );
        }
        $found = self::colourLiterals( self::BASE_STYLESHEETS );
        $new = array();
        foreach ( $found as $where => $problem )
        {
            if ( !in_array( $where, self::BASE_COLOUR_ALLOWLIST, true ) )
            {
                $new[] = "design/admin4/stylesheets/$where -- $problem";
            }
        }
        $this->assertSame( array(), $new, 'use a --a4-* colour token (doc/bc/6.0/admin4-styling.md)' );
        $this->assertSame( array(), array_values( array_diff( self::BASE_COLOUR_ALLOWLIST, array_keys( $found ) ) ),
            'These allowlisted colour literals are gone: take them off BASE_COLOUR_ALLOWLIST' );
    }

    public function testPageStylesheetsUseOnlyColourTokens()
    {
        $problems = array();
        foreach ( self::PAGE_STYLESHEETS as $file )
        {
            $path = self::$root . '/design/admin4/stylesheets/' . $file;
            $this->assertFileExists( $path );
            foreach ( self::declarations( file_get_contents( $path ) ) as $d )
            {
                list( $selector, $property, $value ) = $d;
                if ( strpos( $property, '--' ) === 0 )
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
     *
     * Its limit: it reads rules whose selector names :focus or :focus-visible. A plain rule that outranks the
     * global ring (an id selector such as #logintext { outline: none }) removes the indicator as well and is not
     * seen here; such a rule needs a :focus rule of its own that restores the outline.
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
     * The sidebar script saves a choice in one place only (store(), for a click on a wide window) as the user's
     * eZPreferences admin_left_sidebar / admin_right_sidebar, reads them from the page layout's data-a4-sidebars,
     * reads the width through matchMedia, and touches localStorage only to drop the keys used before, in a try block
     */
    public function testSidebarScriptStoresOnlyAWideWindowsClick()
    {
        $js = file_get_contents( self::$root . '/design/admin4/javascript/ezadmin_menubar.js' );
        $start = strpos( $js, 'function sidebarToggle()' );
        $end = strpos( $js, '// Navbar menu toggling feature' );
        $this->assertNotFalse( $start );
        $this->assertNotFalse( $end );
        $sidebar = substr( $js, $start, $end - $start );
        $this->assertSame( 0, substr_count( $sidebar, 'setItem(' ), 'no browser storage: the choice is an eZPreference' );
        // saved only inside store(), through the shared window.a4Pref (AdminPreferencesTest)
        $this->assertSame( 1, substr_count( $sidebar, 'pref.set(' ), 'saved only inside store()' );
        $this->assertMatchesRegularExpression( '/function store\(side, open\) \{\s*choice\[side\] = open;\s*pref\.set\(prefs\[side\], open \? "1" : "0"\);/', $sidebar );
        $this->assertStringContainsString( 'const pref = window.a4Pref', $sidebar );
        $this->assertStringContainsString( 'window.matchMedia("(max-width: 1023.98px)")', $sidebar );
        $this->assertStringNotContainsString( '$(window).width()', $sidebar );
        $this->assertSame( 0, preg_match_all( '/(?<!window\.)localStorage/', $sidebar ), 'localStorage only as window.localStorage, inside try' );
        $this->assertSame( 1, substr_count( $sidebar, 'try {' ), 'only the clean-up of the old keys, in a try block' );
    }

    /**
     * No text below 12px on admin4's pages, not only in admin4.css and components.css: the base and page stylesheets
     * size text in em of the body, which core.css sets to var(--a4-text-s) (13px). An em size that resolves below
     * 12px from there uses var(--a4-text-xs) instead. (1px font sizes are IE hacks and do not draw text.)
     */
    public function testBaseAndPageStylesheetsDrawNoTextBelowTwelvePixels()
    {
        $core = (string)file_get_contents( self::$root . '/design/admin4/stylesheets/core.css' );
        $this->assertMatchesRegularExpression( '/body \/\*[^*]*\*\/\s*\{[^}]*font-size: var\(--a4-text-s\);/', $core, 'core.css sets the body text from the type scale' );
        $problems = array();
        $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( self::$root . '/design/admin4/stylesheets', FilesystemIterator::SKIP_DOTS ) );
        foreach ( $it as $file )
        {
            $name = $file->getFilename();
            if ( substr( $name, -4 ) !== '.css' || in_array( $name, array( 'admin4.css', 'components.css' ), true ) )
                continue;
            $css = preg_replace( '/\/\*.*?\*\//s', '', (string)file_get_contents( $file->getPathname() ) );
            preg_match_all( '/font(?:-size)?\s*:[^;{}]*?(?<![\w.])(0?\.\d+)em\b/', $css, $m );
            foreach ( $m[1] as $em )
            {
                if ( (float)$em * 13 < 12 )
                    $problems[] = substr( $file->getPathname(), strlen( self::$root ) + 1 ) . ": {$em}em is " . round( (float)$em * 13, 1 ) . 'px';
            }
        }
        $this->assertSame( array(), $problems, 'use var(--a4-text-xs) for these' );
    }

    /**
     * components.css and admin4.css set font sizes with the --a4-text-* tokens and spacing with the --a4-space-*
     * tokens (1px and 2px aside; a layout length is a token of its own, e.g. --a4-pw-toggle-room)
     */
    public function testFrameAndComponentsUseTheScale()
    {
        $problems = array();
        foreach ( array( 'components.css', 'admin4.css' ) as $file )
        {
            foreach ( self::declarations( file_get_contents( self::$root . "/design/admin4/stylesheets/$file" ) ) as $d )
            {
                list( $selector, $property, $value ) = $d;
                if ( ( $property === 'font-size' || $property === 'font' ) && preg_match( '/\d+(\.\d+)?px/i', $value ) )
                {
                    $problems[] = "$file: $selector { $property: $value } -- use a --a4-text-* token";
                }
                if ( preg_match( '/^(margin|padding|gap|row-gap|column-gap)(-|$)/', $property ) )
                {
                    preg_match_all( '/(?<![\w.-])-?\d+(\.\d+)?px/i', $value, $m );
                    foreach ( $m[0] as $length )
                    {
                        $length = strtolower( $length );
                        if ( !in_array( $length, array( '1px', '2px', '-1px', '-2px' ), true ) )
                        {
                            $problems[] = "$file: $selector { $property: $value } -- use a --a4-space-* token";
                        }
                    }
                }
            }
        }
        $this->assertSame( array(), array_values( array_unique( $problems ) ) );
    }

    /**
     * The targets made 24px (WCAG 2.5.8) stay so: the sidebar tabs, the hit areas around the tree's open/close and
     * the search scope switch, the dialog and scope pane close buttons, the page numbers, the node view's tabs switch
     */
    public function testSmallControlsKeepTheirTargets()
    {
        $dir = self::$root . '/design/admin4/stylesheets';
        $found = array();
        // in the order the page loads them: admin4.css after pagelayout.css
        foreach ( array( 'pagelayout.css', 'admin4.css' ) as $file )
        {
            foreach ( self::declarations( file_get_contents( "$dir/$file" ) ) as $d )
            {
                $found[$d[0]][$d[1]] = $d[2];
            }
        }
        $this->assertStringStartsWith( '1.5rem', $found['.sidebar-control']['width'] );
        $this->assertSame( '""', $found['div#contentstructure ul#content_tree_menu ul a.openclose-open::after, div#contentstructure ul#content_tree_menu ul a.openclose-close::after']['content'] );
        $this->assertSame( 'var(--a4-space-5)', $found['#header .searchblock .searchscope']['width'] );
        $this->assertSame( '24px', $found['.window-close']['width'] );
        $this->assertSame( '24px', $found['.window-close']['height'] );
        $this->assertSame( '24px', $found['.searchscope-pane .close']['width'] );
        $this->assertSame( '24px', $found['.pagenavigator a']['min-width'] );
        $this->assertSame( '24px', $found['.pagenavigator a']['min-height'] );
        // the node view's tabs switch: a 36 x 24px control with a visible label in both templates that draw it
        $this->assertSame( '36px', $found['.tab-block .show-hide-tabs']['width'] );
        $this->assertSame( '24px', $found['.tab-block .show-hide-tabs']['height'] );
        $this->assertArrayHasKey( '.a4-tabs-switch-label', $found );
        foreach ( array( 'window_controls.tpl', 'node/top_node_full.tpl' ) as $template )
        {
            $this->assertStringContainsString( '<span class="a4-tabs-switch-label" aria-hidden="true">{\'Tabs\'|i18n( \'design/admin/parts/my/menu\' )}</span>',
                file_get_contents( self::$root . "/design/admin4/templates/$template" ), $template );
        }
    }

    /**
     * Every data table stripes its even body rows with --a4-zebra (table.list, .a4-table, the sub-items table), the
     * hovered row wins; no admin4 stylesheet paints rows in a colour of its own
     */
    public function testDataTablesShareOneZebra()
    {
        $dir = self::$root . '/design/admin4/stylesheets';
        $found = array();
        foreach ( array( 'components.css', 'admin4.css' ) as $file )
        {
            foreach ( self::declarations( file_get_contents( "$dir/$file" ) ) as $d )
            {
                $found[$d[0]][$d[1]] = $d[2];
            }
        }
        // only data rows count (a header row of th in tbody does not), so the first data row is plain; a row header
        // (th scope=row) takes the stripe with its cells
        $rows = ' > tbody > tr:where(:nth-child(even of :has(> td):not([hidden]))) > :is(td, th[scope="row"])';
        $this->assertSame( 'var(--a4-zebra)', $found['.a4-table' . $rows]['background-color'] );
        $this->assertSame( 'var(--a4-zebra)', $found['table.list:not(.a4-plain)' . $rows]['background-color'] );
        $this->assertSame( 'var(--a4-info-bg)', $found['table.list:not(.a4-plain) > tbody > tr:hover > :is(td, th[scope="row"])']['background-color'] );
        $this->assertSame( 'var(--a4-info-bg)', $found['.a4-table > tbody > tr:hover > :is(td, th[scope="row"])']['background-color'] );
        // the forms laid out as a list are not data tables
        foreach ( array( 'shop/editvatrule.tpl', 'shop/editcurrency.tpl' ) as $template )
        {
            $this->assertStringContainsString( '<table class="list a4-plain"', file_get_contents( self::$root . "/design/admin4/templates/$template" ), $template );
        }
        $this->assertStringContainsString( '--exp-dt-row-alt: var(--a4-zebra)', file_get_contents( "$dir/admin4.css" ) );

        $problems = array();
        foreach ( array_merge( glob( "$dir/*.css" ), glob( "$dir/*/*.css" ) ) as $path )
        {
            foreach ( self::declarations( file_get_contents( $path ) ) as $d )
            {
                list( $selector, $property, $value ) = $d;
                if ( preg_match( '/tr\.(bglight|bgdark)|tr:nth-(child|of-type)\((even|odd|2n)/', $selector )
                     && preg_match( '/^background(-color)?$/', $property )
                     && !preg_match( '/^(transparent|var\(--a4-zebra\)|var\(--a4-surface\)|var\(--a4-soft\)|var\(--exp-dt-row-alt\))$/', trim( $value ) ) )
                {
                    $problems[] = basename( $path ) . ": $selector { $property: $value }";
                }
            }
        }
        $this->assertSame( array(), $problems, 'zebra rows take --a4-zebra' );
    }

    /**
     * The controls in a row are as high as each other (--a4-control-h): the node's language choice and buttons, the
     * sub-items toolbar and its pagers; the left column's width grip is a focusable separator
     */
    public function testControlsInARowShareOneHeight()
    {
        $dir = self::$root . '/design/admin4/stylesheets';
        $found = array();
        foreach ( self::declarations( file_get_contents( "$dir/admin4.css" ) ) as $d )
        {
            $found[$d[0]][$d[1]] = $d[2];
        }
        $this->assertSame( 'var(--a4-control-h)', $found['.controlbar.a4-node-actions form .button-left .block select']['height'] );
        // drawn by the page (the forced-colors block further down gives the native control back)
        $this->assertMatchesRegularExpression( '/\n\.controlbar\.a4-node-actions form \.button-left \.block select \{[^}]*\bappearance: none;/',
            file_get_contents( "$dir/admin4.css" ) );
        $this->assertSame( 'var(--a4-control-h)', $found['html #action-controls .exp-dt-action']['height'] );
        $this->assertSame( 'var(--a4-control-h)', $found['html .exp-dt-first, html .exp-dt-prev, html .exp-dt-next, html .exp-dt-last, html .exp-dt-page']['height'] );
        $this->assertSame( 'col-resize', $found['.leftmenu-resize-handle']['cursor'] );
        $this->assertStringContainsString( '--a4-control-h: 32px', file_get_contents( "$dir/components.css" ) );
        $grip = file_get_contents( self::$root . '/design/admin4/templates/page_leftmenu.tpl' );
        $this->assertStringContainsString( 'role="separator"', $grip );
        $this->assertStringContainsString( 'tabindex="0"', $grip );
        $this->assertStringNotContainsString( 'title=', $grip, 'the grip is named by its aria-label alone' );
        // forced colors: the native select without the page's chevron
        $this->assertMatchesRegularExpression( '/@media \(forced-colors: active\) \{\s*\.controlbar\.a4-node-actions form \.button-left \.block select \{[^}]*appearance: auto;[^}]*background-image: none;/',
            file_get_contents( "$dir/admin4.css" ) );
        $this->assertStringContainsString( "handle.addEventListener('keydown'", file_get_contents( self::$root . '/design/admin4/javascript/ezadmin_menubar.js' ) );
    }

    public function testListsMarkTickedRows()
    {
        $dir = self::$root . '/design/admin4/stylesheets';
        $found = array();
        foreach ( array( 'components.css', 'admin4.css' ) as $file )
        {
            foreach ( self::declarations( file_get_contents( "$dir/$file" ) ) as $d )
            {
                // a selector list counts for each of its selectors (commas inside :is()/:has() do not split)
                foreach ( preg_split( '/,(?![^()]*\))/', $d[0] ) as $one )
                {
                    $found[trim( $one )][$d[1]] = $d[2];
                }
            }
        }
        // only the selection checkbox, a checkbox straight in a cell, marks its row
        $ticked = ' > tbody > tr:has(> td > input[type="checkbox"]:checked) > :is(td, th[scope="row"])';
        $this->assertSame( 'var(--a4-accent-soft)', $found['.a4-table' . $ticked]['background-color'] ?? null );
        $this->assertSame( 'var(--a4-accent-soft)', $found['table.list:not(.a4-plain)' . $ticked]['background-color'] ?? null );
        $this->assertSame( 'middle', $found['.a4-table td']['vertical-align'] ?? null );
        $this->assertSame( 'middle', $found['table.list:not(.a4-plain) td']['vertical-align'] ?? null );
        // the ticked tint comes after the hover rule, so it wins
        foreach ( array( 'components.css', 'admin4.css' ) as $file )
        {
            $css = file_get_contents( "$dir/$file" );
            $this->assertGreaterThan( strpos( $css, 'tr:hover > :is(td, th[scope="row"])' ), strpos( $css, 'tr:has(> td > input[type="checkbox"]:checked)' ), $file );
        }
        // no sticky list header: the tables sit in scrolling wrappers (pagelayout.css div:has(> table)), where it never sticks
        foreach ( $found as $selector => $props )
        {
            if ( preg_match( '/table|\.exp-dt/', $selector ) )
                $this->assertNotSame( 'sticky', $props['position'] ?? null, $selector );
        }
    }

    public function testTreeShowsThePathToTheCurrentNode()
    {
        $css = file_get_contents( self::$root . '/design/admin4/stylesheets/admin4.css' );
        $this->assertStringContainsString( 'li.currentnode > a.image-text :is(span.node-name-normal', $css );
        $this->assertSame( 1, preg_match( '/\n(div#contentstructure li\.currentnode > a\.image-text :is\([^)]*\)) \{([^}]*)\}/', $css, $current ) );
        $this->assertStringContainsString( 'box-shadow: inset 3px 0 0 var(--a4-accent);', $current[2] );
        // the bar is an inset shadow on the left: rounded left corners bend it into a "("
        $this->assertStringContainsString( 'border-radius: 0 var(--a4-radius-s) var(--a4-radius-s) 0;', $current[2],
                                           'The current node\'s left corners must be square, or its bar is drawn curved' );
        $this->assertStringContainsString( 'li:has(li.currentnode) > a.image-text', $css );
        $this->assertStringContainsString( 'border-left: 3px solid Highlight;', $css );
    }

    /**
     * The choices above a list (10 | 25 | 50 and the like) were a light gradient with fixed colours: in dark mode a
     * light box whose current value, in light text, could not be read. They are a segmented control on the tokens.
     */
    public function testTablePreferencesAreASegmentedControlOnTheTokens()
    {
        $css = file_get_contents( self::$root . '/design/admin4/stylesheets/theme/rounded.css' );
        preg_match_all( '/(?:^|\n)([^{}\n]*p\.table-preferences[^{}]*)\{([^}]*)\}/', $css, $rules, PREG_SET_ORDER );
        $this->assertGreaterThanOrEqual( 4, count( $rules ) );
        $all = '';
        foreach ( $rules as $rule )
        {
            $this->assertStringNotContainsString( 'gradient', $rule[2], trim( $rule[1] ) );
            $this->assertDoesNotMatchRegularExpression( '/#[0-9a-f]{3,8}\b|rgba?\(/i', $rule[2], trim( $rule[1] ) . ' uses a colour literal' );
            $all .= trim( $rule[1] ) . ' {' . $rule[2] . "}\n";
        }
        $this->assertMatchesRegularExpression( '/p\.table-preferences \{[^}]*border: 1px solid var\(--a4-line\);[^}]*background-color: var\(--a4-surface\);/', $all );
        $this->assertMatchesRegularExpression( '/p\.table-preferences > a \{ color: var\(--a4-accent-text\); \}/', $all );
        $this->assertMatchesRegularExpression( '/p\.table-preferences > span\.current \{[^}]*background-color: var\(--a4-accent-soft\);[^}]*color: var\(--a4-ink\);/', $all );
        $this->assertMatchesRegularExpression( '/p\.table-preferences > a, p\.table-preferences > span \{[^}]*min-height: (2[4-9]|[3-9]\d)px;/', $all,
                                               'The choices are targets of at least 24px' );
        $this->assertMatchesRegularExpression( '/@media \(forced-colors: active\)\s*\{\s*p\.table-preferences[^}]*\}\s*p\.table-preferences > span\.current \{[^}]*Highlight/', $css,
                                               'The current choice needs a forced-colors fallback' );
    }

    public function testEmptyListsUseTheEmptyState()
    {
        $plain = array();
        $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( self::$root . '/design/admin4/templates', FilesystemIterator::SKIP_DOTS ) );
        foreach ( $it as $file )
        {
            if ( substr( (string)$file, -4 ) !== '.tpl' )
                continue;
            // inside a <div class="a4-empty"> a paragraph is the component's text; an .a4-empty inside one is a box in a box
            $inEmpty = false;
            foreach ( file( (string)$file ) as $no => $line )
            {
                $where = substr( (string)$file, strlen( self::$root ) + 1 ) . ':' . ( $no + 1 );
                if ( $inEmpty && strpos( $line, 'class="a4-empty"' ) !== false )
                    $plain[] = "$where (an .a4-empty inside an .a4-empty)";
                if ( strpos( $line, '<div class="a4-empty">' ) !== false )
                    $inEmpty = true;
                elseif ( $inEmpty && preg_match( '#^\s*</div>#', $line ) )
                    $inEmpty = false;
                if ( !$inEmpty && preg_match( "/^\\s*<p>\\{'(There are no |The [a-z ]*list is empty|The list is empty)/", $line ) )
                    $plain[] = $where;
            }
        }
        $this->assertSame( array(), $plain, 'an empty list says so in .a4-empty, not in a plain paragraph' );
    }

    /** Stacking order comes from one scale of --a4-z-* tokens; a number decides it only inside one component. */
    public function testStackingComesFromTheLayerTokens()
    {
        $dir = self::$root . '/design/admin4/stylesheets';
        $problems = array();
        foreach ( array_merge( glob( "$dir/*.css" ), glob( "$dir/*/*.css" ) ) as $path )
        {
            $css = preg_replace( '#/\*.*?\*/#s', '', (string)file_get_contents( $path ) );
            if ( preg_match_all( '/z-index\s*:\s*([^;}]+)/i', $css, $m ) )
            {
                foreach ( $m[1] as $value )
                {
                    $value = trim( preg_replace( '/!important/i', '', $value ) );
                    if ( !preg_match( '/^(0|1|-1|auto|var\(--a4-z-[a-z-]+\))$/', $value ) )
                    {
                        $problems[] = basename( $path ) . ": z-index $value";
                    }
                }
            }
        }
        $this->assertSame( array(), $problems, 'use a --a4-z-* token (0, 1 and -1 only inside one component)' );

        // every token a stylesheet uses is defined (a typo would make the z-index auto)
        $defined = (string)file_get_contents( "$dir/components.css" );
        $undefined = array();
        foreach ( array_merge( glob( "$dir/*.css" ), glob( "$dir/*/*.css" ) ) as $path )
        {
            preg_match_all( '/var\((--a4-z-[a-z-]+)\)/', (string)file_get_contents( $path ), $used );
            foreach ( array_unique( $used[1] ) as $token )
            {
                if ( !preg_match( '/' . preg_quote( $token, '/' ) . '\s*:/', $defined ) )
                {
                    $undefined[] = basename( $path ) . ": $token";
                }
            }
        }
        $this->assertSame( array(), $undefined, 'a --a4-z-* token that components.css does not define' );

        $tokens = array();
        preg_match_all( '/(--a4-z-[a-z-]+)\s*:\s*(-?\d+)\s*;/', (string)file_get_contents( "$dir/components.css" ), $m, PREG_SET_ORDER );
        foreach ( $m as $token )
        {
            $tokens[$token[1]] = (int)$token[2];
        }
        // the order the frame relies on, lowest first
        $order = array( '--a4-z-raised', '--a4-z-sticky', '--a4-z-frame-grip', '--a4-z-frame-control',
                        '--a4-z-hover-preview', '--a4-z-popover', '--a4-z-sidebar', '--a4-z-header',
                        '--a4-z-sidebar-toggle', '--a4-z-menu', '--a4-z-menu-sub', '--a4-z-overlay', '--a4-z-modal',
                        '--a4-z-toast' );
        foreach ( $order as $i => $token )
        {
            $this->assertArrayHasKey( $token, $tokens );
            if ( $i > 0 )
            {
                $this->assertGreaterThan( $tokens[$order[$i - 1]], $tokens[$token], "$token must lie above {$order[$i - 1]}" );
            }
        }
    }

    /** The tabs on the window edges say which column they show and whether it is shown; Escape closes an overlay column. */
    public function testSidebarTabsSayWhatTheyControl()
    {
        $tpl = (string)file_get_contents( self::$root . '/design/admin4/templates/page_topmenu.tpl' );
        $this->assertStringContainsString( 'class="sidebar-control left" aria-controls="leftmenu"', $tpl );
        $this->assertStringContainsString( 'class="sidebar-control right" aria-controls="rightmenu"', $tpl );
        $this->assertStringContainsString( "'Left column'|i18n( 'design/admin/pagelayout' )|wash", $tpl );
        $js = (string)file_get_contents( self::$root . '/design/admin4/javascript/ezadmin_menubar.js' );
        $this->assertStringContainsString( '.attr("aria-expanded", open ? "true" : "false")', $js );
        $this->assertStringContainsString( 'e.key !== "Escape"', $js );
        $this->assertStringContainsString( 'control.focus();', $js );
        // while a context menu is shown, Escape belongs to the menu, whatever the script order
        $this->assertStringContainsString( 'document.querySelectorAll("div.popupmenu")', $js );
    }

    public function testComponentsAreTheSameInAdminAndAdmin4()
    {
        $this->assertFileEquals( self::$root . '/design/admin4/stylesheets/components.css', self::$root . '/design/admin/stylesheets/components.css',
            'design/admin/stylesheets/components.css is a copy of the admin4 file; change admin4 and copy it' );
        // the module stylesheets too: design/admin loads the same components and the same module groups
        foreach ( glob( self::$root . '/design/admin4/stylesheets/modules/*.css' ) as $file )
        {
            $this->assertFileEquals( $file, self::$root . '/design/admin/stylesheets/modules/' . basename( $file ),
                'design/admin/stylesheets/modules/' . basename( $file ) . ' is a copy of the admin4 file; change admin4 and copy it' );
        }
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
