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
            'views/content/trash.php' => array( 'Trash::canEmpty', 'Trash::purgeObjects', 'Trash::emptyArchived' ),
            'commands/trashpurge.php' => array( 'Trash::purge(' ),
            'cronjobs/trashpurge.php' => array( 'Trash::purge(' ),
            'views/setup/session.php' => array( 'SessionGarbageCollector::collect( false )' ),
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
}
