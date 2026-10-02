<?php
/**
 * The Exp Debug bar in the extension point survey (setup/rad, setup/radsurvey): its settings and presets are two
 * registries read out of debugbar.ini, entry by entry, added to the total once (they name no class), and the
 * survey carries the debug bar's own figures (registered, discovered, presets, extension registrations).
 *
 * Reads the live installation's files and settings; no test database.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/debugbar/RadSurveyDebugBarTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/../../../../../kernel/setup/expradsurvey.php';

class RadSurveyDebugBarTest extends PHPUnit\Framework\TestCase
{
    private static $survey;

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        if ( class_exists( 'eZINI' ) && class_exists( 'ezpI18n' ) && class_exists( 'expDebugBarRegistry' ) )
            self::$survey = expRADSurvey::survey();
    }

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        if ( self::$survey === null )
            $this->markTestSkipped( 'the survey needs the kernel classes' );
    }

    /** The [Setting_*] or [Preset_*] blocks of the kernel's settings/debugbar.ini. */
    private static function kernelBlocks( $prefix )
    {
        preg_match_all( '/^\[' . $prefix . '_([A-Za-z0-9_]+)\]/m', file_get_contents( 'settings/debugbar.ini' ), $m );
        return $m[1];
    }

    public function testRegistriesAreRead()
    {
        $r = self::$survey['registries'];
        $this->assertArrayHasKey( 'debugbar', $r );
        $this->assertArrayHasKey( 'debugbarpresets', $r );
        $this->assertSame( array(), $r['debugbar']['broken'] );
        $this->assertSame( array(), $r['debugbarpresets']['broken'] );

        $kernel = array_values( array_filter( $r['debugbar']['entries'], function ( $e ) { return $e['origin'] === 'kernel'; } ) );
        $this->assertSame( self::kernelBlocks( 'Setting' ), array_column( $kernel, 'name' ) );
        $this->assertContains( 'debug_ip_list', array_column( $kernel, 'name' ) );
        $this->assertSame( 'iplist', $kernel[array_search( 'debug_ip_list', array_column( $kernel, 'name' ) )]['value'] );

        $presets = array_values( array_filter( $r['debugbarpresets']['entries'], function ( $e ) { return $e['origin'] === 'kernel'; } ) );
        $this->assertSame( self::kernelBlocks( 'Preset' ), array_column( $presets, 'name' ) );
        $this->assertSame( array( 'Template work', 'SQL tuning', 'Everything', 'Off' ), array_column( $presets, 'value' ) );
    }

    public function testAddedToTheTotalOnce()
    {
        $c = self::$survey['counts'];
        foreach ( array( 'debugbar', 'debugbarpresets' ) as $key )
        {
            $this->assertSame( $c['registry_' . $key], $c['registry_' . $key . '_added'], "$key entries name no class" );
            $this->assertSame( 0, $c['registry_' . $key . '_broken'] );
        }
        $sum = 0;
        foreach ( array_keys( expRADSurvey::totalGroups() ) as $group )
            $sum += $c[$group];
        $this->assertSame( $sum, $c['total'] );
        $this->assertContains( 'registry_debugbar', array_column( expRADSurvey::groupCounts( self::$survey ), 'key' ) );
    }

    public function testDebugBarFigures()
    {
        $c = self::$survey['counts'];
        $own = ( new expDebugBarRegistry() )->survey();
        $this->assertSame( $own['settings'], $c['debugbar_settings'] );
        $this->assertSame( $own['registered'], $c['debugbar_registered'] );
        $this->assertSame( $own['extension_registered'], $c['debugbar_extension_registered'] );
        $this->assertSame( $own['discovered_blocks'] + $own['discovered_extensions'], $c['debugbar_discovered'] );
        $this->assertSame( 4, $c['debugbar_presets'] );
        $this->assertSame( 0, $c['debugbar_problems'] );
        $this->assertGreaterThanOrEqual( 35, $c['debugbar_registered'] );
        $this->assertSame( $c['debugbar_settings'], $c['debugbar_registered'] + $c['debugbar_discovered'] );
    }

    public function testRegistryProblem()
    {
        $this->assertSame( '', expRADSurvey::registryProblem( 'iplist', 'debugbar-type' ) );
        $this->assertSame( '', expRADSurvey::registryProblem( 'Bool', 'debugbar-type' ) );
        $this->assertStringContainsString( 'not one of', expRADSurvey::registryProblem( 'checkbox', 'debugbar-type' ) );
        $this->assertSame( '', expRADSurvey::registryProblem( 'Template work', 'debugbar-preset' ) );
        $this->assertSame( 'a preset needs a name', expRADSurvey::registryProblem( ' ', 'debugbar-preset' ) );
    }
}
