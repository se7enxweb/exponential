<?php
/**
 * Panels of extensions in the Exp Debug bar (expDebugBarPanelInterface, debugbar.ini [DebugBarSettings] Panels[] and
 * [Panel_<id>] Class=): expDebugBarRegistry::panels() and expDebugBarReport::extensionPanels().
 *
 *  DP-01 - A registered panel gets its tab id "x-<id>", its title, its chip and its data
 *  DP-02 - A panel the user may not see draws nothing
 *  DP-03 - A wrong entry (bad id, no class, unknown class, no interface, an id twice in another case) is left out
 *          and named
 *  DP-04 - A panel that throws, or gives no title, is left out; the others are drawn
 *  DP-05 - The chip of a panel is part of the summary, escaped, and opens the panel's tab; an unknown level is ok
 *  DP-06 - Without Panels[] nothing changes
 *  DP-07 - The report draws the panel's tab after Memory, its section with its HTML, and its data in the JSON
 *  DP-08 - The warning about a panel left out is among the messages of the same report
 *
 * No database: the panels are classes of this file, registered through the settings, which are put back afterwards.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expDebugBarPanelsTest extends PHPUnit\Framework\TestCase
{
    /** @var array group/variable => array( value ) or null when it was not set */
    private $saved = array();

    /** @var array the globals the warnings of eZDebug set, name => array( value ) or null */
    private $globals = array();

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        if ( !interface_exists( 'expDebugBarPanelInterface' ) || !class_exists( 'expDebugBarRegistry' ) )
            $this->markTestSkipped( 'The debug bar classes are not loaded' );
        foreach ( array( 'eZDebugWarning', 'eZDebugWarningCount' ) as $name )
            $this->globals[$name] = array_key_exists( $name, $GLOBALS ) ? array( $GLOBALS[$name] ) : null;
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance( 'debugbar.ini' );
        foreach ( $this->saved as $key => $value )
        {
            list( $group, $name ) = explode( '/', $key, 2 );
            if ( $value === null )
                $ini->removeSetting( $group, $name );
            else
                $ini->setVariable( $group, $name, $value[0] );
        }
        $this->saved = array();
        foreach ( $this->globals as $name => $value )
        {
            if ( $value === null )
                unset( $GLOBALS[$name] );
            else
                $GLOBALS[$name] = $value[0];
        }
        $this->globals = array();
    }

    private function set( $group, $name, $value )
    {
        $ini = eZINI::instance( 'debugbar.ini' );
        $key = $group . '/' . $name;
        if ( !array_key_exists( $key, $this->saved ) )
            $this->saved[$key] = $ini->hasVariable( $group, $name ) ? array( $ini->variable( $group, $name ) ) : null;
        $ini->setVariable( $group, $name, $value );
    }

    /**
     * Registers the panels: id => class.
     */
    private function register( array $panels )
    {
        $this->set( 'DebugBarSettings', 'Panels', array_keys( $panels ) );
        foreach ( $panels as $id => $class )
        {
            if ( $class !== null )
                $this->set( 'Panel_' . $id, 'Class', $class );
        }
    }

    public function testARegisteredPanelIsReadyToDraw()
    {
        $this->register( array( 'Layouts' => 'expDebugBarPanelsTestPanel' ) );
        $panels = expDebugBarReport::extensionPanels();
        $this->assertCount( 1, $panels );
        $p = $panels[0];
        $this->assertSame( 'Layouts', $p['id'] );
        $this->assertSame( 'x-layouts', $p['tab'] );
        $this->assertSame( 'Test layouts', $p['title'] );
        $this->assertSame( array( 'x-layouts', 'panel-layouts', 'warn', 'Layout: Home <3 cols>', 'Rule "home"' ), $p['chip'] );
        $this->assertSame( '<p>zones</p>', $p['html'] );
        $this->assertSame( array( 'layout' => 'home' ), $p['data'] );
    }

    public function testAPanelTheUserMayNotSeeDrawsNothing()
    {
        $this->register( array( 'hidden' => 'expDebugBarPanelsTestHiddenPanel' ) );
        $this->assertSame( array(), expDebugBarReport::extensionPanels() );
    }

    public function testWrongEntriesAreLeftOutAndNamed()
    {
        $this->register( array( 'bad id' => 'expDebugBarPanelsTestPanel', 'noclass' => null, 'unknown' => 'expDebugBarPanelsTestNoSuchClass',
                                'plain' => 'stdClass', 'good' => 'expDebugBarPanelsTestPanel', 'GOOD' => 'expDebugBarPanelsTestPanel' ) );
        $panels = ( new expDebugBarRegistry() )->panels( $broken );
        $this->assertSame( array( 'good' => 'expDebugBarPanelsTestPanel' ), $panels );
        $this->assertSame( array( 'bad id', 'noclass', 'unknown', 'plain', 'GOOD' ), array_keys( $broken ) );
        $this->assertStringContainsString( 'does not implement expDebugBarPanelInterface', $broken['plain'] );
        $this->assertStringContainsString( 'same id as good', $broken['GOOD'] );
    }

    public function testAFailingPanelIsLeftOutAndTheOthersAreDrawn()
    {
        $this->register( array( 'broken' => 'expDebugBarPanelsTestThrowingPanel', 'untitled' => 'expDebugBarPanelsTestUntitledPanel',
                                'fine' => 'expDebugBarPanelsTestPanel' ) );
        $this->assertSame( array( 'fine' ), array_column( expDebugBarReport::extensionPanels(), 'id' ) );
    }

    public function testTheChipIsPartOfTheSummary()
    {
        $this->register( array( 'Layouts' => 'expDebugBarPanelsTestPanel', 'other' => 'expDebugBarPanelsTestNoChipPanel' ) );
        $panels = expDebugBarReport::extensionPanels();
        $this->assertNull( $panels[1]['chip'], 'a panel without a chip' );

        $summary = new ReflectionMethod( 'expDebugBarReport', 'summaryHtml' );
        $html = $summary->invoke( null, array(
            'time' => 0.1, 'sql_count' => 1, 'sql_ms' => 1.0, 'memory' => 1048576, 'memory_limit' => 0, 'templates' => null,
            'warnings' => 0, 'errors' => 0, 'notices' => 0, 'deprecations' => 0, 'levels' => array(), 'querycache' => null,
            'panels' => array_values( array_filter( array_column( $panels, 'chip' ) ) ) ) );
        $this->assertStringContainsString( 'data-tab="x-layouts" data-key="panel-layouts" data-level="warn"', $html );
        $this->assertStringContainsString( '>Layout: Home &lt;3 cols&gt;</button>', $html );
        $this->assertStringNotContainsString( '<3 cols>', $html );
    }

    public function testAnUnknownLevelIsOk()
    {
        $this->register( array( 'odd' => 'expDebugBarPanelsTestOddLevelPanel' ) );
        $this->assertSame( 'ok', expDebugBarReport::extensionPanels()[0]['chip'][2] );
    }

    /**
     * The debug report of a fresh eZDebug with its log files in a temporary directory, the debug globals put back.
     *
     * @return string
     */
    private function report()
    {
        $keys = array( 'eZDebugGlobalInstance', 'eZDebugEnabled', 'eZDebugLogOnly', 'eZDebugAlwaysLog', 'eZDebugLogFileEnabled' );
        $saved = array();
        foreach ( $keys as $key )
            $saved[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
        $dir = 'var/tmp/phpunit-debugbar-panels-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 ) . '/';
        mkdir( $dir, 0777, true );
        try
        {
            unset( $GLOBALS['eZDebugAlwaysLog'], $GLOBALS['eZDebugLogFileEnabled'] );
            $debug = new eZDebug();
            foreach ( $debug->LogFiles as $level => $file )
                $debug->LogFiles[$level] = array( $dir, $file[1] );
            $GLOBALS['eZDebugGlobalInstance'] = $debug;
            $GLOBALS['eZDebugEnabled'] = true;
            $GLOBALS['eZDebugLogOnly'] = false;
            ob_start();
            $report = eZDebug::printReport( false, true, true );
            ob_end_clean();
        }
        finally
        {
            foreach ( $saved as $key => $value )
            {
                if ( $value === null )
                    unset( $GLOBALS[$key] );
                else
                    $GLOBALS[$key] = $value[0];
            }
            foreach ( glob( $dir . '*' ) as $file )
                unlink( $file );
            rmdir( $dir );
        }
        return $report;
    }

    public function testTheReportDrawsThePanel()
    {
        $this->register( array( 'Layouts' => 'expDebugBarPanelsTestPanel' ) );
        $report = $this->report();
        $this->assertMatchesRegularExpression( '#data-tab="memory">[^<]*</button><button type="button" role="tab" class="exp-debug-tab" id="exp-debug-tab-x-layouts" aria-controls="exp-debug-panel-x-layouts"[^>]*>Test layouts</button>#',
                                               $report, 'the tab right after Memory' );
        $this->assertMatchesRegularExpression( '#<section class="exp-debug-panel" role="tabpanel" id="exp-debug-panel-x-layouts"[^>]*><h3 class="exp-debug-panel-title">Test layouts</h3><p>zones</p></section>#', $report );
        $this->assertStringContainsString( '"panels":{"Layouts":{"layout":"home"}}', $report );
    }

    public function testTheWarningAboutAPanelLeftOutIsInTheSameReport()
    {
        $this->register( array( 'broken' => 'expDebugBarPanelsTestThrowingPanel' ) );
        $report = $this->report();
        $this->assertStringContainsString( 'Debug bar panel &#039;broken&#039; (expDebugBarPanelsTestThrowingPanel) left out: broken on purpose', $report );
        $this->assertStringContainsString( '"warnings":1', $report, 'counted in the summary of the same report' );
        $this->assertStringNotContainsString( 'exp-debug-tab-x-broken', $report );
    }

    public function testWithoutPanelsNothingChanges()
    {
        $this->set( 'DebugBarSettings', 'Panels', array() );
        $this->assertSame( array(), expDebugBarReport::extensionPanels() );
        $this->assertSame( array(), ( new expDebugBarRegistry() )->panels( $broken ) );
        $this->assertSame( array(), $broken );
    }
}

/** A panel with everything. */
class expDebugBarPanelsTestPanel implements expDebugBarPanelInterface
{
    public function visible()
    {
        return true;
    }
    public function title()
    {
        return 'Test layouts';
    }
    public function chip()
    {
        return array( 'text' => 'Layout: Home <3 cols>', 'title' => 'Rule "home"', 'level' => 'warn' );
    }
    public function html()
    {
        return '<p>zones</p>';
    }
    public function data()
    {
        return array( 'layout' => 'home' );
    }
}

/** A panel the user may not see. */
class expDebugBarPanelsTestHiddenPanel extends expDebugBarPanelsTestPanel
{
    public function visible()
    {
        return false;
    }
}

/** A panel without a chip. */
class expDebugBarPanelsTestNoChipPanel extends expDebugBarPanelsTestPanel
{
    public function chip()
    {
        return null;
    }
}

/** A panel whose chip names a level the bar does not know. */
class expDebugBarPanelsTestOddLevelPanel extends expDebugBarPanelsTestPanel
{
    public function chip()
    {
        return array( 'text' => 'odd', 'level' => 'purple' );
    }
}

/** A panel that fails. */
class expDebugBarPanelsTestThrowingPanel extends expDebugBarPanelsTestPanel
{
    public function html()
    {
        throw new RuntimeException( 'broken on purpose' );
    }
}

/** A panel without a title. */
class expDebugBarPanelsTestUntitledPanel extends expDebugBarPanelsTestPanel
{
    public function title()
    {
        return '';
    }
}
