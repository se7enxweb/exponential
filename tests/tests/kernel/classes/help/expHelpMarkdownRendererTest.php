<?php
/**
 * The help's Markdown renderer and highlighter are safe (doc/eng-us/features/6.0/help-system.md, "Security"): whatever a
 * page under doc/ contains, the HTML it becomes has only allowed elements and attributes, no script, no event
 * handler, no inline style (the admin's Content Security Policy forbids it), and links and images only to safe places.
 * Every case is parsed with DOMDocument and checked element by element. No database, no settings needed: the link and
 * image resolvers are given.
 *
 *  HR-01  raw HTML: script, iframe, object, embed, style, form, svg, math, base, meta, link and event handlers never
 *         become elements or attributes
 *  HR-02  links: javascript:, data:, vbscript: in every spelling (case, entities, whitespace, autolinks, references)
 *         dropped; external links rel=noopener (and noreferrer); attribute breakouts stay inside the attribute
 *  HR-03  relative links and images go through the resolvers; a refused target (null) loses its href/src
 *  HR-04  images: https or a resolved asset only; never http:, data: or javascript:
 *  HR-05  code: fenced code escaped, the language class sanitized, highlighted tokens are spans only
 *  HR-06  headings: anchors unique and safe, TOC text plain, the title from the first heading
 *  HR-07  GFM tables, lists, emphasis, admonitions render (the subset the help uses)
 *  HR-08  isSafeUrl(), slug(), renderPlainText()
 *  HR-09  every Markdown file under doc/ renders to safe HTML (smoke, bounded)
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expHelpMarkdownRendererTest extends PHPUnit\Framework\TestCase
{
    /** elements the rendered help may contain */
    const ELEMENTS = array( 'html', 'body', 'div', 'p', 'br', 'hr', 'span', 'a', 'img', 'strong', 'b', 'em', 'i', 'del', 's', 'code', 'pre',
                            'kbd', 'sup', 'sub', 'mark', 'blockquote', 'ul', 'ol', 'li', 'dl', 'dt', 'dd', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
                            'table', 'thead', 'tbody', 'tfoot', 'tr', 'th', 'td', 'caption', 'figure', 'figcaption', 'aside', 'section',
                            'details', 'summary', 'input', 'button', 'svg', 'use', 'abbr', 'small', 'nav', 'header' );
    /** attributes the rendered help may carry */
    const ATTRIBUTES = array( 'href', 'src', 'alt', 'title', 'class', 'id', 'rel', 'target', 'lang', 'dir', 'role', 'colspan', 'rowspan',
                              'align', 'start', 'type', 'checked', 'disabled', 'width', 'height', 'loading', 'decoding', 'aria-label',
                              'aria-hidden', 'aria-labelledby', 'aria-describedby', 'aria-expanded', 'aria-controls', 'tabindex',
                              'data-lang', 'data-language', 'data-help-copy', 'data-help-code', 'data-anchor', 'data-level', 'data-kind',
                              'focusable', 'xlink:href', 'viewbox', 'open', 'scope', 'datetime', 'translate', 'hidden', 'data-copy',
                              'data-callout', 'data-type', 'referrerpolicy' );

    /** @var array the resolver calls */
    private $calls = array();

    private function renderer()
    {
        $this->calls = array();
        $calls = &$this->calls;
        return new expHelpMarkdownRenderer( array(
            'link_resolver' => static function ( $href, $currentId ) use ( &$calls ) {
                $calls[] = array( 'link', $href, $currentId );
                $href = trim( (string)$href );
                if ( preg_match( '#^https?://#i', $href ) || preg_match( '#^mailto:#i', $href ) )
                    return $href;
                if ( $href !== '' && $href[0] === '#' )
                    return $href;
                if ( preg_match( '#^[a-z][a-z0-9+.-]*:#i', $href ) )
                    return null;
                // the fixture "tree": ok.md and sub/ok.md exist, everything else is refused
                $id = expHelpLinks::resolvePath( preg_replace( '/#.*$/', '', $href ), $currentId );
                return in_array( $id, array( 'help/ok.md', 'help/sub/ok.md' ), true ) ? '/admin/exphelp/viewer/' . $id : null;
            },
            'image_resolver' => static function ( $src, $currentId ) use ( &$calls ) {
                $calls[] = array( 'image', $src, $currentId );
                if ( preg_match( '#^https://#i', (string)$src ) )
                    return $src;
                $id = expHelpLinks::resolvePath( (string)$src, $currentId );
                return $id === 'help/img/ok.png' ? '/admin/exphelp/asset/' . $id : null;
            } ) );
    }

    private function html( $markdown, $id = 'help/page.md' )
    {
        $r = $this->renderer()->render( $markdown, $id );
        $this->assertIsArray( $r );
        $this->assertArrayHasKey( 'html', $r );
        $this->assertArrayHasKey( 'toc', $r );
        $this->assertArrayHasKey( 'title', $r );
        $this->assertSafeHtml( $r['html'], $markdown );
        return $r;
    }

    /** @return DOMDocument */
    private static function dom( $html )
    {
        $dom = new DOMDocument();
        $prev = libxml_use_internal_errors( true );
        $dom->loadHTML( '<?xml encoding="UTF-8"><html><body>' . $html . '</body></html>', LIBXML_NONET );
        libxml_clear_errors();
        libxml_use_internal_errors( $prev );
        return $dom;
    }

    /** the safety contract of rendered help HTML */
    private function assertSafeHtml( $html, $source = '' )
    {
        $context = 'source: ' . substr( (string)$source, 0, 200 );
        $this->assertDoesNotMatchRegularExpression( '/<\s*(script|iframe|object|embed|style|form|base|meta|link|frame|frameset|textarea|select|template|math)\b/i',
                                                    $html, $context );
        $dom = self::dom( $html );
        foreach ( $dom->getElementsByTagName( '*' ) as $el )
        {
            $tag = strtolower( $el->nodeName );
            $this->assertContains( $tag, self::ELEMENTS, 'element <' . $tag . '> | ' . $context );
            foreach ( $el->attributes as $attr )
            {
                $name = strtolower( $attr->nodeName );
                $this->assertDoesNotMatchRegularExpression( '/^on/', $name, 'event handler ' . $name . ' on <' . $tag . '> | ' . $context );
                $this->assertNotSame( 'style', $name, 'inline style on <' . $tag . '> (CSP) | ' . $context );
                $this->assertNotSame( 'srcdoc', $name, $context );
                $this->assertNotSame( 'formaction', $name, $context );
                if ( strpos( $name, 'aria-' ) !== 0 && strpos( $name, 'data-' ) !== 0 )
                    $this->assertContains( $name, self::ATTRIBUTES, 'attribute ' . $name . ' on <' . $tag . '> | ' . $context );
                if ( in_array( $name, array( 'href', 'src', 'xlink:href' ), true ) )
                    $this->assertTrue( self::urlIsSafe( $attr->nodeValue, $name === 'src' ), $name . '="' . $attr->nodeValue . '" | ' . $context );
                if ( $name === 'class' )
                    $this->assertMatchesRegularExpression( '/^[A-Za-z0-9_ :.+#-]*$/', $attr->nodeValue, 'class="' . $attr->nodeValue . '" | ' . $context );
                if ( $name === 'id' )
                    $this->assertMatchesRegularExpression( '/^[\p{L}\p{N}\p{M}_.:-]+$/u', $attr->nodeValue, 'id="' . $attr->nodeValue . '" | ' . $context );
            }
            if ( $tag === 'a' && preg_match( '#^(https?:)?//#i', (string)$el->getAttribute( 'href' ) ) && $el->getAttribute( 'target' ) === '_blank' )
                $this->assertStringContainsString( 'noopener', $el->getAttribute( 'rel' ), 'target=_blank without noopener | ' . $context );
            if ( $tag === 'input' )
                $this->assertSame( 'checkbox', strtolower( $el->getAttribute( 'type' ) ), 'only task-list checkboxes | ' . $context );
        }
    }

    private static function urlIsSafe( $url, $image )
    {
        $u = preg_replace( '/[\x00-\x20]+/', '', html_entity_decode( (string)$url, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
        if ( $u === '' || $u[0] === '#' )
            return !$image;
        if ( $image )
            return (bool)preg_match( '#^(https://|/[^/])#i', $u );
        if ( preg_match( '#^(https?:|mailto:)#i', $u ) )
            return true;
        if ( preg_match( '#^//#', $u ) )
            return false;
        return !preg_match( '#^[a-z][a-z0-9+.-]*:#i', $u );
    }

    /** @return DOMElement[] */
    private static function elements( $html, $tag )
    {
        $out = array();
        foreach ( self::dom( $html )->getElementsByTagName( $tag ) as $el )
            $out[] = $el;
        return $out;
    }

    public static function rawHtml()
    {
        return array(
            'script'        => array( "<script>alert(1)</script>" ),
            'script upper'  => array( "<SCRIPT SRC=//evil.example/x.js></SCRIPT>" ),
            'script split'  => array( "<scr<script>ipt>alert(1)</scr</script>ipt>" ),
            'img onerror'   => array( '<img src=x onerror=alert(1)>' ),
            'svg onload'    => array( '<svg onload=alert(1)><circle r=1 /></svg>' ),
            'svg script'    => array( '<svg><script>alert(1)</script></svg>' ),
            'iframe'        => array( '<iframe src="javascript:alert(1)"></iframe>' ),
            'iframe srcdoc' => array( '<iframe srcdoc="&lt;script&gt;alert(1)&lt;/script&gt;"></iframe>' ),
            'object'        => array( '<object data="data:text/html,<script>alert(1)</script>"></object>' ),
            'embed'         => array( '<embed src="javascript:alert(1)">' ),
            'style'         => array( "<style>body{background:url(javascript:alert(1))}</style>" ),
            'style attr'    => array( '<p style="background:url(javascript:alert(1))">x</p>' ),
            'form'          => array( '<form action="https://evil.example"><input name=a><button formaction=javascript:alert(1)>x</button></form>' ),
            'base'          => array( '<base href="https://evil.example/">' ),
            'meta refresh'  => array( '<meta http-equiv="refresh" content="0;url=javascript:alert(1)">' ),
            'link import'   => array( '<link rel=import href="https://evil.example/x.html">' ),
            'a javascript'  => array( '<a href="javascript:alert(1)">x</a>' ),
            'a entity'      => array( '<a href="&#106;avascript:alert(1)">x</a>' ),
            'details'       => array( '<details open ontoggle=alert(1)><summary>x</summary></details>' ),
            'math'          => array( '<math><mtext><table><mglyph><style><img src=x onerror=alert(1)>' ),
            'comment'       => array( '<!--><img src=x onerror=alert(1)>-->' ),
            'cdata'         => array( '<![CDATA[<script>alert(1)</script>]]>' ),
            'inline'        => array( 'Text <b onclick=alert(1)>bold</b> and <span onmouseover="alert(1)">x</span>' ),
            'table attr'    => array( "| a | b |\n|---|---|\n| <img src=x onerror=alert(1)> | <script>x</script> |" ),
            'in list'       => array( "- <iframe src=x></iframe>\n- <img src=x onerror=alert(1)>" ),
            'in quote'      => array( "> <script>alert(1)</script>\n> **Note** <img src=x onerror=alert(1)>" ),
            'template'      => array( '<template><img src=x onerror=alert(1)></template>' ),
            'noscript'      => array( '<noscript><p title="</noscript><img src=x onerror=alert(1)>">' ),
            'textarea'      => array( '<textarea><img src=x onerror=alert(1)></textarea>' ),
        );
    }

    /** HR-01 */
    #[PHPUnit\Framework\Attributes\DataProvider( 'rawHtml' )]
    public function testRawHtmlIsHarmless( $markdown )
    {
        $r = $this->html( "# Title\n\n" . $markdown . "\n\nAfter." );
        $this->assertStringContainsString( 'After.', $r['html'], 'the rest of the page still renders' );
    }

    public static function badLinks()
    {
        return array(
            array( '[x](javascript:alert(1))' ),
            array( '[x](JAVASCRIPT:alert(1))' ),
            array( '[x]( javascript:alert(1) )' ),
            array( '[x](&#106;avascript:alert(1))' ),
            array( '[x](&#x6A;avascript&#x3A;alert(1))' ),
            array( '[x](java%73cript:alert(1))' ),
            array( "[x](<javascript:alert(1)>)" ),
            array( '[x](data:text/html;base64,PHNjcmlwdD5hbGVydCgxKTwvc2NyaXB0Pg==)' ),
            array( '[x](vbscript:msgbox(1))' ),
            array( '[x](file:///etc/passwd)' ),
            array( '<javascript:alert(1)>' ),
            array( "[x][ref]\n\n[ref]: javascript:alert(1)" ),
            array( "[x][ref]\n\n[ref]: <data:text/html,x>" ),
            array( '[x](//evil.example/x)' ),
        );
    }

    /** HR-02 */
    #[PHPUnit\Framework\Attributes\DataProvider( 'badLinks' )]
    public function testBadLinksDropped( $markdown )
    {
        $r = $this->html( $markdown );
        foreach ( self::elements( $r['html'], 'a' ) as $a )
        {
            $href = (string)$a->getAttribute( 'href' );
            $this->assertDoesNotMatchRegularExpression( '#^\s*(javascript|data|vbscript|file):|^//#i', html_entity_decode( rawurldecode( $href ) ), $markdown );
        }
    }

    public function testExternalLinks()
    {
        $r = $this->html( '[GitHub](https://github.com/se7enxweb/exponential "Issues") and <https://example.com/a?b=1&c=2>' );
        $links = self::elements( $r['html'], 'a' );
        $this->assertCount( 2, $links );
        foreach ( $links as $a )
        {
            $this->assertMatchesRegularExpression( '#^https://#', $a->getAttribute( 'href' ) );
            $this->assertStringContainsString( 'noopener', $a->getAttribute( 'rel' ), 'external links get rel=noopener' );
        }
        $this->assertSame( 'https://example.com/a?b=1&c=2', $links[1]->getAttribute( 'href' ) );
    }

    public function testAttributeBreakout()
    {
        foreach ( array( '[x](https://example.com/"onmouseover="alert(1))', "[x](https://example.com/'onmouseover='alert(1))",
                         '[x](https://example.com/ "t\" onmouseover=\"alert(1)")', '![a" onerror="alert(1)](https://example.com/a.png)',
                         '[x](https://example.com/><script>alert(1)</script>)' ) as $markdown )
        {
            $this->html( $markdown );
        }
    }

    /** HR-03 */
    public function testRelativeLinksThroughResolver()
    {
        $r = $this->html( "[ok](ok.md) [sub](sub/ok.md#part) [out](../../settings/site.ini) [no](missing.md) [top](#local)", 'help/page.md' );
        $hrefs = array_map( static function ( $a ) { return $a->hasAttribute( 'href' ) ? $a->getAttribute( 'href' ) : null; },
                            self::elements( $r['html'], 'a' ) );
        $this->assertContains( '/admin/exphelp/viewer/help/ok.md', $hrefs );
        $this->assertNotEmpty( array_filter( $hrefs, static function ( $h ) { return $h !== null && strpos( $h, '/admin/exphelp/viewer/help/sub/ok.md' ) === 0; } ) );
        foreach ( $hrefs as $h )
        {
            $this->assertStringNotContainsString( 'settings', (string)$h, 'a refused link loses its href' );
            $this->assertStringNotContainsString( 'missing.md', (string)$h );
        }
        $this->assertStringContainsString( 'out', strip_tags( $r['html'] ), 'the text of a refused link stays' );
        $this->assertNotEmpty( array_filter( $this->calls, static function ( $c ) { return $c[0] === 'link'; } ), 'the link resolver was used' );
        foreach ( $this->calls as $c )
            $this->assertSame( 'help/page.md', $c[2], 'the resolver gets the current page id' );
    }

    /** HR-04 */
    public function testImages()
    {
        $r = $this->html( "![ok](img/ok.png) ![ext](https://example.com/a.png) ![http](http://example.com/a.png) "
                        . "![data](data:image/svg+xml;base64,PHN2ZyBvbmxvYWQ9YWxlcnQoMSk+) ![js](javascript:alert(1)) "
                        . "![out](../../settings/site.ini) ![proto](//example.com/a.png)", 'help/page.md' );
        $srcs = array_map( static function ( $i ) { return $i->getAttribute( 'src' ); }, self::elements( $r['html'], 'img' ) );
        $this->assertContains( '/admin/exphelp/asset/help/img/ok.png', $srcs );
        $this->assertContains( 'https://example.com/a.png', $srcs );
        foreach ( $srcs as $src )
            $this->assertMatchesRegularExpression( '#^(https://|/admin/exphelp/asset/)#', $src );
        foreach ( self::elements( $r['html'], 'img' ) as $img )
            $this->assertTrue( $img->hasAttribute( 'alt' ), 'images keep their alt text' );
    }

    /** HR-05 */
    public function testCode()
    {
        $md = "```php\n<?php echo '</code></pre><script>alert(1)</script>'; ?>\n```\n\n"
            . "```\"><script>alert(1)</script>\nplain\n```\n\n"
            . "`<img src=x onerror=alert(1)>` inline\n\n"
            . "    <script>indented()</script>\n";
        $r = $this->html( $md );
        $pres = self::elements( $r['html'], 'pre' );
        $this->assertGreaterThanOrEqual( 2, count( $pres ) );
        $this->assertStringContainsString( "</code></pre><script>alert(1)</script>", $pres[0]->textContent, 'the code is shown as text, unchanged' );
        $codes = self::elements( $r['html'], 'code' );
        $classes = implode( ' ', array_map( static function ( $c ) { return $c->getAttribute( 'class' ); }, $codes ) );
        $this->assertMatchesRegularExpression( '/\blanguage-php\b/', $classes, 'the language class of a fence' );
        $this->assertStringNotContainsString( 'script', $classes );
        $this->assertStringContainsString( '<img src=x onerror=alert(1)>', html_entity_decode( strip_tags( $r['html'] ) ), 'inline code as text' );
    }

    public function testHighlighter()
    {
        $samples = array( 'php' => "<?php \$a = \"</span><script>alert(1)</script>\"; // <b>x</b>\nfunction f() { return 1; }",
                          'js' => "const a = '<img src=x onerror=alert(1)>'; /* </span> */ alert(`\${a}`);",
                          'html' => '<a href="javascript:alert(1)" onclick="x()">x</a><script>alert(1)</script>',
                          'ini' => "[Group]\nKey=<script>\n# comment",
                          'bash' => "echo \"<script>\" > /dev/null # '",
                          'sql' => "SELECT '<script>' FROM t -- </span>",
                          'json' => '{"a": "<\/script>", "b": 1}',
                          'unknown-lang' => '<script>alert(1)</script>',
                          '' => '<script>alert(1)</script>' );
        foreach ( $samples as $lang => $code )
        {
            $html = expHelpHighlighter::highlight( $code, $lang );
            $this->assertSame( $code, html_entity_decode( strip_tags( $html ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ), 'the text is unchanged: ' . $lang );
            foreach ( self::dom( $html )->getElementsByTagName( '*' ) as $el )
            {
                if ( in_array( $el->nodeName, array( 'html', 'body' ), true ) )
                    continue;
                $this->assertSame( 'span', $el->nodeName, $lang );
                $this->assertSame( 1, $el->attributes->length, 'class only: ' . $lang );
                $this->assertMatchesRegularExpression( '/^a4-help-tok-[a-z-]+$/', $el->getAttribute( 'class' ), $lang );
            }
        }
        $this->assertSame( '', expHelpHighlighter::highlight( '', 'php' ) );
    }

    /** HR-06 */
    public function testHeadingsAndToc()
    {
        $md = "# The <script>alert(1)</script> title\n\n## Install\n\ntext\n\n## Install\n\n### \"><img src=x onerror=alert(1)>\n\n## Ünïcode & more\n";
        $r = $this->html( $md );
        // plain text (the templates wash it): the heading's source text, never the renderer's own markup or entities
        $this->assertDoesNotMatchRegularExpression( '/<(strong|em|code|a|span)\b|&lt;|&amp;lt;/', $r['title'], 'the title is plain text' );
        $this->assertStringContainsString( 'title', $r['title'] );
        $ids = array();
        foreach ( array( 'h1', 'h2', 'h3' ) as $h )
            foreach ( self::elements( $r['html'], $h ) as $el )
                if ( $el->hasAttribute( 'id' ) )
                    $ids[] = $el->getAttribute( 'id' );
        $this->assertSame( $ids, array_unique( $ids ), 'anchors are unique' );
        $this->assertGreaterThanOrEqual( 4, count( $ids ) );
        $this->assertNotEmpty( $r['toc'] );
        $anchors = array();
        foreach ( $r['toc'] as $entry )
        {
            $this->assertArrayHasKey( 'level', $entry );
            $this->assertArrayHasKey( 'text', $entry );
            $this->assertArrayHasKey( 'anchor', $entry );
            $this->assertDoesNotMatchRegularExpression( '/<(strong|em|code|a|span)\b|&lt;|&amp;lt;/', $entry['text'], 'TOC text is plain' );
            $this->assertMatchesRegularExpression( '/^[\p{L}\p{N}\p{M}_.:-]+$/u', $entry['anchor'] );
            $this->assertContains( $entry['anchor'], $ids, 'each TOC entry has its heading' );
            $anchors[] = $entry['anchor'];
        }
        $this->assertSame( $anchors, array_unique( $anchors ) );
    }

    /** HR-07 */
    public function testMarkdownSubset()
    {
        $md = "# Page\n\nSome *em*, **strong**, `code`, ~~gone~~.\n\n- one\n- two\n  - nested\n\n1. first\n2. second\n\n"
            . "| Name | Value |\n|:-----|------:|\n| a    | 1     |\n| b \\| c | 2 |\n\n"
            . "> **Note**\n> Remember this.\n\n> **Warning**\n> Careful.\n\n:::tip\nA tip.\n:::\n\n---\n\nEnd";
        $r = $this->html( $md );
        $html = $r['html'];
        $this->assertCount( 1, self::elements( $html, 'table' ) );
        $this->assertCount( 3, self::elements( $html, 'tr' ) );
        $this->assertStringContainsString( 'b | c', self::elements( $html, 'table' )[0]->textContent );
        $this->assertNotEmpty( self::elements( $html, 'strong' ) );
        $this->assertNotEmpty( self::elements( $html, 'em' ) );
        $this->assertGreaterThanOrEqual( 2, count( self::elements( $html, 'ul' ) ) );
        $this->assertCount( 1, self::elements( $html, 'ol' ) );
        $this->assertMatchesRegularExpression( '/a4-help-callout/', $html, 'admonitions become callouts' );
        $this->assertMatchesRegularExpression( '/a4-help-callout[^"]*\b(note|warning|tip)\b|data-callout="(note|warning|tip)"/', $html );
        $this->assertSame( 'Page', $r['title'] );
    }

    /** The notice levels of the help style guide (section 5): Attention, Data protection and Note are callouts */
    public function testNoticeLevelsOfTheStyleGuideAreCallouts()
    {
        $html = $this->html( "> **Attention:** The objects are deleted at once.\n\n> **Data protection:** The section becomes public.\n\n"
                             . ":::data-protection\nExternal media load from a third party.\n:::\n\n> **Note:** A draft is kept." )['html'];
        $this->assertSame( 4, substr_count( $html, 'class="a4-help-callout ' ) );
        $this->assertSame( 3, substr_count( $html, 'a4-help-callout--important' ) );
        $this->assertSame( 1, substr_count( $html, 'a4-help-callout--note' ) );
        $this->assertSame( 2, substr_count( $html, 'a4-bi-person-lock' ), 'data protection has its own icon' );
        $this->assertStringContainsString( 'Data protection</p>', $html, 'the container keeps the level as its title' );
        $this->assertStringNotContainsString( 'Important</p>', $html );
    }

    /** HR-08 */
    public function testHelpers()
    {
        foreach ( array( 'https://example.com', 'http://example.com/x', 'mailto:a@example.com', '/admin/exphelp/viewer/x.md', '#a', 'x.md' ) as $u )
            $this->assertTrue( expHelpMarkdownRenderer::isSafeUrl( $u ), $u );
        foreach ( array( 'javascript:alert(1)', ' JavaScript:alert(1)', "java\tscript:alert(1)", '&#106;avascript:alert(1)', 'data:text/html,x',
                         'vbscript:x', 'file:///etc/passwd', '' ) as $u )
            $this->assertFalse( expHelpMarkdownRenderer::isSafeUrl( $u ), $u );
        $this->assertSame( 'hello-world', expHelpMarkdownRenderer::slug( 'Hello, World!' ) );
        $this->assertMatchesRegularExpression( '/^[a-z0-9-]*$/', expHelpMarkdownRenderer::slug( '"><script>alert(1)</script>' ) );
        $plain = $this->renderer()->renderPlainText( "Line <script>alert(1)</script>\nhttps://example.com\njavascript:alert(1)", 'help/x.txt' );
        $this->assertSafeHtml( $plain['html'] );
        $this->assertStringContainsString( 'alert(1)', html_entity_decode( strip_tags( $plain['html'] ) ), 'shown as text' );
    }

    public static function pathological()
    {
        return array(
            'brackets'     => array( str_repeat( '[', 5000 ) ),
            'emphasis'     => array( str_repeat( '*a ', 20000 ) ),
            'deep quotes'  => array( str_repeat( '>', 500 ) . ' x' ),
            'deep lists'   => array( implode( "\n", array_map( static function ( $i ) { return str_repeat( '  ', $i ) . '- x'; }, range( 0, 300 ) ) ) ),
            'backticks'    => array( str_repeat( '`', 5000 ) . 'x' ),
            'links'        => array( str_repeat( '[a](', 3000 ) ),
            'html'         => array( str_repeat( '<div>', 5000 ) ),
            'table'        => array( "| a |\n|---|\n" . str_repeat( "| x |\n", 5000 ) ),
            'placeholder'  => array( "\x1A0\x1A \x1Ahtml0\x1A <script>alert(1)</script> \0 \x1A" ),
            'split js'     => array( "[x](java\tscript:alert(1)) [y](java\nscript:alert(1)) [z](java%0ascript:alert(1)) [w](&Tab;javascript:alert(1))" ),
            'title break'  => array( '[x](https://example.com "a\\" onmouseover=\\"alert(1)") ![i](https://example.com/a.png "\'><script>")' ),
        );
    }

    /** HR-10 hostile and huge inputs: safe and fast (< 2 s each) */
    #[PHPUnit\Framework\Attributes\DataProvider( 'pathological' )]
    public function testPathologicalInputs( $markdown )
    {
        $start = microtime( true );
        $r = $this->renderer()->render( $markdown, 'help/page.md' );
        $took = microtime( true ) - $start;
        $this->assertLessThan( 2.0, $took, sprintf( 'rendering took %.2f s', $took ) );
        $this->assertSafeHtml( $r['html'], substr( $markdown, 0, 60 ) );
        $this->assertStringNotContainsString( "\x1A", $r['html'], 'no internal placeholder leaks' );
    }

    /** HR-09 */
    public function testEveryDocPageRendersSafely()
    {
        $root = dirname( __DIR__, 5 ) . '/doc';
        $files = array();
        $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $root, FilesystemIterator::SKIP_DOTS ) );
        foreach ( $it as $file )
        {
            $path = $file->getPathname();
            if ( strpos( $path, '/doxygen/' ) !== false || !preg_match( '/\.(md|markdown)$/i', $path ) || $file->isLink() )
                continue;
            if ( $file->getSize() > 512 * 1024 )
                continue;
            $files[] = $path;
        }
        sort( $files );
        // bounded: every file when there are few, a spread otherwise (CI time)
        $step = max( 1, (int)ceil( count( $files ) / 300 ) );
        $renderer = $this->renderer();
        $n = 0;
        for ( $i = 0; $i < count( $files ); $i += $step )
        {
            $id = substr( $files[$i], strlen( $root ) + 1 );
            $r = $renderer->render( (string)file_get_contents( $files[$i] ), $id );
            $this->assertSafeHtml( $r['html'], $id );
            $n++;
        }
        $this->assertGreaterThan( 10, $n );
    }
}
