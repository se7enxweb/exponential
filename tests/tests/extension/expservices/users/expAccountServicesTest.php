<?php
/**
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expUsersTestCase.php';

class expAccountServicesTest extends expUsersTestCase
{
    public function testRegistrationOptionsArePublic()
    {
        $this->loginAnonymous();
        $d = $this->okCall( 'expAccountServices', 'registrationOptions' );
        $this->assertArrayHasKey( 'allowed', $d );
        $this->assertGreaterThan( 0, $d['min_password_length'] );
    }

    public function testRegisterIsRefusedWhenSignedIn()
    {
        $this->assertError( $this->write( 'expAccountServices', 'register', array( 'login' => 'x', 'email' => 'a@example.com', 'password' => self::PASSWORD ) ), 409 );
    }

    public function testRegisterValidatesBeforeCreating()
    {
        $this->loginAnonymous();
        $r = $this->write( 'expAccountServices', 'register', array( 'login' => 'admin', 'email' => 'new@example.com', 'password' => self::PASSWORD ) );
        $this->assertFalse( $r['ok'] );
        $this->assertContains( $r['error']['code'], array( 401, 403, 409 ) );
    }

    public function testChangePassword()
    {
        $u = $this->newUser();
        $this->loginAs( $u );
        $this->assertError( $this->write( 'expAccountServices', 'changePassword', array( 'old_password' => 'wrong', 'new_password' => 'Brand#New123' ) ), 403 );
        $this->assertError( $this->write( 'expAccountServices', 'changePassword', array( 'old_password' => self::PASSWORD, 'new_password' => 'Brand#New123', 'confirm_password' => 'different' ) ), 422 );
        $this->assertError( $this->write( 'expAccountServices', 'changePassword', array( 'old_password' => self::PASSWORD, 'new_password' => 'x' ) ), 422 );
        $this->okWrite( 'expAccountServices', 'changePassword', array( 'old_password' => self::PASSWORD, 'new_password' => 'Brand#New123' ) );
        $user = eZUser::fetch( $u );
        $this->assertTrue( (bool)eZUser::authenticateHash( $user->attribute( 'login' ), 'Brand#New123', $user->site(), $user->attribute( 'password_hash_type' ), $user->attribute( 'password_hash' ) ) );
    }

    public function testChangeEmailNeedsThePassword()
    {
        $u = $this->newUser();
        $this->loginAs( $u );
        $this->assertError( $this->write( 'expAccountServices', 'changeEmail', array( 'password' => 'wrong', 'email' => 'x' . self::uniq() . '@example.com' ) ), 403 );
        $mail = 'changed' . self::uniq() . '@example.com';
        $this->assertSame( $mail, $this->okWrite( 'expAccountServices', 'changeEmail', array( 'password' => self::PASSWORD, 'email' => $mail ) )['email'] );
        $this->assertError( $this->write( 'expAccountServices', 'changeEmail', array( 'password' => self::PASSWORD, 'email' => 'bad' ) ), 422 );
    }

    public function testSelfReads()
    {
        $this->loginAs( $this->memberUser() );
        $me = $this->okCall( 'expAccountServices', 'me' );
        $this->assertSame( $this->memberUser(), $me['id'] );
        $this->assertNotEmpty( $this->okCall( 'expAccountServices', 'myGroups' ) );
        $this->assertArrayHasKey( 'login_count', $this->okCall( 'expAccountServices', 'myLoginInfo' ) );
        $this->assertIsArray( $this->okCall( 'expAccountServices', 'myRoles' ) );
    }

    public function testUpdateMyProfile()
    {
        $this->loginAs( $this->memberUser() );
        $this->okWrite( 'expAccountServices', 'updateMyProfile', array( 'fields' => json_encode( array( 'first_name' => 'Self' ) ) ) );
        $this->assertError( $this->write( 'expAccountServices', 'updateMyProfile', array( 'fields' => json_encode( array( 'user_account' => 'x' ) ) ) ), 422 );
    }

    public function testForgotRequestAnswersTheSameForUnknownAddresses()
    {
        $this->loginAnonymous();
        $d = $this->okWrite( 'expAccountServices', 'forgotRequest', array( 'email' => 'nobody' . self::uniq() . '@example.com' ) );
        $this->assertTrue( $d['requested'] );
        $this->assertError( $this->write( 'expAccountServices', 'forgotRequest', array( 'email' => 'not an address' ) ), 422 );
    }

    public function testResetWithAKey()
    {
        $u = $this->newUser();
        $key = md5( 'exptest' . $u . microtime() );
        eZUserOperationCollection::forgotpassword( $u, $key, time() );
        $this->loginAnonymous();
        $this->assertTrue( $this->okCall( 'expAccountServices', 'forgotKeyValid', array( $key ) )['valid'] );
        $this->assertError( $this->write( 'expAccountServices', 'reset', array( 'key' => $key, 'password' => 'x' ) ), 422 );
        $this->okWrite( 'expAccountServices', 'reset', array( 'key' => $key, 'password' => 'Reset#Pass456' ) );
        $user = eZUser::fetch( $u );
        $this->assertTrue( (bool)eZUser::authenticateHash( $user->attribute( 'login' ), 'Reset#Pass456', $user->site(), $user->attribute( 'password_hash_type' ), $user->attribute( 'password_hash' ) ) );
        $this->assertFalse( $this->okCall( 'expAccountServices', 'forgotKeyValid', array( $key ) )['valid'] );
        $this->assertError( $this->write( 'expAccountServices', 'reset', array( 'key' => $key, 'password' => 'Reset#Pass456' ) ), 404 );
    }

    public function testExpiredAndMalformedKeysAreRefused()
    {
        $u = $this->newUser();
        $key = md5( 'old' . $u . microtime() );
        eZUserOperationCollection::forgotpassword( $u, $key, time() - 200000 );
        $this->assertFalse( $this->okCall( 'expAccountServices', 'forgotKeyValid', array( $key ) )['valid'] );
        $this->assertError( $this->write( 'expAccountServices', 'reset', array( 'key' => $key, 'password' => 'Reset#Pass456' ) ), 404 );
        $this->assertError( $this->write( 'expAccountServices', 'reset', array( 'key' => "' OR 1=1", 'password' => 'Reset#Pass456' ) ), 404 );
        eZForgotPassword::removeByUserID( $u );
    }

    public function testActivationFlow()
    {
        $u = $this->newUser( null, null, array( 'enabled' => '0' ) );
        $this->assertFalse( $this->okCall( 'expUserServices', 'fetch', array( (string)$u ) )['is_enabled'] );
        $key = md5( 'act' . $u . microtime() );
        $k = eZUserAccountKey::createNew( $u, $key, time() );
        $k->store();
        $this->assertSame( 1, $this->okCall( 'expAccountServices', 'unactivatedCount' )['count'] >= 1 ? 1 : 0 );
        $this->assertContains( $u, array_column( $this->call( 'expAccountServices', 'unactivated', array( '200' ) )['data'], 'id' ) );
        $this->loginAnonymous();
        $this->assertTrue( $this->okCall( 'expAccountServices', 'activationKeyValid', array( $key ) )['valid'] );
        $this->okWrite( 'expAccountServices', 'activate', array( 'key' => $key ) );
        $this->assertTrue( eZUser::fetch( $u )->isEnabled( false ) );
        $this->assertError( $this->write( 'expAccountServices', 'activate', array( 'key' => $key ) ), 404 );
    }

    public function testAdminActivateAndCancelReset()
    {
        $u = $this->newUser( null, null, array( 'enabled' => '0' ) );
        $this->okWrite( 'expAccountServices', 'activateUser', array( 'id' => $u ) );
        $this->assertTrue( eZUser::fetch( $u )->isEnabled( false ) );
        eZUserOperationCollection::forgotpassword( $u, md5( 'c' . $u . microtime() ), time() );
        $this->assertGreaterThanOrEqual( 1, $this->okCall( 'expAccountServices', 'pendingResets' )['count'] );
        $this->assertTrue( $this->okWrite( 'expAccountServices', 'cancelReset', array( 'id' => $u ) )['cancelled'] );
    }

    public function testAccountAdminNeedsThePolicy()
    {
        $this->loginAs( $this->memberUser() );
        $this->assertError( $this->call( 'expAccountServices', 'unactivated' ), 403 );
        $this->assertError( $this->write( 'expAccountServices', 'activateUser', array( 'id' => 14 ) ), 403 );
    }
}
