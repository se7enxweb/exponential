<?php
/**
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expUsersTestCase.php';

class expSessionPreferencesServicesTest extends expUsersTestCase
{
    protected function fakeSession( $userId, $expires )
    {
        $key = substr( 'exptest' . md5( uniqid( '', true ) ), 0, 32 );
        $db = eZDB::instance();
        $db->query( "INSERT INTO ezsession ( session_key, expiration_time, data, user_id, user_hash ) VALUES ( '$key', " . (int)$expires . ", '', " . (int)$userId . ", '' )" );
        self::$made['sessions'][] = $key;
        return $key;
    }

    public function testOwnAndSettingsAndHandler()
    {
        $this->assertArrayHasKey( 'user_id', $this->okCall( 'expSessionAdminServices', 'own' ) );
        $s = $this->okCall( 'expSessionAdminServices', 'settings' );
        $this->assertGreaterThan( 0, $s['session_timeout'] );
        $this->assertNotEmpty( $this->okCall( 'expSessionAdminServices', 'handler' )['class'] );
    }

    public function testCountsAndStats()
    {
        $c = $this->okCall( 'expSessionAdminServices', 'count' );
        $this->assertSame( $c['active'] + $c['expired'], $c['total'] );
        $this->assertArrayHasKey( 'registered', $this->okCall( 'expSessionAdminServices', 'stats' ) );
        $this->assertGreaterThanOrEqual( 0, $this->okCall( 'expSessionAdminServices', 'expiredCount' )['count'] );
        $this->assertPaged( $this->call( 'expSessionAdminServices', 'listAll', array( '5' ) ) );
    }

    public function testSessionsNeedThePolicy()
    {
        $this->loginAs( $this->memberUser() );
        $this->assertError( $this->call( 'expSessionAdminServices', 'listAll' ), 403 );
        $this->assertError( $this->write( 'expSessionAdminServices', 'removeExpired' ), 403 );
        $this->loginAnonymous();
        $this->assertError( $this->call( 'expSessionAdminServices', 'count' ), 401 );
    }

    public function testListAndRemoveASession()
    {
        $u = $this->newUser();
        $key = $this->fakeSession( $u, time() + 600 );
        $r = $this->call( 'expSessionAdminServices', 'byUser', array( (string)$u ) );
        $this->assertPaged( $r );
        $this->assertCount( 1, $r['data'] );
        $this->assertStringNotContainsString( $key, json_encode( $r ) );
        $this->assertTrue( $this->okWrite( 'expSessionAdminServices', 'remove', array( 'id' => $r['data'][0]['id'] ) )['removed'] );
        $this->assertCount( 0, $this->call( 'expSessionAdminServices', 'byUser', array( (string)$u ) )['data'] );
        $this->assertError( $this->write( 'expSessionAdminServices', 'remove', array( 'id' => 'doesnotexist' ) ), 404 );
    }

    public function testRemoveByUser()
    {
        $u = $this->newUser();
        $this->fakeSession( $u, time() + 600 );
        $this->fakeSession( $u, time() + 900 );
        $this->assertSame( 2, $this->okWrite( 'expSessionAdminServices', 'removeByUser', array( 'id' => $u ) )['removed'] );
        $this->assertError( $this->write( 'expSessionAdminServices', 'removeByUser', array( 'id' => eZUser::anonymousId() ) ), 409 );
    }

    public function testOwnSessionsOfATestUser()
    {
        $u = $this->memberUser();
        $this->fakeSession( $u, time() + 600 );
        $keep = $this->fakeSession( $u, time() - 600 );
        $this->loginAs( $u );
        $this->assertSame( 1, $this->okCall( 'expSessionAdminServices', 'ownCount' )['count'] );
        $list = $this->call( 'expSessionAdminServices', 'ownList' );
        $this->assertPaged( $list );
        $this->okWrite( 'expSessionAdminServices', 'ownRemove', array( 'id' => $list['data'][0]['id'] ) );
        $this->assertSame( 0, $this->okCall( 'expSessionAdminServices', 'ownCount' )['count'] );
    }

    public function testExpiredSessionsAreCountedNotListed()
    {
        $u = $this->newUser();
        $this->fakeSession( $u, time() - 100 );
        $before = $this->okCall( 'expSessionAdminServices', 'expiredCount' )['count'];
        $this->assertGreaterThanOrEqual( 1, $before );
        $this->assertCount( 1, $this->call( 'expSessionAdminServices', 'byUser', array( (string)$u ) )['data'] );
    }

    public function testPreferencesRoundTrip()
    {
        $n = 'expservices_test_' . self::uniq();
        $this->assertFalse( $this->okCall( 'expPreferencesServices', 'exists', array( $n ) )['exists'] );
        $this->okWrite( 'expPreferencesServices', 'set', array( 'name' => $n, 'value' => 'blue' ) );
        $this->assertSame( 'blue', $this->okCall( 'expPreferencesServices', 'get', array( $n ) )['value'] );
        $this->assertTrue( $this->okCall( 'expPreferencesServices', 'exists', array( $n ) )['exists'] );
        $this->assertSame( 'blue', ((array)$this->okCall( 'expPreferencesServices', 'getMany', array( $n . ',missing_x' ) ))[$n] );
        $r = $this->call( 'expPreferencesServices', 'byPrefix', array( 'expservices_test_' ) );
        $this->assertPaged( $r );
        $this->assertContains( $n, array_column( $r['data'], 'name' ) );
        $this->okWrite( 'expPreferencesServices', 'remove', array( 'name' => $n ) );
        $this->assertNull( $this->okCall( 'expPreferencesServices', 'get', array( $n ) )['value'] );
    }

    public function testPreferenceNamesAndValuesAreChecked()
    {
        $this->assertError( $this->write( 'expPreferencesServices', 'set', array( 'name' => 'bad name!', 'value' => 'x' ) ), 422 );
        $this->assertError( $this->write( 'expPreferencesServices', 'set', array( 'name' => 'expservices_test_v', 'value' => array( 'a' ) ) ), 400 );
    }

    public function testSetManyAndIncrement()
    {
        $a = 'expservices_test_' . self::uniq();
        $b = 'expservices_test_' . self::uniq();
        $this->okWrite( 'expPreferencesServices', 'setMany', array( 'values' => json_encode( array( $a => '1', $b => 'two' ) ) ) );
        $this->assertSame( 2, $this->okWrite( 'expPreferencesServices', 'increment', array( 'name' => $a, 'by' => 1 ) )['value'] );
        $this->assertError( $this->write( 'expPreferencesServices', 'increment', array( 'name' => $b ) ), 409 );
        $this->assertGreaterThanOrEqual( 2, $this->okCall( 'expPreferencesServices', 'count' )['count'] );
        $this->assertPaged( $this->call( 'expPreferencesServices', 'listAll', array( '1' ) ) );
    }

    public function testPreferencesOfAnotherUser()
    {
        $u = $this->newUser();
        $n = 'expservices_test_' . self::uniq();
        $this->okWrite( 'expPreferencesServices', 'setOf', array( 'user' => $u, 'name' => $n, 'value' => 'v' ) );
        $this->assertSame( 'v', $this->okCall( 'expPreferencesServices', 'getOf', array( (string)$u, $n ) )['value'] );
        $this->assertCount( 1, $this->call( 'expPreferencesServices', 'listOf', array( (string)$u ) )['data'] );
    }

    public function testAnonymousHasNoPreferences()
    {
        $this->loginAnonymous();
        $this->assertError( $this->call( 'expPreferencesServices', 'listAll' ), 401 );
    }
}
