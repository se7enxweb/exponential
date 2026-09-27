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
            'apcu' => false, 'proxyHeaders' => true,
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
}
