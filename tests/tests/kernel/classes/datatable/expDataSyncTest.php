<?php
/**
 * The synchronisation between content and data tables (expDataSync, expDataSyncDefinition, expDataSyncLink,
 * expDataSyncConflict, expDataSyncListener, the cronjob part datatablesync, the workflow event "Mirror to data
 * tables"; doc/eng-us/features/6.0/data-tables.md, "Synchronisation"). No installation: the pure parts directly, the
 * persistent classes and the listener on the SQLite fixture (the site's database in memory). What needs content (a
 * run that reads or publishes objects) is covered by the integration in a throwaway installation.
 *
 *  SY-01 - normalize(): directions, modes, rules and states from their lists, the map from lines or an array (bad
 *          column names left out), keys from a list or a comma text, options limited to the known ones
 *  SY-02 - parseMap() / mapText() round trip, comments and lines without "=" left out
 *  SY-03 - problems() without content: the messages of an empty definition, an unknown class field, an unmapped key, a
 *          read-only connection
 *  SY-04 - hash(): only attribute columns and @name/@remote_id count, the order of the values does not
 *  SY-05 - rowKey(): one or more keys, an empty value or a 0 *_id gives '', at most 255 characters
 *  SY-06 - winner(): content, table and ask as given; newest by the times, a table without a time loses
 *  SY-07 - typed(): an empty relation is 0 in an integer column, numbers and texts as their fields type them
 *  SY-08 - The persistent classes on the fixture: map, keys and options arrays, the open conflicts, differences()
 *  SY-09 - The listener: nothing is noted without a live synchronisation or while a synchronisation writes; a removal
 *          wins over a publish, a publish over a move; flush() empties the list; the 50th object flushes
 *  SY-10 - The cronjob part does nothing without the tables
 *  SY-11 - The workflow event type: the chosen synchronisations from the form, attributeDecoder(), execute() never
 *          stops a workflow
 *  SY-12 - The kernel calls the listener at publish, move, location removal, purge and trash, guarded by class_exists,
 *          and notifies content/publish, content/move and content/remove
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expDataTableSqliteFixture.php';

/** An event of a workflow, as much as the event type reads of it */
class expDataSyncTestEvent
{
    public $data = array( 'id' => 7, 'data_text1' => '' );

    public function attribute( $name )
    {
        return isset( $this->data[$name] ) ? $this->data[$name] : null;
    }

    public function setAttribute( $name, $value )
    {
        $this->data[$name] = $value;
    }
}

/** The posted form, as much as fetchHTTPInput() reads of it */
class expDataSyncTestHTTP
{
    public $post = array();

    public function hasPostVariable( $name )
    {
        return isset( $this->post[$name] );
    }

    public function postVariable( $name )
    {
        return $this->post[$name];
    }
}

/** A workflow process, as much as execute() reads of it */
class expDataSyncTestProcess
{
    public $parameters = array();

    public function attribute( $name )
    {
        return $name === 'parameter_list' ? $this->parameters : null;
    }
}

class expDataSyncTest extends PHPUnit\Framework\TestCase
{
    use expDataTableSqliteFixture;

    private $fixture = false;

    /** @var callable|null the exception handler before the test */
    private $exceptionHandler;

    protected function setUp(): void
    {
        $this->exceptionHandler = self::exceptionHandler();
    }

    /** @return callable|null the exception handler in place */
    private static function exceptionHandler()
    {
        $handler = set_exception_handler( null );
        restore_exception_handler();
        return $handler;
    }

    protected function tearDown(): void
    {
        // the cleanup handler of the listener and a first lookup of the current user may install the kernel's handler
        for ( $i = 0; $i < 5 && self::exceptionHandler() !== $this->exceptionHandler; $i++ )
            restore_exception_handler();
        expDataSync::$applying = false;
        expDataSyncListener::reset();
        self::setInstalled( null );
        if ( $this->fixture )
            $this->tearDownFixture();
        $this->fixture = false;
    }

    private function fixture( $syncTables = true )
    {
        $this->setUpFixture( false );
        $this->fixture = true;
        self::setInstalled( null );
        if ( $syncTables )
        {
            $messages = array();
            $this->assertTrue( expDataSync::install( $messages ), implode( '; ', $messages ) );
            $this->assertTrue( expDataSync::installed( true ) );
        }
    }

    private static function setInstalled( $value )
    {
        $property = new ReflectionProperty( 'expDataSync', 'installed' );
        if ( PHP_VERSION_ID < 80100 )
            $property->setAccessible( true );
        $property->setValue( null, $value );
    }

    private static function pending()
    {
        $property = new ReflectionProperty( 'expDataSyncListener', 'pending' );
        if ( PHP_VERSION_ID < 80100 )
            $property->setAccessible( true );
        return $property->getValue();
    }

    private static function root()
    {
        return expDataTableTestLoader::root();
    }

    /** SY-01 */
    public function testNormalize()
    {
        $d = expDataSync::normalize( array( 'name' => '  Articles ', 'direction' => 'two_way', 'mode' => 'live', 'conflict' => 'ask',
                                            'class' => 'myRow', 'content_class' => 'article', 'subtree' => '42',
                                            'map' => "title = title\nbad-name = x\nnode_id = @node_id", 'keys' => ' contentobject_id , remote_id ',
                                            'interval' => '-5', 'state' => 'disabled',
                                            'options' => array( 'remove_missing' => true, 'language' => 'ger-DE', 'evil' => 1 ) ) );
        $this->assertSame( 'Articles', $d['name'] );
        $this->assertSame( 'two_way', $d['direction'] );
        $this->assertSame( 'live', $d['mode'] );
        $this->assertSame( 'ask', $d['conflict'] );
        $this->assertSame( 42, $d['subtree'] );
        $this->assertSame( array( 'title' => 'title', 'node_id' => '@node_id' ), $d['map'] );
        $this->assertSame( array( 'contentobject_id', 'remote_id' ), $d['keys'] );
        $this->assertSame( 0, $d['interval'] );
        $this->assertSame( 'disabled', $d['state'] );
        $this->assertSame( array( 'remove_missing' => true, 'language' => 'ger-DE' ), $d['options'] );

        $bad = expDataSync::normalize( array( 'direction' => 'sideways', 'mode' => 'hourly', 'conflict' => 'coin', 'state' => 'odd',
                                              'map' => array( 'ok' => 'title', '1bad' => 'x', 'empty' => '  ' ), 'keys' => array( 'a', '' ) ) );
        $this->assertSame( '', $bad['direction'] );
        $this->assertSame( '', $bad['mode'] );
        $this->assertSame( '', $bad['conflict'] );
        $this->assertSame( 'enabled', $bad['state'] );
        $this->assertSame( array( 'ok' => 'title' ), $bad['map'] );
        $this->assertSame( array( 'a' ), $bad['keys'] );
        $this->assertSame( 3600, expDataSync::normalize( array() )['interval'] );
        $this->assertSame( 'content_to_table', expDataSync::normalize( array() )['direction'] );
        $this->assertSame( 'once', expDataSync::normalize( array() )['mode'] );
        $this->assertSame( 'newest', expDataSync::normalize( array() )['conflict'] );
    }

    /** SY-02 */
    public function testParseMapAndMapText()
    {
        $map = expDataSync::parseMap( "# the map\ncontentobject_id = @object_id\n\ntitle=title   # the title\nno equals sign\n = nothing\nkey =\n" );
        $this->assertSame( array( 'contentobject_id' => '@object_id', 'title' => 'title' ), $map );
        $this->assertSame( "contentobject_id = @object_id\ntitle = title", expDataSync::mapText( $map ) );
        $this->assertSame( $map, expDataSync::parseMap( expDataSync::mapText( $map ) ) );
        $this->assertSame( '', expDataSync::mapText( array() ) );
    }

    /** SY-03 */
    public function testProblemsWithoutContent()
    {
        $this->fixture( false );
        $problems = expDataSync::problems( expDataSync::normalize( array( 'direction' => 'x', 'mode' => 'x', 'conflict' => 'x' ) ) );
        foreach ( array( 'Give the synchronisation a name.', 'Choose a direction.', 'Choose how it runs.', 'Choose a conflict rule.',
                         'Choose a persistent class with a table.', 'Choose a content class.', 'Map at least one column.',
                         'Name the key column(s) a row is matched by.' ) as $message )
            $this->assertContains( $message, $problems );
        $this->assertNotEmpty( preg_grep( '/^Choose the node/', $problems ) );

        $problems = expDataSync::problems( expDataSync::normalize( array( 'name' => 'n', 'class' => 'expDTFixtureRow',
                                                                         'map' => array( 'title' => 'title', 'nothere' => 'x' ),
                                                                         'keys' => 'remote_id' ) ) );
        $this->assertNotContains( 'Choose a persistent class with a table.', $problems );
        $this->assertContains( "The class has no field 'nothere'.", $problems );
        $this->assertContains( "The key 'remote_id' is not mapped.", $problems );

        expDataConnections::setConnections( array( 't691m2' => array( 'engine' => 'sqlite', 'database' => 'var/datatable/x.sqlite', 'read_only' => true ) ) );
        $readOnly = expDataSync::problems( expDataSync::normalize( array( 'name' => 'n', 'class' => 'expDTFixtureRow', 'direction' => 'two_way',
                                                                          'map' => array( 'title' => 'title' ), 'keys' => 'title' ) ) );
        $this->assertContains( 'The class is on a read-only connection: the table cannot be written.', $readOnly );
        $toContent = expDataSync::problems( expDataSync::normalize( array( 'name' => 'n', 'class' => 'expDTFixtureRow', 'direction' => 'table_to_content',
                                                                           'map' => array( 'title' => 'title' ), 'keys' => 'title' ) ) );
        $this->assertNotContains( 'The class is on a read-only connection: the table cannot be written.', $toContent );
    }

    /** SY-04 */
    public function testHash()
    {
        $map = array( 'contentobject_id' => '@object_id', 'node_id' => '@node_id', 'name' => '@name', 'remote_id' => '@remote_id', 'title' => 'title',
                      'modified' => '@modified' );
        $a = array( 'contentobject_id' => 1, 'node_id' => 2, 'name' => 'N', 'remote_id' => 'r', 'title' => 'T', 'modified' => 100 );
        $b = array( 'modified' => 999, 'title' => 'T', 'remote_id' => 'r', 'name' => 'N', 'node_id' => 3, 'contentobject_id' => 9 );
        $this->assertSame( expDataSync::hash( $a, $map ), expDataSync::hash( $b, $map ), 'the object, node and times do not count, nor the order' );
        $this->assertNotSame( expDataSync::hash( $a, $map ), expDataSync::hash( array( 'title' => 'T2' ) + $a, $map ) );
        $this->assertNotSame( expDataSync::hash( $a, $map ), expDataSync::hash( array( 'name' => 'N2' ) + $a, $map ) );
        $this->assertNotSame( expDataSync::hash( $a, $map ), expDataSync::hash( array( 'remote_id' => 'r2' ) + $a, $map ) );
        $this->assertSame( expDataSync::hash( array( 'title' => '' ), array( 'title' => 'title' ) ), expDataSync::hash( array(), array( 'title' => 'title' ) ),
                           'a missing value is an empty one' );
        $this->assertSame( 32, strlen( expDataSync::hash( $a, $map ) ) );
    }

    /** SY-05 */
    public function testRowKey()
    {
        $this->assertSame( '42', expDataSync::rowKey( array( 'contentobject_id' => 42 ), array( 'contentobject_id' ) ) );
        $this->assertSame( 'a|7', expDataSync::rowKey( array( 'remote_id' => 'a', 'lang' => 7 ), array( 'remote_id', 'lang' ) ) );
        $this->assertSame( '', expDataSync::rowKey( array( 'contentobject_id' => 0 ), array( 'contentobject_id' ) ), 'a 0 id is no key' );
        $this->assertSame( '', expDataSync::rowKey( array( 'remote_id' => '' ), array( 'remote_id' ) ) );
        $this->assertSame( '', expDataSync::rowKey( array(), array( 'remote_id' ) ) );
        $this->assertSame( '0', expDataSync::rowKey( array( 'position' => 0 ), array( 'position' ) ), 'a 0 that is no id is a key' );
        $this->assertSame( 255, mb_strlen( expDataSync::rowKey( array( 'remote_id' => str_repeat( 'x', 400 ) ), array( 'remote_id' ) ) ) );
    }

    /** SY-06 */
    public function testWinner()
    {
        foreach ( array( 'content', 'table', 'ask' ) as $rule )
            $this->assertSame( $rule, expDataSync::winner( $rule, 100, 200 ) );
        $this->assertSame( 'table', expDataSync::winner( 'newest', 100, 200 ) );
        $this->assertSame( 'content', expDataSync::winner( 'newest', 200, 100 ) );
        $this->assertSame( 'content', expDataSync::winner( 'newest', 100, 100 ), 'the same time: content' );
        $this->assertSame( 'content', expDataSync::winner( 'newest', 100, 0 ), 'a table without a time loses' );
        $this->assertSame( 'content', expDataSync::winner( 'anything else', 1, 0 ) );
    }

    /** SY-07 */
    public function testTyped()
    {
        $definition = array( 'fields' => array( 'image' => array( 'datatype' => 'integer' ), 'price' => array( 'datatype' => 'float' ),
                                                'title' => array( 'datatype' => 'string' ), 'body' => array( 'datatype' => 'text' ) ) );
        $typed = expDataSync::typed( array( 'image' => '', 'price' => '9.50', 'title' => 12, 'body' => null, 'other' => 'kept' ), $definition );
        $this->assertSame( 0, $typed['image'] );
        $this->assertSame( 9.5, $typed['price'] );
        $this->assertSame( '12', $typed['title'] );
        $this->assertSame( '', $typed['body'] );
        $this->assertSame( 'kept', $typed['other'] );
        $this->assertSame( expDataSync::typed( array( 'image' => '0' ), $definition ), expDataSync::typed( array( 'image' => '' ), $definition ),
                           'an empty relation and 0 compare equal' );
    }

    /** SY-08 */
    public function testPersistentClasses()
    {
        $this->fixture();
        $sync = new expDataSyncDefinition( array( 'name' => 'Articles', 'direction' => 'two_way', 'run_mode' => 'live', 'class_name' => 'expDTFixtureRow',
                                                  'content_class' => 'article', 'subtree_node_id' => 2, 'key_columns' => ' remote_id , , lang',
                                                  'map' => json_encode( array( 'title' => 'title' ) ), 'options' => '{"remove_missing":true}',
                                                  'conflict_rule' => 'ask', 'state' => 'enabled' ) );
        $sync->store();
        $id = (int)$sync->attribute( 'id' );
        $this->assertGreaterThan( 0, $id );
        $again = expDataSyncDefinition::fetchByID( $id );
        $this->assertSame( array( 'title' => 'title' ), $again->mapArray() );
        $this->assertSame( array( 'remote_id', 'lang' ), $again->keysArray() );
        $this->assertSame( array( 'remove_missing' => true ), $again->optionsArray() );
        $this->assertCount( 1, expDataSyncDefinition::fetchList( array( 'run_mode' => 'live' ) ) );
        $this->assertCount( 0, expDataSyncDefinition::fetchList( array( 'run_mode' => 'once' ) ) );
        $broken = new expDataSyncDefinition( array( 'map' => 'not json', 'options' => '' ) );
        $this->assertSame( array(), $broken->mapArray() );
        $this->assertSame( array(), $broken->optionsArray() );

        $link = new expDataSyncLink( array( 'sync_id' => $id, 'row_key' => 'r1', 'object_id' => 42 ) );
        $link->store();
        $conflict = new expDataSyncConflict( array( 'sync_id' => $id, 'link_id' => (int)$link->attribute( 'id' ), 'object_id' => 42, 'row_key' => 'r1',
                                                    'state' => 'open', 'detected' => 1,
                                                    'content_values' => json_encode( array( 'title' => 'From content', 'price' => 1, 'same' => 'x' ) ),
                                                    'row_values' => json_encode( array( 'title' => 'From the table', 'price' => '1', 'same' => 'x', 'extra' => 'e' ) ) ) );
        $conflict->store();
        $this->assertSame( 1, $again->openConflictCount() );
        $differences = $conflict->differences();
        $this->assertSame( array( 'title', 'extra' ), array_column( $differences, 'column' ), 'equal values (1 and "1") are no difference' );
        $this->assertSame( 'From content', $differences[0]['content'] );
        $this->assertSame( 'From the table', $differences[0]['row'] );
        $this->assertSame( '', $differences[1]['content'] );
        $conflict->setAttribute( 'state', 'resolved' );
        $conflict->store();
        $this->assertSame( 0, $again->openConflictCount() );
        $this->assertSame( array(), ( new expDataSyncConflict( array( 'content_values' => '{bad' ) ) )->contentArray() );
    }

    /** SY-09 */
    public function testListener()
    {
        $this->fixture();
        // no live synchronisation: nothing is noted
        expDataSyncListener::reset();
        expDataSyncListener::published( 5 );
        $this->assertSame( array(), self::pending() );

        $live = new expDataSyncDefinition( array( 'name' => 'live', 'direction' => 'content_to_table', 'run_mode' => 'live', 'state' => 'enabled',
                                                  'class_name' => 'expDTFixtureRow', 'content_class' => 'article', 'map' => '{}' ) );
        $live->store();
        expDataSyncListener::reset();
        expDataSync::$applying = true;
        expDataSyncListener::published( 5 );
        $this->assertSame( array(), self::pending(), 'nothing while a synchronisation writes content' );
        expDataSync::$applying = false;

        expDataSyncListener::moved( 0, 5 );
        $this->assertSame( array( 5 => 'move' ), self::pending() );
        expDataSyncListener::published( 5 );
        $this->assertSame( array( 5 => 'publish' ), self::pending(), 'a publish wins over a move' );
        expDataSyncListener::moved( 0, 5 );
        $this->assertSame( array( 5 => 'publish' ), self::pending() );
        expDataSyncListener::gone( 5 );
        $this->assertSame( array( 5 => 'remove' ), self::pending(), 'a removal wins' );
        expDataSyncListener::published( 5 );
        $this->assertSame( array( 5 => 'remove' ), self::pending() );
        expDataSyncListener::removed( array( 6, 7 ), array( 60, 70 ) );
        $this->assertSame( array( 5 => 'remove', 6 => 'move', 7 => 'move' ), self::pending(), 'a removed location is looked at again' );
        expDataSyncListener::published( 0 );
        $this->assertArrayNotHasKey( 0, self::pending() );

        $this->assertSame( 0, expDataSyncListener::flush(), 'nothing of these objects is mirrored here' );
        $this->assertSame( array(), self::pending() );

        for ( $i = 1; $i < 50; $i++ )
            expDataSyncListener::published( 100 + $i );
        $this->assertCount( 49, self::pending() );
        expDataSyncListener::published( 200 );
        $this->assertSame( array(), self::pending(), 'the 50th object flushes the list' );
    }

    /** SY-10 */
    public function testCronjobPartWithoutTables()
    {
        $this->fixture( false );
        $this->assertFalse( expDataSync::installed( true ) );
        require_once self::root() . '/kernel/private/classes/cronjobs/datatablesync.php';
        $part = new \Exponential\Cronjob\Kernel\Datatablesync();
        $this->assertSame( 0, $part->run( array( 'cli' => eZCLI::instance(), 'isQuiet' => true ) ) );
        $this->assertSame( array(), expDataSync::due() );
    }

    /** SY-11 */
    public function testWorkflowEventType()
    {
        if ( !class_exists( 'eZWorkflowEventType' ) )
            $this->markTestSkipped( 'eZWorkflowEventType is not available' );
        require_once self::root() . '/kernel/classes/workflowtypes/event/expdatatablemirror/expdatatablemirrortype.php';
        $this->fixture( false );
        $type = new expDataTableMirrorType();
        $this->assertSame( 'expdatatablemirror', expDataTableMirrorType::WORKFLOW_TYPE_STRING );
        $event = new expDataSyncTestEvent();
        $http = new expDataSyncTestHTTP();
        $http->post['WorkflowEvent_event_expdatatablemirror_syncs_7'] = array( '3', 'x', '0', '12' );
        $type->fetchHTTPInput( $http, 'WorkflowEvent', $event );
        $this->assertSame( '3,12', $event->attribute( 'data_text1' ) );
        $this->assertSame( array( 3, 12 ), $type->attributeDecoder( $event, 'selected_syncs' ) );
        $this->assertSame( array(), $type->attributeDecoder( $event, 'syncs' ), 'no sync tables: no synchronisations to choose' );
        $this->assertNull( $type->attributeDecoder( $event, 'other' ) );
        $this->assertSame( array( 'selected_syncs', 'syncs' ), $type->typeFunctionalAttributes() );
        $type->fetchHTTPInput( new expDataSyncTestHTTP(), 'WorkflowEvent', $event );
        $this->assertSame( '', $event->attribute( 'data_text1' ), 'nothing ticked: the class decides' );

        $process = new expDataSyncTestProcess();
        $this->assertSame( eZWorkflowType::STATUS_ACCEPTED, $type->execute( $process, $event ) );
        $process->parameters = array( 'object_id' => 42 );
        $this->assertSame( eZWorkflowType::STATUS_ACCEPTED, $type->execute( $process, $event ), 'without the sync tables the workflow goes on' );
    }

    /** SY-12 */
    public function testTheKernelCallsTheListener()
    {
        $read = function ( $file ) {
            return (string)file_get_contents( self::root() . '/' . $file );
        };
        $operations = $read( 'kernel/content/ezcontentoperationcollection.php' );
        $this->assertMatchesRegularExpression( "/notify\( 'content\/publish', array\( \(int\)\\\$objectID, \(int\)\\\$versionNum \) \);\s*if \( class_exists\( 'expDataSyncListener' \) \)\s*expDataSyncListener::published\(/", $operations );
        $this->assertMatchesRegularExpression( "/notify\( 'content\/remove', .*\);\s*if \( class_exists\( 'expDataSyncListener' \) \)\s*expDataSyncListener::removed\(/", $operations );
        $node = $read( 'kernel/classes/ezcontentobjecttreenode.php' );
        $this->assertMatchesRegularExpression( "/notify\( 'content\/move', .*\);\s*if \( class_exists\( 'expDataSyncListener' \) \)\s*expDataSyncListener::moved\(/", $node );
        $object = $read( 'kernel/classes/ezcontentobject.php' );
        $this->assertMatchesRegularExpression( "/function purge\(\)\s*\{\s*\\\$delID = \\\$this->ID;\s*(\/\/[^\n]*\n\s*)*if \( class_exists\( 'expDataSyncListener' \) \)\s*expDataSyncListener::gone\( \(int\)\\\$delID \);/", $object );
        $this->assertMatchesRegularExpression( "/function removeThis\( \\\$nodeID = null \)\s*\{\s*\\\$delID = \\\$this->ID;\s*(\/\/[^\n]*\n\s*)*if \( class_exists\( 'expDataSyncListener' \) \)\s*expDataSyncListener::removed\( array\( \(int\)\\\$delID \) \);/", $object );
    }
}
