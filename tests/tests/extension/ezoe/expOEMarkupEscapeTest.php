<?php
/**
 * The stored values the online editor puts into its markup are escaped once, without the database:
 * eZOEXMLInput::inputXML() and eZOEXMLInput::markupValue().
 *
 *  ME-01 - markupValue() escapes ", <, >, ' and & once
 *  ME-02 - A stored value with ", <, ', & and an attempted attribute stays one attribute value of the element it
 *          belongs to (header, paragraph, link, anchor, table, row, cell, list, item, literal, strong, emphasize,
 *          custom tag), and no element of the markup gets an attribute the stored XML did not have
 *  ME-03 - The anchor of a header and its text are escaped too
 *  ME-04 - Nothing is escaped twice: opening the markup and saving it again gives the stored values back as they were
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expOEMarkupEscapeTest extends PHPUnit\Framework\TestCase
{
    /** A stored value: a quote ending the attribute, markup, an apostrophe, an ampersand, an entity of its own */
    const VALUE = 'x" onmouseover="alert(1)" y=\'<b>&amp;';

    private $styleMap;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        if ( !class_exists( 'eZOEXMLInput' ) )
            $this->markTestSkipped( 'ezoe is not loaded' );
        // the style a custom attribute gives (ezoe.ini [EditorSettings] CustomAttributeStyleMap), without the ini file
        $map = new ReflectionProperty( 'eZOEXMLInput', 'customAttributeStyleMap' );
        $this->styleMap = $map->getValue();
        $map->setValue( null, array( 'margin' => 'margin' ) );
    }

    protected function tearDown(): void
    {
        if ( class_exists( 'eZOEXMLInput' ) )
            ( new ReflectionProperty( 'eZOEXMLInput', 'customAttributeStyleMap' ) )->setValue( null, $this->styleMap );
    }

    /**
     * The stored XML with VALUE in every attribute the editor shows
     *
     * @param bool $withLink A link: saving it again registers its address (in the database), so the test of saving
     *                       leaves it out
     */
    private function storedXml( $withLink = true )
    {
        $v = htmlspecialchars( self::VALUE, ENT_QUOTES | ENT_XML1, 'UTF-8' );
        $link = $withLink ? '<link href="' . $v . '" target="' . $v . '" class="' . $v . '" view="' . $v . '" xhtml:title="' . $v . '" xhtml:id="' . $v . '">l</link>' : '';
        return '<?xml version="1.0" encoding="utf-8"?>' . "\n"
             . '<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/">'
             . '<section><header class="' . $v . '" align="' . $v . '">Head</header>'
             . '<header anchor_name="' . $v . '">T ' . $v . '</header>'
             . '<paragraph class="' . $v . '" align="' . $v . '">text ' . $link
             . '<anchor name="' . $v . '"/><strong custom:margin="' . $v . '">m</strong></paragraph>'
             . '<paragraph><table class="' . $v . '" width="' . $v . '" border="' . $v . '" align="' . $v . '"><tr class="' . $v . '">'
             . '<td class="' . $v . '" align="' . $v . '" xhtml:width="' . $v . '" xhtml:colspan="' . $v . '" xhtml:rowspan="' . $v . '"><paragraph>c</paragraph></td>'
             . '</tr></table></paragraph>'
             . '<paragraph><ul class="' . $v . '"><li class="' . $v . '"><paragraph>i</paragraph></li></ul></paragraph>'
             . '<paragraph><literal class="' . $v . '">lit</literal></paragraph>'
             . '<paragraph><strong class="' . $v . '">s</strong><emphasize class="' . $v . '">e</emphasize></paragraph>'
             . '<paragraph><custom name="' . $v . '" align="' . $v . '"><paragraph>c</paragraph></custom></paragraph>'
             . '</section></section>';
    }

    /** The markup of the editor (inputXML() escapes it once more for the textarea; the browser undoes that) */
    private function markup( $xml )
    {
        $attribute = new eZContentObjectAttribute( array( 'id' => 0, 'contentobject_id' => 0, 'version' => 1, 'data_text' => $xml ) );
        $handler = new eZOEXMLInput( $xml, false, $attribute );
        return html_entity_decode( $handler->inputXML(), ENT_QUOTES | ENT_HTML401, 'UTF-8' );
    }

    /** The markup as a browser reads it */
    private function dom( $markup )
    {
        $doc = new DOMDocument();
        $previous = libxml_use_internal_errors( true );
        $doc->loadHTML( '<html><head><meta charset="utf-8"></head><body>' . $markup . '</body></html>' );
        libxml_clear_errors();
        libxml_use_internal_errors( $previous );
        return new DOMXPath( $doc );
    }

    /** ME-01 */
    public function testMarkupValueEscapesOnce()
    {
        $this->assertSame( 'a&quot;b&lt;c&gt;&#039;d&amp;e', eZOEXMLInput::markupValue( 'a"b<c>\'d&e' ) );
        $this->assertSame( '&amp;amp;', eZOEXMLInput::markupValue( '&amp;' ), 'a stored "&amp;" is a value of its own' );
        $this->assertSame( '12', eZOEXMLInput::markupValue( 12 ) );
        $this->assertSame( '', eZOEXMLInput::markupValue( null ) );
    }

    /** ME-02 */
    public function testAStoredValueStaysOneAttributeValue()
    {
        $xpath = $this->dom( $this->markup( $this->storedXml() ) );
        $checks = array(
            '//h1[not(a)]'                    => array( 'class', 'align' ),
            '//p[@align]'                     => array( 'class', 'align' ),
            '//a[@href]'                      => array( 'href', 'data-mce-href', 'target', 'class', 'view', 'title', 'id' ),
            '//a[contains(@class,"mceItemAnchor") and not(parent::h1)]' => array( 'name' ),
            '//table'                         => array( 'class', 'width', 'border', 'align' ),
            '//tr'                            => array( 'class' ),
            '//td'                            => array( 'class', 'align', 'width', 'colspan', 'rowspan' ),
            '//ul'                            => array( 'class' ),
            '//li'                            => array( 'class' ),
            '//pre'                           => array( 'class' ),
            '//strong[@class]'                => array( 'class' ),
            '//em'                            => array( 'class' ),
            '//div[@type="custom"]'           => array( 'align' ),
        );
        foreach ( $checks as $query => $names )
        {
            $nodes = $xpath->query( $query );
            $this->assertSame( 1, $nodes->length, $query );
            foreach ( $names as $name )
            {
                $this->assertSame( self::VALUE, $nodes->item( 0 )->getAttribute( $name ), "$query @$name" );
            }
        }
        $custom = $xpath->query( '//div[@type="custom"]' )->item( 0 );
        $this->assertSame( 'ezoeItemCustomTag ' . self::VALUE, $custom->getAttribute( 'class' ) );

        // a custom attribute and the style it gives
        $styled = $xpath->query( '//strong[@style]' );
        $this->assertSame( 1, $styled->length );
        $this->assertSame( 'margin: ' . self::VALUE . '; ', $styled->item( 0 )->getAttribute( 'style' ) );
        $this->assertSame( 'margin|' . self::VALUE, $styled->item( 0 )->getAttribute( 'customattributes' ) );

        $this->assertSame( 0, $xpath->query( '//*[@onmouseover]' )->length, 'no attribute of its own' );
        $this->assertSame( 0, $xpath->query( '//*[@y]' )->length, 'no attribute of its own' );
        $this->assertSame( 0, $xpath->query( '//b' )->length, 'no element of its own' );
    }

    /** ME-03 */
    public function testTheAnchorOfAHeaderAndItsTextAreEscaped()
    {
        $xpath = $this->dom( $this->markup( $this->storedXml() ) );
        $anchor = $xpath->query( '//h1/a[contains(@class,"mceItemAnchor")]' );
        $this->assertSame( 1, $anchor->length );
        $this->assertSame( self::VALUE, $anchor->item( 0 )->getAttribute( 'name' ) );
        $this->assertSame( 'T ' . self::VALUE, $anchor->item( 0 )->parentNode->textContent );
    }

    /** ME-04 */
    public function testOpeningAndSavingGivesTheStoredValuesBack()
    {
        $parser = new eZOEInputParser();
        $back = eZXMLTextType::domString( $parser->process( $this->markup( $this->storedXml( false ) ) ) );
        $doc = new DOMDocument();
        $this->assertTrue( $doc->loadXML( $back ), $back );
        $xpath = new DOMXPath( $doc );
        $xhtml = 'http://ez.no/namespaces/ezpublish3/xhtml/';
        // the attributes the parser keeps whatever their value (a class is kept only when it is an available one)
        $checks = array( array( '//header[@align]', null, 'align' ), array( '//paragraph[@align]', null, 'align' ),
                         array( '//header/anchor', null, 'name' ), array( '//paragraph/anchor', null, 'name' ),
                         array( '//table', null, 'width' ), array( '//table', null, 'align' ), array( '//table', null, 'border' ),
                         array( '//td', null, 'align' ), array( '//td', $xhtml, 'width' ), array( '//td', $xhtml, 'colspan' ),
                         array( '//td', $xhtml, 'rowspan' ) );
        foreach ( $checks as $check )
        {
            list( $query, $namespace, $name ) = $check;
            $nodes = $xpath->query( $query );
            $this->assertSame( 1, $nodes->length, $query . ' in ' . $back );
            $value = $namespace === null ? $nodes->item( 0 )->getAttribute( $name ) : $nodes->item( 0 )->getAttributeNS( $namespace, $name );
            $this->assertSame( self::VALUE, $value, "$query @$name, nothing escaped twice" );
        }
        $this->assertSame( 'T ' . self::VALUE, $xpath->query( '//header[anchor]' )->item( 0 )->textContent );
        $this->assertStringNotContainsString( '&amp;amp;amp;', $back );
        $this->assertStringNotContainsString( '&amp;quot;', $back );
    }
}
