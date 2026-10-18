<?php
/**
 * The editor engines under an enforced Content Security Policy (doc/features/6.0/content-security-policy.md): an
 * engine that needs inline scripts (TinyMCE 3) is not offered where the policy runs none, and editors get the
 * configured or the first other engine instead of an editor that cannot start.
 *
 * - CSPE-01: TinyMCE 3 says it needs inline scripts; TinyMCE 8 and an engine without the method do not.
 * - CSPE-02: blocked, a TinyMCE 3 preference, configuration or ForceEngine falls back to an engine that runs.
 * - CSPE-03: blocked, the engine switch and the engine page leave TinyMCE 3 out, and choosing it is refused.
 * - CSPE-04: allowed (report-only, or enforce with 'unsafe-inline'), everything is as before.
 * - CSPE-05: without an override the policy decides (site.ini [ContentSecurityPolicy], in memory).
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/ezoe/expOEContentSecurityPolicyEngineTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expOETestCase.php';

class expOEContentSecurityPolicyEngineTest extends expOETestCase
{
    /** @var array|null site.ini [ContentSecurityPolicy] and [DesignSettings] SiteDesign as they were */
    protected $savedPolicy = null;

    public function tearDown(): void
    {
        if ( $this->savedPolicy !== null )
        {
            $ini = eZINI::instance();
            foreach ( $this->savedPolicy as $name => $value )
            {
                if ( $value === null )
                    $ini->removeSetting( 'ContentSecurityPolicy', $name );
                else
                    $ini->setVariable( 'ContentSecurityPolicy', $name, $value );
            }
            $this->savedPolicy = null;
        }
        parent::tearDown();
    }

    protected function block()
    {
        expOEEditor::setInlineScriptsAllowed( false );
        expOEEditor::reset();
    }

    /** CSPE-01 */
    public function testTinyMCE3NeedsInlineScriptsAndTheOthersDoNot()
    {
        $engines = expOEEditor::engines();
        $this->assertTrue( $engines['tinymce3']->needsInlineScripts() );
        $this->assertFalse( $engines['tinymce8']->needsInlineScripts() );
        $this->assertFalse( ( new expOETestThirdEngine() )->needsInlineScripts() );
    }

    /** CSPE-02 */
    public function testABlockedPreferenceFallsBackToTheConfiguredEngine()
    {
        $this->ini( 'EditorEngine', 'tinymce8' );
        $this->block();
        $this->assertFalse( expOEEditor::hasEngine( 'tinymce3' ) );
        $this->assertSame( 'tinymce8', expOEEditor::resolve( 'tinymce3' ) );
        $this->preference( 'tinymce3' );
        $this->assertSame( 'tinymce8', expOEEditor::resolve() );
    }

    /** CSPE-02 */
    public function testABlockedConfiguredEngineFallsBackToTheFirstThatRuns()
    {
        $this->ini( 'EditorEngine', 'tinymce3' );
        $this->block();
        $this->assertSame( 'tinymce8', expOEEditor::configuredEngine() );
        $this->assertSame( 'tinymce8', expOEEditor::resolve( '' ) );
        $this->assertSame( 'tinymce8', expOEEditor::info( expOEEditor::resolve( '' ) )['identifier'] );
    }

    /** CSPE-02 */
    public function testABlockedForcedEngineIsIgnored()
    {
        $this->ini( 'EditorEngine', 'tinymce8' );
        $this->ini( 'ForceEngine', 'tinymce3' );
        $this->block();
        $this->assertSame( '', expOEEditor::forcedEngine() );
        $this->assertSame( 'tinymce8', expOEEditor::resolve( 'tinymce3' ) );
    }

    /** CSPE-03 */
    public function testTheSwitchAndTheEnginePageLeaveABlockedEngineOut()
    {
        $this->ini( 'EditorEngine', 'tinymce8' );
        $this->block();
        $this->assertArrayNotHasKey( 'tinymce3', expOEEditor::labels() );
        $this->assertNotContains( 'tinymce3', array_column( expOEEditor::switchOptions( 'tinymce8' ), 'identifier' ) );
        $this->assertNotContains( 'tinymce3', array_column( expOEEditor::switchChoices( 'tinymce8' ), 'identifier' ) );
        $details = array_column( expOEEditor::details(), null, 'identifier' );
        $this->assertSame( 'blocked', $details['tinymce3']['status'] );
        $this->assertStringContainsString( 'Content Security Policy', $details['tinymce3']['why'] );
        $this->assertSame( 'available', $details['tinymce8']['status'] );
    }

    /** CSPE-03 */
    public function testChoosingABlockedEngineIsRefused()
    {
        $this->preference( 'tinymce8' );
        $this->block();
        $this->assertFalse( expOEEditor::setUserEngine( 'tinymce3' ) );
        $this->assertSame( 'tinymce8', eZPreferences::value( expOEEditor::PREFERENCE ) );
    }

    /** CSPE-04 */
    public function testAllowedEverythingIsAsBefore()
    {
        $this->ini( 'EditorEngine', 'tinymce3' );
        expOEEditor::setInlineScriptsAllowed( true );
        $this->assertTrue( expOEEditor::hasEngine( 'tinymce3' ) );
        $this->assertSame( 'tinymce3', expOEEditor::configuredEngine() );
        $this->assertSame( 'tinymce3', expOEEditor::resolve( '' ) );
        $this->assertArrayHasKey( 'tinymce3', expOEEditor::labels() );
    }

    /** CSPE-05 */
    public function testWithoutAnOverrideThePolicyDecides()
    {
        if ( !class_exists( 'expContentSecurityPolicy' ) || !method_exists( 'expContentSecurityPolicy', 'allowsInlineScripts' ) )
            $this->markTestSkipped( 'expContentSecurityPolicy::allowsInlineScripts() is not there' );
        $ini = eZINI::instance();
        $this->savedPolicy = array();
        foreach ( array( 'Mode', 'Directives' ) as $name )
            $this->savedPolicy[$name] = $ini->hasVariable( 'ContentSecurityPolicy', $name ) ? $ini->variable( 'ContentSecurityPolicy', $name ) : null;
        $directives = is_array( $this->savedPolicy['Directives'] ) ? $this->savedPolicy['Directives'] : array();
        expOEEditor::setInlineScriptsAllowed( null );

        $ini->setVariable( 'ContentSecurityPolicy', 'Mode', 'report-only' );
        $ini->setVariable( 'ContentSecurityPolicy', 'Directives', array( 'script-src' => "'self'" ) + $directives );
        $this->assertTrue( expOEEditor::inlineScriptsAllowed(), 'report-only blocks nothing' );

        $ini->setVariable( 'ContentSecurityPolicy', 'Mode', 'enforce' );
        $this->assertFalse( expOEEditor::inlineScriptsAllowed(), "enforce with script-src 'self'" );
        $this->assertFalse( expOEEditor::hasEngine( 'tinymce3' ) );

        $ini->setVariable( 'ContentSecurityPolicy', 'Directives', array( 'script-src' => "'self' 'unsafe-inline'" ) + $directives );
        $this->assertTrue( expOEEditor::inlineScriptsAllowed(), "enforce with 'unsafe-inline'" );
        $this->assertTrue( expOEEditor::hasEngine( 'tinymce3' ) );
    }
}
