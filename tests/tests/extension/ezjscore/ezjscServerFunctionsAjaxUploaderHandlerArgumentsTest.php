<?php
/**
 * The AJAX uploader builds its handler from the posted target (AjaxUploadHandlerData). A missing or incomplete
 * target, as a stale script or a hand-made request sends it, is refused with "Arguments error." instead of reaching
 * the handler's constructor (ArgumentCountError).
 *
 *  AU-01 - no handler data, or fewer values than the constructor requires: "Arguments error."
 *  AU-02 - handler data that is not a list of plain values: "Arguments error."
 *  AU-03 - complete handler data: the handler is built (its canUpload() is asked)
 *  AU-04 - a handler without required constructor arguments needs no data
 *  AU-05 - every handler shipped in ezjscore.ini refuses an empty target the same way
 *
 * The handlers are stand-ins registered in memory ([AjaxUploader] AjaxUploadHandler[]); no database.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/ezjscore/ezjscServerFunctionsAjaxUploaderHandlerArgumentsTest.php
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

/** A handler whose constructor takes the attribute id and the version, like ezpRelationListAjaxUploader */
class ezjscAjaxUploaderArgsTestHandler implements ezpAjaxUploaderHandlerInterface
{
    public static $built = null;

    public function __construct( $attributeId, $version )
    {
        self::$built = array( $attributeId, $version );
    }

    public function canUpload()
    {
        return false;
    }

    public function getFileInfo()
    {
        throw new RuntimeException( 'not in this test' );
    }

    public function getContentClass( array $mimeData )
    {
        throw new RuntimeException( 'not in this test' );
    }

    public function getDefaultParentNodeId( eZContentClass $class )
    {
        return 2;
    }

    public function createObject( $file, $location, $name = '' )
    {
        throw new RuntimeException( 'not in this test' );
    }

    public function serializeObject( eZContentObject $object )
    {
        throw new RuntimeException( 'not in this test' );
    }
}

/** A handler without constructor arguments */
class ezjscAjaxUploaderArgsTestPlainHandler extends ezjscAjaxUploaderArgsTestHandler
{
    public function __construct()
    {
        self::$built = array();
    }
}

class ezjscServerFunctionsAjaxUploaderHandlerArgumentsTest extends PHPUnit\Framework\TestCase
{
    private $cwd;

    protected function setUp(): void
    {
        $this->cwd = getcwd();
        chdir( dirname( __DIR__, 4 ) );
        $ini = eZINI::instance( 'ezjscore.ini' );
        $ini->setReadOnlySettingsCheck( false );
        $ini->setVariable( 'AjaxUploader', 'AjaxUploadHandler', array( 'argstest' => 'ezjscAjaxUploaderArgsTestHandler',
                                                                       'argsplain' => 'ezjscAjaxUploaderArgsTestPlainHandler' ) );
        ezjscAjaxUploaderArgsTestHandler::$built = null;
        $_POST = array();
    }

    protected function tearDown(): void
    {
        eZINI::resetInstance( 'ezjscore.ini' );
        $_POST = array();
        chdir( $this->cwd );
    }

    /** uploadForm() for $identifier with $post; the exception's class and message */
    private function uploadForm( $identifier, array $post )
    {
        $_POST = $post;
        try
        {
            ezjscServerFunctionsAjaxUploader::uploadForm( array( $identifier ) );
            return 'returned';
        }
        catch ( Throwable $e )
        {
            return get_class( $e ) . ': ' . $e->getMessage();
        }
    }

    /** AU-01 */
    public function testMissingOrIncompleteDataIsRefused()
    {
        $this->assertSame( 'InvalidArgumentException: Arguments error.', $this->uploadForm( 'argstest', array() ), 'no data' );
        $this->assertSame( 'InvalidArgumentException: Arguments error.',
                           $this->uploadForm( 'argstest', array( 'AjaxUploadHandlerData' => array( 'ObjectRelationsAttributeId' => 7 ) ) ), 'one value' );
        $this->assertNull( ezjscAjaxUploaderArgsTestHandler::$built, 'the constructor was not reached' );
    }

    /** AU-02 */
    public function testDataThatIsNotAListOfValuesIsRefused()
    {
        $this->assertSame( 'InvalidArgumentException: Arguments error.',
                           $this->uploadForm( 'argstest', array( 'AjaxUploadHandlerData' => 'x' ) ), 'a string' );
        $this->assertSame( 'InvalidArgumentException: Arguments error.',
                           $this->uploadForm( 'argstest', array( 'AjaxUploadHandlerData' => array( 'a' => array( 1 ), 'b' => 2 ) ) ), 'a nested array' );
        $this->assertNull( ezjscAjaxUploaderArgsTestHandler::$built );
    }

    /** AU-03 */
    public function testCompleteDataBuildsTheHandler()
    {
        $result = $this->uploadForm( 'argstest', array( 'AjaxUploadHandlerData' => array( 'ObjectRelationsAttributeId' => 7, 'Version' => 1 ) ) );
        $this->assertStringStartsWith( 'RuntimeException: ', $result, 'past the handler: canUpload() refused' );
        $this->assertSame( array( 7, 1 ), ezjscAjaxUploaderArgsTestHandler::$built );
    }

    /** AU-04 */
    public function testAHandlerWithoutArgumentsNeedsNoData()
    {
        $this->assertTrue( ezjscServerFunctionsAjaxUploader::handlerDataFits( 'argsplain', array() ) );
        $this->assertStringStartsWith( 'RuntimeException: ', $this->uploadForm( 'argsplain', array() ) );
        $this->assertSame( array(), ezjscAjaxUploaderArgsTestHandler::$built );
    }

    /** AU-05 */
    public function testTheShippedHandlersRefuseAnEmptyTarget()
    {
        $root = dirname( __DIR__, 4 ) . '/extension/ezjscore';
        $settings = (string)file_get_contents( $root . '/settings/ezjscore.ini' );
        $this->assertSame( 2, preg_match_all( '/^AjaxUploadHandler\[(\w+)\]=(\w+)$/m', $settings, $m, PREG_SET_ORDER ) );
        require_once $root . '/classes/ajaxuploader/ezprelationlistajaxuploader.php';
        require_once $root . '/classes/ajaxuploader/ezprelationajaxuploader.php';
        $handlers = array();
        foreach ( $m as $match )
            $handlers[$match[1]] = $match[2];
        eZINI::instance( 'ezjscore.ini' )->setVariable( 'AjaxUploader', 'AjaxUploadHandler', $handlers );
        foreach ( $handlers as $identifier => $class )
        {
            $this->assertTrue( class_exists( $class ), $class );
            $this->assertSame( 'InvalidArgumentException: Arguments error.', $this->uploadForm( $identifier, array() ), $identifier );
            $this->assertSame( 'InvalidArgumentException: Arguments error.',
                               $this->uploadForm( $identifier, array( 'AjaxUploadHandlerData' => array( 'Version' => 1 ) ) ), $identifier . ', one value' );
        }
    }
}
