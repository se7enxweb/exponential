<?php
/**
 * The AttributeFilter of a subtree fetch on MongoDB (eZContentObjectTreeNode::mongoAttributeFilterStages()). The
 * MongoDB path of subTreeByNodeID() and subTreeCountByNodeID() used to leave the filter out, so a fetch asking for
 * one group by name, or the events of a month, returned the whole subtree.
 *
 *  MF-01 - no filter: no stages
 *  MF-02 - the node and object fields with every comparison: =, !=, <, >, <=, >=, in, not_in, between,
 *          not_between, like and not_like (* the wildcard, \* a star), on the flattened field names
 *  MF-03 - "or" as the first entry joins the comparisons with $or; "and" and no entry with $and
 *  MF-04 - visibility compares is_invisible the other way round; state looks up the state links
 *  MF-05 - class_name, an unknown operator: false (the caller returns an empty result, never everything)
 *  MF-06 - with the joins, the stages that flatten object, class and name come first (for the count)
 *
 * No database: the stages are plain arrays. Class attribute filters need the class tables and are proven on a
 * MongoDB installation against the same fetches on PostgreSQL.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

#[\PHPUnit\Framework\Attributes\Group('kernel')]
class eZContentObjectTreeNodeMongoAttributeFilterTest extends PHPUnit\Framework\TestCase
{
    private function match( $filter )
    {
        $stages = eZContentObjectTreeNode::mongoAttributeFilterStages( $filter );
        $this->assertIsArray( $stages );
        $last = end( $stages );
        return $last['$match'];
    }

    public function testNoFilter()
    {
        $this->assertSame( array(), eZContentObjectTreeNode::mongoAttributeFilterStages( false ) );
        $this->assertSame( array(), eZContentObjectTreeNode::mongoAttributeFilterStages( array() ) );
    }

    public function testComparisons()
    {
        $this->assertSame( array( 'name' => array( '$eq' => 'Administrator users' ) ), $this->match( array( array( 'name', '=', 'Administrator users' ) ) ) );
        $this->assertSame( array( 'published' => array( '$gt' => 5 ) ), $this->match( array( array( 'published', '>', '5' ) ) ) );
        $this->assertSame( array( 'section_id' => array( '$ne' => 3 ) ), $this->match( array( array( 'section', '!=', 3 ) ) ) );
        $this->assertSame( array( 'depth' => array( '$lte' => 3 ) ), $this->match( array( array( 'depth', '<=', 3 ) ) ) );
        $this->assertSame( array( 'class_identifier' => array( '$in' => array( 'folder', 'article' ) ) ),
                           $this->match( array( array( 'class_identifier', 'in', array( 'folder', 'article' ) ) ) ) );
        $this->assertSame( array( 'owner_id' => array( '$nin' => array( 14, 10 ) ) ), $this->match( array( array( 'owner', 'not_in', array( '14', 10 ) ) ) ) );
        $this->assertSame( array( 'modified' => array( '$gte' => 1, '$lte' => 9 ) ), $this->match( array( array( 'modified', 'between', array( 1, 9 ) ) ) ) );
        $this->assertSame( array( 'modified' => array( '$not' => array( '$gte' => 1, '$lte' => 9 ) ) ),
                           $this->match( array( array( 'modified', 'not_between', array( 1, 9 ) ) ) ) );
        $this->assertSame( array( 'path_string' => array( '$regex' => '^/1/2/.*$' ) ), $this->match( array( array( 'path', 'like', '/1/2/*' ) ) ) );
        $this->assertSame( array( 'name' => array( '$not' => array( '$regex' => '^a\*b.*$' ) ) ), $this->match( array( array( 'name', 'not_like', 'a\*b*' ) ) ) );
    }

    public function testJoinType()
    {
        $or = $this->match( array( 'or', array( 'priority', '=', 5 ), array( 'node_id', '=', 2 ) ) );
        $this->assertSame( array( '$or' => array( array( 'priority' => array( '$eq' => 5 ) ), array( 'node_id' => array( '$eq' => 2 ) ) ) ), $or );
        $and = $this->match( array( 'and', array( 'priority', '=', 5 ), array( 'node_id', '=', 2 ) ) );
        $this->assertArrayHasKey( '$and', $and );
    }

    public function testVisibilityAndState()
    {
        $this->assertSame( array( 'is_invisible' => array( '$eq' => 0 ) ), $this->match( array( array( 'visibility', '=', '1' ) ) ) );
        $stages = eZContentObjectTreeNode::mongoAttributeFilterStages( array( array( 'state', '=', 4 ) ) );
        $this->assertSame( 'ezcobj_state_link', $stages[0]['$lookup']['from'] );
        $this->assertSame( array( '_afstate0.contentobject_state_id' => array( '$in' => array( 4 ) ) ), $stages[1]['$match'] );
        $this->assertSame( array( '_afstate0' => 0 ), $stages[2]['$project'] );
    }

    public function testRefused()
    {
        $this->assertFalse( eZContentObjectTreeNode::mongoAttributeFilterStages( array( array( 'class_name', '=', 'Folder' ) ) ) );
        $this->assertFalse( eZContentObjectTreeNode::mongoAttributeFilterStages( array( array( 'name', 'sounds_like', 'x' ) ) ) );
    }

    public function testWithJoins()
    {
        $stages = eZContentObjectTreeNode::mongoAttributeFilterStages( array( array( 'name', '=', 'x' ) ), array( 'mask' => 1, 'ids' => array() ) );
        $this->assertSame( 'ezcontentobject', $stages[0]['$lookup']['from'] );
        $last = end( $stages );
        $this->assertSame( array( 'name' => array( '$eq' => 'x' ) ), $last['$match'] );
    }
}
