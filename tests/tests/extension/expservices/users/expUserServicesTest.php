<?php
/**
 * expuser: lookups, lists, search, account administration and the access checks.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expUsersTestCase.php';

class expUserServicesTest extends expUsersTestCase
{
    public function testCurrentIsAdmin()
    {
        $d = $this->okCall( 'expUserServices', 'current' );
        $this->assertSame( 'admin', $d['login'] );
        $this->assertTrue( $d['is_registered'] );
        $this->assertNotEmpty( $d['groups'] );
    }

    public function testCurrentAnonymousHasNoPrivateData()
    {
        $this->loginAnonymous();
        $d = $this->okCall( 'expUserServices', 'current' );
        $this->assertFalse( $d['is_registered'] );
        $this->assertTrue( $d['is_anonymous'] );
    }

    public function testFetchNeverLeaksHashes()
    {
        $admin = eZUser::fetchByName( 'admin' );
        $d = $this->okCall( 'expUserServices', 'fetch', array( (string)$admin->attribute( 'contentobject_id' ) ) );
        $json = json_encode( $d );
        $this->assertStringNotContainsString( 'password', $json );
        $this->assertStringNotContainsString( $admin->attribute( 'password_hash' ), $json );
        $this->assertSame( 'admin', $d['login'] );
    }

    public function testFetchUnknownIs404()
    {
        $this->assertError( $this->call( 'expUserServices', 'fetch', array( '99999999' ) ), 404 );
    }

    public function testFetchNeedsAnInteger()
    {
        $this->assertError( $this->call( 'expUserServices', 'fetch', array( 'abc' ) ), 400 );
    }

    public function testAnonymousCannotFetch()
    {
        $this->loginAnonymous();
        $this->assertError( $this->call( 'expUserServices', 'fetch', array( '14' ) ), 401 );
    }

    public function testByLoginAndEmail()
    {
        $d = $this->okCall( 'expUserServices', 'byLogin', array( 'admin' ) );
        $this->assertSame( 'admin', $d['login'] );
        $email = eZUser::fetchByName( 'admin' )->attribute( 'email' );
        $this->assertSame( 'admin', $this->okCall( 'expUserServices', 'byEmail', array( $email ) )['login'] );
        $this->assertError( $this->call( 'expUserServices', 'byLogin', array( 'no-such-login-exptest' ) ), 404 );
    }

    public function testExists()
    {
        $this->assertTrue( $this->okCall( 'expUserServices', 'exists', array( 'admin' ) )['exists'] );
        $this->assertFalse( $this->okCall( 'expUserServices', 'exists', array( 'no-such-login-exptest' ) )['exists'] );
    }

    public function testSearch()
    {
        $r = $this->call( 'expUserServices', 'search', array( 'admin' ) );
        $this->assertPaged( $r );
        $this->assertGreaterThanOrEqual( 1, $this->meta( $r )['total'] );
        $this->assertError( $this->call( 'expUserServices', 'search', array( 'a' ) ), 400 );
    }

    public function testListAllIsPaged()
    {
        $r = $this->call( 'expUserServices', 'listAll', array( '1', '0' ) );
        $this->assertPaged( $r );
        $this->assertSame( 1, $this->meta( $r )['count'] );
        $this->assertSame( 1, $this->meta( $r )['limit'] );
    }

    public function testListAllRejectsBadPaging()
    {
        $this->assertError( $this->call( 'expUserServices', 'listAll', array( '0', '0' ) ), 400 );
    }

    public function testCountMatchesList()
    {
        $count = $this->okCall( 'expUserServices', 'count' )['count'];
        $this->assertSame( $count, $this->meta( $this->call( 'expUserServices', 'listAll', array( '1' ) ) )['total'] );
    }

    public function testCreateFetchAndListByGroup()
    {
        $group = $this->fixtureGroup();
        $id = $this->newUser( $group, null, array() );
        $d = $this->okCall( 'expUserServices', 'fetch', array( (string)$id ) );
        $this->assertTrue( $d['is_enabled'] );
        $this->assertContains( (int)eZContentObjectTreeNode::fetch( $group )->attribute( 'contentobject_id' ), $d['groups'] );
        $r = $this->call( 'expUserServices', 'listByGroup', array( (string)$group ) );
        $this->assertPaged( $r );
        $ids = array_column( $r['data'], 'id' );
        $this->assertContains( $id, $ids );
    }

    public function testCreatedUserCanAuthenticate()
    {
        $id = $this->newUser();
        $user = eZUser::fetch( $id );
        $this->assertTrue( (bool)eZUser::authenticateHash( $user->attribute( 'login' ), self::PASSWORD, $user->site(),
                                                           $user->attribute( 'password_hash_type' ), $user->attribute( 'password_hash' ) ) );
    }

    public function testCreateDuplicateLoginIs409()
    {
        $login = self::uniq();
        $this->newUser( null, $login );
        $r = $this->write( 'expUserServices', 'create', array( 'group' => $this->fixtureGroup(), 'login' => $login, 'email' => 'other' . $login . '@example.com', 'password' => self::PASSWORD ) );
        $this->assertError( $r, 409 );
    }

    public function testCreateInvalidEmailAndShortPassword()
    {
        $g = $this->fixtureGroup();
        $this->assertError( $this->write( 'expUserServices', 'create', array( 'group' => $g, 'login' => self::uniq(), 'email' => 'not-an-email', 'password' => self::PASSWORD ) ), 422 );
        $this->assertError( $this->write( 'expUserServices', 'create', array( 'group' => $g, 'login' => self::uniq(), 'email' => 'a@example.com', 'password' => 'x' ) ), 422 );
    }

    public function testCreateInANonGroupIs404()
    {
        $r = $this->write( 'expUserServices', 'create', array( 'group' => 2, 'login' => self::uniq(), 'email' => 'a@example.com', 'password' => self::PASSWORD ) );
        $this->assertError( $r, 404 );
    }

    public function testWritesNeedPost()
    {
        $r = expServiceBase::invoke( 'expUserServices', 'create', array() );
        $this->assertError( $r, 403 );
    }

    public function testAnonymousCannotCreate()
    {
        $this->loginAnonymous();
        $this->assertError( $this->write( 'expUserServices', 'create', array( 'group' => 12, 'login' => 'x', 'email' => 'a@example.com', 'password' => self::PASSWORD ) ), 401 );
    }

    public function testDisableAndEnable()
    {
        $id = $this->newUser();
        $this->assertFalse( $this->okWrite( 'expUserServices', 'disable', array( 'id' => $id ) )['enabled'] );
        $this->assertFalse( $this->okCall( 'expUserServices', 'fetch', array( (string)$id ) )['is_enabled'] );
        $this->assertTrue( $this->okWrite( 'expUserServices', 'enable', array( 'id' => $id ) )['enabled'] );
        $this->assertTrue( $this->okCall( 'expUserServices', 'fetch', array( (string)$id ) )['is_enabled'] );
    }

    public function testCannotDisableOrRemoveSelf()
    {
        $me = (int)eZUser::currentUserID();
        $this->assertError( $this->write( 'expUserServices', 'disable', array( 'id' => $me ) ), 409 );
        $this->assertError( $this->write( 'expUserServices', 'remove', array( 'id' => $me ) ), 409 );
    }

    public function testSetPasswordChangesTheHash()
    {
        $id = $this->newUser();
        $this->okWrite( 'expUserServices', 'setPassword', array( 'id' => $id, 'password' => 'Another#Pass99' ) );
        $user = eZUser::fetch( $id );
        $this->assertTrue( (bool)eZUser::authenticateHash( $user->attribute( 'login' ), 'Another#Pass99', $user->site(), $user->attribute( 'password_hash_type' ), $user->attribute( 'password_hash' ) ) );
        $this->assertError( $this->write( 'expUserServices', 'setPassword', array( 'id' => $id, 'password' => 'x' ) ), 422 );
    }

    public function testChangeEmailAndLogin()
    {
        $id = $this->newUser();
        $old = $this->storedEmail( $id );
        $login = self::uniq();
        try
        {
            $this->assertSame( $login, $this->okWrite( 'expUserServices', 'changeLogin', array( 'id' => $id, 'login' => $login ) )['login'] );

            // an address the admin sets waits for its confirmation from the new mailbox like the person's own
            // change (mailpreferences.ini [EmailChangeSettings] Confirm=enabled, the default)
            $this->setIni( 'mailpreferences.ini', 'EmailChangeSettings', 'Confirm', 'enabled' );
            $this->assertTrue( expMailAddressChange::confirmationRequired() );
            $new = $login . '@new.example.com';
            $d = $this->okWrite( 'expUserServices', 'changeEmail', array( 'id' => $id, 'email' => $new ) );
            $this->assertSame( $id, $d['id'] );
            $this->assertSame( $old, $d['email'], 'the answer names the address the account still has' );
            $this->assertTrue( $d['pending'] );
            $this->assertSame( $old, $this->storedEmail( $id ), 'the account keeps its address until the change is confirmed' );
            $confirmed = expMailPreferencesService::confirm( $this->confirmationTokenFor( $new ), new expConsentContext( 'confirm', 'expservices test', '192.0.2.31', 0, 'admin' ) );
            $this->assertSame( 'confirmed', $confirmed['result'] );
            $this->assertSame( $new, $this->storedEmail( $id ) );

            // the old behaviour, by setting: the address changes at once
            $this->setIni( 'mailpreferences.ini', 'EmailChangeSettings', 'Confirm', 'disabled' );
            $direct = $login . '@direct.example.com';
            $d = $this->okWrite( 'expUserServices', 'changeEmail', array( 'id' => $id, 'email' => $direct ) );
            $this->assertSame( $direct, $d['email'] );
            $this->assertArrayNotHasKey( 'pending', $d );
            $this->assertSame( $direct, $this->storedEmail( $id ) );

            $this->assertSame( $login, eZUser::fetch( $id )->attribute( 'login' ) );
            $this->assertError( $this->write( 'expUserServices', 'changeLogin', array( 'id' => $id, 'login' => 'admin' ) ), 409 );
        }
        finally
        {
            $this->forgetMailRecordsOf( $id );
        }
    }

    public function testLoginInfoAndSettings()
    {
        $id = $this->newUser();
        $info = $this->okCall( 'expUserServices', 'loginInfo', array( (string)$id ) );
        $this->assertSame( 0, $info['failed_login_attempts'] );
        $this->assertFalse( $info['is_locked'] );
        $this->okWrite( 'expUserServices', 'setMaxLogin', array( 'id' => $id, 'max_login' => 3 ) );
        $this->assertSame( 3, $this->okCall( 'expUserServices', 'settings', array( (string)$id ) )['max_login'] );
        $this->assertSame( 0, $this->okWrite( 'expUserServices', 'unlock', array( 'id' => $id ) )['failed_login_attempts'] );
        $this->assertArrayHasKey( 'last_visit', $this->okCall( 'expUserServices', 'lastVisit', array( (string)$id ) ) );
    }

    public function testProfileReadAndUpdate()
    {
        $id = $this->newUser();
        $p = $this->okCall( 'expUserServices', 'profile', array( (string)$id ) );
        $this->assertArrayHasKey( 'first_name', (array)$p['fields'] );
        $this->assertArrayNotHasKey( 'user_account', (array)$p['fields'] );
        $this->okWrite( 'expUserServices', 'updateProfile', array( 'id' => $id, 'fields' => json_encode( array( 'first_name' => 'Changed' ) ) ) );
        $this->assertSame( 'Changed', $this->okCall( 'expUserServices', 'profile', array( (string)$id ) )['fields']->first_name['value'] );
        $this->assertError( $this->write( 'expUserServices', 'updateProfile', array( 'id' => $id, 'fields' => json_encode( array( 'user_account' => 'x' ) ) ) ), 422 );
        $this->assertError( $this->write( 'expUserServices', 'updateProfile', array( 'id' => $id, 'fields' => json_encode( array( 'nope' => 'x' ) ) ) ), 422 );
    }

    public function testRemoveGoesToTrash()
    {
        $id = $this->newUser();
        $d = $this->okWrite( 'expUserServices', 'remove', array( 'id' => $id ) );
        $this->assertTrue( $d['to_trash'] );
        $this->assertError( $this->call( 'expUserServices', 'fetch', array( (string)$id ) ), 404 );
    }

    public function testValidationHelpers()
    {
        $this->assertFalse( $this->okCall( 'expUserServices', 'validatePassword', array( 'x' ) )['valid'] );
        $this->assertTrue( $this->okCall( 'expUserServices', 'validatePassword', array( self::PASSWORD ) )['valid'] );
        $this->assertTrue( $this->okCall( 'expUserServices', 'validateLogin', array( 'admin' ) )['taken'] );
        $p = $this->okCall( 'expUserServices', 'passwordPolicy' );
        $this->assertGreaterThan( 0, $p['min_length'] );
        $this->assertSame( 16, strlen( $this->okCall( 'expUserServices', 'generatePassword', array( '16' ) )['password'] ) );
        $this->assertError( $this->call( 'expUserServices', 'generatePassword', array( '2' ) ), 400 );
    }

    public function testRolesGroupsAndPoliciesOfAdmin()
    {
        $id = (string)eZUser::currentUserID();
        $roles = $this->okCall( 'expUserServices', 'rolesOf', array( $id ) );
        $this->assertNotEmpty( $roles );
        $groups = $this->okCall( 'expUserServices', 'groupsOf', array( $id ) );
        $this->assertNotEmpty( $groups );
        $r = $this->call( 'expUserServices', 'effectivePolicies', array( $id, '5' ) );
        $this->assertPaged( $r );
        $this->assertLessThanOrEqual( 5, count( $r['data'] ) );
        $this->assertArrayHasKey( 'assignments', $this->okCall( 'expUserServices', 'limitations', array( $id ) ) );
    }

    public function testHasAccess()
    {
        $this->assertTrue( $this->okCall( 'expUserServices', 'hasAccess', array( 'content', 'read' ) )['allowed'] );
        $this->assertSame( 'yes', $this->okCall( 'expUserServices', 'hasAccess', array( 'setup', 'administrate' ) )['access'] );
        $this->loginAnonymous();
        $this->assertFalse( $this->okCall( 'expUserServices', 'hasAccess', array( 'nomodule', 'nofunction' ) )['allowed'] );
    }

    public function testHasAccessOfAndCanNode()
    {
        $id = (string)eZUser::currentUserID();
        $this->assertTrue( $this->okCall( 'expUserServices', 'hasAccessOf', array( $id, 'content', 'edit' ) )['allowed'] );
        $n = $this->okCall( 'expUserServices', 'canNode', array( '2' ) );
        $this->assertTrue( $n['read'] );
        $this->assertTrue( $n['edit'] );
        $this->assertError( $this->call( 'expUserServices', 'canNode', array( '99999999' ) ), 404 );
    }

    public function testAccessSummaryAndClasses()
    {
        $this->assertNotEmpty( (array)$this->okCall( 'expUserServices', 'accessSummary' ) );
        $c = $this->okCall( 'expUserServices', 'userClasses' );
        $this->assertContains( 'user', $c['user'] );
        $this->assertContains( 'user_group', $c['group'] ?: array( 'user_group' ) );
        $this->assertGreaterThan( 0, $this->okCall( 'expUserServices', 'anonymous' )['id'] );
        $this->assertNotEmpty( $this->okCall( 'expUserServices', 'hashTypes' )['default'] );
    }

    public function testLoggedInCount()
    {
        $d = $this->okCall( 'expUserServices', 'loggedInCount' );
        $this->assertArrayHasKey( 'registered', $d );
        $this->assertArrayHasKey( 'anonymous', $d );
        $this->assertPaged( $this->call( 'expUserServices', 'loggedIn', array( '5' ) ) );
        $this->assertFalse( $this->okCall( 'expUserServices', 'isOnline', array( (string)$this->newUser() ) )['online'] );
    }

    public function testOtherUsersPrivateDataStaysHidden()
    {
        $member = $this->memberUser();
        $other = $this->newUser();
        $this->loginAs( $member );
        $d = $this->okCall( 'expUserServices', 'fetch', array( (string)$other ) );
        $this->assertArrayNotHasKey( 'email', $d );
        $this->assertArrayNotHasKey( 'login', $d );
        $this->assertError( $this->call( 'expUserServices', 'loginInfo', array( (string)$other ) ), 403 );
        $this->assertError( $this->call( 'expUserServices', 'byEmail', array( eZUser::fetch( $other )->attribute( 'email' ) ) ), 404 );
        $own = $this->okCall( 'expUserServices', 'fetch', array( (string)$member ) );
        $this->assertArrayHasKey( 'email', $own );
    }

    public function testRoleQueriesNeedThePolicy()
    {
        $this->loginAs( $this->memberUser() );
        $this->assertError( $this->call( 'expUserServices', 'rolesOf', array( '14' ) ), 403 );
    }
}
