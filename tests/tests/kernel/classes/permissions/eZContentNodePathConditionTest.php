<?php
/**
 * Tests of eZContentObjectTreeNode::createNodesConditionSQLStringFromPath(), the condition that selects the nodes
 * of a path string (the node's ancestors, with or without the node itself, at most $limit of them counted from
 * the end of the path). No database: the handler that only escapes stands in for the real one.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/fixtures/ezcontentpermissionsqltestdb.php';

class eZContentNodePathConditionTest extends PHPUnit\Framework\TestCase
{
    private $db;
    private $hadDB;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->hadDB = array_key_exists( 'eZDBGlobalInstance', $GLOBALS );
        $this->db = $GLOBALS['eZDBGlobalInstance'] ?? null;
        eZDB::setInstance( new eZContentPermissionSQLTestDB() );
    }

    protected function tearDown(): void
    {
        if ( $this->hadDB )
            $GLOBALS['eZDBGlobalInstance'] = $this->db;
        else
            unset( $GLOBALS['eZDBGlobalInstance'] );
    }

    public function testNodesConditionFromPath()
    {
        $this->assertSame( ' node_id IN ( 1, 2, 5 ) and ', eZContentObjectTreeNode::createNodesConditionSQLStringFromPath( '/1/2/5/', true ) );
        $this->assertSame( ' node_id IN ( 1, 2 ) and ', eZContentObjectTreeNode::createNodesConditionSQLStringFromPath( '/1/2/5/', false ) );
        $this->assertSame( ' node_id = 1 and ', eZContentObjectTreeNode::createNodesConditionSQLStringFromPath( '/1/2/', false ) );
        $this->assertFalse( eZContentObjectTreeNode::createNodesConditionSQLStringFromPath( '/1/', false ) );
    }

    public function testNodesConditionFromPathWithALimit()
    {
        $this->assertSame( ' node_id IN ( 5, 9 ) and ', eZContentObjectTreeNode::createNodesConditionSQLStringFromPath( '/1/2/5/9/', true, 2 ) );
        $this->assertSame( ' node_id IN ( 2, 5 ) and ', eZContentObjectTreeNode::createNodesConditionSQLStringFromPath( '/1/2/5/9/', false, 2 ) );
        $this->assertSame( ' node_id = 9 and ', eZContentObjectTreeNode::createNodesConditionSQLStringFromPath( '/1/2/5/9/', true, 1 ) );
        $this->assertSame( ' node_id IN ( 1, 2, 5, 9 ) and ', eZContentObjectTreeNode::createNodesConditionSQLStringFromPath( '/1/2/5/9/', true, 10 ) );
    }
}
