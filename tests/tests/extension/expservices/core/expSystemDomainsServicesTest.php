<?php
/**
 * The cronjob, extension, package, workflow, velocity, rad and debug domains (read-only).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expServicesCoreTestCase.php';

class expSystemDomainsServicesTest extends expServicesCoreTestCase
{
    // ---- cronjob

    public function testCronjobParts()
    {
        $r = $this->ok( 'expCronjobServices', 'parts' );
        $parts = array_column( $r['data'], 'part' );
        $this->assertContains( 'frequent', $parts );
        $this->assertContains( 'infrequent', $parts );
    }

    public function testCronjobScripts()
    {
        $d = $this->ok( 'expCronjobServices', 'scripts', array( 'frequent' ) )['data'];
        $this->assertSame( 'frequent', $d['part'] );
        $this->assertContains( 'workflow.php', array_column( $d['scripts'], 'script' ) );
    }

    public function testCronjobScriptsMissingPart()
    {
        $this->assertError( $this->call( 'expCronjobServices', 'scripts', array( 'nopart' ) ), 404 );
        $this->assertError( $this->call( 'expCronjobServices', 'scripts' ), 400 );
    }

    public function testCronjobAll()
    {
        $r = $this->ok( 'expCronjobServices', 'all', array( '2' ) );
        $this->assertPaged( $r );
        $this->assertCount( 2, $r['data'] );
    }

    public function testCronjobSettings()
    {
        $d = $this->ok( 'expCronjobServices', 'settings' )['data'];
        $this->assertGreaterThan( 0, $d['max_script_execution_time'] );
        $this->assertNotEmpty( $d['directories'] );
    }

    public function testCronjobStatus()
    {
        $r = $this->ok( 'expCronjobServices', 'status' );
        $this->assertIsArray( $r['data'] );
    }

    public function testCronjobDeferred()
    {
        $this->assertIsInt( $this->ok( 'expCronjobServices', 'deferred' )['data']['count'] );
    }

    public function testCronjobRunnables()
    {
        $r = $this->ok( 'expCronjobServices', 'runnables' );
        $this->assertPaged( $r );
    }

    public function testCronjobNeedsPolicy()
    {
        $this->loginAnonymous();
        $this->assertError( $this->call( 'expCronjobServices', 'parts' ), 401 );
    }

    // ---- extension

    public function testExtensionList()
    {
        $r = $this->ok( 'expExtensionServices', 'list', array( '200' ) );
        $names = array_column( $r['data'], 'name' );
        $this->assertContains( 'ezjscore', $names );
        $this->assertContains( 'expservices', $names );
    }

    public function testExtensionAvailable()
    {
        $r = $this->ok( 'expExtensionServices', 'available', array( '500' ) );
        $this->assertGreaterThanOrEqual( $this->ok( 'expExtensionServices', 'count' )['data']['active'], $r['meta']['total'] );
    }

    public function testExtensionInfo()
    {
        $d = $this->ok( 'expExtensionServices', 'info', array( 'expservices' ) )['data'];
        $this->assertTrue( $d['active'] );
        $this->assertSame( expServicesCatalog::VERSION, $d['info']['version'] );
    }

    public function testExtensionInfoMissingAndBad()
    {
        $this->assertError( $this->call( 'expExtensionServices', 'info', array( 'nosuchextension' ) ), 404 );
        $this->assertError( $this->call( 'expExtensionServices', 'info', array( '../x' ) ), 400 );
    }

    public function testExtensionIsActive()
    {
        $this->assertTrue( $this->ok( 'expExtensionServices', 'isactive', array( 'ezjscore' ) )['data']['active'] );
        $this->assertFalse( $this->ok( 'expExtensionServices', 'isactive', array( 'nosuchextension' ) )['data']['active'] );
    }

    public function testExtensionAccessAndDesign()
    {
        $this->assertIsArray( $this->ok( 'expExtensionServices', 'accessextensions' )['data'] );
        $this->assertIsArray( $this->ok( 'expExtensionServices', 'designextensions' )['data'] );
    }

    public function testExtensionSettingsFiles()
    {
        $r = $this->ok( 'expExtensionServices', 'settingsfiles', array( 'expservices' ) );
        $this->assertContains( 'extension/expservices/settings/expservices.ini', $r['data'] );
    }

    public function testExtensionDirectories()
    {
        $this->assertContains( 'extension', $this->ok( 'expExtensionServices', 'directories' )['data'] );
    }

    public function testExtensionCount()
    {
        $d = $this->ok( 'expExtensionServices', 'count' )['data'];
        $this->assertGreaterThan( 0, $d['active'] );
    }

    public function testExtensionNeedsPolicy()
    {
        $this->loginAnonymous();
        $this->assertError( $this->call( 'expExtensionServices', 'list' ), 401 );
    }

    // ---- package

    public function testPackageList()
    {
        $r = $this->ok( 'expPackageServices', 'list', array( '5' ) );
        $this->assertPaged( $r );
        if ( $r['data'] )
            foreach ( array( 'name', 'summary', 'version', 'vendor', 'type', 'state', 'installed' ) as $k )
                $this->assertArrayHasKey( $k, $r['data'][0] );
    }

    public function testPackageView()
    {
        $list = $this->ok( 'expPackageServices', 'list', array( '1' ) );
        if ( !$list['data'] )
            $this->markTestSkipped( 'No packages' );
        $d = $this->ok( 'expPackageServices', 'view', array( $list['data'][0]['name'] ) )['data'];
        $this->assertSame( $list['data'][0]['name'], $d['name'] );
        $this->assertArrayHasKey( 'description', $d );
    }

    public function testPackageViewMissing()
    {
        $this->assertError( $this->call( 'expPackageServices', 'view', array( 'no_such_package' ) ), 404 );
        $this->assertError( $this->call( 'expPackageServices', 'view' ), 400 );
    }

    public function testPackageFiles()
    {
        $list = $this->ok( 'expPackageServices', 'list', array( '1' ) );
        if ( !$list['data'] )
            $this->markTestSkipped( 'No packages' );
        $this->assertPaged( $this->ok( 'expPackageServices', 'files', array( $list['data'][0]['name'], '5' ) ) );
    }

    public function testPackageTypesStatesRepositories()
    {
        $this->assertGreaterThan( 0, $this->ok( 'expPackageServices', 'types' )['meta']['total'] );
        $this->assertGreaterThan( 0, $this->ok( 'expPackageServices', 'states' )['meta']['total'] );
        $this->assertContains( 'local', array_column( $this->ok( 'expPackageServices', 'repositories' )['data'], 'id' ) );
    }

    public function testPackageCount()
    {
        $d = $this->ok( 'expPackageServices', 'count' )['data'];
        $this->assertLessThanOrEqual( $d['total'], $d['installed'] );
        $this->assertSame( $d['total'], $this->ok( 'expPackageServices', 'list', array( '200' ) )['meta']['total'] );
    }

    // ---- workflow

    public function testWorkflowList()
    {
        $r = $this->ok( 'expWorkflowServices', 'list' );
        $this->assertPaged( $r );
    }

    public function testWorkflowViewMissing()
    {
        $this->assertError( $this->call( 'expWorkflowServices', 'view', array( '99999999' ) ), 404 );
        $this->assertError( $this->call( 'expWorkflowServices', 'events', array( '99999999' ) ), 404 );
    }

    public function testWorkflowViewOfAnExisting()
    {
        $list = $this->ok( 'expWorkflowServices', 'list' )['data'];
        if ( !$list )
            $this->markTestSkipped( 'No workflows' );
        $d = $this->ok( 'expWorkflowServices', 'view', array( $list[0]['id'] ) )['data'];
        $this->assertArrayHasKey( 'events', $d );
    }

    public function testWorkflowEventTypes()
    {
        $this->assertIsArray( $this->ok( 'expWorkflowServices', 'eventtypes' )['data'] );
    }

    public function testWorkflowGroups()
    {
        $this->assertIsArray( $this->ok( 'expWorkflowServices', 'groups' )['data'] );
    }

    public function testWorkflowTriggers()
    {
        $this->assertPaged( $this->ok( 'expWorkflowServices', 'triggers' ) );
        $this->assertPaged( $this->ok( 'expWorkflowServices', 'triggers', array( '5', '0', 'content' ) ) );
    }

    public function testWorkflowTriggerMissing()
    {
        $this->assertError( $this->call( 'expWorkflowServices', 'trigger', array( '99999999' ) ), 404 );
    }

    public function testWorkflowProcesses()
    {
        $this->assertPaged( $this->ok( 'expWorkflowServices', 'processes' ) );
        $this->assertError( $this->call( 'expWorkflowServices', 'process', array( '99999999' ) ), 404 );
    }

    public function testWorkflowStatuses()
    {
        $this->assertGreaterThan( 3, $this->ok( 'expWorkflowServices', 'statuses' )['meta']['total'] );
    }

    public function testWorkflowNeedsPolicy()
    {
        $this->loginAnonymous();
        $this->assertError( $this->call( 'expWorkflowServices', 'list' ), 401 );
    }

    // ---- velocity

    public function testVelocityInstalled()
    {
        $this->assertTrue( $this->ok( 'expVelocityServices', 'installed' )['data']['installed'] );
    }

    public function testVelocityEngines()
    {
        $d = $this->ok( 'expVelocityServices', 'engines' )['data'];
        $this->assertContains( 'qbix', $d['engines'] );
        $this->assertContains( $d['default'], $d['engines'] );
    }

    public function testVelocityStatus()
    {
        $d = $this->ok( 'expVelocityServices', 'status' )['data'];
        $this->assertArrayHasKey( 'running', $d );
        $this->assertArrayNotHasKey( 'pidFile', $d );
        $this->assertArrayNotHasKey( 'script', $d );
    }

    public function testVelocityStatusBadEngine()
    {
        $this->assertError( $this->call( 'expVelocityServices', 'status', array( 'nginx' ) ), 400 );
    }

    public function testVelocityUrls()
    {
        $r = $this->ok( 'expVelocityServices', 'urls' );
        $this->assertArrayHasKey( 'url', $r['data'][0] );
    }

    public function testVelocityCacheHidesPaths()
    {
        $json = json_encode( $this->ok( 'expVelocityServices', 'cache' ) );
        $this->assertStringNotContainsString( '"marker"', $json );
    }

    // ---- rad

    public function testRadSummary()
    {
        $r = $this->ok( 'expRadServices', 'summary' );
        $this->assertArrayHasKey( 'ini', $r['data'] );
        $this->assertArrayHasKey( 'settings', $r['data'] );
    }

    public function testRadCounts()
    {
        $d = $this->ok( 'expRadServices', 'counts', array( 'ini' ) )['data'];
        $this->assertSame( $this->ok( 'expRadServices', 'summary' )['data']['ini'], $d['value'] );
        $this->assertError( $this->call( 'expRadServices', 'counts', array( 'nope' ) ), 404 );
    }

    public function testRadGroups()
    {
        $this->assertIsArray( $this->ok( 'expRadServices', 'groups' )['data'] );
    }

    public function testRadRunnables()
    {
        $r = $this->ok( 'expRadServices', 'runnables', array( '3' ) );
        $this->assertPaged( $r );
        $cron = $this->ok( 'expRadServices', 'runnables', array( '200', '0', 'cronjob' ) );
        foreach ( $cron['data'] as $row )
            $this->assertSame( 'cronjob', $row['kind'] );
    }

    public function testRadFiles()
    {
        $this->assertPaged( $this->ok( 'expRadServices', 'files', array( '4' ) ) );
    }

    public function testRadIniCommandAndJobTypes()
    {
        $this->assertArrayHasKey( 'actions', $this->ok( 'expRadServices', 'inicommand' )['data'] );
        $this->assertIsArray( $this->ok( 'expRadServices', 'contentjobtypes' )['data'] );
    }

    // ---- debug

    public function testDebugSummary()
    {
        $d = $this->ok( 'expDebugServices', 'summary' )['data'];
        $this->assertIsArray( $d );
    }

    public function testDebugEnabledAndLevel()
    {
        $this->assertIsBool( $this->ok( 'expDebugServices', 'enabled' )['data']['enabled'] );
        $this->assertArrayHasKey( 'debug_output', $this->ok( 'expDebugServices', 'level' )['data'] );
    }

    public function testDebugThresholds()
    {
        $this->assertArrayHasKey( 'time_ms', $this->ok( 'expDebugServices', 'thresholds' )['data'] );
    }

    public function testDebugEngineAndPhp()
    {
        $this->assertNotEmpty( $this->ok( 'expDebugServices', 'engine' )['data']['engine'] );
        $this->assertSame( PHP_VERSION, $this->ok( 'expDebugServices', 'phpversion' )['data']['version'] );
    }

    public function testDebugNeedsPolicyAndLogin()
    {
        $this->loginAnonymous();
        $this->assertError( $this->call( 'expDebugServices', 'summary' ), 401 );
        $this->assertError( $this->call( 'expDebugServices', 'phpversion' ), 401 );
    }

    public function testEveryCoreServiceExceptWritesIsReadOnly()
    {
        foreach ( array( 'expCronjobServices', 'expExtensionServices', 'expPackageServices', 'expWorkflowServices', 'expVelocityServices', 'expRadServices', 'expDebugServices' ) as $class )
            foreach ( $class::$services as $name => $decl )
                $this->assertFalse( $decl['write'], "$class::$name" );
    }
}
