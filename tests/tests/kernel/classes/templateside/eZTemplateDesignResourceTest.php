<?php
/**
 * The design resource of the template engine, the part that needs no request: design settings and their override
 * by the site basics, finding a file in a list of design bases, the design start path, override keys (set, merge,
 * remove, global keys), global overrides, the design extensions, the list of design bases and the override array
 * built from the designs of this checkout, and the source template of a resolved override file.
 *
 * No database. The globals the resource keeps its state in are saved and put back.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group eztemplate
 */

require_once dirname( __DIR__ ) . '/datatypes/eZDatatypeTestFixtures.php';

class eZTemplateDesignResourceTest extends eZDatatypeTestCase
{
    const GLOBALS = array( 'eZTemplateDesignSetting', 'eZSiteBasics', 'eZTemplateDesignResourceStartPath', 'eZDesignKeys',
                           'eZDesignOverrides', 'eZTemplateDesignResourceInstance' );

    private $saved = array();
    private $dir;

    protected function setUp(): void
    {
        parent::setUp();
        foreach ( self::GLOBALS as $name )
            $this->saved[$name] = array_key_exists( $name, $GLOBALS ) ? array( $GLOBALS[$name] ) : null;
        $this->dir = 'var/tmp/phpunit-k1d-design-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( $this->dir . '/one/images', 0777, true );
        mkdir( $this->dir . '/two/images', 0777, true );
        file_put_contents( $this->dir . '/two/images/logo.png', 'x' );
        file_put_contents( $this->dir . '/one/style.css', 'x' );
        file_put_contents( $this->dir . '/two/style.css', 'x' );
    }

    protected function tearDown(): void
    {
        foreach ( $this->saved as $name => $value )
        {
            if ( $value === null )
                unset( $GLOBALS[$name] );
            else
                $GLOBALS[$name] = $value[0];
        }
        foreach ( array( 'two/images/logo.png', 'one/style.css', 'two/style.css' ) as $file )
            unlink( $this->dir . '/' . $file );
        foreach ( array( 'one/images', 'two/images', 'one', 'two', '' ) as $dir )
            rmdir( $this->dir . '/' . $dir );
        parent::tearDown();
    }

    public function testFileMatchTakesTheFirstBaseThatHasTheFile()
    {
        $bases = array( $this->dir . '/one', $this->dir . '/two', $this->dir . '/one' );
        $tried = array();
        $this->assertSame( array( 'resource' => $this->dir . '/one', 'path' => $this->dir . '/one/style.css' ),
                           eZTemplateDesignResource::fileMatch( $bases, false, 'style.css', $tried ) );
        $this->assertSame( array( $this->dir . '/one/style.css' ), $tried );

        $tried = array();
        $this->assertSame( array( 'resource' => $this->dir . '/two/images', 'path' => $this->dir . '/two/images/logo.png' ),
                           eZTemplateDesignResource::fileMatch( $bases, 'images', 'logo.png', $tried ) );
        $this->assertSame( array( $this->dir . '/one/images/logo.png', $this->dir . '/two/images/logo.png' ), $tried );

        $tried = array();
        $this->assertFalse( eZTemplateDesignResource::fileMatch( $bases, 'images', 'none.png', $tried ) );
        $this->assertCount( 2, $tried, 'each base is tried once' );
    }

    public function testDesignSettings()
    {
        unset( $GLOBALS['eZTemplateDesignSetting'], $GLOBALS['eZSiteBasics'] );
        $ini = eZINI::instance();
        $this->assertSame( $ini->variable( 'DesignSettings', 'StandardDesign' ), eZTemplateDesignResource::designSetting() );
        $this->assertSame( $ini->variable( 'DesignSettings', 'SiteDesign' ), eZTemplateDesignResource::designSetting( 'site' ) );
        $this->assertNull( @eZTemplateDesignResource::designSetting( 'other' ) );

        $resource = new eZTemplateDesignResource();
        $resource->setDesignSetting( 'k1dsite', 'site' );
        $resource->setDesignSetting( 'k1dstandard' );
        @$resource->setDesignSetting( 'x', 'other' );
        $this->assertSame( 'k1dsite', eZTemplateDesignResource::designSetting( 'site' ) );
        $this->assertSame( 'k1dstandard', eZTemplateDesignResource::designSetting( 'standard' ) );
        $this->assertArrayNotHasKey( 'other', $GLOBALS['eZTemplateDesignSetting'] );

        $GLOBALS['eZSiteBasics']['site-design-override'] = 'k1doverride';
        $this->assertSame( 'k1doverride', eZTemplateDesignResource::designSetting( 'site' ) );
        $this->assertSame( 'k1dstandard', eZTemplateDesignResource::designSetting( 'standard' ) );
    }

    public function testDesignStartPath()
    {
        eZTemplateDesignResource::setDesignStartPath( false );
        $this->assertSame( 'design', eZTemplateDesignResource::designStartPath() );
        eZTemplateDesignResource::setDesignStartPath( 'k1d/design' );
        $this->assertSame( 'k1d/design', eZTemplateDesignResource::designStartPath() );
    }

    public function testKeys()
    {
        unset( $GLOBALS['eZDesignKeys'] );
        $resource = new eZTemplateDesignResource();
        $resource->clearKeys();
        $this->assertSame( array(), $resource->keys() );
        $resource->setKeys( array( array( 'class', 2 ), array( 'node', 5 ), array( 'ignored' ) ) );
        $this->assertSame( array( 'class' => 2, 'node' => 5 ), $resource->keys() );
        $resource->setKeys( array( array( 'node', 6 ) ) );
        $this->assertSame( array( 'class' => 2, 'node' => 6 ), $resource->keys() );
        $resource->removeKey( 'class' );
        $resource->removeKey( 'nothing' );
        $this->assertSame( array( 'node' => 6 ), $resource->keys() );
        $GLOBALS['eZDesignKeys'] = array( 'section' => 1, 'node' => 7 );
        $this->assertSame( array( 'node' => 7, 'section' => 1 ), $resource->keys() );
        $resource->clearKeys();
        $this->assertSame( array( 'section' => 1, 'node' => 7 ), $resource->keys() );

        $keys = array( 'a' => 1 );
        $resource->mergeKeys( $keys, array( array( 'b', 2 ), array( 'a', 3 ) ) );
        $this->assertSame( array( 'a' => 3, 'b' => 2 ), $keys );
    }

    public function testGlobalOverridesAndInstance()
    {
        unset( $GLOBALS['eZDesignOverrides'], $GLOBALS['eZTemplateDesignResourceInstance'] );
        eZTemplateDesignResource::addGlobalOverride( 'k1d', 'node/view/full.tpl', 'k1d/full.tpl', 'templates', array( 'class' => 4 ) );
        $this->assertSame( array( 'k1d' => array( 'Source' => 'node/view/full.tpl', 'MatchFile' => 'k1d/full.tpl', 'Subdir' => 'templates', 'Match' => array( 'class' => 4 ) ) ),
                           $GLOBALS['eZDesignOverrides'] );
        $instance = eZTemplateDesignResource::instance();
        $this->assertSame( $instance, eZTemplateDesignResource::instance() );
        $this->assertSame( 'design', $instance->resourceName() );
        $instance->setOverrideAccess( 'k1dsite' );
        $this->assertSame( 'k1dsite', $instance->OverrideSiteAccess );
    }

    public function testDesignExtensionsAreTheSettingReversed()
    {
        $extensions = eZINI::instance( 'design.ini' )->variable( 'ExtensionSettings', 'DesignExtensions' );
        $this->assertSame( array_reverse( $extensions ), eZTemplateDesignResource::designExtensions() );
    }

    public function testAllDesignBasesStartWithTheSiteDesignAndEndWithStandard()
    {
        $bases = eZTemplateDesignResource::allDesignBases();
        $this->assertNotEmpty( $bases );
        $this->assertContains( 'design/standard', $bases );
        foreach ( $bases as $base )
            $this->assertStringContainsString( 'design/', $base );
        $this->assertSame( $bases, eZTemplateDesignResource::allDesignBases(), 'kept in memory' );
    }

    public function testOverrideArrayKnowsTheStandardTemplates()
    {
        eZTemplateDesignResource::clearInMemoryOverrideArray();
        $array = eZTemplateDesignResource::overrideArray();
        $this->assertArrayHasKey( '/pagelayout.tpl', $array );
        $this->assertSame( '/pagelayout.tpl', $array['/pagelayout.tpl']['template'] );
        $this->assertFileExists( $array['/pagelayout.tpl']['base_dir'] . $array['/pagelayout.tpl']['template'] );
        $this->assertSame( $array, eZTemplateDesignResource::overrideArray(), 'kept in memory' );
    }

    public function testSourceForMatchFile()
    {
        eZTemplateDesignResource::clearInMemoryOverrideArray();
        $this->assertFalse( eZTemplateDesignResource::sourceForMatchFile( 'design/k1d/templates/nothing/at/all.tpl' ) );
    }

    public function testClearInMemoryCache()
    {
        $GLOBALS['eZTemplateDesignSetting'] = array( 'site' => 'k1d' );
        eZTemplateDesignResource::clearInMemoryCache();
        $this->assertSame( array(), $GLOBALS['eZTemplateDesignSetting'] );
        $this->assertArrayNotHasKey( 'eZOverrideTemplateCacheMap', $GLOBALS );
    }
}
