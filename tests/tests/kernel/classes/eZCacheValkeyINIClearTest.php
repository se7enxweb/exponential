<?php
/**
 * File containing the eZCacheValkeyINIClearTest class.
 *
 * Clearing the INI caches (ezcache --clear-tag=ini, --clear-id=ini, --clear-id=global_ini, --clear-all, and their purge)
 * also clears the compiled INI arrays the sevenx_valkey_cache extension keeps in Valkey for this installation. Before,
 * only the files went, and every web host went on reading its old settings from Valkey. No database, no Valkey: a
 * stand-in for the extension's class records the calls; it reports itself disabled outside these tests, so eZINI
 * ignores it in every other test of the same process.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

if ( !class_exists( 'sevenxValkeyINICache', false ) )
{
    /** Stand-in with the extension's public surface that eZINI and eZCache use. */
    class sevenxValkeyINICache
    {
        public static $enabled = false;
        public static $clears = 0;
        private static $instance;
        public static function instance() { return self::$instance ?: ( self::$instance = new self() ); }
        public function isEnabled() { return self::$enabled; }
        public function clear() { self::$clears++; return true; }
        public function load( $cachedFile ) { return false; }
        public function save( $cachedFile, array $data ) { return false; }
        public function delete( $cachedFile ) { return false; }
    }
}

class eZCacheValkeyINIClearTest extends ezpTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if ( !property_exists( 'sevenxValkeyINICache', 'clears' ) )
            $this->markTestSkipped( 'the real sevenx_valkey_cache extension is loaded; this test needs the stand-in' );
        sevenxValkeyINICache::$enabled = true;
        sevenxValkeyINICache::$clears = 0;
    }

    protected function tearDown(): void
    {
        sevenxValkeyINICache::$enabled = false;
        parent::tearDown();
    }

    /** A cache item as eZCache::fetchList() has it, with a path that does not exist (nothing on disk is touched). */
    private function item( $id )
    {
        return array( 'name' => $id, 'id' => $id, 'tag' => array( 'ini' ), 'enabled' => true,
                      'path' => 'no-such-cache-dir-' . $id . '-' . getmypid() );
    }

    public function testClearingTheIniItemClearsTheValkeyIniCache()
    {
        eZCache::clearItem( $this->item( 'ini' ) );
        $this->assertSame( 1, sevenxValkeyINICache::$clears );
    }

    public function testClearingTheGlobalIniItemClearsTheValkeyIniCache()
    {
        $item = $this->item( 'global_ini' );
        $item['function'] = function () {};   // the real item removes var/cache/ini; nothing is removed here
        eZCache::clearItem( $item );
        $this->assertSame( 1, sevenxValkeyINICache::$clears );
    }

    public function testPurgingTheIniItemClearsTheValkeyIniCacheToo()
    {
        eZCache::clearItem( $this->item( 'ini' ), true );
        $this->assertSame( 1, sevenxValkeyINICache::$clears );
    }

    public function testOtherCacheItemsLeaveTheValkeyIniCacheAlone()
    {
        foreach ( array( 'template', 'content', 'classid', 'translation' ) as $id )
            eZCache::clearItem( $this->item( $id ) );
        $this->assertSame( 0, sevenxValkeyINICache::$clears );
    }

    public function testADisabledValkeyIniCacheIsNotCleared()
    {
        sevenxValkeyINICache::$enabled = false;
        $this->assertFalse( eZCache::clearValkeyINICache() );
        eZCache::clearItem( $this->item( 'ini' ) );
        $this->assertSame( 0, sevenxValkeyINICache::$clears );
    }

    public function testTheIniItemsAreTheOnesThatCarryTheIniTag()
    {
        $ids = array();
        foreach ( eZCache::fetchList() as $item )
            if ( in_array( 'ini', $item['tag'], true ) && in_array( $item['id'], array( 'ini', 'global_ini' ), true ) )
                $ids[] = $item['id'];
        sort( $ids );
        $this->assertSame( array( 'global_ini', 'ini' ), $ids );
    }
}
