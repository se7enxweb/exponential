<?php
/**
 * The REST error answers: the result status objects (ezpRestHttpResponse, ezpOauthRequired with its
 * WWW-Authenticate header, ezpRestOauthErrorStatus) as they write the status line, headers and body; the OAuth error
 * types and their HTTP codes; the REST and OAuth exceptions; and ezpRestErrorController, which turns each kind of
 * exception into the right status.
 *
 * No database. The response writer is ezpRestHttpResponseWriter on an ezcMvcResponse; nothing is sent.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group rest
 */

class ezpRestStatusAndErrorTestPlainWriter extends ezcMvcResponseWriter
{
    public $headers = array();
    public $response;

    public function __construct( ezcMvcResponse $response )
    {
        $this->response = $response;
    }

    public function handleResponse()
    {
    }
}

class ezpRestStatusAndErrorTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        if ( !class_exists( 'ezcMvcHttpResponseWriter' ) )
            $this->markTestSkipped( 'the MVC component of the Zeta Components is not installed' );
        chdir( dirname( __DIR__, 5 ) );
    }

    private static function writer()
    {
        $writer = new ezpRestHttpResponseWriter( new ezcMvcResponse() );
        $writer->headers = array();
        return $writer;
    }

    // ---------------------------------------------------------------- ezpRestHttpResponse

    public function testHttpResponseWithMessage()
    {
        $writer = self::writer();
        ( new ezpRestHttpResponse( 404, 'Not Found' ) )->process( $writer );
        $this->assertSame( 'Not Found', $writer->headers['HTTP/1.1 404'] );
        $this->assertSame( 'application/json; charset=UTF-8', $writer->headers['Content-Type'] );
        $this->assertSame( '{"error_message":"Not Found"}', $writer->response->body );
    }

    public function testHttpResponseWithoutMessageHasNoBody()
    {
        $writer = self::writer();
        ( new ezpRestHttpResponse( 204 ) )->process( $writer );
        $this->assertArrayHasKey( 'HTTP/1.1 204', $writer->headers );
        $this->assertArrayNotHasKey( 'Content-Type', $writer->headers );
        $this->assertSame( "", (string)$writer->response->body );
    }

    public function testHttpResponseOnANonHttpWriterOnlySetsTheBody()
    {
        $writer = new ezpRestStatusAndErrorTestPlainWriter( new ezcMvcResponse() );
        ( new ezpRestHttpResponse( 500, 'boom' ) )->process( $writer );
        $this->assertSame( array( 'Content-Type' => 'application/json; charset=UTF-8' ), $writer->headers );
        $this->assertSame( '{"error_message":"boom"}', $writer->response->body );
    }

    // ---------------------------------------------------------------- OAuth

    public static function oauthErrorCodeProvider()
    {
        return array(
            array( ezpOauthErrorType::INVALID_REQUEST, 400 ),
            array( ezpOauthErrorType::INVALID_TOKEN, 401 ),
            array( ezpOauthErrorType::EXPIRED_TOKEN, 401 ),
            array( ezpOauthErrorType::INSUFFICIENT_SCOPE, 403 ),
            array( 'something_else', 500 ),
            array( null, 500 ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('oauthErrorCodeProvider')]
    public function testOauthErrorCodes( $type, $code )
    {
        $this->assertSame( $code, ezpOauthErrorType::httpCodeforError( $type ) );
    }

    public static function tokenEndpointErrorProvider()
    {
        return array(
            array( ezpOauthTokenEndpointErrorType::UNAUTHORIZED_CLIENT, 401 ),
            array( ezpOauthTokenEndpointErrorType::INVALID_REQUEST, 400 ),
            array( ezpOauthTokenEndpointErrorType::INVALID_CLIENT, 400 ),
            array( ezpOauthTokenEndpointErrorType::INVALID_GRANT, 400 ),
            array( ezpOauthTokenEndpointErrorType::UNSUPPORTED_GRANT_TYPE, 400 ),
            array( ezpOauthTokenEndpointErrorType::INVALID_SCOPE, 400 ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('tokenEndpointErrorProvider')]
    public function testTokenEndpointErrorCodes( $type, $code )
    {
        $this->assertSame( $code, ezpOauthTokenEndpointErrorType::httpCodeForError( $type ) );
    }

    public function testOauthRequiredWithoutError()
    {
        $writer = self::writer();
        ( new ezpOauthRequired( ezpOauthRequired::DEFAULT_REALM ) )->process( $writer );
        $this->assertSame( '', $writer->headers['HTTP/1.1 401'] );
        $this->assertSame( "OAuth realm='Exponential REST'", $writer->headers['WWW-Authenticate'] );
        $this->assertArrayNotHasKey( 'Content-Type', $writer->headers );
        $this->assertSame( "", (string)$writer->response->body );
    }

    public function testOauthRequiredWithErrorAndDescription()
    {
        $writer = self::writer();
        ( new ezpOauthRequired( 'K1', ezpOauthErrorType::INSUFFICIENT_SCOPE, 'needs more' ) )->process( $writer );
        $this->assertArrayHasKey( 'HTTP/1.1 403', $writer->headers );
        $this->assertSame( "OAuth realm='K1', error='insufficient_scope', error_description='needs more'", $writer->headers['WWW-Authenticate'] );
        $this->assertSame( array( 'error' => 'insufficient_scope', 'error_description' => 'needs more' ), json_decode( $writer->response->body, true ) );
    }

    public function testOauthRequiredWithErrorOnly()
    {
        $writer = self::writer();
        ( new ezpOauthRequired( 'K1', ezpOauthErrorType::EXPIRED_TOKEN ) )->process( $writer );
        $this->assertArrayHasKey( 'HTTP/1.1 401', $writer->headers );
        $this->assertSame( '{"error":"expired_token"}', $writer->response->body );
    }

    public function testOauthErrorStatus()
    {
        $writer = self::writer();
        ( new ezpRestOauthErrorStatus( ezpOauthErrorType::INVALID_REQUEST, 'raw text' ) )->process( $writer );
        $this->assertSame( '', $writer->headers['HTTP/1.1 400'] );
        $this->assertSame( 'raw text', $writer->response->body );
        $writer = self::writer();
        ( new ezpRestOauthErrorStatus( ezpOauthErrorType::INVALID_TOKEN ) )->process( $writer );
        $this->assertArrayHasKey( 'HTTP/1.1 401', $writer->headers );
        $this->assertSame( "", (string)$writer->response->body );
    }

    // ---------------------------------------------------------------- exceptions

    public function testOauthExceptionsCarryTheirErrorType()
    {
        $this->assertSame( 'expired_token', ( new ezpOauthExpiredTokenException( 'm' ) )->errorType );
        $this->assertSame( 'insufficient_scope', ( new ezpOauthInsufficientScopeException( 'm' ) )->errorType );
        $this->assertSame( 'invalid_request', ( new ezpOauthInvalidRequestException( 'm' ) )->errorType );
        $this->assertSame( 'invalid_token', ( new ezpOauthInvalidTokenException( 'm' ) )->errorType );
        $this->assertNull( ( new ezpOauthNoAuthInfoException( 'm' ) )->errorType );
        $this->assertNull( ( new ezpOauthTokenNotFoundException( 'm' ) )->errorType );
        $this->assertSame( 'm', ( new ezpOauthInvalidTokenException( 'm' ) )->getMessage() );
        $this->assertInstanceOf( 'ezpOauthRequiredException', new ezpOauthExpiredTokenException( 'm' ) );
        $this->assertInstanceOf( 'ezpOauthBadRequestException', new ezpOauthInvalidRequestException( 'm' ) );
    }

    public function testRestExceptionMessages()
    {
        $this->assertSame( 'Selected authentication style was not found', ( new ezpRestAuthStyleNotFoundException() )->getMessage() );
        $this->assertSame( "The output content renderer 'xml' could not be found.", ( new ezpRestContentRendererNotFoundException( 'xml' ) )->getMessage() );
        $this->assertSame( 'Could not find filter k1 in the system. Are your settings correct?', ( new ezpRestFilterNotFoundException( 'k1' ) )->getMessage() );
        $this->assertSame( 'Missing parameter for filter constructor.', ( new ezpRestFilterMissingParameterException() )->getMessage() );
        $this->assertSame( 'Communication over HTTPS is required.', ( new ezpRestHTTPSRequiredException() )->getMessage() );
        $this->assertSame( "The API provider 'k1' could not be found.", ( new ezpRestProviderNotFoundException( 'k1' ) )->getMessage() );
        $this->assertSame( 'Could not find the route security filter.', ( new ezpRestRouteSecurityFilterNotFoundException() )->getMessage() );
        $e = new ezpRouteMethodNotAllowedException( array( 'GET', 'POST' ) );
        $this->assertSame( 'This method is not supported, allowed methods are: GET, POST', $e->getMessage() );
        $this->assertSame( array( 'GET', 'POST' ), $e->getAllowedMethods() );
        $this->assertSame( array(), ( new ezpRouteMethodNotAllowedException() )->getAllowedMethods() );
    }

    // ---------------------------------------------------------------- error controller

    private static function show( $exception )
    {
        $request = new ezpRestRequest( null, 'http-get', '', '/fatal' );
        $request->variables = array( 'exception' => $exception );
        $controller = new ezpRestErrorController( 'show', $request );
        return $controller->createResult();
    }

    private static function processed( ezcMvcResult $result )
    {
        $writer = self::writer();
        $result->status->process( $writer );
        return $writer;
    }

    public function testRouteNotFoundIs404()
    {
        $result = self::show( new ezcMvcRouteNotFoundException( new ezpRestRequest( null, 'http-get', '', '/nothing' ) ) );
        $this->assertInstanceOf( 'ezpRestHttpResponse', $result->status );
        $this->assertSame( 404, $result->status->code );
        $this->assertSame( 'Not Found', $result->status->message );
    }

    public function testContentNotFoundIs404()
    {
        $result = self::show( new ezpContentNotFoundException( 'no node 99' ) );
        $this->assertSame( 404, $result->status->code );
        $this->assertSame( 'Not Found', $result->status->message, 'the message of the exception is not shown' );
    }

    public function testOauthBadRequest()
    {
        $result = self::show( new ezpOauthInvalidRequestException( 'bad grant' ) );
        $this->assertInstanceOf( 'ezpRestOauthErrorStatus', $result->status );
        $this->assertSame( 'invalid_request', $result->status->errorType );
        $this->assertSame( 'bad grant', $result->variables['message'] );
        $this->assertArrayHasKey( 'HTTP/1.1 400', self::processed( $result )->headers );
    }

    public function testOauthRequired()
    {
        $result = self::show( new ezpOauthExpiredTokenException( 'expired' ) );
        $this->assertInstanceOf( 'ezpOauthRequired', $result->status );
        $this->assertSame( 'Exponential REST', $result->status->realm );
        $this->assertSame( 'expired_token', $result->status->errorType );
        $this->assertSame( 'expired', $result->status->errorMessage );
        $this->assertArrayHasKey( 'HTTP/1.1 401', self::processed( $result )->headers );
    }

    public function testMethodNotAllowed()
    {
        $result = self::show( new ezpRouteMethodNotAllowedException( array( 'GET', 'OPTIONS' ) ) );
        $this->assertInstanceOf( 'ezpRestMvcResult', $result );
        $this->assertInstanceOf( 'ezpRestStatusResponse', $result->status );
        $writer = self::processed( $result );
        $this->assertSame( 'Method Not Allowed', $writer->headers['HTTP/1.1 405'] );
        $this->assertSame( 'GET, OPTIONS', $writer->headers['Allow'] );
    }

    public function testAnyOtherExceptionIs500WithItsMessage()
    {
        $result = self::show( new RuntimeException( 'database gone' ) );
        $this->assertSame( 500, $result->status->code );
        $this->assertSame( 'database gone', $result->variables['message'] );
        $this->assertSame( '{"error_message":"database gone"}', self::processed( $result )->response->body );
    }

    // ---------------------------------------------------------------- result and view

    public function testMvcResultExportRoundTrip()
    {
        $result = new ezpRestMvcResult( null, null, 'k1', null, array(), null, array( 'a' => 1 ) );
        $result->responseGroups = array( 'Metadata' );
        $copy = ezpRestMvcResult::__set_state( get_object_vars( $result ) );
        $this->assertInstanceOf( 'ezpRestMvcResult', $copy );
        $this->assertSame( array( 'Metadata' ), $copy->responseGroups );
        $this->assertSame( array( 'a' => 1 ), $copy->variables );
        $this->assertSame( 'k1', $copy->generator );
    }

    public function testJsonViewSetsTheContentTypeAndOneJsonZone()
    {
        $result = new ezcMvcResult();
        $view = new ezpRestJsonView( new ezpRestRequest(), $result );
        $this->assertSame( 'application/json', $result->content->type );
        $this->assertSame( 'UTF-8', $result->content->charset );
        $zones = $view->createZones( false );
        $this->assertCount( 1, $zones );
        $this->assertInstanceOf( 'ezcMvcJsonViewHandler', $zones[0] );
    }

    public function testAuthProviderRoutes()
    {
        $provider = new ezpRestAuthProvider();
        $routes = $provider->getRoutes();
        $this->assertSame( array( 'basicAuth', 'oauthLogin', 'oauthToken' ), array_keys( $routes ) );
        $request = new ezpRestRequest( null, 'http-post', '', '/oauth/token' );
        $info = $routes['oauthToken']->matches( $request );
        $this->assertSame( 'ezpRestOauthTokenController', $info->controllerClass );
        $this->assertSame( 'handleRequest', $info->action );
    }
}
