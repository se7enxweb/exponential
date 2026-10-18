<?php
/**
 * E-books of help maintained as content (doc/features/6.0/help-content.md, "E-books"), without a database: the
 * pieces of the EPUB writer that decide what goes into the book.
 *
 *  HCE-01  headings move below the topic's title, never inside fenced code, at most to h6
 *  HCE-02  the renderer's HTML becomes well-formed XHTML; copy buttons, scripts and heading anchors are left out
 *  HCE-03  a content document is well-formed XHTML with xml:lang
 *  HCE-04  topic anchors and language tags
 *  HCE-05  only storage files are put into a book: no path outside var/.../storage, no traversal
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expHelpContentExportTest extends PHPUnit\Framework\TestCase
{
    /** HCE-01 */
    public function testDemoteHeadings()
    {
        $md = "Intro\n\n## Steps\n\n1. Do.\n\n```\n## not a heading\n```\n\n###### deep";
        $this->assertSame( "Intro\n\n### Steps\n\n1. Do.\n\n```\n## not a heading\n```\n\n###### deep", expHelpContentExport::demoteHeadings( $md, 1 ) );
        $this->assertSame( $md, expHelpContentExport::demoteHeadings( $md, 0 ) );
        $this->assertSame( '###### Steps', expHelpContentExport::demoteHeadings( '## Steps', 9 ) );
    }

    /** HCE-02 */
    public function testXhtmlFragment()
    {
        $html = '<h3 id="a">Steps <a class="a4-help-anchor" href="#a" aria-hidden="true">#</a></h3><p>One<br>two &amp; &nbsp;three</p>'
              . '<img src="x.png" alt="An edit form"><button type="button" class="a4-help-copy">Copy</button><script>alert(1)</script>'
              . '<table><tr><td>a</td></tr></table>';
        $xhtml = expHelpContentExport::xhtmlFragment( $html );
        $doc = new DOMDocument();
        $this->assertTrue( $doc->loadXML( '<div xmlns="http://www.w3.org/1999/xhtml">' . $xhtml . '</div>' ), $xhtml );
        $this->assertStringContainsString( '<br/>', $xhtml );
        $this->assertStringContainsString( 'alt="An edit form"', $xhtml );
        $this->assertStringNotContainsString( 'button', $xhtml );
        $this->assertStringNotContainsString( 'script', $xhtml );
        $this->assertStringNotContainsString( 'a4-help-anchor', $xhtml );
        $this->assertSame( '', expHelpContentExport::xhtmlFragment( '' ) );
    }

    /** HCE-03 */
    public function testXhtmlDocument()
    {
        $page = expHelpContentExport::xhtml( 'en-US', 'A <b> & "c"', '<p>' . expHelpContentExport::xhtmlFragment( '<p>x</p>' ) . '</p>' );
        $doc = new DOMDocument();
        $this->assertTrue( $doc->loadXML( $page ) );
        $this->assertSame( 'en-US', $doc->documentElement->getAttribute( 'xml:lang' ) );
        $this->assertSame( 'A <b> & "c"', $doc->getElementsByTagName( 'title' )->item( 0 )->textContent );
    }

    /** HCE-04 */
    public function testAnchorsAndLanguage()
    {
        $this->assertSame( 't-content-publish-article', expHelpContentExport::topicAnchor( 'content.publish-article' ) );
        $this->assertSame( 'en', expHelpContentExport::xmlLang( '' ) );
        $this->assertSame( 'de-DE', expHelpContentExport::xmlLang( 'de_de' ) );
    }

    /** HCE-05 */
    public function testStorageFile()
    {
        foreach ( array( '', '/etc/passwd', 'var/../settings/override/site.ini.append.php', '/var/site/cache/x.png',
                         'https://example.org/var/site/storage/x.png/../../../../etc/passwd', '/var/site/storage/../../../etc/passwd' ) as $url )
            $this->assertNull( expHelpContentExport::storageFile( $url ), $url );
    }
}

?>
