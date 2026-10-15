<?php
/**
 * The server side of ezmultiupload's upload page (expMultiuploadService, ezmultiupload 6.1): the input checks, the
 * form token, the permission pre-check and the duplicate check before an upload, with nodes, classes and stored
 * files held in memory (a subclass replaces the methods that read the database).
 *
 *  MU-01 - positiveId() takes only positive integers: no sign, no leading zero, no spaces, no fractions, no arrays
 *  MU-02 - idList() takes a comma-separated list or an array of positive ids, drops duplicates, refuses any bad one,
 *          an empty list and more than MAX_IDS
 *  MU-03 - cleanText() removes control characters, trims, cuts at MAX_TEXT and takes only strings
 *  MU-04 - tokenValid(): the session's token only (constant-time compare); no token in use: true
 *  MU-05 - requestAllowed(): POST only, not from another site, with the token
 *  MU-06 - parseSize() and smallestLimit(): php.ini sizes and the smallest limit that is set
 *  MU-07 - typeAllowed() and freeName()
 *  MU-08 - check(): the permission pre-check per file: a class the user may not create there is refused with the
 *          reason, the class comes from upload.ini (jpg: image, pdf: file), a required alternative text is reported,
 *          a file over the class's size limit is refused, a type outside the location's list is refused
 *  MU-09 - check(): a file with the same name (any case) and size in the location is a duplicate with a free name to
 *          rename it to; the same name with another size is not
 *  MU-10 - check() for a location files cannot be uploaded to: no file is looked at, the location's reason is given
 *  MU-11 - the templates of the admin4 design carry no inline CSS, and the page's script writes no style
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/ezmultiupload/expMultiuploadServiceTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

$expMultiuploadClass = dirname( __DIR__, 4 ) . '/extension/ezmultiupload/classes/expmultiuploadservice.php';
if ( is_file( $expMultiuploadClass ) )
    require_once $expMultiuploadClass;
unset( $expMultiuploadClass );

if ( class_exists( 'expMultiuploadService' ) )
{
    /** A node held in memory */
    class expMultiuploadServiceTestNode extends eZContentObjectTreeNode
    {
        public $values = array();
        public $readable = true;
        public $creatable = true;

        public function __construct( array $values )
        {
            $this->values = $values;
        }

        function attribute( $name, $noFunction = false )
        {
            return isset( $this->values[$name] ) ? $this->values[$name] : null;
        }

        function canRead()
        {
            return $this->readable;
        }

        function canCreate()
        {
            return $this->creatable;
        }
    }

    /** A content class held in memory */
    class expMultiuploadServiceTestClass extends eZContentClass
    {
        public $values = array();

        public function __construct( array $values )
        {
            $this->values = $values;
        }

        function attribute( $name, $noFunction = false )
        {
            return isset( $this->values[$name] ) ? $this->values[$name] : null;
        }
    }

    /** The service with the database parts in memory */
    class expMultiuploadServiceTestService extends expMultiuploadService
    {
        public static $nodes = array();
        public static $token = null;
        public static $creatable = array();      // class identifier => bool
        public static $altRequired = array();    // class identifier => bool
        public static $classMax = array();       // class identifier => bytes
        public static $children = array();       // stored files below the node
        public static $childFilesAsked = false;

        protected static function expectedToken()
        {
            return self::$token;
        }

        protected static function fetchNode( $nodeID )
        {
            return isset( self::$nodes[$nodeID] ) ? self::$nodes[$nodeID] : null;
        }

        protected static function fetchClassByIdentifier( $identifier )
        {
            return new expMultiuploadServiceTestClass( array( 'id' => crc32( $identifier ) % 1000, 'identifier' => $identifier, 'name' => ucfirst( $identifier ) ) );
        }

        protected static function canCreateClass( $node, $class )
        {
            $id = $class->attribute( 'identifier' );
            return isset( self::$creatable[$id] ) ? self::$creatable[$id] : true;
        }

        protected static function altTextRequired( $class )
        {
            $id = $class->attribute( 'identifier' );
            return !empty( self::$altRequired[$id] );
        }

        protected static function classMaxSize( $class )
        {
            $id = $class->attribute( 'identifier' );
            return isset( self::$classMax[$id] ) ? self::$classMax[$id] : 0;
        }

        protected static function isContainer( $node )
        {
            return !empty( $node->values['is_container'] );
        }

        protected static function childFiles( $node )
        {
            self::$childFilesAsked = true;
            return self::$children;
        }

        protected static function pathNames( $node )
        {
            return array( 'Media', (string)$node->attribute( 'name' ) );
        }

        protected static function url( $path )
        {
            return '/admin/' . $path;
        }
    }
}

class expMultiuploadServiceTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $cwd;

    protected function setUp(): void
    {
        if ( !class_exists( 'expMultiuploadService' ) )
            self::markTestSkipped( 'ezmultiupload 6.1 or later is not installed.' );
        $this->cwd = getcwd();
        chdir( dirname( __DIR__, 4 ) );
        $ini = eZINI::instance( 'ezmultiupload.ini' );
        $ini->setReadOnlySettingsCheck( false );
        $ini->setVariable( 'MultiUploadSettings', 'AvailableClasses', array( 'folder', 'gallery' ) );
        $ini->setVariable( 'MultiUploadSettings', 'AvailableSubtreeNode', array() );
        $ini->setVariable( 'MultiUploadSettings', 'MaxFileSize', '64M' );
        $ini->setVariable( 'FileTypeSettings_folder', 'FileType', array( '*.*' ) );
        $ini->setVariable( 'FileTypeSettings_gallery', 'FileType', array( '*.jpg', '*.png' ) );
        expMultiuploadServiceTestService::$nodes = array(
            100 => new expMultiuploadServiceTestNode( array( 'node_id' => 100, 'name' => 'Folder A', 'class_identifier' => 'folder', 'contentclass_id' => 1 ) ),
            101 => new expMultiuploadServiceTestNode( array( 'node_id' => 101, 'name' => 'Gallery', 'class_identifier' => 'gallery', 'contentclass_id' => 2 ) ),
            102 => new expMultiuploadServiceTestNode( array( 'node_id' => 102, 'name' => 'Article', 'class_identifier' => 'article', 'contentclass_id' => 3 ) ),
        );
        expMultiuploadServiceTestService::$token = null;
        expMultiuploadServiceTestService::$creatable = array();
        expMultiuploadServiceTestService::$altRequired = array();
        expMultiuploadServiceTestService::$classMax = array();
        expMultiuploadServiceTestService::$children = array();
        expMultiuploadServiceTestService::$childFilesAsked = false;
    }

    protected function tearDown(): void
    {
        if ( $this->cwd !== null )
        {
            eZINI::resetInstance( 'ezmultiupload.ini' );
            chdir( $this->cwd );
        }
    }

    /** MU-01 */
    public function testPositiveIdTakesOnlyPositiveIntegers()
    {
        $this->assertSame( 43, expMultiuploadService::positiveId( '43' ) );
        $this->assertSame( 7, expMultiuploadService::positiveId( 7 ) );
        $this->assertSame( 123456789012345678, expMultiuploadService::positiveId( '123456789012345678' ) );
        foreach ( array( '0', '-1', '+5', '05', ' 5', '5 ', '1e3', '1.5', '0x1f', '', '12345678901234567890', '5;', "5\n" ) as $bad )
            $this->assertSame( 0, expMultiuploadService::positiveId( $bad ), var_export( $bad, true ) );
        foreach ( array( 0, -3, 1.5, null, true, array( 5 ) ) as $bad )
            $this->assertSame( 0, expMultiuploadService::positiveId( $bad ), var_export( $bad, true ) );
    }

    /** MU-02 */
    public function testIdListRefusesAnyBadIdAndTooManyIds()
    {
        $this->assertSame( array( 3, 4 ), expMultiuploadService::idList( '3,4,3' ) );
        $this->assertSame( array( 3, 4 ), expMultiuploadService::idList( ' 3 , 4' ) );
        $this->assertSame( array( 8, 9 ), expMultiuploadService::idList( array( '8', 9 ) ) );
        $this->assertFalse( expMultiuploadService::idList( '3,x' ) );
        $this->assertFalse( expMultiuploadService::idList( '3,,4' ) );
        $this->assertFalse( expMultiuploadService::idList( '' ) );
        $this->assertFalse( expMultiuploadService::idList( array() ) );
        $this->assertFalse( expMultiuploadService::idList( array( array( 3 ) ) ) );
        $this->assertFalse( expMultiuploadService::idList( null ) );
        $this->assertFalse( expMultiuploadService::idList( implode( ',', range( 1, expMultiuploadService::MAX_IDS + 1 ) ) ) );
        $this->assertCount( expMultiuploadService::MAX_IDS, expMultiuploadService::idList( implode( ',', range( 1, expMultiuploadService::MAX_IDS ) ) ) );
    }

    /** MU-03 */
    public function testCleanText()
    {
        $this->assertSame( 'A b', expMultiuploadService::cleanText( "  A\x00b\n " ) );
        $this->assertSame( 255, mb_strlen( expMultiuploadService::cleanText( str_repeat( 'ä', 300 ) ), 'UTF-8' ) );
        $this->assertSame( '', expMultiuploadService::cleanText( array( 'x' ) ) );
        $this->assertSame( '', expMultiuploadService::cleanText( null ) );
        $this->assertSame( '<b>&', expMultiuploadService::cleanText( '<b>&' ), 'escaping is the output\'s job' );
    }

    /** MU-04 */
    public function testTokenValidComparesWithTheSessionToken()
    {
        expMultiuploadServiceTestService::$token = 'abc123';
        $this->assertTrue( expMultiuploadServiceTestService::tokenValid( 'abc123' ) );
        $this->assertFalse( expMultiuploadServiceTestService::tokenValid( 'abc124' ) );
        $this->assertFalse( expMultiuploadServiceTestService::tokenValid( '' ) );
        $this->assertFalse( expMultiuploadServiceTestService::tokenValid( null ) );
        $this->assertFalse( expMultiuploadServiceTestService::tokenValid( array( 'abc123' ) ) );
        expMultiuploadServiceTestService::$token = null;
        $this->assertTrue( expMultiuploadServiceTestService::tokenValid( null ), 'no form token in use' );
    }

    /** MU-05 */
    public function testRequestAllowed()
    {
        expMultiuploadServiceTestService::$token = 't';
        $this->assertTrue( expMultiuploadServiceTestService::requestAllowed( 'POST', null, 't' ) );
        $this->assertTrue( expMultiuploadServiceTestService::requestAllowed( 'post', 'same-origin', 't' ) );
        $this->assertIsString( expMultiuploadServiceTestService::requestAllowed( 'GET', null, 't' ) );
        $this->assertIsString( expMultiuploadServiceTestService::requestAllowed( 'POST', 'cross-site', 't' ) );
        $this->assertIsString( expMultiuploadServiceTestService::requestAllowed( 'POST', 'same-origin', null ) );
        $this->assertIsString( expMultiuploadServiceTestService::requestAllowed( 'POST', 'same-origin', 'u' ) );
    }

    /** MU-06 */
    public function testSizes()
    {
        $this->assertSame( 64 * 1024 * 1024, expMultiuploadService::parseSize( '64M' ) );
        $this->assertSame( 512 * 1024, expMultiuploadService::parseSize( '512k' ) );
        $this->assertSame( 1024 * 1024 * 1024, expMultiuploadService::parseSize( '1G' ) );
        $this->assertSame( 1000, expMultiuploadService::parseSize( '1000' ) );
        $this->assertSame( 0, expMultiuploadService::parseSize( '' ) );
        $this->assertSame( 0, expMultiuploadService::parseSize( 'lots' ) );
        $this->assertSame( 5, expMultiuploadService::smallestLimit( array( 0, 9, 5 ) ) );
        $this->assertSame( 0, expMultiuploadService::smallestLimit( array( 0, 0 ) ) );
        $this->assertLessThanOrEqual( 64 * 1024 * 1024, expMultiuploadService::maxFileSize() );
        $this->assertGreaterThan( 0, expMultiuploadService::maxFileSize() );
        $this->assertSame( '64 MB', expMultiuploadService::sizeText( 64 * 1024 * 1024 ) );
        $this->assertSame( '1.5 kB', expMultiuploadService::sizeText( 1536 ) );
    }

    /** MU-07 */
    public function testTypesAndFreeNames()
    {
        $this->assertTrue( expMultiuploadService::typeAllowed( 'a.PDF', array() ) );
        $this->assertTrue( expMultiuploadService::typeAllowed( 'a.pdf', array( '*.*' ) ) );
        $this->assertTrue( expMultiuploadService::typeAllowed( 'a.JPG', array( '*.jpg' ) ) );
        $this->assertFalse( expMultiuploadService::typeAllowed( 'a.pdf', array( '*.jpg', '*.png' ) ) );
        $this->assertFalse( expMultiuploadService::typeAllowed( 'jpg', array( '*.jpg' ) ) );
        $this->assertSame( 'photo-2.jpg', expMultiuploadService::freeName( 'photo.jpg', array( 'photo.jpg' ) ) );
        $this->assertSame( 'photo-3.jpg', expMultiuploadService::freeName( 'photo.jpg', array( 'photo.jpg', 'photo-2.jpg' ) ) );
        $this->assertSame( 'README-2', expMultiuploadService::freeName( 'README', array( 'readme' ) ) );
        $this->assertSame( '.htaccess-2', expMultiuploadService::freeName( '.htaccess', array() ) );
    }

    /** MU-08 */
    public function testCheckIsThePermissionPreCheck()
    {
        expMultiuploadServiceTestService::$creatable = array( 'file' => false );
        expMultiuploadServiceTestService::$altRequired = array( 'image' => true );
        expMultiuploadServiceTestService::$classMax = array( 'image' => 1024 * 1024 );
        $check = expMultiuploadServiceTestService::check( 100, array(
            array( 'name' => 'photo.jpg', 'size' => 1000 ),
            array( 'name' => 'report.pdf', 'size' => 1000 ),
            array( 'name' => 'huge.jpg', 'size' => 2 * 1024 * 1024 ),
            array( 'name' => '', 'size' => 1 ),
            'not a file',
        ) );
        $this->assertTrue( $check['ok'] );
        $this->assertTrue( $check['location']['allowed'] );
        $this->assertSame( 'Media / Folder A', $check['location']['pathText'] );
        list( $photo, $pdf, $huge, $nameless, $junk ) = $check['files'];
        $this->assertSame( 'image', $photo['class'] );
        $this->assertTrue( $photo['allowed'] );
        $this->assertTrue( $photo['altRequired'] );
        $this->assertSame( 'file', $pdf['class'] );
        $this->assertFalse( $pdf['allowed'] );
        $this->assertStringContainsString( 'File', $pdf['reason'] );
        $this->assertFalse( $huge['allowed'] );
        $this->assertStringContainsString( '1 MB', $huge['reason'] );
        $this->assertFalse( $nameless['allowed'] );
        $this->assertFalse( $junk['allowed'] );

        // the gallery takes only *.jpg and *.png
        $gallery = expMultiuploadServiceTestService::check( 101, array( array( 'name' => 'notes.txt', 'size' => 5 ), array( 'name' => 'a.png', 'size' => 5 ) ) );
        $this->assertFalse( $gallery['files'][0]['allowed'] );
        $this->assertTrue( $gallery['files'][1]['allowed'] );
        $this->assertSame( array( '*.jpg', '*.png' ), $gallery['fileTypes'] );

        $tooMany = expMultiuploadServiceTestService::check( 100, array_fill( 0, expMultiuploadService::MAX_FILES + 1, array( 'name' => 'a.txt', 'size' => 1 ) ) );
        $this->assertFalse( $tooMany['ok'] );
    }

    /** MU-09 */
    public function testCheckFindsFilesThatAreThereAlready()
    {
        expMultiuploadServiceTestService::$children = array(
            array( 'name' => 'Photo.JPG', 'size' => 677, 'nodeId' => 500, 'title' => 'Photo', 'url' => '/admin/content/view/full/500' ),
            array( 'name' => 'photo-2.jpg', 'size' => 10, 'nodeId' => 501, 'title' => 'Photo 2', 'url' => '/admin/content/view/full/501' ),
        );
        $check = expMultiuploadServiceTestService::check( 100, array( array( 'name' => 'photo.jpg', 'size' => 677 ), array( 'name' => 'photo.jpg', 'size' => 678 ) ) );
        $dup = $check['files'][0]['duplicate'];
        $this->assertSame( 500, $dup['nodeId'] );
        $this->assertSame( 'photo-3.jpg', $dup['suggestion'] );
        $this->assertNull( $check['files'][1]['duplicate'], 'another size is another file' );
    }

    /** MU-10 */
    public function testCheckOfALocationThatTakesNoUploads()
    {
        $check = expMultiuploadServiceTestService::check( 102, array( array( 'name' => 'a.jpg', 'size' => 1 ) ) );
        $this->assertFalse( $check['location']['allowed'] );
        $this->assertNotSame( '', $check['location']['reason'] );
        $this->assertSame( array(), $check['files'] );
        $this->assertFalse( expMultiuploadServiceTestService::$childFilesAsked );

        expMultiuploadServiceTestService::$nodes[100]->creatable = false;
        $refused = expMultiuploadServiceTestService::check( 100, array() );
        $this->assertFalse( $refused['location']['allowed'] );

        $missing = expMultiuploadServiceTestService::check( 999, array() );
        $this->assertFalse( $missing['location']['allowed'] );
    }

    /** MU-11 */
    public function testNoInlineCss()
    {
        $root = dirname( __DIR__, 4 ) . '/extension/ezmultiupload';
        foreach ( glob( $root . '/design/admin4/templates/ezmultiupload/*.tpl' ) as $file )
        {
            $text = preg_replace( '/\{\*.*?\*\}/s', '', file_get_contents( $file ) );
            $this->assertDoesNotMatchRegularExpression( '/<style\b|\sstyle\s*=/i', $text, basename( $file ) );
        }
        $script = file_get_contents( $root . '/design/standard/javascript/expmultiupload.js' );
        $this->assertDoesNotMatchRegularExpression( '/\.style\.|style=|cssText/', $script );
        $css = file_get_contents( $root . '/design/standard/stylesheets/expmultiupload.css' );
        $this->assertDoesNotMatchRegularExpression( '/#[0-9a-f]{3,8}\b|rgba?\(/i', $css, 'colours come from the tokens' );
    }
}
