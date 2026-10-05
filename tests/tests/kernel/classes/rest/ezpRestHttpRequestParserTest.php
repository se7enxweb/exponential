<?php
/**
 * Tests of the REST request parser (ezpRestHttpRequestParser::createRequest()): the HTTP method as protocol, the
 * _method override of POST for PUT and DELETE, ResponseGroups, Translation and OutputFormat taken out of the query
 * into the request's own variables, the remaining GET and POST data and the request date.
 *
 * $_GET, $_POST and $_SERVER are set by the test and put back. No database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group rest
 */

class ezpRestHttpRequestParserTest extends PHPUnit\Framework\TestCase
{
    private $saved;

    protected function setUp(): void
    {
        if ( !class_exists( 'ezcMvcHttpRequestParser' ) )
            $this->markTestSkipped( 'the MVC component of the Zeta Components is not installed' );
        $this->saved = array( $_GET, $_POST, $_SERVER );
        $_GET = array();
        $_POST = array();
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/api/ezp/v1/content/node/2';
        $_SERVER['HTTP_HOST'] = 't1.example.invalid';
        $_SERVER['SERVER_NAME'] = 't1.example.invalid';
        $_SERVER['REQUEST_TIME'] = 1700000000;
        unset( $_SERVER['HTTPS'] );
    }

    protected function tearDown(): void
    {
        if ( $this->saved !== null )
            list( $_GET, $_POST, $_SERVER ) = $this->saved;
    }

    private static function parse()
    {
        $parser = new ezpRestHttpRequestParser();
        return $parser->createRequest();
    }

    public function testGetRequest()
    {
        $_GET = array( 'ResponseGroups' => 'Metadata,Fields', 'Translation' => 'eng-GB', 'limit' => '10' );
        $request = self::parse();
        $this->assertInstanceOf( 'ezpRestRequest', $request );
        $this->assertSame( 'http-get', $request->protocol );
        $this->assertSame( array( 'Metadata', 'Fields' ), $request->variables['ResponseGroups'] );
        $this->assertSame( 'eng-GB', $request->contentVariables['Translation'] );
        $this->assertNull( $request->contentVariables['OutputFormat'] );
        $this->assertSame( array( 'limit' => '10' ), $request->get );
        $this->assertSame( 1700000000, $request->date->getTimestamp() );
    }

    public function testWithoutOwnVariables()
    {
        $request = self::parse();
        $this->assertSame( array(), $request->variables['ResponseGroups'] );
        $this->assertNull( $request->contentVariables['Translation'] );
    }

    public static function methodProvider()
    {
        return array(
            'post'                 => array( 'POST', array(), 'http-post', 'http-post' ),
            'post as put'          => array( 'POST', array( '_method' => 'PUT' ), 'http-put', 'http-post' ),
            'post as delete'       => array( 'POST', array( '_method' => 'delete' ), 'http-delete', 'http-post' ),
            'post as get ignored'  => array( 'POST', array( '_method' => 'GET' ), 'http-post', 'http-post' ),
            'get ignores _method'  => array( 'GET', array( '_method' => 'DELETE' ), 'http-get', 'http-get' ),
            'delete'               => array( 'DELETE', array(), 'http-delete', 'http-delete' ),
            'post with an array'   => array( 'POST', array( '_method' => array( 'PUT' ) ), 'http-post', 'http-post' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('methodProvider')]
    public function testProtocol( $method, $post, $protocol, $originalProtocol )
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        $_POST = $post + array( 'title' => 'x' );
        $request = self::parse();
        $this->assertSame( $protocol, $request->protocol );
        $this->assertSame( $originalProtocol, $request->originalProtocol );
        if ( $method === 'POST' )
            $this->assertArrayNotHasKey( '_method', $request->post );
        $this->assertSame( 'x', $request->post['title'] );
    }

    public function testResponseGroupsPostedAsAList()
    {
        $_GET = array( 'ResponseGroups' => array( 'Metadata', 'Fields' ) );
        $request = self::parse();
        $this->assertSame( array( 'Metadata', 'Fields' ), $request->variables['ResponseGroups'] );
    }

    public function testEncryptedOverHttps()
    {
        $_SERVER['HTTPS'] = 'on';
        $this->assertTrue( self::parse()->isEncrypted );
    }
}
