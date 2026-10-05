<?php
/**
 * The SOAP messages of lib/ezsoap, without a network:
 *   - eZSOAPCodec::encodeValue(): string, boolean, int, float, list (SOAP-ENC:Array), hash (SOAPStruct), nesting,
 *     a type it cannot encode
 *   - eZSOAPRequest: name, namespace, parameters from the constructor and addParameter(), body attributes, the
 *     payload envelope
 *   - eZSOAPResponse: payload() and decodeStream() round trip for every type, a null value, a fault (payload and
 *     decoding), a response with an HTTP header before the XML, a response without a namespace, elements without
 *     xsi:type (document style), invalid XML
 *   - eZSOAPParameter, eZSOAPFault, eZSOAPHeader, eZSOAPEnvelope
 *
 * No network, no database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezsoap
 */

class eZSOAPMessageTest extends PHPUnit\Framework\TestCase
{
    const NS = 'urn:example-service';

    private function encode( $value )
    {
        $doc = new DOMDocument( '1.0', 'utf-8' );
        $node = eZSOAPCodec::encodeValue( $doc, 'v', $value );
        if ( $node === false )
            return false;
        $doc->appendChild( $node );
        return $doc->saveXML( $node );
    }

    public function testEncodeScalars()
    {
        $this->assertSame( '<v xsi:type="xsd:string">a &amp; b</v>', $this->encode( 'a & b' ) );
        $this->assertSame( '<v xsi:type="xsd:boolean">true</v>', $this->encode( true ) );
        $this->assertSame( '<v xsi:type="xsd:boolean">false</v>', $this->encode( false ) );
        $this->assertSame( '<v xsi:type="xsd:int">42</v>', $this->encode( 42 ) );
        $this->assertSame( '<v xsi:type="xsd:float">1.5</v>', $this->encode( 1.5 ) );
        $this->assertFalse( $this->encode( null ) );
        $this->assertFalse( $this->encode( new stdClass() ) );
    }

    public function testEncodeListAndStruct()
    {
        $this->assertSame(
            '<v xsi:type="SOAP-ENC:Array" SOAP-ENC:arrayType="xsd:string[2]"><item xsi:type="xsd:string">a</item><item xsi:type="xsd:int">2</item></v>',
            $this->encode( array( 'a', 2 ) )
        );
        $this->assertSame(
            '<v xsi:type="SOAP-ENC:SOAPStruct"><x xsi:type="xsd:int">1</x><y xsi:type="xsd:string">b</y></v>',
            $this->encode( array( 'x' => 1, 'y' => 'b' ) )
        );
    }

    private function roundTrip( $value, $name = 'getThing', $namespace = self::NS )
    {
        $response = new eZSOAPResponse( $name, $namespace );
        $response->setValue( $value );
        $xml = $response->payload();
        $decoded = new eZSOAPResponse();
        $decoded->decodeStream( new eZSOAPRequest( $name, $namespace ), $xml );
        return $decoded;
    }

    public static function roundTripProvider()
    {
        return array(
            'string'        => array( 'hello <world>', 'hello <world>' ),
            'empty string'  => array( '', '' ),
            'multi-byte'    => array( 'grüße', 'grüße' ),
            'int'           => array( 7, '7' ),
            'float'         => array( 2.5, '2.5' ),
            'true'          => array( true, true ),
            'false'         => array( false, false ),
            'list'          => array( array( 'a', 'b', 'c' ), array( 'a', 'b', 'c' ) ),
            'struct'        => array( array( 'id' => 3, 'name' => 'x' ), array( 'id' => '3', 'name' => 'x' ) ),
            'nested'        => array( array( 'items' => array( 'p', 'q' ), 'meta' => array( 'ok' => true ) ),
                                      array( 'items' => array( 'p', 'q' ), 'meta' => array( 'ok' => true ) ) ),
            'empty list'    => array( array(), array() ),
        );
    }

    /**
     * Numbers come back as their text: the decoder returns the element content of int and float values.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('roundTripProvider')]
    public function testRoundTrip( $value, $expected )
    {
        $decoded = $this->roundTrip( $value );
        $this->assertFalse( (bool)$decoded->isFault() );
        $this->assertSame( $expected, $decoded->value() );
    }

    /**
     * A null value is written as an empty xsi:nil element and read back as null. It made payload() fail: the codec
     * has no encoding for null and appendChild() was given false.
     */
    public function testNullRoundTrip()
    {
        $response = new eZSOAPResponse( 'getThing', self::NS );
        $response->setValue( null );
        $xml = $response->payload();
        $this->assertStringContainsString( 'xsi:nil="true"', $xml );
        $this->assertNull( $this->roundTrip( null )->value() );
    }

    public function testRoundTripWithoutANamespace()
    {
        $this->assertSame( 'plain', $this->roundTrip( 'plain', 'ping', '' )->value() );
    }

    public function testFault()
    {
        $response = new eZSOAPResponse( 'getThing', self::NS );
        $response->setValue( new eZSOAPFault( 'Server', 'Something broke' ) );
        $xml = $response->payload();
        $this->assertStringContainsString( '<faultcode>Server</faultcode>', $xml );
        $this->assertStringContainsString( '<faultstring>Something broke</faultstring>', $xml );

        $decoded = new eZSOAPResponse();
        $decoded->decodeStream( new eZSOAPRequest( 'getThing', self::NS ), $xml );
        $this->assertTrue( (bool)$decoded->isFault() );
        $this->assertSame( 'Server', $decoded->faultCode() );
        $this->assertSame( 'Something broke', $decoded->faultString() );
    }

    public function testHttpHeaderBeforeTheXmlIsStripped()
    {
        $response = new eZSOAPResponse( 'getThing', self::NS );
        $response->setValue( 'body' );
        $raw = "HTTP/1.1 200 OK\r\nContent-Type: text/xml\r\n\r\n" . $response->payload();
        $decoded = new eZSOAPResponse();
        $decoded->decodeStream( new eZSOAPRequest( 'getThing', self::NS ), $raw );
        $this->assertSame( 'body', $decoded->value() );
        $this->assertStringStartsWith( '<?xml', $decoded->stripHTTPHeader( $raw ) );
    }

    /**
     * Document style services send elements without xsi:type. Decoding them failed on the missing attribute.
     */
    public function testUntypedElements()
    {
        $xml = '<?xml version="1.0"?>' .
            '<E:Envelope xmlns:E="http://schemas.xmlsoap.org/soap/envelope/"><E:Body>' .
            '<m:getThingResponse xmlns:m="' . self::NS . '"><return><id>5</id><name>five</name></return></m:getThingResponse>' .
            '</E:Body></E:Envelope>';
        $decoded = new eZSOAPResponse();
        $decoded->decodeStream( new eZSOAPRequest( 'getThing', self::NS ), $xml );
        $this->assertSame( array( 'id' => '5', 'name' => 'five' ), $decoded->value() );

        $text = new DOMDocument();
        $text->loadXML( '<return>just text</return>' );
        $this->assertSame( 'just text', eZSOAPResponse::decodeDataTypes( $text->documentElement ) );
    }

    public function testBase64AndOldSchemaNamespace()
    {
        $doc = new DOMDocument();
        $doc->loadXML( '<r xmlns:xsi="http://www.w3.org/1999/XMLSchema-instance" xsi:type="xsd:base64">' . base64_encode( "bin\0ary" ) . '</r>' );
        $this->assertSame( "bin\0ary", eZSOAPResponse::decodeDataTypes( $doc->documentElement ) );
    }

    public function testInvalidXmlLeavesTheValueUnset()
    {
        $decoded = new eZSOAPResponse();
        @$decoded->decodeStream( new eZSOAPRequest( 'getThing', self::NS ), '<?xml version="1.0"?><broken' );
        $this->assertFalse( $decoded->value() );
        $this->assertFalse( (bool)$decoded->isFault() );
    }

    public function testRequestPayload()
    {
        $request = new eZSOAPRequest( 'addNumbers', self::NS, array( 'a' => 1, 'b' => 2 ) );
        $request->addParameter( 'label', 'sum' );
        $request->addBodyAttribute( 'encodingStyle', eZSOAPEnvelope::ENC, eZSOAPEnvelope::ENV_PREFIX );
        $this->assertSame( 'addNumbers', $request->name() );
        $this->assertSame( self::NS, $request->ns() );
        $this->assertCount( 3, $request->Parameters );
        $this->assertSame( 'label', $request->Parameters[2]->name() );

        $doc = new DOMDocument();
        $this->assertTrue( $doc->loadXML( $request->payload() ) );
        $this->assertSame( eZSOAPEnvelope::ENV, $doc->documentElement->namespaceURI );
        $this->assertSame( 'Envelope', $doc->documentElement->localName );
        $body = $doc->getElementsByTagNameNS( eZSOAPEnvelope::ENV, 'Body' )->item( 0 );
        $this->assertSame( eZSOAPEnvelope::ENC, $body->getAttribute( 'SOAP-ENV:encodingStyle' ) );
        $call = $doc->getElementsByTagNameNS( self::NS, 'addNumbers' )->item( 0 );
        $this->assertNotNull( $call );
        $this->assertSame( '1', $call->getElementsByTagName( 'a' )->item( 0 )->textContent );
        $this->assertSame( 'sum', $call->getElementsByTagName( 'label' )->item( 0 )->textContent );
    }

    public function testSmallValueClasses()
    {
        $p = new eZSOAPParameter( 'n', 1 );
        $p->setName( 'm' );
        $p->setValue( 2 );
        $this->assertSame( 'm', $p->name() );
        $this->assertSame( 2, $p->value() );

        $f = new eZSOAPFault( 'Client', 'bad input' );
        $this->assertSame( 'Client', $f->faultCode() );
        $this->assertSame( 'bad input', $f->faultString() );

        $h = new eZSOAPHeader();
        $h->addHeader( 'X-Test', 'yes' );
        $this->assertSame( array( 'X-Test' => 'yes' ), $h->Headers );

        $e = new eZSOAPEnvelope();
        $this->assertInstanceOf( eZSOAPHeader::class, $e->Header );
        $this->assertInstanceOf( eZSOAPBody::class, $e->Body );
    }
}
