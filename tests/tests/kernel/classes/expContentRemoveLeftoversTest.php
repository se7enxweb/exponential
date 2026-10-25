<?php
/**
 * Three leftovers around removing content. No database: a stand-in records every query.
 *
 *  RL-01 - content/action ActionRemove: a node ID that no longer exists is answered with "not available" before
 *          the node is used (it ended in "Call to a member function attribute() on null")
 *  RL-02 - content/removeeditversion: the DiscardConfirm of the session counts for one request only; it stayed
 *          and let any later GET discard the version without asking
 *  RL-03 - eZContentObject::purge(): every event type of workflow.ini cleans up, also when another type was
 *          already loaded in this request (then only the loaded ones did)
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
use Exponential\View\Kernel\Content\Removeeditversion;

/** Database stand-in: every query recorded, every read empty */
class expRemoveLeftoversStandInDB extends eZNullDB
{
    public $queries = array();

    function __construct( $parameters )
    {
    }

    function query( $sql, $server = false )
    {
        $this->queries[] = $sql;
        return true;
    }

    function arrayQuery( $sql, $params = array(), $server = false )
    {
        $this->queries[] = $sql;
        return array();
    }

    function databaseName()
    {
        return 'stand-in';
    }
}

/** Module stand-in: handleError() recorded */
class expRemoveLeftoversModule
{
    public $errors = array();

    function handleError( $code, $type = false, $parameters = array(), $userParameters = false )
    {
        $this->errors[] = array( $code, $type );
        return array( 'error' => $code );
    }

    function redirectTo( $uri )
    {
        return true;
    }

    function setTitle( $title )
    {
    }
}

/** A workflow event type loaded earlier in the request */
class expRemoveLeftoversEventType extends eZWorkflowEventType
{
    public static $cleaned = array();

    function __construct()
    {
        $this->TypeString = 'event_expleftovers';
    }

    function cleanupAfterRemoving( $attr = array() )
    {
        self::$cleaned[] = $attr;
    }
}

class expContentRemoveLeftoversTest extends PHPUnit\Framework\TestCase
{
    private $previousDB;
    private $session;
    private $wasStarted;
    private $savedGlobals = array();

    // eZWorkflowTypes and eZWorkflowTypeObjects are not restored but cleaned (see tearDown())
    private const GLOBALS_KEYS = array( 'eZWorkflowAllowedTypes', 'eZCurrentAccess' );
    private $savedTypeObjects;

    /**
     * The kernel registers its shutdown and exception handlers (eZExecution) on the way through purge(); done once
     * here so no test is reported for adding them.
     */
    public static function setUpBeforeClass(): void
    {
        eZExecution::registerShutdownHandler();
    }

    protected function setUp(): void
    {
        $this->previousDB = $GLOBALS['eZDBGlobalInstance'] ?? null;
        $this->session = $_SESSION ?? null;
        $started = new ReflectionProperty( 'eZSession', 'hasStarted' );
        $this->wasStarted = $started->getValue();
        $started->setValue( null, true );
        $_SESSION = array();
        $_POST = array();
        foreach ( self::GLOBALS_KEYS as $key )
            $this->savedGlobals[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
        $this->savedTypeObjects = isset( $GLOBALS['eZWorkflowTypeObjects'] ) && is_array( $GLOBALS['eZWorkflowTypeObjects'] )
            ? $GLOBALS['eZWorkflowTypeObjects'] : array();
    }

    protected function tearDown(): void
    {
        eZDB::setInstance( $this->previousDB );
        ( new ReflectionProperty( 'eZSession', 'hasStarted' ) )->setValue( null, $this->wasStarted );
        $_SESSION = $this->session;
        $_POST = array();
        foreach ( self::GLOBALS_KEYS as $key )
        {
            unset( $GLOBALS[$key] );
            if ( $this->savedGlobals[$key] !== null )
                $GLOBALS[$key] = $this->savedGlobals[$key][0];
        }
        // The event types loaded here stay registered: their files are included once per process and would not
        // register again. Only the stand-in type goes.
        $objects = isset( $GLOBALS['eZWorkflowTypeObjects'] ) && is_array( $GLOBALS['eZWorkflowTypeObjects'] ) ? $GLOBALS['eZWorkflowTypeObjects'] : array();
        $GLOBALS['eZWorkflowTypeObjects'] = array_merge( $objects, $this->savedTypeObjects );
        unset( $GLOBALS['eZWorkflowTypeObjects']['event_expleftovers'], $GLOBALS['eZWorkflowTypes']['event_expleftovers'] );
    }

    /** RL-01 */
    public function testActionRemoveChecksTheNodeBeforeUsingIt()
    {
        $code = (string)file_get_contents( dirname( __DIR__, 4 ) . '/kernel/private/classes/views/content/action.php' );
        $start = strpos( $code, 'hasPostVariable( "ActionRemove" )' );
        $this->assertNotFalse( $start );
        $branch = substr( $code, $start, 1200 );
        $fetch = strpos( $branch, '\\eZContentObjectTreeNode::fetch( $contentNodeID )' );
        $guard = strpos( $branch, 'if ( !$node instanceof \\eZContentObjectTreeNode )' );
        $use = strpos( $branch, '$node->attribute( \'parent_node_id\' )' );
        $this->assertNotFalse( $fetch );
        $this->assertNotFalse( $guard, 'the fetched node is checked' );
        $this->assertNotFalse( $use );
        $this->assertTrue( $fetch < $guard && $guard < $use, 'between fetching and using the node' );
        $this->assertStringContainsString( 'KERNEL_NOT_AVAILABLE', substr( $branch, $guard, $use - $guard ) );
    }

    /** RL-02 */
    public function testDiscardConfirmOfTheSessionCountsOnce()
    {
        $db = new expRemoveLeftoversStandInDB( array() );
        eZDB::setInstance( $db );
        $_SESSION = array( 'DiscardObjectID' => 4711, 'DiscardObjectVersion' => 3, 'DiscardObjectLanguage' => 'eng-GB',
                           'DiscardConfirm' => 0 );
        $module = new expRemoveLeftoversModule();
        ( new Removeeditversion() )->run( array( 'Params' => array( 'Module' => $module ) ) );

        // confirmed: the object was looked up (not found here, so "not available")
        $this->assertSame( array( array( eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) ), $module->errors );
        $this->assertArrayNotHasKey( 'DiscardConfirm', $_SESSION, 'used up by this request' );
    }

    /** RL-03 */
    public function testPurgeLetsEveryEventTypeCleanUp()
    {
        $db = new expRemoveLeftoversStandInDB( array() );
        eZDB::setInstance( $db );
        $GLOBALS['eZCurrentAccess'] = array( 'name' => 'expleftovers' );
        $searchKey = 'eZSearchPlugin_expleftovers';
        $savedSearch = array_key_exists( $searchKey, $GLOBALS ) ? array( $GLOBALS[$searchKey] ) : null;
        $GLOBALS[$searchKey] = false; // no search engine
        expRemoveLeftoversEventType::$cleaned = array();
        // one event type is loaded already in this request, and only that one is created
        unset( $GLOBALS['eZWorkflowAllowedTypes'] );
        $GLOBALS['eZWorkflowTypes']['event_expleftovers'] = array( 'class_name' => 'expRemoveLeftoversEventType' );
        $GLOBALS['eZWorkflowTypeObjects'] = array( 'event_expleftovers' => new expRemoveLeftoversEventType() );
        try
        {
            $object = new eZContentObject( array( 'id' => 4711, 'name' => 'x', 'contentclass_id' => 1 ) );
            // the audit record of the purge is not the point here (it would read the class through the database)
            expAuditHook::muted( 'content.object.purge', function () use ( $object ) { $object->purge(); } );
        }
        finally
        {
            unset( $GLOBALS[$searchKey] );
            if ( $savedSearch !== null )
                $GLOBALS[$searchKey] = $savedSearch[0];
        }

        $this->assertSame( array( array( 'DeleteContentObject' => 4711 ) ), expRemoveLeftoversEventType::$cleaned,
                           'the type loaded before still cleans up' );
        $approve = array_filter( $db->queries, function ( $sql ) {
            return strpos( $sql, "workflow_type_string = 'event_ezapprove'" ) !== false;
        } );
        $this->assertNotEmpty( $approve, 'event_ezapprove (workflow.ini, not loaded before) cleans up as well' );
    }
}
