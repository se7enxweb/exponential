<?php
/**
 * The view cache file of a node view with cache_ttl=0 holds only the no-cache advice.
 *
 * A template that sets {set-block scope=root variable=cache_ttl}0{/set-block} turns the view cache off for the node.
 * The first request still stores a cache file, so that the next requests learn from it that they generate without the
 * cache lock and store nothing. That file used to hold the whole serialized page, which is never read back: write IO
 * and disk space for nothing, multiplied by every combination of view parameters a client asks for. It now holds
 * array( 'no_cache' => true, 'cache_ttl' => 0 ).
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/eZNodeviewfunctionsViewCacheTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class eZNodeviewfunctionsViewCacheTest extends PHPUnit\Framework\TestCase
{
    private $cacheFile;

    protected function tearDown(): void
    {
        if ( $this->cacheFile !== null && file_exists( $this->cacheFile ) )
        {
            unlink( $this->cacheFile );
        }
        parent::tearDown();
    }

    public function testNoCacheResultKeepsOnlyTheAdvice()
    {
        $result = array(
            'content'      => str_repeat( '<p>page</p>', 1000 ),
            'content_info' => array( 'node_id' => 2 ),
            'cache_ttl'    => 0,
            'no_cache'     => true,
        );

        $this->assertSame(
            array( 'no_cache' => true, 'cache_ttl' => 0 ),
            eZNodeviewfunctions::viewCacheFileData( $result )
        );
    }

    public function testCachedResultIsStoredWhole()
    {
        $result = array(
            'content'      => '<p>page</p>',
            'content_info' => array( 'node_id' => 2 ),
            'cache_ttl'    => -1,
        );

        $this->assertSame( $result, eZNodeviewfunctions::viewCacheFileData( $result ) );
    }

    /**
     * The retrieve callback reads the advice file and answers with failure 3, which tells the cluster file handler to
     * generate without the lock and without storing.
     */
    public function testRetrieveAnswersTheAdviceWithNoCacheFailure()
    {
        $this->cacheFile = tempnam( sys_get_temp_dir(), 'viewcache' );
        file_put_contents( $this->cacheFile, serialize( eZNodeviewfunctions::viewCacheFileData( array( 'cache_ttl' => 0, 'no_cache' => true ) ) ) );

        $retval = eZNodeviewfunctions::contentViewRetrieve( $this->cacheFile, time(), array() );

        $this->assertInstanceOf( 'eZClusterFileFailure', $retval );
        $this->assertSame( 3, $retval->errno() );
    }
}
