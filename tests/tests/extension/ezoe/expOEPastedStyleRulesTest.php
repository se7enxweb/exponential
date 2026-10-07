<?php
/**
 * Styles pasted into the editor become attributes only where they can, without the database:
 * eZOEInputParser::elementStylesToAttribute() through eZOEInputParser::process().
 *
 *  PS-01 - A declaration without a colon, or with an empty name, is left out without a PHP warning
 *  PS-02 - A namespace declaration (xmlns, xmlns:x, xml:...) never reaches the stored XML, which loads again
 *  PS-03 - An alignment is a keyword; markup, a script address, an expression or a second colon is left out
 *  PS-04 - An alignment keeps its keyword in lower case, also with !important
 *  PS-05 - Names that are not CSS property names (vendor prefixes, other scripts, @rules) are left out
 *  PS-06 - Very long declarations do not break the parser
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expOEPastedStyleRulesTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        if ( !class_exists( 'eZOEInputParser' ) )
            $this->markTestSkipped( 'ezoe is not loaded' );
    }

    /**
     * Parses $html and returns the stored XML without its declaration and section element; every PHP warning or
     * notice raised meanwhile, also when the XML is loaded again, fails the test.
     */
    private function xml( $html )
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
            $xml = $doc ? eZXMLTextType::domString( $doc ) : '';
            $check = new DOMDocument();
            $loaded = $xml !== '' && $check->loadXML( $xml );
        }
        finally
        {
            restore_error_handler();
        }
        $this->assertSame( array(), $warnings, 'no PHP warning for: ' . substr( $html, 0, 200 ) );
        $this->assertTrue( $loaded, 'the stored XML loads again' );
        return preg_replace( '/^<\?xml[^>]*\?>\s*<section[^>]*>|<\/section>\s*$/', '', $xml );
    }

    /** PS-01 */
    public function testADeclarationWithoutColonOrNameIsLeftOut()
    {
        $this->assertSame( '<paragraph align="right">a</paragraph>', $this->xml( '<p style="color; text-align: right">a</p>' ) );
        $this->assertSame( '<paragraph align="left">a</paragraph>', $this->xml( '<p style=": x; ;;; text-align: left">a</p>' ) );
    }

    /** PS-02 */
    public function testANamespaceDeclarationNeverReachesTheStoredXml()
    {
        foreach ( array( 'xmlns: http://example.invalid/', 'xmlns:foo: bar', 'XMLNS: x', 'xml:lang: en', 'xmlns : y' ) as $declaration )
        {
            $xml = $this->xml( '<p style="' . $declaration . '; text-align: center">a</p>' );
            $this->assertSame( '<paragraph align="center">a</paragraph>', $xml, $declaration );
        }
    }

    /** PS-03 */
    public function testAnAlignmentIsAKeyword()
    {
        foreach ( array( '&quot;&gt;&lt;script&gt;x&lt;/script&gt;', '&quot; onmouseover=&quot;x', 'javascript:alert(1)',
                         'expression(alert(1))', ': right', 'right:left', 'url(http://example.invalid/a)', 'r&#xE9;ght' ) as $value )
        {
            $this->assertSame( '<paragraph>a</paragraph>', $this->xml( '<p style="text-align: ' . $value . '">a</p>' ), $value );
            $this->assertSame( '<paragraph>a</paragraph>', $this->xml( '<p style="float: ' . $value . '">a</p>' ), $value );
        }
    }

    /** PS-04 */
    public function testAnAlignmentKeepsItsKeyword()
    {
        $this->assertSame( '<paragraph align="center">a</paragraph>', $this->xml( '<p style="TEXT-ALIGN: Center">a</p>' ) );
        $this->assertSame( '<paragraph align="justify">a</paragraph>', $this->xml( '<p style="text-align: justify !important">a</p>' ) );
        $this->assertSame( '<header align="right">a</header>',
                           preg_replace( '/^(<section>)+|(<\/section>)+$/', '', $this->xml( '<h2 style="text-align: right">a</h2>' ) ) );
    }

    /** PS-05 */
    public function testNamesThatAreNotPropertyNamesAreLeftOut()
    {
        $this->assertSame( '<paragraph align="left">a</paragraph>',
                           $this->xml( '<p style="@font-face: x; -webkit-text-size-adjust: 100%; t&#xEB;xt-align: right; text-align: left">a</p>' ) );
    }

    /** PS-06 */
    public function testVeryLongDeclarations()
    {
        $this->assertSame( '<paragraph align="right">a</paragraph>',
                           $this->xml( '<p style="' . str_repeat( 'a:b;', 20000 ) . 'text-align: right">a</p>' ) );
        $this->assertSame( '<paragraph>a</paragraph>', $this->xml( '<p style="text-align: ' . str_repeat( 'x:', 50000 ) . '">a</p>' ) );
    }
}
