<?php
/**
 * ezpRestRequest: the constructor and __set_state() (a request exported with var_export() and read back is the
 * same request), the host and base URI, the content query string, and the parsed body of POST, PUT and other
 * methods by content type.
 *
 * No database. The API provider name of the prefix filter is set by the test and put back.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group rest
 */

class ezpRestRequestTest extends PHPUnit\Framework\TestCase
{
    private $provider;

    protected function setUp(): void
    {
        if ( !class_exists( 'ezcMvcRequest' ) )
            $this->markTestSkipped( 'the MVC component of the Zeta Components is not installed' );
        chdir( dirname( __DIR__, 5 ) );
        $property = new ReflectionProperty( 'ezpRestPrefixFilterInterface', 'apiProvider' );
        $this->provider = $property->getValue();
    }

    protected function tearDown(): void
    {
        if ( class_exists( 'ezpRestPrefixFilterInterface' ) )
            ( new ReflectionProperty( 'ezpRestPrefixFilterInterface', 'apiProvider' ) )->setValue( null, $this->provider );
    }

    private static function full( $isFatal = false )
    {
        return new ezpRestRequest( new DateTime( '2026-10-05 12:00:00' ), 'http-put', 'api.k1.example.invalid', '/api/content/node/2',
            'req-1', 'https://ref.k1.example.invalid/', array( 'nodeId' => '2' ), array( 'g' => '1' ), array( 'p' => '2' ),
            array( 'Translation' => 'eng-GB' ), true, '{"a":1}', array(), null, null, null, array( 'CONTENT_TYPE' => 'application/json' ),
            array( 'c' => 'v' ), $isFatal, 'http-post' );
    }

    public function testConstructorKeepsEveryValue()
    {
        $request = self::full( true );
        $this->assertSame( 'http-put', $request->protocol );
        $this->assertSame( 'http-post', $request->originalProtocol );
        $this->assertSame( 'api.k1.example.invalid', $request->host );
        $this->assertSame( '/api/content/node/2', $request->uri );
        $this->assertSame( 'req-1', $request->requestId );
        $this->assertSame( array( 'g' => '1' ), $request->get );
        $this->assertSame( array( 'p' => '2' ), $request->post );
        $this->assertSame( array( 'Translation' => 'eng-GB' ), $request->contentVariables );
        $this->assertTrue( $request->isEncrypted );
        $this->assertSame( '{"a":1}', $request->body );
        $this->assertSame( array( 'c' => 'v' ), $request->cookies );
        $this->assertTrue( $request->isFatal );
    }

    public function testDefaults()
    {
        $request = new ezpRestRequest();
        $this->assertSame( '', $request->protocol );
        $this->assertSame( '', $request->originalProtocol, 'the original protocol is the protocol when not given' );
        $this->assertSame( array(), $request->variables );
        $this->assertFalse( $request->isEncrypted );
        $this->assertFalse( $request->isFatal );
    }

    public function testExportedRequestIsReadBackUnchanged()
    {
        foreach ( array( false, true ) as $isFatal )
        {
            $request = self::full( $isFatal );
            $copy = ezpRestRequest::__set_state( get_object_vars( $request ) );
            $this->assertEquals( $request, $copy );
            $this->assertSame( $isFatal, $copy->isFatal );
        }
    }

    public function testHostUri()
    {
        $request = self::full();
        $this->assertSame( 'http://api.k1.example.invalid', $request->getHostURI() );
        $request->protocol = 'https-get';
        $this->assertSame( 'https://api.k1.example.invalid', $request->getHostURI() );
    }

    public function testBaseUriPutsTheProviderAfterThePrefix()
    {
        $prefix = eZINI::instance( 'rest.ini' )->variable( 'System', 'ApiPrefix' );
        ( new ReflectionProperty( 'ezpRestPrefixFilterInterface', 'apiProvider' ) )->setValue( null, 'k1prov' );
        $request = new ezpRestRequest( null, 'http-get', 'api.k1.example.invalid', $prefix . '/content/node/2' );
        $this->assertSame( 'http://api.k1.example.invalid' . $prefix . '/k1prov/content/node/2', $request->getBaseURI() );
    }

    public function testContentQueryString()
    {
        $request = new ezpRestRequest();
        $request->contentVariables = array( 'Translation' => 'eng-GB', 'OutputFormat' => null, 'Limit' => 10 );
        $this->assertSame( 'Translation=eng-GB&Limit=10', $request->getContentQueryString() );
        $this->assertSame( '?Translation=eng-GB&Limit=10', $request->getContentQueryString( true ) );
        $request->contentVariables = array( 'OutputFormat' => null );
        $this->assertSame( '', $request->getContentQueryString( true ) );
    }

    private static function bodyRequest( $protocol, $contentType, $body, array $post = array() )
    {
        $raw = $contentType === null ? array() : array( 'CONTENT_TYPE' => $contentType );
        return new ezpRestRequest( null, $protocol, '', '', '', '', array(), array(), $post, array(), false, $body, null, null, null, null, $raw );
    }

    public function testPostBody()
    {
        $this->assertSame( array( 'a' => 1 ), self::bodyRequest( 'http-post', 'application/json; charset=utf-8', '{"a":1}' )->getParsedBody() );
        $this->assertSame( array( 'f' => 'v' ), self::bodyRequest( 'http-post', 'application/x-www-form-urlencoded', 'f=other', array( 'f' => 'v' ) )->getParsedBody() );
    }

    public function testOtherMethodsParseTheBody()
    {
        $this->assertNull( self::bodyRequest( 'http-put', null, 'x=1' )->getParsedBody() );
        $this->assertSame( array(), self::bodyRequest( 'http-put', 'application/json', '' )->getParsedBody() );
        $this->assertSame( array( 'x' => '1', 'y' => array( 'a', 'b' ) ),
                           self::bodyRequest( 'http-put', 'application/x-www-form-urlencoded', 'x=1&y[]=a&y[]=b' )->getParsedBody() );
        $this->assertSame( array( 'n' => array( 1, 2 ) ), self::bodyRequest( 'http-patch', 'application/json', '{"n":[1,2]}' )->getParsedBody() );
        $this->assertNull( self::bodyRequest( 'http-put', 'text/plain', 'hello' )->getParsedBody() );
    }
}
