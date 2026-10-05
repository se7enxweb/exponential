<?php
/**
 * ezpKernelWeb::appendRequestQuery(): a module redirect carries the request query only to the own host.
 *
 * Pure function, no database. Run: php vendor/bin/phpunit tests/tests/kernel/classes/ezpKernelWebAppendQueryTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class ezpKernelWebAppendQueryTest extends PHPUnit\Framework\TestCase
{
    const HOST = 'alpha.se7enx.com';

    private function run_( $target, $query = 'gclid=x', $host = self::HOST, array $trusted = array() )
    {
        return ezpKernelWeb::appendRequestQuery( $target, $query, $host, $trusted );
    }

    public function testRelativeTargetKeepsQuery()
    {
        $this->assertSame( '/content/view/full/2?gclid=x', $this->run_( '/content/view/full/2' ) );
        $this->assertSame( '/a?gclid=x', $this->run_( '/a', '?gclid=x' ) );
    }

    public function testEmptyQueryChangesNothing()
    {
        $this->assertSame( '/a', $this->run_( '/a', '' ) );
        $this->assertSame( 'https://other.example/a', $this->run_( 'https://other.example/a', '' ) );
    }

    public function testSameHostAndPort()
    {
        $this->assertSame( 'https://alpha.se7enx.com/a?gclid=x', $this->run_( 'https://alpha.se7enx.com/a' ) );
        $this->assertSame( 'https://alpha.se7enx.com:8080/a?gclid=x', $this->run_( 'https://alpha.se7enx.com:8080/a', 'gclid=x', 'alpha.se7enx.com:8080' ) );
        $this->assertSame( 'http://alpha.se7enx.com:8080/a?gclid=x', $this->run_( 'http://alpha.se7enx.com:8080/a' ) );
    }

    public function testUppercaseHostAndTrailingDot()
    {
        $this->assertSame( 'https://ALPHA.se7enx.com/a?gclid=x', $this->run_( 'https://ALPHA.se7enx.com/a', 'gclid=x', 'Alpha.Se7enx.com' ) );
        $this->assertSame( 'https://alpha.se7enx.com./a?gclid=x', $this->run_( 'https://alpha.se7enx.com./a' ) );
    }

    public function testOtherHostKeepsTargetUntouched()
    {
        foreach ( array( 'https://other.example/pay?seal=1', 'https://edit.alpha.se7enx.com/a', 'https://alpha.se7enx.com.evil.example/a',
                         'https://evil.example/@alpha.se7enx.com', 'https://alpha.se7enx.com@evil.example/a' ) as $target )
        {
            $this->assertSame( $target, $this->run_( $target ), $target );
        }
    }

    public function testProtocolRelativeAndOtherSchemes()
    {
        $this->assertSame( '//evil.example/a', $this->run_( '//evil.example/a' ) );
        $this->assertSame( '//evil.example', $this->run_( '//evil.example' ) );
        $this->assertSame( '//alpha.se7enx.com/a?gclid=x', $this->run_( '//alpha.se7enx.com/a' ) );
        $this->assertSame( 'ftp://evil.example/a', $this->run_( 'ftp://evil.example/a' ) );
        $this->assertSame( 'mailto:a@b.example', $this->run_( 'mailto:a@b.example' ) );
        $this->assertSame( 'javascript:alert(1)', $this->run_( 'javascript:alert(1)' ) );
    }

    public function testExistingQueryJoinsWithAmpersand()
    {
        $this->assertSame( '/a?b=1&gclid=x', $this->run_( '/a?b=1' ) );
        $this->assertSame( 'https://alpha.se7enx.com/a?b=1&gclid=x', $this->run_( 'https://alpha.se7enx.com/a?b=1' ) );
    }

    public function testFragmentStaysLast()
    {
        $this->assertSame( '/a?gclid=x#top', $this->run_( '/a#top' ) );
        $this->assertSame( '/a?b=1&gclid=x#top', $this->run_( '/a?b=1#top' ) );
    }

    public function testIdnHostMatchesItsPunycodeForm()
    {
        if ( !function_exists( 'idn_to_ascii' ) )
        {
            $this->markTestSkipped( 'intl is not loaded' );
        }
        $this->assertSame( 'https://xn--mnchen-3ya.example/a?gclid=x', $this->run_( 'https://xn--mnchen-3ya.example/a', 'gclid=x', 'münchen.example' ) );
        $this->assertSame( 'https://xn--mnchen-3ya.example/a', $this->run_( 'https://xn--mnchen-3ya.example/a' ) );
    }

    /** A spoofed X-Forwarded-Host only changes what counts as own host for that very request; the configured SiteURL host always counts. */
    public function testTrustedHostsAndSpoofedForwardedHost()
    {
        $this->assertSame( 'https://alpha.se7enx.com/a?gclid=x', $this->run_( 'https://alpha.se7enx.com/a', 'gclid=x', 'evil.example', array( 'alpha.se7enx.com' ) ) );
        // a target on the spoofed host is not in the trusted list, only the current host: documented, harmless (the client sent its own query)
        $this->assertSame( 'https://evil.example/a?gclid=x', $this->run_( 'https://evil.example/a', 'gclid=x', 'evil.example' ) );
        $this->assertSame( 'https://other.example/a', $this->run_( 'https://other.example/a', 'gclid=x', 'evil.example', array( 'alpha.se7enx.com' ) ) );
    }

    public function testHostMatchedSiteaccessOnAnotherHostLosesTheQuery()
    {
        $this->assertSame( 'https://edit.alpha.se7enx.com/content/view/full/2', $this->run_( 'https://edit.alpha.se7enx.com/content/view/full/2' ) );
    }
}
