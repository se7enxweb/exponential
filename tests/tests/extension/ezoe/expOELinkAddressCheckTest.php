<?php
/**
 * The online editor refuses a link that runs a script and a mailto: link with an invalid address, and accepts every
 * ordinary link and valid address, without the database: eZOEInputParser::isScriptURL(), invalidMailtoAddress(),
 * isValidMailAddress() and eZOEInputParser::process().
 *
 *  LA-01 - Script addresses in every spelling a browser follows: case, leading spaces and control characters, tabs
 *          and line breaks inside the scheme, numeric character references with and without ';', nested ones,
 *          HTML5 names (&colon; &Tab;), vbscript:, livescript:, data: with markup (text/html, image/svg+xml ...)
 *  LA-02 - No false positives: "javascript" in a path, query, anchor or file name, a relative path with a colon later
 *          on, data: images, other schemes
 *  LA-03 - Valid addresses pass: +tags, subdomains, internationalized domains (also with an xn-- top level domain),
 *          their ASCII form, several addresses, a query, percent-encoding, no address at all, any case of MAILTO:
 *  LA-04 - Invalid addresses are named in the message: no domain dot, two @, a leading dot, a space, one bad address of
 *          several
 *  LA-05 - Through the parser: the input is invalid, the link loses its address, the message names it; a link to an
 *          anchor keeps the input valid (a kept address is registered in the database, so it is not parsed here)
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expOELinkAddressCheckTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        if ( !class_exists( 'eZOEInputParser' ) )
            $this->markTestSkipped( 'ezoe is not loaded' );
    }

    public static function scriptAddresses()
    {
        return array(
            'plain'                     => array( 'javascript:alert(1)' ),
            'mixed case'                => array( 'JaVaScRiPt:alert(1)' ),
            'leading space'             => array( '  javascript:alert(1)' ),
            'leading control character' => array( "\x01\x1Fjavascript:alert(1)" ),
            'tab inside the scheme'     => array( "java\tscript:alert(1)" ),
            'line feed inside'          => array( "java\nscr\ript:alert(1)" ),
            'decimal reference'         => array( '&#106;avascript:alert(1)' ),
            'hex reference'             => array( '&#x6A;avascript:alert(1)' ),
            'upper hex reference'       => array( '&#X6a;avascript:alert(1)' ),
            'reference without ;'       => array( '&#106avascript:alert(1)' ),
            'zero padded reference'     => array( '&#0000106;avascript:alert(1)' ),
            'every letter a reference'  => array( '&#106;&#97;&#118;&#97;&#115;&#99;&#114;&#105;&#112;&#116;&#58;alert(1)' ),
            'named colon'               => array( 'javascript&colon;alert(1)' ),
            'named tab inside'          => array( 'java&Tab;script:alert(1)' ),
            'reference to a tab'        => array( 'java&#x09;script:alert(1)' ),
            'nested reference'          => array( '&amp;#106;avascript:alert(1)' ),
            'leading space reference'   => array( '&#32;javascript:alert(1)' ),
            'vbscript'                  => array( 'VBScript:msgbox(1)' ),
            'livescript'                => array( 'livescript:x' ),
            'data html'                 => array( 'data:text/html,<script>alert(1)</script>' ),
            'data html base64'          => array( 'data:text/html;base64,PHNjcmlwdD4=' ),
            'data html upper case'      => array( 'DATA:TEXT/HTML,x' ),
            'data svg'                  => array( 'data:image/svg+xml,<svg onload=alert(1)>' ),
            'data xhtml'                => array( 'data:application/xhtml+xml,x' ),
            'data without type'         => array( 'data:,alert(1)' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('scriptAddresses')]
    public function testAScriptAddressIsRecognized( $url )
    {
        $this->assertTrue( eZOEInputParser::isScriptURL( $url ), $url );
    }

    public static function ordinaryAddresses()
    {
        return array(
            'https'                       => array( 'https://example.com/' ),
            'javascript in the path'      => array( 'https://example.com/javascript:void' ),
            'javascript in the query'     => array( 'http://example.com/?q=javascript:alert(1)' ),
            'javascript as a file'        => array( 'javascript.html' ),
            'javascript in a folder name' => array( '/javascript-guide/intro' ),
            'relative path with a colon'  => array( './javascript:x' ),
            'anchor'                      => array( '#javascript' ),
            'space inside the scheme'     => array( 'java script:x' ),
            'percent-encoded scheme'      => array( '%6Aavascript:x' ),
            'mailto'                      => array( 'mailto:info@example.com' ),
            'tel'                         => array( 'tel:+4930123456' ),
            'ftp'                         => array( 'ftp://example.com/file.txt' ),
            'data png'                    => array( 'data:image/png;base64,iVBORw0KGgo=' ),
            'data jpeg'                   => array( 'data:image/jpeg;base64,/9j/4AAQ' ),
            'ampersand entity'            => array( 'https://example.com/?a=1&amp;b=2' ),
            'eznode'                      => array( 'eznode://42' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('ordinaryAddresses')]
    public function testAnOrdinaryAddressIsNoScript( $url )
    {
        $this->assertFalse( eZOEInputParser::isScriptURL( $url ), $url );
    }

    public static function validMailtoLinks()
    {
        return array(
            'plain'                    => array( 'mailto:info@example.com' ),
            'plus tag'                 => array( 'mailto:first.last+news@example.com' ),
            'subdomains'               => array( 'mailto:team@mail.eu.example.co.uk' ),
            'idn domain'               => array( 'mailto:kontakt@bücher.de' ),
            'idn domain ascii form'    => array( 'mailto:kontakt@xn--bcher-kva.de' ),
            'idn top level domain'     => array( 'mailto:info@пример.рф' ),
            'idn tld ascii form'       => array( 'mailto:info@xn--e1afmkfd.xn--p1ai' ),
            'percent-encoded idn'      => array( 'mailto:kontakt@b%C3%BCcher.de' ),
            'percent-encoded plus'     => array( 'mailto:first%2Btag@example.com' ),
            'several addresses'        => array( 'mailto:a@example.com,b@example.org' ),
            'with a query'             => array( 'mailto:info@example.com?subject=Hello%20there&body=x' ),
            'no address, only a query' => array( 'mailto:?to=info@example.com&subject=x' ),
            'upper case scheme'        => array( 'MAILTO:INFO@EXAMPLE.COM' ),
            'digits and hyphens'       => array( 'mailto:user-1@ex-ample1.com' ),
            'not a mailto link'        => array( 'https://example.com/a@b' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('validMailtoLinks')]
    public function testAValidAddressPasses( $url )
    {
        $this->assertNull( eZOEInputParser::invalidMailtoAddress( $url ), $url );
    }

    public static function invalidMailtoLinks()
    {
        return array(
            'no dot in the domain' => array( 'mailto:abc@x', 'abc@x' ),
            'two at signs'         => array( 'mailto:a@@example.com', 'a@@example.com' ),
            'no at sign'           => array( 'mailto:info.example.com', 'info.example.com' ),
            'leading dot'          => array( 'mailto:.a@example.com', '.a@example.com' ),
            'space'                => array( 'mailto:a b@example.com', 'a b@example.com' ),
            'empty local part'     => array( 'mailto:@example.com', '@example.com' ),
            'one bad of several'   => array( 'mailto:a@example.com,bad', 'bad' ),
            'bad idn'              => array( 'mailto:a@bü--.de', 'a@bü--.de' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('invalidMailtoLinks')]
    public function testAnInvalidAddressIsNamed( $url, $address )
    {
        $this->assertSame( $address, eZOEInputParser::invalidMailtoAddress( $url ) );
    }

    /**
     * @return array the parser, its messages and the stored XML for $html
     */
    private function parse( $html )
    {
        $parser = new eZOEInputParser();
        $doc = $parser->process( '<p>' . $html . '</p>' );
        return array( $parser, implode( "\n", $parser->getMessages() ), $doc ? eZXMLTextType::domString( $doc ) : '' );
    }

    public function testTheParserRefusesAndKeeps()
    {
        foreach ( array( '&amp;#106;avascript:alert(1)', 'java&amp;Tab;script:alert(1)', ' JavaScript:alert(1)',
                         'data:text/html,x' ) as $href )
        {
            list( $parser, $messages, $xml ) = $this->parse( '<a href="' . $href . '">s</a>' );
            $this->assertFalse( $parser->isValid(), $href );
            $this->assertStringContainsString( 'Using scripts in links is not allowed', $messages, $href );
            $this->assertStringNotContainsString( 'url_id', $xml, "$href: the link keeps no address" );
            $this->assertStringNotContainsString( 'anchor_name', $xml, "$href: nor an anchor cut from it" );
        }

        list( $parser, $messages ) = $this->parse( '<a href="mailto:abc@x">m</a>' );
        $this->assertFalse( $parser->isValid() );
        $this->assertStringContainsString( "Invalid e-mail address: 'abc@x'", $messages );

        // an address the parser keeps is registered as a URL, which needs the database: the anchor only here, the
        // valid addresses above through invalidMailtoAddress()
        foreach ( array( '#top' ) as $href )
        {
            list( $parser, $messages ) = $this->parse( '<a href="' . $href . '">ok</a>' );
            $this->assertTrue( $parser->isValid(), $href );
            $this->assertSame( '', $messages, $href );
        }
    }
}
