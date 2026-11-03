<?php
/**
 * "Choose an existing image" of the image field: expImageCopy copies the image of an existing object
 * into the image field of a draft.
 *
 * - the copy is a file of its own, under the edited object's storage path, with the same bytes;
 * - the source object (its stored XML, its files, its ezimagefile rows) does not change;
 * - removing the source afterwards leaves the copy intact;
 * - an empty alternative text takes the source's, a given one is kept;
 * - an object the user may not read, one that does not exist and one without an image are refused,
 *   and the field is left as it was;
 * - the search finds an image object by name, and only objects with an image.
 *
 * Runs against the installation's own database and storage, like eZImageCopyOwnFilesTest: each test
 * creates a folder of its own below the media root and removes it with everything in it afterwards;
 * the last assertions are that none of its files is left.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/datatypes/ezimage/expImageCopyFromObjectTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expImageCopyFromObjectTest extends PHPUnit\Framework\TestCase
{
    const IMAGE_FILE_PATH = 'tests/tests/kernel/datatypes/ezimage/ezimagetype_regression_issue14983.png';

    /** @var eZScript|null */
    protected static $script = null;

    /** @var string|null why the kernel could not be started */
    protected static $bootError = null;

    /** @var int the test's own folder node */
    protected $folderNodeID = 0;

    /** @var eZContentObject|null the test's own folder */
    protected $folder = null;

    /** @var string[] every image file the test made, checked for leftovers */
    protected $paths = array();

    /** @var eZUser|null */
    protected $admin = null;

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
            ezpLiveInstallation::installedSiteDatabase();
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
        $this->admin = eZUser::fetchByName( 'admin' );
        $this->loginAdmin();

        $mediaRoot = (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'MediaRootNode' );
        $this->folder = eZContentFunctions::createAndPublishObject( array(
            'parent_node_id' => $mediaRoot, 'class_identifier' => 'folder',
            'attributes' => array( 'name' => 'expImageCopyFromObjectTest ' . uniqid() ) ) );
        $this->assertInstanceOf( 'eZContentObject', $this->folder, 'test folder created' );
        $this->folderNodeID = (int)$this->folder->attribute( 'main_node_id' );
        $this->paths = array();
    }

    public function tearDown(): void
    {
        $this->loginAdmin();
        if ( $this->folderNodeID && eZContentObjectTreeNode::fetch( $this->folderNodeID ) )
        {
            eZContentOperationCollection::deleteObject( array( $this->folderNodeID ), false );
            eZContentObject::clearCache();
        }
        $leftFiles = array();
        foreach ( array_unique( $this->paths ) as $path )
            if ( file_exists( $path ) )
                $leftFiles[] = $path;
        parent::tearDown();
        $this->assertSame( array(), $leftFiles, 'no file of the test is left' );
    }

    /**
     * The copy is a new file of the edited object, with the source's bytes; the source does not change,
     * and removing it leaves the copy.
     */
    public function testCopyIsAFileOfItsOwnAndTheSourceIsUntouched()
    {
        $source = $this->createImage( 'Pick source ' . uniqid(), 'The source text' );
        $sourceAttribute = expImageCopy::sourceAttribute( $source );
        $this->assertInstanceOf( 'eZContentObjectAttribute', $sourceAttribute );
        $sourceXML = $sourceAttribute->attribute( 'data_text' );
        $sourceURL = $this->originalURL( $sourceAttribute );
        $sourceRows = $this->rows( $sourceAttribute->attribute( 'id' ) );
        $sourceHash = md5_file( $sourceURL );

        $target = $this->createImage( 'Pick target ' . uniqid(), '', false );
        $draftAttribute = $this->draftImageAttribute( $target );

        $this->assertTrue( expImageCopy::copyFromObject( $draftAttribute, $source->attribute( 'id' ) ) );

        $stored = eZContentObjectAttribute::fetch( $draftAttribute->attribute( 'id' ), $draftAttribute->attribute( 'version' ) );
        $copyURL = $this->originalURL( $stored );
        $this->paths[] = $copyURL;
        $this->assertNotSame( '', $copyURL );
        $this->assertNotSame( $sourceURL, $copyURL, 'the copy has a path of its own' );
        $this->assertStringContainsString( '/' . $draftAttribute->attribute( 'id' ) . '/', $copyURL, 'the copy is stored under the edited field' );
        $this->assertStringNotContainsString( '/' . $sourceAttribute->attribute( 'id' ) . '/', $copyURL, 'the copy is not stored under the source field' );
        $this->assertFileExists( $copyURL );
        $this->assertSame( $sourceHash, md5_file( $copyURL ), 'the copy has the bytes of the source' );
        $this->assertContains( $copyURL, $this->rows( $draftAttribute->attribute( 'id' ) ), 'the copy is registered for the edited field' );
        $this->assertSame( 'The source text', $stored->attribute( 'content' )->attribute( 'alternative_text' ), 'an empty alternative text takes the source\'s' );

        $sourceAgain = eZContentObjectAttribute::fetch( $sourceAttribute->attribute( 'id' ), $sourceAttribute->attribute( 'version' ) );
        $this->assertSame( $sourceXML, $sourceAgain->attribute( 'data_text' ), 'the source XML is unchanged' );
        $this->assertSame( $sourceRows, $this->rows( $sourceAttribute->attribute( 'id' ) ), 'the source rows are unchanged' );
        $this->assertSame( $sourceHash, md5_file( $sourceURL ), 'the source file is unchanged' );

        // Removing the source leaves the copy
        eZContentOperationCollection::deleteObject( array( $source->attribute( 'main_node_id' ) ), false );
        eZContentObject::clearCache();
        $this->assertFileExists( $copyURL, 'the copy survives the removal of the source' );
        $this->assertSame( $sourceHash, md5_file( $copyURL ) );
    }

    /**
     * A given alternative text is kept.
     */
    public function testGivenAlternativeTextIsKept()
    {
        $source = $this->createImage( 'Pick alt source ' . uniqid(), 'Source text' );
        $target = $this->createImage( 'Pick alt target ' . uniqid(), '', false );
        $draftAttribute = $this->draftImageAttribute( $target );
        $draftAttribute->attribute( 'content' )->setAttribute( 'alternative_text', 'My own text' );

        $this->assertTrue( expImageCopy::copyFromObject( $draftAttribute, (string)$source->attribute( 'id' ) ) );
        $stored = eZContentObjectAttribute::fetch( $draftAttribute->attribute( 'id' ), $draftAttribute->attribute( 'version' ) );
        $this->paths[] = $this->originalURL( $stored );
        $this->assertSame( 'My own text', $stored->attribute( 'content' )->attribute( 'alternative_text' ) );
    }

    /**
     * A user who may not read the source gets the same answer as for an object that does not exist, and
     * the field does not change.
     */
    public function testUnreadableSourceIsRefused()
    {
        $source = $this->createImage( 'Pick hidden source ' . uniqid(), '' );
        $target = $this->createImage( 'Pick hidden target ' . uniqid(), '', false );
        $draftAttribute = $this->draftImageAttribute( $target );
        $before = $draftAttribute->attribute( 'data_text' );

        // The source goes to the users section, which the anonymous user may not read (in a shipped installation
        // anonymous reads the standard and media sections only); the test then runs as the anonymous user
        $usersSection = eZSection::fetchByIdentifier( 'users', false );
        if ( !is_array( $usersSection ) )
            $this->markTestSkipped( 'No "users" section in this database' );
        eZContentObjectTreeNode::assignSectionToSubTree( (int)$source->attribute( 'main_node_id' ), (int)$usersSection['id'] );
        $anonymous = eZUser::fetch( (int)eZINI::instance()->variable( 'UserSettings', 'AnonymousUserID' ) );
        $this->assertInstanceOf( 'eZUser', $anonymous );
        eZUser::setCurrentlyLoggedInUser( $anonymous, $anonymous->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
        eZContentObject::clearCache();
        $sourceForAnonymous = eZContentObject::fetch( $source->attribute( 'id' ) );
        if ( $sourceForAnonymous->canRead() )
        {
            $this->loginAdmin();
            $this->markTestSkipped( 'The anonymous user may read the users section in this installation' );
        }

        $result = expImageCopy::copyFromObject( $draftAttribute, $source->attribute( 'id' ) );
        $missing = expImageCopy::copyFromObject( $draftAttribute, 2147483000 );
        $this->loginAdmin();

        $this->assertIsString( $result );
        $this->assertSame( $missing, $result, 'refused as an object that does not exist' );
        $stored = eZContentObjectAttribute::fetch( $draftAttribute->attribute( 'id' ), $draftAttribute->attribute( 'version' ) );
        $this->assertSame( $before, $stored->attribute( 'data_text' ), 'the field is unchanged' );
    }

    /**
     * An object without an image and input that is no object id are refused.
     */
    public function testSourceWithoutImageAndBadInputAreRefused()
    {
        $target = $this->createImage( 'Pick plain target ' . uniqid(), '', false );
        $draftAttribute = $this->draftImageAttribute( $target );
        $before = $draftAttribute->attribute( 'data_text' );
        $emptyImage = $this->createImage( 'Pick empty image ' . uniqid(), '', false );

        foreach ( array( $this->folder->attribute( 'id' ), $emptyImage->attribute( 'id' ) ) as $objectID )
        {
            $result = expImageCopy::copyFromObject( $draftAttribute, $objectID );
            $this->assertIsString( $result, "object $objectID has no image" );
            $this->assertSame( ezpI18n::tr( 'kernel/classes/datatypes', 'The selected object has no image.' ), $result );
        }
        foreach ( array( false, '', '0', '-3', '1e3', ' ', array( 'x' ), '12abc', '99999999999' ) as $input )
            $this->assertIsString( expImageCopy::copyFromObject( $draftAttribute, $input ), 'bad input ' . var_export( $input, true ) );

        $stored = eZContentObjectAttribute::fetch( $draftAttribute->attribute( 'id' ), $draftAttribute->attribute( 'version' ) );
        $this->assertSame( $before, $stored->attribute( 'data_text' ), 'the field is unchanged' );
    }

    /**
     * The search finds an image object by its name, and lists only objects with an image.
     */
    public function testSearchFindsImagesByName()
    {
        $marker = 'pickmark' . bin2hex( random_bytes( 4 ) );
        $withImage = $this->createImage( 'Search ' . $marker . ' with', '' );
        $this->createImage( 'Search ' . $marker . ' without', '', false );

        $results = expImageCopy::search( $marker );
        $ids = array();
        foreach ( $results as $result )
        {
            $ids[] = $result['object_id'];
            $this->assertInstanceOf( 'eZContentObjectAttribute', $result['attribute'] );
        }
        $this->assertSame( array( (int)$withImage->attribute( 'id' ) ), $ids, 'only the object with an image is found' );
        $this->assertSame( array(), expImageCopy::search( $marker . ' does not exist' ) );
        $this->assertContains( 'image', expImageCopy::imageClassIdentifiers() );
    }

    protected function loginAdmin()
    {
        if ( $this->admin instanceof eZUser )
            eZUser::setCurrentlyLoggedInUser( $this->admin, $this->admin->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
        eZContentObject::clearCache();
    }

    protected function createImage( $name, $altText, $withFile = true )
    {
        $attributes = array( 'name' => $name );
        if ( $withFile )
            $attributes['image'] = self::IMAGE_FILE_PATH . '|' . $altText;
        $object = eZContentFunctions::createAndPublishObject( array(
            'parent_node_id' => $this->folderNodeID, 'class_identifier' => 'image', 'attributes' => $attributes ) );
        $this->assertInstanceOf( 'eZContentObject', $object, "image object '$name' created" );
        eZContentObject::clearCache( $object->attribute( 'id' ) );
        $object = eZContentObject::fetch( $object->attribute( 'id' ) );
        if ( $withFile )
            $this->paths[] = $this->originalURL( expImageCopy::sourceAttribute( $object ) );
        return $object;
    }

    /**
     * The image field of a new draft of $object, as the edit form has it.
     */
    protected function draftImageAttribute( eZContentObject $object )
    {
        $version = $object->createNewVersion();
        foreach ( $version->contentObjectAttributes() as $attribute )
            if ( $attribute->attribute( 'data_type_string' ) === 'ezimage' )
                return $attribute;
        $this->fail( 'the draft has an image field' );
    }

    protected function originalURL( $attribute )
    {
        $this->assertInstanceOf( 'eZContentObjectAttribute', $attribute );
        $doc = simplexml_load_string( $attribute->attribute( 'data_text' ) );
        return $doc ? (string)$doc['url'] : '';
    }

    protected function rows( $attributeID )
    {
        $paths = array();
        foreach ( eZImageFile::fetchForContentObjectAttribute( $attributeID, true ) as $row )
            $paths[] = $row->attribute( 'filepath' );
        sort( $paths );
        return $paths;
    }
}
