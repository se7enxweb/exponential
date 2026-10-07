<?php
/**
 * The dialogs of the editor (ezoe/dialog, ezoe/relations) open for whoever may read the object and for whoever may
 * edit the version being edited: Exponential\View\Extension\Ezoe\Ezoe\Dialog::mayOpen(). Someone who edits a draft of
 * an object that was never published can not read the object yet, and an extension may let others edit a version
 * (filter content/edit/access, as for uploads and custom tags).
 *
 * Each test creates its own user (never published, address at x1.example.invalid) with a role of its own and removes
 * both again; the object is the one of the content root, which is not changed.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expOETestCase.php';

use Exponential\View\Extension\Ezoe\Ezoe\Dialog;

class expOEDialogAccessTest extends expOETestCase
{
    const ADDRESS_DOMAIN = 'x1.example.invalid';

    private $roles = array();
    private $userIDs = array();

    public function tearDown(): void
    {
        foreach ( $this->roles as $role )
        {
            $role->removeThis();
        }
        foreach ( $this->userIDs as $userID )
        {
            $object = eZContentObject::fetch( $userID );
            if ( $object instanceof eZContentObject )
            {
                $object->purge();
            }
            if ( eZUser::fetch( $userID ) )
            {
                eZUser::removeUser( $userID );
            }
            eZUser::purgeUserCacheByUserId( $userID );
        }
        unset( $GLOBALS['ezpolicylimitation_list'] );
        eZRole::expireCache();
        eZContentObject::clearCache();
        $this->roles = $this->userIDs = array();
        parent::tearDown();
    }

    /**
     * Logs in a user that was never published (no groups, so only the policies given apply).
     *
     * @param array $policies array( array( module, function ), ... ) without limitations
     */
    private function logInUserWith( array $policies )
    {
        $this->loginAdmin();
        $adminID = (int)eZUser::currentUserID();
        $login = 'x1-ezoe-dialog-' . bin2hex( random_bytes( 4 ) );
        $userObject = eZContentClass::fetchByIdentifier( 'user' )->instantiate( $adminID );
        $userID = (int)$userObject->attribute( 'id' );
        $this->userIDs[] = $userID;

        $user = eZUser::fetch( $userID ) ?: eZUser::create( $userID );
        $user->setAttribute( 'login', $login );
        $user->setAttribute( 'email', $login . '@' . self::ADDRESS_DOMAIN );
        $user->setAttribute( 'password_hash', eZUser::createHash( $login, bin2hex( random_bytes( 12 ) ), eZUser::site(), eZUser::hashType() ) );
        $user->setAttribute( 'password_hash_type', eZUser::hashType() );
        $user->store();

        $role = eZRole::create( 'X1 ezoe dialog ' . $login );
        $role->store();
        $this->roles[] = $role;
        foreach ( $policies as $policy )
        {
            $role->appendPolicy( $policy[0], $policy[1] );
        }
        $role->assignToUser( $userID );
        eZRole::expireCache();
        eZUser::purgeUserCacheByUserId( $userID );
        unset( $GLOBALS['ezpolicylimitation_list'] );
        eZContentObject::clearCache();

        eZUser::setCurrentlyLoggedInUser( eZUser::fetch( $userID ), $userID, eZUser::NO_SESSION_REGENERATE );
        $this->assertSame( $userID, (int)eZUser::currentUserID() );
    }

    /** The object of the content root and its current version */
    private function rootObject()
    {
        $node = eZContentObjectTreeNode::fetch( 2 );
        $this->assertInstanceOf( 'eZContentObjectTreeNode', $node );
        $object = eZContentObject::fetch( (int)$node->attribute( 'contentobject_id' ) );
        return array( $object, (int)$object->attribute( 'current_version' ) );
    }

    public function testWhoMayReadTheObjectOpensTheDialogs()
    {
        $this->logInUserWith( array( array( 'content', 'read' ) ) );
        list( $object, $version ) = $this->rootObject();

        $this->assertTrue( $object->canRead() );
        $this->assertTrue( Dialog::mayOpen( $object, $version ) );
    }

    public function testWhoMayEditTheVersionButNotReadTheObjectOpensTheDialogs()
    {
        $this->logInUserWith( array( array( 'content', 'edit' ) ) );
        list( $object, $version ) = $this->rootObject();

        $this->assertFalse( (bool)$object->canRead(), 'the user may not read the object' );
        $this->assertTrue( (bool)$object->editAccess( $object->version( $version ) ), 'the user may edit the version' );
        $this->assertTrue( Dialog::mayOpen( $object, $version ) );
    }

    public function testWhoMayNeitherReadNorEditDoesNotOpenThem()
    {
        $this->logInUserWith( array( array( 'content', 'create' ) ) );
        list( $object, $version ) = $this->rootObject();

        $this->assertFalse( Dialog::mayOpen( $object, $version ) );
    }

    public function testAVersionThatDoesNotExistOrNoObjectOpensNothing()
    {
        $this->logInUserWith( array( array( 'content', 'edit' ) ) );
        list( $object, $version ) = $this->rootObject();

        $this->assertFalse( Dialog::mayOpen( $object, 999999 ) );
        $this->assertFalse( Dialog::mayOpen( null, $version ) );
    }
}
