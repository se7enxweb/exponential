<?php
/**
 * Who may open the versions of an object (content/history) and whose content they see there, on the installation
 * the tests run on. Live style: no test database. Each test creates its own user (never published, address at
 * x1.example.invalid), its own role and its own folder draft below node 2, and removes all of them again in
 * tearDown(), also when the test fails; where there is no installation (CI) the tests are skipped.
 *
 *  HL-01 - An editor with an edit policy for the subtree and no read policy opens the history of someone's draft
 *  HL-02 - A reader without any edit policy (as the anonymous user) does not; neither does the anonymous user
 *  HL-03 - A reader of the object with an edit policy for other content opens it, as before
 *  HL-04 - A further editor let in only by a content/edit/access listener opens it
 *  HL-05 - The editor sees the content of the archived version, of a rejected one and of their own draft, not of
 *          someone's draft; with content/versionread they see that too
 *  HL-06 - An edit policy for one language: edit access in the other language is refused (the copy check)
 *  HL-07 - The policy functions of the view: "read or edit" lets an edit-only user reach the view
 *  HL-08 - A section limitation that does not match keeps the editor out
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use Exponential\View\Kernel\Content\History;

class eZContentHistoryAccessLiveTest extends PHPUnit\Framework\TestCase
{
    const ADDRESS_DOMAIN = 'x1.example.invalid';
    const ADMIN_ID = 14;

    private static $installation;
    private $previousUser;
    private $objects = array();
    private $roles = array();
    private $userIDs = array();
    private $listeners = array();

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
        foreach ( $this->listeners as $id )
        {
            ezpEvent::getInstance()->detach( 'content/edit/access', $id );
        }
        if ( $this->previousUser instanceof eZUser )
        {
            eZUser::setCurrentlyLoggedInUser( $this->previousUser, $this->previousUser->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
        }
        foreach ( $this->roles as $role )
        {
            try { $role->removeThis(); } catch ( Exception $e ) { /* go on with the rest */ }
        }
        foreach ( array_reverse( $this->objects ) as $objectID )
        {
            try
            {
                $object = eZContentObject::fetch( $objectID );
                if ( $object instanceof eZContentObject )
                {
                    $object->purge();
                }
            }
            catch ( Exception $e ) { /* go on with the rest */ }
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
        $this->roles = $this->objects = $this->userIDs = $this->listeners = array();
    }

    /**
     * A user that was never published (no groups, so only the role given here applies), logged in.
     *
     * @param array $policies array( array( module, function, limitations ), ... )
     * @return int the user's content object ID
     */
    private function logInUserWithRole( array $policies )
    {
        $login = 'x1-history-access-' . bin2hex( random_bytes( 4 ) );
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

        $role = eZRole::create( 'X1 history access ' . $login );
        $role->store();
        $this->roles[] = $role;
        foreach ( $policies as $policy )
        {
            $role->appendPolicy( $policy[0], $policy[1], isset( $policy[2] ) ? $policy[2] : array() );
        }
        $role->assignToUser( $userID );
        eZRole::expireCache();
        eZUser::purgeUserCacheByUserId( $userID );

        eZUser::setCurrentlyLoggedInUser( eZUser::fetch( $userID ), $userID, eZUser::NO_SESSION_REGENERATE );
        $this->assertSame( $userID, (int)eZUser::currentUserID() );
        return $userID;
    }

    /** A new folder of the administrator that was never published, to be published below node 2 */
    private function draft()
    {
        $class = eZContentClass::fetchByIdentifier( 'folder' );
        if ( !$class instanceof eZContentClass )
        {
            $this->markTestSkipped( 'needs the folder class' );
        }
        $object = $class->instantiate( self::ADMIN_ID );
        $this->objects[] = (int)$object->attribute( 'id' );
        $object->createNodeAssignment( 2, true );
        return $this->fresh( $object );
    }

    /** The object as the next request sees it, without permissions cached on it */
    private function fresh( $object )
    {
        eZContentObject::clearCache( array( (int)$object->attribute( 'id' ) ) );
        return eZContentObject::fetch( $object->attribute( 'id' ) );
    }

    /** A further version of $object made by $creatorID, in $status */
    private function addVersion( $object, $creatorID, $status )
    {
        $version = $object->createNewVersion( false, false );
        $version->setAttribute( 'creator_id', $creatorID );
        $version->setAttribute( 'status', $status );
        $version->store();
        return (int)$version->attribute( 'version' );
    }

    private function subtreeOf( $nodeID )
    {
        $node = eZContentObjectTreeNode::fetch( $nodeID, false, false );
        $this->assertIsArray( $node, "node $nodeID exists" );
        return $node['path_string'];
    }

    private function seenVersions( $object )
    {
        $object = $this->fresh( $object );
        $canRead = (bool)$object->attribute( 'can_read' );
        $canEdit = (bool)$object->editAccess();
        $seen = array();
        foreach ( $object->versions() as $version )
        {
            if ( History::canSeeVersionContent( $version, $canRead, $canEdit, (int)eZUser::currentUserID() ) )
                $seen[] = (int)$version->attribute( 'version' );
        }
        sort( $seen );
        return $seen;
    }

    private function mayOpenView()
    {
        $params = array();
        return (bool)eZUser::currentUser()->hasAccessToView( eZModule::findModule( 'content' ), 'history', $params );
    }

    /** HL-01 */
    public function testAnEditorWithoutReadOpensTheHistoryOfSomeonesDraft()
    {
        $draft = $this->draft();
        $this->logInUserWithRole( array( array( 'content', 'edit', array( 'Subtree' => array( $this->subtreeOf( 2 ) ) ) ) ) );
        $draft = $this->fresh( $draft );
        $this->assertFalse( (bool)$draft->attribute( 'can_read' ), 'the administrator\'s draft is not readable for the editor' );
        $this->assertTrue( (bool)$draft->editAccess() );
        $this->assertTrue( History::canOpen( $draft ) );
        $this->assertTrue( $this->mayOpenView(), 'the view lets in who has edit and no read policy' );
    }

    /** HL-02 */
    public function testAReaderWithoutEditStaysOut()
    {
        $root = eZContentObjectTreeNode::fetch( 2 )->attribute( 'object' );
        $this->logInUserWithRole( array( array( 'content', 'read' ) ) );
        $root = $this->fresh( $root );
        $this->assertTrue( (bool)$root->attribute( 'can_read' ) );
        $this->assertFalse( History::canOpen( $root ) );
        $this->tearDown();

        $anonymous = eZUser::fetch( eZUser::anonymousId() );
        $this->assertInstanceOf( eZUser::class, $anonymous );
        eZUser::setCurrentlyLoggedInUser( $anonymous, eZUser::anonymousId(), eZUser::NO_SESSION_REGENERATE );
        $root = $this->fresh( eZContentObjectTreeNode::fetch( 2 )->attribute( 'object' ) );
        $this->assertFalse( History::canOpen( $root ), 'the anonymous user' );
    }

    /** HL-03 */
    public function testAReaderWithAnEditPolicyElsewhereOpensIt()
    {
        $root = eZContentObjectTreeNode::fetch( 2 )->attribute( 'object' );
        $this->logInUserWithRole( array( array( 'content', 'read' ),
                                         array( 'content', 'edit', array( 'Subtree' => array( $this->subtreeOf( 5 ) ) ) ) ) );
        $root = $this->fresh( $root );
        $this->assertFalse( (bool)$root->editAccess() );
        $this->assertTrue( History::canOpen( $root ) );
    }

    /** HL-04 */
    public function testAFurtherEditorLetInByAListenerOpensIt()
    {
        $draft = $this->draft();
        $draftID = (int)$draft->attribute( 'id' );
        $this->logInUserWithRole( array( array( 'content', 'read', array( 'Subtree' => array( $this->subtreeOf( 2 ) ) ) ) ) );
        $draft = $this->fresh( $draft );
        $this->assertFalse( History::canOpen( $draft ) );
        $this->listeners[] = ezpEvent::getInstance()->attach( 'content/edit/access', function ( $allowed, $object ) use ( $draftID )
        {
            return $allowed || ( $object instanceof eZContentObject && (int)$object->attribute( 'id' ) === $draftID );
        } );
        $this->assertTrue( History::canOpen( $this->fresh( $draft ) ) );
        $this->assertTrue( $this->mayOpenView(), 'a read policy is enough for the view, the object decides' );
    }

    /** HL-05 */
    public function testTheEditorSeesArchivedRejectedAndOwnVersionsNotSomeonesDraft()
    {
        $draft = $this->draft();
        // version 1 archived, then a draft and a rejected version of the administrator, then the editor's draft
        $v1 = $draft->version( 1 );
        $v1->setAttribute( 'status', eZContentObjectVersion::STATUS_ARCHIVED );
        $v1->store();
        $v2 = $this->addVersion( $draft, self::ADMIN_ID, eZContentObjectVersion::STATUS_DRAFT );
        $rejected = $this->addVersion( $this->fresh( $draft ), self::ADMIN_ID, eZContentObjectVersion::STATUS_REJECTED );

        $editorID = $this->logInUserWithRole( array( array( 'content', 'edit', array( 'Subtree' => array( $this->subtreeOf( 2 ) ) ) ) ) );
        $v3 = $this->addVersion( $this->fresh( $draft ), $editorID, eZContentObjectVersion::STATUS_DRAFT );
        $this->assertSame( array( 1, $rejected, $v3 ), $this->seenVersions( $draft ), 'archived, rejected and own, not version ' . $v2 );
        $this->tearDownUserOnly();

        $this->logInUserWithRole( array( array( 'content', 'edit', array( 'Subtree' => array( $this->subtreeOf( 2 ) ) ) ),
                                         array( 'content', 'versionread' ) ) );
        $this->assertSame( array( 1, $v2, $rejected, $v3 ), $this->seenVersions( $draft ), 'with versionread every version' );
    }

    /** Logs the last user out and removes their role, keeping the objects of the test */
    private function tearDownUserOnly()
    {
        foreach ( $this->roles as $role )
        {
            $role->removeThis();
        }
        $this->roles = array();
        eZRole::expireCache();
        eZUser::setCurrentlyLoggedInUser( $this->previousUser, $this->previousUser->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
    }

    /** HL-06 */
    public function testAnEditPolicyForOneLanguageRefusesTheOther()
    {
        $languages = eZContentLanguage::fetchList();
        if ( count( $languages ) < 2 )
        {
            $this->markTestSkipped( 'needs two languages' );
        }
        $draft = $this->draft();
        $initial = $draft->initialLanguageCode();
        $other = null;
        foreach ( $languages as $language )
        {
            if ( $language->attribute( 'locale' ) !== $initial )
            {
                $other = $language->attribute( 'locale' );
                break;
            }
        }
        $this->logInUserWithRole( array( array( 'content', 'edit', array( 'Subtree' => array( $this->subtreeOf( 2 ) ),
                                                                           'Language' => array( $initial ) ) ) ) );
        $draft = $this->fresh( $draft );
        $this->assertTrue( History::canOpen( $draft ) );
        $this->assertTrue( (bool)$draft->editAccess( $draft->version( 1 ), $initial ) );
        $this->assertFalse( (bool)$draft->editAccess( $draft->version( 1 ), $other ), "no edit in $other" );
    }

    /** HL-07 */
    public function testTheViewLetsInAnEditOnlyUser()
    {
        $this->logInUserWithRole( array( array( 'content', 'edit' ) ) );
        $this->assertTrue( $this->mayOpenView() );
        $this->tearDown();
        $this->logInUserWithRole( array( array( 'content', 'diff' ) ) );
        $this->assertFalse( $this->mayOpenView(), 'neither read nor edit' );
    }

    /** HL-08 */
    public function testASectionLimitationThatDoesNotMatchKeepsTheEditorOut()
    {
        $draft = $this->draft();
        $otherSection = (int)$draft->attribute( 'section_id' ) + 1000;
        $this->logInUserWithRole( array( array( 'content', 'edit', array( 'Section' => array( $otherSection ) ) ) ) );
        $this->assertFalse( History::canOpen( $this->fresh( $draft ) ) );
    }
}
