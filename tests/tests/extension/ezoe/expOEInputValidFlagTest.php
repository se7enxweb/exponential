<?php
/**
 * A link the online editor refuses makes the input invalid through the flag the parser reads, without the database:
 * eZOEInputParser::process() and isValid().
 *
 *  IV-01 - A script link: isValid() is false, the message is there, the link loses its address
 *  IV-02 - An invalid e-mail address: isValid() is false and the message is there
 *  IV-03 - Neither raises a PHP warning or deprecation (no undefined or dynamic property)
 *  IV-04 - A link to an anchor of the same page keeps the input valid
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expOEInputValidFlagTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        if ( !class_exists( 'eZOEInputParser' ) )
            $this->markTestSkipped( 'ezoe is not loaded' );
    }

    /**
     * Parses $html; every PHP warning, notice or deprecation raised meanwhile fails the test.
     *
     * @return array the parser and the stored XML
     */
    private function parse( $html )
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
            $doc = $parser->process( '<p>' . $html . '</p>' );
        }
        finally
        {
            restore_error_handler();
        }
        $this->assertSame( array(), $warnings );
        return array( $parser, $doc ? eZXMLTextType::domString( $doc ) : '' );
    }

    public function testAScriptLinkMakesTheInputInvalid()
    {
        list( $parser, $xml ) = $this->parse( '<a href="javascript:alert(1)">s</a>' );
        $this->assertFalse( $parser->isValid() );
        $this->assertStringContainsString( 'Using scripts in links is not allowed', implode( "\n", $parser->getMessages() ) );
        $this->assertStringNotContainsString( 'javascript:', $xml );
    }

    public function testAnInvalidEmailAddressMakesTheInputInvalid()
    {
        list( $parser ) = $this->parse( '<a href="mailto:abc@x">m</a>' );
        $this->assertFalse( $parser->isValid() );
        $this->assertStringContainsString( 'abc@x', implode( "\n", $parser->getMessages() ) );
    }

    public function testALinkToAnAnchorKeepsTheInputValid()
    {
        list( $parser ) = $this->parse( '<a href="#top">t</a>' );
        $this->assertTrue( $parser->isValid() );
        $this->assertSame( array(), $parser->getMessages() );
    }
}
