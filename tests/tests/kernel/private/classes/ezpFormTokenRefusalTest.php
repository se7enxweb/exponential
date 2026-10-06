<?php
/**
 * ezpFormTokenRefusal and ezpFormTokenException without a request: when a refusal is answered as JSON, which URLs
 * count as this site's (open redirects refused), the retry link, the texts, the template parameters, the JSON body,
 * the built-in HTML (escaped) and the collapse window setting.
 *
 * No database. $_SERVER and the site.ini setting are set by each test and put back; nothing writes the log or the
 * collapse state.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class ezpFormTokenRefusalTest extends PHPUnit\Framework\TestCase
{
    private $server;
    private $savedCollapse = false;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->server = $_SERVER;
        foreach ( array( 'HTTP_X_REQUESTED_WITH', 'CONTENT_TYPE', 'HTTP_CONTENT_TYPE', 'HTTP_ACCEPT', 'HTTP_REFERER', 'REQUEST_URI' ) as $key )
            unset( $_SERVER[$key] );
        $_SERVER['HTTP_HOST'] = 'www.k1.example.invalid:8080';
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->server;
        if ( $this->savedCollapse !== false )
        {
            $ini = eZINI::instance();
            if ( $this->savedCollapse === null )
                $ini->removeSetting( 'HTMLForms', 'RefusalLogCollapseSeconds' );
            else
                $ini->setVariable( 'HTMLForms', 'RefusalLogCollapseSeconds', $this->savedCollapse[0] );
            $this->savedCollapse = false;
        }
    }

    private function collapseSetting( $value )
    {
        $ini = eZINI::instance();
        if ( $this->savedCollapse === false )
            $this->savedCollapse = $ini->hasVariable( 'HTMLForms', 'RefusalLogCollapseSeconds' ) ? array( $ini->variable( 'HTMLForms', 'RefusalLogCollapseSeconds' ) ) : null;
        if ( $value === null )
            $ini->removeSetting( 'HTMLForms', 'RefusalLogCollapseSeconds' );
        else
            $ini->setVariable( 'HTMLForms', 'RefusalLogCollapseSeconds', $value );
    }

    // ------------------------------------------------------------ exception

    public function testExceptionReasons()
    {
        $missing = new ezpFormTokenException( ezpFormTokenException::MISSING );
        $this->assertSame( 'missing', $missing->getReason() );
        $this->assertSame( 'form_token_missing', $missing->getReasonCode() );
        $this->assertSame( 'Missing form token from Request', $missing->getMessage() );
        $this->assertSame( 403, $missing->getCode() );

        $wrong = new ezpFormTokenException( ezpFormTokenException::WRONG );
        $this->assertSame( 'form_token_wrong', $wrong->getReasonCode() );
        $this->assertSame( 'Wrong form token found in Request!', $wrong->getMessage() );

        $other = new ezpFormTokenException( 'anything else', 'own message', new RuntimeException( 'x' ) );
        $this->assertSame( 'missing', $other->getReason() );
        $this->assertSame( 'own message', $other->getMessage() );
        $this->assertInstanceOf( RuntimeException::class, $other->getPrevious() );

        $notThrowable = new ezpFormTokenException( 'wrong', null, 'not an exception' );
        $this->assertNull( $notThrowable->getPrevious() );
    }

    // ------------------------------------------------------------ wantsJson

    public function testRestAlwaysWantsJson()
    {
        $this->assertTrue( ezpFormTokenRefusal::wantsJson( true ) );
    }

    public function testPlainRequestWantsHtml()
    {
        $this->assertFalse( ezpFormTokenRefusal::wantsJson() );
    }

    public static function jsonRequestProvider()
    {
        return array(
            'xhr' => array( array( 'HTTP_X_REQUESTED_WITH' => ' XMLHttpRequest ' ), true ),
            'other requested-with' => array( array( 'HTTP_X_REQUESTED_WITH' => 'com.example.app' ), false ),
            'json body' => array( array( 'CONTENT_TYPE' => 'application/json; charset=utf-8' ), true ),
            'vendor json body' => array( array( 'CONTENT_TYPE' => 'application/vnd.api+json' ), true ),
            'json body in HTTP_CONTENT_TYPE' => array( array( 'HTTP_CONTENT_TYPE' => 'Application/JSON' ), true ),
            'not quite json' => array( array( 'CONTENT_TYPE' => 'application/jsonp' ), false ),
            'form body' => array( array( 'CONTENT_TYPE' => 'application/x-www-form-urlencoded' ), false ),
            'accept json' => array( array( 'HTTP_ACCEPT' => 'application/json' ), true ),
            'accept json and html' => array( array( 'HTTP_ACCEPT' => 'text/html, application/json;q=0.9' ), false ),
            'accept json and xhtml' => array( array( 'HTTP_ACCEPT' => 'application/xhtml+xml, application/json' ), false ),
            'accept anything' => array( array( 'HTTP_ACCEPT' => '*/*' ), false ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('jsonRequestProvider')]
    public function testWantsJson( $server, $expected )
    {
        foreach ( $server as $key => $value )
            $_SERVER[$key] = $value;
        $this->assertSame( $expected, ezpFormTokenRefusal::wantsJson() );
    }

    // ------------------------------------------------------------ sameSiteURL

    public static function urlProvider()
    {
        return array(
            'empty' => array( '', '' ),
            'root relative' => array( '/content/edit/12/1', '/content/edit/12/1' ),
            'with query' => array( '/a/b?c=d&e=f', '/a/b?c=d&e=f' ),
            'empty query dropped' => array( '/a?', '/a' ),
            'fragment dropped' => array( '/a#top', '/a' ),
            'own host, other port' => array( 'https://WWW.k1.example.invalid/a?b=1', '/a?b=1' ),
            'own host with port' => array( 'http://www.k1.example.invalid:8080/x', '/x' ),
            'other host' => array( 'https://evil.example.invalid/a', '' ),
            'protocol relative' => array( '//evil.example.invalid/a', '' ),
            'not http' => array( 'javascript:alert(1)', '' ),
            'ftp on own host' => array( 'ftp://www.k1.example.invalid/a', '' ),
            'credentials' => array( 'https://user:pw@www.k1.example.invalid/a', '' ),
            'relative path' => array( 'content/view', '' ),
            'backslash' => array( '/\\evil.example.invalid', '' ),
            'control character' => array( "/a\nb", '' ),
            'host without path' => array( 'https://www.k1.example.invalid', '' ),
            'too long' => array( '/' . str_repeat( 'a', 2048 ), '' ),
            'just short enough' => array( '/' . str_repeat( 'a', 2047 ), '/' . str_repeat( 'a', 2047 ) ),
            'broken' => array( 'http:///x', '' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('urlProvider')]
    public function testSameSiteURL( $url, $expected )
    {
        $this->assertSame( $expected, ezpFormTokenRefusal::sameSiteURL( $url ) );
    }

    public function testNoOwnHostRefusesAbsoluteUrls()
    {
        unset( $_SERVER['HTTP_HOST'] );
        $this->assertSame( '', ezpFormTokenRefusal::sameSiteURL( 'https://www.k1.example.invalid/a' ) );
        $this->assertSame( '/a', ezpFormTokenRefusal::sameSiteURL( '/a' ) );
    }

    // ------------------------------------------------------------ referrer and retry

    public function testRetryGoesToTheReferrerFirst()
    {
        $_SERVER['HTTP_REFERER'] = 'https://www.k1.example.invalid/form?step=2';
        $_SERVER['REQUEST_URI'] = '/content/action';
        $this->assertSame( '/form?step=2', ezpFormTokenRefusal::referrer() );
        $this->assertSame( '/form?step=2', ezpFormTokenRefusal::retryURL() );
    }

    public function testRetryFallsBackToTheRequestedUrl()
    {
        $_SERVER['HTTP_REFERER'] = 'https://evil.example.invalid/form';
        $_SERVER['REQUEST_URI'] = '/content/action?x=1';
        $this->assertSame( '', ezpFormTokenRefusal::referrer() );
        $this->assertSame( '/content/action?x=1', ezpFormTokenRefusal::retryURL() );
    }

    public function testRetryFallsBackToTheRoot()
    {
        $this->assertSame( '/', ezpFormTokenRefusal::retryURL() );
        $_SERVER['REQUEST_URI'] = '//evil.example.invalid/';
        $this->assertSame( '/', ezpFormTokenRefusal::retryURL() );
    }

    // ------------------------------------------------------------ texts and bodies

    public function testTextsAreTheSameForBothReasons()
    {
        $missing = ezpFormTokenRefusal::texts( ezpFormTokenException::MISSING );
        $this->assertSame( array( 'title', 'message', 'action' ), array_keys( $missing ) );
        $this->assertSame( $missing, ezpFormTokenRefusal::texts( ezpFormTokenException::WRONG ) );
        foreach ( $missing as $text )
            $this->assertNotSame( '', $text );
        $this->assertNotSame( '', ezpFormTokenRefusal::pathName() );
    }

    public function testTemplateParameters()
    {
        $_SERVER['HTTP_REFERER'] = '/back';
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $parameters = ezpFormTokenRefusal::templateParameters( new ezpFormTokenException( 'wrong' ) );
        $this->assertSame( 'wrong', $parameters['reason'] );
        $this->assertSame( 'form_token_wrong', $parameters['reason_code'] );
        $this->assertSame( '/back', $parameters['referrer'] );
        $this->assertSame( '/back', $parameters['retry_url'] );
        $this->assertTrue( $parameters['is_ajax'] );
        $texts = ezpFormTokenRefusal::texts( 'wrong' );
        $this->assertSame( $texts['title'], $parameters['title'] );
        $this->assertSame( $texts['action'], $parameters['action'] );
    }

    public function testJsonBody()
    {
        $_SERVER['REQUEST_URI'] = '/api/save?id=1';
        $body = json_decode( ezpFormTokenRefusal::jsonBody( new ezpFormTokenException( 'missing' ) ), true );
        $this->assertSame( 403, $body['error']['code'] );
        $this->assertSame( 'form_token_missing', $body['error']['reason'] );
        $this->assertSame( '/api/save?id=1', $body['error']['retry_url'] );
        $this->assertSame( ezpFormTokenRefusal::texts( 'missing' )['message'], $body['error']['message'] );
        $this->assertStringContainsString( '"/api/save?id=1"', ezpFormTokenRefusal::jsonBody( new ezpFormTokenException( 'missing' ) ) );
    }

    public function testFallbackContentEscapesEverything()
    {
        $html = ezpFormTokenRefusal::fallbackContent( array( 'title' => '<b>T</b>', 'message' => 'M & "q"', 'action' => 'A\'s', 'retry_url' => '/x?a=1&b="2"' ) );
        $this->assertSame( '<div class="message-warning form-token-refused"><h2>&lt;b&gt;T&lt;/b&gt;</h2><p>M &amp; &quot;q&quot;</p>'
            . '<p><a href="/x?a=1&amp;b=&quot;2&quot;">A&#039;s</a></p></div>', $html );
    }

    public function testFallbackContentFillsMissingParameters()
    {
        $_SERVER['REQUEST_URI'] = '/form';
        $texts = ezpFormTokenRefusal::texts( 'missing' );
        $html = ezpFormTokenRefusal::fallbackContent( array() );
        $this->assertStringContainsString( htmlspecialchars( $texts['title'], ENT_QUOTES ), $html );
        $this->assertStringContainsString( 'href="/form"', $html );
    }

    public function testStandalonePage()
    {
        $html = ezpFormTokenRefusal::standalonePage( array( 'reason' => 'wrong', 'retry_url' => '/r<' ) );
        $texts = ezpFormTokenRefusal::texts( 'wrong' );
        $this->assertStringStartsWith( '<!doctype html>', $html );
        $this->assertStringEndsWith( '</body></html>', $html );
        $this->assertStringContainsString( '<meta name="robots" content="noindex">', $html );
        $this->assertStringContainsString( '<title>' . htmlspecialchars( $texts['title'], ENT_QUOTES ) . '</title>', $html );
        $this->assertStringContainsString( 'href="/r&lt;"', $html );
        $this->assertStringContainsString( '>403<', $html );
    }

    // ------------------------------------------------------------ collapse window

    public function testCollapseSecondsFromTheSetting()
    {
        $this->collapseSetting( '15' );
        $this->assertSame( 15, ezpFormTokenRefusal::collapseSeconds() );
        $this->collapseSetting( '0' );
        $this->assertSame( 0, ezpFormTokenRefusal::collapseSeconds() );
        $this->collapseSetting( '-5' );
        $this->assertSame( 0, ezpFormTokenRefusal::collapseSeconds() );
        $this->collapseSetting( null );
        $this->assertSame( ezpFormTokenRefusal::DEFAULT_COLLAPSE_SECONDS, ezpFormTokenRefusal::collapseSeconds() );
    }

    public function testStateFileIsInTheCacheDirectory()
    {
        $this->assertSame( eZSys::cacheDirectory() . '/formtoken/refusals.json', ezpFormTokenRefusal::stateFile() );
    }
}
