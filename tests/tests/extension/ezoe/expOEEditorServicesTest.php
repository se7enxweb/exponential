<?php
/** The expeditor remote services: engines, get, set, uploadExtensions, config. Live site, the admin user. */

require_once __DIR__ . '/expOETestCase.php';

class expOEEditorServicesTest extends expOETestCase
{
    protected function post( $method, array $post )
    {
        expServiceBase::$trustRequest = true;
        expServiceBase::$postData = $post;
        try
        {
            return expServiceBase::invoke( 'expEditorServices', $method, array() );
        }
        finally
        {
            expServiceBase::$trustRequest = null;
            expServiceBase::$postData = null;
        }
    }

    public function testEveryServiceIsDeclaredAndImplemented()
    {
        $this->assertCount( 5, expEditorServices::$services );
        foreach ( expEditorServices::$services as $method => $d )
        {
            $this->assertTrue( method_exists( 'expEditorServices', $method ), $method );
            $this->assertSame( array( 'ezoe', 'editor' ), $d['access'], $method );
            $this->assertNotSame( '', $d['summary'], $method );
        }
    }

    public function testOnlySetWrites()
    {
        foreach ( expEditorServices::$services as $method => $d )
            $this->assertSame( $method === 'set', $d['write'], $method );
    }

    public function testTheDomainIsRegisteredForEzjscore()
    {
        $this->assertSame( 'expEditorServices', eZINI::instance( 'ezjscore.ini' )->variable( 'ezjscServer_expeditor', 'Class' ) );
    }

    public function testTheCatalogListsTheDomain()
    {
        $r = $this->ok( 'expServicesCatalog', 'catalog', array( 'editor' ) );
        $this->assertSame( 5, $r['meta']['total'] );
    }

    public function testEnginesListsBothBuiltInEngines()
    {
        $r = $this->ok( 'expEditorServices', 'engines' )['data'];
        $ids = array_column( $r['engines'], 'identifier' );
        $this->assertContains( 'tinymce3', $ids );
        $this->assertContains( 'tinymce8', $ids );
        $this->assertSame( 'expOETinyMCE3Engine', $r['engines'][0]['class'] );
    }

    public function testEnginesMarksTheCurrentAndTheDefault()
    {
        $this->ini( 'EditorEngine', 'tinymce3' );
        $this->preference( 'tinymce8' );
        $r = $this->ok( 'expEditorServices', 'engines' )['data'];
        $this->assertSame( 'tinymce8', $r['current'] );
        $this->assertSame( 'tinymce3', $r['default'] );
        $this->assertSame( array( 'tinymce8' ), array_column( array_filter( $r['engines'], function ( $e ) { return $e['current']; } ), 'identifier' ) );
    }

    public function testEnginesReportsTheProblems()
    {
        $this->ini( 'Engines', $this->savedEngines + array( 'missing' => 'expOENoSuchClass' ) );
        $r = $this->ok( 'expEditorServices', 'engines' )['data'];
        $this->assertSame( 'missing', $r['problems'][0]['id'] );
        $this->assertNotContains( 'missing', array_column( $r['engines'], 'identifier' ) );
    }

    public function testEnginesIncludesAThirdEngine()
    {
        $this->ini( 'Engines', $this->savedEngines + array( 'third' => 'expOETestThirdEngine' ) );
        $this->assertContains( 'third', array_column( $this->ok( 'expEditorServices', 'engines' )['data']['engines'], 'identifier' ) );
    }

    public function testGetReturnsThePreferenceAndTheEngineInUse()
    {
        $this->ini( 'EditorEngine', 'tinymce3' );
        $this->preference( 'tinymce8' );
        $g = $this->ok( 'expEditorServices', 'get' )['data'];
        $this->assertSame( 'tinymce8', $g['preference'] );
        $this->assertSame( 'tinymce8', $g['engine'] );
        $this->assertSame( 'tinymce3', $g['default'] );
    }

    public function testGetWithoutAPreferenceUsesTheDefault()
    {
        $this->ini( 'EditorEngine', 'tinymce3' );
        $this->preference( '' );
        $g = $this->ok( 'expEditorServices', 'get' )['data'];
        $this->assertSame( '', $g['preference'] );
        $this->assertSame( 'tinymce3', $g['engine'] );
    }

    public function testGetReportsTheSwitch()
    {
        $this->ini( 'EngineSwitch', 'enabled' );
        $this->assertTrue( $this->ok( 'expEditorServices', 'get' )['data']['switch_enabled'] );
        $this->ini( 'EngineSwitch', 'disabled' );
        $this->assertFalse( $this->ok( 'expEditorServices', 'get' )['data']['switch_enabled'] );
    }

    public function testSetNeedsAPost()
    {
        $this->assertError( $this->call( 'expEditorServices', 'set' ), 403 );
    }

    public function testSetRefusesAnUnknownEngine()
    {
        $this->assertError( $this->post( 'set', array( 'engine' => 'nonsense' ) ), 422 );
    }

    public function testSetStoresTheChoice()
    {
        $r = $this->post( 'set', array( 'engine' => 'tinymce8' ) );
        $this->assertTrue( $r['ok'] );
        $this->assertSame( 'tinymce8', $r['data']['engine'] );
        $this->assertSame( 'tinymce8', $this->dbPreference() );
    }

    public function testSetWithAnEmptyEngineGoesBackToTheDefault()
    {
        $this->post( 'set', array( 'engine' => 'tinymce8' ) );
        $r = $this->post( 'set', array( 'engine' => '' ) );
        $this->assertSame( '', $r['data']['preference'] );
    }

    public function testUploadExtensionsListsTheTypes()
    {
        $r = $this->ok( 'expEditorServices', 'uploadExtensions' )['data'];
        $this->assertContains( 'png', $r['extensions'] );
        $this->assertContains( 'pdf', $r['extensions'] );
        $this->assertNotContains( 'php', $r['extensions'] );
    }

    public function testUploadExtensionsSaysWhetherTheCheckIsEnforced()
    {
        $this->ini( 'UploadExtensionCheck', 'always' );
        $this->assertTrue( $this->ok( 'expEditorServices', 'uploadExtensions' )['data']['enforced'] );
        $this->ini( 'UploadExtensionCheck', 'disabled' );
        $this->assertFalse( $this->ok( 'expEditorServices', 'uploadExtensions' )['data']['enforced'] );
        $this->ini( 'UploadExtensionCheck', 'engine' );
        $this->preference( 'tinymce3' );
        $this->assertFalse( $this->ok( 'expEditorServices', 'uploadExtensions' )['data']['enforced'] );
        $this->preference( 'tinymce8' );
        $this->assertTrue( $this->ok( 'expEditorServices', 'uploadExtensions' )['data']['enforced'] );
    }

    public function testConfigDescribesTheEngineInUse()
    {
        $this->preference( 'tinymce8' );
        $c = $this->ok( 'expEditorServices', 'config' )['data'];
        $this->assertSame( 'tinymce8', $c['engine'] );
        $this->assertSame( 'ezlink', $c['toolbar_map']['link'] );
        $this->assertArrayHasKey( 'ezembed', $c['plugins'] );
        $this->assertSame( 'gpl', $c['config']['license_key'] );
        $this->assertNotEmpty( $c['buttons'] );
    }

    public function testConfigOfTinyMCE3HasNoPlugins()
    {
        $this->preference( 'tinymce3' );
        $c = $this->ok( 'expEditorServices', 'config' )['data'];
        $this->assertSame( 'tinymce3', $c['engine'] );
        $this->assertSame( array(), $c['plugins'] );
    }

    public function testConfigTakesAnotherLayout()
    {
        $c = $this->ok( 'expEditorServices', 'config', array( 'mini' ) )['data'];
        $this->assertNotEmpty( $c['buttons'] );
    }

    public function testAnonymousIsRefused()
    {
        $this->loginAnonymous();
        foreach ( array( 'engines', 'get', 'uploadExtensions', 'config' ) as $method )
            $this->assertContains( $this->call( 'expEditorServices', $method )['error']['code'], array( 401, 403 ), $method );
    }
}
