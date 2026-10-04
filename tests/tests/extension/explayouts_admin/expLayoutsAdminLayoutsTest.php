<?php
/**
 * Admin layouts (admin4l): resolver, rules, separation from the site layouts, cache keys, block definitions.
 *
 *  AL-01 - the layout types admin_3col, admin_2col, admin_full are admin types; site types are not
 *  AL-02 - module/view patterns (moduleViewMatches) and the module targets, which never match without a request
 *  AL-03 - which siteaccesses the admin layouts apply to ([AdminLayoutSettings] SiteAccessMatch)
 *  AL-04 - resolveAdmin: edit views -> admin_full, content, setup, user, shop -> admin_3col, unknown module -> the
 *          default layout; false when the safety switch is off, on a site siteaccess, without a request
 *  AL-05 - separation: the site resolver never returns an admin layout, the admin one never a site layout
 *  AL-06 - cache key: five strings, differs by module, view, siteaccess, follows the generation
 *  AL-07 - block definitions: every admin_* block has Group=admin, a handler, a view template, is in
 *          [AdminBlockSettings] and not in the site's [BlockSettings]
 *
 * Live database and settings, as the kernel runs (admin siteaccess); the test changes nothing but the resolver's own
 * cache generation (clearCache()), and puts every setting and global it changes back.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group explayouts_admin
 */

class expLayoutsAdminLayoutsTest extends PHPUnit\Framework\TestCase
{
    protected static $bootError = null;
    protected static $script = null;
    protected $savedGlobals = array();

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        try
        {
            $root = dirname( __DIR__, 4 );
            chdir( $root );
            if ( !class_exists( 'eZScript' ) )
                require_once $root . '/autoload.php';
            $script = eZScript::instance( array( 'use-session' => false, 'use-modules' => true, 'use-extensions' => true ) );
            $script->startup();
            $script->setUseSiteAccess( 'admin' );
            $script->initialize();
            eZExecution::setCleanExit();
            if ( !eZDB::instance()->isConnected() )
                throw new RuntimeException( 'no database connection' );
            self::$script = $script;
        }
        catch ( Throwable $e )
        {
            self::$bootError = $e->getMessage();
        }
    }

    protected function setUp(): void
    {
        if ( self::$bootError !== null )
            $this->markTestSkipped( 'kernel did not boot: ' . self::$bootError );
        $this->savedGlobals = array( 'eZCurrentAccess' => isset( $GLOBALS['eZCurrentAccess'] ) ? $GLOBALS['eZCurrentAccess'] : null,
                                     'eZRequestedModuleParams' => isset( $GLOBALS['eZRequestedModuleParams'] ) ? $GLOBALS['eZRequestedModuleParams'] : null );
        $GLOBALS['eZCurrentAccess']['name'] = 'admin';
        $this->setEnabled( 'enabled' );
        expLayoutsResolver::clearCache();
    }

    protected function tearDown(): void
    {
        foreach ( $this->savedGlobals as $name => $value )
        {
            if ( $value === null )
                unset( $GLOBALS[$name] );
            else
                $GLOBALS[$name] = $value;
        }
        eZINI::resetInstance( 'explayouts.ini' );
        if ( self::$bootError === null )
            expLayoutsResolver::clearCache();
    }

    protected function setEnabled( $value )
    {
        eZINI::instance( 'explayouts.ini' )->setVariable( 'AdminLayoutSettings', 'Enabled', $value );
    }

    protected function request( $module, $view )
    {
        $GLOBALS['eZRequestedModuleParams'] = array( 'module_name' => $module, 'function_name' => $view );
    }

    protected function typeOf( $layout )
    {
        return $layout ? $layout->attribute( 'layout_type' ) : false;
    }

    // AL-01

    public function testTheAdminLayoutTypesAreAdminTypesAndTheSiteTypesAreNot()
    {
        foreach ( array( 'admin_3col', 'admin_2col', 'admin_full' ) as $type )
        {
            $this->assertTrue( expLayoutsLayoutType::isAdminType( $type ), $type );
            $this->assertSame( 'admin', expLayoutsLayoutType::getGroup( $type ) );
        }
        $this->assertSame( array( 'header', 'topmenu', 'left', 'right', 'main_top', 'main', 'main_bottom', 'footer' ), expLayoutsLayoutType::getZones( 'admin_3col' ) );
        $this->assertSame( array( 'header', 'topmenu', 'left', 'main_top', 'main', 'main_bottom', 'footer' ), expLayoutsLayoutType::getZones( 'admin_2col' ) );
        $this->assertNotContains( 'left', expLayoutsLayoutType::getZones( 'admin_full' ) );
        $this->assertNotContains( 'right', expLayoutsLayoutType::getZones( 'admin_full' ) );
        $this->assertContains( 'main', expLayoutsLayoutType::getZones( 'admin_full' ) );
        foreach ( array_keys( expLayoutsLayoutType::getAvailableTypes() ) as $type )
            $this->assertFalse( expLayoutsLayoutType::isAdminType( $type ), "site list holds $type" );
        $this->assertFalse( expLayoutsLayoutType::isAdminType( 'no_such_type' ) );
    }

    // AL-02

    public function testModuleViewPatterns()
    {
        $cases = array( array( '*', 'content', 'view', true ), array( 'content/*', 'content', 'edit', true ), array( 'content/view', 'content', 'view', true ),
                        array( 'content/view', 'content', 'edit', false ), array( 'content', 'content', 'anything', true ), array( 'content', 'setup', 'view', false ),
                        array( '*/view', 'user', 'view', true ), array( '*/view', 'user', 'edit', false ), array( ' setup ', 'setup', 'rad', true ), array( '', 'setup', 'rad', false ) );
        foreach ( $cases as $c )
            $this->assertSame( $c[3], expLayoutsResolver::moduleViewMatches( $c[0], $c[1], $c[2] ), "'{$c[0]}' vs {$c[1]}/{$c[2]}" );
    }

    public function testModuleTargetsNeedARequestContextAndOtherTargetsNeverMatchOne()
    {
        $module = new expLayoutsRuleTarget();
        $module->setAttribute( 'target_type', 'module_view' );
        $module->setAttribute( 'target_value', 'content/view' );
        $this->assertFalse( expLayoutsResolver::targetMatches( $module, '/content/view' ), 'the site never supplies a context' );
        $this->assertTrue( expLayoutsResolver::targetMatches( $module, 'content/view', array( 'module' => 'content', 'view' => 'view' ) ) );
        $this->assertFalse( expLayoutsResolver::targetMatches( $module, 'content/edit', array( 'module' => 'content', 'view' => 'edit' ) ) );
        $path = new expLayoutsRuleTarget();
        $path->setAttribute( 'target_type', 'path_prefix' );
        $path->setAttribute( 'target_value', '/content' );
        $this->assertFalse( expLayoutsResolver::targetMatches( $path, '/content/view', array( 'module' => 'content', 'view' => 'view' ) ), 'a site target never matches an admin request' );
    }

    // AL-03

    public function testSiteAccessMatch()
    {
        foreach ( array( 'admin', 'admin_ger', 'admintest_admin4', 'admintest_admin4l' ) as $name )
            $this->assertTrue( expLayoutsResolver::isAdminSiteAccess( $name ), $name );
        foreach ( array( 'site', 'eng', 'administrator', '' ) as $name )
            $this->assertFalse( expLayoutsResolver::isAdminSiteAccess( $name ), "'$name'" );
        $this->assertTrue( expLayoutsResolver::isAdminSiteAccess(), 'the current siteaccess of this test is admin' );
    }

    // AL-04

    public function testTheModulesAndViewsGetTheirLayouts()
    {
        $expected = array( array( 'content', 'view', 'admin_3col' ), array( 'content', 'dashboard', 'admin_3col' ), array( 'setup', 'rad', 'admin_3col' ),
                           array( 'user', 'preferences', 'admin_3col' ), array( 'shop', 'orderlist', 'admin_3col' ),
                           array( 'content', 'edit', 'admin_full' ), array( 'no_such_module', 'index', 'admin_3col' ) );
        foreach ( $expected as $e )
        {
            $layout = expLayoutsResolver::resolveAdmin( $e[0], $e[1] );
            $this->assertNotFalse( $layout, "{$e[0]}/{$e[1]}" );
            $this->assertSame( $e[2], $this->typeOf( $layout ), "{$e[0]}/{$e[1]}" );
            $this->assertSame( 2, (int)$layout->attribute( 'status' ), 'only published layouts resolve' );
        }
    }

    public function testTheRequestOfTheKernelIsUsedWithoutArguments()
    {
        $this->request( 'content', 'edit' );
        $this->assertSame( 'admin_full', $this->typeOf( expLayoutsResolver::resolveAdmin() ) );
        $this->request( 'setup', 'info' );
        $this->assertSame( 'admin_3col', $this->typeOf( expLayoutsResolver::resolveAdmin() ) );
        unset( $GLOBALS['eZRequestedModuleParams'] );
        $this->assertFalse( expLayoutsResolver::resolveAdmin(), 'no request, no module' );
    }

    public function testTheSafetySwitchReturnsPlainAdmin4()
    {
        $this->assertTrue( expLayoutsResolver::adminLayoutsEnabled() );
        $this->assertNotFalse( expLayoutsResolver::resolveAdmin( 'content', 'view' ) );
        foreach ( array( 'disabled', '', 'yes' ) as $value )
        {
            $this->setEnabled( $value );
            $this->assertFalse( expLayoutsResolver::adminLayoutsEnabled(), "'$value'" );
            $this->assertFalse( expLayoutsResolver::resolveAdmin( 'content', 'view' ), "'$value'" );
        }
    }

    public function testASiteSiteaccessNeverGetsAnAdminLayout()
    {
        $GLOBALS['eZCurrentAccess']['name'] = 'site';
        $this->assertFalse( expLayoutsResolver::resolveAdmin( 'content', 'view' ) );
        $GLOBALS['eZCurrentAccess']['name'] = 'admin';
        $this->assertNotFalse( expLayoutsResolver::resolveAdmin( 'content', 'view' ) );
    }

    public function testAnUnresolvedAdminPageFallsBackToTheDefaultLayoutOnlyWhenItIsAnAdminLayout()
    {
        $ini = eZINI::instance( 'explayouts.ini' );
        $ini->setVariable( 'AdminLayoutSettings', 'DefaultLayout', '' );
        expLayoutsResolver::clearCache();
        // the module '*' rule matches everything: with it present nothing is unresolved, so only the setting is checked
        $this->assertNotFalse( expLayoutsResolver::resolveAdmin( 'no_such_module', 'index' ) );
        $site = null;
        foreach ( expLayoutsLayout::fetchList() as $candidate )
            if ( (int)$candidate->attribute( 'status' ) === 2 && !expLayoutsLayoutType::isAdminType( $candidate->attribute( 'layout_type' ) ) )
            {
                $site = $candidate;
                break;
            }
        if ( $site )
        {
            $ini->setVariable( 'AdminLayoutSettings', 'DefaultLayout', $site->attribute( 'identifier' ) );
            expLayoutsResolver::clearCache();
            $layout = expLayoutsResolver::resolveAdmin( 'no_such_module', 'index' );
            $this->assertTrue( !$layout || expLayoutsLayoutType::isAdminType( $layout->attribute( 'layout_type' ) ), 'a site layout is never taken as the default' );
        }
    }

    // AL-05

    public function testTheSiteResolverNeverReturnsAnAdminLayout()
    {
        foreach ( array( '/', '/content/view/full/2', '/Fit-Healthy', '/setup/rad', '/user/preferences' ) as $path )
        {
            $layout = expLayoutsResolver::resolve( $path );
            $this->assertTrue( !$layout || !expLayoutsLayoutType::isAdminType( $layout->attribute( 'layout_type' ) ), "site path $path" );
        }
    }

    public function testNoPublishedAdminLayoutIsSharedAndTheSitesListHidesThem()
    {
        $adminIds = array();
        foreach ( expLayoutsLayout::fetchList() as $layout )
            if ( expLayoutsLayoutType::isAdminType( $layout->attribute( 'layout_type' ) ) )
                $adminIds[] = (int)$layout->attribute( 'id' );
        $this->assertNotEmpty( $adminIds, 'the admin layouts are seeded' );
        foreach ( expLayoutsLayout::fetchShared() as $shared )
            $this->assertNotContains( (int)$shared->attribute( 'id' ), $adminIds, 'an admin layout is never offered as shared layout' );
    }

    // AL-06

    public function testTheCacheKeyIsFiveStringsAndFollowsModuleViewSiteaccessAndGeneration()
    {
        $a = expLayoutsResolver::adminCacheKey( 'content', 'view' );
        $this->assertCount( 5, $a );
        foreach ( $a as $part )
            $this->assertIsString( $part );
        $this->assertSame( $a, expLayoutsResolver::adminCacheKey( 'content', 'view' ), 'stable' );
        $this->assertNotSame( $a, expLayoutsResolver::adminCacheKey( 'content', 'edit' ) );
        $this->assertNotSame( $a, expLayoutsResolver::adminCacheKey( 'setup', 'view' ) );
        $GLOBALS['eZCurrentAccess']['name'] = 'admintest_admin4';
        $this->assertNotSame( $a, expLayoutsResolver::adminCacheKey( 'content', 'view' ), 'per siteaccess' );
        $GLOBALS['eZCurrentAccess']['name'] = 'admin';
        $this->request( 'content', 'view' );
        $this->assertSame( $a, expLayoutsResolver::adminCacheKey(), 'the request of the kernel when no module is named' );
    }

    public function testTheGenerationChangesWhenTheResolverCacheIsCleared()
    {
        $before = expLayoutsResolver::adminGeneration();
        $this->assertNotSame( '', $before );
        $this->assertSame( $before, expLayoutsResolver::adminGeneration(), 'stable between clears' );
        expLayoutsResolver::clearCache();
        $this->assertNotSame( $before, expLayoutsResolver::adminGeneration() );
        $this->assertNotSame( $before, expLayoutsResolver::adminCacheKey( 'content', 'view' )[0] );
    }

    public function testResolveAdminIsAnsweredTwiceTheSameAndFromTheCache()
    {
        $first = expLayoutsResolver::resolveAdmin( 'content', 'view' );
        $second = expLayoutsResolver::resolveAdmin( 'content', 'view' );
        $this->assertSame( (int)$first->attribute( 'id' ), (int)$second->attribute( 'id' ) );
        $start = microtime( true );
        for ( $i = 0; $i < 200; $i++ )
            expLayoutsResolver::resolveAdmin( 'content', 'view' );
        $this->assertLessThan( 0.5, microtime( true ) - $start, '200 repeated lookups' );
    }

    // AL-07

    public function testEveryAdminBlockIsDefinedWithAHandlerAViewTemplateAndOnlyInTheAdminList()
    {
        $ini = eZINI::instance( 'explayouts.ini' );
        $admin = $ini->variable( 'AdminBlockSettings', 'AvailableBlocks' );
        $site = $ini->variable( 'BlockSettings', 'AvailableBlocks' );
        $this->assertGreaterThanOrEqual( 18, count( $admin ) );
        foreach ( $admin as $id )
        {
            $group = 'BlockDefinition_' . $id;
            $this->assertTrue( $ini->hasGroup( $group ), $id );
            $this->assertSame( 'admin', $ini->variable( $group, 'Group' ), "$id is an admin block" );
            $handler = $ini->variable( $group, 'Handler' );
            $this->assertTrue( class_exists( $handler ), "$id handler $handler" );
            $this->assertFileExists( 'design/admin4l/templates/explayouts/block/' . $id . '.tpl', $id );
            $this->assertNotContains( $id, $site, "$id is not offered to the site layouts" );
        }
        foreach ( array( 'admin_module_result', 'admin_logo', 'admin_tab_menu', 'admin_left_menu', 'admin_footer' ) as $id )
            $this->assertContains( $id, $admin );
    }

    public function testEveryAdminLayoutHasTheZonesOfItsTypeAndTheMainZoneHoldsTheModuleResult()
    {
        foreach ( array( 'admin_3col', 'admin_2col', 'admin_full' ) as $type )
        {
            $layout = expLayoutsLayout::fetchByIdentifier( $type, 2 );
            $this->assertNotFalse( $layout, "$type is published" );
            $zones = array();
            foreach ( expLayoutsZone::fetchByLayout( (int)$layout->attribute( 'id' ), 2 ) as $zone )
            {
                $zones[] = $zone->attribute( 'identifier' );
                if ( $zone->attribute( 'identifier' ) === 'main' )
                {
                    $defs = array();
                    foreach ( expLayoutsBlock::fetchByZone( (int)$zone->attribute( 'id' ), 2 ) as $block )
                        $defs[] = $block->attribute( 'definition_identifier' );
                    $this->assertContains( 'admin_module_result', $defs, "$type main zone" );
                }
            }
            $this->assertEqualsCanonicalizing( expLayoutsLayoutType::getZones( $type ), $zones, $type );
        }
    }

    public function testTheTemplatesTheAdmin4lDesignOwnsExist()
    {
        $this->assertFileExists( 'design/admin4l/templates/pagelayout.tpl' );
        $this->assertFileExists( 'design/admin4l/templates/explayouts/admin_zone.tpl' );
        $text = (string)file_get_contents( 'design/admin4l/templates/pagelayout.tpl' );
        $this->assertStringContainsString( 'resolve_admin_layout', $text );
        $this->assertStringContainsString( 'admin_layout_cache_key', $text, 'cached parts are keyed by the admin layout' );
    }
}
