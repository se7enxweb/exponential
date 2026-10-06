<?php
/**
 * Who may open the versions of an object (content/history), and whose content they see there, without the database:
 * \Exponential\View\Kernel\Content\History::canOpen(), canSeeVersionContent() and isRemovableStatus().
 *
 *  HA-01 - Who may read the object and edit some content opens its history, as before
 *  HA-02 - Who may edit it opens it too: the owner's draft is not readable for the other editors
 *  HA-03 - A further editor an extension lets in (filter content/edit/access) opens it
 *  HA-04 - Who may neither read nor edit stays out, also when a listener refuses edit
 *  HA-05 - A reader without any edit policy (the anonymous user) stays out, as the view's functions kept them out
 *  HA-06 - One's own versions show their content, the anonymous user's do not
 *  HA-07 - The published and archived versions show their content to who may read or edit the object
 *  HA-08 - Someone else's draft or pending version shows its content only with content/versionread; a rejected
 *          version shows it to who may edit the object, to make the next version from it
 *  HA-09 - Removal is offered and done for drafts, archived, rejected and untouched drafts only
 *  HA-10 - The view's policy functions are "read or edit", so the view decides for the object
 *  HA-11 - The Back button: without an origin the object's own location (never node 2 by rule, never an edit that makes a
 *          draft), else the page the history was opened from when it is safe
 *
 * The object and its versions are stand-ins whose permissions are given; the current user is a stand-in anonymous
 * user.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

use Exponential\View\Kernel\Content\History;

class eZContentHistoryAccessTest extends PHPUnit\Framework\TestCase
{
    const EDITOR_ID = 990402;
    const OTHER_ID = 990403;

    private $hadUser;
    private $user;
    private $listeners = array();

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->hadUser = array_key_exists( 'eZUserGlobalInstance_', $GLOBALS );
        $this->user = $this->hadUser ? $GLOBALS['eZUserGlobalInstance_'] : null;
        $GLOBALS['eZUserGlobalInstance_'] = new eZUser( array( 'contentobject_id' => eZUser::anonymousId(), 'login' => 'x1anonymous' ) );
    }

    protected function tearDown(): void
    {
        foreach ( $this->listeners as $id )
        {
            ezpEvent::getInstance()->detach( 'content/edit/access', $id );
        }
        if ( $this->hadUser )
            $GLOBALS['eZUserGlobalInstance_'] = $this->user;
        else
            unset( $GLOBALS['eZUserGlobalInstance_'] );
    }

    private function object( $canRead, $canEdit )
    {
        $object = new eZContentObject( array( 'id' => 990401, 'status' => eZContentObject::STATUS_DRAFT ) );
        $permissions = array( 'can_read' => $canRead, 'can_edit' => $canEdit );
        $object->setPermissions( $permissions );
        return $object;
    }

    private function version( $status, $creatorID, $canVersionRead = 0 )
    {
        $version = new eZContentObjectVersion( array( 'contentobject_id' => 990401, 'version' => 3, 'status' => $status, 'creator_id' => $creatorID ) );
        $version->Permissions = array( 'can_versionread' => $canVersionRead );
        return $version;
    }

    private function canOpen( $object, $canEdit = null, $mayEditSomewhere = null )
    {
        return History::canOpen( $object, $canEdit, $mayEditSomewhere );
    }

    /** HA-01 */
    public function testWhoMayReadAndEditSomeContentOpensIt()
    {
        $this->assertTrue( $this->canOpen( $this->object( 1, 0 ), null, true ) );
        $this->assertTrue( $this->canOpen( $this->object( 1, 0 ), false, true ) );
    }

    /** HA-02 */
    public function testWhoMayEditOpensIt()
    {
        $object = $this->object( 0, 1 );
        $this->assertTrue( $this->canOpen( $object ) );
        $this->assertTrue( $this->canOpen( $object, true ), 'the answer the view has already' );
        $this->assertTrue( $this->canOpen( $object, true, false ), 'the edit policies elsewhere do not matter' );
    }

    /** HA-03 */
    public function testAFurtherEditorAnExtensionLetsInOpensIt()
    {
        $object = $this->object( 0, 0 );
        $this->assertFalse( $this->canOpen( $object ) );
        $this->listeners[] = ezpEvent::getInstance()->attach( 'content/edit/access', function ( $allowed, $subject ) use ( $object )
        {
            return $allowed || $subject === $object;
        } );
        $this->assertTrue( $this->canOpen( $object ) );
    }

    /** HA-04 */
    public function testWhoMayNeitherReadNorEditStaysOut()
    {
        $this->assertFalse( $this->canOpen( $this->object( 0, 0 ) ) );
        $this->assertFalse( $this->canOpen( $this->object( 0, 0 ), null, true ), 'an edit policy for other content does not open it' );
        $this->assertFalse( $this->canOpen( $this->object( 0, 1 ), false ) );
        $object = $this->object( 0, 1 );
        $this->listeners[] = ezpEvent::getInstance()->attach( 'content/edit/access', function ( $allowed ) { return false; } );
        $this->assertFalse( $this->canOpen( $object ) );
    }

    /** HA-05 */
    public function testAReaderWithoutAnyEditPolicyStaysOut()
    {
        $this->assertFalse( $this->canOpen( $this->object( 1, 0 ), null, false ) );
        $this->assertFalse( $this->canOpen( $this->object( 1, 0 ), false, false ) );
    }

    /** HA-06 */
    public function testOwnVersionsShowTheirContent()
    {
        foreach ( array( eZContentObjectVersion::STATUS_DRAFT, eZContentObjectVersion::STATUS_PENDING, eZContentObjectVersion::STATUS_REJECTED ) as $status )
        {
            $this->assertTrue( History::canSeeVersionContent( $this->version( $status, self::EDITOR_ID ), false, true, self::EDITOR_ID ), "own version in status $status" );
        }
        $anonymous = (int)eZUser::anonymousId();
        $this->assertFalse( History::canSeeVersionContent( $this->version( eZContentObjectVersion::STATUS_DRAFT, $anonymous ), false, true, $anonymous ),
                            'a draft of the anonymous user belongs to every visitor' );
        $this->assertFalse( History::canSeeVersionContent( null, true, true, self::EDITOR_ID ) );
    }

    /** HA-07 */
    public function testPublishedAndArchivedVersionsShowTheirContentToReadersAndEditors()
    {
        foreach ( array( eZContentObjectVersion::STATUS_PUBLISHED, eZContentObjectVersion::STATUS_ARCHIVED ) as $status )
        {
            $version = $this->version( $status, self::OTHER_ID );
            $this->assertTrue( History::canSeeVersionContent( $version, true, false, self::EDITOR_ID ), "reader, status $status" );
            $this->assertTrue( History::canSeeVersionContent( $version, false, true, self::EDITOR_ID ), "editor, status $status" );
            $this->assertFalse( History::canSeeVersionContent( $version, false, false, self::EDITOR_ID ), "neither, status $status" );
        }
    }

    /** HA-08 */
    public function testSomeoneElsesDraftNeedsVersionRead()
    {
        foreach ( array( eZContentObjectVersion::STATUS_DRAFT, eZContentObjectVersion::STATUS_PENDING, eZContentObjectVersion::STATUS_REPEAT,
                         eZContentObjectVersion::STATUS_INTERNAL_DRAFT, eZContentObjectVersion::STATUS_QUEUED ) as $status )
        {
            $this->assertFalse( History::canSeeVersionContent( $this->version( $status, self::OTHER_ID, 0 ), true, true, self::EDITOR_ID ), "status $status without versionread" );
            $this->assertTrue( History::canSeeVersionContent( $this->version( $status, self::OTHER_ID, 1 ), false, true, self::EDITOR_ID ), "status $status with versionread" );
        }
        $rejected = eZContentObjectVersion::STATUS_REJECTED;
        $this->assertTrue( History::canSeeVersionContent( $this->version( $rejected, self::OTHER_ID, 0 ), false, true, self::EDITOR_ID ), 'rejected, for an editor' );
        $this->assertFalse( History::canSeeVersionContent( $this->version( $rejected, self::OTHER_ID, 0 ), true, false, self::EDITOR_ID ), 'rejected, for a reader' );
        $this->assertTrue( History::canSeeVersionContent( $this->version( $rejected, self::OTHER_ID, 1 ), true, false, self::EDITOR_ID ), 'rejected, for a reader with versionread' );
    }

    /** HA-09 */
    public function testRemovableStatuses()
    {
        $removable = array();
        foreach ( range( 0, 7 ) as $status )
        {
            if ( History::isRemovableStatus( $status ) )
                $removable[] = $status;
        }
        $this->assertSame( array( eZContentObjectVersion::STATUS_DRAFT, eZContentObjectVersion::STATUS_ARCHIVED,
                                  eZContentObjectVersion::STATUS_REJECTED, eZContentObjectVersion::STATUS_INTERNAL_DRAFT ), $removable );
        $this->assertFalse( History::isRemovableStatus( '1' ) );
    }

    /** HA-10 */
    public function testTheViewFunctionsAreReadOrEdit()
    {
        // module.php reads limitation values from the database, so it is read as text here
        $source = file_get_contents( 'kernel/content/module.php' );
        $this->assertMatchesRegularExpression( "/\\\$ViewList\\['history'\\] = array\\((?:\\s*\\/\\/[^\\n]*)?\\s*'functions' => array\\( 'read or edit' \\),/", $source );
        $this->assertMatchesRegularExpression( "/\\\$ViewList\\['versionview'\\] = array\\(\\s*'functions' => array\\( 'versionread' \\),/", $source,
                                               'the version view keeps asking versionread' );
    }

    private function origin( array $candidates, $objectID = 1, $mainNodeID = 2 )
    {
        return History::originURI( $objectID, $mainNodeID, $candidates, '/admin', array(), 'alpha.example' );
    }

    /** HA-11 */
    public function testWhereTheBackButtonGoes()
    {
        // opened directly: the object's own location, or the dashboard for an object without one
        $this->assertSame( '/content/view/full/2', $this->origin( array() ) );
        $this->assertSame( '/content/view/full/61', $this->origin( array( null, '' ), 57, 61 ) );
        $this->assertSame( '/content/dashboard', $this->origin( array(), 990401, 0 ) );
        // from a node view, with or without the siteaccess prefix and the host of this site
        $this->assertSame( '/content/view/full/61', $this->origin( array( 'https://alpha.example/admin/content/view/full/61' ) ) );
        $this->assertSame( '/content/view/full/61', $this->origin( array( '/admin/content/view/full/61' ) ) );
        $this->assertSame( '/Company/About', $this->origin( array( '/admin/Company/About' ) ) );
        // from the edit of a version of this object ("Manage versions"): back to it
        $this->assertSame( '/content/edit/1/9/eng-GB', $this->origin( array( 'content/edit/1/9/eng-GB' ) ) );
        $this->assertSame( '/content/edit/1/9/eng-GB', $this->origin( array( 'https://alpha.example/admin/content/edit/1/9/eng-GB' ) ) );
        // never an edit that makes a new draft, nor the edit of another object
        $this->assertSame( '/content/view/full/2', $this->origin( array( '/content/edit/1' ) ) );
        $this->assertSame( '/content/view/full/2', $this->origin( array( '/content/edit/1/' ) ) );
        $this->assertSame( '/content/view/full/2', $this->origin( array( '/content/edit/57/3/eng-GB' ) ) );
        // never the history itself, a view a return never goes to, another host or a script
        $this->assertSame( '/content/view/full/2', $this->origin( array( 'https://alpha.example/admin/content/history/1' ) ) );
        $this->assertSame( '/content/view/full/2', $this->origin( array( '/user/logout' ) ) );
        $this->assertSame( '/content/view/full/2', $this->origin( array( 'https://evil.example/admin/content/view/full/61' ) ) );
        $this->assertSame( '/content/view/full/2', $this->origin( array( '//evil.example/x' ) ) );
        $this->assertSame( '/content/view/full/2', $this->origin( array( 'javascript:alert(1)' ) ) );
        $this->assertSame( '/content/view/full/2', $this->origin( array( "/content/view/full/61\r\nX: y" ) ) );
        // the first that passes wins: the form's own, then the edit, then the Referer header
        $this->assertSame( '/content/dashboard', $this->origin( array( '/content/dashboard', '/content/edit/1/9', '/content/view/full/61' ) ) );
        $this->assertSame( '/content/view/full/61', $this->origin( array( '/content/edit/1', 'https://alpha.example/admin/content/view/full/61' ) ) );
    }
}
