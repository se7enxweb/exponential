<?php
/**
 * The parsed address of system page bookmarks (expbookmark_view.module_name, view_name, view_parameters) on a SQLite
 * database in memory, set as the global database of the test: no installation. A table made by an early 6.0.15 build
 * (the address alone) keeps working, the upgrade steps add and fill the columns, and bookmarks move to a renamed
 * module or view.
 *
 *  BV-P1 a table without the columns: hasParsedColumns() is false, definition() leaves them out, a row is stored
 *  BV-P2 6.0.15-bookmark-view-columns adds the three columns and the index, and is in place afterwards
 *  BV-P3 6.0.15-bookmark-view-fill fills the columns of the rows from their address, and is in place afterwards
 *  BV-P4 renameModuleView(): a dry run changes nothing; a module, or one view, moves with its parameters and query
 *        string, the parsed columns follow, other modules stay; names that are not valid are refused
 *  BV-P5 a table the installer creates from share/db_schema.dba has the columns and both indexes
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group bookmarks
 */

class BookmarkViewParsedColumnsTest extends PHPUnit\Framework\TestCase
{
    /** @var array|null the global database before the test */
    private $savedGlobal;

    /** @var eZSQLite3DB */
    private $db;

    protected function setUp(): void
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The SQLite3 extension is not loaded' );
        $this->savedGlobal = array_key_exists( 'eZDBGlobalInstance', $GLOBALS ) ? array( $GLOBALS['eZDBGlobalInstance'] ) : null;
        $this->db = new eZSQLite3DB( array( 'server' => '', 'port' => false, 'user' => '', 'password' => '', 'database' => ':memory:',
                                            'use_slave_server' => false, 'slave_server' => '', 'slave_port' => false, 'slave_user' => '',
                                            'slave_password' => '', 'slave_database' => '', 'socket' => false, 'charset' => 'utf-8',
                                            'is_internal_charset' => false, 'builtin_encoding' => false, 'connect_retries' => 1,
                                            'use_persistent_connection' => false, 'show_errors' => false ) );
        eZDB::setInstance( $this->db );
        expKernelTableSchema::reset();
        expBookmarkView::reset();
    }

    protected function tearDown(): void
    {
        expBookmarkView::reset();
        expKernelTableSchema::reset();
        if ( $this->savedGlobal === null )
            unset( $GLOBALS['eZDBGlobalInstance'] );
        else
            $GLOBALS['eZDBGlobalInstance'] = $this->savedGlobal[0];
        $this->db = null;
    }

    /** The table as an early 6.0.15 build made it, with two system page bookmarks of user 14 and one of user 15. */
    private function earlyTable()
    {
        $this->db->query( 'CREATE TABLE expbookmark_view ( bookmark_id integer NOT NULL DEFAULT 0, created integer NOT NULL DEFAULT 0, '
                          . 'module_uri longtext, user_id integer NOT NULL DEFAULT 0, PRIMARY KEY ( bookmark_id ) )' );
        $this->db->query( 'CREATE INDEX expbookmark_view_user ON expbookmark_view ( user_id )' );
        $this->db->query( "INSERT INTO expbookmark_view ( bookmark_id, created, module_uri, user_id ) VALUES ( 1, 1, 'role/view/3', 14 )" );
        $this->db->query( "INSERT INTO expbookmark_view ( bookmark_id, created, module_uri, user_id ) VALUES ( 2, 1, 'audit/console/(user)/14/(offset)/20', 14 )" );
        $this->db->query( "INSERT INTO expbookmark_view ( bookmark_id, created, module_uri, user_id ) VALUES ( 3, 1, 'content/search?SearchText=news', 15 )" );
        expBookmarkView::reset();
    }

    private function columns()
    {
        return array_column( (array)$this->db->arrayQuery( 'PRAGMA table_info(expbookmark_view)' ), 'name' );
    }

    private function row( $id )
    {
        $rows = $this->db->arrayQuery( 'SELECT * FROM expbookmark_view WHERE bookmark_id = ' . (int)$id );
        return $rows ? $rows[0] : null;
    }

    private function upgrade()
    {
        $context = new expUpgradeContext( $this->db, true );
        foreach ( array( new expUpgradeStepBookmarkViewColumns(), new expUpgradeStepBookmarkViewFill() ) as $step )
            if ( $step->detect( $context )['effect'] === expUpgradeStep::MISSING )
                $step->apply( $context );
    }

    /** BV-P1 */
    public function testEarlyTableKeepsWorking()
    {
        $this->earlyTable();
        $this->assertTrue( expBookmarkView::isAvailable() );
        $this->assertFalse( expBookmarkView::hasParsedColumns( true ) );
        $definition = expBookmarkView::definition();
        foreach ( expBookmarkView::PARSED_COLUMNS as $column )
        {
            $this->assertArrayNotHasKey( $column, $definition['fields'], $column );
            $this->assertArrayHasKey( $column, expBookmarkView::definitionWithAllColumns()['fields'], $column );
        }
        $row = new expBookmarkView( array( 'bookmark_id' => 4, 'user_id' => 14, 'module_uri' => 'setup/info', 'created' => 2 )
                                    + expBookmarkView::parsedFields( 'setup/info' ) );
        $row->store();
        $this->assertSame( 'setup/info', $this->row( 4 )['module_uri'] );
        $this->assertSame( array(), expBookmarkView::staleParsedRows(), 'nothing to fill while the columns are missing' );
    }

    /** BV-P2 */
    public function testColumnsStep()
    {
        $this->earlyTable();
        $context = new expUpgradeContext( $this->db, true );
        $step = new expUpgradeStepBookmarkViewColumns();
        $this->assertSame( array( '6.0.15-bookmark-view-table' ), $step->after() );
        $state = $step->detect( $context );
        $this->assertSame( expUpgradeStep::MISSING, $state['effect'] );
        foreach ( array( 'module_name', 'view_name', 'view_parameters', 'expbookmark_view_module' ) as $part )
            $this->assertStringContainsString( $part, $state['detail'] );
        $step->apply( $context );
        $this->assertSame( array(), array_diff( expBookmarkView::PARSED_COLUMNS, $this->columns() ) );
        $this->assertTrue( expBookmarkView::hasParsedColumns() );
        $this->assertTrue( expUpgradeCatalog::hasIndex( $this->db, 'expbookmark_view', array( 'module_name', 'view_name' ), false ) );
        $this->assertSame( expUpgradeStep::PRESENT, $step->detect( $context )['effect'] );
        $this->assertSame( 'role/view/3', $this->row( 1 )['module_uri'], 'the rows stay' );
        $this->assertSame( '', (string)$this->row( 1 )['module_name'] );
    }

    /** BV-P3 */
    public function testFillStep()
    {
        $this->earlyTable();
        $context = new expUpgradeContext( $this->db, true );
        ( new expUpgradeStepBookmarkViewColumns() )->apply( $context );
        $fill = new expUpgradeStepBookmarkViewFill();
        $this->assertSame( 'scripts', $fill->stage() );
        $state = $fill->detect( $context );
        $this->assertSame( expUpgradeStep::MISSING, $state['effect'] );
        $this->assertSame( array( 'bookmark 1: role/view', 'bookmark 2: audit/console', 'bookmark 3: content/search' ), $fill->plan( $context ) );
        $fill->apply( $context );
        $this->assertSame( expUpgradeStep::PRESENT, $fill->detect( $context )['effect'] );

        $row = $this->row( 2 );
        $this->assertSame( 'audit', $row['module_name'] );
        $this->assertSame( 'console', $row['view_name'] );
        $this->assertSame( array( 'ordered' => array(), 'unordered' => array( 'user' => '14', 'offset' => '20' ), 'query' => array() ),
                           json_decode( $row['view_parameters'], true ) );
        $this->assertSame( array( 'ordered' => array( '3' ), 'unordered' => array(), 'query' => array() ),
                           json_decode( $this->row( 1 )['view_parameters'], true ) );
        $this->assertSame( array( 'SearchText' => 'news' ), json_decode( $this->row( 3 )['view_parameters'], true )['query'] );
        $this->assertSame( '{"ordered":["3"],"unordered":{},"query":{}}', $this->row( 1 )['view_parameters'], 'empty maps are objects' );
    }

    /** BV-P4 */
    public function testRenameModuleView()
    {
        $this->earlyTable();
        $this->upgrade();
        $this->assertFalse( expBookmarkView::renameModuleView( 'role', 'roles/view' ), 'a module cannot become one view' );
        $this->assertFalse( expBookmarkView::renameModuleView( 'role/../x', 'roles' ) );
        $this->assertFalse( expBookmarkView::renameModuleView( 'role', 'https://example.org' ) );

        $dry = expBookmarkView::renameModuleView( 'audit', 'auditlog', false );
        $this->assertSame( array( 2 => array( 'from' => 'audit/console/(user)/14/(offset)/20', 'to' => 'auditlog/console/(user)/14/(offset)/20' ) ), $dry );
        $this->assertSame( 'audit', $this->row( 2 )['module_name'], 'a dry run changes nothing' );

        $this->assertSame( $dry, expBookmarkView::renameModuleView( 'audit', 'auditlog' ) );
        $row = $this->row( 2 );
        $this->assertSame( 'auditlog/console/(user)/14/(offset)/20', $row['module_uri'] );
        $this->assertSame( 'auditlog', $row['module_name'] );
        $this->assertSame( 'console', $row['view_name'] );

        $changes = expBookmarkView::renameModuleView( 'content/search', 'search/find' );
        $this->assertSame( array( 3 => array( 'from' => 'content/search?SearchText=news', 'to' => 'search/find?SearchText=news' ) ), $changes );
        $this->assertSame( 'find', $this->row( 3 )['view_name'] );
        $this->assertSame( 'role/view/3', $this->row( 1 )['module_uri'], 'other modules stay' );
        $this->assertSame( array(), expBookmarkView::renameModuleView( 'role/edit', 'role/change' ), 'no bookmark of that view' );
        $this->assertSame( array(), expBookmarkView::staleParsedRows(), 'the columns follow the address' );
    }

    /** BV-P5 */
    public function testInstallerTable()
    {
        $messages = array();
        $this->assertTrue( expBookmarkView::install( $this->db, $messages ), implode( '; ', $messages ) );
        $this->assertSame( array( 'bookmark_id', 'created', 'module_name', 'module_uri', 'user_id', 'view_name', 'view_parameters' ), $this->columns() );
        $this->assertTrue( expBookmarkView::hasParsedColumns( true ) );
        $this->assertTrue( expUpgradeCatalog::hasIndex( $this->db, 'expbookmark_view', array( 'module_name', 'view_name' ), false ) );
        $this->assertTrue( expUpgradeCatalog::hasIndex( $this->db, 'expbookmark_view', array( 'user_id' ), false ) );
        $context = new expUpgradeContext( $this->db, false );
        $this->assertSame( expUpgradeStep::PRESENT, ( new expUpgradeStepBookmarkViewColumns() )->detect( $context )['effect'] );
        $this->assertSame( expUpgradeStep::PRESENT, ( new expUpgradeStepBookmarkViewFill() )->detect( $context )['effect'] );
    }
}
