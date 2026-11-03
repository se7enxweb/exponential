<?php
/**
 * File containing the expDataTypeInventoryNodesSqliteTest class.
 *
 * The content list of class/datatype/view on a real engine: a SQLite database file of its own with the kernel schema
 * of share/db_schema.dba and the clean data. The folders of the clean data get dates of the test's own, one of them a
 * second location; the list pages through the main nodes only, counts them once, sorts by modified, published, name
 * and class both ways, narrows to one class and moves an offset past the end to the last page. No installation.
 *
 *     php vendor/bin/phpunit --filter expDataTypeInventoryNodesSqliteTest
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expDataTypeInventoryNodesSqliteTest extends PHPUnit\Framework\TestCase
{
    /** @var eZSQLite3DB|null */
    private $db;

    /** @var string */
    private $dir;

    /** @var array|null */
    private $saved;

    /** @var array object id => name, of the folders, oldest modified first */
    private $folders = array();

    /** @var int */
    private $folderClass = 0;

    /** @var int */
    private $userGroupClass = 0;

    protected function setUp(): void
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The SQLite3 extension is not loaded' );
        $root = dirname( __DIR__, 4 );
        $this->dir = $root . '/var/tmp/datatype-nodes-' . getmypid() . '-' . bin2hex( random_bytes( 3 ) );
        mkdir( $this->dir, 0775, true );
        $handler = set_exception_handler( null );
        restore_exception_handler();
        $this->saved = array( 'db' => array_key_exists( 'eZDBGlobalInstance', $GLOBALS ) ? array( $GLOBALS['eZDBGlobalInstance'] ) : null, 'handler' => $handler );
        $this->db = new eZSQLite3DB( array( 'server' => '', 'port' => false, 'user' => '', 'password' => '', 'database' => $this->dir . '/kernel.sqlite',
                                            'use_slave_server' => false, 'slave_server' => '', 'slave_port' => false, 'slave_user' => '',
                                            'slave_password' => '', 'slave_database' => '', 'socket' => false, 'charset' => 'utf-8',
                                            'is_internal_charset' => false, 'builtin_encoding' => false, 'connect_retries' => 1,
                                            'use_persistent_connection' => false, 'show_errors' => false ) );
        $GLOBALS['eZDBGlobalInstance'] = $this->db;
        $messages = array();
        $this->assertTrue( expKernelTableSchema::install( array(), $this->db, $root . '/share/db_schema.dba', $messages ), 'the kernel schema' );
        $this->assertNotFalse( $this->db->insertFile( $root . '/kernel/sql/sqlite', 'cleandata.sql', false ), 'kernel/sql/sqlite/cleandata.sql loads' );
        eZContentLanguage::expireCache( false );
        eZContentObject::clearCache();

        $this->folderClass = (int)$this->db->arrayQuery( "SELECT id FROM ezcontentclass WHERE identifier = 'folder' AND version = 0" )[0]['id'];
        $this->userGroupClass = (int)$this->db->arrayQuery( "SELECT id FROM ezcontentclass WHERE identifier = 'user_group' AND version = 0" )[0]['id'];
        $rows = $this->db->arrayQuery( 'SELECT o.id, o.name FROM ezcontentobject o, ezcontentobject_tree t WHERE t.contentobject_id = o.id AND t.node_id = t.main_node_id'
                                       . ' AND o.status = 1 AND o.contentclass_id = ' . $this->folderClass . ' ORDER BY o.id' );
        $this->assertGreaterThanOrEqual( 3, count( $rows ), 'the clean data has folders' );
        // modified: in the order of the ids; published: the other way round
        $n = count( $rows );
        foreach ( $rows as $i => $row )
        {
            $this->db->query( 'UPDATE ezcontentobject SET modified = ' . ( 1000000 + $i * 10 ) . ', published = ' . ( 1000000 + ( $n - $i ) * 10 ) . ' WHERE id = ' . (int)$row['id'] );
            $this->folders[(int)$row['id']] = (string)$row['name'];
        }
        // a second location of the first folder, below the second one: never a row of its own
        $first = (int)$rows[0]['id'];
        $main = $this->db->arrayQuery( "SELECT * FROM ezcontentobject_tree WHERE contentobject_id = $first AND node_id = main_node_id" )[0];
        $parent = $this->db->arrayQuery( 'SELECT node_id, path_string, depth FROM ezcontentobject_tree WHERE contentobject_id = ' . (int)$rows[1]['id'] )[0];
        $this->db->query( 'INSERT INTO ezcontentobject_tree ( node_id, parent_node_id, contentobject_id, contentobject_version, contentobject_is_published, depth,'
                          . ' path_string, path_identification_string, main_node_id, sort_field, sort_order, priority, modified_subnode, remote_id, is_hidden, is_invisible )'
                          . " VALUES ( 9001, {$parent['node_id']}, $first, {$main['contentobject_version']}, 1, " . ( $parent['depth'] + 1 ) . ", '{$parent['path_string']}9001/',"
                          . " 'second_location', {$main['node_id']}, 1, 1, 0, 0, 'datatypenodestestsecond', 0, 0 )" );
    }

    protected function tearDown(): void
    {
        if ( $this->saved !== null )
        {
            if ( $this->saved['db'] === null )
                unset( $GLOBALS['eZDBGlobalInstance'] );
            else
                $GLOBALS['eZDBGlobalInstance'] = $this->saved['db'][0];
            for ( $i = 0; $i < 5; $i++ )
            {
                $now = set_exception_handler( null );
                restore_exception_handler();
                if ( $now === $this->saved['handler'] )
                    break;
                restore_exception_handler();
            }
        }
        eZContentObject::clearCache();
        eZContentLanguage::expireCache( false );
        if ( $this->db )
            $this->db->close();
        foreach ( (array)glob( $this->dir . '/*' ) as $file )
            @unlink( $file );
        @rmdir( $this->dir );
        parent::tearDown();
    }

    private function page( array $userParameters, ?array $classIDs = null )
    {
        $classIDs = $classIDs === null ? array( $this->folderClass ) : $classIDs;
        return expDataTypeInventory::nodePage( $classIDs, expDataTypeInventory::nodeParameters( $userParameters, $classIDs ), false, array() );
    }

    private static function names( array $page )
    {
        return array_map( function ( $row ) { return $row['name']; }, $page['rows'] );
    }

    public function testMainNodesOnlyNewestFirstByDefault()
    {
        $page = $this->page( array() );
        $this->assertSame( count( $this->folders ), $page['total'], 'counted once per object, the second location is not' );
        $this->assertSame( array_reverse( array_values( $this->folders ) ), self::names( $page ) );
        $row = $page['rows'][0];
        foreach ( array( 'node_id', 'name', 'url_alias', 'class_name', 'class_id', 'section_id', 'section_name', 'languages', 'owner_id', 'owner_name', 'modified', 'published' ) as $key )
            $this->assertArrayHasKey( $key, $row );
        $this->assertSame( $this->folderClass, $row['class_id'] );
        $locales = array_column( $this->db->arrayQuery( 'SELECT locale FROM ezcontent_language' ), 'locale' );
        $this->assertNotEmpty( $row['languages'] );
        $this->assertSame( array(), array_diff( $row['languages'], $locales ), 'the translations of the object, as locales' );
        $this->assertNotSame( '', $row['section_name'] );
    }

    public function testSortsBothWays()
    {
        $byId = array_values( $this->folders );
        $this->assertSame( $byId, self::names( $this->page( array( 'order' => 'asc' ) ) ) );
        $this->assertSame( $byId, self::names( $this->page( array( 'sort' => 'published' ) ) ), 'published runs the other way round' );
        $this->assertSame( array_reverse( $byId ), self::names( $this->page( array( 'sort' => 'published', 'order' => 'asc' ) ) ) );
        $sorted = $byId;
        sort( $sorted, SORT_STRING | SORT_FLAG_CASE );
        $this->assertSame( $sorted, self::names( $this->page( array( 'sort' => 'name' ) ) ) );
        $this->assertSame( array_reverse( $sorted ), self::names( $this->page( array( 'sort' => 'name', 'order' => 'desc' ) ) ) );
    }

    public function testPagesAndAnOffsetPastTheEnd()
    {
        $byId = array_values( $this->folders );
        $all = array_reverse( $byId );
        $page = $this->page( array( 'limit' => '10', 'offset' => '1' ) );
        $this->assertSame( array_slice( $all, 1, 10 ), self::names( $page ) );
        $this->assertSame( 1, $page['offset'] );
        $last = $this->page( array( 'limit' => '10', 'offset' => '500' ) );
        $this->assertSame( 0, $last['offset'], 'past the end: the last page' );
        $this->assertSame( array_slice( $all, 0, 10 ), self::names( $last ) );
    }

    public function testClassSortAndClassFilter()
    {
        $classes = array( $this->folderClass, $this->userGroupClass );
        $both = $this->page( array( 'sort' => 'class', 'limit' => '100' ), $classes );
        $this->assertGreaterThan( count( $this->folders ), $both['total'] );
        $classNames = array_map( function ( $row ) { return $row['class_name']; }, $both['rows'] );
        $sorted = $classNames;
        sort( $sorted, SORT_STRING | SORT_FLAG_CASE );
        $this->assertSame( $sorted, $classNames, 'grouped by class name, A to Z' );
        $groups = $this->page( array( 'class' => (string)$this->userGroupClass, 'limit' => '100' ), $classes );
        $this->assertSame( $both['total'] - count( $this->folders ), $groups['total'] );
        foreach ( $groups['rows'] as $row )
            $this->assertSame( $this->userGroupClass, $row['class_id'] );
    }

    public function testNoClassesNoContent()
    {
        $this->assertSame( array( 'rows' => array(), 'total' => 0, 'offset' => 0 ), $this->page( array(), array() ) );
    }

    public function testThePageIsReadThroughTheIndexOnTheClass()
    {
        $plan = $this->db->arrayQuery( 'EXPLAIN QUERY PLAN SELECT id FROM ezcontentobject WHERE contentclass_id = ' . $this->folderClass );
        $this->assertStringContainsString( 'ezcontentobject_classid', json_encode( $plan ) );
    }
}
