<?php
/** The page /ezoe/engine: the select over the registry, saving the choice, who may use it. */

require_once __DIR__ . '/expOETestCase.php';

class expOEEngineViewTest extends expOETestCase
{
    protected function page( array $post = array(), $user = 'admin' )
    {
        return $this->runView( 'engine', array(), $post, $user );
    }

    public function testThePageRendersASelectWithEveryEngine()
    {
        $html = $this->page();
        $this->assertStringContainsString( '<select id="ezoe-engine" name="Engine">', $html );
        $this->assertStringContainsString( 'value="tinymce3"', $html );
        $this->assertStringContainsString( 'value="tinymce8"', $html );
    }

    public function testThePageOffersTheSiteDefault()
    {
        $this->assertMatchesRegularExpression( '#<option value=""[^>]*>Default of this site \(TinyMCE [38]\)</option>#', $this->page() );
    }

    public function testThePagePostsToItself()
    {
        $this->assertStringContainsString( 'method="post" action="/ezoe/engine"', $this->page() );
    }

    public function testThePageStartsWithoutAnyText()
    {
        $this->assertStringStartsNotWith( '<?php', ltrim( $this->page() ) );
    }

    public function testSavingAnEngineStoresThePreference()
    {
        $html = $this->page( array( 'SaveEngineButton' => 'Save', 'Engine' => 'tinymce3' ) );
        $this->assertStringContainsString( 'The editor of your user was saved.', $html );
        $this->assertSame( 'tinymce3', $this->storedPreference() );
    }

    public function testSavingTinyMCE8StoresThePreference()
    {
        $this->page( array( 'SaveEngineButton' => 'Save', 'Engine' => 'tinymce8' ) );
        $this->assertSame( 'tinymce8', $this->storedPreference() );
    }

    public function testTheSavedEngineIsSelectedOnTheNextVisit()
    {
        $this->page( array( 'SaveEngineButton' => 'Save', 'Engine' => 'tinymce3' ) );
        $this->assertMatchesRegularExpression( '#<option value="tinymce3" selected="selected">#', $this->page() );
    }

    public function testAnEmptyChoiceGoesBackToTheSiteDefault()
    {
        $this->page( array( 'SaveEngineButton' => 'Save', 'Engine' => 'tinymce3' ) );
        $html = $this->page( array( 'SaveEngineButton' => 'Save', 'Engine' => '' ) );
        $this->assertStringContainsString( 'The editor of your user was saved.', $html );
        $this->assertSame( '', $this->storedPreference() );
        $this->assertMatchesRegularExpression( '#<option value="" selected="selected">#', $this->page() );
    }

    public function testAnUnknownEngineIsRefusedAndNothingIsStored()
    {
        $this->page( array( 'SaveEngineButton' => 'Save', 'Engine' => 'tinymce3' ) );
        $html = $this->page( array( 'SaveEngineButton' => 'Save', 'Engine' => 'nonsense' ) );
        $this->assertStringContainsString( 'This editor is not available.', $html );
        $this->assertSame( 'tinymce3', $this->storedPreference() );
    }

    public function testNothingIsSavedWithoutThePostButton()
    {
        $this->page( array( 'SaveEngineButton' => 'Save', 'Engine' => 'tinymce3' ) );
        $this->page( array( 'Engine' => 'tinymce8' ) );
        $this->assertSame( 'tinymce3', $this->storedPreference() );
    }

    public function testTheEnginesAreEscapedInTheMarkup()
    {
        $this->assertStringNotContainsString( '<script', $this->page() );
    }

    public function testTheViewNeedsTheEditorPolicy()
    {
        $ViewList = array();
        $FunctionList = array();
        $Module = array();
        ob_start();
        include dirname( __DIR__, 4 ) . '/extension/ezoe/modules/ezoe/module.php';
        ob_end_clean();
        $this->assertSame( array( 'editor' ), $ViewList['engine']['functions'] );
        $this->assertArrayHasKey( 'editor', $FunctionList );
    }

    public function testAnonymousHasNoEditorPolicy()
    {
        $this->loginAnonymous();
        $this->assertSame( 'no', eZUser::currentUser()->hasAccessTo( 'ezoe', 'editor' )['accessWord'] );
    }

    public function testTheAdminHasTheEditorPolicy()
    {
        $this->assertNotSame( 'no', eZUser::currentUser()->hasAccessTo( 'ezoe', 'editor' )['accessWord'] );
    }

    public function testTheFormIsAPostSoTheFormTokenApplies()
    {
        $source = file_get_contents( dirname( __DIR__, 4 ) . '/extension/ezoe/design/standard/templates/ezoe/engine.tpl' );
        $this->assertStringContainsString( '<form method="post"', $source );
        $this->assertStringNotContainsString( 'method="get"', $source );
    }

    public function testTheViewSavesOnlyThroughTheRegistry()
    {
        $source = file_get_contents( dirname( __DIR__, 4 ) . '/extension/ezoe/modules/ezoe/engine.php' );
        $this->assertStringContainsString( 'expOEEditor::setUserEngine', $source );
        $this->assertStringNotContainsString( 'eZPreferences::setValue', $source );
    }

    protected function storedPreference()
    {
        $admin = eZUser::fetchByName( 'admin' );
        $rows = eZDB::instance()->arrayQuery( "SELECT value, " . mt_rand() . " AS nonce FROM ezpreferences WHERE user_id = " . (int) $admin->attribute( 'contentobject_id' ) . " AND name = 'ezoe_engine'" );
        return $rows ? $rows[0]['value'] : '';
    }
}
