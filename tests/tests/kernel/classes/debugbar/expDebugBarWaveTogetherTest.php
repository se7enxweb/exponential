<?php
/**
 * The debug bar changes of 16 October 2026 together (Server tab, panels of extensions, OPcache/APCu/versions chips,
 * the PHP version for administrators only, the Rendering tab):
 *
 *  WT-01 - The summary draws the chips of extensions' panels before the runtime chips, which stay at the right end
 *  WT-02 - A viewer without rights gets neither the runtime chips nor the versions
 *  WT-03 - The kernel calls the Rendering tab's collector only when its class can be loaded, so a process with an
 *          older autoload array (a Velocity worker until its restart) keeps working
 *  WT-04 - The built-in tab with the id velocity is called Server
 *
 * No database.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expDebugBarWaveTogetherTest extends PHPUnit\Framework\TestCase
{
    private static function summary( $runtime, array $panels )
    {
        $summary = new ReflectionMethod( 'expDebugBarReport', 'summaryHtml' );
        return $summary->invoke( null, array(
            'time' => 0.1, 'sql_count' => 1, 'sql_ms' => 1.0, 'memory' => 1048576, 'memory_limit' => 0, 'templates' => null,
            'warnings' => 0, 'errors' => 0, 'notices' => 0, 'deprecations' => 0, 'levels' => array(), 'querycache' => null,
            'runtime' => $runtime, 'panels' => $panels ) );
    }

    public function testPanelChipsComeBeforeTheRuntimeChipsAtTheRightEnd()
    {
        $runtime = array( 'php' => '8.4.19', 'sapi' => 'fpm-fcgi', 'exponential' => '6.0.15', 'opcache' => null, 'apcu' => null );
        $html = self::summary( $runtime, array( array( 'x-mylayouts', 'panel-mylayouts', 'ok', 'Layout <b>', 'Rule home' ) ) );
        $panel = strpos( $html, 'data-key="panel-mylayouts"' );
        $versions = strpos( $html, 'data-key="versions"' );
        $this->assertNotFalse( $panel );
        $this->assertNotFalse( $versions );
        $this->assertLessThan( $versions, $panel, 'the panel chip comes before the versions' );
        $this->assertSame( 1, substr_count( $html, 'exp-debug-end' ), 'one chip pushes the runtime chips to the right end' );
        $this->assertMatchesRegularExpression( '#class="exp-debug-chip exp-debug-end" data-tab="velocity" data-key="versions"#', $html );
        $this->assertStringContainsString( 'Layout &lt;b&gt;', $html, 'the panel chip text is escaped' );
    }

    public function testNoRuntimeChipsWithoutRights()
    {
        $html = self::summary( expDebugBarReport::runtimeFigures( false ), array() );
        $this->assertStringNotContainsString( 'exp-debug-end', $html );
        $this->assertStringNotContainsString( 'data-key="versions"', $html );
        $this->assertStringNotContainsString( PHP_VERSION, $html );
    }

    public static function kernelFiles()
    {
        return array(
            'content view' => array( 'kernel/private/classes/views/content/view.php' ),
            'module view' => array( 'kernel/private/classes/runnable/moduleview.php' ),
            'node view functions' => array( 'kernel/classes/eznodeviewfunctions.php' ),
        );
    }

    /**
     * @dataProvider kernelFiles
     */
    #[PHPUnit\Framework\Attributes\DataProvider('kernelFiles')]
    public function testTheCollectorIsCalledOnlyWhenItsClassLoads( $file )
    {
        $source = file_get_contents( dirname( __DIR__, 5 ) . '/' . $file );
        $this->assertNotFalse( $source );
        $calls = preg_match_all( '/\\\\?expRenderStats::/', $source );
        $this->assertGreaterThan( 0, $calls );
        $guarded = preg_match_all( "/class_exists\\( 'expRenderStats' \\) && \\\\?expRenderStats::/", $source );
        $this->assertSame( $calls, $guarded, "every call in $file is guarded by class_exists()" );
    }

    public function testTheVelocityTabIsCalledServer()
    {
        $this->assertContains( 'Server', expDebugBarReport::scriptStrings() );
        $this->assertNotContains( 'Velocity', expDebugBarReport::scriptStrings() );
    }
}
