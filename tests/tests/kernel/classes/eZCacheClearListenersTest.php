<?php
/**
 * File containing the eZCacheClearListenersTest class.
 *
 * The cache clear listeners of site.ini [Cache] ClearListeners[] (expCacheClearListener): eZCache calls them after
 * every cache item it clears or purges, eZContentCacheManager for the nodes of a content change. Without a listener
 * nothing happens; a listener whose class does not exist is left out; one that throws does not stop the clear or the
 * other listeners. No database and no cache server: the listeners here record the calls, and the cache items have
 * paths that do not exist, so nothing on disk is touched.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

/** Records every call */
class eZCacheClearListenersTestRecorder implements expCacheClearListener
{
    public static $calls = array();
    public function cacheItemCleared( array $cacheItem ) { self::$calls[] = array( 'item', $cacheItem['id'], $cacheItem['purge'] ); }
    public function nodeCachesCleared( array $nodeIDList ) { self::$calls[] = array( 'nodes', $nodeIDList ); }
}

/** Fails every call, as a listener whose cache server does not answer */
class eZCacheClearListenersTestFailing implements expCacheClearListener
{
    public static $calls = 0;
    public function cacheItemCleared( array $cacheItem ) { self::$calls++; throw new RuntimeException( 'cache server does not answer' ); }
    public function nodeCachesCleared( array $nodeIDList ) { self::$calls++; throw new RuntimeException( 'cache server does not answer' ); }
}

/** Not a listener */
class eZCacheClearListenersTestNotAListener
{
    public static $created = 0;
    public function __construct() { self::$created++; }
}

class eZCacheClearListenersTest extends ezpTestCase
{
    private $saved;
    private $hadSetting;

    /**
     * The first clear by id or tag of a process registers the kernel's shutdown and exception handlers (through the
     * audit); done once here, so no test is reported for adding them.
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        eZCache::clearByID( 'no-such-cache-id', array() );
    }

    protected function setUp(): void
    {
        parent::setUp();
        $ini = eZINI::instance();
        $this->hadSetting = $ini->hasVariable( 'Cache', 'ClearListeners' );
        $this->saved = $this->hadSetting ? $ini->variable( 'Cache', 'ClearListeners' ) : null;
        eZCacheClearListenersTestRecorder::$calls = array();
        eZCacheClearListenersTestFailing::$calls = 0;
        eZCacheClearListenersTestNotAListener::$created = 0;
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance();
        if ( $this->hadSetting )
            $ini->setVariable( 'Cache', 'ClearListeners', $this->saved );
        else
            $ini->removeSetting( 'Cache', 'ClearListeners' );
        parent::tearDown();
    }

    private function listen( array $classes )
    {
        eZINI::instance()->setVariable( 'Cache', 'ClearListeners', $classes );
    }

    /** A cache item as eZCache::fetchList() has it, with a path that does not exist (nothing on disk is touched) */
    private function item( $id )
    {
        return array( 'name' => $id, 'id' => $id, 'tag' => array( 'test' ), 'enabled' => true,
                      'path' => 'no-such-cache-dir-' . $id . '-' . getmypid() );
    }

    public function testTheShippedSettingListsNoListener()
    {
        $this->assertTrue( $this->hadSetting, 'site.ini [Cache] ClearListeners[]' );
        $this->assertSame( array(), array_values( array_filter( (array)$this->saved ) ) );
    }

    public function testWithoutListenersAClearStillWorks()
    {
        $this->listen( array() );
        $this->assertSame( array(), eZCache::clearListeners() );
        eZCache::clearItem( $this->item( 'content' ) );
        eZCache::notifyNodeCachesCleared( array( 2 ) );
        $this->assertSame( array(), eZCacheClearListenersTestRecorder::$calls );
    }

    public function testAListenerHearsEveryClearAndPurge()
    {
        $this->listen( array( 'eZCacheClearListenersTestRecorder' ) );
        eZCache::clearItem( $this->item( 'ini' ) );
        eZCache::clearItem( $this->item( 'content' ), true );
        $this->assertSame( array( array( 'item', 'ini', false ), array( 'item', 'content', true ) ),
                           eZCacheClearListenersTestRecorder::$calls );
    }

    public function testClearByIdAndTagReachTheListener()
    {
        $this->listen( array( 'eZCacheClearListenersTestRecorder' ) );
        $list = array( $this->item( 'one' ), $this->item( 'two' ) );
        eZCache::clearByID( 'two', $list );
        eZCache::clearByTag( 'test', $list );
        $this->assertSame( array( array( 'item', 'two', false ), array( 'item', 'one', false ), array( 'item', 'two', false ) ),
                           eZCacheClearListenersTestRecorder::$calls );
    }

    public function testTheNodesOfAContentChangeReachTheListener()
    {
        $this->listen( array( 'eZCacheClearListenersTestRecorder' ) );
        eZCache::notifyNodeCachesCleared( array( 7 => 4563, 9 => 2 ) );
        eZCache::notifyNodeCachesCleared( array() );
        $this->assertSame( array( array( 'nodes', array( 4563, 2 ) ) ), eZCacheClearListenersTestRecorder::$calls );
    }

    public function testACacheItemCanBeNamedById()
    {
        $this->listen( array( 'eZCacheClearListenersTestRecorder' ) );
        eZCache::notifyCacheItemCleared( 'content' );
        eZCache::notifyCacheItemCleared( 'no-such-cache-id' );
        $this->assertSame( array( array( 'item', 'content', false ) ), eZCacheClearListenersTestRecorder::$calls );
    }

    public function testAMissingClassIsLeftOut()
    {
        $this->listen( array( 'noSuchExtensionClearListener', '', 'eZCacheClearListenersTestRecorder' ) );
        $listeners = eZCache::clearListeners();
        $this->assertCount( 1, $listeners );
        $this->assertInstanceOf( 'eZCacheClearListenersTestRecorder', $listeners[0] );
    }

    public function testAClassThatIsNotAListenerIsLeftOut()
    {
        $this->listen( array( 'eZCacheClearListenersTestNotAListener' ) );
        $this->assertSame( array(), eZCache::clearListeners() );
        $this->assertSame( 0, eZCacheClearListenersTestNotAListener::$created );
    }

    public function testAListedTwiceListenerIsCalledOnce()
    {
        $this->listen( array( 'eZCacheClearListenersTestRecorder', 'eZCacheClearListenersTestRecorder' ) );
        eZCache::clearItem( $this->item( 'content' ) );
        $this->assertCount( 1, eZCacheClearListenersTestRecorder::$calls );
    }

    public function testAFailingListenerStopsNeitherTheClearNorTheOthers()
    {
        $this->listen( array( 'eZCacheClearListenersTestFailing', 'eZCacheClearListenersTestRecorder' ) );
        $ran = false;
        $item = $this->item( 'content' );
        $item['function'] = function () use ( &$ran ) { $ran = true; };
        eZCache::clearItem( $item );
        eZCache::notifyNodeCachesCleared( array( 2 ) );
        $this->assertTrue( $ran, 'the cache item itself was cleared' );
        $this->assertSame( 2, eZCacheClearListenersTestFailing::$calls );
        $this->assertSame( array( array( 'item', 'content', false ), array( 'nodes', array( 2 ) ) ),
                           eZCacheClearListenersTestRecorder::$calls );
    }
}
