<?php
/**
 * eZSOAPServer::processRequest() with a request for a plain function (no "Class::method"):
 * the name has no second part, and splitting it must not raise "Undefined array key 1"
 * (seen 49 times in the web server's error log). The registered function is still called.
 *
 * No network, no database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezsoap
 */

function ezsoapServerFunctionNameTestEcho( $value )
{
    return 'echo:' . $value;
}

class eZSOAPServerFunctionNameTestServer extends eZSOAPServer
{
    public $responses = array();

    public function __construct( $rawPostData )
    {
        $this->RawPostData = $rawPostData;
    }

    function showResponse( $functionName, $namespaceURI, $value )
    {
        $this->responses[] = array( $functionName, $namespaceURI, $value );
    }
}

class eZSOAPServerFunctionNameTest extends PHPUnit\Framework\TestCase
{
    private $method;

    protected function setUp(): void
    {
        parent::setUp();
        $this->method = $_SERVER['REQUEST_METHOD'] ?? null;
        $_SERVER['REQUEST_METHOD'] = 'POST';
    }

    protected function tearDown(): void
    {
        if ( $this->method === null )
            unset( $_SERVER['REQUEST_METHOD'] );
        else
            $_SERVER['REQUEST_METHOD'] = $this->method;
        parent::tearDown();
    }

    private function envelope( $function )
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"'
            . ' xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'
            . ' xmlns:ns="urn:bughunt"><SOAP-ENV:Body><ns:' . $function . '>'
            . '<value xsi:type="xsd:string">x</value></ns:' . $function . '></SOAP-ENV:Body></SOAP-ENV:Envelope>';
    }

    private function process( eZSOAPServer $server )
    {
        $warnings = array();
        set_error_handler( function ( $no, $str, $file, $line ) use ( &$warnings ) {
            $warnings[] = "$str ($file:$line)";
            return true;
        } );
        try
        {
            $server->processRequest();
        }
        finally
        {
            restore_error_handler();
        }
        return $warnings;
    }

    public function testPlainFunctionIsCalledWithoutAWarning()
    {
        $server = new eZSOAPServerFunctionNameTestServer( $this->envelope( 'ezsoapServerFunctionNameTestEcho' ) );
        $server->registerFunction( 'ezsoapServerFunctionNameTestEcho' );

        $warnings = $this->process( $server );

        $this->assertSame( array(), $warnings );
        $this->assertCount( 1, $server->responses );
        $this->assertSame( 'echo:x', $server->responses[0][2] );
    }

    public function testUnknownPlainFunctionIsAFaultWithoutAWarning()
    {
        $server = new eZSOAPServerFunctionNameTestServer( $this->envelope( 'notRegisteredAnywhere' ) );

        $warnings = $this->process( $server );

        $this->assertSame( array(), $warnings );
        $this->assertCount( 1, $server->responses );
        $this->assertInstanceOf( 'eZSOAPFault', $server->responses[0][2] );
    }
}
