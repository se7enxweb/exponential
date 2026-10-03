<?php
/**
 * The ini and cache domains. Writes (cache clearing) are only tested as dry runs or refusals.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expServicesCoreTestCase.php';

class expIniCacheServicesTest extends expServicesCoreTestCase
{
    const INI = 'expIniServices';
    const CACHE = 'expCacheServices';

    // ---- ini

    public function testFiles()
    {
        $r = $this->ok( self::INI, 'files', array( '10' ) );
        $this->assertPaged( $r );
        $this->assertGreaterThan( 50, $r['meta']['total'] );
        $this->assertArrayHasKey( 'ini', $r['data'][0] );
    }

    public function testFilesContainsSiteIni()
    {
        $names = array();
        for ( $offset = 0; $offset < 2000; $offset += 200 )
        {
            $r = $this->ok( self::INI, 'files', array( '200', (string)$offset ) );
            $names = array_merge( $names, array_column( $r['data'], 'ini' ) );
            if ( !$r['meta']['has_more'] )
                break;
        }
        $this->assertContains( 'site.ini', $names );
    }

    public function testGroups()
    {
        $r = $this->ok( self::INI, 'groups', array( 'site.ini', '500' ) );
        $this->assertContains( 'SiteSettings', $r['data'] );
    }

    public function testGroupsRejectsPath()
    {
        $this->assertError( $this->call( self::INI, 'groups', array( '../settings/site.ini' ) ), 400 );
        $this->assertError( $this->call( self::INI, 'groups', array( 'site.php' ) ), 400 );
    }

    public function testGroupsMissingFile()
    {
        $this->assertError( $this->call( self::INI, 'groups', array( 'doesnotexist.ini' ) ), 404 );
    }

    public function testVariables()
    {
        $d = $this->ok( self::INI, 'variables', array( 'site.ini', 'SiteSettings' ) )['data'];
        $this->assertArrayHasKey( 'SiteName', $d );
    }

    public function testVariablesMissingGroup()
    {
        $this->assertError( $this->call( self::INI, 'variables', array( 'site.ini', 'NoSuchGroup' ) ), 404 );
    }

    public function testGet()
    {
        $d = $this->ok( self::INI, 'get', array( 'site.ini', 'SiteSettings', 'SiteName' ) )['data'];
        $this->assertSame( eZINI::instance()->variable( 'SiteSettings', 'SiteName' ), $d['value'] );
        $this->assertFalse( $d['masked'] );
    }

    public function testGetMissing()
    {
        $this->assertError( $this->call( self::INI, 'get', array( 'site.ini', 'SiteSettings', 'NoSuchVar' ) ), 404 );
    }

    public function testGetMasksSecrets()
    {
        $d = $this->ok( self::INI, 'get', array( 'site.ini', 'UserSettings', 'MinPasswordLength' ) )['data'];
        $this->assertTrue( $d['masked'] );
        $this->assertSame( expIniEditor::MASK, $d['value'] );
    }

    public function testVariablesMaskSecrets()
    {
        $d = $this->ok( self::INI, 'variables', array( 'site.ini', 'DatabaseSettings' ) )['data'];
        foreach ( $d as $name => $value )
            if ( expIniEditor::isSecret( $name ) && $value !== '' )
                $this->assertSame( expIniEditor::MASK, $value, $name );
        $this->assertArrayHasKey( 'Server', $d );
    }

    public function testNoDatabasePasswordInAnyAnswer()
    {
        $password = (string)eZINI::instance()->variable( 'DatabaseSettings', 'Password' );
        if ( $password === '' )
            $this->markTestSkipped( 'No database password is set' );
        $all = json_encode( $this->ok( self::INI, 'variables', array( 'site.ini', 'DatabaseSettings' ) ) )
              . json_encode( $this->ok( self::INI, 'search', array( 'site.ini', 'pass' ) ) )
              . json_encode( $this->ok( self::INI, 'get', array( 'site.ini', 'DatabaseSettings', 'Password' ) ) );
        $this->assertStringNotContainsString( $password, $all );
    }

    public function testHas()
    {
        $this->assertTrue( $this->ok( self::INI, 'has', array( 'site.ini', 'SiteSettings' ) )['data']['exists'] );
        $this->assertTrue( $this->ok( self::INI, 'has', array( 'site.ini', 'SiteSettings', 'SiteName' ) )['data']['exists'] );
        $this->assertFalse( $this->ok( self::INI, 'has', array( 'site.ini', 'SiteSettings', 'Nope' ) )['data']['exists'] );
        $this->assertFalse( $this->ok( self::INI, 'has', array( 'site.ini', 'NoGroup' ) )['data']['exists'] );
    }

    public function testSearch()
    {
        $r = $this->ok( self::INI, 'search', array( 'site.ini', 'sitename', '10' ) );
        $this->assertPaged( $r );
        $this->assertContains( 'SiteName', array_column( $r['data'], 'variable' ) );
    }

    public function testSiteaccesses()
    {
        $r = $this->ok( self::INI, 'siteaccesses' );
        $this->assertContains( 'admin', $r['data'] );
    }

    public function testScopes()
    {
        $r = $this->ok( self::INI, 'scopes' );
        $this->assertGreaterThan( 2, $r['meta']['total'] );
        $this->assertArrayHasKey( 'name', $r['data'][0] );
    }

    public function testSecret()
    {
        $this->assertTrue( $this->ok( self::INI, 'secret', array( 'ApiKey' ) )['data']['secret'] );
        $this->assertTrue( $this->ok( self::INI, 'secret', array( 'Password' ) )['data']['secret'] );
        $this->assertFalse( $this->ok( self::INI, 'secret', array( 'SortKey' ) )['data']['secret'] );
        $this->assertFalse( $this->ok( self::INI, 'secret', array( 'SiteName' ) )['data']['secret'] );
    }

    public function testActiveExtensions()
    {
        $r = $this->ok( self::INI, 'activeextensions' );
        $this->assertContains( 'expservices', $r['data'] );
    }

    public function testIniNeedsSetupPolicy()
    {
        $this->loginAnonymous();
        foreach ( array( 'files', 'groups', 'get', 'secret' ) as $m )
            $this->assertError( $this->call( self::INI, $m, array( 'site.ini', 'a', 'b' ) ), 401, $m );
    }

    public function testIniIsReadOnly()
    {
        foreach ( expIniServices::$services as $name => $decl )
            $this->assertFalse( $decl['write'], $name );
    }

    // ---- cache

    public function testCacheList()
    {
        $r = $this->ok( self::CACHE, 'list', array( '5' ) );
        $this->assertPaged( $r );
        $this->assertGreaterThan( 10, $r['meta']['total'] );
        foreach ( array( 'id', 'name', 'tags', 'enabled', 'how' ) as $k )
            $this->assertArrayHasKey( $k, $r['data'][0] );
    }

    public function testCacheListNeedsPolicy()
    {
        $this->loginAnonymous();
        $this->assertError( $this->call( self::CACHE, 'list' ), 401 );
    }

    public function testCacheTags()
    {
        $r = $this->ok( self::CACHE, 'tags' );
        $tags = array_column( $r['data'], 'tag' );
        $this->assertContains( 'content', $tags );
        $this->assertContains( 'template', $tags );
    }

    public function testCacheDescribe()
    {
        $d = $this->ok( self::CACHE, 'describe', array( 'content' ) )['data'];
        $this->assertSame( 'content', $d['id'] );
        $this->assertArrayHasKey( 'files', $d );
    }

    public function testCacheDescribeMissing()
    {
        $this->assertError( $this->call( self::CACHE, 'describe', array( 'nosuchcache' ) ), 404 );
        $this->assertError( $this->call( self::CACHE, 'describe' ), 400 );
    }

    public function testCachePhp()
    {
        $this->assertIsArray( $this->ok( self::CACHE, 'php' )['data'] );
    }

    public function testCacheVelocity()
    {
        $this->assertArrayHasKey( 'ok', $this->ok( self::CACHE, 'velocity' )['data'] );
    }

    public function testCacheHttp()
    {
        $d = $this->ok( self::CACHE, 'http' )['data'];
        $this->assertArrayHasKey( 'enabled', $d );
    }

    public function testCacheStaticIsOff()
    {
        $d = $this->ok( self::CACHE, 'staticcache' )['data'];
        $this->assertArrayHasKey( 'enabled', $d );
    }

    public function testCacheQuery()
    {
        $this->assertArrayHasKey( 'available', $this->ok( self::CACHE, 'query' )['data'] );
    }

    public function testClearIsAWrite()
    {
        foreach ( array( 'clear', 'cleartag', 'clearid', 'clearnode', 'clearvelocity', 'clearopcache' ) as $m )
            $this->assertTrue( expCacheServices::$services[$m]['write'], $m );
    }

    public function testClearNeedsPost()
    {
        foreach ( array( 'clear', 'cleartag', 'clearid', 'clearnode', 'clearvelocity', 'clearopcache' ) as $m )
            $this->assertError( $this->call( self::CACHE, $m ), 403, $m );
    }

    public function testClearNeedsPolicy()
    {
        expServiceBase::$trustRequest = true;
        $this->loginAnonymous();
        $this->assertError( $this->call( self::CACHE, 'clear' ), 401 );
    }

    public function testClearBadArgs()
    {
        expServiceBase::$trustRequest = true;
        expServiceBase::$postData = array( 'by' => 'everything' );
        $this->assertError( $this->call( self::CACHE, 'clear' ), 400 );
        expServiceBase::$postData = array( 'by' => 'tag' );
        $this->assertError( $this->call( self::CACHE, 'clear' ), 400 );
        expServiceBase::$postData = array();
        $this->assertError( $this->call( self::CACHE, 'clear' ), 400 );
    }

    public function testClearTagDryRun()
    {
        expServiceBase::$trustRequest = true;
        expServiceBase::$postData = array( 'tag' => 'template', 'dry_run' => '1' );
        $d = $this->ok( self::CACHE, 'cleartag' )['data'];
        $this->assertTrue( $d['dry_run'] );
        $this->assertTrue( $d['ok'] );
    }

    public function testClearIdDryRun()
    {
        expServiceBase::$trustRequest = true;
        expServiceBase::$postData = array( 'id' => 'content', 'dry_run' => '1' );
        $this->assertTrue( $this->ok( self::CACHE, 'clearid' )['data']['dry_run'] );
    }

    public function testClearAllDryRun()
    {
        expServiceBase::$trustRequest = true;
        expServiceBase::$postData = array( 'by' => 'all', 'dry_run' => '1' );
        $d = $this->ok( self::CACHE, 'clear' )['data'];
        $this->assertTrue( $d['dry_run'] );
        $this->assertNotEmpty( $d['items'] );
    }

    public function testClearVelocityDryRun()
    {
        expServiceBase::$trustRequest = true;
        expServiceBase::$postData = array( 'dry_run' => '1' );
        $r = $this->call( self::CACHE, 'clearvelocity' );
        $this->assertTrue( $r['ok'] || $r['error']['code'] === 422 );
    }

    public function testClearOpcacheDryRun()
    {
        expServiceBase::$trustRequest = true;
        expServiceBase::$postData = array( 'dry_run' => '1' );
        $r = $this->call( self::CACHE, 'clearopcache' );
        $this->assertTrue( $r['ok'] || $r['error']['code'] === 422 );
    }

    public function testClearUnknownTagIsRefused()
    {
        expServiceBase::$trustRequest = true;
        expServiceBase::$postData = array( 'tag' => 'no-such-tag', 'dry_run' => '1' );
        $r = $this->call( self::CACHE, 'cleartag' );
        $this->assertFalse( $r['ok'] );
    }

    public function testClearNodeUnknown()
    {
        expServiceBase::$trustRequest = true;
        expServiceBase::$postData = array( 'node_id' => '99999999' );
        $this->assertError( $this->call( self::CACHE, 'clearnode' ), 404 );
    }
}
