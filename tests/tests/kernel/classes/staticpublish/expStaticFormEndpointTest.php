<?php
/**
 * The form endpoint of static publishing (expStaticFormEndpoint) and its token (expStaticFormToken): pure, with a
 * fixed key, a fake rate limit and a fake hand-over.
 *
 *  FE-01 the token: signed for a target and an object; another target, object, key, a changed or expired token fail
 *  FE-02 the origins of a target: public address and custom domain; CORS never a wildcard
 *  FE-03 switched off, an unknown target, a target with Forms=hide: 404
 *  FE-04 a post from another origin (or none): 403; the Referer stands in for a missing Origin
 *  FE-05 a bad token: 403, a plain post goes back to its page with #exp-static-form-failed
 *  FE-06 the rate limit: 429 with Retry-After, keyed by a hash of the target and the address
 *  FE-07 accepted: the form's own fields handed over without the endpoint's; JSON for a script, 303 back for a form
 *  FE-08 a preflight: 204 with the CORS headers for a target's origin, 403 for another; only POST and OPTIONS
 *  FE-09 every refusal and post is an audit event system.staticpublish.form
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expStaticFormEndpointTest extends PHPUnit\Framework\TestCase
{
    const KEY = 'test-key-0123456789';
    const NOW = 1800000000;
    const ORIGIN = 'https://owner.github.io';

    private $handed = array();
    private $rateKeys = array();
    private $wait = 0;
    private $events = array();

    private function endpoint( array $options = array() )
    {
        $self = $this;
        return new expStaticFormEndpoint( $options + array(
            'enabled' => true, 'key' => self::KEY, 'now' => self::NOW,
            'targets' => array(
                'pub' => new expStaticTarget( 'pub', array( 'SiteAccess' => 'site', 'Forms' => 'endpoint', 'PublicURL' => 'https://owner.github.io/repo/', 'CustomDomain' => 'www.example.com' ) ),
                'quiet' => new expStaticTarget( 'quiet', array( 'SiteAccess' => 'site', 'Forms' => 'hide', 'PublicURL' => 'https://other.github.io/' ) ) ),
            'rate' => function ( $key ) use ( $self ) { $self->rateKeys[] = $key; return $self->wait; },
            'hand_over' => function ( expStaticTarget $target, array $fields ) use ( $self ) { $self->handed[] = array( $target->id(), $fields ); return array( true, 'Thanks' ); },
            'audit' => function ( $name, $data ) use ( $self ) { $self->events[] = array( $name, $data['result'], $data['reason'] ); },
        ) );
    }

    private function post( array $extra = array() )
    {
        return $extra + array( 'ExpStaticTarget' => 'pub', 'ExpStaticToken' => expStaticFormToken::sign( 'pub', 57, self::NOW - 86400, self::KEY ),
                               'ContentObjectID' => '57', 'ContentNodeID' => '59', 'ContentObjectAttribute_ezstring_data_text_1' => 'Hello' );
    }

    /** FE-01 */
    public function testToken()
    {
        $token = expStaticFormToken::sign( 'pub', 57, self::NOW, self::KEY );
        $this->assertMatchesRegularExpression( '#^v1\.1800000000\.57\.[A-Za-z0-9_-]{43}$#', $token );
        $this->assertSame( '', expStaticFormToken::problem( $token, 'pub', '57', 400, self::NOW + 10, self::KEY ) );
        $this->assertStringContainsString( 'another form', expStaticFormToken::problem( $token, 'pub', '58', 400, self::NOW, self::KEY ) );
        $this->assertStringContainsString( 'not signed', expStaticFormToken::problem( $token, 'other', '57', 400, self::NOW, self::KEY ) );
        $this->assertStringContainsString( 'not signed', expStaticFormToken::problem( $token, 'pub', '57', 400, self::NOW, 'another key' ) );
        $this->assertStringContainsString( 'not signed', expStaticFormToken::problem( substr( $token, 0, -1 ) . ( substr( $token, -1 ) === 'A' ? 'B' : 'A' ), 'pub', '57', 400, self::NOW, self::KEY ) );
        $this->assertStringContainsString( 'expired', expStaticFormToken::problem( $token, 'pub', '57', 400, self::NOW + 401 * 86400, self::KEY ) );
        $this->assertStringContainsString( 'future', expStaticFormToken::problem( $token, 'pub', '57', 400, self::NOW - 3600, self::KEY ) );
        $this->assertStringContainsString( 'no token', expStaticFormToken::problem( '', 'pub', '57', 400, self::NOW, self::KEY ) );
    }

    /** FE-02 */
    public function testOrigins()
    {
        $target = new expStaticTarget( 'pub', array( 'PublicURL' => 'https://Owner.GitHub.io/repo/', 'CustomDomain' => 'www.example.com' ) );
        $this->assertSame( array( 'https://owner.github.io', 'https://www.example.com', 'http://www.example.com' ), expStaticFormEndpoint::origins( $target ) );
        $this->assertSame( 'http://localhost:4000', expStaticFormEndpoint::origin( 'http://localhost:4000/x?y' ) );
        $this->assertSame( '', expStaticFormEndpoint::origin( 'null' ) );
        $this->assertSame( array( 'Access-Control-Allow-Origin' => self::ORIGIN ), expStaticFormEndpoint::cors( self::ORIGIN ) );
    }

    /** FE-03 */
    public function testNotTaken()
    {
        $headers = array( 'origin' => self::ORIGIN, 'accept' => 'application/json' );
        $this->assertSame( 404, $this->endpoint( array( 'enabled' => false ) )->handle( 'POST', $headers, $this->post(), '1.2.3.4' )['status'] );
        $this->assertSame( 404, $this->endpoint()->handle( 'POST', $headers, $this->post( array( 'ExpStaticTarget' => 'nope' ) ), '1.2.3.4' )['status'] );
        $this->assertSame( 404, $this->endpoint()->handle( 'POST', array( 'origin' => 'https://other.github.io' ) + $headers, $this->post( array( 'ExpStaticTarget' => 'quiet' ) ), '1.2.3.4' )['status'] );
        $this->assertSame( array(), $this->handed );
    }

    /** FE-04 */
    public function testOrigin()
    {
        $answer = $this->endpoint()->handle( 'POST', array( 'origin' => 'https://evil.example.net', 'accept' => 'application/json' ), $this->post(), '1.2.3.4' );
        $this->assertSame( 403, $answer['status'] );
        $this->assertArrayNotHasKey( 'Access-Control-Allow-Origin', $answer['headers'] );
        $this->assertSame( 403, $this->endpoint()->handle( 'POST', array( 'accept' => 'application/json' ), $this->post(), '1.2.3.4' )['status'] );
        $answer = $this->endpoint()->handle( 'POST', array( 'referer' => 'https://www.example.com/contact/' ), $this->post(), '1.2.3.4' );
        $this->assertSame( 303, $answer['status'] );
        $this->assertSame( 'https://www.example.com/contact/#exp-static-form-sent', $answer['redirect'] );
    }

    /** FE-05 */
    public function testBadToken()
    {
        $answer = $this->endpoint()->handle( 'POST', array( 'origin' => self::ORIGIN, 'referer' => self::ORIGIN . '/repo/contact/#x' ),
                                             $this->post( array( 'ContentObjectID' => '58' ) ), '1.2.3.4' );
        $this->assertSame( 303, $answer['status'] );
        $this->assertSame( self::ORIGIN . '/repo/contact/#exp-static-form-failed', $answer['redirect'] );
        $this->assertSame( array(), $this->handed );
        $json = $this->endpoint()->handle( 'POST', array( 'origin' => self::ORIGIN, 'x-requested-with' => 'fetch' ), $this->post( array( 'ExpStaticToken' => 'v1.x' ) ), '1.2.3.4' );
        $this->assertSame( 403, $json['status'] );
        $this->assertFalse( $json['json']['ok'] );
        $this->assertSame( self::ORIGIN, $json['headers']['Access-Control-Allow-Origin'] );
    }

    /** FE-06 */
    public function testRateLimit()
    {
        $this->wait = 120;
        $answer = $this->endpoint()->handle( 'POST', array( 'origin' => self::ORIGIN, 'accept' => 'application/json' ), $this->post(), '1.2.3.4' );
        $this->assertSame( 429, $answer['status'] );
        $this->assertSame( '120', $answer['headers']['Retry-After'] );
        $this->assertSame( array( hash( 'sha256', 'pub|1.2.3.4' ) ), $this->rateKeys );
        $this->assertSame( array(), $this->handed );
    }

    /** FE-07 */
    public function testAccepted()
    {
        $answer = $this->endpoint()->handle( 'POST', array( 'origin' => self::ORIGIN, 'accept' => 'application/json, */*' ), $this->post(), '1.2.3.4' );
        $this->assertSame( 200, $answer['status'] );
        $this->assertSame( array( 'ok' => true, 'message' => 'Thanks' ), $answer['json'] );
        $this->assertSame( self::ORIGIN, $answer['headers']['Access-Control-Allow-Origin'] );
        $this->assertSame( 'Origin', $answer['headers']['Vary'] );
        $this->assertSame( array( 'pub', array( 'ContentObjectID' => '57', 'ContentNodeID' => '59', 'ContentObjectAttribute_ezstring_data_text_1' => 'Hello' ) ), $this->handed[0] );
        $plain = $this->endpoint()->handle( 'POST', array( 'origin' => self::ORIGIN ), $this->post(), '1.2.3.4' );
        $this->assertSame( 303, $plain['status'] );
        $this->assertSame( 'https://owner.github.io/repo/#exp-static-form-sent', $plain['redirect'] );
    }

    /** FE-08 */
    public function testPreflight()
    {
        $answer = $this->endpoint()->handle( 'OPTIONS', array( 'origin' => 'https://www.example.com' ), array(), '1.2.3.4' );
        $this->assertSame( 204, $answer['status'] );
        $this->assertSame( 'https://www.example.com', $answer['headers']['Access-Control-Allow-Origin'] );
        $this->assertSame( 'POST, OPTIONS', $answer['headers']['Access-Control-Allow-Methods'] );
        $this->assertArrayNotHasKey( 'Access-Control-Allow-Credentials', $answer['headers'] );
        $this->assertSame( 403, $this->endpoint()->handle( 'OPTIONS', array( 'origin' => 'https://other.github.io' ), array(), '1.2.3.4' )['status'] );
        $get = $this->endpoint()->handle( 'GET', array(), array(), '1.2.3.4' );
        $this->assertSame( 405, $get['status'] );
        $this->assertSame( 'POST, OPTIONS', $get['headers']['Allow'] );
    }

    /** FE-09 */
    public function testAudit()
    {
        $this->endpoint()->handle( 'POST', array( 'origin' => 'https://evil.example.net' ), $this->post(), '1.2.3.4' );
        $this->endpoint()->handle( 'POST', array( 'origin' => self::ORIGIN ), $this->post(), '1.2.3.4' );
        $this->assertSame( 'system.staticpublish.form', $this->events[0][0] );
        $this->assertSame( array( 'failure', 'success' ), array_column( $this->events, 1 ) );
    }
}
