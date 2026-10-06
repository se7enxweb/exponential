<?php
/**
 * Access checks for a user other than the current one ($userID of checkAccess()), and a content limitation of an
 * extension from the role to the fetch, on the installation the tests run on. Live style: no test database. Each
 * test creates its own user (never published, address at x1.example.invalid) and its own role, and removes them
 * again; where there is no installation (CI) the tests are skipped.
 *
 *  UA-01 - An object and a node are checked for the user given, not for the current user
 *  UA-02 - A version is checked for the user given
 *  UA-03 - A user ID that is no user gets no access, and the current user stays the same
 *  UA-04 - A read policy with an extension limitation without handler shows nothing, in checkAccess() and in fetches
 *  UA-05 - With a handler the same policy shows what the handler allows, in checkAccess() and in fetches
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

/** Allows every object, in PHP and in SQL */
class X1LiveContentLimitationHandler implements ezpContentLimitationHandler
{
    public function checkAccess( $limitation, array $values, $functionName, $subject, $userID )
    {
        return in_array( 1, $values );
    }

    public function permissionSQL( $limitation, array $values, $tableAliasName, $userID )
    {
        return in_array( 1, $values ) ? '1 = 1' : false;
    }
}

class eZContentAccessForUserLiveTest extends PHPUnit\Framework\TestCase
{
    const ADDRESS_DOMAIN = 'x1.example.invalid';
    const ADMIN_ID = 14;

    private static $installation;
    private $previousUser;
    private $objects = array();
    private $roles = array();
    private $userIDs = array();

    public static function setUpBeforeClass(): void
    {
        self::$installation = dirname( __DIR__, 5 );
        ezpLiveInstallation::requireOrSkip();
    }

    protected function setUp(): void
    {
        chdir( self::$installation );
        $this->previousUser = eZUser::currentUser();
        eZUser::setCurrentlyLoggedInUser( eZUser::fetch( self::ADMIN_ID ), self::ADMIN_ID, eZUser::NO_SESSION_REGENERATE );
    }

    protected function tearDown(): void
    {
        if ( $this->previousUser instanceof eZUser )
        {
            eZUser::setCurrentlyLoggedInUser( $this->previousUser, $this->previousUser->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
        }
        foreach ( $this->roles as $role )
        {
            $role->removeThis();
        }
        foreach ( array_reverse( $this->objects ) as $objectID )
        {
            $object = eZContentObject::fetch( $objectID );
            if ( $object instanceof eZContentObject )
            {
                $object->purge();
            }
        }
        foreach ( $this->userIDs as $userID )
        {
            if ( eZUser::fetch( $userID ) )
            {
                eZUser::removeUser( $userID );
            }
            eZUser::purgeUserCacheByUserId( $userID );
        }
        unset( $GLOBALS['ezpolicylimitation_list'] );
        ezpINIHelper::restoreINISettings();
        eZRole::expireCache();
        eZContentObject::clearCache();
        $this->roles = $this->objects = $this->userIDs = array();
    }

    /**
     * A user that was never published (no groups, so only the role given here applies).
     *
     * @param array $policies array( array( module, function, limitations ), ... )
     * @return int the user's content object ID
     */
    private function userWithRole( array $policies )
    {
        $login = 'x1-access-for-user-' . bin2hex( random_bytes( 4 ) );
        $userObject = eZContentClass::fetchByIdentifier( 'user' )->instantiate( self::ADMIN_ID );
        $userID = (int)$userObject->attribute( 'id' );
        $this->objects[] = $userID;
        $this->userIDs[] = $userID;

        $user = eZUser::fetch( $userID );
        if ( !$user )
        {
            $user = eZUser::create( $userID );
        }
        $user->setAttribute( 'login', $login );
        $user->setAttribute( 'email', $login . '@' . self::ADDRESS_DOMAIN );
        $user->setAttribute( 'password_hash', eZUser::createHash( $login, bin2hex( random_bytes( 12 ) ), eZUser::site(), eZUser::hashType() ) );
        $user->setAttribute( 'password_hash_type', eZUser::hashType() );
        $user->store();

        $role = eZRole::create( 'X1 access for user ' . $login );
        $role->store();
        $this->roles[] = $role;
        foreach ( $policies as $policy )
        {
            $role->appendPolicy( $policy[0], $policy[1], isset( $policy[2] ) ? $policy[2] : array() );
        }
        $role->assignToUser( $userID );
        eZRole::expireCache();
        eZUser::purgeUserCacheByUserId( $userID );
        return $userID;
    }

    private function logIn( $userID )
    {
        unset( $GLOBALS['ezpolicylimitation_list'] );
        eZUser::setCurrentlyLoggedInUser( eZUser::fetch( $userID ), $userID, eZUser::NO_SESSION_REGENERATE );
        $this->assertSame( $userID, (int)eZUser::currentUserID() );
    }

    private function node( $nodeID )
    {
        $node = eZContentObjectTreeNode::fetch( $nodeID );
        $this->assertInstanceOf( 'eZContentObjectTreeNode', $node, "node $nodeID exists" );
        return $node;
    }

    /** UA-01 */
    public function testObjectAndNodeAreCheckedForTheUserGiven()
    {
        $userID = $this->userWithRole( array( array( 'content', 'read', array( 'Subtree' => array( $this->node( 2 )->attribute( 'path_string' ) ) ) ) ) );
        $content = $this->node( 2 );
        $users = $this->node( 5 );

        $this->assertEquals( 1, $content->checkAccess( 'read', false, false, false, false, $userID ) );
        $this->assertEquals( 1, $content->object()->checkAccess( 'read', false, false, false, false, $userID ) );
        $this->assertEquals( 0, $users->checkAccess( 'read', false, false, false, false, $userID ) );
        $this->assertEquals( 0, $users->object()->checkAccess( 'read', false, false, false, false, $userID ) );
        // The current user, the administrator, may read both
        $this->assertEquals( 1, $users->checkAccess( 'read' ) );
        $this->assertEquals( 1, $users->object()->checkAccess( 'read' ) );
        $this->assertSame( self::ADMIN_ID, (int)eZUser::currentUserID() );
    }

    /** UA-02 */
    public function testVersionIsCheckedForTheUserGiven()
    {
        $reader = $this->userWithRole( array( array( 'content', 'versionread', array( 'Subtree' => array( $this->node( 2 )->attribute( 'path_string' ) ) ) ) ) );
        $nobody = $this->userWithRole( array( array( 'content', 'read' ) ) );
        $version = $this->node( 2 )->object()->currentVersion();

        $this->assertEquals( 1, $version->checkAccess( 'versionread', false, false, false, false, $reader ) );
        $this->assertEquals( 0, $version->checkAccess( 'versionread', false, false, false, false, $nobody ) );
        $this->assertEquals( 1, $version->checkAccess( 'versionread' ) );
    }

    /** UA-03 */
    public function testAnIDThatIsNoUserGetsNoAccess()
    {
        $node = $this->node( 2 );
        $noUser = $node->attribute( 'contentobject_id' );
        $this->assertNull( eZUser::fetch( $noUser ) );

        $this->assertEquals( 0, $node->checkAccess( 'read', false, false, false, false, $noUser ) );
        $this->assertEquals( 0, $node->object()->checkAccess( 'read', false, false, false, false, $noUser ) );
        $this->assertEquals( 0, $node->object()->currentVersion()->checkAccess( 'versionread', false, false, false, false, $noUser ) );
        $this->assertSame( self::ADMIN_ID, (int)eZUser::currentUserID() );
    }

    private function userWithExtensionLimitation()
    {
        return $this->userWithRole( array( array( 'content', 'read', array( 'Subtree' => array( $this->node( 2 )->attribute( 'path_string' ) ),
                                                                            'X1LiveLimitation' => array( 1 ) ) ) ) );
    }

    /** UA-04 */
    public function testExtensionLimitationWithoutHandlerShowsNothing()
    {
        ezpINIHelper::setINISetting( 'site.ini', 'RoleSettings', 'LimitationHandlers', array() );
        $userID = $this->userWithExtensionLimitation();
        $this->assertGreaterThan( 0, eZContentObjectTreeNode::subTreeCountByNodeID( array(), 2 ), 'the administrator sees nodes below node 2' );

        $this->assertEquals( 0, $this->node( 2 )->checkAccess( 'read', false, false, false, false, $userID ) );
        $this->logIn( $userID );
        $this->assertEquals( 0, $this->node( 2 )->checkAccess( 'read' ) );
        $this->assertSame( 0, (int)eZContentObjectTreeNode::subTreeCountByNodeID( array(), 2 ) );
    }

    /** UA-05 */
    public function testExtensionLimitationWithHandlerShowsWhatTheHandlerAllows()
    {
        ezpINIHelper::setINISetting( 'site.ini', 'RoleSettings', 'LimitationHandlers', array( 'X1LiveLimitation' => 'X1LiveContentLimitationHandler' ) );
        $userID = $this->userWithExtensionLimitation();
        $all = (int)eZContentObjectTreeNode::subTreeCountByNodeID( array(), 2 );

        $this->assertEquals( 1, $this->node( 2 )->checkAccess( 'read', false, false, false, false, $userID ) );
        $this->logIn( $userID );
        $this->assertEquals( 1, $this->node( 2 )->checkAccess( 'read' ) );
        $this->assertSame( $all, (int)eZContentObjectTreeNode::subTreeCountByNodeID( array(), 2 ) );
    }
}
