<?php
/**
 * The XHTML the TinyMCE 8 editor posts is parsed by eZOEInputParser into the same ezxml as the TinyMCE 3
 * markup, and the ezxml the handler shows an editor parses back to itself. Pairs of markup: [ TinyMCE 3, TinyMCE 8 ].
 */

require_once __DIR__ . '/expOETestCase.php';

class expOERoundTripTest extends expOETestCase
{
    protected function xml( $html )
    {
        $parser = new eZOEInputParser();
        $doc = $parser->process( $html );
        $this->assertNotNull( $doc, 'the parser gave no document for: ' . $html );
        $xml = eZXMLTextType::domString( $doc );
        // a link is stored by id: the id is the same for the same url, the test only needs equality
        return preg_replace( '/^<\?xml[^>]*\?>\s*<section[^>]*>/', '', $xml );
    }

    public static function pairs()
    {
        return array(
            'paragraph'      => array( '<p>Hello</p>', '<p>Hello</p>' ),
            'two paragraphs' => array( '<p>One</p><p>Two</p>', '<p>One</p>' . "\n" . '<p>Two</p>' ),
            'bold'           => array( '<p><strong>a</strong></p>', '<p><strong>a</strong></p>' ),
            'bold b tag'     => array( '<p><strong>a</strong></p>', '<p><b>a</b></p>' ),
            'emphasize'      => array( '<p><em>a</em></p>', '<p><i>a</i></p>' ),
            'underline'      => array( '<p><u>a</u></p>', '<p><u>a</u></p>' ),
            'mixed inline'   => array( '<p><strong>a</strong> <em>b</em></p>', '<p><strong>a</strong> <em>b</em></p>' ),
            'link'           => array( '<p><a href="http://example.com/">x</a></p>', '<p><a href="http://example.com/">x</a></p>' ),
            'link target'    => array( '<p><a href="http://example.com/" target="_blank">x</a></p>', '<p><a target="_blank" href="http://example.com/">x</a></p>' ),
            'link title'     => array( '<p><a href="http://example.com/" title="t">x</a></p>', '<p><a title="t" href="http://example.com/">x</a></p>' ),
            'eznode link'    => array( '<p><a href="eznode://2">x</a></p>', '<p><a href="eznode://2">x</a></p>' ),
            'ezobject link'  => array( '<p><a href="ezobject://108">x</a></p>', '<p><a href="ezobject://108">x</a></p>' ),
            'mailto'         => array( '<p><a href="mailto:a@example.com">x</a></p>', '<p><a href="mailto:a@example.com">x</a></p>' ),
            'anchor'         => array( '<p><a name="top" class="mceItemAnchor"></a>x</p>', '<p><a name="top" class="mceItemAnchor"></a>x</p>' ),
            'unordered list' => array( '<ul><li>a</li><li>b</li></ul>', '<ul>' . "\n" . '<li>a</li>' . "\n" . '<li>b</li>' . "\n" . '</ul>' ),
            'ordered list'   => array( '<ol><li>a</li></ol>', '<ol>' . "\n" . '<li>a</li>' . "\n" . '</ol>' ),
            'list bold item' => array( '<ul><li><strong>a</strong></li></ul>', '<ul><li><strong>a</strong></li></ul>' ),
            'heading'        => array( '<h2>Head</h2>', '<h2>Head</h2>' ),
            'heading class'  => array( '<h2 class="c">Head</h2>', '<h2 class="c">Head</h2>' ),
            'line break'     => array( '<p>a<br />b</p>', '<p>a<br>b</p>' ),
            'entities'       => array( '<p>a&nbsp;b &amp; c &lt; d</p>', '<p>a&nbsp;b &amp; c &lt; d</p>' ),
            'table'          => array( '<table class="list" width="100%" border="0"><tr><th>h</th></tr><tr><td>c</td></tr></table>',
                                       '<table class="list" border="0" width="100%"><tbody><tr><th>h</th></tr><tr><td>c</td></tr></tbody></table>' ),
            'table colspan'  => array( '<table border="0"><tr><td colspan="2">c</td></tr></table>', '<table border="0"><tbody><tr><td colspan="2">c</td></tr></tbody></table>' ),
            'embed image'    => array( '<p><img id="eZObject_108" view="embed" inline="false" alt="medium" src="x.jpg" /></p>',
                                       '<p><img id="eZObject_108" view="embed" inline="false" alt="medium" src="x.jpg" width="200" height="112"></p>' ),
            'embed inline'   => array( '<p>a <img id="eZObject_108" view="embed-inline" inline="true" alt="small" src="x.jpg" /> b</p>',
                                       '<p>a <img id="eZObject_108" view="embed-inline" inline="true" alt="small" src="x.jpg" width="50" height="30"> b</p>' ),
            'embed div'      => array( '<div id="eZObject_108" type="" view="embed" inline="false" alt="medium" align="middle"><img src="x.jpg"/></div>',
                                       '<div id="eZObject_108" class="ezoeItemNonEditable" view="embed" inline="false" alt="medium" align="middle"><img src="x.jpg"></div>' ),
            'literal'        => array( '<pre>code &lt;b&gt;</pre>', '<pre>code &lt;b&gt;</pre>' ),
            'literal class'  => array( '<pre class="ezoeItemNonEditable">code</pre>', '<pre>code</pre>' ),
            'custom tag'     => array( '<div type="custom" class="quote" customattributes="author|Me"><p>x</p></div>',
                                       '<div class="quote" type="custom" customattributes="author|Me"><p>x</p></div>' ),
        );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'pairs' )]
    public function testTinyMCE8MarkupGivesTheSameXmlAsTinyMCE3( $tiny3, $tiny8 )
    {
        $this->assertSame( $this->xml( $tiny3 ), $this->xml( $tiny8 ) );
    }

    public function testTheTinyMCE8AnchorWithAnIdAloneIsLostSoTheEditorWritesAName()
    {
        // TinyMCE 8 writes <a id="x">; the parser only knows <a name="x" class="mceItemAnchor">, and the
        // ezlink plugin converts one into the other when the content is saved
        $this->assertStringNotContainsString( '<anchor', $this->xml( '<p><a id="top">x</a></p>' ) );
        $this->assertStringContainsString( '<anchor name="top"/>', $this->xml( '<p><a name="top" class="mceItemAnchor"></a>x</p>' ) );
        $plugin = file_get_contents( dirname( __DIR__, 4 ) . '/extension/ezoe/design/standard/javascript/tinymce8_ez/plugins/ezlink/plugin.js' );
        $this->assertStringContainsString( "classList.add( 'mceItemAnchor' )", $plugin );
    }

    public function testEmbedGivesTheObjectIdAndSize()
    {
        $xml = $this->xml( '<p><img id="eZObject_108" view="embed" inline="false" alt="medium" src="x.jpg"></p>' );
        $this->assertStringContainsString( 'object_id="108"', $xml );
        $this->assertStringContainsString( 'size="medium"', $xml );
        $this->assertStringContainsString( 'view="embed"', $xml );
    }

    public function testInlineEmbedIsAnEmbedInlineTag()
    {
        $this->assertStringContainsString( '<embed-inline', $this->xml( '<p>a <img id="eZObject_108" view="embed-inline" inline="true" alt="small" src="x.jpg"> b</p>' ) );
    }

    public function testCustomTagKeepsItsNameAndAttributes()
    {
        $xml = $this->xml( '<div class="quote" type="custom" customattributes="author|Me"><p>x</p></div>' );
        $this->assertStringContainsString( '<custom name="quote" custom:author="Me">', $xml );
    }

    public function testLiteralKeepsMarkupAsText()
    {
        $this->assertStringContainsString( '<literal>code &lt;b&gt;</literal>', $this->xml( '<pre>code &lt;b&gt;</pre>' ) );
    }

    public function testScriptsAreNotKept()
    {
        $this->assertStringNotContainsString( 'script', $this->xml( '<p>a<script>alert(1)</script>b</p>' ) );
    }

    public function testEventHandlersAreNotKept()
    {
        $this->assertStringNotContainsString( 'onclick', $this->xml( '<p><a href="http://example.com/" onclick="x()">a</a></p>' ) );
    }

    public function testAJavascriptLinkIsNotKept()
    {
        $this->assertStringNotContainsString( 'javascript:', $this->xml( '<p><a href="javascript:alert(1)">a</a></p>' ) );
    }

    public static function xmlDocuments()
    {
        $head = '<?xml version="1.0" encoding="utf-8"?><section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/">';
        return array(
            'paragraph' => array( $head . '<paragraph>Hello</paragraph></section>', array( '<paragraph>Hello</paragraph>' ) ),
            'bold'      => array( $head . '<paragraph>a <strong>b</strong><emphasize>c</emphasize></paragraph></section>', array( '<strong>b</strong>', '<emphasize>c</emphasize>' ) ),
            'embed'     => array( $head . '<paragraph><embed view="embed" size="medium" object_id="108"/></paragraph></section>', array( 'object_id="108"', 'size="medium"' ) ),
            'link'      => array( $head . '<paragraph><link node_id="2">x</link></paragraph></section>', array( '<link node_id="2">x</link>' ) ),
            'heading'   => array( $head . '<section><header>Head</header><paragraph>x</paragraph></section></section>', array( '<header>Head</header>' ) ),
            'literal'   => array( $head . '<paragraph><literal>x &lt; y</literal></paragraph></section>', array( '<literal>x &lt; y</literal>' ) ),
            'list'      => array( $head . '<paragraph><ul><li><paragraph>a</paragraph></li></ul></paragraph></section>', array( '<li>', 'a' ) ),
        );
    }

    protected function canonical( $xml )
    {
        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = false;
        $doc->loadXML( $xml );
        return preg_replace( '/>\s+</', '><', trim( $doc->documentElement->C14N() ) );
    }

    protected function backAndForth( $xml )
    {
        $attribute = new eZContentObjectAttribute( array( 'id' => 0, 'contentobject_id' => 0, 'version' => 1, 'data_text' => $xml ) );
        $handler = new eZOEXMLInput( $xml, false, $attribute );
        $html = html_entity_decode( $handler->inputXML(), ENT_QUOTES, 'UTF-8' );
        $parser = new eZOEInputParser();
        return eZXMLTextType::domString( $parser->process( $html ) );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'xmlDocuments' )]
    public function testTheXmlThatTheEditorShowsKeepsItsContent( $xml, array $tokens )
    {
        $back = $this->backAndForth( $xml );
        foreach ( $tokens as $token )
            $this->assertStringContainsString( $token, $back );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'xmlDocuments' )]
    public function testOpeningAndSavingTwiceChangesNothingTheSecondTime( $xml, array $tokens )
    {
        $once = $this->backAndForth( $xml );
        $twice = $this->backAndForth( $once );
        $this->assertSame( $this->canonical( $once ), $this->canonical( $twice ) );
    }
}
