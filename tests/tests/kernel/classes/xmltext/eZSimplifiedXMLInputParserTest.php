<?php
/**
 * The simplified XML input parser, which turns what an editor typed into the stored XML of the XML text datatype:
 * HTML names of tags, paragraphs and lines, headers and sections, lists, tables, literal text, links (anchors,
 * objects, external and script links), embedded objects, custom tags and their attributes, classes, schema errors,
 * entities and spaces, line breaks, and how the error levels stop or let through the input.
 *
 * No database: external links are counted instead of being registered (see eZXMLTextTestFixtures.php); links and
 * embeds by node (eznode://), which look the node up, are not used.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group xmltext
 */

require_once __DIR__ . '/eZXMLTextTestFixtures.php';

class eZSimplifiedXMLInputParserTest extends eZXMLTextTestCase
{
    public static function structureProvider()
    {
        return array(
            'plain text' => array( 'Hello world', '<paragraph>Hello world</paragraph>' ),
            'html names of inline tags' => array( '<b>bold</b> and <i>it</i>', '<paragraph><strong>bold</strong> and <emphasize>it</emphasize></paragraph>' ),
            'bold and em' => array( '<bold>a</bold><em>b</em>', '<paragraph><strong>a</strong><emphasize>b</emphasize></paragraph>' ),
            'nested inline' => array( '<emphasize>e<strong>s</strong></emphasize>', '<paragraph><emphasize>e<strong>s</strong></emphasize></paragraph>' ),
            'two paragraphs' => array( '<p>One</p><p>Two</p>', '<paragraph>One</paragraph><paragraph>Two</paragraph>' ),
            'para tag' => array( '<para>One</para>', '<paragraph>One</paragraph>' ),
            'unclosed paragraphs' => array( '<p>One<p>Two', '<paragraph>One</paragraph><paragraph>Two</paragraph>' ),
            'tag names in capitals' => array( '<P>One</P><B>b</B>', '<paragraph>One</paragraph><paragraph><strong>b</strong></paragraph>' ),
            'unclosed inline tag' => array( '<b>unclosed', '<paragraph><strong>unclosed</strong></paragraph>' ),
            'headers make sections' => array( '<h1>Title</h1><p>Text</p><h2>Sub</h2><p>More</p>',
                                              '<section><header>Title</header><paragraph>Text</paragraph><section><header>Sub</header><paragraph>More</paragraph></section></section>' ),
            'deep header' => array( '<h3>Deep</h3>', '<section><section><section><header>Deep</header></section></section></section>' ),
            'header levels going up' => array( '<header level="2">Two</header><header level="1">One</header>',
                                               '<section><section><header>Two</header></section></section><section><header>One</header></section>' ),
            'negative header level is level 1' => array( '<header level="-1">x</header>', '<section><header>x</header></section>' ),
            'every h tag' => array( '<h4>4</h4>', '<section><section><section><section><header>4</header></section></section></section></section>' ),
            'h1 then two h2' => array( '<h1>A</h1><h2>B</h2><h2>C</h2>', '<section><header>A</header><section><header>B</header></section><section><header>C</header></section></section>' ),
            'list' => array( '<ul><li>a</li><li>b</li></ul>', '<paragraph><ul><li><paragraph>a</paragraph></li><li><paragraph>b</paragraph></li></ul></paragraph>' ),
            'unclosed list items' => array( '<ol><li>a<li>b</ol>', '<paragraph><ol><li><paragraph>a</paragraph></li><li><paragraph>b</paragraph></li></ol></paragraph>' ),
            'nested list goes into the item before' => array( '<ul><li>a</li><ul><li>nested</li></ul></ul>',
                                                             '<paragraph><ul><li><paragraph>a<ul><li><paragraph>nested</paragraph></li></ul></paragraph></li></ul></paragraph>' ),
            'table' => array( '<table><tr><td>1</td><td colspan="2">2</td></tr></table>',
                              '<paragraph><table><tr><td><paragraph>1</paragraph></td><td xhtml:colspan="2"><paragraph>2</paragraph></td></tr></table></paragraph>' ),
            'table head cell width' => array( '<table><tr><th width="10">h</th></tr></table>', '<paragraph><table><tr><th xhtml:width="10"><paragraph>h</paragraph></th></tr></table></paragraph>' ),
            'cell rowspan' => array( '<table><tr><td rowspan="3">x</td></tr></table>', '<paragraph><table><tr><td xhtml:rowspan="3"><paragraph>x</paragraph></td></tr></table></paragraph>' ),
            'literal keeps markup as text' => array( '<literal>a <b>not bold</b> b</literal>', '<paragraph><literal>a &lt;b&gt;not bold&lt;/b&gt; b</literal></paragraph>' ),
            'literal in capitals' => array( '<literal>x</LITERAL>', '<paragraph><literal>x</literal></paragraph>' ),
            'anchor' => array( '<anchor name="here" />text', '<paragraph><anchor name="here"/>text</paragraph>' ),
            'line break' => array( 'a<br/>b<br/><br/>c', '<paragraph><line>a</line><line>b</line></paragraph><paragraph>c</paragraph>' ),
            'line break inside bold' => array( '<strong>a<br/>b</strong>', '<paragraph><line><strong>a</strong></line><line><strong>b</strong></line></paragraph>' ),
            'entities' => array( 'a &amp; b &lt;c&gt; &quot;d&quot; &#039;e&apos;', '<paragraph>a &amp; b &lt;c&gt; "d" \'e\'</paragraph>' ),
            'numeric entities' => array( 'x &#65;&#x42;&#8364;', "<paragraph>x AB\u{20AC}</paragraph>" ),
            'multiple spaces' => array( 'a    b     c', '<paragraph>a b c</paragraph>' ),
            'leading spaces trimmed' => array( '<p>   a</p>', '<paragraph>a</paragraph>' ),
            'tab is a space' => array( "a\tb", '<paragraph>a b</paragraph>' ),
            'line feed without parsing line breaks' => array( "a\nb", '<paragraph>ab</paragraph>' ),
            'greater-than in attribute' => array( '<custom name="factbox" title="a>b">x</custom>', '<paragraph><custom name="factbox" custom:title="a&gt;b"><paragraph>x</paragraph></custom></paragraph>' ),
            'single quoted attribute' => array( "<p align='center'>x</p>", '<paragraph align="center">x</paragraph>' ),
            'unquoted attribute' => array( '<p align=right>x</p>', '<paragraph align="right">x</paragraph>' ),
            'unquoted attributes in a table' => array( '<table><tr><td colspan=2 width=50%>x</td></tr></table>', '<paragraph><table><tr><td xhtml:colspan="2" xhtml:width="50%"><paragraph>x</paragraph></td></tr></table></paragraph>' ),
            'empty attribute value ignored' => array( '<p align="">x</p>', '<paragraph>x</paragraph>' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('structureProvider')]
    public function testStructure( $input, $expected )
    {
        list( $xml, $messages, $valid ) = $this->parse( $input );
        $this->assertSame( $expected, $xml );
        $this->assertSame( array(), $messages );
        $this->assertTrue( $valid );
    }

    public static function errorProvider()
    {
        return array(
            'unknown tag' => array( '<unknown>x</unknown>', '<paragraph>x</paragraph>', 'Unknown tag: &lt;unknown&gt;.' ),
            'h7' => array( '<h7>bad</h7>', '<paragraph>bad</paragraph>', 'Unknown tag: &lt;h7&gt;.' ),
            'stray closing tag' => array( 'text</b>', '<paragraph>text</paragraph>', 'Wrong closing tag : &lt;/b&gt;.' ),
            'unfinished closing tag' => array( 'a</b', '<paragraph>a/b</paragraph>', 'Wrong closing tag' ),
            'unfinished opening tag' => array( 'a<b', '<paragraph>ab</paragraph>', 'Wrong opening tag' ),
            'class not allowed' => array( '<p class="nonexistent">x</p>', '<paragraph>x</paragraph>', "Class 'nonexistent' is not allowed for element &lt;paragraph&gt; (check content.ini)." ),
            'attribute not allowed' => array( '<p title="x">x</p>', '<paragraph>x</paragraph>', "Attribute 'title' is not allowed in &lt;paragraph&gt; element." ),
            'namespaced attribute not allowed' => array( '<p xhtml:foo="1">x</p>', '<paragraph>x</paragraph>', "Attribute 'xhtml:foo' is not allowed in &lt;paragraph&gt; element." ),
            'empty paragraph' => array( '<p></p>', '', "&lt;paragraph&gt; tag can't be empty." ),
            'empty bold' => array( '<b></b>', '', "&lt;strong&gt; tag can't be empty." ),
            'empty table' => array( '<table></table>', '', "&lt;table&gt; tag can't be empty." ),
            'cell outside a table' => array( '<td>bare cell</td>', '<paragraph>bare cell</paragraph>', '&lt;td&gt; is not allowed to be a child of &lt;section&gt;.' ),
            'list item outside a list' => array( '<li>bare li</li>', '<paragraph>bare li</paragraph>', '&lt;li&gt; is not allowed to be a child of &lt;section&gt;.' ),
            'unknown custom tag' => array( '<custom name="nosuchtag">x</custom>', '', "Custom tag 'nosuchtag' is not allowed." ),
            'custom tag without a name' => array( '<custom>noname</custom>', '', "Custom tag '' is not allowed." ),
            'script link' => array( '<a href="javascript:alert(1)">x</a>', '<paragraph><link>x</link></paragraph>', "Using scripts in links is not allowed, link 'javascript:alert(1)' has been removed" ),
            'script link with spaces and capitals' => array( '<a href="  JavaScript:alert(1)">x</a>', '<paragraph><link>x</link></paragraph>', "Using scripts in links is not allowed, link 'JavaScript:alert(1)' has been removed" ),
            'data link' => array( '<a href="data:text/html;base64,PHNjcmlwdD4=">x</a>', '<paragraph><link>x</link></paragraph>', "Using scripts in links is not allowed, link 'data:text/html;base64,PHNjcmlwdD4=' has been removed" ),
            'self embed' => array( '<embed href="ezobject://5" />', '<paragraph><embed/></paragraph>', 'Object 5 can not be embeded to itself.' ),
            'literal class not allowed' => array( '<literal class="php">x</literal>', '<paragraph><literal>x</literal></paragraph>', "Class 'php' is not allowed for element &lt;literal&gt; (check content.ini)." ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('errorProvider')]
    public function testErrorsAreReportedAndTheRestIsKept( $input, $expected, $message )
    {
        list( $xml, $messages, $valid ) = $this->parse( $input );
        $this->assertSame( $expected, $xml );
        $this->assertContains( $message, $messages );
        $this->assertFalse( $valid );
    }

    public function testInvalidCharactersAreCountedInAMessage()
    {
        list( , $messages ) = $this->parse( "a\x01\x02b\x0Bc" );
        $this->assertSame( array( '2 invalid character(s) have been found and replaced by a space' ), $messages );
    }

    public function testClassesFromTheSettingsAreKept()
    {
        list( $xml, $messages ) = $this->parse( '<p class="lead">x</p><literal class="html">&lt;b&gt;</literal><table class="list"><tr><td>c</td></tr></table>' );
        $this->assertSame( '<paragraph class="lead">x</paragraph><paragraph><literal class="html">&amp;lt;b&amp;gt;</literal><table class="list"><tr><td><paragraph>c</paragraph></td></tr></table></paragraph>', $xml );
        $this->assertSame( array(), $messages );
    }

    public function testCustomTags()
    {
        list( $xml, $messages ) = $this->parse( '<custom name="factbox" title="Facts">Box</custom><custom name="strike">s</custom> and <custom name="quote" author="Ada">Q</custom>' );
        $this->assertSame( '<paragraph><custom name="factbox" custom:title="Facts"><paragraph>Box</paragraph></custom><line><custom name="strike">s</custom> and </line><custom name="quote" custom:author="Ada"><paragraph>Q</paragraph></custom></paragraph>', $xml );
        $this->assertSame( array(), $messages );
    }

    public function testCustomTagAttributesNotInTheSettingsAreRemoved()
    {
        list( $xml, $messages, $valid ) = $this->parse( '<custom name="strike" color="red">s</custom>' );
        $this->assertSame( '<paragraph><custom name="strike">s</custom></paragraph>', $xml );
        $this->assertSame( array( "Attribute 'custom:color' is not allowed in &lt;custom&gt; element." ), $messages );
        $this->assertFalse( $valid );
    }

    public function testCustomAttributeOfARegularTag()
    {
        list( $xml, $messages ) = $this->parse( '<p note="check">x</p><p custom:note="again">y</p>' );
        $this->assertSame( '<paragraph custom:note="check">x</paragraph><paragraph custom:note="again">y</paragraph>', $xml );
        $this->assertSame( array(), $messages );
    }

    public function testHeaderInsideABlockCustomTagStaysInIt()
    {
        list( $xml ) = $this->parse( '<header level="3">h3</header><custom name="factbox" title="f"><header level="2">h2</header></custom>' );
        $this->assertSame( '<section><section><section><header>h3</header></section><paragraph><custom name="factbox" custom:title="f"><header>h2</header></custom></paragraph></section></section>', $xml );
    }

    public static function linkProvider()
    {
        return array(
            'anchor in the page' => array( '<a href="#here">jump</a>', '<paragraph><link anchor_name="here">jump</link></paragraph>', array(), array() ),
            'object' => array( '<a href="ezobject://12">obj</a>', '<paragraph><link object_id="12">obj</link></paragraph>', array( '12' ), array() ),
            'object with anchor' => array( '<a href="ezobject://12#part">obj</a>', '<paragraph><link object_id="12" anchor_name="part">obj</link></paragraph>', array( '12' ), array() ),
            'link tag with target and title' => array( '<link href="ezobject://3" target="_blank" title="t">l</link>', '<paragraph><link target="_blank" xhtml:title="t" object_id="3">l</link></paragraph>', array( '3' ), array() ),
            'link with a required attribute among others' => array( '<a title="t" href="#x">l</a>', '<paragraph><link xhtml:title="t" anchor_name="x">l</link></paragraph>', array(), array() ),
            'link id' => array( '<a href="#x" id="top">l</a>', '<paragraph><link xhtml:id="top" anchor_name="x">l</link></paragraph>', array(), array() ),
            'external' => array( '<a href="https://k1d.example.invalid/page">ext</a>', '<paragraph><link url_id="9000">ext</link></paragraph>', array(), array( 9000 => 'https://k1d.example.invalid/page' ) ),
            'external with anchor' => array( '<a href="https://k1d.example.invalid/p#sec">ext</a>', '<paragraph><link url_id="9000" anchor_name="sec">ext</link></paragraph>', array(), array( 9000 => 'https://k1d.example.invalid/p' ) ),
            'external with quotes and ampersand' => array( '<a href="https://k1d.example.invalid/?a=1&amp;b=\'x\'">ext</a>', '<paragraph><link url_id="9000">ext</link></paragraph>', array(), array( 9000 => 'https://k1d.example.invalid/?a=1&b=%27x%27' ) ),
            'mail address' => array( '<a href="mailto:someone@k1d.example.invalid">m</a>', '<paragraph><link url_id="9000">m</link></paragraph>', array(), array( 9000 => 'mailto:someone@k1d.example.invalid' ) ),
            'same url twice' => array( '<a href="http://k1d.example.invalid/">a</a><a href="http://k1d.example.invalid/">b</a>', '<paragraph><link url_id="9000">a</link><link url_id="9000">b</link></paragraph>', array(), array( 9000 => 'http://k1d.example.invalid/' ) ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('linkProvider')]
    public function testLinks( $input, $expected, $linkedObjects, $urls )
    {
        $parser = $this->parser();
        list( $xml, $messages, $valid ) = $this->parse( $input, $parser );
        $this->assertSame( $expected, $xml );
        $this->assertSame( array(), $messages );
        $this->assertSame( $linkedObjects, $parser->getLinkedObjectIDArray() );
        $this->assertSame( $urls, $parser->registeredURLs );
        $this->assertSame( array_keys( $urls ), $parser->getUrlIDArray() );
    }

    public function testRequiredAttributeMissingAmongOthers()
    {
        list( $xml, $messages, $valid ) = $this->parse( '<a title="t">l</a><embed size="small" />' );
        $this->assertSame( '<paragraph><link xhtml:title="t">l</link><embed size="small"/></paragraph>', $xml );
        $this->assertSame( array( "Required attribute 'href' is not presented in tag &lt;link&gt;.", "Required attribute 'href' is not presented in tag &lt;embed&gt;." ), $messages );
        $this->assertFalse( $valid );
    }

    public function testInvalidMailAddressIsRemoved()
    {
        $parser = $this->parser();
        list( $xml, $messages, $valid ) = $this->parse( '<a href="mailto:a..b@k1d.example.invalid">m</a>', $parser );
        $this->assertSame( '<paragraph><link>m</link></paragraph>', $xml );
        $this->assertSame( array( "Invalid e-mail address: 'a..b@k1d.example.invalid'" ), $messages );
        $this->assertSame( array(), $parser->registeredURLs );
    }

    public function testEmbeds()
    {
        $parser = $this->parser();
        list( $xml, $messages ) = $this->parse( '<embed href="ezobject://7" size="medium" align="left" class="itemized" /><embed-inline href="ezobject://9" view="line" /><embed href="ezobject://7" />', $parser );
        $this->assertSame( '<paragraph><embed size="medium" align="left" class="itemized" object_id="7"/><embed-inline view="line" object_id="9"/><embed object_id="7"/></paragraph>', $xml );
        $this->assertSame( array(), $messages );
        $this->assertSame( array( '7', '9' ), $parser->getRelatedObjectIDArray() );
    }

    public function testEmbedAttributesNotInTheSchemaAreRemoved()
    {
        list( $xml, $messages ) = $this->parse( '<embed href="ezobject://7" foo="bar" />' );
        $this->assertSame( '<paragraph><embed object_id="7"/></paragraph>', $xml );
        $this->assertSame( array( "Attribute 'custom:foo' is not allowed in &lt;embed&gt; element." ), $messages );
    }

    public function testRemovingDefaultAttributes()
    {
        $parser = $this->parser( 5, false, eZXMLInputParser::ERROR_NONE, true );
        list( $xml ) = $this->parse( '<a href="#a" target="_self">l</a><a href="#b" target="_blank">m</a><embed href="ezobject://7" view="embed" align="" />', $parser );
        $this->assertSame( '<paragraph><link anchor_name="a">l</link><link target="_blank" anchor_name="b">m</link><embed object_id="7"/></paragraph>', $xml );
    }

    public function testLineBreaksBecomeLinesAndParagraphs()
    {
        list( $xml, $messages ) = $this->parse( "One\nTwo\n\nThree", $this->parser( 5, true ) );
        $this->assertSame( '<paragraph><line>One</line><line>Two</line></paragraph><paragraph>Three</paragraph>', $xml );
        $this->assertSame( array(), $messages );
    }

    public function testLineBreaksInAHeader()
    {
        list( $xml ) = $this->parse( "<header level=\"1\">A\nB</header>", $this->parser( 5, true ) );
        $this->assertSame( '<section><header><line>A</line><line>B</line></header></section>', $xml );
    }

    public function testErrorLevelsDecideWhetherProcessingStops()
    {
        $parser = $this->parser( 5, false, eZXMLInputParser::ERROR_SCHEMA );
        $this->assertFalse( $parser->process( '<p title="x">x</p>' ), 'a schema error stops at the schema level' );
        $this->assertFalse( $parser->isValid() );

        $parser = $this->parser( 5, false, eZXMLInputParser::ERROR_SCHEMA );
        $this->assertInstanceOf( 'DOMDocument', $parser->process( '<unknown>x</unknown>' ), 'a syntax error does not stop at the schema level' );

        $parser = $this->parser( 5, false, eZXMLInputParser::ERROR_SYNTAX );
        $this->assertFalse( $parser->process( '<unknown>x</unknown>' ) );

        $parser = $this->parser( 5, false, eZXMLInputParser::ERROR_DATA );
        $this->assertFalse( $parser->process( '<a href="javascript:x()">x</a>' ) );
    }

    public function testErrorsNotDetectedAreNotReported()
    {
        $parser = new eZXMLTextTestInputParser( 5, eZXMLInputParser::ERROR_NONE, eZXMLInputParser::ERROR_SYNTAX );
        $document = $parser->process( '<p title="x">x</p>' );
        $this->assertInstanceOf( 'DOMDocument', $document );
        $this->assertSame( array(), $parser->getMessages() );
        $this->assertTrue( $parser->isValid() );
        $this->assertSame( '<paragraph>x</paragraph>', self::body( $document ) );
    }

    public function testOldErrorLevelConstantsAreStillUnderstood()
    {
        $parser = new eZXMLTextTestInputParser( 5, true, eZXMLInputParser::SHOW_ALL_ERRORS );
        $this->assertSame( eZXMLInputParser::ERROR_ALL, $parser->DetectErrorLevel );
        $this->assertSame( eZXMLInputParser::ERROR_ALL, $parser->ValidateErrorLevel );
        $parser = new eZXMLTextTestInputParser( 5, false, eZXMLInputParser::SHOW_SCHEMA_ERRORS );
        $this->assertSame( eZXMLInputParser::ERROR_SCHEMA, $parser->DetectErrorLevel );
        $this->assertSame( eZXMLInputParser::ERROR_NONE, $parser->ValidateErrorLevel );
    }

    public function testTooDeepNestingIsRefused()
    {
        $parser = $this->parser();
        $document = $parser->process( str_repeat( '<custom name="quote">', 250 ) . 'x' );
        $this->assertFalse( $document );
        $this->assertFalse( $parser->isValid() );
        $this->assertContains( 'Tags are nested too deeply.', $parser->getMessages() );
    }

    public function testManyGreaterThanSignsInAnAttributeAreParsed()
    {
        list( $xml ) = $this->parse( '<custom name="factbox" title="' . str_repeat( '>', 3000 ) . '">x</custom>' );
        $this->assertStringContainsString( 'custom:title="' . str_repeat( '&gt;', 3000 ) . '"', $xml );
    }

    public function testAllowedNumericEntitiesAndMultipleSpaces()
    {
        $parser = $this->parser();
        $parser->AllowNumericEntities = true;
        $parser->AllowMultipleSpaces = true;
        list( $xml ) = $this->parse( 'a  &#65;', $parser );
        $this->assertSame( '<paragraph>a  &amp;#65;</paragraph>', $xml );
    }

    public function testStrictHeadersReportAWrongNesting()
    {
        $parser = $this->parser();
        $parser->StrictHeaders = true;
        list( $xml, $messages, $valid ) = $this->parse( '<h3>Deep</h3>', $parser );
        $this->assertSame( array( 'Incorrect headers nesting' ), $messages );
        $this->assertFalse( $valid );
        $this->assertSame( '<section><section><section><header>Deep</header></section></section></section>', $xml );
    }

    public function testParseAttributes()
    {
        $parser = $this->parser();
        $this->assertSame( array( 'a' => '1', 'b' => 'two words', 'c' => "it's", 'd' => '0', 'xhtml:e' => 'x' ),
                           $parser->parseAttributes( 'a="1" b="two words" c="it\'s" d=0 xhtml:e=\'x\' f=""' ) );
        $this->assertSame( array(), $parser->parseAttributes( '"no name"' ) );
    }

    public function testWashText()
    {
        $parser = $this->parser();
        $this->assertSame( "a < b > c & d \" e ' f '", $parser->washText( 'a &lt; b &gt; c &amp; d &quot; e &apos; f &#039;' ) );
        $this->assertSame( '&amp;lt;', $parser->washText( '&amp;amp;lt;' ) );
        $this->assertSame( "\u{00E9}", $parser->washText( '&#233;' ) );
        $this->assertSame( '&#', $parser->convertNumericEntities( '&#' ) );
    }

    public function testTagNameHeader()
    {
        $parser = $this->parser();
        foreach ( array( 1, 2, 3, 4, 5, 6 ) as $level )
        {
            $attributes = array();
            $this->assertSame( 'header', $parser->tagNameHeader( 'h' . $level, $attributes ) );
            $this->assertSame( (string)$level, $attributes['level'] );
        }
        $attributes = array();
        $this->assertSame( '', $parser->tagNameHeader( 'h9', $attributes ) );
    }

    public function testCreateRootNodeDeclaresTheNamespaces()
    {
        $parser = $this->parser();
        $document = $parser->createRootNode();
        $this->assertSame( '<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>',
                           $document->saveXML( $document->documentElement ) );
    }

    public function testProcessWithoutARootNode()
    {
        $parser = $this->parser();
        $document = $parser->process( '<paragraph>x</paragraph>', false );
        $this->assertInstanceOf( 'DOMDocument', $document );
        $this->assertSame( 'paragraph', $document->documentElement->nodeName );
    }

    public function testSettersAndDocumentClass()
    {
        $parser = $this->parser();
        $parser->setParseLineBreaks( true );
        $parser->setRemoveDefaultAttrs( true );
        $this->assertTrue( $parser->ParseLineBreaks );
        $this->assertTrue( $parser->RemoveDefaultAttrs );
        $parser->setDOMDocumentClass( 'eZXMLTextTestDocument' );
        $this->assertInstanceOf( 'eZXMLTextTestDocument', $parser->process( 'x' ) );
    }
}

class eZXMLTextTestDocument extends DOMDocument
{
}
