<?php
/**
 * The role-aware HTTP cache contract (kernel/private/classes/httpcache).
 *
 *  HC-01 — Keys differ by scheme, host, siteaccess, path and context; query order does not matter
 *  HC-02 — Users with the same roles share a context; other roles, a private context and anonymous never do
 *  HC-03 — A new generation changes every context and makes every entry stale
 *  HC-04 — A tag purge makes the entries carrying the tag stale, and only those
 *  HC-05 — An expired entry is served stale within the window while one request renders it; a purged one never
 *  HC-06 — Placeholders: the form token of each visitor is put back into the stored page
 *  HC-07 — Only listed query parameters are cached
 *  HC-08 — The early exit finds the user in a files session, and treats an unknown session as anonymous
 *  HC-09 — Garbage collection removes expired, purged and orphaned files and keeps current ones
 *  HC-10 — Metadata always points at its own body
 *  HC-11 — The gzip joined from pre-compressed parts is exactly the served page
 *  HC-12 — The siteaccess from the URI (element and map), as eZSiteAccess::match() finds it
 *  HC-13 — The siteaccess from host and URI together (host_uri, every host match method)
 *  HC-14 — MatchOrder, StaticMatch, DefaultAccess; what cannot be known before the kernel is null
 *  HC-15 — Scheme and host as the kernel works them out, behind a load balancer that ends TLS too
 *  HC-16 — A page of a URI-matched siteaccess stored behind a load balancer is found by the early exit and the web server
 *
 * No database, no kernel: the contract is pure PHP by design.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 * @group httpcache
 */

require_once __DIR__ . '/../../../../../kernel/private/classes/httpcache/ezphttpcachecontract.php';

class ezpHttpCacheContractTest extends PHPUnit\Framework\TestCase
{
    private $dir;
    private $sessions;

    protected function setUp(): void
    {
        $base = dirname( __DIR__, 6 ) . '/var/tmp';
        $this->dir = $base . '/httpcache-test-' . getmypid() . '-' . mt_rand();
        $this->sessions = $this->dir . '-sessions';
        mkdir( $this->dir, 0770, true );
        mkdir( $this->sessions, 0770, true );
    }

    protected function tearDown(): void
    {
        foreach ( array( $this->dir, $this->sessions ) as $d )
            $this->removeTree( $d );
    }

    private function removeTree( $d )
    {
        if ( !is_dir( $d ) )
            return;
        foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $d, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::CHILD_FIRST ) as $f )
            $f->isDir() ? rmdir( $f->getPathname() ) : unlink( $f->getPathname() );
        rmdir( $d );
    }

    private function contract( array $extra = array() )
    {
        return new ezpHttpCacheContract( $extra + array(
            'enabled' => true, 'secret' => str_repeat( 'k', 64 ), 'dir' => $this->dir,
            'hosts' => array( 'example.org' => 'site' ), 'sessionCookie' => array( 'site' => 'eZSESSID' ),
            'sessionSavePath' => $this->sessions, 'formTokenSecret' => 'secret', 'maxAge' => 3600, 'swr' => 60,
            'apcu' => false,
        ) );
    }

    private function request( $uri = '/page', array $cookies = array() )
    {
        return array( 'scheme' => 'https', 'host' => 'example.org', 'uri' => $uri, 'method' => 'GET',
                      'cookies' => $cookies, 'acceptEncoding' => '', 'ifNoneMatch' => null );
    }

    private function storeAnonymous( ezpHttpCacheContract $c, $uri, $body, array $tags = array( 'ez-all' ), $maxAge = null )
    {
        $key = $c->entryKey( 'https', 'example.org', 'site', $uri, $c->anonymousContext( 'site' ) );
        return array( $key, $c->storeEntry( $key, 200, array( 'Content-Type' => 'text/html' ), $body, $tags, array(), $maxAge ) );
    }

    /** HC-01 */
    public function testKeysDifferByEveryPartAndIgnoreQueryOrder()
    {
        $c = $this->contract();
        $ctx = $c->anonymousContext( 'site' );
        $base = $c->entryKey( 'https', 'example.org', 'site', '/a?x=1&y=2', $ctx );
        $this->assertSame( $base, $c->entryKey( 'https', 'EXAMPLE.org', 'site', '/a?y=2&x=1', $ctx ) );
        foreach ( array(
            $c->entryKey( 'http', 'example.org', 'site', '/a?x=1&y=2', $ctx ),
            $c->entryKey( 'https', 'other.org', 'site', '/a?x=1&y=2', $ctx ),
            $c->entryKey( 'https', 'example.org', 'admin', '/a?x=1&y=2', $ctx ),
            $c->entryKey( 'https', 'example.org', 'site', '/b?x=1&y=2', $ctx ),
            $c->entryKey( 'https', 'example.org', 'site', '/a?x=1&y=2', $c->privateContext( 14, 'site' ) ),
        ) as $other )
            $this->assertNotSame( $base, $other );
    }

    /** HC-02 */
    public function testContextsAreSharedOnlyByTheSameRoles()
    {
        $c = $this->contract();
        $this->assertSame( $c->roleContext( array( 3, 1 ), array( 'a' ), 'site' ), $c->roleContext( array( 1, 3 ), array( 'a' ), 'site' ) );
        $this->assertNotSame( $c->roleContext( array( 1 ), array(), 'site' ), $c->roleContext( array( 1, 3 ), array(), 'site' ) );
        $this->assertNotSame( $c->roleContext( array( 1 ), array( 'a' ), 'site' ), $c->roleContext( array( 1 ), array( 'b' ), 'site' ) );
        $this->assertNotSame( $c->privateContext( 14, 'site' ), $c->privateContext( 15, 'site' ) );
        $this->assertNotSame( $c->anonymousContext( 'site' ), $c->roleContext( array(), array(), 'site' ) );
    }

    /** HC-03 */
    public function testANewGenerationChangesContextsAndStalesEntries()
    {
        $c = $this->contract();
        $before = $c->anonymousContext( 'site' );
        list( $key ) = $this->storeAnonymous( $c, '/page', 'body' );
        $this->assertNotNull( $c->loadEntry( $key ) );
        usleep( 1000 );
        $c->bumpGeneration();
        $this->assertNotSame( $before, $c->anonymousContext( 'site' ) );
        $this->assertNull( $c->loadEntry( $key ) );
    }

    /** HC-04 */
    public function testATagPurgeStalesOnlyEntriesCarryingTheTag()
    {
        $c = $this->contract();
        list( $a ) = $this->storeAnonymous( $c, '/a', 'A', array( 'ez-all', 'l2' ) );
        list( $b ) = $this->storeAnonymous( $c, '/b', 'B', array( 'ez-all', 'l3' ) );
        usleep( 1000 );
        $c->purgeTags( array( 'l2' ) );
        $this->assertNull( $c->loadEntry( $a ) );
        $this->assertSame( 'purged', $c->lastReason );
        $this->assertNotNull( $c->loadEntry( $b ) );
    }

    /** HC-05 */
    public function testStaleWhileRevalidate()
    {
        $c = $this->contract( array( 'maxAge' => 1, 'swr' => 30 ) );
        list( $key ) = $this->storeAnonymous( $c, '/page', 'old' );
        // Age the entry past its max-age.
        $meta = json_decode( file_get_contents( $this->metaFile( $key ) ), true );
        $meta['created'] -= 5;
        file_put_contents( $this->metaFile( $key ), json_encode( $meta ) );

        $this->assertNull( $c->serve( $this->request() ), 'the first request renders' );
        $this->assertSame( 'expired, refreshing', $c->lastReason );
        $stale = $c->serve( $this->request() );
        $this->assertNotNull( $stale, 'the others get the stale page meanwhile' );
        $this->assertSame( 'STALE', $stale[1]['X-Exp-Cache'] );

        $this->storeAnonymous( $c, '/page', 'new' );
        $fresh = $c->serve( $this->request() );
        $this->assertSame( 'HIT', $fresh[1]['X-Exp-Cache'] );
        $this->assertSame( 'new', $fresh[2] );

        // Purged is never served stale.
        $meta = json_decode( file_get_contents( $this->metaFile( $key ) ), true );
        $meta['created'] -= 5;
        file_put_contents( $this->metaFile( $key ), json_encode( $meta ) );
        usleep( 1000 );
        $c->purgeTags( array( 'ez-all' ) );
        $this->assertNull( $c->serve( $this->request() ) );
        $this->assertNull( $c->serve( $this->request() ) );
    }

    private function metaFile( $key )
    {
        return $this->dir . '/e/' . substr( $key, 0, 2 ) . '/' . $key . '.meta';
    }

    /** HC-06 */
    public function testTheFormTokenOfEachVisitorIsPutBack()
    {
        $c = $this->contract();
        $sid = str_repeat( 'a', 26 );
        $token = $c->formToken( $sid );
        $html = '<form><input name="ezxform_token" value="' . $token . '"/></form>';
        list( $body, $offsets ) = ezpHttpCacheContract::extractPlaceholders( $html, array( 'form_token' => $token ) );
        $this->assertStringNotContainsString( $token, $body );
        $key = $c->entryKey( 'https', 'example.org', 'site', '/form', $c->anonymousContext( 'site' ) );
        $c->storeEntry( $key, 200, array(), $body, array( 'ez-all' ), $offsets );

        file_put_contents( $this->sessions . '/sess_' . $sid, '' );
        $mine = $c->serve( $this->request( '/form', array( 'eZSESSID' => $sid ) ) );
        $this->assertSame( $html, $mine[2] );

        $other = str_repeat( 'b', 26 );
        file_put_contents( $this->sessions . '/sess_' . $other, '' );
        $theirs = $c->serve( $this->request( '/form', array( 'eZSESSID' => $other ) ) );
        $this->assertStringContainsString( $c->formToken( $other ), $theirs[2] );
        $this->assertStringNotContainsString( $token, $theirs[2] );
    }

    /** HC-07 */
    public function testOnlyListedQueryParametersAreCached()
    {
        $c = $this->contract( array( 'queryParameters' => array( 'page' ) ) );
        $this->assertTrue( $c->queryAllowed( '/a' ) );
        $this->assertTrue( $c->queryAllowed( '/a?page=2' ) );
        $this->assertFalse( $c->queryAllowed( '/a?page=2&x=1' ) );
        $this->assertNull( $c->serve( $this->request( '/a?utm=1' ) ) );
        $this->assertSame( 'query string', $c->lastReason );
    }

    /** HC-08 */
    public function testSessionsAreReadFromFiles()
    {
        $c = $this->contract();
        $sid = str_repeat( 'c', 26 );
        file_put_contents( $this->sessions . '/sess_' . $sid, 'x|i:1;eZUserLoggedInID|s:2:"14";' );
        $this->assertSame( 14, $c->sessionUserID( $sid ) );
        file_put_contents( $this->sessions . '/sess_' . $sid, 'eZUserLoggedInID|i:15;' );
        $this->assertSame( 15, $c->sessionUserID( $sid ) );
        $this->assertSame( 0, $c->sessionUserID( str_repeat( 'd', 26 ) ), 'no such session: anonymous' );
        $this->assertNull( $c->sessionUserID( '../../etc/passwd' ), 'never a path' );
        // A signed-in user without a record is a miss, never the anonymous page.
        $this->storeAnonymous( $c, '/page', 'anon' );
        $this->assertNull( $c->serve( $this->request( '/page', array( 'eZSESSID' => $sid ) ) ) );
        $this->assertSame( 'no user record', $c->lastReason );
    }

    /** HC-09 */
    public function testGarbageCollection()
    {
        $c = $this->contract();
        list( $keep ) = $this->storeAnonymous( $c, '/keep', 'keep', array( 'ez-all', 'l5' ) );
        list( $gone ) = $this->storeAnonymous( $c, '/gone', 'gone', array( 'ez-all', 'l6' ) );
        usleep( 1000 );
        $c->purgeTags( array( 'l6' ) );
        $orphan = $this->dir . '/e/' . substr( $keep, 0, 2 ) . '/' . $keep . '.0123456789abcdef.body';
        file_put_contents( $orphan, 'x' );
        touch( $orphan, time() - 3600 );
        $counts = $c->gc();
        $this->assertSame( 1, $counts['kept'] );
        $this->assertSame( 1, $counts['entries'] );
        $this->assertFileDoesNotExist( $orphan );
        $this->assertNotNull( $c->loadEntry( $keep ) );
    }

    /** HC-10 */
    public function testMetadataPointsAtItsOwnBody()
    {
        $c = $this->contract();
        list( $key, $first ) = $this->storeAnonymous( $c, '/page', 'first body' );
        list( , $second ) = $this->storeAnonymous( $c, '/page', 'second body, longer' );
        $this->assertNotSame( $first['etag'], $second['etag'] );
        $entry = $c->loadEntry( $key );
        $this->assertSame( 'second body, longer', $entry[1] );
        $bodies = glob( $this->dir . '/e/' . substr( $key, 0, 2 ) . '/' . $key . '.*.body' );
        $this->assertCount( 1, $bodies, 'the old body is removed' );
    }

    /** HC-11 */
    public function testJoinedGzipIsExactlyTheServedPage()
    {
        $token = str_repeat( 'a1b2c3d4e5', 4 );
        $html = '<html><head><title>x</title></head><body>' . str_repeat( '<p class="x">Lorem ipsum dolor sit amet</p>', 400 ) . '</body></html>';
        $cases = array(
            'no placeholder'   => $html,
            'one'              => str_replace( '<body>', '<body><input name="ezxform_token" value="' . $token . '" />', $html ),
            'several'          => str_replace( '<p class="x">', '<p data-t="' . $token . '">', substr( $html, 0, 3000 ) ) . substr( $html, 3000 ),
            'at the start'     => $token . $html,
            'at the end'       => $html . $token,
            'back to back'     => $token . $token . $html,
        );
        foreach ( $cases as $what => $page )
        {
            list( $body, $placeholders ) = ezpHttpCacheContract::extractPlaceholders( $page, array( 'form_token' => $token ) );
            $other = sha1( 'another visitor' );
            $values = array( 'form_token' => $other );
            $plain = ezpHttpCacheContract::substitute( $body, $placeholders, $values );
            $parts = ezpHttpCacheContract::deflateParts( $body, $placeholders );
            $this->assertCount( count( $placeholders ) + 1, $parts, $what );
            $gz = ezpHttpCacheContract::assembleGzip( $parts, $placeholders, $values, $plain );
            $this->assertSame( $plain, gzdecode( $gz ), $what . ': decodes to the page with this visitor\'s value' );
            $this->assertSame( "\x1f\x8b", substr( $gz, 0, 2 ), $what . ': a gzip member' );
            $this->assertLessThan( strlen( $plain ) / 3, strlen( $gz ), $what . ': compressed' );
        }
    }

    /** The site.ini rules eZSiteAccess::match() uses, as the listener writes them. */
    private function matchRules( array $extra = array() )
    {
        return $extra + array(
            'static' => '', 'default' => 'site', 'order' => array( 'uri', 'host' ),
            'list' => array( 'site', 'eng', 'admin', 'bold', 'bold_ger' ),
            'uriType' => 'element', 'uriElement' => 1,
            'uriMap' => array( array( 'admin', 'admin' ), array( 'ADMIN', 'admin' ) ),
            'hostType' => 'map', 'hostMap' => array( array( 'bold.example.org', 'bold' ) ),
            'hostUri' => array(), 'hostUriMethod' => 'strict',
        );
    }

    /** HC-12 */
    public function testTheSiteAccessFromTheUri()
    {
        $c = $this->contract( array( 'match' => $this->matchRules() ) );
        $this->assertSame( 'bold_ger', $c->resolveSiteAccess( 'example.org', '/bold_ger' ) );
        $this->assertSame( 'bold_ger', $c->resolveSiteAccess( 'example.org', '/bold_ger/kontakt?x=1' ) );
        $this->assertSame( 'bold_ger', $c->resolveSiteAccess( 'example.org:8787', '/bold_ger/' ) );
        $this->assertSame( 'eng', $c->resolveSiteAccess( 'example.org', '/eng/about' ) );
        // No siteaccess in the path: the next rule (the host map), then DefaultAccess.
        $this->assertSame( 'bold', $c->resolveSiteAccess( 'bold.example.org', '/kontakt' ) );
        $this->assertSame( 'site', $c->resolveSiteAccess( 'example.org', '/kontakt' ) );
        $this->assertSame( 'site', $c->resolveSiteAccess( 'example.org', '/' ) );
        // The URI wins over the host, as MatchOrder=uri;host says.
        $this->assertSame( 'eng', $c->resolveSiteAccess( 'bold.example.org', '/eng' ) );
        // Decoded as eZURI does.
        $this->assertSame( 'bold_ger', $c->resolveSiteAccess( 'example.org', '/bold%5Fger/x' ) );
        // A name the kernel would normalise (and perhaps redirect): not known here.
        $this->assertNull( $c->resolveSiteAccess( 'example.org', '/bold-ger/x' ) );
        // More than one element.
        $c = $this->contract( array( 'match' => $this->matchRules( array( 'uriElement' => 2, 'list' => array( 'site', 'de_shop' ) ) ) ) );
        $this->assertSame( 'de_shop', $c->resolveSiteAccess( 'example.org', '/de/shop/cart' ) );
        $this->assertSame( 'site', $c->resolveSiteAccess( 'example.org', '/de/other' ) );
        // map: the first element against URIMatchMapItems, only for listed siteaccesses.
        $c = $this->contract( array( 'match' => $this->matchRules( array( 'uriType' => 'map',
            'uriMap' => array( array( 'de', 'bold_ger' ), array( 'x', 'unlisted' ) ) ) ) ) );
        $this->assertSame( 'bold_ger', $c->resolveSiteAccess( 'example.org', '/de/kontakt' ) );
        $this->assertSame( 'site', $c->resolveSiteAccess( 'example.org', '/x/kontakt' ) );
        $this->assertSame( 'site', $c->resolveSiteAccess( 'example.org', '/bold_ger' ) );
    }

    /** HC-13 */
    public function testTheSiteAccessFromHostAndUri()
    {
        $c = $this->contract( array( 'match' => $this->matchRules( array(
            'order' => array( 'host_uri', 'uri' ),
            'hostUri' => array(
                array( 'www.example.org', 'shop', 'bold' ),
                array( 'www.example.org', '', 'eng' ),
                array( 'de.', '', 'bold_ger', 'start' ),
                array( '.example.com', 'en', 'eng', 'end' ),
                array( 'intranet', '', 'admin', 'part' ),
            ),
        ) ) ) );
        $this->assertSame( 'bold', $c->resolveSiteAccess( 'www.example.org', '/shop/cart' ) );
        $this->assertSame( 'bold', $c->resolveSiteAccess( 'www.example.org', '/shop' ) );
        // \b as in the kernel: "shopping" is not "shop".
        $this->assertSame( 'eng', $c->resolveSiteAccess( 'www.example.org', '/shopping' ) );
        $this->assertSame( 'eng', $c->resolveSiteAccess( 'www.example.org:443', '/' ) );
        $this->assertSame( 'bold_ger', $c->resolveSiteAccess( 'de.example.net', '/kontakt' ) );
        $this->assertSame( 'eng', $c->resolveSiteAccess( 'news.example.com', '/en/x' ) );
        $this->assertSame( 'admin', $c->resolveSiteAccess( 'my.intranet.lan', '/' ) );
        // strict is exact.
        $this->assertSame( 'site', $c->resolveSiteAccess( 'www.example.org.evil', '/' ) );
        // No host_uri match: the next rule, then DefaultAccess.
        $this->assertSame( 'bold_ger', $c->resolveSiteAccess( 'other.org', '/bold_ger/x' ) );
        $this->assertSame( 'site', $c->resolveSiteAccess( 'other.org', '/x' ) );
    }

    /** HC-14 */
    public function testMatchOrderAndWhatCannotBeKnown()
    {
        $rules = $this->matchRules();
        $this->assertSame( 'eng', $this->contract( array( 'match' => array( 'static' => 'eng' ) + $rules ) )->resolveSiteAccess( 'example.org', '/bold_ger' ) );
        $this->assertSame( 'site', $this->contract( array( 'match' => array( 'order' => array( 'none' ) ) + $rules ) )->resolveSiteAccess( 'example.org', '/bold_ger' ) );
        $this->assertSame( 'bold', $this->contract( array( 'match' => array( 'order' => array( 'host', 'uri' ) ) + $rules ) )->resolveSiteAccess( 'bold.example.org', '/eng' ) );
        // A rule that needs what the early exit does not have, before anything matched.
        foreach ( array( 'port', 'servervar', 'index' ) as $probe )
            $this->assertNull( $this->contract( array( 'match' => array( 'order' => array( $probe, 'uri' ) ) + $rules ) )->resolveSiteAccess( 'example.org', '/eng' ), $probe );
        // ... but after a rule that matched, it is never asked.
        $this->assertSame( 'eng', $this->contract( array( 'match' => array( 'order' => array( 'uri', 'port' ) ) + $rules ) )->resolveSiteAccess( 'example.org', '/eng' ) );
        foreach ( array( array( 'uriType' => 'regexp' ), array( 'uriType' => 'text' ),
                         array( 'order' => array( 'host' ), 'hostType' => 'element' ),
                         array( 'order' => array( 'host' ), 'hostType' => 'regexp' ) ) as $unsupported )
            $this->assertNull( $this->contract( array( 'match' => $unsupported + $rules ) )->resolveSiteAccess( 'example.org', '/eng' ), json_encode( $unsupported ) );
        // The index file in the path: eZSys strips it, not followed here.
        $this->assertNull( $this->contract( array( 'match' => $rules ) )->resolveSiteAccess( 'example.org', '/index.php/eng' ) );
        // A contract from before 'match': the host map.
        $old = $this->contract();
        $this->assertSame( 'site', $old->resolveSiteAccess( 'EXAMPLE.org:8080', '/eng' ) );
        $this->assertNull( $old->resolveSiteAccess( 'other.org', '/' ) );
        // Which siteaccesses are cached.
        $c = $this->contract( array( 'siteaccesses' => array( 'site', 'bold_ger' ) ) );
        $this->assertTrue( $c->cachesSiteAccess( 'bold_ger' ) );
        $this->assertFalse( $c->cachesSiteAccess( 'eng' ) );
        $this->assertFalse( $c->cachesSiteAccess( null ) );
        $this->assertTrue( $old->cachesSiteAccess( 'site' ) );
    }

    /** HC-15 */
    public function testSchemeAndHostAsTheKernelHasThem()
    {
        $c = $this->contract( array( 'sslPort' => '443' ) );
        $this->assertSame( array( 'http', 'example.org' ), $c->requestOrigin( array( 'HTTP_HOST' => 'example.org', 'SERVER_PORT' => '80' ) ) );
        $this->assertSame( array( 'https', 'example.org' ), $c->requestOrigin( array( 'HTTP_HOST' => 'example.org', 'HTTPS' => 'on' ) ) );
        $this->assertSame( array( 'http', 'example.org' ), $c->requestOrigin( array( 'HTTP_HOST' => 'example.org', 'HTTPS' => 'off' ) ) );
        $this->assertSame( array( 'https', 'example.org' ), $c->requestOrigin( array( 'HTTP_HOST' => 'example.org', 'SERVER_PORT' => '443' ) ) );
        $this->assertSame( array( 'https', 'example.org:443' ), $c->requestOrigin( array( 'HTTP_HOST' => 'example.org:443', 'SERVER_PORT' => '8080' ) ) );
        // A load balancer that ends TLS and forwards to exp:8080.
        $lb = array( 'HTTP_HOST' => 'exp:8080', 'SERVER_PORT' => '8080', 'HTTP_X_FORWARDED_PROTO' => 'https' );
        $this->assertSame( array( 'https', 'exp:8080' ), $c->requestOrigin( $lb ) );
        $this->assertSame( array( 'https', 'www.example.org' ), $c->requestOrigin( $lb + array( 'HTTP_X_FORWARDED_HOST' => 'www.example.org, proxy.lan' ) ) );
        $this->assertSame( array( 'http', 'exp:8080' ), $c->requestOrigin( array( 'HTTP_X_FORWARDED_PROTO' => 'http' ) + $lb ) );
        $this->assertSame( array( 'https', 'exp:8080' ), $c->requestOrigin( array( 'HTTP_HOST' => 'exp:8080', 'HTTP_X_FORWARDED_PORT' => '443' ) ) );
        $this->assertSame( array( 'http', 'exp:8080' ), $c->requestOrigin( array( 'HTTP_HOST' => 'exp:8080', 'HTTP_X_FORWARDED_PORT' => '80' ) ) );
        $this->assertSame( array( 'https', 'exp:8080' ), $this->contract( array( 'sslProxyServerName' => 'lb1' ) )
            ->requestOrigin( array( 'HTTP_HOST' => 'exp:8080', 'HTTP_X_FORWARDED_SERVER' => 'lb1' ) ) );
        // SSLPort elsewhere.
        $this->assertSame( array( 'https', 'example.org:8443' ), $this->contract( array( 'sslPort' => '8443' ) )
            ->requestOrigin( array( 'HTTP_HOST' => 'example.org:8443' ) ) );
    }

    /** HC-16 */
    public function testAUriSiteAccessBehindALoadBalancerIsFound()
    {
        $c = $this->contract( array( 'match' => $this->matchRules(), 'siteaccesses' => array( 'site', 'bold_ger' ),
                                     'sessionCookie' => array( 'site' => 'eZSESSID', 'bold_ger' => 'eZSESSID' ), 'sslPort' => '443' ) );
        // Stored as the kernel does: scheme, host and siteaccess from its own view of the request.
        $lb = array( 'HTTP_HOST' => 'exp:8080', 'SERVER_PORT' => '8080', 'HTTP_X_FORWARDED_PROTO' => 'https',
                     'HTTP_X_FORWARDED_HOST' => 'www.example.org', 'REQUEST_METHOD' => 'GET' );
        list( $scheme, $host ) = $c->requestOrigin( $lb );
        $sa = $c->resolveSiteAccess( $host, '/bold_ger/kontakt' );
        $this->assertSame( 'bold_ger', $sa );
        $key = $c->entryKey( $scheme, $host, $sa, '/bold_ger/kontakt', $c->anonymousContext( $sa ) );
        $c->storeEntry( $key, 200, array( 'Content-Type' => 'text/html' ), '<p>kontakt</p>', array( 'ez-all' ), array() );

        $base = array( 'uri' => '/bold_ger/kontakt', 'method' => 'GET', 'cookies' => array(), 'acceptEncoding' => '', 'ifNoneMatch' => null );
        // The early exit hands over $_SERVER.
        $hit = $c->serve( $base + array( 'server' => $lb, 'host' => 'exp:8080' ) );
        $this->assertSame( '<p>kontakt</p>', $hit[2] ?? null );
        // The web server's process hands over the headers.
        $hit = $c->serve( $base + array( 'scheme' => 'http', 'host' => 'exp:8080', 'port' => 8080,
            'headers' => array( 'host' => 'exp:8080', 'x-forwarded-proto' => 'https', 'x-forwarded-host' => 'www.example.org' ) ) );
        $this->assertSame( '<p>kontakt</p>', $hit[2] ?? null );
        // One that passes only host and scheme misses; it never gets another page.
        $this->assertNull( $c->serve( $base + array( 'scheme' => 'http', 'host' => 'exp:8080' ) ) );
        // Another siteaccess's URL, a siteaccess that is not cached.
        $this->assertNull( $c->serve( array( 'uri' => '/bold_ger/other' ) + $base + array( 'server' => $lb ) ) );
        $this->assertNull( $c->serve( array( 'uri' => '/eng/kontakt' ) + $base + array( 'server' => $lb ) ) );
        $this->assertSame( 'siteaccess', $c->lastReason );
    }

    /**
     * HC-17: the purge tags go out in one header, and only when TagHeader names
     * one. By default a hit carries none (the tags name internal ids); an old
     * contract's proxyHeaders sends nothing; ProxyHeaders=enabled still reads
     * as xkey; an invalid name is none.
     */
    public function testPurgeTagsAreOneHeaderAndOnlyWhenAskedFor()
    {
        $tags = array( 'ez-all', 'ez-location-2', 'ez-content-57' );
        $names = function ( array $response ) {
            return array_values( array_intersect( array_map( 'strtolower', array_keys( $response[1] ) ),
                                                  array( 'xkey', 'surrogate-key', 'cache-tag' ) ) );
        };

        $off = $this->contract();
        $this->storeAnonymous( $off, '/tags', '<p>t</p>', $tags );
        $hit = $off->serve( $this->request( '/tags' ) );
        $this->assertNotNull( $hit );
        $this->assertSame( array(), $names( $hit ), 'no tag header by default' );
        $this->assertSame( array(), $off->tagHeaders( $tags ) );

        $old = $this->contract( array( 'proxyHeaders' => true ) );
        $this->assertSame( array(), $names( $old->serve( $this->request( '/tags' ) ) ), 'an old contract sends none' );

        foreach ( array( 'xkey', 'Surrogate-Key', 'Cache-Tag' ) as $header )
        {
            $c = $this->contract( array( 'tagHeader' => $header ) );
            $hit = $c->serve( $this->request( '/tags' ) );
            $this->assertSame( array( strtolower( $header ) ), $names( $hit ), "exactly one header: $header" );
            $this->assertSame( implode( ' ', $tags ), $hit[1][$header] );
            $this->assertSame( array( $header => implode( ' ', $tags ) ), $c->tagHeaders( $tags ), 'MISS uses the same' );
        }

        $this->assertSame( '', ezpHttpCacheContract::tagHeaderName( 'disabled' ) );
        $this->assertSame( '', ezpHttpCacheContract::tagHeaderName( '' ) );
        $this->assertSame( 'xkey', ezpHttpCacheContract::tagHeaderName( 'disabled', 'enabled' ), 'ProxyHeaders=enabled' );
        $this->assertSame( 'Surrogate-Key', ezpHttpCacheContract::tagHeaderName( 'Surrogate-Key', 'enabled' ) );
        $this->assertSame( '', ezpHttpCacheContract::tagHeaderName( "x key\r\nX-Evil: 1" ), 'not a header name' );
        $this->assertSame( '', ezpHttpCacheContract::tagHeaderName( 'Content-Length' ), 'a framing header' );
    }
}
