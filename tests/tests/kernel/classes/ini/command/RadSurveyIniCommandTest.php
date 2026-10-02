<?php
/**
 * The exp:ini actions and scope providers in the extension point survey (setup/rad, setup/radsurvey).
 *
 *  RI-01 — iniCommand() lists every built-in action (each with its description) and the two built-in scope
 *          providers, none broken
 *  RI-02 — The survey counts them (ini_actions, ini_scope_providers, ini_command_broken, the inicommand tab) and
 *          leaves the total as it was: the registrations are already points of "settings that name a class"
 *  RI-03 — The catalogue explains the mechanism with an example, the survey view has an inicommand section and
 *          the admin and admin4 templates show the figures
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group ini
 */

require_once __DIR__ . '/../../../../../../kernel/setup/expradsurvey.php';

class RadSurveyIniCommandTest extends PHPUnit\Framework\TestCase
{
    private static $survey;

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 6 ) );
        if ( class_exists( 'eZINI' ) && class_exists( 'ezpI18n' ) )
            self::$survey = expRADSurvey::survey();
    }

    /** RI-01 */
    public function testIniCommandGroup()
    {
        $group = expRADSurvey::iniCommand();
        $this->assertSame( array_keys( expIniActionRegistry::BUILT_IN ), array_column( $group['actions'], 'name' ) );
        foreach ( $group['actions'] as $action )
        {
            $this->assertTrue( $action['ok'], $action['name'] );
            $this->assertTrue( $action['builtin'], $action['name'] );
            $this->assertNotSame( '', $action['description'] );
        }
        $this->assertSame( array( 'expIniCoreScopeProvider', 'expIniExtensionScopeProvider' ), array_column( $group['providers'], 'class' ) );
        $this->assertSame( array( true, true ), array_column( $group['providers'], 'ok' ) );
        $this->assertSame( array(), $group['broken'] );
    }

    /** RI-02 */
    public function testSurveyCounts()
    {
        if ( self::$survey === null )
            $this->markTestSkipped( 'the survey needs the kernel classes' );
        $c = self::$survey['counts'];
        $this->assertSame( count( expIniActionRegistry::BUILT_IN ), $c['ini_actions'] );
        $this->assertSame( 0, $c['ini_actions_registered'] );
        $this->assertSame( 2, $c['ini_scope_providers'] );
        $this->assertSame( 0, $c['ini_command_broken'] );
        $this->assertSame( $c['ini_actions'] + $c['ini_scope_providers'], $c['inicommand'] );
        $this->assertSame( $c['settings'] + $c['repositories'] + $c['contracts'] + $c['views'] + $c['callables']
                           + $c['events'] + $c['overrides'] + $c['replaced'] + $c['runnables'] + $c['registries_added'], $c['total'] );

        // each registration is a setting naming a class already
        $fromIni = array_filter( self::$survey['settings'], function ( $s ) { return $s['ini'] === 'ini.ini' && $s['section'] === 'IniCommandSettings'; } );
        $this->assertCount( $c['ini_actions'] + $c['ini_scope_providers'], $fromIni );
    }

    /** RI-03 */
    public function testCatalogueViewTemplates()
    {
        $catalogue = (string)file_get_contents( 'kernel/setup/expradcatalogue.php' );
        $this->assertStringContainsString( "'iniaction' => array(", $catalogue );
        $this->assertStringContainsString( 'Actions[dump]=myExtIniActionDump', $catalogue );
        $view = (string)file_get_contents( 'kernel/private/classes/views/setup/radsurvey.php' );
        $this->assertStringContainsString( "case 'inicommand':", $view );
        $this->assertStringContainsString( "'inicommand'   => array(", $view );
        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            $this->assertStringContainsString( 'survey_counts.ini_actions', (string)file_get_contents( "design/$design/templates/setup/radsurvey.tpl" ) );
            $this->assertStringContainsString( 'survey_counts.ini_command_broken', (string)file_get_contents( "design/$design/templates/setup/radsurvey.tpl" ) );
            $this->assertStringContainsString( 'rad_survey.counts.ini_actions', (string)file_get_contents( "design/$design/templates/setup/rad.tpl" ) );
        }
    }
}
