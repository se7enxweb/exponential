<?php
/**
 * A copy of an object with an image owns its image files.
 *
 * eZContentObject::copy() used to keep the source attribute as the owner in the copied image
 * XML, and publishing the copy re-pointed the source's ezimagefile rows at the copy's paths.
 * Removing the copy then left rows behind and could delete the source's files.
 *
 * These tests run against the installation's own database and storage, with the kernel started
 * once (eZScript, the admin siteaccess): no test database, no shared cache is rebuilt from
 * other data. Each test creates a folder of its own below the media root, with image objects of
 * the shipped "image" class, and removes the folder with everything in it afterwards; the last
 * assertions of each test are that none of its rows or files is left.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/datatypes/ezimage/eZImageCopyOwnFilesTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class eZImageCopyOwnFilesTest extends PHPUnit\Framework\TestCase
{
    const IMAGE_FILE_PATH = 'tests/tests/kernel/datatypes/ezimage/ezimagetype_regression_issue14983.png';

    /** @var eZScript|null */
    protected static $script = null;

    /** @var string|null why the kernel could not be started */
    protected static $bootError = null;

    /** @var int the test's own folder node */
    protected $folderNodeID = 0;

    /** @var int[] every image attribute id the test made, checked for leftovers */
    protected $attributeIDs = array();

    /** @var string[] every file path the test's rows named, checked for leftovers */
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
        if ( !eZContentClass::fetchByIdentifier( 'image' ) )
            $this->markTestSkipped( 'No "image" class in this database' );
        $admin = eZUser::fetchByName( 'admin' );
        if ( $admin instanceof eZUser )
            eZUser::setCurrentlyLoggedInUser( $admin, $admin->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );

        $mediaRoot = (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'MediaRootNode' );
        $folder = eZContentFunctions::createAndPublishObject( array(
            'parent_node_id' => $mediaRoot, 'class_identifier' => 'folder',
            'attributes' => array( 'name' => 'eZImageCopyOwnFilesTest ' . uniqid() ) ) );
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
            foreach ( $this->rows( $attributeID ) as $path )
                $leftRows[] = "$attributeID $path";
        $leftFiles = array();
        foreach ( array_unique( $this->paths ) as $path )
            if ( file_exists( $path ) )
                $leftFiles[] = $path;
        parent::tearDown();
        $this->assertSame( array(), $leftRows, 'no ezimagefile row of the test is left' );
        $this->assertSame( array(), $leftFiles, 'no file of the test is left' );
    }

    /**
     * Copy: the XML of every copied version names the copy's attribute as the owner, its
     * rows name only its own (existing) files, and the source's rows do not change.
     */
    public function testCopyOwnsItsImageFiles()
    {
        $source = $this->createNewVersion( $this->createImage( 'Copy source' ) );
        $sourceAttributeID = $this->imageAttributeID( $source );
        $this->generateAlias( $source, 'medium' );
        $sourceRows = $this->rows( $sourceAttributeID );
        $this->assertNotEmpty( $sourceRows );

        $copy = $this->createCopy( $source );
        $copyAttributeID = $this->imageAttributeID( $copy );
        $this->assertNotEquals( $sourceAttributeID, $copyAttributeID );

        foreach ( $this->imageAttributes( $copy ) as $attribute )
        {
            $doc = simplexml_load_string( $attribute->attribute( 'data_text' ) );
            $this->assertEquals( $copyAttributeID, (string)$doc->original['attribute_id'], 'copied XML names the copy as the owner' );
            $this->assertEquals( $attribute->attribute( 'version' ), (string)$doc->original['attribute_version'] );
            $this->assertFileExists( (string)$doc['url'] );
        }

        $copyRows = $this->rows( $copyAttributeID );
        $this->assertNotEmpty( $copyRows );
        foreach ( $copyRows as $path )
        {
            $this->assertFileExists( $path );
            $this->assertNotContains( $path, $sourceRows, 'the copy has no row for a source file' );
        }
        $this->assertEquals( $sourceRows, $this->rows( $sourceAttributeID ), 'the source rows are unchanged' );
    }

    /**
     * Removing the copy (purge, trash then purge, delete) removes exactly its rows and files;
     * the source keeps all of its rows and files.
     */
    public function testRemoveCopyLeavesTheSourceIntact()
    {
        $source = $this->createNewVersion( $this->createImage( 'Remove source' ) );
        $sourceAttributeID = $this->imageAttributeID( $source );
        $this->generateAlias( $source, 'medium' );
        $sourceRows = $this->rows( $sourceAttributeID );

        foreach ( array( 'purge', 'trash', 'delete' ) as $how )
        {
            $copy = $this->createCopy( $source );
            $copyAttributeID = $this->imageAttributeID( $copy );
            $this->generateAlias( $copy, 'small' );
            $copyRows = $this->rows( $copyAttributeID );
            $this->assertNotEmpty( $copyRows );

            if ( $how == 'purge' )
                $copy->purge();
            elseif ( $how == 'trash' )
            {
                eZContentOperationCollection::deleteObject( array( $copy->attribute( 'main_node_id' ) ), true );
                $copy = $this->forceFetchContentObject( $copy->attribute( 'id' ) );
                $this->assertEquals( $sourceRows, $this->rows( $sourceAttributeID ), "$how: trashing keeps the source rows" );
                foreach ( $this->rows( $copyAttributeID ) as $path )
                    $this->paths[] = $path;
                $copy->purge();
            }
            else
                eZContentOperationCollection::deleteObject( array( $copy->attribute( 'main_node_id' ) ), false );
            eZContentObject::clearCache();

            $this->assertEquals( array(), $this->rows( $copyAttributeID ), "$how: no row of the copy is left" );
            foreach ( $copyRows as $path )
                $this->assertFileDoesNotExist( $path, "$how: no file of the copy is left" );
            $this->assertEquals( $sourceRows, $this->rows( $sourceAttributeID ), "$how: the source rows are unchanged" );
            foreach ( $sourceRows as $path )
                $this->assertFileExists( $path, "$how: the source files exist" );
        }
    }

    /**
     * A new version of the same object keeps sharing the image of the first one, as before,
     * and removing the old version keeps the file the new one uses.
     */
    public function testNewVersionSharesTheImage()
    {
        $object = $this->createImage( 'Version source' );
        $attributeID = $this->imageAttributeID( $object );
        $rowsBefore = $this->rows( $attributeID );
        $object = $this->createNewVersion( $object );

        $attributes = $this->imageAttributes( $object );
        $this->assertCount( 2, $attributes );
        $v1 = simplexml_load_string( $attributes[0]->attribute( 'data_text' ) );
        $v2 = simplexml_load_string( $attributes[1]->attribute( 'data_text' ) );
        $this->assertEquals( (string)$v1->original['attribute_id'], (string)$v2->original['attribute_id'] );
        $this->assertEquals( (string)$v1->original['attribute_version'], (string)$v2->original['attribute_version'] );
        $this->assertEquals( (string)$v1['url'], (string)$v2['url'], 'the new version names the same file' );
        $this->assertEquals( $rowsBefore, $this->rows( $attributeID ), 'no new file for the new version' );

        $object->version( 1 )->removeThis();
        $this->assertFileExists( (string)$v2['url'] );
        $this->assertContains( (string)$v2['url'], $this->rows( $attributeID ) );
    }

    /**
     * moveFilepath() moves the rows of the same object's attributes along (translations
     * sharing a moved file), never a row of another object; with $followSharedRows false
     * (the file was linked, not moved) only the caller's row.
     */
    public function testMoveFilepathKeepsRowsOfOtherObjects()
    {
        $aID = $this->imageAttributeID( $this->createImage( 'Move A' ) );
        $bID = $this->imageAttributeID( $this->createImage( 'Move B' ) );
        // Rows only, no files: paths no installation has
        $old = 'var/storage/images/ezimagecopyownfilestest/moved-old.png';
        $new = 'var/storage/images/ezimagecopyownfilestest/moved-new.png';
        $linked = 'var/storage/images/ezimagecopyownfilestest/linked-by-b.png';
        eZImageFile::appendFilepath( $aID, $old, true );
        eZImageFile::appendFilepath( $bID, $old, true );

        eZImageFile::moveFilepath( $aID, $old, $new );
        $this->assertContains( $new, $this->rows( $aID ) );
        $this->assertNotContains( $old, $this->rows( $aID ) );
        $this->assertContains( $old, $this->rows( $bID ), 'another object keeps its row' );
        $this->assertNotContains( $new, $this->rows( $bID ) );

        eZImageFile::moveFilepath( $bID, $old, $linked, false );
        $this->assertContains( $linked, $this->rows( $bID ) );
        $this->assertNotContains( $old, $this->rows( $bID ) );
        $this->assertContains( $new, $this->rows( $aID ), 'A keeps its row' );
        $this->assertNotContains( $linked, $this->rows( $aID ) );

        // No XML names these paths, so removing the objects would not find them
        eZImageFile::removeFilepath( $aID, $new );
        eZImageFile::removeFilepath( $bID, $linked );
    }

    protected function createImage( $name )
    {
        $object = eZContentFunctions::createAndPublishObject( array(
            'parent_node_id' => $this->folderNodeID, 'class_identifier' => 'image',
            'attributes' => array( 'name' => $name, 'image' => self::IMAGE_FILE_PATH . '|' . $name ) ) );
        $this->assertInstanceOf( 'eZContentObject', $object, "image object '$name' created" );
        $object = $this->forceFetchContentObject( $object->attribute( 'id' ) );
        $this->imageAttributeID( $object );
        return $object;
    }

    protected function createNewVersion( eZContentObject $object )
    {
        $this->assertTrue( eZContentFunctions::updateAndPublishObject( $object,
            array( 'attributes' => array( 'name' => $object->attribute( 'name' ) . ' v2' ) ) ) );
        return $this->forceFetchContentObject( $object->attribute( 'id' ) );
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
        $this->imageAttributeID( $copy );
        return $copy;
    }

    protected function generateAlias( eZContentObject $object, $aliasName )
    {
        $dataMap = $object->fetchDataMap();
        $alias = $dataMap['image']->attribute( 'content' )->imageAlias( $aliasName );
        $this->assertFileExists( $alias['url'] );
        $this->paths[] = $alias['url'];
    }

    protected function forceFetchContentObject( $contentObjectId )
    {
        eZContentObject::clearCache( $contentObjectId );
        return eZContentObject::fetch( $contentObjectId );
    }

    /** The image attribute id of $object, remembered with its paths for the leftover check */
    protected function imageAttributeID( eZContentObject $object )
    {
        $dataMap = $object->fetchDataMap();
        $this->assertArrayHasKey( 'image', $dataMap );
        $id = (int)$dataMap['image']->attribute( 'id' );
        $this->attributeIDs[] = $id;
        foreach ( $this->rows( $id ) as $path )
            $this->paths[] = $path;
        return $id;
    }

    /** @return eZContentObjectAttribute[] the image attribute of every version, oldest first */
    protected function imageAttributes( eZContentObject $object )
    {
        return eZPersistentObject::fetchObjectList( eZContentObjectAttribute::definition(), null,
            array( 'contentobject_id' => $object->attribute( 'id' ), 'data_type_string' => 'ezimage' ), array( 'version' => 'asc' ) );
    }

    /** @return string[] the ezimagefile paths of an attribute, sorted */
    protected function rows( $attributeID )
    {
        $paths = array();
        foreach ( eZImageFile::fetchForContentObjectAttribute( $attributeID, true ) as $row )
            $paths[] = $row->attribute( 'filepath' );
        sort( $paths );
        return $paths;
    }
}
