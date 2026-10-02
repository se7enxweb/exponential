<?php
/**
 * The extension points of the runnables (#207 stage 4), guide doc/bc/6.0/cli_cronjob_view_abstractions.md:
 * events around run() and the [RunnableSettings] registry of site.ini.
 *
 *  EP-01 — eventName() is runnable/<kind>/<moment>
 *  EP-02 — main() of each kind notifies runnable/<kind>/before with ( runnable, class, scope ) before run()
 *  EP-03 — runnable/<kind>/after filters the result: what the listener returns is what main() returns
 *  EP-04 — Without listeners main() returns run()'s result unchanged (the views' $Result rule included)
 *  EP-05 — Implementation[<class>]=<subclass> re-implements a command, a cronjob part and a view; main() runs the
 *          subclass and the events name it; an entry that is not a subclass is ignored
 *  EP-06 — Listeners[]=<event>@<callback> of [RunnableSettings] are attached once per ezpEvent instance and fire;
 *          entries without "@" are skipped
 *  EP-07 — settings() with its own directory reads that site.ini privately, without creating a shared eZINI instance
 *          (how a command reads the settings before its script has set them up)
 *  EP-08 — settings/site.ini documents [RunnableSettings] Implementation[] and Listeners[] and leaves both empty
 *
 * No database. Uses ezpEvent and eZINI (autoloaded); ezpEvent is reset around each test.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 * @group runnable
 */

foreach ( array( 'runnable', 'command', 'cronjobpart', 'moduleview' ) as $file )
    require_once __DIR__ . '/../../../../../kernel/private/classes/runnable/' . $file . '.php';

const EZP_TEST_EP_SETTINGS = 'tests/tests/kernel/classes/runnable/fixtures/settings';

trait ezpTestEpFixtureSettings
{
    public static function settings( $rootDir = 'settings' )
    {
        return parent::settings( EZP_TEST_EP_SETTINGS );
    }
}

class ezpTestEpCommand extends \Exponential\Runnable\Command
{
    use ezpTestEpFixtureSettings;
    public function run()
    {
        return 'command';
    }
}
class ezpTestEpCommandImpl extends ezpTestEpCommand
{
    public function run()
    {
        return 'command-impl';
    }
}

class ezpTestEpCronjobPart extends \Exponential\Runnable\CronjobPart
{
    use ezpTestEpFixtureSettings;
    public function run( array $scope )
    {
        return 'cronjob:' . ( isset( $scope['isQuiet'] ) ? (int) $scope['isQuiet'] : '-' );
    }
}
class ezpTestEpCronjobPartImpl extends ezpTestEpCronjobPart
{
    public function run( array $scope )
    {
        return 'cronjob-impl';
    }
}

class ezpTestEpView extends \Exponential\Runnable\ModuleView
{
    use ezpTestEpFixtureSettings;
    public function run( array $scope )
    {
        return $this->viewResult( isset( $scope['Result'] ) ? $scope['Result'] : null, 'returned' );
    }
}
class ezpTestEpViewImpl extends ezpTestEpView
{
    public function run( array $scope )
    {
        return array( 'content' => 'view-impl' );
    }
}

class ezpTestEpOther extends \Exponential\Runnable\Command
{
    use ezpTestEpFixtureSettings;
    public function run()
    {
        return 'other';
    }
}

/** the listener the fixture names */
class ezpTestEpListener
{
    public static $calls = array();
    public static function before( $runnable, $class, $scope )
    {
        self::$calls[] = array( 'before', $class, $scope );
    }
    public static function after( $result, $runnable, $class, $scope )
    {
        self::$calls[] = array( 'after', $class );
        if ( is_array( $result ) )
            $result['listened'] = true;
        return $result;
    }
}

/** a plain runnable without fixture settings: whatever the real settings say (nothing for these names) */
class ezpTestEpPlainView extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        return $this->viewResult( isset( $scope['Result'] ) ? $scope['Result'] : null, 'returned' );
    }
}

class RunnableExtensionPointsTest extends PHPUnit\Framework\TestCase
{
    private $log = array();

    protected function setUp(): void
    {
        if ( !class_exists( 'ezpEvent' ) || !class_exists( 'eZINI' ) )
            $this->markTestSkipped( 'ezpEvent and eZINI are not autoloadable here' );
        ezpEvent::resetInstance();
        ezpTestEpListener::$calls = array();
        $this->log = array();
    }

    protected function tearDown(): void
    {
        if ( class_exists( 'ezpEvent', false ) )
            ezpEvent::resetInstance();
    }

    private function listen( $kind )
    {
        $log = &$this->log;
        $events = ezpEvent::getInstance();
        $events->attach( "runnable/$kind/before", function ( $runnable, $class, $scope ) use ( &$log ) {
            $log[] = array( 'before', get_class( $runnable ), $class, $scope );
        } );
        $events->attach( "runnable/$kind/after", function ( $result, $runnable, $class, $scope ) use ( &$log ) {
            $log[] = array( 'after', $result, $class );
            return is_string( $result ) ? $result . '+filtered' : $result;
        } );
    }

    /** EP-01 */
    public function testEventName()
    {
        $this->assertSame( 'runnable/command/before', \Exponential\Runnable\Runnable::eventName( 'command', 'before' ) );
        $this->assertSame( 'runnable/view/after', \Exponential\Runnable\Runnable::eventName( 'view', 'after' ) );
        $this->assertSame( 'runnable', \Exponential\Runnable\Runnable::EVENT_PREFIX );
    }

    /** EP-02, EP-03, EP-05 for the three kinds */
    public function testEventsAroundEachKindAndReimplementation()
    {
        $this->listen( 'command' );
        $this->listen( 'cronjob' );
        $this->listen( 'view' );

        $this->assertSame( 'command-impl+filtered', ezpTestEpCommand::main( '/bin/php/x.php' ) );
        $this->assertSame( array( 'before', 'ezpTestEpCommandImpl', 'ezpTestEpCommandImpl', array() ), $this->log[0] );
        $this->assertSame( array( 'after', 'command-impl', 'ezpTestEpCommandImpl' ), $this->log[1] );

        $scope = array( 'cli' => 'c', 'isQuiet' => true );
        $this->assertSame( 'cronjob-impl+filtered', ezpTestEpCronjobPart::main( '/cronjobs/x.php', $scope ) );
        $this->assertSame( array( 'before', 'ezpTestEpCronjobPartImpl', 'ezpTestEpCronjobPartImpl', $scope ), $this->log[2] );

        $r = ezpTestEpView::main( '/kernel/m/v.php', array( 'Params' => array( 'a' => 1 ) ) );
        $this->assertSame( 'view-impl', $r['content'] );
        $this->assertSame( 'ezpTestEpViewImpl', $this->log[4][2] );
        $this->assertSame( array( 'Params' => array( 'a' => 1 ) ), $this->log[4][3] );
        $this->assertCount( 6, $this->log );
    }

    /** EP-04 */
    public function testWithoutListenersTheResultIsUnchanged()
    {
        $this->assertSame( 'returned', ezpTestEpPlainView::main( '/v.php', array() ) );
        $this->assertSame( array( 'content' => 'x' ), ezpTestEpPlainView::main( '/v.php', array( 'Result' => array( 'content' => 'x' ) ) ) );
        $this->assertSame( 'returned', ezpTestEpPlainView::main( '/v.php', array( 'Result' => array() ) ) );
    }

    /** EP-05 */
    public function testReimplementationRegistry()
    {
        $this->assertSame( 'ezpTestEpCommandImpl', ezpTestEpCommand::implementation() );
        $this->assertSame( 'ezpTestEpCronjobPartImpl', ezpTestEpCronjobPart::implementation() );
        $this->assertSame( 'ezpTestEpViewImpl', ezpTestEpView::implementation() );
        $this->assertInstanceOf( 'ezpTestEpViewImpl', ezpTestEpView::create( '/v.php' ) );
        // ezpTestEpOther => ezpTestEpView is not a subclass: ignored
        $this->assertSame( 'ezpTestEpOther', ezpTestEpOther::implementation() );
        $this->assertSame( 'other', ezpTestEpOther::main() );
        // a class with no entry is itself
        $this->assertSame( 'ezpTestEpPlainView', ezpTestEpPlainView::implementation() );
    }

    /** EP-06 */
    public function testSettingsListenersAttachOnceAndFire()
    {
        $this->assertSame( array(
            array( 'runnable/view/after', 'ezpTestEpListener::after' ),
            array( 'runnable/view/before', 'ezpTestEpListener::before' ),
        ), ezpTestEpView::settingsListeners() );

        $r1 = ezpTestEpView::main( '/v.php', array() );
        $r2 = ezpTestEpView::main( '/v.php', array() );
        $this->assertTrue( $r1['listened'] );
        $this->assertTrue( $r2['listened'] );
        // two runs, each one before and one after: attached once, not once per run
        $this->assertSame( array( 'before', 'after', 'before', 'after' ), array_column( ezpTestEpListener::$calls, 0 ) );
        $this->assertSame( 'ezpTestEpViewImpl', ezpTestEpListener::$calls[0][1] );

        // a new ezpEvent instance gets them attached again
        ezpEvent::resetInstance();
        ezpTestEpListener::$calls = array();
        ezpTestEpView::main( '/v.php', array() );
        $this->assertCount( 2, ezpTestEpListener::$calls );
    }

    /** EP-07 */
    public function testPrivateSettingsRead()
    {
        $ini = \Exponential\Runnable\Runnable::settings( EZP_TEST_EP_SETTINGS );
        $this->assertInstanceOf( 'eZINI', $ini );
        $this->assertSame( 'ezpTestEpViewImpl', $ini->variable( 'RunnableSettings', 'Implementation' )['ezpTestEpView'] );
        $this->assertSame( $ini, \Exponential\Runnable\Runnable::settings( EZP_TEST_EP_SETTINGS ), 'read once per process' );
        $this->assertFalse( eZINI::isLoaded( 'site.ini', EZP_TEST_EP_SETTINGS ), 'no shared instance is created' );
    }

    /** EP-08 */
    public function testSiteIniDocumentsTheRegistry()
    {
        $ini = (string) file_get_contents( dirname( __DIR__, 5 ) . '/settings/site.ini' );
        $this->assertMatchesRegularExpression( '/^\[RunnableSettings\]$/m', $ini );
        $section = substr( $ini, strpos( $ini, '[RunnableSettings]' ) );
        $section = substr( $section, 0, strpos( $section, "\n[", 1 ) );
        $this->assertMatchesRegularExpression( '/^Implementation\[\]$/m', $section );
        $this->assertMatchesRegularExpression( '/^Listeners\[\]$/m', $section );
        $this->assertStringContainsString( 'runnable/<kind>/before', $section );
        $this->assertStringContainsString( 'runnable/<kind>/after', $section );
        // nothing is re-implemented or listened to by default
        $this->assertDoesNotMatchRegularExpression( '/^(Implementation\[.+\]|Listeners\[\])=/m', $section );
    }
}
