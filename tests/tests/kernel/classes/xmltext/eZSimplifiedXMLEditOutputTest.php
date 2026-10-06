<?php
/**
 * The edit output of the simplified XML input, which turns stored XML text back into the text the editor edits,
 * and the round trip text -> stored XML -> text -> stored XML, which must give the same stored XML again: headers
 * with their levels, paragraphs, lines, lists, tables, links (object, anchor, external), embeds, custom tags,
 * literal text, attributes with quotes and markup in them.
 *
 * No database: external links are counted instead of registered, and the edit output is given the URLs.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group xmltext
 */

require_once __DIR__ . '/eZXMLTextTestFixtures.php';

class eZSimplifiedXMLEditOutputTest extends eZXMLTextTestCase
{
    private function stored( $input, $parser = null )
    {
        $parser = $parser ?: $this->parser( 5, true );
        $document = $parser->process( $input );
        $this->assertInstanceOf( 'DOMDocument', $document, implode( ' ', $parser->getMessages() ) );
        return array( eZXMLTextType::domString( $document ), $parser );
    }

    private function editText( $xml, array $urls = array() )
    {
        $dom = new DOMDocument( '1.0', 'utf-8' );
        $dom->loadXML( $xml );
        $output = new eZSimplifiedXMLEditOutput();
        $output->LinkArray = $urls;
        $output->XMLSchema = eZXMLSchema::instance();
        $output->NestingLevel = 0;
        $output->Output = '';
        $sectionLevel = -1;
        $output->outputTag( $dom->documentElement, $sectionLevel );
        return $output->Output;
    }

    public static function editTextProvider()
    {
        return array(
            'paragraph' => array( 'Hello', 'Hello' ),
            'two paragraphs' => array( "One\n\nTwo", "One\n\nTwo" ),
            'lines' => array( "One\nTwo", "One\nTwo" ),
            'inline tags' => array( '<strong>b</strong> <emphasize>e</emphasize>', '<strong>b</strong> <emphasize>e</emphasize>' ),
            'headers get their level' => array( '<h1>T</h1><p>x</p><h2>S</h2>', "<header level=\"1\">T</header>\nx\n\n<header level=\"2\">S</header>" ),
            'list' => array( '<ul><li>a</li><li>b</li></ul>', "<ul>\n  <li>a</li>\n  <li>b</li>\n</ul>" ),
            'table' => array( '<table><tr><td colspan="2">x</td></tr></table>', "<table>\n  <tr>\n    <td colspan=\"2\">x</td>\n  </tr>\n</table>" ),
            'header in a table cell starts at level 1' => array( '<table><tr><td><h1>H</h1></td></tr></table>', "<table>\n  <tr>\n    <td>\n      <header level=\"1\">H</header>\n    </td>\n  </tr>\n</table>" ),
            'literal keeps its text' => array( '<literal>a <b> & c</literal>', '<literal>a <b> & c</literal>' ),
            'text is escaped' => array( 'a &amp; b &lt; c', 'a &amp; b &lt; c' ),
            'object link' => array( '<a href="ezobject://12#part">o</a>', '<link href="ezobject://12#part">o</link>' ),
            'anchor link' => array( '<a href="#here">o</a>', '<link href="#here">o</link>' ),
            'link title and target' => array( '<link href="ezobject://3" target="_blank" title="t">l</link>', '<link target="_blank" title="t" href="ezobject://3">l</link>' ),
            'anchor' => array( '<anchor name="a1" />x', '<anchor name="a1" />x' ),
            'embed' => array( '<embed href="ezobject://7" size="medium" />', '<embed size="medium" href="ezobject://7" />' ),
            'embed inline' => array( '<embed-inline href="ezobject://9" />', '<embed-inline href="ezobject://9" />' ),
            'custom tag' => array( '<custom name="factbox" title="F">Box</custom>', "<custom name=\"factbox\" custom:title=\"F\">Box</custom>" ),
            'inline custom tag' => array( 'a <custom name="strike">s</custom>', 'a <custom name="strike">s</custom>' ),
            'attribute values are escaped' => array( '<custom name="factbox" title="Say &quot;hi&quot; &amp; go">Box</custom>', '<custom name="factbox" custom:title="Say &quot;hi&quot; &amp; go">Box</custom>' ),
            'paragraph class' => array( '<p class="lead">x</p>', '<paragraph class="lead">x</paragraph>' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('editTextProvider')]
    public function testEditText( $input, $expected )
    {
        list( $xml ) = $this->stored( $input );
        $this->assertSame( $expected, $this->editText( $xml ) );
    }

    public static function roundTripProvider()
    {
        $cases = array();
        foreach ( self::editTextProvider() as $name => $case )
            $cases[$name] = array( $case[0] );
        $cases['quote in a custom attribute'] = array( '<custom name="factbox" title="Say &quot;hi&quot;">Box</custom>' );
        $cases['ampersand and less-than in a title'] = array( '<a href="#x" title="Tom &amp; Jerry &lt;3">l</a>' );
        $cases['apostrophe in a title'] = array( '<a href="#x" title="it&#039;s">l</a>' );
        $cases['nested sections'] = array( '<h1>A</h1><h2>B</h2><p>b</p><h3>C</h3><h1>D</h1>' );
        return $cases;
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('roundTripProvider')]
    public function testEditTextGivesTheSameStoredXmlBack( $input )
    {
        list( $xml ) = $this->stored( $input );
        $text = $this->editText( $xml );
        list( $again, $parser ) = $this->stored( $text );
        $this->assertSame( array(), $parser->getMessages(), $text );
        $this->assertSame( self::withoutTmpNamespace( $xml ), self::withoutTmpNamespace( $again ), "edit text: $text" );
    }

    /**
     * The parser leaves the declaration of its temporary namespace on the elements it created, depending on how
     * they came about; it carries no attribute and does not change the content.
     */
    private static function withoutTmpNamespace( $xml )
    {
        return str_replace( ' xmlns:tmp="http://ez.no/namespaces/ezpublish3/temporary/"', '', $xml );
    }

    public function testExternalLinkRoundTripUsesTheUrlTable()
    {
        list( $xml, $parser ) = $this->stored( '<a href="https://k1d.example.invalid/?a=1&amp;b=2#sec">ext</a>' );
        $urls = $parser->registeredURLs;
        $this->assertSame( array( 9000 => 'https://k1d.example.invalid/?a=1&b=2' ), $urls );
        $text = $this->editText( $xml, $urls );
        $parser2 = $this->parser( 5, true );
        list( $again ) = $this->stored( $text, $parser2 );
        $this->assertSame( $urls, $parser2->registeredURLs );
        $this->assertSame( $xml, $again );
    }

    public function testLinkToAUrlThatIsGoneHasNoTarget()
    {
        list( $xml ) = $this->stored( '<a href="https://k1d.example.invalid/">ext</a>' );
        $this->assertSame( '<link href="">ext</link>', $this->editText( $xml ) );
    }

    public function testPerformOutputOfAnEmptyDocument()
    {
        $output = new eZSimplifiedXMLEditOutput();
        $this->assertSame( '', $output->performOutput( new DOMDocument() ) );
        $dom = new DOMDocument();
        $dom->loadXML( '<section/>' );
        $this->assertSame( '', $output->performOutput( $dom ) );
    }

    public function testPerformOutputWithoutLinks()
    {
        list( $xml ) = $this->stored( "<h1>T</h1>Para" );
        $dom = new DOMDocument();
        $dom->loadXML( $xml );
        $output = new eZSimplifiedXMLEditOutput();
        $this->assertSame( "<header level=\"1\">T</header>\nPara", $output->performOutput( $dom ) );
    }

    public function testEznodeHrefWithoutPath()
    {
        $this->assertSame( 'eznode://42', eZSimplifiedXMLEditOutput::eznodeHref( 42, 'false' ) );
        $this->assertSame( 'eznode://42', eZSimplifiedXMLEditOutput::eznodeHref( 42, null ) );
    }

    public function testNodeLinkAndEmbedWithoutPath()
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>' . "\n" . '<section><paragraph><link node_id="42" anchor_name="a">n</link><embed node_id="43"/></paragraph></section>';
        $this->assertSame( '<link href="eznode://42#a">n</link><embed href="eznode://43" />', $this->editText( $xml ) );
    }
}
