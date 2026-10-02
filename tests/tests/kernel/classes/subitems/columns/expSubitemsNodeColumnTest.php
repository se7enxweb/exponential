<?php
/**
 * The Node, Location and Dates columns read from the location: checked against real nodes.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expSubitemsColumnsTestCase.php';

class expSubitemsNodeColumnTest extends expSubitemsColumnsTestCase
{
    public function testParentNodeID()
    {
        $this->assertSame( 1, $this->value( 'parentnodeid', $this->contentRoot() ) );
        $this->assertSame( 1, $this->value( 'parentnodeid', $this->mediaRoot() ) );
        $user = $this->adminUserNode();
        $this->assertSame( (int)$user->attribute( 'parent_node_id' ), $this->value( 'parentnodeid', $user ) );
        $this->assertSame( 'number', $this->column( 'parentnodeid' )->setting( 'Type' ) );
    }

    public function testParentName()
    {
        $this->assertNull( $this->value( 'parentname', $this->contentRoot() ), 'node 1 has no name to show' );
        $user = $this->adminUserNode();
        $this->assertSame( $user->fetchParent()->getName(), $this->value( 'parentname', $user ) );
    }

    public function testDepthAndPaths()
    {
        $root = $this->contentRoot();
        $this->assertSame( 1, $this->value( 'depth', $root ) );
        $this->assertSame( '/1/2/', $this->value( 'pathstring', $root ) );
        $this->assertSame( '/1/' . $this->mediaRoot()->attribute( 'node_id' ) . '/', $this->value( 'pathstring', $this->mediaRoot() ) );
        $user = $this->adminUserNode();
        $this->assertSame( (int)$user->attribute( 'depth' ), $this->value( 'depth', $user ) );
        $this->assertStringEndsWith( '/' . $user->attribute( 'node_id' ) . '/', $this->value( 'pathstring', $user ) );
        $this->assertSame( $user->attribute( 'path_identification_string' ), $this->value( 'pathidentification', $user ) );
        $this->assertSame( 'path', $this->column( 'pathstring' )->sortBy() );
        $this->assertSame( 'depth', $this->column( 'depth' )->sortBy() );
    }

    public function testMainNodeAndIsMain()
    {
        $root = $this->contentRoot();
        $this->assertSame( 2, $this->value( 'mainnodeid', $root ) );
        $this->assertTrue( $this->value( 'ismain', $root ) );
        $this->assertTrue( $this->value( 'ismain', $this->adminUserNode() ) );
    }

    public function testChildrenSort()
    {
        foreach ( array( $this->contentRoot(), $this->mediaRoot(), $this->usersRoot() ) as $node )
        {
            $this->assertSame( eZContentObjectTreeNode::sortFieldName( $node->attribute( 'sort_field' ) ), $this->value( 'childsortfield', $node ) );
            $this->assertContains( $this->value( 'childsortorder', $node ), array( 'Ascending', 'Descending' ) );
        }
    }

    public function testHiddenAndInvisibleAreSeparate()
    {
        $root = $this->contentRoot();
        $this->assertFalse( $this->value( 'hidden', $root ) );
        $this->assertFalse( $this->value( 'invisible', $root ) );
        $this->assertSame( 'visibility', $this->column( 'invisible' )->sortBy() );

        // the same node object with the flags of a node below a hidden one
        $copy = clone $root;
        $copy->setAttribute( 'is_hidden', 0 );
        $copy->setAttribute( 'is_invisible', 1 );
        $this->assertFalse( $this->value( 'hidden', $copy ) );
        $this->assertTrue( $this->value( 'invisible', $copy ) );
        $copy->setAttribute( 'is_hidden', 1 );
        $this->assertTrue( $this->value( 'hidden', $copy ) );
    }

    public function testContainer()
    {
        $this->assertTrue( $this->value( 'iscontainer', $this->contentRoot() ) );
        $this->assertFalse( $this->value( 'iscontainer', $this->adminUserNode() ) );
    }

    public function testChildrenAndSubtreeCounts()
    {
        $media = $this->mediaRoot();
        $this->assertSame( (int)$media->childrenCount(), $this->value( 'childrencount', $media ) );
        $this->assertGreaterThan( 0, $this->value( 'childrencount', $this->usersRoot() ), 'the users root has groups' );
        $this->assertSame( 0, $this->value( 'childrencount', $this->adminUserNode() ) );
        $subtree = $this->value( 'subtreecount', $media );
        $this->assertGreaterThanOrEqual( $this->value( 'childrencount', $media ), $subtree );
        $this->assertSame( 0, $this->value( 'subtreecount', $this->adminUserNode() ) );
    }

    public function testChildClasses()
    {
        $users = $this->usersRoot();
        $list = $this->value( 'childclasses', $users );
        $this->assertIsArray( $list );
        $total = 0;
        foreach ( $list as $item )
        {
            $this->assertMatchesRegularExpression( '/^[a-z0-9_]+ \(\d+\)$/', $item );
            preg_match( '/\((\d+)\)$/', $item, $m );
            $total += (int)$m[1];
        }
        $this->assertSame( (int)$users->childrenCount( false ), $total, 'the numbers add up to the children' );
        $this->assertNull( $this->value( 'childclasses', $this->adminUserNode() ), 'no children, nothing to show' );
    }

    public function testNewestChild()
    {
        $users = $this->usersRoot();
        $children = $users->subTree( array( 'Depth' => 1, 'DepthOperator' => 'eq', 'SortBy' => array( array( 'published', false ) ), 'Limit' => 1 ) );
        $this->assertSame( $children[0]->getName(), $this->value( 'newestchildname', $users ) );
        $this->assertSame( (int)$children[0]->attribute( 'object' )->attribute( 'published' ), $this->value( 'newestchild', $users ) );
        $this->assertNull( $this->value( 'newestchild', $this->adminUserNode() ) );
        $this->assertNull( $this->value( 'newestchildname', $this->adminUserNode() ) );
    }

    public function testModifiedSubnode()
    {
        $root = $this->contentRoot();
        $this->assertSame( (int)$root->attribute( 'modified_subnode' ), $this->value( 'modifiedsubnode', $root ) );
        $this->assertSame( 'modified_subnode', $this->column( 'modifiedsubnode' )->sortBy() );
    }

    public function testViewCount()
    {
        $root = $this->contentRoot();
        $row = eZViewCounter::fetch( 2, false );
        $expected = is_array( $row ) ? (int)$row['count'] : null;
        $this->assertSame( $expected, $this->value( 'viewcount', $root ) );
    }

    public function testDateColumns()
    {
        $root = $this->contentRoot();
        $object = $root->attribute( 'object' );
        $published = (int)$object->attribute( 'published' );
        $this->assertSame( date( 'c', $published ), $this->value( 'publishediso', $root ) );
        $this->assertSame( date( 'c', (int)$object->attribute( 'modified' ) ), $this->value( 'modifiediso', $root ) );
        $this->assertSame( (int)floor( ( time() - $published ) / 86400 ), $this->value( 'dayssincepublished', $root ) );
        $this->assertSame( (int)floor( ( time() - (int)$object->attribute( 'modified' ) ) / 86400 ), $this->value( 'dayssincemodified', $root ) );
        $this->assertStringEndsWith( 'ago', $this->value( 'publishedage', $root ) );
        $this->assertSame( 'published', $this->column( 'publishedage' )->sortBy() );
        $this->assertSame( 'modified', $this->column( 'modifiedage' )->sortBy() );
    }

    public function testFormatAge()
    {
        $now = 1790000000;
        $this->assertSame( '3 days ago', expSubitemsFieldColumn::formatAge( $now - 3 * 86400 - 5, $now ) );
        $this->assertSame( '1 hour ago', expSubitemsFieldColumn::formatAge( $now - 3600, $now ) );
        $this->assertSame( 'in 2 weeks', expSubitemsFieldColumn::formatAge( $now + 15 * 86400, $now ) );
        $this->assertSame( 'just now', expSubitemsFieldColumn::formatAge( $now - 10, $now ) );
        $this->assertNull( expSubitemsFieldColumn::formatAge( 0, $now ) );
        $this->assertNull( expSubitemsDateColumn::days( 0 ) );
        $this->assertSame( 2, expSubitemsDateColumn::days( $now - 2 * 86400 - 1, $now ) );
    }

    public function testFormatBytes()
    {
        $this->assertSame( '12 B', expSubitemsFieldColumn::formatBytes( 12 ) );
        $this->assertSame( '207 kB', expSubitemsFieldColumn::formatBytes( 212210 ) );
        $this->assertSame( '4.3 MB', expSubitemsFieldColumn::formatBytes( 4510015 ) );
    }
}
