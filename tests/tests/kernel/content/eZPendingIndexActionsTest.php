<?php
/**
 * The search index entries delayed indexing queues (ezpending_actions), without the database.
 *
 *  PA-01 - A queued row carries the time it was queued (created), with SQL and with MongoDB
 *  PA-02 - A publication that moved a node of the object (a parent changed in the edit form) is handed to an engine
 *          that implements addNodeAssignment() as a move: with delayed indexing exactly one index_moved_node row
 *  PA-03 - The same publication with an engine without addNodeAssignment() (the kernel's ezsearch) queues exactly
 *          one index_object row, as before; the mark is used once
 *  PA-04 - The mark belongs to one version: another version of the object is an ordinary publication
 *  PA-05 - publishNode() marks the version in its move branch, right after the node was moved
 *  PA-06 - A real move (content/move) tells the engine it is a move: index_moved_node with an engine that implements
 *          addNodeAssignment(), nothing with the kernel's ezsearch
 *  PA-07 - A new location (content/addlocation) is no move: index_object
 *  PA-08 - An object that goes to the trash loses both kinds of queued entry (index_object and index_moved_node)
 *  PA-09 - Every method step of every kernel operation without parameters of its own fills exactly the optional
 *          method parameters listed here, so a new operation parameter cannot land in one unnoticed
 *  PA-10 - The cronjob indexcontent processes both actions, oldest first, and the subtree only for index_moved_node
 *
 * The database is a driver that records the statements it is given; the search engines are recorders.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

/**
 * A "connected" SQL driver that records every statement instead of running it; queries return no rows.
 */
class eZPendingIndexActionsTestDB extends eZDBInterface
{
    public $Statements = array();
    public $Inserts = array();

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

    function arrayQuery( $sql, $params = array(), $server = false )
    {
        return array();
    }
}

/**
 * The same driver as the MongoDB one names itself: inserts are documents.
 */
class eZPendingIndexActionsTestMongoDB extends eZPendingIndexActionsTestDB
{
    function databaseName()
    {
        return 'mongo';
    }

    function insert( $collection, array $document )
    {
        $this->Inserts[] = array( $collection, $document );
        return true;
    }
}

/**
 * An engine without addNodeAssignment(), as the kernel's ezsearch.
 */
class eZPendingIndexActionsTestKernelEngine implements ezpSearchEngine
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
        $this->added[] = $contentObject;
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
}

/**
 * An engine that implements addNodeAssignment() as ezfind's eZSolr does: it registers the object, moved or not.
 */
class eZPendingIndexActionsTestFindEngine extends eZPendingIndexActionsTestKernelEngine
{
    public $assignments = array();

    public function addNodeAssignment( $mainNodeID, $objectID, $nodeAssignmentIDList, $isMoved )
    {
        $this->assignments[] = array( $mainNodeID, $objectID, $nodeAssignmentIDList, $isMoved );
        eZContentOperationCollection::registerSearchObject( $objectID, null, $isMoved );
        return true;
    }
}

class eZPendingIndexActionsTest extends PHPUnit\Framework\TestCase
{
    const OBJECT_ID = 990501;

    private $db;
    private $access;
    private $engineKey;
    private $engine;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->db = isset( $GLOBALS['eZDBGlobalInstance'] ) ? $GLOBALS['eZDBGlobalInstance'] : null;
        $this->access = array_key_exists( 'eZCurrentAccess', $GLOBALS ) ? $GLOBALS['eZCurrentAccess'] : null;
        if ( !isset( $GLOBALS['eZCurrentAccess']['name'] ) )
        {
            $GLOBALS['eZCurrentAccess'] = array( 'name' => 'patest' );
        }
        $this->engineKey = 'eZSearchPlugin_' . $GLOBALS['eZCurrentAccess']['name'];
        $this->engine = array_key_exists( $this->engineKey, $GLOBALS ) ? $GLOBALS[$this->engineKey] : null;
        ezpINIHelper::setINISetting( 'site.ini', 'SearchSettings', 'DelayedIndexing', 'enabled' );
        $this->setMarks( array() );
    }

    protected function tearDown(): void
    {
        $this->setMarks( array() );
        unset( $GLOBALS['eZContentObjectContentObjectCache'][self::OBJECT_ID] );
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

    private function setMarks( array $marks )
    {
        $property = new ReflectionProperty( 'eZContentOperationCollection', 'movedByPublication' );
        $property->setValue( null, $marks );
    }

    private function marks()
    {
        $property = new ReflectionProperty( 'eZContentOperationCollection', 'movedByPublication' );
        return $property->getValue();
    }

    private function recorder( $class = 'eZPendingIndexActionsTestDB' )
    {
        $db = new $class();
        eZDB::setInstance( $db );
        return $db;
    }

    private function useEngine( $engine )
    {
        $GLOBALS[$this->engineKey] = $engine;
        return $engine;
    }

    /**
     * The object as eZContentObject::fetch() finds it in its cache, with its main node.
     */
    private function cacheObject( $mainNodeID )
    {
        $object = new eZContentObject( array( 'id' => self::OBJECT_ID, 'current_version' => 3, 'contentclass_id' => 2 ) );
        $object->MainNodeID = $mainNodeID;
        $GLOBALS['eZContentObjectContentObjectCache'][self::OBJECT_ID] = $object;
        return $object;
    }

    /**
     * [action, param] of every row the recorder was asked to queue.
     */
    private function rows( eZPendingIndexActionsTestDB $db )
    {
        $rows = array();
        foreach ( $db->Statements as $sql )
        {
            if ( preg_match( "/^INSERT INTO ezpending_actions\\( action, created, param \\) VALUES \\( '([a-z_]+)', ([0-9]+), '([0-9]+)' \\)$/", $sql, $m ) )
                $rows[] = array( $m[1], $m[3] );
            else
                $this->fail( "not a queued row: $sql" );
        }
        return $rows;
    }

    private function source( $file )
    {
        return file_get_contents( $file );
    }

    /** PA-01 */
    public function testAQueuedRowCarriesItsTime()
    {
        $db = $this->recorder();
        $before = time();
        eZContentOperationCollection::registerSearchObject( self::OBJECT_ID, 2 );
        $this->assertCount( 1, $db->Statements );
        $this->assertMatchesRegularExpression( "/^INSERT INTO ezpending_actions\\( action, created, param \\) VALUES \\( 'index_object', ([0-9]+), '990501' \\)$/",
                                               $db->Statements[0] );
        preg_match( '/, ([0-9]+), /', $db->Statements[0], $m );
        $this->assertGreaterThanOrEqual( $before, (int)$m[1] );
        $this->assertLessThanOrEqual( time(), (int)$m[1] );

        $mongo = $this->recorder( 'eZPendingIndexActionsTestMongoDB' );
        eZContentOperationCollection::registerSearchObject( self::OBJECT_ID, null, true );
        $this->assertSame( array(), $mongo->Statements );
        $this->assertCount( 1, $mongo->Inserts );
        $this->assertSame( 'ezpending_actions', $mongo->Inserts[0][0] );
        $this->assertSame( array( 'action', 'created', 'param' ), array_keys( $mongo->Inserts[0][1] ) );
        $this->assertSame( 'index_moved_node', $mongo->Inserts[0][1]['action'] );
        $this->assertSame( '990501', $mongo->Inserts[0][1]['param'] );
        $this->assertGreaterThanOrEqual( $before, $mongo->Inserts[0][1]['created'] );
    }

    /** PA-02 */
    public function testAPublicationThatMovedTheObjectIsAMoveForTheEngine()
    {
        $this->cacheObject( 4242 );
        $engine = $this->useEngine( new eZPendingIndexActionsTestFindEngine() );
        $db = $this->recorder();
        $this->setMarks( array( self::OBJECT_ID . '/3' => true ) );

        eZContentOperationCollection::registerSearchObject( self::OBJECT_ID, 3 );

        $this->assertSame( array( array( 4242, self::OBJECT_ID, array(), true ) ), $engine->assignments );
        $this->assertSame( array( array( 'index_moved_node', '990501' ) ), $this->rows( $db ), 'exactly one row, the subtree reindex' );
        $this->assertSame( array(), $this->marks() );
    }

    /** PA-03 */
    public function testWithTheKernelEngineTheMovedPublicationQueuesIndexObject()
    {
        $this->cacheObject( 4242 );
        $this->useEngine( new eZPendingIndexActionsTestKernelEngine() );
        $db = $this->recorder();
        $this->setMarks( array( self::OBJECT_ID . '/3' => true ) );

        eZContentOperationCollection::registerSearchObject( self::OBJECT_ID, 3 );
        eZContentOperationCollection::registerSearchObject( self::OBJECT_ID, 3 );

        $this->assertSame( array( array( 'index_object', '990501' ), array( 'index_object', '990501' ) ), $this->rows( $db ) );
        $this->assertSame( array(), $this->marks(), 'the mark is used once' );

        // without delayed indexing the object is indexed at once, once
        ezpINIHelper::setINISetting( 'site.ini', 'SearchSettings', 'DelayedIndexing', 'disabled' );
        $engine = $this->useEngine( new eZPendingIndexActionsTestKernelEngine() );
        $db = $this->recorder();
        $this->setMarks( array( self::OBJECT_ID . '/3' => true ) );
        eZContentOperationCollection::registerSearchObject( self::OBJECT_ID, 3 );
        $this->assertSame( array(), $db->Statements );
        $this->assertCount( 1, $engine->added );
    }

    /** PA-04 */
    public function testTheMarkBelongsToOneVersion()
    {
        $this->cacheObject( 4242 );
        $engine = $this->useEngine( new eZPendingIndexActionsTestFindEngine() );
        $db = $this->recorder();
        $this->setMarks( array( self::OBJECT_ID . '/3' => true ) );

        eZContentOperationCollection::registerSearchObject( self::OBJECT_ID, 2 );
        $this->assertSame( array(), $engine->assignments );
        $this->assertSame( array( array( 'index_object', '990501' ) ), $this->rows( $db ) );
        $this->assertSame( array( self::OBJECT_ID . '/3' => true ), $this->marks() );

        // a caller without a version (the engines' own calls) never uses the mark
        eZContentOperationCollection::registerSearchObject( self::OBJECT_ID );
        $this->assertSame( array( self::OBJECT_ID . '/3' => true ), $this->marks() );
    }

    /** PA-05 */
    public function testPublishNodeMarksTheVersionWhenItMovesTheNode()
    {
        $source = $this->source( 'kernel/content/ezcontentoperationcollection.php' );
        $this->assertMatchesRegularExpression(
            '/\$originalNode->move\( \$parentNodeID \);\s*\$existingNode = eZContentObjectTreeNode::fetchNode\( \$originalObjectID, \$parentNodeID \);\s*\$updateFields = true;\s*(\/\/[^\n]*\s*)*self::\$movedByPublication\[\(int\)\$objectID \. \'\/\' \. \(int\)\$versionNum\] = true;/',
            $source );
        $this->assertSame( 1, preg_match_all( '/self::\$movedByPublication\[[^\]]*\] = true;/', $source ),
                           'publishNode() is the only place that marks' );
    }

    /** PA-06 */
    public function testARealMoveQueuesTheSubtreeReindex()
    {
        $this->assertMatchesRegularExpression( '/eZSearch::addNodeAssignment\([^;]*, true \);/',
                                               $this->source( 'kernel/classes/ezcontentobjecttreenodeoperations.php' ) );

        $engine = $this->useEngine( new eZPendingIndexActionsTestFindEngine() );
        $db = $this->recorder();
        eZSearch::addNodeAssignment( 4243, self::OBJECT_ID, array( 4243 ), true );
        $this->assertSame( array( array( 'index_moved_node', '990501' ) ), $this->rows( $db ) );
        $this->assertCount( 1, $engine->assignments );

        $this->useEngine( new eZPendingIndexActionsTestKernelEngine() );
        $db = $this->recorder();
        $this->assertFalse( eZSearch::addNodeAssignment( 4243, self::OBJECT_ID, array( 4243 ), true ) );
        $this->assertSame( array(), $db->Statements, 'the kernel engine has no subtree to reindex' );
    }

    /** PA-07 */
    public function testANewLocationIsNoMove()
    {
        $this->assertMatchesRegularExpression( '/eZSearch::addNodeAssignment\( \$nodeID, \$objectID, \$selectedNodeIDArray \);/',
                                               $this->source( 'kernel/content/ezcontentoperationcollection.php' ) );
        $this->useEngine( new eZPendingIndexActionsTestFindEngine() );
        $db = $this->recorder();
        eZSearch::addNodeAssignment( 4242, self::OBJECT_ID, array( 4244 ) );
        $this->assertSame( array( array( 'index_object', '990501' ) ), $this->rows( $db ) );
    }

    /** PA-08 */
    public function testTheTrashRemovesBothKindsOfEntry()
    {
        $method = new ReflectionMethod( 'eZContentOperationCollection', 'removePendingIndexActions' );
        $db = $this->recorder();
        $method->invoke( null, array() );
        $this->assertSame( array(), $db->Statements );

        $method->invoke( null, array( 3 => 990501, 7 => 990502 ) );
        $this->assertCount( 2, $db->Statements );
        foreach ( array( 'index_object', 'index_moved_node' ) as $i => $action )
        {
            $sql = preg_replace( '/\s+/', ' ', $db->Statements[$i] );
            $this->assertStringStartsWith( 'DELETE FROM ezpending_actions', $sql );
            $this->assertStringContainsString( "action='$action'", str_replace( ' ', '', $sql ) );
            $this->assertMatchesRegularExpression( "/param\\s+IN\\s*\\(\\s*'?990501'?\\s*,\\s*'?990502'?\\s*\\)/", $sql );
        }
        $this->assertStringContainsString( 'self::removePendingIndexActions( $pendingActionsToDelete );',
                                           $this->source( 'kernel/content/ezcontentoperationcollection.php' ) );
    }

    /** PA-09 */
    public function testInheritingStepsFillOnlyTheirIntendedOptionalParameters()
    {
        // operation/step => the optional method parameters the operation's parameters fill by position
        $intended = array(
            'content/delete/delete' => array( 'moveToTrash<-move_to_trash' ),
            'content/swap/swap' => array( 'nodeIdList<-node_id_list' ),
            'content/translationavailable/translationavailable' => array( 'status<-status' ),
            'content/sort/sort' => array( 'sortingOrder<-sorting_order' ),
            'content/updatepriority/updatepriority' => array( 'priorityArray<-priority', 'priorityIDArray<-priority_id' ),
            'user/activation/activation' => array( 'enableUser<-is_enabled' ),
        );
        $found = array();
        $checked = 0;
        foreach ( glob( 'kernel/*/operation_definition.php' ) as $file )
        {
            $module = basename( dirname( $file ) );
            $OperationList = array();
            include $file;
            foreach ( $OperationList as $operationName => $operation )
            {
                $class = $operation['default_call_method']['class'] ?? null;
                if ( isset( $operation['default_call_method']['include_file'] ) && !class_exists( $class ) )
                    include_once $operation['default_call_method']['include_file'];
                $operationParameters = array_column( $operation['parameters'] ?? array(), 'name' );
                // [step, the parameters it inherits]: a loop's children take the loop's child_parameters
                $steps = array();
                foreach ( $operation['body'] ?? array() as $step )
                    $steps[] = array( $step, $operationParameters );
                while ( $steps )
                {
                    list( $step, $parameters ) = array_shift( $steps );
                    foreach ( $step['children'] ?? array() as $child )
                        $steps[] = array( $child, array_column( $step['child_parameters'] ?? array(), 'name' ) );
                    if ( ( $step['type'] ?? '' ) !== 'method' || isset( $step['parameters'] ) )
                        continue;
                    $method = new ReflectionMethod( $step['class'] ?? $class, $step['method'] );
                    $key = "$module/$operationName/{$step['name']}";
                    $this->assertGreaterThanOrEqual( $method->getNumberOfRequiredParameters(), count( $parameters ),
                                                     "$key: the operation passes fewer parameters than the method requires" );
                    foreach ( $method->getParameters() as $i => $parameter )
                    {
                        if ( $parameter->isOptional() && isset( $parameters[$i] ) )
                            $found[$key][] = $parameter->getName() . '<-' . $parameters[$i];
                    }
                    ++$checked;
                }
            }
        }
        ksort( $intended );
        ksort( $found );
        $this->assertSame( $intended, $found, 'an operation parameter lands in an optional method parameter: give the step parameters of its own' );
        $this->assertGreaterThan( 40, $checked );
    }

    /** PA-10 */
    public function testTheCronjobProcessesBothActionsOldestFirst()
    {
        $source = $this->source( 'kernel/private/classes/cronjobs/indexcontent.php' );
        $this->assertStringContainsString( "WHERE action = 'index_object' OR action = 'index_moved_node' GROUP BY param, action ORDER BY min(created)", $source );
        $this->assertStringContainsString( "[ '\$match'   => [ 'action' => [ '\$in' => [ 'index_object', 'index_moved_node' ] ] ] ]", $source );
        $this->assertMatchesRegularExpression( "/if \\( \\\$action == 'index_moved_node' \\).{0,2000}subTreeCount/s", $source );
    }
}
