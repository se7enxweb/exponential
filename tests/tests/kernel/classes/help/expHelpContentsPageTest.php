<?php
/**
 * The contents page of the help (exphelp/contents, doc/eng-us/features/6.0/help-system.md, "All help pages"), without a
 * database: its folders closed, Expand all / Collapse all, deep links to a folder.
 *
 *  HCP-01  the tree is closed by default (native <details>, title and page count), every folder gets a unique id from
 *          its path so #exphelp-folder-... links to it; the view no longer opens every folder
 *  HCP-02  each admin design's contents.tpl has the control: a role=group with two real buttons (type=button,
 *          aria-controls = the tree's id), hidden until exphelp.js shows it, translated strings, no inline CSS or JS
 *  HCP-03  the control's strings are in the 11 complete kernel catalogues (context kernel/exphelp), once each,
 *          translated (empty only in untranslated); the client strings carry the two announcements
 *  HCP-04  help.css places the control with tokens, sticky at the end side of the help column (never over the side
 *          menus), its room kept from the first paint, above the debug bar by --exp-debug-clearance, which
 *          expdebugbar.css sets for the bar and for its minimised tab
 *  HCP-06  the help navigation of the viewer and the contents page has a "Clear filter" button (type=button,
 *          aria-controls the filter field, hidden until there is a filter), translated in the 11 catalogues
 *  HCP-07  help.css never styles .a4-help itself (the shared field help text of components.css; help.css loads on
 *          every admin page): the help page frame is .a4-help-frame in every exphelp template
 *  HCP-05  the help dashboard shows the Exponential logo right above "Help center": admin4 the header's inline drawing
 *          (dark mode through its tokens), classic the same drawing's file; named "Exponential", a fixed size
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

if ( !class_exists( 'expHelpNavigation' ) )
    require_once __DIR__ . '/../../../../../kernel/classes/help/exphelpnavigation.php';
if ( !class_exists( 'Exponential\\View\\Kernel\\Exphelp\\Contents' ) )
    require_once __DIR__ . '/../../../../../kernel/private/classes/views/exphelp/contents.php';

class expHelpContentsPageTest extends PHPUnit\Framework\TestCase
{
    const STRINGS = array( 'Expand all', 'Collapse all', 'Open or close every folder', 'Every folder is open.', 'Every folder is closed.' );
    const CATALOGUES = array( 'eng-US', 'untranslated', 'ger-DE', 'fre-FR', 'esl-ES', 'esl-MX', 'ukr-UA', 'heb-IL', 'lat-VA', 'tlh-QO', 'ara-SA' );

    /** @var string */
    protected $cache;

    protected function setUp(): void
    {
        $this->cache = sys_get_temp_dir() . '/exphelpcontents-' . getmypid() . '-' . mt_rand();
    }

    protected function tearDown(): void
    {
        if ( is_dir( $this->cache . '/nav' ) )
        {
            foreach ( glob( $this->cache . '/nav/*' ) as $file )
                unlink( $file );
            rmdir( $this->cache . '/nav' );
        }
        if ( is_dir( $this->cache ) )
            rmdir( $this->cache );
    }

    private static function root()
    {
        return dirname( __DIR__, 5 );
    }

    private static function node( $name, $path, $title, array $children = array(), array $documents = array() )
    {
        return array( 'name' => $name, 'path' => $path, 'title' => $title, 'index' => null, 'index_id' => null, 'order' => 1000,
                      'children' => $children, 'documents' => $documents );
    }

    private static function doc( $id, $title )
    {
        return array( 'id' => $id, 'path' => 'doc/' . $id, 'title' => $title, 'order' => 1000, 'type' => 'feature', 'audience' => array() );
    }

    private function index()
    {
        $tree = self::node( '', '', '', array(
            self::node( 'help', 'help', 'Help', array(
                self::node( 'editors', 'help/editors', 'Editors', array(), array( self::doc( 'help/editors/a.md', 'A' ) ) ),
                self::node( 'Editors', 'help/Editors', 'Editors again', array(), array( self::doc( 'help/Editors/b.md', 'B' ) ) ),
            ) ),
            self::node( 'bc', 'bc', 'Bc', array(), array( self::doc( 'bc/c.md', 'C' ), self::doc( 'bc/d.md', 'D' ) ) ),
        ) );
        $cache = $this->cache;
        return new class( $tree, $cache ) {
            private $tree; private $cache;
            public function __construct( $tree, $cache ) { $this->tree = $tree; $this->cache = $cache; }
            public function signature() { return 'contents'; }
            public function cacheDir() { return $this->cache; }
            public function tree() { return $this->tree; }
        };
    }

    /** HCP-01 */
    public function testTreeClosedWithFolderIds()
    {
        $html = \Exponential\View\Kernel\Exphelp\Contents::folderIds( ( new expHelpNavigation( $this->index() ) )->html() );
        $this->assertStringNotContainsString( '<details open', $html, 'every folder starts closed' );
        $this->assertSame( 4, substr_count( $html, '<details class="a4-help-nav-details" id="exphelp-folder-' ) );
        $this->assertStringContainsString( 'id="exphelp-folder-help" data-nav-path="help"', $html );
        $this->assertStringContainsString( 'id="exphelp-folder-help-editors" data-nav-path="help/editors"', $html );
        $this->assertStringContainsString( 'id="exphelp-folder-help-editors-2" data-nav-path="help/Editors"', $html, 'ids are unique' );
        $this->assertMatchesRegularExpression( '#id="exphelp-folder-bc" data-nav-path="bc"><summary class="a4-help-nav-summary"><span class="a4-help-nav-text" dir="auto">[^<]+</span><span class="a4-help-nav-count" aria-hidden="true">2</span>#', $html,
                                           'a closed folder shows its title and page count' );
        $view = (string)file_get_contents( self::root() . '/kernel/private/classes/views/exphelp/contents.php' );
        $this->assertStringContainsString( "self::folderIds( \$navigation->html() )", $view );
        $this->assertStringNotContainsString( "html( '', true )", $view, 'the contents page no longer opens every folder' );
    }

    /** HCP-02 */
    public function testTemplatesCarryTheControl()
    {
        foreach ( array( 'design/admin4', 'design/admin' ) as $design )
        {
            $tpl = (string)file_get_contents( self::root() . "/$design/templates/exphelp/contents.tpl" );
            $this->assertStringContainsString( 'id="exphelp-contents-tree"', $tpl, $design );
            $this->assertMatchesRegularExpression( '#<div class="a4-help-contents-controls" role="group" aria-label="\{\'Open or close every folder\'\|i18n\( \'kernel/exphelp\' \)\|wash\}" data-exphelp-contents-controls hidden>#', $tpl, $design );
            foreach ( array( 'expand-all' => 'Expand all', 'collapse-all' => 'Collapse all' ) as $hook => $label )
            {
                $this->assertMatchesRegularExpression( '#<button type="button" class="a4-btn a4-btn-small" aria-controls="exphelp-contents-tree" data-exphelp-' . $hook . '>.*\{\'' . $label . '\'\|i18n\( \'kernel/exphelp\' \)\}</button>#', $tpl, "$design $hook" );
            }
            $this->assertDoesNotMatchRegularExpression( '#<style|style="|<script|\son[a-z]+=#i', $tpl, "$design: no inline CSS or JS" );
            $this->assertStringContainsString( "{ezscript_require( array( 'exphelp.js' ) )}", $tpl );
        }
        $this->assertFileEquals( self::root() . '/design/admin4/templates/exphelp/contents.tpl', self::root() . '/design/admin/templates/exphelp/contents.tpl' );
    }

    /** HCP-03 */
    public function testStringsTranslatedOnce()
    {
        foreach ( self::CATALOGUES as $lang )
        {
            $dom = new DOMDocument();
            $this->assertTrue( $dom->load( self::root() . "/share/translations/$lang/translation.ts" ), $lang );
            $found = array();
            foreach ( $dom->getElementsByTagName( 'context' ) as $context )
            {
                if ( trim( $context->getElementsByTagName( 'name' )->item( 0 )->textContent ) !== 'kernel/exphelp' )
                    continue;
                foreach ( $context->getElementsByTagName( 'message' ) as $message )
                {
                    $source = $message->getElementsByTagName( 'source' )->item( 0 )->textContent;
                    if ( in_array( $source, self::STRINGS, true ) )
                        $found[$source][] = $message->getElementsByTagName( 'translation' )->item( 0 )->textContent;
                }
            }
            foreach ( self::STRINGS as $source )
            {
                $this->assertArrayHasKey( $source, $found, "$lang: $source" );
                $this->assertCount( 1, $found[$source], "$lang: $source once" );
                if ( $lang !== 'untranslated' )
                    $this->assertNotSame( '', trim( $found[$source][0] ), "$lang: $source translated" );
                if ( !in_array( $lang, array( 'eng-US', 'untranslated' ), true ) )
                    $this->assertNotSame( $source, $found[$source][0], "$lang: $source is not the English text" );
            }
        }
        $php = (string)file_get_contents( self::root() . '/kernel/classes/help/exphelp.php' );
        $this->assertStringContainsString( "'allOpen' => ezpI18n::tr( \$c, 'Every folder is open.' )", $php );
        $this->assertStringContainsString( "'allClosed' => ezpI18n::tr( \$c, 'Every folder is closed.' )", $php );
    }

    /** HCP-05 */
    public function testDashboardShowsTheLogoAboveHelpCenter()
    {
        foreach ( array( 'design/admin4', 'design/admin' ) as $design )
        {
            $tpl = (string)file_get_contents( self::root() . "/$design/templates/exphelp/dashboard.tpl" );
            $this->assertMatchesRegularExpression( "#\{include uri='design:exphelp/parts/dashboard_brand.tpl'\}\s*<p class=\"a4-help-hero-pre\">#", $tpl, "$design: the logo right above Help center" );
        }
        $admin4 = (string)file_get_contents( self::root() . '/design/admin4/templates/exphelp/parts/dashboard_brand.tpl' );
        $this->assertStringContainsString( '<p class="a4-help-brand" role="img" aria-label="Exponential">{include uri=\'design:parts/a4_logo.tpl\'}</p>', $admin4,
                                           "admin4: the header's own drawing, named Exponential" );
        $logo = (string)file_get_contents( self::root() . '/design/admin4/templates/parts/a4_logo.tpl' );
        $this->assertStringContainsString( 'fill="currentColor"', $logo, 'the drawing takes the ink color, so it follows the dark mode' );
        $classic = (string)file_get_contents( self::root() . '/design/admin/templates/exphelp/parts/dashboard_brand.tpl' );
        $this->assertStringContainsString( "src={'design/admin4/images/admin4/exponential-logo.svg'|ezroot} width=\"203\" height=\"32\" alt=\"Exponential\"", $classic,
                                           'classic: the same drawing as a file of this repository, with its size and name' );
        $this->assertFileExists( self::root() . '/design/admin4/images/admin4/exponential-logo.svg' );
        foreach ( array( $admin4, $classic ) as $part )
            $this->assertDoesNotMatchRegularExpression( '#<style|style="|<script|https?://#i', preg_replace( '/\{\*.*?\*\}/s', '', $part ) );
        $css = (string)file_get_contents( self::root() . '/design/admin4/stylesheets/modules/help.css' );
        $this->assertMatchesRegularExpression( '#\.a4-help-brand \.a4-logo-full,\s*\.a4-help-brand-img \{[^}]*width: calc\(var\(--a4-space-6\) \* 380 / 60\);\s*height: var\(--a4-space-6\);#', $css, 'a fixed size' );
    }

    /** HCP-06 */
    public function testNavigationHasAClearFilterButton()
    {
        $button = '<button type="button" class="a4-btn a4-btn-ghost a4-btn-small a4-help-nav-clear" aria-controls="exphelp-nav-filter" data-exphelp-navfilter-clear hidden>'
            . '<span class="a4-icon a4-bi-x-lg" aria-hidden="true"></span> {\'Clear filter\'|i18n( \'kernel/exphelp\' )}</button>';
        foreach ( array( 'design/admin4', 'design/admin' ) as $design )
        {
            foreach ( array( 'viewer', 'contents' ) as $view )
            {
                $tpl = (string)file_get_contents( self::root() . "/$design/templates/exphelp/$view.tpl" );
                $this->assertStringContainsString( 'id="exphelp-nav-filter"', $tpl, "$design $view" );
                $this->assertStringContainsString( $button, $tpl, "$design $view: the clear button" );
            }
            $css = (string)file_get_contents( self::root() . "/$design/stylesheets/modules/help.css" );
            $this->assertStringContainsString( '.a4-help-nav-clear[hidden] { display: none; }', $css );
        }
        foreach ( self::CATALOGUES as $lang )
        {
            $dom = new DOMDocument();
            $dom->load( self::root() . "/share/translations/$lang/translation.ts" );
            $count = 0;
            foreach ( $dom->getElementsByTagName( 'context' ) as $context )
            {
                if ( trim( $context->getElementsByTagName( 'name' )->item( 0 )->textContent ) !== 'kernel/exphelp' )
                    continue;
                foreach ( $context->getElementsByTagName( 'message' ) as $message )
                {
                    if ( $message->getElementsByTagName( 'source' )->item( 0 )->textContent === 'Clear filter' )
                    {
                        $count++;
                        if ( $lang !== 'untranslated' )
                            $this->assertNotSame( '', trim( $message->getElementsByTagName( 'translation' )->item( 0 )->textContent ), $lang );
                    }
                }
            }
            $this->assertSame( 1, $count, "$lang: Clear filter once in kernel/exphelp" );
        }
    }

    /** HCP-07 */
    public function testHelpStylesheetNeverStylesTheSharedFieldHelpClass()
    {
        // .a4-help is the shared help text under a form field (components.css); help.css loads on every admin page,
        // so a rule for .a4-help itself turned every field help across the administration into a narrow grid
        foreach ( array( 'design/admin4', 'design/admin' ) as $design )
        {
            $css = (string)file_get_contents( self::root() . "/$design/stylesheets/modules/help.css" );
            $this->assertSame( 0, preg_match_all( '/\.a4-help(?![\w-])/', $css, $m ), "$design help.css styles only .a4-help-* classes, never .a4-help: " . implode( ', ', $m[0] ) );
            $this->assertMatchesRegularExpression( '/^\.a4-help-frame \{/m', $css, 'the help page frame' );
            foreach ( glob( self::root() . "/$design/templates/exphelp/{,parts/}*.tpl", GLOB_BRACE ) as $tpl )
            {
                $html = (string)file_get_contents( $tpl );
                $this->assertDoesNotMatchRegularExpression( '/class="a4-page a4-help[" ]/', $html, basename( $tpl ) . ': the frame is .a4-help-frame' );
                if ( preg_match( '/class="a4-page /', $html ) )
                    $this->assertMatchesRegularExpression( '/class="a4-page a4-help-frame[" ]/', $html, basename( $tpl ) );
            }
        }
    }

    /** HCP-04 */
    public function testControlStaysClearOfTheDebugBar()
    {
        foreach ( array( 'design/admin4', 'design/admin' ) as $design )
        {
            $css = (string)file_get_contents( self::root() . "/$design/stylesheets/modules/help.css" );
            $this->assertMatchesRegularExpression( '#\.a4-help-contents-controls \{[^}]*position: sticky;[^}]*bottom: calc\(var\(--exp-debug-clearance, 0px\) \+ var\(--a4-space-4\)\);[^}]*justify-self: end;#', $css, "$design: sticky at the end of the help column, never over the side menus" );
            $this->assertStringNotContainsString( 'position: fixed', substr( $css, (int)strpos( $css, '.a4-help-contents-controls {' ), 400 ) );
            $this->assertStringContainsString( '.a4-js .a4-help-contents-controls[hidden] { display: flex; visibility: hidden; }', $css, 'its room is kept until the script shows it (no layout shift)' );
            $this->assertStringContainsString( '.a4-help-contents-controls[hidden] { display: none; }', $css );
        }
        // the classic page frame clips without being a scroll box, or sticky sticks to it instead of the window
        $theme = (string)file_get_contents( self::root() . '/design/admin/stylesheets/theme/rounded.css' );
        $this->assertMatchesRegularExpression( '/#page\s*\{[^}]*overflow: clip;[^}]*display: flow-root;/', $theme, 'classic #page: overflow clip (no scroll box) and still containing its floats' );
        $this->assertDoesNotMatchRegularExpression( '/#page\s*\{[^}]*overflow: hidden/', $theme );
        // a stored filter: the tree and the clear button keep their room, invisible, until it is applied
        $css = (string)file_get_contents( self::root() . '/design/admin4/stylesheets/modules/help.css' );
        $this->assertStringContainsString( '.exp-help-nav-pending [data-exphelp-nav] .a4-help-nav-list.is-top { visibility: hidden; }', $css );
        $this->assertStringContainsString( '.exp-help-nav-pending [data-exphelp-nav] .a4-help-nav-clear[hidden] { display: inline-flex; visibility: hidden; }', $css );
        $debug = (string)file_get_contents( self::root() . '/design/standard/stylesheets/expdebugbar.css' );
        $this->assertStringContainsString( 'body.exp-debug-bar { --exp-debug-clearance: var(--exp-debug-bar-h, 38px); }', $debug );
        $this->assertStringContainsString( 'body:has(#debug.exp-debug-min:not([hidden])) { --exp-debug-clearance: 54px; }', $debug );
        // the minimised tab is 8px off the bottom and 40px high, its badge 6px above it: 54px
        $this->assertMatchesRegularExpression( '#button\.exp-debug-restore \{[^}]*bottom: 8px;[^}]*height: 40px;#', $debug );
        $this->assertMatchesRegularExpression( '#\.exp-debug-restore-badge \{[^}]*top: -6px;#', $debug );
    }
}
