<?php
/**
 * The script of the help pages (design/admin4/javascript/exphelp.js, module exphelp). Checked on the file and, in node,
 * against a small stand-in for the DOM (js/mini_dom.js, harness js/exphelp.js); no database.
 *
 * - HELPJS-01: the instant search asks the JSON view named by data-exphelp-endpoint (q trimmed, the checked filters,
 *   limit, format=json, Accept: application/json), builds the results as text (a <b> or <img> in a title or snippet
 *   stays text, only the matches become <mark>), drops a result whose link is not this site's or https (javascript:),
 *   reads the snippet from snippet_parts or, without them, from the escaped snippet, shows the count in the page's
 *   language (data-exphelp-i18n) and fills the facet counts.
 * - HELPJS-02: an older answer arriving after a newer one is ignored; an empty query sends nothing; a failed request or
 *   a browser without fetch says the search is not available (the form still goes to the search page); a changed
 *   filter searches again.
 * - HELPJS-03: keys: / focuses the search (not while typing, not with Ctrl, and before the admin header's own /
 *   shortcut of admin4.js, not out of a dialog), ArrowDown/ArrowUp move the focus through
 *   the results and back to the field, Enter opens the first result once the list is up (before that the form is
 *   sent), Esc closes the list, then clears the field.
 * - HELPJS-04: every <pre> of the page gets one copy button (type=button), in A's .a4-help-code wrapper when there is
 *   one; it copies the code's text through the clipboard, says so in a live region and turns back after a while;
 *   without a clipboard it tells the user to press Ctrl+C.
 * - HELPJS-05: with data-exphelp-collapsible="h2" each h2 gets a show/hide button (aria-expanded, aria-controls) over
 *   its section; a link to a heading inside a closed section, or inside a closed <details>, opens it.
 * - HELPJS-06: the table of contents marks the heading being read (aria-current="location", is-current); j/k go to
 *   the next/previous heading, [ and ] to the previous/next page, never to a javascript: link.
 * - HELPJS-07: pages without [data-exphelp] are left alone. The file has no eval, no new Function, no innerHTML or
 *   document.write (it runs under the admin's Content Security Policy, script-src 'self' + nonce), and does not
 *   need jQuery; every other design's copy of it is identical, and every exphelp template that carries a hook loads it.
 * - HELPJS-08: with data-exphelp-canonical-base on the form, sending it goes to the canonical address
 *   (/exphelp/search/(q)/<words>/(category)/<id>/(audience)/<id>/(type)/<id>), and on the search page itself the
 *   address follows the search (the answer's canonical_href when it is safe); an empty field with a filter set lists
 *   the pages of that filter (browse mode).
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminHelpScriptTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminNodeBinary.php';

class AdminHelpScriptTest extends PHPUnit\Framework\TestCase
{
    const SCRIPT = 'design/admin4/javascript/exphelp.js';

    private static $cases = null;

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function read( $path )
    {
        $file = self::root() . '/' . $path;
        self::assertFileExists( $file );
        return (string)file_get_contents( $file );
    }

    /** The JSON lines the harness printed, by case name; run once for the class */
    private function cases()
    {
        if ( self::$cases === null )
        {
            $node = AdminNodeBinary::require();
            $out = (string)shell_exec( escapeshellarg( $node ) . ' ' . escapeshellarg( __DIR__ . '/js/exphelp.js' ) . ' '
                                       . escapeshellarg( self::root() . '/' . self::SCRIPT ) . ' 2>&1' );
            $cases = array();
            foreach ( array_filter( explode( "\n", $out ) ) as $line )
            {
                $row = json_decode( $line, true );
                $this->assertIsArray( $row, "the harness ran: $out" );
                $cases[$row['name']] = $row;
            }
            $this->assertArrayNotHasKey( 'crash', $cases, $out );
            self::$cases = $cases;
        }
        return self::$cases;
    }

    /** HELPJS-01 */
    public function testTheSearchAsksTheJsonViewAndShowsTextOnly()
    {
        $c = $this->cases()['search'];
        $this->assertSame( 1, $c['requests'], 'one request after the pause in typing' );
        $this->assertSame( '/admin/exphelp/search?q=cache&audience=editor&limit=20&format=json', $c['url'] );
        $this->assertSame( 'application/json', $c['accept'] );
        $this->assertFalse( $c['hidden'] );
        $this->assertSame( array( '/admin/exphelp/viewer/(path)/doc/a.md#clear', '/admin/exphelp/viewer/(path)/doc/c.md' ), $c['links'],
                           'the javascript: result is dropped' );
        $this->assertSame( array( 'Clearing <b>caches</b>', 'Fallback' ), $c['titles'], 'markup in a title stays text' );
        $this->assertSame( 0, $c['imgs'], 'markup in a snippet stays text' );
        $this->assertSame( array( 'cache', 'cache' ), $c['marks'] );
        $this->assertSame( array( 'Run the cache <img src=x onerror=alert(1)> tool', 'a <b> cache & more' ), $c['snippet'] );
        $this->assertSame( array( 'admin', 'operations', 'editor' ), $c['tags'] );
        $this->assertSame( '3 Treffer', $c['status'], 'the count in the language of the page' );
        $this->assertSame( '7', $c['count'], 'the facet count' );
        $this->assertSame( 'true', $c['expanded'] );
    }

    /** HELPJS-02 */
    public function testTheSearchKeepsTheNewestAnswerAndSaysWhenItFails()
    {
        $c = $this->cases();
        $this->assertSame( 2, $c['stale answer ignored']['requests'] );
        $this->assertSame( array( '/new' ), $c['stale answer ignored']['links'] );
        $this->assertSame( '1 result', $c['stale answer ignored']['status'] );
        $this->assertSame( 0, $c['empty query']['requests'] );
        $this->assertTrue( $c['empty query']['hidden'] );
        $this->assertFalse( $c['error']['hidden'] );
        $this->assertSame( 0, $c['error']['links'] );
        $this->assertStringContainsString( 'not available', $c['error']['status'] );
        $this->assertStringContainsString( 'not available', $c['no fetch']['status'] );
        $this->assertSame( 2, $c['filter change']['requests'] );
        $this->assertSame( '/admin/exphelp/search?q=cache&category=x&limit=20&format=json', $c['filter change']['url'] );
    }

    /** HELPJS-03 */
    public function testTheSearchKeys()
    {
        $c = $this->cases();
        $k = $c['keys'];
        $this->assertSame( 'q', $k['focusInput'], '/ focuses the field' );
        $this->assertFalse( $k['slashInInput'], '/ typed in the field is a character' );
        $s = $c['slash beats the header search'];
        $this->assertSame( 'q', $s['focused'], "/ goes to the help search before the admin header's own / shortcut (capture phase)" );
        $this->assertTrue( $s['prevented'] );
        $this->assertSame( 'searchtext', $s['disabledGoesToHeader'], 'without a usable help search the header keeps /' );
        $this->assertNotSame( 'q', $s['fromDialog'], 'not out of a dialog' );
        $this->assertSame( '/admin/exphelp/viewer/(path)/doc/a.md#clear', $k['first'] );
        $this->assertSame( '/admin/exphelp/viewer/(path)/doc/c.md', $k['second'] );
        $this->assertSame( '/admin/exphelp/viewer/(path)/doc/c.md', $k['stays'], 'the last result stays' );
        $this->assertSame( 'q', $k['back'], 'ArrowUp from the first result goes back to the field' );
        $this->assertTrue( $k['enterPrevented'] );
        $this->assertSame( array( '/admin/exphelp/viewer/(path)/doc/a.md#clear' ), $k['assigned'] );
        $this->assertTrue( $k['escPrevented'] );
        $this->assertSame( array( 'hidden' => true, 'links' => 0, 'value' => 'cache' ), $k['afterEsc'] );
        $this->assertSame( '', $k['value2'], 'a second Esc clears the field' );
        $this->assertFalse( $c['error']['early'], 'Enter before the answer sends the form' );
    }

    /** HELPJS-04 */
    public function testCopyButtons()
    {
        $c = $this->cases();
        $this->assertSame( 2, $c['copy']['wraps'] );
        $this->assertSame( array( 1, 1 ), $c['copy']['perWrap'], 'one button per block, the renderer\'s wrapper reused' );
        $this->assertSame( array( 'button', 'button' ), $c['copy']['types'] );
        $this->assertSame( array( '$a = 1 < 2;' ), $c['copy']['copied'] );
        $this->assertSame( 'Copied', $c['copy']['label'] );
        $this->assertSame( 'Copy', $c['copy']['after'] );
        $this->assertSame( 'Copied', $c['copy']['announced'] );
        $this->assertSame( 'Press Ctrl+C to copy', $c['copy without clipboard']['label'] );
    }

    /** HELPJS-05 */
    public function testCollapsibleSections()
    {
        $c = $this->cases()['collapse'];
        $this->assertSame( 2, $c['toggles'] );
        $this->assertSame( array( 'true', 'true' ), $c['before'], 'sections start open' );
        $this->assertStringContainsString( 'echo 1;', $c['sectionOneText'] );
        $this->assertSame( array( 'expanded' => 'false', 'hidden' => true, 'label' => 'Show section: Two' ), $c['closed'] );
        $this->assertSame( array( 'expanded' => 'true', 'hidden' => false ), $c['reopened'], 'a link into a closed section opens it' );
        $this->assertTrue( $c['detailsOpen'], 'a link into a closed <details> opens it' );
        $this->assertCount( 2, array_unique( $c['controls'] ) );
    }

    /** HELPJS-06 */
    public function testContentsAndPageKeys()
    {
        $c = $this->cases();
        $this->assertSame( array( 't2' ), $c['scrollspy']['first'] );
        $this->assertSame( array( 't3' ), $c['scrollspy']['second'] );
        $this->assertTrue( $c['scrollspy']['cls'] );
        $this->assertFalse( $c['scrollspy']['old'] );
        $p = $c['page keys'];
        $this->assertSame( array( 'two', 'one' ), $p['scrolled'], 'j to the next heading, k back' );
        $this->assertSame( '#one', $p['hash'] );
        $this->assertFalse( $p['typingJ'], 'j typed in a field is a character' );
        $this->assertTrue( $p['prev'] );
        $this->assertFalse( $p['next'], 'never a javascript: link' );
        $this->assertSame( array( '/admin/exphelp/viewer/(path)/doc/a.md' ), $p['assigned'] );
        $this->assertFalse( $p['ctrl'] );
    }

    /** HELPJS-08 */
    public function testTheCanonicalSearchAddressAndBrowsing()
    {
        $c = $this->cases()['canonical address'];
        $this->assertTrue( $c['submitPrevented'] );
        $this->assertSame( array( '/admin/exphelp/search/(q)/a+b+c+d%2Be/(category)/guides/(audience)/editor' ), $c['assigned'],
                           "the form goes to the canonical address: q, category, audience, type; '/', '(' and ')' become spaces, a space '+'" );
        $this->assertFalse( $c['plainPrevented'], 'without data-exphelp-canonical-base the form is sent as it is' );
        $this->assertSame( array( '/admin/exphelp/search' ), $c['emptyAssigned'] );
        $this->assertSame( '/admin/exphelp/search?q=&audience=editor&limit=20&format=json', $c['browseUrl'],
                           'an empty field with a filter set asks for the browse list' );
        $this->assertSame( array( '/admin/exphelp/viewer/x.md' ), $c['browseLinks'] );
        $this->assertSame( '2 Treffer', $c['browseStatus'] );
        $this->assertSame( array(), $c['dashReplaced'], 'another page keeps its address' );
        $this->assertSame( array( '/admin/exphelp/search/(q)/cache', '/admin/exphelp/search/(q)/cach', '/admin/exphelp/search' ), $c['pageReplaced'],
                           "the search page follows the search: the server's canonical_href, its own address for an unsafe one, the base when emptied" );
    }

    /** HELPJS-07 */
    public function testTheScriptIsCspCleanAndLoaded()
    {
        $this->assertSame( 0, $this->cases()['other pages untouched']['listeners'], 'no listeners on other pages' );
        $js = self::read( self::SCRIPT );
        foreach ( array( '/\beval\s*\(/', '/new\s+Function\b/', '/\.innerHTML\b/', '/\.outerHTML\s*=/', '/insertAdjacentHTML/',
                         '/document\.write/', '/setTimeout\s*\(\s*[\'"]/', '/\bjQuery\b|\$\(/', '/\.style\./', '/setAttribute\(\s*[\'"]style/' ) as $re )
        {
            $this->assertDoesNotMatchRegularExpression( $re, $js, "exphelp.js must not match $re" );
        }
        $root = self::root();
        foreach ( glob( "$root/design/*/javascript/exphelp.js" ) as $copy )
        {
            $this->assertSame( $js, (string)file_get_contents( $copy ), "$copy is the same as admin4's" );
        }
        foreach ( glob( "$root/{design/*,extension/*/design/*}/templates/exphelp", GLOB_BRACE | GLOB_ONLYDIR ) as $dir )
        {
            $all = '';
            foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ) ) as $f )
            {
                if ( substr( $f->getFilename(), -4 ) === '.tpl' )
                    $all .= file_get_contents( $f->getPathname() );
            }
            if ( preg_match( '/data-exphelp-(search|content|toc)\b/', $all ) )
            {
                $this->assertMatchesRegularExpression( '/ezscript_require\(\s*[\'"]exphelp\.js[\'"]\s*\)/', $all, "$dir loads exphelp.js" );
            }
        }
    }
}
