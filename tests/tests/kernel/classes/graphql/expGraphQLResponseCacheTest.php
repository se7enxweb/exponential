<?php
/**
 * The response cache of the GraphQL API (doc/eng-us/features/6.0/graphql.md, "Speed"): the key of an anonymous GET
 * made from the raw request, entries with their expiry and lifetime, the early answer of index_rest.php (with its
 * rate limit and 304), and the end point's answer: MISS, then HIT with the same body, BYPASS for what must not be
 * kept, an ETag and 304 for every answer. No database: fixture content (expGraphQLSchemaTest) and directories of
 * their own under var/tmp.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/expGraphQLSchemaTest.php';

class expGraphQLResponseCacheTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    protected $dir;

    public static function setUpBeforeClass(): void
    {
        if ( !expGraphQL::loadLibrary() )
        {
            if ( getenv( 'CI' ) )
                self::fail( 'webonyx/graphql-php is not installed (composer.json requires it)' );
            self::markTestSkipped( 'webonyx/graphql-php is not installed; set GRAPHQL_LIBRARY_PATH' );
        }
    }

    protected function setUp(): void
    {
        $this->dir = dirname( __DIR__, 5 ) . '/var/tmp/expgraphqlcache-' . getmypid() . '-' . mt_rand();
    }

    protected function tearDown(): void
    {
        self::removeTree( $this->dir );
    }

    protected static function removeTree( $dir )
    {
        if ( !is_dir( $dir ) )
            return;
        foreach ( (array)scandir( $dir ) as $entry )
        {
            if ( $entry === '.' || $entry === '..' )
                continue;
            is_dir( "$dir/$entry" ) ? self::removeTree( "$dir/$entry" ) : unlink( "$dir/$entry" );
        }
        rmdir( $dir );
    }

    protected static function get( array $extra = array() )
    {
        return $extra + array( 'REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/api/graphql?id=home', 'QUERY_STRING' => 'id=home',
                               'HTTP_HOST' => 'example.com', 'HTTPS' => 'on', 'REMOTE_ADDR' => '192.0.2.1' );
    }

    public function testTheKeyOfAnAnonymousGetComesFromTheRawRequest()
    {
        $key = expGraphQLResponseCache::anonymousKey( self::get() );
        $this->assertMatchesRegularExpression( '/^[0-9a-f]{40}$/', $key );
        $this->assertSame( $key, expGraphQLResponseCache::anonymousKey( self::get( array( 'REMOTE_ADDR' => '192.0.2.2', 'HTTP_COOKIE' => 'x=1' ) ) ),
                           'the address and cookies do not change the answer' );
        foreach ( array( array( 'HTTP_HOST' => 'other.example.com' ), array( 'HTTPS' => '' ), array( 'HTTP_X_SITEACCESS' => 'admin' ),
                         array( 'QUERY_STRING' => 'id=home&variables=%7B%7D', 'REQUEST_URI' => '/api/graphql?id=home&variables=%7B%7D' ) ) as $change )
            $this->assertNotSame( $key, expGraphQLResponseCache::anonymousKey( self::get( $change ) ), json_encode( $change ) );
        $this->assertNotSame( $key, expGraphQLResponseCache::anonymousKey( self::get( array( 'HTTP_ORIGIN' => 'https://app.example.com' ) ) ), 'a CORS request has its own answer' );
        foreach ( array( array( 'REQUEST_METHOD' => 'POST' ), array( 'HTTP_AUTHORIZATION' => 'Bearer x' ), array( 'HTTP_X_API_KEY' => 'k' ),
                         array( 'PHP_AUTH_USER' => 'staff' ),
                         array( 'QUERY_STRING' => 'query=%7Ba%7D', 'REQUEST_URI' => '/api/graphql?query=%7Ba%7D' ),
                         array( 'REQUEST_URI' => '/api/ezp/v2/content?id=home' ) ) as $change )
            $this->assertNull( expGraphQLResponseCache::anonymousKey( self::get( $change ) ), json_encode( $change ) );
        $this->assertNotNull( expGraphQLResponseCache::anonymousKey( self::get( array( 'QUERY_STRING' => 'extensions=%7B%7D', 'REQUEST_URI' => '/site/api/graphql/?extensions=%7B%7D' ) ) ) );
    }

    public function testAnEntryHoldsUntilItIsExpiredOrOld()
    {
        $cache = new expGraphQLResponseCache( $this->dir );
        $this->assertNull( $cache->read( 'aa11' ) );
        $now = microtime( true );
        $this->assertTrue( $cache->write( 'aa11', '{"data":{}}', array( 'etag' => '"e"', 'schemaHash' => 'h', 'ttl' => 60 ), $now ) );
        $entry = $cache->read( 'aa11', $now + 1 );
        $this->assertSame( '{"data":{}}', $entry['body'] );
        $this->assertSame( '"e"', $entry['etag'] );
        $this->assertNull( $cache->read( 'aa11', $now + 61 ), 'past its lifetime' );
        $cache->write( 'bb22', '{"data":{}}', array( 'etag' => '"e"', 'schemaHash' => 'h', 'ttl' => 0 ), $now - 1 );
        $this->assertNotNull( $cache->read( 'bb22' ) );
        $cache->expire();
        $this->assertNull( $cache->read( 'bb22' ), 'made before the expiry' );
        $cache->write( 'cc33', '{"data":{}}', array( 'etag' => '"e"', 'schemaHash' => 'h', 'ttl' => 0 ) );
        $this->assertNotNull( $cache->read( 'cc33' ) );
        $this->assertSame( 3, $cache->clear(), 'every file, the stale ones too' );
        $this->assertNull( $cache->read( 'cc33' ) );
    }

    public function testTheEarlyAnswerServesAnEntryWithItsRateLimitAnd304()
    {
        $root = $this->dir . '/root';
        $cache = new expGraphQLResponseCache( expGraphQLResponseCache::directoryOf( $root ) );
        $server = self::get();
        $this->assertFalse( expGraphQLResponseCache::serveEarly( $root, $server ), 'nothing cached yet' );
        $cache->write( expGraphQLResponseCache::anonymousKey( $server ), '{"data":{"a":1}}',
                       array( 'etag' => '"abc"', 'schemaHash' => 'h', 'ttl' => 60, 'rateLimit' => 2, 'rateWindow' => 60 ) );
        ob_start();
        $served = expGraphQLResponseCache::serveEarly( $root, $server );
        $this->assertSame( '{"data":{"a":1}}', ob_get_clean() );
        $this->assertTrue( $served );
        ob_start();
        $this->assertTrue( expGraphQLResponseCache::serveEarly( $root, $server + array( 'HTTP_IF_NONE_MATCH' => 'W/"abc"' ) ) );
        $this->assertSame( '', ob_get_clean(), '304: no body' );
        $this->assertFalse( expGraphQLResponseCache::serveEarly( $root, $server ), 'over the anonymous limit: the kernel answers (429)' );
        ob_start();
        $this->assertTrue( expGraphQLResponseCache::serveEarly( $root, array( 'REMOTE_ADDR' => '192.0.2.9' ) + $server ), 'another address has its own limit' );
        ob_end_clean();
    }

    public function testASignedInGetIsAnsweredEarlyOnceTheKernelCheckedItsCredentials()
    {
        $root = $this->dir . '/root';
        $cache = new expGraphQLResponseCache( expGraphQLResponseCache::directoryOf( $root ) );
        $store = new expGraphQLPersistedQueries( $this->dir . '/store' );
        $store->store( '{ node(id: 2) { name } }', 'home' );
        $server = self::get( array( 'HTTP_AUTHORIZATION' => 'Bearer abc123' ) );
        $this->assertNull( expGraphQLResponseCache::anonymousKey( $server ) );
        $this->assertNotNull( expGraphQLResponseCache::signedInKey( $server, 'r' ) );
        $this->assertNotSame( expGraphQLResponseCache::signedInKey( $server, 'r' ), expGraphQLResponseCache::signedInKey( $server, 's' ), 'other rights, other answer' );
        $this->assertNotSame( expGraphQLResponseCache::credential( $server ), expGraphQLResponseCache::credential( self::get( array( 'HTTP_AUTHORIZATION' => 'Bearer abc124' ) ) ) );
        $this->assertFalse( expGraphQLResponseCache::serveEarly( $root, $server ), 'credentials the kernel has not checked' );

        $miss = self::respondWith( $cache, $store, expGraphQLContext::AUTH_TOKEN, array( 'id' => 'home' ), $server );
        $this->assertSame( 'MISS', $miss['headers']['X-GraphQL-Cache'] );
        $known = $cache->readCredential( expGraphQLResponseCache::credential( $server ) );
        $this->assertSame( 'roles-of-14', $known['roleHash'] );
        $this->assertSame( 'user:14', $known['identity'] );

        ob_start();
        $served = expGraphQLResponseCache::serveEarly( $root, $server );
        $this->assertSame( $miss['body'], ob_get_clean() );
        $this->assertTrue( $served );
        $this->assertFalse( expGraphQLResponseCache::serveEarly( $root, self::get( array( 'HTTP_AUTHORIZATION' => 'Bearer other' ) ) ), 'another token' );

        $cache->forgetCredentials();
        $this->assertNull( $cache->readCredential( expGraphQLResponseCache::credential( $server ) ), 'a key was revoked, a role changed' );
        $this->assertFalse( expGraphQLResponseCache::serveEarly( $root, $server ) );
        $this->assertFalse( $cache->writeCredential( 'ab', array( 'roleHash' => 'r', 'ttl' => 0 ) ), 'CredentialCacheTtl=0' );
        $cache->writeCredential( 'cd', array( 'roleHash' => 'r', 'ttl' => 60 ) );
        $this->assertNull( $cache->readCredential( 'cd', microtime( true ) + 61 ), 'past CredentialCacheTtl' );
    }

    public function testETags()
    {
        $etag = expGraphQLResponseCache::etag( '{"data":{}}' );
        $this->assertMatchesRegularExpression( '/^"[0-9a-f]{32}"$/', $etag );
        $this->assertTrue( expGraphQLResponseCache::etagMatches( $etag, $etag ) );
        $this->assertTrue( expGraphQLResponseCache::etagMatches( '"x", W/' . $etag, $etag ) );
        $this->assertTrue( expGraphQLResponseCache::etagMatches( '*', $etag ) );
        $this->assertFalse( expGraphQLResponseCache::etagMatches( '"x"', $etag ) );
    }

    public function testTheEndPointKeepsTheAnswersOfPersistedQueries()
    {
        $cache = new expGraphQLResponseCache( $this->dir . '/cache' );
        $store = new expGraphQLPersistedQueries( $this->dir . '/store' );
        $store->store( '{ node(id: 2) { name } }', 'home' );
        $store->store( '{ currentUser { id } node(id: 2) { name } }', 'me' );
        $server = self::get();

        $miss = self::respondWith( $cache, $store, expGraphQLContext::AUTH_ANONYMOUS, array( 'id' => 'home' ), $server );
        $this->assertSame( 200, $miss['status'] );
        $this->assertSame( 'MISS', $miss['headers']['X-GraphQL-Cache'] );
        $this->assertSame( 'public, max-age=0, must-revalidate', $miss['headers']['Cache-Control'] );
        $this->assertStringNotContainsString( 'requestId', $miss['body'], 'the same body for every request' );
        $this->assertSame( array( 'node' => array( 'name' => 'Home' ) ), json_decode( $miss['body'], true )['data'] );
        $this->assertNotNull( $cache->read( expGraphQLResponseCache::anonymousKey( $server ) ), 'kept under the key the early answer reads' );

        $hit = self::respondWith( $cache, $store, expGraphQLContext::AUTH_ANONYMOUS, array( 'id' => 'home' ), $server );
        $this->assertSame( 'HIT', $hit['headers']['X-GraphQL-Cache'] );
        $this->assertSame( $miss['body'], $hit['body'] );
        $this->assertSame( $miss['headers']['ETag'], $hit['headers']['ETag'] );
        $notModified = self::respondWith( $cache, $store, expGraphQLContext::AUTH_ANONYMOUS, array( 'id' => 'home' ),
                                          $server + array( 'HTTP_IF_NONE_MATCH' => $hit['headers']['ETag'] ) );
        $this->assertSame( 304, $notModified['status'] );
        $this->assertSame( '', $notModified['body'] );

        // signed in: kept per rights, private; an answer naming the user is not kept
        $token = self::respondWith( $cache, $store, expGraphQLContext::AUTH_TOKEN, array( 'id' => 'home' ), $server );
        $this->assertSame( 'MISS', $token['headers']['X-GraphQL-Cache'] );
        $this->assertSame( 'private, no-cache', $token['headers']['Cache-Control'] );
        $this->assertSame( 'HIT', self::respondWith( $cache, $store, expGraphQLContext::AUTH_TOKEN, array( 'id' => 'home' ), $server )['headers']['X-GraphQL-Cache'] );
        $this->assertSame( 'BYPASS', self::respondWith( $cache, $store, expGraphQLContext::AUTH_TOKEN, array( 'id' => 'me' ), $server )['headers']['X-GraphQL-Cache'] );
        $this->assertSame( 'BYPASS', self::respondWith( $cache, $store, expGraphQLContext::AUTH_TOKEN, array( 'id' => 'me' ), $server )['headers']['X-GraphQL-Cache'] );

        // ad hoc: never kept, but with an ETag; an error is not kept
        $adHoc = self::respondWith( $cache, $store, expGraphQLContext::AUTH_ANONYMOUS, array( 'query' => '{ node(id: 2) { name } }' ), $server );
        $this->assertArrayNotHasKey( 'X-GraphQL-Cache', $adHoc['headers'] );
        $this->assertSame( 'private, no-store', $adHoc['headers']['Cache-Control'] );
        $this->assertSame( 304, self::respondWith( $cache, $store, expGraphQLContext::AUTH_ANONYMOUS, array( 'query' => '{ node(id: 2) { name } }' ),
                                                   $server + array( 'HTTP_IF_NONE_MATCH' => $adHoc['headers']['ETag'] ) )['status'] );
        $missing = self::respondWith( $cache, $store, expGraphQLContext::AUTH_ANONYMOUS, array( 'id' => 'nope' ), $server );
        $this->assertSame( 'PERSISTED_QUERY_NOT_FOUND', json_decode( $missing['body'], true )['errors'][0]['extensions']['code'] );

        // published content: everything made before is stale
        $cache->expire();
        $this->assertSame( 'MISS', self::respondWith( $cache, $store, expGraphQLContext::AUTH_ANONYMOUS, array( 'id' => 'home' ), $server )['headers']['X-GraphQL-Cache'] );
    }

    /**
     * The answer of the end point for fixture content (a folder at node 2).
     *
     * @param expGraphQLResponseCache $cache
     * @param expGraphQLPersistedQueries $store
     * @param string $authMode
     * @param array $request
     * @param array $server
     * @return array status, body, headers
     */
    protected static function respondWith( $cache, $store, $authMode, array $request, array $server )
    {
        $catalogue = expGraphQLClassCatalogue::filtered( array( array( 'id' => 1, 'identifier' => 'folder', 'name' => 'Folder', 'description' => '',
            'attributes' => array( array( 'id' => 1, 'identifier' => 'short_name', 'datatype' => 'ezstring', 'name' => 'Short name', 'description' => '',
                                          'required' => false, 'options' => array() ) ) ) ), array() );
        $builder = new expGraphQLSchemaBuilder( $catalogue, new expGraphQLNaming( 'identifier' ) );
        $repository = new expGraphQLTestRepository( $catalogue, array() );
        $repository->byNode[2] = new expGraphQLTestContent( array( 'id' => 1, 'class' => 'folder', 'name' => 'Home',
            'location' => new expGraphQLTestLocation( array( 'id' => 2, 'parentId' => 1, 'contentId' => 1, 'depth' => 1, 'pathString' => '/1/2/', 'urlAlias' => '' ) ),
            'fields' => array() ) );
        $userId = $authMode === expGraphQLContext::AUTH_ANONYMOUS ? (int)eZINI::instance()->variable( 'UserSettings', 'AnonymousUserID' ) : 14;
        $user = new eZUser( array( 'contentobject_id' => $userId, 'login' => 'x' ) );
        $context = new expGraphQLContext( $user, $authMode, $repository, 'https://example.com' );
        $context->roleHash = 'roles-of-' . $userId; // no database
        $graphql = new expGraphQLServer( $builder );
        $graphql->useStorage( $cache, $store );
        return $graphql->respond( $request, $context, $server );
    }
}
