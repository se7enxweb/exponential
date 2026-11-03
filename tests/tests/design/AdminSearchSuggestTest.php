<?php
/**
 * The suggestions under the header search field of the administration (every admin design): page_search.tpl,
 * parts/search_suggest.tpl, javascript/page/search_suggest.js, the "Suggestions" block of modules/search.css and the
 * view content/suggest (expSearchSuggest).
 *
 *  SG-01  one script, partial template and stylesheet, in design/admin, for every admin design
 *  SG-02  the field is a combobox controlling the listbox, with the address of content/suggest and the fewest
 *         characters, in both copies of admin3's header (admin3, admin4, admin4l) and in the admin design; not in the browse mode; the script is
 *         loaded once, through the design's script mechanism (ezscript), no inline script or style
 *  SG-03  the script: no jQuery, no innerHTML, the text as a view parameter (a4ViewParameter.encode), 250 ms, a
 *         cancelled request when the text changes, aria-activedescendant, every followed address checked (a4SafeURL)
 *  SG-04  the behaviour, run in node on mini_dom.js (js/search_suggest.js): min length, one request after the delay,
 *         groups with labelled headings, the server's text as text with the typed words marked, unsafe addresses
 *         left out, arrow keys with wrap, Enter on an entry and on "View all results", Escape, a click outside,
 *         focus again, a cancelled request, a failure, an empty answer, a sign-in page; Ctrl+/ and Cmd+/ focus the
 *         field, "/" alone is not taken
 *  SG-05  the stylesheet: the block uses the design's variables (dark mode) and no colour of its own, a static
 *         panel in the narrow screen menu, touch-sized entries
 *  SG-06  every new text is in the German catalogue; the settings are documented in admininterface.ini
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminSearchSuggestTest.php
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminNodeBinary.php';

require_once __DIR__ . '/AdminFiles.php';

class AdminSearchSuggestTest extends PHPUnit\Framework\TestCase
{
    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function read( $path )
    {
        return AdminFiles::read( self::root() . '/' . $path );
    }

    /** The "Suggestions" block of modules/search.css */
    private static function block( $design )
    {
        $css = self::read( "design/$design/stylesheets/modules/search.css" );
        $start = strpos( $css, '/* ---- Suggestions' );
        $end = strpos( $css, '/* ---- Results' );
        return $start !== false && $end !== false && $end > $start ? substr( $css, $start, $end - $start ) : '';
    }

    /** SG-01 */
    public function testOneCopyServesEveryDesign()
    {
        foreach ( array( 'javascript/page/search_suggest.js', 'templates/parts/search_suggest.tpl', 'stylesheets/modules/search.css' ) as $file )
        {
            $this->assertFileExists( self::root() . "/design/standard/$file" );
            foreach ( array( 'admin', 'admin3', 'admin4', 'admin4l' ) as $design )
                $this->assertFileDoesNotExist( self::root() . "/design/$design/$file", "$design/$file" );
        }
    }

    /** SG-02 */
    public function testTheFieldIsAComboboxWithTheSuggestAddress()
    {
        $partial = self::read( 'design/standard/templates/parts/search_suggest.tpl' );
        $this->assertStringContainsString( 'id="{$suggest_id|wash}-list" role="listbox"', $partial );
        $this->assertStringContainsString( 'id="{$suggest_id|wash}-status" role="status"', $partial );
        $this->assertStringContainsString( 'hidden="hidden"', $partial );

        foreach ( array( 'standard/templates/parts/header_search.tpl' => 'searchsuggest{$box}', 'admin/templates/page_search.tpl' => 'searchsuggest' ) as $design => $id )
        {
            $tpl = self::read( "design/$design" );
            $this->assertStringContainsString( 'role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="' . $id . '-list" ', $tpl, $design );
            $this->assertStringContainsString( "data-suggest-url={'content/suggest'|ezurl}", $tpl, $design );
            $this->assertStringContainsString( "data-suggest-min=\"{ezini( 'SearchSuggestSettings', 'MinLength', 'admininterface.ini' )|int}\"", $tpl, $design );
            $this->assertStringContainsString( "{include uri='design:parts/search_suggest.tpl' suggest_id=", $tpl, $design );
            $this->assertStringContainsString( "ezini( 'SearchSuggestSettings', 'Enabled', 'admininterface.ini' )|eq( 'true' ), \$ui_context|ne( 'browse' )", $tpl, $design );
            $this->assertMatchesRegularExpression( "#\\{ezscript\\( array\\( [^)]*'page/search_suggest\\.js' \\) \\)\\}#", $tpl, $design );
            $this->assertSame( 1, substr_count( $tpl, "'page/search_suggest.js'" ), "$design: loaded once" );
        }
        // admin3 (and admin4, admin4l): both copies of the header search, and the script once (with the header's copy)
        $tpl = self::read( 'design/standard/templates/parts/header_search.tpl' );
        $this->assertStringContainsString( "{if \$box|eq( '' )}\n{ezscript( array( 'page/page_search.js' ) )}\n{if \$search_suggest}{ezscript( array( 'page/search_suggest.js' ) )}{/if}", $tpl );
        $this->assertStringContainsString( "suggest_id=concat( 'searchsuggest', \$box )", $tpl );
        $this->assertStringContainsString( 'aria-keyshortcuts="/ Control+/ Meta+/"', $tpl );
        $this->assertStringNotContainsString( 'style=', self::read( 'design/standard/templates/parts/search_suggest.tpl' ) );
    }

    /** SG-03 */
    public function testTheScriptIsPlainAndSafe()
    {
        $js = self::read( 'design/standard/javascript/page/search_suggest.js' );
        // the code, without its comments
        $code = preg_replace( '#/\*.*?\*/|^\s*//.*$#ms', '', $js );
        foreach ( array( 'jQuery', '$(', 'innerHTML', 'insertAdjacentHTML', 'outerHTML', 'document.write', 'YUI' ) as $forbidden )
            $this->assertStringNotContainsString( $forbidden, $code, $forbidden );
        $this->assertStringContainsString( "base + '/(text)/' + encode(query)", $js );
        $this->assertStringContainsString( 'window.a4ViewParameter.encode(value)', $js );
        $this->assertStringContainsString( 'var DELAY = 250', $js );
        $this->assertStringContainsString( 'new AbortController()', $js );
        $this->assertStringContainsString( "controller.abort()", $js );
        $this->assertStringContainsString( "'aria-activedescendant'", $js );
        $this->assertStringContainsString( "window.a4SafeURL(url, 'search_suggest')", $js );
        $this->assertStringContainsString( "credentials: 'same-origin'", $js );
        $this->assertStringContainsString( "document.createElement('mark')", $js );
    }

    /** SG-04 */
    public function testTheBehaviour()
    {
        $node = AdminNodeBinary::require();
        $out = (string)shell_exec( escapeshellarg( $node ) . ' ' . escapeshellarg( __DIR__ . '/js/search_suggest.js' ) . ' '
                                   . escapeshellarg( self::root() . '/design/standard/javascript/page/search_suggest.js' ) . ' '
                                   . escapeshellarg( self::root() . '/design/admin4/javascript/a4_url.js' ) . ' 2>&1' );
        $s = json_decode( $out, true );
        $this->assertIsArray( $s, 'the harness ran: ' . $out );
        $this->assertArrayNotHasKey( 'error', $s, $out );

        // fewer characters than data-suggest-min: nothing asked, nothing shown
        $this->assertSame( 0, $s['short']['requests'] );
        $this->assertFalse( $s['short']['state']['open'] );

        // one request, 250 ms after the last key, the text as a view parameter, with the session's cookie
        $r = $s['render'];
        $this->assertSame( 0, $r['before'] );
        $this->assertSame( 1, $r['requests'] );
        $this->assertSame( '/admin/content/suggest/(text)/admi', $r['url'] );
        $this->assertSame( 'same-origin', $r['credentials'] );
        $this->assertTrue( $r['signal'] );
        $st = $r['state'];
        $this->assertTrue( $st['open'] );
        $this->assertSame( 'true', $st['expanded'] );
        $this->assertSame( array( 'Content 9', 'Document pages', 'Administration' ), $st['headings'] );
        $this->assertSame( array( true, true, true ), $st['groups'], 'each group is labelled by its heading' );
        // the server's text is text: no <b> element, the typed words marked
        $this->assertSame( 'Administrator <b>users</b>', $st['titles'][0] );
        $this->assertSame( 0, $st['bold'] );
        $this->assertSame( array( 'Admi', 'admi', 'Admi' ), $st['marks'] );
        // javascript: and another host are left out; three entries and "View all results" with the count
        $this->assertSame( array( '/admin/Users/Administrator-users', '/admin/pdfdeepsearch/viewer/abc/4', '/admin/setup/solr' ), $st['hrefs'] );
        $this->assertSame( 4, $st['options'] );
        $this->assertSame( 'ALL', end( $st['titles'] ) );
        $this->assertSame( array( 'User group', 'Page 4', '9 RESULTS' ), $st['metas'] );
        $this->assertSame( array( 'Users' ), $st['paths'] );
        $this->assertSame( array( '/share/icons/folder.svg' ), $st['icons'] );
        $this->assertSame( '3 FOUND', $st['status'] );
        $this->assertNull( $st['active'] );

        // Down, Down, then Up twice wraps to "View all results"
        $this->assertTrue( $s['down1']['activeIsOption'] );
        $this->assertSame( array( $s['down1']['active'] ), $s['down1']['selected'] );
        $this->assertNotSame( $s['down1']['active'], $s['down2']['active'] );
        $this->assertStringEndsWith( '-all', $s['wrapUp']['active'] );
        // Enter on it searches: the form is sent, the list closed
        $this->assertSame( array( 'searchform' ), $s['enterAll']['submitted'] );
        $this->assertTrue( $s['enterAll']['prevented'] );
        $this->assertFalse( $s['enterAll']['open'] );
        // Enter on an entry follows its link
        $this->assertSame( array( '/admin/Users/Administrator-users' ), $s['enterItem']['clicked'] );
        $this->assertFalse( $s['enterItem']['open'] );
        // Escape closes and forgets the active entry; the same text focused again shows the list; a click outside closes
        $this->assertFalse( $s['escape']['open'] );
        $this->assertSame( 'false', $s['escape']['expanded'] );
        $this->assertNull( $s['escape']['active'] );
        $this->assertTrue( $s['escape']['prevented'] );
        $this->assertTrue( $s['refocus']['open'] );
        $this->assertFalse( $s['outside']['open'] );
        // a changed text cancels the running request; failures say so, an empty answer says "none"
        $this->assertTrue( $s['abort']['aborted'] );
        $this->assertSame( array( 'FAILED' ), $s['failed']['none'] );
        $this->assertSame( 'FAILED', $s['failed']['status'] );
        $this->assertSame( array( 'NONE' ), $s['empty']['none'] );
        $this->assertSame( 1, $s['empty']['options'], '"View all results" stays' );
        $this->assertSame( array( 'FAILED' ), $s['html']['none'], 'a sign-in page instead of JSON' );
        // the shortcuts
        $this->assertSame( 'other', $s['slash']['focus'] );
        $this->assertFalse( $s['slash']['prevented'] );
        $this->assertSame( 'searchtext', $s['ctrl']['focus'] );
        $this->assertTrue( $s['ctrl']['prevented'] );
        $this->assertSame( 'searchtext', $s['meta']['focus'] );
    }

    /** SG-05 */
    public function testTheStylesheetUsesTheDesignsVariables()
    {
        $block = self::block( 'standard' );
        $this->assertNotSame( '', $block );
        // not tied to one design's header markup (AdminUI's header has no .searchblock)
        $this->assertStringContainsString( "\n.a4-suggest {", $block );
        $this->assertStringContainsString( "\n.a4-suggest[hidden] {", $block );
        $this->assertStringContainsString( '.header-search-mobile .searchblock .a4-suggest {', $block );
        $this->assertStringContainsString( 'background: var(--a4-surface);', $block );
        $this->assertStringContainsString( '@media (pointer: coarse)', $block );
        $this->assertStringContainsString( '@media (forced-colors: active)', $block );
        $this->assertDoesNotMatchRegularExpression( '/#[0-9a-f]{3,8}\b|rgba?\(/i', $block, 'colours come from --a4-* variables, so dark mode follows' );
        $this->assertStringNotContainsString( '!important', $block );
    }

    /** SG-06 */
    public function testTextsAreTranslatedAndSettingsDocumented()
    {
        $ts = new DOMDocument();
        $this->assertTrue( $ts->load( self::root() . '/share/translations/ger-DE/translation.ts' ) );
        $xpath = new DOMXPath( $ts );
        $sources = array();
        preg_match_all( "/'([^']+)'\\|i18n\\( 'design\\/admin\\/search\\/suggest' \\)/", self::read( 'design/standard/templates/parts/search_suggest.tpl' ), $m );
        $sources = array_merge( $sources, $m[1] );
        preg_match_all( "/ezpI18n::tr\\( self::CONTEXT, '([^']+)'/", self::read( 'kernel/classes/expsearchsuggest.php' ), $m );
        $sources = array_merge( $sources, $m[1] );
        $this->assertGreaterThanOrEqual( 12, count( $sources ) );
        foreach ( $sources as $source )
        {
            $found = $xpath->query( "//context[name='design/admin/search/suggest']/message[source=" . self::xpathString( $source ) . "]/translation" );
            $this->assertSame( 1, $found->length, $source );
            $this->assertNotSame( '', trim( $found->item( 0 )->textContent ), $source );
        }

        $ini = self::read( 'settings/admininterface.ini' );
        $this->assertStringContainsString( "[SearchSuggestSettings]\n", $ini );
        foreach ( array( 'Enabled=true', 'MinLength=3', 'Groups[]=content', 'Groups[]=pages', 'Groups[]=users', 'Groups[]=classes',
                         'Groups[]=admin', 'Limits[content]=', 'PagesPerDocument=', 'PageHitClasses[]=file', 'Wildcard=auto',
                         'AdminPagesCacheTTL=', 'AdminMenuSkipParts[]=ezcontentnavigationpart' ) as $line )
            $this->assertStringContainsString( "\n" . $line, $ini, $line );
        $site = self::read( 'settings/site.ini' );
        $this->assertStringContainsString( "CacheItems[]=searchsuggest\n", $site );
        $this->assertStringContainsString( "[Cache_searchsuggest]\n", $site );
    }

    private static function xpathString( $value )
    {
        if ( strpos( $value, "'" ) === false )
            return "'" . $value . "'";
        return 'concat(\'' . str_replace( "'", "', \"'\", '", $value ) . '\')';
    }
}
