<?php
/**
 * The warning eZContentCacheManager writes when a change touches so many nodes that it expires all view caches.
 *
 * clearNodeViewCacheArray() clears the view cache of each node while their number is below site.ini
 * [ContentSettings] CacheThreshold; from the threshold on it expires every view cache of the site instead. That
 * used to be a debug message nobody saw in a log; it is a warning now, with the number of nodes and the threshold.
 *
 * Nothing of the site is touched: the threshold is set in-process, the static cache is off, the expiry timestamps
 * go to a private directory under var/tmp (site.ini [FileSettings] ExpiryDir) with an expiry handler of their own,
 * the content events go to an event instance without the site's listeners, and the debug messages to a debug
 * instance of the test. All of it is restored in tearDown().
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit f9691221a3), Copyright (C) Mugo Web, GPL v2
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class eZContentCacheManagerThresholdWarningTest extends PHPUnit\Framework\TestCase
{
    const GLOBAL_KEYS = array( 'eZDebugGlobalInstance', 'eZDebugEnabled', 'eZDebugLogOnly', 'eZDebugAlwaysLog',
                               'eZDebugLogFileEnabled', 'eZExpiryHandlerInstance' );

    private $dir;
    private $savedGlobals = array();
    private $debug;
    private $events = array();

    /**
     * The kernel registers its shutdown and exception handlers (eZExecution) the first time a file handler stores
     * something; done once here so no test is reported for adding them.
     */
    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        eZExecution::registerShutdownHandler();
    }

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->savedGlobals['eZCurrentAccess'] = array_key_exists( 'eZCurrentAccess', $GLOBALS ) ? array( $GLOBALS['eZCurrentAccess'] ) : null;
        if ( !isset( $GLOBALS['eZCurrentAccess']['name'] ) )
            $GLOBALS['eZCurrentAccess'] = array( 'name' => 'phpunit-cachethreshold' );
        foreach ( self::GLOBAL_KEYS as $key )
            $this->savedGlobals[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;

        $this->dir = getcwd() . '/var/tmp/phpunit-cachethreshold-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( $this->dir . '/log', 0777, true );
        mkdir( $this->dir . '/expiry', 0777, true );

        ezpINIHelper::setINISetting( 'site.ini', 'ContentSettings', 'CacheThreshold', '3' );
        ezpINIHelper::setINISetting( 'site.ini', 'ContentSettings', 'StaticCache', 'disabled' );
        ezpINIHelper::setINISetting( 'site.ini', 'FileSettings', 'ExpiryDir', $this->dir . '/expiry' );
        // an absolute CacheDir: the view cache files the nodes' clearing looks for are below it, not the site's
        ezpINIHelper::setINISetting( 'site.ini', 'FileSettings', 'CacheDir', $this->dir . '/cache' );
        unset( $GLOBALS['eZExpiryHandlerInstance'] );

        // the content events without the site's listeners (HTTP cache purges and the like)
        ezpEvent::resetInstance();
        $events = &$this->events;
        ezpEvent::getInstance()->attach( 'content/cache/all', function () use ( &$events ) { $events[] = 'all'; } );
        ezpEvent::getInstance()->attach( 'content/cache', function () use ( &$events ) { $events[] = 'nodes'; } );

        unset( $GLOBALS['eZDebugAlwaysLog'], $GLOBALS['eZDebugLogFileEnabled'] );
        $this->debug = new eZDebug();
        foreach ( $this->debug->LogFiles as $level => $file )
            $this->debug->LogFiles[$level] = array( $this->dir . '/log/', $file[1] );
        $GLOBALS['eZDebugGlobalInstance'] = $this->debug;
        $GLOBALS['eZDebugEnabled'] = true;
        $GLOBALS['eZDebugLogOnly'] = false;
    }

    protected function tearDown(): void
    {
        ezpEvent::resetInstance();
        ezpINIHelper::restoreINISettings();
        foreach ( $this->savedGlobals as $key => $saved )
        {
            if ( $saved === null )
                unset( $GLOBALS[$key] );
            else
                $GLOBALS[$key] = $saved[0];
        }
        self::removeTree( $this->dir );
    }

    private static function removeTree( $path )
    {
        if ( !$path || !file_exists( $path ) )
            return;
        if ( is_dir( $path ) && !is_link( $path ) )
        {
            foreach ( scandir( $path ) as $entry )
            {
                if ( $entry !== '.' && $entry !== '..' )
                    self::removeTree( $path . '/' . $entry );
            }
            rmdir( $path );
        }
        else
            unlink( $path );
    }

    private function warnings()
    {
        $warnings = array();
        foreach ( $this->debug->DebugStrings as $message )
        {
            if ( $message['Level'] == eZDebug::LEVEL_WARNING && strpos( $message['String'], 'Expiring all view cache' ) !== false )
                $warnings[] = $message['String'];
        }
        return $warnings;
    }

    /**
     * @testdox at the threshold all view caches are expired, with a warning that gives the node count and the threshold
     */
    public function testThresholdReachedWarnsWithTheNodeCount(): void
    {
        $before = time();
        $this->assertTrue( eZContentCacheManager::clearNodeViewCacheArray( array( 101, 102, 103, 104 ) ) );

        $warnings = $this->warnings();
        $this->assertCount( 1, $warnings );
        $this->assertStringContainsString( 'the view cache of 4 nodes', $warnings[0] );
        $this->assertStringContainsString( 'CacheThreshold=3', $warnings[0] );
        $this->assertSame( array( 'all' ), $this->events );

        // all view caches expired, in the private expiry file
        $this->assertFileExists( $this->dir . '/expiry/expiry.php' );
        $this->assertGreaterThanOrEqual( $before, eZExpiryHandler::instance()->getTimestamp( 'content-view-cache', 0 ) );
    }

    /**
     * @testdox below the threshold the nodes' caches are cleared one by one and nothing is warned
     */
    public function testBelowThresholdWarnsNothing(): void
    {
        eZContentCacheManager::clearNodeViewCacheArray( array( 2147483001, 2147483002 ) );
        $this->assertSame( array(), $this->warnings() );
        $this->assertSame( array( 'nodes' ), $this->events );
    }

    /**
     * @testdox the warning text
     */
    public function testWarningText(): void
    {
        $this->assertSame( 'Expiring all view cache: the view cache of 300 nodes was to be cleared, which reaches site.ini [ContentSettings] CacheThreshold=3',
                           eZContentCacheManager::cacheThresholdWarningText( 300, 300 ) );
    }
}
