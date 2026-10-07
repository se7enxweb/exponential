<?php
/**
 * Styles pasted into the editor (from a word processor, from another site) are turned into attributes where they can
 * be, and left out where they can not, without a PHP warning and without making the input invalid:
 * eZOEInputParser::elementStylesToAttribute().
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expOETestCase.php';

class expOEPastedStylesTest extends expOETestCase
{
    /**
     * Parses $html and returns the stored XML without its declaration and section element; every PHP warning or
     * notice raised meanwhile fails the test.
     */
    protected function xml( $html )
    {
        $warnings = array();
        set_error_handler( function ( $number, $message ) use ( &$warnings )
        {
            $warnings[] = $message;
            return true;
        } );
        try
        {
            $parser = new eZOEInputParser();
            $doc = $parser->process( $html );
        }
        finally
        {
            restore_error_handler();
        }
        $this->assertSame( array(), $warnings, 'no PHP warning for: ' . $html );
        $this->assertNotNull( $doc, 'the parser gave a document for: ' . $html );
        $this->assertTrue( $parser->isValid(), 'the input stays valid: ' . $html );
        $xml = eZXMLTextType::domString( $doc );
        $check = new DOMDocument();
        $this->assertTrue( $check->loadXML( $xml ), 'the stored XML loads again' );
        return preg_replace( '/^<\?xml[^>]*\?>\s*<section[^>]*>|<\/section>\s*$/', '', $xml );
    }

    public function testADeclarationWithoutColonIsLeftOut()
    {
        $this->assertSame( '<paragraph align="right">a</paragraph>', $this->xml( '<p style="color; text-align: right">a</p>' ) );
        $this->assertSame( '<paragraph align="center">a</paragraph>', $this->xml( '<p style="text-align: center; mso-hide">a</p>' ) );
    }

    public function testAValueKeepsItsColons()
    {
        $this->assertSame( '<paragraph align="left">a</paragraph>',
                           $this->xml( '<p style="background: url(http://example.invalid/a.png); text-align: left">a</p>' ) );
    }

    public function testNamesThatCanNotBeAttributesAreLeftOut()
    {
        $this->assertSame( '<paragraph align="left">a</paragraph>',
                           $this->xml( '<p style="@font-face: x; -webkit-text-size-adjust: 100%; text-align: left">a</p>' ) );
    }

    public function testCharactersXmlDoesNotAllowNeverReachTheStoredXml()
    {
        $this->assertSame( '<paragraph align="center">a b</paragraph>', $this->xml( "<p style=\"text-align: center\x0B\">a\x0Bb</p>" ) );
    }
}
