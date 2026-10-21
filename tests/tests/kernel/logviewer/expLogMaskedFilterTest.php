<?php
/**
 * The request and client address filters of the log viewer compare what the viewer is shown, on every path that
 * filters (doc/eng-us/features/6.0/log-viewer.md, "Search"), without a database.
 *
 *  LV-220  expLogRedactor::ip(): IPv4, IPv6 and its compressed forms (2001::7334, fe80::1, ::1), a value written masked
 *          already; a "Class::method" in a text is left alone
 *  LV-221  expLogRedactor::ipFilter() and ipFilterOf(): a filter as a link writes it; masked(); criteria() takes an
 *          address copied as shown (203.0.x.x, 2001:db8:x:x) and still refuses anything else
 *  LV-222  the error log: a masked viewer finds by what is shown (also uppercase and compressed IPv6), never by a guess
 *          at the hidden part; an unmasked viewer by what is written, and by a masked value copied from a link
 *  LV-223  404.log and security.log (addresses written masked): the address and the request as shown, for both viewers
 *  LV-224  the archives (.N and .N.gz) are filtered the same way
 *  LV-225  the digest: a log grouped by uri links with the request filter, masked, and that link finds the group's
 *          entries; the other groupings link as before
 *  LV-226  the permalink to share: the filters as the masks show them (never the raw address or e-mail address), and
 *          opened by a masked or an unmasked viewer it finds the entry; with MaskIPs disabled the address stays
 *  LV-227  without a redactor (the request lines of one entry, the command line before a viewer) the filters compare
 *          what is written, as before
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expLogViewerTestFixtures.php';

class expLogMaskedFilterTest extends PHPUnit\Framework\TestCase
{
    const START = 1792195200;

    /** @var string */
    private $root;

    protected function setUp(): void
    {
        $this->root = expLogViewerTestFixtures::root( 'lvmask' );
    }

    protected function tearDown(): void
    {
        expLogViewerTestFixtures::remove( $this->root );
    }

    private function config( array $values = array() )
    {
        $values = array_replace_recursive( array( 'TailSettings' => array( 'ChunkCacheTTL' => 0, 'InitialBytes' => 1024 ),
                                                  'RedactionSettings' => array( 'SecretSettings' => array(), 'SecretGroups' => array() ) ), $values );
        return expLogViewerTestFixtures::config( $this->root, $values );
    }

    private function viewer( $unmasked = false, array $values = array() )
    {
        $config = $this->config( $values );
        return new expLogViewer( $config, new expLogFileLocator( $config, 'var/log' ), $unmasked, 'test-salt' );
    }

    private static function debugLine( $t, $ip, $url, $message )
    {
        return '[ ' . date( 'M d Y H:i:s', $t ) . " ] [$ip] [admin] [$url] $message\n";
    }

    private static function notFound( $t, $uri, $ip )
    {
        return '[ ' . date( 'M d Y H:i:s', $t ) . ' ] [' . $ip . '] 404 GET ' . $uri . ' siteaccess=site reason=module:x'
               . ' node=- referrer=- ua=3f2a9c1b "Mozilla/5.0"' . "\n";
    }

    private static function security( $t, $uri, $ip )
    {
        return '[ ' . date( 'M d Y H:i:s', $t ) . ' ] [' . $ip . '] probe/wp-login confidence=high 404 GET ' . $uri
               . ' siteaccess=site namespace=' . $uri . ' ua=3f2a9c1b "curl/8.5.0"' . "\n";
    }

    /** The shown requests a viewer's search of a file finds */
    private function found( expLogViewer $v, $id, array $in, $rotations = false )
    {
        $r = $v->search( $v->searchFiles( $id, 'file', $rotations ), expLogSearch::criteria( $in ) );
        $this->assertSame( '', $r['error'] );
        return array_column( $r['entries'], 'u' );
    }

    /** The state of a permalink path, read as the view reads its parameters */
    private static function stateOf( $path )
    {
        $parts = explode( '/', substr( $path, strlen( expLogPermalink::VIEW ) + 1 ) );
        array_shift( $parts );
        $params = array();
        for ( $i = 0; $i + 1 < count( $parts ); $i += 2 )
            $params[trim( $parts[$i], '()' )] = urldecode( $parts[$i + 1] ); // as the kernel decodes the address
        return expLogPermalink::state( $params, true );
    }

    /** LV-220 */
    public function testTheAddressFieldAsShown()
    {
        $r = new expLogRedactor( true, true, true );
        $this->assertSame( '203.0.x.x', $r->ip( '203.0.113.7' ) );
        $this->assertSame( '2001:db8:x:x', $r->ip( '2001:db8:85a3::8a2e:370:7334' ) );
        $this->assertSame( '2001:db8:x:x', $r->ip( '2001:db8::1' ) );
        $this->assertSame( '2001:DB8:x:x', $r->ip( '2001:DB8:85A3::1' ) );
        $this->assertSame( '2001::x', $r->ip( '2001::7334' ), 'one group before the ::' );
        $this->assertSame( 'fe80::x', $r->ip( 'fe80::1' ) );
        $this->assertSame( '::x', $r->ip( '::1' ) );
        $this->assertSame( '203.0.x.x', $r->ip( '203.0.x.x' ), 'written masked already (the request logs)' );
        $this->assertSame( '2001:db8:x:x', $r->ip( '2001:db8:x:x' ) );
        $this->assertSame( 'x', $r->ip( 'x' ) );
        $this->assertSame( '', $r->ip( '' ) );
        $this->assertSame( 'Face::add failed', $r->text( 'Face::add failed' ), 'a static call in a text is no address' );
        $this->assertSame( '2001::7334', ( new expLogRedactor( true, false, true ) )->ip( '2001::7334' ) );
        // what the request logs write is the same form (expRequestLog::formatIp)
        $this->assertSame( '2001::x', expRequestLog::formatIp( '2001::7334' ) );
        $this->assertSame( '203.0.x.x', expRequestLog::formatIp( '203.0.113.7' ) );
    }

    /** LV-221 */
    public function testFiltersAsALinkWritesThem()
    {
        $cases = array( '203.0.113.7' => '203.0.x.x', '203.0.113' => '203.0.x', '203.0.1' => '203.0.x', '203.0.' => '203.0.',
                        '203' => '203', '203.0.x.x' => '203.0.x.x', '2001:db8:85a3::8a2e:370:7334' => '2001:db8:x:x',
                        '2001:db8:85a3' => '2001:db8:x', '2001:db8:' => '2001:db8:', '2001:db8::1' => '2001:db8:x:x',
                        '2001::7334' => '2001::x', '2001::' => '2001::', 'fe80::1' => 'fe80::x', '::1' => '::x', '::' => '::',
                        '2001:db8:x:x' => '2001:db8:x:x', '::ffff:203.0.113.7' => '::x', '' => '' );
        foreach ( $cases as $in => $out )
        {
            $this->assertSame( $out, expLogRedactor::ipFilter( (string)$in ), (string)$in );
            // a masked filter is a start of the address as shown
            if ( substr_count( (string)$in, '.' ) === 3 && strpos( (string)$in, ':' ) === false && strpos( (string)$in, 'x' ) === false )
                $this->assertStringStartsWith( $out, ( new expLogRedactor() )->ip( (string)$in ) );
        }
        $this->assertSame( '203.0.113.7', ( new expLogRedactor( true, false, true ) )->ipFilterOf( '203.0.113.7' ) );
        $this->assertSame( '203.0.x.x', ( new expLogRedactor( false, true, false ) )->ipFilterOf( '203.0.113.7' ) );

        $all = new expLogRedactor( true, true, true, array( 'Sup3rS3cretValue' ) );
        $this->assertSame( $all, $all->masked() );
        $none = new expLogRedactor( false, false, false, array( 'Sup3rS3cretValue' ) );
        $this->assertNotSame( $none, $none->masked() );
        $this->assertSame( $none->masked(), $none->masked(), 'made once' );
        $this->assertSame( '203.0.x.x', $none->masked()->ip( '203.0.113.7' ) );
        $this->assertSame( 'pw ***', $none->masked()->text( 'pw Sup3rS3cretValue' ), 'with the same secret values' );

        foreach ( array( '203.0.x.x', '2001:db8:x:x', '2001::X', '::x', '203.0.' ) as $ok )
        {
            $c = expLogSearch::criteria( array( 'ip' => $ok ) );
            $this->assertSame( array(), $c['invalid'], $ok );
            $this->assertSame( $ok, $c['ip'] );
        }
        foreach ( array( '203.0.113.7; x', 'host.example', '203.0.*', '<x>' ) as $bad )
        {
            $c = expLogSearch::criteria( array( 'ip' => $bad ) );
            $this->assertSame( 'not_an_address', $c['invalid']['ip'], $bad );
            $this->assertSame( '', $c['ip'] );
        }
    }

    /** LV-222 */
    public function testTheErrorLogIsFilteredAsShown()
    {
        $reset = 'https://example.test/user/reset?token=abc123def456&mail=jane@example.com';
        file_put_contents( $this->root . '/var/log/error.log',
                           self::debugLine( self::START, '203.0.113.7', $reset, 'Error: reset failed' )
                           . self::debugLine( self::START + 1, '203.0.200.9', 'https://example.test/news', 'Error: news' )
                           . self::debugLine( self::START + 2, '2001:db8:85a3::8a2e:370:7334', 'https://example.test/v6', 'Error: v6' )
                           . self::debugLine( self::START + 3, '2001::7334', 'https://example.test/short', 'Error: short v6' )
                           . self::debugLine( self::START + 4, '::1', 'https://example.test/local', 'Error: local' ) );
        $id = 'var/log/error.log';
        $masked = $this->viewer();
        $shownReset = 'https://example.test/user/reset?token=***&mail=j***@***.com';

        // as shown: the shown start and the whole shown address, in any case
        $this->assertSame( array( $shownReset, 'https://example.test/news' ), $this->found( $masked, $id, array( 'ip' => '203.0.' ) ) );
        $this->assertSame( array( $shownReset, 'https://example.test/news' ), $this->found( $masked, $id, array( 'ip' => '203.0.x.x' ) ) );
        $this->assertSame( array( $shownReset, 'https://example.test/news' ), $this->found( $masked, $id, array( 'ip' => '203.0.X' ) ) );
        $this->assertSame( array( 'https://example.test/v6' ), $this->found( $masked, $id, array( 'ip' => '2001:DB8:' ) ) );
        $this->assertSame( array( 'https://example.test/v6' ), $this->found( $masked, $id, array( 'ip' => '2001:db8:x:x' ) ) );
        $this->assertSame( array( 'https://example.test/short' ), $this->found( $masked, $id, array( 'ip' => '2001::x' ) ) );
        $this->assertSame( array( 'https://example.test/local' ), $this->found( $masked, $id, array( 'ip' => '::x' ) ) );
        // a guess at the hidden part finds nothing, right or wrong
        foreach ( array( '203.0.1', '203.0.113', '203.0.113.7', '2001:db8:85a3', '2001::7', '2001::7334', '::1' ) as $guess )
            $this->assertSame( array(), $this->found( $masked, $id, array( 'ip' => $guess ) ), $guess );
        // the request: the shown value, copied with its *** too; not a hidden one
        $this->assertSame( array( $shownReset ), $this->found( $masked, $id, array( 'uri' => 'token=***&mail=j***@' ) ) );
        $this->assertSame( array(), $this->found( $masked, $id, array( 'uri' => 'token=abc' ) ) );
        $this->assertSame( array(), $this->found( $masked, $id, array( 'uri' => 'jane@' ) ) );
        // both filters together
        $this->assertSame( array( $shownReset ), $this->found( $masked, $id, array( 'ip' => '203.0.x.x', 'uri' => '/user/reset' ) ) );

        // unmasked: as written; a masked value copied from a link finds as a masked viewer would
        $plain = $this->viewer( true );
        $writtenReset = 'https://example.test/user/reset?token=***&mail=jane@example.com';
        $this->assertSame( array( $writtenReset ), $this->found( $plain, $id, array( 'ip' => '203.0.113.7' ) ) );
        $this->assertSame( array( 'https://example.test/short' ), $this->found( $plain, $id, array( 'ip' => '2001::7' ) ) );
        $this->assertSame( array( 'https://example.test/local' ), $this->found( $plain, $id, array( 'ip' => '::1' ) ) );
        $this->assertSame( array( 'https://example.test/v6' ), $this->found( $plain, $id, array( 'ip' => '2001:db8:85A3:' ) ) );
        $this->assertSame( array( $writtenReset, 'https://example.test/news' ), $this->found( $plain, $id, array( 'ip' => '203.0.x.x' ) ) );
        $this->assertSame( array( 'https://example.test/short' ), $this->found( $plain, $id, array( 'ip' => '2001::x' ) ) );
        $this->assertSame( array( $writtenReset ), $this->found( $plain, $id, array( 'uri' => 'mail=j***@***.com' ) ) );
        $this->assertSame( array( $writtenReset ), $this->found( $plain, $id, array( 'uri' => 'mail=jane@example.com' ) ) );
        $this->assertSame( array(), $this->found( $plain, $id, array( 'uri' => 'token=abc' ) ), 'a token is hidden from every viewer' );
    }

    /** LV-223 */
    public function testTheRequestLogsAreFilteredAsShown()
    {
        file_put_contents( $this->root . '/var/log/404.log',
                           self::notFound( self::START, '/no-such-page?q=***', '203.0.x.x' )
                           . self::notFound( self::START + 1, '/other', '198.51.x.x' )
                           . self::notFound( self::START + 2, '/v6-page', '2001:db8:x:x' )
                           . self::notFound( self::START + 3, '/odd', 'x' ) );
        file_put_contents( $this->root . '/var/log/security.log',
                           self::security( self::START, '/wp-login.php', '203.0.x.x' )
                           . self::security( self::START + 1, '/content/search?SearchText=203.0.x.x', '2001::x' ) );
        foreach ( array( $this->viewer(), $this->viewer( true ) ) as $v )
        {
            $this->assertSame( array( '/no-such-page?q=***' ), $this->found( $v, 'var/log/404.log', array( 'ip' => '203.0.x.x' ) ) );
            $this->assertSame( array( '/no-such-page?q=***' ), $this->found( $v, 'var/log/404.log', array( 'ip' => '203.0.' ) ) );
            $this->assertSame( array( '/other' ), $this->found( $v, 'var/log/404.log', array( 'ip' => '198.51.X.X' ) ) );
            $this->assertSame( array( '/v6-page' ), $this->found( $v, 'var/log/404.log', array( 'ip' => '2001:db8:x:x' ) ) );
            $this->assertSame( array( '/odd' ), $this->found( $v, 'var/log/404.log', array( 'ip' => 'x' ) ) );
            $this->assertSame( array(), $this->found( $v, 'var/log/404.log', array( 'ip' => '203.0.113' ) ), 'never written, never found' );
            $this->assertSame( array( '/no-such-page?q=***' ), $this->found( $v, 'var/log/404.log', array( 'uri' => 'page?q=***' ) ) );
            $this->assertSame( array( '/wp-login.php' ), $this->found( $v, 'var/log/security.log', array( 'ip' => '203.0.x.x' ) ) );
            $this->assertSame( array( '/content/search?SearchText=203.0.x.x' ), $this->found( $v, 'var/log/security.log', array( 'ip' => '2001::x' ) ) );
            $this->assertSame( array( '/content/search?SearchText=203.0.x.x' ),
                               $this->found( $v, 'var/log/security.log', array( 'uri' => 'SearchText=203.0.x.x' ) ) );
        }
    }

    /** LV-224 */
    public function testTheArchivesAreFilteredAsShown()
    {
        file_put_contents( $this->root . '/var/log/error.log', self::debugLine( self::START + 20, '192.0.2.1', 'https://example.test/now', 'Error: now' ) );
        file_put_contents( $this->root . '/var/log/error.log.1', self::debugLine( self::START + 10, '203.0.113.7', 'https://example.test/one', 'Error: one' ) );
        file_put_contents( $this->root . '/var/log/error.log.2.gz', gzencode( self::debugLine( self::START, '203.0.113.8', 'https://example.test/two?mail=jane@example.com', 'Error: two' ) ) );
        file_put_contents( $this->root . '/var/log/404.log.1', self::notFound( self::START, '/archived', '203.0.x.x' ) );
        $masked = $this->viewer();
        $all = $this->found( $masked, 'var/log/error.log', array( 'ip' => '203.0.x.x' ), true );
        sort( $all );
        $this->assertSame( array( 'https://example.test/one', 'https://example.test/two?mail=j***@***.com' ), $all );
        $this->assertSame( array(), $this->found( $masked, 'var/log/error.log', array( 'ip' => '203.0.113' ), true ) );
        $this->assertSame( array(), $this->found( $masked, 'var/log/error.log', array( 'uri' => 'jane@' ), true ) );
        $this->assertSame( array( 'https://example.test/two?mail=j***@***.com' ),
                           $this->found( $masked, 'var/log/error.log', array( 'uri' => 'mail=j***@' ), true ) );
        // one archive opened on its own ((archive)/N)
        $this->assertSame( array( 'https://example.test/one' ), $this->found( $masked, 'var/log/error.log.1', array( 'ip' => '203.0.x.x' ) ) );
        $this->assertSame( array( '/archived' ), $this->found( $masked, 'var/log/404.log.1', array( 'ip' => '203.0.x.x' ) ) );
        $plain = $this->viewer( true );
        $this->assertSame( array( 'https://example.test/two?mail=jane@example.com' ),
                           $this->found( $plain, 'var/log/error.log', array( 'ip' => '203.0.113.8' ), true ) );
    }

    /** LV-225 */
    public function testTheDigestLinksWithTheMaskedRequestFilter()
    {
        $now = time();
        file_put_contents( $this->root . '/var/log/404.log',
                           self::notFound( $now - 30, '/blog/12?mail=***', '203.0.x.x' )
                           . self::notFound( $now - 20, '/blog/13', '198.51.x.x' )
                           . self::notFound( $now - 10, '/shop/1', '203.0.x.x' ) );
        $config = $this->config( array( 'DigestSettings' => array( 'GroupBy' => array( '404' => 'uri', 'security' => 'category-rule' ) ) ) );
        $locator = new expLogFileLocator( $config, 'var/log' );
        $digest = new expLogDigest( $config, new expLogRedactor( true, true, true, array() ) );
        $since = $now - 3600;
        $out = $digest->digestFile( $locator->resolveKey( '404' ), $since );
        $this->assertSame( 'uri', $out['grouping'] );
        $groups = array_column( $out['groups'], null, 'text' );
        $this->assertArrayHasKey( 'GET /blog/#', $groups );
        $link = expLogDigest::link( $groups['GET /blog/#'], $since, $out['grouping'] );
        $this->assertStringContainsString( '/(uri)/~2Fblog~2F', $link );
        $state = self::stateOf( $link );
        $this->assertSame( '/blog/', $state['uri'] );
        $viewer = new expLogViewer( $config, $locator, false, 'test-salt' );
        $r = $viewer->search( $viewer->searchFiles( 'var/log/404.log' ), expLogSearch::criteria( expLogPermalink::criteriaInput( $state ) ) );
        $this->assertSame( array( '/blog/12?mail=***', '/blog/13' ), array_column( $r['entries'], 'u' ) );
        // the other groupings, and a link without the grouping, are as before
        $this->assertStringNotContainsString( '(uri)', expLogDigest::link( $groups['GET /blog/#'], $since ) );
        $this->assertStringNotContainsString( '(uri)', expLogDigest::link( $groups['GET /blog/#'], $since, 'reason' ) );
        $this->assertSame( '/blog/', expLogDigest::uriFilterOf( 'GET /blog/#' ) );
        $this->assertSame( '/a/b', expLogDigest::uriFilterOf( 'POST /a/b' ) );
        $this->assertSame( '', expLogDigest::uriFilterOf( 'GET /#' ), 'no more than /' );
        $this->assertSame( '', expLogDigest::uriFilterOf( 'reason=node:#' ) );
    }

    /** LV-226 */
    public function testTheSharedPermalinkCarriesTheMaskedFilters()
    {
        file_put_contents( $this->root . '/var/log/error.log',
                           self::debugLine( self::START, '203.0.113.7', 'https://example.test/u?mail=jane@example.com', 'Error: a' )
                           . self::debugLine( self::START + 1, '198.51.100.2', 'https://example.test/other', 'Error: b' ) );
        $config = $this->config();
        $state = expLogPermalink::state( array( 'ip' => '203.0.113.7', 'uri' => 'mail=jane@example.com' ), false );
        $share = expLogPermalink::build( 'error', expLogPermalink::shareable( $state, expLogRedactor::fromConfig( $config, false ) ) );
        $this->assertStringNotContainsString( '113', $share );
        $this->assertStringNotContainsString( 'jane', $share );
        $this->assertStringNotContainsString( 'example.com', $share );
        $opened = self::stateOf( $share );
        $this->assertSame( '203.0.x.x', $opened['ip'] );
        $this->assertSame( 'mail=j***@***.com', $opened['uri'] );
        foreach ( array( false, true ) as $unmasked )
        {
            $v = $this->viewer( $unmasked );
            $r = $v->search( $v->searchFiles( 'var/log/error.log' ), expLogSearch::criteria( expLogPermalink::criteriaInput( $opened ) ) );
            $this->assertCount( 1, $r['entries'], $unmasked ? 'unmasked' : 'masked' );
            $this->assertStringContainsString( 'example.test/u?mail=', $r['entries'][0]['u'] );
        }
        // the link of the view itself (the address bar) keeps the filters as typed
        $this->assertStringContainsString( '(ip)/203.0.113.7', expLogPermalink::build( 'error', $state ) );
        // a shared link that is masked already stays as it is
        $this->assertSame( $share, expLogPermalink::build( 'error', expLogPermalink::shareable( $opened, expLogRedactor::fromConfig( $config, false ) ) ) );
        // with MaskIPs disabled every viewer is shown the address, and the link keeps it
        $open = $this->config( array( 'RedactionSettings' => array( 'MaskIPs' => 'disabled' ) ) );
        $kept = expLogPermalink::shareable( $state, expLogRedactor::fromConfig( $open, false ) );
        $this->assertSame( '203.0.113.7', $kept['ip'] );
        $this->assertSame( 'mail=j***@***.com', $kept['uri'] );
        // no filter, no change
        $this->assertSame( expLogPermalink::defaults(), expLogPermalink::shareable( array(), expLogRedactor::fromConfig( $config, false ) ) );
    }

    /** LV-227 */
    public function testWithoutARedactorTheFiltersCompareWhatIsWritten()
    {
        $t = date( 'M d Y H:i:s', self::START );
        $entry = expLogParser::parse( "[ $t ] [203.0.113.7] [admin] [https://example.test/a?token=abc123def456] Error: x\n" )[0];
        $this->assertSame( 1, expLogSearch::entryMatches( $entry, expLogSearch::criteria( array( 'ip' => '203.0.113.7', 'uri' => 'token=abc' ) ) ) );
        $this->assertSame( 0, expLogSearch::entryMatches( $entry, expLogSearch::criteria( array( 'ip' => '203.0.x.x' ) ) ) );
        $masked = new expLogRedactor();
        $this->assertSame( 1, expLogSearch::entryMatches( $entry, expLogSearch::criteria( array( 'ip' => '203.0.x.x', 'uri' => 'token=***' ) ), $masked ) );
        $this->assertSame( 0, expLogSearch::entryMatches( $entry, expLogSearch::criteria( array( 'ip' => '203.0.113.7' ) ), $masked ) );
        $this->assertTrue( expLogSearch::shownStartsWith( $masked, '203.0.113.7', '203.0.x' ) );
        $this->assertFalse( expLogSearch::shownStartsWith( $masked, '203.0.113.7', '203.0.1' ) );
        $this->assertTrue( expLogSearch::shownContains( $masked, '/a?token=abc123def456', 'token=***' ) );
        $this->assertFalse( expLogSearch::shownContains( $masked, '/a?token=abc123def456', 'abc' ) );
    }
}
