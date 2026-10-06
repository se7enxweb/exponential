<?php
/**
 * The REST answer cache and results with a status object (a refusal such as 403 access_denied).
 *
 *  AC-01 the cause: a result whose status is an ezpRestStatusResponse, written to the cache file with var_export(),
 *        cannot be read back (the status class has no __set_state()); that was a 500 on the second identical read
 *  AC-02 only a result without a status object is cached
 *  AC-03 a cache file that cannot be read back counts as expired (regenerated), a good one is returned
 *
 * No database: the cache files are written to var/tmp and removed by the test.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 * @group rest
 */

class ezpRestAnswerCacheTest extends PHPUnit\Framework\TestCase
{
    private $files = array();

    protected function setUp(): void
    {
        if ( !class_exists( 'ezcMvcResult' ) )
            $this->markTestSkipped( 'the MVC component of the Zeta Components is not installed' );
        chdir( dirname( __DIR__, 5 ) );
    }

    protected function tearDown(): void
    {
        foreach ( $this->files as $file )
            if ( is_file( $file ) )
                unlink( $file );
        $this->files = array();
    }

    private function cacheFile( $data )
    {
        $file = dirname( __DIR__, 5 ) . '/var/tmp/rest-answer-cache-test-' . uniqid() . '.php';
        $this->files[] = $file;
        $storage = ( new ReflectionClass( 'ezpRestCacheStorageClusterObject' ) )->newInstanceWithoutConstructor();
        $prepare = new ReflectionMethod( 'ezpRestCacheStorageClusterObject', 'prepareData' );
        file_put_contents( $file, $prepare->invoke( $storage, $data ) );
        return array( $storage, $file );
    }

    private static function refusal()
    {
        $result = new ezpRestMvcResult();
        $result->status = new ezpRestStatusResponse( 403, array( 'error' => 'access_denied', 'error_message' => 'You may not read node 2 (content/read).' ) );
        return $result;
    }

    /** AC-01 */
    public function testARefusalCannotBeReadBackFromACacheFile()
    {
        list( , $file ) = $this->cacheFile( self::refusal() );
        $this->expectException( Error::class );
        include $file;
    }

    /** AC-02 */
    public function testOnlyResultsWithoutAStatusAreCached()
    {
        $plain = new ezpRestMvcResult();
        $plain->variables['metadata'] = array( 'nodeId' => 2 );
        $this->assertTrue( ezpRestMvcController::isCacheable( $plain ) );
        $this->assertFalse( ezpRestMvcController::isCacheable( self::refusal() ) );
        $created = new ezpRestMvcResult();
        $created->status = new ezpRestHttpResponse( 403, 'no' );
        $this->assertFalse( ezpRestMvcController::isCacheable( $created ) );
        $this->assertFalse( ezpRestMvcController::isCacheable( null ) );
    }

    /** AC-03 */
    public function testUnreadableCacheFileCountsAsExpired()
    {
        list( $storage, $file ) = $this->cacheFile( self::refusal() );
        $answer = $storage->clusterRetrieve( $file, time(), array() );
        $this->assertInstanceOf( 'eZClusterFileFailure', $answer );
        $this->assertSame( eZClusterFileFailure::FILE_EXPIRED, $answer->errno() );

        $plain = new ezpRestMvcResult();
        $plain->variables['metadata'] = array( 'nodeId' => 2 );
        list( $storage, $file ) = $this->cacheFile( $plain );
        $answer = $storage->clusterRetrieve( $file, time(), array() );
        $this->assertInstanceOf( 'ezpRestMvcResult', $answer );
        $this->assertSame( array( 'nodeId' => 2 ), $answer->variables['metadata'] );
    }
}
