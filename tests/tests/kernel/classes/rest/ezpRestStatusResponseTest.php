<?php
/**
 * Tests of ezpRestStatusResponse::process(): the status line with its reason phrase, extra headers (Allow for a
 * refused method), a message as a JSON body, a code without a phrase in the table, and a writer that is not an HTTP
 * writer. The response writer is ezpRestHttpResponseWriter, the one the REST kernel uses, on an ezcMvcResponse built by the test; nothing is sent.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group rest
 */

class ezpRestStatusResponseTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        if ( !class_exists( 'ezcMvcHttpResponseWriter' ) )
            $this->markTestSkipped( 'the MVC component of the Zeta Components is not installed' );
    }

    private static function writer()
    {
        $writer = new ezpRestHttpResponseWriter( new ezcMvcResponse() );
        $writer->headers = array();
        return $writer;
    }

    public static function codeProvider()
    {
        return array(
            array( 200, 'OK' ), array( 201, 'Created' ), array( 404, 'Not Found' ),
            array( 405, 'Method Not Allowed' ), array( 500, 'Internal Server Error' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('codeProvider')]
    public function testStatusLine( $code, $phrase )
    {
        $writer = self::writer();
        ( new ezpRestStatusResponse( $code ) )->process( $writer );
        $this->assertSame( $phrase, $writer->headers["HTTP/1.1 $code"] );
        $this->assertArrayNotHasKey( 'Content-Type', $writer->headers );
    }

    public function testHeadersAndJsonMessage()
    {
        $writer = self::writer();
        $status = new ezpRestStatusResponse( 405, 'Use GET', array( 'Allow' => 'GET, OPTIONS' ) );
        $status->process( $writer );
        $this->assertSame( 'GET, OPTIONS', $writer->headers['Allow'] );
        $this->assertSame( 'application/json; charset=UTF-8', $writer->headers['Content-Type'] );
        $this->assertSame( '"Use GET"', $writer->response->body );
    }

    public function testCodeWithoutAPhrase()
    {
        $writer = self::writer();
        ( new ezpRestStatusResponse( 422, array( 'error' => 'bad input' ) ) )->process( $writer );
        $this->assertSame( '', $writer->headers['HTTP/1.1 422'] );
        $this->assertSame( '{"error":"bad input"}', $writer->response->body );
    }
}
