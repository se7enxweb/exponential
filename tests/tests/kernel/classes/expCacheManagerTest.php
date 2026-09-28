<?php
/**
 * File containing the expCacheManagerTest class.
 *
 * The parts of expCacheManager -- what Setup > Cache and exp:cache share --
 * that can be checked without a database or a web server: how selections are
 * resolved, that a dry run changes nothing, that bad input is refused before
 * anything is touched, and what the results look like.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class expCacheManagerTest extends ezpTestCase
{
    private $tmp;

    /**
     * The first static cache lookup registers the kernel's shutdown and
     * exception handlers (eZExecution); done once here so no test is
     * reported for adding them.
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        expCacheManager::clearStaticCache( array( 'nodes' => array( 'x' ) ), true );
        expCacheManager::staticCacheTargets( 'no-such-siteaccess-177' );
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->tmp = eZSys::rootDir() . '/var/tmp/expcachemanagertest-' . getmypid();
    }

    public function tearDown(): void
    {
        if ( is_dir( $this->tmp ) )
            eZDir::recursiveDelete( $this->tmp );
        ezpINIHelper::restoreINISettings();
        parent::tearDown();
    }

    private function assertResult( array $r )
    {
        foreach ( array( 'ok', 'message', 'items', 'dry_run', 'data' ) as $key )
            $this->assertArrayHasKey( $key, $r );
        $this->assertIsBool( $r['ok'] );
        $this->assertIsString( $r['message'] );
        $this->assertIsArray( $r['items'] );
    }

    public function testSplitList()
    {
        $this->assertSame( array( 'a', 'b', 'c' ), expCacheManager::splitList( 'a, b,,c,a' ) );
        $this->assertSame( array( 'a', 'b' ), expCacheManager::splitList( array( 'a', 'b,a' ) ) );
        $this->assertSame( array(), expCacheManager::splitList( '' ) );
        $this->assertSame( array(), expCacheManager::splitList( null ) );
    }

    public function testResultShape()
    {
        $r = expCacheManager::result( true, 'done', array( array( 'path' => 'x' ) ), true, array( 'n' => 1 ) );
        $this->assertResult( $r );
        $this->assertTrue( $r['dry_run'] );
        $this->assertSame( 1, $r['data']['n'] );
    }

    /** --tag=ini reaches the global INI cache; --id=ini does not (why the help says: use the tag). */
    public function testIniTagAndIniIdDiffer()
    {
        $m = new expCacheManager();
        list( $byTag, $unknown ) = $m->selectItems( 'tag', array( 'ini' ) );
        $this->assertSame( array(), $unknown );
        $tagIDs = array_map( function ( $i ) { return $i['id']; }, $byTag );
        $this->assertContains( 'global_ini', $tagIDs );
        $this->assertContains( 'ini', $tagIDs );

        list( $byID, ) = $m->selectItems( 'id', array( 'ini' ) );
        $this->assertSame( array( 'ini' ), array_map( function ( $i ) { return $i['id']; }, $byID ) );
    }

    public function testUnknownSelectionsAreRefusedAndNothingIsCleared()
    {
        $m = new expCacheManager();
        $r = $m->clear( 'id', array( 'template', 'no-such-cache-177' ) );
        $this->assertResult( $r );
        $this->assertFalse( $r['ok'] );
        $this->assertStringContainsString( 'no-such-cache-177', $r['message'] );
        $this->assertSame( array(), $r['items'] );

        $this->assertFalse( $m->clear( 'tag', array( 'no-such-tag-177' ) )['ok'] );
        $this->assertFalse( $m->clear( 'tag', array() )['ok'] );
        $this->assertFalse( $m->clear( 'everything' )['ok'] );
    }

    public function testDryRunDescribesWithoutClearing()
    {
        $m = new expCacheManager();
        $dir = eZSys::cacheDirectory() . '/override';
        eZDir::mkdir( $dir, false, true );
        $marker = $dir . '/expcachemanagertest.txt';
        file_put_contents( $marker, 'keep' );

        $r = $m->clear( 'id', array( 'template-override' ), true );
        $this->assertResult( $r );
        $this->assertTrue( $r['ok'] );
        $this->assertTrue( $r['dry_run'] );
        $this->assertCount( 1, $r['items'] );
        $this->assertSame( 'template-override', $r['items'][0]['id'] );
        $this->assertSame( $dir, $r['items'][0]['path'] );
        $this->assertGreaterThanOrEqual( 1, $r['items'][0]['files'] );
        $this->assertFileExists( $marker );

        $all = $m->clear( 'all', array(), true );
        $this->assertCount( count( $m->cacheList() ), $all['items'] );
        $this->assertFileExists( $marker );
        @unlink( $marker );
    }

    public function testClearByIDRemovesTheDirectory()
    {
        $m = new expCacheManager();
        $dir = eZSys::cacheDirectory() . '/override';
        eZDir::mkdir( $dir, false, true );
        file_put_contents( $dir . '/expcachemanagertest.txt', 'x' );
        $r = $m->clear( 'id', array( 'template-override' ) );
        $this->assertTrue( $r['ok'] );
        $this->assertFalse( $r['dry_run'] );
        $this->assertFileDoesNotExist( $dir . '/expcachemanagertest.txt' );
    }

    public function testPathStats()
    {
        eZDir::mkdir( $this->tmp . '/a/b', false, true );
        file_put_contents( $this->tmp . '/a/one', '12345' );
        file_put_contents( $this->tmp . '/a/b/two', '123' );
        $s = expCacheManager::pathStats( $this->tmp );
        $this->assertSame( array( 'files' => 2, 'bytes' => 8, 'complete' => true ), $s );
        $this->assertFalse( expCacheManager::pathStats( $this->tmp, 1 )['complete'] );
        $this->assertSame( 1, expCacheManager::pathStats( $this->tmp . '/a/one' )['files'] );
        $this->assertSame( 0, expCacheManager::pathStats( $this->tmp . '/missing' )['files'] );
    }

    public function testHttpCacheTagsAreValidatedFirst()
    {
        $r = expCacheManager::purgeHttpCacheTags( array( 'l2', 'bad tag' ), true );
        $this->assertFalse( $r['ok'] );
        $this->assertStringContainsString( 'bad tag', $r['message'] );
        $this->assertFalse( expCacheManager::purgeHttpCacheNodes( array( '12a' ), true )['ok'] );
        $this->assertFalse( expCacheManager::purgeHttpCacheTags( array(), true )['ok'] );
    }

    public function testHttpCacheSwitchedOffIsAFailureAsOnThePage()
    {
        ezpINIHelper::setINISetting( 'httpcache.ini', 'HttpCacheSettings', 'Enabled', 'disabled' );
        $r = expCacheManager::clearHttpCache( true );
        $this->assertFalse( $r['ok'] );
        $this->assertStringContainsString( 'switched off', $r['message'] );
        $this->assertFalse( expCacheManager::purgeHttpCacheNodes( array( 2 ), true )['ok'] );
    }

    public function testHttpCacheDryRunListsTheTags()
    {
        ezpINIHelper::setINISetting( 'httpcache.ini', 'HttpCacheSettings', 'Enabled', 'enabled' );
        $r = expCacheManager::purgeHttpCacheNodes( array( 2, '62' ), true );
        $this->assertTrue( $r['ok'] );
        $this->assertTrue( $r['dry_run'] );
        $this->assertSame( array( 'l2', 'l62' ), array_map( function ( $i ) { return $i['tag']; }, $r['items'] ) );
    }

    public function testNodeIDFromSystemURL()
    {
        $this->assertSame( 42, expCacheManager::nodeIDFromURL( '/content/view/full/42' ) );
        $this->assertSame( 7, expCacheManager::nodeIDFromURL( 'https://example.com/site/content/view/line/7' ) );
    }

    public function testUnknownStaticSiteIsRefused()
    {
        $this->assertFalse( expCacheManager::staticCacheTargets( 'no-such-siteaccess-177' ) );
        $r = expCacheManager::regenerateStaticCache( null, array( 'siteaccess' => 'no-such-siteaccess-177' ), true );
        $this->assertFalse( $r['ok'] );
        $r = expCacheManager::clearStaticCache( array( 'nodes' => array( 'x' ) ), true );
        $this->assertFalse( $r['ok'] );
    }

    public function testPHPCacheStateShape()
    {
        $s = expCacheManager::phpCacheState();
        foreach ( array( 'opcache', 'apcu' ) as $k )
        {
            $this->assertIsBool( $s[$k]['available'] );
            $this->assertIsString( $s[$k]['text'] );
        }
        // A dry run of either never empties anything.
        $this->assertTrue( expCacheManager::resetOPcache( true )['dry_run'] );
        $this->assertTrue( expCacheManager::clearAPCu( true )['dry_run'] );
    }

    public function testQueryCacheDryRun()
    {
        $r = expCacheManager::clearQueryCache( true );
        $this->assertResult( $r );
        $this->assertTrue( $r['dry_run'] );
        $this->assertTrue( expCacheManager::queryCacheStatus()['ok'] );
    }

    public function testBytes()
    {
        $this->assertSame( '512 B', expCacheManager::bytes( 512 ) );
        $this->assertSame( '1.5 KB', expCacheManager::bytes( 1536 ) );
        $this->assertSame( '2 MB', expCacheManager::bytes( 2097152 ) );
    }
}
