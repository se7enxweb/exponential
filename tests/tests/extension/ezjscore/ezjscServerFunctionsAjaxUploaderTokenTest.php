<?php
/**
 * The AJAX uploader keeps the path of a stored file on the server (ezjscServerFunctionsAjaxUploader): upload()
 * gives the location form a token, bound to the user and the upload target, and preview() takes the file only by
 * that token, once, and only from below the storage directory. upload() asks the handler's canUpload() as
 * uploadForm() and preview() do.
 *
 *  AT-01 - preview() with a path posted as UploadFile and no token: refused, the file is untouched
 *  AT-02 - preview() with an unknown token: refused
 *  AT-03 - a token of another user or another upload target: refused
 *  AT-04 - the token of this user and target: the stored file becomes the object, and the token is used once
 *  AT-05 - a stored path outside the storage directory or with ".." parts is refused even with a valid token
 *  AT-06 - upload() without canUpload(): refused before the file is looked at
 *  AT-07 - the location forms of design/admin and design/admin4 carry the token, not the path
 *  AT-08 - the pending list keeps at most PENDING_MAX entries
 *
 * The handler is a stand-in registered in memory ([AjaxUploader] AjaxUploadHandler[]), the user id comes from a
 * subclass and the session is $_SESSION; no database.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/ezjscore/ezjscServerFunctionsAjaxUploaderTokenTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

$ezjscoreRoot = dirname( __DIR__, 4 ) . '/extension/ezjscore';
require_once $ezjscoreRoot . '/classes/ezjscserverfunctions.php';
require_once $ezjscoreRoot . '/classes/ezjscserverfunctionsajaxuploader.php';
require_once $ezjscoreRoot . '/classes/ajaxuploader/ezpajaxuploaderhandlerinterface.php';
unset( $ezjscoreRoot );

/** Thrown by the stand-in handler's createObject(): the file preview() handed over is recorded first */
class ezjscAjaxUploaderTokenTestCreated extends Exception
{
}

/** The upload functions with a fixed current user */
class ezjscAjaxUploaderTokenTestFunctions extends ezjscServerFunctionsAjaxUploader
{
    public static $userID = 14;

    protected static function currentUserID()
    {
        return self::$userID;
    }
}

/** An upload handler held in memory */
class ezjscAjaxUploaderTokenTestHandler implements ezpAjaxUploaderHandlerInterface
{
    public static $canUpload = true;
    public static $fileInfoAsked = false;
    public static $created = null;

    public function __construct()
    {
    }

    public function canUpload()
    {
        return self::$canUpload;
    }

    public function getFileInfo()
    {
        self::$fileInfoAsked = true;
        throw new RuntimeException( 'no file in this test' );
    }

    public function getContentClass( array $mimeData )
    {
        throw new RuntimeException( 'no class in this test' );
    }

    public function getDefaultParentNodeId( eZContentClass $class )
    {
        return 2;
    }

    /** true: createObject() fails, as for a location the user may not create in */
    public static $createFails = false;

    public function createObject( $file, $location, $name = '' )
    {
        self::$created = array( 'file' => $file, 'contents' => is_file( $file ) ? file_get_contents( $file ) : null );
        if ( self::$createFails )
            throw new RuntimeException( 'create failed' );
        return new eZContentObject( array() );
    }

    /** Called by preview() once the object exists, before the preview template: the test ends here */
    public function serializeObject( eZContentObject $object )
    {
        throw new ezjscAjaxUploaderTokenTestCreated( 'created' );
    }
}

class ezjscServerFunctionsAjaxUploaderTokenTest extends PHPUnit\Framework\TestCase
{
    /** @var string the working directory before the test */
    private $cwd;

    /** @var string a file outside the storage directory */
    private $victim;

    /** @var string a directory below the storage directory */
    private $storedDir;

    /** @var string a file upload() could have stored */
    private $stored;

    /** @var array $_SESSION before the test */
    private $session;

    /**
     * The first session and cluster handler of the process install the kernel's exception handler once: done here,
     * so no test is reported for it.
     */
    public static function setUpBeforeClass(): void
    {
        $cwd = getcwd();
        chdir( dirname( __DIR__, 4 ) );
        eZClusterFileHandler::instance();
        eZHTTPTool::instance()->hasSessionVariable( ezjscServerFunctionsAjaxUploader::PENDING_SESSION_KEY );
        chdir( $cwd );
    }

    protected function setUp(): void
    {
        $this->cwd = getcwd();
        chdir( dirname( __DIR__, 4 ) );
        $ini = eZINI::instance( 'ezjscore.ini' );
        $ini->setReadOnlySettingsCheck( false );
        $ini->setVariable( 'AjaxUploader', 'AjaxUploadHandler', array( 'tokentest' => 'ezjscAjaxUploaderTokenTestHandler' ) );

        $tag = getmypid() . '-' . mt_rand();
        $this->victim = 'var/tmp/phpunit-ajaxuploader-token-' . $tag . '.txt';
        eZDir::mkdir( dirname( $this->victim ), false, true );
        file_put_contents( $this->victim, 'not an upload' );
        $this->storedDir = eZSys::storageDirectory() . '/phpunit-ajaxuploader-token-' . $tag;
        eZDir::mkdir( $this->storedDir, false, true );
        $this->stored = $this->storedDir . '/' . md5( $tag ) . '.txt';
        file_put_contents( $this->stored, 'the uploaded file' );

        $this->session = isset( $_SESSION ) ? $_SESSION : null;
        $_SESSION = array();
        $_POST = array();
        ezjscAjaxUploaderTokenTestFunctions::$userID = 14;
        ezjscAjaxUploaderTokenTestHandler::$canUpload = true;
        ezjscAjaxUploaderTokenTestHandler::$fileInfoAsked = false;
        ezjscAjaxUploaderTokenTestHandler::$created = null;
        ezjscAjaxUploaderTokenTestHandler::$createFails = false;
    }

    protected function tearDown(): void
    {
        eZINI::resetInstance( 'ezjscore.ini' );
        foreach ( array( $this->victim, $this->stored ) as $file )
            if ( is_file( $file ) )
                unlink( $file );
        if ( is_dir( $this->storedDir ) )
            rmdir( $this->storedDir );
        if ( ezjscAjaxUploaderTokenTestHandler::$created !== null && is_file( ezjscAjaxUploaderTokenTestHandler::$created['file'] ) )
            unlink( ezjscAjaxUploaderTokenTestHandler::$created['file'] );
        $_POST = array();
        $_SESSION = $this->session;
        chdir( $this->cwd );
    }

    /** A token for $file as upload() makes it, for user $userID and the handler data $data */
    private function remember( $file, $userID, array $data = array( 'ObjectRelationsAttributeId' => 7, 'Version' => 1 ) )
    {
        $_POST = array( 'AjaxUploadHandlerData' => $data );
        $store = isset( $_SESSION[ezjscServerFunctionsAjaxUploader::PENDING_SESSION_KEY] ) ? $_SESSION[ezjscServerFunctionsAjaxUploader::PENDING_SESSION_KEY] : array();
        list( $store, $token ) = ezjscServerFunctionsAjaxUploader::rememberPending( $store, $file, 'report.txt',
                                                                                 ezjscServerFunctionsAjaxUploader::pendingBinding( array( 'tokentest' ), $userID ) );
        $_SESSION[ezjscServerFunctionsAjaxUploader::PENDING_SESSION_KEY] = $store;
        $_POST = array();
        return $token;
    }

    /** preview() with $post; the exception's message, or 'created' when the handler was asked to create the object */
    private function preview( array $post )
    {
        $_POST = $post + array( 'AjaxUploadHandlerData' => array( 'ObjectRelationsAttributeId' => 7, 'Version' => 1 ),
                                'UploadLocation' => 2, 'UploadName' => 'x' );
        try
        {
            ezjscAjaxUploaderTokenTestFunctions::preview( array( 'tokentest' ) );
            return 'returned';
        }
        catch ( Exception $e )
        {
            return $e->getMessage();
        }
    }

    /** AT-01 */
    public function testAPostedPathIsNotTaken()
    {
        $result = $this->preview( array( 'UploadFile' => $this->victim, 'UploadOriginalFilename' => 'x.txt' ) );
        $this->assertSame( 'Unable to retrieve the uploaded file.', $result );
        $this->assertFileExists( $this->victim );
        $this->assertSame( 'not an upload', file_get_contents( $this->victim ) );
        $this->assertNull( ezjscAjaxUploaderTokenTestHandler::$created );
    }

    /** AT-01, a posted path of a file upload() did store */
    public function testAPostedStoredPathIsNotTakenEither()
    {
        $this->assertSame( 'Unable to retrieve the uploaded file.', $this->preview( array( 'UploadFile' => $this->stored ) ) );
        $this->assertFileExists( $this->stored );
        $this->assertNull( ezjscAjaxUploaderTokenTestHandler::$created );
    }

    /** AT-02 */
    public function testAnUnknownTokenIsRefused()
    {
        $this->remember( $this->stored, 14 );
        foreach ( array( str_repeat( 'a', 32 ), '../../x', array( 'x' ), '' ) as $token )
        {
            $this->assertSame( 'Unable to retrieve the uploaded file.', $this->preview( array( 'UploadToken' => $token, 'UploadFile' => $this->victim ) ) );
        }
        $this->assertFileExists( $this->stored );
        $this->assertFileExists( $this->victim );
        $this->assertNull( ezjscAjaxUploaderTokenTestHandler::$created );
    }

    /** AT-03 */
    public function testATokenOfAnotherUserOrTargetIsRefused()
    {
        $other = $this->remember( $this->stored, 15 );
        $this->assertSame( 'Unable to retrieve the uploaded file.', $this->preview( array( 'UploadToken' => $other ) ) );
        $otherTarget = $this->remember( $this->stored, 14, array( 'ObjectRelationsAttributeId' => 8, 'Version' => 1 ) );
        $this->assertSame( 'Unable to retrieve the uploaded file.', $this->preview( array( 'UploadToken' => $otherTarget ) ) );
        $this->assertFileExists( $this->stored );
        $this->assertNull( ezjscAjaxUploaderTokenTestHandler::$created );
    }

    /** AT-04 */
    public function testTheOwnTokenGivesTheStoredFileOnce()
    {
        $token = $this->remember( $this->stored, 14 );
        $this->assertSame( 'created', $this->preview( array( 'UploadToken' => $token, 'UploadOriginalFilename' => '../../evil.php' ) ) );
        $created = ezjscAjaxUploaderTokenTestHandler::$created;
        $this->assertSame( 'the uploaded file', $created['contents'] );
        $this->assertSame( 'report.txt', basename( $created['file'] ), 'the name is the stored one, not the posted one' );
        $this->assertSame( 'ajaxuploader-' . $token, basename( dirname( $created['file'] ) ), 'a directory of the token\'s own' );
        $this->assertFileDoesNotExist( $created['file'], 'the temporary copy is removed' );
        $this->assertDirectoryDoesNotExist( dirname( $created['file'] ) );
        $this->assertFileDoesNotExist( $this->stored );

        ezjscAjaxUploaderTokenTestHandler::$created = null;
        file_put_contents( $this->stored, 'the uploaded file' );
        $this->assertSame( 'Unable to retrieve the uploaded file.', $this->preview( array( 'UploadToken' => $token ) ), 'used once' );
    }

    /** AT-09, a failed object leaves the stored file and the token, and no temporary copy */
    public function testAFailedObjectKeepsTheTokenAndTheFile()
    {
        $token = $this->remember( $this->stored, 14 );
        ezjscAjaxUploaderTokenTestHandler::$createFails = true;
        $this->assertSame( 'create failed', $this->preview( array( 'UploadToken' => $token ) ) );
        $created = ezjscAjaxUploaderTokenTestHandler::$created;
        $this->assertSame( 'the uploaded file', $created['contents'] );
        $this->assertFileDoesNotExist( $created['file'] );
        $this->assertDirectoryDoesNotExist( dirname( $created['file'] ) );
        $this->assertFileExists( $this->stored );

        // another location: the same token takes the file
        ezjscAjaxUploaderTokenTestHandler::$createFails = false;
        $this->assertSame( 'created', $this->preview( array( 'UploadToken' => $token ) ) );
        $this->assertFileDoesNotExist( $this->stored );
    }

    /** AT-10, a token lives PENDING_LIFETIME seconds; expired and evicted entries give their files up for deletion */
    public function testTokensExpireAndDroppedFilesAreReturned()
    {
        $now = 1760000000;
        list( $store, $token, $dropped ) = ezjscServerFunctionsAjaxUploader::rememberPending( array(), 'old', 'n', 'b', $now );
        $this->assertSame( array(), $dropped );
        $this->assertSame( 'old', ezjscServerFunctionsAjaxUploader::pendingEntry( $store, $token, 'b', $now + ezjscServerFunctionsAjaxUploader::PENDING_LIFETIME - 1 )['file'] );
        $this->assertNull( ezjscServerFunctionsAjaxUploader::pendingEntry( $store, $token, 'b', $now + ezjscServerFunctionsAjaxUploader::PENDING_LIFETIME ), 'expired' );
        $this->assertNull( ezjscServerFunctionsAjaxUploader::pendingEntry( $store, $token, 'b', $now - 1 ), 'made in the future' );
        $this->assertNull( ezjscServerFunctionsAjaxUploader::pendingEntry( array( $token => array( 'file' => 'x', 'binding' => 'b' ) ), $token, 'b', $now ), 'no time' );

        list( $store, , $dropped ) = ezjscServerFunctionsAjaxUploader::rememberPending( $store, 'new', 'n', 'b', $now + ezjscServerFunctionsAjaxUploader::PENDING_LIFETIME );
        $this->assertSame( array( 'old' ), $dropped, 'the expired entry is dropped and its file given up' );
        $this->assertCount( 1, $store );

        $store = array();
        for ( $i = 0; $i < ezjscServerFunctionsAjaxUploader::PENDING_MAX; $i++ )
            list( $store ) = ezjscServerFunctionsAjaxUploader::rememberPending( $store, "f$i", 'n', 'b', $now );
        list( $store, , $dropped ) = ezjscServerFunctionsAjaxUploader::rememberPending( $store, 'one more', 'n', 'b', $now );
        $this->assertSame( array( 'f0' ), $dropped, 'the evicted entry gives its file up' );
    }

    /** AT-05 */
    public function testAStoredPathMustBeBelowTheStorageDirectory()
    {
        $dir = eZSys::storageDirectory();
        $this->assertTrue( ezjscServerFunctionsAjaxUploader::isStoredUploadPath( $this->stored, $dir ) );
        $this->assertTrue( ezjscServerFunctionsAjaxUploader::isStoredUploadPath( $this->stored, $dir, true ) );
        $this->assertFalse( ezjscServerFunctionsAjaxUploader::isStoredUploadPath( $this->victim, $dir ) );
        $this->assertFalse( ezjscServerFunctionsAjaxUploader::isStoredUploadPath( $dir . '/../../' . $this->victim, $dir ) );
        $this->assertFalse( ezjscServerFunctionsAjaxUploader::isStoredUploadPath( $dir . '/./x', $dir ) );
        $this->assertFalse( ezjscServerFunctionsAjaxUploader::isStoredUploadPath( $dir . '/a//b', $dir ) );
        $this->assertFalse( ezjscServerFunctionsAjaxUploader::isStoredUploadPath( $dir . "/a\0b", $dir ) );
        $this->assertFalse( ezjscServerFunctionsAjaxUploader::isStoredUploadPath( $dir . 'x/a', $dir ) );
        $this->assertFalse( ezjscServerFunctionsAjaxUploader::isStoredUploadPath( $dir . '/missing.txt', $dir, true ) );

        $dotted = $this->storedDir . '/../' . basename( $this->storedDir ) . '/' . basename( $this->stored );
        $this->assertFileExists( $dotted );
        $token = $this->remember( $dotted, 14 );
        $this->assertSame( 'Unable to retrieve the uploaded file.', $this->preview( array( 'UploadToken' => $token ) ), 'a ".." part' );
        $this->assertFileExists( $this->stored );

        $token = $this->remember( $this->victim, 14 );
        $this->assertSame( 'Unable to retrieve the uploaded file.', $this->preview( array( 'UploadToken' => $token ) ) );
        $this->assertFileExists( $this->victim );
    }

    /** AT-06 */
    public function testUploadAsksCanUpload()
    {
        ezjscAjaxUploaderTokenTestHandler::$canUpload = false;
        $result = json_decode( ezjscAjaxUploaderTokenTestFunctions::upload( array( 'tokentest' ) ), true );
        $this->assertSame( 'You are not allowed to upload a file.', $result['error_text'] );
        $this->assertFalse( ezjscAjaxUploaderTokenTestHandler::$fileInfoAsked );
    }

    /** AT-07 */
    public function testTheLocationFormsCarryTheToken()
    {
        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            $source = file_get_contents( "design/$design/templates/ajaxuploader/location.tpl" );
            $this->assertStringContainsString( 'name="UploadToken" value="{$upload_token|wash()}"', $source, $design );
            $this->assertStringNotContainsString( 'name="UploadFile"', $source, $design );
            $this->assertStringNotContainsString( '$file.filename', $source, $design );
        }
    }

    /** AT-08 */
    public function testThePendingListIsBounded()
    {
        $store = array();
        $first = null;
        for ( $i = 0; $i <= ezjscServerFunctionsAjaxUploader::PENDING_MAX; $i++ )
        {
            list( $store, $token ) = ezjscServerFunctionsAjaxUploader::rememberPending( $store, "f$i", 'n', 'b' );
            $this->assertMatchesRegularExpression( '/^[0-9a-f]{32}$/', $token );
            if ( $first === null )
                $first = $token;
        }
        $this->assertCount( ezjscServerFunctionsAjaxUploader::PENDING_MAX, $store );
        $this->assertNull( ezjscServerFunctionsAjaxUploader::pendingEntry( $store, $first, 'b' ), 'the oldest is forgotten' );
        $this->assertSame( 'f1', ezjscServerFunctionsAjaxUploader::pendingEntry( $store, array_keys( $store )[0], 'b' )['file'] );
    }
}
