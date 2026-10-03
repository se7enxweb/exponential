<?php
/**
 * The session and system domains, against the live installation.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expServicesCoreTestCase.php';

class expSessionSystemServicesTest extends expServicesCoreTestCase
{
    const SES = 'expSessionServices';
    const SYS = 'expSystemServices';

    // ---- session

    public function testWhoamiAdmin()
    {
        $d = $this->ok( self::SES, 'whoami' )['data'];
        $this->assertSame( 'admin', $d['login'] );
        $this->assertTrue( $d['registered'] );
        $this->assertFalse( $d['anonymous'] );
        $this->assertIsArray( $d['groups'] );
    }

    public function testWhoamiNeverExposesSecrets()
    {
        $json = json_encode( $this->ok( self::SES, 'whoami' ) );
        $this->assertStringNotContainsString( 'password', strtolower( $json ) );
        $this->assertStringNotContainsString( 'hash', strtolower( $json ) );
    }

    public function testWhoamiAnonymous()
    {
        $this->loginAnonymous();
        $d = $this->ok( self::SES, 'whoami' )['data'];
        $this->assertTrue( $d['anonymous'] );
        $this->assertNull( $d['login'] );
        $this->assertNull( $d['email'] );
    }

    public function testTokenShape()
    {
        $d = $this->ok( self::SES, 'token' )['data'];
        $this->assertSame( 'ezxform_token', $d['field'] );
        $this->assertSame( 'X-CSRF-Token', $d['header'] );
        $this->assertSame( expServiceBase::formToken(), $d['token'] );
    }

    public function testTokenWorksForWritesOfTheFixture()
    {
        if ( expServiceBase::formToken() === null )
            $this->markTestSkipped( 'The form token protection is off' );
        $token = $this->ok( self::SES, 'token' )['data']['token'];
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['ezxform_token'] = $token;
        $this->assertTrue( $this->call( 'expServicesFixtureServices', 'change' )['ok'] );
    }

    public function testPing()
    {
        $d = $this->ok( self::SES, 'ping' )['data'];
        $this->assertTrue( $d['registered'] );
        $this->assertEqualsWithDelta( time(), $d['time'], 5 );
        $this->loginAnonymous();
        $this->assertFalse( $this->ok( self::SES, 'ping' )['data']['registered'] );
    }

    public function testAccessYes()
    {
        $d = $this->ok( self::SES, 'access', array( 'setup', 'setup' ) )['data'];
        $this->assertSame( 'yes', $d['access_word'] );
    }

    public function testAccessNo()
    {
        $d = $this->ok( self::SES, 'access', array( 'nomodule', 'nofunction' ) )['data'];
        $this->assertContains( $d['access_word'], array( 'yes', 'no', 'limited' ) );
    }

    public function testAccessArgs()
    {
        $this->assertError( $this->call( self::SES, 'access', array( 'setup' ) ), 400 );
        $this->assertError( $this->call( self::SES, 'access', array( 'set up', 'x' ) ), 400 );
    }

    public function testAccessNeedsLogin()
    {
        $this->loginAnonymous();
        $this->assertError( $this->call( self::SES, 'access', array( 'setup', 'setup' ) ), 401 );
    }

    public function testRoles()
    {
        $r = $this->ok( self::SES, 'roles' );
        $this->assertGreaterThanOrEqual( 1, $r['meta']['total'] );
        $this->assertArrayHasKey( 'policies', $r['data'][0] );
    }

    public function testGroups()
    {
        $r = $this->ok( self::SES, 'groups' );
        $this->assertGreaterThanOrEqual( 1, count( $r['data'] ) );
        $this->assertArrayHasKey( 'name', $r['data'][0] );
    }

    public function testLoginNeedsPost()
    {
        $this->assertError( $this->call( self::SES, 'login' ), 403 );
    }

    public function testLoginNeedsFields()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertError( $this->call( self::SES, 'login' ), 400 );
    }

    public function testLoginWrongPassword()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        expServiceBase::$postData = array( 'username' => 'no-such-user-' . uniqid(), 'password' => 'x' . uniqid() );
        $this->assertError( $this->call( self::SES, 'login' ), 401 );
    }

    public function testLogoutNeedsPostAndLogin()
    {
        $this->assertError( $this->call( self::SES, 'logout' ), 403 );
        $this->loginAnonymous();
        expServiceBase::$trustRequest = true;
        $this->assertError( $this->call( self::SES, 'logout' ), 401 );
    }

    public function testSessionServicesAreDeclared()
    {
        $this->assertCount( 8, expSessionServices::$services );
        $this->assertTrue( expSessionServices::$services['logout']['write'] );
        $this->assertFalse( expSessionServices::$services['login']['write'] );
    }

    public function testExportUserShape()
    {
        $d = expSessionServices::exportUser( eZUser::currentUser() );
        foreach ( array( 'id', 'login', 'name', 'email', 'registered', 'anonymous', 'groups' ) as $k )
            $this->assertArrayHasKey( $k, $d );
    }

    // ---- system

    public function testVersion()
    {
        $d = $this->ok( self::SYS, 'version' )['data'];
        $this->assertSame( eZPublishSDK::version(), $d['version'] );
        $this->assertSame( PHP_VERSION, $d['php'] );
        $this->assertIsInt( $d['major'] );
    }

    public function testVersionIsPublic()
    {
        $this->loginAnonymous();
        $this->assertTrue( $this->call( self::SYS, 'version' )['ok'] );
    }

    public function testInfoNeedsPolicy()
    {
        $this->loginAnonymous();
        $this->assertError( $this->call( self::SYS, 'info' ), 401 );
    }

    public function testInfo()
    {
        $d = $this->ok( self::SYS, 'info' )['data'];
        foreach ( array( 'site_name', 'site_url', 'version', 'php', 'database', 'database_type', 'locale', 'extensions' ) as $k )
            $this->assertArrayHasKey( $k, $d );
        $this->assertGreaterThan( 0, $d['extensions'] );
    }

    public function testInfoHasNoCredentials()
    {
        $json = strtolower( json_encode( $this->ok( self::SYS, 'info' ) ) . json_encode( $this->ok( self::SYS, 'database' ) ) );
        $this->assertStringNotContainsString( 'password', $json );
        $this->assertStringNotContainsString( 'user"', $json );
    }

    public function testSettings()
    {
        $d = $this->ok( self::SYS, 'settings' )['data'];
        $this->assertArrayHasKey( 'design', $d );
        $this->assertArrayHasKey( 'default_access', $d );
    }

    public function testTime()
    {
        $d = $this->ok( self::SYS, 'time' )['data'];
        $this->assertEqualsWithDelta( time(), $d['timestamp'], 5 );
        $this->assertNotFalse( strtotime( $d['iso'] ) );
    }

    public function testPhp()
    {
        $d = $this->ok( self::SYS, 'php' )['data'];
        $this->assertSame( PHP_VERSION, $d['version'] );
        $this->assertSame( PHP_SAPI, $d['sapi'] );
        $this->assertArrayHasKey( 'memory_limit', $d );
    }

    public function testPhpExtensionsPaged()
    {
        $r = $this->ok( self::SYS, 'phpextensions', array( '5', '2' ) );
        $this->assertPaged( $r );
        $this->assertCount( 5, $r['data'] );
        $this->assertSame( 2, $r['meta']['offset'] );
        $this->assertArrayHasKey( 'name', $r['data'][0] );
    }

    public function testPhpExtensionsContainsCore()
    {
        $r = $this->ok( self::SYS, 'phpextensions', array( '200' ) );
        $names = array_column( $r['data'], 'name' );
        $this->assertContains( 'json', $names );
    }

    public function testPhpExtensionsBadPaging()
    {
        $this->assertError( $this->call( self::SYS, 'phpextensions', array( 'x' ) ), 400 );
        $this->assertError( $this->call( self::SYS, 'phpextensions', array( '0' ) ), 400 );
    }

    public function testDatabase()
    {
        $d = $this->ok( self::SYS, 'database' )['data'];
        $this->assertTrue( $d['connected'] );
        $this->assertNotSame( '', $d['name'] );
    }

    public function testSiteaccesses()
    {
        $r = $this->ok( self::SYS, 'siteaccesses' );
        $this->assertContains( 'admin', $r['data'] );
    }

    public function testSiteaccess()
    {
        $d = $this->ok( self::SYS, 'siteaccess' )['data'];
        $this->assertArrayHasKey( 'name', $d );
    }

    public function testLanguages()
    {
        $r = $this->ok( self::SYS, 'languages' );
        $this->assertGreaterThanOrEqual( 1, $r['meta']['total'] );
        $this->assertArrayHasKey( 'locale', $r['data'][0] );
    }

    public function testLocales()
    {
        $r = $this->ok( self::SYS, 'locales', array( '5' ) );
        $this->assertPaged( $r );
        $this->assertCount( 5, $r['data'] );
    }

    public function testHealth()
    {
        $d = $this->ok( self::SYS, 'health' )['data'];
        $this->assertTrue( $d['ok'] );
        $this->assertGreaterThanOrEqual( 4, count( $d['checks'] ) );
    }

    public function testDirectoriesAreRelative()
    {
        $r = $this->ok( self::SYS, 'directories' );
        foreach ( $r['data'] as $d )
            $this->assertStringStartsNotWith( '/', $d['path'] );
        $this->assertSame( 3, $r['meta']['total'] );
    }

    public function testStatistics()
    {
        $d = $this->ok( self::SYS, 'statistics' )['data'];
        $this->assertGreaterThan( 0, $d['objects'] );
        $this->assertGreaterThan( 0, $d['classes'] );
        $this->assertLessThanOrEqual( $d['objects'], $d['published_objects'] );
    }

    public function testLoad()
    {
        $d = $this->ok( self::SYS, 'load' )['data'];
        $this->assertArrayHasKey( 'used', $d['memory'] );
    }

    public function testUrls()
    {
        $d = $this->ok( self::SYS, 'urls' )['data'];
        $this->assertArrayHasKey( 'server_url', $d );
    }

    public function testSystemServicesAreAllReadOnly()
    {
        foreach ( expSystemServices::$services as $name => $decl )
            $this->assertFalse( $decl['write'], $name );
    }
}
