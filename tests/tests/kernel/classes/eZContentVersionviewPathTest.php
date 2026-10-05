<?php
/**
 * The path of the version preview (\Exponential\View\Kernel\Content\Versionview::previewParentNodes()).
 *
 * The version view builds a preview node; for an object that was never published it has no node ID and the path string
 * /<parent path>/ followed by an empty element. fetchPath() dropped the last element as the node's own, which was the
 * parent, so the preview path lost the parent folder.
 *
 * Database tests: each test publishes a folder under node 2 in the test database.
 *
 * @group database
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

#[\PHPUnit\Framework\Attributes\Group('database')]
class eZContentVersionviewPathTest extends ezpDatabaseTestCase
{
    protected $backupGlobals = false;

    /**
     * Builds a preview node the way the version view does: the parent's path string followed by the node ID.
     */
    private function previewNode( $parentNodeID, $nodeID )
    {
        $parent = eZContentObjectTreeNode::fetch( $parentNodeID );
        $node = new eZContentObjectTreeNode();
        $node->setAttribute( 'parent_node_id', $parentNodeID );
        $node->setAttribute( 'node_id', $nodeID );
        $node->setAttribute( 'path_string', $parent->attribute( 'path_string' ) . $nodeID . '/' );
        return $node;
    }

    private function nodeIDs( array $nodes )
    {
        $ids = array();
        foreach ( $nodes as $node )
        {
            $ids[] = (int)$node->attribute( 'node_id' );
        }
        return $ids;
    }

    /**
     * Returns the node ID of a new folder under node 2.
     */
    private function createFolder()
    {
        // Outside a web request nobody sets the module paths, and the publish operation needs them
        eZModule::setGlobalPathList( eZModule::activeModuleRepositories() );
        $folder = new ezpObject( 'folder', 2 );
        $folder->name = 'Version preview path';
        $folder->publish();
        return (int)eZContentObject::fetch( $folder->attribute( 'id' ) )->attribute( 'main_node_id' );
    }

    public function testUnpublishedObjectKeepsTheParentInThePath()
    {
        $folderNodeID = $this->createFolder();

        $parents = \Exponential\View\Kernel\Content\Versionview::previewParentNodes( $this->previewNode( $folderNodeID, null ) );

        $this->assertEquals( array( 2, $folderNodeID ), $this->nodeIDs( $parents ) );
    }

    public function testPublishedObjectPathEndsAboveItsNode()
    {
        $folderNodeID = $this->createFolder();
        $node = eZContentObjectTreeNode::fetch( $folderNodeID );

        $parents = \Exponential\View\Kernel\Content\Versionview::previewParentNodes( $this->previewNode( 2, $folderNodeID ) );

        $this->assertEquals( array( 2 ), $this->nodeIDs( $parents ) );
        $this->assertEquals( $this->nodeIDs( $node->attribute( 'path' ) ), $this->nodeIDs( $parents ) );
    }

    public function testPreviewWithoutLocationHasAnEmptyPath()
    {
        $node = new eZContentObjectTreeNode();
        $node->setAttribute( 'path_string', '' );

        $this->assertSame( array(), \Exponential\View\Kernel\Content\Versionview::previewParentNodes( $node ) );
    }
}
