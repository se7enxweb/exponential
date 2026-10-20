<?php
/**
 * The registry of the system card providers (settings/syscard.ini [Providers], [Runtime]): order, switches, extensions,
 * budgets, timeouts and the safety net that keeps secrets off the card. No database.
 *
 *  SCR-01 - The providers come in the order of ProviderClasses[]; a class listed twice counts once
 *  SCR-02 - An extension's ProviderClasses[] adds its provider at the end; a missing or wrong class is reported, not fatal
 *  SCR-03 - Enabled[<id>]=disabled switches a provider off
 *  SCR-04 - Card providers are on all three cards, info and about providers only on theirs
 *  SCR-05 - Providers run by priority but their facts keep the card order; Priority[] and Budget[] override
 *  SCR-06 - When the total budget is spent, the lowest priorities show "n/a (time)"
 *  SCR-07 - A provider that throws shows "n/a (error)"; one that hangs is killed and shows "n/a (timeout)" (isolation)
 *  SCR-08 - A fact named like a secret is dropped; a password inside a value is masked
 *  SCR-09 - The shipped syscard.ini lists only existing providers, and each has a unique id
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expsyscardtestini.php';

class expSysCardRegistryTestProvider extends expSysCardBaseProvider
{
    public $sleep = 0.0;
    public $throw = false;
    public $returns = array();
    public static $ran = array();

    public function __construct( $id = 'one', array $modes = array( 'card' ), $priority = 50, $section = 'system' )
    {
        $this->id = $id;
        $this->modes = $modes;
        $this->priority = $priority;
        $this->section = $section;
    }

    public function collect( expSysCardContext $context )
    {
        self::$ran[] = $this->id;
        if ( $this->sleep > 0 )
            usleep( (int)( $this->sleep * 1000000 ) );
        if ( $this->throw )
            throw new RuntimeException( 'broken' );
        return $this->returns ? $this->returns : array( $this->fact( 'value', ucfirst( $this->id ), 'value of ' . $this->id ) );
    }
}

class expSysCardRegistryTestSecondProvider extends expSysCardRegistryTestProvider
{
    public function __construct()
    {
        parent::__construct( 'second' );
    }
}

class expSysCardRegistryTestExtensionProvider extends expSysCardRegistryTestProvider
{
    public function __construct()
    {
        parent::__construct( 'myext', array( 'card' ), 50, 'site' );
    }
}

class expSysCardRegistryTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        expSysCardRegistryTestProvider::$ran = array();
    }

    private function ids( array $providers )
    {
        return array_map( function ( $p ) { return $p->id(); }, $providers );
    }

    private function keys( array $facts )
    {
        return array_map( function ( $f ) { return $f->key; }, $facts );
    }

    public function testOrderAndDuplicates()
    {
        $registry = new expSysCardRegistry( array( 'ProviderClasses' => array( 'expSysCardRegistryTestSecondProvider', 'expSysCardRegistryTestExtensionProvider', 'expSysCardRegistryTestSecondProvider' ) ) );
        $this->assertSame( array( 'second', 'myext' ), $this->ids( $registry->providers() ) );
        $this->assertSame( array( 'expSysCardRegistryTestSecondProvider', 'expSysCardRegistryTestExtensionProvider' ), $registry->classes() );
    }

    public function testExtensionAddsAndBrokenClassesAreReported()
    {
        // what eZINI makes of the kernel's ProviderClasses[] plus an extension's syscard.ini.append.php
        $kernel = array( 'expSysCardRegistryTestSecondProvider' );
        $extension = array( 'expSysCardRegistryTestExtensionProvider', 'noSuchSysCardProvider', 'stdClass' );
        $registry = new expSysCardRegistry( array( 'ProviderClasses' => array_merge( $kernel, $extension ) ) );
        $this->assertSame( array( 'second', 'myext' ), $this->ids( $registry->providers() ) );
        $problems = $registry->problems();
        $this->assertCount( 2, $problems );
        $this->assertStringContainsString( 'noSuchSysCardProvider: no such class', $problems[0] );
        $this->assertStringContainsString( 'stdClass: not an expSysCardProvider', $problems[1] );
    }

    public function testDisable()
    {
        $registry = new expSysCardRegistry( array( 'ProviderClasses' => array( 'expSysCardRegistryTestSecondProvider', 'expSysCardRegistryTestExtensionProvider' ),
                                                   'Enabled' => array( 'MyExt' => 'disabled', 'second' => 'enabled' ) ) );
        $this->assertSame( array( 'second' ), $this->ids( $registry->providers() ) );
        $this->assertFalse( $registry->isEnabled( 'myext' ) );
        $this->assertTrue( $registry->isEnabled( 'unknown' ), 'on unless switched off' );
    }

    public function testModes()
    {
        $registry = new expSysCardRegistry();
        $registry->add( new expSysCardRegistryTestProvider( 'card', array( 'card' ) ) );
        $registry->add( new expSysCardRegistryTestProvider( 'inventory', array( 'info' ) ) );
        $registry->add( new expSysCardRegistryTestProvider( 'project', array( 'about' ) ) );
        $this->assertSame( array( 'card' ), $this->ids( $registry->providers( 'card' ) ) );
        $this->assertSame( array( 'card', 'inventory' ), $this->ids( $registry->providers( 'info' ) ) );
        $this->assertSame( array( 'card', 'project' ), $this->ids( $registry->providers( 'about' ) ) );
    }

    public function testPriorityRunsFirstButCardOrderStays()
    {
        $registry = new expSysCardRegistry( array( 'Priority' => array( 'low' => 99 ), 'Budget' => array( 'low' => 7 ) ) );
        $registry->add( new expSysCardRegistryTestProvider( 'low', array( 'card' ), 10 ) );
        $registry->add( new expSysCardRegistryTestProvider( 'mid', array( 'card' ), 50 ) );
        $registry->add( new expSysCardRegistryTestProvider( 'high', array( 'card' ), 90 ) );
        $result = $registry->collect( expSysCardContext::fake() );
        $this->assertSame( array( 'low', 'high', 'mid' ), expSysCardRegistryTestProvider::$ran, 'Priority[low]=99 runs it first' );
        $this->assertSame( array( 'low.value', 'mid.value', 'high.value' ), $this->keys( $result['facts'] ) );
        $this->assertSame( 7.0, $result['runs'][0]['budget'] );
        $this->assertSame( 'ok', $result['runs'][0]['status'] );
        $this->assertSame( 'low', $result['facts'][0]->provider );
    }

    public function testTotalBudget()
    {
        $registry = new expSysCardRegistry( array( 'TotalBudget' => 0.2 ) );
        $slow = new expSysCardRegistryTestProvider( 'slow', array( 'card' ), 90 );
        $slow->sleep = 0.3;
        $registry->add( $slow );
        $registry->add( new expSysCardRegistryTestProvider( 'late', array( 'card' ), 10 ) );
        $result = $registry->collect( expSysCardContext::fake() );
        $this->assertSame( array( 'slow' ), expSysCardRegistryTestProvider::$ran );
        $this->assertSame( 'slow', $result['runs'][0]['status'], 'over its budget without isolation: reported as slow' );
        $this->assertSame( 'skipped', $result['runs'][1]['status'] );
        $this->assertSame( 'late.unavailable', $result['facts'][1]->key );
        $this->assertSame( 'n/a (time)', $result['facts'][1]->value );
    }

    public function testErrorAndIsolatedTimeout()
    {
        $registry = new expSysCardRegistry();
        $broken = new expSysCardRegistryTestProvider( 'broken' );
        $broken->throw = true;
        $registry->add( $broken );
        $result = $registry->collect( expSysCardContext::fake() );
        $this->assertSame( 'error', $result['runs'][0]['status'] );
        $this->assertSame( 'n/a (error)', $result['facts'][0]->value );

        if ( !function_exists( 'pcntl_fork' ) || !function_exists( 'posix_kill' ) )
            $this->markTestSkipped( 'pcntl and posix are needed to isolate a provider' );
        $registry = new expSysCardRegistry( array( 'Isolation' => 'enabled', 'Budget' => array( 'hang' => 0.3, 'fast' => 2 ) ) );
        $hang = new expSysCardRegistryTestProvider( 'hang' );
        $hang->sleep = 5.0;
        $registry->add( $hang );
        $registry->add( new expSysCardRegistryTestProvider( 'fast' ) );
        $this->assertTrue( $registry->isolates() );
        $start = microtime( true );
        $result = $registry->collect( expSysCardContext::real( 'card', sys_get_temp_dir() ) );
        $this->assertLessThan( 2.0, microtime( true ) - $start, 'the hanging provider is killed when its budget is spent' );
        $this->assertSame( 'timeout', $result['runs'][0]['status'] );
        $this->assertSame( 'n/a (timeout)', $result['facts'][0]->value );
        $this->assertSame( 'ok', $result['runs'][1]['status'] );
        $this->assertSame( 'value of fast', $result['facts'][1]->value, 'the facts come back from the child process' );
    }

    public function testSecretsNeverReachTheCard()
    {
        $registry = new expSysCardRegistry();
        $leaky = new expSysCardRegistryTestProvider( 'leaky' );
        $leaky->returns = array(
            new expSysCardFact( 'leaky.password', 'Password', 'hunter2' ),
            new expSysCardFact( 'leaky.api_key', 'Key', 'k-123' ),
            new expSysCardFact( 'leaky.thing', 'Session ID', 'abc' ),
            new expSysCardFact( 'leaky.dsn', 'Address', 'mysql://root:hunter2@db.internal/site' ),
            new expSysCardFact( 'leaky.url', 'Address', 'mysql://root:hunter2@db.internal/site' ),
            new expSysCardFact( 'leaky.pair', 'Options', 'host=db password=hunter2 port=1' ),
            new expSysCardFact( 'leaky.header', 'Header', 'Authorization: Bearer abcdefghijklmnop' ),
            new expSysCardFact( 'leaky.fine', 'Version', '1.2.3', expSysCardFact::WARN, 'system', 'Upgrade; token=t0k3n123' ),
        );
        $registry->add( $leaky );
        $facts = $registry->collect( expSysCardContext::fake() )['facts'];
        $keys = $this->keys( $facts );
        $this->assertSame( array( 'leaky.url', 'leaky.pair', 'leaky.header', 'leaky.fine' ), $keys );
        $all = json_encode( array_map( function ( $f ) { return $f->toArray(); }, $facts ) );
        foreach ( array( 'hunter2', 'k-123', 'abcdefghijklmnop', 't0k3n123' ) as $secret )
            $this->assertStringNotContainsString( $secret, $all );
        $this->assertSame( 'mysql://root:***@db.internal/site', $facts[0]->value );
        $this->assertSame( '1.2.3', $facts[3]->value );
    }

    public function testShippedSettings()
    {
        $ini = expSysCardTestIni::read( expSysCardTestIni::file() );
        $this->assertIsArray( $ini );
        $classes = array_values( array_filter( $ini['Providers']['ProviderClasses'], 'strlen' ) );
        $this->assertGreaterThanOrEqual( 16, count( $classes ) );
        $registry = new expSysCardRegistry( array( 'ProviderClasses' => $classes ) );
        $ids = array();
        foreach ( array( 'card', 'info', 'about' ) as $mode )
            foreach ( $registry->providers( $mode ) as $provider )
                $ids[get_class( $provider )] = $provider->id();
        $this->assertSame( array(), $registry->problems() );
        $this->assertCount( count( $classes ), $ids, 'every listed class is a provider' );
        $this->assertSame( count( $ids ), count( array_unique( $ids ) ), 'ids are unique' );
        $this->assertSame( array( 'exponential', 'host', 'php', 'velocity', 'webserver', 'database', 'site', 'caches', 'help', 'cron', 'composer' ),
                           $this->ids( $registry->providers( 'card' ) ) );
        foreach ( array( 'rainbow', 'orange', 'mono', 'high-contrast', 'light' ) as $theme )
        {
            $this->assertContains( $theme, $ini['Theme']['Themes'] );
            $this->assertArrayHasKey( 'Theme-' . $theme, $ini );
        }
        $this->assertSame( 'rainbow', $ini['Theme']['Default'] );
    }
}
