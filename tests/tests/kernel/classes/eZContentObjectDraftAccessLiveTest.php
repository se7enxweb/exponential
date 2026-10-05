<?php
/**
 * Access to objects that were never published (drafts of new objects), on the installation the tests run on.
 * Live style: no test database. Each test creates its own user (never published, address at x1.example.invalid),
 * its own role and its own draft below node 2, and removes all of them again; where there is no installation (CI)
 * the tests are skipped.
 *
 *  DA-01 - An edit policy limited to the subtree the draft will be published in allows edit (an approver)
 *  DA-02 - A role for edit assigned for that subtree (User_Subtree) allows edit too
 *  DA-03 - A read policy for that subtree, or a read role assigned for it, does not open someone else's draft
 *  DA-04 - Remove with a policy for that subtree stays denied for someone else's draft
 *  DA-05 - An edit policy, or an edit role assignment, for another subtree is denied
 *  DA-06 - Create under the parent allows edit at version 2 (a rejected first version), also through a preview node
 *  DA-07 - Create for another class does not allow edit
 *  DA-08 - Without a node assignment the create rule denies, without an error
 *  DA-09 - The owner of the draft keeps edit, whatever subtree the policy names
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class eZContentObjectDraftAccessLiveTest extends PHPUnit\Framework\TestCase
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
        self::$installation = dirname( __DIR__, 4 );
        ezpLiveInstallation::requireOrSkip();
    }

    protected function setUp(): void
    {
        chdir( self::$installation );
        $this->previousUser = eZUser::currentUser();
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
        eZRole::expireCache();
        eZContentObject::clearCache();
        $this->roles = $this->objects = $this->userIDs = array();
    }

    /**
     * A user that was never published (no groups, so only the role given here applies), logged in.
     *
     * @param array $policies array( array( module, function, limitations ), ... )
     * @param array $assignment array( limit identifier, limit value ) for the role assignment, or none
     * @return int the user's content object ID
     */
    private function logInUserWithRole( array $policies, array $assignment = array() )
    {
        $login = 'x1-draft-access-' . bin2hex( random_bytes( 4 ) );
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

        $role = eZRole::create( 'X1 draft access ' . $login );
        $role->store();
        $this->roles[] = $role;
        foreach ( $policies as $policy )
        {
            $role->appendPolicy( $policy[0], $policy[1], isset( $policy[2] ) ? $policy[2] : array() );
        }
        if ( $assignment )
        {
            $role->assignToUser( $userID, $assignment[0], $assignment[1] );
        }
        else
        {
            $role->assignToUser( $userID );
        }
        eZRole::expireCache();
        eZUser::purgeUserCacheByUserId( $userID );

        eZUser::setCurrentlyLoggedInUser( eZUser::fetch( $userID ), $userID, eZUser::NO_SESSION_REGENERATE );
        $this->assertSame( $userID, (int)eZUser::currentUserID() );
        return $userID;
    }

    private function draftClass()
    {
        $class = eZContentClass::fetchByIdentifier( 'folder' );
        if ( !$class instanceof eZContentClass )
        {
            $this->markTestSkipped( 'needs the folder class' );
        }
        return $class;
    }

    /** A new folder that was never published, to be published below node 2 */
    private function draft( $ownerID = self::ADMIN_ID, $withLocation = true )
    {
        $object = $this->draftClass()->instantiate( $ownerID );
        $this->objects[] = (int)$object->attribute( 'id' );
        if ( $withLocation )
        {
            $object->createNodeAssignment( 2, true );
        }
        return $this->fresh( $object );
    }

    /** The object as checkAccess() sees it on the next request, without permissions cached on it */
    private function fresh( $object )
    {
        eZContentObject::clearCache( array( (int)$object->attribute( 'id' ) ) );
        return eZContentObject::fetch( $object->attribute( 'id' ) );
    }

    private function subtreeOf( $nodeID )
    {
        $node = eZContentObjectTreeNode::fetch( $nodeID, false, false );
        $this->assertIsArray( $node, "node $nodeID exists" );
        return $node['path_string'];
    }

    /** DA-01 */
    public function testSubtreeEditPolicyAllowsEditOfADraftThere()
    {
        $draft = $this->draft();
        $this->logInUserWithRole( array( array( 'content', 'edit', array( 'Subtree' => array( $this->subtreeOf( 2 ) ) ) ) ) );
        $this->assertSame( array( 2 ), $draft->draftParentNodeIDArray() );
        $this->assertEquals( 1, $draft->checkAccess( 'edit' ) );
        $this->assertTrue( $this->fresh( $draft )->canEdit() );
    }

    /** DA-02 */
    public function testEditRoleAssignedForTheSubtreeAllowsEditOfADraftThere()
    {
        $draft = $this->draft();
        $this->logInUserWithRole( array( array( 'content', 'edit' ) ), array( 'subtree', 2 ) );
        $this->assertEquals( 1, $draft->checkAccess( 'edit' ) );
    }

    /** DA-03 */
    public function testReadOfSomeoneElsesDraftStaysClosed()
    {
        $draft = $this->draft();
        $this->logInUserWithRole( array( array( 'content', 'read', array( 'Subtree' => array( $this->subtreeOf( 2 ) ) ) ) ) );
        $this->assertEquals( 0, $draft->checkAccess( 'read' ) );
        $this->tearDown();

        $draft = $this->draft();
        $this->logInUserWithRole( array( array( 'content', 'read' ) ), array( 'subtree', 2 ) );
        $this->assertEquals( 0, $draft->checkAccess( 'read' ) );
    }

    /** DA-04 */
    public function testRemoveOfSomeoneElsesDraftStaysClosed()
    {
        $draft = $this->draft();
        $this->logInUserWithRole( array( array( 'content', 'remove', array( 'Subtree' => array( $this->subtreeOf( 2 ) ) ) ) ) );
        $this->assertEquals( 0, $draft->checkAccess( 'remove' ) );
    }

    /** DA-05 */
    public function testEditForAnotherSubtreeIsDenied()
    {
        $draft = $this->draft();
        $this->logInUserWithRole( array( array( 'content', 'edit', array( 'Subtree' => array( $this->subtreeOf( 5 ) ) ) ) ) );
        $this->assertEquals( 0, $draft->checkAccess( 'edit' ) );
        $this->tearDown();

        $draft = $this->draft();
        $this->logInUserWithRole( array( array( 'content', 'edit' ) ), array( 'subtree', 5 ) );
        $this->assertEquals( 0, $draft->checkAccess( 'edit' ) );
    }

    /** DA-06 */
    public function testCreateUnderTheParentAllowsEditAtVersionTwo()
    {
        $draft = $this->draft();
        $version = $draft->createNewVersion( 1 );
        $draft->setAttribute( 'current_version', $version->attribute( 'version' ) );
        $draft->store();
        $draft = $this->fresh( $draft );
        $this->assertSame( 2, (int)$draft->attribute( 'current_version' ) );
        $this->assertSame( array( 2 ), $draft->draftParentNodeIDArray(), 'version 2 keeps the location of version 1' );

        $this->logInUserWithRole( array( array( 'content', 'create', array( 'Class' => array( $draft->attribute( 'contentclass_id' ) ) ) ) ) );
        $this->assertEquals( 1, $draft->checkAccess( 'edit' ) );
        $this->assertSame( 1, $draft->draftCreateAccess() );

        // The preview node content/versionview builds for it (no node ID) answers the same
        $previewNode = new eZContentObjectTreeNode();
        $previewNode->setAttribute( 'contentobject_id', $draft->attribute( 'id' ) );
        $previewNode->setAttribute( 'contentobject_version', 2 );
        $previewNode->setAttribute( 'parent_node_id', 2 );
        $previewNode->setAttribute( 'path_string', $this->subtreeOf( 2 ) . '/' );
        $this->assertEquals( 1, $previewNode->checkAccess( 'edit' ) );
    }

    /** DA-07 */
    public function testCreateForAnotherClassDoesNotAllowEdit()
    {
        $draft = $this->draft();
        $otherClassID = (int)eZContentClass::classIDByIdentifier( 'user_group' );
        $this->assertNotSame( (int)$draft->attribute( 'contentclass_id' ), $otherClassID );
        $this->logInUserWithRole( array( array( 'content', 'create', array( 'Class' => array( $otherClassID ) ) ) ) );
        $this->assertEquals( 0, $draft->checkAccess( 'edit' ) );
    }

    /** DA-08 */
    public function testWithoutNodeAssignmentTheCreateRuleDenies()
    {
        $draft = $this->draft( self::ADMIN_ID, false );
        $this->assertSame( array(), $draft->draftParentNodeIDArray() );
        $this->logInUserWithRole( array( array( 'content', 'create', array( 'Class' => array( $draft->attribute( 'contentclass_id' ) ) ) ) ) );
        $this->assertEquals( 0, $draft->checkAccess( 'edit' ) );
        $this->assertNull( $draft->draftCreateAccess() );
    }

    /** DA-09 */
    public function testTheOwnerKeepsEdit()
    {
        $userID = $this->logInUserWithRole( array( array( 'content', 'edit', array( 'Subtree' => array( $this->subtreeOf( 5 ) ) ) ) ) );
        $draft = $this->draft( $userID );
        $this->assertSame( $userID, (int)$draft->attribute( 'owner_id' ) );
        $this->assertEquals( 1, $draft->checkAccess( 'edit' ) );
    }
}
