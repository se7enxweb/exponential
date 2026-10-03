<?php
/** The engine registry: what is registered, which engine a user gets, what happens with broken entries. */

require_once __DIR__ . '/expOETestCase.php';

class expOEEditorRegistryTest extends expOETestCase
{
    public function testTheShippedIniRegistersTheTwoEngines()
    {
        $registered = expOEEditor::registered();
        $this->assertSame( 'expOETinyMCE3Engine', $registered['tinymce3'] );
        $this->assertSame( 'expOETinyMCE8Engine', $registered['tinymce8'] );
    }

    public function testBothEnginesAreInstantiatedAndAvailable()
    {
        $engines = expOEEditor::engines();
        $this->assertInstanceOf( 'expOEEditorEngine', $engines['tinymce3'] );
        $this->assertInstanceOf( 'expOEEditorEngine', $engines['tinymce8'] );
        $this->assertSame( array(), expOEEditor::problems() );
    }

    public function testEnginesKnowTheirIdentifier()
    {
        foreach ( expOEEditor::engines() as $id => $engine )
            $this->assertSame( $id, $engine->identifier() );
    }

    public function testLabelsAreTranslatedNames()
    {
        $labels = expOEEditor::labels();
        $this->assertSame( 'TinyMCE 3', $labels['tinymce3'] );
        $this->assertSame( 'TinyMCE 8', $labels['tinymce8'] );
    }

    public function testHasEngine()
    {
        $this->assertTrue( expOEEditor::hasEngine( 'tinymce3' ) );
        $this->assertFalse( expOEEditor::hasEngine( 'nonsense' ) );
        $this->assertFalse( expOEEditor::hasEngine( null ) );
        $this->assertFalse( expOEEditor::hasEngine( '' ) );
        $this->assertFalse( expOEEditor::hasEngine( array( 'tinymce3' ) ) );
    }

    public function testAnotherEngineIsOneClassAndOneLine()
    {
        $this->ini( 'Engines', $this->savedEngines + array( 'third' => 'expOETestThirdEngine' ) );
        $this->assertArrayHasKey( 'third', expOEEditor::engines() );
        $this->assertSame( 'third', expOEEditor::resolve( 'third' ) );
        $this->assertSame( 'Third editor', expOEEditor::labels()['third'] );
    }

    public function testAThirdEngineGetsItsTemplateAndConfig()
    {
        $this->ini( 'Engines', $this->savedEngines + array( 'third' => 'expOETestThirdEngine' ) );
        $info = expOEEditor::info( 'third', array( 'attribute_id' => 77 ) );
        $this->assertSame( 'design:content/datatype/edit/ezxmltext_third.tpl', $info['template'] );
        $this->assertSame( array( 'attribute' => 77 ), $info['config'] );
    }

    public function testASwitchChoiceIsEveryOtherEngine()
    {
        $this->ini( 'Engines', $this->savedEngines + array( 'third' => 'expOETestThirdEngine' ) );
        $ids = array_column( expOEEditor::switchChoices( 'tinymce3' ), 'identifier' );
        $this->assertSame( array( 'tinymce8', 'third' ), $ids );
        $this->assertNotContains( 'tinymce3', $ids );
    }

    public function testUserPreferenceWinsOverTheSite()
    {
        $this->ini( 'EditorEngine', 'tinymce8' );
        $this->assertSame( 'tinymce3', expOEEditor::resolve( 'tinymce3' ) );
        $this->ini( 'EditorEngine', 'tinymce3' );
        $this->assertSame( 'tinymce8', expOEEditor::resolve( 'tinymce8' ) );
    }

    public function testNoPreferenceTakesTheConfiguredEngine()
    {
        $this->ini( 'EditorEngine', 'tinymce8' );
        $this->assertSame( 'tinymce8', expOEEditor::resolve( '' ) );
        $this->ini( 'EditorEngine', 'tinymce3' );
        $this->assertSame( 'tinymce3', expOEEditor::resolve( '' ) );
    }

    public function testThePreferenceIsReadForTheCurrentUser()
    {
        $this->ini( 'EditorEngine', 'tinymce3' );
        $this->preference( 'tinymce8' );
        $this->assertSame( 'tinymce8', expOEEditor::resolve() );
        $this->preference( '' );
        $this->assertSame( 'tinymce3', expOEEditor::resolve() );
    }

    public function testAnUnknownPreferenceFallsBackToTheSite()
    {
        $this->ini( 'EditorEngine', 'tinymce8' );
        $this->assertSame( 'tinymce8', expOEEditor::resolve( 'doesnotexist' ) );
        $this->ini( 'EditorEngine', 'tinymce3' );
        $this->assertSame( 'tinymce3', expOEEditor::resolve( 'doesnotexist' ) );
    }

    public function testAnUnknownSiteEngineFallsBackToTinyMCE3()
    {
        $this->ini( 'EditorEngine', 'nonsense' );
        $this->assertSame( 'tinymce3', expOEEditor::configuredEngine() );
        $this->assertSame( 'tinymce3', expOEEditor::resolve( 'nonsense' ) );
    }

    public function testInfoOfAnUnknownEngineIsTheDefault()
    {
        $this->assertSame( 'tinymce3', expOEEditor::info( 'nonsense' )['identifier'] );
    }

    public function testAMissingClassIsAProblemNotAFatal()
    {
        $this->ini( 'Engines', $this->savedEngines + array( 'missing' => 'expOENoSuchClass' ) );
        $this->assertArrayNotHasKey( 'missing', expOEEditor::engines() );
        $this->assertStringContainsString( 'does not exist', $this->why( 'missing' ) );
        $this->assertArrayHasKey( 'tinymce3', expOEEditor::engines() );
    }

    public function testAClassThatIsNoEngineIsAProblem()
    {
        $this->ini( 'Engines', $this->savedEngines + array( 'plain' => 'expOETestNotAnEngine' ) );
        $this->assertArrayNotHasKey( 'plain', expOEEditor::engines() );
        $this->assertStringContainsString( 'does not implement', $this->why( 'plain' ) );
    }

    public function testAnEngineThatNamesAnotherIdentifierIsAProblem()
    {
        $this->ini( 'Engines', $this->savedEngines + array( 'liar' => 'expOETestWrongIdEngine' ) );
        $this->assertArrayNotHasKey( 'liar', expOEEditor::engines() );
        $this->assertStringContainsString( 'identifier', $this->why( 'liar' ) );
    }

    public function testAnUnavailableEngineIsHiddenAndReported()
    {
        $this->ini( 'Engines', $this->savedEngines + array( 'gone' => 'expOETestUnavailableEngine' ) );
        $this->assertArrayNotHasKey( 'gone', expOEEditor::engines() );
        $this->assertStringContainsString( 'not available', $this->why( 'gone' ) );
        $this->assertSame( expOEEditor::configuredEngine(), expOEEditor::resolve( 'gone' ) );
    }

    public function testAnEngineThatThrowsIsAProblem()
    {
        $this->ini( 'Engines', $this->savedEngines + array( 'throws' => 'expOETestThrowingEngine' ) );
        $this->assertArrayNotHasKey( 'throws', expOEEditor::engines() );
        $this->assertStringContainsString( 'cannot start', $this->why( 'throws' ) );
    }

    public function testABadIdentifierIsAProblem()
    {
        $this->ini( 'Engines', $this->savedEngines + array( 'Bad Id' => 'expOETestThirdEngine' ) );
        $this->assertArrayNotHasKey( 'Bad Id', expOEEditor::engines() );
        $this->assertStringContainsString( 'identifier', $this->why( 'Bad Id' ) );
    }

    public function testTheBuiltInEngineSurvivesAnOverrideThatLeavesItOut()
    {
        $this->ini( 'Engines', array( 'tinymce8' => 'expOETinyMCE8Engine' ) );
        $this->assertArrayHasKey( 'tinymce3', expOEEditor::engines() );
        $this->assertSame( 'expOETinyMCE3Engine', expOEEditor::registered()['tinymce3'] );
    }

    public function testAnEmptyRegistryStillHasTheDefault()
    {
        $this->ini( 'Engines', array() );
        $this->assertSame( array( 'tinymce3' ), array_keys( expOEEditor::engines() ) );
    }

    public function testSwitchEnabledFollowsTheIni()
    {
        $this->ini( 'EngineSwitch', 'enabled' );
        $this->assertTrue( expOEEditor::switchEnabled() );
        $this->ini( 'EngineSwitch', 'disabled' );
        $this->assertFalse( expOEEditor::switchEnabled() );
    }

    public function testSettingAnUnknownEngineIsRefusedAndStoresNothing()
    {
        $this->preference( 'tinymce3' );
        $this->assertFalse( expOEEditor::setUserEngine( 'nonsense' ) );
        $this->assertSame( 'tinymce3', eZPreferences::value( expOEEditor::PREFERENCE ) );
    }

    public function testSettingAKnownEngineStoresThePreference()
    {
        $this->assertTrue( expOEEditor::setUserEngine( 'tinymce8' ) );
        $this->assertSame( 'tinymce8', eZPreferences::value( expOEEditor::PREFERENCE ) );
        $this->assertTrue( expOEEditor::setUserEngine( '' ) );
        $this->assertSame( '', eZPreferences::value( expOEEditor::PREFERENCE ) );
    }

    public function testTheAuditBranchNamesTheEvent()
    {
        $events = ( new expOEAuditBranch() )->events();
        $this->assertArrayHasKey( 'content.ezoe.engine.change', $events );
        $this->assertSame( 'content', $events['content.ezoe.engine.change']['channel'] );
    }

    public function testTheAuditBranchIsRegistered()
    {
        $ini = eZINI::instance( 'audit.ini' );
        $branches = $ini->hasVariable( 'AuditEventSettings', 'Branches' ) ? $ini->variable( 'AuditEventSettings', 'Branches' ) : array();
        $this->assertSame( 'expOEAuditBranch', isset( $branches['ezoe'] ) ? $branches['ezoe'] : null );
    }

    public function testTheRegistryIsCountedBySetupRad()
    {
        if ( !class_exists( 'expRADSurvey' ) )
            $this->markTestSkipped( 'The survey is not loaded' );
        $descriptors = expRADSurvey::registryDescriptors();
        $this->assertArrayHasKey( 'ezoeengines', $descriptors );
        $this->assertSame( 'ezoe.ini', $descriptors['ezoeengines']['ini'] );
        $this->assertSame( 'expOEEditorEngine', $descriptors['ezoeengines']['variables']['Engines'] );
    }

    public function testSetupRadListsBothEngines()
    {
        if ( !class_exists( 'expRADSurvey' ) )
            $this->markTestSkipped( 'The survey is not loaded' );
        $registries = expRADSurvey::registries();
        $this->assertArrayHasKey( 'ezoeengines', $registries );
        $names = array_column( $registries['ezoeengines']['entries'], 'name' );
        $this->assertContains( 'tinymce3', $names );
        $this->assertContains( 'tinymce8', $names );
        $this->assertSame( array(), $registries['ezoeengines']['broken'] );
    }

    protected function why( $id )
    {
        foreach ( expOEEditor::problems() as $p )
        {
            if ( $p['id'] === $id )
                return $p['why'];
        }
        $this->fail( "no problem reported for $id" );
    }
}
