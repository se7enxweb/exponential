<?php
/**
 * The kernel code that asks for sets of ids of any size through expDBInList, against a database that records the
 * statements: each asks in lists of at most chunkSize() ids and handles a failed list instead of reading rows out of
 * false. No database.
 *
 *  IC-01 - eZContentObjectTreeNode::fetchAliasesFromNodeList(): 2500 nodes in three statements; false on a failure
 *  IC-02 - eZContentObjectTreeNode::getParentNodeIdListByContentObjectID(): lists, the main-node tail, a failure is []
 *  IC-03 - eZContentObjectTreeNode::findMainNodeArray(): lists with the conditions after the IN list
 *  IC-04 - eZSearchEngine::updateObjectsSection(): one UPDATE per list, none for no objects
 *  IC-05 - ezpSessionHandlerDB::deleteByUserIDs(): sessions of 2500 users read in lists; a failed list removes nothing
 *  IC-06 - eZSubtreeNotificationRule::fetchUserList(): subscriber nodes and their roles in lists; a failure is []
 *  IC-07 - TrashList::classOptions() on MongoDB: the objects of the trash in lists; a failed list gives no class
 *  IC-08 - eZContentObject::fetchIDArray(): the objects in lists, the language filter after each IN list
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expDBInListCallersTestDB extends eZNullDB
{
    public $name = 'mysql';
    /** @var string[] */
    public $queries = array();
    /** @var string[] statements starting with one of these fail */
    public $failOn = array();
    /** @var callable[] statement prefix => function ( $sql, int[] $ids ) returning the rows */
    public $answers = array();

    public function __construct()
    {
    }

    function databaseName()
    {
        return $this->name;
    }

    function generateSQLINStatement( $elements, $columnName = '', $not = false, $unique = true, $type = false )
    {
        if ( !is_array( $elements ) )
            $elements = array( $elements );
        return $columnName . ( $not ? ' NOT IN' : ' IN' ) . ' ( ' . implode( ', ', $elements ) . ' )';
    }

    function escapeString( $str )
    {
        return addslashes( (string)$str );
    }

    function concatString( $strings = array() )
    {
        return implode( ' || ', $strings );
    }

    function bitAnd( $arg1, $arg2 )
    {
        return "( $arg1 & $arg2 )";
    }

    private function answer( $sql )
    {
        $this->queries[] = $sql;
        $flat = preg_replace( '/\s+/', ' ', trim( $sql ) );
        foreach ( $this->failOn as $prefix )
            if ( strpos( $flat, $prefix ) === 0 )
                return false;
        foreach ( $this->answers as $prefix => $answer )
            if ( strpos( $flat, $prefix ) === 0 )
                return $answer( $flat, self::inList( $flat ) );
        return array();
    }

    function arrayQuery( $sql, $params = array(), $server = false )
    {
        return $this->answer( $sql );
    }

    function query( $sql, $server = false )
    {
        return $this->answer( $sql ) !== false;
    }

    /** @return int[] the ids of the first IN list of a statement */
    public static function inList( $sql )
    {
        return preg_match( '/ IN \( ([0-9, ]*) \)/', $sql, $m ) && trim( $m[1] ) !== '' ? array_map( 'intval', explode( ', ', $m[1] ) ) : array();
    }

    /** @return string[] the statements (whitespace folded) that start with $prefix */
    public function queriesStartingWith( $prefix )
    {
        $out = array();
        foreach ( $this->queries as $sql )
        {
            $flat = preg_replace( '/\s+/', ' ', trim( $sql ) );
            if ( strpos( $flat, $prefix ) === 0 )
                $out[] = $flat;
        }
        return $out;
    }
}

class expDBInListCallersTest extends PHPUnit\Framework\TestCase
{
    private $hadDB;
    private $previousDB;
    /** @var expDBInListCallersTestDB */
    private $db;

    protected function setUp(): void
    {
        $this->hadDB = array_key_exists( 'eZDBGlobalInstance', $GLOBALS );
        $this->previousDB = $this->hadDB ? $GLOBALS['eZDBGlobalInstance'] : null;
        $this->db = new expDBInListCallersTestDB();
        eZDB::setInstance( $this->db );
        expDBInList::setChunkSize( 1000 );
    }

    /** @var callable|null the exception handler before the test */
    private $handler;

    protected function assertPreConditions(): void
    {
        $this->handler = set_exception_handler( null );
        restore_exception_handler();
    }

    protected function tearDown(): void
    {
        // the language list of fetchIDArray() registers eZExecution's exception handler once per process
        $current = set_exception_handler( null );
        restore_exception_handler();
        if ( $current === array( 'eZExecution', 'defaultExceptionHandler' ) && $this->handler !== $current )
            restore_exception_handler();
        expDBInList::setChunkSize( null );
        if ( $this->hadDB )
            eZDB::setInstance( $this->previousDB );
        else
            unset( $GLOBALS['eZDBGlobalInstance'] );
    }

    /** every list of the statements at most 1000 ids, all ids asked once */
    private function assertLists( array $statements, array $ids, $message = '' )
    {
        $asked = array();
        foreach ( $statements as $sql )
        {
            $list = expDBInListCallersTestDB::inList( $sql );
            $this->assertLessThanOrEqual( 1000, count( $list ), $message );
            $asked = array_merge( $asked, $list );
        }
        sort( $asked );
        $this->assertSame( $ids, $asked, $message );
    }

    public function testIC01FetchAliasesFromNodeList()
    {
        $this->db->answers['SELECT node_id, path_identification_string'] = function ( $sql, $ids ) {
            $rows = array();
            foreach ( $ids as $id )
                $rows[] = array( 'node_id' => $id, 'path_identification_string' => 'n' . $id );
            return $rows;
        };
        $rows = eZContentObjectTreeNode::fetchAliasesFromNodeList( range( 1, 2500 ) );
        $statements = $this->db->queriesStartingWith( 'SELECT node_id, path_identification_string' );
        $this->assertCount( 3, $statements );
        $this->assertLists( $statements, range( 1, 2500 ) );
        $this->assertCount( 2500, $rows );
        $this->assertSame( 'n2500', $rows[2499]['path_identification_string'] );

        $this->db->failOn = array( 'SELECT node_id, path_identification_string' );
        $this->assertFalse( eZContentObjectTreeNode::fetchAliasesFromNodeList( range( 1, 10 ) ) );
        $this->assertSame( array(), eZContentObjectTreeNode::fetchAliasesFromNodeList( array() ) );
    }

    public function testIC02ParentNodeIDs()
    {
        $this->db->answers['SELECT parent_node_id, contentobject_id FROM ezcontentobject_tree'] = function ( $sql, $ids ) {
            $rows = array();
            foreach ( $ids as $id )
                $rows[] = array( 'parent_node_id' => $id + 100000, 'contentobject_id' => $id );
            return $rows;
        };
        $parents = eZContentObjectTreeNode::getParentNodeIdListByContentObjectID( range( 1, 1001 ), true, true );
        $statements = $this->db->queriesStartingWith( 'SELECT parent_node_id' );
        $this->assertCount( 2, $statements );
        $this->assertLists( $statements, range( 1, 1001 ) );
        foreach ( $statements as $sql )
            $this->assertStringEndsWith( ' ) AND node_id = main_node_id', $sql );
        $this->assertCount( 1001, $parents );
        $this->assertSame( array( 101001 ), $parents[1001] );

        $this->db->failOn = array( 'SELECT parent_node_id' );
        $this->assertSame( array(), eZContentObjectTreeNode::getParentNodeIdListByContentObjectID( range( 1, 1500 ) ) );
    }

    public function testIC03FindMainNodeArray()
    {
        $rows = eZContentObjectTreeNode::findMainNodeArray( range( 1, 2001 ), false );
        $statements = $this->db->queriesStartingWith( 'SELECT ezcontentobject.contentclass_id' );
        $this->assertCount( 3, $statements );
        $this->assertLists( $statements, range( 1, 2001 ) );
        foreach ( $statements as $sql )
            $this->assertStringEndsWith( ' ) AND ezcontentobject_tree.main_node_id = ezcontentobject_tree.node_id AND ezcontentclass.version = 0', $sql );
        $this->assertSame( array(), $rows );

        $this->db->failOn = array( 'SELECT ezcontentobject.contentclass_id' );
        $this->assertFalse( eZContentObjectTreeNode::findMainNodeArray( array( 1 ), false ) );
        $this->assertSame( array(), eZContentObjectTreeNode::findMainNodeArray( array( 1 ), true ) );
    }

    public function testIC04UpdateObjectsSection()
    {
        $engine = new eZSearchEngine();
        $engine->updateObjectsSection( range( 1, 2500 ), 7 );
        $statements = $this->db->queriesStartingWith( 'UPDATE ezsearch_object_word_link' );
        $this->assertCount( 3, $statements );
        $this->assertLists( $statements, range( 1, 2500 ) );
        $this->assertStringStartsWith( "UPDATE ezsearch_object_word_link SET section_id='7' WHERE contentobject_id IN ( 1, ", $statements[0] );

        $this->db->queries = array();
        $engine->updateObjectsSection( array(), 7 );
        $this->assertSame( array(), $this->db->queries, 'no objects, no statement' );
    }

    public function testIC05DeleteSessionsByUserIDs()
    {
        $this->db->answers['SELECT session_key FROM ezsession'] = function ( $sql, $ids ) {
            return in_array( 2400, $ids, true ) ? array( array( 'session_key' => 'k2400' ) ) : array();
        };
        $handler = new ezpSessionHandlerDB();
        $handler->deleteByUserIDs( range( 1, 2500 ) );
        $statements = $this->db->queriesStartingWith( 'SELECT session_key FROM ezsession' );
        $this->assertCount( 3, $statements );
        $this->assertLists( $statements, range( 1, 2500 ) );
        $this->assertSame( array( "DELETE FROM ezsession WHERE session_key='k2400'" ), $this->db->queriesStartingWith( 'DELETE FROM ezsession' ) );

        $this->db->queries = array();
        $this->db->failOn = array( 'SELECT session_key FROM ezsession' );
        $handler->deleteByUserIDs( range( 1, 2500 ) );
        $this->assertSame( array(), $this->db->queriesStartingWith( 'DELETE' ), 'a failed list removes nothing' );
    }

    public function testIC06SubtreeNotificationUserList()
    {
        // 2500 subscribers below node 2; their user nodes are 10001..12500
        $this->db->answers['SELECT DISTINCT subtree_rule.user_id'] = function ( $sql, $ids ) {
            $rows = array();
            foreach ( range( 1, 2500 ) as $i )
                $rows[] = array( 'user_id' => 20000 + $i, 'node_id' => 10000 + $i );
            return $rows;
        };
        $this->db->answers['SELECT DISTINCT user_node.node_id'] = function ( $sql, $ids ) {
            $rows = array();
            foreach ( $ids as $id )
                $rows[] = array( 'node_id' => $id, 'path_string' => '/1/5/' . $id . '/', 'contentobject_id' => $id + 10000 );
            return $rows;
        };
        $object = new eZContentObject( array( 'id' => 50, 'contentclass_id' => 2, 'section_id' => 1, 'owner_id' => 14 ) );

        // no role gives read access: the list is empty after the role statements
        $this->assertSame( array(), eZSubtreeNotificationRule::fetchUserList( array( 2 ), $object ) );
        $nodes = $this->db->queriesStartingWith( 'SELECT DISTINCT user_node.node_id' );
        $this->assertCount( 3, $nodes );
        $this->assertLists( $nodes, range( 10001, 12500 ) );
        foreach ( $nodes as $sql )
            $this->assertStringEndsWith( " ) AND user_node.path_string LIKE user_tree.path_string || '%'", $sql );
        $roles = $this->db->queriesStartingWith( 'SELECT DISTINCT user_role.contentobject_id' );
        $this->assertCount( 3, $roles );
        $this->assertLists( $roles, range( 20001, 22500 ) );

        foreach ( array( 'SELECT DISTINCT subtree_rule.user_id', 'SELECT DISTINCT user_node.node_id', 'SELECT DISTINCT user_role.contentobject_id' ) as $failing )
        {
            $this->db->queries = array();
            $this->db->failOn = array( $failing );
            $this->assertSame( array(), eZSubtreeNotificationRule::fetchUserList( array( 2 ), $object ), $failing );
        }
    }

    public function testIC07TrashClassOptionsOnMongo()
    {
        $this->db->name = 'mongo';
        $this->db->answers['SELECT contentobject_id FROM ezcontentobject_trash'] = function () {
            $rows = array();
            foreach ( range( 1, 2500 ) as $id )
                $rows[] = array( 'contentobject_id' => $id );
            return $rows;
        };
        \Exponential\Service\TrashList::classOptions();
        $statements = $this->db->queriesStartingWith( 'SELECT contentclass_id FROM ezcontentobject WHERE' );
        $this->assertCount( 3, $statements );
        $this->assertLists( $statements, range( 1, 2500 ) );

        // the trash cannot be read: no bogus object 0, no further statement
        $this->db->queries = array();
        $this->db->failOn = array( 'SELECT contentobject_id FROM ezcontentobject_trash' );
        $this->assertSame( array(), \Exponential\Service\TrashList::classOptions() );
        $this->assertSame( array(), $this->db->queriesStartingWith( 'SELECT contentclass_id' ) );
    }

    public function testIC08FetchIDArray()
    {
        $this->db->answers['SELECT ezcontentclass.serialized_name_list'] = function ( $sql, $ids ) {
            return array();
        };
        $this->assertSame( array(), eZContentObject::fetchIDArray( range( 1, 1001 ), false ) );
        $statements = $this->db->queriesStartingWith( 'SELECT ezcontentclass.serialized_name_list' );
        $this->assertCount( 2, $statements );
        $this->assertLists( $statements, range( 1, 1001 ) );
        foreach ( $statements as $sql )
            $this->assertStringContainsString( ' ) AND ezcontentobject.id = ezcontentobject_name.contentobject_id AND ', $sql );

        $this->db->failOn = array( 'SELECT ezcontentclass.serialized_name_list' );
        $this->assertSame( array(), eZContentObject::fetchIDArray( array( 1 ), false ) );
    }
}
