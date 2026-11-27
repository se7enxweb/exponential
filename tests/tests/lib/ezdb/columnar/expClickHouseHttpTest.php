<?php
/**
 * ClickHouse's HTTP interface (expClickHouseHttp) without a server: the address and headers of a request, results in
 * TabSeparatedWithNamesAndTypes read back, error answers.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expColumnarTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('columnar')]
class expClickHouseHttpTest extends PHPUnit\Framework\TestCase
{
    public function testRequest()
    {
        $http = new expClickHouseHttp( array( 'host' => 'ch.example', 'user' => 'app', 'password' => 'p@ss', 'database' => 'analytics',
                                              'settings' => array( 'max_execution_time' => 60 ) ) );
        $sent = array();
        $http->Sender = function ( $url, $headers, $body ) use ( &$sent ) {
            $sent = array( $url, $headers, $body );
            return array( 200, array( 'x-clickhouse-summary' => '{"read_rows":"1","written_rows":"0"}' ), "one\nUInt8\n1\n" );
        };
        $result = $http->query( 'SELECT 1 AS one', array( 'mutations_sync' => 2 ) );
        $this->assertSame( 'http://ch.example:8123/?default_format=TabSeparatedWithNamesAndTypes&max_execution_time=60&mutations_sync=2', $sent[0] );
        $this->assertContains( 'X-ClickHouse-User: app', $sent[1] );
        $this->assertContains( 'X-ClickHouse-Key: p@ss', $sent[1] );
        $this->assertContains( 'X-ClickHouse-Database: analytics', $sent[1] );
        $this->assertStringNotContainsString( 'p@ss', $sent[0], 'the password is never in the address' );
        $this->assertSame( 'SELECT 1 AS one', $sent[2] );
        $this->assertSame( array( 'one' ), $result['columns'] );
        $this->assertSame( array( array( '1' ) ), $result['rows'] );
        $this->assertSame( '1', $result['summary']['read_rows'] );

        $secure = new expClickHouseHttp( array( 'host' => '::1', 'secure' => true ) );
        $this->assertStringStartsWith( 'https://[::1]:8443/?', $secure->url() );
        $newline = new expClickHouseHttp( array( 'user' => "a\r\nX-Injected: 1" ) );
        $newline->Sender = function ( $url, $headers, $body ) use ( &$sent ) {
            $sent = $headers;
            return array( 200, array(), '' );
        };
        $newline->query( 'SELECT 1' );
        $this->assertContains( 'X-ClickHouse-User: aX-Injected: 1', $sent, 'no header can be added through a value' );
    }

    public function testParse()
    {
        $body = "id\tname\tn\nUInt64\tString\tNullable(String)\n"
              . "1\ttab\\there\\nline\t\\N\n"
              . "2\tback\\\\slash \\' quote\tNULL\n"
              . "3\t\tx\n";
        $result = expClickHouseHttp::parse( $body );
        $this->assertSame( array( 'id', 'name', 'n' ), $result['columns'] );
        $this->assertSame( array( 'UInt64', 'String', 'Nullable(String)' ), $result['types'] );
        $this->assertSame( array( array( '1', "tab\there\nline", null ), array( '2', "back\\slash ' quote", 'NULL' ), array( '3', '', 'x' ) ),
                           $result['rows'] );
        $this->assertSame( array( array( '' ) ), expClickHouseHttp::parse( "e\nString\n\n" )['rows'], 'a row of one empty text is kept' );
        $this->assertSame( array(), expClickHouseHttp::parse( "e\nString\n" )['rows'] );
        $this->assertSame( array( 'columns' => array(), 'types' => array(), 'rows' => array() ), expClickHouseHttp::parse( '' ) );
    }

    public function testErrors()
    {
        $http = new expClickHouseHttp( array() );
        $http->Sender = function () {
            return array( 404, array( 'x-clickhouse-exception-code' => '60' ),
                          "Code: 60. DB::Exception: Unknown table expression identifier 'x' in scope SELECT * FROM x. (UNKNOWN_TABLE) (version 25.8.33.6 (official build))\n" );
        };
        $this->assertFalse( $http->query( 'SELECT * FROM x' ) );
        $this->assertSame( 60, $http->ErrorCode );
        $this->assertSame( "Unknown table expression identifier 'x' in scope SELECT * FROM x. (UNKNOWN_TABLE)", $http->Error );

        // the server failed after it began to send: status 200, the exception at the end
        $http->Sender = function () {
            return array( 200, array(), "n\nUInt64\n1\nCode: 241. DB::Exception: Memory limit exceeded. (MEMORY_LIMIT_EXCEEDED) (version 25.8.33.6 (official build))\n" );
        };
        $this->assertFalse( $http->query( 'SELECT n FROM big' ) );
        $this->assertSame( 241, $http->ErrorCode );

        $http->Sender = function () {
            return false;
        };
        $this->assertFalse( $http->query( 'SELECT 1' ) );
        $this->assertSame( -1, $http->ErrorCode );

        $this->assertSame( 516, expClickHouseHttp::codeOf( 'Code: 516. DB::Exception: x: Authentication failed' ) );
        $this->assertSame( 0, expClickHouseHttp::codeOf( 'Bad Gateway' ) );
        $this->assertSame( 'HTTP status 502', expClickHouseHttp::messageOf( '', 502 ) );
        $this->assertFalse( expClickHouseHttp::endsInException( "a\tCode: 1. DB::Exception: inside a value\n" ) );
    }

    public function testUnescape()
    {
        $this->assertSame( "a\tb\nc\\d'e\0", expClickHouseHttp::unescape( 'a\\tb\\nc\\\\d\\\'e\\0' ) );
        $this->assertSame( 'plain', expClickHouseHttp::unescape( 'plain' ) );
    }

    public function testNoServer()
    {
        // a port nothing listens on: refused at once, through curl or PHP's own stream
        $http = new expClickHouseHttp( array( 'host' => '127.0.0.1', 'port' => 1, 'timeout' => 2, 'connect_timeout' => 2 ) );
        $this->assertFalse( $http->query( 'SELECT 1' ) );
        $this->assertSame( -1, $http->ErrorCode );
        $this->assertStringContainsString( 'did not answer', $http->Error );
    }
}
