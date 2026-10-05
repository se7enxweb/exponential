<?php
/**
 * The request side of eZHTTPTool (lib/ezutils), without a network or a session:
 *   - POST and GET variables: set, has, read with and without a fallback, variable() preferring POST, the attribute
 *     interface for post and get
 *   - image buttons: name_x/name_y pairs become name (true) or, with a trailing number, name with that number
 *   - parseHTTPResponse(): header and body split, header names lower-cased, a response without a body separator
 *   - createRedirectUrl(): absolute paths, protocol and host in the path, user name, password and port in the path,
 *     the override parameters, a relative path after SCRIPT_URL
 *   - basicCredentials(), username() and password() from PHP_AUTH_USER/PHP_AUTH_PW
 *
 * $_GET, $_POST and $_SERVER are restored in tearDown().
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezutils
 */

class eZHTTPToolRequestTest extends PHPUnit\Framework\TestCase
{
    private $saved;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->saved = array( $_GET, $_POST, $_SERVER );
        $_GET = array();
        $_POST = array();
    }

    protected function tearDown(): void
    {
        list( $_GET, $_POST, $_SERVER ) = $this->saved;
    }

    public function testPostAndGetVariables()
    {
        $http = new eZHTTPTool();
        $http->setPostVariable( 'p', 'post value' );
        $http->setGetVariable( 'g', 'get value' );
        $this->assertTrue( $http->hasPostVariable( 'p' ) );
        $this->assertFalse( $http->hasPostVariable( 'g' ) );
        $this->assertTrue( $http->hasGetVariable( 'g' ) );
        $this->assertSame( 'post value', $http->postVariable( 'p' ) );
        $this->assertSame( 'get value', $http->getVariable( 'g' ) );
        $this->assertSame( 'fallback', $http->postVariable( 'missing', 'fallback' ) );
        $this->assertSame( 'fallback', $http->getVariable( 'missing', 'fallback' ) );
        $this->assertNull( @$http->postVariable( 'missing' ) );
        $this->assertNull( @$http->getVariable( 'missing' ) );
        $this->assertSame( array( 'p' => 'post value' ), $http->attribute( 'post' ) );
        $this->assertSame( array( 'g' => 'get value' ), $http->attribute( 'get' ) );
        $this->assertNull( $http->attribute( 'nothing' ) );
        $this->assertTrue( $http->hasAttribute( 'session' ) );
    }

    public function testVariablePrefersPost()
    {
        $http = new eZHTTPTool();
        $_POST['v'] = 'from post';
        $_GET['v'] = 'from get';
        $_GET['only_get'] = 'g';
        $this->assertSame( 'from post', $http->variable( 'v' ) );
        $this->assertSame( 'g', $http->variable( 'only_get' ) );
        $this->assertTrue( $http->hasVariable( 'only_get' ) );
        $this->assertFalse( $http->hasVariable( 'none' ) );
        $this->assertSame( 0, $http->variable( 'none', 0 ) );
    }

    public function testImageButtons()
    {
        $_POST = array( 'PublishButton_x' => '10', 'PublishButton_y' => '4', 'RemoveItem_42_x' => '1', 'RemoveItem_42_y' => '1', 'Lonely_x' => '3' );
        ( new eZHTTPTool() )->createPostVarsFromImageButtons();
        $this->assertTrue( $_POST['PublishButton'] );
        $this->assertSame( '42', $_POST['RemoveItem'] );
        $this->assertArrayNotHasKey( 'Lonely', $_POST, 'an _x without its _y is no button' );
    }

    public function testParseHttpResponse()
    {
        $response = "HTTP/1.1 200 OK\r\nContent-Type: text/html; charset=utf-8\r\nX-Thing:  value  \r\n\r\n<p>body\r\n\r\nmore</p>";
        $this->assertTrue( eZHTTPTool::parseHTTPResponse( $response, $header, $body ) );
        $this->assertSame( 'text/html; charset=utf-8', $header['content-type'] );
        $this->assertSame( 'value', $header['x-thing'] );
        $this->assertSame( "<p>body\r\n\r\nmore</p>", $body );

        $broken = "no separator here";
        $this->assertFalse( eZHTTPTool::parseHTTPResponse( $broken, $header2, $body2 ) );
        $empty = '';
        $this->assertFalse( eZHTTPTool::parseHTTPResponse( $empty, $header3, $body3 ) );
    }

    public static function redirectProvider()
    {
        $base = array( 'host' => 'example.org', 'protocol' => 'https', 'pre_url' => false );
        return array(
            'absolute path'             => array( '/content/view/full/2', $base, 'https://example.org/content/view/full/2' ),
            'protocol and host in path' => array( 'http://other.example/x', $base, 'http://other.example/x' ),
            'host in path'              => array( '//other.example/x', $base, 'https://other.example/x' ),
            'user, password and port'   => array( '//me:secret@other.example:8080/x', $base, 'https://me:secret@other.example:8080/x' ),
            'port parameter'            => array( '/x', $base + array( 'port' => 8443 ), 'https://example.org:8443/x' ),
            'override host'             => array( 'http://other.example/x', $base + array( 'override_host' => 'forced.example' ), 'http://forced.example/x' ),
            'override protocol'         => array( 'http://other.example/x', $base + array( 'override_protocol' => 'https' ), 'https://other.example/x' ),
            'override user'             => array( '/x', $base + array( 'override_username' => 'u', 'override_password' => 'p' ), 'https://u:p@example.org/x' ),
            'user without password'     => array( '/x', $base + array( 'username' => 'u' ), 'https://u@example.org/x' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('redirectProvider')]
    public function testCreateRedirectUrl( $path, $parameters, $expected )
    {
        $this->assertSame( $expected, eZHTTPTool::createRedirectUrl( $path, $parameters ) );
    }

    public function testRelativeRedirectFollowsTheScriptUrl()
    {
        $_SERVER['SCRIPT_URL'] = '/site/folder';
        $this->assertSame( 'https://example.org/site/folder/page', eZHTTPTool::createRedirectUrl( 'page', array( 'host' => 'example.org', 'protocol' => 'https' ) ) );
        $_SERVER['SCRIPT_URL'] = '/site/folder/';
        $this->assertSame( 'https://example.org/site/folder/page', eZHTTPTool::createRedirectUrl( 'page', array( 'host' => 'example.org', 'protocol' => 'https' ) ) );
    }

    public static function credentialsProvider()
    {
        return array(
            'plain'                 => array( base64_encode( 'alice:secret' ), array( 'alice', 'secret' ) ),
            'colon in the password' => array( base64_encode( 'alice:se:cr:et' ), array( 'alice', 'se:cr:et' ) ),
            'empty password'        => array( base64_encode( 'alice:' ), array( 'alice', '' ) ),
            'no colon'              => array( base64_encode( 'alice' ), array( 'alice', false ) ),
            'not base64'            => array( '%%%', array( false, false ) ),
        );
    }

    /**
     * The password is everything after the first colon. It was cut at its own first colon, and credentials without
     * any colon raised an undefined offset warning.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('credentialsProvider')]
    public function testBasicCredentials( $encoded, $expected )
    {
        $this->assertSame( $expected, eZHTTPTool::basicCredentials( $encoded ) );
    }

    public function testUsernameAndPasswordFromPhpAuth()
    {
        unset( $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'] );
        $this->assertFalse( eZHTTPTool::username() );
        $this->assertFalse( eZHTTPTool::password() );
        $_SERVER['PHP_AUTH_USER'] = 'bob';
        $_SERVER['PHP_AUTH_PW'] = 'p:w';
        $this->assertSame( 'bob', eZHTTPTool::username() );
        $this->assertSame( 'p:w', eZHTTPTool::password() );
    }

    public function testSharedInstance()
    {
        $saved = $GLOBALS['eZHTTPToolInstance'] ?? null;
        unset( $GLOBALS['eZHTTPToolInstance'] );
        $_POST = array( 'Go_x' => 1, 'Go_y' => 1 );
        $http = eZHTTPTool::instance();
        $this->assertSame( $http, eZHTTPTool::instance() );
        $this->assertTrue( $_POST['Go'], 'the instance turns image buttons into variables' );
        if ( $saved !== null )
            $GLOBALS['eZHTTPToolInstance'] = $saved;
        else
            unset( $GLOBALS['eZHTTPToolInstance'] );
    }
}
