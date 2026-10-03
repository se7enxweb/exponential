<?php
require_once __DIR__ . '/../media/expMediaTestCase.php';

/** expdesign: designs, bases, overrides and templates (read only). */
class expDesignServicesTest extends expMediaTestCase
{
    public function testEveryServiceIsDeclared()
    {
        $this->assertDeclared( 'expDesignServices', 13 );
        foreach ( expDesignServices::$services as $m => $d )
            $this->assertFalse( $d['write'] );
    }

    public function testDesignsListsAdminAndStandard()
    {
        $names = array_column( $this->ok( 'expDesignServices', 'designs' )['data'], 'name' );
        $this->assertContains( 'standard', $names );
        $this->assertContains( 'admin', $names );
    }

    public function testCurrentDesign()
    {
        $c = $this->ok( 'expDesignServices', 'current' )['data'];
        $this->assertSame( 'standard', $c['standard'] );
        $this->assertNotSame( '', $c['site'] );
    }

    public function testBasesEndWithStandard()
    {
        $bases = $this->ok( 'expDesignServices', 'bases' )['data'];
        $this->assertContains( 'design/standard', $bases );
        $this->assertNotEmpty( $this->ok( 'expDesignServices', 'extensions' )['data'] );
    }

    public function testOverridesArePagedAndCounted()
    {
        $r = $this->ok( 'expDesignServices', 'overrides', array( 5, 0 ) );
        $this->assertPaged( $r );
        $this->assertSame( $r['meta']['total'], $this->ok( 'expDesignServices', 'overridecount' )['data']['count'] );
        $this->assertArrayHasKey( 'source', $r['data'][0] );
    }

    public function testOverrideByNameAndForSource()
    {
        $first = $this->ok( 'expDesignServices', 'overrides', array( 1, 0 ) )['data'][0];
        $this->assertSame( $first['name'], $this->ok( 'expDesignServices', 'override', array( $first['name'] ) )['data']['name'] );
        $for = $this->ok( 'expDesignServices', 'overridesfor', array( $first['source'] ) )['data'];
        $this->assertContains( $first['name'], array_column( $for, 'name' ) );
        $this->fails( 404, 'expDesignServices', 'override', array( 'no_such_override_' . uniqid() ) );
    }

    public function testOverridesFilteredBySource()
    {
        foreach ( $this->ok( 'expDesignServices', 'overrides', array( 50, 0, 'node/view/full.tpl' ) )['data'] as $o )
            $this->assertSame( 'node/view/full.tpl', $o['source'] );
    }

    public function testTemplatesAreSortedAndPrefixed()
    {
        $r = $this->ok( 'expDesignServices', 'templates', array( 10, 0, 'node/view' ) );
        $this->assertPaged( $r );
        foreach ( $r['data'] as $t )
            $this->assertStringStartsWith( 'node/view', $t['template'] );
        $names = array_column( $r['data'], 'template' );
        $sorted = $names;
        sort( $sorted );
        $this->assertSame( $sorted, $names );
    }

    public function testResolveATemplate()
    {
        $t = $this->ok( 'expDesignServices', 'resolve', array( 'node/view/full.tpl' ) )['data'];
        $this->assertNotNull( $t['base_dir'] );
        $this->assertIsArray( $t['overrides'] );
        $this->fails( 404, 'expDesignServices', 'resolve', array( 'no/such/template.tpl' ) );
        $this->assertGreaterThan( 100, $this->ok( 'expDesignServices', 'templatecount' )['data']['count'] );
    }

    public function testSourceOfATemplateInsideADesign()
    {
        $s = $this->ok( 'expDesignServices', 'source', array( 'design/standard/templates/node/view/full.tpl' ) )['data'];
        $this->assertGreaterThan( 0, $s['size'] );
        $this->assertSame( $s['size'], strlen( $s['content'] ) );
    }

    public function testSourceRefusesPathsOutsideTheDesigns()
    {
        foreach ( array( 'settings/site.ini', 'design/standard/templates/../../../../settings/site.ini', '/etc/passwd', 'design/standard/templates/x.php', 'config.php' ) as $path )
            $this->fails( 400, 'expDesignServices', 'source', array( $path ) );
        $this->fails( 404, 'expDesignServices', 'source', array( 'design/standard/templates/no/such.tpl' ) );
    }

    public function testCacheState()
    {
        $this->assertArrayHasKey( 'template_compile', $this->ok( 'expDesignServices', 'cachestate' )['data'] );
    }

    public function testNeedsSetupAdministrate()
    {
        $this->loginAnonymous();
        $this->assertFalse( $this->call( 'expDesignServices', 'designs' )['ok'] );
        $this->assertFalse( $this->call( 'expDesignServices', 'source', array( 'design/standard/templates/node/view/full.tpl' ) )['ok'] );
    }
}
