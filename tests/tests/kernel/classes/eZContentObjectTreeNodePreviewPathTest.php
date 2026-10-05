<?php
/**
 * The path of a preview node of an object that was never published: eZContentObjectTreeNode::fetchPath() and
 * pathStringEndsWithParent(), and \Exponential\View\Kernel\Content\Versionview::previewParentNodes(). No database:
 * the node lookup is a stand-in that records which path elements were asked for.
 *
 *  PP-01 - A node without node ID and with a path string ends with its parent (/1/2/58//)
 *  PP-02 - A node with node ID ends with itself; a node without path string ends with nothing
 *  PP-03 - fetchPath() of a preview node asks for the whole path, the parent included
 *  PP-04 - fetchPath() of a published node leaves its own (last) element out, as before
 *  PP-05 - previewParentNodes() of a preview without location is empty, without a lookup
 *  PP-06 - previewParentNodes() returns what the path lookup gives, and an empty array for a non-array
 *  PP-07 - The "path" attribute is fetchPath() (templates reading $node.path get the same path)
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

/** A node whose path lookup records its arguments instead of asking the database */
class X1PreviewPathStandInNode extends eZContentObjectTreeNode
{
    public static $calls = array();
    public static $result = array();

    static function fetchNodesByPathString( $nodePath, $withLastNode = false, $asObjects = true, $limit = false )
    {
        self::$calls[] = array( $nodePath, $withLastNode, $asObjects );
        return self::$result;
    }
}

class eZContentObjectTreeNodePreviewPathTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        X1PreviewPathStandInNode::$calls = array();
        X1PreviewPathStandInNode::$result = array();
    }

    private function node( $nodeID, $pathString )
    {
        $node = new X1PreviewPathStandInNode();
        $node->setAttribute( 'node_id', $nodeID );
        $node->setAttribute( 'path_string', $pathString );
        return $node;
    }

    /** PP-01 */
    public function testAPreviewNodeEndsWithItsParent()
    {
        $this->assertTrue( $this->node( null, '/1/2/58//' )->pathStringEndsWithParent() );
        $this->assertTrue( $this->node( 0, '/1/2/' )->pathStringEndsWithParent() );
    }

    /** PP-02 */
    public function testAPublishedNodeEndsWithItself()
    {
        $this->assertFalse( $this->node( 64, '/1/2/58/64/' )->pathStringEndsWithParent() );
        $this->assertFalse( $this->node( null, '' )->pathStringEndsWithParent() );
        $this->assertFalse( $this->node( null, '/' )->pathStringEndsWithParent() );
    }

    /** PP-03 */
    public function testFetchPathOfAPreviewNodeIncludesTheParent()
    {
        $this->node( null, '/1/2/58//' )->fetchPath();
        $this->assertSame( array( array( '/1/2/58//', true, true ) ), X1PreviewPathStandInNode::$calls );
    }

    /** PP-04 */
    public function testFetchPathOfAPublishedNodeLeavesItselfOut()
    {
        $this->node( 64, '/1/2/58/64/' )->fetchPath();
        $this->assertSame( array( array( '/1/2/58/64/', false, true ) ), X1PreviewPathStandInNode::$calls );
    }

    /** PP-05 */
    public function testAPreviewWithoutLocationHasAnEmptyPath()
    {
        $this->assertSame( array(), \Exponential\View\Kernel\Content\Versionview::previewParentNodes( $this->node( null, '' ) ) );
        $this->assertSame( array(), X1PreviewPathStandInNode::$calls );
    }

    /** PP-06 */
    public function testPreviewParentNodesReturnsThePathLookup()
    {
        $home = new eZContentObjectTreeNode( array( 'node_id' => 2 ) );
        $folder = new eZContentObjectTreeNode( array( 'node_id' => 58 ) );
        X1PreviewPathStandInNode::$result = array( $home, $folder );
        $this->assertSame( array( $home, $folder ), \Exponential\View\Kernel\Content\Versionview::previewParentNodes( $this->node( null, '/1/2/58//' ) ) );
        $this->assertSame( array( array( '/1/2/58//', true, true ) ), X1PreviewPathStandInNode::$calls );

        X1PreviewPathStandInNode::$result = null;
        $this->assertSame( array(), \Exponential\View\Kernel\Content\Versionview::previewParentNodes( $this->node( 64, '/1/2/58/64/' ) ) );
    }

    /** PP-07 */
    public function testThePathAttributeIsFetchPath()
    {
        $folder = new eZContentObjectTreeNode( array( 'node_id' => 58 ) );
        X1PreviewPathStandInNode::$result = array( $folder );
        $this->assertSame( array( $folder ), $this->node( null, '/1/2/58//' )->attribute( 'path' ) );
        $this->assertSame( array( array( '/1/2/58//', true, true ) ), X1PreviewPathStandInNode::$calls );
    }
}
