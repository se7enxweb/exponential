<?php
/**
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expUsersTestCase.php';

class expNotificationServicesTest extends expUsersTestCase
{
    public function testSettingsAndDigest()
    {
        $this->loginAs( $this->memberUser() );
        $d = $this->okCall( 'expNotificationServices', 'settings' );
        $this->assertFalse( $d['digest']['receive_digest'] );
        $r = $this->okWrite( 'expNotificationServices', 'setDigest', array( 'receive_digest' => '1', 'digest_type' => 3, 'time' => '08:30' ) );
        $this->assertTrue( $r['receive_digest'] );
        $this->assertSame( '08:30', $this->okCall( 'expNotificationServices', 'settings' )['digest']['time'] );
        $this->assertError( $this->write( 'expNotificationServices', 'setDigest', array( 'receive_digest' => '1', 'digest_type' => 9 ) ), 422 );
        $this->assertError( $this->write( 'expNotificationServices', 'setDigest', array( 'receive_digest' => '1', 'time' => '25:99' ) ), 422 );
        $this->assertCount( 4, $this->okCall( 'expNotificationServices', 'digestTypes' ) );
    }

    public function testSubscribeAndUnsubscribe()
    {
        $this->loginAs( $this->memberUser() );
        $this->assertFalse( $this->okCall( 'expNotificationServices', 'isSubscribed', array( '43' ) )['subscribed'] );
        $this->okWrite( 'expNotificationServices', 'subscribe', array( 'node' => 43 ) );
        $this->assertTrue( $this->okCall( 'expNotificationServices', 'isSubscribed', array( '43' ) )['subscribed'] );
        $this->assertSame( 1, $this->okCall( 'expNotificationServices', 'subscriptionCount' )['count'] );
        $this->assertError( $this->write( 'expNotificationServices', 'subscribe', array( 'node' => 43 ) ), 409 );
        $r = $this->call( 'expNotificationServices', 'subscriptions' );
        $this->assertPaged( $r );
        $this->assertSame( 43, $r['data'][0]['node_id'] );
        $this->assertTrue( $this->okWrite( 'expNotificationServices', 'setDigestForSubscription', array( 'node' => 43, 'use_digest' => '1' ) )['use_digest'] );
        $this->okWrite( 'expNotificationServices', 'unsubscribe', array( 'node' => 43 ) );
        $this->assertError( $this->write( 'expNotificationServices', 'unsubscribe', array( 'node' => 43 ) ), 404 );
    }

    public function testSubscribeToUnknownNodeAndUnsubscribeAll()
    {
        $this->loginAs( $this->memberUser() );
        $this->assertError( $this->write( 'expNotificationServices', 'subscribe', array( 'node' => 99999999 ) ), 404 );
        $this->okWrite( 'expNotificationServices', 'subscribe', array( 'node' => 43 ) );
        $this->okWrite( 'expNotificationServices', 'subscribe', array( 'node' => 2 ) );
        $this->assertSame( 2, $this->okWrite( 'expNotificationServices', 'unsubscribeAll' )['removed'] );
    }

    public function testCollaborationTypes()
    {
        $this->loginAs( $this->memberUser() );
        $this->okWrite( 'expNotificationServices', 'subscribeCollaboration', array( 'type' => 'ezapprove' ) );
        $this->assertContains( 'ezapprove', $this->okCall( 'expNotificationServices', 'collaborationTypes' ) );
        $this->assertError( $this->write( 'expNotificationServices', 'subscribeCollaboration', array( 'type' => 'nope' ) ), 422 );
        $this->okWrite( 'expNotificationServices', 'unsubscribeCollaboration', array( 'type' => 'ezapprove' ) );
        $this->assertNotContains( 'ezapprove', $this->okCall( 'expNotificationServices', 'collaborationTypes' ) );
    }

    public function testAdminReads()
    {
        $this->assertNotEmpty( $this->okCall( 'expNotificationServices', 'handlers' ) );
        $this->assertNotEmpty( $this->okCall( 'expNotificationServices', 'eventTypes' ) );
        $this->assertArrayHasKey( 'items_waiting', $this->okCall( 'expNotificationServices', 'queue' ) );
        $this->assertPaged( $this->call( 'expNotificationServices', 'events', array( '5' ) ) );
    }

    public function testAdminManagesAnotherUsersSubscription()
    {
        $u = $this->newUser();
        $this->okWrite( 'expNotificationServices', 'subscribeUser', array( 'user' => $u, 'node' => 43 ) );
        $this->assertCount( 1, $this->call( 'expNotificationServices', 'subscriptionsOf', array( (string)$u ) )['data'] );
        $this->assertContains( $u, array_column( $this->call( 'expNotificationServices', 'subscribers', array( '43' ) )['data'], 'user_id' ) );
        $this->okWrite( 'expNotificationServices', 'unsubscribeUser', array( 'user' => $u, 'node' => 43 ) );
        $this->assertCount( 0, $this->call( 'expNotificationServices', 'subscriptionsOf', array( (string)$u ) )['data'] );
    }

    public function testNotificationNeedsThePolicy()
    {
        $this->loginAs( $this->memberUser() );
        $this->assertError( $this->call( 'expNotificationServices', 'queue' ), 403 );
        $this->loginAnonymous();
        $this->assertError( $this->call( 'expNotificationServices', 'settings' ), 401 );
    }
}
