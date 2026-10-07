<?php
/**
 * Who may open the dialogs of the online editor (ezoe/dialog, ezoe/relations), without the database:
 * Exponential\View\Extension\Ezoe\Ezoe\Dialog::mayOpen() and mayEditVersion().
 *
 *  DA-01 - Who may read the object opens them, whatever version the address names, as before
 *  DA-02 - The creator of a draft (draft, internal draft, to be repeated) who may edit the object opens them without
 *          read access (an object that was never published)
 *  DA-03 - Someone else's draft opens nothing for who may not read the object, also with edit access
 *  DA-04 - A published, pending, archived, rejected or queued version opens nothing for who may not read the object
 *  DA-05 - A version that does not exist, version 0 or below, a version of another object, or no object opens nothing
 *  DA-06 - An object in the trash opens nothing for who may not read it
 *  DA-07 - Edit access is asked for the version in its own language (a Language limitation applies)
 *  DA-08 - Without edit access nothing opens; a listener of content/edit/access has its say both ways
 *  DA-09 - Both views ask Dialog::mayOpen()
 *  DA-10 - The dialog the address names is a template name in design:ezoe/ only (letters, digits, "_", "-")
 *
 * The object and its versions are stand-ins whose permissions are given; the current user is a stand-in user.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__, 4 ) . '/extension/ezoe/classes/runnable/views/ezoe/dialog.php';

use Exponential\View\Extension\Ezoe\Ezoe\Dialog;

/** An object whose versions are given, and which can record the edit checks asked of it */
class expOEDialogTestObject extends eZContentObject
{
    public $testVersions = array();
    public $editCalls = array();
    public $recordEdit = false;
    public $editAnswer = false;

    function version( $version, $asObject = true )
    {
        return isset( $this->testVersions[(int)$version] ) ? $this->testVersions[(int)$version] : null;
    }

    function editAccess( $version = null, $language = false, $userID = false )
    {
        if ( !$this->recordEdit )
        {
            return parent::editAccess( $version, $language, $userID );
        }
        $this->editCalls[] = array( $version, $language );
        return $this->editAnswer;
    }
}

/** A version whose language is given */
class expOEDialogTestVersion extends eZContentObjectVersion
{
    public $testLanguage = false;

    function initialLanguageCode()
    {
        return $this->testLanguage;
    }
}

class expOEDialogAccessRulesTest extends PHPUnit\Framework\TestCase
{
    const OBJECT_ID = 990501;
    const EDITOR_ID = 990502;
    const OTHER_ID = 990503;

    private $hadUser;
    private $user;
    private $listeners = array();

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->hadUser = array_key_exists( 'eZUserGlobalInstance_', $GLOBALS );
        $this->user = $this->hadUser ? $GLOBALS['eZUserGlobalInstance_'] : null;
        $user = new eZUser( array( 'contentobject_id' => self::EDITOR_ID, 'login' => 'x1editor' ) );
        // an enabled user, without asking the database
        $user->setUserCache( array( 'info' => array( self::EDITOR_ID => array( 'is_enabled' => true ) ) ) );
        $GLOBALS['eZUserGlobalInstance_'] = $user;
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

    private function object( $canRead, $canEdit, $status = eZContentObject::STATUS_DRAFT )
    {
        $object = new expOEDialogTestObject( array( 'id' => self::OBJECT_ID, 'status' => $status, 'current_version' => 1 ) );
        $permissions = array( 'can_read' => $canRead, 'can_edit' => $canEdit );
        $object->setPermissions( $permissions );
        return $object;
    }

    private function addVersion( $object, $number, $status, $creatorID, $objectID = self::OBJECT_ID )
    {
        $version = new expOEDialogTestVersion( array( 'contentobject_id' => $objectID, 'version' => $number,
                                                      'status' => $status, 'creator_id' => $creatorID ) );
        $object->testVersions[$number] = $version;
        return $version;
    }

    /** DA-01 */
    public function testWhoMayReadOpensThemForAnyVersion()
    {
        $object = $this->object( 1, 0, eZContentObject::STATUS_PUBLISHED );
        $this->addVersion( $object, 1, eZContentObjectVersion::STATUS_PUBLISHED, self::OTHER_ID );
        $this->assertTrue( Dialog::mayOpen( $object, 1 ) );
        $this->assertTrue( Dialog::mayOpen( $object, 7 ), 'the version number was never checked for readers' );
    }

    /** DA-02 */
    public function testTheCreatorOfADraftOpensThemWithoutReadAccess()
    {
        foreach ( array( eZContentObjectVersion::STATUS_DRAFT, eZContentObjectVersion::STATUS_INTERNAL_DRAFT,
                         eZContentObjectVersion::STATUS_REPEAT ) as $status )
        {
            $object = $this->object( 0, 1 );
            $this->addVersion( $object, 2, $status, self::EDITOR_ID );
            $this->assertTrue( Dialog::mayEditVersion( $object, 2 ), "status $status" );
            $this->assertTrue( Dialog::mayOpen( $object, 2 ), "status $status" );
            $this->assertTrue( Dialog::mayOpen( $object, '2' ), 'the number as the address gives it' );
        }
    }

    /** DA-03 */
    public function testSomeoneElsesDraftOpensNothing()
    {
        $object = $this->object( 0, 1 );
        $this->addVersion( $object, 2, eZContentObjectVersion::STATUS_DRAFT, self::OTHER_ID );
        $this->assertFalse( Dialog::mayOpen( $object, 2 ) );
    }

    /** DA-04 */
    public function testAVersionThatIsNotADraftOpensNothing()
    {
        foreach ( array( eZContentObjectVersion::STATUS_PUBLISHED, eZContentObjectVersion::STATUS_PENDING,
                         eZContentObjectVersion::STATUS_ARCHIVED, eZContentObjectVersion::STATUS_REJECTED,
                         eZContentObjectVersion::STATUS_QUEUED ) as $status )
        {
            $object = $this->object( 0, 1, eZContentObject::STATUS_PUBLISHED );
            $this->addVersion( $object, 3, $status, self::EDITOR_ID );
            $this->assertFalse( Dialog::mayOpen( $object, 3 ), "status $status" );
        }
    }

    /** DA-05 */
    public function testAVersionThatIsNotThereOpensNothing()
    {
        $object = $this->object( 0, 1 );
        $this->addVersion( $object, 1, eZContentObjectVersion::STATUS_DRAFT, self::EDITOR_ID );
        $this->assertFalse( Dialog::mayOpen( $object, 999999 ) );
        $this->assertFalse( Dialog::mayOpen( $object, 0 ), 'version 0 is not "the current version" here' );
        $this->assertFalse( Dialog::mayOpen( $object, -1 ) );
        $this->assertFalse( Dialog::mayOpen( $object, 'x' ) );
        $this->assertFalse( Dialog::mayOpen( null, 1 ) );
        $this->assertFalse( Dialog::mayOpen( false, 1 ) );

        $other = $this->object( 0, 1 );
        $this->addVersion( $other, 4, eZContentObjectVersion::STATUS_DRAFT, self::EDITOR_ID, self::OBJECT_ID + 100 );
        $this->assertFalse( Dialog::mayOpen( $other, 4 ), 'a version of another object' );
    }

    /** DA-06 */
    public function testAnObjectInTheTrashOpensNothing()
    {
        $object = $this->object( 0, 1, eZContentObject::STATUS_ARCHIVED );
        $this->addVersion( $object, 2, eZContentObjectVersion::STATUS_DRAFT, self::EDITOR_ID );
        $this->assertFalse( Dialog::mayOpen( $object, 2 ) );
    }

    /** DA-07 */
    public function testEditAccessIsAskedForTheVersionInItsLanguage()
    {
        $object = $this->object( 0, 0 );
        $object->recordEdit = true;
        $object->editAnswer = true;
        $version = $this->addVersion( $object, 2, eZContentObjectVersion::STATUS_DRAFT, self::EDITOR_ID );
        $version->testLanguage = 'ger-DE';
        $this->assertTrue( Dialog::mayOpen( $object, 2 ) );
        $this->assertSame( array( array( $version, 'ger-DE' ) ), $object->editCalls );

        $object->editCalls = array();
        $object->editAnswer = false;
        $this->assertFalse( Dialog::mayOpen( $object, 2 ), 'not allowed in that language' );

        $object->editCalls = array();
        $object->editAnswer = true;
        $version->testLanguage = false;
        $this->assertTrue( Dialog::mayOpen( $object, 2 ) );
        $this->assertSame( array( array( $version, false ) ), $object->editCalls, 'no language: any' );
    }

    /** DA-08 */
    public function testWithoutEditAccessNothingOpensAndAListenerHasItsSay()
    {
        $object = $this->object( 0, 0 );
        $version = $this->addVersion( $object, 2, eZContentObjectVersion::STATUS_DRAFT, self::EDITOR_ID );
        $this->assertFalse( Dialog::mayOpen( $object, 2 ) );

        $seen = array();
        $this->listeners[] = ezpEvent::getInstance()->attach( 'content/edit/access', function ( $allowed, $subject, $subjectVersion ) use ( $object, &$seen )
        {
            $seen[] = $subjectVersion;
            return $allowed || $subject === $object;
        } );
        $this->assertTrue( Dialog::mayOpen( $object, 2 ), 'a listener lets the creator in' );
        $this->assertSame( array( $version ), $seen, 'the listener gets the version being edited' );

        $refusing = $this->object( 0, 1 );
        $this->addVersion( $refusing, 2, eZContentObjectVersion::STATUS_DRAFT, self::EDITOR_ID );
        $this->listeners[] = ezpEvent::getInstance()->attach( 'content/edit/access', function () { return false; } );
        $this->assertFalse( Dialog::mayOpen( $refusing, 2 ), 'a listener keeps the creator out' );
    }

    /** DA-10 */
    public function testTheDialogIsATemplateNameInTheEditorsDesignOnly()
    {
        foreach ( array( 'tag_link', 'help', 'merge_cells', 'tag_table_cell', 'my-dialog', 'Tag2' ) as $name )
            $this->assertTrue( Dialog::isDialogName( $name ), $name );
        foreach ( array( '', '..', 'tag_link.tpl', 'a/b', '../content/edit', "tag_link\n", 'tag link', 'tag_l%69nk',
                         str_repeat( 'a', 101 ), null, array( 'help' ) ) as $name )
            $this->assertFalse( Dialog::isDialogName( $name ), var_export( $name, true ) );
        $source = file_get_contents( dirname( __DIR__, 4 ) . '/extension/ezoe/classes/runnable/views/ezoe/dialog.php' );
        $this->assertStringContainsString( 'if ( !self::isDialogName( $dialog ) )', $source );
    }

    /** DA-09 */
    public function testBothViewsAskMayOpen()
    {
        $root = dirname( __DIR__, 4 ) . '/extension/ezoe/classes/runnable/views/ezoe/';
        $this->assertStringContainsString( 'self::mayOpen( $object, $objectVersion )', file_get_contents( $root . 'dialog.php' ) );
        $relations = file_get_contents( $root . 'relations.php' );
        $this->assertStringContainsString( 'Dialog::mayOpen( $object, $objectVersion )', $relations );
        $this->assertStringNotContainsString( '!$object->canRead()', $relations );
    }
}
