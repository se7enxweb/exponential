<?php
/**
 * Characters above U+FFFF (XML 1.0 #x10000-#x10FFFF) survive the input handler and the input parser.
 * Before, eZXMLInputHandler removed them when the stored XML was opened in an editor ("<mi>&#x1D465;</mi>" in a
 * MathML attribute became "<mi></mi>") and eZXMLInputParser::process() made each of them a space when the editor
 * posted ("a &#x1F600; b" was stored as "a b"). Characters XML does not allow are still removed. No database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class eZXMLInputSupplementaryCharactersTest extends PHPUnit\Framework\TestCase
{
    const HEAD = '<?xml version="1.0" encoding="utf-8"?><section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/">';

    public static function supplementaryTexts()
    {
        return array(
            'emoji'              => array( "a \u{1F600} b" ),
            'mathematical italic' => array( "\u{1D465} + 1" ),
            'double-struck'      => array( "\u{1D538} = \\mathbb{A}" ),
            'last plane'         => array( "x\u{10FFFD}y" ),
        );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'supplementaryTexts' )]
    public function testTheInputHandlerKeepsThemInText( $text )
    {
        $xml = self::HEAD . '<paragraph>' . $text . '</paragraph></section>';
        $handler = new eZXMLInputHandler( $xml, false, null );
        $this->assertSame( $xml, $handler->XMLData );
    }

    public function testTheInputHandlerKeepsThemInACustomAttribute()
    {
        $xml = self::HEAD . '<paragraph><custom name="math_mathml" custom:mathml="&lt;math&gt;&lt;mi&gt;' . "\u{1D465}" .
               '&lt;/mi&gt;&lt;/math&gt;">x</custom></paragraph></section>';
        $handler = new eZXMLInputHandler( $xml, false, null );
        $this->assertSame( $xml, $handler->XMLData );
    }

    public function testTheInputHandlerStillRemovesWhatXmlDoesNotAllow()
    {
        $handler = new eZXMLInputHandler( "a\x01b\u{FFFE}c", false, null );
        $this->assertSame( 'abc', $handler->XMLData );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'supplementaryTexts' )]
    public function testTheInputParserKeepsThem( $text )
    {
        $parser = new eZSimplifiedXMLInputParser( 2, eZXMLInputParser::ERROR_NONE, eZXMLInputParser::ERROR_NONE, true );
        $document = $parser->process( '<paragraph>' . $text . '</paragraph>' );
        $this->assertInstanceOf( DOMDocument::class, $document );
        $this->assertSame( $text, $document->documentElement->textContent );
        $this->assertSame( array(), $parser->getMessages() );
    }

    public function testTheInputParserStillReplacesWhatXmlDoesNotAllow()
    {
        $parser = new eZSimplifiedXMLInputParser( 2, eZXMLInputParser::ERROR_NONE, eZXMLInputParser::ERROR_NONE, true );
        $document = $parser->process( "<paragraph>a\x01b</paragraph>" );
        $this->assertSame( 'a b', $document->documentElement->textContent );
        $this->assertCount( 1, $parser->getMessages() );
    }
}
