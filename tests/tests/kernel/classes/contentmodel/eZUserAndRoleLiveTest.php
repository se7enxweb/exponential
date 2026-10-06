<?php
/**
 * Users and roles of a throwaway user group: a user made from its text form (login|email|hash|hash type|enabled),
 * the lookups by login, e-mail and id, the groups of a user, signing in with the right and a wrong password
 * (failed attempts counted and reset), a disabled account, the text form read back, and a role with policies
 * assigned to the group (with and without a subtree limitation): the role list and access array of the user,
 * hasAccessTo(), copying a role, removing the assignment and the role.
 *
 * The login and e-mail are made for each run (k1c..., ...@k1c.example.invalid); everything is removed again.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/expContentModelLiveTestCase.php';

class eZUserAndRoleLiveTest extends expContentModelLiveTestCase
{
    protected static $group;
    protected static $user;
    protected static $login;
    protected static $email;
    protected static $password;

    /** @var int[] roles to remove */
    protected static $roles = array();

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        foreach ( array( 'user', 'user_group' ) as $identifier )
        {
            if ( !eZContentClass::fetchByIdentifier( $identifier ) )
                self::markTestSkipped( "needs the content class '$identifier'" );
        }
        static::$roles = array();
        $suffix = substr( md5( uniqid( '', true ) ), 0, 10 );
        static::$login = 'k1c' . $suffix;
        static::$email = 'k1c-' . $suffix . '@k1c.example.invalid';
        static::$password = 'K1c-pass-' . $suffix;
        static::$group = static::createObject( 'user_group', static::$root['node'], array( 'name' => 'k1c group ' . $suffix ) );
        $hash = eZUser::createHash( static::$login, static::$password, eZUser::site(), eZUser::hashType() );
        static::$user = static::createObject( 'user', static::$group['node'], array(
            'first_name' => 'K1c', 'last_name' => 'Tester',
            'user_account' => static::$login . '|' . static::$email . '|' . $hash . '|' . eZUser::passwordHashTypeName( eZUser::hashType() ) . '|1' ) );
    }

    public static function tearDownAfterClass(): void
    {
        foreach ( static::$roles as $roleID )
        {
            if ( eZRole::fetch( $roleID ) )
                eZRole::removeRole( $roleID );
        }
        static::$roles = array();
        if ( static::$user )
        {
            eZUser::removeSessionData( static::$user['object'] );
            eZUser::purgeUserCacheByUserId( static::$user['object'] );
        }
        parent::tearDownAfterClass();
        if ( static::$login && eZUser::fetchByName( static::$login ) )
            throw new RuntimeException( 'the test user is still there' );
    }

    private function user()
    {
        $user = eZUser::fetch( static::$user['object'] );
        $this->assertInstanceOf( 'eZUser', $user );
        return $user;
    }

    private function newRole( $name )
    {
        $role = eZRole::create( 'k1c ' . $name . ' ' . uniqid() );
        $role->store();
        static::$roles[] = (int)$role->attribute( 'id' );
        return $role;
    }

    // ---------------------------------------------------------------- user

    public function testUserFromItsTextForm()
    {
        $user = $this->user();
        $this->assertSame( static::$login, $user->attribute( 'login' ) );
        $this->assertSame( static::$email, $user->attribute( 'email' ) );
        $this->assertSame( static::$user['object'], (int)eZUser::fetchByName( static::$login )->attribute( 'contentobject_id' ) );
        $this->assertSame( static::$user['object'], (int)eZUser::fetchByEmail( static::$email )->attribute( 'contentobject_id' ) );
        $this->assertNull( eZUser::fetchByName( 'k1c-nobody-' . uniqid() ) );
        $this->assertTrue( $user->isEnabled() );
        $this->assertTrue( eZUser::isUserObject( eZContentObject::fetch( static::$user['object'] ) ) );
        $this->assertFalse( eZUser::isUserObject( eZContentObject::fetch( static::$group['object'] ) ) );
        $this->assertSame( 'K1c Tester', eZContentObject::fetch( static::$user['object'] )->name() );

        $text = eZContentObject::fetch( static::$user['object'] )->dataMap()['user_account']->toString();
        $parts = explode( '|', $text );
        $this->assertSame( static::$login, $parts[0] );
        $this->assertSame( static::$email, $parts[1] );
        $this->assertSame( $user->attribute( 'password_hash' ), $parts[2] );
        $this->assertSame( '1', (string)$parts[4] );
    }

    public function testGroupsOfTheUser()
    {
        $user = $this->user();
        $groups = array_map( 'intval', $user->groups() );
        $this->assertContains( static::$group['object'], $groups );
        $groupObjects = $user->groups( true );
        $names = array();
        foreach ( $groupObjects as $group )
            $names[] = $group->attribute( 'name' );
        $this->assertContains( eZContentObject::fetch( static::$group['object'] )->name(), $names );
    }

    public function testSignInWithTheRightAndAWrongPassword()
    {
        eZUser::setFailedLoginAttempts( static::$user['object'], 0, true );
        $wrong = eZUser::loginUser( static::$login, 'not the password' );
        $this->assertFalse( $wrong );
        $counted = eZUser::maxNumberOfFailedLogin() != 0 && !eZUser::isTrusted();
        $this->assertSame( $counted ? 1 : 0, (int)eZUser::failedLoginAttemptsByUserID( static::$user['object'] ), 'counted where the site limits failed sign ins' );
        eZUser::setFailedLoginAttempts( static::$user['object'], 2, true );
        $this->assertSame( 2, (int)eZUser::failedLoginAttemptsByUserID( static::$user['object'] ) );
        $wrong = eZUser::loginUser( static::$email, 'still not it', eZUser::AUTHENTICATE_EMAIL );
        $this->assertFalse( $wrong );

        $user = eZUser::loginUser( static::$login, static::$password );
        $this->assertInstanceOf( 'eZUser', $user );
        $this->assertSame( static::$user['object'], (int)$user->attribute( 'contentobject_id' ) );
        $this->assertSame( $counted ? 0 : 2, (int)eZUser::failedLoginAttemptsByUserID( static::$user['object'] ), 'a successful sign in resets the count where it is kept' );
        eZUser::setFailedLoginAttempts( static::$user['object'], 0, true );
        $this->assertSame( static::$user['object'], (int)eZUser::currentUserID() );
        static::loginAdmin();
    }

    public function testDisabledAccountCannotSignIn()
    {
        eZUserType::storeIsEnabled( static::$user['object'], 0 );
        eZUser::purgeUserCacheByUserId( static::$user['object'] );
        try
        {
            $this->assertFalse( $this->user()->isEnabled() );
            $this->assertFalse( eZUser::loginUser( static::$login, static::$password ) );
        }
        finally
        {
            eZUserType::storeIsEnabled( static::$user['object'], 1 );
            eZUser::purgeUserCacheByUserId( static::$user['object'] );
            static::loginAdmin();
        }
        $this->assertTrue( $this->user()->isEnabled() );
    }

    public function testLoginNameValidationFollowsTheSettings()
    {
        $error = '';
        $regexList = eZINI::instance()->variable( 'UserSettings', 'UserNameValidationRegex' );
        $this->assertTrue( eZUser::validateLoginName( 'k1c_good_login', $error ) );
        foreach ( (array)$regexList as $regex )
        {
            // a name the rule matches is refused with a message
            if ( preg_match( $regex, 'bad login!' ) )
            {
                $this->assertFalse( eZUser::validateLoginName( 'bad login!', $error ) );
                $this->assertNotSame( '', $error );
            }
        }
    }

    public function testCreatedPasswordsAndHashes()
    {
        $password = eZUser::createPassword( 12 );
        $this->assertSame( 12, strlen( $password ) );
        $this->assertNotSame( $password, eZUser::createPassword( 12 ) );
        $user = $this->user();
        $this->assertTrue( eZUser::authenticateHash( static::$login, static::$password, eZUser::site(), $user->attribute( 'password_hash_type' ), $user->attribute( 'password_hash' ) ) );
        $this->assertFalse( eZUser::authenticateHash( static::$login, 'wrong', eZUser::site(), $user->attribute( 'password_hash_type' ), $user->attribute( 'password_hash' ) ) );
    }

    // ---------------------------------------------------------------- roles

    public function testRoleWithPoliciesAssignedToTheGroup()
    {
        $role = $this->newRole( 'reader' );
        $role->appendPolicy( 'content', 'read', array( 'Section' => array( 1 ) ) );
        $role->appendPolicy( 'k1c_module', '*' );
        $role->store();
        $roleID = (int)$role->attribute( 'id' );

        $role = eZRole::fetch( $roleID );
        $this->assertSame( 2, (int)$role->policyCount() );
        $this->assertTrue( (bool)$role->hasPolicy( 'content', 'read' ) );
        $this->assertTrue( (bool)$role->hasPolicy( 'k1c_module' ) );
        $this->assertFalse( (bool)$role->hasPolicy( 'content', 'edit' ) );
        $access = $role->accessArray();
        $this->assertArrayHasKey( 'content', $access );
        $this->assertArrayHasKey( 'read', $access['content'] );

        $role->assignToUser( static::$group['object'] );
        eZRole::expireCache();
        eZUser::purgeUserCacheByUserId( static::$user['object'] );
        $this->assertContains( $roleID, array_map( 'intval', eZRole::fetchIDListByUser( array( static::$group['object'] ) ) ) );
        $byUser = eZRole::fetchByUser( array( static::$group['object'] ) );
        $this->assertContains( $roleID, array_map( function ( $r ) { return (int)$r->attribute( 'id' ); }, $byUser ) );
        $this->assertContains( static::$group['object'], array_map( 'intval', array_column( $role->fetchUserID(), 'contentobject_id' ) ) );

        $user = $this->user();
        $this->assertContains( $roleID, array_map( 'intval', $user->roleIDList() ) );
        $access = $user->hasAccessTo( 'k1c_module', 'anything' );
        $this->assertSame( 'yes', $access['accessWord'], 'a policy for every function of the module' );

        // a second assignment, limited to a subtree
        $subtree = eZContentObjectTreeNode::fetch( static::$root['node'] )->attribute( 'path_string' );
        $role->assignToUser( static::$group['object'], 'subtree', static::$root['node'] );
        $rows = eZDB::instance()->arrayQuery( 'SELECT limit_identifier, limit_value FROM ezuser_role WHERE role_id=' . $roleID . ' ORDER BY id' );
        $this->assertSame( array( '', '' ), array( (string)$rows[0]['limit_identifier'], (string)$rows[0]['limit_value'] ) );
        $this->assertSame( array( 'Subtree', $subtree ), array( $rows[1]['limit_identifier'], $rows[1]['limit_value'] ) );

        $role->removeUserAssignment( static::$group['object'] );
        eZRole::expireCache();
        eZUser::purgeUserCacheByUserId( static::$user['object'] );
        $this->assertNotContains( $roleID, array_map( 'intval', eZRole::fetchIDListByUser( array( static::$group['object'] ) ) ) );
        $this->assertSame( array(), eZDB::instance()->arrayQuery( 'SELECT id FROM ezuser_role WHERE role_id=' . $roleID ) );
    }

    public function testRoleCopyAndRemoval()
    {
        $role = $this->newRole( 'original' );
        $role->appendPolicy( 'content', 'read' );
        $role->appendPolicy( 'content', 'versionread' );
        $role->store();
        $copy = $role->copy();
        static::$roles[] = (int)$copy->attribute( 'id' );
        $this->assertNotSame( (int)$role->attribute( 'id' ), (int)$copy->attribute( 'id' ) );
        $this->assertSame( 2, (int)eZRole::fetch( $copy->attribute( 'id' ) )->policyCount() );
        $this->assertStringContainsString( $role->attribute( 'name' ), $copy->attribute( 'name' ) );

        $copyID = (int)$copy->attribute( 'id' );
        $copy->removePolicy( 'content', 'read' );
        $this->assertSame( 1, (int)eZRole::fetch( $copyID )->policyCount() );
        eZRole::removeRole( $copyID );
        $this->assertNull( eZRole::fetch( $copyID ) );
        $this->assertSame( array(), eZDB::instance()->arrayQuery( 'SELECT id FROM ezpolicy WHERE role_id=' . $copyID ) );
        $this->assertSame( (int)$role->attribute( 'id' ), (int)eZRole::fetchByName( $role->attribute( 'name' ) )->attribute( 'id' ) );
    }
}
