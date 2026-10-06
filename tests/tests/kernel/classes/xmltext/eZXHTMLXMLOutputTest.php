<?php
/**
 * The XHTML output handler of the XML text datatype without its templates: which tag template is used with which
 * variables and design keys, how paragraphs are broken by block tags, lines, headers numbered for the table of
 * contents, tables with their row and column counts, links to URLs, anchors, objects and nodes, script URLs that
 * are never given to the page, custom tags, classes not allowed, text with spaces and entities, literal text,
 * comments and CDATA in stored XML, and broken stored XML.
 *
 * Each tag template is replaced by a marker that shows the template name and its variables
 * ("[paragraph classification=]...[/paragraph]"), so the result does not depend on the designs of the
 * installation. No database: the URLs of links are given to the handler instead of being read.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group xmltext
 */

require_once __DIR__ . '/eZXMLTextTestFixtures.php';

class eZXMLTextTestXHTMLOutput extends eZXHTMLXMLOutput
{
    public $testLinks = array();
    public $designKeysSeen = array();

    function prefetch()
    {
        $this->LinkArray = $this->testLinks;
    }

    function renderTag( $element, $content, $vars )
    {
        $currentTag = $this->OutputTags[$element->nodeName] ?? null;
        if ( $currentTag && isset( $currentTag['quickRender'] ) )
            return parent::renderTag( $element, $content, $vars );
        $name = basename( $this->TemplateUri, '.tpl' );
        $shown = array();
        foreach ( array( 'href', 'level', 'header_number', 'toc_anchor_name', 'row_count', 'col_count', 'classification', 'colspan', 'title', 'target', 'name', 'align', 'author' ) as $var )
        {
            $value = $this->Tpl->variable( $var, 'xmltagns' );
            if ( $this->Tpl->hasVariable( $var, 'xmltagns' ) && $value !== '' && $value !== null )
                $shown[] = $var . '=' . ( is_scalar( $value ) ? $value : gettype( $value ) );
        }
        $keys = $this->Res->keys();
        foreach ( array( 'classification', 'table_classification', 'attribute_identifier' ) as $key )
        {
            if ( isset( $keys[$key] ) && $keys[$key] !== '' )
                $this->designKeysSeen[] = "$name:$key=" . $keys[$key];
        }
        return '[' . $name . ( $shown ? ' ' . implode( ' ', $shown ) : '' ) . ']' . $content . '[/' . $name . ']';
    }
}

class eZXHTMLXMLOutputTest extends eZXMLTextTestCase
{
    const ROOT = '<?xml version="1.0" encoding="utf-8"?><section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/">';

    private function render( $body, array $links = array(), $attribute = null, $setup = null )
    {
        $output = new eZXMLTextTestXHTMLOutput( self::ROOT . $body . '</section>', false, $attribute );
        $output->testLinks = $links;
        $output->AllowMultipleSpaces = false;
        $output->AllowNumericEntities = false;
        $output->RenderParagraphInTableCells = true;
        if ( $setup )
            $setup( $output );
        return $output->outputText();
    }

    private function renderInput( $input )
    {
        $document = $this->parser( 5, true )->process( $input );
        $xml = eZXMLTextType::domString( $document );
        $output = new eZXMLTextTestXHTMLOutput( $xml, false );
        return $output->outputText();
    }

    public static function renderProvider()
    {
        return array(
            'paragraph' => array( '<paragraph>Hello</paragraph>', '[paragraph]Hello[/paragraph]' ),
            'inline tags' => array( '<paragraph>a <strong>b</strong> <emphasize>c</emphasize></paragraph>', '[paragraph]a [strong]b[/strong] [emphasize]c[/emphasize][/paragraph]' ),
            'lines' => array( '<paragraph><line>one</line><line>two</line></paragraph>', '[paragraph][line]one[/line]two[/paragraph]' ),
            'paragraph broken by a list' => array( '<paragraph>before<ul><li><paragraph>i</paragraph></li></ul>after</paragraph>',
                                                   '[paragraph]before[/paragraph][ul][li]i[/li][/ul][paragraph]after[/paragraph]' ),
            'list items keep their paragraphs when they have two' => array( '<paragraph><ol><li><paragraph>a</paragraph><paragraph>b</paragraph></li></ol></paragraph>',
                                                                            '[ol][li][paragraph]a[/paragraph][paragraph]b[/paragraph][/li][/ol]' ),
            'headers are numbered' => array( '<section><header>A</header><section><header>A1</header></section><section><header>A2</header></section></section><section><header>B</header></section>',
                                             '[header level=1 header_number=1 toc_anchor_name=1]A[/header][header level=2 header_number=1.1 toc_anchor_name=1_1]A1[/header][header level=2 header_number=1.2 toc_anchor_name=1_2]A2[/header][header level=1 header_number=2 toc_anchor_name=2]B[/header]' ),
            'table' => array( '<paragraph><table class="list"><tr><th>h</th><td xhtml:colspan="2">c</td></tr><tr><td><paragraph>d</paragraph></td></tr></table></paragraph>',
                              '[table row_count=1 col_count=0 classification=list][tr row_count=0 col_count=1][th row_count=0 col_count=0]h[/th][td row_count=0 col_count=1 colspan=2]c[/td][/tr][tr row_count=1 col_count=0][td row_count=1 col_count=0][paragraph]d[/paragraph][/td][/tr][/table]' ),
            'headers in a table start again at level 1' => array( '<section><header>A</header><paragraph><table><tr><td><section><header>In cell</header></section></td></tr></table></paragraph></section>',
                                                                 '[header level=1 header_number=1 toc_anchor_name=1]A[/header][table row_count=0 col_count=0][tr row_count=0 col_count=0][td row_count=0 col_count=0][header level=1 header_number=2 toc_anchor_name=2]In cell[/header][/td][/tr][/table]' ),
            'literal' => array( '<paragraph><literal class="html">a &lt;b&gt;  c</literal></paragraph>', '[literal classification=html]a <b>  c[/literal]' ),
            'anchor' => array( '<paragraph><anchor name="here"/>x</paragraph>', '[paragraph][anchor name=here][/anchor]x[/paragraph]' ),
            'anchor link' => array( '<paragraph><link anchor_name="here" target="_blank" xhtml:title="t">go</link></paragraph>', '[paragraph][link href=#here title=t target=_blank]go[/link][/paragraph]' ),
            'default link target' => array( '<paragraph><link anchor_name="x">go</link></paragraph>', '[paragraph][link href=#x target=_self]go[/link][/paragraph]' ),
            'link with href' => array( '<paragraph><link href="https://k1d.example.invalid/">go</link></paragraph>', '[paragraph][link href=https://k1d.example.invalid/ target=_self]go[/link][/paragraph]' ),
            'inline custom tag' => array( '<paragraph>a <custom name="strike">s</custom></paragraph>', '[paragraph]a [strike]s[/strike][/paragraph]' ),
            'block custom tag' => array( '<paragraph><custom name="quote" custom:author="Ada"><paragraph>Q</paragraph></custom></paragraph>', '[quote author=Ada][paragraph]Q[/paragraph][/quote]' ),
            'custom tag with a path as name' => array( '<paragraph><custom name="../../secret">x</custom></paragraph>', '[paragraph]x[/paragraph]' ),
            'custom tag without a name' => array( '<paragraph><custom>x</custom></paragraph>', '[paragraph]x[/paragraph]' ),
            'class not allowed' => array( '<paragraph class="evil">x</paragraph><paragraph class="lead">y</paragraph>', '[paragraph classification=lead]y[/paragraph]' ),
            'text is escaped and spaces folded' => array( '<paragraph>a &lt;b&gt; &amp;   "c"</paragraph>', '[paragraph]a &lt;b&gt; &amp; &quot;c&quot;[/paragraph]' ),
            'line breaks in the stored text are dropped' => array( "<paragraph>a\nb</paragraph>", '[paragraph]ab[/paragraph]' ),
            'non-breaking space' => array( "<paragraph>a\u{00A0}b &amp;nbsp; c</paragraph>", '[paragraph]a&nbsp;b &nbsp; c[/paragraph]' ),
            'comment' => array( '<paragraph>a<!-- secret -->b</paragraph>', '[paragraph]ab[/paragraph]' ),
            'cdata' => array( '<paragraph><![CDATA[<i>x</i>]]></paragraph>', '[paragraph]&lt;i&gt;x&lt;/i&gt;[/paragraph]' ),
            'cdata in literal' => array( '<paragraph><literal><![CDATA[<i>x</i>]]></literal></paragraph>', '[literal]<i>x</i>[/literal]' ),
            'empty section' => array( '', '' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('renderProvider')]
    public function testRender( $body, $expected )
    {
        $this->assertSame( $expected, $this->render( $body ) );
    }

    public function testExternalLinkUsesTheUrlTable()
    {
        $this->assertSame( '[paragraph][link href=https://k1d.example.invalid/?a=1&amp;b=2#sec target=_self]go[/link][/paragraph]',
                           $this->render( '<paragraph><link url_id="77" anchor_name="sec">go</link></paragraph>', array( 77 => 'https://k1d.example.invalid/?a=1&amp;b=2' ) ) );
        $this->assertSame( '[paragraph][link target=_self]gone[/link][/paragraph]',
                           $this->render( '<paragraph><link url_id="78">gone</link></paragraph>' ) );
    }

    public static function scriptURLProvider()
    {
        return array( array( 'javascript:alert(1)' ), array( 'JAVASCRIPT:x' ), array( " \tjava\nscript:x" ), array( 'jav&#x61;script:x' ),
                      array( 'vbscript:x' ), array( 'livescript:x' ), array( 'data:text/html,x' ), array( '&amp;#106;avascript:x' ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('scriptURLProvider')]
    public function testScriptURLsAreNeverRendered( $url )
    {
        $this->assertTrue( eZXMLOutputHandler::isUnsafeURL( $url ) );
        $this->assertSame( '[paragraph][link href=# target=_self]x[/link][/paragraph]', $this->render( '<paragraph><link url_id="5">x</link></paragraph>', array( 5 => $url ) ) );
    }

    public function testSafeURLs()
    {
        foreach ( array( 'https://k1d.example.invalid/', '/content/view/full/2', 'mailto:a@k1d.example.invalid', '#top', 'javascript-guide.html', 'ftp://k1d.example.invalid' ) as $url )
            $this->assertFalse( eZXMLOutputHandler::isUnsafeURL( $url ), $url );
    }

    public function testLinksAndEmbedsOfMissingObjectsAndNodes()
    {
        // no objects or nodes were found: links get no address, embeds are not rendered at all
        $setup = function ( $output ) { $output->ObjectArray = array(); $output->NodeArray = array(); };
        $this->assertSame( '[paragraph][link target=_self]o[/link][link target=_self]n[/link][/paragraph]',
                           $this->render( '<paragraph><link object_id="999">o</link><link node_id="998">n</link></paragraph>', array(), null, $setup ) );
    }

    public function testParagraphsInTableCellsCanBeLeftOut()
    {
        $html = $this->render( '<paragraph><table><tr><td><paragraph align="right">c</paragraph></td><td><paragraph>a</paragraph><paragraph>b</paragraph></td></tr></table></paragraph>', array(), null,
                               function ( $output ) { $output->RenderParagraphInTableCells = false; } );
        $this->assertSame( '[table row_count=0 col_count=1][tr row_count=0 col_count=1][td row_count=0 col_count=0 align=right]c[/td][td row_count=0 col_count=1][paragraph]a[/paragraph][paragraph]b[/paragraph][/td][/tr][/table]', $html );
    }

    public function testMultipleSpacesAndNumericEntitiesWhenAllowed()
    {
        $html = $this->render( '<paragraph>a   b &amp;#8364;</paragraph>', array(), null, function ( $output ) {
            $output->AllowMultipleSpaces = true;
            $output->AllowNumericEntities = true;
        } );
        $this->assertSame( '[paragraph]a &nbsp; b &#8364;[/paragraph]', $html );
    }

    public function testDesignKeysOfClassesAndTheAttribute()
    {
        $classAttribute = $this->classAttribute( 'ezxmltext', array( 'identifier' => 'body' ) );
        $attribute = $this->objectAttribute( 'ezxmltext', $classAttribute );
        $output = new eZXMLTextTestXHTMLOutput( self::ROOT . '<paragraph class="lead">x</paragraph><paragraph><table class="cols"><tr><td>c</td></tr></table></paragraph></section>', false, $attribute );
        $output->outputText();
        $this->assertContains( 'paragraph:classification=lead', $output->designKeysSeen );
        $this->assertContains( 'paragraph:attribute_identifier=body', $output->designKeysSeen );
        $this->assertContains( 'td:table_classification=cols', $output->designKeysSeen );
        $this->assertContains( 'tr:table_classification=cols', $output->designKeysSeen );
        // the keys do not leak into the rest of the page
        $keys = eZTemplateDesignResource::instance()->keys();
        $this->assertArrayNotHasKey( 'attribute_identifier', $keys );
        $this->assertArrayNotHasKey( 'classification', $keys );
        $this->assertArrayNotHasKey( 'table_classification', $keys );
    }

    public function testHeaderAnchorsCarryTheAttributeId()
    {
        $attribute = $this->objectAttribute( 'ezxmltext' );
        $output = new eZXMLTextTestXHTMLOutput( self::ROOT . '<section><header>A</header></section></section>', false, $attribute );
        $this->assertSame( '[header level=1 header_number=1 toc_anchor_name=4711_1]A[/header]', $output->outputText() );
    }

    public function testNothingOrBrokenStoredXml()
    {
        foreach ( array( '', null, false, '<section><paragraph>broken', 'not xml' ) as $xml )
        {
            $output = new eZXMLTextTestXHTMLOutput( $xml, false );
            $this->assertSame( '', $output->outputText(), var_export( $xml, true ) );
        }
    }

    public function testForeignElementsAreRenderedByTheirName()
    {
        // a block of its own: the paragraph around it has no inline content left to render
        $this->assertSame( '[unknown]x[/unknown]', $this->render( '<paragraph><unknown>x</unknown></paragraph>' ) );
    }

    public function testHeaderOutsideASection()
    {
        $output = new eZXMLTextTestXHTMLOutput( '<?xml version="1.0"?><header>A</header>', false );
        $this->assertSame( '[header level=0]A[/header]', $output->outputText() );
    }

    public function testParsedInputRendersAsExpected()
    {
        $this->assertSame( '[paragraph][line]One[/line]Two [strong]bold[/strong][/paragraph][paragraph]Next[/paragraph]', $this->renderInput( "One\nTwo <b>bold</b>\n\nNext" ) );
    }

    public function testHandlerAttributes()
    {
        $output = new eZXHTMLXMLOutput( self::ROOT . '</section>', 'ezpdf' );
        $this->assertSame( array( 'output_text', 'aliased_type', 'aliased_handler', 'view_template_name' ), $output->attributes() );
        $this->assertTrue( $output->hasAttribute( 'output_text' ) );
        $this->assertFalse( $output->hasAttribute( 'nothing' ) );
        $this->assertSame( 'ezpdf', $output->attribute( 'aliased_type' ) );
        $this->assertSame( 'ezxmltext', $output->attribute( 'view_template_name' ) );
        $this->assertNull( @$output->attribute( 'nothing' ) );
        $this->assertTrue( $output->isValid() );
        $this->assertSame( self::ROOT . '</section>', $output->xmlData() );
        $this->assertSame( '', $output->attribute( 'output_text' ) );
        $this->assertInstanceOf( 'eZXMLInputHandler', $output->attribute( 'aliased_handler' ) );
    }
}
