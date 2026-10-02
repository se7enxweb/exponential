<?php
/**
 * Removing a media attribute deletes its file only when no other ezmedia row names it.
 *
 * A copy of an object with a media file shares the source's file: the copied ezmedia row keeps
 * the file name. eZMediaType::deleteStoredObjectAttribute() used to delete every row's file on a
 * purge without looking at other rows, and counted only the rows of the same object when one
 * version went, so purging a copy deleted the file the source still plays.
 *
 * These tests run against the installation's own database and storage, with the kernel started
 * once (eZScript, the admin siteaccess): no test database, no shared cache is rebuilt from
 * other data. Each test creates a folder of its own below the media root, with objects of the
 * shipped "video" class (its "file" attribute is an ezmedia one), and removes the folder with
 * everything in it afterwards; the last assertions of each test are that none of its rows or
 * files is left.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/datatypes/ezmedia/eZMediaSharedFileRemovalTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class eZMediaSharedFileRemovalTest extends PHPUnit\Framework\TestCase
{
    const MEDIA_FILE_PATH = 'tests/tests/kernel/datatypes/ezmedia/ezmediatype_regression_issue14983.flv';
    const CLASS_IDENTIFIER = 'video';
    const ATTRIBUTE_IDENTIFIER = 'file';

    /** @var eZScript|null */
    protected static $script = null;

    /** @var string|null why the kernel could not be started */
    protected static $bootError = null;

    /** @var int the test's own folder node */
    protected $folderNodeID = 0;

    /** @var int[] every media attribute id the test made, checked for leftover rows */
    protected $attributeIDs = array();

    /** @var string[] every file path the test's rows named, checked for leftover files */
    protected $paths = array();

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        if ( self::$script !== null || self::$bootError !== null )
            return;
        try
        {
            $root = dirname( __DIR__, 5 );
            chdir( $root );
            if ( !class_exists( 'eZScript' ) )
                require_once $root . '/autoload.php';
            $script = eZScript::instance( array( 'use-session' => false, 'use-modules' => true, 'use-extensions' => true ) );
            $script->startup();
            $script->setUseSiteAccess( 'admin' );
            $script->initialize();
            eZExecution::setCleanExit();
            if ( !eZDB::instance()->isConnected() )
                throw new RuntimeException( 'no database connection' );
            self::$script = $script;
        }
        catch ( Throwable $e )
        {
            self::$bootError = $e->getMessage();
        }
    }

    public function setUp(): void
    {
        parent::setUp();
        if ( self::$bootError !== null )
            $this->markTestSkipped( 'Kernel not available: ' . self::$bootError );
        $class = eZContentClass::fetchByIdentifier( self::CLASS_IDENTIFIER );
        if ( !$class || !$class->fetchAttributeByIdentifier( self::ATTRIBUTE_IDENTIFIER ) )
            $this->markTestSkipped( 'No "video" class with a "file" attribute in this database' );
        $admin = eZUser::fetchByName( 'admin' );
        if ( $admin instanceof eZUser )
            eZUser::setCurrentlyLoggedInUser( $admin, $admin->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );

        $mediaRoot = (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'MediaRootNode' );
        $folder = eZContentFunctions::createAndPublishObject( array(
            'parent_node_id' => $mediaRoot, 'class_identifier' => 'folder',
            'attributes' => array( 'name' => 'eZMediaSharedFileRemovalTest ' . uniqid() ) ) );
        $this->assertInstanceOf( 'eZContentObject', $folder, 'test folder created' );
        $this->folderNodeID = (int)$folder->attribute( 'main_node_id' );
        $this->attributeIDs = array();
        $this->paths = array();
    }

    public function tearDown(): void
    {
        if ( $this->folderNodeID && eZContentObjectTreeNode::fetch( $this->folderNodeID ) )
        {
            eZContentOperationCollection::deleteObject( array( $this->folderNodeID ), false );
            eZContentObject::clearCache();
        }
        $leftRows = array();
        foreach ( array_unique( $this->attributeIDs ) as $attributeID )
            foreach ( (array)eZMedia::fetch( $attributeID, null ) as $row )
                $leftRows[] = "$attributeID " . $row->attribute( 'filename' );
        $leftFiles = array();
        foreach ( array_unique( $this->paths ) as $path )
            if ( file_exists( $path ) )
                $leftFiles[] = $path;
        parent::tearDown();
        $this->assertSame( array(), $leftRows, 'no ezmedia row of the test is left' );
        $this->assertSame( array(), $leftFiles, 'no file of the test is left' );
    }

    /** Purging a copy keeps the file the source shares with it */
    public function testPurgeCopyKeepsTheSourceFile()
    {
        $source = $this->createMedia( 'Copy source' );
        $path = $this->filePath( $source );
        $copy = $this->createCopy( $source );
        $this->assertEquals( $path, $this->filePath( $copy ), 'the copy shares the source file' );

        $copy->purge();
        eZContentObject::clearCache();
        $this->assertFileExists( $path, 'the source file is kept' );
        $this->assertNotEmpty( eZMedia::fetch( $this->mediaAttributeID( $source ), null ), 'the source row is kept' );
    }

    /** Purging the source while a copy exists keeps the copy's (shared) file */
    public function testPurgeSourceKeepsTheCopyFile()
    {
        $source = $this->createMedia( 'Purged source' );
        $path = $this->filePath( $source );
        $copy = $this->createCopy( $source );

        $source->purge();
        eZContentObject::clearCache();
        $this->assertFileExists( $path, 'the copy file is kept' );
        $this->assertEquals( $path, $this->filePath( $copy ) );
    }

    /**
     * Removing one version: a version sharing the file with another version of the same object
     * keeps it, and a version whose file another object's row names (a copy) keeps it too.
     */
    public function testVersionPurgeKeepsSharedFiles()
    {
        $object = $this->createNewVersion( $this->createMedia( 'Version source' ) );
        $path = $this->filePath( $object );
        $copy = $this->createCopy( $object );

        $object->version( 1 )->removeThis();
        $this->assertFileExists( $path, 'removing version 1 keeps the file version 2 names' );

        // The copy's versions go one by one; the source still names the file
        $copyAttribute = $this->mediaAttribute( $copy );
        $copyAttribute->dataType()->deleteStoredObjectAttribute( $copyAttribute, $copyAttribute->attribute( 'version' ) );
        $this->assertFileExists( $path, 'removing the copy\'s version keeps the source file' );
        $this->assertNotEmpty( eZMedia::fetch( $this->mediaAttributeID( $object ), null ), 'the source row is kept' );
    }

    /** A plain object with a file of its own: purging it deletes its file and row */
    public function testPurgeSingleObjectDeletesItsFile()
    {
        $object = $this->createMedia( 'Single' );
        $attributeID = $this->mediaAttributeID( $object );
        $path = $this->filePath( $object );
        $this->assertFileExists( $path );

        $object->purge();
        eZContentObject::clearCache();
        $this->assertFileDoesNotExist( $path, 'the only row naming the file is gone, so is the file' );
        $this->assertEmpty( eZMedia::fetch( $attributeID, null ) );
    }

    protected function createMedia( $name )
    {
        $object = eZContentFunctions::createAndPublishObject( array(
            'parent_node_id' => $this->folderNodeID, 'class_identifier' => self::CLASS_IDENTIFIER,
            'attributes' => array( 'name' => $name, self::ATTRIBUTE_IDENTIFIER => self::MEDIA_FILE_PATH ) ) );
        $this->assertInstanceOf( 'eZContentObject', $object, "media object '$name' created" );
        $object = $this->forceFetchContentObject( $object->attribute( 'id' ) );
        $this->assertFileExists( $this->filePath( $object ), 'the media file is stored' );
        return $object;
    }

    protected function createNewVersion( eZContentObject $object )
    {
        $this->assertTrue( eZContentFunctions::updateAndPublishObject( $object,
            array( 'attributes' => array( 'name' => $object->attribute( 'name' ) . ' v2' ) ) ) );
        $object = $this->forceFetchContentObject( $object->attribute( 'id' ) );
        $this->filePath( $object );
        return $object;
    }

    /** As content/copy does it: all versions, published below the test folder */
    protected function createCopy( eZContentObject $sourceObject )
    {
        $db = eZDB::instance();
        $db->begin();
        $newObject = $sourceObject->copy();
        $newObject->setAttribute( 'section_id', 0 );
        $newObject->store();
        $curVersion = $newObject->attribute( 'current_version' );
        foreach ( $newObject->attribute( 'current' )->attribute( 'node_assignments' ) as $assignment )
            $assignment->purge();
        eZNodeAssignment::create( array( 'contentobject_id' => $newObject->attribute( 'id' ), 'contentobject_version' => $curVersion,
                                         'parent_node' => $this->folderNodeID, 'is_main' => 1 ) )->store();
        eZOperationHandler::execute( 'content', 'publish', array( 'object_id' => $newObject->attribute( 'id' ), 'version' => $curVersion ) );
        $db->commit();

        $copy = $this->forceFetchContentObject( $newObject->attribute( 'id' ) );
        $this->filePath( $copy );
        return $copy;
    }

    protected function forceFetchContentObject( $contentObjectId )
    {
        eZContentObject::clearCache( $contentObjectId );
        return eZContentObject::fetch( $contentObjectId );
    }

    protected function mediaAttribute( eZContentObject $object )
    {
        $dataMap = $object->fetchDataMap();
        $this->assertArrayHasKey( self::ATTRIBUTE_IDENTIFIER, $dataMap );
        return $dataMap[self::ATTRIBUTE_IDENTIFIER];
    }

    /** The media attribute id of $object, remembered with its file paths for the leftover check */
    protected function mediaAttributeID( eZContentObject $object )
    {
        $id = (int)$this->mediaAttribute( $object )->attribute( 'id' );
        $this->attributeIDs[] = $id;
        foreach ( (array)eZMedia::fetch( $id, null ) as $row )
            $this->paths[] = $row->attribute( 'filepath' );
        return $id;
    }

    /** The stored file of the current version's media row */
    protected function filePath( eZContentObject $object )
    {
        $this->mediaAttributeID( $object );
        $media = $this->mediaAttribute( $object )->content();
        $this->assertInstanceOf( 'eZMedia', $media );
        $path = $media->attribute( 'filepath' );
        $this->paths[] = $path;
        return $path;
    }
}
