<?php
/**
 * The decisions of the link check (expLinkCheck), without a network or a database: the fetcher, the resolver, the
 * content, alias and MX lookups, the clock and sleeping are stand-ins.
 *
 *  LC-01 - The kind of an address follows its scheme
 *  LC-02 - https is tested like http: a 2xx answer is valid, a 404 invalid, 429, 503 and a refusal (403) leave the
 *          state as it is
 *  LC-03 - A failed HEAD is asked again with GET; a HEAD that works sends no GET
 *  LC-04 - Redirects are followed, relative ones too, up to MaxRedirects
 *  LC-05 - Private, loopback, link-local and reserved addresses are never requested, also behind a redirect,
 *          unless AllowedPrivateHosts names the host, the address or its range
 *  LC-06 - The request is pinned to the address that was checked; an unknown host is invalid without a request
 *  LC-07 - Requests to the same host wait HostDelay; other hosts do not
 *  LC-08 - Links to content are judged by their target: published and visible is valid, hidden or missing invalid
 *  LC-09 - mailto: is valid when the domain has a mail server; paths are looked up as aliases, then on SiteURL
 *  LC-10 - file: is never tested; settings come with their defaults; a recent check is not repeated
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expLinkCheckTest extends PHPUnit\Framework\TestCase
{
    /** @var array the requests the stand-in fetcher was asked for: method url ip */
    private $asked = array();
    /** @var array url => answer, or url => array( 'HEAD' => answer, 'GET' => answer ) */
    private $answers = array();
    private $slept = array();
    private $now = 1000.0;

    private function checker( array $settings = array(), array $extra = array() )
    {
        $this->asked = array();
        $this->slept = array();
        $dns = isset( $extra['dns'] ) ? $extra['dns'] : array();
        return new expLinkCheck( $settings + array( 'HostDelay' => 0 ), $extra + array(
            'fetcher' => function ( $method, $url, $ip ) {
                $this->asked[] = $method . ' ' . $url . ' ' . $ip;
                $answer = isset( $this->answers[$url] ) ? $this->answers[$url] : array( 'status' => 0, 'error' => 'unreachable' );
                if ( isset( $answer['HEAD'] ) || isset( $answer['GET'] ) )
                    $answer = $answer[$method];
                return $answer;
            },
            'resolver' => function ( $host ) use ( $dns ) { return isset( $dns[$host] ) ? $dns[$host] : array( '93.184.216.34' ); },
            'content' => function ( $kind, $id ) { return $id === 1 ? 'visible' : ( $id === 2 ? 'hidden' : 'missing' ); },
            'internal' => function ( $path ) { return $path === '/about' || $path === 'user/login'; },
            'mx' => function ( $domain ) { return $domain === 'example.org'; },
            'clock' => function () { return $this->now; },
            'sleep' => function ( $micro ) { $this->slept[] = $micro; $this->now += $micro / 1000000; },
        ) );
    }

    /** LC-01 */
    public function testKindFollowsScheme()
    {
        $this->assertSame( 'web', expLinkCheck::kind( 'https://example.org/' ) );
        $this->assertSame( 'web', expLinkCheck::kind( 'HTTP://example.org/' ) );
        $this->assertSame( 'web', expLinkCheck::kind( 'ftp://example.org/x' ) );
        $this->assertSame( 'mailto', expLinkCheck::kind( 'mailto:a@example.org' ) );
        $this->assertSame( 'content', expLinkCheck::kind( 'ezlocation://506' ) );
        $this->assertSame( 'content', expLinkCheck::kind( 'eznode://2' ) );
        $this->assertSame( 'content', expLinkCheck::kind( 'ezobject://57' ) );
        $this->assertSame( 'file', expLinkCheck::kind( 'file:///etc/passwd' ) );
        $this->assertSame( 'internal', expLinkCheck::kind( '/about' ) );
        $this->assertSame( 'internal', expLinkCheck::kind( 'tel:+4912345' ) );
    }

    /** LC-02 */
    public function testHttpsIsTested()
    {
        $this->answers = array( 'https://example.org/ok' => array( 'status' => 200 ),
                                'https://example.org/gone' => array( 'status' => 404 ),
                                'https://example.org/busy' => array( 'status' => 429 ),
                                'https://example.org/down' => array( 'status' => 503 ),
                                'https://example.org/bots' => array( 'status' => 403 ) );
        $checker = $this->checker();
        $this->assertSame( expLinkCheck::VALID, $checker->check( 'https://example.org/ok' )['result'] );
        $this->assertSame( expLinkCheck::INVALID, $checker->check( 'https://example.org/gone' )['result'] );
        $this->assertSame( expLinkCheck::UNKNOWN, $checker->check( 'https://example.org/busy' )['result'] );
        $this->assertSame( expLinkCheck::UNKNOWN, $checker->check( 'https://example.org/down' )['result'] );
        $this->assertSame( expLinkCheck::UNKNOWN, $checker->check( 'https://example.org/bots' )['result'] );
        $this->assertSame( expLinkCheck::INVALID, $checker->check( 'https://example.org/nothing' )['result'] );
    }

    /** LC-03 */
    public function testHeadFailureIsAskedAgainWithGet()
    {
        $this->answers = array( 'https://example.org/nohead' => array( 'HEAD' => array( 'status' => 405 ), 'GET' => array( 'status' => 200 ) ),
                                'https://example.org/fine' => array( 'status' => 204 ) );
        $checker = $this->checker();
        $this->assertSame( expLinkCheck::VALID, $checker->check( 'https://example.org/nohead' )['result'] );
        $this->assertSame( array( 'HEAD https://example.org/nohead 93.184.216.34', 'GET https://example.org/nohead 93.184.216.34' ), $this->asked );
        $checker = $this->checker();
        $checker->check( 'https://example.org/fine' );
        $this->assertCount( 1, $this->asked );
        $this->assertSame( 1, $checker->requests );
    }

    /** LC-04 */
    public function testRedirectsAreFollowedToALimit()
    {
        $this->answers = array( 'http://example.org/a' => array( 'status' => 301, 'location' => 'https://example.org/b' ),
                                'https://example.org/b' => array( 'status' => 302, 'location' => '/c?x=1' ),
                                'https://example.org/c?x=1' => array( 'status' => 302, 'location' => 'd' ),
                                'https://example.org/d' => array( 'status' => 200 ) );
        $answer = $this->checker()->check( 'http://example.org/a' );
        $this->assertSame( expLinkCheck::VALID, $answer['result'] );
        $this->assertStringContainsString( '3 redirects', $answer['reason'] );
        $this->assertSame( expLinkCheck::INVALID, $this->checker( array( 'MaxRedirects' => 2 ) )->check( 'http://example.org/a' )['result'] );
        $this->assertSame( 'http://other.example/x', expLinkCheck::absoluteURL( 'http://example.org/a/b', '//other.example/x' ) );
        $this->assertSame( 'http://example.org:8080/a/c', expLinkCheck::absoluteURL( 'http://example.org:8080/a/b', 'c' ) );
    }

    /** LC-05 */
    public function testPrivateAddressesAreNotRequested()
    {
        foreach ( array( '127.0.0.1', '10.1.2.3', '172.16.5.4', '192.168.1.1', '169.254.169.254', '100.64.0.1', '0.0.0.0',
                         '::1', 'fe80::1', 'fd00::1', '::ffff:127.0.0.1', 'not-an-ip' ) as $ip )
            $this->assertTrue( expLinkCheck::isPrivateAddress( $ip ), $ip );
        foreach ( array( '93.184.216.34', '8.8.8.8', '2606:2800:220:1:248:1893:25c8:1946' ) as $ip )
            $this->assertFalse( expLinkCheck::isPrivateAddress( $ip ), $ip );

        $checker = $this->checker( array(), array( 'dns' => array( 'intranet.example' => array( '10.0.0.5' ) ) ) );
        $this->assertSame( expLinkCheck::UNKNOWN, $checker->check( 'http://intranet.example/' )['result'] );
        $this->assertSame( expLinkCheck::UNKNOWN, $checker->check( 'http://169.254.169.254/latest/meta-data/' )['result'] );
        $this->assertSame( array(), $this->asked );

        // behind a redirect too
        $this->answers = array( 'https://example.org/jump' => array( 'status' => 302, 'location' => 'http://127.0.0.1/admin' ) );
        $checker = $this->checker();
        $this->assertSame( expLinkCheck::UNKNOWN, $checker->check( 'https://example.org/jump' )['result'] );
        $this->assertCount( 1, $this->asked );

        // allowed by name, by address or by range
        $this->answers = array( 'http://intranet.example/' => array( 'status' => 200 ), 'http://10.0.0.5/' => array( 'status' => 200 ) );
        $dns = array( 'dns' => array( 'intranet.example' => array( '10.0.0.5' ) ) );
        $this->assertSame( expLinkCheck::VALID, $this->checker( array( 'AllowedPrivateHosts' => array( 'intranet.example' ) ), $dns )->check( 'http://intranet.example/' )['result'] );
        $this->assertSame( expLinkCheck::VALID, $this->checker( array( 'AllowedPrivateHosts' => array( '10.0.0.5' ) ), $dns )->check( 'http://intranet.example/' )['result'] );
        $this->assertSame( expLinkCheck::VALID, $this->checker( array( 'AllowedPrivateHosts' => array( '10.0.0.0/8' ) ), $dns )->check( 'http://10.0.0.5/' )['result'] );
        $this->assertSame( expLinkCheck::UNKNOWN, $this->checker( array( 'AllowedPrivateHosts' => array( '10.1.0.0/16' ) ), $dns )->check( 'http://10.0.0.5/' )['result'] );
    }

    /** LC-06 */
    public function testRequestIsPinnedAndUnknownHostIsInvalid()
    {
        $this->answers = array( 'https://example.org/' => array( 'status' => 200 ) );
        $checker = $this->checker( array(), array( 'dns' => array( 'example.org' => array( '93.184.216.34' ), 'nowhere.example' => array() ) ) );
        $checker->check( 'https://example.org/' );
        $this->assertSame( array( 'HEAD https://example.org/ 93.184.216.34' ), $this->asked );
        $answer = $checker->check( 'https://nowhere.example/' );
        $this->assertSame( expLinkCheck::INVALID, $answer['result'] );
        $this->assertStringContainsString( 'host not found', $answer['reason'] );
        $this->assertCount( 1, $this->asked );
    }

    /** LC-07 */
    public function testSameHostWaits()
    {
        $this->answers = array( 'https://example.org/1' => array( 'status' => 200 ), 'https://example.org/2' => array( 'status' => 200 ),
                                'https://other.example/1' => array( 'status' => 200 ) );
        $checker = $this->checker( array( 'HostDelay' => 1000 ) );
        $checker->check( 'https://example.org/1' );
        $checker->check( 'https://other.example/1' );
        $this->assertSame( array(), $this->slept );
        $checker->check( 'https://example.org/2' );
        $this->assertSame( array( 1000000 ), $this->slept );
    }

    /** LC-08 */
    public function testContentLinksAreJudgedByTheirTarget()
    {
        $checker = $this->checker();
        $this->assertSame( expLinkCheck::VALID, $checker->check( 'ezlocation://1' )['result'] );
        $this->assertSame( expLinkCheck::INVALID, $checker->check( 'eznode://2' )['result'] );
        $this->assertSame( expLinkCheck::INVALID, $checker->check( 'ezobject://3' )['result'] );
        $this->assertSame( array(), $this->asked );
    }

    /** LC-09 */
    public function testMailAndPaths()
    {
        $checker = $this->checker();
        $this->assertSame( expLinkCheck::VALID, $checker->check( 'mailto:someone@example.org?subject=hi' )['result'] );
        $this->assertSame( expLinkCheck::INVALID, $checker->check( 'mailto:someone@nomail.example' )['result'] );
        $this->assertSame( expLinkCheck::INVALID, $checker->check( 'mailto:nobody' )['result'] );
        $this->assertSame( expLinkCheck::VALID, $checker->check( '/about' )['result'] );
        $this->assertSame( expLinkCheck::INVALID, $checker->check( '/missing' )['result'] );
        $this->assertSame( array(), $this->asked );

        $this->assertSame( 'content/dashboard', expLinkCheck::internalPath( '/admin/content/dashboard?x=1#top', array( 'admin', 'site' ) ) );
        $this->assertSame( 'about', expLinkCheck::internalPath( '/about/', array( 'admin' ) ) );
        $this->assertSame( '', expLinkCheck::internalPath( '/site', array( 'site' ) ) );

        // a path that is no alias is tried on the site's own address, which may be private
        $this->answers = array( 'http://site.local/missing' => array( 'status' => 200 ) );
        $checker = $this->checker( array( 'SiteURL' => array( 'http://site.local' ) ), array( 'dns' => array( 'site.local' => array( '127.0.0.1' ) ) ) );
        $this->assertSame( expLinkCheck::VALID, $checker->check( '/missing' )['result'] );
    }

    /** LC-10 */
    public function testFileSettingsAndRecheck()
    {
        $this->assertSame( expLinkCheck::UNKNOWN, $this->checker()->check( 'file:///etc/passwd' )['result'] );
        $this->assertSame( array(), $this->asked );
        $defaults = expLinkCheck::defaults();
        $this->assertSame( 15, $defaults['Timeout'] );
        $this->assertSame( 5, $defaults['MaxRedirects'] );
        $this->assertSame( 1000, $defaults['HostDelay'] );
        $this->assertSame( 72000, $defaults['RecheckInterval'] );
        $this->assertTrue( \Exponential\Cronjob\Kernel\Linkcheck::isRecent( 990, 1000, 100 ) );
        $this->assertFalse( \Exponential\Cronjob\Kernel\Linkcheck::isRecent( 800, 1000, 100 ) );
        $this->assertFalse( \Exponential\Cronjob\Kernel\Linkcheck::isRecent( 0, 1000, 100 ) );
        $this->assertFalse( \Exponential\Cronjob\Kernel\Linkcheck::isRecent( 990, 1000, 0 ) );
    }
}
