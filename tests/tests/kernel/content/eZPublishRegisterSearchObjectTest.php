<?php
/**
 * What the publish operation hands to registerSearchObject(), without the database.
 *
 *  RS-01 - The step register-search-object has its own parameters, object_id and version: the operation's notify
 *          is not handed on
 *  RS-02 - Through the operation's parameter mapping, registerSearchObject() gets $isMoved false for a publication
 *          with notify true, false or missing
 *  RS-03 - With DelayedIndexing=enabled the publish step queues index_object (not index_moved_node), whatever notify
 *          is; registerSearchObject() with $isMoved true queues index_moved_node; the row carries its time
 *  RS-04 - No method step of publish without parameters of its own takes more than object_id and version, so a new
 *          operation parameter cannot land in an optional parameter of a step again
 *  RS-05 - Moving a node tells the search engine it is a move: eZContentObjectTreeNodeOperations::move() calls
 *          eZSearch::addNodeAssignment() with $isMoved true, and eZSearch hands it to the engine
 *
 * The database is a driver that records the statements it is given; the search engine is a recorder.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

/**
 * Records what the operation hands to register-search-object.
 */
class eZPublishRegisterSearchObjectTestRecorder
{
    public static $calls = array();

    public static function registerSearchObject( $objectID, $version = null, $isMoved = false )
    {
        self::$calls[] = array( $objectID, $version, $isMoved );
    }
}

/**
 * A "connected" driver that records every statement instead of running it.
 */
class eZPublishRegisterSearchObjectTestDB extends eZDBInterface
{
    public $Statements = array();

    public function __construct()
    {
        $parameters = array_fill_keys(
            array( 'server', 'port', 'user', 'password', 'database', 'use_slave_server',
                   'slave_server', 'slave_port', 'slave_user', 'slave_password', 'slave_database',
                   'socket', 'is_internal_charset', 'builtin_encoding', 'connect_retries', 'use_persistent_connection' ),
            false
        );
        $parameters['server'] = 'db.example.invalid';
        $parameters['database'] = 'exampledb';
        $parameters['charset'] = 'utf-8';
        $parameters['is_internal_charset'] = true;
        parent::__construct( $parameters );
        $this->IsConnected = true;
    }

    function databaseName()
    {
        return 'recorder';
    }

    function query( $sql, $server = false )
    {
        $this->Statements[] = $sql;
        return true;
    }
}

/**
 * A search engine that records the node assignments it is told about.
 */
class eZPublishRegisterSearchObjectTestEngine implements ezpSearchEngine
{
    public $added = array();

    public function needCommit()
    {
        return false;
    }

    public function needRemoveWithUpdate()
    {
        return false;
    }

    public function addObject( $contentObject, $commit = true )
    {
        return true;
    }

    public function removeObject( $contentObject, $commit = null )
    {
        return true;
    }

    public function removeObjectById( $contentObjectId, $commit = null )
    {
        return true;
    }

    public function search( $searchText, $params = array(), $searchTypes = array() )
    {
        return array();
    }

    public function supportedSearchTypes()
    {
        return array();
    }

    public function commit()
    {
    }

    public function addNodeAssignment( $mainNodeID, $objectID, $nodeAssignmentIDList, $isMoved = false )
    {
        $this->added[] = array( $mainNodeID, $objectID, $nodeAssignmentIDList, $isMoved );
        return true;
    }
}

class eZPublishRegisterSearchObjectTest extends PHPUnit\Framework\TestCase
{
    private $db;
    private $access;
    private $engineKey;
    private $engine;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        eZPublishRegisterSearchObjectTestRecorder::$calls = array();
        $this->db = isset( $GLOBALS['eZDBGlobalInstance'] ) ? $GLOBALS['eZDBGlobalInstance'] : null;
        $this->access = array_key_exists( 'eZCurrentAccess', $GLOBALS ) ? $GLOBALS['eZCurrentAccess'] : null;
        if ( !isset( $GLOBALS['eZCurrentAccess']['name'] ) )
        {
            $GLOBALS['eZCurrentAccess'] = array( 'name' => 'rstest' );
        }
        $this->engineKey = 'eZSearchPlugin_' . $GLOBALS['eZCurrentAccess']['name'];
        $this->engine = isset( $GLOBALS[$this->engineKey] ) ? $GLOBALS[$this->engineKey] : null;
    }

    protected function tearDown(): void
    {
        eZDB::setInstance( $this->db );
        if ( $this->engine === null )
            unset( $GLOBALS[$this->engineKey] );
        else
            $GLOBALS[$this->engineKey] = $this->engine;
        if ( $this->access === null )
            unset( $GLOBALS['eZCurrentAccess'] );
        else
            $GLOBALS['eZCurrentAccess'] = $this->access;
        ezpINIHelper::restoreINISettings();
    }

    private function publish()
    {
        $OperationList = array();
        include 'kernel/content/operation_definition.php';
        return $OperationList['publish'];
    }

    private function step( $name )
    {
        foreach ( $this->publish()['body'] as $body )
        {
            if ( isset( $body['name'] ) && $body['name'] === $name )
                return $body;
        }
        $this->fail( "the publish operation has no step $name" );
    }

    /**
     * The parameters the operation hands to a step: its own, or else all of the operation's (executeBody()).
     */
    private function parametersOf( array $step )
    {
        return isset( $step['parameters'] ) ? $step['parameters'] : $this->publish()['parameters'];
    }

    private function publications()
    {
        return array(
            'notify missing' => array( 'object_id' => 990401, 'version' => 2 ),
            'notify true' => array( 'object_id' => 990401, 'version' => 2, 'notify' => true ),
            'notify false' => array( 'object_id' => 990401, 'version' => 2, 'notify' => false ),
        );
    }

    /** RS-01 */
    public function testTheStepHasItsOwnParameters()
    {
        $step = $this->step( 'register-search-object' );
        $this->assertSame( 'registerSearchObject', $step['method'] );
        $this->assertArrayHasKey( 'parameters', $step, 'without its own parameters the step gets notify as $isMoved' );
        $this->assertSame( array( 'object_id', 'version' ), array_column( $step['parameters'], 'name' ) );
    }

    /** RS-02 */
    public function testRegisterSearchObjectGetsIsMovedFalse()
    {
        $info = new eZModuleOperationInfo( 'content', false );
        $step = $this->step( 'register-search-object' );
        foreach ( $this->publications() as $parameters )
        {
            $info->executeClassMethod( __FILE__, 'eZPublishRegisterSearchObjectTestRecorder', 'registerSearchObject',
                                       $this->parametersOf( $step ), $parameters );
        }
        $this->assertSame( array( array( 990401, 2, false ), array( 990401, 2, false ), array( 990401, 2, false ) ),
                           eZPublishRegisterSearchObjectTestRecorder::$calls );
    }

    /** RS-03 */
    public function testDelayedIndexingQueuesIndexObject()
    {
        ezpINIHelper::setINISetting( 'site.ini', 'SearchSettings', 'DelayedIndexing', 'enabled' );
        $info = new eZModuleOperationInfo( 'content', false );
        $step = $this->step( 'register-search-object' );
        foreach ( $this->publications() as $case => $parameters )
        {
            $db = new eZPublishRegisterSearchObjectTestDB();
            eZDB::setInstance( $db );
            $info->executeClassMethod( 'kernel/content/ezcontentoperationcollection.php', 'eZContentOperationCollection',
                                       'registerSearchObject', $this->parametersOf( $step ), $parameters );
            $this->assertSame( array( 'index_object', '990401' ), $this->queuedRow( $db->Statements ), $case );
        }

        $db = new eZPublishRegisterSearchObjectTestDB();
        eZDB::setInstance( $db );
        eZContentOperationCollection::registerSearchObject( 990402, null, true );
        $this->assertSame( array( 'index_moved_node', '990402' ), $this->queuedRow( $db->Statements ),
                           'a move still queues the subtree reindex' );
    }

    /**
     * The action and param of the one row queued, which carries the time it was queued (created).
     */
    private function queuedRow( array $statements )
    {
        $this->assertCount( 1, $statements );
        $this->assertMatchesRegularExpression(
            "/^INSERT INTO ezpending_actions\\( action, created, param \\) VALUES \\( '([a-z_]+)', ([0-9]+), '([0-9]+)' \\)$/",
            $statements[0] );
        preg_match( "/VALUES \\( '([a-z_]+)', ([0-9]+), '([0-9]+)' \\)/", $statements[0], $m );
        $this->assertLessThanOrEqual( 5, abs( time() - (int)$m[2] ), 'created is the time the row was queued' );
        return array( $m[1], $m[3] );
    }

    /** RS-04 */
    public function testNoInheritingStepTakesMoreThanObjectAndVersion()
    {
        $publish = $this->publish();
        $this->assertSame( array( 'object_id', 'version' ), array_slice( array_column( $publish['parameters'], 'name' ), 0, 2 ) );
        $class = $publish['default_call_method']['class'];
        $checked = 0;
        foreach ( $publish['body'] as $body )
        {
            if ( $body['type'] !== 'method' || isset( $body['parameters'] ) )
                continue;
            $method = new ReflectionMethod( $class, $body['method'] );
            $this->assertLessThanOrEqual( 2, $method->getNumberOfParameters(),
                                          "{$body['name']}: {$body['method']}() takes more than object_id and version, so it needs parameters of its own" );
            ++$checked;
        }
        $this->assertGreaterThan( 0, $checked );
    }

    /** RS-05 */
    public function testAMoveTellsTheEngineItIsAMove()
    {
        $this->assertMatchesRegularExpression( '/eZSearch::addNodeAssignment\([^;]*, true \);/',
                                               file_get_contents( 'kernel/classes/ezcontentobjecttreenodeoperations.php' ) );
        $engine = new eZPublishRegisterSearchObjectTestEngine();
        $GLOBALS[$this->engineKey] = $engine;
        eZSearch::addNodeAssignment( 10, 990403, array( 11 ), true );
        eZSearch::addNodeAssignment( 10, 990403, array( 11 ) );
        $this->assertSame( array( array( 10, 990403, array( 11 ), true ), array( 10, 990403, array( 11 ), false ) ), $engine->added );
    }
}
