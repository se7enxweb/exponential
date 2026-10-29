<?php
/**
 * eZContentObjectTreeNode::viewCount() is 0 for a node without a row in ezview_counter (a node nobody has viewed
 * since counting began), with no "array offset on null" warning, and the stored count otherwise.
 *
 * No database: a stand-in answers the one query with no rows, or with one row.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class eZContentObjectTreeNodeViewCountTestDatabase extends eZDBInterface
{
    public $rows = array();
    public $queries = array();

    public function __construct()
    {
        $this->DB = 'viewcount-test';
    }

    function query( $sql, $server = false )
    {
        $this->queries[] = $sql;
        return true;
    }

    function arrayQuery( $sql, $params = array(), $server = false )
    {
        $this->queries[] = $sql;
        return $this->rows;
    }

    function isConnected()
    {
        return true;
    }

    function escapeString( $str )
    {
        return addslashes( (string)$str );
    }

    function databaseName()
    {
        return 'viewcount-test';
    }
}

class eZContentObjectTreeNodeViewCountTest extends PHPUnit\Framework\TestCase
{
    private $hadDb;
    private $savedDb;

    protected function setUp(): void
    {
        $this->hadDb = array_key_exists( 'eZDBGlobalInstance', $GLOBALS );
        $this->savedDb = $GLOBALS['eZDBGlobalInstance'] ?? null;
    }

    protected function tearDown(): void
    {
        if ( $this->hadDb )
            $GLOBALS['eZDBGlobalInstance'] = $this->savedDb;
        else
            unset( $GLOBALS['eZDBGlobalInstance'] );
    }

    private function node( array $rows )
    {
        $db = new eZContentObjectTreeNodeViewCountTestDatabase();
        $db->rows = $rows;
        eZDB::setInstance( $db );
        return new eZContentObjectTreeNode( array( 'node_id' => 4242 ) );
    }

    public function testANodeWithoutACounterRowHasNoViews()
    {
        $warnings = array();
        set_error_handler( function ( $errno, $errstr ) use ( &$warnings )
        {
            $warnings[] = $errstr;
            return true;
        } );
        try
        {
            $count = $this->node( array() )->viewCount();
        }
        finally
        {
            restore_error_handler();
        }

        $this->assertSame( 0, $count );
        $this->assertSame( array(), $warnings );
    }

    public function testTheStoredCountIsReturned()
    {
        $this->assertSame( 17, $this->node( array( array( 'node_id' => '4242', 'count' => '17' ) ) )->viewCount() );
    }
}
