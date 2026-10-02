<?php
/**
 * The runnables in the extension point survey (setup/rad, setup/radsurvey; #207 stage 4).
 *
 *  RS-01 — runnables() finds the commands, cronjob parts and views of the kernel and the extensions in the autoload
 *          arrays, each with kind, owner and path, and every one of them extends an Exponential\Runnable base
 *  RS-02 — A class of those namespaces that is not a runnable (the built-in server's router) is not counted
 *  RS-03 — Implementation[] entries: a subclass is counted as re-implemented, an entry naming no runnable, a missing
 *          replacement and one that does not extend the class are broken, with the reason
 *  RS-04 — runnableEvents(): before and after for each kind, before notifies, after filters
 *  RS-05 — The survey counts: runnables per kind and owner add up, the six events are in the event list, and the
 *          total includes the runnables
 *  RS-06 — The catalogue explains the mechanism with an example, and the survey view has a runnables section
 *
 * No database.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 * @group runnable
 */

require_once __DIR__ . '/../../../../../kernel/setup/expradsurvey.php';

class ezpTestRadSurveyWithImplementations extends expRADSurvey
{
    public static $map = array();
    public static function runnableImplementations()
    {
        return self::$map;
    }
}

class RadSurveyRunnablesTest extends PHPUnit\Framework\TestCase
{
    private static $runnables;

    private static function runnables()
    {
        if ( self::$runnables === null )
        {
            ezpTestRadSurveyWithImplementations::$map = array();
            self::$runnables = ezpTestRadSurveyWithImplementations::runnables();
        }
        return self::$runnables;
    }

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
    }

    /** RS-01 */
    public function testRunnablesAreFound()
    {
        $r = self::runnables();
        $this->assertGreaterThan( 250, count( $r['list'] ), 'the kernel alone has more than 250' );
        $kinds = array_count_values( array_column( $r['list'], 'kind' ) );
        foreach ( array( 'command', 'cronjob', 'view' ) as $kind )
            $this->assertArrayHasKey( $kind, $kinds );
        $owners = array_count_values( array_column( $r['list'], 'owner' ) );
        $this->assertArrayHasKey( 'kernel', $owners );
        $classes = array_column( $r['list'], 'class' );
        $this->assertContains( 'Exponential\\Command\\Kernel\\Ezcache', $classes );
        $this->assertContains( 'Exponential\\Cronjob\\Kernel\\Workflow', $classes );
        $this->assertContains( 'Exponential\\View\\Kernel\\Content\\History', $classes );
        foreach ( $r['list'] as $entry )
        {
            $this->assertSame( '', $entry['implementation'] );
            $this->assertMatchesRegularExpression( '/extends\s+\\\\?Exponential\\\\Runnable\\\\(Command|CronjobPart|ModuleView)\b/',
                                                   (string) file_get_contents( $entry['path'] ), $entry['class'] );
        }
    }

    /** RS-02 */
    public function testTheRouterIsNoRunnable()
    {
        $this->assertNotContains( 'Exponential\\Command\\Kernel\\VelocityRouter', array_column( self::runnables()['list'], 'class' ) );
    }

    /** RS-03 */
    public function testImplementationEntries()
    {
        if ( !class_exists( 'Exponential\\View\\Kernel\\Content\\History' ) )
            $this->markTestSkipped( 'the kernel classes are not autoloadable here' );
        if ( !class_exists( 'ezpTestRadHistoryView', false ) )
            eval( 'class ezpTestRadHistoryView extends \\Exponential\\View\\Kernel\\Content\\History {}' );
        ezpTestRadSurveyWithImplementations::$map = array(
            'Exponential\\View\\Kernel\\Content\\History' => 'ezpTestRadHistoryView',
            '\\Exponential\\Command\\Kernel\\Ezcache'      => 'ezpTestRadNoSuchClass',
            'Exponential\\Cronjob\\Kernel\\Workflow'       => 'ezpTestRadHistoryView',
            'myNotARunnable'                               => 'ezpTestRadHistoryView' );
        $r = ezpTestRadSurveyWithImplementations::runnables();
        ezpTestRadSurveyWithImplementations::$map = array();

        $byClass = array_column( $r['list'], 'implementation', 'class' );
        $this->assertSame( 'ezpTestRadHistoryView', $byClass['Exponential\\View\\Kernel\\Content\\History'] );
        $this->assertSame( '', $byClass['Exponential\\Command\\Kernel\\Ezcache'] );
        $this->assertSame( '', $byClass['Exponential\\Cronjob\\Kernel\\Workflow'] );

        $why = array_column( $r['broken'], 'why', 'class' );
        $this->assertCount( 3, $r['broken'] );
        $this->assertSame( 'the replacement class does not exist', $why['Exponential\\Command\\Kernel\\Ezcache'] );
        $this->assertSame( 'the replacement does not extend the class it replaces', $why['Exponential\\Cronjob\\Kernel\\Workflow'] );
        $this->assertSame( 'names no command, cronjob part or view class', $why['myNotARunnable'] );
    }

    /** RS-04 */
    public function testRunnableEvents()
    {
        $events = expRADSurvey::runnableEvents();
        $this->assertCount( 6, $events );
        foreach ( array( 'command', 'cronjob', 'view' ) as $kind )
        {
            $this->assertSame( 'notify', $events["runnable/$kind/before"]['kind'] );
            $this->assertSame( 'filter', $events["runnable/$kind/after"]['kind'] );
            $this->assertSame( \Exponential\Runnable\Runnable::eventName( $kind, 'after' ), $events["runnable/$kind/after"]['event'] );
        }
    }

    /** the whole survey, made once before the tests: it reaches kernel code that installs its own error and exception handlers */
    private static $survey;

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        if ( class_exists( 'eZINI' ) && class_exists( 'ezpI18n' ) )
            self::$survey = expRADSurvey::survey();
    }

    /** RS-05 */
    public function testSurveyCounts()
    {
        if ( self::$survey === null )
            $this->markTestSkipped( 'the survey needs the kernel classes' );
        $survey = self::$survey;
        $c = $survey['counts'];
        $this->assertSame( count( $survey['runnables']['list'] ), $c['runnables'] );
        $this->assertSame( $c['runnables'], $c['runnable_commands'] + $c['runnable_cronjobs'] + $c['runnable_views'] );
        $this->assertSame( $c['runnables'], $c['runnable_kernel'] + $c['runnable_extension'] );
        $this->assertSame( 0, $c['runnable_broken'], 'the shipped settings re-implement nothing' );
        foreach ( array_keys( expRADSurvey::runnableEvents() ) as $event )
            $this->assertArrayHasKey( $event, $survey['events'] );
        $this->assertSame( $c['settings'] + $c['repositories'] + $c['contracts'] + $c['views'] + $c['callables']
                           + $c['events'] + $c['overrides'] + $c['replaced'] + $c['runnables'], $c['total'] );
    }

    /** RS-06 */
    public function testCatalogueAndView()
    {
        $catalogue = (string) file_get_contents( 'kernel/setup/expradcatalogue.php' );
        $this->assertStringContainsString( "'runnable' => array(", $catalogue );
        $this->assertStringContainsString( 'Implementation[Exponential\\View\\Kernel\\Content\\History]=myHistoryView', $catalogue );
        $this->assertStringContainsString( "'runnable'  => 'A subclass", $catalogue );
        $view = (string) file_get_contents( 'kernel/private/classes/views/setup/radsurvey.php' );
        $this->assertStringContainsString( "case 'runnables':", $view );
        foreach ( array( 'admin', 'admin4' ) as $design )
            $this->assertStringContainsString( 'survey_counts.runnables', (string) file_get_contents( "design/$design/templates/setup/radsurvey.tpl" ) );
    }
}
