<?php
/**
 * The rate limits of the GraphQL API (doc/eng-us/features/6.0/graphql.md, "Rate limits"): a fixed window per
 * identity counted in a file, the headers a client gets, the identity and limit of each kind of request. No database:
 * the counters are a directory of their own under var/tmp.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class expGraphQLRateLimiterTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    protected $dir;

    protected function setUp(): void
    {
        $this->dir = dirname( __DIR__, 5 ) . '/var/tmp/expgraphqlrate-' . getmypid() . '-' . mt_rand();
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

    public function testAnIdentityIsCountedInItsWindow()
    {
        $limiter = new expGraphQLRateLimiter( $this->dir );
        $now = 1767225600;
        $first = $limiter->hit( 'ip:192.0.2.1', 3, 60, $now );
        $this->assertSame( array( 'allowed' => true, 'limit' => 3, 'remaining' => 2, 'reset' => 60 ), $first );
        $this->assertTrue( $limiter->hit( 'ip:192.0.2.1', 3, 60, $now + 1 )['allowed'] );
        $third = $limiter->hit( 'ip:192.0.2.1', 3, 60, $now + 2 );
        $this->assertTrue( $third['allowed'] );
        $this->assertSame( 0, $third['remaining'] );
        $over = $limiter->hit( 'ip:192.0.2.1', 3, 60, $now + 10 );
        $this->assertFalse( $over['allowed'] );
        $this->assertSame( 50, $over['reset'], 'the window ends 60 seconds after its first request' );
        $this->assertTrue( $limiter->hit( 'ip:192.0.2.2', 3, 60, $now + 10 )['allowed'], 'another identity has its own count' );
        $this->assertTrue( $limiter->hit( 'ip:192.0.2.1', 3, 60, $now + 60 )['allowed'], 'a new window' );
        $this->assertSame( array( 'allowed' => true, 'limit' => 0, 'remaining' => 0, 'reset' => 0 ), $limiter->hit( 'ip:192.0.2.1', 0, 60, $now ) );
    }

    public function testTheHeadersTellTheLimit()
    {
        $this->assertSame( array( 'X-RateLimit-Limit' => '120', 'X-RateLimit-Remaining' => '7', 'X-RateLimit-Reset' => '30' ),
                           expGraphQLRateLimiter::headers( array( 'allowed' => true, 'limit' => 120, 'remaining' => 7, 'reset' => 30 ) ) );
        $over = expGraphQLRateLimiter::headers( array( 'allowed' => false, 'limit' => 120, 'remaining' => 0, 'reset' => 12 ) );
        $this->assertSame( '12', $over['Retry-After'] );
        $this->assertSame( array(), expGraphQLRateLimiter::headers( array( 'allowed' => true, 'limit' => 0, 'remaining' => 0, 'reset' => 0 ) ) );
        $refusal = expGraphQLRateLimiter::refusal( array( 'allowed' => false, 'limit' => 120, 'remaining' => 0, 'reset' => 12 ) );
        $this->assertSame( 'RATE_LIMITED', $refusal['errors'][0]['extensions']['code'] );
    }

    public function testEachKindOfRequestHasItsIdentityAndLimit()
    {
        $anonymousId = (int)eZINI::instance()->variable( 'UserSettings', 'AnonymousUserID' );
        $anonymous = new eZUser( array( 'contentobject_id' => $anonymousId, 'login' => 'anonymous' ) );
        $admin = new eZUser( array( 'contentobject_id' => 14, 'login' => 'admin' ) );
        $ip = expGraphQLRateLimiter::forRequest( expGraphQLContext::AUTH_ANONYMOUS, $anonymous, null, '192.0.2.9' );
        $this->assertSame( 'ip:192.0.2.9', $ip['identity'] );
        $this->assertSame( (int)expGraphQL::setting( 'RateLimitAnonymous', 120 ), $ip['limit'] );
        $user = expGraphQLRateLimiter::forRequest( expGraphQLContext::AUTH_TOKEN, $admin, null, '192.0.2.9' );
        $this->assertSame( 'user:14', $user['identity'] );
        $this->assertSame( (int)expGraphQL::setting( 'RateLimit', 600 ), $user['limit'] );
        $this->assertSame( 'key:7', expGraphQLRateLimiter::forRequest( expGraphQLContext::AUTH_API_KEY, $admin, 7, '192.0.2.9' )['identity'] );
        $this->assertNull( expGraphQLRateLimiter::forRequest( expGraphQLContext::AUTH_CONSOLE, $admin, null, '' )['identity'] );
    }
}
