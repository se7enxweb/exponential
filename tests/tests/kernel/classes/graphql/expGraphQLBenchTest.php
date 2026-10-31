<?php
/**
 * The speed measurement of exp:graphql:bench (doc/eng-us/features/6.0/graphql.md, "Speed"; decision 18): the
 * requests of each row, the statistics, the verdict against the hard limit and the targets, and a whole measurement
 * with a transport that answers without HTTP. No database, no network.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class expGraphQLBenchTest extends PHPUnit\Framework\TestCase
{
    public function testTheRequestsOfEachRow()
    {
        $bench = new expGraphQLBench( 'https://example.com/api/graphql', 'articles',
                                      array( 'variables' => array( 'limit' => 50 ), 'headers' => array( 'X-Siteaccess: site' ) ) );
        $get = $bench->request( 'get' );
        $this->assertSame( 'GET', $get['method'] );
        $this->assertSame( 'https://example.com/api/graphql?id=articles&variables=%7B%22limit%22%3A50%7D', $get['url'] );
        $this->assertSame( array( 'X-Siteaccess: site' ), $get['headers'] );
        $this->assertContains( 'If-None-Match: "e1"', $bench->request( '304', '"e1"' )['headers'] );

        // the early answer reads a JSON body: the same key as the kernel's (expGraphQLResponseCache::persistedFields())
        $post = $bench->request( 'post' );
        $this->assertSame( 'POST', $post['method'] );
        $this->assertSame( array( 'id' => 'articles', 'variables' => array( 'limit' => 50 ) ), json_decode( $post['body'], true ) );
        $this->assertNotNull( expGraphQLResponseCache::persistedFields( 'application/json', $post['body'] ) );

        // a form is read by the kernel only: the row of a cached answer that starts the kernel
        $kernel = $bench->request( 'kernel' );
        $this->assertContains( 'Content-Type: application/x-www-form-urlencoded', $kernel['headers'] );
        $this->assertNull( expGraphQLResponseCache::persistedFields( 'application/x-www-form-urlencoded', $kernel['body'] ) );
        parse_str( $kernel['body'], $form );
        $parsed = expGraphQLServer::parseHttpRequest( 'POST', 'application/x-www-form-urlencoded', $kernel['body'], array(), $form );
        $this->assertSame( 'articles', $parsed['id'] );
        $this->assertSame( array( 'limit' => 50 ), $parsed['variables'] );

        $this->assertFalse( $bench->canRun( 'uncached' ), 'the uncached row needs the text of the query' );
        $this->assertFalse( $bench->canRun( 'other' ) );
        $withText = new expGraphQLBench( 'https://example.com/api/graphql?x=1', 'articles', array( 'query' => '{ a }' ) );
        $this->assertTrue( $withText->canRun( 'uncached' ) );
        $this->assertSame( array( 'query' => '{ a }' ), json_decode( $withText->request( 'uncached' )['body'], true ) );
        $this->assertSame( 'https://example.com/api/graphql?x=1&id=articles', $withText->request( 'get' )['url'] );
    }

    public function testStatistics()
    {
        $this->assertSame( array( 'median' => null, 'p95' => null, 'max' => null, 'min' => null, 'mean' => null ), expGraphQLBench::stats( array() ) );
        $times = range( 1, 100 );
        shuffle( $times );
        $stats = expGraphQLBench::stats( $times );
        $this->assertEquals( 50.5, $stats['median'] );
        $this->assertEquals( 95, $stats['p95'], 'nearest rank' );
        $this->assertEquals( 100, $stats['max'] );
        $this->assertEquals( 1, $stats['min'] );
        $this->assertEquals( 3, expGraphQLBench::stats( array( 5, 1, 3 ) )['median'] );
    }

    /**
     * @param string $row
     * @param float[] $times
     * @param int $status
     * @param string $cache
     * @return array a result of runRow()
     */
    protected static function rowResult( $row, array $times, $status = 200, $cache = 'HIT' )
    {
        return array( 'row' => $row, 'n' => count( $times ), 'times' => $times, 'statuses' => array( $status => count( $times ) ),
                      'cache' => array( $cache => count( $times ) ), 'failed' => 0 ) + expGraphQLBench::stats( $times );
    }

    public function testTheVerdictFollowsDecision18()
    {
        $fast = expGraphQLBench::verdict( self::rowResult( 'get', array( 1.0, 2.0, 3.0 ) ) );
        $this->assertSame( 'PASS', $fast['verdict'] );
        $this->assertSame( array( '20' => true, '13' => true ), $fast['targets'] );

        $within = expGraphQLBench::verdict( self::rowResult( 'kernel', array( 15.0, 16.0, 33.0 ) ) );
        $this->assertSame( 'PASS', $within['verdict'], '33 ms is within the hard limit' );
        $this->assertSame( array( '20' => false, '13' => false ), $within['targets'], 'p95 33 ms' );

        $over = expGraphQLBench::verdict( self::rowResult( 'post', array( 1.0, 1.0, 33.1 ) ) );
        $this->assertSame( 'FAIL', $over['verdict'], 'the hard limit is the max: one request over it fails the row' );
        $this->assertStringContainsString( 'hard limit', $over['reason'] );

        $this->assertSame( 'FAIL', expGraphQLBench::verdict( self::rowResult( 'get', array( 1.0 ), 200, 'MISS' ) )['verdict'], 'not from the cache' );
        $this->assertSame( 'FAIL', expGraphQLBench::verdict( self::rowResult( 'get', array( 1.0 ), 429, '-' ) )['verdict'], 'rate limited' );
        $this->assertSame( 'PASS', expGraphQLBench::verdict( self::rowResult( '304', array( 1.0 ), 304, 'HIT' ) )['verdict'] );
        $this->assertSame( 'FAIL', expGraphQLBench::verdict( self::rowResult( '304', array( 1.0 ), 200, 'HIT' ) )['verdict'], 'no 304' );
        $this->assertSame( 'MEASURED', expGraphQLBench::verdict( self::rowResult( 'uncached', array( 90.0, 400.0 ), 200, '' ) )['verdict'], 'no limit' );
    }

    public function testAMeasurementWithoutHttp()
    {
        $sent = array();
        $transport = function ( array $request ) use ( &$sent ) {
            $sent[] = $request['method'] . ' ' . ( in_array( 'If-None-Match: "e"', $request['headers'], true ) ? '304' : 'x' );
            $notModified = in_array( 'If-None-Match: "e"', $request['headers'], true );
            return array( 'status' => $notModified ? 304 : 200, 'cache' => 'HIT', 'etag' => '"e"', 'ms' => 2.0 );
        };
        $bench = new expGraphQLBench( 'https://example.com/api/graphql', 'articles', array( 'transport' => $transport ) );
        $result = Exponential\Command\Kernel\Expgraphqlbench::measure( $bench, array( 'get', '304', 'uncached' ), 5, 2 );
        $this->assertTrue( $result['pass'] );
        $this->assertSame( array( 'uncached' ), $result['skipped'] );
        $this->assertSame( 10, $result['rows']['get']['all']['n'] );
        $this->assertCount( 2, $result['rows']['get']['runs'] );
        $this->assertSame( 'PASS', $result['rows']['304']['all']['verdict'] );
        $this->assertSame( array( 304 => 10 ), $result['rows']['304']['all']['statuses'] );
        $this->assertSame( 2 * ( 2 + 5 ) * 2, count( $sent ), 'two requests warm each row up, uncounted' );

        $slow = new expGraphQLBench( 'https://example.com/api/graphql', 'articles', array( 'transport' => function () {
            return array( 'status' => 200, 'cache' => 'HIT', 'etag' => '', 'ms' => 40.0 );
        } ) );
        $this->assertFalse( Exponential\Command\Kernel\Expgraphqlbench::measure( $slow, array( 'kernel' ), 3, 1 )['pass'] );
        $down = new expGraphQLBench( 'https://example.com/api/graphql', 'articles', array( 'transport' => function () {
            return array( 'status' => 0, 'cache' => '', 'etag' => '', 'ms' => null );
        } ) );
        $failed = Exponential\Command\Kernel\Expgraphqlbench::measure( $down, array( 'get' ), 3, 1 );
        $this->assertFalse( $failed['pass'] );
        $this->assertSame( 3, $failed['rows']['get']['all']['failed'] );
    }
}
