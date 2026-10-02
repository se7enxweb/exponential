<?php
/**
 * The services a view, a command and a cronjob part share (#207 stage 3), kernel/private/classes/services,
 * guide doc/bc/6.0/cli_cronjob_view_abstractions.md.
 *
 *  SV-01 — Trash::canEmpty() is content/cleantrash granted fully or with limitations, nothing else
 *  SV-02 — Trash::purgeObjects() of no ids purges nothing and touches nothing
 *  SV-03 — SessionGarbageCollector::addBasketHook() registers the gc_pre hook once per process, as a callable
 *  SV-04 — The callers use the services: content/trash, setup/session, bin/php/trashpurge.php,
 *          bin/php/ezsessiongc.php, cronjobs/trashpurge.php and cronjobs/session_gc.php
 *  SV-05 — The trash service hands the CLI's arguments to eZScriptTrashPurge in its constructor's order
 *  SV-06 — The trash view's Empty button and eZScriptTrashPurge share purgeInBatches(): a transaction per batch, the cache
 *          cleared and a pause between batches; emptyTrash() uses the command's 100 and 1 s and then sweeps what is left
 *  SV-07 — collect() (the sessions view, the command, the cronjob part) removes the expired baskets too: one garbage
 *          collection by the session handler, with the basket hook attached
 *  SV-08 — DraftsCleanup::duration() adds up days, hours, minutes and seconds, false when no numeric part is set
 *  SV-09 — DraftsCleanup kinds: the user drafts and the internal drafts, their status, settings and default lifetime
 *  SV-10 — The two drafts cronjob parts call DraftsCleanup::cleanup() and no longer carry the settings code; the
 *          cache toolbar (setup/cachetoolbar) clears through expCacheManager as setup/cache and exp:cache do
 *
 * No database.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 * @group runnable
 */

require_once __DIR__ . '/../../../../../kernel/private/classes/services/trash.php';
require_once __DIR__ . '/../../../../../kernel/private/classes/services/sessiongarbagecollector.php';
require_once __DIR__ . '/../../../../../kernel/private/classes/services/draftscleanup.php';

class ezpTestTrashUser
{
    public $word;
    public $asked = array();
    public function __construct( $word )
    {
        $this->word = $word;
    }
    public function hasAccessTo( $module, $function )
    {
        $this->asked[] = "$module/$function";
        return array( 'accessWord' => $this->word );
    }
}

class ServicesTest extends PHPUnit\Framework\TestCase
{
    private static function root()
    {
        return dirname( __DIR__, 5 );
    }

    /** SV-01 */
    public function testCanEmpty()
    {
        foreach ( array( 'yes' => true, 'limited' => true, 'no' => false, '' => false ) as $word => $expected )
        {
            $user = new ezpTestTrashUser( $word );
            $this->assertSame( $expected, \Exponential\Service\Trash::canEmpty( $user ), "accessWord '$word'" );
            $this->assertSame( array( 'content/cleantrash' ), $user->asked );
        }
    }

    /** SV-02 */
    public function testPurgeNothing()
    {
        $this->assertSame( 0, \Exponential\Service\Trash::purgeObjects( array() ) );
    }

    /** SV-03 */
    public function testBasketHookOnce()
    {
        if ( !class_exists( 'eZSession' ) )
            $this->markTestSkipped( 'eZSession is not autoloadable here' );
        $read = function () { return isset( self::$callbackFunctions['gc_pre'] ) ? self::$callbackFunctions['gc_pre'] : array(); };
        $hooks = Closure::bind( $read, null, 'eZSession' );
        $before = count( $hooks() );
        \Exponential\Service\SessionGarbageCollector::addBasketHook();
        \Exponential\Service\SessionGarbageCollector::addBasketHook();
        $after = $hooks();
        $this->assertLessThanOrEqual( $before + 1, count( $after ) );
        $this->assertContains( array( 'Exponential\\Service\\SessionGarbageCollector', 'cleanupBaskets' ), $after );
        $this->assertTrue( is_callable( array( 'Exponential\\Service\\SessionGarbageCollector', 'cleanupBaskets' ) ) );
    }

    /** SV-04 */
    public function testCallersUseTheServices()
    {
        $base = self::root() . '/kernel/private/classes/';
        $expect = array(
            'views/content/trash.php' => array( 'Trash::canEmpty', 'Trash::purgeObjects', 'Trash::emptyTrash( 100, 1 )' ),
            'commands/trashpurge.php' => array( 'Trash::purge(' ),
            'cronjobs/trashpurge.php' => array( 'Trash::purge(' ),
            'views/setup/session.php' => array( 'SessionGarbageCollector::collect()' ),
            'commands/ezsessiongc.php' => array( 'SessionGarbageCollector::collect()' ),
            'cronjobs/session_gc.php' => array( 'SessionGarbageCollector::collect()' ),
        );
        foreach ( $expect as $file => $needles )
        {
            $code = (string) file_get_contents( $base . $file );
            foreach ( $needles as $needle )
                $this->assertStringContainsString( $needle, $code, "$file calls $needle" );
            $this->assertStringNotContainsString( 'new \\eZScriptTrashPurge', $code, "$file leaves eZScriptTrashPurge to the service" );
            $this->assertStringNotContainsString( '\\eZSession::garbageCollector()', $code, "$file leaves the garbage collector to the service" );
        }
    }

    /** SV-06 */
    public function testTheViewAndTheCommandShareTheBatches()
    {
        $base = self::root() . '/kernel/private/classes/';
        $handler = (string) file_get_contents( $base . 'ezscripttrashpurge.php' );
        $this->assertStringContainsString( 'Trash::purgeInBatches(', $handler, 'eZScriptTrashPurge runs the service\'s batches' );
        $this->assertStringNotContainsString( '$db->begin()', $handler, 'the transactions are the service\'s' );
        $view = (string) file_get_contents( $base . 'views/content/trash.php' );
        $this->assertStringNotContainsString( 'emptyArchived(', $view, 'the Empty button no longer purges without transactions' );

        $service = (string) file_get_contents( $base . 'services/trash.php' );
        $m = array();
        preg_match( '/function purgeInBatches.*?\n    \}\n/s', $service, $m );
        $this->assertNotEmpty( $m );
        $body = $m[0];
        // a transaction per batch, the cache cleared and the pause between batches, a stop on an empty batch
        $this->assertLessThan( strpos( $body, '$db->commit()' ), strpos( $body, '$db->begin()' ) );
        $this->assertStringContainsString( "'Limit' => \$iterationLimit", $body );
        $this->assertStringContainsString( 'sleep( $sleep )', $body );
        $this->assertStringContainsString( '\\eZContentObject::clearCache()', $body );
        $this->assertStringContainsString( 'if ( !$trashList )', $body );

        $p = ( new ReflectionMethod( 'Exponential\\Service\\Trash', 'emptyTrash' ) )->getParameters();
        $this->assertSame( array( 100, 1 ), array( $p[0]->getDefaultValue(), $p[1]->getDefaultValue() ), 'the command\'s batch size and pause' );
        // everything: what has no trash entry is swept by emptyArchived() after the batches
        preg_match( '/function emptyTrash.*?\n    \}\n/s', $service, $m );
        $this->assertLessThan( strpos( $m[0], 'emptyArchived(' ), strpos( $m[0], 'purgeInBatches(' ) );
    }

    /** SV-07 */
    public function testCollectRemovesTheBasketsToo()
    {
        if ( !class_exists( 'eZSession' ) || !class_exists( 'ezpSessionHandler' ) )
            $this->markTestSkipped( 'eZSession is not autoloadable here' );
        $handler = new class extends ezpSessionHandler {
            public $gc = array();
            public function __construct() {}
            public function gc( $maxLifeTime ) { $this->gc[] = $maxLifeTime; return true; }
            public function read( $sessionId ) { return ''; }
            public function write( $sessionId, $sessionData ) { return true; }
            public function destroy( $sessionId ) { return true; }
            public function regenerate( $updateBackendData = true ) { return true; }
            public function cleanup() { return true; }
            public function deleteByUserIDs( array $userIDArray ) {}
        };
        $prop = new ReflectionProperty( 'eZSession', 'handlerInstance' );
        $old = $prop->getValue();
        $prop->setValue( null, $handler );
        try
        {
            $this->assertTrue( \Exponential\Service\SessionGarbageCollector::collect() );
        }
        finally
        {
            $prop->setValue( null, $old );
        }
        $this->assertCount( 1, $handler->gc, 'the handler collected once' );
        $read = Closure::bind( function () { return isset( self::$callbackFunctions['gc_pre'] ) ? self::$callbackFunctions['gc_pre'] : array(); }, null, 'eZSession' );
        $this->assertContains( array( 'Exponential\\Service\\SessionGarbageCollector', 'cleanupBaskets' ), $read(),
                               'the baskets of the expired sessions are removed with them (gc_pre hook)' );
    }

    /** SV-05 */
    public function testPurgeHandsTheArgumentsOn()
    {
        $source = (string) file_get_contents( self::root() . '/kernel/private/classes/ezscripttrashpurge.php' );
        $this->assertMatchesRegularExpression( '/function __construct\(\s*eZCLI \$cli,\s*\$quiet = true,\s*\$memoryMonitoring = false,\s*\?eZScript \$script = null/', $source );
        $this->assertMatchesRegularExpression( '/function run\(\s*\$iterationLimit = 100,\s*\$sleep = 1,\s*\$trashedDays = null\s*\)/', $source );
        $m = new ReflectionMethod( 'Exponential\\Service\\Trash', 'purge' );
        $names = array_map( function ( $p ) { return $p->getName(); }, $m->getParameters() );
        $this->assertSame( array( 'cli', 'quiet', 'memoryMonitoring', 'script', 'iterationLimit', 'sleep', 'trashedDays' ), $names );
        $this->assertTrue( $m->getParameters()[1]->getDefaultValue(), 'quiet by default, as eZScriptTrashPurge' );
    }

    /** SV-08 */
    public function testDraftsLifetime()
    {
        $d = function ( $setting ) { return \Exponential\Service\DraftsCleanup::duration( $setting ); };
        $this->assertSame( 90 * 86400, $d( array( 'days' => 90 ) ) );
        $this->assertSame( 24 * 3600, $d( array( 'hours' => 24 ) ) );
        $this->assertSame( 86400 + 2 * 3600 + 3 * 60 + 4, $d( array( 'days' => 1, 'hours' => 2, 'minutes' => 3, 'seconds' => 4 ) ) );
        $this->assertSame( 0, $d( array( 'days' => 0 ) ), 'zero is a lifetime: every draft is old enough' );
        $this->assertSame( 1800, $d( array( 'minutes' => '30' ) ), 'ini values are strings' );
        $this->assertFalse( $d( array() ) );
        $this->assertFalse( $d( array( 'weeks' => 2 ) ), 'an unknown unit sets nothing' );
        $this->assertFalse( $d( array( 'days' => 'many' ) ) );
        $this->assertFalse( $d( '90' ), 'not an array' );
    }

    /** SV-09 */
    public function testDraftsKinds()
    {
        if ( !class_exists( 'eZContentObjectVersion' ) )
            $this->markTestSkipped( 'eZContentObjectVersion is not autoloadable here' );
        $kinds = \Exponential\Service\DraftsCleanup::kinds();
        $this->assertSame( array( 'drafts', 'internal' ), array_keys( $kinds ) );
        $user = $kinds[\Exponential\Service\DraftsCleanup::USER_DRAFTS];
        $this->assertSame( \eZContentObjectVersion::STATUS_DRAFT, $user['status'] );
        $this->assertSame( array( 'DraftsCleanUpLimit', 'DraftsDuration', array( 'days' => 90 ) ),
                           array( $user['limit'], $user['duration'], $user['default'] ) );
        $internal = $kinds[\Exponential\Service\DraftsCleanup::INTERNAL_DRAFTS];
        $this->assertSame( \eZContentObjectVersion::STATUS_INTERNAL_DRAFT, $internal['status'] );
        $this->assertSame( array( 'InternalDraftsCleanUpLimit', 'InternalDraftsDuration', array( 'hours' => 24 ) ),
                           array( $internal['limit'], $internal['duration'], $internal['default'] ) );
        try
        {
            \Exponential\Service\DraftsCleanup::cleanup( 'archived' );
            $this->fail( 'an unknown kind is refused' );
        }
        catch ( InvalidArgumentException $e )
        {
            $this->assertStringContainsString( 'archived', $e->getMessage() );
        }
    }

    /** SV-10 */
    public function testDraftPartsAndCacheToolbarUseTheSharedCode()
    {
        $base = self::root() . '/kernel/private/classes/';
        foreach ( array( 'cronjobs/old_drafts_cleanup.php' => 'USER_DRAFTS', 'cronjobs/internal_drafts_cleanup.php' => 'INTERNAL_DRAFTS' ) as $file => $kind )
        {
            $code = (string) file_get_contents( $base . $file );
            $this->assertStringContainsString( "DraftsCleanup::cleanup( \\Exponential\\Service\\DraftsCleanup::$kind )", $code, $file );
            $this->assertStringNotContainsString( 'removeVersions(', $code, "$file leaves the removal to the service" );
            $this->assertStringNotContainsString( "'VersionManagement'", $code, "$file leaves the settings to the service" );
            $this->assertStringContainsString( 'if ( $processedCount !== null )', $code, "$file: null is 'no lifetime set'" );
        }
        $toolbar = (string) file_get_contents( $base . 'views/setup/cachetoolbar.php' );
        $this->assertStringContainsString( "\$cacheManager->clear( 'all' )", $toolbar );
        $this->assertStringContainsString( "\$cacheManager->clear( 'tag', \$tags )", $toolbar );
        $this->assertStringNotContainsString( '\\eZCache::clearAll(', $toolbar );
        $this->assertStringNotContainsString( '\\eZCache::clearByTag(', $toolbar );
        $this->assertStringContainsString( "'TemplateContent' => array( 'template', 'content' )", $toolbar, 'template first, then content, as before' );
    }
}
