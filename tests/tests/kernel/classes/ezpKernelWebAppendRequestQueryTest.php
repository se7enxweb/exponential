<?php
/**
 * ezpKernelWeb::appendRequestQuery() carries the query of the current request over a
 * module redirect, but only to targets on the own host: the host of the request or a trusted
 * host (the configured SiteURL host).
 *
 * A redirect used to append the query to every target: a sealed payment window URL on
 * another host broke on tracking parameters such as gclid or _gl, request data leaked to
 * external hosts, and a target that already had a query got a second "?". A protocol-relative
 * target (//host) or any other scheme counts as a host target too, the host comparison ignores
 * case, port and a trailing dot and matches IDN hosts in their punycode form, and the
 * configured SiteURL host (the trusted hosts) always counts as own host.
 *
 * The method is static and pure, so these tests need no kernel, database or siteaccess.
 * Each case is one row of appendRequestQueryProvider(); a new rule is a new row.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/ezpKernelWebAppendRequestQueryTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class ezpKernelWebAppendRequestQueryTest extends PHPUnit\Framework\TestCase
{
    /**
     * @return array redirect target, query of the current request, current host, expected target
     */
    public static function appendRequestQueryProvider()
    {
        return array(
            'empty query leaves the target alone' =>
                array( '/content/view/full/2', '', 'www.example.com', '/content/view/full/2' ),
            'a bare "?" counts as empty' =>
                array( '/content/view/full/2', '?', 'www.example.com', '/content/view/full/2' ),
            'relative target gets the query' =>
                array( '/user/login', '?from=shop', 'www.example.com', '/user/login?from=shop' ),
            'query without the leading "?"' =>
                array( '/user/login', 'from=shop', 'www.example.com', '/user/login?from=shop' ),
            'target with a query joins with "&"' =>
                array( '/shop/basket?step=2', '?from=shop', 'www.example.com', '/shop/basket?step=2&from=shop' ),
            'fragment stays at the end' =>
                array( '/content/view/full/2#comments', '?from=shop', 'www.example.com', '/content/view/full/2?from=shop#comments' ),
            'fragment after an existing query' =>
                array( '/shop/basket?step=2#total', '?from=shop', 'www.example.com', '/shop/basket?step=2&from=shop#total' ),
            'absolute target on the own host' =>
                array( 'https://www.example.com/user/login', '?from=shop', 'www.example.com', 'https://www.example.com/user/login?from=shop' ),
            'own host given with a port' =>
                array( 'https://www.example.com/user/login', '?from=shop', 'www.example.com:443', 'https://www.example.com/user/login?from=shop' ),
            'own host in a different case' =>
                array( 'https://WWW.Example.com/user/login', '?from=shop', 'www.example.com', 'https://WWW.Example.com/user/login?from=shop' ),
            'plain http target on the own host' =>
                array( 'http://www.example.com/user/login', '?from=shop', 'www.example.com', 'http://www.example.com/user/login?from=shop' ),
            'foreign host is left untouched' =>
                array( 'https://pay.example.net/checkout?seal=abc', '?gclid=123&_gl=xyz', 'www.example.com', 'https://pay.example.net/checkout?seal=abc' ),
            'foreign host without a query stays without one' =>
                array( 'https://pay.example.net/checkout', '?gclid=123', 'www.example.com', 'https://pay.example.net/checkout' ),
            'a www variant of the own host counts as foreign' =>
                array( 'https://www.example.com/user/login', '?from=shop', 'example.com', 'https://www.example.com/user/login' ),
            'a sub domain of the own host counts as foreign' =>
                array( 'https://shop.example.com/basket', '?from=shop', 'example.com', 'https://shop.example.com/basket' ),
            'own host with a trailing dot' =>
                array( 'https://www.example.com./user/login', '?from=shop', 'www.example.com', 'https://www.example.com./user/login?from=shop' ),
            'own host on another port' =>
                array( 'https://www.example.com:8080/user/login', '?from=shop', 'www.example.com:8080', 'https://www.example.com:8080/user/login?from=shop' ),
            'own host name inside a foreign host' =>
                array( 'https://www.example.com.evil.example/a', '?from=shop', 'www.example.com', 'https://www.example.com.evil.example/a' ),
            'own host name as user info of a foreign host' =>
                array( 'https://www.example.com@evil.example/a', '?from=shop', 'www.example.com', 'https://www.example.com@evil.example/a' ),
            'own host name in the path of a foreign host' =>
                array( 'https://evil.example/@www.example.com', '?from=shop', 'www.example.com', 'https://evil.example/@www.example.com' ),
            'protocol-relative target on a foreign host' =>
                array( '//evil.example/a', '?from=shop', 'www.example.com', '//evil.example/a' ),
            'protocol-relative target without a path' =>
                array( '//evil.example', '?from=shop', 'www.example.com', '//evil.example' ),
            'protocol-relative target on the own host' =>
                array( '//www.example.com/a', '?from=shop', 'www.example.com', '//www.example.com/a?from=shop' ),
            'other scheme: ftp' =>
                array( 'ftp://evil.example/a', '?from=shop', 'www.example.com', 'ftp://evil.example/a' ),
            'other scheme: mailto' =>
                array( 'mailto:a@b.example', '?from=shop', 'www.example.com', 'mailto:a@b.example' ),
            'other scheme: javascript' =>
                array( 'javascript:alert(1)', '?from=shop', 'www.example.com', 'javascript:alert(1)' ),
        );
    }

    /**
     * @dataProvider appendRequestQueryProvider
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'appendRequestQueryProvider' )]
    public function testAppendRequestQuery( $redirectURI, $queryString, $currentHost, $expected )
    {
        $this->assertSame( $expected, ezpKernelWeb::appendRequestQuery( $redirectURI, $queryString, $currentHost ) );
    }

    /**
     * @return array redirect target, query of the current request, current host, trusted hosts, expected target
     */
    public static function trustedHostsProvider()
    {
        return array(
            'protocol-relative target on another host is left untouched' =>
                array( '//pay.example.net/checkout', '?gclid=123', 'www.example.com', array(), '//pay.example.net/checkout' ),
            'protocol-relative target on the own host gets the query' =>
                array( '//www.example.com/user/login', '?from=shop', 'www.example.com', array(), '//www.example.com/user/login?from=shop' ),
            'another scheme on the own host gets the query' =>
                array( 'ftp://www.example.com/file', '?from=shop', 'www.example.com', array(), 'ftp://www.example.com/file?from=shop' ),
            'a target without a host is left untouched' =>
                array( 'mailto:shop@example.com', '?from=shop', 'www.example.com', array(), 'mailto:shop@example.com' ),
            'a trusted host counts as the own host' =>
                array( 'https://www.example.com/user/login', '?from=shop', 'example.com', array( 'www.example.com' ), 'https://www.example.com/user/login?from=shop' ),
            'a trusted host is compared without port and case' =>
                array( 'https://WWW.example.com/user/login', '?from=shop', 'example.com', array( 'www.example.com:8080' ), 'https://WWW.example.com/user/login?from=shop' ),
            'an empty trusted host matches nothing' =>
                array( 'https://pay.example.net/checkout', '?gclid=123', 'www.example.com', array( '' ), 'https://pay.example.net/checkout' ),
            'a trusted host does not make other hosts own' =>
                array( 'https://pay.example.net/checkout', '?gclid=123', 'example.com', array( 'www.example.com' ), 'https://pay.example.net/checkout' ),
            'a trailing dot of the target host is ignored' =>
                array( 'https://www.example.com./user/login', '?from=shop', 'www.example.com', array(), 'https://www.example.com./user/login?from=shop' ),
            'an IPv6 host with a port is the own host' =>
                array( 'http://[::1]:8080/user/login', '?from=shop', '[::1]:8080', array(), 'http://[::1]:8080/user/login?from=shop' ),
            'the SiteURL host counts even when the current host differs' =>
                array( 'https://www.example.com/a', 'gclid=x', 'evil.example', array( 'www.example.com' ), 'https://www.example.com/a?gclid=x' ),
            // a spoofed X-Forwarded-Host only changes what counts as own host for that very request, so the
            // client only ever gets its own query back
            'a spoofed current host counts for its own request' =>
                array( 'https://evil.example/a', 'gclid=x', 'evil.example', array(), 'https://evil.example/a?gclid=x' ),
            // a host-matched siteaccess on another host (edit.<host>) loses the query, on purpose
            'a siteaccess on another host is foreign' =>
                array( 'https://edit.example.com/content/view/full/2', 'gclid=x', 'www.example.com', array( 'www.example.com' ), 'https://edit.example.com/content/view/full/2' ),
        );
    }

    /**
     * @dataProvider trustedHostsProvider
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'trustedHostsProvider' )]
    public function testTrustedHosts( $redirectURI, $queryString, $currentHost, $trustedHosts, $expected )
    {
        $this->assertSame( $expected, ezpKernelWeb::appendRequestQuery( $redirectURI, $queryString, $currentHost, $trustedHosts ) );
    }

    /**
     * An internationalised own host matches its punycode form in the target.
     */
    public function testInternationalisedHost()
    {
        if ( !function_exists( 'idn_to_ascii' ) )
        {
            $this->markTestSkipped( 'needs the intl extension (idn_to_ascii)' );
        }
        $this->assertSame( 'https://xn--mnchen-3ya.example/user/login?from=shop',
                           ezpKernelWeb::appendRequestQuery( 'https://xn--mnchen-3ya.example/user/login', '?from=shop', "m\xC3\xBCnchen.example" ) );
        // and only that host: a punycode target is foreign to any other host
        $this->assertSame( 'https://xn--mnchen-3ya.example/user/login',
                           ezpKernelWeb::appendRequestQuery( 'https://xn--mnchen-3ya.example/user/login', '?from=shop', 'www.example.com' ) );
    }

    /**
     * The query is appended once: no second "?" on a target that already has a query.
     */
    public function testNoSecondQuestionMark()
    {
        $result = ezpKernelWeb::appendRequestQuery( '/shop/basket?step=2', '?from=shop', 'www.example.com' );
        $this->assertSame( 1, substr_count( $result, '?' ) );
    }

    /**
     * Non-string arguments are taken as strings, as the redirect passes whatever
     * eZSys::queryString() and eZSys::hostname() return.
     */
    public function testNullArgumentsAreTakenAsStrings()
    {
        $this->assertSame( '/user/login', ezpKernelWeb::appendRequestQuery( '/user/login', null, null ) );
    }
}
