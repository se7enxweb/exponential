<?php
/**
 * Three calls that did not work as written. No database: a stand-in answers every query with nothing.
 *
 *  KC-01 - fetch( 'collaboration', 'tree_count' ) named a method of 13 parameters with 4 (ArgumentCountError);
 *          it now reaches eZCollaborationFunctionCollection::fetchObjectTreeCount(), and a missing parent node
 *          is "not found" instead of a call on null
 *  KC-02 - ezpContentPublishingQueue::init() connects the INI hooks once, not on every add()
 *  KC-03 - content/download announces its event with positional arguments: a listener with other parameter names
 *          is called (string keys became named arguments under PHP 8: "Unknown named parameter")
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
/** Database stand-in: every read empty */
class expKernelCallFixesStandInDB extends eZNullDB
{
    function __construct( $parameters )
    {
    }

    function query( $sql, $server = false )
    {
        return true;
    }

    function arrayQuery( $sql, $params = array(), $server = false )
    {
        return array();
    }

    function databaseName()
    {
        return 'stand-in';
    }
}

/** A publishing queue hook that counts how often it runs */
class expKernelCallFixesQueueHook
{
    public static $runs = 0;

    public static function hook()
    {
        self::$runs++;
    }
}

/** A content/download listener with parameter names of its own */
class expKernelCallFixesDownloadListener
{
    public static $calls = array();

    public static function downloaded( $objectId, $attributeId )
    {
        self::$calls[] = array( $objectId, $attributeId );
    }
}

class expKernelCallFixesTest extends PHPUnit\Framework\TestCase
{
    public static function setUpBeforeClass(): void
    {
        eZExecution::registerShutdownHandler();
    }

    /** KC-01 */
    public function testCollaborationTreeCountReachesItsMethod()
    {
        $savedPaths = array_key_exists( 'eZModuleGlobalPathList', $GLOBALS ) ? array( $GLOBALS['eZModuleGlobalPathList'] ) : null;
        eZModule::setGlobalPathList( array( dirname( __DIR__, 4 ) . '/kernel' ) );
        try
        {
            $info = new eZModuleFunctionInfo( 'collaboration' );
            $this->assertTrue( $info->loadDefinition() );
        }
        finally
        {
            unset( $GLOBALS['eZModuleGlobalPathList'] );
            if ( $savedPaths !== null )
                $GLOBALS['eZModuleGlobalPathList'] = $savedPaths[0];
        }
        $definition = $info->FunctionList['tree_count'];
        $this->assertSame( array( 'class' => 'eZCollaborationFunctionCollection', 'method' => 'fetchObjectTreeCount' ),
                           $definition['call_method'] );
        $method = new ReflectionMethod( $definition['call_method']['class'], $definition['call_method']['method'] );
        $this->assertSame( count( $definition['parameters'] ), $method->getNumberOfParameters(), 'one parameter per declared one' );

        $previousDB = $GLOBALS['eZDBGlobalInstance'] ?? null;
        eZDB::setInstance( new expKernelCallFixesStandInDB( array() ) );
        try
        {
            $collection = new eZCollaborationFunctionCollection();
            $result = $collection->fetchObjectTreeCount( 999999, false, false, 0 );
        }
        finally
        {
            eZDB::setInstance( $previousDB );
        }
        $this->assertSame( array( 'error' => array( 'error_type' => 'kernel', 'error_code' => eZError::KERNEL_NOT_FOUND ) ), $result );
    }

    /** KC-02 */
    public function testPublishingQueueConnectsItsHooksOnce()
    {
        $signals = new ReflectionProperty( 'ezpContentPublishingQueue', 'signals' );
        $initialized = new ReflectionProperty( 'ezpContentPublishingQueue', 'hooksInitialized' );
        $savedSignals = $signals->getValue();
        $savedInitialized = $initialized->getValue();
        $ini = eZINI::instance( 'content.ini' );
        $savedHooks = $ini->variable( 'PublishingSettings', 'AsynchronousPublishingPreQueueHooks' );
        $signals->setValue( null, null );
        $initialized->setValue( null, false );
        $ini->setVariable( 'PublishingSettings', 'AsynchronousPublishingPreQueueHooks', array( 'expKernelCallFixesQueueHook::hook' ) );
        expKernelCallFixesQueueHook::$runs = 0;
        try
        {
            ezpContentPublishingQueue::init();
            ezpContentPublishingQueue::init();
            ezpContentPublishingQueue::init();
            ezpContentPublishingQueue::signals()->emit( 'preQueue', 1, 4711 );
        }
        finally
        {
            $ini->setVariable( 'PublishingSettings', 'AsynchronousPublishingPreQueueHooks', $savedHooks );
            $signals->setValue( null, $savedSignals );
            $initialized->setValue( null, $savedInitialized );
        }
        $this->assertSame( 1, expKernelCallFixesQueueHook::$runs );
    }

    /** KC-03 */
    public function testDownloadEventArgumentsArePositional()
    {
        $code = (string)file_get_contents( dirname( __DIR__, 4 ) . '/kernel/private/classes/views/content/download.php' );
        $this->assertSame( 1, preg_match( "/notify\\(\\s*'content\\/download',\\s*(array\\([^;]*\\))\\s*\\);/", $code, $match ) );
        $this->assertStringNotContainsString( '=>', $match[1], 'no string keys: they would be named arguments' );

        // what that means for a listener: ezpEvent passes the array through call_user_func_array()
        $event = new ezpEvent( false );
        $event->attach( 'content/download', 'expKernelCallFixesDownloadListener::downloaded' );
        expKernelCallFixesDownloadListener::$calls = array();
        $event->notify( 'content/download', array( 12, 34 ) );
        $this->assertSame( array( array( 12, 34 ) ), expKernelCallFixesDownloadListener::$calls );
        $this->expectException( Error::class );
        $event->notify( 'content/download', array( 'contentObjectID' => 12, 'contentObjectAttributeID' => 34 ) );
    }
}
